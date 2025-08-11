document.addEventListener("DOMContentLoaded", function () {
	const input = document.querySelector('input[name="or_photo"]');
	const preview = document.getElementById("or-photo-preview");
	if (input && preview) {
		input.addEventListener("change", function (e) {
			preview.innerHTML = "";
			const file = e.target.files[0];
			if (file && (file.type === "image/jpeg" || file.type === "image/png")) {
				const reader = new FileReader();
				reader.onload = function (evt) {
					const wrapper = document.createElement("div");
					wrapper.style.position = "relative";
					wrapper.style.display = "inline-block";

					const img = document.createElement("img");
					img.src = evt.target.result;
					img.style.maxWidth = "200px";
					img.style.maxHeight = "200px";
					img.className = "img-thumbnail mt-2";
					img.style.cursor = "pointer";

					// Remove button
					const removeBtn = document.createElement("button");
					removeBtn.type = "button";
					removeBtn.innerHTML = "&times;";
					removeBtn.title = "Remove photo";
					removeBtn.style.position = "absolute";
					removeBtn.style.top = "5px";
					removeBtn.style.right = "5px";
					removeBtn.style.background = "rgba(0,0,0,0.6)";
					removeBtn.style.color = "#fff";
					removeBtn.style.border = "none";
					removeBtn.style.borderRadius = "50%";
					removeBtn.style.width = "28px";
					removeBtn.style.height = "28px";
					removeBtn.style.fontSize = "20px";
					removeBtn.style.cursor = "pointer";
					removeBtn.style.display = "flex";
					removeBtn.style.alignItems = "center";
					removeBtn.style.justifyContent = "center";
					removeBtn.addEventListener("click", function (ev) {
						ev.preventDefault();
						input.value = "";
						preview.innerHTML = "";
					});

					// Preview button
					const previewBtn = document.createElement("button");
					previewBtn.type = "button";
					previewBtn.innerHTML = "&#128269;"; // magnifying glass icon unicode
					previewBtn.title = "Preview photo";
					previewBtn.style.position = "absolute";
					previewBtn.style.top = "5px";
					previewBtn.style.right = "40px";
					previewBtn.style.background = "rgba(0,0,0,0.6)";
					previewBtn.style.color = "#fff";
					previewBtn.style.border = "none";
					previewBtn.style.borderRadius = "50%";
					previewBtn.style.width = "28px";
					previewBtn.style.height = "28px";
					previewBtn.style.fontSize = "16px";
					previewBtn.style.cursor = "pointer";
					previewBtn.style.display = "flex";
					previewBtn.style.alignItems = "center";
					previewBtn.style.justifyContent = "center";
					previewBtn.addEventListener("click", function (ev) {
						ev.preventDefault();
						const hoverImg = document.createElement("img");
						hoverImg.src = img.src;
						hoverImg.style.position = "fixed";
						hoverImg.style.top = "50%";
						hoverImg.style.left = "50%";
						hoverImg.style.transform = "translate(-50%, -50%)";
						hoverImg.style.maxWidth = "90vw";
						hoverImg.style.maxHeight = "90vh";
						hoverImg.style.zIndex = "9999";
						hoverImg.style.boxShadow = "0 0 20px rgba(0,0,0,0.5)";
						hoverImg.id = "hover-or-photo-img";
						document.body.appendChild(hoverImg);

						hoverImg.addEventListener("click", function () {
							hoverImg.remove();
						});
					});

					wrapper.appendChild(img);
					wrapper.appendChild(removeBtn);
					wrapper.appendChild(previewBtn);
					preview.appendChild(wrapper);
				};
				reader.readAsDataURL(file);
			}
		});
	}

	// const captureBtn = document.getElementById("capture-photo-btn");
	// const video = document.getElementById("camera-stream");
	// const canvas = document.getElementById("camera-canvas");
	// const takePhotoBtn = document.getElementById("take-photo-btn");
	// const closeCameraBtn = document.getElementById("close-camera-btn");
	// const photoPreview = document.getElementById("captured-photo-preview");
	// const photoInput = document.getElementById("or_photo_camera");
	// let stream = null;

	// captureBtn.addEventListener("click", async function () {
	// 	if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
	// 		let constraints = { video: true };
	// 		// Try to use facingMode if supported
	// 		try {
	// 			const supportedConstraints =
	// 				navigator.mediaDevices.getSupportedConstraints();
	// 			if (supportedConstraints.facingMode) {
	// 				constraints = { video: { facingMode: "environment" } };
	// 			}
	// 		} catch (e) {
	// 			// Ignore, fallback to default
	// 		}
	// 		try {
	// 			stream = await navigator.mediaDevices.getUserMedia(constraints);
	// 			video.srcObject = stream;
	// 			video.style.display = "block";
	// 			takePhotoBtn.style.display = "inline-block";
	// 			closeCameraBtn.style.display = "inline-block";
	// 			captureBtn.style.display = "none";
	// 		} catch (err) {
	// 			// alert("Unable to access camera: " + err.message);
	// 			Swal.fire({
	// 				icon: "error",
	// 				title: "Cannot access Camera",
	// 				html: "Unable to access camera: " + err.message,
	// 				showConfirmButton: true,
	// 			});
	// 		}
	// 	} else {
	// 		Swal.fire({
	// 			icon: "error",
	// 			title: "Camera Not Supported",
	// 			html: "Camera not supported in this browser.<br>Please use a different browser or device.",
	// 			showConfirmButton: true,
	// 		});
	// 	}
	// });

	// takePhotoBtn.addEventListener("click", function () {
	// 	canvas.width = video.videoWidth;
	// 	canvas.height = video.videoHeight;
	// 	canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height);
	// 	const dataUrl = canvas.toDataURL("image/png");
	// 	photoPreview.innerHTML =
	// 		'<img src="' +
	// 		dataUrl +
	// 		'" class="img-fluid rounded" style="max-height:320px;max-width:240px;">';
	// 	photoInput.value = dataUrl;
	// 	if (stream) {
	// 		stream.getTracks().forEach((track) => track.stop());
	// 	}
	// 	video.style.display = "none";
	// 	takePhotoBtn.style.display = "none";
	// 	closeCameraBtn.style.display = "none";
	// 	captureBtn.style.display = "inline-block";
	// });

	// closeCameraBtn.addEventListener("click", function () {
	// 	if (stream) {
	// 		stream.getTracks().forEach((track) => track.stop());
	// 	}
	// 	video.style.display = "none";
	// 	takePhotoBtn.style.display = "none";
	// 	closeCameraBtn.style.display = "none";
	// 	captureBtn.style.display = "inline-block";
	// });

	const captureBtn = document.getElementById("capture-photo-btn");
	const video = document.getElementById("camera-stream");
	const canvas = document.getElementById("camera-canvas");
	const takePhotoBtn = document.getElementById("take-photo-btn");
	const closeCameraBtn = document.getElementById("close-camera-btn");
	const photoPreview = document.getElementById("captured-photo-preview");
	const photoInput = document.getElementById("or_photo_camera");
	let stream = null;

	captureBtn.addEventListener("click", async function () {
		// Detect iOS device
		const isIOS =
			/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

		if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
			let constraints = {
				video: {
					width: { ideal: 1080 },
					height: { ideal: 1440 },
					aspectRatio: { ideal: 3 / 4 },
				},
			};

			// Always try to use the back (environment) camera first, fallback to default if not available
			if (
				"facingMode" in
				(navigator.mediaDevices.getSupportedConstraints
					? navigator.mediaDevices.getSupportedConstraints()
					: {})
			) {
				constraints.video.facingMode = { ideal: "environment" };
			}

			try {
				// Show camera UI
				video.style.display = "block";
				takePhotoBtn.style.display = "inline-block";
				closeCameraBtn.style.display = "inline-block";
				captureBtn.style.display = "none";

				stream = await navigator.mediaDevices.getUserMedia(constraints);
				video.srcObject = stream;
				video.play();

				// On iOS, force inline playback and controls to keep video in browser
				if (isIOS) {
					video.setAttribute("playsinline", "true");
					video.setAttribute("webkit-playsinline", "true");
					video.removeAttribute("controls");
					// Ensure capture button is visible and works as on Android
					takePhotoBtn.style.display = "inline-block";
				}
			} catch (err) {
				Swal.fire({
					icon: "error",
					title: "Cannot access Camera",
					html: "Unable to access camera: " + err.message,
					showConfirmButton: true,
				});
				// Hide camera UI on error
				video.style.display = "none";
				takePhotoBtn.style.display = "none";
				closeCameraBtn.style.display = "none";
				captureBtn.style.display = "inline-block";
			}
		} else {
			Swal.fire({
				icon: "error",
				title: "Camera Not Supported",
				html: "Camera not supported in this browser.<br>Please use a different browser or device.",
				showConfirmButton: true,
			});
		}
	});

	takePhotoBtn.addEventListener("click", function () {
		// Always use portrait dimensions
		const portraitWidth = 480;
		const portraitHeight = 640;
		canvas.width = portraitWidth;
		canvas.height = portraitHeight;

		// Calculate cropping to center the video in portrait
		const videoAspect = video.videoWidth / video.videoHeight;
		const canvasAspect = portraitWidth / portraitHeight;
		let sx = 0,
			sy = 0,
			sw = video.videoWidth,
			sh = video.videoHeight;

		if (videoAspect > canvasAspect) {
			// Video is wider than canvas, crop sides
			sw = video.videoHeight * canvasAspect;
			sx = (video.videoWidth - sw) / 2;
		} else if (videoAspect < canvasAspect) {
			// Video is taller than canvas, crop top/bottom
			sh = video.videoWidth / canvasAspect;
			sy = (video.videoHeight - sh) / 2;
		}

		canvas
			.getContext("2d")
			.drawImage(video, sx, sy, sw, sh, 0, 0, portraitWidth, portraitHeight);
		const dataUrl = canvas.toDataURL("image/png");
		photoPreview.innerHTML =
			'<img src="' +
			dataUrl +
			'" class="img-fluid rounded" style="max-height:480px;max-width:640px;">';
		photoInput.value = dataUrl;
		if (stream) {
			stream.getTracks().forEach((track) => track.stop());
		}
		video.style.display = "none";
		takePhotoBtn.style.display = "none";
		closeCameraBtn.style.display = "none";
		captureBtn.style.display = "inline-block";
	});

	closeCameraBtn.addEventListener("click", function () {
		if (stream) {
			stream.getTracks().forEach((track) => track.stop());
		}
		video.style.display = "none";
		takePhotoBtn.style.display = "none";
		closeCameraBtn.style.display = "none";
		captureBtn.style.display = "inline-block";
	});

	// Store the sample receipt image src on page load
	let sampleReceiptImgSrc = "";
	const origSampleImg = document.querySelector("#sample-receipt-preview img");
	if (origSampleImg) {
		sampleReceiptImgSrc = origSampleImg.src;
	}

	const sampleReceiptLink = document.getElementById("sample-receipt-link");
	const sampleReceiptPreview = document.getElementById(
		"sample-receipt-preview"
	);
	if (sampleReceiptLink && sampleReceiptPreview) {
		sampleReceiptLink.addEventListener("click", function (e) {
			e.preventDefault();
			// Prevent background scroll
			document.body.style.overflow = "hidden";

			// Always use the stored image src
			let imgSrc = sampleReceiptImgSrc;

			sampleReceiptPreview.style.display = "flex";
			sampleReceiptPreview.style.position = "fixed";
			sampleReceiptPreview.style.top = "0";
			sampleReceiptPreview.style.left = "0";
			sampleReceiptPreview.style.width = "100vw";
			sampleReceiptPreview.style.height = "100vh";
			sampleReceiptPreview.style.alignItems = "center";
			sampleReceiptPreview.style.justifyContent = "center";
			sampleReceiptPreview.style.background = "rgba(0,0,0,0.5)";
			sampleReceiptPreview.style.zIndex = "10000";

			sampleReceiptPreview.innerHTML = "";

			const modalContainer = document.createElement("div");
			modalContainer.style.position = "relative";
			modalContainer.style.background = "#fff";
			modalContainer.style.borderRadius = "12px";
			modalContainer.style.boxShadow = "0 4px 24px rgba(0,0,0,0.4)";
			modalContainer.style.padding = "16px 16px 8px 16px";
			modalContainer.style.display = "flex";
			modalContainer.style.flexDirection = "column";
			modalContainer.style.alignItems = "flex-end";

			const closeBtn = document.createElement("button");
			closeBtn.id = "sample-receipt-close-btn";
			closeBtn.innerHTML = "&times;";
			closeBtn.title = "Close";
			closeBtn.style.position = "absolute";
			closeBtn.style.top = "8px";
			closeBtn.style.right = "8px";
			closeBtn.style.background = "#f44336";
			closeBtn.style.color = "#fff";
			closeBtn.style.border = "none";
			closeBtn.style.borderRadius = "50%";
			closeBtn.style.width = "36px";
			closeBtn.style.height = "36px";
			closeBtn.style.fontSize = "24px";
			closeBtn.style.cursor = "pointer";
			closeBtn.style.zIndex = "10001";
			closeBtn.addEventListener("click", function () {
				sampleReceiptPreview.style.display = "none";
				sampleReceiptPreview.innerHTML = "";
				// Restore background scroll
				document.body.style.overflow = "";
				// Reset preview style
				sampleReceiptPreview.style.position = "absolute";
				sampleReceiptPreview.style.width = "";
				sampleReceiptPreview.style.height = "";
				sampleReceiptPreview.style.background = "#fff";
				sampleReceiptPreview.style.alignItems = "";
				sampleReceiptPreview.style.justifyContent = "";
				sampleReceiptPreview.style.top = "";
				sampleReceiptPreview.style.left = "";
				sampleReceiptPreview.style.zIndex = "10";
			});

			const img = document.createElement("img");
			img.src = imgSrc;
			img.alt = "Sample Receipt";
			img.style.maxWidth = "400px";
			img.style.maxHeight = "80vh";
			img.style.borderRadius = "10px";
			img.style.boxShadow = "0 4px 24px rgba(0,0,0,0.4)";
			img.style.display = "block";
			img.style.margin = "0 auto";

			modalContainer.appendChild(closeBtn);
			modalContainer.appendChild(img);
			sampleReceiptPreview.appendChild(modalContainer);
		});
	}
});
