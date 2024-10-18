<?php

class Base_Controller
{
	public $lang = array();	
	
	private $_vars = array();
	
	// Whois form properties
	private $_inline = false;
	private $_type = 'default';
	private $_is_js_included = false;
	private $_include_whois_header = false;
	
	private $_view_dir = '';
	private $_theme_dir = '';
	

	public function __construct()
	{		
		$this->_view_dir = 'views';
			
		// Error messages
		$this->Warning = array();
		$this->Error = array();
		
		$this->set('array_taxpercentages', 			Setting_Model::getInstance()->get('array_taxpercentages'));
		$this->set('array_taxpercentages_info', 	Setting_Model::getInstance()->get('array_taxpercentages_info'));
		$this->set('array_total_taxpercentages',	Setting_Model::getInstance()->get('array_total_taxpercentages'));
	}

	public function set($index, $value)
	{
		$this->_vars[$index] = $value;
	}
	
	public function get($index)
	{
		return $this->_vars[$index];
	}
	
	public function setTheme($directory)
	{
		$this->_theme_dir = $this->_view_dir.'/'.$directory;
	}
			
	function display($view)
	{
		
		global $_LANG;
		
		// Load variables
		foreach($this->_vars as $key => $value)
		{
			${$key} = $value;
		}
				
		if($this->_is_js_included === true)
		{
			$this->startBuffer();
		}
		elseif($this->_type != 'inline')
		{
			if($this->_theme_dir && file_exists($this->_theme_dir."/header.phtml"))
			{
				include $this->_theme_dir."/header.phtml";
			}
			else
			{
				include $this->_view_dir."/header.phtml";
			}
		}
	
		// Load view for including JS and CSS files
		if($this->_include_whois_header === true)
		{
			include $this->_theme_dir."/whois_header.phtml";
		}

		// Load view
		global $autoload_path;
		$paths_to_check = array_merge($autoload_path, array(''));
		$view_included = false;
		
		foreach($paths_to_check as $path)
		{
			if($this->_theme_dir && file_exists($path.$this->_theme_dir."/".$view))
			{
				include $path.$this->_theme_dir."/".$view;
				$view_included = true;
				break;
			}
			elseif(file_exists($path.$this->_view_dir."/".$view))
			{
				include $path.$this->_view_dir."/".$view;
				$view_included = true;
				break;
			}
		}
		
		if($view_included === false)
		{
			fatal_error('Een bestand ontbreekt', 'Kan het benodigde bestand voor de view "'.$view.'" niet vinden.');
		}
		
		
		// Should we include the footer?
		if($this->_is_js_included === true)
		{
			$html = $this->endBuffer();
			$this->parseJS($html);
		}
		elseif($this->_type != 'inline')
		{
			if($this->_theme_dir && file_exists($this->_theme_dir."/footer.phtml"))
			{
				include $this->_theme_dir."/footer.phtml";
			}
			else
			{
				include $this->_view_dir."/footer.phtml";
			}
		}		
	}
	
	function element($view, $theme_dir = false)
	{
		global $_LANG;
		
		if($theme_dir === false)
		{
			$theme_dir = $this->_theme_dir;
		}
		else
		{
			$theme_dir = $this->_view_dir.'/'.$theme_dir;
		}
		
		
		// Load variables
		foreach($this->_vars as $key => $value)
		{
			${$key} = $value;
		}
		
		// Load view
		global $autoload_path;
		$paths_to_check = array_merge($autoload_path, array(''));
		$view_included = false;

		foreach($paths_to_check as $path)
		{
			if($theme_dir && file_exists($path.$theme_dir."/elements/".$view))
			{
				include $path.$theme_dir."/elements/".$view;
				$view_included = true;
				break;
			}
			elseif($theme_dir && file_exists($path.$theme_dir."/".$view))
			{
				include $path.$theme_dir."/".$view;
				$view_included = true;
				break;
			}
			elseif(file_exists($path.$this->_view_dir."/elements/".$view))
			{
				include $path.$this->_view_dir."/elements/".$view;
				$view_included = true;
				break;
			}
		}
		
		if($view_included === false)
		{
			fatal_error('Een bestand ontbreekt', 'Kan het benodigde bestand voor de view "'.$view.'" niet vinden.');
		}
	
	}
	

	function startBuffer()
	{
		ob_start();
	}
	
	function endBuffer()
	{
		// Store output in variable
		$output = ob_get_contents();
		
		// Stop buffering
		ob_end_clean();
		
		// Return output
		return $output;
	}
	
	function parseJS($output)
	{
		// Force header to be javascript, prevent nosniff issue
		header('Content-Type: text/javascript');

		// Remove linebreaks
		$output = str_replace(array("\r\n","\n"),"",$output);
			
		// Echo content
		if(isset($_GET['jquerydiv']) && $_GET['jquerydiv'])
		{
			// jQuery dialogs support
			echo "jQuery('#" . htmlspecialchars($_GET['jquerydiv']) . "').append('". $output ."');\r\n";
		}
		else
		{
			echo "document.write('". $output ."');\r\n";
		}
		exit;
	}
	
	function setType($type)
	{
		
		switch($type)
		{
			case 'inline':	$this->_type = 'inline';	break;
			case 'extern':	$this->_type = 'extern';	break;
			default:		$this->_type = 'default';	break;
		}
	}
	
	function setViewDir($directory)
	{
		$this->_view_dir = $directory;	
	}
	
	function getType()
	{
		return $this->_type;
	}
	
	function setJSLoaded($boolean)
	{
		$this->_is_js_included = $boolean;
	}
	
	function getJSLoaded()
	{
		return $this->_is_js_included;
	}
	
	function setIncludeWhoisHeader($boolean)
	{
		$this->_include_whois_header = $boolean;
	}
}

