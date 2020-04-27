<?php defined('InShopNC') or exit('Access Invalid!'); ?>

<meta name="referrer" content="no-referrer">
<!--<link href="--><?php //echo SHOP_TEMPLATES_URL;?><!--/select2.min.css" rel="stylesheet" type="text/css">-->
<!--<script src="--><?php //echo RESOURCE_SITE_URL; ?><!--/js/select2.min.js"></script>-->

<script src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.ajaxContent.pack.js"></script>
<script src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery-ui/i18n/zh-CN.js"></script>
<script src="<?php echo RESOURCE_SITE_URL; ?>/js/common_select.js"></script>

<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/fileupload/jquery.iframe-transport.js"
        charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/fileupload/jquery.ui.widget.js"
        charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/fileupload/jquery.fileupload.js"
        charset="utf-8"></script>
<script src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.poshytip.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.mousewheel.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.charCount.js"></script>
<!--[if lt IE 8]>
<script src="<?php echo RESOURCE_SITE_URL; ?>/js/json2.js"></script>
<![endif]-->
<script src="<?php echo SHOP_RESOURCE_SITE_URL; ?>/js/store_goods_add.step2.js"></script>
<link rel="stylesheet" type="text/css"
      href="<?php echo RESOURCE_SITE_URL; ?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"/>
<style>
    .ncsc-form-goods {
        border: solid #E6E6E6;
        border-width: 1px 1px 0 1px;
    }

    .ncsc-form-goods h3 {
        font-size: 14px;
        font-weight: 600;
        line-height: 22px;
        color: #000;
        clear: both;
        background-color: #F5F5F5;
        padding: 5px 0 5px 12px;
        border-bottom: solid 1px #E7E7E7;
    }

    .ncsc-form-goods dl {
        font-size: 0;
        *word-spacing: -1px /*IE6、7*/;
        line-height: 20px;
        clear: both;
        padding: 0;
        margin: 0;
        border-bottom: solid 1px #E6E6E6;
        overflow: hidden;
    }

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
        <form method="post" action="<?php echo urlShop('zcy_goods', 'zcy_goods2');?>">
            <input type="hidden" name="good_id" id="good_id" value="<?php echo $_GET['goods_id']?>"/>
<!--            <input type="hidden" name="goods_name" id="good_name" value=""/>-->
            <table width="450" height="200" border="0" style="margin: auto;">
                <tr>
                    <th colspan="2" align="center"><h2 align="center">商品上传至政采云</h2></th>
                </tr>


                <tr>
                    <td align="right"><font color="#FF0000">*</font>政采云商品一级属性：</td>
                    <td>
                        <select name="one" id="one">
                            <option value=""></option>
                            <?php foreach ($output['goods_class'] as $key => $val) { ?>
                                <?php if ($val['level'] == 1) { ?>
                                    <option value="<?php echo $val['id']; ?>"><?php echo $val['name']; ?></option>
                                <?php } ?>
                            <?php } ?>
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

            <div class="bottom tc hr32">
                <label class="submit-border">
                    <input type="submit" class="submit" value="下一步">
                </label>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    var provinceId = null;//纪录共同的数组下标值

    $("#one").change(function () {//当省级下拉菜单被改变触发change事件
        $("#two").html("<option>--请选择--</option>");
        $("#three").html("<option>--请选择--</option>");
        provinceId = $("#one").val();
        $.ajax({
            type: "GET",
            url: "/shop/index.php?act=zcy_goods&op=linkage",
            data: {id: provinceId},
            dataType: "json",
            beforeSend: function () {
                $('#dataLoading').show();
            },
            complete: function () {
                $('#dataLoading').hide();
            },
            success: function (data) {
                $.each(data, function (k, v) {
                    var str = "<option value=" + v.id + ">" + v.name + "</option>"
                    $("#two").append(str);//添加option标签
                })
            }
        });
    });
    //----------------------------联动第三级-------------------------------------------------------
    $("#two").change(function () {
        $("#three").html("<option>--请选择--</option>");
        provinceId = $("#two").val();
        $.ajax({
            type: "GET",
            url: "/shop/index.php?act=zcy_goods&op=linkage",
            data: {id: provinceId},
            dataType: "json",
            beforeSend: function () {
                $('#dataLoading').show();
            },
            complete: function () {
                $('#dataLoading').hide();
            },
            success: function (data) {
                $.each(data, function (k, v) {
                    var str = "<option value=" + v.id + ">" + v.name + "</option>"
                    $("#three").append(str);//添加option标签
                })
            }
        });
    })
    function get_brand(k)
    {
        var str2 = '';
        var str_sck_1 = '';
        var str_sck_2 = '';
        var str_sck_3 = '';
        $.ajax({
            type: "GET",
            url: "/shop/index.php?act=zcy_config&op=get_brand_myself",
            data: {goods_id: provinceId},
            dataType: "json",
            beforeSend: function () {
                $('#dataLoading').show();
            },
            complete: function () {
                $('#dataLoading').hide();
            },
            success: function (data) {
                // console.log(data)
                $.each(data,function(ks,vs){
                    str_sck_3 += '<option value="'+vs.fullName+'">'+vs.fullName+'</option>';
                })
                $('#get_brand').html(str_sck_3)

            }
        });
    }



</script>