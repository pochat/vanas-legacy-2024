<?php

// Desarrollo
// require_once($_SERVER[DOCUMENT_ROOT].'/vanas/lib/com_func.inc.php');
// Prod
// TODO Descomentar al subir
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/com_func.inc.php');

class ClasesDao {
    private $duracionClasePrevia;
    private $duracionClase;  
    
    public function getNumClasesTraslapadas($fechaHora, $clave_clase,$fg_omitir_actuales='') {

        $this->duracionClasePrevia = (ObtenConfiguracion(94) - 1); // El valor esta en minutos en la tabla de configuracion
        $this->duracionClase = ObtenConfiguracion(94); // El valor esta en minutos en la tabla de configuracion
        
        $numNormalesYExtras = $this->getCountClasesTraslapadas($fechaHora, $clave_clase);
        
        #Si estas editando las globales class no cuentan estas ya que todo apuntan alas temporales
        if(!empty($fg_omitir_actuales)){
            
        }else{
            $numGlobales = $this->getGlobalesTraslapadas($fechaHora);
        }
        $numGlobalesTemporales = $this-> getGlobalesTraslapadasTemporales($fechaHora);

        $numGlobalesGrupales = $this-> getGlobalesTraslapadasGrupales($fechaHora);
        
        $numNormalesYExtras=!empty($numNormalesYExtras)?$numNormalesYExtras:0;
        $numGlobales=!empty($numGlobales)?$numGlobales:0;
        $numGlobalesTemporales=!empty($numGlobalesTemporales)?$numGlobalesTemporales:0;
        $numGlobalesGrupales=!empty($numGlobalesGrupales)?$numGlobalesGrupales:0;

        return ($numNormalesYExtras + $numGlobales + $numGlobalesTemporales+$numGlobalesGrupales);        
    }

    public function getNumClasesTraslapadasZoom($fechaHora, $clave_clase,$fg_omitir_actuales='',$fg_omitir_temporales_actual='',$fg_zoom=1) {

        $this->duracionClasePrevia = (ObtenConfiguracion(94) - 1); // El valor esta en minutos en la tabla de configuracion
        $this->duracionClase = ObtenConfiguracion(94); // El valor esta en minutos en la tabla de configuracion
        
        $numNormalesYExtras = $this->getCountClasesTraslapadas($fechaHora, $clave_clase,$fg_zoom);
        
        #Si estas editando las globales class no cuentan estas ya que todo apuntan alas temporales
        if(!empty($fg_omitir_actuales)){
            
        }else{
            $numGlobales = $this->getGlobalesTraslapadas($fechaHora,$fg_zoom);
        }
        $numGlobalesTemporales = $this-> getGlobalesTraslapadasTemporales($fechaHora,$clave_clase,$fg_omitir_temporales_actual,$fg_zoom);

        $numGlobalesGrupales = $this-> getGlobalesTraslapadasGrupales($fechaHora,$fg_zoom);
        
        $numNormalesYExtras=!empty($numNormalesYExtras)?$numNormalesYExtras:0;
        $numGlobales=!empty($numGlobales)?$numGlobales:0;
        $numGlobalesTemporales=!empty($numGlobalesTemporales)?$numGlobalesTemporales:0;
        $numGlobalesGrupales=!empty($numGlobalesGrupales)?$numGlobalesGrupales:0;

        return ($numNormalesYExtras + $numGlobales + $numGlobalesTemporales+$numGlobalesGrupales);        
    }


    public function getCountClasesTraslapadas($fechaHora, $clave_clase,$fg_zoom='') {

        $Query  = "SELECT COUNT(DISTINCT kc.fl_clase) ";
        $Query .= "FROM k_clase kc ";
        if($fg_zoom==1)
            $Query .="JOIN k_live_session b ON b.fl_clase=kc.fl_clase ";
        $Query .= "WHERE 1=1 ";
        if($fg_zoom==1)
            $Query.="AND b.zoom_url IS NOT NULL AND b.zoom_url<>'' ";
        $Query .= "AND ( ( ";
        $Query .= "DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s') BETWEEN " ;
        $Query .= "DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL " . $this->duracionClase . " MINUTE), '%Y-%m-%dT%H:%i:%s') ";
        $Query .= ") OR (";   
        $Query .= "DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL " . $this->duracionClasePrevia . " MINUTE), '%Y-%m-%dT%H:%i:%s') BETWEEN " ;
        $Query .= "DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL " . $this->duracionClase . " MINUTE), '%Y-%m-%dT%H:%i:%s') ";  
        $Query .= ")) ";     
        if (!empty($clave_clase)) // Excluye la busqueda sobre si misma
        $Query .= "AND kc.fl_clase <> $clave_clase ";

        //echo "<br> Query para clases traslapadas HoraFin: $Query <br>";

        $row = RecuperaValor($Query);

        return !empty($row[0])?$row[0]:NULL;        
    }

