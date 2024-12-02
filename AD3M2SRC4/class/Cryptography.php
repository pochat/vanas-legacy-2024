<?php

class Cryptography {
    
    private $key = 'M13E24';

    /**
     * EGMC 20150729
     * Es la variable que se utiliza para saber si 
     * la clase ya fue instanciada que sirve para patrón singleton
     * @var instance de la clase
     */
    private static $instance;

    /**
     * EGMC 20150729
     * contructor privado para aplicar patrón singleton
     */
    private function __construct() {
        
    }

    /**
     * EGMC 20150729
     * Aplica patrón singleton
     * @return class regresa la instancia del objeto
     */
    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    /**
     * EGMC 20150729
     * Encripta una cadena de texto
     * @param type $string
     * @return type
     */
    public function encryption($string = ''){
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($this->key), $string, MCRYPT_MODE_CBC, md5(md5($this->key))));
    }
    /**
     * EGMC 20150729
     * Desencripta una cadena de texto
     * @param type $encrypted
     * @return type
     */
    public function decryption($encrypted = '')
    {
        return  rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($this->key), base64_decode($encrypted), MCRYPT_MODE_CBC, md5(md5($this->key))), "\0");
    }

}
