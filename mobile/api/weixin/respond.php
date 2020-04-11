<?php 

require_once('../../../index.php');
include_once ("WxPayPubHelper.php");

// 使用通用通知接口
$notify = new Notify_pub ();

// 存储微信的回调
$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
$notify->saveData($xml);

define (KEY,'epOKnJrHoCZtVXZyNKhuLYtu2yQ4g3mc'); // 通加密串

// file_put_contents('jpush.txt', print_r($notify->checkSign(),true),FILE_APPEND);
if ($notify->checkSign() == TRUE) {
        // file_put_contents('jpush.txt', print_r($notify->data["return_code"],true),FILE_APPEND);
    if ($notify->data["return_code"] == "FAIL") {
        // file_put_contents('jpush.txt', print_r($notify->data["result_code"],true),FILE_APPEND);
    }elseif ($notify->data["result_code"] == "FAIL") {

    }else {
        $order_sn = intval($notify->data['out_trade_no']);
        $wx_sn  = $notify->data['transaction_id'];
        $omap['pay_sn'] = $order_sn;
        $order = Model()->table("order")->where($omap)->find();
        $gmap['order_id'] = $order['order_id'];
        $goods = Model()->table('order_goods')->where($gmap)->select();
        if(!empty($goods)){
            foreach ($goods as $key => $value) {
                $arr[] = $goods['goods_id'];
            }
            $str = implode(',', $arr);
            $sql = "update ".DBPRE."goods set `goods_salenum`=`goods_salenum`+1 where goods_id in (".$str.")";
            Model()->query($sql);
        }
        if($order['order_state'] == 10){
            $map['order_state'] = 20;
            // file_put_contents('jpush.txt', print_r($map,true),FILE_APPEND);
            $condition['pay_sn'] = $order_sn;
            $map['payment_time'] = time();
            $map['trade_no'] = $wx_sn;
            $map['paytype'] = 'wx';
            // file_put_contents('jpush.txt', print_r($condition,true),FILE_APPEND);
            // file_put_contents('jpush.txt', print_r(Model("order"),true),FILE_APPEND);
            $rs = Model()->table("order")->where($condition)->update($map);
            
            if($rs){
                echo 'SUCCESS';
            }
        }else{
            echo 'SUCCESS';
        }
    }
}else{

    echo 'FAIL';exit;
}

?>