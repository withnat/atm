<?php
namespace System\DB;

use System\Arr;
use System\Auth;
use System\CSV;
use System\Data;
use System\File;
use System\Folder;
use System\Paginator;
use System\Str;
use System\Uri;
use System\XML;

/**
 * private static $_sqlWheres = [
 *  ['operator' => 'AND', 'condition' => 'name=Nat'],
 *  ['operator' => 'OR', 'condition' => '('],
 *  ['operator' => 'AND', 'condition' => 'votes > 100'],
 *  ['operator' => 'AND', 'condition' => 'title <> 'Admin''],
 *  ['operator' => '', 'condition' => ')']
 * ]
 */

abstract class Base
{
	protected static $_instance;
	protected static $_connection;
	protected static $_dbCachePath;
	protected static $_queryCachePath;
	protected static $_tables;
	protected static $_info;
	protected static $_sqlRaw;
	protected static $_sqlSelects;
	protected static $_sqlTable;
	protected static $_sqlJoins;
	protected static $_sqlWheres;
	protected static $_sqlGroups;
	protected static $_sqlSorts;
	protected static $_sqlTake;
	protected static $_sqlSkip;
    protected static $_autoSearchKeyword;
    protected static $_autoSearchColumns;
	protected static $_transactionMode;
	protected static $_transactionSqls;
    protected static $_queries;
	protected static $_identifierLeft = '`';
	protected static $_identifierRight = '`';
	protected static $_affectedRows;

	protected static $_datas;

	public function __construct() // ok
	{
		if (is_null(static::$_connection))
		{
			static::_connect();

			static::$_dbCachePath = BASEPATH_TMP.'/cache/db/';
			static::$_queryCachePath = BASEPATH_TMP.'/cache/queries/';

			if (DEV_MODE)
			{
				Folder::delete(static::$_dbCachePath);
				Folder::delete(static::$_dbCachePath);
			}

			Folder::create(static::$_dbCachePath);
			Folder::create(static::$_queryCachePath);

			static::$_instance = $this;
		}
	}

	private static function _getInstance() // ok
	{
		if (is_null(static::$_instance))
			static::$_instance = new static;

		return static::$_instance;
	}

	private static function _getQuerySessionId() // ok
	{
		return 'xxx';
		$debugInfo = debug_backtrace();
		$countDebugInfo = count($debugInfo);
		$thisFile = __FILE__;

		for ($i=1; $i < $countDebugInfo; ++$i)
		{
			if ($debugInfo[$i]['file'] != $thisFile)
				break;
		}

		$file = $debugInfo[$i]['file'];
		$function = $debugInfo[$i+1]['function'];

		if ($function == '{closure}')
		{
			$className = File::getName($file);
			$className = str_replace('.php', '', $className);
			$class = new \ReflectionClass($className);

			$methods = $class->getMethods();
			$methods = Arr::getColumn($methods, 'name');

			$i += 1;

			for ($i; $i < $countDebugInfo; ++$i)
			{
				$function = $debugInfo['function'];

				if (Arr::has($methods, $function))
					break;
			}
		}

		return $file . '.' . $function;
	}

	// ========== Migration ==========

	public static function createDatabase($name, $character = null, $collate = null)
	{
		$sql = 'CREATE DATABASE IF NOT EXISTS ' . static::wrapTable($name);

		if ($character)
			$sql .= ' CHARACTER SET ' . $character;

		if ($collate)
			$sql .= ' COLLATE ' . $collate;

		if (static::$_connection->query($sql))
			return true;
		else
			return false;
	}

	public static function dropDatabase($name)
	{
		$sql = 'DROP DATABASE IF EXISTS ' . static::wrapTable($name);

		if (static::$_connection->query($sql))
			return true;
		else
			return false;
	}

	public static function createTable($name)
	{

	}

	public static function dropTable($name)
	{

	}

	public static function renameTable($name)
	{

	}

	public static function addColumn($table, $column)
	{

	}

	public static function dropColumn($table, $column)
	{

	}

	public static function modifyColumn($table, $column)
	{

	}

	////////////

	public static function getPlatform()
	{
	}

	// ========== Utility ==========

	public static function listDatabases()
	{
	}

	public static function databaseExists($database)
	{
	}

	public static function optimizeDatabase($database)
	{
	}

	public static function optimizeTable($table)
	{
	}

	public static function repaireTable($table)
	{
	}

	public static function backup()
	{
	}

	// ========== Query ==========

	// select

	public static function select($columns = '*') // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);

		if (isset(static::$_sqlSelects[$sid]))
			static::$_sqlSelects[$sid] = array_merge(static::$_sqlSelects[$sid], $columns);
		else
			static::$_sqlSelects[$sid] = $columns;

