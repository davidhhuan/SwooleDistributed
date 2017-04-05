<?php

/*
 * PHP version 7.1+
 *
 * @copyright  No copyrights
 * @link       http://www.cnblogs.com/davidhhuan
 * @license    The MIT License (MIT) https://opensource.org/licenses/MIT
 */
namespace app\Lib\Security;

use Swoole\Http\Request;
use Yoke\Exception\LogicException;
use Yoke\Exception\StatusCode;
use Yoke\Util\JsonUtil;
use app\Models\AppAccountModel;
use app\Lib\Util\ObjectUtil;
use Yoke\Security\DataCrypt;

/**
 * 校验请求
 *
 * @author  birdylee <birdylee_cn@163.com>
 * @since   2017年04月05日
 * @version 1.0
 *
 */
class VerifyRequest
{
    /**
     * 
     * @param Request $request
     * @return array
     * @throws LogicException
     */
    public function handleClientRequest(Request $request)
    {
        if (!isset($request->post) || !isset($request->post['data'])) {
            throw new LogicException(
                    StatusCode::BAD_REQUEST['info'], 
                    StatusCode::BAD_REQUEST['status']
                    );
        }
        $transData = JsonUtil::decode($request->post['data']);
        if (empty($transData)) {
            throw new LogicException(
                    StatusCode::BAD_REQUEST['info'], 
                    StatusCode::BAD_REQUEST['status']
                    );
        }
        
        $appAccount = [];
        switch ($transData['operation']) {
            case 'accessToken': 
                $appAccount = AppAccountModel::getAccountInfo($transData['token']);
                break;
            case 'api':
                $appAccount = AppAccountModel::getAccountInfoViaToken($transData['token']);
                break;
            default:
                throw new LogicException(
                        StatusCode::BAD_REQUEST['info'], 
                        StatusCode::BAD_REQUEST['status']
                        );
                break;
        }
        if (empty($appAccount)) {
            throw new LogicException(
                    StatusCode::REQUEST_FORBIDDEN['info'], 
                    StatusCode::REQUEST_FORBIDDEN['status']
                    );
        }
        ObjectUtil::instance()->setAppAccount($appAccount);
        
        $dataCrypt = new DataCrypt($appAccount['app_id'], $transData['token'], $appAccount['encoding_aes_key']);
        $rsDecrypt = $dataCrypt->decrypt(
                $transData['data'], 
                $transData['nonce'], 
                $transData['timestamp'], 
                $transData['signature']
        );
        if (!isset($rsDecrypt['status'])) {
            throw new LogicException(
                    StatusCode::SERVER_ERROR['info'], 
                    StatusCode::SERVER_ERROR['status']
                    );
        }
        if ($rsDecrypt['status'] != StatusCode::SUCCESS['status']) {
            throw new LogicException($rsDecrypt['status'], $rsDecrypt['info']);
        }
        $requestData = JsonUtil::decode($rsDecrypt['retval']['data']);
        if (empty($requestData)) {
            throw new LogicException(
                    StatusCode::REQUEST_FORBIDDEN['info'], 
                    StatusCode::REQUEST_FORBIDDEN['status']
                    );
        }
        $this->checkRequestData($appAccount, $requestData);
        ObjectUtil::instance()->setRequestData($requestData);
        
        return [
            $appAccount, 
            $requestData,
        ];
    }
    
    /**
     * @param array $appAccount
     * @param array $requestData
     */
    private function checkRequestData($appAccount, $requestData)
    {
        if (!isset($requestData['watermark']) || !isset($requestData['watermark']['appId'])) {
            throw new LogicException(
                    StatusCode::REQUEST_FORBIDDEN['info'], 
                    StatusCode::REQUEST_FORBIDDEN['status']
                    );
        }
        
        if ($appAccount['app_id'] != $requestData['watermark']['appId']) {
            throw new LogicException(
                    StatusCode::REQUEST_FORBIDDEN['info'], 
                    StatusCode::REQUEST_FORBIDDEN['status']
                    );
        }
    }

}
