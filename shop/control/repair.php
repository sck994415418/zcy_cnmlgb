<?php
/**
 * 维修租赁用户端
 * autor:LEE
 * 2016.6.27
 */
defined('InShopNC') or exit('Access Invalid!');
class repairControl extends BaseHomeControl{

    //维修详情
    public function wxinfoOp(){
        $order_id   = intval($_POST['order_id']);
        $key  = $_POST['key'];
        $memberid = getmemberid($key);
        // $wxmap['buyer_id'] = $memberid;
        $wxmap['is_master'] = '1';
        $wxmap['order_id'] = $order_id;
        $field = "order_id,master_id,wxstate,jdtime,cftime,wxtime,finnshed_time,add_time,evaluation_state";
        $wxorder = Model()->table("order")->field($field)->where($wxmap)->find();
        $master_id = $wxorder['master_id'];
        $wxorder['add_state'] = "下单";
        
        $memap['member_id'] = $master_id;
        $fields = "member_id,member_truename,member_name,member_avatar,member_mobile";
        $member = Model()->table("member")->field($fields)->where($memap)->find();
        $member['member_avatar'] = UPLOAD_SITE_URL.'/'.ATTACH_AVATAR.'/'.$member['member_avatar'];

        $wxorder['jd_state'] = $member["member_truename"].'已接单，'.$member['member_mobile'];
        $wxorder['cf_state'] = "维修师傅已出发";
        $wxorder['wx_state'] = "设备维修中";
        $wxorder['finnshed_state'] = "订单完成";

        $list['member'] = $member;
        $list['order'] = $wxorder;
        $res = getarrres('200','成功',$list);
        exit(json_encode($res));
    }

    //用户租赁列表
    public function userzlOp(){
        $key = $_POST['key'];
        $memberid = getmemberid($key);
        if(empty($memberid)){
            $res = getarrres('-401','fail',0);
            exit(json_encode($res));
        }
        $field = "rorder_id,buyer_name,buyer_id,add_time,goods_id";
        $map['buyer_id'] = $memberid;
        $list = Model()->table("rent_order")->field($field)->where($map)->select();
        foreach ($list as $k => $v) {
            $goodsid = $v['goods_id'];
            $goods = Model()->table("goods_rent")->where("goods_id = $goodsid")->find();
            $list[$k]['goods_image'] = cthumb($goods['goods_image'], 360, $goods['store_id']);
            $list[$k]['goods_name'] = $goods['goods_name'];
            $list[$k]['goods_jingle'] = $goods['goods_jingle'];
            $store_id = $goods['store_id'];
            $store = Model()->table("store")->where("store_id = $store_id")->find();
            $list[$k]['store_phone'] = $store['store_phone'];
        }
        $res = getarrres('200','success',$list);
        exit(json_encode($res));
    }

    //用户维修列表
    public function userwxOp(){
        $key = $_POST['key'];
        $memberid = getmemberid($key);
        if(empty($memberid)){
            $res = getarrres('-401','fail',0);
            exit(json_encode($res));
        }
        $field = "order_id,order_sn,order_state,pay_sn,buyer_name,buyer_id,add_time,master_id,scost,tel,type,address,nowtime,apptime,wxstate,jdtime,cftime,wxtime,finnshed_time,payment_code";
        $map['buyer_id'] = $memberid;
        $map['is_master'] = 1;
        $list = Model()->table("order")->field($field)->where($map)->order("add_time desc")->select();
        $smcost = Model()->table("setting")->where("name='smcost'")->find();
        $dj = floatval($smcost['value']);
        foreach ($list as $k => $v) {
            $master = $v['master_id'];
            $sf = Model()->table("member")->field("member_name,member_mobile")->where("member_id=$master")->find();
            $list[$k]['master_name'] = $sf['member_name'];
            $list[$k]['master_mobile'] = $sf['member_mobile'];
            if($v['order_state'] <= 10){
                $list[$k]['order_state'] = '未付款';
            }else{
                $list[$k]['order_state'] = '已付款';
            }
            if($v['wxstate'] == 0){
                $list[$k]['state'] = '未接单';
            }
            if($v['wxstate'] == 1){
                $list[$k]['state'] = '接单';
            }
            if($v['wxstate'] == 2){
                $list[$k]['state'] = '出发';
            }
            if($v['wxstate'] == 3){
                $list[$k]['state'] = '维修中';
            }
            if($v['wxstate'] == 4){
                $list[$k]['state'] = '已完成';
            }
            $list[$k]['wxdcost'] = $dj;
        }
        $res = getarrres('200','查询成功',$list);
        exit(json_encode($res));
    }

