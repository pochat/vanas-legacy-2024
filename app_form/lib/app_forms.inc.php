<?php

/**
JGFL 20180216
Funciones Bootstrap
**/
function Forma_CampoSelect_Boostrap($p_prompt, $p_nombre, $p_opc, $p_val, $p_actual, $p_seleccionar=False, $col_size="12", $fa="fa-flag", $p_script="", $p_requerido=false) {
  $required = "";
  $ic_color = "#A2A2A2";
  if($p_requerido==true){
    $required = ObtenEtiqueta(2248);
    $ic_color = "#0092DB";
  }
  
  if($p_prompt=="")
    $opt_def = ObtenEtiqueta(70);
  else
    $opt_def = $p_prompt;
  $sec = "
  <div class='col col-sm-12 col-md-12 col-lg-".$col_size." padding-10'>
  <div class='form-group no-margin' id='div_".$p_nombre."'>  
    <div class='input-group select'>
      <span class='input-group-addon' style='color:".$ic_color."; background-color:#fff;'><i class='fa ".$fa." fa-fw' style='height:25px; font-size:20px;line-height:30px;'></i></span>
      <select name='".$p_nombre."' id='".$p_nombre."' class='select2' style='height:40px;font-size:16px;' $p_script>";
      # Seleccionar
      if($p_seleccionar)
        $sec .= "<option value=''>".$opt_def." ".$required."</option>\n";
      # registros
      $tot = count($p_opc);
      for($i = 0; $i < $tot; $i++) {
        $sec .= "<option value=\"$p_val[$i]\"";
        if("".$p_actual."" == "".$p_val[$i]."")
          $sec .= " selected";
        $sec .= ">$p_opc[$i]</option>\n";
      }
  $sec .= "
      </select>
    </div>
  </div>
  </div>";
  
  return $sec;
  

}

function Forma_CampoSelectBD_Boostrap($p_prompt, $p_nombre, $p_query, $p_actual, $p_seleccionar=False, $col_size="12", $fa="fa-flag", $p_script="", $p_requerido=false) {
  $required = "";
  $ic_color = "#A2A2A2";
  if($p_requerido==true){
    $required = ObtenEtiqueta(2248);
    $ic_color = "#0092DB";
  }
  
  if($p_prompt=="")
    $opt_def = ObtenEtiqueta(70);
  else
    $opt_def = $p_prompt;  
  $html = "
  <div class='col col-sm-12 col-md-12 col-lg-".$col_size." padding-10'>
  <div class='form-group no-margin' id='div_".$p_nombre."'>  
    <div class='input-group select'>
      <span class='input-group-addon' style='color:".$ic_color."; background-color:#fff;'><i class='fa ".$fa." fa-fw' style='height:25px; font-size:20px;line-height:30px;'></i></span>
      <select name='".$p_nombre."' id='".$p_nombre."' class='select2 effect-7' ".$p_script." style='height:40px;'>";
      # Seleccionar
      if($p_seleccionar)
        $html .= "<option value=''>".$opt_def." ".$required."</option>\n";
      # registros
      $rs = EjecutaQuery($p_query);
      while($row = RecuperaRegistro($rs)) {
        $html .= "<option value=\"$row[1]\"";
        if($p_actual == $row[1])
          $html .= " selected";
        $html .= ">$row[0]</option>\n";
      }
  $html .= "
      </select>
      <span class='focus-border'><i></i></span>
    </div>
  </div>
  </div>";
  return $html;

}

