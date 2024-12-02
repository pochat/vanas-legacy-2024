<?php

  # Libreria de funciones
  require '../lib/general.inc.php';
  
  # Previene descargas si se ejecuta directamente en el URL
  if(!ValidaSesion( ))
    exit;
?>

$(function() {
	$("#prwDialog").dialog({
		modal: true,
		width: 'auto',
		heigth: 'auto',
		autoOpen: false,
		position: 'top',
		buttons: {
			<?php echo ETQ_ACEPTAR; ?>: function() {
				$(this).dialog('close');
				var titulo = document.getElementById('prwTitulo');
				titulo.innerHTML = "";
				var contenido = document.getElementById('prwContenido');
				contenido.innerHTML = "";
			}
		}
	});
});

  
function Preview(archivo) {
    
  var tipo = archivo.indexOf("swf");
  var titulo = document.getElementById('prwTitulo');
  var contenido = document.getElementById('prwContenido');
  var dialogo = document.getElementById('prwDialog');
  
  // Limpia el contenido anterior
  titulo.innerHTML = "<br>"+archivo;
  contenido.innerHTML = "";
  
  // Si el tipo de archivo es nulo quiere decir que es una imagen		
  if(tipo == "-1"){
    var imgPreview = document.createElement("img");
    imgPreview.setAttribute('id','imgPreview');
    imgPreview.setAttribute('src',archivo+"?"+Math.floor(Math.random()*101));
    contenido.appendChild(imgPreview);
    contenido.style.textAlign = "center"; 
    contenido.style.paddingRight = "0px";
    contenido.style.paddingLeft = "0px";
    dialogo.style.paddingBottom = "0px";
  
  // Si el tipo de archivo trae un valor quiere decir que es flash
  }else{
    
    var swfPreview = document.createElement("embed");
    swfPreview.setAttribute('src',archivo+"?"+Math.floor(Math.random()*101));
    swfPreview.style.position = "absolute";
    swfPreview.width = 'auto';
    swfPreview.height = 'auto';
    swfPreview.type = "application/x-shockwave-flash" ;
    swfPreview.quality = "high";	
    swfPreview.setAttribute("pluginspage","https://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash");
    contenido.appendChild(swfPreview);
    contenido.style.textAlign = "center";
    contenido.style.paddingRight = "200px";
    
    if(navigator.userAgent.toLowerCase().indexOf('chrome') > -1){
      contenido.style.paddingLeft = "280px";
    }
    
    dialogo.style.paddingBottom = "400px";
  
  }
  
  $('#prwDialog').dialog('open');
  
}

function LimpiaCampo(campo) {
  
  $('#'+campo).val('');
  $('#nom_'+campo).html('<?php echo ObtenEtiqueta(215); ?>');  
}
