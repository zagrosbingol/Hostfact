<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class UBL
{
    private $ns_cac;
    private $ns_cbc;
    public $Success;
    public $Error;
    public $Warning;
    public function __construct()
    {
        $this->ns_cac = "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2";
        $this->ns_cbc = "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2";
        $this->Success = $this->Error = $this->Warning = [];
    }
    public function generateInvoiceUBL($objects = [])
    {
        global $company;
        $this->invoice = $objects["invoice"];
        $invoice_tax_reverse = $this->invoice->VatShift == "yes" || $this->invoice->VatShift == "" && $this->invoice->AmountTax == money(0, false) && $objects["invoice"]->Country != $company->Country && $objects["invoice"]->CompanyName != "" && $objects["invoice"]->TaxNumber != "" && ($objects["invoice"]->Country == "NL" || strpos($objects["invoice"]->Country, "EU-") !== false) ? true : false;
        if(isset($objects["pdf"]) && @file_exists($objects["pdf"]["path"] . $objects["pdf"]["filename"])) {
            $pdf_base64 = base64_encode(file_get_contents($objects["pdf"]["path"] . $objects["pdf"]["filename"]));
        }
        $xml = new SimpleXMLElement($this->_setUBLHeader());
        $xml->addChild("UBLVersionID", "2.1", $this->ns_cbc);
        $xml->addChild("CustomizationID", "urn:www.cenbii.eu:transaction:biitrns010:ver2.0:extended:urn:www.peppol.eu:bis:peppol4a:ver2.0:extended:urn:www.simplerinvoicing.org:si:si-ubl:ver1.1.x", $this->ns_cbc);
        $xml->addChild("ProfileID", "urn:www.cenbii.eu:profile:bii04:ver2.0", $this->ns_cbc);
        $xml->addChild("ID", $this->invoice->InvoiceCode, $this->ns_cbc);
        $xml->addChild("IssueDate", date("Y-m-d", strtotime(rewrite_date_site2db($this->invoice->Date))), $this->ns_cbc);
        if($this->invoice->PayBefore) {
            $xml->addChild("DueDate", date("Y-m-d", strtotime(rewrite_date_site2db($this->invoice->PayBefore))), $this->ns_cbc);
        }
        $typeCode = $xml->addChild("InvoiceTypeCode", "380", $this->ns_cbc);
        $typeCode->addAttribute("listID", "UNCL1001");
        $typeCode->addAttribute("listAgencyID", "6");
        $xml->addChild("DocumentCurrencyCode", "EUR", $this->ns_cbc);
        if($this->invoice->ReferenceNumber) {
            $xml->addChild("OrderReference", "", $this->ns_cac)->addChild("ID", $this->invoice->ReferenceNumber, $this->ns_cbc);
        }
        if(isset($pdf_base64) && $pdf_base64) {
            $AdditionalDocumentReference = $xml->addChild("AdditionalDocumentReference", "", $this->ns_cac);
            $AdditionalDocumentReference->addChild("ID", $objects["pdf"]["filename"], $this->ns_cbc);
            $AdditionalDocumentReference->addChild("Attachment", "", $this->ns_cac)->addChild("EmbeddedDocumentBinaryObject", $pdf_base64, $this->ns_cbc)->addAttribute("mimeCode", "application/pdf");
        }
        $xml = $this->_setAccountingSupplier($xml, $company, "WFS");
        $xml = $this->_setAccountingCustomer($xml, $this->invoice, $objects["debtor"], "debtor");
        $payment = $xml->addChild("PaymentMeans", "", $this->ns_cac);
        if($this->invoice->Authorisation == "yes") {
            $paymentCode = $payment->addChild("PaymentMeansCode", "49", $this->ns_cbc);
        } else {
            $paymentCode = $payment->addChild("PaymentMeansCode", "1", $this->ns_cbc);
        }
        $paymentCode->addAttribute("listID", "UNCL4461");
        if($this->invoice->PayBefore) {
            $payment->addChild("PaymentDueDate", date("Y-m-d", strtotime(rewrite_date_site2db($this->invoice->PayBefore))), $this->ns_cbc);
        }
        if($this->invoice->TransactionID) {
            $payment->addChild("PaymentID", $this->invoice->TransactionID, $this->ns_cbc);
        }
        if($company->AccountNumber) {
            $financialAccount = $payment->addChild("PayeeFinancialAccount", "", $this->ns_cac);
            $financialAccount->addChild("ID", preg_replace("/[^a-z0-9]/i", "", $company->AccountNumber), $this->ns_cbc)->addAttribute("schemeID", "IBAN");
            if($company->AccountBIC) {
                $financialAccount->addChild("FinancialInstitutionBranch", "", $this->ns_cac)->addChild("FinancialInstitution", "", $this->ns_cac)->addChild("ID", $company->AccountBIC, $this->ns_cbc)->addAttribute("schemeID", "BIC");
            }
        }
        $tax_categories = [];
        if(!empty($this->invoice->Elements)) {
            foreach ($this->invoice->Elements as $key => $_element) {
                if(!is_numeric($key)) {
                } elseif(0 < $this->invoice->Discount) {
                    if(0 < $_element["DiscountPercentage"]) {
                        $line_extension_amount = $_element["Number"] * $_element["PriceExcl"] * $_element["Periods"] * round(1 - $_element["DiscountPercentage"], 4);
                    } else {
                        $line_extension_amount = $_element["Number"] * $_element["PriceExcl"] * $_element["Periods"];
                    }
                    $tax_percent = floatval($_element["TaxPercentage"] * 100);
                    if(isset($tax_categories[$tax_percent])) {
                        $tax_categories[$tax_percent] = $tax_categories[$tax_percent] + floatval($line_extension_amount * $this->invoice->Discount / 100);
                    } else {
                        $tax_categories[$tax_percent] = floatval($line_extension_amount * $this->invoice->Discount / 100);
                    }
                }
            }
        }
        $tax_difference = deformat_money($this->invoice->AmountIncl);
        $total_allowance_charge = 0;
        if(0 < $this->invoice->Discount && !empty($tax_categories)) {
            foreach ($tax_categories as $_tax => $_amount) {
                $allowanceChargePerTax = $xml->addChild("AllowanceCharge", "", $this->ns_cac);
                $allowanceChargePerTax->addChild("ChargeIndicator", "false", $this->ns_cbc);
                $allowanceChargePerTax->addChild("AllowanceChargeReasonCode", "43", $this->ns_cbc)->addAttribute("listID", "UNCL4465");
                $allowanceChargePerTax->addChild("AllowanceChargeReason", __("discount on invoice"), $this->ns_cbc);
                $allowance_charge_amount = abs(floatval(deformat_money($_amount)));
                $allowance_charge_amount = $allowance_charge_amount - 0;
                $allowanceChargePerTax->addChild("Amount", $this->_rounding($allowance_charge_amount), $this->ns_cbc)->addAttribute("currencyID", "EUR");
                $taxCategory = $allowanceChargePerTax->addChild("TaxCategory", "", $this->ns_cac);
                $taxCategoryID = $taxCategory->addChild("ID", $this->_getTaxCategory($_tax, $invoice_tax_reverse), $this->ns_cbc);
                $taxCategoryID->addAttribute("schemeID", "UNCL5305");
                $taxCategory->addChild("Percent", number_format($_tax, 2), $this->ns_cbc);
                $taxSchemeID = $taxCategory->addChild("TaxScheme", "", $this->ns_cac)->addChild("ID", "VAT", $this->ns_cbc);
                $taxSchemeID->addAttribute("schemeID", "UN/ECE 5153");
                $allowanceChargePerTax->addChild("TaxTotal", "", $this->ns_cac)->addChild("TaxAmount", $this->_rounding(deformat_money(money($allowance_charge_amount * $_tax / 100, false))), $this->ns_cbc)->addAttribute("currencyID", "EUR");
                $total_allowance_charge = $total_allowance_charge + $this->_rounding($allowance_charge_amount);
                $tax_difference += round($allowance_charge_amount, 2);
                $tax_difference += round(deformat_money(money($allowance_charge_amount * $_tax / 100, false)), 2);
            }
        }
        $taxTotal = $xml->addChild("TaxTotal", "", $this->ns_cac);
        $taxTotal->addChild("TaxAmount", $this->_rounding(deformat_money($this->invoice->AmountTax)), $this->ns_cbc)->addAttribute("currencyID", "EUR");
        if(isset($this->invoice->used_taxrates) && !empty($this->invoice->used_taxrates)) {
            foreach ($this->invoice->used_taxrates as $_tax_perc => $_tax) {
                $taxSubtotal = $taxTotal->addChild("TaxSubtotal", "", $this->ns_cac);
                $taxSubtotal->addChild("TaxableAmount", deformat_money($_tax["AmountExcl"]), $this->ns_cbc)->addAttribute("currencyID", "EUR");
                $taxSubtotal->addChild("TaxAmount", $this->_rounding(deformat_money($_tax["AmountTax"])), $this->ns_cbc)->addAttribute("currencyID", "EUR");
                $taxCategory = $taxSubtotal->addChild("TaxCategory", "", $this->ns_cac);
                $taxCategoryID = $taxCategory->addChild("ID", $this->_getTaxCategory($_tax_perc * 100, $invoice_tax_reverse), $this->ns_cbc);
                $taxCategoryID->addAttribute("schemeID", "UNCL5305");
                $taxCategory->addChild("Percent", number_format($_tax_perc * 100, 2), $this->ns_cbc);
                if($invoice_tax_reverse === true) {
                    $taxCategory->addChild("TaxExemptionReason", "Reverse charge", $this->ns_cbc);
                }
                $taxSchemeID = $taxCategory->addChild("TaxScheme", "", $this->ns_cac)->addChild("ID", "VAT", $this->ns_cbc);
                $taxSchemeID->addAttribute("schemeID", "UN/ECE 5153");
                $taxSchemeID->addAttribute("schemeAgencyID", "6");
            }
        }
        $legalMonetaryTotal = $xml->addChild("LegalMonetaryTotal", "", $this->ns_cac);
        $legalMonetaryTotal->addChild("LineExtensionAmount", $this->_rounding(deformat_money($this->invoice->AmountExcl) + $total_allowance_charge), $this->ns_cbc)->addAttribute("currencyID", "EUR");
        $legalMonetaryTotal->addChild("TaxExclusiveAmount", $this->_rounding(deformat_money($this->invoice->AmountExcl)), $this->ns_cbc)->addAttribute("currencyID", "EUR");
        $legalMonetaryTotal->addChild("TaxInclusiveAmount", $this->_rounding(deformat_money($this->invoice->AmountIncl)), $this->ns_cbc)->addAttribute("currencyID", "EUR");
        if(!empty($this->invoice->Elements)) {
            foreach ($this->invoice->Elements as $key => $_element) {
                if(!is_numeric($key) || !trim($_element["Description"]) && isEmptyFloat($_element["AmountIncl"])) {
                } else {
                    $invoiceLine = $xml->addChild("InvoiceLine", "", $this->ns_cac);
                    $invoiceLine->addChild("ID", $_element["id"], $this->ns_cbc);
                    if(strrpos($_element["Number"], ".") !== false) {
                        $decimals = strlen($_element["Number"]) - strrpos($_element["Number"], ".") - 1;
                    } else {
                        $decimals = 2;
                    }
                    if($decimals <= 2) {
                        $decimals = 2;
                    } elseif(6 < $decimals) {
                        $decimals = 6;
                    }
                    $number = $invoiceLine->addChild("InvoicedQuantity", number_format($_element["Number"], $decimals, ".", false), $this->ns_cbc);
                    if($_element["NumberSuffix"]) {
                        $number->addAttribute("unitCode", "ZZ");
                    } else {
                        $number->addAttribute("unitCode", "ZZ");
                    }
                    $invoiceLine->addChild("LineExtensionAmount", $_element["AmountExcl"], $this->ns_cbc)->addAttribute("currencyID", "EUR");
                    if(0 < $_element["DiscountPercentage"]) {
                        $allowanceCharge = $invoiceLine->addChild("AllowanceCharge", "", $this->ns_cac);
                        $allowanceCharge->addChild("ChargeIndicator", "false", $this->ns_cbc);
                        $allowanceCharge->addChild("AllowanceChargeReason", __("discount on invoiceline"), $this->ns_cbc);
                        $allowanceCharge->addChild("Amount", $_element["Number"] * $_element["PriceExcl"] * $_element["DiscountPercentage"] * $_element["Periods"], $this->ns_cbc)->addAttribute("currencyID", "EUR");
                    }
                    if(0 < $_element["DiscountPercentage"]) {
                        $line_extension_amount = $_element["Number"] * $_element["PriceExcl"] * $_element["Periods"] * round(1 - $_element["DiscountPercentage"], 4);
                    } else {
                        $line_extension_amount = $_element["Number"] * $_element["PriceExcl"] * $_element["Periods"];
                    }
                    $taxTotal = $invoiceLine->addChild("TaxTotal", "", $this->ns_cac);
                    $taxTotal->addChild("TaxAmount", round($line_extension_amount * $_element["TaxPercentage"], 2), $this->ns_cbc)->addAttribute("currencyID", "EUR");
                    $tax_difference -= round($line_extension_amount, 2);
                    $tax_difference -= round($line_extension_amount * $_element["TaxPercentage"], 2);
                    $item = $invoiceLine->addChild("Item", "", $this->ns_cac);
                    $item->addChild("Description", trim($_element["Description"]) ? $_element["Description"] : "-", $this->ns_cbc);
                    $item->addChild("Name", htmlspecialchars(mb_substr(htmlspecialchars_decode($_element["Description"]), 0, 100)), $this->ns_cbc);
                    $classifiedTaxCategory = $item->addChild("ClassifiedTaxCategory", "", $this->ns_cac);
                    $classifiedTaxCategory->addChild("ID", $this->_getTaxCategory($_element["TaxPercentage"] * 100, $invoice_tax_reverse), $this->ns_cbc)->addAttribute("schemeID", "UNCL5305");
                    $classifiedTaxCategory->addChild("Percent", number_format($_element["TaxPercentage"] * 100, 2), $this->ns_cbc);
                    $taxSchemeID = $classifiedTaxCategory->addChild("TaxScheme", "", $this->ns_cac)->addChild("ID", "VAT", $this->ns_cbc);
                    $taxSchemeID->addAttribute("schemeID", "UN/ECE 5153");
                    $taxSchemeID->addAttribute("schemeAgencyID", "6");
                    $price = $invoiceLine->addChild("Price", "", $this->ns_cac);
                    $price->addChild("PriceAmount", $_element["PriceExcl"], $this->ns_cbc)->addAttribute("currencyID", "EUR");
                    $price->addChild("BaseQuantity", "1", $this->ns_cbc);
                }
            }
        }
        if(!isEmptyFloat($total_allowance_charge)) {
            $legalMonetaryTotal->addChild("AllowanceTotalAmount", $this->_rounding(deformat_money(money($total_allowance_charge, false))), $this->ns_cbc)->addAttribute("currencyID", "EUR");
        }
        if(round((double) $tax_difference, 2) != 0) {
            $legalMonetaryTotal->addChild("PayableRoundingAmount", $this->_rounding($tax_difference), $this->ns_cbc)->addAttribute("currencyID", "EUR");
        }
        $legalMonetaryTotal->addChild("PayableAmount", $this->_rounding(deformat_money($this->invoice->AmountIncl)), $this->ns_cbc)->addAttribute("currencyID", "EUR");
        $xml = $xml->asXML();
        $xml = str_replace("><", ">\n<", $xml);
        $this->document = Database_Model::getInstance()->getOne("HostFact_Templates")->where("id", $this->layout_template)->asArray()->execute();
        if(!$this->document["Type"]) {
            $this->layout_template = INVOICE_STD_TEMPLATE;
            $this->document = Database_Model::getInstance()->getOne("HostFact_Templates")->where("id", $this->layout_template)->asArray()->execute();
        }
        $this->UBLFilename = $this->document["FileName"] ? strpos($this->document["FileName"], ".pdf") === false ? $this->document["FileName"] . ".xml" : str_replace(".pdf", ".xml", $this->document["FileName"]) : $this->document["Title"] . ".xml";
        if(isset($this->invoice->InvoiceCode)) {
            $this->UBLFilename = str_replace("[invoice->InvoiceCode]", $this->invoice->InvoiceCode, $this->UBLFilename);
            if($this->document["Type"] != "other" && strpos($this->UBLFilename, $this->invoice->InvoiceCode) === false) {
                $this->UBLFilename = sprintf(__("xml default UBL filename"), $this->invoice->InvoiceCode);
            }
        }
        $this->UBLFilename = preg_replace("/[\\/:*?|\\\\]/i", "", htmlspecialchars_decode($this->UBLFilename));
        return $xml;
    }
    private function _setUBLHeader()
    {
        $header = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n        <Invoice xmlns=\"urn:oasis:names:specification:ubl:schema:xsd:Invoice-2\" xmlns:cac=\"urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2\" xmlns:cbc=\"urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"urn:oasis:names:specification:ubl:schema:xsd:Invoice-2 http://docs.oasis-open.org/ubl/os-UBL-2.1/xsd/maindoc/UBL-Invoice-2.1.xsd\">\n        </Invoice>";
        return $header;
    }
    private function _setAccountingSupplier($xml, $creditor, $creditor_type)
    {
        $AccountingSupplierParty = $xml->addChild("AccountingSupplierParty", "", $this->ns_cac);
        if($creditor_type == "WFS") {
            $AccountingSupplierParty = $AccountingSupplierParty->addChild("Party", "", $this->ns_cac);
            if($creditor->CompanyName) {
                $AccountingSupplierParty->addChild("PartyName", "", $this->ns_cac)->addChild("Name", $creditor->CompanyName, $this->ns_cbc);
            }
            $postalAddress = $AccountingSupplierParty->addChild("PostalAddress", "", $this->ns_cac);
            if($creditor->Address) {
                $postalAddress->addChild("StreetName", str_replace("\n", ", ", $creditor->Address), $this->ns_cbc);
            }
            if($creditor->City) {
                $postalAddress->addChild("CityName", $creditor->City, $this->ns_cbc);
            }
            if($creditor->ZipCode) {
                $postalAddress->addChild("PostalZone", $creditor->ZipCode, $this->ns_cbc);
            }
            if($creditor->Country) {
                $country = $postalAddress->addChild("Country", "", $this->ns_cac);
                $idcode = $country->addChild("IdentificationCode", str_replace("EU-", "", $creditor->Country), $this->ns_cbc);
                $idcode->addAttribute("listID", "ISO3166-1:Alpha2");
                $idcode->addAttribute("listAgencyID", "6");
            }
            if($creditor->TaxNumber) {
                $partyTaxScheme = $AccountingSupplierParty->addChild("PartyTaxScheme", "", $this->ns_cac);
                $companyID = $partyTaxScheme->addChild("CompanyID", preg_replace("/[^a-z0-9]/i", "", $creditor->TaxNumber), $this->ns_cbc);
                $companyID->addAttribute("schemeID", "NL:VAT");
                $companyID->addAttribute("schemeAgencyID", "ZZZ");
                $taxSchemeID = $partyTaxScheme->addChild("TaxScheme", "", $this->ns_cac)->addChild("ID", "VAT", $this->ns_cbc);
                $taxSchemeID->addAttribute("schemeID", "UN/ECE 5153");
                $taxSchemeID->addAttribute("schemeAgencyID", "6");
            }
            if($creditor->CompanyNumber) {
                $kvk = $AccountingSupplierParty->addChild("PartyLegalEntity", "", $this->ns_cac)->addChild("CompanyID", $creditor->CompanyNumber, $this->ns_cbc);
                $kvk->addAttribute("schemeID", "NL:KVK");
                $kvk->addAttribute("schemeAgencyID", "ZZZ");
            }
            $contact = $AccountingSupplierParty->addChild("Contact", "", $this->ns_cac);
            if(isset($creditor->SurName)) {
                $contact->addChild("Name", $creditor->SurName, $this->ns_cbc);
            }
            if($creditor->PhoneNumber || $creditor->PhoneNumber) {
                $contact->addChild("Telephone", $creditor->PhoneNumber ? $creditor->PhoneNumber : $creditor->MobileNumber, $this->ns_cbc);
            }
            if($creditor->FaxNumber) {
                $contact->addChild("Telefax", $creditor->FaxNumber, $this->ns_cbc);
            }
            if($creditor->EmailAddress) {
                $contact->addChild("ElectronicMail", $creditor->EmailAddress, $this->ns_cbc);
            }
        }
        return $xml;
    }
    private function _setAccountingCustomer($xml, $invoice, $debtor, $debtor_type)
    {
        $AccountingCustomerParty = $xml->addChild("AccountingCustomerParty", "", $this->ns_cac);
        if($debtor_type == "WFS") {
        } else {
            $AccountingCustomerParty->addChild("SupplierAssignedAccountID", $debtor->DebtorCode, $this->ns_cbc);
            $AccountingCustomerParty = $AccountingCustomerParty->addChild("Party", "", $this->ns_cac);
            if($invoice->CompanyName) {
                $AccountingCustomerParty->addChild("PartyName", "", $this->ns_cac)->addChild("Name", $invoice->CompanyName, $this->ns_cbc);
            } else {
                $AccountingCustomerParty->addChild("PartyName", "", $this->ns_cac)->addChild("Name", $invoice->Initials . " " . $invoice->SurName, $this->ns_cbc);
            }
            $postalAddress = $AccountingCustomerParty->addChild("PostalAddress", "", $this->ns_cac);
            if($invoice->Address) {
                $postalAddress->addChild("StreetName", str_replace("\n", ", ", $invoice->Address), $this->ns_cbc);
            }
            if($invoice->City) {
                $postalAddress->addChild("CityName", $invoice->City, $this->ns_cbc);
            }
            if($invoice->ZipCode) {
                $postalAddress->addChild("PostalZone", $invoice->ZipCode, $this->ns_cbc);
            }
            if($invoice->Country) {
                $country = $postalAddress->addChild("Country", "", $this->ns_cac);
                $idcode = $country->addChild("IdentificationCode", str_replace("EU-", "", $invoice->Country), $this->ns_cbc);
                $idcode->addAttribute("listID", "ISO3166-1:Alpha2");
                $idcode->addAttribute("listAgencyID", "6");
            }
            if($debtor->TaxNumber) {
                $partyTaxScheme = $AccountingCustomerParty->addChild("PartyTaxScheme", "", $this->ns_cac);
                $companyID = $partyTaxScheme->addChild("CompanyID", preg_replace("/[^a-z0-9]/i", "", $debtor->TaxNumber), $this->ns_cbc);
                $companyID->addAttribute("schemeID", "NL:VAT");
                $companyID->addAttribute("schemeAgencyID", "ZZZ");
                $taxSchemeID = $partyTaxScheme->addChild("TaxScheme", "", $this->ns_cac)->addChild("ID", "VAT", $this->ns_cbc);
                $taxSchemeID->addAttribute("schemeID", "UN/ECE 5153");
                $taxSchemeID->addAttribute("schemeAgencyID", "6");
            }
            if($debtor->CompanyNumber) {
                $kvk = $AccountingCustomerParty->addChild("PartyLegalEntity", "", $this->ns_cac)->addChild("CompanyID", $debtor->CompanyNumber, $this->ns_cbc);
                $kvk->addAttribute("schemeID", "NL:KVK");
                $kvk->addAttribute("schemeAgencyID", "ZZZ");
            }
            $contact = $AccountingCustomerParty->addChild("Contact", "", $this->ns_cac);
            if($invoice->Initials || $invoice->SurName) {
                $contact->addChild("Name", $invoice->Initials . " " . $invoice->SurName, $this->ns_cbc);
            }
            if($debtor->PhoneNumber || $debtor->MobileNumber) {
                $contact->addChild("Telephone", $debtor->PhoneNumber ? $debtor->PhoneNumber : $debtor->MobileNumber, $this->ns_cbc);
            }
            if($debtor->FaxNumber) {
                $contact->addChild("Telefax", $debtor->FaxNumber, $this->ns_cbc);
            }
            if($invoice->EmailAddress) {
                $contact->addChild("ElectronicMail", $invoice->EmailAddress, $this->ns_cbc);
            }
        }
        return $xml;
    }
    public function importUBL($ubl_file)
    {
        if(!@file_exists($ubl_file)) {
            return false;
        }
        $xml = simplexml_load_file($ubl_file);
        $this->ns = $xml->getNamespaces(true);
        if($this->validateUBLfile($xml) === false) {
            return false;
        }
        $invoice_array = [];
        $invoice_array["reference"] = (string) $xml->children($this->ns["cbc"])->ID;
        $invoice_array["issuedate"] = (string) $xml->children($this->ns["cbc"])->IssueDate;
        $invoice_array["duedate"] = (string) $xml->children($this->ns["cbc"])->DueDate;
        $invoice_array["amountexcl"] = $this->_getXMLChild($xml, ["LegalMonetaryTotal" => "cac", "TaxExclusiveAmount" => "cbc"]);
        if($this->_validateChildCurrency($this->_getXMLChild($xml, ["LegalMonetaryTotal" => "cac", "TaxExclusiveAmount" => "cbc"], false)) === false) {
            return false;
        }
        $invoice_array["amountincl"] = $this->_getXMLChild($xml, ["LegalMonetaryTotal" => "cac", "TaxInclusiveAmount" => "cbc"]);
        if($this->_validateChildCurrency($this->_getXMLChild($xml, ["LegalMonetaryTotal" => "cac", "TaxInclusiveAmount" => "cbc"], false)) === false) {
            return false;
        }
        if(!$invoice_array["amountincl"]) {
            $invoice_array["amountincl"] = $this->_getXMLChild($xml, ["LegalMonetaryTotal" => "cac", "PayableAmount" => "cbc"]);
            if($this->_validateChildCurrency($this->_getXMLChild($xml, ["LegalMonetaryTotal" => "cac", "PayableAmount" => "cbc"], false)) === false) {
                return false;
            }
        }
        $invoice_type_code = $this->_getXMLChild($xml, ["InvoiceTypeCode" => "cbc"]);
        if(!$invoice_type_code) {
            $invoice_type_code = "380";
        }
        $invoice_array["status"] = "invoice";
        if($invoice_type_code == "381" || $invoice_type_code == "380" && $invoice_array["amountincl"] < 0) {
            $invoice_array["status"] = "creditinvoice";
        }
        if(!empty($xml->children($this->ns["cac"])->AdditionalDocumentReference)) {
            foreach ($xml->children($this->ns["cac"])->AdditionalDocumentReference as $_document) {
                $_attr = $_document->children($this->ns["cac"])->Attachment->children($this->ns["cbc"])->attributes();
                if($_document->children($this->ns["cac"])->Attachment->children($this->ns["cbc"]) && strtolower((string) $_attr["mimeCode"]) == "application/pdf") {
                    $invoice_array["pdf"]["filename"] = $this->_getXMLChild($_document, ["ID" => "cbc"]);
                    $invoice_array["pdf"]["base64"] = $this->_getXMLChild($_document, ["Attachment" => "cac", "EmbeddedDocumentBinaryObject" => "cbc"]);
                }
            }
        }
        $invoice_array["creditor"]["customercode"] = $this->_getXMLChild($xml, ["AccountingCustomerParty" => "cac", "SupplierAssignedAccountID" => "cbc"]);
        $AccountingSupplier = $this->_getXMLChild($xml, ["AccountingSupplierParty" => "cac", "Party" => "cac"], false);
        if(!empty($AccountingSupplier->children($this->ns["cac"])->PartyIdentification)) {
            foreach ($AccountingSupplier->children($this->ns["cac"])->PartyIdentification as $_test) {
                $_attr = $_test->children($this->ns["cbc"])->attributes();
                if(strtolower((string) $_attr["schemeAgencyName"]) == "kvk") {
                    $invoice_array["creditor"]["kvk"] = (string) $_test->children($this->ns["cbc"])->ID;
                } elseif(strtolower((string) $_attr["schemeAgencyName"]) == "btw") {
                    $invoice_array["creditor"]["btw"] = (string) $_test->children($this->ns["cbc"])->ID;
                }
            }
        }
        $invoice_array["creditor"]["company"] = $this->_getXMLChild($AccountingSupplier, ["PartyName" => "cac", "Name" => "cbc"]);
        $invoice_array["creditor"]["accountnumber"] = $this->_getXMLChild($xml, ["PaymentMeans" => "cac", "PayeeFinancialAccount" => "cac", "ID" => "cbc"]);
        $name = $this->_getXMLChild($AccountingSupplier, ["Contact" => "cac", "Name" => "cbc"]);
        if($name) {
            $name_parts = explode(" ", $name, 2);
            if(!empty($name_parts)) {
                $invoice_array["creditor"]["initials"] = count($name_parts) == 1 ? "" : $name_parts[0];
                $invoice_array["creditor"]["surname"] = count($name_parts) == 1 ? $name_parts[0] : $name_parts[1];
            }
        }
        $postalAddress = $this->_getXMLChild($AccountingSupplier, ["PostalAddress" => "cac"], false);
        if(!empty($postalAddress)) {
            $invoice_array["creditor"]["address"] = $this->_getXMLChild($postalAddress, ["StreetName" => "cbc"]);
            $invoice_array["creditor"]["city"] = $this->_getXMLChild($postalAddress, ["CityName" => "cbc"]);
            $invoice_array["creditor"]["postal"] = $this->_getXMLChild($postalAddress, ["PostalZone" => "cbc"]);
            $invoice_array["creditor"]["country"] = $this->_getXMLChild($postalAddress, ["Country" => "cac", "IdentificationCode" => "cbc"]);
        } else {
            $physicalLocation = $this->_getXMLChild($AccountingSupplier, ["PhysicalLocation" => "cac"], false);
            if(!empty($physicalLocation)) {
                $invoice_array["creditor"]["address"] = $this->_getXMLChild($physicalLocation, ["Address" => "cac", "StreetName" => "cbc"]);
                $invoice_array["creditor"]["city"] = $this->_getXMLChild($physicalLocation, ["Address" => "cac", "CityName" => "cbc"]);
                $invoice_array["creditor"]["postal"] = $this->_getXMLChild($physicalLocation, ["Address" => "cac", "PostalZone" => "cbc"]);
                $invoice_array["creditor"]["country"] = $this->_getXMLChild($physicalLocation, ["Address" => "cac", "Country" => "cac", "IdentificationCode" => "cbc"]);
            }
        }
        $invoice_array["creditor"]["phonenumber"] = $this->_getXMLChild($AccountingSupplier, ["Contact" => "cac", "Telephone" => "cbc"]);
        $invoice_array["creditor"]["faxnumber"] = $this->_getXMLChild($AccountingSupplier, ["Contact" => "cac", "Telefax" => "cbc"]);
        $invoice_array["creditor"]["emailaddress"] = $this->_getXMLChild($AccountingSupplier, ["Contact" => "cac", "ElectronicMail" => "cbc"]);
        $payment_means_code = $this->_getXMLChild($xml, ["PaymentMeans" => "cac", "PaymentMeansCode" => "cbc"]);
        if($payment_means_code && $payment_means_code == "49") {
            $invoice_array["authorisation"] = "yes";
        }
        $invoice_lines = $this->_getXMLChild($xml, ["InvoiceLine" => "cac"], false);
        $counter = 0;
        if(!empty($invoice_lines)) {
            foreach ($invoice_lines as $_invoice_line) {
                $line = [];
                $line["number"] = $this->_getXMLChild($_invoice_line, ["InvoicedQuantity" => "cbc"]);
                if(isEmptyFloat($line["number"])) {
                    $line["number"] = 1;
                }
                $line["description"] = $this->_getXMLChild($_invoice_line, ["Item" => "cac", "Description" => "cbc"]);
                if(!$line["description"]) {
                    $line["description"] = $this->_getXMLChild($_invoice_line, ["Item" => "cac", "Name" => "cbc"]);
                }
                $line["description"] = strip_tags($line["description"]);
                $line_total = $this->_getXMLChild($_invoice_line, ["LineExtensionAmount" => "cbc"]);
                if($this->_validateChildCurrency($this->_getXMLChild($_invoice_line, ["LineExtensionAmount" => "cbc"], false)) === false) {
                    return false;
                }
                $line["priceexcl"] = $line_total / $line["number"];
                $line["taxpercentage"] = $this->_getXMLChild($_invoice_line, ["TaxTotal" => "cac", "TaxSubtotal" => "cac", "Percent" => "cbc"]);
                if(!$line["taxpercentage"]) {
                    $line["taxpercentage"] = $this->_getXMLChild($_invoice_line, ["TaxTotal" => "cac", "TaxSubtotal" => "cac", "TaxCategory" => "cac", "Percent" => "cbc"]);
                }
                if(!$line["taxpercentage"]) {
                    $line["taxpercentage"] = $this->_getXMLChild($_invoice_line, ["Item" => "cac", "ClassifiedTaxCategory" => "cac", "Percent" => "cbc"]);
                }
                if(!$line["taxpercentage"]) {
                    $line["taxpercentage"] = 0;
                }
                $invoice_array["lines"][$counter] = $line;
                $counter++;
            }
        }
        $discounts_charges = $this->_getXMLChild($xml, ["AllowanceCharge" => "cac"], false);
        if(!empty($discounts_charges)) {
            foreach ($discounts_charges as $_discount_charge) {
                $line = [];
                $is_discount = $this->_getXMLChild($_discount_charge, ["ChargeIndicator" => "cbc"]) == "false" ? true : false;
                $line["number"] = "1";
                $line["description"] = $this->_getXMLChild($_discount_charge, ["AllowanceChargeReason" => "cbc"]);
                $line_total = $this->_getXMLChild($_discount_charge, ["Amount" => "cbc"]);
                if($this->_validateChildCurrency($this->_getXMLChild($_discount_charge, ["Amount" => "cbc"], false)) === false) {
                    return false;
                }
                $line["priceexcl"] = $is_discount ? $line_total / $line["number"] * -1 : $line_total / $line["number"];
                $line["taxpercentage"] = $this->_getXMLChild($_discount_charge, ["TaxCategory" => "cac", "Percent" => "cbc"]);
                $invoice_array["lines"][$counter] = $line;
                $counter++;
            }
        }
        return $invoice_array;
    }
    private function _getXMLChild($xml, $children = [], $has_value = true)
    {
        $child = "";
        foreach ($children as $_child => $_ns) {
            if(is_object($child)) {
                $child = $child->children($this->ns[$_ns])->{$_child};
            } else {
                $child = $xml->children($this->ns[$_ns])->{$_child};
            }
            if(is_object($child) && empty($child)) {
                return false;
            }
        }
        return $has_value === true ? (string) $child : $child;
    }
    public function attachUBLtoCreditInvoice($ubl_data, $creditinvoice_obj)
    {
        if(isset($ubl_data["reference"]) && $ubl_data["reference"]) {
            $creditinvoice_obj->InvoiceCode = $ubl_data["reference"];
        }
        if(isset($ubl_data["issuedate"]) && $ubl_data["issuedate"]) {
            $creditinvoice_obj->Date = rewrite_date_db2site($ubl_data["issuedate"]);
        }
        if($ubl_data["status"] == "creditinvoice") {
            $creditinvoice_obj->Status = 8;
        }
        if(isset($ubl_data["issuedate"]) && $ubl_data["issuedate"] && isset($ubl_data["duedate"]) && $ubl_data["duedate"]) {
            $date1 = new DateTime($ubl_data["issuedate"]);
            $date2 = new DateTime($ubl_data["duedate"]);
            $term = intval($date1->diff($date2)->days);
            if($term && is_numeric($term)) {
                $creditinvoice_obj->Term = $term;
            }
        }
        if(isset($ubl_data["amountexcl"]) && $ubl_data["amountexcl"]) {
            $creditinvoice_obj->AmountExcl = $ubl_data["amountexcl"];
        }
        if(isset($ubl_data["amountincl"]) && $ubl_data["amountincl"]) {
            $creditinvoice_obj->AmountIncl = $ubl_data["amountincl"];
        }
        if(isset($ubl_data["pdf"]) && $ubl_data["pdf"]["filename"] && $ubl_data["pdf"]["base64"]) {
            $creditinvoice_obj->AttachmentUBLPDF = $ubl_data["pdf"];
        }
        if(!empty($ubl_data["creditor"])) {
            $creditinvoice_obj->newCreditor = new creditor();
            if(!isset($creditinvoice_obj->CreditorID) && $ubl_data["creditor"]["accountnumber"]) {
                $result = $creditinvoice_obj->newCreditor->all(["id", "CreditorCode", "MyCustomerCode", "AccountNumber"], "CreditorCode", "ASC", "-1", "AccountNumber", $ubl_data["creditor"]["accountnumber"]);
                if($result && $result["CountRows"] == 1) {
                    unset($result["CountRows"]);
                    $creditinvoice_obj->CreditorID = $result[key($result)]["id"];
                }
            }
            if(!isset($creditinvoice_obj->CreditorID) && $ubl_data["creditor"]["company"]) {
                $result = $creditinvoice_obj->newCreditor->all(["id", "CreditorCode", "MyCustomerCode", "AccountNumber"], "CreditorCode", "ASC", "-1", "CompanyNameExactMatch", $ubl_data["creditor"]["company"]);
                if($result && $result["CountRows"] == 1) {
                    unset($result["CountRows"]);
                    $creditinvoice_obj->CreditorID = $result[key($result)]["id"];
                }
            }
            if(!isset($creditinvoice_obj->CreditorID) && $ubl_data["creditor"]["customercode"]) {
                $result = $creditinvoice_obj->newCreditor->all(["id", "CreditorCode", "MyCustomerCode", "AccountNumber"], "CreditorCode", "ASC", "-1", "MyCustomerCodeExactMatch", $ubl_data["creditor"]["customercode"]);
                if($result && $result["CountRows"] == 1) {
                    unset($result["CountRows"]);
                    $creditinvoice_obj->CreditorID = $result[key($result)]["id"];
                }
            }
            if(isset($creditinvoice_obj->CreditorID) && $result[key($result)]["MyCustomerCode"] == "" && $ubl_data["creditor"]["customercode"] && strlen($ubl_data["creditor"]["customercode"]) <= 50) {
                $creditinvoice_obj->MyCustomerCode = $ubl_data["creditor"]["customercode"];
            }
            if(isset($creditinvoice_obj->CreditorID) && $result[key($result)]["AccountNumber"] == "" && $ubl_data["creditor"]["accountnumber"] && strlen($ubl_data["creditor"]["accountnumber"]) <= 50) {
                $creditinvoice_obj->AccountNumber = $ubl_data["creditor"]["accountnumber"];
            }
            if(!isset($creditinvoice_obj->CreditorID)) {
                $creditinvoice_obj->newCreditor->CompanyName = $ubl_data["creditor"]["company"];
                $creditinvoice_obj->newCreditor->Initials = $ubl_data["creditor"]["initials"];
                $creditinvoice_obj->newCreditor->SurName = $ubl_data["creditor"]["surname"];
                $creditinvoice_obj->newCreditor->Address = $ubl_data["creditor"]["address"];
                $creditinvoice_obj->newCreditor->City = $ubl_data["creditor"]["city"];
                $creditinvoice_obj->newCreditor->ZipCode = $ubl_data["creditor"]["postal"];
                $creditinvoice_obj->newCreditor->Country = $ubl_data["creditor"]["country"];
                $creditinvoice_obj->newCreditor->PhoneNumber = $ubl_data["creditor"]["phonenumber"];
                $creditinvoice_obj->newCreditor->FaxNumber = $ubl_data["creditor"]["faxnumber"];
                $creditinvoice_obj->newCreditor->EmailAddress = $ubl_data["creditor"]["emailaddress"];
                if($ubl_data["creditor"]["customercode"] && strlen($ubl_data["creditor"]["customercode"]) <= 50) {
                    $creditinvoice_obj->newCreditor->UBLMyCustomerCode = $ubl_data["creditor"]["customercode"];
                }
                if($ubl_data["creditor"]["accountnumber"] && strlen($ubl_data["creditor"]["accountnumber"]) <= 50) {
                    $creditinvoice_obj->newCreditor->UBLAccountNumber = $ubl_data["creditor"]["accountnumber"];
                }
            }
        }
        if(isset($ubl_data["authorisation"]) && $ubl_data["authorisation"] == "yes") {
            $creditinvoice_obj->Authorisation = "yes";
        }
        if(!empty($ubl_data["lines"])) {
            $creditinvoice_obj->Elements = [];
            $index = 0;
            foreach ($ubl_data["lines"] as $_invoice_line) {
                $creditinvoice_obj->Elements[$index]["Number"] = $_invoice_line["number"];
                $creditinvoice_obj->Elements[$index]["Description"] = $_invoice_line["description"];
                $creditinvoice_obj->Elements[$index]["PriceExcl"] = $_invoice_line["priceexcl"];
                $creditinvoice_obj->Elements[$index]["TaxPercentage"] = $_invoice_line["taxpercentage"];
                $index++;
            }
            $creditinvoice_obj->Elements["CountRows"] = $index;
        }
        return $creditinvoice_obj;
    }
    public function validateUBLfile($xml)
    {
        $ubl_invoice_id = (string) $xml->children($this->ns["cbc"])->ID;
        if(!$ubl_invoice_id || !is_string($ubl_invoice_id)) {
            $this->Error[] = __("file is not a valid ubl file");
        }
        $ubl_invoice_date = (string) $xml->children($this->ns["cbc"])->IssueDate;
        if(!$ubl_invoice_date || !is_string($ubl_invoice_date)) {
            $this->Error[] = __("file is not a valid ubl file");
        }
        $ubl_currency = strtoupper((string) $xml->children($this->ns["cbc"])->DocumentCurrencyCode);
        if($ubl_currency && $ubl_currency != strtoupper(CURRENCY_CODE)) {
            $this->Error[] = sprintf(__("ubl file has invalid currency"), $ubl_currency);
        }
        if(!empty($this->Error)) {
            return false;
        }
        return true;
    }
    private function _validateChildCurrency($ubl_child)
    {
        if($ubl_child) {
            $_attr = $ubl_child->attributes();
            $currency = strtoupper((string) $_attr["currencyID"]);
            if(is_string($currency) && $currency != "" && $currency != strtoupper(CURRENCY_CODE)) {
                $this->Error[] = sprintf(__("ubl child has invalid currency"), $currency);
                return false;
            }
        }
        return true;
    }
    private function _getTaxCategory($tax_amount, $reverse_tax = false)
    {
        $tax_amount = (int) $tax_amount;
        if($tax_amount === 21) {
            $tax_category = "S";
        } elseif($tax_amount < 21 && $tax_amount !== 0) {
            $tax_category = "AA";
        } elseif(21 < $tax_amount) {
            $tax_category = "H";
        } elseif($tax_amount === 0 && $reverse_tax) {
            $tax_category = "AE";
        } else {
            $tax_category = "Z";
        }
        return $tax_category;
    }
    private function _rounding($amount, $decimals = 2)
    {
        $dec_point = ".";
        $thousands_sep = false;
        return number_format($amount, $decimals, $dec_point, $thousands_sep);
    }
}

?>