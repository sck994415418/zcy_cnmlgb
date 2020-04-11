<?php defined('InShopNC') or exit('Access Invalid!');?>
<?php if(!empty($output['goods_list']) && is_array($output['goods_list'])){?>

<ul class="goods-list">
  <?php foreach($output['goods_list'] as $key=>$val){?>
  <li>
    <div class="goods-thumb"> <a href="<?php echo urlShop('goods', 'index', array('goods_id' => $val['goods_id']));?>" target="_blank"><img src="<?php echo thumb($val, 240);?>"/></a></div>
    <dl class="goods-info">
      <dt><a href="<?php echo urlShop('goods', 'index', array('goods_id' => $val['goods_id']));?>" target="_blank"><?php echo $val['goods_name'];?></a> </dt>
      <dd>销售价格：<?php echo $lang['currency'].$val['goods_price'];?>
    </dl>
    <a nctype="btn_add_xianshi_goods" data-goods-id="<?php echo $val['goods_id'];?>" data-goods-name="<?php echo $val['goods_name'];?>" data-goods-img="<?php echo thumb($val, 240);?>" data-goods-price="<?php echo $val['goods_price'];?>" href="javascript:void(0);" class="ncsc-btn-mini">选择商品/修改租金价格</a> </li>
  <?php } ?>
</ul>
<div class="pagination"><?php echo $output['show_page']; ?></div>
<?php } else { ?>
<div><?php echo $lang['no_record'];?></div>
<?php } ?>
<div id="dialog_add_xianshi_goods" style="display:none;">
  <input id="dialog_goods_id" type="hidden">
  <input id="dialog_input_goods_price" type="hidden">
  <div class="selected-goods-info">
    <div class="goods-thumb"><img id="dialog_goods_img" src="" alt=""></div>
    <dl class="goods-info">
      <dt id="dialog_goods_name"></dt>
      <dd>销售价格：<?php echo $lang['currency']; ?><span id="dialog_goods_price"></span></dd>
      <dd>租&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;金：<input id="dialog_rent_price" type="text" class="text w70"><em class="add-on"><i class="icon-renminbi"></i></em>
      <dd>押&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;金：<input id="dialog_rent_pledge" type="text" class="text w70"><em class="add-on"><i class="icon-renminbi"></i></em>
      <dd>最短租期： <select name="rent_short_time" id="rent_short_time">
      	<option value="6">6个月</option>
      	<option value="12">12个月</option>
      	<option value="18">18个月</option>
      </select>
      </dd>
      <p id="dialog_add_rent_goods_error" style="display: none;">输入不能为空</p>
    </dl>
  </div>
  <div class="eject_con">
    <div class="bottom pt10 pb10"><a id="btn_submit" class="submit" href="javascript:void(0);">提交</a></div>
  </div>
</div>
