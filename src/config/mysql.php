<?php
/**
 * Created by PhpStorm.
 * User: zhangjincheng
 * Date: 16-7-15
 * Time: 下午4:49
 */
$config['mysql']['active'] = 'test';
$config['mysql']['test']['host'] = '172.18.107.96';
$config['mysql']['test']['port'] = '3306';
$config['mysql']['test']['user'] = 'devmall';
$config['mysql']['test']['password'] = 'devmall';
$config['mysql']['test']['database'] = 'malltest';
$config['mysql']['test']['charset'] = 'utf8';
$config['mysql']['asyn_max_count'] = 10;
return $config;