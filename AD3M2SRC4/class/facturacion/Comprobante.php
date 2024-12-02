<?php

require_once PATH_ADM_CLASS . '/nusoap.php';

/**
 * EGMC 20151109
 * Definición:
 * Nodo-Comprobante
 *    Attr-version
 *    Attr-serie;
 *    Attr-folio;
 *    Attr-fecha;
 *    Attr-sello;
 *    Attr-formaDePago;
 *    Attr-noCertificado;
 *    Attr-certificado;
 *    Attr-condicionesDePago;
 *    Attr-subTotal;
 *    Attr-descuento;
 *    Attr-motivoDescuento;
 *    Attr-TipoCambio;
 *    Attr-Moneda;
 *    Attr-total;
 *    Attr-tipoDeComprobante;
 *    Attr-metodoDePago;
 *    Attr-LugarExpedicion;
 *    Attr-NumCtaPago;
 *    Attr-FolioFiscalOrig;
 *    Attr-SerieFolioFiscalOrig;
 *    Attr-FechaFolioFiscalOrig;
 *    Attr-MontoFolioFiscalOrig;
 * 
 *    Nodo-Emisor
 *    Nodo-Receptor
 *    Nodo-Conceptos
 *    Nodo-Impuestos
 *    Nodo-Complemento
 *    Nodo-Assenda

 */
class Comprobante {

    public static $atributos = array(
        'version' => '3.2',
        'serie' => null,
        'folio' => null,
        'fecha' => '2015-10-17T13:00:00',
        'sello' => null,
        'formaDePago' => 'Pago en una sola exhibicion',
        'noCertificado' => '',
        'certificado' => null,
        'condicionesDePago' => 'N/A',
        'subTotal' => '0.00',
        'descuento' => '',
        'motivoDescuento' => '',
        'TipoCambio' => '',
        'Moneda' => 'MXN',
        'total' => '0.00',
        'tipoDeComprobante' => 'ingreso',
        'metodoDePago' => 'Efectivo',
        'LugarExpedicion' => 'Mexico, Distrito Federal',
        'NumCtaPago' => null,
        'FolioFiscalOrig' => null,
        'SerieFolioFiscalOrig' => null,
        'FechaFolioFiscalOrig' => null,
        'MontoFolioFiscalOrig' => null
    );
    
    private $version = '3.2';

    /**
     * EGMC 0151109
     * $serie 
     * No se incluye en la versión 3.2
     */
    private $serie;

    /**
     * EGMC 0151109
     * $folio 
     * No se incluye en la versión 3.2
     */
    private $folio;
    private $fecha;
    private $sello;

    /**
     * Valores:
     *      -Pago en una sola exhibición
     *      -Parcialidad 1 de X. 
     * @var string 
     */
    private $formaDePago;
    private $noCertificado;
    private $certificado;
    private $condicionesDePago;
    private $subTotal;
    private $descuento;
    private $motivoDescuento;
    private $TipoCambio;
    private $Moneda;
    private $total;

    /**
     * Valores (FIJOS): 
     *      ingreso
     *      egreso
     *      traslado
     * @var string 
     */
    private $tipoDeComprobante;

    /**
     * Posibles valores:
     * cheque, tarjeta de crédito o debito, depósito en cuenta, etc.
     * @var string
     */
    private $metodoDePago;
    private $LugarExpedicion;
    private $NumCtaPago;
    private $FolioFiscalOrig;
    private $SerieFolioFiscalOrig;
    private $FechaFolioFiscalOrig;
    private $MontoFolioFiscalOrig;
// Objetos
    private $Emisor;
    private $Receptor;
    private $Conceptos;
    private $ImpuestosComprobante;
// XML de timbrado, para PACs que no regresan XML completo timbrado
    private $agregarXMLTimbrado = False;
    private $tfdNameSpace;
    private $tfdSchemaLocation;
    private $tfdVersion;
    private $tfdFechaTimbrado;
    private $tfdSelloCFD;
    private $tfdNoCertificadoSAT;
    private $tfdSelloSAT;
    private $tfdUUID;

