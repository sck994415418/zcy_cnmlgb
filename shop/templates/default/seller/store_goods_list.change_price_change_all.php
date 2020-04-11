<?php defined('InShopNC') or exit('Access Invalid!');?>
<style type="text/css">
	tr.goodslist:hover td{ background-color:#AFF;}
	input.pricetxt{border:dotted 1px #00FF00;}
	a.olink:visited{color:#5F20F0;}
	.toDisplay {position:fixed;background:#FFFFFF;display:none;width:40%;height:230px;top:200px;left:30%;right:30%;z-Index:3;text-align:center;padding:50px 50px 50px 50px;}
	.mask {display:none;z-index:2;position:fixed;width:100%; height:100%;top:0;left:0;background:#000;opacity:0.5;}
	#info{color:#FF0000;line-height:25px;text-align:center;mini-height:25px;}
	.yslist{margin-left:185px;text-align:left;line-height:25px;padding-left:35px; background-image:url(../../../../list-bg.png); background-position:left; background-repeat:no-repeat;}
	.form{border-bottom: solid 1px #E6E6E6;text-align:center;padding:35px;display:block;}
	#fsdiv{margin:0px auto;display:inline-block;width:180px;text-align:center;line-height:240px;}
	#zf_classdiv{margin:0px auto;display:none;width:200px;text-align:center;}
	#zf_pricediv{margin:0px auto;display:none;width:220px;text-align:center;}
	#sjdiv{margin:0px auto;display:inline-block;width:140px;text-align:center;}
	#subdiv{margin:0px auto;display:inline-block;width:120px;text-align:center;}
	#rslist{text-align:center;}
	#rslist table{width:80%;margin:20px auto;}
	#rslist th{padding:5px;border:solid 1px #CCC;text-align:center;}
	#rslist td{padding:5px;border:solid 1px #CCC}
</style>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
  <a href="<?php echo urlShop('store_goods_add');?>" class="ncsc-btn ncsc-btn-green" title="<?php echo $lang['store_goods_index_add_goods'];?>"> <?php echo $lang['store_goods_index_add_goods'];?></a>
</div>
<?php $zf_url = new zf_url(); ?>
<form method="post" action="index.php">
	<div class="form">
        <input type="hidden" name="act" value="store_goods_change_price" />
        <input type="hidden" name="op" value="index" />
        <input type="hidden" name="type" value="change_all" />
    	<div id="fsdiv">
            <select name="fs" id="fs">
              <option value="0">所有商品</option>
              <option value="1">按商城goods_id</option>
              <option value="2">按政府采购网分类</option>
              <option value="3">按政府采购网售价</option>
            </select>
        </div>
    	<div id="zf_classdiv">
			<?php
                 $sql="select `id`,`class_name`,`class_type` from `zmkj_zf_class`";
            //链接数据库
                 $zf_class=$zf_url->select_data($sql);
                 if(!empty($zf_class)){
                    echo "<select multiple=\"multiple\" size=\"8\" name=\"zf_class\" id=\"zf_class\" style=\"height:auto;width:auto;\">";
                    foreach($zf_class as $key=>$val){
                        echo "<option value=\"".$val['id']."\"";
                        if ($_GET['zf_class'] == $val['id']){
                            echo " selected=selected";
                        }
                        echo ">".$val['class_name']."</option>";
                    }
                    echo '</select>';
                 }
            ?>  
        </div>
<script type="text/javascript">
	<?php
		echo "var zf_class = [";
		foreach($zf_class as $key=>$val){
			echo "[\"".$val['id']."\",\"".$val['class_name']."\"],";
		}
		echo "[\"0\",\"未分类\"]];";
	?>
</script>
    	<div id="zf_pricediv">
        	<select name="id_price" id="id_price">
              <option value="eq">等于</option>
              <option value="xy">小于</option>
              <option value="jy">介于</option>
              <option value="dy">大于</option>
            </select>
            <input type="text" size="10" name="num" id="num" value=""/>
        </div>
        <div id="sjdiv">
			<select name="sj" id="sj">
              <option value="s">上调</option>
              <option value="j">下调</option>
            </select>
            <input type="text" size="5" name="jg" id="jg" value=""/>
        </div>
        <div id="subdiv">
        	<label class="submit-border"><input type="button" class="submit" value="执行" id="submit" /></label>
        </div>
        <div id="info"></div>
	</div>
</form>
<div id="rslist"></div>
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
$(document).ready(function(){
	displayblock();	
});
</script>
<script language="javascript">
function displayblock(){
	if($("#fs").val()=="0"){
		$("#zf_classdiv").css("display","none");
		$("#zf_pricediv").css("display","none");
	}else if($("#fs").val()=="1" || $("#fs").val()=="3"){
		$("#zf_classdiv").css("display","none");
		$("#zf_pricediv").css("display","inline-block");
	}else if($("#fs").val()=="2"){
		$("#zf_classdiv").css("display","inline-block");
		$("#zf_pricediv").css("display","none");
	}
}
</script>
<script language="javascript">
$("#fs").change(function(){
	displayblock();	
});
</script>
<script language="javascript">
$("#submit").click(function(){
	if($("#fs").val()=="1" || $("#fs").val()=="3"){
		if($("#id_price").val()=="jy"){
			if(($("#num").val().trim()!="") && ($("#num").val().indexOf(",") != -1)){
				nums = $("#num").val().trim().split(",");
				for(i=0;i<nums.length;i++){
					if(i>1 || isNaN(nums[i]) || nums[i] == ""){
						$("#info").text("格式错误：改价范围格式如“1000,2000”，两个数字用英文逗号隔开");
						$("#num").focus();
						return;
					}
				}
			}else{
				$("#info").text("格式错误：改价范围格式如“1000,2000”，两个数字用英文逗号隔开");
				$("#num").focus();
				return;
			}
		}else{
			if($("#num").val().trim()=="" || isNaN($("#num").val())){
				$("#info").text("改价范围只能是数字！");
				$("#num").focus();
				return;
			}
		}
	}
	if($("#jg").val().trim()=="" || isNaN($("#jg").val())){
		$("#info").text("调整价格只能是数字！");
		$("#jg").focus();
		return;
	}
	var parm = {};
	switch($("#fs").val()){
		case "0" :
			parm["fs"] = "0";
			parm["sj"] = $("#sj").val();
			parm["jg"] = $("#jg").val().trim();
			break;
		case "1" :
			parm["fs"] = "1";
			parm["id_price"] = $("#id_price").val();
			parm["num"] = $("#num").val().trim();
			parm["sj"] = $("#sj").val();
			parm["jg"] = $("#jg").val().trim();
			break;
		case "2" :
			parm["fs"] = "2";
			parm["zf_class"] = $("#zf_class").val()
			parm["sj"] = $("#sj").val();
			parm["jg"] = $("#jg").val().trim();
			break;
		case "3" :
			$("#info").text("此功能暂停使用！");
			return;
			parm["fs"] = "3";
			parm["id_price"] = $("#id_price").val();
			parm["num"] = $("#num").val().trim();
			parm["sj"] = $("#sj").val();
			parm["jg"] = $("#jg").val().trim();
			break;
	}
	$.ajax({
		url:"/shop/index.php?act=store_goods_change_price&op=change_price_all",
		timeout:2000,
		type:"post",
		dataType:"JSON",
		data:JSON.stringify(parm),
		contentType: "application/json;charset=utf-8",
		success:function(data){
			if(data.isSuccess){
				$("#info").text(data.returnMsg + "; 修改商品数量： "+data.affected_rows);
				get_change_list();
			}else{
				$("#info").text("改价失败");
			}
		},
		error : function(xhr) {
			$("#info").text("出现错误："+xhr.status + ":" + xhr.statusText + "; 错误信息：" + xhr.responseText);
		}
	});
});
</script>
<script type="text/javascript">
$(document).ready(function(){
	get_change_list();
});
function get_change_list(){
	$.ajax({
		url:"/shop/index.php?act=store_goods_change_price&op=get_change_price_list",
		dataType:"JSON",
		contentType: "application/json;charset=utf-8",
		success:function(data){
			if(data.rows>0){
				output = "<table><tr><th>序号</th><th>改价方式</th><th>范围</th><th>调整</th><th>数值</th><th>改价时间</th></tr>";
				for(i=0;i<data.rows;i++){
					switch(data.rs[i].fs){
						case "0":
							output = output + "<tr><td>" + (i+1) + "</td><td>所有商品</td><td></td><td>" + data.rs[i].sj + "</td><td>" + data.rs[i].jg + "</td><td>" + data.rs[i].time + "</td></tr>";
							break;
						case "1":
							output = output + "<tr><td>" + (i+1) + "</td><td>按商品SKU</td><td>" + data.rs[i].ip + data.rs[i].num + "</td><td>" + data.rs[i].sj + "</td><td>" + data.rs[i].jg + "</td><td>" + data.rs[i].time + "</td></tr>";
							break;
						case "2":
							zf_class_names = "";
							zf_class_ids = data.rs[i].zf_class.split(",");
							for(j=0;j<zf_class_ids.length;j++){
								zf_class.forEach( function(item){
									if(item[0] == zf_class_ids[j]){
										zf_class_names = zf_class_names + item[1] + "</br>";
									}
								});
							}
							output = output + "<tr><td>" + (i+1) + "</td><td>按政府采购网分类</td><td>" + zf_class_names + "</td><td>" + data.rs[i].sj + "</td><td>" + data.rs[i].jg + "</td><td>" + data.rs[i].time + "</td></tr>";
							break;
						case "3":
							output = output + "<tr><td>" + (i+1) + "</td><td>按政府采购网售价</td><td>" + data.rs[i].ip + data.rs[i].num + "</td><td>" + data.rs[i].sj + "</td><td>" + data.rs[i].jg + "</td><td>" + data.rs[i].time + "</td></tr>";
							break;
					}
				};
				output = output + "</table>";
				reg = />-</g;
				output = output.replace(reg,">↓<");
				reg = />\+</g;
				output = output.replace(reg,">↑<");
				reg = /dy/g;
				output = output.replace(reg,"大于");
				reg = /jy/g;
				output = output.replace(reg,"介于");
				reg = /eq/g;
				output = output.replace(reg,"等于");
				reg = /xy/g;
				output = output.replace(reg,"小于");
				$("#rslist").html(output);
			}else{
				$("#rslist").text("暂无记录");
			}
		},
		error : function(xhr) {
			$("#rslist").text("出现错误："+xhr.status + ":" + xhr.statusText + "; 错误信息：" + xhr.responseText);
		}
	});
}
</script>