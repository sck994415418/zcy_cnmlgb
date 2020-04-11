<?php defined('InShopNC') or exit('Access Invalid!'); ?>
<style type="text/css">
body{font-family:"微软雅黑";font-size:16px}.clear{zoom:1}
.clear:after{content:"";clear:both;display:block}
.content-div{margin-top:20px;padding-left:10%}
.mui-input-row input{border:0;outline:0;background:transparent;margin-left:6%;font-family:"微软雅黑"}
.mui-input-row{border:1px solid #73e4f4;font-size:16px;padding:18px}
.border-t-no{
	border-top: none;
}
.mui-input-row label{color:#212121}
button{
	border:0;outline:0;background:#73e4f4;font-size:18px;
	color: #fff;
	display: block;
	width: 40%;
	padding: 20px;
	margin: 30px auto;
}
.mui-center{margin: 0 auto;text-align: center;}


</style>
<div class="warp-all">
	<div class="mainbox">
		<div class="nch-breadcrumb wrapper">
			<i class="icon-home">
			</i>
			<span>
				<a href="http://b2bc.zm-y.com/shop">
					首页
				</a></span>
			<span class="arrow">></span>
			<span>设备维修</span>
			<span class="arrow">></span>
			<span>支付</span>
		</div>
		<div class="content-div">	
			   
			     <div class="mui-input-row">
			        <label>支付方式</label>			        
			    </div>
			     <div class="mui-input-row border-t-no">
			        <input type="radio" name="radio" id="radio" value="支付宝" />
			        <label>支付宝</label>			        
			    </div>
			      <div class="mui-input-row border-t-no">
			        <input type="radio" name="radio" id="radio" value="微信" />
			        <label>微信</label>			        
			    </div>
			    <!-- <div class="mui-input-row border-t-no">
			        <input type="radio" name="radio" id="radio" value="银行卡" />
			        <label>银行卡</label>			        
			    </div> -->
			     
			   
			    <div class="mui-center">
			    	<button class="sub">确认支付</button><button class="sub">线下支付</button>
			    </div>
				<div class="mui-service">
					服务说明：本公司支持终身服务，您的一键点击我们为您解决您的后顾之忧！
				</div>
		</div>
		