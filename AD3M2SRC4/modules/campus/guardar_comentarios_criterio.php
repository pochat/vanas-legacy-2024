<?php
 # Libreria de funciones
  require '../../lib/general.inc.php';


$fl_criterio=RecibeParametroNumerico('fl_criterio');
$ds_comentarios=RecibeParametroHTML('ds_descripcion');
$no_calificacion=RecibeParametroNumerico('no_calificacion');
$cl_sesion=RecibeParametroHTML('fl_alumno');
$fl_programa=RecibeParametroNumerico('fl_programa');
$es_comentario_final= RecibeParametroNumerico('comen_final');
$fg_comentario_criterio=RecibeParametroNumerico('fg_comentario_crietrio');
$fg_guardar_todo=RecibeParametroNumerico('fg_guardar_todo');
$ds_comentario_final_teacher=RecibeParametroHTML('ds_comentarios');
$rangeInput=RecibeParametroNumerico('rangeInput');

  
  
  
    #Ejecura acciones si son comentarios de cada criterio
    if($fg_comentario_criterio==1){


	        #Verificamos si existe 
            $Query="SELECT fl_criterio FROM c_com_criterio_admin WHERE fl_criterio=$fl_criterio AND cl_sesion='$cl_sesion'  AND fl_programa=$fl_programa ";
	        $row=RecuperaValor($Query);
	        $existe=$row[0];
	
	            if($existe){
	
		            $Query="UPDATE c_com_criterio_admin SET ds_comentarios='$ds_comentarios',fe_modificacion=CURRENT_TIMESTAMP ";
                    if(!empty($rangeInput))
                    $Query.=", no_porcentaje_equivalente=$rangeInput ";    
                    $Query.="WHERE fl_criterio=$fl_criterio  AND cl_sesion='$cl_sesion'  AND fl_programa=$fl_programa  ";
		            EjecutaQuery($Query);
	
	                    #Recuperamos la fecha de modificacion para presentarla 
                    $Query="SELECT fe_modificacion FROM c_com_criterio_admin WHERE fl_criterio=$fl_criterio  AND cl_sesion='$cl_sesion'  AND fl_programa=$fl_programa   ";
			            $row=RecuperaValor($Query);
			            $fe_modificacion=$row[0];
			
			            $fe_modificacion=strtotime('+0 day',strtotime($fe_modificacion));
			            $fe_modificacion= date('Y-m-d H:i:s',$fe_modificacion );
			            #DAMOS FORMATO DIA,MES, AÑO.
			            $date = date_create($fe_modificacion);
			            $fe_modificacion=date_format($date,'F j , Y , g:i a');
	
	
	
	            }else{
			            #Eliminamos el comentario del teacher
                    EjecutaQuery("DELETE FROM c_com_criterio_admin WHERE fl_criterio=$fl_criterio  AND cl_sesion='$cl_sesion'  AND fl_programa=$fl_programa   ");
			
			
			
			            #Recupermos el peso que tiene el criterio.
			            $Query="SELECT no_valor 
                                FROM k_criterio_curso a
                                JOIN c_criterio b ON b.fl_criterio=a.fl_criterio
                                WHERE b.fl_criterio=$fl_criterio AND fl_programa=$fl_programa  ";
			            $row=RecuperaValor($Query);
			            $no_porcentaje=$row[0];
			
			            $no_porcentaje_criterio= ( $no_porcentaje * $rangeInput ) /100 ;
			
			            #Inserta comentarios de cada criterio.
			            $Query="INSERT INTO c_com_criterio_admin (fl_criterio,no_porcentaje_equivalente,ds_comentarios,cl_sesion,fl_programa,fe_creacion,fe_modificacion)";
			            $Query.="VALUES ($fl_criterio,$rangeInput,'$ds_comentarios','$cl_sesion',$fl_programa, CURRENT_TIMESTAMP,CURRENT_TIMESTAMP )";
			            $fl_comentario=EjecutaInsert($Query);

			            #Recuperamos la fecha de modificacion para presentarla 
			            $Query="SELECT fe_modificacion FROM c_com_criterio_admin WHERE fl_comentario_admin= $fl_comentario ";
			            $row=RecuperaValor($Query);
			            $fe_modificacion=$row[0];
			
			            $fe_modificacion=strtotime('+0 day',strtotime($fe_modificacion));
			            $fe_modificacion= date('Y-m-d H:i:s',$fe_modificacion );
			            #DAMOS FORMATO DIA,MES, AÑO.
			            $date = date_create($fe_modificacion);
			            $fe_modificacion=date_format($date,'F j , Y , g:i a');
                   } 
        
		
		
		
		
      
    }

    if($es_comentario_final==1){#Quiere decir que es comentario final de la rubric.
  
       
        #Eliminamos el comentario del teacher
        EjecutaQuery("DELETE FROM c_com_criterio_admin WHERE  cl_sesion='$cl_sesion' AND fg_com_final='1' AND fl_programa=$fl_programa ");
   
    
        #Inserta comentario final del teacher.
        $Query="INSERT INTO c_com_criterio_admin (ds_comentarios,cl_sesion,fl_programa,fe_creacion,fe_modificacion,fg_com_final )";
        $Query.="VALUES ('$ds_comentario_final_teacher','$cl_sesion',$fl_programa,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'1' )";
        $fl_comentario_admin=EjecutaInsert($Query);
        
        #Recuperamos la fecha de modificacion para presentarla 
        $Query="SELECT fe_modificacion FROM c_com_criterio_admin WHERE fl_comentario_admin= $fl_comentario_admin ";
        $row=RecuperaValor($Query);
        $fe_modificacion=$row[0];
        
        $fe_modificacion=strtotime('+0 day',strtotime($fe_modificacion));
        $fe_modificacion= date('Y-m-d H:i:s',$fe_modificacion );
        #DAMOS FORMATO DIA,MES, AÑO.
        $date = date_create($fe_modificacion);
        $fe_modificacion=date_format($date,'F j , Y , g:i a');
       

    }

    if($fg_guardar_todo==1){#Quiere decir que se guarda ya todo la calificacion asignada del teacher

        
        #Verificamos si existen comentarios del teacher por cada criterio., entonces recorremos todas las calificaciones asignadas. 
        $Queryc="SELECT fl_criterio,no_porcentaje_real FROM c_calculo_criterio_temp_admin WHERE cl_sesion='$cl_sesion' AND fl_programa=$fl_programa ORDER BY fl_criterio ASC  ";
        $rs2 = EjecutaQuery($Queryc);
        for($i=1;$row2=RecuperaRegistro($rs2);$i++){
                    
              $fl_criterio_in=$row2['fl_criterio'];
              $no_porcentaje_in=$row2['no_porcentaje_real'];
             
              
                 #Verificamos si existe un comentario asignado del teacher por cada calificacion(rangeInput).
              $Queryb="SELECT fl_comentario_admin FROM c_com_criterio_admin WHERE fl_criterio=$fl_criterio_in AND  cl_sesion='$cl_sesion' AND fl_programa=$fl_programa ";
                 $rowb=RecuperaValor($Queryb);
                 $ds_comentario_teacher=$rowb[0];

                 #Si no existe el cometario inserta un comentario vacio
                 if(empty($ds_comentario_teacher)){
                     
                
                     #Inserta comentarios de cada criterio.
                     $Query="INSERT INTO c_com_criterio_admin (fl_criterio,no_porcentaje_equivalente,ds_comentarios,cl_sesion,fl_programa,fe_creacion,fe_modificacion)";
                     $Query.="VALUES ($fl_criterio_in,$no_porcentaje_in,'','$cl_sesion',$fl_programa,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP )";
                     $fl_comentario=EjecutaInsert($Query);

                 }
                          
        }
        
        
        #Verificamos si existe un comentario general del  teacher.
        $Queryc="SELECT fl_comentario_admin,ds_comentarios FROM c_com_criterio_admin WHERE cl_sesion='$cl_sesion' AND fl_programa=$fl_programa AND fg_com_final='1'  ";
        $rowc=RecuperaValor($Queryc);
        $ds_comentario_final_teacher=$rowc[0];
        $ds_com_final=str_texto($rowc[1]);
        if(empty($ds_comentario_final_teacher)){
        
            #Inserta comentario final del teacher.
            $Query="INSERT INTO c_com_criterio_admin (ds_comentarios,cl_sesion,fl_programa,fe_creacion,fe_modificacion,fg_com_final)";
            $Query.="VALUES ('','$cl_sesion',$fl_programa,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'1' )";
            $fl_comentario_final_teacher=EjecutaInsert($Query);
        
        }
        
        
       
        #Eliminamos el comentario del teacher.
        EjecutaQuery("DELETE FROM k_calificacion_admin WHERE  cl_sesion='$cl_sesion' AND fl_programa=$fl_programa   ");
        
        
        
        
        
        #Sumamos todos los que pernetencen al alumno, programa y leccion.
        $Query ="SELECT SUM(no_porcentaje) FROM c_calculo_criterio_temp_admin  WHERE cl_sesion='$cl_sesion' AND fl_programa=$fl_programa   ";
        $row=RecuperaValor($Query);
        $no_calificacion=$row[0];

        
	
		
		
    
            $Query="INSERT INTO k_calificacion_admin (cl_sesion,fl_programa,ds_comentarios,no_calificacion) ";
            $Query.="VALUES('$cl_sesion',$fl_programa,'$ds_com_final',$no_calificacion)";
            $fl_calificacion_teacher=EjecutaInsert($Query);

			
            #Verificamos en que rango se encuentra y se le asigna su promedio 
            $Query="SELECT fl_calificacion, no_min,no_max  FROM c_calificacion WHERE 1=1 ";
            $rs=EjecutaQuery($Query);
           
            for($i=1;$row=RecuperaRegistro($rs);$i++){
                $no_min=$row[1];
                $no_max=$row[2];

                if( ($no_calificacion>=$no_min) && ($no_calificacion<=$no_max)){
                
                        $fl_promedio=$row[0];
                
                } 
            
            }
            
            #Actualizamos el app form
            $Query="UPDATE c_sesion SET fg_calificado='1',fl_promedio=$fl_promedio WHERE cl_sesion='$cl_sesion' ";     
            EjecutaQuery($Query);

            
			#Actualizamos la fecha de odificacion 
			$Query="UPDATE c_com_criterio_admin SET fe_modificacion=CURRENT_TIMESTAMP WHERE cl_sesion='$cl_sesion' AND fl_programa=$fl_programa AND fg_com_final='1' ";
			EjecutaQuery($Query);
            
             
     
			
		
    }




if(empty($fg_guardar_todo)){#solo presenta cuando se ejecutan los cometarios del tecaher.
?>
<h2 style="margin: 2px 4px; line-height: 60%;font-size:21px;"><i><small><?php echo ObtenEtiqueta(1680)." :<br/>".$fe_modificacion; ?></small></i></h2>


<?php
}



?>