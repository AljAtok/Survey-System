<div class="container">
    <form method="POST" class="form needs-validation" novalidate>
        <div class="d-flex flex-column gap-2">
            <div class="position-relative">
                <label for="unit">Unit:</label>
                <select name="unit" class="form-control form-control-sm select2" data-placeholder="Select Unit" required>
                    <option></option>
                    <?php foreach($units as $row):?>
                    <option value="<?=encode($row->unit_id)?>"><?=$row->unit_name?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="position-relative">
                <label for="location">Location:</label>
                <select name="location" class="form-control form-control-sm select2" data-placeholder="Select Location" required>
                    <option></option>
                    <?php foreach($locations as $row):?>
                    <option value="<?=encode($row->location_id)?>"><?=$row->location_name?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="position-relative">
                <label for="fname">First Name: </label>
                <input type="text" id="fname" name="fname" class="form-control form-control-sm" value="" required>
            </div>
            <div class="position-relative">
                <label for="lname">Last Name: </label>
                <input type="text" id="lname" name="lname" class="form-control form-control-sm" value="" required>
            </div>
            <div class="position-relative">
                <label for="mname">Middle Name / Initial: </label>
                <input type="text" id="mname" name="mname" class="form-control form-control-sm" value="">
            </div>
            <div class="position-relative">
                <label for="employee_no">Employee No: </label>
                <input type="text" id="employee_no" name="employee_no" class="form-control form-control-sm" value="" required>
            </div>
            <div class="position-relative">
                <label for="contact">Employee Contact: </label>
                <input type="contact" id="contact" name="contact" class="form-control form-control-sm validate-contact" value="" maxlength="11" required>
            </div>
            <div class="position-relative">
                <label for="email">Employee Email: </label>
                <input type="email" id="email" name="email" class="form-control form-control-sm" value="" required>
            </div>
            <div class="position-relative">
                <label for="password">Employee Password: </label>
                <input type="password" id="password" name="password" class="form-control form-control-sm" value="<?= generate_random(8) ?>" required>
                <div class="invalid-feedback">Password is required.</div>
                <div class="pt-1 ml-1 custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input show-password" id="show-password-box">
                    <label class="custom-control-label" for="show-password-box">Show Password</label>
                </div>
            </div>
            <div class="position-relative">
                <label>User Type:</label>
                <select name="employee_type" class="form-control form-control-sm select2" data-placeholder="Select User Type" required>
                    <option></option>
                    <?php foreach($employee_types as $row):?>
                    <option value="<?=encode($row->employee_type_id)?>"><?=$row->employee_type_name?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
    </form>
</div>