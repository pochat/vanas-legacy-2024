<?php 
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  require_once '../../../vendor/autoload.php';
  
  
  define('CLIENT_ID', 'NIPQsyxmTX2vz3UQioU_g');
  define('CLIENT_SECRET', 'FfRCRFv94w7U4QDf4Hnpf3bKV6Xv9tXt');
  define('REDIRECT_URI', 'https://dev.vanas.ca/AD3M2SRC4/modules/campus/test_email.php');

  
  /*$from_add = ObtenConfiguracion(4);
  $ds_email="mike@vanas.ca";
  $subject="Subject";
  $message="test aplications send";
  $bcc="terrylonz@gmail.com";
  
  echo $mail_apply = EnviaMailHTML('', $from_add, $ds_email, $subject, $message, $bcc);
  */


  /***************************************************/
  class DB {
      private $dbHost     = "localhost";
      private $dbUsername = "dev";
      private $dbPassword = "D#v3L0p3rr";
      private $dbName     = "vanas_prod";
      
      public function __construct(){
          if(!isset($this->db)){
              // Connect to the database
              $conn = new mysqli($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName);
              if($conn->connect_error){
                  die("Failed to connect with MySQL: " . $conn->connect_error);
              }else{
                  $this->db = $conn;
              }
          }
      }
      
      public function is_table_empty() {
          $result = $this->db->query("SELECT id FROM token");
          if($result->num_rows) {
              return false;
          }
          
          return true;
      }
      
      public function get_access_token() {
          $sql = $this->db->query("SELECT access_token FROM token");
          $result = $sql->fetch_assoc();
          return json_decode($result['access_token']);
      }
      
      public function get_refersh_token() {
          $result = $this->get_access_token();
          return $result->refresh_token;
      }
      
      public function update_access_token($token) {
          if($this->is_table_empty()) {
              $this->db->query("INSERT INTO token(access_token) VALUES('$token')");
          } else {
              $this->db->query("UPDATE token SET access_token = '$token' WHERE id = (SELECT id FROM token)");
          }
      }
  }

  function create_meeting() {
      $client = new GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);
      
      $db = new DB();
      $arr_token = $db->get_access_token();
      $accessToken = $arr_token->access_token;
      echo"llego";
      try {
          $response = $client->request('POST', '/v2/users/me/meetings', [
              "headers" => [
                  "Authorization" => "Bearer $accessToken"
              ],
              'json' => [
                  "topic" => "Let's learn php",
                  "type" => 2,
                  "start_time" => "2020-05-05T20:30:00",
                  "duration" => "30", // 30 mins
                  "password" => "123456"
              ],
          ]);
          
          $data = json_decode($response->getBody());
          echo "Join URL: ". $data->join_url;
          echo "<br>";
          echo "Meeting Password: ". $data->password;
          
      }
      catch(Exception $e) {
          if( 401 == $e->getCode() ) {
              $refresh_token = $db->get_refersh_token();
              
              $client = new GuzzleHttp\Client(['base_uri' => 'https://zoom.us']);
              $response = $client->request('POST', '/oauth/token', [
                  "headers" => [
                      "Authorization" => "Basic ". base64_encode(CLIENT_ID.':'.CLIENT_SECRET)
                  ],
                  'form_params' => [
                      "grant_type" => "refresh_token",
                      "refresh_token" => $refresh_token
                  ],
              ]);
              $db->update_access_token($response->getBody());
              
              create_meeting();
          } else {
              echo $e->getMessage();
          }
      }
  }
  
  create_meeting();












  try {
      $client = new GuzzleHttp\Client(['base_uri' => 'https://zoom.us']);
      
      $response = $client->request('POST', '/oauth/token', [
          "headers" => [
              "Authorization" => "Basic ". base64_encode(CLIENT_ID.':'.CLIENT_SECRET)
          ],
          'form_params' => [
              "grant_type" => "authorization_code",
              "code" => $_GET['code'],
              "redirect_uri" => REDIRECT_URI
          ],
      ]);
      
      $token = json_decode($response->getBody()->getContents(), true);
      
      $db = new DB();
      
      if($db->is_table_empty()) {
          $db->update_access_token(json_encode($token));
          echo "Access token inserted successfully.";
      }
  }
  catch(Exception $e) {
      echo $e->getMessage();
  }


  $url = "https://zoom.us/oauth/authorize?response_type=code&client_id=".CLIENT_ID."&redirect_uri=".REDIRECT_URI;

?> 
 <a href="<?php echo $url; ?>">Login with Zoom</a>
 
	