<?php defined('InShopNC') or exit('Access Invalid!');?>


<style>
    .ncsc-form-goods { border: solid #E6E6E6; border-width: 1px 1px 0 1px;}
    .ncsc-form-goods h3 { font-size: 14px; font-weight: 600; line-height: 22px; color: #000; clear: both; background-color: #F5F5F5; padding: 5px 0 5px 12px; border-bottom: solid 1px #E7E7E7;}
    .ncsc-form-goods dl { font-size: 0; *word-spacing:-1px/*IE6、7*/; line-height: 20px; clear: both; padding: 0; margin: 0; border-bottom: solid 1px #E6E6E6; overflow: hidden;}

    .ncsc-form-goods dl dt {
        font-size: 12px;
        line-height: 30px;
        color: #333;
        vertical-align: top;
        letter-spacing: normal;
        word-spacing: normal;
        text-align: right;
        display: inline-block;
        width: 20%;
        padding: 8px 1% 8px 0;
        margin: 0;
    }
    .spec li {
        font-size: 12px;
        vertical-align: top;
        letter-spacing: normal;
        word-spacing: normal;
        display: inline-block;
        *display: inline;
        width: 40%;
        margin-bottom: 6px;
        zoom: 1;
    }
    .ncsc-form-goods dl dd {
        font-size: 12px;
        line-height: 30px;
        vertical-align: top;
        letter-spacing: normal;
        word-spacing: normal;
        display: inline-block;
        width: 65%;
        padding: 8px 0 8px 1%;
        border-left: solid 1px #E6E6E6;
    }
    .w60 {
        width: 70% !important;
        text-align: center;
    }

</style>
<div id="content">
    <div id="dataLoading" class="wp_data_loading">
        <div class="data_loading">加载中...</div>
    </div>
    <div class="zcyadd">
        <form action="#" method="post">
            <table width="450" height="200" border="0" style="margin: auto;">
                <tr>
                    <th colspan="2" align="center"><h2 align="center">商品上传至政采云</h2></th>
                </tr>

                    <input type="hidden" name="good_id" id="good_id" value="" /><input type="hidden" name="goods_name" id="good_name" value="" style="border:none;width:100%;text-align:center" contenteditable="false" />

                <tr>
                    <td alig n="right"><font color="#FF0000">*</font>政采云商品一级属性：</td>
                    <td colspan="2" height="30">
                        <select name="one" id="one">
                            <option value=""></option>
                            <?php foreach($output['goods_class'] as $key=>$val){?>
                                <?php if($val['level'] == 1){ ?>
                                    <option value="<?php echo $val['id']; ?>"><?php echo $val['name']; ?></option>
                                <?php }?>
                            <?php }?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td align="right"><font color="#FF0000">*</font>政采云商品二级属性：</td>
                    <td>
                        <select name="two" id="two">
                            <option value="">--请选择--</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td align="right"><font color="#FF0000">*</font>政采云商品二级属性：</td>
                    <td>
                        <select name="three" id="three">
                            <option value="">--请选择--</option>
                        </select>
                    </td>
                </tr>



            </table>
            <div class="ncsc-form-goods" id="sku">

            </div>
            <div class="bottom tc hr32">
                <label class="submit-border">
                    <input type="submit" class="submit" value="下一步，上传商品图片">
                </label>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    var provinceId = null;//纪录共同的数组下标值

    $("#one").change(function(){//当省级下拉菜单被改变触发change事件
        // $('#dataLoading').show();
        $("#two").html("<option>--请选择--</option>");
        $("#three").html("<option>--请选择--</option>");
        provinceId = $("#one").val();
        $.ajax({
            type: "GET",
            url: "/shop/index.php?act=zcy_goods&op=linkage",
            data:{id:provinceId},
            dataType: "json",
            beforeSend:function(){
                $('#dataLoading').show();
            },
            complete:function(){
                $('#dataLoading').hide();
            },
            success: function(data){
                $.each(data,function(k,v){
                    var str = "<option value="+ v.id +">" + v.name + "</option>"
                    $("#two").append(str);//添加option标签
                })
            }
        });
    });


    //----------------------------联动第三级-------------------------------------------------------
    $("#two").change(function(){
        $("#three").html("<option>--请选择--</option>");
        provinceId = $("#two").val();
        $.ajax({
            type: "GET",
            url: "/shop/index.php?act=zcy_goods&op=linkage",
            data:{id:provinceId},
            dataType: "json",
            beforeSend:function(){
                $('#dataLoading').show();
            },
            complete:function(){
                $('#dataLoading').hide();
            },
            success: function(data){
                $.each(data,function(k,v){
                    var str = "<option value="+ v.id +">" + v.name + "</option>"
                    $("#three").append(str);//添加option标签
                })
            }
        });
    })
    $("#three").change(function(){
        provinceId = $("#three").val();//获取到省和市的共同数组下标
        $.ajax({
            type: "GET",
            url: "/shop/index.php?act=zcy_goods&op=category",
            data:{goods_id:provinceId},
            dataType: "json",
            beforeSend:function(){
                $('#dataLoading').show();
            },
            complete:function(){
                $('#dataLoading').hide();
            },
            success: function(data){

                $.each(data.data_reponse,function(k,v) {
                    var str = ' <dl nc_type="spec_group_dl_0" nctype="spec_group_dl" class="spec-bg">\n' +
                        '                        <dt>\n' +
                        '                            <input readonly name="attrName[]" type="text" class="text w60 tip2 tr" value="'+ v.attrName +'" nctype="spec_name">\n' +
                        '                        </dt>\n' +
                        '                        <dd>\n' +
                        '                            <ul class="spec">\n';
                        var str2 ="";
                        if(v.attrVals == '' || v.attrVals == [] || v.attrVals == null){
                            str2 =  ' <li>                           <span nctype="input_checkbox">\n' +
                                '                                        <input type="text" value="'+ v.attrVals+'" name="attrVals[]">\n' +
                                '                                    </span>\n' +
                                '                                    <span nctype="pv_name">'+ v.group +'</span>\n'+
                                '                                </li>\n';
                        }else{

                            $.each(v.attrVals,function (key,val) {
                                str2 +=  '<li> <span nctype="input_checkbox">\n' +
                                    '     <input type="checkbox" value="'+ val+'" name="attrVals[]">\n' +
                                    '        </span>\n' +
                                    '        <span nctype="pv_name">'+ val +'</span>\n '+
                                    '                                </li>\n';

                            })
                        }

                    var str3 = '          </ul>\n' +
                        '                        </dd>\n' +
                        '                    </dl>' +
                        '<input type="hidden" name="propertyId[]" value="'+ v.propertyId +'">';
                    $("#sku").append(str+str2+str3);
                })


            }
        });
    })

</script>