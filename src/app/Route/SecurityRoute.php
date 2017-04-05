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
                $appAccount = yield AppAccountModel::getAccountInfo($transData['token']);
                break;
            case 'api':
                $appAccount = yield AppAccountModel::getAccountInfoViaToken($transData['token']);
                break;
            default:
                throw new LogicException(StatusCode::BAD_REQUEST['info'], StatusCode::BAD_REQUEST['status']);
                break;
        }
        if (empty($appAccount)) {
            throw new LogicException(StatusCode::REQUEST_FORBIDDEN['status'], StatusCode::REQUEST_FORBIDDEN['info']);
        }
        
        print_r($transData);
        die();
        
        $this->clientData->path = $request->server['path_info'];
        $route = explode('/', $request->server['path_info']);
        $this->clientData->controller_name = $route[1]??null;
        $this->clientData->method_name = $route[2]??null;
    }

    /**
     * 获取控制器名称
     * @return string
     */
    public function getControllerName()
    {
        return $this->clientData->controller_name;
    }

    /**
     * 获取方法名称
     * @return string
     */
    public function getMethodName()
    {
        return $this->clientData->method_name;
    }

    public function getPath()
    {
        return $this->clientData->path;
    }

    public function getParams()
    {
        return $this->clientData->params??null;
    }
}