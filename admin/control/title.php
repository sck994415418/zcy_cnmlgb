<?php

/**
 * 主题管理
 * */
defined('InShopNC') or exit('Access Invalid!');

class titleControl extends SystemControl {

    const EXPORT_SIZE = 5000;

    public function __construct() {
        parent::__construct();
        Language::read('title');
    }

    /**
     * 主题管理
     */
    public function titleOp() {
        $model_title = Model('title');
        $page = new Page();
        $page->setEachNum(10);
        $page->setStyle('admin');
        $title_list = $model_title->titleList(array('order' => 'title_sort asc,title_id asc'), $page);
        Tpl::output('title_list', $title_list);
        Tpl::output('page', $page->show());
        Tpl::showpage('title.index');
    }

    /**
     * 添加主题
     */
    public function title_addOp() {
        $lang = Language::getLangContent();
        $model_title = Model('title');

        if (chksubmit()) {
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input" => $_POST["t_mane"], "require" => "true", "message" => $lang['title_add_name_no_null']),
                array("input" => $_POST["t_sort"], "require" => "true", 'validator' => 'Number', "message" => $lang['title_add_sort_no_null']),
            );
            $error = $obj_validate->validate();
            if ($error != '') {
                showMessage($error);
            }
            $title_array = array();
            $title_array['title_name'] = trim($_POST['t_mane']);
            $title_array['title_sort'] = trim($_POST['t_sort']);
            $title_array['class_id'] = $_POST['class_id'];
            $title_array['class_name'] = $_POST['class_name'];
            $title_id = $model_title->titleAdd('title', $title_array);

            if (!$title_id) {
                showMessage($lang['nc_common_save_fail']);
            }

            //添加主题参数
            if (!empty($_POST['at_value'])) {
                $param_array = $_POST['at_value'];
                foreach ($param_array as $v) {
                    if ($v['value'] != '') {
                        // 转码  防止GBK下用中文逗号截取不正确
                        $comma = '，';
                        if (strtoupper(CHARSET) == 'GBK') {
                            $comma = Language::getGBK($comma);
                        }
                        //参数值
                        //添加参数
                        $pr_array = array();
                        $pr_array['pr_name'] = $v['name']; //参数名
                        $pr_array['pr_value'] = $v['value']; //参数值
                        $pr_array['title_id'] = $title_id; //主题id
                        $pr_array['pr_sort'] = $v['sort']; //参数排序
                        $pr_array['pr_show'] = $v['show']; //参数：是否显示
                        //$pr_array['input_title'] = $v['input_title']; //参数：输入方式
                        $pr_id = $model_title->titleAdd('param', $pr_array);
                        if (!$pr_id) {
                            showMessage($lang['title_index_related_fail']);
                        }
                        //添加参数值
                        $pr_value = explode(',', $v['value']);
                        if (!empty($pr_value)) {
                            $pr_array = array();
                            foreach ($pr_value as $val) {
                                $tpl_array = array();
                                $tpl_array['pr_value_name'] = $val;
                                $tpl_array['pr_id'] = $pr_id;
                                $tpl_array['title_id'] = $title_id;
                                $tpl_array['pr_value_sort'] = 0;
                                $pr_array[] = $tpl_array;
                            }
                            $return = Model('param')->addParamValueAll($pr_array);
                            if (!$return) {
                                showMessage($lang['title_index_related_fail']);
                            }
                        }
                    } else {
                        // 转码  防止GBK下用中文逗号截取不正确
                        $comma = '，';
                        if (strtoupper(CHARSET) == 'GBK') {
                            $comma = Language::getGBK($comma);
                        }
                        //参数值
                        //添加参数
                        $pr_array = array();
                        $pr_array['pr_name'] = $v['name']; //参数名
                        $pr_array['pr_value'] = $v['value']; //参数值
                        $pr_array['title_id'] = $title_id; //主题id
                        $pr_array['pr_sort'] = $v['sort']; //参数排序
                        $pr_array['pr_show'] = $v['show']; //参数：是否显示
                        //$pr_array['input_title'] = $v['input_title']; //参数：输入方式
                        $pr_id = $model_title->titleAdd('param', $pr_array);
                        if (!$pr_id) {
                            showMessage($lang['title_index_related_fail']);
                        }
                        //添加参数值
                        $pr_array = array();
                        $tpl_array = array();
                        $tpl_array['pr_id'] = $pr_id;
                        $tpl_array['title_id'] = $title_id;
                        $tpl_array['pr_value_sort'] = 0;
                        $pr_array[] = $tpl_array;
                        $return = Model('param')->addParamValueAll($pr_array);
                        if (!$return) {
                            showMessage($lang['title_index_related_fail']);
                        }
                    }
                }
            }
            $url = array(
                array(
                    'url' => 'index.php?act=title&op=title_add',
                    'msg' => $lang['title_index_continue_to_dd']
                ),
                array(
                    'url' => 'index.php?act=title&op=title',
                    'msg' => $lang['title_index_return_title_list']
                )
            );
            $this->log(L('nc_add,title_index_title_name') . '[' . $_POST['t_mane'] . ']', 1);
            showMessage($lang['nc_common_save_succ'], $url);
        }
        // 一级分类列表
        $gc_list = Model('goods_class')->getGoodsClassListByParentId(0);
        Tpl::output('gc_list', $gc_list);

