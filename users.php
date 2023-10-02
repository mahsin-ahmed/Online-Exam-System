<?php
header("Content-Type:application/json");
$headers = apache_request_headers();
include('auth.php');
$isAuthenticate = authenticate($headers['Authorization']);
if(isset($_GET['action']) && $_GET['action']!="" && $_GET['action']=="getall" && $isAuthenticate == 1) {
	include('db.php');
	$sql = "SELECT Email,FullName,IsActive, Phone, UserId, Username FROM users";
	if ($result = mysqli_query($con, $sql)) {
		// Fetch one and one row
		$i = 0;
		$listUser = array();
		while ($row = mysqli_fetch_row($result)) {
			$oUser['Email'] = $row[0];
			$oUser['FullName'] = $row[1];
			$oUser['IsActive'] = $row[2];
			$oUser['Phone'] = $row[3];
			$oUser['UserId'] = $row[4];
			$oUser['Username'] = $row[5];
			$listUser[] = $oUser;
			$i++;
		}
		listToJson($listUser);
		mysqli_free_result($result);
	}
	mysqli_close($con);
} else if(isset($_GET['action']) && $_GET['action']!="" && $_GET['action']=="getbyid" && $isAuthenticate == 1) {
	if (isset($_GET['id']) && $_GET['id']!="") {
		include('db.php');
		$id = $_GET['id'];
		$sql = "SELECT Email,FullName,IsActive, Phone, UserId, Username FROM `users` WHERE UserId=$id";
		$result = mysqli_query($con, $sql);
		if(mysqli_num_rows($result)>0) {
			$row = mysqli_fetch_array($result);
			$UserId = $row['UserId'];
			$FullName = $row['FullName'];
			$Username = $row['Username'];
			$IsActive = $row['IsActive'];
			$Phone = $row['Phone'];
			$Email = $row['Email'];
			objectToJson($UserId, $FullName, $Username,$IsActive,$Phone,$Email);
			mysqli_close($con);
		} else {
			response(0,"No Record Found");
		}
	} else {
		response(0,"Invalid Request");
	}
} else if(isset($_GET['action']) && $_GET['action']!="" && $_GET['action']=="add" && $isAuthenticate == 1){
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		//$name = $_POST['fname']; // FormData
		$data = json_decode(file_get_contents('php://input'), true); 
		//print_r(file_get_contents('php://input')); // json
		include('db.php');
		$sql = "INSERT INTO users (Username, Userpass, Email, Phone, FullName)
				VALUES ('".$data["Username"]."', '".$data["Userpass"]."', '".$data["Email"]."', '".$data["Phone"]."', '".$data["FullName"]."')";
		if (mysqli_query($con, $sql)) {
			response(1, "User added successfully.");
		} else {
			response(0, mysqli_error($con));
		}
	} else {
		response(0,"Invalid Request");
	}
} else if(isset($_GET['action']) && $_GET['action'] !="" && $_GET['action']=="resetpass" && $isAuthenticate == 1){
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$data = json_decode(file_get_contents('php://input'), true);
		if($data["OldPass"] == $data["NewPass"]){
			include('db.php');
			$sql = "UPDATE users SET Userpass='".$data["Userpass"]."' WHERE UserId=".$response['UserId']."";
			if (mysqli_query($con, $sql)) {
				response(1, "User-Pass reset successfully.");
			} else {
				response(0, mysqli_error($con));
			}
		} else {
			response(0,"Old and New User-Pass is not matched.");	
		} 
	} else {
		response(0,"Invalid Request.");
	}
} else {
	response(0,"Invalid Request.");
}

function objectToJson($UserId,$FullName,$Username,$IsActive,$Phone,$Email) {
	$response['UserId'] = $UserId;
	$response['FullName'] = $FullName;
	$response['Username'] = $Username;
	$response['IsActive'] = $IsActive;
	$response['Phone'] = $Phone;
	$response['Email'] = $Email;
	$json_response = json_encode($response);
	echo $json_response;
}

?>