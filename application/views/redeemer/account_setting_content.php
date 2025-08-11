<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="#">Home</a></li>
		<li class="breadcrumb-item active" aria-current="page"><?=$title?></li>
	</ol>
</nav>
<br>
<h3><?=$title?></h3>
<hr>
<div class="container-fluid">
		<nav>
			<div class="nav nav-tabs" id="nav-tab" role="tablist">
				<button	button class="nav-link active" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Profile</button>
				<button class="nav-link" id="nav-security-tab" data-bs-toggle="tab" data-bs-target="#nav-security" type="button" role="tab" aria-controls="nav-security" aria-selected="true">Security</button>

			</div>
		</nav>

		<div class="tab-content" id="nav-tabContent">
			<div class="tab-pane fade show active" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
				<br>
				<div class="row">
					<div class="col-md">
						<div class="form-group">
							<label for="fname">First Name: </label>
							<input type="text" class="form-control form-control-sm" value="<?=$employee->employee_fname?>" disabled>
						</div>
					</div>
					<div class="col-md">
						<div class="form-group">
							<label for="lname">Last Name: </label>
							<input type="text" class="form-control form-control-sm" value="<?=$employee->employee_lname?>" disabled>
						</div>
					</div>
					<div class="col-md">
						<div class="form-group">
							<label for="mname">Middle Name / Initial: </label>
							<input type="text" class="form-control form-control-sm" value="<?=$employee->employee_mname?>" disabled>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md">
						<div class="form-group">
							<label for="employee_no">Employee No: </label>
							<input type="text" class="form-control form-control-sm" value="<?=$employee->employee_no?>" disabled>
						</div>
					</div>
					<div class="col-md">
						<div class="form-group">
							<label for="email">Employee Email: </label>
							<input type="email" class="form-control form-control-sm" value="<?=$employee->employee_email?>" disabled>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="nav-security" role="tabpanel" aria-labelledby="nav-security-tab" tabindex="0">
				<br>
				<form method="POST" action="<?=base_url($controller.'/store-new-password')?>" class="needs-validation" novalidate>
					<input type="hidden" name="id" value="<?=$emp_id?>">
					<div class="form-group">
						<label for="password">Current Password: </label>
						<input type="password" id="currentpassword" name="currentpassword" class="form-control form-control-sm" value="" required>
						<div class="invalid-feedback">Current Password is required.</div>
					</div>

					<div class="form-group">
						<label for="password">New Password: </label>
						<input type="password" id="newpassword" name="newpassword" class="form-control form-control-sm" value="" required>
						<div class="invalid-feedback">New Password is required.</div>
					</div>

					<div class="form-group">
						<label for="password">Repeat New Password: </label>
						<input type="password" id="repeatnewpassword" name="repeatnewpassword" class="form-control form-control-sm" value="" required>
						<div class="invalid-feedback">Repeat New Password is required.</div>
					</div>
					<br>
					<button type="submit" class="btn btn-primary btn-main btn-sm" id="submit-btn">Save</button>
				</form>
			</div>
		</div>

</div>