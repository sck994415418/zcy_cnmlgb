<?php
/**
 * 政采云平台商品管理 v3-b12
 *
 */
defined('InShopNC') or exit('Access Invalid!');

class zcy_configControl extends BaseSellerControl
{
    //需要对接政采云的店铺store_id
//	private $zcy_store = array(51,61);

    public function __construct()
    {
        parent::__construct();
        Language::read('member_store_goods_index');

//		if(! in_array($_SESSION["store_id"] , $this->zcy_store)){
//			exit("当前店铺没有此权限！请<a href=\"/shop/index.php?act=seller_center&op=index\">返回</a>");
//		}
        //验证是否有政采云操作权限
        include_once 'zcy_common.php';
        $res = new zcy_commonControl();
        $res->aa();
    }

    public function indexOp()
    {
//        include(BASE_PATH . '/control/zcy_connect_data.php');
//        $zcy_data = new zcy_data();
//        $data = '{"id":3,"fullName":"\u6d77\u5c14\/Haier","logo":"http:\/\/zcy-test.img-cn-hangzhou.aliyuncs.com\/users\/2\/20160116134934720.jpeg","status":1,"createdAt":null,"updatedAt":1587452628000,"auditStatus":null}';
//        $data = json_decode($data,true);
//        $id = $data["id"];
//        $fullName = "".$data["fullName"]."";
//        $logo = $data["logo"];
//        $status = $data["status"];
//        $createdAt = $data["createdAt"]?$data["createdAt"]:0;
//        $updatedAt = $data["updatedAt"]?$data["updatedAt"]:0;
//        $auditStatus = $data["auditStatus"]?$data["auditStatus"]:0;
//        $sql = "update `zcy_brand` set `fullName` = '" . $fullName . "', `logo` = '" . $logo . "', `status` = '" . $status . "', `createdAt` = " . $createdAt . ", `updatedAt` = " . $updatedAt . ", `auditStatus` = " . $auditStatus. " where `id` = " . $id;
//        $rs = $zcy_data->execute_data_return_affected_rows($sql);
//        var_dump($sql);
//        echo '<pre>';
//        var_dump($rs);
//        die;
//
//        $sql = "select * from `zcy_brand` where `id` = " . 9;
//        $rs = $zcy_data->select_data($sql);
//        var_dump($rs);die;



        $this->zcy_configOp();
    }

    /*
     *政采云平台商品列表
     *
     */
    public function zcy_configOp()
    {
        if(empty($_GET['type'])){
            $_GET['type'] = 'zcy_update_category';
        }
        switch ($_GET['type']) {
            case 'zcy_update_category':
                $this->profile_menu('zcy_update_category');
                break;
            case 'zcy_update_category_attrs':
                $this->profile_menu('zcy_update_category_attrs');
                break;
            case 'zcy_update_brand':
                $this->profile_menu('zcy_update_brand');
                break;
            case 'verify':
                $this->profile_menu('zcy_goods_verify');
                break;
            case 'refuse':
                $this->profile_menu('zcy_goods_refuse');
                break;
            default:
                $this->profile_menu('zcy_update_category');
                break;
        }

        switch ($_GET['type']) {
            case 'zcy_update_category'://更新商品类目
                Tpl::showpage('zcy_update_category');
                break;
            case 'zcy_update_category_attrs':// 更新类目属性
                Tpl::showpage('zcy_update_category_attrs');
                break;
            case 'zcy_update_brand':// 更新商品品牌
                Tpl::showpage('zcy_update_brand');
                break;
            case 'update_productid':// 主动映射的商品改价
                Tpl::showpage('store_goods.update_productid');
                break;
            default://逐个商品改价
                Tpl::showpage('zcy_update_category');
                break;

        }
    }


