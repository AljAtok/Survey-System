<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
<input type="hidden" name="id" value="<?= encode($employee->employee_id) ?>">
<div class="form-group">
    <label for="password">Temporary Password: </label>
    <input type="text" id="password" name="password" class="form-control form-control-sm" value="<?= generate_random(7) ?>" required>
    <div class="invalid-feedback">Temporary Password is required.</div>
</div>