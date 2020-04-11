<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。


 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 */

require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");
require_once('../../../index.php');
//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();


file_put_contents('jpush.txt', print_r($verify_result,true),FILE_APPEND);
if($verify_result){//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代

	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
	
    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
	
	//商户订单号

	$out_trade_no = $_POST['out_trade_no'];

	//支付宝交易号

	$trade_no = $_POST['trade_no'];

	//交易状态
	$trade_status = $_POST['trade_status'];


    if($_POST['trade_status'] == 'TRADE_FINISHED') {
    }else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
        $omap['pay_sn'] = $out_trade_no;
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
        //支付宝交易号
        if($order['order_state'] == 10){
            $map['order_state'] = 20;
            $condition['pay_sn'] = $out_trade_no;
            $map['payment_time'] = time();
            $map['trade_no'] = $trade_no;
            $map['paytype'] = 'zfb';
            // file_put_contents('jpush.txt', print_r($condition,true),FILE_APPEND);
            $rs = Model()->table("order")->where($condition)->update($map);
            if($rs){

                echo 'SUCCESS';
            }
        }else{
            echo 'SUCCESS';
        }
        
    }else{ echo 'fail';
        
        
        
    }

}else {
    //验证失败
    echo "fail";

}
?>