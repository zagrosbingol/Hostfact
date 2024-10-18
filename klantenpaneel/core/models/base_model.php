<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

class Base_Model
{
	public $Error;
	public $Warning;
	public $Success;
	public $ErrorFields;

	public $UseAPIError;
	public $UseAPISuccess;

	public function __construct()
	{
		$this->Error = $this->Warning = $this->Success = $this->ErrorFields = array();
	}

	/** this function handles multiple API request in one call, which makes it much more efficient to retrieve/process data of multiple objects
	 *
	 * @param $params	array	an array which contains the parameters for multiple API calls
	 * @return array|bool|mixed
	 */
	function APIRequestMultiple($calls)
	{
		// First look for cacheable requests and remove them from the list which need to be retrieved
		$results_multicall = array();
		$cacheable_keys = array();
		foreach($calls as $key => $_call)
		{
			// Look for options regarding caching. A cacheable option can be true or false (default) or a unique reference to bundle caching for a specific object
			// a cacheGlobal option can be true or false (default). If true, caching might be shared between different clients (settings for example)
			if(isset($_call['options']['cacheable']) && $_call['options']['cacheable'] !== false)
			{
				// We build a key as controller.(uniquereference.).action.serializedParams
				$cacheable_keys[$key] = ($_call['options']['cacheable'] === TRUE) ? implode('.', array($_call['controller'], $_call['action'], base64_encode(json_encode($_call['params'])))) : implode('.', array($_call['controller'], $_call['options']['cacheable'], $_call['action'], base64_encode(json_encode($_call['params']))));
				$cache_global = (isset($_call['options']['cacheGlobal']) && $_call['options']['cacheGlobal'] === true) ? TRUE : FALSE;

				if($results_multicall[$key] = Cache::get($cacheable_keys[$key], $cache_global))
				{
					// We use the cache, so add a parameter to inform about this
					$results_multicall[$key]['fromCache'] = true;

					// Remove this request from multicall
					unset($calls[$key]);
				}
			}
		}

		// Only execute this part if there are any multicall items left
		if(!empty($calls))
		{
			$result = HostFactAPI::sendMultipleRequest(array('multicall' => $calls));

			// general API error
			if(isset($result['status']) && $result['status'] == 'error')
			{
				$this->parseAPIErrors($result['errors']);
				return FALSE;
			}

			if(!empty($result))
			{
				// each individual API call response is processed here
				foreach($calls as $key => $_call)
				{
					// if the specific API call did not return a response
					if(!isset($result[$key]))
					{
						continue;
					}

					// Save to cache before parseAPIResponse if it is a cacheable call
					if(isset($_call['options']['cacheable']) && $_call['options']['cacheable'] !== FALSE)
					{
						// Save cache
						$cache_time = (isset($_call['options']['cacheTime']) && $_call['options']['cacheTime']) ? $_call['options']['cacheTime'] : FALSE;
						$cache_global = (isset($_call['options']['cacheGlobal']) && $_call['options']['cacheGlobal'] === true) ? TRUE : FALSE;
						Cache::set($cacheable_keys[$key], $result[$key], $cache_time, $cache_global);
					}

					// Add response to return array
					$results_multicall[$key] = $this->parseAPIResponse($result[$key]);
				}
			}
			else
			{
				return FALSE;
			}
		}

		return $results_multicall;
	}

