<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros  
  parse_str($_POST['extra_filters']['advanced_search'], $advanced_search);
  $_POST += $advanced_search;
  $fl_param = $_POST['fl_param'];
  $fe_ini = $_POST['fe_uno'];
  $fe_dos = $_POST['fe_dos'];

  if($fe_ini){
  #Damos formato de fecha alos parametros recibidos.
  $fe_ini =strtotime('0 days',strtotime($fe_ini)); 
  $fecha1= date('Y-m-d',$fe_ini);
  }
  if($fe_dos){
  $fe_dos=strtotime('0 days',strtotime($fe_dos)); 
  $fecha2= date('Y-m-d',$fe_dos);
  }

  #Muestra resultados de la busqueda.
  $Query="SELECT  fl_term, nb_programa,ds_duracion, nb_periodo,no_grado ";
  $Query.="FROM k_term a, c_programa b, c_periodo c  ";
  $Query.="WHERE a.fl_programa=b.fl_programa AND a.fl_periodo=c.fl_periodo AND b.fg_archive='0' ";

  if($fl_param=='Start Date'){
     if($fecha1)
       $Query.="AND c.fe_inicio >= '$fecha1'  ";
     if($fecha2)
       $Query.="AND c.fe_inicio <= '$fecha2' ";  
     
  
  }
  $Query.="ORDER BY c.fe_inicio DESC, nb_programa, no_grado ";

  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);

  
?>
{

    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
      $fl_term=$row['fl_term'];
      $nb_programa=$row['nb_programa'];
      $ds_duracion=$row['ds_duracion'];
      $nb_periodo=$row['nb_periodo'];
      $no_grado=$row['no_grado'];  
      
  
      #Revisamos el grado
      if ($no_grado!='1'){

          #Revisamos que este asignado a un term inicial.
          $Query="SELECT a.fl_programa,a.fl_term_ini FROM k_term a, c_programa b, c_periodo c ";
          $Query.="WHERE a.fl_programa=b.fl_programa AND a.fl_periodo=c.fl_periodo AND fl_term=$fl_term ";
          $row=RecuperaValor($Query);
          $fl_term_i=$row[1];

          if($fl_term_i==0)
              $leyenda="<span class='text text-danger'><b><i> ".ObtenEtiqueta(2343)."</i><b></span>";
          else
              $leyenda="";


      }else
          $leyenda="";
         
  
    echo '
    {
        "checkbox": " ",
        
        "name": "<a href=\'javascript:Envia(\"terms_frm.php\",\"'.$fl_term.'\");\'>'.$nb_programa.' <br>'.$leyenda.'</a>",
        "fe_inicio": "<td><a href=\'javascript:Envia(\"terms_frm.php\",\"'.$fl_term.'\");\'>'.$ds_duracion.'</a></td>",           
        "course": "<td><a href=\'javascript:Envia(\"terms_frm.php\",\"'.$fl_term.'\");\'>'.$nb_periodo.'</a></td>",          
        "estatus": "<a href=\'javascript:Envia(\"terms_frm.php\",\"'.$fl_term.'\");\'>'.$no_grado.'</a> ",
        "delete": "<a href=\'javascript:Borra(\"terms_del.php\", \"'.$fl_term.'\");\' class=\'btn btn-xs btn-default\' ><i class=\'fa  fa-trash-o\'></i> </a> "
                        
           
 
    }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]

}
