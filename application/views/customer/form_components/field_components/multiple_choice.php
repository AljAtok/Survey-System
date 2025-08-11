<?php 
    $required = $is_required == 1 ? 'required' : '';
    $css_class_required = $is_required == 1 ? 'required' : '';
?>

<div class="w-100">
    <label for="<?= $form_field_name ?>" class="font-avenir"><?= $form_field_name ?></label>
    <?php if ($form_field_description) : ?>
        <p class="small"><?= $form_field_description ?></p>
    <?php endif; ?>

    <?php foreach ($options as $option) : ?>
        <div class="form-check">
            <input class="form-check-input" type="radio" value="<?= $option->option_name ?>" name="<?= $enc_field_name ?>" <?= $required ?>>
            <label class="form-check-label" for="<?= $option->option_name ?>">
                <?= $option->option_name ?>
            </label>
        </div>
    <?php endforeach; ?>

    <?php if (isset($mco_data->has_others_option)) : ?>
        <div class="form-check align-items-center">
            <input class="form-check-input" type="radio" value="" name="<?= $enc_field_name ?>">
            <label class="form-check-label" for="Others">
                Others
            </label>
            <input class="border-0 border-bottom border-dark rounded-0 shadow-none" name="<?= encode('x') ?>" data-belongs-to="<?= $form_field_name ?>" disabled required>
        </div>
    <?php endif; ?>
</div> 