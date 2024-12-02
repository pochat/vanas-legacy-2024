<?php

# La libreria de funciones
require '../../lib/general.inc.php';

#Recibimos parametros y actualizamos la BD.
$school_type=$_POST['school_type'];
$file_account_number=$_POST['file_account_number'];
$fg_report_type_code=$_POST['fg_report_type_code'];
$filer_amendment_note=$_POST['filer_amendment_note'];
$post_secondary_educational_institution_name=$_POST['post_secondary_educational_institution_name'];
$post_secondary_educational_institution_mailing_address=$_POST['post_secondary_educational_institution_mailing_address'];
$province_state_code=$_POST['province_state_code'];
$country_code=$_POST['country_code'];
$city_name=$_POST['city_name'];
$postal_zip_code=$_POST['postal_zip_code'];
$contact_name=$_POST['contact_name'];
$contact_area_code=$_POST['contact_area_code'];
$contact_phone_number=$_POST['contact_phone_number'];
$contact_extension_number=$_POST['contact_extension_number'];
$taxation_year=$_POST['taxation_year'];

$sbmt_ref_id=$_POST['sbmt_ref_id'];
$trnmtr_nbr=$_POST['trnmtr_nbr'];
$l1_nm=$_POST['l1_nm'];
$cntc_email_area=$_POST['cntc_email_area'];

EjecutaQuery("UPDATE c_configuracion SET ds_valor='$sbmt_ref_id' WHERE cl_configuracion=157 ");
EjecutaQuery("UPDATE c_configuracion SET ds_valor='$trnmtr_nbr' WHERE cl_configuracion=158 ");
EjecutaQuery("UPDATE c_configuracion SET ds_valor='$l1_nm' WHERE cl_configuracion=159 ");
EjecutaQuery("UPDATE c_configuracion SET ds_valor='$cntc_email_area' WHERE cl_configuracion=160 ");


EjecutaQuery("UPDATE c_configuracion SET ds_valor='$taxation_year' WHERE cl_configuracion=156 ");

if(!empty($school_type)){
    EjecutaQuery("UPDATE c_configuracion SET ds_valor='$school_type' WHERE cl_configuracion=140 ");
}
if(!empty($file_account_number)){
    EjecutaQuery("UPDATE c_configuracion SET ds_valor='$file_account_number' WHERE cl_configuracion=141 ");
}
if(!empty($post_secondary_educational_institution_mailing_address)){
    EjecutaQuery("UPDATE c_configuracion SET ds_valor='$post_secondary_educational_institution_mailing_address' WHERE cl_configuracion=144 ");
}
if(!empty($contact_name)){
    EjecutaQuery("UPDATE c_configuracion SET ds_valor='$contact_name' WHERE cl_configuracion=145 ");
}

//if(!empty($filer_amendment_note)){
  EjecutaQuery("UPDATE c_configuracion SET ds_valor='$filer_amendment_note' WHERE cl_configuracion=147 ");
//}
if(!empty($post_secondary_educational_institution_name)){
    EjecutaQuery("UPDATE c_configuracion SET ds_valor='$post_secondary_educational_institution_name' WHERE cl_configuracion=148 ");
}

if(!empty($province_state_code)){
    EjecutaQuery("UPDATE c_configuracion SET ds_valor='$province_state_code' WHERE cl_configuracion=149 ");
}
if(!empty($country_code)){
    EjecutaQuery("UPDATE c_configuracion SET ds_valor='$country_code' WHERE cl_configuracion=150 ");
}
if(!empty($city_name)){
    EjecutaQuery("UPDATE c_configuracion SET ds_valor='$city_name' WHERE cl_configuracion=151 ");
}
if(!empty($postal_zip_code)){
    EjecutaQuery("UPDATE c_configuracion SET ds_valor='$postal_zip_code' WHERE cl_configuracion=152 ");
}

if(!empty($contact_area_code)){
    EjecutaQuery("UPDATE c_configuracion SET ds_valor='$contact_area_code' WHERE cl_configuracion=153 ");
}
if(!empty($contact_phone_number)){
    EjecutaQuery("UPDATE c_configuracion SET ds_valor='$contact_phone_number' WHERE cl_configuracion=154 ");
}
if(!empty($contact_extension_number)){
    EjecutaQuery("UPDATE c_configuracion SET ds_valor='$contact_extension_number' WHERE cl_configuracion=155 ");
}



?>