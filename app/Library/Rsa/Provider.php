<?php
declare(strict_types = 1);
namespace App\Library\Rsa;


class Provider
{
    private $_config;
    public function __construct(array $config)
    {
        $this->_config = $config;
    }
    /**
     * 私鑰加密
     * @param string $data 要加密的數據
     * @throws \Exception
     * @return string 加密後的字符串
     */
    public function privateKeyEncode(string $data) : string
    {
        $this->_hasKey('private_key');
        $private_key = openssl_pkey_get_private($this->_config['private_key']);
        openssl_private_encrypt($data, $encrypted, $private_key);
        return !empty($encrypted)?base64_encode($encrypted):'';
    }
    /**
     * 公鑰加密
     * @param string $data 要加密的數據
     * @throws \Exception
     * @return string 加密後的字符串
     */
    public function publicKeyEncode(string $data) : string
    {
        $this->_hasKey('public_key');
        $public_key = openssl_pkey_get_public($this->_config['public_key']);
        openssl_public_encrypt($data, $encrypted, $public_key);
        return !empty($encrypted)?base64_encode($encrypted):'';
    }
    /**
     * 用公鑰解密私鑰加密的內容
     * @param string $data 要解密的數據
     * @throws \Exception
     * @return string 解密後的字符串
     */
    public function decodePrivateEncode(string $data) : string
    {
        $this->_hasKey('public_key');
        $public_key = openssl_pkey_get_public($this->_config['public_key']);
        openssl_public_decrypt(base64_decode($data), $decrypted, $public_key);
        return !empty($decrypted)?$decrypted:''; //把拼接的數據base64_decode 解密還原
    }
    /**
     * 用私鑰解密公鑰加密的數據
     * @param string $data  要解密的數據
     * @return string 解密後的字符串
     * @throws \Exception
     */
    public function decodePublicEncode(string $data) : string
    {
        $this->_hasKey('private_key');
        $private_key = openssl_pkey_get_private($this->_config['private_key']);
        openssl_private_decrypt(base64_decode($data), $decrypted, $private_key); //私鑰解密
        return !empty($decrypted)?$decrypted:'';
    }
    /**
     * 檢查是否包含需要的配置項
     * @param string $key public_key 公鑰 private_key 私鑰
     * @return bool
     * @throws \Exception
     */
    private function _hasKey(string $key = 'public_key') : bool
    {
        if(!isset($this->_config[$key])) {
            throw new \Exception('請配置祕鑰');
        }
        return true;
    }
}