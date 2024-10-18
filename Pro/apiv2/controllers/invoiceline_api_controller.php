<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "invoice_api_controller.php";
class invoiceline_api_controller extends invoice_api_controller
{
    public function add_api_action()
    {
        $parse_array = $this->getValidParameters();
        $invoice = $this->object;
        $invoice->Identifier = $this->_get_invoice_id();
        if($invoice->show()) {
            HostFact_API::beginTransaction();
            $aInvoiceLines = $this->_updateInvoiceLines($parse_array);
            foreach ($invoice as $key => $value) {
                if(is_string($value)) {
                    $invoice->{$key} = htmlspecialchars_decode($value);
                }
            }
            if(!empty($invoice->Error) || !$invoice->edit()) {
                HostFact_API::parseError($invoice->Error, true);
            }
            if(!empty($aInvoiceLines)) {
                HostFact_API::parseSuccess(sprintf(__("success add x invoicelines"), count($aInvoiceLines)));
                HostFact_API::commit();
            }
            return $this->_show_invoice($invoice->Identifier);
        } else {
            HostFact_API::parseError($invoice->Error, true);
        }
    }
    public function delete_api_action()
    {
        $parse_array = $this->getValidParameters();
        $invoice = $this->object;
        $invoice->Identifier = $this->_get_invoice_id();
        if($invoice->show()) {
            HostFact_API::beginTransaction();
            $aInvoiceLines = $this->_deleteInvoiceLines($parse_array);
            if(!empty($invoice->Error) || HostFact_API::hasErrors()) {
                HostFact_API::parseError($invoice->Error, true);
            }
            if(0 < $aInvoiceLines) {
                HostFact_API::parseSuccess(sprintf(__("success del x invoicelines"), $aInvoiceLines));
            } else {
                HostFact_API::parseError(__("there are no invoice ids found"), true);
            }
            $elements = new invoiceelement();
            $count = $elements->all($invoice->InvoiceCode);
            if(empty($count)) {
                return parent::delete_api_action();
            }
            foreach ($invoice->Variables as $key) {
                if(is_string($invoice->{$key})) {
                    $invoice->{$key} = htmlspecialchars_decode($invoice->{$key});
                }
            }
            if($invoice->edit()) {
                HostFact_API::commit();
                return $this->_show_invoice($invoice->Identifier);
            }
            HostFact_API::parseError($invoice->Error, true);
        } else {
            HostFact_API::parseError($invoice->Error, true);
        }
    }
}

?>