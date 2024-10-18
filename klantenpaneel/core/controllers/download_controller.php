<?php
/**
 * This is a core PHP file. For customization, create a file in the folder /custom/.
 * Files with the same filename in this folder will automatically be loaded instead of the current file
 */

define("DOWNLOAD_PHP_ACTIVE", "YES");

class Download_Controller extends Base_Controller
{
	public function __construct(Template $template)
	{
		parent::__construct($template);

		// strip the namespace
		$this->ClassName = implode('', array_slice(explode('\\', get_called_class()), -1));
	}

	public function index()
	{
		$_SESSION['delete_download'] = $_SESSION['force_download_file'];

		download_file($_SESSION['force_download_file']);
	}
}