<?php defined('InShopNC') or exit('Access Invalid!');?>
<?php
if (!@include(BASE_PATH.'/control/zcy_connect_data.php')) exit('zcy_connect_data.php isn\'t exists!');
require_once(BASE_PATH.'/../zcy/nr_zcy.php');
$zcy = new nr_zcy();
$orderId = $_GET['orderId'];
$rs = $zcy->order_other_info(array($orderId));
if(!empty($rs)){
    if($rs['success'] == true){
        if(!empty($rs['result'])){
            if($rs['result'][0]['openOrderContractDTO']['hasContract'] = true){
                foreach ($rs['result'][0]['openOrderContractDTO']['contractInfoDTOs'] as $k=>$v){
                    ?>

                        <div class="ncsc-oredr-show">
                        <div class="ncsc-order-info">
                            <div class="ncsc-order-details">
                                <div class="title">订单附加信息</div>
                                <div class="content">
                                    <dl>
                                        <dt>合同编号：</dt>
                                        <dd><?php echo $v['contractNo'];?></dd>
                                    </dl>
                                    <dl>
                                        <dt>合同状态：</dt>
                                        <dd>
                                            <?php echo $v['contractStatusStr'];?>
                                        </dd>
                                    </dl>
                                    <dl>
                                        <dt>下载链接：</dt>
                                        <dd><a href="<?php echo $v['contractDownloadUrl']; ?>"><?php echo $v['contractDownloadUrl']; ?></a></dd>
                                    </dl>
                                    <dl>
                                        <dt></dt>
                                        <dd></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>


                    <?php
                }
            }else{
                echo '<p style="text-align: center;font-size: 19px;line-height: 150px;"该订单未签订合同！</p>';
                die;
            }
        }else{
            echo '<p style="text-align: center;font-size: 19px;line-height: 150px;">暂无更多信息</p>';
            die;
        }
    }else{
        echo '<p style="text-align: center;font-size: 19px;line-height: 150px;">页面错误，请稍后重试！</p>';
        die;
    }
}else{
    echo '<p style="text-align: center;font-size: 19px;line-height: 150px;">暂无更多信息</p>';
    die;
}

?>
<script type="text/javascript">
$(function(){
    $('#show_shipping').on('hover',function(){
        var_send = '<?php echo date("Y-m-d H:i:s",$output['order_info']['extend_order_common']['shipping_time']); ?>&nbsp;&nbsp;<?php echo $lang['member_show_seller_has_send'];?><br/>';
    	$.getJSON('index.php?act=store_deliver&op=get_express&e_code=<?php echo $output['order_info']['express_info']['e_code']?>&shipping_code=<?php echo $output['order_info']['shipping_code']?>&t=<?php echo random(7);?>',function(data){
    		if(data){
    			data = var_send+data;
    			$('#shipping_ul').html(data);
    			$('#show_shipping').unbind('hover');
    		}else{
    			$('#shipping_ul').html(var_send);
    			$('#show_shipping').unbind('hover');
    		}
    	});
    });
});
</script>
