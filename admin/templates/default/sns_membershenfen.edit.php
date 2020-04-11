<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['sns_member_shenfen'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=member_shenfen&op=index"><span><?php echo $lang['sns_member_shenfen_manage'];?></span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['nc_edit'];?></span></a></li>
      </ul>
    </div>
  </div>
<div class="fixed-empty"></div>
  <form id="membertag_form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="mshenfen_id" value="<?php echo $output['mshenfen_info']['mshenfen_id'];?>" />
    <table class="table tb-type2">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="membershenfen_name"><?php echo $lang['sns_member_shenfen_name'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php echo $output['mshenfen_info']['mshenfen_name'];?>" name="membershenfen_name" id="membershenfen_name" class="txt"></td>
          <td class="vatop tips"><?php echo $lang['sns_member_tag_name_tips'];?></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label><?php echo $lang['nc_recommend'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform onoff"><label for="mtag_recommend1" class="cb-enable <?php if($output['mshenfen_info']['is_show'] == 1){?>selected<?php }?>"><span><?php echo $lang['nc_yes'];?></span></label>
            <label for="mtag_recommend0" class="cb-disable <?php if($output['mshenfen_info']['is_show'] == 0){?>selected<?php }?>"><span><?php echo $lang['nc_no'];?></span></label>
            <input id="mtag_recommend1" name="is_show" <?php if($output['mshenfen_info']['is_show'] == 1){?>checked="checked"<?php }?> value="1" type="radio">
            <input id="mtag_recommend0" name="is_show" <?php if($output['mshenfen_info']['is_show'] == 0){?>checked="checked"<?php }?> value="0" type="radio"></td>
          <td class="vatop tips"><?php echo $lang['sns_member_tag_recommend_tips'];?></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="mshenfen_sort"><?php echo $lang['nc_sort'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php echo $output['mshenfen_info']['mshenfen_sort'];?>" name="membershenfen_sort" id="mshenfen_sort" class="txt"></td>
          <td class="vatop tips"><?php echo $lang['sns_member_tag_sort_tips'];?></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="2"><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span><?php echo $lang['nc_submit'];?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script type="text/javascript">
$(function(){
	var textButton1="<input type='text' name='textfield' id='textfield1' class='type-file-text' /><input type='button' name='button' id='button1' value='' class='type-file-button' />"
	$(textButton1).insertBefore("#membertag_img");
	$("#membertag_img").change(function(){$("#textfield1").val($("#membertag_img").val());});

	$("#submitBtn").click(function(){
		if($("#membertag_form").valid()){
			$("#membertag_form").submit();
		}
	});
	
	$('#membertag_form').validate({
        errorPlacement: function(error, element){
			error.appendTo(element.parent().parent().prev().find('td:first'));
        },

        rules : {
        	membertag_name : {
                required : true,
                maxlength : 20
            },
            membertag_sort : {
            	digits   : true
            },
            membertag_desc :{
				maxlength : 50
            }
        },
        messages : {
        	membertag_name : {
                required : '<?php echo $lang['sns_member_tag_name_null_error'];?>',
                maxlength : '<?php echo $lang['sns_member_tag_name_max_error'];?>'
            },
            membertag_sort : {
            	digits   : '<?php echo $lang['sns_member_tag_sort_error'];?>'
            },
            membertag_desc :{
				maxlength : '<?php echo $lang['sns_member_tag_desc_max_error'];?>'
            }
        }
    });
});
</script>