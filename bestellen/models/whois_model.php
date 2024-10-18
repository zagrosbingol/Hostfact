<?php
class Whois_Model extends Base_Model{
	
	private $_properties;
	private $_tlds;
	
	function __construct(){
		
		// Load parent constructor
		parent::__construct();
		
		
		// Last WHOIS check information
		$this->SLD = '';
		$this->TLD = '';
		$this->_tlds = array();

		// Determine available TLD's
		$pdo_statement = $this->_db->prepare("SELECT `ProductTLD` as `TLD`, `PriceExcl`, `PricePeriod`, `ProductCode`, `TaxPercentage`, (`PriceExcl` * (1+`TaxPercentage`)) as `PriceIncl`  
											 FROM `HostFact_Products` p, `HostFact_GroupRelations` g 
											 WHERE g.`Group`=:group_id AND g.`Type`='product' AND g.`Reference`=p.`id` AND p.`ProductType`='domain' AND p.`ProductTLD`<>'' AND p.`Status`='1' 
											 ORDER BY `ProductTLD` ASC");
		$pdo_statement->bindValue(':group_id', GROUP_DOMAIN);									 
		$pdo_statement->execute();	
		$tlds = $pdo_statement->fetchAll();

		// Split popular
		$popular_list = explode("|",strtolower(POPULAR_LIST));
		
		$tld_count = 0;
		foreach($tlds as $tmp_tld){
			
			$tmp_tld->TLD = strtolower($tmp_tld->TLD);
			
			if(in_array($tmp_tld->TLD, $popular_list))
			{
				$tmp_tld->Type = 'popular';
				$this->_tlds[array_search($tmp_tld->TLD, $popular_list)] = $tmp_tld;
			}
			else
			{
				$tmp_tld->Type = 'other';
				$this->_tlds[count($popular_list) + $tld_count] = $tmp_tld;
			}
			
			
			$tld_count++;
		}
		// Sort on key
		ksort($this->_tlds);
		
	}
	
	function checkDomain($sld, $tld){
		// Check again domain
		if(!$this->parseDomain($sld.'.'.$tld)){
			return false;	
		}
		
		// Check IDN charachters allowd
		if(!$this->validIDNDomain($sld, $tld))
		{
			return 'invalid';
		}
		
		// Get public whois server
		if(!$public_whois = $this->getPublicWhoisServer($this->TLD)){
			return false;
		}

		// Execute check
		$ns = @fsockopen($public_whois['server'],43, $errno, $errstr, 10);
		if(!$ns){
			$this->Error[] = sprintf(__('could not connect to whois server'),$public_whois['server']);
			return false;
		}

		if($this->TLD == "nl"){
			fputs($ns,"is ".$this->SLD.".".$this->TLD."\r\n");	
		}else{
			fputs($ns,$this->SLD.".".$this->TLD."\r\n");
		}

		// Set stream reading timeout
		stream_set_timeout($ns, 3);

		$result = '';
		while(!feof($ns)){
			$line = fgets($ns);
			if($line === false)
			{
				break;
			}
			$result .= $line;
		}
		fclose($ns);

		// First check for limits, busy servers etc
		if(	stripos($result, 'Server too busy, try again later') !== false ||
			stripos($result, 'maximum number of requests per second exceeded') !== false ||
			stripos($result, 'WHOIS LIMIT EXCEEDED') !== false)
		{
			$this->Error[] = sprintf(__('could not connect to whois server'),$public_whois['server']);
			return false;
		}

		// Check availability
		if (preg_match("/".$public_whois['nomatch']."/i",$result) > 0) {
			return 'available'; 
		}else{ 
			return 'unavailable'; 
		}
		
	}

	function getPublicWhoisServer($tld){
		// Get WHOIS server from database
		$pdo_statement = $this->_db->prepare("SELECT `Tld`, `WhoisServer`, `WhoisNoMatch`
												 FROM `HostFact_TopLevelDomain`  
												 WHERE `Tld`=:tld
												 ORDER BY `Tld` ASC");
		$pdo_statement->bindValue(':tld', $tld);						
		$pdo_statement->execute();	
		$whois_server = $pdo_statement->fetch();
				
		// Is the public whois server in the database?
		if(isset($whois_server->WhoisServer) && $whois_server->WhoisServer){
			return array('server' => $whois_server->WhoisServer, 'nomatch' => $whois_server->WhoisNoMatch);
		}else{
			$this->Error[] = sprintf(__('unknown whois server'),htmlspecialchars($tld));
			return false;
		}
		
		
	}
	
	function parseDomain($domain){
		
		// Escape domain
		$domain = strtolower(stripslashes(htmlspecialchars(trim($domain))));
		
		// Strip www., http:// and https://
		$domain = preg_replace('/^(http(s)?:\/\/)?(www\.)?/i', '', $domain);

		// Explode domain on dot-notation
		$exploded_domain = explode('.',$domain, 2);
		
		// Check SLD
		$this->SLD = $exploded_domain[0];
			// Check if SLD is not empty
			if(!$this->SLD){
				$this->Error[] = __('no domain entered');
				return false;
			}
			
			// Check if SLD is between 2 and 63 characters
			if(strlen($this->SLD) < 2 || strlen($this->SLD) > 63){
				$this->Error[] = __('sld must be between 2 and 63 characters');
				return false;
			}
		
			// Check if SLD do not contain dots
			if(strpos($this->SLD,'.') !== false){
				$this->Error[] = __('sld should not contain dots');
				return false;
			}
			
			$idn = $this->getAllowedIDNCharacters();
			if(preg_match("/^[a-z".$idn."0-9-]+(\.[a-z".$idn."0-9-]+)*$/i", $this->SLD) == 0){
				$this->Error[] = __('sld contains invalid characters');
				return false;
			}
			
		// Check TLD
		if(isset($exploded_domain[1])){
			// Get all tlds and check if tld exists
			$all_tlds = $this->getTopLevelDomains('all');
			
			$tmp_all_tlds = array();
			foreach($all_tlds as $tmp_tld){
				$tmp_all_tlds[] = $tmp_tld->TLD;
			}
			
			// If not in available domains, give error
			if(!in_array($exploded_domain[1], $tmp_all_tlds)){
				$this->Warning[] = __('tld not available');
			}
			
			$this->TLD = $exploded_domain[1];
		}
		
		// Domain correctly parsed
		return true;
	}
	
	function validIDNDomain($sld, $tld)
	{
		// Check allowed IDN
		$idn = $this->getAllowedIDNCharacters($tld);
				
		// Preg-match for valid characters
		if(preg_match("/^[a-z".$idn."0-9-]+(\.[a-z".$idn."0-9-]+)*$/i", $sld) == 0){
			return false;
		}
		
		return true;
	}
	
	function getAllowedIDNCharacters($tld = '')
	{
		if($tld)
		{
			// Get specific characters for this tld
			$pdo_statement = $this->_db->prepare("SELECT `AllowedIDNCharacters` FROM `HostFact_TopLevelDomain` WHERE `Tld`=:tld");
			$pdo_statement->bindValue(':tld', $tld);
		}
		else
		{
			// Get IDN characters from all tlds
			$pdo_statement = $this->_db->prepare("SELECT `AllowedIDNCharacters` FROM `HostFact_TopLevelDomain`");
		}
										 
		$pdo_statement->execute();	
		$idn_per_tld = $pdo_statement->fetchAll();
				
		$idn = '';
		foreach($idn_per_tld as $idn_tld)
		{
			$idn .= $idn_tld->AllowedIDNCharacters;
		}

		return $idn;
	}
	
	
	function getTopLevelDomains($filter = 'popular'){
		
		$topleveldomains = array();
		
		foreach($this->_tlds as $k=>$tld)
		{
			if(($tld->Type == $filter || $filter == 'all' || ($filter == 'popular' && $this->TLD == $tld->TLD)) && ($filter != 'other' || $this->TLD != $tld->TLD))
			{
				// Check if this is the chosen TLD, so we shoud put in as first element
				if($this->TLD && $this->TLD == $tld->TLD)
				{
					array_unshift($topleveldomains, $tld);
				}
				else
				{
					$topleveldomains[] = $tld;	
				}
			}			
		}

		return $topleveldomains;
		
	}
	
	
}