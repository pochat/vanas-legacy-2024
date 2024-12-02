<?php

  # Libreria de funciones
  require '../lib/general.inc.php';
  
  # Previene descargas si se ejecuta directamente en el URL
  if(!ValidaSesion( ))
    exit;
?>
/* Inicia frmColumnas */
var
  editar_reg, // Registro que se esta editando, 0=Insertar
  fl_columna;  // Folio del registro en la base de datos
  
	$(function() {
		
		var
      no_columna = $("#no_columna"),
			nb_columna = $("#nb_columna"),
			tr_columna = $("#tr_columna"),
			fg_align = $("#fg_align"),
			ds_fg_align = $("#ds_fg_align"),
			no_width_c = $("#no_width_c"),
			allFields = $([]).add(nb_columna).add(tr_columna).add(no_width_c),
			tips = $("#validateTips"),
      tot_regs_columnas = $("#tot_regs_columnas");
    
		function updateTips(t) {
			tips.text(t).effect("highlight",{},1500);
		}

		function checkLength(o) {

			if ( o.val().length < 1 ) {
				o.addClass('css_input_error');
				updateTips("<?php echo ObtenMensaje(ERR_REQUERIDO); ?>");
        o.focus();
	      o.select();
				return false;
			} else {
				return true;
			}

		}

		function checkRegexp(o,regexp,n) {

			if ( !( regexp.test( o.val() ) ) ) {
				o.addClass('css_input_error');
				updateTips(n);
        o.focus();
	      o.select();
				return false;
			} else {
				return true;
			}

		}
		
		$("#frmColumnas").dialog({
			autoOpen: false,
			width: 500,
			modal: true,
      resizable: false,
      buttons: {
        '<?php echo ETQ_CANCELAR; ?>': function() {
          updateTips('');
					$(this).dialog('close');
				},
				'<?php echo ETQ_SALVAR ?>': function() {
					var
            bValid = true, registro, clase, liga, cadena, ds_align;
          
					allFields.removeClass('css_input_error');
					bValid = bValid && checkLength(nb_columna);
          
					if (bValid) {
            if(editar_reg == 0) { // Inserta registro
              $('#tot_regs_columnas').val((tot_regs_columnas.val() * 1) + 1);
              registro = tot_regs_columnas.val();
              folio = 0;
            }
            else {
              registro = editar_reg;
              folio = fl_columna;
            }
            
            if(registro % 2 == 0) {
              clase = "css_tabla_detalle_bg";
              clase_ico = "css_tabla_detalle_ico_bg";
            }
            else {
              clase = "css_tabla_detalle";
              clase_ico = "css_tabla_detalle_ico";
            }
            
            ds_align = '<?php echo ObtenEtiqueta(253); ?>';
            if(fg_align.val() == 'C')
              ds_align = '<?php echo ObtenEtiqueta(254); ?>';
            if(fg_align.val() == 'R')
              ds_align = '<?php echo ObtenEtiqueta(255); ?>';
            
            liga = "<a href=\"javascript:ActualizaEnTabla('columnas', "+registro+");\">";
            cadena = 
              '<tr id=reg_columnas_'+registro+' class='+clase+'>'+
              '<td align=center>'+liga+registro+'</a></td>'+
              '<input type=hidden name=columnas_1_reg_'+registro+' id=columnas_1_reg_'+registro+' value="'+registro+'">'+
              '<td>'+liga+nb_columna.val()+'</a></td>'+
              '<input type=hidden name=columnas_2_reg_'+registro+' id=columnas_2_reg_'+registro+' value="'+nb_columna.val()+'">'+
              '<td>'+liga+tr_columna.val()+'</a></td>'+
              '<input type=hidden name=columnas_3_reg_'+registro+' id=columnas_3_reg_'+registro+' value="'+tr_columna.val()+'">'+
              '<td>'+liga+ds_align+'</a></td>'+
              '<input type=hidden name=columnas_4_reg_'+registro+' id=columnas_4_reg_'+registro+' value="'+fg_align.val()+'">'+
              '<td align=center>'+liga+no_width_c.val()+'</a></td>'+
              '<input type=hidden name=columnas_5_reg_'+registro+' id=columnas_5_reg_'+registro+' value="'+no_width_c.val()+'">'+
              '<td class='+clase_ico+' align=center>'+liga+'<img src="<?php echo PATH_IMAGES."/".IMG_EDITAR; ?>" width=17 height=16 border=0 title="<?php echo ETQ_EDITAR; ?>"></a></td>'+
              '<td class='+clase_ico+' align=center><a href="javascript:BorraEnTabla(\'columnas\', '+registro+');"><img src="<?php echo PATH_IMAGES."/".IMG_BORRAR; ?>" width=17 height=16 border=0 title="<?php echo ETQ_ELIMINAR; ?>"></a></td>'+
              '<input type=hidden name=fl_columna_'+registro+' id=fl_columna_'+registro+' value='+folio+'>'+
              '</tr>';
            
            if(editar_reg == 0) { // Inserta registro
              $('#columnas tbody').append(cadena);
            }
            else { // Edita registro
              var o = $('#reg_columnas_' + editar_reg);
              o.replaceWith(cadena);
            }
            
            updateTips('');
						$(this).dialog('close');
					}
				}
			},
			close: function() {
				allFields.val('').removeClass('css_input_error');
        updateTips('');
			}
		});
    
	});
  
  function InsertaEnTabla(tabla) {
    
    $('#ds_no_columna').html(($('#tot_regs_columnas').val() * 1) + 1);
    $('#fg_align').val('L');
    editar_reg = 0;
    $('#frmColumnas').dialog('option', 'title', '<?php echo ObtenEtiqueta(256); ?>');
    $('#frmColumnas').dialog('open');
    $('#nb_columna').focus();
  }
  
  function ActualizaEnTabla(tabla, reg) {
    var
      no_columna,
      nb_columna,
      tr_columna,
      fg_align,
			no_width_c,
			fl_editar;
    
    no_columna = $("#columnas_1_reg_"+reg).val();
    nb_columna = $("#columnas_2_reg_"+reg).val();
    tr_columna = $("#columnas_3_reg_"+reg).val();
    fg_align = $("#columnas_4_reg_"+reg).val();
    no_width_c = $("#columnas_5_reg_"+reg).val();
    fl_editar = $("#fl_columna_"+reg).val();
    $('#frmColumnas').dialog('option', 'title', '<?php echo ObtenEtiqueta(257); ?>');
    $('#no_columna').val(no_columna);
    $('#ds_no_columna').html(no_columna);
    $('#nb_columna').val(nb_columna);
    $('#tr_columna').val(tr_columna);
    $('#fg_align').val(fg_align);
    $('#no_width_c').val(no_width_c);
    editar_reg = reg;
    fl_columna = fl_editar;
    $('#frmColumnas').dialog('open');
    $('#nb_submenu').focus();
  }
  
  function BorraEnTabla(tabla, reg) {
    var
      fl_borrar;
    
    fl_borrar = $('#fl_columna_'+reg).val();
    $('#regs_borrar_'+tabla).val($('#regs_borrar_'+tabla).val()+fl_borrar+',');
    $('#reg_'+tabla+'_'+reg).remove();
  }
  
/* Termina frmColumnas */