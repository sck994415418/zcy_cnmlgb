<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<style type="text/css">
  @import url("<?php echo SHOP_TEMPLATES_URL; ?>/css/zcy.css");
</style>
<?php
//	require_once(BASE_PATH.'/../zcy/nr_zcy.php');
//	$zcy = new nr_zcy;
	if (!@include(BASE_PATH.'/control/zcy_connect_data.php')) exit('zcy_connect_data.php isn\'t exists!');
	$con = new zcy_data();
?>
<div id="content">
    <!--S 分类选择区域-->
    <div class="wrapper_search">
        <div class="wp_sort">
            <div id="dataLoading" class="wp_data_loading">
                <div class="data_loading">加载中...</div>
            </div>
            <div class="sort_selector">
                <div class="sort_title">
                    <label class="submit-border"><input onClick="get_category(0,2)" value="更新一级分类" type="button" class="submit" style=" width: 200px;" /></label>
					&nbsp;&nbsp;
					<label class="submit-border"><input onClick="get_category(0,4)" value="更新全部分类" type="button" class="submit" style=" width: 200px;" /></label>
                </div>
            </div>
            <div id="class_div" class="wp_sort_block">
                <div class="sort_list">
                    <div class="wp_category_list">
                        <div id="class_div_1" class="category_list">
                            <ul>
<?php
    $sql = "select * from `zcy_category` where `level` =1";
    $rs = $con->select_data($sql);

    foreach($rs as $cat){
?>
                                <li class="" nctype="selClass" data-param="{id:<?php echo $cat["id"];?>,deep:1}"><a class="" href="javascript:void(0)"><i class="icon-double-angle-right"></i><?php echo $cat["name"];?></a></li>
<?php        
   }    
?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="sort_list">
                    <div class="wp_category_list blank">
                        <div id="class_div_2" class="category_list">
                            <ul>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="sort_list sort_list_last">
                    <div class="wp_category_list blank">
                        <div id="class_div_3" class="category_list">
                            <ul>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="alert">
            <p id="cat"><span>正在更新： </span><span id="c1"></span><span id="c2"></span><span id="c3"></span></p><p id="cc"></p>
        </div>
        <div class="wp_confirm">
            <div class="bottom tc">
				<input type="hidden" name="root" id="root" value="" />
				<input type="hidden" name="depth" id="depth" value="" />
                <label class="submit-border"><input disabled="disabled" nctype="buttonNextStep" value="更新当前类目" type="submit" class="submit" style=" width: 200px;" /></label>
            </div>
        </div>
    </div>
	<div id="error_info"></div>
    <script src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/zcy_update_category.js"></script> 
    <script>
        SEARCHKEY = '<?php echo $lang['store_goods_step1_search_input_text'];?>';
        RESOURCE_SITE_URL = '<?php echo RESOURCE_SITE_URL;?>';
    </script>
</div>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js"></script>
<script>
$(function(){
    //Ajax提示
    $('.tip').poshytip({
        className: 'tip-yellowsimple',
        showTimeout: 1,
        alignTo: 'target',
        alignX: 'center',
        alignY: 'top',
        offsetY: 5,
        allowTipHover: false
    });
});
</script>
