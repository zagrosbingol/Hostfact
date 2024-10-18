<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function getDebtorGroups()
{
    require_once "class/group.php";
    $groups = new group();
    $groups->Type = "debtor";
    $fields = ["GroupName"];
    $group_list = $groups->all($fields);
    $aResult = [];
    if(is_array($group_list) && 0 < $group_list["CountRows"]) {
        foreach ($group_list as $key => $aDebtorGroup) {
            $aResult[] = ["value" => $aDebtorGroup["id"], "key" => $aDebtorGroup["GroupName"]];
        }
    }
    return $aResult;
}
function getCreditorGroups()
{
    require_once "class/group.php";
    $groups = new group();
    $groups->Type = "creditor";
    $fields = ["GroupName"];
    $group_list = $groups->all($fields);
    $aResult = [];
    if(is_array($group_list) && 0 < $group_list["CountRows"]) {
        foreach ($group_list as $key => $aDebtorGroup) {
            $aResult[] = ["value" => $aDebtorGroup["id"], "key" => $aDebtorGroup["GroupName"]];
        }
    }
    return $aResult;
}
function getPriceQuoteStatus()
{
    global $array_pricequotestatus;
    foreach ($array_pricequotestatus as $iId => $sStatus) {
        $aResult[] = ["value" => $iId, "key" => $sStatus];
    }
    return $aResult;
}
function getInvoiceStatus()
{
    global $array_invoicestatus;
    foreach ($array_invoicestatus as $iId => $sStatus) {
        $aResult[] = ["value" => $iId, "key" => $sStatus];
    }
    return $aResult;
}
function getCreditInvoiceStatus()
{
    global $array_creditinvoicestatus;
    foreach ($array_creditinvoicestatus as $iId => $sStatus) {
        $aResult[] = ["value" => $iId, "key" => $sStatus];
    }
    return $aResult;
}

?>