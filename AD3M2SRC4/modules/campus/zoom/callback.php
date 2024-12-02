<?php

require_once 'config.php';
  
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
    $email=$licencia;
 
    if($db->is_table_empty($email)) {
        $db->update_access_token(json_encode($token),$email);
        echo "Access token inserted successfully.";
		echo json_encode($token);
		echo "<br>";
		echo "client id:".CLIENT_ID;
		echo "<br>secret id:".CLIENT_SECRET;
		echo "<br>data:".json_decode($response->getBody());
    }else{
		$db->update_access_token(json_encode($token),$email);
		echo "Access token update successfully.<br>";
		echo json_encode($token);
		echo "<br>";
		echo "client id:".CLIENT_ID;
		echo "<br>secret id:".CLIENT_SECRET;
		echo "<br>data:".json_decode($response->getBody());
	}
} catch(Exception $e) {
	echo "entro";
    echo $e->getMessage();
}


?>