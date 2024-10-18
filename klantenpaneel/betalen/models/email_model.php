<?php
class Email_Model{
	
	public $Sender, $Recipient, $CarbonCopy, $BlindCarbonCopy;
	public $Message, $Subject;
	
	private $mailer;
		
	function __construct(){
		
		// Load parent constructor
		$this->_db = Database_Model::getInstance();
		
		// Load PHPmailer
        require_once "includes/mail/PHPMailer.php";
        require_once "includes/mail/SMTP.php";
        require_once "includes/mail/Exception.php";

        $this->mailer = new PHPMailer\PHPMailer\PHPMailer();
		$this->mailer->SMTPOptions = array('ssl' => array('verify_peer' => false, 'allow_self_signed' => false, 'verify_peer_name' => false));


		// Set default options
		$this->mailer->CharSet = 'utf-8';
		$this->mailer->SetLanguage(substr(LANGUAGE,0,2));
		$this->mailer->IsHTML(true);
		
		if(Setting_Model::getInstance()->get('SMTP_ON') == "1"){
			$this->mailer->IsSMTP();
				
			$this->mailer->SMTPSecure = (substr(Setting_Model::getInstance()->get('SMTP_HOST'),0,6) == 'tls://') ? 'tls' : ((substr(Setting_Model::getInstance()->get('SMTP_HOST'),0,6) == 'ssl://') ? 'ssl' : $this->mailer->SMTPSecure);
			$this->mailer->Host 		= (substr(Setting_Model::getInstance()->get('SMTP_HOST'),0,6) == 'tls://') ? substr(Setting_Model::getInstance()->get('SMTP_HOST'),6) : ((substr(Setting_Model::getInstance()->get('SMTP_HOST'),0,6) == 'ssl://') ? substr(Setting_Model::getInstance()->get('SMTP_HOST'),6) : Setting_Model::getInstance()->get('SMTP_HOST'));	
			$this->mailer->SMTPAuth 	= (Setting_Model::getInstance()->get('SMTP_AUTH') == "1") ? true : false;
			$this->mailer->Username 	= Setting_Model::getInstance()->get('SMTP_USERNAME');
			$this->mailer->Password 	= passcrypt(Setting_Model::getInstance()->get('SMTP_PASSWORD'));
  		}
  		
  		
	}
	
	function set_variables($object){
		foreach($object as $key => $value)
		{
			if(in_array($key,array('debtor','account','invoice','pricequote')))
			{
				// Trim or format e-mailaddresses
				if(isset($value->EmailAddress)){ $value->EmailAddress = check_email_address($value->EmailAddress, 'convert', ', '); }
				if(isset($value->InvoiceEmailAddress)){ $value->InvoiceEmailAddress = check_email_address($value->InvoiceEmailAddress, 'convert', ', '); }
			}
			${$key} = $value;
		}
		
		$this->Subject = str_replace("&gt;",">",$this->Subject);
		$this->Subject = str_replace("&lt;","<",$this->Subject);
		$this->Subject = str_replace("\"","{&quot;}",$this->Subject);
		$this->Subject = str_replace("&#39;","'",$this->Subject);

		# Replace vars in email subject		
		$pattern = '/\[(([_.\da-z0-9]+)->([_.\da-z0-9]+))\]/i';
		$replacement = '<?PHP echo (isset($\\1)) ? $\\1 : ""; ?>';
		$str = preg_replace($pattern, $replacement, stripslashes($this->Subject));
		
		ob_start();
		eval("?>".$str."<?PHP ");
		$str = ob_get_contents();
		ob_end_clean();
				
		if($str){
			$this->Subject = $str;
		}else{
			$str = preg_replace($pattern, $replacement, stripslashes($this->Subject));
			eval("\$str =\" ?> ".$str." \";");
			$this->Subject = $str;
		}
		
		if(substr(trim($this->Subject),0,2) == "?>")
				$this->Subject = substr(trim($this->Subject),2);
		$this->Subject = str_replace("{&quot;}","\"",$this->Subject);		
			
		
		$this->Message = str_replace("&amp;&amp;","&&",$this->Message);
		$this->Message = str_replace("&quot;","WFQUOTEWF",$this->Message);
		$this->Message = str_replace("&gt;",">",$this->Message);
		$this->Message = str_replace("&lt;","<",$this->Message);
		$this->Message = str_replace("\"","{&quot;}",$this->Message);
		$this->Message = str_replace("&#39;","'",$this->Message);
		$this->Message = str_replace("WFQUOTEWF","\"",$this->Message);
		
		# Replace vars in email body
		$pattern = '/\[([->_.\da-z0-9]+)\]/i';
		$replacement = '<?PHP echo (isset($\\1)) ? "$\\1" : "[\\1]" ; ?>';
		$str = preg_replace($pattern, $replacement, stripslashes($this->Message));

		ob_start();
		eval("?>".$str."<?PHP ");
		$str = ob_get_contents();
		ob_end_clean();
			
		if($str){
			$this->Message = $str;
		}else{
			$str = preg_replace($pattern, $replacement, stripslashes($this->Message));
			eval("\$str =\" ?> ".$str." \";");
			$this->Message = $str;
		}
		$this->Message = str_replace("&&","&amp;&amp;",$this->Message);
		$this->Message = str_replace("\"","&quot;",$this->Message);
		$this->Message = str_replace("{&quot;}","\"",$this->Message);		
		$this->Message = str_replace("<pre>","",$this->Message);		
		$this->Message = str_replace("</pre>","",$this->Message);
	}
	
