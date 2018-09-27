<?php
use System\Request;

class HomeController extends System\Mvc\Controller
{
	public function indexAction()
	{
		$this->redirect(BACKENPATH);
	}
}
