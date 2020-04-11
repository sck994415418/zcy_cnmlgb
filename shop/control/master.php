<?php
/**
 * 商城api
 * autor:LEE
 * 2016.6.27
 */
defined('InShopNC') or exit('Access Invalid!');
class masterControl extends BaseHomeControl{

    /**
     * 公告
     */
    public function gonggaoOp(){
        $map['name'] = 'gonggao';
        $cost = Model()->table("setting")->where($map)->find();
        $res = getarrres('200','success',$cost['value']);
        exit(json_encode($res));
    }

    /**
     * 绑定手机
     */
    public function mobilebdOp() {
        $tel  = isset($_POST['tel'])?$_POST['tel']:'';
        $key  = $_POST['key'];
        $memberid = getmemberid($key);
        $memap["member_id"] = $memberid;
        $upmap['member_mobile_bind'] = 1;
        if($tel != ''){
            $upmap['member_mobile'] = $tel;
            $upmap['member_name'] = $tel;   
        }
        $member = Model()->table("member")->where($memap)->update($upmap);
        $res = getarrres('200','success',1);
        exit(json_encode($res));
    }

    /**
     * 上传头像
     */
    public function imageOp(){
        $key = $_POST['key'];
        $token = Model("mb_user_token");
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $memberid = $tokens['member_id'];

        $image = $_POST['image'];

        if(empty($image)){
            $res = getarrres('-401','参数为空',0);
            exit(json_encode($res));
        }
        $dir = '../data/upload/shop/avatar/';
        $aa = !empty($memberid)?$memberid:'AA';
        // $filename = $this->base64_upload($image,"resource/",$aa);
        $filename = $this->base64_upload($image,$dir,$aa);

        if($filename == false){
            $res = getarrres('-401','图片上传失败',0);
            exit(json_encode($res));
        }

        $condition['member_avatar'] = $filename;
        $rs = Model()->table("member")->where("member_id=$memberid")->update($condition);
        if($rs){
            $res = getarrres('200','success',1);
            exit(json_encode($res));
        }else{
            $res = getarrres('-401','failed',0);
            exit(json_encode($res));
        }
    }

    public function base64_upload($base64,$dir,$memberid) {

        $base64_image = str_replace(' ', '+', $base64);
        //post的数据里面，加号会被替换为空格，需要重新替换回来，如果不是post的数据，则注释掉这一行
        // if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)){
            //匹配成功
            // if($result[2] == 'jpeg'){
                $image_name = 'avatar_'.$memberid.'.jpg';
                //纯粹是看jpeg不爽才替换
            // }else{

            //     $image_name = 'avatar_'.intval($memberid).'.'.$result[2];
            // }
            $image_file = $dir.$image_name;
            //服务器文件存储路径

            if(file_put_contents($image_file, base64_decode($base64_image))){
                return $image_name;
            }else{
                return false;
            }
        // }else{
        //     return false;
        // }
    }

    /**
     * 登录
     * (post)username:用户名 password:密码
     */
    public function loginOp(){
        $client = isset($_POST['client'])?$_POST['client']:'iOS';
        $city = isset($_POST['city'])?$_POST['city']:'';
        $rid = isset($_POST['rid'])?$_POST['rid']:'';
        $user_name = $_POST['username'];
        $password = md5($_POST['password']);
        $map['member_mobile'] = $user_name;
        $map['member_passwd'] = $password;
        $map['is_master'] = 1;
        $member = Model('member')->table('member')->where($map)->find();
        if(empty($member)){
            $maps['member_name'] = $user_name;
            $maps['member_passwd'] = $password;
            $maps['is_master'] = 1;
            $member = Model('member')->table('member')->where($maps)->find();  
        }
        if(empty($member) || $member['member_id'] == '' || $member['member_name'] == ''){
            $res = getarrres('-401','账号或密码错误，请重新登录...',0);
            exit(json_encode($res));
        }
        $num = count($member);
        if($num > 0){
        	$token = $this->_get_token($member['member_id'], $member['member_name'], $client);
        	if($token){
                $TAG1 = "teacher";
                $TAG2 = $city;
                $ALIAS1 = "alias1".$member['member_id'];
                $REGISTRATION_ID1 = $rid;
                
                $logindata = array('username' => $member['member_name'], 'userid' => $member['member_id'], 'key' => $token,'bname'=>$ALIAS1,'tag'=>$TAG1,'tag2'=>$TAG2);

                $loginarr = getarrres('200','您已登陆成功，即将跳转...',$logindata);
                echo json_encode($loginarr);

                if($REGISTRATION_ID1 != ''){
                    ini_set("display_errors", "On");
                    error_reporting(E_ALL | E_STRICT);
                    require_once("../src/JPush/JPush.php");

                    $app_key = '48dac1cb88f87fb9561a33ff';
                    $master_secret = 'dbb71af71f2f49e355a70777';

                    // 初始化
                    $client = new JPush($app_key, $master_secret);

                    // 更新指定的设备的Alias(亦可以增加/删除Tags)
                    $tagre1 = $client->device()->isDeviceInTag($REGISTRATION_ID1, $TAG1);
                    if(!$tagre1){
                        $result = $client->device()->updateTag($TAG1, array($REGISTRATION_ID1));
                    }
                    $tagre2 = $client->device()->isDeviceInTag($REGISTRATION_ID1, $TAG2);
                    if(!$tagre2){
                        $result = $client->device()->updateTag($TAG2, array($REGISTRATION_ID1));
                    }
                    $result = $client->device()->updateDevice($REGISTRATION_ID1, $ALIAS1);
                }else{
                    $res = getarrres('-401','账号或密码错误，请重新登录...',0);
                    exit(json_encode($res));
                }
				exit();
            }else{
            	$res = getarrres('-401','账号或密码错误，请重新登录...',1);
				exit(json_encode($res));
            }
        }else{
        	$res = getarrres('-401','账号或密码错误，请重新登录...',0);
			exit(json_encode($res));
        }
    }