    /**
     * 用户中心右边，小导航
     *
     * @param string $menu_key 当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_key = '')
    {
        $menu_array = array(
            array('menu_key' => 'zcy_update_category', 'menu_name' => "更新商品类目", 'menu_url' => urlShop('zcy_config', 'index', array('type' => 'zcy_update_category'))),
            array('menu_key' => 'zcy_update_category_attrs', 'menu_name' => "更新类目属性", 'menu_url' => urlShop('zcy_config', 'index', array('type' => 'zcy_update_category_attrs'))),
            array('menu_key' => 'zcy_update_brand', 'menu_name' => "更新商品品牌", 'menu_url' => urlShop('zcy_config', 'index', array('type' => 'zcy_update_brand'))),
//            array('menu_key' => 'zcy_goods_freez', 'menu_name' => "销售区划管理", 'menu_url' => urlShop('zcy_goods', 'index', array('type' => 'freez'))),
//            array('menu_key' => 'zcy_goods_refuse', 'menu_name' => "仓库管理", 'menu_url' => urlShop('zcy_goods', 'index', array('type' => 'refuse')))
        );
        Tpl::output('member_menu', $menu_array);
        Tpl::output('menu_key', $menu_key);
    }

    /**
     * 用户中心政采云-基本配置-获取商品类目
     * ajax获取政采云商品类目，返回json
     *
     */
    public function get_categoryOp()
    {
        $data = file_get_contents("php://input");
        if (!is_null($data)) {
            $data = json_decode($data);
            if (is_object($data)) {
                $data = (array)$data;
            }
            $root = $data["root"];
            $depth = $data["depth"];
            if ($root == "") {
                $root = 0;
            }
            if ($depth == "") {
                $depth = 2;
            }
            require_once(BASE_PATH . '/../zcy/nr_zcy.php');
            $zcy = new nr_zcy;
            $rs = $zcy->get_category($root, $depth);
//            Tpl::output('outputs',$rs);

            echo $rs;
        }
    }


    /**
     * 用户中心政采云-基本配置-单条更新商品类目
     * ajax获取政采云商品类目，返回json
     *
     */
    public function update_categoryOp()
    {
        $data = file_get_contents("php://input");
        if (!is_null($data)) {
            $data = json_decode($data);
            if (is_object($data)) {
                $data = (array)$data;
            }
            $id = $data["id"];
            $pid = $data["pid"];
            $name = $data["name"];
            $level = $data["level"];
            $hasChildren = $data["hasChildren"];
            $status = $data["status"];
            $hasSpu = $data["hasSpu"];
            if ((!is_null($id)) and (!is_null($name)) and (!is_null($pid)) and (!is_null($level)) and (!is_null($hasChildren)) and (!is_null($status)) and (!is_null($hasSpu))) {
                if (!@include(BASE_PATH . '/control/zcy_connect_data.php')) exit('zcy_connect_data.php isn\'t exists!');
                $zcy_data = new zcy_data();
                $sql = "select `id` from `zmkj_zcy_category` where `id` = $id";
                $rs = $zcy_data->select_data($sql);
                if (is_array($rs)) {
                    if (empty($rs)) {
                        $sql = "insert into `zmkj_zcy_category` (`id`,`pid`,`name`,`level`,`hasChildren`,`status`,`hasSpu`,`update_time`) values($id,$pid,\"$name\",$level," . (int)$hasChildren . ",$status," . (int)$hasSpu . ",now())";
                        $rs = $zcy_data->execute_data_return_affected_rows($sql);
                        if ($rs["isSuccess"] and ($rs["affectedRows"] == 1)) {
                            exit(json_encode(array('resultMsg' => "添加成功", "isSuccess" => true)));
                        } else {
                            exit(json_encode(array('resultMsg' => $rs["resultMsg"], "isSuccess" => false)));
                        }
                    } else {
                        $sql = "update `zmkj_zcy_category` set `pid` = " . $pid . ",`name` = \"" . $name . "\",`level` = " . $level . ",`hasChildren` = " . (int)$hasChildren . ",`status` = " . $status . ",`hasSpu` = " . (int)$hasSpu . ",`update_time` = now() where `id` = " . $id;
                        $rs = $zcy_data->execute_data_return_affected_rows($sql);
                        if (($rs["isSuccess"]) and ($rs["affectedRows"] == 1)) {
                            exit(json_encode(array('resultMsg' => "更新成功", "isSuccess" => true)));
                        } else {
                            exit(json_encode(array('resultMsg' => $rs["resultMsg"], "isSuccess" => false)));
                        }
                    }
                } else {
                    exit(json_encode(array('resultMsg' => $rs, "isSuccess" => false)));
                }
            } else {
                exit(json_encode(array('resultMsg' => "参数不全", "isSuccess" => false)));
            }
        } else {
            exit(json_encode(array('resultMsg' => "参数错误", "isSuccess" => false)));
        }
    }


