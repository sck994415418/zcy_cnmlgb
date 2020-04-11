$(document).ready(function(){
	//根据jQuery选择器找到需要加载ystep的容器
		//loadStep 方法可以初始化ystep
		$(".ystep1").loadStep({
			//ystep的外观大小
			//可选值：small,large
			size: "small",
			//ystep配色方案
			//可选值：green,blue
			color: "green",
			//ystep中包含的步骤
			steps: [{
				//步骤名称
				title: "下单",
				//步骤内容(鼠标移动到本步骤节点时，会提示该内容)
				content: "您的订单已收到"
			}, {
				title: "师傅接单",
				content: "师傅收到订单"
			}, {
				title: "师傅出发",
				content: "正在马不停蹄的前往"
			}, {
				title: "维修中",
				content: "稍等，服务正在进行"
			}, {
				title: "完成",
				content: "您的订单已完成"
			}]
		});
		var i=$("#ddgz").find("li").length;
		$(".ystep1").setStep(i);
		if(i==5){
			$("#submit").removeAttr("disabled");
			$("#submit").css("background-color","#00bcd4")
		};
		/*遮幕*/
		$("#qxbtn").click(function(){
			$("#zhemu").show();
			$("#quxiaodiv").show();
		});
		$("#close").click(function(){
			$("#zhemu").hide();
			$("#quxiaodiv").hide();
		});
		$("#fbtn").click(function(){
			$("#zhemu").hide();
			$("#quxiaodiv").hide();
		});
		
})
