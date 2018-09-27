<?php
defined('VD') or die('Access Denied');

use System\Html;
use System\Paginator;
use System\Uri;
?>

<div class="table-responsive">
	<div class="paginator-options">
		<?=Paginator::options()?>
	</div>
	<div class="paginator-detail">
		<?=Paginator::detail()?>
	</div>
	<div class="hidden-xs hidden-sm">
		<table class="table table-striped table-bordered table-hover" data-url="<?=Uri::route('user?datatable=1')?>">
			<thead>
				<tr>
					<?php if (User::hasWritePermission('user')): ?>
						<th class="text-center"><?=Html::toggleid()?></th>
					<?php endif; ?>
					<th class="searchable"><?=Paginator::sort(t('Name'), 'name')?></th>
					<th class="searchable"><?=Paginator::sort(t('Username'), 'username')?></th>
					<th class="exact-searchable"><?=t('User Group')?></th>
					<th class="exact-searchable"><?=t('Department')?></th>
					<th class="text-center"><?=Paginator::sort(t('Active'), 'status')?></th>
					<th><?=Paginator::sort(t('Last Visit Date'), 'visited')?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->rows as $row): ?>
					<tr>
						<?php if (User::hasWritePermission('user')): ?>
							<td class="text-center"><?=Html::checkid($row->id)?></td>
						<?php endif; ?>
						<td><?=Html::link('user/form?id=' . $row->id, $row->name)?></td>
						<td><?=$row->username?></td>
						<td><?=$row->usergroup?></td>
						<td><?=$row->department?></td>
						<td class="text-center"><?=Html::active($row->status)?></td>
						<td><?=$row->visited?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div class="visible-xs visible-sm">
		<table class="table table-striped table-bordered table-hover">
			<tbody>
				<?php foreach ($this->rows as $row): ?>
					<tr>
						<?php if (User::hasWritePermission('user')): ?>
							<td class="text-center"><?=Html::checkid($row->id)?></td>
						<?php endif; ?>
						<td>
							<label><?=t('Name')?> :</label> <?=Html::link('user/form?id=' . $row->id, $row->name)?><br />
							<label><?=t('User Name')?> :</label> <?=$row->username?><br />
							<label><?=t('User Group')?> :</label> <?=$row->usergroup?><br />
							<label><?=t('Department')?> :</label> <?=($row->department ? $row->department : '-')?><br />
							<label><?=t('Active')?> :</label> <?=Html::active($row->status)?><br />
							<label><?=t('Last Visit Date')?> :</label> <?=$row->visited?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div class="paginator-link">
		<?=Paginator::link()?>
	</div>
</div>
