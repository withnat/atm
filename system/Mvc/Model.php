<?php
namespace System\Mvc;

use System\Arr;
use System\DB;
use System\File;
use System\Folder;
use System\Auth;

/**
 * TASK is [load|loadAll|activate|deactivate|archive|trash|discontinue|delete|deleteAll]
 *
 * User::[TASK]ById(1);
 * User::[TASK]ByName[And|Or]Email('Nat Withe', 'nat@withnat.com');
 * User::[TASK]ById[Not]GreaterThan(1);
 * User::[TASK]ById[Not]LessThan(1);
 * User::[TASK]ByName[Not]Contain('Wit');
 * User::[TASK]ByName[Not]StartWith('Nat');
 * User::[TASK]ByName[Not]EndWith('Withe');
 * User::[TASK]ById[Not]Between([1,100]);
 * User::[TASK]ById[Not]In([1,100]);
 * User::[TASK]ByNameIs[Not]Null();
 *
 * User::[TASK]Where('name=?', 'Nat Withe');
 * User::[TASK]Where('name=:name', [':name' => 'Nat Withe']);
 *
 * For count|sum|min|max|avg|std|increase|decrease
 *      Product::countByCategoryId(1);                          // count (*)
 *      Product::countIdCategoryId(1);                          // count specific column name
 *      Product::minPriceByCategoryId(1);                       // must specify column name
 *      Product::increaseOrderingByCategoryId($id, $num = 1);   // must specify column name
 *      Product::decreaseOrderingByCategoryId($id, $num = 1);   // must specify column name
 *
 * $user = User::loadById(1);
 * $user->delete();
 *
 * ----------------------------------------
 *
 * -Empty & Truncate table
 *
 * User::deleteAll();   // no where
 * User::truncate();
 *
 * ----------------------------------------
 *
 * -Inserting new record
 *
 * $user = new User();
 * $user->name = 'Nat Withe';
 * $user->[save|bind]();
 *
 * User::[save|bind]($data);
 *
 * ----------------------------------------
 *
 * -Updating data
 *
 * User::where($where)->[save|bind]($data);
 *
 * $user = User::load($id);
 * $user->name = 'Nat Withe';
 * $user->[save|bind]();
 */
class Model
{
	protected static $_instance = null;
	protected static $_table;
	private static $_modelLocations = [];

	protected static $_tasks = ['load', 'increase', 'decrease', 'activate',
							'deactivate', 'archive', 'trash', 'discontinue',
							'delete', 'truncate', 'count', 'sum', 'min', 'max', 'avg', 'std'];
	protected static $_scopes = ['All', ''];
	//protected static $_bys = ['By', ''];
	protected static $_bys = [''];

	protected static $error;

	public function __construct()
	{
		static::_setTable();

		$table = static::_getTable();
		$columns = DB::getColumnListing($table);

		static::$_instance[$table] = $this;

		foreach ($columns as $column)
			static::$_instance[$table]->$column = null;

		return static::$_instance[$table];
	}

	private static function _getInstance()
	{
		$table = static::_getTable();

		if (isset(static::$_instance[$table]) == false)
			new static();

		return static::$_instance[$table];
	}

	public static function table($table)
	{
		static::$_table = $table;

		return static::_getInstance();
	}

	private static function _getTable()
	{
		if (empty(static::$table))
			static::_setTable();
		else
			static::$_table = static::$table;

		return static::$_table;
	}

	private static function _setTable()
	{
		if (empty(static::$_table))
			static::$_table = get_called_class();
	}

    public static function __callStatic($method, $args)
    {
	    return static::_processCall($method, $args);
    }

	public function __call($method, $args)
	{
		return static::_processCall($method, $args);
	}

	private static function _processCall($method, $args)
	{
		$tasks = static::$_tasks;
		$scopes = static::$_scopes;
		$bys = static::$_bys;

		foreach ($tasks as $task)
		{
			foreach ($scopes as $scope)
			{
				foreach ($bys as $by)
				{
					$pattern = $task . $scope . $by;

					if (substr($method, 0, strlen($pattern)) == $pattern)
					{
						$column = substr($method, strlen($pattern));
						return static::{'_' . $task}($column, $args, $scope);
				    }
				}
			}
		}

		return false;
	}

