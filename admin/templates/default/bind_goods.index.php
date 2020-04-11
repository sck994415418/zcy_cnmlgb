<?php defined('InShopNC') or exit('Access Invalid!'); ?>
<link href="<?php echo ADMIN_TEMPLATES_URL; ?>/css/font/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
<!--[if IE 7]>
  <link rel="stylesheet" href="<?php echo ADMIN_TEMPLATES_URL; ?>/css/font/font-awesome/css/font-awesome-ie7.min.css">
<![endif]-->
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>绑定商品</h3>
            <ul class="tab-base">
                <li><a href="JavaScript:void(0);" class="current"><span>所有商品</span></a></li>
            </ul>
        </div>
    </div>

    <div class="fixed-empty"></div>
    <form method="get" name="formSearch" id="formSearch">
        <input type="hidden" name="act" value="zf_class">
        <input type="hidden" name="op" value="bind_goods">
        <table class="tb-type1 noborder search" style='width:800px'>
            <tbody>
                <tr><input type='hidden' name='zf_class_id' value="<?php echo $output['zf_class_id']; ?>" />
            <th><label for="search_goods_name"> 商品名称</label></th>
            <td><input type="text" value="<?php echo $output['search']['search_goods_name']; ?>" name="search_goods_name" id="search_goods_name" class="txt"></td>
            <th><label for="search_commonid">平台货号</label></th>
            <td><input type="text" value="<?php echo $output['search']['search_id'] ?>" name="search_id" id="search_commonid" class="txt" /></td>
            <th><label>分类</label></th>
            <td id="searchgc_td"></td><input type="hidden" id="choose_gcid" name="choose_gcid" value="0"/>
            </tr>
            <tr>
                <th><label><?php echo $lang['goods_index_brand']; ?></label></th>
                <td>
                    <div id="ajax_brand" class="ncsc-brand-select w180">
                        <div class="selection">
                            <input name="b_name" id="b_name" value="<?php echo $_REQUEST['b_name']; ?>" type="text" class="txt w180" readonly="readonly" />
                            <input type="hidden" name="b_id" id="b_id" value="<?php echo $_REQUEST['b_id']; ?>" />
                        </div>
                        <div class="ncsc-brand-select-container">
                            <div class="brand-index" data-url="index.php?act=common&op=ajax_get_brand">
                                <div class="letter" nctype="letter">
                                    <ul>
                                        <li><a href="javascript:void(0);" data-letter="all">全部品牌</a></li>
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
                                    </ul>
                                </div>
                                <div class="search" nctype="search"><input name="search_brand_keyword" id="search_brand_keyword" type="text" class="text" placeholder="品牌名称关键字查找"/><a href="javascript:void(0);" class="ncsc-btn-mini" style="vertical-align: top;">Go</a></div>
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
                </td>
                <th><label>是否绑定</label></th>
                <td><select name="search_state">
                        <?php foreach ($output['state'] as $key => $val) { ?>
                            <option value="<?php echo $key; ?>" <?php if ($output['search']['search_state'] != '' && $output['search']['search_state'] == $key) { ?>selected<?php } ?>><?php echo $val; ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td><a href="javascript:$('#formSearch').submit();" class="btn-search " title="<?php echo $lang['nc_query']; ?>">&nbsp;</a></td>
                <td class="w120">&nbsp;</td>
            </tr>
            </tbody>
        </table>
    </form>
    <table class="table tb-type2" id="prompt">
        <tbody>
            <tr class="space odd">
                <th colspan="12"><div class="title">
                        <h5>操作提示</h5>
                        <span class="arrow"></span></div></th>
            </tr>
            <tr>
                <td><ul>
                        <li>1.此界面仅仅显示自营店铺的、正规的商品。</li>
                        <li>2.可以在此界面对商品进行绑定。</li>
                    </ul></td>
            </tr>
        </tbody>
    </table>
    <form method='post' id="form_goods" action="<?php echo urlAdmin('zf_class', 'goods_del'); ?>">
        <input type="hidden" name="form_submit" value="ok" />
        <table class="table tb-type2">
            <thead>
                <tr class="thead">
                    <th class="w24"></th>
                    <th class="w60 align-center">平台货号</th>
                    <th colspan="2">商品名称</th>
                    <th>品牌</th>
                    <th class="w72 align-center">价格(元)</th>
                    <th class="w72 align-center">商品状态</th>
                    <th class="w108 align-center"><?php echo $lang['nc_handle']; ?> </th>
                </tr>
            </thead>
            <tbody>
            <style>
                a.a1:link{color:#000000!important;background-color: #33b4b5!important;}
                a.a1:hover{color:red!important;;background-color: #ccc!important;}
            </style>
            <script>
                //綁定、解绑
                function goods_lockup1(ids, state, zf_class_id, _this) {
                    _url = "<?php echo ADMIN_SITE_URL; ?>/index.php?act=zf_class&op=ajax_bind_goods&id=" + ids + "&state=" + state + "&zf_class_id=" + zf_class_id;
                    _obj = $(_this);
                    $.ajax({
                        url: _url,
                        type: "get",
                        dataType: "json",
                        success: function (_data) {
                            if (_data['code'] == 1) {
                                _obj.parents('tr').remove();
                            } else {
                                alert(_data['msg']);
                            }
                        }
                    });
                }
            </script>
            <?php if (!empty($output['goods_list']) && is_array($output['goods_list'])) { ?>
                <?php foreach ($output['goods_list'] as $k => $v) { ?>
                    <tr class="hover edit">
                        <td><input type="checkbox" name="id[]" value="<?php echo $v['goods_id']; ?>" class="checkitem"></td>
                        <td class="align-center"><?php echo $v['goods_id']; ?></td>
                        <td class="w60 picture"><div class="size-56x56"><span class="thumb size-56x56"><i></i><img src="<?php echo thumb($v, 60); ?>" onload="javascript:DrawImage(this, 56, 56);"/></span></div></td>
                        <td><dl class="goods-info"><dt class="goods-name" style="max-width:800px"><?php echo $v['goods_name']; ?></dt></td>
                        <td><?php echo $v['brand_name']; ?></td>
                        <td class="align-center"><?php echo $v['goods_promotion_price'] ?></td>
                        <td class="align-center"><?php echo $output['state'][$v['is_bind']]; ?></td>
                        <td class="align-center">
                            <?php if ($v['is_bind'] == 1) { ?>
                                <a class="a1" style=";cursor:pointer;display: block;width:60px;height:26px;line-height: 26px;margin-left:22px" onclick="javascript:goods_lockup1(<?php echo $v['goods_id']; ?>, 1,<?php echo $output['zf_class_id']; ?>, this);">解绑</a>
                            <?php } else { ?>
                                <a class="a1" style="cursor:pointer;display: block;width:60px;height:26px;line-height: 26px;margin-left:22px;" onclick="javascript:goods_lockup1(<?php echo $v['goods_id']; ?>, 0,<?php echo $output['zf_class_id']; ?>, this);">绑定</a>      
                            <?php } ?>
                        </td>
                    </tr>
                    <tr style="display:none;">
                        <td colspan="20"><div class="ncsc-goods-sku ps-container"></div></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr class="no_data">
                    <td colspan="15"><?php echo $lang['nc_no_record']; ?></td>
                </tr>
            <?php } ?>
            <th><label>分类</label></th>
            <td id="searchgc_td"></td><input type="hidden" id="choose_gcid" name="choose_gcid" value="0"/>
            </tbody>
            <tfoot>
                <tr class="tfoot">
                    <td><input type="checkbox" class="checkall" id="checkallBottom"></td>
                    <td colspan="16"><label for="checkallBottom"><?php echo $lang['nc_select_all']; ?></label>
                        &nbsp;&nbsp;<a href="JavaScript:void(0);" class="btn" nctype="lockup_batch1"><span>批量绑定</span></a>
                        &nbsp;&nbsp;<a href="JavaScript:void(0);" class="btn" nctype="lockup_batch0"><span>批量解绑</span></a>
                        <div class="pagination"> <?php echo $output['page']; ?> </div></td>
                </tr>
            </tfoot>
        </table>
    </form>
</div>

<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/dialog/dialog.js" id="dialog_js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery-ui/jquery.ui.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.mousewheel.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/common_select.js" charset="utf-8"></script>

<script type="text/javascript">
                                    var SITEURL = "<?php echo SHOP_SITE_URL; ?>";
                                    $(function () {
                                        //商品分类
                                        init_gcselect(<?php echo $output['gc_choose_json']; ?>,<?php echo $output['gc_json'] ?>);
                                        /* AJAX选择品牌 */
                                        $("#ajax_brand").brandinit();
                                        $('#ncsubmit').click(function () {
                                            $('input[name="op"]').val('goods');
                                            $('#formSearch').submit();
                                        });
                                        // 批量绑定
                                        $('a[nctype="lockup_batch0"]').click(function () {
                                            str = getId();
                                            if (str) {
                                                goods_lockup(str, 1);
                                            }
                                        });
                                        // 批量解绑
                                        $('a[nctype="lockup_batch1"]').click(function () {
                                            str = getId();
                                            if (str) {
                                                goods_lockup(str, 0);
                                            }
                                        });


                                    });

                                    // 获得选中ID
                                    function getId() {
                                        var str = '';
                                        $('#form_goods').find('input[name="id[]"]:checked').each(function () {
                                            id = parseInt($(this).val());
                                            if (!isNaN(id)) {
                                                str += id + ',';
                                            }
                                        });
                                        if (str == '') {
                                            return false;
                                        }
                                        str = str.substr(0, (str.length - 1));
                                        return str;
                                    }


</script>
