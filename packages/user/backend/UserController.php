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
use System\Str;

class UserController extends Controller
{
	public $title = 'User List';

	public function indexAction()
	{
		$dataTable = User::dataTable();

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

		$title = t($this->title) . ': ' . $mode;
		$breadcrumb = [Html::link('user', t($this->title)), $mode];
		$showBlockControl = ($id != Auth::identity()->id);

		if ($id)
		{
			$data = User::loadById($id);
			//$data->status = ($data->status == 0);
		}

		Document::setTitle(t($this->title));

		$view = new View();
		$view->setBreadcrumb($breadcrumb);
		$view->form = @$data;
		$view->showBlockControl = $showBlockControl;
		$view->display('form');
	}

	public function profileAction()
	{
		$id = Auth::identity()->id;
		$title = t('My Profile');
		$breadcrumb = $title;

		Document::setTitle(t($this->title));
		$view = new View();

		$view->setBreadcrumb($breadcrumb);
		$view->form = User::loadById($id);
		$view->display();
	}

	public function checkAvailableAction()
	{
		if (User::exists(Request::post()))
			echo 'false';
		else
			echo 'true';
	}

	public function saveAction()
	{
		Request::ensureIsPost();
		Session::checkToken();

		$user = new User();
		$data = Request::post();
		//$data->status = (Request::boolean('status') == 1) ? 0 : 1;

		if ($data->id == 1)
			$data->status = 1;
		else
			$data->status = Request::boolean('status');

		if ($data->id)
			$user->loadById($data->id);

		if ($user->id and !$data->password)
			unset($data->password);
		elseif ($data->password)
		{
			// Hash value will change everytime.
			if ($data->password == $data->passwordConfirm)
			{
				$data->password = Str::hash($data->password);
				$data->passwordConfirm = $data->password;
			}
			else // Not match data will be detected by model validator.
				$data->password = Str::hash($data->password);

		}
		else
		{
			$randomString = Str::random(32);
			$data->password = Str::hash($randomString);
			$data->passwordConfirm = $data->password;
		}

		$user->bind($data);

		if ($user->save() == false)
		{
			Flash::danger($user->errorInfo());
			Request::set('id', $data->id);
			$this->formAction();
			return;
		}

		if (Auth::identity()->id == $user->id)
			Auth::loadProfile2Session($user);

		Flash::success(t('Item was successfully saved.'));
		$this->redirect('user');
	}

	public function saveprofileAction()
	{
		Request::ensureIsPost();
		Session::checkToken();

		$data = Request::post();

		if ($data->password)
		{
			// Hash value will change everytime.
			if ($data->password == $data->passwordConfirm)
			{
				$data->password = Str::hash($data->password);
				$data->passwordConfirm = $data->password;
			}
			else // Not match data will be detected by model validator.
				$data->password = Str::hash($data->password);
		}
		else
			unset($data->password);

		$user = User::loadById(Auth::identity()->id);
		$user->bind($data);

		if ($user->save() == false)
		{
			Flash::danger($user->errorInfo());
			Request::set('id', $data->id);
			$this->profileAction();
			return;
		}

		Auth::loadProfile2Session($user);

		Flash::success(t('Item was successfully saved.'));
		$this->redirect('user/profile');
	}

	public function activateAction()
	{
		$id = Request::get('id');
		$id = (array)$id;

		$affected = DB::table('User')->whereIn('id', $id)->update(['status' => 1]);

		echo Flash::success($affected . ' ' . t('item(s) successfully activated.'));
	}

	public function deactivateAction()
	{
		$id = Request::get('id');
		$id = (array)$id;
		$msg = '';

		if (in_array(Auth::identity()->id, $id))
			$msg .= Flash::danger(t('You can\'t block yourself.'));
		elseif (in_array(1, $id))
			$msg .= Flash::danger(t('You can\'t block a Super User.'));

		DB::table('Session')
			->whereIn('userId', $id)
			->whereNotIn('userId', [1, Auth::identity()->id])
			->delete();

		DB::table('User')
			->whereIn('id', $id)
			->whereNotIn('id', [1, Auth::identity()->id]);

		$affected = DB::update(['status' => 0]);
		$msg .= Flash::success($affected . ' ' . t('item(s) successfully deactivated.'));

		echo $msg;
	}

	public function deleteAction()
	{
		$id = Request::get('id');
		$id = (array)$id;
		$msg = '';

		if (in_array(Auth::identity()->id, $id))
			$msg .= Flash::danger(t('You can\'t delete yourself.'));
		elseif (in_array(1, $id))
			$msg .= Flash::danger(t('You can\'t delete a Super User.'));

		DB::table('Session')
			->whereIn('userId', $id)
			->whereNotIn('userId', [1, Auth::identity()->id])
			->delete();

		DB::table('User')
			->whereIn('id', $id)
			->whereNotIn('id', [1, Auth::identity()->id]);

		$affected = DB::discontinue();
		$msg .= Flash::success($affected . ' ' . t('item(s) successfully deleted.'));

		echo $msg;
	}

	public function loginAction()
	{
		if (Auth::loggedin())
			$this->redirect('home');

		$redirect = '';

		if (Request::isPost())
		{
			Session::checkToken();
			$data = Request::post();

			if ($data->username or $data->password)
			{
				if (Auth::login($data->username, $data->password))
				{
					if ($data->redirect)
						$this->redirect(Str::decode($data->redirect));
					else
						$this->redirect('home');
				}
				else
					Flash::danger(t('Username and password do not match.'));
			}
		}
		else
			$redirect = Request::get('redirect');

		$view = new View();

		$view->setLayout('login');
		$view->redirect = $redirect;
		$view->display();
	}

	public function logoutAction()
	{
		Auth::logout();
		$this->redirect(Request::homeUrl());
	}
}
