<?php
/**
 * Pay.php
 *
 * Part of AllinpaySDK.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Fackeronline <1077341744@qq.com>
 * @link      https://github.com/Fakeronline
 */

namespace Fakeronline\AllinpaySDK\Responses;

$mypath = dirname(__FILE__);
$datapath = str_replace(DIRECTORY_SEPARATOR.'Responses','', $mypath);

require_once $datapath.DIRECTORY_SEPARATOR.'Services'.DIRECTORY_SEPARATOR.'Response.php';
require_once $datapath.DIRECTORY_SEPARATOR.'Utils'.DIRECTORY_SEPARATOR.'Arr.php';

use Fakeronline\AllinpaySDK\Services\Response;
use Fakeronline\AllinpaySDK\Utils\Arr;

class Pay extends Response{

    protected function properties(){
        return [
            'merchantId', 'version', 'language', 'signType', 'payType', 'issuerId', 'paymentOrderId', 'orderNo', 'orderDatetime', 'orderAmount', 'payDatetime', 'payAmount', 'ext1', 'ext2', 'payResult', 'errorCode', 'returnDatetime', 'signMsg'
        ];
    }

    public function getOrderNo(){
        return Arr::get($this->value, 'orderNo');
    }

}

