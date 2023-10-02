<?php

function response($StatusCode, $Message) {
	$response['Message'] = $Message;
	$response['StatusCode'] = $StatusCode;
	$json_response = json_encode($response);
	echo $json_response;
}
function authenticate($Token){
    $isAuth = 0;
    if(isset($Token)){
        $Username=openssl_decrypt($Token,"AES-128-ECB","quiz");
        $TokenNew = openssl_encrypt($Username,"AES-128-ECB","quiz");
        if($Token==$TokenNew){
            $isAuth = 1;
        }
    }
    return $isAuth;
}

function listToJson($rows) {
	$json_response = json_encode($rows);
	echo $json_response;
}

?>