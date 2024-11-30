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
  
  # Recibe parametros
  $fl_programa_sp = RecibeParametroNumerico('fl_programa');
  # Si es 2 muestra las categorias
  # Si es 3 muestra Course outline
  $type = RecibeParametroNumerico('type');

?>

  <!-- HTML BEGIN HERE -->
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
    &times;
  </button>
  <h3 class="modal-title" id="TitleModalCat"><b><center><?php if($type==2) echo ObtenEtiqueta(1789); else echo ObtenEtiqueta(1295); ?></center></b></h3>
</div>
<div class="modal-body">
  <?php if($type==2){ ?>
<div class="table-responsive" style="overflow: auto;">	
  <table class='table' width="100%">
  <tbody>
  <!-- FIEL OF STUDY -->
  <tr style="border-top: 0px solid #ddd; padding:3px;"> 
    <td class="text-right" style="border-top: 0px solid #ddd;"><b><?php echo ObtenEtiqueta(1306).":"; ?></b></td>
    <td style="border-top: 0px solid #ddd;">
      <?php
        #Query en general
        $Querry = "SELECT COUNT(*) FROM c_categoria_programa_sp c LEFT JOIN k_categoria_programa_sp k ON(c.fl_cat_prog_sp = k.fl_cat_prog_sp) WHERE k.fl_programa_sp = $fl_programa_sp ";
        $Querry1 ="SELECT c.nb_categoria FROM c_categoria_programa_sp c LEFT JOIN k_categoria_programa_sp k ON(c.fl_cat_prog_sp = k.fl_cat_prog_sp) WHERE k.fl_programa_sp = $fl_programa_sp ";
        $tot_fiel = RecuperaValor($Querry." AND c.fg_categoria = 'FOS'");            
        if(!empty($tot_fiel[0])){
          $rs_p = EjecutaQuery($Querry1." AND c.fg_categoria = 'FOS'");
          for($i_p=0;$i_p<$row_p=RecuperaRegistro($rs_p);$i_p++)
            echo "<span class='label label-primary' style='font-weight: normal; padding: .1em .6em .0em; font-size: 95%; background-color:#DE8294; border-color:#DE8294;'>".str_texto($row_p[0])."</span>&nbsp;";
        }
      ?>									   
    </td>
  </tr>

  <!-- CATEGORIAS -->
  <tr style="border-top: 0px solid #ddd;padding:3px;">
    <td class="text-right" style="border-top: 0px solid #ddd;"><b><?php echo ObtenEtiqueta(1307).":"; ?></b></td>
    <td style="border-top: 0px solid #ddd;">
    <?php
      $tot_cat = RecuperaValor($Querry." AND c.fg_categoria = 'CAT'");
      if(!empty($tot_cat[0])){
        $rs_c = EjecutaQuery($Querry1." AND c.fg_categoria = 'CAT'");
        for($i_c=0;$i_c<$row_c=RecuperaRegistro($rs_c);$i_c++)
          echo "<span class='label label-primary' style='font-weight: normal; padding: .1em .6em .0em; font-size: 95%; background-color:#DE82C9; border-color:#DE82C9;'>".str_texto($row_c[0])."</span>&nbsp;";
      }else{
        echo "&nbsp;"; 
      }
    ?>
    </td>
  </tr>
  
  <!-- GRADE -->
  <tr style="border-top: 0px solid #ddd;padding:3px;">
    <td class="text-right" style="border-top: 0px solid #ddd;"><b><?php echo ObtenEtiqueta(1308).":"; ?></b></td>
    <td style="border-top: 0px solid #ddd;">
    <?php  
      $tot_gra = RecuperaValor("SELECT g.nb_grado FROM k_grade_programa_sp r, k_grado_fame g WHERE fl_programa_sp = $fl_programa_sp AND r.fl_grado = g.fl_grado");
      if(!empty($tot_gra[0])){
        $rs_g = EjecutaQuery("SELECT g.nb_grado FROM k_grade_programa_sp r, k_grado_fame g WHERE fl_programa_sp = $fl_programa_sp AND r.fl_grado = g.fl_grado");
        for($i_g=0;$i_g<$row_g=RecuperaRegistro($rs_g);$i_g++)
          echo "<span class='label label-primary' style='font-weight: normal; padding: .1em .6em .0em; font-size: 95%; background-color:#B482DE; border-color:#B482DE;'>".str_texto($row_g[0])."</span>&nbsp;";
      }else{
        echo "&nbsp;"; 
      }
    ?>
    </td>
  </tr>
  
   <!-- HARDWARE -->
  <tr style="border-top: 0px solid #ddd;padding:3px;">
  <td class="text-right" style="border-top: 0px solid #ddd;"><b><?php echo ObtenEtiqueta(1309).":"; ?></b></td>
  <td style="border-top: 0px solid #ddd;">
  <?php 
    $tot_har = RecuperaValor($Querry." AND c.fg_categoria = 'HAR'");
    if(!empty($tot_har[0])){
      $rs_h = EjecutaQuery($Querry1." AND c.fg_categoria = 'HAR'");
      for($i_h=0;$i_h<$row_h=RecuperaRegistro($rs_h);$i_h++)
        echo "<span class='label label-primary' style='font-weight: normal; padding: .1em .6em .0em; font-size: 95%; background-color:#8682DE; border-color:#8682DE;'>".str_texto($row_h[0])."</span>&nbsp;";
    }else{
      echo "&nbsp;"; 
    }
  ?>									
  </td>
  </tr>
    
  <!-- SOFTWARE -->
  <tr style="border-top: 0px solid #ddd;padding:3px;">
  <td class="text-right" style="border-top: 0px solid #ddd;"><b><?php echo ObtenEtiqueta(1310).":"; ?></b></td>
  <td style="border-top: 0px solid #ddd;">
    <?php 
        $tot_sof = RecuperaValor($Querry." AND c.fg_categoria = 'SOF'");
        if(!empty($tot_sof[0])){
          $rs_s = EjecutaQuery($Querry1." AND c.fg_categoria = 'SOF'");
          for($i_s=0;$i_s<$row_s=RecuperaRegistro($rs_s);$i_s++)
            echo "<span class='label label-primary' style='font-weight: normal; padding: .1em .6em .0em; font-size: 95%; background-color:#82A5DE; border-color:#82A5DE;'>".str_texto($row_s[0])."</span>&nbsp;";
        }else{
          echo "&nbsp;"; 
        }
    ?>
  
  </td>
  </tr>
  
  <!-- LEVEL -->
  <tr style="border-top: 0px solid #ddd;padding:3px;">
  <td class="text-right" style="border-top: 0px solid #ddd;"><b><?php echo ObtenEtiqueta(1311).":"; ?></b></td>
  <td style="border-top: 0px solid #ddd;">
  <?php
    $row_lvl = RecuperaValor("SELECT fg_level FROM c_programa_sp WHERE fl_programa_sp = $fl_programa_sp");
      if(empty($row_lvl[0])){
        $ds_level = "";
      }else{
        switch ($row_lvl[0]){
          case 'LVB': $ds_level = ObtenEtiqueta(1317); break;
          case 'LVI': $ds_level = ObtenEtiqueta(1321); break;
          case 'LVA': $ds_level = ObtenEtiqueta(1322); break;
        }
      }
    echo "<dd style='padding-bottom:2px;'><span class='label label-primary' style='font-weight: normal; padding: .1em .6em .0em; font-size: 95%; background-color:#82D7DE; border-color:#82D7DE;'>".$ds_level."</span>&nbsp;</dd>"
  ?>
  </td>
  </tr>
  <!-- CODIGO DE CURSO -->
  <tr style="border-top: 0px solid #ddd;padding:3px;">
  <td class="text-right" style="border-top: 0px solid #ddd;"><b><?php echo ObtenEtiqueta(1312).":"; ?></b></td>
  <td style="border-top: 0px solid #ddd;">
  <?php
    $row_cc = RecuperaValor("SELECT ds_course_code FROM c_programa_sp WHERE fl_programa_sp = $fl_programa_sp");
    if(!empty($row_cc[0])){
      echo "<dd style='padding-bottom:2px;'><span class='label label-primary' style='font-weight: normal; padding: .1em .6em .0em; font-size: 95%; background-color:#82DEB4; border-color:#82DEB4;'>".str_texto($row_cc[0])."</span>&nbsp;</dd>";
    }else{
      echo "&nbsp;";
    }
  ?>									
  </td>
  </tr>
  <!-- CURSO PREREQUISITO -->
  <tr style="border-top: 0px solid #ddd;padding:3px;">
  <td class="text-right" style="border-top: 0px solid #ddd;"><b><?php echo ObtenEtiqueta(1313).":"; ?></b></td>
  <td style="border-top: 0px solid #ddd;">
  <?php 
    $Querry2 = "SELECT c.nb_programa".$sufix." FROM k_relacion_programa_sp k, c_programa_sp c WHERE k.fl_programa_sp_act = $fl_programa_sp AND k.fl_programa_sp_rel = c.fl_programa_sp ";
      $tot_cup = RecuperaValor($Querry2." AND fg_puesto = 'ANT'");
      if(!empty($tot_cup[0])){
        $rs_cp = EjecutaQuery($Querry2." AND fg_puesto = 'ANT'");
        for($i_cp=0;$i_cp<$row_cp=RecuperaRegistro($rs_cp);$i_cp++)
          echo "<span class='label label-primary' style='font-weight: normal; padding: .1em .6em .0em; font-size: 95%; background-color:#82DE82; border-color:#82DE82;'>".str_texto($row_cp[0])."</span>&nbsp;";
    }else{
      echo "&nbsp;";
    }
  ?>									
  </td>
  </tr>
  
  <!-- CURSO SIGUIENTE -->
  <tr style="border-top: 0px solid #ddd;padding:3px;">
  <td class="text-right" style="border-top: 0px solid #ddd;"><b><?php echo ObtenEtiqueta(1314).":"; ?></b></td>
  <td style="border-top: 0px solid #ddd;">									
  <?php 
    $tot_cus = RecuperaValor($Querry2." AND fg_puesto = 'SIG'");                                                
    if(!empty($tot_cus[0])){
      $rs_cs = EjecutaQuery($Querry2." AND fg_puesto = 'SIG'");
      for($i_cs=0;$i_cs<$row_cs=RecuperaRegistro($rs_cs);$i_cs++){
        echo "<span class='label label-primary' style='font-weight: normal; padding: .1em .6em .0em; font-size: 95%; background-color:#C2DE82; border-color:#C2DE82;'>".str_texto($row_cs[0])."</span>&nbsp; ";
      }
    }else{
      echo "&nbsp;";
    }
  ?>									
  </td>
  </tr>

  <!-- Maping Curriculum -->
  <tr style="border-top: 0px solid #ddd;padding:3px;">
  <td class="text-right" style="border-top: 0px solid #ddd;"><b><?php echo ObtenEtiqueta(2056).":"; ?></b></td>
  <td style="border-top: 0px solid #ddd;">									
  <?php 
  
    $Querry="SELECT ds_contenido FROM c_programa_sp WHERE fl_programa_sp=$fl_programa_sp ";
    $row=RecuperaValor($Querry);
    $ds_contenido=$row[0];

    #Query en general
    $Querry = " SELECT  CASE WHEN C.fl_instituto IS NULL THEN CONCAT (C.nb_course_code,' (code: ',C.cl_course_code,') - ',P.ds_pais,', ',E.ds_provincia)										
		ELSE CONCAT (C.nb_course_code,' (code: ',C.cl_course_code,') - ',P.ds_pais,' - ".ObtenEtiqueta(2626)." ',F.ds_instituto) END AS nb_course,C.fl_course_code
        FROM k_course_code_prog_fame S
		JOIN c_course_code C ON C.fl_course_code=S.fl_course_code
		JOIN c_pais P ON P.fl_pais=C.fl_pais
		LEFT JOIN k_provincias E ON E.fl_provincia=C.fl_estado 
        LEFT JOIN c_instituto F ON F.fl_instituto=C.fl_instituto 
		WHERE S.fl_programa_sp=$fl_programa_sp ";
    $rs_c = EjecutaQuery($Querry);

    for($i_c=0;$i_c<$row_c=RecuperaRegistro($rs_c);$i_c++){
      
      $fl_curriculum=$row_c[1];

      if($i_c%2==0){
        $espacio="<br style='line-height:.9;'/><br/>";
      }else{
        $espacio="&nbsp;";
      }
			
      echo "<span class='label label-primary' data-toggle='modal' href='#myModal2' Onclick='PresentaContenidoCurricumMap($fl_curriculum,$fl_programa_sp);' style='font-weight: normal; padding: .1em .6em .0em; font-size: 95%; background-color:#5f9dca; border-color:#5f9dca;'>".str_texto($row_c[0])."</span>".$espacio." ";
    }
  ?>									
  </td>
  </tr>
  </tbody>
  </table>
  </div>
<?php
  }else{
    $Query  = "SELECT nb_programa".$sufix.", ds_learning".$sufix.", ds_metodo".$sufix.", ds_requerimiento".$sufix.", ds_programa".$sufix.", nb_programa, ds_learning ";
    $Query .= "FROM c_programa_sp WHERE fl_programa_sp=$fl_programa_sp ";
    $row = RecuperaValor($Query);
    $nb_programa = str_texto(!empty($row[0])?$row[0]:NULL);
    $ds_learning = str_uso_normal(!empty($row[1])?$row[1]:NULL);
    $ds_metodo = str_uso_normal(!empty($row[2])?$row[2]:NULL);
    $ds_requerimiento = str_uso_normal(!empty($row[3])?$row[3]:NULL);
    $ds_programa = str_uso_normal(!empty($row[4])?$row[4]:NULL);
?>
<div class="row">
  <div class="col-md-12" style="overflow-y: scroll; height:400px; color:#999; padding:0px 50px; 0px 50px;">
    <h5><b><?php echo ObtenEtiqueta(1298); ?></b></h5>                  
    <p><?php echo $ds_programa; ?></p>
    <br>                  
    <h5><b><?php echo ObtenEtiqueta(1300); ?></b></h5>                  
    <p><?php echo $ds_learning; ?></p>
    <br>                  
    <h5><b><?php echo ObtenEtiqueta(1302); ?></b></h5>                  
    <p><?php echo $ds_metodo; ?></p>
    <br>                  
    <h5><b><?php echo ObtenEtiqueta(1304); ?></b></h5>                  
    <p><?php echo $ds_requerimiento ?></p>
    <br>                  
  </div>
</div>
</div>
<?php
  }
?>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-primary" data-dismiss="modal">
    Close
  </button>
</div>
