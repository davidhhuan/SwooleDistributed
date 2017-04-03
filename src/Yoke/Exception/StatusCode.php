<?php

/*
 * PHP version 7.1+
 *
 * @copyright  No copyrights
 * @link       http://www.cnblogs.com/davidhhuan
 * @license    The MIT License (MIT) https://opensource.org/licenses/MIT
 */

namespace Yoke\Exception;

/**
 * 我是接口描述信息哦！
 *
 * @author  birdylee <birdylee_cn@163.com>
 * @since   2017年04月02日
 * @version 1.0
 *
 */
interface StatusCode
{
    /**
     * 成功
     *
     * @var array
     */
    const SUCCESS  = [
        'status' => 200, 
        'info' => 'success', 
    ];

    /**
     * 请求参数错误
     *
     * @var array
     */
    const BAD_REQUEST = [
        'status' => 400, 
        'info' => 'Bad request', 
    ];

    /**
     * 无权限
     *
     * @var array
     */
    const REQUEST_FORBIDDEN = [
        'status' => 403, 
        'info' => 'Forbidden',
    ];

    /**
     * 未找到(无此种请求)
     *
     * @var array
     */
    const REQUEST_NOT_FOUND = [
        'status' => 404, 
        'info' => 'Not found', 
    ];

    /**
     * 服务请求参数错误
     *
     * @var array
     */
    const SERVICE_BAD_REQUEST = [
        'status' => 407, 
        'info' => 'Bad request', 
    ];

    /**
     * 请求超时
     *
     * @var array
     */
    const REQUEST_TIME_OUT = [
        'status' => 408, 
        'info' => 'Time out', 
    ];

    /**
     * 重复请求
     *
     * @var array
     */
    const REQUEST_CONFLICT = [
        'status' => 409, 
        'info' => 'conflict', 
    ];

    /**
     * 服务器未知错误
     *
     * @var array
     */
    const SERVER_ERROR = [
        'status' => 500, 
        'info' => 'Server error',
    ];
    
    /**
     * 未知错误
     * 
     * @var array
     */
    const UNKNOW_ERROR = [
        'status' => 4000, 
        'info' => 'Unknow error', 
    ];
    
    /**
     * 数据库服务器未知错误
     *
     * @var array
     */
    const DB_SERVER_ERROR = [
        'status' => 4001,
        'info' => 'Db server error', 
    ];

    /**
     * 服务帐号不存在
     *
     * @var array
     */
    const SERVER_ACCOUNT_ERROR = [
        'status' => 4002, 
        'info' => 'Server account error', 
    ];

    /**
     * 服务器解码错误
     * @var array
     */
    const SERVER_DECODE_ERROR = [
        'status' => 4003, 
        'info' => 'Server decode error', 
    ];

    /**
     * 方法参数错误
     * @var array
     */
    const INVALID_ARGUMENT = [
        'status' => 5001,
        'info' => 'Invalid argument', 
    ];

    /**
     * 数据未找到
     *
     * @var array
     */
    const DATA_NOT_FOUND = [
        'status' => 5002, 
        'info' => 'Data not found', 
    ];

    /**
     * 重复的事件
     *
     * @var array
     */
    const DUPLICATE_EVENT = [
        'status' => 5003, 
        'info' => 'Duplicate event', 
    ];

    /**
     * 溢出（过大）
     *
     * @var array
     */
    const OVER_FLOW = [
        'status' => 5004, 
        'info' => 'Out of memory. Too large', 
    ];

    /**
     * 溢出（过小）
     *
     * @var array
     */
    const UNDER_FLOW = [
        'status' => 5005, 
        'info' => 'Out of memory. Too small'
    ];

    /**
     * 不为空
     *
     * @var array
     */
    const NOT_EMPTY = [
        'status' => 5006, 
        'info' => 'Not empty', 
    ];

    /**
     * access_token 失效
     * 
     * @var array
     */
    const ACCESS_TOKEN_INVALID = [
        'status' => 5007, 
        'info' => 'Access token invalid.'
    ];
    
    /**
     * sha加密生成签名失败
     * 
     * @var array
     */
    const COMPUTE_SIGNATURE_ERROR = [
        'status' => 5008, 
        'info' => 'Compute signature error', 
    ];
    
    /**
     * aes 加密失败
     * 
     * @var array
     */
    const ENCRYPT_AES_ERROR = [
        'status' => 5009, 
        'info' => 'Encrypt aes error', 
    ];
    
    /**
     * aes 解密失败
     * 
     * @var array
     */
    const DECRYPT_AES_ERROR = [
        'status' => 5010, 
        'info' => 'Decrypt aes error', 
    ];
    
    /**
     * base64加密失败
     * 
     * @var array
     * 
     */
    const ENCODE_BASE64_ERROR = [
        'status' => 5011, 
        'info' => 'Encode base64 error', 
    ];
    
    /**
     * base64解密失败
     * 
     * @var array
     */
    const DECODE_BASE64_ERROR = [
        'status' => 5012, 
        'info' => 'Decode base64 error', 
    ];
    
    /**
     * 解密后得到的buffer非法
     * 
     * @var array
     */
    const ILLEGAL_BUFFER = [
        'status' => 5013, 
        'info' => 'Illegal buffer', 
    ];
    
    /**
     * AES key有问题
     * 
     * @var array
     */
    const ILLEGAL_AES_KEY = [
        'status' => 5014, 
        'info' => 'Illegal aes key', 
    ];
    
    /**
     * Validate signature error
     * 
     * @var array 
     */
    const VALIDATE_SIGNATURE_ERROR = [
        'status' => 5015, 
        'info' => 'Validate signature error',
    ];
    
}
