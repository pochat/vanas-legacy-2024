<?php

	# Libreria de funciones	
	require("../lib/self_general.php");

	# Variable initialization
    $total_porcentaje=0;
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  /**
   * MJD ##la clave representa el fl_usu_pro de la tabla k_usuario_programa.
   * @param 
   * 
   */
  $clave = decriptClave($_GET['clave']);
  $fg_confirmado = RecibeParametroNumerico('c', True, False);
  $p=RecibeParametroNumerico('p',true,False);//para saber si tiene programa.
  
     if(!empty($p)){ //solo si tiene programa.
         # Obtenemos el usuario y el programa  
         $row = RecuperaValor("SELECT  fl_usuario_sp, fl_programa_sp, fe_entregado, fe_inicio_programa, fe_final_programa,ds_progreso FROM k_usuario_programa WHERE fl_usu_pro=$clave");       
         
         $fl_usuario_sp = $row[0];
         $fg_oculta_quiz = "";  
         $fl_programa_sp = $row[1];
         $fe_entregado=$row['fe_entregado'];
         $fe_inicio_curso=$row['fe_inicio_programa'];
         $fe_fin_curso=$row['fe_final_programa'];
         $ds_progreso=$row['ds_progreso'];
     }else{
         $Query="SELECT fl_usuario FROM c_usuario WHERE fl_usuario=$clave ";
         $row=RecuperaValor($Query);
         $fl_usuario_sp = $row['fl_usuario'];

     }
     $fl_perfil_usr=ObtenPerfilUsuario($fl_usuario_sp);

     #Verificamos su usuario no esta asignado a un programa  /*30-ene-2020  comentado ya no se utlizara ya que ahora todos los usuarios desde que te registras, se genera un c_usuario y se busca por el fl_usu_pro*/
     /*if(empty($fl_usuario_sp)){
         $Query="SELECT fl_usuario FROM c_usuario WHERE fl_usuario=$clave ";
         $row=RecuperaValor($Query);
         $fl_usuario_sp = $row['fl_usuario'];
     }*/

  #Verificamos si el adm o el teacher es logueado para acambiar el password
  if(($fl_perfil==PFL_ADMINISTRADOR)||($fl_perfil==PFL_MAESTRO_SELF))
      $fl_usuario_pwd=$fl_usuario_sp;
  else
      $fl_usuario_pwd=$fl_usuario;

  # indica que aun no ha confirmado el usuario  /* 30-ene-2020 comentado ahora todos los students ,teachers se genera un c_usuario */
 // if(empty($fg_confirmado)||empty($fl_programa_sp)){
  //  $fl_usuario_sp = RecibeParametroNumerico('clave', True, False);
 //   $fg_oculta_quiz = "hide";
 // }
  
  # Inicio del programa
  if(!empty($fe_inicio_progama)){
    #Damos formato alas fechas.
    $fe_inicio_curso=strtotime('+0 day',strtotime($fe_inicio_curso));
    $fe_inicio_curso= date('Y-m-d',$fe_inicio_curso);
    $fe_inicio_curso=GeneraFormatoFecha($fe_inicio_curso);
  }
  # Final del programa
  if(!empty($fe_final_programa)){
    $fe_fin_curso=strtotime('+0 day',strtotime($fe_fin_curso));
    $fe_fin_curso= date('Y-m-d',$fe_fin_curso);
    $fe_fin_curso=GeneraFormatoFecha($fe_fin_curso);
  }
  # Fecha de entrega
  if(!empty($fe_entregado)){
    $fe_entregado=strtotime('+0 day',strtotime($fe_entregado));
    $fe_entregado= date('Y-m-d',$fe_entregado);   
  }
  
  #Obtenemos fecha actual :
  $Query = "Select CURDATE() ";
  $row = RecuperaValor($Query);
  $fe_actual = str_texto($row[0]);
  $fe_actual=strtotime('+0 day',strtotime($fe_actual));
  $fe_actual= date('Y-m-d',$fe_actual);
  $fe_emision=GeneraFormatoFecha($fe_actual);
  
  # Obtenemos la informacion de los usuarios
 # if(!empty($fg_confirmado)){
    $Query  = "SELECT a.ds_nombres, a.ds_apaterno, a.fg_genero, ".ConsultaFechaBD('a.fe_nacimiento', FMT_CAPTURA).", b.ds_number, b.ds_street, b.ds_city, b.ds_state, ";
    $Query .= "b.ds_zip, a.ds_email, b.ds_phone_number, b.fl_pais, a.fl_instituto,a.ds_alias FROM c_usuario a ";
    $Query .= "LEFT JOIN k_usu_direccion_sp b ON(a.fl_usuario=b.fl_usuario_sp) ";
    $Query .= "WHERE a.fl_usuario=$fl_usuario_sp ";

 /* }  #,MJD 05/02/2010  Nota: Ya no se tomara en cuenta el k_envio_email_reg, todos los usuarios desde que se registran estan en c_usuario.
  else{
    $Query  = "SELECT a.ds_first_name, a.ds_last_name, a.fg_genero, DATE_FORMAT(a.fe_nacimiento, '%d-%m-%Y'), b.ds_number, b.ds_street, ";
    $Query .= "b.ds_city, b.ds_state, b.ds_zip, a.ds_email, b.ds_phone_number, b.fl_pais, a.fl_invitado_por_instituto ";
    $Query .= "FROM k_envio_email_reg_selfp a LEFT JOIN k_usu_direccion_sp b ON(a.fl_envio_correo=b.fl_usuario_sp) ";
    $Query .= "WHERE a.fl_envio_correo=$fl_usuario_sp ";
  }
  */
  $row = RecuperaValor($Query);
  $ds_nombres = str_texto($row[0]);
  $ds_apaterno = str_texto($row[1]);
  $fg_genero = str_texto($row[2]);
  $fe_nacimiento = str_texto($row[3]);
  $ds_number = str_texto($row[4]);
  $ds_street = str_texto($row[5]);
  $ds_city = str_texto($row[6]);
  $ds_state = str_texto($row[7]);  
  $ds_zip = str_texto($row[8]);
  $ds_email = str_texto($row[9]);
  $ds_phone_number = str_texto($row[10]);
  $fl_pais = $row[11];
  $fl_instituto = $row[12];
  $ds_alias=str_texto($row['ds_alias']);
  # Obtenemos el nombre del instituto
  $nb_instituto = ObtenNameInstituto($fl_instituto);
  
  # Si no ha introducido su pais tomara el del instituto
  if(empty($fl_pais)){
    $row3 =RecuperaValor("SELECT fl_pais  FROM c_instituto WHERE fl_instituto=$fl_instituto");
    $fl_pais = $row3[0];
  }  

  
  
  #Recupermaos el grado que tiene el usuario/alumno.
  $Query="SELECT fl_grado FROM c_alumno_sp WHERE fl_alumno_sp=$fl_usuario_sp";
  $row=RecuperaValor($Query);
  $fl_grado=$row[0];
  
  
  #Recuperamos los datos del papa
  $Query="SELECT cl_parentesco,ds_fname,ds_lname,ds_email_alumno,ds_email FROM k_responsable_alumno WHERE fl_usuario=$fl_usuario_sp ";
  
  $row=RecuperaValor($Query);
  $cl_parentesco=!empty($row[0])?$row[0]:NULL;
  $ds_fname=!empty($row[1])?$row[1]:NULL;
  $ds_lname=!empty($row[2])?$row[2]:NULL;
  $email_parentesco=!empty($row[4])?$row[4]:NULL;
  
  
  
  #Verificamos si tiene calificaciones asignadas, y si no lo tiene ,se esconde tab de grade teacher.
  $Query="SELECT COUNT(*) FROM k_entrega_semanal_sp A
         JOIN c_leccion_sp B on A.fl_leccion_sp=B.fl_leccion_sp AND B.fl_programa_sp=$fl_programa_sp
         WHERE A.fl_alumno=$fl_usuario_sp and A.fl_promedio_semana IS NOT NULL ";
  $row=RecuperaValor($Query);
  $existe_calificaciones=$row[0];
  if($existe_calificaciones>0)
      $fg_oculta_grade="";
  else
      $fg_oculta_grade="hidden";
  
  
  #Verificamos si tiene manual asessemt grade
  $Query="SELECT fg_grade_tea FROM  k_details_usu_pro WHERE fl_usu_pro=$clave ";
  $row=RecuperaValor($Query); 
  $fg_permite_ver_historial=!empty($row[0])?$row[0]:NULL;
  
  