    function __construct($version, $serie, $folio, $fecha, $sello, $formaDePago, $noCertificado, $certificado, $condicionesDePago, $subTotal, $descuento, $motivoDescuento, $TipoCambio, $Moneda, $total, $tipoDeComprobante, $metodoDePago, $LugarExpedicion, Emisor $Emisor, Receptor $Receptor, $Conceptos, ImpuestosComprobante $ImpuestosComprobante) {

//        $this->setVersion($version);
//        $this->setSerie($serie);
//        $this->setFolio($folio);
//        $this->setFecha($fecha);
//        $this->setSello($sello);
//        $this->setFormaDePago($formaDePago);
//        $this->setNoCertificado($noCertificado);
//        $this->setCertificado($certificado);
//        $this->setSubTotal($subTotal);
//        $this->setDescuento($descuento);
//        $this->setMotivoDescuento($motivoDescuento);
//        $this->setTipoCambio($TipoCambio);
//        $this->setMoneda($Moneda);
//        $this->setTotal($total);
//        $this->setTipoDeComprobante($tipoDeComprobante);
//        $this->setMetodoDePago($metodoDePago);
//        $this->setLugarExpedicion($LugarExpedicion);

        $this->version = $version;
        $this->serie = $serie;
        $this->folio = $folio;
        $this->fecha = $fecha;
        $this->sello = $sello;
        $this->formaDePago = $formaDePago;
        $this->noCertificado = $noCertificado;
        $this->certificado = $certificado;
        $this->condicionesDePago = $condicionesDePago;
        $this->subTotal = Facturacion::formatoImporte($subTotal);
        $this->descuento = Facturacion::formatoImporte($descuento);
        $this->motivoDescuento = $motivoDescuento;
        $this->TipoCambio = $TipoCambio;
        $this->Moneda = $Moneda;
        $this->total = Facturacion::formatoImporte($total);
        $this->tipoDeComprobante = $tipoDeComprobante;
        $this->metodoDePago = $metodoDePago;
        $this->LugarExpedicion = $LugarExpedicion;

        $this->NumCtaPago = '';
        $this->FolioFiscalOrig = '';
        $this->SerieFolioFiscalOrig = '';
        $this->FechaFolioFiscalOrig = '';
        $this->MontoFolioFiscalOrig = '';

        $this->Emisor = $Emisor; //new Emisor();
        $this->Receptor = $Receptor;
        $this->Conceptos = $Conceptos;
        $this->ImpuestosComprobante = $ImpuestosComprobante;
    }

// Metodos principales
    public function getInfo() {

        $htmlInfo = array();
        $htmlInfo[] = "Versión: " . $this->getVersion();
        $htmlInfo[] = "Serie: " . $this->getSerie();
        $htmlInfo[] = "Folio: " . $this->getFolio();
        $htmlInfo[] = "Fecha: " . $this->getFecha();
        $htmlInfo[] = "Sello: " . $this->getSello();
        $htmlInfo[] = "Forma de pago: " . $this->getFormaDePago();
        $htmlInfo[] = "Número de Certificado: " . $this->getNoCertificado();
        $htmlInfo[] = "Certificado: " . $this->getCertificado();
        $htmlInfo[] = "Subtotal: " . $this->getSubTotal();
        $htmlInfo[] = "Moneda: " . $this->getMoneda();
        $htmlInfo[] = "Tipo de cambio: " . $this->getTipoCambio();
        $htmlInfo[] = "Total: " . $this->getTotal();
        $htmlInfo[] = "Método de pago: " . $this->getMetodoDePago();
        $htmlInfo[] = "Tipo de comprobante: " . $this->getTipoDeComprobante();
        $htmlInfo[] = "Lugar de expedición: " . $this->getLugarExpedicion();

// Emisor
        $emisor = $this->Emisor;
        $domicilioEmisor = $emisor->getDomicilioFiscal();

        $htmlInfo[] = "------------------------------------------";
        $htmlInfo[] = "EMISOR ";
        $htmlInfo[] = "------------------------------------------";
        $htmlInfo[] = "RFC: " . $emisor->getRfc();
        $htmlInfo[] = "Nombre: " . $emisor->getNombre();
        $htmlInfo[] = "DOMICILIO EMISOR ";
        $htmlInfo[] = "Calle: " . $domicilioEmisor->getCalle();
        $htmlInfo[] = "Número exterior: " . $domicilioEmisor->getNoExterior();
        $htmlInfo[] = "Número interior: " . $domicilioEmisor->getNoInterior();
        $htmlInfo[] = "Colonia: " . $domicilioEmisor->getColonia();
        $htmlInfo[] = "Municipio: " . $domicilioEmisor->getMunicipio();
        $htmlInfo[] = "Estado: " . $domicilioEmisor->getEstado();
        $htmlInfo[] = "País: " . $domicilioEmisor->getPais();
        $htmlInfo[] = "Código Postal: " . $domicilioEmisor->getCodigoPostal();
        $htmlInfo[] = "Régimen fiscal: " . $emisor->getRegimenFiscal();
        $htmlInfo[] = "------------------------------------------";
        $htmlInfo[] = "EXPEDIDO EN ";
        $htmlInfo[] = "------------------------------------------";
        $htmlInfo[] = "Estado: " . $emisor->getEstadoExpedicion();
        $htmlInfo[] = "País: " . $emisor->getPaisExpedicion();

// Receptor
        $receptor = $this->Receptor;
        $domicilioReceptor = $receptor->getDomicilio();

        $htmlInfo[] = "------------------------------------------";
        $htmlInfo[] = "RECEPTOR ";
        $htmlInfo[] = "------------------------------------------";
        $htmlInfo[] = "RFC: " . $receptor->getRfc();
        $htmlInfo[] = "Nombre: " . $receptor->getNombre();
        $htmlInfo[] = "DOMICILIO RECEPTOR ";
        $htmlInfo[] = "Calle: " . $domicilioReceptor->getCalle();
        $htmlInfo[] = "Número exterior: " . $domicilioReceptor->getNoExterior();
        $htmlInfo[] = "Número interior: " . $domicilioReceptor->getNoInterior();
        $htmlInfo[] = "Colonia: " . $domicilioReceptor->getColonia();
        $htmlInfo[] = "Municipio: " . $domicilioReceptor->getMunicipio();
        $htmlInfo[] = "Estado: " . $domicilioReceptor->getEstado();
        $htmlInfo[] = "País: " . $domicilioReceptor->getPais();
        $htmlInfo[] = "Código Postal: " . $domicilioReceptor->getCodigoPostal();

// Conceptos
        $conceptos = $this->getConceptos();

        $htmlInfo[] = "------------------------------------------";
        $htmlInfo[] = "CONCEPTOS ";
        $htmlInfo[] = "------------------------------------------";

        $numConcepto = 0;
        if (sizeof($conceptos) > 0) {
            foreach ($conceptos as $concepto) {
                $htmlInfo[] = "CONCEPTO # " . ++$numConcepto;
                $htmlInfo[] = "Cantidad: " . $concepto->getCantidad();
                $htmlInfo[] = "Descripción: " . $concepto->getDescripcion();
                $htmlInfo[] = "Valor Unitario: " . $concepto->getValorUnitario();
                $htmlInfo[] = "Importe: " . $concepto->getImporte();
            }
        } else
            echo "No hay conceptos";

// Impuestos
        $impuestosComprobante = $this->getImpuestosComprobante();
        $tipoImpuestos = $impuestosComprobante->getImpuestos();

        $htmlInfo[] = "------------------------------------------";
        $htmlInfo[] = "IMPUESTOS ";
        $htmlInfo[] = "------------------------------------------";

        $htmlInfo[] = "Total de impuestos trasladados: " . $impuestosComprobante->getTotalImpuestosTrasladados();

//$test = new Impuesto();
//$test = $TrasladosArr[0];

        $num = 0;
        if (sizeof($tipoImpuestos) > 0) {
            foreach ($tipoImpuestos as $impuestos) { // Arreglo de tipos de impuestos (Retenciones, Traslados)
                $impuestoAux = new Impuesto();
                foreach ($impuestos as $impuesto) {
                    $htmlInfo[] = "IMPUESTO # " . ++$num;
                    $htmlInfo[] = "Tipo de registro: " . $impuesto->getTipoImpuesto();
                    $htmlInfo[] = "Tipo de impuesto: " . $impuesto->getTipoImpuesto();
                    $htmlInfo[] = "Tasa: " . $impuesto->getTasa();
                    $htmlInfo[] = "Importe: " . $impuesto->getImporte();
                }
            }
        } else
            echo "No hay impuestos";

        $separadorLinea = "<br />";
        return implode($separadorLinea, $htmlInfo);
    }

