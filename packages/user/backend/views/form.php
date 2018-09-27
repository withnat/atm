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
				<?php if (@$this->form->id != 1): ?>
					<div class="ibox-tools">
						<?=Form::buttonDelete()?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5><?=t('User Information')?></h5>
			</div>
			<div class="ibox-content">
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('User Group')?></label>
					<div class="col-lg-10">
						<?=Form::select('User.userGroupId')?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>
				<div class="departmentIdPanel" style="display:none;">
					<div class="form-group">
						<label class="col-lg-2 control-label required"><?=t('Department')?></label>
						<div class="col-lg-10">
							<?=Form::select('User.departmentId')?>
						</div>
					</div>
					<div class="hr-line-dashed"></div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('Name')?></label>
					<div class="col-lg-10">
						<?=Form::text('User.name')?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('Username')?></label>
					<div class="col-lg-10">
						<?=Form::text('User.username')?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>
				<div class="form-group">
					<label class="col-lg-2 control-label"><?=t('Password')?></label>
					<div class="col-lg-10">
						<?=Form::password('User.password')?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>
				<div class="form-group">
					<label class="col-lg-2 control-label"><?=t('Confirm Password')?></label>
					<div class="col-lg-10">
						<?=Form::password('User.passwordConfirm')?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('Email')?></label>
					<div class="col-lg-10">
						<?=Form::email('User.email')?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>
				<div class="form-group">
					<label class="col-lg-2 control-label required"><?=t('Language')?></label>
					<div class="col-lg-10">
						<?=Form::select('User.languageId')?>
					</div>
				</div>
				<div class="hr-line-dashed"></div>

				<?php if ($this->showBlockControl): ?>
					<div class="form-group">
						<label class="col-lg-2 control-label"><?=t('Active')?></label>
						<div class="col-lg-10">
							<?=Form::boolean('User.status')?>
						</div>
					</div>
					<div class="hr-line-dashed"></div>
				<?php endif; ?>

				<div class="form-group">
					<label class="col-lg-2 control-label"><?=t('Avatar')?></label>
					<div class="col-lg-10 control-label">
						<?=Form::hidden('imageAvatar')?>
						<input type="file" id="imageAvatarFile" class="file" />
						<div id="imageAvatarPreview" class="preview text-left">
							<div id="deleteImageAvatarFile" class="delete fa fa-2x fa-times"></div>
							<?php if (@$this->form->imageAvatar): ?>
								<img src="<?=$this->form->imageAvatar?>" />
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?=Form::close()?>
<?=FormHelper::hasWritePermissionJs('user')?>

<script type="text/javascript">
	$(document).ready(function(){
		<?php if (@$this->form->id == 1): ?>
			$('#userGroupId').prop('disabled', true);
		<?php endif; ?>

		$('#userGroupId').on('change', function(){
			toggleSubcontractor();
		});

		toggleSubcontractor();
	});

	function toggleSubcontractor()
	{
		var userGroupId = $('#userGroupId').val();

		if (userGroupId == 3)
			$('.departmentIdPanel').show();
		else
		{
			$('.departmentIdPanel').hide();
			$('#departmentId').val('');
		}
	}
</script>
