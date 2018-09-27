<?php
namespace System;

final class JSON
{
	private function __construct(){}

	public static function dataTables($rows)
	{
		//$data = Request::get();
		//$draw = $data->draw;

		$recordsTotal = count($rows);
		$recordsFiltered = count($rows);
		$json = json_encode($rows);

		// preven json_encode converts empty value to array.
		$json = str_replace('{}', '""', $json);

		$json = '
		{
			
			"recordsTotal": ' . $recordsTotal . ',
			"recordsFiltered": ' . $recordsFiltered . ',
			"data": ' . $json . '
		}';

		return $json;
	}
}
