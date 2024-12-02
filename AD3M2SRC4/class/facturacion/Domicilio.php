<?php

/**
 * EGMC 20151109
 * Nodo-Domicilio o DomicilioFiscal
 *    Attr-calle
 *    Attr-noExterior
 *    Attr-noInterior
 *    Attr-colonia
 *    Attr-referencia
 *    Attr-municipio
 *    Attr-estado
 *    Attr-pais
 *    Attr-codigoPostal
 */
class Domicilio {

    public static $atributos = array(
        'calle' => '',
        'noExterior' => '',
        'noInterior' => '',
        'colonia' => '',
        'localidad' => '',
        'referencia' => '',
        'municipio' => '',
        'estado' => '',
        'pais' => 'México',
        'codigoPostal' => ''
    );
// Atributos
    private $calle;
    private $noExterior;
    private $noInterior;
    private $colonia;
    private $localidad;
    private $referencia;
    private $municipio;
    private $estado;
    private $pais;
    private $codigoPostal;

    function __construct($calle = "", $noExterior = "", $noInterior = "", $colonia = "", $municipio = "", $estado = "", $pais = "", $codigoPostal = "", $localidad = null, $referencia = null) {

//    $calle = $this->getValorCorregido($calle);
//    $noExterior = $this->getValorCorregido($noExterior);
//    $noInterior = $this->getValorCorregido($noInterior);
//    $colonia = $this->getValorCorregido($colonia);
//    $municipio = $this->getValorCorregido($municipio);
//    $estado = $this->getValorCorregido($estado);
//    $pais = $this->getValorCorregido($pais);
//    $codigoPostal = $this->getValorCorregido($codigoPostal);
//    
//    $this->setCalle($calle);
//    $this->setNoExterior($noExterior);
//    $this->setNoInterior($noInterior);
//    $this->setColonia($colonia);
//    $this->setMunicipio($municipio);
//    $this->setEstado($estado);
//    $this->setPais($pais);
//    $this->setCodigoPostal($codigoPostal);

        $this->calle = $this->corregirValor($calle);
        $this->noExterior = $this->corregirValor($noExterior);
        $this->noInterior = $this->corregirValor($noInterior);
        $this->colonia = $this->corregirValor($colonia);
        $this->municipio = $this->corregirValor($municipio);
        $this->estado = $this->corregirValor($estado);
        $this->pais = $this->corregirValor($pais);
        $this->codigoPostal = $this->corregirValor($codigoPostal);
        $this->localidad = $this->corregirValor($localidad);
        $this->referencia = $this->corregirValor($referencia);
    }

    private function corregirValor($valor) {
        /**
         * EGMC 
         * Aquí se pueden agregar otras validaciones
         */
        return trim($valor);
    }

// Getters y Setters
    public function getCalle() {
        return $this->calle;
    }

    public function setCalle($calle) {
        $this->calle = $calle;
    }

    public function getNoExterior() {
        return $this->noExterior;
    }

    public function setNoExterior($noExterior) {
        $this->noExterior = $noExterior;
    }

    public function getNoInterior() {
        return $this->noInterior;
    }

    public function setNoInterior($noInterior) {
        $this->noInterior = $noInterior;
    }

    public function getColonia() {
        return $this->colonia;
    }

    public function setColonia($colonia) {
        $this->colonia = $colonia;
    }

    public function getMunicipio() {
        return $this->municipio;
    }

    public function setMunicipio($municipio) {
        $this->municipio = $municipio;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    public function getPais() {
        return $this->pais;
    }

    public function setPais($pais) {
        $this->pais = $pais;
    }

    public function getCodigoPostal() {
        return $this->codigoPostal;
    }

    public function setCodigoPostal($codigoPostal) {
        $this->codigoPostal = $codigoPostal;
    }

    public function getLocalidad() {
        return $this->localidad;
    }

    public function getReferencia() {
        return $this->referencia;
    }

}

?>