    public function getCadenaOriginal($encodeUtf8 = true) {
        $separadorLinea = "|";
        $inicioLinea = "||";
        $finLinea = $inicioLinea;

        $version = $this->getVersion();
        $serie = $this->getSerie();
        $folio = $this->getFolio();
        $fecha = $this->getFecha();
        $sello = $this->getSello();
        $formaDePago = $this->getFormaDePago();
        $noCertificado = $this->getNoCertificado();
        $certificado = $this->getCertificado();
        $condicionesDePago = $this->getCondicionesPago();
        $subTotal = $this->getSubTotal();
        $descuento = $this->getDescuento();
        $motivoDescuento = $this->getMotivoDescuento();
        $TipoCambio = $this->getTipoCambio();
        $Moneda = $this->getMoneda();
        $total = $this->getTotal();
        $tipoDeComprobante = $this->getTipoDeComprobante();
        $metodoDePago = $this->getMetodoDePago();
        $LugarExpedicion = $this->getLugarExpedicion();
// No existen todavia
        $NumCtaPago = $this->getNumCtaPago(); // Falta
        $FolioFiscalOrig = $this->getFolioFiscalOrig(); // Falta
        $SerieFolioFiscalOrig = $this->getSerieFolioFiscalOrig(); // Falta
        $FechaFolioFiscalOrig = $this->getFechaFolioFiscalOrig(); // Falta
        $MontoFolioFiscalOrig = $this->getMontoFolioFiscalOrig(); // Falta
// EMISOR
        $Emisor = $this->Emisor;
        $domicilioEmisor = $Emisor->getDomicilioFiscal();

// Datos emisor
        $rfcEmisor = $Emisor->getRfc();
        $nombreEmisor = trim($Emisor->getNombre()); // El nombre no debe llevar espacios al final
// Expedido en (Opcional)
// No las estoy usando pero Faltan las que estan en blanco TODO
        /* $calleExpedicion = $calle;
          $noExteriorExpedicion = "";
          $noInteriorExpedicion = "";
          $coloniaExpedicion = "";
          $localidadExpedicion = "";
          $referenciaExpedicion = "";
          $municipioExpedicion = ""; */
// TODO Revisar si este domicilio lleva pais y estado  
        $estadoExpedicion = $Emisor->getEstadoExpedicion();
        $paisExpedicion = $Emisor->getPaisExpedicion();
//$codigoPostalExpedicion = $codigoPostal; // Revisar si es el mismo  
// Domicilio Emisor
        $calle = $domicilioEmisor->getCalle();
        $noExterior = $domicilioEmisor->getNoExterior();
        $noInterior = $domicilioEmisor->getNoInterior();
        $colonia = $domicilioEmisor->getColonia();
        $localidad = $domicilioEmisor->getLocalidad();
        $referencia = $domicilioEmisor->getReferencia();
        $municipio = $domicilioEmisor->getMunicipio();
        $estado = $domicilioEmisor->getEstado();
        $pais = $domicilioEmisor->getPais();
        $codigoPostal = $domicilioEmisor->getCodigoPostal();

// Regimen Fiscal
        $regimenFiscal = $Emisor->getRegimenFiscal();

// Receptor
        $receptor = $this->Receptor;
        $domicilioReceptor = $receptor->getDomicilio();

        $rfcReceptor = $receptor->getRfc();
        $nombreReceptor = trim($receptor->getNombre()); // El nombre no debe twner espacios al final
// Domicilio
        $calleNumReceptor = $domicilioReceptor->getCalle();
        $noExteriorReceptor = $domicilioReceptor->getNoExterior();
        $noInteriorReceptor = $domicilioReceptor->getNoInterior();
        $coloniaReceptor = $domicilioReceptor->getColonia();
        $localidadReceptor = ""; // TODO Falta
        $referenciaReceptor = ""; // TODO Falta
        $ciudadReceptor = $domicilioReceptor->getMunicipio(); // Es el municipio
        $estadoReceptor = $domicilioReceptor->getEstado();
        $paisReceptor = $domicilioReceptor->getPais();
        $cpReceptor = $domicilioReceptor->getCodigoPostal();


// Cadena original
        $cadena_original = "";
        $cadena_original .= "||$this->version";
//$cadena_original .= "|$this->serie"; // No incluir en la version 3.2
//$cadena_original .= "|$this->folio"; // No incluir en la version 3.2
        $cadena_original .= "|$this->fecha";

        $cadena_original .= "|$this->tipoDeComprobante|$this->formaDePago";
        if (!empty($this->condicionesDePago)) {
            $cadena_original .= "|$this->condicionesDePago";
        }
        $cadena_original .= "|" . $this->subTotal;
        if (!empty($this->descuento)) {
            $cadena_original .= "|$this->descuento";
        }
//        Dbg::data($cadena_original);
        if (!empty($this->motivoDescuento)) {
            $cadena_original .= "|$this->motivoDescuento";
        }
//        Dbg::data($cadena_original);
//        if (!empty($this->TipoCambio)) {
        $cadena_original .= "|$this->TipoCambio";
//        }
//        Dbg::data($cadena_original);
        if (!empty($this->Moneda)) {
            $cadena_original .= "|$this->Moneda";
        }
//        Dbg::data($cadena_original);
        $cadena_original .= "|" . $this->total;
        $cadena_original .= "|$this->metodoDePago";
        $cadena_original .= "|$this->LugarExpedicion";
//        Dbg::data($cadena_original);
// No existen todavia
        if (!empty($this->NumCtaPago)) {
            $cadena_original .= "|$this->NumCtaPago";
        }
        if (!empty($this->FolioFiscalOrig)) {
            $cadena_original .= "|$this->FolioFiscalOrig";
        }
        if (!empty($this->SerieFolioFiscalOrig)) {
            $cadena_original .= "|$this->SerieFolioFiscalOrig";
        }
        if (!empty($this->FechaFolioFiscalOrig)) {
            $cadena_original .= "|$this->FechaFolioFiscalOrig";
        }
        if (!empty($this->MontoFolioFiscalOrig)) {
            $cadena_original .= "|$this->MontoFolioFiscalOrig";
        }


// Emisor
        $cadena_original .= "|$rfcEmisor";
        if (!empty($nombreEmisor))
            $cadena_original .= "|$nombreEmisor";

        if (!empty($calle))
            $cadena_original .= "|$calle";
        if (!empty($noExterior))
            $cadena_original .= "|$noExterior";
        if (!empty($noInterior))
            $cadena_original .= "|$noInterior";
        if (!empty($colonia))
            $cadena_original .= "|$colonia";
        if (!empty($localidad))
            $cadena_original .= "|$localidad";
        if (!empty($referencia))
            $cadena_original .= "|$referencia";
        $cadena_original .= "|$municipio";
        $cadena_original .= "|$estado";
        $cadena_original .= "|$pais";
        $cadena_original .= "|$codigoPostal";

// Expedido en (Opcional)
// No las estoy usando
        if (!empty($calleExpedicion))
            $cadena_original .= "|$calleExpedicion";
        if (!empty($noExteriorExpedicion))
            $cadena_original .= "|$noExteriorExpedicion";
        if (!empty($noInteriorExpedicion))
            $cadena_original .= "|$noInteriorExpedicion";
        if (!empty($coloniaExpedicion))
            $cadena_original .= "|$coloniaExpedicion";
        if (!empty($localidadExpedicion))
            $cadena_original .= "|$localidadExpedicion";
        if (!empty($referenciaExpedicion))
            $cadena_original .= "|$referenciaExpedicion";
        if (!empty($municipioExpedicion))
            $cadena_original .= "|$municipioExpedicion";
        if (!empty($estadoExpedicion))
            $cadena_original .= "|$estadoExpedicion";
        if (!empty($paisExpedicion))
            $cadena_original .= "|$paisExpedicion";
        if (!empty($codigoPostalExpedicion))
            $cadena_original .= "|$codigoPostalExpedicion";

        $cadena_original .= "|$regimenFiscal";

// Receptor
        $cadena_original .= "|$rfcReceptor";
        if (!empty($nombreReceptor))
            $cadena_original .= "|$nombreReceptor";

        if (!empty($calleNumReceptor))
            $cadena_original .= "|$calleNumReceptor";
        if (!empty($noExteriorReceptor))
            $cadena_original .= "|$noExteriorReceptor";
        if (!empty($noInteriorReceptor))
            $cadena_original .= "|$noInteriorReceptor";
        if (!empty($coloniaReceptor))
            $cadena_original .= "|$coloniaReceptor";
        if (!empty($localidadReceptor))
            $cadena_original .= "|$localidadReceptor";
        if (!empty($referenciaReceptor))
            $cadena_original .= "|$referenciaReceptor";
        $cadena_original .= "|$ciudadReceptor"; // Es el municipio
        $cadena_original .= "|$estadoReceptor";
        $cadena_original .= "|$paisReceptor";
        if (!empty($cpReceptor))
            $cadena_original .= "|$cpReceptor";


// Conceptos
        $conceptos = $this->getConceptos();
//        Dbg::data($conceptos);
        if (sizeof($conceptos) > 0) {
            foreach ($conceptos as $concepto) {
                $cadena_original .= "|" . (string) $concepto->getCantidad();
                $cadena_original .= "|" . (string) $concepto->getUnidad();
//                var_dump($concepto->getNoIdentificacion());
                if ($concepto->getNoIdentificacion() != null) {
                    $cadena_original .= "|" . (string) $concepto->getNoIdentificacion();
                }
                $cadena_original .= "|" . (string) $concepto->getDescripcion();
                $cadena_original .= "|" . (string) $concepto->getValorUnitario();
                $cadena_original .= "|" . (string) $concepto->getImporte();
            }
        }
//        Dbg::data($cadena_original);
// Impuestos
        $impuestosComprobante = $this->getImpuestosComprobante();
        $tipoImpuestos = $impuestosComprobante->getImpuestos();

//        Dbg::data($tipoImpuestos);

        $cadena_retenciones = "";
        $cadena_traslados = "";
        if (sizeof($tipoImpuestos) > 0) {
            foreach ($tipoImpuestos as $impuestos) { // Arreglo de tipos de impuestos (Retenciones, Traslados)
                $impuestoAux = new Impuesto();
                foreach ($impuestos as $impuesto) {

// Retenciones
                    $tipoReg = $impuesto->getTipoImpuesto();
                    if ($tipoReg == "RETENCION") {
                        $cadena_retenciones .= "|" . (string) $impuesto->getTipoImpuesto();
                        $cadena_retenciones .= "|" . (string) $impuesto->getImporte();
                    }
                    if ($tipoReg == "TRASLADO") {
                        $cadena_traslados .= "|" . (string) $impuesto->getTipoImpuesto();
                        $cadena_traslados .= "|" . (string) $impuesto->getTasa();
                        $cadena_traslados .= "|" . (string) $impuesto->getImporte();
                    }
                }
            }
        }

        $cadena_original .= $cadena_retenciones;

        $totalImpuestosRetenidos = $impuestosComprobante->getTotalImpuestosRetenidos();
        if (!empty($totalImpuestosRetenidos))
            $cadena_original .= "|$totalImpuestosRetenidos";

//        Dbg::data($cadena_original);
        $totalImpuestosTrasladados = $impuestosComprobante->getTotalImpuestosTrasladados();
        $cadena_original .= $cadena_traslados;
        if (!empty($totalImpuestosTrasladados))
            $cadena_original .= "|$totalImpuestosTrasladados";

//        Dbg::data($cadena_original);

        $cadena_original .= "||";


        /* Este es el código anterior  
          $doc3 = new SimpleXMLElement(utf8_encode($row[0]));
          $comprobante = $doc3->children('http://www.sat.gob.mx/cfd/3');

          foreach($comprobante as $item) {
          $conceptos = $item->children('http://www.sat.gob.mx/cfd/3');
          foreach($conceptos->Concepto as $item2) {
          $cadena_original .= "|". (string)$item2->attributes()->cantidad;
          $cadena_original .= "|". (string)$item2->attributes()->unidad;
          // TODO Falta el num de identificacion
          $cadena_original .= "|". (string)$item2->attributes()->descripcion;
          $cadena_original .= "|". (string)number_format($item2->attributes()->valorUnitario, 2, '.','');
          $cadena_original .= "|". (string)number_format($item2->attributes()->importe, 2, '.','');

          // TODO Falta nodo de informacion aduanera que no estamos usando
          }

          $impuestos = $comprobante->Impuestos;

          foreach($impuestos->Retenciones as $retenciones) {
          foreach($retenciones as $retencion) {
          $cadena_original .= "|". (string)$retencion->attributes()->impuesto;
          $cadena_original .= "|". (string)number_format($retencion->attributes()->importe, 2, '.','');
          }
          }
          if (!empty($totalImpuestosRetenidos)) $cadena_original .= "|$totalImpuestosRetenidos";

          foreach($impuestos->Traslados as $traslados) {
          foreach($traslados as $traslado) {
          $cadena_original .= "|". (string)$traslado->attributes()->impuesto;
          $cadena_original .= "|". (string)$traslado->attributes()->tasa;
          $cadena_original .= "|". (string)number_format($traslado->attributes()->importe, 2, '.','');
          }
          }

          if (!empty($totalImpuestosTrasladados)) $cadena_original .= "|$totalImpuestosTrasladados";

          }

          $cadena_original .= "||";
         */
//        Dbg::data($cadena_original);
        if ($encodeUtf8) {
            return utf8_encode($cadena_original);
        }

        return $cadena_original;
    }

