<?php 
    $required = $is_required == 1 ? 'required' : '';
    $css_class_required = $is_required == 1 ? 'required' : '';
?>

<div class="w-100">
    <label for="<?= $form_field_name ?>" class="font-avenir"><?= $form_field_name ?></label>
    <?php if ($form_field_description) : ?>
        <p class="small"><?= $form_field_description ?></p>
    <?php endif; ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <p class="m-0"><?= $lso_data->left_label ?></p>
            </div>
            <div class="col-8">
                <input type="range" class="w-100 custom-range" min="<?= $lso_data->min_value ?>" max="<?= $lso_data->max_value ?>" name="<?= $enc_field_name ?>" placeholder="" <?= $required ?>>
            </div>
            <div class="col-2">
                <p class="m-0"><?= $lso_data->right_label ?></p>
            </div>
        </div>
    </div>
    
</div> 

