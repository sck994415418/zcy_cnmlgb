(function($) {
	$.fn.fullScreen = function(settings) {//首页焦点区满屏背景广告切换
		var defaults = {
			time: 5000,
			css: 'full-screen-slides-pagination'
		};
		var settings = $.extend(defaults, settings);
		return this.each(function(){
			var $this = $(this);
		    var size = $this.find("li").size();
		    var now = 0;
		    var enter = 0;
		    var speed = settings.time;
			var len = $this.find("ul li").length;
			var aindex = 0;
			var isstop = 0;
		    $this.find("li:gt(0)").hide();
			var btn = '<ul class="' + settings.css + '">';
			for (var i = 0; i < size; i++) {
				btn += '<li>' + '<a href="javascript:void(0)">' + (i + 1) + '</a>' + '</li>';
			}
			btn += "</ul><div class='banner-arrow'><div class='arrow pre'></div><div class='arrow next'></div></div>";
			$this.after(btn);
			var $pagination = $this.next();
			$pagination.find("li").first().addClass('current');
			$pagination.find("li").click(function() {
        		var change = $(this).index();
        		$(this).addClass('current').siblings('li').removeClass('current');
        		$this.find("li").eq(change).css('z-index', '800').show();
        		$this.find("li").eq(now).css('z-index', '900').fadeOut(400,
        		function() {
        			$this.find("li").eq(change).fadeIn(500);
        		});
        		now = change;
			}).mouseenter(function() {
        		enter = 1;
        	}).mouseleave(function() {
        		enter = 0;
        	});
        	function slide(aindex) {
				if(typeof(now) == "undefined"){
					now = aindex;
				} else {
					now = now;
					}
				var change = now + 1;
        		if (enter == 0){
        			if (change == size) {
        				change = 0;
        			}
        			$pagination.find("li").eq(change).trigger("click");
        		}
        		setTimeout(slide, speed);
				}
        	setTimeout(slide, speed);
			
			var $bannerarrow = $(".banner-arrow");
			$bannerarrow.find(".arrow").css("opacity", 0.0).hover(function() {
				$(this).stop(true, false).animate({
					"opacity": "0.5"
				},
				300);
			},
			function() {
				$(this).stop(true, false).animate({
					"opacity": "0"
				},
				300);
			});
			$bannerarrow.find(".pre").click(function() {
				aindex -= 1;
				if (aindex == -1) {
					aindex = len - 1;
				}
				slide(aindex);
			});
			$bannerarrow.find(".next").click(function() {
				aindex += 1;
				if (aindex == len) {
					aindex = 0;
				}
				slide(aindex);
			});
			
		});
	}
	$.fn.jfocus = function(settings) {//首页焦点广告图切换
		var defaults = {
			time: 5000
		};
		//.extend 用于将一个或多个对象的内容合并到目标对象
		var settings = $.extend(defaults, settings);
		//each 规定为每个匹配元素规定运行的函数
		return this.each(function(){
			var $this = $(this);
			var Height = $this.height();
			//find 获得当前元素集合中每个元素的后代  	.length 属性可返回字符串中的字符数目
			var len = $this.find("ul li").length;
			var index = 0;
			var picTimer;
			//倒计时
			var btn = "<div class='pagination'>";
			for (var i = 0; i < len; i++) {
				btn += "<span></span>";
			}
			//左右按钮
			btn += "</div><div class='arrow pre'></div><div class='arrow next'></div>";
			//append() 方法在被选元素的结尾（仍然在内部）插入指定内容。
			$this.append(btn);
			//find() 方法获得当前元素集合中每个元素的后代	opacity 属性设置元素的不透明级别	当鼠标指针穿过元素时，会发生 mouseenter 事件
			$this.find(".pagination span").css("opacity", 0.4).mouseenter(function() {
				//pagination 
				index = $this.find(".pagination span").index(this);
				showPics(index);
				//eq() 方法将匹配元素集缩减值指定 index 上的一个。 trigger() 方法触发被选元素的指定事件类型。
			}).eq(0).trigger("mouseenter");
			$this.find(".arrow").css("opacity", 0.0).hover(function() {
				//stop() 方法停止当前正在运行的动画。	animate() 方法执行 CSS 属性集的自定义动画。
				$(this).stop(true, false).animate({
					"opacity": "0.5"
				},
				300);
			},
			function() {
				$(this).stop(true, false).animate({
					"opacity": "0"
				},
				300);
			});
			$this.find(".pre").click(function() {
				index -= 1;
				if (index == -1) {
					index = len - 1;
				}
				showPics(index);
			});
			$this.find(".next").click(function() {
				index += 1;
				if (index == len) {
					index = 0;
				}
				showPics(index);
			});
			$this.find("ul").css("height", Height * (len));
			$this.hover(function() {
				clearInterval(picTimer);
			},
			function() {
				picTimer = setInterval(function() {
					index++;
					if (index == len) {
						index = 0;
					}
					showPics(index);
				},
				settings.time);
				//当鼠标指针离开元素时，会发生 mouseleave 事件。
			}).trigger("mouseleave");
			function showPics(index) {
				var nowTop = -index * Height;
				$this.find("ul").stop(true, false).animate({
					"top": nowTop
				},
				300);
				$this.find(".pagination span").stop(true, false).animate({
					"opacity": "0.4"
				},
				300).eq(index).stop(true, false).animate({
					"opacity": "1"
				},
				300);
			}
			//限时折扣js
//			sped = 50;
//			xianshi = $('#saleDiscount');
//			//取到li的数量
//			count = xianshi.find("ul li").length;
//			one = $('#one');
//			two = $('#two');
//			two.innerHTML = one.innerHTML;
//			alert(count);return;
//			function Marquee(){
//		        if(two.offsetTop-xianshi.scrollTop<=0) {
//		        	xianshi.scrollTop-=one.offsetHeight;
//		        } else {
//		            xianshi.scrollTop++;
//		        }
//		    }
//		    var MyMar=setInterval(Marquee,sped);
//		    xianshi.onmouseover=function(){clearInterval(MyMar)}
//		    xianshi.onmouseout=function(){MyMar=setInterval(Marquee,sped)}
		});
	}
	$.fn.jfade = function(settings) {//首页标准模块中间多图广告鼠标触及凸显
		var defaults = {
			start_opacity: "1",
			high_opacity: "1",
			low_opacity: ".1",
			timing: "500"
		};
		var settings = $.extend(defaults, settings);
		settings.element = $(this);
		//set opacity to start
		$(settings.element).css("opacity", settings.start_opacity);
		//mouse over
		$(settings.element).hover(
		//mouse in
		function() {
			$(this).stop().animate({
				opacity: settings.high_opacity
			},
			settings.timing); //100% opacity for hovered object
			$(this).siblings().stop().animate({
				opacity: settings.low_opacity
			},
			settings.timing); //dimmed opacity for other objects
		},
		//mouse out
		function() {
			$(this).stop().animate({
				opacity: settings.start_opacity
			},
			settings.timing); //return hovered object to start opacity
			$(this).siblings().stop().animate({
				opacity: settings.start_opacity
			},
			settings.timing); // return other objects to start opacity
		});
		return this;
	}
})(jQuery);
	function takeCount() {
	    setTimeout("takeCount()", 1000);
	    $(".time-remain").each(function(){
	        var obj = $(this);
	        var tms = obj.attr("count_down");
	        if (tms>0) {
	            tms = parseInt(tms)-1;
                var days = Math.floor(tms / (1 * 60 * 60 * 24));
                var hours = Math.floor(tms / (1 * 60 * 60)) % 24;
                var minutes = Math.floor(tms / (1 * 60)) % 60;
                var seconds = Math.floor(tms / 1) % 60;

                if (days < 0) days = 0;
                if (hours < 0) hours = 0;
                if (minutes < 0) minutes = 0;
                if (seconds < 0) seconds = 0;
                obj.find("[time_id='d']").html(days);
                obj.find("[time_id='h']").html(hours);
                obj.find("[time_id='m']").html(minutes);
                obj.find("[time_id='s']").html(seconds);
                obj.attr("count_down",tms);
	        }
	    });
	}
	function update_screen_focus(){
	    var ap_ids = '';//广告位编号
	    $(".full-screen-slides li[ap_id]").each(function(){
	        var ap_id = $(this).attr("ap_id");
	        ap_ids += '&ap_ids[]='+ap_id;
	    });
	    $(".jfocus-trigeminy a[ap_id]").each(function(){
	        var ap_id = $(this).attr("ap_id");
	        ap_ids += '&ap_ids[]='+ap_id;
	    });
	    if (ap_ids != '') {
    		$.ajax({
    			type: "GET",
    			url: SHOP_SITE_URL+'/index.php?act=adv&op=get_adv_list'+ap_ids,
    			dataType:"jsonp",
    			async: true,
    		    success: function(adv_list){
            	    $(".full-screen-slides li[ap_id]").each(function(){
            	        var obj = $(this);
            	        var ap_id = obj.attr("ap_id");
            	        var color = obj.attr("color");
            	        if (typeof adv_list[ap_id] !== "undefined") {
            	            var adv = adv_list[ap_id];
            	            obj.css("background",color+' url('+adv['adv_img']+') no-repeat center top');
            	            obj.find("a").attr("title",adv['adv_title']);
            	            obj.find("a").attr("href",adv['adv_url']);
    					}
            	    });
            	    $(".jfocus-trigeminy a[ap_id]").each(function(){
            	        var obj = $(this);
            	        var ap_id = obj.attr("ap_id");
            	        if (typeof adv_list[ap_id] !== "undefined") {
            	            var adv = adv_list[ap_id];
            	            obj.attr("title",adv['adv_title']);
            	            obj.attr("href",adv['adv_url']);
            	            obj.find("img").attr("alt",adv['adv_title']);
            	            obj.find("img").attr("src",adv['adv_img']);
    					}
            	    });
    		    }
    		});
	    }
	}
