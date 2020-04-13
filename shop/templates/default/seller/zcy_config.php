<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
  <a href="<?php echo urlShop('store_goods_add');?>" class="ncsc-btn ncsc-btn-green" title="<?php echo $lang['store_goods_index_add_goods'];?>"> <?php echo $lang['store_goods_index_add_goods'];?></a>
</div>
<style type="text/css">
  @import url("<?php echo SHOP_TEMPLATES_URL; ?>/css/zcy.css");
</style>
<div id="content">

<?php
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
	if($rs["success"]){
		$total = $rs["data_response"]["total"];
		if($total > 0){
?>
				<ul class="list_title">
                	<li class="itemcode">商品代码(货号)</li>
                    <li class="category">商品分类</li>
                    <li class="brandname">品牌名称</li>
                    <li class="goodname">商品名称</li>
                    <li class="goodstorage">库存</li>
                </ul>
<?php			
			foreach($rs["data_response"]["data"] as $good){
?>
				<ul class="goods_line">
                	<li class="itemcode"><?php echo $good["itemCode"]; ?></li>
                    <li class="category"><?php echo $good["categoryId"]; ?></li>
                    <li class="brandname"><?php echo $good["brandName"]; ?></li>
                    <li class="goodname"><?php echo $good["name"]; ?></li>
<?php
				$stock = $zcy->get_stock($good["itemCode"],"");
				print_r($stock);
?>
                    <li class="goodstorage"><?php echo "件"; ?></li>
                </ul>
<?php
			}
			$page = new page($total,20);
			echo "<div id=\"pageinfo\">".$page->fpage()."</div>";
		}else{
			echo "暂无内容";
		}
	}
?>
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