	function send(){
		
		$subject = "";
  
  		$this->Sender = htmlspecialchars_decode($this->Sender, ENT_NOQUOTES);

		if(getFirstMailAddress($this->Sender)){
			$this->mailer->From 		= $this->Sender;
			$this->mailer->FromName 	= "";
		}else{

			$from = explode("<",$this->Sender);
			if(is_array($from) && count($from) == 2){
				$this->mailer->From 	= str_replace(">","",$from[1]);
				$this->mailer->FromName = $from[0];
			}else{
				$this->mailer->From = $this->Sender;
			}
  		}

		// Add address and CC
		$recips = array();
		$intRecipients = 0;
		$recipients = explode(";", check_email_address($this->Recipient, 'convert'));
		foreach($recipients as $recipient)
		{
			if($intRecipients === 0)
			{
				$this->mailer->AddAddress($recipient);
				$recips[] = $recipient;
			}
			else
			{
				if(!in_array($recipient,$recips))
				{
					$this->mailer->AddCC($recipient);
					$recips[] = $recipient;
				}
			}
			$intRecipients++;
		}
		
		// Add CC
		$ccs = explode(";", check_email_address($this->CarbonCopy, 'convert'));
		foreach($ccs as $cc)
		{
			if(!in_array($cc,$recips))
			{
				$this->mailer->AddCC($cc);
				$recips[] = $cc;
			}
		}
		
		// Add BCC
		$bccs = explode(";", check_email_address($this->BlindCarbonCopy, 'convert'));
		foreach($bccs as $bcc)
		{
			if(!in_array($bcc,$recips))
			{
				$this->mailer->AddBCC($bcc);
				$recips[] = $bcc;
			}
		}

		$this->mailer->Encoding = 'quoted-printable';
		$this->mailer->Body = $this->Message;
		$this->mailer->Subject = $this->Subject;

		// DKIM support
		$current_dkim = json_decode(htmlspecialchars_decode(DKIM_DOMAINS), true);
		$dkim_domain = substr($this->mailer->From,strrpos($this->mailer->From, '@')+1);

		if($current_dkim && isset($current_dkim[$dkim_domain]))
		{
			// Create temp file
			$dkim_filename = @tempnam('temp/','dkim');
			if(@file_put_contents($dkim_filename, $current_dkim[$dkim_domain]['private']))
			{
				$this->mailer->DKIM_domain = $dkim_domain;
				$this->mailer->DKIM_private = $dkim_filename; //path to file on the disk.
				$this->mailer->DKIM_selector = $current_dkim[$dkim_domain]['selector'];// change this to whatever you set during step 2
				$this->mailer->DKIM_passphrase = "";
				$this->mailer->DKIM_identifier = $this->mailer->From;
			}
		}


		$result = $this->mailer->Send();

		if(!$result){
			$this->Error[] = $this->mailer->ErrorInfo;
			return false;
		}else{		
			return true;
		}	
	}
}