<?php

/**
 * 政府采购：分类增删改
 * 原则：两个政采分类中，包含2个相同商品，相当于同一个商品，只会在最后一个分类中，留下商品。
 *      多对多的映射较为复杂，
 * 结果：分类之间：商品无交叉关系。
 *      采用1对多的映射关系：政府采购网-->本商城分类。
 */
defined('InShopNC') or exit('Access Invalid!');

class zf_classControl extends SystemControl {

    public function __construct() {
        parent::__construct();
        Language::read('goods');
    }

    /**
     * 政采分类：列表页
     */
    public function indexOp() {
        //拓展功能搜索框
        //品目名称
        $class_name = trim($_GET['class_name']);
        $class_id = trim($_GET['class_id']);
        $goods_name = trim($_GET['goods_name']);
        $array = array();
         $array['table'] = "zf_class";
        if (!empty($class_name)) {
            $array['where'] = "class_name like '%{$class_name}%'";
        }
        if (!empty($class_id)) {
            $array['where'] = "class_id like '%{$class_id}%'";
        }
          if (!empty($goods_name)) { 
             $array['table'] = "zf_class,goods";
             $array['join_type'] = "left join";
             $array['join_on'][0] = "goods.zf_class_id=zf_class.id";
             $array['where'] = " goods_name like '%{$goods_name}%'";
             $array['group'] = "id";
        }
        //编码
        //关键功能

       
        $array['order'] = "class_type desc,goods_num desc,id asc";

        $page = new Page();
        $page->setEachNum(10);
        $page->setStyle('admin');
        $class_list = Db::select($array, $page);
         //print_r(Log::read());
        Tpl::output('page', $page->show());
        Tpl::output('class_list', $class_list);
        //var_dump($class_list);
        Tpl::showpage('zf_class.index');
    }

    /**
     * 添加页
     */
    public function addOp() {
        if (empty($_POST)) {
            Tpl::showpage('zf_class.add');
        } else {
            $class_id = $_POST['class_id'];
            $class_name = $_POST['class_name'];
            $type_array = array(0, 1);
            if (!in_array($class_type, $type_array)) {
                showMessage("参数错误", "", "html");
            }
            if ($parent_id === 0) {
                $deep = 1;
            }
            $add_time = time();
            $goods_num = 0;
            $sql = "INSERT INTO `zmkj_zf_class`(`class_id`, `class_name`, `goods_num`, `class_type`,`add_time`) VALUES ('{$class_id}','{$class_name}',0,{$class_type},{$add_time})";
            $rs = Model()->execute($sql);
            if ($rs) {
                showMessage("添加成功", "index.php?act=zf_class&op=index", "html");
            } else {
                showMessage("添加失败", "", "html");
            }
        }
    }

    /**
     * 编辑页
     */
    public function editOp() {
        if (empty($_POST)) {
            $id = $_GET['id'];
            $sql = "select * from zmkj_zf_class where id={$id}";
           // echo $sql;
            $class_info = Model()->query($sql);
            Tpl::output('class_info', $class_info);
         //   print_r($class_info);
            Tpl::showpage('zf_class.edit');
        } else {
            $id = $_POST['id'];
            $class_id = $_POST['class_id'];
            $class_name = $_POST['class_name'];
            $class_type = $_POST['class_type'];
            $type_array = array(0, 1);
            if (!in_array($class_type, $type_array)) {
                showMessage("参数错误", "", "html");
            }
            if ($parent_id === 0) {
                $deep = 1;
            }
            $edit_time = time();
            $sql = "Update `zmkj_zf_class` set `class_id`=\"{$class_id}\",`class_type`=\"{$class_type}\",`class_name`=\"{$class_name}\",`edit_time`={$edit_time} where `id` ={$id}";
            $rs = Model()->execute($sql);
            if ($rs) {
                showMessage("修改成功", "index.php?act=zf_class&op=index", "html");
            } else {
                showMessage("修改失败", "", "html");
            }
        }
    }