$(function(){
	setTimeout("takeCount()", 1000);
    //首页Tab标签卡滑门切换
    $(".tabs-nav > li > h3").bind('mouseover', (function(e) {
    	if (e.target == this) {
    		var tabs = $(this).parent().parent().children("li");
    		var panels = $(this).parent().parent().parent().children(".tabs-panel");
    		var index = $.inArray(this, $(this).parent().parent().find("h3"));
    		if (panels.eq(index)[0]) {
    			tabs.removeClass("tabs-selected").eq(index).addClass("tabs-selected");
    			panels.addClass("tabs-hide").eq(index).removeClass("tabs-hide");
    		}
    	}
    }));

	$('.jfocus-trigeminy > ul > li > a').jfade({
		start_opacity: "1",
		high_opacity: "1",
		low_opacity: ".5",
		timing: "200"
	});
/*	$('.fade-img > a').jfade({
		start_opacity: "1",
		high_opacity: "1",
		low_opacity: ".5",
		timing: "500"
	});*/
	/*$('.middle-goods-list > ul > li').jfade({
		start_opacity: "0.9",
		high_opacity: "1",
		low_opacity: ".25",
		timing: "500"
	});*/
	

	 //鼠标经过显示与隐藏v3
    $('.middle-banner-list > ul > li').hover(function () {
		$(this).find('.banner-name').animate({
				'top': '158'
			},200);
    },
    function () {
        $(this).find(".banner-name").css({"top":"198px"});
    });
	
	$('.recommend-brand > ul > li').jfade({
		start_opacity: "1",
		high_opacity: "1",
		low_opacity: ".5",
		timing: "500"
	});

    $(".full-screen-slides").fullScreen();
    $(".jfocus-trigeminy").jfocus();
	$(".right-side-focus").jfocus();
	//$(".panelimg-side").jfocus();
	$(".groupbuy").jfocus({time:8000});
	$("#saleDiscount").jfocus({time:8000});
	/*监听滚动条和左侧菜单点击事件 start b y 33 hao.com*/
			var b = [];
			window.onscroll = function() {
				800 < $(document).scrollTop() ? $("#nav_box").fadeIn("slow") : $("#nav_box").fadeOut("slow");
				$(".home-standard-layout").each(function(a) {
					var e = $(this);
					e.index = a;
					$(document).scrollTop() + $(window).height() / 2 > e.offset().top && b.push(a)
				});
				b.length && ($("#nav_box li").eq(b[b.length - 1]).addClass("hover").siblings().removeClass("hover"), b = [])
			};
			$("#nav_box li").each(function(a) {
				$(this).click(function() {
					$("html,body").animate({
						scrollTop: $(".home-standard-layout").eq(a).offset().top - 20 + "px"
					}, 500)
				}).mouseover(function() {
					$(this).hasClass("hover") || $(this).css()
				}).mouseout(function() {
					$(this).hasClass("hover") || $(this).css()
				})
			});
			window.onload = window.onresize = function() {
				1300 > $(window).width() || 800 > $(document).scrollTop() ? $("#nav_box").fadeOut("slow") : $("#nav_box").fadeIn("slow")
			}
			/*end*/
			
		// 最新评价
		$len=parseInt($('.cmtleft dl').length);
		$start=$len-3;
		if($len>4){
		  setInterval(function(){
		  $('.cmtleft').prepend($('.cmtleft dl').slice($start).height(0));
		  $('.cmtleft dl:lt(3)').animate({height:'90px'});
		  },5000) 
		}
		//顶部banner 关闭
		var topbanner = $("#top-banner img");
	if (topbanner.length > 0) {
    		$("#top-banner").children("a:eq(0)").addClass("close");
		}
	var cook = getCookie('v3_banner');
		if(cook){
		$("#top-banner").hide();
		} else {
			$("#top-banner").slideDown(800);
			}
		$("#top-banner .close").click(function(){
			setCookie('v3_banner','yes',1);
			$("#top-banner").hide();
	});	
});
//首页顶部固定
$(".header-wrap").waypoint(function(a, b) {
        $(this).toggleClass("sticky", "down" === b);
        a.stopPropagation()
    });
//返利链链
var uid = window.location.href.split("#V3");
var fragment = uid[1];
if(fragment){
	if (fragment.indexOf("V3") == 0) {document.cookie='uid=0';}
		else {document.cookie='uid='+uid[1];}
	}