    public function getClavesClasesTraslapadas($fechaHora, $clave_clase) {

        $duracionClasePrevia = (ObtenConfiguracion(94) - 1); // El valor esta en minutos en la tabla de configuracion
        $duracionClase = ObtenConfiguracion(94); // El valor esta en minutos en la tabla de configuracion

        $clavesLicenciasUsadas = array();

        # Cambia el Query para que haga union clases normales y globales
        $Query  = "SELECT DISTINCT cl_licencia, fl_clase_global FROM ( ";
        $Query .= "(SELECT kls.cl_licencia, 0 fl_clase_global FROM k_clase kc, k_live_session kls WHERE kc.fl_clase = kls.fl_clase ";
        $Query .= "AND ((DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s') BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') ";
        $Query .= "AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s')) OR ";
        $Query .= "(DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL ".$this->duracionClasePrevia." MINUTE), '%Y-%m-%dT%H:%i:%s') BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') ";
        $Query .= "AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s') )) ORDER BY kls.cl_licencia) UNION ";
        $Query .= "(SELECT kls.cl_licencia, kc.fl_clase_global FROM k_clase_cg kc, k_live_sesion_cg kls WHERE kc.fl_clase_cg = kls.fl_clase_cg ";
        $Query .= "AND ((DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s') BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') ";
        $Query .= "AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s')) OR ";
        $Query .= "(DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL ".$this->duracionClasePrevia." MINUTE), '%Y-%m-%dT%H:%i:%s') BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') ";
        $Query .= "AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s'))) ORDER BY kls.cl_licencia) ";
        $Query .= ") AS main ";
        //echo "<br> Query para claves lic traslapadas: $Query <br>";

        $rs = EjecutaQuery($Query);
        for($i = 0; $row = RecuperaRegistro($rs); $i++) {
            array_push($clavesLicenciasUsadas, $row[0], $row[1]);   
        }

        return $clavesLicenciasUsadas;        
    }  
    public function getClavesClasesTraslapadasZoom($fechaHora, $clave_clase) {

        $duracionClasePrevia = (ObtenConfiguracion(94) - 1); // El valor esta en minutos en la tabla de configuracion
        $duracionClase = ObtenConfiguracion(94); // El valor esta en minutos en la tabla de configuracion

        $clavesLicenciasUsadas = array();

        # Cambia el Query para que haga union clases normales y globales y se añade grupales
        $Query  = "SELECT DISTINCT zoom_id, fl_clase_global FROM ( ";
        $Query .= "(SELECT kls.zoom_id, 0 fl_clase_global FROM k_clase kc, k_live_session kls WHERE kc.fl_clase = kls.fl_clase ";
        $Query .= "AND ((DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s') BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') ";
        $Query .= "AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s')) OR ";
        $Query .= "(DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL ".$this->duracionClasePrevia." MINUTE), '%Y-%m-%dT%H:%i:%s') BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') ";
        $Query .= "AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s') )) ORDER BY kls.cl_licencia) UNION ";
        $Query .= "(SELECT kls.zoom_id, kc.fl_clase_global FROM k_clase_cg kc, k_live_sesion_cg kls WHERE kc.fl_clase_cg = kls.fl_clase_cg ";
        $Query .= "AND ((DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s') BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') ";
        $Query .= "AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s')) OR ";
        $Query .= "(DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL ".$this->duracionClasePrevia." MINUTE), '%Y-%m-%dT%H:%i:%s') BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') ";
        $Query .= "AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s'))) ORDER BY kls.cl_licencia) ";  
        $Query .=" UNION  ";
        $Query .= "(SELECT kls.zoom_id, 0 fl_clase_global FROM k_clase_grupo kc, k_live_session_grupal kls WHERE kls.fl_clase_grupo=kc.fl_clase_grupo ";
        $Query .= "AND ((DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s') BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') ";
        $Query .= "AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s')) OR ";
        $Query .= "(DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL ".$this->duracionClasePrevia." MINUTE), '%Y-%m-%dT%H:%i:%s') BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') ";
        $Query .= "AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s') )) ORDER BY kls.cl_licencia) ";
        
        $Query .= ") AS main ";
        //echo "<br> Query para claves lic traslapadas: $Query <br>";

        $rs = EjecutaQuery($Query);
        for($i = 0; $row = RecuperaRegistro($rs); $i++) {
            if(!empty($row[0]))
            array_push($clavesLicenciasUsadas, $row[0], $row[1]);   
        }

        return $clavesLicenciasUsadas;        
    }
    
