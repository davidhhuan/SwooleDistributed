<?php
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
 * Created by PhpStorm.
 * User: zhangjincheng
 * Date: 16-9-19
 * Time: 下午2:36
 */
class YokeAppServer extends AppServer
{
    /**
     * http服务器发来消息
     * @param $request
     * @param $response
     */
    public function onSwooleRequest(Request $request, Response $response)
    {
        $error404 = false;
        $controllerInstance = null;
        $this->route->handleClientRequest($request);
        list($host) = explode(':', $request->header['host']??'');
        //接口请求
        if ($this->route->getPath() == '/') {
            return $this->handleApiRequest($request, $response);
        } else {
            $controllerName = $this->route->getControllerName();
            $controllerInstance = ControllerFactory::getInstance()->getController($controllerName);
            if ($controllerInstance != null) {
                if($this->route->getMethodName()=='_consul_health'){//健康检查
                    $response->end('ok');
                    $controllerInstance->destroy();
                    return;
                }
                $methodName = $this->config->get('http.method_prefix', '') . $this->route->getMethodName();
                if (!method_exists($controllerInstance, $methodName)) {
                    $methodName = 'defaultMethod';
                }
                try {
                    $controllerInstance->setRequestResponse($request, $response, $controllerName, $methodName);
                    Coroutine::startCoroutine([$controllerInstance, $methodName], $this->route->getParams());
                    return;
                } catch (\Exception $e) {
                    call_user_func([$controllerInstance, 'onExceptionHandle'], $e);
                }
            } else {
                $error404 = true;
            }
        }
        
        if ($error404) {
            if ($controllerInstance != null) {
                $controllerInstance->destroy();
            }
            //先根据path找下www目录
            $wwwPath = $this->getHostRoot($host) . $this->route->getPath();
            $result = httpEndFile($wwwPath, $request, $response);
            if (!$result) {
                $response->header('HTTP/1.1', '404 Not Found');
                if (!isset($this->cache404)) {//内存缓存404页面
                    $template = $this->loader->view('server::error_404');
                    $this->cache404 = $template->render();
                }
                $response->end($this->cache404);
            }
        }
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
            $controllerName = $requestData['serviceName'];
            $controllerInstance = ControllerFactory::getInstance()->getController($controllerName);
            if ($controllerInstance != NULL) {
                $methodName = $requestData['methodName'] . 'Action';
                if (!method_exists($controllerInstance, $methodName)) {
                    throw new LogicException(
                    StatusCode::REQUEST_NOT_FOUND['info'], StatusCode::REQUEST_NOT_FOUND['status']
                    );
                }

                $controllerInstance->setRequestResponse($request, $response, $controllerName, $methodName);
                Coroutine::startCoroutine([$controllerInstance, $methodName], $requestData['args']);
            } else {
                throw new LogicException(
                StatusCode::REQUEST_NOT_FOUND['info'], StatusCode::REQUEST_NOT_FOUND['status']
                );
            }
        } catch (\Exception $e) {
            $responseRs = ResultUtil::returnRs(['status' => $e->getCode(), 'info' => $e->getMessage()]);
        }

        $response->end(JsonUtil::encode($responseRs));
    }
}