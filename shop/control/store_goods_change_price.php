<?php
/**
 * 商品管理 v3-b12
 *
 */
defined('InShopNC') or exit('Access Invalid!');
class store_goods_change_priceControl extends BaseSellerControl {
	//需要对接政采云的店铺store_id
	private $zcy_store = array(51);
	
    public function __construct() {
        parent::__construct();
        Language::read('member_store_goods_index');
		//验证是否有操作权限
		if(! in_array($_SESSION["store_id"] , $this->zcy_store)){
			exit("当前店铺没有此权限！请<a href=\"/shop/index.php?act=seller_center&op=index\">返回</a>");
		}
    }

    public function indexOp() {
        $this->store_goods_change_priceOp();
    }
	/*
	 *商品改价模块
	 *列表页单击价格ajax改价调用
	*/	
	public function changPriceOp(){
		if($_POST["goods_id"]!="" and $_POST["new_price"]!=""){
			$goods_id=trim($_POST["goods_id"]);
			$new_price=trim($_POST["new_price"]);
			$zf = new zf_url();
			$sql="select * from `zmkj_goods` where `store_id` = ".$_SESSION['store_id']." and `goods_id` = $goods_id";
			if($rs=$zf->select_data($sql)){
				if($rs[0]["goods_marketprice"]<$new_price){
					$goods_marketprice=$new_price;
				}else{
					$goods_marketprice=$rs[0]["goods_marketprice"];
				}
				if($rs[0]["goods_price"]<$new_price){
					$goods_price=$new_price;
				}else{
					$goods_price=$rs[0]["goods_price"];
				}
				$sql="update `zmkj_goods` set `goods_marketprice` = $goods_marketprice , `goods_price`=$goods_price , `goods_promotion_price`=$new_price, `goods_edittime`= unix_timestamp(now()) where `store_id` = ".$_SESSION['store_id']." and  `goods_id` = $goods_id";
				if($zf->execute_data($sql)){
					echo "success";
				}else{
					echo "更新数据库失败";
					exit();
				}
			}else{
				echo "未找到此商品";
				exit();
			}
		}else{
			echo "参数错误";
		}
	}
	/*
	 *商品改价模块
	 *列表页更新映射商品
	*/	
	public function listyingsheOp(){
		$goods_id = $_GET["goods_id"];
		$sql = "select * from `zmkj_goods_orm` where `skuid` = $goods_id";
		$zf_url = new zf_url();
		$rs = $zf_url->select_data($sql);
		if($rs){
			exit(json_encode(array(isSuccess=>true,returnMsg=>$rs)));
		}else{
			exit(json_encode(array(isSuccess=>false,returnMsg=>"查询失败！")));
		}
	}
	
	
	/*
	 *商品改价模块
	 *列表页改变政府采购网分类
	*/	
	public function change_zf_classOp(){
		$goods_id = $_GET["goods_id"];
		$zf_class_id = $_GET["zf_class_id"];
		if($zf_class_id=='0'){
			$sql = "update `zmkj_goods` set `zf_class_id` = $zf_class_id , `is_bind` = 0 where `goods_id` = $goods_id and `store_id` = ".$_SESSION["store_id"];
		}else{
			$sql = "update `zmkj_goods` set `zf_class_id` = $zf_class_id , `is_bind` = 1 where `goods_id` = $goods_id and `store_id` = ".$_SESSION["store_id"];
		}
		$zf_url = new zf_url();
		$rs = $zf_url->execute_data_return_affected_rows($sql);
		$sql = "select * from `zmkj_zf_class` where `id` = $zf_class_id";
		$rs1 = $zf_url->select_data($sql);
		if(!empty($rs1)){
			if($rs1[0]["class_type"]==1){
				$class_type = "sanjia";
			}else{
				$class_type = "yijia";
			}
		}else{
			$class_type = "noexist";
		}
		$rs["class_type"] = $class_type;
		exit(json_encode($rs));
	}	
	