    /**
     * 用户中心政采云-基本配置-更新商品类目
     * ajax获取商品分类的子级数据
     *
     */
    public function ajax_get_categoryOp()
    {
        $id = intval($_GET["id"]);
        if ($id <= 0) {
            exit(json_encode(array('isSuccess' => false, 'resultMsg' => "参数错误！")));
        }
        if (!@include(BASE_PATH . '/control/zcy_connect_data.php')) exit(json_encode(array('isSuccess' => false, 'resultMsg' => 'zcy_connect_data.php isn\'t exists!')));
        $zcy_data = new zcy_data();
        $sql = "select * from `zmkj_zcy_category` where `pid` = $id";
        $rs = $zcy_data->select_data($sql);
        if (is_array($rs)) {
            exit(json_encode(array('isSuccess' => true, 'response_data' => $rs, 'resultMsg' => '成功')));
        } else {
            exit(json_encode(array('isSuccess' => false, 'response_data' => '', 'resultMsg' => $rs)));
        }
    }


    /**
     * 用户中心政采云-基本配置-更新类目属性
     * ajax获取商品当前分类id下的所有3级分类id
     *
     */
    public function ajax_get_category_idOp()
    {
        $id = $_GET["id"];
        if (!is_numeric($id)) {
            exit(json_encode(array('isSuccess' => false, 'resultMsg' => "参数错误！id=" . $id)));
        } else {
            $id = intval($id);
            if (!@include(BASE_PATH . '/control/zcy_connect_data.php')) exit(json_encode(array('isSuccess' => false, 'resultMsg' => 'zcy_connect_data.php isn\'t exists!')));
            $zcy_data = new zcy_data();
            if ($id == 0) {    //$id==0,更新全部
                $sql = "select `id` , `name` from `zmkj_zcy_category` where `level` = 3";
            } else {
                $sql = "select `id` , `name` , `level` from `zmkj_zcy_category` where `id` = $id";
                $rs = $zcy_data->select_data($sql);
                if (is_array($rs)) {
                    if (!empty($rs)) {
                        switch ($rs[0]["level"]) {
                            case 1:
                                $sql = "select `id` , `name` from `zmkj_zcy_category` where `pid` in (select `id` from `zmkj_zcy_category` where `pid` = $id)";
                                break;
                            case 2:
                                $sql = "select `id` , `name` from `zmkj_zcy_category` where `pid`  = $id";
                                break;
                            case 3:
                                exit(json_encode(array('isSuccess' => true, 'response_data' => array(array('id' => "$id", 'name' => $rs[0]["name"])), 'resultMsg' => '成功')));
                                break;
                        }
                    }
                } else {
                    exit(json_encode(array('isSuccess' => false, 'response_data' => '', 'resultMsg' => $rs)));
                }
            }
            $rs = $zcy_data->select_data($sql);
            if (is_array($rs)) {
                exit(json_encode(array('isSuccess' => true, 'response_data' => $rs, 'resultMsg' => '成功')));
            } else {
                exit(json_encode(array('isSuccess' => false, 'response_data' => '', 'resultMsg' => $rs)));
            }
        }

    }


