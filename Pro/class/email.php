<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class email
{
    public $Identifier;
    public $Recipient;
    public $Debtor;
    public $CarbonCopy;
    public $BlindCarbonCopy;
    public $Sender;
    public $Subject;
    public $Message;
    public $Attachment;
    public $SentDate;
    public $Status;
    public $AttachmentName;
    public $Sent_bcc;
    public $AutoSubmitted;
    public $CountRows;
    public $Error;
    public $Warning;
    public $Success;
    public $Variables = ["Identifier", "Recipient", "Debtor", "CarbonCopy", "BlindCarbonCopy", "Sender", "Subject", "Message", "Attachment", "SentDate", "Status"];
    const RETENTION_DAYS_EMAIL_ARCHIVE = 30;
    public function __construct($director = "", $object = NULL)
    {
        global $debtor;
        if($director == "invoice") {
            unset($pricequote);
            $invoice = $object;
        } elseif($director == "pricequote") {
            unset($invoice);
            $pricequote = $object;
        }
        if(isset($invoice)) {
            $result = Database_Model::getInstance()->getOne(["HostFact_EmailTemplates", "HostFact_Invoice", "HostFact_Templates"], ["HostFact_EmailTemplates.*"])->where("HostFact_Invoice.id", $invoice->Identifier)->where("HostFact_Invoice.Template = HostFact_Templates.id")->where("HostFact_Templates.EmailTemplate = HostFact_EmailTemplates.id")->where("HostFact_Templates.Type", "invoice")->execute();
            if($result && 0 < $result->id) {
                foreach ($result as $key => $value) {
                    if($key != "id") {
                        $this->{$key} = $value;
                    }
                }
            } else {
                $result = Database_Model::getInstance()->getOne(["HostFact_EmailTemplates", "HostFact_Templates"], ["HostFact_EmailTemplates.*"])->where("HostFact_Templates.Standard", "1")->where("HostFact_Templates.EmailTemplate = HostFact_EmailTemplates.id")->where("HostFact_Templates.Type", "invoice")->execute();
                if($result && 0 < $result->id) {
                    foreach ($result as $key => $value) {
                        if($key != "id") {
                            $this->{$key} = $value;
                        }
                    }
                }
            }
        } elseif(isset($pricequote)) {
            $result = Database_Model::getInstance()->getOne(["HostFact_EmailTemplates", "HostFact_PriceQuote", "HostFact_Templates"], ["HostFact_EmailTemplates.*"])->where("HostFact_PriceQuote.id", $pricequote->Identifier)->where("HostFact_PriceQuote.Template = HostFact_Templates.id")->where("HostFact_Templates.EmailTemplate = HostFact_EmailTemplates.id")->where("HostFact_Templates.Type", "pricequote")->execute();
            if($result && 0 < $result->id) {
                foreach ($result as $key => $value) {
                    if($key != "id") {
                        $this->{$key} = $value;
                    }
                }
            } else {
                $result = Database_Model::getInstance()->getOne(["HostFact_EmailTemplates", "HostFact_Templates"], ["HostFact_EmailTemplates.*"])->where("HostFact_Templates.Standard", "1")->where("HostFact_Templates.EmailTemplate = HostFact_EmailTemplates.id")->where("HostFact_Templates.Type", "pricequote")->execute();
                if($result && 0 < $result->id) {
                    foreach ($result as $key => $value) {
                        if($key != "id") {
                            $this->{$key} = $value;
                        }
                    }
                }
            }
        }
        $this->Status = "0";
        $this->Sent_bcc = true;
        $this->AutoSubmitted = false;
        $this->Error = [];
        $this->Warning = [];
        $this->Success = [];
    }
    public function show()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for email");
            return false;
        }
        $result = Database_Model::getInstance()->getOne("HostFact_Emails", ["*"])->where("id", $this->Identifier)->execute();
        if(!$result || empty($result->id)) {
            $this->Error[] = __("invalid identifier for email");
            return false;
        }
        foreach ($result as $key => $value) {
            if($key != "id") {
                $this->{$key} = $value;
            }
        }
        return true;
    }
    public function add($objects = [])
    {
        global $invoice;
        global $pricequote;
        global $account;
        $hosting = isset($_SESSION["hosting"]) ? $_SESSION["hosting"] : "";
        $domain = isset($_SESSION["domain"]) ? $_SESSION["domain"] : "";
        if(is_array($objects)) {
            foreach ($objects as $objName => $object) {
                if(in_array($objName, ["debtor", "account", "invoice", "pricequote"])) {
                    if(isset($object->EmailAddress)) {
                        $object->EmailAddress = check_email_address($object->EmailAddress, "convert", ", ");
                    }
                    if(isset($object->InvoiceEmailAddress)) {
                        $object->InvoiceEmailAddress = check_email_address($object->InvoiceEmailAddress, "convert", ", ");
                    }
                    if(isset($object->ReminderEmailAddress)) {
                        $object->ReminderEmailAddress = check_email_address($object->ReminderEmailAddress, "convert", ", ");
                    }
                }
                ${$objName} = $object;
            }
        }
        $this->SentDate = date("YmdHis");
        require_once "class/company.php";
        $company = new company();
        $company->show();
        require_once "class/debtor.php";
        $debtor = new debtor();
        if(isset($this->Debtor) && 0 < $this->Debtor) {
            $debtor->Identifier = $this->Debtor;
        } else {
            $debtor->Identifier = isset($invoice->Debtor) ? $invoice->Debtor : (isset($pricequote->Debtor) ? $pricequote->Debtor : (isset($this->Debtor) ? $this->Debtor : 0));
        }
        if(0 < $debtor->Identifier) {
            $debtor->show();
            $debtor->EmailAddress = check_email_address($debtor->EmailAddress, "convert", ", ");
            $debtor->InvoiceEmailAddress = check_email_address(!empty($debtor->InvoiceEmailAddress) ? $debtor->InvoiceEmailAddress : $debtor->EmailAddress, "convert", ", ");
            $debtor->ReminderEmailAddress = check_email_address(!empty($debtor->ReminderEmailAddress) ? $debtor->ReminderEmailAddress : $debtor->InvoiceEmailAddress, "convert", ", ");
        }
        if(isset($invoice->Identifier)) {
            $format = $invoice;
            $invoice->format();
        }
        if(isset($pricequote->Identifier)) {
            $format = $pricequote;
            $pricequote->format();
            $invoice = $pricequote;
        }
        if(isset($this->PeriodicIdentifier)) {
            require_once "class/periodic.php";
            $periodic = new periodic();
            $periodic->Identifier = $this->PeriodicIdentifier;
            $periodic->show();
            $periodic->format();
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
                if(isset($account->Signature) && $account->Signature && strpos($this->Message, "[account-&gt;Signature]") !== false && strpos($account->Signature, "<body>") !== false) {
                    $pattern = "/<body>(.*)<\\/body>/is";
                    if(preg_match($pattern, $account->Signature, $matches) && $matches[1]) {
                        $account->Signature = $matches[1];
                    }
                    $this->Message = str_replace("[account-&gt;Signature]", $account->Signature, $this->Message);
                }
                if(strpos($this->Message, "[domain-&gt;AuthKey]") !== false && $domain && 0 < $domain->Identifier) {
                    $token = $domain->getToken(true);
                    $_SESSION["domain_token_retrieved"][$domain->Domain . "." . $domain->Tld] = true;
                    if($token !== false) {
                        $domain->show();
                    }
                }
                if(!isset($this->skipEval) || $this->skipEval !== true) {
                    $this->Subject = str_replace("&gt;", ">", $this->Subject);
                    $this->Subject = str_replace("&lt;", "<", $this->Subject);
                    $this->Subject = str_replace("\"", "{&quot;}", $this->Subject);
                    $this->Subject = str_replace("&#39;", "'", $this->Subject);
                    $this->Subject = str_replace("[email->Today]", rewrite_date_db2site(date("Ymd")), $this->Subject);
                    $pattern = "/\\[(([_.\\da-z0-9]+)->([_.\\da-z0-9]+))\\]/i";
                    $replacement = "<?PHP echo (isset(\$\\1)) ? \$\\1 : \"\"; ?>";
                    $str = preg_replace($pattern, $replacement, stripslashes($this->Subject));
                    ob_start();
                    eval("?>" . $str . "<?PHP ");
                    $str = ob_get_contents();
                    ob_end_clean();
                    if($str) {
                        $this->Subject = $str;
                    } else {
                        $str = preg_replace($pattern, $replacement, stripslashes($this->Subject));
                        eval("\$str =\" ?> " . $str . " \";");
                        $this->Subject = $str;
                    }
                    if(substr(trim($this->Subject), 0, 2) == "?>") {
                        $this->Subject = substr(trim($this->Subject), 2);
                    }
                    $this->Subject = str_replace("{&quot;}", "\"", $this->Subject);
                    $str = NULL;
                }
                if($this->Message && (!isset($this->skipEval) || $this->skipEval !== true)) {
                    if(strpos($this->Message, "[debtor-&gt;OpenInvoicesOverview]") !== false) {
                        $_add_draft_invoice_variables = isset($invoice->Identifier) && 0 < $invoice->Identifier && in_array($invoice->Status, [0, 1]) && $invoice->Authorisation == "no" ? true : false;
                        $this->Message = str_replace("[debtor-&gt;OpenInvoicesOverview]", $this->addOpenInvoiceTable($debtor, false, $_add_draft_invoice_variables), $this->Message);
                        unset($_add_draft_invoice_variables);
                        $this->Message = str_replace("[invoice->AmountIncl_NoFormat]", money(deformat_money($invoice->AmountIncl), false), $this->Message);
                    }
                    if(strpos($this->Message, "[debtor-&gt;OtherOpenInvoicesOverview]") !== false) {
                        $_exclude_invoice_id = isset($invoice->Identifier) && 0 < $invoice->Identifier ? $invoice->Identifier : 0;
                        $this->Message = str_replace("[debtor-&gt;OtherOpenInvoicesOverview]", $this->addOpenInvoiceTable($debtor, $_exclude_invoice_id), $this->Message);
                        unset($_exclude_invoice_id);
                    }
                    if(strpos($this->Message, "[has_other_outstanding_invoices]") !== false) {
                        $_exclude_invoice_id = isset($invoice->Identifier) && 0 < $invoice->Identifier ? $invoice->Identifier : 0;
                        $open_invoices = new invoice();
                        $open_invoices_list = $open_invoices->all(["InvoiceCode", "Authorisation"], false, false, -1, "Debtor", $debtor->Identifier, "2|3");
                        $number_of_other_outstanding_invoices = 0;
                        foreach ($open_invoices_list as $k => $_invoice) {
                            if(!is_numeric($k) || 0 < $_exclude_invoice_id && $_exclude_invoice_id == $_invoice["id"] || $_invoice["Authorisation"] == "yes") {
                            } else {
                                $number_of_other_outstanding_invoices++;
                            }
                        }
                        unset($_exclude_invoice_id);
                    }
                    $this->Message = str_replace("&amp;&amp;", "&&", $this->Message);
                    $this->Message = str_replace("&quot;", "WFQUOTEWF", $this->Message);
                    $this->Message = str_replace("&gt;", ">", $this->Message);
                    $this->Message = str_replace("&lt;", "<", $this->Message);
                    $this->Message = str_replace("\"", "{&quot;}", $this->Message);
                    $this->Message = str_replace("&#39;", "'", $this->Message);
                    $this->Message = str_replace("WFQUOTEWF", "\"", $this->Message);
                    $this->Message = str_replace("[email->Today]", rewrite_date_db2site(date("Ymd")), $this->Message);
                    if(stristr($this->Message, "[debtor->OpenAmountExcl]") || stristr($this->Message, "[debtor->OpenAmountIncl]")) {
                        $debtor->getOpenAmount();
                    }
                    if(stristr($this->Message, "[debtor->TotalAmountExcl]") || stristr($this->Message, "[debtor->AverageOutstandingDays]")) {
                        $debtor->getFinancialInfo();
                    }
                    $this->Message = str_replace("[debtor->Password]", "<?PHP echo (isset(\$debtor->Password)) ? passcrypt(\$debtor->Password) : \"\"; ?>", $this->Message);
                    $this->Message = str_replace("[debtor->Sex]", "[debtor->SexName]", $this->Message);
                    $this->Message = str_replace("[invoice->Sex]", "[invoice->SexName]", $this->Message);
                    $str = stripslashes($this->Message);
                    $pattern = [];
                    $replace = [];
                    $pattern[] = "/\\[period\\](<br ?\\/?>)*(.*)\\[\\/period\\]/Usi";
                    $replace[] = "<?php if(\$invoiceElement->StartPeriod){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[credit\\](<br ?\\/?>)*(.*)\\[\\/credit\\]/Usi";
                    $replace[] = "<?php if(isset(\$invoice->InvoiceCode) && \$invoice->Status == 8){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[non-credit\\](<br ?\\/?>)*(.*)\\[\\/non-credit\\]/Usi";
                    $replace[] = "<?php if(!isset(\$invoice->InvoiceCode) || \$invoice->Status != 8){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[paid\\](<br ?\\/?>)*(.*)\\[\\/paid\\]/Usi";
                    $replace[] = "<?php if(\$invoice->Paid == 1 || \$invoice->Status == 4){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[unpaid\\](<br ?\\/?>)*(.*)\\[\\/unpaid\\]/Usi";
                    $replace[] = "<?php if(!(\$invoice->Paid == 1 || \$invoice->Status == 4)){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[partly_paid\\](<br ?\\/?>)*(.*)\\[\\/partly_paid\\]/Usi";
                    $replace[] = "<?php if((\$invoice->Status == 2 || \$invoice->Status == 3) && floatval(deformat_money(\$invoice->AmountPaid)) != 0){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[directdebit\\](<br ?\\/?>)*(.*)\\[\\/directdebit\\]/Usi";
                    $replace[] = "<?php if(\$invoice->Authorisation == \"yes\"){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[transfer\\](<br ?\\/?>)*(.*)\\[\\/transfer\\]/Usi";
                    $replace[] = "<?php if(\$invoice->Authorisation != \"yes\"){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[reversecharge\\](<br ?\\/?>)*(.*)\\[\\/reversecharge\\]/Usi";
                    $replace[] = "<?php if(\$invoice->VatShift == 'yes' || (\$invoice->VatShift == '' && (\$invoice->AmountTax == money(0,false)) && \$invoice->Country != \$company->Country && \$invoice->CompanyName != \"\" && \$invoice->TaxNumber != \"\" && (\$invoice->Country == \"NL\" || isset(\$_SESSION['wf_cache_array_country_EU'][\$invoice->Country])))){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[male\\](<br ?\\/?>)*(.*)\\[\\/male\\]/Usi";
                    $replace[] = "<?php if(isset(\$invoice->Sex) && \$invoice->Sex == \"m\"){ ?>\\2<?php } elseif(!isset(\$invoice->Sex) && \$debtor->Sex == \"m\") { ?>\\2<?php } ?>";
                    $pattern[] = "/\\[female\\](<br ?\\/?>)*(.*)\\[\\/female\\]/Usi";
                    $replace[] = "<?php if(isset(\$invoice->Sex) && \$invoice->Sex == \"f\"){ ?>\\2<?php } elseif(!isset(\$invoice->Sex) && \$debtor->Sex == \"f\") { ?>\\2<?php } ?>";
                    $pattern[] = "/\\[department\\](<br ?\\/?>)*(.*)\\[\\/department\\]/Usi";
                    $replace[] = "<?php if(isset(\$invoice->Sex) && \$invoice->Sex == \"d\"){ ?>\\2<?php } elseif(!isset(\$invoice->Sex) && \$debtor->Sex == \"d\") { ?>\\2<?php } ?>";
                    $pattern[] = "/\\[is_company\\](<br ?\\/?>)*(.*)\\[\\/is_company\\]/Usi";
                    $replace[] = "<?php if(isset(\$invoice->CompanyName) && \$invoice->CompanyName != \"\"){ ?>\\2<?php } elseif(!isset(\$invoice->CompanyName) && isset(\$debtor->CompanyName) && \$debtor->CompanyName != \"\"){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[is_consumer\\](<br ?\\/?>)*(.*)\\[\\/is_consumer\\]/Usi";
                    $replace[] = "<?php if(isset(\$invoice->CompanyName) && \$invoice->CompanyName == \"\"){ ?>\\2<?php } elseif(!isset(\$invoice->CompanyName) && isset(\$debtor->CompanyName) && \$debtor->CompanyName == \"\"){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[has_contact\\](<br ?\\/?>)*(.*)\\[\\/has_contact\\]/Usi";
                    $replace[] = "<?php if(isset(\$invoice->Initials) && (\$invoice->Initials != \"\" || \$invoice->SurName != \"\")){ ?>\\2<?php } elseif(!isset(\$invoice->Initials) && isset(\$debtor->Initials) && (\$debtor->Initials != \"\" || \$debtor->SurName != \"\")){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[reference\\](<br ?\\/?>)*(.*)\\[\\/reference\\]/Usi";
                    $replace[] = "<?php if(\$invoice->ReferenceNumber != \"\"){ ?>\\2<?php } ?>";
                    $pattern[] = "/\\[has_other_outstanding_invoices\\](<br ?\\/?>)*(.*)\\[\\/has_other_outstanding_invoices\\]/Usi";
                    $replace[] = "<?php if(isset(\$number_of_other_outstanding_invoices) && \$number_of_other_outstanding_invoices > 0){ ?>\\2<?php } ?>";
                    $newline_patterns = [];
                    $newline_replaces = [];
                    foreach ($pattern as $tmp_key => $tmp_pattern) {
                        $newline_patterns[] = str_replace("/Usi", "<br \\/>/i", $tmp_pattern);
                        $newline_replaces[] = $replace[$tmp_key];
                    }
                    $pattern = array_merge($newline_patterns, $pattern);
                    $replace = array_merge($newline_replaces, $replace);
                    $str = preg_replace($pattern, $replace, $str);
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
                    $pattern = "/\\[([_.\\da-z0-9]+)->([_.\\da-z0-9]+)->([_.\\da-z0-9]+)\\]/i";
                    $replacement = "<?PHP echo (isset(\$\\1->\\2->\\3)) ? nl2br(\$\\1->\\2->\\3) : \"\" ; ?>";
                    $str = preg_replace($pattern, $replacement, $str);
                    $pattern = "/\\[([_.\\da-z0-9]+)->([_.\\da-z0-9]+)\\]/i";
                    $replacement = "<?PHP echo (isset(\$\\1->\\2)) ? \"\$\\1->\\2\" : \"[\\1->\\2]\" ; ?>";
                    $str = preg_replace($pattern, $replacement, $str);
                    ob_start();
                    eval("?>" . $str . "<?PHP ");
                    $str = ob_get_contents();
                    ob_end_clean();
                    $str = str_replace("&lt;", "[[LT]]", str_replace("&gt;", "[[GT]]", $str));
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
                    $str = str_replace("[debtor->custom->", "[custom->", $str);
                    if($str) {
                        $this->Message = $str;
                    } else {
                        $str = preg_replace($pattern, $replacement, stripslashes($this->Message));
                        eval("\$str =\" ?> " . $str . " \";");
                        $this->Message = $str;
                    }
                    $this->Message = str_replace("&&", "&amp;&amp;", $this->Message);
                    $this->Message = str_replace("\"", "&quot;", $this->Message);
                    $this->Message = str_replace("{&quot;}", "\"", $this->Message);
                    $this->Message = str_replace("<pre>", "", $this->Message);
                    $this->Message = str_replace("</pre>", "", $this->Message);
                }
                $this->Attachment = str_replace("class/pdf.php?type=other&template=", "TemplateOther", $this->Attachment);
                if(!$this->validate()) {
                    return false;
                }
                if(is_array($this->Attachment)) {
                    $this->Attachment = implode("|", $this->Attachment);
                }
                if(empty($this->Error)) {
                    $result = Database_Model::getInstance()->insert("HostFact_Emails", ["Recipient" => $this->Recipient, "Debtor" => $debtor->Identifier, "CarbonCopy" => $this->CarbonCopy, "BlindCarbonCopy" => $this->BlindCarbonCopy, "Sender" => $this->Sender, "Subject" => $this->Subject, "Message" => $this->Message, "Attachment" => $this->Attachment, "SentDate" => $this->SentDate, "Status" => $this->Status])->execute();
                    $this->Identifier = $result;
                }
                if(isset($invoice->Identifier)) {
                    $invoice = $format;
                    $invoice->show();
                }
                if(isset($pricequote->Identifier)) {
                    $pricequote = $format;
                    $pricequote->show();
                    $invoice = NULL;
                }
                return !empty($this->Error) ? $this->Error : true;
        }
    }
    public function edit()
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for email");
            return false;
        }
        if(!$this->validate()) {
            return false;
        }
        $result = Database_Model::getInstance()->update("HostFact_Emails", ["Recipient" => $this->Recipient, "CarbonCopy" => $this->CarbonCopy, "BlindCarbonCopy" => $this->BlindCarbonCopy, "Sender" => $this->Sender, "Subject" => $this->Subject, "Message" => $this->Message, "Attachment" => $this->Attachment, "SentDate" => $this->SentDate, "Status" => $this->Status])->where("id", $this->Identifier)->execute();
        if($result) {
            return true;
        }
        return false;
    }
    public function delete($remove = false)
    {
        if(!is_numeric($this->Identifier)) {
            $this->Error[] = __("invalid identifier for email");
            return false;
        }
        if(!$remove) {
            $result = Database_Model::getInstance()->update("HostFact_Emails", ["Status" => "9"])->where("id", $this->Identifier)->execute();
        } else {
            $result = Database_Model::getInstance()->delete("HostFact_Emails")->where("id", $this->Identifier)->execute();
        }
        if($result) {
            $this->Success[] = __("email deleted");
            return true;
        }
        return false;
    }
    public function sent($type = false, $id = false, $logging = true, $objects = [])
    {
        if(defined("IS_DEMO") && IS_DEMO) {
            if($this->Identifier) {
                Database_Model::getInstance()->update("HostFact_Emails", ["Status" => "1", "SentDate" => ["RAW" => "NOW()"]])->where("id", $this->Identifier)->execute();
            }
            $this->Success[] = __("demo - mail not available in demo");
            return true;
        }
        if(!$this->Recipient) {
            $this->Error[] = __("no recipient for mail");
            return false;
        }
        if(is_array($objects)) {
            foreach ($objects as $objName => $object) {
                if(in_array($objName, ["debtor", "account", "invoice", "pricequote"])) {
                    if(isset($object->EmailAddress)) {
                        $object->EmailAddress = check_email_address($object->EmailAddress, "convert", ", ");
                    }
                    if(isset($object->InvoiceEmailAddress)) {
                        $object->InvoiceEmailAddress = check_email_address($object->InvoiceEmailAddress, "convert", ", ");
                    }
                    if(isset($object->ReminderEmailAddress)) {
                        $object->ReminderEmailAddress = check_email_address($object->ReminderEmailAddress, "convert", ", ");
                    }
                }
                ${$objName} = $object;
            }
        }
        if(!class_exists("debtor")) {
            require_once "class/debtor.php";
        }
        $debtor = new debtor();
        if(isset($this->Debtor) && 0 < $this->Debtor) {
            $debtor->Identifier = $this->Debtor;
            $debtor->show();
        } else {
            $debtor->Identifier = isset($invoice->Debtor) ? $invoice->Debtor : (isset($pricequote->Debtor) ? $pricequote->Debtor : (isset($this->Debtor) ? $this->Debtor : 0));
        }
        $debtor_id = isset($debtor_id) ? $debtor_id : $debtor->Identifier;
        $name = [];
        $attachment = [];
        $pdf_invoice = false;
        $type = $type ? $type : false;
        if($type) {
            $OutputType = "F";
            require_once "class/pdf.php";
            $company = new company();
            $company->show();
            $debtor = new debtor();
            if(isset($debtor_id)) {
                $debtor->Identifier = $debtor_id;
                $debtor->show();
            }
            if($type == "invoice") {
                $invoice_email_attachments = $debtor->InvoiceEmailAttachments != "" ? $debtor->InvoiceEmailAttachments : INVOICE_EMAIL_ATTACHMENTS;
            } else {
                $invoice_email_attachments = "pdf";
            }
            switch ($invoice_email_attachments) {
                case "pdf":
                    $template = isset($invoice->Template) ? $invoice->Template : (isset($pricequote->Template) ? $pricequote->Template : "test");
                    $pdf = new pdfCreator($template, $objects, "", "D", true);
                    if(!$pdf->generatePDF($OutputType)) {
                        $this->Error = array_merge($this->Error, $pdf->Error);
                        return false;
                    }
                    $pdf_invoice = "temp/" . $pdf->Name;
                    $attachment = ["temp/" . $pdf->Name];
                    $name = [$pdf->Name];
                    break;
                case "pdfubl":
                case "ublwithpdf":
                case "pdfublwithpdf":
                    $template = isset($invoice->Template) ? $invoice->Template : (isset($pricequote->Template) ? $pricequote->Template : "test");
                    $pdf = new pdfCreator($template, $objects, "", "D", true);
                    if(!$pdf->generatePDF($OutputType)) {
                        $this->Error = array_merge($this->Error, $pdf->Error);
                        return false;
                    }
                    if($invoice_email_attachments != "ublwithpdf") {
                        $pdf_invoice = "temp/" . $pdf->Name;
                        $attachment = ["temp/" . $pdf->Name];
                        $name = [$pdf->Name];
                    }
                    $ubl_params = ["invoice" => $invoice, "debtor" => $debtor];
                    if($invoice_email_attachments == "ublwithpdf" || $invoice_email_attachments == "pdfublwithpdf") {
                        $ubl_params["pdf"] = ["path" => "temp/", "filename" => $pdf->Name];
                    }
                    require_once "class/ubl.php";
                    $ubl = new UBL();
                    $ubl->layout_template = $template;
                    $ubl_file_data = $ubl->generateInvoiceUBL($ubl_params);
                    if($ubl->UBLFilename && @file_exists("temp/" . $ubl->UBLFilename)) {
                        @unlink("temp/" . $ubl->UBLFilename);
                    }
                    if(file_put_contents("temp/" . $ubl->UBLFilename, $ubl_file_data, LOCK_EX)) {
                        $attachment[] = "temp/" . $ubl->UBLFilename;
                        $name[] = $ubl->UBLFilename;
                    }
                    break;
                default:
                    if(in_array($type, ["invoice", "pricequote"])) {
                        require_once "class/attachment.php";
                        $obj_attachment = new attachment();
                        $attachment_files = $obj_attachment->getAttachments(${$type}->Identifier, $type);
                        if(count($attachment_files)) {
                            foreach ($attachment_files as $file) {
                                $attachment[] = $obj_attachment->fileDir($file->Reference, $file->Type) . $file->FilenameServer;
                                $name[] = $file->Filename;
                            }
                        }
                        unset($obj_attachment);
                    }
            }
        }
        if($this->Attachment && is_array($this->Attachment)) {
            $attachment = array_merge($attachment, $this->Attachment);
        }
        if($this->Attachment && !is_array($this->Attachment)) {
            $attachment = array_merge($attachment, explode("|", $this->Attachment));
        }
        $attachment_2 = [];
        if(is_array($attachment)) {
            foreach ($attachment as $k => $v) {
                if(trim($v)) {
                    $attachment_2[$k] = $v;
                }
            }
        }
        $attachment = $attachment_2;
        if(is_array($attachment)) {
            foreach ($attachment as $k => $v) {
                if(!isset($name[$k])) {
                    $name[$k] = explode("/", $v);
                    $name[$k] = $name[$k][count($name[$k]) - 1];
                }
            }
        }
        $name_array = $name;
        $name = [];
        if(is_array($attachment)) {
            foreach ($attachment as $k => $v) {
                $name = [];
                if(substr($v, 0, 6) == "class/") {
                    $OutputType = "F";
                    $type = "other";
                    $template = explode("=", $v);
                    $template = $template[count($template) - 1];
                    require_once "class/pdf.php";
                    $company = new company();
                    $company->show();
                    $debtor = new debtor();
                    $debtor->Identifier = $debtor_id;
                    $debtor->show();
                    $template = isset($template) ? $template : $_GET["template"];
                    $pdf = new pdfCreator($template, $objects);
                    if(!$pdf->generatePDF($OutputType)) {
                        $this->Error = array_merge($this->Error, $pdf->Error);
                        return false;
                    }
                    $attachment_temp[$k] = "temp/" . $pdf->Name;
                    $name_array[$k] = $pdf->Name;
                } elseif(substr($v, 0, 13) == "TemplateOther") {
                    $OutputType = "F";
                    $type = "other";
                    $template = substr($v, 13);
                    require_once "class/pdf.php";
                    require_once "class/template.php";
                    $t = new template();
                    $t->Identifier = $template;
                    $t->show();
                    if(!empty($t->Error)) {
                        $this->Error[] = __("other template attachement not found");
                        return false;
                    }
                    $company = new company();
                    $company->show();
                    if(isset($debtor_id)) {
                        $debtor = new debtor();
                        $debtor->Identifier = $debtor_id;
                        $debtor->show();
                    }
                    $objects["debtor"] = $debtor;
                    if($type && isset($invoice->Identifier)) {
                        $invoice->show();
                    }
                    $template = isset($template) ? $template : esc($_GET["template"]);
                    $pdf = new pdfCreator($template, $objects, "other", $OutputType);
                    if(!$pdf->generatePDF($OutputType)) {
                        $this->Error = array_merge($this->Error, $pdf->Error);
                        return false;
                    }
                    $name_array[$k] = $pdf->Name;
                    if($pdf->Name != "" && file_exists("temp/" . $pdf->Name)) {
                        $attachment_temp[$k] = "temp/" . $pdf->Name;
                        $name_array[$k] = $pdf->Name;
                    } else {
                        $this->Error[] = sprintf(__("other template attachement not attached"), $pdf->Name);
                    }
                } elseif(file_exists($v)) {
                    $attachment_temp[$k] = $v;
                } else {
                    $this->Error[] = sprintf(__("attachement not attached"), "");
                }
            }
        }
        unset($objects);
        unset($object);
        if(isset($attachment_temp)) {
            $attachment = $attachment_temp;
        }
        $name = $name_array;
        $this->Sender = htmlspecialchars_decode($this->Sender, ENT_NOQUOTES);
        $this->CarbonCopy = htmlspecialchars_decode($this->CarbonCopy, ENT_NOQUOTES);
        $this->BlindCarbonCopy = htmlspecialchars_decode($this->BlindCarbonCopy, ENT_NOQUOTES);
        if(empty($this->Error)) {
            $unlink_files = [];
            require_once "3rdparty/mail/PHPMailer.php";
            require_once "3rdparty/mail/SMTP.php";
            require_once "3rdparty/mail/Exception.php";
            $mailer = new PHPMailer\PHPMailer\PHPMailer();
            $mailer->SMTPOptions = ["ssl" => ["verify_peer" => false, "allow_self_signed" => false, "verify_peer_name" => false]];
            $mailer->SetLanguage(substr(LANGUAGE_CODE, 0, 2));
            if(defined("SMTP_ON") && SMTP_ON == "1") {
                $mailer->IsSMTP();
                $mailer->SMTPSecure = substr(SMTP_HOST, 0, 6) == "tls://" ? "tls" : (substr(SMTP_HOST, 0, 6) == "ssl://" ? "ssl" : $mailer->SMTPSecure);
                $mailer->Host = substr(SMTP_HOST, 0, 6) == "tls://" ? substr(SMTP_HOST, 6) : (substr(SMTP_HOST, 0, 6) == "ssl://" ? substr(SMTP_HOST, 6) : SMTP_HOST);
                $mailer->SMTPAuth = SMTP_AUTH == "1" ? true : false;
                $mailer->Username = SMTP_USERNAME;
                $mailer->Password = passcrypt(SMTP_PASSWORD);
            } else {
                $mailer->IsMail();
            }
            if(getFirstMailAddress($this->Sender)) {
                $mailer->From = $this->Sender;
                $mailer->FromName = "";
            } else {
                $from = explode("<", $this->Sender);
                if(is_array($from) && count($from) == 2) {
                    $mailer->From = str_replace(">", "", $from[1]);
                    $mailer->FromName = $from[0];
                } else {
                    $mailer->From = $this->Sender;
                }
            }
            $mailer->IsHTML(true);
            $mailer->CharSet = "UTF-8";
            $recips = [];
            $intRecipients = 0;
            $recipients = explode(";", check_email_address($this->Recipient, "convert"));
            foreach ($recipients as $recipient) {
                if($intRecipients === 0) {
                    $mailer->AddAddress($recipient);
                    $recips[] = $recipient;
                } elseif(!in_array($recipient, $recips)) {
                    $mailer->AddCC($recipient);
                    $recips[] = $recipient;
                }
                $intRecipients++;
            }
            $ccs = explode(";", check_email_address($this->CarbonCopy, "convert"));
            foreach ($ccs as $cc) {
                if(!in_array($cc, $recips)) {
                    $mailer->AddCC($cc);
                    $recips[] = $cc;
                }
            }
            $bccs = explode(";", check_email_address($this->BlindCarbonCopy, "convert"));
            foreach ($bccs as $bcc) {
                if(!in_array($bcc, $recips)) {
                    $mailer->AddBCC($bcc);
                    $recips[] = $bcc;
                }
            }
            if($this->Sent_bcc && BCC_EMAILADDRESS && check_email_address(BCC_EMAILADDRESS)) {
                $globalbccs = explode(";", check_email_address(BCC_EMAILADDRESS, "convert"));
                foreach ($globalbccs as $globalbcc) {
                    if(!in_array($globalbcc, $recips)) {
                        $mailer->AddBCC($globalbcc);
                        $recips[] = $globalbcc;
                    }
                }
            }
            $inline_attached = [];
            if(strpos($this->Message, "src=\"data:") !== false) {
                preg_match_all("/src=\"data\\:(.*)\"/", $this->Message, $matches);
                if(!empty($matches[1])) {
                    foreach ($matches[1] as $k => $match) {
                        $inline_attached[] = $match;
                        $match_extension = substr($match, strrpos($match, ".") + 1);
                        switch ($match_extension) {
                            case "jpeg":
                            case "jpg":
                                $match_mimetype = "image/jpeg";
                                break;
                            case "gif":
                                $match_mimetype = "image/gif";
                                break;
                            case "png":
                                $match_mimetype = "image/png";
                                break;
                            default:
                                $match_mimetype = "image/jpeg";
                                $mailer->AddEmbeddedImage($match, "cid_" . $k, "", "base64", $match_mimetype);
                                $this->Message = str_replace("data:" . $match, "cid:cid_" . $k, $this->Message);
                        }
                    }
                }
            }
            if(!empty($attachment_temp)) {
                foreach ($attachment_temp as $k => $v) {
                    if(file_exists($v) && !in_array($v, $inline_attached)) {
                        if(strpos(strtolower($v), ".pdf") !== false) {
                            $mailer->AddAttachment($v, $name[$k], "base64", "application/pdf");
                        } else {
                            $mailer->AddAttachment($v, $name[$k]);
                        }
                    }
                }
            }
            $mailer->Encoding = "quoted-printable";
            $mailer->Body = $this->Message;
            $mailer->AltBody = trim($this->strip_html_tags(html_entity_decode($this->Message)));
            $mailer->Subject = $this->Subject;
            if($this->AutoSubmitted !== false) {
                if($this->AutoSubmitted !== true && in_array($this->AutoSubmitted, ["no", "auto-generated", "auto-replied"])) {
                    $mailer->addCustomHeader("Auto-Submitted", $this->AutoSubmitted);
                } else {
                    $mailer->addCustomHeader("Auto-Submitted", "auto-generated");
                }
                $this->AutoSubmitted = false;
            }
            $current_dkim = json_decode(htmlspecialchars_decode(DKIM_DOMAINS), true);
            $dkim_domain = substr($mailer->From, strrpos($mailer->From, "@") + 1);
            if($current_dkim && isset($current_dkim[$dkim_domain])) {
                $dkim_filename = @tempnam("temp/", "dkim");
                if(@file_put_contents($dkim_filename, $current_dkim[$dkim_domain]["private"])) {
                    $mailer->DKIM_domain = $dkim_domain;
                    $mailer->DKIM_private = $dkim_filename;
                    $mailer->DKIM_selector = $current_dkim[$dkim_domain]["selector"];
                    $mailer->DKIM_passphrase = "";
                    $mailer->DKIM_identifier = $mailer->From;
                }
            }
            ob_start();
            $mailer->SMTPDebug = 1;
            $result = $mailer->Send();
            $smtp_debug = ob_get_contents();
            $mailer->SMTPDebug = 0;
            $mailer->getSMTPInstance()->setDebugLevel($mailer->SMTPDebug);
            ob_end_clean();
            if(!$result) {
                $this->EmailSpecificError = false;
                if(strpos($mailer->ErrorInfo, "https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting") !== false) {
                    $this->Error[] = "<br />" . nl2br($smtp_debug);
                } else {
                    switch ($mailer->ErrorInfo) {
                        case "SMTP Error: Could not connect to SMTP host.":
                        case "SMTP Error: Could not authenticate.":
                            $this->Error[] = __("invalid smtp settings, check your smtp settings");
                            break;
                        default:
                            $this->Error[] = $mailer->ErrorInfo;
                            if($recipients && $recipients[0] && 0 < strpos($mailer->ErrorInfo, $recipients[0])) {
                                $this->EmailSpecificError = true;
                            }
                    }
                }
                $this->MailerError = $mailer->ErrorInfo;
                if(0 < $this->Identifier) {
                    Database_Model::getInstance()->update("HostFact_Emails", ["Status" => "8"])->where("id", $this->Identifier)->execute();
                }
                return false;
            }
            if(is_array($this->Attachment)) {
                $this->Attachment = implode("|", $this->Attachment);
            }
            $this->Attachment = $pdf_invoice ? $this->Attachment . "|" . $pdf_invoice : $this->Attachment;
            $this->Attachment = str_replace("class/pdf.php?type=other&template=", "TemplateOther", $this->Attachment);
            if(0 < $this->Identifier) {
                Database_Model::getInstance()->update("HostFact_Emails", ["Status" => "1", "SentDate" => ["RAW" => "NOW()"]])->where("id", $this->Identifier)->execute();
            }
            if($logging) {
                $this->Success[] = sprintf(__("email sent succesfully"), $this->Subject, $this->Recipient);
            }
            return true;
        } else {
            if(0 < $this->Identifier) {
                Database_Model::getInstance()->update("HostFact_Emails", ["Status" => "8"])->where("id", $this->Identifier)->execute();
            }
            return false;
        }
    }
    public function validate()
    {
        if(!$this->Recipient) {
            return false;
        }
        return true;
    }
    public function all($fields, $sort = false, $order = false, $limit = "-1", $searchat = false, $searchfor = false, $group = false, $show_results = MAX_RESULTS_LIST)
    {
        $limit = !is_numeric($show_results) ? -1 : $limit;
        if(!is_array($fields) || empty($fields)) {
            $this->Error[] = __("no fields given for retrieving data");
            return false;
        }
        if(!is_numeric($show_results)) {
            $this->Error[] = __("invalid number for displaying results");
            return false;
        }
        $DebtorArray = ["DebtorCode", "CompanyName", "SurName", "Initials"];
        $DebtorFields = 0 < count(array_intersect($DebtorArray, $fields)) ? true : false;
        $search_at = [];
        $DebtorSearch = false;
        if($searchat && (0 < strlen($searchfor) || $searchfor)) {
            $search_at = explode("|", $searchat);
            $DebtorSearch = 0 < count(array_intersect($DebtorArray, $search_at)) ? true : false;
        }
        $select = ["HostFact_Emails.id"];
        foreach ($fields as $column) {
            if(in_array($column, $DebtorArray)) {
                $select[] = "HostFact_Debtors.`" . $column . "`";
            } else {
                $select[] = "HostFact_Emails.`" . $column . "`";
            }
        }
        Database_Model::getInstance()->get("HostFact_Emails", $select);
        if($DebtorFields || $DebtorSearch) {
            Database_Model::getInstance()->join("HostFact_Debtors", "HostFact_Debtors.`id` = HostFact_Emails.`Debtor`");
        }
        if(!empty($search_at)) {
            $or_clausule = [];
            foreach ($search_at as $searchColumn) {
                if(in_array($searchColumn, ["Debtor"])) {
                    $or_clausule[] = ["HostFact_Emails.`" . $searchColumn . "`", $searchfor];
                } elseif(in_array($searchColumn, $DebtorArray)) {
                    $or_clausule[] = ["HostFact_Debtors.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                } else {
                    $or_clausule[] = ["HostFact_Emails.`" . $searchColumn . "`", ["LIKE" => "%" . $searchfor . "%"]];
                }
            }
            Database_Model::getInstance()->orWhere($or_clausule);
        }
        $order = $sort ? $order ? $order : "ASC" : "";
        if(in_array($sort, $DebtorArray)) {
            Database_Model::getInstance()->orderBy("HostFact_Debtors.`" . $sort . "`", $order);
        } elseif($sort) {
            Database_Model::getInstance()->orderBy("HostFact_Emails." . $sort, $order);
        }
        if(0 <= $limit) {
            Database_Model::getInstance()->limit((max(1, $limit) - 1) * $show_results, $show_results);
        }
        if(is_numeric($group)) {
            Database_Model::getInstance()->where("HostFact_Emails.`Status`", $group);
        } else {
            Database_Model::getInstance()->where("HostFact_Emails.`Status`", ["<=" => 9]);
        }
        $list = [];
        $this->CountRows = 0;
        $list["CountRows"] = 0;
        if($email_list = Database_Model::getInstance()->execute()) {
            $list["CountRows"] = $this->CountRows = Database_Model::getInstance()->rowCount("HostFact_Emails", "HostFact_Emails.id");
            foreach ($email_list as $result) {
                $list[$result->id] = ["id" => $result->id];
                foreach ($fields as $column) {
                    if(in_array($column, $this->Variables)) {
                        $list[$result->id][$column] = htmlspecialchars($result->{$column});
                    }
                }
            }
        }
        return $list;
    }
    public function strip_html_tags($text)
    {
        $text = str_replace("\n", "", $text);
        $text = str_replace("\r", "", $text);
        $text = preg_replace("/\\s+/", " ", $text);
        $text = preg_replace(["@<head[^>]*?>.*?</head>@siu", "@<style[^>]*?>.*?</style>@siu", "@<script[^>]*?.*?</script>@siu", "@<object[^>]*?.*?</object>@siu", "@<embed[^>]*?.*?</embed>@siu", "@<applet[^>]*?.*?</applet>@siu", "@<noframes[^>]*?.*?</noframes>@siu", "@<noscript[^>]*?.*?</noscript>@siu", "@<noembed[^>]*?.*?</noembed>@siu", "@</?((address)|(blockquote)|(center)|(del))@iu", "@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu", "@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu", "@</?((table)|(th)|(td)|(caption))@iu", "@</?((form)|(button)|(fieldset)|(legend)|(input))@iu", "@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu", "@</?((frameset)|(frame)|(iframe))@iu"], [" ", " ", " ", " ", " ", " ", " ", " ", " ", "\n\$0", "\n\$0", "\n\$0", "\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0"], $text);
        $text = preg_replace("/\\s+/", " ", $text);
        $text = str_replace(["<br />", "<br/>", "<br>"], "\n", $text);
        return strip_tags($text);
    }
    public function addOpenInvoiceTable($debtor, $excludeInvoiceID = false, $addDraftRecord = false)
    {
        $online_payment_active = false;
        require_once "class/paymentmethod.php";
        $paymentmethod = Database_Model::getInstance()->get("HostFact_PaymentMethods", ["id"])->where("Directory", ["NOT IN" => ["", "payment.auth"]])->where("Availability", ["IN" => [paymentmethod::AVAILABILITY_CLIENTAREA, paymentmethod::AVAILABILITY_ALWAYS]])->execute();
        if(0 < count($paymentmethod)) {
            $online_payment_active = true;
        }
        $open_invoices = new invoice();
        $open_invoices_list = $open_invoices->all(["InvoiceCode", "Date", "PaymentURL", "AmountIncl", "AmountPaid", "Status", "Authorisation"], "Date` ASC, `InvoiceCode", "ASC", -1, "Debtor", $debtor->Identifier, "2|3");
        $td_css_border_valign_padding = "border-bottom: 1px solid #eaeaea; vertical-align:top; padding-top:0.5em;padding-bottom:0.5em;font-size:12px;font-family:arial,helvetica,sans-serif;";
        $number_of_records = 0;
        $html = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%; min-width:400px; max-width:600px; border-collapse:collapse;\"><tr><th align=\"left\" style=\"" . $td_css_border_valign_padding . "\">" . countryCodeTranslatedString($debtor->Country, "invoice") . "</th>" . "<th style=\"width:15px;" . $td_css_border_valign_padding . "\">&nbsp;</th>" . "<th align=\"left\" style=\"" . $td_css_border_valign_padding . "\">" . countryCodeTranslatedString($debtor->Country, "invoice date") . "</th>" . "<th align=\"right\" colspan=\"2\" style=\"width:80px;" . $td_css_border_valign_padding . "\">" . countryCodeTranslatedString($debtor->Country, "open sum short") . "</th>" . ($online_payment_active ? "<th style=\"" . $td_css_border_valign_padding . "\">&nbsp;</th>" : "") . "</tr>";
        foreach ($open_invoices_list as $k => $_invoice) {
            if(!is_numeric($k) || 0 < $excludeInvoiceID && $excludeInvoiceID == $_invoice["id"] || $_invoice["Authorisation"] == "yes") {
            } else {
                $days_due = 0 < $_invoice["DaysDue"] ? " &nbsp; <span style=\"color:red; font-size:11px; white-space: nowrap;\">" . ($_invoice["DaysDue"] == 1 ? countryCodeTranslatedString($debtor->Country, "debtor open invoice overview - 1 day expired") : sprintf(countryCodeTranslatedString($debtor->Country, "debtor open invoice overview - x days expired"), $_invoice["DaysDue"])) . "</span>" : "";
                $open_amount = $_invoice["Status"] == 2 ? $_invoice["AmountIncl"] : $_invoice["AmountIncl"] - $_invoice["AmountPaid"];
                $online_payment = "<span style=\"white-space: nowrap;\"> &nbsp;&nbsp; <a href=\"" . IDEAL_EMAIL . "?payment=" . urlencode(htmlspecialchars_decode($_invoice["InvoiceCode"])) . "&amp;key=" . urlencode($_invoice["PaymentURL"]) . "\">" . countryCodeTranslatedString($debtor->Country, "debtor open invoice overview - payment link") . "</a></span>";
                $number_of_records++;
                $html .= "<tr><td style=\"" . $td_css_border_valign_padding . "\">" . $_invoice["InvoiceCode"] . "</th>" . "<td style=\"" . $td_css_border_valign_padding . "\">&nbsp;</th>" . "<td style=\"" . $td_css_border_valign_padding . "\">" . rewrite_date_db2site($_invoice["Date"]) . $days_due . "</th>" . "<td style=\"width:15px;" . $td_css_border_valign_padding . "\">" . CURRENCY_SIGN_LEFT . "</th>" . "<td align=\"right\" style=\"" . $td_css_border_valign_padding . "\">" . money($open_amount, false) . "</th>" . ($online_payment_active ? "<td align=\"right\" style=\"" . $td_css_border_valign_padding . "\">" . (0 < $open_amount ? $online_payment : "") . "</td>" : "") . "</tr>";
            }
        }
        if($addDraftRecord === true) {
            $online_payment = "<span style=\"white-space: nowrap;\"> &nbsp;&nbsp; <a href=\"[invoice->PaymentURLRaw]\">" . countryCodeTranslatedString($debtor->Country, "debtor open invoice overview - payment link") . "</a></span>";
            $number_of_records++;
            $html .= "<tr><td style=\"" . $td_css_border_valign_padding . "\">[invoice->InvoiceCode]</th>" . "<td style=\"" . $td_css_border_valign_padding . "\">&nbsp;</th>" . "<td style=\"" . $td_css_border_valign_padding . "\">[invoice->Date]</th>" . "<td style=\"width:15px;" . $td_css_border_valign_padding . "\">" . CURRENCY_SIGN_LEFT . "</th>" . "<td align=\"right\" style=\"" . $td_css_border_valign_padding . "\">[invoice->AmountIncl_NoFormat]</th>" . ($online_payment_active ? "<td align=\"right\" style=\"" . $td_css_border_valign_padding . "\">" . (0 < $open_amount ? $online_payment : "") . "</th>" : "") . "</tr>";
        }
        $html .= "</table>";
        return 0 < $number_of_records ? $html : "";
    }
    public static function cronCleanup()
    {
        $date_retention = new Datetime("30 days ago");
        Database_Model::getInstance()->delete("HostFact_Emails")->where("SentDate", ["<" => $date_retention->format("Y-m-d")])->where("Status", ["IN" => [1, 8]])->execute();
    }
}

?>