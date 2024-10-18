<?php
// Load database connection and settings
chdir('../');
require_once "config.php";

require_once "ideal.ing.advanced.v3/payment_provider.php";
$tmp_payment_provider = new ideal_ing_advanced_v3();
	
if(isset($_GET['trxid']))
{
	// Validate transaction			
	$tmp_payment_provider->validateTransaction($_GET['trxid']);
}
else
{
	// If no GET-variable
	$tmp_payment_provider->paymentStatusUnknown();
}