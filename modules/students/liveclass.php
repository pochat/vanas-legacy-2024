<?php 

if (!$_POST["username"]||$_POST["username"]=="Guest") $username="Guest".rand(1000,9999);
else $username=$_POST["username"];

$username=preg_replace("/[^0-9a-zA-Z_]/","-",$username);
$usertype=$_POST["usertype"];
$userroom=$_POST["room"];
$userroom=preg_replace("/[^0-9a-zA-Z\s_]/","-",$userroom);

$username="Teacher";
$usertype="M";
$userroom="VANAS";

setcookie("username",urlencode($username),time()+72000);
setcookie("usertype",urlencode($usertype),time()+72000);
if ($userroom) setcookie("userroom",urlencode($userroom),time()+72000);

 
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
 
  #Código necesario para el Chat
  require_once dirname(__FILE__)."/../common/chat/src/phpfreechat.class.php";
  $params["serverid"] = md5(__FILE__); // calculate a unique id for this chat
  $chat = new phpFreeChat($params);
  
  $path = "";
  $file = "";
  $tipo = "live";
  $titulo = "Live Classroom";
  
  PresentaHeader($titulo);
  Presenta_Desktop($path, $file, $tipo, "student");
  
  # Presenta chat
  echo "
                    <tr>
                      <td></td>
                      <td>";
  //$chat->printChat();
  echo "
                      </td>
                      <td></td>
                    </tr>
                    <tr>
                      <td colspan='3' height='5'></td>
                    </tr>";

  #Presenta columna derecha que es exclusiva para el live session 
  echo "
                  </table>
                </td>
              </tr>
            </table>
          </td>
          <td width='182' class='right_colum'>
            <table border='0' cellpadding='0' cellspacing='0' align='center'>
              <tr>
                <td height='415' valign='top'>
                  <table border='0' cellpadding='0' cellspacing='0' align='center'>
                    <tr>
                      <td width='150' height='25' class='right_column_labels'>Fecha/Hora</td>
                    </tr>
                    <tr>
                      <td height='25'></td>
                    </tr>
                    <tr>
                      <td height='120' class='video_sketch'>
                        Teacher's Webcam
                      </td>
                    </tr>
                    <tr>
                      <td class='right_column_labels'>Teacher</td>
                    </tr>
                    <tr>
                      <td height='20'></td>
                    </tr>
                    <tr>
                      <td height='120' class='video_sketch'>
                        Student's Webcam
                      </td>
                    </tr>
                    <tr>
                      <td class='right_column_labels'>Student</td>
                    </tr>                    
                  </table>
                </td>
              </tr>
              <tr>
                <td width='182' class='division_line'>
                  <table border='0' cellpadding='0' cellspacing='0' align='center'>
                    <tr>
                      <td class='right_column_labels'>Attendees:</td>
                    </tr>
                    <tr>
                      <td height='10'></td>
                    </tr>
                    <tr>
                      <td class='right_column_labels'>Fulano<br>Sutano<br>Mengano<br>Perengano</td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                    </tr>
                  </table
                </td>
              </tr>
            </table>
          </td>	
        </tr>";
  echo "
        <tr>
          <td colspan='3' height='20' class='footer'>Footer</td>
        </tr>
      </table>
    </body>
  </html>";
  
?>