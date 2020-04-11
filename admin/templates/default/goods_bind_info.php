<?php defined('InShopNC') or exit('Access Invalid!'); ?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>查看-商品信息</h3>
            <ul class="tab-base">
                <li><a href="index.php?act=goods_orm&op=index&is_bind_type=<?php echo $output['is_bind_type'];?>&curpage=<?php echo $output['bind_page'];?>#<?php echo $output['goods_id'];?>"><span>返回</span></a></li>
            </ul>
        </div>
    </div>
    <div class="fixed-empty"></div>
    <form method="post" action='index.php?act=goods_orm&op=show_data'>
        <table id="prompt" class="table tb-type2">
            <tbody>
                <tr class="space odd">
                    <th colspan="12" class="nobg"> <div class="title">
                            <h5>44<?php echo $lang['nc_prompts']; ?></h5>
                            <span class="arrow"></span> </div>
                    </th>
                </tr>
                <tr class="odd">
                    <td>
                        <ul>
                            <li>1.省政府后台：http://211.90.38.212:8080/TPBidder/WSSCZtbMis_HeBei/Account/login.aspx</li>
                            <li>2.进入商品维护--云端商品报价</li>
                            <li>3.选择相应的类目，查看右侧相应的展示商品</li>
                            <li>4.点击详细，记录productId，productName，productUrl</li>
                            <li>5.本商城的sku，即为商品的goods_id</li>
                            <li>5.报价的数目，最好从后台去查找</li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
        <input type="hidden" value="ok" name="form_submit">
        <style>
            td{
                line-height:60px;
            }
            input{
                margin-left: 20px;
            }
        </style>
        <table class="table tb-type2">
            <tbody>
            <input type="hidden" style="width:180px" class="txt" name="id" value="<?php echo $output['goods_id'] ?>" required />
            <input type="hidden" style="width:180px" class="txt" name="is_bind_type" value="<?php echo $output['is_bind_type'] ?>" required />
            <input type="hidden" style="width:180px" class="txt" name="bind_page" value="<?php echo $output['bind_page'] ?>" required />
            <tr>
                <td class="required" colspan="2">
                    <label class="validation" for="sku">政府商品id：</label>
                    <input type="text" style="width:180px" class="txt" name="product_id" value="<?php echo $output['goods_bind_info'][0]['product_id'] ?>" required />
                </td>
            </tr>
            <tr>
                <td class="required" colspan="2">
                    <label class="validation" for="sku">企业报价数：</label>
                    <input type="text" style="width:180px" class="txt" name="store_num" value="<?php echo $output['goods_bind_info'][0]['store_num'] ?>" />
                </td>
            </tr>
            </tbody>
        </table>

        <table class="table tb-type2 mtw">
            <tfoot>
                <tr class="tfoot">
                    <td colspan="15"><input type='submit'value='提交' /></td>
                </tr>
            </tfoot>
        </table>
    </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/common_select.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.mousewheel.js"></script>
<script>
    $(function () {
        //表单验证
        $('#type_form').validate({
            errorPlacement: function (error, element) {
                error.appendTo(element.parent().parent().prev().find('td:first'));
            },

            rules: {
                t_mane: {
                    required: true,
                    maxlength: 20,
                    minlength: 1
                },
                t_sort: {
                    required: true,
                    digits: true
                }
            },
            messages: {
                t_mane: {
                    required: '<?php echo $lang['type_add_name_no_null']; ?>',
                    maxlength: '<?php echo $lang['type_add_name_max']; ?>',
                    minlength: '<?php echo $lang['type_add_name_max']; ?>'
                },
                t_sort: {
                    required: '<?php echo $lang['type_add_sort_no_null']; ?>',
                    digits: '<?php echo $lang['type_add_sort_no_digits']; ?>'
                }
            }
        });

    });
</script>
