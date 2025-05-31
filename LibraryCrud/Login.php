
<!DOCTYPE html>
<html>
<head>
	<link href="MainCss/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<script src="MainJs/js/bootstrap.min.js"></script>
	<?php
	session_start();
	?>
	<title>Book_Order_Database</title>
	<link rel="stylesheet" type="text/css" href="Styles.css">
</head>
<body style = "background-image: url('pics/Background_image.jpg')">
<div class="container">
	<div class="d-flex justify-content-center h-10">
		<div class="card">
			<div class="card-header">
				<h3>SIGN IN</h3>
			</div>
			<div class="card-body">
				<form method = "POST">
			  <div class="form-group">
			    <input name = "username" type="text" class="form-control" id="Inputusername" aria-describedby="emailHelp" placeholder="Enter Username">
			  </div>
					<div class="input-group form-group">
						<input name = "password" type="password" class="form-control" id = "Inputpassword" placeholder="Enter Password">
					</div>
						<div class="alert alert-danger" id = "alert" style = "display:none; transition:2s;"role="alert">
					Incorrect Password or Username!
					</div>
					<div class="form-group">
						<input name = "login" type="submit" id = "submit" value="Login" class="btn float-right login_btn">
						<input name = "create" type="submit" id = "submit" value="Register" class="btn float-right login_btn" style = "margin-left: 160px;">
					</div>
				</form>

				<?php
		$mysql = new mysqli('localhost', 'root', '', 'booksdb');

		if (isset($_POST['login'])) {
		    $username = $_POST['username'];
		    $password = $_POST['password'];

		    if ($username === 'admin' && $password === 'admin') {
		        $_SESSION['username'] = $username;
		        $_SESSION['message'] = "Admin Login successful!";
		        header("Location: Main.php");
		        exit();
		    }

		    $stmt = $mysql->prepare('SELECT * FROM users WHERE user_name = ? AND password = ?');
		    $stmt->bind_param('ss', $username, $password);
		    $stmt->execute();
		    $result = $stmt->get_result();

		    if ($result->num_rows > 0) {
		        $_SESSION['username'] = $username; 
		        $_SESSION['message'] = "User Login successful!";
		        header("Location: UserLogin.php");
		        exit();
		    } else {
		        echo "<script>alert('Incorrect Password');</script>";
		    }

		    $stmt->close();
		} else if (isset($_POST['create'])) {
		    header("Location: Create.php");
		    exit();
}
				?>


			</div>
		</div>
	</div>
</div>

</body>
</html>

