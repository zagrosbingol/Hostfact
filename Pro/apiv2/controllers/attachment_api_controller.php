<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class attachment_api_controller extends api_controller
{
    public $InvoiceCode;
    public function __construct()
    {
        parent::__construct();
        HostFact_API::setObjectNames("attachments", "attachment");
        require_once "class/attachment.php";
        $this->addParameter("Identifier", "int");
        $this->addParameter("Type", "string");
        $this->addParameter("Filename", "string");
        $this->addParameter("Base64", "text");
        $this->addParameter("ReferenceIdentifier", "int");
        $this->addParameter("InvoiceCode", "string");
        $this->addParameter("PriceQuoteCode", "string");
        $this->addParameter("TicketID", "string");
        $this->addParameter("CreditInvoiceCode", "string");
        $this->addParameter("DebtorCode", "string");
        $this->addParameter("CreditorCode", "string");
        $this->object = new attachment();
        if(defined("IS_DEMO") && IS_DEMO) {
            HostFact_API::parseError("Not allowed in DEMO environment", true);
        }
    }
    public function add_api_action()
    {
        $this->_setInput();
        if($this->object->FileType == "creditinvoice") {
            $this->object->deleteAllAttachments($this->_get_creditinvoice_id(), "creditinvoice");
            $this->_remove_old_creditinvoice_file();
        }
        if($this->object->saveBase64()) {
            HostFact_API::parseSuccess($this->object->Success, true);
        } else {
            HostFact_API::parseError($this->object->Error, true);
        }
    }
    public function download_api_action()
    {
        $this->_setInput();
        if($this->object->FileType == "creditinvoice") {
            $this->_download_old_creditinvoice_file();
        }
        if($response = $this->object->download()) {
            HostFact_API::parseSuccess($response, true);
        } else {
            HostFact_API::parseError($this->object->Error, true);
        }
    }
    public function delete_api_action()
    {
        $this->_setInput();
        if($this->object->FileType == "creditinvoice") {
            $removeOld = $this->_remove_old_creditinvoice_file();
            if($removeOld === true) {
                HostFact_API::parseSuccess(sprintf(__("api attachment successfully removed"), $this->object->FilenameOriginal), true);
            } elseif($removeOld !== false) {
                HostFact_API::parseError($removeOld, true);
            }
        }
        if($this->object->delete()) {
            HostFact_API::parseSuccess($this->object->Success, true);
        } else {
            HostFact_API::parseError($this->object->Error, true);
        }
    }
    private function _setInput()
    {
        $parse_array = $this->getValidParameters();
        $this->object->Identifier = isset($parse_array["Identifier"]) ? $parse_array["Identifier"] : "";
        $this->object->FileType = isset($parse_array["Type"]) ? $parse_array["Type"] : "";
        $this->object->FilenameOriginal = isset($parse_array["Filename"]) ? $parse_array["Filename"] : "";
        $this->object->FileBase64 = isset($parse_array["Base64"]) ? $parse_array["Base64"] : "";
        $this->object->Reference = isset($parse_array["ReferenceIdentifier"]) ? $parse_array["ReferenceIdentifier"] : "";
        $this->object->InvoiceCode = isset($parse_array["InvoiceCode"]) ? $parse_array["InvoiceCode"] : "";
        $this->object->PriceQuoteCode = isset($parse_array["PriceQuoteCode"]) ? $parse_array["PriceQuoteCode"] : "";
        $this->object->TicketID = isset($parse_array["TicketID"]) ? $parse_array["TicketID"] : "";
        $this->object->CreditInvoiceCode = isset($parse_array["CreditInvoiceCode"]) ? $parse_array["CreditInvoiceCode"] : "";
        $this->object->DebtorCode = isset($parse_array["DebtorCode"]) ? $parse_array["DebtorCode"] : "";
        $this->object->CreditorCode = isset($parse_array["CreditorCode"]) ? $parse_array["CreditorCode"] : "";
    }
    public function _download_old_creditinvoice_file()
    {
        require_once "class/creditinvoice.php";
        $creditinvoice = new creditinvoice();
        $creditinvoice->Identifier = $this->_get_creditinvoice_id();
        if($creditinvoice->show() && $creditinvoice->Location != "") {
            $FilePath = $this->object->fileDir(0, "creditinvoice", true) . $creditinvoice->Location;
            if(@file_exists($FilePath)) {
                $handle = @fopen($FilePath, "r");
                $filedata = @fread($handle, @filesize($FilePath));
                fclose($handle);
                $result["Filename"] = $creditinvoice->Location;
                $result["Base64"] = base64_encode($filedata);
                HostFact_API::parseSuccess($result, true);
            } else {
                HostFact_API::parseError(sprintf(__("the attachment can not be found"), esc($creditinvoice->Location)), true);
            }
        }
        return false;
    }
    public function _remove_old_creditinvoice_file()
    {
        require_once "class/creditinvoice.php";
        $creditinvoice = new creditinvoice();
        $creditinvoice->Identifier = $this->_get_creditinvoice_id();
        if($creditinvoice->show() && $creditinvoice->Location != "") {
            $this->object->FilenameOriginal = $creditinvoice->Location;
            if(@file_exists(@$this->object->fileDir(0, "creditinvoice", true) . $creditinvoice->Location) && @unlink(@$this->object->fileDir(0, "creditinvoice", true) . $creditinvoice->Location)) {
                $creditinvoice->Location = "";
                $creditinvoice->edit();
                return true;
            }
            return __("api the attachment can not be found");
        }
        return false;
    }
    protected function _get_creditinvoice_id()
    {
        require_once "class/creditinvoice.php";
        $creditinvoice = new creditinvoice();
        if($creditInvoice_id = HostFact_API::getRequestParameter("ReferenceIdentifier")) {
            $creditinvoice_id = $creditinvoice->getID("identifier", $creditInvoice_id);
            if($creditInvoice_id === false) {
                HostFact_API::parseError(__("invalid identifier for creditinvoice"), true);
            }
            $this->object->Reference = $creditInvoice_id;
            return $creditInvoice_id;
        }
        if($creditInvoiceCode = HostFact_API::getRequestParameter("CreditInvoiceCode")) {
            if($creditInvoice_id = $creditinvoice->getID("creditinvoicecode", $creditInvoiceCode)) {
                $this->object->Reference = $creditInvoice_id;
                return $creditInvoice_id;
            }
            HostFact_API::parseError(__("invalid creditinvoicecode"), true);
        } else {
            return false;
        }
    }
}

?>