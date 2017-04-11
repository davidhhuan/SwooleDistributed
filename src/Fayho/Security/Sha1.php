<?php

/*
 * PHP version 7.1+
 *
 * @copyright  No copyrights
 * @link       http://www.cnblogs.com/davidhhuan
 * @license    The MIT License (MIT) https://opensource.org/licenses/MIT
 */
namespace Fayho\Security;

use Fayho\Util\ResultUtil;
use Fayho\Exception\StatusCode;

/**
 * 我是类描述信息哦！
 *
 * @author  birdylee <birdylee_cn@163.com>
 * @since   2017年04月02日
 * @version 1.0
 *
 */
class Sha1
{

    /**
	 * 用SHA1算法生成安全签名
	 * @param string $token 票据
	 * @param string $timestamp 时间戳
	 * @param string $nonce 随机字符串
	 * @param string $encrypt 密文消息
	 */
	public static function getSignature($token, $timestamp, $nonce, $encrypt_msg)
	{
		//排序
		try {
			$array = array($encrypt_msg, $token, $timestamp, $nonce);
			sort($array, SORT_STRING);
			$str = implode($array);
            return ResultUtil::returnRs(StatusCode::SUCCESS, ['signature' => sha1($str)]);
		} catch (Exception $e) {
            return ResultUtil::returnRs(StatusCode::COMPUTE_SIGNATURE_ERROR);
		}
	}

}
