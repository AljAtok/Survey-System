$(document).ready(function () {
    const baseUrl  = $("#base_url").val();

    let url = document.location.toString();
    if (url.match('#')) {
        $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
    } 

    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash;
    })

    let forms = document.getElementsByClassName('needs-validation');
    let validation = Array.prototype.filter.call(forms, function(form) {
        let submitButton = $(form).find('button[type="submit"]');
        form.addEventListener('submit', function(event) {
            toggleButtonLoading(submitButton);
            event.preventDefault();
            event.stopPropagation();
            if (form.checkValidity() === false) {
                toggleButtonLoading(submitButton, 'Submit');
                showError('The form has a invalid field values.');

                //add a delay to the button to prevent spamming
            } else {
                let url  = $(form).attr('action');
                let data = new FormData($(form)[0]);
                $.ajax({
                    contentType: false,
                    processData: false,
                    url        : url,
                    data       : data,
                    method     : 'POST',
                    success: function(response){
                        try {
                            let parse_response = JSON.parse(response);
                            if(parse_response.result){
                                showSuccess(parse_response.message);
                                setTimeout(function() { 
                                    if (parse_response.redirect == '') {
                                        location.reload();
                                    } else {
                                        window.location.replace(parse_response.redirect);
                                    }
                                }, 1500);
                            } else {
                                if (parse_response.hasOwnProperty('data')) {
                                    for (let row in parse_response.data) {
                                        let fieldName = row;
                                        let rowData   = parse_response.data[row];
                                        let field = $(form).find(`[name="${fieldName}"]`).removeClass('is-valid').removeClass('is-invalid');
                                        field.addClass(rowData.is_valid);
                                        if (rowData.err_message != '') {
                                            form.classList.remove('was-validated');
                                            field.parents('.form-group').find('.invalid-feedback').text(rowData.err_message);
                                        }
                                    }
                                    showError('The form has a invalid field values.');
                                } else {
                                    showError(parse_response.message);
                                    if(parse_response.public) {
                                        setTimeout(function() { 
                                            if (parse_response.redirect == '') {
                                                location.reload();
                                            } else {
                                                window.location.replace(parse_response.redirect);
                                            }
                                        }, 1500);
                                    }
                                    if(parse_response.tampered) {
                                        setTimeout(function() { 
                                            location.reload();
                                        }, 1500);
                                    }
                                }
                            }
                        } catch (exception) {
                            showError('System Encountered an Error: Please Contact the System Administrator.');
                            setTimeout(function() { 
                                location.reload();
                            }, 5000);
                        }
                    },
                    //testing needed
                    error: function(jqXHR, textStatus, errorThrown) {
                        if (jqXHR.status == 403) {
                            showError('Survey Session Expired: Please Try Again.');
                            setTimeout(function() { 
                                location.reload();
                            }, 1500);
                        } else {
                            showError('An error occurred: ' + textStatus);
                        }
                        toggleButtonLoading(submitButton, 'Submit');
                    } //testing needed

                }).done(function() {
                    toggleButtonLoading(submitButton, 'Submit');
                });
            }
            form.classList.add('was-validated');
        }, false);
    });

    $(document).ready(function () {

    });


    function showError(message) {
        Lobibox.notify("error", { 
            size    : "mini",
            position: "top right",
            msg     : message,
            sound   : false,
            icon    : 'fas fa-exclamation-circle',
            delay   : 1500
        });
    }

    function showSuccess(message) {
        Lobibox.notify("success", { 
            size    : "mini",
            position: "top right",
            msg     : message,
            sound   : false,
            icon    : 'fas fa-check-circle',
            delay   : 1500
        });
    }

    function showWarning(message, size = 'mini', delay = 1500) {
        Lobibox.notify("warning", { 
            size    : size,
            position: "top right",
            msg     : message,
            sound   : false,
            icon    : (size == 'mini') ? 'fas fa-exclamation-circle' : false,
            delay   : delay
        });
    }

    function removeValidationClass(element) {
        if (element.hasClass('is-valid')) {
            element.removeClass('is-valid')
        }

        if (element.hasClass('is-invalid')) {
            element.removeClass('is-invalid')
        }
    }

    function toggleValidationClass(result, element) {
        if (result) {
            if (!element.hasClass('is-valid')) {
                if (element.hasClass('is-invalid')) {
                    element.removeClass('is-invalid')
                }
                element.addClass('is-valid')
            }
        } else {
            if (!element.hasClass('is-invalid')) {
                if (element.hasClass('is-valid')) {
                    element.removeClass('is-valid')
                }
                element.addClass('is-invalid')
            }
        }
    }

    function getSpinner() {
        return '<i class="fas fa-circle-notch fa-spin"></i>&nbsp;&nbsp;&nbsp;'
    }

    function toggleButtonLoading(element, defaultText = "") {
        let elementDisabledValue = $(element).prop("disabled");
        let spinner = $(
            "<div class=\"loader-container text-light text-center\">"
            + "<span class=\"fa fa-circle-notch fa-spin\"></span>"
            + "<span>&nbsp;Loading</span>"
            + "</div>");

        $(element).empty();
        if (elementDisabledValue) {
            $(element)
                .prop("disabled", !elementDisabledValue)
                .append(defaultText);
        } else {
            $(element)
                .prop("disabled", !elementDisabledValue)
                .append(spinner);
        }
    }

    // function validateContact(element) {
    //     let   elementValue = $(element).val();
    //     const baseUrl      = $("#base_url").val();
    //     let   url          = baseUrl + '/check-contact-prefix';
    //     if ($(element).val().length == 11) {
    //         let data = {
    //             contact_number : elementValue
    //         };
    //         $.ajax({
    //             method: "POST",
    //             url   : url,
    //             data  : data,
    //         }).done(response => {
    //             let responseData = JSON.parse(response);
    //             toggleValidationClass(responseData.result, element);
    //         });
    //     } else if ($(element).val().length < 11 && elementValue != '') {
    //         toggleValidationClass(false, element);
    //     } else {
    //         removeValidationClass(element);
    //     }
    // }
 
    
    //CDI & CTG Form Front End Validation
    const contactField = document.querySelector('#contactnumber');

    if (contactField) {
        let pattern = /^(09|\+639)\d{9}$/;

        contactField.addEventListener('input', function(event) {
            const maxLength = 11;
            let value = event.target.value;

            if (value.length > maxLength) {
                value = value.slice(0, maxLength);
                event.target.value = value;
            }

            if (pattern.test(contactField.value)) {
                contactField.classList.add('field-success');
                contactField.classList.remove('field-error');
            } else {
                contactField.classList.add('field-error');
                contactField.classList.remove('field-success');
            }
        });

        contactField.addEventListener('keydown', function(event) {
            const isNumericKey = (event.key >= '0' && event.key <= '9');
            const isAllowedKey = isNumericKey || event.key === 'Backspace' || event.key === 'Delete';

            if (!isAllowedKey) {
                event.preventDefault();
            }
        });
    }

    const emailField = document.querySelector('#email');

    if (emailField) {
        let pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        emailField.addEventListener('input', function() {

            if (pattern.test(emailField.value)) {
                emailField.classList.add('field-success');
                emailField.classList.remove('field-error');
            } else {
                emailField.classList.add('field-error');
                emailField.classList.remove('field-success');
            }
        });
    }

    const selectFields = document.querySelectorAll('select');

    if(selectFields){
        selectFields.forEach(function(selectField) {
            selectField.addEventListener('change', function() {
                if (selectField.value) {
                    selectField.classList.add('field-success');
                }
            });
        });
    }

    const textFields = document.querySelectorAll('input[type="text"]:not(#email)');

    textFields.forEach(function(textField) {
        textField.addEventListener('input', function() {
            // Check if the text field has an entry
            if (textField.value.trim()) {
                textField.classList.add('field-success');
                textField.classList.remove('field-error');
            } else {
                textField.classList.add('field-error');
                textField.classList.remove('field-success');
            }
        });
    });

    //CDI Validation for Others Script
    let radios = document.querySelectorAll('.others-radio');

    if (radios.length > 0) {
        
        radios.forEach(function(radio) {
            let input = radio.parentNode.nextElementSibling;
        
            input.disabled = !radio.checked;
        
            radio.addEventListener('change', function() {
                input.disabled = !radio.checked;
            });
        
            input.addEventListener('input', function() {
                radio.value = this.value;
            });
        
            // Add a change event listener to all the radio buttons in the group
            let radioGroup = document.querySelectorAll(`input[name="${radio.name}"]`);
            radioGroup.forEach(function(otherRadio) {
                if (otherRadio !== radio) {
                    otherRadio.addEventListener('change', function() {
                        input.disabled = true;
                    });
                }
            });
        });
    }

    //CDI Validation for Conditional Questions

    //Group A Condition
    const cdiGkkncncSelector = document.querySelector('#cdi-gkkncnc-selector');
    const cdiGroupAElements = document.querySelectorAll('.cdi-g-a');

    $(document).ready(function () {
        cdiCheckRadioButton();
    });

    function cdiCheckRadioButton() {
        if (cdiGkkncncSelector) {
            const defaultRadioButton3 = cdiGroupAElements[0].querySelector('input[type="radio"].default');

            const defaultRadioButton15 = cdiGroupAElements[1].querySelector('input[type="radio"].default');
            const defaultRadioButton16 = cdiGroupAElements[2].querySelector('input[type="radio"].default');
        
            if (cdiGkkncncSelector.value === cdiGkkncncSelector.options[0].value) {

                cdiGroupAElements[0].classList.add('d-none');
                cdiGroupAElements[2].classList.add('d-none');

                defaultRadioButton3.disabled = false;
                defaultRadioButton3.checked = true;

                defaultRadioButton16.disabled = false;
                defaultRadioButton16.checked = true;

                cdiGroupAElements[1].classList.remove('d-none');

                defaultRadioButton15.disabled = true;
                defaultRadioButton15.checked = false;
            }  else {

                cdiGroupAElements[0].classList.remove('d-none');
                cdiGroupAElements[2].classList.remove('d-none');

                defaultRadioButton3.disabled = true;
                defaultRadioButton3.checked = false;

                defaultRadioButton16.disabled = true;
                defaultRadioButton16.checked = false;

                cdiGroupAElements[1].classList.add('d-none');

                defaultRadioButton15.disabled = false;
                defaultRadioButton15.checked = true;
            }
        }
    }

    if (cdiGkkncncSelector) {
        cdiGkkncncSelector.addEventListener('change', function() {
            cdiCheckRadioButton();
        });
    }

    //Groub B Condition
    const cdiBkbSelector = document.querySelector('#cdi-bkb-selector');
    const cdiGroupBElements = document.querySelectorAll('.cdi-g-b');
    
    $(document).ready(function () {
        cdiGroupBElements.forEach(function(element) {
            
            let hasValue = Array.from(element.querySelectorAll('input[type="radio"]:not(.default)')).some(radio => radio.checked);
            
            if (hasValue) {
                element.classList.remove('d-none');
            }
        });  
    });

    if (cdiBkbSelector) {
        const defaultRadioButton26 = cdiGroupBElements[0].querySelector('input[type="radio"].default');
        const defaultRadioButton27 = cdiGroupBElements[1].querySelector('input[type="radio"].default');

        cdiBkbSelector.addEventListener('change', function() {

            if(cdiBkbSelector.value === cdiBkbSelector.options[0].value){
                cdiGroupBElements[1].classList.add('d-none');
                cdiGroupBElements[0].classList.remove('d-none');

                defaultRadioButton27.disabled = false; 
                defaultRadioButton27.checked = true;

                defaultRadioButton26.disabled = true; 
                defaultRadioButton26.checked = false;
            } else {
                cdiGroupBElements[0].classList.add('d-none');
                cdiGroupBElements[1].classList.remove('d-none');
   
                defaultRadioButton26.disabled = false; 
                defaultRadioButton26.checked = true;

                defaultRadioButton27.disabled = true; 
                defaultRadioButton27.checked = false;
            }
        });
    }

    //CTG Validation for Conditional Questions

    //Group A Condition
    const ctgGkkncncSelector = document.querySelector('#ctg-gkkncnc-selector');
    const ctgGroupAElements = document.querySelectorAll('.ctg-g-a');

    $(document).ready(function () {
        ctgCheckRadioButton();
    });

    function ctgCheckRadioButton() {
        if (ctgGkkncncSelector) {
            const defaultRadioButton3 = ctgGroupAElements[0].querySelector('input[type="radio"].default'); //43 -- FIRST TIME
            const defaultRadioButton15 = ctgGroupAElements[1].querySelector('input[type="radio"].default'); //44 -- RETURNING

            const defaultRadioButton16 = ctgGroupAElements[2].querySelector('input[type="radio"].default'); //31 -- GANO KA KADALAS
        
            if (ctgGkkncncSelector.value === ctgGkkncncSelector.options[0].value) {

                ctgGroupAElements[0].classList.remove('d-none');

                defaultRadioButton15.disabled = false;
                defaultRadioButton15.checked = true;

                defaultRadioButton16.disabled = false;
                defaultRadioButton16.checked = true;

                ctgGroupAElements[1].classList.add('d-none');
                ctgGroupAElements[2].classList.add('d-none');
                // defaultRadioButton16.disabled = true;
                // defaultRadioButton16.checked = false;

                defaultRadioButton3.disabled = true;
                defaultRadioButton3.checked = false;
            }  else {

                ctgGroupAElements[0].classList.add('d-none');

                defaultRadioButton15.disabled = true;
                defaultRadioButton15.checked = false;

                defaultRadioButton16.disabled = true;
                defaultRadioButton16.checked = false;

                ctgGroupAElements[1].classList.remove('d-none');
                ctgGroupAElements[2].classList.remove('d-none');

                // defaultRadioButton16.disabled = false;
                // defaultRadioButton16.checked = true;

                defaultRadioButton3.disabled = false;
                defaultRadioButton3.checked = true;
            }

            // console.log(defaultRadioButton3);
            // console.log(defaultRadioButton15);
            // console.log(defaultRadioButton16);
        }
    }

    if (ctgGkkncncSelector) {
        ctgGkkncncSelector.addEventListener('change', function() {
            ctgCheckRadioButton();
        });
    }

    //Groub B Condition
    const ctgBkbSelector = document.querySelector('#ctg-bkb-selector');
    const ctgGroupBElements = document.querySelectorAll('.ctg-g-b');

    // console.log(ctgBkbSelector);
    // // return;
    // console.log(ctgGroupBElements);
    
    $(document).ready(function () {
        ctgGroupBElements.forEach(function(element) {
            
            let hasValue = Array.from(element.querySelectorAll('input[type="radio"]:not(.default)')).some(radio => radio.checked);
            
            if (hasValue) {
                element.classList.remove('d-none');
            }
        });  
    });

    if (ctgBkbSelector) {
        const defaultRadioButton26 = ctgGroupBElements[0].querySelector('input[type="radio"].default');
        const defaultRadioButton27 = ctgGroupBElements[1].querySelector('input[type="radio"].default');

        ctgBkbSelector.addEventListener('change', function() {

            if(ctgBkbSelector.value === ctgBkbSelector.options[0].value){
                ctgGroupBElements[1].classList.add('d-none');
                ctgGroupBElements[0].classList.remove('d-none');

                defaultRadioButton27.disabled = false; 
                defaultRadioButton27.checked = true;

                defaultRadioButton26.disabled = true; 
                defaultRadioButton26.checked = false;
            } else {
                ctgGroupBElements[0].classList.add('d-none');
                ctgGroupBElements[1].classList.remove('d-none');
   
                defaultRadioButton26.disabled = false; 
                defaultRadioButton26.checked = true;

                defaultRadioButton27.disabled = true; 
                defaultRadioButton27.checked = false;
            }

            // console.log(defaultRadioButton26);
            // console.log(defaultRadioButton27);
        });
    }

    let loadingScreen = document.getElementById('loadingScreen');

    if (loadingScreen) {
        function updateLoadingScreen() {
            if (navigator.onLine) {
                loadingScreen.style.display = 'none';
            } else {
                loadingScreen.style.display = 'block';
            }
        }

        // Update the loading screen immediately on page load
        updateLoadingScreen();

        // Update the loading screen whenever the user's internet connection changes
        window.addEventListener('online', updateLoadingScreen);
        window.addEventListener('offline', updateLoadingScreen);
    } 

    //Success Page QR Code
    const qrCode = document.querySelector('#qrcode');

    if(qrCode){
        const code = document.querySelector('#qrcode').dataset.value;
        let qrcode = new QRCode(document.getElementById("qrcode"), {
            text: code,
            width: 128,
            height: 128,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    }

    $('.overlay__outer').attr('id', 'loaded__overlay');

});



