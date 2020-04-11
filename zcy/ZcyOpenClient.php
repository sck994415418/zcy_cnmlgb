<?php

/**
 * @Desc 政采云开放平台客户端
 * Date: 16/9/20
 * Time: 上午9:52
 */
class ZcyOpenClient
{
    /*
     GET请求接口数据
     */
    function   sendGet($gate_way,$uri,$method,$app_key,$secret,$params){
        $headers=array();
        $headers['Content-Type']="application/x-www-form-urlencoded; charset=utf-8";
        $headers['Accept']="application/json";
        $headers['X-Ca-Timestamp']=$this->getMillisecond();  //需要使毫秒，只有15分钟有效
        $headers['X-Ca-Key']=$app_key;

        $purl= $uri.$params;
        $url=$gate_way.$purl;

        //获取签名原始字符串
        $sigString = $this->buildStringToSign($purl,$headers, null, $method);
        //通过秘钥加密，生成签名
        $signature = $this->signature($sigString, $secret);

        //设置签名头
        $headers["X-Ca-Signature-Headers"] = $this->getXCaSignatureHeaders($headers);
        $headers["X-Ca-Signature"] = $signature;

        return  $result = $this->sendRequest($url, $method, null, $headers, $http_code, $error);
    }

    /*
     POST请求接口数据
     */
    function   sendPost($gate_way,$uri,$method,$app_key,$secret,$data){
        $url=$gate_way.$uri;

        $headers=array();
        $headers['Content-Type']="application/x-www-form-urlencoded; charset=utf-8";
        $headers['Accept']="application/json";
        $headers['X-Ca-Format']="json2";
        $headers['X-Ca-Timestamp']=$this->getMillisecond();  //需要使毫秒，只有15分钟有效
        $headers['X-Ca-Key']=$app_key;

        //获取签名原始字符串
        $sigString = $this->buildStringToSign($uri,$headers, $data, $method);
        
		//通过秘钥加密，生成签名
        $signature = $this->signature($sigString, $secret);

        //设置签名头
        $headers["X-Ca-Signature-Headers"] = $this->getXCaSignatureHeaders($headers);
        $headers["X-Ca-Signature"] = $signature;

        return  $result = $this->sendRequest($url, $method, $data, $headers, $http_code, $error);
    }

    /*
     POST/multipart请求接口数据
     */
    function   sendPostMultipart($gate_way,$uri,$method,$app_key,$secret,$data,$file_one){
        $url=$gate_way.$uri;

        $headers=array();
        $headers['Content-Type']="multipart/form-data; charset=utf-8;";//只要是文件上传的api,必须是multipart/form-data; charset=utf-8;
        $headers['Accept']="application/json";
        $headers['X-Ca-Format']="json2";
        $headers['X-Ca-Timestamp']=$this->getMillisecond();  //需要是毫秒，只有15分钟有效
        $headers['X-Ca-Key']=$app_key;

        //获取签名原始字符串
        $sigString = $this->buildStringToSign($uri,$headers, $data, $method);
        //通过秘钥加密，生成签名
        $signature = $this->signature($sigString, $secret);

        //设置签名头
        $headers["X-Ca-Signature-Headers"] = $this->getXCaSignatureHeaders($headers);
        $headers["X-Ca-Signature"] = $signature;

        return  $result = $this->sendPostMultipartRequest($url, $method, $data, $file_one, $headers, $http_code, $error);
    }

    /**
     * 通过HmacSHA256以及base64加密生成签名
     *
     * @param $source string 待加密的字符串
     * @param $secret string 加密密码
     * @return string 加密签名
     */
    public function signature($source, $secret)
    {
        //hash_hmac 第4个参数必须指定为true 表示输出二进制byte数组，否则是16进制字串
        $signString = hash_hmac('sha256', $source, $secret,true);
        return base64_encode ($signString );
    }

