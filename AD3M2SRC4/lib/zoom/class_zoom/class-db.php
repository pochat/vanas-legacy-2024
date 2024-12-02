<?php


$db_host=ObtenConfiguracion(10);


class DB {
    private $dbHost     = "".DATABASE_SERVER."";
    private $dbUsername = "".DATABASE_USER."";
    private $dbPassword = "".DATABASE_PWD."";
    private $dbName     = "".DATABASE_NAME."";
 
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
    
    public function is_table_empty($host_id_email_zoom) {
        $result = $this->db->query("SELECT id FROM zoom WHERE id=$host_id_email_zoom ");
        if($result->num_rows) {
            return false;
        }
        
        return true;
    }
    
    public function get_access_token($host_id_email_zoom) {

        $sql = $this->db->query("SELECT access_token FROM zoom WHERE id=$host_id_email_zoom  ");
        $result = $sql->fetch_assoc();
        return json_decode($result['access_token']);
    }
    
    public function get_refersh_token($host_id_email_zoom) {

        $this->db->query("UPDATE zoom SET fe_ultmod =CURRENT_TIMESTAMP WHERE id=$host_id_email_zoom  ");
        
        $result = $this->get_access_token($host_id_email_zoom);
        return $result->refresh_token;
    }
    
    public function update_access_token($token,$host_id_email_zoom) {
       

        if($this->is_table_empty($host_id_email_zoom)) {
            $this->db->query("INSERT INTO zoom(id,access_token,host_email_zoom,client_id_zoom,client_secret_zoom,fg_activo) VALUES($host_id_email_zoom,'$token','$email','$client_id_zoom','$client_secret_zoom','1')");
        } else {

            #Recuperamos datos adicionales del token que existe en BD
            $Query="SELECT host_email_zoom,client_id_zoom,client_secret_zoom FROM zoom WHERE id=$host_id_email_zoom  ";
            $row=RecuperaValor($Query);
            $email=$row['host_email_zoom'];
            $client_id_zoom=$row['client_id_zoom'];
            $client_secret_zoom=$row['client_secret_zoom'];

            $this->db->query("UPDATE zoom SET access_token = '$token',fe_ultmod =CURRENT_TIMESTAMP ,host_email_zoom='$email',client_id_zoom='$client_id_zoom',client_secret_zoom='$client_secret_zoom',fg_activo='1' WHERE id=$host_id_email_zoom  ");
        }



    }
}

?>