<?php

class Facturacion {

    // Atributos
    public $total = 0;
    public $subTotal = 0;
    public $totalImpuestosTrasladados = 0;
    public $totalImpuestosRetenidos = 0;

    /**
     * EGMC 20151112
     * Variable para asignar el PAC de facturación a utilizar
     * PACs integrados
     * SolucionFactible
     * FoliosDigitales
     * FacturadorElectronico
     */
    private $timbrarEn = 'FacturadorElectronico';

    public function timbrar($data, $saveXML = true, $pathFacturas = PATH_ADM_FACTURAS) {


        $this->total = 0;
        $this->subTotal = 0;

//        Dbg::data($data);
//        Dbg::pd();

        /**
         * INICIA ASINGNACIÓN DE DATOS DE EMISOR
         */
        /**
         * EGMC 20151111
         * EMISOR
         * Asignación del domicilio fiscal del Emisor
         * $data['Emisor']['DomicilioFiscal']
         */
        extract($data['Emisor']['DomicilioFiscal']);
        $DomicilioFiscal = new Domicilio($calle, $noExterior, $noInterior, $colonia, $municipio, $estado, $pais, $codigoPostal);
//        Dbg::data($DomicilioFiscal);

        /**
         * EGMC 20151111
         * EMISOR
         * Asignación de Lugar de expedición de la factura 
         */
        extract($data['Emisor']['ExpedidoEn']);
        $ExpedidoEn = new Domicilio($calle, $noExterior, $noInterior, $colonia, $municipio, $estado, $pais, $codigoPostal);
//        Dbg::data($ExpedidoEn);

        /**
         * EGMC 20151111
         * EMISOR
         * Asignamos datos del emisor y crea el objeto
         */
        extract($data['Emisor']['Emisor']);
        $Emisor = new Emisor($rfc, $nombre, $DomicilioFiscal, $Regimen, $ExpedidoEn);
//        Dbg::pd($Emisor);

        /**
         * TERMINA ASINGNACIÓN DE DATOS DE EMISOR
         */
        /**
         * INICIA ASINGNACIÓN DE DATOS DE RECEPTOR
         */
        /**
         * EGMC 20151111
         * RECEPTOR
         * Asignación del domicilio fiscal del RECEPTOR
         * $data['Receptor']['Domicilio']
         */
        extract($data['Receptor']['Domicilio']);
        $Domicilio = new Domicilio($calle, $noExterior, $noInterior, $colonia, $municipio, $estado, $pais, $codigoPostal);
//        Dbg::data($Domicilio);

        /**
         * EGMC 20151111
         * EMISOR
         * Asignamos datos del emisor y crea el objeto
         */
        extract($data['Receptor']['Receptor']);
        $Receptor = new Receptor($rfc, $nombre, $Domicilio);
//        Dbg::data($Receptor);

        /**
         * TERMINA ASINGNACIÓN DE DATOS DE RECEPTOR
         */
        /**
         * INICIA ASIGNACIÓN DE CONCEPTOS
         */
        $Conceptos = array();
        foreach ($data['Conceptos'] as $concepto) {
            extract($concepto);
//            Dbg::data($importe);
//            Dbg::data(floatval($importe));
            if (floatval($importe) <= 0) {
                $importe = $cantidad * $valorUnitario;
            }
//            Dbg::data($importe);
            $Conceptos[] = new Concepto($cantidad, $unidad, $noIdentificacion, $descripcion, $valorUnitario, $importe);
            $this->subTotal += $importe;
        }
//        Dbg::pd($conceptos);

        /**
         * TERMINA ASIGNACIÓN DE CONCEPTOS
         */
        /**
         * INICIA ASIGNACIÓN DE IMPUESTOS
         */
        $impuestos = array();
        foreach ($data['Impuestos'] as $impuestoData) {
            extract($impuestoData);



            if ($tipoImpuesto = 'TRASLADO') {

                if (floatval($importe) <= 0) {
                    /**
                     * EGMC 
                     * Esto está mal pero para pruebas lo dejamos
                     */
                    $importe = $this->subTotal * $tasa;
                }

                $this->totalImpuestosTrasladados+=$importe;
            } elseif ($tipoImpuesto = 'RETENCION') {

                if (floatval($importe) <= 0) {
                    /**
                     * EGMC 
                     * ESTO ESTÁ MAL PERO PARA HACER PRUEBAS LO DEJAMOS
                     */
                    $importe = $this->subTotal;
                }

                $this->totalImpuestosRetenidos+=$importe;
            }

            $impuestos[] = new Impuesto($tipoImpuesto, $impuesto, $importe, $tasa);
            /**
             * EGMC 
             * ESTO ESTÁ MAL PERO PARA HACER PRUEBAS LO DEJAMOS
             */
            $this->total +=$importe + $this->subTotal;
        }
//        Dbg::data($impuestos);
        /**
         * EGMC 20151112 
         * IMPUESTOS
         * Se asignan datos de los IMPUESTOS
         */
        $ImpuestosComprobante = new ImpuestosComprobante($this->totalImpuestosTrasladados, $this->totalImpuestosRetenidos, $impuestos);
//        Dbg::pd($ImpuestosComprobante);
        /**
         * TERMINA ASIGNACIÓN DE IMPUESTOS
         */
        /**
         * INICIA ASIGNACIÓN DE COMPROBANTE
         */
        /**
         * EGMC 20151111
         * COMPROBANTE
         * Se asiganan datos del COMPROBANTE y crea el objeto
         */
        extract($data['Comprobante']);

        if (floatval($subTotal) <= 0) {
            $subTotal = $this->subTotal;
        }

        if (floatval($total) <= 0) {
            $total = $this->total;
        }

        $Comprobante = new Comprobante($version, $serie, $folio, $fecha, $sello, $formaDePago, $noCertificado, $certificado, $condicionesDePago, $subTotal, $descuento, $motivoDescuento, $TipoCambio, $Moneda, $total, $tipoDeComprobante, $metodoDePago, $LugarExpedicion, $Emisor, $Receptor, $Conceptos, $ImpuestosComprobante);

        /**
         * EGMC 20151112
         * Asignación de los atributos:
         * certificado
         * noCertificado
         * sello
         */
        $Comprobante->setCertificado($this->_getCertificado($data['Emisor']['Certificados']));
        $Comprobante->setNoCertificado($data['Emisor']['Certificados']['noCertificado']);
        $Comprobante->setSello($this->_getSello($Comprobante, $data['Emisor']['Certificados']));
//      Dbg::pd($Comprobante);

        /**
         * TERMINA ASIGNACIÓN DE COMPROBANTE
         */
        $xmlCFDI = $Comprobante->getXML();
        /**
         * TERMINA ASIGNACIÓN DE COMPROBANTE
         */
        $xmlErrors = array();
        $xmlErrors = $Comprobante->ValidaComprobante();
//        Dbg::pd($xmlErrors);

        $dataResult = array();
        /**
         * EGMC 20151113
         * Realiza el timbrado con del xml por medio de los 3 diferentes PACs
         */
        if (empty($xmlErrors)) {

            if ($this->timbrarEn == 'FacturadorElectronico') {
                $PacFacturadorElectronico = new PacFacturadorElectronico();
                $dataResult = $PacFacturadorElectronico->timbrar($Comprobante);
            } elseif ($this->timbrarEn == 'FoliosDigitales') {
                $PacFoliosDigitales = new PacFoliosDigitales();
                $dataResult = $PacFoliosDigitales->timbrar($Comprobante);
            } elseif ($this->timbrarEn == 'SolucionFactible') {
                $PacSolucionFactible = new PacSolucionFactible();
                $dataResult = $PacSolucionFactible->timbrar($Comprobante);
            }
        }

        /**
         * EGMC 20151113
         * Verificación de errores
         */
        if (!isset($dataResult['error'])) {
            /**
             * EGMC 20151113
             * Guardado físico del archivo timbrado
             */
            if ($saveXML) {
                $dataResult['pathCFDITimbrado'] = $this->saveXML($dataResult['xmlCFDITimbrado']);
            }
        }

        return $dataResult;
    }