    /**
     * 基于请求报文构建签名
     *
     * 构建签名的方法如下：
     * 1、填写HTTP方法： GET 或 POST 。加完拼上换行符："\n"
     * 2、检测header里是否有 "Accept"字段，有的话就把Accept字段里的值拼上，加上换行符："\n"。
     * 3、检测header里是否有 "Content-MD5"字段，有的话就把Content-MD5字段里的值拼上，加上换行符："\n"。
     * 4、检测header里是否有 "Content-Type"字段，有的话就把Content-Type字段里的值拼上，加上换行符："\n"。
     * 5、检测header里是否有 "Date"字段，有的话就把Date字段里的值拼上，加上换行符："\n"。
     * 6、将header里以"X-Ca-"开头的字段的Key,Value以字典序排序，然后以"Key:value\n"的方式一个个拼接。
     * 7、将接口访问URI拼接进去，不带query参数部分
     * 8、如果有参数，拼接个 "?"，并将URI上的参数和post请求body中的参数合并，按key的字典序排序，
     * 然后以"key=value"的方式一个个拼接，中间以"&"间隔。body中的参数如果值是list只使用第一个元素。
     *
     * @param $uri string 请求URI包括路径中的参数
     * @param $headers array 请求头
     * @param $param array POST请求中的body参数
     * @param $method string  请求方法 GET/POST
     * @throws Exception
     * @return string 签名字符串
     */
    public function buildStringToSign($uri, $headers, $param, $method)
    {

        $lf = "\n";

        //拼接请求方法
        $sb = $method . $lf;

        //拼接非X-CA头
        if (isset($headers["Accept"])) {
            $sb .= $headers["Accept"];
        }

        $sb .= $lf;

        if (isset($headers["Content-MD5"])) {
            $sb .= $headers["Content-MD5"];
        }

        $sb .= $lf;

        if (isset($headers["Content-Type"])) {
            $sb .= $headers["Content-Type"];
        }

        $sb .= $lf;

        if (isset($headers["Date"])) {
            $sb .= $headers["Date"];
        }
        $sb .= $lf;


        //拼接X-CA头和参数
        $sb .= $this->buildHeaders($headers) . $this->buildResource($uri, $param);

        return $sb;
    }

    /**
     * 使用请求头进行签名
     *
     * 将header里以"X-Ca-"开头的字段的Key,Value以字典序排序，然后以"Key:value\n"的方式一个个拼接。
     *
     * @param $headers array 请求报文头
     * @return string headers签名字符串
     */
    private function buildHeaders($headers)
    {
        $XCA = 'X-Ca-';
        if ($headers == null) {
            return '';
        }

        //通过key按字典升序排序
        ksort($headers);

        $valueSb = '';
        foreach ($headers as $key => $value) {
            $position = strpos($key, $XCA);
            if (is_long($position) && ($position == 0)) {
                //拼装key:value字符串
                $valueSb .= $key . ':' . $value . "\n";
            }
        }
        return $valueSb;
    }

    /**
     * 获取X-Ca-Signature-Headers的值
     *
     * 把入参headers里以"X-Ca-"开头的Key,进行额外的单独拼接，以","分隔,形成单独的字符串。
     *
     * @param $headers array 请求报文头
     * @return string ","分隔的key列表字符串
     */
    public function getXCaSignatureHeaders($headers)
    {
        $XCA = 'X-Ca-';
        if ($headers == null) {
            return '';
        }

        $firstKey = true;
        $keySb = '';
        foreach ($headers as $key => $value) {
            $position = strpos($key, $XCA);
            if (is_long($position) && ($position == 0)) {
                //key内容以","分隔拼装
                if (!$firstKey) {
                    $keySb .= ',';
                }
                $keySb .= $key;


                $firstKey = false;
            }
        }

        return $keySb;
    }

    /**
     * 基于访问路径和参数构造签名
     *
     * 将路径上的参数和body中的参数进行合并排序后，重新组装为url
     *
     * @param $uri string 请求URI
     * @param $param array POST请求body参数 ，如果参数值是个数组，只使用第一个值
     * @return string 重新组装后的url
     */
    private function buildResource($uri, $param)
    {
        //处理入参param 如果参数值是个数组，只使用第一个值
        if (is_array($param)) {
            $param = $this->convertParam($param);
        }

        //解析uri
        $uriParts = explode('?', $uri);
        $path = $uriParts[0];
        $query = null;
        if(sizeof($uriParts) >= 2) {
            $query = $uriParts[1];
        }

        //解析url中的参数
        $queryParams = $this->convertUrlQuery($query);

        //与body中的参数合并
        if (is_array($param)) {
            $queryParams = array_merge($queryParams, $param);
        }

        //按key字典序排序
        ksort($queryParams);

        //重新拼装url
        $sb = $path;
        if ($queryParams) {
            $sb .= '?' . $this->getUrlQuery($queryParams);
        }

        return $sb;
    }

