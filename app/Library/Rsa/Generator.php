<?php
declare(strict_types = 1);
namespace App\Library\Rsa;

class Generator {

    private $_publicKey = '';
    private $_privateKey = '';

    protected $_config = [];
    function __construct(array $config = [],bool $autoGenerate = true)
    {
        $this->_config = empty($config) ? array(
            'private_key_bits' => 1024,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ) : $config;
        if($autoGenerate) {
            $this->generate();
        }
    }

    /**
     * @return Generator
     */
    public function generate() : Generator
    {
        $r = openssl_pkey_new($this->_config);
        openssl_pkey_export($r, $this->_privateKey);
        $rp = openssl_pkey_get_details($r);
        $pubKey = $rp['key'];
        $this->_publicKey = $pubKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getPublicKey() : string
    {
        return $this->_publicKey;
    }

    /**
     * @return string
     */
    public function getPrivateKey(): string
    {
        return $this->_privateKey;
    }

}