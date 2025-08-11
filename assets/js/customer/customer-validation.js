$(function () {
	//Checkbox Selection Validation
	$(".w-100[data-min-selection][data-max-selection]").each(function () {
		const container = $(this);
		const minSelection = parseInt(container.attr("data-min-selection"));
		const maxSelection = parseInt(container.attr("data-max-selection"));
		const checkboxes = container.find('.form-check-input[type="checkbox"]');

		checkboxes.on("change", function () {
			const checkedCount = container.find(
				'.form-check-input[type="checkbox"]:checked'
			).length;

			if (checkedCount > maxSelection) {
				$(this).prop("checked", false);
				alert(`You can only select up to ${maxSelection} options.`);
			} else if (checkedCount < minSelection) {
				$(this).prop("checked", true);
				alert(`You must select at least ${minSelection} options.`);
			}
		});
	});

	//Special FE Field Validation
	$('[for="Email"]')
		.next("input")
		.on("keyup", function () {
			// const email = $(this).val();
			// const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

			$(this).attr("type", "email");

			// const isValid = emailRegex.test(email);

			// $(this).toggleClass('is-valid', isValid)
			//         .toggleClass('is-invalid', !isValid);
		});

	$('[for="Contact Number"]')
		.next("input")
		.on("keyup", function () {
			// const contactNumber = $(this).val();
			// const contactNumberRegex = /^09[0-9]{9}$/;

			$(this).attr("type", "tel");
			$(this).attr("maxlength", "11");

			// const isValid = contactNumberRegex.test(contactNumber);
			// console.log(isValid);
			// $(this).toggleClass('is-valid', isValid)
			//         .toggleClass('is-invalid', !isValid);
		});
});
