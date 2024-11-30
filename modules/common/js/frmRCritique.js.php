
// Cambia tab seleccionado
function CambiaTab(alumno, semana, tab, init) {
  
  // Refresca area de trabajo
  $.ajax({
    type: 'POST',
    url : 'div_critique.php',
    data: 'alumno='+alumno+
          '&semana='+semana+
          '&tab='+tab,
    success: function(html) {
      $('#div_critique').html(html);
    }
  });
  
  // Limpia el pizarron
  if(init != 1) {
    // Limpia el pizarron
    setCPDrawAction(5);
    // Apaga el pizarron
    if($("#canvas").css('display') == 'inline')
      togglePizarron.click();
  }
}

$(function() {
      
  $('#dlg_camara').dialog({
    width: 270,
    height: 240,
    position: [932, 218],
    closeOnEscape: false,
    title: 'My webcam',
    resizable: false,
    beforeClose: function(event, ui) { return false; }
  });
});

