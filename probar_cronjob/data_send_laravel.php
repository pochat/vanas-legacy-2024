<?php
#librerias propias de FAME.
require '../fame/lib/self_general.php';



#opc1
/*
$Query="SELECT * FROM vanas_laravel.teacher_assessment where 1=1 ";
$rs=EjecutaQuery($Query);
while($row=RecuperaRegistro($rs)){

    $criterion_id=$row['criterion_id'];
    $user_id=$row['user_id'];
    $lesson_id=$row['lesson_id'];
    $course_id=$row['course_id'];
    $score=$row['score'];
    $percentage=$row['percentage'];
    $comment=$row['comment'];
    $final_comment=$row['final_comment'];
    $updated_at=$row['updated_at'];
    $created_at=$row['created_at'];


    $Query2="SELECT no_porcentaje from vanas_prod.c_calculo_criterio_temp 
            WHERE  fl_criterio=$criterion_id AND fl_alumno=$user_id AND fl_leccion_sp=$lesson_id AND fl_programa_sp=$course_id ";
    $row2=RecuperaValor($Query2);
    $no_porcentaje=$row2['no_porcentaje'];
    
    if(!empty($no_porcentaje)){
        
        $qUERY="UPDATE vanas_laravel.teacher_assessment SET percentage=$no_porcentaje WHERE criterion_id=$criterion_id and user_id=$user_id AND lesson_id=$lesson_id AND course_id=$course_id ";
        EjecutaQuery($qUERY);

    }


}
*/


# c_com_criterio_teacher_campus | c_calculo_criterio_temp_campus  data export a teacher_assessment_vanas
#opc2

$Query3="SELECT * FROM vanas_laravel.teacher_assessment_vanas where 1=1 ";
$rs3=EjecutaQuery($Query3);
while($row=RecuperaRegistro($rs3)){

    $criterion_id=$row['criterion_id'];
    $user_id=$row['user_id'];
    $lesson_id=$row['vanas_lesson_id'];
    $course_id=$row['course_program_id'];
    $week_id=$row['week_id'];
    $vanas_group_id=$row['vanas_group_id'];
    $degrees=$row['degrees'];
    $score=$row['score'];
    $percentage=$row['percentage'];
    $comment=$row['comment'];
    $final_comment=$row['final_comment'];
    $updated_at=$row['updated_at'];
    $created_at=$row['created_at'];


    $Query2="SELECT no_porcentaje from vanas_prod.c_calculo_criterio_temp_campus 
            WHERE  fl_criterio=$criterion_id AND fl_alumno=$user_id AND fl_leccion=$lesson_id AND fl_programa=$course_id AND fl_grupo=$vanas_group_id AND fl_semana=$week_id  ";
    $row2=RecuperaValor($Query2);
    $no_porcentaje=$row2['no_porcentaje'];
    
    if(!empty($no_porcentaje)){
        
        $qUERY="UPDATE vanas_laravel.teacher_assessment_vanas SET percentage=$no_porcentaje WHERE criterion_id=$criterion_id and user_id=$user_id AND vanas_lesson_id=$lesson_id AND course_program_id=$course_id  ";
        EjecutaQuery($qUERY);

    }


}







?>