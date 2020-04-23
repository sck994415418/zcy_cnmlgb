<?php defined('InShopNC') or exit('Access Invalid!');?>
<?php
require_once(BASE_PATH.'/../zcy/nr_zcy.php');
$zcy = new nr_zcy();
$rs = $zcy->order_list($_GET['status'],array($_GET['orderId']),1,1);
//echo '<pre>';
//print_r($rs);
//var_dump($rs);
?>
<div class="toDisplay">
    <form name="yingshe" id="yingshe" action="#" method="post">
        <input type="hidden" name="skus[skuId]" value="<?php echo $rs['data_response']['data'][0]['orderItems'][0]['skuId']?>">
        <input type="hidden" name="orderId" value="<?php echo $rs['data_response']['data'][0]['order']['id']?>">
        <table width="450" height="500" border="0" style="margin:auto;">
            <tr>
                <th colspan="2" align="center"><h2 align="center">订单发货</h2></th>
            </tr>
            <tr>
                <td align="right"><font color="#FF0000">*</font>发货数量：</td>
                <td><input type="text" name="skus[quantity]" id="skus[quantity]" value="" size="40" /></td>
            </tr>
            <tr>
                <td width="35%" align="right"><font color="#FF0000">*</font>发货方式：</td>
                <td width="35%">
                    <input type="radio" name="shipmentType" id="shipmentType" value="1" checked size="10" />物流发货
                    <input type="radio" name="shipmentType" id="shipmentType" value="2" size="10" />送货上门
                </td>
            </tr>
            <tr>
                <td align="right"><font color="#FF0000">*</font>物流单号：</td>
                <td><input type="text" name="shipmentNo" id="shipmentNo" value="" size="40" /></td>
            </tr>
            <tr>
                <td align="right"><font color="#FF0000"></font>发货物流公司代码：</td>
                <td><input type="text" name="expressCode" placeholder="发货方式为物流发货时，必填 （<=32个字符）" id="expressCode" value="" size="40" /></td>
            </tr>
            <tr>
                <td colspan="2"><span id="errinfo"></span></td>
            </tr>
            <tr>
                <td align="right"><input type="button" name="cancle" class="submit" id="cancle" value="取消" /></td>
                <td align="center"><input type="button" name="submit" class="submit" id="addyingshe" value="提交" /></td>
            </tr>
        </table>
    </form>
</div>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js"></script>
<script src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/store_goods_list.js"></script>
<script>
    $(function(){
        //Ajax提示
        $('.tip').poshytip({
            className: 'tip-yellowsimple',
            showTimeout: 1,
            alignTo: 'target',
            alignX: 'center',
            alignY: 'top',
            offsetY: 5,
            allowTipHover: false
        });
    });
</script>
<script language="javascript">
    function in_array(stringToSearch, arrayToSearch) {
        for (s = 0; s < arrayToSearch.length; s++) {
            thisEntry = arrayToSearch[s].toString();
            if (thisEntry == stringToSearch) {
                return true;
            }
        }
        return false;
    }
</script>
<script language="javascript">
    function addyingshe(goods_id,goods_name) {
        $("#goods_name").val(goods_name.trim());
        $("#goods_id").val(goods_id);
        $(".mask").fadeIn();
        $(".toDisplay").fadeIn();
    }

    $("#cancle").click(function() {
        history.back()
        // $("#goods_name").val("");
        // $("#goods_id").val("");
        // $("#goods_yingshe_id").val("");
        // $("#goods_yingshe_name").val("");
        // $("#errinfo").text("");
        // $(".mask").fadeOut();
        // $(".toDisplay").fadeOut();
    });

    $("#addyingshe").click(function() {
        $.ajax({
            url:"/shop/index.php?act=zcy_order&op=send_order",
            type: "POST",
            dataType: "JSON",
            data: $('#yingshe').serialize(),
            success:function(msg) {
                // alert(msg);
                // return false;
                if(msg.code == 1){
                    alert(msg.msg);
                    location.reload()
                    $("#errinfo").html(msg.msg);
                    // listyingshe(goods_id);

                }else{
                    alert(msg.msg)
                    $("#errinfo").html(msg.msg);
                }
            },
            error : function(xhr) {
                $("#errinfo").html(xhr.status + ":" + xhr.statusText);
            }
        });
    });
</script>
<script language="javascript">
    function listyingshe(goods_id){
        var str = "";
        $.ajax({
            url:"/shop/index.php?act=store_goods_change_price&op=listyingshe",
            timeout:2000,
            type:"get",
            dataType: "JSON",
            data:{
                "goods_id" : goods_id
            },
            success:function(data) {
                if(data.isSuccess){
                    for(i=0;i<data.returnMsg.length;i++){
                        str = str + "<p class='yslist'><a class='olink' href='" + data.returnMsg[i].productUrl + "' target='_blank'>" + data.returnMsg[i].productName + "</a></p>";
                    }
                    $("#ys"+goods_id).html(str);
                    $("#ys"+goods_id).css('display','');
                }else{
                    alert(data.returnMsg);
                }
            },
            error : function(msg) {
                alert(msg.responseText);
            }
        })
    }
</script>
<script type="text/javascript">
    $(".ystd").each(function(){
        if(trim($(this).text())==''){
            $(this).css('display',"none");
        };
    });
</script>
<script type="text/javascript">
    $(".zf_class").dblclick(function(){
        var zf_class_html = $(this).html();
        var zf_class_text = $(this).text();
        var toshow = $("#zf_class").html();
        var span = $(this);
        $(this).html(toshow);
        var goods_id = $(this).attr('goods_id');
        var opt = $(this).children();
        opt.removeClass("w150");
        opt.addClass("w80");
        opt.find("option:contains('" + zf_class_text + "')").attr("selected","selected");
        opt.focus();
        opt.blur(function(){
            span.html(zf_class_html);
        })
        opt.change(function(){
            var rt = $(this).find("option:selected").text();
            $.ajax({
                url:"/shop/index.php?act=store_goods_change_price&op=change_zf_class",
                timeout:2000,
                type:"get",
                dataType: "JSON",
                data:{
                    "goods_id" : goods_id,
                    "zf_class_id" : opt.val()
                },
                success:function(data){
                    if(data.isSuccess && (data.affected_rows == 1)){
                        span.text("更改成功！");
                        switch(data.class_type){
                            case "sanjia":
                                setTimeout(function xx(){span.html("<font color='#FF0000'>" + rt +"</font>");},500);
                                break;
                            case "yijia":
                                setTimeout(function xx(){span.text(rt);},500);
                                break;
                            case "noexist":
                                setTimeout(function xx(){span.html("<font color='#FF00FF'>分类未绑定！</font>");},500);
                                break;
                        }
                    }else{
                        alert(data.returnMsg);
                    }
                },
                error : function(msg) {
                    alert(msg.responseText);
                }

            })
        })
    })
</script>