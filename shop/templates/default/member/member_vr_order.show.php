<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="ncm-oredr-show">
  <div class="ncm-order-info">
    <div class="ncm-order-details">
      <div class="title">租赁订单信息</div>
      <div class="content">
        <dl>
          <dt>接收手机：</dt>
          <dd><?php echo $output['order_info']['buyer_phone'];?>
            <?php if ($output['order_info']['order_state'] == ORDER_STATE_PAY) { ?>
            <a href="javascript:void(0);" class="ncm-btn-mini ncm-btn-orange" dialog_id="vr_code_resend" dialog_title="发送电子兑换码" dialog_width="480" nc_type="dialog" uri="<?php echo urlShop('member_vr_order', 'resend',array('buyer_phone'=>$output['order_info']['buyer_phone'],'order_id'=>$output['order_info']['order_id']));?>"><i class="icon-mobile-phone"></i>重新发送</a>
            <?php } ?>
          </dd>
        </dl>
        <dl class="line">
          <dt>下单时间：</dt>
          <dd><?php echo date("Y-m-d H:i:s",$output['order_info']['add_time']);?></dd>
        </dl>
        <dl class="line">
          <dt>买家留言：</dt>
          <dd><?php echo $output['order_info']['other'];?></dd>
        </dl>
        <dl class="line">
          <dt>型号：</dt>
          <dd><?php echo $output['order_info']['model'];?></dd>
        </dl>
        <dl class="line">
          <dt>商&#12288;&#12288;家：</dt>
          <dd><?php echo $output['order_info']['store_name'];?><a href="javascript:void(0);" id="mapmore">更多<i class="icon-angle-down"></i>
            <div class="more"><span class="arrow"></span>
              <ul>
                <li> 联系电话：<span><?php echo !empty($output['store_info']['live_store_tel']) ? $output['store_info']['live_store_tel'] : $output['store_info']['store_phone']; ?></span> </li>
                <li>地&#12288;&#12288;址： <span><?php echo !empty($output['store_info']['live_store_address']) ? $output['store_info']['live_store_address'] : $output['store_info']['store_address']; ?></span> </li>
                <li>
                  <div id="container"></div>
                </li>
                <li>交通信息：<?php echo $output['store_info']['live_store_bus'];?></li>
              </ul>
            </div>
            </a></dd>
        </dl>
      </div>
    </div>
    <?php if ($output['order_info']['order_state'] == ORDER_STATE_CANCEL){ ?>
    <div class="ncm-order-condition">
      <dl>
        <dt><i class="icon-off orange"></i>订单状态：</dt>
        <dd>交易关闭</dd>
      </dl>
    </div>
    <?php } elseif ($output['order_info']['order_state'] == ORDER_STATE_NEW){ ?>
    <div class="ncm-order-condition">
      <dl>
        <dt><i class="icon-ok-circle green"></i>订单状态：</dt>
        <dd>订单已生成，待付款</dd>
      </dl>
      <ul>
        <li>2. 如果您不想租赁此商品，请点击 <a href="#order-list" class="ncm-btn-mini" >取消订单</a>。 </li>
        <!-- <li>3. 系统将于
          <time><?php echo date('Y-m-d H:i:s',$output['order_info']['order_cancel_day']);?></time>
          自动关闭该订单，请您及时付款。
        </li> -->
      </ul>
    </div>
    <?php } elseif ($output['order_info']['order_state'] == ORDER_STATE_SUCCESS){ ?>
    <div class="ncm-order-condition">
      <dl>
        <dt><i class="icon-ok-circle green"></i>订单状态：</dt>
        <dd>订单已完成。</dd>
      </dl>
      <ul>
        <li>1. 使用期已经结束，感谢您的惠顾！</li>
        <li>2. 去商城看看有没有新的<a href="<?php echo urlShop('aa','index');?>" class="ncm-btn-mini ncm-btn-green" target="_blank">商品</a>。
        </li>
      </ul>
    </div>
    <?php }?>
    <div class="mall-msg">有疑问可咨询<a href="javascript:void(0);" onclick="ajax_form('mall_consult', '平台客服', '<?php echo urlShop('member_mallconsult', 'add_mallconsult', array('inajax' => 1));?>', 640);"><i class="icon-comments-alt"></i>平台客服</a></div>
  </div>
  <?php if ( $output['order_info']['order_state'] != ORDER_STATE_CANCEL){ ?>
  <div class="ncm-order-step">
    <dl class="step-first current">
      <dt>生成订单</dt>
      <dd class="bg"></dd>
      <dd class="date" title="订单生成时间"><?php echo date("Y-m-d H:i:s",$output['order_info']['add_time']); ?></dd>
    </dl>
   <!--  <dl class="<?php echo $output['order_info']['step_list']['step2'] ? 'current' : null ; ?>">
      <dt>完成付款</dt>
      <dd class="bg"> </dd>
      <dd class="date" title="付款时间"><?php echo @date('Y-m-d H:i:s',$output['order_info']['payment_time']); ?></dd>
    </dl> -->
    <dl class="long <?php echo $output['order_info']['step_list']['step4'] ? 'current' : null ; ?>">
      <dt>订单完成</dt>
      <dd class="bg"> </dd>
      <dd class="date" title="订单完成"><?php echo date("Y-m-d H:i:s",$output['order_info']['finnshed_time']); ?></dd>
    </dl>
    <?php if (!empty($output['order_info']['extend_vr_order_code'])){ ?>
    <div class="code-list tip" title="如列表过长超出显示区域时可滚动鼠标进行查看"><i class="arrow"></i>
      <h5>电子兑换码</h5>
      <div id="codeList">
        <ul>
          <?php foreach($output['order_info']['extend_vr_order_code'] as $code_info){ ?>
          <li class="<?php echo $code_info['vr_state'] == 1 ? 'used' : null;?>"><strong><?php echo $code_info['vr_code'];?></strong> <?php echo $code_info['vr_code_desc'];?> </li>
          <?php } ?>
        </ul>
      </div>
    </div>
    <?php } ?>
  </div>
  <?php }?>
  <div class="ncm-order-contnet" id="order-list">
    <table class="ncm-default-table order">
      <thead>
        <tr>
          <th class="w10"></th>
          <th colspan="2">商品</th>
          <th class="w100 tl">单价 (元)</th>
          <th class="w60">数量</th>
          <th class="w100">售后</th>
          <th class="w100">交易状态</th>
          <th class="w120">交易操作</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th colspan="20">
            <span class="ml10" title="租赁订单号">租赁订单号：<?php echo $output['order_info']['order_sn'];?></span>
            <span>下单时间：<?php echo date("Y-m-d H:i",$output['order_info']['add_time']);?></span>
            <span>
              <a href="<?php echo urlShop('show_store','index',array('store_id'=>$output['order_info']['store_id']));?>" title="<?php echo $output['order_info']['store_name'];?>">
                <?php echo $output['store_info']['store_name'];?>
              </a>
            </span>
          </th>
        </tr>
        <tr>
          <td class="bdl"></td>
          <td class="w50"><div class="pic-thumb"><a href="<?php echo urlShop('goods','index',array('goods_id' => $output['order_info']['goods_id']));?>" target="_blank" onMouseOver="toolTip('<img src=<?php echo thumb($output['order_info'], 240);?>>')" onMouseOut="toolTip()"/><img src="<?php echo thumb($output['order_info'], 60);?>"/></a></div></td>
          <td class="tl"><dl class="goods-name">
              <dt><a href="<?php echo urlShop('goods','index',array('goods_id'=>$output['order_info']['goods_id']));?>" target="_blank" title="<?php echo $output['order_info']['goods_name'];?>"><?php echo $output['order_info']['goods_name'];?></a></dt>
              <dd>
                <span class="sale-type">
                  <?php echo $output['order_info']['goods_name']; ?>
                </span>
              </dd>
            </dl></td>
          <td class="tl"><?php echo $output['order_info']['goods_price'];?></td>
          <td><?php echo $output['order_info']['goods_num'];?></td>
          <td> <?php if($output['order_info']['if_refund']){ ?>
              <a href="index.php?act=member_vr_refund&op=add_refund&order_id=<?php echo $output['order_info']['order_id']; ?>">退款</a>
              <?php } ?></td>
          <td class="bdl"><?php echo $output['order_info']['state_desc'];?></td>
          <td class="bdl bdr">

          <?php if ($output['order_info']['order_state'] != 0){ ?>
            <a class="ncm-btn ncm-btn-red" href="index.php?act=member_vr_order&op=change_state&state_type=order_cancel&order_id=<?php echo $order_info['rorder_id'];?>">
              <i class="icon-ban-circle"></i>取消
            </a>
          <?php }?>

          </td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="20"><dl class="sum">
              <dt>订单商品金额：</dt>
              <dd><em><?php echo $output['order_info']['goods_price'];?></em>元</dd>
            </dl></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js"></script>
