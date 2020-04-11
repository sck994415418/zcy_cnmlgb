<?php ?>
<html>
    <head>
        <title>提交表单</title>
        <script src="http://www.nrwspt.com/data/resource/js/jquery.js"></script>
    </head>
    <body>
        <form action="https://api.weixin.qq.com/sns/oauth2/access_token" method="get">
            <input type="hidden" name="appid" value="<?php echo $output['appid']; ?>" />
            <input type="hidden" name="secret" value="<?php echo $output['secret']; ?>" />
            <input type="hidden" name="code" value="<?php echo $output['code']; ?>" />
            <input type="hidden" name="grant_type" value="authorization_code" />
        </form>
        <script type="text/javascript">
                $.ajax({
                    url: "https://api.weixin.qq.com/sns/oauth2/access_token",
                    type: "get",
                    data: $("form").serialize(),
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                    }
                });
        </script>
    </body>
</html>
