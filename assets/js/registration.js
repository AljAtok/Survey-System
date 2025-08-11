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
                toggleButtonLoading(submitButton, 'Register');
            } else {
                let url  = $(form).attr('action');
                let data = new FormData($(form)[0]);
                // let data = $(form).serialize();
                $.ajax({
                    contentType: false,
                    processData: false,
                    url        : url,
                    data       : data,
                    method     : 'POST',
                    success: function(response){
                        let parse_response = JSON.parse(response);
                        if(parse_response.result){
                            showSuccess(parse_response.message);
                            setTimeout(() => window.location.replace(parse_response.redirect), 1000);
                        } else {
                            if (parse_response.hasOwnProperty('data')) {
                                toggleButtonLoading(submitButton, 'Register');
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
                            } else {
                                showError(parse_response.message);
                            }
                        }
                    }
                });
            }
            form.classList.add('was-validated');
        }, false);
    });

    $('.data-table').DataTable({ "aaSorting": [] });
    $('.per-page').on('change', function() {
        window.location = $(this).val();
    });

    formEvents();
    initElements();

    function formEvents() {

        $('.show-password').on('click', function () {
            let passwordField = $(this).parents('form').find('input[name="password"]');
            let type = ($(passwordField).prop('type') === "password") ? "text" : "password";
            $(passwordField).prop('type', type);
        });

        $('.custom-file-input').on('change',function(e){
            let uploadCount = e.target.files.length;
            let fileName    = (uploadCount > 1) ? uploadCount + ' Files' : e.target.files[0].name;
            $(this).next('.custom-file-label').html(fileName);
        });

        $('.validate-contact').keyup(event => {
            validateContact($(event.target));
        });

        $('.validate-email').blur(event => {
            validateEmail($(event.target));
        });

        $('.province-select').change(event => {
            addTownGroup(event.target);
        });

    }

    function initElements() {
        $('select').select2({
            theme           : 'bootstrap4',
            dropdownPosition: 'below',
        });
    }

    $.each($(".table-export"), function() {
        const table = $(this);
        const btn   = $('<button class="btn btn-primary btn-sm mb-3">Export Excel</button>');
        $(btn).insertBefore(table).click(() => {
            const sheetName = $(table).data('sheet-name');
            const fileName = $(table).data('file-name');
            exportTable(table.get(0), sheetName, fileName);
        });

        function exportTable(table, sheetName, fileName, type = "xlsx", fn, dl) {
            let elt = table;
            let wb = XLSX.utils.table_to_book(elt, {sheet:sheetName});
            return dl ?
                XLSX.write(wb, {bookType:type, bookSST:true, type: 'base64'}) :
                XLSX.writeFile(wb, fn || (fileName + '.' + (type || 'xlsx')));
        }
    });

    $('#sidebarCollapse').on('click', function () {
        // open or close navbar
        $('#sidebar').toggleClass('active');
        // close dropdowns
        $('.collapse.in').toggleClass('in');
        // and also adjust aria-expanded attributes we use for the open/closed arrows
        // in our CSS
        $('a[aria-expanded=true]').attr('aria-expanded', 'false');
        $('#content').toggleClass('active');
    });

    function showError(message) {
        Lobibox.notify("error", { 
            size          : "mini",
            position      : "top right",
            msg           : message,
            sound         : false,
            icon          : 'fas fa-exclamation-circle',
            delay         : 3000,
            delayIndicator: false
        });
    }

    function showSuccess(message) {
        Lobibox.notify("success", { 
            size          : "mini",
            position      : "top right",
            msg           : message,
            sound         : false,
            icon          : 'fas fa-check-circle',
            delay         : 3000,
            delayIndicator: false
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

    $(document).on('click', '.edit', function(e){
        e.preventDefault();
        let id  = $(this).attr('data-id');
        let url = $(this).attr('data-url');
        $.ajax({
            url: baseUrl + url + id,
            data: {id:id},
            method: 'POST',
            success:function(response){
                let parse_response = JSON.parse(response);
                if(parse_response['result'] == 1){
                    $('#modal-edit').find('.modal-body').html(parse_response['html']);
                    $('#modal-edit').modal({show:true});
                    initCouponElements();
                    formEvents();
                }else{
                    console.log('Error please contact your administrator.');
                }
            }
        });
    });

    $(document).on('click', '.reset', function(e){
        e.preventDefault();
        let id  = $(this).attr('data-id');
        let url = $(this).attr('data-url');
        $.ajax({
            url: baseUrl + url + id,
            data: {id:id},
            method: 'POST',
            success:function(response){
                let parse_response = JSON.parse(response);
                if(parse_response['result'] == 1){
                    $('#modal-reset').find('.modal-body').html(parse_response['html']);
                    $('#modal-reset').modal({show:true});
                    initCouponElements();
                    formEvents();
                }else{
                    console.log('Error please contact your administrator.');
                }
            }
        });
    });

    $(document).on('click', '.view-details', function(e){
        e.preventDefault();
        let id  = $(this).attr('data-id');
        let url = $(this).attr('data-url');
        $.ajax({
            url: baseUrl + url + id,
            data: {id:id},
            method: 'POST',
            success:function(response){
                let parse_response = JSON.parse(response);
                if(parse_response['result'] == 1){
                    $('#modal-view').find('.modal-body').html(parse_response['html']);
                    $('#modal-view').modal({show:true});
                    initCouponElements();
                    formEvents();
                }else{
                    console.log('Error please contact your administrator.');
                }
            }
        });
    });

    $(document).on('click', '.toggle-inactive', function(e){
        e.preventDefault();
        let id = $(this).attr('data-id');
        $('#activate-form').find('#id').val(id);
        $('#modal-active').modal({show:true});
    });

    $(document).on('click', '.toggle-active', function(e){
        e.preventDefault();
        let id = $(this).attr('data-id');
        $('#deactivate-form').find('#id').val(id);
        $('#modal-deactivate').modal({show:true});
    });

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

    function validateContact(element) {
        let   elementValue = $(element).val();
        const baseUrl      = $("#base_url").val();
        let   url          = baseUrl + '/check-contact-prefix';
        if ($(element).val().length == 11) {
            let data = {
                contact_number : elementValue
            };
            $.ajax({
                method: "POST",
                url   : url,
                data  : data,
            }).done(response => {
                let responseData = JSON.parse(response);
                toggleValidationClass(responseData.result, element);
            });
        } else if ($(element).val().length < 11 && elementValue != '') {
            toggleValidationClass(false, element);
        } else {
            removeValidationClass(element);
        }
    }

    function validateEmail(element) {
        let   elementValue = $(element).val();
        const baseUrl      = $("#base_url").val();
        let   url          = baseUrl + '/check-valid-email';
        let data = {
            email : elementValue
        };

        if (elementValue != '') {
            $.ajax({
                method: "POST",
                url   : url,
                data  : data,
            }).done(response => {
                let responseData = JSON.parse(response);
                toggleValidationClass(responseData.result, element);
            });
        } else {
            removeValidationClass(element);
        }
    }

    function addTownGroup(element) {
        const province         = $(element);
        const url              = baseUrl + '/get-town-group/' + province.val();
        let   townGroupELement = $(element).parents('form').find('.town-group-select');
        $(townGroupELement).load(url);
    }

});


