<?php
namespace System\Language;

class Factory
{
	public static $language = null;

	public static function getLanguage()
	{
		if (!self::$language)
			self::$language = self::createLanguage();

		return self::$language;
	}

	protected static function createLanguage()
	{
		$lang = Language::getInstance();

		return $lang;
	}
}
