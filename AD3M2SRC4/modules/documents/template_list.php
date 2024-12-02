<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros
  # Recibe Parametros  
  parse_str($_POST['extra_filters']['advanced_search'], $advanced_search);
  $_POST += $advanced_search;
  $fg_label = $_POST['fg_label'];
 

  
 #Recupermaos todos los labels.
  
 
  # Consulta para el listado
  $Query  = "SELECT  fl_template , nb_template , nb_categoria , ";
  $Query .= "CASE fg_activo WHEN 1 THEN '".ETQ_SI."' ELSE '".ETQ_NO."' END fg_activo, ";
  $concat = array(ConsultaFechaBD('fe_creacion', FMT_FECHA), "' '", ConsultaFechaBD('fe_creacion', FMT_HORAMIN));
  $Query .= "(".ConcatenaBD($concat).") fe_creacion, ";
  $concat2 = array(ConsultaFechaBD('fe_modificacion', FMT_FECHA), "' '", ConsultaFechaBD('fe_modificacion', FMT_HORAMIN));
  $Query .= "(".ConcatenaBD($concat2).") fe_modificacion ";  
  $Query .= "FROM k_template_doc, c_categoria_doc ";
  $Query .= "WHERE k_template_doc.fl_categoria = c_categoria_doc.fl_categoria ";
  if($fg_label==1)#Campus
      $Query .= "AND fg_sistema='0'  ";    
  if($fg_label==2)#FAME
  $Query .= "AND fg_sistema='1' ";
  
  $Query .= "ORDER BY fl_template ";

  
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);

  
?>
{

    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
      $fl_template = $row[0];
      $nb_template = str_texto($row[1]);
      $nb_categoria = str_texto($row[2]);          
      $fg_activo=str_texto($row[3]);
      $fe_creacion=str_texto($row[4]);
      $fe_modificacion=str_texto($row[5]);
	  
	  switch($fl_template)
	  {
		case 52:
			$order="1.-";
			break;
		case 53:
			$order="3.-";
			break;
		case 54:
			$order="2.-";
			break;
		default:
			$order="";
		
		  
	  }
	  

    echo '
    {      
      "fl_template": "<div><a href=\'javascript:EnviaFame(\"templates_frm.php\",'.$fl_template.');\'>'.$fl_template.' </a></div>",      
      "name": "<td><a href=\'javascript:EnviaFame(\"templates_frm.php\",'.$fl_template.');\'>'.$order.''.$nb_template.'</a></td>",                   
      "ds_categoria": "<td><a href=\'javascript:EnviaFame(\"templates_frm.php\",'.$fl_template.');\'>'.$nb_categoria.'</a></td>",
      "fg_activo": "<td><a href=\'javascript:EnviaFame(\"templates_frm.php\",'.$fl_template.');\'>'.$fg_activo.'</a></td>",     
      "fe_creacion": "<td><a href=\'javascript:EnviaFame(\"templates_frm.php\",'.$fl_template.');\'>'.$fe_creacion.'</a></td>",
      "fe_modificacion": "<td><a href=\'javascript:EnviaFame(\"templates_frm.php\",'.$fl_template.');\'>'.$fe_modificacion.'</a></td>",   
      "eliminar": "<td><a href=\'javascript:Borra(\"templates_del.php\",'.$fl_template.');\' class=\'btn btn-xs btn-default\' style=\'margin-left:5px\'><i class=\'fa  fa-trash-o\'></i></a></td>"   
    }';
	
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
		
    }
    ?>
   ]

}
