<?php
/**
 * Created by PhpStorm.
 * User: tmtbe
 * Date: 16-7-15
 * Time: 下午4:49
 */
$config['database']['active'] = 'test';
$config['database']['test']['host'] = '172.18.107.96';
$config['database']['test']['port'] = '3306';
$config['database']['test']['user'] = 'devmall';
$config['database']['test']['password'] = 'devmall';
$config['database']['test']['database'] = 'malltest';
$config['database']['test']['charset'] = 'utf8';
$config['database']['asyn_max_count'] = 10;
return $config;