    private function _getCertificado($certificados) {

//        $certificados['cer'] = PATH_ADM_PEM . '/' .'goya780416gm0_1210221537s.cer';
        if (file_exists($certificados['cer'])) {
            return $certificado = str_replace(array(chr(10), chr(1)), '', base64_encode(file_get_contents($certificados['cer'])));
        }
    }

    private function _getSello(Comprobante $Comprobante, $certificados, $method = 'Marco') {

//        $certificados['keyPEM'] = PATH_ADM_PEM . '/' .'goya780416gm0_1210221537s.key.pem';
        $sello = false;
        if (file_exists($certificados['keyPEM'])) {
            if ($method == 'Marco') {

                $cadenaOriginal = $Comprobante->getCadenaOriginal();
//    Dbg::data($cadenaOriginal);
                $fileKeyPEM = fopen($certificados['keyPEM'], "r");
                $privateKey = fread($fileKeyPEM, 8192);
//    Dbg::data($privateKey);
                fclose($fileKeyPEM);
                $privateKeyId = openssl_get_privatekey($privateKey, '12345678a');
//    Dbg::data($privateKeyId);
                openssl_sign($cadenaOriginal, $cadenaFirmada, $privateKeyId, OPENSSL_ALGO_SHA1);
//    Dbg::data($cadenaFirmada);    
                $sello = base64_encode($cadenaFirmada);
//    Dbg::data($sello);
            } elseif ($method == 'Eddin') {

                $xmlCFDI = $Comprobante->getXML();

                $cfdiDomDocument = new DomDocument();
                $cfdiDomDocument->loadXML($xmlCFDI) or die("XML invalido");
//debug($xdoc);
//die;
                $xlst = new DOMDocument();
                $xlst->load(PATH_ADM_PEM . '/' . 'cadenaoriginal_3_2.xslt');
//phpinfo();
                $XSLTProcessor = new XSLTProcessor();
//phpinfo(); 
// ini_set('allow_url_fopen','ON');
//phpinfo();
                $XSLTProcessor->importStyleSheet($xlst);

                $privateKey = openssl_pkey_get_private(file_get_contents($certificados['keyPEM']));
                $originalString = $XSLTProcessor->transformToXML($cfdiDomDocument);
                openssl_sign($originalString, $signature, $privateKey);
//        Dbg::data(base64_encode($signature));
                $sello = base64_encode($signature);
            }
//        Dbg::pd();
        }
        return $sello;
    }

