const baseUrl = $("[data-base-url]").data("base-url");
const surveyForm = $("#survey-form");
const submitBtn = $("#submit-btn");

function toggleButtonLoading(element, defaultText = "") {
	const $element = $(element);
	const isDisabled = $element.prop("disabled");

	if (isDisabled) {
		$element.find("div").fadeOut(300, function () {
			$element.prop("disabled", false).html(defaultText);
		});
	} else {
		const spinner = `
            <div class="w-100 text-center" style="display: none;">
                <span class="fa fa-circle-notch fa-spin"></span>
                <span>&nbsp;Loading</span>
            </div>`;
		$element.prop("disabled", true).html(spinner);
		$element.find("div").fadeIn(300);
	}
}

function handleServerErrors(errors) {
	// Clear previous errors
	$(".is-invalid").removeClass("is-invalid");
	$(".invalid-tooltip").remove();

	let firstInvalidField = null;

	for (const [field, message] of Object.entries(errors)) {
		let inputElement = $(`[name="${field}"]`);
		if (field === "or_photo") {
			inputElement = $("#captured-photo-preview");
			inputElement.html(
				`<div class="alert alert-danger">Receipt photo is required</div>`
			);
		} else {
			inputElement.addClass("is-invalid");
			inputElement.after(`<div class="invalid-tooltip">${message}</div>`);
		}

		// Set the first invalid field
		if (!firstInvalidField) {
			firstInvalidField = inputElement;
		}
	}

	if (firstInvalidField) {
		$("html, body").animate(
			{
				scrollTop: firstInvalidField.offset().top - 100,
			},
			500
		);
		firstInvalidField.focus();
	}

	surveyForm.removeClass("was-validated");
}

function handleClientErrors(form) {
	if (form[0].checkValidity() === false) {
		form.addClass("was-validated");
		form[0].reportValidity();
		return false;
	}
	return true;
}

function updateCaptchaImage(image) {
	$("#captcha-container").find("img").replaceWith(image);
	$("#captcha-container").find("input").val("");
}

function updateCsrfHash(csrfHash) {
	surveyForm.data("csrf-hash", csrfHash);
}

