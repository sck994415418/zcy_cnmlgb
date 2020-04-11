<?php
/**
 * 微商城
 *
 *
 *
 **by 好商城V3 www.haoid.cn 运营版*/

defined('InShopNC') or exit('Access Invalid!');
class rentControl extends SystemControl{

    const MICROSHOP_CLASS_LIST = 'index.php?act=rent&op=index';
    const GOODS_FLAG = 1;
    const PERSONAL_FLAG = 2;
    const ALBUM_FLAG = 3;
    const STORE_FLAG = 4;

    private $links = array(
        array('url'=>'act=rent&op=rent_type','lang'=>'nc_manage'),
        array('url'=>'act=rent&op=type_add','lang'=>'nc_new'),
        // array('url'=>'act=rent&op=goods_class_export','lang'=>'goods_class_index_export'),
        // array('url'=>'act=rent&op=goods_class_import','lang'=>'goods_class_index_import'),
        // array('url'=>'act=rent&op=tag','lang'=>'goods_class_index_tag'),
    );

	public function __construct(){
		parent::__construct();
		Language::read('rent');
        Language::read('goods');
	}

    public function indexOp(){
        $this->rent_listsOp();
    }

    /**
     * 租赁列表
     */
    public function rent_listsOp() {
        $model_order = Model('rent_order');
        $condition  = array();
        if($_GET['rorder_sn']) {
            $condition['rorder_sn'] = $_GET['rorder_sn'];
        }
        if($_GET['store_name']) {
            $condition['store_name'] = $_GET['store_name'];
        }
        if(in_array($_GET['rorder_state'],array('0','10','20','30','40'))){
            $condition['rorder_state'] = $_GET['rorder_state'];
        }
        if($_GET['payment_code']) {
            $condition['payment_code'] = $_GET['payment_code'];
        }
        if($_GET['buyer_name']) {
            $condition['buyer_name'] = $_GET['buyer_name'];
        }
        $if_start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_time']);
        $if_end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_time']);
        $start_unixtime = $if_start_time ? strtotime($_GET['query_start_time']) : null;
        $end_unixtime = $if_end_time ? strtotime($_GET['query_end_time']): null;
        if ($start_unixtime || $end_unixtime) {
            $condition['add_time'] = array('time',array($start_unixtime,$end_unixtime));
        }
        $order_list = $model_order->table("rent_order")->where($condition)->limit(30)->select();

        foreach ($order_list as $key => $value) {
            $map['goods_id'] = $value['goods_id'];
            $goods = Model()->table("goods")->where($map)->find();
            $order_list[$key]['goods_name'] = $goods['goods_name'];
            $order_list[$key]['store_name'] = $goods['store_name'];
        }

        Tpl::output('order_list',$order_list);
        Tpl::output('show_page',$model_order->showpage());
        Tpl::showpage("rent_lists");
    }

    /**
     * 租赁信息详情
     */
    public function rent_detailOp(){
        $rorder_id = intval($_GET['rorder_id']);
        if($rorder_id <= 0 ){
            showMessage(L('miss_order_number'));
        }
        $model_order    = Model('rent_order');
        $model_goods    = Model('goods_rent');
        // $order_info = $model_order->getOrderInfo(array('order_id'=>$order_id),array('order_goods','order_common','store'));
        $order_info = $model_order->table("rent_order")->where(array('rorder_id'=>$rorder_id))->find();
        $map['goods_id'] = $order_info['goods_id'];
        $goods = Model()->table("goods")->where($map)->find();
        $order_info['goods_name'] = $goods['goods_name'];
        $order_info['store_name'] = $goods['store_name'];
        $order_info['payment_code'] = '线下支付';
        $goodsidarr = explode('|',$order_info['goods_id']);
        $goods_id = intval($goodsidarr[0]);
        $order_info['extend_order_goods'] = $model_goods->table("goods_rent")->where(array('goods_id'=>$goods_id))->select();
        $order_info['extend_order_goods'][0]['goods_num'] = $goodsidarr[1];

        Tpl::output('order_info',$order_info);
        Tpl::showpage("rent_detail");
    }

