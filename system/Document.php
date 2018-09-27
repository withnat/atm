<?php
namespace System;

final class Document
{
	private static $_generator = 'Vanda';
	private static $_title;
	private static $_metakey;
	private static $_metadesc;
	private static $_metadata = [];

	public static function getGenerator()
	{
		return static::$_generator;
	}

	public static function setGenerator($content)
	{
		static::$_generator = $content;
	}

	public static function getTitle()
	{
		return static::$_title;
	}

	public static function setTitle($title)
	{
		static::$_title = $title;
	}

	public function getPageTitle()
	{
		return static::$_pagetitle;
	}

	public function setPageTitle($title)
	{
		static::$_pagetitle = $title;
	}

	public static function getMetaKey()
	{
		return static::$_metakey;
	}

	public static function setMetaKey($metakey)
	{
		static::$_metakey = $metakey;
	}

	public static function getMetaDesc()
	{
		return static::$_metadesc;
	}

	public static function setMetaDesc($metadesc)
	{
		static::$_metadesc = $metadesc;
	}

	public static function getMetaData()
	{
		return static::$_metadata;
	}

	public static function setMetaData($name, $content, $httpEquiv = false)
	{
		$name = trim(strtolower($name));

		switch ($name)
		{
			case 'generator':
				static::setGenerator($content);
				break;

			case 'keywords':
				static::setMetaKey($content);
				break;

			case 'description':
				static::setMetaDesc($content);
				break;

			default:
				$key = ($httpEquiv ? 'http-equiv' : 'name');
				static::$_metadata[] = '<meta ' . $key . '="' . $name . '" content="' . $content . '" />';
		}
	}

	public static function setRawMetaData($metadata)
	{
		static::$_metadata[] = $metadata;
	}
}
