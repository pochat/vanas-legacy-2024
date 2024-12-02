<?php

class Validate {

    /**
     * EGMC 20150701
     * Es la variable que se utiliza para saber si 
     * la clase ya fue instanciada sirve para patrón singleton
     * @var instance de la clase Html
     */
    private static $instance;

    /**
     * EGMC 20150701
     * contructor privado para aplicar patrón singleton
     */
    private function __construct() {
        
    }

    /**
     * EGMC 20150701
     * Aplica patrón singleton
     * @return class regresa la instancia del objeto
     */
    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function required($value) {
        return $value != '' ? true : false;
    }

    /**
     * EGMC 20150701
     * Valida que la cadena sea un correo electrónico
     * @param sring $email cadena a validar
     * @return bool 
     */
    public function email($email) {

        return preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $email) && filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * EGMC 20150701
     * Valida que el dato sea un número flotante
     * @param sring $float dato a validar
     * @return bool 
     */
    public function float($float) {
        return filter_var($float, FILTER_VALIDATE_FLOAT);
    }

    /**
     * EGMC 20150701
     * Valida que el dato sea un número entero
     * @param sring $float dato a validar
     * @return bool 
     */
    public function int($int) {

//        Dbg::data($int);
//        Dbg::data(filter_var($int, FILTER_VALIDATE_INT));
        return filter_var($int, FILTER_VALIDATE_INT);
    }
    
    /**
     * EGMC 20151002
     * Valida una fecha 2015-01-31
     * @param string $date
     * @return bool
     */
    public function date($date) {
        $date = explode('-', $date);
        return checkdate($date[1], $date[2], $date[0]);
    }

    /**
     * EGMC 20150803
     * Convierte los datos en cadenas html para inertarlos en base de datos
     * @param string|array $toConvert datos a convertir
     * @return string datos convertidos
     */
    public function convertToHTML($toConvert = array()) {
        if (is_array($toConvert) && !empty($toConvert)) {
            $data = array();
            foreach ($toConvert as $field => $dt) {
                $data[$field] = htmlentities($dt, ENT_QUOTES, "UTF-8");
            }
            return $data;
        } elseif (is_string($toConvert)) {
            return htmlentities($dt, ENT_QUOTES, "UTF-8");
        }

        return '';
    }

    /**
     * EGMC 20151001
     * Convierte los datos de cadenas html a datos normales
     * @param string|array $toConvert datos a convertir
     * @return string datos convertidos
     */
    public function decodeEntity($toConvert = array()) {
        if (is_array($toConvert) && !empty($toConvert)) {
            $data = array();
            foreach ($toConvert as $field => $dt) {
                $data[$field] = html_entity_decode($dt);
            }
            return $data;
        } elseif (is_string($toConvert)) {
            return html_entity_decode($dt);
        }

        return '';
    }

    /**
     * EGMC 20150804
     * Realiza la validación de datos haciendo un 
     * match de campos de base de datos y valores
     * @param array $data
     * @param array $validateRules
     * @return array con mensajes de error de los campos 
     */
    public function validate($data, $validateRules) {

//      Dbg::data($data); Dbg::data($validateRules); Dbg::pd();
        $errors = array();
        if (!empty($validateRules)) {
            foreach ($data as $field => $value) {
                if (isset($validateRules[$field])) {

                    if (in_array('required', $validateRules[$field]) && !$this->required($value)) {
                        $errors[$field] = ObtenMensaje(3);
                    } elseif (in_array('int', $validateRules[$field]) && $this->int($value) === false) {
                        $errors[$field] = ObtenMensaje(8);
                    } elseif (in_array('float', $validateRules[$field]) && $this->float($value) === false) {
                        $errors[$field] = ObtenMensaje(23);
                    } elseif (in_array('email', $validateRules[$field]) && $this->email($value) === false) {
                        $errors[$field] = ObtenMensaje(6);
                    } elseif (in_array('date', $validateRules[$field]) && $this->date($value) === false) {
                        $errors[$field] = ObtenMensaje(5);
                    }

                    if (in_array('null', $validateRules[$field]) && $value == '') {
                        unset($errors[$field]);
                    }
                }
            }
        }
//        Dbg::pd($errors);
        return $errors;
    }

}
