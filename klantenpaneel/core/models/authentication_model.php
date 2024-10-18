<?php

class Authentication_Model extends GoogleAuthenticator
{

	function getData()
	{
        // Set API hash.
        if (!empty($_SESSION['Password'])) {
            $data['debtor_username'] = $_SESSION['Username'];
            $data['debtor_password'] = $_SESSION['Password'];
            $data['remote_ip'] = $_SERVER['REMOTE_ADDR'];
            $api_hash = base64_encode(serialize($data));
            HostFactAPI::setAPIHash($api_hash);
        }

		$debtor_model = new Debtor_Model();
		if($debtor_model->show())
		{
			return $debtor_model->TokenData;
		}
		else
		{
			return FALSE;
		}
	}

	function putData($data)
	{
		$debtor_model = new Debtor_Model();
		if($debtor_model->show())
		{
			$debtor_model->updateTokenData($data);
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function getUsers()
	{

	}
}

abstract class GoogleAuthenticator {

	// taken from : https://code.google.com/p/ga4php/

	function __construct($totpskew=1, $hotpskew=10, $hotphuntvalue=200000)
	{
		// the hotpskew is how many tokens forward we look to find the input
		// code the user used
		$this->hotpSkew = $hotpskew;

		// the totpskew value is how many tokens either side of the current
		// token we should check, based on a time skew.
		$this->totpSkew = $totpskew;

		// the hotphuntvalue is what we use to resync tokens.
		// when a user resyncs, we search from 0 to $hutphutvalue to find
		// the two token codes the user has entered - 200000 seems like overkill
		// really as i cant imagine any token out there would ever make it
		// past 200000 token code requests.
		$this->hotpHuntValue = $hotphuntvalue;
	}

	// pure abstract functions that need to be overloaded when
	// creating a sub class
	abstract function getData();
	abstract function putData($data);
	abstract function getUsers();

	// a function to create an empty data structure, filled with some defaults
	function createEmptyData()
	{
		$data["tokenkey"]         = ""; // the token key
		$data["tokentype"]        = "HOTP"; // the token type
		$data["tokentimer"]       = 30; // the token timer (For totp) and not supported by ga yet
		$data["tokencounter"]     = 1; // the token counter for hotp
		$data["tokenalgorithm"]   = "SHA1"; // the token algorithm (not supported by ga yet)
		$data["user"]             = ""; // a place for implementors to store their own data

		return $data;
	}

	// an internal funciton to get data from the overloaded functions
	// and turn them into php arrays.
	function internalGetData($username)
	{
		$data = $this->getData();
		$deco = unserialize(base64_decode($data));

		if(!$deco)
		{
			$deco = $this->createEmptyData();
		}

		return $deco;
	}

	// the function used inside the class to put the data into the
	// datastore using the overloaded data saving class
	function internalPutData($username, $data)
	{
		if($data == "")
		{
			$enco = "";
		}
		else
		{
			$enco = base64_encode(serialize($data));
		}

		return $this->putData($enco);
	}


	// set the token type the user it going to use.
	// this defaults to HOTP - we only do 30s token
	// so lets not be able to set that yet
	function setTokenType($username, $tokentype)
	{
		$tokentype = strtoupper($tokentype);
		if($tokentype != "HOTP" && $tokentype != "TOTP")
		{
			$errorText = "Invalid Token Type";
			return FALSE;
		}

		$data = $this->internalGetData($username);
		$data["tokentype"] = $tokentype;
		return $this->internalPutData($username, $data);
	}


	// create "user" with insert
	function setUser($username, $ttype="HOTP", $key = "", $hexkey="")
	{
		$ttype  = strtoupper($ttype);
		if($ttype != "HOTP" && $ttype !="TOTP")
		{
			return FALSE;
		}

		if($key == "")
		{
			$key = (isset($_SESSION['two_factor_key'])) ? $_SESSION['two_factor_key'] : $this->createBase32Key();

			// Store key in session
			$_SESSION['two_factor_key'] 	= $key;
		}

		$hkey = $this->helperb322hex($key);
		if($hexkey != "")
		{
			$hkey = $hexkey;
		}

		$token = $this->internalGetData($username);
		$token["tokenkey"] = $hkey;
		$token["tokentype"] = $ttype;

		if(!$this->internalPutData($username, $token))
		{
			return FALSE;
		}
		return $key;
	}

	// a function to determine if the user has an actual token
	function hasToken($username)
	{
		$token = $this->internalGetData($username);
		// TODO: change this to a pattern match for an actual key
		if(!isset($token["tokenkey"]))
		{
			return FALSE;
		}
		if($token["tokenkey"] == "")
		{
			return FALSE;
		}

		return TRUE;
	}


	// sets the key for a user - this is assuming you dont want
	// to use one created by the application. returns false
	// if the key is invalid or the user doesn't exist.
	function setUserKey($username, $key)
	{
		// consider scrapping this
		$token = $this->internalGetData($username);
		$token["tokenkey"] = $key;
		$this->internalPutData($username, $token);

		// TODO error checking
		return TRUE;
	}


	// self explanitory?
	function deleteUser($username)
	{
		// oh, we need to figure out how to do thi?
		$this->internalPutData($username, "");
	}

	// user has input their user name and some code, authenticate
	// it
	function authenticateUser($username, $code)
	{

		if(preg_match("/[0-9][0-9][0-9][0-9][0-9][0-9]/",$code)<1)
		{
			return FALSE;
		}

		$tokendata = $this->internalGetData($username);

		//var_dump($tokendata);

		if($tokendata["tokenkey"] == "")
		{
			$errorText = "No Assigned Token";
			return FALSE;
		}

		// TODO: check return value
		$ttype = $tokendata["tokentype"];
		$tlid = $tokendata["tokencounter"];
		$tkey = $tokendata["tokenkey"];

		switch($ttype)
		{
			case "HOTP":
				$st = $tlid+1;
				$en = $tlid+$this->hotpSkew;
				for($i=$st; $i<$en; $i++)
				{
					$stest = $this->oath_hotp($tkey, $i);

					if($code == $stest)
					{
						$tokendata["tokencounter"] = $i;
						$this->internalPutData($username, $tokendata);
						return TRUE;
					}
				}
				return FALSE;
				break;

			case "TOTP":
				$t_now = time();
				$t_ear = $t_now - ($this->totpSkew*$tokendata["tokentimer"]);
				$t_lat = $t_now + ($this->totpSkew*$tokendata["tokentimer"]);
				$t_st = ((int)($t_ear/$tokendata["tokentimer"]));
				$t_en = ((int)($t_lat/$tokendata["tokentimer"]));

				for($i=$t_st; $i<=$t_en; $i++)
				{
					$stest = $this->oath_hotp($tkey, $i);
					if($code == $stest)
					{
						return TRUE;
					}
				}
				break;

			default:
				return FALSE;
		}

		return false;

	}

	// this function allows a user to resync their key. If too
	// many codes are called, we only check up to 20 codes in the future
	// so if the user is at 21, they'll always fail.
	function resyncCode($username, $code1, $code2)
	{
		// here we'll go from 0 all the way thru to 200k.. if we cant find the code, so be it, they'll need a new one
		// for HOTP tokens we start at x and go to x+20

		// for TOTP we go +/-1min TODO = remember that +/- 1min should
		// be changed based on stepping if we change the expiration time
		// for keys

		$tokendata = internalGetData($username);

		// TODO: check return value
		$ttype = $tokendata["tokentype"];
		$tlid = $tokendata["tokencounter"];
		$tkey = $tokendata["tokenkey"];

		if($tkey == "")
		{
			$this->errorText = "No Assigned Token";
			return FALSE;
		}

		switch($ttype)
		{
			case "HOTP":
				$st = 0;
				$en = $this->hotpHuntValue;
				for($i=$st; $i<$en; $i++)
				{
					$stest = $this->oath_hotp($tkey, $i);

					if($code1 == $stest)
					{
						$stest2 = $this->oath_hotp($tkey, $i+1);
						if($code2 == $stest2)
						{
							$tokendata["tokencounter"] = $i+1;
							internalPutData($username, $tokendata);
							return TRUE;
						}
					}
				}
				return FALSE;
				break;

			case "TOTP":
				// ignore it?
				break;
		}

		return FALSE;
	}

	// gets the error text associated with the last error
	function getErrorText()
	{
		return $this->errorText;
	}

	// create a url compatibile with google authenticator.
	function createURL($user)
	{
		// oddity in the google authenticator... hotp needs to be lowercase.
		$data = $this->internalGetData($user);
		$toktype = $data["tokentype"];
		$key = $this->helperhex2b32($data["tokenkey"]);

		// token counter should be one more then current token value, otherwise
		// it gets confused
		$counter = $data["tokencounter"]+1;
		$toktype = strtolower($toktype);
		global $company;
		if($toktype == "hotp")
		{
			$url = "otpauth://$toktype/$user?secret=$key&counter=$counter&issuer=" . urlencode(htmlspecialchars_decode($company->CompanyName));
		}
		else
		{
			$url = "otpauth://$toktype/$user?secret=$key&issuer=" . urlencode(htmlspecialchars_decode($company->CompanyName));
		}

		return $url;
	}

	// creeates a base 32 key (random)
	function createBase32Key()
	{
		$alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567";
		$key = "";
		for($i=0; $i<16; $i++)
		{
			$offset = rand(0,strlen($alphabet)-1);
			$key .= $alphabet[$offset];
		}

		return $key;
	}

	// returns a hex key
	function getKey($username)
	{
		$data = $this->internalGetData($username);
		$key = $data["tokenkey"];

		return $key;
	}

	// get key type
	function getTokenType($username)
	{
		$data = $this->internalGetData($username);
		$toktype = $data["tokentype"];

		return $toktype;
	}


	// TODO: lots of error checking goes in here
	function helperb322hex($b32)
	{
		$alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567";

		$out = "";
		$dous = "";

		for($i = 0; $i < strlen($b32); $i++)
		{
			$in    = strrpos($alphabet, $b32[$i]);
			$b     = str_pad(base_convert($in, 10, 2), 5, "0", STR_PAD_LEFT);
			$out  .= $b;
			$dous .= $b.".";
		}

		$ar = str_split($out,20);

		$out2 = "";
		foreach($ar as $val)
		{
			$rv = str_pad(base_convert($val, 2, 16), 5, "0", STR_PAD_LEFT);
			$out2 .= $rv;

		}

		return $out2;
	}

	// TODO: lots of error checking goes in here
	function helperhex2b32($hex)
	{
		$alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567";

		$ar = str_split($hex, 5);

		$out = "";
		foreach($ar as $var)
		{
			$bc = base_convert($var, 16, 2);
			$bin = str_pad($bc, 20, "0", STR_PAD_LEFT);
			$out .= $bin;
		}

		$out2 = "";
		$ar2 = str_split($out, 5);
		foreach($ar2 as $var2)
		{
			$bc = base_convert($var2, 2, 10);
			$out2 .= $alphabet[$bc];
		}

		return $out2;
	}

	// i've put alot of faith in the code from the
	// php site's examples for the hash_hmac algorithm
	// i assume its mostly correct but i should do
	// some testing to verify this is actually the case
	function oath_hotp($key, $counter)
	{
		$key = pack("H*", $key);
		$cur_counter = array(0,0,0,0,0,0,0,0);
		for($i=7; $i>=0; $i--)
		{
			$cur_counter[$i] = pack ('C*', $counter);
			$counter = $counter >> 8;
		}
		$bin_counter = implode($cur_counter);
		// Pad to 8 chars
		if (strlen ($bin_counter) < 8)
		{
			$bin_counter = str_repeat (chr(0), 8 - strlen ($bin_counter)) . $bin_counter;
		}

		// HMAC
		$hash = hash_hmac ('sha1', $bin_counter, $key);
		return str_pad($this->oath_truncate($hash), 6, "0", STR_PAD_LEFT);
	}


	function oath_truncate($hash, $length = 6)
	{
		// Convert to dec
		foreach(str_split($hash,2) as $hex)
		{
			$hmac_result[]=hexdec($hex);
		}

		// Find offset
		$offset = $hmac_result[19] & 0xf;

		// Algorithm from RFC
		return
			(
				(($hmac_result[$offset+0] & 0x7f) << 24 ) |
				(($hmac_result[$offset+1] & 0xff) << 16 ) |
				(($hmac_result[$offset+2] & 0xff) << 8 ) |
				($hmac_result[$offset+3] & 0xff)
			) % pow(10,$length);
	}


	// some private data bits.
	private $getDatafunction;
	private $putDatafunction;
	private $errorText;
	private $errorCode;

	private $hotpSkew;
	private $totpSkew;

	private $hotpHuntValue;


	/*
	 * error codes
	 * 1: Auth Failed
	 * 2: No Key
	 * 3: input code was invalid (user input an invalid code - must be 6 numerical digits)
	 * 4: user doesnt exist?
	 * 5: key invalid
	 */
}
