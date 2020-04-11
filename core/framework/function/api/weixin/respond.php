<?php 

require_once '../framework/bootstrap.inc.php';
require_once '../JPush/jpush_fun.php';
include_once ("WxPayPubHelper.php");

// 使用通用通知接口
$notify = new Notify_pub ();

// 存储微信的回调
$xml = $GLOBALS ['HTTP_RAW_POST_DATA'];


// $xml = <<<EOF

// <xml><appid><![CDATA[wxc5eb638545d89fc5]]></appid>
// <bank_type><![CDATA[CFT]]></bank_type>
// <cash_fee><![CDATA[1]]></cash_fee>
// <fee_type><![CDATA[CNY]]></fee_type>
// <is_subscribe><![CDATA[N]]></is_subscribe>
// <mch_id><![CDATA[1327475501]]></mch_id>
// <nonce_str><![CDATA[vpkn76bh8favjtjg63aqn9pg37vggruc]]></nonce_str>
// <openid><![CDATA[oATQ7xNdnCjzWe-LCusRkLJp_Svs]]></openid>
// <out_trade_no><![CDATA[20160415120307]]></out_trade_no>
// <result_code><![CDATA[SUCCESS]]></result_code>
// <return_code><![CDATA[SUCCESS]]></return_code>
// <sign><![CDATA[4ADCA710799879959FD3497E01CF08A8]]></sign>
// <time_end><![CDATA[20160415160749]]></time_end>
// <total_fee>1</total_fee>
// <trade_type><![CDATA[APP]]></trade_type>
// <transaction_id><![CDATA[4000092001201604154868758064]]></transaction_id>
// </xml>
// EOF;

$notify->saveData ( $xml );

define ( KEY, 'JK3248dfs8f3jf4ef0dsfJDEhjd344rf' ); // 通加密串


if ($notify->checkSign () == TRUE) {

    if ($notify->data ["return_code"] == "FAIL") {

    }elseif ($notify->data ["result_code"] == "FAIL") {

    }else {
        
        $order_sn = intval ( $notify->data['out_trade_no'] );
        
        $wx_sn  = $notify->data['transaction_id'];
        $total_fee  = $notify->data['total_fee'];
        $total_fee = $total_fee/100;
        
        
        $o = pdo_fetch("select * from ". tablename('dd_dache_data') ." where `o_sn` = :o_sn",array(':o_sn'=>$order_sn));
        if($o['status'] == 3){ //待支付
            
            if($o['amount'] == $total_fee){
                    
                $did = $o['did'];
                $oid = $o['id'];
                $uid = $o['from_user'];
                
                $driver = pdo_fetch("select charge,AES_DECRYPT(money,'ppkcOK668') as money from ". tablename('dd_dache_driver') ." where did = :did",array('did'=>$did));
                
                $data['charge'] = $driver['charge'] + $total_fee;
                $money = $driver['money'] + $total_fee;
                
                $res1 = pdo_update('dd_dache_driver', $data, array('did' => $did));
                $res2 = pdo_query("update ".tablename('dd_dache_driver')." set money = AES_ENCRYPT('$money','ppkcOK668')  WHERE did = :did", array(':did' => $did));
                
                if (!empty($res1) && !empty($res2)) {
                
                    $data1['status'] = 4;
                    $data1['trade_no'] = $wx_sn;
                    $data1['pay_time'] = time();
                    $data1['pay_type'] = 'wxpay';
                
                    $res = pdo_update('dd_dache_data', $data1, array('id' => $o['id']));
                    if(!empty($res)){
                        echo 'SUCCESS';
                
                
                        $push = array('op'=>'pay_success','oid'=>$oid,'pay_type'=>'wxpay');
                
                        do_push(1,$push,"乘客已成功付款 $total_fee 元到您的账户(车费：$o[fee] 元,小费：$o[tip] 元)【乒乓快车】",$did); //推送司机
                        do_push(2,$push,"您已成功支付车费 $total_fee 元,感谢您的使用。【乒乓快车】",$uid); //推送用户
                
                    }else{
                        echo 'FAIL';
                    }
                }else{
                    echo 'FAIL';
                }
                
            }else echo 'FAIL';    
            
        }else echo 'FAIL';
    }
}else{

    echo 'FAIL';exit;
}

?>