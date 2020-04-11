<?php

/**
 * 政府采购商品绑定
 *
 */
defined('InShopNC') or exit('Access Invalid!');

class goods_ormControl extends SystemControl {

    const EXPORT_SIZE = 5000;

    public function __construct() {
        parent::__construct();
    }

    /**
     * 列表页
     */
    public function indexOp() {
       
//        die;
        // $bindPage=empty($_GET['curpage'])? 0 : $_GET['curpage'];
      //   session('bindPage', $bindPage);
        $is_bind_type = empty($_GET['is_bind_type']) ? 1 : $_GET['is_bind_type'];
        //echo $_GET['is_bind_type'];
        TPL::output('is_bind_type', $is_bind_type);
        $array = array();
		
		$keyword = trim($_GET['keyword']);
		if ($rs=strpos($keyword,' ')) {
            $keyword = preg_replace("/(\s+)/","%", $keyword);
		}else{
            $keyword = preg_replace("/([0-9]+)/","%$1", $keyword);
        }
		if (trim($_GET['keyword']) != '') {
			$where ="productName like '%" . $keyword . "%'";
        }
		
		
        if ($is_bind_type == 1) { //我们主动绑定
            $array['table'] = "goods_orm";
            $array['order'] = "sort asc,id desc";
			if (!empty($where)){
				$array['where'] = $where;
			}
        } elseif ($is_bind_type == 2) { //我们被动绑定：别人绑定我们的商品，需要记录，一个一个去查。  
            $array['table'] = "goods_bind";
            $array['order'] = "id desc";
            $array['where'] = "is_show=1";
			if (!empty($where)){
				$array['where'] =  $array['where']." and goods_bind.goods_name like '%" . $keyword . "%'";
			}
			
        } elseif ($is_bind_type == 3) { //不确定显示
            //获取所有三方报价的产品。
            $sql1 = 'select goods_id from zmkj_goods';
            $sql1 .= ' left join zmkj_zf_class on zmkj_zf_class.id=zmkj_goods.zf_class_id';
            $sql1 .= ' where zmkj_zf_class.class_type=1';
            //echo $sql1;
            $all_goods = Model()->query($sql1); //无重复的goods_id
            //获取主动绑定的商品。
            $sql2 = 'select skuid from zmkj_goods_orm';
            //echo $sql2;
            $goods_orm = Model()->query($sql2); //有重复的goods_id
            //获取被动绑定的商品。
            $sql3 = 'select goods_id from zmkj_goods_bind where is_show=1';
            //echo $sql3;
            $goods_bind = Model()->query($sql3); //无重复goods_id
            //一维数组
            $all_goods1 = array();
            $goods_orm1 = array();
            $goods_bind1 = array();
            foreach ($all_goods as $v1) {
                $all_goods1[] = $v1['goods_id'];
            }
            foreach ($goods_orm as $v2) {
                $goods_orm1[] = $v2['skuid'];
            }
            foreach ($goods_bind as $v3) {
                $goods_bind1[] = $v3['goods_id'];
            }
            //过滤掉重复的数值。
            $all_goods2 = array_unique($all_goods1); //985个
            //echo count($all_goods2)."<br />";
            $goods_orm2 = array_unique($goods_orm1);
            //echo count($goods_orm2)."<br />";
            $goods_bind2 = array_unique($goods_bind1);
            //echo count($goods_bind2)."<br />";
            //差异化取舍
            $goods_id = array_diff($all_goods2, $goods_orm2, $goods_bind2);
            $goods_id_str = implode(',', $goods_id);
            //print_r($goods_id);
            //获取商品信息:mysqli的拼接规则
            $array['field'] = "goods.goods_id,goods.goods_name,goods_bind.store_num,goods_bind.product_id";
            $array['table'] = " goods,goods_bind";
            $array['join_type'] = " left join";
            $array['join_on'][0] = " goods.goods_id=goods_bind.goods_id";
            $array['where'] = " goods.goods_id in ({$goods_id_str})";
            $array['order'] = " goods.goods_id desc";
			if (!empty($where)){
				$array['where'] =  $array['where']." and goods.goods_name like '%" . $keyword . "%'";
			}
        } else {
            showMessage("参数错误", "", "html");
        }
        $page = new Page();
        $page->setEachNum(20);
        $page->setStyle('admin');
		
        $goods_orm_list = Db::select($array, $page);
        $bind_page=empty($_GET['curpage']) ? 1:$_GET['curpage'];
        //print_r($bind_page);
        Tpl::output('bind_page', $bind_page);
	    	Tpl::output('page', $page->show());
	    	Tpl::output('goods_orm_list', $goods_orm_list);
        Tpl::showpage('goods_orm.list');
    }

