$(document).ready(function() {
	var isclick = false;

	function change(mydivid, value) {
		if (!isclick) {
			var tds = $("#" + mydivid + " ul li");
			for (var i = 0; i < value; i++) {
				var td = tds[i];
				$(td).find("img").attr("src", "img\/star_full.png");
			}
			var tindex = $("#" + mydivid).attr("currentIndex");
			tindex = tindex == 0 ? 0 : tindex + 1;
			for (var j = value; j < tindex; j++) {
				var td = tds[j];
				$(td).find("img").attr("src", "img\/star_empty.png");
			}
			$("#" + mydivid).attr("currentIndex", value);
		}
	}

	function repeal(mydivid, value) {
		if (!isclick) {
			var tds = $("#" + mydivid + " ul li");
			var tindex = $("#" + mydivid).attr("currentIndex");
			tindex = tindex == 0 ? 0 : tindex + 1;
			for (var i = tindex; i < value; i++) {
				var td = tds[i];
				$(td).find("img").attr("src", "img\/star_empty.png");
			}
			$("#" + mydivid).attr("currentIndex", value);
		}
	}

	function change1(mydivid, value) {
		if (!isclick) {
			change(mydivid, value);
		}
	}
	$(function() {
		initEvent('mydiv2');
	});

	function initEvent(mydivid) {
		//var tableWjx =$("#tableWjx ul li");
		// var items= tableWjx.getElementsByTagName("ul");	

		var tds = $("#" + mydivid + " ul li");
		for (var i = 0; i < tds.length; i++) {
			var td = tds[i];
//			$(td).live('mouseover', function() {
//				var value = $(this).attr("value");
//				change(mydivid, value);
//			});			
			$(td).live('click', function() {
				var value = $(this).attr("value");
				change1(mydivid, value);				
			});
		}
	} /*结束*/
	// $("#subt").click(function(){		
	// 	var textval=$("#textarea").val();
	// 	var cityid=parseInt( $(".star_ul").attr("value")); 	
	// 	if(textval==""){
	// 		$(".pj p").show();			
	// 		return false;
	// 	}else if(cityid==NaN){
	// 		$("#alet").show();
	// 		return false;
	// 	}
	// 	else{
	// 		$("#zhemu").show();
	// 		$("#successdiv").show();
	// 	}
	// });/*结束*/
	$(".pj textarea").focus(function(){
		$(".pj p").hide();
	});/*结束*/
	$("#zhemu").click(function(){
		$("#zhemu").hide();
		$("#successdiv").hide();
	})
	
	
})