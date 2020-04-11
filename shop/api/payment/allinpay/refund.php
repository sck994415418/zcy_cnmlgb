<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>通联支付样例-退款</title>
</head>
<?php

ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);
require_once $_SERVER ['DOCUMENT_ROOT'] . '/data/api/allinpay/Refund.php';
require_once $_SERVER ['DOCUMENT_ROOT'] . '/data/api/allinpay/Services/Response.php';

use Fakeronline\AllinpaySDK;

if(isset($_REQUEST["order_no"]) && $_REQUEST["order_no"] != ""){
	$refund_amount = $_REQUEST["refund_amount"];
	$order_no = $_REQUEST["order_no"];
	$order_datetime = $_REQUEST["order_datetime"];
	
	$refund = new AllinpaySDK\Refund('https://service.allinpay.com/gateway/index.do', '109223111802007', '1234567890'); //创建实例	
	$arr = $refund->parameter($order_no, $refund_amount, date('YmdHis', $order_datetime))->request();
	
	//var_dump($arr);
	
	if(strpos($arr, "refundResult=20") !== false){
		header("location:http://www.nrwspt.com/admin/index.php?act=vr_order&op=allinpay_state&refund_amount=".$refund_amount."&trade_no=".$order_no);
		exit();
	}else{
		echo "<script>alert('退款失败！');history.go(-1);</script>";
	}
	exit();
}

?>
</html>