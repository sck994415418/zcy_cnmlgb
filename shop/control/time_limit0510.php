<?php

defined('InShopNC') or exit('Access Invalid!');

class time_limitControl extends BaseHomeControl {

    public function __construct() {
        parent::__construct();
        Tpl::output('index_sign', '春季大促');
    }

    public function indexOp() {
    	//限时折扣
        $model_xianshi_goods = Model('p_xianshi_goods');
        $xianshi_item = $model_xianshi_goods->getXianshiGoodsCommendList(100);
		foreach($xianshi_item as $key=>$value){
			if(!empty($xianshi_item1)&&is_array($xianshi_item1)){
			foreach($xianshi_item1 as $key1=>$value1){
				if($value['goods_image']==$value1['goods_image']){
					unset($xianshi_item[$key]);
				}
			}		
			}	
			$xianshi_item1[$key]=$value;		
		}
		Tpl::output('show_page', $model_xianshi_goods->showpage(5)); 
		
		Tpl::output('xianshi_item', $xianshi_item);
        Tpl::showpage('time_limit');
    }

}

?>