<?php defined('InShopNC') or exit('Access Invalid!');?>
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/home_goods.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL?>/js/cloudzoom.js"></script>
<style type="text/css">
.ncs-goods-picture .levelB, .ncs-goods-picture .levelC { cursor: url(<?php echo SHOP_TEMPLATES_URL;
?>/images/shop/zoom.cur), pointer;
}
.ncs-goods-picture .levelD { cursor: url(<?php echo SHOP_TEMPLATES_URL;
?>/images/shop/hand.cur), move\9;
}
.nch-sidebar-all-viewed { display: block; height: 20px; text-align: center; padding: 9px 0; }
</style>

<div class="wrapper pr">
  <input type="hidden" id="lockcompare" value="unlock" />
  <div class="ncs-detail<?php if ($output['store_info']['is_own_shop']) echo ' ownshop'; ?>"> 
    <!-- S 商品图片 --> 

  <div id="ncs-goods-picture" class="ncs-goods-picture">
    <div class="gallery_wrap">
        <div class="gallery"><img title="鼠标滚轮向上或向下滚动，能放大或缩小图片哦~" src="<?php echo $output["goods_image"]; ?>" class="cloudzoom" data-cloudzoom="zoomImage: '<?php echo $output["goods_image"]["0"]["2"] ?>'"> </div>
      </div>
    </div>
    
    <!-- S 商品基本信息 -->
    <div class="ncs-goods-summary">
      <div class="name">
        <h1><?php echo $output['goods']['goods_name']; ?></h1>
        <strong><?php echo str_replace("\n", "<br>", $output['goods']['goods_jingle']);?></strong> </div>
      <div class="ncs-meta"> 
        
        <!-- S 商品参考价格 -->
        <dl>
          <dt><?php echo $lang['goods_index_goods_cost_price'];?><?php echo $lang['nc_colon'];?></dt>
          <dd class="cost-price"><strong><?php echo $lang['currency'].ncPriceFormat($output['goods']['goods_marketprice']);?></strong></dd>
        </dl>
        <!-- E 商品参考价格 --> 
        <!-- S 商品发布价格 -->
        <dl>
          <dt><?php echo $lang['goods_index_goods_price'];?><?php echo $lang['nc_colon'];?></dt>
          <dd class="price">
            <?php if (isset($output['goods']['promotion_price']) && !empty($output['goods']['promotion_price'])) {?>
            <strong><?php echo $lang['currency'];?><?php echo ncPriceFormat($output['goods']['promotion_price']);?></strong><em>(原售价<?php echo $lang['nc_colon'];?><?php echo $lang['currency'].ncPriceFormat($output['goods']['goods_price']);?>)</em>
            <?php } else {?>
            <strong><?php echo $lang['currency'].ncPriceFormat($output['goods']['goods_price']);?></strong>
            <?php }?>
          </dd>
        </dl>
        <!-- E 商品发布价格 -->
      </div>
      <!--送达时间 begin  -->
        <?php if ($output['store_info']['store_free_time'] >= '1') {?>
        <dl>
          <dt>服　　务：</dt>
          <dd>由 <a href="<?php echo urlShop('show_store', 'index', array('store_id' => $output['store_info']['store_id']), $output['store_info']['store_domain']);?>" title="进入店铺" ><font color="#D93600"><b><?php echo $output['store_info']['store_name']; ?></b></font></a> 发货并提供售后服务。<span id="store-free-time">18:00 前完成下单，预计(<font color="#D93600"><b>
            <?php $showtime=date("Y-m-d H:i:s"); echo date('m月d日',strtotime($showtime . '+'. $output['store_info']['store_free_time'].' day')); ?>
            </b></font>) 送达</span></dd>
        </dl>
        <?php }?>
        <!--送达时间 end  --> 
      <form action="index.php?act=aa&op=editgoods" method="post" accept-charset="utf-8">
        <input type="hidden" name="id" value="<?php echo $output['goods']['goods_id']; ?>">
        <div class="ncs-buy">
          <div class="ncs-saleout">
            <dl>
              <dt><i class="icon-info-sign"></i>填写租赁信息</dt>
              <br>
              <dd>姓名:&nbsp;&nbsp;&nbsp;<input type="text" name="name" value="" placeholder="请填写姓名！" required></dd>
              <br>
              <dd>电话:&nbsp;&nbsp;&nbsp;<input type="tel" name="tel" value="" placeholder="请填写电话！" required></dd>
              <br>
              <dd>地址:&nbsp;&nbsp;&nbsp;<input type="text" name="address" value="" placeholder="请填写地址！" required></dd>
              <br>
              <dd>型号:&nbsp;&nbsp;&nbsp;<input type="text" name="model" value="" placeholder="请填写型号！" required></dd>
              <br>
              <dd>备注:&nbsp;&nbsp;&nbsp;<textarea name="other"></textarea></dd>
              <br>
              <dd><input type="submit" value="提&nbsp;&nbsp;&nbsp;交"></dd>
            </dl>
          </div> 
        </div>
      </form>
      <!--E 商品信息 --> 
    </div>
 
    <!--S 店铺信息-->
    <div style="position: absolute; z-index: 2; top: -1px; right: -1px;">
      <?php include template('store/info');?>
      <?php if ($output['store_info']['is_own_shop']) { ?>
      <!--S 看了又看 -->
      <div class="ncs-lal">
        <div class="title">看了又看</div>
        <div class="content">
          <ul>
            <?php foreach ((array) $output['goods_rand_list'] as $g) { ?>
            <li>
              <div class="goods-pic"><a title="<?php echo $g['goods_name']; ?>" href="<?php echo urlShop('goods', 'index', array('goods_id' => $g['goods_id'], )); ?>"> <img alt="" src="<?php echo UPLOAD_SITE_URL;?>/shop/common/loading.gif" rel="lazy" data-url="<?php echo cthumb($g['goods_image'], 60); ?>" /> </a></div>
              <div class="goods-price">￥<?php echo ncPriceFormat($g['goods_promotion_price']); ?></div>
            </li>
            <?php } ?>
          </ul>
        </div>
      </div>
      <!--E 看了又看 -- > 
      
    </div>
    <?php } ?>
    <!--E 店铺信息 --> 
  </div>
  <div class="clear"></div>
