<?php
/**
 * 融云 Server API PHP 客户端
 * create by kitName
 * create datetime : 2016-09-05 
 * 
 * v2.0.1
 */
defined('InShopNC') or exit('Access Invalid!');
class exampleControl extends BaseHomeControl{

	//根据用户信息获取token
	public function getTokenOp(){

		include 'rongcloud.php';

		$appKey = 'pwe86ga5epo06';

		$appSecret = '58PDTb6v8AqF';

		$jsonPath = "jsonsource/";

		$RongCloud = new RongCloud($appKey,$appSecret);

		$memberid  = isset($_POST['memberid'])?$_POST['memberid']:'';
		$member_name  = isset($_POST['member_name'])?$_POST['member_name']:'';
		if($memberid == '' || $member_name == ''){
			$res = getarrres('-401','传入信息不完整',array($memberid,$member_name));
            exit(json_encode($res));
		}
		$datas['member_id'] = $memberid;
        $memberarr = Model()->table("member")->where($datas)->find();
		$member_avatar  = getMemberAvatar($memberarr['member_avatar']);
        $result = $RongCloud->user()->getToken($memberid, $member_name, $member_avatar);
        $array = get_object_vars(json_decode($result));
        $array['member_avatar'] = $member_avatar;
        exit(json_encode($array));
	}

	/**
		第三方登录存储用户接口
	 **/
	public function setuserOp(){
		$client = isset($_POST['client'])?$_POST['client']:'';
		$openid = isset($_POST['openid'])?$_POST['openid']:'';
		$nickname = isset($_POST['nickname'])?$_POST['nickname']:'';
		$avatar = isset($_POST['avatar'])?$_POST['avatar']:'';
		$type = isset($_POST['type'])?$_POST['type']:'';
		if($client == '' || $openid == '' || $nickname == '' || $type ==''){
			$res = getarrres('-401','传入信息不完整',array($token,$openid,$nickname,$type));
            exit(json_encode($res));
		}
		if($type == 'qq'){
			$selmap['member_qqopenid'] = $openid;
            $nickname = $nickname.'_qq';
		}else{
			$selmap['member_wxopenid'] = $openid;
            $nickname = preg_replace('~\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]~', '', $nickname);
            $nickname = $nickname.'_wx';
		}
		// 建立登录信息
		$insert['member_name'] = $nickname;
		$insert['client_type'] = $client;
		$insert['login_time'] = time();

		$selarr = Model()->table("member")->field("member_id")->where($selmap)->find();
		$member_id = empty($selarr['member_id'])?'':$selarr['member_id'];
		if($member_id != ''){
			$insert['member_id'] = $member_id;
			$token = $this->_get_token($member_id, $nickname, $client);
			$insert['token'] = $token;
			$rest = Model()->table("mb_user_token")->insert($insert);
			if($token != ''){
				$res = getarrres('200','登录成功',array('key'=>$token,'user_id'=>$member_id,'user_name'=>$nickname));
			}else{
				$res = getarrres('-401','登录失败',0);
			}
			exit(json_encode($res));
		}
		if($type == 'qq'){
			$insmap['member_qqopenid'] = $openid;
		}else{
			$insmap['member_wxopenid'] = $openid;
		}
		$insmap['member_name'] = $nickname;
		$insmap['member_avatar'] = $avatar;
		$insmap['member_time'] = time();
		$insmap['member_login_time'] = time();
		$result = Model()->table("member")->insert($insmap);
		if($result){
			$member_id = Model()->table("member")->getLastID();
			$insert['member_id'] = $member_id;
			$token = $this->_get_token($member_id, $nickname, $client);
			$insert['token'] = $token;
			$rest = Model()->table("mb_user_token")->insert($insert);

			ini_set("display_errors", "On");
            error_reporting(E_ALL | E_STRICT);
            require_once("../src/JPush/JPush.php");

            $app_key = 'ae1d588ecf543c12249732df';
            $master_secret = '4517e2aab7e88df3e1c0411b';

            $TAG1 = "users";
            $ALIAS1 = "alias1".$member_id;
            $REGISTRATION_ID1 = $_POST['rid'];

            // 初始化
            $client = new JPush($app_key, $master_secret);

            // 更新指定的设备的Alias(亦可以增加/删除Tags)
            $result = $client->device()->updateDevice($REGISTRATION_ID1, $ALIAS1);
            $result = $client->device()->updateTag($TAG1, array($REGISTRATION_ID1));

			$res = getarrres('200','登录成功',array('key'=>$token,'user_id'=>$member_id,'user_name'=>$nickname,'alias'=>$ALIAS1,'tag'=>$TAG1));
        }else{
            $res = getarrres('-401','登录失败',0);
        }
        exit(json_encode($res));
	}

