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
        $miner = get_instance()->mysql_pool->dbQueryBuilder
                    ->select('*')
                    ->from(static::getTableName());
        if (!is_null($appId)) {
            $miner = $miner->where('app_id', $appId);
        }
        $result = yield $miner->coroutineSend();
        
        return $result;
    }
    
    /**
     * 根据token同步获取账号信息
     * 
     * @param type $token
     */
    public static function getAccountInfoViaToken($token)
    {
        return yield get_instance()->redis_pool->getCoroutine()->hMget($token);
    }
    

}