    function cancelar($fl_factura) {
//    $clave = $fl_factura;

        /*         * **************************************************************************
          // Conectandose a los servicios del PAC
         * *************************************************************************** */
        $urlWS = ObtenConfiguracion(700);

        // Crea el cliente para conectar al web service de folios digitales
        $client = new nusoap_client($urlWS, true);

        $result = $client->call('CancelarCFDI', array('usuario' => ObtenConfiguracion(701), 'password' => ObtenConfiguracion(702), 'cadenaXML' => $xml_enviado, 'Referencia' => $referenciaCliente));


        // Procesa la respuestas del PAC
        $arrRespuestaPAC = array();

        if (is_array($result)) {
            foreach ($result as $r1) {
                if (is_array($r1)) {
                    foreach ($r1 as $r2) {
                        if (is_array($r1)) {
                            foreach ($r2 as $r3) {
                                $arrRespuestaPAC = $r2;
                            }
                        }
                    }
                }
            }
        }
    }

    public function saveXML($xmlCFDI, $pathFacturas = PATH_ADM_FACTURAS) {
        // Creacion del objeto que contiene los datos de la factura
        $comprobanteTimbrado = new DatosComprobante();
        $comprobanteTimbrado->setXmlComprobante($xmlCFDI);

        $domDocument = new DOMDocument('1.0', 'utf-8');
        $domDocument->loadXML($xmlCFDI);
        $pathXMLCFDI = $pathFacturas . "/" . $comprobanteTimbrado->folioFiscal . ".xml";
        $domDocument->save($pathXMLCFDI);

        return $pathXMLCFDI;
    }

    public static function formatoImporte($valor, $decimales = 2) {
        if ($valor != '') {
            $valor = str_replace('.00', '', number_format($valor, $decimales, '.', ''));
        }

        return $valor;
    }

    public function toXML(SimpleXMLElement $object, array $data) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $new_object = $object->addChild($key);
                $this->toXML($new_object, $value);
            } else {
                $object->addChild($key, $value);
            }
        }
    }

}
