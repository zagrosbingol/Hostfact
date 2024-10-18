<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(isset($_GET["page"]) && $_GET["page"] == "services") {
    $load_all_product_module_languagefiles = true;
}
require_once "config.php";
checkRight(U_SETTINGS_SHOW);
$page = isset($_GET["page"]) ? $_GET["page"] : "general";
switch ($page) {
    case "finance":
        if(!empty($_POST) && U_SETTINGS_EDIT) {
            if(isset($_POST["payment_mail_when_helper"]) && $_POST["payment_mail_when_helper"] == "no") {
                $_POST["PAYMENT_MAIL_WHEN"] = "";
                $_POST["PAYMENT_MAIL"] = "";
            } else {
                $_POST["PAYMENT_MAIL_WHEN"] = isset($_POST["PAYMENT_MAIL_WHEN"]) ? implode("|", $_POST["PAYMENT_MAIL_WHEN"]) : "";
            }
            if(isset($_POST["contract_renew_for_helper"]) && $_POST["contract_renew_for_helper"] == "no") {
                $_POST["CONTRACT_RENEW_FOR"] = "none";
            } elseif($_POST["contract_renew_for_helper"] == "yes" && $_POST["PERIODIC_REMINDER_SENT_FOR"] == "private") {
                $_POST["CONTRACT_RENEW_FOR"] = "private";
            }
            if(isset($_POST["contract_renew_confirm_mail_helper"]) && $_POST["contract_renew_confirm_mail_helper"] == "no") {
                $_POST["CONTRACT_RENEW_CONFIRM_MAIL"] = "0";
            }
        }
        break;
    case "automation":
        if(!empty($_POST) && U_SETTINGS_EDIT) {
            require_once "class/automation.php";
            $automation = new automation();
            $automation_array = ["acceptorder_value", "sentinvoice_value", "makeinvoice_value", "registerdomain_value", "makeaccount_value", "checkticket_value", "batchmail_value", "makebackup_value", "remindersummation_value"];
            foreach ($automation_array as $k) {
                if(!isset($_POST[$k])) {
                    $_POST[$k] = "0";
                }
            }
            foreach ($_POST as $key => $value) {
                $var = explode("_", $key, 2);
                $set[$var[0]][$var[1]] = esc($value);
            }
            unset($_POST["acceptorder_checkbox_exception"]);
            unset($_POST["sentinvoice_checkbox_exception"]);
            unset($_POST["remindersummation_checkbox_exception"]);
            $automation->edit($set);
            $tasks = ["CRONJOB_NOTIFY_ORDER" => __("tasks process order"), "CRONJOB_NOTIFY_DOMAIN" => __("tasks register or transfer domains"), "CRONJOB_NOTIFY_HOSTING" => __("tasks create hosting accounts"), "CRONJOB_NOTIFY_TERMINATIONS" => __("tasks termination actions")];
            $tasks = do_filter("automation_cronjob_notify_settings", $tasks);
            $fill_array = ["CRONJOB_NOTIFY_MAILADDRESS"];
            $fill_array = array_merge($fill_array, array_keys($tasks));
            if(isset($_POST["cronjob_error_notification_helper"]) && $_POST["cronjob_error_notification_helper"] == "no") {
                foreach ($fill_array as $k) {
                    $_POST[$k] = "";
                }
            } else {
                if(strlen($_POST["CRONJOB_NOTIFY_MAILADDRESS"]) && !check_email_address($_POST["CRONJOB_NOTIFY_MAILADDRESS"])) {
                    $automation->Error[] = __("invalid cron notification emailaddress");
                }
                foreach ($fill_array as $k) {
                    if(!isset($_POST[$k])) {
                        $_POST[$k] = "";
                    }
                }
            }
            flashMessage($automation);
        }
        break;
    case "services":
        if(!empty($_POST) && U_SERVICESETTING_EDIT) {
            if(isset($_POST["ACCOUNT_GENERATION"]) && $_POST["ACCOUNT_GENERATION"] == "1" && preg_match("/^[a-z]/i", $_POST["ACCOUNTCODE_PREFIX"]) == 0) {
                $error_class->Error[] = __("hosting account generation - prefix must start with a-z character");
            }
            if(!isset($_POST["DOMAIN_SYNC"]) || $_POST["DOMAIN_SYNC"] != "yes") {
                $_POST["DOMAIN_SYNC"] = "no";
            }
            if(!isset($_POST["DOMAIN_SYNC_EXPDATE"]) || $_POST["DOMAIN_SYNC_EXPDATE"] != "yes") {
                $_POST["DOMAIN_SYNC_EXPDATE"] = "no";
            }
            if(!isset($_POST["DOMAIN_SYNC_NAMESERVERS"]) || $_POST["DOMAIN_SYNC_NAMESERVERS"] != "yes") {
                $_POST["DOMAIN_SYNC_NAMESERVERS"] = "no";
            }
            require_once "class/upgradegroup.php";
            if(isset($_POST["upgradegroup_name"]) && isset($_POST["upgradegroup_id"]) && isset($_POST["upgradegroup_products"])) {
                $upgradegroup = new UpgradeGroup_Model("hosting");
                foreach ($_POST["upgradegroup_id"] as $key => $upgradegroup_id) {
                    $upgradegroup->Name = $_POST["upgradegroup_name"][$key];
                    $upgradegroup->Products = $_POST["upgradegroup_products"][$key];
                    if(9999999 <= $upgradegroup_id) {
                        $upgradegroup->add();
                    } else {
                        $upgradegroup->Identifier = $upgradegroup_id;
                        $upgradegroup->edit();
                    }
                    $error_class->Error = array_merge($error_class->Error, $upgradegroup->Error);
                }
            }
            if(isset($_POST["delete_groups"]) && $_POST["delete_groups"]) {
                $upgradegroup = new UpgradeGroup_Model("hosting");
                $groups_to_delete = explode(",", $_POST["delete_groups"]);
                foreach ($groups_to_delete as $upgradegroup_id) {
                    $upgradegroup->Identifier = $upgradegroup_id;
                    $upgradegroup->delete();
                    $error_class->Error = array_merge($error_class->Error, $upgradegroup->Error);
                }
            }
            if(!empty($additional_product_types)) {
                foreach ($additional_product_types as $tmp_module_type => $tmp_module_title) {
                    if(isset($_POST["module"][$tmp_module_type]) && isset($_module_instances[$tmp_module_type]) && method_exists($_module_instances[$tmp_module_type], "service_setting_save")) {
                        $_module_instances[$tmp_module_type]->service_setting_save($_POST["module"][$tmp_module_type]);
                    }
                }
            }
            include_once "3rdparty/modules/products/product_module.php";
            foreach ($additional_product_types_all as $_product_module => $_product_module_active) {
                if(isset($_POST["ProductModules"]) && in_array($_product_module, $_POST["ProductModules"])) {
                    if($_product_module_active == "active") {
                    } else {
                        $product_module = new product_module();
                        $product_module->module_enable_disable($_product_module, "enable");
                    }
                } elseif($_product_module_active == "active") {
                    if(isset($_POST["iagreewithdeactivate"]) && $_POST["iagreewithdeactivate"] == "yes") {
                        $_module_instances[$_product_module]->module_disable();
                    } else {
                        $error_class->Error[] = __("cannot disable product module without agree");
                    }
                }
            }
            if(isset($_POST["dns_management_module"]) && $_POST["dns_management_module"] == "yes" && !is_module_active("dnsmanagement")) {
                include_once __DIR__ . "/3rdparty/modules/hostfact_module.php";
                $module = new hostfact_module();
                $module->ModuleType = "dns";
                $module->module_enable_disable("dnsmanagement", "enable");
            } elseif(is_module_active("dnsmanagement") && !isset($_POST["dns_management_module"])) {
                include_once __DIR__ . "/3rdparty/modules/hostfact_module.php";
                $module = new hostfact_module();
                $module->ModuleType = "dns";
                $module->module_enable_disable("dnsmanagement", "disable");
            }
            unset($_POST["module"]);
            unset($_POST["ProductModules"]);
            unset($_POST["delete_groups"]);
            unset($_POST["upgradegroup_name"]);
            unset($_POST["upgradegroup_id"]);
            unset($_POST["upgradegroup_products"]);
        }
        break;
    case "api":
        if(isset($_POST["API_KEY"]) && !empty($_POST) && U_SETTINGS_EDIT) {
            if(!isset($_POST["API_ACTIVE"]) && $_POST["API_ACTIVE"] != "yes") {
                $_POST["API_ACTIVE"] = "no";
            }
            if($_POST["API_ACTIVE"] == "yes" && $_POST["API_LOG_TYPE"] != "none" && $_POST["API_CLEAN_LOG_AFTER_DAYS"] <= 0) {
                $error_class->Error[] = __("api log days not 0");
                unset($_POST);
            }
        }
        break;
    case "tickets":
        if(!empty($_POST) && U_SETTINGS_EDIT) {
            if(!isset($_POST["TICKET_USE"])) {
                $_POST["TICKET_USE"] = 0;
            }
            if(!isset($_POST["TICKET_NOTIFY_TO_EMPLOYEE"])) {
                $_POST["TICKET_NOTIFY_TO_EMPLOYEE"] = 0;
            }
            if(isset($_POST["TICKET_EMAILADDRESS"]) && isset($_POST["TICKET_NOTIFY_EMAILADDRESS"]) && $_POST["TICKET_EMAILADDRESS"] && $_POST["TICKET_EMAILADDRESS"] == $_POST["TICKET_NOTIFY_EMAILADDRESS"]) {
                $error_class->Error[] = __("ticket notify address may not be same as ticket mailaddress");
                unset($_POST["TICKET_NOTIFY_EMAILADDRESS"]);
            } elseif($_POST["TICKET_USE_MAIL"] == "1") {
                sleep(1);
                require_once "class/ticket.php";
                $ticket = new mailserver();
                if($_POST["TICKET_POP3_AUTH_TYPE"] === Settings::AUTH_TYPE_OAUTH2_MS && isset($_SESSION["oauth2_device_code"])) {
                    $refresh_token = $ticket->oauth2_get_refresh_token_with_device_code(ticket::$OAUTH2_MS_CLIENT_ID, ticket::$OAUTH2_MS_TOKEN_URL, $_SESSION["oauth2_device_code"]);
                    unset($_SESSION["oauth2_device_code"]);
                    if($refresh_token) {
                        $_POST["TICKET_PASSWORD"] = $refresh_token;
                        $_POST["TICKET_POP3_SERVER"] = "tls://outlook.office365.com";
                        $_POST["TICKET_POP3_PORT"] = "995";
                    } else {
                        $error_class->Error[] = sprintf(__("ticket error oauth device code"), "https://microsoft.com/devicelogin");
                        $ticket_Error = true;
                    }
                }
                if(!$ticket_Error) {
                    $ticket_password = 0 < strlen($_POST["TICKET_PASSWORD"]) ? passcrypt($_POST["TICKET_PASSWORD"]) : TICKET_PASSWORD;
                    if($ticket->connect($_POST["TICKET_POP3_SERVER"], $_POST["TICKET_POP3_PORT"], $_POST["TICKET_EMAILADDRESS"], $ticket_password, $_POST["TICKET_POP3_AUTH_TYPE"]) === false) {
                        $error_class->Error = array_merge($error_class->Error, $ticket->Error);
                        $ticket_Error = true;
                    } else {
                        require_once "class/automation.php";
                        $automation = new automation();
                        $automation->enableCheckTicket();
                    }
                }
            }
            if(isset($_POST["TICKET_NOTIFY_EMAILADDRESS"]) && $_POST["TICKET_NOTIFY_EMAILADDRESS"]) {
                $_POST["TICKET_NOTIFY_EMAILADDRESS"] = check_email_address($_POST["TICKET_NOTIFY_EMAILADDRESS"], "convert");
            }
        }
        break;
    case "general":
        if(!empty($_POST) && U_SETTINGS_EDIT) {
            if(isset($_POST["CURRENCY_FORMAT"])) {
                $settings = new settings();
                if(strlen($_POST["CURRENCY_FORMAT"]) == 1) {
                    $settings->Variable = "AMOUNT_THOU_SEPERATOR";
                    $settings->Value = "";
                    $settings->edit();
                    $settings->Variable = "AMOUNT_DEC_SEPERATOR";
                    $settings->Value = $_POST["CURRENCY_FORMAT"];
                    $settings->edit();
                } else {
                    $settings->Variable = "AMOUNT_THOU_SEPERATOR";
                    $settings->Value = substr($_POST["CURRENCY_FORMAT"], 0, 1) != "#" ? substr($_POST["CURRENCY_FORMAT"], 0, 1) : " ";
                    $settings->edit();
                    $settings->Variable = "AMOUNT_DEC_SEPERATOR";
                    $settings->Value = substr($_POST["CURRENCY_FORMAT"], 1, 1);
                    $settings->edit();
                }
                unset($_POST["CURRENCY_FORMAT"]);
            }
            $settings = new settings();
            $settings->Variable = "ANONYMOUS_FEEDBACK";
            $settings->Value = isset($_POST["ANONYMOUS_FEEDBACK"]) ? "yes" : "no";
            $settings->edit();
            if(isset($_POST["PDF_PAGE_SIZE"])) {
                if(in_array($_POST["PDF_PAGE_SIZE"], ["210x297", "215.9x279.4"])) {
                    $page_size = explode("x", $_POST["PDF_PAGE_SIZE"], 2);
                    $settings = new settings();
                    $settings->Variable = "PDF_PAGE_WIDTH";
                    $settings->Value = $page_size[0];
                    $settings->edit();
                    $settings->Variable = "PDF_PAGE_HEIGHT";
                    $settings->Value = $page_size[1];
                    $settings->edit();
                }
                unset($_POST["PDF_PAGE_SIZE"]);
            }
            $settings = new settings();
            if(isset($_POST["TaxType"])) {
                if($_POST["TaxType"] == "total" && $settings->_validateTax($_POST["TaxRate2"]) || $_POST["TaxType"] == "line" && $settings->_validateTax($_POST["TaxRate1"]) || $_POST["TaxType"] == "both" && $settings->_validateTax($_POST["TaxRate1"]) && $settings->_validateTax($_POST["TaxRate2"]) || $_POST["TaxType"] == "none") {
                    $defaultTaxRate2 = $_POST["DefaultTaxRate2"] ?? 0;
                    if(isset($_POST["TaxChanger"]) && (STANDARD_TAX != $_POST["DefaultTaxRate1"] || STANDARD_TOTAL_TAX != $defaultTaxRate2)) {
                        $settings->updateTaxChanger($_POST["TaxChanger"], $_POST["DefaultTaxRate1"], $defaultTaxRate2, isset($_POST["CompoundTaxRate2"]) && $_POST["CompoundTaxRate2"] == "yes" ? "yes" : "no");
                    }
                    switch ($_POST["TaxType"]) {
                        case "none":
                            $settings->updateTaxes("line");
                            $settings->updateTaxes("total");
                            unset($_POST["TaxRule"]);
                            $_POST["VAT_CALC_METHOD"] = "excl";
                            break;
                        case "line":
                            $settings->updateTaxes("line", $_POST["TaxRate1"], $_POST["TaxLabel1"], $_POST["DefaultTaxRate1"]);
                            $settings->updateTaxes("total");
                            break;
                        case "total":
                            $settings->updateTaxes("line");
                            $settings->updateTaxes("total", $_POST["TaxRate2"], $_POST["TaxLabel2"], $_POST["DefaultTaxRate2"], isset($_POST["CompoundTaxRate2"]) && $_POST["CompoundTaxRate2"] == "yes" ? "yes" : "no");
                            $_POST["VAT_CALC_METHOD"] = "excl";
                            break;
                        case "both":
                            $settings->updateTaxes("line", $_POST["TaxRate1"], $_POST["TaxLabel1"], $_POST["DefaultTaxRate1"]);
                            $settings->updateTaxes("total", $_POST["TaxRate2"], $_POST["TaxLabel2"], $_POST["DefaultTaxRate2"], isset($_POST["CompoundTaxRate2"]) && $_POST["CompoundTaxRate2"] == "yes" ? "yes" : "no");
                            break;
                        default:
                            unset($_SESSION["wf_cache_array_tax"]);
                            unset($_SESSION["wf_cache_array_tax_info"]);
                            unset($_SESSION["wf_cache_array_tax_default"]);
                            unset($_SESSION["wf_cache_array_total_tax"]);
                            unset($_SESSION["wf_cache_array_total_taxpercentages_info"]);
                            unset($_SESSION["wf_cache_array_total_tax_default"]);
                            $settings->removeTaxRules();
                            if(isset($_POST["TaxRule"]["Countries"]) && 1 < count($_POST["TaxRule"]["Countries"])) {
                                foreach ($_POST["TaxRule"]["Countries"] as $k => $cc) {
                                    if(count($_POST["TaxRule"]["Countries"]) - 1 <= $k) {
                                    } else {
                                        $rule = ["CountryCode" => $cc, "StateCode" => isset($_POST["TaxRule"]["States"][$k]) ? $_POST["TaxRule"]["States"][$k] : "all", "Restriction" => isset($_POST["TaxRule"]["Restriction"][$k]) ? $_POST["TaxRule"]["Restriction"][$k] : "all", "TaxLevel1" => isset($_POST["TaxRule"]["Rate1"][$k]) ? $_POST["TaxRule"]["Rate1"][$k] : "", "TaxLevel2" => isset($_POST["TaxRule"]["Rate2"][$k]) ? $_POST["TaxRule"]["Rate2"][$k] : ""];
                                        $settings->addTaxRule($rule);
                                    }
                                }
                            }
                    }
                }
                unset($_POST["TaxType"]);
                unset($_POST["TaxRate1"]);
                unset($_POST["TaxLabel1"]);
                unset($_POST["DefaultTaxRate1"]);
                unset($_POST["TaxRate2"]);
                unset($_POST["TaxLabel2"]);
                unset($_POST["DefaultTaxRate2"]);
                unset($_POST["CompoundTaxRate2"]);
                unset($_POST["TaxChanger"]);
                unset($_POST["TaxRule"]);
            }
            $url_array = ["BACKOFFICE_URL", "CLIENTAREA_URL", "IDEAL_EMAIL", "ORDERFORM_URL", "ORDERFORM_TO_PAYMENTDIR", "BACKUP_DIR", "DIR_EMAIL_ATTACHMENTS", "DIR_CREDIT_INVOICES", "DIR_PDF_FILES", "DIR_TICKET_ATTACHMENTS", "DIR_INVOICE_ATTACHMENTS", "DIR_DEBTOR_ATTACHMENTS", "DIR_CREDITOR_ATTACHMENTS"];
            foreach ($url_array as $url_var) {
                if(substr($_POST[$url_var], -1) != "/") {
                    $_POST[$url_var] = $_POST[$url_var] . "/";
                }
            }
            if(isset($_POST["IP_WHITELIST"])) {
                $new_whitelist = [];
                $whitelisted_ips = [];
                $json_whitelist = IP_WHITELIST && json_decode(htmlspecialchars_decode(IP_WHITELIST), true) ? json_decode(htmlspecialchars_decode(IP_WHITELIST), true) : [];
                foreach ($_POST["IP_WHITELIST"] as $_ip) {
                    if(!trim($_ip)) {
                    } else {
                        foreach ($json_whitelist as $_ip_whitelist) {
                            if(trim($_ip) == $_ip_whitelist["IP"]) {
                                $whitelisted_ips[] = trim($_ip);
                                $new_whitelist[] = $_ip_whitelist;
                            }
                        }
                        $new_whitelist[] = ["DateTime" => date("Y-m-d H:i:s"), "IP" => trim($_ip), "Who" => $account->Identifier];
                    }
                }
                $_POST["IP_WHITELIST"] = !empty($new_whitelist) ? json_encode($new_whitelist) : "";
            } else {
                $_POST["IP_WHITELIST"] = "";
            }
            if(isset($_POST["IP_BLACKLIST"])) {
                if(is_array($_POST["IP_BLACKLIST"]) && !empty($_POST["IP_BLACKLIST"])) {
                    $new_blacklist = [];
                    $json_blacklist = IP_BLACKLIST && json_decode(htmlspecialchars_decode(IP_BLACKLIST), true) ? json_decode(htmlspecialchars_decode(IP_BLACKLIST), true) : [];
                    foreach ($_POST["IP_BLACKLIST"] as $_ip) {
                        if(!trim($_ip) || isset($whitelisted_ips) && in_array($_ip, $whitelisted_ips)) {
                        } elseif(ip_in_range($_SERVER["REMOTE_ADDR"], [$_ip])) {
                            $settings->Error[] = __("unable to blacklist your own ip");
                            flashMessage($settings);
                            $settings = new settings();
                        } else {
                            foreach ($json_blacklist as $_ip_blacklist) {
                                if(trim($_ip) == $_ip_blacklist["IP"]) {
                                    $new_blacklist[] = $_ip_blacklist;
                                }
                            }
                            $new_blacklist[] = ["DateTime" => date("Y-m-d H:i:s"), "IP" => trim($_ip), "Who" => $account->Identifier];
                        }
                    }
                    $_POST["IP_BLACKLIST"] = !empty($new_blacklist) ? json_encode($new_blacklist) : "";
                } else {
                    $_POST["IP_BLACKLIST"] = "";
                }
            } else {
                $_POST["IP_BLACKLIST"] = "";
            }
            $post_data_security_http_headers = [];
            if(isset($_POST["SECURITY_HEADERS"])) {
                foreach ($_POST["SECURITY_HEADERS"] as $key => $value) {
                    $post_data_security_http_headers[$key] = $value;
                }
            }
            $_POST["SECURITY_HEADERS"] = json_encode($post_data_security_http_headers);
            $_POST["DISABLE_CURLOPT_SSL_VERIFICATION"] = isset($_POST["DISABLE_CURLOPT_SSL_VERIFICATION"]) && $_POST["DISABLE_CURLOPT_SSL_VERIFICATION"] == "1" ? "1" : "0";
        }
        break;
    case "mail":
        if(!empty($_POST) && U_SETTINGS_EDIT) {
            if(!isset($_POST["SEND_TICKET_BCC"])) {
                $_POST["SEND_TICKET_BCC"] = "no";
            } elseif($_POST["SEND_TICKET_BCC"] == "yes" && $_POST["BCC_EMAILADDRESS"] != "" && !check_email_address($_POST["BCC_EMAILADDRESS"])) {
                $error_class->Error[] = __("invalid emailaddress");
                unset($_POST);
            }
            $settings = is_object($settings) ? $settings : new settings();
            $dkim_domains = isset($_POST["DKIM_DOMAINS"]) ? $_POST["DKIM_DOMAINS"] : [];
            $settings->setDKIM($dkim_domains);
            unset($_POST["DKIM_DOMAINS"]);
        }
        break;
    default:
        if(!empty($_POST) && !isset($_POST["ajaxResultsPerPage"]) && U_SETTINGS_EDIT && !isset($ticket_Error)) {
            $settings = is_object($settings) ? $settings : new settings();
            foreach ($_POST as $key => $value) {
                $settings->Variable = esc($key);
                $settings->Value = esc($value);
                $settings->edit();
            }
            if(isset($_POST["SMTP_ON"]) && $_POST["SMTP_ON"] == 1) {
                require_once "3rdparty/mail/PHPMailer.php";
                require_once "3rdparty/mail/SMTP.php";
                require_once "3rdparty/mail/Exception.php";
                $mailer = new PHPMailer\PHPMailer\PHPMailer();
                $mailer->SMTPOptions = ["ssl" => ["verify_peer" => false, "allow_self_signed" => false, "verify_peer_name" => false]];
                $mailer->SMTPSecure = substr(esc($_POST["SMTP_HOST"]), 0, 6) == "tls://" ? "tls" : (substr(esc($_POST["SMTP_HOST"]), 0, 6) == "ssl://" ? "ssl" : $mailer->SMTPSecure);
                $mailer->Host = substr(esc($_POST["SMTP_HOST"]), 0, 6) == "tls://" ? substr(esc($_POST["SMTP_HOST"]), 6) : (substr(esc($_POST["SMTP_HOST"]), 0, 6) == "ssl://" ? substr(esc($_POST["SMTP_HOST"]), 6) : esc($_POST["SMTP_HOST"]));
                $mailer->Host .= $_POST["SMTP_HOST_PORT"] ? ":" . esc($_POST["SMTP_HOST_PORT"]) : "";
                $mailer->SMTPAuth = $_POST["SMTP_AUTH"] == "1" ? true : false;
                $mailer->Username = esc($_POST["SMTP_USERNAME"]);
                $mailer->Password = $_POST["SMTP_PASSWORD"] ? esc($_POST["SMTP_PASSWORD"]) : passcrypt(SMTP_PASSWORD);
                $mailer->IsSMTP();
                try {
                    ob_start();
                    $mailer->SMTPDebug = 1;
                    $result = $mailer->SmtpConnect();
                    $smtp_debug = ob_get_contents();
                    $mailer->SMTPDebug = 0;
                    $mailer->getSMTPInstance()->setDebugLevel($mailer->SMTPDebug);
                    ob_end_clean();
                    if($result) {
                        if(!$mailer->getSMTPInstance()->Mail("noreply@hostfact.nl")) {
                            $settings->Warning[] = __("smtp server test error");
                        }
                    } elseif(!$result) {
                        $settings->Warning[] = sprintf(__("smtp server cannot connect"), "<br />" . nl2br($smtp_debug));
                    }
                } catch (phpmailerException $e) {
                    $settings->Warning[] = sprintf(__("smtp server cannot connect"), $e->getMessage());
                }
            }
            if(isset($_POST["CLIENTAREA_URL"])) {
                $settings->checkClientAreaUrl($_POST["CLIENTAREA_URL"]);
            }
            if(empty($settings->Error)) {
                $settings->Success[] = __("settings are modified");
            }
            flashMessage($settings);
            header("Location: settings.php?page=" . $page);
            exit;
        } else {
            switch ($page) {
                case "finance":
                    require_once "class/template.php";
                    $emailtemplatelist = new emailtemplate();
                    $fields = ["Name"];
                    $emailtemplates = $emailtemplatelist->all($fields);
                    $templatelist = new template();
                    $fields = ["Name", "Type"];
                    $templates = $templatelist->all($fields);
                    $templatelist->Type = "other";
                    $templates_other = $templatelist->all($fields, false, false, "-1", "Type", "other");
                    $message = parse_message();
                    $wfh_page_title = __("settings") . " - " . __("invoices and pricequotes");
                    $sidebar_template = "settings.sidebar.php";
                    require_once "views/settings.finance.php";
                    break;
                case "mail":
                    require_once "class/template.php";
                    $emailtemplatelist = new emailtemplate();
                    $fields = ["Name", "Sender"];
                    $emailtemplates = $emailtemplatelist->all($fields);
                    $message = parse_message($settings);
                    $wfh_page_title = __("settings") . " - " . __("mail settings");
                    $sidebar_template = "settings.sidebar.php";
                    require_once "views/settings.mail.php";
                    break;
                case "automation":
                    require_once "class/automation.php";
                    $automation = isset($automation) ? $automation : new automation();
                    $automation->show();
                    require_once "class/clientareaprofiles.php";
                    $ClientareaProfiles_Model = new ClientareaProfiles_Model();
                    $clientarea_profiles = $ClientareaProfiles_Model->listProfiles();
                    $message = parse_message($automation);
                    $wfh_page_title = __("settings") . " - " . __("automation");
                    $sidebar_template = "settings.sidebar.php";
                    require_once "views/settings.automation.php";
                    break;
                case "services":
                    checkRight(U_SERVICESETTING_SHOW);
                    require_once "class/template.php";
                    $emailtemplatelist = new emailtemplate();
                    $fields = ["Name"];
                    $emailtemplates = $emailtemplatelist->all($fields);
                    $templatelist = new template();
                    $templatelist->Type = "other";
                    $templates_other = $templatelist->all($fields, false, false, "-1", "Type", "other");
                    require_once "class/upgradegroup.php";
                    $upgradegroup = new UpgradeGroup_Model("hosting");
                    $upgradegroups_list = $upgradegroup->all(["id", "Name", "Products", "ServiceType"], "id", "ASC", true);
                    $message = parse_message($settings);
                    $wfh_page_title = __("settings") . " - " . __("services");
                    $sidebar_template = "settings.sidebar.php";
                    require_once "views/settings.services.php";
                    break;
                case "api":
                    require_once "class/apilogfile.php";
                    $apiloglist = new apilogfile();
                    $session = isset($_SESSION["api.show.logfile"]) ? $_SESSION["api.show.logfile"] : [];
                    $sort = isset($session["sort"]) ? $session["sort"] : "DateTime";
                    $order = isset($session["order"]) ? $session["order"] : "DESC";
                    if(isset($_POST["apiLogSearch"])) {
                        $limit = 1;
                        $apiloglist->SearchString = $searchfor;
                    } else {
                        $limit = isset($_POST["ajaxPage"]) && is_numeric($_POST["ajaxPage"]) ? $_POST["ajaxPage"] : (isset($session["limit"]) ? $session["limit"] : "1");
                    }
                    $show_results = isset($_POST["ajaxResultsPerPage"]) && is_numeric($_POST["ajaxResultsPerPage"]) && 0 < $_POST["ajaxResultsPerPage"] ? $_POST["ajaxResultsPerPage"] : MAX_RESULTS_LIST;
                    $searchat = "Input|Response|Action|Controller";
                    $searchfor = isset($session["searchfor"]) ? $session["searchfor"] : false;
                    $fields = ["DateTime", "Controller", "Action", "Input", "ResponseType", "Response", "IP"];
                    $selectgroup = isset($session["responseType"]) ? $session["responseType"] : "false";
                    $list_apilog = $apiloglist->all($fields, $sort, $order, $limit, $searchat, $searchfor, $selectgroup, $show_results);
                    $_SESSION["api.show.logfile"] = ["searchfor" => $searchfor, "sort" => $sort, "order" => $order, "responseType" => $selectgroup, "results" => $show_results, "limit" => $limit];
                    $current_page = $limit;
                    $message = parse_message();
                    $countData = $apiloglist->countAll();
                    if(API_ACTIVE != "yes" && 0 < $countData["CountRows"]) {
                        $showLogRows = true;
                    } elseif(API_ACTIVE != "yes" && (int) $countData["CountRows"] === 0) {
                        $showLogRows = false;
                    } elseif(in_array(API_LOG_TYPE, ["all", "error"])) {
                        $showLogRows = true;
                    } elseif(API_LOG_TYPE == "none" && 0 < $countData["CountRows"]) {
                        $showLogRows = true;
                    } else {
                        $showLogRows = false;
                    }
                    $wfh_page_title = __("settings") . " - " . __("api");
                    $sidebar_template = "settings.sidebar.php";
                    require_once "views/settings.api.php";
                    break;
                case "tickets":
                    require_once "class/template.php";
                    $emailtemplatelist = new emailtemplate();
                    $fields = ["Name"];
                    $emailtemplates = $emailtemplatelist->all($fields);
                    $message = parse_message($settings);
                    $wfh_page_title = __("settings") . " - " . __("ticketsystem");
                    $sidebar_template = "settings.sidebar.php";
                    require_once "views/settings.tickets.php";
                    break;
                default:
                    if($page == "general") {
                        $taxchanger_counters = $settings->getTaxChanger();
                        $taxrules = $settings->getTaxRules();
                        require_once "class/employee.php";
                        $employee = new employee();
                        $fields = ["Name"];
                        $employees = $employee->all($fields);
                    }
                    $message = parse_message($settings);
                    $wfh_page_title = __("settings") . " - " . __("general");
                    $sidebar_template = "settings.sidebar.php";
                    require_once "views/settings.general.php";
            }
        }
}

?>