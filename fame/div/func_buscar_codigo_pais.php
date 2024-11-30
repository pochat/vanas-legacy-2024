<?php


require '../../AD3M2SRC4/lib/general.inc.php';

    
$fl_pais=RecibeParametroNumerico('cl_iso_pais');	


#Busca el codigo de telefono del pais seleccionado
$Query="SELECT ds_no_codigo_area FROM c_pais  WHERE fl_pais=$fl_pais";
$row=RecuperaValor($Query);
$ds_codigo_pais=$row[0];


Forma_CampoOculto('ds_codigo_pais',$ds_codigo_pais);


if(empty($ds_codigo_pais))
    $ds_codigo_pais="<i class='fa fa-globe' aria-hidden='true'></i>";

if(!empty($fl_pais)){
   
    
?>

<script>
    $('#ds_codigo_telefono').val('');

</script>
        <!--<script src="js/jquery.min.js"></script>--->
        <script src="js/jquery.inputmask.bundle.min.js"></script>
   <script type="text/javascript">
       $(document).ready(function () {
           $(":input").inputmask();
       });
	     document.getElementById('ds_codigo_telefono').focus();
		  pageSetUp();
		 
        </script>

<?php
}

?>
 <!-------plugin del tagas de data mask-----estyees un data mask de numero ce campos obligatorios--->
		<!--<script src="js/jquery.maskedinput.js"></script>-->

		<!--<script>
		    jQuery(function ($) {
		        $("#ds_codigo_telefono").mask("(nnn)");
		       // $("#ds_codigo_telefono").mask("(999) 999-9999? x99999");
		    });
		</script>-->



<?php echo $ds_codigo_pais; ?>






 
     
<?php





	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

function Forma_CampoTextoSPPlaceHover2($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_maxlength, $p_size, $p_error='', $p_password=False, $p_id='', $fg_visible=True, $p_script='', $p_texto='', $class_div = "form-group", $prompt_aling='right', $col_sm_promt='col-sm-4', $col_sm_cam='col-sm-4',$placehover) {
    
    if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
        if(!empty($p_error)) {
            $ds_error = ObtenMensaje($p_error);
            $ds_clase_err = 'has-error';
            $ds_clase = 'form-control';      
        }
        else {
            $ds_clase = 'form-control';
            $ds_error = "";
            $ds_clase_err = '';
        }
        if(!empty($p_id)) {
            if($fg_visible)
                $ds_visible = "inline";
            else
                $ds_visible = "none";
        }

        
        echo "
    <div id='div_".$p_nombre."' class='row ".$class_div." ".$ds_clase_err."'>
      <label class='$col_sm_promt control-label text-align-$prompt_aling'>
        ";
        if(!empty($p_id)) echo "<div id='".$p_id."_ppt' style='display:$ds_visible;'>";
        if($p_requerido) echo "* ";
        if(!empty($p_prompt)) echo "$p_prompt:"; else echo "&nbsp;";
        if(!empty($p_id)) echo "</div>";
        echo "
        
      </label>
      <div class='$col_sm_cam'>
        <label class='input'>";
        if(!empty($p_id)) echo "<div id='$p_id' style='display:$ds_visible;'>";
        CampoTextoPlaceHover($p_nombre, $p_valor, $p_maxlength, $p_size, $ds_clase, $p_password, $p_script,$placehover);
        if(!empty($p_texto)) echo "<span class='css_default'>$p_texto</span>";
        if(!empty($p_id)) echo "</div>";
        if(!empty($p_error)){          
            echo "<span class='help-block'><i class='fa fa-warning'></i>".$ds_error."</span><input type='hidden' id='err_".$p_nombre."' value='1'>";
        }
        echo "
        </label>
      </div>      
    </div>";
        
    }
    else
        Forma_CampoOculto($p_nombre, $p_valor);
}

function CampoTextoPlaceHover($p_nombre, $p_valor, $p_maxlength, $p_size, $p_clase='css_input', $p_password=False, $p_script='',$placehover) {
    
    if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
        if(!$p_password)
            $ds_tipo = 'text';
        else
            $ds_tipo = 'password';
        echo "<input type='$ds_tipo' class='$p_clase' id='$p_nombre' name='$p_nombre' value=\"$p_valor\" maxlength='$p_maxlength' size='$p_size' placeholder='$placehover' ";
        if($p_password)
            echo " autocomplete='off'";
        if(!empty($p_script)) echo " $p_script";
        echo ">";
    }
    else
        Forma_CampoOculto($p_nombre, $p_valor);
}

                           
?>