<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="#">Home</a></li>
		<li class="breadcrumb-item "><a class="text-muted" href="<?=base_url($controller.'/safety-officer-report')?>" rel="noopener noreferrer">Safety Officer Report</a></li>
		<li class="breadcrumb-item active" aria-current="page"><?=$title?></li>
	</ol>
</nav>

<?php $medium   = 0; // 1 ?>
<?php $critical = 0; // 2?>
<?php $overdue  = 0; // 3?>
<?php 
	foreach($deadlines as $row){
		if ($row->reminder_type == 1) {
			$medium += 1;
		} elseif ($row->reminder_type == 2) {
			$critical += 1;
		} elseif ($row->reminder_type == 3) {
			$overdue += 1;
		}	
	}	
?>


<div class="row">
	<div class="col-md-2">
		<div class="card" style="border-left: 4px solid green">
			<div class="card-body">
				<h5 class="card-title text-success">Medium</h5>
				<h2><?=$medium?></h2>
			</div>
		</div>
	</div>
	<div class="col-md-2">
		<div class="card" style="border-left: 4px solid orange">
			<div class="card-body">
				<h5 class="card-title text-warning">Critical</h5>
				<h2><?=$critical?></h2>
			</div>
		</div>
	</div>
	<div class="col-md-2">
		<div class="card" style="border-left: 4px solid red">
			<div class="card-body">
				<h5 class="card-title text-danger">Overdue</h5>
				<h2><?=$overdue?></h2>
			</div>
		</div>
	</div>
