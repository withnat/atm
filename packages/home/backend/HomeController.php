<?php

use System\Mvc\Controller;
use System\Mvc\View;
use System\Auth;
use System\DB;
use System\Document;
use System\Flash;
use System\Request;
use System\Session;

class HomeController extends Controller
{
	public $title = 'ATM Dashboard';

	public function preInit()
	{
		Auth::setPassthru('indexAction, proceedAction');
	}

	public function indexAction()
	{
		Document::setTitle(t($this->title));

		$view = new View();
		$view->display();
	}

	public function proceedAction()
	{
		Request::ensureIsPost();
		Session::checkToken();

		$data = Request::post();
		$result = ATM::proceed($data->amount);

		if ($result[0] == 'danger')
			Flash::danger($result[1]);
		else
			Flash::success($result[1]);

		$this->redirect('home');
	}
}
