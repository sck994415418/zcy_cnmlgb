
// 更新分类
function get_brand(categoryId,pageSize,pageNo,beginModifiedDate,endModifiedDate){
	// $('#dataLoading').show();
	// setTimeout(function () {
	// 	$('#dataLoading').hide();
	// },5000)
	// return false;
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

			if(data.isSuccess){
				allc = data.response_data.total;		//所有品牌数量
				allp = Math.ceil(allc/pageSize);		//所有品牌页数
				cc = data.response_data.data.length;	//当前页品牌数量
				$('#dataLoading').hide();
				if(confirm("即将更新"+ allc +"条品牌信息,请耐心等待！")){
					$.ajax({
						url:"/shop/index.php?act=zcy_config&op=update_brand_all",
						type:"post",
						async:false,
						dataType: "JSON",
						data: {pages:allp,pageSize:pageSize,total:data.response_data.total},
						beforeSend:function(){
							$('#dataLoading').show();
						},
						complete:function(){
							$('#dataLoading').hide();
						},
						success:function(datas){
							$("#c3").text(datas.resultMsg);
							// $('#dataLoading').hide();

						},
						error : function(msg) {
							$("#c3").text(msg.resultMsg);
							// $('#dataLoading').hide();
							// $("#error_info").append("<p>"+msg.resultMsg+"</p>");
						}
					})

					// return false;

				}else{
					$("#c3").text("更新取消！");
				}
			}else{
				alert(data.resultMsg);
			}
		},
		error : function(data) {
			alert(data.resultMsg);
		}
	})
}

