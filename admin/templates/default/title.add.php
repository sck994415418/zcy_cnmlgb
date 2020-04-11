<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['nc_title_manage'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=title&op=title"><span><?php echo $lang['nc_list'];?></span></a></li>
        <li><a class="current" href="JavaScript:void(0);"><span><?php echo $lang['nc_new'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="title_form" method="post">
    <table id="prompt" class="table tb-type2">
      <tbody>
        <tr class="space odd">
          <th colspan="12" class="nobg"> <div class="title">
              <h5><?php echo $lang['nc_prompts'];?></h5>
              <span class="arrow"></span> </div>
          </th>
        </tr>
        <tr class="odd">
          <td><ul>
              <li><?php echo $lang['title_add_prompts_three'];?></li>
              <li><?php echo $lang['title_add_prompts_four'];?></li>
            </ul></td>
        </tr>
      </tbody>
    </table>
    <input type="hidden" value="ok" name="form_submit">
    <table class="table tb-type2">
      <tbody>
        <tr>
          <td class="required" colspan="2"><label class="validation" for="t_mane"><?php echo $lang['title_index_title_name'].$lang['nc_colon'];?></label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" class="txt" name="t_mane" id="t_mane" /></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td class="required" colspan="2"><label class="" for="s_sort"><?php echo $lang['title_common_belong_class'].$lang['nc_colon'];;?></label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform" id="gcategory">
            <input type="hidden" value="" class="mls_id" name="class_id" />
            <input type="hidden" value="" class="mls_name" name="class_name" />
            <select class="class-select">
              <option value="0"><?php echo $lang['nc_please_choose'];?>...</option>
              <?php if(!empty($output['gc_list'])){ ?>
              <?php foreach($output['gc_list'] as $k => $v){ ?>
              <?php if ($v['gc_parent_id'] == 0) {?>
              <option value="<?php echo $v['gc_id'];?>"><?php echo $v['gc_name'];?></option>
              <?php } ?>
              <?php } ?>
              <?php } ?>
            </select></td>
          <td class="vatop tips"><?php echo $lang['title_common_belong_class_tips'];?></td>
        </tr>
        <tr>
          <td class="required" colspan="2"><label class="validation" for="t_sort"><?php echo $lang['nc_sort'].$lang['nc_colon'];?></label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" class="txt" name="t_sort" id="t_sort" value="0" /></td>
          <td class="vatop tips"><?php echo $lang['title_add_sort_desc'];?></td>
        </tr>
      </tbody>
    </table>
    <table class="table tb-type2 mtw">
      <thead class="thead">
        <tr class="space">
          <th colspan="15"><?php echo $lang['title_add_pr_add'].$lang['nc_colon'];?></th>
        </tr>
        <tr>
          <th><?php echo $lang['nc_sort'];?></th>
          <th><?php echo $lang['title_add_pr_name'];?></th>
          <th><?php echo $lang['title_add_pr_value'];?></th>
          <th class="align-center"><?php echo $lang['nc_display'];?></th>
          <th class="align-center"><?php echo $lang['nc_handle'];?></th>
        </tr>
      </thead>
      <tbody id="tr_model">
        <tr></tr>
        <tr class="hover edit">
          <td class="w48 sort"><input type="text" name="at_value[key][sort]" value="0" /></td>
          <td class="w25pre name"><input type="text" name="at_value[key][name]" value="" /></td>
          <td class="w50pre name"><textarea rows="10" cols="80" name="at_value[key][value]"></textarea></td>
          <td class="align-center power-onoff"><input type="checkbox" checked="checked" nc_type="checked_show" />
            <input type="hidden" name="at_value[key][show]" value="1" /></td>
          <td class="align-center w60"><a onclick="remove_tr($(this));" href="JavaScript:void(0);"><?php echo $lang['title_add_remove'];?></a></td>
        </tr>
      </tbody>
      <tbody>
        <tr>
          <td colspan="15"><a id="add_title" class="btn-add marginleft" href="JavaScript:void(0);"> <span><?php echo $lang['title_add_pr_add_one'];?></span> </a></td>
        </tr>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="15"><a id="submitBtn" class="btn" href="JavaScript:void(0);"> <span><?php echo $lang['nc_submit'];?></span> </a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.mousewheel.js"></script>
<script>
$(function(){

    $('#spec_div').perfectScrollbar();
    $('#brand_div').perfectScrollbar();
    
	var i = 0;
	var tr_model = '<tr class="hover edit">'+
		'<td class="w48 sort"><input type="text" name="at_value[key][sort]" value="0" /></td>'+
		'<td class="w25pre name"><input type="text" name="at_value[key][name]" value="" /></td>'+
		'<td class="w50pre name"><textarea rows="10" cols="80" name="at_value[key][value]"></textarea></td>'+
		'<td class="align-center power-onoff"><input type="checkbox" checked="checked" nc_type="checked_show" /><input type="hidden" name="at_value[key][show]" value="1" /></td>'+
		'<td class="align-center w60"><a onclick="remove_tr($(this));" href="JavaScript:void(0);"><?php echo $lang['title_add_remove'];?></a></td>'+
	'</tr>';
	$("#add_title").click(function(){
		$('#tr_model > tr:last').after(tr_model.replace(/key/g, i));
		$.getScript(RESOURCE_SITE_URL+"/js/admincp.js");
		i++;
	});

	$('input[nc_title="checked_show"]').live('click', function(){
		var o = $(this).next();
		//alert(o.val());
		if(o.val() == '1'){
			o.val('0');
		}else if(o.val() == '0'){
			o.val('1');
		}
	});


	//表单验证
    $('#title_form').validate({
        errorPlacement: function(error, element){
			error.appendTo(element.parent().parent().prev().find('td:first'));
        },

        rules : {
        	t_mane: {
        		required : true,
                maxlength: 20,
                minlength: 1
            },
            t_sort: {
				required : true,
				digits	 : true
            }
        },
        messages : {
        	t_mane : {
        		required : '<?php echo $lang['title_add_name_no_null'];?>',
        		maxlength: '<?php echo $lang['title_add_name_max'];?>',
        		minlength: '<?php echo $lang['title_add_name_max'];?>' 
            },
            t_sort: {
            	required : '<?php echo $lang['title_add_sort_no_null'];?>',
            	digits : '<?php echo $lang['title_add_sort_no_digits'];?>' 
            }
        }
    });

    //按钮先执行验证再提交表单
    $("#submitBtn").click(function(){
    	spec_check();
        if($("#title_form").valid()){
        	$("#title_form").submit();
    	}
    });

	// 所属分类
    $("#gcategory > select").live('change',function(){
    	spec_scroll($(this));
    	brand_scroll($(this));
    });

	// 规格搜索
    $("#speccategory > select").live('change',function(){
    	spec_scroll($(this));
    });
	// 品牌搜索
    $("#brandcategory > select").live('change',function(){
    	brand_scroll($(this));
    });

    // 规格隐藏未选项
    $('a[nctype="spec_hide"]').live('click',function(){
    	checked_hide('spec');
    });
	// 规格全部显示
	$('a[nctype="spec_show"]').live('click',function(){
		checked_show('spec');
	});
	// 品牌隐藏未选项
	$('a[nctype="brand_hide"]').live('click',function(){
    	checked_hide('brand');
	});
	// 品牌全部显示
	$('a[nctype="brand_show"]').live('click',function(){
		checked_show('brand');
	});
});
var specScroll = 0;
function spec_scroll(o){
	var id = o.val();
	if(!$('#spec_h6_'+id).is('h6')){
		return false;
	}
	$('#spec_div').scrollTop(-specScroll);
	var sp_top = $('#spec_h6_'+id).offset().top;
	var div_top = $('#spec_div').offset().top;
	$('#spec_div').scrollTop(sp_top-div_top);
	specScroll = sp_top-div_top;
}

var brandScroll = 0;
function brand_scroll(o){
	var id = o.val();
	if(!$('#brand_h6_'+id).is('h6')){
		return false;
	}
	$('#brand_div').scrollTop(-brandScroll);
	var sp_top = $('#brand_h6_'+id).offset().top;
	var div_top = $('#brand_div').offset().top;
	$('#brand_div').scrollTop(sp_top-div_top);
	brandScroll = sp_top-div_top;
}

// 隐藏未选项
function checked_show(str) {
	$('#'+str+'_table').find('h6').show().end().find('li').show();
	$('#'+str+'_table').find('tr').show();
	$('a[nctype="'+str+'_show"]').pr('nctype',str+'_hide').children().html('<?php echo $lang['title_common_checked_hide'];?>');
    $('#'+str+'_div').perfectScrollbar('destroy').perfectScrollbar();
}

// 显示全部选项
function checked_hide(str) {
	$('#'+str+'_table').find('h6').hide();
	$('#'+str+'_table').find('input[type="checkbox"]').parents('li').hide();
	$('#'+str+'_table').find('input[type="checkbox"]:checked').parents('li').show();
	$('#'+str+'_table').find('tr').each(function(){
	    if ($(this).find('input[type="checkbox"]:checked').length == 0 ) $(this).hide();
	});
	$('a[nctype="'+str+'_hide"]').pr('nctype',str+'_show').children().html('<?php echo $lang['title_common_checked_show'];?>');
    $('#'+str+'_div').perfectScrollbar('destroy').perfectScrollbar();
}

function spec_check(){
	var id='';
	$('input[nc_title="change_default_spec_value"]:checked').each(function(){
		if(!isNaN($(this).val())){
			id += $(this).val();
		}
	});
	if(id != ''){
		$('#spec_checkbox').val('ok');
	}else{
		$('#spec_checkbox').val('');
	}
}


function remove_tr(o){
	o.parents('tr:first').remove();
}
// 所属分类
gcategoryInit('gcategory');
// 规格搜索
gcategoryInit('speccategory');
// 品牌搜索
gcategoryInit('brandcategory');

</script>
