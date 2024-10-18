<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

class Asset_Controller extends Base_Controller
{

	public function __construct(Template $template)
	{
		parent::__construct($template);
	}

	public function css()
	{
		header("Content-type: text/css; charset: UTF-8");

		$css_files = array('bootstrap.min.css', 'font-awesome.min.css', 'global.min.css');

		$possible_filename = get_url_var($_GET['rt']);
		if($possible_filename !== 'css')
		{
			// Strip all invalid characters by using a whitelist of characters
			$possible_filename = preg_replace('[^a-z0-9-_\.]', '', $possible_filename);
			$css_files = array($possible_filename);
		}

		$combined_css_files = '';
		foreach($css_files as $_css_file)
		{
			// get css file path without filename
			$css_file_path = str_ireplace($_css_file, '', get_file_path($_css_file, 'css'));
			// get the content of the css file, but replace any referer to files (like images or fonts) to a direct path
			$combined_css_files .=  str_replace('../', $css_file_path . '../', file_get_contents(get_file_path($_css_file, 'css', '', FALSE))) . "\n";
		}

		if(Settings_Model::get('CLIENTAREA_PRIMARY_COLOR'))
		{
			// replace primary color with color from the backoffice
			$combined_css_files = str_ireplace('#4F94BA', Settings_Model::get('CLIENTAREA_PRIMARY_COLOR'), $combined_css_files);

			// make the backoffice color 15% darker (just like in the sass files)
			$hover_color = color_brightness(Settings_Model::get('CLIENTAREA_PRIMARY_COLOR'), -0.85);
			$combined_css_files = str_ireplace(array('#3c799a', '#356a87', '#326480'), $hover_color, $combined_css_files);

			// make the backoffice color 12% darker (just like in the sass files)
			$hover_border_color = color_brightness(Settings_Model::get('CLIENTAREA_PRIMARY_COLOR'), -0.88);
			$combined_css_files = str_ireplace(array('#397392', '#274d63'), $hover_border_color, $combined_css_files);

			// make the backoffice color 50% lighter
			$input_hover_color = color_brightness(Settings_Model::get('CLIENTAREA_PRIMARY_COLOR'), 0.5);
			$combined_css_files = str_ireplace(array('#66afe9'), $input_hover_color, $combined_css_files);
		}

		echo $combined_css_files;
		exit;

	}

	public function js()
	{
		header("Content-type: text/javascript; charset: UTF-8");

		$js_files = array('jquery.3.4.0.min.js', 'tether.min.js', 'bootstrap.min.js');

		$include_default = true;
		$possible_filename = get_url_var($_GET['rt']);
		if($possible_filename !== 'js')
		{
			// Strip all invalid characters by using a whitelist of characters
			$possible_filename = preg_replace('[^a-z0-9-_\.]', '', $possible_filename);
			$js_files = array($possible_filename);
			$include_default = false;
		}

		$combined_js_files = '';
		foreach($js_files as $_js_file)
		{
			// get the content of the js file
			$combined_js_files .=  file_get_contents(get_file_path($_js_file, 'js', '', FALSE)) . "\n";
		}

		if($include_default)
		{
			$combined_js_files .= 'var CSRFtoken = "' . CSRF_Model::getToken(TRUE) . '";' . "\n";

			$combined_js_files .= file_get_contents(get_file_path('global.js', 'js', '', FALSE)) . "\n";
		}

		echo $combined_js_files;
		exit;
	}
}