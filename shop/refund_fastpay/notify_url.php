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
require_once("../../index.php");

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();
if($verify_result) {//验证成功

	file_put_contents('a.txt', print_r($_POST,true));
	//批次号
	$batch_no = $_POST['batch_no'];

	//批量退款数据中转账成功的笔数
	$success_num = $_POST['success_num'];

	//批量退款数据中的详细信息
	$result_details = $_POST['result_details'];

	$arr = explode('^', $result_details);
	$refund_fee = $arr['1'];
	$trade_no = $arr['0'];
	$map['refund_amount'] = $refund_fee;
	$map['paystate'] = 1;
	$where['trade_no'] = $trade_no;
	$rs = Model()->table('order')->where($where)->update($map);

	file_put_contents('a.txt', print_r($rs,true));
        
	echo "success";		

}
else {
    //验证失败
    echo "fail";

    //调试用，写文本函数记录程序运行情况是否正常
    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
}
?>