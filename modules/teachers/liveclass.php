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
  $fl_maestro = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
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
  Presenta_Desktop($path, $file, $tipo, "teacher");
  PresentaHerramientas();
  
  #Presenta columna derecha que es exclusiva para el live session 
  echo "
                  </table>
                </td>
              </tr>
            </table>
          </td>
          <td width='182' class='right_colum'>
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
