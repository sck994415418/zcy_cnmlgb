<?php

/**
 * 主题管理
 *
 * by 33hao 好商城V3  www.haoid.cn 开发
 */
defined('InShopNC') or exit('Access Invalid!');

class titleModel extends Model {

    public function __construct() {
        parent::__construct('title');
    }

    public function getTitleBrandList($condition, $field = '*') {
        return $this->table('title_brand')->field($field)->where($condition)->select();
    }

    public function getGoodsAttrIndexList($conditoin, $page = 0, $fields = '', $order = '', $limit = '') {
        return $this->table('goods_pr_index')->where($conditoin)->order($order)->limit($limit)->page($page)->select();
    }

    /**
     * 根据主题查找规格
     * 
     * @param   array   $where  条件
     * @param   string  $field  字段
     * @param   string  $order  排序
     * @return  array   返回二位数组
     */
    public function getSpecByTitle($where, $field, $order = 'spec.sp_sort asc, spec.sp_id asc') {
        $result = $this->table('title_spec,spec')->field($field)->where($where)->join('inner')->on('title_spec.sp_id = spec.sp_id')->order($order)->select();
        return $result;
    }

    /**
     * 根据主题获得规格、主题、参数信息
     * 
     * @param int $title_id 主题id
     * @param int $store_id 店铺id
     * @return array 二位数组
     */
    public function getAttr($title_id, $store_id, $gc_id) {
        $spec_list = $pr_list = $brand_list = array();
        if ($title_id > 0) {
            $spec_list = $this->titleRelatedJoinList(array('title_id' => $title_id), 'spec', 'spec.sp_id as sp_id, spec.sp_name as sp_name');
            $pr_list = $this->titleRelatedJoinList(array('param.title_id' => $title_id), 'pr', 'param.pr_id as pr_id, param.pr_name as pr_name, param_value.pr_value_id as pr_value_id, param_value.pr_value_name as pr_value_name');
            $brand_list = $this->titleRelatedJoinList(array('title_id' => $title_id), 'brand', 'brand.brand_id as brand_id,brand.brand_name as brand_name,brand.brand_initial as brand_initial');

            // 整理数组
            $spec_json = array();
            if (is_array($spec_list) && !empty($spec_list)) {
                $array = array();
                foreach ($spec_list as $val) {
                    $spec_value_list = Model('spec')->getSpecValueList(array('sp_id' => $val['sp_id'], 'gc_id' => $gc_id, 'store_id' => $store_id));
                    $a = array();
                    foreach ($spec_value_list as $v) {
                        $b = array();
                        $b['sp_value_id'] = $v['sp_value_id'];
                        $b['sp_value_name'] = $v['sp_value_name'];
                        $b['sp_value_color'] = $v['sp_value_color'];
                        $a[] = $b;
                        $spec_json[$val['sp_id']][$v['sp_value_id']]['sp_value_name'] = $v['sp_value_name'];
                        $spec_json[$val['sp_id']][$v['sp_value_id']]['sp_value_color'] = $v['sp_value_color'];
                    }
                    $array[$val['sp_id']]['sp_name'] = $val['sp_name'];
                    $array[$val['sp_id']]['sp_format'] = $val['sp_format'];
                    $array[$val['sp_id']]['value'] = $a;
                }
                $spec_list = $array;
            }
            if (is_array($pr_list) && !empty($pr_list)) {
                $array = array();
                foreach ($pr_list as $val) {
                    $a = array();
                    $a['pr_value_id'] = $val['pr_value_id'];
                    $a['pr_value_name'] = $val['pr_value_name'];

                    $array[$val['pr_id']]['pr_name'] = $val ['pr_name'];
                    $array[$val['pr_id']]['value'][] = $a;
                }
                $pr_list = $array;
            }
        }
        if (empty($brand_list)) {
            $brand_list = Model('brand')->getBrandPassedList(array(), '*', 0, 'brand_initial asc, brand_sort asc');
        }
        return array($spec_json, $spec_list, $pr_list, $brand_list);
    }

