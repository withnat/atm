<?php
defined('VD') or die('Access Denied');

use System\DateTime;
use System\Form;
use System\Request;
?>

<?=Form::open()?>
<?=Form::token()?>

<div class="row">
	<div class="col-lg-12">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<?=Form::buttonClose('<i class="fa fa-times"></i> ' . t('Close'), 'onclick="window.location.href=\'' . Request::homeUrl() . '\';"')?>
				<?=Form::buttonSave()?>
			</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5><?=t('System Setting Information')?></h5>
			</div>
			<div class="ibox-content">
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('Site Name')?></label>
					<div class="col-lg-10">
						<?=Form::text('Config.sitename')?>
					</div>
				</div>
				<!--
				<div class="hr-line-dashed"></div>
				<div class="form-group">
					<label class="col-lg-2 control-label"><?=t('Site Offline')?></label>
					<div class="col-lg-10">
						<?=Form::boolean('Config.offline')?>
					</div>
				</div>
				-->
				<div class="hr-line-dashed"></div>
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t(' Default List Limit')?></label>
					<div class="col-lg-10">
						<?=Form::select('Config.pagesize', $this->pagesizeOptions)?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>
				<div class="form-group">
					<label class="col-lg-2 control-label"><?=t('Search Engine Friendly URLs')?></label>
					<div class="col-lg-10">
						<?=Form::boolean('Config.sef')?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('URL Base Path')?></label>
					<div class="col-lg-10">
						<?=Form::text('Config.backendpath')?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('Template')?></label>
					<div class="col-lg-10">
						<?=Form::text('Config.backendtemplate')?>
					</div>
				</div>
				<!--
				<div class="hr-line-dashed"></div>
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('Server Time Zone')?></label>
					<div class="col-lg-10">
						<?=DateTime::timeZoneRegionMenu('Config.timezone', null, 'Region')?>
					</div>
				</div>
				-->
				<div class="hr-line-dashed"></div>
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('Session Life Time')?></label>
					<div class="col-lg-10">
						<div style="float:left;"><?=Form::text('Config.lifetime')?></div>
						<div style="float:left; margin-top:8px; padding-left:5px;">minutes</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5><?=t('Banknote Amount in Machine')?></h5>
			</div>
			<div class="ibox-content">
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('฿20')?></label>
					<div class="col-lg-10">
						<?=Form::text('Config.bn20')?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('฿50')?></label>
					<div class="col-lg-10">
						<?=Form::text('Config.bn50')?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('฿100')?></label>
					<div class="col-lg-10">
						<?=Form::text('Config.bn100')?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('฿500')?></label>
					<div class="col-lg-10">
						<?=Form::text('Config.bn500')?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('฿1000')?></label>
					<div class="col-lg-10">
						<?=Form::text('Config.bn1000')?>
					</div>
				</div>
			</div>
		</div>
		<center><h1>Current Balance is ฿<?=number_format($this->balance)?></h1></center>
	</div>
</div>

<?=Form::close()?>
<?php //=FormHelper::hasWritePermissionJs('config')?>
