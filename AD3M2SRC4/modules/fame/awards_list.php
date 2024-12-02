<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros
  $criterio = RecibeParametroHTML('criterio');
  
 
  #muestra los institutos que ya se encuentran registrados
  $Query="SELECT fl_awards,nb_imagen,ds_titulo,fe_creacion,fl_perfil            
            FROM k_awards order by fl_awards DESC ";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);

  
?>
{

    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
         $fl_awards=$row['fl_awards'];
         $nb_imagen=$row['nb_imagen'];
         $fe_creacion=$row['fe_creacion'];
		 $ds_titulo=$row['ds_titulo'];
         $fl_perfil=$row['fl_perfil'];

         switch ($fl_perfil) {
             case '15':
                 $checked = 'Students';
                 break;

             case '14':
                 $checked = 'Teachers';
                 break;
             
             default:
                 $checked = 'Administrator/SuperAdmin';
                 break;
         }



         
         $archivo="../../../fame/site/uploads/awards/$nb_imagen";	
      echo '
        {
           "checkbox": "<!--<div class=\'checkbox \'><label><input class=\'checkbox\' id=\'ch_'.$i.'\' value=\''.$fl_instituto.'\' type=\'checkbox\' /><span></span><input type=\'hidden\' value=\''.$registros.'\' id=\'tot_registros\' /> </label></div>-->",
            "name":"<a href=\'javascript:Envia(\"awards_frm.php\",'.$fl_awards.');\'>'.$ds_titulo.'</a><br><small class=\'text-muted\'><i>'.$checked.'</i></small>",
            "filename": "<a class=\'thumbnail\' style=\'margin:auto;border: solid 0px; background: transparent;\' data-toggle=\'popover\' href=\'javascript:void(0);\' data-placement=\'top\' data-full=\''.$archivo.'\'>'.$nb_imagen.'</a>",
            "creted_date": "<td><a href=\'javascript:Envia(\"awards_frm.php\",'.$fl_awards.');\'>'.$fe_creacion.'</a></td>",            
            "estatus": "<td><a href=\'javascript:Borra(\"awards_del.php\",'.$fl_awards.');\' class=\'btn btn-xs btn-default\' style=\'margin-left:5px\'><i class=\'fa  fa-trash-o\'></i></a></td>"           
                        
           
 
        }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]

}