     /**
     * 登录生成token
     */
    private function _get_token($member_id, $member_name, $client) {
        $model_mb_user_token = Model('mb_user_token');

        //重新登录后以前的令牌失效
        //暂时停用
        $condition = array();
        $condition['member_id'] = $member_id;
        // $condition['client_type'] = $client;
        $model_mb_user_token->delMbUserToken($condition);

        //生成新的token
        $mb_user_token_info = array();
        $token = md5($member_name . strval(TIMESTAMP) . strval(rand(0,999999)));
        $mb_user_token_info['member_id'] = $member_id;
        $mb_user_token_info['member_name'] = $member_name;
        $mb_user_token_info['token'] = $token;
        $mb_user_token_info['login_time'] = TIMESTAMP;
        $mb_user_token_info['client_type'] = $client;

        $result = $model_mb_user_token->addMbUserToken($mb_user_token_info);

        if($result) {
            return $token;
        } else {
            return null;
        }
    }


	/**
     * 发送短信验证码
     * (post) phone:电话号码 type：短信类型:1为注册,2为登录,3为更改密码
     */
    public function phonenumOp(){
        $phone = $_POST['phone'];
        // if (strlen($phone) == 11){
            $log_type = $_POST['type'];//短信类型:1为注册,2为登录,3为找回密码
            $model_sms_log = Model('sms_log');
            $condition = array();
            $condition['log_ip'] = getIp();
            $condition['log_type'] = $log_type;
            $sms_log = $model_sms_log->getSmsInfo($condition);
            if(!empty($sms_log) && ($sms_log['add_time'] > TIMESTAMP-10)) {//同一IP十分钟内只能发一条短信
                $res = getarrres('-401','同一IP地址十分钟内，请勿多次获取动态码！',0);
                exit(json_encode($res));
            } else {
                $state = 'true';
                $log_array = array();
                $model_member = Model('member');
                $member = $model_member->getMemberInfo(array('member_mobile'=> $phone,'is_master'=>1));
                $captcha = rand(100000, 999999);
                $log_msg = '【'.C('site_name').'】您于'.date("Y-m-d");
                switch ($log_type) {
                    case '1':
                        if(C('sms_register') != 1) {
                            $res = getarrres('-401','系统没有开启手机注册功能',1);
                            exit(json_encode($res));
                        }
                        if(!empty($member)) {//检查手机号是否已被注册
                            $res = getarrres('-401','当前手机号已被注册，请更换其他号码。',2);
                            exit(json_encode($res));
                        }
                        $log_msg .= '申请注册会员，动态码：'.$captcha.'。';
                        break;
                    case '2':
                        if(C('sms_login') != 1) {
                            $res = getarrres('-401','系统没有开启手机登录功能',3);
                            exit(json_encode($res));
                        }
                        if(empty($member)) {//检查手机号是否已绑定会员
                            $res = getarrres('-401','当前手机号未注册，请检查号码是否正确。',4);
                            exit(json_encode($res));
                        }
                        $log_msg .= '申请登录，动态码：'.$captcha.'。';
                        $log_array['member_id'] = $member['member_id'];
                        $log_array['member_name'] = $member['member_name'];
                        break;
                    case '3':
                        if(empty($member)) {//检查手机号是否已绑定会员
                            $res = getarrres('-401','当前手机号未注册，请检查号码是否正确。',6);
                            exit(json_encode($res));
                        }
                        $log_msg .= '申请重置登录密码，动态码：'.$captcha.'。';
                        $log_array['member_id'] = $member['member_id'];
                        $log_array['member_name'] = $member['member_name'];
                        break;
                    case '4':
                        $log_msg .= '提交账户安全验证，验证码是：'.$captcha.'。';
                        $log_array['member_id'] = $member['member_id'];
                        $log_array['member_name'] = $member['member_name'];
                        break;
                    default:
                        $state = '参数错误';
                        $res = getarrres('-401','参数错误',7);
                        exit(json_encode($res));
                        break;
                }
                if($state == 'true'){
                    $sms = new Sms();
                    
                    $result = $sms->send($phone,$log_msg);
                    
                    
                    if($result){
                        $log_array['log_phone'] = $phone;
                        $log_array['log_captcha'] = $captcha;
                        $log_array['log_ip'] = getIp();
                        $log_array['log_msg'] = $log_msg;
                        $log_array['log_type'] = $log_type;
                        $log_array['add_time'] = time();
                        $model_sms_log->addSms($log_array);
                        $res = getarrres('200','发送成功',$captcha);
                        exit(json_encode($res));
                    } else {
                        $res = getarrres('-401','手机短信发送失败',8);
                        exit(json_encode($res));
                    }
                }
            }
        /*} else {
            $res = getarrres('-401','手机格式不对',0);
            exit(json_encode($res));
        }*/
    }


