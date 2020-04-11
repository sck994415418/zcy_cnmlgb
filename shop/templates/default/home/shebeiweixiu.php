<?php defined('InShopNC') or exit('Access Invalid!'); ?>
<style type="text/css">
	body{font-family:"微软雅黑";font-size:16px}.clear{zoom:1}.clear:after{content:"";clear:both;display:block}.content-div{margin-top:20px;padding-left:10%}.mui-input-row input{border:0;outline:0;background:transparent;margin-left:6%;width:70%;font-family:"微软雅黑"}.mui-input-row{border:1px solid #73e4f4;font-size:16px;padding:18px}.mui-input-row label{color:#212121}.border-t-no{border-top:0}.money{color:#fe4040}.mui-center{margin:0 auto;text-align:center}.sub{background:#73e4f4;color:#fff;border:0;outline:0;width:30%;text-align:center;margin:40px auto;padding:18px;font-size:20px}.mui-input-radio{position:relative;width:100%;text-align:right;font-size:16px;margin-top:30px;height:30px;line-height:30px}.mui-input-radio-div{position:relative;display:inline}.mui-input-radio span,.mui-input-radio label{margin-top:-4px;display:inline-block;height:30px;line-height:30px}input[type=radio]{width:20px;height:20px;position:absolute;left:-24px;top:2px}input[type=radio]:checked:before{color:red!important}.mui-service{color:#7c7c7c;margin-top:20px;margin-bottom:40px;font-size:15px}#zhemu{display:none;z-index:9999;position:absolute;filter:alpha(opacity=40);-moz-opacity:.8;background-color:#000;opacity:.5;float:left!important;top:0;left:0;right:0;bottom:0;position:fixed;width:100%;height:100%}
	#tc{z-index:99999;top:40%;display:none;left:40%;right:60%;bottom:60%;position:fixed;width:300px;background-color:#fff!important;height:266px}
	#tc li{height:40px;border-bottom:1px solid#eee;line-height:40px;overflow:hidden;color:#bbb}
	.tc li:first-child{padding-top:20px;padding-left:10px;padding-right:10px;transition:500ms}
	.tc button{border:0;outline:0;background:transparent;font-size:20px}
	.tc li button:first-child{text-align:left;float:left;color:#bbb}
	.tc li button:last-child{text-align:right;float:right;color:#00bcd5}
	.tc li:hover{font-size:18px;color:#212121!important;cursor:pointer}
	.body-chose-active{color:#73e4f4!important;font-size:20px}
	#payway{
		z-index:99999;top:40%;left:40%;right:60%;bottom:60%;position:fixed;width:300px;background-color:#fff!important;
		height: 200px;display: none;border-radius: 10px;
		}
		#payway li{
			height: 40px;
			line-height: 40px;
			text-align: center;
			font-size: 22px;
		}
	.quit{
		float: right;
		margin-right: 20px;
		border: none;
		outline: none;
		margin-top: 10px;
		border:0;outline:0;background:transparent;font-size:18px
	}
	.inmmate,.outline{
		border:0;outline:0;background:#73e4f4;font-size:18px;
		color: #fff;
	}
		.inmmate{
			float: left;
			margin-left: 10px;
			padding: 10px 20px;
			margin-top: 20px;
			border-radius: 10px;
		}
		.outline{
			float: right;
			margin-right: 10px;
			margin-top: 20px;
			padding: 10px 20px;
			border-radius: 10px;
		}
	.now{
		border: 1px solid#73e4f4;
		width: 100px;margin-right: 30px;
		padding: 4px 10px;;
	}
	.nowactive{
		background: #73e4f4;
		color: #fff;padding: 4px 10px;margin-right:60px;
	}
	.ytime{
		display: inline;
	}
</style>
<div class="warp-all">
	<div class="mainbox">
		<div class="nch-breadcrumb wrapper">
			<i class="icon-home">
			</i>
			<span>
				<a href="http://www.nrwspt.com">
					首页
				</a></span>
			<span class="arrow">></span>
			<span>设备维修</span>
		</div>
		<form action="index.php?act=aa&op=index" method="post" accept-charset="utf-8">
			<input type="hidden" name="action" value="sbwx">
			<div class="content-div">	
			    <div class="mui-input-row ">
		     		<label id="now" class="now">现在维修</label>	
		     		<div class="ytime">
			     		<label>预约时间</label>
			        	<input class="oInput"  type="text" name="apptime" placeholder="输入预约时间"style="width: 40%;">			        
		     		</div>
			    </div>
			    <div class="mui-input-row border-t-no">
			        <label>我的位置</label>
			        <input class="oInput" type="text" name="address" placeholder="输入您所在的位置" required>
			    </div>
			      <div class="mui-input-row border-t-no">
			        <label>物品类型</label>
			        <input class="oInput" type="text" name="type" placeholder="请输入您要维修的设备" required>
			    </div>
			     <div class="mui-input-row border-t-no">
			        <label>联系电话</label>
			        <input class="oInput" type="text" name="tel" placeholder="输入联系电话" required>
			    </div>
			    <div class="mui-input-radio">
			    	<div class="mui-input-radio-div">
			    		<input type="hidden" name="xiaofei" id="xiaofei" value="0">
			    		<input type="hidden" name="scost" id="scost" value="<?php echo $output['scost']; ?>">
			    		<input type="radio" name="radio" id="radio" value="加小费" /><label>加小费</label>
			    		<label>上门服务费</label><span class="money" id="zong">￥<?php echo $output['scost']; ?></span>元
			    	</div>
			    </div>
			    <div class="mui-center">
			    	<button class="sub">提交订单</button>
			    </div>
				<div class="mui-service">
					服务说明：本公司支持终身服务，您的一键点击我们为您解决您的后顾之忧！
				</div>
			</div>
		</form>
		<div id="zhemu" class="zhemu"></div>
		<div id="tc" class="tc">
			<ul>
				<li class="clear ">
					<button id="close">取消</button>
					<button id="sure">确定</button>
				</li>
				<li class="oli">20元</li>
				<li  class="oli">30元</li>
				<li  class="oli">40元</li>
				<li  class="oli">50元</li>
				<li  class="oli">60元</li>
			</ul>
		</div>
		<div id="payway" class="payway">
			<ul>
				<li class="clear" ><button id="quit" class="quit">取消</button></li>	
				<li>提交订单成功</li>			
				<li class="clear"><button class="inmmate" id="inmmate">立即支付</button><button class="outline"  id="outline">线下支付</button></li>
			</ul>
		</div>
	</div>
</div>
<script type="text/javascript">
	var oRadio=document.getElementById('radio');//选中框
	var oZhemu=document.getElementById('zhemu');//遮幕
	var oTc=document.getElementById('tc');//消费提示
	var oBtn=document.getElementById('close');//取消按钮
	var oSure=document.getElementById('sure');//弹出框确定按钮
	var oLi=document.getElementsByClassName('oli');//钱
	var oInput=document.getElementsByClassName('oInput');	//获取输入框
	var oSub=document.getElementsByClassName('sub');//提交
	var payway=document.getElementById('payway')//支付方式选择
	var quit=document.getElementById('quit')//支付取消
	var oInmmate=document.getElementById('inmmate')//立即支付
	var Outline=document.getElementById('outline')//线下支付
	var phoneReg = /^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/; //电话正则
	var moneypay=null;//初始化
	var oNow=document.getElementById('now');//现在维修
	var ytime=document.getElementsByClassName('ytime');
	var count=1;
	oNow.onclick=function(){
			if(count=='1'){
				this.className='nowactive';
				count=0;
				ytime[0].style.display='none'
			}	else{
				this.className='now';
				count=1;
				ytime[0].style.display='inline'
			}
	}
	
	
	
	
	oRadio.onclick=function(){
		oZhemu.style.display='block';
		oTc.style.display='block';
	}
	//取消按钮
	oBtn.onclick=function(){
		oZhemu.style.display='none';
		oTc.style.display='none';
		oRadio.checked=false;
	}
	//选中钱	
	for(var a = 0; a < oLi.length; a++) {				
		oLi[a].onclick= function() {
			for(var i = 0; i < oLi.length; i++) {
				oLi[i].className = 'oli';//一定要先清楚所有的class属性
			}
			this.className = 'oli body-chose-active';
			// alert('您将额外支付小费'+this.innerHTML);
			//sessionStorage.setItem('consumption',this.innerHTML)
			moneypay=this.innerHTML;
		}
	}
	//确定按钮
	oSure.onclick=function(){
		if(moneypay!=null){
			oZhemu.style.display='none';
			oTc.style.display='none';
			$("#xiaofei").val(moneypay);
			var num = parseFloat(moneypay)+parseFloat($("#scost").val());
			$("#zong").html('￥'+num);
			alert('您确认要支付额外消费'+moneypay)//弹出支付小费。
		
		}else{
			alert('您还没有确定要支付的额外小费')
		}
		
	}

	//提交按钮
	oSub[0].onclick=function(){
		
		if(!oInput[1].value){
			alert('我的位置不能为空');
			return;
		}
		if(!oInput[2].value){
		alert('物品类型不能为空');
		return;	
		} 
		if(!oInput[3].value){
			alert('联系方式不能为空');
			return ;
		}
		if(oInput[3].value.length != 11) {
			alert('手机号码位数不正确');
			return;
		}
		if(!phoneReg.test(oInput[3].value)){
			alert('手机号格式不正确');
			return ;
		}
		
		payway.style.display='block';
		
		oZhemu.style.display='block'
		
		
	}
	//支付方式的取消按钮
	quit.onclick=function(){
		payway.style.display='none';		
		oZhemu.style.display='none';
	}
	//立即支付
	oInmmate.onclick=function(){
		window.location.href='http://www.nrwspt.com/shop/index.php?act=aa&op=index&te=shebeiweixiupay';
		payway.style.display='none';		
		oZhemu.style.display='none';
	}
	Outline.onclick=function(){
		alert("您选择了线下支付")
		window.location.href='http://www.nrwspt.com/shop/index.php?act=aa&op=index&te=shebeiweixiupay';
		payway.style.display='none';		
		oZhemu.style.display='none';
	}
</script>