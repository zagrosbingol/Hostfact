<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "creditinvoice_api_controller.php";
class creditinvoiceline_api_controller extends creditinvoice_api_controller
{
    public function add_api_action()
    {
        $parse_array = $this->getValidParameters();
        $creditinvoice = $this->object;
        $creditinvoice->Identifier = $this->_get_creditinvoice_id();
        if($creditinvoice->show()) {
            HostFact_API::beginTransaction();
            $aInvoiceLines = $this->_updateCreditInvoiceLines($parse_array);
            foreach ($creditinvoice as $key => $value) {
                if(is_string($value)) {
                    $creditinvoice->{$key} = htmlspecialchars_decode($value);
                }
            }
            if(!empty($creditinvoice->Error) || !$creditinvoice->edit()) {
                HostFact_API::parseError($creditinvoice->Error, true);
            }
            if(!empty($aInvoiceLines)) {
                HostFact_API::parseSuccess(sprintf(__("success add x invoicelines"), count($aInvoiceLines)));
                HostFact_API::commit();
            }
            return $this->_show_creditinvoice($creditinvoice->Identifier);
        } else {
            HostFact_API::parseError($creditinvoice->Error, true);
        }
    }
    public function delete_api_action()
    {
        $parse_array = $this->getValidParameters();
        $creditinvoice = $this->object;
        $creditinvoice->Identifier = $this->_get_creditinvoice_id();
        if($creditinvoice->show()) {
            HostFact_API::beginTransaction();
            $aInvoiceLines = $this->_deleteCreditInvoiceLines($parse_array);
            if(!empty($creditinvoice->Error) || HostFact_API::hasErrors()) {
                HostFact_API::parseError($creditinvoice->Error, true);
            }
            if(0 < $aInvoiceLines) {
                HostFact_API::parseSuccess(sprintf(__("success del x invoicelines"), $aInvoiceLines));
            } else {
                HostFact_API::parseError(__("there are no invoice ids found"), true);
            }
            $creditinvoiceelements = new creditinvoiceelement();
            $count = $creditinvoiceelements->all($creditinvoice->CreditInvoiceCode);
            if(empty($count)) {
                return parent::delete_api_action();
            }
            foreach ($creditinvoice->Variables as $key) {
                if(is_string($creditinvoice->{$key})) {
                    $creditinvoice->{$key} = htmlspecialchars_decode($creditinvoice->{$key});
                }
            }
            if($creditinvoice->edit()) {
                HostFact_API::commit();
                return $this->_show_creditinvoice($creditinvoice->Identifier);
            }
            HostFact_API::parseError($creditinvoice->Error, true);
        } else {
            HostFact_API::parseError($creditinvoice->Error, true);
        }
    }
}

?>