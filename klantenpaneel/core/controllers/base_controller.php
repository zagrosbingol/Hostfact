<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

class Base_Controller
{
	public $Template;
	public $PublicPage = false;

	public function __construct(Template $template)
	{
		$this->Template = $template;

		// strip the namespace
		$this->ClassName = implode('', array_slice(explode('\\', get_called_class()), -1));

		if($this->ClassName != get_called_class())
		{
			$this->PluginName		= str_replace(array('_Controller_Custom', '_Controller'), '', $this->ClassName);
			$this->FullPluginName 	= str_replace(array('_Controller_Custom', '_Controller'), '', get_called_class());

			// Set plugin name for use in template engine
			$this->Template->PluginName = $this->FullPluginName;

			// Set language file environment
			Language::setPluginName($this->FullPluginName);

			// check if plugin is core or custom
			$site_path = (isset(Plugin::$loaded_plugins[$this->FullPluginName]['type']) && Plugin::$loaded_plugins[$this->FullPluginName]['type'] == 'core') ? __SITE_PATH : __SITE_CUSTOM_PATH;

			// Set plugin path
			$this->PluginPath = $site_path . '/' . PLUGINPATH . '/' . strtolower($this->PluginName) . '/';
		}
	}

	public function ajaxSaveFilter()
	{
		Cache::set('filter-' . $_POST['listID'], $_POST['search']);
		exit;
	}

	public function ajaxExtendedSearch()
	{
		// Reset filter cache
		Cache::reset('filter-' . $_POST['listID']);

		// Set extended search cache
		Cache::set('search-' . $_POST['listID'], $_POST['search']);
	}

	public function checkForPublicPage($action)
	{
		return $this->PublicPage;
	}
}