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

        //订单消息推送
        $omap['pay_sn'] = $order_sn;
        $order = Model()->table("order")->where($omap)->find();
        if($order['order_state'] == 10){
            $apptime = isset($order['apptime'])?$order['apptime']:'';
            if($apptime != ''){
                $yy = 1;
            }else{
                $yy = 0;
            }
            $areainfo = 'teacher';
            $city = isset($order['city'])?$order['city']:'';
            if($city != ''){
                $areainfo = $city;
            }
            $order_id = isset($order['order_id'])?$order['order_id']:'';
            ini_set("display_errors", "On");
            error_reporting(E_ALL | E_STRICT);
            require_once("../../../src/JPush/JPush.php");

            $app_key = '48dac1cb88f87fb9561a33ff';
            $master_secret = 'dbb71af71f2f49e355a70777';

            // 初始化
            $client = new JPush($app_key, $master_secret);

            $result = $client->push();
            $result->setPlatform(array('ios', 'android'));
            // $result->addAlias($regi);
            $result->addTag(array($areainfo));
            // $result->setNotificationAlert($content);
            $result->addAndroidNotification('有新的订单！', '办工师', 1, array('time'=>time(),'order_id'=>$order_id,'name'=>$order['buyer_name'],'address'=>$order['address'],'type'=>$order['type'],'tel'=>$order['tel'],'scost'=>$order['scost'],'yuyue'=>$yy,'apptime'=>$apptime));
            $result->addIosNotification("有新的订单！", 'iOS sound', JPush::DISABLE_BADGE, true, 'iOS category', array('time'=>time(),'order_id'=>$order_id,'name'=>$order['buyer_name'],'address'=>$order['address'],'type'=>$order['type'],'tel'=>$order['tel'],'scost'=>$order['scost'],'yuyue'=>$yy,'apptime'=>$apptime));
            $result->setMessage("有新的订单！", 'msg title', 'type', array('time'=>time(),'order_id'=>$order_id,'name'=>$order['buyer_name'],'address'=>$order['address'],'type'=>$order['type'],'tel'=>$order['tel'],'scost'=>$order['scost'],'yuyue'=>$yy,'apptime'=>$apptime));
            $result->setOptions(100000,3600,null,false);
            $result->send();
        }
        if($order['order_state'] == 10){
            $map['order_state'] = 20;
            $condition['pay_sn'] = $order_sn;
            $map['payment_time'] = time();
            $map['trade_no'] = $wx_sn;
            $map['paytype'] = 'wx';
            // file_put_contents('jpush.txt', print_r($condition,true),FILE_APPEND);
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