    /**
     * 第三方报价绑定 它物品 添加页
     */
    public function addOp() {
        if (empty($_POST)) {
            Tpl::showpage('goods_orm.add');
        } else {
            $data = $_POST;
            //本商城参数
            $goods_id = (int) $data['sku']; //商品id,skuid
            $productId = trim($data['productId']);

//            //检查是否已经绑定过
//            $sql = "select skuId from zmkj_goods_orm where skuId={$goods_id}";
////            print_r($sql);
////            die;
//            $goods_orm1 = Model()->query($sql);
//            if (!empty($goods_orm1)) {
//                showMessage("本商城商品id为" . $goods_id . "的商品已经绑定过，请删除后，重新绑定", "", "html");
//            }
            $sql = "select skuId from zmkj_goods_orm where productId='{$productId}'";
            $goods_orm2 = Model()->query($sql);
            if (!empty($goods_orm2)) {
                showMessage("政府商品id为" . $productId . "的商品已经绑定过，请删除后，重新绑定", "", "html");
            }
            //检查是否含有此商品
            $sql = "select goods_id,goods_name,goods_state,goods_promotion_price from zmkj_goods where goods_id ={$goods_id} and store_id=51";
            $goods_info = Model()->query($sql);
            if (empty($goods_info)) {
                showMessage("本店铺不存在此商品，请重新输入", "", "html");
            }
            if ($goods_info[0]['goods_state'] != 1) {
                showMessage("商品已下架或者违规，请重新输入", "", "html");
            }
            $productNameEC = trim($goods_info[0]['goods_name']);
            $productUrlEC = "http://www.nrwspt.com/shop/index.php?act=goods&op=index&goods_id=" . $goods_id;
            $add_time = time();
            $average_price = $data['average_price'];
            //政府参数

            $productName = trim($data['productName']);
            $productUrl = "http://www.hebzfcgwssc.com/Mall/HeBei/detail.aspx?product_id=" . $productId;
            if (!preg_match("/^[0-9]{6}$/", $goods_id)) {
                showMessage("goods_id的格式不正确", "", "html");
            }
            $sql1 = "Insert zmkj_goods_orm";
            $sql1 .= "(skuid,productId,productName,productUrl,productNameEC,productUrlEC,add_time,average_price)";
            $sql1 .= " values ({$goods_id},'{$productId}','{$productName}','{$productUrl}','{$productNameEC}','{$productUrlEC}',{$add_time},'{$average_price}')";
            $rs = Model()->execute($sql1);
            if ($rs) {
                showMessage("成功绑定商品", "http://www.nrwspt.com/admin/index.php?act=goods_orm&op=index", "html");
            } else {
                showMessage("数据入库失败，请重新上传", "", "html");
            }
        }
    }

