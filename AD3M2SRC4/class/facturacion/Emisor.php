<?php

//use Domicilio;

/**
 * EGMC 20151109
 * Definición:
 * Nodo-Emisor
 *    Attr-rfc
 *    Attr-nombre
 *    Nodo-DomicilioFiscal
 *    Nodo-ExpedidoEn
 *    Nodo-RegimenFiscal
 *       Attr-Regimen  
 */
class Emisor {
    
    
    public static $atributos = array(
        'rfc' => '',
        'nombre' => '',
        'Regimen' => 'REGIMEN GENERAL DE LEY PERSONA MORAL'
    );
    
    // Atributos
    private $rfc;
    private $nombre;
    /**
     *Valores:
     * REGIMEN GENERAL DE LEY PERSONA MORAL
     * REGIMEN GENERAL DE LEY PERSONA FISICA
     * 
     * @var string
     */
    private $Regimen = 'REGIMEN GENERAL DE LEY PERSONA MORAL'; // TODO Este atributo debe ser un array segun la documentacion del sat aunque en los ejemplos viene solo un valor
    // Objetos
    private $DomicilioFiscal;
    private $ExpedidoEn;
    // 
    private $estadoExpedicion;
    private $paisExpedicion;

    function __construct($rfc, $nombre, Domicilio $DomicilioFiscal, $Regimen, Domicilio $ExpedidoEn = null, $estadoExpedicion = "", $paisExpedicion = "") {

//        $this->setRfc($rfc);
//        $this->setNombre($nombre);
//        $this->setRegimen($Regimen);
//        $this->setDomicilioFiscal($DomicilioFiscal);
//        $this->setExpedidoEn($ExpedidoEn);
//        $this->setEstadoExpedicion($estadoExpedicion);
//        $this->setPaisExpedicion($paisExpedicion);

        $this->rfc = $rfc;
        $this->nombre = $nombre;
        $this->Regimen = $Regimen;
        $this->DomicilioFiscal = $DomicilioFiscal;
        $this->ExpedidoEn = $ExpedidoEn;
        $this->estadoExpedicion = $estadoExpedicion;
        $this->paisExpedicion = $paisExpedicion;
    }

    // Getters y Setters
    public function getRfc() {
        return $this->rfc;
    }

    public function setRfc($rfc) {
        $this->rfc = $rfc;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function getDomicilioFiscal() {
        return $this->DomicilioFiscal;
    }

    public function setDomicilioFiscal($DomicilioFiscal) {
        $this->DomicilioFiscal = $DomicilioFiscal;
    }

    public function setExpedidoEn($ExpedidoEn) {
        $this->ExpedidoEn = $ExpedidoEn;
    }

    public function getExpedidoEn() {
        return $this->ExpedidoEn;
    }

    public function getEstadoExpedicion() {
        return $this->estadoExpedicion;
    }

    public function setEstadoExpedicion($estadoExpedicion) {
        $this->estadoExpedicion = $estadoExpedicion;
    }

    public function getPaisExpedicion() {
        return $this->paisExpedicion;
    }

    public function setPaisExpedicion($paisExpedicion) {
        $this->paisExpedicion = $paisExpedicion;
    }

    public function getRegimenFiscal() {
        return $this->Regimen;
    }

    public function setRegimenFiscal($Regimen) {
        $this->Regimen = $Regimen;
    }

}

?>