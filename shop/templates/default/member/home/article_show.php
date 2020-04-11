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
	  <div>
	  <table><tr><td>
	    <div>
	    	<ul>
	    		<li><img src="http://b2b2c.zm-y.com/data/upload/shop/common/y.png"/></li>
	    		<li><h2 class="ab_1">合作项目</h2></li>
	    	</ul>
	    </div></td></tr>
	  </table>
	  </div>
        <p><?php echo $output['article']['article_content']; ?></p>
        <div>
	  <table>
	    <tr>
	    	<td>
	    		<div>
	    	<ul>
	    		<li><img src="http://b2b2c.zm-y.com/data/upload/shop/common/y.png"/></li>
	    		<li><h2 class="ab_1">合作项目</h2></li>
	    	</ul>
	    	</div>
	    	</td>
	    	</tr>
	    <tr>
	    <td>
	    	<div class="img_nch-article-con_table">
	    		<ul>
	    			<li><img src="http://b2b2c.zm-y.com/data/upload/shop/common/pic.png"/>
	    				<p>合作项目</p> 					
	    			</li>
	    			<li><img src="http://b2b2c.zm-y.com/data/upload/shop/common/pic.png"/>
	    				<p>合作项目</p>
	    			</li>
	    			<li><img src="http://b2b2c.zm-y.com/data/upload/shop/common/pic.png"/>
	    				<p>合作项目</p>
	    			</li>
	    		</ul>
	    	</div>
	    	</td>
	    	</tr>
	  </table>
	  </div>
	  </div>
	  <div>	  
	  	<div class="lanxi_we"><p><strong>联系我们</strong></p><p><small>Contact us</small></p>	</div>
	  	<table cellpadding="0" cellspacing="0" class="tj_table">
	  		<tr><td><img src="http://b2b2c.zm-y.com/data/upload/shop/common/name.png">　公司名称：<span>某某某有限公司责任公司</span></td></tr>
	  		<tr><td><img src="http://b2b2c.zm-y.com/data/upload/shop/common/add.png">　公司地址：<span>河北省唐山市迁安市燕山大路</span></td></tr>
	  		<tr><td><img src="http://b2b2c.zm-y.com/data/upload/shop/common/tel.png">　联系电话：<span>0315-1552410</span></td></tr>
	  		<tr><td><img src="http://b2b2c.zm-y.com/data/upload/shop/common/w.png">　公司网址：<span>www.cctv.com</span></td></tr>
	  		<tr><td><img src="http://b2b2c.zm-y.com/data/upload/shop/common/e.png">　电子邮箱：<span>1582365478@163.com</span></td></tr>
	  	</table>
	  	  
      </div>
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
