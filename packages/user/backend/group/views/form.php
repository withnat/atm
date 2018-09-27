<?php
defined('VD') or die('Access Denied');

use System\Form;
?>

<?=Form::open()?>
<?=Form::token()?>
<?=Form::hidden('id')?>

<div class="row">
	<div class="col-lg-12">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<?=Form::buttonClose()?>
				<?=Form::buttonSave()?>
				<div class="ibox-tools">
					<?=Form::buttonDelete()?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5><?=t('User Group Information')?></h5>
			</div>
			<div class="ibox-content">
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('Name')?></label>
					<div class="col-lg-10">
						<?=Form::text('UserGroup.name')?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>
				<div class="form-group">
					<label class="col-lg-2 control-label"><?=t('Active')?></label>
					<div class="col-lg-10">
						<?=Form::boolean('UserGroup.status')?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-12">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5><?=t('Permission')?></h5>
			</div>
			<div class="ibox-content">

				<div class="form-group">
					<label class="col-lg-2 control-label"><?=t('Evaluate')?></label>
					<div class="col-lg-10 control-label">
						<?php echo UserGroupHelper::permissionBox('evaluate', @$this->form); ?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>

				<div class="form-group">
					<label class="col-lg-2 control-label"><?=t('Report')?></label>
					<div class="col-lg-10 control-label">
						<?php echo UserGroupHelper::permissionBox('report', @$this->form, 'rn'); ?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>

				<div class="form-group">
					<label class="col-lg-2 control-label"><?=t('Users')?></label>
					<div class="col-lg-10 control-label">
						<?php echo UserGroupHelper::permissionBox('user', @$this->form); ?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>

				<div class="form-group">
					<label class="col-lg-2 control-label"><?=t('Master Data')?></label>
					<div class="col-lg-10 control-label">
						<?php echo UserGroupHelper::permissionBox('master', @$this->form); ?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>

				<div class="form-group">
					<label class="col-lg-2 control-label"><?=t('Settings')?></label>
					<div class="col-lg-10 control-label">
						<?php echo UserGroupHelper::permissionBox('config', @$this->form, 'rwn'); ?>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>

<?=Form::close()?>

<?php if (in_array(@$this->form->id, UserGroup::getFixedId())): ?>
	<?=FormHelper::readonlyJs()?>
<?php else: ?>
	<?=FormHelper::hasWritePermissionJs('user')?>
<?php endif; ?>

<style type="text/css">
	._section {
		text-align: left;
		margin-bottom: 10px;
		text-decoration: underline;
	}

	.control-label span {
		font-weight: normal;
	}
</style>
