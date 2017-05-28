<?php 
$authorization_code = file_get_contents('constants/authorization_code');
$json = file_get_contents('constants/client_secret.json');
$array = json_decode($json);

$client_id = $array->installed->client_id;
$client_secret = $array->installed->client_secret;
$redirect_uri = $array->installed->redirect_uris[0];

$authorization_code = file_get_contents('constants/authorization_code');
echo $str = "curl --data \"code=$authorization_code\" --data \"client_id=$client_id\" --data \"client_secret=$client_secret\" --data \"redirect_uri=$redirect_uri\" --data \"grant_type=authorization_code\" --data \"access_type=offline\" https://www.googleapis.com/oauth2/v4/token";
echo shell_exec($str);