    public function getDomXml() {

//$nameSpace = "http://www.sat.gob.mx/cfd/3";
//$nameSpace_xsi = "http://www.w3.org/2001/XMLSchema-instance";
        /*
          $root_doc = "<?xml version='1.0' encoding='UTF-8'?>
          <Comprobante xsi:schemaLocation='http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv3.xsd'
          xmlns:xsi='{$nameSpace_xsi}' xmlns:cfdi='{$nameSpace}'/>";

          $root = simplexml_load_string($root_doc);
          //print_r($root->asXml());
          //$emisor = $root->addChild('cfdi:Emisor', null, $nameSpace);
          //$emisor->addAttribute('xsi:schemaLocation', "$nameSpace foo.xsd", $nameSpace_xsi);
          //$emisor->addAttribute('rfc', "-------", $nameSpace);
          //print_r($root->asXml());

          //echo $root->Emisor->rfc; */

        $nameSpace = "http://www.sat.gob.mx/cfd/3";
        $nameSpace_xsi = "http://www.w3.org/2001/XMLSchema-instance";
        $localNamespace = "cfdi:";
        $namespaceTimbrado = "tfd:";


        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        $xml = $dom->createElement($localNamespace . 'Comprobante');
        $dom->appendChild($xml);

        $version = $dom->createAttribute('version');
        $xml->appendChild($version);
        $value = $dom->createTextNode('1.0');
        $version->appendChild($value);

        $xsi_schemaLocation = $dom->createAttribute('xsi:schemaLocation');
        $xml->appendChild($xsi_schemaLocation);
        $value = $dom->createTextNode('http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd');
        $xsi_schemaLocation->appendChild($value);

        $xmlns_cfdi = $dom->createAttribute('xmlns:cfdi');
        $xml->appendChild($xmlns_cfdi);
        $value = $dom->createTextNode('http://www.sat.gob.mx/cfd/3');
        $xmlns_cfdi->appendChild($value);

        $xmlns_xsi = $dom->createAttribute('xmlns:xsi');
        $xml->appendChild($xmlns_xsi);
        $value = $dom->createTextNode('http://www.w3.org/2001/XMLSchema-instance');
        $xmlns_xsi->appendChild($value);

        $nodo = $dom->createAttribute('version');
        $xml->appendChild($nodo);
        $value = $dom->createTextNode($this->getVersion());
        $nodo->appendChild($value);

        $serie = $this->getSerie();
        if (!empty($serie)) {
            $nodo = $dom->createAttribute('serie');
            $xml->appendChild($nodo);
            $value = $dom->createTextNode($serie);
            $nodo->appendChild($value);
        }

        $folio = $this->getFolio();
        if (!empty($folio)) {
            $nodo = $dom->createAttribute('folio');
            $xml->appendChild($nodo);
            $value = $dom->createTextNode($folio);
            $nodo->appendChild($value);
        }

        $nodo = $dom->createAttribute('fecha');
        $xml->appendChild($nodo);
        $value = $dom->createTextNode($this->getFecha());
        $nodo->appendChild($value);

        $nodo = $dom->createAttribute('sello');
        $xml->appendChild($nodo);
        $value = $dom->createTextNode($this->getSello());
        $nodo->appendChild($value);

        $nodo = $dom->createAttribute('formaDePago');
        $xml->appendChild($nodo);
        $value = $dom->createTextNode($this->getFormaDePago());
        $nodo->appendChild($value);

        $nodo = $dom->createAttribute('condicionesDePago');
        $xml->appendChild($nodo);
        $value = $dom->createTextNode($this->getCondicionesPago());
        $nodo->appendChild($value);

        $nodo = $dom->createAttribute('noCertificado');
        $xml->appendChild($nodo);
        $value = $dom->createTextNode($this->getNoCertificado());
        $nodo->appendChild($value);

        $nodo = $dom->createAttribute('certificado');
        $xml->appendChild($nodo);
//$value = $dom->createTextNode('MIIEdDCCA1ygAwIBAgIUMjAwMDEwMDAwMDAxMDAwMDU4NjcwDQYJKoZIhvcNAQEFBQAwggFvMRgwFgYDVQQDDA9BLkMuIGRlIHBydWViYXMxLzAtBgNVBAoMJlNlcnZpY2lvIGRlIEFkbWluaXN0cmFjacOzbiBUcmlidXRhcmlhMTgwNgYDVQQLDC9BZG1pbmlzdHJhY2nDs24gZGUgU2VndXJpZGFkIGRlIGxhIEluZm9ybWFjacOzbjEpMCcGCSqGSIb3DQEJARYaYXNpc25ldEBwcnVlYmFzLnNhdC5nb2IubXgxJjAkBgNVBAkMHUF2LiBIaWRhbGdvIDc3LCBDb2wuIEd1ZXJyZXJvMQ4wDAYDVQQRDAUwNjMwMDELMAkGA1UEBhMCTVgxGTAXBgNVBAgMEERpc3RyaXRvIEZlZGVyYWwxEjAQBgNVBAcMCUNveW9hY8OhbjEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTIwMAYJKoZIhvcNAQkCDCNSZXNwb25zYWJsZTogSMOpY3RvciBPcm5lbGFzIEFyY2lnYTAeFw0xMjA3MjcxNzAyMDBaFw0xNjA3MjcxNzAyMDBaMIHbMSkwJwYDVQQDEyBBQ0NFTSBTRVJWSUNJT1MgRU1QUkVTQVJJQUxFUyBTQzEpMCcGA1UEKRMgQUNDRU0gU0VSVklDSU9TIEVNUFJFU0FSSUFMRVMgU0MxKTAnBgNVBAoTIEFDQ0VNIFNFUlZJQ0lPUyBFTVBSRVNBUklBTEVTIFNDMSUwIwYDVQQtExxBQUEwMTAxMDFBQUEgLyBIRUdUNzYxMDAzNFMyMR4wHAYDVQQFExUgLyBIRUdUNzYxMDAzTURGUk5OMDkxETAPBgNVBAsTCFVuaWRhZCAxMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC2TTQSPONBOVxpXv9wLYo8jezBrb34i/tLx8jGdtyy27BcesOav2c1NS/Gdv10u9SkWtwdy34uRAVe7H0a3VMRLHAkvp2qMCHaZc4T8k47Jtb9wrOEh/XFS8LgT4y5OQYo6civfXXdlvxWU/gdM/e6I2lg6FGorP8H4GPAJ/qCNwIDAQABox0wGzAMBgNVHRMBAf8EAjAAMAsGA1UdDwQEAwIGwDANBgkqhkiG9w0BAQUFAAOCAQEATxMecTpMbdhSHo6KVUg4QVF4Op2IBhiMaOrtrXBdJgzGotUFcJgdBCMjtTZXSlq1S4DG1jr8p4NzQlzxsdTxaB8nSKJ4KEMgIT7E62xRUj15jI49qFz7f2uMttZLNThipunsN/NF1XtvESMTDwQFvas/Ugig6qwEfSZc0MDxMpKLEkEePmQwtZD+zXFSMVa6hmOu4M+FzGiRXbj4YJXn9Myjd8xbL/c+9UIcrYoZskxDvMxc6/6M3rNNDY3OFhBK+V/sPMzWWGt8S1yjmtPfXgFs1t65AZ2hcTwTAuHrKwDatJ1ZPfa482ZBROAAX1waz7WwXp0gso7sDCm2/yUVww==');
// TODO falta el no certificado
        $value = $dom->createTextNode($this->getCertificado());
        $nodo->appendChild($value);

        $nodo = $dom->createAttribute('subTotal');
        $xml->appendChild($nodo);
        $value = $dom->createTextNode($this->getSubTotal());
        $nodo->appendChild($value);

        $nodo = $dom->createAttribute('Moneda');
        $xml->appendChild($nodo);
        $value = $dom->createTextNode($this->getMoneda());
        $nodo->appendChild($value);

        $nodo = $dom->createAttribute('TipoCambio');
        $xml->appendChild($nodo);
        $value = $dom->createTextNode($this->getTipoCambio());
        $nodo->appendChild($value);

        $nodo = $dom->createAttribute('total');
        $xml->appendChild($nodo);
        $value = $dom->createTextNode($this->getTotal());
        $nodo->appendChild($value);

        $nodo = $dom->createAttribute('metodoDePago');
        $xml->appendChild($nodo);
        $value = $dom->createTextNode($this->getMetodoDePago());
        $nodo->appendChild($value);

        $nodo = $dom->createAttribute('tipoDeComprobante');
        $xml->appendChild($nodo);
        $value = $dom->createTextNode($this->getTipoDeComprobante());
        $nodo->appendChild($value);

        $nodo = $dom->createAttribute('LugarExpedicion');
        $xml->appendChild($nodo);
        $value = $dom->createTextNode($this->getLugarExpedicion());
        $nodo->appendChild($value);


// Emisor
        $emisor = $dom->createElement($localNamespace . 'Emisor', '');
        $xml->appendChild($emisor);

        $rfc = $dom->createAttribute('rfc');
        $emisor->appendChild($rfc);
        $value = $dom->createTextNode($this->getEmisor()->getRfc());
        $rfc->appendChild($value);

        $nombre = $dom->createAttribute('nombre');
        $emisor->appendChild($nombre);
        $value = $dom->createTextNode(trim($this->getEmisor()->getNombre())); // El nombre no debe llevar espacios al final
        $nombre->appendChild($value);

        $domicilioFiscal = $dom->createElement($localNamespace . 'DomicilioFiscal', '');
        $emisor->appendChild($domicilioFiscal);

        $calleEmisor = $this->getEmisor()->getDomicilioFiscal()->getCalle();
        $noExteriorEmisor = $this->getEmisor()->getDomicilioFiscal()->getNoExterior();
        $noInteriorEmisor = $this->getEmisor()->getDomicilioFiscal()->getNoInterior();
        $coloniaEmisor = $this->getEmisor()->getDomicilioFiscal()->getColonia();
        $municipioEmisor = $this->getEmisor()->getDomicilioFiscal()->getMunicipio();
        $estadoEmisor = $this->getEmisor()->getDomicilioFiscal()->getEstado();
        $paisEmisor = $this->getEmisor()->getDomicilioFiscal()->getPais();
        $codigoPostalEmisor = $this->getEmisor()->getDomicilioFiscal()->getCodigoPostal();

        if (!empty($calleEmisor)) {
            $calle = $dom->createAttribute('calle');
            $domicilioFiscal->appendChild($calle);
            $value = $dom->createTextNode($calleEmisor);
            $calle->appendChild($value);
        }

        if (!empty($noExteriorEmisor)) {
            $noExterior = $dom->createAttribute('noExterior');
            $domicilioFiscal->appendChild($noExterior);
            $value = $dom->createTextNode($noExteriorEmisor);
            $noExterior->appendChild($value);
        }

        if (!empty($noInteriorEmisor)) {
            $noInterior = $dom->createAttribute('noInterior');
            $domicilioFiscal->appendChild($noInterior);
            $value = $dom->createTextNode($noInteriorEmisor);
            $noInterior->appendChild($value);
        }

        if (!empty($coloniaEmisor)) {
            $colonia = $dom->createAttribute('colonia');
            $domicilioFiscal->appendChild($colonia);
            $value = $dom->createTextNode($coloniaEmisor);
            $colonia->appendChild($value);
        }

        if (!empty($municipioEmisor)) {
            $municipio = $dom->createAttribute('municipio');
            $domicilioFiscal->appendChild($municipio);
            $value = $dom->createTextNode($municipioEmisor);
            $municipio->appendChild($value);
        }

        if (!empty($estadoEmisor)) {
            $estado = $dom->createAttribute('estado');
            $domicilioFiscal->appendChild($estado);
            $value = $dom->createTextNode($estadoEmisor);
            $estado->appendChild($value);
        }

        if (!empty($paisEmisor)) {
            $pais = $dom->createAttribute('pais');
            $domicilioFiscal->appendChild($pais);
            $value = $dom->createTextNode($paisEmisor);
            $pais->appendChild($value);
        }

        if (!empty($codigoPostalEmisor)) {
            $codigoPostal = $dom->createAttribute('codigoPostal');
            $domicilioFiscal->appendChild($codigoPostal);
            $value = $dom->createTextNode($codigoPostalEmisor);
            $codigoPostal->appendChild($value);
        }

// Lugar de Expedicion
        $estadoExp = $this->getEmisor()->getEstadoExpedicion();
        $paisExp = $this->getEmisor()->getPaisExpedicion();

        if (!empty($estadoExp) || !empty($paisExp)) {
            $expedidoEn = $dom->createElement($localNamespace . 'ExpedidoEn', '');
            $emisor->appendChild($expedidoEn);

            if (!empty($estadoExp)) {
                $estado = $dom->createAttribute('estado');
                $expedidoEn->appendChild($estado);
                $value = $dom->createTextNode($estadoExp);
                $estado->appendChild($value);
            }

            if (!empty($paisExp)) {
                $pais = $dom->createAttribute('pais');
                $expedidoEn->appendChild($pais);
                $value = $dom->createTextNode($paisExp);
                $pais->appendChild($value);
            }
        }

        $regimenFiscal = $dom->createElement($localNamespace . 'RegimenFiscal', '');
        $emisor->appendChild($regimenFiscal);

        $regimen = $dom->createAttribute('Regimen');
        $regimenFiscal->appendChild($regimen);
        $value = $dom->createTextNode($this->getEmisor()->getRegimenFiscal()); // TODO Revisar si debe ser un arreglo
        $regimen->appendChild($value);

// Receptor
        $receptor = $dom->createElement($localNamespace . 'Receptor', '');
        $xml->appendChild($receptor);

        $rfc = $dom->createAttribute('rfc');
        $receptor->appendChild($rfc);
        $value = $dom->createTextNode($this->getReceptor()->getRfc());
        $rfc->appendChild($value);

        $nombre = $dom->createAttribute('nombre');
        $receptor->appendChild($nombre);
        $value = $dom->createTextNode(trim($this->getReceptor()->getNombre())); // El nombre no debe llevar espacioas al final
        $nombre->appendChild($value);

        $domicilioFiscal = $dom->createElement($localNamespace . 'Domicilio', '');
        $receptor->appendChild($domicilioFiscal);

        $calleReceptor = $this->getReceptor()->getDomicilio()->getCalle();
        $noExteriorReceptor = $this->getReceptor()->getDomicilio()->getNoExterior();
        $noInteriorReceptor = $this->getReceptor()->getDomicilio()->getNoInterior();
        $coloniaReceptor = $this->getReceptor()->getDomicilio()->getColonia();
        $municipioReceptor = $this->getReceptor()->getDomicilio()->getMunicipio();
        $estadoReceptor = $this->getReceptor()->getDomicilio()->getEstado();
        $paisReceptor = $this->getReceptor()->getDomicilio()->getPais();
        $codigoPostalReceptor = $this->getReceptor()->getDomicilio()->getCodigoPostal();

        if (!empty($calleReceptor)) {
            $calle = $dom->createAttribute('calle');
            $domicilioFiscal->appendChild($calle);
            $value = $dom->createTextNode($calleReceptor);
            $calle->appendChild($value);
        }

        if (!empty($noExteriorReceptor)) {
            $noExterior = $dom->createAttribute('noExterior');
            $domicilioFiscal->appendChild($noExterior);
            $value = $dom->createTextNode($noExteriorReceptor);
            $noExterior->appendChild($value);
        }

        if (!empty($noInteriorReceptor)) {
            $noInterior = $dom->createAttribute('noInterior');
            $domicilioFiscal->appendChild($noInterior);
            $value = $dom->createTextNode($noInteriorReceptor);
            $noInterior->appendChild($value);
        }

        if (!empty($coloniaReceptor)) {
            $colonia = $dom->createAttribute('colonia');
            $domicilioFiscal->appendChild($colonia);
            $value = $dom->createTextNode($coloniaReceptor);
            $colonia->appendChild($value);
        }

        if (!empty($municipioReceptor)) {
            $municipio = $dom->createAttribute('municipio');
            $domicilioFiscal->appendChild($municipio);
            $value = $dom->createTextNode($municipioReceptor);
            $municipio->appendChild($value);
        }

        if (!empty($estadoReceptor)) {
            $estado = $dom->createAttribute('estado');
            $domicilioFiscal->appendChild($estado);
            $value = $dom->createTextNode($estadoReceptor);
            $estado->appendChild($value);
        }

        if (!empty($paisReceptor)) {
            $pais = $dom->createAttribute('pais');
            $domicilioFiscal->appendChild($pais);
            $value = $dom->createTextNode($paisReceptor);
            $pais->appendChild($value);
        }

        if (!empty($codigoPostalReceptor)) {
            $codigoPostal = $dom->createAttribute('codigoPostal');
            $domicilioFiscal->appendChild($codigoPostal);
            $value = $dom->createTextNode($codigoPostalReceptor);
            $codigoPostal->appendChild($value);
        }

// Conceptos
        $conceptosArr = $this->getConceptos();
        $conceptos = $dom->createElement($localNamespace . 'Conceptos', '');
        $xml->appendChild($conceptos);

        foreach ($conceptosArr as $concepto_item) {
            $concepto = $dom->createElement($localNamespace . 'Concepto', '');
            $conceptos->appendChild($concepto);

            $nodo = $dom->createAttribute('cantidad');
            $concepto->appendChild($nodo);
            $value = $dom->createTextNode($concepto_item->getCantidad());
            $nodo->appendChild($value);

            $nodo = $dom->createAttribute('unidad');
            $concepto->appendChild($nodo);
            $value = $dom->createTextNode($concepto_item->getUnidad());
            $nodo->appendChild($value);


// No requerido
            $noId = $concepto_item->getNoIdentificacion();
            if (!empty($noId)) {
                $nodo = $dom->createAttribute('noIdentificacion');
                $concepto->appendChild($nodo);
                $value = $dom->createTextNode($concepto_item->getNoIdentificacion());
                $nodo->appendChild($value);
            }

            $nodo = $dom->createAttribute('descripcion');
            $concepto->appendChild($nodo);
            $value = $dom->createTextNode($concepto_item->getDescripcion());
            $nodo->appendChild($value);

            $nodo = $dom->createAttribute('valorUnitario');
            $concepto->appendChild($nodo);
            $value = $dom->createTextNode($concepto_item->getValorUnitario());
            $nodo->appendChild($value);

            $nodo = $dom->createAttribute('importe');
            $concepto->appendChild($nodo);
            $value = $dom->createTextNode($concepto_item->getImporte());
            $nodo->appendChild($value);
        }

// Impuestos
        $impuestosComprobante = $this->getImpuestosComprobante();
        $impuestos = $dom->createElement($localNamespace . 'Impuestos', '');
        $xml->appendChild($impuestos);

        $retenciones = $dom->createElement($localNamespace . 'Retenciones', '');
//$impuestos->appendChild($retenciones);
        $existenRetenciones = false;

        $traslados = $dom->createElement($localNamespace . 'Traslados', '');
//$impuestos->appendChild($traslados);
        $existenTraslados = false;

        $nodo = $dom->createAttribute('totalImpuestosTrasladados');
        $impuestos->appendChild($nodo);
        $value = $dom->createTextNode($impuestosComprobante->getTotalImpuestosTrasladados());
        $nodo->appendChild($value);

        $tipoImpuestos = $impuestosComprobante->getImpuestos();

        if (sizeof($tipoImpuestos) > 0) {
            foreach ($tipoImpuestos as $impuestosArr) { // Arreglo de tipos de impuestos (Retenciones, Traslados)
                $impuestoAux = new Impuesto();
                foreach ($impuestosArr as $impuesto_item) {
                    if ($impuesto_item->getTipoImpuesto() == "RETENCION") {
                        $nombreNodo = "Retencion";
                        $impuesto = $dom->createElement($localNamespace . $nombreNodo, '');
                        $retenciones->appendChild($impuesto);
                        $existenRetenciones = true;
                    } else if ($impuesto_item->getTipoImpuesto() == "TRASLADO") {
                        $nombreNodo = "Traslado";
                        $impuesto = $dom->createElement($localNamespace . $nombreNodo, '');
                        $traslados->appendChild($impuesto);
                        $existenTraslados = true;
                    }

                    $nodo = $dom->createAttribute('impuesto');
                    $impuesto->appendChild($nodo);
                    $value = $dom->createTextNode($impuesto_item->getTipoImpuesto());
                    $nodo->appendChild($value);

                    if ($impuesto_item->getTipoImpuesto() == "TRASLADO") {
                        $nodo = $dom->createAttribute('tasa');
                        $impuesto->appendChild($nodo);
                        $value = $dom->createTextNode($impuesto_item->getTasa());
                        $nodo->appendChild($value);
                    }

                    $nodo = $dom->createAttribute('importe');
                    $impuesto->appendChild($nodo);
                    $value = $dom->createTextNode($impuesto_item->getImporte());
                    $nodo->appendChild($value);
                }
            }

// Solo si existen retenciones o traslados se agrega el nodo respectivo al XML
            if ($existenRetenciones)
                $impuestos->appendChild($retenciones);

            if ($existenTraslados)
                $impuestos->appendChild($traslados);

            if ($this->agregarXMLTimbrado) {
                $xmlTimbrado = $dom->createElement($localNamespace . 'Complemento', '');
                $xml->appendChild($xmlTimbrado);

                $timbradoAttr = $dom->createElement($namespaceTimbrado . 'TimbreFiscalDigital', '');
                $xmlTimbrado->appendChild($timbradoAttr);

                $nodo = $dom->createAttribute('xmlns:tfd');
                $timbradoAttr->appendChild($nodo);
                $value = $dom->createTextNode($this->tfdNameSpace);
                $nodo->appendChild($value);

                $nodo = $dom->createAttribute('xsi:schemaLocation');
                $timbradoAttr->appendChild($nodo);
                $value = $dom->createTextNode($this->tfdSchemaLocation);
                $nodo->appendChild($value);

                $nodo = $dom->createAttribute('selloCFD');
                $timbradoAttr->appendChild($nodo);
                $value = $dom->createTextNode($this->tfdSelloCFD);
                $nodo->appendChild($value);

                $nodo = $dom->createAttribute('FechaTimbrado');
                $timbradoAttr->appendChild($nodo);
                $value = $dom->createTextNode($this->tfdFechaTimbrado);
                $nodo->appendChild($value);

                $nodo = $dom->createAttribute('UUID');
                $timbradoAttr->appendChild($nodo);
                $value = $dom->createTextNode($this->tfdUUID);
                $nodo->appendChild($value);

                $nodo = $dom->createAttribute('noCertificadoSAT');
                $timbradoAttr->appendChild($nodo);
                $value = $dom->createTextNode($this->tfdNoCertificadoSAT);
                $nodo->appendChild($value);

                $nodo = $dom->createAttribute('version');
                $timbradoAttr->appendChild($nodo);
                $value = $dom->createTextNode($this->tfdVersion);
                $nodo->appendChild($value);

                $nodo = $dom->createAttribute('selloSAT');
                $timbradoAttr->appendChild($nodo);
                $value = $dom->createTextNode($this->tfdSelloSAT);
                $nodo->appendChild($value);
            }
        } else {
            echo "</br>ERROR: Arreglo de tipo de impuestos vacio. </br>";
        }


//echo 'Archivo XML: ' . $dom->save("test.xml");
//return $dom->saveXML();
        return $dom;
    }

