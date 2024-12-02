<?php

  # Librerias
  require '../../lib/general.inc.php';

  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion();

  # Recibe parametros
  $fl_usuario = RecibeParametroNumerico('fl_usuario');
  $valor = RecibeParametroNumerico('valor');
  $fl_clase=RecibeParametroNumerico('fl_clase');
  $fg_gg=RecibeParametroHTML('fg_gg');



  if($valor){

      if($fg_gg){


           $Query="SELECT fl_live_session_grupal,fl_clase_grupo FROM k_live_session_grupal WHERE fl_clase_grupo= $fl_clase ";
           $ro = RecuperaValor($Query);

            $Queryf = "SELECT fe_clase from k_clase_grupo WHERE fl_clase_grupo=". $ro['fl_clase_grupo']." ";
            $rpf = RecuperaValor($Queryf);
            $fe_clase = $rpf['fe_clase'];

           #$Query = "SELECT count(*) FROM k_live_session_asistencia_gg WHERE fl_live_session_gg=".$ro['fl_live_session_grupal']." and fl_usuario=$fl_usuario ";
           #$rp = RecuperaValor($Query);

            EjecutaQuery("DELETE FROM  k_live_session_asistencia_gg where fl_live_session_gg=" . $ro['fl_live_session_grupal'] . " AND fl_usuario=$fl_usuario ");


            $Query = "insert into k_live_session_asistencia_gg (fl_live_session_gg,fl_usuario,cl_estatus_asistencia_gg,fe_asistencia_gg)";
            $Query .= "values(" . $ro['fl_live_session_grupal'] . ",$fl_usuario,$valor,'$fe_clase')";
            $fl_data = EjecutaInsert($Query);









    }else{


          $Query="select * from k_live_session where fl_clase = $fl_clase ";
          $ro=RecuperaValor($Query);

          $Query="SELECT fe_clase FROM k_clase WHERE fl_clase=$fl_clase ";
          $rp=RecuperaValor($Query);
          $fe_clase=$rp['fe_clase'];


          if(($valor==2)||($valor==3)){

              EjecutaQuery("DELETE FROM  k_live_session_asistencia where fl_live_session=".$ro['fl_live_session']." AND fl_usuario=$fl_usuario ");

              $Query ="insert into k_live_session_asistencia (fl_live_session,fl_usuario,cl_estatus_asistencia,fe_asistencia)";
              $Query.="values(".$ro['fl_live_session'].",$fl_usuario,$valor,'$fe_clase')";
              $fl_data=EjecutaInsert($Query);

          }


      }

  }

?>