    /**
     * 用户中心政采云-基本配置-更新类目属性
     * ajax获取商品3级分类的属性列表
     *
     */
    public function update_category_attrsOp()
    {
        $id = $_GET["id"];
        if (!is_numeric($id)) {
            exit(json_encode(array('isSuccess' => false, 'resultMsg' => "参数错误！")));
        } else {
            if (intval($id) <= 0) {
                exit(json_encode(array('isSuccess' => false, 'resultMsg' => "参数错误！")));
            } else {
                $id = intval($id);
                require_once(BASE_PATH . '/../zcy/nr_zcy.php');
                $zcy = new nr_zcy;
                $category_attrs = $zcy->get_category_attrs($id);
//				print_r($category_attrs);
//				die();
                if ($category_attrs["success"]) {
                    $category_attrs_ids = array();
                    if (!@include(BASE_PATH . '/control/zcy_connect_data.php')) exit(json_encode(array('isSuccess' => false, 'resultMsg' => 'zcy_connect_data.php isn\'t exists!')));
                    $zcy_data = new zcy_data();
                    foreach ($category_attrs["data_reponse"] as $category_attr) {
                        $category_attrs_ids[] = $category_attr["propertyId"];
                        $sql = "select `propertyId` from `zcy_category_property` where `propertyId` = " . $category_attr["propertyId"];
                        $rs = $zcy_data->select_data($sql);
                        if (!(is_array($rs))) {
                            exit(json_encode(array('isSuccess' => false, 'response_data' => '', 'resultMsg' => $rs)));
                        }
                        if (!empty($category_attr["attrVals"])) {
                            $attrVals = implode(",", $category_attr["attrVals"]);
                        } else {
                            $attrVals = "";
                        }
                        if (empty($rs)) {            //添加表zcy_category_property属性行
                            $sql = "insert into `zcy_category_property` (`propertyId`,`propertyGroup`,`attrName`,`attrVals`,`isRequired`,`isImportant`,`isSukCandidate`,`isSkuCandidate`,`isUserDefined`,`isKey`,`isBound`,`unit`,`valueType`,`multi`,`composite`,`user_attrVals`) values(" . $category_attr["propertyId"] . ",'" . $category_attr["group"] . "','" . $category_attr["attrName"] . "','" . $attrVals . "'," . (int)$category_attr["attrMetas"]["isRequired"] . "," . (int)$category_attr["attrMetas"]["isImportant"] . "," . (int)$category_attr["attrMetas"]["isSukCandidate"] . "," . (int)$category_attr["attrMetas"]["isSkuCandidate"] . "," . (int)$category_attr["attrMetas"]["isUserDefined"] . "," . (int)$category_attr["attrMetas"]["isKey"] . "," . (int)$category_attr["attrMetas"]["isBound"] . ",'" . $category_attr["attrMetas"]["unit"] . "','" . $category_attr["attrMetas"]["valueType"] . "'," . (int)$category_attr["attrMetas"]["multi"] . "," . (int)$category_attr["composite"] . ",'')";
                            $rs = $zcy_data->execute_data_return_affected_rows($sql);
                            if (!($rs["isSuccess"])) {
                                exit(json_encode(array('resultMsg' => $rs["resultMsg"], "isSuccess" => false)));
                            }
                        } else {                    //更新表zcy_category_property属性行
                            $sql = "update `zcy_category_property` set `propertyGroup` = '" . $category_attr["group"] . "', `attrName` = '" . $category_attr["attrName"] . "', `attrVals` = '" . $attrVals . "', `isRequired` = " . (int)$category_attr["attrMetas"]["isRequired"] . ", `isImportant` = " . (int)$category_attr["attrMetas"]["isImportant"] . ", `isSukCandidate` = " . (int)$category_attr["attrMetas"]["isSukCandidate"] . ", `isSkuCandidate` = " . (int)$category_attr["attrMetas"]["isSkuCandidate"] . ", `isUserDefined` = " . (int)$category_attr["attrMetas"]["isUserDefined"] . ", `isKey` = " . (int)$category_attr["attrMetas"]["isKey"] . ", `isBound` = " . (int)$category_attr["attrMetas"]["isBound"] . ", `unit` = '" . $category_attr["attrMetas"]["unit"] . "', `valueType` = '" . $category_attr["attrMetas"]["valueType"] . "', `multi` = " . (int)$category_attr["attrMetas"]["multi"] . ", `composite` = " . (int)$category_attr["composite"] . " where `propertyId` = " . $category_attr["propertyId"];
                            $rs = $zcy_data->execute_data_return_affected_rows($sql);
                            if (!($rs["isSuccess"])) {
                                exit(json_encode(array('resultMsg' => $rs["resultMsg"], 'response_data' => '', "isSuccess" => false)));
                            }
                        }
                    }
                    $category_attrs_ids = implode(",", $category_attrs_ids);
                    $sql = "select `categoryId` from `zcy_category_attrs` where `categoryId` = $id";
                    $rs = $zcy_data->select_data($sql);
                    if (is_array($rs)) {
                        if (empty($rs)) {                //添加类目属性id列表
                            $sql = "insert into `zcy_category_attrs` (`categoryId`,`attrIds`) values($id,'" . $category_attrs_ids . "')";
//							echo $sql;
                            $rs = $zcy_data->execute_data_return_affected_rows($sql);
//							print_r($rs);
                            if ($rs["isSuccess"] and ($rs["affectedRows"] == 1)) {
                                exit(json_encode(array('resultMsg' => "添加成功", 'response_data' => '', "isSuccess" => true)));
                            } else {
                                exit(json_encode(array('resultMsg' => $rs["resultMsg"], 'response_data' => '', "isSuccess" => false)));
                            }
                        } else {                        //更新类目属性id列表
                            $sql = "update `zcy_category_attrs` set `attrIds` = '" . $category_attrs_ids . "' where `categoryId` = " . $id;
//							echo $sql;
                            $rs = $zcy_data->execute_data_return_affected_rows($sql);
//							print_r($rs);
                            if ($rs["isSuccess"]) {
                                exit(json_encode(array('resultMsg' => "更新成功", 'response_data' => '', "isSuccess" => true)));
                            } else {
                                exit(json_encode(array('resultMsg' => $rs["resultMsg"], 'response_data' => '', "isSuccess" => false)));
                            }
                        }
                    } else {
                        exit(json_encode(array('isSuccess' => false, 'response_data' => '', 'resultMsg' => $rs)));
                    }
                } else {
                    exit(json_encode(array("isSuccess" => false, 'response_data' => '', 'resultMsg' => $category_attrs["error_response"])));
                }
            }
        }
    }