    /**
     * 更改密码
     * (post)key:令牌  phone:电话号码 captcha:动态码 password:新密码 repassword:确认新密码
     */
    public function chepwdOp(){
    	//验证动态码
    	// $key = $_POST['key'];
    	// $memberid = getmemberid($key);
    	$phone = $_POST['phone'];
        $captcha = $_POST['captcha'];
        $password = $_POST['password'];
        $repassword = $_POST['repassword'];
        if (strlen($phone) == 11){
            $condition = array();
            $condition['log_phone'] = $phone;
            $condition['log_captcha'] = $captcha;
            $model_sms_log = Model('sms_log');
            $sms_log = $model_sms_log->getSmsInfo($condition);
            if(empty($sms_log) || ($sms_log['add_time'] < TIMESTAMP-1800)) {//半小时内进行验证为有效
                $res = getarrres('-401','动态码错误或已过期，重新输入',0);
                exit(json_encode($res));
            }
            if($password != $repassword){
            	$res = getarrres('-401','两次输入密码不一致！',0);
                exit(json_encode($res));
            }
            $passwordss = md5($password);
            $map['member_mobile'] = $phone;
            $con['member_passwd'] = $passwordss;
            $result = Model()->table('member')->where($map)->update($con);
            if($result){
            	$res = getarrres('200','修改密码成功，即将跳转...',1);
            	exit(json_encode($res));
            }else{
            	$res = getarrres('-401','修改失败，请重新输入...',0);
                exit(json_encode($res));
            }
            
        }
        $res = getarrres('-401','验证失败',0);
        exit(json_encode($res));
    }

    /**
     * 验证注册动态码
     */
    public function check_sms_captchaOp(){
        $phone = $_POST['phone'];
        $captcha = $_POST['captcha'];
        if (strlen($phone) == 11){
            $condition = array();
            $condition['log_phone'] = $phone;
            $condition['log_captcha'] = $captcha;
            $model_sms_log = Model('sms_log');
            $sms_log = $model_sms_log->getSmsInfo($condition);
            //file_put_contents("aaa.txt",print_r($sms_log,true).'---'.date("Y-m-d H:i:s",time()).'---$_REQUEST---'.PHP_EOL, FILE_APPEND );
            if(empty($sms_log) || ($sms_log['add_time'] < TIMESTAMP-1800)) {//半小时内进行验证为有效
                $res = getarrres('-401','动态码错误或已过期，重新输入',0);
                exit(json_encode($res));
            }
            $res = getarrres('200','验证成功!请填写注册信息！',true);
            exit(json_encode($res));
        }
        $res = getarrres('-401','验证失败',0);
        exit(json_encode($res));
    }

    /**
     * 注册
     * (post)member_name:姓名 password:密码 repassword：确认密码 member_mobile：电话号 moneycard:银行卡号 member_qq:qq号
     */
    public function registeOp(){
        $map['member_name'] = $_POST['member_mobile'];
        $map['member_truename'] = $_POST['member_name'];
        $map['member_passwd'] = md5($_POST['password']);
        $repassword = md5($_POST['repassword']);
        if($_POST['repassword'] != $_POST['password']){
           $res = getarrres('-401','两次密码输入不一致',0);
            exit(json_encode($res)); 
        }
        $map['member_mobile'] = $_POST['member_mobile'];
        $map['moneycard'] = $_POST['moneycard'];
        $map['card'] = $_POST['card'];
        $map['member_qq'] = isset($_POST['member_qq'])?$_POST['member_qq']:'';
        $map['is_master'] = 1;
        $id = Model()->table("member")->insert($map);
        if($id){
            $res = getarrres('200','成功',array('id'=>$id,'member_name'=>$map['member_name'],'password'=>$map['member_passwd']));
        }else{
            $res = getarrres('-401','失败',0);
        }
        exit(json_encode($res));
    }

