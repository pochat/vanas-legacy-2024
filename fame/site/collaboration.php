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

  #Recuperamos el isttituo
  $fl_instituto=ObtenInstituto($fl_usuario);
  //$titulo=ObtenEtiqueta();
  
  $Query="SELECT fg_gender,fg_grade,fg_educational, fg_international, fg_blocking, fg_ferpa, fg_addStudents, fg_addTeachers, fg_deletions FROM k_instituto_filtro WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $fg_grade=$row['fg_grade'];
  $fg_gender=$row['fg_gender'];
  $fg_educational = $row['fg_educational'];
  $fg_international = $row['fg_international'];
  $fg_blocking = $row['fg_blocking'];
  $fg_ferpa=$row['fg_ferpa'];
  $fg_addStudents=$row['fg_addStudents'];
  $fg_addTeachers=$row['fg_addTeachers'];
  $fg_deletions=$row['fg_deletions'];
  
  #Recuperamos si el instituo se podra saltar el Parent authorization
  $Query="SELECT fg_parent_authorization,fg_privacy FROM c_instituto WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $fg_parent_authorization=$row['fg_parent_authorization'];
  $fg_privacy=$row['fg_privacy'];

  $bloked = ($fg_privacy=='1')?"state-disabled":"";
  $slider_enabled=($fg_privacy=='1')?"false":"true";
  
  #Valores default, cuando el Instituto no ha registrado cambios.
  if(empty($fg_gender)){
      $fg_grade=4;
      $fg_nivel=3;
      $fg_gender=1;
      
  }

   if($fg_gender==1){
      $check="checked";
	  $check2="";
	  }else{
		 $check2="checked";
		 $check="";
	  }
  
  # Educational
  if($fg_educational==1)
    $educational = "checked";
  if($fg_educational==2)
    $educational = "checked";
   
?>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/css/bootstrap-slider.min.css" />
	<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/css/bootstrap-slider.css" />-->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/bootstrap-slider.js" ></script>
	<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/bootstrap-slider.min.js" ></script>-->
	<script src="https://use.fontawesome.com/24a0b7748b.js"></script>

<style>
.slider.slider-vertical .slider-tick, .slider.slider-vertical .slider-handle {
   
    margin-left: -5px !important;
}

.bigBox {
height: 50px !important;
}
</style>

<!-- TITULO DE LA PAGINA DEPENDIENDO EL PERFIL -->
<div class="row">
  <div class="col-xs-12 col-sm-7 col-md-7 col-lg-5">
	<h4><i class="fa fa-user-secret" aria-hidden="true"></i> > <?php echo ObtenEtiqueta(1620); ?> </h4><br/>
	<h5><?php echo ObtenEtiqueta(1621); ?></h4>
	<br/>
  </div>