        Tpl::output('spec_list', $s_list);
        Tpl::output('brand_list', $b_list);
        Tpl::showpage('title.add');
    }

    /**
     * 编辑主题
     */
    public function title_editOp() {
        $lang = Language::getLangContent();
        if (empty($_GET['t_id'])) {
            showMessage($lang['param_error']);
        }

        //参数模型
        $model_title = Model('title');
        //编辑保存
        if (chksubmit()) {
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input" => $_POST["t_mane"], "require" => "true", "message" => $lang['title_add_name_no_null']),
                array("input" => $_POST["t_sort"], "require" => "true", 'validator' => 'Number', "message" => $lang['title_add_sort_no_null']),
            );
            $error = $obj_validate->validate();
            if ($error != '') {
                showMessage($error);
            }
            //参数
            $title_id = intval($_POST['t_id']);
            // 转码  防止GBK下用中文逗号截取不正确
            $comma = '，';
            if (strtoupper(CHARSET) == 'GBK') {
                $comma = Language::getGBK($comma);
            }
            if (is_array($_POST['at_value']) && !empty($_POST['at_value'])) {
                $param_array = $_POST['at_value'];
                foreach ($param_array as $v) {
                    // 要删除的参数id
                    $del_array = array();
                    if (!empty($_POST['a_del'])) {
                        $del_array = $_POST['a_del'];
                    }

                    $v['value'] = str_replace($comma, ',', $v['value']);      //把参数值中的中文逗号替换成英文逗号

                    if (isset($v['form_submit']) && $v['form_submit'] == 'ok' && !in_array($v['a_id'], $del_array)) {    //原参数已修改
                        /**
                         * 参数
                         */
                        $pr_array = array();
                        $pr_array['pr_name'] = $v['name'];
                        $pr_array['title_id'] = $title_id;
                        $pr_array['pr_sort'] = $v['sort'];
                        $pr_array['pr_show'] = $v['show'];
                        $return = $model_title->titleUpdate($pr_array, array('title_id' => $title_id, 'pr_id' => intval($v['a_id'])), 'param');
                        if (!$return) {
                            showMessage($lang['title_index_related_fail']);
                        }
                    } else if (!isset($v['form_submit'])) {         //新增参数
                        // 参数
                        $pr_array = array();
                        $pr_array['pr_name'] = $v['name'];
                        $pr_array['pr_value'] = $v['value'];
                        $pr_array['title_id'] = $title_id;
                        $pr_array['pr_sort'] = $v['sort'];
                        $pr_array['pr_show'] = $v['show'];
                        $pr_id = $model_title->titleAdd('param', $pr_array);
                        if (!$pr_id) {
                            showMessage($lang['title_index_related_fail']);
                        }

                        //添加参数值
                        $pr_value = explode(',', $v['value']);
                        if (!empty($pr_value)) {
                            $pr_array = array();
                            foreach ($pr_value as $val) {
                                $tpl_array = array();
                                $tpl_array['pr_value_name'] = $val;
                                $tpl_array['pr_id'] = $pr_id;
                                $tpl_array['title_id'] = $title_id;
                                $tpl_array['pr_value_sort'] = 0;
                                $pr_array[] = $tpl_array;
                            }
                            $return = Model('param')->addParamValueAll($pr_array);
                            if (!$return) {
                                showMessage($lang['title_index_related_fail']);
                            }
                        }
                    }
                }
                // 删除参数
                if (!empty($_POST['a_del'])) {
                    $del_id = '"' . implode('","', $_POST['a_del']) . '"';
                    //Model()->query("delete from zmkj_param_value where pr_id in ({$del_id});");
                    // Model()->query("delete from zmkj_param where pr_id in ({$del_id});");
                    $model_title->delTitle('param_value', array('in_pr_id' => $del_id)); //删除参数值
                    $model_title->delTitle('param', array('in_pr_id' => $del_id)); //删除参数
                }
            }
            //更新主题信息
            $title_array = array();
            $title_array['title_name'] = trim($_POST['t_mane']);
            $title_array['title_sort'] = trim($_POST['t_sort']);
            $title_array['class_id'] = $_POST['class_id'];
            $title_array['class_name'] = $_POST['class_name'];
            $return = $model_title->titleUpdate($title_array, array('title_id' => $title_id), 'title');
            if ($return) {
                $url = array(
                    array(
                        'url' => 'index.php?act=title&op=title',
                        'msg' => $lang['title_index_return_title_list']
                    )
                );
                $this->log(L('nc_edit,title_index_title_name') . '[' . $_POST['t_mane'] . ']', 1);
                showMessage($lang['nc_common_save_succ'], $url);
            } else {
                $this->log(L('nc_edit,title_index_title_name') . '[' . $_POST['t_mane'] . ']', 0);
                showMessage($lang['nc_common_save_fail']);
            }
        }
        //不提交执行，执行下列程序
        //参数列表
        $title_info = $model_title->titleList(array('title_id' => intval($_GET['t_id'])));
        if (!title_info) {
            showMessage($lang['param_error']);
        }
        Tpl::output('title_info', $title_info['0']);

        // 一级分类列表
        $gc_list = Model('goods_class')->getGoodsClassListByParentId(0);
        Tpl::output('gc_list', $gc_list);

        //参数
        $pr_list = $model_title->titleRelatedList('param', array('title_id' => intval($_GET['t_id']), 'order' => 'pr_sort asc'));
        Tpl::output('pr_list', $pr_list);

        Tpl::showpage('title.edit');
    }

    /**
     * 编辑参数
     */
    public function pr_editOp() {
        $lang = Language::getLangContent();
        $model = Model();
        if ($_POST['form_submit']) {
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input" => $_POST["pr_name"], "require" => "true", "message" => $lang['title_edit_title_pr_name_no_null']),
                array("input" => $_POST["pr_sort"], "require" => "true", 'validator' => 'Number', "message" => $lang['title_edit_title_pr_sort_no_null']),
            );
            $error = $obj_validate->validate();
            if ($error != '') {
                showMessage($error);
            } else {
                //更新参数值表
                $pr_value = $_POST['pr_value'];
                $pr_array = array();

                // 要删除的规格值id
                $del_array = array();
                if (!empty($_POST['pr_del'])) {
                    $del_array = $_POST['pr_del'];
                }
                $model_param = Model('param');
                if (!empty($pr_value) && is_array($pr_value)) {
                    foreach ($pr_value as $key => $val) {
                        if (isset($val['form_submit']) && $val['form_submit'] == 'ok' && !in_array(intval($key), $del_array)) {  // 参数已修改
                            $where = array();
                            $where['pr_value_id'] = intval($key);
                            $update = array();
                            $update['pr_value_name'] = $val['name'];
                            $update['pr_value_sort'] = intval($val['sort']);

                            $model_param->editParamValue($update, $where);

                            $pr_array[] = $val['name'];
                        } else if (isset($val['form_submit']) && $val['form_submit'] == '' && !in_array(intval($key), $del_array)) { // 参数未修改
                            $pr_array[] = $val['name'];
                        } else if (!isset($val['form_submit'])) {

                            $insert = array();
                            $insert['pr_value_name'] = $val['name'];
                            $insert['pr_id'] = intval($_POST['pr_id']);
                            $insert['title_id'] = intval($_POST['title_id']);
                            $insert['pr_value_sort'] = intval($val['sort']);

                            $model_param->addParamValue($insert);

                            $pr_array[] = $val['name'];
                        }
                    }
                    // 删除参数值
                    $model->table('param_value')->delete(implode(',', $del_array));
                }

                /**
                 * 更新参数
                 */
                $data = array();
                $data['pr_id'] = intval($_POST['pr_id']);
                $data['pr_name'] = $_POST['pr_name'];
                $data['pr_value'] = implode(',', $pr_array);
                $data['pr_show'] = intval($_POST['pr_show']);
                $data['pr_sort'] = intval($_POST['pr_sort']);
                $return = $model->table('param')->update($data);

                if ($return) {
                    $this->log(L('title_edit_title_pr_edit') . '[' . $_POST['pr_name'] . ']', 1);
                    showMessage($lang['title_edit_title_pr_edit_succ'], 'index.php?act=title&op=title');
                } else {
                    $this->log(L('title_edit_title_pr_edit') . '[' . $_POST['pr_name'] . ']', 0);
                    showMessage($lang['title_edit_title_pr_edit_fail'], '', '', 'error');
                }
            }
        }

        $pr_id = intval($_GET['pr_id']);
        if ($pr_id == 0) {
            showMessage($lang['param_error']);
        }
        $pr_info = $model->table('param')->where('pr_id=' . $pr_id)->find();
        Tpl::output('pr_info', $pr_info);

        $pr_value_list = $model->table('param_value')->where('pr_id=' . $pr_id)->order('pr_value_sort asc, pr_value_id asc')->select();
        Tpl::output('pr_value_list', $pr_value_list);

        Tpl::showpage('title_param.edit');
    }

    /**
     * 删除主题
     * :先删除和主题相关的东西，最后删除主题。
     */
    public function title_delOp() {
        $lang = Language::getLangContent();
        if (empty($_GET['del_id'])) {
            showMessage($lang['param_error']);
        }
        //参数模型
        $model_title = Model('title');
        if (is_array($_GET['del_id'])) {
            $id = "'" . implode("','", $_GET['del_id']) . "'";
        } else {
            $id = intval($_GET['del_id']);
        }
        //参数列表
        $title_list = $model_title->titleList(array('in_title_id' => $id));
        if (is_array($title_list) && !empty($title_list)) {
            //删除参数值表
            $pr_list = $model_title->titleRelatedList('param', array('in_title_id' => $id), 'pr_id');
            if (is_array($pr_list) && !empty($pr_list)) {
                $prs_id = '';
                foreach ($pr_list as $val) {
                    $prs_id .= '"' . $val['pr_id'] . '",';
                }
                $prs_id = trim($prs_id, ',');
                $return1 = $model_title->delTitle('param_value', array('in_pr_id' => $prs_id)); //删除参数值
                $return2 = $model_title->delTitle('param', array('in_pr_id' => $prs_id));  //删除参数
                if (!$return1 || !$return2) {
                    showMessage($lang['title_index_del_related_pr_fail']);
                }
            }
            //删除主题
            $return = $model_title->delTitle('title', array('in_title_id' => $id));
            if (!$return) {
                showMessage($lang['title_index_del_fail']);
            }

            $this->log(L('nc_delete,title_index_title_name') . '[ID:' . $id . ']', 1);
            showMessage($lang['title_index_del_succ']);
        } else {
            $this->log(L('nc_delete,title_index_title_name') . '[ID:' . $id . ']', 0);
            showMessage($lang['param_error']);
        }
    }

    /**
     * ajax操作
     */
    public function ajaxOp() {
        $model_title = Model('title');
        switch ($_GET['branch']) {
            case 'sort':
//			case 'name':
                $return = $model_title->titleUpdate(
                        array($_GET['column'] => trim($_GET['value'])), array('title_id' => intval($_GET['id'])), 'title'
                );
                if ($return) {
                    $this->log(L('title_index_title_name,nc_sort') . '[ID:' . intval($_GET['id']) . ']', 1);
                    echo 'true';
                    exit;
                } else {
                    echo 'false';
                    exit;
                }
                break;
        }
    }

    /**
     * 主题导出
     */
    public function export_step1Op() {
        $model_title = Model('title');
        $page = new Page();
        $page->setEachNum(self::EXPORT_SIZE);
        $title_list = $model_title->titleList(array('order' => 'title_sort asc'), $page);
        if (!is_numeric($_GET['curpage'])) {
            $count = $page->getTotalNum();
            $array = array();
            if ($count > self::EXPORT_SIZE) { //显示下载链接
                $page = ceil($count / self::EXPORT_SIZE);
                for ($i = 1; $i <= $page; $i++) {
                    $limit1 = ($i - 1) * self::EXPORT_SIZE + 1;
                    $limit2 = $i * self::EXPORT_SIZE > $count ? $count : $i * self::EXPORT_SIZE;
                    $array[$i] = $limit1 . ' ~ ' . $limit2;
                }
                Tpl::output('list', $array);
                Tpl::output('murl', 'index.php?act=title&op=title');
                Tpl::showpage('export.excel');
            } else { //如果数量小，直接下载
                $this->createExcel($title_list);
            }
        } else { //下载
            $this->createExcel($title_list);
        }
    }

    /**
     * 生成excel
     *
     * @param array $data
     */
    private function createExcel($data = array()) {
        Language::read('export');
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
        //header
        $excel_data[0][] = array('styleid' => 's_title', 'data' => L('exp_title_name'));

        foreach ((array) $data as $k => $v) {
            $tmp = array();
            $tmp[] = array('data' => $v['title_name']);
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset(L('exp_title_name'), CHARSET));
        $excel_obj->generateXML($excel_obj->charset(L('exp_title_name'), CHARSET) . $_GET['curpage'] . '-' . date('Y-m-d-H', time()));
    }

}
