<?php 
  # Libreria de funciones	
  require("../lib/self_general.php");
  # Include the ZIP.
  require('../lib/pclzip-2-8-2/pclzip.lib.php');
 

  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  
  $fl_programa_sp = RecibeParametroNumerico('fl_programa');

  
  
  #Cpmenzamos generndo archivos necesarios.
  #Recuperamos el nombre de la leccion 
  $Query="SELECT nb_programa FROM c_programa_sp WHERE fl_programa_sp=$fl_programa_sp ";
  $row=RecuperaValor($Query);
  $ds_titulo=$row['nb_programa'];
  $id_curso="ID-".$fl_programa_sp;

  $nb_leccion_sp="FAME_".$ds_titulo;

  #Creamos el nombre del archivo zip
  $nb_leccion_sp =str_replace(":","_",$nb_leccion_sp); 
  $nb_leccion_sp =str_replace(" ","_",$nb_leccion_sp); 
  $nb_leccion_sp =str_replace("-","_",$nb_leccion_sp);
  $nb_leccion_sp =str_replace("(","_",$nb_leccion_sp);
  $nb_leccion_sp =str_replace(")","_",$nb_leccion_sp);




  ######################################
  #Creamos el directorio principal
  #####################################
  $carpeta = ''.$nb_leccion_sp.'_'.$fl_instituto.'';
  if (!file_exists($carpeta)) {
      mkdir($carpeta, 0777, true);
  }





  #Geramos el scale.xml
  
  $xml='<?xml version="1.0" encoding="UTF-8"?>
<scales_definition>
</scales_definition>';
  
  $archivo_scales="".$nb_leccion_sp."_".$fl_instituto."/scales.xml";
  $archivo=fopen($archivo_scales,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_scales);

  
  #Generamos el archivo roles.xml
  $xml='<?xml version="1.0" encoding="UTF-8"?>
<roles_definition>
  <role id="5">
	<name></name>
	<shortname>student</shortname>
	<nameincourse>$@NULL@$</nameincourse>
	<description></description>
	<sortorder>5</sortorder>
	<archetype>student</archetype>
  </role>
</roles_definition>';
  
  $archivo_roles="".$nb_leccion_sp."_".$fl_instituto."/roles.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles);
  
 
  #gENRAMOS EL ARCHIVO QUESTIONS.XML
  $xml='<?xml version="1.0" encoding="UTF-8"?>
<question_categories>
</question_categories>';
  
  $archivo_roles="".$nb_leccion_sp."_".$fl_instituto."/questions.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles);
  
  #genermos el archivo outcomes.xml
  $xml='<?xml version="1.0" encoding="UTF-8"?>
<outcomes_definition>
</outcomes_definition>';
  
  $archivo_roles="".$nb_leccion_sp."_".$fl_instituto."/outcomes.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles);
  
  
  #generamos el archivo groups.xml
   $xml='<?xml version="1.0" encoding="UTF-8"?>
<groups>
  <groupings>
  </groupings>
</groups>';
  
  $archivo_roles="".$nb_leccion_sp."_".$fl_instituto."/groups.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles); 
  
  
  
  
  #geeramos el archivo gradebook.xml
   $xml='<?xml version="1.0" encoding="UTF-8"?>
<gradebook>
  <attributes>
  </attributes>
  <grade_categories>
  </grade_categories>
  <grade_items>
  </grade_items>
  <grade_letters>
  </grade_letters>
  <grade_settings>
    <grade_setting id="">
      <name>minmaxtouse</name>
      <value>1</value>
    </grade_setting>
  </grade_settings>
</gradebook>';
  
  $archivo_roles="".$nb_leccion_sp."_".$fl_instituto."/gradebook.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles); 
 
  #generamos el archivo grade_history.xml
  $xml='<?xml version="1.0" encoding="UTF-8"?>
<grade_history>
  <grade_grades>
  </grade_grades>
</grade_history>'; 
  $archivo_roles="".$nb_leccion_sp."_".$fl_instituto."/grade_history.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles); 

  
  
  #Se crea archivo files
  $xml='<?xml version="1.0" encoding="UTF-8"?>
<files>
</files>';
  $archivo_roles="".$nb_leccion_sp."_".$fl_instituto."/files.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles);  
  


  
  
  
  #Generamos el archivo back_export_moodle_cursos
  $xml='<?xml version="1.0" encoding="UTF-8"?>
<moodle_backup>
  <information>
    <name>fame_finall.mbz</name>
    <moodle_version>2018120300</moodle_version>
    <moodle_release>3.6 (Build: 20181203)</moodle_release>
    <backup_version>2018120300</backup_version>
    <backup_release>3.6</backup_release>
    <backup_date>1545188207</backup_date>
    <mnet_remoteusers>0</mnet_remoteusers>
    <include_files>1</include_files>
    <include_file_references_to_external_content>0</include_file_references_to_external_content>
    <original_wwwroot>http://localhost/moodle</original_wwwroot>
    <original_site_identifier_hash>01dca8ecc6832bdec89858352cfb1b16</original_site_identifier_hash>
    <original_course_id>2</original_course_id>
    <original_course_format>topics</original_course_format>
    <original_course_fullname>'.$ds_titulo.'</original_course_fullname>
    <original_course_shortname>'.$ds_titulo.'</original_course_shortname>
    <original_course_startdate>1545199200</original_course_startdate>
    <original_course_enddate>1576735200</original_course_enddate>
    <original_course_contextid>47</original_course_contextid>
    <original_system_contextid>1</original_system_contextid>
    <details>
      <detail backup_id="cbff2c211d676067fff74edad63a1534">
        <type>course</type>
        <format>moodle2</format>
        <interactive>1</interactive>
        <mode>10</mode>
        <execution>1</execution>
        <executiontime>0</executiontime>
      </detail>
    </details>
    <contents>';
	
$xml.=' 
      <activities>';
	  

#Recuperamos las lecciones que tiene la leccion
$Query="SELECT fg_animacion,ds_animacion,fg_ref_animacion,ds_ref_animacion,no_sketch,ds_no_sketch,fg_ref_sketch,ds_ref_sketch FROM c_leccion_sp WHERE fl_programa_sp= $fl_programa_sp ";
$rs = EjecutaQuery($Query);
$contador=1;
$contador_section=0;$contador_assigment=0;
for($i=1;$row=RecuperaRegistro($rs);$i++){
	$fg_animacion=$row[0];
	$ds_animacion=$row[1];
	$fg_ref_animacion=$row[2];
	$ds_ref_animacion=$row[3];
	$no_sketch=$row[4];
	$ds_no_sketch=$row[5];
	$fg_ref_sketch=$row[6];
	$ds_ref_sketch=$row[7];
	$contador++;
	//if( ($fg_animacion)||($fg_ref_animacion) ||($no_sketch)||($fg_ref_sketch) ){
	//	$contador++;	
	//}
    

    if($fg_animacion){
		
		$contador_assigment++;
		
		

$xml.='
        <activity>
          <moduleid>'.$contador_assigment.'</moduleid>
          <sectionid>'.$contador.'</sectionid>
          <modulename>assign</modulename>
          <title>Assignment</title>
          <directory>activities/assign_'.$contador_assigment.'</directory>
        </activity>';

	}


	if($fg_ref_animacion){
		
		$contador_assigment++;
		
		
$xml.='
        <activity>
          <moduleid>'.$contador_assigment.'</moduleid>
          <sectionid>'.$contador.'</sectionid>
          <modulename>assign</modulename>
          <title>Assignment Reference</title>
          <directory>activities/assign_'.$contador_assigment.'</directory>
        </activity>';
	
	}
		

	if($no_sketch){
		$contador_assigment++;
		
$xml.='
        <activity>
          <moduleid>'.$contador_assigment.'</moduleid>
          <sectionid>'.$contador.'</sectionid>
          <modulename>assign</modulename>
          <title>Sketch</title>
          <directory>activities/assign_'.$contador_assigment.'</directory>
        </activity>';
	
	}	


	if($fg_ref_sketch){
		$contador_assigment++;
		
$xml.='
        <activity>
          <moduleid>'.$contador_assigment.'</moduleid>
          <sectionid>'.$contador.'</sectionid>
          <modulename>assign</modulename>
          <title>Sketch Reference</title>
          <directory>activities/assign_'.$contador_assigment.'</directory>
        </activity>';
	
	}





	
}	
		
$xml.='
      </activities>';	
	
$xml.='
      <sections>';

$xml.='
        <section>
          <sectionid>1</sectionid>
          <title>0</title>
          <directory>sections/section_1</directory>
        </section>';


#Recuperamos las lecciones que tiene la leccion  y se gerara sus actividades asugnmet
$Query="SELECT no_semana,ds_titulo FROM c_leccion_sp WHERE fl_programa_sp= $fl_programa_sp ";
$rs = EjecutaQuery($Query);
$contador=1;
for($i=1;$row=RecuperaRegistro($rs);$i++){

    $contador++;

    $no_semana=$row['no_semana'];
    $ds_titulo=$row['ds_titulo'];
     
	$ds_titulo="Session ".$no_semana.": ".$ds_titulo; 

$xml.=' 
        <section>
          <sectionid>'.$contador.'</sectionid>
          <title>'.$ds_titulo.'</title>
          <directory>sections/section_'.$contador.'</directory>
        </section>';
}
/*$xml.=' <section>
          <sectionid>1</sectionid>
          <title>0</title>
          <directory>sections/section_1</directory>
        </section>
        <section>
          <sectionid>2</sectionid>
          <title>sesion1</title>
          <directory>sections/section_2</directory>
        </section>
        <section>
          <sectionid>3</sectionid>
          <title>sesion2</title>
          <directory>sections/section_3</directory>
        </section>';*/
