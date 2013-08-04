<?php

$form = $this->form['build'];
$fields = $this->form['fields'];
$hidden = array();
$action = array();
$css = explode(" ", $form['css']);
$transform = array('radio', 'checkbox');
$this->assets->set('script', 'js/form.js');
?>
<form id="<?= $form['fid']; ?>" action="<?= $form['action']; ?>" method="<?= $form['method']; ?>" class="<?= $form['css']; ?>">
<fieldset>
	<? if ($form['title']): ?>
	<legend><?= $form['title']; ?></legend>
	<? endif; ?>
	<div class="message"></div>
	<?
	foreach ($fields as $id => $field):
		if ($id == 'hidden' or $id == 'action'):
			$$id = $field['field'];
		else: 
	?>
	<div class="form-group">
		<? if (isset($field['label'])): ?>
		<label class="control-label col-lg-2" for="<?= $id; ?>"><?= $field['label']; ?></label>
		<? endif; ?>
		<div class="controls col-lg-6"<? if (isset($field['verbose'])): ?> data-toggle="popover" data-placement="right" data-trigger="manual" data-content=""<? endif; ?>>
			<? if (is_array($field['field'])): ?>
				<? if (in_array('form-transform', $css) and in_array($field['type'], $transform)): ?>
				<div class="btn-group" data-toggle="buttons">
					<? foreach($field['field'] as $index => $build): ?>
					<label class="btn btn-default"><?= $build['label']; ?><?= $build['field']; ?></label>
					<? endforeach; ?>
				</div>
				<? else: ?>
					<? foreach($field['field'] as $index => $build): ?>
					<label><?= $build['label']; ?>&nbsp;<?= $build['field']; ?></label>
					<? endforeach; ?>
				<? endif; ?>

			<? else: ?>
			<?= $field['field']; ?>
			<? endif; ?>

			<? if (isset($field['help'])): ?>
			<div class="help-block"><?= $field['help']; ?></div>
			<? endif; ?>
		</div>
	</div>
	<? endif; ?>
	<? endforeach; ?>
	<? if (!empty($hidden)): ?>
	<div style="display: none;">
		<?= implode("\n", $hidden); ?>
	</div>
	<? endif; ?>
	<? if (!empty($action)): ?>
	<div class="form-actions">
		<?= implode("", $action); ?>
	</div>
	<? endif; ?>
</fieldset>
</form>
