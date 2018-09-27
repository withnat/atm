<?php
use System\Mvc\Model;
use System\Mvc\View;
use System\Arr;
use System\Cookie;
use System\DB;
use System\Request;
use System\Uri;

class UserGroup extends Model
{
	public static $table = 'UserGroup';

	public static function rules()
	{
		$rules = [
			'name' => 'label:Name|required|unique:user/group/check-available'
		];

		return $rules;
	}

	public static function dataTable()
	{
		if (Request::isPost())
		{
			$data = Request::post();
			$data = Arr::fromObject($data);
		}
		else
		{
			$context = Uri::getContext();
			$data = Cookie::get($context . 'searchform');
			$data = json_decode($data, true);
		}

		list($search) = @array_values($data);

		DB::table('UserGroup')->where('status', '>', -1);

		if ($search)
			DB::whereContain('name', $search);

		$rows = DB::paginate();

		$view = new View();
		$view->setLayout(null);
		$view->rows = $rows;

		return $view->render('datatable');
	}

	public static function hasFixedId($requestDeleteId)
	{
		$fixedId = static::getFixedId();

		foreach ($fixedId as $id)
		{
			if (in_array($id, $requestDeleteId))
				return true;
		}

		return false;
	}

	public static function getFixedId()
	{
		$id = [];
		$userGroups = UserGroup::loadAllByFixPermission(1);

		foreach ($userGroups as $userGroup)
			$id[] = $userGroup->id;

		return $id;
	}

	public static function getFixedName()
	{
		$userGroups = UserGroup::loadAllByFixed(1);
		$n = count($userGroups);
		$name = '';

		for ($i=0; $i < $n; ++$i)
		{
			if ($i < $n - 1)
				$name .= $userGroups[$i]->name . ', ';
			else
				$name .= ' and ' . $userGroups[$i]->name;
		}

		return $name;
	}
}
