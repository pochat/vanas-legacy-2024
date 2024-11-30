<?php

	# Libreria de funciones
	require("../lib/self_general.php");

  $fl_usuario_sp = ValidaSesion(False,0, True);

  $Query = "SELECT fl_cat_prog_sp, fl_fto_cat_sp FROM k_filtro_categoria_fame WHERE fl_usuario_sp = $fl_usuario_sp";
  $rs = EjecutaQuery($Query);

  ?>

  <label><?php echo ObtenEtiqueta(1256); ?></label>
  <div class="select2-container select2-container-multi select2" id="s2id_datos" style="width:100%">
    <ul class="select2-choices">
      <?php
        for($i=0;$row = RecuperaRegistro($rs);$i++){
          // echo "row = ' $row[0]'<br>";
          if(is_numeric($row[0])){
            $fl_cat_prog_sp = $row[0];
            $fl_cat_prog_sp_del = $row[0];
          }elseif((substr($row[0], 0, 2))=="P-"){
            $fl_cat_prog_sp = substr($row[0], 2);
            $fl_cat_prog_sp_del = $row[0];
          }elseif((substr($row[0], 0, 2))=="G-"){
            $fl_cat_prog_sp = $row[0];
            $fl_cat_prog_sp_del = $row[0];
          }elseif((substr($row[0], 0, 2))=="K-"){
            $fl_cat_prog_sp = $row[0];
            $fl_cat_prog_sp_del = $row[0];
          }        
          else{
            $fl_cat_prog_sp = $row[0];
            $fl_cat_prog_sp_del = $row[0];
          }
          
          // echo "fl_cat_prog_sp = ' $fl_cat_prog_sp'<br>";
          
          if(is_numeric($fl_cat_prog_sp)){          
            $cont_val = RecuperaValor("SELECT fl_programa_sp FROM k_categoria_programa_sp WHERE fl_cat_prog_sp = $fl_cat_prog_sp");
            // if(!empty($cont_val[0]))
            if(is_numeric($row[0]))
              $Query_2  = "SELECT  a.nb_categoria, a.fl_cat_prog_sp, a.fg_categoria FROM c_categoria_programa_sp a, k_categoria_programa_sp b WHERE b.fl_cat_prog_sp = $fl_cat_prog_sp AND a.fl_cat_prog_sp = b.fl_cat_prog_sp  ";
            else
              $Query_2 = "SELECT CONCAT(nb_programa".$sufix.", ' (code: ', ds_course_code ,')') as programa, fl_programa_sp FROM c_programa_sp WHERE fl_programa_sp = $fl_cat_prog_sp  AND fg_publico='1' ";
            
            // echo $Query_2;
            
            $row_2 = RecuperaValor($Query_2);
            $nb_categoria = str_texto($row_2[0]);
            $fl_cat_prog_sp = ($row_2[1]);
            $fg_categoria = str_texto($row_2[2]);
            switch ($fg_categoria){
              case 'CAT': $color = "background-color:#DE82C9; border-color:#DE82C9;"; break;
              case 'SOF': $color = "background-color:#82A5DE; border-color:#82A5DE;"; break;
              case 'HAR': $color = "background-color:#8682DE; border-color:#8682DE;"; break;
              case 'CCE': $color = "background-color:#82DE82; border-color:#82DE82;"; break;
              case 'CCS': $color = "background-color:#C2DE82; border-color:#C2DE82;"; break;
              case 'FOS': $color = "background-color:#DE8294; border-color:#DE8294;"; break;
            }
          }
          else{  

                    # Niveles
                    $color=NULL;
                    switch ($row[0]){
                      case 'LVB': $color = "background-color:#82D7DE; border-color:#82D7DE;"; break;
                      case 'LVI': $color = "background-color:#82D7DE; border-color:#82D7DE;"; break;
                      case 'LVA': $color = "background-color:#82D7DE; border-color:#82D7DE;"; break;
                    }
          
                    if((substr($row[0], 0, 2))=="G-"){
                    $grado = substr($row[0], 2);
                    $row_grado = RecuperaValor("SELECT nb_grado, fl_grado FROM k_grado_fame WHERE fl_grado = $grado");
                    $nb_categoria = $row_grado[0];
                        $color = "background-color:#B482DE;; border-color:#B482DE;";
              
                    }elseif((substr($row[0],0,2))=="K-"){
                        
                        $course_code = substr($row[0], 2);
                        $row_course=RecuperaValor("SELECT CONCAT (C.nb_course_code,' (code: ',C.cl_course_code,') - ',P.ds_pais,', ',E.ds_provincia) as course_code,C.fl_course_code
                                                    FROM c_course_code C
                                                    JOIN c_pais P ON P.fl_pais=C.fl_pais 
                                                    JOIN k_provincias E ON E.fl_provincia=C.fl_estado WHERE C.fl_course_code=$course_code AND EXISTS ( SELECT 1 FROM k_course_code_prog_fame D JOIN c_programa_sp M  WHERE D.fl_course_code=C.fl_course_code AND fg_publico='1'  )  ");
                        $nb_categoria = $row_course[0];
                        $color = "background-color:#5f9dca; border-color:#5f9dca;";
                    
                    }else{
                      switch ($fl_cat_prog_sp){
                        case 'LVB': $nb_categoria = ObtenEtiqueta(1317); break;
                        case 'LVI': $nb_categoria = ObtenEtiqueta(1321); break;
                        case 'LVA': $nb_categoria = ObtenEtiqueta(1322); break;
                        $color = "background-color:#82D7DE;; border-color:#82D7DE;"; 
                      }
                    }
          }

          echo '<li class="select2-search-choice" style="font-size: 100%; font-weight: normal; '."$color".' ">
                  <div style="color:#fff;">'."$nb_categoria".'</div>
                  <a href="javascript:DelFto('.$row[1].'); FtaCatSugerencias(); MtraFtoCatSel(); MtraResFtos();" class="select2-search-choice-close" tabindex="-1"></a>
                </li>';
                #FiltraCategorias('."'$fl_cat_prog_sp_del'".', 2, 0); ActualizaFtoCat(); MuestraFiltro();                       
        }
      ?>
    </ul>
  </div>