<?php
  # Librerias
  require '../../lib/general.inc.php';
 
  
  # Consulta para el listado
  $Query  = "SELECT id,host_email_zoom,client_id_zoom,client_secret_zoom,host_id,fg_activo ";
  $Query .= "FROM zoom ";
  $Query .= "ORDER BY id ASC ";

  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
?>
{
    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
        $id = $row["id"];
        $host_email_zoom = str_texto($row["host_email_zoom"]);
        $client_id_zoom = str_texto($row["client_id_zoom"]);
        $client_secret_zoom = str_texto($row["client_secret_zoom"]);
        $host_id=$row['host_id'];
        $fg_activo=$row['fg_activo'];

        if($fg_activo==1){
            $label="Yes";
            $color="success";
        }else{
            $label="No";
            $color="danger";
        }


      
    echo '
    {      
      "id": "<div><a href=\'javascript:EnviaFame(\"zoom_frm.php\",'.$id.');\'>'.$id.' </a></div>",      
      "host_email": "<td><a href=\'javascript:EnviaFame(\"zoom_frm.php\",'.$id.');\'>'.$host_email_zoom.'</a></td>",
      "host_id": "<td><a href=\'javascript:EnviaFame(\"zoom_frm.php\",'.$id.');\'>'.$host_id.'</a></td>",
      "client_id": "<td><a href=\'javascript:EnviaFame(\"zoom_frm.php\",'.$id.');\'>'.$client_id_zoom.'</a></td>",
      "client_secret": "<td><a href=\'javascript:EnviaFame(\"zoom_frm.php\",'.$id.');\'>'.$client_secret_zoom.'</a></td>",
      "fg_activo": "<td><a href=\'javascript:EnviaFame(\"zoom_frm.php\",'.$id.');\'><label class=\'label label-'.$color.'\'>'.$label.'</label></a></td>"
      
    }';

      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]
}