	private static function _extractColumn($column)
	{
		$column = ltrim($column, 'By');
		$column = 'And' . $column;
		$column = str_replace('And', ',And', $column);
		$column = str_replace('Or', ',Or', $column);

		$columns = explode(',', $column);
		array_splice($columns, 0, 1);

		return $columns;
	}

	private static function _load($column, $args, $scope)
	{
		$table = static::_getTable();

		if ($column == 'Where')
			static::_buildWhere($column, $args);
		else
		{
			$columns = DB::getColumnListing($table);

			if (in_array('status', $columns))
			{
				$cs = static::_extractColumn($column);

				$autoWhereStatus = true;
				$seeklist = ['StatusGreaterThan',
					'StatusLessThan',
					'StatusContain',
					'StatusStartWith',
					'StatusEndWith',
					'StatusBetween',
					'StatusIn',
					'StatusIsNull',
					'Status',
					'StatusNotGreaterThan',
					'StatusNotLessThan',
					'StatusNotContain',
					'StatusNotStartWith',
					'StatusNotEndWith',
					'StatusNotBetween',
					'StatusNotIn',
					'StatusIsNull',
					'StatusNot'];

				foreach ($cs as $c)
				{
					if (substr($c, 0, 3) == 'And')
						$c = substr($c, 3);
					elseif (substr($c, 0, 2) == 'Or')
						$c = substr($c, 2);

					if (in_array($c, $seeklist))
					{
						$autoWhereStatus = false;
						break;
					}
				}

				if ($autoWhereStatus)
				{
					if ($column)
					{
						DB::where(function () use ($column, $args)
						{
							static::_buildWhere($column, $args);
						});
					}

					if (SIDE == 'backend')
						DB::where('status', '>', 0);
					else
						DB::where('status', 2);
				}
				elseif ($column)
					static::_buildWhere($column, $args);
			}
			elseif ($column)
				static::_buildWhere($column, $args);
		}

		$data = DB::table($table)->{'load' . $scope}();

		if ($scope == 'All')
			return $data;
		else
		{
			static::_getInstance();
			$instance = static::$_instance[$table];

			foreach ($data as $key => $value)
				$instance->$key = $value;

			return $instance;
		}
	}

	private static function _activate($column, $args)
	{
		DB::table(static::_getTable());
		static::_buildWhere($column, $args);

		return DB::activate();
	}

	private static function _deactivate($column, $args)
	{
		DB::table(static::_getTable());
		static::_buildWhere($column, $args);

		return DB::deactivate();
	}

	private static function _archive($column, $args)
	{
		DB::table(static::_getTable());
		static::_buildWhere($column, $args);

		return DB::archive();
	}

	private static function _trash($column, $args)
	{
		DB::table(static::_getTable());
		static::_buildWhere($column, $args);

		return DB::trash();
	}

	private static function _discontinue($column, $args)
	{
		DB::table(static::_getTable());
		static::_buildWhere($column, $args);

		return DB::discontinue();
	}

	private static function _increase($column, $args)
	{
		$arr = explode('By', $column);
		$column = static::_formatColumnName($arr[0]);

		DB::table(static::_getTable());
		static::_buildWhere($arr[1], $args);

		return DB::increase($column, Arr::last($args));
	}

	private static function _decrease($column, $args)
	{
		$arr = explode('By', $column);
		$column = static::_formatColumnName($arr[0]);

		DB::table(static::_getTable());
		static::_buildWhere($arr[1], $args);

		if ($arr[1] == 'Where')
		{
			if (strpos($args[0], ':') === false)
				$sign = ':';
			else
				$sign = '?';

			$markers = explode($sign, $args[0]);
			pr($markers);

			if (strpos($args[0], ':') === false)
			{
				$args = Arr::flatten($args);

				// Remove where string (ie id=?) from first element
				array_shift($args);
			}
			else
			{
				// We need associative array so don't need to flatten it.
				$args = @$args[1];
			}
		}
		else
		{
			$columns = static::_extractColumn($arr[1]);

			// Product::decreaseOrderingById(1, 2, 3);
			// $num should be 2 not 3
			if (count($args) > count($columns))
				$num = $args[count($columns)];
			else
				$num = 1;
		}

		return DB::decrease($column, $num);
	}

