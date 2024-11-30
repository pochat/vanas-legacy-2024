<?php 
	# Libreria de funciones	
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  $clave = RecibeParametroNumerico('c', True, False);

  #Recupermaos el grado que tiene el usuario/alumno.
  $Query="SELECT ds_instituto,ds_alias,ds_nombres,ds_apaterno FROM c_instituto c JOIN c_usuario b ON b.fl_usuario=c.fl_usuario_sp WHERE c.fl_instituto=$clave ";
  $row=RecuperaValor($Query);
  $nb_instituto=$row['ds_instituto'];
  $ds_alias=$row['ds_alias'];
  $ds_nombres=$row['ds_nombres'];
  $ds_apaterno=$row['ds_apaterno'];
  
  
?>

  <script src=\"https://use.fontawesome.com/840229d803.js\"></script>
  <!------permite que se visualize bien la j , la g en el input----->
  <style>

      .smart-form .input input, .smart-form .select select, .smart-form .textarea textarea {

          padding: 6px 10px;
      }
  </style> 
  
  
  
  
  <div class="row">
	<div class="col-md-6">
	
				<blockquote style="background:#f7f3f3;border-left: 5px solid #0092cd;">
				  <span style="font-size: 26px; line-height: 1.5em;">
					<i class="fa fa-graduation-cap" aria-hidden="true"></i> <?php echo ObtenEtiqueta(2524)." ".$nb_instituto;?>										
				  </span>
				</blockquote>
	
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
        <a href="#programs" data-toggle="tab"><i class="fa fa-fw fa-lg fa-info"></i><?php echo ObtenEtiqueta(1583); ?></a>
      </li>

    </ul>
 
    <!--- Contenido -->
    <div id="myTabContent1" class="tab-content">
      <!--<form class="smart-form" name="users" id="users" method="post" action="site/users_details_iu.php">-->
      
        
        
        <div class="tab-pane fade in active"  id="programs">
        
	
		
          <div class="row padding-10">          
            <div class="col col-xs-12 col-sm-5">
			  
			
			  <label class="label" style="margin-left:5px;"><b><?php echo ObtenEtiqueta(763);?></b></label>
              <?php
              CampoTexto('ds_nombres', $ds_nombres, 'form-control', False, '', ObtenEtiqueta(763), "fa-user", "col-md-12", "append");
              ?>
            </div>
			
			<div class="col col-xs-12 col-sm-5">
			  <label class="label" style="margin-left:5px;"><b><?php echo ObtenEtiqueta(910);?></b></label>
              <?php
              CampoTexto('ds_apaterno', $ds_apaterno, 'form-control', False, '', ObtenEtiqueta(910), "fa-user", "col-md-12", "append");
              ?>
            </div>
		  </div>	
		  <div class="row padding-10"> 
            <div class="col col-xs-12 col-sm-5">
			  <label class="label" style="margin-left:5px;"><b><?php echo ObtenEtiqueta(1793);?></b></label>
              <?php
              CampoTexto('ds_alias', $ds_alias, 'form-control', False, '', ObtenEtiqueta(910), "fa-key", "col-md-12", "append");
              ?>
            </div>
          </div>
          
       

         
          
        </div>
        



		
		
    </div>

   </div>
 </form>
 <!-- Botones --->
  <ul class="ui-widget ui-chatbox demo-btns" style="right: 0px; display: block; padding:0px 60px 10px 0px;">
    <li>
      <a onclick="parent.location='<?php if($fl_perfil== PFL_ADMINISTRADOR) echo "index.php#site/institutions.php"; else echo "index.php#site/institutions.php"; ?>'" class="btn btn-default btn-circle btn-lg"><i class="glyphicon glyphicon-remove"></i></a>
    </li>
    <li>
      <a href="javascript:void(0);" onclick="parent.location='index.php#site/institutions.php'" class="btn btn-primary btn-circle btn-lg" id="save_info"><i class="glyphicon glyphicon-ok"></i></a>
    </li>
  </ul>