    /**
     * 新增商品商品与参数对应
     * 
     * @param int $goods_id
     * @param int $commonid
     * @param array $param
     * @return boolean
     */
    public function addGoodsTitle($goods_id, $commonid, $param) {
        // 商品与参数对应
        $sa_array = array();
        $sa_array['goods_id'] = $goods_id;
        $sa_array['goods_commonid'] = $commonid;
        $sa_array['gc_id'] = $param['cate_id'];
        $sa_array['title_id'] = $param['title_id'];
        if (is_array($param['pr'])) {
            $sa_array['value'] = $param['pr'];
            $this->titleGoodsRelatedAdd($sa_array, 'goods_pr_index');
            return true;
        } else {
            return false;
        }
    }

    public function delGoodsAttr($conditoin) {
        return $this->table('goods_pr_index')->where($conditoin)->delete();
    }

    /**
     * 主题列表
     * @param array  $param 
     * @param object $page  
     * @param string $field 
     */
    public function titleList($param, $page = '', $field = '*') {
        $condition_str = $this->getCondition($param);
        $array = array();
        $array['table'] = 'title';
        $array['where'] = $condition_str;
        $array['field'] = $field;
        $array['order'] = $param['order'];
        $list_title = Db::select($array, $page);
        return $list_title;
    }

    /**
     * 添加主题信息
     * @param string $table 表名
     * @param array $param 一维数组
     * @return bool
     */
    public function titleAdd($table, $param) {
        return Db::insert($table, $param);
    }

    /**
     * 添加对应关系信息
     * @param string $table 表名
     * @param array $param 一维数组
     * @param string $id
     * @param string $row 列名
     * @return bool
     */
    public function titleRelatedAdd($table, $param, $id, $row = '') {
        $insert_str = '';
        if (is_array($param)) {
            foreach ($param as $v) {
                $insert_str .= "('" . $id . "', '" . $v . "'),";
            }
        } else {
            $insert_str .= "('" . $id . "', '" . $param . "'),";
        }
        $insert_str = rtrim($insert_str, ',');
        return Db::query("insert into `" . DBPRE . $table . "` " . $row . " values " . $insert_str);
    }

    /**
     * 添加商品与规格、参数对应关系信息
     * 
     * @param array $param 一维数组
     * @param string $table 表名
     * @return bool
     */
    public function titleGoodsRelatedAdd($param, $table, $title = "") {
        if (is_array($param ['value']) && !empty($param ['value'])) {
            $insert_array = array();
            foreach ($param ['value'] as $key => $val) {
                if (is_array($val) && !empty($val)) {
                    foreach ($val as $k => $v) {
                        if (intval($k) > 0 && $k != 'name') {
                            $insert = array();
                            $insert['goods_id'] = $param ['goods_id'];
                            $insert['goods_commonid'] = $param ['goods_commonid'];
                            $insert['gc_id'] = $param ['gc_id'];
                            $insert['title_id'] = $param ['title_id'];
                            $insert['pr_id'] = $key;
                            $insert['pr_value_id'] = $k;
                            $insert_array[] = $insert;
                        }
                    }
                }
            }
            $this->table($table)->insertAll($insert_array);
        }
    }

    /**
     * 对应关系信息列表
     * @param string $table 表名
     * @param array $param 一维数组
     * @param string $id
     * @param string $row 列名
     * @return Array
     */
    public function titleRelatedList($table, $param, $field = '*') {
        $condition_str = $this->getCondition($param);
        $array = array();
        $array['table'] = $table;
        $array['where'] = $condition_str;
        $array['field'] = $field;
        $array['order'] = $param['order'];
        $list_title = Db::select($array);
        return $list_title;
    }

    /**
     * 计算商品主题与品牌对应表数量
     * @param array $condition
     * @return int
     */
    public function getTitleBrandCount($condition) {
        return $this->table('title_brand')->where($condition)->count();
    }