	/*
	 *商品改价模块
	 *快速添加商品映射关系ajax调用
	*/	
	public function addyingsheOp(){
		$data = file_get_contents("php://input");
		if(!is_null($data)){
			$data = json_decode($data);
		    if(is_object($data)) {
		        $data = (array)$data;
			}
			$goods_id=$data["goods_id"];
			$goods_name=$data["goods_name"];
			$goods_yingshe_id=$data["goods_yingshe_id"];
			$goods_yingshe_name=$data["goods_yingshe_name"];
			if((!is_null($goods_id)) and (!is_null($goods_name)) and (!is_null($goods_yingshe_id)) and(!is_null($goods_yingshe_name))){
				$zf_url = new zf_url();
				$sql = "select * from `zmkj_goods_orm` where `productId` like '%$goods_yingshe_id%'";
				$rs=$zf_url->select_data($sql);
				if(!empty($rs)){
					//print_r($rs);
					exit(json_encode(array('returnMsg' => "该商品已映射!</br><a href='".$rs[0]["productUrlEC"]."' target=\"_blank\">".$rs[0]["productNameEC"]."</a>", "isSuccess" => false)));
				}else{
					$sql = "insert into `zmkj_goods_orm` (`skuid`,`productId`,`productName`,`productUrl`,`productNameEC`,`productUrlEC`,`add_time`,`average_price`) values($goods_id,'$goods_yingshe_id','$goods_yingshe_name','http://www.hebzfcgwssc.com/Mall/HeBei/detail.aspx?product_id=$goods_yingshe_id','$goods_name','http://www.nrwspt.com/shop/index.php?act=goods&op=index&goods_id=$goods_id',unix_timestamp(now()),0)";
					if($zf_url->execute_data($sql)){
						exit(json_encode(array('returnMsg' => "添加映射成功", "isSuccess" => true)));
					}else{
						exit(json_encode(array('returnMsg' => "添加映射失败", "isSuccess" => false)));
					}
				//$sql = "select `goods`"
				}
			}else{
				exit(json_encode(array('returnMsg' => "参数错误", "isSuccess" => false)));
			}
		}else{
			exit(json_encode(array('returnMsg' => "参数错误", "isSuccess" => false)));
		}
	}
	
