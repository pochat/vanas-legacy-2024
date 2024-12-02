<?php

  # Libreria de funciones
  require_once '../lib/general.inc.php';
  
  # Previene descargas si se ejecuta directamente en el URL
  if(!ValidaSesion( ))
    exit;
?>
// Datos del lov
var lov    = "";
var tipo   = ""; // 1=radioButton 2=checkBox
var tamLov = ""; // 1=chico 2=mediano 3=grande 4=enorme
var campo1 = "";
var campo2 = "";
var valorActual = "";
var condicion = "";
var sust_div = "";

// Crea la instancia del dialogo para lovs
$(function() {
	
	// crea el dialogo
	$("BODY").append(
		'<div id="lovDialog">'+
			'<div id="lovFiltro">'+
				'<br><input id="filtro" type="text" onkeypress="validaEnter(event)">&nbsp;'+
				'<img id="lovBuscar" src="<?php echo PATH_IMAGES; ?>/icon_search.gif" onclick="lovBusqueda()" />&nbsp;'+
				'<img id="lovLimpiar" src="<?php echo PATH_IMAGES; ?>/icon_erase.gif" onclick="lovLimpia()" />'+
				'<br><br>'+
				'<div id="lovTitulos"></div>'+	
			'</div>'+
			'<div id="lovLista"></div>'+
		'</div>'
	);
	
	$("#lovDialog").dialog({
		bgiframe: true,
		modal: true,
		autoOpen: false,
		resizable: false,
		closeOnEscape: true
	});
	
});

// Establece las opciones del lov
function jLov(lovName,tipoLov,titulo,tamanio,idCampo1,idCampo2,actual,cond,sustituir_div){
	// Setea los datos del lov
	lov         = lovName;
	tipo        = tipoLov;
	tamLov      = tamanio;
	campo1      = idCampo1;
	campo2      = idCampo2;
  valorActual = $('#'+idCampo1).val();
  condicion   = cond;
  sust_div    = sustituir_div;
	
	// Titulo del dialogo
  $('#lovDialog').dialog('option', 'title', titulo);
	
	// Valida el navegador para establecer el alto del dialogo
	if (navigator.appName != "Microsoft Internet Explorer"){ 
		$('#lovDialog').dialog('option', 'height', 390);
	}
	else{
		$('#lovDialog').dialog('option', 'height', 440);
	}
	
  // Setea la clase para el ancho del listado
	// Si el lov es chico
	if(tamLov == 1){
		$("#lovLista").addClass('chico');
		$('#lovDialog').dialog('option', 'width', 300);
	}
	//Si el lov es mediano
	if(tamLov == 2){
		$("#lovLista").addClass('mediano');
		$('#lovDialog').dialog('option', 'width', 500);
	}
	//Si el lov es grande
	if(tamLov == 3){
		$("#lovLista").addClass('grande');
		$('#lovDialog').dialog('option', 'width', 700);
	}
  //Si el lov es enorme
	if(tamLov == 4){
		$("#lovLista").addClass('enorme');
		$('#lovDialog').dialog('option', 'width', 800);
	}
  
  // Botones del dialogo
  $('#lovDialog').dialog('option', 'buttons', {
      
      <?php echo ETQ_CANCELAR; ?>: function() {
        
        // Limpia el filtro
        $("#filtro").val('');
        
        // Limpia los datos del lov
        clsDatosLov();
        
        // Cierra el dialogo
        $(this).dialog('close');
      },
      
      <?php echo ETQ_ACEPTAR; ?>: function() {		
        
        // Regresa los datos del campo seleccionado si es un lov radioButton
        if(tipo == 1){
          datosLovRadio();
        }
        
        // Regresa los datos del campo seleccionado si es un lov checkBox
        if(tipo == 2){
          datosLovCheck();
        }
        
        // Limpia los datos del lov
        clsDatosLov();
        
        // Cierra el dialogo
        $(this).dialog('close');
      }
    });
	
	// Se llena la lista
	lovBusqueda();
	
	// Se despliega el lov
	$("#lovDialog").dialog("open");
	
}

