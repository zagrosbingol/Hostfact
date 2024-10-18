<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
define("API_DIR", "apiv2");
if(empty($_POST)) {
    $_POST = json_decode(file_get_contents("php://input"), true);
}
define("InStAlLHosTFacT", "Ra.33sUdhWlkd22");
define("LOGINSYSTEM", true);
define("SKIP_CSRF_CHECK", true);
chdir("../");
require_once "config.php";
require_once API_DIR . "/hostfactapi.class.php";
HostFact_API::parseRawRequestData();
HostFact_API::setResponseType("JSON");
HostFact_API::setRequestMethod("POST");
HostFact_API::checkLogin();
error_reporting(0);
ini_set("display_errors", "off");
require_once "class/company.php";
$company = new company();
$company->show();
require_once "class/employee.php";
$account = new employee();
$account->Name = $account->Function = $account->EmailAddress = $account->PhoneNumber = $account->MobileNumber = $account->Signature = "";
$result = HostFact_API::routeRequest();

?>