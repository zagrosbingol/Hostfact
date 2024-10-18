<?php
// Load database connection and settings
chdir('../');
require_once "config.php";

require_once "multisafepay/payment_provider.php";
$tmp_payment_provider = new multisafepay();

if(isset($_GET['transactionid']))
{
	// Validate transaction			
	$tmp_payment_provider->validateTransaction($_GET['transactionid']);
}