		return static::_getInstance();
	}

	public static function avg($columns) // ok
	{
        return static::_queryAggregate('AVG()', $columns);
	}

	public static function count($columns = '*') // ok
	{
        return static::_queryAggregate('COUNT()', $columns);
	}

	public static function countDistinct($columns) // ok
	{
        return static::_queryAggregate('COUNT(DISTINCT())', $columns);
	}

	public static function min($columns) // ok
	{
        return static::_queryAggregate('MIN()', $columns);
	}

	public static function max($columns) // ok
	{
        return static::_queryAggregate('MAX()', $columns);
	}

	public static function std($columns) // ok
	{
        return static::_queryAggregate('STD()', $columns);
	}

	public static function sum($columns) // ok
	{
        return static::_queryAggregate('SUM()', $columns);
	}

	public static function distinct($column) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);
		$alias = '';

		if (stripos($column, ' AS '))
		{
			$arr = explode(' AS ', $column);
			$column = $arr[0];
			$alias = ' AS ' . $arr[1];
		}

		static::$_sqlSelects[$sid][] = 'DISTINCT(' . $column . ')' . $alias;

		return static::loadAll();
	}

	private static function _queryAggregate($function, $columns) // ok
	{
		$sid = static::_getQuerySessionId();
		$returnObject = false;

		if (is_array($columns) == false)
		{
			$columns = explode(',', $columns);

			if (count($columns) > 1)
				$returnObject = true;
		}
		else
			$returnObject = true;

		$columns = static::wrapColumn($columns);

		foreach ($columns as $column)
			static::$_sqlSelects[$sid][] = str_replace('()', '(' . $column . ')', $function);

		if ($returnObject)
			return static::load();
		else
			return static::loadSingle();
	}

	public static function autoSearchKeyword($keyword)
    {
	    $sid = static::_getQuerySessionId();
        static::$_autoSearchKeyword[$sid] = $keyword;
    }

    public static function autoSearchColumn($columns = '*')
    {
        if (is_array($columns) == false)
            $columns = explode(',', $columns);

        foreach ($columns as $key => $value)
            $columns[$key] = static::wrapColumn($value);

	    $sid = static::_getQuerySessionId();

	    if (isset(static::$_autoSearchColumns[$sid]))
            static::$_autoSearchColumns[$sid] = array_merge(static::$_autoSearchColumns[$sid], $columns);
	    else
	        static::$_autoSearchColumns[$sid] = $columns;
    }

	// from

	public static function table($table) // ok
	{
		static::from($table);

        return static::_getInstance();
	}

	public static function from($table) // ok
	{
		$sid = static::_getQuerySessionId();
		static::$_sqlTable[$sid] = static::wrapTable($table);

		return static::_getInstance();
	}

    private static function _setJoin($type, $table, $condition) // ok
    {
	    $sid = static::_getQuerySessionId();
        $table = static::wrapTable($table);

	    if (strpos($condition, '='))
	    {
		    $arr = explode('=', $condition);
		    $arr = static::wrapColumn($arr);

		    $condition = $arr[0] . ' = ' . $arr[1];
	    }

        $sql = $type . ' ' . $table . ' ON ' . $condition;

        static::$_sqlJoins[$sid][] = $sql;
    }

	public static function innerJoin($table, $condition) // ok
	{
        static::_setJoin('INNER JOIN', $table, $condition);

        return static::_getInstance();
	}

    public static function leftJoin($table, $condition) // ok
    {
        static::_setJoin('LEFT JOIN', $table, $condition);

        return static::_getInstance();
    }

    public static function rightJoin($table, $condition) // ok
    {
        static::_setJoin('RIGHT JOIN', $table, $condition);

        return static::_getInstance();
    }

	// save

	public static function insert($data) // ok
	{
		$sql = static::_buildQueryInsert($data);

		if (count($data) > 1)
		{
			static::transaction(function() use ($sql){
				static::raw($sql)->execute();
			});

			$affectedRows = count($data);
		}
		else
		{
			static::raw($sql)->execute();
			$affectedRows = static::getAffectedRows();
		}

		return $affectedRows;
	}

	public static function update($data) // ok
	{
		$sql = static::_buildQueryUpdate($data);

		// TODO กรณีโดน remove data ที่ไม่มีชื่อคอลัมน์ใน table ออกจนหมด จะได้ $sql เป็นค่าว่าง
		if ($sql)
		{
			static::raw($sql)->execute();

			return static::getAffectedRows();
		}
	}

	private static function _prepareDataBeforeSave($data)
	{
		$data = Arr::fromObject($data);

		if (Arr::isAssoc($data))
			$datas[] = $data;
		else
			$datas = $data;

		$n = count($datas);

		for ($i = 0; $i < $n; ++$i)
		{
			foreach ($datas[$i] as $key => $value)
			{
				if (static::columnExists($key) == false)
				{
					unset($datas[$i][$key]);
					continue;
				}

				if (is_array($value))
					$datas[$i][$key] = ',' . implode(',', $value) . ',';

				// data from submitted form is string, use mb_strlen because value may be 0
				elseif (is_string($value) and mb_strlen($value) == 0)
				{
					$default = static::column($key)->default;

					if (mb_strlen($default))
					{
						if ($default == 'CURRENT_TIMESTAMP')
						{
							// define default value by database server
							unset($datas[$i][$key]);
						}
						else
							$datas[$i][$key] = $default;
					}
					elseif (static::column($key)->nullable)
						$datas[$i][$key] = null;
				}

				// define default value by database server
				elseif (is_null($value) and static::column($key)->nullable == false)
					unset($datas[$i][$key]);
			}
		}
		
		return $datas;
	}

	public static function save($data) // ok
	{
		$sid = static::_getQuerySessionId();
		$where = static::_buildWhere();
		$data = (array)$data;

		if ($where)
		{
			$sql = static::_buildQuerySave($data);
			static::raw($sql)->execute();

			$affectedRows = static::getAffectedRows();
		}
		else
		{
			$datas = [];
			$autoOrdering = false;

			if (Arr::isAssoc($data))
				$datas[] = $data;
			else
				$datas = $data;

			if (static::columnExists('ordering') and array_key_exists('ordering', $datas[0]) == false)
			{
				$autoOrdering = true;
				static::lockTable(@static::$_sqlTable[$sid]);
			}

			$sql = static::_buildQuerySave($data);

			if (count($datas) > 1)
			{
				static::transaction(function() use ($sql){
					static::raw($sql)->execute();
				});

				$affectedRows = count($datas);
			}
			else
			{
				static::raw($sql)->execute();
				$affectedRows = static::getAffectedRows();
			}

			if ($autoOrdering)
				static::unlockTables();
		}

		return $affectedRows;
	}

	public static function increase($columns, $num = 1) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);
		$where = static::_buildWhere();
		$num = (float)$num;

		$sql = 'UPDATE ' . @static::$_sqlTable[$sid] . ' SET ';

		foreach ($columns as $column)
			$sql .= $column . ' = IFNULL(' . $column . ', 0) + ' . $num . ', ';

		$sql = substr($sql, 0, -2) . $where;
		$sql .= static::_buildSort();
		$sql .= static::_buildLimit();

		static::raw($sql)->execute();

		return static::getAffectedRows();
	}

	public static function decrease($columns, $num = 1) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);
		$where = static::_buildWhere();
		$num = (float)$num;

		$sql = 'UPDATE ' . @static::$_sqlTable[$sid] . ' SET ';

		foreach ($columns as $column)
			$sql .= $column . ' = IFNULL(' . $column . ', 0) - ' . $num . ', ';

		$sql = substr($sql, 0, -2) . $where;

		static::raw($sql)->execute();

		return static::getAffectedRows();
	}

    public static function activate() // ok
    {
        return static::update(['status' => 1]);
    }

    public static function deactivate() // ok
    {
        return static::update(['status' => 0]);
    }

	public static function archive() // ok
	{
		return static::update(['status' => -1]);
	}

    public static function trash() // ok
    {
        return static::update(['status' => -2]);
    }

    public static function discontinue() // ok
    {
        return static::update(['status' => -3]);
    }

	// delete

	public static function delete($deleteUploadedFiles = false, $fileBackupPath = null)
	{
		$where = static::_buildWhere();

		if ($where == '')
		{
			static::where('id', null);
			$where = static::_buildWhere();
		}

		if ($fileBackupPath)
			static::_backupUploadedFiles($fileBackupPath, $where);

		if ($deleteUploadedFiles)
			static::_deleteUploadedFiles($where);

		$sql = static::_buildQueryDelete($where);
		static::_query($sql);

		return static::getAffectedRows();
	}

    public static function deleteAll($deleteUploadedFiles = false, $fileBackupPath = null)
    {
	    if ($fileBackupPath)
		    static::_backupUploadedFiles($fileBackupPath);

	    if ($deleteUploadedFiles)
		    static::_deleteUploadedFiles();

	    $sql = static::_buildQueryDelete();
	    static::_query($sql);

        return static::getAffectedRows();
    }

	public static function truncate($deleteUploadedFiles = false, $fileBackupPath = null)
	{
		$sid = static::_getQuerySessionId();
		$table = @static::$_sqlTable[$sid];

		if ($fileBackupPath)
			static::_backupUploadedFiles($fileBackupPath);

		if ($deleteUploadedFiles)
			static::_deleteUploadedFiles();

		$sql = 'TRUNCATE ' . $table;

		// The static::$_sqlTable will be removed by
		// this method but maybe we need to call method
		// deleteAll() if no DROP privilege to truncate.

		if (static::$_transactionMode)
			$result = static::$_connection->query($sql);
		else
		{
			$result = static::transaction(function () use ($sql){
				static::$_connection->query($sql);
			});
		}

		if ($result and DEV_MODE)
			static::$_queries[] = $sql;
		else
			static::deleteAll();
	}

	private static function _deleteUploadedFiles($where = null)
	{
		$sid = static::_getQuerySessionId();
		$table = @static::$_sqlTable[$sid];

		// Have to use raw query because in case we call
		// method truncate() without DROP privilege. System will calls
		// method deleteAll() automatically. And static::$_sqlTable
		// will be removed by this method that called in method
		// truncate() already.

		$sql = 'SELECT * FROM ' . $table . $where;
		$result = static::$_connection->query($sql);

		if (DEV_MODE)
			static::$_queries[] = $sql;

		$deleted = 0;
		$assetPath = str_replace(BASEPATH, '', BASEPATH_ASSETS);

		while ($row = $result->fetch())
		{
			foreach ($row as $value)
			{
				if (stripos($value, $assetPath) !== false)
				{
					if (File::delete(BASEPATH . $value))
						++$deleted;
				}
			}
		}

		return $deleted;
	}

	private static function _backupUploadedFiles($backupPath, $where = null)
	{
		$sid = static::_getQuerySessionId();
		$table = @static::$_sqlTable[$sid];
		$backupPath = rtrim($backupPath, '/');

		Folder::create($backupPath);

		// Have to use raw query because in case we call
		// method truncate() without DROP privilege. System will calls
		// method deleteAll() automatically. And static::$_sqlTable
		// will be removed by this method that called in method
		// truncate() already.

		$sql = 'SELECT * FROM ' . $table . $where;
		$result = static::$_connection->query($sql);

		if (DEV_MODE)
			static::$_queries[] = $sql;

		$backedup = 0;
		$assetPath = str_replace(BASEPATH, '', BASEPATH_ASSETS);

		while ($row = $result->fetch())
		{
			foreach ($row as $value)
			{
				if (stripos($value, $assetPath) !== false)
				{
					if (File::exists(BASEPATH . $value))
					{
						$filename = File::getName($value);
						copy($value, $backupPath . DS . $filename);

						++$backedup;
					}
				}
			}
		}

		return $backedup;
	}

	// normal where

	public static function groupStart() // ok
	{
		$sid = static::_getQuerySessionId();
		static::$_sqlWheres[$sid][] = ['AND', '('];

		return static::_getInstance();
	}

	public static function orGroupStart() // ok
	{
		$sid = static::_getQuerySessionId();
		static::$_sqlWheres[$sid][] = ['OR', '('];

		return static::_getInstance();
	}

	public static function groupEnd() // ok
	{
		$sid = static::_getQuerySessionId();
		static::$_sqlWheres[$sid][] = ['', ')'];

		return static::_getInstance();
	}

	public static function where($where) // ok
	{
		$args = func_get_args();

		if ($args[0] instanceof \Closure)
		{
			static::groupStart();
			$args[0]();
			static::groupEnd();
		}
		else
		{
			$sid = static::_getQuerySessionId();
			$where = static::_parseWhere($args);

			static::$_sqlWheres[$sid][] = ['AND', $where];
		}

		return static::_getInstance();
	}

	public static function orWhere($where) // ok
	{
		$sid = static::_getQuerySessionId();
		$args = func_get_args();

		if (is_callable($args[0]))
		{
			static::orGroupStart();
			$args[0]();
			static::groupEnd();
		}
		else
		{
			$where = static::_parseWhere($args);

			if ($where)
				static::$_sqlWheres[$sid][] = ['OR', $where];
		}

        return static::_getInstance();
	}

	private static function _parseWhere($args) // ok
	{
		$countArgs = count($args);

		if ($countArgs == 1)
        {
            if (is_numeric(trim($args[0])))
                $where = static::wrapColumn('id') . ' = ' . $args[0];
            else
                $where = $args[0];
        }
        else
		{
            $where = $args[0];
            $where = str_replace('{', static::$_identifierLeft, $where);
			$where = str_replace('}', static::$_identifierRight, $where);

			if (strpos($args[0], '?'))
			{
				$args = Arr::flatten($args);

				for ($i = 1, $n = count($args); $i < $n; ++$i)
					$where = Str::replace($where, '?', static::escape($args[$i]), 1);
			}
			elseif (strpos($args[0], ':'))
			{
				// We need associative array so don't need to flatten it.
				foreach ($args[1] as $key => $value)
					$where = str_replace($key, static::escape($value), $where);
			}
			else
			{
				$args = Arr::flatten($args);
				$column = static::wrapColumn($args[0]);
                $operator = '';
                $value = '';

				if ($countArgs == 2)
				{
					$operator = '=';
					$value = $args[1];
				}
				elseif ($countArgs > 2)
				{
					$operator = trim($args[1]);
					$value = $args[2];
				}

				$value = static::escape($value);
				$where = $column . ' ' . $operator . ' ' . $value;
			}
		}

		return $where;
	}

	// where between

	public static function whereBetween($column, $start, $end)
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);
		$start = static::escape($start);
		$end = static::escape($end);

		static::$_sqlWheres[$sid][] = ['AND', $column . ' BETWEEN ' . $start . ' AND ' . $end];

		return static::_getInstance();
	}

	public static function orWhereBetween($column, $start, $end) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);
		$start = static::escape($start);
		$end = static::escape($end);

		static::$_sqlWheres[$sid][] = ['OR', $column . ' BETWEEN ' . $start . ' AND ' . $end];

        return static::_getInstance();
	}

	public static function whereNotBetween($column, $start, $end) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);
		$start = static::escape($start);
		$end = static::escape($end);

		static::$_sqlWheres[$sid][] = ['AND', $column . ' NOT BETWEEN ' . $start . ' AND ' . $end];

        return static::_getInstance();
	}

	public static function orWhereNotBetween($column, $start, $end) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);
		$start = static::escape($start);
		$end = static::escape($end);

		static::$_sqlWheres[$sid][] = ['OR', $column . ' NOT BETWEEN ' . $start . ' AND ' . $end];

        return static::_getInstance();
	}

	// where like

	public static function whereContain($column, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[$sid][] = ['AND', $column . ' LIKE \'%' . $value . '%\''];

        return static::_getInstance();
	}

	public static function orWhereContain($column, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[$sid][] = ['OR', $column . ' LIKE \'%' . $value . '%\''];

        return static::_getInstance();
	}

	public static function whereStartWith($column, $value) // ok
    {
	    $sid = static::_getQuerySessionId();
	    $column = static::wrapColumn($column);
	    $value = static::escapeLike($value);

	    static::$_sqlWheres[$sid][] = ['AND', $column . ' LIKE \'' . $value . '%\''];

		return static::_getInstance();
	}

	public static function orWhereStartWith($column, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[$sid][] = ['OR', $column . ' LIKE \'' . $value . '%\''];

        return static::_getInstance();
	}

	public static function whereEndWith($column, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[$sid][] = ['AND', $column . ' LIKE \'%' . $value . '\''];

        return static::_getInstance();
	}

	public static function orWhereEndWith($column, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[$sid][] = ['OR', $column . ' LIKE \'%' . $value . '\''];

        return static::_getInstance();
	}

	public static function whereNotContain($column, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[$sid][] = ['AND', $column . ' NOT LIKE \'%' . $value . '%\''];

        return static::_getInstance();
	}

	public static function orWhereNotContain($column, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[$sid][] = ['OR', $column . ' NOT LIKE \'%' . $value . '%\''];

        return static::_getInstance();
	}

	public static function whereNotStartWith($column, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[$sid][] = ['AND', $column . ' NOT LIKE \'' . $value . '%\''];

        return static::_getInstance();
	}

	public static function orWhereNotStartWith($column, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[$sid][] = ['OR', $column . ' NOT LIKE \'' . $value . '%\''];

        return static::_getInstance();
	}

	public static function whereNotEndWith($column, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[$sid][] = ['AND', $column . ' NOT LIKE \'%' . $value . '\''];

        return static::_getInstance();
	}

	public static function orWhereNotEndWith($column, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[$sid][] = ['OR', $column . ' NOT LIKE \'%' . $value . '\''];

        return static::_getInstance();
	}

	// where in

	public static function whereIn($column, $values) // ok
	{
        static::_setWhereIn('IN', $column, $values, 'AND');

        return static::_getInstance();
	}

	public static function orWhereIn($column, $values) // ok
	{
        static::_setWhereIn('IN', $column, $values, 'OR');

        return static::_getInstance();
	}

	public static function whereNotIn($column, $values) // ok
	{
        static::_setWhereIn('NOT IN', $column, $values, 'AND');

        return static::_getInstance();
	}

	public static function orWhereNotIn($column, $values) // ok
	{
        static::_setWhereIn('NOT IN', $column, $values, 'OR');

        return static::_getInstance();
	}

	private static function _setWhereIn($operator, $column, $values, $condition) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);
		$values = (array)$values;
		$values = Arr::flatten($values); // $args parameter sent from model class as multi-dimension array

		$values = array_map([static::_getInstance(), 'escape'], $values);
		$where = $column . ' ' . $operator . ' (' . implode(', ', $values) . ')';

		static::$_sqlWheres[$sid][] = [$condition, $where];

	}

	// where null

	public static function whereNull($column) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);

		static::$_sqlWheres[$sid][] = ['AND', $column . ' IS NULL'];

        return static::_getInstance();
	}

	public static function orWhereNull($column) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);

		static::$_sqlWheres[$sid][] = ['OR', $column . ' IS NULL'];

        return static::_getInstance();
	}

	public static function whereNotNull($column) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);

		static::$_sqlWheres[$sid][] = ['AND', $column . ' IS NOT NULL'];

        return static::_getInstance();
	}

	public static function orWhereNotNull($column) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);

		static::$_sqlWheres[$sid][] = ['OR', $column . ' IS NOT NULL'];

        return static::_getInstance();
	}

	// group

	public static function group($columns) // ok
	{
		$sid = static::_getQuerySessionId();

		static::$_sqlGroups[$sid][] = static::wrapColumn($columns);

        return static::_getInstance();
	}

	// order by

	public static function sort($columns, $direction = 'ASC') // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);

		foreach ($columns as $column)
			static::$_sqlSorts[$sid][] = $column . ' ' . strtoupper($direction);

        return static::_getInstance();
	}

    public static function sortAsc($columns) // ok
    {
        static::sort($columns, 'ASC');

        return static::_getInstance();
    }

    public static function sortDesc($columns) // ok
    {
        static::sort($columns, 'DESC');

        return static::_getInstance();
    }

	// limit

	public static function take($num) // ok
	{
		$sid = static::_getQuerySessionId();

		static::$_sqlTake[$sid] = (int)$num;

        return static::_getInstance();
	}

	public static function skip($num) // ok
	{
		$sid = static::_getQuerySessionId();

		static::$_sqlSkip[$sid] = (int)$num;

        return static::_getInstance();
	}

	// query

	public static function raw($sql) // ok
	{
		$sid = static::_getQuerySessionId();

		static::$_sqlRaw[$sid] = $sql;

		return static::_getInstance();
	}

	public static function execute() // ok
	{
		$sql = static::_buildQuerySelect();

		static::_query($sql);

        return static::getAffectedRows();
	}

	private static function _query($sql) // ok
	{
		if (stripos($sql, ' WHERE '))
			$keyword = ' WHERE ';
		elseif (stripos($sql, ' VALUES '))
			$keyword = ' VALUES ';
		elseif (stripos($sql, ' SET '))
			$keyword = ' SET ';
		else
			$keyword = '';

		$pos = stripos($sql, $keyword);

		$block1 = substr($sql, 0, $pos);
		$block2 = substr($sql, $pos);

		if ($pos)
			$block1 = str_replace('#_', DB_PREFIX, $block1);
		else
			$block2 = str_replace('#_', DB_PREFIX, $block2);

		$sql = $block1 . $block2;

		if (DEV_MODE)
			static::$_queries[] = $sql;

		if (static::$_transactionMode)
			static::$_transactionSqls[] = $sql;
		else
		{
			$result = static::$_connection->query($sql);

			if (is_object($result))
				static::$_affectedRows = $result->rowCount();
			elseif (static::$_connection->errorInfo()[2] and DEV_MODE)
				static::_displayError();

			static::reset();
			return $result;
		}
	}

	// load data

	public static function loadSingle() // ok
	{
		$sql = static::_buildQuerySelect();
		$data = static::load($sql);
        $data = (array)$data;
        $data = current($data);

        return $data;
	}
	
	public static function load() // ok
	{
		$args = func_get_args();

		if (isset($args[0]))
			$sql = $args[0];
		else
			$sql = static::_buildQuerySelect();

		$sql = trim($sql);

		if (strtoupper(substr($sql, 0, 6)) != 'SELECT')
			return false;

		if (strripos($sql, ' LIMIT ') == false)
			$sql .= ' LIMIT 1 ';

		$result = static::_query($sql);

		if ($result)
		{
			if ($result->rowCount())
				return $result->fetch();
			else
			{
				$sql = trim($sql);

				if (strtoupper(substr($sql, 0, 6)) != 'SELECT')
					return false;

				$select = substr($sql, 7, stripos($sql, ' FROM ') - 7);

				if (trim($select) == '*')
				{
					$pattern = '/' . DB_PREFIX . '(.*)\s+/U';
					preg_match_all($pattern, $sql, $tables);

					$columns = [];

					foreach ($tables[0] as $table)
					{
						$table = trim($table);
						$table = ltrim($table, static::$_identifierLeft);
						$table = rtrim($table, static::$_identifierRight);

						$columns = array_merge($columns, array_values(static::getColumnListing($table)));
					}
				}
				else
				{
					$columns = [];
					$arr = explode(',', $select);

					foreach ($arr as $column)
					{
						$pos = stripos($column, ' AS ');

						if ($pos)
							$column = substr($column, $pos + 4);

						$column = ltrim($column, static::$_identifierLeft);
						$column = rtrim($column, static::$_identifierLeft);
						$column = trim($column);

						$columns[] = $column;
					}
				}

				$data = new \stdClass();

				foreach ($columns as $column)
					$data->$column = '';

				return $data;
			}
		}
		else
		{
			//
		}
	}

	public static function loadAll() // ok
	{
		$args = func_get_args();
		$paginate = false;
        $no = 0;

		if (isset($args[0]) and is_object($args[0]))
        {
            $paginate = true;
            $no = $args[0]->pagenumstart;
        }

        $sql = static::_buildQuerySelect();
		$result = static::_query($sql);

		if ($result)
		{
			$rows = $result->fetchAll();

			foreach ($rows as $row)
			{
				if ($paginate)
					$row->{':no'} = $no++;
			}

			return $rows;
		}
		else
			static::_displayError($result);
	}

	public static function paginate(Paginator $paginator = null) // ok
	{
		$sql = static::_buildQuerySelect();

//		if (is_null($paginator))
//			$paginator = new Paginator();

		$sqlCount = 'SELECT COUNT(*) ' . substr($sql, stripos($sql, ' FROM '));
		Paginator::$recordtotal = static::raw($sqlCount)->loadSingle();

		Paginator::initialize();
		$limitPos = strripos($sql, ' LIMIT ');

		if (Paginator::$sortcol and strripos($sql, ' ORDER BY ') === false)
		{
			$sortcol = Paginator::$sortcol;
			$sortdir = Paginator::$sortdir;

			// Clean up values from cookie.
			// (Don't remove dot! In case order by alias table nam ie t.name)
			$sortcol = preg_replace('/[^.a-z0-9]+/i', '', $sortcol);

			if (in_array(strtolower($sortdir), ['asc', 'desc']) == false)
				$sortdir = 'ASC';

			$sortcol = static::wrapColumn($sortcol);
			$orderBy = ' ORDER BY ' . $sortcol . ' ' . $sortdir;

			if ($limitPos)
			{
				$limit = substr($sql, $limitPos);
				$sql = substr($sql, 0, $limitPos);

				$sql .= $orderBy . $limit;
			}
			else
				$sql .= $orderBy;
		}

		if (Paginator::$recordtotal and empty($limitPos))
		{
			// Ensure integer
			$page = (int)Paginator::$page;
			$pagesize = (int)Paginator::$pagesize;

			$offset = $pagesize * ($page - 1);

			$sql .= ' LIMIT ' . $pagesize . ' OFFSET ' . $offset;
		}

		$result = static::_query($sql);

		if ($result)
		{
			$rows = $result->fetchAll();
			$no = 0;

			foreach ($rows as $row)
				$row->{':no'} = $no++;

			return $rows;
		}
		else
			static::_displayError($result);
	}

	public static function toJSON() // ok
	{
		$data = static::loadAll();
		$json = json_encode($data);

		// preven json_encode converts empty value to array.
		$json = str_replace('{}', '""', $json);

		return $json;
	}

	public static function toCSV($header = false)
	{
		$data = static::loadAll();

		if ($header === true and isset($data[0]))
		{
			$header = [];

			foreach ($data[0] as $key => $value)
				$header[] = $key;

			array_unshift($data, $header);
		}

		$csv = Csv::fromDataSet($data);

		return $csv;
	}

	public static function toXML()
	{
		$data = static::loadAll();
		$xml = Xml::fromDataSet($data);

		return $xml;
	}

	public static function getCreatorUpdaterInfo($data) // ok
	{
		if (empty($data))
			return $data; // ensure to return the same input data type.

		if (is_array($data) and isset($data[0]))
			$row = $data[0];
		else
			$row = $data;

		if (is_object($row) == false)
			return $data;

		if (array_key_exists('creator', $row) == false and
			array_key_exists('updater', $row) == false)
			return $data;

		$userIds = [];

		if (is_array($data))
		{
			foreach ($data as $row)
				$userIds = static::_addUserIdToArray($row, $userIds);
		}
		else
			$userIds = static::_addUserIdToArray($data, $userIds);

		if (empty($userIds))
			return $data;

		$sql = 'SELECT ' . static::wrapColumn('id, name, username, email') . ' '
			. 'FROM ' . static::wrapTable('User') . ' '
			. 'WHERE ' . static::wrapColumn('id') . ' '
						. 'IN (' . implode(', ', $userIds) . ') ';
		$result = static::$_connection->query($sql);

		if (DEV_MODE)
			static::$_queries[] = $sql;

		$userInfo = [];

		while ($row = $result->fetch_assoc())
			$userInfo[$row['id']] = $row;

		if (is_array($data))
		{
			$returnData = [];

			foreach ($data as $row)
				$returnData[] = static::_getCreatorUpdaterInfoEachRow($row, $userInfo);
		}
		else
			$returnData = static::_getCreatorUpdaterInfoEachRow($data, $userInfo);

		return $returnData;
	}

	private static function _addUserIdToArray($row, $userIds) // ok
	{
		foreach ($row as $column => $value)
		{
			if ($column == 'creator' or $column == 'updater')
			{
				if ($value and Arr::has($userIds, $value) == false)
					$userIds[] = $value;
			}
		}

		return $userIds;
	}

	private static function _getCreatorUpdaterInfoEachRow($row, $userInfo) // ok
	{
		$returnRow = new \stdClass();

		foreach ($row as $column => $value)
		{
			$returnRow->{$column} = $value;

			if ($column == 'creator' or $column == 'updater')
			{
				$data = new \stdClass();

				if ($value and isset($userInfo[$value]))
				{
					$data->name = $userInfo[$value]['name'];
					$data->username = $userInfo[$value]['username'];
					$data->email = $userInfo[$value]['email'];
				}
				else
				{
					$data->name = '';
					$data->username = '';
					$data->email = '';
				}

				$returnRow->{':'.$column} = $data;
			}
		}

		return $returnRow;
	}

	// transaction

	public static function transaction($callback, $testMode = false) // ok
	{
		static::beginTransaction();

		$callback();

		if (static::transactionSuccess())
		{
			if ($testMode == false)
				static::commit();

			return true;
		}
		else
		{
			static::rollback();
			return false;
		}
	}

	public static function beginTransaction() // ok
	{
		// Ensure the connection is established in case we
		// start a transaction manually before set a query.
		static::_getInstance();

		static::$_connection->beginTransaction();
		static::$_transactionMode = true;
	}

	public static function commit() // ok
	{
		static::_queryTransaction();

		static::$_connection->commit();
		static::$_transactionMode = false;
		static::$_transactionSqls = null;
	}

	public static function rollback() // ok
	{
		static::$_connection->rollback();
		static::$_transactionMode = false;
		static::$_transactionSqls = null;
	}

	public static function transactionSuccess() // ok
	{
		return static::_queryTransaction();
	}

	private static function _queryTransaction() // ok
	{
		$sqls = static::$_transactionSqls;

		static::$_transactionMode = false;
		static::$_transactionSqls = null;

		if (is_array($sqls))
		{
			try
			{
				foreach ($sqls as $sql)
				{
					if (DEV_MODE)
						static::$_queries[] = $sql;

					static::$_connection->query($sql);
				}

				return true;
			}
			catch (\PDOException $e)
			{
				return false;
			}
		}
		else
			return false;
	}

	// build

	private static function _buildQuerySelect() // ok
	{
		$sid = static::_getQuerySessionId();

		if (isset(static::$_sqlRaw[$sid]))
			return static::$_sqlRaw[$sid];

		$sql = 'SELECT ';

		if (isset(static::$_sqlSelects[$sid]))
			$sql .= implode(', ', static::$_sqlSelects[$sid]);
		else
			$sql .= '*';

		$sql .= static::_buildFrom();
		$sql .= static::_buildWhere();
		$sql .= static::_buildGroup();
		$sql .= static::_buildSort();
		$sql .= static::_buildLimit();

		return $sql;
	}

	private static function _buildQueryInsert($data) // ok
	{
		$sid = static::_getQuerySessionId();
		$data = Arr::removeKey($data, 'id');
		$data = static::_prepareDataBeforeSave($data);
		$table = @static::$_sqlTable[$sid];
		$datas = [];
		$columns = [];
		$values = '';

		if (Arr::isAssoc($data))
			$datas[] = $data;
		else
			$datas = $data;

		foreach (array_keys($datas[0]) as $column)
			$columns[] = static::wrapColumn($column);

		foreach ($datas as $data)
		{
			$data = array_map([static::_getInstance(), 'escape'], array_values($data));
			$values .= '(' . implode(', ', $data) . '), ';
		}

		$columns = implode(', ', $columns);
		$values = substr($values, 0, -2);

		$sql = 'INSERT INTO ' . $table . ' (' . $columns . ') VALUES ' . $values;

		return $sql;
	}

	private static function _buildQueryUpdate($data) // ok
	{
		$sid = static::_getQuerySessionId();
		$data = static::_prepareDataBeforeSave($data);

		if (Arr::isAssoc($data) == false)
			$data = $data[0];

		// Data maybe empty after calling static::_prepareDataBeforeSave()
		if (empty($data))
			return '';

		$columns = array_keys($data);
		$values = array_values($data);

		$sql = 'UPDATE ' . @static::$_sqlTable[$sid] . ' SET ';
		$n = count($columns);

		for ($i = 0; $i < $n; ++$i)
			$sql .= static::wrapColumn($columns[$i]) . ' = ' . static::escape($values[$i]) . ', ';

		$sql = substr($sql, 0, -2);

		$sql .= static::_buildWhere();
		$sql .= static::_buildSort();
		$sql .= static::_buildLimit();

		return $sql;
	}

	private static function _buildQuerySave($data) // ok
	{
		$where = static::_buildWhere();

		if ($where)
		{
			if (Arr::isAssoc($data) == false)
				$data = $data[0];

			if (static::columnExists('updated') and array_key_exists('updated', $data) == false)
				$data = Data::push($data, 'updated', date('Y-m-d H:i:s'));

			if (static::columnExists('updater') and array_key_exists('updater', $data) == false)
				$data = Data::push($data, 'updater', (int)Auth::identity()->id);

			$sql = static::_buildQueryUpdate($data);
		}
		else
		{
			$datas = [];
			$autoOrdering = false;
			$ordering = 0;

			if (Arr::isAssoc($data))
				$datas[] = $data;
			else
				$datas = $data;

			if (static::columnExists('ordering') and array_key_exists('ordering', $datas[0]) == false)
			{
				$autoOrdering = true;
				$ordering = static::getNewOrdering();
			}

			$n = count($datas);

			for ($i = 0; $i < $n; ++$i)
			{
				if ($autoOrdering)
				{
					$datas[$i] = Data::push($datas[$i], 'ordering', $ordering);
					++$ordering;
				}

				if (static::columnExists('created') and array_key_exists('created', $datas[$i]) == false)
					$datas[$i] = Data::push($datas[$i], 'created', date('Y-m-d H:i:s'));

				if (static::columnExists('creator') and array_key_exists('creator', $datas[$i]) == false)
					$datas[$i] = Data::push($datas[$i], 'creator', (int)Auth::identity()->id);
			}

			$sql = static::_buildQueryInsert($datas);
		}

		return $sql;
	}

	private static function _buildQueryDelete($where = null) // ok
	{
		$sid = static::_getQuerySessionId();
		$table = @static::$_sqlTable[$sid];

		$sql = 'DELETE FROM ' . $table . $where;

		$sql .= static::_buildSort();
		$sql .= static::_buildLimit();

		return $sql;
	}

	private static function _buildFrom() // ok
	{
		$sid = static::_getQuerySessionId();
		$table = @static::$_sqlTable[$sid];

		$sql = ' FROM ' . $table;

		if (isset(static::$_sqlJoins[$sid]))
			$sql .= ' ' . implode(' ', static::$_sqlJoins[$sid]);

		return $sql;
	}

	private static function _buildWhere() // ok
	{
		$sid = static::_getQuerySessionId();

		if (isset(static::$_autoSearchKeyword[$sid]))
		{
			if (isset(static::$_autoSearchColumns[$sid]))
				$searchColumns = static::$_autoSearchColumns[$sid];
			elseif (isset(static::$_sqlSelects[$sid]))
				$searchColumns = static::$_sqlSelects[$sid];
			else
				$searchColumns = ['*'];

			if (Arr::has($searchColumns, '*') and isset(static::$_sqlTable[$sid]))
				$searchColumns = static::getColumnListing();

			foreach ($searchColumns as $searchColumn)
				static::orWhereContain($searchColumn, static::$_autoSearchKeyword[$sid]);
		}

		$where = '';

		if (isset(static::$_sqlWheres[$sid]))
		{
			$where = ' WHERE ';
			$_sqlWheres = static::$_sqlWheres[$sid];
			$countWhere = count($_sqlWheres);

			for ($i=0; $i < $countWhere; ++$i)
			{
				if ($i > 0 and $_sqlWheres[$i][1] != ')' and $_sqlWheres[$i - 1][1] != '(')
					$where .= ' ' . $_sqlWheres[$i][0].' ';

				$where .= $_sqlWheres[$i][1];

				//$where .= $where .= $_sqlWheres[$i][0]. ' ' . $_sqlWheres[$i][1];
			}
		}

		return $where;
	}

	private static function _buildGroup() // ok
	{
		$sid = static::_getQuerySessionId();

		if (isset(static::$_sqlGroups[$sid]))
			return ' GROUP BY ' . implode(', ', static::$_sqlGroups[$sid]);
	}

	private static function _buildSort() // ok
	{
		$sid = static::_getQuerySessionId();

		if (isset(static::$_sqlSorts[$sid]))
			return ' ORDER BY ' . implode(', ', static::$_sqlSorts[$sid]);
	}

	private static function _buildLimit() // ok
	{
		$sid = static::_getQuerySessionId();
		$sql = '';

		// if number, PHP will convert to 1.844674407371E+19
		if (isset(static::$_sqlSkip[$sid]) and empty(static::$_sqlTake[$sid]))
			static::$_sqlTake[$sid] = '18446744073709551615';

		if (isset(static::$_sqlTake[$sid]))
			$sql = ' LIMIT ' . static::$_sqlTake[$sid];

		if (isset(static::$_sqlSkip[$sid]))
			$sql .= ' OFFSET ' . static::$_sqlSkip[$sid];

		return $sql;
	}

	// other

	public static function formatTableName($table) // ok
	{
		if (substr($table, 0, 1) != static::$_identifierLeft and substr($table, 0, strlen(DB_PREFIX)) != DB_PREFIX)
			$table = DB_PREFIX . ucfirst($table);

		return $table;
	}

	public static function wrapTable($table) // ok
	{
		$pos = stripos($table, ' AS ');

		if ($pos)
		{
			$haystack = $table;

			$table = substr($haystack, 0, $pos);
			$alias = substr($haystack, $pos + 4);

			$table = trim($table);
			$alias = trim($alias);
		}
		else
		{
			$table = trim($table);
			$alias = '';
		}

		$table = static::formatTableName($table);
		$table = static::$_identifierLeft . $table . static::$_identifierRight;

		if ($alias)
		{
			$alias = static::$_identifierLeft . $alias . static::$_identifierRight;
			$table .= ' AS ' . $alias;
		}

		return $table;
	}

	public static function wrapColumn($columns) // ok
	{
		if (is_array($columns) == false)
		{
			$columns = explode(',', $columns);
			$returnArray = false;
		}
		else
			$returnArray = true;

		$returnData = [];

		foreach ($columns as $column)
		{
			if (trim($column) == '*')
				$column = '*';
			else
			{
				$pos = stripos($column, ' AS ');

				if ($pos)
				{
					$haystack = $column;

					$column = substr($haystack, 0, $pos);
					$alias = substr($haystack, $pos + 4);

					$column = trim($column);
					$alias = trim($alias);
				}
				else
				{
					$column = trim($column);
					$alias = '';
				}

				$arr = explode('.', $column);
				$column = static::$_identifierLeft . $arr[0] . static::$_identifierRight;

				if (isset($arr[1]))
					$column .= '.' . static::$_identifierLeft . $arr[1] . static::$_identifierRight;

				if ($alias)
					$column .= ' AS ' . static::$_identifierLeft . $alias . static::$_identifierRight;
			}

			$returnData[] = $column;
		}

		if ($returnArray == false)
			$returnData = implode(', ', $returnData);

		return $returnData;
	}

