<?php

require_once PATH_ADM_CLASS . '/nusoap.php';

class PacSolucionFactible {

    private $usrPAC = 'testing@solucionfactible.com';
    private $pwdPAC = 'timbrado.SF.16672';
    private $urlWS = "https://testing.solucionfactible.com/ws/services/Timbrado?wsdl";

    function __construct() {
        
    }

    public function timbrar(Comprobante $Comprobante) {

        $xmlCFDI = $Comprobante->getXML();

        /**
         * EGMC 
         * Conectandose a los servicios del PAC
         */
        try {
            $client = new SoapClient($this->urlWS);
            $params = array('usuario' => $this->usrPAC, 'password' => $this->pwdPAC, 'cfdiBase64' => base64_encode(utf8_encode(str_ascii($xmlCFDI))), 'zip' => False);
            $call = $client->__soapCall('timbrarBase64', array('parameters' => $params));
        } catch (SoapFault $fault) {
            echo "SOAPFault: " . $fault->faultcode . "-" . $fault->faultstring . "\n";
        }

        $response = $call->return;

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

// Validar el arreglo para saber si es respuesta correcta
// El arreeglo $xmlErrors es el que usamos nosotros para acumular errores, por ejemplo si el RFC esta en blanco
        if ($response->status == 200) {

            $resultados = $response->resultados;
            $xmlTimbrado = (($resultados->cfdiTimbrado));
            $acuseRecibo = $resultados->uuid;

            // Creacion del objeto que contiene los datos de la factura
            $comprobanteTimbrado = new DatosComprobante();
            $comprobanteTimbrado->setXmlComprobante($xmlTimbrado);

            $domDocument = new DOMDocument('1.0', 'utf-8');
            $domDocument->loadXML($xmlTimbrado);

            $domDocument->save(PATH_ADM_FACTURAS . "/" . $comprobanteTimbrado->folioFiscal . ".xml");
        } else {

            echo
            Dbg::pd($response);
            $codigoError = $response->status;
            $mensajeError = $response->mensaje;
            $infoError = "";
        }
    }

}
