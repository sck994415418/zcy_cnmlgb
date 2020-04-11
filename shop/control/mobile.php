<?php
/**
 * 商城api
 * autor:LEE
 * 2016.6.27
 */
defined('InShopNC') or exit('Access Invalid!');
class mobileControl extends BaseHomeControl{

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
            	$res = getarrres('200','修改密码成功',1);
            	exit(json_encode($res));
            }else{
            	$res = getarrres('-401','修改失败',0);
                exit(json_encode($res));
            }
            
        }
        $res = getarrres('-401','验证失败',0);
        exit(json_encode($res));
    }

    //判断key是否过期
    public function keygqOp(){
        $key = $_POST['key'];
        $token = Model("mb_user_token");
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        if(empty($tokens)){
            $res = getarrres('-401','令牌已过期',0);
        }else{
            $res = getarrres('200','未过期',1);
        }
        exit(json_encode($res));
    }

    //站内信
    public function getmassageOp(){
        $key = $_POST['key'];
        $token = Model("mb_user_token");
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $memberid = $tokens['member_id'];

        $sql = "select * from ".DBPRE."message where (to_member_id like '%".$memberid."%') or (to_member_id='all')";
        $massage = Model()->query($sql);
        if(empty($massage)){
            $res = getarrres('-401','没有站内信',0);
        }else{
            $res = getarrres('200','站内信列表',$massage);
        }
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
        $filename = $this->base64_upload($image,$dir,$aa);

        if($filename == false){
            $res = getarrres('-401','图片上传失败',0);
            exit(json_encode($res));
        }

        $condition['member_avatar'] = $filename;
        $rs = Model()->table("member")->where("member_id=$memberid")->update($condition);
        if($rs){
            $res = getarrres('200','成功',1);
        }else{
            $res = getarrres('-401','失败',0);
        }
        exit(json_encode($res));
    }

    //积分兑换礼品
    public function credchangeOp(){
        $key = isset($_POST['key'])?$_POST['key']:'';
        $aid = isset($_POST['aid'])?$_POST['aid']:'';
        $gid = isset($_POST['gid'])?$_POST['gid']:'';
        $point = isset($_POST['point'])?$_POST['point']:'';
        $token = Model('mb_user_token');
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $mmap['member_id'] = $tokens['member_id'];
        $member = Model()->table("member")->where($mmap)->find();
        if($member['member_points'] < $point){
            $res = getarrres('-401','积分不足',0);
            exit(json_encode($res));
        }
        $map['point_buyername'] = $tokens['member_name'];
        $map['point_buyerid'] = $tokens['member_id'];
        $map['point_addtime'] = time();
        $map['point_allpoint'] = $point;
        $rs = Model()->table("points_order")->insert($map);
        $id = Model()->table("points_order")->getLastID();
        $where['address_id'] = $aid;
        $address = Model()->table("address")->where($where)->find();
        $amap['point_orderid'] = $id;
        $amap['point_truename'] = $address['true_name'];
        $amap['point_areaid'] = $address['area_id'];
        $amap['point_areainfo'] = $address['area_info'];
        $amap['point_address'] = $address['address'];
        $amap['point_mobphone'] = $address['mob_phone'];
        $rst = Model()->table("points_orderaddress")->insert($amap);
        $wheres['pgoods_id'] = $gid;
        $goods = Model()->table("points_goods")->where($wheres)->find();
        $gmap['point_orderid'] = $id;
        $gmap['point_goodsid'] = $gid;
        $gmap['point_goodsname'] = $goods['pgoods_name'];
        $gmap['point_goodspoints'] = $goods['pgoods_points'];
        $gmap['point_goodsnum'] = 1;
        $gmap['point_goodsimage'] = $goods['pgoods_image'];
        $rss = Model()->table("points_ordergoods")->insert($gmap);
        if($rss && $rst && $rs){
            $sql = "update ".DBPRE."member set member_points=member_points-".$point." where member_id=".$tokens['member_id'];
            Model()->query($sql);
            $res = getarrres('200','兑换成功',1);
        }else{
            $res = getarrres('-401','兑换失败',0);
        }
        exit(json_encode($res));
    }

    //促销商品
    public function salegoodsOp(){
        $map['goods_promotion_type'] = 2;
        $map['goods_state'] = 1;
        $map['goods_verify'] = 1;
        $field = "goods_id,goods_name,goods_image,goods_price,goods_promotion_price";
        $goods = Model()->table("goods")->field($field)->where($map)->select();
        foreach ($goods as $key => $value) {
            $goods[$key]['goods_image'] = cthumb($value['goods_image'], 360, $value['store_id']);
        }
        if(empty($goods)){
            $res = getarrres('200','促销商品为空',0);
        }else{
            $res = getarrres('200','商品列表',$goods);
        }
        exit(json_encode($res));
    }

    //积分礼品列表
    public function giftgoodsOp(){
        $map['pgoods_show'] = 1;
        $field = "pgoods_name,pgoods_points,pgoods_id,pgoods_image";
        $pgoods = Model()->table("points_goods")->field($field)->where($map)->select();
        foreach ($pgoods as $key => $value) {
            $pgoods[$key]['pgoods_image'] = 'http://www.nrwspt.com/data/upload/'.ATTACH_POINTPROD.DS.$value['pgoods_image'];
        }
        if(empty($pgoods)){
            $res = getarrres('200','礼品为空',0);
        }else{
            $res = getarrres('200','礼品列表',$pgoods);
        }
        exit(json_encode($res));
    } 

    //热卖商品
    public function hootgoodsOp(){
        $map['goods_state'] = 1;
        $map['goods_verify'] = 1;
        $field = "goods_id,goods_name,goods_image,goods_price,goods_promotion_price";
        $goods = Model()->table("goods")->field($field)->where($map)->order("goods_salenum desc")->limit("0,20")->select();
        foreach ($goods as $key => $value) {
            $goods[$key]['goods_image'] = cthumb($value['goods_image'], 360, $value['store_id']);
        }
        if(empty($goods)){
            $res = getarrres('200','热卖商品为空',0);
        }else{
            $res = getarrres('200','热卖商品',$goods);
        }
        exit(json_encode($res));
    }

    /**
     * 快报
     */
    public function kuaibaoOp(){
        $map['name'] = 'kuaibao';
        $cost = Model()->table("setting")->where($map)->find();
        $res = getarrres('200','success',$cost['value']);
        exit(json_encode($res));
    }

    /**
     * 幻灯片
     */
    public function shophdpOp(){
        $type = isset($_POST['type'])?$_POST['type']:1050;
        $map['ap_id'] = $type;
        $field = "adv_content,adv_title";
        $hdp = Model()->table("adv")->field($field)->where($map)->select();
        foreach ($hdp as $key => $value) {
            $content = $value['adv_content'];
            unset($hdp[$key]['adv_content']);
            $pic = unserialize($content);
            $hdp[$key]['pic'] = UPLOAD_SITE_URL."/".ATTACH_ADV."/".$pic['adv_pic'];
        }
        $res = getarrres('200','success',$hdp);
        exit(json_encode($res));
    }

    //提交评价
    public function set_evaluateOp(){
        $key = $_POST['key'];//令牌
        $token = Model('mb_user_token');
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $username = $tokens['member_name'];
        $member_id = $tokens['member_id'];
        $order_id = $_POST['order_id'];//订单id
        $goods_id = $_POST['goods_id'];//商品id
        $condition['geval_orderid'] = $order_id;
        $condition['geval_scores'] = $_POST['goodstart'];//星级
        $condition['geval_explain'] = $_POST['content'];//评价内容
        // $store_desccredit = $_POST['store_desccredit'];//描述
        // $store_servicecredit = $_POST['store_servicecredit'];//服务
        // $store_deliverycredit = $_POST['store_deliverycredit'];//发货
        $map['order_id'] = $order_id;
        $maps['goods_id'] = $goods_id;
        $order = Model()->table('order')->where($map)->find();
        $goods = Model()->table('goods')->where($maps)->find();
        $store_id = $order['store_id'];
        $condition['geval_storeid'] = $store_id;
        $condition['geval_storename'] = $order['store_name'];
        $condition['geval_orderno'] = $order['order_sn'];
        $condition['geval_ordergoodsid'] = $goods_id;
        $condition['geval_goodsid'] = $goods_id;
        $condition['geval_goodsname'] = $goods['goods_name'];
        $condition['geval_goodsprice'] = $order['goods_price'];
        $condition['geval_storename'] = $order['store_name'];
        $condition['geval_goodsimage'] = $order['goods_image'];
        $condition['geval_isanonymous'] = $_POST['geval_isanonymous'];//是否匿名评价
        $condition['geval_addtime'] = time();
        $condition['geval_frommemberid'] = $memberid;
        $condition['geval_frommembername'] = $username;
        // $image = $_POST['image'];

        // if(empty($image)){
        //     $res = getarrres('-401','参数为空',0);
        //     exit(json_encode($res));
        // }
        // $dir = '../data/upload/shop/common/';
        // $aa = !empty($memberid)?$memberid:'AA';
        // //$filename = $this->base64_upload($image,"resource/",$aa);
        // $filename = $this->base64_upload($image,$dir,$aa);

        // if($filename == false){
        //     $res = getarrres('-401','图片上传失败',0);
        //     exit(json_encode($res));
        // }

        // $condition['geval_image'] = $filename;
        Model()->table("evaluate_goods")->insert($condition);
        $res = getarrres('200','评价成功！',1);
        exit(json_encode($res));
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

    //商品评价信息
    public function goods_evaluateOp() {
        $goods_id = intval($_POST['goods_id']);
        $type = intval($_POST['type']);

        $condition = array();
        $condition['geval_goodsid'] = $goods_id;
        switch ($type) {
            case '1':
            $condition['geval_scores'] = array('in', '5,4');
            break;
            case '2':
            $condition['geval_scores'] = array('in', '3,2');
            break;
            case '3':
            $condition['geval_scores'] = array('in', '1');
            break;
            case '4':
            $condition['geval_image|geval_image_again'] = array('neq', '');
            break;
            case '5':
            $condition['geval_content_again'] = array('neq', '');
            break;
        }
        
        //查询商品评分信息
        $model_evaluate_goods = Model("evaluate_goods");
        $goods_eval_list = $model_evaluate_goods->getEvaluateGoodsList($condition);
        $goods_eval_list = Logic('member_evaluate')->evaluateListDity($goods_eval_list);

        $page_count = $model_evaluate_goods->gettotalpage();
        foreach ($goods_eval_list as $key => $value) {
            $goods_eval_list[$key]['geval_goodsimage'] = cthumb($value['geval_goodsimage'], 360, $value['geval_storeid']);
        }
        $res = getarrres('200','success',$goods_eval_list);
        exit(json_encode($res));
    }

    /**
    * 设置支付密码
    */
    public function setpaypwdOp(){
        $pwd = md5($_POST['pwd']);
        $key  = $_POST['key'];
        $memberid = getmemberid($key);
        $memap["member_id"] = $memberid;
        $upmap['member_paypwd'] = $pwd;
        $member = Model()->table("member")->where($memap)->update($upmap);
        $res = getarrres('200','success',1);
        exit(json_encode($res));
    }

    /**
    * 修改密码
    */
    public function editpwdOp(){
        $pwd = md5($_POST['pwd']);
        $key  = $_POST['key'];
        $memberid = getmemberid($key);
        $memap["member_id"] = $memberid;
        $upmap['member_passwd'] = $pwd;
        $member = Model()->table("member")->where($memap)->update($upmap);
        $res = getarrres('200','success',1);
        exit(json_encode($res));
    }   

    /**
     * 绑定手机
     */
    public function mobilebdOp() {
        $tel  = $_POST['tel'];
        $key  = $_POST['key'];
        $memberid = getmemberid($key);
        $memap["member_id"] = $memberid;
        $upmap['member_mobile_bind'] = 1;
        $upmap['member_mobile'] = $tel;
        $member = Model()->table("member")->where($memap)->update($upmap);
        $res = getarrres('200','success',1);
        exit(json_encode($res));
    }

    /**
     * 检测是否设置了支付密码
     */
    public function get_paypwd_infoOp(){
        $key  = $_POST['key'];
        $memberid = getmemberid($key);
        $memap["member_id"] = $memberid;
        $member = Model()->table("member")->where($memap)->find();
        if($member['member_paypwd'] == ''){
            $res = getarrres('200','success',0);
        }else{
            $res = getarrres('200','success',1);
        }
        exit(json_encode($res));
    }

    /**
     * 商品详情--店铺推荐(商品列表)
     * (post)店铺id
     */
    public function storegtjOp(){
        $map['goods_commend'] = 1;
        $map['goods_state'] = 1;
        $map['goods_verify'] = 1;
        $map['store_id'] = $_POST['store_id'];
        $field = "goods_id,goods_name,goods_image,goods_marketprice,goods_promotion_price,goods_price";
        $goods = Model()->table("goods")->field($field)->where($map)->order("goods_addtime desc")->limit(6)->select();
        foreach ($goods as $key => $value) {
            $goods[$key]['goods_image'] = cthumb($value['goods_image'], 360, $value['store_id']);
        }
        $res = getarrres('200','success',$goods);
        exit(json_encode($res));
    }

    /**
     * 领取代金券
     * (post)store_id:店铺id tid:代金券id
     */
    public function voucher_freeexOp(){
        $voucher_t_id = $_POST['tid'];
        $key  = $_POST['key'];
        $token = Model('mb_user_token');
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $username = $tokens['member_name'];
        $member_id = $tokens['member_id'];
        if($username == ""){
            $res = getarrres('-401','令牌为空','0');
            exit(json_encode($res));
        }else{
            $t_voucher = Model()->table("voucher_template")->where("voucher_t_id=$voucher_t_id")->find();
            $num = $t_voucher['voucher_t_giveout'];
            $total = $t_voucher['voucher_t_total'];
            if($num >= $total){
                $res = getarrres('-401','代金券已领取空！','0');
                exit(json_encode($res));
            }else{
                $map['voucher_t_id'] = $voucher_t_id;
                $map['voucher_title'] = $t_voucher['voucher_t_title'];
                $map['voucher_desc'] = $t_voucher['voucher_t_desc'];
                $map['voucher_start_date'] = $t_voucher['voucher_t_start_date'];
                $map['voucher_end_date'] = $t_voucher['voucher_t_end_date'];
                $map['voucher_price'] = $t_voucher['voucher_t_price'];
                $map['voucher_store_id'] = $t_voucher['voucher_t_store_id'];
                $map['voucher_limit'] = $t_voucher['voucher_t_limit'];
                $map['voucher_state'] = 1;
                $map['voucher_active_date'] = time();
                $map['voucher_owner_id'] = $member_id;
                $map['voucher_owner_name'] = $username;
                $rs = Model()->table("voucher")->insert($map);
                if($rs){
                    $nums['voucher_t_giveout'] = $num+1;
                    $r = Model()->table("voucher_template")->where("voucher_t_id=$voucher_t_id")->update($nums);
                    $res = getarrres('200','已领取！','1');
                    exit(json_encode($res));
                }else{
                    $res = getarrres('-401','领取失败！','0');
                    exit(json_encode($res));
                }
            }
            
        }
    }

    /**
     * 店铺活动
     */
    public function store_promotionOp(){
        $xianshi_array = Model('p_xianshi');
        $promotion['promotion'] = $condition = array();
        $condition['store_id'] = $_POST["store_id"];
        $xianshi = $xianshi_array->getXianshiList($condition);
        if(!empty($xianshi)){
            foreach($xianshi as $key=>$value){
                $xianshi[$key]['start_time_text'] = date('Y-m-d',$value['start_time']);
                $xianshi[$key]['end_time_text'] = date('Y-m-d',$value['end_time']);
            }       
            $promotion['promotion']['xianshi'] = $xianshi;
        }
        $mansong_array = Model('p_mansong');
        $mansong = $mansong_array->getMansongInfoByStoreID($_POST["store_id"]);
        if(!empty($mansong)){
            $mansong['start_time_text'] = date('Y-m-d',$mansong['start_time']);
            $mansong['end_time_text'] = date('Y-m-d',$mansong['end_time']);
            $promotion['promotion']['mansong'] = $mansong;
        }       
        $res = getarrres('200','成功',$promotion);
        exit(json_encode($res));
    }

    /**
     * 店铺信息 
     * (post)store_id：店铺id key：令牌
     */
    public function storeinfoOp(){
        $key  = $_POST['key'];
        if($key != ''){
            $memberid = getmemberid($key);
        }
        $store_id = (int) $_POST['store_id'];
        $store_online_info = Model('store')->getStoreOnlineInfoByID($store_id);

        $store_info = array();
        $store_info['store_id'] = $store_online_info['store_id'];
        $store_info['store_name'] = $store_online_info['store_name'];
        $store_info['member_id'] = $store_online_info['member_id'];

        // 店铺头像
        $store_info['store_avatar'] = $store_online_info['store_avatar']
        ? UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$store_online_info['store_avatar']
        : UPLOAD_SITE_URL.'/'.ATTACH_COMMON.DS.C('default_store_avatar');

        // 商品数
        $store_info['goods_count'] = (int) $store_online_info['goods_count'];

        // 店铺被收藏次数
        $store_info['store_collect'] = (int) $store_online_info['store_collect'];

        // 如果已登录 判断该店铺是否已被收藏
        if ($memberId) {
            $c = (int) Model('favorites')->getStoreFavoritesCountByStoreId($store_id, $memberId);
            $store_info['is_favorate'] = $c > 0;
        } else {
            $store_info['is_favorate'] = false;
        }

        // 是否官方店铺
        $store_info['is_own_shop'] = $store_online_info['is_own_shop'];

        // 动态评分
        // if ($store_info['is_own_shop']) {
        //     $store_info['store_credit_text'] = '官方店铺';
        // } else {
            // $store_info['store_credit_text'] = sprintf(
            //     '描述: %0.1f, 服务: %0.1f, 物流: %0.1f',
            //     $store_online_info['store_credit']['store_desccredit']['credit'],
            //     $store_online_info['store_credit']['store_servicecredit']['credit'],
            //     $store_online_info['store_credit']['store_deliverycredit']['credit']
            // );
        // }
        //描述
        $store_info['store_desccredit'] = $store_online_info['store_credit']['store_desccredit']['credit'];
        //服务
        $store_info['store_servicecredit'] = $store_online_info['store_credit']['store_servicecredit']['credit'];
        //物流
        $store_info['store_deliverycredit'] = $store_online_info['store_credit']['store_deliverycredit']['credit'];

        // 页头背景图
        $store_info['mb_title_img'] = $store_online_info['mb_title_img']
        ? UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$store_online_info['mb_title_img']
        : '';

        // 轮播
        $store_info['mb_sliders'] = array();
        $mbSliders = @unserialize($store_online_info['mb_sliders']);
        if ($mbSliders) {
            foreach ((array) $mbSliders as $s) {
                if ($s['img']) {
                    $s['imgUrl'] = UPLOAD_SITE_URL.DS.ATTACH_STORE.DS.$s['img'];
                    $store_info['mb_sliders'][] = $s;
                }
            }
        }

        $res = getarrres('200','成功',array(
            'store_info' => $store_info,
            ));
        exit(json_encode($res));
    }


    /**
     * 店铺商品 
     * (post)store_id：店铺id commend：是否为推荐 是：1 sort:综合排序：1 从高到低 2从低到高 3 人气排序 sale:销量优先 
     * last:最低价  tall:最高价
     */
    public function storegoodsOp(){
        $sort = $_POST['sort'];
        $sale = $_POST['sale'];
        $last = $_POST['last'];
        $tall = $_POST['tall'];
        $commend = $_POST['commend'];
        $store_id = $_POST['store_id'];
        $map['store_id'] = $store_id;
        if($commend != ''){
            $map['goods_commend'] = $commend;
        }
        $order = '';
        if($sort == 1){
            $order .= "goods_price desc";
        }  
        if($sort == 2){
            $order .= "goods_price asc";
        }  
        if($sort == 3){
            $order .= "goods_collect desc";
        }
        if($sale != ''){
            $order .= "goods_salenum desc";
        }
        if($last != '' && $tall != ''){
            $map['goods_price'] = array('BETWEEN',$last,$tall);
        }
        $map['goods_state'] = 1;
        $map['goods_verify'] = 1;
        $goods = Model('goods')->table('goods')->where($map)->order($order)->select(); 
        foreach ($goods as $key => $value) {
            $goods[$key]['goods_image'] = cthumb($value['goods_image'], 360, $value['store_id']);
        }
        $res = getarrres('200','成功',$goods);
        exit(json_encode($res));
    }

    /**
     * 店铺商品--收藏排行
     * (post)store_id：店铺id 
     */
    public function storecollOp(){
        $store_id = $_POST['store_id'];
        $map['store_id'] = $store_id;
        $map['goods_state'] = 1;
        $map['goods_verify'] = 1;
        $goods = Model()->table('goods')->where($map)->order('goods_collect desc')->limit(3)->select();
        foreach ($goods as $key => $value) {
            $goods[$key]['goods_image'] = cthumb($value['goods_image'], 360, $value['store_id']);
        }
        $res = getarrres('200','成功',$goods);
        exit(json_encode($res));
    }

    /**
     * 店铺商品--销量排行
     * (post)store_id：店铺id 
     */
    public function storesalenumOp(){
        $store_id = $_POST['store_id'];
        $map['store_id'] = $store_id;
        $map['goods_state'] = 1;
        $map['goods_verify'] = 1;
        $goods = Model()->table('goods')->where($map)->order('goods_salenum desc')->limit(3)->select();
        foreach ($goods as $key => $value) {
            $goods[$key]['goods_image'] = cthumb($value['goods_image'], 360, $value['store_id']);
        }
        $res = getarrres('200','成功',$goods);
        exit(json_encode($res));
    }

    /**
     * 店铺商品--新品上市
     * (post)store_id：店铺id  show_day：新品时间期限
     */
    public function storenewsOp(){
        $store_id = (int) $_POST['store_id'];
        if ($store_id <= 0) {
            output_data(array('goods_list'=>array()));
        }
        $show_day = ($t = intval($_POST['show_day']))>0?$t:30;
        $where = array();
        $where['store_id'] = $store_id;
        //$where['is_book'] = 0;//默认不显示预订商品
        $stime = strtotime(date('Y-m-d',time() - 86400*$show_day));
        $etime = $stime + 86400*($show_day+1);
        $where['goods_addtime'] = array('between',array($stime,$etime));
        $order = 'goods_addtime desc, goods_id desc';
        $model_goods = Model('goods');
        $goods_fields = $this->getGoodsFields();
        $where['goods_state'] = 1;
        $where['goods_verify'] = 1;
        $goods_list = $model_goods->getGoodsListByColorDistinct($where, $goods_fields, $order);
        if ($goods_list) {
            $goods_list = $this->_goods_list_extend($goods_list);
            foreach($goods_list as $k=>$v){
                $v['goods_addtime_text'] = $v['goods_addtime']?@date('Y年m月d日',$v['goods_addtime']):'';
                $goods_list[$k] = $v;
                $goods_list[$k]['goods_image'] = cthumb($v['goods_image'], 360, $v['store_id']);
            }
        }
        $res = getarrres('200','成功',$goods_list);
        exit(json_encode($res));
    }

    private function getGoodsFields()
    {
        return implode(',', array(
            'goods_id',
            'goods_commonid',
            'store_id',
            'store_name',
            'goods_name',
            'goods_price',
            'goods_promotion_price',
            'goods_promotion_type',
            'goods_marketprice',
            'goods_image',
            'goods_salenum',
            'evaluation_good_star',
            'evaluation_count',
            'is_virtual',
            'is_presell',
            'is_fcode',
            'have_gift',
            'goods_addtime',
            ));
    }

    /**
     * 热门搜索 
     */
    public function searchOp(){
        $list = @explode(',',C('hot_search'));
        if (!$list || !is_array($list)) { 
            $list = array();
        }
        if ($_COOKIE['hisSearch'] != '') {
            $his_search_list = explode('~', $_COOKIE['hisSearch']);
        }
        if (!$his_search_list || !is_array($his_search_list)) {
            $his_search_list = array(); 
            $flow1 = Model()->table("flowstat_1")->where("type='goods'")->order("clicknum desc")->limit(4)->select();
            foreach ($flow1 as $k => $v) {
                $goods_id = $v['goods_id'];
                $listss = Model()->table("goods")->where("goods_id=$goods_id")->find();
                $his_search_list[] = $listss['goods_name'];
            }
        }
        $res = getarrres('200','成功',array('list'=>$list,'his_list'=>$his_search_list));
        exit(json_encode($res));
    }

    /**
     * 手机注册
     */
    public function sms_registerOp(){  
        $phone = $_POST['phone'];
        $captcha = $_POST['captcha'];
        $password = $_POST['password'];
        $client = $_POST['client']; 
        $logic_connect_api = Logic('connect_apis');
        $state_data = $logic_connect_api->smsRegister($phone, $captcha, $password, $client);
        $state_data['error']=$state_data['state'];
        unset($state_data['state']);
        if($state_data['state']=='1'){
            $res = getarrres('200','成功',1);
        } else {
            $res = getarrres('-401',$state_data['msg'],0);
        }
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
            // $condition['log_type'] = 3;
            $model_sms_log = Model('sms_log');
            $sms_log = $model_sms_log->getSmsInfo($condition);
            if(empty($sms_log) || ($sms_log['add_time'] < TIMESTAMP-1800)) {//半小时内进行验证为有效
                $res = getarrres('-401','动态码错误或已过期，重新输入',0);
                exit(json_encode($res));
            }
            $res = getarrres('200','成功',true);
            exit(json_encode($res));
        }
        $res = getarrres('-401','验证失败',0);
        exit(json_encode($res));
    }

    /**
     * 发送短信验证码
     * (post) phone:电话号码 type：短信类型:1为注册,2为登录,3为找回密码
     */
    public function phonenumOp(){
        $phone = $_POST['phone'];
        // if (strlen($phone) == 11){
            $log_type = $_POST['type'];//短信类型:1为注册,2为登录,3为找回密码,4绑定手机
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
                        $res = getarrres('-401','系统没有开启手机注册功能',0);
                        exit(json_encode($res));
                    }
                        if(!empty($member)) {//检查手机号是否已被注册
                            $res = getarrres('-401','当前手机号已被注册，请更换其他号码。',0);
                            exit(json_encode($res));
                        }
                        $log_msg .= '申请注册会员，动态码：'.$captcha.'。';
                        break;
                        case '2':
                        if(C('sms_login') != 1) {
                            $res = getarrres('-401','系统没有开启手机登录功能',0);
                            exit(json_encode($res));
                        }
                        if(empty($member)) {//检查手机号是否已绑定会员
                            $res = getarrres('-401','当前手机号未注册，请检查号码是否正确。',0);
                            exit(json_encode($res));
                        }
                        $log_msg .= '申请登录，动态码：'.$captcha.'。';
                        $log_array['member_id'] = $member['member_id'];
                        $log_array['member_name'] = $member['member_name'];
                        break;
                        case '3':
                        if(C('sms_password') != 1) {
                            $res = getarrres('-401','系统没有开启手机找回密码功能',0);
                            exit(json_encode($res));
                        }
                        if(empty($member)) {//检查手机号是否已绑定会员
                            $res = getarrres('-401','当前手机号未注册，请检查号码是否正确。',0);
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
                        $res = getarrres('-401','参数错误',0);
                        exit(json_encode($res));
                        break;
                    }
                    if($state == 'true'){
                        $sms = new Sms();

                        $result = $sms->send($phone,$log_msg);

                    //if(!$result){
                        if($result){
                            $log_array['log_phone'] = $phone;
                            $log_array['log_captcha'] = $captcha;
                            $log_array['log_ip'] = getIp();
                            $log_array['log_msg'] = $log_msg;
                            $log_array['log_type'] = $log_type;
                            $log_array['add_time'] = time();
                            $model_sms_log->addSms($log_array);

                        // output_data(array('sms_time'=>10,'error'=>'1'));
                            $res = getarrres('200','成功',1);
                            exit(json_encode($res));
                        } else {
                            $res = getarrres('-401','手机短信发送失败',0);
                            exit(json_encode($res));
                        }
                    }
                }
            // } else {
            //     $res = getarrres('-401','手机格式不对',0);
            //     exit(json_encode($res));
            // }
        }

    /**
     * 设置默认地址
     */
    public function defaddressOp(){
        $addressid = $_POST['address_id'];
        $key  = $_POST['key'];
        $memberid = getmemberid($key);
        if(empty($memberid)){
            $res = getarrres('-401','登录已过期，请重新登录！',0);
            exit(json_encode($res));
        }
        $model = Model('address');
        $maps['is_default'] = 0;
        $wheres['member_id'] = $memberid;
        $model->table('address')->where($wheres)->update($maps);
        $map['is_default'] = 1;
        $where['address_id'] = $addressid;
        $where['member_id'] = $memberid;
        $model->table('address')->where($where)->update($map);
        $res = getarrres('200','',1);
        exit(json_encode($res));
    }

    /**
     * 所有地区列表
     */
    public function areaOp() {
        $model_area = Model("area");
        $map['area_parent_id'] = 0;
        $areaone = $model_area->table('area')->where($map)->select();
        foreach ($areaone as $k=>$v) {
            $parent = $v['area_id'];
            $mapes['area_parent_id'] = $parent;
            $areaone[$k]['clide'] = $model_area->table('area')->where($mapes)->select();
            foreach ($areaone[$k]['clide'] as $key=>$value) {
                $parents = $value['area_id'];
                $maps['area_parent_id'] = $parents;
                $areaone[$k]['clide'][$key]['clideclide'] = $model_area->table('area')->where($maps)->select();
            }
        }
        $res = getarrres('200','',$areaone);
        exit(json_encode($res));
    }

    /**
     * 我的足迹添加
     * goods_id：商品id key:令牌
     */
    public function addbrowseOp() {
        $key  = $_POST['key'];
        $memberid = getmemberid($key);
        if(empty($memberid)){
            $res = getarrres('-401','登录已过期，请重新登录！',0);
            exit(json_encode($res));
        }
        $model_area = Model("goods_browse");
        $goods = Model("goods");
        $goodsid = $_POST['goods_id'];
        $map['goods_id'] = $goodsid;
        $goodsarr = $goods->table('goods')->where($map)->find();
        $maps['goods_id'] = $goodsid;
        $maps['member_id'] = $memberid;
        $maps['browsetime'] = time();
        $maps['gc_id'] = $goodsarr['gc_id'];
        $maps['gc_id_1'] = $goodsarr['gc_id_1'];
        $maps['gc_id_2'] = $goodsarr['gc_id_2'];
        $maps['gc_id_3'] = $goodsarr['gc_id_3'];
        $goods->table('goods_browse')->insert($maps);
        $res = getarrres('200','',1);
        exit(json_encode($res));
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

        $res = getarrres('200','',array('version' => $version, 'url' => $url));
        exit(json_encode($res));
    }

	/**
     * 添加反馈
     * key 当前登录令牌
     * feedback 反馈内容
     */
    public function feedback_addOp() {
    	$feedback  = $_POST['feedback'];
    	$key  = $_POST['key'];
        $token = Model('mb_user_token');
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $memberid = $tokens['member_id'];
        $member_name = $tokens['member_name'];
        $client_type = $tokens['client_type'];
        $model_mb_feedback = Model('mb_feedback');

        $param = array();
        $param['content'] = $_POST['feedback'];
        $param['type'] = $client_type;
        $param['ftime'] = TIMESTAMP;
        $param['member_id'] = $memberid;
        $param['member_name'] = $member_name;

        $result = $model_mb_feedback->addMbFeedback($param);

        if($result) {
            $res = getarrres('200','保存成功',1);
        } else {
            $res = getarrres('-401','保存失败',0);
        }
        exit(json_encode($res));
    }

	/* 
	 * 商品列表
	 * (get) 传值：
	 *  key 排序方式 1-销量 2-浏览量 3-价格 空-按最新发布排序
	 *  order 排序方式 1-升序 2-降序
	 *  gc_id 分类编号
	 *  keyword 搜索关键字
	 *  gc_id和keyword二选一不能同时出现
	 *  own_shop 是否为平台自营
	 *  area_id 地区id
	 *  groupbuy 团购
	 *  xianshi 显示
	 *  virtual 虚拟
	 *  have_gift 是否拥有赠品
	 *  virtual 虚拟
	 * date 为 ：
	 */
	public function goodslistOp(){
		$model_goods = Model('goods');
        $model_search = Model('search');

        //查询条件
        $condition = array();
        if(!empty($_GET['gc_id']) && intval($_GET['gc_id']) > 0) {

            $condition['gc_id'] = $_GET['gc_id'];

        }elseif(!empty($_GET['keyword'])) {
            
            $condition['goods_name|goods_jingle'] = array('like', '%' . $_GET['keyword'] . '%');
            if ($_COOKIE['hisSearch'] == '') {
                $his_sh_list = array();
            } else {
                $his_sh_list = explode('~', $_COOKIE['hisSearch']);
            }
            if (strlen($_GET['keyword']) <= 20 && !in_array($_GET['keyword'],$his_sh_list)) {
                if (array_unshift($his_sh_list, $_GET['keyword']) > 8) {
                    array_pop($his_sh_list);
                }
            }
            setcookie('hisSearch', implode('~', $his_sh_list), time()+2592000, '/', SUBDOMAIN_SUFFIX ? SUBDOMAIN_SUFFIX : '', false);

        } elseif (!empty($_GET['barcode'])) {
            $condition['goods_barcode'] = $_GET['barcode'];
        } elseif (!empty($_GET['b_id']) && intval($_GET['b_id'] > 0)) {
            $condition['brand_id'] = intval($_GET['b_id']);
        }
        $price_from = preg_match('/^[\d.]{1,20}$/',$_GET['price_from']) ? $_GET['price_from'] : null;
        $price_to = preg_match('/^[\d.]{1,20}$/',$_GET['price_to']) ? $_GET['price_to'] : null;

        //所需字段
        $fieldstr = "goods_id,goods_commonid,store_id,goods_name,goods_jingle,goods_price,goods_promotion_price,goods_promotion_type,goods_marketprice,goods_image,goods_salenum,evaluation_good_star,evaluation_count";

        $fieldstr .= ',is_virtual,is_presell,is_fcode,have_gift,goods_jingle,store_id,store_name,is_own_shop';

        //排序方式
        $order = $this->_goods_list_order($_GET['key'], $_GET['order']);

        //全文搜索搜索参数
        $indexer_searcharr = $_GET;
        //搜索消费者保障服务
        $search_ci_arr = array();
        $_GET['ci'] = trim($_GET['ci'],'_');
        if ($_GET['ci'] && $_GET['ci'] != 0 && is_string($_GET['ci'])) {
            //处理参数
            $search_ci= $_GET['ci'];
            $search_ci_arr = explode('_',$search_ci);
            $indexer_searcharr['search_ci_arr'] = $search_ci_arr;
        }
        if ($_GET['own_shop'] == 1) {
            $indexer_searcharr['type'] = 1;
        }
        $indexer_searcharr['price_from'] = $price_from;
        $indexer_searcharr['price_to'] = $price_to;
        $indexer_searcharr['goods_state'] = 1;
        $indexer_searcharr['goods_verify'] = 1;
        //优先从全文索引库里查找
        list($goods_list,$indexer_count) = $model_search->indexerSearch($indexer_searcharr,$this->page);
        if (!is_null($goods_list)) {
            $goods_list = array_values($goods_list);
            pagecmd('setEachNum',$this->page);
            pagecmd('setTotalNum',$indexer_count);
        } else {
            //查询消费者保障服务
            $contract_item = array();
            if (C('contract_allow') == 1) {
                $contract_item = Model('contract')->getContractItemByCache();
            }
            //消费者保障服务
            if ($contract_item && $search_ci_arr) {
                foreach ($search_ci_arr as $ci_val) {
                    $condition["contract_{$ci_val}"] = 1;
                }
            }

            if ($price_from && $price_from) {
                $condition['goods_promotion_price'] = array('between',"{$price_from},{$price_to}");
            } elseif ($price_from) {
                $condition['goods_promotion_price'] = array('egt',$price_from);
            } elseif ($price_to) {
                $condition['goods_promotion_price'] = array('elt',$price_to);
            }
            if ($_GET['gift'] == 1) {
                $condition['have_gift'] = 1;
            }
            if ($_GET['own_shop'] == 1) {
                $condition['store_id'] = 1;
            }
            if (intval($_GET['area_id']) > 0) {
                $condition['areaid_1'] = intval($_GET['area_id']);
            }
            $condition['goods_state'] = 1;
            $condition['goods_verify'] = 1;
            //团购和限时折扣搜索
            $_tmp = array();
            if ($_GET['groupbuy'] == 1) {
                $_tmp[] = 1;
            }
            if ($_GET['xianshi'] == 1) {
                $_tmp[] = 2;
            }
            if ($_tmp) {
                $condition['goods_promotion_type'] = array('in',$_tmp);
            }
            unset($_tmp);

            //虚拟商品
            if ($_GET['virtual'] == 1) {
                $condition['is_virtual'] = 1;
            }

            $goods_list = $model_goods->getGoodsListByColorDistinct($condition, $fieldstr, $order);
        }
        $page_count = $model_goods->gettotalpage();
        //处理商品列表(团购、限时折扣、商品图片)
        $goods_list = $this->_goods_list_extend($goods_list);
        $res = getarrres('200','',array('goods_list' => $goods_list));
        exit(json_encode($res));
    }

	/**
     * 商品列表排序方式
     */
    private function _goods_list_order($key, $order) {
        $result = 'is_own_shop desc,goods_id desc';
        if (!empty($key)) {

            $sequence = 'desc';
            if($order == 1) {
                $sequence = 'asc';
            }

            switch ($key) {
                //销量
                case '1' :
                $result = 'goods_salenum' . ' ' . $sequence;
                break;
                //浏览量
                case '2' :
                $result = 'goods_click' . ' ' . $sequence;
                break;
                //价格
                case '3' :
                $result = 'goods_price' . ' ' . $sequence;
                break;
            }
        }
        return $result;
    }

    private function _goods_list_extend($goods_list) {
        //获取商品列表编号数组
        $commonid_array = array();
        $goodsid_array = array();
        foreach($goods_list as $key => $value) {
            $commonid_array[] = $value['goods_commonid'];
            $goodsid_array[] = $value['goods_id'];
        }

        //促销
        $model_store = Model('store');
        $groupbuy_list = Model('groupbuy')->getGroupbuyListByGoodsCommonIDString(implode(',', $commonid_array));
        $xianshi_list = Model('p_xianshi_goods')->getXianshiGoodsListByGoodsString(implode(',', $goodsid_array));
        foreach ($goods_list as $key => $value) {
            //抢购
            if (isset($groupbuy_list[$value['goods_commonid']])) {
                $goods_list[$key]['goods_price'] = $groupbuy_list[$value['goods_commonid']]['groupbuy_price'];
                $goods_list[$key]['group_flag'] = true;
            } else {
                $goods_list[$key]['group_flag'] = false;
            }

            //限时折扣
            if (isset($xianshi_list[$value['goods_id']]) && !$goods_list[$key]['group_flag']) {
                $goods_list[$key]['goods_price'] = $xianshi_list[$value['goods_id']]['xianshi_price'];
                $goods_list[$key]['xianshi_flag'] = true;
            } else {
                $goods_list[$key]['xianshi_flag'] = false;
            }

            //商品图片url
            $goods_list[$key]['goods_image_url'] = cthumb($value['goods_image'], 360, $value['store_id']);
            $store_info = $model_store->getStoreInfoByID($value['store_id']);
            $goods_list[$key]['store_name'] = $store_info['store_name'];

            unset($goods_list[$key]['store_id']);
            unset($goods_list[$key]['goods_commonid']);
            unset($goods_list[$key]['nc_distinct']);
        }

        return $goods_list;
    }

    /* 
     * 购物车、直接购买第一步:选择收获地址和配置方式
     * (post) 传值：key:令牌  cart_array:购买参数   ifcart:购物车购买标志  cart_id:购物车id   store_id:店铺id
     */
    public function buystep1Op(){
        $ifcart  = intval($_POST['ifcart']);
        $key  = $_POST['key'];
        $store_id = isset($_POST['store_id'])?$_POST['store_id']:'';
        $cart_array = $_POST['cart_array'];
        // $cart_id = $_POST['cart_id'];
        $memberid = getmemberid($key);
        if(empty($memberid)){
            $res = getarrres('-401','登录已过期，请重新登录！',1);
            exit(json_encode($res));
        }
        $cart_array = explode(',', $_POST['cart_array']);
        $cart_id = explode(',', $_POST['cart_id']);
        $model_cart = Model("cart");
        $model_goods = Model("goods");
        $buystepinfo = array();
        //购物车
        if($ifcart){
            if($store_id == ''){
                $store_id = array();
            }
            foreach ($cart_id as $k => $v) {
                $map['cart_id'] = $v;
                $cartarr = $model_cart->table("cart")->where($map)->find();
                $buystepinfo['amount'] += $cartarr['goods_price']*$cartarr['goods_num'];
                $goods_id = $cartarr['goods_id'];
                $arrs = $model_goods->table('goods')->where("goods_id=$goods_id")->find();
                $store_id[] = $arrs['store_id'];
                $arrs['goods_num'] = $cartarr['goods_num'];
                $buystepinfo['goods_list'][] = $arrs;
                $buystepinfo['goods_freight'] += $arrs['goods_freight'];
            }
            $store_id = implode(',', $store_id);
        //直接购买
        }else{
            foreach ($cart_array as $key => $value) {
                $arr = explode('|', $value);
                $goods_id = $arr[0];
                $num = $arr[1];
                $arrs = $model_goods->table('goods')->where("goods_id=$goods_id")->find();
                $arrs['goods_num'] = $num;
                $buystepinfo['goods_list'][] = $arrs;
                $goods_promotion_type = $arrs['goods_promotion_type'];
                $buystepinfo['goods_freight'] += $arrs['goods_freight'];
                //团购
                if($goods_promotion_type == 1){
                    $com['store_id'] = $store_id;
                    $com['goods_id'] = $arr[0];
                    // $com['starttime'] = array('elt',time());//<=
                    // $com['endtime'] = array('egt',time());//>=
                    $goodstg = Model('groupbuy')->table('groupbuy')->where($com)->find();
                }
                //限时折扣
                if($goods_promotion_type == 2){
                    $con['store_id'] = $store_id;
                    $model_xianshi = Model('p_xianshi');
                    $xianshiarr = $model_xianshi->table('p_xianshi')->where($con)->find();
                    $xianshiid = $xianshiarr['xianshi_id'];
                    $com['xianshi_id'] = $xianshiid;
                    $com['goods_id'] = $arr[0];
                    $goodsxs = Model('p_xianshi_goods')->table('p_xianshi_goods')->where($com)->find();
                }
                if($goodstg['groupbuy_price'] != ''){
                    $buystepinfo['amount'] += $goodstg['groupbuy_price'];
                }else if($goodsxs['xianshi_price'] != ''){
                    $buystepinfo['amount'] += $goodsxs['xianshi_price'];
                }else if($arrs['goods_price'] != ''){
                    $buystepinfo['amount'] += $arrs['goods_price'];
                }else{
                    $buystepinfo['amount'] += $arrs['goods_promotion_price'];
                }
                $buystepinfo['amount'] = $buystepinfo['amount']*$num;
            }
        }
        foreach ($buystepinfo['goods_list'] as $key => $value) {
            $buystepinfo['goods_list'][$key]['goods_image'] = cthumb($value['goods_image'], 240, $value['store_id']);
        }
        //代金券
        $vou['voucher_owner_id'] = $memberid;
        $vou['voucher_store_id'] = array('in',$store_id);

        $model = Model('address');
        $where['is_default'] = 1;
        $where['member_id'] = $memberid;
        $aaa = $model->table('address')->where($where)->find();
        if(empty($aaa)){
            $wheres['member_id'] = $memberid;
            $aaa = $model->table('address')->where($wheres)->order('address_id desc')->find();
        }
        $buystepinfo['address_id'] = $aaa['address_id'];

        $voucher = Model()->table("voucher")->where($vou)->find();
        if($buystepinfo['amount'] >= $voucher['voucher_limit']){
            $buystepinfo['voucher'] = $voucher['voucher_price'];
        }else{
            $buystepinfo['voucher'] = 0;
        }
        $buystepinfo['amount'] = floatval($buystepinfo['amount'])-floatval($buystepinfo['voucher']);
        $res = getarrres('200','成功',$buystepinfo);
        exit(json_encode($res));
    }


	/* 
	 * 购物车、直接购买第二步:保存订单入库，产生订单号，开始选择支付方式
	 * (post) 传值：
	 *   key 当前登录令牌
	 *   ifcart 购物车购买标志 1
	 *   goods_id 购买参数  商品id|数量|购买价格，商品2id|商品2数量|商品2购买价格
	 *   address_id 收货地址编号
	 *   vat_hash 发票信息（字符串）
     *   voucher 代金券，内容以竖线分割 voucher_t_id|voucher_price
     *   store_id 店铺id
     *   store_name 店铺名称
     *   store_str 多店铺购买信息字符串
     *   payment_code 支付代码 online
     *   shipping_fee 运费
     *   message 订单留言
     *   goods_amount 商品金额
	 * date 为 
	 */
	public function buystep2Op(){
        $ifcart  = $_POST['ifcart'];
        $key  = $_POST['key'];
        $token = Model('mb_user_token');
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $memberid = $tokens['member_id'];
        if(empty($memberid)){
            $res = getarrres('-401','登录已过期，请重新登录！',1);
            exit(json_encode($res));
        }
        if($_POST['address_id'] == ''){
            $res = getarrres('-401','请填写地址！',1);
            exit(json_encode($res));
        }
        file_put_contents('aaa.txt', print_r($_POST,true));
        if($ifcart){
            $mmap['member_id'] = $memberid;
            $marr = Model('member')->table('member')->where($mmap)->find();
            $pay_sn = $this->makePaySn($memberid);
            $store_str = explode(',',$_POST['store_str']);
            foreach ($store_str as $k => $val){
                $valarr = explode('|', $val);
                $goods_id = $valarr[5];
                if($valarr[4] <= 0){
                    $valarr[4] = $this->gettrans($_POST['address_id'],$goods_id);
                }
                file_put_contents('aaa.txt', print_r($valarr[4],true));
                $param['pay_sn'] = $pay_sn;
                $order_pay['pay_sn'] = $pay_sn;
                $order_pay['buyer_id'] = $memberid;
                $pay_id = Model('order_pay')->table('order_pay')->insert($order_pay);
                $order_sn = $this->makeOrderSn($pay_id);
                $param['order_sn'] = $order_sn;
                $param['store_id'] = $valarr[0];
                $param['store_name'] = $valarr[1];
                $param['buyer_id'] = $memberid;
                $param['buyer_name'] = $tokens['member_name'];
                $param['buyer_email'] = $marr['member_email'];
                $param['add_time'] = time();
                $param['payment_code'] = 'online';
                $param['goods_amount'] = floatval($valarr[3])+floatval($valarr[4]);
                $param['order_amount'] = floatval($valarr[3])+floatval($valarr[4]);
                $param['shipping_fee'] = $valarr[4];
                $model_order = Model('order');

                $order_id = $model_order->addOrder($param);

                $oparam['store_id'] = $valarr[0];
                $oparam['order_id'] = $order_id;
                $oparam['order_message'] = $valarr[2];
                $voucherstr = $_POST['voucher'];
                $voucherarr = explode('|', $voucherstr);
                $oparam['voucher_price'] = $voucherarr['1'];
                $oparam['voucher_code'] = $voucherarr['0'];
                $oparam['daddress_id'] = $_POST['address_id'];
                $address_id = $_POST['address_id'];
                $address = Model()->table("address")->where("address_id=$address_id")->find();
                $addarr['phone'] = $address['mob_phone'];
                $addarr['mob_phone'] = $address['mob_phone'];
                $addarr['address'] = $address['area_info'].'  '.$address['address'];
                $addarr['area'] = $address['area_info'];
                $addarr['street'] = $address['address'];
                $oparam['reciver_info'] = serialize($addarr);
                $oparam['reciver_name'] = $address['true_name'];
                $oparam['invoice_info'] = $this->buyEncrypt($_POST['vat_hash'],$memberid);
                $oparam['invoice_info'] = $_POST['vat_hash'];

                $model_order->addOrderCommon($oparam);

                $goods_id = $valarr[5];
                $goods_list = array();
                $goodsmap['goods_id'] = $goods_id;
                $goods_info = Model('goods')->table('goods')->where($goodsmap)->find();
                $cart_info = Model('cart')->table('cart')->where($goodsmap)->find();
                $goods_info['goods_num'] = $cart_info['goods_num'];
                $goods_info['goods_pay_price'] = $cart_info['goods_price'];
                $order_goods['order_id']        = $order_id;
                $order_goods['goods_id']        = $goods_info['goods_id'];
                $order_goods['goods_name']      = $goods_info['goods_name'];
                $order_goods['goods_price']     = $goods_info['goods_price'];
                $order_goods['goods_num']       = $goods_info['goods_num'];
                $order_goods['goods_image']     = $goods_info['goods_image'];
                $order_goods['goods_pay_price'] = $goods_info['goods_pay_price'];
                $order_goods['store_id']        = $val;
                $order_goods['buyer_id']        = $memberid;
                if($goods_info['goods_promotion_type'] != ''){
                    $order_goods['goods_type']      = $goods_info['goods_promotion_type'];
                }
                $order_goods['gc_id']           = $goods_info['gc_id'];
                Model('order_goods')->table('order_goods')->insert($order_goods);
            }
            $cartmap['buyer_id'] = $memberid;
            Model()->table("cart")->where($cartmap)->delete();
        }else{
            $mmap['member_id'] = $memberid;
            $marr = Model('member')->table('member')->where($mmap)->find();
            $pay_sn = $this->makePaySn($memberid);
            $param['pay_sn'] = $pay_sn;
            $order_pay['pay_sn'] = $pay_sn;
            $order_pay['buyer_id'] = $memberid;
            $pay_id = Model('order_pay')->table('order_pay')->insert($order_pay);
            $order_sn = $this->makeOrderSn($pay_id);

            $num = 0;
            $goods_id = explode(',', $_POST['goods_id']);
            $goods_list = array();
            $goodsarr = array();
            foreach ($goods_id as $ks => $v) {
                $goods_lists = explode('|', $v);
                $id = $goods_lists[0];
                $goodsmap['goods_id'] = $id;
                $goodsarr[] = $id;
                $goods_list[$ks] = Model('goods')->table('goods')->where($goodsmap)->find();
                $goods_list[$ks]['goods_num'] = $goods_lists[1];
                $goods_list[$ks]['goods_pay_price'] = $goods_lists[2];
                $num = $num + $goods_lists[2]*$goods_lists[1];
            }
            $goodstr = implode(',', $goodsarr);
            if($_POST['goods_amount'] == ''){
                $_POST['goods_amount'] = $num;
            }
            if($_POST['shipping_fee'] <= 0){
                $_POST['shipping_fee'] = $this->gettrans($_POST['address_id'],$goodstr);
            }
            file_put_contents('aaa.txt', print_r($_POST['shipping_fee'],true));
            $param['order_sn'] = $order_sn;
            $param['store_id'] = $_POST['store_id'];
            $param['store_name'] = $_POST['store_name'];
            $param['buyer_id'] = $memberid;
            $param['buyer_name'] = $tokens['member_name'];
            $param['buyer_email'] = $marr['member_email'];
            $param['add_time'] = time();
            $param['payment_code'] = $_POST['payment_code'];
            $param['goods_amount'] = floatval($_POST['goods_amount'])+floatval($_POST['shipping_fee']);
            $param['order_amount'] = floatval($_POST['goods_amount'])+floatval($_POST['shipping_fee']);
            $param['shipping_fee'] = $_POST['shipping_fee'];
            $model_order = Model('order');

            $order_id = $model_order->addOrder($param);

            $oparam['store_id'] = $_POST['store_id'];
            $oparam['order_id'] = $order_id;
            $oparam['order_message'] = $_POST['store_id'];
            $voucherstr = $_POST['voucher'];
            $voucherarr = explode('|', $voucherstr);
            $oparam['voucher_price'] = $voucherarr['1'];
            $oparam['voucher_code'] = $voucherarr['0'];
            $address_id = $_POST['address_id'];
            $address = Model()->table("address")->where("address_id=$address_id")->find();
            $addarr['phone'] = $address['mob_phone'];
            $addarr['mob_phone'] = $address['mob_phone'];
            $addarr['address'] = $address['area_info'].'  '.$address['address'];
            $addarr['area'] = $address['area_info'];
            $addarr['street'] = $address['address'];
            $oparam['reciver_info'] = serialize($addarr);
            $oparam['reciver_name'] = $address['true_name'];
            $oparam['daddress_id'] = $_POST['address_id'];
            $oparam['invoice_info'] = $this->buyEncrypt($_POST['vat_hash'],$memberid);
            $oparam['invoice_info'] = $_POST['vat_hash'];

            $model_order->addOrderCommon($oparam);

            foreach ($goods_list as $goods_info) {
                $order_goods['order_id']        = $order_id;
                $order_goods['goods_id']        = $goods_info['goods_id'];
                $order_goods['goods_name']      = $goods_info['goods_name'];
                $order_goods['goods_price']     = $goods_info['goods_price'];
                $order_goods['goods_num']       = $goods_info['goods_num'];
                $order_goods['goods_image']     = $goods_info['goods_image'];
                $order_goods['goods_pay_price'] = $goods_info['goods_pay_price'];
                $order_goods['store_id']        = $_POST['store_id'];
                $order_goods['buyer_id']          = $memberid;
                if($goods_info['goods_promotion_type'] != ''){
                    $order_goods['goods_type']      = $goods_info['goods_promotion_type'];
                }
                $order_goods['gc_id']           = $goods_info['gc_id'];
                Model()->table('order_goods')->insert($order_goods);
            }
        }
        $res = getarrres('200','',array('pay_sn' => $pay_sn,'available_predeposit' => $marr['available_predeposit'],'available_rc_balance' => $marr['available_rc_balance'],'amount'=>$param['order_amount']));
        exit(json_encode($res));
    }

    // 计算运费函数
    private function gettrans($address_id,$goods_id){
        if(empty($address_id)){
            return 0;
        }
        if(empty($goods_id)){
             return 0;
        }
        $address = Model()->table("address")->where("address_id=$address_id")->find();
        $pro = explode(' ', $address['address']);
        if(count($pro) < 3){
            $pro = explode(' ', $address['area_info']);
        }
        $arr = explode(',', $goods_id);
        $amount = 0;
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
        }
         
        return $amount;
    }

    /**
     * 加密
     * @param array/string $string
     * @param int $member_id
     * @return mixed arrray/string
     */
    public function buyEncrypt($string, $member_id) {
        $buy_key = sha1(md5($member_id.'&'.MD5_KEY));
        if (is_array($string)) {
            $string = serialize($string);
        } else {
            $string = strval($string);
        }
        return encrypt(base64_encode($string), $buy_key);
    }

    /**
     * 解密
     * @param string $string
     * @param int $member_id
     * @param number $ttl
     */
    public function buyDecrypt($string, $member_id, $ttl = 0) {
        $buy_key = sha1(md5($member_id.'&'.MD5_KEY));
        if (empty($string)) return;
        $string = base64_decode(decrypt(strval($string), $buy_key, $ttl));
        return ($tmp = @unserialize($string)) !== false ? $tmp : $string;
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
     * 查询默认地址信息
     */
    public function daddressOp(){
        $key  = $_POST['key'];
        $memberid = getmemberid($key);
        if(empty($memberid)){
            $res = getarrres('-401','登录已过期，请重新登录！',0);
            exit(json_encode($res));
        }
        $model = Model('address');
        $where['is_default'] = 1;
        $where['member_id'] = $memberid;
        $aaa = $model->table('address')->where($where)->find();
        if(empty($aaa)){
            $wheres['member_id'] = $memberid;
            $aaa = $model->table('address')->where($wheres)->order('address_id desc')->find();
        }
        $res = getarrres('200','',$aaa);
        exit(json_encode($res));
    }

    /* 
     * 支付方式
     * date 为 ：返回支付方式名称及名称代码
     */
    public function paywayOp(){
        $map['payment_state'] = 1;
        $field = "payment_name,payment_code";
        $paylist = Model('mb_payment')->table('mb_payment')->field($field)->where($map)->select();
        $res = getarrres('200','',$paylist);
        exit(json_encode($res));
    }

	/* 
	 * 验证密码
	 * (post) 传值：key:令牌 possword:密码
	 * date 为 ：
	 */
	public function checkpasswordOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',0);
            exit(json_encode($res));
        }
        if(empty($_POST['password'])) {
            $res = getarrres('-401','参数错误',0);
            exit(json_encode($res));
        }

        $model_member = Model('member');

        $member_info = $model_member->getMemberInfoByID($memberid);
        if($member_info['member_paypwd'] == md5($_POST['password'])) {
            $res = getarrres('200','',1);
        } else {
            $res = getarrres('-401','密码错误',0);
        }
        exit(json_encode($res));
    }

	/* 
	 * 更换收货地址
	 * (post) 传值：key:令牌 city_id:市级id area_id:地区id freight_hash：运费hash，第一步返回结果里有直接提交
	 * date 为 ：
	 */
	public function changeaddressOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',1);
            exit(json_encode($res));
        }
        $logic_buy = Logic('buy');
        if (empty($_POST['city_id'])) {
            $_POST['city_id'] = $_POST['area_id'];
        }

        $data = $logic_buy->changeAddr($_POST['freight_hash'], $_POST['city_id'], $_POST['area_id'], $memberid);
        if(!empty($data) && $data['state'] == 'success' ) {
            $res = getarrres('200','地址修改成功',$data);
        } else {
            $res = getarrres('-401','地址修改失败',0);
        }
        exit(json_encode($res));
    }

	/* 
	 * 支付实物订单
	 * (post) 传值：key:令牌 pay_sn：订单编号
	 * date 为 ：
	 */
	public function payOp(){
		$key = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',0);
            exit(json_encode($res));
        }
        $pay_sn = $_POST['pay_sn'];
        if (!preg_match('/^\d{18}$/',$pay_sn)){
            $res = getarrres('-401','该订单不存在',4);
            exit(json_encode($res));
        }

        //查询支付单信息
        $model_order= Model('order');
        $pay_info = $model_order->getOrderPayInfo(array('pay_sn'=>$pay_sn,'buyer_id'=>$memberid),true);
        if(empty($pay_info)){
            $res = getarrres('-401','该订单不存在',3);
            exit(json_encode($res));
        }
    
        //取子订单列表
        $condition = array();
        $condition['pay_sn'] = $pay_sn;
        $condition['order_state'] = array('in',array(ORDER_STATE_NEW,ORDER_STATE_PAY));
        $order_list = $model_order->getOrderList($condition,'','*','','',array(),true);
        if (empty($order_list)) {
            $res = getarrres('-401','未找到需要支付的订单',2);
            exit(json_encode($res));
        }

            //定义输出数组
        $pay = array();
            //支付提示主信息
            //订单总支付金额(不包含货到付款)
        $pay['pay_amount'] = 0;
            //充值卡支付金额(之前支付中止，余额被锁定)
        $pay['payed_rcb_amount'] = 0;
            //预存款支付金额(之前支付中止，余额被锁定)
        $pay['payed_pd_amount'] = 0;
            //还需在线支付金额(之前支付中止，余额被锁定)
        $pay['pay_diff_amount'] = 0;
            //账户可用金额
        $pay['member_available_pd'] = 0;
        $pay['member_available_rcb'] = 0;

        $logic_order = Logic('order');

            //计算相关支付金额
        foreach ($order_list as $key => $order_info) {
            if (!in_array($order_info['payment_code'],array('offline','chain'))) {
                if ($order_info['order_state'] == ORDER_STATE_NEW) {
                    $pay['payed_rcb_amount'] += $order_info['rcb_amount'];
                    $pay['payed_pd_amount'] += $order_info['pd_amount'];
                    $pay['pay_diff_amount'] += $order_info['order_amount'] - $order_info['rcb_amount'] - $order_info['pd_amount'];
                }
            }
        }
        if ($order_info['chain_id'] && $order_info['payment_code'] == 'chain') {
            $order_list[0]['order_remind'] = '下单成功，请在'.CHAIN_ORDER_PAYPUT_DAY.'日内前往门店提货，逾期订单将自动取消。';
            $flag_chain = 1;
        }

            //如果线上线下支付金额都为0，转到支付成功页
        if (empty($pay['pay_diff_amount'])) {
            $res = getarrres('-401','订单重复支付',1);
            exit(json_encode($res));
        }

        $payment_list = Model('mb_payment')->getMbPaymentOpenList();
        if(!empty($payment_list)) {
            foreach ($payment_list as $k => $value) {
                if ($value['payment_code'] == 'wxpay') {
                    unset($payment_list[$k]);
                    continue;
                }
                unset($payment_list[$k]['payment_id']);
                unset($payment_list[$k]['payment_config']);
                unset($payment_list[$k]['payment_state']);
                unset($payment_list[$k]['payment_state_text']);
            }
        }
            //显示预存款、支付密码、充值卡
        $pay['member_available_pd'] = $this->member_info['available_predeposit'];
        $pay['member_available_rcb'] = $this->member_info['available_rc_balance'];
        $pay['member_paypwd'] = $this->member_info['member_paypwd'] ? true : false;
        $pay['pay_sn'] = $pay_sn;
        $pay['payed_amount'] = ncPriceFormat($pay['payed_rcb_amount']+$pay['payed_pd_amount']);
        unset($pay['payed_pd_amount']);unset($pay['payed_rcb_amount']);
        $pay['pay_amount'] = ncPriceFormat($pay['pay_diff_amount']);
        unset($pay['pay_diff_amount']);
        $pay['member_available_pd'] = ncPriceFormat($pay['member_available_pd']);
        $pay['member_available_rcb'] = ncPriceFormat($pay['member_available_rcb']);
        $pay['payment_list'] = $payment_list ? array_values($payment_list) : array();
        $res = getarrres('200','支付成功',array('pay_info'=>$pay));
        exit(json_encode($res));
    }

	/**
     * AJAX验证支付密码
	 * (post) 传值：key:令牌 password：支付密码
     */
    public function check_pd_pwdOp(){
    	$key = $_POST['key'];
        $memberid = getmemberid($key);
        if(empty($memberid)){
            $res = getarrres('-401','登录已过期，请重新登录！',0);
            exit(json_encode($res));
        }
        if (empty($_POST['password'])) {
            $res = getarrres('-401','支付密码格式不正确',0);
            exit(json_encode($res));
        }
        $buyer_info = Model('member')->getMemberInfoByID($memberid,'member_paypwd');
        if ($buyer_info['member_paypwd'] != '') {
            if ($buyer_info['member_paypwd'] === md5($_POST['password'])) {
                $res = getarrres('200','支付密码验证成功',1);
                exit(json_encode($res));
            }
        }
        $res = getarrres('-401','支付密码验证失败',0);
        exit(json_encode($res));
    }

	/* 
	 * 发票信息列表
	 * (post) 传值：key:令牌
	 * date 为 ：
	 */
	public function invoicelistOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',1);
            exit(json_encode($res));
        }
        $model_invoice = Model('invoice');

        $condition = array();
        $condition['member_id'] = $memberid;

        $invoice_list = $model_invoice->getInvList($condition, 10, 'inv_id,inv_title,inv_content');
        if(!empty($invoice_list)){
            $res = getarrres('200','',$invoice_list);
        }else{
            $res = getarrres('-401','无发票信息','0');
        }
        if (strtoupper(CHARSET) == 'GBK'){
            $res = Language::getUTF8($res);
        } else {
            $res = $res;
        }
        exit(json_encode($res));
    }

	/* 
	 * 发票信息删除
	 * (post) 传值：key:令牌 inv_id:发票id
	 * date 为 ：
	 */
	public function invoicedelOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			 $res = getarrres('-401','登录已过期，请重新登录！',3);
            exit(json_encode($res));
        }
        $inv_id = intval($_POST['inv_id']);
        if($inv_id <= 0) {
            $res = getarrres('-401','参数错误',2);
            exit(json_encode($res));
        }
        $model_invoice = Model('invoice');
        $result = $model_invoice->delInv(array('inv_id'=>$inv_id,'member_id'=>$memberid));
        if($result) {
            $res = getarrres('200','',1);
        } else {
            $res = getarrres('-401','删除失败',0);
        }
        exit(json_encode($res));
    }

	/* 
	 * 发票信息添加
	 * (post) 传值：key:令牌 inv_title_select:发票类型 inv_title:发票抬头 inv_content:发票内容 可通过invoice_content_list接口获取可选值列表
	 * date 为 ：
	 */
	public function invoiceaddOp(){
		$inv_title_select  = $_POST['inv_title_select'];
		$inv_title  = $_POST['inv_title'];
		$inv_content  = $_POST['inv_content'];
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',1);
            exit(json_encode($res));
        }
        $model_invoice = Model('invoice');
        $data = array();
        $data['inv_state'] = 1;
        $data['inv_title'] = $inv_title_select == 'person' ? '个人' : $inv_title;
        $data['inv_content'] = $inv_content;
        $data['member_id'] = $memberid;
        $result = $model_invoice->addInv($data);
        if($result) {
            $res = getarrres('200','',array('inv_id' => $result));
        } else {
            $res = getarrres('-401','添加失败',0);
        }
        exit(json_encode($res));
    }

	 /**
     * 发票内容列表
     */
    public function invoice_content_listOp() {
        $invoice_content_list = array(
            '明细',
            '酒',
            '食品',
            '饮料',
            '玩具',
            '日用品',
            '装修材料',
            '化妆品',
            '办公用品',
            '学生用品',
            '家居用品',
            '饰品',
            '服装',
            '箱包',
            '精品',
            '家电',
            '劳防用品',
            '耗材',
            '电脑配件'
            );
        $res = getarrres('200','',array('invoice_content_list' => $invoice_content_list));
        exit(json_encode($res));
    }

	/* 
	 * 分类：品牌推荐
	 * date 为 ：品牌id:brand_id  品牌名称brand_name  品牌图片brand_pic
	 */
	public function brandOp(){
        file_put_contents("../a.txt",print_r('11',true).'---'.date("Y-m-d H:i:s",time()).'---$get---'.PHP_EOL, FILE_APPEND );
        $model = Model("brand");
        $map['brand_recommend'] = 1;
        $fields="brand_id,brand_name,brand_pic";
        $brand = $model->table('brand')->field($fields)->where($map)->select();
        foreach ($brand as $key => $val) {
            $brand[$key]['brand_pic'] = brandImage($val['brand_pic']);
        }
        if(!empty($brand)){
           $res = getarrres('200','',$brand);
       }else{
           $res = getarrres('-401','无推荐品牌','0');
       }
       if (strtoupper(CHARSET) == 'GBK'){
           $res = Language::getUTF8($res);
       } else {
           $res = $res;
       }
       exit(json_encode($res));
   }

	/* 
	 * 分类：分类名称
	 * date 为 ：分类信息
	 */
	public function goodsclassOp(){
		$model = Model("goods_class");
		$map['gc_parent_id'] = 0;
		$fields = "gc_name,gc_id";
		$class_list = $model->table("goods_class")->field($fields)->where($map)->select();

        $goods_class_array = $model->table('goods_class')->getGoodsClassForCacheModel();
        foreach ($class_list as $key => $value) {
            $gc_id = $value['gc_id'];
            $class_list[$key]['gc_thumb'] = UPLOAD_SITE_URL.'/'.ATTACH_COMMON.'/category-pic-'.$gc_id.'.jpg';

            $class_list[$key]['text'] = '';
            $child_class_string = $goods_class_array[$value['gc_id']]['child'];
            $child_class_array = explode(',', $child_class_string);
            // var_dump($childclass_array);
            //子分类标题
            if($child_class_array[0]['gc_id'] != null && $child_class_array[0]['gc_name'] != null){
                foreach ($child_class_array as $k=>$child_class) {
                    // $class_list[$key]['text'] .= $goods_class_array[$child_class]['gc_name'] . '/';
                    // $class_list[$key]['text_id'] .= $goods_class_array[$child_class]['gc_id'] . '/';
                    $class_list[$key]['child'][$k]['gc_id'] = $goods_class_array[$child_class]['gc_id'];
                    $class_list[$key]['child'][$k]['gc_name'] = $goods_class_array[$child_class]['gc_name'];
                    $child_class_child = $goods_class_array[$value['gc_id']]['childchild'];
                    $child_child_array = explode(',', $child_class_child);
                    $a = 0;
                    foreach ($child_child_array as $ks=>$childs) {
                        $model = Model("goods_class");
                        $mapc['gc_id'] = $childs;
                        $mapc['gc_parent_id'] = $goods_class_array[$child_class]['gc_id'];
                        $fieldc = "gc_name,gc_id";
                        $class_child = $model->table("goods_class")->field($fieldc)->where($mapc)->find();
                        if($class_child['gc_id'] != null && $class_child['gc_name'] != null){
                            $class_list[$key]['child'][$k]['childchild'][$a]['gc_id'] = $class_child['gc_id'];
                            $class_list[$key]['child'][$k]['childchild'][$a]['gc_name'] = $class_child['gc_name'];
                            $a++;
                        }else{
                            $class_list[$key]['child'][$k]['childchild'] = array();
                        }
                    }
                }
            }else{
                $class_list[$key]['child'] = array();
            }
        }
        if(!empty($class_list)){
            $res = getarrres('200','',$class_list);
        }else{
            $res = getarrres('-401','无分类','0');
        }
        if (strtoupper(CHARSET) == 'GBK'){
            $res = Language::getUTF8($res);
        } else {
            $res = $res;
        }
        echo json_encode($res);
    }

	/* 
	 * 商品详情
	 * (get)传值：商品id:goods_id key:令牌
	 * date 为 
	 */
	public function goodsdetailOp(){
		$goods_id = intval($_POST['goods_id']); 
        $key  = isset($_POST['key'])?$_POST['key']:'';
        if($goods_id == ''){
           $res = getarrres('-401','参数为空','0');
           exit(json_encode($res));
        }
        $model = Model("goods");
        $map['goods_id'] = $goods_id;
        $fields = "goods_id,goods_name,goods_jingle,store_name,store_id,goods_price,goods_storage,goods_image,evaluation_good_star,goods_salenum,transport_id,goods_freight,evaluation_count,goods_commonid,is_own_shop,goods_state,goods_verify";
        $goods = $model->table('goods')->field($fields)->where($map)->find();
        if(empty($goods)){
            $res = getarrres('-401','商品不存在！','0');
            exit(json_encode($res));
        }
        
        if($goods['goods_state'] != 1){
            $res = getarrres('-401','商品已下架！','0');
            exit(json_encode($res));
        }
        if($goods['goods_verify'] != 1){
            $res = getarrres('-401','商品未通过审核！','0');
            exit(json_encode($res));
        }
        $goodscid = $goods['goods_commonid'];
        $goodsc = Model()->table("goods_common")->where("goods_commonid = $goodscid")->field("goods_body")->find();
        $bodys = str_replace("\n","",$goodsc['goods_body']);
        $bodys = str_replace("\r","",$bodys);
        $bodys = str_replace("\t","",$bodys);
        $goods['goods_body'] = $bodys;
        $goods['goods_image'] = cthumb($goods['goods_image'], 360, $goods['store_id']);
        $area = $goods['transport_id']; 
        $areaarr = Model()->table("transport")->where("id=$area")->find();
        if(empty($areaarr)){
            $goods['transport_title'] = "全国";
        }else{
            $goods['transport_title'] = $areaarr['title'];
        }
        if($goods['goods_freight'] == 0){
            $goods['goods_freight'] = '免运费';
        }
        if($key != ''){
            $memberid = getmemberid($key);
            $mapss['member_id'] = $memberid;
            $mapss['fav_id'] = $goods['goods_id'];
            $mapss['fav_type'] = 'goods';
            $count = Model()->table("favorites")->where($mapss)->find();
            if(!empty($count)){
                $goods['collect'] = 1;
            }else{
                $goods['collect'] = 0;
            } 
        }else{
            $goods['collect'] = 0;
        }
        if($goods['evaluation_good_star'] == 0){
            $goods['evaluation_good_star'] = '0%';
        }
        if($goods['evaluation_good_star'] == 1){
            $goods['evaluation_good_star'] = '20%';
        }
        if($goods['evaluation_good_star'] == 2){
            $goods['evaluation_good_star'] = '40%';
        }
        if($goods['evaluation_good_star'] == 3){
            $goods['evaluation_good_star'] = '60%';
        }
        if($goods['evaluation_good_star'] == 4){
            $goods['evaluation_good_star'] = '80%';
        }
        if($goods['evaluation_good_star'] == 5){
            $goods['evaluation_good_star'] = '100%';
        }
        $store_id = (int) $goods['store_id'];
        $stomap['store_id'] = $store_id;
        $storelast = Model("store")->table("store")->field("serviceid")->where($stomap);
        $store_online_info = Model('store')->getStoreOnlineInfoByID($store_id);
        $store_info = array();
        $serviceid = $store_online_info['serviceid'];
        if($serviceid != ''){
            $goods['customer_serviceID'] = $serviceid;
        }else{
            $goods['customer_serviceID'] = '';
        }
        $store_info['store_id'] = $store_online_info['store_id'];
        $store_info['store_name'] = $store_online_info['store_name'];
        $store_info['member_id'] = $store_online_info['member_id'];

        // 店铺头像
        $store_info['store_avatar'] = $store_online_info['store_avatar']
        ? UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$store_online_info['store_avatar']
        : UPLOAD_SITE_URL.'/'.ATTACH_COMMON.DS.C('default_store_avatar');

        // 商品数
        $store_info['goods_count'] = (int) $store_online_info['goods_count'];

        // 店铺被收藏次数
        $store_info['store_collect'] = (int) $store_online_info['store_collect'];

        // 如果已登录 判断该店铺是否已被收藏
        if ($memberId) {
            $c = (int) Model('favorites')->getStoreFavoritesCountByStoreId($store_id, $memberId);
            $store_info['is_favorate'] = $c > 0;
        } else {
            $store_info['is_favorate'] = false;
        }

        $store_info['is_own_shop'] = $goods['is_own_shop'];
        //描述
        $store_info['store_desccredit'] = $store_online_info['store_credit']['store_desccredit']['credit'];
        //服务
        $store_info['store_servicecredit'] = $store_online_info['store_credit']['store_servicecredit']['credit'];
        //物流
        $store_info['store_deliverycredit'] = $store_online_info['store_credit']['store_deliverycredit']['credit'];

        // 页头背景图
        $store_info['mb_title_img'] = $store_online_info['mb_title_img']
        ? UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$store_online_info['mb_title_img']
        : '';

        // 轮播
        $store_info['mb_sliders'] = array();
        $mbSliders = @unserialize($store_online_info['mb_sliders']);
        if ($mbSliders) {
            foreach ((array) $mbSliders as $s) {
                if ($s['img']) {
                    $s['imgUrl'] = UPLOAD_SITE_URL.DS.ATTACH_STORE.DS.$s['img'];
                    $store_info['mb_sliders'][] = $s;
                }
            }
        }

        $goods['store'] = $store_info;
        if(!empty($goods)){
           $res = getarrres('200','',$goods);
        }else{
           $res = getarrres('-401','商品不存在','1');
        }
        echo json_encode($res);
    }

	/* 
	 * 购物车列表
	 * (post)传值：key:令牌
	 * date 为 
	 */
	public function cartlistOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',1);
            exit(json_encode($res));
        }
        $model_cart = Model('cart');
        $condition = array('buyer_id' => $memberid);
        $cart_list  = $model_cart->listCart('db', $condition);
            // 购物车列表 [得到最新商品属性及促销信息]
        $cart_list = logic('buy_1')->getGoodsCartList($cart_list);

        $model_goods = Model('goods');
        $sum = 0;
        $cart_a = array();
        foreach ($cart_list as $key => $val) {
            $cart_list[$key]['goods_sum'] = ncPriceFormat($val['goods_price'] * $val['goods_num']);
            $cart_list[$key]['goods_image'] = cthumb($val['goods_image'], 360, $val['store_id']);
        }
        $num = 0;
        foreach ($cart_list as $k => $v) {
            $num = $num+$v['goods_sum'];
        }
        $cart_list['num'] = $num;
        if(!empty($cart_list)){
           $array = getarrres('200','',$cart_list);
        }else{
           $array = getarrres('-401','没有购买记录','0');
        }	
		/**
		 * 转码
		 */
		if (strtoupper(CHARSET) == 'GBK'){
			$array = Language::getUTF8($array);
		} else {
			$array = $array;
		}
		exit(json_encode($array));
	}

	/* 
	 * 购物车添加
	 * (post)传值：key:令牌 goods_id:商品id  quantity:购买数量
	 * date 为 
	 */
	public function cartaddOp(){
		$key  = $_POST['key'];
		$goods_id = intval($_POST['goods_id']);
        $quantity = intval($_POST['quantity']);
        if(empty($key)){
           $res = getarrres('-401','未登录，请登录！',3);
           exit(json_encode($res)); 
        }
        if(empty($goods_id) ||empty($quantity) || $goods_id <= 0 || $quantity <= 0){
           $res = getarrres('-401','字段不完整！',3);
           exit(json_encode($res));
        }
        $memberid = getmemberid($key);
        if(empty($memberid)){
            $res = getarrres('-401','登录已过期，请重新登录！',2);
            exit(json_encode($res));
        }

        $model_goods = Model('goods');
        $model_cart	= Model('cart');
        $logic_buy_1 = Logic('buy_1');

        $goods_info = $model_goods->getGoodsOnlineInfoAndPromotionById($goods_id);

        //验证是否可以购买
        if(empty($goods_info)) {
            $res = getarrres('-401','商品已下架不能购买',4);
            exit(json_encode($res));
        }

		//抢购
        $logic_buy_1->getGroupbuyInfo($goods_info);

		//限时折扣
        $logic_buy_1->getXianshiInfo($goods_info,$quantity);

        if ($goods_info['store_id'] == $memberid) {
            $res = getarrres('-401','不能购买自己发布的商品',5);
            exit(json_encode($res));
        }
        if(intval($goods_info['goods_storage']) < 1 || intval($goods_info['goods_storage']) < $quantity) {
            $res = getarrres('-401','库存不足',6);
            exit(json_encode($res));
        }

        $param = array();
        $param['buyer_id']	= $memberid;
        $param['store_id']	= $goods_info['store_id'];
        $param['goods_id']	= $goods_info['goods_id'];
        $param['goods_name'] = $goods_info['goods_name'];
        $param['goods_price'] = $goods_info['goods_price'];
        $param['goods_image'] = $goods_info['goods_image'];
        $param['store_name'] = $goods_info['store_name'];

        $result = $model_cart->addCart($param, 'db', $quantity);
        if($result) {
            $res = getarrres('200','',1);
        } else {
            $res = getarrres('-401','添加失败',0);
        }
        exit(json_encode($res));
    }

	/* 
	 * 购物车删除
	 * (post)传值：key:令牌 cart_id:购物车id 
	 * date 为 ：分类信息
	 */
    public function cartdelOp(){
        $key  = $_POST['key'];
        $memberid = getmemberid($key);
        if(empty($memberid)){
            $res = getarrres('-401','登录已过期，请重新登录！',0);
            exit(json_encode($res));
        }
        $cart_id = intval($_POST['cart_id']);

        $model_cart = Model('cart');

        if($cart_id > 0) {
            $condition = array();
            $condition['buyer_id'] = $memberid;
            $condition['cart_id'] = $cart_id;

            $model_cart->delCart('db', $condition);
        }

        $res = getarrres('200','删除成功',1);
        exit(json_encode($res));
    }

	/* 
	 * 购物车修改数量
	 * (post)传值：key:令牌 cart_id:购物车id  quantity:更改后的购物数量
	 * date 为 
	 */
	public function cartbuynumOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',2);
            exit(json_encode($res));
        }
        $cart_id = intval(abs($_POST['cart_id']));
        $quantity = intval(abs($_POST['quantity']));
        if(empty($cart_id) || empty($quantity)) {
            $res = getarrres('-401','参数有误',3);
            exit(json_encode($res));
        }

        $model_cart = Model('cart');

        $cart_info = $model_cart->getCartInfo(array('cart_id'=>$cart_id, 'buyer_id' => $memberid));

        //检查是否为本人购物车
        if($cart_info['buyer_id'] != $memberid) {
            $res = getarrres('-401','参数有误',3);
            exit(json_encode($res));
        }

        //检查库存是否充足
        if(!$this->_check_goods_storage($cart_info, $quantity, $memberid)) {
            $res = getarrres('-401','超出限购数或库存不足',4);
            exit(json_encode($res));
        }

        $data = array();
        $data['goods_num'] = $quantity;
        $update = $model_cart->editCart($data, array('cart_id'=>$cart_id));
        if ($update) {
            $return = array();
            $return['quantity'] = $quantity;
            $return['goods_price'] = ncPriceFormat($cart_info['goods_price']);
            $return['total_price'] = ncPriceFormat($cart_info['goods_price'] * $quantity);
            $res = getarrres('200','修改成功',$return);
        } else {
            $res = getarrres('-401','修改失败',1);
        }
        exit(json_encode($res));
    }

	/**
     * 检查库存是否充足
     */
    private function _check_goods_storage(& $cart_info, $quantity, $member_id) {
        $model_goods= Model('goods');
        $model_bl = Model('p_bundling');
        $logic_buy_1 = Logic('buy_1');

        if ($cart_info['bl_id'] == '0') {
            //普通商品
            $goods_info	= $model_goods->getGoodsOnlineInfoAndPromotionById($cart_info['goods_id']);

            //团购
            $logic_buy_1->getGroupbuyInfo($goods_info);
            if ($goods_info['ifgroupbuy']) {
                if ($goods_info['upper_limit'] && $quantity > $goods_info['upper_limit']) {
                    return false;
                }
            }

                //限时折扣
            $logic_buy_1->getXianshiInfo($goods_info,$quantity);

            if(intval($goods_info['goods_storage']) < $quantity) {
                return false;
            }
            $goods_info['cart_id'] = $cart_info['cart_id'];
            $cart_info = $goods_info;
        } else {
                //优惠套装商品
                $bl_goods_list = $model_bl->getBundlingGoodsList(array('bl_id' => $cart_info['bl_id']));
                $goods_id_array = array();
                foreach ($bl_goods_list as $goods) {
                    $goods_id_array[] = $goods['goods_id'];
                }
                $bl_goods_list = $model_goods->getGoodsOnlineListAndPromotionByIdArray($goods_id_array);

        		    //如果有商品库存不足，更新购买数量到目前最大库存
                foreach ($bl_goods_list as $goods_info) {
                  if (intval($goods_info['goods_storage']) < $quantity) {
                    return false;
                }
            }
        }
        return true;
    }

	/**
     * 购物车数量
     * (post)传值：key:令牌
	 * date 为 
     */
	public function cartcountOp() {	
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',0);
         exit(json_encode($res));
        }	
        $model_cart = Model('cart');
        $count = $model_cart->countCartByMemberId($memberid);
        $res = getarrres('200','',$count);
        exit(json_encode($res));
    }

	//热卖商品
    public function hotgoodsOp() {
        $model_class  = Model('goods');
        $map['goods_state'] = 1;
        $map['goods_verify'] = 1;
        $goods_class  = $model_class->field('goods_name,goods_id,goods_price,goods_image,store_id,gc_id')->order('goods_salenum desc')->where($map)->limit('2')->select();
        foreach ($goods_class as $key => $val) {
        $goods_class[$key]['goods_image'] = cthumb($val['goods_image'], 360, $val['store_id']);;
        }
        if(!empty($goods_class)){
        $array = getarrres('200','',$goods_class);
        }else{
        $array = getarrres('-401','未找到商品','0');
        }	
		/**
		 * 转码
		 */
		if (strtoupper(CHARSET) == 'GBK'){
			$array = Language::getUTF8($array);
		} else {
			$array = $array;
		}
		exit(json_encode($goods_class));
	}

	/* 
	 * 登录接口
	 * (post)传值：username:会员名  password:密码 client:客户端类型
	 * date 为 ：登录成功:返回key的值，密码错误:1，用户名不存在:2
	 */
	public function loginOp(){
		$username = $_POST['username'];  
        $password = $_POST['password'];  
        $client  = $_POST['client']; 
        if(empty($username) || empty($password) || empty($client)) {
            $array = getarrres('-401','参数有误','2');
            exit(json_encode($array));
        }

        $model_member = Model('member');

        $array = array();
        $array['member_name']   = $username;
        $array['member_passwd'] = md5($password);
        $member_info = $model_member->getMemberInfo($array);
        if(empty($member_info) && preg_match('/^0?(13|15|17|18|14)[0-9]{9}$/i', $_POST['username'])) {
        //根据会员名没找到时查手机号
            $array = array();
            $array['member_mobile']   = $username;
            $array['member_passwd'] = md5($password);
            $member_info = $model_member->getMemberInfo($array);
        }

        if(empty($member_info) && (strpos($_POST['username'], '@') > 0)) {//按邮箱和密码查询会员
            $array = array();
            $array['member_email']   = $username;
            $array['member_passwd'] = md5($password);
            $member_info = $model_member->getMemberInfo($array);
        }

        if(is_array($member_info) && !empty($member_info)) {
            $token = $this->_get_token($member_info['member_id'], $member_info['member_name'], $client);
            if($token) {
                ini_set("display_errors", "On");
                error_reporting(E_ALL | E_STRICT);
                require_once("../src/JPush/JPush.php");

                $app_key = 'ae1d588ecf543c12249732df';
                $master_secret = '4517e2aab7e88df3e1c0411b';

                $TAG1 = "users";
                $ALIAS1 = "alias1".$member_info['member_id'];
                $REGISTRATION_ID1 = $_POST['rid'];

                // 初始化
                $client = new JPush($app_key, $master_secret);

                // 更新指定的设备的Alias(亦可以增加/删除Tags)
                $result = $client->device()->updateDevice($REGISTRATION_ID1, $ALIAS1);
                $result = $client->device()->updateTag($TAG1, array($REGISTRATION_ID1));

                $logindata = array('username' => $member_info['member_name'], 'userid' => $member_info['member_id'], 'key' => $token,'tel'=>$member_info['member_mobile'],'bname'=>$ALIAS1,'tag'=>$TAG1);
                // $_SESSION['wap_member_info'] = $logindata;

                $array = getarrres('200','登录成功',$logindata);

            } else {
                $array = getarrres('-401','登录失败','1');
            }
        } else {
            $array = getarrres('-401','用户名密码错误','0');
        }  
        exit(json_encode($array));
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
            return null;
        }

    }

	/* 
	 * 注册接口
	 * (post)传值：username:会员名  password:密码 password_confirm:确认密码 email:邮箱  client:客户端类型
	 * date 为 ：登录成功:返回key的值，1：已注册，2：参数有误 3,两次输入密码不一致
	 */
	public function registerOp(){
		$username = $_POST['username'];  
        $password = $_POST['password']; 
        $password_confirm = $_POST['password_confirm']; 
        $email = $_POST['email']; 
        $client  = $_POST['client'];
        if (process::islock('reg')){
           $array = getarrres('-401','您的操作过于频繁，请稍后再试','0');
           exit(json_encode($array));
       } 
       $model_member	= Model('member');
       $register_info = array();
       $register_info['username'] = $username;
       $register_info['password'] = $password;
       $register_info['password_confirm'] = $password_confirm;
       $register_info['email'] = $email;

       $member_info = $model_member->register($register_info);
       if(!isset($member_info['error'])) {
            process::addprocess('reg');
            $token = $this->_get_token($member_info['member_id'], $member_info['member_name'], $client);
            if($token) {
                $array = getarrres('200','',array('username' => $member_info['member_name'], 'userid' => $member_info['member_id'], 'key' => $token));
                exit(json_encode($array));
            } else {
                $array = getarrres('-401','注册失败',1);
                exit(json_encode($array));
            }
        } else {
            $array = getarrres('-401',$member_info['error'],2);
            exit(json_encode($array));
       }
    }

	/* 
	 * 注销接口
	 * (post)传值：username:会员名  key:令牌 client:客户端类型
	 * 成功返回1 不成功返回0
	 */
	public function logoutOp(){
		$username = $_POST['username'];
		$client  = $_POST['client'];
		$key  = $_POST['key'];
		if ($username == '' || $key == '' || $client == '') {  
            $res = getarrres('-401','参数有误','0'); 
            exit(json_encode($res));
        } 
        $token = Model('mb_user_token');
        $data['member_name'] = $username;
        $data['token'] = $key;
        $data['client_type'] = $client;
        $token->where($data)->delete();
        $res = getarrres('200','成功',1);
        echo json_encode($res);  
        exit();
    }

	/* 
	 * 我的商城 
	 * (post)传值： key:令牌
	 * 返回：用户名username 用户头像avator  积分point  预存款predepoit 充值卡available_rc_balance 未查到值返回 0用户未找到 1token未找到
	 */
	public function ourinfoOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期！',1);
            exit(json_encode($res));
        }
        $member = Model('member');
        $datas['member_id'] = $memberid;
        $memberarr = $member->where($datas)->find();
        $avatar = getMemberAvatar($memberarr['member_avatar']);
        if(!empty($memberarr)){
            $arr = array('username'=>$memberarr['member_name'],'avator'=>$avatar,'point'=>$memberarr['member_points'],
         'predepoit'=>$memberarr['available_predeposit'],'available_rc_balance'=>$memberarr['available_rc_balance'],'member_id'=>$memberid);
            $res = getarrres('200','',$arr);
        }else{
            $res = getarrres('-401','未找到用户',0);
        }
        echo json_encode($res);
        exit(); 
    }

	/* 
	 * 商品收藏
	 * (post)传值： key:令牌
	 * 返回：商品名称goods_name 商品图片地址goods_image_url  商品价格goods_price  收藏编号fav_id  未查到值返回 0用户未找到 1token未找到
	 */
	public function goodscollectOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',1);
            exit(json_encode($res));
        }
        $model_favorites = Model('favorites');
        $favorites_list = $model_favorites->getGoodsFavoritesList(array('member_id'=>$memberid, '*'));
        $favorites_id = '';
        foreach ($favorites_list as $value){
            $favorites_id .= $value['fav_id'] . ',';
        }
        $favorites_id = rtrim($favorites_id, ',');

        $model_goods = Model('goods');
        $field = 'goods_id,goods_name,goods_price,goods_image,store_id';
        $goods_list = $model_goods->getGoodsList(array(
            'goods_id' => array('in', $favorites_id),
            ), $field);
        foreach ($goods_list as $key=>$value) {
            $goods_list[$key]['fav_id'] = $value['goods_id'];
            $goods_list[$key]['goods_image_url'] = cthumb($value['goods_image'], 240, $value['store_id']);
        }
        $res = getarrres('200','',array('favorites_list' => $goods_list));
        exit(json_encode($res));
    }

	/* 
	 * 添加商品收藏
	 * (post)传值： key:令牌 goods_id:商品id
	 * 返回
	 */
	public function addgoodscollectOp(){
		$goods_id  = intval($_POST['goods_id']);
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',2);
            exit(json_encode($res));
        }

        $favorites_model = Model('favorites');

		//判断是否已经收藏
        $favorites_info = $favorites_model->getOneFavorites(array('fav_id'=>$goods_id,'fav_type'=>'goods','member_id'=>$memberid));
        if(!empty($favorites_info)) {
            $res = getarrres('-401','您已经收藏了该商品',3);
            exit(json_encode($res));
        }

		//判断商品是否为当前会员所有
        $goods_model = Model('goods');
        $goods_info = $goods_model->getGoodsInfoByID($goods_id);
        $seller_info = Model('seller')->getSellerInfo(array('member_id'=>$memberid));
        if ($goods_info['store_id'] == $seller_info['store_id']) {
            $res = getarrres('-401','您不能收藏自己发布的商品',4);
            exit(json_encode($res));
        }

            //添加收藏
        $insert_arr = array();
        $insert_arr['member_id'] = $memberid;
        $insert_arr['member_name'] = $memberid;
        $insert_arr['fav_id'] = $goods_id;
        $insert_arr['fav_type'] = 'goods';
        $insert_arr['fav_time'] = TIMESTAMP;
        $result = $favorites_model->addFavorites($insert_arr);

        if ($result){
                //增加收藏数量
            $goods_model->editGoodsById(array('goods_collect' => array('exp', 'goods_collect + 1')), $goods_id);
            $res = getarrres('200','收藏成功',1);
        }else{
            $res = getarrres('-401','收藏失败',0);
        }
        exit(json_encode($res));
    }

	/* 
	 * 删除商品收藏
	 * (post)传值： key:令牌 fav_id:收藏id
	 * 返回
	 */
	public function delgoodscollectOp(){
		$fav_id = intval($_POST['fav_id']);
        if($fav_id == ''){
            $fav_id = intval($_POST['goods_id']);
        }
        $key = $_POST['key'];
        $memberid = getmemberid($key);
        if(empty($memberid)){
           $res = getarrres('-401','登录已过期，请重新登录！',2);
           exit(json_encode($res));
       }

       if ($fav_id <= 0){
           $res = getarrres('-401','参数错误',3);
           exit(json_encode($res));
       }

       $model_favorites = Model('favorites');
       $model_goods = Model('goods');

       $condition = array();
       $condition['fav_type'] = 'goods';
       $condition['fav_id'] = $fav_id;


       $model_favorites->delFavorites($condition);

       $model_goods->editGoodsById(array('goods_collect' => array('exp', 'goods_collect - 1')), $fav_id);

       $res = getarrres('200','收藏删除成功',1);
       exit(json_encode($res));
   }

	/* 
	 * 商品收藏详情
	 * (post)传值： key:令牌 fav_id:收藏id
	 * 返回
	 */
	public function infogoodscollectOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',0);
            exit(json_encode($res));
        }
        $favorites_info = $condition = array();
        $fav_id = $_POST['fav_id'];
        if($fav_id>0){
            $condition['fav_type'] = 'goods';
            $condition['log_id'] = $fav_id;
            $condition['member_id'] = $memberid;

            $model_favorites = Model('favorites');
    			//判断收藏是否存在
            $favorites_info = $model_favorites->getOneFavorites($condition);
            if($favorites_info)$favorites_info['favorites_info'] = $favorites_info;
        }
        $res = getarrres('200','',$favorites_info);
        exit(json_encode($res));
    }

	/* 
	 * 店铺收藏
	 * (post)传值： key:令牌
	 * 返回：店铺名称store_name 店铺id:store_id  收藏编号fav_id  未查到值返回 0用户未找到 1token未找到
	 */
	public function storecollectOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',1);
            exit(json_encode($res));
        }

        $model_favorites = Model('favorites');
        $model_store = Model('store');

        $favorites_list = $model_favorites->getStoreFavoritesList(array(
            'member_id'=>$memberid,
            ), '*');

        $store_list = array();

        $favorites_list_indexed = array();
        foreach ($favorites_list as $v) {
            $item = array();
            $item['store_id'] = $v['store_id'];
            $item['store_name'] = $v['store_name'];
            $item['fav_time'] = $v['fav_time'];
            $item['fav_time_text'] = date('Y-m-d H:i', $v['fav_time']);

            $store = $model_store->getStoreInfoByID($v['store_id']);
            $item['goods_count'] = $store['goods_count'];
            $item['store_collect'] = $store['store_collect'];

            $item['store_avatar'] = $store['store_avatar'];
            if ($store['store_avatar']) {
                $item['store_avatar_url'] = UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$store['store_avatar'];
            } else {
                $item['store_avatar_url'] = UPLOAD_SITE_URL.'/'.ATTACH_COMMON.DS.C('default_store_avatar');
            }

            $store_list[] = $item;
        }

        $res = getarrres('200','',array('favorites_list' => $store_list));
        exit(json_encode($res));
    }

	/* 
	 * 添加店铺收藏
	 * (post)传值： key:令牌 store_id:商品id
	 * 返回
	 */
	public function addstorecollectOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',2);
            exit(json_encode($res));
        }
        $fav_id = intval($_POST['store_id']);
        if ($fav_id <= 0){
            $res = getarrres('-401','参数错误',5);
            exit(json_encode($res));
        }

        $favorites_model = Model('favorites');

            //判断是否已经收藏
        $favorites_info = $favorites_model->getOneFavorites(array(
            'fav_id'=>$fav_id,
            'fav_type'=>'store',
            'member_id'=>$memberid,
            ));
        if(!empty($favorites_info)){
            $res = getarrres('-401','您已经收藏了该店铺',3);
            exit(json_encode($res));
        }

        //判断店铺是否为当前会员所有
        $seller_info = Model('seller')->getSellerInfo(array('member_id'=>$this->member_info['member_id']));
        if ($fav_id == $seller_info['store_id']) {
            $res = getarrres('-401','您不能收藏自己的店铺',4);
            exit(json_encode($res));
        }

            //添加收藏
        $insert_arr = array();
        $insert_arr['member_id'] = $memberid;
        $insert_arr['member_name'] = $memberid;
        $insert_arr['fav_id'] = $fav_id;
        $insert_arr['fav_type'] = 'store';
        $insert_arr['fav_time'] = time();
        $result = $favorites_model->addFavorites($insert_arr);

        if ($result) {
                //增加收藏数量
            $store_model = Model('store');
            $store_model->editStore(array('store_collect'=>array('exp', 'store_collect+1')), array('store_id' => $fav_id));
            $res = getarrres('200','收藏成功',1);
        } else {
            $res = getarrres('-401','收藏失败',0);
        }
        exit(json_encode($res));
    }

	/* 
	 * 删除店铺收藏
	 * (post)传值： key:令牌 store_id:店铺id
	 * 返回
	 */
	public function delstorecollectOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',2);
            exit(json_encode($res));
        }
        $fav_id = intval($_POST['store_id']);
        if ($fav_id <= 0) {
            $res = getarrres('-401','参数错误',3);
            exit(json_encode($res));
        }

        $model_favorites = Model('favorites');
        $model_store = Model('store');

        $condition = array();
        $condition['fav_type'] = 'store';
        $condition['fav_id'] = $fav_id;
        $condition['member_id'] = $memberid;

            //判断是否已经收藏
            // $favorites_info = $model_favorites->getOneFavorites($condition);
            // if(empty($favorites_info)){
            //     $res = getarrres('-401','收藏删除失败',0);
            // 	exit(json_encode($res));
            // }

        $model_favorites->delFavorites($condition);

        $model_store->editStore(array(
        'store_collect' => array('exp', 'store_collect - 1'),
        ), array(
        'store_id' => $fav_id,
        'store_collect' => array('gt', 0),
        ));

        $res = getarrres('200','收藏删除成功',1);
        exit(json_encode($res));
    }

	/* 
	 * *我的足迹
	 * (post)传值： key:令牌
	 * 返回：浏览信息
	 */
	public function browseOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',1);
            exit(json_encode($res));
        }
        $map['member_id'] = $memberid;
        $browsearr = Model('goods_browse')->table("goods_browse")->where($map)->select();
        foreach ($browsearr as $k => $v) {
           $goods_id = $v['goods_id'];
           $map['goods_id']=$goods_id;
           $goods = Model("goods")->getGoodsInfoByID($goods_id);
           $browsearr[$k]['goods_name']= $goods['goods_name'];
           $browsearr[$k]['goods_image']= cthumb($goods['goods_image'], 240, $goods['store_id']);
           $browsearr[$k]['goods_promotion_price']=$goods['goods_promotion_price'];
        }
        if(!empty($browsearr)){
            $res = getarrres('200','',$browsearr);
        }else{
            $res = getarrres('-401','还未浏览过','0');
        }
        exit(json_encode($res));
    }

    /* 
     * *我的足迹:清空
     * (post)传值： key:令牌
     * 返回：浏览信息
     */
    public function browseqkOp(){
        $key  = $_POST['key'];
        $memberid = getmemberid($key);
        if(empty($memberid)){
            $res = getarrres('-401','登录已过期，请重新登录！',1);
            exit(json_encode($res));
        }
        $map['member_id'] = $memberid;
        $browsearr = Model('goods_browse')->table("goods_browse")->where($map)->delete();
        if($browsearr){
            $res = getarrres('200','已清空',1);
        }else{
            $res = getarrres('-401','清空失败','0');
        }
        exit(json_encode($res));
    }

	/* 
	 * 实物订单列表
	 * (post)传值： key:令牌 state:订单状态 10 待付款 20 待收货 30待自提 40待评价
	 * 返回：
	 */
	public function matterorderOp(){
        $state  = $_POST['state'];
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',1);
            exit(json_encode($res));
        }
        $model_order = Model('order');
        $condition = array();        
        if($state != ''){
            $condition['order_state'] = $state;
        }
        $condition['buyer_id'] = $memberid;
        // $condition['order_state'] = $state;
        $condition['delete_state'] = 0;
        $condition['is_master'] = array('neq',1);
        $order_list_array = $model_order->getOrderList($condition, '', 'order_id,shipping_fee,order_sn,pay_sn,store_id,store_name,order_amount,goods_amount,shipping_fee,order_state,add_time', 'order_id desc','', array('order_goods'));
        $order_group_list = $order_pay_sn_array = array();
        foreach ($order_list_array as $key =>$value) {
            $store_id = $value['store_id'];

            foreach ($value['extend_order_goods'] as $val) {                   
                $value = array_merge($value,$val);
            }
            unset($value['extend_order_goods']);

            if($store_id <= 0){
                $storearr = explode('_', $value['goods_image']);
                $store_id = $storearr['0'];
            }

            $value['goods_image'] = cthumb($value['goods_image'], 240, $store_id);
            $order_group_list[$value['pay_sn']]['order_list'][] = $value;

            //如果有在线支付且未付款的订单则显示合并付款链接
            if ($value['order_state'] == ORDER_STATE_NEW) {
            $order_group_list[$value['pay_sn']]['pay_amount'] += $value['order_amount'] - $value['rcb_amount'] - $value['pd_amount'];
            }
            $order_group_list[$value['pay_sn']]['add_time'] = $value['add_time'];

            //记录一下pay_sn，后面需要查询支付单表
            $order_pay_sn_array[] = $value['pay_sn'];

        }

        $new_order_group_list = array();
        foreach ($order_group_list as $key => $value) {
            $value['pay_sn'] = strval($key);
            $new_order_group_list[] = $value;
        }
        // $page_count = $model_order->gettotalpage();
        $res = getarrres('200','',$new_order_group_list);
        exit(json_encode($res));
    }

    /* 
    * 实物订单详细
    * (post)传值： key:令牌 order_id:订单编号
    * 返回：订单信息
    */
    public function matterdetailOp(){
        $key  = $_POST['key'];
        $memberid = getmemberid($key);
        // if(empty($memberid)){
        // 	$res = getarrres('-401','登录已过期，请重新登录！',1);
        //     exit(json_encode($res));
        // }
        $order_id = intval($_POST['order_id']);

        $model_order    = Model('order');
        $condition['order_id'] = $order_id;
        if($memberid != ''){
            $condition['buyer_id'] = $memberid;
        }
        $order_info = $model_order->getOrderInfo($condition,array('order_goods'),"order_sn,add_time,buyer_name,payment_time,order_amount,shipping_fee,order_state,evaluation_state");
        // var_dump($order_info);
        $order_common = $model_order->getOrderInfo($condition,array('order_goods','order_common','store'));
        $order_info['address'] = $order_common['extend_order_common']['reciver_info']['address'];
        $order_info['reciver_name'] = $order_common['extend_order_common']['reciver_name'];
        $order_info['order_message'] = $order_common['extend_order_common']['reciver_info']['order_message'];
        $order_info['store_name'] = $order_common['extend_store']['store_name'];
        foreach ($order_common['extend_order_goods'] as $key => $value) {
            $store_id = $value['store_id'];
            if($store_id <= 0){
                $imgarr = explode('_', $value['goods_image']);
                $store_id = $imgarr['0'];
            }
            $order_common['extend_order_goods'][$key]['goods_image'] = cthumb($value['goods_image'], 360, $store_id);
        }
        $order_info['goodslist'] = $order_common['extend_order_goods'];
        unset($order_info['extend_order_goods']);
        unset($order_info['state_desc']);
        $res = getarrres('200','订单信息',$order_info);
        exit(json_encode($res));

    }

	/* 
	 * 取消实物订单
	 * (post)传值： key:令牌 order_id:订单编号
	 * 返回：1
	 */
	public function ordercancelOp(){
		$order_id  = intval($_POST['order_id']);
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',2);
            exit(json_encode($res));
        }
        $model_order = Model('order');
        $logic_order = Logic('order');

        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $memberid;
        $order = Model()->table("order")->where($condition)->find();
        if($order['order_state'] > 10){
            $res = getarrres('-401','订单已付款，无法操作！',1);
            exit(json_encode($res));
        }
        if($order['order_state'] == 0){
            $res = getarrres('-401','订单已取消！',1);
            exit(json_encode($res));
        }
        // $condition['order_type'] = 1;
        $upcon['order_state'] = 0;
        $upcon['delete_state'] = 1;
        $model_order->where($condition)->update($upcon);
        $order_info = $model_order->getOrderInfo($condition);
        $if_allow = $model_order->getOrderOperateState('buyer_cancel',$order_info);
        if (!$if_allow) {
            $res = getarrres('-401','无权操作',0);
        }
        if (TIMESTAMP - 86400 < $order_info['api_pay_time']) {
            $_hour = ceil(($order_info['api_pay_time']+86400-TIMESTAMP)/3600);
            $res = getarrres('-401','该订单曾尝试使用第三方支付平台支付，须在'.$_hour.'小时以后才可取消',2);
        }
        $result = $logic_order->changeOrderStateCancel($order_info,'buyer', $memberid, '其它原因');
        if(!$result['state']) {
            $res = getarrres('-401','取消失败',$result['msg']);
        } else {
            $res = getarrres('200','取消订单成功！',1);
        }
        exit(json_encode($res));
    }

	/* 
	 * 实物订单确认收货
	 * (post)传值： key:令牌 order_id:订单编号
	 * 返回：1
	 */
	public function orderreceiveOp(){
		$order_id = intval($_POST['order_id']);
		$key      = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',2);
            exit(json_encode($res));
        }
        $model_order = Model('order');
        $logic_order = Logic('order');

        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $memberid;
        $order_info = $model_order->getOrderInfo($condition);
        $if_allow   = $model_order->getOrderOperateState('receive',$order_info);
        if (!$if_allow) {
            $res = getarrres('-401','无权操作',0);
        }

        $result = $logic_order->changeOrderStateReceive($order_info,'buyer', $memberid,'签收了货物');
        if(!$result['state']) {
            $res = getarrres('-401','确认失败',$result['msg']);
        } else {
            if(floatval($order_info['order_amount']) > 0){
                $sql = "update zmkj_store set store_amount=store_amount+".floatval($order_info['order_amount'])."  WHERE store_id = ".$order_info['store_id'];
                Model()->execute($sql);
                $insert['order_id'] = $order_info['order_id'];
                $insert['order_sn'] = $order_info['order_sn'];
                $insert['order_add_time'] = $order_info['add_time'];
                $insert['payment_code'] = $order_info['payment_code'];
                $insert['order_amount'] = $order_info['order_amount'];
                $insert['shipping_fee'] = $order_info['shipping_fee'];
                $insert['evaluation_state'] = $order_info['evaluation_state'];
                $insert['order_state'] = 40;
                $insert['refund_state'] = $order_info['refund_state'];
                $insert['refund_amount'] = $order_info['refund_amount'];
                $insert['order_from'] = 2;
                $insert['order_isvalid'] = 1;
                $insert['store_id'] = $order_info['store_id'];
                $insert['store_name'] = $order_info['store_name'];
                $insert['buyer_id'] = $order_info['buyer_id'];
                $insert['buyer_name'] = $order_info['buyer_name'];
                $r = Model()->table("stat_order")->insert($insert);
            }
            $res = getarrres('200','确认成功',1);
        }
        exit(json_encode($res));
    }

	/* 
	 * 实物订单物流跟踪
	 * (post)传值：key:令牌 order_id:订单编号
	 * 返回：1
	 */
	public function searchdeliverOp(){
		$order_id	= intval($_POST['order_id']);
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',2);
         exit(json_encode($res));
     }
     if ($order_id <= 0) {
        $res = getarrres('-401','订单不存在',1);
        exit(json_encode($res));
    }

    $model_order	= Model('order');
    $condition['order_id'] = $order_id;
    $condition['buyer_id'] = $memberid;
    $order_info = $model_order->getOrderInfo($condition,array('order_common','order_goods'));
    if (empty($order_info) || !in_array($order_info['order_state'],array(ORDER_STATE_SEND,ORDER_STATE_SUCCESS))) {
        $res = getarrres('-401','订单不存在',1);
        exit(json_encode($res));
    }

    $express = rkcache('express',true);
    $e_code = $express[$order_info['extend_order_common']['shipping_express_id']]['e_code'];
    $e_name = $express[$order_info['extend_order_common']['shipping_express_id']]['e_name'];

    $deliver_info = $this->_get_express($e_code, $order_info['shipping_code']);

    $res = getarrres('200','',array('express_name' => $e_name, 'shipping_code' => $order_info['shipping_code'], 'deliver_info' => $deliver_info));
    exit(json_encode($res));
}

	/* 
	 * 虚拟订单列表
	 * (post)传值： key:令牌
	 * 返回：
	 */
	public function virtualorderOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',1);
         exit(json_encode($res));
     }
     $ownShopIds = Model('store')->getOwnShopIds();

     $model_vr_order = Model('vr_order');

     $condition = array();
     $condition['buyer_id'] = $memberid;
     if (preg_match('/^\d{10,20}$/',$_POST['order_key'])) {
        $condition['order_sn'] = $_POST['order_key'];
    } elseif ($_POST['order_key'] != '') {
        $condition['goods_name'] = array('like','%'.$_POST['order_key'].'%');
    }
    if ($_POST['state_type'] != '') {
        $condition['order_state'] = str_replace(
            array('state_new','state_pay'),
            array(ORDER_STATE_NEW,ORDER_STATE_PAY), $_POST['state_type']);
    }
    $order_list = $model_vr_order->getOrderList($condition,'', '*', 'order_id desc');

    foreach ($order_list as $key => $order) {
            //显示取消订单
        $order_list[$key]['if_cancel'] = $model_vr_order->getOrderOperateState('buyer_cancel',$order);
        
            //显示支付
        $order_list[$key]['if_pay'] = $model_vr_order->getOrderOperateState('payment',$order);

            //显示评价
        $order_list[$key]['if_evaluation'] = $model_vr_order->getOrderOperateState('evaluation',$order);

        $order_list[$key]['goods_image_url'] = cthumb($order['goods_image'], 240, $order['store_id']);

        $order_list[$key]['ownshop'] = in_array($order['store_id'], $ownShopIds);
    }

    $page_count = $model_vr_order->gettotalpage();

    $res = getarrres('200','',array('order_list' => $order_list));
    exit(json_encode($res));
}

	/* 
	 * 虚拟订单详细
	 * (post)传值： key:令牌 order_id:订单编号
	 * 返回：订单信息
	 */
	public function virtualdetailOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',1);
         exit(json_encode($res));
     }
     $order_id  = $_POST['order_id'];
     if($key == '' || $order_id == ''){
       $res = getarrres('-401','参数有误',1);
       exit(json_encode($res));
   }
   if ($order_id <= 0) {
    $res = getarrres('-401','订单不存在',1);
    exit(json_encode($res));
}
$model_vr_order = Model('vr_order');
$condition = array();
$condition['order_id'] = $order_id;
$condition['buyer_id'] = $memberid;
$order_info = $model_vr_order->getOrderInfo($condition);
if (empty($order_info) || $order_info['delete_state'] == ORDER_DEL_STATE_DROP) {
    $res = getarrres('-401','订单不存在',1);
    exit(json_encode($res));
}
$order_list = array();
$order_list[$order_id] = $order_info;

        //显示取消订单
