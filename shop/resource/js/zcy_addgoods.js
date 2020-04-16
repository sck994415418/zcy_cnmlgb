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
    if(Number(data_str.deep)<3){
        $('#root').val(data_str.id);
        $('#depth').val(4-Number(data_str.deep));
    }
    var deep = parseInt(data_str.deep) + 1;
    $.post('/shop/index.php?act=zcy_goods&op=category', {id : data_str.id}, function(data) {
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
function get_category(root,depth){
    var allc = 0;
    var category = new Array();
    var parms = {};
    parms.root = root;
    parms.depth = depth;
    $.ajax({
        url:"/shop/index.php?act=zcy_config&op=get_category",
        type:"post",
//		async:false,
        dataType: "JSON",
        data: JSON.stringify(parms),
        beforeSend:function(){
            $('#dataLoading').show();
        },
        complete:function(){
            $('#dataLoading').hide();
        },
        success:function(data){
            if(data.success){
                if(data.data_response.node.hasChildren){
                    c1 = data.data_response.children;
                    for(i1=0;i1<c1.length;i1++){
                        id = c1[i1].node.id;
                        pid = c1[i1].node.pid;
                        name = c1[i1].node.name;
                        level = c1[i1].node.level;
                        hasChildren = c1[i1].node.hasChildren;
                        status = c1[i1].node.status;
                        hasSpu = c1[i1].node.hasSpu;
                        category[allc] = {"pname":{"c1_name":"","c2_name":""},"id":id,"pid":pid,"name":name,"level":level,"hasChildren":hasChildren,"status":status,"hasSpu":hasSpu};
                        allc++;
                        if(hasChildren){
                            c2 = c1[i1].children;
                            for(i2=0;i2<c2.length;i2++){
                                id = c2[i2].node.id;
                                pid = c2[i2].node.pid;
                                name = c2[i2].node.name;
                                level = c2[i2].node.level;
                                hasChildren = c2[i2].node.hasChildren;
                                status = c2[i2].node.status;
                                hasSpu = c2[i2].node.hasSpu;
                                category[allc] = {"pname":{"c1_name":c1[i1].node.name,"c2_name":""},"id":id,"pid":pid,"name":name,"level":level,"hasChildren":hasChildren,"status":status,"hasSpu":hasSpu};
                                allc++;
                                if(hasChildren){
                                    c3 = c2[i2].children;
                                    for(i3=0;i3<c3.length;i3++){
                                        id = c3[i3].node.id;
                                        pid = c3[i3].node.pid;
                                        name = c3[i3].node.name;
                                        level = c3[i3].node.level;
                                        hasChildren = c3[i3].node.hasChildren;
                                        status = c3[i3].node.status;
                                        hasSpu = c3[i3].node.hasSpu;
                                        category[allc] = {"pname":{"c1_name":c1[i1].node.name,"c2_name":c2[i2].node.name},"id":id,"pid":pid,"name":name,"level":level,"hasChildren":hasChildren,"status":status,"hasSpu":hasSpu};
                                        allc++;
                                    }
                                }
                            }
                        }
                    }
                }
                $('#dataLoading').hide();
                if(confirm("即将更新"+allc+"条分类信息,请耐心等待！")){
                    for(i=0;i<allc;i++){
                        parms = {};
                        parms.id = category[i].id;
                        parms.pid = category[i].pid;
                        parms.name = category[i].name;
                        parms.level = category[i].level;
                        parms.hasChildren = category[i].hasChildren;
                        parms.status = category[i].status;
                        parms.hasSpu = category[i].hasSpu;
                        $.ajax({
                            url:"/shop/index.php?act=zcy_config&op=update_category",
                            type:"post",
                            async:false,
                            dataType: "JSON",
                            data: JSON.stringify(parms),
                            success:function(data){
                                if(data.isSuccess){
                                    if(category[i].pname.c1_name!=""){$("#c1").text(category[i].pname.c1_name + " >> ");}
                                    if(category[i].pname.c2_name!=""){$("#c2").text(category[i].pname.c2_name + " >> ");}
                                    $("#c3").text(parms.name);
                                    $("#cc").text("("+(i+1)+'/'+allc+")"+Math.round((i+1) / allc * 10000) / 100.00 + "%");
                                }else{
                                    $("#error_info").append("<p style='font-color:#FF0000;'>"+data.resultMsg+": "+parms.name+"</p>");
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
                alert(data.resultCode+":"+data.resultMsg);
            }
        },
        error : function(msg) {
            alert(msg.responseText);
        }
    })
}



$('input[nctype="buttonNextStep"]').click(function(){
    var root = $("#root").val();
    var depth = $("#depth").val();

    get_category(root,depth);
})

