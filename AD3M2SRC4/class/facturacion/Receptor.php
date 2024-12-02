<?php

//use Domicilio;

/**
 * EGMC 20151109
 * Definición:
 * Nodo-Receptor
 *    Attr-rfc
 *    Attr-nombre
 *    Nodo-Domicilio
 */
class Receptor {

    public static $atributos = array(
        'rfc' => '',
        'nombre' => ''
    );
    
    // Atributos
    private $rfc;
    private $nombre;
    //  Objetos
    private $Domicilio;

    function __construct($rfc, $nombre, Domicilio $Domicilio) {

//        $this->setRfc($rfc);
//        $this->setNombre($nombre);
//        $this->setDomicilio($Domicilio);

        $this->rfc = $rfc;
        $this->nombre = $nombre;
        $this->Domicilio = $Domicilio;
    }

    // Getters y Setters
    public function getRfc() {
        return $this->rfc;
    }

    public function setRfc($rfc) {
        $this->rfc = $rfc;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function getDomicilio() {
        return $this->Domicilio;
    }

    public function setDomicilio($Domicilio) {
        $this->Domicilio = $Domicilio;
    }

}

?>