$order_info['if_cancel'] = $model_vr_order->getOrderOperateState('buyer_cancel',$order_info);

        //显示评价
$order_info['if_evaluation'] = $model_vr_order->getOrderOperateState('evaluation',$order_info);

        //显示退款
$order_info['if_refund'] = $model_vr_order->getOrderOperateState('refund',$order_info);

$order_info['goods_image_url'] = cthumb($order_info['goods_image'], 240, $order_info['store_id']);

$ownShopIds = Model('store')->getOwnShopIds();
$order_info['ownshop'] = in_array($order_info['store_id'], $ownShopIds);

$order_info['vr_indate'] = $order_info['vr_indate'] ? date('Y-m-d',$order_info['vr_indate']) : '';
$order_info['add_time'] = date('Y-m-d',$order_info['add_time']);
$order_info['payment_time'] = $order_info['payment_time'] ? date('Y-m-d',$order_info['payment_time']) : '';
$order_info['finnshed_time'] = $order_info['finnshed_time'] ? date('Y-m-d',$order_info['finnshed_time']) : '';

$order_info['if_resend'] = $order_info['order_state'] == ORDER_STATE_PAY ? true : false;
        //取兑换码列表
$vr_code_list = $model_vr_order->getOrderCodeList(array('order_id' => $order_info['order_id']));
$order_info['code_list'] = $vr_code_list ? $vr_code_list : array();

