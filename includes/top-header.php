<?php
//session_start();
?>

<div class="top-bar animate-dropdown">
	<div class="container">
		<div class="header-top-inner">
			<div class="cnt-account">
				<ul class="list-unstyled">

					<?php if (strlen($_SESSION['login'])) {   ?>
						<li><a href="#"><i class="icon fa fa-user"></i>Welcome -<?php 
						///Test decryption
						$key='qkwjdiw239&&jdafweihbrhnan&^%$ggdnawhd4njshjwuuO';
						 
						$encryption_key = base64_decode($key);
						list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($_SESSION['username']), 2),2,null);
						$name= openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);	
							
							
						echo htmlentities($name); ?></a></li>
					<?php } ?>

					<li><a href="my-account.php"><i class="icon fa fa-user"></i>My Account</a></li>
					<li><a href="my-wishlist.php"><i class="icon fa fa-heart"></i>Wishlist</a></li>
					<li><a href="my-cart.php"><i class="icon fa fa-shopping-cart"></i>My Cart</a></li>
					<?php if (strlen($_SESSION['login']) == 0) {   ?>
						<li><a href="login.php"><i class="icon fa fa-sign-in"></i>Login</a></li>
					<?php } else { ?>

						<li><a href="logout.php"><i class="icon fa fa-sign-out"></i>Logout</a></li>
					<?php } ?>
				</ul>
			</div><!-- /.cnt-account -->

			<div class="cnt-block">
				<ul class="list-unstyled list-inline">
					<li class="dropdown dropdown-small">
						<a href="track-orders.php" class="dropdown-toggle"><span class="key">Track Order</b></a>

					</li>


				</ul>
			</div>

			<div class="clearfix"></div>
		</div><!-- /.header-top-inner -->
	</div><!-- /.container -->
</div><!-- /.header-top -->