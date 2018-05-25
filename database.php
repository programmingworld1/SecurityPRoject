<?php
$server = 'localhost';
$username = 'root';
$password = '';
$database = 'securityproject';

if(isset($_SESSION['difficulty']) && $_SESSION['difficulty'] == 'low' || $_SESSION['difficulty'] == 'medium'){
	$connection = mysqli_connect($server, $username, $password, $database);
	if (!$connection)
	{
		die('Could not connect: ' . mysql_error());
	}

} else if(isset($_SESSION['difficulty']) && $_SESSION['difficulty'] == 'high'){
	try{
		$conn = new PDO("mysql:host=$server;dbname=$database;", $username, $password);
	} catch(PDOException $e){
		die( "Connection failed: " . $e->getMessage());
	}
} else{
	echo 'Database.php: Wrong settings specified in settings file' . '<br>';
}