$res = getarrres('200','',array('order_info' => $order_info));
exit(json_encode($res));
}

	/* 
	 * 取消虚拟订单
	 * (post)传值： key:令牌 order_id:订单编号
	 * 返回：1
	 */
	public function vrordercancelOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',0);
         exit(json_encode($res));
     }
     $order_id  = intval($_POST['order_id']);
     $model_vr_order = Model('vr_order');
     $condition = array();
     $condition['order_id'] = $order_id;
     $condition['buyer_id'] = $memberid;
     $order_info	= $model_vr_order->getOrderInfo($condition);

     $if_allow = $model_vr_order->getOrderOperateState('buyer_cancel',$order_info);
     if (!$if_allow) {
        $res = getarrres('-401','无权操作',0);
        exit(json_encode($res));
    }

    $logic_vr_order = Logic('vr_order');
    $result = $logic_vr_order->changeOrderStateCancel($order_info,'buyer', '其它原因');

    if(!$result['state']) {
        $res = getarrres('-401','操作失败',$result['msg']);
    } else {
        $res = getarrres('200','操作成功',1);
    }
    exit(json_encode($res));
}

	/* 
	 * 发送兑换码到手机
	 * (post)传值： key:令牌 order_id:订单编号 buyer_phone:电话号
	 * 返回：1
	 */
	public function resendOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',0);
         exit(json_encode($res));
     }
     if (!preg_match('/^[\d]{11}$/',$_POST['buyer_phone'])) {
        $res = getarrres('-401','请正确填写手机号',0);
        exit(json_encode($res));
    }
    $order_id   = intval($_POST['order_id']);
    if ($order_id <= 0) {
        $res = getarrres('-401','参数错误',0);
        exit(json_encode($res));
    }

    $model_vr_order = Model('vr_order');

    $condition = array();
    $condition['order_id'] = $order_id;
    $condition['buyer_id'] = $memberid;
    $order_info = $model_vr_order->getOrderInfo($condition);
    if (empty($order_info) && $order_info['order_state'] != ORDER_STATE_PAY) {
        $res = getarrres('-401','订单信息发生错误',0);
        exit(json_encode($res));
    }
    if ($order_info['vr_send_times'] >= 5) {
        $res = getarrres('-401','您发送的次数过多，无法发送',0);
        exit(json_encode($res));
    }

        //发送兑换码到手机
    $param = array('order_id'=>$order_id,'buyer_id'=>$memberid,'buyer_phone'=>$_POST['buyer_phone'],'goods_name'=>$order_info['goods_name']);
    QueueClient::push('sendVrCode', $param);

    $model_vr_order->editOrder(array('vr_send_times'=>array('exp','vr_send_times+1')),array('order_id'=>$order_id));

    $res = getarrres('200','操作成功',1);
    exit(json_encode($res));
}

	/* 
	 * *代金券
	 * (post)传值： key:令牌
	 * 返回：
	 */
	public function voucherOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',1);
         exit(json_encode($res));
     }
     $model = Model("voucher");
     $voucherarr = $model->table("voucher")->where("voucher_owner_id=$memberid")->select();
     foreach ($voucherarr as $key => $value) {
        $smap['store_id'] = $value['voucher_store_id'];
        $field = "store_name,store_avatar";
        $store = Model()->table('store')->field($field)->where($smap)->find();
        $voucherarr[$key]['voucher_store_name'] = $store['store_name'];
            // 店铺头像
        $voucherarr[$key]['store_avatar'] = $store['store_avatar']? UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$store['store_avatar']: UPLOAD_SITE_URL.'/'.ATTACH_COMMON.DS.C('default_store_avatar');

    }
    if(!empty($voucherarr)){
       $res = getarrres('200','',$voucherarr);
   }else{
       $res = getarrres('-401','无代金券',0);
   }
   echo json_encode($res);
}

	/* 
	 * *充值卡余额
	 * (post)传值： key:令牌
	 * 返回：充值卡信息
	 */
	public function rcbOp(){
		$key  = $_POST['key'];
        $token = Model('mb_user_token');
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $memberid = $tokens['member_id'];
        if(empty($memberid)){
           $res = getarrres('-401','登录已过期，请重新登录！',1);
           exit(json_encode($res));
       }
       $member = Model("member")->table('member')->where("member_id=$memberid")->find();
       $model = Model("rechargecard");
       $rechargecard = $model->table("rechargecard")->where("member_id=$memberid")->select();
       if(!empty($rechargecard)){
           $res = getarrres('200','success',$member['available_rc_balance']);
       }else{
           $res = getarrres('-401','无充值卡',0);
       }
       echo json_encode($res);
   }

    /**
     * 充值卡充值
     */
    public function rechargecard_addOp()
    {
        $key  = $_POST['key'];
        $token = Model('mb_user_token');
        $data['token'] = $key;
        $tokens = $token->where($data)->find();
        $memberid = $tokens['member_id'];
        $member_name = $tokens['member_name'];
        $param = $_POST;
        $rc_sn = trim($param["rc_sn"]);
        if (!$rc_sn) {
            $res = getarrres('-401','请输入充值卡号',0);
            echo json_encode($res);
        }
        try {
            Model('predeposit')->addRechargeCard($rc_sn, array('member_id'=>$memberid,'member_name'=>$member_name));
            $res = getarrres('200','success',1);
        } catch (Exception $e) {
            output_error($e->getMessage());
            $res = getarrres('-401',$e->getMessage(),0);
        }
        echo json_encode($res);
    }

	/* 
	 * *预存款：账户余额
	 * (post)传值： key:令牌
	 * 返回：变更日志
	 */
	public function pdlogOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',1);
         exit(json_encode($res));
     }
     $model = Model("pd_log");
     $pdlog = $model->table("pd_log")->where("lg_member_id=$memberid")->select();
     if(!empty($pdlog)){
       $res = getarrres('200','',$pdlog);
   }else{
       $res = getarrres('200','无变更记录',0);
   }
   echo json_encode($res);
}

	/* 
	 * *预存款：充值明细
	 * (post)传值： key:令牌
	 * 返回：充值记录
	 */
	public function pdrechargeOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',1);
         exit(json_encode($res));
     }
     $model = Model("pd_recharge");
     $pdrecharge = $model->table("pd_recharge")->where("pdr_member_id=$memberid")->select();
     if(!empty($pdrecharge)){
       $res = getarrres('200','',$pdrecharge);
   }else{
       $res = getarrres('-401','无充值记录',0);
   }
   echo json_encode($res);
}

	/* 
	 * *预存款：余额提现
	 * (post)传值： key:令牌
	 * 返回：提现记录
	 */
	public function pdcashOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',1);
         exit(json_encode($res));
     }
     $model = Model("pd_cash");
     $pdcash = $model->table("pd_cash")->where("pdc_member_id=$memberid")->select();
     if(!empty($pdcash)){
       $res = getarrres('200','',$pdcash);
   }else{
       $res = getarrres('200','无提现记录',0);
   }
   echo json_encode($res);
}

	/* 
	 * *积分日志
	 * (post)传值： key:令牌
	 * 返回：积分日志
	 */
	public function pointslogOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',1);
         exit(json_encode($res));
     }
     $model = Model("points_log");
     $points_log = $model->table("points_log")->where("pl_memberid=$memberid")->select();
     foreach ($points_log as $key => $value) {
        switch ($value['pl_stage']){
            case 'regist':
            $insertarr = '注册会员';
            break;
            case 'login':
            $insertarr = '会员登录';
            break;
            case 'comments':
            $insertarr = '评论商品';
            break;
            case 'order':
            $insertarr = '购物消费';
            break;
            case 'pointorder':
            $insertarr = '兑换礼品';
            break;            
            case 'signin':
            $insertarr = '会员签到';
            break;
        }
        $points_log[$key]['stage'] = $insertarr;
    }
    if(!empty($points_log)){
       $res = getarrres('200','',$points_log);
   }else{
       $res = getarrres('-401','无积分记录',0);
   }
   echo json_encode($res);
}

	/* 
	 * 地址信息列表
	 * (post)传值： key:令牌
	 * 返回：地址信息
	 */
	public function addressOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',1);
         exit(json_encode($res));
     }
     $model = Model("address");
     $address = $model->table("address")->where("member_id=$memberid")->select();
     if(!empty($address)){
       $res = getarrres('200','',$address);
   }else{
       $res = getarrres('-401','无地址信息',0);
   }
   echo json_encode($res);
}

	/* 
	 * 地址添加
	 * (post)传值： key:令牌  收货人姓名：true_name    联系手机：mob_phone    地区：area_info   详细地址：address   默认地址：is_default 
	 * 返回：address_id
	 */
	public function incaddressOp(){
		$model_address = Model('address');
        $key  = trim($_POST['key']);
        $map['token'] = $key;
        $selectaaa = Model("mb_user_token")->where($map)->find();
        $memberid = $selectaaa['member_id'];
        if(empty($memberid)){
            $res = getarrres('-401','登录已过期，请重新登录！',1);
            exit(json_encode($res));
        }
        $address_info = $this->_address_valid($_POST);
        $result = $model_address->addAddress($address_info);
        if($result) {
            $res = getarrres('200','保存成功',array('address_id' => $result));
        } else {
            $res = getarrres('-401','保存失败',0);
        }
        exit(json_encode($res));
    }

	/* 
	 * 地址编辑
	 * (post)传值： key:令牌 地址id:address_id  收货人姓名：true_name  联系手机：mob_phone  地区：area_info 详细地址：address 默认地址：is_default 
	 * 返回：1
	 */
	public function editaddressOp(){
		$key  = trim($_POST['key']);
        $map['token'] = $key;
        $selectaaa = Model("mb_user_token")->where($map)->find();
        $memberid = $selectaaa['member_id'];
        if(empty($memberid)){
           $res = getarrres('-401','登录已过期，请重新登录！',1);
           exit(json_encode($res));
       }
       $address_id = intval($_POST['address_id']);

       $model_address = Model('address');

        //验证地址是否为本人
       $address_info = $model_address->getOneAddress($address_id);
       if ($address_info['member_id'] != $memberid){
        $res = getarrres('-401','不是本人',1);
        exit(json_encode($res));
    }

    $address_info = $this->_address_valid($_POST);
    $result = $model_address->editAddress($address_info, array('address_id' => $address_id));
    if($result){
        $res = getarrres('200','修改成功',1);
    } else {
        $res = getarrres('-401','保存失败',0);
    }
    exit(json_encode($res));
}

	/**
     * 验证地址数据
     */
    private function _address_valid($aa) {
    	$key  = trim($aa['key']);
      $memberid = getmemberid($key);
      if(empty($memberid)){
       $res = getarrres('-401','登录已过期，请重新登录！',1);
       exit(json_encode($res));
   }
   $obj_validate = new Validate();
   $obj_validate->validateparam = array(
    array("input"=>trim($aa["true_name"]),"require"=>"true","message"=>'姓名不能为空'),
    array("input"=>trim($aa["area_info"]),"require"=>"true","message"=>'地区不能为空'),
    array("input"=>trim($aa["address"]),"require"=>"true","message"=>'地址不能为空'),
    array("input"=>trim($aa['mob_phone']),'require'=>'true','message'=>'联系方式不能为空')
    );
   $error = $obj_validate->validate();
   if ($error != ''){
    $res = getarrres('-401','参数错误',1);
    exit(json_encode($res));
}

$data = array();
$data['member_id'] = $memberid;
$data['true_name'] = $aa['true_name'];
$data['area_id'] = intval($aa['area_id']);
        // $data['city_id'] = intval($_POST['city_id']);
$data['area_info'] = $aa['area_info'];
$data['address'] = $aa['address'];
        // $data['tel_phone'] = $_POST['tel_phone'];
$data['mob_phone'] = $aa['mob_phone'];
$data['is_default'] = $aa['is_default'];		
return $data;
}

	/* 
	 * 地址删除
	 * (post)传值： key:令牌 地址id:address_id 
	 * 返回：1
	 */
	public function deladdressOp(){
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',2);
         exit(json_encode($res));
     }
     $address_id  = $_POST['address_id'];
     $map['member_id'] = $memberid;
     $map['address_id'] = $address_id;
     $model = Model("address");
     $id = $model->table('address')->where($map)->delete(); 
     if($id){
      $res = getarrres('200','删除成功',1);
  }else{
      $res = getarrres('-201','删除失败',0);
  }
  exit(json_encode($res));
}

	/* 
	 * 地址详细
	 * (post)传值：key:令牌 地址id:address_id 
	 * 返回：
	 */
	public function infoadressOp(){
		$address_id  = $_POST['address_id'];
		$key  = $_POST['key'];
		$memberid = getmemberid($key);
		if(empty($memberid)){
			$res = getarrres('-401','登录已过期，请重新登录！',1);
         exit(json_encode($res));
     }
     $model_address = Model('address');

     $condition = array();
     $condition['address_id'] = $address_id;
     $address_info = $model_address->getAddressInfo($condition);
     if(!empty($address_id) && $address_info['member_id'] == $memberid) {
        $res = getarrres('200','',array('address_info' => $address_info));
    } else {
        $res = getarrres('-201','地址不存在',0);
    }
    exit(json_encode($res));
}

	/* 
	 * 地区列表
	 * (post)传值：地址id:area_id 
	 * 返回：
	 */
	public function arealistOp(){
		$area_id = intval($_POST['area_id']);
        $model_area = Model('area');
        $condition = array();
        if($area_id > 0) {
            $condition['area_parent_id'] = $area_id;
        } else {
            $condition['area_deep'] = 1;
        }
        $area_list = $model_area->getAreaList($condition, 'area_id,area_name');
        $res = getarrres('200','',array('area_list' => $area_list));
        exit(json_encode($res));
    }

	/**
     * 从第三方取快递信息
     *
     */
    public function _get_express($e_code, $shipping_code){
        $url = 'http://www.kuaidi100.com/query?type='.$e_code.'&postid='.$shipping_code.'&id=1&valicode=&temp='.random(4).'&sessionid=&tmp='.random(4);
        import('function.ftp');
        $content = dfsockopen($url);
        $content = json_decode($content,true);

        if ($content['status'] != 200) { 
            $res = getarrres('-401','物流信息查询失败',0);
            exit(json_encode($res));
        }
        $content['data'] = array_reverse($content['data']);
        $output = array();
        if (is_array($content['data'])){
            foreach ($content['data'] as $k=>$v) {
                if ($v['time'] == '') continue;
                $output[]= $v['time'].'&nbsp;&nbsp;'.$v['context'];
            }
        }
        if (empty($output)) exit(json_encode(false));
        if (strtoupper(CHARSET) == 'GBK'){
            $output = Language::getUTF8($output);//网站GBK使用编码时,转换为UTF-8,防止json输出汉字问题
        }

        return $output;
    }

    private function order_type_no($stage) { 
        switch ($stage){
           case 'state_new':
           $condition['order_state'] = '10';
           break;
           case 'state_send':
           $condition['order_state'] = '30';
           break;
           case 'state_notakes':
           $condition['order_type'] = '3';
           $condition['order_state'] = '30';
           break;
           case 'state_noeval':
           $condition['order_state'] = '40';
           break;
       }
       return $condition;
    }
}
