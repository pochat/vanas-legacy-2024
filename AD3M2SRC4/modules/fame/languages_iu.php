<?php
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion( );
  
  # Recibimos parametros
  $fl_idioma = RecibeParametroNumerico('fl_idioma');
  $fl_leccion = RecibeParametroNumerico('fl_leccion');
  // $ds_language1 = RecibeParametroHTML('ds_language', false);  
  $ds_language = $_POST['ds_language'];
  $ds_language = str_uso_normal($ds_language);  
  $fg_idiomas = RecibeParametroNumerico('fg_idiomas');
  $accion = RecibeParametroHTML('accion');
  if(empty($fg_idiomas)){
    $result['result'] = array(
    "error"=> true);
    # Debe traer leccion
    if(!empty($fl_leccion)){    
      # Buscamos la ruta de la leccion
      $ruta = SP_HOME."/vanas_videos/fame/lessons/video_".$fl_leccion."/";    
      
      # Obtenemos el nombre del archivo de la leccion
      $row = RecuperaValor("SELECT ds_vl_ruta FROM c_leccion_sp WHERE fl_leccion_sp=".$fl_leccion);
      $ds_vl_ruta = $row[0];
      
      # Obtenemos el idioma
      $row1 = RecuperaValor("SELECT ds_code, nb_idioma FROM c_idioma WHERE fl_idioma=".$fl_idioma);
      $ds_code = $row1[0];
      $nb_idioma = str_texto($row1[1]);
      
      # Creamos el archito vtt
      # File name
      $archivo0 = $ds_vl_ruta."_".$ds_code.".vtt";
      $archivo = $ruta.$archivo0;
      
      # Verificamos si esta activado o desactivado el idioma
      $rowAD = RecuperaValor("SELECT fg_activo FROM k_idioma_video WHERE fl_leccion_sp=$fl_leccion and fl_idioma=$fl_idioma");
      $activo = $rowAD[0];
          
      # Accion de elimar y activar o desactivar
      if($accion=="D" || $accion=="AD"){
        # Eliminamos registro de la BD y el archivo
        if($accion=="D"){
          if(unlink($archivo)){#unlikn para poder eliminar el archivo fisico
            EjecutaQuery("DELETE FROM k_idioma_video WHERE fl_leccion_sp=$fl_leccion and fl_idioma=$fl_idioma ");
          }else{
		    EjecutaQuery("DELETE FROM k_idioma_video WHERE fl_leccion_sp=$fl_leccion and fl_idioma=$fl_idioma ");
		  }
		  $message = ObtenEtiqueta(2023)."&nbsp;".$nb_idioma;
        }
        # Activamos o desactivamos
        else{
          # Actualizamos si los activamos o desactivamos
          if(empty($activo)){
            EjecutaQuery("UPDATE k_idioma_video SET fg_activo='1' WHERE fl_leccion_sp=$fl_leccion AND fl_idioma=$fl_idioma ");
            $message = ObtenEtiqueta(2019)."&nbsp;".$nb_idioma;
          }
          else{
            EjecutaQuery("UPDATE k_idioma_video SET fg_activo='0' WHERE fl_leccion_sp=$fl_leccion AND fl_idioma=$fl_idioma ");          
            $message = ObtenEtiqueta(2021)."&nbsp;".$nb_idioma;
          }
        }
      }
      else{
        # Si no existe el idioma en el video lo inserta en caso contrario lo actualiza
        if(!ExisteEnTabla('k_idioma_video', 'fl_leccion_sp', $fl_leccion, 'fl_idioma', $fl_idioma, true)){    
          $Query  = "INSERT INTO k_idioma_video (fl_leccion_sp,fl_idioma,ds_language,nb_archivo,fg_activo) ";
          $Query .= "VALUES ($fl_leccion, $fl_idioma, '".str_texto($ds_language)."','$archivo0', '1')";
          $message = ObtenEtiqueta(2024)."&nbsp;".$nb_idioma;
        }
        else{
          $Query = "UPDATE k_idioma_video SET ds_language='', fg_activo='1' WHERE fl_leccion_sp=$fl_leccion AND fl_idioma=$fl_idioma ";
          $message = ObtenEtiqueta(2022)."&nbsp;".$nb_idioma;
        }
        EjecutaQuery($Query);
        
        # Open file
        $fch= fopen($archivo, "w+"); 
        # Writing file
        // $ds_language = htmlentities($ds_language);        
        $ds_language = str_uso_normal($ds_language);
        fwrite($fch, $ds_language);
        # Close  file
        fclose($fch);        
      }
      
      # Resultado
      $result['result'] = array(
      "error" => false,
      "message" => $message
      );
    }
  }
  # Envviamos el layout actulaizado de los idiomas
  else{
    $idiomas = "<ul style='padding-left: 15px; padding-top:10px; color: darkgrey;'>";    
    $rs = EjecutaQuery("SELECT a.fl_idioma, nb_idioma, a.fg_activo FROM k_idioma_video a, c_idioma b WHERE  a.fl_idioma = b.fl_idioma AND  fl_leccion_sp=".$fl_leccion);
    $tot_idiomas = CuentaRegistros($rs);
    for($i=1;$rowl=RecuperaRegistro($rs);$i++){
      $fl_idioma_bd = $rowl[0];
      $ds_language = str_texto($rowl[1]);
      $fg_activo = $rowl[2];
      $eye = "fa-eye";
      $lbl_eye = "desactivar";
      if(empty($fg_activo)){
        $eye = "fa-eye-slash";
        $lbl_eye = "activar";
      }
      $pla1 = "right";
      $pla2 = "bottom";
      $pla3 = "left";
      if($i==$tot_idiomas){
        $pla1 = "top";
        $pla2 = "top";
      }
      $idiomas .= "
      <li class='message no-margin' style='padding-top:2px;'>
        <i class='fa ".$eye." cursor-pointer' rel='tooltip' data-placement='".$pla1."' data-original-title='".$lbl_eye."' onclick='Languages(".$fl_leccion.", ".$fl_idioma_bd.", \"AD\");'></i> 
        <a href='languages.php?fl_leccion=".$fl_leccion."&fg_editar=1&fl_idioma=".$fl_idioma_bd."' data-toggle='modal' data-target='#LanguageModal' rel='tooltip' data-placement='".$pla2."' data-original-title='Editar'>".$ds_language."</a> 
        <i class='fa fa-times pull-right cursor-pointer' rel='tooltip' data-placement='".$pla3."' data-original-title='delete' onclick='Languages(".$fl_leccion.", ".$fl_idioma_bd.", \"D\")'></i>
      </li>";
    }
    $idiomas .= "</ul>
    <script>pageSetUp();</script>";
    $result['contenido'] = $idiomas;
  }
  
  echo json_encode((Object) $result);
?>