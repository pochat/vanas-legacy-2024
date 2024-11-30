<?php
	#librerias propias de FAME.

   require '../fame/lib/self_general.php';
	
	
	
	##sE UTILIZO PARA ACTUALIZAR TODOS LOS PRECISO DE LOS ISTITUTOS.
	/*#EjecutaQuery('DELETE FROM c_princing ');
	
	$Query="SELECT fl_instituto FROM c_instituto WHERE 1=1 order by fl_instituto ASC ";
	$rs1 = EjecutaQuery($Query);
	for($i2=1;$row=RecuperaRegistro($rs1);$i2++){
	
	   $fl_instituto=$row['fl_instituto'];
	   
	   
       $Query="SELECT fl_princing FROM c_princing ORDER BY fl_princing DESC  ";
	   $row=RecuperaValor($Query);
	   $fl_princing=$row[0]+1;
	  
	   
	   $Query="INSERT INTO c_princing (fl_princing,fl_instituto,no_ini,no_fin,ds_descuento_mensual,mn_mensual,ds_descuento_anual,mn_anual,mn_descuento_licencia,fg_activo) ";
	   $Query.="VALUES($fl_princing,$fl_instituto,1,9, 15,28.99,0,24.99,0,'1' ) ";
	   $fl_princing=EjecutaInsert($Query);
	   
       $Query="SELECT fl_princing FROM c_princing ORDER BY fl_princing DESC  ";
	   $row=RecuperaValor($Query);
	   $fl_princing=$row[0]+1;
	   
	   
	   $Query="INSERT INTO c_princing (fl_princing,fl_instituto,no_ini,no_fin,ds_descuento_mensual,mn_mensual,ds_descuento_anual,mn_anual,mn_descuento_licencia,fg_activo) ";
	   $Query.="VALUES($fl_princing,$fl_instituto,10,19, 20,26.99,0,22.99,7,'1' ) ";
	   $fl_princing=EjecutaInsert($Query);
	   
       $Query="SELECT fl_princing FROM c_princing ORDER BY fl_princing DESC  ";
	   $row=RecuperaValor($Query);
	   $fl_princing=$row[0]+1;
	   
	   
	   $Query="INSERT INTO c_princing (fl_princing,fl_instituto,no_ini,no_fin,ds_descuento_mensual,mn_mensual,ds_descuento_anual,mn_anual,mn_descuento_licencia,fg_activo) ";
	   $Query.="VALUES($fl_princing,$fl_instituto,20,29, 26,24.99,0,20.99,14,'1')";
	   $fl_princing=EjecutaInsert($Query);
	   
       $Query="SELECT fl_princing FROM c_princing ORDER BY fl_princing DESC  ";
	   $row=RecuperaValor($Query);
	   $fl_princing=$row[0]+1;
	   
	
	  
	   $Query="INSERT INTO c_princing (fl_princing,fl_instituto,no_ini,no_fin,ds_descuento_mensual,mn_mensual,ds_descuento_anual,mn_anual,mn_descuento_licencia,fg_activo) ";
	   $Query.="VALUES($fl_princing,$fl_instituto,30,59, 33,22.99,0,18.99,21,'1')";
	   $fl_princing=EjecutaInsert($Query);
	   
	   $Query="SELECT fl_princing FROM c_princing ORDER BY fl_princing DESC  ";
	   $row=RecuperaValor($Query);
	   $fl_princing=$row[0]+1;
	
	
	
	   $Query="INSERT INTO c_princing (fl_princing,fl_instituto,no_ini,no_fin,ds_descuento_mensual,mn_mensual,ds_descuento_anual,mn_anual,mn_descuento_licencia,fg_activo) ";
	   $Query.="VALUES($fl_princing,$fl_instituto,60,99, 40,20.99,0,16.99,28,'1')";
	   $fl_princing=EjecutaInsert($Query);
	   
       $Query="SELECT fl_princing FROM c_princing ORDER BY fl_princing DESC  ";
	   $row=RecuperaValor($Query);
	   $fl_princing=$row[0]+1;
	   
	   
	   
	   $Query="INSERT INTO c_princing (fl_princing,fl_instituto,no_ini,no_fin,ds_descuento_mensual,mn_mensual,ds_descuento_anual,mn_anual,mn_descuento_licencia,fg_activo) ";
	   $Query.="VALUES($fl_princing,$fl_instituto,100,199, 91.38,2.50,0,2.50,0,'1')";
	   $fl_princing=EjecutaInsert($Query);
	   
	   $Query="SELECT fl_princing FROM c_princing ORDER BY fl_princing DESC  ";
	   $row=RecuperaValor($Query);
	   $fl_princing=$row[0]+1;
	   
	
	
	   $Query="INSERT INTO c_princing (fl_princing,fl_instituto,no_ini,no_fin,ds_descuento_mensual,mn_mensual,ds_descuento_anual,mn_anual,mn_descuento_licencia,fg_activo) ";
	   $Query.="VALUES($fl_princing,$fl_instituto,200,299, 93.53,0,0,1.88,0,'0')";
	   $fl_princing=EjecutaInsert($Query);
	   
	   $Query="SELECT fl_princing FROM c_princing ORDER BY fl_princing DESC  ";
	   $row=RecuperaValor($Query);
	   $fl_princing=$row[0]+1;
	   
	   $Query="INSERT INTO c_princing (fl_princing,fl_instituto,no_ini,no_fin,ds_descuento_mensual,mn_mensual,ds_descuento_anual,mn_anual,mn_descuento_licencia,fg_activo) ";
	   $Query.="VALUES($fl_princing,$fl_instituto,300,499, 95.15,0,0,1.41,0,'0')";
	   $fl_princing=EjecutaInsert($Query);
	   
	   $Query="SELECT fl_princing FROM c_princing ORDER BY fl_princing DESC  ";
	   $row=RecuperaValor($Query);
	   $fl_princing=$row[0]+1;
	   	   
	   $Query="INSERT INTO c_princing (fl_princing,fl_instituto,no_ini,no_fin,ds_descuento_mensual,mn_mensual,ds_descuento_anual,mn_anual,mn_descuento_licencia,fg_activo) ";
	   $Query.="VALUES($fl_princing,$fl_instituto,500,999, 96.36,0,0,1.05,0,'0')";
	   $fl_princing=EjecutaInsert($Query);
	   
	   $Query="SELECT fl_princing FROM c_princing ORDER BY fl_princing DESC  ";
	   $row=RecuperaValor($Query);
	   $fl_princing=$row[0]+1;
	   
	   
	      	   
	   $Query="INSERT INTO c_princing (fl_princing,fl_instituto,no_ini,no_fin,ds_descuento_mensual,mn_mensual,ds_descuento_anual,mn_anual,mn_descuento_licencia,fg_activo) ";
	   $Query.="VALUES($fl_princing,$fl_instituto,1000,2999, 97.27,0,0,0.79,0,'0')";
	   $fl_princing=EjecutaInsert($Query);
	   
	   $Query="SELECT fl_princing FROM c_princing ORDER BY fl_princing DESC  ";
	   $row=RecuperaValor($Query);
	   $fl_princing=$row[0]+1;
	
	
	   $Query="INSERT INTO c_princing (fl_princing,fl_instituto,no_ini,no_fin,ds_descuento_mensual,mn_mensual,ds_descuento_anual,mn_anual,mn_descuento_licencia,fg_activo) ";
	   $Query.="VALUES($fl_princing,$fl_instituto,3000,5999, 97.95,0,0,0.59,0,'0')";
	   $fl_princing=EjecutaInsert($Query);
	   
       $Query="SELECT fl_princing FROM c_princing ORDER BY fl_princing DESC  ";
	   $row=RecuperaValor($Query);
	   $fl_princing=$row[0]+1;
	
	
	
	
	       	   
	   $Query="INSERT INTO c_princing (fl_princing,fl_instituto,no_ini,no_fin,ds_descuento_mensual,mn_mensual,ds_descuento_anual,mn_anual,mn_descuento_licencia,fg_activo) ";
	   $Query.="VALUES($fl_princing,$fl_instituto,6000,11999, 98.47,0,0,0.44,0,'0')";
	   $fl_princing=EjecutaInsert($Query);
	   $fl_princing=$fl_princing+1;
	   
       $Query="SELECT fl_princing FROM c_princing ORDER BY fl_princing DESC  ";
	   $row=RecuperaValor($Query);
	   $fl_princing=$row[0]+1;
       
       
	            	   
	   $Query="INSERT INTO c_princing (fl_princing,fl_instituto,no_ini,no_fin,ds_descuento_mensual,mn_mensual,ds_descuento_anual,mn_anual,mn_descuento_licencia,fg_activo) ";
	   $Query.="VALUES($fl_princing,$fl_instituto,12000,15000, 98.85,0,0,0.33,0,'0')";
	   $fl_princing=EjecutaInsert($Query);
	   
	
	
	
	}
	
	*/
	
	$Query="SELECT fl_instituto FROM c_instituto WHERE 1=1 order by fl_instituto ASC ";
	$rs1 = EjecutaQuery($Query);
	for($i2=1;$row=RecuperaRegistro($rs1);$i2++){
	
	        $fl_instituto=$row['fl_instituto'];
			
			#$Query2="SELECT fl_princing FROM c_princing WHERE fl_instituto=$fl_instituto AND no_ini=100 AND no_fin=199 ";
			#$roe = RecuperaValor($Query2);
			#for($i2=1;$row=RecuperaRegistro($rs1);$i2++){
			     #  $fl_princing=$roe['fl_princing'];      
			   			 
						 EjecutaQuery("UPDATE c_princing SET fg_activo='0',mn_mensual=0 where fl_instituto=$fl_instituto AND no_ini=100 AND no_fin=199  ");
			
			
			#}
			
	
	
	}
	
	
	
	
	
	
	
	
?>