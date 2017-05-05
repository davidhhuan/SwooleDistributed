<?php

/*
 * PHP version 7.1+
 *
 * @copyright  No copyrights
 * @link       http://www.cnblogs.com/davidhhuan
 * @license    The MIT License (MIT) https://opensource.org/licenses/MIT
 */
namespace Fayho\Exception;

/**
 * 逻辑异常
 *
 * 程序中的逻辑错误产生的异常
 *
 * @author  birdylee <birdylee_cn@163.com>
 * @since   2017年04月03日
 * @version 1.0
 *
 */
class LogicException extends \LogicException
{

    /**
     * 构造方法
     * 
     * @param string     $message
     * @param int        $code
     * @param mixed      $args
     * @param \Throwable $previous
     */
    public function __construct($message, $code = StatusCode::UNKNOW_ERROR['status'], $args = null, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
