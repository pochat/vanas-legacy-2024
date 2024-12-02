<?php 

	 $Queryt  = "INSERT INTO k_term (fl_programa, fl_periodo, no_grado, fl_term_ini,fg_data_temporal) ";
	 $Queryt .= "VALUES($fl_programa, $fl_periodo, $no_grado, $fl_term_ini,'1')";
	 $fl_term = EjecutaInsert($Queryt);

	 #Genera los pagos
	 $Query  = "SELECT no_a_payments, no_b_payments, no_c_payments, no_d_payments, no_semanas ";
	 $Query .= "FROM k_programa_costos ";
	 $Query .= "WHERE fl_programa=$fl_programa ";
	 $row = RecuperaValor($Query);
	 $no_payments[1] = $row[0];
	 $no_payments[2] = $row[1];
	 $no_payments[3] = $row[2];
	 $no_payments[4] = $row[3];
	 $no_semanas = $row[4];

	 # Obtenemos loa fecha inicial del periodo que eligio y los meses que dura 
	 $row = RecuperaValor("SELECT ".  ConsultaFechaBD('fe_inicio', FMT_FECHA)." FROM c_periodo WHERE fl_periodo=$fl_periodo");    
	 $fe_inicio = str_texto($row[0]);
	 $fe_pago_firt = date('d-m-Y',strtotime('-2 week',strtotime ($fe_inicio )));
	 $duracion_meses = $no_semanas/4;
	 for($i=1;$i<=4;$i++){
		 for($j=1;$j<=$no_payments[$i];$j++){
			 $meses = $duracion_meses/$no_payments[$i];
			 # Verificamos si encontro un break utiliza la fecha guardada
			 if($encontro){
				 $fe_pago =  $fe_guardarda;
			 }
			 # Aumenta los meses que dura los pagos
			 if($j==1){
				 $fe_pago = $fe_pago_firt;
			 }else{
				 $fe_pago = date('Y-m-d',strtotime('+'.$meses.' month '.$fe_pago.''));
			 }
			 # Busca que la fecha no se encuentre en un break
			 $Query = "SELECT  fe_ini FROM c_break WHERE '$fe_pago' BETWEEN fe_ini AND fe_fin ";
			 $row =  RecuperaValor($Query);
			 $fe_ini = $row[0];

			 # Si existe un registro reducira 4 dias antes la fecha inicial del break
			 if(!empty($fe_ini)){
				 $fe_guardarda = $fe_pago;
				 $fe_pago = date('Y-m-d',strtotime ( '-4 day' , strtotime ( $fe_ini ) ) );
				 $encontro = True;
			 }
			 else{
				 $fe_pago =$fe_pago ;
				 $encontro = False;
			 }
			 
			 # Formato de fecha de pagos
			 $fe_pago = date('Y-m-d', strtotime($fe_pago));
			 #Insertamos las fechas de pagos
			 $Query_pagos  = "INSERT INTO k_term_pago (fl_term,no_opcion,no_pago,fe_pago) ";
			 $Query_pagos .= "VALUES ($fl_term,$i,$j,'$fe_pago') ";
			 EjecutaQuery($Query_pagos);
		 }
	 }
	 fe_ini_fe_fin($fl_term);



	 //PROCEDE A GENERAR LAS SEMANAS.
	 $row = RecuperaValor("SELECT fe_inicio FROM c_periodo WHERE fl_periodo=$fl_periodo");
	 $anio_ini = substr($row[0], 0, 4);
	 $mes_ini = substr($row[0], 5, 2);
	 $dia_ini = substr($row[0], 8, 2);
	 $fe_inicio = date_create( );
	 if($fg_frecuencia == 1)
	 {
		 $limite_entrega = ObtenConfiguracion(23);
		 $limite_calificacion = ObtenConfiguracion(24);
	 }
	 else
	 {
		 $limite_entrega = ObtenConfiguracion(49);
		 $limite_calificacion = ObtenConfiguracion(50);
	 }
	 $Query  = "SELECT fl_leccion, no_semana ";
	 $Query .= "FROM c_leccion ";
	 $Query .= "WHERE fl_programa=$fl_programa ";
	 $Query .= "AND no_grado=$no_grado ";
	 $Query .= "ORDER BY no_semana";
	 $rs = EjecutaQuery($Query);
	 for($i = 0; $row = RecuperaRegistro($rs); $i++){
		 $fl_leccion[$i] = $row[0];
		 $no_semana[$i] = $row[1] - 1;
		 if($fg_frecuencia == 2){
			 $no_semana[$i] *= 2;
		 }
		 # Obtenemos la hora variable de configuracion y la agragamos las fechas
		 $hr = substr(ObtenConfiguracion(68),0,2);
		 $min= substr(ObtenConfiguracion(68),3,2);
		 date_time_set($fe_inicio, $hr, $min);
		 date_date_set($fe_inicio, $anio_ini, $mes_ini, $dia_ini);
		 date_modify($fe_inicio, "+".$no_semana[$i]." week");
		 $fe_publicacion = date_format($fe_inicio, 'Y-m-d H:i');
		 date_modify($fe_inicio, "+$limite_entrega day");
		 $fe_entrega = date_format($fe_inicio, 'Y-m-d H:i');
		 date_date_set($fe_inicio, $anio_ini, $mes_ini, $dia_ini);
		 date_modify($fe_inicio, "+".$no_semana[$i]." week");
		 date_modify($fe_inicio, "+$limite_calificacion day");
		 $fe_calificacion = date_format($fe_inicio, 'Y-m-d H:i');
		 $Query  = "INSERT INTO k_semana(fl_term, fl_leccion, fe_publicacion, fe_entrega, fe_calificacion) ";
		 $Query .= "VALUES($fl_term, $fl_leccion[$i], '$fe_publicacion', '$fe_entrega', '$fe_calificacion') ";
		 EjecutaQuery($Query);
	 }





?>