<?php

/*
 * PHP version 7.1+
 *
 * @copyright  No copyrights
 * @link       http://www.cnblogs.com/davidhhuan
 * @license    The MIT License (MIT) https://opensource.org/licenses/MIT
 */
namespace Fayho\Exception;

use Fayho\Exception\StatusCode;
/**
 * 只能在系统运行时才能发现错误抛出的异常
 *
 * @author  birdylee <birdylee_cn@163.com>
 * @since   2017年04月02日
 * @version 1.0
 * @link http://php.net/manual/zh/class.runtimeexception.php 
 *
 */
class SystemException extends \RuntimeException
{

    /**
     * 构造方法
     *
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message, $code = StatusCode::SERVER_ERROR['status'], \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
