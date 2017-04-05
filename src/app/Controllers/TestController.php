<?php

/*
 * PHP version 7.1+
 *
 * @copyright  No copyrights
 * @link       http://www.cnblogs.com/davidhhuan
 * @license    The MIT License (MIT) https://opensource.org/licenses/MIT
 */
namespace app\Controllers;

use Server\Components\Consul\ConsulServices;
use Server\CoreBase\Controller;
use Server\CoreBase\SelectCoroutine;
use Server\Memory\Lock;
use Yoke\Security\DataCrypt;
use app\Models\AppAccountModel;

/**
 * 
 * @author  birdylee <birdylee_cn@163.com>
 * @since   2017年04月02日
 * @version 1.0
 */
class TestController extends BaseController
{
    public function http_testContent()
    {
        $appAccount = AppAccountModel::getAccountInfo('yoke!sdcvMa950dK3La$3vcVgaUiadKb');
        
        print_r($appAccount);
        die();
        
        $encodingAesKey = "Xv12#20LogpAftmMfgtrad_-RFasdfvcXLHMtUiOdv>vZzAdfv*5bn)hgbCVdGtB";
        $appSecret = '!wedFxZPpi(6$Xbm>fd123*5FgHjvmBc';
        $timestamp = "1409304348";
        $nonce = "xxxxxx";
        $appId = "yoke!sdcvMa950dK3La$3vcVgaUiadKb";
        $token = $appId;
        
        $data = [
            'system' => 'mall', // 系统。目前固定为 mall
            'serviceName' => 'CredentialController', 
            'methodName' => 'getAccessToken', 
            'args' => [
                'appSecret' => $appSecret, 
            ], 
            'callback' => [ //原样返回的数据，这里作为保留字段

            ],
            'watermark' => [//水印，用于校验。目前只校验appId
                'appId' => $appId, 
            ], 
        ];
        $dataCrypt = new DataCrypt($appId, $token, $encodingAesKey);
        
        $rsEncrypt = $dataCrypt->encrypt($data, $nonce, $timestamp);
        print_r($rsEncrypt);
        
        $dataMock = [
            'transmission' => [
                'mode' => 'security', //传输方式，固定值
                'version' => '1.0', //传输版本
                'client' => [
                    'platform' => 'android', //客户端。ios|android|wap|pc，全小写
                    'info' => [
                        //APP(iOS|Android)
                        'dNumber' => '111111', //设备号
                        'dBrand' => 'meizu', //设备品牌。暂时为空
                        'dOsVersion' => '6.0', //设备操作系统版本
                    ],
                ],
            ], 
            'operation' => 'accessToken', //操作。accessToken代表该请求是取accessToken
            'data' => $rsEncrypt['retval']['data'], //加密后的数据
            'token' => $token, //这里是appID
            'nonce' => $rsEncrypt['retval']['nonce'], //6位随机数
            'timestamp' => $rsEncrypt['retval']['timestamp'], //接口，获取服务器时间
            'signature' => $rsEncrypt['retval']['signature'], //签名。sha1(sort([$data, $token, $nonce, $timestamp], SORT_STRING));
        ];
        
        print_r(json_encode($dataMock));
        
        $rsDecrypt = $dataCrypt->decrypt(
                $rsEncrypt['retval']['data'], 
                $nonce, 
                $timestamp, 
                $rsEncrypt['retval']['signature']
        );
        print_r($rsDecrypt);
        
        $this->http_output->end('a');
    }
}
