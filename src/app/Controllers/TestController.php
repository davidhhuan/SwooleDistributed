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
use Server\CoreBase\SelectCoroutine;
use Server\Memory\Lock;
use Yoke\Security\DataCrypt;
use app\Models\AppAccountModel;
use Server\CoreBase\Controller;
use Swoole\Http\Client as SwooleHttpClient;

/**
 * 
 * @author  birdylee <birdylee_cn@163.com>
 * @since   2017年04月02日
 * @version 1.0
 */
class TestController extends Controller
{
    public function http_testContent()
    {
        $appAccount = AppAccountModel::getAccountInfo('yoke!sdcvMa950dK3La$3vcVgaUiadKb');
//        \Yoke\Util\DevUtil::dump($appAccount);
        
        $appId = $appAccount['app_id'];
        $appSecret = $appAccount['app_secret'];
        $encodingAesKey = $appAccount['encoding_aes_key'];
        $timestamp = time();
        $nonce = \Yoke\Util\StringUtil::getRandomStr(6);
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
//        \Yoke\Util\DevUtil::dump(json_encode($dataMock), false);
        
        $cli = new SwooleHttpClient('127.0.0.1', 8081); 
        $cli->post('/', ['data' => json_encode($dataMock)], function ($cli) use (
                $appId, $nonce, $token, $encodingAesKey, $timestamp) {
//            echo $cli->body;
            $body = $cli->body;
            $responseBody = \Yoke\Util\JsonUtil::decode($body);
            if ($responseBody['status'] == \Yoke\Exception\StatusCode::SUCCESS['status']) {
                $retval = $responseBody['retval'];
                $dataCrypt = new DataCrypt($appId, $token, $encodingAesKey);
                $rsDecrypt = $dataCrypt->decrypt(
                        $retval['data'], 
                        $retval['nonce'], 
                        $retval['timestamp'], 
                        $retval['signature']
                );
//                \Yoke\Util\DevUtil::dump($rsDecrypt);
                
                if ($rsDecrypt['status'] == \Yoke\Exception\StatusCode::SUCCESS['status']) {
                    $retvalDecrypt = $rsDecrypt['retval'];
                    $responseData = \Yoke\Util\JsonUtil::decode($retvalDecrypt['data']);
                    
                    //发出接口请求
                    $data = [
                        'system' => 'mall', // 系统。目前固定为 mall
                        'serviceName' => 'CredentialController', 
                        'methodName' => 'testApi', 
                        'args' => [
                            
                        ], 
                        'callback' => [ //原样返回的数据，这里作为保留字段

                        ],
                        'watermark' => [//水印，用于校验。目前只校验appId
                            'appId' => $appId, 
                        ], 
                    ];
                    $dataCrypt = new DataCrypt($appId, $responseData['accessToken'], $encodingAesKey);

                    $rsEncrypt = $dataCrypt->encrypt($data, $nonce, $timestamp);
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
                        'operation' => 'api', //操作。accessToken代表该请求是取accessToken
                        'data' => $rsEncrypt['retval']['data'], //加密后的数据
                        'token' => $responseData['accessToken'], //这里是appID
                        'nonce' => $rsEncrypt['retval']['nonce'], //6位随机数
                        'timestamp' => $rsEncrypt['retval']['timestamp'], //接口，获取服务器时间
                        'signature' => $rsEncrypt['retval']['signature'], //签名。sha1(sort([$data, $token, $nonce, $timestamp], SORT_STRING));
                    ];
                    
                    $cliApi = new SwooleHttpClient('127.0.0.1', 8081); 
                    $cliApi->post('/', ['data' => json_encode($dataMock)], function ($cliApi) use ($appId) {
                        echo $cliApi->body;
                    });
                }
            }
        });

//        $rsDecrypt = $dataCrypt->decrypt(
//                $rsEncrypt['retval']['data'], 
//                $nonce, 
//                $timestamp, 
//                $rsEncrypt['retval']['signature']
//        );
//        \Yoke\Util\DevUtil::dump($rsDecrypt, false);
        
        $this->http_output->end('a');
    }
}
