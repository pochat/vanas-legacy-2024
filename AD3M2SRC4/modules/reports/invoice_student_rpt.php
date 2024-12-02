<?php
  require('../../lib/general.inc.php');
  require_once('../../lib/tcpdf/config/lang/eng.php');
  require_once('../../lib/tcpdf/tcpdf.php');
  
  
    #fl_pago_curso_alumno del tabla k_pago_curso_alumno que representa un pago por liberar un curso en especifico.
    #fl_admin_pagos_alumno de la tabla k_admin_pagos_alumno que representa el pago corresponiente a un plan elegido
    #fg_tipo de pago 1=Pertenece aun pago de plan 2=pertenece aun pago de un curso en especifico   
  
    $fl_admin_pagos = RecibeParametroNumerico('c', True);
    $fl_usuario=RecibeParametroNumerico('u',True);
    $fl_instituto=RecibeParametroNumerico('i',True);
    $fg_tipo_pago=RecibeParametroNumerico('t',True);
  
    #Recuperamos datos del studiante
    $Query="SELECT ds_nombres,ds_apaterno,ds_email, ";
    $Query .="fe_nacimiento, ";
    $Query .="ds_login FROM c_usuario WHERE fl_usuario=$fl_usuario ";
    $row=RecuperaValor($Query);
    $ds_nombres=$row['ds_nombres'];
    $ds_apaterno=$row['ds_apaterno'];
    $ds_email=$row['ds_email'];
    $fe_nacimiento=$row['fe_nacimiento'];
    $ds_login=$row['ds_login'];
    
    $fe_nacimiento=strtotime('+0 day',strtotime($fe_nacimiento));
    $fe_nacimiento= date('Y-m-d',$fe_nacimiento);
    #DAMOS FORMATO DIA,MES, AÑO.
    $date = date_create($fe_nacimiento);
    $fe_nacimiento=date_format($date,'F j, Y');

     #Recuperamos el datos del instituto:
	 $Query="SELECT ds_instituto,nb_plan,S.ds_pais,no_telefono,ds_rfc  
             FROM c_instituto I
             JOIN c_plan_fame P ON P.cl_plan_fame=I.cl_plan_fame
             JOIN c_pais S ON S.fl_pais=I.fl_pais
             WHERE fl_instituto=$fl_instituto ";
	 $row=RecuperaValor($Query);
	 $nb_instituto=str_texto($row[0]);
     $nb_plan_fame=str_texto($row[1]);
     $ds_pais=str_texto($row[2]);
     $no_telefono_instituto=$row[3];
	 $ds_rfc=str_texto($row['ds_rfc']);

     
	 
	 #Recuperamos la direccion del Usuario.
     $Query="SELECT ds_state,ds_city,ds_number,ds_street,ds_zip,ds_phone_number ";
     $Query.="FROM k_usu_direccion_sp A
     WHERE fl_usuario_sp=$fl_usuario ";
     $row=RecuperaValor($Query);
     $ds_estado=$row['ds_state'];
     $ds_ciudad=$row['ds_city'];
     $ds_numero_casa=$row['ds_number'];
     $ds_calle=$row['ds_street'];
     $ds_codigo_postal=$row['ds_zip'];
     $ds_telefono=$row['ds_phone_number'];
    
     if(is_numeric($ds_estado)){
     
        #Recuperamos la provoincia de canada
         $Query4="SELECT ds_provincia FROM k_provincias WHERE fl_provincia=$ds_estado ";
         $row4=RecuperaValor($Query4);
         $ds_estado=str_texto($row4[0]);
     }
    
    #Obtenemos fecha actual :
    $Query = "Select CURDATE() ";
    $row = RecuperaValor($Query);
    $fe_actual = str_texto($row[0]);
    $fe_actual=strtotime('+0 day',strtotime($fe_actual));
    $fe_actual= date('Y-m-d',$fe_actual);
    $fe_emision=GeneraFormatoFecha($fe_actual);
    
    
    #Recuperamos la fecha y datos del pago segun el tipo de pago.
    if($fg_tipo_pago==1){
    
    #Recupermaos datos el plan
    $Query="SELECT  D.mn_total mn_total ,D.id_pago_stripe id_pago,D.fe_pago fe_pago,
		    CASE WHEN C.fg_plan='A' THEN 'Essential Plan Annual'
		    ELSE 'Essencial Plan Monthly' 
		    END ds_descripcion_pago ,C.id_cliente_stripe,D.id_invoice_stripe,C.fe_periodo_inicial, C.fe_periodo_final,mn_tax,mn_subtotal,D.mn_descuento,D.mn_porcentaje_cupon descuento_cupon ,D.fg_tipo_descuento,C.mn_precio_real,C.fg_plan   
		    ,C.fe_periodo_final_mes  
            FROM k_current_plan_alumno C
      	    JOIN k_admin_pagos_alumno D ON D.fl_current_plan_alumno= C.fl_current_plan_alumno
		    WHERE C.fl_alumno=$fl_usuario AND D.fl_admin_pagos_alumno=$fl_admin_pagos  ";
    
        
    }else{
    
    #Recuermaos datos del pago del curso.
    $Query="SELECT  A.mn_total ,A.id_pago,A.fe_pago,CONCAT( 'Unlock Course: ',B.nb_programa) ds_descripcion_pago,
            A.id_customer,''id_invoice_stripe,''fe_periodo_inicial,''fe_periodo_final,mn_tax,mn_costo_curso,''mn_descuento,A.mn_descuento descuento_cupon ,A.fg_tipo_descuento,''mn_precio_real  		
            FROM k_pago_curso_alumno A 
			JOIN c_programa_sp B ON B.fl_programa_sp=A.fl_programa_sp 
			WHERE A.fl_alumno_sp=$fl_usuario AND A.fl_pago_curso_alumno=$fl_admin_pagos ";

    }
    $row3=RecuperaValor($Query);
    $mn_total=$row3[0];
    $id_pago_stripe=str_texto($row3[1]);
    $fe_pago=GeneraFormatoFecha(($row3[2]));
    $ds_descripcion_pago=str_texto($row3[3]);
    $id_cliente_stripe=str_texto($row3[4]);
    $id_invoice_stripe=str_texto($row3[5]);
    $fe_periodo_inicial=GeneraFormatoFecha(($row3[6]));
    $fe_periodo_final=GeneraFormatoFecha(($row3[7]));
    $mn_tax=$row3[8];
    $mn_monto_sin_tax=$row3[9];
    $mn_descuento=$row3['mn_descuento'];
    $mn_porcentaje_descuento_cupon=$row3['descuento_cupon'];
    $fg_tipo_descuento=$row3['fg_tipo_descuento']; 
    $mn_precio_real=$row3['mn_precio_real'];
    $fe_periodo_final_mes=$row3['fe_periodo_final_mes'];
    $fg_plan=$row3['fg_plan'];
    
    if($fg_plan=='M'){
        
        $fe_periodo_final=GeneraFormatoFecha($fe_periodo_final_mes);
    }


    
    if($fg_tipo_descuento=='P'){
    
    
            #Caluclamos el descuento
        $mn_descuento_cupon=$mn_porcentaje_descuento_cupon;
            if($mn_porcentaje_descuento_cupon){
                $mn_descuento= "<br/>Discount: $mn_porcentaje_descuento_cupon%";
            }else
                $mn_descuento="";
            
            
            
    }
    if($fg_tipo_descuento=='C'){
        
            #Caluclamos el descuento
            $mn_descuento_cupon= $mn_porcentaje_descuento_cupon;
    
            if($mn_porcentaje_descuento_cupon){
                $mn_descuento= "<br/>Discount: $mn_porcentaje_descuento_cupon";
            }else
                $mn_descuento="";
    
    }
    
    
    
    if($fg_tipo_pago==1){#cuando pagaron un plna mes/anio

            $interval=$fe_periodo_inicial." to ".$fe_periodo_final;
            $cantidad=1;
            if(!empty($mn_porcentaje_descuento_cupon))
            $mn_monto_sin_tax=$mn_monto_sin_tax;
        
        
        
        
        
    }else{
        $cantidad=1;
        $interval="";
        $id_invoice_stripe=$id_pago_stripe;
        $nb_plan_fame="";
    } 
    
    
    
    
    
    $left_footer=str_uso_normal(ObtenEtiqueta(1746)).str_uso_normal(ObtenEtiqueta(1737));
    $right_footer="";
    
    
    
  // Extend the TCPDF class to create custom Header and Footer
  class MYPDF extends TCPDF 
  {
    //Page header
    public function Header() {
    
      global $fl_admin_pagos;
      global $ds_nombres;
      global $ds_apaterno;
      global $ds_email;
      global $fe_nacimiento;
      global $ds_estado;
      global $ds_ciudad;
      global $ds_numero_casa;
      global $ds_codigo_postal;
      global $ds_telefono;
      global $ds_calle;
      global $ds_pais;
      global $fe_emision;
      global $nb_instituto; 
      global $nb_plan_fame;
      global $no_telefono_instituto;
	  global $fe_pago;	
      global $fl_instituto;
	  global $ds_rfc;
	  global $id_pago_stripe;
	  global $id_cliente_stripe;
      global $mn_total;
	  global $ds_descripcion_pago;
      global $id_invoice_stripe;
	  global $interval;
      global $cantidad;
      global $mn_monto_sin_tax;
      global $mn_tax;
      global $mn_descuento;
      global $mn_porcentaje_descuento_cupon;
      global $mn_descuento_cupon;
      global $fg_tipo_descuento;
      global $fe_periodo_final_mes;
      
      $encabezado = '
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
      <tr>
        <td style="width:100%;">
         &nbsp;
        </td>
      </tr>
   ';
$encabezado.='<tr>
       
        
        <td rowspan="5" style="width:40%; color:#037EB7; font-family:Tahoma; font-size:32px; text-align:right;">
                <img src="../../images/Vanas_doc_logo.jpg" />
        </td>
		
		<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
         
        </td>
		
		 <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
		 
        </td>
      </tr>
	  ';
	  $encabezado.='
      <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
		
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
         '.ObtenEtiqueta(516).'  
        </td>
      </tr>
	  ';
	  $encabezado.='
      <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
   
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
          '.ObtenEtiqueta(518).' 
        </td>
      </tr>
      ';
	   $encabezado.='
      <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
		
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
       '.ObtenEtiqueta(519).'   
        </td>
      </tr>
      
      <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
        
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
          '.ObtenEtiqueta(1741).': '.ObtenEtiqueta(1740).'
        </td>
      </tr>
      
       <tr>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:62px; font-weight:normal; text-align:left;">
		<b>'.ObtenEtiqueta(1729).'</b>
        </td>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
        
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
		
        </td>
      </tr>
      
      
		
          ';

 $encabezado.='

    </table>';
      
      
      
      
      
      
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 0, $this->writeHTML($encabezado, true, false, true, false, ''), 0, true, 'J', 0, '', 0, false, 'M', 'B');
        
       // $this->SetFont('helvetica', 'B', 10);
        //$this->Cell(0, 5, 'Invoice  '.$id_pago.'                               PAGE '.
                 //   $this->getAliasNumPage().' of '.$this->getAliasNbPages(), 'B', true, 'J', 0, '', 0, false, 'M', 'B');
        //$this->Cell(0, 8, '','', true, 'center', 0, '', 0, false, 'M', 'B');

    }

    // Page footer
    public function Footer() {
	
	    global $left_footer;
	    global $right_footer;
	
	
        $left_column = ''.$left_footer.'';
        $right_column = ''.$right_footer.'';
        // Position at 15 mm from bottom
        $this->SetY(-20);
        // Set font
        $this->SetFont('helvetica', '', 9);
        $this->writeHTMLCell(140, '', '', '', $left_column, 0, 0, 0, true, 'J', true);
        $this->writeHTMLCell(0, '', '', '', '', 0, 0, 0, true, 'J', true);
        $this->SetFont('helvetica', '', 10);
        $this->writeHTMLCell(0, '', '', '', $right_column, 0, 0, 0, true, 'C', true);
       
    }

  }
  
  
  
 
 
  
 
  
  
  
  

  
  
  
  # Empezamos a mostrar datos
  $htmlcontent = '<table border="0" cellpadding="1" cellspacing="0" width="100%">'; 
  
  
  $htmlcontent .='<tr>';
  $htmlcontent .='<td colspan="7" style=" height:15px; color:#000000; font-family:Arial;font-weight:bold; font-size:35px;  text-align:center;"> </td>';
  $htmlcontent .='</tr>';
  

  
   $htmlcontent .= '<tr >';
   $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"><strong>'.ObtenEtiqueta(1726).'</strong>  </td>';
   $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"><strong>'.ObtenEtiqueta(1727).'</strong>   </td>';
   $htmlcontent .= '</tr>';
   
   $htmlcontent .= '<tr>';
   $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$ds_nombres.' '.$ds_apaterno.'   </td>';
   $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.ObtenEtiqueta(1728).':</td>';
   $htmlcontent .='<td  colspan="2" style="height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$id_invoice_stripe.'</td>';
  
   $htmlcontent .='</tr>';
  
  
  
  
  
   $htmlcontent .= '<tr>';
   $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$nb_instituto.'   ';
   $htmlcontent .='</td>';
   $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.ObtenEtiqueta(1730).':</td>';
   $htmlcontent .='<td colspan="2" style="height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$fe_emision.'</td>';
   $htmlcontent .='</tr>';
  
  
  
  
  
  
  
   $htmlcontent .= '<tr>';
   $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">';
    if($ds_numero_casa)
          $htmlcontent.=''.$ds_numero_casa.' ';
		  $htmlcontent.=''.$ds_calle.' '.$ds_codigo_postal.'';
   
   $htmlcontent .='</td>';
   $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.ObtenEtiqueta(1731).':</td>';
   $htmlcontent .='<td colspan="3" style=" height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$id_pago_stripe.' </td>';
 
   $htmlcontent .='</tr>';
  
  
  
  
   
  
   $htmlcontent .= '<tr>';
   $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">';
   if($ds_estado)
   $htmlcontent .=''.$ds_estado.' ';
   $htmlcontent .=''.$ds_pais.'  </td>';

   $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.ObtenEtiqueta(1732).': </td>';
   
   $htmlcontent .='<td colspan="2" style=" height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$nb_plan_fame.'  </td>';
   $htmlcontent .='</tr>';
   
   
   
   
  
   $htmlcontent .= '<tr>';
   $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">  </td>';
   $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left; margin-lef:35px;">'.ObtenEtiqueta(1715).':</td>';
   $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$fe_pago.' </td>';
   $htmlcontent .='<td style="width:5%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   $htmlcontent .='</tr>';
  
  
  
   $htmlcontent .= '<tr>';
   $htmlcontent .='<td style=" width:50%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"><b>'.ObtenEtiqueta(1733).':</b> '.$id_cliente_stripe.' </td>';
   $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"></td>';
   $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   $htmlcontent .='<td style="width:10%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   $htmlcontent .='</tr>';
   /*  
   $htmlcontent .= '<tr>';
   $htmlcontent .='<td style=" width:50%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"><b>'.ObtenEtiqueta(1792).':</b> '.$ds_rfc.' </td>';
   $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"></td>';
   $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   $htmlcontent .='<td style="width:10%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   $htmlcontent .='</tr>';
   */
   $htmlcontent .='<tr>';
   $htmlcontent .='<td colspan="4" style=" height:15px; color:#000000; font-family:Arial;font-weight:bold; font-size:35px;  text-align:center;"> <br/> <br/></td>';
   $htmlcontent .='</tr>';
  
	
         
	 
   $htmlcontent .= '<tr>';
   $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:40%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><b>'.ObtenEtiqueta(1716).' </b> </td>';
   $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><b>'.ObtenEtiqueta(1734).' </b></td>';
   $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:10%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><b> '.ObtenEtiqueta(1717).' </b>  </td>';
   $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;"><b> '.ObtenEtiqueta(1718).' ('.ObtenConfiguracion(113).'$)</b>  </td>';
   $htmlcontent .= '</tr>';

   
   
   
   
   if($fg_tipo_descuento=='P'){
       
       $mn_porcentaje_descuento_cupon="(".number_format($mn_porcentaje_descuento_cupon)."%)";
       $mn_monto_sin_tax=$mn_monto_sin_tax+$mn_descuento_cupon;
   }
   if($fg_tipo_descuento=='C'){
       $mn_porcentaje_descuento_cupon="($".number_format($mn_porcentaje_descuento_cupon,2).")";
       $mn_monto_sin_tax=$mn_monto_sin_tax+$mn_descuento_cupon;
   }
  
   $htmlcontent .= '<tr >';
   $htmlcontent .='<td style="border-bottom:2px solid #000; width:40%; height:15px; color:#000000; font-family:Arial; font-size:33px;  text-align:left;">'.$ds_descripcion_pago.' '.$mn_descuento.'  </td>';
   $htmlcontent .='<td style="border-bottom:2px solid #000; width:30%; height:15px; color:#000000; font-family:Arial; font-size:33px;  text-align:left;">'.$interval.' </td>';
   $htmlcontent .='<td style="border-bottom:2px solid #000; width:10%; height:15px; color:#000000; font-family:Arial; font-size:33px;  text-align:center;">'.$cantidad.'</td>';
   $htmlcontent .='<td style="border-bottom:2px solid #000; width:20%; height:15px; color:#000000; font-family:Arial; font-size:33px;  text-align:right;">'.number_format($mn_monto_sin_tax,2).' </td>';
   $htmlcontent .= '</tr>';

   
   
    #Obtenemos el porcentaje del tax.
    $mn_porcentaje=($mn_tax/$mn_monto_sin_tax) *100;

   
   #Presentamos totale y tax.
   $htmlcontent .= '<tr >';
   $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   $htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
   $htmlcontent .='<td style=" width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.ObtenEtiqueta(1736).' '.ObtenConfiguracion(113).' </td>';
   $htmlcontent .='<td style=" width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.number_format($mn_monto_sin_tax,2).' </td>';
   $htmlcontent .= '</tr>';
   
   
   $htmlcontent .= '<tr >';
   $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   $htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
   $htmlcontent .='<td style="border-bottom:2px solid #000; width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">*'.ObtenEtiqueta(1735).' ('.number_format($mn_porcentaje).'%) </td>';
   $htmlcontent .='<td style="border-bottom:2px solid #000; width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.number_format($mn_tax,2).' </td>';
   $htmlcontent .= '</tr>';

   $htmlcontent .= '<tr >';
   $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   $htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
   $htmlcontent .='<td style="border-bottom:2px solid #000; width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">Coupon '.$mn_porcentaje_descuento_cupon.' </td>';
   $htmlcontent .='<td style="border-bottom:2px solid #000; width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">-'.number_format($mn_descuento_cupon,2).' </td>';
   $htmlcontent .= '</tr>';
   
   
    #Presentamos totale y tax.
   $htmlcontent .= '<tr >';
   $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   $htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
   $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.ObtenEtiqueta(1742).' '.ObtenConfiguracion(113).'</td>';
   $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.$mn_total.'  </td>';
   $htmlcontent .= '</tr>';
 
 
 
 
  $htmlcontent .= '


    </table>
  ';
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  // create new PDF document
  $pdf = new MYPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);

  
  // set default header data
  $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);


 

  // set default monospaced font
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

  //set margins
  //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
  $pdf->SetHeaderMargin(4);
  $pdf->SetFooterMargin(5);
  $pdf->SetTopMargin(60);

  //set auto page breaks
  $pdf->SetAutoPageBreak(TRUE, 25);

  //set image scale factor
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 

  //set some language-dependent strings
  $pdf->setLanguageArray($l); 

  // ---------------------------------------------------------

  // set font
  $pdf->SetFont('helvetica', '', 10); 

  // add a page
  $pdf->AddPage("P"); 
    
  // output the HTML content
  $pdf->writeHTML($htmlcontent, true, 0, true, 0); 
  
  $nombre_archivo = 'Invoice_'.$ds_nombres.'_'.$id_pago_stripe.'.pdf';
  //Close and output PDF document
  ob_end_clean();
  $pdf->Output($nombre_archivo, 'D');
 


?>