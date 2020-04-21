<?php

/**
 * Created by PhpStorm.
 * User: changle
 * Date: 16/9/23
 * Time: 下午3:00
 */
class ZcyItemUtil
{
    public function uploadZcyItemImg($gate_way,$app_key,$secret,$filePath,$fileName,$size,$fileContentType,$description){
        $rtn = Array();
        require_once('Common.php');//阿里云OSS组件
        require_once('ZcyOpenClient.php');//政采云开放平台SDK组件
        $p= new ZcyOpenClient();
        $fileTokenStrs=array();
        $fileTokenStrs['_data_']="{\"quatity\":\"2\"}";
        $stsTokenResponse= $p->sendPost($gate_way,"/open/zcy.zoss.filetoken.get","POST",$app_key,$secret,$fileTokenStrs);
        $fileFullPath = "";//商品图片全路径,示例:http://xxx-bucket.oss-cn-hangzhou.aliyuncs.com/3816471/8dcabe35-878c-4d2a-996a-5e1d3ee0ca34.png
        $fileId = "";//OSS中文件id
        $httpResponse=json_decode($stsTokenResponse,true);//json串化
        if($httpResponse['data_response']['success']=="true"){
            //获取OSS STS临时凭证
            $credentials = $httpResponse['data_response']['result'];
            $securityToken = $credentials['securityToken'];
            $accessKeySecret = $credentials['accessKeySecret'];
            $accessKeyId = $credentials['accessKeyId'];
            $bucket = $credentials['bucket'];
            $data = $credentials['data'];//文件ID数组
            $endPoint = $credentials['endPoint'];
            try{
                $fileSuffix = "";
                
                $tempArr = explode(".",json_encode($filePath));
                if(count($tempArr)>1){
                    $fileSuffix = $tempArr[count($tempArr)-1];//得到文件后缀
                }
                $pos = strrpos($data[0],"/");
                $realFilePath = $bucket."/".substr($data[0],0,$pos);
                $fileId = $data[0].".".$fileSuffix;

                $ossClient = Common::getOssClientSTS($accessKeyId, $accessKeySecret, $endPoint, $securityToken);
                var_dump($ossClient);die;
                // 上传本地文件,$response是一个ResponseCore类型
                $response = $ossClient->uploadFile($bucket, $fileId, $filePath);
                if($response&&$response->status==200){
                    $rtn['result'] = $fileId;
                    $rtn['success'] = true;
                    $rtn['error'] = "";
                    $header = $response->header;
                    $fileFullPath = $header["oss-request-url"];
                    //回传文件信息
                    $fileCallBackUri = "/open/zcy.zoss.filemeta.upload";
                    $params = Array();
                    $params['documentMetas']=Array();
                    $documentMeta1 = Array();
                    $documentMeta1['id']=$fileId;
                    $documentMeta1['fileName']=$fileName;
                    $documentMeta1['path']=$realFilePath;
                    $documentMeta1['size']=$size;
                    $documentMeta1['type']=$fileContentType;
                    $documentMeta1['description']=$description;
                    $documentMeta1['uploadTime']=date('Y-m-d h:i:s',time());
                    $params['documentMetas'][0]=$documentMeta1;
                    $fileMetaStrs = Array();
                    $fileMetaStrs['_data_'] = json_encode($params,false);
                    $fileMetaResponse= $p->sendPost($gate_way,$fileCallBackUri,"POST",$app_key,$secret,$fileMetaStrs);
                    $jfileMetaResponse = json_decode($fileMetaResponse,true);
                    if($jfileMetaResponse['data_response']['success']!="true"){
                        $rtn['error'] = "Warning ! callback file info failed!";
                    }
                }else{
                    $rtn['result'] = "";
                    $rtn['success'] = false;
                    $rtn['error'] = "upload aliyun oss failed!";
                }
            }catch(OssException $e){
                printf(__FUNCTION__ . "upload ZcyItem: FAILED\n");
                printf($e->getMessage() . "\n");
            }
        }else{
            print_r("System error!Get Zoss STS token failed!");
        }
        return $rtn;
    }
}