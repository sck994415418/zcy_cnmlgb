<div class="goods-gallery add-step2"> <a class="sample_demo" id="select_s" href="index.php?act=store_album&op=pic_list&item=des" style="display:none;"><?php echo $lang['nc_submit'];?></a>
  <?php if(!empty($output['pic_list'])){?>
  <ul class="list">
    <?php foreach ($output['pic_list'] as $v){?>
    <li onclick="insert_editor('https://zcy-gov-item.oss-cn-north-2-gov-1.aliyuncs.com/<?php echo $v['fileid'];?>');"><a href="JavaScript:void(0);"><img src="https://zcy-gov-item.oss-cn-north-2-gov-1.aliyuncs.com/<?php echo $v['fileid'];?>" title='<?php echo $v['fileid'];?>'/></span></a></li>
    <?php }?>
  </ul>
  <?php }else{?>
  <div class="warning-option"><i class="icon-warning-sign"></i><span>相册中暂无图片</span></div>
  <?php }?>
  <div class="pagination"><?php echo $output['show_page']; ?></div>
</div>
<script>
$(document).ready(function(){
	$('.demo').ajaxContent({
		event:'click', //mouseover
		loaderType:'img',
		loadingMsg:'<?php echo SHOP_TEMPLATES_URL;?>/images/loading.gif',
		target:'#des_demo'
	});
	$('#jump_menu').change(function(){
		$('#select_s').attr('href',$('#select_s').attr('href')+"&id="+$('#jump_menu').val());
		$('.sample_demo').ajaxContent({
			event:'click', //mouseover
			loaderType:'img',
			loadingMsg:'<?php echo SHOP_TEMPLATES_URL;?>/images/loading.gif',
			target:'#des_demo'
		});
		$('#select_s').click();
	});
});
</script>