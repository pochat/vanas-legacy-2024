<?php

  # Libreria de funciones
  require '../lib/general.inc.php';
  
  # Previene descargas si se ejecuta directamente en el URL
  if(!ValidaSesion( ))
    exit;
?>
/* Inicia frmSubmenus */
var
  editar_reg, // Registro que se esta editando, 0=Insertar
  fl_submenus;  // Folio del registro en la base de datos
  
	$(function() {
		
		var
      nb_submenu = $("#nb_submenu"),
			tr_submenu = $("#tr_submenu"),
			ds_submenu = $("#ds_submenu"),
			no_orden = $("#no_orden"),
			fg_menu = $("#fg_menu"),
			ds_fg_fijo = $("#ds_fg_fijo"),
			fl_parent = $("#fl_parent"),
			allFields = $([]).add(nb_submenu).add(tr_submenu).add(ds_submenu).add(no_orden),
			tips = $("#validateTips"),
      tot_regs_submenus = $("#tot_regs_submenus");
    
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
		
		$("#frmSubmenus").dialog({
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
            bValid = true, ds_fg_menu, registro, clase, liga, cadena;
          
					allFields.removeClass('css_input_error');
					bValid = bValid && checkLength(nb_submenu) && checkLength(no_orden);
					bValid = bValid && checkRegexp(no_orden,/^[0-9]+$/, "<?php echo ObtenMensaje(ERR_ENTERO); ?>");
          
					if (bValid) {
            if(editar_reg == 0) { // Inserta registro
              $('#tot_regs_submenus').val((tot_regs_submenus.val() * 1) + 1);
              registro = tot_regs_submenus.val();
              folio = 0;
            }
            else {
              registro = editar_reg;
              folio = fl_submenus;
            }
            
            if(fg_menu.attr('checked') == true)
              ds_fg_menu = "<?php echo ETQ_SI; ?>";
            else
              ds_fg_menu = "<?php echo ETQ_NO; ?>";
            
            if(registro % 2 == 0) {
              clase = "css_tabla_detalle_bg";
              clase_ico = "css_tabla_detalle_ico_bg";
            }
            else {
              clase = "css_tabla_detalle";
              clase_ico = "css_tabla_detalle_ico";
            }
            
            liga = "<a href=\"javascript:ActualizaEnTabla('submenus', "+registro+");\">";
            cadena = 
              '<tr id=reg_submenus_'+registro+'>'+
              '<td class='+clase+'>'+liga+nb_submenu.val()+'</a></td>'+
              '<input type=hidden name=submenus_1_reg_'+ registro+' id=submenus_1_reg_'+registro+' value="'+ nb_submenu.val()+'">'+
              '<td class='+clase+'>'+liga+tr_submenu.val()+'</a></td>'+
              '<input type=hidden name=submenus_2_reg_'+registro+' id=submenus_2_reg_'+registro+' value="'+tr_submenu.val()+'">'+
              '<td class='+clase+'>'+liga+ds_submenu.val()+'</a></td>'+
              '<input type=hidden name=submenus_3_reg_'+registro+' id=submenus_3_reg_'+registro+' value="'+ds_submenu.val()+'">'+
              '<td class='+clase+' align=right>'+liga+no_orden.val()+'</a></td>'+
              '<input type=hidden name=submenus_4_reg_'+registro+' id=submenus_4_reg_'+registro+' value="'+no_orden.val()+'">'+
              '<td class='+clase+' align=center>'+liga+ds_fg_menu+'</a></td>'+
              '<input type=hidden name=submenus_5_reg_'+registro+' id=submenus_5_reg_'+registro+' value="'+ds_fg_menu+'">'+
              '<td class='+clase+' align=center>'+liga+ds_fg_fijo.val()+'</a></td>'+
              '<input type=hidden name=submenus_6_reg_'+registro+' id=submenus_6_reg_'+registro+' value="'+ds_fg_fijo.val()+'">'+
              '<td class='+clase+'></td>'+
              '<input type=hidden name=submenus_7_reg_'+registro+' id=submenus_7_reg_'+registro+' value="'+fl_parent.val()+'">'+
              '<td class='+clase_ico+' align=center>'+liga+'<img src="<?php echo PATH_IMAGES."/".IMG_EDITAR; ?>" width=17 height=16 border=0 title="<?php echo ETQ_EDITAR; ?>"></a></td>'+
              '<td class='+clase_ico+' align=center><a href="javascript:BorraEnTabla(\'submenus\', '+registro+');"><img src="<?php echo PATH_IMAGES."/".IMG_BORRAR; ?>" width=17 height=16 border=0 title="<?php echo ETQ_ELIMINAR; ?>"></a></td>'+
              '<input type=hidden name=fl_submenus_'+registro+' id=fl_submenus_'+registro+' value='+folio+'>'+
              '</tr>';
            
            if(editar_reg == 0) { // Inserta registro
              $('#submenus tbody').append(cadena);
            }
            else { // Edita registro
              var o = $('#reg_submenus_' + editar_reg);
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
  
    $('#fg_menu').attr('checked', true);
    $('#ds_fg_fijo').html('<?php echo ETQ_NO; ?>');
    $('#ds_fg_fijo').val('<?php echo ETQ_NO; ?>');
    $('#fl_parent').val('0');
    editar_reg = 0;
    $('#frmSubmenus').dialog('option', 'title', '<?php echo ObtenEtiqueta(167); ?>');
    $('#frmSubmenus').dialog('open');
    $('#nb_submenu').focus();
  }
  
  function InsertaNivel(reg) {
    
    $('#fg_menu').attr('checked', true);
    $('#ds_fg_fijo').html('<?php echo ETQ_NO; ?>');
    $('#ds_fg_fijo').val('<?php echo ETQ_NO; ?>');
    $('#fl_parent').val(reg);
    editar_reg = 0;
    $('#frmSubmenus').dialog('option', 'title', '<?php echo ObtenEtiqueta(167); ?>');
    $('#frmSubmenus').dialog('open');
    $('#nb_submenu').focus();
  }
  
  function ActualizaEnTabla(tabla, reg) {
    var
      nb_submenu,
			tr_submenu,
			ds_submenu,
			no_orden,
      ds_fg_menu,
      ds_fg_fijo,
			fl_parent,
			fl_editar;
    
    nb_submenu = $("#submenus_1_reg_"+reg).val();
    tr_submenu = $("#submenus_2_reg_"+reg).val();
    ds_submenu = $("#submenus_3_reg_"+reg).val();
    no_orden = $("#submenus_4_reg_"+reg).val();
    ds_fg_menu = $("#submenus_5_reg_"+reg).val();
    ds_fg_fijo = $("#submenus_6_reg_"+reg).val();
    fl_parent = $("#submenus_7_reg_"+reg).val();
    fl_editar = $("#fl_submenus_"+reg).val();
    $('#frmSubmenus').dialog('option', 'title', '<?php echo ObtenEtiqueta(168); ?>');
    $('#nb_submenu').val(nb_submenu);
    $('#tr_submenu').val(tr_submenu);
    $('#ds_submenu').val(ds_submenu);
    $('#no_orden').val(no_orden);
    if(ds_fg_menu == '<?php echo ETQ_NO; ?>')
      $('#fg_menu').attr('checked', false);
    else
      $('#fg_menu').attr('checked', true);
    $('#ds_fg_fijo').val(ds_fg_fijo);
    $('#ds_fg_fijo').html(ds_fg_fijo);
    $('#fl_parent').val(fl_parent);
    editar_reg = reg;
    fl_submenus = fl_editar;
    $('#frmSubmenus').dialog('open');
    $('#nb_submenu').focus();
  }
  
  function BorraEnTabla(tabla, reg) {
    var fl_borrar;
    
    fl_borrar = $('#fl_'+tabla+'_'+reg).val();
    $('#regs_borrar_'+tabla).val($('#regs_borrar_'+tabla).val()+fl_borrar+',');
    $('#reg_'+tabla+'_'+reg).remove();
  }
  
/* Termina frmSubmenus */