<?php

/*
 * PHP version 7.1+
 *
 * @copyright  No copyrights
 * @link       http://www.cnblogs.com/davidhhuan
 * @license    The MIT License (MIT) https://opensource.org/licenses/MIT
 */
namespace Yoke\Util;

/**
 * 我是类描述信息哦！
 *
 * @author  birdylee <birdylee_cn@163.com>
 * @since   2017年04月05日
 * @version 1.0
 *
 */
class ArrayUtil
{

    /**
     * 使用了参考Yii的CMap::mergeArray，不过原方法当索引是数字时不覆盖，这里需要的功能是覆盖
     *
     * @param array $a
     * @param array $b
     * @return array
     */
    public static function mergeArray($a, $b)
    {
        $args = func_get_args();
        $res = array_shift($args);
        while (!empty($args))
        {
            $next = array_shift($args);
            foreach ($next as $k => $v)
            {
                if (is_array($v) && isset($res[$k]) && is_array($res[$k]))
                    $res[$k] = self::mergeArray($res[$k], $v);
                else
                    $res[$k] = $v;
            }
        }
        return $res;
    }
    
    /**
     * 数组键值转换字符串
     * 
     * @param type $data
     * @return \stdClass
     */
    public static function toString($data)
    {
        if (empty($data) && !is_object($data)) {
            return new \stdClass();
        }
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if (is_array($val)) {
                    $data[$key] = self::toString($val);
                } else {
                    !is_bool($val) && !is_object($data) && $data[$key] = (string) $val;
                }
            }
        } else if (!is_bool($data) && !is_object($data)) {
            $data = (string) $data;
        }
        return $data;
    }

}
