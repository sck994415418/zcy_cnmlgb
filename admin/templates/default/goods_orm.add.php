<?php defined('InShopNC') or exit('Access Invalid!'); ?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>新增-商品绑定</h3>
            <ul class="tab-base">
                <li><a href="index.php?act=goods_orm&op=index"><span>列表</span></a></li>
                <li><a class="current" href="JavaScript:void(0);"><span>新增</span></a></li>
            </ul>
        </div>
    </div>
    <div class="fixed-empty"></div>
    <form id="type_form" method="post">
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
                            <li>6.三方报价产品类目：台式计算机、便携式计算机、喷墨打印机、激光打印机、针式打印机、普通电视设备（电视机）、空调机、复印机、投影仪、多功能一体机、碎纸机、扫描仪、通用摄像机</li>
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
                <tr>
                    <td class="required" colspan="2">
                        <label class="validation" for="sku">商品sku:</label>
                        <input type="text" style="width:180px" class="txt" name="sku" value="" required />
                    </td>
                </tr> 
                <tr>
                    <td class="required" colspan="2">
                        <label class="validation" for="sku">政府商品id：</label>
                        <input type="text" style="width:180px" class="txt" name="productId" value="" required />
                    </td>
                </tr>
                <tr>
                    <td class="required" colspan="2">
                        <label class="validation" for="sku">政府商品名字：</label>
                        <input type="text" style="width:180px" class="txt" name="productName" value="" required />
                    </td>
                </tr>
                <tr>
                    <td class="required" colspan="2">
                        <label class="validation" for="sku">政府商品平均价格：</label>
                        <input type="text" style="width:180px" class="txt" name="average_price" value="" />
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="table tb-type2 mtw">
            <tfoot>
                <tr class="tfoot">
                    <td colspan="15"><a id="submitBtn" class="btn" href="JavaScript:void(0);"> <span>提交</span> </a></td>
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

        //按钮先执行验证再提交表单
        $("#submitBtn").click(function () {
            $("#type_form").submit();

        });
    });
</script>