    /**
     * 对body中的参数进行转换处理
     *
     * 如果参数值为数据，那么只取第一个元素。没有考虑值为数组嵌套的情况。
     *
     * @param $params
     * @return array
     */
    private function convertParam($params)
    {
        $newParams = array();
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $newValue = $value[0];
            } else {
                $newValue=$value;
            }
            $newParams[$key] = $newValue;
        }
        return $newParams;

    }


    /**
     * 解析查询参数
     *
     * @param $query string url查询参数 name=test&password=111
     * @return array key-value数组
     */
    private function convertUrlQuery($query)
    {
        if (!$query) {
            //返回空数组
            return array();
        }

        $queryParts = explode('&', $query);

        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params;
    }

    /**
     * 通过参数数组返回url查询参数
     *
     * @param $params array 参数数组
     * @return string url查询参数 name=test&password=111
     */
    private function getUrlQuery($params)
    {
        if (!$params) {
            return null;
        }

        $tmp = array();
        foreach ($params as $key => $value) {
            $tmp[] = $key . '=' . $value;
        }

        return implode('&', $tmp);
    }

    /*
     时间戳 豪秒
     */
    public function getMillisecond(){
        list($s1,$s2)=explode(' ',microtime());
        return (float)sprintf('%.0f',(floatval($s1)+floatval($s2))*1000);
    }

    /*
     * POST请求数据
     * */
    public function sendRequest($url, $method, $data, $header = null, &$http_code = 0, &$error = '', $time_out = false)
    {
        try {

            $ch = curl_init($url);
            if (stripos($url, "https://") !== false) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            }
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $time_out ? : 30);
            curl_setopt($ch, CURLOPT_TIMEOUT, $time_out ? : 60);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $fields_string = '';
            if ($data) {
                if (is_array($data)) {
                    foreach ($data as $k => $v) {
                        $fields_string .= $k . '=' . urlencode($v) . '&';
                    }
                    $fields_string = rtrim($fields_string, '&');
                } else {
                    $fields_string = $data;
                }
            }


            switch ($method) {
                case 'GET':
                    if ($fields_string) {
                        if (strpos($url, '?')) {

                            curl_setopt($ch, CURLOPT_URL, $url . '&' . $fields_string);
                        } else {
                            curl_setopt($ch, CURLOPT_URL, $url . '?' . $fields_string);
                        }
                    }
                    break;
                case 'POST':
                    curl_setopt($ch, CURLOPT_POST, true);

                    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                    break;
                default:
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                    break;
            }
            if (!is_array($header)) {
                $header = [];
            }

            $httpheader = array();
            foreach ($header as $key => $value) {
                array_push($httpheader, $key . ': ' . $value);
            }
            if ($httpheader) {
                $$httpheader.=
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
            }
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return $response;
        } catch (Exception $e) {
            $error = $e->getMessage();
            return null;
        }
    }

    /*
    POST/multipart请求数据
    */
    public function sendPostMultipartRequest($url, $method, $data, $file_one, $header = null, &$http_code = 0, &$error = '', $time_out = false)
    {
        try {

            $ch = curl_init($url);
            if (stripos($url, "https://") !== false) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            }
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $time_out ? : 30);
            curl_setopt($ch, CURLOPT_TIMEOUT, $time_out ? : 60);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $fields_string = '';
            if ($data) {
                if (is_array($data)) {
                    foreach ($data as $k => $v) {
                        $fields_string .= $k . '=' . urlencode($v) . '&';
                    }
                    $fields_string = rtrim($fields_string, '&');
                } else {
                    $fields_string = $data;
                }
            }


            switch ($method) {
                case 'GET':
                    if ($fields_string) {
                        if (strpos($url, '?')) {

                            curl_setopt($ch, CURLOPT_URL, $url . '&' . $fields_string);
                        } else {
                            curl_setopt($ch, CURLOPT_URL, $url . '?' . $fields_string);
                        }
                    }
                    break;
                case 'POST':
                    curl_setopt($ch, CURLOPT_POST, true);

                    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1 );
                    /**
                    处理文件上传
                     **/
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->buildMultipartParams($fields_string,$file_one));
                    break;
                default:
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                    break;
            }
            if (!is_array($header)) {
                $header = [];
            }

            $httpheader = array();
            foreach ($header as $key => $value) {
                array_push($httpheader, $key . ': ' . $value);
            }
            if ($httpheader) {
                $$httpheader.=
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
            }
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return $response;
        } catch (Exception $e) {
            $error = $e->getMessage();
            return null;
        }
    }

    public function buildMultipartParams($fields_string,$file_one){
        if($fields_string){
            $kv = explode("=",$fields_string);
            $form_data = array(
                $kv[0]=>$kv[1],
                "fileone"=>$file_one
            );
            return $form_data;
        }

    }
}