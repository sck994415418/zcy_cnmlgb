<?php defined('InShopNC') or exit('Access Invalid!'); ?>
<!--规划表结构-->
<style>
    .Ptable-item {
        padding: 12px 0;
        line-height: 220%;
        color: #999;
        font-size: 12px;
    }
    .Ptable-item h3 {
        width: 110px;
        text-align: right;
        font-weight: 400;
        font-size: 12px;
        float: left;
    }
    .Ptable-item dl {
        margin-left: 110px;
    }
    .Ptable-item dt {
        width: 160px;
        float: left;
        text-align: right;
        padding-right: 5px;
    }
    .Ptable-item dd {
        margin-left: 210px;
    }
</style>
<div class="Ptable">
    <?php if (!empty($output['goods_param']) && is_array($output['goods_param'])) { ?>
        <?php foreach ($output['goods_param'] as $title => $param_list) { ?>
            <div class="Ptable-item">
                <?php if (!empty($param_list) && is_array($param_list)) { ?>
                    <h3><?php echo $title; ?></h3>
                    <?php foreach ($param_list as $param_name => $param_value) { ?>
                        <dl>
                            <dt><?php echo trim(str_replace("'", "", $param_name)); ?></dt><dd><?php echo $param_value; ?></dd>
                        </dl>
                    <?php } ?>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } ?>
</div>
