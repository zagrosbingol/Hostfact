<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(!function_exists("json_encode")) {
    fatal_error("Your server has an outdated PHP version.", "Please contact your server administrator to update your PHP version (minimal version 5.3).");
}
$server_addr = isset($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_ADDR"] : (isset($_SERVER["LOCAL_ADDR"]) ? $_SERVER["LOCAL_ADDR"] : "");
define("cthlvS4DqTs2", md5($server_addr));
if(!defined("InStAlLHosTFacT") && @is_dir("install")) {
    header("Location: install/");
    exit;
}
if(!defined("InStAlLHosTFacT") && @is_dir("update")) {
    if(is_readable("update") && file_exists("update/index.php")) {
        header("Location: update/");
        exit;
    }
    $_SESSION["flashMessage"] = [];
    fatal_error("Er is een fout opgetreden tijdens het wegschrijven van de update.", "Ververs de pagina en probeer de patch opnieuw uit te voeren. Mocht het probleem blijven bestaan, verwijder dan de map \"Pro/update\" handmatig om weer toegang tot HostFact te verkrijgen. Daarna kan de update gedownload worden via het klantenpaneel op onze website.");
}
if(file_exists("class/database.php")) {
    require_once "class/database.php";
} else {
    fatal_error("A file seems to be missing", "File class/database.php is missing.");
}
if(!defined("InStAlLHosTFacT") || !InStAlLHosTFacT) {
    if(isset($_POST["UserName"])) {
        $valid = checkLicense();
        if(isset($valid[3]) && strlen($valid[3]) == 32) {
        } else {
            $tmp_valid = $valid;
            $valid = [];
            foreach ($tmp_valid as $index_valid => $value_valid) {
                if($index_valid == 3) {
                    $valid[] = "";
                }
                $valid[] = $value_valid;
            }
        }
        if($valid[1] != "OK" || $valid[2] != encrypt($server_addr) && $valid[2] != "free4all") {
            if(!defined("LICENSE")) {
                $tablename_lowercase_check = Database_Model::getInstance()->getOne("hostfact_settings", ["Value"])->where("Variable", "LICENSE")->execute();
                if($tablename_lowercase_check && isset($tablename_lowercase_check->Value)) {
                    fatal_error(__("database tables are lowercase"), __("database tables are lowercase description"));
                }
            }
            if(isset($valid[5])) {
                echo $valid[5];
                exit;
            }
            if(isset($valid[4])) {
                echo $valid[4];
                exit;
            }
            header("Location: install/");
            exit;
        }
        if($valid[4] && str_replace("-", "", $valid[4]) != LICENSE_UPDATE_EXPIRE_DATE) {
            Database_Model::getInstance()->update("HostFact_Settings", ["Value" => str_replace("-", "", $valid[3])])->where("Variable", "LICENSE_UPDATE_EXPIRE_DATE")->execute();
        }
        if(cthlvS4DqTs2 == md5($server_addr) && !defined("sHN/mDPxc.ag2")) {
            define("sHN/mDPxc.ag2", passcrypt(LICENSE));
        }
        if(isset($valid[6]) && substr($valid[6], 0, 10) == "wfmessage=") {
            $_SESSION["header_message"] = substr($valid[6], 10);
        }
        if(isset($valid[3])) {
            $license_hash = md5(sha1(LICENSE . $valid[3]));
            Database_Model::getInstance()->update("HostFact_Settings", ["Value" => $license_hash])->where("Variable", "LICENSE_HASH")->execute();
        }
    } elseif(!defined("sHN/mDPxc.ag2")) {
        define("sHN/mDPxc.ag2", passcrypt(LICENSE));
    }
} elseif(defined("InStAlLHosTFacT") && InStAlLHosTFacT == "Ra.33sUdhWlkd22" && defined("LICENSE")) {
    define("sHN/mDPxc.ag2", passcrypt(LICENSE));
}
if(!isset($_SESSION["UserPro"]) && !defined("LOGINSYSTEM") && isset($_COOKIE["WFUN"])) {
    if(file_exists("class/employee.php")) {
        require_once "class/employee.php";
    } elseif(file_exists("employee.php")) {
        require_once "employee.php";
    } else {
        fatal_error("A file seems to be missing", "File class/employee.php is missing.");
    }
    $account = new employee();
    $account->UserName = passcrypt(esc($_COOKIE["WFUN"]));
    $account->Password = esc($_COOKIE["WFPWD"]);
    $account->search("already_encrypted");
    if(0 < $account->Identifier && md5($account->Identifier) == $_COOKIE["WFU"]) {
        session_regenerate_id(true);
        $_SESSION["UserPro"] = $account->Identifier;
        $_SESSION["UserNamePro"] = $account->UserName;
        $_SESSION["PasswordPro"] = $account->Password;
        $_SESSION["LastDate"] = $account->LastDate;
        $_SESSION["LoggedIn"] = true;
        $account->updateLastDate($account->Identifier, date("YmdHis"));
    } else {
        require_once "includes/functions.php";
        logFailedLoginAttempt("backoffice", $_SERVER["REMOTE_ADDR"], $account->UserName);
    }
}
if(isset($_SESSION["UserPro"]) && isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] === true) {
    if(file_exists("class/employee.php")) {
        require_once "class/employee.php";
    } elseif(file_exists("employee.php")) {
        require_once "employee.php";
    } else {
        fatal_error("A file seems to be missing", "File class/employee.php is missing.");
    }
    $account = isset($account) ? $account : new employee();
    $account->Identifier = $_SESSION["UserPro"];
    $account->show();
    if(!(isset($account) && isset($_SESSION["UserNamePro"]) && isset($_SESSION["PasswordPro"]) && $account->UserName == $_SESSION["UserNamePro"] && $account->Password == $_SESSION["PasswordPro"])) {
        $_SESSION["UserPro"] = "";
        $_SESSION["UserNamePro"] = "";
        $_SESSION["PasswordPro"] = "";
        $_SESSION["LastDate"] = "";
        session_unset();
        @session_destroy();
        if(!defined("INDEXPAGE")) {
            header("Location: index.php");
            exit;
        }
        header("Location: login.php");
        exit;
    }
    $ipv4_or_v6 = filter_var($_SERVER["REMOTE_ADDR"], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? "IPv6" : "IPv4";
    $tmp_ip = $ipv4_or_v6 == "IPv4" ? $_SERVER["REMOTE_ADDR"] : implode(":", array_slice(explode(":", $_SERVER["REMOTE_ADDR"]), 0, 4));
    if(!isset($_SESSION["CurrentSession" . $ipv4_or_v6])) {
        $_SESSION["CurrentSession" . $ipv4_or_v6] = md5($_SESSION["UserNamePro"] . $_SESSION["PasswordPro"] . $tmp_ip);
    } elseif(isset($_SESSION["CurrentSession" . $ipv4_or_v6]) && $_SESSION["CurrentSession" . $ipv4_or_v6] != md5($_SESSION["UserNamePro"] . $_SESSION["PasswordPro"] . $tmp_ip)) {
        session_unset();
        session_destroy();
        $suffix = defined("DB_NAME") ? substr(md5(DB_NAME), 0, 8) : "";
        $sessionPrefix = defined("SESSION_PREFIX") ? SESSION_PREFIX : "hfb";
        session_name($sessionPrefix . $suffix);
        $current_session_params = session_get_cookie_params();
        $http_only = true;
        !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" or $secure_flag = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" || $_SERVER["SERVER_PORT"] == 443;
        session_set_cookie_params($current_session_params["lifetime"], $current_session_params["path"], $current_session_params["domain"], $secure_flag, $http_only);
        session_start();
        $_SESSION["errormsg"] = __("ip conflict in session");
        header("Location: login.php");
        exit;
    }
    unset($tmp_ip);
    if(defined("LOGIN_COOKIE") && LOGIN_COOKIE == "yes") {
        $cookieExpirationHours = defined("COOKIE_EXPIRATION_TIME") ? (int) COOKIE_EXPIRATION_TIME : 1;
        $cookieExpirationTime = time() + 3600 * $cookieExpirationHours;
        setcookie("WFU", md5($account->Identifier), $cookieExpirationTime);
        setcookie("WFUN", passcrypt($account->UserName), $cookieExpirationTime);
        setcookie("WFPWD", wf_password_hash($account->Password . $_SERVER["REMOTE_ADDR"] . "-" . $cookieExpirationTime), $cookieExpirationTime);
        setcookie("CookieExpiration", $cookieExpirationTime, $cookieExpirationTime);
    }
    $account->showRights($account->Identifier, true);
    $account->showPreferences($account->Identifier, true);
    $fields = ["Name"];
    $accounts = $account->all($fields);
    if(file_exists("class/company.php")) {
        require_once "class/company.php";
    } else {
        fatal_error("A file seems to be missing", "File class/company.php is missing.");
    }
    $company = isset($company) ? $company : new company();
    $company->show();
} elseif(defined("LOGINSYSTEM") && LOGINSYSTEM) {
} elseif(!defined("InStAlLHosTFacT") || !InStAlLHosTFacT) {
    if(isset($_SESSION["RefererURL"])) {
        $refurl = $_SESSION["RefererURL"];
    }
    $_SESSION["UserPro"] = "";
    $_SESSION["UserNamePro"] = "";
    $_SESSION["PasswordPro"] = "";
    $_SESSION["LastDate"] = "";
    session_unset();
    @session_destroy();
    $suffix = defined("DB_NAME") ? substr(md5(DB_NAME), 0, 8) : "";
    $sessionPrefix = defined("SESSION_PREFIX") ? SESSION_PREFIX : "hfb";
    session_name($sessionPrefix . $suffix);
    $current_session_params = session_get_cookie_params();
    $http_only = true;
    !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" or $secure_flag = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" || $_SERVER["SERVER_PORT"] == 443;
    session_set_cookie_params($current_session_params["lifetime"], $current_session_params["path"], $current_session_params["domain"], $secure_flag, $http_only);
    session_start();
    if(isset($refurl)) {
        $_SESSION["RefererURL"] = $refurl;
    } else {
        $_SESSION["RefererURL"] = $_SERVER["REQUEST_URI"];
    }
    if(strpos($_SESSION["RefererURL"], "autocomplete") !== false) {
        unset($_SESSION["RefererURL"]);
        echo json_encode([["error" => "logged out"]]);
        exit;
    }
    if(strpos($_SESSION["RefererURL"], "XMLRequest.php") !== false) {
        unset($_SESSION["RefererURL"]);
        echo json_encode(["ajaxResponse" => "logout"], JSON_THROW_ON_ERROR);
        exit;
    }
    header("Location: login.php");
    exit;
}
function get_hostfact_plan()
{
    if(defined("INT_WF_ACTIVE_DEBTOR_LIMIT")) {
        exit;
    }
    if(defined("LICENSE")) {
        $result = Database_Model::getInstance()->getOne("HostFact_Settings", ["Value"])->where("Variable", "LICENSE_HASH")->execute();
        if($result) {
            switch ($result->Value) {
                case md5(sha1(LICENSE . "c77202661f99f41ffa6adf6605edb379")):
                case md5(sha1(LICENSE)):
                    define("INT_WF_ACTIVE_DEBTOR_LIMIT", 0);
                    return true;
                    break;
                case md5(sha1(LICENSE . "e985b99f70237b921a57f70538424c71")):
                    define("INT_WF_ACTIVE_DEBTOR_LIMIT", 2000);
                    return true;
                    break;
            }
        }
    }
    define("INT_WF_ACTIVE_DEBTOR_LIMIT", 500);
    return true;
}
function checkLicense($type = "license", $license = LICENSE)
{
    global $server_addr;
    $url = INTERFACE_URL . "/hosting/infofile.php?action=" . urlencode($type);
    $url .= "&license=" . urlencode(encrypt($license));
    $url .= "&licensemd5=" . md5(trim($license));
    $url .= "&version=" . urlencode(SOFTWARE_VERSION);
    $url .= "&server=" . urlencode(encrypt($server_addr));
    $url .= "&ip=" . urlencode($server_addr);
    $result = getContent($url, "license");
    if(substr($result, 0, 8) != "HOSTFACT") {
        $result = "STANDALONE|OK|" . encrypt($server_addr) . "|";
    }
    $result = explode("|", $result);
    return $result;
}
function getContent($url, $type = false)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    settings::disableSSLVerificationIfNeeded($ch);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, "10");
    $content = curl_exec($ch);
    if($type == "license" && !$content && in_array(curl_errno($ch), ["35"])) {
        $error_message = curl_error($ch) . " [" . curl_errno($ch) . "]";
        curl_setopt($ch, CURLOPT_URL, str_replace("https://", "http://", $url));
        curl_exec($ch);
        $last_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if(0 < $last_http_code && in_array($last_http_code, ["301", "302"])) {
            fatal_error("cURL error during license check", $error_message);
        }
    }
    curl_close($ch);
    return $content;
}
function checkVersion($downloadLink = false)
{
    if(!isset($_SESSION["versionChecker"])) {
        $url = INTERFACE_URL . "/hosting/infofile.php?action=version_info" . (function_exists("phpversion") ? "&php=" . phpversion() : "") . "&version=" . SOFTWARE_VERSION;
        $result = getcontent($url);
        $_SESSION["versionChecker"] = $result;
    } else {
        $result = $_SESSION["versionChecker"];
    }
    $result = json_decode($result);
    if(!isset($result->result) || $result->result != "OK") {
        return "";
    }
    if($downloadLink === true) {
        $ioncube_version = "";
        if(function_exists("ioncube_loader_version")) {
            $ioncube_version = ioncube_loader_version();
            if(substr_count($ioncube_version, ".") == 1) {
                $ioncube_version .= ".0";
            }
        }
        $link = "<a href=\"" . INTERFACE_URL_CUSTOMERPANEL . "/download.php?license=" . urlencode(LICENSE) . "&amp;from=" . SOFTWARE_VERSION . "&amp;link=newversion" . ($ioncube_version ? "&amp;ioncube=" . ioncube_loader_version() : "") . "\" target=\"_blank\">" . sprintf(__("new version available"), $result->version) . "</a>";
        $_version_dialog = false;
        if(version_compare($result->php_version, PHP_VERSION, ">")) {
            $_version_dialog = "<strong>" . sprintf(__("software version check php title"), htmlspecialchars($result->php_version)) . "</strong><br />" . sprintf(__("software version check php"), htmlspecialchars(PHP_VERSION), htmlspecialchars($result->php_version)) . "<br /><br />";
        }
        if($ioncube_version && version_compare($result->ioncube_loader, $ioncube_version, ">")) {
            $_version_dialog .= "<strong>" . sprintf(__("software version check ioncube title"), htmlspecialchars($result->ioncube_loader)) . "</strong><br />" . sprintf(__("software version check ioncube"), htmlspecialchars($ioncube_version), htmlspecialchars($result->ioncube_loader)) . "<br /><br />";
        }
        if($_version_dialog !== false) {
            $link = "<a onclick=\"\$('#dialog_version').dialog('open');\">" . sprintf(__("new version available"), $result->version) . "</a>";
            $link .= "<div id=\"dialog_version\" class=\"hide\" title=\"" . sprintf(__("new version available"), $result->version) . "\"><p>" . __("software version check intro") . "<br /><br />" . $_version_dialog . "</p><br /><p><a class=\"a1 c1 float_right\" onclick=\"\$('#dialog_version').dialog('close');\"><span>" . __("cancel") . "</span></a></p></div>\n\t\t \t\t\t\t<script type=\"text/javascript\">\n\t\t \t\t\t\t\$(document).ready(function(){\n \t\t\t\t\t\t\t\$('#dialog_version').dialog({modal: true, autoOpen: false, resizable: false, width: 600, height: 'auto'});\n\t\t\t\t\t\t});\n\t\t\t\t\t\t</script>";
        }
        if(isset($result->patchhash) && $result->patchhash) {
            $version_current = explode(".", SOFTWARE_VERSION);
            $version_new = explode(".", $result->version);
            if($version_current[0] == $version_new[0] && version_compare(SOFTWARE_VERSION, $result->version, "<")) {
                echo __("auto updater patch available") . " <a href=\"index.php?action=download_patch&version=" . htmlspecialchars($result->version) . "&filehash=" . htmlspecialchars($result->patchhash) . "\" onclick=\"\$(this).next().removeClass('hide');\$(this).remove();\" style=\"text-decoration:underline;\">" . __("auto updater patch available - download link") . "</a>";
                echo "<span class=\"button_loader_span hide\" style=\"line-height:32px\">\n            <img src=\"images/icon_circle_loader_green.gif\" alt=\"\" class=\"ico inline\" style=\"margin-bottom:-3px;\" />&nbsp;&nbsp;\n            <span class=\"loading_green\">" . __("loading") . "</span>\n        </span>";
                return NULL;
            }
        }
        echo $link;
    } else {
        return version_compare(SOFTWARE_VERSION, $result->version, "<");
    }
}
function is_email($email)
{
    if(1 < substr_count($email, "@")) {
        return false;
    }
    if(preg_match("/^[^\\\\ \\/@<>.]+(\\.[^\\\\ \\/@<>.]+)*@[^\\\\ \\/@<>.]+(\\.[^\\\\ \\/@<>.]+)*(\\.[^\\\\ \\/@<>.]{2,63})\$/u", strtolower(trim(rtrim($email)))) == 0) {
        return false;
    }
    return true;
}
function is_date($date)
{
    $date = str_replace(":", "", str_replace("/", "", str_replace(" ", "", str_replace("-", "", $date))));
    $year = substr($date, 0, 4);
    $month = substr($date, 4, 2);
    $day = substr($date, 6, 2);
    return checkdate(intval($month), intval($day), intval($year));
}
function rewrite_date_db2site($datum, $format = false)
{
    $datum = str_replace("-", "", $datum);
    $datum = str_replace("/", "", $datum);
    $datum = str_replace(":", "", $datum);
    $datum = str_replace(" ", "", $datum);
    $dag = "00";
    $maand = "00";
    $jaar = "0000";
    $uur = "00";
    $minuut = "00";
    $seconde = "00";
    $dag = substr($datum, 6, 2);
    $maand = substr($datum, 4, 2);
    $jaar = substr($datum, 0, 4);
    $jaar2 = substr($jaar, 2, 2);
    $uur = substr($datum, 8, 2);
    $minuut = substr($datum, 10, 2);
    $seconde = substr($datum, 12, 2);
    if(!$format) {
        $datum = DATE_FORMAT;
    } else {
        $datum = $format;
    }
    $datum = str_replace("%d", $dag, $datum);
    $datum = str_replace("%m", $maand, $datum);
    $datum = str_replace("%Y", $jaar, $datum);
    $datum = str_replace("%y", $jaar2, $datum);
    $datum = str_replace("%H", $uur, $datum);
    $datum = str_replace("%i", $minuut, $datum);
    $datum = str_replace("%s", $seconde, $datum);
    if(intval(str_replace(":", "", str_replace("/", "", str_replace("-", "", $datum)))) === 0 && $datum !== "00:00") {
        return "";
    }
    return $datum;
}
function rewrite_date_site2db($datum, $format = false)
{
    $dag = "00";
    $maand = "00";
    $jaar = "0000";
    $uur = "00";
    $minuut = "00";
    $seconde = "00";
    if(!$format) {
        $format = str_replace("%", "", DATE_FORMAT);
    } else {
        $format = str_replace("%", "", $format);
    }
    $s = 0;
    $i = 0;
    while ($i < strlen($format)) {
        $sign = substr($format, $i, 1);
        switch ($sign) {
            case "j":
                $dag = substr($datum, $s, 2);
                if(is_numeric($dag)) {
                    $s += 2;
                } else {
                    $dag = substr($datum, $s, 1);
                    $s += 1;
                }
                break;
            case "d":
                $dag = substr($datum, $s, 2);
                $s += 2;
                break;
            case "m":
                $maand = substr($datum, $s, 2);
                $s += 2;
                break;
            case "Y":
                $jaar = substr($datum, $s, 4);
                $s += 4;
                break;
            case "y":
                $jaar = 50 < substr($datum, $s, 2) ? "19" . substr($datum, $s, 2) : "20" . substr($datum, $s, 2);
                $s += 2;
                break;
            case "H":
                $uur = substr($datum, $s, 2);
                $s += 2;
                break;
            case "i":
                $minuut = substr($datum, $s, 2);
                $s += 2;
                break;
            case "s":
                $seconde = substr($datum, $s, 2);
                $s += 2;
                break;
            default:
                $s += 1;
                $i++;
        }
    }
    $jaar = $jaar ? $jaar : "0000";
    $maand = $maand ? $maand : "00";
    $dag = $dag ? $dag : "00";
    $uur = $uur ? $uur : "00";
    $minuut = $minuut ? $minuut : "00";
    $seconde = $seconde ? $seconde : "00";
    $datum2 = $jaar . $maand . $dag . $uur . $minuut . $seconde;
    if(is_date($datum2)) {
        return $datum2;
    }
    if(is_date($datum)) {
        return str_replace(" ", "", str_replace(":", "", str_replace("/", "", str_replace("-", "", $datum))));
    }
    return false;
}
function calculate_date($start_date, $periods, $periodic)
{
    if(!filter_var($periods, FILTER_VALIDATE_INT) || !is_date($start_date) || !in_array($periodic, ["d", "w", "m", "k", "h", "j", "t"])) {
        return false;
    }
    switch ($periodic) {
        case "d":
            $interval = "+" . $periods . " day";
            break;
        case "w":
            $interval = "+" . $periods . " week";
            break;
        case "m":
            $interval = "+" . $periods . " month";
            break;
        case "k":
            $interval = "+" . 3 * $periods . " month";
            break;
        case "h":
            $interval = "+" . 6 * $periods . " month";
            break;
        case "j":
            $interval = "+" . $periods . " year";
            break;
        case "t":
            $interval = "+" . 2 * $periods . " year";
            break;
        default:
            return date("Y-m-d", strtotime($interval, strtotime($start_date)));
    }
}
function quarter($period = "current")
{
    $current_quarter = ceil(date("n") / 4);
    if($period == "previous") {
        if($current_quarter == 1) {
            return 4;
        }
        return $current_quarter - 1;
    }
    return $current_quarter;
}
function week2date($year, $week, $day = 1)
{
    $string = $year . "W" . sprintf("%02d", $week) . $day;
    return strtotime($string);
}
function money($amount, $sign = true, $max_decimals = AMOUNT_DEC_PLACES)
{
    if(is_numeric($amount)) {
        if($max_decimals === false) {
            if(strrpos($amount, ".") === false) {
                $max_decimals = AMOUNT_DEC_PLACES;
            } else {
                $max_decimals = max(AMOUNT_DEC_PLACES, strlen($amount) - strrpos($amount, ".") - 1);
            }
        }
        $amount = 0 <= $amount ? $amount + 0 : $amount - 0;
        $amount = number_format($amount, $max_decimals, AMOUNT_DEC_SEPERATOR, AMOUNT_THOU_SEPERATOR);
        if($sign === false) {
            return $amount;
        }
        return (CURRENCY_SIGN_LEFT ? CURRENCY_SIGN_LEFT . " " : "") . $amount . (CURRENCY_SIGN_RIGHT ? " " . CURRENCY_SIGN_RIGHT : "");
    }
    return $amount;
}
function currency_sign_td($sign, $is_table = true)
{
    return $sign ? $sign : ($is_table ? "&nbsp;" : "");
}
function showNumber($number, $decimals = false, $show_thousand_seperator = true)
{
    if($decimals === false) {
        if(strrpos($number, ".") === false) {
            $decimals = 0;
        } else {
            $decimals = strlen($number) - strrpos($number, ".") - 1;
        }
    }
    if(!is_numeric($number)) {
        $number = 0;
    }
    return number_format($number, $decimals, AMOUNT_DEC_SEPERATOR, $show_thousand_seperator ? AMOUNT_THOU_SEPERATOR : "");
}
function deformat_money($amount)
{
    $amount = str_replace([CURRENCY_SIGN_LEFT, CURRENCY_SIGN_RIGHT, " "], "", $amount);
    if(strrpos($amount, AMOUNT_THOU_SEPERATOR) !== false && strrpos($amount, AMOUNT_DEC_SEPERATOR) !== false && strrpos($amount, AMOUNT_THOU_SEPERATOR) < strrpos($amount, AMOUNT_DEC_SEPERATOR)) {
        $amount = str_replace(AMOUNT_THOU_SEPERATOR, "", $amount);
        $amount = str_replace(AMOUNT_DEC_SEPERATOR, ".", $amount);
    } elseif(strrpos($amount, AMOUNT_THOU_SEPERATOR) !== false && strrpos($amount, AMOUNT_DEC_SEPERATOR) !== false && strrpos($amount, AMOUNT_DEC_SEPERATOR) < strrpos($amount, AMOUNT_THOU_SEPERATOR)) {
        $amount = str_replace(AMOUNT_DEC_SEPERATOR, "", $amount);
        $amount = str_replace(AMOUNT_THOU_SEPERATOR, ".", $amount);
    } elseif(strrpos($amount, AMOUNT_THOU_SEPERATOR) === false && strrpos($amount, AMOUNT_DEC_SEPERATOR) !== false) {
        $amount = str_replace(AMOUNT_DEC_SEPERATOR, ".", $amount);
    } else {
        if(AMOUNT_THOU_SEPERATOR != "." && AMOUNT_THOU_SEPERATOR != ",") {
            $amount = str_replace(AMOUNT_THOU_SEPERATOR, "", $amount);
        } else {
            if(1 < count(explode(AMOUNT_THOU_SEPERATOR, $amount))) {
                $amount = str_replace(AMOUNT_THOU_SEPERATOR, "", substr($amount, 0, strrpos($amount, AMOUNT_THOU_SEPERATOR))) . substr($amount, strrpos($amount, AMOUNT_THOU_SEPERATOR));
            }
            $amount = str_replace(AMOUNT_THOU_SEPERATOR, AMOUNT_DEC_SEPERATOR, $amount);
        }
        $amount = str_replace(AMOUNT_DEC_SEPERATOR, ".", $amount);
    }
    return floatval($amount);
}
function encrypt($string)
{
    $key = "HDUEn4dei3493xdn3493eh";
    $string = crypt($string, $key);
    return $string;
}
function convert_crypto($str, $ky = "")
{
    $ori_str = $str;
    if($ky == "") {
        return $str;
    }
    $ky = str_replace(chr(32), "", $ky);
    if(strlen($ky) < 8) {
        fatal_error("No database connection configured", "Please regenerate the connect.php file to recover your database connection: <a href=\"" . INTERFACE_URL . "/hosting/connect_generator.php\">" . INTERFACE_URL . "/hosting/connect_generator.php</a>");
    }
    $kl = strlen($ky) < 32 ? strlen($ky) : 32;
    $k = [];
    for ($i = 0; $i < $kl; $i++) {
        $k[$i] = ord($ky[$i]) & 31;
    }
    $j = 0;
    for ($i = 0; $i < strlen($str); $i++) {
        $e = ord($str[$i]);
        $str[$i] = $e & 224 ? chr($e ^ $k[$j]) : chr($e);
        $j++;
        $j = $j == $kl ? 0 : $j;
    }
    if(trim($str) != $str || rtrim($str) != $str) {
        $str = "AA" . $ori_str . "AA";
        $j = 0;
        for ($i = 0; $i < strlen($str); $i++) {
            $e = ord($str[$i]);
            $str[$i] = $e & 224 ? chr($e ^ $k[$j]) : chr($e);
            $j++;
            $j = $j == $kl ? 0 : $j;
        }
    }
    if(substr($str, 0, 2) == "AA" && substr($str, -2) == "AA") {
        $str = substr($str, 2, -2);
    }
    return $str;
}
function passcrypt($string)
{
    $string = htmlspecialchars_decode($string);
    $key = substr(md5("HUDE*#dedh38" . LICENSE), 0, 16);
    $string = convert_crypto($string, $key);
    $string = htmlspecialchars_decode($string);
    return $string;
}
function parsePrefixVariables($prefix)
{
    $prefix = str_replace("[yyyy]", date("Y"), $prefix);
    $prefix = str_replace("[yy]", date("y"), $prefix);
    $prefix = str_replace("[mm]", date("m"), $prefix);
    $prefix = str_replace(["[jaar]", "[year]", "[jaartal]"], date("Y"), $prefix);
    $prefix = str_replace(["[maand]", "[month]"], date("m"), $prefix);
    return $prefix;
}
function wf_password_hash($password)
{
    if(function_exists("password_hash")) {
        return password_hash($password, PASSWORD_BCRYPT);
    }
    if(function_exists("crypt")) {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $randomString = "";
        for ($i = 0; $i < 22; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        $salt = "\$2a\$10\$" . $randomString;
        return crypt($password, $salt);
    }
    return false;
}
function wf_password_verify($password, $hash)
{
    if(function_exists("password_verify")) {
        return password_verify($password, $hash);
    }
    if(function_exists("crypt")) {
        $verify_hash = crypt($password, substr($hash, 0, 29));
        if($verify_hash == $hash) {
            return true;
        }
        return false;
    }
    return false;
}
function getDaysFromPeriod($start_date, $end_date)
{
    $days = (strtotime($end_date) - strtotime($start_date)) / 60 / 60 / 24;
    return number_format($days, 0, ".", "");
}

?>