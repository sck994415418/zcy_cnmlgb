<?php
/**
 * Query.php
 *
 * Part of allinpay.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Fackeronline <1077341744@qq.com>
 * @link      https://github.com/Fakeronline
 */

namespace Fakeronline\AllinpaySDK;

use Fakeronline\AllinpaySDK\Services\Request;
use Exception;
use Fakeronline\AllinpaySDK\Tools\Encrypt;

class Query extends Request{

    protected function properties(){
        return [
            'merchantId', 'version', 'signType', 'orderNo', 'orderDatetime', 'queryDatetime', 'signMsg'
        ];
    }

    public function __construct($url, $merchantId, $key){

        parent::__construct($url, $merchantId, $key);
        $this->setSignType()->setVersion(self::VERSION_15);   //���ü�������
    }

    final public function parameter($orderNo, $orderDatetime, $queryDatetime){

        if( empty($orderNo) || empty($orderDatetime) || empty($queryDatetime) ){
            throw new Exception('������š�����ʱ��Ͷ�����ѯʱ��Ϊ��Ҫ����!');
        }

        $this->value['orderNo'] = $orderNo;
        $this->value['orderDatetime'] = $orderDatetime;
        $this->value['queryDatetime'] = $queryDatetime;

        $this->postData = $this->sort($this->properties, $this->value);

        $this->postData['signMsg'] = Encrypt::MD5_sign($this->postData, $this->config['md5key']);

        return $this;
    }

}
 

