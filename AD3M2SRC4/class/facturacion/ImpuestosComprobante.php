<?php

class ImpuestosComprobante {

    private $totalImpuestosTrasladados;
    private $totalImpuestosRetenidos;
    private $impuestos;

    function __construct($totalImpuestosTrasladados = "0.00", $totalImpuestosRetenidos = "0.00", $impuestos = null) {
        $this->totalImpuestosTrasladados = Facturacion::formatoImporte($totalImpuestosTrasladados);
        $this->totalImpuestosRetenidos = Facturacion::formatoImporte($totalImpuestosRetenidos);
        $this->impuestos = $impuestos;
    }

    // Getters y Setters
    public function getTotalImpuestosTrasladados() {
        return Facturacion::formatoImporte($this->totalImpuestosTrasladados);
    }

    public function setTotalImpuestosTrasladados($totalImpuestosTrasladados) {
        $this->totalImpuestosTrasladados = Facturacion::formatoImporte($totalImpuestosTrasladados);
    }

    public function getTotalImpuestosRetenidos() {
        return Facturacion::formatoImporte($this->totalImpuestosRetenidos);
    }

    public function setTotalImpuestosRetenidos($totalImpuestosRetenidos) {
        $this->totalImpuestosRetenidos = Facturacion::formatoImporte($totalImpuestosRetenidos);
    }

    public function getImpuestos() {
        return $this->impuestos;
    }

    public function setImpuestos($impuestos) {
        $this->impuestos = $impuestos;
    }

}

?>