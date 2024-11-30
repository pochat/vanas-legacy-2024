<?php
  
  # Completa ligas que no tengan http
  function CompletaLigas($text) {
    
    $text = str_replace("www\.", "http://www.", $text);
    $text = str_replace("http://http://www\.", "http://www.", $text);
    $text = str_replace("https://http://www\.", "https://www.", $text);
    return $text;
  }
  
  # Convierte links de Youtube en videos embebidos en un iframe
  function iframe_youtube($text) {
    
    $base = "http://www.youtube.com/watch?v=";
    $text = str_replace("http://youtu.be/", $base, $text);
    while(stripos($text, $base) !== false) {
      $start = stripos($text, $base);
      $len = strlen($base);
      $v_id = substr($text, $start+$len, 11);
      if(($end = stripos($text, " ", $start)) !== false)
        $sus = substr($text, $start, ($end-$start));
      else
        $sus = substr($text, $start);
      $c_iframe = "<iframe width=\"480\" height=\"360\" src='http://www.youtube.com/embed/$v_id' frameborder='0' allowfullscreen></iframe>";
      $text = str_replace($sus, $c_iframe, $text);
    }
    return $text;
  }
  
  # Convierte links de Vimeo en videos embebidos en un iframe
  function iframe_vimeo($text) {
    
    if(stripos($text, "<iframe ") !== false)
      return $text;
    $base = "http://vimeo.com/";
    while(stripos($text, $base) !== false) {
      $start = stripos($text, $base);
      $len = strlen($base);
      $v_id = substr($text, $start+$len, 8);
      if(!ValidaEntero($v_id))
        return $text;
      if(($end = stripos($text, " ", $start)) !== false)
        $sus = substr($text, $start, ($end-$start));
      else
        $sus = substr($text, $start);
      $c_iframe = "<iframe src='http://player.vimeo.com/video/$v_id?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff' width=\"480\" height=\"270\" frameborder='0' webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>";
      $text = str_replace($sus, $c_iframe, $text);
    }
    return $text;
  }
  
  # Separa iframes para que queden soos en un parrafo
  function SeparaFrames($text) {
    
    $text = str_replace("<iframe ", "<p><iframe ", $text);
    $text = str_replace("</iframe>", "</iframe></p>", $text);
    $text = str_replace("<p><p><iframe ", "<p><iframe ", $text);
    $text = str_replace("</iframe></p></p>", "</iframe></p>", $text);
    $text = str_replace("</p>", " </p>", $text);
    
    $base = "<iframe ";
    $resto = $text;
    while(stripos($resto, $base) !== false) {
      $start = stripos($resto, $base);
      $end = stripos($resto, "</iframe>", $start);
      $c_iframe = substr($resto, $start, ($end-$start));
      $resto = substr($resto, ($end+9));
      $c_width_start = stripos($c_iframe, "width=");
      $c_width_end = stripos($c_iframe, "\"", ($c_width_start+7));
      $c_width = substr($c_iframe, ($c_width_start+7), ($c_width_end-($c_width_start+7)));
      $c_height_start = stripos($c_iframe, "height=");
      $c_height_end = stripos($c_iframe, "\"", ($c_height_start+8));
      $c_height = substr($c_iframe, ($c_height_start+8), ($c_height_end-($c_height_start+8)));
      $n_width = 480;
      $n_height = (int) ($n_width * ($c_height / $c_width));
      $n_iframe = str_replace($c_height, $n_height, $c_iframe);
      $n_iframe = str_replace($c_width, $n_width, $n_iframe);
      $text = str_replace($c_iframe, $n_iframe, $text);
    }
    return $text;
  }
  
  # Detecta URLs y los convierte en ligas
  function txt2link($text){
    
    if((stripos($text, "<a href=") !== false) OR (stripos($text, "<iframe ") !== false))
      return $text;
    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
    preg_match_all($reg_exUrl, $text, $matches);
    $usedPatterns = array();
    foreach($matches[0] as $pattern){
      if(!array_key_exists($pattern, $usedPatterns)){
        $usedPatterns[$pattern] = true;
        $text = str_replace($pattern, "<a href='$pattern' target='_blank'>$pattern</a> ", $text);
      }
    }
    return $text;
  }
  
  # Llamado a las funciones para procesar posts y comentarios
  function PorcesaCadena($p_cadena) {
    
    $p_cadena = str_uso_normal($p_cadena);
    $p_cadena = CompletaLigas($p_cadena);
    $p_cadena = iframe_youtube($p_cadena);
    $p_cadena = iframe_vimeo($p_cadena);
    $p_cadena = SeparaFrames($p_cadena);
    $p_cadena = txt2link($p_cadena);
    $p_cadena = str_html_bd($p_cadena);
    
    return $p_cadena;
  }
  
?>