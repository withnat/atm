<?php
namespace System;

final class Cache
{
	public static $lifeTime;
	public static $cachePath = 'tmp/cache/pages/'; // need to assign value this line because Controller::clearCache() maybe called before Controller::setCache that $cachePath still empty.
	public static $cacheFile;

	public function __construct($lifeTime)
	{
		self::$lifeTime = $lifeTime;
		self::$cacheFile = md5(PACKAGE.SUBPACKAGE.ACTION).Paginator::get('pagesize').Paginator::get('page').EXT;
	}
}
