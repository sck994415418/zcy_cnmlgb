<?php defined('InShopNC') or exit('Access Invalid!'); ?>

<div class="ncm-flow-layout">
    <div class="ncm-flow-container">
        <div class="title"><a href="javascript:history.go(-1);" class="ncm-btn-mini fr"><i class="icon-reply"></i>返&nbsp;回</a>
            <h3><?php echo $lang['member_second_evaluation_toevaluategoods']; ?></h3>
        </div>
        <form id="evalform" method="post" action="index.php?act=member_evaluate&op=<?php echo $_GET['op']; ?>&order_id=<?php echo $_GET['order_id']; ?>">
            <div class="alert alert-block">
                <h4>操作提示：</h4>
                <ul>
                    <li><?php echo $lang['member_evaluation_rule_3']; ?></li>
                    <li><?php echo $output['ruleexplain']; ?></li>
                    <li><?php echo $lang['member_evaluation_rule_4']; ?></li>
                </ul>
            </div>
            <div class="tabmenu">
                <ul class="tab">
                    <li class="active"><a href="javascript:void(0);">追加评价</a></li>
                </ul>
            </div>
            <table class="ncm-default-table deliver mb30">
                <thead>
                    <tr>
                        <th colspan="2"><?php echo $lang['member_evaluation_order_desc']; ?></th>
                        <th>评价详情</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($output['order_goods'])) { ?>
                        <?php foreach ($output['order_goods'] as $goods) { ?>
                            <tr class="bd-line">
                                <td valign="top" class="w150">
                                    <div class="pic-thumb" style="float:right;margin-right:18px;margin-top:10px">
                                        <a href="index.php?act=goods&goods_id=<?php echo $goods['goods_id']; ?>" target="_blank">
                                            <img src="<?php echo $goods['goods_image_url']; ?>"/>
                                        </a>
                                    </div>
                                </td>
                                <td valign="top" class="tl w200">
                                    <dl class="goods-name">
                                        <dt style="width: 190px;">
                                            <a href="index.php?act=goods&goods_id=<?php echo $goods['goods_id']; ?>" target="_blank"><?php echo $goods['goods_name']; ?></a></dt>
                                        <dd>
                                            <span class="rmb-price"><?php echo $goods['goods_price']; ?></span>&nbsp;*&nbsp;<?php echo $goods['goods_num']; ?>&nbsp;件
                                        </dd>
                                    </dl>
                                </td>
                                <td valign="top" class="tr">
                                    <input type="hidden" name="goods_id" value="<?php echo $goods['goods_id'];?>" />
                                    <textarea name="comment" cols="150" style="width: 280px;">
                                    </textarea>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
            <div class="ncm-default-form">
                <div class="bottom">
                    <label class="submit-border">
                        <input id="btn_submit" type="button" class="submit" value="<?php echo $lang['member_evaluation_submit']; ?>"/>
                    </label>
                </div>
            </div>
        </form>
    </div>
    <div class="ncm-flow-item">
        <?php if (!$output['store_info']['is_own_shop']) { ?>
            <?php require('evaluation.store_info.php'); ?>
        <?php } ?>
    </div>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.raty/jquery.raty.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.raty').raty({
            path: "<?php echo RESOURCE_SITE_URL; ?>/js/jquery.raty/img",
            click: function (score) {
                $(this).find('[nctype="score"]').val(score);
            }
        });

        $('.raty-x2').raty({
            path: "<?php echo RESOURCE_SITE_URL; ?>/js/jquery.raty/img",
            starOff: 'star-off-x2.png',
            starOn: 'star-on-x2.png',
            width: 150,
            click: function (score) {
                $(this).find('[nctype="score"]').val(score);
            }
        });
        //事件绑定
        $('#btn_submit').on('click', function () {
            ajaxpost('evalform', '', '', 'onerror');//ajaxpost参数：表单id
        });
    });
</script>
