$(function(){
	$.ajax({
		url : "index.php?act=store_task&op=load_task",
		type : "post",
		dataType : "json",
		success : function(data) {
		}
	});
});