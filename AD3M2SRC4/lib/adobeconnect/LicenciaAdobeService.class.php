<?php

require_once "LicenciaAdobeDao.class.php";

class LicenciaAdobeService {
  
  private $LicenciaAdobeDao;
  
  function __construct() {
    $this->LicenciaAdobeDao = new LicenciaAdobeDao();
  }
  
  public function getLicenciasActivas() {    
    return $this->LicenciaAdobeDao->getLicenciasActivas();    
  }

  public function getLicenciasDisponibles($licenciasUsadas, $enInsertLiveSession=false) {    
    return $this->LicenciaAdobeDao->getLicenciasDisponibles($licenciasUsadas, $enInsertLiveSession);    
  }  
  
  public function getLicenciaByClave($claveLicencia) {    
    return $this->LicenciaAdobeDao->getLicenciaByClave($claveLicencia);    
  }
  
  public function licenciasSuficientes($numRequeridas, $numDisponibles) {
    return ($numDisponibles >=  $numRequeridas);
    //return $numDisponibles;
  }

  #Para Zoom
  public function getLicenciasDisponiblesZoom($licenciasUsadas, $enInsertLiveSession=false) {    
      return $this->LicenciaAdobeDao->getLicenciasDisponiblesZoom($licenciasUsadas, $enInsertLiveSession);    
  } 
  public function licenciasSuficientesZoom($numRequeridas, $numDisponibles) {
      return ($numDisponibles >=  $numRequeridas);
      //return $numDisponibles;
  }

}
  
?>