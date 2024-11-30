<?php 
  # Libreria de funciones
  require("../lib/self_general.php");
  
  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  
  # RecibeParametros
  $fl_instituto = RecibeParametroNumerico('fl_instituto');
  
  # Obtenemos la informacion
  # Obtenemos la informacion de privacidad
  $row0 = RecuperaValor("SELECT fg_gender, fg_grade, fg_educational, fg_international, fg_blocking, fg_ferpa, fg_addStudents, fg_addTeachers, fg_deletions FROM k_instituto_filtro WHERE fl_instituto=$fl_instituto");
  $fg_genderr = $row0['fg_gender'];
  $fg_gradee = $row0['fg_grade'];
  $fg_educational = $row0['fg_educational'];
  $fg_international = $row0['fg_international'];
  $fg_blocking = $row0['fg_blocking'];
  $fg_ferpa=$row0['fg_ferpa'];
  $fg_addStudents=$row0['fg_addStudents'];
  $fg_addTeachers=$row0['fg_addTeachers'];
  $fg_deletions=$row0['fg_deletions'];

  # Genero
  if($fg_genderr==1){
    $gender =  ObtenEtiqueta(1623);
  }
  else{
    $gender =  ObtenEtiqueta(1624);
  }
  # Grado
  if($fg_gradee==1)
    $grades = ObtenEtiqueta(1627);
  else{
    if($fg_gradee==2)
      $grades = ObtenEtiqueta(1628);
    else{
      if($fg_gradee==3)
        $grades = ObtenEtiqueta(1629);
      else
        $grades = ObtenEtiqueta(1630);
    }
  }
  # Educational
  if(!empty($fg_educational)){
    if($fg_educational==1)
      $educational = ObtenEtiqueta(1828);
    if($fg_educational==2)
      $educational = ObtenEtiqueta(1635);
  }
  else
    $educational = ObtenEtiqueta(1889);
  
  # International
  if(!empty($fg_international)){
    if($fg_international==1)
      $international = ObtenEtiqueta(1633);
    if($fg_international==2)
      $international = ObtenEtiqueta(1634);
  }
  else
    $international = ObtenEtiqueta(1889);
  
  # Blocking
  if(!empty($fg_blocking))
    $blocking = ObtenEtiqueta(16);
  else
    $blocking = ObtenEtiqueta(17);
  
  #Ferpa.
  if(!empty($fg_ferpa))
  $fg_ferpa=ObtenEtiqueta(16);
  else
  $fg_ferpa=ObtenEtiqueta(17);

  # Add and Delete
if ($fg_addStudents==0 || empty($fg_addStudents)) {
  $fg_addStudents=ObtenEtiqueta(2611);
} else {
  $fg_addStudents=ObtenEtiqueta(2612);
}
if ($fg_addTeachers==0 || empty($fg_addTeachers)) {
  $fg_addTeachers=ObtenEtiqueta(2611);
} else {
  $fg_addTeachers=ObtenEtiqueta(2612);
}
if ($fg_deletions==0 || empty($fg_deletions)) {
  $fg_deletions=ObtenEtiqueta(2611);
} else {
  $fg_deletions=ObtenEtiqueta(2612);
}
?>

