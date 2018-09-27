<?php
use System\Mvc\Model;
use System\Mvc\View;
use System\Arr;
use System\Auth;
use System\Cookie;
use System\DB;
use System\Request;
use System\Uri;

class User extends Model
{
	public static $table = 'User';

	public static function rules()
	{
		$rules = [
			'userGroupId' => 'required',
			'departmentId' => 'requiredIf:userGroupId=3:This field is required.',
			//'subcontractorId' => 'requiredIfEmpty:',
			//'subcontractorId' => 'requiredIfNotEmpty:',
			'name' => 'required',
			'username' => 'label:User Name|required|unique:user/check-available',
			'password' => 'minlength:4',
			'passwordConfirm' => 'label:Confirm Password|equalTo:password',
			'email' => 'label:Email|required|email|unique:user/check-available',
			'languageId' => 'required',
			//'imageAvatarFile' => 'extension:xls,csv'
			//require_from_group
			'choices' => 'options:((a,A), (b,B))|default:a'
		];

		if (empty(Auth::identity()->id))
			$rules['password'] = 'required|minlength:4';

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

		list($search, $userGroupId) = @array_values($data);

		DB::select('u.id, u.userGroupId, u.name, u.username, d.name AS department, u.status, u.visited')
			->from('User AS u')
			->leftJoin('Department AS d', 'u.departmentId=d.id')
			->where('u.status', '>', -1);

		if ($search)
		{
			DB::where(function () use ($search)
			{
				DB::whereContain('u.name', $search)
					->orWhereContain('u.username', $search)
					->orWhereContain('d.name', $search);
			});
		}

		if ($userGroupId)
		{
			//DB::whereContain('userGroupIds', ',' . $userGroupId . ',');
			DB::where('u.userGroupId', $userGroupId);
		}

		$detect = new Mobile_Detect();

		if ($detect->isMobile())
			DB::sort('u.name');

		$rows = DB::paginate();
		$userGroups = DB::table('UserGroup')->loadAll();
		$groups = [];

		foreach ($userGroups as $userGroup)
			$groups[$userGroup->id] = $userGroup->name;

		$n = count($rows);

		for ($i = 0; $i < $n; ++$i)
		{
			/*
			$rows[$i]->usergroup = '';
			$userGroupIds = explode(',', $rows[$i]->userGroupIds);

			foreach ($userGroupIds as $userGroupId)
			{
				if (array_key_exists($userGroupId, $groups))
					$rows[$i]->usergroup .= $groups[$userGroupId] . '<br />';
			}
			*/

			//$rows[$i]->usergroup = $groups[$rows[$i]->userGroupId];
		}

		foreach ($rows as $row)
		{
			$row->usergroup = @$groups[$row->userGroupId];

			if (System\DateTime::valid($row->visited) == false)
				$row->visited = 'Never';
		}

		$view = new View();
		$view->setLayout(null);
		$view->rows = $rows;

		return $view->render('datatable');
	}

	public static function hasPermission($section)
	{
		$permission = UserGroup::loadById(Auth::identity()->userGroupId)->permission;

		if ((strpos($permission, ',' . $section . ':r,') === false and
			strpos($permission, ',' . $section . ':w,') === false and
			strpos($permission, ',' . $section . ':d,') === false) or
			strpos($permission, ',' . $section . ':n,') !== false)
			return false;
		else
			return true;
	}

	public static function hasWritePermission($section)
	{
		$permission = UserGroup::loadById(Auth::identity()->userGroupId)->permission;

		if (strpos($permission, ',' . $section . ':w,') !== false or
			strpos($permission, ',' . $section . ':d,') !== false)
			return true;
		else
			return false;
	}

	public static function hasDeletePermission($section)
	{
		$permission = UserGroup::loadById(Auth::identity()->userGroupId)->permission;

		if (strpos($permission, ',' . $section . ':d,') !== false)
			return true;
		else
			return false;
	}
}
