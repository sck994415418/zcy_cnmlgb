<?php
/**
 * 属性模型
 *
 * by 33hao 好商城V3  www.haoid.cn 开发
 */
defined('InShopNC') or exit('Access Invalid!');

class paramModel extends Model {
    const SHOW0 = 0;    // 不显示
    const SHOW1 = 1;    // 显示
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 属性列表
     * 
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getParamList($condition, $field = '*') {
        return $this->table('param')->where($condition)->field($field)->order('pr_sort asc')->select();
    }

    /**
     * 属性列表
     *
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getParamShowList($condition, $field = '*') {
        $condition['pr_show'] = self::SHOW1;
        return $this->getParamList($condition, $field);
    }
    
    /**
     * 属性值列表
     * 
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getParamValueList($condition, $field = '*') {
        return $this->table('param_value')->where($condition)->field($field)->order('pr_value_sort asc,pr_value_id asc')->select();
    }
    
    /**
     * 保存属性值
     * @param array $insert
     * @return boolean
     */
    public function addParamValueAll($insert) {
        return $this->table('param_value')->insertAll($insert);
    }
    
    /**
     * 保存属性值
     * @param array $insert
     * @return boolean
     */
    public function addParamValue($insert) {
        return $this->table('param_value')->insert($insert);
    }
    
    /**
     * 编辑属性值
     * @param array $update
     * @param array $condition
     * @return boolean
     */
    public function editParamValue($update, $condition) {
        return $this->table('param_value')->where($condition)->update($update);
    }
}