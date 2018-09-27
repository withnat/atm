<?php
class Router
{
	function build($com, $task='index')
	{
		$link = Request::getBaseUrl().DS.$com.DS.$task;
		return $link;
	}
}
