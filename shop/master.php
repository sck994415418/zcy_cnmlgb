<?php
/**
 * 商城api
 * autor:LEE
 * 2016.6.27
 */
defined('InShopNC') or exit('Access Invalid!');
class masterControl extends BaseHomeControl{

    /**
     * 上传头像
     */
    public function imageOp(){
        $key = $_POST['key'];
        $token = Model("mb_user_token");
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $memberid = $tokens['member_id'];

        $filename = 'avatar_'.intval($memberid).'.jpg';
        // $xmlstr = $_POST['image'];
        $xmlstr = $GLOBALS['HTTP_RAW_POST_DATA'];

        if(empty($xmlstr)){
            $xmlstr = file_get_contents('php://input')?file_get_contents('php://input'):gzuncompress($GLOBALS ['HTTP_RAW_POST_DATA']);
        }

        $str = explode('=', $xmlstr);
        $xmlstr = $str[2];
        
        file_put_contents('aaa.txt',"<?php echo $xmlstr; ?>-----".date('Y-m-d H:i:s',time()));

        if(!$xmlstr){
            $res = getarrres('-401','没有接收到数据流',0);
            exit(json_encode($res));
        }

        $file = fopen(UPLOAD_SITE_URL.DS.ATTACH_AVATAR.DS,"w");//打开文件准备写入
        fwrite($file,$xmlstr);//写入
        fclose($file);//关闭

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

    /**
     * 登录
     * (post)username:用户名 password:密码
     */
    public function loginOp(){
        $user_name = $_POST['username'];
        $password = md5($_POST['password']);
        $map['member_name'] = $user_name;
        $map['member_passwd'] = $password;
        $map['is_master'] = 1;
        $member = Model('member')->table('member')->where($map)->find();
        $num = count($member);
        if($num > 0){
        	$token = $this->_get_token($member['member_id'], $member['member_name'], 'app');
        	if($token) {
                ini_set("display_errors", "On");
                error_reporting(E_ALL | E_STRICT);
                require_once("../src/JPush/JPush.php");

                $app_key = '48dac1cb88f87fb9561a33ff';
                $master_secret = 'dbb71af71f2f49e355a70777';

                $TAG1 = "teacher";
                $ALIAS1 = "alias1".$member['member_id'];
                $REGISTRATION_ID1 = $_POST['rid'];

                // 初始化
                $client = new JPush($app_key, $master_secret);

                // 更新指定的设备的Alias(亦可以增加/删除Tags)
                $result = $client->device()->updateTag($TAG1, array($REGISTRATION_ID1));
                $result = $client->device()->updateDevice($REGISTRATION_ID1, $ALIAS1);

				$logindata = array('username' => $member['member_name'], 'userid' => $member['member_id'], 'key' => $token,'bname'=>$ALIAS1,'tag'=>$TAG1);
				$_SESSION['wap_member_info'] = $logindata;
                $array = getarrres('200','您已登陆成功，即将跳转...',$logindata);
				exit(json_encode($array));
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
     * QQ登录
     */
    public function qq_loginOp()
	{
		require_once(BASE_PATH.DS.'api'.DS.'qq'.DS.'comm'.DS."config.php");
	    $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
	    $login_url = "https://graph.z.com/oauth2.0/authorize?response_type=code&client_id=" 
	        . $_SESSION["appid"] . "&redirect_uri=" . urlencode($_SESSION["callback"])
	        . "&state=" . $_SESSION['state']
	        . "&scope=".$_SESSION["scope"];
	    header("Location:$login_url");
	}

	/**
     * 发送短信验证码
     * (post) phone:电话号码 type：短信类型:1为注册,2为登录,3为更改密码
     */
    public function phonenumOp(){
        $phone = $_POST['phone'];
        if (strlen($phone) == 11){
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
                $member = $model_member->getMemberInfo(array('member_mobile'=> $phone));
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
        } else {
            $res = getarrres('-401','手机格式不对',0);
            exit(json_encode($res));
        }
    }


    /**
     * 更改密码//
     * (post)key:令牌  phone:电话号码 captcha:动态码 password:新密码 repassword:确认新密码
     */
    public function chepwdOp(){
    	//验证动态码
    	$key = $_POST['key'];
    	$memberid = getmemberid($key);
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
            $password = md5($password);
            $result = Model('member')->table('member')->where()->update("member_passwd=$password");
            if($result){
            	$res = getarrres('200','修改密码成功，即将跳转...',true);
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
            if(empty($sms_log) || ($sms_log['add_time'] < TIMESTAMP-1800)) {//半小时内进行验证为有效
                $res = getarrres('-401','动态码错误或已过期，重新输入',0);
                exit(json_encode($res));
            }
            $res = getarrres('200','success',true);
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
        $map['member_name'] = 'phone_'.$_POST['member_mobile'];
        $map['member_truename'] = $_POST['member_name'];
        $map['password'] = $_POST['password'];
        $map['repassword'] = $_POST['repassword'];
        $map['member_mobile'] = $_POST['member_mobile'];
        $map['moneycard'] = $_POST['moneycard'];
        $map['member_qq'] = $_POST['member_qq'];
        $map['is_master'] = 1;
        $id = Model()->table("member")->insert($map);
        $res = getarrres('200','成功',$id);
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
        if($money < $tokens['amount']){
            $memberid = $tokens['member_id'];
            $map['money'] = $money;
            $map['member_id'] = $memberid;
            $result = Model()->table('kit')->insert($map); 
            $res = getarrres('200','提现成功，即将跳转...',1);
        }else{
            $res = getarrres('-401','余额不足',0);
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
        $field = "member_name,member_avatar,moneycard,member_mobile";
    	$member = Model("member")->table("member")->field($field)->where("member_id=$memberid")->find();
    	foreach ($member as $k => $v) {
    		$member['member_avatar'] = getMemberAvatar($v['member_avatar']);
    	}
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
        $state = Model("order")->table('order')->where("order_id=$order_id")->delete();
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
            $result->addAndroidNotification('订单已被取消', 'notification title', 1, array("order_type"=>"del","order_id"=>$order_id));
            $result->addIosNotification("订单已被取消", 'iOS sound', JPush::DISABLE_BADGE, true, 'iOS category', array("order_type"=>"del","order_id"=>$order_id));
            $result->setMessage("msg content", 'msg title', 'type', array("order_type"=>"del","order_id"=>$order_id));
            $result->setOptions(100000, 3600, null, false);
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
        $addtime = $order['add_time'];
        $map['jdtime'] = time();
        $map['wxstate'] = 1;
        $map['master_id'] = $memberid;
    	$orders = Model("order")->table('order')->where("order_id=$order_id")->update($map);
    	if($orders){
    		$res = getarrres('200','接单成功',$order_id);
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
            $result->addAndroidNotification('师傅已接单!', 'notification title', 1, array('order_id'=>$order_id,"member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'addtime'=>$addtime,'order_type'=>'receive'));
            $result->addIosNotification("师傅已接单!", 'iOS sound', JPush::DISABLE_BADGE, true, 'iOS category', array('order_id'=>$order_id,"member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'addtime'=>$addtime,'order_type'=>'receive'));
            $result->setMessage("msg content", 'msg title', 'type', array('order_id'=>$order_id,"member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'addtime'=>$addtime,'order_type'=>'receive'));
            $result->setOptions(100000, 3600, null, false);
            $result->send();

			exit();
    	}else{
            $res = getarrres('-401','failed',0);
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
        $member_truename = $tokens['member_truename'];
        $member_mobile = $tokens['member_mobile'];
        $order = Model()->table("order")->where("order_id=$order_id")->find();
        $map['cftime'] = time();
        $map['wxstate'] = 2;
        $map['master_id'] = $memberid;
        $result = Model("order")->table('order')->where("order_id=$order_id")->update($map);
        if($result){
            $res = getarrres('200','success',1);
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
            $result->addAndroidNotification('师傅已出发!', 'notification title', 1, array("member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'order_type'=>'setout'));
            $result->addIosNotification("师傅已出发!", 'iOS sound', JPush::DISABLE_BADGE, true, 'iOS category', array("member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'order_type'=>'setout'));
            $result->setMessage("msg content", 'msg title', 'type', array("member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'order_type'=>'setout'));
            $result->setOptions(100000, 3600, null, false);
            $result->send();

            exit();
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
        $member_truename = $tokens['member_truename'];
        $member_mobile = $tokens['member_mobile'];
        $order = Model()->table("order")->where("order_id=$order_id")->find();
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
            $result->addAndroidNotification('设备维修中!', 'notification title', 1, array("member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'order_type'=>'start'));
            $result->addIosNotification("设备维修中!", 'iOS sound', JPush::DISABLE_BADGE, true, 'iOS category', array("member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'order_type'=>'start'));
            $result->setMessage("msg content", 'msg title', 'type', array("member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'order_type'=>'start'));
            $result->setOptions(100000, 3600, null, false);
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
        $member_truename = $tokens['member_truename'];
        $member_mobile = $tokens['member_mobile'];
        $order = Model()->table("order")->where("order_id=$order_id")->find();
        $map['finnshed_time'] = time();
        $map['wxstate'] = 4;
        $map['master_id'] = $memberid;
        $result = Model("order")->table('order')->where("order_id=$order_id")->update($map);
        if($result){
            $res = getarrres('200','success',1);
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
            $result->addAndroidNotification('完成!', 'notification title', 1, array("member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'order_type'=>'end'));
            $result->addIosNotification("完成!", 'iOS sound', JPush::DISABLE_BADGE, true, 'iOS category', array("member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'order_type'=>'end'));
            $result->setMessage("msg content", 'msg title', 'type', array("member_truename"=>$member_truename, "member_mobile"=>$member_mobile,'time'=>time(),'order_type'=>'end'));
            $result->setOptions(100000, 3600, null, false);
            $result->send();

            exit();
        }
    }


	/* 
	 * 接单列表查询
	 * (post)传值：key:令牌
	 */
	public function orderlistOp(){
		$key = $_POST['key'];
    	$memberid = getmemberid($key);
    	$orderlist = Model('order')->table("order")->where("master_id=$memberid")->select();
        $amount = 0;
        foreach ($orderlist as $k => $v) {
            $amount += $v['order_amount'];
        }
        $order['amount'] = $amount;
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
        $orderinfo = Model("order")->table("order")->where("master_id=$memberid,order_id=$order_id")->select();
        foreach ($orderinfo as $key => $value) {
            if($value['payment_code'] == 'online'){
                $orderinfo[$key]['payment_code'] = '线上付款';
            }else{
                $orderinfo[$key]['payment_code'] = '货到付款';
            }
        }
        $res = getarrres('200','查询成功',$order);
        exit(json_encode($res));
    }


}
