<?php

require_once '../../zoom_config.php';
  
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

    //fata saber que licencia ocupara.
    $host_id_email_zoom=1;
    
    if($db->is_table_empty($host_id_email_zoom)) {
        $db->update_access_token(json_encode($token),$host_id_email_zoom);
        echo "Access token inserted successfully.";
    }
} catch(Exception $e) {
    echo $e->getMessage();
}


?>