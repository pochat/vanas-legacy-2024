<?php

class Facturacion {

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

    public function timbrar($data, Emisor $Emisor, Receptor $Receptor, $conceptos, $impuestos) {
        
        Dbg::data($data);
        Dbg::data($Emisor);
        Dbg::data($Receptor);
        Dbg::data($conceptos);
        Dbg::data($impuestos);
        Dbg::pd();
        
        //Obtener la información de la factura, en Edulight la informacion ya existe en la tabla k_factura
//        $Query = "SELECT no_factura, DATE_FORMAT(fe_factura, '%Y-%m-%dT%H:%i:%s') 'fe_factura', mn_subtotal, ";
//        $Query .= "mn_tipo_cambio, mn_total, cl_forma_pago, ds_rfc, ds_razon_social, ";
//        $Query .= "fl_pais_moneda, no_impuesto, mn_impuestos, mn_total, ds_total, tr_total, fl_dom_fiscal, fl_pais_cliente, fl_cotizacion_origen, ";
//        $Query .= "no_porcentaje, fg_total ";
//        $Query .= "FROM k_factura ";
//        $Query .= "WHERE fl_factura=$fl_factura";
//        $row = RecuperaValor($Query);
//        $no_factura = $row[0];
//        $fe_factura = $row[1];
//        $mn_subtotal = $row[2];
//        $mn_tipo_cambio = $row[3];
//        $mn_total = $row[4];
//        $cl_forma_pago = $row[5];
//        $ds_rfc = str_ascii($row[6]);
//        $ds_razon_social = str_ascii($row[7]);
//        $fl_pais_moneda = $row[8];
//        $no_impuesto = $row[9];
//        $mn_impuestos = $row[10];
//        $mn_total = $row[11];
//        $ds_total = str_ascii($row[12]);
//        $tr_total = str_ascii($row[13]);
//        $fl_dom_fiscal = $row[14];
//        $fl_pais_cliente = $row[15];
//        $fl_cotizacion_origen = $row[16];
//        $no_porcentaje = $row[17];
//        $fg_total = $row[18];
//        Dbg::pd($data);

        /**
         * Atributos fijos del comprobante
         */
        $versionSAT = "3.2";
        $serie = ""; // Edulight no usa series en los comprobantes
        $folio = ""; // MDB Para la version 3.2 ya no es requerido, es el folio que regresa el PAC

        // Forma de pago, basada en porcentajes de la tabla k_factura
        // Si k_factura.no_porcentaje = 100 -> Pago en una sola exhibición. 
        // Si k_factura.no_porcentaje <> 100 -> Es parcialidad
        //    Para identificar la primera usar el campo fg_total debe tener 0, Para la segunda fg_total = 1.
        $formaDePago = "Pago en una sola exhibici&oacute;n";
//        if ($no_porcentaje == 100) {
//            $formaDePago = "Pago en una sola exhibicion"; // EBL Guardar este valor en etiqueta de BD
//        } else {
//            if (!$fg_total) {// EBL Guardar este valor en etiqueta de BD
//                $formaDePago = "Parcialidad 1 de 2";
//            } else {// EBL Guardar este valor en etiqueta de BD
//                $formaDePago = "Parcialidad 2 de 2";
//            }
//        }

        // Condiciones de pago
        $condicionesDePago = "";
//        if ($no_porcentaje <> 100) {
//            if (!$fg_total) {
//                $condicionesDePago = ($no_porcentaje * 1) . "% " . ObtenEtiqueta(611) . " " . (100 - $no_porcentaje) . "% " . ObtenEtiqueta(612);
//            } else {
//                $condicionesDePago = ($no_anticipo * 1) . "% " . ObtenEtiqueta(611) . " " . (100 - $no_anticipo) . "% " . ObtenEtiqueta(612);
//            }
//            $condicionesDePago = utf8_encode(str_ascii($condicionesDePago));
//        } else {
//            $condicionesDePago = utf8_encode(str_ascii(ObtenEtiqueta(613)));
//        }

        // TODO Uso una fecha actual para que me permita generar la factura
        //$fecha = $fe_factura; // Con este formato 2011-05-17T11:10:58, ya lo tiene desde la BD
//        $fecha = "2015-10-14T23:50:58";
        $fecha = date("Y-m-dTH:i:s");
        $lugarExpedicion = ObtenConfiguracion(724);
        $estadoExpedicion = ObtenConfiguracion(723);
        if (!empty($estadoExpedicion))
            $lugarExpedicion .= ", " . $estadoExpedicion;


        $regimenFiscal = ObtenConfiguracion(713); // Regimen Edulight

        switch ($cl_forma_pago) {
            case '1': $metodoDePago = ObtenEtiqueta(591);
                break;
            case '2': $metodoDePago = ObtenEtiqueta(592);
                break;
            case '3': $metodoDePago = ObtenEtiqueta(593);
                break;
            case '4': $metodoDePago = ObtenEtiqueta(594);
                break;
        }

        /* $noCertificado = $atributosComprobante->noCertificado;
          $certificado = $atributosComprobante->certificado; */
        //$noCertificado = "12345678901234567890"; // MDB EBL Revisar cual e el certificasdo de edulight.
        $noCertificado = "20001000000200000216";
        //$certificado = "MIIEdDCCA1ygAwIBAgIUMjAwMDEwMDAwMDAxMDAwMDU4NjcwDQYJKoZIhvcNAQEFBQAwggFvMRgwFgYDVQQDDA9BLkMuIGRlIHBydWViYXMxLzAtBgNVBAoMJlNlcnZpY2lvIGRlIEFkbWluaXN0cmFjacOzbiBUcmlidXRhcmlhMTgwNgYDVQQLDC9BZG1pbmlzdHJhY2nDs24gZGUgU2VndXJpZGFkIGRlIGxhIEluZm9ybWFjacOzbjEpMCcGCSqGSIb3DQEJARYaYXNpc25ldEBwcnVlYmFzLnNhdC5nb2IubXgxJjAkBgNVBAkMHUF2LiBIaWRhbGdvIDc3LCBDb2wuIEd1ZXJyZXJvMQ4wDAYDVQQRDAUwNjMwMDELMAkGA1UEBhMCTVgxGTAXBgNVBAgMEERpc3RyaXRvIEZlZGVyYWwxEjAQBgNVBAcMCUNveW9hY8OhbjEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTIwMAYJKoZIhvcNAQkCDCNSZXNwb25zYWJsZTogSMOpY3RvciBPcm5lbGFzIEFyY2lnYTAeFw0xMjA3MjcxNzAyMDBaFw0xNjA3MjcxNzAyMDBaMIHbMSkwJwYDVQQDEyBBQ0NFTSBTRVJWSUNJT1MgRU1QUkVTQVJJQUxFUyBTQzEpMCcGA1UEKRMgQUNDRU0gU0VSVklDSU9TIEVNUFJFU0FSSUFMRVMgU0MxKTAnBgNVBAoTIEFDQ0VNIFNFUlZJQ0lPUyBFTVBSRVNBUklBTEVTIFNDMSUwIwYDVQQtExxBQUEwMTAxMDFBQUEgLyBIRUdUNzYxMDAzNFMyMR4wHAYDVQQFExUgLyBIRUdUNzYxMDAzTURGUk5OMDkxETAPBgNVBAsTCFVuaWRhZCAxMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC2TTQSPONBOVxpXv9wLYo8jezBrb34i/tLx8jGdtyy27BcesOav2c1NS/Gdv10u9SkWtwdy34uRAVe7H0a3VMRLHAkvp2qMCHaZc4T8k47Jtb9wrOEh/XFS8LgT4y5OQYo6civfXXdlvxWU/gdM/e6I2lg6FGorP8H4GPAJ/qCNwIDAQABox0wGzAMBgNVHRMBAf8EAjAAMAsGA1UdDwQEAwIGwDANBgkqhkiG9w0BAQUFAAOCAQEATxMecTpMbdhSHo6KVUg4QVF4Op2IBhiMaOrtrXBdJgzGotUFcJgdBCMjtTZXSlq1S4DG1jr8p4NzQlzxsdTxaB8nSKJ4KEMgIT7E62xRUj15jI49qFz7f2uMttZLNThipunsN/NF1XtvESMTDwQFvas/Ugig6qwEfSZc0MDxMpKLEkEePmQwtZD+zXFSMVa6hmOu4M+FzGiRXbj4YJXn9Myjd8xbL/c+9UIcrYoZskxDvMxc6/6M3rNNDY3OFhBK+V/sPMzWWGt8S1yjmtPfXgFs1t65AZ2hcTwTAuHrKwDatJ1ZPfa482ZBROAAX1waz7WwXp0gso7sDCm2/yUVww==";
        $certificado = "";

        $subTotal = $mn_subtotal;
//    $fl_moneda = $fl_pais_moneda;
        $row = RecuperaValor("SELECT ds_moneda, tr_moneda FROM c_pais WHERE fl_pais=" . $fl_pais_moneda);
        $ds_moneda = str_ascii($row[0]);

        $total = $mn_total;
        $tipoDeComprobante = ObtenConfiguracion(714);

        // Datos del Emisor
        $rfcEmisor = CorrigeRFC(str_ascii(ObtenConfiguracion(9)));
        $rfcEmisor = "GOYA780416GM0";
        $nombreEmisor = str_ascii(ObtenConfiguracion(8));
        // Expedido En
        // No lo usamos en edulight
        $estadoExpedicion = "";
        $paisExpedicion = "";
        // Domicilio emisor
        // Datos fijos para Edulight
        $calleEmisor = str_ascii(ObtenConfiguracion(10));
        $noExterior = "";
        $noInterior = "";
        $colonia = "";
        $municipio = ObtenConfiguracion(719);
        $codigoPostal = ObtenConfiguracion(720);
        $estado = ObtenConfiguracion(721);
        $pais = ObtenConfiguracion(722);

        $domicilioEmisor = new Domicilio($calleEmisor, $noExterior, $noInterior, $colonia, $municipio, $estado, $pais, $codigoPostal);

        $Emisor = new Emisor($rfcEmisor, $nombreEmisor, $domicilioEmisor, $estadoExpedicion, $paisExpedicion);
        // TODO Revisar si debe ser un arreglo
        $Emisor->setRegimenFiscal($regimenFiscal);

        // Datos del Receptor
        $rfcReceptor = CorrigeRFC($ds_rfc);
        $nombreReceptor = $ds_razon_social;

        $Query = "SELECT ds_calle_num, ds_colonia, ds_cp, ds_ciudad, ds_estado, fl_pais FROM c_domicilio WHERE fl_domicilio=$fl_dom_fiscal";
        $row = RecuperaValor($Query);
        $calleNumReceptor = str_ascii($row[0]);
        $coloniaReceptor = str_ascii($row[1]);
        $cpReceptor = $row[2];
        $ciudadReceptor = str_ascii($row[3]);
        $estadoReceptor = str_ascii($row[4]);
        $flPaisReceptor = $row[5];
        $Query = "SELECT nb_pais, tr_pais FROM c_pais WHERE fl_pais=$flPaisReceptor";
        $row = RecuperaValor($Query);
        $paisReceptor = str_ascii($row[1]);

        // Lugar de expedicion
        $estadoExpedicion = str_ascii(ObtenConfiguracion(723));
        $paisExpedicion = str_ascii(ObtenConfiguracion(724));

        $domicilioReceptor = new Domicilio($calleNumReceptor, $noExterior, $noInterior, $coloniaReceptor, $ciudadReceptor, $estadoReceptor, $paisReceptor, $cpReceptor);

        $Receptor = new Emisor($rfcReceptor, $nombreReceptor, $domicilioReceptor, $estadoExpedicion, $paisExpedicion);

        $conceptosArr = array();

        /*         * ************************************************** */
        // Detalle de la factura
        /*         * ************************************************** */

        $fl_pais_MXN = 1;
        $row = RecuperaValor("SELECT mn_tipo_cambio FROM c_pais WHERE fl_pais=$fl_pais_MXN");
        $mn_tipo_cambio_MXN = $row[0];


        # Recibe parametros generales
        $fl_cotizacion = $fl_cotizacion_origen;
        //$fl_pais_moneda = 1; // TODO
        # Recupera el tipo de cambio segun la Moneda seleccionada y convierte los montos
        if (!empty($fl_pais_moneda)) {
            $row = RecuperaValor("SELECT mn_tipo_cambio FROM c_pais WHERE fl_pais=$fl_pais_moneda");
            $mn_tipo_cambio = $row[0];
        }
        $ds_mn_tipo_cambio = "* $mn_tipo_cambio ";

        # Recupera bloques por editorial, linea de producto
        $Query = "SELECT a.fl_linea_producto ";
        $Query .= "FROM k_cot_bloque a, c_linea_producto b, c_editorial c ";
        $Query .= "WHERE a.fl_linea_producto=b.fl_linea_producto ";
        $Query .= "AND b.fl_editorial=c.fl_editorial ";
        $Query .= "AND a.fl_cotizacion=$fl_cotizacion ";
        $Query .= "ORDER BY c.cl_editorial, b.cl_linea_producto";
        $rs = EjecutaQuery($Query);
        for ($tot_bloques = 0; $row = RecuperaRegistro($rs); $tot_bloques++) {
            $fl_linea_producto = $row[0];

            # Recupera productos del bloque
            $Query = "SELECT a.fl_producto, a.no_cantidad-a.no_cant_bundle, a.no_cant_bundle, a.mn_precio $ds_mn_tipo_cambio, ";
            $Query .= "a.mn_importe $ds_mn_tipo_cambio, ";
            $Query .= "b.cl_producto, b.nb_producto, c.cl_grado ";
            $Query .= "FROM (k_cot_detalle a, c_producto b) LEFT JOIN c_grado c ";
            $Query .= "ON (b.fl_grado=c.fl_grado) ";
            $Query .= "WHERE a.fl_producto=b.fl_producto ";
            $Query .= "AND a.fl_cotizacion=$fl_cotizacion ";
            $Query .= "AND a.fl_linea_producto=$fl_linea_producto ";
            $Query .= "ORDER BY c.no_orden, b.nb_producto";

            $rs2 = EjecutaQuery($Query);
            while ($row2 = RecuperaRegistro($rs2)) {
                $fl_producto_d = $row2[0];
                $no_cantidad_d = $row2[1];
                $no_cant_bundle_d = $row2[2];
                $mn_precio_d = $row2[3];
                $mn_importe = $row2[4];
                $cl_producto = str_ascii($row2[5]);
                $nb_producto = str_ascii($row2[6]);
                $cl_grado = str_ascii($row2[7]);

                if ($no_cant_bundle_d > 0) {
                    $no_cantidad_d = $no_cant_bundle_d;
                    $mn_precio_d = 0;
                    $mn_importe = 0;
                }


                $unidad = ObtenConfiguracion(725); // En Edulight siempre usaremos este concepto
                $noIdentificacion = ""; // Edulight no lo usa

                $concepto = new Concepto();
                $concepto->setCantidad($no_cantidad_d);
                $concepto->setUnidad($unidad);
                $concepto->setNoIdentificacion($noIdentificacion);
                $concepto->setDescripcion(CorrigeDescripcion($nb_producto));
                $concepto->setValorUnitario(number_format($mn_precio_d, 2, '.', ''));
                $concepto->setImporte(number_format($mn_importe, 2, '.', ''));
                array_push($conceptosArr, $concepto);
            }
        }

        # Recupera totales de la cotizacion
        $Query = "SELECT mn_descuento $ds_mn_tipo_cambio, no_impuesto, mn_impuestos $ds_mn_tipo_cambio, mn_tot_cotizacion $ds_mn_tipo_cambio ";
        $Query .= "FROM k_cotizacion ";
        $Query .= "WHERE fl_cotizacion=$fl_cotizacion";
        $row = RecuperaValor($Query);
        $mn_descuento_cot = $row[0];
        $no_impuesto_cot = $row[1];
        $mn_impuestos_cot = $row[2];
        $mn_tot_cotizacion_cot = $row[3];

        $TrasladosArr = array();

        $Impuesto = new Impuesto();
        $Impuesto->setTipoRegistro("TRASLADO");
        $Impuesto->setTipoImpuesto("IVA"); //(string) ObtenEtiqueta(506));
        $Impuesto->setTasa((string) ($no_impuesto_cot * 100));
        $Impuesto->setImporte((string) number_format($mn_impuestos_cot, 2, '.', ''));
        array_push($TrasladosArr, $Impuesto);

        $totalImpuestosTrasladados = $mn_tot_cotizacion_cot;

        /*         * ************************************************** */

        // No lo usamos para edulight
        $RetencionesArr = array();
        $totalImpuestosRetenidos = "";

        $ImpuestosComprobante = new ImpuestosComprobante($totalImpuestosTrasladados, $totalImpuestosRetenidos, $ImpuestosArr);

        $ConceptosArr = array();
        $ConceptosArr = $conceptosArr;
        $ImpuestosComprobanteArr = array();

        array_push($ImpuestosComprobanteArr, $RetencionesArr, $TrasladosArr);


        $ImpuestosComprobante->setImpuestos($ImpuestosComprobanteArr);

        $Comprobante = new Comprobante($versionSAT, $serie, $folio, $fecha, $sello, $formaDePago, $noCertificado, $certificado, number_format($subTotal, 2, '.', ''), $ds_moneda, number_format($total, 2, '.', ''), $metodoDePago, $tipoDeComprobante, $Emisor, $Receptor, $ConceptosArr, $ImpuestosComprobante, $mn_tipo_cambio_MXN, $lugarExpedicion, $noAprobacion, $anioAprobacion);
        $Comprobante->setCondicionesPago($condicionesDePago);

        $Comprobante->preserveWhiteSpace = false;
        $Comprobante->formatOutput = false;


        /**
         * ******************************************************      
          VERSION ANTERIOR GENERACION CADENA ORIGINAL Y FIRMA
         * ******************************************************
         */
        $cadena_original = $Comprobante->getCadenaOriginal();
        $cadena_original = utf8_encode($cadena_original);

        $key = 'archivosPEM/goya780416gm0_1210221537s.key.pem';
        $fp = fopen($key, "r");
        $priv_key = fread($fp, 8192);
        fclose($fp);
        $pkeyid = openssl_get_privatekey($priv_key);
        openssl_sign($cadena_original, $cadenafirmada, $pkeyid, OPENSSL_ALGO_SHA1);
        $sello = base64_encode($cadenafirmada);


        $file = "archivosPEM/goya780416gm0_1210221537s.cer.pem";
        $datos = file($file);
        $certificado = "";
        $carga = false;
        for ($i = 0; $i < sizeof($datos); $i++) {
            if (strstr($datos[$i], "END CERTIFICATE"))
                $carga = false;
            if ($carga)
                $certificado .= trim($datos[$i]);
            if (strstr($datos[$i], "BEGIN CERTIFICATE"))
                $carga = true;
        }
        // El certificado como base64 lo agrega al XML para simplificar la validacion
        $Comprobante->setCertificado($certificado);

        $Comprobante->setSello($sello);
        $xml_enviado = $Comprobante->getXML();


        /*         * ***********************************
          Nueva version PENDIENTE
         * Referencia: http://www.lacorona.com.mx/fortiz/sat/xml.php
         * *********************************** */
        /*
          $xml_enviado = new DOMDocument("1.0","UTF-8");
          $xml_enviado->loadXML(utf8_encode( ( $Comprobante->getXML() ) ));

          $archivoXSLTCadenaOriSAT = "archivosSAT/cadenaoriginal_3_2.xslt";
          $xsl = new DOMDocument("1.0","UTF-8");
          $xsl->load($archivoXSLTCadenaOriSAT);

          $proc = new XSLTProcessor;
          $proc->importStyleSheet($xsl);
          $cadena_original = $proc->transformToXML($xml_enviado);

          $key='archivosPEM/aaa010101aaa__csd_01.key.pem';
          $fp = fopen($key, "r");
          $priv_key = fread($fp, 8192);
          fclose($fp);
          $pkeyid = openssl_get_privatekey($priv_key);
          openssl_sign($cadena_original, $cadenafirmada, $pkeyid, OPENSSL_ALGO_SHA1);
          openssl_free_key($pkeyid);
          $sello = base64_encode($cadenafirmada);

          $Comprobante->setSello($sello);

          $file = "archivosPEM/aaa010101aaa__csd_01.cer.pem";
          $datos = file($file);
          $certificado = "";
          $carga=false;
          for ($i=0; $i<sizeof($datos); $i++) {
          if (strstr($datos[$i],"END CERTIFICATE")) $carga=false;
          if ($carga) $certificado .= trim($datos[$i]);
          if (strstr($datos[$i],"BEGIN CERTIFICATE")) $carga=true;
          }
          // El certificado como base64 lo agrega al XML para simplificar la validacion
          $Comprobante->setCertificado($certificado);


          $xml_enviado = $Comprobante->getXML();


         */

        // En casos de que se generen errores y se requiera revisar el XML que se esta enviando, es este archivo
        /* $nombre_archivo =  $fl_factura . "_tmp.xml";
          $nombre_xml_tmp = "./facturas/" . $nombre_archivo;
          $dom_respuesta = new DOMDocument('1.0', 'utf-8');
          $dom_respuesta->loadXML(utf8_encode(str_ascii($xml_enviado)));
          $dom_respuesta->save($nombre_xml_tmp);

          exit; */

        //$Comprobante->imprimeInfo();
        // Se almacena en la base de datos el xml de ida
        /* $Query  = "INSERT INTO k_factura_electronica (fl_factura, ds_xml_envio, ds_cadena_original, ds_sello_emisor) ";
          $Query .= "VALUES($fl_factura, '" . str_html_bd($xml_enviado) . "', '" . utf8_decode($cadena_original) . "', '" . $sello . "') ";
          $fl_factura = EjecutaInsert($Query); */

        // A la respuesta XMl de folios digitales utf8_decode y luego 
        // str_html_bd para guardarlo en BD
        //    Al leer esto de la BD y mostrarlo en el browser usar str_uso_normal, si se despliega en una forma para editar, usar str_texto
        // str_texto para desplagar en la pantalla

        /*
          // TODO Falta el id del cliente
          $fl_cliente_loomtek = 1;

          // Conectandose a los web services de Loomtek para obtener los folios del cliente y el usuario y password para conectarse
          // DESARROLLO $urlWSLoomtek = "http://localhost/loomtek_ws/serverWS.php?WSDL";
          $urlWSLoomtek = "http://www.loomtek.com.mx/loomtek_admon/webservices/serverWS.php?WSDL";
          $wsLoomtek = new nusoap_client($urlWSLoomtek, false);
          $foliosDisponibles = $wsLoomtek->call('getFoliosCliente', array('fl_cliente' => $fl_cliente_loomtek));
          $ultimoUtilizado = $wsLoomtek->call('getUltimoUtilizado', array('fl_cliente' => $fl_cliente_loomtek));

          if (empty($foliosDisponibles)) {
          echo "No existen folios disponibles para el cliente";
          exit;
          }
         */
        // Se crea la referencia con el id del cliente y el folio con el formato xxxxxxyyyyyyyy, las x para el id cliente, las y para los folios
        $referenciaCliente = str_pad($fl_cliente_loomtek, 6, "0", STR_PAD_LEFT) . str_pad($ultimoUtilizado + 1, 8, "0", STR_PAD_LEFT);

        // Validaciones y correciones de valores
        $arr_errores_xml = array();
        $arr_errores_xml = $Comprobante->ValidaComprobante();


        define(SOL_FACT, "SolucionFactible");
        define(FOL_DIG, "FoliosDigitales");
        define(FACT_ELECT, "FacturadorElectronico");

        $timbrarEn = FACT_ELECT;

        if ($timbrarEn == FOL_DIG) {
            $usrPAC = ObtenConfiguracion(701);
            $pwdPAC = ObtenConfiguracion(702);
            $urlWS = ObtenConfiguracion(700);
        }

        if ($timbrarEn == SOL_FACT) {
            $usrPAC = "testing@solucionfactible.com";
            $pwdPAC = "timbrado.SF.16672";
            $urlWS = "https://testing.solucionfactible.com/ws/services/Timbrado?wsdl";
        }

        if ($timbrarEn == FACT_ELECT) {
            $usrPAC = "test";
            $pwdPAC = "TEST";
            $urlWS = "https://stagecfdi.facturadorelectronico.com/wstimbrado/Timbrado.asmx?WSDL";
            //Pruebas anterior "https://pruebas.facturadorelectronico.com/clientes/wstimbrado/Timbrado.asmx?WSDL";
            //$urlWS = "https://cfdi.facturadorelectronico.com/wstimbrado/timbrado.asmx?WSDL"; // Prod
        }

        $arrRespuestaPAC = array();

        if (empty($arr_errores_xml)) {

            /*             * **************************************************************************
              // Conectandose a los servicios del PAC
             * *************************************************************************** */

            if ($timbrarEn == FOL_DIG) {

                // Crea el cliente para conectar al web service de folios digitales
                $client = new nusoap_client($urlWS, true);

                // Comentado porque ya me acabe los timbres 
                $result = $client->call('TimbrarPruebaCFDI', array('usuario' => $usrPAC, 'password' => $pwdPAC, 'cadenaXML' => $xml_enviado, 'Referencia' => $referenciaCliente));

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
            }

            if ($timbrarEn == FACT_ELECT) {

                // Crea el cliente para conectar al web service de folios digitales
                $client = new nusoap_client($urlWS, true);

                // Comentado porque ya me acabe los timbres 
                $result = $client->call('obtenerTimbrado', array('Usuario' => $usrPAC, 'password' => $pwdPAC, 'CFDIcliente' => (($xml_enviado))));

                $xml = new SimpleXMLElement('<obtenerTimbrado/>');
                $this->toXML($xml, $result);

                // Para saber que elementos tiene el XML que regresa el PAC
                // usar la siguiente instruccion sobre los elementos puede ayudar var_dump($timbre);      
                $timbre = $xml->obtenerTimbradoResult->timbre;
                $errores = $xml->obtenerTimbradoResult->timbre->errores;
                $primerError = $errores[0]->Error[0]; // Se puede implementar la lectura del arreglo.

                $esValido = $timbre->{"!esValido"}; // True o False pero como string
                if (strtoupper($esValido) == 'TRUE') {
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
                } else {
                    $codigoError = $primerError->{"!codigo"};
                    $mensajeError = $primerError->{"!mensaje"};
                }
            }  // Fin FACT_ELECT


            if ($timbrarEn == SOL_FACT) {
                try {
                    $client = new SoapClient($urlWS);
                    $params = array('usuario' => $usrPAC, 'password' => $pwdPAC, 'cfdiBase64' => base64_encode(utf8_encode(str_ascii($xml_enviado))), 'zip' => False);
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
              $dom_respuesta = new DOMDocument('1.0', 'utf-8');
              $dom_respuesta->loadXML(utf8_encode(str_ascii($row[0])));

              $dom_respuesta->save("./facturas/" . $comprobanteTimbrado->folioFiscal . ".xml");
              } */
        } // Termina IF $arr_errores_xml
        // Validar el arreglo para saber si es respuesta correcta
        // El arreeglo $arr_errores_xml es el que usamos nosotros para acumular errores, por ejemplo si el RFC esta en blanco
        if ((empty($arrRespuestaPAC[0]) && empty($arrRespuestaPAC[1]) && empty($arrRespuestaPAC[2]) && empty($arr_errores_xml) && $timbrarEn == FOL_DIG ) || ( empty($arr_errores_xml) && $timbrarEn == SOL_FACT && ($ret->status == 200) ) || ( empty($arr_errores_xml) && $timbrarEn == FACT_ELECT && strtoupper($esValido) == 'TRUE' )
        ) {

            if ($timbrarEn == FOL_DIG) {
                $xmlTimbrado = $arrRespuestaPAC[3];
                $acuseRecibo = $arrRespuestaPAC[4];
            }
            if ($timbrarEn == SOL_FACT) {
                $resultados = $ret->resultados;
                $xmlTimbrado = (($resultados->cfdiTimbrado));
                $acuseRecibo = $resultados->uuid;
            }
            if ($timbrarEn == FACT_ELECT) {
                $resultados = $ret->resultados;
                $xmlTimbrado = $ComprobanteTimbrado->getXML();
                $acuseRecibo = $resultados->uuid;
            }

            // Creacion del objeto que contiene los datos de la factura
            $comprobanteTimbrado = new DatosComprobante();
            $comprobanteTimbrado->setXmlComprobante((utf8_encode(str_ascii($xmlTimbrado))));



            $Query = "INSERT INTO k_factura_electronica ";
            $Query .= "(fl_factura, ds_xml_envio, ds_xml_respuesta, ds_respuesta_completa, no_codigo_error, ds_error, ";
            $Query .= "ds_info_error, ds_cadena_original, ds_sello_emisor, cl_uuid_certificacion, fe_certificacion, no_certificado_sat, ";
            $Query .= "ds_sello_sat, fg_estatus, cl_codigo_cancelacion, ds_mensaje, ds_xml_cancelacion, fe_cancelacion) ";
            $Query .= "VALUES ($fl_factura, '" . str_html_bd(utf8_decode($xml_enviado)) . "', '" . str_html_bd(utf8_decode($xmlTimbrado)) . "', '" . str_html_bd(utf8_decode($acuseRecibo)) . "', ";
            $Query .= "NULL, NULL, NULL, '" . str_html_bd(utf8_decode($cadena_original)) . "', '" . $sello . "', '" . $comprobanteTimbrado->folioFiscal . "', '" . $comprobanteTimbrado->fechaTimbrado . "', ";
            $Query .= "'" . $comprobanteTimbrado->certificadoSAT . "', '" . $comprobanteTimbrado->selloSAT . "', 'V', NULL, NULL, NULL, NULL)";

            $fl_factura = EjecutaInsert($Query);


            // Actualiza el folio fiscal en el campo de numero de factura
            // MDB 24/NOV/2014
            // El numero de factura es un consecutivo, ya no se va a guardar el folio fiscal en este campo
            // Obtiene el folio actual, lo asigna a la factura y lo incrementa para ser usado por la siguiente factura.
            $folio_actual = ObtenConfiguracion(728) + 1;

            $Query = "UPDATE k_factura SET ";
            $Query .= "no_factura = '" . $folio_actual . "' ";
            $Query .= "WHERE fl_factura = $fl_factura ";
            //$Query .= "AND (no_factura IS NULL or no_factura = '') "; // TODO Solo para desarrollo 

            EjecutaQuery($Query);

            $Query = "UPDATE c_configuracion SET ";
            $Query .= "ds_valor = '" . $folio_actual . "' ";
            $Query .= "WHERE cl_configuracion = 728 ";

            EjecutaQuery($Query);

            // Genera permanentemente el archivo XML de la factura
            $Query = "SELECT ds_xml_respuesta FROM k_factura_electronica WHERE fl_factura=$fl_factura";
            $row = RecuperaValor($Query);

            if (!empty($row[0])) {
                $dom_respuesta = new DOMDocument('1.0', 'utf-8');
                $dom_respuesta->loadXML(utf8_encode(str_ascii($row[0])));

                $dom_respuesta->save("./facturas/" . $comprobanteTimbrado->folioFiscal . ".xml");
            }

            // Decrementa el numero de folios disponibles para el cliente de loomtek y actualiza el folio usado
            // $wsLoomtek->call('decrementaFoliosCliente', array('fl_cliente' => $fl_cliente_loomtek));
            //$wsLoomtek->call('incrementaFolioUtilizado', array('fl_cliente' => $fl_cliente_loomtek));
        } else {

            if ($timbrarEn == FOL_DIG) {
                $codigoError = $arrRespuestaPAC[0];
                $mensajeError = $arrRespuestaPAC[1];
                $infoError = $arrRespuestaPAC[2];
            }

            if ($timbrarEn == SOL_FACT) {
                $codigoError = $ret->status;
                $mensajeError = $ret->mensaje;
                $infoError = "";
            }
            if ($timbrarEn == FACT_ELECT) {
                $codigoError = $codigoError;
                $mensajeError = $mensajeError;
                $infoError = "";
            }

            # Recupera el usuario de la sesion
            $fl_usuario = ValidaSesion();

            if (!empty($arr_errores_xml)) {

                $cl_mensaje = $arr_errores_xml[0];

                # Obtiene el mensaje
                $Query = "SELECT ds_titulo, tr_titulo, ds_mensaje, tr_mensaje, fg_severidad, fg_tipo ";
                $Query .= "FROM c_mensaje ";
                $Query .= "WHERE cl_mensaje = $cl_mensaje";
                $row = RecuperaValor($Query);
                if ($row) {
                    $ds_titulo = EscogeIdioma($row[0], $row[1]);
                    $ds_mensaje = EscogeIdioma($row[2], $row[3]);
                    $fg_severidad = $row[4];
                    $fg_tipo = $row[5];
                } else {
                    $ds_titulo = "Internal Error";
                    $ds_mensaje = "Undefined error code.";
                    $fg_tipo = 1;
                }
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
            $nombre_archivo = $fl_factura . "_tmp.xml";
            $nombre_xml_tmp = "./facturas/" . $nombre_archivo;
            $dom_respuesta = new DOMDocument('1.0', 'utf-8');
            $dom_respuesta->loadXML(utf8_encode(str_ascii($xml_enviado)));
            $dom_respuesta->save($nombre_xml_tmp);


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
    }

    function cancelarComprobante($fl_factura) {
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
    
    public static function formatoImporte($valor, $decimales = 2)
    {
        if($valor != '')
        {
            $valor = str_replace('.00','', number_format($valor, $decimales,'.',''));
        }
        
        return $valor;
    }

}
