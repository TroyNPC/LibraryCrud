<?php
	include 'Functions/Function_Create_Account.php';
	$servername = "localhost";
	$username_db = "root";   
	$password_db = "";       
	$dbname = "booksdb";      

	$conn = mysqli_connect($servername, $username_db, $password_db, $dbname);
	if (!$conn) {
	    die("Connection failed: " . mysqli_connect_error());
	}

	if (isset($_POST['Login'])) {
	               header("Location: login.php"); 
	            exit();
	}
	?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link href="MainCss/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="MainJs/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="Styles.css">
</head>
<body style="background-image: url('pics/Background_image.jpg')">
    <div class="container">
        <div class="d-flex justify-content-center h-10">
            <div class="card">
                <div class="card-header">
                    <h3>Create Account</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)) : ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error) : ?>
                                <p><?php echo $error; ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter Username" value="<?php echo isset($username) ? $username : ''; ?>" required>
                        </div>
                        <div class="input-group form-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
                        </div>
                        <div class="input-group form-group">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                        </div>
                    <div class = "inline" style = "display:flex;">
                        <div class="form-group">
                            <input type="submit" name = 'Register' value="Register" class= "btn btn-warning">
                        </div>
                    </form>
                    <br>
                    <form method = "POST">
                      <div class="form-group" style = "">
                           <input type="submit" name = 'Login' value="Login" class="btn float-right login_btn" style = "margin-left: 180%;">
                    </div>
                </form>
                	</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
