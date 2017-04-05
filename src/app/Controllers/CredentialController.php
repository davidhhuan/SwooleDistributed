<?php

/*
 * PHP version 7.1+
 *
 * @copyright  No copyrights
 * @link       http://www.cnblogs.com/davidhhuan
 * @license    The MIT License (MIT) https://opensource.org/licenses/MIT
 */
namespace app\Controllers;

use Server\Components\Consul\ConsulServices;
use Server\CoreBase\Controller;
use Server\CoreBase\SelectCoroutine;
use Server\Memory\Lock;
use Yoke\Security\DataCrypt;
use app\Models\AppAccountModel;
use Yoke\Exception\LogicException;
use Yoke\Exception\StatusCode;

/**
 * 
 * @author  birdylee <birdylee_cn@163.com>
 * @since   2017年04月02日
 * @version 1.0
 */
class CredentialController extends BaseController
{
    /**
     * 获取accessToken
     * 
     * @param string $appSecret
     */
    public function getAccessTokenAction($appSecret)
    {
        if ($this->appAccount['app_secret'] != $appSecret) {
            throw new LogicException(
                    StatusCode::REQUEST_FORBIDDEN['info'], 
                    StatusCode::REQUEST_FORBIDDEN['status']
                    );
        }
    }
}
