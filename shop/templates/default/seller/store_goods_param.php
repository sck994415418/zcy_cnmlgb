<?php defined('InShopNC') or exit('Access Invalid!'); ?>
<!--规划表结构-->
<style>
    .Ptable-item {
        padding: 12px 10px;
        line-height: 220%;
        color: #999;
        font-size: 12px;
        overflow: hidden;
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
    <form action="index.php?act=store_goods_add&op=spec_save&goods_id=<?php echo $_GET['goods_id']; ?>&edit_goods_sign=<?php echo $output['edit_goods_sign'];?>" method="post">	
        <?php foreach ($output['title'] as $title) { ?>
            <div class="Ptable-item">
                <h3><?php echo $title['title_name']; ?></h3>
                <dl>
                    <?php
                    $param_list = Model()->query("select * from zmkj_param where title_id = " . $title['title_id']." order by pr_sort asc,pr_id asc");
                    foreach ($param_list as $key => $param) {
                        ?>
                        <dt><?php echo trim($param['pr_name']); ?>:</dt>
                        <dd><input type="text" name="<?php echo "at_value[" . $title['title_name'] . "]" . "['" . $param['pr_name'] . "']"; ?>" value="<?php
                            if (!empty($output['goods_param'][$title['title_name']]) && is_array($output['goods_param'][$title['title_name']])) {
                                foreach ($output['goods_param'][$title['title_name']] as $key1 => $value) {
                                    if (trim(str_replace("'", "", $key1)) == trim($param['pr_name'])) {
                                        echo $value;
                                    }
                                }
                            }
                            ?>" placeholder="参数值" /></dd>
                        <?php } ?>
                </dl>
            </div>
        <?php } ?>
        <input style="margin-left:272px!important" type="submit" value="确认提交" />
    </form>
</div>
