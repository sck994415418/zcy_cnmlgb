<?php
    include "TopSdk.php";
    date_default_timezone_set('Asia/Shanghai'); 
    //修改
    // $c = new TopClient;
    // $c->appkey = '23471426';
    // $c->secretKey = '4daf4bcb1554cdd9341fb83992251305';

    // $req = new TradeVoucherUploadRequest;
    // $req->setFileName("example");
    // $req->setFileData("@/Users/xt/Downloads/1.jpg");
    // $req->setSellerNick("奥利奥官方旗舰店");
    // $req->setBuyerNick("101NufynDYcbjf2cFQDd62j8M/mjtyz6RoxQ2OL1c0e/Bc=");
    // var_dump($c->execute($req));

    //修改
    // $req2 = new TradeVoucherUploadRequest;
    // $req2->setFileName("example");

    // $myPic = array(
    //         'type' => 'application/octet-stream',
    //         'content' => file_get_contents('/Users/xt/Downloads/1.jpg')
    //         );
    // $req2->setFileData($myPic);
    // $req2->setSellerNick("办工师");
    // $req2->setBuyerNick("101NufynDYcbjf2cFQDd62j8M/mjtyz6RoxQ2OL1c0e/Bc=");

    $appkey = '23471426';

    $secret = '4daf4bcb1554cdd9341fb83992251305';

    $c = new TopClient;
    $c->appkey = $appkey;
    $c->secretKey = $secret;
    $req = new OpenimUsersAddRequest;
    $userinfos = new Userinfos;
    $userinfos->nick="king";
    $userinfos->icon_url="http://xxx.com/xxx";
    $userinfos->email="uid@taobao.com";
    $userinfos->mobile="18600000000";
    $userinfos->taobaoid="tbnick123";
    $userinfos->userid="imuser123";
    $userinfos->password="xxxxxx";
    $userinfos->remark="demo";
    $userinfos->extra="{}";
    $userinfos->career="demo";
    $userinfos->vip="{}";
    $userinfos->address="demo";
    $userinfos->name="demo";
    $userinfos->age="123";
    $userinfos->gender="M";
    $userinfos->wechat="demo";
    $userinfos->qq="demo";
    $userinfos->weibo="demo";
    $req->setUserinfos(json_encode($userinfos));
    $resp = $c->execute($req);

    var_dump(json_encode($resp));
?>