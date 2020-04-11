<?php defined('InShopNC') or exit('Access Invalid!'); ?>
<!--政采分类-->
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>政采分类</h3>
            <ul class="tab-base">
                <li><a class="current"><span>管理</span></a></li>
                <li><a href="index.php?act=zf_class&op=add"><span>新增</span></a></li>
            </ul>
        </div>
    </div>
    <div class="fixed-empty">

    </div>
    <form method="get" style="margin-top:10px" target="workspace">    
        <input type ="hidden" name="act" value="zf_class" />
        <input type ="hidden" name="op" value="index" />
        品目名称：<input type="text" name="class_name" value="" />
        品目ID：<input type="text" name="class_id" value="" />
        商品名称：<input type="text" name="goods_name" value="" />
        <input type="submit" value="搜索" />
    </form>
    <table class="table tb-type2" id="prompt">
        <tbody>
            <tr class="space odd">
                <th class="nobg" colspan="12"><div class="title"><h5>操作提醒</h5><span class="arrow"></span></div></th>
            </tr>
            <tr>
                <td>
                    <ul>
                        <li>1.本目录为：政府采购网分类</li>
                        <li>2.通过该分类 绑定 相应的的商品</li>
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>
    <form method='get'>
        <input type="hidden" name="act" value="zf_class" />
        <input type="hidden" name="op" value="del" />
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="submit_type" id="submit_type" value="" />
        <table class="table tb-type2">
            <thead>
                <tr class="thead">
                    <th></th>
                    <th>政采分类name</th>
                    <th>政采分类id</th>
                    <th>商品数量</th>
                    <th></th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($output['class_list']) && is_array($output['class_list'])) { ?>
                    <?php foreach ($output['class_list'] as $k => $v) { ?>
                        <tr class="hover edit">
                            <td class="w48"><input type="checkbox" name="id[]" value="<?php echo $v['id']; ?>" class="checkitem"></td>
                            <td class="w50pre name">
                                <span><?php if ($v['class_type'] == 1) { ?>
                                        <font style="color:red;font-weight:bold"><?php echo $v['class_name']; ?></font>
                                    <?php } else { ?>
                                        <?php echo $v['class_name']; ?>
                                    <?php } ?></span>
                            </td>
                            <td><?php echo $v['class_id']; ?></td>
                            <td><?php echo $v['goods_num']; ?></td>
                            <td></td>
                            <td class="w96"><a href="index.php?act=zf_class&op=bind_goods&zf_class_id=<?php echo $v['id']; ?>">绑定</a> | <a href="index.php?act=zf_class&op=edit&id=<?php echo $v['id']; ?>">修改</a> | <a href="javascript:if(confirm('确认删除？'))window.location = 'index.php?act=zf_class&op=del&id=<?php echo $v['id']; ?>';">删除</a></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr class="no_data">
                        <td colspan="10"><?php echo $lang['nc_no_record']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <?php if (!empty($output['class_list']) && is_array($output['class_list'])) { ?>
                <tfoot>
                    <tr class="tfoot">
                        <td><input type="checkbox" class="checkall" id="checkall_2"></td>
                        <td id="batchAction" colspan="15">
                            <span class="all_checkbox">
                                <label for="checkall_2">全选</label>
                            </span>&nbsp;&nbsp;<a href="JavaScript:void(0);" class="btn" onclick="if (confirm('确认删除？')) {
                                            $('#submit_type').val('del');
                                            $('form:first').submit();
                                        }"><span>删除</span></a>  <div class="pagination"> <?php echo $output['page']; ?> </div></td>
                        </td>
                    </tr>
                </tfoot>
            <?php } ?>
        </table>
    </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.edit.js" charset="utf-8"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.goods_class.js" charset="utf-8"></script> 