    public function getClasesTraslapadas($fechaHora, $clave_clase) {
        
        $Query = "SELECT fl_clase, fl_grupo, fl_semana, fe_clase, fg_obligatorio, fg_adicional FROM (
     (SELECT kc.fl_clase, fl_grupo, fl_semana, kc.fe_clase, kc.fg_obligatorio, kc.fg_adicional
     FROM k_clase kc 
     WHERE 1=1 AND ( 
      ( 
        DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s') 
        BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') 
        AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s')
      )
      OR 
      (
        DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL ".$this->duracionClasePrevia." MINUTE), '%Y-%m-%dT%H:%i:%s') 
        BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') 
        AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s'))
     ) ORDER BY kc.fl_clase)
     UNION
     (SELECT kcg.fl_clase_cg fl_clase, '0' fl_grupo,'0' fl_semana, kcg.fe_clase, 
     kcg.fg_obligatorio, '0' fg_adicional
     FROM k_clase_cg kcg
     WHERE 1=1 AND ( 
      ( 
        DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s') 
        BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') 
        AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s')
      )
      OR 
      (
        DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL ".$this->duracionClasePrevia." MINUTE), '%Y-%m-%dT%H:%i:%s') 
        BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') 
        AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s'))
     ) ORDER BY kcg.fl_clase_cg, kcg.no_orden DESC)
     UNION
     (SELECT kcg.fl_clase_cg fl_clase, '0' fl_grupo,'0' fl_semana, kcg.fe_clase, 
     kcg.fg_obligatorio, '0' fg_adicional
     FROM k_clase_cg_temporal kcg
     WHERE 1=1 AND ( 
      ( 
        DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s') 
        BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') 
        AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s')
      )
      OR 
      (
        DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL ".$this->duracionClasePrevia." MINUTE), '%Y-%m-%dT%H:%i:%s') 
        BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') 
        AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s'))
     ) ORDER BY kcg.fl_clase_cg, kcg.no_orden DESC)

    UNION
     (SELECT kcg.fl_clase_grupo fl_clase, '0' fl_grupo,'0' fl_semana, kcg.fe_clase, 
     kcg.fg_obligatorio, '0' fg_adicional
     FROM k_clase_grupo kcg
     WHERE 1=1 AND ( 
      ( 
        DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s') 
        BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') 
        AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s')
      )
      OR 
      (
        DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL ".$this->duracionClasePrevia." MINUTE), '%Y-%m-%dT%H:%i:%s') 
        BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') 
        AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s'))
     ) ORDER BY kcg.fl_clase_grupo DESC)


    ) AS main ";
        $rs = EjecutaQuery($Query);

        return $rs;        
    }
    public function getClasesTraslapadasZoom($fechaHora, $clave_clase) {
        
        $Query = "SELECT fl_clase, fl_grupo, fl_semana, fe_clase, fg_obligatorio, fg_adicional,fg_grupal,fg_global_class FROM (
     (SELECT kc.fl_clase, fl_grupo, fl_semana, kc.fe_clase, kc.fg_obligatorio, kc.fg_adicional,'0'fg_grupal,'0'fg_global_class
     FROM k_clase kc 
     WHERE 1=1 AND ( 
      ( 
        DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s') 
        BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') 
        AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s')
      )
      OR 
      (
        DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL ".$this->duracionClasePrevia." MINUTE), '%Y-%m-%dT%H:%i:%s') 
        BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') 
        AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s'))
     ) ORDER BY kc.fl_clase)
     UNION
     (SELECT kcg.fl_clase_cg fl_clase, '0' fl_grupo,'0' fl_semana, kcg.fe_clase, 
     kcg.fg_obligatorio, '0' fg_adicional,'0'fg_grupal,'1'fg_global_class 
     FROM k_clase_cg kcg
     WHERE 1=1 AND ( 
      ( 
        DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s') 
        BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') 
        AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s')
      )
      OR 
      (
        DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL ".$this->duracionClasePrevia." MINUTE), '%Y-%m-%dT%H:%i:%s') 
        BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') 
        AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s'))
     ) ORDER BY kcg.fl_clase_cg, kcg.no_orden DESC)
     UNION
     (SELECT kcg.fl_clase_cg fl_clase, '0' fl_grupo,'0' fl_semana, kcg.fe_clase, 
     kcg.fg_obligatorio, '0' fg_adicional,'0'fg_grupal,'1'fg_global_class 
     FROM k_clase_cg_temporal kcg
     WHERE 1=1 AND ( 
      ( 
        DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s') 
        BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') 
        AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s')
      )
      OR 
      (
        DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL ".$this->duracionClasePrevia." MINUTE), '%Y-%m-%dT%H:%i:%s') 
        BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') 
        AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s'))
     ) ORDER BY kcg.fl_clase_cg, kcg.no_orden DESC)

    UNION
     (SELECT kcg.fl_clase_grupo fl_clase, '0' fl_grupo,'0' fl_semana, kcg.fe_clase, 
     kcg.fg_obligatorio, '0' fg_adicional,'1'fg_grupal,'0'fg_global_class 
     FROM k_clase_grupo kcg
     WHERE 1=1 AND ( 
      ( 
        DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s') 
        BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') 
        AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s')
      )
      OR 
      (
        DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL ".$this->duracionClasePrevia." MINUTE), '%Y-%m-%dT%H:%i:%s') 
        BETWEEN DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') 
        AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s'))
     ) ORDER BY kcg.fl_clase_grupo DESC)


    ) AS main ";
        $rs = EjecutaQuery($Query);

        return $rs;        
    }

    // TODO
    // Gabriel, aqui va tu query para obtener las clases globales que se traslapan en esa fecha y hora
    public function getGlobalesTraslapadas($fechaHora,$fg_zoom='') {
        //$arr_globales_traslapadas = array();
        // return 0;
        $Query  = "SELECT COUNT(DISTINCT kcg.fl_clase_cg)  ";
        $Query .= "FROM k_clase_cg kcg ";
        if($fg_zoom==1)
            $Query .=" JOIN k_live_sesion_cg b ON b.fl_clase_cg=kcg.fl_clase_cg ";
        $Query .= "WHERE 1=1 ";
        if($fg_zoom==1)
            $Query .=" AND b.zoom_url IS NOT null AND b.zoom_url<>'' "; 
        $Query .= "AND ( ( ";
        $Query .= "DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s') BETWEEN ";
        $Query .= "DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s') ";
        $Query .= ") OR ( ";
        $Query .= "DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL ".$this->duracionClasePrevia." MINUTE), '%Y-%m-%dT%H:%i:%s') BETWEEN ";
        $Query .= "DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s') ";
        $Query .= "))";
        
        $row = RecuperaValor($Query);

        return !empty($row[0])?$row[0]:NULL;
    }

    public function getGlobalesTraslapadasGrupales($fechaHora,$fg_zoom='') {

        $Query  = "SELECT COUNT(DISTINCT kcg.fl_clase_grupo)  ";
        $Query .= "FROM k_clase_grupo kcg ";
        if($fg_zoom==1)
            $Query.="JOIN k_live_session_grupal b ON b.fl_clase_grupo=kcg.fl_clase_grupo ";
        $Query .= "WHERE 1=1 ";
        if($fg_zoom==1)
            $Query .=" AND b.zoom_url IS not null AND b.zoom_url<>'' ";
        $Query .= "AND ( ( ";
        $Query .= "DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s') BETWEEN ";
        $Query .= "DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s') ";
        $Query .= ") OR ( ";
        $Query .= "DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL ".$this->duracionClasePrevia." MINUTE), '%Y-%m-%dT%H:%i:%s') BETWEEN ";
        $Query .= "DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s') ";
        $Query .= "))";

        $row = RecuperaValor($Query);

        return !empty($row[0])?$row[0]:NULL;
    }

    public function getGlobalesTraslapadasTemporales($fechaHora,$clave_clase='',$fg_omitir_temporales_actual='',$fg_zoom='') {
        //$arr_globales_traslapadas = array();  
        // return 0;
        $Query  = "SELECT COUNT(DISTINCT kcg.fl_clase_cg)  ";
        $Query .= "FROM k_clase_cg_temporal kcg ";
        $Query .= "WHERE 1=1 ";
        $Query .= "AND ( ( ";
        $Query .= "DATE_FORMAT({$fechaHora}, '%Y-%m-%dT%H:%i:%s') BETWEEN ";
        $Query .= "DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s') ";
        $Query .= ") OR ( ";
        $Query .= "DATE_FORMAT(DATE_ADD({$fechaHora}, INTERVAL ".$this->duracionClasePrevia." MINUTE), '%Y-%m-%dT%H:%i:%s') BETWEEN ";
        $Query .= "DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') AND DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL ".$this->duracionClase." MINUTE), '%Y-%m-%dT%H:%i:%s') ";
        $Query .= "))";
        if(!empty($fg_omitir_temporales_actual)){
            $Query.="AND fl_clase_cg<> $clave_clase";
        }
        
        $row = RecuperaValor($Query);

        return !empty($row[0])?$row[0]:NULL;
    }
}
  
?>