    /* 
     * 品牌
     * (post)传值：recommend:查询推荐传参 值为1
     */
    public function brandOp(){
        $recommend = $_POST['recommend'];
        $map['brand_recommend'] = array('in','0,1');
        if($recommend != ''){
            $map['brand_recommend'] = $recommend;
        }
        $brand = Model()->table("brand")->where($map)->select();
        foreach ($brand as $key => $value) {
            $brand[$key]['brand_pic'] = 'http://www.nrwspt.com/data/upload'.DS.ATTACH_BRAND.DS.$value['brand_pic'];
        }
        $res = getarrres('200','查询成功',$brand);
        exit(json_encode($res));
    }

    /* 
     * 商品
     * (post)传值：brand_id:品牌id hoot:热租设备 num:热租设备显示数量 goods_id:商品id
     */
    public function goodsOp(){
        $brand_id = $_POST['brand_id'];
        $hoot = $_POST['hoot'];
        $goods_id = $_POST['goods_id'];
        $where['goods_state'] = 1;
        $where['goods_verify'] = 1;
        if($brand_id != ''){
            $where['brand_id'] = $brand_id;
        }
        if($goods_id != ''){
            $where['goods_id'] = $goods_id;
            $goods = Model()->table('goods_rent')->where($where)->find();
            $goods['goods_image'] = cthumb($goods['goods_image'], 360, $goods['store_id']);
            $brand_id = $goods['brand_id'];
            $where['brand_id'] = $brand_id;
            $brand = Model()->table("brand")->field("brand_name")->where($where)->find();
            $goods['brand_name'] = $brand['brand_name'];
        }else{
            $num = isset($_POST['num'])?$_POST['num']:8;
            if($hoot != '' && $brand_id == ''){
                $goods = Model()->table('goods_rent')->where($where)->order("goods_salenum desc")->limit($num)->select();
            }
            if($hoot == ''){
                $goods = Model()->table('goods_rent')->where($where)->select();
            }
            if($hoot != ''){
                $goods = Model()->table('goods_rent')->where($where)->order("goods_salenum desc")->limit($num)->select();
            }
            if($brand_id == '' && $hoot == ''){
                $goods = Model()->table('goods_rent')->where($where)->select();
            }
            foreach ($goods as $key => $value) {
                $goods[$key]['goods_image'] = cthumb($value['goods_image'], 360, $value['store_id']);
            }
            foreach ($goods as $k => $v) {
                $brand_id = $v['brand_id'];
                $brand = Model()->table("brand")->field("brand_name")->where("brand_id=$brand_id")->find();
                $goods[$k]['brand_name'] = $brand['brand_name'];
            }
        }
        $res = getarrres('200','查询成功',$goods);
        exit(json_encode($res));
    }

    /* 
     * 提价租赁申请
     * (post)传值：key:令牌 goods_id:设备id name:姓名 tel:电话 model:型号 other:备注
     */
    public function rentinfoOp(){
        $key = $_POST['key'];
        $memberid = getmemberid($key);
        $map['buyer_id']     = $memberid;
        $map['goods_id']     = $_POST['goods_id'];
        $map['buyer_name']   = $_POST['name'];
        $map['buyer_phone']  = $_POST['tel'];
        $map['add_time']     = time();
        $map['model']        = $_POST['model'];
        $map['other']        = isset($_POST['other'])?$_POST['other']:'';
        $map['buyer_address'] = $_POST['buyer_address'];
        $result = Model("rent_order")->table("rent_order")->insert($map);
        if($result){
            $res = getarrres('200','订单已提交',1);
            exit(json_encode($res));
        }
    }