$(function () {
	submitBtn.on("click", function (event) {
		event.preventDefault();

		toggleButtonLoading(submitBtn);

		if (!handleClientErrors(surveyForm)) {
			toggleButtonLoading(submitBtn, "Submit");
			return;
		}

		const csrfName = surveyForm.data("csrf-name");
		const csrfValue = surveyForm.data("csrf-hash");

		// const formData = surveyForm
		// 	.find(":input, textarea, select")
		// 	.serializeArray();
		// formData.push({ name: csrfName, value: csrfValue });
		// console.log(formData);

		const formData = new FormData(surveyForm[0]);
		formData.append(csrfName, csrfValue);

		// For debugging: log all formData entries
		// for (let [key, value] of formData.entries()) {
		// 	console.log(key, value);
		// }

		$.ajax({
			url: baseUrl + "/submit",
			type: "POST",
			data: formData,
			contentType: false,
			processData: false,

			success: function (response) {
				Swal.fire({
					icon: "success",
					title: "Submitted",
					showConfirmButton: false,
					timer: 1500,
				}).then((result) => {
					if (result.dismiss === Swal.DismissReason.timer) {
						window.location.href = response.redirect;
					}
				});
			},
			error: function (xhr) {
				let response = null;

				if (xhr.responseJSON) {
					response = xhr.responseJSON;
				} else if (xhr.responseText && xhr.responseText.trim() !== "") {
					try {
						response = JSON.parse(xhr.responseText);
					} catch (e) {
						window.location.reload(); // Reload page if response is not JSON
					}
				}

				if (response && response.errors) {
					handleServerErrors(response.errors);
				}

				if (response && response.image) {
					updateCaptchaImage(response.image);
				}

				if (response && response.csrf_hash) {
					updateCsrfHash(response.csrf_hash);
				}

				if (response && response.timer_interval) {
					Swal.fire({
						title: "Too many attempts",
						html: "Please try again in <b></b>",
						timer: response.timer_interval,
						timerProgressBar: true,
						allowOutsideClick: false,
						didOpen: () => {
							Swal.showLoading();
							const timer = Swal.getPopup().querySelector("b");
							timerInterval = setInterval(() => {
								const timeLeft = Swal.getTimerLeft();
								const minutes = Math.floor(timeLeft / 60000);
								const seconds = Math.floor((timeLeft % 60000) / 1000);
								timer.textContent = `${minutes}:${
									seconds < 10 ? "0" : ""
								}${seconds}`;
							}, 100);
						},
						willClose: () => {
							clearInterval(timerInterval);
						},
					});
				}

				toggleButtonLoading(submitBtn, "Submit");
			},
			// success: function(response) {
			//     response = JSON.parse(response);
			//     let timerInterval = 1500;

			//     if(response.status) {

			//         if (response.refresh) {
			//             location.reload();
			//         } else if(response.redirect) {
			//             window.location.href = response.redirect;
			//         }

			//     } else {

			//         if (response.hasOwnProperty('image')) {
			//             $('#captcha-container').find('img').replaceWith(response.image);
			//             $('#captcha-container').find('input').val('');
			//         }

			//         if (response.refresh) {
			//             location.reload();
			//         } else if(response.redirect) {
			//             window.location.href = response.redirect;
			//         }
			//     }
			//     surveyForm.data('csrf-hash', response.csrf_hash);

			//     if(response.hasOwnProperty('timer_interval')) {
			//         timerInterval = response.timer_interval;

			//     setTimeout(function() {
			//         toggleButtonLoading(submitBtn, 'Submit');
			//     }, timerInterval);
			// },
			// error: function(response) {
			//     setTimeout(function() {
			//         location.reload();
			//     }, 300000);
			// }
		});
	});

	$("#province").change(function () {
		let id = $(this).val();
		const csrfName = surveyForm.data("csrf-name");
		const csrfValue = surveyForm.data("csrf-hash");
		$("#town").empty();
		if (id) {
			var formData = [{ name: "province_id", value: id }];
			formData.push({ name: csrfName, value: csrfValue });
			$.ajax({
				url: baseUrl + "/get-town",
				data: $.param(formData),
				type: "POST",
				success: function (response) {
					var parse_response = JSON.parse(response);
					if (parse_response["result"] == 1) {
						$("#brgy").empty().trigger("change");
						$("#town").empty();
						$("#town").select2("destroy");
						$("#town").attr("data-placeholder", "Select Town");
						$("#town").select2({
							placeholder: $("#town").attr("data-placeholder"),
							allowClear: true,
							theme: "bootstrap-5",
						});
						$("#town").append(parse_response["info"]);
					}

					if (parse_response.csrf_hash) {
						updateCsrfHash(parse_response.csrf_hash);
					}
				},
			});
		}
	});

	$("#town").change(function () {
		let id = $(this).val();
		const csrfName = surveyForm.data("csrf-name");
		const csrfValue = surveyForm.data("csrf-hash");
		$("#brgy").empty();
		if (id) {
			var formData = [{ name: "barangay", value: id }];
			formData.push({ name: csrfName, value: csrfValue });
			$.ajax({
				url: baseUrl + "/get-barangay",
				data: $.param(formData),
				type: "POST",
				success: function (response) {
					var parse_response = JSON.parse(response);
					if (parse_response["result"] == 1) {
						$("#brgy").empty();
						$("#brgy").select2("destroy");
						$("#brgy").attr("data-placeholder", "Select Barangay");
						$("#brgy").select2({
							placeholder: $("#town").attr("data-placeholder"),
							allowClear: true,
							theme: "bootstrap-5",
						});
						$("#brgy").append(parse_response["info"]);
					}

					if (parse_response.csrf_hash) {
						updateCsrfHash(parse_response.csrf_hash);
					}
				},
			});
		}
	});
});
