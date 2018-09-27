<?php
use System\Mvc\Helper;
use System\Form;

class UserGroupHelper extends Helper
{
	public static function permissionBox($key, $data, $option='rwdn')
	{
		$readChecked = (@strpos($data->permission, ','.$key.':r,') !== false or empty($data->id)) ? $key . ':r' : '';
		$writeChecked = (@strpos($data->permission, ','.$key.':w,') !== false or (empty($data->id) and strpos($option, 'r') === false)) ? $key . ':w' : '';
		$deleteChecked = @strpos($data->permission, ','.$key.':d,') !== false ? $key . ':d' : '';
		$noneChecked = @strpos($data->permission, ','.$key.':n,') !== false ? $key . ':n' : '';

		$html = '<table border="0" cellpadding="0" cellspacing="0"><tr><td style="width:110px;">';

		if (strpos($option, 'r') !== false)
			$html .= Form::radio($key, [[$key.':r'=>'Read']], $readChecked);

		$html .= '</td><td style="width:110px;">';

		if (strpos($option, 'w') !== false)
			$html .= Form::radio($key, [[$key . ':w' => 'Write']], $writeChecked);

		$html .= '</td><td style="width:170px;">';

		if (strpos($option, 'd') !== false)
			$html .= Form::radio($key, [[$key . ':d' => 'Write & Delete']], $deleteChecked);

		$html .= '</td><td style="width:110px;">';

		if (strpos($option, 'n') !== false)
			$html .= Form::radio($key, [[$key . ':n' => 'None']], $noneChecked);

		$html .= '</td></tr></table>';

		return $html;
	}
}
