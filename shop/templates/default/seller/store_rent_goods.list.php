<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
  	<a id="btn_show_goods_select" class="ncsc-btn ncsc-btn-green" href="javascript:void(0);"><i></i>添加商品</a> </div>
</div>
<!--搜索框-->
<div id="div_goods_select" class="div-goods-select" style="display: none;">
    <table class="search-form">
      <tr><th class="w150"><strong>第一步：搜索店内商品</strong></th><td class="w160"><input id="search_goods_name" type="text w150" class="text" name="goods_name" value=""/></td>
        <td class="w70 tc"><a href="javascript:void(0);" id="btn_search_goods" class="ncsc-btn"/><i class="icon-search"></i><?php echo $lang['nc_search'];?></a></td><td class="w10"></td><td><p class="hint">不输入名称直接搜索将显示店内所有普通商品，特殊商品不能参加。</p></td>
      </tr>
    </table>
  <div id="div_goods_search_result" class="search-result"></div>
  <a id="btn_hide_goods_select" class="close" href="javascript:void(0);">X</a> 
</div>
<!--选择商品后的框-->
<div id="dialog_edit_xianshi_goods" class="eject_con" style="display: none;">
    <input id="dialog_xianshi_goods_id" type="hidden">
    <dl><dt>商品价格：</dt><dd><span id="dialog_edit_goods_price"></dd>
    </dl>
    <dl><dt>折扣价格：</dt><dd><input id="dialog_edit_xianshi_price" type="text" class="text w70"><em class="add-on"><i class="icon-renminbi"></i></em>
    <p id="dialog_edit_xianshi_goods_error" style="display:none;"><label for="dialog_edit_xianshi_goods_error" class="error"><i class='icon-exclamation-sign'></i>折扣价格不能为空，且必须小于商品价格</label></p>
    </dl>    
    <div class="eject_con">
        <div class="bottom pt10 pb10"><a id="btn_edit_xianshi_goods_submit" class="submit" href="javascript:void(0);">提交</a></div>
    </div>
</div>
 <!--好商城V3-B11-->
<form method="get" action="index.php">
  <table class="search-form">
    <input type="hidden" name="act" value="store_goods_online" />
    <input type="hidden" name="op" value="index" />
    <tr>
      <td>&nbsp;</td>
      <th>本店分类</th>
      <td class="w160"><select name="stc_id" class="w150">
          <option value="0"><?php echo $lang['nc_please_choose'];?></option>
          <?php if(is_array($output['store_goods_class']) && !empty($output['store_goods_class'])){?>
          <?php foreach ($output['store_goods_class'] as $val) {?>
          <option value="<?php echo $val['stc_id']; ?>" <?php if ($_GET['stc_id'] == $val['stc_id']){ echo 'selected=selected';}?>><?php echo $val['stc_name']; ?></option>
          <?php if (is_array($val['child']) && count($val['child'])>0){?>
          <?php foreach ($val['child'] as $child_val){?>
          <option value="<?php echo $child_val['stc_id']; ?>" <?php if ($_GET['stc_id'] == $child_val['stc_id']){ echo 'selected=selected';}?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $child_val['stc_name']; ?></option>
          <?php }?>
          <?php }?>
          <?php }?>
          <?php }?>
        </select></td>
      <th> <select name="search_type">
          <option value="0" <?php if ($_GET['type'] == 0) {?>selected="selected"<?php }?>>商品名称</option>
          <option value="1" <?php if ($_GET['type'] == 1) {?>selected="selected"<?php }?>>商家货号</option>
          <option value="2" <?php if ($_GET['type'] == 2) {?>selected="selected"<?php }?>>平台货号</option>
        </select>
      </th>
      <td class="w160"><input type="text" class="text w150" name="keyword" value="<?php echo $_GET['keyword']; ?>"/></td>
      <td class="tc w70"><label class="submit-border">
          <input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" />
        </label></td>
    </tr>
  </table>
