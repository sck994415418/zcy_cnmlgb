<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8" />
  <title>手机接口示例</title>
</head>
<body  style="padding:20px;">
  <input type="button" id="js" value="go">
  <script type="text/javascript" >
  $(function(){
    $("#js").click(function(){
      $.ajax({
        type:"POST",
        url:"index.php",
        dataType: 'json',
        data:{'act':'mobile','op':'register','username':'bdbb','password':'bbb','password_confirm':'bbb','email':'bbb','client':'bbb'},
        success:function(data){
          alert(data.msg);
        },
        error:function(XMLHttpRequest,textStatus,errorThrown){
            alert(XMLHttpRequest.status);
            alert(XMLHttpRequest.readyState);
            alert(errorThrown);
        }
      });
      return false;
    })
  });

  </script> 
  <h1 style="font-size:22px;weight:bold;">接口示例</h1>
  2.1.<a href="index.php?act=mobile&op=brand" target="_blank" title="">获取推荐品牌</a><br>
  2.2.<a href="index.php?act=mobile&op=goodsclass" target="_blank" title="">获取分类</a><br>
  2.3.<a href="index.php?act=mobile&op=goodsdetail&goods_id=100042" target="_blank" title="">商品详情</a><br>
  2.4.购物车列表<br>
    <form action="index.php?act=mobile&op=cartlist" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="submit" value="GO">
    </form><br>
  2.5.购物车添加<br>
    <form action="index.php?act=mobile&op=cartadd" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="goods_id" value="" placeholder="商品id">
      <input type="text" name="quantity" value="" placeholder="数量">
      <input type="submit" value="GO">
    </form><br>
  2.6.购物车删除<br>
    <form action="index.php?act=mobile&op=cartdel" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="cart_id" value="" placeholder="购物车id">
      <input type="submit" value="GO">
    </form><br>
  2.7.购物车数量修改<br>
    <form action="index.php?act=mobile&op=cartbuynum" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="cart_id" value="" placeholder="购物车id">
      <input type="text" name="quantity" value="" placeholder="数量">
      <input type="submit" value="GO">
    </form><br>
  2.8.查询购物车数量<br>
    <form action="index.php?act=mobile&op=cartcount" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="submit" value="GO">
    </form><br>
  2.9.发票列表<br>
    <form action="index.php?act=mobile&op=invoicelist" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="submit" value="GO">
    </form><br>
  2.10.发票删除<br>
    <form action="index.php?act=mobile&op=invoicedel" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="inv_id" value="" placeholder="发票id">
      <input type="submit" value="GO">
    </form><br>
  2.11.发票信息添加<br>
    <form action="index.php?act=mobile&op=invoiceadd" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="inv_title_select" value="" placeholder="发票类型">
      <input type="text" name="inv_title" value="" placeholder="发票抬头">
      <input type="text" name="inv_content" value="" placeholder="发票内容">
      <input type="submit" value="GO">
    </form><br>
  2.12.<a href="index.php?act=mobile&op=invoice_content_list" target="_blank" title="">发票信息列表</a><br>
  2.13.商品收藏添加<br>
    <form action="index.php?act=mobile&op=addgoodscollect" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="goods_id" value="" placeholder="商品id">
      <input type="submit" value="GO">
    </form><br>
  2.13.商品收藏删除<br>
    <form action="index.php?act=mobile&op=delgoodscollect" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="fav_id" value="" placeholder="收藏id">
      <input type="submit" value="GO">
    </form><br>
  2.13.商品收藏详情<br>
    <form action="index.php?act=mobile&op=infogoodscollect" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="fav_id" value="" placeholder="收藏id">
      <input type="submit" value="GO">
    </form><br>
  2.14.店铺收藏添加<br>
    <form action="index.php?act=mobile&op=addstorecollect" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="store_id" value="" placeholder="店铺id">
      <input type="submit" value="GO">
    </form><br>
  2.15.店铺收藏删除<br>
    <form action="index.php?act=mobile&op=delstorecollect" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="store_id" value="" placeholder="店铺id">
      <input type="submit" value="GO">
    </form><br>
  2.16.购买第一步<br>
    <form action="index.php?act=mobile&op=buystep1" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="cart_id" value="" placeholder="购买参数">
      <input type="text" name="ifcart" value="" placeholder="购物车购买标志">
      <input type="text" name="address_id" value="" placeholder="地址id">
      <input type="submit" value="GO">
    </form><br>
  2.16.支付<br>
    <form action="index.php?act=mobile&op=pay" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="pay_sn" value="" placeholder="订单编号">
      <input type="submit" value="GO">
    </form><br>
  2.17.<a href="index.php?act=mobile&op=goodslist" target="_blank" title="">商品列表</a><br>
  2.18.验证支付密码<br>
    <form action="index.php?act=mobile&op=check_pd_pwd" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="password" value="" placeholder="支付密码">
      <input type="submit" value="GO">
    </form><br>
  2.19.验证密码<br>
    <form action="index.php?act=mobile&op=checkpassword" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="password" value="" placeholder="支付密码">
      <input type="submit" value="GO">
    </form><br>
  2.19.更换收货地址<br>
    <form action="index.php?act=mobile&op=changeaddress" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="city_id" value="" placeholder="市级id">
      <input type="text" name="area_id" value="" placeholder="地区id">
      <input type="text" name="freight_hash" value="" placeholder="运费">
      <input type="submit" value="GO">
    </form><br>
  2.20.<a href="index.php?act=mobile&op=apk_version" target="_blank" title="">Android版本检查</a><br>
  2.21.购买第二步<br>
    <form action="index.php?act=mobile&op=buystep1" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="pd_pay" value="" placeholder="是否使用预存款支付 1-使用 0-不使用">
      <input type="text" name="password" value="" placeholder="用户支付密码">
      <input type="text" name="fcode" value="" placeholder="F码">
      <input type="text" name="voucher" value="" placeholder="代金券，内容以竖线分割">
      <input type="text" name="invoice_id" value="" placeholder="发票信息编号">
      <input type="text" name="pay_name" value="" placeholder="付款方式，可选值">
      <input type="text" name="offpay_hash_batch" value="" placeholder="店铺是否支持货到付款hash">
      <input type="text" name="offpay_hash" value="" placeholder="是否支持货到付款hash，通过更换收货地址接口获得">
      <input type="text" name="vat_hash" value="" placeholder="发票信息hash，第一步接口提供">
      <input type="text" name="cart_id" value="" placeholder="购买参数">
      <input type="text" name="ifcart" value="" placeholder="购物车购买标志">
      <input type="submit" value="GO">
    </form><br>
  <hr>
  1.1.<a href="index.php?act=mobile&op=hotgoods" target="_blank" title="">获取热卖商品</a><br>
  1.2.登录接口<br>
    <form action="index.php?act=mobile&op=login" method="post" accept-charset="utf-8">
      <input type="text" name="username" value="" placeholder="用户名">
      <input type="text" name="password" value="" placeholder="密码">
      <input type="text" name="client" value="" placeholder="客户端类型">
      <input type="submit" value="GO">
    </form><br>
  1.3.注册接口<br>
    <form action="index.php?act=mobile&op=register" method="post" accept-charset="utf-8">
      <input type="text" name="username" value="" placeholder="用户名">
      <input type="text" name="password" value="" placeholder="密码">
      <input type="text" name="password_confirm" value="" placeholder="确认密码">
      <input type="text" name="email" value="" placeholder="邮箱">
      <input type="text" name="client" value="" placeholder="客户端类型">
      <input type="submit" value="GO">
    </form><br>
  1.4.注销接口<br>
    <form action="index.php?act=mobile&op=logout" method="post" accept-charset="utf-8">
      <input type="text" name="username" value="" placeholder="用户名">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="client" value="" placeholder="客户端类型">
      <input type="submit" value="GO">
    </form><br>
  1.5.我的商城<br>
    <form action="index.php?act=mobile&op=ourinfo" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="submit" value="GO">
    </form><br>
  1.6.商品收藏<br>
    <form action="index.php?act=mobile&op=goodscollect" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="submit" value="GO">
    </form><br>
  1.7.店铺收藏<br>
    <form action="index.php?act=mobile&op=storecollect" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="submit" value="GO">
    </form><br>
  1.8.我的足迹<br>
    <form action="index.php?act=mobile&op=browse" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="submit" value="GO">
    </form><br>
  1.9.实物订单列表<br>
    <form action="index.php?act=mobile&op=matterorder" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="submit" value="GO">
    </form><br>
  1.10.虚拟订单列表<br>
    <form action="index.php?act=mobile&op=virtualorder" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="submit" value="GO">
    </form><br>
  1.11.实物订单详细<br>
    <form action="index.php?act=mobile&op=matterdetail" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="order_id" value="" placeholder="订单id">
      <input type="submit" value="GO">
    </form><br>
  1.12.虚拟订单详细<br>
    <form action="index.php?act=mobile&op=virtualdetail" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="order_id" value="" placeholder="订单id">
      <input type="submit" value="GO">
    </form><br>
  1.13.代金券<br>
    <form action="index.php?act=mobile&op=voucher" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="submit" value="GO">
    </form><br>
  1.14.充值卡<br>
    <form action="index.php?act=mobile&op=rcb" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="submit" value="GO">
    </form><br>
  1.15.预存款：账户余额<br>
    <form action="index.php?act=mobile&op=pdlog" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="submit" value="GO">
    </form><br>
  1.16.预存款：充值明细<br>
    <form action="index.php?act=mobile&op=pdrecharge" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="submit" value="GO">
    </form><br>
  1.17.预存款：余额提现<br>
    <form action="index.php?act=mobile&op=pdcash" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="submit" value="GO">
    </form><br>
  1.18.积分日志<br>
    <form action="index.php?act=mobile&op=pointslog" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="submit" value="GO">
    </form><br>
  1.19.地址信息列表<br>
    <form action="index.php?act=mobile&op=address" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="submit" value="GO">
    </form><br>
  1.20.地址添加<br>
    <form action="index.php?act=mobile&op=incaddress" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="true_name" value="" placeholder="收货人姓名">
      <input type="text" name="mob_phone" value="" placeholder="联系手机">
      <input type="text" name="area_info" value="" placeholder="地区">
      <input type="text" name="address" value="" placeholder="详细地址">
      <input type="text" name="is_default" value="" placeholder="是否默认">
      <input type="submit" value="GO">
    </form><br>
  1.21.地址编辑<br>
    <form action="index.php?act=mobile&op=editaddress" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="true_name" value="" placeholder="收货人姓名">
      <input type="text" name="mob_phone" value="" placeholder="联系手机">
      <input type="text" name="area_info" value="" placeholder="地区">
      <input type="text" name="address" value="" placeholder="详细地址">
      <input type="text" name="is_default" value="" placeholder="是否默认">
      <input type="text" name="address_id" value="" placeholder="地址id">
      <input type="submit" value="GO">
    </form><br>
  1.21.地址删除<br>
    <form action="index.php?act=mobile&op=deladdress" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="address_id" value="" placeholder="地址id">
      <input type="submit" value="GO">
    </form><br>
  1.22.地址详细<br>
    <form action="index.php?act=mobile&op=infoadress" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="address_id" value="" placeholder="地址id">
      <input type="submit" value="GO">
    </form><br>
  1.23.地区列表<br>
    <form action="index.php?act=mobile&op=arealist" method="post" accept-charset="utf-8">
      <input type="text" name="area_id" value="" placeholder="地区id">
      <input type="submit" value="GO">
    </form><br>
  1.23.订单取消<br>
    <form action="index.php?act=mobile&op=ordercancel" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="order_id" value="" placeholder="订单id">
      <input type="submit" value="GO">
    </form><br>
  1.24.订单确认收货<br>
    <form action="index.php?act=mobile&op=orderreceive" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="order_id" value="" placeholder="订单id">
      <input type="submit" value="GO">
    </form><br>
  1.24.订单物流跟踪<br>
    <form action="index.php?act=mobile&op=searchdeliver" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="order_id" value="" placeholder="订单id">
      <input type="submit" value="GO">
    </form><br>
  1.25.虚拟订单取消<br>
    <form action="index.php?act=mobile&op=vrordercancel" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="order_id" value="" placeholder="订单id">
      <input type="submit" value="GO">
    </form><br>
  1.26.发送兑换码到手机<br>
    <form action="index.php?act=mobile&op=resend" method="post" accept-charset="utf-8">
      <input type="text" name="key" value="" placeholder="令牌">
      <input type="text" name="order_id" value="" placeholder="订单id">
      <input type="text" name="buyer_phone" value="" placeholder="电话号">
      <input type="submit" value="GO">
    </form><br>
</body>
</html>