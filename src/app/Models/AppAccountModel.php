<?php

/*
 * PHP version 7.1+
 *
 * @copyright  No copyrights
 * @link       http://www.cnblogs.com/davidhhuan
 * @license    The MIT License (MIT) https://opensource.org/licenses/MIT
 */
namespace app\Models;

use Server\CoreBase\Model;

/**
 * 我是类描述信息哦！
 *
 * @author  birdylee <birdylee_cn@163.com>
 * @since   2017年04月03日
 * @version 1.0
 *
 */
class AppAccountModel extends BaseModel
{
    /**
     * 
     * @return string 表名
     */
    public static function getTableName()
    {
        return self::DATA_TABLE_PREFIX . 'app_account';
    }
    
    /**
     * 同步获取账号信息
     * 
     * @param type $appId
     * @return type
     */
    public static function getAccountInfo($appId = null)
    {
        $miner = get_instance()->mysql_pool->getSync()
                    ->select('*')
                    ->from(static::getTableName());
        if (!is_null($appId)) {
            $miner = $miner->where('app_id', $appId);
        }
        $result = $miner->pdoQuery();
        $info = false;
        if (isset($result['result']) && is_array($result['result']) && count($result['result'])) {
            $info = current($result['result']);
        }
        
        return $info;
    }
    
    /**
     * 根据token同步获取账号信息
     * 
     * @param type $token
     */
    public static function getAccountInfoViaToken($hashKey)
    {
        $info = false;
        $result = get_instance()->redis_pool->getSync()->hGetAll($hashKey);
        if (is_array($result)) {
            $info = $result;
        }
        
        return $info;
    }
    
    /**
     * 
     */
    public function getAccessToken()
    {
        $accessToken = md5(uniqid('YOKE', true));
        $expired = 30*86400;
        $hashKey = $accessToken;
        yield $this->redis_pool->getCoroutine()->hMset(
                $hashKey, 
                $this->appAccount
                );
        yield $this->redis_pool->getCoroutine()->expire($hashKey, $expired);
        
        return [
            'accessToken' => $accessToken,
            'expiredTimestamp' => time() + $expired
        ];
    }
    

}
