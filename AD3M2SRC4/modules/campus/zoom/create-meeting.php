<?php
require_once 'config.php';
 
function create_meeting($duration,$topic,$date,$pass) {
    $client = new GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);
 
    $db = new DB();
    $arr_token = $db->get_access_token();
    $accessToken = $arr_token->access_token;
 
    try {
        $response = $client->request('POST', '/v2/users/me/meetings', [
            "headers" => [
                "Authorization" => "Bearer $accessToken"
            ],
            'json' => [
                "topic" => $topic,
                "type" => 2,
                "start_time" => $date,
                "duration" => $duration, // 30 mins
                "password" => $pass
            ],
        ]);
 
        $data = json_decode($response->getBody());
        echo "Join URL: ". $data->join_url;
        echo "<br>";
        echo "Meeting Password: ". $data->password;
		echo "Join ID: ". $data->id;
		echo "Host ID: ". $data->host_id;
		//echo "==============================";
		//echo $data;
		//echo "================================";
 
    } catch(Exception $e) {
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
 

/*
$time=5;


for ($i = 1; $i <= 10; $i++) {    



    $duration=30;
    $time =$time+5;
    $topic="FAME TEST-".$i;
    $date="2020-06-18T16:$time:00";
    $pass="fame_".$i;


    create_meeting($duration,$topic,$date,$pass);
}
*/





?>