    public function doorcostOp(){
        $map['name'] = 'smcost';
        $cost = Model()->table("setting")->where($map)->find();
        $res = getarrres('200','success',intval($cost['value']));
        exit(json_encode($res));
    }

    /* 
     * 提交维修订单
     * (post)传值：key:令牌 nowtime:现在预约 apptime：预约维修时间 address:我的位置 type:物品类型 tel:联系电话 scost:小费
     */
    public function setorderOp(){
        $key = $_POST['key'];
        $city = isset($_POST['city'])?$_POST['city']:'';
        $token = Model('mb_user_token');
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $memberid = $tokens['member_id'];
        $member_name = $tokens['member_name'];
        $map['buyer_id']     = $memberid;
        $map['buyer_name']   = $member_name;
        $pay_sn = $this->makePaySn($memberid);
        $map['pay_sn'] = $pay_sn;
        $order_pay['pay_sn'] = $pay_sn;
        $order_pay['buyer_id'] = $memberid;
        $pay_id = Model('order_pay')->table('order_pay')->insert($order_pay);
        $map['order_sn'] = $this->makeOrderSn($pay_id);
        $apptime = isset($_POST['apptime'])?$_POST['apptime']:'';
        if($apptime == ''){
            $map['nowtime']  = $_POST['nowtime'];
        }else{
            $map['apptime']  = $_POST['apptime'];
        }
        $maps['name'] = 'smcost';
        $cost = Model()->table("setting")->where($maps)->find();
        $amount = floatval($cost['value'])+floatval($_POST['scost']);//价钱
        
        $map['city']         = $city;
        $map['add_time']     = time();
        $map['address']      = $_POST['address'];
        $map['type']         = $_POST['type'];
        $map['tel']          = $_POST['tel'];
        $map['scost']        = $_POST['scost'];
        $map['order_amount'] = $amount;
        $map['wxstate']      = 0;
        $map['is_master']    = 1;
        $order_id = Model()->table("order")->insert($map);
        $id = Model()->table("order")->getLastID();
        if($order_id){
            $wxstr = $this->get_wx_paystr($pay_sn,$amount);
            $zfbstr = $this->get_paystr($pay_sn,$amount);
            $wstr = base64_decode($wxstr);
            $warr['wx'] = json_decode($wstr,true);
            $warr['zfb'] = base64_decode($zfbstr);
            $warr['order_id'] = $id;
            $res = getarrres('200','订单已提交',$warr);
            echo json_encode($res);
            exit();
        }else{
            $res = getarrres('-401','failed',0);
            exit(json_encode($res));
        }
    }


    //获取支付宝支付字符串
    public function get_paystr($pay_sn,$amount){
        require_once './api1/alipay/lib/alipay_core.function.php';
        require_once './api1/alipay/lib/alipay_rsa.function.php';
        $private_key_path = 'http://www.nrwspt.com/shop/api1/alipay/key/pkcs.txt';
        $params = array();

        $params['service'] = 'mobile.securitypay.pay';
        $params['partner'] = '2088221492686499';
        $params['notify_url'] = 'http://www.nrwspt.com/shop/api1/alipay/notify_url.php';
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
        return base64_encode($a);
    }
     
    //获取微信支付字符串    
    public function get_wx_paystr($pay_sn,$amount){
        $return_url = 'http://www.nrwspt.com/shop/api1/weixin/respond.php';
        define ( APPID, 'wx389228fd0b48b35b' ); // appid
        define ( APPSECRET, '916b8eee1fb8961319276607013d66c8' ); // appSecret
        define ( MCHID, '1370739702' );
        define ( KEY, 'epOKnJrHoCZtVXZyNKhuLYtu2yQ4g3mc'); // 通加密串
        define ( NOTIFY_URL, $return_url ); // 成功回调url

        include_once ("./api1/weixin/WxPayPubHelper.php");  

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
        return base64_encode(json_encode($a));
    }

