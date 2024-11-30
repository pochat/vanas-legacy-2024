<?php 
# Libreria de funciones	
require("../lib/self_general.php");

# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion(False,0, True);

# Verifica que el usuario tenga permiso de usar esta funcion
if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
}
 $fl_instituto=ObtenInstituto($fl_usuario);

 
 $fg_gender=RecibeParametroHTML('fg_gender');
 $fg_grade=RecibeParametroHTML('fg_grade');
// $fg_nivel=RecibeParametroHTML('fg_nivel');
  $fg_international = RecibeParametroHTML('fg_international');
  $fg_educational = RecibeParametroHTML('fg_educational');
  $fg_blocking = RecibeParametroBinario('fg_blocking');
  $fg_parent_authorization=RecibeParametroBinario('fg_parent_authorization');
  $fg_ferpa=RecibeParametroBinario('fg_ferpa');
  $fg_addStudents=RecibeParametroBinario('fg_addStudents');
  $fg_addTeachers=RecibeParametroBinario('fg_addTeachers');
  $fg_deletions=RecibeParametroBinario('fg_deletions');

#Verifica si existe un registro:
$Query="SELECT COUNT(*) FROM k_instituto_filtro WHERE fl_instituto=$fl_instituto ";
$row=RecuperaValor($Query);
$existe=$row[0];
#Inserta Registro
if($existe==1){
  #Actuliza
  $Query  = "UPDATE k_instituto_filtro SET fg_gender='$fg_gender',fg_grade='$fg_grade',fg_ferpa='$fg_ferpa', fg_addStudents='$fg_addStudents', fg_addTeachers='$fg_addTeachers', fg_deletions='$fg_deletions', ";
  $Query .= "fg_educational='$fg_educational', fg_international='$fg_international',  fe_ultmod=NOW(), fg_blocking='$fg_blocking' WHERE fl_instituto=$fl_instituto ";
  EjecutaQuery($Query);

}else{
  #Inserta
  $Query="INSERT INTO k_instituto_filtro (fl_instituto,fg_gender,fg_grade,fg_educational, fg_international, fe_creacion, fe_ultmod, fg_blocking,fg_ferpa, fg_addStudents, fg_addTeachers, fg_deletions) ";
  $Query.="VALUES($fl_instituto,'$fg_gender','$fg_grade', '$fg_educational', '$fg_international',NOW(), NOW(), '$fg_blocking','$fg_ferpa', '$fg_addStudents', '$fg_addTeachers', '$fg_deletions')";
  $fl_k_instituto_filtro=EjecutaInsert($Query);
}

 EjecutaQuery("UPDATE c_instituto SET fg_parent_authorization='$fg_parent_authorization' WHERE fl_instituto=$fl_instituto ");

#MJD
/*
 *Verifica si este instituto es el rector y si si lo es entonces aplicara lo mismo a sus dependencias.
 *
 */

 $Query="SELECT fl_instituto FROM c_instituto WHERE fl_instituto_rector=$fl_instituto ";
 $rs=EjecutaQuery($Query);
 for($i=0; $row=RecuperaRegistro($rs); $i++){

     $fl_instituto=$row['fl_instituto'];

     #Verifica si existe un registro:
     $Query="SELECT COUNT(*) FROM k_instituto_filtro WHERE fl_instituto=$fl_instituto ";
     $row=RecuperaValor($Query);
     $existe=$row[0];
     #Inserta Registro
     if($existe==1){
         #Actuliza
         $Query  = "UPDATE k_instituto_filtro SET fg_gender='$fg_gender',fg_grade='$fg_grade',fg_ferpa='$fg_ferpa', fg_addStudents='$fg_addStudents', fg_addTeachers='$fg_addTeachers', fg_deletions='$fg_deletions', ";
         $Query .= "fg_educational='$fg_educational', fg_international='$fg_international',  fe_ultmod=NOW(), fg_blocking='$fg_blocking' WHERE fl_instituto=$fl_instituto ";
         EjecutaQuery($Query);
         
     }else{
         #Inserta
         $Query="INSERT INTO k_instituto_filtro (fl_instituto,fg_gender,fg_grade,fg_educational, fg_international, fe_creacion, fe_ultmod, fg_blocking,fg_ferpa, fg_addStudents, fg_addTeachers, fg_deletions) ";
         $Query.="VALUES($fl_instituto,'$fg_gender','$fg_grade', '$fg_educational', '$fg_international',NOW(), NOW(), '$fg_blocking','$fg_ferpa', '$fg_addStudents', '$fg_addTeachers', '$fg_deletions')";
         $fl_k_instituto_filtro=EjecutaInsert($Query);
     }

     EjecutaQuery("UPDATE c_instituto SET fg_parent_authorization='$fg_parent_authorization' WHERE fl_instituto=$fl_instituto ");


 }



?>