	private static function _delete($column, $args)
	{
		DB::table(static::_getTable());
		static::_buildWhere($column, $args);

		return DB::delete(true);
	}

	private static function _truncate()
	{
		return DB::table(static::_getTable())->truncate(true);
	}

	private static function _count($column, $args)
	{
		$arr = explode('By', $column);
		DB::table(static::_getTable());

		if (count($arr) > 1)
		{
			$column = static::_formatColumnName($arr[0]);
			static::_buildWhere($arr[1], $args);
		}
		else
		{
			$column = '*';
			static::_buildWhere($arr[0], $args);
		}

		return DB::count($column);
	}

	private static function _sum($column, $args)
	{
		$arr = explode('By', $column);
		$column = static::_formatColumnName($arr[0]);

		DB::table(static::_getTable());
		static::_buildWhere($arr[1], $args);

		return DB::sum($column);
	}

	private static function _min($column, $args)
	{
		$arr = explode('By', $column);
		$column = static::_formatColumnName($arr[0]);

		DB::table(static::_getTable());
		static::_buildWhere($arr[1], $args);

		return DB::min($column);
	}

	private static function _max($column, $args)
	{
		$arr = explode('By', $column);
		$column = static::_formatColumnName($arr[0]);

		DB::table(static::_getTable());
		static::_buildWhere($arr[1], $args);

		return DB::max($column);
	}

	private static function _avg($column, $args)
	{
		$arr = explode('By', $column);
		$column = static::_formatColumnName($arr[0]);

		DB::table(static::_getTable());
		static::_buildWhere($arr[1], $args);

		return DB::avg($column);
	}

	private static function _std($column, $args)
	{
		$arr = explode('By', $column);
		$column = static::_formatColumnName($arr[0]);

		DB::table(static::_getTable());
		static::_buildWhere($arr[1], $args);

		return DB::std($column);
	}

