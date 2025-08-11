<input type="hidden" name="id" value="<?= encode($row->form_id) ?>">
<div class="form-group">
    <label for="form_name">Form Name: </label>
    <input type="text" id="form_name" name="form_name" class="form-control form-control-sm" value="<?= $row->form_name ?>" required>
    <div class="invalid-feedback">Form Name is required.</div>
</div>