
<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(229, PERMISO_EJECUCION)) {    
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
 
  $fe_ini = isset($_POST['fe_uno'])?$_POST['fe_uno']:NULL;
  $fe_dos = isset($_POST['fe_dos'])?$_POST['fe_dos']:NULL;
 
  #Obtenemos fecha actual :
  $Query3 = "Select CURDATE() ";
  $row = RecuperaValor($Query3);
  $fe_consulta =strtotime('-1 years',strtotime($row[0])); #restamos 1 años.
  $fecha1= date('d-m-Y',$fe_consulta);
  $fecha_dos=strtotime('0 days',strtotime($row[0]));
  $fecha2= date('d-m-Y',$fecha_dos);	
	

  #Obtenemos año actual :
  $Query3 = "Select CURDATE() ";
  $row = RecuperaValor($Query3);
  $anio_actual=strtotime('-0 years',strtotime($row[0]));
  $anio_actual= date('Y',$anio_actual);
  
  $anio_anterior =strtotime('-1 years',strtotime($row[0])); #restamos 1 años.
  $anio_anterior= date('Y',$anio_anterior);

  $fe_consulta =strtotime('-1 years',strtotime(''.$anio_actual.'-07-01')); #restamos 1 años.
  $fecha1= date('d-m-Y',$fe_consulta);


  $fecha_dos=strtotime('0 days',strtotime(''.$anio_actual.'-06-30'));
  $fecha2= date('d-m-Y',$fecha_dos);	

  ?>

         <div class="col-sm-12 col-md-6 col-lg-4">
            <label class='hidden'><strong><?php echo ObtenEtiqueta(2062); ?> </strong></label>
            <?php echo"
	        <label class='input'><input type='text' name='fe_uno' id='fe_uno' maxlength='10' class='datepicker hasDatepicker' value='$fecha1' placeholder='" . ObtenEtiqueta(643) . "'>" . Forma_Calendario("fe_uno") . "</label>
            ";
	        ?>	
          </div>
          <div class="col-sm-12 col-md-6 col-lg-4">
            <label class='hidden'><strong><?php echo ObtenEtiqueta(2062); ?> </strong></label>
            <?php echo"
	        <label class='input'><input type='text' name='fe_dos' id='fe_dos' maxlength='10' class='datepicker hasDatepicker' value='$fecha2' placeholder='" . ObtenEtiqueta(642) . "'>" . Forma_Calendario("fe_dos") . "</label>
            ";
            Forma_CampoOculto('fl_programa','');      
                  
	        ?>	
          </div>
  
  
 
  
  <script>
  pageSetUp();
  </script>
