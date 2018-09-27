<?php
defined('VD') or die('Access Denied');

use System\Form;
?>

<?=Form::open('home/proceed')?>
<?=Form::token()?>

	<div class="row">
		<div style="text-align:center; margin-top:100px;">
			<h1>Welcome to <?=Config::get('sitename')?></h1>
			<h2>Current Balance is ฿<?=number_format(ATM::getBalance())?></h2>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-6 col-lg-offset-3">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5><?=t('Withdrawal')?></h5>
				</div>
				<div class="ibox-content">
					<div class="form-group">
						<label class="col-lg-2 control-label required"><?=t('Amount')?></label>
						<div class="col-lg-10">
							<?=Form::text('ATM.amount')?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-2 control-label">&nbsp;</label>
						<div class="col-lg-10">
							<?=Form::buttonSave()?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<?=Form::close()?>
