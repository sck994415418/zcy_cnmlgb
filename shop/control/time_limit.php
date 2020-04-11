<?php

defined('InShopNC') or exit('Access Invalid!');

class time_limitControl extends BaseHomeControl {

    public function __construct() {
        parent::__construct();
        Tpl::output('index_sign', '春季大促');
    }

    public function indexOp() {
    	//限时折扣
        /* $model_xianshi_goods = Model('p_xianshi_goods');
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
		 */
		$page= new page();
		$eachNum =18;
		$page->setEachNum($eachNum);
		$count_res=model()->query("SELECT `xianshi_goods_id`,COUNT(`xianshi_id`) AS count FROM `zmkj_p_xianshi_goods` where state=1 and start_time < 1523581637 and end_time >1523581637 GROUP BY `goods_image` ORDER BY COUNT(`xianshi_id`) DESC");
		$page->setTotalNum(count($count_res));
		$startnum=$page->getLimitStart();
		$limit="{$startnum},{$eachNum}";
		$model_xianshi_goods = Model('p_xianshi_goods');
		$xianshi_item = $model_xianshi_goods->getXianshiGoodsCommendList(100);
		foreach($xianshi_item as $key=>$value){
				if(!empty($value['goods_id'])){
					$a=Model()->query("select goods_name,goods_price,vipprice,gjprice from zmkj_goods where goods_id=".$value['goods_id']);
					$xianshi_item[$key]['goods_name'] = $a[0]['goods_name'];
					if ($_SESSION['mshenfen_id'] == 2 && $a[0]['vipprice'] != '0.00'){
						$xianshi_item[$key]['goods_price'] = $a[0]['goods_price'];
						if($xianshi_item[$key]['xianshi_price'] >$a[0]['vipprice']){
							$xianshi_item[$key]['xianshi_price'] = $a[0]['vipprice'];
						}
						$xianshi_item[$key]['xianshi_discount'] = number_format($xianshi_item[$key]['xianshi_price'] / $a[0]['goods_price'] * 10, 1).'折';
					}elseif($_SESSION['mshenfen_id'] == 3 && $a[0]['gjprice'] != '0.00'){
						if($xianshi_item[$key]['xianshi_price'] >$a[0]['gjprice']){
							$xianshi_item[$key]['xianshi_price'] = $a[0]['gjprice'];
						}
						$xianshi_item[$key]['xianshi_discount'] = number_format($xianshi_item[$key]['xianshi_price'] / $a[0]['goods_price'] * 10, 1).'折';
					} 
				}
				$xianshi_item[$key]['goods_price'] = $a[0]['goods_price'];
		}
		
				
		
		Tpl::output('show_page', $page->show()); 
		Tpl::output('xianshi_item', $xianshi_item);
        Tpl::showpage('time_limit');
    }

}

?>