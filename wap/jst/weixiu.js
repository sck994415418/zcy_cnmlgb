$("document").ready(function(){
	/*列表切换*/
	$("#firstpane .menu_body:eq(0)").show();
	$("#firstpane p.menu_head").click(function() {
		$(this).addClass("current").next("div.menu_body").slideToggle(300).siblings("div.menu_body").slideUp("slow");
		$(this).siblings().removeClass("current");
	});
	$("#secondpane .menu_body:eq(0)").show();
	$("#secondpane p.menu_head").mouseover(function() {
		$(this).addClass("current").next("div.menu_body").slideDown(500).siblings("div.menu_body").slideUp("slow");
		$(this).siblings().removeClass("current");
	});
	var $qd = $("#qdbtn");
var $submit = $("#submit");
$qd.click(function() {	
	var reg = /^\s*$/g;
	var yysj = $("#yysj").val();
	var wz = $("#wz").val();
	var xh = $("#xh").val();
	var tel = $("#tel").val();
	var phReg = /^1[34578]\d{9}$/;
	if (reg.test(yysj) && reg.test(wz) && reg.test(xh) && !phReg.test(tel)) {	
		$submit.css("background-color", "#DCDCDC");
		// $submit.attr("disabled", true);
	} else {
		$submit.css("background-color", " #00bcd5");
		// $submit.removeAttr("disabled");
	}
});//
/*提交按钮*/
$submit.click(function(){
$("#form").submit();	
	$submit.css("background-color", "#DCDCDC");
		// $submit.attr("disabled", true);
		
})

/*添加消费弹窗*/
var j = 20;
$("#num").text(j);
$(function() {
		$("#radio").click(function() {
			if ($(this).attr("checked")) {
				$("#alert").show();
				$("#zhemu").show();
			}
		})
	})
	/*小费弹窗结束*/
$(function() {
		$("#close").click(function() {
			$("#alert").hide();
			$("#zhemu").hide();
			$("#radio").attr("checked", false);
			var j = 20;
			$("#num").text(j);
		})
	}) //
	/*确定*/
$("#tr").click(function() {
		$("#alert").hide();
		$("#zhemu").hide();
	})
	/*添加维修费用*/
$(function() {
	$("td").click(function() {
		var i = parseFloat($(this).text());
		var j = parseFloat($("#nums").val());
		var z = j + i;
		$("#num").text(z);
		
	})
})
//})
//$(document).ready(function() {
//	/*列表切换*/
//	$("#firstpane .menu_body:eq(0)").show();
//	$("#firstpane p.menu_head").click(function() {
//		$(this).addClass("current").next("div.menu_body").slideToggle(300).siblings("div.menu_body").slideUp("slow");
//		$(this).siblings().removeClass("current");
//	});
//	$("#secondpane .menu_body:eq(0)").show();
//	$("#secondpane p.menu_head").mouseover(function() {
//		$(this).addClass("current").next("div.menu_body").slideDown(500).siblings("div.menu_body").slideUp("slow");
//		$(this).siblings().removeClass("current");
//	});
//	
//	var j=20;
//   	    $("#num").text(j);
//	/*小费弹窗*/
//$(function(){
//	$("#radio").click(function(){
//		if($(this).attr("checked")){
//			$("#alert").show();
//   		$("#zhemu").show();
//   		
//		}
//	})
//})
///*小费弹窗结束*/
//$(function(){
//	$("#close").click(function(){
//		$("#alert").hide();
//   		$("#zhemu").hide();
//   		$("#radio").attr("checked",false);
//   		var j=20;
//   	    $("#num").text(j);
//	})
//})
///*确定*/
//$("#tr").click(function(){
//	$("#alert").hide();
//   		$("#zhemu").hide();
//   	
//})
//
///*添加维修费用*/
//$(function(){
//	$("td").click(function(){
// var i= parseInt($(this).text());
// var j=20;
// var z=j+i;   
//		$("#num").text(z);
//	})
//})
/*结束*/
/*地图*/
var map = new BMap.Map("allmap");  // 创建Map实例
	map.centerAndZoom("迁安",15);      // 初始化地图,用城市名设置地图中
});