$xml.='
      </sections>
      <course>
        <courseid>2</courseid>
        <title>'.$ds_titulo.'</title>
        <directory>course</directory>
      </course>
    </contents>
    <settings>
      <setting>
        <level>root</level>
        <name>filename</name>
        <value>fame_finall.mbz</value>
      </setting>
      <setting>
        <level>root</level>
        <name>imscc11</name>
        <value>0</value>
      </setting>
      <setting>
        <level>root</level>
        <name>users</name>
        <value>0</value>
      </setting>
      <setting>
        <level>root</level>
        <name>anonymize</name>
        <value>0</value>
      </setting>
      <setting>
        <level>root</level>
        <name>role_assignments</name>
        <value>0</value>
      </setting>
      <setting>
        <level>root</level>
        <name>activities</name>
        <value>1</value>
      </setting>
      <setting>
        <level>root</level>
        <name>blocks</name>
        <value>0</value>
      </setting>
      <setting>
        <level>root</level>
        <name>filters</name>
        <value>0</value>
      </setting>
      <setting>
        <level>root</level>
        <name>comments</name>
        <value>0</value>
      </setting>
      <setting>
        <level>root</level>
        <name>badges</name>
        <value>0</value>
      </setting>
      <setting>
        <level>root</level>
        <name>calendarevents</name>
        <value>0</value>
      </setting>
      <setting>
        <level>root</level>
        <name>userscompletion</name>
        <value>0</value>
      </setting>
      <setting>
        <level>root</level>
        <name>logs</name>
        <value>0</value>
      </setting>
      <setting>
        <level>root</level>
        <name>grade_histories</name>
        <value>0</value>
      </setting>
      <setting>
        <level>root</level>
        <name>questionbank</name>
        <value>0</value>
      </setting>
      <setting>
        <level>root</level>
        <name>groups</name>
        <value>0</value>
      </setting>
      <setting>
        <level>root</level>
        <name>competencies</name>
        <value>0</value>
      </setting>';

#Recuperamos el curso.
#Recuperamos las lecciones que tiene la leccion
$Query="SELECT no_semana,ds_titulo FROM c_leccion_sp WHERE fl_programa_sp= $fl_programa_sp ";
$rs = EjecutaQuery($Query);
$contador=0;
for($i=1;$row=RecuperaRegistro($rs);$i++){

    $contador++;

    $no_semana=$row['no_semana'];
    $ds_titulo=$row['ds_titulo']; 
	  
	$ds_titulo="Session ".$no_semana.": ".$ds_titulo;   
	  
$xml.='
      <setting>
        <level>section</level>
        <section>section_'.$contador.'</section>
        <name>section_'.$contador.'_included</name>
        <value>'.$contador.'</value>
      </setting>
      <setting>
        <level>section</level>
        <section>section_'.$contador.'</section>
        <name>section_'.$contador.'_userinfo</name>
        <value>0</value>
      </setting>';
}	  

$xml.='
    </settings>
  </information>
</moodle_backup>';
  $archivo_roles="".$nb_leccion_sp."_".$fl_instituto."/moodle_backup.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles); 
  
  
  
  
  
  ######################################
  #Creamos el directorio Course
  #####################################
  $carpeta = ''.$nb_leccion_sp.'_'.$fl_instituto.'/course';
  if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
  }
  
#generamos el archivo roles.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<roles>
  <role_overrides>
  </role_overrides>
  <role_assignments>
  </role_assignments>
</roles>'; 
  $archivo_roles=$carpeta."/roles.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles); 
  
  
  #generamos el archivo inforef.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<inforef>
  <roleref>
    <role>
      <id>5</id>
    </role>
  </roleref>
</inforef>'; 
  $archivo_roles=$carpeta."/inforef.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles); 
  
  
  
   
  #generamos el archivo enrolments.xml
  $xml='<?xml version="1.0" encoding="UTF-8"?>
<enrolments>
  <enrols>';
  
