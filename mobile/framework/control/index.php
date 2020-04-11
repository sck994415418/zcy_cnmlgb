<?php
/**
 * 首页
 *
 * @好商城V4 (c) 2015-2016 33hao Inc.
 * @license    http://www.haoid.cn
 * @link       交流群号：216611541
 * @since      好商城提供技术支持 授权请购买shopnc授权
 */

defined('InShopNC') or exit('Access Invalid!');
class indexControl extends mobileHomeControl{

	public function __construct() {
        parent::__construct();
    }

    /**
     * 订单评价
     */
    public function setevaluatesOp(){
        $sessarr = $_SESSION['wap_member_info'];
        $order_id = isset($_POST['order_id'])?$_POST['order_id']:'';
        $condition['geval_content'] = isset($_POST['content'])?$_POST['content']:'';
        $condition['geval_scores']  = isset($_POST['star'])?$_POST['star']:'';
        $condition['geval_orderid'] = $order_id;
        $condition['geval_state']   = 0;
        $condition['geval_frommemberid'] = isset($sessarr['userid'])?$sessarr['userid']:'';
        $condition['geval_frommembername'] = isset($sessarr['username'])?$sessarr['username']:'';
        $condition['geval_addtime'] = time();
        $result = Model()->table('evaluate_goods')->insert($condition);
        if($result){
            $this->_output_special(1);
        }else{
            $this->_output_special(0);
        }
    }

    /**
     * 订单详情
     */
    public function order_infoOp() {
        $sessarr = $_SESSION['wap_member_info'];
        $order_id = isset($_GET['order_id'])?$_GET['order_id']:'';
        if ($order_id <= 0) {
            output_error('订单不存在');
        }
        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = isset($sessarr['userid'])?$sessarr['userid']:'';
        $order = $model_order->getOrderInfo($condition);
        $order['add_time'] = date('Y-m-d',$order['add_time']);
        $order['add_times'] = date('H:i',$order['add_time']);
        $order['jdtime'] = date('Y-m-d',$order['jdtime']);
        $order['jdtimes'] = date('H:i',$order['jdtime']);
        $order['cftime'] = date('Y-m-d',$order['cftime']);
        $order['cftimes'] = date('H:i',$order['cftime']);
        $order['wxtime'] = date('Y-m-d',$order['wxtime']);
        $order['wxtimes'] = date('H:i',$order['wxtime']);
        $order['finnshed_time'] = date('Y-m-d',$order['finnshed_time']);
        $order['finnshed_times'] = date('H:i',$order['finnshed_time']);

        if(empty($order['master_id'])){
            $memap['member_id'] = $order['master_id'];
            $member = Model()->table("member")->where($memap)->find();
            $data['master_name'] = $member['member_name'];
            $data['master_img']  = getMemberAvatar($member['member_avatar']);
        }else{
            $data['master_name'] = '';
            $data['master_img']  = '';
        }
        $data['tel'] = $order['tel'];
        if($order['wxstate'] == 0){
            $str = '<li>
                                <dl>
                                    <dd class="d1">'.$order['add_time'].'</dd>
                                    <dd class="d2">'.$order['add_times'].'</dd>
                                    <dd class="d3"> 下单</dd>
                                </dl>
                            </li>';
        }
        if($order['wxstate'] == 1){
            $str = '<li>
                                <dl>
                                    <dd class="d1">'.$order['add_time'].'</dd>
                                    <dd class="d2">'.$order['add_times'].'</dd>
                                    <dd class="d3"> 下单</dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dd class="d1">'.$order['jdtime'].'</dd>
                                    <dd class="d2">'.$order['jdtimes'].'</dd>
                                    <dd class="d3">'.$order['master_name'].'已接单'.$order['tel'].'</dd>
                                </dl>
                            </li>';
        }
        if($order['wxstate'] == 2){
            $str = '<li>
                                <dl>
                                    <dd class="d1">'.$order['add_time'].'</dd>
                                    <dd class="d2">'.$order['add_times'].'</dd>
                                    <dd class="d3"> 下单</dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dd class="d1">'.$order['jdtime'].'</dd>
                                    <dd class="d2">'.$order['jdtimes'].'</dd>
                                    <dd class="d3">'.$order['master_name'].'已接单'.$order['tel'].'</dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dd class="d1">'.$order['cftime'].'</dd>
                                    <dd class="d2">'.$order['cftimes'].'</dd>
                                    <dd class="d3"> 维修师傅已出发</dd>
                                </dl>
                            </li>';
        }
        if($order['wxstate'] == 3){
            $str = '<li>
                                <dl>
                                    <dd class="d1">'.$order['add_time'].'</dd>
                                    <dd class="d2">'.$order['add_times'].'</dd>
                                    <dd class="d3"> 下单</dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dd class="d1">'.$order['jdtime'].'</dd>
                                    <dd class="d2">'.$order['jdtimes'].'</dd>
                                    <dd class="d3">'.$order['master_name'].'已接单'.$order['tel'].'</dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dd class="d1">'.$order['cftime'].'</dd>
                                    <dd class="d2">'.$order['cftimes'].'</dd>
                                    <dd class="d3"> 维修师傅已出发</dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dd class="d1">'.$order['wxtime'].'</dd>
                                    <dd class="d2">'.$order['wxtimes'].'</dd>
                                    <dd class="d3">维修中</dd>
                                </dl>
                            </li>';
        }
        if($order['wxstate'] == 4){
            $str = '<li>
                                <dl>
                                    <dd class="d1">'.$order['add_time'].'</dd>
                                    <dd class="d2">'.$order['add_times'].'</dd>
                                    <dd class="d3"> 下单</dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dd class="d1">'.$order['jdtime'].'</dd>
                                    <dd class="d2">'.$order['jdtimes'].'</dd>
                                    <dd class="d3">'.$order['master_name'].'已接单'.$order['tel'].'</dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dd class="d1">'.$order['cftime'].'</dd>
                                    <dd class="d2">'.$order['cftimes'].'</dd>
                                    <dd class="d3"> 维修师傅已出发</dd>
                                </dl>
                            </li>
                            <li id="wx">
                                <dl>
                                    <dd class="d1">'.$order['wxtime'].'</dd>
                                    <dd class="d2">'.$order['wxtimes'].'</dd>
                                    <dd class="d3">维修中</dd>
                                </dl>
                            </li>
                            <li id="wc">
                                <dl>
                                    <dd class="d1">'.$order['wctime'].'</dd>
                                    <dd class="d2">'.$order['wctime'].'</dd>
                                    <dd class="d3">完成</dd>
                                </dl>
                            </li>';
        }
        $data['str'] = $str;
        $this->_output_special($data);
    }