</div>
<!-- S 优惠套装 -->
<div class="ncs-promotion" id="nc-bundling" style="display:none;"></div>
<!-- E 优惠套装 -->
</div>
<form id="buynow_form" method="post" action="<?php echo SHOP_SITE_URL;?>/index.php">
  <input id="act" name="act" type="hidden" value="buy" />
  <input id="op" name="op" type="hidden" value="buy_step1" />
  <input id="cart_id" name="cart_id[]" type="hidden"/>
</form>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.charCount.js"></script> 
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ajaxContent.pack.js" type="text/javascript"></script> 
<script src="<?php echo RESOURCE_SITE_URL;?>/js/sns.js" type="text/javascript" charset="utf-8"></script> 
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.F_slider.js" type="text/javascript" charset="utf-8"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/waypoints.js"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.raty/jquery.raty.min.js"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.nyroModal/custom.min.js" charset="utf-8"></script>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/jquery.nyroModal/styles/nyroModal.css" rel="stylesheet" type="text/css" id="cssfile2" />
<script type="text/javascript">
/** 辅助浏览 **/
jQuery(function($){
	// 放大镜效果 产品图片
     CloudZoom.quickStart();
     
     		    // 图片切换效果
                $(".controller li").first().addClass('current');
                $('.controller').find('li').mouseover(function(){
                $(this).first().addClass("current").siblings().removeClass("current");
                });
});

    //收藏分享处下拉操作
    jQuery.divselect = function(divselectid,inputselectid) {
      var inputselect = $(inputselectid);
      $(divselectid).mouseover(function(){
          var ul = $(divselectid+" ul");
          ul.slideDown("fast");
          if(ul.css("display")=="none"){
              ul.slideDown("fast");
          }
      });
      $(divselectid).live('mouseleave',function(){
          $(divselectid+" ul").hide();
      });
    };
