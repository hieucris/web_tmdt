<?php
session_start();
error_reporting(0);
include('includes/config.php');

$vnp_HashSecret = "A3EFDFABA8653DF2342E8DAC29B51AF0";
$vnp_SecureHash = $_GET['vnp_SecureHash'];
$inputData = array();
foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}

unset($inputData['vnp_SecureHash']);
ksort($inputData);
$i = 0;
$hashData = "";
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
}

$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
//$secureHash = md5($vnp_HashSecret . $hashData);
//So sanh hash các giá trị, nếu giống thì mới kiểm tra tình trạng đơn
if ($secureHash == $vnp_SecureHash) {
    if ($_GET['vnp_ResponseCode'] == '00') {
        $sql = "update orders set paymentMethod='OnePay', orderStatus='Successful' where orderStatus is null and userId=" . $_SESSION['id'] . " and productId in (";
        foreach ($_SESSION['cart'] as $id => $value) {
            $sql .= $id . ",";
        }
        $sql = substr($sql, 0, -1) . ")";
        $query = mysqli_query($con, $sql);
        unset($_SESSION['cart']);
        echo "<script>
				alert('Successfully paid!');
				window.location.href='/index.php';
			</script>";
    } else {
        echo "<script>
				alert('UnSuccessfully paid!');
				window.location.href='/index.php';
			</script>";
    }
} else {
    echo "Invalid signature";
}