	/**
      登录生成token
     */
    private function _get_token($member_id, $member_name, $client) {
        $model_mb_user_token = Model('mb_user_token');

        //重新登录后以前的令牌失效
        //暂时停用
        $condition = array();
        $condition['member_id'] = $member_id;
        $condition['client_type'] = $client;
        $model_mb_user_token->delMbUserToken($condition);

        //生成新的token
        $mb_user_token_info = array();
        $token = md5($member_name.strval(TIMESTAMP).strval(rand(0,999999)));
        $mb_user_token_info['member_id'] = $member_id;
        $mb_user_token_info['member_name'] = $member_name;
        $mb_user_token_info['token'] = $token;
        $mb_user_token_info['login_time'] = TIMESTAMP;
        $mb_user_token_info['client_type'] = $client;

        $result = $model_mb_user_token->addMbUserToken($mb_user_token_info);

        if($result){
            return $token;
        } else {
            return '';
        }

    }

    /**
      店铺介绍
     */
    public function storejsOp(){
    	$store_id = isset($_POST['store_id'])?$_POST['store_id']:'';
    	if($store_id == ''){
    		$res = getarrres('-401','传入信息不完整',array($store_id));
            exit(json_encode($res));
    	}
    	$key = isset($_POST['key'])?$_POST['key']:'';
    	if($key != ''){
    		$memberid = getmemberid($key);
    		$mapa['store_id'] = $store_id;
    		$mapa['member_id'] = $memberid;
    		$mapa['fav_type'] = 'store';
    		$arr = Model()->table("favorites")->where($mapa)->find();
    		if(!empty($arr)){
    			$ftype = '已收藏';
    		}else{
    			$ftype = '未收藏';
    		}
    	}else{
            $ftype = '未收藏';
        } 
        $mapac['store_id'] = $store_id;
        $mapac['fav_type'] = 'store';
        $arrz = Model()->table("favorites")->where($mapac)->select();
        $count = count($arrz);  
        if(!empty($arrz)){
            $fcount = $count;
        }else{
            $fcount = 0;
        }
        
    	$map['store_id'] = $store_id;
    	$store = Model()->table("store")->where($map)->find();
    	if($store['store_description'] != ''){
    		$res = getarrres('200','查询成功',array('des'=>$store['store_description'],'time'=>$store['store_time'],'name'=>$store['store_name'],'company'=>$store['store_company_name'],'ftype'=>$ftype,'fcount'=>$fcount));
        }else{
            $res = getarrres('-401','查询为空',0);
        }
        exit(json_encode($res));
    }

