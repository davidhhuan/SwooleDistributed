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
use Fayho\Security\PKCS7Encoder;
use Fayho\Util\StringUtil;
use Fayho\Exception\SystemException;

/**
 * 提供接收和推送给消息的加解密接口.
 *
 * @author  birdylee <birdylee_cn@163.com>
 * @since   2017年04月02日
 * @version 1.0
 *
 */
class Prpcrypt
{

    private $key1;
    
    private $key2;

    public function __construct($key1, $key2 = null)
    {
        if (strlen($key1) < 16) {
            throw new SystemException('The length of the key1 must be greater then 16.');
        }
        empty($key2) && $key2 = $key1;
        if (strlen($key2) < 16) {
            throw new SystemException('The length of the key2 must be greater then 16.');
        }
        $this->key1 = md5($key1 . "=");
        $this->key2 = md5($key2 . "=");
    }
    
    /**
     * 对明文进行加密
     * @param string $plaintext 需要加密的明文
     * @return string 加密后的密文
     */
    public function encrypt($plaintext)
    {
//        try {
//            //获得16位随机字符串，填充到明文之前
//            $random = StringUtil::getRandomStr();
//            $plaintext = $random . pack("N", strlen($plaintext)) . $plaintext;
//            // 网络字节序
//            $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
//            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_256, '', MCRYPT_MODE_CBC, '');
//            //使用自定义的填充方式对明文进行补位填充
//            $plaintext = PKCS7Encoder::encode($plaintext);
//            mcrypt_generic_init($module, $this->getKey(), $this->getIv());
//            //加密
//            $encrypted = mcrypt_generic($module, $plaintext);
//            mcrypt_generic_deinit($module);
//            mcrypt_module_close($module);
//            //使用BASE64对加密后的字符串进行编码
//            $encrypted = base64_encode($encrypted);
//            return ResultUtil::returnRs(StatusCode::SUCCESS, ['encrypt' => $encrypted]);
//        } catch (Exception $e) {
//            return ResultUtil::returnRs(StatusCode::ENCRYPT_AES_ERROR);
//        }
        
        //获得16位随机字符串，填充到明文之前
        $random = StringUtil::getRandomStr();
        $plaintext = $random . pack("N", strlen($plaintext)) . $plaintext;
        
        //使用自定义的填充方式对明文进行补位填充
        $plaintext = PKCS7Encoder::encode($plaintext);
        
        $ciphertext = openssl_encrypt($plaintext,  'AES-256-CBC', $this->getKey(), OPENSSL_RAW_DATA, $this->getIv());
        if ($ciphertext === false) {
            return ResultUtil::returnRs(StatusCode::ENCRYPT_AES_ERROR);
        } else {
            return ResultUtil::returnRs(StatusCode::SUCCESS, ['encrypt' => base64_encode(base64_encode($ciphertext))]);
        }
        
    }
    
    /**
     * 对密文进行解密
     * @param string $ciphertext 需要解密的密文
     * @return string 解密得到的明文
     */
    public function decrypt($ciphertext)
    {
//        try {
//            //使用BASE64对需要解密的字符串进行解码
//            $ciphertextDec = base64_decode($ciphertext);
//            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_256, '', MCRYPT_MODE_CBC, '');
//            mcrypt_generic_init($module, $this->getKey(), $this->getIv());
//
//            //解密
//            $decrypted = mdecrypt_generic($module, $ciphertextDec);
//            mcrypt_generic_deinit($module);
//            mcrypt_module_close($module);
//        } catch (Exception $e) {
//            return ResultUtil::returnRs(StatusCode::DECRYPT_AES_ERROR);
//        }
//
//        $contentRs = '';
//        try {
//            //去除补位字符
//            $result = PKCS7Encoder::decode($decrypted);
//            //去除16位随机字符串,网络字节序和AppId
//            if (strlen($result) < 16) {
//                return ResultUtil::returnRs(StatusCode::SUCCESS, ['decrypt' => $contentRs]);
//            }
//            $content = substr($result, 16, strlen($result));
//            $lenList = unpack("N", substr($content, 0, 4));
//            $len = $lenList[1];
//            $contentRs = substr($content, 4, $len);
//        } catch (Exception $e) {
//            return ResultUtil::returnRs(StatusCode::ILLEGAL_BUFFER);
//        }
//        
//        return ResultUtil::returnRs(StatusCode::SUCCESS, ['decrypt' => $contentRs]);
        
        $plaintext = openssl_decrypt(base64_decode(base64_decode($ciphertext)), 'AES-256-CBC', $this->getKey(), OPENSSL_RAW_DATA, $this->getIv());
        if ($plaintext === false) {
            return ResultUtil::returnRs(StatusCode::DECRYPT_AES_ERROR);
        }
        $contentRs = '';
        try {
            //去除补位字符
            $plaintext = PKCS7Encoder::decode($plaintext);
            //去除16位随机字符串,网络字节序
            if (strlen($plaintext) < 16) {
                return ResultUtil::returnRs(StatusCode::SUCCESS, ['decrypt' => $contentRs]);
            }
            $content = substr($plaintext, 16, strlen($plaintext));
            $lenList = unpack("N", substr($content, 0, 4));
            $len = $lenList[1];
            $contentRs = substr($content, 4, $len);
        } catch (Exception $e) {
            return ResultUtil::returnRs(StatusCode::ILLEGAL_BUFFER);
        }
        
        return ResultUtil::returnRs(StatusCode::SUCCESS, ['decrypt' => $contentRs]);
    }
    
    /**
     * 
     * @return string
     */
    private function getIv()
    {
        return substr($this->key1, 0, 10) . substr($this->key2, 0, 6);
    }
    
    /**
     * 
     * @return string 
     */
    private function getKey()
    {
        return md5($this->key1 . $this->key2);
    }


}
