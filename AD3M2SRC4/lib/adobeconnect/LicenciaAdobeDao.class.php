<?php

// Desarrollo
//require_once($_SERVER[DOCUMENT_ROOT].'/vanas/desarrollo/lib/com_func.inc.php');
// Prod
// TODO Descomentar al subir
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/com_func.inc.php');

require_once(PATH_ADM_HOME . "/lib/adobeconnect/LicenciaAdobe.class.php");

class LicenciaAdobeDao {
    
    public function getLicenciasActivas() {
        
        $licenciasActivas = array();
        
        $Query = "SELECT * FROM c_licencia_adobe_c WHERE fg_activo = 1 ";
        $rs = EjecutaQuery($Query);
        for($i = 0; $row = RecuperaRegistro($rs); $i++) {
            $clLicencia = $row[0];
            $dsUsr = $row[1];
            $dsPwd = $row[2];
            $dsBaseDomain = $row[3];
            $dsRootFolderId = $row[4];
            $dsPrincipalId = $row[5];
            $fgActivo = $row[6];
            
            $Licencia = new LicenciaAdobe($clLicencia, $dsUsr, $dsPwd, $dsBaseDomain, $dsRootFolderId, $dsPrincipalId, $fgActivo);
            
            array_push($licenciasActivas, $Licencia);
            
        }
        
        return $licenciasActivas;
    }

    public function getLicenciasDisponibles($licenciasUsadas, $enInsertLiveSession) {    
        
        $licenciasActivas = array();
        
        // MDB 25/SEP/2016
        // Estaba usando este codigo pero es incorrecto, lo dejo comentado para futuras referencias.
        // No se deben dejar de considerar las licencias que se estan usando, porque da un dato incorrecto 
        // ya que es menor el numero de lic. indicadas, lo correcto es que estas licencias sean parte de las disponibles
        // portque se usaran en la actualizacion de los datos.
        
        $fgConsideraLicencias = false;
        
        if ($enInsertLiveSession) {
            if (sizeof($licenciasUsadas) > 0) {
                //echo "Tenemos licencias usadas: " . sizeof($licenciasUsadas) . "<br>";
                //echo "Lic usadas: " . $licenciasUsadas[0] . "<br>";
                $arrLicUsadas = implode(',', $licenciasUsadas);
                $fgConsideraLicencias = true;
            }
        }
        
        $Query = "SELECT * FROM c_licencia_adobe_c WHERE fg_activo = 1 ";
        if ($fgConsideraLicencias)
            $Query .= "AND cl_licencia NOT IN ({$arrLicUsadas})";
        
        //echo "<br>Licencias disponibles: $Query <br>";  
        $rs = EjecutaQuery($Query);
        for($i = 0; $row = RecuperaRegistro($rs); $i++) {
            $clLicencia = $row[0];
            $dsUsr = $row[1];
            $dsPwd = $row[2];
            $dsBaseDomain = $row[3];
            $dsRootFolderId = $row[4];
            $dsPrincipalId = $row[5];
            $fgActivo = $row[6];
            
            $Licencia = new LicenciaAdobe($clLicencia, $dsUsr, $dsPwd, $dsBaseDomain, $dsRootFolderId, $dsPrincipalId, $fgActivo);
            
            array_push($licenciasActivas, $Licencia);
            
        }
        
        return $licenciasActivas;
    }  
    
    public function getLicenciaByClave($claveLicencia) { 
        $Query = "SELECT * FROM c_licencia_adobe_c WHERE cl_licencia = {$claveLicencia} ";
        $row = RecuperaValor($Query);      
        
        $clLicencia = $row[0];
        $dsUsr = $row[1];
        $dsPwd = $row[2];
        $dsBaseDomain = $row[3];
        $dsRootFolderId = $row[4];
        $dsPrincipalId = $row[5];
        $fgActivo = $row[6];
        
        $Licencia = new LicenciaAdobe($clLicencia, $dsUsr, $dsPwd, $dsBaseDomain, $dsRootFolderId, $dsPrincipalId, $fgActivo);
        
        return $Licencia;        
    }      
    
    
    public function getLicenciasDisponiblesZoom($licenciasUsadas, $enInsertLiveSession) {    
        
        $licenciasActivas = array();

        $fgConsideraLicencias = false;
        
        if ($enInsertLiveSession) {
            if (sizeof($licenciasUsadas) > 0) {
                //echo "Tenemos licencias usadas: " . sizeof($licenciasUsadas) . "<br>";
                //echo "Lic usadas: " . $licenciasUsadas[0] . "<br>";
                $arrLicUsadas = implode(',', $licenciasUsadas);
                $fgConsideraLicencias = true;
            }
        }
        
        $Query = "SELECT * FROM zoom WHERE fg_activo = 1 ";
        if ($fgConsideraLicencias)
            $Query .= "AND id NOT IN ({$arrLicUsadas}) ";
        $Query.="AND no_request<100 ";
        
        //echo "<br>Licencias disponibles: $Query <br>";  
        $rs = EjecutaQuery($Query);
        for($i = 0; $row = RecuperaRegistro($rs); $i++) {
            $clLicencia = $row[0];
            
            array_push($licenciasActivas, $clLicencia);
            
        }
        
        return $licenciasActivas;
    }  


}
?>
