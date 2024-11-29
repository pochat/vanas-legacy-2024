<?php
  
  # Libreria de funciones
  require("lib/sp_general.inc.php");
  
  // $Query = "SELECT fl_video_contenido, ds_ruta_video FROM k_video_contenido ORDER BY fl_video_contenido DESC ";
  $Query = "SELECT b.fl_video_contenido, nb_programa, nb_pagina, b.ds_ruta_video, CONCAT(a.cl_pagina,'_',a.fl_programa,'_',a.no_grado)
  FROM k_video_contenido b 
  LEFT JOIN c_pagina a ON  (a.cl_pagina=b.cl_pagina AND a.fl_programa=b.fl_programa AND a.no_grado=b.no_grado)
  LEFT JOIN c_programa c ON(c.fl_programa=a.fl_programa)
  ORDER BY fl_video_contenido DESC ";
  $rs = EjecutaQuery($Query);
  $ruta1 = "/mnt/data2/vanas/vanas_videos/campus/student_library/";
  $ruta2 = "/var/www/html/vanas_videos/";
  echo "<table>
    <tr>
      <th>No</th>
      <th>Name Video</th>
      <th>Version anterior</th>
      <th>Status</th>
      <th>Programa</th>
      <th>Titulo</th>
      <th>Id</th>
      <th>Duration</th>
    </tr>";
  for($i=0;$row=RecuperaRegistro($rs);$i++){
    $fl_video_contenido = $row[0];
    $nb_programa = $row[1];
    $nb_pagina = $row[2];
    $ds_ruta_video = $row[3];
    $id = $row[4];
    $ffpmeg = "/usr/bin/ffmpeg ";
    $ruta_video = $ruta1."video_".$fl_video_contenido."/video_".$fl_video_contenido."_hd/".array_shift(explode('.',$ds_ruta_video)).".m3u8";
    $imagen = $ruta1."video_".$fl_video_contenido."/video_".$fl_video_contenido."_hd/img_%d.png";
    $comando_image_sd = $ffpmeg." -i $ruta_video -ss 00:00:06 -vframes 1 $imagen";    
    $ruta_video2 = $ruta2.$ds_ruta_video;
    $duration  = Duration($ffpmeg, $ruta_video);
    # Si existe el archivo convertido
    if(file_exists($ruta_video)){
      $v1 = true;
      # actualiza el tiempo del video
      EjecutaQuery("UPDATE k_video_contenido SET ds_duration='".$duration."' WHERE fl_video_contenido=".$fl_video_contenido);
    }
    else
      $v1 = false;
    # Si existe la version anterior
    if(file_exists($ruta_video2))
      $v2 = true;
    else
      $v2 = false;
    $st = "";
    # No existe el archivo viejo y no se genero el archivo nuevo
    if($v1==false && $v2==false)
      $st = "style='color:red;'";
    # Si existe archivo viejo pero no el nuevo
    if($v2==true  && $v1==false)
      $st = "style='color:blue;'";
    # crea la imagen
    // if($v1 == true){
      // exec($comando_image_sd);
      // echo $comando_image_sd."<br/>";
    // }
    echo "<tr ".$st.">
            <td>".$fl_video_contenido."</td>
            <td>".$ds_ruta_video."</td>";
            if($v2==true)           
             echo "<td>Yes</td>";
           else
             echo "<td>Not</td>";
            if($v1==true)           
             echo "<td>Yes</td>";
            else
             echo "<td>Not</td>"; 
            echo "
            <td>".$nb_programa."</td>
            <td>".$nb_pagina."</td>
            <td>".$id."</td>
            <td>".$duration."</td>";
    echo "</tr>";
  }
  echo "</table>";
  
  
  function Duration($ffpmeg, $file,$segodos=false){

  //$time = 00:00:00.000 format
  $time =  exec($ffpmeg." -i ".$file." 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//");

  $duration = explode(".",$time);
  if($segundos==true)
  $duration_in_seconds = $duration[0]*3600 + $duration[1]*60+ round($duration[2]);

  return $duration[0];

}
?>