    /*
     * Valida el comprobante antes de enviarlo a procesar.
     *  Descripcion errores
     *    115. RFC vacio
     */

    public function ValidaComprobante() {
        $errores = array();

        $rfcReceptor = $this->getReceptor()->getRfc();

// RFC no debe estar en blanco
        if (empty($rfcReceptor)) {
            array_push($errores, 115);
        }

        return $errores;
    }

    public function getXML() {
        $dom = $this->getDomXml();

        return $dom->saveXML();
    }

    public function escribeArchivoXML() {
        $dom = $this->getDomXml();

        $dom->save("factura.xml");

        return "Nombre archivo: <a href='factura.xml'>factura.xml</a>";
    }

    public function imprimeInfo() {
        echo $this->getInfo();
    }

// Getters y Setters
    public function getVersion() {
        return $this->version;
    }

    public function setVersion($version) {
        $this->version = $version;
    }

    public function getSerie() {
        return $this->serie;
    }

    public function setSerie($serie) {
        $this->serie = $serie;
    }

    public function getFolio() {
        return $this->folio;
    }

    public function setFolio($folio) {
        $this->folio = $folio;
    }

    public function getFecha() {
        return $this->fecha;
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    public function getSello() {
        return $this->sello;
    }

    public function setSello($sello) {
        $this->sello = $sello;
    }

    public function getFormaDePago() {
        return $this->formaDePago;
    }

    public function setFormaDePago($formaDePago) {
        $this->formaDePago = $formaDePago;
    }

    public function getNoCertificado() {
        return $this->noCertificado;
    }

    public function setNoCertificado($noCertificado) {
        $this->noCertificado = $noCertificado;
    }

    public function getCertificado() {
        return $this->certificado;
    }

    public function setCertificado($certificado) {
//
//        Dbg::data($certificado);
//        Dbg::pd($this->certificado);

        $this->certificado = $certificado;
    }

    public function getCondicionesPago() {
        return $this->condicionesDePago;
    }

    public function setCondicionesPago($condicionesDePago) {
        $this->condicionesDePago = $condicionesDePago;
    }

    public function getSubTotal() {
        return Facturacion::formatoImporte($this->subTotal);
    }

    public function setSubTotal($subTotal) {
        $this->subTotal = Facturacion::formatoImporte($subTotal);
    }

    public function getMoneda() {
        return $this->Moneda;
    }

    public function setMoneda($Moneda) {
        $this->Moneda = $Moneda;
    }

    public function getTotal() {
        return Facturacion::formatoImporte($this->total);
    }

    public function setTotal($total) {
        $this->total = Facturacion::formatoImporte($total);
    }

    public function getMetodoDePago() {
        return $this->metodoDePago;
    }

    public function setMetodoDePago($metodoDePago) {
        $this->metodoDePago = $metodoDePago;
    }

    public function getTipoDeComprobante() {
        return $this->tipoDeComprobante;
    }

    public function setTipoDeComprobante($tipoDeComprobante) {
        $this->tipoDeComprobante = $tipoDeComprobante;
    }

    public function getEmisor() {
        return $this->Emisor;
    }

    public function setEmisor($Emisor) {
        $this->Emisor = $Emisor;
    }

    public function getReceptor() {
        return $this->Receptor;
    }

    public function setReceptor($Receptor) {
        $this->Receptor = $Receptor;
    }

    public function getConceptos() {
        return $this->Conceptos;
    }

    public function setConceptos($Conceptos) {
        $this->Conceptos = $Conceptos;
    }

    public function getImpuestosComprobante() {
        return $this->ImpuestosComprobante;
    }

    public function setImpuestosComprobante($ImpuestosComprobante) {
        $this->ImpuestosComprobante = $ImpuestosComprobante;
    }

    public function getTipoCambio() {
        return $this->TipoCambio;
    }

    public function setTipoCambio($TipoCambio) {
        $this->TipoCambio = $TipoCambio;
    }

    public function getLugarExpedicion() {
        return $this->LugarExpedicion;
    }

    public function setLugarExpedicion($LugarExpedicion) {
        $this->LugarExpedicion = $LugarExpedicion;
    }

    function getAgregarXMLTimbrado() {
        return $this->agregarXMLTimbrado;
    }

    function getTfdSchemaLocation() {
        return $this->tfdSchemaLocation;
    }

    function getTfdVersion() {
        return $this->tfdVersion;
    }

    function getTfdFechaTimbrado() {
        return $this->tfdFechaTimbrado;
    }

    function getTfdSelloCFD() {
        return $this->tfdSelloCFD;
    }

    function getTfdNoCertificadoSAT() {
        return $this->tfdNoCertificadoSAT;
    }

    function getTfdSelloSAT() {
        return $this->tfdSelloSAT;
    }

    function getTfdUUID() {
        return $this->tfdUUID;
    }

    function setAgregarXMLTimbrado($agregarXMLTimbrado) {
        $this->agregarXMLTimbrado = $agregarXMLTimbrado;
    }

    function setTfdSchemaLocation($tfdSchemaLocation) {
        $this->tfdSchemaLocation = $tfdSchemaLocation;
    }

    function setTfdVersion($tfdVersion) {
        $this->tfdVersion = $tfdVersion;
    }

    function setTfdFechaTimbrado($tfdFechaTimbrado) {
        $this->tfdFechaTimbrado = $tfdFechaTimbrado;
    }

    function setTfdSelloCFD($tfdSelloCFD) {
        $this->tfdSelloCFD = $tfdSelloCFD;
    }

    function setTfdNoCertificadoSAT($tfdNoCertificadoSAT) {
        $this->tfdNoCertificadoSAT = $tfdNoCertificadoSAT;
    }

    function setTfdSelloSAT($tfdSelloSAT) {
        $this->tfdSelloSAT = $tfdSelloSAT;
    }

    function setTfdUUID($tfdUUID) {
        $this->tfdUUID = $tfdUUID;
    }

    function getTfdNameSpace() {
        return $this->tfdNameSpace;
    }

    function setTfdNameSpace($tfdNameSpace) {
        $this->tfdNameSpace = $tfdNameSpace;
    }

    public function getDescuento() {
        return Facturacion::formatoImporte($this->descuento);
    }

    public function getMotivoDescuento() {
        return $this->motivoDescuento;
    }

    public function getNumCtaPago() {
        return $this->NumCtaPago;
    }

    public function getFolioFiscalOrig() {
        return $this->FolioFiscalOrig;
    }

    public function getSerieFolioFiscalOrig() {
        return $this->SerieFolioFiscalOrig;
    }

    public function getFechaFolioFiscalOrig() {
        return $this->FechaFolioFiscalOrig;
    }

    public function getMontoFolioFiscalOrig() {
        return $this->MontoFolioFiscalOrig;
    }

}

?>