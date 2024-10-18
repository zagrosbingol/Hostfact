<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

class Settings_Model extends Base_Model
{
	static $settings                  = array();
	static $countries                 = array();
	static $states                    = array();
	static $legaltypes                = array();
	static $taxpercentages            = array();
	static $taxpercentages_info       = array();
	static $total_taxpercentages      = array();
	static $total_taxpercentages_info = array();
	static $sexes                     = array();
	static $payment_methods           = array();
	static $languages                 = array();

	static $default_calls = array();

    const GENDER_SHOW_IF_IN_ARRAY = ['m','f'];

	function __construct()
	{
		$this->setInitialSettings();

		// We define some initials calls for getting information we will need on every page...
		self::$default_calls = array('debtorshow'  => array('controller' => 'debtor', 'action' => 'show', 'params' => array(), 'options' => array('cacheable' => true)),
							   		'backofficesettings' => array('controller' => 'global', 'action' => 'settings', 'params' => array(), 'options' => array('cacheable' => true, 'cacheGlobal' => true)),
							   		'arraylists'         => array('controller' => 'global', 'action' => 'arraylists', 'params' => array(), 'options' => array('cacheable' => true, 'cacheGlobal' => true)));
	}

	/**
	 * Get settings from the backoffice even if user is not logged in and set them in static var
	 * this should be the only data retrieve through a database connection, all other data is retrieved by the API
	 */
	function setInitialSettings()
	{
		if($settings = Cache::get('InitialSettings', true))
		{
			// Settings from cache
		}
		else
		{
			$result = Database_Model::getInstance()
				->get('HostFact_Settings')
				->where('Variable', array('IN' => array('CLIENTAREA_DEFAULT_LANG', 'BACKOFFICE_URL', 'CLIENTAREA_URL', 'CLIENTAREA_LOGO_URL', 'CLIENTAREA_HEADER_TITLE', 'IS_INTERNATIONAL', 'CLIENTAREA_PRIMARY_COLOR', 'PASSWORD_GENERATION')))
				->execute();

            $settings = [];
            if ($result) {
                foreach ($result as $key => $value) {
                    $settings[$value->Variable] = $value->Value;
                }

                Cache::set('InitialSettings', $settings, false, true);
            }
		}

		// Set settings in Setting object
		foreach($settings as $key => $value)
		{
			if(!isset(self::$settings[strtoupper($key)]))
			{
				self::$settings[strtoupper($key)] = $value;
			}
		}
	}

	/**
	 * Retrieve the global settings from the backoffice (/Pro/) and set them in static var
	 */
	function setDefaultSettings($default_settings)
	{
		if(is_array($default_settings))
		{
			foreach($default_settings as $key => $value)
			{
				if(!isset(self::$settings[strtoupper($key)]))
				{
					self::$settings[strtoupper($key)] = $value;
				}
			}
		}
	}

	/**
	 * Used to retrieve the value from a static variable
	 *
	 */
	static function get($variabele)
	{
		if(isset(self::$settings[$variabele]))
		{
			return self::$settings[$variabele];
		}

		return FALSE;
	}

	static function set($variabele, $value)
	{
		self::$settings[$variabele] = $value;
	}

	public function setArraylists($array_lists)
	{
		if(is_array($array_lists))
		{
			foreach($array_lists as $key => $value)
			{
				switch($key)
				{
					case 'countries':
						$array_country = array();
						foreach($value as $_country_code => $_country_info)
						{
							$array_country[$_country_code] = (isset($_country_info[ACTIVE_LANG])) ? $_country_info[ACTIVE_LANG] : $_country_info[FALLBACK_LANGUAGE];
						}

						self::$countries = $array_country;
					break;

					case 'states':
						self::$states = $value;
					break;

					case 'legalforms':
						self::$legaltypes = $value;
					break;

					case 'taxpercentages':
						self::$taxpercentages = $value;
					break;

					case 'taxpercentages_info':
						self::$taxpercentages_info = $value;
					break;

					case 'total_taxpercentages':
						self::$total_taxpercentages = $value;
					break;

					case 'total_taxpercentages_info':
						self::$total_taxpercentages_info = $value;
					break;

					case 'sexes':
						self::$sexes = $value;
					break;

					case 'payment_methods':
						if(is_array($value))
						{
							self::$payment_methods = $value;
						}
					break;
				}
			}
		}
	}

	public function setLanguages($array_list_languages)
	{
		self::$languages = $array_list_languages;
	}

	public function loadBackofficeSettings()
	{
		$result = $this->APIRequest('global', 'settings', array(), array('cacheable' => true, 'cacheGlobal' => true));
		if($result === FALSE || !isset($result['settings']))
		{
			return FALSE;
		}

		$this->setDefaultSettings($result['settings']);

		return TRUE;
	}

	static function setDebtorSettings($debtor_settings)
	{
		if(is_array($debtor_settings))
		{
			foreach($debtor_settings as $key => $value)
			{
				if(!isset(self::$settings[strtoupper($key)]))
				{
					self::$settings[strtoupper($key)] = $value;
				}
			}
		}
	}

    /**
     * Only show the gender name if it's a male or female.
     *
     * @param string $gender - Value for the gender.
     * @param bool $always - If true, always show the gender name.
     *
     * @return string - Display name.
     */
    public static function getGenderTranslation(string $gender, bool $always = false): string
    {

        if ($always === true || in_array($gender, self::GENDER_SHOW_IF_IN_ARRAY)) {
            return __($gender, 'array_sex');
        }

        return '';
    }

}