	private static function _buildWhere($column, $args)
	{
		// Product::loadWhere('{id}=?', 1);
		if ($column == 'Where')
		{
			$where = $args[0];

			if (strpos($args[0], ':') === false)
			{
				$args = Arr::flatten($args);

				// Remove where string (ie id=?) from first element
				array_shift($args);
			}
			else
			{
				// We need associative array so don't need to flatten it.
				$args = @$args[1];
			}

			DB::where($where, $args);
		}
		// Product::loadById(1);
		else
		{
			$columns = static::_extractColumn($column);

			foreach ($columns as $index => $column)
			{
				$column = $columns[$index];
				$value = @$args[$index]; // not provide $args, leave it null

				if (substr($column, 0, 3) == 'And')
				{
					$method = 'where';
					$column = substr($column, 3);
				}
				elseif (substr($column, 0, 2) == 'Or')
				{
					$method = 'orWhere';
					$column = substr($column, 2);
				}

				if (substr($column, -14) == 'NotGreaterThan')
				{
					$column = rtrim($column, 'NotGreaterThan');
					$column = static::_formatColumnName($column);
					DB::{$method}($column, '<=', $value);
				}
				elseif (substr($column, -11) == 'GreaterThan')
				{
					$column = rtrim($column, 'GreaterThan');
					$column = static::_formatColumnName($column);
					DB::{$method}($column, '>', $value);
				}
				elseif (substr($column, -11) == 'NotLessThan')
				{
					$column = rtrim($column, 'NotLessThan');
					$column = static::_formatColumnName($column);
					DB::{$method}($column, '>=', $value);
				}
				elseif (substr($column, -8) == 'LessThan')
				{
					$column = rtrim($column, 'LessThan');
					$column = static::_formatColumnName($column);
					DB::{$method}($column, '<', $value);
				}
				elseif (substr($column, -10) == 'NotContain')
				{
					$column = rtrim($column, 'NotContain');
					$column = static::_formatColumnName($column);

					if (is_array($value) or is_object($value))
					{
						DB::{$method}(function () use ($column, $value)
						{
							foreach ($value as $v)
								DB::whereNotContain($column, $v);
						});
					}
					else
						DB::{$method . 'NotContain'}($column, $value);
				}
				elseif (substr($column, -7) == 'Contain')
				{
					$column = rtrim($column, 'Contain');
					$column = static::_formatColumnName($column);

					if (is_array($value) or is_object($value))
					{
						DB::{$method}(function () use ($column, $value)
					{
						foreach ($value as $v)
							DB::orWhereContain($column, $v);
					});
					}
					else
						DB::{$method . 'Contain'}($column, $value);
				}
				elseif (substr($column, -12) == 'NotStartWith')
				{
					$column = rtrim($column, 'NotStartWith');
					$column = static::_formatColumnName($column);

					if (is_array($value) or is_object($value))
					{
						DB::{$method}(function () use ($column, $value)
						{
							foreach ($value as $v)
								DB::whereNotStartWith($column, $v);
						});
					}
					else
						DB::{$method . 'NotStartWith'}($column, $value);
				}
				elseif (substr($column, -9) == 'StartWith')
				{
					$column = rtrim($column, 'StartWith');
					$column = static::_formatColumnName($column);

					if (is_array($value) or is_object($value))
					{
						DB::{$method}(function () use ($column, $value)
					{
						foreach ($value as $v)
							DB::orWhereStartWith($column, $v);
					});
					}
					else
						DB::{$method . 'StartWith'}($column, $value);
				}
				elseif (substr($column, -10) == 'NotEndWith')
				{
					$column = rtrim($column, 'NotEndWith');
					$column = static::_formatColumnName($column);

					if (is_array($value) or is_object($value))
					{
						DB::{$method}(function () use ($column, $value)
						{
							foreach ($value as $v)
								DB::whereNotEndWith($column, $v);
						});
					}
					else
						DB::{$method . 'NotEndWith'}($column, $value);
				}
				elseif (substr($column, -7) == 'EndWith')
				{
					$column = rtrim($column, 'EndWith');
					$column = static::_formatColumnName($column);

					if (is_array($value) or is_object($value))
					{
						DB::{$method}(function () use ($column, $value)
						{
							foreach ($value as $v)
								DB::orWhereEndWith($column, $v);
						});
					}
					else
						DB::{$method . 'EndWith'}($column, $value);
				}
				elseif (substr($column, -10) == 'NotBetween')
				{
					$column = rtrim($column, 'NotBetween');
					$column = static::_formatColumnName($column);
					DB::{$method . 'NotBetween'}($column, Arr::first($value), Arr::last($value));
				}
				elseif (substr($column, -7) == 'Between')
				{
					$column = rtrim($column, 'Between');
					$column = static::_formatColumnName($column);
					DB::{$method . 'Between'}($column, Arr::first($value), Arr::last($value));
				}
				elseif (substr($column, -5) == 'NotIn')
				{
					$column = rtrim($column, 'NotIn');
					$column = static::_formatColumnName($column);
					DB::{$method . 'NotIn'}($column, $value);
				}
				elseif (substr($column, -2) == 'In')
				{
					$column = rtrim($column, 'In');
					$column = static::_formatColumnName($column);
					DB::{$method . 'In'}($column, $value);
				}
				elseif (substr($column, -9) == 'IsNotNull')
				{
					$column = rtrim($column, 'IsNotNull');
					$column = static::_formatColumnName($column);
					DB::{$method . 'NotNull'}($column);
				}
				elseif (substr($column, -6) == 'IsNull')
				{
					$column = rtrim($column, 'IsNull');
					$column = static::_formatColumnName($column);
					DB::{$method . 'Null'}($column);
				}
				elseif (substr($column, -3) == 'Not')
				{
					$column = rtrim($column, 'Not');
					$column = static::_formatColumnName($column);
					DB::{$method}($column, '!=', $value);
				}
				else
				{
					$column = static::_formatColumnName($column);
					DB::{$method}($column, $value);
				}
			}
		}
		/*
		elseif ($args)
		{
			// Don't need to format column name.
			// Maybe column name is where statement
			// ie. id=? or id=:id etc
			$column = $args[0];
			array_shift($args);
			DB::where($column, $args);
		}
		*/
	}

