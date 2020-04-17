<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="tabmenu">
  <?php include template('layout/submenu_order');?>
<!--  <a href="--><?php //echo urlShop('zcy_goods','add_goods');?><!--" class="ncsc-btn ncsc-btn-green" title="--><?php //echo $lang['store_goods_index_add_goods'];?><!--"> --><?php //echo $lang['store_goods_index_add_goods'];?><!--</a>-->
</div>
<style type="text/css">
    <link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
</style>
<div id="content">
<?php
	if (!@include(BASE_PATH.'/control/zcy_connect_data.php')) exit('zcy_connect_data.php isn\'t exists!');
	$pageNo = $_GET["page"];
	if(is_numeric($pageNo)){
		$pageNo = floor($pageNo);
		if($pageNo < 1){
			$pageNo = 1;
		}
	}else{
		$pageNo = 1;
	}
	$type = $_GET["status"];
	switch($type){
        case '0'://待接单
            $status = 0;
            break;
        case '1'://已接单待发货
            $status = 1;
            break;
        case '2'://已部分发货待确认
            $status = 2;
            break;
        case '3'://全部发货,待确认收货
            $status = 3;
            break;
        case '4'://已确认收货,待验收
            $status = 4;
            break;
        case '5'://已验收待结算
            $status = 5;
            break;
        case '6'://启动结算
            $status = 6;
            break;
        case '7'://交易完成
            $status = 7;
            break;
        case '-4'://采购人申请取消订单
            $status = -4;
            break;
        case '10'://退换货中
            $status = 10;
            break;
        case '-2'://供应商拒绝接单
            $status = -2;
            break;
        case '-5'://供应商同意取消订单
            $status = -5;
            break;
        case '-6'://全部退货、订单关闭
            $status = -6;
            break;
        default:
            $status = 0;
            break;
	}
	require_once(BASE_PATH.'/../zcy/nr_zcy.php');
	$zcy = new nr_zcy();
	$rs = $zcy->order_list($status,'',$pageNo,10);
?>
<table class="ncsc-default-table order">
    <thead>
    <tr>
        <th class="w10"></th>
        <th colspan="2"><?php echo $lang['store_order_goods_detail'];?></th>
        <th class="w100"><?php echo $lang['store_order_goods_single_price'];?></th>
        <th class="w40"><?php echo $lang['store_show_order_amount'];?></th>
        <th class="w110"><?php echo $lang['store_order_buyer'];?></th>
        <th class="w120"><?php echo $lang['store_order_sum'];?></th>
        <th class="w150">交易操作</th>
    </tr>
    </thead>
<?php
	if($rs["success"]){
	    echo "<pre>";
//	    var_dump($rs["data_response"]["data"]);
		$total = $rs["data_response"]["total"];
		if($total > 0){
?>
<?php
			foreach($rs["data_response"]["data"] as $v){
?>




                <tbody>
                <tr>
                    <td colspan="20" class="sep-row"></td>
                </tr>
                <tr>
                    <th colspan="20"><span class="ml10">订单编号：<em><?php echo $v['order']['id'];?></em>
        </span> <span>下单时间：<em class="goods-time"><?php echo $v['order']['create_time']; ?></em></span>
<!--                        <span class="fr mr5"> <a href="index.php?act=store_order_print&amp;order_id=577" class="ncsc-btn-mini" target="_blank" title="打印发货单"><i class="icon-print"></i>打印发货单</a></span>-->
                    </th>
                </tr>


                <?php foreach($v["orderItems"] as $g){?>
                <tr>
                    <td class="bdl"></td>
                    <td class="w70"><div class="ncsc-goods-thumb"><a href="http://www.xlshop.com/shop/index.php?act=goods&amp;op=index&amp;goods_id=<?php echo $v['shipmentItems']?>" target="_blank"><img src="http://www.xlshop.com/data/upload/shop/common/default_goods_image_60.gif" onmouseover="toolTip('<img src=http://www.xlshop.com/data/upload/shop/common/default_goods_image_240.gif>')" onmouseout="toolTip()"></a></div></td>
                    <td class="tl"><dl class="goods-name">
                            <dt><a target="_blank" href="https://www.zcygov.cn/items/<?php echo $g['itemId']?>"><?php echo $g['itemName']; ?></a></dt>
                            <dd>
                            </dd>
                        </dl></td>
                    <td><?php echo number_format($g['skuPrice']*.01,2); ?></td>
                    <td><?php echo $g['quantity']; ?></td>

                    <!-- S 合并TD -->
                    <td class="bdl" rowspan="1"><div class="buyer"><?php echo $v['order']['purchaserName'];?>          <p member_id="486">
                            </p>
                            <div class="buyer-info"> <em></em>
                                <div class="con">
                                    <h3><i></i><span>联系信息</span></h3>
                                    <dl>
                                        <dt>姓名：</dt>
                                        <dd><?php echo $v['delivery']['receiverName']?></dd>
                                    </dl>
                                    <dl>
                                        <dt>电话：</dt>
                                        <dd><?php echo $v['delivery']['mobile']?></dd>
                                    </dl>
                                    <dl>
                                        <dt>地址：</dt>
                                        <dd><?php echo $v['delivery']['fullAddr']?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div></td>
                    <td class="bdl" rowspan="1"><p class="ncsc-order-amount"><?php echo number_format($v['order']['fee'],2);?></p>
<!--                        <p class="goods-freight">-->
<!--                            （免运费）                  </p>-->
                        <p class="goods-pay" title="支付方式：在线付款">在线付款</p></td>
                    <td class="bdl bdr" rowspan="1"><p></p>

                        <!-- 订单查看 -->
                        <p><a href="index.php?act=zcy_order&amp;op=show_order&amp;orderId=<?php echo $v['order']['id']?>&amp;status=<?php echo $status?>" target="_blank">订单详情</a></p>

                        <!-- 物流跟踪 -->
                        <p>
                        </p>


                    </td>



                    <!-- E 合并TD -->
                </tr>

                <?php } ?>

                <!-- S 赠品列表 -->
                <!-- E 赠品列表 -->

                </tbody>






<?php

			}

		}else{
?>
    <tr>
      <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
<?php
		}
?>
  <tfoot>
<?php
		if($total > 0){
			$page = new page($total,10);
?>
    <tr>
      <td colspan="20"><div class="pagination"> <?php echo $page->fpage(); ?> </div></td>
    </tr>
<?php
		}
	}
?>
  </tfoot>

</table>

</div>



<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js"></script>
<script src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/store_goods_list.js"></script> 
<script>
$(function(){
    //Ajax提示
    $('.tip').poshytip({
        className: 'tip-yellowsimple',
        showTimeout: 1,
        alignTo: 'target',
        alignX: 'center',
        alignY: 'top',
        offsetY: 5,
        allowTipHover: false
    });
});
</script>