  # parametros
  $clave = $_GET['clave'];
  $clave = 26;
  $file_name = $_GET['file_name'];
  $file_name = "Soap_Box_Sess_3";
  # rutas de los archivos
  # Ruta general
  $ruta1 = "/var/www/html/vanas/dev/vanas_videos/fame/lessons";
  # Ruta del SD
  $ruta_sd = $ruta1."/video_".$clave."/video_".$clave."_sd/";
  # Nombre del archivo m3u8
  $file_name_hls = $ruta_sd."/".$file_name."_sd.m3u8";
  # Comando para convertir el archivo mp4 a m3u8
  $comando_mp4_to_hls = "/var/www/html/vanas/dev/self_pace/ffmpeg/ffmpeg -i ".$ruta_sd/$file_name.".mp4  -s hd1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 $file_name_hls";
  # Ejecutamos el comando
  exec($comando_mp4_to_hls." > /dev/null &");