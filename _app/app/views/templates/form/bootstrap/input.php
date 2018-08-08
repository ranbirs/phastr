<?php if ($field['label']['value']) : ?>
<label <?= \sys\utils\Html::attr($field['label']['attr']); ?>>
<?php endif; ?>
<?= $field['label']['value']; ?>
<?php if (isset($field['prepend'])) : ?>
    <?= $field['prepend']; ?>
<?php endif; ?>
    <input data-toggle="tooltip" data-placement="top"
	<?= \sys\utils\Html::attr($field['attr']); ?>>
<?php if (isset($field['append'])) : ?>
    <?= $field['append']; ?>
<?php endif; ?>
<?php if ($field['label']['value']) : ?>
    </label>
<?php endif; ?>