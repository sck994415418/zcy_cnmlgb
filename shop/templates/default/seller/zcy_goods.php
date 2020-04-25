<?php defined('InShopNC') or exit('Access Invalid!');?>

<meta name="referrer" content="no-referrer">
<script src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.ajaxContent.pack.js"></script>
<script src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery-ui/i18n/zh-CN.js"></script>
<script src="<?php echo RESOURCE_SITE_URL; ?>/js/common_select.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/fileupload/jquery.iframe-transport.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/fileupload/jquery.ui.widget.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/fileupload/jquery.fileupload.js" charset="utf-8"></script>
<script src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.poshytip.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.mousewheel.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.charCount.js"></script>
<!--[if lt IE 8]>
  <script src="<?php echo RESOURCE_SITE_URL; ?>/js/json2.js"></script>
<![endif]-->
<script src="<?php echo SHOP_RESOURCE_SITE_URL; ?>/js/store_goods_add.step2.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL; ?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
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
            <input type="hidden" name="good_id" id="good_id" value="" />
            <input type="hidden" name="goods_name" id="good_name" value=""/>
            <table width="450" height="200" border="0" style="margin: auto;">
                <tr>
                    <th colspan="2" align="center"><h2 align="center">商品上传至政采云</h2></th>
                </tr>



                <tr>
                    <td align="right"><font color="#FF0000">*</font>政采云商品一级属性：</td>
                    <td>
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
            <div class="ncsc-form-goods">
                <dl>
                    <dt><i class="required">*</i><?php echo $lang['store_goods_album_goods_pic'] . $lang['nc_colon']; ?></dt>
                    <dd>
                        <div class="ncsc-goods-default-pic">
                            <div class="goodspic-uplaod">
                                <div class="upload-thumb"> <img nctype="goods_image" src="<?php echo thumb($output['goods'], 240); ?>"/> </div>
                                <input type="hidden" name="image_path" id="image_path" nctype="goods_image" value="<?php echo $output['goods']['goods_image'] ?>" />
                                <span></span>
                                <p class="hint"><?php echo $lang['store_goods_step2_description_one']; ?><?php printf($lang['store_goods_step2_description_two'], intval(C('image_max_filesize')) / 1024); ?></p>
                                <div class="handle">
                                    <div class="ncsc-upload-btn"> <a href="javascript:void(0);"><span>
                                            <input type="file" hidefocus="true" size="1" class="input-file" name="goods_image" id="goods_image">
                                        </span>
<!--                                            <p><i class="icon-upload-alt"></i>图片上传</p>-->
                                        </a> </div>
                                    <a class="ncsc-btn mt5" nctype="show_image" href="<?php echo urlShop('store_album', 'zcy_pic_list', array('item' => 'goods')); ?>"><i class="icon-picture"></i>从图片空间选择</a> <a href="javascript:void(0);" nctype="del_goods_demo" class="ncsc-btn mt5" style="display: none;"><i class="icon-circle-arrow-up"></i>关闭相册</a></div>
                            </div>
                        </div>
                        <div id="demo"></div>
                    </dd>
                </dl>
                <dl>
                    <dt><?php echo $lang['store_goods_index_goods_desc'] . $lang['nc_colon']; ?></dt>
                    <dd id="ncProductDetails">
                        <div class="tabs">
                            <ul class="ui-tabs-nav" jquery1239647486215="2">
                                <li class="ui-tabs-selected"><a href="#panel-1" jquery1239647486215="8"><i class="icon-desktop"></i> 电脑端</a></li>
                                <li class="selected"><a href="#panel-2" jquery1239647486215="9"><i class="icon-mobile-phone"></i>手机端</a></li>
                            </ul>
                            <div id="panel-1" class="ui-tabs-panel" jquery1239647486215="4">
                                <?php showEditor('g_body', $output['goods']['goods_body'], '100%', '480px', 'visibility:hidden;', "false", $output['editor_multimedia']); ?>
                                <div class="hr8">
                                    <div class="ncsc-upload-btn"> <a href="javascript:void(0);"><span>
                                            <input type="file" hidefocus="true" size="1" class="input-file" name="add_album" id="add_album" multiple="multiple">
                                        </span>
