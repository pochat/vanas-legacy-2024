<?php

# Libreria de funciones	
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  $row_cont_cur = RecuperaValor("SELECT fl_programa_sp FROM c_programa_sp ORDER BY fl_programa_sp DESC LIMIT 1");
  
  #Verficamos si es alumno de vanas para colocar boton de pagar curso o invitar a otro compadre.
  if($fl_perfil_sp==PFL_ESTUDIANTE_SELF)
  $fg_puede_liberar_curso=PuedeLiberarCurso($fl_instituto,$fl_usuario);
  
  #Identifiacmos si el logueado es b2c
  if($fg_puede_liberar_curso)
	  $fg_b2c=1;
  else
	  $fg_b2c=0;
  #Los trials no pueden ver boton export
  $fg_plan_instituto_=ObtenPlanActualInstituto($fl_instituto);
  if(empty($fg_plan_instituto_))
  $fg_b2c=1;

  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);

  # Recibimos datos
  $orden = !empty($_POST['order']) ? $_POST['order'] : 0 ;
  $fl_playlist = !empty($_POST['playlist']) ? $_POST['playlist'] : 0 ;
  $fl_programa_sp = !empty($_POST['fl_programa']) ? $_POST['fl_programa'] : 0 ;

  # Obtenemos el progreso del curso
  $Query = "SELECT ds_progreso, fl_programa_sp FROM k_usuario_programa WHERE fl_playlist = ".$fl_playlist." ORDER BY fl_programa_sp DESC LIMIT 1 OFFSET 1 ";
  $progress = RecuperaValor($Query);

  # Obtenemos datos del curso
  $Query = "SELECT nb_programa".$sufix.", nb_thumb, fl_programa_sp FROM c_programa_sp WHERE fl_programa_sp = ".$fl_programa_sp;
  $curso = RecuperaValor($Query);

  # Obtenemos el numero de lecciones del curso
  $Query = RecuperaValor("SELECT COUNT(*) FROM c_leccion_sp WHERE fl_programa_sp = ".$progress['fl_programa_sp']);
  $no_lessons = $Query[0];

  # Obtenemos la cantidad de students en este curso
    $Query  = "SELECT COUNT(*) FROM c_usuario a LEFT JOIN k_usuario_programa b ON(a.fl_usuario=b.fl_usuario_sp) WHERE  a.fl_perfil_sp=".PFL_ESTUDIANTE_SELF." AND fl_instituto=".$fl_instituto." AND b.fl_programa_sp=".$fl_programa_sp;
    $row = RecuperaValor($Query);
    $no_studets = $row[0];

    # Obtenemos los grupos que existen de este curso en este instituto
    $Queryg  = "SELECT nb_grupo FROM c_usuario a LEFT JOIN k_usuario_programa b ON(a.fl_usuario=b.fl_usuario_sp) ";
    $Queryg .= "LEFT JOIN c_alumno_sp c ON(c.fl_alumno_sp=a.fl_usuario) ";
    $Queryg .= "WHERE  a.fl_perfil_sp=".PFL_ESTUDIANTE_SELF." AND fl_instituto=".$fl_instituto." AND b.fl_programa_sp=".$fl_programa_sp." AND nb_grupo<>'' GROUP BY c.nb_grupo ";
    $rsg = EjecutaQuery($Queryg);
    $no_groups = CuentaRegistros($rsg);

