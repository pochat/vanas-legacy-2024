<?php

require_once "ClasesDao.class.php";

class ClasesService {
  
  private $ClasesDao;
  
  function __construct() {
    $this->ClasesDao = new ClasesDao();
  }

  
  public function getNumClasesTraslapadas($fechaHora, $clave_clase,$fg_omitir_actuales='') {    
      return $this->ClasesDao->getNumClasesTraslapadas($fechaHora, $clave_clase,$fg_omitir_actuales);    
  }

  public function getClavesLicenciasTraslapadas($fechaHora, $clave_clase) {    
    return $this->ClasesDao->getClavesClasesTraslapadas($fechaHora, $clave_clase);
  }  
  
  public function getClasesTraslapadas($fechaHora, $clave_clase=0) {    
    return $this->ClasesDao->getClasesTraslapadas($fechaHora, $clave_clase);
  }
  
  
  public function getNumClasesTraslapadasZoom($fechaHora, $clave_clase,$fg_omitir_actuales='',$fg_omitir_temporales_actual='') {    
      return $this->ClasesDao->getNumClasesTraslapadasZoom($fechaHora, $clave_clase,$fg_omitir_actuales,$fg_omitir_temporales_actual);    
  }
  public function getClavesLicenciasTraslapadasZoom($fechaHora, $clave_clase) {    
      return $this->ClasesDao->getClavesClasesTraslapadasZoom($fechaHora, $clave_clase);
  } 

  public function getClasesTraslapadasZoom($fechaHora, $clave_clase=0) {    
      return $this->ClasesDao->getClasesTraslapadasZoom($fechaHora, $clave_clase);
  }
  
}
  
?>