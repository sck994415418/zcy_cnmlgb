<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="tabmenu">
    <?php include template('layout/submenu');?>
    <a href="<?php echo urlShop('store_goods_add');?>" class="ncsc-btn ncsc-btn-green" title="<?php echo $lang['store_goods_index_add_goods'];?>"> <?php echo $lang['store_goods_index_add_goods'];?></a>
</div>
<style>
    .ncsc-form-goods { border: solid #E6E6E6; border-width: 1px 1px 0 1px;}
    .ncsc-form-goods h3 { font-size: 14px; font-weight: 600; line-height: 22px; color: #000; clear: both; background-color: #F5F5F5; padding: 5px 0 5px 12px; border-bottom: solid 1px #E7E7E7;}
    .ncsc-form-goods dl { font-size: 0; *word-spacing:-1px/*IE6、7*/; line-height: 20px; clear: both; padding: 0; margin: 0; border-bottom: solid 1px #E6E6E6; overflow: hidden;}

</style>
<div id="content">
    <div class="zcyadd">
        <form action="#" method="post">
            <table width="450" height="200" border="0" style="margin: auto;">
                <tr>
                    <th colspan="2" align="center"><h2 align="center">商品上传至政采云</h2></th>
                </tr>
                <tr>
                    <td colspan="2" height="30"><input type="hidden" name="good_id" id="good_id" value="" /><input type="text" name="goods_name" id="good_name" value="" style="border:none;width:100%;text-align:center" contenteditable="false" /></td>
                </tr>
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
                    <td colspan="2" height="30">
                        <select name="two" id="two">
                            <option value="">--请选择--</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td align="right"><font color="#FF0000">*</font>政采云商品二级属性：</td>
                    <td colspan="2" height="30">
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
        $("#two").html("<option>--请选择--</option>");
        $("#three").html("<option>--请选择--</option>");
        provinceId = $("#one").val();
        $.ajax({
            type: "GET",
            url: "/shop/index.php?act=zcy_goods&op=linkage",
            data:{id:provinceId},
            dataType: "json",
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
            success: function(data){
                $.each(data.data_reponse,function(k,v) {
                    var str = ' <dl nc_type="spec_group_dl_0" nctype="spec_group_dl" class="spec-bg">\n' +
                        '                        <dt>\n' +
                        '                            <input name="attrName[]" type="text" class="text w60 tip2 tr" value="'+ v.attrName +'" nctype="spec_name">\n' +
                        '                        </dt>\n' +
                        '                        <dd>\n' +
                        '                            <ul class="spec">\n' +
                        '                                <li>\n' +
                        '                                    <span nctype="input_checkbox">\n' +
                        '                                        <input type="text" value="2" nc_type="2004" name="sp_val[36][2004]">\n' +
                        '                                    </span>\n' +
                        '                                    <span nctype="pv_name">2</span>\n' +
                        '                                </li>\n' +
                        '                            </ul>\n' +
                        '                        </dd>\n' +
                        '                    </dl>';
                    $("#sku").append(str);
                })


            }
        });
    })

</script>