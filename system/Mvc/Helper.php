<?php
namespace System\Mvc;

use System\File;
use System\Folder;

class Helper
{
	private static $_helperLocations = [];

	public static function getHelperLocation_bak($className)
	{
		if (isset(static::$_helperLocations[$className]) == false)
		{
			$file = BASEPATH_TMP.'/cache/helperlocation'.EXT;

			if (File::exists($file) == false or DEV_MODE)
			{
				$info = array();
				$packagePath = BASEPATH_PACKAGES;

				$packageDir = dir($packagePath);
				while (($fileName = $packageDir->read()) !== false)
				{
					if ($fileName == '.' or $fileName == '..' or is_dir($packagePath.'/'.$fileName) == false)
						continue;

					$helperPath = $packagePath.'/'.$fileName.'/helpers';
					if (Folder::exists($helperPath))
					{
						$helperDir = dir($helperPath);
						while (($fileName = $helperDir->read()) !== false)
						{
							if (strpos(strtolower($fileName), EXT) === false)
								continue;

							$helperFileName = File::getName($helperPath.'/'.$fileName);
							$helperFileName = File::stripExtension($helperFileName);
							$helperLocation = $helperPath.'/'.$fileName;

							$info[$helperFileName] = $helperLocation;
						}
						$helperDir->close();
					}
				}
				$packageDir->close();

				static::$_helperLocations[$className] = $info[$className];

				$fp = fopen($file, 'w');
				fwrite($fp, '<?php //'.serialize($info));
			}

			$content = file_get_contents($file);
			$content = substr($content, 8);
			static::$_helperLocations = unserialize($content);
		}

		return static::$_helperLocations[$className];
	}

	public static function getHelperLocation($class) // ok ล่าสุด
	{
		if (isset(static::$_helperLocations[$class]) == false)
		{
			$tempFile = BASEPATH_TMP.'/cache/helpers.php';

			// Need to create new file to replace the old one everytime in development mode
			// because cannot use the same method as deleting/creating temp table files in
			// class database because when model class is called, this method, getModelLocation()
			// will be executed via autoload function. That means temp model location files
			// will be created, but, these file will be deleted immediately after model object created
			// via model->__construct() method.

			if (File::exists($tempFile) == false or DEV_MODE)
				static::_loadHelperLocationToTempFile();

			static::$_helperLocations = static::_loadHelperLocationFromTempFile();
		}

		return @static::$_helperLocations[$class];
	}

	private static function _loadHelperLocationToTempFile() // ok ล่าสุด
	{
		$tempFile = BASEPATH_TMP.'/cache/helpers.php';
		$packagePath = BASEPATH_PACKAGES;
		$packageEntries = scandir($packagePath);

		$content = [];

		foreach ($packageEntries as $packageEntry)
		{
			if (Folder::exists($packagePath.'/'.$packageEntry) == false)
				continue;

			$helperPath = $packagePath.'/'.$packageEntry.'/helpers';

			if (Folder::exists($helperPath))
			{
				$helperEntries = scandir($helperPath);

				foreach ($helperEntries as $helperEntry)
				{
					if (mb_stripos($helperEntry, '.php') === false)
						continue;

					$helperName = File::getNameWithoutExtension($helperEntry);
					$helperLocation = $helperPath.'/'.$helperEntry;

					$content[$helperName] = $helperLocation;
				}
			}
		}

		$fp = fopen($tempFile, 'w');
		fwrite($fp, '<?php //'.serialize($content));
	}

	private static function _loadHelperLocationFromTempFile() // ok ล่าสุด
	{
		$tempFile = BASEPATH_TMP.'/cache/helpers.php';

		$content = file_get_contents($tempFile);
		$content = substr($content, 8);
		$content = @unserialize($content);

		if ($content === false)
		{
			static::_loadHelperLocationToTempFile();
			$content = static::_loadHelperLocationFromTempFile();
		}

		return $content;
	}
}

/*
class Helper
{
	public static function render($package, $view=null)
	{
		if  ($view == null)
			$view = 'index';

		if (File::exists('packages/'.$package.'/helpers/views/'.$view.'.php'))
			include('packages/'.$package.'/helpers/views/'.$view.'.php');
		elseif (File::exists('system/'.$package.'/helpers/views/'.$view.'.php'))
			include('system/'.$package.'/helpers/views/'.$view.'.php');
		else
			echo '<p>Widget layout not found.</p>';
	}
}

class HelperView
{
	public $package;

	public function __construct($package)
	{
		$this->package = $package;
	}

	public function render($view=null)
	{
		if  ($view == null)
			$view = 'index';

		if (File::exists('packages/'.$this->package.'/helpers/views/'.$view.'.php'))
			include('packages/'.$this->package.'/helpers/views/'.$view.'.php');
		elseif (File::exists('system/'.$this->package.'/helpers/views/'.$view.'.php'))
			include('system/'.$this->package.'/helpers/views/'.$view.'.php');
		else
			echo '<p>Widget layout not found.</p>';
	}
}
*/
