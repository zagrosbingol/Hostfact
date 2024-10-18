<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
define("JB_DEBUG", false);
require_once "3rdparty/vendor/autoload.php";
class pdfCreator
{
    public $template;
    public $vars;
    public $downloadInstead;
    public $OuputType;
    public $is_example_preview;
    public $Error;
    private $elements_translated = false;
    private $processed_lines = [];
    private $added_fonts = [];
    private $calledGetOpenAmount = false;
    private $calledGetFinancialInfo = false;
    private $line_discount_merged_amount = true;
    public function __construct($template, $vars = NULL, $type = "other", $outputtype = "D", $download_instead = false, $is_example_preview = false, $show_accepted_data = false)
    {
        require_once "class/templateblock.php";
        $block_model = new templateblock();
        $this->AvailableFonts = $block_model->getAvailableFonts();
        $this->setOutputType($outputtype);
        $this->downloadInstead($download_instead);
        $this->is_example_preview = $is_example_preview;
        $this->show_accepted_data = $show_accepted_data;
        $this->start_time = microtime(true);
        $this->Type = $type;
        $this->Error = [];
        if($this->loadTemplate($template) !== false) {
            $this->createPages($vars);
        }
    }
    public function downloadInstead($bool)
    {
        $this->downloadInstead = $bool;
    }
    public function setOutputType($OutputType = "D")
    {
        $this->OutputType = $OutputType;
    }
    public function loadTemplate($template = NULL)
    {
        if(!is_numeric($template)) {
            return false;
        }
        $this->calledGetOpenAmount = false;
        $this->calledGetFinancialInfo = false;
        $this->blocks = [];
        $this->document = Database_Model::getInstance()->getOne("HostFact_Templates", ["*"])->where("id", $template)->asArray()->execute();
        if(isset($this->document["PostLocation"]) && $this->document["PostLocation"] && @file_exists($this->document["PostLocation"]) === false) {
            $this->Error[] = sprintf(__("cannot generate pdf, stationery file no longer exists"), $this->document["PostLocation"]);
            return false;
        }
        if(isset($this->document["Location"]) && $this->document["Location"] && @file_exists($this->document["Location"]) === false) {
            $this->Error[] = sprintf(__("cannot generate pdf, stationery file no longer exists"), $this->document["Location"]);
            return false;
        }
        if(!$this->document || !isset($this->document["Type"])) {
            $template = isset($this->Type) && $this->Type == "pricequote" ? PRICEQUOTE_STD_TEMPLATE : INVOICE_STD_TEMPLATE;
            $this->document = Database_Model::getInstance()->getOne("HostFact_Templates", ["*"])->where("id", $template)->asArray()->execute();
            $template = $this->document["id"];
        }
        if(!isset($this->pdf)) {
            $this->pdf = new setasign\Fpdi\TcpdfFpdi("P", "mm", [PDF_PAGE_WIDTH, PDF_PAGE_HEIGHT]);
            $this->pdf->setPrintHeader(false);
            $this->pdf->setPrintFooter(false);
            $this->pdf->totalpages = 0;
            $this->pdf->Today = rewrite_date_db2site(date("Ymd"));
        }
        $this->pdf->startPageGroup();
        $this->processed_lines = [];
        $this->splitted_lines = [];
        $this->within_split = false;
        $this->pdf->SetCreator("HostFact B.V.");
        $this->pdf->SetAuthor($this->document["Author"]);
        $this->pdf->SetTitle($this->document["Title"]);
        $this->pdf->SetSubject($this->document["Title"]);
        $this->pdf->SetAutoPageBreak(false);
        if((!isset($this->downloadInstead) || $this->downloadInstead === false) && $this->OutputType == "D") {
            $this->document["Location"] = $this->document["PostLocation"] && @file_exists($this->document["PostLocation"]) ? $this->document["PostLocation"] : "";
        }
        $this->pagecount = $this->document["Location"] ? $this->pdf->setSourceFile($this->document["Location"]) : 0;
        $this->Name = $this->document["FileName"] ? strpos($this->document["FileName"], ".pdf") === false ? $this->document["FileName"] . ".pdf" : $this->document["FileName"] : $this->document["Title"] . ".pdf";
        require_once "class/templateblock.php";
        $block_model = new templateblock();
        $this->blocks = $block_model->listBlocks($template);
        $this->block_model = $block_model;
    }
    public function createPages($objects)
    {
        $this->objects = $objects;
        foreach ($this->objects as $objName => $object) {
            if(in_array($objName, ["debtor", "account", "invoice", "pricequote"])) {
                if(isset($object->EmailAddress)) {
                    $object->EmailAddress = check_email_address($object->EmailAddress, "convert", ", ");
                }
                if(isset($object->InvoiceEmailAddress)) {
                    $object->InvoiceEmailAddress = check_email_address(!empty($object->InvoiceEmailAddress) ? $object->InvoiceEmailAddress : $object->EmailAddress, "convert", ", ");
                }
                if(isset($object->ReminderEmailAddress)) {
                    $object->ReminderEmailAddress = check_email_address(!empty($object->ReminderEmailAddress) ? $object->ReminderEmailAddress : $object->InvoiceEmailAddress, "convert", ", ");
                }
            }
            $this->objects[$objName] = $object;
        }
        unset($objects);
        if(!isset($this->objects["invoice"]) && isset($this->objects["pricequote"])) {
            $this->objects["invoice"] = $this->objects["pricequote"];
        }
        if(isset($this->objects["invoice"]) && !$this->is_example_preview) {
            $this->objects["invoice"]->format(false);
        }
        if(!isset($this->objects["company"])) {
            global $company;
            $company->show();
            $this->objects["company"] = $company;
        }
        if(isset($this->objects["hosting"]->Username)) {
            $this->Name = str_replace("[hosting->Username]", $this->objects["hosting"]->Username, $this->Name);
        } elseif(isset($this->objects["invoice"]->InvoiceCode)) {
            $this->Name = str_replace("[invoice->InvoiceCode]", $this->objects["invoice"]->InvoiceCode, $this->Name);
            if($this->document["Type"] != "other" && strpos($this->Name, $this->objects["invoice"]->InvoiceCode) === false) {
                $this->Name = sprintf(__("pdf default invoice filename"), $this->objects["invoice"]->InvoiceCode);
            }
        } elseif($this->document["Type"] != "other" && isset($this->objects["invoice"]->PriceQuoteCode)) {
            $this->Name = str_replace("[invoice->PriceQuoteCode]", $this->objects["invoice"]->PriceQuoteCode, $this->Name);
            $this->Name = str_replace("[pricequote->PriceQuoteCode]", $this->objects["invoice"]->PriceQuoteCode, $this->Name);
            if(strpos($this->Name, $this->objects["invoice"]->PriceQuoteCode) === false) {
                $this->Name = sprintf(__("pdf default pricequote filename"), $this->objects["invoice"]->PriceQuoteCode);
            }
        }
        if(isset($this->document["Type"]) && $this->document["Type"] == "pricequote") {
            require_once "class/pricequote.php";
        } else {
            require_once "class/invoice.php";
        }
        if(isset($this->objects["invoice"]->Discount) && 0 < $this->objects["invoice"]->Discount) {
            $element = $this->document["Type"] == "pricequote" ? new pricequoteelement() : new invoiceelement();
            $this->objects["invoice"]->Elements["discount"] = [];
            foreach ($element->Variables as $v) {
                $this->objects["invoice"]->Elements["discount"][$v] = "";
            }
            $this->objects["invoice"]->Elements["discount"]["id"] = "discount";
            $this->objects["invoice"]->Elements["discount"]["ProductName"] = "";
            $this->objects["invoice"]->Elements["discount"]["Description"] = $this->document["Type"] == "pricequote" ? sprintf(__("pdf discount on pricequote"), showNumber($this->objects["invoice"]->Discount)) : sprintf(__("pdf discount on invoice"), showNumber($this->objects["invoice"]->Discount));
            $this->objects["invoice"]->Elements["discount"]["PriceExcl"] = str_replace(" ", "", $this->objects["invoice"]->AmountDiscount);
            $this->objects["invoice"]->Elements["discount"]["PriceIncl"] = str_replace(" ", "", $this->objects["invoice"]->AmountDiscountIncl);
            $this->objects["invoice"]->Elements["discount"]["AmountExcl"] = str_replace(" ", "", $this->objects["invoice"]->AmountDiscount);
            $this->objects["invoice"]->Elements["discount"]["AmountIncl"] = str_replace(" ", "", $this->objects["invoice"]->AmountDiscountIncl);
            $this->objects["invoice"]->Elements["discount"]["NoDiscountAmountExcl"] = $this->objects["invoice"]->Elements["discount"]["AmountExcl"];
            $this->objects["invoice"]->Elements["discount"]["NoDiscountAmountIncl"] = $this->objects["invoice"]->Elements["discount"]["AmountIncl"];
            $this->objects["invoice"]->Elements["discount"]["DiscountAmountExcl"] = "";
            $this->objects["invoice"]->Elements["discount"]["DiscountAmountIncl"] = "";
            $this->objects["invoice"]->Elements["discount"]["FullTaxPercentage"] = "";
            $this->objects["invoice"]->Elements["discount"]["TaxPercentage"] = $this->objects["invoice"]->Elements["discount"]["FullTaxPercentage"];
            $this->objects["invoice"]->Elements["discount"]["FullDiscountPercentage"] = "";
            $this->objects["invoice"]->Elements["discount"]["DiscountPercentage"] = $this->objects["invoice"]->Elements["discount"]["FullDiscountPercentage"];
        }
        $this->clone = 0;
        $this->create_page();
    }
    private function create_page()
    {
        if(JB_DEBUG) {
            echo "<h1>Nieuwe pagina</h1>";
            echo "<hr />TIJD: " . (microtime(true) - $this->start_time) . " sec<br />";
        }
        $this->pdf->addPage();
        $create_another_page = false;
        $this->invoicelines_completed = true;
        $this->pagetotals["PriceExcl"] = 0;
        $this->pagetotals["PriceIncl"] = 0;
        if($this->document["Location"] && 0 < $this->pagecount) {
            if($this->pagecount < $this->pdf->getGroupPageNo()) {
                $template_ref = $this->pdf->ImportPage($this->pagecount);
            } else {
                $template_ref = $this->pdf->ImportPage($this->pdf->getGroupPageNo());
            }
            $template_size = $this->pdf->getTemplateSize($template_ref);
            if(round((double) $template_size["width"], 2) != PDF_PAGE_WIDTH || round((double) $template_size["height"], 2) != PDF_PAGE_HEIGHT) {
                $_x = (PDF_PAGE_WIDTH - round((double) $template_size["width"], 2)) / 2;
                $_y = (PDF_PAGE_HEIGHT - round((double) $template_size["height"], 2)) / 2;
                $this->pdf->useTemplate($template_ref, $_x, $_y, $template_size["width"]);
            } else {
                $this->pdf->useTemplate($template_ref, 0, 0);
            }
            $this->pdf->setPageMark();
        }
        foreach ($this->blocks as $block) {
            if($block["visibility"] == "none") {
            } elseif($block["type"] == "invoicelines") {
                if(!isset($block["elements"])) {
                    $block["elements"] = [];
                    foreach ($this->objects["invoice"]->Elements as $k => $element) {
                        if(is_numeric($k) || $k == "discount") {
                            $element["key"] = $k;
                            $block["elements"][] = $element;
                            $block["value"][] = $block["value"][1];
                        }
                    }
                    unset($block["value"][count($block["value"]) - 1]);
                }
                $this->create_table($block);
                $create_another_page = $this->invoicelines_completed === false ? true : false;
            }
        }
        foreach ($this->blocks as $block) {
            if($block["visibility"] == "none" || $block["visibility"] == "first" && $this->pdf->getGroupPageNo() != 1 || $block["visibility"] == "last" && $create_another_page !== false) {
            } else {
                switch ($block["type"]) {
                    case "image":
                        $this->create_image($block);
                        break;
                    case "table":
                    case "totals":
                        $this->create_table($block);
                        break;
                    case "invoicelines":
                    case templateblock::BLOCK_TYPE_QR_CODE:
                        $this->createQrCodeBlock($block);
                        break;
                    default:
                        $this->create_block($block);
                }
            }
        }
        if(isset($this->show_accepted_data) && $this->show_accepted_data === true) {
            $this->pdf->SetAlpha(0);
            $this->pdf->Rect(0, 0, 210, 50, "F", [], [255, 255, 255]);
            $this->pdf->SetAlpha(1);
            $this->pdf->SetFont("helvetica", "", 10);
            $this->pdf->setColor("text", 65, 117, 5);
            $accept_html = "<table cellspacing=\"3\">";
            $accept_html .= "<tr><td><strong>" . sprintf(__("pdf accepted pricequote - accepted online"), rewrite_date_db2site($this->objects["pricequote"]->AcceptDate, "%d-%m-%Y"), rewrite_date_db2site($this->objects["pricequote"]->AcceptDate, "%H:%i")) . "</strong></td> </tr>";
            $accept_html .= "<tr><td>" . $this->objects["pricequote"]->AcceptName . "</td></tr>";
            $accept_html .= "<tr><td>" . check_email_address($this->objects["pricequote"]->AcceptEmailAddress, "convert", ", ") . "</td></tr>";
            $accept_html .= "<tr><td>" . __("pdf accepted pricequote - ipaddress") . ": " . $this->objects["pricequote"]->AcceptIPAddress . "</td></tr>";
            $accept_html .= "</table>";
            $this->pdf->writeHTMLCell(90, 5, 20, 15, $accept_html, 0, $ln = 1, false, $reseth = true, "left", $autopadding = true);
            if($this->objects["pricequote"]->AcceptSignatureBase64) {
                $this->pdf->ImageSVG("@" . base64_decode($this->objects["pricequote"]->AcceptSignatureBase64), 110, 20, "", 20, "", $align = "", $palign = "", $border = 0, $fitonpage = false);
            }
        }
        if($create_another_page === true) {
            return $this->create_page();
        }
    }
    public function generatePDF($OutputType = "D")
    {
        if(!empty($this->Error)) {
            return false;
        }
        if(!is_writable("temp/")) {
            $pro_map_name = software_get_relative_path();
            $pro_map_name .= "temp/";
            $this->Error[] = sprintf(__("cannot generate pdf, no folder rights"), $pro_map_name);
            return false;
        }
        $this->setOutputType($OutputType);
        $this->pdf->SetDisplayMode("real");
        if(JB_DEBUG) {
            echo "<hr />TIJD: " . (microtime(true) - $this->start_time) . " sec<br />";
            echo "GEHEUGEN: " . memory_get_peak_usage() / 1024 / 1024 . " MB";
            exit;
        }
        $this->Name = preg_replace("/[\\/:*?|\\\\]/i", "", htmlspecialchars_decode($this->Name));
        if(file_exists(realpath("temp") . "/" . $this->Name)) {
            @unlink("temp/" . $this->Name);
        }
        if($OutputType == "I") {
            $this->pdf->Output(realpath("temp") . "/" . $this->Name, $OutputType);
        } elseif($OutputType == "D" || $OutputType == "F") {
            $this->pdf->Output(realpath("temp") . "/" . $this->Name, "F");
            $filename = realpath("temp") . "/" . $this->Name;
            if($OutputType != "F") {
                header("Cache-Control: public, must-revalidate");
                header("Pragma: hack");
                header("Content-Type: application/pdf");
                header("Content-Length: " . filesize($filename));
                header("Content-Disposition: attachment; filename=\"" . $this->Name . "\"");
                header("Content-Transfer-Encoding: binary");
                $fp = fopen($filename, "rb");
                $buffer = fread($fp, filesize($filename));
                fclose($fp);
                echo $buffer;
                @unlink($filename);
                exit;
            }
        }
        return true;
    }
    private function create_table($block)
    {
        if(JB_DEBUG) {
            echo "<hr />Table s: " . (microtime(true) - $this->start_time) . " sec<br />";
        }
        if($block["text"]["family"] != "" && !array_key_exists($block["text"]["family"], $this->AvailableFonts["custom"]) && !array_key_exists($block["text"]["family"], $this->AvailableFonts["default"])) {
            $block["text"]["family"] = "helvetica";
        }
        $x_pointer = $block["positioning"]["x"];
        $y_pointer = $block["positioning"]["y"];
        $table_width = $block["positioning"]["w"];
        if($block["type"] == "invoicelines" && isEmptyFloat($block["positioning"]["h"])) {
            $block["positioning"]["h"] = 100;
        }
        $last_col = 0;
        foreach ($block["cols"] as $col_id => $tmp_col) {
            $colWidth = isset($tmp_col["positioning"]["w"]) ? (double) $tmp_col["positioning"]["w"] : 0;
            $table_width -= $colWidth;
            if(isEmptyFloat($colWidth)) {
                $last_col = $col_id;
            }
        }
        if(0 < $table_width) {
            $block["cols"][$last_col]["positioning"]["w"] = $table_width;
        }
        $this->pdf->SetXY($x_pointer, $y_pointer);
        $multi_row_zebra_offset = 0;
        $border_row_id = 0;
        $invoicelines_on_this_page = 0;
        if($block["type"] == "totals") {
            $has_page_totals_in_block = false;
            foreach ($block["value"] as $row_id => $row) {
                if(isset($block["rows"][$row_id]["style"]["totaltype"]) && in_array($block["rows"][$row_id]["style"]["totaltype"], ["pagetotalexcl", "pagetotalincl"])) {
                    $has_page_totals_in_block = true;
                }
            }
            if($has_page_totals_in_block === true && $this->invoicelines_completed === false) {
                foreach ($block["value"] as $row_id => $row) {
                    if(!isset($block["rows"][$row_id]["style"]["totaltype"]) || !in_array($block["rows"][$row_id]["style"]["totaltype"], ["pagetotalexcl", "pagetotalincl"])) {
                        unset($block["value"][$row_id]);
                    }
                }
            }
        }
        foreach ($block["value"] as $row_id => $row) {
            if($block["type"] == "invoicelines" && 1 <= $row_id && in_array($row_id, $this->processed_lines)) {
            } else {
                $original_row_id = $row_id;
                $loop_rows = [$row];
                $loop_row_prevent_borders = false;
                if(1 < $row_id && $block["type"] == "invoicelines") {
                    $row_id = 1;
                }
                $y_start = $this->pdf->GetY();
                $x_start = $x_pointer;
                $need_a_clone = true;
                if($need_a_clone === true) {
                    $this->clone++;
                    if(JB_DEBUG) {
                        echo "<hr />Clone " . $this->clone . ": " . (microtime(true) - $this->start_time) . " sec<br />";
                    }
                    $clone_pdf = clone $this->pdf;
                    self::unset_cached_files($clone_pdf);
                    self::set_cloned_file_id($clone_pdf);
                }
                $loop_twice = $need_a_clone === false ? 1 : 0;
                while ($loop_twice <= 1) {
                    if($loop_twice === 0) {
                        if(isset($invoiceElement)) {
                            unset($invoiceElement);
                        }
                    } elseif($loop_twice === 1 && $block["type"] == "invoicelines" && $block["positioning"]["y"] + $block["positioning"]["h"] < $y_pointer) {
                        if(1 <= $invoicelines_on_this_page) {
                            $this->invoicelines_completed = false;
                            if(JB_DEBUG) {
                                echo "<hr />Table e: " . (microtime(true) - $this->start_time) . " sec<br />";
                            }
                            return NULL;
                        }
                    } elseif($loop_twice == 1 && $block["type"] == "invoicelines") {
                        if(0 < $original_row_id && !isset($this->splitted_lines[$original_row_id])) {
                            $this->processed_lines[] = $original_row_id;
                        }
                        if(1 <= $row_id) {
                            $invoicelines_on_this_page++;
                        }
                    }
                    if($block["type"] == "totals" && isset($block["rows"][$original_row_id]["style"]["totaltype"])) {
                        switch ($block["rows"][$original_row_id]["style"]["totaltype"]) {
                            case "pagetotalexcl":
                            case "pagetotalincl":
                                if($this->pdf->getGroupPageNo() == 1 && $this->invoicelines_completed !== false) {
                                }
                                break;
                            case "amounttax":
                                $loop_rows = [];
                                if(isset($this->objects["invoice"]->used_taxrates) && is_array($this->objects["invoice"]->used_taxrates)) {
                                    foreach ($this->objects["invoice"]->used_taxrates as $tax_rate => $tax_info) {
                                        if(!isEmptyFloat($tax_rate)) {
                                            global $array_taxpercentages_info;
                                            $loop_rows[] = [isset($array_taxpercentages_info[(string) (double) $tax_rate]["label"]) ? $array_taxpercentages_info[(string) (double) $tax_rate]["label"] : "", $tax_info["AmountTax"]];
                                        }
                                    }
                                }
                                if(isset($this->objects["invoice"]->TaxRate) && 0 < $this->objects["invoice"]->TaxRate) {
                                    $loop_rows[] = [$this->objects["invoice"]->TaxRate_Label, $this->objects["invoice"]->TaxRate_Amount];
                                }
                                break;
                        }
                    }
                    if($block["type"] == "invoicelines" && 0 < $original_row_id) {
                        $invoiceElement = $this->document["Type"] == "pricequote" ? new pricequoteelement() : new invoiceelement();
                        $element_value = isset($block["elements"][$original_row_id - 1]) && is_array($block["elements"][$original_row_id - 1]) ? $block["elements"][$original_row_id - 1] : [];
                        if(JB_DEBUG) {
                            echo "<hr />Table IL-a: " . (microtime(true) - $this->start_time) . " sec<br />";
                        }
                        if(!$this->is_example_preview) {
                            if($element_value["id"] != "0" && is_numeric($element_value["id"])) {
                                $invoiceElement->Identifier = $element_value["id"];
                                $invoiceElement->show();
                                $invoiceElement->format();
                                if(0 < $invoiceElement->DiscountPercentage && strpos(implode(";", $loop_rows[0]), "[linediscount]") === false) {
                                    $loop_rows = [];
                                    $loop_rows[] = $row;
                                    $loop_rows[] = $row;
                                    $loop_row_prevent_borders = true;
                                }
                            } elseif($element_value["id"] == "discount") {
                                foreach ($element_value as $k => $v) {
                                    $invoiceElement->{$k} = $v;
                                }
                                $invoiceElement->Description = $invoiceElement->Description;
                                $invoiceElement->PeriodPriceExcl = $invoiceElement->PriceExcl;
                                $loop_rows = [];
                                $loop_rows[] = array_fill(0, count($row), "");
                                $loop_rows[] = $row;
                            }
                            if(isset($this->within_split) && $this->within_split === true && isset($this->splitted_lines[$original_row_id])) {
                                $invoiceElement->ProductCode = "";
                                $invoiceElement->PriceExcl = 0;
                                $invoiceElement->PriceIncl = 0;
                                $element_value["Number"] = 0;
                            }
                        } else {
                            foreach ($element_value as $k => $v) {
                                $invoiceElement->{$k} = $v;
                            }
                            if(0 < $invoiceElement->DiscountPercentage && strpos(implode(";", $loop_rows[0]), "[linediscount]") === false) {
                                $loop_rows = [];
                                $loop_rows[] = $row;
                                $loop_rows[] = $row;
                                $loop_row_prevent_borders = true;
                            }
                        }
                        if(JB_DEBUG) {
                            echo "<hr />Table IL-b: " . (microtime(true) - $this->start_time) . " sec<br />";
                        }
                    }
                    if($loop_twice == 1 && $need_a_clone === true) {
                        $x_start = $x_pointer;
                        if(isset($element_value["id"])) {
                            if($element_value["id"] == "discount") {
                                $element_value["Number"] = 1;
                                $element_value["Periods"] = 1;
                                $element_value["PriceExcl"] = deformat_money($element_value["PriceExcl"]);
                                $element_value["PriceIncl"] = deformat_money($element_value["PriceIncl"]);
                            }
                            if($this->within_split === false) {
                                $this->pagetotals["PriceExcl"] += $element_value["id"] == "discount" ? $element_value["PriceExcl"] : round((double) $element_value["AmountExcl"], 2);
                                $this->pagetotals["PriceIncl"] += $element_value["id"] == "discount" ? $element_value["PriceIncl"] : round((double) $element_value["AmountIncl"], 2);
                            }
                        }
                    }
                    $multi_row_xy = ["x" => $x_start, "y" => $y_start];
                    $y_start_row = $y_start;
                    $cell_height_helper = $y_pointer;
                    if($loop_twice === 1 && 1 < count($loop_rows)) {
                        $cell_height_helper = $y_start_row;
                    }
                    if($loop_twice === 0) {
                        $loop_row_y_pointers = [];
                    }
                    foreach ($loop_rows as $tmp_row_id => $tmp_row) {
                        $x_start = $x_pointer;
                        $y_start_row = $multi_row_xy["y"];
                        if(0 < $tmp_row_id || $block["type"] == "invoicelines") {
                            $cell_height_helper = isset($loop_row_y_pointers[$tmp_row_id]) ? $loop_row_y_pointers[$tmp_row_id] : $y_pointer;
                        }
                        if($loop_twice === 1 && 0 < $tmp_row_id && $loop_row_prevent_borders === false) {
                            $multi_row_zebra_offset++;
                        }
                        $zebra_odd_even = ($original_row_id + $multi_row_zebra_offset) % 2 ? "odd" : "even";
                        if($loop_twice == 1) {
                            $border_row_id++;
                        }
                        $get_y = 0;
                        foreach ($tmp_row as $col_id => $value) {
                            $cell_x = $multi_row_xy["x"];
                            $cell_y = $multi_row_xy["y"];
                            $cell_width = isset($block["cols"][$col_id]["positioning"]["w"]) && $block["cols"][$col_id]["positioning"]["w"] ? $block["cols"][$col_id]["positioning"]["w"] : 0;
                            if(isset($block["rows"][$row_id]["text"]["family"]) && $block["rows"][$row_id]["text"]["family"] != "" && !array_key_exists($block["rows"][$row_id]["text"]["family"], $this->AvailableFonts["custom"]) && !array_key_exists($block["rows"][$row_id]["text"]["family"], $this->AvailableFonts["default"])) {
                                $cell_font = "helvetica";
                            } else {
                                $cell_font = isset($block["rows"][$row_id]["text"]["family"]) && $block["rows"][$row_id]["text"]["family"] ? $block["rows"][$row_id]["text"]["family"] : (isset($block["cols"][$col_id]["text"]["family"]) && $block["cols"][$col_id]["text"]["family"] ? $block["cols"][$col_id]["text"]["family"] : (isset($block["text"]["family"]) && $block["text"]["family"] ? $block["text"]["family"] : "helvetica"));
                            }
                            $cell_font_size = isset($block["rows"][$row_id]["text"]["size"]) && $block["rows"][$row_id]["text"]["size"] ? $block["rows"][$row_id]["text"]["size"] : (isset($block["cols"][$col_id]["text"]["size"]) && $block["cols"][$col_id]["text"]["size"] ? $block["cols"][$col_id]["text"]["size"] : (isset($block["text"]["size"]) && $block["text"]["size"] ? $block["text"]["size"] : "10"));
                            $cell_font_color = $this->htmlcolor2rgb(isset($block["rows"][$row_id]["text"]["color"]) && $block["rows"][$row_id]["text"]["color"] ? $block["rows"][$row_id]["text"]["color"] : (isset($block["cols"][$col_id]["text"]["color"]) && $block["cols"][$col_id]["text"]["color"] ? $block["cols"][$col_id]["text"]["color"] : (isset($block["text"]["color"]) && $block["text"]["color"] ? $block["text"]["color"] : "")));
                            $cell_align = isset($block["rows"][$row_id]["text"]["align"]) && $block["rows"][$row_id]["text"]["align"] ? $block["rows"][$row_id]["text"]["align"] : (isset($block["cols"][$col_id]["text"]["align"]) && $block["cols"][$col_id]["text"]["align"] ? $block["cols"][$col_id]["text"]["align"] : (isset($block["text"]["align"]) && $block["text"]["align"] ? $block["text"]["align"] : "left"));
                            $cell_line_height = $block["type"] == "invoicelines" && $block["rows"][$row_id]["text"]["lineheight"] ? $block["rows"][$row_id]["text"]["lineheight"] : ($block["text"]["lineheight"] ? $block["text"]["lineheight"] : "1.25");
                            $cell_font_style = isset($block["rows"][$row_id]["text"]["style"]) && $block["rows"][$row_id]["text"]["style"] ? $block["rows"][$row_id]["text"]["style"] : (isset($block["cols"][$col_id]["text"]["style"]) && $block["cols"][$col_id]["text"]["style"] ? $block["cols"][$col_id]["text"]["style"] : (isset($block["text"]["style"]) && $block["text"]["style"] ? $block["text"]["style"] : ""));
                            $cell_border = "";
                            $cell_border .= ($loop_row_prevent_borders === false || $loop_row_prevent_borders === true && $tmp_row_id === 0) && (isset($block["borders"]["top"]) && $block["borders"]["top"] == "yes" && $border_row_id == 1 || isset($block["rows"][$row_id]["borders"]["top"]) && $block["rows"][$row_id]["borders"]["top"] == "yes" || isset($block["cols"][$col_id]["borders"]["top"]) && $block["cols"][$col_id]["borders"]["top"] == "yes") ? "T" : "";
                            $cell_border .= isset($block["borders"]["right"]) && $block["borders"]["right"] == "yes" && $col_id === count($tmp_row) - 1 || isset($block["rows"][$row_id]["borders"]["right"]) && $block["rows"][$row_id]["borders"]["right"] == "yes" && $col_id == count($tmp_row) - 1 || isset($block["cols"][$col_id]["borders"]["right"]) && $block["cols"][$col_id]["borders"]["right"] == "yes" ? "R" : "";
                            $cell_border .= ($loop_row_prevent_borders === false || $loop_row_prevent_borders === true && $tmp_row_id === count($loop_rows) - 1) && (isset($block["borders"]["bottom"]) && $block["borders"]["bottom"] == "yes" && $original_row_id == count($block["value"]) - 1 || isset($block["rows"][$row_id]["borders"]["bottom"]) && $block["rows"][$row_id]["borders"]["bottom"] == "yes" || isset($block["cols"][$col_id]["borders"]["bottom"]) && $block["cols"][$col_id]["borders"]["bottom"] == "yes") ? "B" : "";
                            $cell_border .= isset($block["borders"]["left"]) && $block["borders"]["left"] == "yes" && $col_id === 0 || isset($block["rows"][$row_id]["borders"]["left"]) && $block["rows"][$row_id]["borders"]["left"] == "yes" && $col_id === 0 || isset($block["cols"][$col_id]["borders"]["left"]) && $block["cols"][$col_id]["borders"]["left"] == "yes" ? "L" : "";
                            if($block["borders"]["top"] == "yes" || $block["borders"]["right"] == "yes" || $block["borders"]["bottom"] == "yes" || $block["borders"]["left"] == "yes") {
                                $cell_border_thickness = $block["borders"]["thickness"];
                                $cell_border_type = $block["borders"]["type"];
                                $cell_border_color = $block["borders"]["color"];
                            } elseif(isset($block["rows"][$row_id]["borders"]["top"]) && $block["rows"][$row_id]["borders"]["top"] == "yes" || isset($block["rows"][$row_id]["borders"]["right"]) && $block["rows"][$row_id]["borders"]["right"] == "yes" || isset($block["rows"][$row_id]["borders"]["bottom"]) && $block["rows"][$row_id]["borders"]["bottom"] == "yes" || isset($block["rows"][$row_id]["borders"]["left"]) && $block["rows"][$row_id]["borders"]["left"] == "yes") {
                                $cell_border_thickness = isset($block["rows"][$row_id]["borders"]["thickness"]) ? $block["rows"][$row_id]["borders"]["thickness"] : $block["borders"]["thickness"];
                                $cell_border_type = isset($block["rows"][$row_id]["borders"]["type"]) ? $block["rows"][$row_id]["borders"]["type"] : $block["borders"]["type"];
                                $cell_border_color = isset($block["rows"][$row_id]["borders"]["color"]) ? $block["rows"][$row_id]["borders"]["color"] : $block["borders"]["color"];
                            } elseif(isset($block["cols"][$col_id]["borders"]["top"]) && $block["cols"][$col_id]["borders"]["top"] == "yes" || isset($block["cols"][$col_id]["borders"]["right"]) && $block["cols"][$col_id]["borders"]["right"] == "yes" || isset($block["cols"][$col_id]["borders"]["bottom"]) && $block["cols"][$col_id]["borders"]["bottom"] == "yes" || isset($block["cols"][$col_id]["borders"]["left"]) && $block["cols"][$col_id]["borders"]["left"] == "yes") {
                                $cell_border_thickness = isset($block["cols"][$col_id]["borders"]["thickness"]) ? $block["cols"][$col_id]["borders"]["thickness"] : $block["borders"]["thickness"];
                                $cell_border_type = isset($block["cols"][$col_id]["borders"]["type"]) ? $block["cols"][$col_id]["borders"]["type"] : $block["borders"]["type"];
                                $cell_border_color = isset($block["cols"][$col_id]["borders"]["color"]) ? $block["cols"][$col_id]["borders"]["color"] : $block["borders"]["color"];
                            }
                            $cell_background_row = isset($block["rows"][$row_id]["style"]["bgcolor"]) && $block["rows"][$row_id]["style"]["bgcolor"] || $zebra_odd_even == "even" && isset($block["rows"][$row_id]["style"]["bgcolor_even"]) && $block["rows"][$row_id]["style"]["bgcolor_even"] ? $zebra_odd_even == "even" && isset($block["rows"][$row_id]["style"]["bgcolor_even"]) && $block["rows"][$row_id]["style"]["bgcolor_even"] ? $block["rows"][$row_id]["style"]["bgcolor_even"] : $block["rows"][$row_id]["style"]["bgcolor"] : "";
                            $cell_background_col = $cell_background_row ? $cell_background_row : (isset($block["cols"][$col_id]["style"]["bgcolor"]) && $block["cols"][$col_id]["style"]["bgcolor"] ? $block["cols"][$col_id]["style"]["bgcolor"] : "");
                            $pdf_to_handle = $loop_twice === 0 ? $clone_pdf : $this->pdf;
                            $cell_align = strtoupper(substr($cell_align, 0, 1));
                            $pdf_to_handle->setCellHeightRatio(max(1, $cell_line_height - 0));
                            $includeSubset = in_array($cell_font, ["helvetica", "freeserif"]) ? "default" : false;
                            $pdf_to_handle->SetFont($cell_font, $cell_font_style, $cell_font_size, "", $includeSubset);
                            $pdf_to_handle->SetTextColor($cell_font_color[0], $cell_font_color[1], $cell_font_color[2]);
                            if($cell_background_col) {
                                $cell_fill = true;
                                $cell_bgcolors = $this->htmlcolor2rgb($cell_background_col);
                                $pdf_to_handle->SetFillColor($cell_bgcolors[0], $cell_bgcolors[1], $cell_bgcolors[2]);
                            } else {
                                $cell_fill = false;
                            }
                            $cell_border = $cell_border == "" ? 0 : [$cell_border => ["width" => $this->block_model->px_to_mm($cell_border_thickness, false), "cap" => "butt", "join" => "bevel", "dash" => $cell_border_type == "solid" ? "0" : "1", "color" => $this->htmlcolor2rgb($cell_border_color)]];
                            if($block["type"] == "invoicelines" && 0 < $row_id && isset($invoiceElement->ProductType) && $invoiceElement->ProductType != "other" && isset($block["rows"][$row_id]["additional_description"][$invoiceElement->ProductType]) && $block["rows"][$row_id]["additional_description"][$invoiceElement->ProductType]) {
                                if(!$this->is_example_preview) {
                                    require_once "class/service.php";
                                    $service = new service();
                                    if($service->show($invoiceElement->Reference, $invoiceElement->ProductType) && isset($service->{$invoiceElement->ProductType})) {
                                        $invoiceElement->ProductTypeObject = $service->{$invoiceElement->ProductType};
                                    }
                                }
                                if(isset($invoiceElement->ProductTypeObject)) {
                                    $additional_description = str_replace("[" . $invoiceElement->ProductType . "->", "[invoiceElement->ProductTypeObject->", $block["rows"][$row_id]["additional_description"][$invoiceElement->ProductType]);
                                    $value = str_replace("[invoiceElement->Description]", "[invoiceElement->Description] " . $additional_description, $value);
                                }
                            }
                            if($block["type"] == "invoicelines" && 0 < $row_id) {
                                $html = $this->translate_variables($value, $invoiceElement, $tmp_row_id);
                                if(isset($invoiceElement->ProductType)) {
                                    $html = str_replace("[invoiceElement->ProductTypeObject->", "[" . $invoiceElement->ProductType . "->", $html);
                                }
                            } else {
                                $html = $this->translate_variables($value);
                            }
                            if($loop_twice === 0 && $block["type"] == "invoicelines" && 0 < $row_id) {
                                if(isset($this->within_split) && $this->within_split === true) {
                                    if(!isset($this->splitted_lines[$original_row_id][$col_id])) {
                                        $html = "";
                                    } else {
                                        $this->splitted_lines[$original_row_id][$col_id]["from"] = $this->splitted_lines[$original_row_id][$col_id]["from"] + $this->splitted_lines[$original_row_id][$col_id]["length"];
                                        $this->splitted_lines[$original_row_id][$col_id]["length"] = 0;
                                        $html = str_replace("<br />", " <br/>WFREMOVEWF ", $html);
                                        $html = trim(implode(" ", array_slice(explode(" ", $html), $this->splitted_lines[$original_row_id][$col_id]["from"])));
                                        $html = trim(str_replace("WFREMOVEWF", "", $html));
                                        while (substr($html, 0, 5) == "<br/>") {
                                            $html = substr($html, 5);
                                        }
                                    }
                                }
                            } elseif($loop_twice === 1 && $block["type"] == "invoicelines" && 0 < $row_id && isset($this->within_split) && $this->within_split === true) {
                                $this->within_split = false;
                            }
                            if($loop_twice === 0) {
                                $clone_pdf->writeHTMLCell($cell_width, $h = 0, $cell_x, $cell_y, $html, $cell_border, $ln = 1, $cell_fill, $reseth = true, $cell_align, $autopadding = true);
                                $x_start = $x_start + $cell_width;
                                $y_pointer = $y_pointer < $clone_pdf->GetY() ? $clone_pdf->GetY() : $y_pointer;
                                $get_y = $clone_pdf->GetY();
                                $clone_pdf->SetY($y_start_row);
                                if($block["type"] == "invoicelines" && $block["positioning"]["y"] + $block["positioning"]["h"] < $y_pointer && $block["positioning"]["y"] + $block["positioning"]["h"] < $y_pointer) {
                                    if(!isset($this->splitted_lines[$original_row_id][$col_id]["from"])) {
                                        $this->splitted_lines[$original_row_id][$col_id]["from"] = 0;
                                        $html = str_replace("<br />", " <br/>WFREMOVEWF ", $html);
                                    }
                                    $html_words = explode(" ", $html);
                                    $new_html = $html_words[0];
                                    $_word_count = 1;
                                    $_word_incrementer = 1;
                                    for ($y_pointer = 0; $y_pointer < $block["positioning"]["y"] + $block["positioning"]["h"] && $_word_count <= count($html_words); $loop++) {
                                        if(0 < $y_pointer && 40 < $block["positioning"]["y"] + $block["positioning"]["h"] - $y_pointer) {
                                            $_word_incrementer = 50;
                                        } elseif(0 < $y_pointer && 20 < $block["positioning"]["y"] + $block["positioning"]["h"] - $y_pointer) {
                                            $_word_incrementer = 25;
                                        } elseif(0 < $y_pointer && 10 < $block["positioning"]["y"] + $block["positioning"]["h"] - $y_pointer) {
                                            $_word_incrementer = 10;
                                        } else {
                                            $_word_incrementer = 1;
                                        }
                                        $_word_count += $_word_incrementer;
                                        $new_html = implode(" ", array_slice($html_words, 0, $_word_count));
                                        $clone_pdf->SetY($y_start_row);
                                        $clone_pdf->writeHTMLCell($cell_width, $h = 0, $cell_x, $cell_y, $new_html, $cell_border, $ln = 1, $cell_fill, $reseth = true, $cell_align, $autopadding = true);
                                        $y_pointer = $clone_pdf->GetY();
                                        if($y_pointer < $block["positioning"]["y"] + $block["positioning"]["h"]) {
                                            $get_y = $y_pointer;
                                        } elseif(1 < $_word_incrementer && $block["positioning"]["y"] + $block["positioning"]["h"] < $y_pointer) {
                                            $_word_count -= 10;
                                            $y_pointer = 0;
                                        }
                                    }
                                    $y_pointer = $get_y;
                                    if(1 < $_word_count - 1) {
                                        $this->splitted_lines[$original_row_id][$col_id]["length"] = $_word_count - 1;
                                    }
                                }
                            } else {
                                if($block["type"] == "invoicelines" && isset($this->splitted_lines[$original_row_id][$col_id])) {
                                    if(0 < $this->splitted_lines[$original_row_id][$col_id]["length"]) {
                                        $html = str_replace("<br />", " <br/>WFREMOVEWF ", $html);
                                        $html = trim(implode(" ", array_slice(explode(" ", $html), $this->splitted_lines[$original_row_id][$col_id]["from"], $this->splitted_lines[$original_row_id][$col_id]["length"])));
                                    } else {
                                        $html = str_replace("<br />", " <br/>WFREMOVEWF ", $html);
                                        $html = trim(implode(" ", array_slice(explode(" ", $html), $this->splitted_lines[$original_row_id][$col_id]["from"])));
                                    }
                                    $html = trim(str_replace("WFREMOVEWF", "", $html));
                                    if(substr($html, 0, 5) == "<br/>") {
                                        $html = substr($html, 5);
                                    }
                                    if((int) $this->splitted_lines[$original_row_id][$col_id]["length"] === 0) {
                                        if(0 < $element_value["DiscountPercentage"] && isEmptyFloat($element_value["Number"]) && $this->line_discount_merged_amount === false) {
                                            $element_value["Number"] = $invoiceElement->Number;
                                            $element_value["NumberSuffix"] = $invoiceElement->NumberSuffix;
                                            $this->pagetotals["PriceExcl"] -= number_format(round((double) $element_value["DiscountPercentage"], 4) * $element_value["PriceExcl"] * $element_value["Number"] * $element_value["Periods"], 2, ".", "");
                                            $this->pagetotals["PriceIncl"] -= $this->objects["invoice"]->VatCalcMethod == "incl" ? round(round((double) $element_value["DiscountPercentage"], 4) * $element_value["PriceExcl"] * $element_value["Number"] * $element_value["Periods"] * (1 + $element_value["TaxPercentage"]), 2) : round(round(round((double) $element_value["DiscountPercentage"], 4) * $element_value["PriceExcl"] * $element_value["Number"] * $element_value["Periods"], 2) * (1 + $element_value["TaxPercentage"]), 2);
                                        }
                                        unset($this->splitted_lines);
                                        $this->processed_lines[] = $original_row_id;
                                    }
                                }
                                if(($block["type"] != "invoicelines" || 0 < $row_id) && $html != "" && isset($block["cols"][$col_id]["style"]["format"]) && $block["cols"][$col_id]["style"]["format"] == "money") {
                                    if(CURRENCY_SIGN_LEFT) {
                                        $this->pdf->writeHTMLCell($cell_width, $cell_height_helper - $y_start_row, $cell_x, $cell_y, CURRENCY_SIGN_LEFT, $cell_border, $ln = 1, $cell_fill, $reseth = true, "L", $autopadding = true);
                                    }
                                    $html = CURRENCY_SIGN_RIGHT ? $html . " " . CURRENCY_SIGN_RIGHT : $html;
                                    $this->pdf->writeHTMLCell($cell_width, $cell_height_helper - $y_start_row, $cell_x, $cell_y, $html, $cell_border, $ln = 1, $cell_fill, $reseth = true, "R", $autopadding = true);
                                } else {
                                    $this->pdf->writeHTMLCell($cell_width, $cell_height_helper - $y_start_row, $cell_x, $cell_y, $html, $cell_border, $ln = 1, $cell_fill, $reseth = true, $cell_align, $autopadding = true);
                                }
                                $x_start = $x_start + $cell_width;
                                $y_pointer = $y_pointer < $this->pdf->GetY() ? $this->pdf->GetY() : $y_pointer;
                                $get_y = $get_y < $this->pdf->GetY() ? $this->pdf->GetY() : $get_y;
                                $this->pdf->SetY($y_start_row);
                            }
                            $multi_row_xy["x"] = $x_start;
                        }
                        $multi_row_xy["x"] = $x_pointer;
                        $multi_row_xy["y"] = $get_y;
                        $loop_row_y_pointers[$tmp_row_id] = $y_pointer;
                        if(!empty($this->splitted_lines) && 0 < $row_id) {
                            $this->pdf->SetXY($x_pointer, $y_pointer);
                            $loop_twice++;
                        }
                    }
                }
                if($block["type"] == "invoicelines" && !empty($this->splitted_lines) && 0 < $row_id) {
                    if(0 < $element_value["DiscountPercentage"] && !isEmptyFloat($element_value["Number"]) && $this->line_discount_merged_amount === false) {
                        $this->pagetotals["PriceExcl"] += number_format(round((double) $element_value["DiscountPercentage"], 4) * $element_value["PriceExcl"] * $element_value["Number"] * $element_value["Periods"], 2, ".", "");
                        $this->pagetotals["PriceIncl"] += $this->objects["invoice"]->VatCalcMethod == "incl" ? round(round((double) $element_value["DiscountPercentage"], 4) * $element_value["PriceExcl"] * $element_value["Number"] * $element_value["Periods"] * (1 + $element_value["TaxPercentage"]), 2) : round(round(round((double) $element_value["DiscountPercentage"], 4) * $element_value["PriceExcl"] * $element_value["Number"] * $element_value["Periods"], 2) * (1 + $element_value["TaxPercentage"]), 2);
                    }
                    $this->invoicelines_completed = false;
                    $this->within_split = true;
                    return NULL;
                }
            }
        }
        if(JB_DEBUG) {
            echo "<hr />Table e: " . (microtime(true) - $this->start_time) . " sec<br />";
        }
    }
    private function create_block($block)
    {
        if(JB_DEBUG) {
            echo "<hr />Block s: " . (microtime(true) - $this->start_time) . " sec<br />";
        }
        $x_pointer = $block["positioning"]["x"];
        $y_pointer = $block["positioning"]["y"];
        $this->pdf->SetXY($x_pointer, $y_pointer);
        if(!array_key_exists($block["text"]["family"], $this->AvailableFonts["custom"]) && !array_key_exists($block["text"]["family"], $this->AvailableFonts["default"])) {
            $cell_font = "helvetica";
        } else {
            $cell_font = $block["text"]["family"] ? $block["text"]["family"] : "helvetica";
        }
        $cell_font_size = $block["text"]["size"] ? $block["text"]["size"] : "10";
        $cell_font_color = $this->htmlcolor2rgb($block["text"]["color"] ? $block["text"]["color"] : "#000");
        $cell_align = $block["text"]["align"] ? $block["text"]["align"] : "left";
        $cell_line_height = $block["text"]["lineheight"] ? $block["text"]["lineheight"] : "1.25";
        $cell_font_style = $block["text"]["style"] ? $block["text"]["style"] : "";
        $cell_border = "";
        $cell_border .= $block["borders"]["top"] == "yes" ? "T" : "";
        $cell_border .= $block["borders"]["right"] == "yes" ? "R" : "";
        $cell_border .= $block["borders"]["bottom"] == "yes" ? "B" : "";
        $cell_border .= $block["borders"]["left"] == "yes" ? "L" : "";
        $cell_background = $block["style"]["bgcolor"] ? $block["style"]["bgcolor"] : "";
        $cell_align = strtoupper(substr($block["text"]["align"], 0, 1));
        $this->pdf->setCellHeightRatio($cell_line_height);
        $includeSubset = in_array($cell_font, ["helvetica", "freeserif"]) ? "default" : false;
        $this->pdf->SetFont($cell_font, $cell_font_style, $cell_font_size, "", $includeSubset);
        $this->pdf->SetTextColor($cell_font_color[0], $cell_font_color[1], $cell_font_color[2]);
        if($cell_background) {
            $cell_fill = true;
            $cell_bgcolors = $this->htmlcolor2rgb($cell_background);
            $this->pdf->SetFillColor($cell_bgcolors[0], $cell_bgcolors[1], $cell_bgcolors[2]);
        } else {
            $cell_fill = false;
        }
        $cell_border = $cell_border == "" ? 0 : [$cell_border => ["width" => $this->block_model->px_to_mm($block["borders"]["thickness"], false), "cap" => "butt", "join" => "miter", "dash" => $block["borders"]["type"] == "solid" ? "0" : "1", "color" => $this->htmlcolor2rgb($block["borders"]["color"])]];
        $html = $this->translate_variables($block["value"]);
        if($cell_align == "R" && strpos($html, "{:ptg:}") !== false) {
            $block["positioning"]["w"] += $this->pdf->getStringWidth("{:ptg:}") - 0 * $this->pdf->getStringWidth("8");
        }
        $this->pdf->writeHTMLCell($block["positioning"]["w"], $h = 0, $block["positioning"]["x"], $block["positioning"]["y"], $html, $cell_border, $ln = 1, $cell_fill, $reseth = true, $cell_align, $autopadding = true);
        if(JB_DEBUG) {
            echo "<hr />Block e: " . (microtime(true) - $this->start_time) . " sec<br />";
        }
    }
    private function create_image($block)
    {
        if(JB_DEBUG) {
            echo "<hr />Image s: " . $block["value"] . ": " . (microtime(true) - $this->start_time) . " sec<br />";
        }
        if(!trim($block["value"])) {
            return NULL;
        }
        if(@getimagesize($block["value"]) === false) {
            return NULL;
        }
        $img = $this->pdf->Image($block["value"], $block["positioning"]["x"], $block["positioning"]["y"], $block["positioning"]["w"], $block["positioning"]["h"], $type = "", $link = "", $align = "", $resize = false, $dpi = 300, $palign = "", $ismask = false, $imgmask = false, $border = 0, $fitbox = false, $hidden = false, $fitonpage = false, $alt = false, $altimgs = []);
        if(JB_DEBUG) {
            echo "<hr />Image e: " . $block["value"] . ": " . (microtime(true) - $this->start_time) . " sec<br />";
        }
    }
    private function createQRCodeBlock($block)
    {
        $blockModel = new templateblock();
        $qrSizeToRender = 2 * $blockModel->mm_to_px($block["positioning"]["w"]);
        if($this->is_example_preview === true) {
            $imgBase64Encoded = $blockModel->generateExampleQRCode($qrSizeToRender);
        } else {
            if(!isset($this->objects["invoice"]) || !$this->objects["invoice"] instanceof invoice) {
                return NULL;
            }
            $invoice = $this->objects["invoice"];
            if($invoice->canGenerateQRCode() === false) {
                if($invoice->canGenerateExampleQRCode()) {
                    $imgBase64Encoded = $blockModel->generateExampleQRCode($qrSizeToRender);
                } else {
                    return NULL;
                }
            } else {
                $url = str_replace("&amp;", "&", $invoice->PaymentURLRaw);
                $imgBase64Encoded = $blockModel->generateQRCodeForUrl($url, $qrSizeToRender);
            }
        }
        $x_pointer = $block["positioning"]["x"];
        $y_pointer = $block["positioning"]["y"];
        $this->pdf->SetXY($x_pointer, $y_pointer);
        $path = tempnam(sys_get_temp_dir(), "qrcode_");
        $imgBase64 = str_replace("data:image/png;base64,", "", $imgBase64Encoded);
        file_put_contents($path, base64_decode($imgBase64));
        $this->pdf->Image($path, $block["positioning"]["x"], $block["positioning"]["y"], $block["positioning"]["w"], $block["positioning"]["h"], $type = "", $link = "", $align = "", $resize = false, $dpi = 300, $palign = "", $ismask = false, $imgmask = false, $border = 0, $fitbox = false, $hidden = false, $fitonpage = false, $alt = false, $altimgs = []);
        @unlink($path);
    }
    private function htmlcolor2rgb($color)
    {
        if($color && $color[0] == "#") {
            $color = substr($color, 1);
        }
        if(strlen($color) == 6) {
            list($r, $g, $b) = [$color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]];
        } elseif(strlen($color) == 3) {
            list($r, $g, $b) = [$color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]];
        } else {
            $r = 0;
            $g = 0;
            $b = 0;
        }
        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);
        return [$r, $g, $b];
    }
    private function translate_variables($value, $invoiceElement = false, $multi_row_id = 0)
    {
        if(!$invoiceElement || $multi_row_id === 0 && !isEmptyFloat(deformat_money($invoiceElement->PriceExcl)) || isset($invoiceElement->id) && $invoiceElement->id == "discount" || $invoiceElement->ProductCode || preg_match("[invoiceElement->Description]", $value) || $multi_row_id == 1 && 0 < $invoiceElement->DiscountPercentage && (preg_match("[invoiceElement->Description]", $value) || preg_match("[invoiceElement->NoDiscountAmountExcl]", $value) || preg_match("[invoiceElement->NoDiscountAmountIncl]", $value))) {
            $pdf = $this->pdf;
            if(isset($invoiceElement->id) && $invoiceElement->id == "discount" && (preg_match("[invoiceElement->TaxPercentage]", $value) || preg_match("[invoiceElement->FullTaxPercentage]", $value))) {
                return "";
            }
            foreach ($this->objects as $objName => $object) {
                ${$objName} = $object;
            }
            if($multi_row_id == 1 && 0 < $invoiceElement->DiscountPercentage) {
                if(preg_match("[invoiceElement->Description]", $value)) {
                    $value = $this->Type == "pricequote" ? sprintf(__("x discount on pricequoteline"), showNumber($invoiceElement->FullDiscountPercentage)) : sprintf(__("x discount on invoiceline"), showNumber($invoiceElement->FullDiscountPercentage));
                } elseif(preg_match("[invoiceElement->NoDiscountAmountExcl]", $value)) {
                    $value = $invoiceElement->DiscountAmountExcl;
                    $this->line_discount_merged_amount = false;
                } elseif(preg_match("[invoiceElement->NoDiscountAmountIncl]", $value)) {
                    $value = $invoiceElement->DiscountAmountIncl;
                    $this->line_discount_merged_amount = false;
                } else {
                    $value = "";
                }
                return $value;
            }
            $pdf->CurrentPageNumber = $this->pdf->getGroupPageNo();
            $pdf->TotalPageNumber = $this->pdf->getPageGroupAlias();
            if(isset($invoice)) {
                $invoice->PageTotalExcl = money($this->pagetotals["PriceExcl"], false);
                $invoice->PageTotalIncl = money($this->pagetotals["PriceIncl"], false);
            }
            switch ($debtor->Sex) {
                case "m":
                case "f":
                    $debtor->SexName = __("salutation female");
                    break;
                case "d":
                    $debtor->SexName = "";
                    break;
                default:
                    $debtor->SexName = __("salutation male");
                    if(isset($invoice->Sex)) {
                        switch ($invoice->Sex) {
                            case "m":
                            case "f":
                                $invoice->SexName = __("salutation female");
                                break;
                            case "d":
                                $invoice->SexName = "";
                                break;
                            default:
                                $invoice->SexName = __("salutation male");
                        }
                    }
                    if((strpos($value, "[debtor->OpenAmountExcl]") !== false || strpos($value, "[debtor->OpenAmountIncl]") !== false) && $this->calledGetOpenAmount === false) {
                        $debtor->getOpenAmount(false);
                        $this->calledGetOpenAmount = true;
                    }
                    if((strpos($value, "[debtor->TotalAmountExcl]") !== false || strpos($value, "[debtor->AverageOutstandingDays]") !== false) && $this->calledGetFinancialInfo === false) {
                        $debtor->getFinancialInfo();
                        $this->calledGetFinancialInfo === true;
                    }
                    $value = str_replace("[debtor->Sex]", "[debtor->SexName]", $value);
                    $value = str_replace("[invoice->Sex]", "[invoice->SexName]", $value);
                    $value = str_replace("&lt;", "[[LT]]", str_replace("&gt;", "[[GT]]", $value));
                    if(isset($domain) && 0 < $domain->Identifier && !isset($_SESSION["domain_token_retrieved"][$domain->Domain . "." . $domain->Tld]) && strpos($this->Message, "[domain-&gt;AuthKey]") !== false) {
                        $token = $domain->getToken(true);
                        $_SESSION["domain_token_retrieved"][$domain->Domain . "." . $domain->Tld] = true;
                        if($token !== false) {
                            $domain->show();
                        }
                    }
                    $pattern = [];
                    $replace = [];
                    $newline_patterns = [];
                    $newline_replaces = [];
                    $newline_patterns[] = "/\\r\\n\\[period\\]([\\r\\n]*)(.*)\\[\\/period\\]/si";
                    $newline_replaces[] = "<?php if(\$invoiceElement->StartPeriod){ ?>\r\n\\2<?php } ?>";
                    $pattern[] = "/\\[period\\]([\\r\\n]*)(.*)\\[\\/period\\]/si";
                    $replace[] = "<?php if(\$invoiceElement->StartPeriod){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[credit\\]([\\r\\n]*)(.*)\\[\\/credit\\]/si";
                    $replace[] = "<?php if(isset(\$invoice->InvoiceCode) && \$invoice->Status == 8){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[non-credit\\]([\\r\\n]*)(.*)\\[\\/non-credit\\]/si";
                    $replace[] = "<?php if(!isset(\$invoice->InvoiceCode) || \$invoice->Status != 8){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[paid\\]([\\r\\n]*)(.*)\\[\\/paid\\]/si";
                    $replace[] = "<?php if(\$invoice->Paid == 1 || \$invoice->Status == 4){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[unpaid\\]([\\r\\n]*)(.*)\\[\\/unpaid\\]/si";
                    $replace[] = "<?php if(!(\$invoice->Paid == 1 || \$invoice->Status == 4)){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[partly_paid\\]([\\r\\n]*)(.*)\\[\\/partly_paid\\]/si";
                    $replace[] = "<?php if((\$invoice->Status == 2 || \$invoice->Status == 3) && floatval(deformat_money(\$invoice->AmountPaid)) != 0){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[directdebit\\]([\\r\\n]*)(.*)\\[\\/directdebit\\]/si";
                    $replace[] = "<?php if(\$invoice->Authorisation == \"yes\"){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[transfer\\]([\\r\\n]*)(.*)\\[\\/transfer\\]/si";
                    $replace[] = "<?php if(\$invoice->Authorisation != \"yes\"){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[reversecharge\\]([\\r\\n]*)(.*)\\[\\/reversecharge\\]/si";
                    $replace[] = "<?php if(\$invoice->VatShift == 'yes' || (\$invoice->VatShift == '' && (\$invoice->AmountTax == money(0,false)) && \$invoice->Country != \$company->Country && \$invoice->CompanyName != \"\" && ((isset(\$invoice->TaxNumber) && \$invoice->TaxNumber != \"\") || \$debtor->TaxNumber != \"\") && (\$invoice->Country == \"NL\" || isset(\$_SESSION['wf_cache_array_country_EU'][\$invoice->Country])))){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[male\\]([\\r\\n]*)(.*)\\[\\/male\\]/si";
                    $replace[] = "<?php if(isset(\$invoice->Sex) && \$invoice->Sex == \"m\"){ ?>\\2<?php } elseif(!isset(\$invoice->Sex) && \$debtor->Sex == \"m\") { ?>\\2<?php } ?>";
                    $pattern[] = "/\\[female\\]([\\r\\n]*)(.*)\\[\\/female\\]/si";
                    $replace[] = "<?php if(isset(\$invoice->Sex) && \$invoice->Sex == \"f\"){ ?>\\2<?php } elseif(!isset(\$invoice->Sex) && \$debtor->Sex == \"f\") { ?>\\2<?php } ?>";
                    $pattern[] = "/\\[department\\]([\\r\\n]*)(.*)\\[\\/department\\]/si";
                    $replace[] = "<?php if(isset(\$invoice->Sex) && \$invoice->Sex == \"d\"){ ?>\\2<?php } elseif(!isset(\$invoice->Sex) && \$debtor->Sex == \"d\") { ?>\\2<?php } ?>";
                    $pattern[] = "/\\[is_company\\]([\\r\\n]*)(.*)\\[\\/is_company\\]/si";
                    $replace[] = "<?php if(isset(\$invoice->CompanyName) && \$invoice->CompanyName != \"\"){ ?>\\2<?php } elseif(!isset(\$invoice->CompanyName) && isset(\$debtor->CompanyName) && \$debtor->CompanyName != \"\"){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[is_consumer\\]([\\r\\n]*)(.*)\\[\\/is_consumer\\]/si";
                    $replace[] = "<?php if(isset(\$invoice->CompanyName) && \$invoice->CompanyName == \"\"){ ?>\\2<?php } elseif(!isset(\$invoice->CompanyName) && isset(\$debtor->CompanyName) && \$debtor->CompanyName == \"\"){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[has_contact\\]([\\r\\n]*)(.*)\\[\\/has_contact\\]/si";
                    $replace[] = "<?php if(isset(\$invoice->Initials) && (\$invoice->Initials != \"\" || \$invoice->SurName != \"\")){ ?>\\2<?php } elseif(!isset(\$invoice->Initials) && isset(\$debtor->Initials) && (\$debtor->Initials != \"\" || \$debtor->SurName != \"\")){ ?>\\2<?php } ?>";
                    $newline_patterns[] = "/\\r\\n\\[linediscount\\]([\\r\\n]*)(.*)\\[\\/linediscount\\]/si";
                    $newline_replaces[] = "<?php if(\$invoiceElement->DiscountPercentage > 0){ ?>\r\n\\2<?php } ?>";
                    $pattern[] = "/\\[linediscount\\]([\\r\\n]*)(.*)\\[\\/linediscount\\]/si";
                    $replace[] = "<?php if(\$invoiceElement->DiscountPercentage > 0){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[reference\\]([\\r\\n]*)(.*)\\[\\/reference\\]/si";
                    $replace[] = "<?php if(\$invoice->ReferenceNumber != \"\"){ ?>\\2<?php } ?>";
                    foreach ($pattern as $tmp_key => $tmp_pattern) {
                        $newline_patterns[] = str_replace("/si", "\\r\\n\\[/i", $tmp_pattern);
                        $newline_replaces[] = $replace[$tmp_key] . "[";
                    }
                    $pattern = array_merge($newline_patterns, $pattern);
                    $replace = array_merge($newline_replaces, $replace);
                    $str = preg_replace($pattern, $replace, $value);
                    $str = nl2br($str);
                    $str = str_replace("[invoiceElement->Number]", "[invoiceElement->Number][invoiceElement->NumberSuffix]", $str);
                    $str = str_replace("[debtor->Password]", "<?PHP echo (isset(\$debtor->Password)) ? passcrypt(\$debtor->Password) : \"\"; ?>", $str);
                    if(isset($invoiceElement) && $invoiceElement !== false) {
                        $str = str_replace("[invoiceElement->FullDiscountPercentage]", showNumber($invoiceElement->FullDiscountPercentage), $str);
                        $str = str_replace("[invoiceElement->FullTaxPercentage]", $invoiceElement->FullTaxPercentage, $str);
                        $str = str_replace("[invoiceElement->Description]", "<?PHP echo (isset(\$invoiceElement->Description)) ? nl2br(str_replace('&lt;','[[LT]]',str_replace('&gt;','[[GT]]',\$invoiceElement->Description))) : \"\"; ?>", $str);
                    }
                    $str = str_replace("[invoice->Description]", "<?PHP echo (isset(\$invoice->Description)) ? nl2br(\$invoice->Description) : \"\"; ?>", $str);
                    $str = str_replace("[custom->", "[debtor->custom->", $str);
                    if(!empty($invoice->custom)) {
                        foreach ($invoice->custom as $customFieldName => $customFieldValue) {
                            $invoice->custom->{$customFieldName} = str_replace(">", "[[GT]]", str_replace("<", "[[LT]]", $customFieldValue));
                        }
                        $pattern = "/\\[debtor->custom->([_.\\da-z0-9]+)\\]/i";
                        if(preg_match_all($pattern, $str, $matches)) {
                            foreach ($matches[1] as $_fieldcode) {
                                if(isset($invoice->custom->{$_fieldcode})) {
                                    $str = str_replace("[debtor->custom->" . $_fieldcode . "]", "[invoice->custom->" . $_fieldcode . "]", $str);
                                }
                            }
                        }
                    }
                    if(isset($debtor->custom) && is_array($debtor->custom)) {
                        foreach ($debtor->custom as $customFieldName => $customFieldValue) {
                            $debtor->custom->{$customFieldName} = str_replace(">", "[[GT]]", str_replace("<", "[[LT]]", $customFieldValue));
                        }
                    }
                    $pattern = "/\\r\\n\\[([_.\\da-z0-9]+->[_.\\da-z0-9]+(->[_.\\da-z0-9]+){0,1})\\]<br \\/>/i";
                    $replacement = "<?PHP if(isset(\$\\1) && \$\\1){ echo nl2br(\$\\1).\"<br />\"; }elseif(isset(\$\\1)){ echo \"\"; }else{ echo \"WFbrackA\\1WFbrackB<br />\"; } ?>";
                    $str = preg_replace($pattern, $replacement, $str);
                    $pattern = "/\\[([_.\\da-z0-9]+->[_.\\da-z0-9]+(->[_.\\da-z0-9]+){0,1})\\]/i";
                    $replacement = "<?PHP if(isset(\$\\1) && \$\\1){ echo nl2br(\$\\1); }elseif(isset(\$\\1)){ echo \"\"; }else{ echo \"WFbrackA\\1WFbrackB\"; } ?>";
                    $str = preg_replace($pattern, $replacement, $str);
                    $str = str_replace(["WFbrackA", "WFbrackB"], ["[", "]"], $str);
                    $currentErrorReporting = error_reporting(0);
                    ob_start();
                    eval("?>" . $str . "<?PHP ");
                    $str = ob_get_contents();
                    ob_end_clean();
                    error_reporting($currentErrorReporting);
                    $str = str_replace("<", "[[LT]]", str_replace(">", "[[GT]]", $str));
                    $str = str_replace("&lt;", "[[LT]]", str_replace("&gt;", "[[GT]]", $str));
                    $str = html_entity_decode($str, ENT_COMPAT, "UTF-8");
                    $str = str_replace(["[[LT]]br[[GT]]", "[[LT]]br /[[GT]]", "[[LT]]br[[GT]]"], "<br />", $str);
                    if(strpos($str, "[[LT]]") !== false || strpos($str, "[[GT]]") !== false) {
                        $count_html_parsing = 0;
                        do {
                            $count_html_parsing++;
                            $str_before_parsing = $str;
                            $pattern = "/\\[\\[LT\\]\\]([^\\s]*)\\b([\\s].*)?\\[\\[GT\\]\\](.*)\\[\\[LT\\]\\]\\/\\1\\[\\[GT\\]\\]/siU";
                            $replace = "<\\1\\2>\\3</\\1>";
                            $str = preg_replace($pattern, $replace, $str);
                        } while (!($count_html_parsing < 10 && $str_before_parsing != $str));
                        $str = str_replace(["[[LT]]", "[[GT]]"], ["&lt;", "&gt;"], $str);
                    }
                    if(substr($str, -6) == "<br />") {
                        $str = substr($str, 0, -6);
                    }
                    $str = str_replace("[debtor->custom->", "[custom->", $str);
                    return $str;
            }
        } else {
            return "";
        }
    }
    private static function unset_cached_files($pdf)
    {
        if(isset($pdf->cached_files)) {
            $pdf->cached_files = [];
        }
    }
    private static function set_cloned_file_id($pdf)
    {
        if(isset($pdf->file_id)) {
            $pdf->file_id = "clone" . $pdf->file_id;
            $pdf->imagekeys = [];
        }
    }
}

?>