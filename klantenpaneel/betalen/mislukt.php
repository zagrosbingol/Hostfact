<?php
// Load database connection and settings
require_once "config.php";

$tmp_payment_provider = new Payment_Provider_Base;
$tmp_payment_provider->paymentStatusUnknown('');