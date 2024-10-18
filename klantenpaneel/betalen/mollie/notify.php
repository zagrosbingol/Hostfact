<?php
// Load database connection and settings
chdir('../');
require_once "config.php";

// Load payment provider class
require_once "mollie/payment_provider.php";
$tmp_payment_provider = new mollie();
$tmp_payment_provider->isNotificationScript = true;

if(isset($_POST["id"]))
{
	// Validate transaction			
	$tmp_payment_provider->validateTransaction($_POST["id"]);
}
else
{
	// If no GET-variable
	$tmp_payment_provider->paymentStatusUnknown('transactie ID niet bekend');
}
?>