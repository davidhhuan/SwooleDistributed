<?php

/*
 * PHP version 7.1+
 *
 * @copyright  No copyrights
 * @link       http://www.cnblogs.com/davidhhuan
 * @license    The MIT License (MIT) https://opensource.org/licenses/MIT
 */
namespace Yoke\Util;

use Yoke\Util\VarDumper;
/**
 * 开发集合
 *
 * @author  birdylee <birdylee_cn@163.com>
 * @since   2017年04月05日
 * @version 1.0
 *
 */
class DevUtil
{

    /**
     * 判断是否debug模式
     * 
     * @return boolean
     */
    public static function isDebug()
    {
        $rs = defined('RUN_MODE') ?? false;
        if ($rs && RUN_MODE != 'dev') {
            $rs = false;
        }
        
        return $rs;
    }
    
    /**
     * 
     * @param type $obj
     */
    public static function dump($obj, $isDie = true)
    {
        if (php_sapi_name() == 'cli') {
            echo "\n";
            print_r($obj);
            echo "\n";
        } else {
            VarDumper::dump($obj, 10, true);
        }
        
        if ($isDie) {
            die();
        }
    }

}
