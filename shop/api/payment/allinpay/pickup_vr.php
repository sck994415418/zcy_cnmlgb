<?php
require_once $_SERVER ['DOCUMENT_ROOT'] . '/data/api/allinpay/Pay.php';
require_once $_SERVER ['DOCUMENT_ROOT'] . '/data/api/allinpay/Services/Response.php';

$_GET['act']	= 'payment';
$_GET['op']		= 'return';
$_GET['payment_code'] = 'allinpay';

$_GET['out_trade_no'] = $_POST ['orderNo'];
$_GET['extra_common_param'] = 'vr_order';
$_GET['trade_no'] = $_POST ['orderNo'];
$_GET['order_datetime'] = $_POST ['orderDatetime'];

//var_dump($_REQUEST);

require_once(dirname(__FILE__).'/../../../index.php');

?>
