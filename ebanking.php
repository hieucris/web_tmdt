<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (isset($_GET['action'])) {
	$sql = "update orders set paymentMethod='Online Banking', orderStatus='Successful' where orderStatus is null and userId=" . $_SESSION['id'] . " and productId in (";
	foreach ($_SESSION['cart'] as $id => $value) {
		$sql .= $id . ",";
	}
	$sql = substr($sql, 0, -1) . ")";
	$query = mysqli_query($con, $sql);
	unset($_SESSION['cart']);
	echo "<script>alert('Successfully paid!')</script>";
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

	<title>UNO Store|Online Banking</title>
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
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

	<!-- HTML5 elements and media queries Support for IE8 : HTML5 shim and Respond.js -->
	<!--[if lt IE 9]>
			<script src="assets/js/html5shiv.js"></script>
			<script src="assets/js/respond.min.js"></script>
		<![endif]-->

</head>

<body class="cnt-home">


	<!-- ============================================== HEADER ============================================== -->
	<header class="header-style-1">
		<?php include('includes/top-header.php'); ?>
		<?php include('includes/main-header.php'); ?>
		<?php include('includes/menu-bar.php'); ?>
	</header>
	<!-- ============================================== HEADER : END ============================================== -->
	<div class="breadcrumb">
		<div class="container">
			<div class="breadcrumb-inner">
				<ul class="list-inline list-unstyled">
					<li><a href="my-cart.php">My Cart </a></li>
					<li class='active'>Pay</li>
				</ul>
			</div><!-- /.breadcrumb-inner -->
		</div><!-- /.container -->
	</div><!-- /.breadcrumb -->

	<div class="body-content outer-top-xs">
		<div class="container">
			<div class="row inner-bottom-sm">
				<div class="shopping-cart">
					<div class="col-md-12 col-sm-12 shopping-cart-table ">
						<div class="table-responsive">
							<form name="cart" method="post">
								<?php
								if (!empty($_SESSION['cart'])) {
								?>
									<table class="table table-bordered">
										<thead>
											<tr>
												<th class="cart-description item">Image</th>
												<th class="cart-product-name item">Product Name</th>

												<th class="cart-qty item">Quantity</th>
												<th class="cart-sub-total item">Price Per unit</th>
												<th class="cart-sub-total item">Shipping Charge</th>
												<th class="cart-total last-item">
													<div class="cart-grand-total">
														Grand Total
													</div>
												</th>
											</tr>
										</thead><!-- /thead -->
										<tbody>
											<?php
											$pdtid = array();
											$sql = "SELECT * FROM products WHERE id IN (SELECT productid FROM orders WHERE orderStatus IS null) OR id IN(";
											foreach ($_SESSION['cart'] as $id => $value) {
												$sql .= $id . ",";
											}

											$sql = substr($sql, 0, -1) . ") ORDER BY id ASC";
											$query = mysqli_query($con, $sql);
											$totalprice = 0;
											$totalqunty = 0;
											if (!empty($query)) {
												while ($row = mysqli_fetch_array($query)) {
													$quantity = $_SESSION['cart'][$row['id']]['quantity'];
													$subtotal = $_SESSION['cart'][$row['id']]['quantity'] * $row['productPrice'] + $row['shippingCharge'];
													$totalprice += $subtotal;
													$_SESSION['qnty'] = $totalqunty += $quantity;

													array_push($pdtid, $row['id']);
													//print_r($_SESSION['pid'])=$pdtid;exit;
											?>
													<tr>
														<td class="cart-image">
															<a class="entry-thumbnail" href="product-details.php">
																<img src="admin/productimages/<?php echo $row['id']; ?>/<?php echo $row['productImage1']; ?>" alt="" width="114" height="146">
															</a>
														</td>
														<td class="cart-product-name-info">
															<h4 class='cart-product-description'><a href="product-details.php?pid=<?php echo htmlentities($pd = $row['id']); ?>"><?php echo $row['productName'];

																																													$_SESSION['sid'] = $pd;
																																													?></a></h4>
															<div class="row">
																<div class="col-sm-4">
																	<div class="rating rateit-small"></div>
																</div>
																<div class="col-sm-8">
																	<?php $rt = mysqli_query($con, "select * from productreviews where productId='$pd'");
																	$num = mysqli_num_rows($rt); {
																	?>
																		<div class="reviews">
																			( <?php echo htmlentities($num); ?> Reviews )
																		</div>
																	<?php } ?>
																</div>
															</div><!-- /.row -->

														</td>
														<td class="cart-product-quantity">
															<?php echo $_SESSION['cart'][$row['id']]['quantity']; ?>
														</td>
														<td class="cart-product-sub-total"><span class="cart-sub-total-price"><?php echo "$" . $row['productPrice']; ?>.00</span></td>
														<td class="cart-product-sub-total"><span class="cart-sub-total-price"><?php echo "$" . $row['shippingCharge']; ?>.00</span></td>
														<td class="cart-product-grand-total">
															<span class="cart-sub-total-price"><?php echo "$" . ($_SESSION['cart'][$row['id']]['quantity'] * $row['productPrice'] + $row['shippingCharge']); ?>.00</span>
														</td>
													</tr>
											<?php }
											}
											$_SESSION['pid'] = $pdtid;
											?>
										</tbody><!-- /tbody -->
									</table><!-- /table -->
						</div>
					</div><!-- /.shopping-cart-table -->
					<div class="info-boxes wow fadeInUp">
						<div class="info-boxes-inner">
							<div class="row">
								<div class="col-md-6 col-sm-4 col-lg-4">
									<div class="info-box">
										<div class="row">
											<div class="col-xs-2">
												<i class="icon fa fa-dollar"></i>
											</div>
											<div class="col-xs-10">
												<h4 class="info-box-heading green">
													Total: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
													<span><?php echo "$totalprice" . ".00"; ?></span>
												</h4>
											</div>
										</div>
										<h6 class="text" ; align='right'>Please pay now!</h6>
									</div>
								</div><!-- .col -->
							</div>
						</div>
					</div>
					<br></br>
					<div class="col-md-4 col-sm-12 cart-shopping-total">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>
										<div class="cart-grand-total"> Payment via Online Banking </div>
									</th>
								</tr>
							</thead><!-- /thead -->
							<tbody>
								<tr>
									<td>
										<div class="cart-checkout-btn pull-right">
											<div>
												<span>
													<b>Account name: </b> NGUYEN THANH PHUONG
												</span>
												<span>
													<b>Account number: </b> 1010158623004
												</span>
												<span>
													<b>Bank name: </b> Military Commercial Joint Stock Bank
												</span>
												<span>
													<b>SWIFT Code: </b> MSCBVNVX
												</span>
											</div>
											<br>
											<div class="action"><a href="ebanking.php?page=payment&action=pay&id=<?php echo $_SESSION['id'].$totalprice; ?>" class="lnk btn btn-primary">Complete</a></div>
											</br>
										</div>
									</td>
								</tr>
							</tbody><!-- /tbody -->
						</table>
					<?php } else {
								echo "Your shopping Cart is empty";
								
							} ?>
					</div>
				</div>
			</div>
			</form>
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