// Lov radio button
function datosLovRadio(){
	radioBtn = document.getElementsByName("selLov");
	var i;
	
	for (i=0;i<radioBtn.length;i++){
		if (radioBtn[i].checked) 
			break; 
	}
	
	// Regresa los parametros a los campos 
	params = radioBtn[i].value.split("^");
  vars_div = params[0].split("|");
  
  $("#"+campo1).val(vars_div[0]);
  $("#"+campo2).val(params[1]);
	
	// Limpia el filtro
	$("#filtro").val('');
  
  if(sust_div != '') {
    var nb_div = "div_"+campo1;
    
    if(vars_div[1] != '')
      $("#"+sust_div).val(vars_div[1]);
    
    $.ajax({
      type: "POST",
      url : nb_div+".php",
      data: "clave="+$("#"+campo1).val()+
            "&accion=lov"+
            "&variable="+$("#"+sust_div).val()+
            "&variable2=0",
      success: function(html){
        $("#"+nb_div).html(html);
      }
    });
  }
}

// Lov check box
function datosLovCheck(){
	fieldId = document.getElementById(campo1);
	fieldValue = document.getElementById(campo2);
	checkBox = document.getElementsByName("selLov");
	var i
	var j=0;
	var ids = new Array();
	var values = new Array();
	
	for (i=0;i<checkBox.length;i++){ 
		if (checkBox[i].checked){
			params = checkBox[i].value.split("^");
			ids[j] = params[0];
			values[j] = params[1];
			j++;
		}
	}
	
	// Limpia los campos de cualqueir valor agregado anteriormente
	fieldId.value = "";
	fieldValue.value = "";
	
	// Regresa los parametros a los campos 
	for (i=0;i<ids.length;i++){ 
	
		fieldId.value += ids[i]+",";
		fieldValue.value += values[i]+",";
	
	}
	
	fieldId.value = fieldId.value.substring(0,fieldId.value.length-1);
	fieldValue.value = fieldValue.value.substring(0,fieldValue.value.length-1);
	
	// Limpia el filtro
	$("#filtro").val('');
}

// Valida si se presiono enter para realizar la busqueda (13) o 
// se dio clic en el boton de lov o busqueda (-1)
function validaEnter(evt){
	if(evt.keyCode == 13){
		lovBusqueda();
	}
}

// Vacia los datos del catalogo en la lista
function lovBusqueda(){

	var filtro = $('#filtro').val();
	
	// Obtener la instancia del objeto XMLHttpRequest
	if(window.XMLHttpRequest) {
		peticion_http = new XMLHttpRequest();
	}
		else if(window.ActiveXObject) {
		peticion_http = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	// Preparar la funcion de respuesta
	peticion_http.onreadystatechange = despliegaLista;
	
	// Realizar peticion HTTP
	peticion_http.open("POST", "../../lib/adm_lovs.php", true);
	peticion_http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	peticion_http.send("lov=" + lov + "&str_filtro=" + filtro + "&val_ini=" + valorActual + "&tipo=" + tipo + "&cond=" + condicion);
	
	function despliegaLista() {
		
		if(peticion_http.readyState == 4) {
			if(peticion_http.status == 200) {
				
				cadena = peticion_http.responseText;
				
				// Llena la lista
				document.getElementById('lovLista').innerHTML = cadena;
			}
		}
	}
}

// Limpia los datos del lov
function clsDatosLov(){
	
	// Si el lov es chico
	if(tamLov == 1){
		$("#lovLista").removeClass('chico');
	}
	// Si el lov es mediano
	if(tamLov == 2){
		$("#lovLista").removeClass('mediano');
	}
	// Si el lov es grande
	if(tamLov == 3){
		$("#lovLista").removeClass('grande');
	}
	// Si el lov es enorme
	if(tamLov == 4){
		$("#lovLista").removeClass('enorme');
	}
	
	lov    = "";
	tipo   = "";
	tamLov = "";
	campo1 = "";
	campo2 = "";
	
}

// Limpia el filtro
function lovLimpia(){
	$('#filtro').val(''); 
	$('#filtro').select();
  lovBusqueda();
}