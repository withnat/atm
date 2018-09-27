<?php
use System\Mvc\Controller;
use System\Mvc\View;
use System\Auth;
use System\DB;
use System\Document;
use System\Flash;
use System\Request;
use System\Session;
use System\Uri;

class ConfigController extends Controller
{
	public $title = 'Settings';

	public function preInit()
	{
		Auth::setPassthru('indexAction, saveAction');
	}

	public function indexAction()
	{
		$pagesizeOptions = [5, 10, 15, 20, 25, 30, 50, 100, 500, 1000];

		$rows = Config::loadAll();
		$balance = ATM::getBalance();
		$data = new stdClass();

		foreach ($rows as $row)
			$data->{$row->key} = $row->value;

		Document::setTitle(t($this->title));

		$view = new View();
		$view->setBreadcrumb(t('Settings'));
		$view->pagesizeOptions = $pagesizeOptions;
		$view->balance = $balance;
		$view->form = $data;
		$view->display();
	}

	public function saveAction()
	{
		Request::ensureIsPost();
		Session::checkToken();

		$data = Request::post();
		$data = System\Arr::fromObject($data);
		$data = array_map('trim', $data);

		foreach ($data as $key => $value)
		{
			if ($key == 'backendpath')
				$value = Uri::slugify($value);

			if (in_array($key, ['bn20', 'bn50', 'bn100', 'bn500', 'bn1000']))
				$value = abs((int)$value);

			$data = ['value' => $value];

			DB::table('Config')->where('key', $key)->update($data);
		}

		Flash::success(t('Item was successfully saved.'));
		$this->redirect('config');
	}
}