    /**
     * 更新参数信息
     * @param	array $update 更新数据
     * @param	array $param 条件
     * @param	string $table 表名
     * @return	bool
     */
    public function titleUpdate($update, $param, $table) {
        $condition_str = $this->getCondition($param);
        if (empty($update)) {
            return false;
        }
        if (is_array($update)) {
            $tmp = array();
            foreach ($update as $k => $v) {
                $tmp[$k] = $v;
            }
            $result = Db::update($table, $tmp, $condition_str);
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 主题与参数关联信息,多表查询
     *
     * @param array $param 条件
     * @param int $title 参数
     * @param string $field 字段
     * @param string $order 排序
     * @return boolean
     */
    public function titleRelatedJoinList($param, $title = '', $field = '*', $order = '') {
        $array = array();
        switch ($title) {
            case 'spec':
                $table = 'title_spec,spec';
                $join = 'inner';
                $on = 'title_spec.sp_id=spec.sp_id';
                $order = !empty($order) ? $order : 'spec.sp_id asc, spec.sp_sort asc';
                break;
            case 'pr':
                $table = 'param,param_value';
                $join = 'inner';
                $on = 'param.pr_id=param_value.pr_id';
                $order = !empty($order) ? $order : 'param.pr_sort asc, param_value.pr_value_sort asc, param_value.pr_value_id asc';
                break;
            case 'brand':
                $table = 'title_brand,brand';
                $join = 'inner';
                $on = 'title_brand.brand_id=brand.brand_id';
                $param['brand_apply'] = 1;  //只查询通过的品牌
                $order = !empty($order) ? $order : 'brand.brand_initial asc, brand.brand_sort asc';
                break;
        }
        $result = $this->table($table)->field($field)->join($join)->on($on)->where($param)->order($order)->select();
        return $result;
    }

    /**
     * 删除参数相关
     * 
     * @param string $table 表名 spec,spec_value
     * @param array $param 一维数组
     * @return bool
     */
    public function delTitle($table, $param) {
        $condition_str = $this->getCondition($param);
        return Db::delete($table, $condition_str);
    }

    /**
     * 将条件数组组合为SQL语句的条件部分
     * 
     * @param	array $condition_array
     * @return	string
     */
    private function getCondition($condition_array) {
        $condition_str = '';
        if ($condition_array['goods_id'] != '') {
            $condition_str .= " and goods_id ='" . $condition_array['goods_id'] . "'";
        }
        if ($condition_array['in_goods_id'] != '') {
            $condition_str .= " and goods_id in (" . $condition_array['in_goods_id'] . ")";
        }
        if ($condition_array['gc_id'] != '') {
            $condition_str .= " and gc_id ='" . $condition_array['gc_id'] . "'";
        }
        if ($condition_array['in_gc_id'] != '') {
            $condition_str .= " and gc_id in (" . $condition_array['in_gc_id'] . ")";
        }
        if ($condition_array['title_id'] != '') {
            $condition_str .= ' and title_id = "' . $condition_array['title_id'] . '"';
        }
        if ($condition_array['goods_class.title_id'] != '') {
            $condition_str .= ' and goods_class.title_id = "' . $condition_array['goods_class.title_id'] . '"';
        }
        if ($condition_array['in_title_id'] != '') {
            $condition_str .= ' and title_id in (' . $condition_array['in_title_id'] . ')';
        }
        if ($condition_array['in_sp_id'] != '') {
            $condition_str .= ' and sp_id in (' . $condition_array['in_sp_id'] . ')';
        }
        if ($condition_array['pr_id'] != '') {
            $condition_str .= ' and pr_id = "' . $condition_array['pr_id'] . '"';
        }
        if ($condition_array['in_pr_id'] != '') {
            $condition_str .= ' and pr_id in (' . $condition_array['in_pr_id'] . ')';
        }
        if ($condition_array['brand_id'] != '') {
            $condition_str .= " and brand_id = '" . $condition_array['brand_id'] . "'";
        }
        if ($condition_array['sp_value_id'] != '') {
            $condition_str .= " and sp_value_id = '" . $condition_array['sp_value_id'] . "'";
        }
        if ($condition_array['pr_value_id'] != '') {
            $condition_str .= " and pr_value_id = '" . $condition_array['pr_value_id'] . "'";
        }
        if ($condition_array['brand_apply'] != '') {
            $condition_str .= " and brand.brand_apply = '" . $condition_array['brand_apply'] . "'";
        }
        if ($condition_array['pr_show'] != '') {
            $condition_str .= " and pr_show = '" . $condition_array['pr_show'] . "'";
        }
        return $condition_str;
    }

}
