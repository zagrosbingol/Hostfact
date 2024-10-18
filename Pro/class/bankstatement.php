<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class Transaction_Matches_Model
{
    public function getTransactionMatchesTable($reference_type, $reference_id)
    {
        $options = [];
        $options["cols"] = [["key" => "id", "title" => __("bank transaction id"), "sortable" => "id", "width" => 40], ["key" => "date", "title" => __("date"), "sortable" => "Date", "width" => 70], ["key" => "name", "title" => __("from bank account"), "sortable" => "Name", "class" => "show_col_ws", "td_class" => "show_col_ws"], ["key" => "description", "title" => __("bank transaction description"), "sortable" => "ShortDescription"], ["key" => "amount", "title" => __("bank transaction amount"), "sortable" => "Amount", "width" => 100, "colspan" => 3, "special_type" => "amount"], ["key" => "referencecode", "title" => __("invoice no")], ["key" => "matchedamount", "title" => __("bank transaction matched amount"), "sortable" => "MatchedAmount", "width" => 100, "colspan" => 3, "special_type" => "amount"]];
        $options["filter"] = "";
        $options["parameters"]["reference_type"] = $reference_type;
        $options["parameters"]["reference_id"] = $reference_id;
        $options["page_total_placeholder"] = "page_total_placeholder_transactions";
        $options["table_class"] = "bank_transaction_table";
        $options["data"] = ["class/bankstatement.php", "Transaction_Matches_Model", "get_data_table"];
        $options["form_action"] = "";
        $options_tmp = array_merge($options, $options["parameters"]);
        unset($options_tmp["parameters"]);
        $this->listTransactionMatches($options_tmp);
        return (int) $this->total_results === 0 ? false : $options;
    }
    public function get_data_table($offset, $results_per_page, $sort_by = "", $sort_order = "ASC", $parameters = [])
    {
        $options = !empty($parameters) ? $parameters : [];
        $options["offset"] = $offset;
        $options["results_per_page"] = $results_per_page;
        $options["sort_by"] = $sort_by;
        $options["sort_order"] = $sort_order;
        $matches_list = $this->listTransactionMatches($options);
        $data = ["TotalResults" => $this->total_results];
        foreach ($matches_list as $_match) {
            $td_payment_match_type = $_match->MatchType == "part_payment" ? "<br /><span class=\"c4 smallfont\">" . __("matched as partpayment") . "</span>" : ($_match->MatchType == "full_payment" ? "<br /><span class=\"c4 smallfont\">" . __("matched as fullpayment") . "</span>" : "");
            $td_multiple_matches = 1 < $_match->MatchCount ? "<span class=\"c4 smallfont\">" . sprintf(__("bank transaction matchcount"), $_match->MatchCount) . "</span>" : "";
            $td_match_Name = 0 < strlen($_match->Name) ? htmlspecialchars($_match->Name) . "<br />" : "";
            $td_match_AccountNumber = 0 < strlen($_match->AccountNumber) ? "<span class=\"c4\">" . htmlspecialchars($_match->AccountNumber) . "</span>" : "";
            $td_match_ShortDescription = 0 < strlen($_match->ShortDescription) ? htmlspecialchars($_match->ShortDescription) . "<br />" : "";
            $td_match_ExtendedDescription = 0 < strlen($_match->ExtendedDescription) ? "<span class=\"c4\">" . htmlspecialchars($_match->ExtendedDescription) . "</span>" : "";
            $data[] = ["<a href=\"invoices.php?page=bankstatement&amp;action=show&amp;id=" . htmlspecialchars($_match->TransactionID) . "\" class=\"a1 c1\">#" . htmlspecialchars($_match->TransactionID) . "</a>", rewrite_date_db2site($_match->Date), $td_match_Name . $td_match_AccountNumber, $td_match_ShortDescription . $td_match_ExtendedDescription, [currency_sign_td(CURRENCY_SIGN_LEFT), money($_match->Amount, false) . "<br />" . $td_multiple_matches, currency_sign_td(CURRENCY_SIGN_RIGHT)], isset($_match->ReferenceCode) ? htmlspecialchars($_match->ReferenceCode) : "", [currency_sign_td(CURRENCY_SIGN_LEFT), money($_match->MatchedAmount, false) . $td_payment_match_type, currency_sign_td(CURRENCY_SIGN_RIGHT)]];
        }
        return $data;
    }
    public function listTransactionMatches($options = [])
    {
        $fields = isset($options["fields"]) && is_array($options["fields"]) ? $options["fields"] : [];
        $sort_by = isset($options["sort_by"]) && $options["sort_by"] ? $options["sort_by"] : "TransactionID";
        $sort_order = isset($options["sort_order"]) && $options["sort_order"] ? $options["sort_order"] : "DESC";
        $offset = isset($options["offset"]) && $options["offset"] ? $options["offset"] : "0";
        $results_per_page = isset($options["results_per_page"]) && $options["results_per_page"] ? $options["results_per_page"] : MAX_RESULTS_LIST;
        $filter = isset($options["filter"]) && $options["filter"] ? $options["filter"] : "";
        $group_by = isset($options["group_by"]) && $options["group_by"] ? $options["group_by"] : "";
        if(!is_array($fields) || empty($fields)) {
            $fields = ["id", "TransactionID", "MatchedAmount", "MatchType", "MatchCount", "Date", "Amount", "ShortDescription", "ExtendedDescription", "Name", "AccountNumber"];
        }
        $select = [];
        if(!in_array("id", $fields)) {
            $select[] = "HostFact_Transaction_Matches.`id`";
        }
        $TransactionArray = ["Date", "Amount", "ShortDescription", "ExtendedDescription", "Name", "AccountNumber", "MatchCount"];
        foreach ($fields as $column) {
            if(in_array($column, $TransactionArray)) {
                $select[] = "HostFact_Transactions.`" . $column . "`";
            } else {
                $select[] = "HostFact_Transaction_Matches.`" . $column . "`";
            }
        }
        if(isset($options["reference_type"]) && isset($options["reference_id"])) {
            if($options["reference_type"] == "debtor") {
                $select[] = "HostFact_Invoice.`InvoiceCode` as `ReferenceCode`";
            } elseif($options["reference_type"] == "creditor") {
                $select[] = "HostFact_CreditInvoice.`CreditInvoiceCode` as `ReferenceCode`";
            }
        }
        Database_Model::getInstance()->get("HostFact_Transaction_Matches", $select);
        if(0 < count(array_intersect($TransactionArray, $fields))) {
            Database_Model::getInstance()->join("HostFact_Transactions", "HostFact_Transaction_Matches.`TransactionID` = HostFact_Transactions.`id`");
        }
        if(isset($options["reference_type"]) && isset($options["reference_id"])) {
            if($options["reference_type"] == "debtor") {
                Database_Model::getInstance()->where("HostFact_Transaction_Matches.ReferenceType", "invoice")->where("HostFact_Transaction_Matches.RelationID", $options["reference_id"]);
                Database_Model::getInstance()->join("HostFact_Invoice", "HostFact_Transaction_Matches.`ReferenceID`= HostFact_Invoice.`id`");
            } elseif($options["reference_type"] == "creditor") {
                Database_Model::getInstance()->where("HostFact_Transaction_Matches.ReferenceType", "creditinvoice")->where("HostFact_Transaction_Matches.RelationID", $options["reference_id"]);
                Database_Model::getInstance()->join("HostFact_CreditInvoice", "HostFact_Transaction_Matches.`ReferenceID`= HostFact_CreditInvoice.`id`");
            } else {
                Database_Model::getInstance()->where("HostFact_Transaction_Matches.ReferenceType", $options["reference_type"])->where("HostFact_Transaction_Matches.ReferenceID", $options["reference_id"]);
            }
        } elseif(isset($options["transaction_id"]) && isset($options["transaction_id"])) {
            Database_Model::getInstance()->where("HostFact_Transaction_Matches.TransactionID", $options["transaction_id"]);
        }
        Database_Model::getInstance()->where("HostFact_Transactions.Status", ["IN" => ["matched"]]);
        if($sort_by) {
            if(in_array($sort_by, $TransactionArray)) {
                Database_Model::getInstance()->orderBy("HostFact_Transactions.`" . $sort_by . "`", $sort_order);
            } else {
                Database_Model::getInstance()->orderBy("HostFact_Transaction_Matches.`" . $sort_by . "`", $sort_order);
            }
        }
        if(0 <= $offset && $results_per_page != "all") {
            Database_Model::getInstance()->limit($offset, $results_per_page);
        }
        $this->total_results = 0;
        if($list = Database_Model::getInstance()->execute()) {
            $this->total_results = Database_Model::getInstance()->rowCount("HostFact_Transaction_Matches", "HostFact_Transaction_Matches.id");
        }
        return $list;
    }
}
class Transaction_Model
{
    private $candidates;
    private $match_list;
    public $Error;
    public $Warning;
    public $Success;
    public $StatusArray;
    public function __construct()
    {
        $this->Error = $this->Warning = $this->Success = [];
        $this->StatusArray = ["unmatched" => __("banktransaction status todo"), "matched" => __("banktransaction status matched"), "nomatch" => __("banktransaction status nomatch")];
    }
    public function show($transaction_id)
    {
        $result = Database_Model::getInstance()->getOne("HostFact_Transactions")->where("id", intval($transaction_id))->execute();
        if(!$result) {
            $this->Error[] = __("could not find the bank transaction");
            return false;
        }
        foreach ($result as $key => $value) {
            $this->{$key} = $value;
        }
        $this->unmatchedAmount = $this->Amount;
        if($this->Status == "matched") {
            $this->match_list = [];
            $result = Database_Model::getInstance()->get("HostFact_Transaction_Matches", ["HostFact_Transaction_Matches.*", "HostFact_Invoice.`InvoiceCode` as `ReferenceCode`", "'' as `ExternalInvoiceCode`", "HostFact_Debtors.`DebtorCode` as `RelationCode`", "HostFact_Debtors.CompanyName", "HostFact_Debtors.SurName", "HostFact_Debtors.Initials"])->join("HostFact_Invoice", "HostFact_Transaction_Matches.`ReferenceType`='invoice' AND HostFact_Transaction_Matches.`ReferenceID`= HostFact_Invoice.`id`")->join("HostFact_Debtors", "HostFact_Invoice.`Debtor` = HostFact_Debtors.`id`")->where("HostFact_Transaction_Matches.TransactionID", $transaction_id)->where("HostFact_Transaction_Matches.ReferenceType", "invoice")->getUnion("HostFact_Transaction_Matches", ["HostFact_Transaction_Matches.*", "HostFact_CreditInvoice.`CreditInvoiceCode` as `ReferenceCode`", "HostFact_CreditInvoice.InvoiceCode as `ExternalInvoiceCode`", "HostFact_Creditors.`CreditorCode` as `RelationCode`", "HostFact_Creditors.CompanyName", "HostFact_Creditors.SurName", "HostFact_Creditors.Initials"])->join("HostFact_CreditInvoice", "HostFact_Transaction_Matches.`ReferenceType`='creditinvoice' AND HostFact_Transaction_Matches.`ReferenceID`= HostFact_CreditInvoice.`id`")->join("HostFact_Creditors", "HostFact_CreditInvoice.`Creditor` = HostFact_Creditors.`id`")->where("HostFact_Transaction_Matches.TransactionID", $transaction_id)->where("HostFact_Transaction_Matches.ReferenceType", "creditinvoice")->closeUnion()->execute();
            if($result && !empty($result)) {
                foreach ($result as $_match) {
                    $this->unmatchedAmount -= $_match->MatchedAmount;
                }
                $this->match_list = $result;
            }
        }
        return true;
    }
    public function deleteTransactions($id_array)
    {
        if(empty($id_array)) {
            return true;
        }
        $result = Database_Model::getInstance()->delete("HostFact_Transactions")->where("id", ["IN" => $id_array])->execute();
        if($result) {
            $this->Success[] = sprintf(__("banktransactions are deleted"), count($id_array));
            return true;
        }
        return false;
    }
    public function getTransactionTable($bankaccount = "")
    {
        $options = [];
        $options["cols"] = [["key" => "id", "title" => __("bank transaction id"), "sortable" => "id", "width" => 70], ["key" => "date", "title" => __("date"), "sortable" => "Date", "width" => 70], ["key" => "name", "title" => __("from bank account"), "sortable" => "Name", "class" => "nowrap"], ["key" => "description", "title" => __("bank transaction description"), "sortable" => "ShortDescription"], ["key" => "amount", "title" => __("bank transaction amount"), "sortable" => "Amount", "width" => 100, "colspan" => 3, "special_type" => "amount"], ["key" => "status", "title" => __("status"), "width" => 110]];
        $options["data"] = ["class/bankstatement.php", "Transaction_Model", "get_data_transactions_table"];
        $options["table_class"] = "bank_transaction_table";
        if(U_INVOICE_EDIT) {
            $options["actions"] = [["action" => "delete_transaction", "title" => __("delete"), "dialog" => ["content" => __("banktransaction delete transactions dialog")]]];
        }
        $options["form_action"] = "invoices.php?page=bankstatement&bankaccount=" . htmlspecialchars($bankaccount);
        $options["results_per_page"] = 25;
        $options["sort_by"] = "id";
        $options["sort_order"] = "DESC";
        return $options;
    }
    public function get_data_transactions_table($offset, $results_per_page, $sort_by = "", $sort_order = "ASC", $parameters = [])
    {
        $options = !empty($parameters) ? $parameters : [];
        $options["offset"] = $offset;
        $options["results_per_page"] = $results_per_page;
        $options["sort_by"] = $sort_by;
        $options["sort_order"] = $sort_order;
        $transaction_list = $this->listTransactions($options);
        $data = ["TotalResults" => $this->total_results];
        foreach ($transaction_list as $_transaction) {
            $td_multiple_matches = 1 < $_transaction->MatchCount ? "<br /><span class=\"c4 smallfont\">" . sprintf(__("bank transaction matchcount"), $_transaction->MatchCount) . "</span>" : ($_transaction->MatchCount == 1 ? "<br /><span class=\"c4 smallfont\">" . __("bank transaction matchcount single") . "</span>" : "");
            $td_match_Name = 0 < strlen($_transaction->Name) ? htmlspecialchars($_transaction->Name) . "<br />" : "";
            $td_match_AccountNumber = 0 < strlen($_transaction->AccountNumber) ? "<span class=\"c4\">" . htmlspecialchars($_transaction->AccountNumber) . "</span>" : "";
            $td_match_ShortDescription = 0 < strlen($_transaction->ShortDescription) ? htmlspecialchars($_transaction->ShortDescription) . "<br />" : "";
            $td_match_ExtendedDescription = 0 < strlen($_transaction->ExtendedDescription) ? "<span class=\"c4\">" . htmlspecialchars($_transaction->ExtendedDescription) . "</span>" : "";
            $data[] = ["id" => $_transaction->id, "<a href=\"invoices.php?page=bankstatement&amp;action=show&amp;id=" . htmlspecialchars($_transaction->id) . "\" class=\"a1 c1\">#" . htmlspecialchars($_transaction->id) . "</a>", rewrite_date_db2site($_transaction->Date), $td_match_Name . $td_match_AccountNumber, $td_match_ShortDescription . $td_match_ExtendedDescription, [currency_sign_td(CURRENCY_SIGN_LEFT), money($_transaction->Amount, false), currency_sign_td(CURRENCY_SIGN_RIGHT)], htmlspecialchars($this->StatusArray[$_transaction->Status]) . $td_multiple_matches];
        }
        return $data;
    }
    public function listTransactions($options = [])
    {
        $fields = isset($options["fields"]) && is_array($options["fields"]) ? $options["fields"] : [];
        $sort_by = isset($options["sort_by"]) && $options["sort_by"] ? $options["sort_by"] : "id";
        $sort_order = isset($options["sort_order"]) && $options["sort_order"] ? $options["sort_order"] : "DESC";
        $offset = isset($options["offset"]) && $options["offset"] ? $options["offset"] : "0";
        $results_per_page = isset($options["results_per_page"]) && $options["results_per_page"] ? $options["results_per_page"] : MAX_RESULTS_LIST;
        $filter = isset($options["filter"]) && $options["filter"] ? $options["filter"] : "";
        $group_by = isset($options["group_by"]) && $options["group_by"] ? $options["group_by"] : "";
        if(!is_array($fields) || empty($fields)) {
            $fields = ["id", "MatchCount", "Date", "Amount", "ShortDescription", "ExtendedDescription", "Name", "AccountNumber", "Status"];
        }
        $select = [];
        if(!in_array("id", $fields)) {
            $select[] = "id";
        }
        foreach ($fields as $column) {
            $select[] = "HostFact_Transactions.`" . $column . "`";
        }
        Database_Model::getInstance()->get("HostFact_Transactions", $select);
        if(isset($options["bankaccount"])) {
            Database_Model::getInstance()->where("BankAccount", $options["bankaccount"]);
        }
        if($filter && array_key_exists($filter, $this->StatusArray)) {
            Database_Model::getInstance()->where("Status", $filter);
        }
        if($sort_by) {
            Database_Model::getInstance()->orderBy($sort_by, $sort_order);
        }
        if(0 <= $offset && $results_per_page != "all") {
            Database_Model::getInstance()->limit($offset, $results_per_page);
        }
        $this->total_results = 0;
        if($list = Database_Model::getInstance()->execute()) {
            $this->total_results = Database_Model::getInstance()->rowCount("HostFact_Transactions", "HostFact_Transactions.id");
        }
        return $list;
    }
    public function listUnprocessedTransactions()
    {
        $transactions = Database_Model::getInstance()->get("HostFact_Transactions")->where("Status", "unmatched")->execute();
        return $transactions;
    }
    public function processMatch($transaction_id, $post_data, $directdebit_answers = "")
    {
        $transaction = Database_Model::getInstance()->getOne("HostFact_Transactions")->where("id", $transaction_id)->where("Status", "unmatched")->execute();
        if(!$transaction) {
            $this->Error[] = __("could not find the bank transaction");
            return false;
        }
        Database_Model::getInstance()->beginTransaction();
        $invoice_ids_to_checkauto = [];
        $match_count = 0;
        foreach ($post_data as $_match) {
            $match_count++;
            if($_match["reference_type"] == "invoice") {
                require_once "class/invoice.php";
                $object_info = new invoice();
                $object_info->Identifier = esc($_match["reference_id"]);
                $object_info->show();
                $relation_id = $object_info->Debtor;
            } else {
                require_once "class/creditinvoice.php";
                $object_info = new creditinvoice();
                $object_info->Identifier = esc($_match["reference_id"]);
                $object_info->show();
                $relation_id = $object_info->Creditor;
            }
            Database_Model::getInstance()->insert("HostFact_Transaction_Matches", ["TransactionID" => $transaction_id, "ReferenceType" => esc($_match["reference_type"]), "ReferenceID" => esc($_match["reference_id"]), "RelationID" => $relation_id, "MatchedAmount" => esc($_match["amount_matched"]), "MatchedBy" => isset($_SESSION["UserPro"]) ? $_SESSION["UserPro"] : 0, "MatchType" => isset($_match["partpayment_choice"]) && $_match["partpayment_choice"] ? esc($_match["partpayment_choice"]) : ""])->execute();
            if($_match["reference_type"] == "invoice") {
                if(in_array($object_info->Status, [2, 3]) && $transaction->Type != "reversal") {
                    if(isEmptyFloat($object_info->AmountPaid) && round((double) $object_info->AmountIncl, 2) <= round((double) esc($_match["amount_matched"]), 2) || isset($_match["partpayment_choice"]) && $_match["partpayment_choice"] == "full_payment") {
                        $object_info->markaspaid($transaction->Date);
                    } elseif(!isEmptyFloat($object_info->AmountPaid) && round($object_info->AmountIncl - $object_info->AmountPaid, 2) <= round((double) esc($_match["amount_matched"]), 2)) {
                        $object_info->partpayment(round($object_info->AmountIncl - $object_info->AmountPaid, 2), $transaction->Date);
                    } elseif(round((double) esc($_match["amount_matched"]), 2) < round($object_info->AmountIncl - $object_info->AmountPaid, 2)) {
                        $object_info->partpayment(round((double) esc($_match["amount_matched"]), 2));
                    }
                    if($object_info->Status == 4 || isset($_match["partpayment_choice"]) && $_match["partpayment_choice"] == "full_payment") {
                        $invoice_ids_to_checkauto[] = $object_info->Identifier;
                    }
                } elseif($object_info->Status == 4) {
                    if($object_info->Authorisation == "yes" && SDD_ID && $object_info->SDDBatchID && $directdebit_answers) {
                        parse_str($directdebit_answers, $directdebit_answers);
                        require_once "class/directdebit.php";
                        $directdebit = new directdebit();
                        $failedAction = $directdebit_answers["InvoiceAction"];
                        $reason = esc($directdebit_answers["Reason"]);
                        $notifyDebtorAboutNewDirectDebitDate = isset($directdebit_answers["NotifyDebtor"]) && $directdebit_answers["NotifyDebtor"] == "yes" ? true : false;
                        $notifyDebtorWithMailID = $notifyDebtorAboutNewDirectDebitDate === true ? esc($directdebit_answers["NotifyMail"]) : 0;
                        $directdebit->failedDirectDebitByInvoiceID($object_info->Identifier, $reason, $failedAction, $notifyDebtorAboutNewDirectDebitDate, $notifyDebtorWithMailID);
                        $this->Error = array_merge($this->Error, $directdebit->Error);
                        $this->Warning = array_merge($this->Warning, $directdebit->Warning);
                        $this->Success = array_merge($this->Success, $directdebit->Success);
                    } elseif(0 < $object_info->AmountIncl && $_match["amount_matched"] < 0 && isset($_match["partpayment_choice"]) && $_match["partpayment_choice"] != "full_payment") {
                        $object_info->AmountPaid = $object_info->AmountIncl;
                        $object_info->partpayment(round((double) esc($_match["amount_matched"]), 2));
                    }
                }
                $this->Error = array_merge($this->Error, $object_info->Error);
                $this->Warning = array_merge($this->Warning, $object_info->Warning);
            } else {
                if(in_array($object_info->Status, [0, 1, 2])) {
                    if(isEmptyFloat($object_info->AmountPaid) && round((double) esc($_match["amount_matched"]), 2) <= round(-1 * $object_info->AmountIncl, 2) || isset($_match["partpayment_choice"]) && $_match["partpayment_choice"] == "full_payment") {
                        $object_info->markaspaid($transaction->Date);
                    } elseif(!isEmptyFloat($object_info->AmountPaid) && round((double) esc($_match["amount_matched"]), 2) <= round(-1 * ($object_info->AmountIncl - $object_info->AmountPaid), 2)) {
                        $object_info->partpayment(round($object_info->AmountIncl - $object_info->AmountPaid, 2), $transaction->Date);
                    } elseif(round(-1 * ($object_info->AmountIncl - $object_info->AmountPaid), 2) < round((double) esc($_match["amount_matched"]), 2)) {
                        $object_info->partpayment(round(-1 * esc($_match["amount_matched"]), 2));
                    }
                } elseif($object_info->Status == 3) {
                    if(0 < $object_info->AmountIncl && $transaction->Type == "reversal") {
                        $object_info->AmountPaid = $object_info->AmountIncl;
                        $object_info->partpayment(-1 * round(esc($_match["amount_matched"]), 2));
                    } elseif($object_info->AmountIncl < 0 && 0 < $_match["amount_matched"] && isset($_match["partpayment_choice"]) && $_match["partpayment_choice"] != "full_payment") {
                        $object_info->AmountPaid = $object_info->AmountIncl;
                        $object_info->partpayment(-1 * round(esc($_match["amount_matched"]), 2));
                    }
                }
                $this->Error = array_merge($this->Error, $object_info->Error);
                $this->Warning = array_merge($this->Warning, $object_info->Warning);
            }
        }
        Database_Model::getInstance()->update("HostFact_Transactions", ["Status" => "matched", "MatchCount" => $match_count])->where("id", $transaction_id)->where("Status", "unmatched")->execute();
        if(!empty($this->Error)) {
            Database_Model::getInstance()->rollBack();
            return false;
        }
        Database_Model::getInstance()->commit();
        foreach ($invoice_ids_to_checkauto as $invoice_id) {
            $invoice = new invoice();
            $invoice->Identifier = $invoice_id;
            $invoice->show();
            $invoice->checkAuto();
        }
        return true;
    }
    public function processNoMatch($transaction_id)
    {
        Database_Model::getInstance()->update("HostFact_Transactions", ["Status" => "nomatch"])->where("id", $transaction_id)->where("Status", "unmatched")->execute();
    }
    public function undoMatching($transaction_id)
    {
        Database_Model::getInstance()->update("HostFact_Transactions", ["Status" => "unmatched", "MatchCount" => 0])->where("id", $transaction_id)->execute();
        Database_Model::getInstance()->delete("HostFact_Transaction_Matches")->where("TransactionID", $transaction_id)->execute();
    }
    public function getCandidates()
    {
        return $this->candidates;
    }
    public function getMatches()
    {
        return isset($this->match_list) && is_array($this->match_list) ? $this->match_list : [];
    }
    public function findMatch($record_info, $batch_or_invoices = "batch")
    {
        $this->candidates = [];
        $this->matchedAmount = 0;
        $this->unmatchedAmount = round((double) $record_info->Amount, 2);
        $record_info->AccountNumber = strtoupper(preg_replace("/[^0-9A-Z]/i", "", $record_info->AccountNumber));
        switch ($record_info->Type) {
            case "batch":
                $sdd_batch_id = explode("-", trim($record_info->ShortDescription), 2);
                $results = Database_Model::getInstance()->get(["HostFact_SDD_BatchElements", "HostFact_Invoice", "HostFact_Debtors"], ["HostFact_SDD_BatchElements.InvoiceID", "HostFact_SDD_BatchElements.Amount", "HostFact_SDD_BatchElements.Debtor", "HostFact_Invoice.InvoiceCode", "HostFact_Invoice.CompanyName", "HostFact_Invoice.Initials", "HostFact_Invoice.SurName", "HostFact_Invoice.Status", "HostFact_Debtors.DebtorCode"])->where("HostFact_SDD_BatchElements.BatchID", trim($sdd_batch_id[0]))->where("HostFact_SDD_BatchElements.MandateType", trim($sdd_batch_id[1]))->where("HostFact_SDD_BatchElements.`InvoiceID` = HostFact_Invoice.`id`")->where("HostFact_Debtors.`id` = HostFact_SDD_BatchElements.`Debtor`")->orderBy("IF(SUBSTRING(HostFact_Invoice.`InvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_Invoice.`InvoiceCode`,1,1))", "DESC")->orderBy("LENGTH(HostFact_Invoice.`InvoiceCode`)", "DESC")->orderBy("HostFact_Invoice.`InvoiceCode`", "DESC")->execute();
                if($results) {
                    foreach ($results as $tmp_invoice) {
                        $tmp_invoice->id = $tmp_invoice->InvoiceID;
                        $this->_addCandidateToArray($tmp_invoice, "invoice", round((double) $tmp_invoice->Amount, 2), "match");
                    }
                }
                if(isEmptyFloat(round((double) $this->unmatchedAmount, 2))) {
                    if($batch_or_invoices == "batch") {
                        require_once "class/directdebit.php";
                        $directdebit = new directdebit();
                        $batch_info = $directdebit->getBatchInfo($sdd_batch_id[0]);
                        if($batch_info) {
                            $batch_invoices_amount = count($this->candidates);
                            $this->_removeMatchesFromCandidateArray();
                            $this->matchedAmount = $this->unmatchedAmount;
                            $this->candidates[] = (object) ["ReferenceID" => $batch_info->BatchID, "Type" => "batch", "ReferencePrefix" => $batch_invoices_amount . " " . strtolower(__("invoices")), "AmountPayable" => $this->matchedAmount, "AmountMatched" => $this->matchedAmount, "ReferenceCode" => $batch_info->BatchID, "CandidateType" => "match"];
                            $this->unmatchedAmount = 0;
                        }
                    }
                    return true;
                }
                $this->_removeMatchesFromCandidateArray();
                return false;
                break;
            case "reversal":
                $regex_string = ".*";
                for ($regex_i = 0; $regex_i < strlen($record_info->AccountNumber); $regex_i++) {
                    $regex_string .= $record_info->AccountNumber[$regex_i] . ".*";
                }
                $results = Database_Model::getInstance()->get(["HostFact_SDD_BatchElements", "HostFact_Invoice"], ["HostFact_SDD_BatchElements.InvoiceID", "HostFact_SDD_BatchElements.Amount", "HostFact_Invoice.InvoiceCode", "HostFact_Invoice.AmountIncl", "HostFact_Invoice.AmountPaid", "HostFact_Invoice.CompanyName", "HostFact_Invoice.Initials", "HostFact_Invoice.SurName", "HostFact_Invoice.Status", "HostFact_Invoice.Debtor", "HostFact_Invoice.Reminders", "HostFact_Invoice.Summations", "HostFact_Debtors.DebtorCode", "HostFact_Debtors.AccountNumber"])->join("HostFact_Debtors", "HostFact_Debtors.`id` = HostFact_Invoice.`Debtor`")->where("HostFact_SDD_BatchElements.IBAN", ["REGEXP" => "^" . $regex_string . "\$"])->where("HostFact_SDD_BatchElements.`InvoiceID` = HostFact_Invoice.`id`")->execute();
                if($results && is_array($results)) {
                    foreach ($results as $tmp_invoice) {
                        if(stripos($record_info->ShortDescription, $tmp_invoice->InvoiceCode) !== false) {
                            $tmp_invoice->Amount = round(-1 * $tmp_invoice->Amount, 2);
                            $matched_amount = $tmp_invoice->Amount;
                            $tmp_invoice->id = $tmp_invoice->InvoiceID;
                            $this->_addCandidateToArray($tmp_invoice, "invoice", $matched_amount, "match");
                        }
                    }
                }
                if(isEmptyFloat($this->unmatchedAmount)) {
                    return true;
                }
                $this->_removeMatchesFromCandidateArray();
                $results = Database_Model::getInstance()->get("HostFact_CreditInvoice", ["HostFact_CreditInvoice.id", "HostFact_CreditInvoice.CreditInvoiceCode", "HostFact_CreditInvoice.InvoiceCode", "HostFact_CreditInvoice.AmountIncl", "HostFact_CreditInvoice.AmountExcl", "(HostFact_CreditInvoice.`AmountIncl` - HostFact_CreditInvoice.`AmountPaid`) as `Amount`", "HostFact_CreditInvoice.Creditor", "HostFact_CreditInvoice.Status", "HostFact_Creditors.CompanyName", "HostFact_Creditors.Initials", "HostFact_Creditors.SurName", "HostFact_Creditors.CreditorCode"])->join("HostFact_Creditors", "HostFact_Creditors.`id` = HostFact_CreditInvoice.`Creditor`")->where("HostFact_CreditInvoice.Status", ["IN" => [0, 1, 2, 3]])->execute();
                if($results && is_array($results)) {
                    foreach ($results as $tmp_invoice) {
                        if(stripos($record_info->ShortDescription, $tmp_invoice->InvoiceCode) !== false) {
                            $candidate_or_match = round((double) $record_info->Amount, 2) == round($tmp_invoice->Amount, 2) ? "match" : "candidate";
                            $tmp_invoice->Amount = round(-1 * $tmp_invoice->Amount, 2);
                            $matched_amount = $tmp_invoice->Status == "3" ? $record_info->Amount : -1 * $record_info->Amount;
                            $this->_addCandidateToArray($tmp_invoice, "creditinvoice", $matched_amount, $candidate_or_match);
                        }
                    }
                }
                if(isEmptyFloat($this->unmatchedAmount)) {
                    return true;
                }
                $this->_removeMatchesFromCandidateArray();
                return false;
                break;
            default:
                $matched_some_open_invoices = false;
                $correct_matches_if_more_suggestions = false;
                $list_open_invoices = [];
                $results = Database_Model::getInstance()->get("HostFact_Invoice", ["HostFact_Invoice.id", "HostFact_Invoice.InvoiceCode", "HostFact_Invoice.AmountIncl", "HostFact_Invoice.AmountExcl", "(HostFact_Invoice.`AmountIncl` - HostFact_Invoice.`AmountPaid`) as `Amount`", "HostFact_Invoice.CompanyName", "HostFact_Invoice.Initials", "HostFact_Invoice.SurName", "HostFact_Invoice.Debtor", "HostFact_Invoice.Reminders", "HostFact_Invoice.Summations", "HostFact_Invoice.Date", "HostFact_Invoice.Status", "HostFact_Debtors.DebtorCode", "HostFact_Debtors.AccountNumber"])->join("HostFact_Debtors", "HostFact_Debtors.`id` = HostFact_Invoice.`Debtor`")->where("HostFact_Invoice.Status", ["IN" => [2, 3]])->execute();
                foreach ($results as $tmp_invoice) {
                    if(stripos($record_info->ShortDescription, $tmp_invoice->InvoiceCode) !== false || stripos($record_info->ShortDescription, str_replace(parsePrefixVariables(INVOICECODE_PREFIX), "", $tmp_invoice->InvoiceCode)) !== false) {
                        $matched_amount = 0 < $tmp_invoice->Amount ? min(round((double) $tmp_invoice->Amount, 2), round((double) $this->unmatchedAmount, 2)) : max(round((double) $tmp_invoice->Amount, 2), round((double) $this->unmatchedAmount, 2));
                        $match_or_candidate = stripos($record_info->ShortDescription, $tmp_invoice->InvoiceCode) !== false && $matched_amount == round((double) $tmp_invoice->Amount, 2) ? "match" : "candidate";
                        $this->_addCandidateToArray($tmp_invoice, "invoice", $matched_amount, $match_or_candidate);
                        $_one_candidate_invoice_date = $tmp_invoice->Date;
                    } else {
                        $list_open_invoices[] = $tmp_invoice;
                    }
                }
                if(isEmptyFloat(round((double) $this->unmatchedAmount, 2))) {
                    if(count($this->candidates) == 1) {
                        $_candidate = current($this->candidates);
                        if($_candidate->PaymentType == "part") {
                            $results = Database_Model::getInstance()->get("HostFact_Invoice", ["HostFact_Invoice.id", "HostFact_Invoice.InvoiceCode", "HostFact_Invoice.AmountIncl", "HostFact_Invoice.AmountExcl", "(HostFact_Invoice.`AmountIncl` - HostFact_Invoice.`AmountPaid`) as `Amount`", "HostFact_Invoice.CompanyName", "HostFact_Invoice.Initials", "HostFact_Invoice.SurName", "HostFact_Invoice.Debtor", "HostFact_Invoice.Reminders", "HostFact_Invoice.Summations", "HostFact_Invoice.Status", "HostFact_Debtors.DebtorCode", "HostFact_Debtors.AccountNumber"])->join("HostFact_Debtors", "HostFact_Debtors.`id` = HostFact_Invoice.`Debtor`")->where("HostFact_Invoice.Debtor", $_candidate->RelationID)->where("HostFact_Invoice.Date", [">=" => $_one_candidate_invoice_date])->where("HostFact_Invoice.Status", 8)->execute();
                            foreach ($results as $tmp_invoice) {
                                $this->_addCandidateToArray($tmp_invoice, "invoice", 0, "candidate");
                            }
                        }
                    } elseif(1 < count($this->candidates)) {
                        $tmp_amount_to_match = round((double) $record_info->Amount, 2);
                        foreach ($this->candidates as $key => $_candidate) {
                            $tmp_amount_to_match -= $_candidate->AmountPayable;
                        }
                        if(isEmptyFloat($tmp_amount_to_match)) {
                            foreach ($this->candidates as $key => $_candidate) {
                                $this->candidates[$key]->AmountMatched = $this->candidates[$key]->AmountPayable;
                                $this->candidates[$key]->PaymentType = "full";
                            }
                        }
                        foreach ($this->candidates as $key => $_candidate) {
                            if($_candidate->CandidateType == "candidate") {
                                unset($this->candidates[$key]);
                            }
                        }
                    }
                    return true;
                } else {
                    if(!isEmptyFloat($this->matchedAmount)) {
                        $matched_some_open_invoices = true;
                    }
                    $list_non_open_invoices = [];
                    $results = Database_Model::getInstance()->get("HostFact_Invoice", ["HostFact_Invoice.id", "HostFact_Invoice.InvoiceCode", "HostFact_Invoice.AmountIncl", "HostFact_Invoice.AmountExcl", "HostFact_Invoice.CompanyName", "HostFact_Invoice.Initials", "HostFact_Invoice.SurName", "HostFact_Invoice.Debtor", "HostFact_Invoice.Reminders", "HostFact_Invoice.Summations", "HostFact_Invoice.Date", "HostFact_Invoice.Status", "HostFact_Debtors.DebtorCode", "HostFact_Debtors.AccountNumber"])->join("HostFact_Debtors", "HostFact_Debtors.`id` = HostFact_Invoice.`Debtor`")->where("HostFact_Invoice.Status", [">=" => 4])->orderBy("HostFact_Invoice.Date", "DESC")->execute();
                    $optimize_this_check = false;
                    $_one_candidate_invoice_date = [];
                    foreach ($results as $tmp_invoice) {
                        if(stripos($record_info->ShortDescription, $tmp_invoice->InvoiceCode) !== false || stripos($record_info->ShortDescription, str_replace(parsePrefixVariables(INVOICECODE_PREFIX), "", $tmp_invoice->InvoiceCode)) !== false) {
                            $_amount_is_matching = round((double) $tmp_invoice->AmountIncl, 2) == round((double) $record_info->Amount, 2) || round((double) $tmp_invoice->AmountExcl, 2) == round((double) $record_info->Amount, 2) ? true : false;
                            $_debtorcode_is_matching = stripos($record_info->ShortDescription, $tmp_invoice->DebtorCode) !== false ? true : false;
                            $_account_number_is_matching = $tmp_invoice->AccountNumber && strtoupper(preg_replace("/[^0-9A-Z]/i", "", $tmp_invoice->AccountNumber)) == $record_info->AccountNumber ? true : false;
                            $_invoicecode_is_match = stripos($record_info->ShortDescription, $tmp_invoice->InvoiceCode) !== false ? true : false;
                            if($_amount_is_matching && ($_debtorcode_is_matching || $_account_number_is_matching) && $_invoicecode_is_match) {
                                $match_or_candidate = "match";
                            } elseif($_amount_is_matching || $_debtorcode_is_matching || $_account_number_is_matching || $_invoicecode_is_match) {
                                $match_or_candidate = "candidate";
                            }
                            $matched_amount = 0 < $tmp_invoice->AmountIncl ? min(round((double) $tmp_invoice->AmountIncl, 2), round((double) $this->unmatchedAmount, 2)) : max(round((double) $tmp_invoice->AmountIncl, 2), round((double) $this->unmatchedAmount, 2));
                            $tmp_invoice->Amount = $tmp_invoice->AmountIncl;
                            $this->_addCandidateToArray($tmp_invoice, "invoice", $matched_amount, $match_or_candidate);
                            $_one_candidate_invoice_date["invoice" . $tmp_invoice->id] = $tmp_invoice->Date;
                            $optimize_this_check = true;
                        } else {
                            $list_non_open_invoices[] = $tmp_invoice;
                        }
                    }
                    if($optimize_this_check === true) {
                        if(1 < count($this->candidates)) {
                            $code_length = 0;
                            foreach ($this->candidates as $key => $_candidate) {
                                if($code_length === 0) {
                                    $code_length = strlen($_candidate->ReferenceCode);
                                } elseif(strlen($_candidate->ReferenceCode) < $code_length && $_candidate->CandidateType != "match") {
                                    unset($this->candidates[$key]);
                                }
                            }
                        }
                        if(count($this->candidates) === 1 && isEmptyFloat(round((double) $this->matchedAmount, 2))) {
                            foreach ($this->candidates as $key => $_candidate) {
                                if($_candidate->AmountMatched == round((double) $this->unmatchedAmount, 2) && !($_candidate->ReferenceStatus == "paid" && 0 < $_candidate->AmountPayable && $_candidate->AmountMatched < 0) && $_candidate->ReferenceStatus != "") {
                                    $this->candidates[$key]->CandidateType = "match";
                                    $this->matchedAmount += $_candidate->AmountMatched;
                                    $this->unmatchedAmount -= $_candidate->AmountMatched;
                                    $correct_matches_if_more_suggestions = true;
                                } else {
                                    $results = Database_Model::getInstance()->get("HostFact_Invoice", ["HostFact_Invoice.id", "HostFact_Invoice.InvoiceCode", "HostFact_Invoice.AmountIncl", "HostFact_Invoice.AmountExcl", "(HostFact_Invoice.`AmountIncl` - HostFact_Invoice.`AmountPaid`) as `Amount`", "HostFact_Invoice.CompanyName", "HostFact_Invoice.Initials", "HostFact_Invoice.SurName", "HostFact_Invoice.Debtor", "HostFact_Invoice.Reminders", "HostFact_Invoice.Summations", "HostFact_Invoice.Status", "HostFact_Debtors.DebtorCode", "HostFact_Debtors.AccountNumber"])->join("HostFact_Debtors", "HostFact_Debtors.`id` = HostFact_Invoice.`Debtor`")->where("HostFact_Invoice.Debtor", $_candidate->RelationID)->where("HostFact_Invoice.Date", [">=" => $_one_candidate_invoice_date["invoice" . $_candidate->ReferenceID]])->execute();
                                    if($results) {
                                        foreach ($results as $tmp_invoice) {
                                            $this->_addCandidateToArray($tmp_invoice, "invoice", 0, "candidate");
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $results = Database_Model::getInstance()->get("HostFact_Invoice", ["HostFact_Invoice.id", "HostFact_Invoice.InvoiceCode", "HostFact_Invoice.AmountIncl", "HostFact_Invoice.AmountExcl", "(HostFact_Invoice.`AmountIncl` - HostFact_Invoice.`AmountPaid`) as `Amount`", "HostFact_Invoice.CompanyName", "HostFact_Invoice.Initials", "HostFact_Invoice.SurName", "HostFact_Invoice.Debtor", "HostFact_Invoice.Reminders", "HostFact_Invoice.Summations", "HostFact_Invoice.Status", "HostFact_Debtors.DebtorCode", "HostFact_Debtors.AccountNumber"])->join("HostFact_Debtors", "HostFact_Debtors.`id` = HostFact_Invoice.`Debtor`")->orWhere([["ROUND(HostFact_Invoice.`AmountIncl`,2)", round((double) $this->unmatchedAmount, 2)], ["ROUND(HostFact_Invoice.`AmountExcl`,2)", round((double) $this->unmatchedAmount, 2)], ["ROUND(HostFact_Invoice.`AmountIncl` - HostFact_Invoice.`AmountPaid`,2)", round((double) $this->unmatchedAmount, 2)]])->orderBy("HostFact_Invoice.Date", "DESC")->execute();
                    $optimize_this_check = false;
                    foreach ($results as $tmp_invoice) {
                        if(stripos($record_info->ShortDescription, $tmp_invoice->DebtorCode) !== false) {
                            $matchable_amount = in_array($tmp_invoice, [2, 3]) ? $tmp_invoice->Amount : $tmp_invoice->AmountIncl;
                            $matched_amount = 0 < $matchable_amount ? min(round((double) $matchable_amount, 2), round((double) $this->unmatchedAmount, 2)) : max(round((double) $matchable_amount, 2), round((double) $this->unmatchedAmount, 2));
                            if(isset($this->candidates["invoice" . $tmp_invoice->id]) || isEmptyFloat($this->unmatchedAmount) && $tmp_invoice->Status == 4) {
                                if(isset($this->candidates["invoice" . $tmp_invoice->id]) && $this->candidates["invoice" . $tmp_invoice->id]->AmountPayable == round((double) $this->unmatchedAmount, 2) && $this->candidates["invoice" . $tmp_invoice->id]->CandidateType == "candidate") {
                                    $this->candidates["invoice" . $tmp_invoice->id]->AmountMatched = $this->candidates["invoice" . $tmp_invoice->id]->AmountPayable;
                                    $this->candidates["invoice" . $tmp_invoice->id]->CandidateType = "match";
                                    $this->candidates["invoice" . $tmp_invoice->id]->PaymentType = "full";
                                    $this->matchedAmount += $this->candidates["invoice" . $tmp_invoice->id]->AmountMatched;
                                    $this->unmatchedAmount -= $this->candidates["invoice" . $tmp_invoice->id]->AmountMatched;
                                    if($optimize_this_check === true && count($this->candidates) === 1 && isEmptyFloat($this->matchedAmount)) {
                                        foreach ($this->candidates as $key => $_candidate) {
                                            $this->candidates[$key]->CandidateType = "match";
                                            $this->matchedAmount += $_candidate->AmountMatched;
                                            $this->unmatchedAmount -= $_candidate->AmountMatched;
                                        }
                                    }
                                    foreach ($list_open_invoices as $tmp_invoice) {
                                        if($tmp_invoice->AccountNumber && strtoupper(preg_replace("/[^0-9A-Z]/i", "", $tmp_invoice->AccountNumber)) == $record_info->AccountNumber) {
                                            $match_or_candidate = "candidate";
                                            if(isEmptyFloat($this->matchedAmount) && (round((double) $tmp_invoice->Amount, 2) == round((double) $record_info->Amount, 2) || round((double) $tmp_invoice->AmountIncl, 2) == round((double) $record_info->Amount, 2) || round((double) $tmp_invoice->AmountExcl, 2) == round((double) $record_info->Amount, 2))) {
                                                $match_or_candidate = "match";
                                            }
                                            $matched_amount = 0 < $tmp_invoice->Amount ? min(round((double) $tmp_invoice->Amount, 2), round((double) $this->unmatchedAmount, 2)) : max(round((double) $tmp_invoice->Amount, 2), round((double) $this->unmatchedAmount, 2));
                                            if(!isset($this->candidates["invoice" . $tmp_invoice->id])) {
                                                $this->_addCandidateToArray($tmp_invoice, "invoice", $matched_amount, $match_or_candidate);
                                            }
                                        }
                                    }
                                    $results = Database_Model::getInstance()->get("HostFact_CreditInvoice", ["HostFact_CreditInvoice.id", "HostFact_CreditInvoice.CreditInvoiceCode", "HostFact_CreditInvoice.InvoiceCode", "HostFact_CreditInvoice.AmountIncl", "HostFact_CreditInvoice.AmountExcl", "(HostFact_CreditInvoice.`AmountIncl` - HostFact_CreditInvoice.`AmountPaid`) as `Amount`", "HostFact_CreditInvoice.Creditor", "HostFact_CreditInvoice.Status", "HostFact_Creditors.CompanyName", "HostFact_Creditors.Initials", "HostFact_Creditors.SurName", "HostFact_Creditors.CreditorCode"])->join("HostFact_Creditors", "HostFact_Creditors.`id` = HostFact_CreditInvoice.`Creditor`")->where("HostFact_CreditInvoice.Status", ["IN" => [0, 1, 2]])->execute();
                                    if($results) {
                                        foreach ($results as $tmp_invoice) {
                                            if(stripos($record_info->ShortDescription, $tmp_invoice->CreditInvoiceCode) !== false || stripos($record_info->ShortDescription, $tmp_invoice->InvoiceCode) !== false) {
                                                $matched_amount = 0 < $tmp_invoice->Amount ? min(round((double) $tmp_invoice->Amount, 2), round((double) $this->unmatchedAmount, 2)) : max(round((double) $tmp_invoice->Amount, 2), round((double) $this->unmatchedAmount, 2));
                                                $match_or_candidate = $matched_amount == round(-1 * $tmp_invoice->Amount, 2) || $matched_amount == round(-1 * $tmp_invoice->AmountIncl, 2) || $matched_amount == round(-1 * $tmp_invoice->AmountExcl, 2) ? "match" : "candidate";
                                                $this->_addCandidateToArray($tmp_invoice, "creditinvoice", $matched_amount, $match_or_candidate);
                                            }
                                        }
                                    }
                                    if($correct_matches_if_more_suggestions === true && 1 < count($this->candidates)) {
                                        $this->_removeMatchesFromCandidateArray();
                                    }
                                    if(isEmptyFloat(round((double) $this->unmatchedAmount, 2)) && 1 <= count($this->candidates)) {
                                        return true;
                                    }
                                    return false;
                                }
                            } else {
                                $match_or_candidate = "candidate";
                                if(stripos($record_info->ShortDescription, str_replace(parsePrefixVariables(INVOICECODE_PREFIX), "", $tmp_invoice->InvoiceCode)) !== false) {
                                    $match_or_candidate = "match";
                                }
                                $this->_addCandidateToArray($tmp_invoice, "invoice", $matched_amount, $match_or_candidate);
                                $optimize_this_check = true;
                            }
                        }
                    }
                }
        }
    }
    private function _addCandidateToArray($_candidate, $reference_type, $matched_amount, $match_or_candidate)
    {
        if($reference_type == "invoice") {
            $tmp_amount_payable = round((double) $_candidate->Amount, 2);
            $tmp_paymenttype = $tmp_amount_payable == $matched_amount || $_candidate->Status == 4 ? "full" : "part";
            $tmp_reference_status = $_candidate->Status == 4 ? "paid" : (in_array($_candidate->Status, [2, 3]) ? "outstanding" : "");
        } else {
            $tmp_amount_payable = round(-1 * $_candidate->Amount, 2);
            $tmp_paymenttype = $tmp_amount_payable == $matched_amount || $_candidate->Status == 3 ? "full" : "part";
            $tmp_reference_status = $_candidate->Status == 3 ? "paid" : (in_array($_candidate->Status, [1, 2]) ? "outstanding" : "");
        }
        $this->candidates[$reference_type . $_candidate->id] = (object) ["Type" => $reference_type, "ReferenceID" => $_candidate->id, "ReferenceCode" => $reference_type == "invoice" ? $_candidate->InvoiceCode : $_candidate->CreditInvoiceCode, "ReferencePrefix" => $this->getReferencePrefix($_candidate, $reference_type), "RelationID" => $reference_type == "invoice" ? $_candidate->Debtor : $_candidate->Creditor, "RelationCode" => $reference_type == "invoice" ? $_candidate->DebtorCode : $_candidate->CreditorCode, "RelationName" => $_candidate->CompanyName ? $_candidate->CompanyName : $_candidate->Initials . " " . $_candidate->SurName, "PaymentType" => $tmp_paymenttype, "AmountPayable" => $tmp_amount_payable, "AmountMatched" => $matched_amount, "CandidateType" => $match_or_candidate, "ReferenceStatus" => $tmp_reference_status];
        if($match_or_candidate == "match") {
            $this->matchedAmount += round((double) $matched_amount, 2);
            $this->unmatchedAmount -= round((double) $matched_amount, 2);
        }
    }
    private function _removeMatchesFromCandidateArray()
    {
        foreach ($this->candidates as $key => $_candidate) {
            if($_candidate->CandidateType == "match") {
                $this->matchedAmount -= $_candidate->AmountMatched;
                $this->unmatchedAmount += $_candidate->AmountMatched;
            }
            $this->candidates[$key]->CandidateType = "candidate";
        }
    }
    private function getReferencePrefix($_candidate, $reference_type = "invoice")
    {
        $reference_type = isset($_candidate->Type) && $_candidate->Type == "creditinvoice" ? "creditinvoice" : $reference_type;
        $reference_prefix = "";
        if($reference_type == "creditinvoice") {
            if($_candidate->Status == 8) {
                $reference_prefix = strtolower(__("invoicestatus credit invoice"));
            } elseif($_candidate->Status == 3) {
                $reference_prefix = strtolower(__("already paid"));
            }
            if($_candidate->InvoiceCode) {
                $reference_prefix = $reference_prefix ? $_candidate->InvoiceCode . " / " . $reference_prefix : $_candidate->InvoiceCode;
            } elseif($_candidate->ExternalInvoiceCode) {
                $reference_prefix = $reference_prefix ? $_candidate->ExternalInvoiceCode . " / " . $reference_prefix : $_candidate->ExternalInvoiceCode;
            }
        } elseif($reference_type == "invoice") {
            if($_candidate->Status == 9) {
                $reference_prefix = strtolower(__("invoicestatus expire"));
            } elseif($_candidate->Status == 8) {
                $reference_prefix = strtolower(__("invoicestatus credit invoice"));
            } elseif($_candidate->Status == 4) {
                $reference_prefix = strtolower(__("already paid"));
            } elseif(0 < $_candidate->Summations) {
                $reference_prefix = 1 < $_candidate->Summations ? sprintf(__("bank transaction prefix # summations"), $_candidate->Summations) : __("bank transaction prefix 1 summation");
            } elseif(0 < $_candidate->Reminders) {
                $reference_prefix = 1 < $_candidate->Reminders ? sprintf(__("bank transaction prefix # reminders"), $_candidate->Reminders) : __("bank transaction prefix 1 reminder");
            }
        }
        return $reference_prefix;
    }
    public function getCandidateTable()
    {
        $options = [];
        $options["cols"] = [["key" => "add_icon", "title" => "&nbsp;", "width" => 16], ["key" => "invoicecode", "title" => __("invoice no"), "sortable" => "ReferenceCode"], ["key" => "debtor", "title" => __("debtor"), "sortable" => "RelationCode"], ["key" => "amountexcl", "title" => __("amountexcl"), "sortable" => "AmountExcl", "width" => 100, "colspan" => 3, "class" => "show_col_ws", "td_class" => "show_col_ws", "special_type" => "amount"], ["key" => "amountincl", "title" => __("amountincl"), "sortable" => "AmountIncl", "width" => 100, "colspan" => 3, "special_type" => "amount"], ["key" => "date", "title" => __("invoice date"), "sortable" => "Date"], ["key" => "status", "title" => __("status"), "sortable" => "Status"]];
        $candidate_ids = [];
        foreach ($this->candidates as $tmp) {
            $candidate_ids[$tmp->Type][] = $tmp->ReferenceID;
        }
        $options["filter"] = "";
        $options["parameters"]["candidate_list"] = $candidate_ids;
        $options["data"] = ["class/bankstatement.php", "Transaction_Model", "get_data_candidates_table"];
        $options["form_action"] = "";
        $options["results_per_page"] = 10;
        $options["sort_by"] = "ReferenceCode";
        $options["sort_order"] = "DESC";
        return $options;
    }
    public function get_data_candidates_table($offset, $results_per_page, $sort_by = "", $sort_order = "ASC", $parameters = [])
    {
        global $array_invoicestatus;
        global $array_creditinvoicestatus;
        $options = !empty($parameters) ? $parameters : [];
        $options["offset"] = $offset;
        $options["results_per_page"] = $results_per_page;
        $options["sort_by"] = $sort_by;
        $options["sort_order"] = $sort_order;
        $candidate_list = $this->listCandidates($options);
        $data = ["TotalResults" => $this->total_results];
        foreach ($candidate_list as $_candidate) {
            $_data_status = $_candidate->Type == "invoice" && $_candidate->Status == 4 ? "paid" : ($_candidate->Type == "invoice" && in_array($_candidate->Status, [2, 3]) ? "outstanding" : ($_candidate->Type == "creditinvoice" && $_candidate->Status == 3 ? "paid" : ($_candidate->Type == "creditinvoice" && in_array($_candidate->Status, [1, 2]) ? "outstanding" : "")));
            $amount_payable = $_candidate->Status == 4 ? round((double) $_candidate->AmountIncl, 2) : round($_candidate->AmountIncl - $_candidate->AmountPaid, 2);
            $ico_add = "<img src=\"images/ico_add.png\" class=\"pointer match_add_icon\" style=\"margin-top:-1px;\" data-reference-type=\"" . $_candidate->Type . "\" data-reference-id=\"" . $_candidate->ReferenceID . "\" data-reference-code=\"" . htmlspecialchars($_candidate->ReferenceCode) . "\" data-relation-id=\"" . $_candidate->RelationID . "\" data-relation-code=\"" . htmlspecialchars($_candidate->RelationCode) . "\" data-relation-name=\"" . ($_candidate->CompanyName ? htmlspecialchars($_candidate->CompanyName) : htmlspecialchars($_candidate->Initials . " " . $_candidate->SurName)) . "\" data-amount-payable=\"" . $amount_payable . "\" data-reference-prefix=\"" . htmlspecialchars($this->getReferencePrefix($_candidate)) . "\" data-reference-status=\"" . $_data_status . "\">";
            $invoicecode_link_class = substr($_candidate->PayBefore, 0, 10) < date("Y-m-d") && ($_candidate->Status == 2 || $_candidate->Status == 3) ? "c3" : "c1";
            $invoicecode_directdebit = $_candidate->Type == "invoice" && $_candidate->Authorisation == "yes" ? "\n<span class=\"fontsmall c4\">" . __("inc") . "</span>" : "";
            if($_candidate->Type == "invoice") {
                if($_candidate->Status == "4") {
                    $status_td = "<span class=\"ico inline check\">" . __("invoice status paid") . "</span>";
                } elseif($_candidate->Status == "2" || $_candidate->Status == "3") {
                    if($_candidate->Authorisation == "yes") {
                        if($_candidate->TransactionID) {
                            $status_td = 1 < $_candidate->AuthTrials ? sprintf(__("invoice status waiting incasso trials"), $_candidate->AuthTrials) : __("invoice status waiting incasso");
                        } else {
                            $status_td = 1 <= $_candidate->AuthTrials ? sprintf(__("invoice status open for incasso trials"), $_candidate->AuthTrials + 1) : __("invoice status open for incasso");
                        }
                    } elseif(date("Y-m-d") <= substr($_candidate->PayBefore, 0, 10)) {
                        $status_td = __("invoice status unpaid");
                    } elseif((int) $_candidate->Summations === 0 && ((int) $_candidate->Reminders === 0 || $_candidate->Reminders < INVOICE_REMINDER_NUMBER && date("Y-m-d", strtotime("+ " . INVOICE_REMINDER_TERM . " day", strtotime($_candidate->ReminderDate))) < date("Y-m-d"))) {
                        $status_td = "<span class=\"infopopupinvoicestatus delaypopup\"><a href=\"invoices.php?page=show&id=" . $_candidate->ReferenceID . "&open=reminder\" class=\"ico inline sendemail\"><em>" . str_replace("{count}", $_candidate->Reminders + 1, __("invoice status send reminder")) . "</em></a>" . "&nbsp;";
                        if(1 <= $_candidate->Reminders) {
                            $status_td .= "<span class=\"popup\">" . __("last reminder was sent on") . ": " . rewrite_date_db2site($_candidate->ReminderDate) . "<b></b></span>";
                        }
                        $status_td .= "</span>";
                    } elseif((int) $_candidate->Summations === 0 && date("Y-m-d") <= date("Y-m-d", strtotime("+ " . INVOICE_REMINDER_TERM . " day", strtotime($_candidate->ReminderDate)))) {
                        $status_td = "<span class=\"infopopupinvoicestatus delaypopup\">" . str_replace("{count}", $_candidate->Reminders, __("invoice status reminder sent")) . "&nbsp;<span class=\"popup\">" . __("last reminder was sent on") . ": " . rewrite_date_db2site($_candidate->ReminderDate) . "<b></b></span>" . "</span>";
                    } elseif(INT_SUPPORT_SUMMATIONS && ((int) $_candidate->Summations === 0 || date("Y-m-d", strtotime("+ " . INVOICE_SUMMATION_TERM . " day", strtotime($_candidate->SummationDate))) < date("Y-m-d"))) {
                        $status_td = "<span class=\"infopopupinvoicestatus delaypopup\"><a href=\"invoices.php?page=show&id=" . $_candidate->ReferenceID . "&open=summation\" class=\"ico inline sendemail\"><em>" . str_replace("{count}", $_candidate->Summations + 1, __("invoice status send summation")) . "</em></a>" . "&nbsp;";
                        if(1 <= $_candidate->Summations) {
                            $status_td .= "<span class=\"popup\">" . __("last summation was sent on") . ": " . rewrite_date_db2site($_candidate->SummationDate) . "<b></b></span>";
                        } else {
                            $status_td .= "<span class=\"popup\">" . __("last reminder was sent on") . ": " . rewrite_date_db2site($_candidate->ReminderDate) . "<b></b></span>";
                        }
                        $status_td .= "</span>";
                    } elseif(INT_SUPPORT_SUMMATIONS && date("Y-m-d") <= date("Y-m-d", strtotime("+ " . INVOICE_SUMMATION_TERM . " day", strtotime($_candidate->SummationDate)))) {
                        $status_td = "<span class=\"infopopupinvoicestatus delaypopup\">" . str_replace("{count}", $_candidate->Summations, __("invoice status summation sent")) . "&nbsp;<span class=\"popup\">" . __("last summation was sent on") . ": " . rewrite_date_db2site($_candidate->SummationDate) . "<b></b></span>" . "</span>";
                    } else {
                        $status_td = __("invoice status unpaid");
                    }
                } else {
                    $status_td = $array_invoicestatus[$_candidate->Status];
                }
            } else {
                $status_td = $array_creditinvoicestatus[$_candidate->Status];
            }
            $partpayment_td = "";
            if(!isEmptyFloat($_candidate->AmountPaid) && ($_candidate->Type == "invoice" && in_array($_candidate->Status, [2, 3]) || $_candidate->Type == "creditinvoice" && in_array($_candidate->Status, [1, 2]))) {
                $partpayment_td = "<span class=\"ico inline money infopopuptop\">&nbsp;<span class=\"popup\">" . __("open sum") . ": " . money($_candidate->AmountIncl - $_candidate->AmountPaid) . "<b></b></span></span>";
            }
            $inv_link = $_candidate->Type == "invoice" ? "invoices.php?page=show&id=" : "creditors.php?page=show_invoice&id=";
            $rel_link = $_candidate->Type == "invoice" ? "debtors.php?page=show&id=" : "creditors.php?page=show&id=";
            $data[] = [$ico_add, "<a href=\"" . $inv_link . $_candidate->ReferenceID . "\" class=\"" . $invoicecode_link_class . " a1\">" . htmlspecialchars($_candidate->ReferenceCode) . "</a>" . $invoicecode_directdebit . ($_candidate->ExternalInvoiceCode ? "<span class=\"c4\"> / " . htmlspecialchars($_candidate->ExternalInvoiceCode) . "</span>" : ""), "<a href=\"" . $rel_link . $_candidate->RelationID . "\" class=\"a1\">" . ($_candidate->CompanyName ? htmlspecialchars($_candidate->CompanyName) : htmlspecialchars($_candidate->SurName . ", " . $_candidate->Initials)) . "</a>", [currency_sign_td(CURRENCY_SIGN_LEFT), money($_candidate->AmountExcl, false), currency_sign_td(CURRENCY_SIGN_RIGHT)], [currency_sign_td(CURRENCY_SIGN_LEFT), money($_candidate->AmountIncl, false), currency_sign_td(CURRENCY_SIGN_RIGHT) . $partpayment_td], rewrite_date_db2site($_candidate->Date), ($_candidate->Type == "creditinvoice" ? "<div class=\"inline_tag_div\" style=\"background-color:#999999;color:#fff;width:60px;font-size:11px;\">" . __("bank transaction type purchase") . "</div> " : "<div class=\"inline_tag_div\" style=\"width:60px;font-size:11px;\">" . __("bank transaction type sales") . "</div> ") . $status_td];
        }
        return $data;
    }
    public function listCandidates($options = [])
    {
        $sort_by = isset($options["sort_by"]) && $options["sort_by"] ? $options["sort_by"] : "ReferenceCode";
        $sort_order = isset($options["sort_order"]) && $options["sort_order"] ? $options["sort_order"] : "ASC";
        $offset = isset($options["offset"]) && $options["offset"] ? $options["offset"] : "0";
        $results_per_page = isset($options["results_per_page"]) && $options["results_per_page"] ? $options["results_per_page"] : MAX_RESULTS_LIST;
        $filter = isset($options["filter"]) && $options["filter"] != "" ? $options["filter"] : "";
        $group_by = isset($options["group_by"]) && $options["group_by"] ? $options["group_by"] : "";
        $loop_array_for_results_and_rowcount = ["results", "rowcount"];
        $this->total_results = 0;
        $list = [];
        foreach ($loop_array_for_results_and_rowcount as $_sql_type) {
            if($_sql_type == "rowcount") {
                $select = ["HostFact_Invoice.`id`"];
            } else {
                $select = ["'invoice' as `Type`", "HostFact_Invoice.`id` as ReferenceID", "HostFact_Invoice.InvoiceCode as `ReferenceCode`", "HostFact_Invoice.Debtor as `RelationID`", "HostFact_Invoice.Date", "HostFact_Debtors.DebtorCode as `RelationCode`", "HostFact_Debtors.CompanyName", "HostFact_Debtors.SurName", "HostFact_Debtors.Initials", "DATE_ADD(HostFact_Invoice.`Date`, INTERVAL HostFact_Invoice.`Term` DAY) as PayBefore", "HostFact_Invoice.Status", "HostFact_Invoice.AmountExcl", "HostFact_Invoice.AmountIncl", "HostFact_Invoice.AmountPaid", "HostFact_Invoice.Authorisation", "HostFact_Invoice.TransactionID", "HostFact_Invoice.AuthTrials", "HostFact_Invoice.Reminders", "HostFact_Invoice.ReminderDate", "HostFact_Invoice.Summations", "HostFact_Invoice.SummationDate", "'' as `ExternalInvoiceCode`"];
            }
            Database_Model::getInstance()->get("HostFact_Invoice", $select)->join("HostFact_Debtors", "HostFact_Invoice.`Debtor` = HostFact_Debtors.`id`")->where("HostFact_Invoice.Status", ["!=" => 0]);
            if($filter != "") {
                if(strpos($filter, "SDD") !== false && (strpos($filter, "FRST") !== false || strpos($filter, "RCUR") !== false)) {
                    $search_for = "%" . str_replace(["-FRST", "-RCUR"], "", $filter) . "%";
                } else {
                    $search_for = "%" . $filter . "%";
                }
                Database_Model::getInstance()->orWhere([["HostFact_Invoice.InvoiceCode", ["LIKE" => $search_for]], ["HostFact_Invoice.SDDBatchID", ["LIKE" => $search_for]], ["HostFact_Debtors.DebtorCode", ["LIKE" => $search_for]], ["HostFact_Debtors.CompanyName", ["LIKE" => $search_for]], ["HostFact_Debtors.SurName", ["LIKE" => $search_for]], ["HostFact_Debtors.AccountNumber", ["LIKE" => $search_for]]]);
            } elseif(isset($options["candidate_list"]["invoice"]) && is_array($options["candidate_list"]["invoice"]) && 0 < count($options["candidate_list"]["invoice"])) {
                Database_Model::getInstance()->where("HostFact_Invoice.id", ["IN" => $options["candidate_list"]["invoice"]]);
            } else {
                Database_Model::getInstance()->where("HostFact_Invoice.InvoiceCode", "");
            }
            if($sort_by && $_sql_type == "results") {
                switch ($sort_by) {
                    case "ReferenceCode":
                        Database_Model::getInstance()->orderBy("IF(SUBSTRING(HostFact_Invoice.`InvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_Invoice.`InvoiceCode`,1,1))", $sort_order)->orderBy("LENGTH(HostFact_Invoice.`InvoiceCode`)", $sort_order)->orderBy("HostFact_Invoice.`InvoiceCode`", $sort_order);
                        break;
                    default:
                        Database_Model::getInstance()->orderBy($sort_by, $sort_order);
                }
            }
            if($_sql_type == "results" && 0 <= $offset && $results_per_page != "all") {
                Database_Model::getInstance()->limit(0, $offset + $results_per_page);
            }
            if($_sql_type == "rowcount") {
                $select = ["HostFact_CreditInvoice.`id`"];
            } else {
                $select = ["'creditinvoice' as `Type`", "HostFact_CreditInvoice.`id` as ReferenceID", "HostFact_CreditInvoice.CreditInvoiceCode as `ReferenceCode`", "HostFact_CreditInvoice.Creditor as `RelationID`", "HostFact_CreditInvoice.Date", "HostFact_Creditors.CreditorCode as `RelationCode`", "HostFact_Creditors.CompanyName", "HostFact_Creditors.SurName", "HostFact_Creditors.Initials", "DATE_ADD(HostFact_CreditInvoice.`Date`, INTERVAL HostFact_CreditInvoice.`Term` DAY) as PayBefore", "HostFact_CreditInvoice.Status", "HostFact_CreditInvoice.AmountExcl", "HostFact_CreditInvoice.AmountIncl", "HostFact_CreditInvoice.AmountPaid", "'' as `Authorisation`", "'' as `TransactionID`", "'' as `AuthTrials`", "'' as `Reminders`", "'' as `ReminderDate`", "'' as `Summations`", "'' as `SummationDate`", "HostFact_CreditInvoice.InvoiceCode as `ExternalInvoiceCode`"];
            }
            Database_Model::getInstance()->getUnion("HostFact_CreditInvoice", $select)->join("HostFact_Creditors", "HostFact_CreditInvoice.`Creditor` = HostFact_Creditors.`id`");
            if($filter != "") {
                $search_for = "%" . $filter . "%";
                Database_Model::getInstance()->orWhere([["HostFact_CreditInvoice.InvoiceCode", ["LIKE" => $search_for]], ["HostFact_CreditInvoice.CreditInvoiceCode", ["LIKE" => $search_for]], ["HostFact_Creditors.CreditorCode", ["LIKE" => $search_for]], ["HostFact_Creditors.CompanyName", ["LIKE" => $search_for]], ["HostFact_Creditors.SurName", ["LIKE" => $search_for]], ["HostFact_Creditors.AccountNumber", ["LIKE" => $search_for]]]);
            } elseif(!empty($options["candidate_list"]["creditinvoice"])) {
                Database_Model::getInstance()->where("HostFact_CreditInvoice.id", ["IN" => $options["candidate_list"]["creditinvoice"]]);
            } else {
                Database_Model::getInstance()->where("HostFact_CreditInvoice.CreditInvoiceCode", "");
            }
            if($sort_by && $_sql_type == "results") {
                switch ($sort_by) {
                    case "ReferenceCode":
                        Database_Model::getInstance()->orderBy("IF(SUBSTRING(HostFact_CreditInvoice.`CreditInvoiceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(HostFact_CreditInvoice.`CreditInvoiceCode`,1,1))", $sort_order)->orderBy("LENGTH(HostFact_CreditInvoice.`CreditInvoiceCode`)", $sort_order)->orderBy("HostFact_CreditInvoice.`CreditInvoiceCode`", $sort_order);
                        break;
                    default:
                        Database_Model::getInstance()->orderBy($sort_by, $sort_order);
                }
            }
            if($_sql_type == "results" && 0 <= $offset && $results_per_page != "all") {
                Database_Model::getInstance()->limit(0, $offset + $results_per_page);
            }
            Database_Model::getInstance()->closeUnion();
            if($sort_by && $_sql_type == "results") {
                switch ($sort_by) {
                    case "ReferenceCode":
                        Database_Model::getInstance()->orderBy("IF(SUBSTRING(`ReferenceCode`,1,1) REGEXP '^[0-9]*\$', 0, SUBSTRING(`ReferenceCode`,1,1))", $sort_order)->orderBy("LENGTH(`ReferenceCode`)", $sort_order)->orderBy("`ReferenceCode`", $sort_order);
                        break;
                    default:
                        Database_Model::getInstance()->orderBy($sort_by, $sort_order);
                }
            }
            if($_sql_type == "results" && 0 <= $offset && $results_per_page != "all") {
                Database_Model::getInstance()->limit($offset, $results_per_page);
            }
            if($result = Database_Model::getInstance()->execute()) {
                if($_sql_type == "rowcount") {
                    $this->total_results = count($result);
                } else {
                    $list = $result;
                }
            }
        }
        return $list;
    }
}
class Bank_Statement_Model
{
    protected $fileIdentification;
    protected $fileCreationDate;
    protected $statementRecords;
    protected $createdHashesInThisFile;
    public function __construct()
    {
        $this->statementRecords = [];
        $this->createdHashesInThisFile = [];
        $this->totalNumberOfRecords = 0;
    }
    public function saveRecentActivity()
    {
        $activity_log = [];
        foreach ($this->statementRecords as $_record) {
            $activity_log[$_record["BankAccount"]][$_record["Date"]][] = $_record["TransactionID"];
        }
        foreach ($activity_log as $bank_account => $dates) {
            $counter = 0;
            $start_date = false;
            $end_date = false;
            foreach ($dates as $record_date => $_records) {
                $start_date = $start_date === false || $record_date < $start_date ? $record_date : $start_date;
                $end_date = $end_date === false || $end_date < $record_date ? $record_date : $end_date;
                $counter += count($_records);
            }
            $result = Database_Model::getInstance()->insert("HostFact_Transaction_Import", ["BankAccount" => $bank_account, "ImportDate" => date("Y-m-d H:i:s"), "Counter" => $counter, "StartDate" => $start_date, "EndDate" => $end_date])->execute();
        }
    }
    public function getRecentActivity()
    {
        $recent_activity = [];
        $result = Database_Model::getInstance()->get("HostFact_Transaction_Import")->orderBy("ImportDate", "DESC")->asArray()->execute();
        if($result) {
            foreach ($result as $_import_log) {
                if(isset($recent_activity[$_import_log["BankAccount"]]) && 3 <= count($recent_activity[$_import_log["BankAccount"]])) {
                } else {
                    $recent_activity[$_import_log["BankAccount"]][] = $_import_log;
                }
            }
        }
        return $recent_activity;
    }
}
class MT940Structured_Model extends Bank_Statement_Model
{
    public function readFile($filename, $nice_filename)
    {
        if(!file_exists($filename)) {
            $this->Error[] = sprintf(__("bankstatement readfile not found"), htmlspecialchars($nice_filename));
            return false;
        }
        $lines = [];
        $content = file_get_contents($filename);
        if(strpos($content, ":86:") === false || strpos($content, "/NAME/") === false && strpos($content, "/CNTP/") === false) {
            $this->Error[] = sprintf(__("bankstatement invalid file"), htmlspecialchars($nice_filename));
            return false;
        }
        $content = explode("\n", str_replace(["\r\n", "\r"], "\n", $content));
        $tmp_line = "";
        foreach ($content as $line) {
            if(substr($line, 0, 1) == ":" && $tmp_line) {
                $lines[] = $tmp_line;
                $tmp_line = "";
            }
            $tmp_line .= $line;
        }
        if($tmp_line) {
            $lines[] = $tmp_line;
            $tmp_line = "";
        }
        Database_Model::getInstance()->beginTransaction();
        $this->statementRecords = [];
        foreach ($lines as $line_id => $line_content) {
            $this->readLine($line_content);
        }
        $this->saveRecentActivity();
        Database_Model::getInstance()->commit();
        $this->Success[] = sprintf(__("bankstatement file read, x transactions added, y already present"), count($this->statementRecords), $this->totalNumberOfRecords - count($this->statementRecords));
        return true;
    }
    private function readLine($line)
    {
        if(!preg_match("/^:[0-9a-z]{2,3}:/i", $line, $matches)) {
            return false;
        }
        $code = $matches[0];
        $line = substr($line, strlen($code));
        switch ($code) {
            case ":940:":
            case ":20:":
            case ":28C:":
            case ":60F:":
            case ":62F:":
                return true;
                break;
            case ":25:":
                $line = explode(" ", $line, 2);
                $this->_mt940_statement_iban = $line[0];
                $this->_mt940_statement_currency = isset($line[1]) ? $line[1] : "EUR";
                break;
            case ":61:":
                $this->_mt940_entry_date = "20" . substr($line, 0, 2) . "-" . substr($line, 2, 2) . "-" . substr($line, 4, 2);
                $debet_credit_position = 10;
                if(substr($line, 6, 1) == "C" || substr($line, 6, 1) == "D") {
                    $debet_credit_position = 6;
                }
                $this->_mt940_entry_amount = str_replace(",", ".", substr($line, $debet_credit_position + 1, 15));
                $this->_mt940_entry_amount = substr($line, $debet_credit_position, 1) == "C" ? (double) $this->_mt940_entry_amount : -1 * (double) $this->_mt940_entry_amount;
                $this->_mt940_entry_bankcode = substr($line, $debet_credit_position + 16, 4);
                $this->_mt940_entry_bankcode2 = trim(substr($line, $debet_credit_position + 20, 16));
                $this->_mt940_entry_iban = substr($line, $debet_credit_position + 36, 34);
                break;
            case ":86:":
                if(!isset($this->_mt940_entry_date)) {
                } else {
                    $this->totalNumberOfRecords++;
                    $preg_codes = ["MARF", "EREF", "PREF", "RTRN", "BENM", "ORDP", "NAME", "ID", "ADDR", "REMI", "CDTRREFTP//CD/SCOR/ISSR/CUR/CDTRREF", "ISDT", "CSID", "CNTP"];
                    $codes_from_line = [];
                    foreach ($preg_codes as $preg_code) {
                        $line = str_replace("/" . $preg_code, "++||++" . $preg_code, $line);
                    }
                    $line_exploded = explode("++||++", $line);
                    foreach ($line_exploded as $_code_result) {
                        if(strpos($_code_result, "CDTRREFTP//CD/SCOR/ISSR/CUR/CDTRREF") !== false) {
                            $codes_from_line["CDTRREFTP//CD/SCOR/ISSR/CUR/CDTRREF"] = substr($_code_result, strpos($_code_result, "CDTRREFTP//CD/SCOR/ISSR/CUR/CDTRREF") + strlen("CDTRREFTP//CD/SCOR/ISSR/CUR/CDTRREF") + 1);
                        } elseif($_code_result) {
                            $_code_result = explode("/", $_code_result, 2);
                            if(count($_code_result) == 2) {
                                $codes_from_line[$_code_result[0]] = $_code_result[1];
                            }
                        }
                    }
                    $entryID = "";
                    $entryType = "";
                    $entryShortDescription = "";
                    $entryExtendedDescription = "";
                    $entryDebtorName = "";
                    if(isset($codes_from_line["PREF"])) {
                        $entryType = "batch";
                        $entryShortDescription = rtrim(trim($codes_from_line["PREF"]), "/");
                    } elseif(isset($codes_from_line["RTRN"]) && isset($codes_from_line["MARF"])) {
                        $entryType = "reversal";
                        $entryShortDescription = rtrim(trim($codes_from_line["EREF"]), "/");
                        $entryExtendedDescription = $codes_from_line["RTRN"] . " / " . $codes_from_line["MARF"];
                    }
                    $entryDebtorName = isset($codes_from_line["NAME"]) ? $codes_from_line["NAME"] : "";
                    if(!$entryDebtorName && isset($codes_from_line["CNTP"])) {
                        $tmp = explode("/", $codes_from_line["CNTP"]);
                        $entryDebtorName = isset($tmp[2]) ? $tmp[2] : "";
                        if(isset($tmp[2])) {
                            $this->_mt940_entry_iban = $tmp[0];
                        }
                    }
                    if(!$entryShortDescription && isset($codes_from_line["REMI"]) && $codes_from_line["REMI"]) {
                        $codes_from_line["REMI"] = str_replace(["USTD//", "STRD/CUR/", "STRD/ISO/"], "", $codes_from_line["REMI"]);
                        $entryShortDescription = rtrim(trim($codes_from_line["REMI"]), "/");
                        $entryShortDescription = rtrim($entryShortDescription, "/");
                    } elseif(!$entryShortDescription && isset($codes_from_line["CDTRREFTP//CD/SCOR/ISSR/CUR/CDTRREF"]) && $codes_from_line["CDTRREFTP//CD/SCOR/ISSR/CUR/CDTRREF"]) {
                        $entryShortDescription = trim($codes_from_line["CDTRREFTP//CD/SCOR/ISSR/CUR/CDTRREF"]);
                    }
                    if(!$entryShortDescription && isset($codes_from_line["EREF"])) {
                        $entryShortDescription = rtrim(trim($codes_from_line["EREF"]), "/");
                    } elseif($entryShortDescription && isset($codes_from_line["EREF"])) {
                        $entryExtendedDescription = rtrim(trim($codes_from_line["EREF"]), "/");
                    }
                    for ($_eid = 1; !$entryID || in_array($entryID, $this->createdHashesInThisFile); $_eid++) {
                        $_eid_suffix = 1 < $_eid ? "||" . $_eid : "";
                        $entryID = $this->_mt940_entry_date . "||" . md5($this->_mt940_statement_iban . "||" . $entryShortDescription . "||" . substr($this->_mt940_entry_iban, -5) . "||" . $this->_mt940_entry_amount . $_eid_suffix);
                    }
                    $this->createdHashesInThisFile[] = $entryID;
                    if($entryType == "") {
                        $entryType = 0 <= $this->_mt940_entry_amount ? "deposit" : "withdrawal";
                    }
                    $result = Database_Model::getInstance()->insert("HostFact_Transactions", ["BankAccount" => $this->_mt940_statement_iban, "Date" => $this->_mt940_entry_date, "Type" => $entryType, "Amount" => $this->_mt940_entry_amount, "ShortDescription" => $entryShortDescription, "ExtendedDescription" => $entryExtendedDescription, "Name" => $entryDebtorName, "AccountNumber" => $this->_mt940_entry_iban, "AccountBIC" => "", "BankReference" => $entryID, "Status" => "unmatched"])->onDuplicate(["id" => ["RAW" => "id"]])->execute();
                    if($result) {
                        $transaction_id = $result;
                        if(0 < $transaction_id) {
                            $this->statementRecords[] = ["TransactionID" => $transaction_id, "Date" => $this->_mt940_entry_date, "BankAccount" => $this->_mt940_statement_iban];
                        }
                    }
                    unset($this->_mt940_entry_date);
                    unset($this->_mt940_entry_amount);
                    unset($this->_mt940_entry_bankcode);
                    unset($this->_mt940_entry_bankcode2);
                    unset($this->_mt940_entry_iban);
                }
                break;
            default:
                return true;
        }
    }
}
class MT940_Model extends Bank_Statement_Model
{
    public function readFile($filename, $nice_filename)
    {
        if(!file_exists($filename)) {
            $this->Error[] = sprintf(__("bankstatement readfile not found"), htmlspecialchars($nice_filename));
            return false;
        }
        $lines = [];
        $content = file_get_contents($filename);
        if(strpos($content, ":86:") === false) {
            $this->Error[] = sprintf(__("bankstatement invalid file"), htmlspecialchars($nice_filename));
            return false;
        }
        $content = explode("\n", str_replace(["\r\n", "\r"], "\n", $content));
        $tmp_line = "";
        foreach ($content as $line) {
            if(substr($line, 0, 1) == ":" && $tmp_line) {
                $lines[] = $tmp_line;
                $tmp_line = "";
            }
            $tmp_line .= $line;
        }
        if($tmp_line) {
            $lines[] = $tmp_line;
            $tmp_line = "";
        }
        $prev_prefix = "";
        $prev_k = 0;
        foreach ($lines as $k => $line) {
            if(substr($line, 0, 4) == ":86:" && $prev_prefix == ":86:") {
                $lines[$prev_k] = trim($lines[$prev_k]) . " " . trim(substr($line, 4));
                unset($lines[$k]);
            } else {
                $prev_k = $k;
                $prev_prefix = substr($line, 0, 4);
            }
        }
        Database_Model::getInstance()->beginTransaction();
        $this->statementRecords = [];
        if(stripos($lines[0], "knab") !== false) {
            $this->is_knab = true;
        }
        $this->transaction_open = false;
        foreach ($lines as $line_id => $line_content) {
            $this->readLine($line_content);
        }
        $this->saveRecentActivity();
        Database_Model::getInstance()->commit();
        $this->Success[] = sprintf(__("bankstatement file read, x transactions added, y already present"), count($this->statementRecords), $this->totalNumberOfRecords - count($this->statementRecords));
        return true;
    }
    private function readLine($line)
    {
        if(!preg_match("/^:[0-9a-z]{2,3}:/i", $line, $matches)) {
            return false;
        }
        $code = $matches[0];
        $line = substr($line, strlen($code));
        switch ($code) {
            case ":940:":
            case ":20:":
            case ":28C:":
            case ":60F:":
            case ":62F:":
                return true;
                break;
            case ":25:":
                $line = explode(" ", $line, 2);
                $this->_mt940_statement_iban = $line[0];
                $this->_mt940_statement_currency = isset($line[1]) ? $line[1] : "EUR";
                break;
            case ":61:":
                $this->transaction_open = true;
                $this->_mt940_entry_date = "20" . substr($line, 0, 2) . "-" . substr($line, 2, 2) . "-" . substr($line, 4, 2);
                $debet_credit_position = 10;
                if(substr($line, 6, 1) == "C" || substr($line, 6, 1) == "D") {
                    $debet_credit_position = 6;
                }
                $this->_mt940_entry_amount = str_replace(",", ".", substr($line, $debet_credit_position + 1, 15));
                $this->_mt940_entry_amount = substr($line, $debet_credit_position, 1) == "C" ? (double) $this->_mt940_entry_amount : -1 * (double) $this->_mt940_entry_amount;
                $this->_mt940_entry_bankcode = substr($line, $debet_credit_position + 16, 4);
                $this->_mt940_entry_bankcode2 = trim(substr($line, $debet_credit_position + 20, 16));
                $this->_mt940_entry_debtorname = substr($line, $debet_credit_position + 36, 34);
                break;
            case ":86:":
                if(!$this->transaction_open) {
                } else {
                    $this->transaction_open = false;
                    $this->totalNumberOfRecords++;
                    $entryID = "";
                    $entryType = "";
                    $entryShortDescription = $line;
                    $entryExtendedDescription = "";
                    $entryDebtorName = $this->_mt940_entry_debtorname;
                    for ($_eid = 1; !$entryID || in_array($entryID, $this->createdHashesInThisFile); $_eid++) {
                        $_eid_suffix = 1 < $_eid ? "||" . $_eid : "";
                        $entryID = $this->_mt940_entry_date . "||" . md5($this->_mt940_statement_iban . "||" . $entryShortDescription . "||" . substr($this->_mt940_entry_iban, -5) . "||" . $this->_mt940_entry_amount . $_eid_suffix);
                    }
                    $this->createdHashesInThisFile[] = $entryID;
                    if($entryType == "") {
                        $entryType = 0 <= $this->_mt940_entry_amount ? "deposit" : "withdrawal";
                    }
                    if(isset($this->is_knab) && $this->is_knab === true && (!isset($this->_mt940_entry_iban) || !$this->_mt940_entry_iban)) {
                        preg_match("/REK: (.*?)\\/NAAM: (.*?)/U", $entryShortDescription, $matches);
                        if(is_array($matches)) {
                            if(1 < count($matches)) {
                                $this->_mt940_entry_iban = $matches[1];
                                $entryShortDescription = str_replace($matches[0], "", $entryShortDescription);
                            }
                            if(2 < count($matches) && $entryDebtorName == "") {
                                $entryDebtorName = $matches[2];
                            }
                        }
                    }
                    $result = Database_Model::getInstance()->insert("HostFact_Transactions", ["BankAccount" => $this->_mt940_statement_iban, "Date" => $this->_mt940_entry_date, "Type" => $entryType, "Amount" => $this->_mt940_entry_amount, "ShortDescription" => $entryShortDescription, "ExtendedDescription" => $entryExtendedDescription, "Name" => $entryDebtorName, "AccountNumber" => $this->_mt940_entry_iban, "AccountBIC" => "", "BankReference" => $entryID, "Status" => "unmatched"])->onDuplicate(["id" => ["RAW" => "id"]])->execute();
                    if($result) {
                        $transaction_id = $result;
                        if(0 < $transaction_id) {
                            $this->statementRecords[] = ["TransactionID" => $transaction_id, "Date" => $this->_mt940_entry_date, "BankAccount" => $this->_mt940_statement_iban];
                        }
                    }
                    unset($this->_mt940_entry_date);
                    unset($this->_mt940_entry_amount);
                    unset($this->_mt940_entry_bankcode);
                    unset($this->_mt940_entry_bankcode2);
                    unset($this->_mt940_entry_iban);
                }
                break;
            default:
                return true;
        }
    }
}
class CAMT053_Model extends Bank_Statement_Model
{
    protected $debug = true;
    public function readFile($filename, $nice_filename)
    {
        if(!file_exists($filename)) {
            $this->Error[] = sprintf(__("bankstatement readfile not found"), htmlspecialchars($nice_filename));
            return false;
        }
        $xml_array = [];
        try {
            if(substr($nice_filename, -4, 4) == ".zip" && function_exists("zip_open")) {
                $zip = zip_open($filename);
                if(is_resource($zip)) {
                    do {
                        $entry = zip_read($zip);
                        if($entry && substr(zip_entry_name($entry), -4, 4) == ".xml") {
                            $entry_content = zip_entry_read($entry, zip_entry_filesize($entry));
                            $entry_content = utf8_encode($entry_content);
                            $xml_tmp = @new SimpleXMLElement($entry_content);
                            if(isset($xml_tmp->BkToCstmrStmt->Stmt)) {
                                $xml_array[(string) $xml_tmp->BkToCstmrStmt->GrpHdr->MsgId] = $xml_tmp;
                            }
                        }
                    } while (!$entry);
                    zip_close($zip);
                    ksort($xml_array);
                } else {
                    throw new Exception("invalid file");
                }
            } else {
                try {
                    $xml_tmp = @new SimpleXMLElement($filename, NULL, true);
                } catch (Exception $e) {
                    $entry_content = @file_get_contents($filename);
                    $entry_content = utf8_encode($entry_content);
                    $xml_tmp = @new SimpleXMLElement($entry_content);
                }
                if(!isset($xml_tmp->BkToCstmrStmt->Stmt)) {
                    throw new Exception("invalid file");
                }
                $xml_array[] = $xml_tmp;
            }
        } catch (Exception $e) {
            $this->Error[] = sprintf(__("bankstatement invalid file"), htmlspecialchars($nice_filename));
            return false;
        }
        $this->statementRecords = [];
        Database_Model::getInstance()->beginTransaction();
        foreach ($xml_array as $xml_element) {
            $this->xml = $xml_element;
            $this->fileIdentification = $this->xml->BkToCstmrStmt->GrpHdr->MsgId;
            $this->fileCreationDate = date("Y-m-d H:i:s", strtotime($this->xml->BkToCstmrStmt->GrpHdr->CreDtTm));
            foreach ($this->xml->BkToCstmrStmt->Stmt as $statement) {
                if(!$this->readStatement($statement)) {
                }
            }
        }
        $this->saveRecentActivity();
        Database_Model::getInstance()->commit();
        $this->Success[] = sprintf(__("bankstatement file read, x transactions added, y already present"), count($this->statementRecords), $this->totalNumberOfRecords - count($this->statementRecords));
        return true;
    }
    public function readStatement($statement)
    {
        $statementId = $statement->Id;
        $statementIBAN = (string) $statement->Acct->Id->IBAN;
        if(isset($statement->Acct->Ccy)) {
            $statementCurrency = $statement->Acct->Ccy;
        } else {
            $statementCurrency = CURRENCY_CODE;
        }
        if($statementCurrency != CURRENCY_CODE) {
            $this->Error[] = sprintf(__("bankstatement only default currency is supported"), CURRENCY_CODE);
            return false;
        }
        if(isset($statement->TxsSummry->TtlNtries->NbOfNtries)) {
            $statementNumberOfEntries = $statement->TxsSummry->TtlNtries->NbOfNtries;
            if((int) $statementNumberOfEntries === 0) {
                return true;
            }
        }
        $statementBalanceStart = 0;
        $statementBalanceEnd = 0;
        foreach ($statement->Bal as $balance) {
            if($balance->Tp->CdOrPrtry->Cd == "OPBD") {
                $statementBalanceStart = $balance->CdtDbtInd == "CRDT" ? $balance->Amt : -1 * $balance->Amt;
            } elseif($balance->Tp->CdOrPrtry->Cd == "CLBD") {
                $statementBalanceEnd = $balance->CdtDbtInd == "CRDT" ? $balance->Amt : -1 * $balance->Amt;
            }
        }
        $array_entries_import = [];
        foreach ($statement->Ntry as $entry) {
            $this->totalNumberOfRecords++;
            if($debug) {
                echo "<strong>Transactie:</strong>:<br />";
            }
            $entryType = "";
            $entryShortDescription = "";
            $entryExtendedDescription = "";
            $entryDebtorName = "";
            $entryDebtorIBAN = "";
            $entryDebtorBIC = "";
            $entryAmount = $entry->CdtDbtInd == "CRDT" ? (double) $entry->Amt : -1 * (double) $entry->Amt;
            if($debug) {
                echo "Bedrag: " . $entryAmount . "<br />";
            }
            if(isset($entry->ValDt->Dt)) {
                $entryDate = date("Y-m-d", strtotime($entry->ValDt->Dt));
                if($debug) {
                    echo "Datum: " . $entryDate . "<br />";
                }
            } elseif(isset($entry->BookgDt->Dt)) {
                $entryDate = date("Y-m-d", strtotime($entry->BookgDt->Dt));
                if($debug) {
                    echo "Datum: " . $entryDate . "<br />";
                }
            }
            if(isset($entry->NtryDtls->Btch)) {
                if(isset($entry->NtryDtls->Btch->PmtInfId)) {
                    $sdd_id = trim($entry->NtryDtls->Btch->PmtInfId);
                    $entryShortDescription = $sdd_id;
                }
                if(isset($entry->CdtDbtInd) && $entry->CdtDbtInd == "CRDT") {
                    $entryType = "batch";
                }
            }
            if($entry->RvslInd == "true") {
                if($debug) {
                    echo "Correctie op eerdere transactie<br />";
                }
                $entryType = "reversal";
                $entryShortDescription = trim($entry->NtryDtls->TxDtls->Refs->EndToEndId);
                $entryExtendedDescription = $entry->NtryDtls->TxDtls->RtrInf->Rsn->Cd . ": " . $entry->NtryDtls->TxDtls->RtrInf->AddtlInf;
            }
            if(isset($entry->NtryDtls->TxDtls)) {
                foreach ($entry->NtryDtls->TxDtls as $transaction_detail) {
                    if($entryType == "batch" && isset($transaction_detail->Refs->PmtInfId)) {
                        $sdd_id = trim($transaction_detail->Refs->PmtInfId);
                        $entryShortDescription = $sdd_id;
                    }
                    if($entryType == "batch" && isset($transaction_detail->Refs->EndToEndId)) {
                        $entryExtendedDescription .= $transaction_detail->Refs->EndToEndId . "\r\n";
                    } elseif(isset($transaction_detail->RltdPties->DbtrAcct)) {
                        $entryDebtorName = isset($transaction_detail->RltdPties->Dbtr->Nm) ? $transaction_detail->RltdPties->Dbtr->Nm : "";
                        if(isset($transaction_detail->RltdPties->DbtrAcct->Id->IBAN)) {
                            $entryDebtorIBAN = $transaction_detail->RltdPties->DbtrAcct->Id->IBAN;
                        } elseif(isset($transaction_detail->RltdPties->DbtrAcct->Id->Othr->Id)) {
                            $entryDebtorIBAN = $transaction_detail->RltdPties->DbtrAcct->Id->Othr->Id;
                        }
                        $entryDebtorBIC = isset($transaction_detail->RltdAgts->DbtrAgt->FinInstnId->BIC) ? $transaction_detail->RltdAgts->DbtrAgt->FinInstnId->BIC : "";
                    } elseif(isset($transaction_detail->RltdPties->Cdtr)) {
                        $entryDebtorName = isset($transaction_detail->RltdPties->Cdtr->Nm) ? $transaction_detail->RltdPties->Cdtr->Nm : "";
                        if(isset($transaction_detail->RltdPties->CdtrAcct->Id->IBAN)) {
                            $entryDebtorIBAN = $transaction_detail->RltdPties->CdtrAcct->Id->IBAN;
                        } elseif(isset($transaction_detail->RltdPties->CdtrAcct->Id->Othr->Id)) {
                            $entryDebtorIBAN = $transaction_detail->RltdPties->CdtrAcct->Id->Othr->Id;
                        }
                        $entryDebtorBIC = isset($transaction_detail->RltdAgts->CdtrAgt->FinInstnId->BIC) ? $transaction_detail->RltdAgts->CdtrAgt->FinInstnId->BIC : "";
                    }
                    if(!$entryShortDescription && isset($transaction_detail->RmtInf->Strd->CdtrRefInf->Ref)) {
                        $entryShortDescription = trim($transaction_detail->RmtInf->Strd->CdtrRefInf->Ref);
                        if(!$entryExtendedDescription && isset($transaction_detail->RmtInf->Ustrd)) {
                            $entryExtendedDescription = trim($transaction_detail->RmtInf->Ustrd);
                        }
                    } elseif(!$entryShortDescription && isset($transaction_detail->RmtInf->Ustrd)) {
                        $entryShortDescription = trim($transaction_detail->RmtInf->Ustrd);
                    }
                    if(!$entryShortDescription && isset($entry->NtryDtls->TxDtls->AddtlTxInf)) {
                        $entryShortDescription = trim($entry->NtryDtls->TxDtls->AddtlTxInf);
                    } elseif($entryShortDescription && !$entryExtendedDescription && isset($entry->NtryDtls->TxDtls->AddtlTxInf)) {
                        $entryExtendedDescription = $entry->NtryDtls->TxDtls->AddtlTxInf;
                    }
                    if(!$entryShortDescription && isset($entry->NtryDtls->TxDtls->Refs->EndToEndId)) {
                        $entryShortDescription = trim($entry->NtryDtls->TxDtls->Refs->EndToEndId);
                    } elseif($entryShortDescription && !$entryExtendedDescription && isset($entry->NtryDtls->TxDtls->Refs->EndToEndId)) {
                        $entryExtendedDescription = $entry->NtryDtls->TxDtls->Refs->EndToEndId;
                    }
                }
            }
            if(isset($entry->AddtlNtryInf) && $entry->AddtlNtryInf && !$entryShortDescription) {
                $entryShortDescription = $entry->AddtlNtryInf;
            }
            for ($_eid = 1; !$entryID || in_array($entryID, $this->createdHashesInThisFile); $_eid++) {
                $_eid_suffix = 1 < $_eid ? "||" . $_eid : "";
                $entryID = $entryDate . "||" . md5($statementIBAN . "||" . $entryShortDescription . "||" . substr($entryDebtorIBAN, -5) . "||" . $entryAmount . $_eid_suffix);
            }
            $this->createdHashesInThisFile[] = $entryID;
            if($entryType == "") {
                $entryType = 0 <= $entryAmount ? "deposit" : "withdrawal";
            }
            if($debug) {
                echo "Type: " . $entryType . "<br />";
                echo "Short: " . $entryShortDescription . "<br />";
                echo "Extended: " . $entryExtendedDescription . "<br />";
                echo "Debtor: " . $entryDebtorName . "<br />";
                echo "IBAN: " . $entryDebtorIBAN . "<br />";
                echo "BIC: " . $entryDebtorBIC . "<br />";
            }
            if(!$entryType || !$entryShortDescription || !$entryDebtorName || !$entryDebtorIBAN) {
            }
            if($debug) {
                echo "<br /><br /><hr />";
            }
            $_entry = ["BankAccount" => $statementIBAN, "Date" => $entryDate, "Type" => $entryType, "Amount" => $entryAmount, "ShortDescription" => $entryShortDescription, "ExtendedDescription" => $entryExtendedDescription, "Name" => $entryDebtorName, "AccountNumber" => $entryDebtorIBAN, "AccountBIC" => $entryDebtorBIC, "BankReference" => $entryID];
            if(!isset($array_entries_import[$entryDate])) {
                $array_entries_import[$entryDate] = [];
            }
            if($entryType == "batch") {
                array_unshift($array_entries_import[$entryDate], $_entry);
            } else {
                $array_entries_import[$entryDate][] = $_entry;
            }
        }
        foreach ($array_entries_import as $_entries_per_day) {
            foreach ($_entries_per_day as $_entry_to_add) {
                $result = Database_Model::getInstance()->insert("HostFact_Transactions", ["BankAccount" => $_entry_to_add["BankAccount"], "Date" => $_entry_to_add["Date"], "Type" => $_entry_to_add["Type"], "Amount" => $_entry_to_add["Amount"], "ShortDescription" => $_entry_to_add["ShortDescription"], "ExtendedDescription" => $_entry_to_add["ExtendedDescription"], "Name" => $_entry_to_add["Name"], "AccountNumber" => $_entry_to_add["AccountNumber"], "AccountBIC" => $_entry_to_add["AccountBIC"], "BankReference" => $_entry_to_add["BankReference"], "Status" => "unmatched"])->onDuplicate(["id" => ["RAW" => "id"]])->execute();
                if($result) {
                    $transaction_id = $result;
                    if(0 < $transaction_id) {
                        $this->statementRecords[] = ["TransactionID" => $transaction_id, "Date" => $_entry_to_add["Date"], "BankAccount" => $_entry_to_add["BankAccount"]];
                    }
                }
            }
        }
        return true;
    }
}

?>