<?php

/*
 * 入口文件
 */
//header("content-type:html/text;charset=utf-8");
//获取请求url
set_time_limit(300);
 ob_end_flush();//关闭缓存
 ob_implicit_flush(true);
@$config = include "../data/config/config.ini.php";
define("InShopNC", true);
define('DEMAIN', $_SERVER['HTTP_HOST']);
define('IP', $config['db'][1]['dbhost']);
define('PORT', $config['db'][1]['dbport']);
define('USER', $config['db'][1]['dbuser']);
define('PWD', $config['db'][1]['dbpwd']);
define('DBNAME', $config['db'][1]['dbname']);
$requst_url = $_SERVER['REQUEST_URI'];
//进行拆分
$array = explode("/", $requst_url);
//默认和引用function
$func = !empty($array[2]) ? $array[2] : "getAccessToken";
$func_array = array(
    'getAccessToken',
    'getProductCategory',
    'getProductPool',
    'getProductDetail',
    'getProductImage',
    'getProductOnShelvesInfo',
    'getProductInfoFromEC',
    'getProductInventory',
    'queryCountPrice',
    'createOrder',
    'getProductInfoFromEC'
);
//print_r($config);
if (!in_array($func, $func_array)) {
    $func = "getAccessToken";
}
$post = json_decode(@$_POST['data'], false);
include "buy.php";
$buy = new buy();
$buy->$func($post);
