<?php

# Libreria de funciones
require '../../lib/general.inc.php';

# Recibe parametros
$fl_instituto = RecibeParametroNumerico('fl_instituto');
$ds_titulo=$_POST['ds_titulo'];
$fl_perfil=$_POST['fl_perfil'];


# Ruta para las imagenes
# tmp
if(!empty($fl_instituto)){

    $ruta_img = SP_HOME."/fame/site/uploads/$fl_instituto/awards";
}else{
    $ruta_img = SP_HOME."/fame/site/uploads/awards";
}
# Cambiamos los permisos de la carpeta 
chmod($ruta_img, 0777); 

# Recibe archivo.
if(!empty($_FILES)){
    # Nombre original del archivo
    $file_name_ori = $_FILES['file']['name'];
    # Nombre de la official
    $tempFile = $_FILES['file']['tmp_name'];
    # Obtenemos la extension del archivo
    $ext = ObtenExtensionArchivo($file_name_ori);      
    
    # Creamos la carpeta del video
    if (!file_exists($ruta_img)) {
        mkdir($ruta_img, 0777, true);        
    }
    # Cambiamos los permisos de la carpeta 
    chmod($ruta_img, 0777); 

    $date=date("Ymd His");

    # Nombre para todos los archivos
    $name_ori = explode(".", $file_name_ori);
    $name_main = $name_ori[0];
    $name_main=$name_main.$date;
    #Quitar espacios en nombre.
    $name_main=str_replace(" ","_",$name_main);

    $filename=$name_main.".".$ext;

    move_uploaded_file($tempFile, $ruta_img."/".$filename);


    if(empty($fl_instituto)){
        $Query="INSERT INTO k_awards(fl_perfil,ds_titulo,nb_imagen,fe_creacion,fe_ulmod)";
        $Query.="VALUES($fl_perfil,'$ds_titulo','$filename',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";  
       
    }else{
        $Query="INSERT INTO k_awards(fl_instituto,nb_imagen,fe_creacion,fe_ulmod)";
        $Query.="VALUES($fl_instituto,'$filename',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
    }
    $dat=EjecutaInsert($Query);

    
}


if(!empty($fl_instituto)){


    $Query="SELECT nb_imagen FROM k_awards WHERE fl_instituto=$fl_instituto ";
    $rs=EjecutaQuery($Query);
    for($i=1;$row=RecuperaRegistro($rs);$i++){
        
        $nb_imagen=$row['nb_imagen'];
        
        echo"<div class='col-md-3'><a class='zoomimg' href='#'> 
                                 <img src='../../../fame/site/uploads/$fl_instituto/awards/$nb_imagen' class='away no-border' class='img-rounded' width='40px' height='40px'>
                                  <span style='left:-300px;'>
                                  <div class='modal-dialog demo-modal' style='width:500px;height:500px;margin-top: 10px;'>
                                   <div class='modal-content' style='width:500px;height:500px;'>
                                   <div class='modal-body padding-5'  style='width:500px;height:500px;'>
                                        <img class='superbox-current-img' src='../../../fame/site/uploads/$fl_instituto/awards/$nb_imagen' style='width:494px;height:490px;'>
                                    </div>
                                    </div>
                                    </div>
                                    </span>
                                	</a>&nbsp;&nbsp;
         </div>";

        




    }

}else{
    $result["error"] = false;
    $result["success"] = true;
    echo json_encode((Object) $result);
}

?>