?>
<!-- Modal del programa que requiere ----->
<div class="modal-content">
	<!--- Header -->
	<div class="modal-header">
	  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	  	<span aria-hidden="true">×</span>
	  </button>
	  <h4 class="modal-title" id="gridModalLabel"><i style='color: orange;' class="fa fa-info-circle" aria-hidden="true">
	  </i> <?php echo ObtenEtiqueta(2006); ?>
	  </h4>
	</div>
	<!-- Body --->
	<div class="modal-body">
		<div class="product-content product-wrap clearfix">
	   		<div class="row">
	      		<!-- Imagen Inf Students Groups-->
	      	<div class="col-md-4 col-sm-12 col-xs-12">
	        	<div class="product-image"> 
	         		<img src="/AD3M2SRC4/modules/fame/uploads/<?php echo $curso['nb_thumb'] ?>" alt="Fundamentos de anatomía" class="img-responsive" style="margin:auto;"> 
	         		<div class="product-info smart-form">
	            		<div class="row">
	              			<div class="col-md-3"> 
	              			</div>
	              		<div class="col-md-12 col-sm-12 col-xs-12"> 
	                		<h5>
	                		<small>
	                		<!-- <center>Students (<?php echo $no_studets; ?>)</center>
	                		<center>Groups (<?php echo $no_groups; ?>)</center> -->
	                		</small>
	                		</h5>                       
	             		</div>
	            	</div>
	        	</div>
	        </div>
	    </div>
    	<!--- Boton para iniciar o continurar detalles de curso --->
    	<div class="col-md-8 col-sm-12 col-xs-12">
        	<!-- <div class="row">
        		<div class="col-md-12 col-sm-12 col-xs-12">
            		<div style="float: right;">                 
              			<a class="btn btn-default disabled">
              			<span class="caret"></span>
              			</a> 
            		</div> 
          		</div> 
        	</div> -->
        	<div class="product-deatil" style="padding-top: 0px; padding-bottom:2px;"><br>
          		<h5 class="name">
            	<a><?php echo $curso['nb_programa'.$sufix]; ?></a>
          		</h5><br>
            	<a data-toggle="collapse" aria-expanded="false" aria-controls="collapseExample2" class="disabled">                  
                <span style="font-size: 24px;color: #21c2f8;font-family: Lato,sans-serif;"><?php echo $no_lessons." ".ObtenEtiqueta(1242); ?></span>
            	</a>  
            	<br><br>
            	<div class="row">
		            <div class="col-xs-12 col-sm-12 col-md-12"> 
              			<!-- ICH: Div para mostrar informacion general del curso --->
            			<ul class="nav navbar-nav navbar-left col-xs-2 col-sm-1 col-md-2">
                			<li class="dropdown">
                  			<a href="javascript:void(0);" data-toggle="dropdown" style="padding:0px 0px 0px 0px; background:transparent;" class="disabled"><i class="fa fa-fw fa-sm fa-info-circle " style="color:#9aa7af;" aria-hidden="true"></i></a>
                			</li>
            			</ul>
              			<!-- ICH: Div para mostrar distintas categorias del curso --->
              			<ul class="nav navbar-nav navbar-left col-xs-2 col-sm-1 col-md-2">
                			<li class="dropdown">
                  			<a href="javascript:void(0);" data-toggle="dropdown" style="padding:0px 0px 0px 0px; background:transparent;" class="disabled"><i class="fa fa-fw fa-sm fa-tags " style="color:#9aa7af;" aria-hidden="true"></i></a>
                			</li>
            			</ul>
              			<!-- ICH: Div para mostrar informacion del curso --->
              			<ul class="nav navbar-nav navbar-left col-xs-2 col-sm-1 col-md-2">
                			<a href="javascript:void(0);"><i class="fa fa-fw fa-sm fa-file-text-o" style="color:#9aa7af;" data-toggle="modal" aria-hidden="true"></i></a>
            			</ul>
         			</div>
        		</div>
        	</div>
        	<div class="description"></div>
        		<div class="product-info smart-form">
        			<div class="row">
            			<div class="col-md-12 col-sm-12 col-xs-12">
            				<br>
              				<a class="btn btn-success" href="javascript: $('#requiredModal').modal('toggle'); redireccionar('#site/desktop.php?fl_programa=<?php echo($curso['fl_programa_sp']) ?>');"> <i class="fa fa-check" aria-hidden="true"></i><?php echo ObtenEtiqueta(1149); ?></a>
            				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				                <div class="row">                      
                					<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10" style="padding-left:13px;">
                						<br>
                    					<div class="progress progress-xs" data-progressbar-value="<?php echo $progress['ds_progreso']; ?>">
                    						<div class="progress-bar">
                    						</div>
                    					</div>
                					</div>
                				</div>
            				</div>
            			</div>
        			</div>
        		</div>
    		</div>          
    	</div>
	</div>
</div>
</div>


