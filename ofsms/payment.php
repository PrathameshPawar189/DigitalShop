<?php
session_start();
require "vendor/autoload.php";
require 'includes/dbconnection.php';

use Razorpay\Api\Api;
// use Razorpay\Api\Errors\SignatureVerificationError;
$fname=$_POST['fname'];
$cnumber=$_POST['cnumber'];
$fnaobno=$_POST['flatbldgnumber'];
$street=$_POST['streename'];
$area=$_POST['area'];
$lndmark=$_POST['landmark'];
$city=$_POST['city'];
$zcode=$_POST['zipcode'];
$state=$_POST['state'];
$userid=$_SESSION['ofsmsuid'];
$patype='online';
 $ordernumber=mt_rand(100000000, 999999999);
$sql="insert into tblorder(OrderNumber,UserID,FullName,ContactNumber,FlatNo,StreetName,Area,Landmark,City,Zipcode,State,payType)values(:ordernumber,:userid,:fname,:cnumber,:fnaobno,:street,:area,:lndmark,:city,:zcode,:state,:patype)";
$query=$dbh->prepare($sql);
$query->bindParam(':ordernumber',$ordernumber,PDO::PARAM_STR);
$query->bindParam(':userid',$userid,PDO::PARAM_STR);
$query->bindParam(':fname',$fname,PDO::PARAM_STR);
$query->bindParam(':cnumber',$cnumber,PDO::PARAM_STR);
$query->bindParam(':fnaobno',$fnaobno,PDO::PARAM_STR);
$query->bindParam(':street',$street,PDO::PARAM_STR);
$query->bindParam(':area',$area,PDO::PARAM_STR);
$query->bindParam(':lndmark',$lndmark,PDO::PARAM_STR);
$query->bindParam(':city',$city,PDO::PARAM_STR);
$query->bindParam(':zcode',$zcode,PDO::PARAM_STR);
$query->bindParam(':state',$state,PDO::PARAM_STR);
$query->bindParam(':patype',$patype,PDO::PARAM_STR);
$price = $_SESSION['tprice'];
// $q = serialize($query);
 $query->execute();
   $LastInsertId=$dbh->lastInsertId();
  //  if ($LastInsertId>0) 
  if(true){
$quantity=$_POST['quantity'];
$pdd=$_SESSION['pid'];
	$value=array_combine($pdd,$quantity);

foreach($value as $pdid=> $qty){
$sql="insert into tblorderdetails(UserId,OrderNumber,ProductId,ProductQty)values(:userid,:ordernumber,:pdid,:qty)";
$query=$dbh->prepare($sql);
$query->bindParam(':ordernumber',$ordernumber,PDO::PARAM_STR);
$query->bindParam(':userid',$userid,PDO::PARAM_STR);
$query->bindParam(':pdid',$pdid,PDO::PARAM_STR);
$query->bindParam(':qty',$qty,PDO::PARAM_STR);
}
   }
// echo "hii";
$keyId = 'rzp_live_TDk5wUtBG2TvYH';
$keySecret = 'astkh21gDdrB9fS7YRlbo2Iy';
// $keyId = 'rzp_test_JO2Y69OjTYIDJ4';
// $keySecret = 'TQ2EbxRUWVomXrjYFGRroGCJ';
$displayCurrency = 'INR';

$api = new Api($keyId, $keySecret);

$orderData = array('receipt'=> strval(rand(999,9999)),'amount'=>  1* 100, 'currency' => 'INR', 'payment_capture' => 1);
$razorpayOrder = $api->order->create($orderData);
$razorpayOrderId="";
$razorpayOrderId = $razorpayOrder['id'];
$_SESSION['razorpay_order_id'] = $razorpayOrderId;
$_SESSION['order'] = $razorpayOrder;


$displayAmount = $amount = $orderData['amount'];
$checkout = 'automatic';
$data = [
      "key"               => $keyId,
      "amount"            => $amount,
      "name"              => "Home Decore",
      "description"       => "Payment",
      "prefill"           => [
          "name"              => "Kunal Shingote",
          "email"             => "kunal123@gmail.com",
          "contact"           => "9970820480",
      ],
      "theme"             => [
          "color"             => "#009846"
      ],
      "order_id"          => $razorpayOrderId,
  ];
  $json = json_encode($data);
?>
<form name='razorpayform' action="verify.php" method="POST">
<input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
<input type="hidden" name="razorpay_signature"  id="razorpay_signature" >
</form>
<script type="text/javascript" src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
// Checkout details as a json
var options = <?=$json?>;
/**
 * The entire list of Checkout fields is available at
 * https://docs.razorpay.com/docs/checkout-form#checkout-fields
 */
options.handler = function (response){
    document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
    document.getElementById('razorpay_signature').value = response.razorpay_signature;
    document.razorpayform.submit();
};
// Boolean whether to show image inside a white frame. (default: true)
options.theme.image_padding = false;
options.modal = {
    ondismiss: function() {
        console.log("This code runs when the popup is closed");
		window.location = 'index.php';
    },
    // Boolean indicating whether pressing escape key
    // should close the checkout form. (default: true)
    escape: true,
    // Boolean indicating whether clicking translucent blank
    // space outside checkout form should close the form. (default: false)
    backdropclose: false
};
//alert("Hoo");
var rzp = new Razorpay(options);
rzp.open();
</script>
