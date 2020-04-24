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
function get_brand(categoryId,pageSize,pageNo,beginModifiedDate,endModifiedDate){
	var allc = 0;
	var parms = {};
	if(pageSize == ""){
		pageSize = 100;
	}
	if(pageNo == ""){
		pageNo = 1;
	}
	parms.categoryId = categoryId;
	parms.pageSize = pageSize;
	parms.pageNo = pageNo;
	parms.beginModifiedDate = beginModifiedDate;
	parms.endModifiedDate = endModifiedDate;
	$.ajax({
		url:"/shop/index.php?act=zcy_config&op=get_brand",
		type: "POST",
		dataType: "JSON",
		async:false,
		data: JSON.stringify(parms),
		beforeSend:function(){
			$('#dataLoading').show();
		},
		complete:function(){
			$('#dataLoading').hide();
		},
		success:function(data){
			// console.log(data.response_data.data);
			// return false;
			if(data.isSuccess){
				allc = data.response_data.total;		//所有品牌数量
				allp = Math.ceil(allc/pageSize);		//所有品牌页数
				cc = data.response_data.data.length;	//当前页品牌数量
				if(confirm("即将更新"+ allc +"条品牌信息,请耐心等待！")){

					$('#dataLoading').show();
					$("#c1").text("第"+pageNo+"页: ");
					$("#c2").text(((pageNo-1)*pageSize+1)+"~"+((pageNo-1)*pageSize+cc));
					for(i=0;i<cc;i++){
						parms1 = {};
						parms1.id = data.response_data.data[i].id;
						parms1.fullName = data.response_data.data[i].fullName;
						parms1.logo = data.response_data.data[i].logo;
						parms1.status = data.response_data.data[i].status;
						parms1.createdAt = data.response_data.data[i].createdAt;
						parms1.updatedAt = data.response_data.data[i].updatedAt;
						parms1.auditStatus = data.response_data.data[i].auditStatus;
						$.ajax({
							url:"/shop/index.php?act=zcy_config&op=update_brand",
							type:"post",
							async:false,
							dataType: "JSON",
							data: JSON.stringify(parms1),
							// beforeSend:function(){
							// 	$('#dataLoading').show();
							// },
							// complete:function(){
							// 	$('#dataLoading').hide();
							// },
							success:function(datas){
								// console.log(datas)
								if(datas.isSuccess){
									// console.log('datas_success')
									// console.log(datas)
									// alert(datas)
									$("#c3").text(parms1.fullName);
									// console.log(parms1.fullName)
									$("#cc").text("("+(i+1)+'/'+allc+")"+Math.round((i+1) / allc * 10000) / 100.00 + "%");
								}else{
									// console.log('datas_error')
									// console.log(datas)
									// alert(datas)
									// return false;
									// console.log(parms1.fullName)
									$("#error_info").append("<p style='color:#FF0000;'>"+parms1.fullName+"(" + parms1.id + "): "+datas.resultMsg+"</p>");
								}
							},
							error : function(msg) {
								// console.log('msg_success'+msg)
								// alert(msg.responseText)
								$("#error_info").append("<p>"+msg.resultMsg+"</p>");
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
			alert(msg.resultMsg);
		}
	})
}


	
$('input[nctype="buttonNextStep"]').click(function(){
	var root = $("#root").val();
	get_brand(root,'','','','');
})

