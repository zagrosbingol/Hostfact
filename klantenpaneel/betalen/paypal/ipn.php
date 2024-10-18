<?php
// Load database connection and settings
chdir('../');
require_once "config.php";

require_once "paypal/payment_provider.php";
$tmp_payment_provider = new paypal();
$tmp_payment_provider->isNotificationScript = true;

if(isset($_POST['txn_id']))
{
	// Validate transaction			
	$tmp_payment_provider->validateTransaction($_POST['txn_id']);
}