	/*
	 *商品改价模块
	 *商品批量改价ajax调用
	*/	
	public function change_price_allOp(){
		$data = file_get_contents("php://input");
		if(!is_null($data)){
			$data = json_decode($data);
		    if(is_object($data)) {
		        $data = (array)$data;
			}
			//print_r($data);
			$fs = $data["fs"];
			$sj = $data["sj"];
			$jg = $data["jg"];
			if(($fs!="") and ($sj!="") and ($jg!="") and (is_numeric($jg))){
				switch($sj){
					case "s":
						$sj = "+";
						break;
					case "j":
						$sj = "-";
						break;
				}
				$zf_url = new zf_url();
				switch($fs){
					case "0":
						$sql = "insert into `zmkj_zf_change_price` (`C_fs`,`C_sj`,`C_jg`) values('$fs','$sj',".number_format($jg,2).")";
						$sql1 = "update `zmkj_goods` set `goods_promotion_price` = `goods_promotion_price`".$sj.$jg." where `goods_state` = 1 and `goods_verify` =1 and `store_id` = ".$_SESSION["store_id"];
						$rs = $zf_url->execute_data_return_affected_rows($sql1);
						if($rs["isSuccess"]){
							$zf_url->execute_data($sql);
							exit(json_encode(array('returnMsg' => "改价成功", "affected_rows"=>$rs["affected_rows"], "isSuccess" => true)));
						}else{
							exit(json_encode(array('returnMsg' => "修改失败", "isSuccess" => false)));
						}
						break;
					case "1":
						$id_price = $data["id_price"];
						$num = $data["num"];
						if($id_price!="" and $num !=""){
							switch($id_price){
								case "eq":
									$sql = "insert into `zmkj_zf_change_price` (`C_fs`,`C_ip`,`C_num`,`C_sj`,`C_jg`) values('$fs','$id_price','$num','$sj',".number_format($jg,2).")";
									$sql1 = "update `zmkj_goods` set `goods_promotion_price` = `goods_promotion_price`".$sj.$jg." where `goods_state` = 1 and `goods_verify` =1 and `goods_id` = $num and `store_id` = ".$_SESSION["store_id"];
									break;
								case "dy":
									$sql = "insert into `zmkj_zf_change_price` (`C_fs`,`C_ip`,`C_num`,`C_sj`,`C_jg`) values('$fs','$id_price','$num','$sj',".number_format($jg,2).")";
									$sql1 = "update `zmkj_goods` set `goods_promotion_price` = `goods_promotion_price`".$sj.$jg." where `goods_state` = 1 and `goods_verify` =1 and `goods_id` > $num and `store_id` = ".$_SESSION["store_id"];
									break;
								case "jy":
									$nums = explode(",",$num);
									$sql = "insert into `zmkj_zf_change_price` (`C_fs`,`C_ip`,`C_num`,`C_sj`,`C_jg`) values('$fs','$id_price','$num','$sj',".number_format($jg,2).")";
									$sql1 = "update `zmkj_goods` set `goods_promotion_price` = `goods_promotion_price`".$sj.$jg." where `goods_state` = 1 and `goods_verify` =1 and `goods_id` BETWEEN $nums[0] and $nums[1] and `store_id` = ".$_SESSION["store_id"];
									break;
								case "xy":
									$sql = "insert into `zmkj_zf_change_price` (`C_fs`,`C_ip`,`C_num`,`C_sj`,`C_jg`) values('$fs','$id_price','$num','$sj',".number_format($jg,2).")";
									$sql1 = "update `zmkj_goods` set `goods_promotion_price` = `goods_promotion_price`".$sj.$jg." where `goods_state` = 1 and `goods_verify` =1 and `goods_id` < $num and `store_id` = ".$_SESSION["store_id"];
									break;
								default:
									exit(json_encode(array('returnMsg' => "修改失败:参数错误->'$id_price':$id_price", "isSuccess" => false)));
							}
							$rs = $zf_url->execute_data_return_affected_rows($sql1);
							if($rs["isSuccess"]){
								$zf_url->execute_data($sql);
								exit(json_encode(array('returnMsg' => "改价成功", "affected_rows"=>$rs["affected_rows"], "isSuccess" => true)));
							}else{
								exit(json_encode(array('returnMsg' => "修改失败", "isSuccess" => false)));
							}
							//exit(json_encode(array('returnMsg' => $sql1, "isSuccess" => true)));
						}else{
							exit(json_encode(array('returnMsg' => "修改失败:参数错误", "isSuccess" => false)));
						}
						break;
					case "2":
						$zf_class = $data["zf_class"];
						$zf_class = implode(',',$zf_class);
						$sql = "insert into `zmkj_zf_change_price` (`C_fs`,`C_sj`,`C_jg`,`C_zf_class`) values('$fs','$sj',".number_format($jg,2).",'$zf_class')";
						$sql1 = "update `zmkj_goods` set `goods_promotion_price` = `goods_promotion_price`".$sj.$jg." where `goods_state` = 1 and `goods_verify` =1 and `zf_class_id` in ($zf_class) and `store_id` = ".$_SESSION["store_id"];
						$rs = $zf_url->execute_data_return_affected_rows($sql1);
						if($rs["isSuccess"]){
							$zf_url->execute_data($sql);
							exit(json_encode(array('returnMsg' => "改价成功", "affected_rows"=>$rs["affected_rows"], "isSuccess" => true)));
						}else{
							exit(json_encode(array('returnMsg' => "修改失败", "isSuccess" => false)));
						}
//						exit(json_encode(array('returnMsg' =>$zf_class.$sql.";".$sql1, "isSuccess" => true)));
						break;
					case "3":
						$id_price = $data["id_price"];
						$num = $data["num"];
						if($id_price!="" and $num !=""){
							switch($id_price){
								case "eq":
									$sql = "insert into `zmkj_zf_change_price` (`C_fs`,`C_ip`,`C_num`,`C_sj`,`C_jg`) values('$fs','$id_price','$num','$sj',".number_format($jg,2).")";
									$sql1 = "update `zmkj_goods` set `goods_promotion_price` = `goods_promotion_price`".$sj.$jg." where `goods_state` = 1 and `goods_verify` =1 and `goods_promotion_price` = $num and `store_id` = ".$_SESSION["store_id"];
									break;
								case "dy":
									$sql = "insert into `zmkj_zf_change_price` (`C_fs`,`C_ip`,`C_num`,`C_sj`,`C_jg`) values('$fs','$id_price','$num','$sj',".number_format($jg,2).")";
									$sql1 = "update `zmkj_goods` set `goods_promotion_price` = `goods_promotion_price`".$sj.$jg." where `goods_state` = 1 and `goods_verify` =1 and `goods_promotion_price` > $num and `store_id` = ".$_SESSION["store_id"];
									break;
								case "jy":
									$nums = explode(",",$num);
									$sql = "insert into `zmkj_zf_change_price` (`C_fs`,`C_ip`,`C_num`,`C_sj`,`C_jg`) values('$fs','$id_price','$num','$sj',".number_format($jg,2).")";
									$sql1 = "update `zmkj_goods` set `goods_promotion_price` = `goods_promotion_price`".$sj.$jg." where `goods_state` = 1 and `goods_verify` =1 and `goods_promotion_price` BETWEEN $nums[0] and $nums[1] and `store_id` = ".$_SESSION["store_id"];
									break;
								case "xy":
									$sql = "insert into `zmkj_zf_change_price` (`C_fs`,`C_ip`,`C_num`,`C_sj`,`C_jg`) values('$fs','$id_price','$num','$sj',".number_format($jg,2).")";
									$sql1 = "update `zmkj_goods` set `goods_promotion_price` = `goods_promotion_price`".$sj.$jg." where `goods_state` = 1 and `goods_verify` =1 and `goods_promotion_price` < $num and `store_id` = ".$_SESSION["store_id"];
									break;
								default:
									exit(json_encode(array('returnMsg' => "修改失败:参数错误->'$id_price':$id_price", "isSuccess" => false)));
							}
							$rs = $zf_url->execute_data_return_affected_rows($sql1);
							if($rs["isSuccess"]){
								$zf_url->execute_data($sql);
								exit(json_encode(array('returnMsg' => "改价成功", "affected_rows"=>$rs["affected_rows"], "isSuccess" => true)));
							}else{
								exit(json_encode(array('returnMsg' => "修改失败", "isSuccess" => false)));
							}
							//exit(json_encode(array('returnMsg' => $sql."; ".$sql1, "isSuccess" => true)));
						}else{
							exit(json_encode(array('returnMsg' => "修改失败:参数错误", "isSuccess" => false)));
						}
						break;
				}
			}else{
				exit(json_encode(array('returnMsg' => "参数错误", "isSuccess" => false)));
			}
		}else{
			exit(json_encode(array('returnMsg' => "参数错误", "isSuccess" => false)));
		}
	}
	
