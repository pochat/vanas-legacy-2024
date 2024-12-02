<?php

/**
 * EGMC 20151109
 * Definición:
 * Nodo-Impuestos
 *    Nodo-Retenciones
 *       Nodo-Retencion
 *          Attr-impuesto
 *             Attr-valor = ISR
 *             Attr-valor = IVA
 *          Attr-importe
 * 
 *    Nodo-Traslados    
 *       Nodo-Traslado
 *          Attr-impuesto
 *             Attr-valor = IVA
 *             Attr-valor = IEPS
 *          Attr-tasa
 *          Attr-importe
 */
class Impuesto {

    public static $atributos = array(
        'tipoImpuesto' => 'TRASLADO',
        'impuesto' => 'IVA',
        'importe' => '0.00',
        'tasa' => '16.00'
    );

    /**
     * Tipo de impuesto, es el valor del nodo Retenciones o Traslados
     * 
     * Valores (FIJOS):
     *  RETENCION
     *  TRASLADO
     * 
     * @var string 
     */
    private $tipoImpuesto; // NOTA: ANTES ERA $tipoRegistro
    /**
     * ATRIBUTO - impuesto
     *  
     * Valores (FIJOS):
     * IVA  => Retencion y Traslado
     * ISR  => Retencion
     * IEPS => Traslado
     * @var string
     */
    private $impuesto;

    /**
     * ATRIBUTO - importe
     * @var string 
     */
    private $importe;

    /**
     * ATRIBUTO - tasa
     * Tasa del impuesto aplica sólo para los tipos de impueto tralados
     * @var string 
     */
    private $tasa = '16.00';

    function __construct($tipoImpuesto = 'TRASLADO', $impuesto = 'IVA', $importe = 0, $tasa = '16.00') {
        $this->tipoImpuesto = $tipoImpuesto;
        $this->impuesto = $impuesto;
        $this->importe = Facturacion::formatoImporte($importe);
        $this->tasa = Facturacion::formatoImporte($tasa);
    }

    // Getters y Setters
    public function getTipoImpuesto() {
        return $this->tipoImpuesto;
    }

    public function setTipoImpuesto($tipoImpuesto) {
        $this->tipoImpuesto = $tipoImpuesto;
    }

    public function getImpuesto() {
        return $this->impuesto;
    }

    public function setImpuesto($impuesto) {
        $this->impuesto = $impuesto;
    }

    public function getTasa() {
        return Facturacion::formatoImporte($this->tasa);
    }

    public function setTasa($tasa = 16) {
        $this->tasa = Facturacion::formatoImporte($tasa);
    }

    public function getImporte() {
        return Facturacion::formatoImporte($this->importe);
    }

    public function setImporte($importe) {
        $this->importe = Facturacion::formatoImporte($importe);
    }

}

?>