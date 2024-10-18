<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class templateblock
{
    public $TEMPLATE_RESIZE_GRID_SIZE = 10;
    public $TEMPLATE_DRAGABLE_GRID_SIZE = 10;
    public $TEMPLATE_BLOCK_PADDING = 5;
    public $TEMPLATE_FONT_SIZE_ADJUSTER = 1.08;
    public $id;
    public $template_id;
    public $type;
    public $positioning;
    public $visibility;
    public $text;
    public $border;
    public $style;
    public $cols;
    public $rows;
    public $value;
    public $Success;
    public $Warning;
    public $Error;
    const BLOCK_TYPE_TEXT = "text";
    const BLOCK_TYPE_TABLE = "table";
    const BLOCK_TYPE_IMAGE = "image";
    const BLOCK_TYPE_LINES = "invoicelines";
    const BLOCK_TYPE_TOTALS = "totals";
    const BLOCK_TYPE_QR_CODE = "qrcode";
    const QR_CODE_MIN_SIZE = 30;
    const CUSTOM_FONT_PATH = "3rdparty/fonts/";
    public function __construct()
    {
        $this->Success = $this->Warning = $this->Error = [];
    }
    public function getDefaultBlock($type)
    {
        $this->type = $type;
        $this->positioning = ["x" => 0, "y" => 0, "w" => 0, "h" => 0];
        $this->visibility = "all";
        $this->text = ["family" => "helvetica", "size" => 10, "color" => "#000000", "align" => "left", "lineheight" => "1.25", "style" => ""];
        $this->borders = ["top" => "no", "right" => "no", "bottom" => "no", "left" => "no", "thickness" => "1", "color" => "#000000", "type" => "solid"];
        $this->style = ["bgcolor" => ""];
        $this->value = "";
        $this->cols = [];
        $this->rows = [];
        switch ($type) {
            case "table":
                $default_col = $this->_getDefaultCol();
                $cols = [$default_col, $default_col];
                $cols[0]["positioning"]["w"] = 30;
                $cols[1]["positioning"]["w"] = 0;
                $this->cols = $cols;
                $this->value = [["", ""]];
                break;
            case "qrcode":
                $this->positioning = ["x" => 0, "y" => 0, "w" => 30, "h" => 30];
                break;
        }
    }
    public function getBlock($block_id)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_TemplateBlocks")->where("id", $block_id)->asArray()->execute();
        if($result) {
            return $this->_formatBlockArray($result);
        }
        return [];
    }
    public function listBlocks($template_id)
    {
        $result = Database_Model::getInstance()->get("HostFact_TemplateBlocks")->where("template_id", $template_id)->asArray()->execute();
        if($result) {
            $list_blocks = [];
            foreach ($result as $k => $item) {
                $list_blocks[$k] = $this->_formatBlockArray($item);
            }
            usort($list_blocks, function ($block1, $block2) {
                if($block1["type"] == $block2["type"]) {
                    return 0;
                }
                if($block2["type"] == self::BLOCK_TYPE_QR_CODE) {
                    return -1;
                }
                $block1Image = $block1["type"] == self::BLOCK_TYPE_IMAGE;
                $block2Image = $block2["type"] === self::BLOCK_TYPE_IMAGE;
                if($block1Image && $block2Image) {
                    return 0;
                }
                if($block1Image) {
                    return -1;
                }
                return 1;
            });
            return $list_blocks;
        } else {
            return [];
        }
    }
    public function createBlock($template_id, $type)
    {
        $this->getDefaultBlock($type);
        $result = Database_Model::getInstance()->insert("HostFact_TemplateBlocks", ["template_id" => $template_id, "type" => $type, "value" => is_array($this->value) ? json_encode($this->value) : $this->value, "positioning_x" => $this->positioning["x"], "positioning_y" => $this->positioning["y"], "positioning_w" => $this->positioning["w"], "positioning_h" => $this->positioning["h"], "visibility" => $this->visibility, "text_family" => $this->text["family"], "text_size" => $this->text["size"], "text_color" => $this->text["color"], "text_align" => $this->text["align"], "text_lineheight" => $this->text["lineheight"], "text_style" => $this->text["style"], "borders_top" => $this->borders["top"], "borders_right" => $this->borders["right"], "borders_bottom" => $this->borders["bottom"], "borders_left" => $this->borders["left"], "borders_thickness" => $this->borders["thickness"], "borders_color" => $this->borders["color"], "borders_type" => $this->borders["type"], "style_bgcolor" => $this->style["bgcolor"], "cols" => json_encode($this->cols), "rows" => json_encode($this->rows)])->execute();
        if($result) {
            return $result;
        }
        return false;
    }
    public function createDefaultBlocks($template_type, $template_id)
    {
        switch ($template_type) {
            case "invoice":
                if(LANGUAGE_CODE == "nl_NL") {
                    $sql_dump = "INSERT INTO `HostFact_TemplateBlocks` (`template_id`, `type`, `value`, `positioning_x`, `positioning_y`, `positioning_w`, `positioning_h`, `visibility`, `text_family`, `text_size`, `text_color`, `text_align`, `text_lineheight`, `text_style`, `borders_top`, `borders_right`, `borders_bottom`, `borders_left`, `borders_thickness`, `borders_color`, `borders_type`, `style_bgcolor`, `cols`, `rows`) VALUES (:template_id, 'invoicelines', '[[\"Aantal\",\"Omschrijving\",\"Prijs per stuk\",\"Bedrag\"],[\"[invoiceElement->Number]\",\"[invoiceElement->Description]\\\\r\\\\n[period]Periode: [invoiceElement->StartPeriod] tot [invoiceElement->EndPeriod][\\/period]\",\"[invoiceElement->PriceExcl]\",\"[invoiceElement->NoDiscountAmountExcl]\"]]', 20, 127, 170, 90, 'all', 'helvetica', 10, '#000000', 'left', 0, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"20\"}},{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"\"}},{\"text\":{\"align\":\"right\"},\"positioning\":{\"w\":\"27\"},\"style\":{\"format\":\"money\"}},{\"text\":{\"align\":\"right\"},\"positioning\":{\"w\":\"27\"},\"style\":{\"format\":\"money\"}}]', '[{\"text\":{\"family\":\"\",\"size\":\"10\",\"style\":\"B\",\"color\":\"\",\"lineheight\":\"1.5\"},\"borders\":{\"bottom\":\"yes\",\"type\":\"solid\",\"thickness\":\"1\",\"color\":\"\"},\"style\":{\"bgcolor\":\"\"}},{\"text\":{\"family\":\"\",\"size\":\"10\",\"style\":\"\",\"color\":\"\",\"lineheight\":\"1.5\"},\"borders\":{\"type\":\"solid\",\"thickness\":\"\",\"color\":\"\"},\"style\":{\"bgcolor\":\"\",\"bgcolor_even\":\"\"}}]'),                    (:template_id, 'text', 'Factuur', 20, 63, 0, 0, 'all', 'helvetica', 24, '#000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),                    (:template_id, 'text', '[company->CompanyName]\\r\\n[company->Address]\\r\\n[company->ZipCode]  [company->City]\\r\\n[company->CountryLong]\\r\\n\\r\\nE-mail: [company->EmailAddress]\\r\\nWebsite: [company->Website]\\r\\n\\r\\nKvK nummer: [company->CompanyNumber]\\r\\nBTW nummer: [company->TaxNumber]\\r\\nIBAN: [company->AccountNumber]\\r\\nBIC: [company->AccountBIC]', 110, 20, 80, 0, 'all', 'helvetica', 9, '#000', 'right', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'totals', '[[\"Paginatotaal excl\",\"[invoice->PageTotalExcl]\"],[\"Totaal excl. BTW\",\"[invoice->AmountExcl]\"],[\"\"],[\"Totaal incl. BTW\",\"[invoice->AmountIncl]\"]]', 130, 220, 60, 0, 'all', 'helvetica', 10, '#000', 'left', 1.75, '', 'yes', 'no', 'no', 'no', 1, '#000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"33\"}},{\"style\":{\"format\":\"money\"}}]', '[{\"style\":{\"totaltype\":\"pagetotalexcl\",\"bgcolor\":\"\"}},{\"style\":{\"totaltype\":\"amountexcl\",\"bgcolor\":\"\"}},{\"style\":{\"totaltype\":\"amounttax\",\"bgcolor\":\"\"}},{\"style\":{\"totaltype\":\"amountincl\",\"bgcolor\":\"\"},\"borders\":{\"top\":\"yes\",\"bottom\":\"yes\"}}]'),                    (:template_id, 'text', '[reversecharge]BTW verlegd: [invoice->TaxNumber][/reversecharge]', 20, 220, 0, 0, 'last', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),                    (:template_id, 'text', '[invoice->CompanyName]\\r\\n[invoice->Initials] [invoice->SurName]\\r\\n[invoice->Address]\\r\\n[invoice->ZipCode]  [invoice->City]\\r\\n[invoice->CountryLong]', 20, 87, 0, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),                    (:template_id, 'table', '[[\"Klantnummer:\",\"[debtor->DebtorCode]\"],[\"Factuurnummer:\",\"[invoice->InvoiceCode]\"],[\"Factuurdatum:\",\"[invoice->Date]\"]]', 130, 97, 60, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"30\"}},{\"text\":{\"align\":\"right\"},\"positioning\":{\"w\":\"\"}}]', '[]'),                    (:template_id, 'text', '[paid]De factuur is reeds betaald.[/paid]\\r\\n[unpaid]\\r\\n[directdebit]Het bedrag wordt automatisch van uw bankrekening afgeschreven.[/directdebit]\\r\\n[transfer]Te betalen binnen [invoice->Term] dagen na de factuurdatum (voor [invoice->PayBefore]) op rekeningnummer [company->AccountNumber] t.n.v. [company->CompanyName] onder vermelding van klantnummer en factuurnummer.[/transfer]\\r\\n[/unpaid]', 20, 260, 170, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]')";
                } else {
                    $sql_dump = "INSERT INTO `HostFact_TemplateBlocks` (`template_id`, `type`, `value`, `positioning_x`, `positioning_y`, `positioning_w`, `positioning_h`, `visibility`, `text_family`, `text_size`, `text_color`, `text_align`, `text_lineheight`, `text_style`, `borders_top`, `borders_right`, `borders_bottom`, `borders_left`, `borders_thickness`, `borders_color`, `borders_type`, `style_bgcolor`, `cols`, `rows`) VALUES (:template_id, 'invoicelines', '[[\"Quantity\",\"Description\",\"Price per unit\",\"Amount\"],[\"[invoiceElement->Number]\",\"[invoiceElement->Description]\\\\r\\\\n[period]Period: [invoiceElement->StartPeriod] till [invoiceElement->EndPeriod][\\/period]\",\"[invoiceElement->PriceExcl]\",\"[invoiceElement->NoDiscountAmountExcl]\"]]', 20, 127, 170, 90, 'all', 'helvetica', 10, '#000000', 'left', 0, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"20\"}},{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"\"}},{\"text\":{\"align\":\"right\"},\"positioning\":{\"w\":\"27\"},\"style\":{\"format\":\"money\"}},{\"text\":{\"align\":\"right\"},\"positioning\":{\"w\":\"27\"},\"style\":{\"format\":\"money\"}}]', '[{\"text\":{\"family\":\"\",\"size\":\"10\",\"style\":\"B\",\"color\":\"\",\"lineheight\":\"1.5\"},\"borders\":{\"bottom\":\"yes\",\"type\":\"solid\",\"thickness\":\"1\",\"color\":\"\"},\"style\":{\"bgcolor\":\"\"}},{\"text\":{\"family\":\"\",\"size\":\"10\",\"style\":\"\",\"color\":\"\",\"lineheight\":\"1.5\"},\"borders\":{\"type\":\"solid\",\"thickness\":\"\",\"color\":\"\"},\"style\":{\"bgcolor\":\"\",\"bgcolor_even\":\"\"}}]'),(:template_id, 'text', 'Invoice', 20, 63, 0, 0, 'all', 'helvetica', 24, '#000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', '[company->CompanyName]\\r\\n[company->Address]\\r\\n[company->Address2]\\r\\n[company->ZipCode] [company->City]\\r\\n[company->StateName]\\r\\n[company->CountryLong]\\r\\n\\r\\nTax number: [company->TaxNumber]\\r\\nBank account: [company->AccountNumber]', 130, 20, 60, 0, 'all', 'helvetica', 9, '#000', 'right', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'totals', '[[\"Page total\",\"[invoice->PageTotalExcl]\"],[\"Subtotal\",\"[invoice->AmountExcl]\"],[\"\"],[\"Invoice total\",\"[invoice->AmountIncl]\"]]', 130, 220, 60, 0, 'all', 'helvetica', 10, '#000', 'left', 1.75, '', 'yes', 'no', 'no', 'no', 1, '#000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"33\"}},{\"style\":{\"format\":\"money\"}}]', '[{\"style\":{\"totaltype\":\"pagetotalexcl\",\"bgcolor\":\"\"}},{\"style\":{\"totaltype\":\"amountexcl\",\"bgcolor\":\"\"}},{\"style\":{\"totaltype\":\"amounttax\",\"bgcolor\":\"\"}},{\"style\":{\"totaltype\":\"amountincl\",\"bgcolor\":\"\"},\"borders\":{\"top\":\"yes\",\"bottom\":\"yes\"}}]'),(:template_id, 'text', '[invoice->CompanyName]\\r\\n[invoice->Initials] [invoice->SurName]\\r\\n[invoice->Address]\\r\\n[invoice->Address2]\\r\\n[invoice->ZipCode] [invoice->City]\\r\\n[invoice->StateName]\\r\\n[invoice->CountryLong]', 20, 87, 0, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'table', '[[\"Client number:\",\"[debtor->DebtorCode]\"],[\"Invoice number:\",\"[invoice->InvoiceCode]\"],[\"Invoice date:\",\"[invoice->Date]\"]]', 130, 87, 60, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"30\"}},{\"text\":{\"align\":\"right\"},\"positioning\":{\"w\":\"\"}}]', '[]'),(:template_id, 'text', '[paid]This invoice has been paid.[/paid]\\r\\n[unpaid]Pay withing [invoice->Term] days after the invoice date (before [invoice->PayBefore]) to bank account number [company->AccountNumber] attn [company->CompanyName]. Please include your invoice number and client number on the bank transaction.[/unpaid]', 20, 260, 170, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]')";
                }
                Database_Model::getInstance()->rawQuery($sql_dump, ["template_id" => $template_id]);
                break;
            case "creditinvoice":
                if(LANGUAGE_CODE == "nl_NL") {
                    $sql_dump = "INSERT INTO `HostFact_TemplateBlocks` (`template_id`, `type`, `value`, `positioning_x`, `positioning_y`, `positioning_w`, `positioning_h`, `visibility`, `text_family`, `text_size`, `text_color`, `text_align`, `text_lineheight`, `text_style`, `borders_top`, `borders_right`, `borders_bottom`, `borders_left`, `borders_thickness`, `borders_color`, `borders_type`, `style_bgcolor`, `cols`, `rows`) VALUES (:template_id, 'invoicelines', '[[\"Aantal\",\"Omschrijving\",\"Prijs per stuk\",\"Bedrag\"],[\"[invoiceElement->Number]\",\"[invoiceElement->Description]\\\\r\\\\n[period]Periode: [invoiceElement->StartPeriod] tot [invoiceElement->EndPeriod][\\/period]\",\"[invoiceElement->PriceExcl]\",\"[invoiceElement->NoDiscountAmountExcl]\"]]', 20, 127, 170, 90, 'all', 'helvetica', 10, '#000000', 'left', 0, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"20\"}},{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"\"}},{\"text\":{\"align\":\"right\"},\"positioning\":{\"w\":\"27\"},\"style\":{\"format\":\"money\"}},{\"text\":{\"align\":\"right\"},\"positioning\":{\"w\":\"27\"},\"style\":{\"format\":\"money\"}}]', '[{\"text\":{\"family\":\"\",\"size\":\"10\",\"style\":\"B\",\"color\":\"\",\"lineheight\":\"1.5\"},\"borders\":{\"bottom\":\"yes\",\"type\":\"solid\",\"thickness\":\"1\",\"color\":\"\"},\"style\":{\"bgcolor\":\"\"}},{\"text\":{\"family\":\"\",\"size\":\"10\",\"style\":\"\",\"color\":\"\",\"lineheight\":\"1.5\"},\"borders\":{\"type\":\"solid\",\"thickness\":\"\",\"color\":\"\"},\"style\":{\"bgcolor\":\"\",\"bgcolor_even\":\"\"}}]'),                    (:template_id, 'text', 'Creditfactuur', 20, 63, 0, 0, 'all', 'helvetica', 24, '#000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),                    (:template_id, 'text', '[company->CompanyName]\\r\\n[company->Address]\\r\\n[company->ZipCode]  [company->City]\\r\\n[company->CountryLong]\\r\\n\\r\\nE-mail: [company->EmailAddress]\\r\\nWebsite: [company->Website]\\r\\n\\r\\nKvK nummer: [company->CompanyNumber]\\r\\nBTW nummer: [company->TaxNumber]\\r\\nIBAN: [company->AccountNumber]\\r\\nBIC: [company->AccountBIC]', 110, 20, 80, 0, 'all', 'helvetica', 9, '#000', 'right', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'totals', '[[\"Paginatotaal excl\",\"[invoice->PageTotalExcl]\"],[\"Totaal excl. BTW\",\"[invoice->AmountExcl]\"],[\"\"],[\"Totaal incl. BTW\",\"[invoice->AmountIncl]\"]]', 130, 220, 60, 0, 'all', 'helvetica', 10, '#000', 'left', 1.75, '', 'yes', 'no', 'no', 'no', 1, '#000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"33\"}},{\"style\":{\"format\":\"money\"}}]', '[{\"style\":{\"totaltype\":\"pagetotalexcl\",\"bgcolor\":\"\"}},{\"style\":{\"totaltype\":\"amountexcl\",\"bgcolor\":\"\"}},{\"style\":{\"totaltype\":\"amounttax\",\"bgcolor\":\"\"}},{\"style\":{\"totaltype\":\"amountincl\",\"bgcolor\":\"\"},\"borders\":{\"top\":\"yes\",\"bottom\":\"yes\"}}]'),                    (:template_id, 'text', '[reversecharge]BTW verlegd: [invoice->TaxNumber][/reversecharge]', 20, 220, 0, 0, 'last', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),                    (:template_id, 'text', '[invoice->CompanyName]\\r\\n[invoice->Initials] [invoice->SurName]\\r\\n[invoice->Address]\\r\\n[invoice->ZipCode]  [invoice->City]\\r\\n[invoice->CountryLong]', 20, 87, 0, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),                    (:template_id, 'table', '[[\"Klantnummer:\",\"[debtor->DebtorCode]\"],[\"Factuurnummer:\",\"[invoice->InvoiceCode]\"],[\"Factuurdatum:\",\"[invoice->Date]\"]]', 130, 97, 60, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"30\"}},{\"text\":{\"align\":\"right\"},\"positioning\":{\"w\":\"\"}}]', '[]'),                    (:template_id, 'text', 'Het bedrag wordt binnen [invoice->Term] dagen op uw bankrekening overgemaakt.', 20, 260, 170, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]')";
                } else {
                    $sql_dump = "INSERT INTO `HostFact_TemplateBlocks` (`template_id`, `type`, `value`, `positioning_x`, `positioning_y`, `positioning_w`, `positioning_h`, `visibility`, `text_family`, `text_size`, `text_color`, `text_align`, `text_lineheight`, `text_style`, `borders_top`, `borders_right`, `borders_bottom`, `borders_left`, `borders_thickness`, `borders_color`, `borders_type`, `style_bgcolor`, `cols`, `rows`) VALUES (:template_id, 'invoicelines', '[[\"Quantity\",\"Description\",\"Price per unit\",\"Amount\"],[\"[invoiceElement->Number]\",\"[invoiceElement->Description]\\\\r\\\\n[period]Period: [invoiceElement->StartPeriod] till [invoiceElement->EndPeriod][\\/period]\",\"[invoiceElement->PriceExcl]\",\"[invoiceElement->NoDiscountAmountExcl]\"]]', 20, 127, 170, 90, 'all', 'helvetica', 10, '#000000', 'left', 0, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"20\"}},{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"\"}},{\"text\":{\"align\":\"right\"},\"positioning\":{\"w\":\"27\"},\"style\":{\"format\":\"money\"}},{\"text\":{\"align\":\"right\"},\"positioning\":{\"w\":\"27\"},\"style\":{\"format\":\"money\"}}]', '[{\"text\":{\"family\":\"\",\"size\":\"10\",\"style\":\"B\",\"color\":\"\",\"lineheight\":\"1.5\"},\"borders\":{\"bottom\":\"yes\",\"type\":\"solid\",\"thickness\":\"1\",\"color\":\"\"},\"style\":{\"bgcolor\":\"\"}},{\"text\":{\"family\":\"\",\"size\":\"10\",\"style\":\"\",\"color\":\"\",\"lineheight\":\"1.5\"},\"borders\":{\"type\":\"solid\",\"thickness\":\"\",\"color\":\"\"},\"style\":{\"bgcolor\":\"\",\"bgcolor_even\":\"\"}}]'),(:template_id, 'text', 'Credit invoice', 20, 63, 0, 0, 'all', 'helvetica', 24, '#000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', '[company->CompanyName]\\r\\n[company->Address]\\r\\n[company->Address2]\\r\\n[company->ZipCode] [company->City]\\r\\n[company->StateName]\\r\\n[company->CountryLong]\\r\\n\\r\\nTax number: [company->TaxNumber]\\r\\nBank account: [company->AccountNumber]', 130, 20, 60, 0, 'all', 'helvetica', 9, '#000', 'right', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'totals', '[[\"Page total\",\"[invoice->PageTotalExcl]\"],[\"Subtotal\",\"[invoice->AmountExcl]\"],[\"\"],[\"Invoice total\",\"[invoice->AmountIncl]\"]]', 130, 220, 60, 0, 'all', 'helvetica', 10, '#000', 'left', 1.75, '', 'yes', 'no', 'no', 'no', 1, '#000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"33\"}},{\"style\":{\"format\":\"money\"}}]', '[{\"style\":{\"totaltype\":\"pagetotalexcl\",\"bgcolor\":\"\"}},{\"style\":{\"totaltype\":\"amountexcl\",\"bgcolor\":\"\"}},{\"style\":{\"totaltype\":\"amounttax\",\"bgcolor\":\"\"}},{\"style\":{\"totaltype\":\"amountincl\",\"bgcolor\":\"\"},\"borders\":{\"top\":\"yes\",\"bottom\":\"yes\"}}]'),(:template_id, 'text', '[invoice->CompanyName]\\r\\n[invoice->Initials] [invoice->SurName]\\r\\n[invoice->Address]\\r\\n[invoice->Address2]\\r\\n[invoice->ZipCode] [invoice->City]\\r\\n[invoice->StateName]\\r\\n[invoice->CountryLong]', 20, 87, 0, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'table', '[[\"Client number:\",\"[debtor->DebtorCode]\"],[\"Invoice number:\",\"[invoice->InvoiceCode]\"],[\"Invoice date:\",\"[invoice->Date]\"]]', 130, 87, 60, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"30\"}},{\"text\":{\"align\":\"right\"},\"positioning\":{\"w\":\"\"}}]', '[]'),(:template_id, 'text', '[paid]This invoice has been paid.[/paid]\\r\\n[unpaid]Pay withing [invoice->Term] days after the invoice date (before [invoice->PayBefore]) to bank account number [company->AccountNumber] attn [company->CompanyName]. Please include your invoice number and client number on the bank transaction.[/unpaid]', 20, 260, 170, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]')";
                }
                Database_Model::getInstance()->rawQuery($sql_dump, ["template_id" => $template_id]);
                break;
            case "pricequote":
                if(LANGUAGE_CODE == "nl_NL") {
                    $sql_dump = "INSERT INTO `HostFact_TemplateBlocks` (`template_id`, `type`, `value`, `positioning_x`, `positioning_y`, `positioning_w`, `positioning_h`, `visibility`, `text_family`, `text_size`, `text_color`, `text_align`, `text_lineheight`, `text_style`, `borders_top`, `borders_right`, `borders_bottom`, `borders_left`, `borders_thickness`, `borders_color`, `borders_type`, `style_bgcolor`, `cols`, `rows`) VALUES                     (:template_id, 'invoicelines', '[[\"Aantal\",\"Omschrijving\",\"Prijs per stuk\",\"Bedrag\"],[\"[invoiceElement->Number]\",\"[invoiceElement->Description]\\\\r\\\\n[period]Periode: [invoiceElement->StartPeriod] tot [invoiceElement->EndPeriod][\\/period]\",\"[invoiceElement->PriceExcl]\",\"[invoiceElement->NoDiscountAmountExcl]\"]]', 20, 127, 170, 90, 'all', 'helvetica', 10, '#000000', 'left', 0, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"20\"}},{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"\"}},{\"text\":{\"align\":\"right\"},\"positioning\":{\"w\":\"27\"},\"style\":{\"format\":\"money\"}},{\"text\":{\"align\":\"right\"},\"positioning\":{\"w\":\"27\"},\"style\":{\"format\":\"money\"}}]', '[{\"text\":{\"family\":\"\",\"size\":\"10\",\"style\":\"B\",\"color\":\"\",\"lineheight\":\"1.5\"},\"borders\":{\"bottom\":\"yes\",\"type\":\"solid\",\"thickness\":\"1\",\"color\":\"\"},\"style\":{\"bgcolor\":\"\"}},{\"text\":{\"family\":\"\",\"size\":\"10\",\"style\":\"\",\"color\":\"\",\"lineheight\":\"1.75\"},\"borders\":{\"type\":\"solid\",\"thickness\":\"\",\"color\":\"\"},\"style\":{\"bgcolor\":\"\",\"bgcolor_even\":\"\"}}]'),                    (:template_id, 'text', 'Offerte', 20, 63, 0, 0, 'all', 'helvetica', 24, '#000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),                    (:template_id, 'text', '[company->CompanyName]\\r\\n[company->Address]\\r\\n[company->ZipCode]  [company->City]\\r\\n[company->CountryLong]\\r\\n\\r\\nE-mail: [company->EmailAddress]\\r\\nWebsite: [company->Website]\\r\\n\\r\\nKvK nummer: [company->CompanyNumber]\\r\\nBTW nummer: [company->TaxNumber]\\r\\nIBAN: [company->AccountNumber]\\r\\nBIC: [company->AccountBIC]', 110, 20, 80, 0, 'all', 'helvetica', 9, '#000', 'right', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),                    (:template_id, 'totals', '[[\"Paginatotaal excl\",\"[invoice->PageTotalExcl]\"],[\"Totaal excl. BTW\",\"[invoice->AmountExcl]\"],[\"\"],[\"Totaal incl. BTW\",\"[invoice->AmountIncl]\"]]', 130, 220, 60, 0, 'all', 'helvetica', 10, '#000', 'left', 1.75, '', 'yes', 'no', 'no', 'no', 1, '#000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"33\"}},{\"style\":{\"format\":\"money\"}}]', '[{\"style\":{\"totaltype\":\"pagetotalexcl\",\"bgcolor\":\"\"}},{\"style\":{\"totaltype\":\"amountexcl\",\"bgcolor\":\"\"}},{\"style\":{\"totaltype\":\"amounttax\",\"bgcolor\":\"\"}},{\"style\":{\"totaltype\":\"amountincl\",\"bgcolor\":\"\"},\"borders\":{\"top\":\"yes\",\"bottom\":\"yes\"}}]'),                    (:template_id, 'text', '[reversecharge]BTW verlegd: [debtor->TaxNumber][/reversecharge]', 20, 222, 0, 0, 'last', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),                    (:template_id, 'text', '[invoice->CompanyName]\\r\\n[invoice->Initials] [invoice->SurName]\\r\\n[invoice->Address]\\r\\n[invoice->ZipCode]  [invoice->City]\\r\\n[invoice->CountryLong]', 20, 87, 0, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),                    (:template_id, 'table', '[[\"Klantnummer:\",\"[debtor->DebtorCode]\"],[\"Offertenummer:\",\"[invoice->PriceQuoteCode]\"],[\"Offertedatum:\",\"[invoice->Date]\"]]', 130, 97, 60, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"30\"}},{\"text\":{\"align\":\"right\"},\"positioning\":{\"w\":\"\"}}]', '[]'),                    (:template_id, 'text', 'De offerte is [invoice->Term] dagen geldig na offertedatum.', 20, 260, 170, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]')";
                } else {
                    $sql_dump = "INSERT INTO `HostFact_TemplateBlocks` (`template_id`, `type`, `value`, `positioning_x`, `positioning_y`, `positioning_w`, `positioning_h`, `visibility`, `text_family`, `text_size`, `text_color`, `text_align`, `text_lineheight`, `text_style`, `borders_top`, `borders_right`, `borders_bottom`, `borders_left`, `borders_thickness`, `borders_color`, `borders_type`, `style_bgcolor`, `cols`, `rows`) VALUES (:template_id, 'invoicelines', '[[\"Quantity\",\"Description\",\"Price per unit\",\"Amount\"],[\"[invoiceElement->Number]\",\"[invoiceElement->Description]\\\\r\\\\n[period]Period: [invoiceElement->StartPeriod] till [invoiceElement->EndPeriod][\\/period]\",\"[invoiceElement->PriceExcl]\",\"[invoiceElement->NoDiscountAmountExcl]\"]]', 20, 127, 170, 90, 'all', 'helvetica', 10, '#000000', 'left', 0, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"20\"}},{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"\"}},{\"text\":{\"align\":\"right\"},\"positioning\":{\"w\":\"27\"},\"style\":{\"format\":\"money\"}},{\"text\":{\"align\":\"right\"},\"positioning\":{\"w\":\"27\"},\"style\":{\"format\":\"money\"}}]', '[{\"text\":{\"family\":\"\",\"size\":\"10\",\"style\":\"B\",\"color\":\"\",\"lineheight\":\"1.5\"},\"borders\":{\"bottom\":\"yes\",\"type\":\"solid\",\"thickness\":\"1\",\"color\":\"\"},\"style\":{\"bgcolor\":\"\"}},{\"text\":{\"family\":\"\",\"size\":\"10\",\"style\":\"\",\"color\":\"\",\"lineheight\":\"1.75\"},\"borders\":{\"type\":\"solid\",\"thickness\":\"\",\"color\":\"\"},\"style\":{\"bgcolor\":\"\",\"bgcolor_even\":\"\"}}]'),(:template_id, 'text', 'Estimate', 20, 63, 0, 0, 'all', 'helvetica', 24, '#000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', '[company->CompanyName]\\r\\n[company->Address]\\r\\n[company->Address2]\\r\\n[company->ZipCode] [company->City]\\r\\n[company->StateName]\\r\\n[company->CountryLong]\\r\\n\\r\\nTax number: [company->TaxNumber]\\r\\nBank account: [company->AccountNumber]', 130, 20, 60, 0, 'all', 'helvetica', 9, '#000', 'right', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'totals', '[[\"Page total\",\"[invoice->PageTotalExcl]\"],[\"Subtotal\",\"[invoice->AmountExcl]\"],[\"\"],[\"Estimate total\",\"[invoice->AmountIncl]\"]]', 130, 220, 60, 0, 'all', 'helvetica', 10, '#000', 'left', 1.75, '', 'yes', 'no', 'no', 'no', 1, '#000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"33\"}},{\"style\":{\"format\":\"money\"}}]', '[{\"style\":{\"totaltype\":\"pagetotalexcl\",\"bgcolor\":\"\"}},{\"style\":{\"totaltype\":\"amountexcl\",\"bgcolor\":\"\"}},{\"style\":{\"totaltype\":\"amounttax\",\"bgcolor\":\"\"}},{\"style\":{\"totaltype\":\"amountincl\",\"bgcolor\":\"\"},\"borders\":{\"top\":\"yes\",\"bottom\":\"yes\"}}]'),(:template_id, 'text', '[invoice->CompanyName]\\r\\n[invoice->Initials] [invoice->SurName]\\r\\n[invoice->Address]\\r\\n[invoice->Address2]\\r\\n[invoice->ZipCode] [invoice->City]\\r\\n[invoice->StateName]\\r\\n[invoice->CountryLong]', 20, 87, 0, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'table', '[[\"Client number:\",\"[debtor->DebtorCode]\"],[\"Estimate number:\",\"[invoice->PriceQuoteCode]\"],[\"Estimate date:\",\"[invoice->Date]\"]]', 130, 87, 60, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"30\"}},{\"text\":{\"align\":\"right\"},\"positioning\":{\"w\":\"\"}}]', '[]'),(:template_id, 'text', 'The estimate is valid for [invoice->Term] days.', 20, 260, 170, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]')";
                }
                Database_Model::getInstance()->rawQuery($sql_dump, ["template_id" => $template_id]);
                break;
            case "reminder":
                if(LANGUAGE_CODE == "nl_NL") {
                    $sql_dump = "INSERT INTO `HostFact_TemplateBlocks` (`template_id`, `type`, `value`, `positioning_x`, `positioning_y`, `positioning_w`, `positioning_h`, `visibility`, `text_family`, `text_size`, `text_color`, `text_align`, `text_lineheight`, `text_style`, `borders_top`, `borders_right`, `borders_bottom`, `borders_left`, `borders_thickness`, `borders_color`, `borders_type`, `style_bgcolor`, `cols`, `rows`) VALUES(:template_id, 'text', 'Herinnering', 20, 63, 0, 0, 'all', 'helvetica', 24, '#000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', '[company->CompanyName]\\r\\n[company->Address]\\r\\n[company->ZipCode]  [company->City]\\r\\n[company->CountryLong]\\r\\n\\r\\nE-mail: [company->EmailAddress]\\r\\nWebsite: [company->Website]\\r\\n\\r\\nKvK nummer: [company->CompanyNumber]\\r\\nBTW nummer: [company->TaxNumber]\\r\\nIBAN: [company->AccountNumber]\\r\\nBIC: [company->AccountBIC]', 110, 20, 80, 0, 'all', 'helvetica', 9, '#000', 'right', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', '[invoice->CompanyName]\\r\\n[invoice->Initials] [invoice->SurName]\\r\\n[invoice->Address]\\r\\n[invoice->ZipCode]  [invoice->City]\\r\\n[invoice->CountryLong]', 20, 87, 0, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', 'Geachte [debtor->Initials] [debtor->SurName],\\r\\n\\r\\nEnige tijd geleden heeft u van ons de factuur [invoice->InvoiceCode] ontvangen voor de afgenomen diensten. Helaas hebben we nog geen betaling van het verschuldigde bedrag ontvangen.\\r\\n\\r\\nWij vragen u daarom het totaalbedrag van € [invoice->AmountIncl] binnen [invoice->Term] dagen te voldoen, met vermelding van uw klantnummer en factuurnummer. \\r\\n\\r\\nMet vriendelijke groet,\\r\\n\\r\\n[company->CompanyName]', 20, 130, 170, 0, 'all', 'helvetica', 10, '#000000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[]', '[]')";
                } else {
                    $sql_dump = "INSERT INTO `HostFact_TemplateBlocks` (`template_id`, `type`, `value`, `positioning_x`, `positioning_y`, `positioning_w`, `positioning_h`, `visibility`, `text_family`, `text_size`, `text_color`, `text_align`, `text_lineheight`, `text_style`, `borders_top`, `borders_right`, `borders_bottom`, `borders_left`, `borders_thickness`, `borders_color`, `borders_type`, `style_bgcolor`, `cols`, `rows`) VALUES (:template_id, 'text', 'Reminder', 20, 63, 0, 0, 'all', 'helvetica', 24, '#000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', '[company->CompanyName]\\r\\n[company->Address]\\r\\n[company->Address2]\\r\\n[company->ZipCode] [company->City]\\r\\n[company->StateName]\\r\\n[company->CountryLong]\\r\\n\\r\\nTax number: [company->TaxNumber]\\r\\nBank account: [company->AccountNumber]', 130, 20, 60, 0, 'all', 'helvetica', 9, '#000', 'right', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', '[invoice->CompanyName]\\r\\n[invoice->Initials] [invoice->SurName]\\r\\n[invoice->Address]\\r\\n[invoice->Address2]\\r\\n[invoice->ZipCode] [invoice->City]\\r\\n[invoice->StateName] \\r\\n[invoice->CountryLong]', 20, 87, 0, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', 'Dear [debtor->Initials] [debtor->SurName],\\r\\n\\r\\nRecently you received the invoice [invoice->InvoiceCode] from us for the services. Unfortunately we have not yet received a payment.\\r\\n\\r\\nWe therefore kindly ask you to pay the outstanding amount of [invoice->AmountIncl] within [invoice->Term] days. Please include your client number and invoice number in your payment. \\r\\n\\r\\nKind regards,\\r\\n\\r\\n[company->CompanyName]', 20, 130, 170, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]')";
                }
                Database_Model::getInstance()->rawQuery($sql_dump, ["template_id" => $template_id]);
                break;
            case "summation":
                if(LANGUAGE_CODE == "nl_NL") {
                    $sql_dump = "INSERT INTO `HostFact_TemplateBlocks` (`template_id`, `type`, `value`, `positioning_x`, `positioning_y`, `positioning_w`, `positioning_h`, `visibility`, `text_family`, `text_size`, `text_color`, `text_align`, `text_lineheight`, `text_style`, `borders_top`, `borders_right`, `borders_bottom`, `borders_left`, `borders_thickness`, `borders_color`, `borders_type`, `style_bgcolor`, `cols`, `rows`) VALUES                     (:template_id, 'text', 'Aanmaning', 20, 63, 0, 0, 'all', 'helvetica', 24, '#000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', '[company->CompanyName]\\r\\n[company->Address]\\r\\n[company->ZipCode]  [company->City]\\r\\n[company->CountryLong]\\r\\n\\r\\nE-mail: [company->EmailAddress]\\r\\nWebsite: [company->Website]\\r\\n\\r\\nKvK nummer: [company->CompanyNumber]\\r\\nBTW nummer: [company->TaxNumber]\\r\\nIBAN: [company->AccountNumber]\\r\\nBIC: [company->AccountBIC]', 110, 20, 80, 0, 'all', 'helvetica', 9, '#000', 'right', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', '[invoice->CompanyName]\\r\\n[invoice->Initials] [invoice->SurName]\\r\\n[invoice->Address]\\r\\n[invoice->ZipCode]  [invoice->City]\\r\\n[invoice->CountryLong]', 20, 87, 0, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', 'Geachte [debtor->Initials] [debtor->SurName],\\r\\n\\r\\nEnige tijd geleden heeft u van ons de factuur [invoice->InvoiceCode] ontvangen voor de afgenomen diensten. Ondanks herhaaldelijke herinneringen heeft u het verschuldigde bedrag niet voldaan op onze bankrekening.\\r\\n\\r\\nWij vragen u daarom een laatste keer het totaal verschuldigde bedrag van € [invoice->AmountIncl] te voldoen, met vermelding van uw klantnummer en factuurnummer. Indien wij de betaling niet binnen [invoice->Term] dagen hebben ontvangen, zullen wij genoodzaakt zijn de vordering neer te leggen bij een incassobureau.\\r\\n\\r\\nMet vriendelijke groet,\\r\\n\\r\\n[company->CompanyName]', 20, 130, 170, 0, 'all', 'helvetica', 10, '#000000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[]', '[]')";
                } else {
                    $sql_dump = "INSERT INTO `HostFact_TemplateBlocks` (`template_id`, `type`, `value`, `positioning_x`, `positioning_y`, `positioning_w`, `positioning_h`, `visibility`, `text_family`, `text_size`, `text_color`, `text_align`, `text_lineheight`, `text_style`, `borders_top`, `borders_right`, `borders_bottom`, `borders_left`, `borders_thickness`, `borders_color`, `borders_type`, `style_bgcolor`, `cols`, `rows`) VALUES (:template_id, 'text', 'Reminder', 20, 63, 0, 0, 'all', 'helvetica', 24, '#000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', '[company->CompanyName]\\r\\n[company->Address]\\r\\n[company->Address2]\\r\\n[company->ZipCode] [company->City]\\r\\n[company->StateName]\\r\\n[company->CountryLong]\\r\\n\\r\\nTax number: [company->TaxNumber]\\r\\nBank account: [company->AccountNumber]', 130, 20, 60, 0, 'all', 'helvetica', 9, '#000', 'right', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', '[invoice->CompanyName]\\r\\n[invoice->Initials] [invoice->SurName]\\r\\n[invoice->Address]\\r\\n[invoice->Address2]\\r\\n[invoice->ZipCode] [invoice->City]\\r\\n[invoice->StateName] \\r\\n[invoice->CountryLong]', 20, 87, 0, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', 'Dear [debtor->Initials] [debtor->SurName],\\r\\n\\r\\nRecently you received the invoice [invoice->InvoiceCode] from us for the services. Unfortunately we have not yet received a payment.\\r\\n\\r\\nWe therefore kindly ask you to pay the outstanding amount of [invoice->AmountIncl] within [invoice->Term] days. Please include your client number and invoice number in your payment. \\r\\n\\r\\nKind regards,\\r\\n\\r\\n[company->CompanyName]', 20, 130, 170, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]')";
                }
                Database_Model::getInstance()->rawQuery($sql_dump, ["template_id" => $template_id]);
                break;
            case "hostingdata":
                if(LANGUAGE_CODE == "nl_NL") {
                    $sql_dump = "INSERT INTO `HostFact_TemplateBlocks` (`template_id`, `type`, `value`, `positioning_x`, `positioning_y`, `positioning_w`, `positioning_h`, `visibility`, `text_family`, `text_size`, `text_color`, `text_align`, `text_lineheight`, `text_style`, `borders_top`, `borders_right`, `borders_bottom`, `borders_left`, `borders_thickness`, `borders_color`, `borders_type`, `style_bgcolor`, `cols`, `rows`) VALUES                     (:template_id, 'text', 'Gegevens webhostingpakket', 20, 63, 0, 0, 'all', 'helvetica', 24, '#000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', '[company->CompanyName]\\r\\n[company->Address]\\r\\n[company->ZipCode]  [company->City]\\r\\n[company->CountryLong]\\r\\n\\r\\nE-mail: [company->EmailAddress]\\r\\nWebsite: [company->Website]\\r\\n\\r\\nKvK nummer: [company->CompanyNumber]\\r\\nBTW nummer: [company->TaxNumber]\\r\\nIBAN: [company->AccountNumber]\\r\\nBIC: [company->AccountBIC]', 110, 20, 80, 0, 'all', 'helvetica', 9, '#000', 'right', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', '[debtor->CompanyName]\\r\\n[debtor->Initials] [debtor->SurName]\\r\\n[debtor->Address]\\r\\n[debtor->ZipCode]  [debtor->City]\\r\\n[debtor->CountryLong]', 20, 87, 0, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', 'Geachte [debtor->Initials] [debtor->SurName],\\r\\n\\r\\nUw hostingpakket is aangemaakt en gereed voor gebruik!', 20, 130, 170, 0, 'all', 'helvetica', 10, '#000000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[]', '[]'),(:template_id, 'text', 'Indien u nog vragen heeft of problemen ondervindt bij het inloggen, neem dan contact met ons op!\\r\\n\\r\\nMet vriendelijke groet,\\r\\n\\r\\n[company->CompanyName]', 20, 174, 170, 0, 'all', 'helvetica', 10, '#000000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[]', '[]'),(:template_id, 'text', 'Hostingpakket gegevens', 20, 148, 0, 0, 'all', 'helvetica', 10, '#000000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[]', '[]'),(:template_id, 'table', '[[\"Controle paneel:\",\"http:\\/\\/[hosting->Domain]:[server->Port]\\/\"],[\"Gebruikersnaam:\",\"[hosting->Username]\"],[\"Wachtwoord:\",\"[hosting->Password]\"],[\"Domeinnaam:\",\"[hosting->Domain]\"]]', 20, 153, 170, 0, 'all', 'helvetica', 10, '#000000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"35\"}},{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"\"}}]', '[]')";
                } else {
                    $sql_dump = "INSERT INTO `HostFact_TemplateBlocks` (`template_id`, `type`, `value`, `positioning_x`, `positioning_y`, `positioning_w`, `positioning_h`, `visibility`, `text_family`, `text_size`, `text_color`, `text_align`, `text_lineheight`, `text_style`, `borders_top`, `borders_right`, `borders_bottom`, `borders_left`, `borders_thickness`, `borders_color`, `borders_type`, `style_bgcolor`, `cols`, `rows`) VALUES (:template_id, 'text', 'Hosting account information', 20, 63, 0, 0, 'all', 'helvetica', 24, '#000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', '[company->CompanyName]\\r\\n[company->Address]\\r\\n[company->Address2]\\r\\n[company->ZipCode] [company->City]\\r\\n[company->StateName]\\r\\n[company->CountryLong]\\r\\n\\r\\nTax number: [company->TaxNumber]\\r\\nBank account: [company->AccountNumber]', 130, 20, 60, 0, 'all', 'helvetica', 9, '#000', 'right', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', '[debtor->CompanyName]\\r\\n[debtor->Initials] [debtor->SurName]\\r\\n[debtor->Address]\\r\\n[debtor->Address2]\\r\\n[debtor->ZipCode] [debtor->City]\\r\\n[debtor->StateName] \\r\\n[debtor->CountryLong]', 20, 87, 0, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', 'Dear [debtor->Initials] [debtor->SurName],\\r\\n\\r\\nYour hosting account has been created and is ready for use!', 20, 130, 170, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', 'If you have any questions or have problems logging in, then please contact us!\\r\\n\\r\\nKind regards,\\r\\n\\r\\n[company->CompanyName]', 20, 174, 170, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'text', 'Hosting account details', 20, 148, 0, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, 'B', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[]', '[]'),(:template_id, 'table', '[[\"Control panel:\",\"http:\\/\\/[hosting->Domain]:[server->Port]\\/\"],[\"Username:\",\"[hosting->Username]\"],[\"Password:\",\"[hosting->Password]\"],[\"Domain name:\",\"[hosting->Domain]\"]]', 20, 153, 170, 0, 'all', 'helvetica', 10, '#000', 'left', 1.25, '', 'no', 'no', 'no', 'no', 1, '#000', 'solid', '', '[{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"35\"}},{\"text\":{\"align\":\"left\"},\"positioning\":{\"w\":\"\"}}]', '[]')";
                }
                Database_Model::getInstance()->rawQuery($sql_dump, ["template_id" => $template_id]);
                break;
        }
    }
    public function saveBlock($block_id, $block)
    {
        if(is_array($block["cols"])) {
            $new_cols = [];
            foreach ($block["cols"] as $k => $v) {
                if($v["text"]["align"] == "money_left") {
                    $v["text"]["align"] = "left";
                    $v["style"]["format"] = "money";
                } elseif($v["text"]["align"] == "money_right") {
                    $v["text"]["align"] = "right";
                    $v["style"]["format"] = "money";
                } elseif($v["text"]["align"] == "money") {
                    $v["text"]["align"] = "right";
                    $v["style"]["format"] = "money";
                }
                $new_cols[] = $v;
            }
            $block["cols"] = $new_cols;
        }
        if(is_array($block["rows"])) {
            $new_rows = [];
            foreach ($block["rows"] as $k => $v) {
                if(isset($v["style"]["totaltype"]) && $v["style"]["totaltype"]) {
                    $block["value"][$k][1] = $this->_getDefaultTotalVariable($v["style"]["totaltype"]);
                }
                $new_rows[] = $v;
            }
            $block["rows"] = $new_rows;
        }
        if(is_array($block["value"])) {
            $new_value = [];
            foreach ($block["value"] as $tmp_row) {
                $tmp_row_value = [];
                foreach ($tmp_row as $tmp_cell) {
                    $tmp_row_value[] = esc($tmp_cell);
                }
                $new_value[] = $tmp_row_value;
            }
            $block["value"] = $new_value;
        }
        if($block["type"] == "totals") {
            $block["cols"][] = ["style" => ["format" => "money"]];
        }
        if($block["type"] == "image" && !$block["positioning"]["w"]) {
            $img_info = @getimagesize($block["value"]);
            if(!empty($img_info)) {
                $block["positioning"]["w"] = $this->px_to_mm($img_info[0], true);
                if(210 < $block["positioning"]["w"]) {
                    $block["positioning"]["w"] = 210;
                }
            } else {
                $block["positioning"]["w"] = "50";
            }
        }
        $current_block = Database_Model::getInstance()->getOne("HostFact_TemplateBlocks", ["value"])->where("id", $block_id)->execute();
        if($block["type"] === "qrcode") {
            $block["positioning"]["w"] = max(30, (int) $block["positioning"]["w"]);
            $block["positioning"]["h"] = $block["positioning"]["w"];
        }
        $result = Database_Model::getInstance()->update("HostFact_TemplateBlocks", ["value" => is_array($block["value"]) ? json_encode($block["value"]) : $block["value"], "positioning_x" => number2db($block["positioning"]["x"]), "positioning_y" => number2db($block["positioning"]["y"]), "positioning_w" => number2db($block["positioning"]["w"]), "positioning_h" => number2db($block["positioning"]["h"]), "visibility" => $block["visibility"], "text_family" => $block["text"]["family"], "text_size" => $block["text"]["size"], "text_color" => $block["text"]["color"], "text_align" => $block["text"]["align"], "text_lineheight" => $block["text"]["lineheight"], "text_style" => $block["text"]["style"], "borders_top" => $block["borders"]["top"], "borders_right" => $block["borders"]["right"], "borders_bottom" => $block["borders"]["bottom"], "borders_left" => $block["borders"]["left"], "borders_thickness" => $block["borders"]["thickness"], "borders_color" => $block["borders"]["color"], "borders_type" => $block["borders"]["type"], "style_bgcolor" => $block["style"]["bgcolor"], "cols" => json_encode($block["cols"]), "rows" => json_encode($block["rows"])])->where("id", $block_id)->execute();
        if($result) {
            if($block["type"] == "image" && $block["value"] != $current_block->value && strpos($current_block->value, DIR_PDF_FILES . "images/") !== false) {
                $result_blocks = Database_Model::getInstance()->get("HostFact_TemplateBlocks", "id")->where("value", $current_block->value)->execute();
                if(empty($result_blocks) && @file_exists($current_block->value)) {
                    @unlink($current_block->value);
                }
            }
            $this->Success[] = __("template block adjusted");
            return true;
        }
        $this->Error[] = __("template block not adjusted");
        return false;
    }
    public function deleteBlock($block_id)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_TemplateBlocks", ["type", "value"])->where("id", $block_id)->execute();
        if($result) {
            if(in_array($result->type, ["invoicelines", "totals"])) {
                Database_Model::getInstance()->update("HostFact_TemplateBlocks", ["visibility" => "none"])->where("id", $block_id)->execute();
                return true;
            }
            if($result->type) {
                Database_Model::getInstance()->delete("HostFact_TemplateBlocks")->where("id", $block_id)->execute();
            }
            if($result->type == "image" && strpos($result->value, DIR_PDF_FILES . "images/") !== false) {
                $result_blocks = Database_Model::getInstance()->get("HostFact_TemplateBlocks", "id")->where("value", $result->value)->execute();
                if(empty($result_blocks) && @file_exists($result->value)) {
                    @unlink($result->value);
                }
            }
        }
        return true;
    }
    public function savePosition($block_id, $x, $y)
    {
        $result = Database_Model::getInstance()->update("HostFact_TemplateBlocks", ["positioning_x" => $this->px_to_mm($x, false), "positioning_y" => $this->px_to_mm($y, false)])->where("id", $block_id)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function saveSize($block_id, $w, $h)
    {
        $width = $this->px_to_mm($w, false);
        $height = $this->px_to_mm($h, false);
        $block = $this->getBlock($block_id);
        if($block["type"] === "qrcode") {
            $width = max(30, (int) $width);
            $height = $width;
        }
        $result = Database_Model::getInstance()->update("HostFact_TemplateBlocks", ["positioning_w" => $width, "positioning_h" => $height])->where("id", $block_id)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function getAvailableFonts()
    {
        $font_array = ["default" => [], "custom" => []];
        $font_array["default"]["helvetica"] = ["title" => "helvetica", "filename" => "Helvetica"];
        $font_array["default"]["freeserif"] = ["title" => "freeserif", "filename" => "FreeSerif"];
        $customFontPath = static::CUSTOM_FONT_PATH;
        if(is_dir($customFontPath)) {
            $files = scandir($customFontPath);
            foreach ($files as $_filename) {
                if(substr(strtolower($_filename), -4) == ".ttf") {
                    $fontPath = TCPDF_FONTS::_getfontpath();
                    $font_path_parts = pathinfo($_filename);
                    if(!isset($font_path_parts["filename"])) {
                        $font_path_parts["filename"] = substr($font_path_parts["basename"], 0, -1 * (strlen($font_path_parts["extension"]) + 1));
                    }
                    $font_name = strtolower($font_path_parts["filename"]);
                    $font_name = preg_replace("/[^a-z0-9_]/", "", $font_name);
                    $search = ["bold", "oblique", "italic", "regular"];
                    $replace = ["b", "i", "i", ""];
                    $font_name = str_replace($search, $replace, $font_name);
                    if(empty($font_name)) {
                        $font_name = "tcpdffont";
                    }
                    if(!file_exists($fontPath . $font_name . ".php")) {
                        $pdf = new TCPDF_FONTS();
                        $pdf->addTTFfont($customFontPath . $_filename, "", "", 32);
                    }
                    $tmp_filename = rtrim(strtolower($_filename));
                    $tmp_filename = preg_replace("/[^a-z0-9_.]/", "", $tmp_filename);
                    $search = ["bold", "oblique", "italic", "regular"];
                    $replace = ["b", "i", "i", ""];
                    $tmp_filename = str_replace($search, $replace, $tmp_filename);
                    $font_array["custom"][str_replace(".ttf", "", $tmp_filename)] = ["filename" => $_filename, "title" => $tmp_filename];
                }
            }
        }
        if(isset($font_array["custom"]) && is_array($font_array["custom"])) {
            ksort($font_array["custom"]);
        }
        return $font_array;
    }
    private function checkMinMaxBlockposition($block)
    {
        if(0 < $block["positioning"]["x"]) {
            $block["positioning"]["x"] = PDF_PAGE_WIDTH - 10 < $block["positioning"]["x"] ? PDF_PAGE_WIDTH - 10 : $block["positioning"]["x"];
        } elseif($block["positioning"]["x"] < 0 && empty($block["positioning"]["w"]) && isset($block["text"]["align"]) && $block["text"]["align"] == "right") {
            $block["positioning"]["x"] = 0;
        } elseif(0 < $block["positioning"]["w"]) {
            $block["positioning"]["x"] = $block["positioning"]["x"] < 0 - ($block["positioning"]["w"] - 10) ? 0 - ($block["positioning"]["w"] - 10) : $block["positioning"]["x"];
        }
        if(0 < $block["positioning"]["y"]) {
            $block["positioning"]["y"] = PDF_PAGE_HEIGHT - 10 < $block["positioning"]["y"] ? PDF_PAGE_HEIGHT - 10 : $block["positioning"]["y"];
        } elseif(0 < $block["positioning"]["h"]) {
            $block["positioning"]["y"] = $block["positioning"]["y"] < 0 - ($block["positioning"]["h"] - 10) ? 0 - ($block["positioning"]["h"] - 10) : $block["positioning"]["y"];
        }
        return $block;
    }
    public function show_template_block($block, $block_id)
    {
        $block_css = "";
        $block = $this->checkMinMaxBlockposition($block);
        if($block["positioning"]["x"]) {
            if($block["positioning"]["x"] < 0 && empty($block["positioning"]["w"])) {
                $left = $this->mm_to_px(PDF_PAGE_WIDTH - 10);
                $block_css .= "right:" . $left . "px;";
            } else {
                $left = $this->mm_to_px($block["positioning"]["x"]);
                $block_css .= "left:" . $left . "px;";
            }
        }
        if($block["positioning"]["y"]) {
            if($block["positioning"]["y"] < 0 && empty($block["positioning"]["h"])) {
                $top = $this->mm_to_px(PDF_PAGE_HEIGHT - 10);
                $block_css .= "bottom:" . $top . "px;";
            } else {
                $top = $this->mm_to_px($block["positioning"]["y"]);
                $block_css .= "top:" . $top . "px;";
            }
        }
        if($block["positioning"]["w"]) {
            $width = $this->mm_to_px($block["positioning"]["w"]);
            if(!in_array($block["type"], ["invoicelines", "totals", "table"])) {
                $width = $width - $this->TEMPLATE_BLOCK_PADDING - $this->TEMPLATE_BLOCK_PADDING;
            }
            $block_css .= "width:" . $width . "px;";
        } elseif(isset($block["text"]["align"]) && $block["text"]["align"] == "right") {
            $width = 0 < $block["positioning"]["x"] && $block["positioning"]["x"] < PDF_PAGE_WIDTH - 10 ? $this->mm_to_px(PDF_PAGE_WIDTH - 10 - $block["positioning"]["x"]) : $this->mm_to_px(PDF_PAGE_WIDTH - 10);
            $block_css .= "width:" . $width . "px;";
        }
        if($block["positioning"]["h"]) {
            $height = $this->mm_to_px($block["positioning"]["h"]);
            if(!in_array($block["type"], ["invoicelines", "totals", "table"])) {
                $height = $height - $this->TEMPLATE_BLOCK_PADDING - $this->TEMPLATE_BLOCK_PADDING;
            }
            $block_css .= "height:" . $height . "px;";
        }
        $text_css = "";
        if($block["text"]["family"]) {
            $text_css .= "font-family:" . $block["text"]["family"] . ";";
        }
        if($block["text"]["size"]) {
            $text_css .= "font-size:" . $block["text"]["size"] * $this->TEMPLATE_FONT_SIZE_ADJUSTER . "pt;";
        }
        if($block["text"]["color"]) {
            $text_css .= "color:" . $block["text"]["color"] . ";";
        }
        if($block["text"]["align"]) {
            $text_css .= "text-align:" . $block["text"]["align"] . ";";
        }
        if($block["text"]["lineheight"]) {
            $text_css .= "line-height:" . $block["text"]["lineheight"] . "em;";
        }
        if($block["text"]["style"]) {
            if(strpos($block["text"]["style"], "B") !== false) {
                $text_css .= "font-weight:bold;";
            }
            if(strpos($block["text"]["style"], "I") !== false) {
                $text_css .= "font-style:italic;";
            }
            if(strpos($block["text"]["style"], "U") !== false) {
                $text_css .= "text-decoration:underline;";
            }
        }
        $block_css .= $text_css;
        $border_css = "";
        if($block["borders"]["top"] == "yes") {
            $border_css .= "border-top:" . $block["borders"]["thickness"] . "px " . $block["borders"]["type"] . " " . $block["borders"]["color"] . ";";
        }
        if($block["borders"]["right"] == "yes") {
            $border_css .= "border-right:" . $block["borders"]["thickness"] . "px " . $block["borders"]["type"] . " " . $block["borders"]["color"] . ";";
        }
        if($block["borders"]["bottom"] == "yes") {
            $border_css .= "border-bottom:" . $block["borders"]["thickness"] . "px " . $block["borders"]["type"] . " " . $block["borders"]["color"] . ";";
        }
        if($block["borders"]["left"] == "yes") {
            $border_css .= "border-left:" . $block["borders"]["thickness"] . "px " . $block["borders"]["type"] . " " . $block["borders"]["color"] . ";";
        }
        if($block["type"] != "totals") {
            $block_css .= $border_css;
            $border_css = "";
        }
        if($block["style"]["bgcolor"]) {
            $block_css .= "background-color:" . $block["style"]["bgcolor"] . ";";
        }
        echo "\t\t<div id=\"template_block_";
        echo $block_id;
        echo "\" style=\"";
        echo $block_css;
        echo "\" class=\"template_block block_";
        echo $block["type"];
        echo "\">\n\t\t\t";
        if($block["type"] == "image") {
            if($block["value"]) {
                echo "<img src=\"";
                echo htmlspecialchars($block["value"]);
                echo "\" alt=\"\" style=\"";
                if($block["positioning"]["w"]) {
                    echo "width:100%;";
                }
                if($block["positioning"]["h"]) {
                    echo "height:100%;";
                }
                echo "\"/>";
            }
        } elseif($block["type"] == "table" || $block["type"] == "totals" || $block["type"] == "invoicelines") {
            if($block["type"] == "invoicelines") {
                for ($ii = 2; $ii <= count($this->example_data["invoice"]->Elements); $ii++) {
                    $block["value"][$ii] = $block["value"][1];
                }
            }
            echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%;";
            echo $border_css . $text_css;
            echo "\">";
            $multi_row_zebra_offset = 0;
            $border_row_id = 0;
            foreach ($block["value"] as $row_id => $row) {
                $original_row_id = $row_id;
                $loop_rows = [$row];
                $loop_row_prevent_borders = false;
                if($block["type"] == "invoicelines" && 1 < $row_id) {
                    $row_id = 1;
                }
                if($block["type"] == "invoicelines") {
                    $example_data["invoiceElement"] = isset($this->example_data["invoiceElement"][$original_row_id - 1]) ? $this->example_data["invoiceElement"][$original_row_id - 1] : $this->example_data["invoiceElement"][0];
                    if(isset($example_data["invoiceElement"]["DiscountPercentage"]) && 0 < $example_data["invoiceElement"]["DiscountPercentage"] && strpos(implode(";", $loop_rows[0]), "[linediscount]") === false) {
                        $loop_rows = [];
                        $loop_rows[] = $row;
                        $loop_rows[] = $row;
                        $loop_row_prevent_borders = true;
                    }
                } elseif($block["type"] == "totals" && isset($block["rows"][$original_row_id]["style"]["totaltype"]) && $block["rows"][$original_row_id]["style"]["totaltype"] == "amounttax") {
                    $loop_rows = [];
                    if(isset($this->example_data["invoice"]->used_taxrates) && is_array($this->example_data["invoice"]->used_taxrates)) {
                        foreach ($this->example_data["invoice"]->used_taxrates as $tax_rate => $tax_info) {
                            global $array_taxpercentages_info;
                            $loop_rows[] = [isset($array_taxpercentages_info[(string) (double) $tax_rate]["label"]) ? $array_taxpercentages_info[(string) (double) $tax_rate]["label"] : "", $tax_info["AmountTax"]];
                        }
                    }
                    if(isset($this->example_data["invoice"]->TaxRate) && 0 < $this->example_data["invoice"]->TaxRate) {
                        $loop_rows[] = [$this->example_data["invoice"]->TaxRate_Label, $this->example_data["invoice"]->TaxRate_Amount];
                    }
                }
                foreach ($loop_rows as $tmp_row_id => $tmp_row) {
                    if(0 < $tmp_row_id && $loop_row_prevent_borders === false) {
                        $multi_row_zebra_offset++;
                    }
                    $zebra_odd_even = ($original_row_id + $multi_row_zebra_offset) % 2 ? "odd" : "even";
                    $border_row_id++;
                    echo "<tr>";
                    foreach ($tmp_row as $col_id => $value) {
                        $is_money_format = isset($block["cols"][$col_id]["style"]["format"]) && $block["cols"][$col_id]["style"]["format"] == "money" && ($block["type"] != "invoicelines" || 0 < $row_id) ? true : false;
                        $td_css = $td_money_left = $td_money = $td_money_right = "";
                        if(isset($block["cols"][$col_id]["positioning"]["w"]) && $block["cols"][$col_id]["positioning"]["w"]) {
                            $td_css = "width:" . ($this->mm_to_px($block["cols"][$col_id]["positioning"]["w"]) - $this->TEMPLATE_BLOCK_PADDING - $this->TEMPLATE_BLOCK_PADDING) . "px;";
                        }
                        $tmp = "";
                        if(isset($block["rows"][$row_id]["text"]["family"]) && $block["rows"][$row_id]["text"]["family"]) {
                            $tmp = "font-family:" . $block["rows"][$row_id]["text"]["family"] . ";";
                        } elseif(isset($block["cols"][$col_id]["text"]["family"]) && $block["cols"][$col_id]["text"]["family"]) {
                            $tmp = "font-family:" . $block["cols"][$col_id]["text"]["family"] . ";";
                        }
                        $td_css .= $tmp;
                        if($is_money_format) {
                            $td_money_left .= $tmp;
                            $td_money_right .= $tmp;
                        }
                        $tmp = "";
                        if(isset($block["rows"][$row_id]["text"]["size"]) && $block["rows"][$row_id]["text"]["size"]) {
                            $tmp .= "font-size:" . $block["rows"][$row_id]["text"]["size"] * $this->TEMPLATE_FONT_SIZE_ADJUSTER . "pt;";
                        } elseif(isset($block["cols"][$col_id]["text"]["size"]) && $block["cols"][$col_id]["text"]["size"]) {
                            $tmp .= "font-size:" . $block["cols"][$col_id]["text"]["size"] * $this->TEMPLATE_FONT_SIZE_ADJUSTER . "pt;";
                        }
                        $td_css .= $tmp;
                        if($is_money_format) {
                            $td_money_left .= $tmp;
                            $td_money_right .= $tmp;
                        }
                        $tmp = "";
                        if(isset($block["rows"][$row_id]["text"]["color"]) && $block["rows"][$row_id]["text"]["color"]) {
                            $tmp .= "color:" . $block["rows"][$row_id]["text"]["color"] . ";";
                        } elseif(isset($block["cols"][$col_id]["text"]["color"]) && $block["cols"][$col_id]["text"]["color"]) {
                            $tmp .= "color:" . $block["cols"][$col_id]["text"]["color"] . ";";
                        }
                        $td_css .= $tmp;
                        if($is_money_format) {
                            $td_money_left .= $tmp;
                            $td_money_right .= $tmp;
                        }
                        $tmp = "";
                        if(isset($block["rows"][$row_id]["text"]["align"]) && $block["rows"][$row_id]["text"]["align"]) {
                            $tmp .= "text-align:" . $block["rows"][$row_id]["text"]["align"] . ";";
                        } elseif(isset($block["cols"][$col_id]["text"]["align"]) && $block["cols"][$col_id]["text"]["align"]) {
                            $tmp .= "text-align:" . $block["cols"][$col_id]["text"]["align"] . ";";
                        }
                        $td_css .= $tmp;
                        $tmp = "";
                        if($block["type"] == "invoicelines" && $block["rows"][$row_id]["text"]["lineheight"]) {
                            $tmp .= "line-height:" . $block["rows"][$row_id]["text"]["lineheight"] . "em;";
                        } elseif($block["text"]["lineheight"]) {
                            $tmp .= "line-height:" . $block["text"]["lineheight"] . "em;";
                        }
                        $td_css .= $tmp;
                        if($is_money_format) {
                            $td_money_left .= $tmp;
                            $td_money_right .= $tmp;
                        }
                        $tmp = "";
                        if(isset($block["rows"][$row_id]["text"]["style"]) && $block["rows"][$row_id]["text"]["style"]) {
                            if(strpos($block["rows"][$row_id]["text"]["style"], "B") !== false) {
                                $tmp .= "font-weight:bold;";
                            }
                            if(strpos($block["rows"][$row_id]["text"]["style"], "I") !== false) {
                                $tmp .= "font-style:italic;";
                            }
                            if(strpos($block["rows"][$row_id]["text"]["style"], "U") !== false) {
                                $tmp .= "text-decoration:underline;";
                            }
                        } elseif(isset($block["cols"][$col_id]["text"]["style"]) && $block["cols"][$col_id]["text"]["style"]) {
                            if(strpos($block["cols"][$col_id]["text"]["style"], "B") !== false) {
                                $tmp .= "font-weight:bold;";
                            }
                            if(strpos($block["cols"][$col_id]["text"]["style"], "I") !== false) {
                                $tmp .= "font-style:italic;";
                            }
                            if(strpos($block["cols"][$col_id]["text"]["style"], "U") !== false) {
                                $tmp .= "text-decoration:underline;";
                            }
                        }
                        $td_css .= $tmp;
                        if($is_money_format) {
                            $td_money_left .= $tmp;
                            $td_money_right .= $tmp;
                        }
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
                        $tmp = "";
                        if(($loop_row_prevent_borders === false || $loop_row_prevent_borders === true && $tmp_row_id === 0) && isset($block["rows"][$row_id]["borders"]["top"]) && $block["rows"][$row_id]["borders"]["top"] == "yes") {
                            $tmp .= "border-top:" . $cell_border_thickness . "px " . $cell_border_type . " " . $cell_border_color . ";";
                        } elseif(($loop_row_prevent_borders === false || $loop_row_prevent_borders === true && $tmp_row_id === 0) && isset($block["cols"][$col_id]["borders"]["top"]) && $block["cols"][$col_id]["borders"]["top"] == "yes") {
                            $tmp .= "border-top:" . $cell_border_thickness . "px " . $cell_border_type . " " . $cell_border_color . ";";
                        }
                        $td_css .= $tmp;
                        if($is_money_format) {
                            $td_money_left .= $tmp;
                            $td_money_right .= $tmp;
                        }
                        $tmp = "";
                        if($col_id == count($row) - 1 && isset($block["rows"][$row_id]["borders"]["right"]) && $block["rows"][$row_id]["borders"]["right"] == "yes") {
                            $tmp .= "border-right:" . $cell_border_thickness . "px " . $cell_border_type . " " . $cell_border_color . ";";
                        } elseif(isset($block["cols"][$col_id]["borders"]["right"]) && $block["cols"][$col_id]["borders"]["right"] == "yes") {
                            $tmp .= "border-right:" . $cell_border_thickness . "px " . $cell_border_type . " " . $cell_border_color . ";";
                        }
                        $td_css .= $tmp;
                        if($is_money_format) {
                            $td_money_right .= $tmp;
                        }
                        $tmp = "";
                        if(($loop_row_prevent_borders === false || $loop_row_prevent_borders === true && $tmp_row_id == count($loop_rows) - 1) && isset($block["rows"][$row_id]["borders"]["bottom"]) && $block["rows"][$row_id]["borders"]["bottom"] == "yes") {
                            $tmp .= "border-bottom:" . $cell_border_thickness . "px " . $cell_border_type . " " . $cell_border_color . ";";
                        } elseif(($loop_row_prevent_borders === false || $loop_row_prevent_borders === true && $tmp_row_id == count($loop_rows) - 1) && isset($block["cols"][$col_id]["borders"]["bottom"]) && $block["cols"][$col_id]["borders"]["bottom"] == "yes") {
                            $tmp .= "border-bottom:" . $cell_border_thickness . "px " . $cell_border_type . " " . $cell_border_color . ";";
                        }
                        $td_css .= $tmp;
                        if($is_money_format) {
                            $td_money_left .= $tmp;
                            $td_money_right .= $tmp;
                        }
                        $tmp = "";
                        if($col_id === 0 && isset($block["rows"][$row_id]["borders"]["left"]) && $block["rows"][$row_id]["borders"]["left"] == "yes") {
                            $tmp .= "border-left:" . $cell_border_thickness . "px " . $cell_border_type . " " . $cell_border_color . ";";
                        } elseif(isset($block["cols"][$col_id]["borders"]["left"]) && $block["cols"][$col_id]["borders"]["left"] == "yes") {
                            $tmp .= "border-left:" . $cell_border_thickness . "px " . $cell_border_type . " " . $cell_border_color . ";";
                        }
                        $td_css .= $tmp;
                        if($is_money_format) {
                            $td_money_left .= $tmp;
                        }
                        $tmp = "";
                        if(isset($block["rows"][$row_id]["style"]["bgcolor"]) && $block["rows"][$row_id]["style"]["bgcolor"] || $zebra_odd_even == "even" && isset($block["rows"][$row_id]["style"]["bgcolor_even"]) && $block["rows"][$row_id]["style"]["bgcolor_even"]) {
                            if($zebra_odd_even == "even" && $block["rows"][$row_id]["style"]["bgcolor_even"]) {
                                $tmp .= "background-color:" . $block["rows"][$row_id]["style"]["bgcolor_even"] . ";";
                            } else {
                                $tmp .= "background-color:" . $block["rows"][$row_id]["style"]["bgcolor"] . ";";
                            }
                        } elseif(isset($block["cols"][$col_id]["style"]["bgcolor"]) && $block["cols"][$col_id]["style"]["bgcolor"]) {
                            $tmp .= "background-color:" . $block["cols"][$col_id]["style"]["bgcolor"] . ";";
                        }
                        $td_css .= $tmp;
                        if($is_money_format) {
                            $td_money_left .= $tmp;
                            $td_money_right .= $tmp;
                        }
                        $width = 0;
                        if(isset($block["cols"][$col_id]["positioning"]["w"]) && 0 < $block["cols"][$col_id]["positioning"]["w"]) {
                            $width = $this->mm_to_px($block["cols"][$col_id]["positioning"]["w"]);
                        }
                        $check_for_additional_description_residu = false;
                        if($block["type"] == "invoicelines" && 0 < $row_id && isset($this->example_data) && is_array($this->example_data)) {
                            $example_data = $this->example_data;
                            $example_data["invoiceElement"] = isset($example_data["invoiceElement"][$original_row_id - 1]) ? $example_data["invoiceElement"][$original_row_id - 1] : $example_data["invoiceElement"][0];
                            if(isset($example_data["invoiceElement"]["ProductType"]) && $example_data["invoiceElement"]["ProductType"] != "other" && isset($block["rows"][$row_id]["additional_description"][$example_data["invoiceElement"]["ProductType"]]) && $block["rows"][$row_id]["additional_description"][$example_data["invoiceElement"]["ProductType"]]) {
                                $additional_description = str_replace("[" . $example_data["invoiceElement"]["ProductType"] . "->", "[invoiceElement->ProductTypeObject->", $block["rows"][$row_id]["additional_description"][$example_data["invoiceElement"]["ProductType"]]);
                                $value = str_replace("[invoiceElement->Description]", "[invoiceElement->Description] " . $additional_description, $value);
                                $check_for_additional_description_residu = $example_data["invoiceElement"]["ProductType"];
                            }
                        }
                        $html = $this->_parseValueToHTML($value, $original_row_id, $tmp_row_id);
                        if($check_for_additional_description_residu !== false) {
                            $html = str_replace("[invoiceElement-&gt;ProductTypeObject-&gt;", "[" . $check_for_additional_description_residu . "-&gt;", $html);
                        }
                        if(isset($block["cols"][$col_id]["style"]["format"]) && $block["cols"][$col_id]["style"]["format"] == "money") {
                            if(($block["type"] != "invoicelines" || 0 < $row_id) && $html != "") {
                                $width = $width - 10 - 4 * $this->TEMPLATE_BLOCK_PADDING;
                                echo "<td valign=\"top\" style=\"width: 10px;";
                                echo $td_money_left;
                                echo "\">";
                                echo CURRENCY_SIGN_LEFT;
                                echo "</td><td valign=\"top\" style=\"";
                                if(0 < $width) {
                                    echo "width:" . $width . "px;";
                                }
                                echo "text-align:right;";
                                echo $td_money_right;
                                echo "\">";
                                echo $html;
                                if(CURRENCY_SIGN_RIGHT) {
                                    echo " " . CURRENCY_SIGN_RIGHT;
                                }
                                echo "</td>";
                            } else {
                                echo "<td valign=\"top\" colspan=\"2\" style=\"";
                                echo $td_css;
                                echo "\">";
                                echo trim($html) ? $html : "&nbsp;";
                                echo "</td>";
                            }
                        } else {
                            echo "<td valign=\"top\" style=\"";
                            echo $td_css;
                            echo "\"><div style=\"";
                            if(0 < $width) {
                                echo "width:" . ($width - 2 * $this->TEMPLATE_BLOCK_PADDING) . "px;";
                            }
                            echo "white-space:nowrap;overflow:hidden;\">";
                            echo trim($html) ? $html : "&nbsp;";
                            echo "</div></td>";
                        }
                    }
                    echo "</tr>";
                }
            }
            echo "</table>";
        } elseif($block["type"] === "qrcode") {
            echo "                <img src=\"";
            echo $this->generateExampleQRCode($width);
            echo "\" style=\"max-width:100%\" />\n                ";
        } else {
            echo $this->_parseValueToHTML($block["value"]);
        }
        echo "\t\t</div>\n\t\t";
    }
    private function _parseValueToHTML($value, $row_id = 0, $multi_row_id = 0)
    {
        if(isset($this->example_data) && is_array($this->example_data)) {
            $example_data = $this->example_data;
            if($value == "[invoiceElement->Number]") {
                $value .= "[invoiceElement->NumberSuffix]";
            }
            if(0 < $row_id) {
                $example_data["invoiceElement"] = isset($example_data["invoiceElement"][$row_id - 1]) ? $example_data["invoiceElement"][$row_id - 1] : $example_data["invoiceElement"][0];
            } else {
                unset($example_data["invoiceElement"]);
            }
            if($multi_row_id == 1 && 0 < $example_data["invoiceElement"]["DiscountPercentage"]) {
                if(preg_match("[invoiceElement->Description]", $value)) {
                    $value = sprintf(__("x discount on invoiceline"), showNumber($example_data["invoiceElement"]["FullDiscountPercentage"]));
                } elseif(preg_match("[invoiceElement->NoDiscountAmountExcl]", $value)) {
                    $value = $example_data["invoiceElement"]["DiscountAmountExcl"];
                } elseif(preg_match("[invoiceElement->NoDiscountAmountIncl]", $value)) {
                    $value = $example_data["invoiceElement"]["DiscountAmountIncl"];
                } else {
                    $value = "";
                }
                return $value;
            }
            $match_array = ["[pdf->Today]", "[pdf->CurrentPageNumber]", "[pdf->TotalPageNumber]"];
            $replace_array = [rewrite_date_db2site(date("Ymd")), "1", "1"];
            $value = str_replace($match_array, $replace_array, $value);
            $pattern = [];
            $replace = [];
            $newline_patterns = [];
            $newline_replaces = [];
            $pattern[] = "/\\[period\\]([\\r\\n]*)(.*)\\[\\/period\\]/si";
            $replace[] = isset($example_data["invoiceElement"]["StartPeriod"]) && $example_data["invoiceElement"]["StartPeriod"] ? "\\2" : "";
            $pattern[] = "/\\[credit\\]([\\r\\n]*)(.*)\\[\\/credit\\]/si";
            $replace[] = "";
            $pattern[] = "/\\[non-credit\\]([\\r\\n]*)(.*)\\[\\/non-credit\\]/si";
            $replace[] = "\\2";
            $pattern[] = "/\\[paid\\]([\\r\\n]*)(.*)\\[\\/paid\\]/si";
            $replace[] = "";
            $pattern[] = "/\\[unpaid\\]([\\r\\n]*)(.*)\\[\\/unpaid\\]/si";
            $replace[] = "\\2";
            $pattern[] = "/\\[partly_paid\\]([\\r\\n]*)(.*)\\[\\/partly_paid\\]/si";
            $replace[] = "\\2";
            $pattern[] = "/\\[directdebit\\]([\\r\\n]*)(.*)\\[\\/directdebit\\]/si";
            $replace[] = "";
            $pattern[] = "/\\[transfer\\]([\\r\\n]*)(.*)\\[\\/transfer\\]/si";
            $replace[] = "\\2";
            $pattern[] = "/\\[male\\]([\\r\\n]*)(.*)\\[\\/male\\]/si";
            $replace[] = "\\2";
            $pattern[] = "/\\[female\\]([\\r\\n]*)(.*)\\[\\/female\\]/si";
            $replace[] = "";
            $pattern[] = "/\\[department\\]([\\r\\n]*)(.*)\\[\\/department\\]/si";
            $replace[] = "";
            $pattern[] = "/\\[is_company\\]([\\r\\n]*)(.*)\\[\\/is_company\\]/si";
            $replace[] = "\\2";
            $pattern[] = "/\\[is_consumer\\]([\\r\\n]*)(.*)\\[\\/is_consumer\\]/si";
            $replace[] = "";
            $pattern[] = "/\\[has_contact\\]([\\r\\n]*)(.*)\\[\\/has_contact\\]/si";
            $replace[] = "\\2";
            $newline_patterns[] = "/\\r\\n\\[linediscount\\]([\\r\\n]*)(.*)\\[\\/linediscount\\]/si";
            $newline_replaces[] = isset($example_data["invoiceElement"]["DiscountPercentage"]) && 0 < $example_data["invoiceElement"]["DiscountPercentage"] ? "\n\\2" : "";
            $pattern[] = "/\\[linediscount\\]([\\r\\n]*)(.*)\\[\\/linediscount\\]/si";
            $replace[] = isset($example_data["invoiceElement"]["DiscountPercentage"]) && 0 < $example_data["invoiceElement"]["DiscountPercentage"] ? "\\2" : "";
            $pattern[] = "/\\[reference\\]([\\r\\n]*)(.*)\\[\\/reference\\]/si";
            $replace[] = isset($example_data["invoice"]->ReferenceNumber) && $example_data["invoice"]->ReferenceNumber != "" ? "\\2" : "";
            foreach ($pattern as $tmp_key => $tmp_pattern) {
                $newline_patterns[] = str_replace("/si", "\\r\\n/i", $tmp_pattern);
                $newline_replaces[] = $replace[$tmp_key];
            }
            $pattern = array_merge($newline_patterns, $pattern);
            $replace = array_merge($newline_replaces, $replace);
            $value = preg_replace($pattern, $replace, $value);
            $match_array = [];
            $replace_array = [];
            foreach ($example_data as $obj => $attributes) {
                foreach ($attributes as $attribute => $val) {
                    if($attribute == "ProductTypeObject") {
                        foreach ($val as $sub_attribute => $sub_val) {
                            if(is_string($sub_val) || is_float($sub_val)) {
                                if(!$sub_val) {
                                    $match_array[] = "\r\n[" . $obj . "->" . $attribute . "->" . $sub_attribute . "]" . "\r\n";
                                    $replace_array[] = "\r\n" . $sub_val;
                                }
                                $match_array[] = "[" . $obj . "->" . $attribute . "->" . $sub_attribute . "]";
                                $replace_array[] = $sub_val;
                            }
                        }
                    } elseif(is_string($val) || is_float($val)) {
                        if(!$val) {
                            $match_array[] = "\r\n[" . $obj . "->" . $attribute . "]" . "\r\n";
                            $replace_array[] = "\r\n" . $val;
                        }
                        $match_array[] = "[" . $obj . "->" . $attribute . "]";
                        $replace_array[] = $val;
                    }
                }
            }
            $value = str_replace($match_array, $replace_array, $value);
        }
        $value = htmlspecialchars($value);
        $value = str_replace("<", "[[LT]]", str_replace(">", "[[GT]]", $value));
        $value = str_replace("&lt;", "[[LT]]", str_replace("&gt;", "[[GT]]", $value));
        $value = html_entity_decode($value, ENT_COMPAT, "UTF-8");
        $value = str_replace(["[[LT]]br[[GT]]", "[[LT]]br /[[GT]]", "[[LT]]br[[GT]]"], "<br />", $value);
        if(strpos($value, "[[LT]]") !== false || strpos($value, "[[GT]]") !== false) {
            $count_html_parsing = 0;
            do {
                $count_html_parsing++;
                $value_before_parsing = $value;
                $pattern = "/\\[\\[LT\\]\\]([^\\s]*)\\b([\\s].*)?\\[\\[GT\\]\\](.*)\\[\\[LT\\]\\]\\/\\1\\[\\[GT\\]\\]/siU";
                $replace = "<\\1\\2>\\3</\\1>";
                $value = preg_replace($pattern, $replace, $value);
            } while (!($count_html_parsing < 10 && $value_before_parsing != $value));
            $value = str_replace(["[[LT]]", "[[GT]]"], ["&lt;", "&gt;"], $value);
        }
        return nl2br($value);
    }
    public function mm_to_px($position)
    {
        $mm_to_px = 0;
        return floor($position * $mm_to_px);
    }
    public function px_to_mm($position, $floor = true)
    {
        $mm_to_px = 0;
        return $floor === true ? floor($position * 1 / $mm_to_px) : $position * 1 / $mm_to_px;
    }
    public function loadExampleData()
    {
        $example_data = [];
        $company = new company();
        $company->show();
        foreach ($company->Variables as $var) {
            $company->{$var} = htmlspecialchars_decode($company->{$var});
        }
        global $array_country;
        $company->CountryLong = $array_country[$company->Country];
        $example_data["company"] = $company;
        require_once "class/debtor.php";
        $debtor = new debtor();
        $debtor->Identifier = 0;
        $debtor->DebtorCode = $debtor->newDebtorCode();
        $debtor->Username = $debtor->DebtorCode;
        $debtor->Password = __("exampledata debtor password");
        $debtor->CompanyName = __("exampledata debtor companyname");
        $debtor->CompanyNumber = __("exampledata debtor companynumber");
        $debtor->TaxNumber = __("exampledata debtor taxnumber");
        $debtor->Sex = __("exampledata debtor sex");
        $debtor->SexName = __("salutation male");
        $debtor->Initials = __("exampledata debtor initials");
        $debtor->SurName = __("exampledata debtor surname");
        $debtor->Address = __("exampledata debtor address");
        $debtor->Address2 = __("exampledata debtor address2");
        $debtor->ZipCode = __("exampledata debtor zipcode");
        $debtor->City = __("exampledata debtor city");
        $debtor->State = __("exampledata debtor state");
        $debtor->StateName = __("exampledata debtor statename");
        $debtor->Country = __("exampledata debtor country");
        $debtor->CountryLong = $array_country[$debtor->Country];
        $debtor->PhoneNumber = __("exampledata debtor phonenumber");
        $debtor->FaxNumber = __("exampledata debtor faxnumber");
        $debtor->MobileNumber = __("exampledata debtor mobilenumber");
        $debtor->EmailAddress = __("exampledata debtor emailaddress");
        $debtor->Website = __("exampledata debtor website");
        $debtor->AccountNumber = __("exampledata debtor accountnumber");
        $debtor->AccountBIC = __("exampledata debtor accountswift");
        $debtor->AccountName = __("exampledata debtor accountname");
        $debtor->AccountBank = __("exampledata debtor accountbank");
        $debtor->AccountCity = __("exampledata debtor accountcity");
        $debtor->OpenAmountExcl = money("125.50", false);
        $debtor->OpenAmountIncl = money(0 * (1 + STANDARD_TAX), false);
        $debtor->MandateID = __("sdd mandate id");
        $example_data["debtor"] = $debtor;
        require_once "class/domain.php";
        $domain = new domain();
        $domain->Domain = __("exampledata domain domain");
        $domain->Tld = __("exampledata domain tld");
        $domain->AuthKey = "sDldin232981Dldkwnw";
        $domain->RegistrationDate = rewrite_date_db2site(date("Y-m-d"));
        $domain->ExpirationDate = rewrite_date_db2site(date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y") + 1)));
        $domain->DNS1 = __("exampledata domain dns1");
        $domain->DNS2 = __("exampledata domain dns2");
        $domain->DNS3 = __("exampledata domain dns3");
        $example_data["domain"] = $domain;
        require_once "class/hosting.php";
        $hosting = new hosting();
        $hosting->Username = "id0001";
        $hosting->Password = "oi#4BBd\$93";
        $hosting->Domain = __("exampledata domain domain") . "." . __("exampledata domain tld");
        $hosting->PackageName = "Starter";
        $hosting->DiscSpace = formatMB(200);
        $hosting->BandWidth = formatMB(5000);
        $hosting->IP = "12.34.56.78";
        $hosting->Port = "2222";
        $example_data["hosting"] = $hosting;
        require_once "class/server.php";
        $server = new server();
        $server->Name = "Server 1";
        $server->Panel = "directadmin";
        $server->IP = "12.34.56.78";
        $server->Port = "2222";
        $server->DNS1 = __("exampledata domain dns1");
        $server->DNS2 = __("exampledata domain dns2");
        $server->DNS3 = __("exampledata domain dns3");
        $example_data["server"] = $server;
        global $array_sex;
        require_once "class/invoice.php";
        $invoice = new invoice();
        $invoice->InvoiceCode = $invoice->newInvoiceCode();
        $invoice->Date = rewrite_date_db2site(date("Y-m-d"));
        $invoice->Term = INVOICE_TERM;
        $invoice->PayBefore = rewrite_date_db2site(date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + $invoice->Term, date("Y"))));
        $invoice->ReferenceNumber = __("exampledata invoice referencenumber");
        $invoice->Description = __("exampledata invoice description");
        $invoice->Discount = "0";
        $invoice->Authorisation = "no";
        $invoice->TransactionID = "00112233445566";
        $invoice->CompanyName = $debtor->CompanyName;
        $invoice->TaxNumber = $debtor->TaxNumber;
        $invoice->Sex = $debtor->Sex;
        $invoice->SexName = $debtor->SexName;
        $invoice->Initials = $debtor->Initials;
        $invoice->SurName = $debtor->SurName;
        $invoice->Address = $debtor->Address;
        $invoice->Address2 = $debtor->Address2;
        $invoice->ZipCode = $debtor->ZipCode;
        $invoice->City = $debtor->City;
        $invoice->State = $debtor->State;
        $invoice->StateName = $debtor->StateName;
        $invoice->Country = $debtor->Country;
        $invoice->CountryLong = $debtor->CountryLong;
        $invoice->EmailAddress = $debtor->EmailAddress;
        $invoice->Elements[0] = ["Date" => rewrite_date_db2site(date("Y-m-d")), "Number" => "1", "NumberSuffix" => "", "ProductCode" => "P001", "ProductName" => __("exampledata invoice line1 productname"), "Description" => __("exampledata invoice line1 description"), "StartPeriod" => rewrite_date_db2site(date("Y-m-d")), "EndPeriod" => rewrite_date_db2site(date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y") + 1))), "PriceExcl" => money("5", false), "PriceIncl" => money(5 * (1 + STANDARD_TAX), false), "AmountExcl" => money("60", false), "AmountTax" => money(60 * STANDARD_TAX, false), "AmountIncl" => money(60 * (1 + STANDARD_TAX), false), "TaxPercentage" => STANDARD_TAX, "FullTaxPercentage" => STANDARD_TAX * 100, "Periods" => "1", "Periodic" => __("exampledata invoice line1 periodic"), "FullDiscountPercentage" => "", "DiscountAmountExcl" => "", "DiscountAmountIncl" => "", "NoDiscountAmountExcl" => money("60", false), "NoDiscountAmountIncl" => money(60 * (1 + STANDARD_TAX), false), "ProductType" => "hosting", "Reference" => 0, "ProductTypeObject" => $hosting];
        $invoice->Elements[1] = ["Date" => rewrite_date_db2site(date("Y-m-d")), "Number" => "1", "NumberSuffix" => "", "ProductCode" => "P002", "ProductName" => __("exampledata invoice line2 productname"), "Description" => __("exampledata invoice line2 description"), "StartPeriod" => rewrite_date_db2site(date("Y-m-d")), "EndPeriod" => rewrite_date_db2site(date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y") + 1))), "PriceExcl" => money("10.00", false), "PriceIncl" => money(10 * (1 + STANDARD_TAX), false), "AmountExcl" => money(0, false), "AmountTax" => money(0 * STANDARD_TAX, false), "AmountIncl" => money(0 * (1 + STANDARD_TAX), false), "TaxPercentage" => STANDARD_TAX, "FullTaxPercentage" => STANDARD_TAX * 100, "Periods" => "1", "Periodic" => __("exampledata invoice line2 periodic"), "DiscountPercentage" => "1", "FullDiscountPercentage" => "100", "DiscountAmountExcl" => money(-10, false), "DiscountAmountIncl" => money(-10 * (1 + STANDARD_TAX), false), "NoDiscountAmountExcl" => money("10", false), "NoDiscountAmountIncl" => money(10 * (1 + STANDARD_TAX), false), "ProductType" => "domain", "Reference" => 0, "ProductTypeObject" => $domain];
        $invoice->Elements[2] = ["Date" => rewrite_date_db2site(date("Y-m-d")), "Number" => "1", "NumberSuffix" => "", "ProductCode" => "P003", "ProductName" => __("exampledata invoice line3 productname"), "Description" => __("exampledata invoice line3 description"), "StartPeriod" => "", "EndPeriod" => "", "PriceExcl" => money("25.00", false), "PriceIncl" => money(25 * (1 + STANDARD_TAX), false), "AmountExcl" => money("25.00", false), "AmountTax" => money(25 * STANDARD_TAX, false), "AmountIncl" => money(25 * (1 + STANDARD_TAX), false), "TaxPercentage" => STANDARD_TAX, "FullTaxPercentage" => STANDARD_TAX * 100, "Periods" => "1", "Periodic" => "", "FullDiscountPercentage" => "", "DiscountAmountExcl" => "", "DiscountAmountIncl" => "", "NoDiscountAmountExcl" => money("25", false), "NoDiscountAmountIncl" => money(25 * (1 + STANDARD_TAX), false)];
        $invoice->used_taxrates = !isEmptyFloat(STANDARD_TAX) ? [strval(STANDARD_TAX) => ["AmountExcl" => money("85.00", false), "AmountTax" => money(85 * STANDARD_TAX, false)]] : [];
        $invoice->TaxRate_Label = "";
        $invoice->TaxRate = STANDARD_TOTAL_TAX;
        $taxrate_amount = 0;
        if(0 < $invoice->TaxRate) {
            global $array_total_taxpercentages_info;
            $taxrate_amount = 85 * $invoice->TaxRate;
            $invoice->TaxRate_Amount = money($taxrate_amount, false);
            $invoice->TaxRate_Label = isset($array_total_taxpercentages_info[(string) (double) $invoice->TaxRate]["label"]) ? $array_total_taxpercentages_info[(string) (double) $invoice->TaxRate]["label"] : "";
        }
        $invoice->PageTotalExcl = money("85.00", false);
        $invoice->PageTotalIncl = money(85 * (1 + STANDARD_TAX), false);
        $invoice->AmountExcl = money("85.00", false);
        $invoice->AmountIncl = money(85 * (1 + STANDARD_TAX) + $taxrate_amount, false);
        $invoice->PartPayment = money("50.00", false);
        $invoice->AmountPaid = money(85 * (1 + STANDARD_TAX) + $taxrate_amount - 50, false);
        $invoice->AmountDiscount = money("0", false);
        $invoice->AmountDiscountIncl = money("0", false);
        $invoice->PaymentURLRaw = "#";
        $example_data["invoice"] = $invoice;
        $example_data["invoiceElement"] = $invoice->Elements;
        require_once "class/pricequote.php";
        $pricequote = new pricequote();
        $invoice->PriceQuoteCode = $pricequote->newPriceQuoteCode();
        $example_data["pricequote"] = $invoice;
        $example_data["pricequoteElements"] = $invoice->Elements;
        $example_data = do_filter("service_template_example_data", $example_data);
        $this->example_data = $example_data;
    }
    private function _formatBlockArray($item)
    {
        $result = [];
        $result["id"] = $item["id"];
        $result["type"] = $item["type"];
        $result["positioning"] = ["x" => $item["positioning_x"], "y" => $item["positioning_y"], "w" => $item["positioning_w"], "h" => $item["positioning_h"]];
        $result["visibility"] = $item["visibility"];
        $result["text"] = ["family" => $item["text_family"], "size" => $item["text_size"], "color" => $item["text_color"], "align" => $item["text_align"], "lineheight" => $item["text_lineheight"], "style" => $item["text_style"]];
        $result["borders"] = ["top" => $item["borders_top"], "right" => $item["borders_right"], "bottom" => $item["borders_bottom"], "left" => $item["borders_left"], "thickness" => $item["borders_thickness"], "color" => $item["borders_color"], "type" => $item["borders_type"]];
        $result["style"] = ["bgcolor" => $item["style_bgcolor"]];
        $result["value"] = $item["value"];
        $result["cols"] = json_decode($item["cols"], true);
        $result["rows"] = json_decode($item["rows"], true);
        if(!empty($result["cols"]) || !empty($result["rows"])) {
            $result["value"] = json_decode($result["value"], true);
        }
        return $result;
    }
    private function _getDefaultCol()
    {
        $default_col = ["positioning" => ["w" => 0], "text" => ["family" => "", "size" => "", "color" => "", "lineheight" => "1.25", "align" => "", "style" => ""], "borders" => ["top" => "no", "right" => "no", "bottom" => "no", "left" => "no", "thickness" => "1", "color" => "#000000", "type" => "solid"], "style" => ["bgcolor" => "", "format" => ""]];
        return $default_col;
    }
    private function _getDefaultRow()
    {
        $default_row = ["text" => ["family" => "", "size" => "", "color" => "", "lineheight" => "1.25", "align" => "", "style" => ""], "borders" => ["top" => "no", "right" => "no", "bottom" => "no", "left" => "no", "thickness" => "1", "color" => "#000000", "type" => "solid"], "style" => ["bgcolor" => "", "bgcolor_even" => ""]];
        return $default_row;
    }
    private function _getDefaultTotalVariable($totaltype)
    {
        switch ($totaltype) {
            case "amountexcl":
                return "[invoice->AmountExcl]";
                break;
            case "amounttax":
                return "";
                break;
            case "amountincl":
                return "[invoice->AmountIncl]";
                break;
            case "pagetotalexcl":
                return "[invoice->PageTotalExcl]";
                break;
            case "pagetotalincl":
                return "[invoice->PageTotalIncl]";
                break;
        }
    }
    public function generateExampleQRCode(int $size)
    {
        return $this->generateQRCodeForUrl(IDEAL_EMAIL, $size);
    }
    public function generateQRCodeForUrl($url = 300, int $size)
    {
        if(!class_exists("imagick")) {
            return "";
        }
        $renderer = new BaconQrCode\Renderer\ImageRenderer(new BaconQrCode\Renderer\RendererStyle\RendererStyle($size), new BaconQrCode\Renderer\Image\ImagickImageBackEnd());
        $writer = new BaconQrCode\Writer($renderer);
        $qrCode = $writer->writeString($url);
        return "data:image/png;base64," . base64_encode($qrCode);
    }
    public static function isQRCodeAllowed($templateType)
    {
        return class_exists("imagick") && in_array($templateType, ["invoice", "reminder", "summation"]);
    }
}

?>