    /**
     * 第三方报价绑定 它物品 编辑页
     */
    public function editOp() {
        if (!empty($_POST)) {
            $data = $_POST;
            //本商城参数
            $id = $data['id'];
            $goods_id = (int) $data['sku']; //商品id,skuid
            $productId = trim($data['productId']);

            //检查是否已经绑定过
//            $sql = "select goods_id from zmkj_goods_orm where skuId={$goods_id} and id !={$id}";
//            $goods_orm1 = Model()->query($sql);
//            if (!empty($goods_orm1)) {
//                showMessage("本商城商品id为" . $goods_id . "的商品已经绑定过，请删除后，重新绑定", "", "html");
//            }
            $sql = "select goods_id from zmkj_goods_orm where productId='{$productId} and id !={$id}'";
            $goods_orm2 = Model()->query($sql);
            if (!empty($goods_orm2)) {
                showMessage("政府商品id为" . $productId . "的商品已经绑定过，请删除后，重新绑定", "", "html");
            }
            //检查是否含有此商品
            $sql = "select goods_id,goods_name,goods_state,goods_promotion_price from zmkj_goods where goods_id ={$goods_id} and store_id=51";
            $goods_info = Model()->query($sql);
            if (empty($goods_info)) {
                showMessage("本店铺不存在此商品，请重新输入", "", "html");
            }
            if ($goods_info[0]['goods_state'] != 1) {
                showMessage("商品已下架或者违规，请重新输入", "", "html");
            }

            $productNameEC = trim($goods_info[0]['goods_name']);
            $productUrlEC = "http://www.nrwspt.com/shop/index.php?act=goods&op=index&goods_id=" . $goods_id;
            $edit_time = time();
            $average_price = $data['average_price'];
            //政府参数
            $productName = trim($data['productName']);
            $productUrl = "http://www.hebzfcgwssc.com/Mall/HeBei/detail.aspx?product_id=" . $productId;
            if (!preg_match("/^[0-9]{6}$/", $goods_id)) {
                showMessage("goods_id的格式不正确", "", "html");
            }
            //修改赋值
            $sql1 = "Update zmkj_goods_orm set";
            $sql1 .= " skuid={$goods_id},productId='{$productId}',productName='{$productName}'";
            $sql1 .= ",productUrl='{$productUrl}',productNameEC='{$productNameEC}',productUrlEC='{$productUrlEC}',edit_time={$edit_time},average_price='{$average_price}'";
            $sql1 .= " where id={$id}";
            //print_r($sql1);die;
            $rs = Model()->execute($sql1);
            if ($rs) {
                showMessage("成功修改商品绑定的参数", "http://www.nrwspt.com/admin/index.php?act=goods_orm&op=index", "html");
            } else {
                showMessage("数据入库失败，请重新上传", "", "html");
            }
        } else {
            $id = $_GET['id']; //主键
            $sql = "select * from zmkj_goods_orm where id={$id}";
            $goods_orm_info = Model()->query($sql);
            Tpl::output('goods_orm_info', $goods_orm_info[0]);
            Tpl::showpage('goods_orm.edit');
        }
    }

    /**
     * 第三方报价 其他人绑定我们自己的商品 查看页
     */
    public function show_dataOp() {
        //展示页面
        //print_r($_GET['is_bind_type']);        
        if (empty($_POST)) {    
            //产品id具有唯一性
            //print_r($_GET);die;
            $bind_page = $_GET['bind_page'];
            //print_r($bind_page);
            $is_bind_type = $_GET['is_bind_type'];
            $goods_id = $_GET['goods_id'];
            $sql = "select * from zmkj_goods_bind where goods_id={$goods_id}";
            $goods_bind_info = Model()->query($sql);
            //有数据
            if (!empty($goods_bind_info)) {
                Tpl::output('goods_bind_info', $goods_bind_info);
            } else {
             //没数据
                $sql1 = "select goods_id,goods_name from zmkj_goods where goods_id={$goods_id}";
                $goods_info = Model()->query($sql1);
                $sql2 = "insert into zmkj_goods_bind(goods_id,goods_name) values('{$goods_info[0]['goods_id']}','{$goods_info[0]['goods_name']}')";
                $rs = Model()->execute($sql2);
                if (empty($rs)) {
                    showMessage('数据库操作失败', '', 'html');
                }
            }
            Tpl::output('bind_page', $bind_page);
            Tpl::output('is_bind_type', $is_bind_type);
            Tpl::output('goods_id', $goods_id);
            Tpl::showpage('goods_bind_info');
        } else {  //修改提交
            //print_r($_POST);die;
			      $id = intval($_POST['id']);
			      $bind_page = $_POST['bind_page'];
            $product_id = trim($_POST['product_id']);
            $store_num = intval($_POST['store_num']);
            $is_bind_type = $_POST['is_bind_type'];
            if ($store_num < 0) {
                showMessage('参数错误', '', 'html');
            }
            $add_time = time();
            $sql3 = "update zmkj_goods_bind set product_id='{$product_id}',store_num='{$store_num}',add_time='{$add_time}' where goods_id={$id}";
            //echo $sql3;die;
            $rs = Model()->execute($sql3);
            if ($rs) {
                showMessage('操作成功', 'index.php?act=goods_orm&op=index&is_bind_type=' . $is_bind_type.'&curpage='.$bind_page.'#'.$id, 'html');
            } else {
                showMessage('操作失败', '', 'html');
            }
        }
    }

