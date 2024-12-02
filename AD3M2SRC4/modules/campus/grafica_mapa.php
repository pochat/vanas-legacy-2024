<?php
  
  # Libreria general de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea 1 hra
  ValidaSesion( );
  
  $fg_edad=RecibeParametroNumerico('fg_edad');
  $fe_ini=RecibeParametroFecha('fe_ini');
  $fe_fin=RecibeParametroFecha('fe_fin');
  
  if(!empty($fe_ini))
      $fe_ini = "'".ValidaFecha($fe_ini)."'";
  if(!empty($fe_fin))
      $fe_fin = "'".ValidaFecha($fe_fin)."'";


  # Countries - basic
  $Query2  = "SELECT ds_pais,d.fl_pais, COUNT(1) AS cantidad ";
  $Query2 .= "FROM c_usuario a ";
  $Query2 .= "LEFT JOIN k_ses_app_frm_1 b ON b.cl_sesion=a.cl_sesion ";
  $Query2 .= "LEFT JOIN c_pais d ON d.fl_pais=b.ds_add_country ";
  $Query2 .= "WHERE fl_perfil=3 ";
  if(  (!empty($fe_ini)) &&(!empty($fe_fin)) )
  $Query2 .="AND fe_alta>=$fe_ini AND fe_alta<=$fe_fin ";
  $Query2 .= "GROUP BY ds_pais ORDER BY cantidad DESC ";
  $rs1 = EjecutaQuery($Query2);
  $rs2 = EjecutaQuery($Query2);
  $rs5 = EjecutaQuery($Query2);
  $total_cantidad = 0;
  $tabla='';
  for($i=0; $row5=RecuperaRegistro($rs5); $i++){
    $cantidad=$row5[2];
    $ds_pais=str_texto($row5[0]);
	$total_cantidad +=$cantidad;

      #Genermos los datos.
      $tabla.="
      <tr>
        <td>$ds_pais</td>
        <td>$cantidad</td>
		<td>&nbsp;</td>
      </tr>
       ";

  }

