<?php

/*
 * PHP version 7.1+
 *
 * @copyright  No copyrights
 * @link       http://www.cnblogs.com/davidhhuan
 * @license    The MIT License (MIT) https://opensource.org/licenses/MIT
 */
namespace app\Lib\Util;

/**
 * 对象存放空间
 *
 * @author  birdylee <birdylee_cn@163.com>
 * @since   2017年04月05日
 * @version 1.0
 *
 */
class ObjectUtil
{
    private static $objectUtil;
    
    private function __construct()
    {}
    
    /**
     * 
     * @return ObjectUtil
     */
    public static function instance()
    {
        if (is_null(static::$objectUtil)) {
            static::$objectUtil = new ObjectUtil;
        }
        
        return static::$objectUtil;
    }

    /**
     *
     * @var array 
     * @see app\Models\AppAccountModel#getAccountInfo
     */
    private $appAccount = [];
    
    /**
     * 
     * @param array $appAccount
     * @see $appAccount
     */
    public function setAppAccount(array $appAccount)
    {
        $this->appAccount = $appAccount;
    }
    
    /**
     * 
     * @return array
     * @see $appAccount
     */
    public function getAppAccount()
    {
        return $this->appAccount;
    }
    
    /**
     * 接口请求数据
     * 
     * @var type 
     */
    private $requestData = [];
    
    /**
     * 
     * @param array $requestData
     * @see $requestData
     */
    public function setRequestData(array $requestData)
    {
        $this->requestData = $requestData;
    }
    
    /**
     * 
     * @return array
     * @see $requestData
     */
    public function getRequestData()
    {
        return $this->requestData;
    }
}
