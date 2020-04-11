<?php defined('InShopNC') or exit('Access Invalid!');?>
<?php require('groupbuy_head.php');?>
<style type="text/css">
	.ncg-screen{
		width: 988px;
	}
	.time-remain em{
		color: #C9033B;
		font-weight: 600;
		margin: 0 1px;
	}
	.time-remain{
		color: #000000;
		font-weight: bold;
	}
	.ncg-content{
		width: 1080px;
		float: right;
	}
	.ncg-list-content .pic-thumb img{
		width: 200px;
	}
	/*我要抢按钮一直显示*/
	.buy-button{
		opacity: inherit !important;
	}
</style>
<div class="nch-breadcrumb-layout" style="display: block;">
  <div class="nch-breadcrumb wrapper"> <i class="icon-home"></i> <span> <a href="<?php echo urlShop(); ?>">首页</a> </span> <span class="arrow">></span> <span>春季大促</span></div>
</div>

<div class="ncg-container">
  <div class="ncg-content">
	<div class="ncg-nav">
    </div>
    <div class="ncg-screen">
    	
    <?php if (!empty($output['xianshi_item']) && is_array($output['xianshi_item'])) { ?>
    <!-- 限时折扣 -->
    <div class="group-list">
      <ul>
        <?php foreach ($output['xianshi_item'] as $groupbuy) { ?>
        <li class="<?php echo $output['current']; ?>">
          <div class="ncg-list-content"> <a href="<?php echo $groupbuy['goods_url'];?>" class="pic-thumb" target="_blank"><img src="<?php echo UPLOAD_SITE_URL;?>/shop/store/goods/<?php echo $groupbuy['store_id'].'/'.$groupbuy['goods_image'];?>" rel="lazy"></a>
            <h3 class="title"><a href="<?php echo $groupbuy['goods_url'];?>" target="_blank"><?php echo $groupbuy['goods_name'];?></a></h3>
            <?php list($integer_part, $decimal_part) = explode('.', $groupbuy['groupbuy_price']);?>
            <div class="item-prices"> <span class="price"><i><?php echo $lang['currency'];?></i><?php echo $groupbuy['xianshi_price'];?></span>
              <div class="dock"><span class="limit-num"><?php echo $groupbuy['xianshi_discount'];?>&nbsp;<?php echo $lang['text_zhe'];?></span> <del class="orig-price"><?php echo $lang['currency'].$groupbuy['goods_price'];?></del></div>
              <!--<span class="time-remain" count_down="<?php echo $groupbuy['end_time']-TIMESTAMP;?>"><i></i><em time_id="d">0</em><?php echo "天";?><em time_id="h">0</em><?php echo "小时";?> <em time_id="m">0</em><?php echo "分钟";?><em time_id="s">0</em><?php echo "秒";?></span>-->
              <a href="<?php echo $groupbuy['goods_url'];?>" target="_blank" class="buy-button"><?php echo "我要抢";?></a>
              </div>
          </div>
        </li>
        <?php } ?>
      </ul>
    </div>
    <div class="tc mt20 mb20">
      <div class="pagination"><?php echo $output['show_page'];?></div>
    </div>
    <?php } else { ?>
    <div class="no-content"><?php echo "暂时没有折扣活动";?></div>
    <?php } ?>
  </div>
</div>
<script src="<?php echo SHOP_SITE_URL;?>/resource/js/jquery-2.2.0.js"></script>
<script type="text/javascript">
	window.onload = function() {
		takeCount();
	}
	function takeCount() {
	    setTimeout("takeCount()", 1000);
	    $(".time-remain").each(function(){
	        var obj = $(this);
	        var tms = obj.attr("count_down");
	        if (tms>0) {
	            tms = parseInt(tms)-1;
	            var days = Math.floor(tms / (1 * 60 * 60 * 24));
	            var hours = Math.floor(tms / (1 * 60 * 60)) % 24;
	            var minutes = Math.floor(tms / (1 * 60)) % 60;
	            var seconds = Math.floor(tms / 1) % 60;
	
	            if (days < 0) days = 0;
	            if (hours < 0) hours = 0;
	            if (minutes < 0) minutes = 0;
	            if (seconds < 0) seconds = 0;
	            obj.find("[time_id='d']").html(days);
	            obj.find("[time_id='h']").html(hours);
	            obj.find("[time_id='m']").html(minutes);
	            obj.find("[time_id='s']").html(seconds);
	            obj.attr("count_down",tms);
	        }
	    });
	}
	//倒计时标签,鼠标滑入隐藏,滑出显示
//	$(".group-list ul li").mouseover(function(){
//		$(this).find(".time-remain").hide();
//	})
//	$(".group-list ul li").mouseout(function(){
//		$(this).find(".time-remain").show();
//	})
</script>