<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['nc_member_pointsmanage']?></h3>
      <ul class="tab-base">
      	<li><a href="index.php?act=points&op=addpoints"><span><?php echo $lang['nc_manage']?></span></a></li>
        <li><a href="index.php?act=points&op=pointslog"><span><?php echo $lang['admin_points_log_title']?></span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo 积分规则?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <table class="table tb-type2" id="prompt">
    <tbody>
      <tr class="space odd">
        <th colspan="12"><div class="title"><h5><?php echo $lang['nc_prompts'];?></h5><span class="arrow"></span></div></th>
      </tr>
      <tr>
        <td>
        <ul>
        	<li class="tips">提交积分规则设置后,需在设置->清理缓存,后才可生效</li>
          </ul></td>
      </tr>
    </tbody>
  </table>
  <form method="post" name="integration" id="integration" action="index.php?act=points&op=edit">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2">
      <tbody>
        <tr>
          <td class="" colspan="2"><table class="table tb-type2 nomargin">
              <thead>
                <tr class="space">
                  <th colspan="16">积分值获取规则如下:</th>
                </tr>
                <tr class="thead">
                  <th>项目</th>
                  <th>获得积分值</th>
                </tr>
              </thead>
              <tbody>
              	<tr class="hover">
                  <td class="w200">注册会员</td>
                  <td><input id="exp_login" name="points_reg" value="<?php echo $output['value'][0][0]['value'];?>" class="txt" type="text" style="width:60px;">注册会员奖励的积分</td>
                </tr>
                <tr class="hover">
                  <td class="w200">会员登录</td>
                  <td><input id="exp_login" name="points_login" value="<?php echo $output['value'][1][0]['value'];?>" class="txt" type="text" style="width:60px;">每日登录加积分</td>
                </tr>
                <tr class="hover">
                  <td class="w200">购物消费</td>
                  <td><input id="exp_login" name="points_orderrate" value="<?php echo $output['value'][2][0]['value'];?>" class="txt" type="text" style="width:60px;">%完成一笔订单返给的积分比例%，最多不超过800分</td>
                </tr>
                <tr class="hover">
                  <td class="w200">评论商品</td>
                  <td><input id="exp_login" name="points_comments" value="<?php echo $output['value'][3][0]['value'];?>" class="txt" type="text" style="width:60px;">评论订单后加的积分</td>
                </tr>
                <tr class="hover">
                  <td class="w200">邀请新用户注册</td>
                  <td><input id="exp_login" name="points_invite" value="<?php echo $output['value'][4][0]['value'];?>" class="txt" type="text" style="width:60px;">成功邀请一位新用户注册为商城会员时奖励的积分值</td>
                </tr>
                <tr class="hover">
                  <td class="w200">被邀请用户购物成功</td>
                  <td><input id="exp_comments" name="points_rebate" value="<?php echo $output['value'][5][0]['value'];?>" class="txt" type="text" style="width:60px;">%被邀请的用户完成一笔订单时返给订单金额的比例</td>
                </tr>
              </tbody>
            </table>
            <table class="table tb-type2 nomargin">
      <tfoot>
        <tr class="tfoot">
          <td colspan="2" ><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span><?php echo $lang['nc_submit'];?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script>
$(function(){
	$("#submitBtn").click(function(){
		$("#integration").submit();
	});
});
</script> 
