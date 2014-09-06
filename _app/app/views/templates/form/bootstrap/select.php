<?php if ($field['label']['value']) : ?>
    <label<?= \sys\utils\Html::attr($field['label']['attr']); ?>>
<?php endif; ?>
<?= $field['label']['value']; ?>
<?php if (isset($field['prepend'])) : ?>
    <?= $field['prepend']; ?>
<?php endif; ?>
    <select data-toggle="tooltip" data-placement="top"<?= \sys\utils\Html::attr($field['attr']); ?>>
        <?php foreach ($field['options'] as $option) : ?>
            <option<?= \sys\utils\Html::attr($option['attr']); ?>><?= $option['label']; ?></option>
        <?php endforeach; ?>
    </select>
<?php if (isset($field['append'])) : ?>
    <?= $field['append']; ?>
<?php endif; ?>
<?php if ($field['label']['value']) : ?>
    </label>
<?php endif; ?>