#Recuperamos las lecciones que tiene la leccion
$Query="SELECT no_semana,ds_titulo,ds_leccion FROM c_leccion_sp WHERE fl_programa_sp= $fl_programa_sp ";
$rs = EjecutaQuery($Query);
$contador=1;
$contador_section=0;
for($i=1;$row=RecuperaRegistro($rs);$i++){
	$no_semana=$row[0];
$xml.='
    <enrol id="'.$no_semana.'">
      <enrol>manual</enrol>
      <status>0</status>
      <name>$@NULL@$</name>
      <enrolperiod>0</enrolperiod>
      <enrolstartdate>0</enrolstartdate>
      <enrolenddate>0</enrolenddate>
      <expirynotify>0</expirynotify>
      <expirythreshold>86400</expirythreshold>
      <notifyall>0</notifyall>
      <password>$@NULL@$</password>
      <cost>$@NULL@$</cost>
      <currency>$@NULL@$</currency>
      <roleid>5</roleid>
      <customint1>$@NULL@$</customint1>
      <customint2>$@NULL@$</customint2>
      <customint3>$@NULL@$</customint3>
      <customint4>$@NULL@$</customint4>
      <customint5>$@NULL@$</customint5>
      <customint6>$@NULL@$</customint6>
      <customint7>$@NULL@$</customint7>
      <customint8>$@NULL@$</customint8>
      <customchar1>$@NULL@$</customchar1>
      <customchar2>$@NULL@$</customchar2>
      <customchar3>$@NULL@$</customchar3>
      <customdec1>$@NULL@$</customdec1>
      <customdec2>$@NULL@$</customdec2>
      <customtext1>$@NULL@$</customtext1>
      <customtext2>$@NULL@$</customtext2>
      <customtext3>$@NULL@$</customtext3>
      <customtext4>$@NULL@$</customtext4>
      <timecreated>1545186865</timecreated>
      <timemodified>1545186865</timemodified>
      <user_enrolments>
      </user_enrolments>
    </enrol>';
}
/*	
    <enrol id="2">
      <enrol>guest</enrol>
      <status>1</status>
      <name>$@NULL@$</name>
      <enrolperiod>0</enrolperiod>
      <enrolstartdate>0</enrolstartdate>
      <enrolenddate>0</enrolenddate>
      <expirynotify>0</expirynotify>
      <expirythreshold>0</expirythreshold>
      <notifyall>0</notifyall>
      <password></password>
      <cost>$@NULL@$</cost>
      <currency>$@NULL@$</currency>
      <roleid>0</roleid>
      <customint1>$@NULL@$</customint1>
      <customint2>$@NULL@$</customint2>
      <customint3>$@NULL@$</customint3>
      <customint4>$@NULL@$</customint4>
      <customint5>$@NULL@$</customint5>
      <customint6>$@NULL@$</customint6>
      <customint7>$@NULL@$</customint7>
      <customint8>$@NULL@$</customint8>
      <customchar1>$@NULL@$</customchar1>
      <customchar2>$@NULL@$</customchar2>
      <customchar3>$@NULL@$</customchar3>
      <customdec1>$@NULL@$</customdec1>
      <customdec2>$@NULL@$</customdec2>
      <customtext1>$@NULL@$</customtext1>
      <customtext2>$@NULL@$</customtext2>
      <customtext3>$@NULL@$</customtext3>
      <customtext4>$@NULL@$</customtext4>
      <timecreated>1545186865</timecreated>
      <timemodified>1545186865</timemodified>
      <user_enrolments>
      </user_enrolments>
    </enrol>
    <enrol id="3">
      <enrol>self</enrol>
      <status>1</status>
      <name>$@NULL@$</name>
      <enrolperiod>0</enrolperiod>
      <enrolstartdate>0</enrolstartdate>
      <enrolenddate>0</enrolenddate>
      <expirynotify>0</expirynotify>
      <expirythreshold>86400</expirythreshold>
      <notifyall>0</notifyall>
      <password>$@NULL@$</password>
      <cost>$@NULL@$</cost>
      <currency>$@NULL@$</currency>
      <roleid>5</roleid>
      <customint1>0</customint1>
      <customint2>0</customint2>
      <customint3>0</customint3>
      <customint4>1</customint4>
      <customint5>0</customint5>
      <customint6>1</customint6>
      <customint7>$@NULL@$</customint7>
      <customint8>$@NULL@$</customint8>
      <customchar1>$@NULL@$</customchar1>
      <customchar2>$@NULL@$</customchar2>
      <customchar3>$@NULL@$</customchar3>
      <customdec1>$@NULL@$</customdec1>
      <customdec2>$@NULL@$</customdec2>
      <customtext1>$@NULL@$</customtext1>
      <customtext2>$@NULL@$</customtext2>
      <customtext3>$@NULL@$</customtext3>
      <customtext4>$@NULL@$</customtext4>
      <timecreated>1545186865</timecreated>
      <timemodified>1545186865</timemodified>
      <user_enrolments>
      </user_enrolments>
    </enrol>
	<enrol id="4">
      <enrol>self</enrol>
      <status>1</status>
      <name>$@NULL@$</name>
      <enrolperiod>0</enrolperiod>
      <enrolstartdate>0</enrolstartdate>
      <enrolenddate>0</enrolenddate>
      <expirynotify>0</expirynotify>
      <expirythreshold>86400</expirythreshold>
      <notifyall>0</notifyall>
      <password>$@NULL@$</password>
      <cost>$@NULL@$</cost>
      <currency>$@NULL@$</currency>
      <roleid>5</roleid>
      <customint1>0</customint1>
      <customint2>0</customint2>
      <customint3>0</customint3>
      <customint4>1</customint4>
      <customint5>0</customint5>
      <customint6>1</customint6>
      <customint7>$@NULL@$</customint7>
      <customint8>$@NULL@$</customint8>
      <customchar1>$@NULL@$</customchar1>
      <customchar2>$@NULL@$</customchar2>
      <customchar3>$@NULL@$</customchar3>
      <customdec1>$@NULL@$</customdec1>
      <customdec2>$@NULL@$</customdec2>
      <customtext1>$@NULL@$</customtext1>
      <customtext2>$@NULL@$</customtext2>
      <customtext3>$@NULL@$</customtext3>
      <customtext4>$@NULL@$</customtext4>
      <timecreated>1545186865</timecreated>
      <timemodified>1545186865</timemodified>
      <user_enrolments>
      </user_enrolments>
    </enrol>
	<enrol id="5">
      <enrol>self</enrol>
      <status>1</status>
      <name>$@NULL@$</name>
      <enrolperiod>0</enrolperiod>
      <enrolstartdate>0</enrolstartdate>
      <enrolenddate>0</enrolenddate>
      <expirynotify>0</expirynotify>
      <expirythreshold>86400</expirythreshold>
      <notifyall>0</notifyall>
      <password>$@NULL@$</password>
      <cost>$@NULL@$</cost>
      <currency>$@NULL@$</currency>
      <roleid>5</roleid>
      <customint1>0</customint1>
      <customint2>0</customint2>
      <customint3>0</customint3>
      <customint4>1</customint4>
      <customint5>0</customint5>
      <customint6>1</customint6>
      <customint7>$@NULL@$</customint7>
      <customint8>$@NULL@$</customint8>
      <customchar1>$@NULL@$</customchar1>
      <customchar2>$@NULL@$</customchar2>
      <customchar3>$@NULL@$</customchar3>
      <customdec1>$@NULL@$</customdec1>
      <customdec2>$@NULL@$</customdec2>
      <customtext1>$@NULL@$</customtext1>
      <customtext2>$@NULL@$</customtext2>
      <customtext3>$@NULL@$</customtext3>
      <customtext4>$@NULL@$</customtext4>
      <timecreated>1545186865</timecreated>
      <timemodified>1545186865</timemodified>
      <user_enrolments>
      </user_enrolments>
    </enrol>
	<enrol id="6">
      <enrol>self</enrol>
      <status>1</status>
      <name>$@NULL@$</name>
      <enrolperiod>0</enrolperiod>
      <enrolstartdate>0</enrolstartdate>
      <enrolenddate>0</enrolenddate>
      <expirynotify>0</expirynotify>
      <expirythreshold>86400</expirythreshold>
      <notifyall>0</notifyall>
      <password>$@NULL@$</password>
      <cost>$@NULL@$</cost>
      <currency>$@NULL@$</currency>
      <roleid>5</roleid>
      <customint1>0</customint1>
      <customint2>0</customint2>
      <customint3>0</customint3>
      <customint4>1</customint4>
      <customint5>0</customint5>
      <customint6>1</customint6>
      <customint7>$@NULL@$</customint7>
      <customint8>$@NULL@$</customint8>
      <customchar1>$@NULL@$</customchar1>
      <customchar2>$@NULL@$</customchar2>
      <customchar3>$@NULL@$</customchar3>
      <customdec1>$@NULL@$</customdec1>
      <customdec2>$@NULL@$</customdec2>
      <customtext1>$@NULL@$</customtext1>
      <customtext2>$@NULL@$</customtext2>
      <customtext3>$@NULL@$</customtext3>
      <customtext4>$@NULL@$</customtext4>
      <timecreated>1545186865</timecreated>
      <timemodified>1545186865</timemodified>
      <user_enrolments>
      </user_enrolments>
    </enrol>
	<enrol id="7">
      <enrol>self</enrol>
      <status>1</status>
      <name>$@NULL@$</name>
      <enrolperiod>0</enrolperiod>
      <enrolstartdate>0</enrolstartdate>
      <enrolenddate>0</enrolenddate>
      <expirynotify>0</expirynotify>
      <expirythreshold>86400</expirythreshold>
      <notifyall>0</notifyall>
      <password>$@NULL@$</password>
      <cost>$@NULL@$</cost>
      <currency>$@NULL@$</currency>
      <roleid>5</roleid>
      <customint1>0</customint1>
      <customint2>0</customint2>
      <customint3>0</customint3>
      <customint4>1</customint4>
      <customint5>0</customint5>
      <customint6>1</customint6>
      <customint7>$@NULL@$</customint7>
      <customint8>$@NULL@$</customint8>
      <customchar1>$@NULL@$</customchar1>
      <customchar2>$@NULL@$</customchar2>
      <customchar3>$@NULL@$</customchar3>
      <customdec1>$@NULL@$</customdec1>
      <customdec2>$@NULL@$</customdec2>
      <customtext1>$@NULL@$</customtext1>
      <customtext2>$@NULL@$</customtext2>
      <customtext3>$@NULL@$</customtext3>
      <customtext4>$@NULL@$</customtext4>
      <timecreated>1545186865</timecreated>
      <timemodified>1545186865</timemodified>
      <user_enrolments>
      </user_enrolments>
    </enrol>
	<enrol id="8">
      <enrol>self</enrol>
      <status>1</status>
      <name>$@NULL@$</name>
      <enrolperiod>0</enrolperiod>
      <enrolstartdate>0</enrolstartdate>
      <enrolenddate>0</enrolenddate>
      <expirynotify>0</expirynotify>
      <expirythreshold>86400</expirythreshold>
      <notifyall>0</notifyall>
      <password>$@NULL@$</password>
      <cost>$@NULL@$</cost>
      <currency>$@NULL@$</currency>
      <roleid>5</roleid>
      <customint1>0</customint1>
      <customint2>0</customint2>
      <customint3>0</customint3>
      <customint4>1</customint4>
      <customint5>0</customint5>
      <customint6>1</customint6>
      <customint7>$@NULL@$</customint7>
      <customint8>$@NULL@$</customint8>
      <customchar1>$@NULL@$</customchar1>
      <customchar2>$@NULL@$</customchar2>
      <customchar3>$@NULL@$</customchar3>
      <customdec1>$@NULL@$</customdec1>
      <customdec2>$@NULL@$</customdec2>
      <customtext1>$@NULL@$</customtext1>
      <customtext2>$@NULL@$</customtext2>
      <customtext3>$@NULL@$</customtext3>
      <customtext4>$@NULL@$</customtext4>
      <timecreated>1545186865</timecreated>
      <timemodified>1545186865</timemodified>
      <user_enrolments>
      </user_enrolments>
    </enrol>
	<enrol id="9">
      <enrol>self</enrol>
      <status>1</status>
      <name>$@NULL@$</name>
      <enrolperiod>0</enrolperiod>
      <enrolstartdate>0</enrolstartdate>
      <enrolenddate>0</enrolenddate>
      <expirynotify>0</expirynotify>
      <expirythreshold>86400</expirythreshold>
      <notifyall>0</notifyall>
      <password>$@NULL@$</password>
      <cost>$@NULL@$</cost>
      <currency>$@NULL@$</currency>
      <roleid>5</roleid>
      <customint1>0</customint1>
      <customint2>0</customint2>
      <customint3>0</customint3>
      <customint4>1</customint4>
      <customint5>0</customint5>
      <customint6>1</customint6>
      <customint7>$@NULL@$</customint7>
      <customint8>$@NULL@$</customint8>
      <customchar1>$@NULL@$</customchar1>
      <customchar2>$@NULL@$</customchar2>
      <customchar3>$@NULL@$</customchar3>
      <customdec1>$@NULL@$</customdec1>
      <customdec2>$@NULL@$</customdec2>
      <customtext1>$@NULL@$</customtext1>
      <customtext2>$@NULL@$</customtext2>
      <customtext3>$@NULL@$</customtext3>
      <customtext4>$@NULL@$</customtext4>
      <timecreated>1545186865</timecreated>
      <timemodified>1545186865</timemodified>
      <user_enrolments>
      </user_enrolments>
    </enrol>
	<enrol id="10">
      <enrol>self</enrol>
      <status>1</status>
      <name>$@NULL@$</name>
      <enrolperiod>0</enrolperiod>
      <enrolstartdate>0</enrolstartdate>
      <enrolenddate>0</enrolenddate>
      <expirynotify>0</expirynotify>
      <expirythreshold>86400</expirythreshold>
      <notifyall>0</notifyall>
      <password>$@NULL@$</password>
      <cost>$@NULL@$</cost>
      <currency>$@NULL@$</currency>
      <roleid>5</roleid>
      <customint1>0</customint1>
      <customint2>0</customint2>
      <customint3>0</customint3>
      <customint4>1</customint4>
      <customint5>0</customint5>
      <customint6>1</customint6>
      <customint7>$@NULL@$</customint7>
      <customint8>$@NULL@$</customint8>
      <customchar1>$@NULL@$</customchar1>
      <customchar2>$@NULL@$</customchar2>
      <customchar3>$@NULL@$</customchar3>
      <customdec1>$@NULL@$</customdec1>
      <customdec2>$@NULL@$</customdec2>
      <customtext1>$@NULL@$</customtext1>
      <customtext2>$@NULL@$</customtext2>
      <customtext3>$@NULL@$</customtext3>
      <customtext4>$@NULL@$</customtext4>
      <timecreated>1545186865</timecreated>
      <timemodified>1545186865</timemodified>
      <user_enrolments>
      </user_enrolments>
    </enrol>
	<enrol id="11">
      <enrol>self</enrol>
      <status>1</status>
      <name>$@NULL@$</name>
      <enrolperiod>0</enrolperiod>
      <enrolstartdate>0</enrolstartdate>
      <enrolenddate>0</enrolenddate>
      <expirynotify>0</expirynotify>
      <expirythreshold>86400</expirythreshold>
      <notifyall>0</notifyall>
      <password>$@NULL@$</password>
      <cost>$@NULL@$</cost>
      <currency>$@NULL@$</currency>
      <roleid>5</roleid>
      <customint1>0</customint1>
      <customint2>0</customint2>
      <customint3>0</customint3>
      <customint4>1</customint4>
      <customint5>0</customint5>
      <customint6>1</customint6>
      <customint7>$@NULL@$</customint7>
      <customint8>$@NULL@$</customint8>
      <customchar1>$@NULL@$</customchar1>
      <customchar2>$@NULL@$</customchar2>
      <customchar3>$@NULL@$</customchar3>
      <customdec1>$@NULL@$</customdec1>
      <customdec2>$@NULL@$</customdec2>
      <customtext1>$@NULL@$</customtext1>
      <customtext2>$@NULL@$</customtext2>
      <customtext3>$@NULL@$</customtext3>
      <customtext4>$@NULL@$</customtext4>
      <timecreated>1545186865</timecreated>
      <timemodified>1545186865</timemodified>
      <user_enrolments>
      </user_enrolments>
    </enrol>*/
