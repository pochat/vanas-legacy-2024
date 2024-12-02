
<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_ALUMNOS, PERMISO_EJECUCION)) {    
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
 

  $year_current_actu = date('Y-m');

  
  $actual = strtotime($year_current_actu);
  $mesmenos = date("Y-m", strtotime("-0 month", $actual));
	
            ?>

         <div class="col-sm-12 col-md-12 col-lg-4">
            <label class='hidden'><strong><?php echo ObtenEtiqueta(2062); ?> </strong></label>
            
             <select class="select2" name="fl_instituto_params" id="fl_instituto_params" onchange="busqueda(this.value)">
                <option selected="selected" value='0'> <?php echo ObtenEtiqueta(2065); ?> </option>
                <?php
                $Queryd="SELECT DISTINCT DATE_FORMAT(fe_periodo,'%M, %Y'), DATE_FORMAT(fe_periodo,'%Y-%m')  FROM k_maestro_pago
                            WHERE  DATE_FORMAT(fe_periodo,'%Y-%m') ='$mesmenos' ";
                $rowd=RecuperaValor($Queryd);
                if(empty($rowd[0])){
                
                    $mesmenos = date("Y-m", strtotime("-1 month", $actual));
                }

                # Obtenemos todas las instituciones
                $Query  = "SELECT DISTINCT DATE_FORMAT(fe_periodo,'%M, %Y'), DATE_FORMAT(fe_periodo,'%Y-%m')  FROM k_maestro_pago
                            WHERE 1=1
                            ORDER BY fe_periodo DESC ";
                 $rs = EjecutaQuery($Query);
                 $cont=0;
                 for($i=0;$row=RecuperaRegistro($rs); $i++){
                   $ds_fecha = str_texto($row[0]);
                   $ds_fecha_1=str_texto($row[1]);
                     
					 $cont ++;
                  
                   if($mesmenos==$ds_fecha_1)
                       $selected="selected";
                   else
                       $selected="";
                   
				   //if($cont==2)
					//    $selected="selected";
                   //else
                   //    $selected="";
				   
				   
                   echo "<option ".$selected."   value='".$ds_fecha_1."' >".$ds_fecha."</option>";
                 }
                ?>
                </select>
             
             
             
             	
          </div>
          
  
  
 
 
  
  <script>
  pageSetUp();
  </script>