</div>
<br>
<h3><?=$title . ' : ' . $employee->employee_fname . ' ' .$employee->employee_lname?></h3>
<hr>
<?= $this->session->flashdata('message') ?>
<div class="table-container">
	<br>
	<ul class="nav nav-tabs" id="myTab" role="tablist">
		<li class="nav-item" role="presentation">
			<a class="nav-link active" id="list-tab" data-toggle="tab" href="#list" role="tab" aria-controls="list" aria-selected="true">All</a>
		</li>
		<li class="nav-item" role="presentation">
			<a class="nav-link" id="medium-tab" data-toggle="tab" href="#medium" role="tab" aria-controls="medium" aria-selected="false">Medium</a>
		</li>
		<li class="nav-item" role="presentation">
			<a class="nav-link" id="critical-tab" data-toggle="tab" href="#critical" role="tab" aria-controls="critical" aria-selected="false">Critical</a>
		</li>
		<li class="nav-item" role="presentation">
			<a class="nav-link" id="overdue-tab" data-toggle="tab" href="#overdue" role="tab" aria-controls="overdue" aria-selected="false">Overdue</a>
		</li>
	</ul>
	<div class="tab-content" id="myTabContent">
		<div class="tab-pane fade show active" id="list" role="tabpanel" aria-labelledby="list-tab">
			<br>
			<div class="table-responsive">
				<table class="table table-striped mb-5 mt-3 w-100 data-table-ssr" data-url="/emp-profile-establishment-list/<?=encode($id)?>">
					<thead class="thead-dark">
						<tr>
							<th scope="col" rowspan="2">Brand</th>
							<th scope="col" rowspan="2">Type</th>
							<th scope="col" rowspan="2">Province</th>
							<th scope="col" rowspan="2">City/Municipality</th>
							<th scope="col" rowspan="2">Establishment Name</th>
							<th scope="col" rowspan="2">ERS ID</th>
							<th scope="col" rowspan="2">ERS Password</th>
							<th scope="col" colspan="2">Employee No</th>
							<th scope="col" colspan="2">Contract Employee No</th>
							<th scope="col" rowspan="2">BAVI Safety Officer Name</th>
							<th scope="col" rowspan="2">Third Party Safety Officer Name</th>
							<th scope="col" rowspan="2">First Aider Name</th>
							<th scope="col" rowspan="2">Establishment Status</th>
							<th scope="col" rowspan="2">Establishment Added</th>
							<th scope="col" rowspan="2">Action</th>
						</tr>
						<tr>
							<th scope="col">Male</th>
							<th scope="col">Female</th>
							<th scope="col">Male</th>
							<th scope="col">Female</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
				<br>
			</div>
		</div>
		<div class="tab-pane fade" id="medium" role="tabpanel" aria-labelledby="medium-tab">
			<br>
			<div class="table-responsive">
				<table class="table table-striped mb-5 mt-3 w-100 data-table-ssr" data-url="/emp-profile-medium-establishment-list/<?=encode($id)?>">
					<thead class="thead-dark">
						<tr>
							<th scope="col" rowspan="2">Brand</th>
							<th scope="col" rowspan="2">Type</th>
							<th scope="col" rowspan="2">Province</th>
							<th scope="col" rowspan="2">City/Municipality</th>
							<th scope="col" rowspan="2">Establishment Name</th>
							<th scope="col" rowspan="2">ERS ID</th>
							<th scope="col" rowspan="2">ERS Password</th>
							<th scope="col" colspan="2">Employee No</th>
							<th scope="col" colspan="2">Contract Employee No</th>
							<th scope="col" rowspan="2">BAVI Safety Officer Name</th>
							<th scope="col" rowspan="2">Third Party Safety Officer Name</th>
							<th scope="col" rowspan="2">First Aider Name</th>
							<th scope="col" rowspan="2">Establishment Status</th>
							<th scope="col" rowspan="2">Establishment Added</th>
							<th scope="col" rowspan="2">Action</th>
						</tr>
						<tr>
							<th scope="col">Male</th>
							<th scope="col">Female</th>
							<th scope="col">Male</th>
							<th scope="col">Female</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
				<br>
			</div>
		</div>
		<div class="tab-pane fade" id="critical" role="tabpanel" aria-labelledby="critical-tab">
			<br>
			<div class="table-responsive">
				<table class="table table-striped mb-5 mt-3 w-100 data-table-ssr" data-url="/emp-profile-critical-establishment-list/<?=encode($id)?>">
					<thead class="thead-dark">
						<tr>
							<th scope="col" rowspan="2">Brand</th>
							<th scope="col" rowspan="2">Type</th>
							<th scope="col" rowspan="2">Province</th>
							<th scope="col" rowspan="2">City/Municipality</th>
							<th scope="col" rowspan="2">Establishment Name</th>
							<th scope="col" rowspan="2">ERS ID</th>
							<th scope="col" rowspan="2">ERS Password</th>
							<th scope="col" colspan="2">Employee No</th>
							<th scope="col" colspan="2">Contract Employee No</th>
							<th scope="col" rowspan="2">BAVI Safety Officer Name</th>
							<th scope="col" rowspan="2">Third Party Safety Officer Name</th>
							<th scope="col" rowspan="2">First Aider Name</th>
							<th scope="col" rowspan="2">Establishment Status</th>
							<th scope="col" rowspan="2">Establishment Added</th>
							<th scope="col" rowspan="2">Action</th>
						</tr>
						<tr>
							<th scope="col">Male</th>
							<th scope="col">Female</th>
							<th scope="col">Male</th>
							<th scope="col">Female</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
				<br>
			</div>
		</div>
		<div class="tab-pane fade" id="overdue" role="tabpanel" aria-labelledby="overdue-tab">
			<br>
			<div class="table-responsive">
				<table class="table table-striped mb-5 mt-3 w-100 data-table-ssr" data-url="/emp-profile-overdue-establishment-list/<?=encode($id)?>">
					<thead class="thead-dark">
						<tr>
							<th scope="col" rowspan="2">Brand</th>
							<th scope="col" rowspan="2">Type</th>
							<th scope="col" rowspan="2">Province</th>
							<th scope="col" rowspan="2">City/Municipality</th>
							<th scope="col" rowspan="2">Establishment Name</th>
							<th scope="col" rowspan="2">ERS ID</th>
							<th scope="col" rowspan="2">ERS Password</th>
							<th scope="col" colspan="2">Employee No</th>
							<th scope="col" colspan="2">Contract Employee No</th>
							<th scope="col" rowspan="2">BAVI Safety Officer Name</th>
							<th scope="col" rowspan="2">Third Party Safety Officer Name</th>
							<th scope="col" rowspan="2">First Aider Name</th>
							<th scope="col" rowspan="2">Establishment Status</th>
							<th scope="col" rowspan="2">Establishment Added</th>
							<th scope="col" rowspan="2">Action</th>
						</tr>
						<tr>
							<th scope="col">Male</th>
							<th scope="col">Female</th>
							<th scope="col">Male</th>
							<th scope="col">Female</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
				<br>
			</div>
		</div>
	</div>
		
</div>