$(function(){
	//赠品处滚条
	$('#ncsGoodsGift').perfectScrollbar({suppressScrollX:true});
    <?php if ($output['goods']['goods_state'] == 1 && $output['goods']['goods_storage'] > 0 ) {?>
    // 加入购物车
    $('a[nctype="addcart_submit"]').click(function(){
    	if (typeof(allow_buy) != 'undefined' && allow_buy === false) return ;
        addcart(<?php echo $output['goods']['goods_id'];?>, checkQuantity(),'addcart_callback');
    });
        <?php if (!($output['goods']['is_virtual'] == 1 && $output['goods']['virtual_indate'] < TIMESTAMP)) {?>
        // 立即购买
        $('a[nctype="buynow_submit"]').click(function(){
        	if (typeof(allow_buy) != 'undefined' && allow_buy === false) return ;
            buynow(<?php echo $output['goods']['goods_id']?>,checkQuantity());
        });
        <?php }?>
    <?php }?>
    // 到货通知
    <?php if ($output['goods']['goods_storage'] == 0 || $output['goods']['goods_state'] == 0) {?>
    $('a[nctype="arrival_notice"]').click(function(){
        <?php if ($_SESSION['is_login'] !== '1'){?>
        login_dialog();
        <?php }else{?>
        ajax_form('arrival_notice', '到货通知','<?php echo urlShop('goods', 'arrival_notice', array('goods_id' => $output['goods']['goods_id']));?>', 350);
        <?php }?>
    });
    <?php }?>
    <?php if (($output['goods']['goods_state'] == 0 || $output['goods']['goods_storage'] <= 0) && $output['goods']['is_appoint'] == 1) {?>
    $('a[nctype="appoint_submit"]').click(function(){
        <?php if ($_SESSION['is_login'] !== '1'){?>
        login_dialog();
        <?php }else{?>
        ajax_form('arrival_notice', '立即预约', '<?php echo urlShop('goods', 'arrival_notice', array('goods_id' => $output['goods']['goods_id'], 'type' => 2));?>', 350);
        <?php }?>
    });
    <?php }?>
    //浮动导航  waypoints.js
    $('#main-nav').waypoint(function(event, direction) {
        $(this).parent().parent().parent().toggleClass('sticky', direction === "down");
        event.stopPropagation();
    });

    // 分享收藏下拉操作
    $.divselect("#handle-l");
    $.divselect("#handle-r");

    // 规格选择
    $('dl[nctype="nc-spec"]').find('a').each(function(){
        $(this).click(function(){
            if ($(this).hasClass('hovered')) {
                return false;
            }
            $(this).parents('ul:first').find('a').removeClass('hovered');
            $(this).addClass('hovered');
            checkSpec();
        });
    });

});

function checkSpec() {
    var spec_param = <?php echo $output['spec_list'];?>;
    var spec = new Array();
    $('ul[nctyle="ul_sign"]').find('.hovered').each(function(){
        var data_str = ''; eval('data_str =' + $(this).attr('data-param'));
        spec.push(data_str.valid);
    });
    spec1 = spec.sort(function(a,b){
        return a-b;
    });
    var spec_sign = spec1.join('|');
    $.each(spec_param, function(i, n){
        if (n.sign == spec_sign) {
            window.location.href = n.url;
        }
    });
}

// 验证购买数量
function checkQuantity(){
    var quantity = parseInt($("#quantity").val());
    if (quantity < 1) {
        alert("<?php echo $lang['goods_index_pleaseaddnum'];?>");
        $("#quantity").val('1');
        return false;
    }
    max = parseInt($('[nctype="goods_stock"]').text());
    <?php if ($output['goods']['is_virtual'] == 1 && $output['goods']['virtual_limit'] > 0) {?>
    max = <?php echo $output['goods']['virtual_limit'];?>;
    if(quantity > max){
        alert('最多限购'+max+'件');
        return false;
    }
    <?php } ?>
    <?php if (!empty($output['goods']['upper_limit'])) {?>
    max = <?php echo $output['goods']['upper_limit'];?>;
    if(quantity > max){
        alert('最多限购'+max+'件');
        return false;
    }
    <?php } ?>
    if(quantity > max){
        alert("<?php echo $lang['goods_index_add_too_much'];?>");
        return false;
    }
    return quantity;
}

