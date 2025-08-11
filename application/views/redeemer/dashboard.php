<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="#">Home</a></li>
		<li class="breadcrumb-item active" aria-current="page"><?=$title?></li>
	</ol>
</nav>
<br>
<h3><?=$title?></h3>
<hr>
<form method="GET" action="<?= base_url("{$controller}/dashboard/") ?>">
	<div class="row">
		<div class="col-md-4 offset-md-7">
			<div class="form-group">
				<input type="text" class="form-control form-control-sm yearpicker" id="yearpicker" name="year" value="<?= $year ?>" placeholder="Pick Year" autocomplete="off">
			</div>
		</div>
		<div class="col-md-1">
			<button type="submit" class="btn btn-primary btn-sm">Filter</button>
		</div>
	</div>
</form>
<div class="table-container">
	<div class="table-responsive">
		<table class="table table-striped mb-5 mt-3 w-100">
			<thead class="thead-dark">
				<tr>
					<th scope="col">Business Center</th>
					<th scope="col">Transaction Count</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($business_centers as $row): ?>
				<tr>
					<td><?=$row->bc_name?></td>
					<td><?=$row->transaction_count?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>	
</div>