function Forma_CampoTextoBootstrap($p_prompt, $p_nombre, $p_valor, $col_size="12", $ds_tipo="text", $fa="fa-user", $script="", $p_requerido = false) {
  $required = "";
  $ic_color = "";
  if($p_requerido==true){
    $required = ObtenEtiqueta(2248);
    $ic_color = ";color:#0092DB";
  }
  $data_mark = "";
  if($ds_tipo=="tel"){
    $data_mark = "data-mask='(999) 999-9999'";
  }
  if($ds_tipo=="sin"){
    $data_mark = "data-mask='999-999-999'";
    $ds_tipo="text";
  }
 $input = "
    <div class='col col-sm-12 col-md-12 col-lg-".$col_size." padding-10'>
      <div class='smart-form form-group' id='div_".$p_nombre."'>
        <label class='input'> <i class='icon-prepend fa ".$fa."' style='".$ic_color."; height:30px; font-size:20px;line-height:30px; width:33px;'></i>
          <input  placeholder='".$p_prompt." ".$required."' class='effect-7' type='".$ds_tipo."' name='".$p_nombre."' id='".$p_nombre."' value='".$p_valor."' $script style='height:40px;font-size:16px;padding-left:50px;' ".$data_mark.">
          <span class='focus-border'><i></i></span>
        </label>";        
  $input .="
      </div>
    </div>";
  return $input;
  
}

function CampoArchivo($p_nombre, $p_accept='', $p_maxlength='1', $p_prompt="", $p_valor="") {
  
  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
    if(!empty($p_accept))
      $ds_accept = "accept='$p_accept'";
    if(!empty($p_maxlength))
      $ds_maxlength = "maxlength='$p_maxlength'";
    $ds_nombre = $p_nombre;
    
    if(!empty($p_accept) OR $p_maxlength <> '1') {
      $ds_nombre .= "[]";
      $ds_clase = 'multi';
    }
    return "<input type='text' placeholder='".$p_prompt."' readonly='' id='txt_$p_nombre' name='txt_$p_nombre' style='height:40px;font-size:16px;'>";
  }
  else
    Forma_CampoOculto($p_nombre, "");
}

function Forma_CampoArchivo($p_prompt, $p_nombre, $p_valor='', $p_accept='', $p_maxlength='1', $col_size="12", $fa="", $ruta_imagen, $p_requerido) {
  $required = "";
  $ic_color = "#A2A2A2";
  if($p_requerido==true){
    $required = ObtenEtiqueta(2248);
    $ic_color = "#0092DB";
  }
  $sec = "
  <div class='col col-sm-12 col-md-12 col-lg-".$col_size." smart-form padding-10'>
    <div class='form-group no-margin'>      
      <div class='superbox-list' id='img_div_".$p_nombre."'>";
      if($p_valor!=""){
      $sec .= "
        <img src='".$ruta_imagen."' class='superbox-current-img padding-bottom-10'>";
      }
  $sec .= "      
      </div>
      <div class='input-group'>
        <input type='hidden' id='img_cargada_".$p_nombre."' name='img_cargada_".$p_nombre."' value='".$p_valor."'>
        <span class='input-group-addon' style='color:".$ic_color  ."; background-color:#fff;'><i class='fa ".$fa." fa-fw' style='height:25px; font-size:20px;line-height:30px;'></i></span>
        <label for='file' class='input input-file'>
        <div class='button'><input type='file' name='".$p_nombre."' id='".$p_nombre."' onchange='this.parentNode.nextSibling.value = this.value; $(\"#txt_".$p_nombre."\").val(this.value);'>Browse</div>";
        $sec .= CampoArchivo($p_nombre, $p_accept, $p_maxlength, $p_prompt." ".$required, $p_valor);
   $sec .= "
        </label>
        
      </div>
    </div>
  </div>";

  return $sec;
  
}

function Forma_CampoUpload($p_prompt, $p_nombre, $p_valor='', $p_ruta, $p_accept='', $p_maxlength='1', $col_size="12", $fa="", $p_requerido=false) {
  
    return Forma_CampoArchivo($p_prompt, $p_nombre, $p_valor, $p_accept, $p_maxlength, $col_size, $fa, $p_ruta, $p_requerido);
}

function Forma_SeccionBootstrap($p_titulo, $col_size="12") {
  

  $sec =  "
  <div class='col col-sm-12 col-md-12 col-lg-".$col_size." padding-10'>
    <h4  style='margin:0px; color:#0092cd;'>".$p_titulo."</h4> <hr class='no-margin' />
  </div>\n";
  return $sec;
}

