<?php
# Libreria de funciones
require '../../lib/general.inc.php';


# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion();

# Recibe parametros

$clave = RecibeParametroNumerico('clave');
$fg_error = RecibeParametroNumerico('fg_error');
$error = RecibeParametroNumerico('error');


# Determina si es alta o modificacion
if(!empty($clave))
    $permiso = PERMISO_DETALLE;
else
    $permiso = PERMISO_ALTA;

# Verifica que el usuario tenga permiso de usar esta funcion
if(!ValidaPermiso(FUNC_REFERRAL_STATUS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
}

//programa actual
$programa = ObtenProgramaActual();




# Inicializa variables
if (!$fg_error) { // Sin error, viene del listado
    
   
	
	
   
    
    
   
} else { // Con error, recibe parametros (viene de la pagina de actualizacion)
   
   
    
   
}


# Presenta forma de captura
PresentaHeader();

PresentaEncabezado(FUNC_REFERRAL_STATUS);




# Forma para captura de datos
Forma_Inicia($clave, True);
?>







<?php

# Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
if ($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_REFERRAL_STATUS, PERMISO_MODIFICACION);
else
    $fg_guardar = True;
?>

<?php
Forma_Termina($fg_guardar);

# Pie de Pagina
PresentaFooter();





?>

