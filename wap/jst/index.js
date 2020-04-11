$(function() {
	document.documentElement.style.fontSize = document.documentElement.clientWidth / 6.4 + 'px';
	var mySwiper = new Swiper('.swiper-container', {
		autoplay: 1000, //可选选项，自动滑动
		pagination: '.swiper-pagination',
		paginationClickable: true,
		autoplayDisableOnInteraction: false,
	})
	
	
	
	/*jiance*/
				$.ajax({
   //	type:"get",
   	url:"http://datainfo.duapp.com/shopdata/getBanner.php",
  // 	async:false,
   	dataType: "jsonp",
   	//jsonp: "callback",//传递给请求处理程序或页面的，用以获得jsonp回调函数名的参数名(一般默认为:callback)
   //	jsonpCallback:"flightHandler",//自定义的jsonp回调函数名称，默认为jQuery自动生成的随机函数名，也可以写"?"，jQuery会自动为你处理数据
   	success: function(data){
   		//console.log(json);
   		var $swiper_wrapper=$(".swiper-wrapper");
			$.each(data,function(i){
				var $imgBox=$("<div class='swiper-slide'><img src='"+eval("("+data[i].goodsBenUrl+")")[0]+"'/></div>");
				$swiper_wrapper.append($imgBox);
			})
			var mySwiper=new Swiper(".swiper-container1",{
			autoplay:3000,
			speed:500,
			autoplayDisableOnInteraction:false,
			pagination : '.swiper-pagination',
			paginationClickable :true,
            loop:true,
		});
   	}
   });
});