</div> 
		<div class="row">
			<div class="col-md-6">
					<div class="panel panel-default"> 
					  <div class="panel-body">					
						<div class="row" style="height:240px;">
              <div class="col-md-7" ><br>
									<h4 style="color:#0071BD;"><b><i class="fa fa-child" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo ObtenEtiqueta(1622); ?></b></h4><br/>
									<p ><?php echo ObtenEtiqueta(1625); ?></p>
							</div>
							<div class="col-md-5">
									<form class="smart-form">
											<fieldset>
												<section >
													<div class="row">
														<div class="col col-12">
															<label class="radio <?php echo $bloked;?> ">
																<input name="radio"  type="radio" id="opc1"  <?php echo $check;?> >
																<i></i><b><?php echo ObtenEtiqueta(1623); ?></b></label>
															<label class="radio <?php echo $bloked;?>">
																<input name="radio"  type="radio" id="opc2" <?php echo $check2; ?>>
																<i></i><b><?php echo ObtenEtiqueta(1624); ?></b></label>
														</div>
													</div>
												</section>
											</fieldset>
										</form>
							</div>
						</div>
					  </div>
					</div>	
			</div>
			<script>
			</script>
			<div class="col-md-6">
					<div class="panel panel-default">
					  <div class="panel-body">
							<div class="row"> 
                <div class="col-md-7 text-left"><br/>
										<h4 style="color:#0071BD;"><b><i class="fa fa-university" aria-hidden="true"></i> <?php echo ObtenEtiqueta(1626); ?></b></h4><br/>
										<p><?php echo ObtenEtiqueta(1362); ?></p>
								
								</div>
								<div class="col-md-5" style="padding:15px;">
									
									<input id="ex21" type="text" 
								  data-provide="slider"
								  data-slider-ticks="[1, 2, 3, 4]"
								  data-slider-orientation="vertical"
								  data-slider-ticks-labels='["<b><?php echo ObtenEtiqueta(1627); ?></b>","<b><?php echo ObtenEtiqueta(1628); ?></b>", "<b><?php echo ObtenEtiqueta(1629); ?></b>", "<b><?php echo ObtenEtiqueta(1630); ?></b>"]'
								  data-slider-min="1"
								  data-slider-max="3"
								  data-slider-step="1"
                                  data-slider-enabled="<?php echo $slider_enabled;?>"
								  data-slider-selection = "after"
								  data-slider-value="<?php echo $fg_grade; ?>"
								  data-slider-tooltip="hide" />
								</div>
							</div>
					  </div>
					</div>
			</div>
		</div>
		<div class="row">
      <!-- INTERNATIONAL -->
			<div class="col-md-6">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row" style="height:240px;">
              <div class="col-md-7"><br/>
                <h4 style="color:#0071BD;"><b><i class="fa fa-globe" aria-hidden="true"></i>&nbsp;&nbsp; <?php echo ObtenEtiqueta(1824); ?></b></h4><br/>
                <p><?php echo ObtenEtiqueta(1827); ?></p>
              </div>
              <div class="col-md-5">
                <form class="smart-form">
                  <fieldset>
                    <section >
                      <div class="row">
                        <div class="col col-12">
                          <label class="radio <?php echo $bloked;?>">
                            <input name="radio"  type="radio" id="international_1"  <?php if($fg_international==1) echo "checked"; ?> >
                            <i></i><b><?php echo ObtenEtiqueta(1633); ?></b></label>
                          <label class="radio <?php echo $bloked;?>">
                            <input name="radio"  type="radio" id="international_2" <?php if($fg_international==2) echo "checked"; ?>>
                            <i></i><b><?php echo ObtenEtiqueta(1634); ?></b></label>
                        </div>
                      </div>
                    </section>
                  </fieldset>
                </form>
              </div>
              
            </div>
          </div>
        </div>
			</div>
			
			<!-- EDUCATIONAL -->
      <div class="col-md-6">
        <div class="panel panel-default"> 
          <div class="panel-body">					
            <div class="row" style="height:240px;">
              <div class="col-md-7" >
                <br/>
                <h4 style="color:#0071BD;"><b><i class="fa fa-child" aria-hidden="true"></i>&nbsp;&nbsp; <?php echo ObtenEtiqueta(1829); ?></b></h4></br>
                <p ><?php echo ObtenEtiqueta(1830); ?></p>

              </div>
              <div class="col-md-5">
                <form class="smart-form">
                  <fieldset>
                    <section >
                      <div class="row">
                        <div class="col-12">
                          <label class="radio <?php echo $bloked;?>">
                            <input name="radio"  type="radio" id="educational_1"  <?php if($fg_educational==1) echo "checked"; ?> >
                            <i></i><b><?php echo ObtenEtiqueta(1828); ?></b></label>
                          <label class="radio <?php echo $bloked;?>">
                            <input name="radio"  type="radio" id="educational_2" <?php if($fg_educational==2) echo "checked"; ?>>
                            <i></i><b><?php echo ObtenEtiqueta(1635); ?></b></label>
                        </div>
                      </div>
                    </section>
                  </fieldset>
                </form>
              </div>
              
            </div>
          </div>
        </div>	
			</div>

		</div>
    
    <div class="row">

      <!-- BLOCKING LAST NAME -->
			<div class="col-md-6">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row" style="height:240px;">
              <div class="col-md-7"><br/>
                <h4 style="color:#0071BD;"><b><i class="fa fa-strikethrough" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo ObtenEtiqueta(2067); ?></b></h4><br/>
                <p><?php echo ObtenEtiqueta(2068); ?></p>                
                <p id="info_blocking"></p>                
              </div>
              <div class="col-md-5">
                <form class="smart-form">
                  <fieldset>
                    <section class="col col-5">                      
                      <label class="toggle <?php echo $bloked;?>">
                        <input type="checkbox" name="fg_blocking" id="fg_blocking" 
                        <?php
                        if($fg_blocking==1)
                          echo "checked";
                        ?>>
                        <i data-swchon-text="<?php echo ObtenEtiqueta(16); ?>" data-swchoff-text="<?php echo ObtenEtiqueta(17); ?>"></i>
                      </label>
                    </section>
                  </fieldset>
                </form>
              </div>              
            </div>
          </div>
        </div>
			</div>
			<script>
      var elem = $("#fg_blocking");
      var ele = $("#info_blocking");
      elem.change(function(){
        var check = $(this).is(':checked');        
        if(check==true)
      ele.empty().append("<?php echo ObtenEtiqueta(2070); ?>");
        else
          ele.empty().append("<?php echo ObtenEtiqueta(2069); ?>");
      });
      if(elem.is(':checked')==true)
        ele.empty().append("<?php echo ObtenEtiqueta(2070); ?>");
      else
        ele.empty().append("<?php echo ObtenEtiqueta(2069); ?>");
			</script>
			<!--  -->
      
	  
	  
	  
	  
	  
	  <!-- Parent Authorization -->
	    <div class="col-md-6">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row" style="height:240px;">
              <div class="col-md-7"><br/>
                <h4 style="color:#0071BD;"><b><i class="fa fa-file-text" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo ObtenEtiqueta(2378); ?></b></h4><br/>
                <p><?php echo ObtenEtiqueta(2379); ?></p>                
                <p id="info_blocking"></p>                
              </div>
              <div class="col-md-5">
                <form class="smart-form">
                  <fieldset>
                    <section class="col col-5">                      
                      <label class="toggle <?php echo $bloked;?>">
                        <input type="checkbox" name="fg_parent_authorization" id="fg_parent_authorization" 
                        <?php
                        if($fg_parent_authorization==1)
                          echo "checked";
                        ?>>
                        <i data-swchon-text="<?php echo ObtenEtiqueta(16); ?>" data-swchoff-text="<?php echo ObtenEtiqueta(17); ?>"></i>
                      </label>
                    </section>
                  </fieldset>
                </form>
              </div>              
            </div>
          </div>
        </div>
	    </div>
	  </div>
	  <div class="row">


      <!-- Parent Authorization -->
      <div class="col-md-6">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="row" style="height:240px;">
                  <div class="col-md-7"><br/>
                    <h4 style="color:#0071BD;"><b><i class="fa fa-link" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo ObtenEtiqueta(2570); ?></b></h4><br/>
                    <p><?php echo ObtenEtiqueta(2571); ?></p>                
                    <p id="info_blocking"></p>                
                  </div>
                  <div class="col-md-5">
                    <form class="smart-form">
                      <fieldset>
                        <section class="col col-5">                      
                          <label class="toggle <?php echo $bloked;?>">
                            <input type="checkbox" name="fg_ferpa" id="fg_ferpa" 
                            <?php
                            if($fg_ferpa==1)
                              echo "checked";
                            ?>>
                            <i data-swchon-text="<?php echo ObtenEtiqueta(16); ?>" data-swchoff-text="<?php echo ObtenEtiqueta(17); ?>"></i>
                          </label>
                        </section>
                      </fieldset>
                    </form>
                  </div>              
              </div>
            </div>
          </div>
        </div>

      <!-- Add and Delete -->
      <div class="col-md-6">
        <div class="panel panel-default"> 
          <div class="panel-body">          
            <div class="row" style="height:240px;">
              <div class="col-md-7" >
                <br/>
                <h4 style="color:#0071BD;"><b><i class="fa fa-child" aria-hidden="true"></i>&nbsp;&nbsp; <?php echo ObtenEtiqueta(2607); ?></b></h4></br>
                <p ><?php echo ObtenEtiqueta(2608); ?></p>

              </div>
              <div class="col-md-5">
                <form class="smart-form">
                  <fieldset>
                    <section >
                      <div class="row">
                        <div class="col-12">
                          <label class="toggle <?php echo $bloked;?>">
                            <input name="fg_addStudents"  type="checkbox" id="fg_addStudents"
                            <?php
                            if($fg_addStudents==1)
                              echo "checked";
                            ?>>
                            <i data-swchon-text="<?php echo ObtenEtiqueta(16); ?>" data-swchoff-text="<?php echo ObtenEtiqueta(17); ?>"></i><b><?php echo ObtenEtiqueta(1043); ?></b></label>
                          <label class="toggle <?php echo $bloked;?>">
                            <input name="fg_addTeachers"  type="checkbox" id="fg_addTeachers"
                            <?php
                            if($fg_addTeachers==1)
                              echo "checked";
                            ?>>
                            <i data-swchon-text="<?php echo ObtenEtiqueta(16); ?>" data-swchoff-text="<?php echo ObtenEtiqueta(17); ?>"></i><b><?php echo ObtenEtiqueta(1045); ?></b></label>
                           <label class="toggle <?php echo $bloked;?>">
                            <input name="fg_deletions"  type="checkbox" id="fg_deletions"
                            <?php
                            if($fg_deletions==1)
                              echo "checked";
                            ?>>
                            <i data-swchon-text="<?php echo ObtenEtiqueta(16); ?>" data-swchoff-text="<?php echo ObtenEtiqueta(17); ?>"></i><b><?php echo ObtenEtiqueta(1049); ?></b></label>
                        </div>
                      </div>
                    </section>
                  </fieldset>
                </form>
              </div>
            </div>
          </div>
        </div>  
      </div>
    </div>
	  
	<?php if($fg_privacy<>'1'){?>  
    
    <div class="row">
      <div class="col-md-12 text-center">
        <a href="javascript:void(0);" class="btn btn-primary" style="border-radius:10px;" onclick="GuardarFiltro();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo ObtenEtiqueta(1637);?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
      </div>
		</div>
	<div id="guarda_filtro"></div>	
	<?php } ?>	

