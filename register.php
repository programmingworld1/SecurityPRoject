<?php
session_start();
if (isset($_POST['difficulty'])) {
	$_SESSION['difficulty'] = $_POST['difficulty'];
} else if(empty($_SESSION['difficulty'])) {
	$_SESSION['difficulty'] = 'low';
}
if( isset($_SESSION['user_id']) ){
	header("Location: overview");
}
require 'database.php';

if (isset($_POST['submit'])){
	if(!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['confirm_password'])) {
		$message = '';

		if(!checkEmail($_POST['email'])){
			$message = "Incorrect email";
		} elseif(strlen($_POST['password']) < 6) {
			$message = "Your password must be at least 6 characters";
		} elseif(strlen($_POST['username']) < 4) {
			$message = "Your username must be at least 4 characters";
		} elseif($_POST['password'] != $_POST['confirm_password']) {
			$message = "Your passwords don't match";
		} else {
            try{
                $conn = new PDO("mysql:host=$server;dbname=$database;", $username, $password);
            } catch(PDOException $e){
                die( "Connection failed: " . $e->getMessage());
            }
			// Enter the new user in the database
			$sql = "INSERT INTO users (email, password, username) VALUES (:email, :password, :username)";
			$stmt = $conn->prepare($sql);

			$stmt->bindParam(':email', $_POST['email']);
			$pass = ($_SESSION['difficulty'] == 'high') ? password_hash($_POST['password'], PASSWORD_BCRYPT) : $_POST['password'];
			$stmt->bindParam(':password', $pass);
			$stmt->bindParam(':username', $_POST['username']);

			if ($stmt->execute()) {
				$message = 'Successfully created new user';
			} else {
				$message = 'There was an issue creating your account. Please try again later';
			}
		}
	} else {
		$message = "Fill in all fields to register";
	}
}

function checkEmail($email) {
    if ( strpos($email, '@') !== false ) {
        $split = explode('@', $email);
        return (strpos($split['1'], '.') !== false ? true : false);
    }
    else {
        return false;
    }
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Register</title>
		<link rel="stylesheet" type="text/css" href="assets/css/style.css">
		<link href='http://fonts.googleapis.com/css?family=Comfortaa' rel='stylesheet' type='text/css'>
	</head>
	<body>
		<form action="#" method="POST">
			Security Level:&nbsp
			<?php if($_SESSION['difficulty'] == 'low'){ ?>
				<input name="difficulty" type="submit" value="low" style="width: 100px; background-color: red">
			<?php } else{ ?>
				<input name="difficulty" type="submit" value="low" style="width: 100px; background-color: gray">
			<?php }?>

			<?php if($_SESSION['difficulty'] == 'medium'){ ?>
				<input name="difficulty" type="submit" value="medium" style="width: 100px; background-color: orange">
			<?php } else{ ?>
				<input name="difficulty" type="submit" value="medium" style="width: 100px; background-color: gray">
			<?php }?>

			<?php if($_SESSION['difficulty'] == 'high'){ ?>
				<input name="difficulty" type="submit" value="high" style="width: 100px; background-color: green">
			<?php } else{ ?>
				<input name="difficulty" type="submit" value="high" style="width: 100px; background-color: gray">
			<?php }?>
		</form>

		<div class="header">
			<b>SUPER SECRET INFORMATION NOT TO BE SEEN BY ANYONE WITHOUT ACCOUNT!<br>
			FILL IN THE FORM BELOW TO GAIN ACCESS TO OUR INFORMATION</b>
		</div>

		<?php if(!empty($message)){ ?>
			<p><?php echo $message ?></p>
		<?php } ?>

		<h1>Register</h1>
		<span>or <a href="index">login here</a></span>

		<form action="#" method="POST">
			
			<input type="text" placeholder="Enter your email" name="email">
			<input type="text" placeholder="Enter your username" name="username">
			<input type="password" placeholder="and password" name="password">
			<input type="password" placeholder="confirm password" name="confirm_password">
			<input name="submit" type="submit" value="Register">

		</form>
	</body>
</html>