// 立即购买js
function buynow(goods_id,quantity){
<?php if ($_SESSION['is_login'] !== '1'){?>
	login_dialog();
<?php }else{?>
    if (!quantity) {
        return;
    }
    <?php if ($_SESSION['store_id'] == $output['goods']['store_id']) { ?>
    alert('不能购买自己店铺的商品');return;
    <?php } ?>
    $("#cart_id").val(goods_id+'|'+quantity);
    $("#buynow_form").submit();
<?php }?>
}

$(function(){
    //选择地区查看运费
    $('#transport_pannel>a').click(function(){
    	var id = $(this).attr('nctype');
    	if (id=='undefined') return false;
    	var _self = this,tpl_id = '<?php echo $output['goods']['transport_id'];?>';
	    var url = 'index.php?act=goods&op=calc&rand='+Math.random();
	    $('#transport_price').css('display','none');
	    $('#loading_price').css('display','');
	    $.getJSON(url, {'id':id,'tid':tpl_id}, function(data){
	    	if (data == null) return false;
	        if(data != 'undefined') {$('#nc_kd').html('运费<?php echo $lang['nc_colon'];?><em>' + data + '</em><?php echo $lang['goods_index_yuan'];?>');}else{'<?php echo $lang['goods_index_trans_for_seller'];?>';}
	        $('#transport_price').css('display','');
	    	$('#loading_price').css('display','none');
	        $('#ncrecive').html($(_self).html());
	    });
    });
    $("#nc-bundling").load('index.php?act=goods&op=get_bundling&goods_id=<?php echo $output['goods']['goods_id'];?>', function(){
        if($(this).html() != '') {
            $(this).show();
        }
    });
    $("#salelog_demo").load('index.php?act=goods&op=salelog&goods_id=<?php echo $output['goods']['goods_id'];?>&store_id=<?php echo $output['goods']['store_id'];?>&vr=<?php echo $output['goods']['is_virtual'];?>', function(){
        // Membership card
        $(this).find('[nctype="mcard"]').membershipCard({type:'shop'});
    });
	$("#consulting_demo").load('index.php?act=goods&op=consulting&goods_id=<?php echo $output['goods']['goods_id'];?>&store_id=<?php echo $output['goods']['store_id'];?>', function(){
		// Membership card
		$(this).find('[nctype="mcard"]').membershipCard({type:'shop'});
	});

/** goods.php **/
	// 商品内容部分折叠收起侧边栏控制
	$('#fold').click(function(){
  		$('.ncs-goods-layout').toggleClass('expanded');
	});
	// 商品内容介绍Tab样式切换控制
	$('#categorymenu').find("li").click(function(){
		$('#categorymenu').find("li").removeClass('current');
		$(this).addClass('current');
	});
	// 商品详情默认情况下显示全部
	$('#tabGoodsIntro').click(function(){
		$('.bd').css('display','');
		$('.hd').css('display','');
	});
	// 点击地图隐藏其他以及其标题栏
	$('#tabStoreMap').click(function(){
		$('.bd').css('display','none');
		$('#ncStoreMap').css('display','');
		$('.hd').css('display','none');
	});
	// 点击评价隐藏其他以及其标题栏
	$('#tabGoodsRate').click(function(){
		$('.bd').css('display','none');
		$('#ncGoodsRate').css('display','');
		$('.hd').css('display','none');
	});
	// 点击成交隐藏其他以及其标题
	$('#tabGoodsTraded').click(function(){
		$('.bd').css('display','none');
		$('#ncGoodsTraded').css('display','');
		$('.hd').css('display','none');
	});
	// 点击咨询隐藏其他以及其标题
	$('#tabGuestbook').click(function(){
		$('.bd').css('display','none');
		$('#ncGuestbook').css('display','');
		$('.hd').css('display','none');
	});
	//商品排行Tab切换
	$(".ncs-top-tab > li > a").mouseover(function(e) {
		if (e.target == this) {
			var tabs = $(this).parent().parent().children("li");
			var panels = $(this).parent().parent().parent().children(".ncs-top-panel");
			var index = $.inArray(this, $(this).parent().parent().find("a"));
			if (panels.eq(index)[0]) {
				tabs.removeClass("current ").eq(index).addClass("current ");
				panels.addClass("hide").eq(index).removeClass("hide");
			}
		}
	});
	//信用评价动态评分打分人次Tab切换
	$(".ncs-rate-tab > li > a").mouseover(function(e) {
		if (e.target == this) {
			var tabs = $(this).parent().parent().children("li");
			var panels = $(this).parent().parent().parent().children(".ncs-rate-panel");
			var index = $.inArray(this, $(this).parent().parent().find("a"));
			if (panels.eq(index)[0]) {
				tabs.removeClass("current ").eq(index).addClass("current ");
				panels.addClass("hide").eq(index).removeClass("hide");
			}
		}
	});

//触及显示缩略图
	$('.goods-pic > .thumb').hover(
		function(){
			$(this).next().css('display','block');
		},
		function(){
			$(this).next().css('display','none');
		}
	);

	/* 商品购买数量增减js */
	// 增加
	$('a[nctype="increase"]').click(function(){
		num = parseInt($('#quantity').val());
	    <?php if ($output['goods']['is_virtual'] == 1 && $output['goods']['virtual_limit'] > 0) {?>
	    max = <?php echo $output['goods']['virtual_limit'];?>;
	    if(num >= max){
	        alert('最多限购'+max+'件');
	        return false;
	    }
	    <?php } ?>
	    <?php if (!empty($output['goods']['upper_limit'])) {?>
	    max = <?php echo $output['goods']['upper_limit'];?>;
	    if(num >= max){
	        alert('最多限购'+max+'件');
	        return false;
	    }
	    <?php } ?>
		max = parseInt($('[nctype="goods_stock"]').text());
		if(num < max){
			$('#quantity').val(num+1);
		}
	});
	//减少
	$('a[nctype="decrease"]').click(function(){
		num = parseInt($('#quantity').val());
		if(num > 1){
			$('#quantity').val(num-1);
		}
	});

    //评价列表
    $('#comment_tab').on('click', 'li', function() {
        $('#comment_tab li').removeClass('current');
        $(this).addClass('current');
        load_goodseval($(this).attr('data-type'));
    });
    load_goodseval('all');
    function load_goodseval(type) {
        var url = '<?php echo urlShop('goods', 'comments', array('goods_id' => $output['goods']['goods_id']));?>';
        url += '&type=' + type;
        $("#goodseval").load(url, function(){
            $(this).find('[nctype="mcard"]').membershipCard({type:'shop'});
        });
    }

    //记录浏览历史
	$.get("index.php?act=goods&op=addbrowse",{gid:<?php echo $output['goods']['goods_id'];?>});
	//初始化对比按钮
	initCompare();

    <?php if ($output['goods']['jjg_explain']) { ?>
        $('.couRuleScrollbar').perfectScrollbar({suppressScrollX:true});
    <?php }?>

    // 满即送、加价购显示隐藏
    $('[nctype="show-rule"]').click(function(){
        $(this).parent().find('[nctype="rule-content"]').show();
    });
    $('[nctype="hide-rule"]').click(function(){
        $(this).parents('[nctype="rule-content"]:first').hide()
    });

    $('.ncs-buy').bind({
        mouseover:function(){$(".ncs-point").show();},
        mouseout:function(){$(".ncs-point").hide();}
    });
    
});

