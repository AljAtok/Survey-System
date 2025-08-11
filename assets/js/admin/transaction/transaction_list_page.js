$(function () {
	function appendTableHeaders(table, headers) {
		const thead = table.find("thead");
		thead.empty();

		const headerRow = $("<tr></tr>");
		headers.forEach((header) => {
			const th = $("<th></th>").text(header);
			headerRow.append(th);
		});

		thead.append(headerRow);
	}

	function initializeDataTable(
		tableElement,
		selectedFormId,
		suffixUrl,
		columns
	) {
		if ($.fn.DataTable.isDataTable(tableElement)) {
			// Destroy the existing DataTable instance
			tableElement.DataTable().destroy();
			// Clear the table content (optional, to avoid duplicate rows)
			tableElement.find("tbody").empty();
		}

		tableElement.DataTable({
			serverSide: true,
			processing: true,
			ordering: false,
			ajax: {
				url: baseUrl + suffixUrl + selectedFormId,
				type: "POST",
				dataSrc: "data",
			},
			columns: columns,
		});
	}

	$("#form-select").on("change", function () {
		// console.log('Form selected:', $(this).val());
		let selectedFormId = $(this).val();
		console.log("Selected Form ID:", selectedFormId);

		const name = $(this).find("option:selected").text();

		const Table = $("#dynamic-table");
		Table.removeAttr("data-url");

		if (name.includes("CDI")) {
			appendTableHeaders(Table, [
				"Reference Number",
				"OR Number",
				"Store Name",
				"Name",
				"Contact Number",
				"Email",
				"Date",
				"Status",
				"Action",
			]);

			initializeDataTable(
				Table,
				selectedFormId,
				"/load_transaction_page_table/",
				[
					{ data: "reference_number" },
					{ data: "or_number" },
					{ data: "store_name" },
					{ data: "name" },
					{ data: "contact_number" },
					{ data: "email" },
					{ data: "date" },
					{ data: "status" },
					{ data: "action" },
				]
			);
		} else if (name.includes("CTG")) {
			appendTableHeaders(Table, [
				"Reference Number",
				"OR Number",
				"Name",
				"Contact Number",
				"Email",
				"Date",
				"Status",
				"Action",
			]);

			initializeDataTable(
				Table,
				selectedFormId,
				"/load_transaction_page_table/",
				[
					{ data: "reference_number" },
					{ data: "or_number" },
					{ data: "name" },
					{ data: "contact_number" },
					{ data: "email" },
					{ data: "date" },
					{ data: "status" },
					{ data: "action" },
				]
			);
		} else if (name.includes("CHOOKSTOGO QR PROMO")) {
			appendTableHeaders(Table, [
				"Reference Number",
				"Province",
				"Brgy & Town",
				"Name",
				"Contact Number",
				"Email",
				"Date",
				"Status",
				"Action",
			]);

			initializeDataTable(
				Table,
				selectedFormId,
				"/load_transaction_page_table_for_qr_promo/",
				[
					{ data: "reference_number" },
					{ data: "province_name" },
					{ data: "brgy_town_name" },
					{ data: "name" },
					{ data: "contact_number" },
					{ data: "email" },
					{ data: "date" },
					{ data: "status" },
					{ data: "action" },
				]
			);
		} else if (name.includes("UR QR PROMO")) {
			appendTableHeaders(Table, [
				"Reference Number",
				"Province",
				"Brgy & Town",
				"Name",
				"Contact Number",
				"Email",
				"Date",
				"Status",
				"Action",
			]);

			initializeDataTable(
				Table,
				selectedFormId,
				"/load_transaction_page_table_for_qr_promo/",
				[
					{ data: "reference_number" },
					{ data: "province_name" },
					{ data: "brgy_town_name" },
					{ data: "name" },
					{ data: "contact_number" },
					{ data: "email" },
					{ data: "date" },
					{ data: "status" },
					{ data: "action" },
				]
			);
		}
	});

	$("#form-select").trigger("change");

	//Set Active Tab via URL Hash
	const urlHash = window.location.hash;

	const tabLink = $(`a[href="${urlHash}"]`);

	if (tabLink.length) {
		tabLink.tab("show");
	} else {
		$("#nav-cdi-tab").tab("show");
	}

	const table = $("#nav-tabContent");

	if (table.length) {
		table.on("click", ".redeem-button", function (event) {
			event.preventDefault();

			const id = $(this).attr("id");

			Swal.fire({
				title: "Are you sure?",
				text: "You won't be able to revert this!",
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "Yes, redeem it!",
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						url: baseUrl + "/redeem_voucher",
						type: "POST",
						contentType: "application/json",
						data: JSON.stringify({ id: id }),
						success: function (data) {
							const parseData = JSON.parse(data);

							if (parseData.result) {
								Swal.fire({
									icon: "success",
									title: "Success",
									text: parseData.message,
								});
								cdiSurveyTable.length
									? cdiSurveyTable.DataTable().ajax.reload()
									: "";
								ctgSurveyTable.length
									? ctgSurveyTable.DataTable().ajax.reload()
									: "";
							} else {
								Swal.fire({
									icon: "error",
									title: "Error",
									text: parseData.message,
								});
								setTimeout(function () {
									if (parseData.refresh) {
										location.reload();
									}
								}, 1500);
							}
						},
					});
				}
			});
		});
	}

	// Transaction List, Disable Data - Ajax
	if (table.length) {
		table.on("click", ".disable-button", function (event) {
			event.preventDefault();

			const id = $(this).attr("id");

			Swal.fire({
				title: "Are you sure?",
				text: "You won't be able to revert this!",
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "Yes, disable it!",
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						url: baseUrl + "/disable_transaction",
						type: "POST",
						contentType: "application/json",
						data: JSON.stringify({ id: id }),
						success: function (data) {
							const parseData = JSON.parse(data);

							if (parseData.result) {
								Swal.fire({
									icon: "success",
									title: "Success",
									text: parseData.message,
								});
								cdiSurveyTable.length
									? cdiSurveyTable.DataTable().ajax.reload()
									: "";
								ctgSurveyTable.length
									? ctgSurveyTable.DataTable().ajax.reload()
									: "";
							} else {
								Swal.fire({
									icon: "error",
									title: "Error",
									text: parseData.message,
								});
								setTimeout(function () {
									if (parseData.refresh) {
										location.reload();
									}
								}, 1500);
							}
						},
					});
				}
			});
		});
	}
});