	public static function __loadWhere($where)
	{
		$args = func_get_args();

		if (strpos($args[0], ':') === false)
		{
			$args = Arr::flatten($args);
			// Remove where string (ie id=?) from first element
			array_shift($args);
		}
		else
		{
			// We need associative array so don't need to flatten it.
			$args = @$args[1];
		}

		return DB::table(static::_getTable())->where($where, $args)->load();
	}

	public static function __loadAllWhere($where)
	{
		$args = func_get_args();

		if (strpos($args[0], ':') === false)
		{
			$args = Arr::flatten($args);
			// Remove where string (ie id=?) from first element
			array_shift($args);
		}
		else
		{
			// We need associative array so don't need to flatten it.
			$args = @$args[1];
		}

		return DB::table(static::_getTable())->where($where, $args)->loadAll();
	}

	/*public static function deleteAll()
	{
        return DB::table(static::_getTable())->deleteAll();
	}*/

	// abstract method
	public static function rules()
	{
		return [];
	}

	public static function errorInfo()
	{
		return static::$error;
	}

	private static function _validate()
	{
		$columns = static::rules();

		foreach ($columns as $column => $validate)
		{
			$label = '';
			$rules = [];

			if (is_array($validate))
			{
				foreach ($validate as $key => $value)
				{
					$key = strtolower(trim($key));

					if ($key == 'label')
					{
						$label = $value;
						continue;
					}

					if (is_array($value))
					{
						$rules[$key]['value'] = trim($value[0]);
						$rules[$key]['message'] = trim(@$value[1]);
					}
					else
					{
						$rules[$key]['value'] = trim($value);
						$rules[$key]['message'] = '';
					}
				}
			}
			else
			{
				$arr1s = explode('|', $validate);

				foreach ($arr1s as $arr1)
				{
					$arr2s = explode(':', $arr1);
					$arr2s = array_map('trim', $arr2s);
					$arr2s[0] = strtolower($arr2s[0]);

					if ($arr2s[0] == 'label')
					{
						$label = @$arr2s[1];
						continue;
					}

					if ($arr2s[0] == 'required')
					{
						$rules['required']['value'] = true;
						$rules['required']['message'] = @$arr2s[1];
					}
					elseif ($arr2s[0] == 'unique')
					{
						$rules['unique']['value'] = true;
						$rules['unique']['message'] = @$arr2s[1];
					}
					elseif (in_array($arr2s[0], ['alnum', 'num', 'number', 'int', 'integer', 'email', 'ip', 'ipv4', 'ipv6', 'url']))
					{
						$rules['type']['value'] = $arr2s[0];
						$rules['type']['message'] = @$arr2s[1];
					}
					else
					{
						$rules[$arr2s[0]]['value'] = @$arr2s[1];
						$rules[$arr2s[0]]['message'] = @$arr2s[2];
					}
				}
			}

			if ($label == '')
				$label = $column;

			//

			$orderedRules = [];
			$orderedKeys = ['required', 'alnum', 'num', 'number', 'int', 'integer', 'email', 'ip', 'ipv4', 'ipv6', 'url',
						'min', 'max', 'between', 'minlength', 'maxlength', 'length', 'equalto', 'is', 'not',
						'contain', 'notcontain', 'startwith', 'notstartwith', 'endwith', 'notendwith', 'unique'];

			foreach ($orderedKeys as $key)
			{
				if (array_key_exists($key, $rules))
					$orderedRules[$key] = $rules[$key];
			}

			$rules = $orderedRules;

			//

			foreach ($rules as $rule => $value)
			{
				$data = trim(@static::$_writeData[$column]);

				$error = '';
				$spec = $value['value'];
				$message = $value['message'];

				// Required
				if ($rule == 'required' and $spec and mb_strlen($data) == 0)
					$error = $label . ($message ? $message : ' is required');

				elseif ($rule == 'requiredif')
				{
					if (strpos($data, '='))
					{
						$arr = explode('=', $data);
					}
					$error = '<li>' . $label . ($message ? $message : ' is required') . '</li>';
					continue;
				}

				// Data type
				elseif ($rule == 'alnum' and $data and ctype_alnum($data) == false)
					$error = $label . ($message ? $message : ' not alnum');

				elseif (in_array($rule, ['num', 'number']) and $data and is_numeric($data) == false)
					$error = $label . ($message ? $message : ' not number');

				elseif (in_array($rule, ['int', 'integer']) and $data and preg_match('/^\d+$/', $data))
					$error = $label . ($message ? $message : ' not int');

				elseif ($rule == 'email' and $data and filter_var($data, FILTER_VALIDATE_EMAIL) == false)
					$error = $label . ($message ? $message : ' not email');

				elseif ($rule == 'ip' and $data and filter_var($data, FILTER_VALIDATE_IP) == false)
					$error = $label . ($message ? $message : ' not ip');

				elseif ($rule == 'ipv4' and $data and filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) == false)
					$error = $label . ($message ? $message : ' not ipv4');

				elseif ($rule == 'ipv6' and $data and filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) == false)
					$error = $label . ($message ? $message : ' not ipv6');

				elseif ($rule == 'url' and $data and filter_var($data, FILTER_VALIDATE_URL) == false)
					$error = $label . ($message ? $message : ' not url');

				// Comparison
				elseif ($rule == 'min' and $data and $data < $spec)
					$error = $label . ($message ? $message : ' min is ' . $spec);

				elseif ($rule == 'max' and $data and $data > $spec)
					$error = $label . ($message ? $message : ' max is ' . $spec);

				elseif ($rule == 'between' and $data)
				{
					$arr = explode(',', $spec);
					$min = trim($arr[0]);
					$max = trim(@$arr[1]);

					if ($data < $min or $data > $max)
						$error = $label . ($message ? $message : ' must between ' . $min . ' and ' . $max);
				}

				elseif ($rule == 'minlength' and $data and strlen($data) < $spec)
					$error = $label . ($message ? $message : ' minlen ' . $spec);

				elseif ($rule == 'maxlength' and $data and strlen($data) > $spec)
					$error = $label . ($message ? $message : ' maxlen ' . $spec);

				elseif ($rule == 'length'and $data )
				{
					$arr = explode(',', $spec);
					$min = trim($arr[0]);
					$max = trim(@$arr[1]);

					if (strlen($data) < $min or strlen($data) > $max)
						$error = $label . ($message ? $message : ' length must between ' . $min . ' and ' . $max);
				}

				elseif ($rule == 'equalto' and $data != trim(@static::$_writeData[$spec]))
					$error = $label . ($message ? $message : ' must same as ' . $spec);

				// other
				elseif ($rule == 'is' and $data != $spec)
					$error = $label . ($message ? $message : ' must be ' . $spec);

				elseif ($rule == 'not' and $data == $spec)
					$error = $label . ($message ? $message : ' cannot be ' . $spec);

				elseif ($rule == 'contain' and mb_stripos($spec, $data) === false)
					$error = $label . ($message ? $message : ' must contain ' . $spec);

				elseif ($rule == 'notcontain' and mb_stripos($spec, $data) !== false)
					$error = $label . ($message ? $message : ' must not contain ' . $spec);

				elseif ($rule == 'startwith' and mb_substr($data, 0, mb_strlen($spec)) != $spec)
					$error = $label . ($message ? $message : ' must startwith ' . $spec);

				elseif ($rule == 'notstartwith' and mb_substr($data, 0, mb_strlen($spec)) == $spec)
					$error = $label . ($message ? $message : ' must not startwith ' . $spec);

				elseif ($rule == 'endwith' and mb_substr($data, (0 - mb_strlen($spec))) != $spec)
					$error = $label . ($message ? $message : ' must endwith ' . $spec);

				elseif ($rule == 'notendwith' and mb_substr($data, (0 - mb_strlen($spec))) == $spec)
					$error = $label . ($message ? $message : ' must not endwith ' . $spec);

				elseif ($rule == 'unique' and $spec and $data)
				{
					$dataSet[$column] = $data;
					/*
					$arr = explode(',', $spec);

					foreach ($arr as $key)
					{
						if (trim($key))
							$dataSet[$key] = @static::$_readData[$key];
					}
					*/

					if (DB::table(static::$_table)->duplicate($dataSet, static::$_readData['id']))
						$error = $label . ($message ? $message : ' is exists.');

					unset($dataSet); // Don't forget to reset variable for each loop
				}

				if ($error)
				{
					static::$error .= '<li>' . $error . '</li>';
					break;
				}
			}
		}

		if (static::$error)
		{
			static::$error = '<ul>' . static::$error . '</ul>';
			return false;
		}

		return true;
	}

	public static function exists($data = null)
	{
		return DB::table(static::_getTable())->exists($data, true);
	}

	public static function bind($data)
	{
		foreach ($data as $key => $value)
			static::$_instance[static::_getTable()]->$key = $value;
	}

	public static function save()
	{

//		if (static::_validate() == false)
//		{
//			echo static::$error;
//			return false;
//		}

		$instance = static::$_instance[static::_getTable()];
		$columns = DB::getColumnListing(static::$_table);

		if (@$instance->id)
		{
			DB::where($instance->id);

			if (in_array('updated', $columns))
				$instance->updated = date('Y-m-d H:i:s');

			if (in_array('updater', $columns))
				$instance->updater = (int)Auth::identity()->id;

			DB::table(static::$_table)->update($instance);
		}
		else
		{
			if (in_array('ordering', $columns))
				$instance->ordering = DB::table(static::$_table)->getNewOrdering();

			if (in_array('created', $columns))
				$instance->created = date('Y-m-d H:i:s');

			if (in_array('creator', $columns))
				$instance->creator = (int)Auth::identity()->id;

			DB::table(static::$_table)->insert($instance);
			$instance->id = DB::getLastInsertId();
		}

		return true;
	}

	private static function _formatColumnName($column)
	{
		return lcfirst($column);
	}

	public static function getModelLocation($class) // ok ล่าสุด
	{
		if (isset(static::$_modelLocations[$class]) == false)
		{
			$tempFile = BASEPATH_TMP.'/cache/models.php';

			// Need to create new file to replace the old one everytime in development mode
			// because cannot use the same method as deleting/creating temp table files in
			// class database because when model class is called, this method, getModelLocation()
			// will be executed via autoload function. That means temp model location files
			// will be created, but, these file will be deleted immediately after model object created
			// via model->__construct() method.

			if (File::exists($tempFile) == false or DEV_MODE)
				static::_loadModelLocationToTempFile();

			static::$_modelLocations = static::_loadModelLocationFromTempFile();
		}

		return @static::$_modelLocations[$class];
	}

	private static function _loadModelLocationToTempFile() // ok ล่าสุด
	{
		$tempFile = BASEPATH_TMP.'/cache/models.php';
		$packagePath = BASEPATH_PACKAGES;
		$packageEntries = scandir($packagePath);

		$content = [];

		foreach ($packageEntries as $packageEntry)
		{
			if (Folder::exists($packagePath.'/'.$packageEntry) == false)
				continue;

			$modelPath = $packagePath.'/'.$packageEntry.'/models';

			if (Folder::exists($modelPath))
			{
				$modelEntries = scandir($modelPath);

				foreach ($modelEntries as $modelEntry)
				{
					if (mb_stripos($modelEntry, '.php') === false)
						continue;

					$modelName = File::getNameWithoutExtension($modelEntry);
					$modelLocation = $modelPath.'/'.$modelEntry;

					$content[$modelName] = $modelLocation;
				}
			}
		}

		$fp = fopen($tempFile, 'w');
		fwrite($fp, '<?php //'.serialize($content));
	}

	private static function _loadModelLocationFromTempFile() // ok ล่าสุด
	{
		$tempFile = BASEPATH_TMP.'/cache/models.php';

		$content = file_get_contents($tempFile);
		$content = substr($content, 8);
		$content = @unserialize($content);

		if ($content === false)
		{
			static::_loadModelLocationToTempFile();
			$content = static::_loadModelLocationFromTempFile();
		}

		return $content;
	}
}
