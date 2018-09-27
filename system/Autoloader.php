<?php
namespace System;

use System\Mvc\Helper;
use System\Mvc\Model;

/**
 * Decorate Composer ClassLoader
 */
class Autoloader
{
	/**
	 * The composer class loader
	 *
	 * @var    ClassLoader
	 */
	private static $_loader;

	/**
	 * Constructor
	 *
	 * @param   ClassLoader  $loader  Composer autoloader
	 */
	public function __construct(\Composer\Autoload\ClassLoader  $loader)
	{
		static::$_loader = $loader;
	}

	public static function loadClass($class)
	{
		$file = $class;
		$file = str_replace('\\', '/', $file);

		if (substr($file, 0, 7) == 'System/')
			$file = substr_replace($file, BASEPATH_SYSTEM . '/', 0, 7);

		$file .= '.php';

		// Second condition is used to avoid file at root directory ie config.php
		if (is_file($file) == false or (is_file($file)) and strpos($file, '/') === false)
		{
			//$arr = explode('/', $class);
			//$model = end($arr);

			if (substr($class, -6) == 'Helper')
				$file = Helper::getHelperLocation($class);
			else
				$file = Model::getModelLocation($class);
		}

		if (is_file($file) == false)
			static::$_loader->loadClass($class);
		else
			include_once($file);
	}

	public static function importPackage($package, $subpackage)
	{
		$subpackagePath = ($subpackage ? $subpackage . '/' : '');

		$path = BASEPATH_PACKAGES . '/' . $package . '/' . SIDE . '/' . $subpackagePath;
		$path .= ucfirst($package) . ucfirst($subpackage) . 'Controller.php';

		if (is_file($path))
			include_once $path;
		else
			die('The requested package not found');
	}
}
