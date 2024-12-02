<?php

class DatosComprobante {

  public $version;
  public $serie;
  public $folio;
  public $fecha;
  public $sello;
  public $formaDePago;
  public $noCertificado;
  public $certificado;
  public $subTotal;
  public $moneda;
  public $tipoCambio;
  public $total;
  public $metodoDePago;
  public $tipoDeComprobante;


  public $emisorRfc;
  public $emisorNombre;
  public $estadoExpedicion;
  public $paisExpedicion;
  public $emisorCalle;
  public $emisorNoExterior;
  public $emisorNoInterior;
  public $emisorColonia;
  public $emisorMunicipio;
  public $emisorEstado;
  public $emisorPais;
  public $emisorCodigoPostal;

  public $conceptosArr;

  public $totalImpuestosTrasladados;
  public $impuestosArr;

  // Para comprobantes timbrados
  public $folioFiscal;
  public $fechaTimbrado;
  public $certificadoSAT;
  public $selloSAT;

  // XML
  private $xmlComprobante;

  function __construct() {

  }

  public function asignaDatosXML() {
    $xml = new SimpleXMLElement($this->xmlComprobante);

    $atributosComprobante = $xml->attributes();

    // Wrap de datos del comprobante
    $this->version = $atributosComprobante->version;
    $this->serie = $atributosComprobante->serie;
    $this->folio = $atributosComprobante->folio;
    $this->fecha = $atributosComprobante->fecha;
    $this->sello = $atributosComprobante->sello;
    $this->formaDePago = $atributosComprobante->formaDePago;
   // $this->noCertificado = $atributosComprobante->noCertificado;
    $this->certificado = $atributosComprobante->certificado;
    $this->subTotal = $atributosComprobante->subTotal;
    $this->moneda = $atributosComprobante->Moneda;
    $this->total = $atributosComprobante->total;
    $this->metodoDePago = $atributosComprobante->metodoDePago;
    $this->tipoDeComprobante = $atributosComprobante->tipoDeComprobante;
    $this->noCertificado = $atributosComprobante->noCertificado;

    // Comprobante
    $comprobante = $xml->children('http://www.sat.gob.mx/cfd/3');

    // Datos del Emisor
    $emisorXML = $comprobante->Emisor;
    $atributosEmisor = $emisorXML->attributes();

    // Domicilio emisor
    $domicilioEmisor = $emisorXML->DomicilioFiscal;
    $atrDomEmisor = $domicilioEmisor->attributes();
    // Lugar de expedicion
    $expedidoEn = $emisorXML->ExpedidoEn;
    $atrExpedidoEn = $expedidoEn->attributes();

    // Domicilio emisor
    $domicilioEmisor = $emisorXML->DomicilioFiscal;
    $atrDomEmisor = $domicilioEmisor->attributes();
    // Lugar de expedicion
    $expedidoEn = $emisorXML->ExpedidoEn;
    $atrExpedidoEn = $expedidoEn->attributes();


    // Wrapper Emisor
    $this->emisorRfc = $atributosEmisor->rfc;
    $this->emisorNombre = $atributosEmisor->nombre;

    // MDB Datos quq ya no se usan
    //$this->estadoExpedicion = $atrExpedidoEn->estado;
    //$this->paisExpedicion = $atrExpedidoEn->pais;

    $this->emisorCalle = $atrDomEmisor->calle;
    $this->emisorNoExterior = $atrDomEmisor->noExterior;
    $this->emisorNoInterior = $atrDomEmisor->noInterior;
    $this->emisorColonia = $atrDomEmisor->colonia;
    $this->emisorMunicipio = $atrDomEmisor->municipio;
    $this->emisorEstado = $atrDomEmisor->estado;
    $this->emisorPais = $atrDomEmisor->pais;
    $this->emisorCodigoPostal = $atrDomEmisor->codigoPostal;

    // Conceptos del comprobante
    $this->conceptosArr = array();
    $this->impuestosArr = array();

    foreach($comprobante as $item) {
      $conceptos = $item->children('http://www.sat.gob.mx/cfd/3');
      foreach($conceptos->Concepto as $concepto) {
        array_push($this->conceptosArr, $concepto);
      }
    }

    // Impuestos
    $ImpuestosArr = array();
    $RetencionesArr = array();
    $TrasladosArr = array();

    $impuestos = $comprobante->Impuestos;
    $this->totalImpuestosTrasladados = $impuestos->attributes()->totalImpuestosTrasladados;

    foreach($impuestos->Retenciones as $retenciones) {
      foreach($retenciones as $retencion) {
        $Impuesto = new Impuesto();
        $Impuesto->setTipoRegistro("RETENCION");
        $Impuesto->setTipoImpuesto((string)$retencion->attributes()->impuesto);
        $Impuesto->setImporte((string)$retencion->attributes()->importe);
        //array_push($RetencionesArr, $Impuesto);
        array_push($this->impuestosArr, $Impuesto);
      }
    }


    foreach($impuestos->Traslados as $traslados) {
      foreach($traslados as $traslado) {
        $Impuesto = new Impuesto();
        $Impuesto->setTipoRegistro("TRASLADO");
        $Impuesto->setTipoImpuesto((string)$traslado->attributes()->impuesto);
        $Impuesto->setTasa((string)$traslado->attributes()->tasa);
        $Impuesto->setImporte((string)$traslado->attributes()->importe);
        //array_push($TrasladosArr, $Impuesto);
        array_push($this->impuestosArr, $Impuesto);
      }
    }

    //array_push($this->impuestosArr, $RetencionesArr, $TrasladosArr);

    // Datos del timbrado del PAC/SAT
    $timbradoXML = $comprobante->Complemento;
    $timbreFiscalDigital = $timbradoXML->children('http://www.sat.gob.mx/TimbreFiscalDigital');
    $atributosTimbrado = $timbreFiscalDigital->attributes();

    $this->folioFiscal    = $atributosTimbrado->UUID;
    $this->fechaTimbrado  = $atributosTimbrado->FechaTimbrado;
    $this->certificadoSAT = $atributosTimbrado->noCertificadoSAT;
    $this->selloSAT       = $atributosTimbrado->selloSAT;

  }

  public function setXmlComprobante($xml) {
    $this->xmlComprobante = $xml;
    $this->asignaDatosXml();
  }
  public function getXmlComprobante() {
    return $this->xmlComprobante;
  }
}
?>