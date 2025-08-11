
<div class="container">
    <form method="POST" class="form needs-validation" novalidate>
        <input type="hidden" name="employee_id" value="<?= encode($employee->employee_id) ?>">
        <div class="form-group">
            <label>Unit:</label>
            <select name="unit" class="form-control form-control-sm select2" data-placeholder="Select Unit" required>
                <option></option>
                <?php foreach($units as $row):?>
                <?php $is_selected = ($row->unit_id == $employee->unit_id) ? 'selected' : '' ?>
                <option value="<?=encode($row->unit_id)?>" <?= $is_selected ?> ><?=$row->unit_name?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group">
            <label>Location:</label>
            <select name="location" class="form-control form-control-sm select2" data-placeholder="Select Location" required>
                <option></option>
                <?php foreach($locations as $row):?>
                    <?php $is_selected = ($row->location_id == $employee->location_id) ? 'selected' : '' ?>
                    <option value="<?=encode($row->location_id)?>" <?= $is_selected ?> ><?=$row->location_name?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="form-group">
            <label for="fname">First Name: </label>
            <input type="text" id="fname" name="fname" class="form-control form-control-sm" value="<?= $employee->employee_fname ?>" required>
            <div class="invalid-feedback">First Name is required.</div>
        </div>
        <div class="form-group">
            <label for="lname">Last Name: </label>
            <input type="text" id="lname" name="lname" class="form-control form-control-sm" value="<?= $employee->employee_lname ?>" required>
            <div class="invalid-feedback">Last Name is required.</div>
        </div>
        <div class="form-group">
            <label for="mname">Middle Name / Initial: </label>
            <input type="text" id="mname" name="mname" class="form-control form-control-sm" value="<?=$employee->employee_mname?>">
        </div>
        <div class="form-group">
            <label for="employee_no">Employee No: </label>
            <input type="text" id="employee_no" name="employee_no" class="form-control form-control-sm" value="<?= $employee->employee_no ?>" required>
            <div class="invalid-feedback">Employee No is required.</div>
        </div>
        <div class="form-group">
            <label for="contact">Employee Contact: </label>
            <input type="number" id="contact" name="contact" class="form-control form-control-sm validate-contact" value="<?= $employee->employee_contact ?>">
            <div class="invalid-feedback">Employee Contact is required.</div>
        </div>
        <div class="form-group">
            <label for="email">Employee Email: </label>
            <input type="email" id="email" name="email" class="form-control form-control-sm" value="<?= $employee->employee_email ?>">
            <div class="invalid-feedback">Employee Email is required.</div>
        </div>
        <div class="form-group">
            <label>User Type:</label>
            <select name="employee_type" class="form-control form-control-sm select2" date-placeholder="Select User Type" required>
                <option></option>
                <?php foreach($employee_types as $row):?>
                <?php $is_selected = ($row->employee_type_id == $employee->employee_type_id) ? 'selected' : '' ?>
                <option value="<?=encode($row->employee_type_id)?>" <?=$is_selected?> ><?=$row->employee_type_name?></option>
                <?php endforeach;?>
            </select>
            <div class="invalid-feedback">User Type is required.</div>
        </div>
    </form>
</div>