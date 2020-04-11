<?php
/**
 * Response.php
 *
 * Part of AllinpaySDK.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Fackeronline <1077341744@qq.com>
 * @link      https://github.com/Fakeronline
 */

namespace Fakeronline\AllinpaySDK\Services;

$mypath = dirname(__FILE__);
$datapath = str_replace(DIRECTORY_SEPARATOR.'Services','', $mypath);

require_once $datapath.DIRECTORY_SEPARATOR.'Utils'.DIRECTORY_SEPARATOR.'Arr.php';
require_once $datapath.DIRECTORY_SEPARATOR.'Tools'.DIRECTORY_SEPARATOR.'Encrypt.php';

use Fakeronline\AllinpaySDK\Tools\Encrypt;
use Exception;
use Fakeronline\AllinpaySDK\Utils\Arr;

abstract class Response{

    use ServiceTrait;

    const ENCRYPT_MD5 = 0;  //订单上送和交易结果通知都使用MD5签名
//    const ENCRYPT_OTHER = 1;    //商户用MD5算法验签上送订单，通联交易结果通知使用证书签名 TODO：暂时只支持MD5加密方式

    protected $properties = [];
    protected $key;
    protected $value;
    protected $postData;
    public $errorMsg;

    public function __construct($key){

        if(empty($key)){
            throw new Exception('未传入解密KEY!');
        }

        $this->key = $key;
        $properties = $this->properties();
        $this->properties = array_merge($this->properties, (array)$properties);

    }

    abstract protected function properties();

    final protected function verify(){

        if(empty($this->value)){
            return false;
        }
		
        //$this->value = $this->sort($this->properties, $this->value);
        foreach($this->properties as $v){
            if(isset($this->value[$v]) && $this->value[$v]!=''){
                $this->postData[$v] = $this->value[$v];
            }
        }

        $originalSign = $this->postData['signMsg'];
        unset($this->postData['signMsg']);

        $sign = '';
        //TODO：因为目前仅支持MD5加密方式
        if($this->postData['signType'] == self::ENCRYPT_MD5){
            $sign = Encrypt::MD5_sign($this->postData, $this->key);
        }

        return $sign === $originalSign;
    }

    public function chkVerify($args){

        $this->value = $args;

        if($this->verify()){
            $this->errorMsg = Arr::get($this->value, 'errorCode') ? new Exception('', $this->value['errorCode']) : '';
            return true;
        }

        return false;
    }

}
