<?php
namespace System;

final class Upload
{
	private function __construct(){}

	public static function fromDataSet($data, $root = 'root', $element = 'element', $newline = "\n", $tab = "\t")
	{
		$data = (array)$data;
		$xml = '<' . $root . '>' . $newline;

		foreach ($data as $row)
		{
			$xml .= $tab . '<' . $element . '>' . $newline;

			foreach ($row as $key => $value)
				$xml .= $tab . $tab . '<' . $key . '>' . static::safe($value) . '</' . $key . '>' . $newline;

			$xml .= $tab . '</' . $element . '>' . $newline;
		}

		$xml .= '</' . $root . '>' . $newline;

		return $xml;
	}

	public static function toArray($xml)
	{
		$object = static::toObject($xml);
		$array = Arr::fromObject($object);

		return $array;
	}

	public static function toObject($xml)
	{
		$xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
		$json = json_encode($xml);

		// preven json_encode converts empty value to array.
		$json = str_replace('{}', '""', $json);

		$object = json_decode($json);

		return $object;
	}

	public static function safe($str, $protectAll = false)
	{
		$temp = '__TEMP_AMPERSANDS__';

		// Replace entities to temporary markers so that
		// ampersands won't get messed up
		$str = preg_replace('/&#(\d+);/', $temp.'\\1;', $str);

		if ($protectAll === true)
			$str = preg_replace('/&(\w+);/', $temp.'\\1;', $str);

		$search = array('&', '<', '>', '"', "'", '-');
		$replace = array('&amp;', '&lt;', '&gt;', '&quot;', '&apos;', '&#45;');

		$str = str_replace($search, $replace, $str);

		// Decode the temp markers back to entities
		$str = preg_replace('/'.$temp.'(\d+);/', '&#\\1;', $str);

		if ($protectAll === true)
			return preg_replace('/'.$temp.'(\w+);/', '&\\1;', $str);

		return $str;
	}
}
