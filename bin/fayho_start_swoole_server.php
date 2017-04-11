<?php
/**
 * Created by PhpStorm.
 * User: zhangjincheng
 * Date: 16-6-17
 * Time: 下午1:56
 */
defined('RUN_MODE') || define('RUN_MODE', 'dev');

require_once __DIR__ . '/../vendor/autoload.php';
$worker = new \app\FayhoAppServer();
Server\SwooleServer::run();