<?php
use System\DB;

class ATM extends System\Mvc\Model
{
	public static function rules()
	{
		$rules = [
			'amount' => 'required|number'
		];

		return $rules;
	}

	public static function getBalance()
	{
		$rows = Config::loadAll();
		$balance = 0;

		foreach ($rows as $row)
		{
			if (in_array($row->key, ['bn20', 'bn50', 'bn100', 'bn500', 'bn1000']))
			{
				$bnType = str_replace('bn', '', $row->key);
				$balance += $bnType * $row->value;
			}
		}

		return $balance;
	}

	private static function _getAvailableBanknote()
	{
		$rows = Config::loadAll();
		$banknotes = [];

		foreach ($rows as $row)
		{
			if (in_array($row->key, ['bn20', 'bn50', 'bn100', 'bn500', 'bn1000']) and $row->value > 0)
			{
				$key = str_replace('bn', '', $row->key);
				$banknotes[$key] = $row->value;
			}
		}

		return $banknotes;
	}

	public static function proceed($withdrawAmount)
	{
		$withdrawAmount = abs((int)$withdrawAmount);

		//echo 'ถอน = '.$withdrawAmount.'<br /><br />';

		$balance = static::getBalance();
		
		if ($withdrawAmount > $balance)
			return ['danger', 'Insufficient funds.'];

		$leftAmount = $withdrawAmount;
		$availableBanknote = static::_getAvailableBanknote();
		$paidBanknote = [];

		krsort($availableBanknote);

		//echo 'แบงค์ในตู้';
		//pr($availableBanknote);

		foreach ($availableBanknote as $key => $value)
		{
			$qty = floor($leftAmount / $key);

			if ($qty > 0)
			{
				if ($qty >= $value)
				{
					$leftAmount -= $key * $value;
					$availableBanknote[$key] = 0;
					$paidBanknote[$key] = $value;
				}
				else
				{
					$leftAmount -= $key * $qty;
					$availableBanknote[$key] = ($value - $qty);
					$paidBanknote[$key] = $qty;
				}
			}
		}

		//echo 'แบงค์ในตู้ที่เหลือหลังการคำนวณ';
		//pr($availableBanknote);

		//echo 'แบงค์ที่จ่ายได้';
		//pr($paidBanknote);

		//echo 'เหลือเงินที่จ่ายไม่ได้ = '.$leftAmount;

		if ($leftAmount)
		{
			$availableBanknote = static::_getAvailableBanknote();
			$availableBanknote = implode(', ', array_keys($availableBanknote));

			$msg = ['danger', 'This amount cannot be paid. Available Banknote are ' . $availableBanknote];
		}
		else
		{
			foreach ($availableBanknote as $key => $value)
			{
				$key = 'bn' . $key;
				$data = ['value' => $value];

				DB::table('Config')->where('key', $key)->update($data);
			}

			$msg = 'You get ฿' . number_format($withdrawAmount) . '<br /><br />';
			$msg .= 'ATM dispensed...<br />';
			$msg .= '<ul>';

			foreach ($paidBanknote as $key => $value)
				$msg .= '<li>฿' . $key . ' : ' . number_format($value) . '</li>';

			$msg .= '</ul>';
			$msg .= '<br />Current Balance is ฿' . number_format(static::getBalance());

			$msg = ['success', $msg];
		}

		return $msg;
	}
}