    /**
     * 申请提现
     * (post)key:令牌 money:金额
     */
    public function kitingOp(){
        $money = $_POST['money'];
        $key = $_POST['key'];
        $token = Model("mb_user_token");
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $memberid = $tokens['member_id'];
        $member_name = $tokens['member_name'];

        $mecon['member_id'] = $memberid;

        $smcost = Model()->table("setting")->where("name='smcost'")->find();
        $dj = floatval($smcost['value']);

        //推迟一周佣金金额
        $num = 0;
        $betime = time()-7*24*60*60;
        $condition['master_id'] = $memberid; 
        $condition['is_master'] = 1; 
        $condition['wxstate'] = 4; 
        $condition['finnshed_time'] = array('gt',$betime); 
        $order = Model()->table("order")->where($condition)->select();
        if(!empty($order)){
            foreach ($order as $key => $value) {
                $num = $num+$dj+floatval($order['scost']);
            }
        }
        $member = Model()->table("member")->where("member_id=$memberid")->find();
        $amount = floatval($member['amount'])-$num;
        if(floatval($money) < $amount){
            $map['money'] = $money;
            $map['member_id'] = $memberid;
            $umap['amount'] = floatval($member['amount'])-floatval($money);
            $rs = Model()->table("member")->where("member_id=$memberid")->update($umap);
            if($rs){
                include 'bankList.php';
                $card = $member['moneycard'];
                $str = '';
                $card_8 = substr($card, 0, 8); 
                if (isset($bankList[$card_8])) { 
                    $str = $bankList[$card_8]; 
                } 
                $card_6 = substr($card, 0, 6); 
                if (isset($bankList[$card_6])) { 
                    $str = $bankList[$card_6]; 
                } 
                $card_5 = substr($card, 0, 5); 
                if (isset($bankList[$card_5])) { 
                    $str = $bankList[$card_5]; 
                } 
                $card_4 = substr($card, 0, 4); 
                if (isset($bankList[$card_4])) { 
                    $str = $bankList[$card_4]; 
                } 
                $pdmap['pdc_member_id'] = $memberid;
                $pdmap['pdc_member_name'] = $member_name;
                $pdmap['pdc_amount'] = $money;
                $pdmap['pdc_add_time'] = time();
                $pdmap['pdc_bank_user'] = $member['member_truename'];
                $pdmap['pdc_bank_no'] = $member['moneycard'];
                $pdmap['pdc_bank_name'] = $str;
                $pdmap['pdc_sn'] = md5($memberid.round(0,99));
                $r = Model()->table("pd_cash")->insert($pdmap);
                $res = getarrres('200','提现成功，即将跳转...',1);
            }else{
                $res = getarrres('-401','提现失败',0);
            }
        }else{
            $res = getarrres('-401','可提现金额不足',0);
        }
        exit(json_encode($res));
    }


    /**
     * 个人信息
     * (post)key:令牌
     */
    public function myinfoOp(){
    	$key = $_POST['key'];
    	$memberid = getmemberid($key);
        $field = "member_name,member_avatar,moneycard,member_mobile,member_truename,amount";
    	$member = Model("member")->table("member")->field($field)->where("member_id=$memberid")->find();
		$member['member_avatar'] = getMemberAvatar($member['member_avatar']);

        $map['master_id'] = $memberid;
        $map['is_master'] = 1;
        $cost = Model()->table("order")->where($map)->select();
        $znum = 0;
        foreach ($cost as $k => $v) {
            $znum = $znum+floatval($cost['scost']);
        }
        // $num = intval(count($cost));
        // $smcost = Model()->table("setting")->where("name='smcost'")->find();
        // $dj = floatval($smcost['value']);
        // $member['amount'] = $num*$dj+$znum;
		$res = getarrres('200','成功',$member);
    	exit(json_encode($res));
    }

    /**
     * 编辑个人信息
     * (post)key:令牌 name:姓名 tel:电话号码 moneycard:银行卡 member_name:用户名
     */
    public function editmyinfoOp(){
        $member_truename = $_POST['member_name'];
        $moneycard = $_POST['moneycard'];
        $tel = $_POST['tel'];
        $key = $_POST['key'];
        $memberid = getmemberid($key);
        $map['member_mobile'] = $tel;
        $map['moneycard'] = $moneycard;
        $map['member_name'] = $member_truename;
        $result = Model()->table('member')->where("member_id=$memberid")->update($map);
        $res = getarrres('200','成功',1);
        exit(json_encode($res));
    }

    /**
     * 模式切换
     * (post)key:令牌 model:模式：接单为1 休息为0
     */
    public function chemodelOp(){
    	$model = $_POST['model'];
    	$key = $_POST['key'];
    	$memberid = getmemberid($key);
    	$result = Model("member")->table("member")->where("model=$model")->update("member_id=$memberid");
    	if($result){
    		$res = getarrres('200','成功',1);
    		exit(json_encode($res));
    	}
    }


    /* 
	 * 退出登录
	 * (post)传值：username:会员名  key:令牌 
	 * 成功返回1 不成功返回0
	 */
	public function logoutOp(){
		$username = $_POST['username'];
		$client  = $_POST['client'];
		$key  = $_POST['key'];
		$token = Model('mb_user_token');
		$data['member_name'] = $username;
		$data['token'] = $key;
		$token->where($data)->delete();
		$res = getarrres('200','成功',1);
		echo json_encode($res);  
		exit();
	}