    /**
     * 确定显示 操作
     */
    public function ajax_showOp() {
        $goods_id = intval($_GET['goods_id']);
        if ($goods_id <= 0) {
            showMessage('参数错误', '', 'html');
        }
        $sql = 'select * from zmkj_goods_bind where goods_id={$goods_id}';
        $goods_info = Model()->query($sql);
        if ($goods_info[0]['is_show'] == 1) {
            exit(json_encode(array('state' => 0, 'msg' => '请刷新后，再次尝试')));
        }
        $sql1 = "Update zmkj_goods_bind set is_show = 1 where goods_id='{$goods_id}'";
        $rs1 = Model()->execute($sql1);
        if ($rs1) {
            exit(json_encode(array('state' => 1, 'msg' => '修改成功')));
        } else {
            exit(json_encode(array('state' => 0, 'msg' => '修改失败')));
        }
    }

    /**
     * 删除发生变化的商品 操作：当goods_id发生变化后，需要将goods_id删除，重新绑定。
     */
    public function del_goodsOp() {
        $goods_id = intval($_GET['goods_id']);
        if ($goods_id <= 0) {
            showMessage('参数错误', '', 'html');
        }

        $sql = "select * from zmkj_goods_bind where goods_id={$goods_id}";
        //echo $sql;
        $goods_info = Model()->query($sql);
        //print_r($goods_info);die;
        if (empty($goods_info)) {
            exit(json_encode(array('state' => 0, 'msg' => '请刷新后，再次尝试')));
        }
        $sql1 = "delete from zmkj_goods_bind where goods_id={$goods_id}";
        $rs1 = Model()->execute($sql1);
       
        if ($rs1) {
            exit(json_encode(array('state' => 1, 'msg' => '删除成功')));
        } else {
            exit(json_encode(array('state' => 0, 'msg' => '删除失败')));
        }
    }

    /**
     * 删除和批量删除：暂不开通
     */
    public function delOp() {
        $id = $_GET['id'];
        //print_r($id);
        //die;
        if (!empty($id) && is_array($id)) {
            //批量删除
            $id_str = implode(',', $id);
            $sql = "select id from zmkj_goods_orm where id in ({$id_str})";
            $rs1 = Model()->query($sql);
            if (count($rs1) === count($id)) {
                $sql = "delete from zmkj_goods_orm where id in ({$id_str})";
                $rs2 = Model()->execute($sql);
                if ($rs2) {
                    showMessage("删除成功", "", "html");
                } else {
                    showMessage("删除失败，请重新删除", "", "html");
                }
            } else {
                showMessage("该id不存在，请刷新后，重新删除", "", "html");
            }
        } else {
            //单个删除
            $sql = "select id from zmkj_goods_orm where id={$id}";
            $rs1 = Model()->query($sql);
            if ($rs1) {
                $sql = "delete from zmkj_goods_orm where id={$id}";
                $rs2 = Model()->execute($sql);
                if ($rs2) {
                    showMessage("删除成功", "", "html");
                } else {
                    showMessage("删除失败，请重新删除", "", "html");
                }
            } else {
                showMessage("该id不存在，请刷新后，请重新删除", "", "html");
            }
        }
    }

