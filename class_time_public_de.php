<?php

# Libreria de funciones
require("modules/common/lib/cam_general.inc.php");



$Query = "SELECT  fl_periodo from c_configuracion WHERE cl_configuracion=171 ";
$row = RecuperaValor($Query);
$fl_periodo = str_texto($row[0]);


?>


<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />

    <title>Vanas</title>
    

</head>
<body>
	
    <br /><br />
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%">
                    <thead>
                        <tr>
                            <th scope="col" style="background-color:#0162c9;color: #fff;">#</th>
                            <th scope="col" style="background-color:#0162c9;color: #fff;">Programmname</th>
                            <th scope="col" style="background-color:#0162c9;color: #fff;">Startdatum</th>
                            <th scope="col" style="background-color:#0162c9;color: #fff;">Grad</th>
                            <th scope="col" style="background-color:#0162c9;color: #fff;">Unterrichtszeit</th>
                            <th scope="col" style="background-color:#0162c9;color: #fff;">Zeitzone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        #Muestra resultados de la busqueda.
                        $Query = "SELECT fl_periodo, nb_periodo ,fe_inicio, ";
                        $Query .= "(SELECT COUNT(1) FROM k_term b JOIN c_programa p ON p.fl_programa=b.fl_programa WHERE b.fl_periodo=a.fl_periodo AND p.fg_archive='0' )AS no_cursos ,  ";
                        $Query .= "CASE WHEN fg_activo='1' THEN 'SI' ELSE 'NO' END fg_activo ";
                        $Query .= "FROM c_periodo a  WHERE a.fl_periodo=$fl_periodo ";



                        $Query .= "ORDER BY fe_inicio ASC ";

                        $rs = EjecutaQuery($Query);
                        for ($i = 1; $row = RecuperaRegistro($rs); $i++) {
                            $fl_periodo = $row['fl_periodo'];
                            $nb_periodo = $row['nb_periodo'];
                            $fe_inicio = $row['fe_inicio'];
                            $no_cursos = $row['no_cursos'];
                            $fg_activo = $row['fg_activo'];
                            $fe_inici = $row['fe_inicio'];




                            $Query = "SELECT a.fl_programa, nb_programa,no_grado ";
                            $Query .= "FROM c_programa a, k_term b ";
                            $Query .= "WHERE a.fl_programa=b.fl_programa ";
                            $Query .= "AND b.fl_periodo=$fl_periodo AND a.fg_archive='0' AND b.no_grado=1 ";
                            $Query .= "ORDER BY no_orden, no_grado ";
                            $rs1 = EjecutaQuery($Query);
                            $no_programs = CuentaRegistros($rs1);
                            $nb_programa = "";
                            $nb_program = "";
                            $cont_pro = 0;
                            for ($ii = 1; $roww = RecuperaRegistro($rs1); $ii++) {

                                $fl_programa = $roww[0];
                                $nb_programa = str_texto($roww[1]);
                                $no_grado = $roww['no_grado'];

                                $cont_pro++

                        ?>


                        <tr>
                            <td>
                                <?php echo $cont_pro;?>
                            </td>
                            <td>
                                <b>
                                    <?php echo $nb_programa;?>
                                </b>
                            </td>
                            <td>
                                <?php echo $nb_periodo;?>
                            </td>
                            <td>
                                <?php echo $no_grado;?>
                            </td>
                            <td>
                                <?php
                                    $que = "SELECT fl_class_time,fl_programa FROM k_class_time WHERE fl_programa=$fl_programa AND fl_periodo=$fl_periodo ";
                                    $rs2 = EjecutaQuery($que);

                                    for ($iii = 1; $rowww = RecuperaRegistro($rs2); $iii++) {
                                        $fl_class_time = $rowww['fl_class_time'];
                                        $fl_programa_class = $rowww[1];

                                        $Wqe = "SELECT CASE WHEN cl_dia='1' THEN 'Montag'
								                          WHEN cl_dia='2' THEN 'Dienstag'
								                          WHEN cl_dia='3' THEN 'Mittwoch'
								                          WHEN cl_dia='4' THEN 'Donnerstag'
								                          WHEN cl_dia='5' THEN 'Freitag'
								                          WHEN cl_dia='6' THEN 'Samstag'
                                                          WHEN cl_dia='7' THEN 'Sonntag' 
								                          ELSE 'Sonntag' END dia ,no_hora,ds_tiempo
					                          FROM k_class_time_programa WHERE fl_class_time=$fl_class_time
					                    ";
                                        $rs3 = EjecutaQuery($Wqe);
                                        $totclass = CuentaRegistros($rs3);
                                        for ($mi = 1; $romi = RecuperaRegistro($rs3); $mi++) {

                                            $nb_di = $romi[0];
                                            $nd_hora = $romi[1];
                                            $ampm = $romi[2];

                                            echo $nb_di . " " . $nd_hora . " " . $ampm;
                                            if ($mi <= ($totclass - 1))
                                               echo", ";
                                            else
                                               echo"";

                                        }

                                    }
                                ?>

                            </td>
                            <td>Pacific Time</td>
                        </tr>


                        <?php
                            }

                        }
                        ?>



                    </tbody>
                </table>
            </div>

        </div>
        <div class="col-md-1"></div>


    </div>




    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>