<?php
header("Content-Type:application/json");
$headers = apache_request_headers();
include('auth.php');
$isAuthenticate = authenticate($headers['Authorization']);
if($isAuthenticate != 1){
	response(0,"Authentication failed.");
} else if(isset($_GET['action']) && $_GET['action']!="" && $_GET['action']=="getall") {
	include('db.php');
	$sql = "SELECT Id,Name FROM quizcategory";
	if ($result = mysqli_query($con, $sql)) {
		// Fetch one and one row
		$i = 0;
		$listQuizCategory = array();
		while ($row = mysqli_fetch_row($result)) {
			$oQuizCategory['Id'] = $row[0];
			$oQuizCategory['Name'] = $row[1];
			$listQuizCategory[] = $oQuizCategory;
			$i++;
		}
		listToJson($listQuizCategory);
		mysqli_free_result($result);
	}
	mysqli_close($con);
} else if(isset($_GET['action']) && $_GET['action']!="" && $_GET['action']=="getbyid") {
	if (isset($_GET['id']) && $_GET['id']!="") {
		include('db.php');
		$id = $_GET['id'];
		$sql = "SELECT Id,Name FROM quizcategory WHERE Id=$id";
		$result = mysqli_query($con, $sql);
		if(mysqli_num_rows($result)>0) {
			$row = mysqli_fetch_array($result);
			$Id = $row['Id'];
			$Name = $row['Name'];
			objectToJson($UserId, $FullName, $Username,$IsActive,$Phone,$Email);
			mysqli_close($con);
		} else {
			response(0,"No Record Found.");
		}
	} else {
		response(0,"Method not found.");
	}
} else if(isset($_GET['action']) && $_GET['action']!="" && $_GET['action']=="add"){
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$data = json_decode(file_get_contents('php://input'), true); 
		include('db.php');
		$sql = "INSERT INTO quizcategory (Name)
				VALUES ('".$data["Name"]."')";
		if (mysqli_query($con, $sql)) {
			response(1, "Record added successfully.");
		} else {
			response(0, mysqli_error($con));
		}
	} else {
		response(0,"Method not found.");
	}
} else if(isset($_GET['action']) && $_GET['action']!="" && $_GET['action']=="update"){
	if ($_SERVER["REQUEST_METHOD"] == "PATCH") {
		$data = json_decode(file_get_contents('php://input'), true);
		include('db.php');
		$sql = "UPDATE quizcategory SET Name='".$data["Name"]."' WHERE Id=".$data["Id"];
		if (mysqli_query($con, $sql)) {
			response(1, "Record updated successfully.");
		} else {
			response(0, mysqli_error($con));
		}
	} else {
		response(0,"Method not found.");
	}
} else if(isset($_GET['action']) && $_GET['action']!="" && $_GET['action']=="remove"){
	if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
		$id = $_GET['id'];
		include('db.php');
		$sql = "DELETE FROM quizcategory WHERE Id=".$id;
		if (mysqli_query($con, $sql)) {
			response(1, "Record removed successfully.");
		} else {
			response(0, mysqli_error($con));
		}
	} else {
		response(0,"Method not found.");
	}
} else {
	response(0,"Action not found.");
}

function objectToJson($Id,$Name) {
	$response['Id'] = $Id;
	$response['Name'] = $Name;
	$json_response = json_encode($response);
	echo $json_response;
}

?>