/* 加入购物车后的效果函数 */
function addcart_callback(data){
	$('#bold_num').html(data.num);
    $('#bold_mly').html(price_format(data.amount));
    $('.ncs-cart-popup').fadeIn('fast');
}

<?php if($output['goods']['goods_state'] == 1 && $output['goods']['goods_verify'] == 1 && $output['goods']['is_virtual'] == 0){ ?>
var $cur_area_list,$cur_tab,next_tab_id = 0,cur_select_area = [],calc_area_id = '',calced_area = [],cur_select_area_ids = [];
$(document).ready(function(){
	$("#ncs-freight-selector").hover(function() {
		//如果店铺没有设置默认显示区域，马上异步请求
		<?php if (!$output['store_info']['deliver_region']) { ?>
		if (typeof nc_a === "undefined") {
	 		$.getJSON(SITEURL + "/index.php?act=index&op=json_area&callback=?", function(data) {
	 			nc_a = data;
	 			$cur_tab = $('#ncs-stock').find('li[data-index="0"]');
	 			_loadArea(0);
	 		});
		}
		<?php } ?>
		$(this).addClass("hover");
		$(this).on('mouseleave',function(){
			$(this).removeClass("hover");
		});
	});

	$('ul[class="area-list"]').on('click','a',function(){
		$('#ncs-freight-selector').unbind('mouseleave');
		var tab_id = parseInt($(this).parents('div[data-widget="tab-content"]:first').attr('data-area'));
		if (tab_id == 0) {cur_select_area = [];cur_select_area_ids = []};
		if (tab_id == 1 && cur_select_area.length > 1) {
			cur_select_area.pop();
			cur_select_area_ids.pop();
			if (cur_select_area.length > 1) {
				cur_select_area.pop();
				cur_select_area_ids.pop();
			}
		}
		next_tab_id = tab_id + 1;
		var area_id = $(this).attr('data-value');
		$cur_tab = $('#ncs-stock').find('li[data-index="'+tab_id+'"]');
		$cur_tab.find('em').html($(this).html());
		$cur_tab.find('i').html(' ∨');
		if (tab_id < 2) {
			calc_area_id = area_id;
			cur_select_area.push($(this).html());
			cur_select_area_ids.push(area_id);
			$cur_tab.find('a').removeClass('hover');
			$cur_tab.nextAll().remove();
			if (typeof nc_a === "undefined") {
    	 		$.getJSON(SITEURL + "/index.php?act=index&op=json_area&callback=?", function(data) {
    	 			nc_a = data;
    	 			_loadArea(area_id);
    	 		});
			} else {
				_loadArea(area_id);
			}
		} else {
			//点击第三级，不需要显示子分类
			if (cur_select_area.length == 3) {
				cur_select_area.pop();
				cur_select_area_ids.pop();
			}
			cur_select_area.push($(this).html());
			cur_select_area_ids.push(area_id);
			$('#ncs-freight-selector > div[class="text"] > div').html(cur_select_area.join(''));
			$('#ncs-freight-selector').removeClass("hover");
			_calc();
		}
		$('#ncs-stock').find('li[data-widget="tab-item"]').on('click','a',function(){
			var tab_id = parseInt($(this).parent().attr('data-index'));
			if (tab_id < 2) {
				$(this).parent().nextAll().remove();
				$(this).addClass('hover');
				$('#ncs-stock').find('div[data-widget="tab-content"]').each(function(){
					if ($(this).attr("data-area") == tab_id) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			}
		});
	});
	function _loadArea(area_id){
		if (nc_a[area_id] && nc_a[area_id].length > 0) {
			$('#ncs-stock').find('div[data-widget="tab-content"]').each(function(){
				if ($(this).attr("data-area") == next_tab_id) {
					$(this).show();
					$cur_area_list = $(this).find('ul');
					$cur_area_list.html('');
				} else {
					$(this).hide();
				}
			});
			var areas = [];
			areas = nc_a[area_id];
			for (i = 0; i < areas.length; i++) {
				if (areas[i][1].length > 8) {
					$cur_area_list.append("<li class='longer-area'><a data-value='" + areas[i][0] + "' href='#none'>" + areas[i][1] + "</a></li>");
				} else {
				    $cur_area_list.append("<li><a data-value='" + areas[i][0] + "' href='#none'>" + areas[i][1] + "</a></li>");
				}
			}
			if (area_id > 0){
				$cur_tab.after('<li data-index="' + (next_tab_id) + '" data-widget="tab-item"><a class="hover" href="#none" ><em>请选择</em><i> ∨</i></a></li>');
			}
		} else {
			//点击第一二级时，已经到了最后一级
			$cur_tab.find('a').addClass('hover');
			$('#ncs-freight-selector > div[class="text"] > div').html(cur_select_area);
			$('#ncs-freight-selector').removeClass("hover");
			_calc();
		}
	}
	//计算运费，是否配送
	function _calc() {
		$.cookie('dregion', cur_select_area_ids.join(' ')+'|'+cur_select_area.join(' '), { expires: 30 });
		<?php if (! $output['goods']['transport_id']) { ?>
		return;
		<?php } ?>
		var _args = '';
		_args += "&tid=<?php echo $output['goods']['transport_id']?>";
		<?php if ($output['store_info']['is_own_shop']) { ?>
		_args += "&super=1";
				<?php } ?>
		if (_args != '') {
			_args += '&area_id=' + calc_area_id ;
			if (typeof calced_area[calc_area_id] == 'undefined') {
				//需要请求配送区域设置
				$.getJSON(SITEURL + "/index.php?act=goods&op=calc&" + _args + "&myf=<?php echo $output['store_info']['store_free_price']?>&callback=?", function(data){
					allow_buy = data.total ? true : false;
					calced_area[calc_area_id] = data.total;
					if (data.total === false) {
						$('#ncs-freight-prompt > strong').html('无货').next().remove();
						$('a[nctype="buynow_submit"]').addClass('no-buynow');
						$('a[nctype="addcart_submit"]').addClass('no-buynow');
						$('#store-free-time').hide();
					} else {
						$('#ncs-freight-prompt > strong').html('有货 ').next().remove();
						$('#ncs-freight-prompt > strong').after('<span>' + data.total + '</span>');
						$('a[nctype="buynow_submit"]').removeClass('no-buynow');
						$('a[nctype="addcart_submit"]').removeClass('no-buynow');
						$('#store-free-time').show();
					}
				});	
			} else {
				if (calced_area[calc_area_id] === false) {
					$('#ncs-freight-prompt > strong').html('无货').next().remove();
					$('a[nctype="buynow_submit"]').addClass('no-buynow');
					$('a[nctype="addcart_submit"]').addClass('no-buynow');
					$('#store-free-time').hide();
				} else {
					$('#ncs-freight-prompt > strong').html('有货 ').next().remove();
					$('#ncs-freight-prompt > strong').after('<span>' + calced_area[calc_area_id] + '</span>');
					$('a[nctype="buynow_submit"]').removeClass('no-buynow');
					$('a[nctype="addcart_submit"]').removeClass('no-buynow');
					$('#store-free-time').show();
				}
			}
		}
	}
	//如果店铺设置默认显示配送区域
	<?php if ($output['store_info']['deliver_region']) { ?>
	if (typeof nc_a === "undefined") {
 		$.getJSON(SITEURL + "/index.php?act=index&op=json_area&callback=?", function(data) {
 			nc_a = data;
 			$cur_tab = $('#ncs-stock').find('li[data-index="0"]');
 			_loadArea(0);
 			$('ul[class="area-list"]').find('a[data-value="<?php echo $output['store_info']['deliver_region_ids'][0]?>"]').click();
 		    <?php if ($output['store_info']['deliver_region_ids'][1]) { ?>
 			$('ul[class="area-list"]').find('a[data-value="<?php echo $output['store_info']['deliver_region_ids'][1]?>"]').click();
 		    <?php } ?>
  		    <?php if ($output['store_info']['deliver_region_ids'][2]) { ?>
 			$('ul[class="area-list"]').find('a[data-value="<?php echo $output['store_info']['deliver_region_ids'][2]?>"]').click();
 			<?php } ?>
 		});
	}
	<?php } ?>
});
<?php }?>
</script> 