<?php
/**
 * 支付
 *
 * @好商城V4 (c) 2005-2016 33hao Inc.
 * @license    http://www.haoid.cn
 * @link       交流群号：216611541
 * @since      好商城提供技术支持 授权请购买shopnc授权
 */
defined('InShopNC') or exit('Access Invalid!');

class mobile_paymentControl extends mobileControl{

    //获取支付宝支付字符串
    public function get_paystrOp(){
        $pay_sn = $_POST['pay_sn'];
        $amount = $_POST['amount'];
        // $amount = 0.01;
        require_once './api/alipay/lib/alipay_core.function.php';
        require_once './api/alipay/lib/alipay_rsa.function.php';
        $private_key_path = './api/alipay/key/pkcs.txt';
        $params = array();

        $params['service'] = 'mobile.securitypay.pay';
        $params['partner'] = '2088221492686499';
        $params['notify_url'] = 'http://www.nrwspt.com/mobile/api/alipay/notify_url.php';
        $params['_input_charset'] = 'utf-8';
        $params['out_trade_no'] = $pay_sn;
        $params['subject'] = '办工师';
        $params['seller_id'] = '15238304399@163.com';
        $params['body'] = 'ddd';
        $params['total_fee'] = $amount;
        $params['payment_type'] = '1';
        // $params['it_b_pay'] = '30m';
        $params['show_url'] = 'm.alipay.com';

        $para_filter = paraFilter($params);

        //对待签名参数数组排序
        $para_sort = argSort($para_filter);

        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = createLinkstring($para_sort);

        $sign = rsaSign($prestr,$private_key_path);
        $params['sign'] = urlencode($sign);
        $params['sign_type'] = 'RSA';
        $a = createLinkstring($params);
        $res = getarrres('200','success',$a);
        echo json_encode($res);
    }
     
    //获取微信支付字符串    
    public function get_wx_paystrOp(){
        $pay_sn = $_POST['pay_sn'];
        $amount = $_POST['amount'];
        // $amount = 0.01;
        $return_url = 'http://www.nrwspt.com/mobile/api/weixin/respond.php';
        define ( APPID, 'wx389228fd0b48b35b' ); // appid
        define ( APPSECRET, '916b8eee1fb8961319276607013d66c8' ); // appSecret
        define ( MCHID, '1370739702' );
        define ( KEY, 'epOKnJrHoCZtVXZyNKhuLYtu2yQ4g3mc'); // 通加密串
        define ( NOTIFY_URL, $return_url ); // 成功回调url

        include_once ("./api/weixin/WxPayPubHelper.php");  

        $selfUrl = 'http://'.$_SERVER ['HTTP_HOST'].$_SERVER ['PHP_SELF'].'?'.$_SERVER ['QUERY_STRING'];
        $unifiedOrder = new UnifiedOrder_pub ();
        $unifiedOrder->setParameter ("body", '办工师支付维修费用');
        $unifiedOrder->setParameter ("out_trade_no", $pay_sn); // 商户订单号
        $unifiedOrder->setParameter ("total_fee", $amount * 100 ); // 总金额
        $unifiedOrder->setParameter ("notify_url", NOTIFY_URL ); // 通知地址
        $unifiedOrder->setParameter ("trade_type", "APP" ); // 交易类型

        $prepay_id = $unifiedOrder->getPrepayId();
        $app = new app();
        $a = $app->getParameters($prepay_id);
        $res = getarrres('200','success',$a);
        exit(json_encode($res));
    }

    /**
     * 站内余额支付(充值卡、预存款支付) 实物订单 rcb_pay pd_pay
     */
    public function orderpayOp(){
        $pay_sn = $_POST['pay_sn'];
        $post['rcb_pay'] = $_POST['rcb_pay'];
        $post['pd_pay'] = $_POST['pd_pay'];
        $post['pay_sn']   = $pay_sn;
        $post['amount']   = floatval($_POST['amount']);
        $post['password'] = $_POST['password'];//支付密码
        if (empty($post['password'])) {
            $res = getarrres('-401','密码为空',$order_list);
            exit(json_encode($res));
        }
        $order_list = Model()->table('order')->where("pay_sn=$pay_sn")->find();
        if($order_list['order_state'] != 10){
            if($order_list['order_state'] > 10){
                $res = getarrres('-401','订单已付款',0);
                exit(json_encode($res));
            }
        }
        $model_member = Model('member');
        $buyer_info = $model_member->getMemberInfoByID($order_list['buyer_id']);
        if ($buyer_info['member_paypwd'] == '' || $buyer_info['member_paypwd'] != md5($post['password'])) {
            $res = getarrres('-401','密码错误',$order_list);
            exit(json_encode($res));
        }
        if ($buyer_info['available_rc_balance'] == 0) {
            $post['rcb_pay'] = null;
        }
        if ($buyer_info['available_predeposit'] == 0) {
            $post['pd_pay'] = null;
        }
        if (floatval($order_list[0]['rcb_amount']) > 0 || floatval($order_list[0]['pd_amount']) > 0) {
            $res = getarrres('-401','支付',$order_list);
            exit(json_encode($res));
        }
        $orderss[] = $order_list;
        try {
            $model_member->beginTransaction();
            $logic_buy_1 = Logic('buy_1');
            //使用充值卡支付
            if ($post['rcb_pay'] == 1) {
                $post['rcb_pay'] = $post['amount'];
                $memap['available_rc_balance'] = floatval($buyer_info['available_rc_balance'])-floatval($post['rcb_pay']);
                $order_lists = $logic_buy_1->rcbPay($orderss, $post, $buyer_info);
            }else{
                //使用预存款支付
                if ($post['pd_pay'] == 1) {
                    $post['pd_pay'] = $post['amount'];
                    $memap['available_predeposit'] = floatval($buyer_info['available_predeposit'])-floatval($post['pd_pay']);
                    $order_lists = $logic_buy_1->pdPay($orderss, $post, $buyer_info);
                }
            }
            //特殊订单站内支付处理
            $logic_buy_1->extendInPay($orderss);
            $model_member->commit();
            $memap['freeze_rc_balance'] = 0;
            $mid = $order_list['buyer_id'];
            $model_member->where("member_id=$mid")->update($memap);
        } catch (Exception $e) {
            $res = getarrres('-401','',$e->getMessage());
            exit(json_encode($res));
        }
        $res = getarrres('200','success',$order_list);
        exit(json_encode($res));
    }
}