?>

    <style type="text/css">
        body {
            color: #5d5d5d;
            font-family: Helvetica, Arial, sans-serif;
        }

        h1 {
            font-size: 30px;
            margin: auto;
            margin-top: 50px;
        }

        .container {
            max-width: 800px;
            margin: auto !important;
        }
        
        /* Specific mapael css class are below
         * 'mapael' class is added by plugin
        */

        .mapael .map {
            position: relative;
        }

        .mapael .mapTooltip {
            position: absolute;
            background-color: #000;
            moz-opacity: 0.70;
            opacity: 0.70;
            filter: alpha(opacity=70);
            border-radius: 10px;
            padding: 10px;
            z-index: 1000;
            max-width: 200px;
            display: none;
            color: #fff;
        }

        .mapael .areaLegend {
            margin-bottom: 20px;
        }
    </style>

    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"
            charset="utf-8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.2.7/raphael.min.js" charset="utf-8"></script>
    <script src="<?php echo PATH_JS?>/vector_map/jquery.mapael.js" charset="utf-8"></script>
    <script src="<?php echo PATH_JS?>/vector_map/maps/france_departments.js" charset="utf-8"></script>
    <script src="<?php echo PATH_JS?>/vector_map/maps/world_countries.js" charset="utf-8"></script>
    <script src="<?php echo PATH_JS?>/vector_map/maps/usa_states.js" charset="utf-8"></script>

    <script type="text/javascript">
        $(function () {
            $(".mapcontainer").mapael({
                map: {
                    name: "world_countries",
                    defaultArea: {
                        attrs: {
                            stroke: "#fff",
                            "stroke-width": 1
                        }
                    }
                },
                legend: {
                    area: {
                        mode: "horizontal",
                        title: "Countries population",
                        labelAttrs: {
                            "font-size": 12
                        },
                        marginLeft: 5,
                        marginLeftLabel: 5,
                        slices: [
                            {
                                max: 5000000,
                                attrs: {
                                    fill: "#6489aa"
                                },
                                label: "< 5 millions"
                            },
                            {
                                min: 5000000,
                                max: 10000000,
                                attrs: {
                                    fill: "#459bd9"
                                },
                                label: "> 5 millions and < 10 millions"
                            },
                            {
                                min: 10000000,
                                max: 50000000,
                                attrs: {
                                    fill: "#2579b5"
                                },
                                label: "> 10 millions and < 50 millions"
                            },
                            {
                                min: 50000000,
                                attrs: {
                                    fill: "#1a527b"
                                },
                                label: "> 50 millions"
                            }
                        ]
                    },
                    plot: {
                        mode: "horizontal",
                        title: "Cities population",
                        labelAttrs: {
                            "font-size": 12
                        },
                        marginLeft: 5,
                        marginLeftLabel: 5,
                        slices: [
                            {
                                max: 500000,
                                attrs: {
                                    fill: "#f99200"
                                },
                                attrsHover: {
                                    transform: "s1.5",
                                    "stroke-width": 1
                                },
                                label: "< 500 000",
                                size: 10
                            },
                            {
                                min: 500000,
                                max: 1000000,
                                attrs: {
                                    fill: "#f99200"
                                },
                                attrsHover: {
                                    transform: "s1.5",
                                    "stroke-width": 1
                                },
                                label: "> 500 000 and 1 million",
                                size: 20
                            },
                            {
                                min: 1000000,
                                attrs: {
                                    fill: "#f99200"
                                },
                                attrsHover: {
                                    transform: "s1.5",
                                    "stroke-width": 1
                                },
                                label: "> 1 million",
                                size: 30
                            }
                        ]
                    }
                },
                plots: {
                    
                },
                areas: {
					
				<?php 

					for($m=0; $row2=RecuperaRegistro($rs2); $m++){
						$ds_pais=$row2[0];
						$fl_pais=$row2[1];
						$cantidad=$row2[2];
						
        						
						$Query="SELECT cl_iso2 FROM c_pais WHERE fl_pais=$fl_pais ";
						$ro=RecuperaValor($Query);
						$cl_iso=$ro[0];
					?>

					"<?php echo $cl_iso;?>": {
                        "value": "<?php echo $cantidad; ?>",
                        "attrs": {
                           // "href": "http://www.google.com"
                        },
                        "tooltip": {
                            "content": "<span style=\"font-weight:bold;\"><?php echo $ds_pais;?><\/span><br \/>Total : <?php echo $cantidad; ?>"
                        }
                    },
					
						
				    <?php
					}
					
					
					?>
					
					
					/*
					
                    "AF": {
                        "value": "15",
                        "attrs": {
                            "href": "http://www.google.com"
                        },
                        "tooltip": {
                            "content": "<span style=\"font-weight:bold;\">Afghanistan<\/span><br \/>Population : 25 persona"
                        }
                    },
					*/
					
					
					
                    
                }
            });
        });
    </script>


    <div class="row">
	
		<div class="col-md-5">
		<div style="solid .5px #ddd;">
		 <table class="table table-bordered table-striped table-hover table-condensed"  id="tabla_mapa" >
		<thead>
		  <tr>
			<th width="90%">Country</th>
			<th>Total</th>
		    <th>&nbsp;</th>
		  </tr>
		</thead>
		<tbody>
		  <?php echo $tabla;?>
		</tbody>
    </table></div>
	  
		</div>
	
	
	
	
		<div class="col-md-7">
			<div class="mapcontainer">
				<div class="map">
					
				</div>
				
				
			</div>
		</div>	
	</div>

  
	  
   




<script>
    $(document).ready(function () {
        $('#tabla_mapa').DataTable({
			"order": [[ 2, "desc" ]],
		"iDisplayLength": 5,
		"aLengthMenu": [[5, 10, 20,40,100, -1], [5,10,20,40, 100, "All"]],
		
		
		});

		
    });

   

</script>

