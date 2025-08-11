$(function () {
	function initializeDataTable(tableElement, columnsConfig) {
		let url = tableElement.data("url");

		const dataTable = tableElement.DataTable({
			serverSide: true,
			searching: false,
			ordering: false,
			ajax: {
				url: url,
				type: "GET",
				data: function (d) {
					d.date_filter = $('input[name="date-filter"]').val();
					d.store_filter = $('select[name="store-filter"]').val();
					d.loc_filter = $('select[name="province-filter"]').val();
				},
				dataSrc: "data",
			},
			columns: columnsConfig,
		});

		$(
			'input[name="date-filter"], select[name="store-filter"], select[name="province-filter"]'
		).on("change", function () {
			dataTable.ajax.reload();
		});
	}

	const cdiTableElement = $(".cdi-data-table");
	const ctgTableElement = $(".ctg-data-table");
	const qrPromoTableElement = $(".qr-promo-data-table");

	if (cdiTableElement.length) {
		initializeDataTable(cdiTableElement, [
			{ data: "reference_number" },
			{ data: "or_number" },
			{ data: "store_name" },
			{ data: "name" },
			{ data: "contact_number" },
			{ data: "email" },
			{ data: "date" },
		]);
	} else if (ctgTableElement.length) {
		initializeDataTable(ctgTableElement, [
			{ data: "reference_number" },
			{ data: "or_number" },
			{ data: "name" },
			{ data: "contact_number" },
			{ data: "email" },
			{ data: "date" },
		]);
	} else if (qrPromoTableElement.length) {
		initializeDataTable(qrPromoTableElement, [
			{ data: "reference_number" },
			{ data: "province_name" },
			{ data: "brgy_town_name" },
			{ data: "name" },
			{ data: "contact_number" },
			{ data: "email" },
			{ data: "is_winner" },
			{ data: "winning_date" },
			{ data: "date" },
		]);
	}
});
