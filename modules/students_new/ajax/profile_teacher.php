<?php
  require("../../common/lib/cam_general.inc.php");

  # Recibe parametros
  $fl_maestro = RecibeParametroNumerico('profile_id');
  if(empty($fl_maestro))
    $fl_maestro = RecibeParametroNumerico('profile_id', True);

  function GetProfile($fl_maestro){

    # Query for teacher info
    $Query  = "SELECT ds_nombres, ds_apaterno, ds_amaterno, fg_genero, DATE_FORMAT(fe_nacimiento, '%c') 'fe_mes', ";
    $Query .= "DATE_FORMAT(fe_nacimiento, '%e') 'fe_dia_anio', ds_email, fl_pais, fl_zona_horaria, ds_ruta_avatar, ds_ruta_foto, ";
    $Query .= "ds_empresa, ds_website, ds_gustos, ds_pasatiempos, ds_biografia ";
    $Query .= "FROM c_usuario a, c_maestro b ";
    $Query .= "WHERE a.fl_usuario=b.fl_maestro ";
    $Query .= "AND fl_usuario=$fl_maestro";
    $row = RecuperaValor($Query);
    $ds_nombres = str_uso_normal($row[0]);
    $ds_apaterno = str_uso_normal($row[1]);
    $ds_amaterno = str_uso_normal($row[2]);
    $fg_genero = $row[3];
    $fe_nacimiento = ObtenNombreMes($row[4])." ".$row[5];
    $ds_email = str_uso_normal($row[6]);
    $fl_pais = $row[7];
    $fl_zona_horaria = $row[8];
    $ds_ruta_avatar = $row[9];
    $ds_ruta_foto = str_uso_normal($row[10]);
    $ds_empresa = str_uso_normal($row[11]);
    $ds_website = str_uso_normal($row[12]);
    $ds_gustos = str_uso_normal($row[13]);
    $ds_pasatiempos = str_uso_normal($row[14]);
    $ds_biografia = str_uso_normal($row[15]);

    $result["profile"] = array();
    $result["teaching"] = array();

    if($fg_genero == 'M')
      $gender = ObtenEtiqueta(115);
    else
      $gender = ObtenEtiqueta(116);

    $row  = RecuperaValor("SELECT ds_pais FROM c_pais WHERE fl_pais=$fl_pais");
    $ds_pais = str_uso_normal($row[0]);

    if(!empty($ds_ruta_avatar)){
      $ds_ruta_avatar = "<img src='".PATH_MAE_IMAGES."/avatars/$ds_ruta_avatar'>";
    } else {
      $ds_ruta_avatar = "<img src='".SP_IMAGES."/".IMG_T_AVATAR_DEF."'>";
    }
    if(!empty($ds_ruta_foto)) {
      $ds_ruta_foto = "<img src='".PATH_MAE_IMAGES."/pictures/$ds_ruta_foto'>";
    } else {
      $ds_ruta_foto = "<img src='".PATH_N_COM_IMAGES."/vanas-family-edutisse-header.jpg'>";
    }
    
    $ds_web = "<a href='http://$ds_website' target='_blank'>$ds_website</a>";

    $result["profile"] += array(
      "name" => $ds_nombres." ".$ds_apaterno,
      "profile" => ObtenEtiqueta(421),
      "gender" => $gender,
      "birthday" => $fe_nacimiento,
      "email" => $ds_email,
      "country" => $ds_pais,
      "avatar" => $ds_ruta_avatar,
      "photo" => $ds_ruta_foto,
      "website" => $ds_web,
      "interest" => $ds_gustos,
      "hobby" => $ds_pasatiempos,
      "bio" => $ds_biografia
    );

    # Teaching Programs
    $Query  = "SELECT DISTINCT c.nb_programa, a.nb_grupo ";
    $Query .= "FROM c_grupo a, k_term b, c_programa c ";
    $Query .= "WHERE a.fl_term=b.fl_term ";
    $Query .= "AND b.fl_programa=c.fl_programa ";
    $Query .= "AND a.fl_maestro=$fl_maestro ";
    $rs = EjecutaQuery($Query);

    for($i=0; $row = RecuperaRegistro($rs); $i++){
      $result["teaching"] += array(
        $i => str_uso_normal($row[0]).": &nbsp;&nbsp;".str_uso_normal($row[1])
      );
    }

    echo json_encode((Object)$result);
  }
?>

