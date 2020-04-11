<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['nc_title_manage'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=title&op=title"><span><?php echo $lang['nc_list'];?></span></a></li>
        <li><a href="index.php?act=title&op=title_add"><span><?php echo $lang['nc_new'];?></span></a></li>
        <li><a class="current" href="JavaScript:void(0);"><span><?php echo $lang['title_edit_title_pr_edit'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="pr_form" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="pr_id" value="<?php echo $output['pr_info']['pr_id']?>" />
    <input type="hidden" name="title_id" value="<?php echo $output['pr_info']['title_id']?>" />
    <table class="table tb-title2">
      <tbody>
        <tr class="noborder">
          <td class="required" colspan="2"><label class="validation" for="pr_name"><?php echo $lang['title_add_pr_name'];?></label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" class="txt" name="pr_name" id="pr_name" value="<?php echo $output['pr_info']['pr_name'];?>" /></td>
          <td class="vatop tips"><?php echo $lang['title_pr_edit_name_desc'];?></td>
        </tr>
        <tr>
          <td class="required" colspan="2"><label class="validation" for="pr_sort"><?php echo $lang['nc_sort'];?></label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" class="txt" name="pr_sort" id="pr_sort" value="<?php echo $output['pr_info']['pr_sort'];?>" /></td>
          <td class="vatop tips"><?php echo $lang['title_pr_edit_sort_desc'];?></td>
        </tr>
        <tr>
          <td class="required" colspan="2"><label><?php echo $lang['title_edit_title_pr_is_show'];?></label></td>
        </tr>
        <tr class="noborder">
		  <td class="vatop rowform onoff"><label for="pr_show1" class="cb-enable <?php if($output['pr_info']['pr_show'] == '1'){?>selected<?php }?>"><span><?php echo $lang['nc_yes'];?></span></label>
            <label for="pr_show0" class="cb-disable <?php if($output['pr_info']['pr_show'] == '0'){?>selected<?php }?>"><span><?php echo $lang['nc_no'];?></span></label>
            <input id="pr_show1" name="pr_show" <?php if($output['pr_info']['pr_show'] == '1'){?>checked="checked"<?php }?> value="1" type="radio" />
            <input id="pr_show0" name="pr_show" <?php if($output['pr_info']['pr_show'] == '0'){?>checked="checked"<?php }?> value="0" type="radio" />
          </td>
          </tr>
      </tbody>
    </table>
    <table class="table tb-title2 ">
      <thead class="thead">
        <tr class="space">
          <th colspan="15"><?php echo $lang['spec_add_spec_add'];?></th>
        </tr>
        <tr class="noborder">
          <th><?php echo $lang['nc_del'];?></th>
          <th><?php echo $lang['nc_sort'];?></th>
          <th><?php echo $lang['title_add_pr_value'];?></th>
          <th></th>
          <th class="align-center"><?php echo $lang['nc_handle'];?></th>
        </tr>
      </thead>
      <tbody id="tr_model">
        <tr></tr>
        <?php if(is_array($output['pr_value_list']) && !empty($output['pr_value_list'])) {?>
        <?php foreach($output['pr_value_list'] as $val) {?>
        <tr class="hover edit">
          <input type="hidden" nc_title="submit_value" name='pr_value[<?php echo $val['pr_value_id'];?>][form_submit]' value='' />
          <td class="w48"><input type="checkbox" name="pr_del[<?php echo $val['pr_value_id'];?>]" value="<?php echo $val['pr_value_id'];?>" /></td>
          <td class="w48 sort"><input type="text" nc_title="change_default_submit_value" name="pr_value[<?php echo $val['pr_value_id'];?>][sort]" value="<?php echo $val['pr_value_sort'];?>" /></td>
          <td class="w270 name"><input type="text" nc_title="change_default_submit_value" name="pr_value[<?php echo $val['pr_value_id'];?>][name]" value="<?php echo $val['pr_value_name'];?>" /></td>
          <td></td>
          <td class="w150 align-center"></td>
        </tr>
        <?php }?>
        <?php }else{?>
        <tr class="no_data">
          <td colspan="15"><?php echo $lang['spec_edit_spec_value_null'];?></td>
        </tr>
        <?php }?>
      </tbody>
      <tbody>
        <tr>
          <td colspan="15"><a class="btn-add marginleft" id="add_title" href="JavaScript:void(0);"> <span><?php echo $lang['title_add_pr_add_one_value'];?></span> </a></td>
        </tr>
      </tbody>
    </table>
    <table class="table tb-title2">
      <tfoot>
        <tr class="tfoot">
          <td colspan="15"><a id="submitBtn" class="btn" href="JavaScript:void(0);"> <span><?php echo $lang['nc_submit'];?></span> </a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script type="text/javascript">
$(function(){
    var i=0;
	var tr_model = '<tr class="hover edit">'+
		'<td class="w48"></td><td class="w48 sort"><input type="text" name="pr_value[key][sort]" value="0" /></td>'+
		'<td class="w270 name"><input type="text" name="pr_value[key][name]" value="" /></td>'+
		'<td></td><td class="w150 align-center"><a onclick="remove_tr($(this));" href="JavaScript:void(0);"><?php echo $lang['nc_del'];?></a></td>'+
	'</tr>';
	$("#add_title").click(function(){
		$('#tr_model > tr:last').after(tr_model.replace(/key/g,'s_'+i));
		<?php if(empty($output['pr_value_list'])) {?>
		$('.no_data').hide();
		<?php }?>
		$.getScript(RESOURCE_SITE_URL+"/js/admincp.js");
		i++;
	});

	//表单验证
    $('#pr_form').validate({
        errorPlacement: function(error, element){
			error.appendTo(element.parent().parent().prev().find('td:first'));
        },

        rules : {
        	pr_name: {
        		required : true,
                maxlength: 10,
                minlength: 1
            },
            pr_sort: {
				required : true,
				digits	 : true
            }
        },
        messages : {
        	pr_name : {
            	required : '<?php echo $lang['title_edit_title_pr_name_no_null'];?>',
                maxlength: '<?php echo $lang['title_edit_title_pr_name_max'];?>',
                minlength: '<?php echo $lang['title_edit_title_pr_name_max'];?>'
            },
            pr_sort: {
				required : '<?php echo $lang['title_edit_title_pr_sort_no_null'];?>',
				digits   : '<?php echo $lang['title_edit_title_pr_sort_no_digits'];?>'
            }
        }
    });

    //按钮先执行验证再提交表单
    $("#submitBtn").click(function(){
        if($("#pr_form").valid()){
        	$("#pr_form").submit();
    	}
    });

    //预览图片
    $("input[nc_type='change_default_goods_image']").live("change", function(){
		var src = getFullPath($(this)[0]);
		$(this).parent().prev().find('.low_source').pr('src',src);
	});

    $("input[nc_type='change_default_goods_image']").change(function(){
		$(this).parents('tr:first').find("input[nc_type='submit_value']").val('ok');
	});

    $("input[nc_type='change_default_submit_value']").change(function(){
    	$(this).parents('tr:first').find("input[nc_type='submit_value']").val('ok');
    });
	
});

function remove_tr(o){
	o.parents('tr:first').remove();
}
</script> 
<script type="text/javascript">
$(function(){
	$('input[nc_type="change_default_goods_image"]').live("change", function(){
		$(this).parent().find('input[class="title-file-text"]').val($(this).val());
	});
});
</script> 