    /**
      退款 退货
     */
    public function refundOp(){
        $key = isset($_POST['key'])?$_POST['key']:'';
        $token = Model("mb_user_token");
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $member_id = $tokens['member_id'];
        $member_name = $tokens['member_name'];
        if($member_id == ''){
            $res = getarrres('-401','登录已过期，请重新登录！',0);
            exit(json_encode($res));
        }
        $refund_type = isset($_POST['refund_type '])?$_POST['refund_type ']:'';//1 退款 2 退货
        $number = '';
        if($refund_type == 2){
            $number = isset($_POST['number'])?$_POST['number']:'';
        }
        $order_id      = isset($_POST['order_id'])?$_POST['order_id']:'';
        $goods_state   = isset($_POST['goods_state'])?$_POST['goods_state']:'1';//物流状态 物流状态:1为待发货,2为待收货,3为未收到,4为已收货
        $reason_id     = isset($_POST['reason_id'])?$_POST['reason_id']:'';
        $reason_info   = isset($_POST['reason_info'])?$_POST['reason_info']:'';
        $is_all        = isset($_POST['is_all'])?$_POST['is_all']:'';
        $order_amount  = isset($_POST['order_amount'])?$_POST['order_amount']:'';
        $buyer_message = isset($_POST['buyer_message'])?$_POST['buyer_message']:'';//说明

        if($order_id == '' || $reason_id == '' || $reason_info == '' || $order_amount == '' || $buyer_message == '' || $is_all == ''){
            $res = getarrres('-401','请将申请信息填写完整！',$_POST);
            exit(json_encode($res));
        }

        $mapor['order.order_id'] = $order_id;
        $order = Model()->table("order,order_goods")->join('left join')->on('order.order_id=order_goods.order_id')->where($mapor)->find();
        
        $goods_id = $order['goods_id'];
        $order_goods_id = $order['rec_id'];
        $goods_name = $order['goods_name'];
        $goods_num  = $order['goods_num'];
        $order_sn   = $order['order_sn'];

        $image1 = isset($_POST['image1'])?$_POST['image1']:'';
        $image2 = isset($_POST['image2'])?$_POST['image2']:'';
        $image3 = isset($_POST['image3'])?$_POST['image3']:'';

        $numsss = 0;

        if(!empty($image1)){
            $dir = '../data/upload/shop/refund/';
            $aa1 = !empty($order_id)?$order_id.'1':'AA';
            //$filename = $this->base64_upload($image,"resource/",$aa);
            $filename1 = $this->base64_upload($image1,$dir,$aa1);

            if($filename1 == false){
                $res = getarrres('-401','图片上传失败',0);
                exit(json_encode($res));
            }
            $numsss = $numsss+1;
            $picarr[$numsss] = $filename1;
        }

        if(!empty($image2)){
            $dir = '../data/upload/shop/refund/';
            $aa2 = !empty($order_id)?$order_id.'2':'AA';

            $filename2 = $this->base64_upload($image2,$dir,$aa2);

            if($filename2 == false){
                $res = getarrres('-401','图片上传失败',0);
                exit(json_encode($res));
            }
            $numsss = $numsss+1;
            $picarr[$numsss] = $filename2;
        }

        if(!empty($image3)){
            $dir = '../data/upload/shop/refund/';

            $aa3 = !empty($order_id)?$order_id.'3':'AA';

            $filename3 = $this->base64_upload($image3,$dir,$aa3);

            if($filename3 == false){
                $res = getarrres('-401','图片上传失败',0);
                exit(json_encode($res));
            }
            $numsss = $numsss+1;
            $picarr[$numsss] = $filename3;
        }
       
        $pic_array['buyer'] = $picarr;//上传凭证
        $info = serialize($pic_array);
        $goodarr['goods_id'] = $goods_id;
        $goods = Model()->table("goods")->field("goods_image")->where($goodarr)->find();
        $goods_image = $goods['goods_image'];
        $condition['add_time'] = time();
        $condition['reason_id'] = $reason_id;
        $condition['reason_info'] = $reason_info;
        $condition['buyer_message'] = $buyer_message;
        $condition['buyer_id'] = $member_id;
        $condition['buyer_name'] = $member_name;
        $condition['store_id'] = $order['store_id'];
        $condition['store_name'] = $order['store_name'];
        $condition['goods_image'] = $goods_image;
        $condition['goods_id'] = $goods_id;
        $condition['return_type'] = $refund_type;
        $condition['order_goods_id'] = $order_goods_id;
        $condition['goods_name'] = $goods_name;
        $condition['order_sn'] = $order_sn;
        $condition['refund_sn'] = mt_rand(100,999).substr(100+$order_id,-3).date('ymdHis');
        $condition['order_id'] = $order_id;
        $condition['goods_num'] = empty($number)?$goods_num:$number;
        $condition['refund_amount'] = $order_amount;
        $condition['pic_info'] = $info;
        $result = Model()->table("refund_return")->insert($condition);
        if($result){
            $update['order_state'] = 0;
            $update['refund_state'] = $is_all;
            $where['order_id'] = $order_id;
            Model()->table("order")->where($where)->update($update);
            $res = getarrres('200','申请已提交！',1);
        }else{
            $res = getarrres('-401','填写信息有误！',$_POST);
        }
        exit(json_encode($res));
    }

