<?php
session_start();
if (isset($_POST['difficulty'])) {
	$_SESSION['difficulty'] = $_POST['difficulty'];
} else if(empty($_SESSION['difficulty'])) {
	$_SESSION['difficulty'] = 'low';
}
$admin = false;
require 'database.php';
if($_SESSION['difficulty'] == 'low' || $_SESSION['difficulty'] == 'medium' && isset($_SESSION['user_id'])){
	$get_message = mysqli_query($connection,"SELECT id,title,message FROM messages");
	$message = mysqli_fetch_array($get_message,MYSQLI_ASSOC);
	if ($_SESSION['difficulty'] == 'low' && isset($_GET['admin']) && $_GET['admin'] == 'true')
	{
		$admin = true;
	}
	
	if(isset($_SESSION['user_id']) && $_SESSION['difficulty'] == 'low'
	|| $_SESSION['difficulty'] == 'medium'){
		$sql = 'SELECT id,email,username,admin FROM users WHERE id ="'.$_SESSION['user_id'].'"';
		$get_user = mysqli_query($connection, $sql);
		$user = mysqli_fetch_array($get_user,MYSQLI_ASSOC);
		
		if($user['admin']){
			$admin = true;
		}
	}else{
		$user = null;
	}
}elseif($_SESSION['difficulty'] == 'high' && isset($_SESSION['user_id'])){
	$get_user = $conn->prepare('SELECT id,email,username,admin FROM users WHERE id = :id');
    $get_user->bindParam(':id', $_SESSION['user_id']);
    $get_user->execute();
    $user = $get_user->fetch(PDO::FETCH_ASSOC);
	
	if($user['admin']){
        $admin = true;
    }
	
	$get_message = $conn->prepare('SELECT id,title,message FROM messages');
	$get_message->execute();
}else{
	header("Location: index");
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Overview</title>
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
			<b>YOU HAVE GAINED ACCESS TO THE SUPER SECRET INFORMATION!</b>
		</div>
		<br>
		<b>Welcome <a href="profile/<?php echo $user['username']; ?>"><?php echo $user['username']; ?></a>, You are succesfully logged in!</b>
		<br><br>
		<a href='logout.php'>Logout?</a>
		<br><br>

		<?php foreach($get_message as $row)
		{
			echo '<table border="1" align="center">
			<tr>
			<th>'.$row['title'].'</th>
			</tr>
			<tr><td>
			'.$row['message'].'
			</td></tr></table><br>';
		}
		if ($admin){
            echo '<table border="1" align="center">
			<tr>
			<th>ADMIN ONLY SECRET INFORMATION</th>
			</tr>
			<tr><td>
			ADMINS ARE AWESOME! YOU LOOK GREAT! (Dont let anyone know)
			</td></tr></table><br>';
        }

		?>
	</body>
</html>