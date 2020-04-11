<?php defined('InShopNC') or exit('Access Invalid!');?>

<style type="text/css">

.ccont{margin: 0 auto;width: 1200px;}
.titlenav{font-size: 20px; width: 100%;height: 84px;
/*background-image: url(http://www.nrwspt.com/data/upload/shop/common/wxpjbg.png);background-repeat: no-repeat;
background-position:0 17px;*/
}
.titlenav p{margin-left: 100px;line-height: 84px; border:1px solid#00a9be;height: 54px;width: 164px;line-height: 54px;text-align: center;
margin-top: 10px;}
.pjnr{width: 100%;height: 360px;}
.pjnr textarea{width: 1000px;margin: 0 100px;height: 360px;border-radius: 10px;border: 2px solid#eee;}
.ffdj{width: 100%;}

/*星星*/
.star_ul{ list-style-type: none;padding-left: 100px;}
.star_ul li{float:left;}
#mydiv2{height: 100px;}
/*星星结束*/
.zhushi p{margin-left: 100px;font-size: 14px;color: #fe0000;height: 60px;}
.divbtn{height: 100px;}
.divbtn input{ background-color: #00a9be;color: #fff;border: 0;width: 446px;height: 62px;font-size:34px;margin-left: 600px;line-height: 62px;}
/*遮幕*/
#zhemu {	display: none;	z-index: 9999;	_position: absolute;	filter: alpha(opacity=40);	-moz-opacity: 0.8;	background-color: #000;
	opacity: 0.5;	float: left !important;	top: 0;	left: 0;	right: 0;	bottom: 0;	position: fixed;	width: 100%;
	height: 100%;}
.alert {	display: none;	z-index: 99999;	background-color: #fff;	float: left;
	position: absolute;	font-size: 0.34rem;	top: 50%;	left: 50%;	margin-top: -97px;	margin-left: -160px;
	width: 320px;	height: 192px;}
.alert input {	line-height: 34px;	height: 34px;	width: 80px;}
.alert table {	text-align: center;	width: 100%;}
.alert input {	color: #b5b5b5;	background-color: #fff;	font-size: 14px;	border: 0;}
#tr{	background-color: #fff;	font-size: 14px;	border: 0;	color: #00bdd3;	margin-left: 154px;}
.alert td {	width: 100%;	height: 28px;	border-top: 2px solid#f3f3f3;	font-size: 16px;	color: #cbcbcb;	z-index=-1;}
.alert td:active,.alert td:hover{color:#00bcd5 ;font-weight: bold;cursor: hand;}
</style>
<script type="text/javascript">
        var isclick = false;
        function change(mydivid,num) {
            if (!isclick) {
                var tds = $("#"+mydivid+" ul li");
                for (var i = 0; i < num; i++) {
                    var td = tds[i];
                    $(td).find("img").attr("src","http://www.nrwspt.com/data/upload/shop/common/star_full.png");
                }
                var tindex = $("#"+mydivid).attr("currentIndex");
                tindex = tindex==0?0:tindex+1;
                for (var j = num; j < tindex; j++) {
                    var td = tds[j];
                    $(td).find("img").attr("src","http://www.nrwspt.com/data/upload/shop/common/star_empty.png");
                }
                $("#"+mydivid).attr("currentIndex",num);
                $("#start").val(num);
            }
        }
        function repeal(mydivid,num) {
            if (!isclick) {
                var tds = $("#"+mydivid+" ul li");
                var tindex = $("#"+mydivid).attr("currentIndex");
                tindex = tindex==0?0:tindex+1;
                for (var i = tindex; i < num; i++) {
                    var td = tds[i];
                    $(td).find("img").attr("src","http://www.nrwspt.com/data/upload/shop/common/star_empty.png");
                }
                $("#"+mydivid).attr("currentIndex",num);
                $("#start").val(num);
            }
        }
        function change1(mydivid,num) {
            if (!isclick) {
                change(mydivid,num);

            }
            else {
                alert("Sorry,You had clicked me!");
            }
        }
        $(function(){
            initEvent('mydiv2');
        });
        function initEvent(mydivid) {
            //var tableWjx =$("#tableWjx ul li");
             //var items= tableWjx.getElementsByTagName("ul");

            var tds = $("#"+mydivid+" ul li");
            for (var i = 0; i < tds.length; i++) {
                var td = tds[i];
                $(td).live('mouseover',function(){var num = $(this).attr("num");change(mydivid,num);});
                $(td).live('mouseout',function(){var num = $(this).attr("num");repeal(mydivid,num);});
                $(td).live('click',function(){var num = $(this).attr("num");change1(mydivid,num);});
            }
        }
        /*提交*/

   function do_submit(){
   		var text = $("#texAr").val();
   		if(!text){
   			alert('评价内容不能为空');
   			return false;
   		}
   		return true;
   }
    </script>

<div class="warp-all">

  <div class="mainbox">
    <div class="nch-breadcrumb wrapper">
			<i class="icon-home">	</i>
			<span><a href="http://b2bc.zm-y.com/shop">首页	</a></span>
			<span class="arrow">></span>
			<span>我的订单</span>
			<span class="arrow">></span>
			<span>维修详情</span>
		</div>
		<!--头部结束-->
		<div class="ccont">
			<form method="post" onsubmit="return do_submit();">
				<input type="hidden" name="act" value="aa">
				<input type="hidden" name="op" value="wxpj">
				<input type="hidden" name="action" value="wxpj">
				<input type="hidden" name="start" id="start" value="0">
				<div class="titlenav">
					<p>维修评价</p>
				</div>			
				<div class="pjnr">
					<textarea id="texAr" name='content'></textarea>
			    </div>
				<div class="titlenav">
					<p>服务等级</p>
				</div>	
				<div class="ffdj">
					<div id="mydiv2" currentIndex="0">
							<ul class="star_ul">
								<li num="1"><img src="http://www.nrwspt.com/data/upload/shop/common/star_empty.png" /></li>
								<li num="2"><img src="http://www.nrwspt.com/data/upload/shop/common/star_empty.png" /></li>
								<li num="3"><img src="http://www.nrwspt.com/data/upload/shop/common/star_empty.png" /></li>
								<li num="4"><img src="http://www.nrwspt.com/data/upload/shop/common/star_empty.png" /></li>
								<li num="5"><img src="http://www.nrwspt.com/data/upload/shop/common/star_empty.png" /></li>
							</ul>
						</div>
				</div>
				<div class="zhushi">
					<p>注：如果您有其他问题，请及时拨打0311-524694</p>
				</div>
				
				<div class="divbtn">
					<input id="subt" type="submit" value="提交" />
				</div>
				
			</form>
		</div>
		

  </div>
  
  
  
  
  <!--遮幕-->
	<!--弹窗-->
	<!--<div id="zhemu" class="zhemu">
	</div>
	<div id="alert" class="alert">
		<input id="close" type="button"value="取消"/>
		<input id="tr" type="button"value="确定">
		<table cellpadding="0"cellspacing="0">
			<tr>
				<td colspan="2">
					5
				</td>
			</tr>
			<tr>
				<td colspan="2" >
					10
				</td>
			</tr>
			<tr>
				<td colspan="2">
					20
				</td>
			</tr>
			<tr>
				<td colspan="2">
					25
				</td>
			</tr>
			<tr>
				<td colspan="2">
					30
				</td>
			</tr>
		</table>
	</div>-->

</div>