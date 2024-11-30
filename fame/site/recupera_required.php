<?php 

# Libreria de funciones
require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  $perfil_usuario = ObtenPerfilUsuario($fl_usuario);
  
  #Verficamos si es alumno de vanas para colocar boton de pagar curso o invitar a otro compadre.
  if($perfil_usuario==PFL_ESTUDIANTE_SELF)
  $fg_puede_liberar_curso=PuedeLiberarCurso($fl_instituto,$fl_usuario);
 
 # Accion principal
  $accion = $_REQUEST['accion'];
  # Valor (cadena)a buscar
  $fl_programa = $_REQUEST['fl_programa']; 
  # Valor (cadena)a buscar
  $fl_playlist = $_REQUEST['fl_playlist'];
  # Valor (cadena)a buscar
  $nb_playlist = $_REQUEST['nb_playlist'];

  switch ($accion) {
  	case 'addallrequired': // Add all the courses to the playlist
      if ($flplaylist == "" AND $nb_playlist != "") {
        $fl_playlist=EjecutaInsert("INSERT INTO c_playlist (no_grados, nb_playlist, fl_usuario) VALUES (0, '".$nb_playlist."', $fl_usuario)");
      }
      if (!empty($fl_playlist)) {
        $Query = "SELECT c.nb_programa, fl_programa_sp_rel FROM k_relacion_programa_sp k, c_programa_sp c WHERE k.fl_programa_sp_act = $fl_programa AND k.fl_programa_sp_rel = c.fl_programa_sp AND fg_puesto = 'SIG'";
        $rs = EjecutaQuery($Query);

        for($i=0;$row = RecuperaRegistro($rs);$i++){
          $row_count = RecuperaValor("SELECT COUNT(1) FROM k_playlist_course WHERE fl_programa_sp = $row[1] AND fl_playlist_padre = $fl_playlist ");

                # Query to know the next no_orden in k_playlist_course
                $no_orden = RecuperaValor("SELECT (SELECT IF((SELECT no_orden FROM k_playlist_course k WHERE fl_playlist_padre = $fl_playlist ORDER BY no_orden DESC LIMIT 1) IS NULL, 1, (SELECT no_orden FROM k_playlist_course k WHERE fl_playlist_padre = $fl_playlist ORDER BY no_orden DESC LIMIT 1)+1 )) AS no_orden;");
                if(empty($row_count[0]))
            EjecutaQuery("INSERT INTO k_playlist_course (fl_programa_sp, no_orden, fl_playlist_padre) VALUES ($row[1], $no_orden[0], $fl_playlist)");
        }
      }
  		break;

  	case 'checkallrequired': // Recoveer all the required courses
  		$Query = "SELECT c.nb_programa, nb_thumb FROM k_relacion_programa_sp k, c_programa_sp c WHERE k.fl_programa_sp_act = $fl_programa AND k.fl_programa_sp_rel = c.fl_programa_sp AND fg_puesto = 'SIG'";
		$rs = EjecutaQuery($Query);

    $respond = "<style type='text/css'>
                ul.timeline {
                list-style-type: none;
                position: relative;
                }
                ul.timeline:before {
                    content: ' ';
                    background: #d4d9df;
                    display: inline-block;
                    position: absolute;
                    left: 29px;
                    width: 2px;
                    height: 100%;
                    z-index: 400;
                }
                ul.timeline > li {
                    margin: 20px 0;
                    padding-left: 20px;
                }
                ul.timeline > li:before {
                    content: ' ';
                    background: white;
                    display: inline-block;
                    position: absolute;
                    border-radius: 50%;
                    border: 3px solid #22c0e8;
                    left: 20px;
                    width: 20px;
                    height: 20px;
                    z-index: 400;
                }
              </style>";

    $respond .= "<div class='row'>
                <div class='col-md-12 padding-10'>
                <h4>".ObtenEtiqueta(2582)."</h4>
                <ul class='timeline'>";
		//$respond = "<ul style='text-decoration-color: #333'>";

		for($i=0;$row = RecuperaRegistro($rs);$i++){
			$respond .= "<li style='widht: 90%;'>
                  <h6 class='padding-8'>
                  
                   ".$row[0]."
                  </h6>
                  <img src='/AD3M2SRC4/modules/fame/uploads/".$row[1]."' style='height: 60px; margin-right: 20px;'>
                  </li>";
		}
		$respond .= '</ul>
                </div>
                </div>';

		echo $respond;
  		break;
  	
  	default:
  		# code...
  		break;
  }

?>