    /**
     * 用户中心政采云-基本配置-获取政采云商品品牌列表
     * ajax更新所有商品品牌
     *
     */
    public function get_brandOp()
    {
        $data = file_get_contents("php://input");
        if (!is_null($data)) {
            $data = json_decode($data);
            if (is_object($data)) {
                $data = (array)$data;
            }
            $categoryId = $data["categoryId"];
            $pageSize = $data["pageSize"];
            $pageNo = $data["pageNo"];
            $beginModifiedDate = $data["beginModifiedDate"];
            $endModifiedDate = $data["endModifiedDate"];
            if ($pageSize == '') {
                $pagesize = 100;
            }
            if ($pageNo == '') {
                $pageNo = 1;
            }
            require_once(BASE_PATH . '/../zcy/nr_zcy.php');
            $zcy = new nr_zcy();
            $brands = $zcy->query_brand($categoryId, $pageSize, $pageNo, $beginModifiedDate, $endModifiedDate);
//            $new_brand['result'][0] = $brands["result"]['data'][0];
//            $new_brand['result'][1] = $brands["result"]['data'][1];
//            $brands['result']['data'] =$new_brand['result'];
//            echo "<pre>";
//            var_dump($brands['result']);die;
            if ($brands["success"]) {
                exit(json_encode(array('isSuccess' => true, 'response_data' => $brands["result"], 'resultMsg' => '')));
            } else {
                exit(json_encode(array('isSuccess' => false, 'response_data' => '', 'resultMsg' => 'code:' . $brands["code"] . '; message:' . $brands["message"])));
            }
        } else {
            exit(json_encode(array('resultMsg' => "参数错误", 'response_data' => '', "isSuccess" => false)));
        }
    }


