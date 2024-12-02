<?php

class Facturacion {

    public $total = 0;
    public $subTotal = 0;
    public $totalImpuestosTrasladados = 0;
    public $totalImpuestosRetenidos = 0;

    public function timbrar($data) {

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

        $Comprobante->setCertificado($this->_getCertificado($data['Emisor']['Certificados']));
        $Comprobante->setNoCertificado($data['Emisor']['Certificados']['noCertificado']);
        $Comprobante->setSello($this->_getSello($Comprobante, $data['Emisor']['Certificados']));
//        Dbg::pd($Comprobante);

        $xmlCFDI = $Comprobante->getXML();
        /**
         * TERMINA ASIGNACIÓN DE COMPROBANTE
         */
        $xmlErrors = array();
        $xmlErrors = $Comprobante->ValidaComprobante();
//        Dbg::pd($xmlErrors);


        define(SOL_FACT, "SolucionFactible");
        define(FOL_DIG, "FoliosDigitales");
        define(FACT_ELECT, "FacturadorElectronico");

        $timbrarEn = FACT_ELECT;

        if ($timbrarEn == FACT_ELECT) {
            $usrPAC = "test";
            $pwdPAC = "TEST";
            $urlWS = "https://stagecfdi.facturadorelectronico.com/wstimbrado/Timbrado.asmx?WSDL";
            //Pruebas anterior "https://pruebas.facturadorelectronico.com/clientes/wstimbrado/Timbrado.asmx?WSDL";
            //$urlWS = "https://cfdi.facturadorelectronico.com/wstimbrado/timbrado.asmx?WSDL"; // Prod
        } elseif ($timbrarEn == FOL_DIG) {
            $usrPAC = ObtenConfiguracion(701);
            $pwdPAC = ObtenConfiguracion(702);
            $urlWS = ObtenConfiguracion(700);
        } elseif ($timbrarEn == SOL_FACT) {
            $usrPAC = "testing@solucionfactible.com";
            $pwdPAC = "timbrado.SF.16672";
            $urlWS = "https://testing.solucionfactible.com/ws/services/Timbrado?wsdl";
        }

        $arrRespuestaPAC = array();

        if (empty($xmlErrors)) {

            /*             * **************************************************************************
              // Conectandose a los servicios del PAC
             * *************************************************************************** */


            if ($timbrarEn == FACT_ELECT) {

                // Crea el cliente para conectar al web service de folios digitales
                $client = new nusoap_client($urlWS, true);

                // Comentado porque ya me acabe los timbres 
                $result = $client->call('obtenerTimbrado', array('Usuario' => $usrPAC, 'password' => $pwdPAC, 'CFDIcliente' => (($xmlCFDI))));

                $xml = new SimpleXMLElement('<obtenerTimbrado/>');

                $Facturacion = new Facturacion();
                $Facturacion->toXML($xml, $result);

                // Para saber que elementos tiene el XML que regresa el PAC
                // usar la siguiente instruccion sobre los elementos puede ayudar var_dump($timbre);      
                $timbre = $xml->obtenerTimbradoResult->timbre;

//        $esValido = $timbre->{"!esValido"}; // True o False pero como string
                if (isset($timbre->{"!esValido"}) && strtoupper($timbre->{"!esValido"}) == 'TRUE') {
//            Dbg::pd($timbre);
                    $xmlTimbrado = $timbre->TimbreFiscalDigital;

                    $schemaLocation = $xmlTimbrado->{"schemaLocation"};
                    $version = $xmlTimbrado->{"!version"};
                    $fechaTimbrado = $xmlTimbrado->{"!FechaTimbrado"};
                    $selloCFD = $xmlTimbrado->{"!selloCFD"};
                    $noCertificadoSAT = $xmlTimbrado->{"!noCertificadoSAT"};
                    $selloSAT = $xmlTimbrado->{"!selloSAT"};
                    $UUID = $xmlTimbrado->{"!UUID"};

                    $arr_schema = explode(" ", $schemaLocation);
                    $tfdNameSpace = $arr_schema[0];

                    // Agrega los valores del timbrado para crear el XML completo
                    $ComprobanteTimbrado = $Comprobante;
                    $ComprobanteTimbrado->setTfdSchemaLocation($schemaLocation);
                    $ComprobanteTimbrado->setTfdNameSpace($tfdNameSpace);
                    $ComprobanteTimbrado->setTfdVersion($version);
                    $ComprobanteTimbrado->setTfdFechaTimbrado($fechaTimbrado);
                    $ComprobanteTimbrado->setTfdSelloCFD($selloCFD);
                    $ComprobanteTimbrado->setTfdNoCertificadoSAT($noCertificado);
                    $ComprobanteTimbrado->setTfdSelloSAT($selloSAT);
                    $ComprobanteTimbrado->setTfdUUID($UUID);
                    $ComprobanteTimbrado->setAgregarXMLTimbrado(True);
//            Dbg::pd($ComprobanteTimbrado);
                } else {

//            foreach($timbre->errores->Error as $error){
//                Dbg::data($error);
//            }
                    echo htmlentities($xmlCFDI);
                    Dbg::data($cadenaOriginal);
                    Dbg::pd($timbre);
                }
            }  // Fin FACT_ELECT
            elseif ($timbrarEn == FOL_DIG) {

                // Crea el cliente para conectar al web service de folios digitales
                $client = new nusoap_client($urlWS, true);

                // Comentado porque ya me acabe los timbres 
                $result = $client->call('TimbrarPruebaCFDI', array('usuario' => $usrPAC, 'password' => $pwdPAC, 'cadenaXML' => $xmlCFDI, 'Referencia' => $referenciaCliente));

                // Procesa la respuestas del PAC
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
            } elseif ($timbrarEn == SOL_FACT) {
                try {
                    $client = new SoapClient($urlWS);
                    $params = array('usuario' => $usrPAC, 'password' => $pwdPAC, 'cfdiBase64' => base64_encode(utf8_encode(str_ascii($xmlCFDI))), 'zip' => False);
                    $response = $client->__soapCall('timbrarBase64', array('parameters' => $params));
                } catch (SoapFault $fault) {
                    echo "SOAPFault: " . $fault->faultcode . "-" . $fault->faultstring . "\n";
                }

                $ret = $response->return;
            }  // FIN SOL FACT    




            /* Posibles valores del arreglo que regresa el PAC
             *
             *  RESPUESTA CORRECTA
             *    Respuesta(0) = ""
             *    Respuesta(1) = ""
             *    Respuesta(2) = ""
             *    Resultado(3) = En esta posici�n se env�a el contenido del XML timbrado con el complemento Timbre Fiscal Digital, que le permitir� actualizar el comprobante enviado.
             *    Resultado(4) = En esta posici�n se env�a el contenido del XML del acuse de env�o del CFDI respondido por el SAT, se debe guardar el contenido, ya que es el comprobante de la autenticidad del CFDI.
             *
             *  ERROR
             *    Respuesta(0) = Aqu� se env�a el c�digo de error, que puede ser referente al XML enviado o a la forma de accesar el servicio. Ej. 301, 302.
             *    Respuesta(1) = Mensaje del error producido.
             *    Respuesta(2) = Mensaje opcional con informaci�n que complementa el error para su mejor comprensi�n.
             *    Resultado(3) = ""
             *    Resultado(4) = ""
             *
             *    *** Se recomienda validar las posiciones que van del (0) al (2), si estas NO regresan vac�as se produjo un error.
             */


            // Genera permanentemente el archivo XML de la factura
            /*      $Query  = "SELECT ds_xml_respuesta FROM k_factura_electronica WHERE fl_factura=$fl_factura";
              $row = RecuperaValor($Query);

              if (!empty($row[0])) {
              $domDocument = new DOMDocument('1.0', 'utf-8');
              $domDocument->loadXML(utf8_encode(str_ascii($row[0])));

              $domDocument->save("./facturas/" . $comprobanteTimbrado->folioFiscal . ".xml");
              } */
        } // Termina IF $xmlErrors
// Validar el arreglo para saber si es respuesta correcta
// El arreeglo $xmlErrors es el que usamos nosotros para acumular errores, por ejemplo si el RFC esta en blanco
        if ((empty($arrRespuestaPAC[0]) && empty($arrRespuestaPAC[1]) && empty($arrRespuestaPAC[2]) && empty($xmlErrors) && $timbrarEn == FOL_DIG ) || ( empty($xmlErrors) && $timbrarEn == SOL_FACT && ($ret->status == 200) ) || ( empty($xmlErrors) && $timbrarEn == FACT_ELECT && isset($timbre->{"!esValido"}) && strtoupper($timbre->{"!esValido"}) == 'TRUE' )) {

//    Dbg::data('Entró');
            if ($timbrarEn == FACT_ELECT) {
//    Dbg::data('Entró 2');
                $resultados = $ret->resultados;
                $xmlTimbrado = $ComprobanteTimbrado->getXML();
                $acuseRecibo = $resultados->uuid;
            } elseif ($timbrarEn == SOL_FACT) {
                $resultados = $ret->resultados;
                $xmlTimbrado = (($resultados->cfdiTimbrado));
                $acuseRecibo = $resultados->uuid;
            } elseif ($timbrarEn == FOL_DIG) {
                $xmlTimbrado = $arrRespuestaPAC[3];
                $acuseRecibo = $arrRespuestaPAC[4];
            }

            // Creacion del objeto que contiene los datos de la factura
            $comprobanteTimbrado = new DatosComprobante();
            $comprobanteTimbrado->setXmlComprobante($xmlTimbrado);

            $domDocument = new DOMDocument('1.0', 'utf-8');
            $domDocument->loadXML($xmlTimbrado);

            $domDocument->save(PATH_ADM_FACTURAS . "/" . $comprobanteTimbrado->folioFiscal . ".xml");


            // Decrementa el numero de folios disponibles para el cliente de loomtek y actualiza el folio usado
            // $wsLoomtek->call('decrementaFoliosCliente', array('fl_cliente' => $fl_cliente_loomtek));
            //$wsLoomtek->call('incrementaFolioUtilizado', array('fl_cliente' => $fl_cliente_loomtek));
        } else {

            if ($timbrarEn == FACT_ELECT) {
                Dbg::pd('eRRORORROROROROR');
                $codigoError = $codigoError;
                $mensajeError = $mensajeError;
                $infoError = "";
            } elseif ($timbrarEn == FOL_DIG) {
                $codigoError = $arrRespuestaPAC[0];
                $mensajeError = $arrRespuestaPAC[1];
                $infoError = $arrRespuestaPAC[2];
            } elseif ($timbrarEn == SOL_FACT) {
                $codigoError = $ret->status;
                $mensajeError = $ret->mensaje;
                $infoError = "";
            }

            # Recupera el usuario de la sesion
            $fl_usuario = ValidaSesion();

            if (!empty($xmlErrors)) {
                Dbg::data($xmlErrors);
            }

            # Establece el titulo del mensaje y el icono dependiendo de la severidad
            switch ($fg_severidad) {
                case 'I':
                    $imgSeveridad = PATH_IMAGES . "/" . IMG_INFO;
                    $ds_titulo = ETQ_TIT_INFO . " - " . $ds_titulo;
                    break;
                case 'W':
                    $imgSeveridad = PATH_IMAGES . "/" . IMG_WARNING;
                    $ds_titulo = ETQ_TIT_WARN . " - " . $ds_titulo;
                    break;
                case 'P':
                    $imgSeveridad = PATH_IMAGES . "/" . IMG_HELP;
                    $ds_titulo = ETQ_TIT_CONFIRM . " - " . $ds_titulo;
                    break;
                default :
                    $imgSeveridad = PATH_IMAGES . "/" . IMG_ERROR;
                    $ds_titulo = ETQ_TIT_ERROR . " - " . $ds_titulo;
            }

            // En casos de que se generen errores y se requiera revisar el XML que se esta enviando, es este archivo
            $nombre_archivo = 13 . "_tmp.xml";
            $nombre_xml_tmp = PATH_ADM_FACTURAS . "/" . $nombre_archivo;
            $domDocument = new DOMDocument('1.0', 'utf-8');
            $domDocument->loadXML(utf8_encode(str_ascii($xmlCFDI)));
            $domDocument->save($nombre_xml_tmp);


            # Presenta pagina con el mensaje
            PresentaHeader();
            echo "
        <center>
          <br>
                <TABLE width='50%' border='" . D_BORDES . "' cellPadding='0' cellSpacing='0'>
                    <TR>
              <TD width='20%' align='right' valign='middle'><img src='$imgSeveridad' border='0'></TD>
              <TD width='5%'>&nbsp;</TD>
              <TD width='75%' align='left' valign='middle' class='css_default'>
                    <br><b>$ds_titulo</b><br>
                    <br>$ds_mensaje<br><br>";

            echo "$codigoError<br><br>";
            echo "$mensajeError<br><br>";
            echo "$infoError<br><br>";

            echo "<a href='{$nombre_xml_tmp}' target='blank'>$nombre_archivo</a><br><br>";

            echo "Cadena original[$cadena_original]<br><br>";

            /* echo "<div style='width:800px;'>";
              echo '<h2>Request</h2>';
              echo $client->request;
              echo '<h2>Response</h2>';
              echo $client->response;
              echo "</div>"; */
            echo "                <a href='javascript:history.back()'>" . ETQ_REGRESAR . "</a>
                    <br><br>	
              </TD>
                    </TR>
                </TABLE>
        </center>";
            PresentaFooter();

            exit;
        }






        Dbg::pd('Terminó proceso!!!!');
    }