	/*
	 *商品改价模块
	 *商品批量改价
	 *获取改价记录ajax调用
	*/
	public function get_change_price_listOp(){
		$zf_url = new zf_url();
		$sql = "select `C_fs` as `fs` , `C_ip` as `ip` , `C_num` as `num` , `C_sj` as `sj` , `C_jg` as `jg` , `C_zf_class` as `zf_class` , `C_time` as `time` from `zmkj_zf_change_price` order by `id` DESC LIMIT 0,100";
		$rs = $zf_url->select_data($sql);
		$rows = count($rs);
		$output = array("rs"=>$rs,"rows"=>$rows);
		exit(json_encode($output));
	}
	
	/*
	 *商品改价模块
	 *更新商品链接ajax调用
	*/	
	public function updateProductidOp(){
		if($_POST["goods_id"]!="" and $_POST["productid"]!=""){
			$goods_id=trim($_POST["goods_id"]);
			$productid=trim($_POST["productid"]);
			$zf = new zf_url();
			$zf->update_productid($goods_id,$productid);
		}else{
			echo "参数错误";
			echo $goods_id.":".$productid;
		}
	}
    /**
     * 出售中的商品列表
     */
    public function store_goods_change_priceOp() {
        //$model_goods = Model('goods');

/*        $where = array();
        $where['store_id'] = $_SESSION['store_id'];
        if (intval($_GET['zf_class']) > 0) {
           $where['zf_class_id'] = intval($_GET['zf_class']);
        }
        
        if (trim($_GET['keyword']) != '') {
            switch ($_GET['search_type']) {
                case "0":
                    $where['goods_name'] = array('like', '%' . trim($_GET['keyword']) . '%');
                    break;
                case "1":
                    $where['goods_id'] = array('like', '%' . trim($_GET['keyword']) . '%');
                    break;
                case "2":
                    $where['goods_commonid'] = intval($_GET['keyword']);
                    break;
            }
        }*/

        switch ($_GET['type']) {
            // 批量改价
            case 'change_all':
                $this->profile_menu('change_all');
                //if (isset($_GET['verify']) && in_array($_GET['verify'], array('0', '10'))) {
                //    $where['goods_verify']  = $_GET['verify'];
                //}
                //$goods_list = $model_goods->getGoodsCommonWaitVerifyList($where);
                break;
            // 映射商品改价
            case 'change_yingshe':
                $this->profile_menu('change_yingshe');
                break;
			case 'update_productid':
                $this->profile_menu('update_productid');
                break;
            // 单个改价
            default:
                $this->profile_menu('change_one');
                //$goods_list = $model_goods->getGoodsCommonChangePrcieList($where);
                break;
        }

        //Tpl::output('show_page', $model_goods->showpage());
        //Tpl::output('goods_list', $goods_list);

        // 计算库存
        //$storage_array = $model_goods->calculateStorage($goods_list);
        //Tpl::output('storage_array', $storage_array);

        // 商品分类
        //$store_goods_class = Model('store_goods_class')->getClassTree(array('store_id' => $_SESSION['store_id'], 'stc_state' => '1'));
        //Tpl::output('store_goods_class', $store_goods_class);

        switch ($_GET['type']) {
            case 'change_all':// 商品批量改价
                Tpl::showpage('store_goods_list.change_price_change_all');
                break;
            case 'change_yingshe':// 主动映射的商品改价
                Tpl::showpage('store_goods_yingshe.change_price');
                break;
			case 'update_productid':// 主动映射的商品改价
                Tpl::showpage('store_goods.update_productid');
                break;
            default://逐个商品改价
                Tpl::showpage('store_goods_list.change_price');
                break;
        }
    }

