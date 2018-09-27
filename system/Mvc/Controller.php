<?php
namespace System\Mvc;

use System\Arr;
use System\Request;
use System\Response;

abstract class Controller
{
	public function __call($method, $args)
	{
		$view = new View();
		$view->display();
	}

	public function preInit()
	{
	}

	public function init()
	{
		$excludes = explode(',', BACKEND_SPA_MODE_EXCLUDES);
		$search = PACKAGE . SUBPACKAGE . '.' . ACTION;

		switch (SIDE)
		{
			case 'frontend':

				if (FRONTEND_SPA_MODE and Arr::has($excludes, $search, true) == false)
					Request::ensureIsAjax();

				break;

			case 'backend':

				if (BACKEND_SPA_MODE and Arr::has($excludes, $search, true) == false)
					Request::ensureIsAjax();

				break;
		}
	}

	public function end()
	{
	}

	public static function redirect($url)
	{
		Response::redirect($url);
	}

	public function page_not_found()
	{
		$view = new View(__CLASS__);
		$view->render('404');
	}

	public static function setCache($lifeTime)
	{
		new Cache($lifeTime);

		$cachePath = Cache::$cachePath;
		$cacheFile = Cache::$cacheFile;

		$lifeTime = (int)$lifeTime;

		if ($lifeTime > 0)
		{
			if (File::exists($cachePath.$cacheFile))
			{
				$filemtime = filemtime($cachePath.$cacheFile);
				
				if (time()-$filemtime > $lifeTime)
				{
					File::delete($cachePath.$cacheFile);
					return;
				}
			}

			if (File::exists($cachePath.$cacheFile))
			{
				$view = new View();
				$view->render($cacheFile);
				exit;
			}
		}
		else
		{
			File::delete($cachePath.$cacheFile);
			return;
		}
	}

	public static function clearCache($package=null, $subPackage=null, $action=null)
	{
		if ($package === null)
			$package = PACKAGE;

		if ($subPackage === null)
			$subPackage = SUBPACKAGE;

		if ($action === null)
			$action = ACTION;

		$cachePath = Cache::$cachePath;
		$cacheFile = md5($package.$subPackage.$action);

		$d = dir($cachePath);
		while (($fileName = $d->read()) !== false)
		{
			if (strpos(strtolower($fileName), EXT) === false)
				continue;

			if (substr($fileName, 0, 32) == $cacheFile)
				File::delete($cachePath.$fileName);
		}
	}
}
