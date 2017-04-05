<?php
/*
 * PHP version 7.1+
 *
 * @copyright  No copyrights
 * @link       http://www.cnblogs.com/davidhhuan
 * @license    The MIT License (MIT) https://opensource.org/licenses/MIT
 */

namespace app;

use Server\Asyn\HttpClient\HttpClientPool;
use Server\SwooleDistributedServer;
use Yoke\Util\ResultUtil;
use Yoke\Util\JsonUtil;
use Server\CoreBase\ControllerFactory;
use Yoke\Exception\LogicException;
use Yoke\Exception\StatusCode;
use Swoole\Http\Request;
use Swoole\Http\Response;
use app\Lib\Security\VerifyRequest;
use Server\Coroutine\Coroutine;

/**
 * 我是类描述信息哦！
 *
 * @author  birdylee <birdylee_cn@163.com>
 * @since   2017年04月03日
 * @version 1.0
 *
 */
class YokeAppServer extends AppServer
{
    /**
     * http服务器发来消息
     * @param $request
     * @param $response
     */
    public function onSwooleRequest($request, $response)
    {
        $this->route->handleClientRequest($request);
        list($host) = explode(':', $request->header['host']??'');
        //接口请求
        if ($this->route->getPath() == '/') {
            return $this->handleApiRequest($request, $response);
        } else {
            return parent::onSwooleRequest($request, $response);
        }
    }
    
    /**
     * websocket合并后完整的消息
     * @param $serv
     * @param $fd
     * @param $data
     */
    public function onSwooleWSAllMessage($serv, $fd, $data)
    {
        parent::onSwooleWSAllMessage($serv, $fd, $data);
    }
    
    /**
     * 处理API请求
     * 
     * @param Request $request
     * @param Response $response
     * @throws LogicException
     */
    protected function handleApiRequest(Request $request, Response $response)
    {
        $responseRs = [];
        try {
            $verifyRequest = new VerifyRequest();
            list($appAccount, $requestData) = $verifyRequest->handleClientRequest($request);
            $controllerName = $requestData['data']['serviceName'];
            $controllerInstance = ControllerFactory::getInstance()->getController($controllerName);
            if ($controllerInstance != NULL) {
                $methodName = $requestData['data']['methodName'] . 'Action';
                if (!method_exists($controllerInstance, $methodName)) {
                    throw new LogicException(
                    StatusCode::REQUEST_NOT_FOUND['info'], StatusCode::REQUEST_NOT_FOUND['status']
                    );
                }

                $controllerInstance->setRequestResponse($request, $response, $controllerName, $methodName);
                Coroutine::startCoroutine([$controllerInstance, $methodName], $requestData['data']['args']);
            } else {
                throw new LogicException(
                StatusCode::REQUEST_NOT_FOUND['info'], StatusCode::REQUEST_NOT_FOUND['status']
                );
            }
        } catch (\Exception $e) {
            $responseRs = ResultUtil::returnRs(['status' => $e->getCode(), 'info' => $e->getMessage()]);
            $response->end(JsonUtil::encode($responseRs));
        }
    }
}