    /**
     * 商品上架
     */
   /* public function goods_showOp() {
        $commonid = $_GET['commonid'];
        if (!preg_match('/^[\d,]+$/i', $commonid)) {
            showDialog(L('para_error'), '', 'error');
        }
        $commonid_array = explode(',', $commonid);
        if ($this->store_info['store_state'] != 1) {
            showDialog(L('store_goods_index_goods_show_fail') . '，店铺正在审核中或已经关闭', '', 'error');
        }
        $return = Model('goods')->editProducesOnline(array('goods_commonid' => array('in', $commonid_array), 'store_id' => $_SESSION['store_id']));
        if ($return) {
        	//更改首页全部推荐商品信息/实时更新
	        $model_web_config = Model('web_config');
			$web[] = '121';
			$web[] = '1';
			$web[] = '2';
			$web[] = '3';
			$web[] = '4';
			$web[] = '5';
			$web[] = '7';
			foreach($web as $v){
				$web_list = $model_web_config->getWebList(array('web_id'=>$v));
				$web_array = $web_list[0];
				if(!empty($web_array) && is_array($web_array)) {
					$model_web_config->updateWebHtml($v);
				}
			}
            // 添加操作日志
            $this->recordSellerLog('商品上架，平台货号：'.$commonid);
            showDialog(L('store_goods_index_goods_show_success'), 'reload', 'succ');
        } else {
            showDialog(L('store_goods_index_goods_show_fail'), '', 'error');
        }
    }*/
	