$xml.='
   </enrols>
</enrolments>'; 
  $archivo_roles=$carpeta."/enrolments.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles); 
   
 
 #Se genera el archivo courses 
  
 $xml='<?xml version="1.0" encoding="UTF-8"?>
<course id="2" contextid="47">
  <shortname>'.$ds_titulo.'</shortname>
  <fullname>'.$ds_titulo.'</fullname>
  <idnumber>'.$id_curso.'</idnumber>
  <summary></summary>
  <summaryformat>1</summaryformat>
  <format>topics</format>
  <showgrades>1</showgrades>
  <newsitems>5</newsitems>
  <startdate>1545199200</startdate>
  <enddate>1576735200</enddate>
  <marker>0</marker>
  <maxbytes>0</maxbytes>
  <legacyfiles>0</legacyfiles>
  <showreports>0</showreports>
  <visible>1</visible>
  <groupmode>0</groupmode>
  <groupmodeforce>0</groupmodeforce>
  <defaultgroupingid>0</defaultgroupingid>
  <lang></lang>
  <theme></theme>
  <timecreated>1545186863</timecreated>
  <timemodified>1545186863</timemodified>
  <requested>0</requested>
  <enablecompletion>1</enablecompletion>
  <completionnotify>0</completionnotify>
  <hiddensections>0</hiddensections>
  <coursedisplay>0</coursedisplay>
  <category id="1">
    <name>Miscellaneous</name>
    <description>$@NULL@$</description>
  </category>
  <tags>
  </tags>
</course>';
  $archivo_roles=$carpeta."/course.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles); 
  
  
 #generamos el archivo completiondefaults.xml
  $xml='<?xml version="1.0" encoding="UTF-8"?>
<course_completion_defaults>
</course_completion_defaults>'; 
  $archivo_roles=$carpeta."/completiondefaults.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles); 
  

/*

 ######################################
  #Creamos el directorio files
  #####################################
  $carpeta = '".$nb_leccion_sp."/course';
  if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
  }
  
  #generamos el archivo roles.xml
  $xml='<?xml version="1.0" encoding="UTF-8"?>
		<roles>
		  <role_overrides>
		  </role_overrides>
		  <role_assignments>
		  </role_assignments>
		</roles>'; 
  $archivo_roles=$carpeta."/roles.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles); 
*/
 ######################################
  #Creamos el directorio de numero de lecciones del curso.
  #####################################
  
  $carpeta_sections = ''.$nb_leccion_sp.'_'.$fl_instituto.'/sections';
  if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
  }
  
  
  #Creamos los direcctoirios segun la leccions que tiene.
  
  $carpeta = $carpeta_sections.'/section_1';
  if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
  }
  
  #Se genera el archivo xml section.
    
$xml='<?xml version="1.0" encoding="UTF-8"?>
<section id="1">
  <number>0</number>
  <name>$@NULL@$</name>
  <summary></summary>
  <summaryformat>1</summaryformat>
  <sequence>1</sequence>
  <visible>1</visible>
  <availabilityjson>$@NULL@$</availabilityjson>
  <timemodified>1545186864</timemodified>
</section>';
  $archivo_roles=$carpeta."/section.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles); 
  
  
  
  #Se crea inforef
  $xml='<?xml version="1.0" encoding="UTF-8"?>
<inforef>
</inforef>';
  $archivo_roles=$carpeta."/inforef.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles);
  
  


#Recuperamos las lecciones que tiene la leccion
$Query="SELECT no_semana,ds_titulo,ds_leccion FROM c_leccion_sp WHERE fl_programa_sp= $fl_programa_sp ";
$rs = EjecutaQuery($Query);
$contador=1;
$contador_section=0;
for($i=1;$row=RecuperaRegistro($rs);$i++){
     
    $contador++;
	$contador_section++;

    $no_semana=$row['no_semana'];
    $ds_titulo=$row['ds_titulo'];
	$ds_descripcion=$row['ds_leccion'];

    $ds_titulo="Session ".$no_semana.": ".$ds_titulo; 

   #Cambiamos los caracteres especiales , ya que moodle si las respeta como tal.
    $ds_descripcion =str_replace("&#47;","/",$ds_descripcion); 
    $ds_descripcion =str_replace("&quot;",'"',$ds_descripcion); 
    $ds_descripcion =str_replace("&#061;","=",$ds_descripcion); 
    $ds_descripcion =str_replace("/&gt;","&gt;",$ds_descripcion);
    $ds_descripcion =str_replace("strong","b",$ds_descripcion);
    $ds_descripcion =str_replace("em","i",$ds_descripcion);
	$ds_descripcion =str_replace("&#39;","'",$ds_descripcion);
	$ds_descripcion =str_replace("&nbsp;"," ",$ds_descripcion);
	/*$ds_descripcion =str_replace("&aacute;","á",$ds_descripcion);
	$ds_descripcion =str_replace("&eacute;","é",$ds_descripcion);
	$ds_descripcion =str_replace("&iacute;","í",$ds_descripcion);
	$ds_descripcion =str_replace("&oacute;","ó",$ds_descripcion);
	$ds_descripcion =str_replace("&uacute;","ú",$ds_descripcion);
	*/
	
    




  $carpeta = $carpeta_sections.'/section_'.$contador.'';
  if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
  }
  
  #Se genera el archivo section.
    
 $xml='<?xml version="1.0" encoding="UTF-8"?>
<section id="'.$contador.'">
  <number>'.$contador_section.'</number>
  <name>'.$ds_titulo.'</name>
  <summary>&lt;p&gt;'.$ds_descripcion.'&lt;/p&gt;</summary>
  <summaryformat>1</summaryformat>
  <sequence></sequence>
  <visible>1</visible>
  <availabilityjson>{"op":"&amp;","c":[],"showc":[]}</availabilityjson>
  <timemodified>1545188105</timemodified>
</section>';
  $archivo_roles=$carpeta."/section.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles); 
  
  
  
  #Se crea inforef
  $xml='<?xml version="1.0" encoding="UTF-8"?>
<inforef>
</inforef>';
  $archivo_roles=$carpeta."/inforef.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles);
  
  
  
  
  
  
  
  
}
  
  
/*  
  $carpeta = $carpeta_sections.'/section_2';
  if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
  }
  
  #Se genera el archivo section.
    
 $xml='<?xml version="1.0" encoding="UTF-8"?>
<section id="2">
  <number>1</number>
  <name>sesion1</name>
  <summary>&lt;p&gt;descripición niño ?&amp;nbsp; sesion1&lt;/p&gt;</summary>
  <summaryformat>1</summaryformat>
  <sequence></sequence>
  <visible>1</visible>
  <availabilityjson>{"op":"&amp;","c":[],"showc":[]}</availabilityjson>
  <timemodified>1545188105</timemodified>
</section>';
  $archivo_roles=$carpeta."/section.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles); 
  
  
  
  #Se crea inforef
  $xml='<?xml version="1.0" encoding="UTF-8"?>
<inforef>
</inforef>';
  $archivo_roles=$carpeta."/inforef.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles);
  
  
  
  
  
  $carpeta = $carpeta_sections.'/section_3';
  if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
  }
  
  #Se genera el archivo section.
    
 $xml='<?xml version="1.0" encoding="UTF-8"?>
<section id="3">
  <number>2</number>
  <name>sesion2</name>
  <summary>&lt;p&gt;descripcion de sesion2&lt;/p&gt;</summary>
  <summaryformat>1</summaryformat>
  <sequence></sequence>
  <visible>1</visible>
  <availabilityjson>{"op":"&amp;","c":[],"showc":[]}</availabilityjson>
  <timemodified>1545187819</timemodified>
</section>';
  $archivo_roles=$carpeta."/section.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles); 
  
  
  
  #Se crea inforef
  $xml='<?xml version="1.0" encoding="UTF-8"?>
<inforef>
</inforef>';
  $archivo_roles=$carpeta."/inforef.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles);  
  
  */
  
  
  
  #############finaliza opcion de creacr las sections.################3
  
  
  /*
  
  
  $carpeta = $carpeta_sections.'/section_24';
  if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
  }
  
  #Se genera el archivo xml default.
    
 $xml='<?xml version="1.0" encoding="UTF-8"?>
<section id="24">
  <number>3</number>
  <name>SESIONES3</name>
  <summary></summary>
  <summaryformat>1</summaryformat>
  <sequence></sequence>
  <visible>1</visible>
  <availabilityjson>$@NULL@$</availabilityjson>
  <timemodified>1544818975</timemodified>
</section>';
  $archivo_roles=$carpeta."/section.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles); 
  
  
  
  #Se crea inforef
  $xml='<?xml version="1.0" encoding="UTF-8"?>
<inforef>
</inforef>';
  $archivo_roles=$carpeta."/inforef.xml";
  $archivo=fopen($archivo_roles,"w+b");
  fwrite($archivo,$xml);
  fclose($archivo_roles);  
  
  
*/





#########################finaliza la creacion de seccionts



################Innicia la creacion de actividades del curso leccion.#################################################

$carpeta_activities = ''.$nb_leccion_sp.'_'.$fl_instituto.'/activities';
  if (!file_exists($carpeta_activities)) {
		mkdir($carpeta_activities, 0777, true);
  }



