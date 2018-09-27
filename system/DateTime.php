<?php
namespace System;
/*
// Create two timezone objects, one for Taipei (Taiwan) and one for
// Tokyo (Japan)
$dateTimeZoneTaipei = new DateTimeZone("Asia/Bangkok");
$dateTimeZoneJapan = new DateTimeZone("Asia/Harbin");

// Create two DateTime objects that will contain the same Unix timestamp, but
// have different timezones attached to them.
$dateTimeTaipei = new DateTime("now", $dateTimeZoneTaipei);
$dateTimeJapan = new DateTime("now", $dateTimeZoneJapan);

// Calculate the GMT offset for the date/time contained in the $dateTimeTaipei
// object, but using the timezone rules as defined for Tokyo
// ($dateTimeZoneJapan).
$timeOffset = $dateTimeZoneJapan->getOffset($dateTimeTaipei);

// Should show int(32400) (for dates after Sat Sep 8 01:00:00 1951 JST).
echo ($timeOffset);
*/

final class DateTime extends \DateTime
{
	private $_offset = 0;
	private $_date = null;
	private static $_formats = [
		'Y-m-d',
		'Y-m-d H:i',
		'Y-m-d H:i:s',
		'Y.m.d',
		'Y.m.d H:i',
		'Y.m.d H:i:s',
		'd.m.Y',
		'd.m.Y H:i',
		'd.m.Y H:i:s',
		'd/m/Y',
		'd/m/Y H:i',
		'd/m/Y H:i:s',
		'Ymd',
		'YmdHi',
		'YmdHis'];

	public static function valid($date, $format = null)
	{
		$date = trim($date);

		if ($format)
			$formats = [$format];
		else
			$formats = static::$_formats;

		foreach ($formats as $format)
		{
			$datetime = DateTime::createFromFormat($format, $date);

			if ($datetime and $datetime->format($format) === $date)
				return true;
		}

		return false;
	}

	public static function emptyIfNotValid($date, $format = null)
	{
		if (static::valid($date, $format))
			return $date;
		else
			return null;
	}

//	public static function format($input=null, $dateFormat='d/m/y - H:i', $default='Never')
//	{
//		if ($input == '0000-00-00' or $input == '0000-00-00 00:00:00')
//			$output = $default;
//		else
//		{
//			$timestamp = self::_toTimeStamp($input);
//			$output = date($dateFormat, $timestamp);
//		}
//		return $output;
//	}

	public static function shortDate($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('d/m/Y', $timestamp);
	}

	public static function longDate($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('d F Y', $timestamp);
	}

	public static function shortTime($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('H:i', $timestamp);
	}

	public static function longTime($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('H:i:s', $timestamp);
	}

	public static function fullDateTime($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('d F Y H:i', $timestamp);
	}

	public static function fullLongDateTime($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('d F Y H:i:s', $timestamp);
	}

	public static function defaultDate($input=null, $default='Never')
	{
		if ($input == '0000-00-00' or $input == '0000-00-00 00:00:00')
			$output = $default;
		else
		{
			$timestamp = self::_toTimeStamp($input);
			$output = date('d/m/y', $timestamp);
		}
		return $output;
	}

	public static function defaultDateTime($input=null, $default='Never')
	{
		if ($input == '0000-00-00' or $input == '0000-00-00 00:00:00')
			$output = $default;
		else
		{
			$timestamp = self::_toTimeStamp($input);
			$output = date('d/m/y - H:i', $timestamp);
		}
		return $output;
	}

	public static function defaultLongDateTime($input=null, $default='Never')
	{
		if ($input == '0000-00-00' or $input == '0000-00-00 00:00:00')
			$output = $default;
		else
		{
			$timestamp = self::_toTimeStamp($input);
			$output = date('d/m/Y H:i:s', $timestamp);
		}
		return $output;
	}

	public static function dayMonth($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('d F', $timestamp);
	}

	public static function rfc1123($input=null)
	{
	}

	public static function sortableDateTime($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('Y-m-d H:i:s', $timestamp);
	}

	public static function monthYear($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('F Y', $timestamp);
	}

	public static function day($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('d', $timestamp);
	}

	public static function shortDayName($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('D', $timestamp);
	}

	public static function fullDayName($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('l', $timestamp); // lower case 'L'
	}

	public static function hour($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('g', $timestamp);
	}

	public static function hour24($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('G', $timestamp);
	}

	public static function twoDigitHour($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('h', $timestamp);
	}

	public static function twoDigitHour24($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('H', $timestamp);
	}

	public static function minute($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('i', $timestamp);
	}

	public static function second($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('s', $timestamp);
	}

	public static function month($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('m', $timestamp);
	}

	public static function shortMonthName($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('M', $timestamp);
	}

	public static function monthName($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('F', $timestamp);
	}


	public static function apm($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('A', $timestamp);
	}

	public static function year($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('Y', $timestamp);
	}

	public static function twoDigitYear($input=null)
	{
		$timestamp = self::_toTimeStamp($input);
		return date('y', $timestamp);
	}

	private static function _toTimeStamp($input)
	{
		if (empty($input))
			$input = time();

		if (is_numeric($input) == false)
			$input = strtotime($input);

		return $input;
	}

	public static function toMySQL($local=false)
	{
		if (empty($this->_date))
		{
		}
	}

	public static function toDB($date)
	{
		if (static::valid($date))
		{
		}
	}

	public static function timeZoneMenu($name, $selected = null)
	{
		$defaultTimeZone = date_default_timezone_get();

		$options = [];
		$timestamp = time();

		foreach (timezone_identifiers_list() as $key => $zone)
		{
			date_default_timezone_set($zone);
			$options[] = [$zone => 'UTC ' . date('P', $timestamp) . ' - ' . $zone];
		}

		date_default_timezone_set($defaultTimeZone);

		return Form::select($name, $options, $selected);
	}

	public static function timeZoneRegionMenu($name, $selected = null, $title='')
	{
		$options = [];

		foreach (timezone_identifiers_list() as $zone)
		{
			$region = substr($zone, 0, strpos($zone, '/'));

			if (in_array($region, $options) == false)
				$options[] = $region;
		}

		return Form::select($name, $options, $selected, $title);
	}

	public static function timeZoneCityMenu($name, $selected = null)
	{
		$defaultTimeZone = date_default_timezone_get();

		$options = [];
		$timestamp = time();

		foreach (timezone_identifiers_list() as $key => $zone)
		{
			date_default_timezone_set($zone);
			$options[] = [$zone => 'UTC ' . date('P', $timestamp) . ' - ' . $zone];
		}

		date_default_timezone_set($defaultTimeZone);

		return Form::select($name, $options, $selected);
	}
}