    /**
     * 用户中心右边，小导航
     *
     * @param string $menu_key 当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_key = '') {
        $menu_array = array(
            array('menu_key' => 'change_one',    'menu_name' => "商品逐个改价",   'menu_url' => urlShop('store_goods_change_price', 'index')),
            array('menu_key' => 'change_yingshe',     'menu_name' => "映射商品改价",     'menu_url' => urlShop('store_goods_change_price', 'index', array('type' => 'change_yingshe'))),
            array('menu_key' => 'change_all',     'menu_name' => "商品批量改价",     'menu_url' => urlShop('store_goods_change_price', 'index', array('type' => 'change_all'))),
			array('menu_key' => 'update_productid',     'menu_name' => "更新商品id",     'menu_url' => urlShop('store_goods_change_price', 'index', array('type' => 'update_productid'))),
        );
        Tpl::output ( 'member_menu', $menu_array );
        Tpl::output ( 'menu_key', $menu_key );
    }	
}


	class zf_url {
			
		private $ip = "localhost";
		private $port = "3306";
		private $user = "root";
		private $pwd = "root";
		private $dbname = "api";
//		private $pwd = "";
//		private $dbname = "ceshi";
			

	//增加数据
		public function execute_data($sql) {
	//链接数据库
			$conn = @new mysqli($this->ip, $this->user, $this->pwd, $this->dbname, $this->port);
	// 检测连接
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			}
			$conn->query("set names utf8");
			$rs = $conn->query($sql);
			$affected_rows = mysqli_affected_rows($conn);
			if(!$rs){
				echo mysqli_error($conn);
			}
			$conn->close();
			return $rs;
		}
		
		
	//增加数据 并返回影响行数
		public function execute_data_return_affected_rows($sql) {
	//链接数据库
			$conn = @new mysqli($this->ip, $this->user, $this->pwd, $this->dbname, $this->port);
	// 检测连接
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			}
			$conn->query("set names utf8");
			$rs = $conn->query($sql);
			$affected_rows = mysqli_affected_rows($conn);
			if(!$rs){
				$rs_array = array("isSuccess"=>false,"error_msg"=>mysqli_error($conn));
			}else{
				$rs_array = array("isSuccess"=>true,"affected_rows"=>$affected_rows);
			}
			$conn->close();
			return $rs_array;
		}
		
		
		
	//查询数据
		public function select_data($sql) {
	//链接数据库
			$conn = @new mysqli($this->ip, $this->user, $this->pwd, $this->dbname, $this->port);
	// 检测连接
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			}
			$conn->query("set names utf8");
			$rs = @$conn->query($sql);
			$rs_array = array();
			if ($rs->num_rows > 0) {
	// 输出每行数据
				while ($row = $rs->fetch_assoc()) {
					$rs_array[] = $row;
				}
			} else {
				$rs_array = array();
			}
			$conn->close();
			return $rs_array;
		}
		
	//获取政府采购网商品productid
		public function get_zf_url($goods_id){
			$out="";
			$rs_array = $this->select_data("select * from `zmkj_zf_url` where `goods_id` = ".$goods_id);
			if(!empty($rs_array)){
				if($rs_array[0]["zf_product_id"]=="不存在该商品"){
					$out="不存在";
				}else{
					$out=trim($rs_array[0]["zf_product_id"]);
				}
			}else{
				$out="未录入";
			}
			return $out;
		}
	//更新政府采购网商品product_id
		public function update_productid($goods_id,$productid){
			$rs = $this->select_data("select `goods_id` from `zmkj_zf_url` where `goods_id` = $goods_id");
			$rt = "";
			if(!empty($rs)){
				if($this->execute_data("update `zmkj_zf_url` set `zf_product_id` = \"$productid\" where `goods_id` = $goods_id")){
					$rt = "更新成功";
				}else{
					$rt = "更新失败";
				}
			}else{
				$goods_commonid = $this->select_data("select `goods_commonid` from `zmkj_goods` where `store_id` = ".$_SESSION['store_id']." and `goods_id` = $goods_id");
				if(!empty($goods_commonid)){
					if($this->execute_data("insert into `zmkj_zf_url` (`goods_commonid`,`goods_id`,`zf_product_id`) values(".$goods_commonid[0]["goods_commonid"].",$goods_id,\"$productid\")")){
						$rt = "录入成功";
					}else{
						$rt = "录入失败";
					}
				}else{
					$rt = "此商品不存在或已下架";
				}
			}
			echo $goods_id."\t->\t".$productid."\t->\t".$rt;
		}
	//获取映射商品链接
		public function get_yingshe_goods($goods_id){
			$rs_array = $this->select_data("select * from `zmkj_goods_orm` where `skuid` = ".$goods_id);
			return $rs_array;
		}
	}




  //分页工具  直接加载使用
    /**
        file: page.class.php
        完美分页类 Page
    */
    class Page {
        private $total;                         //数据表中总记录数
        private $listRows;                      //每页显示行数
        private $limit;                         //SQL语句使用limit从句,限制获取记录个数
        private $uri;                           //自动获取url的请求地址
        private $pageNum;                       //总页数
        private $page;                          //当前页  
        private $config = array(
                'head' => "条记录",
                'prev' => "上一页",
                'next' => "下一页",
                'first'=> "首页",
                'last' => "末页"
            );                 
        //在分页信息中显示内容，可以自己通过set()方法设置
        private $listNum = 10;                  //默认分页列表显示的个数
 
