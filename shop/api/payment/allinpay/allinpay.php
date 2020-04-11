<?php
/**
 * 网银在线接口类
 *
 *
 * by 33hao 好商城V3  www.haoid.cn 开发
 */
defined('InShopNC') or exit('Access Invalid!');
require_once BASE_DATA_PATH.DS.'api/allinpay/Pay.php';
require_once BASE_DATA_PATH.DS.'api/allinpay/Responses/Pay.php';

use Fakeronline\AllinpaySDK;

class allinpay{
	/**
	 * 通联支付网关
	 *
	 * @var string
	 */
	//private $gateway   = 'http://ceshi.allinpay.com/gateway/index.do';
	private $gateway   = 'https://service.allinpay.com/gateway/index.do';

	private $key 		 = '1234567890';
	/**
	 * 支付接口标识
	 *
	 * @var string
	 */
	private $code      = 'allinpay';
	/**
	 * 支付接口配置信息
	 *
	 * @var array
	 */
	private $payment;
	/**
	 * 订单信息
	 *
	 * @var array
	 */
	private $order;
	/**
	 * 发送的参数
	 *
	 * @var array
	 */
	private $parameter;
	/**
	 * 支付状态
	 * @var unknown
	 */
	private $pay_result;

	public function __construct($payment_info,$order_info){
		$this->allinpay($payment_info,$order_info);
	}
	public function allinpay($payment_info = array(),$order_info = array()){
		if(!empty($payment_info) and !empty($order_info)){
			$this->payment	= $payment_info;
			$this->order	= $order_info;
		}
	}
	/**
	 * 支付表单
	 *
	 */
	public function submit(){

		$v_amount = $this->order['api_pay_amount'];                  			//支付金额
		$v_mid =  '109223111802007';//'100020091218001';//$this->payment['payment_config']['allinpay_account'];	// 商户号
		
		if($this->order['order_type']=='pd_order'){
			$v_syncurl = SHOP_SITE_URL."/api/payment/allinpay/pickup_pd.php";
			$v_asynurl = SHOP_SITE_URL."/api/payment/allinpay/receive_pd.php";
		}else if($this->order['order_type']=='vr_order'){
			$v_syncurl = SHOP_SITE_URL."/api/payment/allinpay/pickup_vr.php";
			$v_asynurl = SHOP_SITE_URL."/api/payment/allinpay/receive_vr.php";
		}else{
			$v_syncurl = SHOP_SITE_URL."/api/payment/allinpay/pickup.php";	// 请填写返回url,地址应为绝对路径,带有http协议
			$v_asynurl = SHOP_SITE_URL."/api/payment/allinpay/receive.php";
		}
		
		$key   =  $this->key;//'1234567890';//$this->payment['payment_config']['allinpay_key'];			// 如果您还没有设置MD5密钥请登陆我们为您提供商户后台

		$pay = new AllinpaySDK\Pay($this->gateway, $v_mid, $key); //创建支付实例
		$pay->setUrl($v_syncurl , $v_asynurl);    //设置同步URL和异步URL(单选或多选)

		$pay->parameter($this->order['pay_sn'], $v_amount)->request();
		exit;
	}

	/**
	 * 返回地址验证(同步)
	 *
	 * @param
	 * @return boolean
	 */
	public function return_verify(){

		$pay = new AllinpaySDK\Responses\Pay($this->key);
		$result = $pay->chkVerify($_REQUEST);

		/**
		 * 判断返回信息，如果支付成功，并且支付结果可信，则做进一步的处理
		 */
		if($result){
			if($pay->errorMsg){
				return false;//echo "支付失败";
			}else{
				$this->pay_result = true;
				//支付成功，可进行逻辑处理！
				return true;
			}
		}else{
			return false;//echo "<br>校验失败,数据可疑";
		}
	}

	/**
	 * 返回地址验证(异步)
	 * @return boolean
	 */
	public function notify_verify() {
		
		$pay = new AllinpaySDK\Responses\Pay($this->key);
		$result = $pay->chkVerify($_REQUEST);

		/**
		 * 判断返回信息，如果支付成功，并且支付结果可信，则做进一步的处理
		 */
		if($result){
			if($pay->errorMsg){
				return false;//echo "支付失败";
			}else{
				$this->pay_result = true;
				//支付成功，可进行逻辑处理！
				return true;
			}
		}else{
			return false;//echo "<br>校验失败,数据可疑";
		}
	}

	/**
	 * 取得订单支付状态，成功或失败
	 *
	 * @param array $param
	 * @return array
	 */
	public function getPayResult($param){
		return $this->pay_result;
	}

	public function __get($name){
		return $this->$name;
	}
}
