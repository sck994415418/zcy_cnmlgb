<?php defined('InShopNC') or exit('Access Invalid!'); ?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3><?php echo $lang['nc_type_manage']; ?></h3>
            <ul class="tab-base">
                <li><a class="current" href="JavaScript:void(0);"><span>列表</span></a></li>
                <li><a href="index.php?act=goods_orm&op=add"><span>新增</span></a></li>

                <li> <form method="get" style="margin-top:10px;margin-left: 20px">
                        <input type="hidden" name="act" value="goods_orm" />
                        <input type="hidden" name="op" value="download" />
                        <select name="is_bind_type">
                            <option value="1" <?php
                            if ($output['is_bind_type'] == 1) {
                                echo 'selected';
                            }
                            ?>>主动绑定显示</option>       
                            <option value="2" <?php
                            if ($output['is_bind_type'] == 2) {
                                echo 'selected';
                            }
                            ?>>被动绑定显示</option>
                            <option value="3" <?php
                            if ($output['is_bind_type'] == 3) {
                                echo 'selected';
                            }
                            ?>>不确定是否显示</option>
                        </select>
                        关键字：<input type="text" size="30" name="keyword">
                        <input type="submit" value="导出" />
                    </form></li>
            </ul>
        </div>
    </div>
    <div class="fixed-empty"></div>
    <table id="prompt" class="table tb-type2">
        <tbody>
            <tr>
                <td width="75%">
                    <form method="get" style="margin-top:10px;margin-left: 20px">
                        <input type="hidden" name="act" value="goods_orm" />
                        <input type="hidden" name="op" value="index" />
                        <select name="is_bind_type">
                            <option value="1" <?php
                            if ($output['is_bind_type'] == 1) {
                                echo 'selected';
                            }
                            ?>>主动绑定显示</option>       
                            <option value="2" <?php
                            if ($output['is_bind_type'] == 2) {
                                echo 'selected';
                            }
                            ?>>被动绑定显示</option>
                            <option value="3" <?php
                            if ($output['is_bind_type'] == 3) {
                                echo 'selected';
                            }
                            ?>>不确定是否显示</option>
                        </select>
                        关键字：<input type="text" size="30" name="keyword">
                        <input type="submit" value="提交" />
                    </form>
                </td>
            </tr>
        </tbody>
        <tbody>	
            <tr class="space odd">
                <th colspan="12" class="nobg"> 
                    <div class="title">
                        <h5>操作提示</h5>
                        <span class="arrow"></span> 
                    </div>
                </th>  
            </tr>
            <tr class="odd">
                <td>
                    <ul>
                        <li>1.省政府后台：http://211.90.38.212:8080/TPBidder/WSSCZtbMis_HeBei/Account/login.aspx</li>
                        <li>2.进入商品维护--云端商品报价</li>
                        <li>3.选择相应的类目，查看右侧相应的展示商品</li>
                        <li>4.点击详细，记录productId，productName，productUrl，商品goods_id</li>
                        <li>5.主动绑定：绑定他人大于等于2的报价数的商品;
                            被动绑定：他人绑定我们的商品;
                            不确定显示： 除去肯定会显得的商品，不确定显示的商品，需要记录，并被他人所绑定。
                        </li>
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>
    <form id="form_spec" method="get" action="">
        <input type="hidden" name="act" value="goods_orm" />
        <input type="hidden" name="op" value="del" />
        <table class="table tb-type2">
            <thead>
                <tr class="thead">
                    <th></th>
                    <th>sku</th>
                    <th>商品名字</th>
                    <th>政府商品id</th>
                    <?php if ($output['is_bind_type'] == 1) { ?>
                        <th>政府商品名字</th>
                    <?php } else { ?>
                        <th>报价数</th>
                    <?php } ?>
                    <th class="align-center"><?php echo $lang['nc_handle']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($output['goods_orm_list']) && is_array($output['goods_orm_list'])) { ?>
                    <?php if ($output['is_bind_type'] == 1) { ?>
                        <?php foreach ($output['goods_orm_list'] as $val) { ?>
                            <tr class="hover edit">
                                <td class="w24"><input type="checkbox" class="checkitem" name="id[]" value="<?php echo $val['id']; ?>" /></td>
                                <td class=""><?php echo $val['skuid']; ?></td>
                                <td class="w400 name"><a href="<?php echo $val['productUrlEC']; ?>" target="top" title="<?php echo $val['productUrlEC']; ?>"><?php echo $val['productNameEC']; ?></a></td>
                                <td class="w50 name"><?php echo $val['productId']; ?></td>
                                <td class="w400 name"><a href="<?php echo $val['productUrl']; ?>" target="top" title="<?php echo $val['productUrl']; ?>"><?php echo $val['productName']; ?></a></td>
                                <td class="w96 align-center"><a href="index.php?act=goods_orm&op=edit&id=<?php echo $val['id']; ?>">编辑</a> | <a onclick="if (confirm('<?php echo $lang['nc_ensure_del']; ?>')) {
                                            location.href = 'index.php?act=goods_orm&op=del&id=<?php echo $val['id']; ?>';
                                        } else {
                                            return false;
                                        }" href="javascript:void(0)">删除</a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } elseif ($output['is_bind_type'] == 2) { ?>
                        <?php foreach ($output['goods_orm_list'] as $val) { ?>
                            <tr class="hover edit">
                                <td class="w24"><input type="checkbox" class="checkitem" name="goods_id[]" value="<?php echo $val['goods_id']; ?>" /></td>
                                <td class="goods_id"><a name="<?php echo $val['goods_id']; ?>"><?php echo $val['goods_id']; ?></a></td>
                                <td class="w350 name" style="width:450px!important">
                                    <a href="http://www.nrwspt.com/shop/index.php?act=goods&op=index&goods_id=<?php echo $val['goods_id']; ?>"
                                       target="top" title="<?php echo $val['goods_name']; ?>"><?php echo $val['goods_name']; ?></a>
                                </td>
                                <td class="w50 name">
                                    <?php
                                    if (!empty($val['product_id'])) {
                                        echo $val['product_id'];
                                    } else {
                                        echo "暂无信息";
                                    }
                                    ?>
                                </td>
                                <td class="w400 name">
                                    <?php if (!empty($val['store_num'])) { ?>
                                        <a href="http://www.hebzfcgwssc.com/Mall/HeBei/detail.aspx?product_id=<?php echo $val['product_id']; ?>" target="top" title="<?php echo $val['store_num']; ?>"><?php echo $val['store_num']; ?></a>
                                    <?php } else { ?>
                                        <span>暂无信息</span>
                                    <?php } ?>
                                </td>
                                <td class="w120 align-center">
                                    <a href="index.php?act=goods_orm&op=show_data&is_bind_type=2&goods_id=<?php echo $val['goods_id']; ?>&bind_page=<?php echo $output['bind_page']; ?>">查看信息</a> |
                                    <a onclick="del_goods(this)" nctype="<?php echo $val['goods_id']; ?>" title="删除变动的goods_id" href="javascript:void(0)">删除</a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } elseif ($output['is_bind_type'] == 3) { ?>    
                        <?php foreach ($output['goods_orm_list'] as $val) { ?>
                            <tr class="hover edit">
                                <td class="w24"><input type="checkbox" class="checkitem" name="goods_id[]" value="<?php echo $val['goods_id']; ?>" /></td>
                                <td class=""><a name="<?php echo $val['goods_id']; ?>"><?php echo $val['goods_id']; ?></a></td>
                                <td class="w350 name" style="width:450px!important">
                                    <a href="http://www.nrwspt.com/shop/index.php?act=goods&op=index&goods_id=<?php echo $val['goods_id']; ?>"
                                       target="top" title="<?php echo $val['goods_name']; ?>"><?php echo $val['goods_name']; ?></a>
                                </td>
                                <td class="w50 name">
                                    <?php
                                    if (!empty($val['product_id'])) {
                                        echo $val['product_id'];
                                    } else {
                                        echo "暂无信息";
                                    }
                                    ?>
                                </td>
                                <td class="w400 name">
                                    <?php if (!empty($val['store_num'])) { ?>
                                        <a href="http://www.hebzfcgwssc.com/Mall/HeBei/detail.aspx?product_id=<?php echo $val['product_id']; ?>" target="top" title="<?php echo $val['store_num']; ?>"><?php echo $val['store_num']; ?></a>
                                    <?php } else { ?>
                                        <span>暂无信息</span>
                                    <?php } ?>
                                </td>

                                <td class="w120 align-center">
                                    <a href="index.php?act=goods_orm&op=show_data&is_bind_type=3&goods_id=<?php echo $val['goods_id']; ?>&bind_page=<?php echo $output['bind_page']; ?>">查看信息</a> |
                                    <a onclick="is_show(this)" nctype="<?php echo $val['goods_id']; ?>" href="javascript:void(0)">确认显示</a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>   
                <?php } else { ?>
                    <tr class="no_data">
                        <td colspan="10"><?php echo $lang['nc_no_record']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <?php if (!empty($output['goods_orm_list']) && is_array($output['goods_orm_list'])) { ?>
                <tfoot>
                    <tr>
                        <td><input type="checkbox" class="checkall" id="checkallBottom" /></td>
                        <td id="dataFuncs" colspan="16">
                            <!--                            <label for="checkallBottom">全选</label>&nbsp;&nbsp;
                                                        <a class="btn" onclick="submit_form('del');" href="JavaScript:void(0);"> <span>删除</span></a>-->
                            <div class="pagination"> <?php echo $output['page']; ?> </div></td>
                    <tr>
                </tfoot>
            <?php } ?>
        </table>
    </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.edit.js" charset="utf-8"></script> 
<script type="text/javascript">
                            //表单提交
                            function submit_form(type) {
                                var id = '';
                                $('input[type=checkbox]:checked').each(function () {
                                    if (!isNaN($(this).val())) {
                                        id += $(this).val();//拼接字符串
                                    }
                                });
                                //如果为空：单按钮
                                if (id == '') {
                                    alert('<?php echo $lang['nc_ensure_del']; ?>');
                                    return false;
                                }
                                //删除:双按钮
                                if (type == 'del') {
                                    if (!confirm('<?php echo $lang['nc_ensure_del']; ?>')) {
                                        return false;
                                    }
                                }
                                $('#form_spec').submit();
                            }
                            //确认显示操作
                            function is_show(_obj) {
                                var _this = $(_obj);
                                var _goods_id = parseInt(_this.attr('nctype'));
                                $.ajax({
                                    url: 'index.php?act=goods_orm&op=ajax_show&goods_id=' + _goods_id,
                                    type: 'get',
                                    data: '',
                                    dataType: 'json',
                                    success: function (_data) {
                                        if (_data['state'] == 1) {
                                            _this.parents('tr').remove();
                                        } else {
                                            alert(_data['msg']);
                                        }
                                    }
                                })
                            }
                            //一键查询商品详情操作
                            function ajax_is_show() {
                                $.ajax({
                                    url: 'index.php?act=goods_orm&op=ajax_is_show',
                                    type: 'get',
                                    data: '',
                                    dataType: 'json',
                                    success: function (_data) {
                                        if (_data['state'] == 1) {
                                            _this.parents('tr').remove();
                                        } else {
                                            alert(_data['msg']);
                                        }
                                    }
                                })
                            }
                            function del_goods(_obj) {
                                var _this = $(_obj);
                                var _goods_id = parseInt(_this.attr('nctype'));
                                $.ajax({
                                    url: 'index.php?act=goods_orm&op=del_goods&goods_id=' + _goods_id,
                                    type: 'get',
                                    data: '',
                                    dataType: 'json',
                                    success: function (_data) {
                                        if (_data['state'] == 1) {
                                            _this.parents('tr').remove();
                                        } else {
                                            alert(_data['msg']);
                                        }
                                    }
                                })
                            }
</script>