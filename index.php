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
	$message = '';
	if(!empty($_POST['email']) && !empty($_POST['password'])){
		if($_SESSION['difficulty'] == 'low'){
			//Check username and password from database
			$sql='SELECT id,email,password FROM users WHERE email="'.$_POST['email'].'" && password="'.$_POST['password'].'"';
			$get_user = mysqli_query($connection, $sql);
			$results = mysqli_fetch_array($get_user,MYSQLI_ASSOC);

			if($results){
				$_SESSION['user_id'] = $results['id'];
                $sql = 'SELECT admin FROM users WHERE id ="'.$_SESSION['user_id'].'"';
                $get_user = mysqli_query($connection, $sql);
                $get_admin = mysqli_fetch_array($get_user,MYSQLI_ASSOC)['admin'];
                $admin = 'false';
                if($get_admin){
                    $admin = 'true';
                }
				header("Location: overview?admin=".$admin);
			} else {
				$message = 'Sorry, those credentials do not match';
			}
		}elseif($_SESSION['difficulty'] == 'medium'){
            preg_match_all("/([A-z0-9@._]+)/", $_POST['email'], $out, 0); // Search the input for all characters between [] and store them in $out
            $email = $out[0][0]; // Access the first element (2D array)
            preg_match_all("/([A-z0-9@._!@#$%^*()]+)/", $_POST['password'], $out, 0); // Search the input for all characters between [] and store them in $out
            $password = $out[0][0]; // Access the first element (2D array)
			
            $sql  = 'SELECT id,email,password FROM users WHERE email ="'.$email.'" && password="'.$password.'"';
            $get_user = mysqli_query($connection, $sql);
            $results = mysqli_fetch_array($get_user,MYSQLI_ASSOC);

            if($results){
                $_SESSION['user_id'] = $results['id'];
                header("Location: overview");
            } else {
                $message = 'Sorry, those credentials do not match';
            }
		}else{
			$get_user = $conn->prepare('SELECT id,email,password FROM users WHERE email = :email');
			$get_user->bindParam(':email', $_POST['email']);
			$get_user->execute();
			$results = $get_user->fetch(PDO::FETCH_ASSOC);

			if($results > 0 && password_verify($_POST['password'], $results['password']) ){
				$_SESSION['user_id'] = $results['id'];
				header("Location: overview");
			}else{
				$message = 'Sorry, those credentials do not match';
			}
		}
	}else{
		$message = 'Fill in all fields to log in';
	}
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Login</title>
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
            (Click register here to create account) OTHERWISE NO ACCESS!!!</b>
		</div>

		<?php if(!empty($message)){ ?>
			<p><?php echo $message ?></p>
		<?php } ?>

		<h1>Login</h1>
		<span>or <a href="register">register here</a></span>

		<form action="#" method="POST">
			<input type="text" placeholder="Enter your email" name="email">
			<input type="password" placeholder="and password" name="password">

			<input name="submit" type="submit" value="Log in">
		</form>
	</body>
</html>