<?php defined('InShopNC') or exit('Access Invalid!'); ?>
<link href="<?php echo SHOP_TEMPLATES_URL; ?>/css/layout.css" rel="stylesheet" type="text/css">
<div class="nch-container wrapper">
  <div class="left">
    <div class="nch-module nch-module-style01">
      <div class="title">
        <h3><?php echo $lang['article_article_article_class']; ?></h3>
      </div>
      <div class="content">
        <div class="nch-sidebar-article-class">
          <ul>
            <?php foreach ($output['sub_class_list'] as $k=>$v){?>
            <li><a href="<?php echo urlShop('article', 'article', array('ac_id' => $v['ac_id'])); ?>"><?php echo $v['ac_name']?></a></li>
            <?php } ?>
          </ul>
        </div>
      </div>
    </div>
    <div class="nch-module nch-module-style03">
      <div class="title">
        <h3><?php echo $lang['article_article_new_article']; ?></h3>
      </div>
      <div class="content">
        <ul class="nch-sidebar-article-list">
          <?php if(is_array($output['new_article_list']) and !empty($output['new_article_list'])){?>
          <?php foreach ($output['new_article_list'] as $k=>$v){?>
          <li><i></i><a <?php if($v['article_url']!=''){?>target="_blank"<?php } ?> href="<?php
		if ($v['article_url'] != '')
			echo $v['article_url'];
		else
			echo urlShop('article', 'show', array('article_id' => $v['article_id']));
		?>"><?php echo $v['article_title']?></a></li>
          <?php } ?>
          <?php }else{ ?>
          <li><?php echo $lang['article_article_no_new_article']; ?></li>
          <?php } ?>
        </ul>
      </div>
    </div>
  </div>
  <div class="right">
    <div class="nch-article-con">
      <h1><?php echo $output['article']['article_title']; ?></h1>
      <h2 class="ab_1">About us</h2>
      <div class="default">
      	<?php if($output['article']['article_id'] == '25'){ ?>
      		<div>
	  </div>
        <p><?php echo $output['article']['article_content']; ?></p>
      		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<meta name="keywords" content="百度地图,百度地图API，百度地图自定义工具，百度地图所见即所得工具" />
<meta name="description" content="百度地图API自定义地图，帮助用户在可视化操作下生成百度地图" />
<title>百度地图API自定义地图</title>
<!--引用百度地图API-->
<style type="text/css">
    html,body{margin:0;padding:0;}
    .iw_poi_title {color:#CC5522;font-size:14px;font-weight:bold;overflow:hidden;padding-right:13px;white-space:nowrap}
    .iw_poi_content {font:12px arial,sans-serif;overflow:visible;padding-top:4px;white-space:-moz-pre-wrap;word-wrap:break-word}
</style>
<script type="text/javascript" src="http://api.map.baidu.com/api?key=&v=1.1&services=true"></script>
</head>

<body>
  <!--百度地图容器-->
  <div style="width:500px;height:320px;border:#ccc solid 1px;" id="dituContent"></div>
</body>
<script type="text/javascript">
    //创建和初始化地图函数：
    function initMap(){
        createMap();//创建地图
        setMapEvent();//设置地图事件
        addMapControl();//向地图添加控件
        addMarker();//向地图中添加marker
    }
    
    //创建地图函数：
    function createMap(){
        var map = new BMap.Map("dituContent");//在百度地图容器中创建一个地图
        var point = new BMap.Point(114.480324,38.028645);//定义一个中心点坐标
        map.centerAndZoom(point,18);//设定地图的中心点和坐标并将地图显示在地图容器中
        window.map = map;//将map变量存储在全局
    }
    
    //地图事件设置函数：
    function setMapEvent(){
        map.enableDragging();//启用地图拖拽事件，默认启用(可不写)
        map.enableScrollWheelZoom();//启用地图滚轮放大缩小
        map.enableDoubleClickZoom();//启用鼠标双击放大，默认启用(可不写)
        map.enableKeyboard();//启用键盘上下左右键移动地图
    }
    
    //地图控件添加函数：
    function addMapControl(){
        //向地图中添加缩放控件
	var ctrl_nav = new BMap.NavigationControl({anchor:BMAP_ANCHOR_TOP_LEFT,type:BMAP_NAVIGATION_CONTROL_LARGE});
	map.addControl(ctrl_nav);
        //向地图中添加缩略图控件
	var ctrl_ove = new BMap.OverviewMapControl({anchor:BMAP_ANCHOR_BOTTOM_RIGHT,isOpen:1});
	map.addControl(ctrl_ove);
        //向地图中添加比例尺控件
	var ctrl_sca = new BMap.ScaleControl({anchor:BMAP_ANCHOR_BOTTOM_LEFT});
	map.addControl(ctrl_sca);
    }
    
    //标注点数组
    var markerArr = [{title:"诺融科技",content:"广平街7号",point:"114.480827|38.028546",isOpen:0,icon:{w:21,h:21,l:0,t:0,x:6,lb:5}}
		 ];
    //创建marker
    function addMarker(){
        for(var i=0;i<markerArr.length;i++){
            var json = markerArr[i];
            var p0 = json.point.split("|")[0];
            var p1 = json.point.split("|")[1];
            var point = new BMap.Point(p0,p1);
			var iconImg = createIcon(json.icon);
            var marker = new BMap.Marker(point,{icon:iconImg});
			var iw = createInfoWindow(i);
			var label = new BMap.Label(json.title,{"offset":new BMap.Size(json.icon.lb-json.icon.x+10,-20)});
			marker.setLabel(label);
            map.addOverlay(marker);
            label.setStyle({
                        borderColor:"#808080",
                        color:"#333",
                        cursor:"pointer"
            });
			
			(function(){
				var index = i;
				var _iw = createInfoWindow(i);
				var _marker = marker;
				_marker.addEventListener("click",function(){
				    this.openInfoWindow(_iw);
			    });
			    _iw.addEventListener("open",function(){
				    _marker.getLabel().hide();
			    })
			    _iw.addEventListener("close",function(){
				    _marker.getLabel().show();
			    })
				label.addEventListener("click",function(){
				    _marker.openInfoWindow(_iw);
			    })
				if(!!json.isOpen){
					label.hide();
					_marker.openInfoWindow(_iw);
				}
			})()
        }
    }
    //创建InfoWindow
    function createInfoWindow(i){
        var json = markerArr[i];
        var iw = new BMap.InfoWindow("<b class='iw_poi_title' title='" + json.title + "'>" + json.title + "</b><div class='iw_poi_content'>"+json.content+"</div>");
        return iw;
    }
    //创建一个Icon
    function createIcon(json){
        var icon = new BMap.Icon("http://app.baidu.com/map/images/us_mk_icon.png", new BMap.Size(json.w,json.h),{imageOffset: new BMap.Size(-json.l,-json.t),infoWindowOffset:new BMap.Size(json.lb+5,1),offset:new BMap.Size(json.x,json.h)})
        return icon;
    }
    
    initMap();//创建和初始化地图
</script>
</html>
      	<?php } else { ?>
	  <div>
	  </div>
        <p><?php echo $output['article']['article_content']; ?></p>
        <div>
	  <table>
	    <tr>
	    	<td>
	    	</td>
	    	</tr>
	    <tr>
	    <td>
	    	<div class="img_nch-article-con_table" style="height: 200px;">
	    	</div>
	    	</td>
	    	</tr>
	  </table>
	  </div>
	  <?php }?>
	  </div>
	  <!-- <div>	  
	  	<div class="lanxi_we"><p><strong>联系我们</strong></p><p><small>Contact us</small></p>	</div>
	  	<table cellpadding="0" cellspacing="0" class="tj_table">
	  		<tr><td><img src="http://www.nrwspt.com/data/upload/shop/common/name.png">　公司名称：<span> </span></td></tr>
	  		<tr><td><img src="http://www.nrwspt.com/data/upload/shop/common/add.png">　公司地址：<span> </span></td></tr>
	  		<tr><td><img src="http://www.nrwspt.com/data/upload/shop/common/tel.png">　联系电话：<span> </span></td></tr>
	  		<tr><td><img src="http://www.nrwspt.com/data/upload/shop/common/w.png">　公司网址：<span> </span></td></tr>
	  		<tr><td><img src="http://www.nrwspt.com/data/upload/shop/common/e.png">　电子邮箱：<span> </span></td></tr>
	  	</table>
	  	  
      </div> -->
      <div class="more_article"> <span class="fl"><?php echo $lang['article_show_previous']; ?>：
        <?php if(!empty($output['pre_article']) and is_array($output['pre_article'])){?>
        <a <?php if($output['pre_article']['article_url']!=''){?>target="_blank"<?php } ?> href="<?php
		if ($output['pre_article']['article_url'] != '')
			echo $output['pre_article']['article_url'];
		else
			echo urlShop('article', 'show', array('article_id' => $output['pre_article']['article_id']));
		?>"><?php echo $output['pre_article']['article_title']; ?></a> <time><?php echo date('Y-m-d H:i', $output['pre_article']['article_time']); ?></time>
        <?php }else{ ?>
        <?php echo $lang['article_article_not_found']; ?>
        <?php } ?>
        </span> <span class="fr"><?php echo $lang['article_show_next']; ?>：
        <?php if(!empty($output['next_article']) and is_array($output['next_article'])){?>
        <a <?php if($output['next_article']['article_url']!=''){?>target="_blank"<?php } ?> href="<?php
		if ($output['next_article']['article_url'] != '')
			echo $output['next_article']['article_url'];
		else
			echo urlShop('article', 'show', array('article_id' => $output['next_article']['article_id']));
		?>"><?php echo $output['next_article']['article_title']; ?></a> <time><?php echo date('Y-m-d H:i', $output['next_article']['article_time']); ?></time>
        <?php }else{ ?>
        <?php echo $lang['article_article_not_found']; ?>
        <?php } ?>
        </span> </div>
    </div>
  </div>
</div>
