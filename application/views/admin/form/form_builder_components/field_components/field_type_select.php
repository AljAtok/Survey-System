<select class="form-control form-control-sm border-0 border-bottom" name="field_type" required>
    <option selected hidden disabled>Select Field Type</option>
    <?php foreach($field_type_data as $field_type ): ?>

    <?php $selected = $selected_field_type_id == $field_type->field_type_id ? 'selected' : ''?>

        <option value="<?= $field_type->field_type_id ?>" <?= $selected ?>><?= $field_type->field_type ?></option>
        
    <?php endforeach; ?>
</select>