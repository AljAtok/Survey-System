<div>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Transaction ID</th>
				<th>Amount</th>
				<th>Date</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($transaction as $row): ?>
				<tr>
					<td><?php echo $row['transaction_id']; ?></td>
					<td><?php echo $row['amount']; ?></td>
					<td><?php echo $row['date']; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>






<style>
	.table td, .table th {
		padding: 0.6rem;
	}
	.table-bordered td, .table-bordered th {
		border: 1px solid #dee2e6;
	}
	.table td, .table th {
		padding: 0.75rem;
		vertical-align: top;
		border-top: 1px solid #dee2e6;
	}
	.font-weight-bold {
		font-weight: 700;
	}
	.text-center {
		text-align: center;
	}
	.text-danger {
		color: #dc3545;
	}
	.table {
		font-size: 9px;
	}
</style>