#Recuperamos las lecciones que tiene la leccion
$Query="SELECT fg_animacion,ds_animacion,fg_ref_animacion,ds_ref_animacion,no_sketch,ds_no_sketch,fg_ref_sketch,ds_ref_sketch FROM c_leccion_sp WHERE fl_programa_sp= $fl_programa_sp ";
$rs = EjecutaQuery($Query);
$contador_leccion=1;
$contador_asiigment=0;
for($i=1;$row=RecuperaRegistro($rs);$i++){
	$fg_animacion=$row[0];
	$ds_animacion=$row[1];
	$fg_ref_animacion=$row[2];
	$ds_ref_animacion=$row[3];
	$no_sketch=$row[4];
	$ds_no_sketch=$row[5];
	$fg_ref_sketch=$row[6];
	$ds_ref_sketch=$row[7];
	$contador_leccion++;
	//$contador_asiigment++;
	
	
	$ds_animacion =str_replace("&#47;","/",$ds_animacion); 
    $ds_animacion =str_replace("&quot;",'"',$ds_animacion); 
    $ds_animacion =str_replace("&#061;","=",$ds_animacion); 
    $ds_animacion =str_replace("/&gt;","&gt;",$ds_animacion);
    $ds_animacion =str_replace("strong","b",$ds_animacion);
    $ds_animacion =str_replace("em","i",$ds_animacion);
	$ds_animacion =str_replace("&#39;","'",$ds_animacion);
	$ds_animacion =str_replace("&nbsp;"," ",$ds_animacion);
	
	$ds_ref_animacion =str_replace("&#47;","/",$ds_ref_animacion); 
    $ds_ref_animacion =str_replace("&quot;",'"',$ds_ref_animacion); 
    $ds_ref_animacion =str_replace("&#061;","=",$ds_ref_animacion); 
    $ds_ref_animacion =str_replace("/&gt;","&gt;",$ds_ref_animacion);
    $ds_ref_animacion =str_replace("strong","b",$ds_ref_animacion);
    $ds_ref_animacion =str_replace("em","i",$ds_ref_animacion);
	$ds_ref_animacion =str_replace("&#39;","'",$ds_ref_animacion);
	$ds_ref_animacion =str_replace("&nbsp;"," ",$ds_ref_animacion);
	
	$ds_no_sketch =str_replace("&#47;","/",$ds_no_sketch); 
    $ds_no_sketch =str_replace("&quot;",'"',$ds_no_sketch); 
    $ds_no_sketch =str_replace("&#061;","=",$ds_no_sketch); 
    $ds_no_sketch =str_replace("/&gt;","&gt;",$ds_no_sketch);
    $ds_no_sketch =str_replace("strong","b",$ds_no_sketch);
    $ds_no_sketch =str_replace("em","i",$ds_no_sketch);
	$ds_no_sketch =str_replace("&#39;","'",$ds_no_sketch);
	$ds_no_sketch =str_replace("&nbsp;"," ",$ds_no_sketch);
	
	$ds_ref_sketch =str_replace("&#47;","/",$ds_ref_sketch); 
    $ds_ref_sketch =str_replace("&quot;",'"',$ds_ref_sketch); 
    $ds_ref_sketch =str_replace("&#061;","=",$ds_ref_sketch); 
    $ds_ref_sketch =str_replace("/&gt;","&gt;",$ds_ref_sketch);
    $ds_ref_sketch =str_replace("strong","b",$ds_ref_sketch);
    $ds_ref_sketch =str_replace("em","i",$ds_ref_sketch);
	$ds_ref_sketch =str_replace("&#39;","'",$ds_ref_sketch);
	$ds_ref_sketch =str_replace("&nbsp;"," ",$ds_ref_sketch);
	
	
	
	
	
	
	#Si por lo menos uno existe se screa la carpeta que contendra archivo.
	//if( ($fg_animacion) || ($fg_ref_animacion) || ($no_sketch) || ($fg_ref_sketch) ){
			
	//}
	if($fg_animacion){
		
		$contador_asiigment++;


		$carpeta_asiigne = $carpeta_activities.'/assign_'.$contador_asiigment;
		  if (!file_exists($carpeta_asiigne)) {
				mkdir($carpeta_asiigne, 0777, true);
		}
		
		
		
		
		
		 #Geramos el assign.xml
         $xml='<?xml version="1.0" encoding="UTF-8"?>
<activity id="'.$contador_asiigment.'" moduleid="'.$contador_asiigment.'" modulename="assign" contextid="53">
  <assign id="'.$contador_asiigment.'">
    <name>Assignment</name>
    <intro>&lt;p&gt;'.$ds_animacion.'&lt;/p&gt;</intro>
    <introformat>1</introformat>
    <alwaysshowdescription>1</alwaysshowdescription>
    <submissiondrafts>0</submissiondrafts>
    <sendnotifications>0</sendnotifications>
    <sendlatenotifications>0</sendlatenotifications>
    <sendstudentnotifications>1</sendstudentnotifications>
    <duedate>1547013600</duedate>
    <cutoffdate>0</cutoffdate>
    <gradingduedate>1547618400</gradingduedate>
    <allowsubmissionsfromdate>1546408800</allowsubmissionsfromdate>
    <grade>100</grade>
    <timemodified>1546475443</timemodified>
    <completionsubmit>1</completionsubmit>
    <requiresubmissionstatement>0</requiresubmissionstatement>
    <teamsubmission>0</teamsubmission>
    <requireallteammemberssubmit>0</requireallteammemberssubmit>
    <teamsubmissiongroupingid>0</teamsubmissiongroupingid>
    <blindmarking>0</blindmarking>
    <revealidentities>0</revealidentities>
    <attemptreopenmethod>none</attemptreopenmethod>
    <maxattempts>-1</maxattempts>
    <markingworkflow>0</markingworkflow>
    <markingallocation>0</markingallocation>
    <preventsubmissionnotingroup>0</preventsubmissionnotingroup>
    <userflags>
    </userflags>
    <submissions>
    </submissions>
    <grades>
    </grades>
    <plugin_configs>
      <plugin_config id="12">
        <plugin>onlinetext</plugin>
        <subtype>assignsubmission</subtype>
        <name>enabled</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="13">
        <plugin>file</plugin>
        <subtype>assignsubmission</subtype>
        <name>enabled</name>
        <value>1</value>
      </plugin_config>
      <plugin_config id="14">
        <plugin>file</plugin>
        <subtype>assignsubmission</subtype>
        <name>maxfilesubmissions</name>
        <value>20</value>
      </plugin_config>
      <plugin_config id="15">
        <plugin>file</plugin>
        <subtype>assignsubmission</subtype>
        <name>maxsubmissionsizebytes</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="16">
        <plugin>file</plugin>
        <subtype>assignsubmission</subtype>
        <name>filetypeslist</name>
        <value></value>
      </plugin_config>
      <plugin_config id="17">
        <plugin>comments</plugin>
        <subtype>assignsubmission</subtype>
        <name>enabled</name>
        <value>1</value>
      </plugin_config>
      <plugin_config id="18">
        <plugin>comments</plugin>
        <subtype>assignfeedback</subtype>
        <name>enabled</name>
        <value>1</value>
      </plugin_config>
      <plugin_config id="19">
        <plugin>comments</plugin>
        <subtype>assignfeedback</subtype>
        <name>commentinline</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="20">
        <plugin>editpdf</plugin>
        <subtype>assignfeedback</subtype>
        <name>enabled</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="21">
        <plugin>offline</plugin>
        <subtype>assignfeedback</subtype>
        <name>enabled</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="22">
        <plugin>file</plugin>
        <subtype>assignfeedback</subtype>
        <name>enabled</name>
        <value>0</value>
      </plugin_config>
    </plugin_configs>
    <overrides>
    </overrides>
  </assign>
</activity>';
		  
	  $archivo_assign=$carpeta_asiigne."/assign.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);

		
	 
#generamos el archivo grade_history.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<grade_history>
  <grade_grades>
  </grade_grades>
</grade_history>'; 
      $archivo_assign=$carpeta_asiigne."/grade_history.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);

	 
#generamos el archivo roles.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<roles>
  <role_overrides>
  </role_overrides>
  <role_assignments>
  </role_assignments>
</roles>'; 
      $archivo_assign=$carpeta_asiigne."/roles.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);

	
#generamos el archivo grading.xml
      $xml='<?xml version="1.0" encoding="UTF-8"?>
<areas>
  <area id="'.$contador_asiigment.'">
    <areaname>submissions</areaname>
    <activemethod>$@NULL@$</activemethod>
    <definitions>
    </definitions>
  </area>
</areas>'; 
      $archivo_assign=$carpeta_asiigne."/grading.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);

	  

#generamos el archivo inforef.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<inforef>
  <grade_itemref>
    <grade_item>
      <id>'.$contador_asiigment.'</id>
    </grade_item>
  </grade_itemref>
</inforef>'; 
      $archivo_assign=$carpeta_asiigne."/inforef.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);	


#generamos el archivo modules.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<module id="'.$contador_asiigment.'" version="2018120300">
  <modulename>assign</modulename>
  <sectionid>'.$contador_leccion.'</sectionid>
  <sectionnumber>'.$contador_asiigment.'</sectionnumber>
  <idnumber></idnumber>
  <added>1546479003</added>
  <score>0</score>
  <indent>0</indent>
  <visible>1</visible>
  <visibleoncoursepage>1</visibleoncoursepage>
  <visibleold>1</visibleold>
  <groupmode>0</groupmode>
  <groupingid>0</groupingid>
  <completion>1</completion>
  <completiongradeitemnumber>$@NULL@$</completiongradeitemnumber>
  <completionview>0</completionview>
  <completionexpected>0</completionexpected>
  <availability>$@NULL@$</availability>
  <showdescription>0</showdescription>
  <tags>
  </tags>
</module>'; 
      $archivo_assign=$carpeta_asiigne."/module.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);	


#generamos el archivo modules.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<activity_gradebook>
  <grade_items>
    <grade_item id="'.$contador_asiigment.'">
      <categoryid>2</categoryid>
      <itemname>tarea 5</itemname>
      <itemtype>mod</itemtype>
      <itemmodule>assign</itemmodule>
      <iteminstance>'.$contador_asiigment.'</iteminstance>
      <itemnumber>0</itemnumber>
      <iteminfo>$@NULL@$</iteminfo>
      <idnumber></idnumber>
      <calculation>$@NULL@$</calculation>
      <gradetype>1</gradetype>
      <grademax>100.00000</grademax>
      <grademin>0.00000</grademin>
      <scaleid>$@NULL@$</scaleid>
      <outcomeid>$@NULL@$</outcomeid>
      <gradepass>0.00000</gradepass>
      <multfactor>1.00000</multfactor>
      <plusfactor>0.00000</plusfactor>
      <aggregationcoef>0.00000</aggregationcoef>
      <aggregationcoef2>0.25000</aggregationcoef2>
      <weightoverride>0</weightoverride>
      <sortorder>5</sortorder>
      <display>0</display>
      <decimals>$@NULL@$</decimals>
      <hidden>0</hidden>
      <locked>0</locked>
      <locktime>0</locktime>
      <needsupdate>0</needsupdate>
      <timecreated>1546479004</timecreated>
      <timemodified>1546479005</timemodified>
      <grade_grades>
      </grade_grades>
    </grade_item>
  </grade_items>
  <grade_letters>
  </grade_letters>
</activity_gradebook>'; 
      $archivo_assign=$carpeta_asiigne."/grades.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);	





	

		
		
		
	}
	if($fg_ref_animacion){
		
		$contador_asiigment++;
		
		$carpeta_asiigne = $carpeta_activities.'/assign_'.$contador_asiigment;
		  if (!file_exists($carpeta_asiigne)) {
				mkdir($carpeta_asiigne, 0777, true);
		}
		
		
		
		
		 #Geramos el assign.xml
         $xml='<?xml version="1.0" encoding="UTF-8"?>
<activity id="'.$contador_asiigment.'" moduleid="'.$contador_asiigment.'" modulename="assign" contextid="53">
  <assign id="'.$contador_asiigment.'">
    <name>Assignment Reference</name>
    <intro>&lt;p&gt;'.$ds_ref_animacion.'&lt;/p&gt;</intro>
    <introformat>1</introformat>
    <alwaysshowdescription>1</alwaysshowdescription>
    <submissiondrafts>0</submissiondrafts>
    <sendnotifications>0</sendnotifications>
    <sendlatenotifications>0</sendlatenotifications>
    <sendstudentnotifications>1</sendstudentnotifications>
    <duedate>1547013600</duedate>
    <cutoffdate>0</cutoffdate>
    <gradingduedate>1547618400</gradingduedate>
    <allowsubmissionsfromdate>1546408800</allowsubmissionsfromdate>
    <grade>100</grade>
    <timemodified>1546475443</timemodified>
    <completionsubmit>1</completionsubmit>
    <requiresubmissionstatement>0</requiresubmissionstatement>
    <teamsubmission>0</teamsubmission>
    <requireallteammemberssubmit>0</requireallteammemberssubmit>
    <teamsubmissiongroupingid>0</teamsubmissiongroupingid>
    <blindmarking>0</blindmarking>
    <revealidentities>0</revealidentities>
    <attemptreopenmethod>none</attemptreopenmethod>
    <maxattempts>-1</maxattempts>
    <markingworkflow>0</markingworkflow>
    <markingallocation>0</markingallocation>
    <preventsubmissionnotingroup>0</preventsubmissionnotingroup>
    <userflags>
    </userflags>
    <submissions>
    </submissions>
    <grades>
    </grades>
    <plugin_configs>
      <plugin_config id="12">
        <plugin>onlinetext</plugin>
        <subtype>assignsubmission</subtype>
        <name>enabled</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="13">
        <plugin>file</plugin>
        <subtype>assignsubmission</subtype>
        <name>enabled</name>
        <value>1</value>
      </plugin_config>
      <plugin_config id="14">
        <plugin>file</plugin>
        <subtype>assignsubmission</subtype>
        <name>maxfilesubmissions</name>
        <value>20</value>
      </plugin_config>
      <plugin_config id="15">
        <plugin>file</plugin>
        <subtype>assignsubmission</subtype>
        <name>maxsubmissionsizebytes</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="16">
        <plugin>file</plugin>
        <subtype>assignsubmission</subtype>
        <name>filetypeslist</name>
        <value></value>
      </plugin_config>
      <plugin_config id="17">
        <plugin>comments</plugin>
        <subtype>assignsubmission</subtype>
        <name>enabled</name>
        <value>1</value>
      </plugin_config>
      <plugin_config id="18">
        <plugin>comments</plugin>
        <subtype>assignfeedback</subtype>
        <name>enabled</name>
        <value>1</value>
      </plugin_config>
      <plugin_config id="19">
        <plugin>comments</plugin>
        <subtype>assignfeedback</subtype>
        <name>commentinline</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="20">
        <plugin>editpdf</plugin>
        <subtype>assignfeedback</subtype>
        <name>enabled</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="21">
        <plugin>offline</plugin>
        <subtype>assignfeedback</subtype>
        <name>enabled</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="22">
        <plugin>file</plugin>
        <subtype>assignfeedback</subtype>
        <name>enabled</name>
        <value>0</value>
      </plugin_config>
    </plugin_configs>
    <overrides>
    </overrides>
  </assign>
</activity>';
		  
	  $archivo_assign=$carpeta_asiigne."/assign.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);

		
		
		
	 
#generamos el archivo grade_history.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<grade_history>
  <grade_grades>
  </grade_grades>
</grade_history>'; 
      $archivo_assign=$carpeta_asiigne."/grade_history.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);

	 
#generamos el archivo grade_history.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<roles>
  <role_overrides>
  </role_overrides>
  <role_assignments>
  </role_assignments>
</roles>'; 
      $archivo_assign=$carpeta_asiigne."/roles.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);

	
      
      #generamos el archivo grading.xml
      $xml='<?xml version="1.0" encoding="UTF-8"?>
<areas>
  <area id="'.$contador_asiigment.'">
    <areaname>submissions</areaname>
    <activemethod>$@NULL@$</activemethod>
    <definitions>
    </definitions>
  </area>
</areas>'; 
      $archivo_assign=$carpeta_asiigne."/grading.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);



#generamos el archivo inforef.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<inforef>
  <grade_itemref>
    <grade_item>
      <id>'.$contador_asiigment.'</id>
    </grade_item>
  </grade_itemref>
</inforef>'; 
      $archivo_assign=$carpeta_asiigne."/inforef.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);	



#generamos el archivo modules.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<module id="'.$contador_asiigment.'" version="2018120300">
  <modulename>assign</modulename>
  <sectionid>'.$contador_leccion.'</sectionid>
  <sectionnumber>'.$contador_asiigment.'</sectionnumber>
  <idnumber></idnumber>
  <added>1546479003</added>
  <score>0</score>
  <indent>0</indent>
  <visible>1</visible>
  <visibleoncoursepage>1</visibleoncoursepage>
  <visibleold>1</visibleold>
  <groupmode>0</groupmode>
  <groupingid>0</groupingid>
  <completion>1</completion>
  <completiongradeitemnumber>$@NULL@$</completiongradeitemnumber>
  <completionview>0</completionview>
  <completionexpected>0</completionexpected>
  <availability>$@NULL@$</availability>
  <showdescription>0</showdescription>
  <tags>
  </tags>
</module>'; 
      $archivo_assign=$carpeta_asiigne."/module.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);	


#generamos el archivo modules.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<activity_gradebook>
  <grade_items>
    <grade_item id="'.$contador_asiigment.'">
      <categoryid>2</categoryid>
      <itemname>tarea 5</itemname>
      <itemtype>mod</itemtype>
      <itemmodule>assign</itemmodule>
      <iteminstance>'.$contador_asiigment.'</iteminstance>
      <itemnumber>0</itemnumber>
      <iteminfo>$@NULL@$</iteminfo>
      <idnumber></idnumber>
      <calculation>$@NULL@$</calculation>
      <gradetype>1</gradetype>
      <grademax>100.00000</grademax>
      <grademin>0.00000</grademin>
      <scaleid>$@NULL@$</scaleid>
      <outcomeid>$@NULL@$</outcomeid>
      <gradepass>0.00000</gradepass>
      <multfactor>1.00000</multfactor>
      <plusfactor>0.00000</plusfactor>
      <aggregationcoef>0.00000</aggregationcoef>
      <aggregationcoef2>0.25000</aggregationcoef2>
      <weightoverride>0</weightoverride>
      <sortorder>5</sortorder>
      <display>0</display>
      <decimals>$@NULL@$</decimals>
      <hidden>0</hidden>
      <locked>0</locked>
      <locktime>0</locktime>
      <needsupdate>0</needsupdate>
      <timecreated>1546479004</timecreated>
      <timemodified>1546479005</timemodified>
      <grade_grades>
      </grade_grades>
    </grade_item>
  </grade_items>
  <grade_letters>
  </grade_letters>
</activity_gradebook>'; 
      $archivo_assign=$carpeta_asiigne."/grades.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);	


		
		
		
		
		
		
		
	}
	
	if($no_sketch){
		
		
		$contador_asiigment++;
		
		$carpeta_asiigne = $carpeta_activities.'/assign_'.$contador_asiigment;
		  if (!file_exists($carpeta_asiigne)) {
				mkdir($carpeta_asiigne, 0777, true);
		}
		
		
		
		
		 #Geramos el assign.xml
         $xml='<?xml version="1.0" encoding="UTF-8"?>
<activity id="'.$contador_asiigment.'" moduleid="'.$contador_asiigment.'" modulename="assign" contextid="53">
  <assign id="'.$contador_asiigment.'">
    <name>Sketch</name>
    <intro>&lt;p&gt;'.$ds_no_sketch.'&lt;/p&gt;</intro>
    <introformat>1</introformat>
    <alwaysshowdescription>1</alwaysshowdescription>
    <submissiondrafts>0</submissiondrafts>
    <sendnotifications>0</sendnotifications>
    <sendlatenotifications>0</sendlatenotifications>
    <sendstudentnotifications>1</sendstudentnotifications>
    <duedate>1547013600</duedate>
    <cutoffdate>0</cutoffdate>
    <gradingduedate>1547618400</gradingduedate>
    <allowsubmissionsfromdate>1546408800</allowsubmissionsfromdate>
    <grade>100</grade>
    <timemodified>1546475443</timemodified>
    <completionsubmit>1</completionsubmit>
    <requiresubmissionstatement>0</requiresubmissionstatement>
    <teamsubmission>0</teamsubmission>
    <requireallteammemberssubmit>0</requireallteammemberssubmit>
    <teamsubmissiongroupingid>0</teamsubmissiongroupingid>
    <blindmarking>0</blindmarking>
    <revealidentities>0</revealidentities>
    <attemptreopenmethod>none</attemptreopenmethod>
    <maxattempts>-1</maxattempts>
    <markingworkflow>0</markingworkflow>
    <markingallocation>0</markingallocation>
    <preventsubmissionnotingroup>0</preventsubmissionnotingroup>
    <userflags>
    </userflags>
    <submissions>
    </submissions>
    <grades>
    </grades>
    <plugin_configs>
      <plugin_config id="12">
        <plugin>onlinetext</plugin>
        <subtype>assignsubmission</subtype>
        <name>enabled</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="13">
        <plugin>file</plugin>
        <subtype>assignsubmission</subtype>
        <name>enabled</name>
        <value>1</value>
      </plugin_config>
      <plugin_config id="14">
        <plugin>file</plugin>
        <subtype>assignsubmission</subtype>
        <name>maxfilesubmissions</name>
        <value>20</value>
      </plugin_config>
      <plugin_config id="15">
        <plugin>file</plugin>
        <subtype>assignsubmission</subtype>
        <name>maxsubmissionsizebytes</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="16">
        <plugin>file</plugin>
        <subtype>assignsubmission</subtype>
        <name>filetypeslist</name>
        <value></value>
      </plugin_config>
      <plugin_config id="17">
        <plugin>comments</plugin>
        <subtype>assignsubmission</subtype>
        <name>enabled</name>
        <value>1</value>
      </plugin_config>
      <plugin_config id="18">
        <plugin>comments</plugin>
        <subtype>assignfeedback</subtype>
        <name>enabled</name>
        <value>1</value>
      </plugin_config>
      <plugin_config id="19">
        <plugin>comments</plugin>
        <subtype>assignfeedback</subtype>
        <name>commentinline</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="20">
        <plugin>editpdf</plugin>
        <subtype>assignfeedback</subtype>
        <name>enabled</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="21">
        <plugin>offline</plugin>
        <subtype>assignfeedback</subtype>
        <name>enabled</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="22">
        <plugin>file</plugin>
        <subtype>assignfeedback</subtype>
        <name>enabled</name>
        <value>0</value>
      </plugin_config>
    </plugin_configs>
    <overrides>
    </overrides>
  </assign>
</activity>';
		  
	  $archivo_assign=$carpeta_asiigne."/assign.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);

		
		
	 
#generamos el archivo grade_history.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<grade_history>
  <grade_grades>
  </grade_grades>
</grade_history>'; 
      $archivo_assign=$carpeta_asiigne."/grade_history.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);

	 
#generamos el archivo grade_history.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<roles>
  <role_overrides>
  </role_overrides>
  <role_assignments>
  </role_assignments>
</roles>'; 
      $archivo_assign=$carpeta_asiigne."/roles.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);

	
#generamos el archivo grading.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<areas>
  <area id="'.$contador_asiigment.'">
    <areaname>submissions</areaname>
    <activemethod>$@NULL@$</activemethod>
    <definitions>
    </definitions>
  </area>
</areas>'; 
      $archivo_assign=$carpeta_asiigne."/grading.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);

	
#generamos el archivo inforef.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<inforef>
  <grade_itemref>
    <grade_item>
      <id>'.$contador_asiigment.'</id>
    </grade_item>
  </grade_itemref>
</inforef>'; 
      $archivo_assign=$carpeta_asiigne."/inforef.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);	



#generamos el archivo modules.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<module id="'.$contador_asiigment.'" version="2018120300">
  <modulename>assign</modulename>
  <sectionid>'.$contador_leccion.'</sectionid>
  <sectionnumber>'.$contador_asiigment.'</sectionnumber>
  <idnumber></idnumber>
  <added>1546479003</added>
  <score>0</score>
  <indent>0</indent>
  <visible>1</visible>
  <visibleoncoursepage>1</visibleoncoursepage>
  <visibleold>1</visibleold>
  <groupmode>0</groupmode>
  <groupingid>0</groupingid>
  <completion>1</completion>
  <completiongradeitemnumber>$@NULL@$</completiongradeitemnumber>
  <completionview>0</completionview>
  <completionexpected>0</completionexpected>
  <availability>$@NULL@$</availability>
  <showdescription>0</showdescription>
  <tags>
  </tags>
</module>'; 
      $archivo_assign=$carpeta_asiigne."/module.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);	


#generamos el archivo modules.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<activity_gradebook>
  <grade_items>
    <grade_item id="'.$contador_asiigment.'">
      <categoryid>2</categoryid>
      <itemname>tarea 5</itemname>
      <itemtype>mod</itemtype>
      <itemmodule>assign</itemmodule>
      <iteminstance>'.$contador_asiigment.'</iteminstance>
      <itemnumber>0</itemnumber>
      <iteminfo>$@NULL@$</iteminfo>
      <idnumber></idnumber>
      <calculation>$@NULL@$</calculation>
      <gradetype>1</gradetype>
      <grademax>100.00000</grademax>
      <grademin>0.00000</grademin>
      <scaleid>$@NULL@$</scaleid>
      <outcomeid>$@NULL@$</outcomeid>
      <gradepass>0.00000</gradepass>
      <multfactor>1.00000</multfactor>
      <plusfactor>0.00000</plusfactor>
      <aggregationcoef>0.00000</aggregationcoef>
      <aggregationcoef2>0.25000</aggregationcoef2>
      <weightoverride>0</weightoverride>
      <sortorder>5</sortorder>
      <display>0</display>
      <decimals>$@NULL@$</decimals>
      <hidden>0</hidden>
      <locked>0</locked>
      <locktime>0</locktime>
      <needsupdate>0</needsupdate>
      <timecreated>1546479004</timecreated>
      <timemodified>1546479005</timemodified>
      <grade_grades>
      </grade_grades>
    </grade_item>
  </grade_items>
  <grade_letters>
  </grade_letters>
</activity_gradebook>'; 
      $archivo_assign=$carpeta_asiigne."/grades.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);	


		
		
		
		
		
		
		
	}
	if($fg_ref_sketch){
		
		
		
		$contador_asiigment++;
		
		$carpeta_asiigne = $carpeta_activities.'/assign_'.$contador_asiigment;
		  if (!file_exists($carpeta_asiigne)) {
				mkdir($carpeta_asiigne, 0777, true);
		}
		
		
		
		
		 #Geramos el assign.xml
         $xml='<?xml version="1.0" encoding="UTF-8"?>
<activity id="'.$contador_asiigment.'" moduleid="'.$contador_asiigment.'" modulename="assign" contextid="53">
  <assign id="'.$contador_asiigment.'">
    <name>Sketch Reference</name>
    <intro>&lt;p&gt;'.$ds_no_sketch.'&lt;/p&gt;</intro>
    <introformat>1</introformat>
    <alwaysshowdescription>1</alwaysshowdescription>
    <submissiondrafts>0</submissiondrafts>
    <sendnotifications>0</sendnotifications>
    <sendlatenotifications>0</sendlatenotifications>
    <sendstudentnotifications>1</sendstudentnotifications>
    <duedate>1547013600</duedate>
    <cutoffdate>0</cutoffdate>
    <gradingduedate>1547618400</gradingduedate>
    <allowsubmissionsfromdate>1546408800</allowsubmissionsfromdate>
    <grade>100</grade>
    <timemodified>1546475443</timemodified>
    <completionsubmit>1</completionsubmit>
    <requiresubmissionstatement>0</requiresubmissionstatement>
    <teamsubmission>0</teamsubmission>
    <requireallteammemberssubmit>0</requireallteammemberssubmit>
    <teamsubmissiongroupingid>0</teamsubmissiongroupingid>
    <blindmarking>0</blindmarking>
    <revealidentities>0</revealidentities>
    <attemptreopenmethod>none</attemptreopenmethod>
    <maxattempts>-1</maxattempts>
    <markingworkflow>0</markingworkflow>
    <markingallocation>0</markingallocation>
    <preventsubmissionnotingroup>0</preventsubmissionnotingroup>
    <userflags>
    </userflags>
    <submissions>
    </submissions>
    <grades>
    </grades>
    <plugin_configs>
      <plugin_config id="12">
        <plugin>onlinetext</plugin>
        <subtype>assignsubmission</subtype>
        <name>enabled</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="13">
        <plugin>file</plugin>
        <subtype>assignsubmission</subtype>
        <name>enabled</name>
        <value>1</value>
      </plugin_config>
      <plugin_config id="14">
        <plugin>file</plugin>
        <subtype>assignsubmission</subtype>
        <name>maxfilesubmissions</name>
        <value>20</value>
      </plugin_config>
      <plugin_config id="15">
        <plugin>file</plugin>
        <subtype>assignsubmission</subtype>
        <name>maxsubmissionsizebytes</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="16">
        <plugin>file</plugin>
        <subtype>assignsubmission</subtype>
        <name>filetypeslist</name>
        <value></value>
      </plugin_config>
      <plugin_config id="17">
        <plugin>comments</plugin>
        <subtype>assignsubmission</subtype>
        <name>enabled</name>
        <value>1</value>
      </plugin_config>
      <plugin_config id="18">
        <plugin>comments</plugin>
        <subtype>assignfeedback</subtype>
        <name>enabled</name>
        <value>1</value>
      </plugin_config>
      <plugin_config id="19">
        <plugin>comments</plugin>
        <subtype>assignfeedback</subtype>
        <name>commentinline</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="20">
        <plugin>editpdf</plugin>
        <subtype>assignfeedback</subtype>
        <name>enabled</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="21">
        <plugin>offline</plugin>
        <subtype>assignfeedback</subtype>
        <name>enabled</name>
        <value>0</value>
      </plugin_config>
      <plugin_config id="22">
        <plugin>file</plugin>
        <subtype>assignfeedback</subtype>
        <name>enabled</name>
        <value>0</value>
      </plugin_config>
    </plugin_configs>
    <overrides>
    </overrides>
  </assign>
</activity>';
		  
	  $archivo_assign=$carpeta_asiigne."/assign.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);

		
		
	 
#generamos el archivo grade_history.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<grade_history>
  <grade_grades>
  </grade_grades>
</grade_history>'; 
      $archivo_assign=$carpeta_asiigne."/grade_history.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);

	 
#generamos el archivo grade_history.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<roles>
  <role_overrides>
  </role_overrides>
  <role_assignments>
  </role_assignments>
</roles>'; 
      $archivo_assign=$carpeta_asiigne."/roles.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);

	
#generamos el archivo grading.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<areas>
  <area id="'.$contador_asiigment.'">
    <areaname>submissions</areaname>
    <activemethod>$@NULL@$</activemethod>
    <definitions>
    </definitions>
  </area>
</areas>'; 
      $archivo_assign=$carpeta_asiigne."/grading.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);

	
#generamos el archivo inforef.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<inforef>
  <grade_itemref>
    <grade_item>
      <id>'.$contador_asiigment.'</id>
    </grade_item>
  </grade_itemref>
</inforef>'; 
      $archivo_assign=$carpeta_asiigne."/inforef.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);	



#generamos el archivo modules.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<module id="'.$contador_asiigment.'" version="2018120300">
  <modulename>assign</modulename>
  <sectionid>'.$contador_leccion.'</sectionid>
  <sectionnumber>'.$contador_asiigment.'</sectionnumber>
  <idnumber></idnumber>
  <added>1546479003</added>
  <score>0</score>
  <indent>0</indent>
  <visible>1</visible>
  <visibleoncoursepage>1</visibleoncoursepage>
  <visibleold>1</visibleold>
  <groupmode>0</groupmode>
  <groupingid>0</groupingid>
  <completion>1</completion>
  <completiongradeitemnumber>$@NULL@$</completiongradeitemnumber>
  <completionview>0</completionview>
  <completionexpected>0</completionexpected>
  <availability>$@NULL@$</availability>
  <showdescription>0</showdescription>
  <tags>
  </tags>
</module>'; 
      $archivo_assign=$carpeta_asiigne."/module.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);	


#generamos el archivo modules.xml
$xml='<?xml version="1.0" encoding="UTF-8"?>
<activity_gradebook>
  <grade_items>
    <grade_item id="'.$contador_asiigment.'">
      <categoryid>2</categoryid>
      <itemname>tarea 5</itemname>
      <itemtype>mod</itemtype>
      <itemmodule>assign</itemmodule>
      <iteminstance>'.$contador_asiigment.'</iteminstance>
      <itemnumber>0</itemnumber>
      <iteminfo>$@NULL@$</iteminfo>
      <idnumber></idnumber>
      <calculation>$@NULL@$</calculation>
      <gradetype>1</gradetype>
      <grademax>100.00000</grademax>
      <grademin>0.00000</grademin>
      <scaleid>$@NULL@$</scaleid>
      <outcomeid>$@NULL@$</outcomeid>
      <gradepass>0.00000</gradepass>
      <multfactor>1.00000</multfactor>
      <plusfactor>0.00000</plusfactor>
      <aggregationcoef>0.00000</aggregationcoef>
      <aggregationcoef2>0.25000</aggregationcoef2>
      <weightoverride>0</weightoverride>
      <sortorder>5</sortorder>
      <display>0</display>
      <decimals>$@NULL@$</decimals>
      <hidden>0</hidden>
      <locked>0</locked>
      <locktime>0</locktime>
      <needsupdate>0</needsupdate>
      <timecreated>1546479004</timecreated>
      <timemodified>1546479005</timemodified>
      <grade_grades>
      </grade_grades>
    </grade_item>
  </grade_items>
  <grade_letters>
  </grade_letters>
</activity_gradebook>'; 
      $archivo_assign=$carpeta_asiigne."/grades.xml";
	  $archivo=fopen($archivo_assign,"w+b");
	  fwrite($archivo,$xml);
	  fclose($archivo_assign);	


	
		
		
		
		
		
		
		
		
		
		
		
	}
	
	
	
	
	
	
	
	

	
	
	
}




 
  $zip = new ZipArchive();
  //Nombre del archivo ZIP que se creara
  $archivo = $nb_leccion_sp."_".$fl_instituto.".zip";

  $zip->open($archivo,ZIPARCHIVE::CREATE);


  //Nombre y ruta del archivo que se agregara al zip
  $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/scales.xml","scales.xml");
  $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/roles.xml","roles.xml");
  $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/questions.xml","questions.xml");
  $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/outcomes.xml","outcomes.xml");
  $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/groups.xml","groups.xml");
  $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/gradebook.xml","gradebook.xml");
  $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/grade_history.xml","grade_history.xml");
  $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/moodle_backup.xml","moodle_backup.xml");
  $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/files.xml","files.xml");
  
  $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/course/roles.xml","course/roles.xml");
  $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/course/inforef.xml","course/inforef.xml");
  $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/course/enrolments.xml","course/enrolments.xml");
  $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/course/course.xml","course/course.xml");
  $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/course/completiondefaults.xml","course/completiondefaults.xml");

   
  $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/sections/section_1/inforef.xml","sections/section_1/inforef.xml");
  $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/sections/section_1/section.xml","sections/section_1/section.xml");
  
  #Recuperamos las lecciones que tiene la leccion
  $Query="SELECT no_semana,ds_titulo FROM c_leccion_sp WHERE fl_programa_sp= $fl_programa_sp ";
  $rs = EjecutaQuery($Query);
  $contador=1;
  for($i=1;$row=RecuperaRegistro($rs);$i++){

	  $contador++; 
	  
	  $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/sections/section_".$contador."/inforef.xml","sections/section_".$contador."/inforef.xml");
	  $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/sections/section_".$contador."/section.xml","sections/section_".$contador."/section.xml");
	 
  }

    #Agregamos las activities.
	#Recuperamos las lecciones que tiene la leccion
	$Query="SELECT fg_animacion,ds_animacion,fg_ref_animacion,ds_ref_animacion,no_sketch,ds_no_sketch,fg_ref_sketch,ds_ref_sketch FROM c_leccion_sp WHERE fl_programa_sp= $fl_programa_sp ";
	$rs = EjecutaQuery($Query);
	$contador_leccion=1;
	$contador_asiigment=0;
	for($i=1;$row=RecuperaRegistro($rs);$i++){
		$fg_animacion=$row[0];
		$ds_animacion=$row[1];
		$fg_ref_animacion=$row[2];
		$ds_ref_animacion=$row[3];
		$no_sketch=$row[4];
		$ds_no_sketch=$row[5];
		$fg_ref_sketch=$row[6];
		$ds_ref_sketch=$row[7];
		$contador_leccion++;
		//$contador_asiigment++;
		
		
		if($fg_animacion){
			
			$contador_asiigment++;
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/assign.xml","activities/assign_".$contador_asiigment."/assign.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/grade_history.xml","activities/assign_".$contador_asiigment."/grade_history.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/roles.xml","activities/assign_".$contador_asiigment."/roles.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/grading.xml","activities/assign_".$contador_asiigment."/grading.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/inforef.xml","activities/assign_".$contador_asiigment."/inforef.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/module.xml","activities/assign_".$contador_asiigment."/module.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/grades.xml","activities/assign_".$contador_asiigment."/grades.xml");
			
		}
		
		
		if($fg_ref_animacion){
		
			$contador_asiigment++;
		    $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/assign.xml","activities/assign_".$contador_asiigment."/assign.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/grade_history.xml","activities/assign_".$contador_asiigment."/grade_history.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/roles.xml","activities/assign_".$contador_asiigment."/roles.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/grading.xml","activities/assign_".$contador_asiigment."/grading.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/inforef.xml","activities/assign_".$contador_asiigment."/inforef.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/module.xml","activities/assign_".$contador_asiigment."/module.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/grades.xml","activities/assign_".$contador_asiigment."/grades.xml");
			
		}
		
		if($no_sketch){
			
		    $contador_asiigment++;
		    $zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/assign.xml","activities/assign_".$contador_asiigment."/assign.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/grade_history.xml","activities/assign_".$contador_asiigment."/grade_history.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/roles.xml","activities/assign_".$contador_asiigment."/roles.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/grading.xml","activities/assign_".$contador_asiigment."/grading.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/inforef.xml","activities/assign_".$contador_asiigment."/inforef.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/module.xml","activities/assign_".$contador_asiigment."/module.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/grades.xml","activities/assign_".$contador_asiigment."/grades.xml");
		
		}
		if($fg_ref_sketch){
			$contador_asiigment++;
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/assign.xml","activities/assign_".$contador_asiigment."/assign.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/grade_history.xml","activities/assign_".$contador_asiigment."/grade_history.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/roles.xml","activities/assign_".$contador_asiigment."/roles.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/grading.xml","activities/assign_".$contador_asiigment."/grading.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/inforef.xml","activities/assign_".$contador_asiigment."/inforef.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/module.xml","activities/assign_".$contador_asiigment."/module.xml");
			$zip->addfile("".$nb_leccion_sp."_".$fl_instituto."/activities/assign_".$contador_asiigment."/grades.xml","activities/assign_".$contador_asiigment."/grades.xml");
			
		}





	}





  
  $zip->close();
  
  
  



//$url="/var/www/html/vanas/dev/fame/site/".$nb_leccion_sp."_".$fl_instituto.".zip";
$url2="/var/www/html/vanas/fame/site/".$nb_leccion_sp."_".$fl_instituto;

#Eliminamos el directorio creado y el archivo para liberar espacio en disco.
deleteDirectory($url2);
//$entro=unlink($url);


?>


<script>



</script>




