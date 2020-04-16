<?php defined('InShopNC') or exit('Access Invalid!');?>
<style type="text/css">
	tr.goodslist:hover td{ background-color:#AFF;}
	input.pricetxt{border:dotted 1px #00FF00;}
	a.olink:visited{color:#5F20F0;}
	.toDisplay {position:fixed;background:#FFFFFF;display:none;width:40%;height:230px;top:200px;left:30%;right:30%;z-Index:3;text-align:center;padding:50px 50px 50px 50px;}
	.mask {display:none;z-index:2;position:fixed;width:100%; height:100%;top:0;left:0;background:#000;opacity:0.5;}
	#errinfo{color:#FF0000;line-height:25px;text-align:center;mini-height:25px;display:inline-block;}
	.yslist{margin-left:105px;text-align:left;line-height:25px;padding-left:35px; background-image:url(../../../../list-bg.png); background-position:left; background-repeat:no-repeat;}
</style>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
  <a href="<?php echo urlShop('store_goods_add');?>" class="ncsc-btn ncsc-btn-green" title="<?php echo $lang['store_goods_index_add_goods'];?>"> <?php echo $lang['store_goods_index_add_goods'];?></a>
</div>
<form method="get" action="index.php">
  <table class="search-form">
    <input type="hidden" name="act" value="store_goods_change_price" />
    <input type="hidden" name="op" value="index" />
    <input type="hidden" name="type" value="change_yingshe" />
    <tr>
      <td>&nbsp;</td>
      <td class="w100"></td>
      <td class="w160"></td>
      <td class="w160"></td>
      <th>
        <select name="search_type">
          <option value="0" <?php if (intval($_GET['search_type']) == 0) {?>selected="selected"<?php }?>><?php echo $lang['store_goods_index_goods_name'];?></option>
          <option value="1" <?php if (intval($_GET['search_type']) == 1) {?>selected="selected"<?php }?>>商品ID</option>
          <option value="2" <?php if (intval($_GET['search_type']) == 2) {?>selected="selected"<?php }?>>平台货号</option>
          <option value="3" <?php if (intval($_GET['search_type']) == 3) {?>selected="selected"<?php }?>>政府采购网ID</option>
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
      <th class="w105">操作</th>
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

	$zf_url = new zf_url();
    $tj = "`store_id` = ".$_SESSION['store_id']." and `goods_id` in (SELECT DISTINCT `skuid` FROM `zmkj_goods_orm` WHERE 1) and `is_bind` = 0";
    if (trim($_GET['keyword']) != '') {
       switch ($_GET['search_type']) {
           case "0":
				$keyword = str_replace(" ","%",trim($_GET["keyword"]));
                $tj=$tj." and `goods_name` like '%".$keyword."%'";
               break;
           case "1":
               $tj=$tj." and `goods_id` = ".intval(trim($_GET['keyword']));
               break;
           case "2":
               $tj=$tj." and `goods_commonid` = ".intval($_GET['keyword']);
               break;
           case "3":
               $tj=$tj." and `goods_id` IN (select DISTINCT `skuid` from `zmkj_goods_orm` where `productId` like '%".trim($_GET['keyword'])."%')";
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
    <?php if (!empty($rs_array)) { ?>
    <?php foreach ($rs_array as $val) { ?>
    <tr class="goodslist">
      <td class="nscs-table-handle"><span><a href="<?php echo urlShop('store_goods_online', 'edit_goods', array('commonid' => $val['goods_commonid']));?>" class="btn-blue"><i class="icon-edit"></i><p><?php echo $lang['nc_edit'];?></p></a></span><span><a href="javascript:void(0)" onclick="addyingshe(<?php echo $val["goods_id"].",'".$val["goods_name"]."'"; ?>);" class="btn-green"><i class="icon-external-link"></i><p>映射</p></a></span></td>
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
		  echo "<a class=\"olink\" href=\"http://www.hebzfcgwssc.com/Mall/HeBei/detail.aspx?product_id=$url\" target=\"_blank\">打开链接</a>";
	  }
	   ?></td>
      <td><?php echo date("Y-m-d H:i:s",$val["goods_addtime"]); ?></td>
      <td><?php echo date("Y-m-d H:i:s",$val["goods_edittime"]); ?></td>
    </tr>
    <tr class="goodslist">
      <td colspan="8" class="ystd" id="ys<?php echo $val["goods_id"];?>">
    <?php
		$ys_goods=$zf_url->get_yingshe_goods($val["goods_id"]);
		if(!empty($ys_goods)){
			foreach($ys_goods as $ys_good){	
	?>
        <p class="yslist"><a class="olink" href="<?php echo $ys_good["productUrl"];?>" target="_blank"><?php echo $ys_good["productName"];?></a></p>
    <?php 	}
		} ?>
    </tr>
<?php
		}
	} else { ?>
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
<div class="mask">
</div>
<div class="toDisplay">
    <form name="yingshe" id="yingshe" action="#" method="post">
    	<table width="450" height="200" border="0" style="margin:auto;">
        	<tr>
            	<th colspan="2" align="center"><h2 align="center">添加商品映射关系</h2></th>
            </tr>
            <tr>
            	<td colspan="2" height="30"><input type="hidden" name="goods_id" id="goods_id" value="" /><input type="text" name="goods_name" id="goods_name" value="" style="border:none;width:100%;text-align:center" contenteditable="false" /></td>
            </tr>
            <tr>
                <td align="right"><font color="#FF0000">*</font>政府采购网商品ID：</td>
                <td><input type="text" name="goods_yingshe_id" id="goods_yingshe_id" value="" size="40" /></td>
            </tr>
            <tr>
            	<td width="35%" align="right"><font color="#FF0000">*</font>政府采购网商品名称：</td>
                <td width="65%"><input type="text" name="goods_yingshe_name" id="goods_yingshe_name" value="" size="40" /></td>
            </tr>
            <tr>
                <td colspan="2"><span id="errinfo"></span></td>
            </tr>
            <tr>
                <td align="right"><input type="button" name="cancle" class="submit" id="cancle" value="取消" /></td>
                <td align="center"><input type="button" name="submit" class="submit" id="addyingshe" value="添加" /></td>
            </tr>
    	</table>
    </form>
</div>
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
<script language="javascript">
$(".price").click(function(){
	var span = $(this);
	var goods_id = span.attr('id');
	if(span.html().indexOf("input") >= 0)return;
	var txt = $.trim(span.text()).replace("¥",""); 
	var input = $("<input type='text' class='pricetxt' value='" + txt + "'/>"); 
	input.width(span.width());
	input.height(span.height());
	span.html(input); 
	input.trigger("focus"); 
	input.blur(function(){
		var newtxt = $(this).val();
		var inputtxt = $(this);
		if(isNaN(newtxt)){
			alert("价格只能是数字");
			input.focus();
		}else{
			if (newtxt!=txt) {
			span.html("修改中……");
				$.ajax({
					url:"/shop/index.php?act=store_goods_change_price&op=changPrice",
					timeout:2000,
					type:"post",
					data:{
						"goods_id" : goods_id,
						"new_price" : newtxt
						},
					success:function(data) {      
						if(trim(data)=="success"){
							span.html("¥"+newtxt);
						}else{
							alert(trim(data));
							input.trigger("focus");
						}
					},
					error : function(msg) {
            			alert(msg.responseText);
						span.html("¥"+txt);
					}
					})
			}else{
				span.html("¥"+txt);
			}
		}
	})
})
</script>
<script language="javascript">
function in_array(stringToSearch, arrayToSearch) {
     for (s = 0; s < arrayToSearch.length; s++) {
      thisEntry = arrayToSearch[s].toString();
      if (thisEntry == stringToSearch) {
       return true;
      }
     }
     return false;
}
</script>
<script language="javascript">
function addyingshe(goods_id,goods_name) {
	$("#goods_name").val(goods_name.trim());
	$("#goods_id").val(goods_id);
	$(".mask").fadeIn();
	$(".toDisplay").fadeIn();
}

$("#cancle").click(function() {
	$("#goods_name").val("");
	$("#goods_id").val("");
	$("#goods_yingshe_id").val("");
	$("#goods_yingshe_name").val("");
	$("#errinfo").text("");
	$(".mask").fadeOut();
	$(".toDisplay").fadeOut();
});

$("#addyingshe").click(function() {
	var goods_id = $("#goods_id").val();
	var goods_name = $("#goods_name").val();
	var goods_yingshe_id = $("#goods_yingshe_id").val().trim();
	var goods_yingshe_name = $("#goods_yingshe_name").val().trim();
	var goods_yingshe_id_str = ['0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f'];
	if(goods_id==""){
		$("#errinfo").text("goods_id不能为空");
		return;
	}
	if(goods_name==""){
		$("#errinfo").text("goods_name不能为空");
		return;
	}
	if(goods_yingshe_name==""){
		$("#errinfo").text("政府采购网商品名称不能为空");
		$("#goods_yingshe_name").focus();
		return;
	}
	if(goods_yingshe_id==""){
		$("#errinfo").text("政府采购网商品id不能为空");
		$("#goods_yingshe_id").focus();
		return;
	}
	if(isNaN(goods_id)){
		$("#errinfo").text("goods_id只能是数字");
		return;
	}
	var arr = goods_yingshe_id.split("-");
	if(arr.length==5){
		if(arr[0].length!=8||arr[1].length!=4||arr[2].length!=4||arr[3].length!=4||arr[4].length!=12){
			$("#errinfo").text("政府采购网商品ID不正确！");
			$("#goods_yingshe_id").focus();
			return;
		}
		for (x in arr) {
			for(i=0;i<arr[x].length;i++){
				if(!in_array(arr[x].charAt(i),goods_yingshe_id_str)){
					$("#errinfo").text("政府采购网商品ID不正确！");
					$("#goods_yingshe_id").focus();
					return;
				}
			}
		}
	}else{
		$("#errinfo").text("政府采购网商品ID不正确！");
		$("#goods_yingshe_id").focus();
		return;
	}
	$("#errinfo").text("正在创建商品映射……");
	var parms = {};
    parms.goods_id = goods_id;
    parms.goods_name = goods_name;
    parms.goods_yingshe_id = goods_yingshe_id;
    parms.goods_yingshe_name = goods_yingshe_name;
	$.ajax({
		url:"/shop/index.php?act=store_goods_change_price&op=addyingshe",
		type: "POST",
		timeout: 2000,
		dataType: "JSON",
        data: JSON.stringify(parms),
        contentType: "application/json;charset=utf-8",
		success:function(msg) {
			if(msg.isSuccess){
				$("#errinfo").html(msg.returnMsg);
				setTimeout(function xx(){
				$("#goods_name").val("");
				$("#goods_id").val("");
				$("#goods_yingshe_id").val("");
				$("#goods_yingshe_name").val("");
				$("#errinfo").html("");
				$(".mask").fadeOut();
				$(".toDisplay").fadeOut();},1000);
				listyingshe(goods_id);
			}else{
				$("#errinfo").html(msg.returnMsg);
			}
		},
		error : function(xhr) {
            $("#errinfo").html(xhr.status + ":" + xhr.statusText);
		}
	});	
});
</script>
<script language="javascript">
function listyingshe(goods_id){
	var str = "";
	$.ajax({
		url:"/shop/index.php?act=store_goods_change_price&op=listyingshe",
		timeout:2000,
		type:"get",
		dataType: "JSON",
		data:{
			"goods_id" : goods_id
		},
		success:function(data) {      
			if(data.isSuccess){
				for(i=0;i<data.returnMsg.length;i++){
					str = str + "<p class='yslist'><a class='olink' href='" + data.returnMsg[i].productUrl + " target='_blank'>" + data.returnMsg[i].productName + "</a></p>";
				}
				$("#ys"+goods_id).html(str);
			}else{
				alert(data.returnMsg);
			}
		},
		error : function(msg) {
            alert(msg.responseText);
		}
	})
}
</script>