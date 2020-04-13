<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
  <a href="<?php echo urlShop('zcy_goods','add_goods');?>" class="ncsc-btn ncsc-btn-green" title="<?php echo $lang['store_goods_index_add_goods'];?>"> <?php echo $lang['store_goods_index_add_goods'];?></a>
</div>
<style type="text/css">
  @import url("<?php echo SHOP_TEMPLATES_URL; ?>/css/zcy.css");
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
	$type = $_GET["type"];
	switch($type){
		case "on_shelf":
			$status = 1;
			break;
		case "off_shelf":
			$status = -1;
			break;
		case "freez":
			$status = -2;
			break;
		case "verify":
			$status = 2;
			break;
		case "refuse":
			$status = -4;
			break;
		default:
			$status = 1;
			break;
	}
	require_once(BASE_PATH.'/../zcy/nr_zcy.php');
	$zcy = new nr_zcy("314930527","rCT3MqDWnuSvYUhQfkzN");
	$rs = $zcy->goods_list($status,$pageNo,20,11);
?>
<table class="ncsc-default-table">
  <thead>
    <tr nc_type="table_header">
      <th class="w30">&nbsp;</th>
      <th class="w50">&nbsp;</th>
      <th coltype="editable" column="goods_name" checker="check_required" inputwidth="230px">商品名称</th>
      <th class="w100">价格</th>
      <th class="w100">库存</th>
      <th class="w100">发布时间</th>
      <th class="w120">操作</th>
    </tr>
  </thead>
<?php
	if($rs["success"]){
		$total = $rs["data_response"]["total"];
		if($total > 0){
?>
  <tbody>
<?php			
			foreach($rs["data_response"]["data"] as $goods){
				$goods_detail = $zcy->get_goods_detail($goods["itemCode"],$goods["itemId"]);
				if(is_null($goods_detail["error_response"])){
					$goods_detail = $goods_detail["data_response"];
				}else{
					exit("获取商品详情出错:</br>错误代码:".$goods_detail["error_response"]["resultId"]."</br>错误描述：".$goods_detail["error_response"]["resultMsg"]);
				}
				$stock_array = $zcy->get_goods_stock($goods["itemId"],"");
				$stock_all = 0;
				if($stock_array["success"]){
					foreach($stock_array["result"] as $sku){
						foreach($sku["warehouses"] as $stock){
							$stock_all = $stock_all + intval($stock["quantity"]);
						}
					}
				}else{
					$stock_all = $stock_array["error"];
				}

?>
	<tr>
      <th class="tc"><input type="checkbox" class="checkitem tc" onclick="is_p_Allselect()" name="checkitem" <?php if ($val['goods_lock'] == 1) {?>disabled="disabled"<?php }?> value="<?php echo $goods["itemId"]; ?>"/></th>
      <th colspan="20">基础商品ID：<?php echo $goods["itemId"];?></th>
    </tr>
    <tr>
      <td class="trigger"></td>
      <td><div class="pic-thumb"><img src="<?php echo $goods_detail["item"]["mainImage"];?>"/></div></td>
      <td class="tl"><dl class="goods-name">
          <dt style="max-width: 450px !important;"><?php echo $goods["name"]; ?></dt>
          <dd>商家货号：<?php echo "aa";?></dd>
        </dl></td>
      <td><span>¥<?php echo number_format($goods_detail["skus"][0]["price"]/100,2); ?></span></td>
      <td><span <?php if ($output['storage_array'][$val['goods_commonid']]['alarm']) { echo 'style="color:red;"';}?>><?php echo $stock_all.$lang['piece']; ?></span></td>
      <td class="goods-time"><?php echo @date('Y-m-d',$val['goods_addtime']);?></td>
      <td class="nscs-table-handle"><?php if ($val['goods_lock'] == 0) {?>
        <span><a href="<?php echo urlShop('store_goods_online', 'edit_goods', array('commonid' => $val['goods_commonid']));?>" class="btn-blue"><i class="icon-edit"></i>
        <p>编辑</p>
        </a></span><span><a href="javascript:void(0);" onclick="ajax_get_confirm('<?php echo $lang['nc_ensure_del'];?>', '<?php echo urlShop('store_goods_online', 'drop_goods', array('commonid' => $val['goods_commonid']));?>');" class="btn-red"><i class="icon-trash"></i>
        <p>删除</p>
        </a></span>
        <?php } else {?>
        <span class="tip" title="该商品参加抢购活动期间不能进行编辑及删除等操作,可以编辑赠品和推荐组合"><a href="<?php if ($val['is_virtual'] ==1 ) {echo 'javascript:void(0);';} else {echo urlShop('store_goods_online', 'add_gift', array('commonid' => $val['goods_commonid']));}?>" class="btn-orange-current"><i class="icon-lock"></i>
        <p>锁定</p>
        </a></span>
        <?php }?></td>
    </tr>
<?php
				if(count($goods_detail["skus"]) > 1){
?>
    <tr>
      <td colspan="20">
      	<div class="ncsc-goods-sku ps-container">
<?php
					foreach($goods_detail["skus"] as $sku){
?>
        	<ul class="ncsc-goods-sku-list">
            	<li>
                	<div class="goods-thumb" title="商家货号：">
                    	<a href="" target="_blank"><img src="<?php echo $goods_detail["item"]["mainImage"];?>"></a>
                    </div>
<?php
                    	foreach($sku["attrs"] as $attrs_name => $attrs_value){
?>
                    <div class="goods_spec"><?php echo $attrs_name; ?>：<em title="<?php echo $attrs_value; ?>"><?php echo $attrs_value; ?></em></div>
<?php
						}
?>
                    <div class="goods-price">价格：<em title="￥<?php echo number_format($sku["price"]/100,2); ?>">￥<?php echo number_format($sku["price"]/100,2); ?></em></div>
                    <div class="goods-storage">库存：<em title="<?php echo $sku["quantity"]; ?>"><?php echo $sku["quantity"]; ?></em></div>
                    <a href="http://www.nrwspt.com/shop/index.php?act=goods&amp;op=index&amp;goods_id=112660" target="_blank" class="ncsc-btn-mini">查看商品详情</a>
                </li>
            </ul>
<?php
					}
?>
            <div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 3px; width: 950px; display: none;">
            	<div class="ps-scrollbar-x" style="left: 0px; width: 0px;"></div>
            </div>
            <div class="ps-scrollbar-y-rail" style="top: 0px; right: 3px; height: 204px; display: inherit;">
            	<div class="ps-scrollbar-y" style="top: 0px; height: 196px;"></div>
            </div>
        </div>
      </td>
    </tr>
<?php
				}
			}
			
		}else{
?>
    <tr>
      <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
<?php
		}
?>
  </tbody>
  <tfoot>
<?php
		if($total > 0){
			$page = new page($total,20);
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