        /**
            构造方法，可以设置分页类的属性
            @param  int $total      计算分页的总记录数
            @param  int $listRows   可选的，设置每页需要显示的记录数，默认为25条
            @param  mixed   $query  可选的，为向目标页面传递参数,可以是数组，也可以是查询字符串格式
            @param  bool    $ord    可选的，默认值为true, 页面从第一页开始显示，false则为最后一页
         */
        public function __construct($total, $listRows=25, $query="", $ord=true){
            $this->total = $total;
            $this->listRows = $listRows;
            $this->uri = $this->getUri($query);
            $this->pageNum = ceil($this->total / $this->listRows);
            /*以下判断用来设置当前面*/
            if(!empty($_GET["page"])) {
				$page = $_GET["page"];
				if($page>$this->pageNum){
					$page = $this->pageNum;
				}
            }else{
                if($ord)
                    $page = 1;
                else
                    $page = $this->pageNum;
            }
 
            if($total > 0) {
                if(preg_match('/\D/', $page) ){
                    $this->page = 1;
                }else{
                    $this->page = $page;
                }
            }else{
                $this->page = 0;
            }
             
            $this->limit = "LIMIT ".$this->setLimit();
        }
 
        /**
            用于设置显示分页的信息，可以进行连贯操作
            @param  string  $param  是成员属性数组config的下标
            @param  string  $value  用于设置config下标对应的元素值
            @return object          返回本对象自己$this， 用于连惯操作
         */
        function set($param, $value){
            if(array_key_exists($param, $this->config)){
                $this->config[$param] = $value;
            }
            return $this;
        }
         
        /* 不是直接去调用，通过该方法，可以使用在对象外部直接获取私有成员属性limit和page的值 */
        function __get($args){
            if($args == "limit" || $args == "page")
                return $this->$args;
            else
                return null;
        }
         
        /**
            按指定的格式输出分页
            @param  int 0-7的数字分别作为参数，用于自定义输出分页结构和调整结构的顺序，默认输出全部结构
            @return string  分页信息内容
         */
        function fpage(){
            $arr = func_get_args();
 
            $html[0] = "<span class='p1'> 共<b> {$this->total} </b>{$this->config["head"]} </span>";
            $html[1] = " 本页 <b>".$this->disnum()."</b> 条 ";
            $html[2] = " 本页从 <b>{$this->start()}-{$this->end()}</b> 条 ";
            $html[3] = " <b>{$this->page}/{$this->pageNum}</b>页 ";
            $html[4] = $this->firstprev();
            $html[5] = $this->pageList();
            $html[6] = $this->nextlast();
            $html[7] = $this->goPage();
             
            $fpage = '<div style="font:12px \'\5B8B\4F53\',san-serif;">';
            if(count($arr) < 1)
                $arr = array(0, 1,2,3,4,5,6,7);
             
            for($i = 0; $i < count($arr); $i++)
                $fpage .= $html[$arr[$i]];
         
            $fpage .= '</div>';
            return $fpage;
        }
         
        /* 在对象内部使用的私有方法，*/
        private function setLimit(){
            if($this->page > 0)
                return ($this->page-1)*$this->listRows.", {$this->listRows}";
            else
                return 0;
        }
 
