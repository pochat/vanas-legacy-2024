<?php

/**
 * EGMC 20151109
 * Definición:
 * Nodo-Conceptos
 *    Nodo-Concepto
 *       Attr-cantidad
 *       Attr-unidad
 *       Attr-noIdentificacion
 *       Attr-descripcion
 *       Attr-valorUnitario
 *       Attr-importe
 *       Nodo-InformacionAduanera
 *       Nodo-CuentaPredial  
 *       Nodo-ComplementoConcepto  
 *       Nodo-Parte  
 */
class Concepto {
    
    Public static $atributos = array(
        
    'cantidad' => '1',
    'unidad' => '',
    'noIdentificacion' => '',
    'descripcion' => '',
    'valorUnitario' => '0.00',
    'importe' => '0.00'
    );
    
    // Atributos
    private $cantidad;
    private $unidad;
    private $noIdentificacion;
    private $descripcion;
    private $valorUnitario;
    private $importe;

    function __construct($cantidad, $unidad, $noIdentificacion, $descripcion, $valorUnitario, $importe) {

        $this->cantidad = $cantidad;
        $this->unidad = $unidad;
        $this->noIdentificacion = $noIdentificacion;
        $this->descripcion = $descripcion;
        $this->valorUnitario = Facturacion::formatoImporte($valorUnitario);
        $this->importe = Facturacion::formatoImporte($importe);
    }

    // Getters y Setters
    public function getCantidad() {
        return $this->cantidad;
    }

    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    public function getUnidad() {
        return $this->unidad;
    }

    public function setUnidad($unidad) {
        $this->unidad = $unidad;
    }

    public function getNoIdentificacion() {
        return $this->noIdentificacion;
    }

    public function setNoIdentificacion($noIdentificacion) {
        $this->noIdentificacion = $noIdentificacion;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function getValorUnitario() {
        return Facturacion::formatoImporte($this->valorUnitario);
    }

    public function setValorUnitario($valorUnitario) {
        $this->valorUnitario = Facturacion::formatoImporte($valorUnitario);
    }

    public function getImporte() {
        return Facturacion::formatoImporte($this->importe);
    }

    public function setImporte($importe) {
//        $this->importe = number_format($importe, 2, '.', '');
        $this->importe =  Facturacion::formatoImporte($importe);
    }

}

?>