<!--                                            <p><i class="icon-upload-alt" data_type="0" nctype="add_album_i"></i>图片上传</p>-->
                                        </a> </div>
                                    <a class="ncsc-btn mt5" nctype="show_desc" href="index.php?act=store_album&op=zcy_pic_list&item=des"><i class="icon-picture"></i><?php echo $lang['store_goods_album_insert_users_photo']; ?></a> <a href="javascript:void(0);" nctype="del_desc" class="ncsc-btn mt5" style="display: none;"><i class=" icon-circle-arrow-up"></i>关闭相册</a> </div>
                                <p id="des_demo"></p>
                            </div>
                            <div id="panel-2" class="ui-tabs-panel ui-tabs-hide" jquery1239647486215="5">
                                <div class="ncsc-mobile-editor">
                                    <div class="pannel">
                                        <div class="size-tip"><span nctype="img_count_tip">图片总数得超过<em>20</em>张</span><i>|</i><span nctype="txt_count_tip">文字不得超过<em>5000</em>字</span></div>
                                        <div class="control-panel" nctype="mobile_pannel">
                                            <?php if (!empty($output['goods']['mb_body'])) { ?>
                                                <?php foreach ($output['goods']['mb_body'] as $val) { ?>
                                                    <?php if ($val['type'] == 'text') { ?>
                                                        <div class="module m-text">
                                                            <div class="tools"><a nctype="mp_up" href="javascript:void(0);">上移</a><a nctype="mp_down" href="javascript:void(0);">下移</a><a nctype="mp_edit" href="javascript:void(0);">编辑</a><a nctype="mp_del" href="javascript:void(0);">删除</a></div>
                                                            <div class="content">
                                                                <div class="text-div"><?php echo $val['value']; ?></div>
                                                            </div>
                                                            <div class="cover"></div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if ($val['type'] == 'image') { ?>
                                                        <div class="module m-image">
                                                            <div class="tools"><a nctype="mp_up" href="javascript:void(0);">上移</a><a nctype="mp_down" href="javascript:void(0);">下移</a><a nctype="mp_rpl" href="javascript:void(0);">替换</a><a nctype="mp_del" href="javascript:void(0);">删除</a></div>
                                                            <div class="content">
                                                                <div class="image-div"><img src="<?php echo $val['value']; ?>"></div>
                                                            </div>
                                                            <div class="cover"></div>
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                        <div class="add-btn">
                                            <ul class="btn-wrap">
                                                <li><a href="javascript:void(0);" nctype="mb_add_img"><i class="icon-picture"></i>
                                                        <p>图片</p>
                                                    </a></li>
                                                <li><a href="javascript:void(0);" nctype="mb_add_txt"><i class="icon-font"></i>
                                                        <p>文字</p>
                                                    </a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="explain">
                                        <dl>
                                            <dt>1、基本要求：</dt>
                                            <dd>（1）手机详情总体大小：图片+文字，图片不超过20张，文字不超过5000字；</dd>
                                            <dd>建议：所有图片都是本宝贝相关的图片。</dd>
                                        </dl><dl>
                                            <dt>2、图片大小要求：</dt>
                                            <dd>（1）建议使用宽度480 ~ 620像素、高度小于等于960像素的图片；</dd>
                                            <dd>（2）格式为：JPG\JEPG\GIF\PNG；</dd>
                                            <dd>举例：可以上传一张宽度为480，高度为960像素，格式为JPG的图片。</dd>
                                        </dl><dl>
                                            <dt>3、文字要求：</dt>
                                            <dd>（1）每次插入文字不能超过500个字，标点、特殊字符按照一个字计算；</dd>
                                            <dd>（2）请手动输入文字，不要复制粘贴网页上的文字，防止出现乱码；</dd>
                                            <dd>（3）以下特殊字符“<”、“>”、“"”、“'”、“\”会被替换为空。</dd>
                                            <dd>建议：不要添加太多的文字，这样看起来更清晰。</dd>
                                        </dl>
                                    </div>
                                </div>
                                <div class="ncsc-mobile-edit-area" nctype="mobile_editor_area">
                                    <div nctype="mea_img" class="ncsc-mea-img" style="display: none;"></div>
                                    <div class="ncsc-mea-text" nctype="mea_txt" style="display: none;">
                                        <p id="meat_content_count" class="text-tip"></p>
                                        <textarea class="textarea valid" nctype="meat_content"></textarea>
                                        <div class="button"><a class="ncsc-btn ncsc-btn-blue" nctype="meat_submit" href="javascript:void(0);">确认</a><a class="ncsc-btn ml10" nctype="meat_cancel" href="javascript:void(0);">取消</a></div>
                                        <a class="text-close" nctype="meat_cancel" href="javascript:void(0);">X</a>
                                    </div>
                                </div>
                                <input name="m_body" autocomplete="off" type="hidden" value='<?php echo $output['goods']['mobile_body']; ?>'>
                            </div>
                        </div>
                    </dd>
                </dl>
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

    $("#one").change(function () {//当省级下拉菜单被改变触发change事件
        // $('#dataLoading').show();
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
    $("#three").change(function () {
        provinceId = $("#three").val();//获取到省和市的共同数组下标
        $.ajax({
            type: "GET",
            url: "/shop/index.php?act=zcy_goods&op=category",
            data: {goods_id: provinceId},
            dataType: "json",
            beforeSend: function () {
                $('#dataLoading').show();
            },
            complete: function () {
                $('#dataLoading').hide();
            },
            success: function (data) {
                $("#sku").html("");
                $.each(data.data_reponse, function (k, v) {
                    var str = ' <dl nc_type="spec_group_dl_0" nctype="spec_group_dl" class="spec-bg">\n' +
                        '                        <dt>\n' +
                        '                            <input readonly name="' + v.propertyId + '" type="text" class="text w60 tip2 tr" value="' + v.attrName + '" nctype="spec_name">\n' +
                        '                        </dt>\n' +
                        '                        <dd>\n' +
                        '                            <ul class="spec">\n';
                    var str2 = "";
                    if (v.attrVals == '' || v.attrVals == [] || v.attrVals == null) {
                        str2 = ' <li>                           <span nctype="input_checkbox">\n' +
                            '                                        <input type="text" value="' + v.attrVals + '" name="' + v.propertyId + '">\n' +
                            '                                    </span>\n' +
                            '                                    <span nctype="pv_name">' + v.group + '</span>\n' +
                            '                                </li>\n';
                    } else {
                        $.each(v.attrVals, function (key, val) {
                            str2 += '<li> <span nctype="input_checkbox">\n' +
                                '     <input type="radio" value="' + val + '" name="' + v.propertyId + '">\n' +
                                '        </span>\n' +
                                '        <span nctype="pv_name">' + val + '</span>\n ' +
                                '                                </li>\n';
                        })
                    }

                    var str3 = '          </ul>\n' +
                        '                        </dd>\n' +
                        '                    </dl>\n';
                    $("#sku").append(str + str2 + str3);
                })


            }
        });
    })
    $(function(){
        //电脑端手机端tab切换
        $(".tabs").tabs();
    });
</script>