<?php

/*
 * 省政府采购网接口
 * 赵廷建
 * 2018/6/25
 */

class buy {
//key和密钥：剩下的你就改改就行了，比如写个订单接口
//    private $appKey = "hbsrmy";
//    private $appSecret = "7db8fbca-b6d2-4x10-cd5y-h7r4bwl69t704";
    private $appKey = "nrwspt";
    private $appSecret = "7db8fbcc-b9d2-4010-cd57-n7r4bwlp9tb04";
//上传图片
    private $url = DEMAIN;
    private $upload_path = "/data/upload/shop/store/goods/";
    private $type="mysql";
//数据库配置
    private $ip = IP;
    private $port = PORT;
    private $user = USER;
    private $pwd = PWD;
    private $dbname = DBNAME;
//政采 id 和 name 映射关系
    private $zc_name_array = array(
        'A02010104' => '台式计算机',
        'A02010105' => '便携式计算机',
        'A02010107' => '平板式微型计算机',
        'A0201060401' => '液晶显示器',
        'A02010201' => '路由器',
        'A0201020201' => '以太网交换机',
        'A0201020299' => '其他交换设备',
        'A02010103' => '服务器',
        'A02061504' => '不间断电源(UPS)',
        'A02010301' => '防火墙',
        'A0201060101' => '喷墨打印机',
        'A0201060102' => '激光打印机',
        'A0201060104' => '针式打印机',
        'A02091001' => '普通电视设备(电视机)',
        'A0206180201' => '风扇',
        'A0206180101' => '电冰箱',
        'A0206180203' => '空调机',
        'A0206180205' => '空气净化设备',
        'A0206180301' => '洗衣机',
        'A02061808' => '热水器',
        'A020201' => '复印机',
        'A020202' => '投影仪',
        'A020203' => '投影幕',
        'A020204' => '多功能一体机',
        'A02021001' => '速印机',
        'A02021101' => '碎纸机',
        'A0201060901' => '扫描仪',
        'A02081001' => '传真通信设备',
        'A02080801' => '视频会议控制台',
        'A02080802' => '视频会议多点控制器',
        'A02080803' => '视频会议会议室终端',
        'A02080804' => '音视频矩阵',
        'A02080899' => '其他视频会议系统设备',
        'A02091102' => '通用摄像机',
        'A02020502' => '镜头及器材',
        'A0202050101' => '数字照相机',
        'A0202050102' => '通用照相机',
        'A0202050103' => '静视频照相机',
        'A0202050104' => '专用照相机',
        'A0202050105' => '特殊照相机',
        'A0201080102' => '数据库管理系统',
        'A0201080104' => '办公套件',
        'A090101' => '复印纸',
        'A090201' => '鼓粉盒',
        'A090202' => '粉盒',
        'A090203' => '喷墨盒',
        'A090204' => '墨水盒',
        'A090205' => '色带',
        'A060101' => '钢木床类',
        'A060102' => '钢塑床类',
        'A060103' => '轻金属床类',
        'A060104' => '木制床类',
        'A060105' => '塑料床类',
        'A060106' => '竹制床类',
        'A060107' => '藤床类',
        'A060108' => '塑料床类',
        'A060109' => '竹床类',
        'A060110' => '藤床类',
        'A060199' => '其他床类',
        'A060201' => '钢木台、桌类',
        'A060202' => '钢台、桌类',
        'A060203' => '钢塑台、桌类',
        'A060204' => '轻金属台、桌类',
        'A060205' => '木制台、桌类',
        'A060206' => '塑料台、桌类',
        'A060207' => '藤台、桌类',
        'A060299' => '其他台、桌类',
        'A060301' => '金属骨架为主的椅凳类',
        'A060302' => '木骨架为主的椅凳类',
        'A060303' => '竹制、藤制等材料椅凳类',
        'A060304' => '塑料椅凳类',
        'A060305' => '竹制椅凳类',
        'A060306' => '藤椅凳类',
        'A060399' => '其他椅凳类',
        'A060401' => '金属骨架沙发类',
        'A060402' => '木骨架沙发类',
        'A060403' => '竹制、藤制等类似材料沙发类',
        'A060404' => '塑料沙发类',
        'A060405' => '竹制沙发类',
        'A060406' => '藤沙发类',
        'A060499' => '其他沙发类',
        'A060501' => '木质柜类',
        'A060502' => '保险柜',
        'A060503' => '金属质柜类',
        'A060599' => '其他柜类',
        'A060601' => '木质架类',
        'A060602' => '金属质架类',
        'A060699' => '其他材质架类',
        'A060701' => '木质屏风类',
        'A060702' => '金属质屏风类',
        'A060799' => '其他材质屏风类'
    );
//政采 id 和 本商城id的映射关系
    private $zc_nr_class_array = array(
        'A02010104' => "1409, 1410",
        'A02010105' => 1407,
        'A02010107' => 1408,
        'A0201060401' => 1412,
        'A02010201' => 1445,
        'A0201020201' => 1447,
        'A0201020299' => 1447,
        'A02010103' => 2221,
        'A02061504' => 2255,
        'A02010301' => 1451,
        'A0201060101' => 1256,
        'A0201060102' => '1254,1255',
        'A0201060104' => 1258,
        'A02091001' => 2195,
        'A0206180201' => 1723,
        'A0206180101' => 1721,
        'A0206180203' => 2254,
        'A0206180205' => 1722,
        'A0206180301' => 1724,
        'A02061808' => 1725,
        'A020201' => '1242,1243,1244',
        'A020202' => 1210,
        'A020203' => '1211,1212,1213',
        'A020204' => '1207,1208,1730',
        'A02021001' => 1244,
        'A02021101' => '1675,1676',
        'A0201060901' => '1223,1225,1226,1230,1227,1228,1231,1229',
        'A02081001' => '1201,1202,1203',
        'A02080801' => 2224,
        'A02080802' => 2224,
        'A02080803' => 2224,
        'A02080804' => 2224,
        'A02080899' => 2224,
        'A02091102' => '',
        'A02020502' => '2082,2083,2084,2211',
        'A0202050101' => '2082,2083,2084,2211',
        'A0202050102' => '2082,2083,2084,2211',
        'A0202050103' => '2082,2083,2084,2211',
        'A0202050104' => '2082,2083,2084,2211',
        'A0202050105' => '2082,2083,2084,2211',
        'A0201080102' => 2252,
        'A0201080104' => '2239,2250.2251',
        'A090101' => '1283,1284,2185,1286,1287,1288,1289,1706,1705',
        'A090201' => 1280,
        'A090202' => 1270,
        'A090203' => 1260,
        'A090204' => 1261,
        'A090205' => '1264,1265',
        'A060101' => '2240,2241',
        'A060102' => '2240,2241',
        'A060103' => '2240,2241',
        'A060104' => '2240,2241',
        'A060105' => '2240,2241',
        'A060106' => '2240,2241',
        'A060107' => '2240,2241',
        'A060108' => '2240,2241',
        'A060109' => '2240,2241',
        'A060110' => '2240,2241',
        'A060199' => '2240,2241',
        'A060201' => '1604,1605,1607,1608,1609,1610,1611,1734',
        'A060202' => 1606,
        'A060203' => '1604,1605,1607,1608,1609,1610,1611,1734',
        'A060204' => '1604,1605,1607,1608,1609,1610,1611,1734',
        'A060205' => 1603,
        'A060206' => '1604,1605,1607,1608,1609,1610,1611,1734',
        'A060207' => '1604,1605,1607,1608,1609,1610,1611,1734',
        'A060299' => '1604,1605,1607,1608,1609,1610,1611,1734',
        'A060301' => '1652,1653,1654,1655,1656,1613,1614,1615,1616,1617,1618,1619,2235,1651',
        'A060302' => '1652,1653,1654,1655,1656,1613,1614,1615,1616,1617,1618,1619,2235,1651',
        'A060303' => '1652,1653,1654,1655,1656,1613,1614,1615,1616,1617,1618,1619,2235,1651',
        'A060304' => '1652,1653,1654,1655,1656,1613,1614,1615,1616,1617,1618,1619,2235,1651',
        'A060305' => '1652,1653,1654,1655,1656,1613,1614,1615,1616,1617,1618,1619,2235,1651',
        'A060306' => '1652,1653,1654,1655,1656,1613,1614,1615,1616,1617,1618,1619,2235,1651',
        'A060399' => '1652,1653,1654,1655,1656,1613,1614,1615,1616,1617,1618,1619,2235,1651',
        'A060401' => '1621,1623,1624,1626,1628,1629,1630,1631',
        'A060402' => '1621,1623,1624,1626,1628,1629,1630,1631',
        'A060403' => '1621,1623,1624,1626,1628,1629,1630,1631',
        'A060404' => '1621,1623,1624,1626,1628,1629,1630,1631',
        'A060405' => '1621,1623,1624,1626,1628,1629,1630,1631',
        'A060406' => '1621,1623,1624,1626,1628,1629,1630,1631',
        'A060499' => '1621,1623,1624,1626,1628,1629,1630,1631',
        'A060501' => 1634,
        'A060502' => '1646,1647,1648,1640,1642,1643,1644,1645',
        'A060503' => 1636,
        'A060599' => '1635,1637,1638,1639,2243',
        'A060601' => '1946,1637,1699,1214,2089,1934,1935,1945',
        'A060602' => '1946,1637,1699,1214,2089,1934,1935,1945',
        'A060699' => '1946,1637,1699,1214,2089,1934,1935,1945',
        'A060701' => 2075,
        'A060702' => 2075,
        'A060799' => 2075
    );
    private $city_array = array();

