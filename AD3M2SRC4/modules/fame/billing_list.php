<?php

require '../../lib/general.inc.php';

parse_str($_POST['extra_filters']['advanced_search'], $advanced_search);
$_POST += $advanced_search;

$nuevo = !empty($_POST['nuevo'])?$_POST['nuevo']:NULL;
$actual = !empty($_POST['actual'])?$_POST['actual']:NULL;

require 'filtros.inc.php';

$query = 'SELECT I.fl_instituto, I.ds_instituto, P.ds_pais,CONCAT(U.ds_nombres," ", U.ds_apaterno) AS nombre_contacto,K.no_total_licencias,K.no_licencias_usadas,K.no_licencias_disponibles, 
CASE WHEN K.fg_plan="A" then "Annual"
ELSE "Month" END fg_plan, K.mn_total_plan,K.fg_estatus,K.fe_periodo_final,K.fl_princing,fl_instituto_rector 
FROM  k_current_plan K
LEFT JOIN c_instituto I ON I.fl_instituto=K.fl_instituto
LEFT JOIN c_pais P ON P.fl_pais=I.fl_pais
LEFT JOIN c_usuario U ON U.fl_usuario=I.fl_usuario_sp ';
 
$JQueryDataTable = new JQueryDataTable();

if (false) {
    $JQueryDataTable->queryInfo($_POST + array(
        'query' => $query,
        'aliasTable' => 'List'));
    die;
}

$data = $JQueryDataTable->queryInfo($_POST + array(
    'query' => $query,
    'aliasTable' => 'List'), false);

$dt = $data['data'];

$flUsuarios = array();
foreach ($dt as $key => $lt) {
    $flInstituto[] = $lt['List']['fl_instituto'];

}

$tot_registros = 0;
foreach ($dt as $key => $lt) {
    $fl_instituto = $lt['List']['fl_instituto'];
    $ds_instituto = $lt['List']['ds_instituto'];
    $ds_pais = $lt['List']['ds_pais'];
    $nb_admin=$lt['List']['nombre_contacto'];
    $no_usuarios=$lt['List']['no_total_licencias'];
    $no_licencias_usadas=$lt['List']['no_licencias_usadas'];
    $no_licencias_disponibles=$lt['List']['no_licencias_disponibles'];
    $fg_plan=$lt['List']['fg_plan'];
    $mn_total=$lt['List']['mn_total_plan'];
    $fg_estatus=$lt['List']['fg_estatus'];
    $fl_princing=$lt['List']['fl_princing'];
    $fe_periodo_vigencia=$lt['List']['fe_periodo_final'];
	$fl_instituto_rector=$lt['List']['fl_instituto_rector'];

    #se calcula su proximo pago
    $fe_final_periodo=strtotime('+1 day',strtotime($fe_periodo_vigencia));
    $fe_final_periodo= date('Y-m-d',$fe_final_periodo);
    
    #DAMOS FORMATO DIA,MES, AN�O
    $date = date_create($fe_final_periodo);
    $fe_proximo_pago=date_format($date,'F j , Y');
    
    #Recuperamos datos del isntituto Rector.
	 if($fl_instituto_rector){
		$Query="SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto_rector ";
		$ro=RecuperaValor($Query);
		$nb_instituto_rector='<small class=\'text-muted\' ><i>'.ObtenEtiqueta(2524).': '.$ro['ds_instituto'].'</i></small>';
	 }else{
		$nb_instituto_rector=""; 
	 }

    if($fg_plan=='Annual'){#si pago es anual.
       
        $Query="SELECT mn_mensual, mn_anual FROM c_princing WHERE fl_princing=$fl_princing ";
        $row=RecuperaValor($Query);
        $mn_pago_mensual="$".number_format($row[1],2) ;
        
        //$mn_pago_mensual="$ ".number_format( ($mn_total /12),2) ;
        $mn_pago=$fg_plan." $".number_format($mn_total,2);
        $texto_="Monthly";
    
    }else{
    
        $mn_pago="Monthly"." $".number_format($mn_total,2);
        $mn_pago_mensual="$".number_format($mn_total,2) ;
    }

    if($fg_estatus=='A'){
        $Colors="label-success";
        $fg_estatus="Active";
    }
    if($fg_estatus=='C'){
        $Colors="label-warning ";
        $fg_estatus="Cancelado";
    }
    if($fg_estatus=='I'){
        $Colors="label-danger";
        $fg_estatus="Inactivo";
    }
    
    $tot_registros ++;

    $data['data'][$key]['List'] = array(
        "action" => "<button class='btn btn-xs'>Open case</button> <button class='btn btn-xs btn-danger pull-right' style='margin-left:5px'>Delete Record</button> <button class='btn btn-xs btn-success pull-right'>Save Changes</button> ",
        'name' =>'<a href=\'javascript:Envia(\"billing_frm.php\",'.$fl_instituto.');\'>'. $ds_instituto.'</a><br>'.$nb_instituto_rector.' ' ,
        "country" => ''.$ds_pais . '<br><small class="text-muted"></small>',
        "admin" =>''. $nb_admin.'<br><small class="text-muted"></small>', 
        "user" => ''.'<div class="text-left">'.ObtenEtiqueta(988).': '. $no_usuarios.' <br> <small class="text-muted"><i>'.ObtenEtiqueta(989).':  '. $no_licencias_usadas.'</i></small><br> <small class="text-muted"> <i>'.ObtenEtiqueta(990).': '. $no_licencias_disponibles.'</i></small> </div>',
        "fg_plan" =>''.$fg_plan.'<br><small class="text-muted"><i>Renew '.$fe_proximo_pago.'</i></small>' ,
        "mn_total"=>'<div class="text-right">'.$mn_pago.'<br><small class="text-muted"><i>'.$texto_.' '.$mn_pago_mensual.'</i></small>  </div>' ,
        "status" => "<span class='label " . $Colors . "'>" . $fg_estatus . "</span>",
        //"progress" => "<td><div class='progress progress-xs' data-progressbar-value='" . round($lt['no_promedio_t']) . "'><div class='progress-bar'></div></div></td>",
       // "progress" => "<td><div class='progress progress-xs' data-progressbar-value='" . round($lt['mn_progreso']) . "'><div class='progress-bar'></div></div><span class='hidden'>" . round($lt['mn_progreso']) . "</span></td>",
        "comments" => "This is a blank comments area, used to add comments and keep notes",
        "fl_instituto" => $fl_instituto,
        "tot_registros" => $tot_registros
            ) + $lt;
    /**
     * EGMC 20160525
     * Se agrega el maestro
     */
    //$data['data'][$key]['List']['teachers'] = '';
//    if (!empty($teachers[$lt['fl_usuario']])) {
//        $data['data'][$key]['List']['teachers'].="<div class='project-members'>";
//        foreach ($teachers[$lt['fl_usuario']] as $avtr) {
////            Dbg::pd($avtr);

//            if ($avtr['ds_ruta_avatar'] == '') {
//                $avtr['ds_ruta_avatar'] = 'male.png';
//            }

//            $data['data'][$key]['List']['teachers'].= "<a href='javascript:void(0)' rel='tooltip' data-placement='top' data-html='true' data-original-title='Term " . $avtr['no_grado'] . "</small>: " . $avtr['nb_maestro'] . "'><img src='".PATH_MAE_IMAGES."/avatars/" . $avtr['ds_ruta_avatar'] . "' class='online' alt='user'></a>";
//        }
//        $data['data'][$key]['List']['teachers'].="</div>";
//    }
}
//Dbg::pd($data);
die(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_HEX_AMP));
