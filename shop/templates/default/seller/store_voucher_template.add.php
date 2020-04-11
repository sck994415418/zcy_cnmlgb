<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_SITE_URL . "/js/jquery-ui/themes/ui-lightness/jquery.ui.css"; ?>"/>
<!--开始-->
<?php defined('InShopNC') or exit('Access Invalid!'); ?>
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
<!--结束-->
<div class="tabmenu">
    <?php include template('layout/submenu'); ?>
</div>
<div class="ncsc-form-default">
    <form id="add_form" method="post" enctype="multipart/form-data">
        <input type="hidden" id="act" name="act" value="store_voucher"/>
        <?php if ($output['type'] == 'add') { ?>
            <input type="hidden" id="op" name="op" value="templateadd"/>
        <?php } else { ?>
            <input type="hidden" id="op" name="op" value="templateedit"/>
            <input type="hidden" id="tid" name="tid" value="<?php echo $output['t_info']['voucher_t_id']; ?>"/>
        <?php } ?>
        <input type="hidden" id="form_submit" name="form_submit" value="ok"/>
        <dl>
            <dt><i class="required">*</i><?php echo $lang['voucher_template_title'] . $lang['nc_colon']; ?></dt>
            <dd>
                <input type="text" class="w300 text" name="txt_template_title" value="<?php echo $output['t_info']['voucher_t_title']; ?>">
                <span></span>
            </dd>
        </dl>
        <?php if ($output['isOwnShop']) { ?>
            <dl>
                <dt><i class="required">*</i>店铺分类</dt>
                <dd>
                    <select name="sc_id">
                        <option value="0">店铺分类</option>
                        <?php foreach ($output['store_class'] as $k => $v) { ?>
                            <option value="<?php echo $v['sc_id']; ?>" <?php
                            if ($output['t_info']['voucher_t_sc_id'] == $v['sc_id']) {
                                echo 'selected';
                            }
                            ?>><?php echo $v['sc_name']; ?></option>
                                <?php } ?>
                    </select>
                    <span></span>
                </dd>
            </dl>
        <?php } else { ?>
            <input type="hidden" name="sc_id" value="<?php echo $output['store_info']['sc_id']; ?>"/>
        <?php } ?>
        <!--添加的内容开始-->
        <style type="text/css">
            .type{
                margin-left:5px;
            }
            .wp_category_list{
                width:214px;
            }
        </style>
        <script type="text/javascript">
            function choice(_value) {
                if (_value == 0) {                 //全品类
                    $("#brand").hide();
                    $("#class").hide();
                } else if (_value == 1) {          //分类
                    $("#brand").hide();
                    $("#class").show();
                } else if (_value == 2) {        //品牌类
                    $("#brand").show();
                    $("#class").hide();
                }
            }
            $(function () {
                /**
                 * 对象操作:用于编辑的初始化操作
                 */
                if ($(".type:checked").val() == 0) {                 //全品类
                    $("#brand").css('display', 'none');
                    $("#class").css('display', 'none');
                } else if ($(".type:checked").val() == 1) {          //分类
                    $("#brand").hide();
                    $("#class").show();
                } else if ($(".type:checked").val() == 2) {        //品牌类
                    $("#brand").show();
                    $("#class").hide();
                }
            })
        </script>
        <dl>
            <dt><i class="required">*</i>对象选择：</dt>
            <dd>
                全品类： <input class="type" type="radio" name="voucher_t_type" onchange="javascript:choice(this.value)" value="0" <?php
                if (empty($output['t_info']) || $output['t_info']['voucher_t_type'] == 0) {
                    echo "checked";
                }
                ?> />            
