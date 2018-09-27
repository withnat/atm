<?php
namespace System;

/**
 * private static $_sqlWheres = [
 *  ['operator' => 'AND', 'condition' => 'name=Nat'],
 *  ['operator' => 'OR', 'condition' => '('],
 *  ['operator' => 'AND', 'condition' => 'votes > 100'],
 *  ['operator' => 'AND', 'condition' => 'title <> 'Admin''],
 *  ['operator' => '', 'condition' => ')']
]
 */

final class DB_bak1
{
	private static $_instance;
	private static $_connection;
	private static $_dbCachePath;
	private static $_queryCachePath;
	private static $_tables;
	private static $_info;
	private static $_sqlRaw;
	private static $_sqlSelects;
	private static $_sqlTable;
	private static $_sqlJoins;
	private static $_sqlWheres;
	private static $_sqlGroups;
	private static $_sqlSorts;
	private static $_sqlTake;
	private static $_sqlSkip;
    private static $_autoSearchKeyword;
    private static $_autoSearchColumns;
	private static $_transactionMode;
	private static $_transactionSqls;
    private static $_queries;

	private function __construct() // ok
	{
		static::$_dbCachePath = BASEPATH_TMP.'/cache/db/';
		static::$_queryCachePath = BASEPATH_TMP.'/cache/queries/';

		if (is_null(static::$_connection))
			static::_connect();

		static::$_instance = $this;
	}

	private static function _connect() // ok
	{
		if (DEV_MODE)
		{
			Folder::delete(static::$_dbCachePath);
			Folder::delete(static::$_dbCachePath);
		}

		Folder::create(static::$_dbCachePath);
		Folder::create(static::$_queryCachePath);

		$dbhost = DB_HOST;
		$dbport = DB_PORT;
		$dbname = DB_NAME;
		$dbuser = DB_USER;
		$dbpassword = DB_PASSWORD;
		$dbsocket = DB_SOCKET;

		if (trim($dbhost) == '') $dbhost = 'localhost';
		if (trim($dbuser) == '') $dbuser = 'root';

		static::$_connection = new \mysqli($dbhost, $dbuser, $dbpassword, $dbname, $dbport, $dbsocket);

		if (static::$_connection->connect_errno)
			die('Unable to connect to the database.');

		static::$_connection->query('SET NAMES \'utf8\'');
	}

	private static function _getInstance() // ok
	{
		if (is_null(static::$_instance))
			static::$_instance = new static;

		return static::$_instance;
	}

	private static function _getQuerySessionId() // ok
	{
		$debugInfo = debug_backtrace();

		for ($i=1, $n=count($debugInfo); $i < $n; ++$i)
		{
			if ($debugInfo[$i]['file'] != __FILE__)
				break;
		}

		$file = debug_backtrace()[$i]['file'];
		$function = debug_backtrace()[$i+1]['function'];

		if ($function == '{closure}')
		{
			$className = File::getName($file);
			$className = Str::replace($className, EXT, '');
			$class = new \ReflectionClass($className);

			$methods = $class->getMethods();
			$methods = Arr::getColumn($methods, 'name');

			$i += 1;

			for ($i; $i < $n; ++$i)
			{
				$function = debug_backtrace()[$i]['function'];

				if (Arr::has($methods, $function))
					break;
			}
		}

		return $file . '.' . $function;
	}

	public static function table($table) // ok
	{
		static::from($table);

        return static::_getInstance();
	}

	// select

    public static function autoSearchKeyword($keyword) // ok
    {
	    $sid = static::_getQuerySessionId();
        static::$_autoSearchKeyword[$sid] = $keyword;
    }

    public static function autoSearchColumn($columns = '*') // ok
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

	public static function select($columns = '*') // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::wrapColumn($columns);
		$columns = static::_columnsToArray($columns);

		if (isset(static::$_sqlSelects[$sid]))
			static::$_sqlSelects[$sid] = array_merge(static::$_sqlSelects[$sid], $columns);
		else
			static::$_sqlSelects[$sid] = $columns;

