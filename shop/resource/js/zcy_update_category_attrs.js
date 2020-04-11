// 选择商品分类
function selClass($this){
	$('.wp_category_list').css('background', '');
	$('#dataLoading').show();
    $("#commodityspan").hide();
    $("#commoditydt").show();
    $("#commoditydd").show();
    $this.siblings('li').children('a').attr('class', '');
    $this.children('a').attr('class', 'classDivClick');
    var data_str = '';
    eval('data_str = ' + $this.attr('data-param'));
	$('#root').val(data_str.id);
    var deep = parseInt(data_str.deep) + 1;
    $.getJSON('/shop/index.php?act=zcy_config&op=ajax_get_category', {id : data_str.id}, function(data) {
        if (data.isSuccess) {
            $('input[nctype="buttonNextStep"]').attr('disabled', false);
            $('#class_div_' + deep).children('ul').html('').end()
                .parents('.wp_category_list:first').removeClass('blank')
                .parents('.sort_list:first').nextAll('div').children('div').addClass('blank').children('ul').html('');
            $.each(data.response_data, function(i, n){
                $('#class_div_' + deep).children('ul').append('<li data-param="{id:'
                        + n.id +',deep:'+ deep +'}"><a class="" href="javascript:void(0)"><i class="icon-double-angle-right"></i>'
                        + n.name + '</a></li>')
                        .find('li:last').click(function(){
                            selClass($(this));
                        });
            });
        } else {
            $('#class_div_' + data_str.deep).parents('.sort_list:first').nextAll('div').children('div').addClass('blank').children('ul').html('');
            disabledButton();
        }
        $('#dataLoading').hide();
    });
}
function disabledButton() {
    if ($('#class_id').val() != '') {
        $('input[nctype="buttonNextStep"]').attr('disabled', false).css('cursor', 'pointer');
    } else {
        $('input[nctype="buttonNextStep"]').attr('disabled', true).css('cursor', 'auto');
    }
}

$(function(){
    //自定义滚定条
    $('#class_div_1').perfectScrollbar();
    $('#class_div_2').perfectScrollbar();
    $('#class_div_3').perfectScrollbar();

    // ajax选择分类
    $('li[nctype="selClass"]').click(function(){
        selClass($(this));
    });

    // 返回分类选择
    $('a[nc_type="return_choose_sort"]').unbind().click(function(){
        $('#class_id').val('');
        $('#t_id').val('');
        $("#commodityspan").show();
        $("#commoditydt").hide();
        $('#commoditydd').html('');
        $('.wp_search_result').hide();
        $('.wp_sort').show();
    });
    
});


// 更新分类
function get_category_attrs(root){
	var allc = 0;
	var category = new Array();
	var id = root;
	$.ajax({
		url:"/shop/index.php?act=zcy_config&op=ajax_get_category_id",
		async:true,
		dataType: "JSON",
		data: "id="+id,
		beforeSend:function(){
			$('#dataLoading').show();
		},
		complete:function(){
			$('#dataLoading').hide();
		},
		success:function(data){
			if(data.isSuccess){
				category = data.response_data
				allc = category.length;
				$('#dataLoading').hide();
				if(confirm("即将更新"+allc+"条分类信息的属性,请耐心等待！")){
					for(i=0;i<allc;i++){
						parms = {};
						parms.id = category[i].id;
						parms.name = category[i].name;
						$.ajax({
							url:"/shop/index.php?act=zcy_config&op=update_category_attrs",
							type:"get",
							async:false,
							dataType: "JSON",
							data: "id="+parms.id,
							success:function(data){
								if(data.isSuccess){
									$("#c3").text(parms.name);
									$("#cc").text("("+(i+1)+'/'+allc+")"+Math.round((i+1) / allc * 10000) / 100.00 + "%");
								}else{
									$("#error_info").append("<p style='color:#FF0000;'>"+parms.name+"(" + parms.id + "): "+data.resultMsg.resultMsg+"</p>");
								}
							},
							error : function(msg) {
								$("#error_info").append("<p>"+msg.responseText+"</p>");
							}
						})
					}
					$("#c1").text("");
					$("#c2").text("");
					$("#c3").text("更新完成！");
				}else{
					$("#c3").text("更新取消！");
				}
			}else{
				alert(data.resultMsg);
			}
		},
		error : function(msg) {
			alert(msg.responseText);
		}
	})
}


	
$('input[nctype="buttonNextStep"]').click(function(){
	var root = $("#root").val();
	get_category_attrs(root);
})

