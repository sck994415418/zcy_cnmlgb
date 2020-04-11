<?php defined('InShopNC') or exit('Access Invalid!'); ?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>修改-商品绑定</h3>
            <ul class="tab-base">
                <li><a href="index.php?act=zf_class&op=index"><span>管理</span></a></li>
                <li><a class="current" href="JavaScript:void(0);"><span>修改</span></a></li>
            </ul>
        </div>
    </div>
    <div class="fixed-empty"></div>
    <form id="type_form" method="post" action="index.php?act=zf_class&op=edit">
        <table id="prompt" class="table tb-type2">
            <tbody>
                <tr class="space odd">
                    <th colspan="12" class="nobg"> <div class="title">
                            <h5><?php echo $lang['nc_prompts']; ?></h5>
                            <span class="arrow"></span> </div>
                    </th>
                </tr>
                <tr class="odd">
                    <td>
                        <ul>
                            <li>1.本目录为：政府采购网分类</li>
                            <li>2.通过该分类 绑定 相应的的商品</li>
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
            <input type="hidden" name="id" value="<?php echo $output["class_info"][0]['id']; ?>" />
            <tr>
                <td class="required" colspan="2">
                    <label class="validation" for="class_id">政采分类ID:</label>
                    <input type="text" style="width:180px" class="txt" name="class_id" value="<?php echo $output["class_info"][0]['class_id']; ?>" required />
                </td>
            </tr> 
            <tr>
                <td class="required" colspan="2">
                    <label class="validation" for="class_name">政采分类Name：</label>
                    <input type="text" style="width:180px" class="txt" name="class_name" value="<?php echo $output["class_info"][0]['class_name']; ?>" required />
                </td>
            </tr>
            <tr>
                <td class="required" colspan="2">
                    <label class="validation" for="class_name">是否需要三方报价：</label>
                    <span>是：</span><input type="radio" value="1" name="class_type" <?php if ($output["class_info"][0]['class_type'] == 1) {
    echo "checked";
} ?> />
                    <span stlye="margin-left:20px">否：</span><input type="radio" value="0" name="class_type" <?php if ($output["class_info"][0]['class_type'] == 1) {
    echo "";
} ?> />
                </td>
            </tr>
            </tbody>
        </table>
        <table class="table tb-type2 mtw">
            <tfoot>
                <tr class="tfoot">
                    <td colspan="15"><a id="submitBtn" class="btn" href="JavaScript:void(0);"> <span><input type="submit" value="提交" /></span> </a></td>
                </tr>
            </tfoot>
        </table>
    </form>
</div>
