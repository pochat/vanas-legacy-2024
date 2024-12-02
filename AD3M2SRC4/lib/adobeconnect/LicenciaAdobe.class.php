<?php

class LicenciaAdobe {
  
  private $clLicencia;
  private $dsUsr;
  private $dsPwd;
  private $dsBaseDomain;
  private $dsRootFolderId;
  private $dsPrincipalId;
  private $fgActivo;
          
  function __construct($clLicencia, $dsUsr, $dsPwd, $dsBaseDomain, $dsRootFolderId, $dsPrincipalId, $fgActivo) {
    $this->clLicencia = $clLicencia;
    $this->dsUsr = $dsUsr;
    $this->dsPwd = $dsPwd;
    $this->dsBaseDomain = $dsBaseDomain;
    $this->dsRootFolderId = $dsRootFolderId;
    $this->dsPrincipalId = $dsPrincipalId;
    $this->fgActivo = $fgActivo;
  }
  
  function getClLicencia() {
    return $this->clLicencia;
  }

  function getDsUsr() {
    return $this->dsUsr;
  }

  function getDsPwd() {
    return $this->dsPwd;
  }

  function getDsBaseDomain() {
    return $this->dsBaseDomain;
  }

  function getDsRootFolderId() {
    return $this->dsRootFolderId;
  }

  function getDsPrincipalId() {
    return $this->dsPrincipalId;
  }

  function getFgActivo() {
    return $this->fgActivo;
  }

  function setClLicencia($clLicencia) {
    $this->clLicencia = $clLicencia;
  }

  function setDsUsr($dsUsr) {
    $this->dsUsr = $dsUsr;
  }

  function setDsPwd($dsPwd) {
    $this->dsPwd = $dsPwd;
  }

  function setDsBaseDomain($dsBaseDomain) {
    $this->dsBaseDomain = $dsBaseDomain;
  }

  function setDsRootFolderId($dsRootFolderId) {
    $this->dsRootFolderId = $dsRootFolderId;
  }

  function setDsPrincipalId($dsPrincipalId) {
    $this->dsPrincipalId = $dsPrincipalId;
  }

  function setFgActivo($fgActivo) {
    $this->fgActivo = $fgActivo;
  }
          
}


?>