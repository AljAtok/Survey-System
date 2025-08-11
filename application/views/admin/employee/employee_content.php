<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="#">Home</a></li>
		<li class="breadcrumb-item text-muted">File Maintenance</li>
		<li class="breadcrumb-item active" aria-current="page"><?=$title?></li>
	</ol>
</nav>
<br>
<h3><?=$title?></h3>
<hr>
<div class="table-container">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-4">
				<button class="btn btn-primary btn-sm employee-btn" data-component="Register_Employee" data-url="/store_employee">
					<span class="fas fa-plus-circle"></span> Register Employee
				</button>
			</div>
		</div>
	</div>
	<br>
	<div class="table-responsive">
		<table class="table table-striped mb-5 mt-3 w-100 employee-table" data-url="<?= base_url('admin/load_employee_list_table') ?>"">
			<thead class="table-dark">
				<tr>
					<th>Action</th>
					<th>Employee #</th>
					<th>Name</th>
					<th>Unit</th>
					<th>Location</th>
					<th>Email</th>
					<th>Contact #</th>
					<th>Employment Type</th>
					<th>User Type</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>

			</tbody>
		</table>
	</div>
</div>