    /* 
     * 取消订单
     * (post)传值：key:令牌 order_id:订单id
     */
    public function qxorderOp(){
        $order_id = $_POST['order_id'];
        $key = $_POST['key'];
        $token = Model("mb_user_token");
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $memberid = $tokens['member_id'];
        $member_truename = $tokens['member_truename'];
        $member_mobile = $tokens['member_mobile'];
        $order = Model()->table("order")->where("order_id=$order_id")->find();
        if($order['wxstate'] >= 2){
            $res = getarrres('200','师傅已出发，订单无法取消！',0);
            exit(json_encode($res));
        }
        $state = Model()->table('order')->where("order_id=$order_id")->delete();
        if($state){
            $res = getarrres('200','取消成功',1);
            echo json_encode($res);

            $regi = 'alias1'.$order['master_id'];

            ini_set("display_errors", "On");
            error_reporting(E_ALL | E_STRICT);
            require_once("../src/JPush/JPush.php");

            $app_key = '48dac1cb88f87fb9561a33ff';
            $master_secret = 'dbb71af71f2f49e355a70777';

            // 初始化
            $client = new JPush($app_key, $master_secret);

            $result = $client->push();
            $result->setPlatform(array('ios', 'android'));
            $result->addAlias($regi);
            // $result->addTag(array('teacher'));
            // $result->setNotificationAlert($content);
            $result->addAndroidNotification('订单已被取消', '办工师', 1, array("order_type"=>"del","order_id"=>$order_id));
            $result->addIosNotification("订单已被取消", 'iOS sound', JPush::DISABLE_BADGE, true, 'iOS category', array("order_type"=>"del","order_id"=>$order_id));
            $result->setMessage("msg content", 'msg title', 'type', array("order_type"=>"del","order_id"=>$order_id));
            $result->setOptions(100000, 3600, null, true);
            $result->send();

            exit();
        }else{
            $res = getarrres('-401','failed',0);
            exit(json_encode($res));
        }

    }