?>

  <script src=\"https://use.fontawesome.com/840229d803.js\"></script>
  <!------permite que se visualize bien la j , la g en el input----->
  <style>

      .smart-form .input input, .smart-form .select select, .smart-form .textarea textarea {

          padding: 6px 10px;
      }
  </style> 
  
  
  
  
  <div class="row">
	<div class="col-md-5">
	
	<?php echo Profile_pic_FAME($fl_usuario_sp, $fl_programa_sp, $no_session=0, '', $fg_front=true); ?>
	
	</div>
	<div class="col-md-3">
	<?php if($fl_perfil_usr<>PFL_MAESTRO_SELF){  ?>
	
        <h6><?php echo ObtenEtiqueta(1077);?></h6><br />
        <div class="progress progress-xs" data-progressbar-value="<?php echo $ds_progreso; ?>"><div class="progress-bar"></div></div>
        
	<?php } ?>
	</div>
    <div class="col-md-2">
	</div>
	
	
  </div>
  </br>
  
  
  
  
  
<form class="smart-form" name="users" id="users" method="post" action="site/users_details_iu.php">
  <!-- widget content -->
  <div class="widget-body">
    <!-- Se muestra cuando esta guardando --->
    <div class='modal fade' id='save' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' style='overflow-y:scroll;overflow:auto'data-backdrop='static'>
      <span id="gabriel" class="ui-widget ui-chatbox txt-color-white" style="right: 0px; display: block; padding:0px 600px 350px 0px;">
        <i class="fa fa-cog fa-4x  fa-spin txt-color-white" style="position: relative;left: 25px;"></i><h2><strong> Loadding....</strong></h2>
      </span>
    </div>
    
    
    <!-- Tabs -->
    <ul id="myTab1" class="nav nav-tabs bordered">
      <li class="active">
        <a href="#programs" data-toggle="tab"><i class="fa fa-fw fa-lg fa-info"></i><?php echo ObtenEtiqueta(1918); ?></a>
      </li>
	  
	   <li class="">
        <a href="#relacion" data-toggle="tab"><i class="fa fa-fw fa-lg  fa-users"></i><?php echo ObtenEtiqueta(1653); ?></a>
      </li>
	  
      <li class="<?php echo $fg_oculta_quiz; ?>">
        <a href="#quizes" data-toggle="tab"><i class="fa fa-fw fa-lg fa-cubes"></i><?php echo ObtenEtiqueta(1919); ?></a>
      </li>
	  
	  <li >
        <a href="#assigment_grade" data-toggle="tab"><i class="fa fa-fw fa-lg fa-edit"></i><?php echo ObtenEtiqueta(1691); ?></a>
      </li>
	  
	  
	  
	  
	  <!------ Change Password ----->
      <div role="menu" class="widget-toolbar">
        <div class="btn-group">
          <button aria-expanded="true" class="btn dropdown-toggle btn-xs btn-info" data-toggle="dropdown">
            <?php echo ObtenEtiqueta(1875); ?> <i class="fa fa-caret-down"></i>
          </button>
          <ul class="dropdown-menu pull-right">
            <li>
            <a href="javascript:change_pwd(<?php echo $fl_usuario_pwd; ?>);">
            <i class="fa fa-key">&nbsp;</i><?php echo ObtenEtiqueta(1876); ?></a>
            </li>
          </ul>                    
        </div>
      </div>
	  
	  
	  
	  
	  
    </ul>
    
	
	
	<!-- Modal -->
  <div class="modal fade" id="modal-empty-student" tabindex="-1" role="dialog" aria-labelledby="item-title" aria-hidden="true">
    
  </div>
	
	
	
	
	
    <!--- Contenido -->
    <div id="myTabContent1" class="tab-content">
      <!--<form class="smart-form" name="users" id="users" method="post" action="site/users_details_iu.php">-->
      
        
        
        <div class="tab-pane fade in active"  id="programs">
        
		
		
		
		
        <!--
          <div class="row padding-10">
            <div class="col col-xs-12 col-sm-12 col-lg-1 padding-10">
              <img src="<?php echo ObtenAvatarUsuario($fl_usuario_sp); ?>" class="superbox-current-img" />
            </div>
            <div class="col-xs-12 col-sm-12 col-lg-5 padding-10">
              <h1><?php echo ObtenEtiqueta(1127); ?> <br><span class="semi-bold padding-left-10"><?php echo $nb_instituto; ?></span></h1>
            </div>
            <div class="col-xs-12 col-sm-12 col-lg-5 padding-10">
              <h1><?php echo ObtenEtiqueta(2000); ?><br><span class="semi-bold padding-left-10"><?php echo User_Invited($fl_usuario_sp); ?></span></h1>
            </div>            
          </div>--->

		
		  

		  
		  
		  
          <div class="row padding-10">          
            <div class="col col-xs-12 col-sm-5">
              <?php
              CampoTexto('ds_nombres', $ds_nombres, 'form-control', False, '', ObtenEtiqueta(909), "fa-user", "col-md-12", "append");
              ?>
            </div>
            <div class="col col-xs-12 col-sm-5">
              <?php
              CampoTexto('ds_apaterno', $ds_apaterno, 'form-control', False, '', ObtenEtiqueta(910), "fa-user", "col-md-12", "append");
              ?>
            </div>
          </div>
          
          <div class="row padding-10">          
            <div class="col col-xs-12 col-sm-5">
              <?php
              $opc = array(ObtenEtiqueta(115), ObtenEtiqueta(116), ObtenEtiqueta(128)); // Masculino, Femenino
              $val = array('M', 'F', 'N');
              CampoSelect('fg_genero', $opc, $val, $fg_genero, 'select2', True);
              ?>
            </div>
             <div class="col col-xs-12 col-sm-5">
              <?php
              CampoTexto('fe_nacimiento', $fe_nacimiento, 'form-control', False, '', ObtenEtiqueta(120), "", "col-md-12", "append");
              Forma_Calendario('fe_nacimiento');
              ?>
            </div>
          </div>
          
          <div class="row padding-10">          
            <div class="col col-xs-12 col-sm-5">
              <?php
              CampoTexto('ds_number', $ds_number, 'form-control', False, '', ObtenEtiqueta(1574), "", "col-md-12", "append");           
              ?>
            </div>
             <div class="col col-xs-12 col-sm-5">
              <?php
              CampoTexto('ds_street', $ds_street, 'form-control', False, '', ObtenEtiqueta(1577), "", "col-md-12", "append");           
              ?>
            </div>
          </div>
          
          <div class="row padding-10">          
            <div class="col col-xs-12 col-sm-5">
              <?php
              CampoTexto('ds_city', $ds_city, 'form-control', False, '', ObtenEtiqueta(1575), "", "col-md-12", "append");           
              ?>
            </div>
            <div class="col col-xs-12 col-sm-5" id="state_other">
              <?php
              CampoTexto('ds_state', $ds_state, 'form-control', False, '', ObtenEtiqueta(1578), "", "col-md-12", "append");           
              ?>
            </div>
            <div class="col col-xs-12 col-sm-5" id="state_canada">
              <?php
              $Querys = "SELECT CONCAT(ds_provincia,' - ', ds_abreviada ), fl_provincia FROM k_provincias WHERE fl_pais=38 ";
              CampoSelectBD('fl_state', $Querys, $ds_state, 'select2', True, $p_script='', $p_valores='', $p_seleccionar_txt = 'Select', $p_seleccionar_val =0);       
              ?>
            </div>
          </div>
          
          <div class="row padding-10">          
            <div class="col col-xs-12 col-sm-5">
              <?php
              CampoTexto('ds_zip', $ds_zip, 'form-control', False, '', ObtenEtiqueta(1576), "", "col-md-12", "append");           
              ?>
            </div>
            <div class="col col-xs-12 col-sm-5">
              <?php
              CampoTexto('ds_email', $ds_email, 'form-control', False, '', ObtenEtiqueta(766), "fa-envelope", "col-md-12", "append");           
              ?>
              
            </div>
          </div>
          
          <div class="row padding-10">          
            <div class="col col-xs-12 col-sm-5">
              <?php
              CampoTexto('ds_phone_number', $ds_phone_number, 'form-control', False, '', "Phone Number", " fa-phone", "col-md-12", "append");           
              ?>
            </div>
            <div class="col col-xs-12 col-sm-5">                        
              <?php
              $Query = "SELECT CONCAT(ds_pais,' - ',cl_iso2), fl_pais FROM c_pais WHERE 1=1 and fg_activo='1' ";
              CampoSelectBD('fl_pais', $Query, $fl_pais, 'select2', True, $p_script='', $p_valores='', $p_seleccionar_txt = 'Select', $p_seleccionar_val =0);       
              ?>
            </div>
          </div>


           <div class="row padding-10">                      
            <div class="col col-xs-12 col-sm-5">                        
              <?php
              $Query = "SELECT cl_clasificacion_grado,nb_clasificacion_grado  FROM  c_clasificacion_grado WHERE 1=1 ";
              $Query2 = "SELECT fl_grado,nb_grado,cl_clasificacion_grado FROM k_grado_fame WHERE cl_clasificacion_grado=#id_valor# ORDER BY  cl_clasificacion_grado asc ";
              CampoSelectBDGRupoFAME('fl_grado', $Query, $fl_grado, 'select2', True, $p_script='','',false, $Query2); 
             
              ?>
              <input type="hidden" name="fg_confirmado" id="fg_confirmado" value="<?php echo $fg_confirmado; ?>">
            </div>
            <div class="col col-xs-12 col-sm-5">
			  
			  
				<?php
					CampoTexto('ds_alias', $ds_alias, 'form-control', False, "onkeyup='ChangeAlias(".$fl_usuario_sp.");' ", "".ObtenEtiqueta(1793)."", " fa-key", "col-md-12", "append");           
				?>
				
              <!--<h5 style="font-aize:16px !important;"><?php echo ObtenEtiqueta(1793).": ";?> <strong><?php echo $ds_alias; ?></strong></h5>
			  -->	
            </div>
          </div>
         <?php if(($fl_perfil==25)||($fl_perfil==PFL_ADMINISTRADOR)){ ?>
          <div class="row padding-10">
              <div class="col col-xs-12 col-sm-5">                        
              <?php
              $Query = "SELECT ds_instituto, fl_instituto FROM c_instituto WHERE 1=1 and fg_activo='1' ";
              CampoSelectBD('fl_instituto', $Query, $fl_instituto, 'select2', True, $p_script='', $p_valores='', $p_seleccionar_txt = 'Select', $p_seleccionar_val =0);       
              ?>
            </div>
          </div>
        <?php } ?>

          <div class="row padding-10">          
            <div class="col col-xs-12 col-sm-5">
              &nbsp;<br /><br />
            </div>
            <div class="col col-xs-12 col-sm-5">                        
              
                &nbsp;<br /><br />
            </div>
          </div>
         
          
        </div>
        

        <div class="tab-pane fade"  id="quizes">
          <!--- NOMBRE DEL PROGRAMA ---->
          <div class="row no-margin padding-10">
            <h3><?php echo ObtenNombreCourse($fl_programa_sp); ?></h3>
          </div>
          
          <!--- DATOS DE LOS QUIZES ---->
          <div class="row no-margin">
            <table id="tbl_quiz" class="table table-striped table-bordered table-hover">
              <thead>
                <tr>
                  <th><?php echo ObtenEtiqueta(1605); ?> </th>
                  <th><?php echo ObtenEtiqueta(1606); ?> </th>
                  <th><?php echo ObtenEtiqueta(1607); ?> </th>
                  <th><?php echo ObtenEtiqueta(1608); ?> </th>
                  <th><?php echo ObtenEtiqueta(1609); ?> </th>
                  <th><?php echo ObtenEtiqueta(1610); ?> </th>
                  <th><?php echo ObtenEtiqueta(1611); ?> </th>
                </tr>
              </thead>
              <tbody>
              <?php
              
              $total_registros_a_dividir=0;
              #Recuperamos las lecciones del programa
              $Query2  = "SELECT fl_leccion_sp,no_semana,ds_titulo,nb_quiz,no_valor_quiz  ";
              $Query2 .= "FROM c_leccion_sp WHERE fl_programa_sp=$fl_programa_sp AND nb_quiz IS NOT NULL ";
              $rs2 = EjecutaQuery($Query2);
              $contador2=0;
              for($tot2=0;$row2=RecuperaRegistro($rs2);$tot2++) {
                $contador2 ++; 
                $fl_leccion_sp=$row2['fl_leccion_sp'];
                $no_session=$row2['no_semana'];
                $nb_leccion=$row2['ds_titulo'];
                $nb_quiz=$row2['nb_quiz'];
                $no_weight=$row2['no_valor_quiz'];
                
                #Recuperamos los quizes por cada leccion del programa.
                $Query3="SELECT no_intento,no_calificacion,cl_calificacion,fe_final FROM k_quiz_calif_final WHERE fl_leccion_sp=$fl_leccion_sp AND fl_usuario=$fl_usuario_sp ORDER BY no_intento ASC   ";
                $rs3 = EjecutaQuery($Query3);
                $tot_reg=CuentaRegistros($rs3);
                $contador3=0;
               
                for($tot3=0;$row3=RecuperaRegistro($rs3);$tot3++){
                  $fe_termino_quiz=GeneraFormatoFecha($row3['fe_final']);
                  $attemp=$row3['no_intento'];
                  $grade=$row3['cl_calificacion'];
                  $contador3 ++;                  
                  
                                    
                  
   
                  
                  
                  if($contador3==$tot_reg){
                   
                      $no_weight=$row2['no_valor_quiz']."%";
                      $porcentaje=$row3['no_calificacion']."%";
                      
                      $total_porcentaje+=$row3['no_calificacion'];
                  
                      $total_registros_a_dividir++;
                      
                  }else{
                  
                      $porcentaje=null;
                      $no_weight=null;
                  
                  }
                  
                  
                  
                  
                  
                  
                  echo "
                  <tr>
                    <td> ".$fe_termino_quiz." </td>
                    <td> ".$no_session." </td>
                    <td> ".$nb_quiz."  </td>
                    <td> ".$attemp." </td>
                    <td> ".$no_weight." </td>
                    <td> ".$grade."  </td>
                    <td> ".$porcentaje." </td>
                  </tr>";
                 
                }
                
             /*   if($tot_reg>0){#solo pinta los totales , esto solo si la leccion tien quiz.
             
                  $Query="SELECT cl_calificacion,no_min,no_max,no_equivalencia FROM c_calificacion WHERE 1=1 ";
                  $rs4 = EjecutaQuery($Query);
                  $tot_registros = CuentaRegistros($rs4);
                  for($i=1;$row4=RecuperaRegistro($rs4);$i++){
                    $no_min=$row4['no_min'];
                    $no_max=$row4['no_max'];
                    if(( $porcentaje_final >=$no_min)&&($porcentaje_final<=$no_max) ){
                      $grade_final=$row4['cl_calificacion'];
                    }
                  }
                 
                  echo '<tr>';
                  echo '<td colspan="5" style="text-align:right; padding-right:;10px;">'.ObtenEtiqueta(524).':  </td>';
                  echo '<td> '.$grade_final.'  </td>';
                  echo '<td> '.$porcentaje_final.' %</td>';
                  echo '</tr>';
                }*/

              }
              
              #Se realiza calculo de final promedio. y su equivalencia 
              $porcentaje_final=$total_porcentaje/$total_registros_a_dividir;
              
              
                  $Query="SELECT cl_calificacion,no_min,no_max,no_equivalencia FROM c_calificacion_sp WHERE 1=1 ";
                  $rs4 = EjecutaQuery($Query);
                  $tot_registros = CuentaRegistros($rs4);
                  for($i=1;$row4=RecuperaRegistro($rs4);$i++){
                    $no_min=$row4['no_min'];
                    $no_max=$row4['no_max'];
                    
                        if(( $porcentaje_final >=$no_min)&&($porcentaje_final<=$no_max) ){
                          $grade_final=$row4['cl_calificacion'];
                        }
              
                  }
              
              
              echo '<tr>';
              echo '<td colspan="5" style="text-align:right; padding-right:;10px;">'.ObtenEtiqueta(524).':  </td>';
              echo '<td> '.$grade_final.'  </td>';
              echo '<td> '.$porcentaje_final.' %</td>';
              echo '</tr>';
              
              
              
              ?>
              </tbody>
            </table>
          </div>

            

        
          <!--- FECHAS DE COMPLETE AND GRADUATION ---->
          <div class="row no-margin padding-10">
            
            <div class="col col-lg-12 col-md-12 col-sm-12 col-xs-12 text-align-center padding-10">
            <strong><h3>
            <?php
            echo ObtenEtiqueta(525);
            ?>
            </h3></strong>
            </div>
            <div class="col col-lg-6 col-md-12 col-sm-12 col-xs-12 text-align-center">
            <?php
            echo ObtenEtiqueta(536)."<br/>".$fe_fin_curso;
            ?>
            </div>
            <div class="col col-lg-6 col-md-12 col-sm-12 col-xs-12 text-align-center">
            <?php
            echo ObtenEtiqueta(537)."<br/>".$fe_emision
            ?>
            </div>
          </div>
        </div>
		
		 <div class="tab-pane fade"  id="relacion">
		 
		 
		 <section>          
            <div class="col col-xs-12 col-sm-5">
              <?php
              CampoTexto('ds_fname', $ds_fname, 'form-control', False, '', ObtenEtiqueta(909), "fa-user", "col-md-12", "append");
              ?>
            </div>
            <div class="col col-xs-12 col-sm-5">
              <?php
              CampoTexto('ds_lname', $ds_lname, 'form-control', False, '', ObtenEtiqueta(910), "fa-user", "col-md-12", "append");
              ?>
            </div>
          </section>
		 
		   <section>          
            <div class="col col-xs-12 col-sm-5">
			  <?php
              $Querys = "SELECT nb_parentesco,cl_parentesco FROM c_parentesco WHERE 1=1 ";
              CampoSelectBD('cl_parentesco', $Querys, $cl_parentesco, 'select2', True, $p_script='', $p_valores='', $p_seleccionar_txt = 'Select', $p_seleccionar_val =0);       
              ?>
			</div>
			
			<div class="col col-xs-12 col-sm-5">
              <?php
              CampoTexto('email_parentesco', $email_parentesco, 'form-control', False, '', ObtenEtiqueta(766), "fa-envelope", "col-md-12", "append");
              ?>
            </div>
			
			</section>
		  
		  
		  <section>          
            <div class="col col-xs-12 col-sm-5">
              &nbsp;<br /><br />
            </div>
            <div class="col col-xs-12 col-sm-5">                        
              
                &nbsp;<br /><br />
            </div>
          </section>
		 
		 
		 </div>
		
		
		
		
		
		
		
		
		
		
        <div class="tab-pane fade"  id="assigment_grade">
             

			<?php if($fg_permite_ver_historial==1){ ?> 
			 
			 
			 
                     <!--- NOMBRE DEL PROGRAMA ---->
                      <div class="row no-margin padding-10">
                        <h3><?php echo ObtenNombreCourse($fl_programa_sp); ?></h3>
                      </div>

                     <!--- DATOS DE LOS QUIZES ---->
                     <div class="row no-margin">     
                            <table id="Table1" class="table table-striped table-bordered table-hover">
                              <thead>
                                <tr>
                                  <th><?php echo ObtenEtiqueta(1605); ?> </th>
                                  <th class="text-center"><?php echo ObtenEtiqueta(1606); ?> </th>
                                  <th class="text-center"><?php echo ObtenEtiqueta(1692); ?> </th>
                                  
                                  
                                  <th class="text-center"><?php echo ObtenEtiqueta(1610); ?> </th>
                                  <th class="text-center"><?php echo ObtenEtiqueta(1611); ?> </th>
                                </tr>
                              </thead>
                              <tbody>
                          
                    


                    <?php
                            #1.verificamos cuantas lecciones existen en esete programa(CUANDO EXISTE FL_PROMEDIO QUIERE DECIR QUE YA ESTA CALIFICADA)
                   /* $Query3="SELECT A.fl_alumno,A.fl_leccion_sp,A.fl_promedio_semana,C.nb_programa,B.ds_titulo,B.no_valor_rubric,B.no_semana,D.cl_calificacion,D.no_equivalencia 
                            FROM k_entrega_semanal_sp A
                            JOIN c_leccion_sp B  ON B.fl_leccion_sp=A.fl_leccion_sp 
                            JOIN c_programa_sp C ON C.fl_programa_sp=B.fl_programa_sp
                            JOIN c_calificacion_sp D ON D.fl_calificacion=A.fl_promedio_semana
                            WHERE A.fl_alumno=$fl_usuario_sp AND C.fl_programa_sp=$fl_programa_sp AND fl_promedio_semana IS NOT NULL ORDER BY B.no_semana ASC ";
                    Se omite esta consulta y se coloca todos los programas.
					
					
					*/
					$Query3="SELECT A.fl_alumno,B.fl_leccion_sp,A.fl_promedio_semana ,C.nb_programa,B.ds_titulo,B.no_valor_rubric,B.no_semana,D.cl_calificacion,D.no_equivalencia
							FROM c_leccion_sp B
							JOIN c_programa_sp C ON B.fl_programa_sp=C.fl_programa_sp
							LEFT JOIN k_entrega_semanal_sp A ON A.fl_leccion_sp=B.fl_leccion_sp and A.fl_alumno=$fl_usuario_sp
							LEft JOIN c_calificacion_sp D ON D.fl_calificacion=A.fl_promedio_semana
							WHERE B.fl_programa_sp=$fl_programa_sp ORDER BY B.no_semana ASC ;	
					
					
					";
					  
                            $rs3 = EjecutaQuery($Query3);
                            $contador3=0;
                            $total_reg=CuentaRegistros($rs3);
							$total_registross=0;
                            for($tot3=0;$row3=RecuperaRegistro($rs3);$tot3++) {
                            $contador3 ++; 
                            $fl_leccion_sp=$row3[1];
                            $fl_promedio_semana=$row3[2];
                            $nb_leccion=$row3[4];
                            $no_valor_rubric=$row3[5];
                            $no_session=$row3[6];
                            $grade=$row3[7];
                            $porcentaje=$row3[8];
                            $cl_cal=$row3['cl_calificacion'];

                           if(empty($grade))
							$grade="<i class='fa fa-clock-o' aria-hidden='true'></i>";
						   
						   
						   
                            if($cl_cal){
                                # NOTA: EN esta seccion no aplica, se coloca el numero que saco el estudiante.
                                #Recuperamos la calificacion asignada por el teacher (sin calculos ni equivalencias.)
                                $Query2="SELECT no_calificacion FROM k_calificacion_teacher WHERE fl_alumno=$fl_usuario_sp and fl_leccion_sp=$fl_leccion_sp AND fl_programa_sp=$fl_programa_sp ";
                                $row2=RecuperaValor($Query2);
                                $no_calificacion= $row2['no_calificacion'];
                                
                                
                                #Recupermaos la fecha de utima modificacion/creacion
                                $Query3 ="SELECT fe_modificacion 
                                        FROM c_com_criterio_teacher 
                                        WHERE fl_leccion_sp=$fl_leccion_sp AND fl_alumno=$fl_usuario_sp AND fl_programa_sp=$fl_programa_sp  AND fg_com_final='1' ";
                                
                                $row3=RecuperaValor($Query3);
                                $fe_modificacion=GeneraFormatoFecha($row3[0]);
                                
                                
                            }
							if(empty($fe_modificacion))
								$fe_modificacion="<i class='fa fa-clock-o' aria-hidden='true'></i>";
							
							

								  #2. Por cada leccion es uyn registro.
								  echo "
                                  <tr>
                                    <td> ".$fe_modificacion." </td>
                                    <td class='text-center'> ".$no_session." </td>
                                    <td class='text-center'> ".$nb_leccion."  </td>
                                   
                                    <td class='text-center'> ".$grade."  </td>
                                    <td class='text-center'>"; 
									if($no_calificacion)
									echo" ".number_format($no_calificacion)."%";
									else
									echo" <i class='fa fa-clock-o' aria-hidden='true'></i>  ";	
									echo" </td>
                                  </tr>";
                            
									if($no_calificacion){
									$sum_porcentaje += $no_calificacion;
									$total_registross++;

									}
								$fe_modificacion="";
								$grade="";
								$no_calificacion="";


                            }
                            
                            $total=$sum_porcentaje/$total_registross;
                           
                           
                            #Verificamos en que rango se encuuentar y asignamos califiacion final.
                            $Query="SELECT cl_calificacion,no_min,no_max,no_equivalencia FROM c_calificacion_sp WHERE 1=1 ";
                            $rs4 = EjecutaQuery($Query);
                            $tot_registros = CuentaRegistros($rs4);
                            for($i=1;$row4=RecuperaRegistro($rs4);$i++){
                                $no_min=$row4['no_min'];
                                $no_max=$row4['no_max'];
                                if(( number_format($total) >=$no_min)&&(number_format($total)<=$no_max) ){   
                                    $grade_final=$row4['cl_calificacion'];
                                    $no_equivalente=$row4['no_equivalencia'];
                                }
                                
                                
                            }
                            
                            
                    
                    ?>

                        <tr>
                            <td></td>
                            <td></td>
                            <td class="text-right"><?php echo ObtenEtiqueta(524); ?>:</td>
                            <td class='text-center'><?php echo $grade_final; ?></td>
                            <td class='text-center'><?php echo number_format($total)."%"; ?></td>

                        </tr>


                        </tbody>
                      </table>
                    </div>
			<?php }else{ ?>
			
						<div class="row no-margin padding-10">
							<div class="col-md-12 text-center">

								<div class="alert alert-danger" >
									<strong><i class="fa fa-user-times fa-5" aria-hidden="true"></i></strong>&nbsp;<?php echo ObtenEtiqueta(1694); ?>
								</div>
							</div>

						</div>
			
			<?php } ?>



                
        </div>

		
		
		
		
		
		
		
		
		
        <!-- Enviamos la clave-->
        <input type="hidden" id="clave" name="clave" value="<?php echo $fl_usuario_sp; ?>">
      <!--</form>-->
    </div>

   </div>
 </form>
 <!-- Botones --->
  <ul class="ui-widget ui-chatbox demo-btns" style="right: 0px; display: block; padding:0px 60px 10px 0px;">
    <li>
      <a onclick="parent.location='<?php if($fl_perfil== PFL_ADMINISTRADOR) echo "index.php#site/users.php"; else echo "index.php#site/users.php"; ?>'" class="btn btn-default btn-circle btn-lg"><i class="glyphicon glyphicon-remove"></i></a>
    </li>
    <li>
      <a href="javascript:users();" class="btn btn-primary btn-circle btn-lg" id="save_info"><i class="glyphicon glyphicon-ok"></i></a>
    </li>
  </ul>

  <script>
  
  
   // changer password 
    function change_pwd(clave){
      $("#modal-empty-student").modal();
	  
       $.ajax({
            type: 'POST',
            url: 'site/pwd_frm.php',
            data: 'clave='+clave,
            async: true,
            success: function (html) {

                $('#modal-empty-student').html(html);

            }

        });
    }
    
  
  
   
 // Validaciones del alia
 function ChangeAlias(user) { 
 
		var val = document.getElementById("ds_alias").value;
        var user = user;

        if(val.length>0){
          $.ajax({
            type: "POST",
            dataType: 'json',
            url: "site/valida_alias.php",
            async: false,
            data: "ds_alias="+val+
                  "&fl_usuario="+user,        
            success: function(result){
              var error = result.resultado.fg_error;
              if(error==true && val.length>0){
				//alert('error');  
                document.getElementById("ds_alias").style.borderColor = "red";
                document.getElementById("ds_alias").style.background = "#fff0f0";  
               // $("#ds_alias_err").remove();                
               // $("#ds_alias").after("<p id='ds_alias_err' class='form-control-static text-danger'><?php echo ObtenEtiqueta(2011); ?></p>");
              }
              else{
				//alert('nohayeror');
				  
                document.getElementById("ds_alias").style.borderColor = "#739e73";
                document.getElementById("ds_alias").style.background = "#f0fff0";
                
               // $("#ds_alias_err").remove();
              }
              
            }
          });
        }
        else{
         // document.getElementById("ds_alias").style.borderColor = "red";
         // document.getElementById("ds_alias").style.background = "#fff0f0";  
        //  $("#ds_alias_err").remove();                
       //   $("#ds_alias").after("<p id='ds_alias_err' class='form-control-static text-danger'><?php echo  ObtenMensaje(ERR_REQUERIDO); ?></p>");          
        }
       
  
 } 
  
 
  
  
  
  
  
  
  
  
  
  
  
  
  
  /** Debemos agregarlo para el fucnionamiento de diversos  plugins **/
	pageSetUp();
  
  /** Incio de ocultar el campo state o mostrar el select de state **/
  var fl_pais = '<?php echo $fl_pais; ?>';
  if(fl_pais==38){
    $("#state_other").hide();    
    $("#state_canada").show();    
  }
  else{
    $("#state_other").show();    
    $("#state_canada").hide();  
  }
  
  /*** Dependiendo del pais que seleccione mostrar el campo o las provincias ****/
  $("#fl_pais").change(function(){
    var val = $(this).val();
    
    // Si es de canada que muestre el select
    if(val==38){
      $("#state_other").hide();    
      $("#state_canada").show(); 
    }
    // En caso contrario muestra el input  
    else{
      $("#state_other").show();    
      $("#state_canada").hide();  
    }
});
  
  /** Funcion para enviar los datos por serealizacion **/
  function users(){    
    var form = $('#users');
    $.ajax( {
      type: "POST",
      url: form.attr('action'),
      data: form.serialize(),
      xhrFields: {
        onprogress: function (e) {
          $("#save").modal();
        }
      },
      success: function( response ) {        
        $('#save').modal('toggle');
      }
    });    
  }
  
  /*** Validamos los campos ***/
  function validar_form(){
    var ds_nombres = $("#ds_nombres").val();
    var ds_apaterno = $("#ds_apaterno").val();
    // var fg_genero = $("#fg_genero").val();
    var fe_nacimiento = $("#fe_nacimiento").val();
    var ds_number = $("#ds_number").val();
    var ds_street = $("#ds_street").val();
    var ds_city = $("#ds_city").val();
    var ds_state = $("#ds_state").val();
    var ds_zip = $("#ds_zip").val();
    var ds_email = $("#ds_email").val();
    var ds_phone_number = $("#ds_phone_number").val();
    var fl_pais = $("#fl_pais").val();
    var valida_fecha = validarFormatoFecha(fe_nacimiento);

    var fl_grado = $("#fl_grado").val();

    var btn_disable = $('#save_info').addClass('disabled'); //se desabilita
    var ds_fname = $("#ds_fname").val();
	var ds_lname = $("#ds_lname").val();
	var email_parentesco = $("#email_parentesco").val();
	 
	
	
    /** Nombres **/
    if (ds_nombres.length == '') {
      btn_disable;
      $('#lb_ds_nombres').addClass('state-error').removeClass('state-success');
      return;
    }else{
      $('#lb_ds_nombres').removeClass('state-error').addClass('state-success');
    }
    /** Apaterno **/
    if (ds_apaterno.length == '') {
      btn_disable;
      $('#lb_ds_apaterno').addClass('state-error').removeClass('state-success');
      return;
    }else{
      $('#lb_ds_apaterno').removeClass('state-error').addClass('state-success');
    }
    /** Nacimiento **/
    if (fe_nacimiento.length == '' || !valida_fecha) {        
      btn_disable;      
      $('#lb_fe_nacimiento').addClass('state-error').removeClass('state-success');
      return;
    }
    else{
      $('#lb_fe_nacimiento').removeClass('state-error').addClass('state-success');
    }
    /** Number street **/
    if (ds_number.length == '') {      
      btn_disable;
      $('#lb_ds_number').addClass('state-error').removeClass('state-success');
      return;
    }else{      
      $('#lb_ds_number').removeClass('state-error').addClass('state-success');
    }
    /** Street **/
    if (ds_street.length == '') {
      btn_disable;
      $('#lb_ds_street').addClass('state-error').removeClass('state-success');
      return;
    }else{
      $('#lb_ds_street').removeClass('state-error').addClass('state-success');
    }
    /** City **/
    if (ds_city.length == '') {
      btn_disable;
      $('#lb_ds_city').addClass('state-error').removeClass('state-success');
      return;
    }else{
      $('#lb_ds_city').removeClass('state-error').addClass('state-success');
    }
    /** Zip **/
    if (ds_zip.length == '') {
      btn_disable;
      $('#lb_ds_zip').addClass('state-error').removeClass('state-success');
      return;
    }else{
      $('#lb_ds_zip').removeClass('state-error').addClass('state-success');
    }
    /** Email **/
    if (ds_email.length == '') {
      btn_disable;
      $('#lb_ds_email').addClass('state-error').removeClass('state-success');
      return;
    }else{
      $('#lb_ds_email').removeClass('state-error').addClass('state-success');
    }    
    /** Phone **/
    if (ds_phone_number.length == '') {
      btn_disable;
      $('#lb_ds_phone_number').addClass('state-error').removeClass('state-success');
      return;
    }else{
      $('#lb_ds_phone_number').removeClass('state-error').addClass('state-success');
    }
	
	
	
	/** responsable **/
   if (ds_fname.length == '') {
    
     $('#lb_ds_fname').addClass('state-error').removeClass('state-success');
      return;
    }else{
       $('#lb_ds_fname').removeClass('state-error').addClass('state-success');
    }
	
	
	if (ds_lname.length == '') {
      
      $('#lb_ds_lname').addClass('state-error').removeClass('state-success');
      return;
    }else{
      $('#lb_ds_lname').removeClass('state-error').addClass('state-success');
    }
	
	
	if (email_parentesco.length == '') {
      
      $('#lb_email_parentesco').addClass('state-error').removeClass('state-success');
      return;
    }else{
      $('#lb_email_parentesco').removeClass('state-error').addClass('state-success');
    }
	
	
    
    // Habilitaos el boton    
    $("#save_info").removeClass("disabled");
  }
  
  
  /** Funcion para validar la fecha **/
  function validarFormatoFecha(campo) {
    // var RegExPattern = /^\d{1,2}\-\d{1,2}\-\d{2,4}$/;
    var RegExPattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
    if ((campo.match(RegExPattern)) && (campo!='')) {
      return true;
    } else {
      return false;
    }
  }
  
  /** Cada campo ira verificando si estan llenos **/
  $("#ds_apaterno").change(function(){
    validar_form();
  });
  $("#ds_apaterno").change(function(){
    validar_form();
  });
  $("#fe_nacimiento").change(function(){
    validar_form();
  });
  $("#ds_number").change(function(){
    validar_form();
  });
  $("#ds_street").change(function(){
    validar_form();
  });
  $("#ds_city").change(function(){
    validar_form();
  });
  $("#ds_zip").change(function(){
    validar_form();
  });
  $("#ds_email").change(function(){
    validar_form();
  });
  $("#ds_phone_number").change(function(){
    validar_form();
  });
  
  $("#ds_fname").change(function(){
    validar_form();
  });
  
   $("#ds_lname").change(function(){
    validar_form();
  });
  $("#email_parentesco").change(function(){
    validar_form();
  });
  
  
  
  /** Valida la forma **/
  validar_form();
  
  /** DATATABLES PARA LOS QUIZ **/
  var pagefunction = function() {
		/* BASIC ;*/
			var responsiveHelper_dt_basic = undefined;
			var responsiveHelper_datatable_fixed_column = undefined;
			var responsiveHelper_datatable_col_reorder = undefined;
			var responsiveHelper_datatable_tabletools = undefined;
			
			var breakpointDefinition = {
				tablet : 1024,
				phone : 480
			};

			$('#tbl_quizs').dataTable({
				
			});

		/* END BASIC */
  };
  
  /** IMPORTANTES Y NECESARIAS EN DONDE SE UTILIZEN **/
	loadScript("../fame/js/plugin/datatables/jquery.dataTables.min.js", function(){
		loadScript("../fame/js/plugin/datatables/dataTables.colVis.min.js", function(){
			loadScript("../fame/js/plugin/datatables/dataTables.tableTools.min.js", function(){
				loadScript("../fame/js/plugin/datatables/dataTables.bootstrap.min.js", function(){
					loadScript("../fame/js/plugin/datatable-responsive/datatables.responsive.min.js", pagefunction)
				});
			});
		});
	});
  /** FIN CARGA DE SCRIPT PARA LAS  DATATABLES **/  
  
  
  
  </script>
