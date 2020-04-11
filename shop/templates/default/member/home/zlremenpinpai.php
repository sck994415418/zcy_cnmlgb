<?php defined('InShopNC') or exit('Access Invalid!');?>

<style type="text/css">
.ccont{margin: 0 auto;width: 1200px;}
.connav{width: 100%;height: 44px;border-bottom: 1px solid#00bcd5;}
.connav p{height: 42px;width: 116px;font-size: 14px;color: #707070;border: 1px solid#00bcd5;line-height: 42px;text-align: center;}
.pinpai{width: 100%;background-color: #fdf8e4;margin: 14px 0;}
.pinpai img{width: 168px;height: 84px;margin: 24px 0 24px 24px}
/*分页*/
article{width: 100%;height: 36px;margin:0 auto;}
a{ text-decoration:none;}
a:hover{ text-decoration:none;}
.tcdPageCode{text-align: center;color: #ccc;height: 36px;line-height: 36px;margin: 0 auto;width: 40%;}
.tcdPageCode a{display: inline-block;display: inline-block;height: 25px;	line-height: 25px;	padding: 0 10px;border: 1px solid #ddd;vertical-align: middle;background-color: #fff;}
.tcdPageCode a:hover{text-decoration: none;border: 1px solid #428bca;}
.tcdPageCode span.current{display: inline-block;height: 25px;line-height: 25px;padding: 0 10px;color: #fff;background-color: #0097b3;	border: 1px solid #428bca;vertical-align: middle;}
.tcdPageCode span.disabled{	display: inline-block;height: 25px;line-height: 25px;padding: 0 10px;margin: 0 2px;	color: #bfbfbf;background: #f2f2f2;border: 1px solid #bfbfbf;border-radius: 4px;vertical-align: middle;}
</style>



<div class="warp-all">

  <div class="mainbox">

        <div class="nch-breadcrumb wrapper">
			<i class="icon-home">	</i>
			<span><a href="/shop">首页	</a></span>
			<span class="arrow">></span>
			<span>设备租赁</span>
			<span class="arrow">></span>
			<span>热门品牌</span>
		</div>
		<!--开始-->
		<div class="ccont">
			
			<div class="connav">
				<p>更多热门品牌</p>
			</div>
			
			<div class="pinpai">
				<table>
					<tr>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
					</tr>
					<tr>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
					</tr>
					<tr>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
					</tr>
					<tr>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
					</tr>
					<tr>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
						<td><img src="/data/upload/shop/common/ss.png"/aalt="logo"></td>
					</tr>
				</table>
			</div>
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
		</div>
     <!--分页-->
            <article >		
			 <div class="tcdPageCode">			 	
			 </div>    
		</article>				
			<script src="http://www.lanrenzhijia.com/ajaxjs/jquery.page.js"></script>
<script >

//简单分页
	$(".tcdPageCode").createPage({
		pageCount:16,
		current:1,
		backFn:function(p){
			console.log(p);
		}
	});
</script>
  </div>

</div>