<?php
/*
 * PHP version 7.1+
 *
 * @copyright  No copyrights
 * @link       http://www.cnblogs.com/davidhhuan
 * @license    The MIT License (MIT) https://opensource.org/licenses/MIT
 */
namespace Fayho\Security;

use Fayho\Security\Prpcrypt;
use Fayho\Exception\StatusCode;
use Fayho\Exception\SystemException;
use Fayho\Security\Sha1;
use Fayho\Util\ResultUtil;
use Fayho\Util\StringUtil;

/**
 * 数据加密/解密
 *
 * @author  birdylee <birdylee_cn@163.com>
 * @since   2017年04月02日
 * @version 1.0
 *
 */
class DataCrypt
{
    const RANDOM_NONCE_LENGTH = 6;
    
    /**
     * appID
     *
     * @var string
     */
    private $appId;
    
    /**
     * 密钥
     *
     * @var string
     */
    private $token;
    
    /**
     * 加密串
     *
     * @var string
     */
    private $encodingAesKey;
    
    /**
     * 
     * @param type $appId
     * @param type $token
     * @param type $encodingAesKey
     */
    public function __construct($appId, $token, $encodingAesKey)
    {
        $this->checkData($appId, $token, $encodingAesKey);
        
        $this->appId = $appId;
        $this->token = $token;
        $this->encodingAesKey = $encodingAesKey;
    }
    
    /**
     * 检查数据s
     * 
     * @param type $appId
     * @param type $token
     * @param type $encodingAesKey
     */
    private function checkData($appId, $token, $encodingAesKey)
    {
        if (strlen($encodingAesKey) != 64) {
			throw new SystemException(StatusCode::ILLEGAL_AES_KEY['info'], StatusCode::ILLEGAL_AES_KEY['status']);
		}
    }
    
    /**
     * 数据加密
     * 
     * @param type $data
     * @param type $nonce
     * @param type $timestamp
     * 
     * @return array 
     * ```
     * [
     *  'status' => '', 
     *  'info' => '', 
     *  'retval' => [
     *      'data' => '', 
     *      'signature' => '', 
     *      'timestamp' => '', 
     *      'nonce' => '', 
     *  ], 
     * ]
     * ```
     */
    public function encrypt($data, $nonce, $timestamp)
    {
        !is_string($data) && $data = json_encode($data);
        empty($nonce) && $nonce = StringUtil::getRandomStr(self::RANDOM_NONCE_LENGTH);
        empty($timestamp) && $timestamp = time();
        
        $pc = new Prpcrypt($this->encodingAesKey, $this->appId);

		//加密
		$ret = $pc->encrypt($data);
        if ($ret['status'] != StatusCode::SUCCESS['status']) {
            return $ret;
        }
		$encrypt = $ret['retval']['encrypt'];

		//生成安全签名
		$ret = Sha1::getSignature($this->token, $timestamp, $nonce, $encrypt);
        if ($ret['status'] != StatusCode::SUCCESS['status']) {
            return $ret;
        }
        $signature = $ret['retval']['signature'];
        
        $retval = [
            'data' => $encrypt, 
            'signature' => $signature, 
            'timestamp' => $timestamp, 
            'nonce' => $nonce, 
        ];
        
        return ResultUtil::returnRs(StatusCode::SUCCESS, $retval);
    }
    
    /**
     * 数据解密
     * 
     * @param type $data
     * @param type $nonce
     * @param type $timestamp
     * @param type $signature
     * 
     * @return array 
     * ```
     * [
     *  'status' => '', 
     *  'info' => '', 
     *  'retval' => [
     *      'data' => '', 
     *      'signature' => '', 
     *      'timestamp' => '', 
     *      'nonce' => '', 
     *  ], 
     * ]
     * ```
     */
    public function decrypt($data, $nonce, $timestamp, $signature)
    {
        !is_string($data) && $data = json_encode($data);
        empty($nonce) && $nonce = StringUtil::getRandomStr(self::RANDOM_NONCE_LENGTH);
        empty($timestamp) && $timestamp = time();
        
        //验证安全签名
		$ret = Sha1::getSignature($this->token, $timestamp, $nonce, $data);
		if ($ret['status'] != StatusCode::SUCCESS['status']) {
            return $ret;
        }
        $signatureSelf = $ret['retval']['signature'];
		if ($signature != $signatureSelf) {
            return ResultUtil::returnRs(StatusCode::VALIDATE_SIGNATURE_ERROR);
		}
        
		$pc = new Prpcrypt($this->encodingAesKey, $this->appId);
		$ret = $pc->decrypt($data);
        if ($ret['status'] != StatusCode::SUCCESS['status']) {
            return $ret;
        }
        
        $retval = [
            'data' => $ret['retval']['decrypt'], 
            'signature' => $signature, 
            'timestamp' => $timestamp, 
            'nonce' => $nonce, 
        ];
        
		return ResultUtil::returnRs(StatusCode::SUCCESS, $retval);
    }
}