		return static::_getInstance();
	}

	public static function average($columns) // ok
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

	private static function _columnsToString($columns) // ok
	{
		if (is_array($columns))
			$columns = implode(', ', $columns);

		return $columns;
	}

	private static function _columnsToArray($columns) // ok
	{
		if (is_array($columns) == false)
		{
			$columns = explode(',', $columns);
			$columns = Str::trim($columns);
		}

		return $columns;
	}

	public static function distinct($column) // ok
	{
		$sid = static::_getQuerySessionId();
		$column = static::wrapColumn($column);
		$column = static::_columnsToString($column);

		static::$_sqlSelects[$sid][] = 'DISTINCT(' . $column . ')';

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

	// from

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

		    $arr[0] = static::wrapColumn($arr[0]);
		    $arr[1] = static::wrapColumn(@$arr[1]);

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
			DB::transaction(function() use ($sql){
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

		static::raw($sql)->execute();

		return static::getAffectedRows();
	}

	public static function save($data) // ok
	{
		$sid = static::_getQuerySessionId();
		$where = static::_buildWhere();
		$tableInfo = DB::listColumns(@static::$_sqlTable[$sid]);

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

			if (Arr::isAssoc($data) or is_object($data))
				$datas[] = $data;
			else
				$datas = $data;

			if (array_key_exists('ordering', $tableInfo) and array_key_exists('ordering', $datas[0]) == false)
			{
				$autoOrdering = true;
				static::lockTable(@static::$_sqlTable[$sid]);
			}

			$sql = static::_buildQuerySave($data);

			if (count($datas) > 1)
			{
				DB::transaction(function() use ($sql){
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
		$columns = static::wrapColumn($columns);
		$columns = static::_columnsToArray($columns);
		$where = static::_buildWhere();
		$num = (float)$num;

		$sql = 'UPDATE ' . @static::$_sqlTable[$sid] . ' SET ';
		$sets = [];

		foreach ($columns as $column)
			$sets[] = $column . ' = ' . $column . ' + ' . $num;

		$sql .= ' ' . static::_columnsToString($sets);

		if ($where)
			$sql .= ' ' . $where;

		DB::raw($sql)->execute();

		return static::getAffectedRows();
	}

	public static function decrease($columns, $num = 1) // ok
	{
		$sid = static::_getQuerySessionId();
        $columns = static::wrapColumn($columns);
		$columns = static::_columnsToArray($columns);
        $where = static::_buildWhere();
		$num = (float)$num;

        $sql = 'UPDATE ' . @static::$_sqlTable[$sid] . ' SET ';
		$sets = [];

		foreach ($columns as $column)
			$sets[] = $column . ' = ' . $column . ' - ' . $num;

		$sql .= ' ' . static::_columnsToString($sets);

        if ($where)
            $sql .= ' ' . $where;

		DB::raw($sql)->execute();

        return static::getAffectedRows();
	}

    public static function enable() // ok
    {
        return static::update(['status' => 1]);
    }

    public static function disable() // ok
    {
        return static::update(['status' => 0]);
    }

    public static function trash() // ok
    {
        return static::update(['status' => -1]);
    }

    public static function discontinue() // ok
    {
        return static::update(['status' => -2]);
    }

	private static function _extractDataToColumnAndValue($data) // ok
	{
		$columns = [];
		$values = [];

		foreach ($data as $column => $value)
		{
            $columns[] = $column;
            $values[] = $value;
		}

		return [$columns, $values];
	}

	// delete

	public static function delete($deleteUploadedFiles = true, $fileBackupPath = null) // ok
	{
		$where = static::_buildWhere();

		if ($where == '')
			return false;

		if ($fileBackupPath)
			static::_backupUploadedFiles($fileBackupPath, $where);

		if ($deleteUploadedFiles)
			static::_deleteUploadedFiles($where);

		$sql = DB::_buildQueryDelete($where);
		static::_query($sql);

		return static::getAffectedRows();
	}

    public static function deleteAll($deleteUploadedFiles = true, $fileBackupPath = null) // ok
    {
	    if ($fileBackupPath)
		    static::_backupUploadedFiles($fileBackupPath);

	    if ($deleteUploadedFiles)
		    static::_deleteUploadedFiles();

	    $sql = DB::_buildQueryDelete();
	    static::_query($sql);

        return static::getAffectedRows();
    }

	public static function truncate($deleteUploadedFiles = true, $fileBackupPath = null) // ok
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

		if ($result)
			static::$_queries[] = $sql;
		else
			static::deleteAll();
	}

	private static function _deleteUploadedFiles($where = null) // ok
	{
		$sid = static::_getQuerySessionId();
		$table = @static::$_sqlTable[$sid];

		$sql = 'SELECT * FROM ' . $table;

		if ($where)
			$sql .= ' ' . $where;

		// Have to use direct mysqli query because in case we call
		// method truncate() without DROP privilege. System will calls
		// method deleteAll() automatically. And static::$_sqlTable
		// will be removed by this method that called in method
		// truncate() already.
		$result = static::$_connection->query($sql);

		static::$_queries[] = $sql;
		$deleted = 0;

		while ($row = $result->fetch_assoc())
		{
			foreach ($row as $value)
			{
				if (stripos($value, BASEPATH_ASSETS) !== false)
				{
					if (File::delete($value))
						++$deleted;
				}
			}
		}

		return $deleted;
	}

	private static function _backupUploadedFiles($backupPath, $where = null) // ok
	{
		$sid = static::_getQuerySessionId();
		$table = @static::$_sqlTable[$sid];
		$backupPath = Str::wrapRight($backupPath, '/');

		Folder::create($backupPath);

		$sql = 'SELECT * FROM ' . $table;

		if ($where)
			$sql .= ' ' . $where;

		static::$_queries[] = $sql;

		$result = static::$_connection->query($sql);
		$backedup = 0;

		while ($row = $result->fetch_assoc())
		{
			foreach ($row as $value)
			{
				if (stripos($value, BASEPATH_ASSETS) !== false)
				{
					if (File::isExists($value))
					{
						$filename = File::getName($value);
						@copy($value, $backupPath.$filename);

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
		$sid = static::_getQuerySessionId();
		$args = func_get_args();

		if (is_callable($args[0]))
		{
			static::groupStart();
			$args[0]();
			static::groupEnd();
		}
		else
		{
			$where = static::_parseWhere($args);

			if ($where)
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
        $where = '';

		if (count($args) == 1)
        {
            if (is_numeric(trim($args[0])))
                $where = static::wrapColumn('id') . ' = ' . $args[0];
            else
                $where = $args[0];
        }
        elseif (count($args) > 1)
		{
            $where = $args[0];

			if (strpos($args[0], '?') !== false)
			{
				for ($i = 1, $n = count($args); $i < $n; ++$i)
					$where = Str::replace($where, '?', static::quote($args[$i]), 1);
			}
			elseif (strpos($args[0], ':') !== false)
			{
				foreach ($args[1] as $key => $value)
					$where = Str::replace($where, $key, static::quote($value));
			}
			else
			{
				$column = static::wrapColumn($args[0]);
                $operator = '';
                $value = '';

				if (count($args) == 2)
				{
					$operator = '=';
					$value = $args[1];
				}
				elseif (count($args) > 2)
				{	
					$operator = trim($args[1]);
					$value = $args[2];
				}

				$operator = static::escape($operator);
				$value = static::quote($value);

				$where = $column . ' ' . $operator . ' ' . $value;
			}
		}

		return $where;
	}

	// where between

	public static function whereBetween($columns, $start, $end) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);
		$start = static::quote($start);
		$end = static::quote($end);

		foreach ($columns as $column)
            static::$_sqlWheres[$sid][] = ['AND', $column . ' BETWEEN ' . $start . ' AND ' . $end];

		return static::_getInstance();
	}

	public static function orWhereBetween($columns, $start, $end) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);
		$start = static::quote($start);
		$end = static::quote($end);

		foreach ($columns as $column)
            static::$_sqlWheres[$sid][] = ['OR', $column . ' BETWEEN ' . $start . ' AND ' . $end];

        return static::_getInstance();
	}

	public static function whereNotBetween($columns, $start, $end) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);
		$start = static::quote($start);
		$end = static::quote($end);

		foreach ($columns as $column)
            static::$_sqlWheres[$sid][] = ['AND', $column . ' NOT BETWEEN ' . $start . ' AND ' . $end];

        return static::_getInstance();
	}

	public static function orWhereNotBetween($columns, $start, $end) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);
		$start = static::quote($start);
		$end = static::quote($end);

		foreach ($columns as $column)
            static::$_sqlWheres[$sid][] = ['OR', $column . ' NOT BETWEEN ' . $start . ' AND ' . $end];

        return static::_getInstance();
	}

	// where like

	public static function whereContain($columns, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);
		$value = static::escapeLike($value);

		foreach ($columns as $column)
            static::$_sqlWheres[$sid][] = ['AND', $column . ' LIKE \'%' . $value . '%\''];

        return static::_getInstance();
	}

	public static function orWhereContain($columns, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);
		$value = static::escapeLike($value);

		foreach ($columns as $column)
			static::$_sqlWheres[$sid][] = ['OR', $column . ' LIKE \'%' . $value . '%\''];

        return static::_getInstance();
	}

	public static function whereStartWith($columns, $value) // ok
    {
	    $sid = static::_getQuerySessionId();
	    $columns = static::_columnsToArray($columns);
	    $columns = static::wrapColumn($columns);
	    $value = static::escapeLike($value);

	    foreach ($columns as $column)
			static::$_sqlWheres[$sid][] = ['AND', $column . ' LIKE \'' . $value . '%\''];

		return static::_getInstance();
	}

	public static function orWhereStartWith($columns, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);
		$value = static::escapeLike($value);

		foreach ($columns as $column)
			static::$_sqlWheres[$sid][] = ['OR', $column . ' LIKE \'' . $value . '%\''];

        return static::_getInstance();
	}

	public static function whereEndWith($columns, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);
		$value = static::escapeLike($value);

		foreach ($columns as $column)
			static::$_sqlWheres[$sid][] = ['AND', $column . ' LIKE \'%' . $value . '\''];

        return static::_getInstance();
	}

	public static function orWhereEndWith($columns, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);
		$value = static::escapeLike($value);

		foreach ($columns as $column)
			static::$_sqlWheres[$sid][] = ['OR', $column . ' LIKE \'%' . $value . '\''];

        return static::_getInstance();
	}

	public static function whereNotContain($columns, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);
		$value = static::escapeLike($value);

		foreach ($columns as $column)
			static::$_sqlWheres[$sid][] = ['AND', $column . ' NOT LIKE \'%' . $value . '%\''];

        return static::_getInstance();
	}

	public static function orWhereNotContain($columns, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);
		$value = static::escapeLike($value);

		foreach ($columns as $column)
			static::$_sqlWheres[$sid][] = ['OR', $column . ' NOT LIKE \'%' . $value . '%\''];

        return static::_getInstance();
	}

	public static function whereNotStartWith($columns, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);
		$value = static::escapeLike($value);

		foreach ($columns as $column)
			static::$_sqlWheres[$sid][] = ['AND', $column . ' NOT LIKE \'' . $value . '%\''];

        return static::_getInstance();
	}

	public static function orWhereNotStartWith($columns, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);
		$value = static::escapeLike($value);

		foreach ($columns as $column)
			static::$_sqlWheres[$sid][] = ['OR', $column . ' NOT LIKE \'' . $value . '%\''];

        return static::_getInstance();
	}

	public static function whereNotEndWith($columns, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);
		$value = static::escapeLike($value);

		foreach ($columns as $column)
			static::$_sqlWheres[$sid][] = ['AND', $column . ' NOT LIKE \'%' . $value . '\''];

        return static::_getInstance();
	}

	public static function orWhereNotEndWith($columns, $value) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);
		$value = static::escapeLike($value);

		foreach ($columns as $column)
			static::$_sqlWheres[$sid][] = ['OR', $column . ' NOT LIKE \'%' . $value . '\''];

        return static::_getInstance();
	}

	// where in

	public static function whereIn($columns, $values) // ok
	{
        static::_setWhereIn('IN', $columns, $values);

        return static::_getInstance();
	}

	public static function orWhereIn($columns, $values) // ok
	{
        static::_setWhereIn('IN', $columns, $values, 'OR');

        return static::_getInstance();
	}

	public static function whereNotIn($columns, $values) // ok
	{
        static::_setWhereIn('NOT IN', $columns, $values);

        return static::_getInstance();
	}

	public static function orWhereNotIn($columns, $values) // ok
	{
        static::_setWhereIn('NOT IN', $columns, $values, 'OR');

        return static::_getInstance();
	}

	private static function _setWhereIn($operator, $columns, $values, $condition = null) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);
		$values = static::quote($values);
		$values = (array)$values;

		foreach ($columns as $column)
		{
			$where = $column . $operator . ' (' . implode(', ', $values) . ')';

			if ($condition == 'OR')
				static::$_sqlWheres[$sid][] = ['OR', $where];
			else
				static::$_sqlWheres[$sid][] = ['AND', $where];
		}
	}

	// where null

	public static function whereNull($columns) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);

		foreach ($columns as $column)
			static::$_sqlWheres[$sid][] = ['AND', $column . ' IS NULL'];

        return static::_getInstance();
	}

	public static function orWhereNull($columns) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);

		foreach ($columns as $column)
            static::$_sqlWheres[$sid][] = ['OR', $column . ' IS NULL'];

        return static::_getInstance();
	}

	public static function whereNotNull($columns) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);

		foreach ($columns as $column)
            static::$_sqlWheres[$sid][] = ['AND', $column . ' IS NOT NULL'];

        return static::_getInstance();
	}

	public static function orWhereNotNull($columns) // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);

		foreach ($columns as $column)
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

	public static function sort($columns, $mode = 'ASC') // ok
	{
		$sid = static::_getQuerySessionId();
		$columns = static::_columnsToArray($columns);
		$columns = static::wrapColumn($columns);

		foreach ($columns as $column)
			static::$_sqlSorts[$sid][] = $column . ' ' . strtoupper($mode);

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

		$sql = $block1.$block2;
		static::$_queries[] = $sql;

		if (static::$_transactionMode)
			static::$_transactionSqls[] = $sql;
		else
		{
			$result = static::$_connection->query($sql);

			if (static::$_connection->error and DEV_MODE)
				static::_displayError();

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

		if (strripos($sql, ' LIMIT ') == false)
			$sql .= ' LIMIT 1 ';

		$result = static::_query($sql);

		if ($result)
		{
			$data = new \stdClass();

			if ($result->num_rows)
			{
				$row = $result->fetch_assoc();

				foreach ($row as $key => $value)
				{
					if ($key == 'options')
					{
						if ($value)
							$data->{$key} = @unserialize($value);
						else
							$data->{$key} = null;
					}
					else
						$data->{$key} = $value;
				}

				$result->free();

				return $data;
			}
			else
			{
				while ($field = $result->fetch_field())
					$data->{$field->name} = null;

				return $data;
			}
		}
		else
			static::_displayError();
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
			$rs = [];

			while ($row = $result->fetch_assoc())
			{
				$data = new \stdClass();

				if ($paginate)
				{
					$data->{':no'} = $no;
					++$no;
				}

				foreach ($row as $key => $value)
				{
					if ($key == 'options')
					{
						if ($value)
							$data->{$key} = @unserialize($value);
						else
							$data->{$key} = null;
					}
					else
						$data->{$key} = $value;
				}

				$rs[] = $data;
			}

			$result->free();

			return $rs;
		}
		else
			static::_displayError();
	}

	public static function paginate(Paginator $paginator = null) // ok
	{
		$sid = static::_getQuerySessionId();
		$sql = static::_buildQuerySelect();

		if (is_null($paginator))
			$paginator = new Paginator();

		$sqlCount = 'SELECT COUNT(*) ' . substr($sql, stripos($sql, ' FROM '));
		$paginator->total = static::raw($sqlCount)->loadSingle();

		$paginator->prepare();
		$limitPos = strripos($sql, ' LIMIT ');

		if ($paginator->sortcolumn and strripos($sql, ' ORDER BY ') === false)
		{
			// clean up cookie value.
			$sortcolumn = static::escape($paginator->sortcolumn);
			$sortway = static::escape($paginator->sortway);

			$sortcolumn = static::wrapColumn($sortcolumn);
			$orderBy = ' ORDER BY ' . $sortcolumn . ' ' . $sortway;

			if ($limitPos)
			{
				$limit = substr($sql, $limitPos);
				$sql = substr($sql, 0, $limitPos);

				$sql .= $orderBy . $limit;
			}
			else
				$sql .= $orderBy;
		}

		if ($paginator->total and empty($limitPos))
		{
			// clean up cookie value.
			$page = static::escape($paginator->page);
			$pagesize = static::escape($paginator->pagesize);

			// ensure integer
			$page = (int)$page;
			$pagesize = (int)$pagesize;

			$offset = $pagesize * ($page - 1);

			$sql .= ' LIMIT ' . $pagesize . ' OFFSET ' . $offset;
		}

		static::$_sqlRaw[$sid] = $sql;

		return static::loadAll($paginator);
	}

	public static function toJSON() // ok
	{
		return json_encode(static::loadAll());
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
		static::$_connection->begin_transaction();
		static::$_transactionMode = true;
	}

	public static function commit() // ok
	{
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
		if (is_array(static::$_transactionSqls) == false)
			return false;

		foreach (static::$_transactionSqls as $sql)
		{
			static::$_queries[] = $sql;

			if (static::$_connection->query($sql) == false)
				return false;
		}

		return true;
	}

	// build

	private static function _buildQuerySelect() // ok
	{
		$sid = static::_getQuerySessionId();

		if (isset(static::$_sqlRaw[$sid]))
			return static::$_sqlRaw[$sid];

		$sql = 'SELECT ';

		if (isset(static::$_sqlSelects[$sid]))
			$columns = static::$_sqlSelects[$sid];
		else
			$columns = ['*'];

		$sql .= implode(', ', $columns);

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
		$table = @static::$_sqlTable[$sid];
		$datas = [];
		$columns = [];
		$values = '';

		if (Arr::isAssoc($data) or is_object($data))
			$datas[] = $data;
		else
			$datas = $data;

		foreach ($datas as $data)
		{
			$arr = static::_extractDataToColumnAndValue($data);

			if (empty($columns))
			{
				foreach ($arr[0] as $key => $value)
					$arr[0][$key] = static::wrapColumn($value);

				$columns = $arr[0];
			}

			foreach ($arr[1] as $key => $value)
				$arr[1][$key] = static::quote($value);

			$values .= '(' . implode(', ', $arr[1]) . '), ';
		}

		$columns = implode(', ', $columns);
		$values = Str::trimRight($values, 2);

		$sql = 'INSERT INTO ' . $table . ' (' . $columns . ') VALUES ' . $values;

		return $sql;
	}

	private static function _buildQueryUpdate($data) // ok
	{
		if (is_array($data) and Arr::isAssoc($data) == false)
			$data = $data[0];

		$sid = static::_getQuerySessionId();
		$where = static::_buildWhere();

		$arr = static::_extractDataToColumnAndValue($data);
		$columns = $arr[0];
		$values = $arr[1];

		$sql = 'UPDATE ' . @static::$_sqlTable[$sid] . ' SET ';

		for ($i = 0, $n = count($columns); $i < $n; ++$i)
			$sql .= static::wrapColumn($columns[$i]) . ' = ' . static::quote($values[$i]) . ', ';

		$sql = Str::trimRight($sql, ', ');

		if ($where)
			$sql .= ' '. $where;

		if (isset(static::$_sqlSorts[$sid]))
			$sql .= ' ORDER BY ' . implode(', ', static::$_sqlSorts[$sid]);

		if (isset(static::$_sqlTake[$sid]))
			$sql .= ' LIMIT ' . static::$_sqlTake[$sid];

		return $sql;
	}

	private static function _buildQuerySave($data) // ok
	{
		$sid = static::_getQuerySessionId();
		$where = static::_buildWhere();
		$tableInfo = DB::listColumns(@static::$_sqlTable[$sid]);

		if ($where)
		{
			if (is_array($data) and Arr::isAssoc($data) == false)
				$data = $data[0];

			if (array_key_exists('updated', $tableInfo) and array_key_exists('updated', $data) == false)
			{
				if (is_array($data))
					$data['updated'] = date('Y-m-d H:i:s');
				else
					$data->updated = date('Y-m-d H:i:s');
			}

			if (array_key_exists('updater', $tableInfo) and array_key_exists('updater', $data) == false)
			{
				if (is_array($data))
					$data['updater'] = (int)Auth::getIdentity()->id;
				else
					$data->updater = (int)Auth::getIdentity()->id;
			}

			$sql = static::_buildQueryUpdate($data);
		}
		else
		{
			$datas = [];
			$autoOrdering = false;

			if (Arr::isAssoc($data) or is_object($data))
				$datas[] = $data;
			else
				$datas = $data;

			if (array_key_exists('ordering', $tableInfo) and array_key_exists('ordering', $datas[0]) == false)
			{
				$autoOrdering = true;
				$ordering = static::getNewOrdering();
			}

			for ($i=0, $n = count($datas); $i < $n; ++$i)
			{
				if ($autoOrdering)
				{
					if (is_array($datas[$i]))
						$datas[$i]['ordering'] = $ordering;
					else
						$datas[$i]->ordering = $ordering;

					++$ordering;
				}

				if (array_key_exists('created', $tableInfo) and array_key_exists('created', $datas[$i]) == false)
				{
					if (is_array($datas[$i]))
						$datas[$i]['created'] = date('Y-m-d H:i:s');
					else
						$datas[$i]->created = date('Y-m-d H:i:s');
				}

				if (array_key_exists('creator', $tableInfo) and array_key_exists('creator', $datas[$i]) == false)
				{
					if (is_array($datas[$i]))
						$datas[$i]['creator'] = (int)Auth::getIdentity()->id;
					else
						$datas[$i]->creator = (int)Auth::getIdentity()->id;
				}
			}

			$sql = static::_buildQueryInsert($datas);
		}

		return $sql;
	}

	private static function _buildQueryDelete($where = null) // ok
	{
		$sid = static::_getQuerySessionId();
		$table = @static::$_sqlTable[$sid];

		$sql = 'DELETE FROM ' . $table;

		if ($where)
			$sql .= ' ' . $where;

		$sql .= static::_buildSort();
		$sql .= static::_buildLimit();

		return $sql;
	}

	private static function _buildFrom() // ok
	{
		$sid = static::_getQuerySessionId();

		$sql = ' FROM ' . @static::$_sqlTable[$sid];

		if (isset(static::$_sqlJoins[$sid]))
			$sql .= ' ' . implode(', ', static::$_sqlJoins[$sid]);

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

			if (Arr::has($searchColumns, '*'))
			{
				$searchColumns = [];

				if (isset(static::$_sqlTable[$sid]))
				{
					$columns = static::listColumns(static::$_sqlTable[$sid]);

					foreach ($columns as $column)
						$searchColumns[] = $column['COLUMN_NAME'];
				}
			}

			foreach ($searchColumns as $searchColumn)
				static::orWhereContain($searchColumn, static::$_autoSearchKeyword[$sid]);
		}

		$where = '';

		if (isset(static::$_sqlWheres[$sid]))
		{
			$where = ' WHERE ';
			$_sqlWheres = static::$_sqlWheres[$sid];

			for ($i=0, $n = count($_sqlWheres); $i < $n; ++$i)
			{
				if ($i > 0 and $_sqlWheres[$i][1] != ')' and $_sqlWheres[$i - 1][1] != '(')
					$where .= ' ' . $_sqlWheres[$i][0].' ';

				$where .= $_sqlWheres[$i][1];
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
		if ($table)
		{
			$table = Str::trim($table, '`');

			if (Str::getLeft($table, 2) == '#_')
				$table = Str::trimLeft($table, 2);
			elseif (Str::getLeft($table, strlen(DB_PREFIX)) == DB_PREFIX)
				$table = Str::trimLeft($table, strlen(DB_PREFIX));

			$table = ucfirst($table);
			$table = DB_PREFIX . $table;
		}

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

		$table = Str::trim($table, '`');
		$table = static::formatTableName($table);
		$table = Str::wrap($table, '`');

		if ($alias)
			$table .= ' AS ' . Str::wrap($alias, '`');

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
				$column = Str::wrap($arr[0], '`');

				if (isset($arr[1]))
					$column .= '.' . Str::wrap($arr[1], '`');

				if ($alias)
					$column .= ' AS ' . Str::wrap($alias, '`');
			}

			$returnData[] = $column;
		}

		if ($returnArray == false)
			$returnData = implode(', ', $returnData);

		return $returnData;
	}

	public static function wrap($column) // ok
	{
		return static::wrapColumn($column);
	}

	public static function quote($values) // ok
	{
		$returnArray = false;

		if (is_array($values))
			$returnArray = true;

		$values = (array)$values;

		foreach ($values as $key => $value)
		{
			// Cannot use is_string() because method
			// static::escape() will returns data type as string.
			if (is_numeric($value) == false)
			{
				$value = static::escape($value);
				$value = Str::wrap($value, '\'');
			}

			$values[$key] = $value;
		}

		if ($returnArray == false)
			$values = Arr::first($values);

        return $values;
	}

	public static function escape($value) // ok
	{
		return static::$_connection->escape_string($value);
	}

    public static function escapeLike($value) // ok
    {
        $value = static::escape($value);
        $value = addcslashes($value, '%_');

        return $value;
    }

	public static function getNewOrdering() // ok
	{
		$sid = static::_getQuerySessionId();

		$sql = 'SELECT MAX(' . DB::wrapColumn('ordering') . ') '
			. 'AS ' . DB::wrapColumn('ordering') . ' '
			. 'FROM ' . @static::$_sqlTable[$sid];

		static::$_queries[] = $sql;

		$result = static::$_connection->query($sql);
		$row = $result->fetch_assoc();

		// Cannot +1 in query (select max(ordering)+1 from ....
		// because if table is empty it will returns <null>+1 = <null>
		// and <null> will be converted to 0 when insert.

		return (int)$row['ordering'] + 1;
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
		return static::$_connection->insert_id;
	}

	public static function getAffectedRows() // ok
	{
		return static::$_connection->affected_rows;
	}

    public static function duplicate($data, $existingId = null)
    {
	    $sid = static::_getQuerySessionId();
        $arr = static::_extractDataToColumnAndValue($data);
        $columns = $arr[0];
        $values = $arr[1];
	    $where = [];

        for ($i = 0, $n = count($columns); $i < $n; ++$i)
            $where[] = static::wrapColumn($columns[$i]) . ' = ' . static::quote($values[$i]);

	    $where = implode(' OR ', $where);

        if (count($columns) > 1)
            $where = '(' . $where . ')';

        if ($existingId)
            $where .= ' AND ' . static::wrapColumn('id') . ' != ' . $existingId;

	    $sql = 'SELECT ' . static::wrapColumn('id') . ' '
	        . 'FROM ' . @static::$_sqlTable[$sid] . ' '
		    . 'WHERE ' . $where;

	    static::$_queries[] = $sql;

	    $result = static::$_connection->query($sql);
	    $row = $result->fetch_assoc();

        if ($row['id'])
            return true;
        else
            return false;
    }

	public static function listTables() // ok
	{
		if (is_array(static::$_tables) == false)
		{
			if (File::isExists(static::$_dbCachePath . '_tables_' . EXT))
			{
				$content = file_get_contents(static::$_dbCachePath . '_tables_' . EXT);
				$content = substr($content, 8);

				static::$_tables = @unserialize($content);
			}

			if (empty(static::$_tables))
			{
				$sql = 'SHOW TABLES';
				$result = static::$_connection->query($sql);

				static::$_queries[] = $sql;

				while ($table = $result->fetch_assoc())
				{
					$table = (array)$table;
					$table = current($table);

					static::$_tables[] = $table;
				}

				$fp = fopen(static::$_dbCachePath . '_tables_' . EXT, 'w');
				fwrite($fp, '<?php //'.serialize(static::$_tables));
				fclose($fp);
			}
		}

		return static::$_tables;
	}

	public static function listColumns($table) // ok
	{
		$table = static::formatTableName($table);

		if (isset(static::$_info[$table]) == false)
		{
			$file = static::$_dbCachePath.$table.EXT;

			if (File::isExists($file))
			{
				$content = file_get_contents($file);
				$content = substr($content, 8);

				static::$_info[$table] = unserialize($content);
			}
			else
			{
				$sql = 'DESCRIBE ' . static::wrapTable($table);
				$result = static::$_connection->query($sql);

				static::$_queries[] = $sql;

				$tableInfo = [];
				$i = 0;

				while ($row = $result->fetch_assoc())
				{
					$column = $row['Field'];
					$columnPosition = ++$i;
					$dataType = $row['Type'];
					$default = $row['Default'];
					$nullable = ($row['Null'] == 'YES');
					$length = null;
					$scale = null;
					$precision = null;
					$unsigned = (strpos($row['Type'], 'unsigned') !== false);
					$primary = ($row['Key'] == 'PRI');
					$primaryPosition = ($primary ? $columnPosition : null);
					$autoIncrement = ($row['Extra'] == 'auto_increment');

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

					$tableInfo[$row['Field']]['TABLE_NAME'] = $table;
					$tableInfo[$row['Field']]['COLUMN_NAME'] = $column;
					$tableInfo[$row['Field']]['COLUMN_POSITION'] = $columnPosition;
					$tableInfo[$row['Field']]['DATA_TYPE'] = $dataType;
					$tableInfo[$row['Field']]['DEFAULT'] = $default;
					$tableInfo[$row['Field']]['NULLABLE'] = $nullable;
					$tableInfo[$row['Field']]['LENGTH'] = $length;
					$tableInfo[$row['Field']]['SCALE'] = $scale;
					$tableInfo[$row['Field']]['PRECISION'] = $precision;
					$tableInfo[$row['Field']]['UNSIGNED'] = $unsigned;
					$tableInfo[$row['Field']]['PRIMARY'] = $primary;
					$tableInfo[$row['Field']]['PRIMARY_POSITION'] = $primaryPosition;
					$tableInfo[$row['Field']]['IDENTITY'] = '';
					$tableInfo[$row['Field']]['AUTO_INCREMENT'] = $autoIncrement;
				}

				static::$_info[$table] = $tableInfo;

				$fp = fopen($file, 'w');
				fwrite($fp, '<?php //'.serialize($tableInfo));
				fclose($fp);
			}
		}

		return static::$_info[$table];
	}

	public static function tableExists($table) // ok
	{
        $table = static::formatTableName($table);
		$tables = static::listTables();

		return Arr::has($tables, $table);
	}

    public static function columnExists($column) // ok
    {
	    $sid = static::_getQuerySessionId();
	    $table = @static::$_sqlTable[$sid];

        $columns = static::listColumns($table);

        return array_key_exists($column, $columns);
    }

	public static function lockTable($table) // ok
	{
		$sql = 'LOCK TABLES ' . $table . ' WRITE, ' . static::wrapTable('Session') . ' WRITE';

		if (static::$_transactionMode)
			static::$_transactionSqls[] = $sql;
		else
		{
			static::$_connection->query($sql);
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
			static::$_queries[] = $sql;
		}
	}

	public static function getPreparedSelect() // ok
	{
		return static::_buildQuerySelect();
	}

	public static function getPreparedInsert($data) // ok
	{
		return static::_buildQueryInsert($data);
	}

	public static function getPreparedUpdate($data) // ok
	{
		return static::_buildQueryUpdate($data);
	}

	public static function getPreparedSave($data) // ok
	{
		return static::_buildQuerySave($data);
	}

	public static function getPreparedDelete() // ok
	{
		$where = static::_buildWhere();
		return static::_buildQueryDelete($where);
	}

	public static function version() // ok
	{
		static::reset();
		return DB::raw('SELECT VERSION()')->loadSingle();
	}

	private static function _displayError() /////////////////////////////////////////
	{
		echo '<style>body { font-family: tahoma, arial; font-size: 12px; }</style>';
		echo '<fieldset><legend>MySQL Error</legend>'.static::$_connection->error.'</fieldset>';
		echo '<fieldset><legend>In Query</legend>'.DB::getLastQuery().'</fieldset>';

		//exit;
	}

	public static function reset()
	{
		$sid = static::_getQuerySessionId();

		static::$_instance = null;

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
	}
}
