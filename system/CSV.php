<?php
namespace System;

final class CSV
{
	private function __construct(){}

	public static function fromDataSet($data, $delimiter = ',', $newline = "\n", $enclosure = '"')
	{
		$data = (array)$data;
		$csv = '';

		foreach ($data as $row)
		{
			foreach ($row as $key => $value)
				$csv .= $enclosure . static::safe($value, $enclosure) . $enclosure . $delimiter;

			$csv = substr($csv, 0, (0-strlen($delimiter))) . $newline;
		}

		return $csv;
	}

	public static function toArray($csv, $delimiter = ',', $newline = "\n", $enclosure = '"')
	{
		$lines = explode($newline, $csv);
		$data = [];

		foreach ($lines as $line)
		{
			if ($line)
			{
				$columns = explode($delimiter, $line);

				foreach ($columns as $key => $value)
				{
					$value = substr($value, strlen($enclosure));
					$value = substr($value, 0, (0-strlen($enclosure)));
					$value = str_replace($enclosure . $enclosure, $enclosure, $value);

					$columns[$key] = $value;
				}

				$data[] = $columns;
			}
		}

		return $data;
	}

	public static function toAssoc($csv, $delimiter = ',', $newline = "\n", $enclosure = '"')
	{
		$lines = explode($newline, $csv);
		$header = null;
		$data = [];

		foreach ($lines as $line)
		{
			if ($line)
			{
				$columns = explode($delimiter, $line);

				foreach ($columns as $key => $value)
				{
					$value = substr($value, strlen($enclosure));
					$value = substr($value, 0, (0-strlen($enclosure)));
					$value = str_replace($enclosure . $enclosure, $enclosure, $value);

					$columns[$key] = $value;
				}

				if (is_null($header))
					$header = $columns;
				else
					$data[] = array_combine($header, $columns);
			}
		}

		return $data;
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

	public static function safe($str, $enclosure)
	{
		$str = str_replace($enclosure, $enclosure . $enclosure, $str);

		return $str;
	}
}
