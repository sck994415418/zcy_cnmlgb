<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <?php if($output['goods_id'] == ''){

        ?>
        <h3>商品添加</h3>
        <?php }else{ ?>
        <h3>商品修改</h3>
        <?php } ?>
        <?php echo $output['top_link'];?> 
      </div>
    </div>
    <div class="fixed-empty"></div>
    <form id="form1" enctype="multipart/form-data" method="post">
      <input type="hidden" name="form_submit" value="ok" />
      <input type="hidden" name="goods_id" value="<?php echo $output['goods_id']; ?>" />
      <table class="table tb-type2">
        <tbody>
          <tr class="noborder">
            <td colspan="2" class="required">
              <label class="gc_name validation" for="gc_name">商品名称:</label>
            </td>
          </tr>
          <tr class="noborder">
            <td class="vatop rowform">
              <input type="text" maxlength="20" value="<?php echo $output['goods_detail']['goods_name'];?>" name="goods_name" id="gc_name" class="txt">
            </td>
            <td class="vatop tips"></td>
          </tr>
          <tr class="noborder">
            <td colspan="2" class="required">
              <label class="gc_name validation" for="gc_name">商品广告语:</label>
            </td>
          </tr>
          <tr class="noborder">
            <td class="vatop rowform">
              <textarea name="goods_jingle" style="height:80px;width:250px;"><?php echo $output['goods_detail']['goods_jingle'];?></textarea>
            </td>
            <td class="vatop tips"></td>
          </tr>

          <tr>
            <td colspan="2" class="required"><label for="pic">商品图片:</label>
            </td>
          </tr>
          <tr class="noborder">
            <td class="vatop rowform">
              <span class="type-file-show">
                <img class="show_image" src="<?php echo ADMIN_TEMPLATES_URL;?>/images/preview.png">
                <div class="type-file-preview">
                  <img src="<?php echo cthumb($output['goods_detail']['goods_image'], 360, $output['goods_detail']['store_id']); ?>">
                </div>
              </span>
              <span class="type-file-box">
                <input type='text' name='goods_image' id='textfield1' value="<?php echo $output['goods_detail']['goods_image']; ?>" class='type-file-text' />
                <input type='button' name='button' id='button1' value='' class='type-file-button' />
                <input name="pic" type="file" class="type-file-file" id="pic" size="30" hidefocus="true" nc_type="change_pic">
              </span>
            </td>
            <td class="vatop tips"><?php echo '建议用16px * 16px，超出后自动隐藏';?></td>
          </tr>

          <tr>
            <td colspan="2" class="required">
              <label class="validation">分类:</label>
            </td>
          </tr>
          <tr class="noborder">
            <td colspan="2" id="gcategory">
              <select class="class-select" name="gc_id">
                <option value="0"><?php echo $lang['nc_please_choose'];?>...</option>
                <?php
                foreach ($output['type'] as $v) {
                  ?>
                  <option value="<?php echo $v['gc_id'];?>" <?php if($output['goods_detail']['gc_id'] == $v['gc_id']){ ?>selected<?php } ?>><?php echo $v['gc_name'];?></option>
                  <?php 
                  foreach ($v['child'] as $va) {
                    ?>
                    <option value="<?php echo $v['gc_id'].','.$va['gc_id'];?>" <?php if($output['goods_detail']['gc_id'] == $va['gc_id']){ ?>selected<?php } ?>>&nbsp;&nbsp;&nbsp;<?php echo $va['gc_name'];?>
                    </option>
                    <?php
                    foreach ($va['child'] as $value) {
                      ?>
                      <option value="<?php echo $v['gc_id'].','.$va['gc_id'].','.$value['gc_id'];?>" <?php if($output['goods_detail']['gc_id'] == $value['gc_id']){ ?>selected<?php } ?>>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value['gc_name'];?>
                      </option>
                      <?php
                    }
                  }
                }
                ?>
              </select>
            </td>
          </tr>

          <tr>
            <td colspan="2" class="required">
              <label class="validation">品牌:</label>
            </td>
          </tr>
          <tr class="noborder">
            <td colspan="2" id="gcategory">
              <select class="class-select" name="brand_id">
                <option value="0"><?php echo $lang['nc_please_choose'];?>...</option>
                <?php
                  foreach ($output['brand_list'] as $vas) {
                ?>
                  <option value="<?php echo $vas['brand_id'];?>" <?php if($output['goods_detail']['brand_id'] == $vas['brand_id']){ ?>
                    selected<?php } ?>>
                    <?php echo $vas['brand_name'];?>
                  </option>
                <?php } ?>
              </select>
            </td>
          </tr>

          <tr class="noborder">
            <td colspan="2" class="required">
              <label class="gc_name validation" for="gc_name">商品价格:</label>
            </td>
          </tr>
          <tr class="noborder">
            <td class="vatop rowform">
              <input type="text" maxlength="20" value="<?php echo $output['goods_detail']['goods_price'];?>" name="goods_price" id="gc_name" class="txt">
            </td>
            <td class="vatop tips"></td>
          </tr>

          <tr class="noborder">
            <td colspan="2" class="required">
              <label class="gc_name validation" for="gc_name">商品促销价格:</label>
            </td>
          </tr>
          <tr class="noborder">
            <td class="vatop rowform">
              <input type="text" maxlength="20" value="<?php echo $output['goods_detail']['goods_promotion_price'];?>" name="goods_promotion_price" id="gc_name" class="txt">
            </td>
            <td class="vatop tips"></td>
          </tr>

          <tr class="noborder">
            <td colspan="2" class="required">
              <label class="gc_name validation" for="gc_name">商品市场价格:</label>
            </td>
          </tr>
          <tr class="noborder">
            <td class="vatop rowform">
              <input type="text" maxlength="20" value="<?php echo $output['goods_detail']['goods_marketprice'];?>" name="goods_marketprice" id="gc_name" class="txt">
            </td>
            <td class="vatop tips"></td>
          </tr>

          <tr class="noborder">
            <td colspan="2" class="required">
              <label class="gc_name validation" for="gc_name">商品库存:</label>
            </td>
          </tr>
          <tr class="noborder">
            <td class="vatop rowform">
              <input type="text" maxlength="20" value="<?php echo $output['goods_detail']['goods_storage'];?>" name="goods_storage" id="gc_name" class="txt">
            </td>
            <td class="vatop tips"></td>
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
  <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" charset="utf-8"></script> 
  <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.mousewheel.js"></script> 
  <script>
  //按钮先执行验证再提交表单
  $(function(){

    $('#type_div').perfectScrollbar();

    $("#submitBtn").click(function(){
      // if($("#goods_class_form").valid()){
        // $("#goods_class_form").submit();
      // }
      form1.submit();
    });

    $("#pic").change(function(){
      $("#textfield1").val($(this).val());
    });
    $('input[type="radio"][name="t_id"]').click(function(){
      if($(this).val() == '0'){
        $('#t_name').val('');
      }else{
        $('#t_name').val($(this).next('span').html());
      }
    });

    $('#goods_class_form').validate({
      errorPlacement: function(error, element){
        error.appendTo(element.parent().parent().prev().find('td:first'));
      },
      rules : {
        gc_name : {
          required : true,
          remote   : {                
            url :'index.php?act=goods_class&op=ajax&branch=check_class_name',
            type:'get',
            data:{
              gc_name : function(){
                return $('#gc_name').val();
              },
              gc_parent_id : function() {
                return $('#gc_parent_id').val();
              },
              gc_id : ''
            }
          }
        },
        commis_rate : {
          required :true,
          max :100,
          min :0,
          digits :true
        },
        gc_sort : {
          number   : true
        }
      },
      messages : {
        gc_name : {
          required : '<?php echo $lang['goods_class_add_name_null'];?>',
          remote   : '<?php echo $lang['goods_class_add_name_exists'];?>'
        },
        commis_rate : {
          required : '<?php echo $lang['goods_class_add_commis_rate_error'];?>',
          max :'<?php echo $lang['goods_class_add_commis_rate_error'];?>',
          min :'<?php echo $lang['goods_class_add_commis_rate_error'];?>',
          digits :'<?php echo $lang['goods_class_add_commis_rate_error'];?>'
        },
        gc_sort  : {
          number   : '<?php echo $lang['goods_class_add_sort_int'];?>'
        }
      }
    });

  // 所属分类
  $("#gc_parent_id").live('change',function(){
    type_scroll($(this));
  });
    // 类型搜索
    $("#gcategory > select").live('change',function(){
      type_scroll($(this));
    });
  });
var typeScroll = 0;
function type_scroll(o){
  var id = o.val();
  if(!$('#type_dt_'+id).is('dt')){
    return false;
  }
  $('#type_div').scrollTop(-typeScroll);
  var sp_top = $('#type_dt_'+id).offset().top;
  var div_top = $('#type_div').offset().top;
  $('#type_div').scrollTop(sp_top-div_top);
  typeScroll = sp_top-div_top;
}
gcategoryInit('gcategory');
</script> 