<div class="row">
  <div class="col-sm-12">
      <div class="well well-sm">

        <div class="row">

          <!-- hidden for now will be implemented in the future  -->
          <div class="hidden-xs hidden-sm hidden-md hidden-lg">
            <div class="well well-sm">
              <p>world map of all the students and teachers</p>
            </div>
          </div>

          <!-- <div class="col-xs-12 col-sm-6 col-md-offset-3 col-lg-offset-3"> -->
          <div class="col-xs-12">
            <div class="well well-light well-sm no-margin no-padding">

              <div class="row">

                <div class="col-sm-12">
                  <div id="myCarousel" class="carousel fade profile-carousel">
                    <ol class="carousel-indicators"></ol>
                    <div class="carousel-inner">
                      <div id="user-header-pic" class="item active"></div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="row">
                    <div id="user-profile-pic" class="col-sm-3 profile-pic">
                      <!-- <div class="padding-10">
                        <h4 class="font-md"><strong>1,543</strong>
                        <br>
                        <small>Followers</small></h4>
                        <br>
                        <h4 class="font-md"><strong>419</strong>
                        <br>
                        <small>Connections</small></h4>
                      </div> -->
                    </div>
                    <div class="col-sm-6">
                      <h1 class="font-xl semi-bold" id="user-profile-name"></h1>

                      <ul id="user-profile-info" class="list-unstyled"></ul>
                      
                      <br>
                      <!-- <a href="javascript:void(0);" class="btn btn-default btn-xs"><i class="fa fa-envelope-o"></i> Send Message</a> -->
                      <br>
                      <br>

                    </div>
                    <div class="col-sm-3">
                      <!-- <h1><small>Connections</small></h1>
                      <ul class="list-inline friends-list">
                        <li><img src=""></li>
                        <li><img src=""></li>
                        <li><img src=""></li>
                        <li><img src=""></li>
                        <li><img src=""></li>
                        <li><img src=""></li>
                        <li><a href="javascript:void(0);">413 more</a></li>
                      </ul>

                      <h1><small>Recent visitors</small></h1>
                      <ul class="list-inline friends-list">
                        <li><img src=""></li>
                        <li><img src=""></li>
                        <li><img src=""></li>
                      </ul> -->
                    </div>

                  </div>

                </div>

              </div>
            </div>
          </div>
          <!-- portfolio panel -->
          <!-- hidden for now will be implemented in the future  -->
          <div class="hidden-xs hidden-sm hidden-md hidden-lg">
            <div class="well well-sm">
              <p>For Portfolio, Student Art work etc... </p>
            </div>
          </div> 

        </div>
      </div>

  </div>
  
</div>

<script type="text/javascript">
  var teacher = <?php GetProfile($fl_maestro); ?>;

  $("#user-profile-pic").append(teacher.profile.avatar);
  $("#user-profile-name").append(teacher.profile.name);
  $("#user-header-pic").append(teacher.profile.photo);

  $("#user-profile-info").append("<li><p class='text-muted font-md'>Profile: "+teacher.profile.profile+"</p></li>");
  if(teacher.profile.gender == "Male"){
    //$("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-male'>&nbsp;&nbsp;"+teacher.profile.gender+"</p></li>");
  } else {
    //$("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-female'>&nbsp;&nbsp;"+teacher.profile.gender+"</p></li>");
  }

  $("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-gift'>&nbsp;&nbsp;"+teacher.profile.birthday+"</p></li>");
  $("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-envelope'>&nbsp;&nbsp;"+teacher.profile.email+"</p></li>");
  $("#user-profile-info").append("<li><p class='text-muted font-md'><i class='fa fa-globe'>&nbsp;&nbsp;"+teacher.profile.country+"</p></li>");
  $("#user-profile-info").append("<li><p class='text-muted font-md'>Bio: "+teacher.profile.bio+"</p></li>");

  $("#user-profile-info").append("<br>");

  $("#user-profile-info").append("<li><p class='text-muted font-md'>Website: "+teacher.profile.website+"</p></li>");
  $("#user-profile-info").append("<li><p class='text-muted font-md'>Interest: "+teacher.profile.interest+"</p></li>");
  $("#user-profile-info").append("<li><p class='text-muted font-md'>Hobby: "+teacher.profile.hobby+"</p></li>");

  $("#user-profile-info").append("<br>");


  $("#user-profile-info").append("<li><p class='font-md'>Teaching Programs</p></li>");
  for (var k in teacher.teaching){
    $("#user-profile-info").append("<li><p class='text-muted font-sm'>"+teacher.teaching[k]+"</p></li>");
  }
</script>
