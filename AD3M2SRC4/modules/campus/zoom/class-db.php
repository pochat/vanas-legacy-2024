<?php

class DB {
    private $dbHost     = "localhost";
    private $dbUsername = "vanas";
    private $dbPassword = "QDYTEw9tdx54h29";
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
  
    public function is_table_empty($host_id_email_zoom) {
        $result = $this->db->query("SELECT id FROM zoom WHERE id=$host_id_email_zoom ");
        if($result->num_rows) {
            return false;
        }
  
        return true;
    }
  
    public function get_access_token($host_id_email_zoom) {
        $sql = $this->db->query("SELECT access_token FROM zoom WHERE id=$host_id_email_zoom ");
        $result = $sql->fetch_assoc();
        return json_decode($result['access_token']);
    }
  
    public function get_refersh_token($host_id_email_zoom) {
        $result = $this->get_access_token($email);
        return $result->refresh_token;
    }
  
    public function update_access_token($token,$host_id_email_zoom) {
		
		if($host_id_email_zoom==1){
			$email="mario@vanas.ca";
			$client_id_zoom="tHTALDdtTbaU1r8unxMRQ";
			$client_secret_zoom="XfxS4Ic6AKQXhAG87Z284hbcQD79xGLB";
		}
		if($host_id_email_zoom==2){
			$email="info@vanas.ca";
			$client_id_zoom="kLNMzM_GTJy5zVZyqiikcw";
			$client_secret_zoom="4VnxHqRD67Bz1428yt60MtWKB8u9RUq1";
		}
		if($host_id_email_zoom==3){
			$email="admin@vanas.ca";
			$client_id_zoom="kGsV4_VYS8KxJGTS5monhA";
			$client_secret_zoom="oR3PjhE0P65b4GQJIYuYmv5H0csf6jVH";
		}
		if($host_id_email_zoom==4){
			$email="class01@vanas.ca";
			$client_id_zoom="1kygpjUXSKW8DdHPIWUSrA";
			$client_secret_zoom="c18rZV56dmsQ9WDp565dZouHA7sYS6YL";
		}
		
		
		
		
		
		
        if($this->is_table_empty($host_id_email_zoom)) {
            $this->db->query("INSERT INTO zoom(id,access_token,host_email_zoom,client_id_zoom,client_secret_zoom,fg_activo) VALUES($host_id_email_zoom,'$token','$email','$client_id_zoom','$client_secret_zoom','1')");
        } else {
            $this->db->query("UPDATE zoom SET   access_token = '$token',fe_ultmod =CURRENT_TIMESTAMP ,host_email_zoom='$email',client_id_zoom='$client_id_zoom',client_secret_zoom='$client_secret_zoom',fg_activo='1' WHERE id=$host_id_email_zoom  ");
        }
    }
}

?>