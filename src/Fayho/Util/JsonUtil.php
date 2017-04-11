<?php

/*
 * PHP version 7.1+
 *
 * @copyright  No copyrights
 * @link       http://www.cnblogs.com/davidhhuan
 * @license    The MIT License (MIT) https://opensource.org/licenses/MIT
 */
namespace Fayho\Util;

/**
 * json处理类
 *
 * @author  birdylee <birdylee_cn@163.com>
 * @since   2017年04月03日
 * @version 1.0
 *
 */
class JsonUtil
{

    /**
     * json decocode
     *
     * > 空对象不要转换成空数组
     *
     * ###测试数据
     *
     * ```
     * $str = json_encode([
     *   'a'=>"[):][:-o][:@]",
     *   'b'=>[],
     *   'c'=>(object)[],
     *   'd'=>new stdClass(),
     *   'e'=>'{"json":{"content":"json\u6570\u636e\u53d1\u9001\u6d4b\u8bd5"}}',
     *   'f' => [
     *      'a'=>"[):][:-o][:@]",
     *       'b'=>[],
     *       'c'=>(object)[],
     *       'd'=>new stdClass(),
     *       'e'=>'{"json":{"content":"json\u6570\u636e\u53d1\u9001\u6d4b\u8bd5"}}'
     *    ],
     *    'g' => true,
     *    'h' => 123,
     *    'i' => 12.3,
     *    'j' => '123',
     *    'k' => '12.3'
     * ]);
     * ```
     * @param string $json json字符串
     * @param bool $isNotDecode 是否未decode过
     * @return \stdClass|string|\stdClass|array|mixed|string|array|mixed|string|\stdClass
     * @author birdylee <birdylee_cn@163.com>
     * @since 2017.04.03
     */
    public static function decode($json, $isNotDecode = true)
    {
        if ($isNotDecode) {
            $json = json_decode($json);
            $isNotDecode = false;
        }
        if (is_object($json)) {
            $json = (array) $json;
            if (empty($json)) {
                static $emptyObject;
                if (null === $emptyObject) {
                    $emptyObject = new \stdClass();
                }
                return $emptyObject;
            } else {
                return self::decode($json, false);
            }
        } elseif (is_array($json)) {
            if (empty($json)) {
                return [];
            }
            foreach ($json as $key => $value) {
                $json[$key] = self::decode($value, false);
            }
        } elseif (! is_bool($json)) {
            return (string) $json;
        }
        return $json;
    }
    
    /**
     * 
     * @param mixed $value the data to be encoded.
     * @param int $options the encoding options. For more details please refer to
     * <http://www.php.net/manual/en/function.json-encode.php>. Default is `JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE`.
     * @return string the encoding result.
     */
    public static function encode($value, $options = 0, $depth = 512)
    {
        return json_encode($value, $options, $depth);
    }

}
