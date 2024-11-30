<?php
$fl_usuario = ValidaSesion(False,0, True);



?>

<script>
    function EnviaNotificacionPublicacionViernes(fl_usuario_origen,ds_img,nb_texto){
        <?php
        /*$ult_pub="SELECT ds_contenido, nb_img_video, fe_alta FROM c_feed_publicaciones WHERE fl_usuario=$fl_usuario ORDER BY fl_publicacion DESC LIMIT 1;";
        $publicacion=RecuperaValor($ult_pub);
        $ds_contenido=$publicacion['ds_contenido'];
        $nb_img_video=$publicacion['nb_img_video'];
        $fe_alta=$publicacion['fe_alta'];*/
        $avatar=ObtenAvatarUsuario($fl_usuario);
        $nombre= ObtenNombreUsuario($fl_usuario);

        //$fecha=time_elapsed_string($fecha_Ac);
        ?>
        var contenido=document.getElementById("nueva-publicacion");
        contenido.innerHTML +="<div class=\"panel panel-default\">" +
            "                       <div class=\"panel-body status\">\n" +
            "                        <div class=\"who clearfix borde_inferior\">\n" +
            "                            <img src=\"<?php echo $avatar;?>\" alt=\"img\" class=\"online\">\n" +
            "                            <span class=\"name pull-right\">\n" +
            "                                <i class=\"fa fa-user height_user\"></i>\n" +
            "                                <i class=\"fa fa-share-square-o height_user\"></i>\n" +
            "                            </span>\n" +
            "                            <div class=\"margin-left-60\">\n" +
            "                                <span class=\"name \"><b><?php echo $nombre;?></b>\n" +
            "                                <b>• 1st.</b></span>\n" +
            "                                <span class=\"from\"><b>Engineer at Loomtek</b></span><br>\n" +
            "                                <span class=\"from \"><?php echo(!empty($fecha)?$fecha:NULL);?>.</span>\n" +
            "                            </div>\n" +
            "                        </div>\n" +
            "                        <div class=\"text padi_texto_comentario\">\n" + nb_texto +
            "                        </div>\n" +
            "                        <div class=\"image imagen_post\">\n" +
            "                        </div>\n" +
            "                        <ul class=\"links\">\n" +
            "                            <li>\n" +
            "                                <a href=\"javascript:void(0);\"><i class=\"fa fa-heart-o\"></i> 2000</a>\n" +
            "                            </li>\n" +
            "                            <li>\n" +
            "                                <a href=\"javascript:void(0);\"><i class=\"fa fa-comment-o\"></i> 91</a>\n" +
            "                            </li>\n" +
            "                            <!--<li>\n" +
            "                                <a href=\"javascript:void(0);\"><i class=\"fa fa-share-square-o\"></i> Share</a>\n" +
            "                            </li>-->\n" +
            "                        </ul>\n" +
            "                    </div>\n" +
            "                </div>";
    }
</script>