        /* 在对象内部使用的私有方法，用于自动获取访问的当前URL */
        private function getUri($query){   
            $request_uri = $_SERVER["REQUEST_URI"];
            $url = strstr($request_uri,'?') ? $request_uri :  $request_uri.'?';
             
            if(is_array($query))
                $url .= http_build_query($query);
            else if($query != "")
                $url .= "&".trim($query, "?&");
         
            $arr = parse_url($url);
 
            if(isset($arr["query"])){
                parse_str($arr["query"], $arrs);
                unset($arrs["page"]);
                $url = $arr["path"].'?'.http_build_query($arrs);
            }
             
            if(strstr($url, '?')) {
                if(substr($url, -1)!='?')
                    $url = $url.'&';
            }else{
                $url = $url.'?';
            }
             
            return $url;
        }
 
        /* 在对象内部使用的私有方法，用于获取当前页开始的记录数 */
        private function start(){
            if($this->total == 0)
                return 0;
            else
                return ($this->page-1) * $this->listRows+1;
        }
 
        /* 在对象内部使用的私有方法，用于获取当前页结束的记录数 */
        private function end(){
            return min($this->page * $this->listRows, $this->total);
        }
 
        /* 在对象内部使用的私有方法，用于获取上一页和首页的操作信息 */
        private function firstprev(){
            if($this->page > 1) {
                $str = " <a href='{$this->uri}page=1'>{$this->config["first"]}</a> ";
                $str .= "<a href='{$this->uri}page=".($this->page-1)."'>{$this->config["prev"]}</a> ";       
                return $str;
            }
 
        }
     
        /* 在对象内部使用的私有方法，用于获取页数列表信息 */
        private function pageList(){
            $linkPage = " <b>";
             
            $inum = floor($this->listNum/2);
            /*当前页前面的列表 */
            for($i = $inum; $i >= 1; $i--){
                $page = $this->page-$i;
 
                if($page >= 1)
                    $linkPage .= "<a href='{$this->uri}page={$page}'>{$page}</a> ";
            }
            /*当前页的信息 */
            if($this->pageNum > 1)
                $linkPage .= "<span style='padding:1px 2px;background:#BBB;color:white'>{$this->page}</span> ";
             
            /*当前页后面的列表 */
            for($i=1; $i <= $inum; $i++){
                $page = $this->page+$i;
                if($page <= $this->pageNum)
                    $linkPage .= "<a href='{$this->uri}page={$page}'>{$page}</a> ";
                else
                    break;
            }
            $linkPage .= '</b>';
            return $linkPage;
        }
 
        /* 在对象内部使用的私有方法，获取下一页和尾页的操作信息 */
        private function nextlast(){
            if($this->page != $this->pageNum) {
                $str = " <a href='{$this->uri}page=".($this->page+1)."'>{$this->config["next"]}</a> ";
                $str .= " <a href='{$this->uri}page=".($this->pageNum)."'>{$this->config["last"]}</a> ";
                return $str;
            }
        }
 
        /* 在对象内部使用的私有方法，用于显示和处理表单跳转页面 */
        private function goPage(){
                if($this->pageNum > 1) {
                return ' <input style="width:20px;height:17px !important;height:18px;border:1px solid #CCCCCC;" type="text" onkeydown="javascript:if(event.keyCode==13){var page=(this.value>'.$this->pageNum.')?'.$this->pageNum.':this.value;location=\''.$this->uri.'page=\'+page+\'\'}" value="'.$this->page.'"><input style="cursor:pointer;width:25px;height:18px;border:1px solid #CCCCCC;" type="button" value="GO" onclick="javascript:var page=(this.previousSibling.value>'.$this->pageNum.')?'.$this->pageNum.':this.previousSibling.value;location=\''.$this->uri.'page=\'+page+\'\'"> ';
            }
        }
 
        /* 在对象内部使用的私有方法，用于获取本页显示的记录条数 */
        private function disnum(){
            if($this->total > 0){
                return $this->end()-$this->start()+1;
            }else{
                return 0;
            }
        }

}
?>