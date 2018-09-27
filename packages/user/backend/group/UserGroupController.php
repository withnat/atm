<?php
use System\Mvc\Controller;
use System\Mvc\View;
use System\Auth;
use System\DB;
use System\Document;
use System\Flash;
use System\Html;
use System\Request;
use System\Session;

class UserGroupController extends Controller
{
	public $title = 'User Groups';

	public function indexAction()
	{
		$dataTable = UserGroup::dataTable();

		if (Request::get('datatable'))
			die($dataTable);

		Document::setTitle(t($this->title));

		$view = new View();
		$view->setBreadcrumb(t($this->title));
		$view->dataTable = $dataTable;
		$view->display();
	}

	public function formAction()
	{
		$id = Request::get('id');
		$mode = $id ? t('Edit') : t('Add');
		$breadcrumb = [Html::link('user/group', t($this->title)), $mode];

		if ($id)
			$data = UserGroup::loadById($id);

		Document::setTitle(t($this->title));

		$view = new View();
		$view->setBreadcrumb($breadcrumb);
		$view->form = @$data;
		$view->display('form');
	}

	public function checkAvailableAction()
	{
		if (UserGroup::exists(Request::post()))
			echo 'false';
		else
			echo 'true';
	}

	public function saveAction()
	{
		Request::ensureIsPost();
		Session::checkToken();

		$userGroup = new UserGroup();
		$data = Request::post();
		$data->status = Request::boolean('status');

		$evaluate = Request::post('evaluate');
		$report = Request::post('report');
		$user = Request::post('user');
		$master = Request::post('master');
		$config = Request::post('config');

		$data->permission = ','

			. $evaluate . ','
			. $report . ','
			. $user . ','
			. $master . ','
			. $config . ',';

		if ($data->id)
			$userGroup->loadById($data->id);

		$userGroup->bind($data);

		if ($userGroup->save() == false)
		{
			Flash::danger($userGroup->errorInfo());
			Request::set('id', $data->id);
			$this->formAction();
			return;
		}

		Flash::success(t('Item was successfully saved.'));
		$this->redirect('user/group');
	}

	public function activateAction()
	{
		$id = Request::get('id');
		$id = (array)$id;

		$affected = DB::table('UserGroup')->whereIn('id', $id)->update(['status' => 1]);

		echo Flash::success($affected . ' ' . t('item(s) successfully activated.'));
	}

	public function deactivateAction()
	{
		$id = Request::get('id');
		$id = (array)$id;
		$msg = '';

		if (in_array(Auth::identity()->userGroupId, $id))
			$msg .= Flash::danger(t('You can\'t deactivate yourself.'));

		if (UserGroup::hasFixedId($id))
			$msg .= Flash::danger(t('You can\'t deactivate a ' . UserGroup::getFixedName() . '.'));

		$notInId = UserGroup::getFixedId();
		$notInId[] = Auth::identity()->userGroupId;

		DB::table('UserGroup')
			->whereIn('id', $id)
			->whereNotIn('id', $notInId);

		$affected = DB::update(['status' => 0]);
		$msg .= Flash::success($affected . ' ' . t('item(s) successfully deactivated.'));

		echo $msg;
	}

	public function deleteAction()
	{
		$id = Request::get('id');
		$id = (array)$id;
		$msg = '';

		if (in_array(Auth::identity()->userGroupId, $id))
			$msg .= Flash::danger(t('You can\'t delete user groups to which you belong.'));

		if (UserGroup::hasFixedId($id))
			$msg .= Flash::danger(t('You can\'t delete a ' . UserGroup::getFixedName() . '.'));

		$notInId = UserGroup::getFixedId();
		$notInId[] = Auth::identity()->userGroupId;

		DB::table('UserGroup')
			->whereIn('id', $id)
			->whereNotIn('id', $notInId);

		$affected = DB::discontinue();
		$msg .= Flash::success($affected . ' ' . t('item(s) successfully deleted.'));

		echo $msg;
	}
}
