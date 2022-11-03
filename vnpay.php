<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

session_start();
error_reporting(0);
include('includes/config.php');

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

$vnp_TmnCode = "QG22O52H"; //Website ID in VNPAY System
$vnp_HashSecret = "KHVPUGYNOAEYHPHPMNBTZTGIFKEOKQNQ"; //Secret key
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
$vnp_Returnurl = "https://dinhvanhieu.000webhostapp.com/vnpay_return.php";
$vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
//Config input format
//Expire
$startTime = date("YmdHis");
$expire = date('YmdHis',strtotime('+10 minutes',strtotime($startTime)));

//Lay orderID
$sql = "select MAX(id) as order_id from  orders where userId= " . $_SESSION['id'];
$result = mysqli_query($con, $sql);
$order_id = mysqli_fetch_array($result, MYSQLI_ASSOC)['order_id'];

$vnp_TxnRef = $order_id; //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
$vnp_OrderInfo = 'thanh toan';
$vnp_OrderType = 'billpayment';
$vnp_Amount =  $totalprice * 2300000;
$vnp_Locale = 'vn';
$vnp_BankCode = 'NCB';
$vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

$inputData = array(
    "vnp_Version" => "2.1.0",
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_Amount" => $vnp_Amount,
    "vnp_Command" => "pay",
    "vnp_CreateDate" => date('YmdHis'),
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $vnp_IpAddr,
    "vnp_Locale" => $vnp_Locale,
    "vnp_OrderInfo" => $vnp_OrderInfo,
    "vnp_OrderType" => $vnp_OrderType,
    "vnp_ReturnUrl" => $vnp_Returnurl,
    "vnp_TxnRef" => $vnp_TxnRef
);

if (isset($vnp_BankCode) && $vnp_BankCode != "") {
    $inputData['vnp_BankCode'] = $vnp_BankCode;
}
if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
    $inputData['vnp_Bill_State'] = $vnp_Bill_State;
}

//var_dump($inputData);
ksort($inputData);
$query = "";
$i = 0;
$hashdata = "";
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
    $query .= urlencode($key) . "=" . urlencode($value) . '&';
}

$vnp_Url = $vnp_Url . "?" . $query;
if (isset($vnp_HashSecret)) {
    $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//  
    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
}
$returnData = array('code' => '00'
    , 'message' => 'success'
    , 'data' => $vnp_Url);
    if (isset($_POST['redirect'])) {
        header('Location: ' . $vnp_Url);
        die();
    } else {
        echo json_encode($returnData);
    }
?>