    /**
     * 线下支付
     */
    public function offpayOp(){
        $map['pay_sn'] = isset($_GET['pay_sn'])?$_GET['pay_sn']:'';
        $condition['payment_code'] = 'offline';
        $result = Model()->table("order")->where($map)->update($condition);
        if($result){
            echo "<script type='text/javascript' charset='utf-8'>alert('提交成功');window.location.href='/wap/tmpl/member/order_list.html';</script>";
        }else{
            echo "<script type='text/javascript' charset='utf-8'>alert('提交失败');history.go(-1);</script>";
        }

    }

    /**
     * 获取订单详情
     */
    public function tippayOp(){
        $map['order_id'] = isset($_GET['order_id'])?$_GET['order_id']:'';
        $order = Model()->table("order")->where($map)->find();
        $this->_output_special($order);
    }

    /**
     * 生成设备订单
     */
    public function setordersOp(){
        $sessarr = $_SESSION['wap_member_info'];
        if(empty($sessarr)){
            echo "<script type='text/javascript' charset='utf-8'>alert('请登录！');window.location.href='http://www.nrwspt.com/wap/tmpl/member/login.html';</script>";
        }
        $order = Model()->table("order")->field("order_id")->order('id desc')->find();
        $order_id = intval($order['order_id'])+1;
        $map['buyer_id']      = isset($sessarr['userid'])?$sessarr['userid']:'';
        $map['buyer_name']    = isset($sessarr['username'])?$sessarr['username']:'';

        $time = isset($_POST['time'])?$_POST['time']:'';
        if($time == ''){
            $map['nowtime'] = time();
        }else{
            $map['apptime'] = $time;
        }

        $map['address'] = isset($_POST['address'])?$_POST['address']:'';
        $map['type'] = isset($_POST['type'])?$_POST['type']:'';
        $map['tel'] = isset($_POST['tel'])?$_POST['tel']:'';
        $map['scost'] = isset($_POST['num'])?$_POST['num']:'';
        $map['order_amount'] = isset($_POST['num'])?$_POST['num']:'';
        $map['goods_amount'] = isset($_POST['num'])?$_POST['num']:'';
        $map['add_time'] = time();
        $map['wxstate'] = 0;
        $map['is_master'] = 1;
        $map['order_sn'] = $this->makeOrderSn($order_id);
        $pay_sn = $this->makePaySn($sessarr['userid']);
        $map['pay_sn'] = $pay_sn;
        $result = Model()->table("order")->insert($map);
        if($result){
            $pmap['pay_sn'] = $pay_sn;
            $pmap['buyer_id'] = isset($sessarr['userid'])?$sessarr['userid']:'';
            $res = Model()->table("order_pay")->insert($pmap);
            echo "<script type='text/javascript' charset='utf-8'>alert('提交成功');window.location.href='/wap/shangmenfuwufei.html?order_id=".$result."';</script>";
        }else{
            echo "<script type='text/javascript' charset='utf-8'>alert('提交失败');history.go(-1);</script>";
        }
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

    /**
     * 服务费
     */
    public function tipOp(){
        $smcost = Model()->table("setting")->where("name='smcost'")->find();
        $dj = floatval($smcost['value']);
        $this->_output_special($dj);
    }

    /**
     * 首页
     */
	public function indexOp() {
        $model_mb_special = Model('mb_special'); 
        $data = $model_mb_special->getMbSpecialIndex();
        $this->_output_special($data, $_GET['type']);
	}

    /**
     * 快报
     */
    public function kuaibaoOp(){
        $map['name'] = 'kuaibao';
        $cost = Model()->table("setting")->where($map)->find();
        $this->_output_special($cost['value']);
    }

    /**
     * 设备租赁主页品牌1
     */
    public function zulinpinpaiOp(){
        $map['brand_recommend'] = 1;
        $map['brand_apply'] = 1;
        $brand = Model()->table("brand")->where($map)->limit('0,8')->select();
        $onestr = '';
        for ($i=0; $i < 4; $i++) { 
            $onestr .= '<td>
                            <a href="pinpai.html?brand_id='.$brand[$i]['brand_id'].'"><img src="../data/upload/shop/brand/'.$brand[$i]['brand_pic'].'" /></a>
                        </td>';
        }
        for ($i=4; $i < 8; $i++) { 
            $twostr .= '<td>
                            <a href="pinpai.html?brand_id='.$brand[$i]['brand_id'].'"><img src="../data/upload/shop/brand/'.$brand[$i]['brand_pic'].'" /></a>
                        </td>';
        }
        $data['onestr'] = $onestr;
        $data['twostr'] = $twostr;
        $this->_output_special($data);
    }

    /**
     * 热门设备
     */
    public function hotshebeiOp(){
        $map['goods_state']  = 1;
        $map['goods_verify'] = 1;
        $brand_goods = Model()->table("goods_rent")->field('store_id,goods_id,goods_name,goods_image,goods_jingle')->where($map)->order("goods_click desc")->limit('0,6')->select();
        $str = '';
        foreach ($brand_goods as $key => $value){
            $image  = cthumb($value['goods_image'], 360, $value['store_id']);
            $name = mb_substr($value['goods_name'], 0, 10);
            $jingle = mb_substr($value['goods_jingle'], 0, 10);
            $str   .= '<dl id="shebei">
                        <dd>
                            <a href="xinxitianxi.html?goods_id='.$value['goods_id'].'">
                                <dl>
                                    <dd class="img_1">
                                        <img src="img/bg.png">

                                    </dd>
                                    <dd class="img_2">
                                        <img src="'.$image.'">
                                    </dd>
                                    <dd class="a_bc">
                                        <ul>
                                            <li>'.$name.'</li>
                                            <li><small>'.$jingle.'</small></li>
                                        </ul>
                                    </dd>
                                </dl>
                            </a>
                        </dd>
                    </dl>';
        }
        $this->_output_special($str);
    }

    /**
     * 品牌设备
     */
    public function ppshebeiOp(){
        $map['goods_state']  = 1;
        $map['goods_verify'] = 1;
        $map['brand_id']     = $_GET['brand_id'];
        $brand_goods = Model()->table("goods_rent")->field('store_id,goods_id,goods_name,goods_image,goods_jingle')->where($map)->select();
        $str = '';
        foreach ($brand_goods as $key => $value){
            $image  = cthumb($value['goods_image'], 360, $value['store_id']);
            $name   = mb_substr($value['goods_name'], 0, 10);
            $jingle = mb_substr($value['goods_jingle'], 0, 10);
            $str   .= '<dd>
                            <a href="xinxitianxi.html?goods_id='.$value['goods_id'].'">
                                <dl>
                                    <dd style="width: 100%;" class="img_1"><span>'.$jingle.'</span>
                                    </dd>
                                    <dd class="img_2">
                                        <img src="'.$image.'">
                                    </dd>
                                    <dd class="a_bc">
                                        <ul>
                                            <li>'.$name.'</li>
                                            <li><small>'.$jingle.'</small></li>
                                        </ul>
                                    </dd>
                                </dl>
                            </a>
                        </dd>';
        }
        $bmap['brand_id'] = $_GET['brand_id'];
        $brand = Model()->table('brand')->where($bmap)->find();
        $data['str'] = $str;
        $data['names'] = $brand['brand_name'];
        $this->_output_special($data);
    }

    /**
     * 设备详情
     */
    public function shebeiinfoOp(){
        $map['goods_state']  = 1;
        $map['goods_verify'] = 1;
        $map['goods_id']     = $_GET['goods_id'];
        $goods = Model()->table("goods_rent")->where($map)->find();
        $name   = mb_substr($goods['goods_name'], 0, 10);
        $jingle = mb_substr($goods['goods_jingle'], 0, 10);
        $image  = cthumb($goods['goods_image'], 360, $goods['store_id']);

        $smap['store_id'] = $goods['store_id'];
        $store = Model()->table("table")->where($smap)->find();

        $bmap['brand_id'] = $goods['brand_id'];
        $brand = Model()->table('brand')->where($bmap)->find();

        $str = '<dl class="infor_right">
                    <dd><strong>'.$brand['brand_name'].'</strong>
                    </dd>
                    <dd>'.$name.'</dd>
                    <dd>'.$jingle.'</dd>
                    <dd class="img_3"><img src="img/tel.png"><span>'.$store['store_presales'].'</span></dd>
                </dl>';
        $data['str']  = $str;
        $data['names'] = $name;
        $data['imgs'] = '<img src="'.$image.'">';
        $this->_output_special($data);
    }

    /**
     * 生成设备订单
     */
    public function setorderOp(){
        $sessarr = $_SESSION['wap_member_info'];
        if(empty($sessarr)){
            echo "<script type='text/javascript' charset='utf-8'>alert('请登录！');window.location.href='http://www.nrwspt.com/wap/tmpl/member/login.html';</script>";
        }
        $map['buyer_id']      = isset($sessarr['userid'])?$sessarr['userid']:'';
        $map['buyer_name']    = isset($_POST['buyer_name'])?$_POST['buyer_name']:'';
        $map['buyer_phone']   = isset($_POST['buyer_phone'])?$_POST['buyer_phone']:'';
        $map['buyer_address'] = isset($_POST['buyer_address'])?$_POST['buyer_address']:'';
        $map['goods_id']      = isset($_POST['goods_id'])?$_POST['goods_id']:'';
        $map['model']         = isset($_POST['model'])?$_POST['model']:'';
        $map['other']         = isset($_POST['other'])?$_POST['other']:'';
        $map['add_time']      = time();
        $map['order_state']   = 10;
        $map['rorder_from']   = 2;
        $result = Model()->table("rent_order")->insert($map);
        if($result){
            echo "<script type='text/javascript' charset='utf-8'>alert('提交成功');history.go(-1);</script>";
        }else{
            echo "<script type='text/javascript' charset='utf-8'>alert('提交失败');history.go(-1);</script>";
        }
    }


    public function allpinpaiOp(){
        $map['brand_recommend'] = 1;
        $map['brand_apply'] = 1;
        $brand = Model()->table("brand")->where($map)->select();
        $str = '<tr>';
        $num = count($brand);
        for ($i=1; $i <= $num; $i++){ 
            $a = $i-1;
            if($i%4 != 0 && $i != $num){

                $str .= '<td>
                    <a href="pinpai.html?brand_id='.$brand[$a]['brand_id'].'">
                        <img src="../data/upload/shop/brand/'.$brand[$a]['brand_pic'].'">
                    </a>
                </td>';
            }
            if($i != 1 && $i%4 == 0 && $i != $num){
 
                $str .= '<td>
                    <a href="pinpai.html?brand_id='.$brand[$a]['brand_id'].'">
                        <img src="../data/upload/shop/brand/'.$brand[$a]['brand_pic'].'">
                    </a>
                </td></tr><tr>';
            }
            if($i == $num){
 
                $str .= '<td>
                    <a href="pinpai.html?brand_id='.$brand[$a]['brand_id'].'">
                        <img src="../data/upload/shop/brand/'.$brand[$a]['brand_pic'].'"/aalt="logo">
                    </a>
                </td></tr>';
            }
        }
        $this->_output_special($str);                
    }


    /**
     * 专题
     */
	public function specialOp() {
        $model_mb_special = Model('mb_special'); 
        $data = $model_mb_special->getMbSpecialItemUsableListByID($_GET['special_id']);
        $this->_output_special($data, $_GET['type'], $_GET['special_id']);
	}

    /**
     * 输出专题
     */
    private function _output_special($data, $type = 'json', $special_id = 0) {
        $model_special = Model('mb_special');
        if($_GET['type'] == 'html') {
            $html_path = $model_special->getMbSpecialHtmlPath($special_id);
            if(!is_file($html_path)) {
                ob_start();
                Tpl::output('list', $data);
                Tpl::showpage('mb_special');
                file_put_contents($html_path, ob_get_clean());
            }
            header('Location: ' . $model_special->getMbSpecialHtmlUrl($special_id));
            die;
        } else {
            output_data($data);
        }
    }

    /**
     * android客户端版本号
     */
    public function apk_versionOp() {
		$version = C('mobile_apk_version');
		$url = C('mobile_apk');
        if(empty($version)) {
           $version = '';
        }
        if(empty($url)) {
            $url = '';
        }

        output_data(array('version' => $version, 'url' => $url));
    }

    /**
     * 默认搜索词列表
     */
    public function search_key_listOp() {
        $list = @explode(',',C('hot_search'));
        if (!$list || !is_array($list)) { 
            $list = array();
        }
        if ($_COOKIE['hisSearch'] != '') {
            $his_search_list = explode('~', $_COOKIE['hisSearch']);
        }
        if (!$his_search_list || !is_array($his_search_list)) {
            $his_search_list = array();
        }
        output_data(array('list'=>$list,'his_list'=>$his_search_list));
    } 
	/**
     * 热门搜索列表
     */
    public function search_hot_infoOp() {
        if (C('rec_search') != '') {
            $rec_search_list = @unserialize(C('rec_search'));
        }
        $rec_search_list = is_array($rec_search_list) ? $rec_search_list : array();
        $result = $rec_search_list[array_rand($rec_search_list)];
        output_data(array('hot_info'=>$result ? $result : array()));
    }
	/**
     * 高级搜索
     */
    public function search_advOp() {
		
		$gc_id = intval($_GET['gc_id']);
		 
        $area_list = Model('area')->getAreaList(array('area_deep'=>1),'area_id,area_name');
        if (C('contract_allow') == 1) {
            $contract_list = Model('contract')->getContractItemByCache();
            $_tmp = array();$i = 0;
            foreach ($contract_list as $k => $v) {
                $_tmp[$i]['id'] = $v['cti_id'];
                $_tmp[$i]['name'] = $v['cti_name'];
                $i++;
            }
        }
        output_data(array('area_list'=>$area_list ? $area_list : array(),'contract_list'=>$_tmp));
    }
}
