<?php
namespace System;

use System\Str;

final class Data
{
	private function __construct(){}

	public static function push($data, $key, $value)
	{
		if (is_array($data))
			$data[$key] = $value;
		else
			$data->$key = $value;

		return $data;
	}
}
