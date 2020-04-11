<?php 

define('IGNORE_EXCEPTION', true);
if (!@include('global.php')) exit('global.php isn\'t exists!');
if (!@include('33hao.php')) exit('33hao.php isn\'t exists!');
include_once ("WxPayPubHelper.php");

// 使用通用通知接口
$notify = new Notify_pub ();

// 存储微信的回调
$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
file_put_contents('jpush.txt', print_r($xml,true),FILE_APPEND);
$notify->saveData($xml);

define (KEY,'epOKnJrHoCZtVXZyNKhuLYtu2yQ4g3mc'); // 通加密串

file_put_contents('jpush.txt', print_r($notify->checkSign(),true),FILE_APPEND);
if ($notify->checkSign() == TRUE) {
        file_put_contents('jpush.txt', print_r($notify->data["return_code"],true),FILE_APPEND);
    if ($notify->data["return_code"] == "FAIL") {
        file_put_contents('jpush.txt', print_r($notify->data["result_code"],true),FILE_APPEND);
    }elseif ($notify->data["result_code"] == "FAIL") {

    }else {
        $order_sn = intval($notify->data['out_trade_no']);
        file_put_contents('jpush.txt', print_r($order_sn,true),FILE_APPEND);
        $wx_sn  = $notify->data['transaction_id'];
        $total_fee  = $notify->data['total_fee'];
        $total_fee = $total_fee/100;
        $map['order_state'] = 20;
        $rs = Model()->table("order")->where("pay_sn=$order_sn")->update($map);
        file_put_contents('jpush.txt', print_r($rs,true),FILE_APPEND);
        if($rs){
            echo 'SUCCESS';
        }
    }
}else{

    echo 'FAIL';exit;
}

?>