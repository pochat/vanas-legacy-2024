<?php

require_once PATH_ADM_CLASS . '/nusoap.php';

class PacFacturadorElectronico {

    private $usrPAC = 'test';
    private $pwdPAC = 'TEST';
    private $urlWS = "https://stagecfdi.facturadorelectronico.com/wstimbrado/Timbrado.asmx?WSDL";

    //private $urlWS = "https://cfdi.facturadorelectronico.com/wstimbrado/timbrado.asmx?WSDL";

    function __construct() {
        
    }

    public function timbrar(Comprobante $Comprobante) {
        /**
         * EGMC 20151113
         * Obtenemos el xml a enviar
         */
        $xmlCFDI = $Comprobante->getXML();
        $dataResponse['xmlCFDI'] = $xmlCFDI;
//        echo htmlentities($xmlCFDI);

        /* Posibles valores del arreglo que regresa el PAC
         *
         *  RESPUESTA CORRECTA
         *    Respuesta(0) = ""
         *    Respuesta(1) = ""
         *    Respuesta(2) = ""
         *    Resultado(3) = En esta posición se env�a el contenido del XML timbrado con el complemento Timbre Fiscal Digital, que le permitirá actualizar el comprobante enviado.
         *    Resultado(4) = En esta posición se env�a el contenido del XML del acuse de envío del CFDI respondido por el SAT, se debe guardar el contenido, ya que es el comprobante de la autenticidad del CFDI.
         *
         *  ERROR
         *    Respuesta(0) = Aqu� se env�a el c�digo de error, que puede ser referente al XML enviado o a la forma de accesar el servicio. Ej. 301, 302.
         *    Respuesta(1) = Mensaje del error producido.
         *    Respuesta(2) = Mensaje opcional con informaci�n que complementa el error para su mejor comprensión.
         *    Resultado(3) = ""
         *    Resultado(4) = ""
         *
         *    *** Se recomienda validar las posiciones que van del (0) al (2), si estas NO regresan vacías se produjo un error.
         */

        // Crea el cliente para conectar al web service de folios digitales
        $client = new nusoap_client($this->urlWS, true);
        $response = $client->call('obtenerTimbrado', array('Usuario' => $this->usrPAC, 'password' => $this->pwdPAC, 'CFDIcliente' => $xmlCFDI));
//        Dbg::pd($response);
//        Dbg::data($response['obtenerTimbradoResult']['timbre']['TimbreFiscalDigital']['!version']);
        $timbre = $response['obtenerTimbradoResult']['timbre'];
//        Dbg::data($timbre["errores"]);
//        Dbg::data($timbre["!esValido"]);
//        Dbg::pd($timbre);
        /**
          $xml = new SimpleXMLElement('<obtenerTimbrado/>');
          $Facturacion = new Facturacion();
          $Facturacion->toXML($xml, $response);
          Dbg::data($xml);
          // Para saber que elementos tiene el XML que regresa el PAC
          // usar la siguiente instruccion sobre los elementos puede ayudar var_dump($timbre);
          $timbre = $xml->obtenerTimbradoResult->timbre;
          //        $esValido = $timbre->{"!esValido"}; // True o False pero como string
         */
        if (isset($timbre["!esValido"]) && strtoupper($timbre["!esValido"]) == 'TRUE') {

//            $schemaLocation = $timbre->TimbreFiscalDigital->{"schemaLocation"};
//            $schemaLocation = explode(" ", $schemaLocation);
//            $tfdNameSpace = $schemaLocation[0];
//            $version = (string) $timbre->TimbreFiscalDigital->{"!version"};
////            Dbg::data($version);
//            $fechaTimbrado = (string) $timbre->TimbreFiscalDigital->{"!FechaTimbrado"};
//            $selloCFD = (string) $timbre->TimbreFiscalDigital->{"!selloCFD"};
//            $noCertificadoSAT = (string) $timbre->TimbreFiscalDigital->{"!noCertificadoSAT"};
//            $selloSAT = (string) $timbre->TimbreFiscalDigital->{"!selloSAT"};
//            $UUID = (string) $timbre->TimbreFiscalDigital->{"!UUID"};
//            
            $schemaLocation = $timbre['TimbreFiscalDigital']["!xsi:schemaLocation"];
            $version = $timbre['TimbreFiscalDigital']["!version"];
            $fechaTimbrado = $timbre['TimbreFiscalDigital']["!FechaTimbrado"];
            $selloCFD = $timbre['TimbreFiscalDigital']["!selloCFD"];
            $noCertificadoSAT = $timbre['TimbreFiscalDigital']["!noCertificadoSAT"];
            $selloSAT = $timbre['TimbreFiscalDigital']["!selloSAT"];
            $UUID = $timbre['TimbreFiscalDigital']["!UUID"];
            // Agrega los valores del timbrado para crear el XML completo
            $ComprobanteTimbrado = $Comprobante;

            $ComprobanteTimbrado->setTfdSchemaLocation($schemaLocation);
            $schemaLocation = explode(" ", $schemaLocation);
            $tfdNameSpace = $schemaLocation[0];
            $ComprobanteTimbrado->setTfdNameSpace($tfdNameSpace);
            $ComprobanteTimbrado->setTfdVersion($version);
            $ComprobanteTimbrado->setTfdFechaTimbrado($fechaTimbrado);
            $ComprobanteTimbrado->setTfdSelloCFD($selloCFD);
            $ComprobanteTimbrado->setTfdNoCertificadoSAT($noCertificadoSAT);
            $ComprobanteTimbrado->setTfdSelloSAT($selloSAT);
            $ComprobanteTimbrado->setTfdUUID($UUID);
            $ComprobanteTimbrado->setAgregarXMLTimbrado(True);

            $xmlCFDITimbrado = $ComprobanteTimbrado->getXML();

            $dataResponse += compact('timbre', 'TimbreFiscalDigital', 'ComprobanteTimbrado', 'xmlCFDITimbrado');
            $dataResponse['TimbreFiscalDigital'] = compact('schemaLocation', 'tfdNameSpace', 'version', 'fechaTimbrado', 'selloCFD ', 'noCertificadoSAT', 'selloSAT', 'UUID');
        } else {
            /**
             * EGMC 20151112
             * Agregar errores al log y crear una notificación
             */
//            Dbg::data($timbre);
            $dataResponse['error']['codigo_error'] = isset($timbre['errores']['Error'][1]['!codigo']) ? $timbre['errores']['Error'][1]['!codigo'] : '';
            $dataResponse['error']['error'] = isset($timbre['errores']['Error'][1]['!mensaje']) ? $timbre['errores']['Error'][1]['!mensaje'] : '';
            $dataResponse['error']['info_error'] = isset($timbre['errores']['Error'][0]['!mensaje']) ? $timbre['errores']['Error'][0]['!mensaje'] : '';

            if ($dataResponse['error']['codigo_error'] == '') {
                $dataResponse['error']['codigo_error'] = isset($timbre['errores']['Error']['!codigo']) ? $timbre['errores']['Error']['!codigo'] : '';
            }
            if ($dataResponse['error']['error'] == '') {
                $dataResponse['error']['error'] = isset($timbre['errores']['Error']['!mensaje']) ? $timbre['errores']['Error']['!mensaje'] : '';
                $dataResponse['error']['info_error'] = isset($timbre['errores']['Error']['!mensaje']) ? $timbre['errores']['Error']['!mensaje'] : '';
            }
        }

        return $dataResponse;
    }

}