    /**
     * 租赁设备
     */
    public function equipOp() {
       $model_goods = Model ('goods_rent');
        /**
         * 处理商品分类
         */
        $choose_gcid = ($t = intval($_REQUEST['choose_gcid']))>0?$t:0;
        $gccache_arr = Model('rent_class')->getGoodsclassCache($choose_gcid,3);
        Tpl::output('gc_json',json_encode($gccache_arr['showclass']));
        Tpl::output('gc_choose_json',json_encode($gccache_arr['choose_gcid']));

        /**
         * 查询条件
         */
        $where = array();
        if ($_GET['search_goods_name'] != '') {
            $where['goods_name'] = array('like', '%' . trim($_GET['search_goods_name']) . '%');
        }
        if (intval($_GET['search_commonid']) > 0) {
            $where['goods_commonid'] = intval($_GET['search_commonid']);
        }
        if ($_GET['search_store_name'] != '') {
            $where['store_name'] = array('like', '%' . trim($_GET['search_store_name']) . '%');
        }
        if (intval($_GET['b_id']) > 0) {
            $where['brand_id'] = intval($_GET['b_id']);
        }
        if ($choose_gcid > 0){
            $where['gc_id_'.($gccache_arr['showclass'][$choose_gcid]['depth'])] = $choose_gcid;
        }
        if (in_array($_GET['search_state'], array('0','1','10'))) {
            $where['goods_state'] = $_GET['search_state'];
        }
        if (in_array($_GET['search_verify'], array('0','1','10'))) {
            $where['goods_verify'] = $_GET['search_verify'];
        }

        switch ($_GET['type']) {
            // 禁售
            case 'lockup':
                $goods_list = $model_goods->getGoodsCommonLockUpList($where);
                break;
            // 等待审核
            case 'waitverify':
                $goods_list = $model_goods->getGoodsCommonWaitVerifyList($where, '*', 10, 'goods_verify desc, goods_commonid desc');
                break;
            // 全部商品
            default:
                $goods_list = $model_goods->getGoodsCommonList($where);
                break;
        }

        Tpl::output('goods_list', $goods_list);
        Tpl::output('page', $model_goods->showpage(2));

        $storage_array = $model_goods->calculateStorage($goods_list);
        Tpl::output('storage_array', $storage_array);

        // 品牌
        $brand_list = Model('brand')->getBrandPassedList(array());

        Tpl::output('search', $_GET);
        Tpl::output('brand_list', $brand_list);

        Tpl::output('state', array('1' => '出售中', '0' => '仓库中', '10' => '违规下架'));

        Tpl::output('verify', array('1' => '通过', '0' => '未通过', '10' => '等待审核'));

        Tpl::output('ownShopIds', array_fill_keys(Model('store')->getOwnShopIds(), true));

        switch ($_GET['type']) {
            // 禁售
            case 'lockup':
                Tpl::showpage('rent.close');
                break;
            // 等待审核
            case 'waitverify':
                Tpl::showpage('rent.verify');
                break;
            // 全部商品
            default:
                Tpl::showpage('rent_equip');
                break;
        }
    }

     /**
     * 商品设置
     */
    public function rent_setOp() {
        $model_setting = Model('setting');
        if (chksubmit()){
            $update_array = array();
            $update_array['goods_verify'] = $_POST['goods_verify'];
            $result = $model_setting->updateSetting($update_array);
            if ($result === true){
                $this->log(L('nc_edit,nc_goods_set'),1);
                showMessage(L('nc_common_save_succ'));
            }else {
                $this->log(L('nc_edit,nc_goods_set'),0);
                showMessage(L('nc_common_save_fail'));
            }
        }
        $list_setting = $model_setting->getListSetting();
        Tpl::output('list_setting',$list_setting);
        Tpl::showpage('rent.setting');
    }

    /**
     * 租赁设备分类
     */
    public function rent_typeOp() {
        $lang   = Language::getLangContent();
        $model_class = Model('rent_class');
        if (chksubmit()){
            //删除
            if ($_POST['submit_type'] == 'del'){
                $gcids = implode(',', $_POST['check_gc_id']);
                if (!empty($_POST['check_gc_id'])){
                    if (!is_array($_POST['check_gc_id'])){
                        $this->log(L('nc_delete,goods_class_index_class').'[ID:'.$gcids.']',0);
                        showMessage($lang['nc_common_del_fail']);
                    }
                    $del_array = $model_class->delGoodsClassByGcIdString($gcids);
                    $this->log(L('nc_delete,goods_class_index_class').'[ID:'.$gcids.']',1);
                    showMessage($lang['nc_common_del_succ']);
                }else {
                    $this->log(L('nc_delete,goods_class_index_class').'[ID:'.$gcids.']',0);
                    showMessage($lang['nc_common_del_fail']);
                }
            }
        }
        //父ID
        $parent_id = $_GET['gc_parent_id']?intval($_GET['gc_parent_id']):0;

        //列表
        $tmp_list = $model_class->getTreeClassList(3);
        if (is_array($tmp_list)){
            foreach ($tmp_list as $k => $v){
                if ($v['gc_parent_id'] == $parent_id){
                    //判断是否有子类
                    if ($tmp_list[$k+1]['deep'] > $v['deep']){
                        $v['have_child'] = 1;
                    }
                    $class_list[] = $v;
                }
            }
        }
        if ($_GET['ajax'] == '1'){
            //转码
            if (strtoupper(CHARSET) == 'GBK'){
                $class_list = Language::getUTF8($class_list);
            }
            $output = json_encode($class_list);
            print_r($output);
            exit;
        }else {
            Tpl::output('class_list',$class_list);
            Tpl::output('top_link',$this->sublink($this->links,'rent_class'));
        }
        Tpl::showpage("rent_type");
    }