	var $log_root;
	var $log_name;
	var $logtxt;
	

	public function __construct() { //构造方法
//	$this->log_root = "D:\\logs\\";                          //初始化日志文件路径
	$this->log_root = "/logs/";
	$this->log_name = "log-".date("Ymd").".log";         //初始化日志文件名
	$this->logtxt = "\r\n";
	$logfile = @fopen($this->log_root.$this->log_name, "a");
	$this->logtxt = $this->logtxt.date("Y-m-d H:i:s")."\tip：".$this->getIp()."\t".$_SERVER["REQUEST_URI"]."\r\n";
	$this->writeLog($this->logtxt);
	}
	
//写入日志内容
	public function writeLog($logtxt) {
		$logfile = @fopen($this->log_root.$this->log_name, "a");
		fwrite($logfile,$logtxt);
		fclose($logfile);
	}
	
//1获取接口令牌
    public function getAccessToken($data) {
		$this->logtxt = "";
        if (empty($data->appKey) || trim($data->appKey) != $this->appKey) {
			$this->logtxt = $this->logtxt.date("Y-m-d H:i:s")."\t获取令牌：appKey不正确\r\n";
			$this->writeLog($this->logtxt);
            exit(json_encode(array("accessToken" => '', 'returnMsg' => "appkey不正确", "isSuccess" => false)));
        }
        if (empty($data->appSecret) || trim($data->appSecret) != $this->appSecret) {
			$this->logtxt = $this->logtxt.date("Y-m-d H:i:s")."\t获取令牌：appSecret不正确\r\n";
			$this->writeLog($this->logtxt);
            exit(json_encode(array("accessToken" => '', 'returnMsg' => "appSecret不正确", "isSuccess" => false)));
        }

        $appKey = $data->appKey;
        $appSecret = $data->appSecret;
        $token = md5(time() . $appKey . $appSecret);
        $curl_time = time();
        $sql = "insert into zmkj_zf (appKey,appSecret,accessToken,curl_time) values ('{$appKey}','{$appSecret}','{$token}',{$curl_time})";
        $rs = $this->execute_data($sql);
        if ($rs) {
			$this->logtxt = $this->logtxt.date("Y-m-d H:i:s")."\t获取令牌：获取令牌成功！\r\n";
			$this->writeLog($this->logtxt);
            exit(json_encode(array("accessToken" => $token, 'returnMsg' => "验证正确", "isSuccess" => true)));
        } else {
			$this->logtxt = $this->logtxt.date("Y-m-d H:i:s")."\t获取令牌：获取令牌失败！-->操作数据库失败！\r\n";
			$this->writeLog($this->logtxt);
            exit(json_encode(array("accessToken" => "", 'returnMsg' => "操作数据库失败,请联系管理员", "isSuccess" => false)));
        }
    }

//2获取商品分类
//    (
//    [accessToken] => 87d0dff7a046b04126f8fd2b5d6bc4ee
//    [appKey] => epoint
//)
    public function getProductCategory($data) {
         //var_dump($data);
		$this->logtxt = date("Y-m-d H:i:s")."\t获取商品分类";
        if($this->type=="php"){   //php分类映射
        $this->check_login($data);
        $zc_name_array1 = $this->zc_name_array;
        $zc_name_array2 = array();
        foreach ($zc_name_array1 as $key => $value) {
            $zc_name_array3 = array();
            $zc_name_array3['name'] = $value;
            $zc_name_array3['categoryId'] = $key;
            $zc_name_array2[] = $zc_name_array3;
        }
        }elseif($this->type=="mysql"){   //mysql分类映射
                 $sql="select * from zmkj_zf_class";
                 $class_info=$this->select_data($sql); 
                 $zc_name_array2=array();
                  foreach($class_info as $key=>$value){
                  $zc_name_array3 = array();
                  $zc_name_array3['name'] = $value['class_name'];
				  //$this->logtxt = $this->logtxt."\r\n    ".$value['class_name'];
                  $zc_name_array3['categoryId'] = $value['class_id'];
				  //$this->logtxt = $this->logtxt.":    ".$value['class_id'];
				  $this->logtxt = $this->logtxt."\r\n\t\t\t".$value['class_id']."\t\t".$value['class_name'];
                  $zc_name_array2[] = $zc_name_array3;
                  }
        }
		$this->logtxt = $this->logtxt."\r\n";
		$this->writeLog($this->logtxt);
        exit(json_encode(array("result" => $zc_name_array2, 'returnMsg' => "验证正确", "isSuccess" => true)));
    }

//3获取商品池
//    (
//    [appKey] => epoint
//    [accessToken] => 52b8d517b10cd89248b6af1a9e2adf5e
//    [categoryId] => A02010104---因为两种数据类型，需要进行两次测试
//)
    public function getProductPool($data) {       
		$this->check_login($data);
        $zf_class_id = $data->categoryId;
		$this->logtxt = date("Y-m-d H:i:s")."\t获取商品池\t商品品目：".$zf_class_id."\r\n\t\t"; 
        if($this->type=="php"){
			$zc_nr_class_array = $this->zc_nr_class_array;
			//print_r($zc_nr_class_array[$zf_class_id]);
			if (!empty($zc_nr_class_array[$zf_class_id])) {  //有对应的了类型
				if (is_int($zc_nr_class_array[$zf_class_id])) {  //1对1
					$sql = "select goods_id from zmkj_goods where gc_id={$zc_nr_class_array[$zf_class_id]} and store_id=51 and goods_state=1";
					//print_r($sql);
					$rs = $this->select_data($sql);
				} else {  //1对多
					$sql = "select goods_id from zmkj_goods where gc_id in ({$zc_nr_class_array[$zf_class_id]}) and store_id=51 and goods_state=1";
					$rs = $this->select_data($sql);
				}
				$rs1 = array();
				foreach ($rs as $key => $value) {
					$rs1[] = $value['goods_id'];
				}
				exit(json_encode(array("sku" => $rs1, 'returnMsg' => "分类商品编码信息", "isSuccess" => true)));
			} else {  //没有对应的类型
				exit(json_encode(array("sku" => "", 'returnMsg' => "此分类暂时没有商品", "isSuccess" => false)));
			}
        }elseif($this->type=="mysql"){
			$sql="select * from zmkj_zf_class where class_id like '{$zf_class_id}'";
			$class_info=$this->select_data($sql);
			$id=$class_info[0]['id'];
					$sql = "select goods_id from zmkj_goods where zf_class_id={$id} and store_id=51 and goods_state=1";
					//print_r($sql);
					$rs = $this->select_data($sql);
				$rs1 = array();
				$i=0;
				foreach ($rs as $key => $value) {
					$rs1[] = $value['goods_id'];
					$this->logtxt = $this->logtxt."\t".$value['goods_id'];
					$i++;
					if($i>=10){
						$this->logtxt = $this->logtxt."\r\n\t\t";
						$i=0;
					}
				}
				$this->logtxt = $this->logtxt."\r\n";
				$this->writeLog($this->logtxt);
				exit(json_encode(array("sku" => $rs1, 'returnMsg' => "分类商品编码信息", "isSuccess" => true)));
        }
    }

//4获取商品详情接口
//        (
//    [appKey] => epoint
//    [accessToken] => 3aa1798a47611af92a66a54a4570a36c
//    [sku] => 103074
//)
    public function getProductDetail($data) {
        $this->check_login($data);
        $goods_id = $data->sku;
        $sql = "select goods_id,zmkj_zf_class.class_id,zmkj_goods.store_id,zmkj_goods.goods_image,zmkj_brand.brand_name,zmkj_goods.goods_spec,zmkj_goods.goods_name,zmkj_goods.gc_id,zmkj_goods_common.goods_body,zmkj_goods.goods_param from zmkj_goods";
        $sql .= " left join zmkj_brand on zmkj_goods.brand_id=zmkj_brand.brand_id";
        $sql .= " left join zmkj_goods_common on zmkj_goods.goods_commonid=zmkj_goods_common.goods_commonid";
         $sql .= " left join zmkj_zf_class on zmkj_goods.zf_class_id=zmkj_zf_class.id";
        $sql .= " where goods_id={$goods_id}";
        //echo $sql;die;
//print_r($sql);
        $rs = $this->select_data($sql);
//print_r($rs);die;
        $goods_info = array();
        if (!empty($rs) && is_array($rs)) {
            //sku
            $goods_info['sku'] = $rs[0]['goods_id'];
            //图片
			$rs[0]['goods_image'] = str_ireplace('.', '_1280.', $rs[0]['goods_image']);
            $goods_info['image'] = 'http://'.$this->url . $this->upload_path . $rs[0]['store_id'] . "/" . $rs[0]['goods_image'];
            //品牌
            $goods_info['brand'] = $rs[0]['brand_name'];
            if (strlen($rs[0]['goods_name']) >= 300) {
                $goods_name = substr($rs[0]['goods_name'], 0, 299);
            } else {
                $goods_name = $rs[0]['goods_name'];
            }
            //商品名称
            $goods_info['name'] = $goods_name;
            //商品分类
            if($this->type=="php"){
            
            $gc_id1=$rs[0]['gc_id'];
            $zc_nr_class_array=$this->zc_nr_class_array;//分类映射

            $key=array_search($gc_id1,$zc_nr_class_array);//1410
            if($key){
              $gc_id2=$key;
            }else{
              foreach($zc_nr_class_array as $key1=>$value){
                 if(is_string($value)){
                   $str_array=explode(",",$value);
                   //print_r($str_array);die;
                   if(in_array($gc_id1,$str_array)){
                       $gc_id2=$key1;
                   }
                 }
              }
            }
            }elseif($this->type=="mysql"){
            $gc_id2=$rs[0]['class_id'];
            }
            $goods_info['category'] = $gc_id2;
            //商品介绍
            $goods_info['introduction'] = $rs[0]['goods_body'];
            //商品参数
            $param1= unserialize($rs[0]['goods_param']); 
            $param2=array();
            foreach($param1 as $key => $value){
              $param2+=$value;
            }
            $goods_info['param']=$param2;
            //提示信息   
            $goods_info['returnMsg'] = "商品详情信息";
            $goods_info['isSuccess'] = true;
        } else {
            $goods_info['returnMsg'] = "获取商品详情信息错误";
            $goods_info['isSuccess'] = false;
        }
        //print_r($goods_info);
        exit(json_encode($goods_info));
    }

//5获取商品图片接口商品池
//    (
//    [appKey] => epoint
//    [accessToken] => 64fecfdbe568eefc11073191f45a88dc
//    [sku] => Array
//        (
//            [0] => 103074
//        )
//
//)
    public function getProductImage($data) {
 //var_dump($data);
        $this->check_login($data);
        $goods_id_array = $data->sku;
        $img_array = array();
        foreach ($goods_id_array as $key => $goods_id) {
            $sql = "select store_id,goods_image,is_default from zmkj_goods_images where goods_id = {$goods_id}";
            $rs = $this->select_data($sql);
            $img_array1 = array();
            if (!empty($rs) && is_array($rs)) {
                foreach ($rs as $key => $value) {
                    $image = array();
					$value['goods_image'] = str_ireplace('.', '_1280.', $value['goods_image']);
                    $image['path'] = 'http://'.$this->url . $this->upload_path . $value['store_id'] . "/" . $value['goods_image'];
                    $image['primary'] = $value['is_default'];
                    $img_array1['skuId'] = $goods_id;
                    $img_array1['urls'][] = $image;
                }
            } else {  //common_id获取商品图片。
                $sql = "select zmkj_goods_images.goods_image,zmkj_goods_images.store_id,zmkj_goods_images.is_default from zmkj_goods_images ";
                $sql .= " left join zmkj_goods on zmkj_goods.goods_commonid=zmkj_goods_images.goods_commonid";
                $sql .= " where zmkj_goods.goods_id = {$goods_id}";
//print_r($sql);die;
                $rs = $this->select_data($sql);
                if (!empty($rs) && is_array($rs)) {
                    foreach ($rs as $key => $value) {
                        $image = array();
						$value['goods_image'] = str_ireplace('.', '_1280.', $value['goods_image']);
                        $image['path'] = 'http://'.$this->url . $this->upload_path . $value['store_id'] . "/" . $value['goods_image'];
                        $image['primary'] = $value['is_default'];
                        $img_array1['skuId'] = $goods_id;
                        $img_array1['urls'][] = $image;
                    }
                } else {
                    $image['path'] = "";
                    $image['primary'] = "";
                    $img_array1['skuId'] = $goods_id;
                    $img_array1['urls'][] = $image;
                }
            }
            $img_array[] = $img_array1;
        }
        //print_r($_SERVER);
        //print_r($img_array);
        exit(json_encode(array("result" => $img_array, 'returnMsg' => "商品图片信息", "isSuccess" => true)));
    }

//6商品上下架状态查询接口:为了商家的产品，后来下架
//    (
//    [appKey] => epoint
//    [accessToken] => 6195d0a669ca6b769b8eeb97407698eb
//    [sku] => Array
//        (
//            [0] => 103074
//        )
//
//)
    public function getProductOnShelvesInfo($data) {
//print_r($data);
        $this->check_login($data);
		$this->logtxt = "\t\t\t商品上下架状态：";
        $goods_id_array = $data->sku;
        $goods_state = array();
        foreach ($goods_id_array as $key => $goods_id) {
//先通过goods_id调取商品状态；
            $sql = "select goods_state from zmkj_goods where goods_id = {$goods_id}";
            $rs = $this->select_data($sql);
            $goods_state1 = array();
            if (!empty($rs) && is_array($rs)) { //存在
                $goods_state1['skuId'] = $goods_id;
                $goods_state1['listState'] = $rs[0]['goods_state'];
				$this->logtxt = $this->logtxt."\t".$goods_id."\t:".$rs[0]['goods_state'];
            } else { //不存在
                $goods_state1['skuId'] = $goods_id;
                $goods_state1['listState'] = "";
				$this->logtxt = $this->logtxt."\t".$goods_id."\t:无此商品！";
            }
            $goods_state[] = $goods_state1;
        }
		$this->logtxt = $this->logtxt."\r\n";
		$this->writeLog($this->logtxt);
        exit(json_encode(array("onShelvesList" => $goods_state, 'returnMsg' => "商品上下架状态信息", "isSuccess" => true)));
    }

//7商品映射关系查询接口
    public function getProductInfoFromEC($data) {
//print_r($data);
        $this->check_login($data);
        $sql = "select productId,productName,productUrl,skuId,productNameEC,productUrlEC from zmkj_goods_orm";
        $goods_orm = $this->select_data($sql);
        if (empty($goods_orm)) {
            exit(json_encode(array("productInfoList" => "", 'returnMsg' => "未添加任何商品映射信息", "isSuccess" => true)));
        } else {
            exit(json_encode(array("productInfoList" => $goods_orm, 'returnMsg' => "商品映射关系信息", "isSuccess" => true)));
        }
    }

//8商品库存查询接口
//    (
//    [appKey] => epoint
//    [accessToken] => 8beea58d1babf341b8e5c465980080f2
//    [sku] => 103074
//    [num] => 1
//    [cityId] => C98
//    [countyId] => Q1013
//)
    public function getProductInventory($data) {
          $this->check_login($data);
//        include "city.php";
//        $city_array = city();
//
//        foreach ($city_array as $key => $value) {
//            $sql = "select area_id from zmkj_area where area_name = '" . $value['Name'] . "'";
//            $rs = $this->select_data($sql);
//
//            if ($rs) {
//                $city_array[$key]['id'] = $rs[0]['area_id'];
//            } else {
//                $city_array[$key]['id'] = "<span color='red'>没有</span>";
//            }
//            //sleep(0.2);
////                        print_r($city_array);die;
//        }
//        foreach(){
//            
//        }
//        //print_r($city_array);
//        die;
        $sql = "select goods_storage,goods_id from zmkj_goods where goods_id = {$data->sku}";
        $rs = $this->select_data($sql);
        if ($rs) {
            exit(json_encode(array("skuId" => $data->sku, "state" => "00", 'returnMsg' => "有货", "isSuccess" => true)));
        } else {
            exit(json_encode(array("skuId" => $data->sku, "state" => "03", 'returnMsg' => "暂不销售", "isSuccess" => false)));
        }
    }

//9获取商品折扣价格接口
//    (
//    [appKey] => epoint
//    [accessToken] => 75fea90e269c945a437c508ade41cb05
//    [sku] => Array
//        (
//            [0] => 103074
//        )
//
//)
    public function queryCountPrice($data) {
        $this->check_login($data);
        $goods_id_array = $data->sku;
        $goods_state = array();
        foreach ($goods_id_array as $key => $goods_id) {
            $sql = "select goods_promotion_price from zmkj_goods where goods_id = {$goods_id}";
            $rs = $this->select_data($sql);
            $goods_state1 = array();
            if (!empty($rs) && is_array($rs)) { //存在，则展示实际价格。
                $goods_state1['skuId'] = $goods_id;
                $goods_state1['price'] = $rs[0]['goods_promotion_price'];
                $goods_state1['discount'] = 1;
            } else { //不存在,将价格设置为最大
                $goods_state1['skuId'] = $goods_id;
                $goods_state1['price'] = 9999999999;
                $goods_state1['discount'] = 1;
            }
            $goods_state[] = $goods_state1;
        }
        exit(json_encode(array("priceList" => $goods_state, 'returnMsg' => "商品折扣价格", "isSuccess" => true)));
    }

//10创建订单接口
    //        tradeNO	交易流水号（流水号相同，表示同一笔订单交易，）	String	是
//sku 	数组		
    //skuId	商品编码	String	是
    //num	商品数量	int	是
    //price	商品单价	Decimal	是
//name	收货人	String	是
//provinceId	省	String	是
//cityId	市	String	是
//countyId	区	String	是
//address	详细地址	String（40）	是
//phone	座机号	String	否
//mobile	手机号	String	是
//email	邮箱	String	是
//remark	备注【少于100字】	String	否
//
//invoiceState	是否开发票【1=开，0=不开】	int	是
//invoiceType	发票类型【1=增值发票 2=普通发票】	int	是
//companyName	发票抬头	String(32)	是
//invoiceContent	发票内容	int	是
//
//amount	订单金额	Decimal	是
//freight	运费	int 	是
//payment	支付方式【1：公务卡支付；2：账期支付】	String	是
    public function createOrder($data) {
		$this->logtxt = "";
		$this->logtxt = $this->logtxt.json_encode($data)."\r\n";
		$this->writeLog($this->logtxt);
        //$this->check_login($data);
//1.检查电话、邮箱、地址数据全不全
        if (empty($data->mobile) || empty($data->email)) {
            exit(json_encode(array('errorCode' => 'ORDER001_02_1', 'returnMsg' => "电话或者邮箱信息不全", "isSuccess" => false)));
        }
        if (empty($data->provinceId) || empty($data->cityId) || empty($data->countyId) || empty($data->address)) {
//            exit(json_encode(array('errorCode' => 'ORDER001_02', 'returnMsg' => "地址数据不全，请检查后重新下单！", "isSuccess" => false)));
        }
//2.检查城市编码是否正确:城市编码是area_id,判断是否存在，且两者具有关联性，从下往上检查。
       /*  $sql = "select area_id,area_parent_id from zmkj_area where area_id in ({$data->countyId},{$data->cityId},{$data->provinceId})";
        $area_array = $this->select_data($sql);
        //存在性验证。
        if (!(!empty($area_array) && is_array($area_array) && count($area_array) == 3)) {
            exit(json_encode(array('errorCode' => 'ORDER001_02', 'returnMsg' => "城市编码不正确，请重新获取！", "isSuccess" => false)));
        }
        //上下级关联性验证。
        $area_array1 = array();
        foreach ($area_array as $value) {
            $area_array1[$value['area_id']] = $value['area_parent_id'];
        }
        //print_r($data);
        //print_r($area_array1);
       if (!(!empty($area_array1[$data->countyId]) && $area_array1[$data->countyId] == $data->cityId)) {
           exit(json_encode(array('errorCode' => 'ORDER001_02', 'returnMsg' => "区级编码和市级编码不关联，请重新获取！", "isSuccess" => false)));
        }
        if (!(!empty($area_array1[$data->cityId]) && $area_array1[$data->cityId] == $data->provinceId)) {
           exit(json_encode(array('errorCode' => 'ORDER001_03', 'returnMsg' => "市级编码和省级编码不关联", "isSuccess" => false)));
        } */
//3.支付方式  1:公务卡支付；2：账期支付
        $payment = array(1, 2);
        if (!(!empty($data->payment) && in_array($data->payment, $payment))) {
           exit(json_encode(array('errorCode' => 'ORDER002', 'returnMsg' => "支付方式不正确，请检查后重新下单！", "isSuccess" => false)));
        }
//4.发票类型
        $invoice_state = array(0, 1);//是否开发票
        if (!in_array($data->invoiceState, $invoice_state)) {
            exit(json_encode(array('errorCode' => 'ORDER003', 'returnMsg' => "发票参数错误，请重新请求下单", "isSuccess" => false)));
        }
        //print_r($data->invoiceType);die;
        $invoice_type = array(1, 2);//发票类型
       if ($data->invoiceState == 1 && in_array($data->invoiceType, $invoice_type)||$data->invoiceState == 0) {
          
       }else{
        exit(json_encode(array('errorCode' => 'ORDER003_01', 'returnMsg' => "发票类型不正确，请检查后重新下单！", "isSuccess" => false)));
        }
       if (($data->invoiceState == 1 && !empty($data->companyName) && !empty($data->invoiceContent) )||$data->invoiceState == 0) {  //发票内容
         
      }else{
         exit(json_encode(array('errorCode' => 'ORDER003_02', 'returnMsg' => "发票信息为空，请检查后重新下单！", "isSuccess" => false)));
       }
        
//5.商品是否存在：
       //  var_dump($data);
        $goods_array = json_decode(json_encode($data->sku), true);
        if (!(is_array($goods_array) && count($goods_array) > 0)) {
            exit(json_encode(array('errorCode' => 'ORDER005', 'returnMsg' => "接受的sku数据异常", "isSuccess" => false)));
        }
        $goods_id_array = array(); //商品id:一维数组
        $goods_num_array = array();
        //print_r($goods_array);
        foreach ($goods_array as $value) {
            $goods_id_array[] = $value['skuId'];
            $goods_num_array[$value['skuId']] = $value['num'];
        }
        //print_r($goods_num_array);
        $goods_id = implode(",", $goods_id_array);
        //print_r($goods_id);
        $sql = "select goods_id,goods_state,goods_promotion_price,goods_freight from zmkj_goods where goods_id in ({$goods_id}) and store_id=51";
        $goods_info = $this->select_data($sql);
        //所有商品是否存在
        if (!(is_array($goods_info) && !empty($goods_info))) {
            exit(json_encode(array('errorCode' => 'ORDER006_1', 'returnMsg' => "暂不销售，请检查后重新下单！", "isSuccess" => false)));
        }
        //对单个商品存在性进行判断
        if (count($goods_id_array) != count($goods_info)) {
            exit(json_encode(array('errorCode' => 'ORDER006_2', 'returnMsg' => "部分商品暂不销售，请检查后重新下单！", "isSuccess" => false)));
        }
        //对商品的价格、状态进行判断：
        $goods_info1 = array();
        $total_goods_price = 0;
        $total_goods_freight = 0;
        //print_r($goods_info);
        //print_r($goods_num_array);
        foreach ($goods_info as $value) {
            $total_goods_price += $value['goods_promotion_price'] * $goods_num_array[$value['goods_id']];
            $total_goods_freight += $value['goods_freight'];
            $goods_info1[$value['goods_id']] = $value;
            if ($value['goods_state'] != 1) {
                exit(json_encode(array('errorCode' => 'ORDER006_3', 'returnMsg' => "sku为" . $value['goods_id'] . "的商品已下架，请检查后重新下单！", "isSuccess" => false)));
            }
        }
        //echo $total_goods_price;
        //对单价进行判断
        foreach ($goods_array as $value) {
            if ($goods_info1[$value['skuId']]['goods_promotion_price'] != $value['price']) {
                exit(json_encode(array('errorCode' => 'ORDER006_4', 'returnMsg' => "sku为" . $value['skuId'] . "的商品价格发生变动，请检查后重新下单！", "isSuccess" => false)));
            }
        }
        //对运费进行判断
        if ($total_goods_freight != $data->freight) {
            exit(json_encode(array('errorCode' => 'ORDER006_5', 'returnMsg' => "运费价格发生变动，请检查后重新下单！", "isSuccess" => false)));
        }
        //对总价进行判断
        $total_prices = $total_goods_price + $total_goods_freight;
		$total_price = number_format($total_prices, 2,'.','');
		if ($total_price != $data->amount) {
            exit(json_encode(array('errorCode' => 'ORDER006_6', 'returnMsg' => "总价发生变动，请检查后重新下单！", "isSuccess" => false)));
        }
        //送达时间
        $data = date("Y-m-d", time() + 7 * 3600 * 24);
        exit(json_encode(array("orderId" => $this->order($data->tradeNO), "sku" => $goods_array, 'arriveData' => $data, 'amount' => $total_price, 'freight' => $total_goods_freight, 'returnMsg' => "创建订单完成", "isSuccess" => true)));
    }

//检测token是否存在
    private function check_login($data) {

        if (empty($data->appKey) || trim($data->appKey) != $this->appKey) {
			$this->writeLog("\t\t\t验证失败：appKey不正确！\r\n");
            exit(json_encode(array("result" => '', 'returnMsg' => "appKey不正确", "isSuccess" => false)));
        }
        //print_r($data);
//从数据库获取curl_time最大的那条数据
        $sql = "select curl_time,accessToken from zmkj_zf order by id desc limit 1";
        $info = $this->select_data($sql);
//        print_r($info);
//        die;
//做时间验证
        if ($info[0]['curl_time'] + 3600 * 24 < time()) {
			$this->writeLog("\t\t\t验证失败：token已经过时，请重新请求！\r\n");
            exit(json_encode(array("result" => '', 'returnMsg' => "token已经过时，请重新请求", "isSuccess" => false)));
        }
        if (empty($info) || trim($info[0]['accessToken']) != trim($data->accessToken)) {
			$this->writeLog("\t\t\t验证失败：token不正确！\r\n");
            exit(json_encode(array("result" => '', 'returnMsg' => "token不正确", "isSuccess" => false)));
        } else {
            return true;
        }
    }

//增加数据
    private function execute_data($sql) {
//链接数据库
        $conn = @new mysqli($this->ip, $this->user, $this->pwd, $this->dbname, $this->port);
// 检测连接
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        $conn->query("set namse utf8");
        $rs = $conn->query($sql);
        $conn->close();
        return $rs;
    }

//查询数据
    private function select_data($sql) {
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

    //生成交易订单号
    private function order($data) {
        $order = md5(time() . $data . rand(1, 99999));
        return $order;
    }
	
	
	//获取客户端ip
	private function getIp(){
        if(!empty($_SERVER["HTTP_CLIENT_IP"]))
        {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        }
        else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        else if(!empty($_SERVER["REMOTE_ADDR"]))
        {
            $cip = $_SERVER["REMOTE_ADDR"];
        }
        else
        {
            $cip = '';
        }
        preg_match("/[\d\.]{7,15}/", $cip, $cips);
        $cip = isset($cips[0]) ? $cips[0] : 'unknown';
        unset($cips);
        return $cip;
    }

}
