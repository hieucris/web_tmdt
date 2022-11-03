<?php

/* -----------------------------------------------------------------------------

 Version 2.0

 @author OnePAY

------------------------------------------------------------------------------*/

session_start();
error_reporting(0);
include('includes/config.php');

if (isset($_GET['action'])) {
	$sql = "update orders set paymentMethod='OnePay', orderStatus='Successful' where orderStatus is null and userId=" . $_SESSION['id'] . " and productId in (";
	foreach ($_SESSION['cart'] as $id => $value) {
		$sql .= $id . ",";
	}
	$sql = substr($sql, 0, -1) . ")";
	$query = mysqli_query($con, $sql);
	unset($_SESSION['cart']);
	echo "<script>alert('Successfully paid!')</script>";
}

//get total price

$pdtid = array();
$sql = "SELECT * FROM products WHERE id IN(";
foreach ($_SESSION['cart'] as $id => $value) {
	$sql .= $id . ",";
}
$sql = substr($sql, 0, -1) . ") ORDER BY id ASC";
$query = mysqli_query($con, $sql);
$totalprice = 0;
$totalqunty = 0;
$total_shipping = 0;
$items = array();
if (!empty($query)) {
	while ($row = mysqli_fetch_array($query)) {
		$quantity = $_SESSION['cart'][$row['id']]['quantity'];
		$subtotal = $_SESSION['cart'][$row['id']]['quantity'] * $row['productPrice'] + $row['shippingCharge'];
		$totalprice += $subtotal;
		$total_shipping += $row['shippingCharge'];
		$_SESSION['qnty'] = $totalqunty += $quantity;
		//Get items list
		$arr = array($row['productName'], $rowz['productDescription'], $quantity, $row['productPrice'], "0", $row['productCompany'], "USD");
		array_push($items, $arr);
		array_push($pdtid, $row['id']);
		//print_r($_SESSION['pid'])=$pdtid;exit;
	}
}

//Lay thong tin dia chi khach hang
$sql = "select * from users where id= " . $_SESSION['id'];
$result = mysqli_query($con, $sql);
$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
//print_r($data);

//Lay orderID
$sql = "select MAX(id) as order_id from  orders where userId= " . $_SESSION['id'];
$result = mysqli_query($con, $sql);
$order_id = mysqli_fetch_array($result, MYSQLI_ASSOC)['order_id'];

// *********************
// START OF MAIN PROGRAM
// *********************

// Define Constants
// ----------------
// This is secret for encoding the MD5 hash
// This secret will vary from merchant to merchant
// To not create a secure hash, let SECURE_SECRET be an empty string - ""
// $SECURE_SECRET = "secure-hash-secret";
// Khóa bí mật - được cấp bởi OnePAY
$SECURE_SECRET = "A3EFDFABA8653DF2342E8DAC29B51AF0";

// add the start of the vpcURL querystring parameters
// *****************************Lấy giá trị url cổng thanh toán*****************************
$vpcURL = 'https://mtf.onepay.vn/onecomm-pay/vpc.op' . "?";

// Remove the Virtual Payment Client URL from the parameter hash as we 
// do not want to send these fields to the Virtual Payment Client.
// bỏ giá trị url và nút submit ra khỏi mảng dữ liệu
// unset($_POST["virtualPaymentClientURL"]); 
// unset($_POST["SubButL"]);
$vpc_Merchant = "ONEPAY";
$vpc_AccessCode = "D67342C2";
$vpc_MerchTxnRef = time();
$vpc_OrderInfo = "JSECURETEST01";
$vpc_Amount = $totalprice * 2300000;
$vpc_ReturnURL = "https://dinhvanhieu.000webhostapp.com/payment-method.php";
$vpc_Version = "2";
$vpc_Command = "ONEPAY";
$vpc_Locale = "vn";
$vpc_Currency = "VND";

$data = array(
      'vpc_Merchant' => $vpc_Merchant,
      'vpc_AccessCode' => $vpc_AccessCode,
      'vpc_MerchTxnRef' => $vpc_MerchTxnRef,
      'vpc_OrderInfo' => $vpc_OrderInfo,
      'vpc_Amount' => $vpc_Amount,
      'vpc_ReturnURL' => $vpc_ReturnURL,
      'vpc_Version' => $vpc_Version,
      'vpc_Command' => $vpc_Command,
      'vpc_Locale' => $vpc_Locale,
      'vpc_Currency' => $vpc_Currency
);

//$stringHashData = $SECURE_SECRET; *****************************Khởi tạo chuỗi dữ liệu mã hóa trống*****************************
$stringHashData = "";
// sắp xếp dữ liệu theo thứ tự a-z trước khi nối lại
// arrange array data a-z before make a hash
ksort ($data);

// set a parameter to show the first pair in the URL
// đặt tham số đếm = 0
$appendAmp = 0;

foreach($data as $key => $value) {

    // create the md5 input and URL leaving out any fields that have no value
    // tạo chuỗi đầu dữ liệu những tham số có dữ liệu
    if (strlen($value) > 0) {
        // this ensures the first paramter of the URL is preceded by the '?' char
        if ($appendAmp == 0) {
            $vpcURL .= urlencode($key) . '=' . urlencode($value);
            $appendAmp = 1;
        } else {
            $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
        }
        //$stringHashData .= $value; *****************************sử dụng cả tên và giá trị tham số để mã hóa*****************************
        if ((strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
		    $stringHashData .= $key . "=" . $value . "&";
		}
    }
}
//*****************************xóa ký tự & ở thừa ở cuối chuỗi dữ liệu mã hóa*****************************
$stringHashData = rtrim($stringHashData, "&");
// Create the secure hash and append it to the Virtual Payment Client Data if
// the merchant secret has been provided.
// thêm giá trị chuỗi mã hóa dữ liệu được tạo ra ở trên vào cuối url
if (strlen($SECURE_SECRET) > 0) {
    //$vpcURL .= "&vpc_SecureHash=" . strtoupper(md5($stringHashData));
    // *****************************Thay hàm mã hóa dữ liệu*****************************
    $vpcURL .= "&vpc_SecureHash=" . strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*',$SECURE_SECRET)));
}

// FINISH TRANSACTION - Redirect the customers using the Digital Order
// ===================================================================
// chuyển trình duyệt sang cổng thanh toán theo URL được tạo ra
header("Location: ".$vpcURL);

// *******************
// END OF MAIN PROGRAM
// *******************

