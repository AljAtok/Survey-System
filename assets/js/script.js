const baseUrl  = $("#base_url").val();

$(document).ready(function () {


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
                toggleButtonLoading(submitButton, 'Save');
                showError('The form has a invalid field values.');
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
                        }
                    }
                }).done(function() {
                    toggleButtonLoading(submitButton, 'Save');
                });
            }
            form.classList.add('was-validated');
        }, false);
    });


    $('.data-table').DataTable({ "aaSorting": [] });

    formEvents();
    initElements();

    function formEvents() {
        $('.show-password').on('click', function () {
            let passwordField = $(this).parents('form').find('input[name="password"]');
            let type = ($(passwordField).prop('type') === "password") ? "text" : "password";
            $(passwordField).prop('type', type);
        });

        // $('.custom-file-input').on('change',function(e){
        //     let uploadCount = e.target.files.length;
        //     let fileName    = (uploadCount > 1) ? uploadCount + ' Files' : e.target.files[0].name;
        //     $(this).next('.custom-file-label').html(fileName);
        // });

        // $('.choice-select').on('change', function() {
        //     getChoiceFields($(this));
        // });

        // $('.reminder').each(function(i, obj) {
        //     $(this).on('input', function () {
        //         if ($(this).val() > 28) {
        //             if (!$(this).hasClass('is-invalid')) {
        //                 $(this).removeClass('is-valid');
        //                 $(this).addClass('is-invalid');
        //             }
        //         } else {
        //             if (!$(this).hasClass('is-valid')) {
        //                 $(this).removeClass('is-invalid');
        //                 $(this).addClass('is-valid');
        //             }

        //         }
        //     });
        // });

        // $('.validate-contact').blur(event => {
        //     validateContact($(event.target));
        // });

        // $('.bc-select').change(event => {
        //     addFarm(event.target);
        //     addDP(event.target);
        // });

        // $('.form-select').change(event => {
        //     addFormAddtlFields(event.target);
        // });

    }

    function initElements() {
        $('.select2').each(function () { 
            if ($(this).hasClass("select2-hidden-accessible")) {
                $(this).select2('destroy');
            }
            let placeholder = $(this).data('placeholder');
            $(this).select2({
                theme           : 'bootstrap4',
                dropdownPosition: 'auto',
                allowClear      : true,
                placeholder     : placeholder,
            });
        });

        // $(".timepicker").each(function() {
        //     $(this).datetimepicker({ 
        //         format: 'LT',
        //     });
        // });

            // $("#timepicker").datetimepicker({ 
            //     format: 'LT',
            // });


        // $(".yearpicker").datepicker( {
        //     uiLibrary: 'bootstrap4',
        //     format     : "yyyy",
        //     startView  : "years",
        //     minViewMode: 2
        // });

        // $(".monthpicker").datepicker( {
        //     uiLibrary: 'bootstrap4',
        //     format     : "yyyy-mm",
        //     startView  : "month",
        //     monthpicker: 2,
        //     minViewMode: 1
        // });


        // $(".datepicker").datepicker( {
        //     uiLibrary: 'bootstrap4',
        // });

        // let tables = $('.data-table-ssr');
        // tables.each(function () {
        //     // $(this).DataTable().destroy();
        //     if ( !$.fn.DataTable.isDataTable(this) ) {
        //         let table     = $(this);
        //         let dataUrl   = table.data('url');
        //         let dataTable = table.DataTable({
        //             "processing": true,
        //             "serverSide": true,
        //             "bSort"     : false,
        //             "ajax"      : {
        //                 'url' : baseUrl + dataUrl,
        //                 'data': function(data){
        //                     let dateRange      = table.parents('.table-container').find('.dt-ssr-date-range').val();
        //                     let search         = table.DataTable().search();

        //                     // let exportBtn      = table.parents('.table-container').find('.export-excel-btn');
        //                     // let exportBtnUrl   = exportBtn.data('url');
        //                     // exportBtn.attr('href', exportBtnUrl + `?search=${search}&daterange=${dateRange}`);

        //                     data.daterange = dateRange;
        //                 }
        //             },
        //             language: {
        //                 "processing": "Loading..."
        //             },
        //         });
        //     }
        // });

        // $('.dt-ssr-date-range').daterangepicker({
        //     alwaysShowCalendars: true,
        //     autoUpdateInput    : false,
        //     drops              : 'bottom',
        //     locale             : {
        //         cancelLabel: 'Clear',
        //     }
        // }).on('apply.daterangepicker', function(ev, picker) {
        //     $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        //     $(this).parents('.table-container').find('.data-table-ssr').DataTable().draw();
        // }).on('cancel.daterangepicker', function(ev, picker) {
        //     $(this).val('');
        //     $(this).parents('.table-container').find('.data-table-ssr').DataTable().draw();
        // });

        // $('.date-range').daterangepicker({
        //     alwaysShowCalendars : true,
        //     autoUpdateInput: false,
        //     locale: {
        //     cancelLabel: 'Clear',
        //     firstDay: 1
        //     }
        // }).on('apply.daterangepicker', function(ev, picker) {
        //     $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        // }).on('cancel.daterangepicker', function(ev, picker) {
        //     $(this).val('');
        // });
    }

    // $.each($(".table-export"), function() {
    //     const table = $(this);
    //     const btn   = $('<button class="btn btn-primary btn-sm mb-3">Export Excel</button>');
    //     $(btn).insertBefore(table).click(() => {
    //         const sheetName = $(table).data('sheet-name');
    //         const fileName = $(table).data('file-name');
    //         exportTable(table.get(0), sheetName, fileName);
    //     });

    //     function exportTable(table, sheetName, fileName, type = "xlsx", fn, dl) {
    //         let elt = table;
    //         let wb = XLSX.utils.table_to_book(elt, {sheet:sheetName});
    //         return dl ?
    //             XLSX.write(wb, {bookType:type, bookSST:true, type: 'base64'}) :
    //             XLSX.writeFile(wb, fn || (fileName + '.' + (type || 'xlsx')));
    //     }
    // });


    // $('#sidebarCollapse').on('click', function () {
    //     // open or close navbar
    //     $('#sidebar').toggleClass('active');
    //     // close dropdowns
    //     $('.collapse.in').toggleClass('in');
    //     // and also adjust aria-expanded attributes we use for the open/closed arrows
    //     // in our CSS
    //     $('a[aria-expanded=true]').attr('aria-expanded', 'false');
    //     $('#content').toggleClass('active');
    // });


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

    // function showWarning(message, size = 'mini', delay = 1500) {
    //     Lobibox.notify("warning", { 
    //         size    : size,
    //         position: "top right",
    //         msg     : message,
    //         sound   : false,
    //         icon    : (size == 'mini') ? 'fas fa-exclamation-circle' : false,
    //         delay   : delay
    //     });
    // }

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
                    formEvents();
                    initElements();
                }else{
                    console.log('Error please contact your administrator.');
                }
            }
        });
    });


    $(document).on('click', '.reset', function(e){ // Employee Reset Password
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
                    initElements();
                    formEvents();
                }else{
                    console.log('Error please contact your administrator.');
                }
            }
        });
    });

    // $(document).on('click', '.view-details', function(e){
    //     e.preventDefault();
    //     let id  = $(this).attr('data-id');
    //     let url = $(this).attr('data-url');
    //     $.ajax({
    //         url: baseUrl + url + id,
    //         data: {id:id},
    //         method: 'POST',
    //         success:function(response){
    //             let parse_response = JSON.parse(response);
    //             if(parse_response['result'] == 1){
    //                 $('#modal-view').find('.modal-body').html(parse_response['html']);
    //                 $('#modal-view').modal({show:true});
    //                 initElements();
    //                 formEvents();
    //             }else{
    //                 console.log('Error please contact your administrator.');
    //             }
    //         }
    //     });
    // });

    $(document).on('click', '.toggle-inactive', function(e){ //employees & form maintenance
        e.preventDefault();
        let id = $(this).attr('data-id');
        $('#activate-form').find('#id').val(id);
        $('#modal-active').modal({show:true});
    });

    $(document).on('click', '.toggle-active', function(e){ //employees & form maintenance
        e.preventDefault();
        let id = $(this).attr('data-id');
        $('#deactivate-form').find('#id').val(id);
        $('#modal-deactivate').modal({show:true});
    });

    // $(document).on('click', '.cancel', function(e){
    //     e.preventDefault();
    //     let id = $(this).attr('data-id');
    //     $('#cancel-form').find('#id').val(id);
    //     $('#modal-cancel').modal({show:true});
    // });

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

    function validateEmail(element) { // Employee Registration?
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


    //
    $("#scroll1 div").width($("#scroll2").find('table').width());

    $("#scroll1").on("scroll", function(){
        $("#scroll2").scrollLeft($(this).scrollLeft());
    });
    $("#scroll2").on("scroll", function(){
        $("#scroll1").scrollLeft($(this).scrollLeft());
    });

    // 
    // $(document).on('click', '.toggle-inactive2', function(e){
    //     e.preventDefault();
    //     let id = $(this).attr('data-id');
    //     $('#activate-form2').find('#id').val(id);
    //     $('#modal-active2').modal({show:true});
    // });

    // $(document).on('click', '.toggle-active2', function(e){
    //     e.preventDefault();
    //     let id = $(this).attr('data-id');
    //     $('#deactivate-form2').find('#id').val(id);
    //     $('#modal-deactivate2').modal({show:true});
    // });

    $('.overlay__outer').attr('id', 'loaded__overlay');

});



