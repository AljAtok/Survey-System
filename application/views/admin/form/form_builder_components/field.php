<div class="form-group border bg-light rounded p-2 mt-2" data-field-id="<?= $field_data->field_id ?>">

    <div class="field-upper-content">

        <div class="row">
            <div class="col-1">
                <label class="d-none" for="sequence"></label>
                <input type="numerical" class="form-control form-control-sm w-100 border-0 border-bottom text-center"  name="field_sequence" placeholder="" value="<?= $field_data->form_field_sequence?>" required>
            </div>
            <div class="col-6">
                <label class="d-none" for="field_name"></label>
                <input type="text" class="form-control form-control-sm border-0 border-bottom"  name="field_name" placeholder="Field Title" value="<?= $field_data->form_field_name ?>" required>
            </div>
            <div class="col-4">
                <?= $field_component['select'] ?>
            </div>
            <div class="col-1 text-end">
                <button type="button" class="btn btn-sm btn-secondary toggle-field">  
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <?= $field_component['description'] ?>
            </div>
        </div>

    </div>



    <div class="field-lower-content" style="display:none">

        <div class="option-container">
            <?= $options ?>

            <?= $field_component['add_option'] ?>
        </div>

        <div class="special-field-options-container">

                <?php 
                switch($field_data->field_type_id): 
                    case 4: 
                        $checked = !empty($field_data->special_options_data->has_others_option) ? 'checked' : '';
                ?>

                    <hr>
                    <div class="row">
                        <div class="col-12 text-left">
                            <div class="form-check form-check-inline">
                                <input type="checkbox" class="form-check-input"  name="has_others_option" <?= $checked ?>>
                                <label class="form-check-label" for="has_others_option">Has 'Others' option</label>
                            </div>
                        </div>
                    </div>

                <?php break; ?>
                <?php case 6: 
                        $checked = !empty($field_data->special_options_data->has_others_option) ? 'checked' : '';

                        $min_selection = $field_data->special_options_data->min_selection;
                        $max_selection = $field_data->special_options_data->max_selection;
                ?>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <div class="">
                                <label class="d-none" for="left_label"></label>
                                <input type="number" class="form-control form-control-sm"  name="min_selection" placeholder="Minimum Selection" value="<?= $min_selection ?>">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="">
                                <label class="d-none" for="right_label"></label>
                                <input type="number" class="form-control form-control-sm"  name="max_selection" placeholder="Maximum Selection" value="<?= $max_selection ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12 text-left">
                            <div class="form-check form-check-inline">
                                <input type="checkbox" class="form-check-input"  name="has_others_option" <?= $checked ?>>
                                <label class="form-check-label" for="has_others_option">Has 'Others' option</label>
                            </div>
                        </div>
                    </div>
                <?php break; ?>
                <?php case 7: 
                    $min_value = $field_data->special_options_data->min_value;
                    $max_value = $field_data->special_options_data->max_value;    

                    $left_label = $field_data->special_options_data->left_label;
                    $right_label = $field_data->special_options_data->right_label;
                ?>
                    <hr>
                    <div class="row">
                        <div class="col-4">
                            <select class="form-control form-control-sm" name="min_value">
                                <?php 
                                    $default_value = 1;
                                    for ($x = 0; $x <= 1; $x++) {
                                        $selected = (isset($min_value) && $x == $min_value) ? 'selected' : (($x == $default_value) ? 'selected' : '');
                                        echo "<option value=\"$x\" $selected>$x</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-4 text-center">
                            <p>To</p>
                        </div>
                        <div class="col-4">
                            <select class="form-control form-control-sm" name="max_value">
                                <?php 
                                    $default_value = 5;
                                    for ($x = 2; $x <= 10; $x++) {
                                        $selected = (isset($max_value) && $x == $max_value) ? 'selected' : (($x == $default_value) ? 'selected' : '');
                                        echo "<option value=\"$x\" $selected>$x</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="">
                                <label class="d-none" for="left_label"></label>
                                <input type="text" class="form-control form-control-sm"  name="left_label" placeholder="Left Label" value="<?= $left_label ?>">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="">
                                <label class="" for="right_label"></label>
                                <input type="text" class="form-control form-control-sm"  name="right_label" placeholder="Right Label" value="<?= $right_label ?>" >
                            </div>
                        </div>
                    </div>
                <?php break; ?>
                        
                <?php 
                endswitch; 
                ?>

        </div>

        <hr>
        <div class="field-options-container row">
            <div class="col-8">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="is_required" <?= $field_data->is_required == 1 ? 'checked' : ''?>>
                    <label class="form-check-label" for="is_required">Required</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="is_required" <?= $field_data->form_field_description ? 'checked' : ''?>>
                    <label class="form-check-label" for="description">With Description</label>
                </div>
            </div>
            <div class="col-4 text-end">
                <button type="button" class="btn btn-sm btn-danger delete-field">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>

    </div>
</div>