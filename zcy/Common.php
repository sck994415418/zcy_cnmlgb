<?php

if (is_file(__DIR__ . '/../autoload.php')) {
    require_once __DIR__ . '/../autoload.php';
}

use OSS\OssClient;
use OSS\Core\OssException;

/**
 * Class Common
 *
 * 示例程序【zcy/*.php】 的Common类，用于获取OssClient实例和其他公用方法
 */
class Common
{
    /**
     * 根据STS 临时凭证，得到一个OssClient实例
     *
     * @return OssClient 一个OssClient实例
     */
    public static function getOssClientSTS($accessKeyId, $accessKeySecret, $endpoint, $securityToken)
    {
        try {
            include_once('../src/OSS/OssClient.php');
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, false,$securityToken);
        } catch (OssException $e) {
            printf(__FUNCTION__ . "creating OssClient instance: FAILED\n");
            printf($e->getMessage() . "\n");
            return null;
        }
        return $ossClient;
    }

    public static function println($message)
    {
        if (!empty($message)) {
            echo strval($message) . "\n";
        }
    }
}