    private function _getCertificado($certificados) {

//        $certificados['cer'] = PATH_ADM_PEM . '/' .'goya780416gm0_1210221537s.cer';
        if (file_exists($certificados['cer'])) {
            return $certificado = str_replace(array(chr(10), chr(1)), '', base64_encode(file_get_contents($certificados['cer'])));
        }
    }

    private function _getSello(Comprobante $Comprobante, $certificados, $metodo = 'Marco') {

//        $certificados['keyPEM'] = PATH_ADM_PEM . '/' .'goya780416gm0_1210221537s.key.pem';

        if ($metodo == 'Marco')
            if (file_exists($certificados['keyPEM'])) {
               
                $cadenaOriginal = $Comprobante->getCadenaOriginal();
                
                $fileKeyPEM = fopen($certificados['keyPEM'], "r");
//    $privateKey = fread($fileKeyPEM, 8192);
                $privateKey = fread($fileKeyPEM, 8192);
//    Dbg::data($privateKey);
                fclose($fileKeyPEM);
                $privateKeyId = openssl_get_privatekey($privateKey, '12345678a');
//    Dbg::data($privateKeyId);
//    Dbg::data($cadenaOriginal);
                openssl_sign($cadenaOriginal, $cadenaFirmada, $privateKeyId, OPENSSL_ALGO_SHA1);
//    Dbg::data($cadenaFirmada);
                return $sello = base64_encode($cadenaFirmada);
//    Dbg::data($sello);
            }
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
