<?php
	ini_set("display_errors", "On");
	error_reporting(E_ALL | E_STRICT);
	require_once("src/JPush/JPush.php");

    $app_key = '48dac1cb88f87fb9561a33ff';
    $master_secret = 'dbb71af71f2f49e355a70777';

    // 初始化
    $client = new JPush($app_key, $master_secret);

    $payload = $client->push()
    ->setPlatform(array('ios', 'android'))
    ->addAlias('alias145')
    // ->addTag(array('teacher'))
    ->setNotificationAlert('请您尽快处理订单')
    ->addAndroidNotification('请您尽快处理订单','notification title',1,array('time'=>time(),'type'=>2))
    ->addIosNotification("请您尽快处理订单",'iOS sound',JPush::DISABLE_BADGE, true,'iOS category',array('time'=>time(),'type'=>2))
    ->setMessage("请您尽快处理订单",'msg title','type',array('time'=>time(),'type'=>2))
    // ->setOptions(100000, 3600, null, false)
    ->build();  

    $response = $client->schedule()->createSingleSchedule("请您尽快处理订单", $payload, array("time"=>"2016-09-2 16:00:00"));

    echo 'Result=' . json_encode($response);
?>