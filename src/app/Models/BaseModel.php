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
class BaseModel extends Model
{

    const DATA_TABLE_PREFIX = 'yokem_';
    
    /**
     * 表名
     * 
     * @return string
     */
    public static function getTableName()
    {
        return null;
    }

}
