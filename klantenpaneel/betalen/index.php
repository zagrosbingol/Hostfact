<?php
require_once "config.php";
$controller = new PaymentForm_Controller;


// Check for backoffice
if(isset($_GET['testurl']) && $_GET['testurl'] == "true"){ 
	die("ideal"); 
}
// If payment get variable is given, show payment methods
elseif(isset($_GET['payment']))
{
	$controller->index();	
}
// If transaction status is 'paid', show success-message
elseif(isset($_SESSION['payment']['status']) && $_SESSION['payment']['status'] == 'paid')
{
	$controller->paid();	
}
// If transaction status is 'pending', show success-message
elseif(isset($_SESSION['payment']['status']) && $_SESSION['payment']['status'] == 'pending')
{
	$controller->pending();	
}
// If transaction status is 'failed', show error-message
elseif(isset($_SESSION['payment']['status']) && $_SESSION['payment']['status'] == 'failed')
{
	$controller->failed();	
}
else
{
	$controller->index();	
}
exit;