//	public static function wrapColumnWhere($column)
//	{
//		$arr = explode('.', $column);
//		$column = static::$_identifierLeft . $arr[0] . static::$_identifierRight;
//
//		if (isset($arr[1]))
//			$column .= '.' . static::$_identifierLeft . $arr[1] . static::$_identifierRight;
//
//		return $column;
//	}

	public static function wrap($column) // ok
	{
		return static::wrapColumn($column);
	}

	public static function escape($value) // ok
	{
		// Have to use both of is_string() and is_numeric()
		// function to check data type because number sent
		// via url ie delete?id[]=1&id[]=2 will be string.
		if (is_string($value) and !is_numeric($value))
			$value = static::$_connection->quote($value);
		elseif (is_bool($value))
			$value = ($value === false) ? 0 : 1;
		elseif (is_null($value))
			$value = 'NULL';

		return $value;
	}

    public static function escapeLike($value) // ok
    {
	    $trimQuote = false;

	    if (is_string($value))
	    	$trimQuote = true;

        $value = static::escape($value);
        $value = addcslashes($value, '%_');

        if ($trimQuote)
	        $value = substr($value, 1, -1);

        return $value;
    }

	public static function getNewOrdering() // ok
	{
		$sid = static::_getQuerySessionId();
		$table = @static::$_sqlTable[$sid];
		$ordering = static::wrapColumn('ordering');

		// TODO do i need to lock table first?

		$sql = 'SELECT IFNULL(MAX(' . $ordering . '), 0) + 1 '
			. 'AS ' . $ordering . ' '
			. 'FROM ' . $table;

		if (DEV_MODE)
			static::$_queries[] = $sql;

		$result = static::$_connection->query($sql);
		$row = $result->fetch();

		return $row->ordering;
	}

	public static function getLastQuery() // ok
	{
		return end(static::$_queries);
	}

	public static function getAllQueries() // ok
	{
		return static::$_queries;
	}

	public static function getLastInsertId() // ok
	{
		return static::$_connection->lastInsertId();
	}

	public static function getAffectedRows() // ok
	{
		return static::$_affectedRows;
	}

	public static function exists($data = null) // ok
	{
		$sid = static::_getQuerySessionId();
		$data = (array)$data;

		if (is_numeric(@$data[0]))
		{
			$data['id'] = $data[0];
			unset($data[0]);
		}

		$columns = array_keys($data);
		$values = array_values($data);
		$where = [];

		$n = count($columns);

		for ($i = 0; $i < $n; ++$i)
		{
			if ($columns[$i] == 'id')
				continue;

			$where[] = static::wrapColumn($columns[$i]) . ' = ' . static::escape($values[$i]);
		}

		$where = implode(' AND ', $where);

		$idcol = static::wrapColumn('id');
		$table = @static::$_sqlTable[$sid];

		if (@$data['id'])
		{
			$value = static::escape($data['id']);

			if ($where)
				$where .= ' AND ' . $idcol . ' != ' . $value;
			else
				$where = $idcol . ' = ' . $value;
		}

		$args = func_get_args();

		if (isset($args[1])) // Called from Model::exists()
		{
			if (static::columnExists('status') and !array_key_exists('status', $data))
			{
				if ($where)
					$where .= ' AND ';

				$where .= static::wrapColumn('status') . ' > -1';
			}
		}

		$sql = 'SELECT ' . $idcol . ' FROM ' . $table;

		if ($where)
			$sql .= ' WHERE ' . $where;

		$sql .= ' LIMIT 1 ';

		if (DEV_MODE)
			static::$_queries[] = $sql;

		$result = static::$_connection->query($sql);
		$row = $result->fetch();

		if (empty($row->id))
			return false;
		else
			return true;
	}

	public static function getTables() // ok
	{
		if (is_array(static::$_tables) == false)
		{
			$filename = '_tables_.php';

			if (File::exists(static::$_dbCachePath . $filename))
			{
				$content = file_get_contents(static::$_dbCachePath . $filename);
				$content = substr($content, 8);

				static::$_tables = @unserialize($content);
			}

			if (empty(static::$_tables))
			{
				$sql = 'SHOW TABLES';
				$result = static::$_connection->query($sql);

				if (DEV_MODE)
					static::$_queries[] = $sql;

				while ($table = $result->fetch())
				{
					// Get first element of object or array
					$table = current($table);
					static::$_tables[] = $table;
				}

				$fp = fopen(static::$_dbCachePath . $filename, 'w');
				fwrite($fp, '<?php //'.serialize(static::$_tables));
				fclose($fp);
			}
		}

		return static::$_tables;
	}

	private static function _getColumnInfo($table = null) // ok
	{
		if ($table)
			$table = static::formatTableName($table);
		else
		{
			$sid = static::_getQuerySessionId();
			$table = @static::$_sqlTable[$sid];
		}

		if (isset(static::$_info[$table]) == false)
		{
			$filename = $table;
			$filename = ltrim($filename, static::$_identifierLeft);
			$filename = rtrim($filename, static::$_identifierRight);
			$file = static::$_dbCachePath . $filename . '.php';

			if (File::exists($file))
			{
				$content = file_get_contents($file);
				$content = substr($content, 8);

				static::$_info[$table] = unserialize($content);
			}
			else
			{
				$sql = 'DESCRIBE ' . $table;
				$result = static::$_connection->query($sql);

				if (DEV_MODE)
					static::$_queries[] = $sql;

				$rows = $result->fetchAll();
				$tableInfo = [];
				$i = 0;

				foreach ($rows as $row)
				{
					$column = $row->Field;
					$columnPosition = ++$i;
					$dataType = $row->Type;
					$default = $row->Default;
					$nullable = ($row->Null == 'YES');
					$length = null;
					$scale = null;
					$precision = null;
					$unsigned = (strpos($row->Type, 'unsigned') !== false);
					$primary = ($row->Key == 'PRI');
					$primaryPosition = ($primary ? $columnPosition : null);
					$autoIncrement = ($row->Extra == 'auto_increment');

					if (strpos($dataType, '('))
					{
						$arr = explode('(', $dataType);
						$dataType = $arr[0];
						$length = Str::trimRight($arr[1]);

						if (strpos($length, ','))
						{
							$arr = explode(',', $length);
							$scale = $arr[1];
							$precision = $arr[0];
							$length = null;
						}
					}

					$tableInfo[$row->Field]['TABLE_NAME'] = $table;
					$tableInfo[$row->Field]['COLUMN_NAME'] = $column;
					$tableInfo[$row->Field]['COLUMN_POSITION'] = $columnPosition;
					$tableInfo[$row->Field]['DATA_TYPE'] = $dataType;
					$tableInfo[$row->Field]['DEFAULT'] = $default;
					$tableInfo[$row->Field]['NULLABLE'] = $nullable;
					$tableInfo[$row->Field]['LENGTH'] = $length;
					$tableInfo[$row->Field]['SCALE'] = $scale;
					$tableInfo[$row->Field]['PRECISION'] = $precision;
					$tableInfo[$row->Field]['UNSIGNED'] = $unsigned;
					$tableInfo[$row->Field]['PRIMARY'] = $primary;
					$tableInfo[$row->Field]['PRIMARY_POSITION'] = $primaryPosition;
					$tableInfo[$row->Field]['IDENTITY'] = '';
					$tableInfo[$row->Field]['AUTO_INCREMENT'] = $autoIncrement;
				}

				static::$_info[$table] = $tableInfo;

				$fp = fopen($file, 'w');
				fwrite($fp, '<?php //'.serialize($tableInfo));
				fclose($fp);
			}
		}

		return static::$_info[$table];
	}

	public static function getColumnListing($table = null) // ok
	{
		$info = static::_getColumnInfo($table);
		$columns = array_keys($info);

		return $columns;
	}

	public static function column($column)
	{
		$info = static::_getColumnInfo();
		$data = new \stdClass();

		$data->name = $info[$column]['COLUMN_NAME'];
		$data->position = $info[$column]['COLUMN_POSITION'];
		$data->datatype = $info[$column]['DATA_TYPE'];
		$data->default = $info[$column]['DEFAULT'];
		$data->nullable = $info[$column]['NULLABLE'];
		$data->length = $info[$column]['LENGTH'];
		$data->scale = $info[$column]['SCALE'];
		$data->precision = $info[$column]['PRECISION'];
		$data->unsigned = $info[$column]['UNSIGNED'];

		return $data;
	}

	public static function tableExists($table) // ok
	{
        $table = static::formatTableName($table);
		$tables = static::getTables();

		return Arr::has($tables, $table);
	}

    public static function columnExists($column) // ok
    {
        $columns = static::getColumnListing();

        return in_array($column, $columns);
    }

	public static function lockTable($table) // ok
	{
		// TODO why don't wrap $table name ???!!!
		$sql = 'LOCK TABLES ' . $table . ' WRITE, ' . static::wrapTable('Session') . ' WRITE';

		if (static::$_transactionMode)
			static::$_transactionSqls[] = $sql;
		else
		{
			static::$_connection->query($sql);

			if (DEV_MODE)
				static::$_queries[] = $sql;
		}

	}

	public static function unlockTables() // ok
	{
		$sql = 'UNLOCK TABLES';

		if (static::$_transactionMode)
			static::$_transactionSqls[] = $sql;
		else
		{
			static::$_connection->query($sql);

			if (DEV_MODE)
				static::$_queries[] = $sql;
		}
	}

	public static function getPreparedSelect() // ok
	{
		$sql = static::_buildQuerySelect();
		//static::reset();

		return $sql;
	}

	public static function getPreparedInsert($data) // ok
	{
		$sql = static::_buildQueryInsert($data);
		//static::reset();

		return $sql;
	}

	public static function getPreparedUpdate($data) // ok
	{
		$sql = static::_buildQueryUpdate($data);
		//static::reset();

		return $sql;
	}

	public static function getPreparedSave($data) // ok
	{
		$sql = static::_buildQuerySave($data);
		//static::reset();

		return $sql;
	}

	public static function getPreparedDelete() // ok
	{
		$where = static::_buildWhere();
		$sql = static::_buildQueryDelete($where);

		//static::reset();

		return $sql;
	}

