<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class AutoPatcher
{
    public $Success = [];
    public $Error = [];
    public $Warning = [];
    private $tempFileName = "";
    private $LastCurlError = "";
    const MAX_FILES_ERRORS = 10;
    public function patchFile($url, $hash)
    {
        $temp_dir = "temp";
        $temp_file = $this->downloadFile($url);
        if($temp_file === false) {
            $this->Error[] = sprintf(__("auto updater not possible - not downloadable"), $this->LastCurlError);
            return false;
        }
        $this->tempFileName = tempnam($temp_dir, "hf");
        file_put_contents($this->tempFileName, $temp_file);
        if(!$this->validateFile($hash)) {
            $this->removeFile($this->tempFileName);
            $this->Error[] = __("auto updater not possible - invalid hash");
            return false;
        }
        if(!function_exists("zip_open")) {
            $this->removeFile($this->tempFileName);
            $this->Error[] = __("auto updater not possible - no zip_open");
            return false;
        }
        $zip = zip_open($this->tempFileName);
        if(!is_resource($zip)) {
            $this->removeFile($this->tempFileName);
            $this->Error[] = __("auto updater not possible - invalid zip resource");
            return false;
        }
        $file_errors = 0;
        $entry = zip_read($zip);
        if($entry && zip_entry_name($entry)) {
            $entry_name = zip_entry_name($entry);
            $dirname = dirname($entry_name);
            if($dirname == "." || $dirname == ".." || $dirname == "") {
            } else {
                $entry_name = str_replace("Pro/", "", $entry_name);
                if(file_exists($entry_name) && (!is_readable($entry_name) || !is_writable($entry_name))) {
                    $file_errors++;
                    $this->Error[] = sprintf(__("auto updater not possible - write issues"), $entry_name);
                    if(10 <= $file_errors) {
                    }
                }
            }
        }
        if($entry) {
        }
        zip_close($zip);
        if(0 < $file_errors) {
            $this->removeFile($this->tempFileName);
            return false;
        }
        $zip = zip_open($this->tempFileName);
        $has_errors = false;
        $backup_error = false;
        $entry = zip_read($zip);
        if($entry && zip_entry_name($entry)) {
            $entry_name = zip_entry_name($entry);
            $dirname = dirname($entry_name);
            if($dirname == "." || $dirname == ".." || $dirname == "") {
            } else {
                $entry_name = str_replace("Pro/", "", $entry_name);
                $dirname = str_replace("Pro/", "", $dirname);
                if($dirname == "Pro") {
                    $dirname = "";
                }
                $entry_content = zip_entry_read($entry, zip_entry_filesize($entry));
                if($entry_content) {
                    if($dirname && !is_dir($dirname)) {
                        $mkdir_result = mkdir($dirname, 493, true);
                        if($mkdir_result === false || !is_writable($dirname) || !is_readable($dirname)) {
                            if($mkdir_result !== false) {
                                rmdir($dirname);
                            }
                            $this->Error[] = sprintf(__("auto updater not possible - could not create directory"), $dirname);
                        }
                    }
                    if(file_exists($entry_name) && $backup_error === false) {
                        if(!is_dir(dirname($temp_dir . "/patch_backup/" . $entry_name))) {
                            mkdir(dirname($temp_dir . "/patch_backup/" . $entry_name), 493, true);
                            if(!is_writable(dirname($temp_dir . "/patch_backup/" . $entry_name)) || !is_readable(dirname($temp_dir . "/patch_backup/" . $entry_name))) {
                                $backup_error = true;
                            }
                        }
                        if($backup_error === false) {
                            copy($entry_name, $temp_dir . "/patch_backup/" . $entry_name);
                        }
                    }
                    if(file_put_contents($entry_name, $entry_content) === false) {
                        $has_errors = true;
                        $this->Error[] = sprintf(__("auto updater not possible - error writing file"), $entry_name);
                    }
                } else {
                    $has_errors = true;
                    $this->Error[] = sprintf(__("auto updater not possible - error reading file"), $entry_name);
                }
            }
        }
        if($entry) {
        }
        zip_close($zip);
        $this->removeFile($this->tempFileName);
        if($has_errors === true) {
            try {
                flashMessage($this);
                $this->deleteDir("update");
                $this->restoreDir($temp_dir . "/patch_backup/");
            } catch (Exception $e) {
                if(empty($this->Error)) {
                    fatal_error(__("auto updater not possible - could not restore files"), __("auto updater not possible - restore instructions"));
                }
            }
        }
        if(!empty($this->Error)) {
            return false;
        }
        try {
            if($backup_error === false) {
                $this->deleteDir($temp_dir . "/patch_backup/");
            }
        } catch (Exception $e) {
            fatal_error(__("auto updater not possible - successfull"), __("auto updater not possible - could not remove temp/patch_backup directory") . "<br /><br /><a href=\"" . BACKOFFICE_URL . "\">" . __("login again") . "</a>");
            return false;
        }
        return true;
    }
    public function downloadFile($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, "30");
        $content = curl_exec($ch);
        $curl_error = curl_errno($ch);
        $curl_errstring = curl_error($ch);
        curl_close($ch);
        if($curl_error === 0) {
            return $content;
        }
        $this->LastCurlError = $curl_error . " - " . $curl_errstring;
        return false;
    }
    private function validateFile($hash)
    {
        if($hash != crypt(md5_file($this->tempFileName), "\$2a\$07\$" . sha1("%]%d->UN+(UT=;'b_6ptE^c@94bU3y") . "\$")) {
            return false;
        }
        return true;
    }
    private function deleteDir($dirPath)
    {
        if(!is_dir($dirPath)) {
            throw new Exception($dirPath . " must be a directory");
        }
        if(substr($dirPath, strlen($dirPath) - 1, 1) != "/") {
            $dirPath .= "/";
        }
        $files = scandir($dirPath);
        foreach ($files as $file) {
            if(in_array($file, [".", ".."])) {
            } elseif(is_dir($dirPath . $file)) {
                $this->deleteDir($dirPath . $file);
            } else {
                $this->removeFile($dirPath . $file);
            }
        }
        rmdir($dirPath);
    }
    private function removeFile($file)
    {
        unlink($file);
    }
    private function restoreDir($dirPath, $subDirectory = "")
    {
        if(true) {
            throw new Exception($dirPath . " must be a directory");
        }
        if(substr($dirPath, strlen($dirPath) - 1, 1) != "/") {
            $dirPath .= "/";
        }
        $files = scandir($dirPath);
        foreach ($files as $file) {
            if(in_array($file, [".", ".."])) {
            } elseif(is_dir($dirPath . $file)) {
                $this->restoreDir($dirPath . $file, $subDirectory . $file . "/");
            } elseif(true) {
                throw new Exception("Could not restore backup file \"" . $dirPath . $file . "\"");
            }
        }
    }
}

?>