function CampoTextAreaBootstrap($p_prompt,$p_nombre, $p_valor, $p_cols, $p_rows, $p_clase='custom-scroll', $p_editar=True, $col_lbl="12", $fa="fa-comment", $p_requerido=false) {
  $required = "";
  $ic_color = "0092cd";
  if($p_requerido==true){
    $required = ObtenEtiqueta(2248);
    $ic_color = "color:#0092DB";
  }
  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {  
    $sec = "
    <section class='col-sm-12 col-md-12 col-lg-".$col_lbl." padding-10'>
      <div class='smart-form form-group no-margin' id='txtarea_".$p_nombre."'>
        <label class='label' style='color:#0092cd; font-size:16px;'>".$p_prompt."</label>
        <label class='textarea'>
          <i class='icon-append fa ".$fa."' style='".$ic_color."; height:30px; font-size:20px;'></i>
          <textarea class='$p_clase effect-7' id='$p_nombre' name='$p_nombre' cols=$p_cols rows=$p_rows placeholder='".$required."' style='font-size:16px;'";
          if($p_editar == False)
            $sec .= " readonly='readonly'";
          $sec .= ">$p_valor</textarea>
          <span class='focus-border'><i></i></span>
        </label>
       </div>
    </section>";
    return $sec;
  }
  else
    Forma_CampoOculto($p_nombre, $p_valor);
}

function Forma_CampoRadioBootstrap($p_prompt, $p_nombres = array(), $p_labels=array(), $p_valores=array(), $p_scripts=array(), $p_actual=null, $p_editars = array(), $fa="12", $xy="inline-group", $p_requerido=false){
    
    $required=null;
    
    if($p_requerido==true){
        $required = "<p style='color:#0092DB;'>".ObtenEtiqueta(2248)."</p>";    
    }
    $sec = "
  <div class='col col-sm-12 col-md-12 col-lg-".$fa."'>
      <section class='smart-form' id='sec_".$p_nombres[0]."'>
        <label class='label' id='prop_".$p_nombres[0]."' style='font-size:16px;'>".$p_prompt." ".$required."</label>
        <div class='".$xy."'>";
    
    for($i=0; $i<count($p_valores); $i++){
        $sec .= "
              <label class='radio' id='lbl_".$p_nombres[$i]."_".$i."' style='font-size:16px;'>
              <input type='radio' name='".$p_nombres[$i]."' id='".$p_nombres[$i]."' value='".$p_valores[$i]."' ".$p_scripts[$i]." ";
        # Valor actual
        if(($p_actual!=null)||($p_actual==0)){
            if($p_valores[$i] == $p_actual){ $sec .= " checked" ;}
        }
        # Editar
        if(!empty($p_editars)){
            if(($p_editars[$i] == False) && (count($p_editars[$i])>0)){$sec .= " disabled=disabled";}
        }
        # Scripts
        // if(empty($p_scripts[$i])) $sec .= $p_scripts[$i];
        $sec .= ">
              <i></i>".$p_labels[$i]."</label>";
    }
    
    $sec .= "
        </div>
      </section>
    </div>";
    return $sec;
}

function Forma_CampoCalendario($p_prompt, $p_nombre, $p_valor, $p_format="yy-mm-dd", $col_size="12", $fa="fa-calendar", $p_script="", $p_requerido=false){
  $required = "";
  $ic_color = "";
  if($p_requerido==true){
    $required = ObtenEtiqueta(2248);
    $ic_color = "color:#0092DB";
  }
  $sec = "
  <div class='col col-sm-12 col-md-12 col-lg-".$col_size."'>
    <div class='smart-form'>
      <label class='input'> <i class='icon-prepend fa ".$fa."'   style='".$ic_color."' style='top:6px'></i>
        <input type='text' name='".$p_nombre."' id='".$p_nombre."' placeholder='".$p_prompt." ".$required."' value='".$p_valor."' class='datepicker effect-7' data-dateformat='".$p_format."' $p_script style='height:35px;font-size:16px;'>
        <span class='focus-border'><i></i></span>
      </label>
    </div>  
  </div>	";
  return $sec;
}

function Forma_CampoOcultoBootstrap($p_nombre, $p_valor) {
  
  return "
    <input type='hidden' id='$p_nombre' name='$p_nombre' value=\"$p_valor\">\n";
}
?>