    /**
      上传图片
     */
    public function base64_upload($base64,$dir,$memberid) {

        $base64_image = str_replace(' ', '+', $base64);
        //post的数据里面，加号会被替换为空格，需要重新替换回来，如果不是post的数据，则注释掉这一行
        // if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)){
            //匹配成功
            // if($result[2] == 'jpeg'){
                $image_name = time().rand().$memberid.'.jpg';
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
      退款 退货原因
     */
    public function reasonOp(){
        $reason = Model()->table("refund_reason")->select();
        if(!empty($reason)){
            $res = getarrres('200','成功',$reason);
        }else{
            $res = getarrres('-401','原因为空',0);
        }
        exit(json_encode($res));
    }

    /**
      退款 退货列表
     */
    public function refund_listOp(){
        $refund_type = isset($_POST['refund_type'])?$_POST['refund_type']:'';
        $key = isset($_POST['key'])?$_POST['key']:'';
        $token = Model("mb_user_token");
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $member_id = $tokens['member_id'];
        $member_name = $tokens['member_name'];
        if($member_id == ''){
            $res = getarrres('-401','登录已过期，请重新登录！',0);
            exit(json_encode($res));
        }
        $remap['buyer_id'] = $member_id;
        $remap['refund_type'] = $refund_type;
        $field = "refund_id,store_name,refund_amount,add_time,order_id,goods_num";
        $list = Model()->table("refund_return")->field($field)->where($remap)->select();
        if(empty($list)){
            $res = getarrres('-401','没有记录！',$_POST);
            exit(json_encode($res));
        }
       
        foreach ($list as $key => $value){ 
            $mapor['order_id'] = $value['order_id'];
            $field = "store_id,goods_image,goods_name";
            $goods = Model()->table("order_goods")->field($field)->where($mapor)->select();
            foreach ($goods as $k => $v) {
                $goods[$k]['goods_image'] =  cthumb($v['goods_image'], 360, $v['store_id']);
            }
            
            $list[$key]['goods_list'] = $goods;
        }
        $res = getarrres('200','查询成功',$list);
        exit(json_encode($res));
    }

    /**
      退款 退货详细
     */
    public function refund_infoOp(){
        $refund_id = isset($_POST['refund_id'])?$_POST['refund_id']:'';
        $key = isset($_POST['key'])?$_POST['key']:'';
        $token = Model("mb_user_token");
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $member_id = $tokens['member_id'];
        $member_name = $tokens['member_name'];
        if($member_id == ''){
            $res = getarrres('-401','登录已过期，请重新登录！',0);
            exit(json_encode($res));
        }
        $remap['buyer_id'] = $member_id;
        $remap['refund_id'] = $refund_id;
        $field = "refund_id,refund_amount,add_time,order_id,refund_sn,reason_info,refund_amount,buyer_message,seller_state,refund_state,admin_message,seller_message,goods_num";
        $list = Model()->table("refund_return")->field($field)->where($remap)->find();
        if(empty($list)){
            $res = getarrres('-401','没有记录！',$_POST);
            exit(json_encode($res));
        }
        if($list['seller_state'] == 1){
            $list['seller_state'] = '待审核';
        }
        if($list['seller_state'] == 2){
            $list['seller_state'] = '同意';
        }
        if($list['seller_state'] == 3){
            $list['seller_state'] = '不同意';
        }
        if($list['refund_state'] == 1){
            $list['refund_state'] = '处理中';
        }
        if($list['refund_state'] == 2){
            $list['refund_state'] = '待处理';
        }
        if($list['refund_state'] == 3){
            $list['refund_state'] = '已完成';
        }
        $res = getarrres('200','查询成功',$list);
        exit(json_encode($res));
    }

    /**
        退款商品列表
     */
    public function goods_infoOp(){
        $order_id = isset($_POST['order_id'])?$_POST['order_id']:'';
        $key = isset($_POST['key'])?$_POST['key']:'';
        $token = Model("mb_user_token");
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $member_id = $tokens['member_id'];
        $member_name = $tokens['member_name'];
        if($member_id == ''){
            $res = getarrres('-401','登录已过期，请重新登录！',0);
            exit(json_encode($res));
        }

        $map['order_id'] = $order_id;
        $map['buyer_id'] = $member_id;
        $order = Model()->table("order")->field("order_id,store_name")->where($map)->find();
        if(empty($order)){
            $res = getarrres('-401','订单不存在！',$_POST);
            exit(json_encode($res));
        }
       
        $mapor['order_id'] = $order_id;
        $field = "goods_image,goods_name,goods_num,goods_price";
        $goods = Model()->table("order_goods")->field($field)->where($mapor)->select();
        foreach ($goods as $k => $v) {
            $goods[$k]['goods_image'] =  cthumb($v['goods_image'], 360, $v['store_id']);
        }
        $arr['store_name'] = $order['store_name'];        
        $arr['order_id']   = $order['order_id']; 
        $arr['goods_list'] = $goods;       
        $res = getarrres('200','查询成功',$arr);
        exit(json_encode($res));
    }

    /**
        提现说明
     */
    public function txexplainOp(){
        $map['name'] = 'txsm';
        $cost = Model()->table("setting")->where($map)->find();
        $res = getarrres('200','成功',$cost['value']);
        exit(json_encode($res));
    }

    /**
        确认收货地址传回运费
     */
    public function gettransOp(){
        $addr       = isset($_POST['addr'])?$_POST['addr']:'北京';
        $address_id = isset($_POST['address_id'])?$_POST['address_id']:'';
        $goods_id   = isset($_POST['goods_id'])?$_POST['goods_id']:'';
        if(empty($address_id) && empty($addr)){
            $res = getarrres('-401','地址为空',array($address_id,$addr));
            exit(json_encode($res));
        }
        if(empty($goods_id)){
            $res = getarrres('-401','请购买商品',$address_id);
            exit(json_encode($res));
        }
        if($address_id != ''){
            $address = Model()->table("address")->where("address_id=$address_id")->find();
            $pro = explode(' ', $address['address']);
            if(count($pro) < 3){
                $pro = explode(' ', $address['area_info']);
            }
        }else{
            $pro['0'] = $addr;
        }
        $arr = explode(',', $goods_id);
        $amount = 0;
        $num = 0;
        $transport = array();
        foreach ($arr as $key => $value) {
            $goodsmap['goods_id'] = $value;
            $goods = Model()->table('goods')->field('transport_id,goods_freight')->where($goodsmap)->find();
            if(floatval($goods['goods_freight'] > 0)){
                $num = floatval($goods['goods_freight']);
            }else{
                $tmap['transport_id'] = $goods['transport_id'];
                $tmap['area_name'] = array('like','%'.$pro['0'].'%');
                $transports = Model()->table('transport_extend')->where($tmap)->find();
                if(!empty($transports)){
                    $num = $transports['sprice'];
                } 
            }
            $amount += $num;
            $transport[$key]['goods_id']      = $value;
            $transport[$key]['goods_freight'] = $num;
        }
        $arrs['amount'] = $amount;
        $arrs['list']   = $transport;
        $res = getarrres('200','成功',$arrs);
        exit(json_encode($res));
    }
}
?>
