<?php
session_start();
error_reporting(0);
include('includes/config.php');
$key='qkwjdiw239&&jdafweihbrhnan&^%$ggdnawhd4njshjwuuO';
// Test encyption
function encryptthis($data, $key) {
	$encryption_key = base64_decode($key);
	$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
	$encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
	return base64_encode($encrypted . '::' . $iv);
	}

// // //Test decryption
function decryptthis($data, $key) {
	$encryption_key = base64_decode($key);
	list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($data), 2),2,null);
	return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}

// Code user Registration
if (isset($_POST['submit'])) {
	$name = $_POST['fullname'];
	$name = encryptthis($_POST['fullname'],$key);
	$email = $_POST['emailid'];
	$email = encryptthis($_POST['emailid'],$key);
	$password = md5($_POST['password']);
	$query = mysqli_query($con, "insert into users(name,email,password) values('$name','$email','$password')");
	if ($query) {
		echo "<script>alert('You are successfully register');</script>";
	} else {
		echo "<script>alert('Not register something went worng');</script>";
	}
}


// Code for User login
if (isset($_POST['login'])) {
	$key='qkwjdiw239&&jdafweihbrhnan&^%$ggdnawhd4njshjwuuO';
	$email = $_POST['email'];
	$email = encryptthis($_POST['email'],$key);
	$password = md5($_POST['password']);
	$query = mysqli_query($con, "SELECT * FROM users");
	$num = mysqli_fetch_array($query);
	
	if ($num > 0) {
		$check=false;
		while($row = $query->fetch_assoc()) {
			//var_dump($row["id"]); die(); 
			if (decryptthis($row["email"],$key)==$_POST['email']){
				$check=$row['password']==$password;
				if ($check){
					$extra = "my-cart.php";
					$_SESSION['login'] = $_POST['email'];
					$encrypted = encryptthis($_POST['email'],$key);
					$_SESSION['id'] = $row['id'];
					$_SESSION['username'] = $row['name'];
					$uip = $_SERVER['REMOTE_ADDR'];
					$status = 1;
					$log = mysqli_query($con, "insert into userlog(userEmail,userip,status) values('" . $encrypted . "','$uip','$status')");
					$host = $_SERVER['HTTP_HOST'];
					$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
					header("location:https://$host$uri/$extra");
					exit();
				}	
			}
		}

		if(!$check){
			$extra = "login.php";	
		$email = $_POST['email'];
		$uip = $_SERVER['REMOTE_ADDR'];
		$encrypted = encryptthis($_POST['email'],$key);
		$status = 0;
		$log = mysqli_query($con, "insert into userlog(userEmail,userip,status) values('$encrypted','$uip','$status')");
		$host  = $_SERVER['HTTP_HOST'];
		$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("location:https://$host$uri/$extra");
		$_SESSION['errmsg'] = "Invalid email id or Password";
		exit();
		}
		

		
	} 
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="keywords" content="MediaCenter, Template, eCommerce">
	<meta name="robots" content="all">

	<title>Hiếu Store|Sign-in/Signup</title>

	<!-- Bootstrap Core CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">

	<!-- Customizable CSS -->
	<link rel="stylesheet" href="assets/css/main.css">
	<link rel="stylesheet" href="assets/css/green.css">
	<link rel="stylesheet" href="assets/css/owl.carousel.css">
	<link rel="stylesheet" href="assets/css/owl.transitions.css">
	<!--<link rel="stylesheet" href="assets/css/owl.theme.css">-->
	<link href="assets/css/lightbox.css" rel="stylesheet">
	<link rel="stylesheet" href="assets/css/animate.min.css">
	<link rel="stylesheet" href="assets/css/rateit.css">
	<link rel="stylesheet" href="assets/css/bootstrap-select.min.css">

	<!-- Demo Purpose Only. Should be removed in production -->
	<link rel="stylesheet" href="assets/css/config.css">

	<link href="assets/css/green.css" rel="alternate stylesheet" title="Green color">
	<link href="assets/css/blue.css" rel="alternate stylesheet" title="Blue color">
	<link href="assets/css/red.css" rel="alternate stylesheet" title="Red color">
	<link href="assets/css/orange.css" rel="alternate stylesheet" title="Orange color">
	<link href="assets/css/dark-green.css" rel="alternate stylesheet" title="Darkgreen color">
	<!-- Demo Purpose Only. Should be removed in production : END -->


	<!-- Icons/Glyphs -->
	<link rel="stylesheet" href="assets/css/font-awesome.min.css">

	<!-- Fonts -->
	<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>

	<!-- Favicon -->
	<link rel="shortcut icon" href="assets/images/favicon.ico">
	<script type="text/javascript">
		function valid() {
			if (document.register.password.value != document.register.confirmpassword.value) {
				alert("Password and Confirm Password Field do not match  !!");
				document.register.confirmpassword.focus();
				return false;
			}
			return true;
		}
	</script>
	<script>
		function userAvailability() {
			$("#loaderIcon").show();
			jQuery.ajax({
				url: "check_availability.php",
				data: 'email=' + $("#email").val(),
				type: "POST",
				success: function(data) {
					$("#user-availability-status1").html(data);
					$("#loaderIcon").hide();
				},
				error: function() {}
			});
		}
	</script>



</head>

<body class="cnt-home">



	<!-- ============================================== HEADER ============================================== -->
	<header class="header-style-1">

		<!-- ============================================== TOP MENU ============================================== -->
		<?php include('includes/top-header.php'); ?>
		<!-- ============================================== TOP MENU : END ============================================== -->
		<?php include('includes/main-header.php'); ?>
		<!-- ============================================== NAVBAR ============================================== -->
		<?php include('includes/menu-bar.php'); ?>
		<!-- ============================================== NAVBAR : END ============================================== -->

	</header>

	<!-- ============================================== HEADER : END ============================================== -->
	<div class="breadcrumb">
		<div class="container">
			<div class="breadcrumb-inner">
				<ul class="list-inline list-unstyled">
					<li><a href="index.php">Home</a></li>
					<li class='active'>Authentication</li>
				</ul>
			</div><!-- /.breadcrumb-inner -->
		</div><!-- /.container -->
	</div><!-- /.breadcrumb -->

	<div class="body-content outer-top-bd">
		<div class="container">
			<div class="sign-in-page inner-bottom-sm">
				<div class="row">
					<!-- Sign-in -->
					<div class="col-md-6 col-sm-6 sign-in">
						<h4 class="">sign in</h4>
						<p class="">Hello, Welcome to your account.</p>
											
							<form class="register-form outer-top-xs" method="post">
							
								<span style="color:red;">
								<?php
								echo htmlentities($_SESSION['errmsg']);
								?>
								<?php
								echo htmlentities($_SESSION['errmsg'] = "");
								?>
								</span>
									<div class="form-group">
									<label class="info-title" for="exampleInputEmail1">Email Address <span>*</span></label>
									<input type="email" name="email" class="form-control unicase-form-control text-input" id="exampleInputEmail1">
								</div>
								<div class="form-group">
								<label class="info-title" for="exampleInputPassword1">Password <span>*</span></label>
									<input type="password" name="password" class="form-control unicase-form-control text-input" id="exampleInputPassword1">
									</div>
									<div class="radio outer-xs">
									<a href="forgot-password.php" class="forgot-password pull-right">Forgot your Password?</a>
								</div>
								<button type="submit" class="btn-upper btn btn-primary checkout-page-button" name="login">Login</button>

							</form>
						
					</div>
					<!-- Sign-in -->

					<!-- create a new account -->
					
					<div class="col-md-6 col-sm-6 create-new-account">
						<h4 class="checkout-subtitle">create a new account</h4>
						<p class="text title-tag-line">Create your own Shopping account.</p>
						<form class="register-form outer-top-xs" role="form" method="post" name="register" onSubmit="return valid();">
							<div class="form-group">
								<label class="info-title" for="fullname">Full Name <span>*</span></label>
								<input type="text" class="form-control unicase-form-control text-input" id="fullname" name="fullname" required="required">
							</div>


							<div class="form-group">
								<label class="info-title" for="exampleInputEmail2">Email Address <span>*</span></label>
								<input type="email" class="form-control unicase-form-control text-input" id="email" onBlur="userAvailability()" name="emailid" required>
								<span id="user-availability-status1" style="font-size:12px;"></span>
							</div>

							<div class="form-group">
								<label class="info-title" for="password">Password. <span>*</span></label>
								<input type="password" class="form-control unicase-form-control text-input" id="password" name="password" required>
							</div>

							<div class="form-group">
								<label class="info-title" for="confirmpassword">Confirm Password. <span>*</span></label>
								<input type="password" class="form-control unicase-form-control text-input" id="confirmpassword" name="confirmpassword" required>
							</div>


							<button type="submit" name="submit" class="btn-upper btn btn-primary checkout-page-button" id="submit">Sign Up</button>
						</form>
						<span class="checkout-subtitle outer-top-xs">Sign Up Today And You'll Be Able To : </span>
						<div class="checkbox">
							<label class="checkbox">
								Speed your way through the checkout.
							</label>
							<label class="checkbox">
								Track your orders easily.
							</label>
							<label class="checkbox">
								Keep a record of all your purchases.
							</label>
						</div>
					</div>
					
					<!-- create a new account -->
				</div><!-- /.row -->
			</div>
			<?php include('includes/brands-slider.php'); ?>
		</div>
	</div>
	<?php include('includes/footer.php'); ?>
	<script src="assets/js/jquery-1.11.1.min.js"></script>

	<script src="assets/js/bootstrap.min.js"></script>

	<script src="assets/js/bootstrap-hover-dropdown.min.js"></script>
	<script src="assets/js/owl.carousel.min.js"></script>

	<script src="assets/js/echo.min.js"></script>
	<script src="assets/js/jquery.easing-1.3.min.js"></script>
	<script src="assets/js/bootstrap-slider.min.js"></script>
	<script src="assets/js/jquery.rateit.min.js"></script>
	<script type="text/javascript" src="assets/js/lightbox.min.js"></script>
	<script src="assets/js/bootstrap-select.min.js"></script>
	<script src="assets/js/wow.min.js"></script>
	<script src="assets/js/scripts.js"></script>

	<!-- For demo purposes – can be removed on production -->

	<script src="switchstylesheet/switchstylesheet.js"></script>

	<script>
		$(document).ready(function() {
			$(".changecolor").switchstylesheet({
				seperator: "color"
			});
			$('.show-theme-options').click(function() {
				$(this).parent().toggleClass('open');
				return false;
			});
		});

		$(window).bind("load", function() {
			$('.show-theme-options').delay(2000).trigger('click');
		});
	</script>
	<!-- For demo purposes – can be removed on production : End -->

</body>

</html>