	/* 
	 * 接单
	 * (post)传值：key:令牌 order_id:订单id
	 */
	public function setorderOp(){
		$order_id = isset($_POST['order_id'])?$_POST['order_id']:'';
		$key = isset($_POST['key'])?$_POST['key']:'';
        if($key == ''){
            $res = getarrres('-401','令牌为空！',0);
            exit(json_encode($res)); 
        }
        $token = Model("mb_user_token");
    	$data['token'] = $key;
        $tokens = $token->where($data)->find();
        if(empty($tokens)){
            $res = getarrres('-401','登录过期，请重新登录！',0);
            exit(json_encode($res));
        }
        $memberid = $tokens['member_id'];
        if(empty($memberid)){
            $res = getarrres('-401','用户为空！',0);
            exit(json_encode($res));
        }
        $order = Model()->table("order")->where("order_id=$order_id")->find();
        $memberids = $order['buyer_id'];
        $memberss = Model()->table("member")->where("member_id=$memberids")->find();
        $member_truename = $memberss['member_truename'];
        $member_mobile = $memberss['member_mobile'];
        if(empty($order)){
            $res = getarrres('-401','订单不存在',1);
            exit(json_encode($res));
        }
        if($order['wxstate'] == 5){
            $res = getarrres('-401','用户已取消订单',1);
            exit(json_encode($res));
        }
        if($order['master_id'] > 0){
            $res = getarrres('-401','此单已被抢走',0);
            exit(json_encode($res));
        }
        $addtime = $order['add_time'];
        $map['jdtime'] = time();
        $map['wxstate'] = 1;
        $map['master_id'] = empty($memberid)?$member['member_id']:$memberid;
    	$orders = Model()->table('order')->where("order_id=$order_id")->update($map);
    	if($orders){
    		$res = getarrres('200','接单成功',$order_id);
            echo json_encode($res);

            if($order['apptime'] != ''){
                $yuyue = 1;
                $apptime = $order['apptime'];
            }else{
                $yuyue = 0;
                $apptime = "";
            }
            $regi = 'alias1'.$order['buyer_id'];
            $content = $_POST['content'];
            ini_set("display_errors", "On");
            error_reporting(E_ALL | E_STRICT);
            require_once("../src/JPush/JPush.php");

            $app_key = 'ae1d588ecf543c12249732df';
            $master_secret = '4517e2aab7e88df3e1c0411b';

            // 初始化
            $client = new JPush($app_key, $master_secret);

            $result = $client->push();
            $result->setPlatform(array('ios', 'android'));
            $result->addAlias($regi);
            // $result->addTag(array('users'));
            // $result->setNotificationAlert($content);
            $result->addAndroidNotification('师傅已接单!', '办工师', 1, array('order_id'=>$order_id,"member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'addtime'=>$addtime,'order_type'=>'receive','yuyue'=>$yuyue,'apptime'=>$apptime));
            $result->addIosNotification("师傅已接单!", 'iOS sound', JPush::DISABLE_BADGE, true, 'iOS category', array('order_id'=>$order_id,"member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'addtime'=>$addtime,'order_type'=>'receive','yuyue'=>$yuyue,'apptime'=>$apptime));
            $result->setMessage("msg content", 'msg title', 'type', array('order_id'=>$order_id,"member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'addtime'=>$addtime,'order_type'=>'receive','yuyue'=>$yuyue,'apptime'=>$apptime));
            $result->setOptions(100000, 3600, null, true);
            $result->send();
            // if($order['apptime'] > time()){
                if($order['apptime'] != ''){
                    $apptime = $order['apptime'];
                    $address = $order['address'];
                    //定时推送
                    $y = date('Y',time());
                    $m = date('m',time());
                    $d = date('d',time());
                    $apparr = explode('-', $apptime);
                    $str = $y.'-'.$m.'-';
                    if($apparr['0'] == '今天'){
                        $str .= intval($d);
                    }
                    if($apparr['0'] == '明天'){
                        $str .= intval($d)+1;
                    }
                    if($apparr['0'] == '后天'){
                        $str .= intval($d)+2;
                    }
                    $str .= ' '.intval($apparr['1']).':'.intval($apparr['2']);
                    $strtime = strtotime($str)-120;
                    $times = date('Y-m-d H:i:s',$strtime);
                    file_put_contents('aaa.txt', print_r($times,true));
                    $regis = 'alias1'.$memberid;

                    $app_key = '48dac1cb88f87fb9561a33ff';
                    $master_secret = 'dbb71af71f2f49e355a70777';

                    // 初始化
                    $client = new JPush($app_key, $master_secret);

                    $payload = $client->push()
                    ->setPlatform(array('ios', 'android'))
                    ->addAlias($regis)
                    // ->addTag(array('teacher'))
                    ->setNotificationAlert('请您尽快处理订单')
                    ->addAndroidNotification('请您尽快处理订单','办工师',1,array('time'=>time(),'type'=>2,'yuyue'=>2,'order_id'=>$order_id,'apptime'=>$apptime,"member_truename"=>$member_truename, "member_mobile"=>$member_mobile,"address"=>$address))
                    ->addIosNotification("请您尽快处理订单",'iOS sound',JPush::DISABLE_BADGE, true,'iOS category',array('time'=>time(),'type'=>2,'yuyue'=>2,'order_id'=>$order_id,'apptime'=>$apptime,"member_truename"=>$member_truename, "member_mobile"=>$member_mobile,"address"=>$address))
                    ->setMessage("请您尽快处理订单",'msg title','type',array('time'=>time(),'type'=>2,'yuyue'=>2,'order_id'=>$order_id,'apptime'=>$apptime,"member_truename"=>$member_truename, "member_mobile"=>$member_mobile,"address"=>$address))
                    // ->setOptions(100000, 3600, null, true)
                    ->build();  
                    $response = $client->schedule()->createSingleSchedule("请您尽快处理订单", $payload, array("time"=>$times));
                    $schedule_id = $response->data->schedule_id;
                    if($schedule_id != ''){
                        $map['schedule_id'] = $schedule_id;
                        Model()->table('order')->where("order_id=$order_id")->update($map);
                    }
                    
                }
            // }
			exit();
    	}else{
            $res = getarrres('-401','failed',$order_id);
            echo json_encode($res);
        }
	}

    /* 
     * 出发
     * (post)传值：key:令牌 order_id:订单id
     */
    public function cforderOp(){
        $order_id = $_POST['order_id'];
        $key = $_POST['key'];
        $token = Model("mb_user_token");
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $memberid = $tokens['member_id'];
        $member = Model()->table("member")->where("member_id=$memberid")->find();
        $member_truename = empty($member['member_truename'])?'':$member['member_truename'];
        $member_mobile = empty($member['member_mobile'])?'':$member['member_mobile'];
        $omap['order_id'] = $order_id;
        $order = Model()->table("order")->where($omap)->find();
        if(empty($order)){
            $res = getarrres('-401','订单不存在',1);
            exit(json_encode($res));
        }
        if($order['wxstate'] == 5){
            $res = getarrres('-401','用户已取消订单',1);
            exit(json_encode($res));
        }
        $map['cftime'] = time();
        $map['wxstate'] = 2;
        $map['master_id'] = $memberid;
        $result = Model("order")->table('order')->where($omap)->update($map);
        if($result){
            $res = getarrres('200','成功',1);
            echo json_encode($res);

            $regi = 'alias1'.$order['buyer_id'];
            $content = $_POST['content'];
            ini_set("display_errors", "On");
            error_reporting(E_ALL | E_STRICT);
            require_once("../src/JPush/JPush.php");

            $app_key = 'ae1d588ecf543c12249732df';
            $master_secret = '4517e2aab7e88df3e1c0411b';

            // 初始化
            $client = new JPush($app_key, $master_secret);

            $result = $client->push();
            $result->setPlatform(array('ios', 'android'));
            $result->addAlias($regi);
            // $result->addTag(array('users'));
            // $result->setNotificationAlert($content);
            $result->addAndroidNotification('师傅已出发!', '办工师', 1, array("member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'order_type'=>'setout'));
            $result->addIosNotification("师傅已出发!", 'iOS sound', JPush::DISABLE_BADGE, true, 'iOS category', array("member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'order_type'=>'setout'));
            $result->setMessage("msg content", '办工师', 'type', array("member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'order_type'=>'setout'));
            $result->setOptions(100000, 3600, null, true);
            $result->send();
            //删除定时推送
            if($order['schedule_id'] != ''){
                $schedule_id = $order['schedule_id'];

                // 初始化
                $client = new JPush($app_key, $master_secret);

                $client->schedule()->deleteSchedule($schedule_id);
                $map['schedule_id'] = '';
                Model()->table('order')->where("order_id=$order_id")->update($map);
            }
            exit();
        }else{
            $res = getarrres('-401','失败',1);
            exit(json_encode($res));
        }
    }

    /* 
     * 维修中
     * (post)传值：key:令牌 order_id:订单id
     */
    public function wxorderOp(){
        $order_id = $_POST['order_id'];
        $key = $_POST['key'];
        $token = Model("mb_user_token");
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $memberid = $tokens['member_id'];
        $member = Model()->table("member")->where("member_id=$memberid")->find();
        $member_truename = $member['member_truename'];
        $member_mobile = $member['member_mobile'];
        $order = Model()->table("order")->where("order_id=$order_id")->find();
        if(empty($order)){
            $res = getarrres('-401','订单不存在',1);
            exit(json_encode($res));
        }
         if($order['wxstate'] == 5){
            $res = getarrres('-401','用户已取消订单',1);
            exit(json_encode($res));
        }
        $map['wxtime'] = time();
        $map['wxstate'] = 3;
        $result = Model("order")->table('order')->where("order_id=$order_id")->update($map);
        if($result){
            $res = getarrres('200','success',$order_id);
            echo json_encode($res);

            $regi = 'alias1'.$order['buyer_id'];
            $content = $_POST['content'];
            ini_set("display_errors", "On");
            error_reporting(E_ALL | E_STRICT);
            require_once("../src/JPush/JPush.php");

            $app_key = 'ae1d588ecf543c12249732df';
            $master_secret = '4517e2aab7e88df3e1c0411b';

            // 初始化
            $client = new JPush($app_key, $master_secret);

            $result = $client->push();
            $result->setPlatform(array('ios', 'android'));
            $result->addAlias($regi);
            // $result->addTag(array('users'));
            // $result->setNotificationAlert($content);
            $result->addAndroidNotification('设备维修中!', '办工师', 1, array("member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'order_type'=>'start'));
            $result->addIosNotification("设备维修中!", 'iOS sound', JPush::DISABLE_BADGE, true, 'iOS category', array("member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'order_type'=>'start'));
            $result->setMessage("msg content", '办工师', 'type', array("member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'order_type'=>'start'));
            $result->setOptions(100000, 3600, null, true);
            $result->send();

            exit();
        }
    }

    /* 
     * 订单已完成
     * (post)传值：key:令牌 order_id:订单id
     */
    public function endorderOp(){
        $order_id = $_POST['order_id'];
        $key = $_POST['key'];
        $token = Model("mb_user_token");
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $memberid = $tokens['member_id'];
        $member = Model()->table("member")->where("member_id=$memberid")->find();
        $member_truename = $member['member_truename'];
        $member_mobile = $member['member_mobile'];
        $order = Model()->table("order")->where("order_id=$order_id")->find();
        if(empty($order)){
            $res = getarrres('-401','订单不存在',1);
            exit(json_encode($res));
        }
         if($order['wxstate'] == 5){
            $res = getarrres('-401','用户已取消订单',1);
            exit(json_encode($res));
        }
        if($order['wxstate'] == 4){
            $res = getarrres('-401','订单已完成',1);
            echo json_encode($res); 
        }
        $map['finnshed_time'] = time();
        $map['wxstate'] = 4;
        $map['master_id'] = $memberid;
        $smcost = Model()->table("setting")->where("name='smcost'")->find();
        $dj = floatval($smcost['value']);//上门费
        $num = floatval($order['scost']);//小费
        $upmap['amount'] = floatval($member['amount'])+$num+$dj;
        $upmap['available_predeposit'] = floatval($member['available_predeposit'])+$num+$dj;
        $con['order_id'] = $order_id;
        $memap['member_id'] = $memberid;
        $result = Model()->table("order")->where($con)->update($map);
        $member = Model()->table("member")->where($memap)->update($upmap);
        if($result){
            $res = getarrres('200','维修完成',1);
            echo json_encode($res);

            $regi = 'alias1'.$order['buyer_id'];
            $content = $_POST['content'];
            ini_set("display_errors", "On");
            error_reporting(E_ALL | E_STRICT);
            require_once("../src/JPush/JPush.php");

            $app_key = 'ae1d588ecf543c12249732df';
            $master_secret = '4517e2aab7e88df3e1c0411b';

            // 初始化
            $client = new JPush($app_key, $master_secret);

            $result = $client->push();
            $result->setPlatform(array('ios', 'android'));
            $result->addAlias($regi);
            // $result->addTag(array('users'));
            // $result->setNotificationAlert($content);
            $result->addAndroidNotification('完成!', '办工师', 1, array("member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'order_type'=>'end'));
            $result->addIosNotification("完成!", 'iOS sound', JPush::DISABLE_BADGE, true, 'iOS category', array("member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'order_type'=>'end'));
            $result->setMessage("msg content", 'msg title', 'type', array("member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'order_type'=>'end'));
            $result->setOptions(100000, 3600, null, true);
            $result->send();

            exit();
        }else{
           $res = getarrres('-401','失败',1);
            echo json_encode($res); 
        }
    }


	/* 
	 * 接单列表查询
	 * (post)传值：key:令牌
	 */
	public function orderlistOp(){
		$key = $_POST['key'];
    	$memberid = getmemberid($key);
        if($memberid == ''){
            $res = getarrres('-401','登录过期，请重新登录！',$order);
            exit(json_encode($res));
        }
        $field = "order_id,order_sn,pay_sn,buyer_name,buyer_id,add_time,master_id,scost,tel,type,address,nowtime,apptime,wxstate,jdtime,cftime,wxtime,finnshed_time,order_amount";
    	$orderlist = Model('order')->table("order")->field($field)->where("master_id=$memberid")->order("order_id desc")->select();
        if(empty($orderlist)){
            $res = getarrres('-401','师傅当前未接单！',$order);
            exit(json_encode($res));
        }
        foreach ($orderlist as $key => $value) {
            if(empty($value['nowtime']) && !empty($value['apptime'])){
                $orderlist[$key]['ddtype'] = '预约订单';
                $orderlist[$key]['addtime'] = $value['apptime'];
            }else{
                $orderlist[$key]['ddtype'] = '普通订单';
                $orderlist[$key]['addtime'] = date('Y-m-d H:i:s',$value['nowtime']);
            }
        }
        $map['member_id'] = $memberid;
        $map['is_master'] = 1;
        $cost = Model()->table("member")->field("amount")->where($map)->find();

        // $smcost = Model()->table("setting")->where("name='smcost'")->find();
        // $dj = floatval($smcost['value']);
        $order['amount'] = $cost['amount'];
        foreach ($orderlist as $key => $value) {
            $orderlist[$key]['amount'] = floatval($value['order_amount']);
        }

        $order['list'] = $orderlist;
    	$res = getarrres('200','查询成功',$order);
		exit(json_encode($res));
	}

    /* 
     * 个人中心：订单详情
     * (post)传值：key:令牌 order_id:订单id
     */
    public function orderinfoOp(){
        $order_id = $_POST['order_id'];
        $key = $_POST['key'];
        $memberid = getmemberid($key);  
        if($memberid == ''){
            $res = getarrres('-401','登录过期，请重新登录！',$memberid);
            exit(json_encode($res));
        }

        $map['master_id'] = $memberid;
        $map['order_id']  = $order_id;
        $map['wxstate']   = array('neq',5);
        $orderinfo = Model()->table("order")->field('order_id,order_sn,pay_sn,add_time,buyer_id,buyer_name,payment_code,nowtime,apptime,finnshed_time,master_id,is_master,scost,tel,type,address,wxstate,jdtime,wxtime,cftime,city,order_amount')->where($map)->find();
        if(empty($orderinfo)){
            $res = getarrres('-401','订单不存在，或已取消！',$order_id);
            exit(json_encode($res));
        }
        $mem['member_id'] = $memberid;
        $member = Model()->table("member")->field("member_avatar,member_truename")->where($mem)->find();
        $orderinfo['member_avatar'] = getMemberAvatar($member['member_avatar']);

        // $maps['name'] = 'smcost';
        // $cost = Model()->table("setting")->where($maps)->find();
        $amount = $orderinfo['order_amount'];//价钱

        $orderinfo['amount'] = $amount;
         if($orderinfo['payment_code'] == 'online'){
            $orderinfo['payment_code'] = '线上付款';
        }else{
            $orderinfo['payment_code'] = '货到付款';
        }
        if(empty($orderinfo['nowtime']) && !empty($orderinfo['apptime'])){
            $orderinfo['ddtype'] = '预约订单';
            $orderinfo['addtime'] = $orderinfo['apptime'];
        }else{
            $orderinfo['ddtype'] = '普通订单';
            $orderinfo['addtime'] = date('Y-m-d H:i:s',$orderinfo['nowtime']);
        }
        $res = getarrres('200','查询成功',$orderinfo);
        exit(json_encode($res));
    }


}