</form>
<table class="ncsc-default-table">
  <thead>
    <tr nc_type="table_header">
      <th class="w30">&nbsp;</th>
      <th class="w50">&nbsp;</th>
      <th coltype="editable" column="goods_name" checker="check_required" inputwidth="230px">商品名称</th>
      <th class="w100">租金（月）</th>
      <th class="w100">押金</th>
      <th class="w100">最短租期</th>
      <th class="w100">发布时间</th>
      <th class="w120"><?php echo $lang['nc_handle'];?></th>
    </tr>
    <?php if (!empty($output['goods_list'])) { ?>
    	<!--开始-->
    <tr>
      <td class="tc"><input type="checkbox" id="all" class="checkall" onclick="ckAll()"/></td>
      <td colspan="20"><label for="all" ><?php echo $lang['nc_select_all'];?></label>
        <a href="javascript:void(0);" class="ncsc-btn-mini" nc_type="batchbutton" uri="<?php echo urlShop('store_rent_order', 'drop_goods');?>" name="goods_commonid" confirm="<?php echo $lang['nc_ensure_del'];?>"><i class="icon-trash"></i><?php echo $lang['nc_del'];?></a> 
        </td>
    </tr>
    <!--结束-->
    <?php } ?>
  </thead>
  <tbody>
    <?php if (!empty($output['goods_list'])) { ?>
    <?php foreach ($output['goods_list'] as $val) { ?>
    <tr>
      <th class="tc"><input type="checkbox" class="checkitem tc" onclick="is_p_Allselect()" name="checkitem" <?php if ($val['goods_lock'] == 1) {?>disabled="disabled"<?php }?> value="<?php echo $val['goods_commonid']; ?>"/></th>
      <th colspan="20">平台货号：<?php echo $val['goods_commonid'];?></th>
    </tr>
    
    <tr>
      <td class="trigger"><i class="tip icon-plus-sign" nctype="ajaxGoodsList" data-comminid="<?php echo $val['goods_commonid'];?>" title="点击展开查看此商品全部规格；规格值过多时请横向拖动区域内的滚动条进行浏览。"></i></td>
      <td><div class="pic-thumb"><a href="<?php echo urlShop('goods', 'index', array('goods_id' => $output['storage_array'][$val['goods_commonid']]['goods_id']));?>" target="_blank"><img src="<?php echo thumb($val, 60);?>"/></a></div></td>
      <td class="tl"><dl class="goods-name">
          <dt style="max-width: 450px !important;">
            <a href="<?php echo urlShop('goods', 'index', array('goods_id' => $output['storage_array'][$val['goods_commonid']]['goods_id']));?>" target="_blank"><?php echo $val['goods_name']; ?></a>
          </dt>
          <dd>商家货号 : <?php echo $val['goods_serial'];?></dd>
          <dd class="serve"> <span class="<?php if ($val['goods_commend'] == 1) { echo 'open';}?>" title="店铺推荐商品"><i class="commend">荐</i></span> <span class="<?php if ($val['mobile_body'] != '') { echo 'open';}?>" title="手机端商品详情"><i class="icon-tablet"></i></span> <span class="" title="商品页面二维码"><i class="icon-qrcode"></i>
            <div class="QRcode"><a target="_blank" href="<?php echo goodsQRCode(array('goods_id' => $output['storage_array'][$val['goods_commonid']]['goods_id'], 'store_id' => $_SESSION['store_id']));?>">下载标签</a>
              <p><img src="<?php echo goodsQRCode(array('goods_id' => $output['storage_array'][$val['goods_commonid']]['goods_id'], 'store_id' => $_SESSION['store_id']));?>"/></p>
            </div>
            </span>
            <?php if ($val['is_fcode'] ==1) {?>
            <span><a class="ncsc-btn-mini ncsc-btn-red" href="<?php echo urlShop('store_goods_online', 'download_f_code_excel', array('commonid' => $val['goods_commonid']));?>">下载F码</a></span>
            <?php }?>
          </dd>
        </dl></td>
      <td><span><?php echo $lang['currency'].$val['rent_money']; ?></span></td>
      <td><span><?php echo $lang['currency'].$val['cash_pledge']; ?></span></td>
      <td class="goods-time"><?php echo $val['rent_short_time'];?>个月</td>
      <td class="goods-time"><?php echo @date('Y-m-d',$val['rent_addtime']);?></td>
      <td class="nscs-table-handle">
        <span><a href="javascript:void(0);" id="edit_rent_goods" class="btn-blue"><i class="icon-edit"></i>
        <p><?php echo $lang['nc_edit'];?></p>
        </a></span>
        <span><a href="javascript:void(0);" onclick="ajax_get_confirm('<?php echo $lang['nc_ensure_del'];?>', '<?php echo urlShop('store_rent_order', 'drop_goods', array('goods_commonid' => $val['goods_commonid']));?>');" class="btn-red"><i class="icon-trash"></i>
        <p><?php echo $lang['nc_del'];?></p>
        </a></span>
        </td>
    </tr>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php } ?>
  </tbody>
  <tfoot>
    <?php  if (!empty($output['goods_list'])) { ?>
     <!-- <tr>
      <th class="tc"><input type="checkbox" id="all2" class="checkall"/></th>
      <th colspan="10"><label for="all2"><?php echo $lang['nc_select_all'];?></label>
        <a href="javascript:void(0);" nc_type="batchbutton" uri="<?php echo urlShop('store_goods_online', 'drop_goods');?>" name="commonid" confirm="<?php echo $lang['nc_ensure_del'];?>" class="ncsc-btn-mini"><i class="icon-trash"></i><?php echo $lang['nc_del'];?></a> <a href="javascript:void(0);" nc_type="batchbutton" uri="<?php echo urlShop('store_goods_online', 'goods_unshow');?>" name="commonid" class="ncsc-btn-mini"><i class="icon-level-down"></i><?php echo $lang['store_goods_index_unshow'];?></a> <a href="javascript:void(0);" class="ncsc-btn-mini" nctype="batch" data-param="{url:'<?php echo urlShop('store_goods_online', 'edit_jingle');?>', sign:'jingle'}"><i></i>设置广告词</a> <a href="javascript:void(0);" class="ncsc-btn-mini" nctype="batch" data-param="{url:'<?php echo urlShop('store_goods_online', 'edit_plate');?>', sign:'plate'}"><i></i>设置关联版式</a> </th>
    </tr> -->   
    <tr>
      <td colspan="20"><div class="pagination"> <?php echo $output['show_page']; ?> </div></td>
    </tr>
    <?php } ?>
  </tfoot>
</table>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js"></script> 
<script src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/store_goods_list.js"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/template.min.js" charset="utf-8"></script>
<script>
$(function(){
    //Ajax提示
    $('.tip').poshytip({
        className: 'tip-yellowsimple',
        showTimeout: 1,
        alignTo: 'target',
        alignX: 'center',
        alignY: 'top',
        offsetY: 5,
        allowTipHover: false
    });
    $('a[nctype="batch"]').click(function(){
        if($('.checkitem:checked').length == 0){    //没有选择
        	showDialog('请选择需要操作的记录');
            return false;
        }
        var _items = '';
        $('.checkitem:checked').each(function(){
            _items += $(this).val() + ',';
        });
        _items = _items.substr(0, (_items.length - 1));

        var data_str = '';
        eval('data_str = ' + $(this).attr('data-param'));

        if (data_str.sign == 'jingle') {
            ajax_form('ajax_jingle', '设置广告词', data_str.url + '&commonid=' + _items + '&inajax=1', '480');
        } else if (data_str.sign == 'plate') {
            ajax_form('ajax_plate', '设置关联版式', data_str.url + '&commonid=' + _items + '&inajax=1', '480');
        }
    });
    //现实商品搜索
    $('#btn_show_goods_select').on('click',function(){
    		$('#div_goods_select').show();
    })
    //隐藏商品搜索
    $('#btn_hide_goods_select').on('click', function() {
        $('#div_goods_select').hide();
    });
    //搜索商品
    $('#btn_search_goods').on('click', function() {
        var url = "<?php echo urlShop('store_rent_order', 'goods_select');?>";
        url += '&' + $.param({goods_name: $('#search_goods_name').val()});
        $('#div_goods_search_result').load(url);
    });
    $('#div_goods_search_result').on('click', 'a.demo', function() {
        $('#div_goods_search_result').load($(this).attr('href'));
        return false;
    });
    //添加限时折扣商品弹出窗口 
    $('#div_goods_search_result').on('click', '[nctype="btn_add_xianshi_goods"]', function() {
        $('#dialog_goods_id').val($(this).attr('data-goods-id'));
        $('#dialog_goods_name').text($(this).attr('data-goods-name'));
        $('#dialog_goods_price').text($(this).attr('data-goods-price'));
        $('#dialog_input_goods_price').val($(this).attr('data-goods-price'));
        $('#dialog_goods_img').attr('src', $(this).attr('data-goods-img'));
        $('#dialog_add_xianshi_goods').nc_show_dialog({width: 450, title: '添加商品'});
        $('#dialog_rent_price').val('');
        $('#dialog_rent_pledge').val('');
        $('#dialog_add_rent_goods_error').hide();
    });
    //添加限时折扣商品
    $('#div_goods_search_result').on('click', '#btn_submit', function() {
    		var goods_id = $('#dialog_goods_id').val();
        var goods_price = Number($('#dialog_input_goods_price').val());
        var rent_price = Number($('#dialog_rent_price').val());
        var rent_pledge = Number($('#dialog_rent_pledge').val());
        var rent_short_time = Number($('#rent_short_time').val());
        
        if(!isNaN(rent_price) && rent_price > 0 && !isNaN(rent_pledge) && rent_pledge > 0) {
            $.post('<?php echo urlShop('store_rent_order', 'rent_goods_add');?>', 
                {goods_id: goods_id, rent_price: rent_price, rent_pledge: rent_pledge, rent_short_time: rent_short_time},
                function(data) {
//              	alert(data);
                    if(data.result) {
                        $('#dialog_add_xianshi_goods').hide();
                        showSucc(data.message);
                    } else {
                        showError(data.message);
                    }
                }, 
            'json');
        } else {
            $('#dialog_add_rent_goods_error').show();
        }
    });
    $('#edit_rent_goods').on('click',function(){
    	alert(this.tbody.tr.length);false;
    	$('#edit_goods').show();
    })
});
</script>