<!--                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                分类： <input class="type" type="radio" name="voucher_t_type" onchange="javascript:choice(this.value)" value="1" <?php
                if ($output['t_info']['voucher_t_type'] == 1) {
                    echo "checked";
                }
                ?>  />-->
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                品牌类： <input class="type" type="radio" name="voucher_t_type" onchange="javascript:choice(this.value)" value="2" <?php
                if ($output['t_info']['voucher_t_type'] == 2) {
                    echo "checked";
                }
                ?>  />
            </dd>
        </dl>    
        <!--品牌类对象选择-->
        <dl id="brand" style="overflow: visible;">
            <dt><em class="pngFix"></em><?php echo "详细品牌选择："; ?></dt>
            <dd>
                <div class="ncsc-brand-select">
                    <div class="selection">
                        <input name="voucher_t_type_name" id="b_name" value="<?php if($output['t_info']['voucher_t_type']==2){ echo $output['t_info']['voucher_t_type_name'];} ?>" type="text" class="text w180" readonly="readonly" />
                        <input type="hidden" name="voucher_t_type_id[2]" id="b_id" value="<?php echo $output['t_info']['voucher_t_type_id']; ?>" />
                        <em class="add-on" nctype="add-on"><i class="icon-collapse"></i></em>
                    </div>       
                    <div class="ncsc-brand-select-container">
                        <div class="brand-index" data-tid="<?php echo $output['goods_class']['type_id']; ?>" data-url="<?php echo urlShop('store_goods_add', 'ajax_get_brand'); ?>">
                            <div class="letter" nctype="letter">
                                <ul>
                                    <li><a href="javascript:void(0);" data-letter="all">全部</a></li>
                                    <li><a href="javascript:void(0);" data-letter="A">A</a></li>
                                    <li><a href="javascript:void(0);" data-letter="B">B</a></li>
                                    <li><a href="javascript:void(0);" data-letter="C">C</a></li>
                                    <li><a href="javascript:void(0);" data-letter="D">D</a></li>
                                    <li><a href="javascript:void(0);" data-letter="E">E</a></li>
                                    <li><a href="javascript:void(0);" data-letter="F">F</a></li>
                                    <li><a href="javascript:void(0);" data-letter="G">G</a></li>
                                    <li><a href="javascript:void(0);" data-letter="H">H</a></li>
                                    <li><a href="javascript:void(0);" data-letter="I">I</a></li>
                                    <li><a href="javascript:void(0);" data-letter="J">J</a></li>
                                    <li><a href="javascript:void(0);" data-letter="K">K</a></li>
                                    <li><a href="javascript:void(0);" data-letter="L">L</a></li>
                                    <li><a href="javascript:void(0);" data-letter="M">M</a></li>
                                    <li><a href="javascript:void(0);" data-letter="N">N</a></li>
                                    <li><a href="javascript:void(0);" data-letter="O">O</a></li>
                                    <li><a href="javascript:void(0);" data-letter="P">P</a></li>
                                    <li><a href="javascript:void(0);" data-letter="Q">Q</a></li>
                                    <li><a href="javascript:void(0);" data-letter="R">R</a></li>
                                    <li><a href="javascript:void(0);" data-letter="S">S</a></li>
                                    <li><a href="javascript:void(0);" data-letter="T">T</a></li>
                                    <li><a href="javascript:void(0);" data-letter="U">U</a></li>
                                    <li><a href="javascript:void(0);" data-letter="V">V</a></li>
                                    <li><a href="javascript:void(0);" data-letter="W">W</a></li>
                                    <li><a href="javascript:void(0);" data-letter="X">X</a></li>
                                    <li><a href="javascript:void(0);" data-letter="Y">Y</a></li>
                                    <li><a href="javascript:void(0);" data-letter="Z">Z</a></li>
                                    <li><a href="javascript:void(0);" data-letter="0-9">其他</a></li>
                                    <li><a href="javascript:void(0);" data-empty="0">清空</a></li>
                                </ul>
                            </div>
                            <div class="search" nctype="search">
                                <input name="search_brand_keyword" id="search_brand_keyword" type="text" class="text" placeholder="品牌名称关键字查找"/><a href="javascript:void(0);" class="ncsc-btn-mini" style="vertical-align: top;">Go</a></div>
                        </div>
                        <div class="brand-list" nctype="brandList">
                            <ul nctype="brand_list">
                                <?php if (is_array($output['brand_list']) && !empty($output['brand_list'])) { ?>
                                    <?php foreach ($output['brand_list'] as $val) { ?>
                                        <li data-id='<?php echo $val['brand_id']; ?>'data-name='<?php echo $val['brand_name']; ?>'><em><?php echo $val['brand_initial']; ?></em><?php echo $val['brand_name']; ?></li>
                                    <?php } ?>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="no-result" nctype="noBrandList" style="display: none;">没有符合"<strong>搜索关键字</strong>"条件的品牌</div>
                    </div>        
                </div>
                <span style="color:red;">注：</span>点击右侧按钮，可以根据首字母或者关键字进行搜索相应的品牌
            </dd>
        </dl>  
        <!--分类对象选择-->
        <dl id="class">
            <dt><em class="pngFix"></em><?php echo "详细分类选择："; ?></dt>
            <dd>
                <?php if ($output['t_info']['voucher_t_type'] == 1 && !empty($output['t_info']['voucher_t_type_id'])) { ?>
                    当前分类：<span><?php echo $output['t_info']['voucher_t_type_name']; ?> </span>
                <?php } ?>
                <!--S 分类选择区域-->
                <div class="wrapper_search">
                    <div class="wp_sort">
                        <div id="dataLoading" class="wp_data_loading">
                            <div class="data_loading"><?php echo $lang['store_goods_step1_loading']; ?></div>
                        </div>
                        <div id="class_div" class="wp_sort_block">
                            <div class="sort_list">
                                <div class="wp_category_list">
                                    <div id="class_div_1" class="category_list">
                                        <ul>
                                            <?php if (isset($output['goods_class']) && !empty($output['goods_class'])) { ?>
                                                <?php foreach ($output['goods_class'] as $key => $val) { ?>
                                                    <li class="" nctype="selClass" data-param="{gcid:<?php echo $val['gc_id']; ?>,deep:1,tid:<?php echo $val['type_id']; ?>}"> <a class="" href="javascript:void(0)"><i class="icon-double-angle-right"></i><?php echo $val['gc_name']; ?></a></li>                             
                                                <?php } ?>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="sort_list">
                                <div class="wp_category_list blank">
                                    <div id="class_div_2" class="category_list">
                                        <ul>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="sort_list sort_list_last">
                                <div class="wp_category_list blank">
                                    <div id="class_div_3" class="category_list">
                                        <ul>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert">
                        <dl class="hover_tips_cont">
                            <dt id="commodityspan"><span style="color:#F00;">请选择商品类型：</span></dt>
                            <dt id="commoditydt" style="display: none;" class="current_sort">您当前选择的商品分类是<?php echo $lang['nc_colon']; ?></dt>
                            <dd id="commoditydd"></dd>
                        </dl>
                    </div>
                    <input type="hidden" name="voucher_t_type_id[1]" id="class_id" value="" />
                    <input type="hidden" name="t_id" id="t_id" value="" />   
                </div>
                <script src="<?php echo RESOURCE_SITE_URL; ?>/js/common_select.js"></script> 
                <script src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.mousewheel.js"></script> 
                <script src="<?php echo SHOP_RESOURCE_SITE_URL; ?>/js/store_goods_add.step1.js"></script> 
                <script>
                    SEARCHKEY = '<?php echo $lang['store_goods_step1_search_input_text']; ?>';
                    RESOURCE_SITE_URL = '<?php echo RESOURCE_SITE_URL; ?>';
                </script>
            </dd>
        </dl>
        <!--添加的内容结束-->
        <dl>
            <dt><em class="pngFix"></em><?php echo $lang['voucher_template_enddate'] . $lang['nc_colon']; ?></dt>
            <dd>
                <input type="text" class="text w70" id="txt_template_enddate" name="txt_template_enddate" value="" readonly><em class="add-on"><i class="icon-calendar"></i></em>
                <span></span><p class="hint">
                    <?php if ($output['isOwnShop']) { ?>
                        留空则默认30天之后到期
                    <?php } else { ?>
                        <?php echo $lang['voucher_template_enddate_tip']; ?><?php echo @date('Y-m-d', $output['quotainfo']['quota_starttime']); ?> ~ <?php echo @date('Y-m-d', $output['quotainfo']['quota_endtime']); ?>
                    <?php } ?>
                </p>
            </dd>
        </dl>
        <dl>
            <dt><?php echo $lang['voucher_template_price'] . $lang['nc_colon']; ?></dt>
            <dd>
                <select id="select_template_price" name="select_template_price" class="w80 vt">
                    <?php if (!empty($output['pricelist'])) { ?>
                        <?php foreach ($output['pricelist'] as $voucher_price) { ?>
                            <option value="<?php echo $voucher_price['voucher_price']; ?>" <?php echo $output['t_info']['voucher_t_price'] == $voucher_price['voucher_price'] ? 'selected' : ''; ?>><?php echo $voucher_price['voucher_price']; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select><em class="add-on"><i class="icon-renminbi"></i></em>
                <span></span>
            </dd>
        </dl>
        <dl>
            <dt><i class="required">*</i><?php echo $lang['voucher_template_total'] . $lang['nc_colon']; ?></dt>
            <dd>
                <input type="text" class="w70 text" name="txt_template_total" value="<?php echo $output['t_info']['voucher_t_total']; ?>">
                <span></span>
            </dd>
        </dl>
        <dl>
            <dt><i class="required">*</i><?php echo $lang['voucher_template_eachlimit'] . $lang['nc_colon']; ?></dt>
            <dd>
                <select name="eachlimit" class="w80">
                    <option value="0"><?php echo $lang['voucher_template_eachlimit_item']; ?></option>
                    <?php for ($i = 1; $i <= intval(C('promotion_voucher_buyertimes_limit')); $i++) { ?>
                        <option value="<?php echo $i; ?>" <?php echo $output['t_info']['voucher_t_eachlimit'] == $i ? 'selected' : ''; ?>><?php echo $i; ?><?php echo $lang['voucher_template_eachlimit_unit']; ?></option>
                    <?php } ?>
                </select>
            </dd>
        </dl>
        <dl>
            <dt><i class="required">*</i><?php echo $lang['voucher_template_orderpricelimit'] . $lang['nc_colon']; ?></dt>
            <dd>
                <input type="text" name="txt_template_limit" class="text w70" value="<?php echo $output['t_info']['voucher_t_limit']; ?>"><em class="add-on"><i class="icon-renminbi"></i></em>
                <span></span>
            </dd>
        </dl>
        <dl>
            <dt><i class="required">*</i><?php echo $lang['voucher_template_describe'] . $lang['nc_colon']; ?></dt>
            <dd>
                <textarea  name="txt_template_describe" class="textarea w400 h600"><?php echo $output['t_info']['voucher_t_desc']; ?></textarea>
                <span></span>
            </dd>
        </dl>
        <dl>
            <dt><i class="required">*</i><?php echo $lang['voucher_template_image'] . $lang['nc_colon']; ?></dt>
            <dd>
                <div id="customimg_preview" class="ncsc-upload-thumb voucher-pic"><p><?php if ($output['t_info']['voucher_t_customimg']) { ?>
                            <img src="<?php echo $output['t_info']['voucher_t_customimg']; ?>"/>
                        <?php } else { ?>
                            <i class="icon-picture"></i>
                        <?php } ?></p>
                </div>
                <div class="ncsc-upload-btn"><a href="javascript:void(0);"><span>
                            <input type="file" hidefocus="true" size="1" class="input-file" name="customimg" id="customimg" nc_type="customimg"/>
                        </span>
                        <p><i class="icon-upload-alt"></i>图片上传</p>
                    </a> </div>
                <p class="hint"><?php echo $lang['voucher_template_image_tip']; ?></p>
            </dd>
        </dl>
        <?php if ($output['type'] == 'edit') { ?>
            <dl>
                <dt><em class="pngFix"></em><?php echo $lang['nc_status'] . $lang['nc_colon']; ?></dt>
                <dd>
                    <input type="radio" value="<?php echo $output['templatestate_arr']['usable'][0]; ?>" name="tstate" <?php echo $output['t_info']['voucher_t_state'] == $output['templatestate_arr']['usable'][0] ? 'checked' : ''; ?>> <?php echo $output['templatestate_arr']['usable'][1]; ?>
                    <input type="radio" value="<?php echo $output['templatestate_arr']['disabled'][0]; ?>" name="tstate" <?php echo $output['t_info']['voucher_t_state'] == $output['templatestate_arr']['disabled'][0] ? 'checked' : ''; ?>> <?php echo $output['templatestate_arr']['disabled'][1]; ?>
                </dd>
            </dl>
        <?php } ?>
        <div class="bottom">
            <label class="submit-border"><input id='btn_add' type="submit" class="submit" value="<?php echo $lang['nc_submit']; ?>" /></label>
        </div>
    </form>
</div>
<script src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery-ui/i18n/zh-CN.js"></script>
<script>
//判断是否显示预览模块
<?php if (!empty($output['t_info']['voucher_t_customimg'])) { ?>
                        $('#customimg_preview').show();
<?php } ?>
                    var year = <?php echo date('Y', $output['quotainfo']['quota_endtime']); ?>;
                    var month = <?php echo intval(date('m', $output['quotainfo']['quota_endtime'])); ?>;
                    var day = <?php echo intval(date('d', $output['quotainfo']['quota_endtime'])); ?>;
                    $(document).ready(function () {
                        //日期控件
                        $('#txt_template_enddate').datepicker();

                        var currDate = new Date();
                        var date = currDate.getDate();
                        date = date + 1;
                        currDate.setDate(date);
                        $('#txt_template_enddate').datepicker("option", "minDate", currDate);
<?php if (!$output['isOwnShop']) { ?>
                            $('#txt_template_enddate').datepicker("option", "maxDate", new Date(year, month - 1, day));
<?php } ?>


                        $('#txt_template_enddate').val("<?php echo $output['t_info']['voucher_t_end_date'] ? @date('Y-m-d', $output['t_info']['voucher_t_end_date']) : ''; ?>");
                        $('#customimg').change(function () {
                            var src = getFullPath($(this)[0]);
                            if (navigator.userAgent.indexOf("Firefox") > 0) {
                                $('#customimg_preview').show();
                                $('#customimg_preview').children('p').html('<img src="' + src + '">');
                            }
                        });
                        //表单验证
                        $('#add_form').validate({
                            errorPlacement: function (error, element) {
                                var error_td = element.parent('dd').children('span');
                                error_td.append(error);
                            },
                            rules: {
                                txt_template_title: {
                                    required: true,
                                    rangelength: [0, 100]
                                },
                                txt_template_total: {
                                    required: true,
                                    digits: true
                                },
                                txt_template_limit: {
                                    required: true,
                                    number: true
                                },
                                txt_template_describe: {
                                    required: true
                                }
                            },
                            messages: {
                                txt_template_title: {
                                    required: '<i class="icon-exclamation-sign"></i><?php echo $lang['voucher_template_title_error']; ?>',
                                    rangelength: '<i class="icon-exclamation-sign"></i><?php echo $lang['voucher_template_title_error']; ?>'
                                },
                                txt_template_total: {
                                    required: '<i class="icon-exclamation-sign"></i><?php echo $lang['voucher_template_total_error']; ?>',
                                    digits: '<i class="icon-exclamation-sign"></i><?php echo $lang['voucher_template_total_error']; ?>'
                                },
                                txt_template_limit: {
                                    required: '<i class="icon-exclamation-sign"></i><?php echo $lang['voucher_template_limit_error']; ?>',
                                    number: '<i class="icon-exclamation-sign"></i><?php echo $lang['voucher_template_limit_error']; ?>'
                                },
                                txt_template_describe: {
                                    required: '<i class="icon-exclamation-sign"></i><?php echo $lang['voucher_template_describe_error']; ?>'
                                }
                            }
                        });
                    });
</script>