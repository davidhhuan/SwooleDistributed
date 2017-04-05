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

}
