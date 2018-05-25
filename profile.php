<?php
session_start();
if (isset($_POST['difficulty'])) {
	$_SESSION['difficulty'] = $_POST['difficulty'];
} else if(empty($_SESSION['difficulty'])) {
	$_SESSION['difficulty'] = 'low';
}
require 'database.php';
$get_user = null;
if (isset($_GET['user'])) {
	$get_user = $_GET['user'];
}

if($_SESSION['difficulty'] == 'low' || $_SESSION['difficulty'] == 'medium'){
	$sql='SELECT id,email,username,message FROM users WHERE username="'.$get_user.'" LIMIT 1';
	$get_user_result = mysqli_query($connection, $sql);
	$user = mysqli_fetch_array($get_user_result,MYSQLI_ASSOC);
	
	if(!$user && empty($get_user) && $_SESSION['user_id']){
		$sql='SELECT id,email,username,message FROM users WHERE id="'.$_SESSION['user_id'].'"';
		$get_user_result = mysqli_query($connection, $sql);
		$user = mysqli_fetch_array($get_user_result,MYSQLI_ASSOC);
	}elseif(!$user){
		header("Location: ../index");
	}
}else{
	$get_user_result = $conn->prepare('SELECT id,email,username,message FROM users WHERE username = :username LIMIT 1');
	$get_user_result->bindParam(':username', $get_user);
	$get_user_result->execute();
	$user = $get_user_result->fetch(PDO::FETCH_ASSOC);
	
	if(!$user && empty($get_user) && $_SESSION['user_id']){
		$get_user_result = $conn->prepare('SELECT id,email,username,message FROM users WHERE id = :id');
		$get_user_result->bindParam(':id', $_SESSION['user_id']);
		$get_user_result->execute();
		$user = $get_user_result->fetch(PDO::FETCH_ASSOC);
	}elseif(!$user){
		header("Location: ../index");
	}
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Profile: <?php echo $user['username'] ?></title>
		<link rel="stylesheet" type="text/css" href="../assets/css/style.css">
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
			<b>YOU HAVE GAINED ACCESS TO THE SUPER SECRET INFORMATION!</b>
		</div>
		
		<br>Welcome on the profile of <?= $user['username']; ?>
		<br><br>Personal message<br>
		<?php if(!empty($user['message'])){
			if($_SESSION['difficulty'] == 'low' || $_SESSION['difficulty'] == 'medium'){
				echo $user['message'];
			}else{
				echo htmlentities($user['message']);
			}
		}else{
			echo ''.$user['username'].' does not have a personal message currently.';
		}
		?>
	</body>
</html>