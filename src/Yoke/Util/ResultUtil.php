<?php

/*
 * PHP version 7.1+
 *
 * @copyright  No copyrights
 * @link       http://www.cnblogs.com/davidhhuan
 * @license    The MIT License (MIT) https://opensource.org/licenses/MIT
 */
namespace Yoke\Util;

use Yoke\Exception\SystemException;
/**
 * 我是类描述信息哦！
 *
 * @author  birdylee <birdylee_cn@163.com>
 * @since   2017年04月02日
 * @version 1.0
 *
 */
class ResultUtil
{

    /**
     * 结果返回
     * 
     * @param array $statusCode
     * @param \stdClass $retval
     * @return type
     * @throws SystemException
     */
    public static function returnRs(array $statusCode, $retval = null)
    {
        if (!isset($statusCode['status']) || !isset($statusCode['info'])) {
            throw new SystemException('statusCode info error');
        }
        
        if (empty($retval)) {
            $retval = new \stdClass();
        }
        if (!is_array($retval) && !is_object($retval)) {
            throw new SystemException('statusCode retval error');
        }
        
        return [
            'status' => $statusCode['status'],
            'info' => $statusCode['info'],
            'retval' => $retval, 
        ];
    }

}