<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        &times;
      </button>
      <h4 class="modal-title" id="myModalLabel"><i class="fa fa-user-secret"></i> <?php echo ObtenEtiqueta(1820); ?></h4>
    </div>
    <div class="modal-body">      
      <div class="row">
        <div class="col col-sm-12 col-md-12 col-lg-2 text-align-center" style="padding-top:6%;">
          <i style='font-size:95px; color:#e3e3e3 ;' class='fa fa-user-secret fa-5x' ></i>
        </div>
        <div class="col col-sm-12 col-md-12 col-lg-10">
          <div class="row padding-10">
            <h6 class="no-margin" style="font-weight: 100; font-size:medium;"><?php echo ObtenEtiqueta(1821); ?></h6>
          </div>
          <br/>
          <div class="row">        
            <div class="col col-sm-12 col-md-12 col-lg-12 padding-top-10">
            <div class="col-sm-6 text-align-center padding-10">
              <label style="color:#0071BD; font-weight: 100; font-size:medium;"><i class="fa fa-child" aria-hidden="true"></i> <?php echo ObtenEtiqueta(1822); ?></label>
              <div  style="font-weight: 50; font-size:medium;">&nbsp;&nbsp;<?php echo $gender; ?></div>
            </div>
            <div class="col-sm-6 text-align-center padding-10">
              <label style="color:#0071BD; font-weight: 100; font-size:medium;"><i class="fa fa-university" aria-hidden="true"></i> <?php echo ObtenEtiqueta(1823); ?></label>
              <div style="font-weight: 50; font-size:medium;"><?php echo $grades; ?></div>
            </div>
            </div>        
          </div>
          <div class="row padding-top-10">
            <div class="col col-sm-12 col-md-12 col-lg-12">
              <div class="col col-sm-6 text-align-center padding-10">
                <label style="color:#0071BD; font-weight: 100; font-size:medium;"><i class="fa fa-globe" aria-hidden="true"></i> <?php echo ObtenEtiqueta(1824); ?></label>
                <div style="font-weight: 50; font-size:medium;"><?php echo $international; ?></div>
              </div>
              <div class="col col-sm-6 text-align-center padding-10">
                <label style="color:#0071BD; font-weight: 100; font-size:medium;"><i class="fa fa-globe" aria-hidden="true"></i> Educational Partners</label>
                <div style="font-weight: 50; font-size:medium;"><?php echo $educational; ?></div>
              </div>
            </div>
          </div>
          <div class="row padding-top-10">
            <div class="col col-sm-12 col-md-12 col-lg-12">
              <div class="col col-sm-6 text-align-center padding-10">
                <label style="color:#0071BD; font-weight: 100; font-size:medium;"><i class="fa fa-strikethrough" aria-hidden="true"></i> <?php echo ObtenEtiqueta(2067); ?></label>
                <div style="font-weight: 50; font-size:medium;"><?php echo $blocking; ?></div>
              </div>
              <div class="col col-sm-6 text-align-center padding-10">     
                <label style="color:#0071BD; font-weight: 100; font-size:medium;"><i class="fa fa-link" aria-hidden="true"></i> <?php echo ObtenEtiqueta(2570); ?></label>
                <div style="font-weight: 50; font-size:medium;"><?php echo $fg_ferpa; ?></div>
              </div>
            </div>
          </div>
          <div class="row padding-top-10">
            <div class="col col-sm-12 col-md-12 col-lg-12">
              <div class="col col-sm-6 text-align-center padding-10">
                <label style="color:#0071BD; font-weight: 100; font-size:medium;"><i class="fa fa-child" aria-hidden="true"></i> <?php echo ObtenEtiqueta(2609); ?></label>
                <div style="font-weight: 50; font-size:medium;"><?php echo $fg_addStudents; ?></div>
              </div>
              <div class="col col-sm-6 text-align-center padding-10">     
                <label style="color:#0071BD; font-weight: 100; font-size:medium;"><i class="fa fa-user" aria-hidden="true"></i> <?php echo ObtenEtiqueta(2610); ?></label>
                <div style="font-weight: 50; font-size:medium;"><?php echo $fg_addTeachers; ?></div>
              </div>
            </div>
          </div>
          <div class="row padding-top-10">
            <div class="col col-sm-12 col-md-12 col-lg-12">
              <div class="col col-sm-6 text-align-center padding-10">
                <label style="color:#0071BD; font-weight: 100; font-size:medium;"><i class="fa fa-minus-square" aria-hidden="true"></i> <?php echo "Delete users"; ?></label>
                <div style="font-weight: 50; font-size:medium;"><?php echo $fg_deletions; ?></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">
        Cancel
      </button>
    </div>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->