<div class="tabmenu">
    <?php include template('layout/submenu');?>


    <a id="open_uploaders" href="JavaScript:void(0);" class="ncsc-btn ncsc-btn-acidblue"><i class="icon-cloud-upload"></i>上传图片</a>
</div>
<style type="text/css">

    .flex-container {
        display: -webkit-flex;
        display: flex;
        width: 88%;
        /*background-color: lightgrey;*/
        flex-wrap:wrap;
        margin: 0 auto;
    }

    .flex-item {
        /*background-color: cornflowerblue;*/
        width: 190px;
        height: 200px;
        margin: 10px;
        /*border: 1px solid grey;*/
    }
    input{
    }
    @import url("<?php echo SHOP_TEMPLATES_URL; ?>/css/zcy.css");
</style>
<div id="content">
    <!--S 分类选择区域-->
    <td>
        <label for="">全选
            <input type="checkbox" class="all"></a>
        </label>
    </td>
    <ul class="flex-container">
        <?php foreach ($output['img'] as $v){ ?>
        <li class="flex-item click">
            <input type="checkbox" class="input" value="<?php echo $v['apic_cover'];?>">
        <img src="<?php echo cthumb($v['apic_cover'], 240, $_SESSION['store_id']);?>" alt="" width="100%" height="100%">
        </li>
        <?php }?>
    </ul>

    <tr>
        <td colspan="20"><div class="pagination"> <?php echo $output['show_page'] ?> </div></td>
    </tr>
    
</div>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js"></script>

<script>

    $(".click").click(function () {
        if($(this).find("input").attr("checked") == 'checked'){
            $(this).find("input").removeAttr("checked");
        }else{
            $(this).find("input").attr("checked", "checked");
        }
    })

    $('.all').click(function () {
        if($(this).attr("checked") == 'checked'){
            $('.input').attr("checked", "checked");
        }else{
            $('.input').removeAttr("checked");
        }
    })

    $('#open_uploaders').click(function(){
        var test_list = [];
        $('input:checkbox:checked').each(function () {
            test_list.push($(this).val())
        })
        // var test_str = JSON.stringify(test_list );
        if(test_list == null || test_list == [] || test_list== ""){
            alert('请选择图片后上传')
        }else{
            // console.log(test_list);
            $.ajax({
                url:"/shop/index.php?act=zcy_image&op=upgoods",
                type:"POST",
                dataType: "JSON",
                data:{path:test_list},
                success:function (res) {
                    console.log(res);
                },
                error:function(e)
                {
                    console.log(e);
                }
            })
        }

    })

</script>