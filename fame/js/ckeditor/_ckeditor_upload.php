<?php
require_once "CkEditorUpload.php";
        //copy paste de http://docs.cksource.com/CKEditor_3.x/Developers_Guide/File_Browser_(Uploader)/Custom_File_Browser
        // Required: anonymous function reference number as explained above.
        $funcNum = @$_GET['CKEditorFuncNum'] ? @$_GET['CKEditorFuncNum'] : 0;
        // Optional: instance name (might be used to load a specific configuration file or anything else).
        $CKEditor = @$_GET['CKEditor'];
        // Optional: might be used to provide localized messages.
        $langCode = @$_GET['langCode'];
        $url = "";

        if (isset($_FILES["upload"])) {

            $CkEditorUpload = new CkEditorUpload();
//            Debug::data($_FILES["upload"]);
            //Subimos el fichero a tamaño original
            $result = $CkEditorUpload->upload($_FILES["upload"], 'media/', $_FILES["upload"]["name"], null, array("jpg", "JPG", "jpeg", "JPEG", "gif", "GIF", "bmp", "BMP", "png", "PNG", "pdf", "PDF"));
            if (!$result) {
                // Si la imagen se sube correctamente enviamos el nombre de Ã©sta al usuario
                //echo "result: ".$this->Upload->result;
                //Se recupera el path de la foto original para obtener los metadatos
                //$fileName = $this->params['form']['Filedata']['name'];
                $data = $CkEditorUpload->result;
                $fileName = $data;
                //$thefile =  $this->base."/img/photos/560x0/".$fileName;
                $thefile = "http://localhost/vanas/AD3M2SRC4/js/ckeditor/media/" . $fileName;

                // Check the $_FILES array and save the file. Assign the correct path to a variable ($url).
                $url = $thefile;
                // Usually you will only assign something here if the file could not be uploaded.
                $message = '';
            } else {
                $message = "no result";
            }
        } else
            $message = "no file";
        echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction(1, '$url', '$message');</script>";
        die();