//	private static function _columnsToString($columns) // ok
//	{
//		if (is_array($columns))
//			$columns = implode(', ', $columns);
//
//		return $columns;
//	}

	private static function _columnsToArray($columns) // ok
	{
		if (is_array($columns) == false)
			$columns = explode(',', $columns);

		return $columns;
	}

	public static function version() // ok
	{
		static::reset();

		return static::raw('SELECT VERSION()')->loadSingle();
	}

	private static function _displayError()
	{
		echo '<style>body { font-family: tahoma, arial; font-size: 12px; background: #222; color: #b5d5ff; }</style>';
		echo '<fieldset><legend>SQL Error</legend>' . static::$_connection->errorInfo()[2] . '</fieldset>';
		echo '<fieldset><legend>In Query</legend>' . static::getLastQuery() . '</fieldset>';

		if (Paginator::$sortcol)
		{
			$cookie = Uri::getContext() . 'sortcol';

			echo '<script type="text/javascript">
			function fixit()
			{
                document.cookie = \'' . $cookie . '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/\';
                location.reload();
			}
			</script>';

			echo '<p>Try to remove cookie to fix it or 
				<span style="cursor:pointer; text-decoration:underline; font-weight:bold;" 
					onclick="fixit();">
					click here.
				</span></p>';
		}

		exit;
	}

	public static function reset()
	{
		$sid = static::_getQuerySessionId();

		unset(static::$_sqlRaw[$sid]);
		unset(static::$_sqlSelects[$sid]);
		unset(static::$_sqlTable[$sid]);
		unset(static::$_sqlJoins[$sid]);
		unset(static::$_sqlWheres[$sid]);
		unset(static::$_sqlGroups[$sid]);
		unset(static::$_sqlSorts[$sid]);
		unset(static::$_sqlTake[$sid]);
		unset(static::$_sqlSkip[$sid]);
		unset(static::$_autoSearchKeyword[$sid]);
		unset(static::$_autoSearchColumns[$sid]);
		static::$_transactionMode = null;
		static::$_transactionSqls = null;
	}

	public static function set($key, $value = null, $escape = true)
	{
//		$sid = static::_getQuerySessionId();
//
//		if ($escape)
//		{
//
//		}
//
//		if (isset(static::$_datas[$sid]))
//			else
//				static::$_datas[$sid] = [$key => $value];
	}
}
