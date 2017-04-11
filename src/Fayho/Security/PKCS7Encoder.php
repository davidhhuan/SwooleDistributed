<?php

/*
 * PHP version 7.1+
 *
 * @copyright  No copyrights
 * @link       http://www.cnblogs.com/davidhhuan
 * @license    The MIT License (MIT) https://opensource.org/licenses/MIT
 */
namespace Fayho\Security;

use Fayho\Exception\StatusCode;
use Fayho\Util\ResultUtil;

/**
 * 提供基于PKCS7算法的加解密接口.
 *
 * @author  birdylee <birdylee_cn@163.com>
 * @since   2017年04月02日
 * @version 1.0
 *
 */
class PKCS7Encoder
{
    public static $blockSize = 32;

    /**
	 * 对需要加密的明文进行填充补位
	 * @param $text 需要进行填充补位操作的明文
	 * @return 补齐明文字符串
	 */
	public static function encode($text)
	{
		$blockSize = static::$blockSize;
		$textLength = strlen($text);
		//计算需要填充的位数
		$amountToPad = $blockSize - ($textLength % $blockSize);
		if ($amountToPad == 0) {
			$amountToPad = $blockSize;
		}
		//获得补位所用的字符
		$padChr = chr($amountToPad);
		$tmp = "";
		for ($index = 0; $index < $amountToPad; $index++) {
			$tmp .= $padChr;
		}
		return $text . $tmp;
	}
    
    /**
	 * 对解密后的明文进行补位删除
	 * @param decrypted 解密后的明文
	 * @return 删除填充补位后的明文
	 */
	public static function decode($text)
	{
		$pad = ord(substr($text, -1));
		if ($pad < 1 || $pad > 32) {
			$pad = 0;
		}
		return substr($text, 0, (strlen($text) - $pad));
	}

}