    //确认付款推送 
    public function compayOp(){
        $order_id = isset($_POST['order_id'])?$_POST['order_id']:'';
        if($order_id != ''){
            $omap['order_id'] = $order_id;
            $map['payment_code'] = 'online';
            $rs = Model()->table("order")->where($omap)->update($map);
            $order = Model()->table("order")->where($omap)->find();
            if($rs){
                $res = getarrres('200','付款信息已提交',1);
            }else{
                $res = getarrres('-401','提交失败',1);
                exit(json_encode($res)); 
            }
        }else{
            $res = getarrres('-401','订单号为空！',0);
        }
        echo json_encode($res);
        exit();
    }

    /**
     * 生成支付单编号(两位随机 + 从2000-01-01 00:00:00 到现在的秒数+微秒+会员ID%1000)，该值会传给第三方支付接口
     * 长度 =2位 + 10位 + 3位 + 3位  = 18位
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @return string
     */
    public function makePaySn($member_id) {
        return mt_rand(10,99)
        . sprintf('%010d',time() - 946656000)
        . sprintf('%03d', (float) microtime() * 1000)
        . sprintf('%03d', (int) $member_id % 1000);
    }

    /**
     * 订单编号生成规则，n(n>=1)个订单表对应一个支付表，
     * 生成订单编号(年取1位 + $pay_id取13位 + 第N个子订单取2位)
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @param $pay_id 支付表自增ID
     * @return string
     */
    public function makeOrderSn($pay_id) {
        //记录生成子订单的个数，如果生成多个子订单，该值会累加
        static $num;
        if (empty($num)) {
            $num = 1;
        } else {
            $num ++;
        }
        return (date('y',time()) % 9+1) . sprintf('%013d', $pay_id) . sprintf('%02d', $num);
    }

    /* 
     * 查询维修订单
     * (post)传值：key:令牌  order_id:订单id
     */
    public function getorderOp(){
        $order_id = $_POST['order_id'];
        $key = $_POST['key'];
        $memberid = getmemberid($key);
        if($memberid == ''){
            $res = getarrres('-401','登录过期，请重新登录！',$order);
            exit(json_encode($res));
        }
        $field = "add_time,buyer_id,master_id,is_master,scost,tel,type,address,nowtime,apptime,wxstate,order_state,jdtime,cftime,wxtime,finnshed_time";
        $order = Model()->table('order')->where("order_id=$order_id")->field($field)->find();
        if($order['wxstate'] != 0){
            $master_id = $order_id['master_id'];
            $member = Model()->table('member')->where("member_id=$master_id")->field("member_id,member_name,member_truename,member_mobile,is_master,member_avatar")->find();
            $member['member_avatar'] = getMemberAvatar($member['member_avatar']);
        }
        $arr['order'] = $order;
        $arr['member'] = $member;
        $res = getarrres('200','订单信息',$arr);
        exit(json_encode($res));
    }

