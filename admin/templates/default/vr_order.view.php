<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <table class="table tb-type2 order">
    <form action="index.php?act=vr_order&op=show_order" method="post" accept-charset="utf-8">
    <tbody>
      <tr class="space">
        <th colspan="2"><?php echo $lang['order_detail'];?></th>
      </tr>
      <tr>
        <th><?php echo $lang['order_info'];?></th>
      </tr>
      <tr>
        <td colspan="2">
          <ul>
            <li>
              <strong>维修人:</strong>
              <?php echo $output['order_info']['buyer_name'];?>
            </li>
            <li>
              <strong>维修状态>:</strong>
              <?php echo $output['order_info']['state_desc'];?>
            </li>
            <li>
              <strong><?php echo $lang['order_total_price'];?>:</strong>
              <span class="red_common">
                <?php echo $lang['currency'].$output['order_info']['cost'];?> 
              </span>
            </li>
            <li>
              <strong><?php echo $lang['order_time'];?><?php echo $lang['nc_colon'];?></strong>
              <?php echo date('Y-m-d H:i:s',$output['order_info']['add_time']);?>
            </li>
            <li>
              <strong>物品类型<?php echo $lang['nc_colon'];?></strong>
              <?php echo $output['order_info']['type'];?>
            </li>
            <li>
              <strong>买家留言<?php echo $lang['nc_colon'];?></strong>
              <?php echo $output['order_info']['buyer_msg'];?>
            </li>
            <?php if($output['order_info']['jdtime'] != ''){ ?>
            <li>
              <strong>接单时间<?php echo $lang['nc_colon'];?></strong>
              <?php echo date('Y-m-d H:i:s',$output['order_info']['jdtime']);?>
            </li>
            <?php } ?>
            <?php if($output['order_info']['cftime'] != ''){ ?>
            <li>
              <strong>出发时间<?php echo $lang['nc_colon'];?></strong>
              <?php echo date('Y-m-d H:i:s',$output['order_info']['cftime']);?>
            </li>
            <?php } ?>
            <?php if($output['order_info']['wxtime'] != ''){ ?>
            <li>
              <strong>维修时间<?php echo $lang['nc_colon'];?></strong>
              <?php echo date('Y-m-d H:i:s',$output['order_info']['wxtime']);?>
            </li>
            <?php } ?>
            <?php if($output['order_info']['finnshed_time'] > 0){ ?>
            <li>
              <strong>完成时间<?php echo $lang['nc_colon'];?></strong>
              <?php echo date('Y-m-d H:i:s',$output['order_info']['finnshed_time']);?>
            </li>
            <?php } ?>
            <?php if($output['order_info']['evaluate_addtime'] != ''){ ?>
            <li>
              <strong>评价分数<?php echo $lang['nc_colon'];?></strong>
              <?php echo $output['order_info']['evaluate_star'];?>
            </li>
            <li>
              <strong>评价内容<?php echo $lang['nc_colon'];?></strong>
              <?php echo $output['order_info']['evaluate_content'];?>
            </li>
            <li>
              <strong>评价时间<?php echo $lang['nc_colon'];?></strong>
              <?php echo date('Y-m-d H:i:s',$output['order_info']['evaluate_addtime']);?>
            </li>
            <?php } ?>
            <?php if($output['order_info']['wxstate'] == 0){ ?>
              <input type="hidden" name="order_id" value="<?php echo $output['order_info']['order_id'];?>">
              <input type="hidden" name="order_sn" value="<?php echo $output['order_info']['order_sn'];?>">
              <li>
                <strong>派送师傅<?php echo $lang['nc_colon'];?></strong>
                <select name="master_id">
                  <?php foreach ($output['member'] as $key => $value) { 
                      if($value['member_truename'] == ''){
                        $name = $value['member_name'];
                      }else{
                        $name = $value['member_truename'];
                      }
                    ?>
                    <option value="<?php echo $value['member_id']; ?>"><?php echo $name; ?></option>
                  <?php } ?>
                </select>
              </li>
            <?php } ?>
          </ul>
        </td>
      </tr>
    </tbody>

    <tfoot>
      <?php if($output['order_info']['wxstate'] == 0){ ?>
      <tr class="tfoot">
        <td>
          <!-- <a href="JavaScript:void(0);" class="btn" onclick="history.go(-1)"><span><?php echo $lang['nc_back'];?></span></a> -->
          <input type="submit" class="btn" value="派送">
        </td>
      </tr>
      <?php } ?>
    </tfoot>  
  </form>
  </table>
</div>
