<?php
use System\Str;
use System\Language;

function t($string)
{
	/*
	if ($str == 'Subcontractor')
		$str = 'Subcon';
	
	return $str;
	*/

	//$lang = JFactory::getLanguage();
	//return $lang->_($string);

	return Language::_($string, System\Auth::identity()->languageId);
}

/**
 * Method to print human-readable information about a variable for debugging purposes
 *
 * @param	mixed	A variable
 * @return	string
 */
function pr($data)
{
	$data = Str::safe($data, true);

	echo "<pre>\n" . print_r($data, true) . '</pre>';
}

/**
 * Method to print human-readable information about a variable for debugging purposes
 * and terminate the current script
 *
 * @param	mixed	A variable
 * @return	string
 */
function prx($input)
{
	pr($input);
	exit;
}

function c($array)
{
	echo count($array);
}

function cx($array)
{
	c($array);
	exit;
}

function e($input)
{
	echo $input;
}

function ex($input)
{
	e($input);
	exit;
}

function swap(&$var1, &$var2)
{
	list($var1, $var2) = [$var2, $var1];
}

function setValueIfEmpty(&$var1, &$var2, $defaultVal1 = null, $defaultVal2 = null)
{
	if ($var1 == '' and $var2 == '')
	{
		$var1 = $defaultVal1;
		$var2 = $defaultVal2;
	}
	elseif ($var1)
		$var2 = $var1;
	elseif ($var2)
		$var1 = $var2;
}

function spa()
{
	if ((SIDE == 'frontend' and FRONTEND_SPA_MODE) or (SIDE == 'backend' and BACKEND_SPA_MODE))
		return true;
	else
		return false;
}