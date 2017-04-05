<?php

/*
 * PHP version 7.1+
 *
 * @copyright  No copyrights
 * @link       http://www.cnblogs.com/davidhhuan
 * @license    The MIT License (MIT) https://opensource.org/licenses/MIT
 */
namespace app\Controllers;

use Server\CoreBase\Controller;
use Yoke\Util\ResultUtil;
use Yoke\Util\JsonUtil;
use Yoke\Exception\StatusCode;
use Yoke\Security\DataCrypt;
use Yoke\Util\ArrayUtil;

/**
 * 我是类描述信息哦！
 *
 * @author  birdylee <birdylee_cn@163.com>
 * @since   2017年04月03日
 * @version 1.0
 *
 */
class BaseController extends Controller
{
    protected $appAccount;
    
    protected $requestData;
    
    /**
     * 
     * @param string $controllerName
     * @param string $methodName
     */
    protected function initialization($controllerName, $methodName)
    {
        parent::initialization($controllerName, $methodName);
        
        $this->appAccount = \app\Lib\Util\ObjectUtil::instance()->getAppAccount();
        $this->requestData = \app\Lib\Util\ObjectUtil::instance()->getRequestData();
        if (empty($this->appAccount) || empty($this->requestData)) {
            throw new \Yoke\Exception\LogicException(
            \Yoke\Exception\StatusCode::BAD_REQUEST['info'], \Yoke\Exception\StatusCode::BAD_REQUEST['status']
            );
        }
    }
    
    /**
     * 销毁
     */
    public function destroy()
    {
        parent::destroy();
        $this->appAccount = null;
        $this->requestData = null;
    }
    
    /**
     * 发送回API
     * 
     * @param array $rs Yoke\Util\ResultUtil::returnRs
     */
    public function sendApi($resultUtil)
    {
        if ($resultUtil['status'] == StatusCode::SUCCESS['status']) {
            $resultUtil['retval'] = ArrayUtil::mergeArray(
                    $resultUtil['retval'], 
                    [
                        'callback' => $this->requestData['callback'], 
                    ]
                    );
            $dataCrypt = new DataCrypt(
                    $this->appAccount['app_id'], 
                    $this->requestData['token'], 
                    $this->appAccount['encoding_aes_key']
                    );
            $resultUtil = $dataCrypt->encrypt(
                    $resultUtil['retval'], 
                    $this->requestData['nonce'], 
                    $this->requestData['timestamp']
                    );
        }
        
        $rs = JsonUtil::encode([$resultUtil['status'], $resultUtil['info'], $resultUtil['retval']]);
        //http请求
        if (empty($this->fd)) {
            $this->http_output->setContentType('application/json');
            $this->http_output->end($rs);
        } else {
            $this->send($rs);
        }
    }

}
