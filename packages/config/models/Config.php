<?php
use System\DB;

class Config extends System\Mvc\Model
{
	public $table = 'Config';
	protected static $_instance;
	protected static $package;
	protected static $configs;

	public static function rules()
	{
		$rules = [
			'sitename' => 'required',
			'pagesize' => 'required',
			'backendpath' => 'required',
			'backendtemplate' => 'required',
			'lifetime' => 'required|number',

			'bn20' => 'required|number',
			'bn50' => 'required|number',
			'bn100' => 'required|number',
			'bn500' => 'required|number',
			'bn1000' => 'required|number'
		];

		return $rules;
	}

	private static function _getInstance()
	{
		if (is_null(static::$_instance))
			static::$_instance = new static;

		return static::$_instance;
	}

	public static function package($package)
	{
		static::$package = $package;

		return static::_getInstance();
	}

	public static function get($key, $default = null)
	{
		if (isset(static::$configs[static::$package]) == false)
		{
			DB::table('Config');

			if (static::$package)
				DB::where('package', static::$package);
			else
				DB::where('package', '')->orWhereNull('package');

			$rows = DB::loadAll();

			foreach ($rows as $row)
				static::$configs[static::$package][$row->key] = $row->value;
		}

		$value = @static::$configs[static::$package][$key];

		if ($value == '' and $default)
			$value = $default;

		return $value;
	}
}