<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/sns.js" ></script>
<script type="text/javascript">
var cityName = '';
var address = '<?php echo str_replace("'",'"',$output['store_info']['live_store_address']);?>';
var store_name = '<?php echo str_replace("'",'"',$output['store_info']['live_store_name']);?>';
var map = "";
var localCity = "";
var opts = {width : 150,height: 50,title : "商铺名称:"+store_name}
function initialize() {
	map = new BMap.Map("container");
	localCity = new BMap.LocalCity();

	map.enableScrollWheelZoom();
	map.addControl(new BMap.NavigationControl());
	map.addControl(new BMap.ScaleControl());
	map.addControl(new BMap.OverviewMapControl());
	localCity.get(function(cityResult){
	  if (cityResult) {
	  	var level = cityResult.level;
	  	if (level < 13) level = 13;
	    map.centerAndZoom(cityResult.center, level);
	    cityResultName = cityResult.name;
	    if (cityResultName.indexOf(cityName) >= 0) cityName = cityResult.name;
	    	    	getPoint();
	    	  }
	});
}

function loadScript() {
	var script = document.createElement("script");
	script.src = "http://api.map.baidu.com/api?v=1.2&callback=initialize";
	document.body.appendChild(script);
}
function getPoint(){
	var myGeo = new BMap.Geocoder();
	myGeo.getPoint(address, function(point){
	  if (point) {
	    setPoint(point);
	  }
	}, cityName);
}
function setPoint(point){
	  if (point) {
	    map.centerAndZoom(point, 16);
	    var marker = new BMap.Marker(point);
	    var infoWindow = new BMap.InfoWindow("商铺地址:"+address, opts);
			marker.addEventListener("click", function(){
			   this.openInfoWindow(infoWindow);
			});
	    map.addOverlay(marker);
			marker.openInfoWindow(infoWindow);
	  }
}

// 当鼠标放在店铺地图上再加载百度地图。
$(function(){
	$('#mapmore').one('mouseover',function(){
		loadScript();
	});
});
</script>
<script type="text/javascript">
//兑换码列表过多时出现滚条
$(function(){
	$('#codeList').perfectScrollbar();
	//title提示
    	$('.tip').poshytip({
            className: 'tip-yellowsimple',
            showTimeout: 1,
            alignTo: 'target',
            alignX: 'left',
            alignY: 'top',
            offsetX: 5,
            offsetY: -60,
            allowTipHover: false
        });
});
</script>
<script>
function closeErrors() {
return true;
}
window.onerror=closeErrors;
</script>