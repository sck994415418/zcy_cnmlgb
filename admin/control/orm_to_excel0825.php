<?php
/**
 *  v3-b12
 *
 *by 好商城V3 www.haoid.cn 运营版
 **/
function exportExcel($filename,$content){
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/vnd.ms-execl");
	header("Content-Type: application/force-download");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=".$filename);
	header("Content-Transfer-Encoding: binary");
	header("Pragma: no-cache");
	header("Expires: 0");
	
	echo $content;
}	
if($is_bind_type == 1){
	$str = "sku\t商品名称\t政府商品id\t政府商品名称\n";
	//$str = iconv('utf-8','gb2312',$str);
	//print_r($goods_orm_list);
	//die;
	
	if(is_array($goods_orm_list)){
		foreach($goods_orm_list as $key=>$val){
			$str = $str. $val['skuid']."\t".$val['productNameEC']."\t".$val['productId']."\t".$val['productName']."\t0\n";
		}
	}
	$filename = time().'.xls';
	
}elseif($is_bind_type == 2){
	$str = "sku\t商品名称\t政府商品id\t政府商品名称\n";
	//$str = iconv('utf-8','gb2312',$str);
	//print_r($goods_orm_list);
	//die;
	
	if(is_array($goods_orm_list)){
		foreach($goods_orm_list as $key=>$val){
			$str = $str. $val['skuid']."\t".$val['productNameEC']."\t".$val['productId']."\t".$val['productName']."\t0\n";
		}
	}
	$filename = time().'.xls';
}else{
	$str = "sku\t商品名字\t政府商品id\t报价数\n";
	//$str = iconv('utf-8','gb2312',$str);
	//print_r($goods_orm_list);
	//die;
	
	if(is_array($goods_orm_list)){
		foreach($goods_orm_list as $key=>$val){
			$str = $str. $val['goods_id']."\t".$val['goods_name']."\t".$val['product_id']."\t".$val['store_num']."\t0\n";
		}
	}
	$filename = time().'.xls';
	
}
	exportExcel($filename,$str);
?>