<div class="modal-body">
	<div class="form-group text-center">
		<h4>You are redeeming code </h4>
		<h3><?= $row->reference_number ?></h3>
	</div>
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
	<button class="btn btn-sm btn-success text-white redeem-button" count="<?= $i ?>" id=" <?= encode($row->transaction_id) ?>" >Redeem</button>
</div>