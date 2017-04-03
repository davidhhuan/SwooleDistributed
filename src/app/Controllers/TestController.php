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
        $encodingAesKey = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG";
        $token = "pamtest";
        $timestamp = "1409304348";
        $nonce = "xxxxxx";
        $appId = "wxb11529c136998cb6";
        
        $data = [
            'a' => 'aa', 
            'b' => 'bb', 
            'c' => [
                'c1' => '有中文', 
                'c2' => 'c2', 
            ],
        ];
        $dataCrypt = new DataCrypt($appId, $token, $encodingAesKey);
        
        $rsEncrypt = $dataCrypt->encrypt($data, $nonce, $timestamp);
        print_r($rsEncrypt);
        
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
