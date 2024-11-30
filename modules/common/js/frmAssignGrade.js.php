
// Muestra dialogo para asignar calificacion
function AssignGrade(entrega) {
  
  $.ajax({
    type: "POST",
    url: "get_assign_grades.php",
    async: false,
    data: "fl_entrega_semanal="+entrega,
    success: function(msg){
      $('#dlg_grade_content').html(msg);
      $('#dlg_grade').dialog('open');
    }
  });
}

$(function() {
  
  $('#dlg_grade').dialog({
    autoOpen: false,
    resizable: false,
    width: 320,
    height: 300,
    hide: 'highlight',
    title: 'Assign grade',
    modal: true,
    buttons: {
      'Cancel': function() {
        $(this).dialog('close');
      },
      'Submit': function() {
        $(this).dialog('close');
        document.datos.submit();
      }
    }
  });
});