<script>
function GuardarFiltro(){
      var fg_grade, fg_international, fg_international1, fg_international2, fg_educational, fg_educational1, fg_educational2,fg_parent_authorization,fg_ferpa;
			if ($('#opc1').is(':checked')) {
                var fg_gender = 1;
            } else {
                var fg_gender = 2;
            }
			if ($('#opc2').is(':checked')) {
                var fg_gender = 2;
            } else {
                var fg_gender = 1;
            }
			fg_grade = document.getElementById('ex21').value;
      // International
      fg_international1 = $("#international_1").is(':checked');
      fg_international2 = $("#international_2").is(':checked');
      if(fg_international1)
        fg_international = 1;
      if(fg_international2)
        fg_international = 2;

      // Educational
      fg_educational1 = $("#educational_1").is(':checked');
      fg_educational2 = $("#educational_2").is(':checked');
      if(fg_educational1)
        fg_educational = 1;
      if(fg_educational2)
        fg_educational = 2;
      var fg_blocking = $("#fg_blocking").is(":checked"), blocking;
      if(fg_blocking==1)
        blocking = 1;
      else
        blocking = 0;
	
      var fg_parent_authorization = $("#fg_parent_authorization").is(":checked"), fg_parent_authorization;
      if(fg_parent_authorization==1)
        fg_parent_authorization = 1;
      else
        fg_parent_authorization = 0;
	
      var fg_ferpa = $("#fg_ferpa").is(":checked"), fg_ferpa;
      if(fg_ferpa==1)
        fg_ferpa = 1;
      else
        fg_ferpa = 0;

      var fg_addStudents = $("#fg_addStudents").is(":checked");
      if (fg_addStudents==1)
        fg_addStudents = 1;
      else
        fg_addStudents = 0;

      var fg_addTeachers = $("#fg_addTeachers").is(":checked");
      if (fg_addTeachers==1)
        fg_addTeachers = 1;
      else
        fg_addTeachers = 0;

      var fg_deletions = $("#fg_deletions").is(":checked");
      if (fg_deletions==1)
        fg_deletions = 1;
      else
        fg_deletions = 0;
	
			$.ajax({
        type: 'POST',
        url: 'site/guardar_filtro_instituto.php',
        data: 'fg_gender='+fg_gender+
              '&fg_grade='+fg_grade+
              '&fg_ferpa='+fg_ferpa+
              '&fg_international='+fg_international+
              '&fg_educational=' + fg_educational+
              '&fg_blocking='+blocking+
			  '&fg_parent_authorization='+fg_parent_authorization+
        '&fg_addStudents='+fg_addStudents+
        '&fg_addTeachers='+fg_addTeachers+
        '&fg_deletions='+fg_deletions,
        async: false,
        success: function (html) {
            $('#guarda_filtro').html(html);

            $.smallBox({
            title : "<i class=\"fa fa-check\" aria-hidden=\"true\"></i> <?php echo ObtenEtiqueta(1638); ?>",
            //content : "Lorem ipsum dolor sit amet, test consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam",
            color : "#739E73",
            timeout: 4000,
            iconSmall : "fa fa-check ",
            //number : "2"
          });
      
          //e.preventDefault();

        }
			});

}
    <?php if($bloked){?>

    document.getElementById("opc1").disabled = true;
    document.getElementById("opc2").disabled = true;
    document.getElementById("international_1").disabled = true;
    document.getElementById("international_2").disabled = true;
    document.getElementById("educational_1").disabled = true;
    document.getElementById("educational_2").disabled = true; 
    document.getElementById("fg_blocking").disabled = true;
    document.getElementById("fg_parent_authorization").disabled = true;
    document.getElementById("fg_ferpa").disabled = true;
    document.getElementById("ex21").disabled = true;
    document.getElementById("fg_addStudents").disabled = true;
    document.getElementById("fg_addTeachers").disabled = true;
    document.getElementById("fg_deletions").disabled = true;
    
<?php
  }    
?>
</script>