    public function type_addOp(){
        $lang   = Language::getLangContent();
        $model_class = Model('rent_class');
        if (chksubmit()){
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["gc_name"], "require"=>"true", "message"=>$lang['goods_class_add_name_null']),
                array("input"=>$_POST["gc_sort"], "require"=>"true", 'validator'=>'Number', "message"=>$lang['goods_class_add_sort_int']),
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $insert_array = array();
                $insert_array['gc_name']        = $_POST['gc_name'];
                $insert_array['type_id']        = intval($_POST['t_id']);
                $insert_array['type_name']      = trim($_POST['t_name']);
                $insert_array['gc_parent_id']   = intval($_POST['gc_parent_id']);
                $insert_array['commis_rate']    = intval($_POST['commis_rate']);
                $insert_array['gc_sort']        = intval($_POST['gc_sort']);
                $insert_array['gc_virtual']     = intval($_POST['gc_virtual']);
                $result = $model_class->addGoodsClass($insert_array);
                if ($result){
                    if ($insert_array['gc_parent_id'] == 0) {
                        if (!empty($_FILES['pic']['name'])) {//上传图片
                            $upload = new UploadFile();
                            $upload->set('default_dir',ATTACH_COMMON);
                            $upload->set('file_name','category-pic-'.$result.'.jpg');
                            $upload->upfile('pic');
                        }
                    }
                    $url = array(
                        array(
                            'url'=>'index.php?act=rent&op=type_add&gc_parent_id='.$_POST['gc_parent_id'],
                            'msg'=>$lang['goods_class_add_again'],
                        ),
                        array(
                            'url'=>'index.php?act=rent&op=rent_type',
                            'msg'=>$lang['goods_class_add_back_to_list'],
                        )
                    );
                    $this->log(L('nc_add,goods_class_index_class').'['.$_POST['gc_name'].']',1);
                    showMessage($lang['nc_common_save_succ'],$url);
                }else {
                    $this->log(L('nc_add,goods_class_index_class').'['.$_POST['gc_name'].']',0);
                    showMessage($lang['nc_common_save_fail']);
                }
            }
        }

        //父类列表，只取到第二级
        $parent_list = $model_class->getTreeClassList(2);
        $gc_list = array();
        if (is_array($parent_list)){
            foreach ($parent_list as $k => $v){
                $parent_list[$k]['gc_name'] = str_repeat("&nbsp;",$v['deep']*2).$v['gc_name'];
                if($v['deep'] == 1) $gc_list[$k] = $v;
            }
        }
        Tpl::output('gc_list', $gc_list);
        //类型列表
        $model_type = Model('type');
        $type_list  = $model_type->typeList(array('order'=>'type_sort asc'), '', 'type_id,type_name,class_id,class_name');
        $t_list = array();
        if(is_array($type_list) && !empty($type_list)){
            foreach($type_list as $k=>$val){
                $t_list[$val['class_id']]['type'][$k] = $val;
                $t_list[$val['class_id']]['name'] = $val['class_name']==''?L('nc_default'):$val['class_name'];
            }
        }
        ksort($t_list);

        Tpl::output('type_list',$t_list);
        Tpl::output('gc_parent_id',$_GET['gc_parent_id']);
        Tpl::output('parent_list',$parent_list);
        Tpl::output('top_link',$this->sublink($this->links,'goods_class_add'));
        Tpl::showpage('rent_type.add');
    }

    /**
     * 新增商品分类
     * @param array $insert
     * @return boolean
     */
    public function addGoodsClass($insert) {
        // 删除缓存
        $this->dropCache();
        return $this->insert($insert);
    }

    /**
     * 编辑
     */
    public function type_editOp(){
        $lang   = Language::getLangContent();
        $model_class = Model('rent_class');

        if (chksubmit()){
            // 更新分类信息
            $where = array('gc_id' => intval($_POST['gc_id']));
            $update_array = array();
            $update_array['gc_name']        = $_POST['gc_name'];
            $update_array['type_id']        = intval($_POST['t_id']);
            $update_array['type_name']      = trim($_POST['t_name']);
            $update_array['gc_sort']        = intval($_POST['gc_sort']);
            //好商城 v3-b10
            $update_array['gc_parent_id']   = intval($_POST['gc_parent_id']);
            $result = $model_class->editGoodsClass($update_array, $where);
            if (!$result){
                $this->log(L('nc_edit,goods_class_index_class').'['.$_POST['gc_name'].']',0);
                showMessage($lang['goods_class_batch_edit_fail']);
            }

            if (!empty($_FILES['pic']['name'])) {//上传图片
                $upload = new UploadFile();
                $upload->set('default_dir',ATTACH_COMMON);
                $upload->set('file_name','category-pic-'.intval($_POST['gc_id']).'.jpg');
                $upload->upfile('pic');
            }

            // 检测是否需要关联自己操作，统一查询子分类
            if ($_POST['t_commis_rate'] == '1' || $_POST['t_associated'] == '1' || $_POST['t_gc_virtual'] == '1') {
                $gc_id_list = $model_class->getChildClass($_POST['gc_id']);
                $gc_ids = array();
                if (is_array($gc_id_list) && !empty($gc_id_list)) {
                    foreach ($gc_id_list as $val){
                        $gc_ids[] = $val['gc_id'];
                    }
                }
            }

            // 更新该分类下子分类的所有分佣比例
            if ($_POST['t_commis_rate'] == '1' && !empty($gc_ids)){
                $model_class->editGoodsClass(array('commis_rate'=>$update_array['commis_rate']),array('gc_id'=>array('in',$gc_ids)));
            }

            // 更新该分类下子分类的所有类型
            if ($_POST['t_associated'] == '1' && !empty($gc_ids)){
                $where = array();
                $where['gc_id'] = array('in', $gc_ids);
                $update = array();
                $update['type_id'] = intval($_POST['t_id']);
                $update['type_name'] = trim($_POST['t_name']);
                $model_class->editGoodsClass($update, $where);
            }

            $url = array(
                array(
                    'url'=>'index.php?act=rent&op=type_edit&gc_id='.intval($_POST['gc_id']),
                    'msg'=>$lang['goods_class_batch_edit_again'],
                ),
                array(
                    'url'=>'index.php?act=rent&op=rent_type',
                    'msg'=>$lang['goods_class_add_back_to_list'],
                )
            );
            $this->log(L('nc_edit,goods_class_index_class').'['.$_POST['gc_name'].']',1);
            showMessage($lang['goods_class_batch_edit_ok'],$url,'html','succ',1,5000);
        }

        $map['gc_id'] = $_GET['gc_id'];
        $class_array = $model_class->table('rent_class')->where($map)->find();
        if (empty($class_array)){
            showMessage($lang['goods_class_batch_edit_paramerror']);
        }

        //类型列表
        $model_type = Model('type');
        $type_list  = $model_type->typeList(array('order'=>'type_sort asc'), '', 'type_id,type_name,class_id,class_name');
        $t_list = array();
        if(is_array($type_list) && !empty($type_list)){
            foreach($type_list as $k=>$val){
                $t_list[$val['class_id']]['type'][$k] = $val;
                $t_list[$val['class_id']]['name'] = $val['class_name']==''?L('nc_default'):$val['class_name'];
            }
        }
        ksort($t_list);
        //父类列表，只取到第二级
        $parent_list = $model_class->getTreeClassList(2);
        if (is_array($parent_list)){
            foreach ($parent_list as $k => $v){
                $parent_list[$k]['gc_name'] = str_repeat("&nbsp;",$v['deep']*2).$v['gc_name'];
            }
        }
        Tpl::output('parent_list',$parent_list);
        // 一级分类列表
        $gc_list = Model('rent_class')->table('rent_class')->where('gc_parent_id=0')->select();
        Tpl::output('gc_list', $gc_list);
        $pic_name = BASE_UPLOAD_PATH.'/'.ATTACH_COMMON.'/category-pic-'.$class_array['gc_id'].'.jpg';
        if (file_exists($pic_name)) {
            $class_array['pic'] = UPLOAD_SITE_URL.'/'.ATTACH_COMMON.'/category-pic-'.$class_array['gc_id'].'.jpg';
        }

        Tpl::output('type_list',$t_list);
        Tpl::output('class_array',$class_array);
        $this->links[] = array('url'=>'act=rent&op=type_edit','lang'=>'nc_edit');
        Tpl::output('top_link',$this->sublink($this->links,'type_edit'));
        Tpl::showpage('rent_type.edit');
    }

    /**
     * 删除
     */
    public function type_delOp(){
        $lang   = Language::getLangContent();
        $model_class = Model("rent_class");
        if (intval($_GET['gc_id']) > 0){
            //删除分类
            $model_class->delGoodsClassByGcIdString(intval($_GET['gc_id']));
            $this->log(L('nc_delete,goods_class_index_class') . '[ID:' . intval($_GET['gc_id']) . ']',1);
            showMessage($lang['nc_common_del_succ'],'index.php?act=rent&op=rent_type');
        }else {
            $this->log(L('nc_delete,goods_class_index_class') . '[ID:' . intval($_GET['gc_id']) . ']',0);
            showMessage($lang['nc_common_del_fail'],'index.php?act=rent&op=rent_type');
        }
    }

    /**
     * 违规下架
     */
    public function rent_lockupOp() {
        if (chksubmit()) {
            $commonids = $_POST['commonids'];
            $commonid_array = explode(',', $commonids);
            foreach ($commonid_array as $value) {
                if (!is_numeric($value)) {
                    showDialog(L('nc_common_op_fail'), 'reload');
                }
            }
            $update = array();
            $update['goods_stateremark'] = trim($_POST['close_reason']);

            $where = array();
            $where['goods_commonid'] = array('in', $commonid_array);

            Model('goods_rent')->editProducesLockUp($update, $where);
            showDialog(L('nc_common_op_succ'), 'reload', 'succ');
        }
        Tpl::output('commonids', $_GET['id']);
        Tpl::showpage('rent.close_remark', 'null_layout');
    }

    /**
     * 删除设备
     */
    public function rent_delOp() {
        $goods_id = intval($_GET['goods_id']);
        if ($goods_id <= 0) {
            showDialog(L('nc_common_op_fail'), 'reload');
        }
        Model('goods_rent')->delGoodsAll(array('goods_id' => $goods_id));
        showDialog(L('nc_common_op_succ'), 'reload', 'succ');
    }

    /**
     * 审核商品
     */
    public function rent_verifyOp(){
        if (chksubmit()) {
            $commonids = $_POST['commonids'];
            $commonid_array = explode(',', $commonids);
            foreach ($commonid_array as $value) {
                if (!is_numeric($value)) {
                    showDialog(L('nc_common_op_fail'), 'reload');
                }
            }
            $update2 = array();
            $update2['goods_verify'] = intval($_POST['verify_state']);

            $update1 = array();
            $update1['goods_verifyremark'] = trim($_POST['verify_reason']);
            $update1 = array_merge($update1, $update2);
            $where = array();
            $where['goods_commonid'] = array('in', $commonid_array);

            $model_goods = Model('goods_rent');
            if (intval($_POST['verify_state']) == 0) {
                $model_goods->editProducesVerifyFail($where, $update1, $update2);
            } else {
                $model_goods->editProduces($where, $update1, $update2);
            }
            showDialog(L('nc_common_op_succ'), 'reload', 'succ');
        }
        Tpl::output('commonids', $_GET['id']);
        Tpl::showpage('rent.verify_remark', 'null_layout');
    }

    /**
     * 编辑商品
     */
    public function rent_editOp(){
        if (chksubmit()) {
            $data = $_POST;
            unset($data['form_submit']);
            $gc = explode(',', $data['gc_id']);
            $data['gc_id'] = $gc[count($gc)-1];
            foreach ($gc as $k => $v) {
                $str = 'gc_id_'.($k+1);
                $data[$str] = $v;
            }
            if (!empty($_FILES['pic']['name'])) {//上传图片
                $arr = $this->image_upload($_FILES['pic']['tmp_name']);
                $data['goods_image'] = $arr['pic'];
            }
            $data['store_id'] = 1;
            $data['goods_state'] = 1;
            $data['is_own_shop'] = 1;
            $data['gc_id'] = $gc[count($gc)-1];
            $goods['goods_id'] = $data['goods_id'];
            $r = Model()->table('goods_rent')->where($goods)->update($data);
            showDialog(L('nc_common_op_succ'), 'reload', 'succ');
            exit();
        }
        $goods_id = intval($_GET['goods_id']);
        $where['goods_id'] = $goods_id;
        $goods_detail = Model('goods_rent')->table("goods_rent")->where($where)->find();
        //分类
        $type = Model('rent_class')->table("rent_class")->where('gc_parent_id=0')->select();
        foreach ($type as $k => $v) {
            $map['gc_parent_id'] = $v['gc_id'];
            $type[$k]['child'] = Model('rent_class')->table('rent_class')->where($map)->select();
            foreach ($type[$k]['child'] as $key => $va) {
                $maps['gc_parent_id'] = $va['gc_id'];
                $type[$k]['child'][$key]['child'] = Model('rent_class')->table('rent_class')->where($maps)->select();
            }
        }
        Tpl::output('goods_detail', $goods_detail);
        Tpl::output('goods_id', $goods_id);
        $brand_list = Model('brand')->table('brand')->select();
        Tpl::output('brand_list', $brand_list);
        Tpl::output('type', $type);
        Tpl::showpage('rent_edit');
    }

    /**
     * 添加商品
     */
    public function rent_addOp(){
        $lang   = Language::getLangContent();
        if (chksubmit()) {
            $arr = $this->image_upload($_FILES['pic']['tmp_name']);
            $data = $_POST;
            unset($data['form_submit']);
            unset($data['goods_id']);
            $gc = explode(',', $data['gc_id']);
            $data['gc_id'] = $gc[count($gc)-1];
            foreach ($gc as $k => $v) {
                $str = 'gc_id_'.($k+1);
                $data[$str] = $v;
            }
            $data['is_own_shop'] = 1;
            $data['store_id'] = 1;
            $data['goods_state'] = 1;
            $data['goods_image'] = $arr['pic'];
            $data['gc_id'] = $gc[count($gc)-1];
            $r = Model()->table('goods_rent')->insert($data);
            showDialog(L('nc_common_op_succ'), 'reload', 'succ');
            exit();
        }
        //分类
        $type = Model('rent_class')->table("rent_class")->where('gc_parent_id=0')->select();
        foreach ($type as $k => $v) {
            $map['gc_parent_id'] = $v['gc_id'];
            $type[$k]['child'] = Model('rent_class')->table('rent_class')->where($map)->select();
            foreach ($type[$k]['child'] as $key => $va) {
                $maps['gc_parent_id'] = $va['gc_id'];
                $type[$k]['child'][$key]['child'] = Model('rent_class')->table('rent_class')->where($maps)->select();
            }
        }
        Tpl::output('type', $type);
        // 品牌
        $brand_list = Model('brand')->table('brand')->select();
        Tpl::output('brand_list', $brand_list);
        Tpl::showpage('rent_edit');
    }

    /**
     * 上传图片
     */
    public function image_upload($file) {
        // 上传图片
        $upload = new UploadFile();
        $upload->set('default_dir', ATTACH_GOODS . DS . '1' . DS . $upload->getSysSetPath());
        $upload->set('max_size', C('image_max_filesize'));

        $upload->set('thumb_width', GOODS_IMAGES_WIDTH);
        $upload->set('thumb_height', GOODS_IMAGES_HEIGHT);
        $upload->set('thumb_ext', GOODS_IMAGES_EXT);
        $upload->set('fprefix', 1);
        $upload->set('allow_type', array('gif', 'jpg', 'jpeg', 'png'));
        $result = $upload->upfile('pic');
        if (!$result) {
            if (strtoupper(CHARSET) == 'GBK') {
                $upload->error = Language::getUTF8($upload->error);
            }
            $output = array();
            $output['error'] = $upload->error;
            return $output;
            exit();
        }

        $img_path = $upload->getSysSetPath() . $upload->file_name;

        // 取得图像大小
        list($width, $height, $type, $attr) = getimagesize(BASE_UPLOAD_PATH . '/' . ATTACH_GOODS . '/' . '1' . DS . $img_path);


        $data = array ();
        $data ['thumb_name'] = cthumb($upload->getSysSetPath() . $upload->thumb_image, 240, 1);
        $data ['pic']      = $img_path;

        return $data;
        exit();
    }

}

