<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class attachment
{
    public $Identifier;
    public $FileDir;
    public $FileSize;
    public $filename;
    public $FileExtension;
    public $FilenameServer;
    public $FilenameOriginal;
    public $FileBase64;
    public $Error;
    public $Warning;
    public $Success;
    public function __construct()
    {
        $this->FileType = "";
        $this->Reference = "";
        $this->Success = $this->Warning = $this->Error = [];
    }
    public function getAttachmentInfoByFilename($Filename, $Type, $Reference)
    {
        $results = [];
        $return = false;
        $dbResult = Database_Model::getInstance()->getOne("HostFact_Documents", ["id"])->where("Filename", $Filename)->where("Reference", $Reference)->where("Type", $Type)->execute();
        if($dbResult === false) {
            return false;
        }
        return $dbResult->id;
    }
    public function getAttachmentInfo($id)
    {
        $results = [];
        $Filename = [];
        $dbResults = Database_Model::getInstance()->getOne("HostFact_Documents")->where("id", $id)->execute();
        return $dbResults;
    }
    public function getAttachments($id, $type, $showAll = false)
    {
        if(!$id) {
            return [];
        }
        $results = [];
        $Filename = [];
        $dbResults = Database_Model::getInstance()->get("HostFact_Documents")->where("Type", $type)->where("Reference", $id)->execute();
        if(!empty($dbResults)) {
            foreach ($dbResults as $key => $value) {
                if(!@file_exists(@$this->fileDir($value->Reference, $value->Type) . $value->FilenameServer) && $showAll === false) {
                    $Filename[] = $value->Filename;
                } else {
                    $results[$key] = $value;
                }
            }
            if(!empty($Filename) && $showAll === false) {
                $this->Warning[] = sprintf(__("the attachment can not be found"), implode(", ", $Filename));
            }
        }
        return $results;
    }
    public function changeAttachmentReference($type, $from_id, $to_id)
    {
        $result = Database_Model::getInstance()->get("HostFact_Documents")->where("Type", $type)->where("Reference", $from_id)->asArray()->execute();
        if(!empty($result)) {
            foreach ($result as $key => $value) {
                if(!@file_exists(@$this->fileDir($value["Reference"], $value["Type"]) . $value["FilenameServer"])) {
                } else {
                    if(!@is_dir(@$this->fileDir($to_id, $value["Type"]))) {
                        @mkdir(@$this->fileDir($to_id, $value["Type"]), 502, true);
                    }
                    if(rename($this->fileDir($value["Reference"], $value["Type"]) . $value["FilenameServer"], $this->fileDir($to_id, $value["Type"]) . $value["FilenameServer"])) {
                        Database_Model::getInstance()->update("HostFact_Documents", ["Reference" => $to_id])->where("Type", $type)->where("Reference", $from_id)->execute();
                    }
                }
            }
        }
    }
    public function checkAttachments($Param)
    {
        $Identifier = $Param["Identifier"];
        $Files = $Param["Files"];
        $Type = $Param["Type"];
        $currentAttachments = [];
        foreach ($this->getAttachments($Param["Identifier"], $Param["Type"], true) as $key => $value) {
            $currentAttachments[] = $value->id;
            $currentAttachmentsData[$value->id] = $value;
        }
        foreach ($currentAttachments as $key) {
            if(!in_array($key, $Files)) {
                $dir = $this->fileDir($currentAttachmentsData[$key]->Reference, $currentAttachmentsData[$key]->Type);
                if(!@file_exists($dir . $currentAttachmentsData[$key]->FilenameServer) || @unlink($dir . $currentAttachmentsData[$key]->FilenameServer)) {
                    Database_Model::getInstance()->delete("HostFact_Documents")->where("id", $key)->execute();
                    if(@is_dir($dir) && !glob($dir . "*") && !@rmdir($dir)) {
                        $this->Error[] = sprintf(__("unable to remove dir"), $dir);
                    }
                }
            }
        }
        foreach ($Files as $key) {
            if(!in_array($key, $currentAttachments)) {
                $getAttachmentInfo = $this->getAttachmentInfo($key);
                $fullPath = $this->fileDir($Identifier, $Type);
                $basePath = $this->fileDir($Identifier, $Type, true);
                $filename = $getAttachmentInfo->FilenameServer;
                if(!is_dir($fullPath)) {
                    mkdir($fullPath, 502, true);
                }
                if(@file_exists($basePath . $filename) && @copy($basePath . $filename, $fullPath . $filename) && @unlink($basePath . $filename)) {
                    Database_Model::getInstance()->update("HostFact_Documents", ["Type" => $Type, "Reference" => $Identifier])->where("id", $key)->execute();
                } else {
                    $this->Error[] = sprintf(__("unable to move attachment"), $filename, $fullPath);
                }
            }
        }
    }
    public function deleteAllAttachments($reference, $type)
    {
        $param = ["Identifier" => $reference, "Files" => [], "Type" => $type];
        $this->checkAttachments($param);
    }
    public function cronCleanUp()
    {
        $dbResults = Database_Model::getInstance()->get("HostFact_Documents")->where("Reference", "0")->where("DateTime", ["<" => ["RAW" => "DATE_ADD(NOW(), INTERVAL -4 HOUR)"]])->execute();
        if(is_array($dbResults) && !empty($dbResults)) {
            foreach ($dbResults as $document) {
                $filename = $this->fileDir($document->Reference, $document->Type, true) . $document->FilenameServer;
                if(@file_exists($filename) && !@unlink($filename)) {
                    $this->Error[] = sprintf(__("unable to remove attachment from the server"), $filename);
                } else {
                    Database_Model::getInstance()->delete("HostFact_Documents")->where("id", $document->id)->execute();
                }
            }
        }
    }
    public function fileDir($Reference, $fileType, $getTempdir = false)
    {
        $Type = $fileType ? $fileType . "/" : "";
        $Reference = $Reference ? $Reference . "/" : "";
        $dir = "";
        switch ($fileType) {
            case "pdf":
                $dir = DIR_PDF_FILES;
                break;
            case "template_image":
                $dir = DIR_PDF_FILES . "images/";
                break;
            case "email":
                $dir = DIR_EMAIL_ATTACHMENTS;
                break;
            case "ticket":
                $dir = DIR_TICKET_ATTACHMENTS;
                $Type = "";
                break;
            case "pricequote_accepted":
            case "pricequote":
            case "invoice":
                $dir = DIR_INVOICE_ATTACHMENTS;
                break;
            case "creditinvoice":
                $dir = DIR_CREDIT_INVOICES;
                $Type = "";
                break;
            case "debtor":
                $dir = DIR_DEBTOR_ATTACHMENTS;
                $Type = "";
                break;
            case "creditor":
                $dir = DIR_CREDITOR_ATTACHMENTS;
                $Type = "";
                break;
            default:
                if($getTempdir === true) {
                    return $dir;
                }
                return $dir . $Type . $Reference;
        }
    }
    public function deleteAttachment($id)
    {
        if($document = $this->getAttachmentInfo($id)) {
            $dir = $this->fileDir($document->Reference, $document->Type);
            $filename = $dir . $document->FilenameServer;
            if(@file_exists($filename) && !@unlink($filename)) {
                $this->Error[] = sprintf(__("unable to remove attachment from the server"), $filename);
                return false;
            }
            Database_Model::getInstance()->delete("HostFact_Documents")->where("id", $document->id)->execute();
            if(@is_dir($dir) && !glob($dir . "*") && !@rmdir($dir)) {
                $this->Warning[] = sprintf(__("unable to remove dir"), $dir);
            }
            return true;
        }
        $this->Error[] = __("invalid attachment identifier");
        return false;
    }
    public function _removeAttachment()
    {
        $dir = "";
        $file = "";
        if($file && $dir) {
            if(@file_exists($file)) {
                if(!@unlink($file)) {
                    $this->Error[] = sprintf(__("unable to remove attachment from the server"), $file);
                    return false;
                }
                if(@is_dir($dir) && !glob($dir . "*") && !@rmdir($dir)) {
                    $this->Warning[] = sprintf(__("unable to remove dir"), $dir);
                }
                return true;
            }
            $this->Error[] = sprintf(__("unable to remove attachment from the server"), $file);
            return false;
        }
    }
    public function saveBase64($skip_reference_check = false)
    {
        if(strlen($this->FileBase64) === 0) {
            $this->Error[] = __("no base64 string found");
            return false;
        }
        if($skip_reference_check === false && !$this->validateReference() || !$this->_setFileInfo()) {
            return false;
        }
        if($result = @file_put_contents($this->FileDir . $this->FilenameServer, @base64_decode($this->FileBase64))) {
            if($this->FileSize == "") {
                $this->FileSize = @filesize($this->FileDir . $this->FilenameServer);
            }
            $this->_saveFileToDb();
            switch ($this->FileType) {
                case "invoice":
                    $this->Success[] = sprintf(__("invoice the file is saved"), $this->Filename . "." . $this->FileExtension);
                    break;
                case "pricequote":
                    $this->Success[] = sprintf(__("price quote the file is saved"), $this->Filename . "." . $this->FileExtension);
                    break;
                case "creditinvoice":
                    $this->Success[] = sprintf(__("creditinvoice the file is saved"), $this->Filename . "." . $this->FileExtension);
                    break;
                case "debtor":
                    $this->Success[] = sprintf(__("debtor the file is saved"), $this->Filename . "." . $this->FileExtension);
                    break;
                case "creditor":
                    $this->Success[] = sprintf(__("creditor the file is saved"), $this->Filename . "." . $this->FileExtension);
                    break;
                default:
                    return true;
            }
        } else {
            $this->Error[] = __("unable to save the file");
            return false;
        }
    }
    public function saveBase64_Ticket()
    {
        if(strlen($this->FileBase64) === 0) {
            $this->Error[] = __("no base64 string found");
            return false;
        }
        if(!$this->validateReference() || !$this->_setFileInfo(true, false)) {
            return false;
        }
        if($result = @file_put_contents($this->FileDir . $this->FilenameServer, @base64_decode($this->FileBase64))) {
            if($this->FileSize == "") {
                $this->FileSize = @filesize($this->FileDir . $this->FilenameServer);
            }
            return $this->FileDir . $this->FilenameServer;
        }
        $this->Error[] = __("unable to save the file");
        return false;
    }
    public function delete()
    {
        if(!$this->validateReference()) {
            return false;
        }
        $AttachmentId = "";
        if($this->FileType == "creditinvoice") {
            $documentData = $this->getAttachments($this->Reference, $this->FileType);
            if($documentData) {
                $AttachmentId = $documentData[0]->id;
                $this->FilenameOriginal = $documentData[0]->Filename;
            } else {
                $this->Error[] = __("api the attachment can not be found");
                return false;
            }
        } elseif($this->FilenameOriginal != "") {
            $AttachmentId = $this->getAttachmentInfoByFilename($this->FilenameOriginal, $this->FileType, $this->Reference);
            if($AttachmentId === false) {
                $this->Error[] = sprintf(__("the attachment can not be found"), esc($this->FilenameOriginal));
                return false;
            }
        } elseif($this->Identifier != "") {
            $documentData = $this->getAttachmentInfo($this->Identifier);
            if($documentData && $this->FileType == $documentData->Type && $this->Reference == $documentData->Reference) {
                $AttachmentId = $documentData->id;
                $this->FilenameOriginal = $documentData->Filename;
            } else {
                $this->Error[] = __("api the attachment can not be found");
                return false;
            }
        }
        if($this->deleteAttachment($AttachmentId) && empty($this->Error)) {
            $this->Success[] = sprintf(__("api attachment successfully removed"), $this->FilenameOriginal);
            return true;
        }
        return false;
    }
    public function download()
    {
        if(!$this->validateReference()) {
            return false;
        }
        $result = [];
        $AttachmentId = "";
        if($this->FileType == "creditinvoice") {
            $documentData = $this->getAttachments($this->Reference, $this->FileType);
            if($documentData) {
                $AttachmentId = $documentData[0]->id;
            } else {
                $this->Error[] = __("api the attachment can not be found");
                return false;
            }
        } else {
            if($this->FileType == "ticket") {
                if(strpos($this->FilenameOriginal, "..") !== false || strpos($this->FilenameOriginal, "./") !== false || strpos($this->FilenameOriginal, "/") !== false) {
                    $this->Error[] = __("api the attachment can not be found");
                    return false;
                }
                $FilePath = $this->FileDir . $this->Reference . "/" . $this->FilenameOriginal;
                $filename = $this->FilenameOriginal;
                $handle = @fopen($FilePath, "r");
                $filedata = @fread($handle, @filesize($FilePath));
                fclose($handle);
                $result["Filename"] = $filename;
                $result["Base64"] = base64_encode($filedata);
                return $result;
            }
            if($this->FilenameOriginal != "") {
                $AttachmentId = $this->getAttachmentInfoByFilename($this->FilenameOriginal, $this->FileType, $this->Reference);
                if($AttachmentId === false) {
                    $this->Error[] = sprintf(__("the attachment can not be found"), esc($this->FilenameOriginal));
                    return false;
                }
            } elseif($this->Identifier != "") {
                $documentData = $this->getAttachmentInfo($this->Identifier);
                if($documentData && $this->FileType == $documentData->Type && $this->Reference == $documentData->Reference) {
                    $AttachmentId = $documentData->id;
                } else {
                    $this->Error[] = __("api the attachment can not be found");
                    return false;
                }
            }
        }
        if($data = $this->getAttachmentInfo($AttachmentId)) {
            $Filedir = $this->fileDir($data->Reference, $data->Type);
            $FilePath = $Filedir . $data->FilenameServer;
            $filename = $data->Filename;
            $handle = @fopen($FilePath, "r");
            $filedata = @fread($handle, @filesize($FilePath));
            fclose($handle);
            $result["Filename"] = $filename;
            $result["Base64"] = base64_encode($filedata);
            return $result;
        }
        $this->Error[] = sprintf(__("filetype not found"), esc($this->FileType));
        return false;
    }
    private function validateReference($documentId = false)
    {
        $this->FileDir = $this->fileDir(0, $this->FileType, true);
        switch ($this->FileType) {
            case "invoice":
                require_once "class/invoice.php";
                $invoice = new invoice();
                if(0 < $this->Reference) {
                    $invoice->Identifier = $this->Reference;
                    if($invoice->show()) {
                        $this->InvoiceCode = $invoice->InvoiceCode;
                        return true;
                    }
                    $this->Error = array_merge($invoice->Error, $this->Error);
                    return false;
                }
                if($invoice_id = $invoice->getID("invoicecode", $this->InvoiceCode)) {
                    $this->Reference = $invoice_id;
                    return true;
                }
                $this->Error[] = __("invalid identifier for invoice");
                return false;
                break;
            case "creditinvoice":
                require_once "class/creditinvoice.php";
                $creditinvoice = new creditinvoice();
                if(0 < $this->Reference) {
                    $creditinvoice->Identifier = $this->Reference;
                    if($creditinvoice->show()) {
                        $this->CreditInvoiceCode = $creditinvoice->CreditInvoiceCode;
                        return true;
                    }
                    $this->Error = array_merge($creditinvoice->Error, $this->Error);
                    return false;
                }
                if($creditinvoice_id = $creditinvoice->getID("creditinvoicecode", $this->CreditInvoiceCode)) {
                    $this->Reference = $creditinvoice_id;
                    return true;
                }
                $this->Error[] = __("invalid identifier for invoice");
                return false;
                break;
            case "pricequote":
                require_once "class/pricequote.php";
                $pricequote = new pricequote();
                if(0 < $this->Reference) {
                    $pricequote->Identifier = $this->Reference;
                    if($pricequote->show()) {
                        $this->PriceQuoteCode = $pricequote->PriceQuoteCode;
                        return true;
                    }
                    $this->Error = array_merge($pricequote->Error, $this->Error);
                    return false;
                }
                if($pricequote_id = $pricequote->getID("pricequotecode", $this->PriceQuoteCode)) {
                    $this->Reference = $pricequote_id;
                    return true;
                }
                $this->Error[] = __("invalid identifier for pricequote");
                return false;
                break;
            case "debtor":
                require_once "class/debtor.php";
                $debtor = new debtor();
                if(0 < $this->Reference) {
                    $debtor->Identifier = $this->Reference;
                    if($debtor->show()) {
                        $this->DebtorCode = $debtor->DebtorCode;
                        return true;
                    }
                    $this->Error = array_merge($debtor->Error, $this->Error);
                    return false;
                }
                if($debtor_id = $debtor->getID("debtorcode", $this->DebtorCode)) {
                    $this->Reference = $debtor_id;
                    return true;
                }
                $this->Error[] = __("invalid identifier for debtor");
                return false;
                break;
            case "creditor":
                require_once "class/creditor.php";
                $creditor = new creditor();
                if(0 < $this->Reference) {
                    $creditor->Identifier = $this->Reference;
                    if($creditor->show()) {
                        $this->CreditorCode = $creditor->CreditorCode;
                        return true;
                    }
                    $this->Error = array_merge($creditor->Error, $this->Error);
                    return false;
                }
                if($creditor_id = $creditor->getID("creditorcode", $this->CreditorCode)) {
                    $this->Reference = $creditor_id;
                    return true;
                }
                $this->Error[] = __("invalid identifier for creditor");
                return false;
                break;
            case "ticket":
                require_once "class/ticket.php";
                $ticket = new ticket();
                if($this->TicketID) {
                    $this->Reference = $this->TicketID;
                }
                if($this->Reference) {
                    $ticket->TicketID = $this->Reference;
                    if($ticket->show("TicketID")) {
                        return true;
                    }
                    $this->Error = array_merge($ticket->Error, $this->Error);
                    return false;
                }
                break;
            default:
                $this->Error[] = sprintf(__("filetype not found"), esc($this->FileType));
                return false;
        }
    }
    public function _setFileInfo($checkDir = true, $rewriteName = true)
    {
        if(!isset($this->FilenameOriginal) || strlen($this->FilenameOriginal) === 0) {
            $this->Error[] = __("no file found");
            return false;
        }
        if($checkDir === true && !$this->_checkDir()) {
            $this->Error[] = __("no dir found");
            return false;
        }
        if($rewriteName === true) {
            $this->Filename = substr($this->FilenameOriginal, 0, strlen($this->FilenameOriginal) - strlen($this->_getExtension()) - 1);
            $Random = substr(hash("sha1", $this->FilenameOriginal . time() . $this->_getExtension() . $this->FilenameOriginal[mt_rand(0, strlen($this->FilenameOriginal))]), 4, 7);
            $this->FilenameServer = $this->Filename . "-" . $Random . "." . $this->_getExtension();
            $this->_checkFile_exists();
        } else {
            $this->Filename = $this->FilenameOriginal = $this->FilenameServer = $this->FilenameOriginal;
            $this->_checkFile_exists();
            $this->Filename = $this->FilenameOriginal = $this->FilenameOriginal = $this->FilenameServer;
        }
        return true;
    }
    public function _checkFile_exists()
    {
        for ($x = 1; @file_exists($this->FileDir . $this->FilenameServer); $x++) {
            $tmp = substr($this->FilenameServer, 0, strlen($this->FilenameServer) - strlen($this->_getExtension()) - 1);
            if(1 < $x) {
                $this->FilenameServer = substr($tmp, 0, strlen($tmp) - strlen($x) - 2) . "(" . $x . ")." . $this->_getExtension();
            } else {
                $this->FilenameServer = $tmp . "(" . $x . ")." . $this->_getExtension();
            }
        }
    }
    public function _saveFileToDb()
    {
        $result = Database_Model::getInstance()->insert("HostFact_Documents", ["Filename" => $this->FilenameOriginal, "FilenameServer" => $this->FilenameServer, "Size" => $this->FileSize, "Type" => $this->FileType, "Reference" => $this->Reference, "DateTime" => date("Y-m-d H:i:s")])->execute();
        if($result !== false) {
            $document_id = $result;
            return $document_id;
        }
        return false;
    }
    public function _checkDir()
    {
        if(!isset($this->FileDir) || strlen($this->FileDir) === 0) {
            $this->Error[] = __("no dir found");
            return false;
        }
        $this->FileDir = $this->fileDir($this->Reference, $this->FileType);
        if(!@is_dir($this->FileDir)) {
            @mkdir($this->FileDir, 502, true);
        }
        return true;
    }
    public function _getExtension()
    {
        if(!isset($this->FileExtension) || strlen($this->FileExtension) === 0) {
            $this->FileExtension = strtolower(substr($this->FilenameOriginal, strrpos($this->FilenameOriginal, ".") + 1));
        }
        return $this->FileExtension;
    }
    public function createPreviewForPDF($from_path, $to_path, $crop = true)
    {
        if(!class_exists("imagick")) {
            return false;
        }
        $im = new imagick();
        $im->setResolution(200, 200);
        $im->readImage($from_path . "[0]");
        if($crop) {
            $geo = $im->getImageGeometry();
            $size_w_mm = ceil($geo["width"] / 200 * 0 * 10);
            $size_h_mm = ceil($geo["height"] / 200 * 0 * 10);
            if($size_w_mm != PDF_PAGE_WIDTH || $size_h_mm != PDF_PAGE_HEIGHT) {
                $pdf_w_pixels = PDF_PAGE_WIDTH / 10 / 0 * 200;
                $pdf_h_pixels = PDF_PAGE_HEIGHT / 10 / 0 * 200;
                $_x = ceil(($geo["width"] - $pdf_w_pixels) / 2);
                $_y = ceil(($geo["height"] - $pdf_h_pixels) / 2);
                $im->cropImage($pdf_w_pixels, $pdf_h_pixels, $_x, $_y);
                $im->borderImage("white", max(0, ceil(($pdf_w_pixels - $im->getImageWidth()) / 2)), max(0, ceil(($pdf_h_pixels - $im->getImageHeight()) / 2)));
            }
        }
        $im->setImageFormat("png");
        $im->resizeImage(843, 1192, imagick::FILTER_UNDEFINED, 1);
        $im->writeImage($to_path);
        return true;
    }
}

?>