    /**
     * 删除和批量删除
     */
    public function delOp() {
        $id = $_GET['id'];
        if (!empty($id) && is_array($id)) {
            //批量删除
            $id_str = implode(',', $id);
            $sql = "select id from zmkj_zf_class where id in ({$id_str})";
            $rs1 = Model()->query($sql);
            if (count($rs1) === count($id)) {
                $sql = "delete from zmkj_zf_class where id in ({$id_str})";
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
            $sql = "select id from zmkj_zf_class where id={$id}";
            $rs1 = Model()->query($sql);
            if ($rs1) {
                $sql = "delete from zmkj_zf_class where id={$id}";
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

    /**
     * 绑定：商品列表
     */
    public function bind_goodsOp() {
        //print_r($_GET);
        $zf_class_id = $_GET['zf_class_id'];
        Tpl::output('zf_class_id', $zf_class_id);
        $model_goods = Model('goods');
        /**
         * 处理商品分类
         */
        $choose_gcid = ($t = intval($_REQUEST['choose_gcid'])) > 0 ? $t : 0;
        $gccache_arr = Model('goods_class')->getGoodsclassCache($choose_gcid, 3);
        Tpl::output('gc_json', json_encode($gccache_arr['showclass']));
        Tpl::output('gc_choose_json', json_encode($gccache_arr['choose_gcid']));
        /**
         * 查询条件
         */
        $where = array();
        //店铺
        $where['store_id'] = 51;
        $where['goods_state'] = 1;
        $where['goods_verify'] = 1;
        //是否绑定
        if (intval($_GET['search_state']) != 0) {   //"":按绑定的商品走，否则按 未绑定商品走。
            $where['zf_class_id'] = intval($_GET['zf_class_id']);
        } else {
            $where['zf_class_id'] = 0; //默认显示未绑定
        }
        //商品名字
        if ($_GET['search_goods_name'] != '') {
            $where['goods_name'] = array('like', '%' . trim($_GET['search_goods_name']) . '%');
        }
        //common_id查询商品
        if (intval($_GET['search_id']) > 0) {
            $where['goods_id'] = intval($_GET['search_id']);
        }

        //品牌
        if (intval($_GET['b_id']) > 0) {
            $where['brand_id'] = intval($_GET['b_id']);
        }
        //商品分类
        if ($choose_gcid > 0) {
            $where['gc_id_' . ($gccache_arr['showclass'][$choose_gcid]['depth'])] = $choose_gcid;
        }

        // 全部商品
        $goods_list = $model_goods->getGoodsList($where, "goods_id,goods_name,goods_image,goods_promotion_price,brand_id,zf_class_id,is_bind", '', '', '', 20);
        //print_r(Log::read());
//print_r($goods_list);
        foreach ($goods_list as $key => $value) {
            //品牌
            $sql = "select brand_name from zmkj_brand where brand_id=" . $value['brand_id'];
            $brand_info = Model()->query($sql);
            $goods_list[$key]['brand_name'] = $brand_info[0]['brand_name'];
        }
        Tpl::output('goods_list', $goods_list);
        Tpl::output('page', $model_goods->showpage(2));

        // 品牌
        $brand_list = Model('brand')->getBrandPassedList(array());

        Tpl::output('search', $_GET);
        Tpl::output('brand_list', $brand_list);

        Tpl::output('state', array('0' => '未绑定', '1' => '已绑定'));

        Tpl::output('ownShopIds', array_fill_keys(Model('store')->getOwnShopIds(), true));

        Tpl::showpage('bind_goods.index');
    }

    /**
     * 商品绑定，解绑商品。
     */
    public function ajax_bind_goodsOp() {
        $goods_id = intval($_GET['id']);
        $state = intval($_GET['state']);
        $zf_class_id = intval($_GET['zf_class_id']);
        $sql = "select goods_id,zf_class_id,is_bind from zmkj_goods where goods_id={$goods_id} and goods_state=1 and store_id=51";
        $rs = Model()->query($sql);
        if (empty($rs)) {
            exit(json_encode(array('msg' => "该商品不存在或者已经下架或者不是自营店铺的商品，请刷新后继续操作", 'code' => 0, 'data' => '')));
        }
        if ($rs[0]['is_bind'] == $state) {
            $is_bind = $state == 1 ? 0 : 1;
            //綁定
            if ($is_bind == 1) {
                $sql1 = "update zmkj_goods set zf_class_id={$zf_class_id},is_bind={$is_bind} where goods_id={$goods_id}";
            } else {  //解綁
                $sql1 = "update zmkj_goods set zf_class_id=0,is_bind={$is_bind} where goods_id={$goods_id}";
            }
            //echo $sql1;
            $rs1 = Model()->execute($sql1);
            if ($rs1) {
                $sql3 = "select count(goods_id) as num from zmkj_goods where zf_class_id={$zf_class_id}";
                //echo $sql3;
                $num = Model()->query($sql3);
                //print_r($num);
                // die;
                $sql4 = "Update zmkj_zf_class set goods_num=" . $num[0]['num'] . " where id={$zf_class_id}";
                // echo $sql4;
                $rs2 = Model()->execute($sql4);
                if ($rs2) {
                    exit(json_encode(array('msg' => "操作成功", 'code' => 1, 'data' => '')));
                } else {
                    exit(json_encode(array('msg' => "數量更新失敗", 'code' => 0, 'data' => '')));
                }
            } else {
                exit(json_encode(array('msg' => "操作失败", 'code' => 0, 'data' => '')));
            }
        } else {
            exit(json_encode(array('msg' => "参数错误，请刷新后，再次尝试", 'code' => 0, 'data' => '')));
        }
    }

}
