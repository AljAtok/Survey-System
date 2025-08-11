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

        $('.custom-file-input').on('change',function(e){
            let uploadCount = e.target.files.length;
            let fileName    = (uploadCount > 1) ? uploadCount + ' Files' : e.target.files[0].name;
            $(this).next('.custom-file-label').html(fileName);
        });

        $('.choice-select').on('change', function() {
            getChoiceFields($(this));
        });

        $('.reminder').each(function(i, obj) {
            $(this).on('input', function () {
                if ($(this).val() > 28) {
                    if (!$(this).hasClass('is-invalid')) {
                        $(this).removeClass('is-valid');
                        $(this).addClass('is-invalid');
                    }
                } else {
                    if (!$(this).hasClass('is-valid')) {
                        $(this).removeClass('is-invalid');
                        $(this).addClass('is-valid');
                    }

                }
            });
        });

        // $('.validate-contact').blur(event => {
        //     validateContact($(event.target));
        // });

        $('.bc-select').change(event => {
            addFarm(event.target);
            addDP(event.target);
        });

        $('.form-select').change(event => {
            addFormAddtlFields(event.target);
        });

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

        $(".timepicker").each(function() {
            $(this).datetimepicker({ 
                format: 'LT',
            });
        });

            // $("#timepicker").datetimepicker({ 
            //     format: 'LT',
            // });


        $(".yearpicker").datepicker( {
            uiLibrary: 'bootstrap4',
            format     : "yyyy",
            startView  : "years",
            minViewMode: 2
        });

        $(".monthpicker").datepicker( {
            uiLibrary: 'bootstrap4',
            format     : "yyyy-mm",
            startView  : "month",
            monthpicker: 2,
            minViewMode: 1
        });


        $(".datepicker").datepicker( {
            uiLibrary: 'bootstrap4',
        });

        let tables = $('.data-table-ssr');
        tables.each(function () {
            // $(this).DataTable().destroy();
            if ( !$.fn.DataTable.isDataTable(this) ) {
                let table     = $(this);
                let dataUrl   = table.data('url');
                let dataTable = table.DataTable({
                    "processing": true,
                    "serverSide": true,
                    "bSort"     : false,
                    "ajax"      : {
                        'url' : baseUrl + dataUrl,
                        'data': function(data){
                            let dateRange      = table.parents('.table-container').find('.dt-ssr-date-range').val();
                            let search         = table.DataTable().search();

                            // let exportBtn      = table.parents('.table-container').find('.export-excel-btn');
                            // let exportBtnUrl   = exportBtn.data('url');
                            // exportBtn.attr('href', exportBtnUrl + `?search=${search}&daterange=${dateRange}`);

                            data.daterange = dateRange;
                        }
                    },
                    language: {
                        "processing": "Loading..."
                    },
                });
            }
        });

        $('.dt-ssr-date-range').daterangepicker({
            alwaysShowCalendars: true,
            autoUpdateInput    : false,
            drops              : 'bottom',
            locale             : {
                cancelLabel: 'Clear',
            }
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            $(this).parents('.table-container').find('.data-table-ssr').DataTable().draw();
        }).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $(this).parents('.table-container').find('.data-table-ssr').DataTable().draw();
        });

        $('.date-range').daterangepicker({
            alwaysShowCalendars : true,
            autoUpdateInput: false,
            locale: {
            cancelLabel: 'Clear',
            firstDay: 1
            }
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        }).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
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

    //Edit Cut Off
    $(document).on('click', '.editcutoff', function(e){
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
                    $('#modal-cut-off').find('.modal-body').html(parse_response['html']);
                    $('#modal-cut-off').modal({show:true});
                    formEvents();
                    initElements();
                }else{
                    console.log('Error please contact your administrator.');
                }
            }
        });
    });

    // //Edit Cost Center Name/Code
    // $(document).on('click', '.edit-costcenter', function(e){
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
    //                 $('#modal-costcenter').find('.modal-body').html(parse_response['html']);
    //                 $('#modal-costcenter').modal({show:true});
    //                 formEvents();
    //                 initElements();
    //             }else{
    //                 console.log('Error please contact your administrator.');
    //             }
    //         }
    //     });
    // });

    // //Edit Division Name/Code
    // $(document).on('click', '.edit-division', function(e){
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
    //                 $('#modal-division').find('.modal-body').html(parse_response['html']);
    //                 $('#modal-division').modal({show:true});
    //                 formEvents();
    //                 initElements();
    //             }else{
    //                 console.log('Error please contact your administrator.');
    //             }
    //         }
    //     });
    // });

    // //Cost Center & Division Active/Inactive Toggles
    // //Cost Center
    // $(document).on('click', '.toggle-inactive-cost-center', function(e){
    //     e.preventDefault();
    //     let id = $(this).attr('data-id');
    //     $('#activate-cost-center').find('#id').val(id);
    //     $('#modal-active-cost-center').modal({show:true});
    // });

    // $(document).on('click', '.toggle-active-cost-center', function(e){
    //     e.preventDefault();
    //     let id = $(this).attr('data-id');
    //     $('#deactivate-cost-center').find('#id').val(id);
    //     $('#modal-deactivate-cost-center').modal({show:true});
    // });

    // //Division
    // $(document).on('click', '.toggle-inactive-division', function(e){
    //     e.preventDefault();
    //     let id = $(this).attr('data-id');
    //     $('#activate-division').find('#id').val(id);
    //     $('#modal-active-division').modal({show:true});
    // });

    // $(document).on('click', '.toggle-active-division', function(e){
    //     e.preventDefault();
    //     let id = $(this).attr('data-id');
    //     $('#deactivate-division').find('#id').val(id);
    //     $('#modal-deactivate-division').modal({show:true});
    // });


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
                    initElements();
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
                    initElements();
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

    $(document).on('click', '.cancel', function(e){
        e.preventDefault();
        let id = $(this).attr('data-id');
        $('#cancel-form').find('#id').val(id);
        $('#modal-cancel').modal({show:true});
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

    // function validateEmail(element) {
    //     let   elementValue = $(element).val();
    //     const baseUrl      = $("#base_url").val();
    //     let   url          = baseUrl + '/check-valid-email';
    //     let data = {
    //         email : elementValue
    //     };

    //     if (elementValue != '') {
    //         $.ajax({
    //             method: "POST",
    //             url   : url,
    //             data  : data,
    //         }).done(response => {
    //             let responseData = JSON.parse(response);
    //             toggleValidationClass(responseData.result, element);
    //         });
    //     } else {
    //         removeValidationClass(element);
    //     }
    // }

    // function addFarm(element) {
    //     const bc          = $(element);
    //     const url         = baseUrl + '/get-farm/' + bc.val();
    //     let   farmElement = $(element).parents('form').find('.farm-select');
    //     $(farmElement).load(url);
    // }

    // function addDP(element) {
    //     const bc        = $(element);
    //     const url       = baseUrl + '/get-dp/' + bc.val();
    //     let   dpElement = $(element).parents('form').find('.dp-select');
    //     $(dpElement).load(url);
    // }

    // $(document).on('click', '.view-attachments', function(e){
    //     e.preventDefault();
    //     let url            = $(this).attr('data-url');
    //     let transaction_id = $(this).attr('data-id');
    //     let data = { transaction_id: transaction_id };
    //     $.ajax({
    //         url    : baseUrl + url,
    //         data   : data,
    //         method : 'POST',
    //         success: function(response){
    //             let parse_response = JSON.parse(response);
    //             if(parse_response['result'] == 1){
    //                 $('#modal-view').find('.modal-body').html(parse_response['html']);
    //                 $('#modal-view').modal({show:true});
    //             }else{
    //                 console.log('Error please contact your administrator.');
    //             }
    //         }
    //     });
    // });

    // $(document).on('click', '#upload-btn', function(e){
    //     let files    = $('#file-upload')[0].files;
    //     let formData = new FormData();
    //     let uploadUrl  = $(this).attr('data-url');
    //     if(files.length > 0 ){
    //         formData.append('file-upload',files[0]);
    //         $.ajax({
    //             url    : baseUrl+ uploadUrl,
    //             data   : formData,
    //             method : 'POST',
    //             contentType: false,
    //             processData: false,
    //             beforeSend: function (){
    //                 $('#loader-div').removeClass('loaded');
    //             },
    //             complete: function (){
    //                 $('#loader-div').addClass('loaded');
    //                 $('#file-upload').val('');
    //             },
    //             success: function(response){
    //                 var parse_response = JSON.parse(response);
    //                 if(parse_response['result'] == 1){
    //                     $('#table-body').empty();
    //                     $('#table-body').html(parse_response['html']);
    //                     if (parse_response['is_error'] == 1) {
    //                         showError(parse_response['message']);
    //                     } else {
    //                         showSuccess(parse_response['message']);
    //                     }
    //                 }else{
    //                     $('#table-body').empty();
    //                     showError(parse_response['message']);
    //                     console.log('Error please contact your administrator.');
    //                 }
    //             }
    //         });
    //     } else {
    //         showError('Please Select a File to Upload');
    //     }
    // });

    $(document).on('click', '.show-count', function(e){
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
                    $('#modal-show').find('.modal-body').html(parse_response['html']);
                    $('#modal-show').modal({show:true});
                    formEvents();
                    initElements();
                }else{
                    console.log('Error please contact your administrator.');
                }
            }
        });
    });


    window.addEventListener('online', () => {
        $('#color-network-status').css('color', '#5cb85c')
        $('#text-network-status').text('Online');
    });
    window.addEventListener('offline', () => {
        $('#color-network-status').css('color', '#b85c5c')
        $('#text-network-status').text('Offline');
    });

    $("#scroll1 div").width($("#scroll2").find('table').width());

    $("#scroll1").on("scroll", function(){
        $("#scroll2").scrollLeft($(this).scrollLeft());
    });
    $("#scroll2").on("scroll", function(){
        $("#scroll1").scrollLeft($(this).scrollLeft());
    });

    $(document).on('click', '.edit-group', function(e){
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
                    $('#modal-edit-group').find('.modal-body').html(parse_response['html']);
                    $('#modal-edit-group').modal({show:true});
                    formEvents();
                    initElements();
                }else{
                    console.log('Error please contact your administrator.');
                }
            }
        });
    });

    // function getChoiceFields(element) {
    //     const hasChoice      = $(element);
    //     const url            = baseUrl + '/get-choice/' + hasChoice.val();
    //     let   brandContainer = $(element).parents('form').find('.choice-container');
    //     $(brandContainer).load(url, initElements());
    // }

    $(document).on('click', '.toggle-inactive2', function(e){
        e.preventDefault();
        let id = $(this).attr('data-id');
        $('#activate-form2').find('#id').val(id);
        $('#modal-active2').modal({show:true});
    });

    $(document).on('click', '.toggle-active2', function(e){
        e.preventDefault();
        let id = $(this).attr('data-id');
        $('#deactivate-form2').find('#id').val(id);
        $('#modal-deactivate2').modal({show:true});
    });


    // System Specific Functions
    // function computeCleanAreaField(fieldValue, fieldName, destinationField) {
    //     $.ajax({
    //         url    : baseUrl + '/compute_clean_area_field',
    //         data   : {
    //             field         : fieldName,
    //             value         : fieldValue,
    //             form_header_id: $('#header_id').val(),
    //         },
    //         method : 'POST',
    //         success: function(response){
    //             let parse_response = JSON.parse(response);
    //             if(parse_response['result'] == 1){
    //                 let data = parse_response['data'];
    //                 $(destinationField).val(data);
    //             }else{
    //                 console.log('Error please contact your administrator.');
    //             }
    //         }
    //     });
    // };

    // function calculateTimeDiff(startTime, endTime, element) {
    //     $.ajax({
    //         url: baseUrl + '/get_today' ,
    //         method: 'POST',
    //         success:function(response){
    //             let parse_response = JSON.parse(response);
    //             if(parse_response['result'] == 1){
    //                 let dateToday = parse_response['data'];
    //                 let startDate = new Date(dateToday + ' ' + startTime);
    //                 let endDate   = new Date(dateToday + ' ' + endTime);
    //                 let seconds   = (endDate.getTime() - startDate.getTime()) / 1000;
    //                 let minutes   = seconds / 60;
    //                 // let hours     = minutes / 60;
    //                 let calculatedValue = (minutes < 0) ? 0 : minutes;
    //                 $(element).val(calculatedValue);

    //                 $(element).trigger('change');
    //             }else{
    //                 console.log('Error please contact your administrator.');
    //             }
    //         }
    //     });
    // };

    // function calculatePercentage(fieldOne, fieldTwo, fieldThree) {
    //     let elementOne    = parseInt($(fieldOne).val());
    //     let elementTwo    = parseInt($(fieldTwo).val());
    //     let computedValue = (elementOne / elementTwo) * 100;
    //     let roundUpValue  = Math.round((computedValue + Number.EPSILON) * 100) / 100;
    //     if (!isNaN(roundUpValue)) {
    //         $(fieldThree).val(roundUpValue);
    //         $(fieldThree).trigger('change');
    //     }
    // }

    // dirtyFieldEvents();
    // function dirtyFieldEvents() {
    //     //Dirty Area Custom Events

    //     $('#truck_vol').on('blur', function() {
    //         let truckVol = $(this).val();
    //         $('#dirty_area_field_7').val(truckVol);
    //         $('#dirty_area_field_14').val(truckVol);
    //         calculatePercentage('#dirty_area_field_6', '#dirty_area_field_7', '#dirty_area_field_8')
    //         calculatePercentage('#dirty_area_field_13', '#dirty_area_field_14', '#dirty_area_field_15');
    //     });

    //     let fields = ['#dirty_area_field_6', '#dirty_area_field_7', '#dirty_area_field_8'];
    //     fields.forEach(function(element, index, arr) {
    //         $(element).on('blur', function() {
    //             calculatePercentage('#dirty_area_field_6', '#dirty_area_field_7', '#dirty_area_field_8')
    //         });
    //     });

    //     fields = ['#dirty_area_field_13', '#dirty_area_field_14', '#dirty_area_field_15'];
    //     fields.forEach(function(element, index, arr) {
    //         $(element).on('blur', function() {
    //             calculatePercentage('#dirty_area_field_13', '#dirty_area_field_14', '#dirty_area_field_15');
    //         });
    //     });

    //     fields = ['#dirty_area_field_23', '#dirty_area_field_24'];
    //     fields.forEach(function(element, index, arr) {
    //         $(element).on('blur', function(e) {
    //             let dirty_area_field_23 = parseInt($('#dirty_area_field_23').val());
    //             $('#dirty_area_field_24').val(dirty_area_field_23 * 60);
    //         });
    //     });

    //     fields = ['#dirty_area_field_27', '#dirty_area_field_28', '#dirty_area_field_29'];
    //     fields.forEach(function(element, index, arr) {
    //         $(element).on('change.datetimepicker', function(e) {
    //             let startTime        = $('[name="dirty_area_field_27"]').val();
    //             let endTime          = $('[name="dirty_area_field_28"]').val();
    //             calculateTimeDiff(startTime, endTime, '[name="dirty_area_field_29"]')
    //         });
    //     });

    //     fields = ['#dirty_area_field_30', '#dirty_area_field_31', '#dirty_area_field_32'];
    //     fields.forEach(function(element, index, arr) {
    //         $(element).on('change.datetimepicker', function(e) {
    //             let startTime        = $('[name="dirty_area_field_30"]').val();
    //             let endTime          = $('[name="dirty_area_field_31"]').val();
    //             let elementContainer = fields[2];
    //             calculateTimeDiff(startTime, endTime, '[name="dirty_area_field_32"]')
    //         });
    //     });


    //     //Clean Area Custom Events
    //     let cleanAreaFieldsOne = 
    //         ['#clean_area_field_13', '#clean_area_field_14', '#clean_area_field_15', '#clean_area_field_16', '#clean_area_field_17'];
    //     let cleanAreaFieldsTwo = 
    //         ['#clean_area_field_18', '#clean_area_field_19', '#clean_area_field_20', '#clean_area_field_21', '#clean_area_field_22'];
    //     cleanAreaFieldsOne.forEach(function(element, index, arr) {
    //         $(element).on('change', function(e) {
    //             let fieldValue = $(element).val();
    //             computeCleanAreaField(fieldValue, element, cleanAreaFieldsTwo[index]);
    //         });
    //     });

    //     let cleanAreaFieldsThree = 
    //         ['#clean_area_field_31', '#clean_area_field_32', '#clean_area_field_33', '#clean_area_field_34', '#clean_area_field_35'];
    //     let cleanAreaFieldsFour = 
    //         ['#clean_area_field_36', '#clean_area_field_37', '#clean_area_field_38', '#clean_area_field_39', '#clean_area_field_40'];
    //     cleanAreaFieldsThree.forEach(function(element, index, arr) {
    //         $(element).on('change', function(e) {
    //             let fieldValue = $(element).val();
    //             computeCleanAreaField(fieldValue, element, cleanAreaFieldsFour[index]);
    //         });
    //     });

    //     $('#clean_area_field_27').on('change.datetimepicker', function() {
    //         let fieldValue = $('[name="clean_area_field_27"]').val();
    //         computeCleanAreaField(fieldValue, '#clean_area_field_27', '#clean_area_field_28');
    //     });

    //     $('#clean_area_field_9').on('change.datetimepicker', function() {
    //         let fieldValue = $('[name="clean_area_field_9"]').val();
    //         computeCleanAreaField(fieldValue, '#clean_area_field_9', '#clean_area_field_10');
    //     });
        
    //     $('#clean_area_field_76').on('change.datetimepicker', function() {
    //         let fieldValue = $('[name="clean_area_field_76"]').val();
    //         computeCleanAreaField(fieldValue, '#clean_area_field_76', '#clean_area_field_77');
    //     });

    // }

    // function saveAsDraft(isDraft){
    //     let form     = $('#transaction-form');
    //     let draftBtn = $('#draft-btn');
    //     let url      = baseUrl + '/draft-transaction';
    //     let data     = new FormData($(form)[0]);

    //     toggleButtonLoading(draftBtn);
    //     $.ajax({
    //         contentType: false,
    //         processData: false,
    //         url        : url,
    //         data       : data,
    //         method     : 'POST',
    //         beforeSend : function (){
    //             $('.overlay__outer').attr('id', '');
    //         },
    //         complete: function (){
    //             $('.overlay__outer').attr('id', 'loaded__overlay');
    //         },
    //         success: function(response){
    //             try {
    //                 let parse_response = JSON.parse(response);
    //                 if(parse_response.result){
    //                     $('#header_id').val(parse_response.data);
    //                     if (isDraft) {
    //                         showSuccess(parse_response.message);
    //                     }
    //                 } else {
    //                     if (parse_response.hasOwnProperty('data')) {
    //                         for (let row in parse_response.data) {
    //                             let fieldName = row;
    //                             let rowData   = parse_response.data[row];
    //                             let field = $(form).find(`[name="${fieldName}"]`).removeClass('is-valid').removeClass('is-invalid');
    //                             field.addClass(rowData.is_valid);
    //                             if (rowData.err_message != '') {
    //                                 field.parents('.form-group').find('.invalid-feedback').text(rowData.err_message);
    //                             }
    //                         }
    //                         showError('The form has a invalid field values.');
    //                     } else {
    //                         showError(parse_response.message);
    //                     }
    //                 }
    //             } catch (exception) {
    //                 showError('System Encountered an Error: Please Contact the System Administrator.');
    //             }
    //         }
    //     }).done(function() {
    //         toggleButtonLoading(draftBtn, 'Save as Draft');
    //     });

    // }

    // $('.draft-btn').on('click', function () {
    //     saveAsDraft(true);
    // });

    // $('.custom-file-input').on('change',function(e){
    //     let uploadCount = e.target.files.length;
    //     let files       = e.target.files;
    //     let element     = $(this);

    //     if(uploadCount > 0 ){
    //         let formData = new FormData();
    //         for (let x = 0; x < uploadCount; x++) {
    //             formData.append("temp_file_attachment[]", e.target.files[x]);
    //         }
    //         $.ajax({
    //             url        : baseUrl + '/upload-temp-file',
    //             data       : formData,
    //             method     : 'POST',
    //             contentType:false,
    //             processData: false,
    //             beforeSend : function (){
    //                 $('.overlay__outer').attr('id', '');
    //             },
    //             complete: function (){
    //                 $('.overlay__outer').attr('id', 'loaded__overlay');
    //             },
    //             success: function(response){
    //                 var parse_response = JSON.parse(response);
    //                 if(parse_response['result'] == 1){
    //                     let idContainer = $(element).parents('.custom-file').find('.attachment_ids');
    //                     $(idContainer).val(parse_response['data']);
    //                 }else{
    //                     showError(parse_response['message']);
    //                     console.log('Error please contact your administrator.');
    //                 }
    //             }
    //         });
    //     } else {
    //         showError('Please Select a File to Upload');
    //     }
    // });

    // $('input').on('change',function(e){
    //     checkFormStandard(e.target);
    // });

    // function checkFormStandard(element)
    // {
    //     let field_name = $(element).attr('name');
    //     let value      = $(element).val();

    //     $.ajax({
    //         url        : baseUrl + '/check_field_standard',
    //         data       : {field: field_name, value: value},
    //         method     : 'POST',
    //         success: function(response){
    //             try {
    //                 let parse_response = JSON.parse(response);
    //                 if(!parse_response.result){
    //                     if (parse_response.hasOwnProperty('data')) {
    //                         let err_message;
    //                         for (let row in parse_response.data) {
    //                             let fieldName   = row;
    //                             let rowData     = parse_response.data[row];
    //                             let field       = $(`#${field_name}_label`).removeClass('text-warning');
    //                             err_message = 'The Field has a Non Standard Value.';
    //                             field.addClass('text-warning');
    //                             if (rowData.err_message != '') {
    //                                 err_message = err_message + ' ' + rowData.err_message;
    //                             }
    //                         }
    //                         showWarning(err_message, 'normal', 4000);
    //                     } else {
    //                         showWarning(parse_response.message, 'normal', 4000);
    //                     }
    //                 } else {
    //                     $(`#${field_name}_label`).removeClass('text-warning');
    //                 }
    //             } catch (exception) {
    //                 console.log(exception);
    //                 showError('System Encountered an Error: please contact the system administrator.');
    //             }
    //         }
    //     });
    // }

    // let stepperFormEl = document.querySelector('#stepperForm');
    // if (stepperFormEl != null) {
    //     window.stepperForm = new Stepper(stepperFormEl, {
    //         animation: true
    //     });

    //     let btnNextList    = [].slice.call(document.querySelectorAll('.btn-next-form'));
    //     let btnPrevList    = [].slice.call(document.querySelectorAll('.btn-prev-form'));

    //     btnNextList.forEach(function (btn) {
    //         btn.addEventListener('click', function () {

    //             let currentTab = $(this).parents('.bs-stepper-pane');
    //             let fields     = $(currentTab).find(':required');
    //             let isValid    = true;
    //             var form       = document.getElementsByClassName('needs-validation')[0];

    //             $.each($(fields), function(){
    //                 if ($(this).val() == '' && $(this).attr('type') != 'checkbox') {
    //                     isValid = false;
    //                 } else if ($(this).attr('type') == 'checkbox' && !$(this).prop('checked'))  {
    //                     isValid = false;
    //                 }
    //             });

    //             if (isValid) {
    //                 form.classList.remove('was-validated');
    //                 document.getElementById('stepper-header').scrollLeft += 160;
    //                 saveAsDraft(false);
    //                 window.stepperForm.next();
    //             } else {
    //                 form.classList.add('was-validated');
    //             }
    //         });
    //     });

    //     btnPrevList.forEach(function (btn) {
    //         btn.addEventListener('click', function () {
    //             window.stepperForm.previous();
    //             document.getElementById('stepper-header').scrollLeft -= 160;
    //         });
    //     });
    // }

    // function addFormAddtlFields(element) {
    //     const form         = $(element);
    //     const url          = baseUrl + '/form-addtl-field/' + form.val();
    //     let   addtlELement = $(element).parents('form').find('#addtl_fields');
    //     $(addtlELement).load(url);
    // }

    //CDI Reports Page
    if ($('#cdireports').length ) {  

        //Table Filtering
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                let min = $('#filter-list-start-date').val();
                let max = $('#filter-list-end-date').val();
        
                if(max !== "") {
                    max = moment(max).endOf('day').format('YYYY-MM-DD HH:mm:ss');
                }
        
                let createdAt = data[6];
        
                if (
                    (min === "" || max === "" ) ||
                    (moment(createdAt).isSameOrAfter(min) && moment(createdAt).isSameOrBefore(max))
                ) {
                    return true;
                }
                return false;
            },
        );

        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                let storeId = $('#store-filter').val();

                let columnValue = data[7];
        
                if (storeId === "" || storeId == columnValue) {
                    return true;
                }
                return false;
            }
        );
        
        //Table Updating & Table Modification
        let table = $('.report-data-table').DataTable({
            searching: true,
            dom: 'rtlip',
            initComplete: function () {
                $('#filter-list-start-date, #filter-list-end-date').change( function() {
                    table.draw();
                });
            }
        });

        table.column(7).visible(false);

        $('#store-filter').change( function() {
            table.draw();
        });

        //Graph Updating
        $(document).ready(updateGraph);
        
        let debounceTimeout;
        $('#filter-list-start-date, #filter-list-end-date, #store-filter').change(function() {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(updateGraph, 300);
        });

        let ageChart, diningFrequencyChart, purchaseFrequencyChart, preferredFoodChart, serviceSatisfactionChart, cleanlinessSatisfactionChart, recommendationIntentChart;

        let genderChart, civilStatusChart, occupationChart;

        function updateGraph() {
            $.ajax({
                url: baseUrl + '/get_survey_graph_data/' + $('#cdireports').data('value'),
                method: 'POST',
                data: {
                    startDate: $('#filter-list-start-date').val(),
                    endDate: $('#filter-list-end-date').val(),
                    storeId: $('#store-filter').val()
                },
                success: function(response){

                    //serviceSatisfactionChart && cleanlinessSatisfactionChart
                    if (ageChart && diningFrequencyChart && purchaseFrequencyChart && preferredFoodChart && recommendationIntentChart) {
                        ageChart.destroy();
                        diningFrequencyChart.destroy();
                        purchaseFrequencyChart.destroy();
                        preferredFoodChart.destroy();
                        // serviceSatisfactionChart.destroy();
                        // cleanlinessSatisfactionChart.destroy();
                        recommendationIntentChart.destroy();
                    }  

                    //under testing and will be merged once a proper approach is finalized
                    // && civilStatus && occupation
                    if (genderChart && civilStatusChart && occupationChart) {
                        genderChart.destroy();
                        civilStatusChart.destroy();
                        occupationChart.destroy();
                    }

                    const responseData = JSON.parse(response);

                    const diningFrequencyData = responseData[1];

                    const preferredFoodData = responseData[2];

                    const purchaseFrequencyData = responseData[3];

                    const cleanlinessSatisfactionData = responseData[4];

                    const serviceSatisfactionData = responseData[5];

                    const recommendationIntentData = responseData[6];

                    const ageData = responseData[11];

                    const genderData = responseData[12];

                    const civilStatusData = responseData[13];

                    const occupationData = responseData[14];

                    if (occupationData && occupationData.response.hasOwnProperty("Blue Colar Job (Manual Laborers like construction, farmer, drivers, etc)")) {
                        occupationData.response["Blue Colar Job"] = occupationData.response["Blue Colar Job (Manual Laborers like construction, farmer, drivers, etc)"];
                        delete occupationData.response["Blue Colar Job (Manual Laborers like construction, farmer, drivers, etc)"];
                    }
                    
                    if (occupationData && occupationData.response.hasOwnProperty("White Colar Job (Office Jobs, Admin Jobs, Professional jobs like teacher, nurse, etc)")) {
                        occupationData.response["White Colar Job"] = occupationData.response["White Colar Job (Office Jobs, Admin Jobs, Professional jobs like teacher, nurse, etc)"];
                        delete occupationData.response["White Colar Job (Office Jobs, Admin Jobs, Professional jobs like teacher, nurse, etc)"];
                    }

                    //Age Chart - Start
                    const ctxAge = $('#age')[0].getContext('2d');
                    const data = {
                        labels: [],
                        datasets: [{
                            label: 'Age Group',
                            data: [],
                            backgroundColor: ['red', 'blue', 'yellow', 'green', 'purple', 'orange'],
                        }]
                    };
                    if (ageData) {
                        const responseArray = ageData.response;
                        const ageGroups = ['10 - 20 Years Old', '21 - 30 Years Old', '31 - 40 Years Old', '41 - 50 Years Old', '51 - 60 Years Old', '61 and Above'];
                        const dataValues = ageGroups.map(group => responseArray[group] || 0);

                        data.labels = ageGroups;
                        data.datasets[0].data = dataValues;
                    } else {
                        data.labels = ['Empty'];
                        data.datasets[0].data = [1];
                        data.datasets[0].backgroundColor = ['red'];
                    }

                    ageChart = new Chart(ctxAge, {
                        type: 'pie',
                        data: data,
                        options:{
                            responsive: true,
                        }
                    });
                    //Age Chart - End

                    //Gender Chart - Start
                    const ctxGender = $('#gender')[0].getContext('2d');
                    const dataGender = {
                        labels: [],
                        datasets: [{
                            label: 'Gender Group',
                            data: [],
                            backgroundColor: ['pink', 'blue'],
                        }]
                    };
                    if (genderData) {
                        const responseArray = genderData.response;
                        const gender = ['Female', 'Male'];
                        const dataValues = gender.map(group => responseArray[group] || 0);

                        dataGender.labels = gender;
                        dataGender.datasets[0].data = dataValues;
                    } else {
                        dataGender.labels = ['Empty'];
                        dataGender.datasets[0].data = [1];
                        dataGender.datasets[0].backgroundColor = ['red'];
                    }

                    genderChart = new Chart(ctxGender, {
                        type: 'pie',
                        data: dataGender,
                        options:{
                            responsive: true,
                        }
                    });
                    //Gender Chart - End

                    //Civil Status Chart - Start
                    const ctxCivilStatus = $('#civilstatus')[0].getContext('2d');
                    const dataCivilStatus = {
                        labels: [],
                        datasets: [{
                            label: 'Civil Status',
                            data: [],
                            backgroundColor: ['green', 'orange', 'purple'],
                        }]
                    };
                    if (civilStatusData) {
                        const responseArray = civilStatusData.response;
                        const civilStatus = ['Single', 'Married', 'Others'];
                        const dataValues = civilStatus.map(group => responseArray[group] || 0);

                        dataCivilStatus.labels = civilStatus;
                        dataCivilStatus.datasets[0].data = dataValues;
                    }
                    civilStatusChart = new Chart(ctxCivilStatus, {
                        type: 'pie',
                        data: dataCivilStatus,
                        options:{
                            responsive: true,
                        }
                    });
                    //Civil Status Chart - End

                    //Occupation Chart - Start
                    const ctxOccupation = $('#occupation')[0].getContext('2d');
                    const dataOccupation = {
                        labels: [],
                        datasets: [{
                            label: 'Occupation',
                            data: [],
                            backgroundColor: ['red', 'orange', 'purple', 'blue', 'yellow'],
                        }]
                    };
                    if (occupationData) {
                        const responseArray = occupationData.response;
                        const occupation = ['Student', 'BPO', 'White Colar Job', 'Blue Colar Job', 'Self employed'];
                        const dataValues = occupation.map(group => responseArray[group] || 0);

                        dataOccupation.labels = occupation;
                        dataOccupation.datasets[0].data = dataValues;
                    }
                    occupationChart = new Chart(ctxOccupation, {
                        type: 'pie',
                        data: dataOccupation,
                        options:{
                            responsive: true,
                        }
                    });
                    //Occupation Chart - End

                    //Preferred Food Chart - Start
                    const ctxDiningFrequency = $('#diningfrequency');
                    const dataDiningFrequency = {
                        labels: [],
                        datasets: [{
                            label: 'Dining Frequency',
                            data: [],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)'
                            ],
                            borderWidth: 1
                        }]
                    };

                    if (diningFrequencyData) {
                        const responseArray = diningFrequencyData.response;
                        const diningFrequencies = ['First Time', 'Wala pang isang taon', 'Mahigit isang taon'];
                        const dataValues = diningFrequencies.map(group => responseArray[group] || 0);
                        
                        dataDiningFrequency.labels = diningFrequencies;
                        dataDiningFrequency.datasets[0].data = dataValues;
                    } else {
                        dataDiningFrequency.labels = ['Empty'];
                        dataDiningFrequency.datasets[0].data = [1];
                        dataDiningFrequency.datasets[0].backgroundColor = ['red'];
                    }
                    diningFrequencyChart = new Chart(ctxDiningFrequency, {
                        type: 'bar',
                        data: dataDiningFrequency,
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false 
                                }
                            }
                        }
                    });
                    //Preferred Food Chart - End

                    //Preferred Food Chart - Start
                    const ctxPurchaseFrequency = $('#purchasefrequency');
                    const dataPurchaseFrequency = {
                        labels: [],
                        datasets: [{
                            label: 'Purchase Frequency',
                            data: [],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)'
                            ],
                            borderWidth: 1
                        }]
                    };
                    if (purchaseFrequencyData) {
                        const responseArray = purchaseFrequencyData.response;
                        const purchaseFrequencies = ['Araw-Araw', 'Linggo-Linggo', 'Isang Beses sa Isang Buwan', 'Pag may Okasyon o Selebrasyon', 'Others'];
                        const dataValues = purchaseFrequencies.map(group => responseArray[group] || 0);
                        
                        dataPurchaseFrequency.labels = purchaseFrequencies;
                        dataPurchaseFrequency.datasets[0].data = dataValues;
                    } else {
                        dataPurchaseFrequency.labels = ['Empty'];
                        dataPurchaseFrequency.datasets[0].data = [1];
                        dataPurchaseFrequency.datasets[0].backgroundColor = ['red'];
                    }
                    purchaseFrequencyChart = new Chart(ctxPurchaseFrequency, {
                        type: 'bar',
                        data: dataPurchaseFrequency,
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false 
                                }
                            }
                        }
                    });
                    //Preferred Food Chart - End

                    //Preferred Food Chart - Start
                    const ctxPreferredFood = $('#preferredfood');
                    const dataPreferredFood = {
                        labels: [],
                        datasets: [{
                            label: 'Preferred Food',
                            data: [],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)',
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)'
                            ],
                            borderWidth: 1
                        }]
                    };
                    if (preferredFoodData) {
                        const responseArray = preferredFoodData.response;
                        const preferredFoods = ['Chooks Pepper Roast Meal', 'Chooks Sweet Roast Meal', 'Chooks Hot and Spicy Roast Meal', 'Chooks Fried Chicken', 'Kangkong with Bagoong', 'Roasted Liempo', 'Beef Tapa', 'Pork Sisig', 'Burger Steak', 'Krispy Kare - Kare', 'Veggie Kare - Kare', 'Chicken Chicharon Kare - Kare', 'Spaghetti', 'Pancit Palabok', 'Mug Cake', 'French Fries', 'Chicken Chicharon'];
                        const dataValues = preferredFoods.map(group => responseArray[group] || 0);

                        dataPreferredFood.labels = preferredFoods;
                        dataPreferredFood.datasets[0].data = dataValues;
                    } else {
                        dataPreferredFood.labels = ['Empty'];
                        dataPreferredFood.datasets[0].data = [1];
                        dataPreferredFood.datasets[0].backgroundColor = ['red'];
                    }
                    preferredFoodChart = new Chart(ctxPreferredFood, {
                        type: 'radar',
                        data: dataPreferredFood,
                        options: {
                            responsive: true,
                            interaction: {
                            intersect: false,
                            },
                            scales: {
                                    r: {
                                        beginAtZero: true
                                    }
                                },
                            plugins: {
                                legend: {
                                    display: false 
                                }
                            }
                        }
                    });
                    //Preferred Food Chart - End
                    
                    //Service Satisfaction Chart - Start
                    const ctxServiceSatisfaction = $('#servicesatisfaction');
                    const dataServiceSatisfaction = {
                        labels: [],
                        datasets: [{
                            label: 'Service Satisfaction',
                            data: [],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                            ],
                            borderWidth: 1
                        }]
                    };
                    if (serviceSatisfactionData) {
                        const responseArray = serviceSatisfactionData.response;
                        const serviceSatisfactions = ['1', '2', '3', '4', '5'];
                        const dataValues = serviceSatisfactions.map(group => responseArray[group] || 0);

                        dataServiceSatisfaction.labels = serviceSatisfactions;
                        dataServiceSatisfaction.datasets[0].data = dataValues;
                    } else {
                        dataServiceSatisfaction.labels = ['Empty'];
                        dataServiceSatisfaction.datasets[0].data = [1];
                        dataServiceSatisfaction.datasets[0].backgroundColor = ['red'];
                    }
                    // serviceSatisfactionChart = new Chart(ctxServiceSatisfaction, {
                    //     type: 'bar',
                    //     data: dataServiceSatisfaction,
                    //     options: {
                    //         indexAxis: 'y',
                    //         plugins: {
                    //             legend: {
                    //                 display: false 
                    //             }
                    //         }
                    //     }
                    // });
                    //Service Satisfaction Chart - End

                    //Cleanliness Satisfaction Chart - Start
                    const ctxCleanlinessSatisfaction = $('#cleanlinesssatisfaction');
                    const dataCleanlinessSatisfaction = {
                        labels: [],
                        datasets: [{
                            label: 'Cleanliness Satisfaction',
                            data: [],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                            ],
                            borderWidth: 1
                        }]
                    };
                    if (cleanlinessSatisfactionData) {
                        const responseArray = cleanlinessSatisfactionData.response;
                        const cleanlinessSatisfactions = ['1', '2', '3', '4', '5'];
                        const dataValues = cleanlinessSatisfactions.map(group => responseArray[group] || 0);

                        dataCleanlinessSatisfaction.labels = cleanlinessSatisfactions;
                        dataCleanlinessSatisfaction.datasets[0].data = dataValues;
                    } else {
                        dataCleanlinessSatisfaction.labels = ['Empty'];
                        dataCleanlinessSatisfaction.datasets[0].data = [1];
                        dataCleanlinessSatisfaction.datasets[0].backgroundColor = ['red'];
                    }
                    // cleanlinessSatisfactionChart = new Chart(ctxCleanlinessSatisfaction, {
                    //     type: 'bar',
                    //     data: dataCleanlinessSatisfaction,
                    //     options: {
                    //         indexAxis: 'y',
                    //         plugins: {
                    //             legend: {
                    //                 display: false 
                    //             }
                    //         }
                    //     }
                    // });
                    //Cleanliness Satisfaction Chart - End

                    //Recommendation Intent Chart - Start
                    const ctxRecommendationIntent = $('#recommendationintent');
                    const dataRecommendationIntent = {
                        labels: [],
                        datasets: [{
                            label: 'Will Recommend Chooks to Go?',
                            data: [],
                            backgroundColor: [
                                'rgba(0, 255, 0, 0.2)',
                                'rgba(255, 99, 132, 0.2)',
                            ],
                            borderColor: [
                                'rgba(00, 255, 0, 1)',
                                'rgba(255, 99, 132, 1)'
                            ],
                            borderWidth: 1
                        }]
                    };
                    if (recommendationIntentData) {
                        const responseArray = recommendationIntentData.response;
                        const recommendationIntents = ['Oo', 'Hindi'];
                        const dataValues = recommendationIntents.map(group => responseArray[group] || 0);

                        dataRecommendationIntent.labels = recommendationIntents;
                        dataRecommendationIntent.datasets[0].data = dataValues;
                    } else {
                        dataRecommendationIntent.labels = ['Empty'];
                        dataRecommendationIntent.datasets[0].data = [1];
                        dataRecommendationIntent.datasets[0].backgroundColor = ['red'];
                    }
                    recommendationIntentChart = new Chart(ctxRecommendationIntent, {
                        type: 'bar',
                        data: dataRecommendationIntent,
                        options: {
                            responsive: true,
                            interaction: {
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                    //Recommendation Intent Chart - End
                }
            });
        }
    }
    //End of CDI Reports Page

    //CTG Reports Page
    if ($('#ctgreports').length ) {  

        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                let min = $('#filter-list-start-date').val();
                let max = $('#filter-list-end-date').val();
        
                if(max !== "") {
                    max = moment(max).endOf('day').format('YYYY-MM-DD HH:mm:ss');
                }
        
                let createdAt = data[5];
        
                if (
                    (min === "" || max === "" ) ||
                    (moment(createdAt).isSameOrAfter(min) && moment(createdAt).isSameOrBefore(max))
                ) {
                    return true;
                }
                return false;
            },
        );
    
        let table = $('.report-data-table').DataTable({
            searching: true,
            dom: 'rtlip',
            initComplete: function () {
                // Re-draw the table when the start_date or end_date changes
                $('#filter-list-start-date, #filter-list-end-date').change( function() {
                    table.draw();
                });
            }
        });

         //method check
        let ageChart, diningFrequencyChart, purchaseFrequencyChart, preferredFoodChart, serviceSatisfactionChart, favoriteFlavorChart, recommendationIntentChart;
        function updateGraph() {
            $.ajax({
                url: baseUrl + '/get_survey_graph_data/' + $('#ctgreports').data('value'),
                method: 'POST',
                data: {
                    startDate: $('#filter-list-start-date').val(),
                    endDate: $('#filter-list-end-date').val(),
                    // search: $('#filter-search').val()
                },
                success: function(response){

                    if (ageChart && diningFrequencyChart && purchaseFrequencyChart && preferredFoodChart && serviceSatisfactionChart && favoriteFlavorChart && recommendationIntentChart) {
                        ageChart.destroy();
                        diningFrequencyChart.destroy();
                        purchaseFrequencyChart.destroy();
                        preferredFoodChart.destroy();
                        serviceSatisfactionChart.destroy();
                        favoriteFlavorChart.destroy();
                        recommendationIntentChart.destroy();
                    }

                    const responseData = JSON.parse(response);

                    const diningFrequencyData = responseData[29];

                    const favoriteFlavorChartData = responseData[30];

                    const purchaseFrequencyData = responseData[31];

                    const preferredFoodData = responseData[32];

                    const serviceSatisfactionData = responseData[33];

                    const recommendationIntentData = responseData[34];

                    const ageData = responseData[39];

                    //Age Chart - Start
                    const ctxAge = $('#age')[0].getContext('2d');
                    const data = {
                        labels: [],
                        datasets: [{
                            label: 'Age Group',
                            data: [],
                            backgroundColor: ['red', 'blue', 'yellow', 'green', 'purple', 'orange'],
                        }]
                    };
                    if (ageData) {
                        const responseArray = ageData.response;
                        const ageGroups = ['10 - 20 Years Old', '21 - 30 Years Old', '31 - 40 Years Old', '41 - 50 Years Old', '51 - 60 Years Old', '61 and Above'];
                        const dataValues = ageGroups.map(group => responseArray[group] || 0);

                        data.labels = ageGroups;
                        data.datasets[0].data = dataValues;
                    } else {
                        data.labels = ['Empty'];
                        data.datasets[0].data = [1];
                        data.datasets[0].backgroundColor = ['red'];
                    }

                    ageChart = new Chart(ctxAge, {
                        type: 'pie',
                        data: data,
                        options:{
                            responsive: true,
                            maintainAspectRatio: false
                        }
    
                    });
                    //Age Chart - End

                    //Preferred Food Chart - Start
                    const ctxDiningFrequency = $('#diningfrequency');
                    const dataDiningFrequency = {
                        labels: [],
                        datasets: [{
                            label: 'Dining Frequency',
                            data: [],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)'
                            ],
                            borderWidth: 1
                        }]
                    };

                    if (diningFrequencyData) {
                        const responseArray = diningFrequencyData.response;
                        const diningFrequencies = ['First Time', 'Wala pang isang taon', 'Mahigit isang taon'];
                        const dataValues = diningFrequencies.map(group => responseArray[group] || 0);
                        
                        dataDiningFrequency.labels = diningFrequencies;
                        dataDiningFrequency.datasets[0].data = dataValues;
                    } else {
                        dataDiningFrequency.labels = ['Empty'];
                        dataDiningFrequency.datasets[0].data = [1];
                        dataDiningFrequency.datasets[0].backgroundColor = ['red'];
                    }
                    diningFrequencyChart = new Chart(ctxDiningFrequency, {
                        type: 'bar',
                        data: dataDiningFrequency,
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false 
                                }
                            }
                        }
                    });
                    //Preferred Food Chart - End

                    //Preferred Food Chart - Start
                    const ctxPurchaseFrequency = $('#purchasefrequency');
                    const dataPurchaseFrequency = {
                        labels: [],
                        datasets: [{
                            label: 'Purchase Frequency',
                            data: [],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)'
                            ],
                            borderWidth: 1
                        }]
                    };
                    if (purchaseFrequencyData) {
                        const responseArray = purchaseFrequencyData.response;
                        const purchaseFrequencies = ['Araw-Araw', 'Linggo-Linggo', 'Isang Beses sa Isang Buwan', 'Pag may Okasyon o Selebrasyon'];
                        const dataValues = purchaseFrequencies.map(group => responseArray[group] || 0);
                        
                        dataPurchaseFrequency.labels = purchaseFrequencies;
                        dataPurchaseFrequency.datasets[0].data = dataValues;
                    } else {
                        dataPurchaseFrequency.labels = ['Empty'];
                        dataPurchaseFrequency.datasets[0].data = [1];
                        dataPurchaseFrequency.datasets[0].backgroundColor = ['red'];
                    }
                    purchaseFrequencyChart = new Chart(ctxPurchaseFrequency, {
                        type: 'bar',
                        data: dataPurchaseFrequency,
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false 
                                }
                            }
                        }
                    });
                    //Preferred Food Chart - End

                    //Preferred Food Chart - Start
                    const ctxPreferredFood = $('#preferredfood');
                    const dataPreferredFood = {
                        labels: [],
                        datasets: [{
                            label: 'Preferred Food',
                            data: [],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)',
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)'
                            ],
                            borderWidth: 1
                        }]
                    };
                    if (preferredFoodData) {
                        const responseArray = preferredFoodData.response;
                        const preferredFoods = ['Roasted Liempo', 'Liempo Sisig', 'Fried Chicken', 'Spicy Neck', 'Marinated Chicken Cut-ups ( Sweet )', 'Marinated Chicken Cut-ups ( Pepper )', 'Marinated Chicken Cut-ups ( Spicy )', 'Marinated Fried Chicken ( Original )', 'Marinated Fried Chicken ( Buffalo Glaze )', 'Marinated Fried Chicken ( Spicy )', 'Chicken Nuggets'];
                        const dataValues = preferredFoods.map(group => responseArray[group] || 0);

                        dataPreferredFood.labels = preferredFoods;
                        dataPreferredFood.datasets[0].data = dataValues;
                    } else {
                        dataPreferredFood.labels = ['Empty'];
                        dataPreferredFood.datasets[0].data = [1];
                        dataPreferredFood.datasets[0].backgroundColor = ['red'];
                    }
                    preferredFoodChart = new Chart(ctxPreferredFood, {
                        type: 'radar',
                        data: dataPreferredFood,
                        options: {
                            responsive: true,
                            interaction: {
                            intersect: false,
                            },
                            scales: {
                                    r: {
                                        beginAtZero: true
                                    }
                                },
                            plugins: {
                                legend: {
                                    display: false 
                                }
                            }
                        }
                    });
                    //Preferred Food Chart - End
                    
                    //Service Satisfaction Chart - Start
                    const ctxServiceSatisfaction = $('#servicesatisfaction');
                    const dataServiceSatisfaction = {
                        labels: [],
                        datasets: [{
                            label: 'Service Satisfaction',
                            data: [],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                            ],
                            borderWidth: 1
                        }]
                    };
                    if (serviceSatisfactionData) {
                        const responseArray = serviceSatisfactionData.response;
                        const serviceSatisfactions = ['1', '2', '3', '4', '5'];
                        const dataValues = serviceSatisfactions.map(group => responseArray[group] || 0);

                        dataServiceSatisfaction.labels = serviceSatisfactions;
                        dataServiceSatisfaction.datasets[0].data = dataValues;
                    } else {
                        dataServiceSatisfaction.labels = ['Empty'];
                        dataServiceSatisfaction.datasets[0].data = [1];
                        dataServiceSatisfaction.datasets[0].backgroundColor = ['red'];
                    }
                    serviceSatisfactionChart = new Chart(ctxServiceSatisfaction, {
                        type: 'bar',
                        data: dataServiceSatisfaction,
                        options: {
                            indexAxis: 'y',
                            plugins: {
                                legend: {
                                    display: false 
                                }
                            }
                        }
                    });
                    //Service Satisfaction Chart - End

                    //Favorite Flavor Chart - Start
                    const ctxFavoriteFlavor = $('#favoriteflavor');
                    const dataFavoriteFlavor = {
                        labels: [],
                        datasets: [{
                            label: 'Favorite Flavor',
                            data: [],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)'
                            ],
                            borderWidth: 1
                        }]
                    };
                    if (favoriteFlavorChartData) {
                        
                        const responseArray = favoriteFlavorChartData.response;
                        const favoriteFlavors = ['Sweet Roast', 'Pepper Roast', 'Hot and Spicy'];
                        const dataValues = favoriteFlavors.map(group => responseArray[group] || 0);

                        dataFavoriteFlavor.labels = favoriteFlavors;
                        dataFavoriteFlavor.datasets[0].data = dataValues;
                    } else {
                        dataFavoriteFlavor.labels = ['Empty'];
                        dataFavoriteFlavor.datasets[0].data = [1];
                        dataFavoriteFlavor.datasets[0].backgroundColor = ['red'];
                    }
                    favoriteFlavorChart = new Chart(ctxFavoriteFlavor, {
                        type: 'bar',
                        data: dataFavoriteFlavor,
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false 
                                }
                            }
                        }
                    });
                    //Favorite Flavor Chart - End

                    //Recommendation Intent Chart - Start
                    const ctxRecommendationIntent = $('#recommendationintent');
                    const dataRecommendationIntent = {
                        labels: [],
                        datasets: [{
                            label: 'Will Recommend Chooks to Go?',
                            data: [],
                            backgroundColor: [
                                'rgba(0, 255, 0, 0.2)',
                                'rgba(255, 99, 132, 0.2)',
                            ],
                            borderColor: [
                                'rgba(00, 255, 0, 1)',
                                'rgba(255, 99, 132, 1)'
                            ],
                            borderWidth: 1
                        }]
                    };
                    if (recommendationIntentData) {
                        const responseArray = recommendationIntentData.response;
                        const recommendationIntents = ['Oo', 'Hindi'];
                        const dataValues = recommendationIntents.map(group => responseArray[group] || 0);

                        dataRecommendationIntent.labels = recommendationIntents;
                        dataRecommendationIntent.datasets[0].data = dataValues;
                    } else {
                        dataRecommendationIntent.labels = ['Empty'];
                        dataRecommendationIntent.datasets[0].data = [1];
                        dataRecommendationIntent.datasets[0].backgroundColor = ['red'];
                    }
                    recommendationIntentChart = new Chart(ctxRecommendationIntent, {
                        type: 'bar',
                        data: dataRecommendationIntent,
                        options: {
                            responsive: true,
                            interaction: {
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                    //Recommendation Intent Chart - End
                }
            });
        }
        
        $(document).ready(updateGraph);
        
        let debounceTimeout;
        $('#filter-list-start-date, #filter-list-end-date').change(function() {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(updateGraph, 300);
        });
        
    }
    //End of CTG Reports Page

    //js bookmark
    //---------------------------------------------------//
    const form = document.querySelector('#form');

    let requestUrl;
    let option;
    let data;

    if(form){
        
        form.addEventListener('change', function(event){
            if(event.target.id == 'is_required'){
                const value = event.target.checked ? 1 : 0;
                event.target.previousElementSibling.setAttribute('value', value)
            }
        })

        
        let sectionCounter = 1;
        let fieldCounter = 1;

        // If Form has previous data, it will sync the section Counter based on the current amount of existing sections.
        const form_content = form.querySelector("#form_content")
        
            const currentSectionCounter = form_content.lastElementChild    
            if(currentSectionCounter){
                sectionCounter = currentSectionCounter.getAttribute('section')
                sectionCounter++
            }

            if(currentSectionCounter){    
                const field_content = currentSectionCounter.lastElementChild 

                const currentFieldCounter = field_content.lastElementChild
                if(currentFieldCounter){
                    fieldCounter = currentFieldCounter.getAttribute('field')
                    fieldCounter++
                }
            }
        

        //Form Functionality - Element Add & Delete
        form.addEventListener('click', function(event){
            //every code inside the event listener will reset every click. Do Not Forget
            if(event.target.tagName === 'BUTTON' ){
               
                if(event.target.id === 'add-section' ){ 
                    data = sectionCounter;

                    sectionCounter++

                    requestUrl = (baseUrl + '/process_add_section')
                }
                else if(event.target.id === 'add-field'){
                    let currentElement = event.target;

                    while(currentElement.tagName !== 'SECTION'){
                        currentElement = currentElement.parentElement;
                    } 

                    let identifiers=[currentElement.getAttribute('section'), fieldCounter];
                    data = JSON.stringify(identifiers);

                    fieldCounter++;

                    requestUrl = (baseUrl + '/process_add_field')
                }
                else if(event.target.id === 'add-option'){
                    let currentElement = event.target;

                    while(currentElement.id !== 'field'){
                        currentElement = currentElement.parentElement;
                    } 
                    data = currentElement.getAttribute('field');

                    requestUrl = (baseUrl + '/process_add_option')
                }
                else if(event.target.id === 'delete'){
                    
                    let currentElement = event.target;
                    
                    while(currentElement.id !== 'option' && currentElement.id !== 'field' && currentElement.tagName !== 'SECTION'){
                        currentElement = currentElement.parentElement;
                    } 
                    currentElement.remove();
                } 
                option = {
                    method: 'POST',
                    body: data
                }
                request(event);
                }
            })     

            form.addEventListener('change', function(event){
                if(event.target.tagName === 'SELECT'){

                    data = event.target.value;
                
                    option = {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'text/plain'
                        },
                        body: data
                    }
                    requestUrl = (baseUrl + '/process_add_field_content')
                }
                request(event);

                if (event.target.value == 3 || event.target.value == 4 || event.target.value == 5) {
                    setTimeout(() => {
                        let currElement = event.target

                        while(currElement.id !== 'field'){
                        currElement = currElement.parentElement
                        }

                        const id = currElement.getAttribute('field');
                        const defOption = currElement.querySelectorAll('input.defaultoption');

                        defOption.forEach((curOption) => {
                            curOption.setAttribute('value', id )
                        });
                    }, "500");
                    
                }
    
            })
        
            function request(event){
                if(requestUrl){
                    fetch(requestUrl, option)
                    
                    .then(response => {
                        if (response.status == 200) {
                        
                        }
                        return response.text();
                    })  
                    .then (response => {
                        if(event.target.id === 'add-section' ){
                            document.getElementById('form_content').insertAdjacentHTML('beforeend', response);
                        }
                        else if(event.target.id === 'add-field'){

                            let currentElement = event.target;

                            while(currentElement.tagName !== 'SECTION'){
                                currentElement = currentElement.parentElement;
                            } 
                            currentElement.querySelector('#field-container').insertAdjacentHTML('beforeend', response);
                        }
                        else if(event.target.id === 'add-option'){

                            let currentElement = event.target;

                            while(currentElement.id !== 'field'){
                                currentElement = currentElement.parentElement;
                            } 
                            currentElement.querySelector('#option-container').insertAdjacentHTML('beforeend', response);
                        }
                        else if(event.target.tagName === 'SELECT'){
                            let currentElement = event.target;
                        
                            while(currentElement.id !== 'field'){
                                currentElement = currentElement.parentElement;
                            } 
                            currentElement.querySelector('#field-content-container').innerHTML = response;
                        }                         
                    })
                    requestUrl = null;
                    option = null;
                    data = null;
                }

        }
    }
    
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
    
    //Transaction List Redeem Ajax
    const table = document.getElementById('main-content-table');

    if(table){
        table.addEventListener('click', function(event) {
            if (event.target.matches('.redeem-button')){
                event.preventDefault();

                const id = event.target.getAttribute('id');
                const count = event.target.getAttribute('count');

                // console.log(id);
                // return;

                $('#confirmationModal').modal('show');

                const oldConfirmButton = document.getElementById('confirmButton');
                const newConfirmButton = oldConfirmButton.cloneNode(true);
                oldConfirmButton.parentNode.replaceChild(newConfirmButton, oldConfirmButton);

                // if (confirm('Are you sure you want to redeem this item?')) {

                newConfirmButton.addEventListener('click', function() {
                    

                    fetch(baseUrl + '/redeem_voucher', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: id }),
                    })
                    .then(response => response.text())
                    .then(data => {
                        
                        const parseData = JSON.parse(data);

                        if(parseData.result){

                            showSuccess(parseData.message);
                            event.target.remove();

                            const badge = table.querySelector(`#b-${count}`);
                            badge.classList.replace('badge-success', 'badge-danger');
                            badge.textContent = 'Redeemed';

                        }else{
                            showError(parseData.message);
                            setTimeout(function() { 
                                if (parseData.refresh) {
                                    location.reload();
                                }
                            }, 1500);
                        }
                    })
                    $('#confirmationModal').modal('hide');
                });   
            }
        });
    }

    //Transaction List Disable Ajax
    if(table){
        table.addEventListener('click', function(event) {
            if (event.target.matches('.disable-button')){
                event.preventDefault();

                const id = event.target.getAttribute('id');
                const count = event.target.getAttribute('count');

                $('#disableModal').modal('show');

                const oldConfirmButton = document.getElementById('disableButton');
                const newConfirmButton = oldConfirmButton.cloneNode(true);
                oldConfirmButton.parentNode.replaceChild(newConfirmButton, oldConfirmButton);

                newConfirmButton.addEventListener('click', function() {
                    fetch(baseUrl + '/disable_survey_entry', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: id }),
                    })
                    .then(response => response.text())
                    .then(data => {
                        
                        const parseData = JSON.parse(data);

                        if(parseData.result){

                            showSuccess(parseData.message);
                            event.target.remove();

                            const badge = table.querySelector(`#b-${count}`);
                            const redeemButton = table.querySelector(`[count='${count}']`);

                            redeemButton.remove();
                            
                            badge.classList.replace('badge-success', 'badge-danger');
                            badge.textContent = 'Disabled';

                        }else{
                            showError(parseData.message);
                            setTimeout(function() { 
                                if (parseData.refresh) {
                                    location.reload();
                                }
                            }, 1500);
                        }
                    })
                    $('#disableModal').modal('hide');
                });   
            }
        });
    }

    const pdfButton = document.querySelector('button.pdf-button');

    
    if(pdfButton){
        const id = pdfButton.getAttribute('id');

        pdfButton.addEventListener('click', function(event){
            event.preventDefault();
            
            fetch(baseUrl + '/get_pdf_url',{
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id }),
            })
            .then(response => response.text())
            .then(data => {
                // console.log(data);
                window.open(data);
            })
            .catch(error => console.error('Error:', error));
        });
    }

    $('.overlay__outer').attr('id', 'loaded__overlay');

});