	/**	Single API call to the backoffice
	 *
	 * @param $controller    string    controller name, eg debtor or invoice
	 * @param $action        string    action of the controller, eg show or list
	 * @param $params        array    array with parameters for the API call, eg filter data or post data
	 * @param array $options array    additional options can be given here as array
	 * @return bool
	 */
	function APIRequest($controller, $action, $params, $options = array())
	{
		// reset these values, by default API errors are parsed and attached to the object
		// by default API success messages are not parsed and attached to the object
		$this->UseAPIError   = (isset($options['useAPIError']) && $options['useAPIError'] === FALSE) ? FALSE : TRUE;
		$this->UseAPISuccess = (isset($options['useAPISuccess']) && $options['useAPISuccess'] === TRUE) ? TRUE : FALSE;

		// Look for options regarding caching. A cacheable option can be true or false (default) or a unique reference to bundle caching for a specific object
		// a cacheTime option can give a custom caching time, a cacheGlobal option can be true or false (default). If true, caching might be shared between different clients (settings for example)
		$cacheable = (isset($options['cacheable']) && $options['cacheable'] !== false) ? $options['cacheable'] : false;
		$cache_time = (isset($options['cacheTime']) && $options['cacheTime']) ? $options['cacheTime'] : FALSE;
		$cache_global = (isset($options['cacheGlobal']) && $options['cacheGlobal'] === true) ? TRUE : FALSE;

		if($cacheable)
		{
			// We build a key as controller.(uniquereference.).action.serializedParams
			$cacheableKey = ($cacheable === TRUE) ? implode('.', array($controller, $action, base64_encode(json_encode($params)))) : implode('.', array($controller, $cacheable, $action, base64_encode(json_encode($params))));

			if($result = Cache::get($cacheableKey, $cache_global))
			{
				// We use the cache, so add a parameter to inform about this
				$result['fromCache'] = true;
			}
		}

		if(!$cacheable || !$result)
		{
			// Get data via API
			$result = HostFactAPI::sendRequest($controller, $action, $params);

			// Store cache?
			if($cacheable)
			{
				// Save cache
				Cache::set($cacheableKey, $result, $cache_time, $cache_global);
			}
		}

		return $this->parseAPIResponse($result);
	}

	/** This function parses the response from a API call
	 *
	 * @param $result
	 * @return bool
	 */
	function parseAPIResponse($result)
	{
		// invalid response: should only happen if the backoffice cannot be reached or the given API parameters are incorrect
		if(!$result || (isset($result['controller']) && $result['controller'] == 'invalid') || (isset($result['action']) && $result['action'] == 'invalid'))
		{
			// if the API response contains errors, add them to the object
			if(!empty($result['errors']))
			{
				if($this->UseAPIError)
				{
					$this->parseAPIErrors($result['errors']);
				}
				return FALSE;
			}
			// API call failed, but we have no clue why
			else
			{
				$this->Error[] = __('api action failed, reason unknown');
				return FALSE;
			}
		}

		// API response status = error: API call was successfull but returns an error, add the errors to the object
		if($result['status'] == 'error' && isset($result['errors']))
		{
			if($this->UseAPIError)
			{
				$this->parseAPIErrors($result['errors']);
			}
			return FALSE;
		}

		// API response status = error: API call was successfull and returns a success, add the success messages to the object
		if($result['status'] == 'success')
		{
			if(isset($result['success']) && $this->UseAPISuccess)
			{
				$this->parseAPISuccess($result['success']);
			}

			return $result;
		}
	}

	/** this function adds the errors from the API response to this object
	 *
	 * @param $api_errors
	 */
	private function parseAPIErrors($api_errors)
	{
		if(!empty($api_errors))
		{
            // Encode the response.
            $api_errors = array_map(function($message){ return htmlspecialchars($message); }, $api_errors);

			$this->Error = $api_errors;
		}
	}

	/** this function adds the success messages from the API response to this object
	 *
	 * @param $api_success
	 */
	private function parseAPISuccess($api_success)
	{
		if(!empty($api_success))
		{
            // Encode the response.
            $api_success = array_map(function($message){ return htmlspecialchars($message); }, $api_success);

            $this->Success = $api_success;
		}
	}

	/**	this function converts the variables from a object (eg debtor) to an array, mainly used to prepare data for a API call
	 *
	 * @param $whitelist	bool|array	an array which contains the variables that should be converted to the array
	 * @return array
	 */
	function objectParamsToArray($whitelist = false)
	{
		$params = array();
		foreach($this->Variables as $_var)
		{
			if(isset($this->{$_var}) && ($whitelist === false || in_array($_var, $whitelist)))
			{
				$params[$_var] = $this->{$_var};
			}
		}
		return $params;
	}

	/** this function adds the array values from a API response to an object (eg debtor)
	 *
	 * @param $api_response_fields
	 */
	function arrayToObject($api_response_fields)
	{
		$params = array();
		foreach($this->Variables as $_var)
		{
			if(isset($api_response_fields[$_var]))
			{
				$this->{$_var} = $api_response_fields[$_var];
			}
		}
	}
}