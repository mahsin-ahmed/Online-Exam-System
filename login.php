<?php
header("Content-Type:application/json");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //$name = $_POST['fname']; // FormData
    $data = json_decode(file_get_contents('php://input'), true); 
    //print_r(file_get_contents('php://input')); // json
    //echo $data["FullName"];
    // $string_to_encrypt=$data["Username"];
    // $password=$data["Userpass"];
    // $encrypted_string=openssl_encrypt($string_to_encrypt,"AES-128-ECB",$password);
    // $decrypted_string=openssl_decrypt($encrypted_string,"AES-128-ECB",$password);
    // echo "<br/>Encrypted: ".$encrypted_string;
    // echo "<br/>Decrypted: ".$decrypted_string;
    include('db.php');
    $result = mysqli_query($con,"SELECT UserId, Username, FullName FROM `users` WHERE IsActive=1 AND Username='".$data["Username"]."' AND Userpass='".$data["Userpass"]."'");
    if(mysqli_num_rows($result)>0) {
        $row = mysqli_fetch_array($result);
        $Token=openssl_encrypt($row['Username'],"AES-128-ECB","quiz");
        objectToJson($row['UserId'], $row['Username'], $row['FullName'],$Token);
        mysqli_close($con);
    } else {
        response(0,"Invalid Request");
    }
} else {
    response(0,"Invalid Request");
}

function response($StatusCode, $Message) {
	$response['Message'] = $Message;
	$response['StatusCode'] = $StatusCode;
	$json_response = json_encode($response);
	echo $json_response;
}

function objectToJson($UserId,$Username,$FullName,$Token) {
	$response['UserId'] = $UserId;
	$response['FullName'] = $FullName;
	$response['Username'] = $Username;
	$response['Token'] = $Token;
	$json_response = json_encode($response);
	echo $json_response;
}


?>