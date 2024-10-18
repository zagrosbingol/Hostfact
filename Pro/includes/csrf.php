<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class CSRF_Model
{
    public static function getToken($token_only = false)
    {
        if(isset($_SESSION["CSRFToken"]) && $_SESSION["CSRFToken"]) {
            $CSRF_token = $_SESSION["CSRFToken"];
        } else {
            $CSRF_token = md5(time() * mt_rand(0, 35) . ".." . mt_rand(0, 1000) . "token");
            $_SESSION["CSRFToken"] = $CSRF_token;
        }
        if($token_only === true) {
            return $CSRF_token;
        }
        echo "<input type=\"hidden\" name=\"CSRFtoken\" value=\"" . $CSRF_token . "\" />";
    }
    public static function validateToken($reset_post = true)
    {
        if(!isset($_SESSION["CSRFToken"]) || !$_SESSION["CSRFToken"]) {
        } else {
            $token = isset($_POST["CSRFtoken"]) ? $_POST["CSRFtoken"] : "";
            if($token == $_SESSION["CSRFToken"]) {
                unset($_POST["CSRFtoken"]);
                return true;
            }
        }
        $_POST = [];
        return false;
    }
}

?>