    //排序操作：暂不开通
    public function ajax_sort() {
        //接收数据
        $id = (int) $_GET['id'];
        $sort = (int) $_GET['sort'];
        if (!($id > 0)) {
            showMessage("参数错误，请重新输入", "", "html");
        }
        //检查是否含有此商品
        $sql = "select id from zmkj_goods_orm where goods_id ={$goods_id}";
        $goods_orm_info = Model()->query($sql);
        if (empty($goods_orm_info)) {
            showMessage("无此商品映射，请刷新后，继续编辑", "", "html");
        } else {
            $sql = "Update zmkj_goods_orm set sort={$sort} where goods_id ={$goods_id}";
            $rs = Model()->execute($sql);
            if ($rs) {
                showMessage("修改成功，继续编辑", "", "html");
            } else {
                showMessage("修改失败，请刷新后，继续编辑", "", "html");
            }
        }
    }
	public function downloadop(){
		 $is_bind_type = empty($_GET['is_bind_type']) ? 1 : $_GET['is_bind_type'];
        //echo $_GET['is_bind_type'];
        TPL::output('is_bind_type', $is_bind_type);
        $array = array();
		
		$keyword = trim($_GET['keyword']);
		if ($rs=strpos($keyword,' ')) {
            $keyword = preg_replace("/(\s+)/","%", $keyword);
		}else{
            $keyword = preg_replace("/([0-9]+)/","%$1", $keyword);
        }
		if (trim($_GET['keyword']) != '') {
			$where ="productName like '%" . $keyword . "%'";
        }
		
		
        if ($is_bind_type == 1) { //我们主动绑定
            $array['table'] = "goods_orm";
            $array['order'] = "sort asc,id desc";
			if (!empty($where)){
				$array['where'] = $where;
			}
        } elseif ($is_bind_type == 2) { //我们被动绑定：别人绑定我们的商品，需要记录，一个一个去查。  
            $array['table'] = "goods_bind";
            $array['order'] = "id desc";
            $array['where'] = "is_show=1";
			if (!empty($where)){
				$array['where'] =  $array['where']." and goods_bind.goods_name like '%" . $keyword . "%'";
			}
			
        } elseif ($is_bind_type == 3) { //不确定显示
            //获取所有三方报价的产品。
            $sql1 = 'select goods_id from zmkj_goods';
            $sql1 .= ' left join zmkj_zf_class on zmkj_zf_class.id=zmkj_goods.zf_class_id';
            $sql1 .= ' where zmkj_zf_class.class_type=1';
            //echo $sql1;
            $all_goods = Model()->query($sql1); //无重复的goods_id
            //获取主动绑定的商品。
            $sql2 = 'select skuid from zmkj_goods_orm';
            //echo $sql2;
            $goods_orm = Model()->query($sql2); //有重复的goods_id
            //获取被动绑定的商品。
            $sql3 = 'select goods_id from zmkj_goods_bind where is_show=1';
            //echo $sql3;
            $goods_bind = Model()->query($sql3); //无重复goods_id
            //一维数组
            $all_goods1 = array();
            $goods_orm1 = array();
            $goods_bind1 = array();
            foreach ($all_goods as $v1) {
                $all_goods1[] = $v1['goods_id'];
            }
            foreach ($goods_orm as $v2) {
                $goods_orm1[] = $v2['skuid'];
            }
            foreach ($goods_bind as $v3) {
                $goods_bind1[] = $v3['goods_id'];
            }
            //过滤掉重复的数值。
            $all_goods2 = array_unique($all_goods1); //985个
            //echo count($all_goods2)."<br />";
            $goods_orm2 = array_unique($goods_orm1);
            //echo count($goods_orm2)."<br />";
            $goods_bind2 = array_unique($goods_bind1);
            //echo count($goods_bind2)."<br />";
            //差异化取舍
            $goods_id = array_diff($all_goods2, $goods_orm2, $goods_bind2);
            $goods_id_str = implode(',', $goods_id);
            //print_r($goods_id);
            //获取商品信息:mysqli的拼接规则
            $array['field'] = "goods.goods_id,goods.goods_name,goods_bind.store_num,goods_bind.product_id";
            $array['table'] = " goods,goods_bind";
            $array['join_type'] = " left join";
            $array['join_on'][0] = " goods.goods_id=goods_bind.goods_id";
            $array['where'] = " goods.goods_id in ({$goods_id_str})";
            $array['order'] = " goods.goods_id desc";
			if (!empty($where)){
				$array['where'] =  $array['where']." and goods.goods_name like '%" . $keyword . "%'";
			}
        } else {
            showMessage("参数错误", "", "html");
        }
        $page = new Page();
        $page->setEachNum(20000);
        $page->setStyle('admin');
		
        $goods_orm_list = Db::select($array, $page);
		
		include_once("orm_to_excel.php");
		//echo 13456;
		
		
	}

}
