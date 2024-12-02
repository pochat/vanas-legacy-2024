<?php
  require('../../lib/general.inc.php');
  require_once('../../lib/tcpdf/config/lang/eng.php');
  require_once('../../lib/tcpdf/tcpdf.php');
  
  $fl_admin_pagos = RecibeParametroNumerico('c', True);#id del tabla k_admin_pagos
  $fl_usuario=RecibeParametroNumerico('u',True);
  $fl_instituto=RecibeParametroNumerico('i',True);
  
  
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

     #Recuperamos el datos del istituto:
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
    
    
    #Recuperamos la fecha de pago
    $Query3="SELECT fe_pago,fl_pago_stripe,mn_descuento FROM k_admin_pagos WHERE fl_admin_pagos=$fl_admin_pagos";
    $row3=RecuperaValor($Query3);
    $fe_pago=GeneraFormatoFecha(($row3[0]));
    $fl_pago_stripe=$row3[1];
	$mn_descuento=$row3[2];
    
   
    #Recuperamos el id del pago, 
    $Query2="SELECT id_pago_stripe FROM k_pago_stripe WHERE fl_pago=$fl_pago_stripe ";
    $row2=RecuperaValor($Query2);
    $id_pago=str_texto($row2[0]);
    
    
    
    $left_footer=str_uso_normal(ObtenEtiqueta(1746)).ObtenEtiqueta(1737);
    $right_footer="";
    
    
    
  // Extend the TCPDF class to create custom Header and Footer
  class MYPDF extends TCPDF 
  {
    //Page header
    public function Header() {
    
      global $fl_admin_pagos;
      global $ds_login;
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
      global $id_pago;
      global $fl_instituto;
	  global $mn_descuento;
	  global $ds_rfc;
	  
	  
	  
	  
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
  
  
  #Recupermaos datos claves  del programa.
  $Query="SELECT A.ds_descripcion,B.mn_monto,B.mn_tax,B.mn_total,A.fg_motivo_pago,B.id_pago_stripe,B.id_cliente_stripe,A.fe_periodo_inicial,A.fe_periodo_final,A.id_invoice_stripe,A.fl_current_plan
			,A.cl_metodo_pago,A.ds_cheque,A.ds_comentario 
            FROM k_admin_pagos A
            JOIN k_pago_stripe B ON B.fl_pago=A.fl_pago_stripe 
            WHERE A.fl_admin_pagos=$fl_admin_pagos ";
  $row = RecuperaValor($Query);
  $ds_descripcion_pago=str_texto($row[0]);
  $mn_monto_sin_tax=$row[1];
  $mn_tax=$row[2];
  $mn_total=number_format($row[3],2);
  $fg_motivo_pago=$row[4];
  $cl_metodo_pago=$row['cl_metodo_pago'];
  $ds_cheque=str_texto($row['ds_cheque']);
  $ds_comentario_adicional=str_texto($row['ds_comentario']);
  
  
  
  
  $id_pago_stripe=$row[5];
  $id_cliente_stripe=$row[6];
  $fe_periodo_inicial=GeneraFormatoFecha($row[7]);
  $fe_periodo_final=GeneraFormatoFecha($row[8]);
  $id_invoice_stripe=$row[9];
  $fl_current_plan=$row[10];
  

  #Obtenemos la cantidad para colocarlo en detalle del invoice
  $cantidad = intval(preg_replace('/[^0-9]+/', '', $ds_descripcion_pago), 10);
  $ds_descripcion_pago = preg_replace('/[0-9]+/', '', $ds_descripcion_pago);
  
  
  #Recupermao el tipo de plan.
  $Query="SELECT fl_princing,fg_plan FROM k_current_plan WHERE fl_current_plan=$fl_current_plan ";
  $row=RecuperaValor($Query);
  $fl_princing=$row[0];
  $fg_plan=$row[1];
  
  #Recuperamos el metodo de pago.
  if($cl_metodo_pago==1)
	$nb_metodo_pago="Cheque";
  if($cl_metodo_pago==2)
	$nb_metodo_pago="Wire Transfer/Deposit";
  if($cl_metodo_pago==3)
	$nb_metodo_pago="Cash";
  
  if($ds_cheque)
	$nb_metodo_pago= $nb_metodo_pago."-".$ds_cheque;  
  
  
  #Recupermaos el monto por licencia.
  $Query="SELECT mn_costo_por_licencia FROM k_admin_pagos WHERE fl_admin_pagos=$fl_admin_pagos ";
  $rt=RecuperaValor($Query);
  $mn_costo_por_licencia=$rt['mn_costo_por_licencia'];
  
  if(empty($mn_costo_por_licencia)){
  
  $Query2="SELECT mn_anual,mn_mensual FROM c_princing WHERE fl_princing=$fl_princing ";
  $row2=RecuperaValor($Query2);
  if($fg_plan=='M')
	  $mn_costo_por_licencia=$row2[1];
  else
	  $mn_costo_por_licencia=$row2[0];
  
  }
  
  #Agregamos el costo por licencia.
  $ds_descripcion_pago=$ds_descripcion_pago."<br/>".ObtenEtiqueta(1765)." $".$mn_costo_por_licencia."<br/>".ObtenEtiqueta(1750)." ".$mn_descuento."%<br>".$ds_comentario_adicional ;
  
  #Eliminamos las licencias de la descripcion y se la pasamos ala cantidad del detalle del invoice.
  //$ds_descripcion_pago = substr($ds_descripcion_pago, 0, -11); 
 
  
  $interval=$fe_periodo_inicial." to ".$fe_periodo_final;
  
  if(empty($id_invoice_stripe)){
    #Recupermso el intervalo de la fecha del period actual del instituto
    $Query="SELECT fe_periodo_inicial,fe_periodo_final FROM k_current_plan WHERE fl_instituto=$fl_instituto";
    $row=RecuperaValor($Query);
    $fe_periodo_inicial=GeneraFormatoFecha($row[0]);
    $fe_periodo_final=GeneraFormatoFecha($row[1]);
    $interval=$fe_periodo_inicial." to ".$fe_periodo_final;
    
    $id_invoice_stripe=$id_pago_stripe;
  }
     
  //if($fg_motivo_pago=='NP')
  
  
  
  # Empezamos a mostrar datos
  $htmlcontent = '<table border="0" cellpadding="1" cellspacing="0" width="100%">'; 
  
  
  $htmlcontent .='<tr>';
  $htmlcontent .='<td colspan="7" style=" height:15px; color:#000000; font-family:Arial;font-weight:bold; font-size:35px;  text-align:center;"> </td>';
  $htmlcontent .='</tr>';
  

  
   $htmlcontent .= '<tr >';
   $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"><strong>'.ObtenEtiqueta(1726).'</strong>  </td>';
   $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"><strong>'.ObtenEtiqueta(1727).'</strong>   </td>';
   //$htmlcontent .='<td style="width:15%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   //$htmlcontent .='<td style="width:5%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
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
  // $htmlcontent .='<td style="width:5%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
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
   
   
   
   
   
   /*
   $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$nb_plan_fame.' </td>';
   $htmlcontent .='<td style="width:5%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   $htmlcontent .='</tr>';
   */
   $htmlcontent .= '<tr>';
   $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">  </td>';
   $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left; margin-lef:35px;">'.ObtenEtiqueta(1715).':</td>';
   $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$fe_pago.' </td>';
   $htmlcontent .='<td style="width:5%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   $htmlcontent .='</tr>';
  
  
   if($cl_metodo_pago){
   $htmlcontent .= '<tr>';
   $htmlcontent .='<td style=" width:55%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">  </td>';
   $htmlcontent .='<td style="width:13%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left; margin-lef:35px;">'.ObtenEtiqueta(483).':</td>';
   $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;">'.$nb_metodo_pago.' </td>';
   $htmlcontent .='<td style="width:5%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   $htmlcontent .='</tr>';
   }
  
  
  
   $htmlcontent .= '<tr>';
   $htmlcontent .='<td style=" width:50%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"><b>'.ObtenEtiqueta(1733).':</b> '.$id_cliente_stripe.' </td>';
   $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"></td>';
   $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   $htmlcontent .='<td style="width:10%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   $htmlcontent .='</tr>';
     
   $htmlcontent .= '<tr>';
   $htmlcontent .='<td style=" width:50%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"><b>'.ObtenEtiqueta(1792).':</b> '.$ds_rfc.' </td>';
   $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"></td>';
   $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   $htmlcontent .='<td style="width:10%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   $htmlcontent .='</tr>';
   
   $htmlcontent .='<tr>';
   $htmlcontent .='<td colspan="4" style=" height:15px; color:#000000; font-family:Arial;font-weight:bold; font-size:35px;  text-align:center;"> <br/> <br/></td>';
   $htmlcontent .='</tr>';
  
   if($fl_instituto==77)
   $currency="CAD";
   else
   $currency=ObtenConfiguracion(113);
   
         
	 
   $htmlcontent .= '<tr>';
   $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:40%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><b>'.ObtenEtiqueta(1716).' </b> </td>';
   $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><b>'.ObtenEtiqueta(1734).' </b></td>';
   $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:10%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><b> '.ObtenEtiqueta(1717).' </b>  </td>';
   $htmlcontent .='<td style="border-bottom:2px solid #000;border-top:2px solid #000;  width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;"><b> '.ObtenEtiqueta(1718).' ('.$currency.'$)</b>  </td>';
   $htmlcontent .= '</tr>';

   
   
   //$htmlcontent .= '<tr >';
   //$htmlcontent .='<td colspan="4" style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><br/>  </td>';
   //$htmlcontent .= '</tr>';
   
      
   
   
   $htmlcontent .= '<tr >';
   $htmlcontent .='<td style="border-bottom:2px solid #000; width:40%; height:15px; color:#000000; font-family:Arial; font-size:33px;  text-align:left;">'.$ds_descripcion_pago.'  </td>';
   $htmlcontent .='<td style="border-bottom:2px solid #000; width:30%; height:15px; color:#000000; font-family:Arial; font-size:33px;  text-align:left;">'.$interval.' </td>';
   $htmlcontent .='<td style="border-bottom:2px solid #000; width:10%; height:15px; color:#000000; font-family:Arial; font-size:33px;  text-align:center;">'.$cantidad.'</td>';
   $htmlcontent .='<td style="border-bottom:2px solid #000; width:20%; height:15px; color:#000000; font-family:Arial; font-size:33px;  text-align:right;">'.number_format($mn_monto_sin_tax,2).' </td>';
   $htmlcontent .= '</tr>';

   
   
    #Obtenemos el porcentaje del tax.
    $mn_porcentaje=($mn_tax/$mn_monto_sin_tax) *100;
   
   
   //$htmlcontent .= '<tr >';
   //$htmlcontent .='<td colspan="4" style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"><br/>  </td>';
   //$htmlcontent .= '</tr>';
   
   
   
   #Presentamos Descuento.
   //$htmlcontent .= '<tr >';
   //$htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   //$htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
   //$htmlcontent .='<td style=" width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.ObtenEtiqueta(1750).'  </td>';
   //$htmlcontent .='<td style=" width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.$mn_descuento.' %</td>';
   //$htmlcontent .= '</tr>';
     
   
   #Presentamos totale y tax.
   $htmlcontent .= '<tr >';
   $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   $htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
   $htmlcontent .='<td style=" width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.ObtenEtiqueta(1736).' '.$currency.' </td>';
   $htmlcontent .='<td style=" width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.number_format($mn_monto_sin_tax,2).' </td>';
   $htmlcontent .= '</tr>';
   
   
   $htmlcontent .= '<tr >';
   $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   $htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
   $htmlcontent .='<td style="border-bottom:2px solid #000; width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">*'.ObtenEtiqueta(1735).' ('.number_format($mn_porcentaje).'%) </td>';
   $htmlcontent .='<td style="border-bottom:2px solid #000; width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.number_format($mn_tax,2).' </td>';
   $htmlcontent .= '</tr>';
   
   
   
   
    #Presentamos totale y tax.
   $htmlcontent .= '<tr >';
   $htmlcontent .='<td style=" width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:left;"> </td>';
   $htmlcontent .='<td style="width:30%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:center;"> </td>';
   $htmlcontent .='<td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:35px;  text-align:right;">'.ObtenEtiqueta(1742).' '.$currency.'</td>';
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
  
  $nombre_archivo = 'Invoice_'.$nb_instituto.'_'.$id_pago_stripe.'.pdf';
  ob_end_clean();
  //Close and output PDF document
  $pdf->Output($nombre_archivo, 'D');



?>