    /**
     * 用户中心政采云-基本配置-添加或更新商品品牌
     * ajax更新所有商品品牌
     *
     */
    public function update_brandOp()
    {
        $data = file_get_contents("php://input");
        if (!is_null($data)) {
            $data = json_decode($data);
            if (is_object($data)) {
                $data = (array)$data;
            }
            $id = $data["id"];
            $fullName = $data["fullName"];
            $logo = $data["logo"];
            $status = $data["status"];
            $createdAt = $data["createdAt"]?$data["createdAt"]:0;
            $updatedAt = $data["updatedAt"]?$data["updatedAt"]:0;
            $auditStatus = $data["auditStatus"]?$data["auditStatus"]:0;
//            echo '<pre>';
//            exit($data);
//            exit(json_encode($data));
            if ($id != "" and $fullName != "" and $logo != "" and $status != "") {
                if (!@include(BASE_PATH . '/control/zcy_connect_data.php')) exit(json_encode(array('isSuccess' => false, 'resultMsg' => 'zcy_connect_data.php isn\'t exists!')));
                $zcy_data = new zcy_data();
                $sql = "select * from `zcy_brand` where `id` = " . $id;
                $rs = $zcy_data->select_data($sql);


                if (empty($rs)) {            //添加表zcy_brand品牌表
                    $fullName = "'".$data["fullName"]."'";
                    $sql = "insert into `zcy_brand` (`id`,`fullName`,`logo`,`status`,`createdAt`,`updatedAt`,`auditStatus`) values(".$id. ",". $fullName . ",'" . $logo . "','" . $status . "','" . $createdAt . "','" .$updatedAt. "','" . $auditStatus. "')";
                    $rs = $zcy_data->execute_data_return_affected_rows($sql);
                    if (!($rs["isSuccess"])) {
                        exit(json_encode(array('resultMsg' => $rs["resultMsg"], "isSuccess" => false)));
                    }else{
                        exit(json_encode(array('resultMsg' => '成功', "isSuccess" =>true)));
                    }
                } else {                    //更新表zcy_brand品牌表
                    $fullName = "".$data["fullName"]."";
                    $sql = "update `zcy_brand` set `fullName` = '" . $fullName . "', `logo` = '" . $logo . "', `status` = '" . $status . "', `createdAt` = " . $createdAt . ", `updatedAt` = " . $updatedAt . ", `auditStatus` = " . $auditStatus. " where `id` = " . $id;
                    $rs = $zcy_data->execute_data_return_affected_rows($sql);
//                    exit(json_encode($rs));
                    if (!($rs["isSuccess"])) {
                        exit(json_encode(array('resultMsg' => $rs["resultMsg"], 'response_data' => '', "isSuccess" => false)));
                    }else{
                        exit(json_encode(array('resultMsg' => '成功', "isSuccess" =>true)));
                    }
                }


            } else {
                exit(json_encode(array('resultMsg' => "参数错误", 'response_data' => '', "isSuccess" => false)));
            }
        } else {
            exit(json_encode(array('resultMsg' => "参数丢失", 'response_data' => '', "isSuccess" => false)));
        }
    }

}


?>