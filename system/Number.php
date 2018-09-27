<?php
namespace System;

final class Number
{
	public static function byteFormat($size, $precision = 1, $unit = null) // ok
	{
		$unit = strtoupper(trim($unit));

		if ($unit == '')
			$unit = static::getUnitByFileSize($size);

		$size = static::calFileSizeByUnit($size, $unit);
		$size = number_format($size, $precision);

		// Remove useless zero digits.
		// Using $size += 0 statement will remove comma (,)
		// ie. 1,469.0 + 0 = 1469
		$arr = explode('.', $size);

		if (isset($arr[1]) and (int)$arr[1] == 0)
			$size = $arr[0];

		$size .= ' '.$unit;

		return $size;
	}

	public static function getUnitByFileSize($size) // ok
	{
		$size = (int)$size;

		if ($size < 1024)
			$unit = 'B';
		elseif ($size < 1048576)
			$unit = 'KB';
		elseif ($size < 1073741824)
			$unit = 'MB';
		elseif ($size < 1099511627776)
			$unit = 'GB';
		else
			$unit = 'TB';

		return $unit;
	}

	public static function calFileSizeByUnit($size, $unit) // ok
	{
		$size = (int)$size;
		$unit = strtoupper(trim($unit));

		switch ($unit)
		{
			case 'KB':
				$size = $size / 1024;
				break;
			case 'MB':
				$size = $size / 1048576;
				break;
			case 'GB':
				$size = $size / 1073741824;
				break;
			case 'TB':
				$size = $size / 1099511627776;
				break;
		}

		return $size;
	}

	public static function toText()
	{
	}

	public static function toCurrency()
	{
	}
}
