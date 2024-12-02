<?php

  # Libreria de funciones
  require '../lib/general.inc.php';
  
  # Previene descargas si se ejecuta directamente en el URL
  if(!ValidaSesion( ))
    exit;
?>

/* Inicia frmAnexos */
function InsertaEnTabla(tabla) {
  var registro, clase, clase_ico, cadena,
      tot_regs_anexos = $("#tot_regs_anexos");
  
  registro = tot_regs_anexos.val();
  $('#tot_regs_anexos').val((tot_regs_anexos.val() * 1) + 1);
  
  if(registro % 2 == 0) {
    clase = "css_tabla_detalle";
    clase_ico = "css_tabla_detalle_ico";
  }
  else {
    clase = "css_tabla_detalle_bg";
    clase_ico = "css_tabla_detalle_ico_bg";
  }
  
  cadena = 
    '<tr class='+clase+' id=reg_anexos_'+registro+'>'+
    '<input type=hidden name=fl_anexo_'+registro+' id=fl_anexo_'+registro+' value=0>'+
    '<td align=center valign=top><input type=text name=no_orden_a_'+registro+' id=no_orden_a_'+registro+' maxlength=5 size=1 class=css_input></td>'+
    '<td valign=top><input type=text name=ds_caption_a_'+registro+' id=ds_caption_a_'+registro+' maxlength=255 size=20 class=css_input></td>'+
    '<td valign=top><input type=text name=tr_caption_a_'+registro+' id=tr_caption_a_'+registro+' maxlength=255 size=20 class=css_input></td>'+
    '<td><input type=file class=css_input id=archivo_a_'+registro+' name=archivo_a_'+registro+' size=20><br>'+
    '<?php echo ObtenEtiqueta(215); ?></td>'+
    '<input type=hidden name=nb_archivo_a_'+registro+' id=nb_archivo_a_'+registro+'>'+
    '<td><input type=file class=css_input id=archivo_at_'+registro+' name=archivo_at_'+registro+' size=20><br>'+
    '<?php echo ObtenEtiqueta(215); ?></td>'+
    '<input type=hidden name=nb_archivo_a_'+registro+' id=nb_archivo_a_'+registro+'>'+
    '<td class='+clase_ico+' align=center valign=top><a href="javascript:BorraEnTabla(\'anexos\', '+registro+');"><img src="<?php echo PATH_IMAGES."/".IMG_BORRAR; ?>" width=17 height=16 border=0 title="<?php echo ETQ_ELIMINAR; ?>"></a></td>'+
    '</tr>'+
    '<tr class='+clase+' id=reg_anexos2_'+registro+'>'+
    '<td align=center>&nbsp;</td>'+
    '<td><input type=text name=ds_texto_a_'+registro+' id=ds_texto_a_'+registro+' maxlength=255 size=20 class=css_input></td>'+
    '<td><input type=text name=tr_texto_a_'+registro+' id=tr_texto_a_'+registro+' maxlength=255 size=20 class=css_input></td>'+
    '<td><input type=file class=css_input id=imagen_a_'+registro+' name=imagen_a_'+registro+' size=20><br>'+
    '<?php echo ObtenEtiqueta(215); ?></td>'+
    '<input type=hidden name=nb_imagen_a_'+registro+' id=nb_imagen_a_'+registro+'>'+
    '<td>&nbsp;</td>'+
    '<td class='+clase_ico+' align=center>&nbsp;</td>'+
    '</tr>';
  $('#anexos tbody').append(cadena);
}


function BorraEnTabla(tabla, reg) {
  var fl_borrar;
  
  fl_borrar = $('#fl_anexo_'+reg).val();
  $('#regs_borrar_'+tabla).val($('#regs_borrar_'+tabla).val()+fl_borrar+',');
  $('#reg_'+tabla+'_'+reg).remove();
  $('#reg_anexos2_'+reg).remove();
}
  
/* Termina frmAnexos */