    /* 
     * 提交维修评价
     * (post)传值：key:令牌  order_id:订单id content:评价内容 level：评价等级
     */
    public function estimateOp(){
        $key = $_POST['key'];//令牌
        $token = Model('mb_user_token');
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $username = $tokens['member_name'];
        $memberid = $tokens['member_id'];
        $order_id = $_POST['order_id'];//订单id
        $condition['geval_orderid'] = $order_id;
        $condition['geval_scores'] = $_POST['level'];//星级
        $condition['geval_content'] = $_POST['content'];//评价内容
        $map['order_id'] = $order_id;
        $order = Model()->table('order')->where($map)->find();
        
        if($order['evaluation_state'] != 0){
            $res = getarrres('200','订单已评价或者已过期',1);
            exit(json_encode($res));
        }

        $store_id = $order['store_id'];
        $condition['geval_storeid'] = $store_id;
        $condition['geval_storename'] = $order['store_name'];
        $condition['geval_orderno'] = $order['order_sn'];
        $condition['geval_goodsprice'] = $order['goods_price'];
        $condition['geval_storename'] = $order['store_name'];
        $condition['geval_goodsimage'] = $order['goods_image'];
        // $condition['geval_isanonymous'] = $_POST['geval_isanonymous'];//是否匿名评价
        $condition['geval_addtime'] = time();
        $condition['geval_frommemberid'] = $memberid;
        $condition['geval_frommembername'] = $username;

        $rs = Model()->table("evaluate_goods")->insert($condition);

        $omap['order_id'] = $_POST['order_id'];
        $umap['evaluation_state'] = 1;
        Model()->table('order')->where($omap)->update($umap);
        if($rs){
            $res = getarrres('200','评价成功',1);
        }else{
            $res = getarrres('-401','评价失败',0);
        }
        exit(json_encode($res));
    }

    /* 
     * 取消订单
     * (post)传值：key:令牌  order_id:订单id
     */
    public function qxorderOp(){
        $key = $_POST['key'];
        $memberid = getmemberid($key);
        $order_id = $_POST['order_id'];
        $map['order_id'] = $order_id;
        $order = Model()->table("order")->where($map)->find();
        $map['buyer_id'] = $memberid;
        $update['order_state'] = 0;
        $update['wxstate'] = 5;
        $result = Model()->table("order")->where($map)->update($update);
        if($result){
            $res = getarrres('200','已取消',1);
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
            $result->addTag(array('teacher'));
            // $result->setNotificationAlert($content);
            $result->addAndroidNotification('订单已被取消', '办工师', 1, array("order_type"=>"del","order_id"=>$order_id));
            $result->addIosNotification("订单已被取消", 'iOS sound', JPush::DISABLE_BADGE, true, 'iOS category', array("order_type"=>"del","order_id"=>$order_id));
            $result->setMessage("msg content", 'msg title', 'type', array("order_type"=>"del","order_id"=>$order_id));
            $result->setOptions(100000, 3600, null, false);
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
        }else{
            $res = getarrres('-401','取消失败',0);
            exit(json_encode($res));
        }
        
    }

    /** 
      租赁单个商品信息
     */
    public function rent_infoOp(){
        $goods_id = isset($_POST['goods_id'])?$_POST['goods_id']:'';
        if(empty($goods_id)){
            $res = getarrres('-401','查无此商品！',1);
            exit(json_encode($res));
        }
        $map['goods_id'] = $goods_id;
        $map['goods_state'] = 1;
        $map['goods_verify'] = 1;
        $field = "goods_id,goods_jingle,goods_name,goods_image,store_id,brand_id";
        $goods = Model()->table("goods_rent")->field($field)->where($map)->find();
        if(empty($goods)){
            $res = getarrres('-401','查无此商品！',0);
            exit(json_encode($res));
        }
        $goods['goods_image'] = cthumb($goods['goods_image'], 360, $goods['store_id']);
        $brand = $goods['brand_id'];
        $brand = Model()->table("brand")->field("brand_name")->where("brand_id=$brand")->find();
        $goods['brand_name'] = $brand['brand_name'];
        $store = $goods['store_id'];
        $store = Model()->table("store")->field("live_store_tel,member_id,store_phone")->where("store_id=$store")->find();
        $goods['store_phone'] = $store['store_phone'];
        if($store['store_phone'] == ''){
            if($store['live_store_tel'] != ''){
                $goods['store_phone'] = $store['live_store_tel'];
            }else{
                $memap['member_id'] = $store['member_id'];
                $member = Model()->table("member")->field("store_phone")->where($memap)->find();
                $goods['store_phone'] = $member['member_mobile'];
            }
            
        }
        $res = getarrres('200','查询成功',$goods);
        exit(json_encode($res));
    }

}
