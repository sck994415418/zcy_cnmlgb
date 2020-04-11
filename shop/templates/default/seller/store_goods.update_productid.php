<?php defined('InShopNC') or exit('Access Invalid!');?>
<?php
$zf_url = new zf_url();
?>
<style type="text/css">
	tr.goodslist:hover td{ background-color:#AFF;}
	input.pricetxt{border:dotted 1px #00FF00;}
	a.olink:visited{color:#5F20F0;}
	.radios{height:30px;width:150px;line-height:30px;display:inline-block;}
	.goods_num{width:40px;display:inline-block;}
	.goods_id{width:30px;display:inline-block;}
	.goods_name{width:420px;display:inline-block;word-wrap:break-word;word-break:break-all;margin-right:15px;}
	.goods_list{ border-bottom:dotted 1px;padding:5px;}
</style>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<div class="alert mt15 mb5"><strong>操作提示：</strong>
	<ul>
      <li><font color="#FF0000">注：此功能需关闭浏览器跨域访问限制才能正常使用</font></li>
      <li>未录入商品：已绑定河北省政府采购网上商城分类，在改价模块显示未录入</li>
      <li>商品ID错误：已绑定河北省政府采购网上商城分类，在改价模块显示商品链接不存在</li>
      <li>自定义：</li>
	</ul>
</div>
<form method="get" action="index.php" id="seachform" style="padding:20px 0px 0px 0px;">
    <input type="hidden" name="act" value="store_goods_change_price" />
    <input type="hidden" name="op" value="index" />
    <input type="hidden" name="type" value="update_productid" />
<?php
	if($_GET["update_type"]!=""){
		$update_type = trim($_GET["update_type"]);
	}else{
		$update_type = "noin";
	}
?>
	<p align="center" style="padding:20px;">
    	<span class="radios"><label><input type="radio" name="update_type" value="noin"<?php if($update_type=="noin") echo " checked=\"checked\"" ?> />未录入商品ID</label></span>
        <span class="radios"><label><input type="radio" name="update_type" value="in"<?php if($update_type=="in") echo " checked=\"checked\"" ?> />商品ID错误</label></span>
        <span class="radios"><label><input type="radio" name="update_type" value="zdy"<?php if($update_type=="zdy") echo " checked=\"checked\"" ?> />自定义录入</label></span>
    </p>
    <p style="padding:0px 50px 20px 0px;float:right;">
    	<input type="button" id="start" name="start" class="submit" value="自动更新全部ID" />
    </p>
    <br style="clear:both;" />
<?php
if($update_type == "zdy"){
		?>
  <table class="search-form">
    <tr>
      <td>&nbsp;</td>
      <td class="w100"><?php echo '政府采购网分类';?></td>
      <td class="w160">
<?php
     $sql="select `id`,`class_name`,`class_type` from `zmkj_zf_class`";
	 $zf_class = $zf_url->select_data($sql);
?>
        <select name="zf_class" class="w150">
          <option value="0"><?php echo $lang['nc_please_choose'];?></option>
          <?php foreach($zf_class as $key=>$val){?><option value="<?php echo $val['id']; ?>" <?php if ($_GET['zf_class'] == $val['id']){ echo 'selected=selected';}?>><?php echo $val['class_name']; ?></option>
          <?php }?>
        </select>
      </td>
      <td class="w120"><label><input type="checkbox" <?php if ($_GET['zf_class'] != 0 or $_GET['is_bind'] == 'true') echo "checked=\"checked\" ";?>class="checkbox" name="is_bind" value="true"/>已绑定政府分类</label></td>
      <th>
        <select name="search_type">
          <option value="0" <?php if (intval($_GET['search_type']) == 0) {?>selected="selected"<?php }?>><?php echo $lang['store_goods_index_goods_name'];?></option>
          <option value="1" <?php if (intval($_GET['search_type']) == 1) {?>selected="selected"<?php }?>><?php echo "商品ID";?></option>
          <option value="2" <?php if (intval($_GET['search_type']) == 2) {?>selected="selected"<?php }?>>平台货号</option>
        </select>
      </th>
      <td class="w160"><input type="text" class="text" name="keyword" value="<?php echo $_GET['keyword']; ?>"/></td>
      <td class="w120" align="center">&nbsp;&nbsp;
      	<select name="order" class="w100">
      		<option value="1" <?php if (intval($_GET['order']) == 1) {?>selected="selected"<?php }?>>商品ID降序</option>
            <option value="2" <?php if (intval($_GET['order']) == 2) {?>selected="selected"<?php }?>>商品ID升序</option>
      		<option value="3" <?php if (intval($_GET['order']) == 3) {?>selected="selected"<?php }?>>平台货号降序</option>
            <option value="4" <?php if (intval($_GET['order']) == 4) {?>selected="selected"<?php }?>>平台货号升序</option>
            <option value="5" <?php if (intval($_GET['order']) == 5) {?>selected="selected"<?php }?>>修改时间降序</option>
            <option value="6" <?php if (intval($_GET['order']) == 6) {?>selected="selected"<?php }?>>修改时间升序</option>
        </select>
      </td>
      <td class="tc w70"><label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" /></label></td>
    </tr>
  </table>
</form>
<table class="ncsc-default-table">
  <thead>
    <tr nc_type="table_header">
      <th class="w50">操作</th>
      <th class="w80">商品品目</th>
      <th><?php echo $lang['store_goods_index_goods_name'];?></th>
      <th class="w50"><?php //echo $lang['store_goods_index_show'];?>上下架</th>
      <th class="w80"><?php echo $lang['store_goods_index_price'];?></th>
      <th class="w70">政府采购网链接</th>
      <th class="w80">上架时间</th>
      <th class="w100"><?php //echo $lang['nc_handle'];?>最后修改时间</th>
    </tr>
  </thead>
  <tbody>
  	<?php
        $tj = "`store_id` = ".$_SESSION['store_id']." and `goods_state` = 1 and `goods_verify` = 1";
        if (intval($_GET['zf_class']) > 0) {
            $tj = $tj." and `zf_class_id` = ".intval($_GET['zf_class']);
        }
		if(trim($_GET['is_bind']) == 'true'){
			$tj = $tj." and `is_bind` > 0";
		}
		if (trim($_GET['keyword']) != '') {
            switch ($_GET['search_type']) {
                case "0":
                    $tj=$tj." and `goods_name` like '%".trim($_GET['keyword'])."%'";
                    break;
                case "1":
                    $tj=$tj." and `goods_id` = ".intval(trim($_GET['keyword']));
                    break;
                case "2":
                    $tj=$tj." and `goods_commonid` = ".intval($_GET['keyword']);
                    break;
            }
        }
		if(trim($_GET['order']) != ''){
			switch (trim($_GET['order'])){
				case "1":
					$tj = $tj." order by `goods_id` desc";
					break;
				case "2":
					$tj = $tj." order by `goods_id` asc";
					break;
				case "3":
					$tj = $tj." order by `goods_commonid` desc";
					break;
				case "4":
					$tj = $tj." order by `goods_commonid` asc";
					break;
				case "5":
					$tj = $tj." order by `goods_edittime` desc";
					break;
				case "6":
					$tj = $tj." order by `goods_edittime` asc";
					break;
				default:
					$tj = $tj." order by `goods_id` desc";
			}
		}else{
   			$tj = $tj." order by `goods_id` desc";
		}
	$sqlall = "select count(*) from `zmkj_goods` where {$tj}" ;//获取总条数
    $resultall = $zf_url->select_data($sqlall);
    $c = $resultall[0]["count(*)"];//获取总条数
    $page = new page($c,50);//一共多少条 每页显示多少条
	$sql="select * from `zmkj_goods` where {$tj} " .$page->limit;
	$rs_array = $zf_url->select_data($sql);
	?> 
<?php if (!empty($rs_array)) {
		$zf_url = new zf_url();
		 foreach ($rs_array as $val) { ?>
    <tr class="goodslist">
      <td class="nscs-table-handle"><span><a href="<?php echo urlShop('store_goods_online', 'edit_goods', array('commonid' => $val['goods_commonid']));?>" class="btn-blue"><i class="icon-edit"></i><p><?php echo $lang['nc_edit'];?></p></a></span></td>
      <td><?php
	  if($val["is_bind"]==1 and $val["zf_class_id"]>0){
		  foreach($zf_class as $key){
				if($key['id']==$val['zf_class_id']){
					if($key['class_type']==1){
						echo "<font color=\"#F00\">".$key['class_name']."</font>";
					}else{
						echo $key['class_name'];
					}
				}
		  }
	  }else{
		  echo "<font color=\"#F0F\">分类未绑定！</font>";
	  }
	  ?></td>
      <td class="tl"><dl class="goods-name">
          <dt style="max-width: 450px !important;">
            <a href="<?php echo urlShop('goods', 'index', array('goods_id' => $val['goods_id']));?>" target="_blank"><?php echo $val['goods_name']; ?></a></dt>  
        </dl></td>
      <td><?php if($val["goods_state"]==1){
		  echo "已上架";
	  }elseif($val["goods_state"]==0){
		  echo "已下架";
	  }elseif($val["goods_state"]==10){
		  echo "违规下架";
	  }?></td>
      <td><span class="price" id="<?php echo $val["goods_id"];?>"><?php echo $lang['currency'].$val['goods_promotion_price']; ?></span></td>
      <td><?php $url=$zf_url->get_zf_url($val["goods_id"]);
	  if($url=="未录入"){
		  echo "未录入";
	  }elseif($url=="不存在"){
		  echo "<font color='#f00'>商品链接不存在</font>";
	  }else{
		  echo "<a href=\"http://www.hebzfcgwssc.com/Mall/HeBei/detail.aspx?product_id=$url\" target=\"_blank\" class=\"olink\">打开链接</a>";
	  }
	   ?></td>
      <td><span><?php echo date("Y-m-d H:i",$val['goods_addtime']); ?></span></td>
      <td><?php echo date("Y-m-d H:i",$val["goods_edittime"]); ?></td>
    </tr>
    <?php
		$ys_goods=$zf_url->get_yingshe_goods($val["goods_id"]);
		if(!empty($ys_goods)){
			foreach($ys_goods as $ys_good){	
	?>
    <tr class="goodslist">
    	<td colspan="2"></td>
        <td colspan="6" class="tl">┖─&nbsp;&nbsp;<a class="olink" href="<?php echo $ys_good["productUrl"];?>" target="_blank"><?php echo $ys_good["productName"];?></a></td>
    </tr>
    <?php 	}
		}
	?>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php } ?>
  </tbody>
    <?php  if (!empty($rs_array)) { ?>
  <tfoot>
    
    <tr>
      <td colspan="20"><div class="pagination"> <?php echo $page->fpage();?> </div></td>
    </tr>
  </tfoot>
  <?php } ?>
</table>
<?php

//			未录入的商品			未录入的商品			未录入的商品			未录入的商品			未录入的商品			未录入的商品
}elseif($update_type == "noin"){
	$sql="select `goods_id`,`goods_commonid`,`goods_name` from `zmkj_goods` where `store_id` = ".$_SESSION['store_id']." and `goods_state` = 1 and `goods_verify` =1 and `is_bind`>0 and `zf_class_id`> 0 and `goods_id` NOT IN (SELECT `goods_id` FROM `zmkj_zf_url` WHERE 1) order by `goods_id` limit 0,100";
	$zf_url = new zf_url();
	$rs = $zf_url->select_data($sql);
	//print_r($rs);
	echo "<div id=\"goods\">";
	if(!empty($rs)){
		$i=1;
		foreach($rs as $good){
			echo "<p class=\"goods_list\"><span class=\"goods_num\">$i</span><span class=\"goods_id\"><a href=\"".urlShop('store_goods_online', 'edit_goods', array('commonid' => $good['goods_commonid']))."\" class=\"btn-blue\"><i class=\"icon-edit\"></i></a></span><span class=\"goods_name\"><a href=\"".urlShop('goods', 'index', array('goods_id' => $good['goods_id']))."\" target=\"_blank\">".$good["goods_name"]."</a></span><span class=\"goods_productid\" id=\"sku_".$good["goods_id"]."\"><a href=\"javascript:btnSearch(".$good['goods_id'].")\">录入</a></span></p>";
			$i++;
		}
	}else{
		echo "<p style=\"text-align:center;\">没有需要录入的商品!</p>";
	}
	echo "</div>";
}else{
	
	
//			已录入的商品			已录入的商品			已录入的商品			已录入的商品			已录入的商品			已录入的商品
	$sql="select `goods_id`,`goods_commonid`,`goods_name` from `zmkj_goods` where `store_id` = ".$_SESSION['store_id']." and `goods_state` = 1 and `goods_verify` =1 and `is_bind`>0 and `zf_class_id`> 0 and `goods_id` IN (SELECT `goods_id` FROM `zmkj_zf_url` WHERE `zf_product_id` like '%不存在该商品%') order by `goods_id` limit 0,100";
	$zf_url = new zf_url();
	$rs = $zf_url->select_data($sql);
	//print_r($rs);
	echo "<div id=\"goods\">";
	$i=1;
	foreach($rs as $good){
		echo "<p class=\"goods_list\"><span class=\"goods_num\">$i</span><span class=\"goods_id\"><a href=\"".urlShop('store_goods_online', 'edit_goods', array('commonid' => $good['goods_commonid']))."\" class=\"btn-blue\"><i class=\"icon-edit\"></i></a></span><span class=\"goods_name\"><a href=\"".urlShop('goods', 'index', array('goods_id' => $good['goods_id']))."\" target=\"_blank\">".$good["goods_name"]."</a></span><span class=\"goods_productid\" id=\"sku_".$good["goods_id"]."\"><a href=\"javascript:btnSearch(".$good['goods_id'].")\">录入</a></span></p>";
		$i++;
	}
	echo "</div>";
}
?>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js"></script>
<script src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/store_goods_list.js"></script> 
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
});
</script>
<script type="text/javascript">
        function btnSearch(sku) {
            var parms = {};
            parms.ECName = '诺融科技';
            parms.PlatformCode = 'HeBei';
            parms.sku = sku;
            parms.detailurl = '';
            $.ajax({
                type: "POST",
                url: "http://61.155.218.135:8989/Emall_Product_HeBei/EmallManage/Pages/SPSynOneProduct/SPSynOneProduct_WorkFlow.aspx/GetProductUrl",
                dataType: "json",
                data: JSON.stringify(parms),
                contentType: "application/json;utf-8",
                success: function (data) {
                    $("#sku_"+sku).text(data.d);
					if(data.d != "不存在该商品"){
						update_productid(sku,data.d);
					}
                },
                error: function (msg) {
                    //alert("错误信息：" + msg.responseText);
					$("#sku_"+sku).text("出现错误");
                }
            });
        }
</script>
<script type="text/javascript">		
		function update_productid(goods_id,productid){
			$("#sku_"+goods_id).text("正在更新数据库……");
			$.ajax({
					url:"/shop/index.php?act=store_goods_change_price&op=updateproductid",
					timeout:2000,
					type:"post",
					data:{
						"goods_id" : goods_id,
						"productid" : productid
						},
					success:function(data) {      
						$("#sku_"+goods_id).text(data);
					},
					error : function(jqXHR, textStatus, errorThrown) {
            			$("#sku_"+goods_id).text("出现错误");;
					}
				});
		}
</script>
<script type="text/javascript">
  $('input:radio').change(function () {
	$('#seachform').submit();
  });
</script>
<script language="javascript">
$("#start").click(function(){
	var goods_ids=[<?php foreach($rs as $key=>$val){echo $val['goods_id'].",";}?>0];
	var i=0;
	setTimeout(function x(){
		btnSearch(goods_ids[i]);
		i++;
		if(i<goods_ids.length-1)setTimeout(x,200);
	},1000);
})
</script>