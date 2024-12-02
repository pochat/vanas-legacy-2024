
<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_ALUMNOS, PERMISO_EJECUCION)) {    
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
 
 
  $fl_param = isset($_POST['fl_param'])?$_POST['fl_param']:NULL;
  $fe_ini = isset($_POST['fe_uno'])?$_POST['fe_uno']:NULL;
  $fe_dos = isset($_POST['fe_dos'])?$_POST['fe_dos']:NULL;
 
  #Obtenemos fecha actual :
  $Query3 = "Select CURDATE() ";
  $row = RecuperaValor($Query3);
  $fe_consulta =strtotime('-1 years',strtotime($row[0])); #restamos 1 años.
  $fecha1= date('d-m-Y',$fe_consulta);
  $fecha_dos=strtotime('0 days',strtotime($row[0]));
  $fecha2= date('d-m-Y',$fecha_dos);	
	
	
  if($fl_param=="Start Date"){
	
            ?>

         <div class="col-sm-12 col-md-12 col-lg-4">
            <label class='hidden'><strong><?php echo ObtenEtiqueta(2062); ?> </strong></label>
            <?php echo"
	        <label class='input'><input type='text' name='fe_uno' id='fe_uno' maxlength='10' class='datepicker hasDatepicker' value='$fecha1' placeholder='" . ObtenEtiqueta(643) . "'>" . Forma_Calendario("fe_uno") . "</label>
            ";
	        ?>	
          </div>
          <div class="col-sm-12 col-md-12 col-lg-4">
            <label class='hidden'><strong><?php echo ObtenEtiqueta(2062); ?> </strong></label>
            <?php echo"
	        <label class='input'><input type='text' name='fe_dos' id='fe_dos' maxlength='10' class='datepicker hasDatepicker' value='$fecha2' placeholder='" . ObtenEtiqueta(642) . "'>" . Forma_Calendario("fe_dos") . "</label>
            ";
            Forma_CampoOculto('fl_programa','');      
                  
	        ?>	
          </div>
  
  
  <?php
  }else{
        
        
        $Query="
                SELECT DISTINCT c.fl_programa,nb_programa AS Course
                    FROM c_grupo a, k_term b, c_programa c, c_periodo d, c_usuario e WHERE a.fl_term=b.fl_term AND b.fl_programa=c.fl_programa
                         AND b.fl_periodo=d.fl_periodo AND a.fl_maestro=e.fl_usuario  AND c.fg_archive='0'  
                    ORDER By nb_programa ASC
        ";
        $rs = EjecutaQuery($Query);
        ?>

        <div class="col-sm-12 col-md-12 col-lg-4">
            <label class='hidden'><strong><?php echo ObtenEtiqueta(2061); ?> </strong></label>
            <select class="select2" name="fl_programa" id="fl_programa">
        
        <?php
           for($i = 1; $row = RecuperaRegistro($rs); $i++) {   
                 $fl_programa=$row[0];
                 $nb_programa=$row[1];
               
            echo "<option  value='" . $fl_programa . "'  >$nb_programa  </option> ";
            
            
           }
           Forma_CampoOculto('fe_uno','');
           Forma_CampoOculto('fe_dos','');
        ?>
                
        </div>              
  

 
   





        
  <?php      
    }
  ?>
  
  <script>
  pageSetUp();
  </script>
