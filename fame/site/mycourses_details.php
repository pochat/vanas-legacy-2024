<?php
 # Librerias
 require("../lib/self_general.php");


 # Verifica que exista una sesion valida en el cookie y la resetea
 $fl_usuario = ValidaSesion(False,0, True);
  
 # Recibe parametros
 $clave = RecibeParametroNumerico('clave',True);
 $fg_error = RecibeParametroNumerico('fg_error'); 

 # Verifica que el usuario tenga permiso de usar esta funcion
 if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
 }
 
 # Intituto del usuario
 $fl_instituto = ObtenInstituto($fl_usuario);
 $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  
  //$clave=12;

    if(!empty($clave)) { // Actualizacion, recupera de la base de datos

      $Query  = "SELECT nb_programa, ds_duracion, ds_tipo, no_orden, no_grados, fl_template, fg_fulltime, no_creditos, nb_thumb, fg_taxes, fg_nuevo_programa, ds_programa, ds_learning, ds_metodo, ds_requerimiento, ds_course_code, fg_level, fg_obligatorio,fg_publico,no_email_desbloquear,mn_precio,ds_contenido,no_dias_trial,no_dias_pago ";
      $Query .= ",nb_programa_esp,nb_programa_fra,ds_learning_esp,ds_learning_fra,ds_metodo_esp,ds_metodo_fra,ds_requerimiento_esp,ds_requerimiento_fra,fg_compartir_curso,ds_programa_esp,ds_programa_fra ";
	  $Query .= "FROM c_programa_sp ";
      $Query .= "WHERE fl_programa_sp = $clave";
      $row = RecuperaValor($Query);
      $nb_programa = str_texto($row[0]);
      $ds_duracion = str_texto($row[1]);
      $ds_tipo = str_texto($row[2]);
      $no_orden = $row[3];
      $no_grados = $row[4];
      $fl_template = $row[5];
      $fg_fulltime = $row[6];
      $no_creditos = $row[7];
      $nb_thumb = str_texto($row[8]);
      $fg_taxes = $row[9];
      $fg_nuevo_programa = $row[10];
      $ds_programa = str_texto($row[11]);
      $ds_learning = str_texto($row[12]);
      $ds_metodo = str_texto($row[13]);
      $ds_requerimiento = str_texto($row[14]);
      $ds_course_code = str_texto($row[15]);
      $nb_lvl = str_texto($row[16]);
      $fg_obligatorio = $row[17];
      $fg_publicar=$row['fg_publico'];
      $no_email=$row['no_email_desbloquear'];
      $mn_precio=$row['mn_precio'];
      $ds_contenido_curso=str_texto($row['ds_contenido']);
      $no_dias_trial=$row['no_dias_trial'];
      $no_dias_pago=$row['no_dias_pago'];
	  $nb_programa_esp=$row['nb_programa_esp'];
	  $nb_programa_fra=$row['nb_programa_fra'];
	  $ds_learning_esp = str_texto($row['ds_learning_esp']);
	  $ds_learning_fra = str_texto($row['ds_learning_fra']);
      $ds_metodo_esp = str_texto($row['ds_metodo_esp']);
      $ds_metodo_fra = str_texto($row['ds_metodo_fra']);
      $ds_requerimiento_esp = str_texto($row['ds_requerimiento_esp']);
      $ds_requerimiento_fra = str_texto($row['ds_requerimiento_fra']);
      $fg_compartir_curso=$row['fg_compartir_curso'];
      $ds_programa_esp=$row['ds_programa_esp'];
      $ds_programa_fra=$row['ds_programa_fra'];
	  
	  
	  
      
      if(empty($no_email))
          $no_email=null;
      if(empty($mn_precio))
          $mn_precio=null;
      if(empty($no_dias_trial))
          $no_dias_trial=null;
      if(empty($no_dias_pago))
          $no_dias_pago=null;
      
      $Query  = "SELECT no_horas, no_horas_week, no_semanas, ds_credential, cl_delivery, ds_language, cl_type, no_workload, fg_board,ds_credential_esp,ds_credential_fra,ds_language_esp,ds_language_fra ";
      $Query .= "FROM k_programa_detalle_sp ";
      $Query .= "WHERE fl_programa_sp = $clave ";
      $row = RecuperaValor($Query);
      # Datos pagos y para contrato
      $no_horas = $row[0];
      $no_horas_week = $row[1];
      $no_semanas = $row[2];
      $ds_credential = $row[3];
      $cl_delivery = $row[4];
      $ds_language = $row[5];
      $cl_type = $row[6];
      $workload = $row[7];
      $fg_board = $row[8];
	  $ds_credential_esp=$row['ds_credential_esp'];
	  $ds_credential_fra=$row['ds_credential_fra'];
	  $ds_language_esp=$row['ds_language_esp'];
	  $ds_language_fra=$row['ds_language_fra'];

      # Obtenemos los programas de la clase global
      $rs_g = EjecutaQuery("SELECT fl_grado FROM k_grade_programa_sp WHERE fl_programa_sp = $clave");
      # Please initialize variables to avoid errors
        $programas_bd=NULL;
      for($i_g=0;$i_g<$row_g=RecuperaRegistro($rs_g);$i_g++){
        $programas_bd .= $row_g[0].",";
      }
      $programas_bd = explode(",", $programas_bd);  
      
      # Programas prerequisito
      $rs = EjecutaQuery("SELECT fl_programa_sp_rel FROM k_relacion_programa_sp WHERE fl_programa_sp_act = $clave AND fg_puesto = 'ANT'");
      # Please initialize variables to avoid errors
        $programas_pre=NULL;
      for($i=0;$i<$row=RecuperaRegistro($rs);$i++){
        $programas_pre .= $row[0].",";
      }
      $programas_pre = explode(",", $programas_pre);  
      
      # Programas siguientes
      $rs_s = EjecutaQuery("SELECT fl_programa_sp_rel FROM k_relacion_programa_sp WHERE fl_programa_sp_act = $clave AND fg_puesto = 'SIG'");
      # Please initialize variables to avoid errors
        $programa_sig=NULL;
      for($i_s=0;$i_s<$row_s=RecuperaRegistro($rs_s);$i_s++){
        $programa_sig .= $row_s[0].",";
      }
      $programa_sig = explode(",", $programa_sig); 

      # Obtenemos sus categorias principales
      $query = "SELECT k.fl_cat_prog_sp, c.nb_categoria FROM k_categoria_programa_sp k, c_categoria_programa_sp c WHERE k.fl_programa_sp = $clave AND k.fl_cat_prog_sp=c.fl_cat_prog_sp AND c.fg_categoria = 'CAT'";
      $result = EjecutaQuery($query);
      $i = 0;
      // while($registro = mysql_fetch_array($result)){
      //   $nb_tags[$i] = $registro[1];
      //   $i++ ;
      // }
      foreach ($result as $registro) {
        $nb_tags[$i] = $registro[1];
        $i++ ;
      }    
      if($i != 0){
        $cadena = "";
        foreach($nb_tags as $id=>$nb_tags){
          $cadena .= $nb_tags;
          if($id!=($i-1))
            $cadena .= ",";
        }
        $nb_tags = $cadena;
      }

      # Obtenemos sus categorias tipo hardware
      $query = "SELECT k.fl_cat_prog_sp, c.nb_categoria FROM k_categoria_programa_sp k, c_categoria_programa_sp c WHERE k.fl_programa_sp = $clave AND k.fl_cat_prog_sp=c.fl_cat_prog_sp AND c.fg_categoria = 'HAR'";
      $result = EjecutaQuery($query);
      $i = 0;
      // while($registro = mysql_fetch_array($result)){
      //   $nb_har[$i] = $registro[1];
      //   $i++ ;
      // }
      foreach ($result as $registro) {
        $nb_har[$i] = $registro[1];
        $i++ ;
      }    
      if($i != 0){
        $cadena = "";
        foreach($nb_har as $id=>$nb_har){
          $cadena .= $nb_har;
          if($id!=($i-1))
            $cadena .= ",";
        }
        $nb_har = $cadena;
      }

      # Obtenemos sus categorias tipo software
      $query = "SELECT k.fl_cat_prog_sp, c.nb_categoria FROM k_categoria_programa_sp k, c_categoria_programa_sp c WHERE k.fl_programa_sp = $clave AND k.fl_cat_prog_sp=c.fl_cat_prog_sp AND c.fg_categoria = 'SOF'";
      $result = EjecutaQuery($query);
      $i = 0;
      // while($registro = mysql_fetch_array($result)){
      //   $nb_sof[$i] = $registro[1];
      //   $i++ ;
      // }
      foreach ($result as $registro) {
        $nb_sof[$i] = $registro[1];
        $i++ ;
      }  
      if($i != 0){
        $cadena = "";
        foreach($nb_sof as $id=>$nb_sof){
          $cadena .= $nb_sof;
          if($id!=($i-1))
            $cadena .= ",";
        }
        $nb_sof = $cadena;
      }

	  
	
	  
	  
	  
      # Obtenemos sus categorias tipo course code
      $query = "SELECT k.fl_cat_prog_sp, c.nb_categoria FROM k_categoria_programa_sp k, c_categoria_programa_sp c WHERE k.fl_programa_sp = $clave AND k.fl_cat_prog_sp=c.fl_cat_prog_sp AND c.fg_categoria = 'CCE'";
      $result = EjecutaQuery($query);
      $i = 0;
      // while($registro = mysql_fetch_array($result)){
      //   $nb_cce[$i] = $registro[1];
      //   $i++ ;
      // }
      foreach ($result as $registro) {
        $nb_cce[$i] = $registro[1];
        $i++ ;
      }
      if($i != 0){
        $cadena = "";
        foreach($nb_cce as $id=>$nb_cce){
          $cadena .= $nb_cce;
          if($id!=($i-1))
            $cadena .= ",";
        }
        $nb_cce = $cadena;
      }

      # Obtenemos sus categorias tipo course series
      $query = "SELECT k.fl_cat_prog_sp, c.nb_categoria FROM k_categoria_programa_sp k, c_categoria_programa_sp c WHERE k.fl_programa_sp = $clave AND k.fl_cat_prog_sp=c.fl_cat_prog_sp AND c.fg_categoria = 'CSS'";
      $result = EjecutaQuery($query);
      $i = 0;
      // while($registro = mysql_fetch_array($result)){
      //   $nb_css[$i] = $registro[1];
      //   $i++ ;
      // }
      foreach ($result as $registro) {
        $nb_css[$i] = $registro[1];
        $i++ ;
      }
      if($i != 0){
        $cadena = "";
        foreach($nb_css as $id=>$nb_css){
          $cadena .= $nb_css;
          if($id!=($i-1))
            $cadena .= ",";
        }
        $nb_css = $cadena;
      }
      
      
      #Couses Code
      $rs_cou = EjecutaQuery("SELECT  fl_course_code FROM k_course_code_prog_fame C 
                                      WHERE fl_programa_sp=$clave  ");
      # Please initialize variables to avoid errors
      $courses_code_bd=NULL;
      for($i_c=0;$i_c<$row_co=RecuperaRegistro($rs_cou);$i_c++){
          $courses_code_bd .= $row_co[0].",";
      }
      $courses_code_bd = explode(",", $courses_code_bd);






      # Obtenemos sus categorias tipo software
      $query = "SELECT k.fl_cat_prog_sp, c.nb_categoria FROM k_categoria_programa_sp k, c_categoria_programa_sp c WHERE k.fl_programa_sp = $clave AND k.fl_cat_prog_sp=c.fl_cat_prog_sp AND c.fg_categoria = 'FOS'";
      $result = EjecutaQuery($query);
      $i = 0;
      // while($registro = mysql_fetch_array($result)){
      //   $nb_fos[$i] = $registro[1];
      //   $i++ ;
      // }
      foreach ($result as $registro) {
        $nb_fos[$i] = $registro[1];
        $i++ ;
      }
      if($i != 0){
        $cadena = "";
        foreach($nb_fos as $id=>$nb_fos){
          $cadena .= $nb_fos;
          if($id!=($i-1))
            $cadena .= ",";
        }
        $nb_fos = $cadena;
      }

    }else{
        
        EjecutaQuery("DELETE FROM c_programa_sp WHERE nb_programa is null ");
        EjecutaQuery("DELETE FROM k_programa_detalle_sp WHERE fl_programa_sp is null ");

        $Query="INSERT INTO c_programa_sp(nb_programa,ds_duracion,ds_tipo,no_orden,no_grados,fl_usuario_creacion)VALUES('','','','','',$fl_usuario); ";
        $clave=EjecutaInsert($Query);

        $Query  = "INSERT INTO k_programa_detalle_sp (fl_programa_sp, fg_board) ";
        $Query .= "VALUES ($clave,'1')";
        EjecutaQuery($Query);

    }
  
  
    # fixed page
    $Queryf  = "SELECT nb_pagina, ds_pagina, ds_titulo, tr_titulo, ds_contenido, tr_contenido, cl_pagina_sp ";
    $Queryf .= "FROM c_pagina_sp WHERE fl_programa_sp=$clave ";
    $rowf = RecuperaValor($Queryf);
    $nb_pagina = $rowf[0];
    $ds_pagina = $rowf[1];
    $ds_titulo = $rowf[2];
    $tr_titulo = $rowf[3];
    $ds_contenido = $rowf[4];
    $tr_contenido = $rowf[5];
    $cl_pagina = $rowf[6];
    if(empty($cl_pagina))
        $cl_pagina = 0;
  
  
  
  
  
  
  
 ?>
 <style>
 
	                   
 </style>
   
 
 
 
 
 
 
 
  <style type="text/css">
   
   
		    
			.zoomimg {position: relative; z-index: 1150; }
			.zoomimg:hover{ background-color: transparent; z-index: 1150; }
			.zoomimg span{ 
			position: absolute;
			
			padding: 5px;
			left: -50px;
			
			visibility: hidden;
			color: #000;
			width:600px;
			
			}
			.zoomimg span img{ border-width: 0; padding: 2px; width:auto; height:auto; }
			.zoomimg:hover span{ visibility: visible; top: 0;  
			
			}
	
			.modal{
				z-index: 1150 !important;
				
				
			}

			.popover {
			  max-width: 350px;
			}

			
  </style>
  
  
 
  
  
  
  
  
  
 
 
   <!-- widget content -->
            <div class="widget-body">
                <ul id="myTab1" class="nav nav-tabs bordered">
                    <li class="active" id="tab1">
                        <a href="#information_programa" data-toggle="tab" ><i class="fa fa-fw fa-lg fa-info"></i>&nbsp;<?php echo ObtenEtiqueta(2346) ?></a>
                    </li>
                    <li id="tab2">
                        <a href="#categoria" id="tabss2" data-toggle="tab" ><i class="fa fa-fw fa-lg fa-tags" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(2347) ?></a>
                    </li>
					 <li id="tab3">
                        <a href="#course_outline" id="tabss3" data-toggle="tab" ><i class="fa fa-fw fa-lg fa-file-text-o" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(2348) ?></a>
                    </li>
					 <li id="tab4" onclick="MuestraTablas();">
                        <a href="#student_library" id="tabss4" data-toggle="tab" ><i class="fa fa-fw fa-lg fa-folder-open" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(2349) ?></a>
                    </li>

                     <li id="tab5">
                        <a href="#configuration" id="tabss5" data-toggle="tab" ><i class="fa fa-fw fa-lg fa-cogs" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(2341) ?></a>
                    </li>

 
                </ul>
				
				
				<div id="myTabContent1" class="tab-content padding-10 "><!--class='no-border'--->
                    <div class="tab-pane fade in active" id="information_programa">
							<?php include "clibrary_frm_program_information.php"; ?>					
						<div class="row">
							<div class="col-md-1">
							  &nbsp;
							</div>
						    <div class="col-md-10">
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-lg-6 col-md-6">							      
									</div>
									<div class="col-xs-12 col-sm-12 col-lg-6 col-md-6 text-center">     
										<div class="smart-form">										
										<br><br>											
												<ul class="ui-widget ui-chatbox demo-btns" style="padding-bottom: 60px;padding-left: 295px;">													
													<li>
														<a href="javascript:void(0);" onclick="Cancel();"; class="btn btn-default btn-circle btn-lg"><i class="fa fa-times"></i></a>
													</li>													
													<li>
														<a href="javascript:void(0);" onclick="GuardarTab1();"  class="btn btn-primary btn-circle btn-lg"><i class="fa fa-check"></i></a>
													</li>
												</ul>																	   
										</div> 
									</div>
								</div>							
							</div>
							<div class="col-md-1">&nbsp;</div>
						</div>
						
                    </div>
					
					<!----INCIIA CATEGORIA---->
                    <div class="tab-pane fade in" id="categoria">
                       
                        <div class="row">
							<div class="col-md-1">
							  &nbsp;
							</div>
                            <div class="col-md-10">
								<br />
                        
						
						
						 <!-- Categorias -->
                    <div class="row">
                      <div class="col-lg-1"></div>	
						<input type="hidden" id="fl_programa_nuevo_creado" name="fl_programa_nuevo_creado" value="<?php echo $clave ?>">
						
                      <?php
                      $query = "SELECT fl_cat_prog_sp, nb_categoria FROM c_categoria_programa_sp WHERE fg_categoria = 'FOS' ";
                      $result = EjecutaQuery($query);
                      $i = 0;
                      // while($registro = mysql_fetch_array($result)){
                      //     $tit_col[$i] = $registro[1];
                      //     $i++ ;
                      // }
                      foreach ($result as $registro) {
                        $tit_col[$i] = $registro[1];
                          $i++ ;
                      }
                      ?>
                      <script type="text/javascript">
                          var arrayJSFos=<?php echo json_encode($tit_col);?>;
                          $(function(){
                              var sampleTagsFos = arrayJSFos;
                              $('#nb_fos_ul').tagit({
                                  availableTags: sampleTagsFos,
                                  singleField: true,
                                  singleFieldNode: $('#nb_fos')
                              });
                          });
                      </script>

                      <div class="col-xs-12 col-sm-12">
                        <div class="form-group">
                          <label class="col-sm-2 control-label"><strong><div id="lbl_fos">* <?php echo ObtenEtiqueta(1306); ?></div></strong></label>
                          <div class="col-sm-10">
                            <input type="hidden" name="nb_fos" id="nb_fos" value="<?php echo $nb_fos; ?>">
                            <ul id="nb_fos_ul" ></ul>
                            <div class="note hidden" style="color:#A90329;" id="err_fos"><?php echo ObtenEtiqueta(2350);?></div>
                            <div class="note">
                              <strong></strong><i><?php echo ObtenEtiqueta(1324); ?></i>
                            </div>
                          </div>
                        </div>
                      </div>
				</div>



                                          
                    <!-- Categorias -->
                    <div class="row">
                      <div class="col-lg-1"></div>
                      <?php
                      $query = "SELECT fl_cat_prog_sp, nb_categoria FROM c_categoria_programa_sp WHERE fg_categoria = 'CAT' ";
                      $result = EjecutaQuery($query);
                      $i = 0;
                      // while($registro = mysql_fetch_array($result)){
                      //     $tit_col[$i] = $registro[1];
                      //     $i++ ;
                      // }
                      foreach ($result as $registro) {
                        $tit_col[$i] = $registro[1];
                          $i++ ;
                      }
                      ?>
                      <script type="text/javascript">
                          var arrayJSCat=<?php echo json_encode($tit_col);?>;
                          $(function(){
                              var sampleTagsCat = arrayJSCat;
                              $('#singleFieldTags').tagit({
                                  availableTags: sampleTagsCat,
                                  singleField: true,
                                  singleFieldNode: $('#nb_tags')
                              });
                          });
                      </script>

                      <div class="col-xs-12 col-sm-12">
                        <div class="form-group">
                          <label class="col-sm-2 control-label"><strong><div id="lbl_tags">* <?php echo ObtenEtiqueta(1307); ?></div></strong></label>
                          <div class="col-sm-10">
                            <input type="hidden" name="nb_tags" id="nb_tags" value="<?php echo $nb_tags; ?>">
                            <ul id="singleFieldTags" ></ul>
                            <div id="err_tags"  class="note hidden" style="color:#A90329;"><?php echo ObtenEtiqueta(2350);?></div>
                            <div class="note">
                              <strong></strong><i><?php echo ObtenEtiqueta(1315); ?></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div> 
                  
                    <div class="row">                       
                          <div class="col col-xs-12 col-sm-12"> 

							<!--scholl level-->		
												<div class="form-group">
													<label class="col-sm-2 control-label"><strong>* <?php echo ObtenEtiqueta(1308); ?></strong></label>
													<div class="col-sm-10">
													
													<select multiple style="width:100%" class="select2 grados" id="fl_programa_nuevo" name="fl_programa_nuevo">
													
													<?php
														$Query  = "SELECT nb_grado, fl_grado FROM k_grado_fame "; 
														$rsl = EjecutaQuery($Query);
														while($row = RecuperaRegistro($rsl)) {
															echo "<option value=\"$row[1]\"";
															
															#Recuperamos los registros que tiene seleccionado el input 
															$Quer="SELECT fl_grado FROM k_grade_programa_sp WHERE fl_programa_sp = $clave AND fl_grado= $row[1] ";
															$rowe=RecuperaValor($Quer);
															$fl_grado_act=!empty($rowe[0])?$rowe[0]:NULL;
															
															      if($fl_grado_act == $row[1])
																  echo " selected";
															
															# Determina si se debe elegir un valor por traduccion
															$etq_campo = DecodificaEscogeIdiomaBD($row[0]);
															echo "> $etq_campo </option>\n";
														}
													
													?>
						
													</select>
													
														<div id="err_fl_programa_nuevo"  class="note hidden" style="color:#A90329;"><?php echo ObtenEtiqueta(2350);?></div>
														<div class="note">
														  <strong></strong><i><?php echo ObtenEtiqueta(1319); ?></i>
														</div>
													</div>
												</div>	

                          </div>
                    </div>



                <!-- Hardware -->   
                    <div class="row">
                      <div class="col-lg-1"></div>
                      <?php
                      $query = "SELECT fl_cat_prog_sp, nb_categoria FROM c_categoria_programa_sp WHERE fg_categoria = 'HAR' ";
                      $result = EjecutaQuery($query);
                      $i = 0;
                      // while($registro = mysql_fetch_array($result)){
                      //     $tit_col[$i] = $registro[1];
                      //     $i++ ;
                      // }
                      foreach ($result as $registro) {
                        $tit_col[$i] = $registro[1];
                          $i++ ;
                      }
                      ?>
                      <script type="text/javascript">
                          var arrayJSHAR=<?php echo json_encode($tit_col);?>;
                          $(function(){
                              var sampleTagsHar = arrayJSHAR;
                              $('#hardware_ul').tagit({
                                  availableTags: sampleTagsHar,
                                  singleField: true,
                                  singleFieldNode: $('#nb_har')
                              });
                          });
                      </script>

                      <div class="col-xs-12 col-sm-12">
                        <div class="form-group">
                          <label class="col-sm-2 control-label"><strong><div id="lbl_har">* <?php echo ObtenEtiqueta(1309); ?></div></strong></label>
                          <div class="col-sm-10">
                            <input type="hidden" name="nb_har" id="nb_har" value="<?php echo $nb_har; ?>">
                            <ul id="hardware_ul" ></ul>
                            <div id="err_har"></div>
                            <div class="note">
                              <i><strong></strong><?php echo ObtenEtiqueta(1320); ?></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div> 
                  


                    
					
                    <!-- Software -->
					
					
                    <div class="row">
                      <div class="col-lg-1"></div>
                      <?php
                      $query = "SELECT fl_cat_prog_sp, nb_categoria FROM c_categoria_programa_sp WHERE fg_categoria = 'SOF' ";
                      $result = EjecutaQuery($query);
                      $i = 0;
                      // while($registro = mysql_fetch_array($result)){
                      //     $tit_col[$i] = $registro[1];
                      //     $i++ ;
                      // }
                      foreach ($result as $registro) {
                        $tit_col[$i] = $registro[1];
                          $i++ ;
                      }
                      ?>
                      <script type="text/javascript">
                          var arrayJSSof=<?php echo json_encode($tit_col);?>;
                          $(function(){
                              var sampleTagsSof = arrayJSSof;
                              $('#software_ul').tagit({
                                  availableTags: sampleTagsSof,
                                  singleField: true,
                                  singleFieldNode: $('#nb_sof')
                              });
                          });
                      </script>

                      <div class="col-xs-12 col-sm-12">
                        <div class="form-group">
                          <label class="col-sm-2 control-label"><strong><div id="lbl_sof">* <?php echo ObtenEtiqueta(1310); ?></div></strong></label>
                          <div class="col-sm-10">
                            <input type="hidden" name="nb_sof" id="nb_sof" value="<?php echo $nb_sof; ?>">
                            <ul id="software_ul" ></ul>
                            <div id="err_sof" class="note hidden" style="color:#A90329;"><?php echo ObtenEtiqueta(2350);?></div>
							
                            <div class="note">
                              <i><strong></strong><?php echo ObtenEtiqueta(1316); ?></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div> 
                    

                   <div class="row">
                                    <div class="col-md-12">
                                            <div class="form-group">
													<label class="col col-sm-2"><b>* <?php echo ObtenEtiqueta(1311);?></b></label>
                                                    <div class="col col-sm-10">
													
													<?php 
													$p_opc = array(ObtenEtiqueta(1317), ObtenEtiqueta(1321), ObtenEtiqueta(1322));
													$p_val = array('LVB', 'LVI', 'LVA');
													
													?>
														
														<div class="form-group">
														
															<select style="width:100%" class="select2" id="nb_lvl" name="nb_lvl">
																
																<?php 
																
																$totl = count($p_opc);
																
																
																	//echo "<option value=0>".ObtenEtiqueta(70)."</option>\n";
																for($i = 0; $i < $totl; $i++) {
																	echo "<option value=\"$p_val[$i]\"";
																	if($nb_lvl == $p_val[$i])
																		echo " selected";
																	echo ">$p_opc[$i]</option>\n";
																}
																
																
																?>
																
															</select>
														
															<div class="note">
															  <i><strong></strong><?php echo ObtenEtiqueta(1327); ?></i>
															</div>
														</div>
													</div>
                                             </div>
                                    </div>
                   </div>



                  <div class="row">     
                      <div class="col-xs-12 col-sm-12">
						  <div id="div_no_semana2" class="form-group ">
							<label class="col-sm-2 control-label">
							  <div id="lbl_course_code"><strong>* <?php echo ObtenEtiqueta(1312); ?></strong></div>
							</label>
							<div class="col-sm-10" > 
							  <div class="smart-form"id="input_ds_course_code" style="margin-left: 7px;">	
								<label class="input" id="label_input_ds_course_code">
								  <input class="form-control" id="ds_course_code" name="ds_course_code" value="<?php echo $ds_course_code; ?>" maxlength="25" size="12" type="text">
								  <?php
								  if(!empty($ds_course_code_err))
									  echo "<span class='help-block'><i class='fa fa-warning'></i>".ObtenMensaje($ds_course_code_err)."</span>";
                                  ?>
								</label>
                                <div id="err_cours_code" class="note hidden" style="color:#A90329;"><?php echo ObtenEtiqueta(2350);?></div>
								<div id="err_existe_ds_course_code" class="note hidden" style="color:#A90329;"><?php echo ObtenEtiqueta(2351);?></div>
								<div class="note">
								  <i><strong></strong><?php echo ObtenEtiqueta(1318); ?></i>
								</div>
							  </div>
							</div>  
						  </div>
                      </div>
                  </div>
		        
					  
                    <!-- Course Prerequisites -->
                    <div class="row">
                      
                      <div class="col col-xs-12 col-sm-12">
								<div class="form-group">
									<label class="col-sm-2 control-label"><strong><?php echo ObtenEtiqueta(1313); ?></strong></label>
									<div class="col-sm-10">
										<select multiple style="width:100%" class="select2 grados" id="fl_programa_pre" name="fl_programa_pre">
										
										<?php
                                        $Query  = "SELECT CONCAT(a.nb_programa, ' (code: ', a.ds_course_code ,')','',case when a.fl_instituto IS NULL then ' 'ELSE CONCAT(' - ".ObtenEtiqueta(2626)." ',b.ds_instituto) END ) as programa, a.fl_programa_sp FROM c_programa_sp a LEFT JOIN c_instituto b ON b.fl_instituto=a.fl_instituto WHERE a.nb_programa <>'' ORDER BY a.nb_programa ";                                  

											$rsl2 = EjecutaQuery($Query);
											while($row2 = RecuperaRegistro($rsl2)) {
												echo "<option value=\"$row2[1]\"";
												
												#Recuperamos los registros que tiene seleccionado el input 
												$Quer="SELECT fl_programa_sp_rel FROM k_relacion_programa_sp WHERE fl_programa_sp_act = $clave AND fg_puesto = 'ANT' AND fl_programa_sp_rel=$row2[1] ";
												$rowe=RecuperaValor($Quer);
												$fl_grado_act=!empty($rowe[0])?$rowe[0]:NULL;
												
													  if($fl_grado_act == $row2[1])
													  echo " selected";
												
												# Determina si se debe elegir un valor por traduccion
												$etq_campo = DecodificaEscogeIdiomaBD($row2[0]);
												echo "> $etq_campo </option>\n";
											}
										
										?>
			
										</select>
										<div class="note">
											<strong></strong><i><?php echo ObtenEtiqueta(1325); ?></i>
										</div>
									</div>
								</div>												 
                        </div>
                    </div>
                    

					<div class="row">
						<div class="col-xs-12 col-sm-12">
						
									<div class="form-group">
										<label class="col-sm-2 control-label"><strong><?php echo ObtenEtiqueta(1314); ?></strong></label>
										<div class="col-sm-10">
											<select multiple style="width:100%" class="select2 " id="fl_programa_sig" name="fl_programa_sig">
											
											<?php
                                            $Query5  = "SELECT CONCAT(a.nb_programa, ' (code: ', a.ds_course_code ,') - ".ObtenEtiqueta(2626)." ',(SELECT ds_instituto FROM c_instituto v WHERE v.fl_instituto=4),' ',case when a.fl_instituto IS NULL then ' ' ELSE CONCAT(' - ".ObtenEtiqueta(2626)." ',b.ds_instituto) END ) as programa, a.fl_programa_sp FROM c_programa_sp a LEFT JOIN c_instituto b ON a.fl_instituto=b.fl_instituto WHERE  a.nb_programa<>'' AND (a.fl_instituto=$fl_instituto OR a.fl_instituto IS NULL)   ORDER BY a.nb_programa  ";                                  

												$rsl3 = EjecutaQuery($Query5);
												while($row3 = RecuperaRegistro($rsl3)) {
													echo "<option value=\"$row3[1]\"";
													
													#Recuperamos los registros que tiene seleccionado el input 
													$Quer="SELECT fl_programa_sp_rel FROM k_relacion_programa_sp WHERE fl_programa_sp_act = $clave AND fg_puesto = 'SIG' AND fl_programa_sp_rel=$row3[1] ";
													$rowe=RecuperaValor($Quer);
													$fl_programa_sp_relm=!empty($rowe[0])?$rowe[0]:NULL;
													
														  if($fl_programa_sp_relm == $row3[1])
														  echo " selected";
													
													# Determina si se debe elegir un valor por traduccion
													$etq_campo = DecodificaEscogeIdiomaBD($row3[0]);
													echo "> $etq_campo </option>\n";
												}
											
											?>
				
											</select>
											<div class="note">
												<strong></strong><i><?php echo ObtenEtiqueta(1326); ?></i>
											</div>
										</div>
									</div>	
						</div>
					</div>
						


					
					<div class="row">
						<div class="col-xs-12 col-sm-12">
									<div class="form-group ">
										<label class="col-sm-2 control-label"><strong><?php echo ObtenEtiqueta(2056); ?></strong></label>
										<div class="col-sm-10">
											<select multiple style="width:100%" class="select2 " id="fl_course_code" name="fl_course_code">
											
											<?php
												$Query6  = "
															SELECT 	CASE WHEN C.fl_instituto IS NULL THEN 	CONCAT(C.nb_course_code,' (code: ',C.cl_course_code,') - ',P.ds_pais,' ,',E.ds_provincia) 
                                                            ELSE CONCAT (C.nb_course_code,'(code: ',C.cl_course_code,') - ',P.ds_pais,' - ".ObtenEtiqueta(2626)." ',F.ds_instituto)    END AS nb_course ,C.fl_course_code  
															FROM c_course_code C
															JOIN c_pais P ON P.fl_pais=C.fl_pais 
															left JOIN k_provincias E ON E.fl_provincia=C.fl_estado 
															left JOIN c_instituto F ON F.fl_instituto= C.fl_instituto 
																WHERE C.fl_instituto IS NULL or C.fl_instituto=$fl_instituto 
                                                            ORDER BY nb_course asc

												";                                  

												$rsl4 = EjecutaQuery($Query6);
												while($row4 = RecuperaRegistro($rsl4)) {
													echo "<option value=\"$row4[1]\"";
													
													#Recuperamos los registros que tiene seleccionado el input 
													$Quer="SELECT  fl_course_code FROM k_course_code_prog_fame C 
															WHERE fl_programa_sp=$clave AND fl_course_code=$row4[1] ";
													$rowx=RecuperaValor($Quer);
													$fl_courses_c=!empty($rowx[0])?$rowx[0]:NULL;
													
														  if($fl_courses_c == $row4[1])
														  echo " selected";
													
													# Determina si se debe elegir un valor por traduccion
													$etq_campo = DecodificaEscogeIdiomaBD($row4[0]);
													echo "> $etq_campo </option>\n";
												}
											
											?>
				
											</select>
											<div class="note">
												<strong></strong><i><?php echo ObtenEtiqueta(2057); ?></i>
											</div>
										</div>
									</div>

						
						</div>
					</div>




				
					<div class="row">
                            <div class="col-xs-2">&nbsp;</div>

						    <div class="col-xs-10 col-sm-10">
						            <br />
						            <?php  
                                    FAMETinyMCE('ds_contenido_curso',$ds_contenido_curso);
                        
                                    ?>
						    </div>
                            <div class="col-xs-2">&nbsp;</div>
					</div>	

					<div class="row">
					
						 <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6 text-center"> 
							&nbsp;&nbsp;
					     </div>
					
						 <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6 text-center">   

									<div class="smart-form">
									
									<br><br>
											
											<ul class="ui-widget ui-chatbox demo-btns" style="padding-bottom: 60px;padding-left: 295px;">
												
												<li>
													<a href="javascript:void(0);" onclick="Cancel();"; class="btn btn-default btn-circle btn-lg"><i class="fa fa-times"></i></a>
												</li>
												
												<li>
													<a href="javascript:void(0);" onclick="GuardarTab2();"  class="btn btn-primary btn-circle btn-lg"><i class="fa fa-check"></i></a>
												</li>
											</ul>
						
									   
									</div> 


						</div>
					</div>


                        </div>
                        <div class="col-md-1">
							&nbsp;
						</div>
                    </div>


                    </div>
                    <!----FINALIZA CATEGORIA---->



                    <!----INCIIA COURSE OULINE---->
					 <div class="tab-pane fade in" id="course_outline">                       
						<div class="widget-body">
							<!-- Outline Tags for language starts here -->
							<ul id="myTab4" class="nav nav-tabs bordered">
							  <li class="active">
								<a href="#s1outlineLang" data-toggle="tab" aria-expanded="true">English</a>
							  </li>
							  <li class="">
								<a href="#s2outlineLang" data-toggle="tab" aria-expanded="true">Spanish</a>
							  </li>
							  <li class="">
								<a href="#s3outlineLang" data-toggle="tab" aria-expanded="true">French</a>
							  </li>
							</ul>


							<div id="myTabContentLang1" class="tab-content padding-10">


								<!-- Start row for English -->
								<div class="tab-pane fade in active" id="s1outlineLang">							 
									

                                    <div class="row">
                                        <div class="col-md-3">
											 <div class="bs-example">
												 <dl>
													<dt>* <?php echo ObtenEtiqueta(1298); ?></dt>
													<dd><span style="color:#9aa7af; font-style: italic;"><?php echo ObtenEtiqueta(1299); ?></span></dd>
												 </dl>
											 </div>
										 </div>
                                        <div class="col-md-9">
											 <?php  
											 FAMETinyMCE('ds_programa',$ds_programa);                                 
                                             ?>
											 <br>
											<div id="err_ds_programa"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>
											 
										 </div>


                                    </div>
                                    <br />
                                    <div class="row">
										 <div class="col-md-3">
											 <div class="bs-example">
												 <dl>
													<dt>* <?php echo ObtenEtiqueta(1300); ?></dt>
													<dd><span style="color:#9aa7af; font-style: italic;"><?php echo ObtenEtiqueta(1301); ?></span></dd>
												 </dl>
											 </div>
										 </div>
										 <div class="col-md-9">
											 <?php  
											 FAMETinyMCE('ds_learning',$ds_learning);                                 
											 ?>
											 <br>
											<div id="err_ds_learning"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>
											 
										 </div>
									 </div>
									 <br>
									 <div class="row">
										 <div class="col-md-3">
												<div class="bs-example">
												  <dl>
													<dt>* <?php echo ObtenEtiqueta(1302); ?></dt>
													<dd><span style="color:#9aa7af; font-style: italic;">
													<?php echo ObtenEtiqueta(1303); ?>
													</span></dd>
												  </dl>
												</div>
										 </div>
										 <div class="col-md-9">
											 <?php  
											 FAMETinyMCE('ds_metodo',$ds_metodo);                                 
											 ?>
											 <br>
											 <div id="err_ds_metodo"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>
											 
											 
										 </div>
									 </div>

									<br>
									 <div class="row">
										 <div class="col-md-3">
												<div class="bs-example">
												  <dl>
													<dt>* <?php echo ObtenEtiqueta(1304); ?></dt>
													<dd><span style="color:#9aa7af; font-style: italic;">
													<?php echo ObtenEtiqueta(1305); ?>
													</span></dd>
												  </dl>
												</div>
										 </div>
										 <div class="col-md-9">
											 <?php  
											 FAMETinyMCE('ds_requerimiento',$ds_requerimiento);                                 
											 ?>
											 <br>
											 <div id="err_ds_requerimiento"  class="alert alert-danger text-center hidden" style="color:#ffff;"><?php echo ObtenEtiqueta(2350);?></div>										 
										 </div>
									 </div>


									
								</div>	
									
								<!-- Start row for Español -->
								<div class="tab-pane fade in " id="s2outlineLang">
							 
                                     <div class="row">
                                        <div class="col-md-3">
											 <div class="bs-example">
												 <dl>
													<dt>* <?php echo ObtenEtiqueta(1298); ?></dt>
													<dd><span style="color:#9aa7af; font-style: italic;"><?php echo ObtenEtiqueta(1299); ?></span></dd>
												 </dl>
											 </div>
										 </div>
                                        <div class="col-md-9">
											 <?php  
											 FAMETinyMCE('ds_programa_esp',$ds_programa_esp);                                 
                                             ?>
											 <br>
											<div id="ds_programa_esp_err"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>
											 
										 </div>


                                     </div>






									 <div class="row">
										 <div class="col-md-3">
											 <div class="bs-example">
												 <dl>
													<dt>* <?php echo ObtenEtiqueta(1300); ?></dt>
													<dd><span style="color:#9aa7af; font-style: italic;"><?php echo ObtenEtiqueta(1301); ?></span></dd>
												 </dl>
											 </div>
										 </div>
										 <div class="col-md-9">
											 <?php  
											 FAMETinyMCE('ds_learning_esp',$ds_learning_esp);                                 
											 ?>
											 <br>
											<div id="err_ds_learning_esp"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>
											 
										 </div>
									 </div>
									 <br>
									 <div class="row">
										 <div class="col-md-3">
												<div class="bs-example">
												  <dl>
													<dt>* <?php echo ObtenEtiqueta(1302); ?></dt>
													<dd><span style="color:#9aa7af; font-style: italic;">
													<?php echo ObtenEtiqueta(1303); ?>
													</span></dd>
												  </dl>
												</div>
										 </div>
										 <div class="col-md-9">
											 <?php  
											 FAMETinyMCE('ds_metodo_esp',$ds_metodo_esp);                                 
											 ?>
											 <br>
											 <div id="err_ds_metodo_esp"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>
											 
											 
										 </div>
									 </div>

									<br>
									 <div class="row">
										 <div class="col-md-3">
												<div class="bs-example">
												  <dl>
													<dt>* <?php echo ObtenEtiqueta(1304); ?></dt>
													<dd><span style="color:#9aa7af; font-style: italic;">
													<?php echo ObtenEtiqueta(1305); ?>
													</span></dd>
												  </dl>
												</div>
										 </div>
										 <div class="col-md-9">
											 <?php  
											 FAMETinyMCE('ds_requerimiento_esp',$ds_requerimiento_esp);                                 
											 ?>
											 <br>
											 <div id="err_ds_requerimiento_esp"  class="alert alert-danger text-center hidden" style="color:#ffff;"><?php echo ObtenEtiqueta(2350);?></div>										 
										 </div>
					    </div>



								</div>
								
								<!-- Start row for Frances -->
								<div class="tab-pane fade in" id="s3outlineLang">

                                     
                                     <div class="row">
                                        <div class="col-md-3">
											 <div class="bs-example">
												 <dl>
													<dt>* <?php echo ObtenEtiqueta(1298); ?></dt>
													<dd><span style="color:#9aa7af; font-style: italic;"><?php echo ObtenEtiqueta(1299); ?></span></dd>
												 </dl>
											 </div>
										 </div>
                                        <div class="col-md-9">
											 <?php  
											 FAMETinyMCE('ds_programa_fra',$ds_programa_fra);                                 
                                             ?>
											 <br>
											<div id="ds_programa_fra_err"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>
											 
										 </div>


                                     </div>




									 <div class="row">
										 <div class="col-md-3">
											 <div class="bs-example">
												 <dl>
													<dt>* <?php echo ObtenEtiqueta(1300); ?></dt>
													<dd><span style="color:#9aa7af; font-style: italic;"><?php echo ObtenEtiqueta(1301); ?></span></dd>
												 </dl>
											 </div>
										 </div>
										 <div class="col-md-9">
											 <?php  
											 FAMETinyMCE('ds_learning_fra',$ds_learning_fra);                                 
											 ?>
											 <br>
											<div id="err_ds_learning_fra"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>
											 
										 </div>
									 </div>
									 <br>
									 <div class="row">
										 <div class="col-md-3">
												<div class="bs-example">
												  <dl>
													<dt>* <?php echo ObtenEtiqueta(1302); ?></dt>
													<dd><span style="color:#9aa7af; font-style: italic;">
													<?php echo ObtenEtiqueta(1303); ?>
													</span></dd>
												  </dl>
												</div>
										 </div>
										 <div class="col-md-9">
											 <?php  
											 FAMETinyMCE('ds_metodo_fra',$ds_metodo_fra);                                 
											 ?>
											 <br>
											 <div id="err_ds_metodo_fra"  class="alert alert-danger text-center hidden" style="color:#fff;"><?php echo ObtenEtiqueta(2350);?></div>
											 
											 
										 </div>
									 </div>

									<br>
									 <div class="row">
										 <div class="col-md-3">
												<div class="bs-example">
												  <dl>
													<dt>* <?php echo ObtenEtiqueta(1304); ?></dt>
													<dd><span style="color:#9aa7af; font-style: italic;">
													<?php echo ObtenEtiqueta(1305); ?>
													</span></dd>
												  </dl>
												</div>
										 </div>
										 <div class="col-md-9">
											 <?php  
											 FAMETinyMCE('ds_requerimiento_fra',$ds_requerimiento_fra);                                 
											 ?>
											 <br>
											 <div id="err_ds_requerimiento_fra"  class="alert alert-danger text-center hidden" style="color:#ffff;"><?php echo ObtenEtiqueta(2350);?></div>										 
										 </div>
									 </div>






								</div>

                            

                                <div class="row">								
										 <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6 text-center"> 
										   &nbsp;&nbsp;
										 </div>								
										 <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6 text-center">  
								               <div class="smart-form">
												<br><br>										
														<ul class="ui-widget ui-chatbox demo-btns" style="padding-bottom: 60px;padding-left: 295px;">
															
															<li>
																<a href="javascript:void(0);" onclick="Cancel();"; class="btn btn-default btn-circle btn-lg"><i class="fa fa-times"></i></a>
															</li>
															
															<li>
																<a href="javascript:void(0);" onclick="GuardarTab3();"  class="btn btn-primary btn-circle btn-lg"><i class="fa fa-check"></i></a>
															</li>
														</ul>								   
												</div> 
										  </div>
									</div>



							</div>
						</div>


                    </div>
                    <!----FINALIZA COURSE OULINE---->


                    <!----INCIIA STUDENT LIBRARY---->
					<div class="tab-pane fade in" id="student_library">
						 <div class="row">
                                 <div class="col-xs-12 col-sm-12 col-lg-12 col-md-12 text-center">     																		
									<div class="widget-body">
										  <ul id="myTab2" class="nav nav-tabs bordered">
											<li class="active" id="muetra_atatable" onclick="MuestraTablas();">
											  <a href="#s1fame" data-toggle="tab" aria-expanded="true"><?php echo ObtenEtiqueta(2035); ?></a>
											</li>
											<li class="" >
											  <a href="#s2fame" onclick="Videos();" data-toggle="tab" aria-expanded="true"><?php echo ObtenEtiqueta(2036); ?></a>
											</li>
										  </ul>
										  <div id="myTabContent2" class="tab-content padding-10">
											
											<!--inicia tab1-->
											<div class="tab-pane fade in active" id="s1fame">							
														<div class="row">
															<div class="col-xs-12 col-sm-12 col-lg-4 col-md-4">
															   						
																<input type="hidden" id="cl_pagina_creada" name="cl_pagina_creada"  value="<?php echo $cl_pagina;?>" />
																<div class="smart-form">
																	<!---FAMEInputText(ObtenEtiqueta(1983),'nb_pagina',$nb_pagina,False);--->
                                                                    <input type="hidden" name="nb_pagina" id="nb_pagina" value="<?php echo $nb_pagina;?>" /> 
																</div> 														
															</div>
															<div class="col-xs-12 col-sm-12 col-lg-4 col-md-4 ">  
																<div class="smart-form hidden">
																	<?php FAMEInputText(ObtenEtiqueta(1984),'ds_pagina',$ds_pagina,False); ?>
																</div> 
																
															</div>
															<div class="col-xs-12 col-sm-12 col-lg-4 col-md-4 ">
																<div class="smart-form hidden">
																	<?php FAMEInputText(ObtenEtiqueta(1985),'ds_titulo',$ds_titulo,False); ?>
																</div>
															</div>
														</div>	
														<br>
														<div class="row">					
															<div class="col-xs-12 col-sm-12 col-lg-12 col-md-12">  														
																<div class="widget-body">																  
																  <div id="myTabContent2" class="tab-content padding-10">
																	
																		<!----tabla de estudent library---->														
																			<!-- LISTADO PARA LOS USUARIOS DEL ADMINISTRADOR ES DECIR TEACHERS Y STUDENTS -->
																			  <div class="row" style="padding:5px;">
																			  
                                                                              <style>
                                                                                  .jarviswidget > header {
                                                                                      height: 45px;
                                                                                  }
																				  
																				  .zoomimg span img {
																					border-width: 0;
																					padding: 2px;
																					width: auto;
																					height: auto;
																					
																				  }
																				  

                                                                              </style>

																			  <?php
																			  
																			   SectionIni();
																					# Valores para el boton de actions
																				      ArticleIni("col-xs-12 col-sm-12 col-md-12 col-lg-12", "gabriel", "fa-table", 'Student Library', true, true, false, false, false, ObtenEtiqueta(1074), "default", (isset($opt_btn)?$opt_btn:NULL), (isset($val_btn)?$val_btn:NULL), (isset($desc_btn)?$desc_btn:NULL),"","1");
																					  # Muestra Inicio de la tabla
																					  $titulos = array(ObtenEtiqueta(2363), ObtenEtiqueta(2360),ObtenEtiqueta(2361),'',ObtenEtiqueta(2362),'');
																					  MuestraTablaIni("tbl_users", "display projects-table table table-striped table-bordered table-hover", "100%", $titulos,false);          
																					  # Muestra Fin de la tabla
																					  MuestraTablaFin(false);
																					  # Campos para el total de registros
																					  CampoOculto('tot_reg', (isset($tot_reg)?$tot_reg:NULL));
																					  # Muestra el modal para las acciones
																					  MuestraModal("Actions"); 
																					ArticleFin();
																				  SectionFin();
																			  
																			  
																			  
                                                                              ?>
																				
																				

																				<script type="text/javascript">
																				
																				 function MuestraTablas(){
																					 $("#gabriel").removeAttr("style");
																					 
																				 }
																				
																				
																				  /* Debemos agregarlo para el fucnionamiento de diversos  plugins*/
																				pageSetUp();
																				
																				// pagefunction
																			  /** INICIO DE SCRIPT PARA DATATABLE **/
																				var pagefunction = function() {
																					/* Formatting function for row details - modify as you need */
																							function format ( d ) {
																						// `d` is the original data object for the row
																						return '<table cellpadding="5" cellspacing="0" border="0" class="table table-hover table-condensed" width="100%">'+
																						d.gender
																				
																						+ '</table>';
																					}

																					// clears the variable if left blank
																					var table = $('#tbl_users').on( 'processing.dt', function ( e, settings, processing ) {
																					$('#datatable_fixed_column_processing').css( 'display', processing ? 'block' : 'none' );        
																					$("#vanas_loader").show();
																					if(processing == false)
																					  $("vanas_loader").hide();
																					}).DataTable( {
																						//"ajax": "Querys/my_student_library.php",
																						
																					    "ajax": {
																						"url": "Querys/my_student_library.php",
																						"type": "POST",
																						"dataType": "json",
																						"data": function (d) {
																									d.extra_filters = {
																										  'fl_programa_sp': document.getElementById("fl_programa_nuevo_creado").value,
																										  'cl_pagina_creada': document.getElementById("cl_pagina_creada").value,
																										  
																									};
																							}
																						},
																						
																						
																						
																						"bDestroy": true,
																						
																					
																						
																						
																						
																						
																						"iDisplayLength": 15,
																						"columns": [
																						   // { "data": "checkbox", "width":"15px", "orderable": false},
																						  

																							{ "data": "nb_archivo",  "width":"30%", "orderable": false },
																							{ "data": "ds_titulo", "className": "text-align-center", "orderable": false },
																							{ "data": "ds_descripcion","orderable": false},
																							{ "data": "tipo_archivo", "className": "text-align-center" },
																						    { "data": "fecha_creacion", "className": "text-align-center","orderable": false } ,
								  
																							{ "data": "delete" },
																							{ "data": "ordera"},
																						   
																													
																													
																						],
																						"order": [[6, 'desc']],
																						"fnDrawCallback": function( oSettings ) {
																						runAllCharts();
																						/** Se tuiliza para el nombre de las imagenes **/
																						$("[rel=tooltip]").tooltip();

																						    // zoom thumbnails and add bootstrap popovers
																						    // https://getbootstrap.com/javascript/#popovers
																						$('[data-toggle="popover"]').popover({
																						    container: 'body',
																						    html: true,
																						    placement: 'auto',
																						    trigger: 'hover',
																						    content: function() {
																						        // get the url for the full size img
																						        var url = $(this).data('full');
																						        return '<img src="'+url+'" style="max-width:250px;">'
																						    }
																						});




																						/** Total de registros **/
																						var oSettings = this.fnSettings();
																						var iTotalRecords = oSettings.fnRecordsTotal(); 
																						/** Es necesario si vamos a selelecionar muchos registros en la tabla **/
																						$("#tot_reg").val(iTotalRecords);
																					  }
																					} );

																					// Add event listener for opening and closing details
																					$('#tbl_users tbody').on('click', 'td.details-control', function () {
																						var tr = $(this).closest('tr');
																						var row = table.row( tr );
																				 
																						if ( row.child.isShown() ) {
																							// This row is already open - close it
																							row.child.hide();
																							tr.removeClass('shown');
																						}
																						else {
																							// Open this row
																							row.child( format(row.data()) ).show();
																							tr.addClass('shown');
																						}
																					});
																				  
																				  /** INICIO DE SELECIONAR TODOS ***/   
																				  $('#sel_todo').on('change', function(){
																					var v_sel_todo = $(this).is(':checked'), i;
																					var iTotalRecords = $('#tot_reg').val();
																					for(i=1;i<=iTotalRecords;i++){
																					  $("#ch_"+i).prop('checked', v_sel_todo);
																					}
																				  })
																				  /** FIN DE SELECIONAR TODOS ***/
																				  
																				  /*** INICIO DE BUSQUEDA AVANZADA ***/      
																				  /** OBTENEMOS EL VALOR DEL  TIPO DE STATUS A BUSCAR **/ 
																				  // Programas
																				  $("#fl_programa_sp").on('change', function () {
																					var v =$(this).val();
																					// busca en la columna del tupo        
																					table.columns(13).search(v).draw();
																				  });
																				  // Usuarios activos o inactivos
																				  $("#fl_status").on('change', function () {
																					var v =$(this).val();        
																					// busca en la columna del tupo        
																					table.columns(15).search(v).draw();       
																				  });

																				 


																				  // Programas
																				  $("#fl_grupo_sp").on('change', function () {
																					var v =$(this).val();
																					// busca en la columna del tupo        
																					table.columns(3).search(v).draw(); 
																				  });
																				  
																				  /*** FIN DE BUSQUEDA AVANZADA ***/
																				};
																			  
																				/** FIN DE SCRIPT PARA DATATABLE **/
																				// end pagefunction
																			  
																				// load related plugins & run pagefunction
																			  /** FIN CARGA DE SCRIPT PARA LAS  DATATABLES **/      
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
																				
																				
																				
																				
																				
																			  </div>
																			  
																			  
																		
																		<!----END DATATABLE ---->

																	
																 </div>
																</div> 

															</div>
														</div>
														<!--en row-->
															
														<div class="row">

															<div class="col-xs-12 col-sm-12 col-lg-6 col-md-6 text-center"> 
																&nbsp;&nbsp;
															</div>
														
														
															<div class="col-xs-12 col-sm-12 col-lg-6 col-md-6 text-center">  

																<div class="smart-form">								
																	<br><br>
																		
																		<ul class="ui-widget ui-chatbox demo-btns" style="padding-bottom: 60px;padding-left: 295px;">
																			
																			<li>
																				<a href="javascript:void(0);" onclick="Cancel();"; class="btn btn-default btn-circle btn-lg"><i class="fa fa-times"></i></a>
																			</li>
																			
																			<li>
																				<a href="javascript:void(0);" onclick="GuardarTab4();"  class="btn btn-primary btn-circle btn-lg"><i class="fa fa-check"></i></a>
																			</li>
																		</ul>												
																</div> 

															</div>
														
														</div>	
														
											</div>
											<!----finaliza tab1--->
							
                                            <!--inicia tab2--->
											<div class="tab-pane fade" id="s2fame">
                                                <?php require "student_library_videos.php"; ?>
										    </div>
                                            <!---finaliza tab2--->
                                          </div>
								    </div>
									
                                </div>
                            </div>
                    </div>
					<!----FINALIZA STUDENT LIBRARY---->




                    <!----INCIIA CATEGORIA---->
                    <div class="tab-pane fade in" id="configuration">
                       
					    
					   
					   
					   
                        <div class="row">
							<div class="col-md-1">
							  &nbsp;
							</div>

                            <div class="col-md-10">
                                    <div class="smart-form">
                                       <?php  FAMECheckBox(ObtenEtiqueta(2073),'fg_publicar',$fg_publicar,false,ObtenEtiqueta(2353));  ?>	    
									</div> 
                            </div>
                            <div class="col-md-1">
							  &nbsp;
							</div>
                        </div>

                         <div class="row">
							<div class="col-md-1">
							  &nbsp;
							</div>

                            <div class="col-md-10">
                                    <div class="smart-form">
                                       <?php  FAMECheckBox(ObtenEtiqueta(1251),'fg_nuevo_programa',$fg_nuevo_programa,false,ObtenEtiqueta(2354));  ?>	    
									</div> 
                            </div>
                            <div class="col-md-1">
							  &nbsp;
							</div>
                        </div>

                         <div class="row">
							<div class="col-md-1">
							  &nbsp;
							</div>

                            <div class="col-md-10">
                                    <div class="smart-form">
                                       <?php  FAMECheckBox(ObtenEtiqueta(1938),'fg_board',$fg_board,false,ObtenEtiqueta(2355));   ?>	    
									</div> 
                            </div>
                            <div class="col-md-1">
							  &nbsp;
							</div>
                        </div>

                         <div class="row">
							<div class="col-md-1">
							  &nbsp;
							</div>

                            <div class="col-md-10">
                                    <div class="smart-form">
                                       <?php  FAMECheckBox(ObtenEtiqueta(2001),'fg_obligatorio',$fg_obligatorio,false,ObtenEtiqueta(2356));  ?>	    
									</div> 
                            </div>
                            <div class="col-md-1">
							  &nbsp;
							</div>
                        </div>
						
						
						
                         <div class="row">
							<div class="col-md-1">
							  &nbsp;
							</div>

                            <div class="col-md-10">
                                    <div class="smart-form">
                                       <?php  FAMECheckBox(ObtenEtiqueta(2628),'fg_compartir_curso',$fg_compartir_curso,false,ObtenEtiqueta(2629));  ?>	    
									</div> 
                            </div>
                            <div class="col-md-1">
							  &nbsp;
							</div>
                        </div>						
						
														
						<div class="row">
						
							<div class="col-xs-12 col-sm-12 col-lg-6 col-md-6 text-center"> 
							&nbsp;&nbsp;
					        </div>
						
						
							<div class="col-xs-12 col-sm-12 col-lg-6 col-md-6 text-center">
									<div class="smart-form">									
									<br><br>											
											<ul class="ui-widget ui-chatbox demo-btns" style="padding-bottom: 60px;padding-left: 295px;">
												
												<li>
													<a href="javascript:void(0);" onclick="Cancel();"; class="btn btn-default btn-circle btn-lg"><i class="fa fa-times"></i></a>
												</li>
												
												<li>
													<a href="javascript:void(0);" onclick="GuardarTab5();"  class="btn btn-primary btn-circle btn-lg"><i class="fa fa-check"></i></a>
												</li>
											</ul>									   
									</div> 
							</div>						
						</div>																				
						
                    </div>


				</div>	
				
				
				
				
			</div>

<script>

    $(document).ready(function () {

		//validaciones una ve que estan arcadas con rojo las elimna si llevan datos

        $('#nb_fos').change(function () {
            var nb_fos=$('#nb_fos').val();
			if(nb_fos !=0){		
				$('#err_fos').addClass('hidden');
				$('#nb_fos_ul').removeAttr('style');
			}
			
			
        });
		
		
        $('#nb_tags').change(function () {
            var nb_tags=$('#nb_tags').val();
			if(nb_tags !=0){		
				$('#err_tags').addClass('hidden');
				$('#singleFieldTags').removeAttr('style');
			}
			
			
        });
		
		
        $('#fl_programs').change(function () {
             var fl_programs = [];
			 $('#fl_programa_nuevo :selected').each(function(i, selected) {
				fl_programs[i] = $(selected).val();
			 });
			 
			if(fl_programs <0){		
				$('#err_fl_programa_nuevo').addClass('hidden');
			}
			
			
        });
		
		 $('#nb_har').change(function () {
            var nb_har=$('#nb_har').val();
			if(nb_har !=0){		
				$('#err_har').addClass('hidden');
				$('#hardware_ul').removeAttr('style');
			}
			
			
        });
		
		
		

        
		
    })
</script>


<script>

function GuardarTab1(){
	
	var nb_programa = document.getElementById("nb_programa").value;
	var nb_programa_esp = document.getElementById("nb_programa_esp").value;7
	var nb_programa_fra = document.getElementById("nb_programa_fra").value;
	var no_creditos = document.getElementById("no_creditos").value;
	var workload = document.getElementById("workload").value;
    var no_horas = document.getElementById("no_horas").value;
	var no_semanas=document.getElementById("no_semanas").value;
	var ds_credential=document.getElementById("ds_credential").value;
	var ds_credential_esp=document.getElementById("ds_credential_esp").value;
	var ds_credential_fra=document.getElementById("ds_credential_fra").value;
	var cl_delivery=document.getElementById("cl_delivery").value;
	var cl_delivery_esp=document.getElementById("cl_delivery_esp").value;
	var cl_delivery_fra=document.getElementById("cl_delivery_fra").value;
    var cl_type=document.getElementById("cl_type").value;
	var ds_language=document.getElementById("ds_language").value;
	var ds_language_esp=document.getElementById("ds_language_esp").value;
	var ds_language_fra=document.getElementById("ds_language_fra").value;
	var nb_thumb_load=document.getElementById("nb_thumb_load").value;
	//var no_orden=document.getElementById("no_orden").value;
	//var fg_publicar=document.getElementById("fg_publicar").value;
	var clave=<?php echo $clave;?>;
    var tab=1;
 /*
    var mySelections =$('#nb_fos').val(); //tags

    // if( $('#fl_programa_nuevo :selected').length > 0){
		  var selectednumbers = [];
		 $('#fl_programa_nuevo :selected').each(function(i, selected) {
            selectednumbers[i] = $(selected).val();
			alert('enro'+selectednumbers[i]);
         });
		 
	// }
	   
	*/   
	   

    var datos = new FormData();
     datos.append('thumb',$('#thumb')[0].files[0]);
     datos.append('nb_programa',nb_programa);
	 datos.append('nb_programa_esp',nb_programa_esp); 
	 datos.append('nb_programa_fra',nb_programa_fra); 
	 datos.append('no_creditos',no_creditos);
	 datos.append('workload',workload);
	 datos.append('no_horas',no_horas);
	 datos.append('no_semanas',no_semanas);
	 datos.append('ds_credential',ds_credential);
	 datos.append('cl_delivery',cl_delivery);
	 datos.append('cl_delivery_esp',cl_delivery_esp);
	 datos.append('cl_delivery_fra',cl_delivery_fra);
	 datos.append('ds_credential_esp',ds_credential_esp);
	 datos.append('ds_credential_fra',ds_credential_fra);
	 datos.append('cl_type',cl_type);
	 //datos.append('no_orden',no_orden);
	 datos.append('ds_language',ds_language);
	 datos.append('ds_language_esp',ds_language_esp);
	 datos.append('ds_language_fra',ds_language_fra);
	 datos.append('nb_thumb_load',nb_thumb_load);
	 //datos.append('fg_publicar',fg_publicar);
     datos.append('clave',clave);
	 datos.append('tab',tab);
	// datos.append('mySelections',mySelections);
	// datos.append('selectednumbers',selectednumbers);
    //alert(archivo);

	if(nb_programa.length>0){
		$("#nb_programa_input_error").removeClass("state-error");
		$("#nb_programa_texto_error").addClass("hidden");
		var fg_correcto=1;
	}else{		
		$("#nb_programa_input_error").addClass("state-error");
		$("#nb_programa_texto_error").removeClass("hidden");
		var fg_correcto=0;
		return;
	}
	
	if(no_creditos.length>0){
		$("#no_creditos_input_error").removeClass("state-error");
		$("#no_creditos_texto_error").addClass("hidden");
		var fg_correcto=1;
	}else{		
		$("#no_creditos_input_error").addClass("state-error");
		$("#no_creditos_texto_error").removeClass("hidden");
		var fg_correcto=0;
		return;
	}
	
	
	if(cl_delivery !=0){		
		$("#cl_delivery_texto_error").addClass("hidden");
		var fg_correcto=1;
	}else{		
		
		$("#cl_delivery_texto_error").removeClass("hidden");
		var fg_correcto=0;
		return;
	}
	
	if(cl_type !=0){	
		$("#cl_delivery_texto_error").addClass("hidden");
		var fg_correcto=1;
	}else{			
		$("#cl_delivery_texto_error").removeClass("hidden");
	    var fg_correcto=0;
		return;
	}
	
	
	
	
	
	
	if(no_horas.length>0){
		$("#no_horas_input_error").removeClass("state-error");
		$("#no_horas_texto_error").addClass("hidden");
		var fg_correcto=1;
	}else{		
		$("#no_horas_input_error").addClass("state-error");
		$("#no_horas_texto_error").removeClass("hidden");
		var fg_correcto=0;
		return;
	}
	if(no_semanas.length>0){
		$("#no_semanas_input_error").removeClass("state-error");
		$("#no_semanas_texto_error").addClass("hidden");
		var fg_correcto=1;
	}else{		
		$("#no_semanas_input_error").addClass("state-error");
		$("#no_semanas_texto_error").removeClass("hidden");
		var fg_correcto=0;
		return;
	}
	
	if(ds_credential.length>0){
		$("#ds_credential_input_error").removeClass("state-error");
		$("#ds_credential_texto_error").addClass("hidden");
		var fg_correcto=1;
	}else{		
		$("#ds_credential_input_error").addClass("state-error");
		$("#ds_credential_texto_error").removeClass("hidden");
		var fg_correcto=0;
		return;
	}
	
	/*if(no_orden.length>0){
		$("#no_orden_input_error").removeClass("state-error");
		$("#no_orden_texto_error").addClass("hidden");
		var fg_correcto=1;
	}else{		
		$("#no_orden_input_error").addClass("state-error");
		$("#no_orden_texto_error").removeClass("hidden");
		var fg_correcto=0;
		return;
	}
	
	*/
	

	if(fg_correcto==1){
			$.ajax({
			  type:"post",
			  url: 'site/mycourses_iu.php',
			  contentType:false, // se envie multipart
			  data:datos,
			  processData:false, // poque vamos enviar un archivo
			}).done(function(result){
			  
			  var result = JSON.parse(result);
			  var fl_programa_nuevo_creado=result.fl_programa_nuevo_creado;
			  $("#fl_programa_nuevo_creado").val(fl_programa_nuevo_creado);
			  
			    //reemplazamos la imagen con la que se subio.
			  $("#img_mike_preview").empty();
			  $("#img_mike_preview").append("<a class='thumbnail' style='margin:auto;border: solid 0px; background: transparent;margin-left: -33px;' data-toggle='popover' href='javascript:void(0);' data-placement='top' id='img_2_1t' name='img_2_1t' data-full='"+result.ruta_foto+"'>  <img id='img_1_1t' name='img_1_1t' src='"+result.ruta_foto+"' style='width:50%'>  </a> ");
			    
			    
			    
			  
			  //alerta de exito.		  
			  $.smallBox({
				title : "<?php echo ObtenEtiqueta(2357);?>",
				content : "<i class='fa fa-clock-o'></i> <i><?php echo ObtenEtiqueta(2358);?></i>",
				color : "#276627",
				iconSmall : "fa fa-thumbs-up bounce animated",
				timeout : 4000
			  });
		
			  
			    // zoom thumbnails and add bootstrap popovers
			    // https://getbootstrap.com/javascript/#popovers
			    $('[data-toggle="popover"]').popover({
			        container: 'body',
			        html: true,
			        placement: 'auto',
			        trigger: 'hover',
			        content: function() {
			            // get the url for the full size img
			            var url = $(this).data('full');
			            return '<img src="'+url+'" style="max-width:250px;">'
			        }
			    });
	  
			  
			  
			});
	}
}

function GuardarTab2(){
	
	var nb_fos=$('#nb_fos').val(); //tags
	var nb_tags=$('#nb_tags').val();
	var clave=<?php echo $clave;?>;

     var contador_programs=0;
	 var fl_programs = [];
	 $('#fl_programa_nuevo :selected').each(function(i, selected) {
		fl_programs[i] = $(selected).val();
	     	contador_programs++;
		//alert('enro'+fl_programs[i]);
	 });

   
	
	var nb_har=$('#nb_har').val(); 
	var nb_sof=$('#nb_sof').val(); 
	var nb_lvl=document.getElementById("nb_lvl").value;
	var ds_course_code=$('#ds_course_code').val();
    var fl_programa_nuevo_creado=document.getElementById("fl_programa_nuevo_creado").value;





    //Validaciones.
	if(nb_fos !=0){		
		$('#err_fos').addClass('hidden');
	    $('#nb_fos_ul').removeAttr('style');
		var fg_correcto=1;
	}else{
		document.getElementById("nb_fos_ul").style.border = "solid .5px #A90329";
		$('#err_fos').removeClass('hidden');
		var fg_correcto=0;
		return;
	}
	
	if(nb_tags !=0){	
		$('#err_tags').addClass('hidden');
	    $('#singleFieldTags').removeAttr('style');
		var fg_correcto=1;
	}else{
		document.getElementById("singleFieldTags").style.border = "solid .5px #A90329";
		$('#err_tags').removeClass('hidden');
		var fg_correcto=0;
		return;
	}
	
	if(contador_programs>0){	
		
	    $('#err_fl_programa_nuevo').addClass('hidden');
		var fg_correcto=1;	
	}else{		
		
		$('#err_fl_programa_nuevo').removeClass('hidden');
		var fg_correcto=0;
		return;		
	}




    if(nb_har !=0){		
		$('#err_har').addClass('hidden');
	    $('#hardware_ul').removeAttr('style');
		var fg_correcto=1;
	}else{
		document.getElementById("hardware_ul").style.border = "solid .5px #A90329";
		$('#err_har').removeClass('hidden');
		var fg_correcto=0;
		return;
		
	}
	
	if(nb_sof !=0){		
		$('#err_sof').addClass('hidden');
	    $('#software_ul').removeAttr('style');
		var fg_correcto=1;
	}else{
		document.getElementById("software_ul").style.border = "solid .5px #A90329";
		$('#err_sof').removeClass('hidden');
	    var fg_correcto=0;
		return;
		
	}
	
	
	if(ds_course_code.length>0){		
		$('#err_cours_code').addClass('hidden');
	    $('#input_ds_course_code').removeClass('has-error');
		var fg_correcto=1;
	}else{
		$('#input_ds_course_code').addClass('has-error');
		$('#err_cours_code').removeClass('hidden');	
		var fg_correcto=0;
		return;
	}

	var fl_programa_pre = [];
	$('#fl_programa_pre :selected').each(function(i, selected) {
		fl_programa_pre[i] = $(selected).val();
	});
		 


	 var fl_programa_sig = [];
	 $('#fl_programa_sig :selected').each(function(i, selected) {
		fl_programa_sig[i] = $(selected).val();

	 });

	 var fl_course_code = [];
	 $('#fl_course_code :selected').each(function(i, selected) {
		fl_course_code[i] = $(selected).val();
	
	 });
	 
	
	 
	 
		 
	var ds_contenido_curso= CKEDITOR.instances.ds_contenido_curso.getData();
    

	var tab=2;
    var datos = new FormData();
    datos.append('tab',tab);
	 datos.append('clave',clave);
	datos.append('nb_fos',nb_fos);
	datos.append('nb_tags',nb_tags);
	datos.append('nb_har',nb_har);
	datos.append('fl_programa_nuevo',fl_programs);
	datos.append('nb_har',nb_har);
	datos.append('nb_sof',nb_sof);
	datos.append('nb_lvl',nb_lvl);
	datos.append('ds_course_code',ds_course_code);
	datos.append('fl_programa_pre',fl_programa_pre);
	datos.append('fl_programa_sig',fl_programa_sig);
	datos.append('fl_course_code',fl_course_code);
	datos.append('fl_programa_nuevo_creado',fl_programa_nuevo_creado);
	datos.append('ds_contenido_curso',ds_contenido_curso);
	
	
    if(fg_correcto==1){
		$.ajax({
		  type:"post",
		  url: 'site/mycourses_iu.php',
		  contentType:false, // se envie multipart
		  data:datos,
		  processData:false, // poque vamos enviar un archivo
		}).done(function(result){			  
			  var result = JSON.parse(result);
			  
			  var err_ds_course_code=result.err_ds_course_code;
			  var err_ds_cour_code=result.err_ds_cour_code;
			  
			  //alert(err_ds_cour_code);
			  if(err_ds_cour_code==1){
				 // alert('entro');
				  $("#err_existe_ds_course_code").removeClass('hidden');
				  $("#label_input_ds_course_code").addClass('state-error');
				  
			  }else{
			      $("#err_existe_ds_course_code").addClass('hidden');
				  $("#label_input_ds_course_code").removeClass('state-error');
			  
				 
				  
				  
				  //alerta de exito.		  
				  $.smallBox({
					title : "<?php echo ObtenEtiqueta(2357);?>",
					content : "<i class='fa fa-clock-o'></i> <i><?php echo ObtenEtiqueta(2358);?></i>",
					color : "#276627",
					iconSmall : "fa fa-thumbs-up bounce animated",
					timeout : 4000
				  });
				  
			  
			  
			  }
			  
		});
		
	}
	
	
}

function GuardarTab3(){
	
    var clave=<?php echo $clave;?>;
    var ds_programa= CKEDITOR.instances.ds_programa.getData();
    var ds_programa_esp= CKEDITOR.instances.ds_programa_esp.getData();
    var ds_programa_fra= CKEDITOR.instances.ds_programa_fra.getData();
	var ds_learning= CKEDITOR.instances.ds_learning.getData();
	var ds_learning_esp= CKEDITOR.instances.ds_learning_esp.getData();
	var ds_learning_fra= CKEDITOR.instances.ds_learning_fra.getData();
	var ds_metodo= CKEDITOR.instances.ds_metodo.getData();
	var ds_metodo_esp= CKEDITOR.instances.ds_metodo_esp.getData();
	var ds_metodo_fra= CKEDITOR.instances.ds_metodo_fra.getData();
	var ds_requerimiento= CKEDITOR.instances.ds_requerimiento.getData();
	var ds_requerimiento_esp= CKEDITOR.instances.ds_requerimiento_esp.getData();
	var ds_requerimiento_fra= CKEDITOR.instances.ds_requerimiento_fra.getData();
	var fl_programa_nuevo_creado=document.getElementById("fl_programa_nuevo_creado").value;
	


    
	if(ds_programa.length>0){
	    $('#err_ds_programa').addClass('hidden');	
	    var fg_exito=1;		
	}else{		
	    $('#err_ds_programa').removeClass('hidden');
	    var fg_exito=0;		
	}


	if(ds_learning.length>0){
		$('#err_ds_learning').addClass('hidden');	
        var fg_exito=1;		
	}else{		
		$('#err_ds_learning').removeClass('hidden');
		var fg_exito=0;		
	}
	
	if(ds_metodo.length>0){
		$('#err_ds_metodo').addClass('hidden');
		var fg_exito=1;
	}else{		
		$('#err_ds_metodo').removeClass('hidden');	
		var fg_exito=0;		
	}
	
	if(ds_requerimiento.length>0){
		$('#err_ds_requerimiento').addClass('hidden');
		var fg_exito=1;		
	}else{		
		$('#err_ds_requerimiento').removeClass('hidden');
		var fg_exito=0;		
	}
	
	
	var tab=3;
    var datos = new FormData();
    datos.append('tab',tab);
    datos.append('ds_programa',ds_programa);
    datos.append('ds_programa_esp',ds_programa_esp);
    datos.append('ds_programa_fra',ds_programa_fra);
	datos.append('ds_learning',ds_learning);
	datos.append('ds_learning_esp',ds_learning_esp);
	datos.append('ds_learning_fra',ds_learning_fra);
	datos.append('ds_metodo',ds_metodo);
	datos.append('ds_metodo_esp',ds_metodo_esp);
	datos.append('ds_metodo_fra',ds_metodo_fra);
	datos.append('ds_requerimiento',ds_requerimiento);
	datos.append('ds_requerimiento_esp',ds_requerimiento_esp);
	datos.append('ds_requerimiento_fra',ds_requerimiento_fra);
	datos.append('fl_programa_nuevo_creado',fl_programa_nuevo_creado);
	datos.append('clave',clave);
	
	
	if(fg_exito==1){
		
		
		$.ajax({
		  type:"post",
		  url: 'site/mycourses_iu.php',
		  contentType:false, // se envie multipart
		  data:datos,
		  processData:false, // poque vamos enviar un archivo
		}).done(function(result){			  
			  var result = JSON.parse(result);
			  MuestraTablas();
		});
		
		

		//alerta de exito.		  
	    $.smallBox({
		  title : "<?php echo ObtenEtiqueta(2357);?>",
		  content : "<i class='fa fa-clock-o'></i> <i><?php echo ObtenEtiqueta(2358);?></i>",
		  color : "#276627",
		  iconSmall : "fa fa-thumbs-up bounce animated",
		  timeout : 4000
	    });
		
		MuestraTablas();
	}
	
	
	
}


function GuardarTab4(){
	
	var clave=<?php echo $clave;?>;
	var tab=4;
	
	
	var nb_pagina=document.getElementById("nb_pagina").value;
	var ds_pagina=document.getElementById("ds_pagina").value;
	var ds_titulo=document.getElementById("ds_titulo").value;
	var fl_programa_nuevo_creado=document.getElementById("fl_programa_nuevo_creado").value;
	
    var datos = new FormData();
    datos.append('tab',tab);
    datos.append('fl_programa_nuevo_creado',fl_programa_nuevo_creado);
    datos.append('clave',clave);
	datos.append('nb_pagina',nb_pagina);
	datos.append('ds_pagina',ds_pagina);
	datos.append('ds_titulo',ds_titulo);
	
	$.ajax({
		  type:"post",
		  url: 'site/mycourses_iu.php',
		  contentType:false, // se envie multipart
		  data:datos,
		  processData:false, // poque vamos enviar un archivo
		}).done(function(result){
		  
		  var result = JSON.parse(result);
		  var cl_pagina_sp=result.cl_pagina_sp;
		  if(cl_pagina_sp){
			   $("#cl_pagina_creada").val(cl_pagina_sp);	  
		  }  
		  
		  //alerta de exito.		  
		  $.smallBox({
			title : "<?php echo ObtenEtiqueta(2357);?>",
			content : "<i class='fa fa-clock-o'></i> <i><?php echo ObtenEtiqueta(2358);?></i>",
			color : "#276627",
			iconSmall : "fa fa-thumbs-up bounce animated",
			timeout : 4000
		  });
		  
		  
		  
		  
    });
}



function UploadStudentLibrary(){
	
	$('#frm-student_library').empty();
	//var ele = $('#modal-frm-student_library').modal('toggle');
    var clave=<?php echo $clave;?>;
	var fl_programa_nuevo_creado=document.getElementById("fl_programa_nuevo_creado").value;
    var cl_pagina_creada=document.getElementById("cl_pagina_creada").value;
	var fg_accion=1;
	
	
	
	
	var datos = new FormData();
    datos.append('clave',clave);
	datos.append('fl_programa_nuevo_creado',fl_programa_nuevo_creado);
	datos.append('cl_pagina_creada',cl_pagina_creada);
	datos.append('fg_accion',fg_accion);
	
    $.ajax({
      type: 'POST',
      url: 'site/modal_upload_files_student_library.php',
	  contentType:false, // se envie multipart
      data: datos,
	  processData:false, // poque vamos enviar un archivo
      success: function(html){
          $('#frm-student_library').append(html);
		  $("#gabriel").removeAttr("style");

      }
    });
    
	
	  //onlcick
	  document.getElementById('muetra_atatable').click();
	
	
}


function ElimnarArchivo(clave){
    var conf = confirm('<?php echo ObtenEtiqueta(2230);?>');
    if(conf==true){
	 var fg_accion=2;
     $.ajax({
        type: 'POST',
        url: 'site/upload_files_student_library.php',
        data: 'fl_archivo_delete='+clave+
		      '&fg_accion_1='+fg_accion,
      }).done(function(result){
        var result = JSON.parse(result);
		
        var success = result.success;
        if(success==true){
          $('#tbl_users').DataTable().ajax.reload();
		  MuestraTablas();
		  
		   $('[data-toggle="popover"]').popover({
			container: 'body',
			html: true,
			placement: 'auto',
			trigger: 'hover',
			content: function() {
			  // get the url for the full size img
			  var url = $(this).data('full');
			  return '<img src="'+url+'" style="max-width:250px;">'
			}
		  });
		  
		  
        }
        
      });
    }

}

function GuardarTab5(){
	
    var clave=<?php echo $clave;?>;
    var fl_programa_nuevo_creado=document.getElementById("fl_programa_nuevo_creado").value;
    var tab=5;
	
	
	if ($('#fg_publicar').is(':checked')) {
        var fg_publicar = 1;
	} else {
		var fg_publicar = 0;
	}
	
	
	if ($('#fg_nuevo_programa').is(':checked')) {
        var fg_nuevo_programa = 1;
	} else {
		var fg_nuevo_programa = 0;
	}
	
	
	if ($('#fg_board').is(':checked')) {
        var fg_board = 1;
	} else {
		var fg_board = 0;
	}
	
	
	if ($('#fg_obligatorio').is(':checked')) {
        var fg_obligatorio = 1;
	} else {
		var fg_obligatorio = 0;
	}
	
	if ($('#fg_compartir_curso').is(':checked')) {
        var fg_compartir_curso = 1;
	} else {
		var fg_compartir_curso = 0;
	}

	var datos = new FormData();
	datos.append('tab',tab);
    datos.append('clave',clave);
	datos.append('fg_publicar',fg_publicar);
	datos.append('fg_nuevo_programa',fg_nuevo_programa);
	datos.append('fg_board',fg_board);
	datos.append('fg_obligatorio',fg_obligatorio);
	datos.append('fl_programa_nuevo_creado',fl_programa_nuevo_creado);
	datos.append('fg_compartir_curso',fg_compartir_curso);
	
	
	$.ajax({
		type:"post",
		url: 'site/mycourses_iu.php',
		contentType:false, // se envie multipart
		data:datos,
		processData:false, // poque vamos enviar un archivo
		}).done(function(result){
  
  
  	  
	   //alerta de exito.		  
	   $.smallBox({
		 title : "<?php echo ObtenEtiqueta(2357);?>",
		 content : "<i class='fa fa-clock-o'></i> <i><?php echo ObtenEtiqueta(2358);?></i>",
		 color : "#276627",
		 iconSmall : "fa fa-thumbs-up bounce animated",
		 timeout : 4000
	   });
		  
		  
  
  
    });
	
	
	
}


function Cancel(){
	
	
	window.location = "index.php#site/courses_library_list.php";
	
}



	
    //
function validaNumericos(event) {
    if(event.charCode >= 48 && event.charCode <= 57){
      return true;
     }
     return false;        
}	


 //Valida numero y dos decimales
    function NumeroDecimal(e, field) {
        key = e.keyCode ? e.keyCode : e.which
        // backspace
        if (key == 8) return true
        // 0-9
        if (key > 47 && key < 58) {
            if (field.value == "") return true
            regexp = /.[0-9]{2}$/
            return !(regexp.test(field.value))
        }
        // .
        if (key == 46) {
            if (field.value == "") return false
            regexp = /^[0-9]+$/
            return regexp.test(field.value)
        }
        // other key
        return false
 
    }
	//Valida Numeros y dos decimales.
    function NumDecimal(e, field){
		
		 key = e.keyCode ? e.keyCode : e.which
		  // backspace
		  if (key == 8) return true
		  // 0-9
		  if (key > 47 && key < 58) {
			if (field.value == "") return true
			regexp = /.[0-9]{2}$/
			return !(regexp.test(field.value))
		  }
		  //.
		  if (key == 46) {
			if (field.value == "") return false
			regexp = /^[0-9]+$/
			return regexp.test(field.value)
		  }
		  // other key
		  return false;
		
		
	}


	
 	function MuestraTabla(fl_instituto,fl_maestro_sp){
		
		 //pasamos por ajax los valores y presentamos modal.
         $.ajax({
             type: 'POST',
             url: 'site/muetra_tabla_img.php',
             data: 'fl_instituto='+fl_instituto+ 
                   '&fl_maestro_sp='+fl_maestro_sp,

             async: true,
             success: function (html) {
                 $('#muetra_tabla_img').html(html);
             }
         });
	}
	
	
	
	 $(document).ready(function () {
		$("#gabriel").removeAttr("style");
		
		

	  // zoom thumbnails and add bootstrap popovers
	  // https://getbootstrap.com/javascript/#popovers
	  $('[data-toggle="popover"]').popover({
		container: 'body',
		html: true,
		placement: 'auto',
		trigger: 'hover',
		content: function() {
		  // get the url for the full size img
		  var url = $(this).data('full');
		  return '<img src="'+url+'" style="max-width:250px;">'
		}
	  });
																			
		
		
     }); 
</script>


  	<div class='modal-content' id='frm-student_library'></div>
 	  
