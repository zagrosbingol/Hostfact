<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class template
{
    public $Identifier;
    public $Name;
    public $Location;
    public $Standard;
    public $Author;
    public $Title;
    public $FileName;
    public $Type;
    public $EmailTemplate;
    public $PostLocation;
    public $ElementsPerPage;
    public $Elements;
    public $CountRows;
    public $Table;
    public $Error;
    public $Warning;
    public $Success;
    public $Variables = ["Identifier", "Name", "Location", "Standard", "Author", "Title", "FileName", "Type", "EmailTemplate", "PostLocation", "ElementsPerPage"];
    public function __construct()
    {
        $this->Type = "invoice";
        $this->Standard = "0";
        $this->ElementsPerPage = "10";
        $this->ExtraElement = false;
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for template");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Templates")->where("id", $this->Identifier)->execute();
        if(!isset($result->id) || empty($result->id)) {
            $this->Error[] = __("invalid identifier for template");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = htmlspecialchars($value);
        }
        if(!defined("PDF_MODULE") || PDF_MODULE == "fpdf") {
            $elements = new templateelement();
            $fields = ["Name", "Type", "Font", "FontSize", "FontStyle", "Align", "Visible", "Page", "X", "Y", "Width", "Height", "Value"];
            $this->Elements = $elements->all($fields, "Name", "ASC", "-1", "", "", $this->Identifier);
        }
        return true;
    }
    public function add()
    {
        if($this->validate() !== true) {
            return false;
        }
        if($this->Standard == 1) {
            Database_Model::getInstance()->update("HostFact_Templates", ["Standard" => "0"])->where("Type", $this->Type)->execute();
        }
        $result = Database_Model::getInstance()->insert("HostFact_Templates", ["Name" => $this->Name, "Location" => $this->Location, "Standard" => $this->Standard, "Author" => $this->Author, "Title" => $this->Title, "FileName" => $this->FileName, "Type" => $this->Type, "EmailTemplate" => $this->EmailTemplate, "PostLocation" => $this->PostLocation, "ElementsPerPage" => $this->ElementsPerPage])->execute();
        if($result) {
            $this->Identifier = $result;
            $this->Success[] = sprintf(__("template created"), $this->Name);
            return true;
        }
        return false;
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for template");
            return false;
        }
        if($this->validate() !== true) {
            return false;
        }
        if($this->Standard == 1) {
            Database_Model::getInstance()->update("HostFact_Templates", ["Standard" => "0"])->where("Type", $this->Type)->execute();
        }
        $result = Database_Model::getInstance()->update("HostFact_Templates", ["Name" => $this->Name, "Location" => $this->Location, "Standard" => $this->Standard, "Author" => $this->Author, "Title" => $this->Title, "FileName" => $this->FileName, "Type" => $this->Type, "EmailTemplate" => $this->EmailTemplate, "PostLocation" => $this->PostLocation, "ElementsPerPage" => $this->ElementsPerPage])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("template updated"), $this->Name);
            return true;
        }
        return false;
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for template");
            return false;
        }
        if(!$this->show()) {
            return false;
        }
        if($this->Standard == 1) {
            $this->Error[] = __("template is default template");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Settings", ["id"])->where("Value", $this->Identifier)->where("Variable", ["IN" => ["CREDIT_TEMPLATE", "INVOICE_REMINDER_LETTER", "INVOICE_SUMMATION_LETTER"]])->execute();
        if($result) {
            $this->Error[] = __("template in use for setting");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Debtors", ["DebtorCode"])->where("Status", ["<" => 9])->orWhere([["InvoiceTemplate", $this->Identifier], ["PriceQuoteTemplate", $this->Identifier]])->execute();
        if($result) {
            $this->Error[] = sprintf(__("template in use for debtor setting"), $result->DebtorCode);
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Packages", ["PackageName"])->where("Status", ["<" => 9])->where("PdfTemplate", $this->Identifier)->execute();
        if($result) {
            $this->Error[] = sprintf(__("template in use for package"), $result->PackageName);
            return false;
        }
        Database_Model::getInstance()->delete("HostFact_Templates")->where("id", $this->Identifier)->execute();
        if(!defined("PDF_MODULE") || PDF_MODULE == "fpdf") {
            Database_Model::getInstance()->delete("HostFact_TemplateElements")->where("Template", $this->Identifier)->execute();
        } else {
            Database_Model::getInstance()->delete("HostFact_TemplateBlocks")->where("template_id", $this->Identifier)->execute();
        }
        $email_list = Database_Model::getInstance()->get("HostFact_EmailTemplates", ["id", "Attachment"])->where("Attachment", ["LIKE" => "%TemplateOther" . $this->Identifier . "%"])->execute();
        if($email_list) {
            foreach ($email_list as $result) {
                $new_attachment = [];
                $attachments = explode("|", $result->Attachment);
                foreach ($attachments as $attach) {
                    if($attach != "TemplateOther" . $this->Identifier) {
                        $new_attachment[] = $attach;
                    }
                }
                $new_attachment = implode("|", $new_attachment);
                Database_Model::getInstance()->update("HostFact_EmailTemplates", ["Attachment" => $new_attachment])->where("id", $result->id)->execute();
            }
        }
        $this->Success[] = sprintf(__("template deleted"), $this->Name);
        return true;
    }
    public function TemplateClone()
    {
        $template_list = $this->all(["Name"]);
        $used_names = [];
        foreach ($template_list as $t) {
            $used_names[] = $t["Name"];
        }
        $this->show();
        $old_name = $this->Name;
        $i = 2;
        do {
            $this->Name = $old_name . "(" . $i . ")";
            $i++;
        } while (!in_array($this->Name, $used_names));
        $this->Standard = 0;
        foreach ($this as $key2 => $value2) {
            if(is_string($value2)) {
                $this->{$key2} = htmlspecialchars_decode($value2);
            }
        }
        $this->add();
        foreach ($this->Elements as $key => $value) {
            if(is_numeric($key)) {
                $elements = new templateelement();
                foreach ($value as $key2 => $value2) {
                    if(is_string($value2)) {
                        $elements->{$key2} = htmlspecialchars_decode($value2);
                    }
                }
                $elements->Template = $this->Identifier;
                $elements->add();
            }
        }
        $this->show();
        return true;
    }
    public function createNewTemplateFrom($clone_from, $to_type)
    {
        $this->Identifier = $clone_from;
        if(!$this->show()) {
            return false;
        }
        $template_list = $this->all(["Name"]);
        $used_names = [];
        foreach ($template_list as $t) {
            $used_names[] = $t["Name"];
        }
        $old_name = $this->Name;
        $i = 2;
        do {
            $this->Name = $old_name . "(" . $i . ")";
            $i++;
        } while (!in_array($this->Name, $used_names));
        foreach ($this as $key2 => $value2) {
            if(is_string($value2)) {
                $this->{$key2} = htmlspecialchars_decode($value2);
            }
        }
        $from_type = $this->Type;
        $this->Type = $to_type;
        $this->Standard = 0;
        if($this->Type == "other") {
            $this->EmailTemplate = 0;
        }
        if(!$this->add()) {
            return false;
        }
        $result = Database_Model::getInstance()->get("HostFact_TemplateBlocks")->where("template_id", $clone_from)->asArray()->execute();
        if($result) {
            foreach ($result as $k => $item) {
                if($this->Type == "other" && ($item["type"] == "invoicelines" || $item["type"] == "totals")) {
                } else {
                    Database_Model::getInstance()->insert("HostFact_TemplateBlocks", ["template_id" => $this->Identifier, "type" => $item["type"], "value" => $item["value"], "positioning_x" => $item["positioning_x"], "positioning_y" => $item["positioning_y"], "positioning_w" => $item["positioning_w"], "positioning_h" => $item["positioning_h"], "visibility" => $item["visibility"], "text_family" => $item["text_family"], "text_size" => $item["text_size"], "text_color" => $item["text_color"], "text_align" => $item["text_align"], "text_lineheight" => $item["text_lineheight"], "text_style" => $item["text_style"], "borders_top" => $item["borders_top"], "borders_right" => $item["borders_right"], "borders_bottom" => $item["borders_bottom"], "borders_left" => $item["borders_left"], "borders_thickness" => $item["borders_thickness"], "borders_color" => $item["borders_color"], "borders_type" => $item["borders_type"], "style_bgcolor" => $item["style_bgcolor"], "cols" => $item["cols"], "rows" => $item["rows"]])->execute();
                }
            }
        }
        if($from_type == "other" && $this->Type != "other") {
            require_once "class/templateblock.php";
            $templateblock = new templateblock();
            $templateblock->createBlock($this->Identifier, "invoicelines");
            $templateblock->createBlock($this->Identifier, "totals");
        }
        return true;
    }
    public function transform()
    {
        if(!$this->show()) {
            return false;
        }
        if($this->Type == "invoice") {
            $result = Database_Model::getInstance()->getOne("HostFact_Invoice", ["id"])->where("Template", $this->Identifier)->execute();
            if($result) {
                $this->Error[] = __("this template is used for one or more invoices and cannot be transformed");
                return false;
            }
            $result = Database_Model::getInstance()->getOne("HostFact_Debtors", ["id"])->where("InvoiceTemplate", $this->Identifier)->execute();
            if($result) {
                $this->Error[] = __("this template is used as default template for one or more debtors and cannot be transformed");
                return false;
            }
        } else {
            $result = Database_Model::getInstance()->getOne("HostFact_PriceQuote", ["id"])->where("Template", $this->Identifier)->execute();
            if($result) {
                $this->Error[] = __("this template is used for one or more pricequotes and cannot be transformed");
                return false;
            }
            $result = Database_Model::getInstance()->getOne("HostFact_Debtors", ["id"])->where("PriceQuoteTemplate", $this->Identifier)->execute();
            if($result) {
                $this->Error[] = __("this template is used as default pricequote template for one or more debtors and cannot be transformed");
                return false;
            }
        }
        $this->Type = $this->Type == "invoice" ? "pricequote" : "invoice";
        $result = Database_Model::getInstance()->update("HostFact_Templates", ["Type" => $this->Type, "Standard" => "0"])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("template transformed"), $this->Name);
            return true;
        }
        return false;
    }
    public function readDir()
    {
        $dir = DIR_PDF_FILES;
        $pdf_source = [];
        if($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if($file != "." && $file != ".." && $file != "index.php" && file_exists($dir . $file)) {
                    $pdf_source[$dir . $file] = $file;
                }
            }
        }
        asort($pdf_source);
        return $pdf_source;
    }
    public function getStandard($type)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_Templates", ["id"])->where("Type", $type)->where("Standard", "1")->execute();
        if($result === false) {
            return false;
        }
        return $result->id;
    }
    public function setDefault($template_id, $type)
    {
        $this->Identifier = $template_id;
        if(!$this->show()) {
            return false;
        }
        Database_Model::getInstance()->update("HostFact_Templates", ["Standard" => "0"])->where("Type", $type)->execute();
        $result = Database_Model::getInstance()->update("HostFact_Templates", ["Standard" => "1"])->where("id", $template_id)->execute();
        if($result) {
            $this->Success[] = sprintf(__("template set as default"), $this->Name);
            return true;
        }
        return false;
    }
    public function printLayout($example_data = [])
    {
        $OutputType = "D";
        require "class/pdf.php";
        $objects = [];
        if(!empty($example_data)) {
            unset($example_data["invoiceElement"]);
            $objects = $example_data;
        } else {
            require_once "class/debtor.php";
            $debtor = new debtor();
            foreach ($debtor->Variables as $key) {
                $debtor->{$key} = "[" . $key . "]";
            }
            $debtor->CountryLong = "[CountryLong]";
            $objects["debtor"] = $debtor;
            switch ($this->Type) {
                case "other":
                case "invoice":
                    require_once "class/invoice.php";
                    $invoice = new invoice();
                    $variables = get_object_vars($invoice);
                    foreach ($variables as $key => $value) {
                        if(!is_array($value)) {
                            $invoice->{$key} = "[" . $key . "]";
                        }
                    }
                    $invoice->AmountTax = "[AmountTax]";
                    $invoice->AmountDiscount = "[AmountDiscount]";
                    $invoice->AmountDiscountIncl = "[AmountDiscountIncl]";
                    $invoice->PayBefore = "[PayBefore]";
                    $invoice->Identifier = "Identifier";
                    $invoice->CountryLong = "[CountryLong]";
                    $invoice->Elements = [];
                    $invoiceelement = new invoiceelement();
                    $variables = get_object_vars($invoiceelement);
                    foreach ($variables as $key => $value) {
                        if(!is_array($value)) {
                            $invoice->Elements[0][$key] = "[" . $key . "]";
                        }
                    }
                    $invoice->Elements[0]["id"] = 0;
                    $invoice->Elements[0]["NoDiscountAmountIncl"] = "[NoDiscountAmountIncl]";
                    $invoice->Elements[0]["NoDiscountAmountTax"] = "[NoDiscountAmountTax]";
                    $invoice->Elements[0]["NoDiscountAmountExcl"] = "[NoDiscountAmountExcl]";
                    $invoice->Elements[0]["AmountIncl"] = "[AmountIncl]";
                    $invoice->Elements[0]["AmountTax"] = "[AmountTax]";
                    $invoice->Elements[0]["AmountExcl"] = "[AmountExcl]";
                    $invoice->Elements[0]["PeriodPriceIncl"] = "[PeriodPriceIncl]";
                    $invoice->Elements[0]["PeriodPriceTax"] = "[PeriodPriceTax]";
                    $invoice->Elements[0]["PeriodPriceExcl"] = "[PeriodPriceExcl]";
                    $invoice->Elements[0]["DiscountAmountIncl"] = "[DiscountAmountIncl]";
                    $invoice->Elements[0]["DiscountAmountTax"] = "[DiscountAmountTax]";
                    $invoice->Elements[0]["DiscountAmountExcl"] = "[DiscountAmountExcl]";
                    $invoice->Elements[0]["PriceIncl"] = "[PriceIncl]";
                    $invoice->Elements[0]["PriceTax"] = "[PriceTax]";
                    $invoice->Elements[0]["PriceExcl"] = "[PriceExcl]";
                    $invoice->Elements[0]["FullDiscountPercentage"] = "[FullDiscountPercentage]";
                    $invoice->Elements[0]["FullTaxPercentage"] = "[FullTaxPercentage]";
                    $objects["invoice"] = $invoice;
                    break;
                case "pricequote":
                    require_once "class/pricequote.php";
                    $pricequote = new pricequote();
                    $variables = get_object_vars($pricequote);
                    foreach ($variables as $key => $value) {
                        if(!is_array($value)) {
                            $pricequote->{$key} = "[" . $key . "]";
                        }
                    }
                    $pricequote->AmountTax = "[AmountTax]";
                    $pricequote->AmountDiscount = "[AmountDiscount]";
                    $pricequote->AmountDiscountIncl = "[AmountDiscountIncl]";
                    $pricequote->ExpirationDate = "[ExpirationDate]";
                    $pricequote->Identifier = "Identifier";
                    $pricequote->CountryLong = "[CountryLong]";
                    $pricequote->Elements = [];
                    $pricequoteeelement = new pricequoteelement();
                    $variables = get_object_vars($pricequoteeelement);
                    foreach ($variables as $key => $value) {
                        if(!is_array($value)) {
                            $pricequote->Elements[0][$key] = "[" . $key . "]";
                        }
                    }
                    $pricequote->Elements[0]["id"] = 0;
                    $pricequote->Elements[0]["NoDiscountAmountIncl"] = "[NoDiscountAmountIncl]";
                    $pricequote->Elements[0]["NoDiscountAmountTax"] = "[NoDiscountAmountTax]";
                    $pricequote->Elements[0]["NoDiscountAmountExcl"] = "[NoDiscountAmountExcl]";
                    $pricequote->Elements[0]["AmountIncl"] = "[AmountIncl]";
                    $pricequote->Elements[0]["AmountTax"] = "[AmountTax]";
                    $pricequote->Elements[0]["AmountExcl"] = "[AmountExcl]";
                    $pricequote->Elements[0]["PeriodPriceIncl"] = "[PeriodPriceIncl]";
                    $pricequote->Elements[0]["PeriodPriceTax"] = "[PeriodPriceTax]";
                    $pricequote->Elements[0]["PeriodPriceExcl"] = "[PeriodPriceExcl]";
                    $pricequote->Elements[0]["DiscountAmountIncl"] = "[DiscountAmountIncl]";
                    $pricequote->Elements[0]["DiscountAmountTax"] = "[DiscountAmountTax]";
                    $pricequote->Elements[0]["DiscountAmountExcl"] = "[DiscountAmountExcl]";
                    $pricequote->Elements[0]["PriceIncl"] = "[PriceIncl]";
                    $pricequote->Elements[0]["PriceTax"] = "[PriceTax]";
                    $pricequote->Elements[0]["PriceExcl"] = "[PriceExcl]";
                    $pricequote->Elements[0]["FullDiscountPercentage"] = "[FullDiscountPercentage]";
                    $pricequote->Elements[0]["FullTaxPercentage"] = "[FullTaxPercentage]";
                    $objects["invoice"] = $pricequote;
                    $objects["pricequote"] = $pricequote;
                    break;
                default:
                    if($this->Type == "other") {
                        require_once "class/hosting.php";
                        require_once "class/server.php";
                        $hosting = new hosting();
                        $server = new server();
                        $variables_hosting = get_object_vars($hosting);
                        $variables_server = get_object_vars($server);
                        foreach ($variables_hosting as $key => $value) {
                            if(!is_array($value)) {
                                $hosting->{$key} = "[" . $key . "]";
                            }
                        }
                        foreach ($variables_server as $key => $value) {
                            if(!is_array($value)) {
                                $server->{$key} = "[" . $key . "]";
                            }
                        }
                        $objects["hosting"] = $hosting;
                        $objects["server"] = $server;
                    }
            }
        }
        $pdf = new pdfCreator($this->Identifier, $objects, $this->Type, "D", true, true);
        $pdf->setOutputType($OutputType);
        if(!$pdf->generatePDF($OutputType)) {
            $this->Error = array_merge($this->Error, $pdf->Error);
            return false;
        }
        return true;
    }
    public function validate()
    {
        if(!(is_string($this->Name) && 3 < strlen($this->Name) && strlen($this->Name) <= 100)) {
            $this->Error[] = __("template invalid name");
        }
        if(!(is_string($this->Author) && strlen($this->Author) <= 255 || strlen($this->Author) === 0)) {
            $this->Error[] = __("template invalid author");
        }
        if(!($this->Standard == "1" || $this->Standard == "0")) {
            $this->Standard = "0";
        }
        if(!($this->Type == "invoice" || $this->Type == "pricequote" || $this->Type == "other")) {
            $this->Error[] = __("template invalid type");
        }
        if(!$this->EmailTemplate && $this->Type != "other") {
            $this->Warning[] = __("no email template");
        }
        if(!is_numeric($this->ElementsPerPage) || $this->ElementsPerPage < 1) {
            $this->ElementsPerPage = 10;
        }
        if(!empty($this->Error)) {
            return false;
        }
        return true;
    }
    public function all($fields, $sort = "Name", $order = false, $limit = "-1", $searchat = false, $searchfor = false, $group = false)
    {
        $limit = !is_numeric($limit) ? -1 : $limit;
        if(!is_array($fields) || empty($fields)) {
            $this->Error[] = __("no fields given for retrieving data");
            return false;
        }
        $select = ["id"];
        foreach ($fields as $value) {
            $select[] = $value;
        }
        Database_Model::getInstance()->get("HostFact_Templates", $select);
        if($searchat && in_array($searchat, $this->Variables)) {
            Database_Model::getInstance()->where($searchat, ["LIKE" => "%" . $searchfor . "%"]);
        }
        if($sort) {
            $order = in_array($order, ["ASC", "DESC"]) ? $order : "ASC";
            Database_Model::getInstance()->orderBy($sort, $order);
        }
        if(0 <= $limit) {
            $show_results = MAX_RESULTS_LIST;
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        $list = [];
        $list["CountRows"] = $this->CountRows = 0;
        if($template_list = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = $this->CountRows = Database_Model::getInstance()->rowCount("HostFact_Templates", "id");
            foreach ($template_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $value) {
                    $list[$result->id][$value] = htmlspecialchars($result->{$value});
                }
            }
        }
        $list["CountRows"] = $this->CountRows;
        return $list;
    }
}
class templateelement
{
    public $Identifier;
    public $Template;
    public $Name;
    public $Type;
    public $Font;
    public $FontSize;
    public $FontStyle;
    public $Align;
    public $Visible;
    public $Page;
    public $X;
    public $Y;
    public $Width;
    public $Height;
    public $Value;
    public $CountRows;
    public $Table;
    public $Error;
    public $Warning;
    public $Success;
    public $Variables = ["Identifier", "Template", "Name", "Type", "Font", "FontSize", "FontStyle", "Align", "Visible", "Page", "X", "Y", "Width", "Height", "Value"];
    public function __construct()
    {
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
        $this->Page = 1;
        $this->FontSize = "10";
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for template element");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_TemplateElements")->where("id", $this->Identifier)->execute();
        if($result === false) {
            $this->Error[] = __("invalid identifier for template element");
            return false;
        }
        foreach ($result as $key => $value) {
            if($key != "id") {
                $this->{$key} = htmlspecialchars($value);
            }
        }
        return true;
    }
    public function add()
    {
        if($this->validate() !== true) {
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_TemplateElements", ["Template" => $this->Template, "Name" => $this->Name, "Type" => $this->Type, "Font" => $this->Font, "FontSize" => $this->FontSize, "FontStyle" => $this->FontStyle, "Align" => $this->Align, "Visible" => $this->Visible, "Page" => $this->Page, "X" => $this->X, "Y" => $this->Y, "Width" => $this->Width, "Height" => $this->Height, "Value" => $this->Value])->execute();
        if($result) {
            $this->Identifier = $result;
            return true;
        }
        return false;
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for template element");
            return false;
        }
        if($this->validate() !== true) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_TemplateElements", ["Template" => $this->Template, "Name" => $this->Name, "Type" => $this->Type, "Font" => $this->Font, "FontSize" => $this->FontSize, "FontStyle" => $this->FontStyle, "Align" => $this->Align, "Visible" => $this->Visible, "Page" => $this->Page, "X" => $this->X, "Y" => $this->Y, "Width" => $this->Width, "Height" => $this->Height, "Value" => $this->Value])->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for template element");
            return false;
        }
        $result = Database_Model::getInstance()->delete("HostFact_TemplateElements")->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function validate()
    {
        if(!(is_string($this->Name) && 3 <= strlen($this->Name) && strlen($this->Name) <= 100)) {
            $this->Error[] = __("template element invalid name");
        }
        if(!empty($this->Error)) {
            return false;
        }
        return true;
    }
    public function all($fields, $sort = false, $order = false, $limit = "-1", $searchat = false, $searchfor = false, $group = false)
    {
        $limit = !is_numeric($limit) ? -1 : $limit;
        if(!is_array($fields) || empty($fields)) {
            $this->Error[] = __("no fields given for retrieving data");
            return false;
        }
        $select = ["id"];
        foreach ($fields as $value) {
            $select[] = $value;
        }
        Database_Model::getInstance()->get("HostFact_TemplateElements", $select);
        if($searchat && in_array($searchat, $this->Variables)) {
            Database_Model::getInstance()->where($searchat, ["LIKE" => "%" . $searchfor . "%"]);
        }
        if($group) {
            Database_Model::getInstance()->where("Template", $group);
        }
        if($sort) {
            $order = in_array($order, ["ASC", "DESC"]) ? $order : "ASC";
            Database_Model::getInstance()->orderBy($sort, $order);
        }
        if(0 <= $limit) {
            $show_results = MAX_RESULTS_LIST;
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        $list = [];
        $list["CountRows"] = $this->CountRows = 0;
        if($template_list = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = $this->CountRows = Database_Model::getInstance()->rowCount("HostFact_TemplateElements", "id");
            foreach ($template_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $value) {
                    $list[$result->id][$value] = htmlspecialchars($result->{$value});
                }
            }
        }
        $list["CountRows"] = $this->CountRows;
        return $list;
    }
}
class emailtemplate
{
    public $Identifier;
    public $Name;
    public $CarbonCopy;
    public $BlindCarbonCopy;
    public $Sender;
    public $Subject;
    public $Message;
    public $Attachment;
    public $CountRows;
    public $Table;
    public $Error;
    public $Warning;
    public $Success;
    public $Variables = ["Identifier", "Name", "CarbonCopy", "BlindCarbonCopy", "Sender", "Subject", "Message", "Attachment"];
    public function __construct()
    {
        $this->Attachment = [];
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for emailtemplate");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_EmailTemplates")->where("id", $this->Identifier)->execute();
        if($result === false) {
            $this->Error[] = __("invalid identifier for emailtemplate");
            return false;
        }
        foreach ($result as $key => $value) {
            if($key != "id") {
                $this->{$key} = htmlspecialchars($value);
            }
        }
        if(!is_array($this->Attachment) && trim($this->Attachment)) {
            $this->Attachment = explode("|", $this->Attachment);
        } elseif(!is_array($this->Attachment)) {
            $this->Attachment = explode("|", $this->Attachment);
        }
        return true;
    }
    public function add()
    {
        if(is_array($this->Attachment)) {
            $attachment = "";
            foreach ($this->Attachment as $v) {
                $attachment .= "|" . $v;
            }
            $this->Attachment = substr($attachment, 1);
        }
        if($this->validate() === false) {
            if(!is_array($this->Attachment)) {
                $this->Attachment = explode("|", $this->Attachment);
            }
            return false;
        }
        $result = Database_Model::getInstance()->insert("HostFact_EmailTemplates", ["Name" => $this->Name, "CarbonCopy" => check_email_address($this->CarbonCopy, "convert"), "BlindCarbonCopy" => check_email_address($this->BlindCarbonCopy, "convert"), "Sender" => $this->Sender, "Subject" => $this->Subject, "Message" => $this->Message, "Attachment" => $this->Attachment])->execute();
        if($result) {
            $this->Identifier = $result;
            $this->Success[] = sprintf(__("emailtemplate created"), $this->Name);
            return true;
        }
        if(!is_array($this->Attachment)) {
            $this->Attachment = explode("|", $this->Attachment);
        }
        return false;
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for emailtemplate");
            return false;
        }
        if(is_array($this->Attachment)) {
            $attachment = "";
            foreach ($this->Attachment as $v) {
                $attachment .= "|" . $v;
            }
            $this->Attachment = substr($attachment, 1);
        }
        if($this->validate() === false) {
            if(!is_array($this->Attachment)) {
                $this->Attachment = explode("|", $this->Attachment);
            }
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_EmailTemplates", ["Name" => $this->Name, "CarbonCopy" => check_email_address($this->CarbonCopy, "convert"), "BlindCarbonCopy" => check_email_address($this->BlindCarbonCopy, "convert"), "Sender" => $this->Sender, "Subject" => $this->Subject, "Message" => $this->Message, "Attachment" => $this->Attachment])->where("id", $this->Identifier)->execute();
        if($result) {
            $this->Success[] = sprintf(__("emailtemplate adjusted"), $this->Name);
            return true;
        }
        if(!is_array($this->Attachment)) {
            $this->Attachment = explode("|", $this->Attachment);
        }
        return false;
    }
    public function delete()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for emailtemplate");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Templates", ["id"])->where("EmailTemplate", $this->Identifier)->execute();
        if($result) {
            $this->Error[] = __("emailtemplate in use for template");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Settings", ["id"])->where("Value", $this->Identifier)->where("Variable", ["IN" => ["PAYMENT_MAIL", "REMINDER_MAIL", "REMINDER_MAIL_SECOND", "SUMMATION_MAIL", "PERIODIC_REMINDER_MAIL", "CONTRACT_RENEW_CONFIRM_MAIL", "WELCOME_MAIL", "TICKET_NOTIFY_EMAIL_TEMPLATE", "TICKET_NOTIFY_CUSTOMER_EMAIL", "TICKET_REACTION_EMAIL_TEMPLATE"]])->execute();
        if($result) {
            $this->Error[] = __("emailtemplate in use for setting");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Debtors", ["id", "DebtorCode"])->where("Status", ["<" => 9])->orWhere([["ReminderTemplate", $this->Identifier], ["SecondReminderTemplate", $this->Identifier], ["SummationTemplate", $this->Identifier], ["PaymentMail", $this->Identifier]])->execute();
        if($result) {
            $this->Error[] = sprintf(__("emailtemplate in use for debtor setting"), $result->DebtorCode);
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Packages", ["id", "PackageName"])->where("Status", ["<" => 9])->where("EmailTemplate", $this->Identifier)->execute();
        if($result) {
            $this->Error[] = sprintf(__("emailtemplate in use for package"), $result->PackageName);
            return false;
        }
        Database_Model::getInstance()->delete("HostFact_EmailTemplates")->where("id", $this->Identifier)->execute();
        $this->Success[] = __("emailtemplate is deleted");
        return true;
    }
    public function TemplateClone()
    {
        $template_list = $this->all(["Name"]);
        $used_names = [];
        foreach ($template_list as $t) {
            $used_names[] = $t["Name"];
        }
        $this->show();
        $old_name = $this->Name;
        $i = 2;
        do {
            $this->Name = $old_name . "(" . $i . ")";
            $i++;
        } while (!in_array($this->Name, $used_names));
        foreach ($this->Variables as $key) {
            if(is_string($this->{$key})) {
                $this->{$key} = htmlspecialchars_decode($this->{$key});
            }
        }
        return $this->add();
    }
    public function readDir()
    {
        $dir = DIR_EMAIL_ATTACHMENTS;
        $pdf_source = [];
        if($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if($file != "." && $file != ".." && $file != "index.php" && file_exists($dir . $file)) {
                    $pdf_source[$dir . $file] = $file;
                }
            }
        }
        asort($pdf_source);
        return $pdf_source;
    }
    public function test_send($emailaddress = BCC_EMAILADDRESS)
    {
        require_once "class/email.php";
        $email = new email();
        $email->Recipient = $emailaddress;
        $email->Sender = $this->Sender;
        $email->Subject = $this->Subject;
        $email->Message = htmlspecialchars_decode($this->Message);
        $email->Attachment = $this->Attachment;
        $email->Sent_bcc = false;
        $email->Message = $email->Message;
        if($email->sent(false, false, false)) {
            $this->Success[] = sprintf(__("test email sent"), $emailaddress);
            return true;
        }
        $this->Error[] = sprintf(__("test email not sent"), $email->Error[0]);
        return false;
    }
    public function validate()
    {
        if(!(is_string($this->Name) && 3 < strlen($this->Name) && strlen($this->Name) <= 100)) {
            $this->Error[] = __("emailtemplate invalid name");
        }
        if(!empty($this->Error)) {
            return false;
        }
        return true;
    }
    public function all($fields, $sort = "Name", $order = false, $limit = "-1")
    {
        if(!is_array($fields) || empty($fields)) {
            $this->Error[] = __("no fields given for retrieving data");
            return false;
        }
        $select = ["id"];
        foreach ($fields as $value) {
            $select[] = $value;
        }
        Database_Model::getInstance()->get("HostFact_EmailTemplates", $select);
        if($sort) {
            $order = in_array($order, ["ASC", "DESC"]) ? $order : "ASC";
            Database_Model::getInstance()->orderBy($sort, $order);
        }
        if(0 <= $limit) {
            $show_results = MAX_RESULTS_LIST;
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        $list = [];
        $list["CountRows"] = 0;
        if($emailtemplate_list = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = Database_Model::getInstance()->rowCount("HostFact_EmailTemplates", "id");
            foreach ($emailtemplate_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    $list[$result->id][$column] = htmlspecialchars($result->{$column});
                }
            }
        }
        return $list;
    }
    public function getAllTemplatesBySender($sender)
    {
        $result = Database_Model::getInstance()->get("HostFact_EmailTemplates", ["COUNT(`id`) as Count", "Sender"])->groupBy("Sender")->execute();
        if(1 < count($result)) {
            foreach ($result as $key) {
                if($key->Sender == htmlspecialchars_decode($sender)) {
                    return $key->Count;
                }
            }
            return 0;
        } else {
            return 0;
        }
    }
}

?>