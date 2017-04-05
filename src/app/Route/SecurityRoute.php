<?php
/**
 * Created by PhpStorm.
 * User: zhangjincheng
 * Date: 16-7-15
 * Time: 下午3:11
 */

namespace App\Route;

use Server\Route\IRoute;
use Yoke\Exception\LogicException;
use Yoke\Exception\StatusCode;
use Yoke\Util\JsonUtil;
use app\Models\AppAccountModel;
use app\Lib\Util\ObjectUtil;
use Yoke\Security\DataCrypt;


class SecurityRoute implements IRoute
{
    private $clientData;
    
    private $loader;

    public function __construct()
    {
        $this->clientData = new \stdClass();
        $this->loader = get_instance()->loader;
    }

    /**
     * 设置反序列化后的数据 Object
     * @param $data
     * @return \stdClass
     */
    public function handleClientData($data)
    {
        $this->clientData = $data;
        return $this->clientData;
    }

    /**
     * 处理http request
     * 
     * @param Swoole\Http\Request $request
     */
    public function handleClientRequest($request)
    {
        if (!isset($request->post) || !isset($request->post['data'])) {
            throw new LogicException(StatusCode::BAD_REQUEST['info'], StatusCode::BAD_REQUEST['status']);
        }
        $transData = JsonUtil::decode($request->post['data']);
        if (empty($transData)) {
            throw new LogicException(StatusCode::BAD_REQUEST['info'], StatusCode::BAD_REQUEST['status']);
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
                throw new LogicException(StatusCode::BAD_REQUEST['info'], StatusCode::BAD_REQUEST['status']);
                break;
        }
        if (empty($appAccount)) {
            throw new LogicException(StatusCode::REQUEST_FORBIDDEN['status'], StatusCode::REQUEST_FORBIDDEN['info']);
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
            throw new LogicException(StatusCode::SERVER_ERROR['status'], StatusCode::SERVER_ERROR['info']);
        }
        if ($rsDecrypt['status'] != StatusCode::SUCCESS['status']) {
            throw new LogicException($rsDecrypt['status'], $rsDecrypt['info']);
        }
        $requestData = JsonUtil::decode($rsDecrypt['retval']['data']);
        if (empty($requestData)) {
            throw new LogicException(StatusCode::REQUEST_FORBIDDEN['status'], StatusCode::REQUEST_FORBIDDEN['info']);
        }
        $this->checkRequestData($appAccount, $requestData);
        ObjectUtil::instance()->setRequestData($requestData);
        
        $this->clientData->path = $request->server['path_info'];
        $route = explode('/', $request->server['path_info']);
        $this->clientData->controllerName = $requestData['serviceName']??null;
        $this->clientData->methodName = $requestData['methodName']??null;
    }

    /**
     * 获取控制器名称
     * @return string
     */
    public function getControllerName()
    {
        return $this->clientData->controllerName;
    }

    /**
     * 获取方法名称
     * @return string
     */
    public function getMethodName()
    {
        return $this->clientData->methodName;
    }

    public function getPath()
    {
        return $this->clientData->path;
    }

    public function getParams()
    {
        return $this->clientData->params??null;
    }
    
    /**
     * @param array $appAccount
     * @param array $requestData
     */
    private function checkRequestData($appAccount, $requestData)
    {
        if (!isset($requestData['watermark']) || !isset($requestData['watermark']['appId'])) {
            throw new LogicException(
                    StatusCode::REQUEST_FORBIDDEN['status'], 
                    StatusCode::REQUEST_FORBIDDEN['info']
                    );
        }
        
        if ($appAccount['app_id'] != $requestData['watermark']['appId']) {
            throw new LogicException(
                    StatusCode::REQUEST_FORBIDDEN['status'], 
                    StatusCode::REQUEST_FORBIDDEN['info']
                    );
        }
    }
}