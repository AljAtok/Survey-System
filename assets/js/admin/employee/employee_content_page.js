$(function() {
    function initializeDataTable(tableElement, columnsConfig) {
        let url = tableElement.data('url');

        tableElement.DataTable({
            serverSide: true,
            ordering: false,
            ajax: {
                url: url,
                type: 'GET',
                dataSrc: 'data'
            },
            columns: columnsConfig,
        });
    }

    const employeeTableElement = $('.employee-table');

    if (employeeTableElement.length) {
        initializeDataTable(employeeTableElement, [
            { data: 'action' },
            { data: 'employee_no' },
            { data: 'name' },
            { data: 'unit_name' },
            { data: 'location_name' },
            { data: 'employee_email' },
            { data: 'employee_contact' },
            { data: 'employment_type_name' },
            { data: 'employee_type_name' },
            { data: 'badge' },
        ]);
    }

    $('.table-container').on('click', '.employee-btn', function() {
        let button = $(this);
        let component = button.data('component');
        let title = button.data('component').replaceAll('_', ' ');
        let url = button.data('url');

        let employee_id = button.data('id');

        $.ajax({
            url: baseUrl + '/load_employee_modal',
            type: 'GET',
            data: {
                component: component,
                employee_id: employee_id
            },
            success: function(response) {
                Swal.fire({
                    title: title,
                    html: response,
                    showCancelButton: true,
                    showConfirmButton: true,
                    confirmButtonText: 'Submit',
                    width: '600px',
                    didOpen: () => {
                        let select2Elements = $('.select2');
                        select2Elements.each(function() {
                            $(this).select2({
                                theme: 'bootstrap-5',
                                dropdownParent: $('.swal2-container')
                            });
                        });
                    },
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            const form = $('.form');
                            form.find('.is-invalid').addClass('is-valid').removeClass('is-invalid');
                            form.find('.invalid-tooltip').remove();
  
                            $.ajax({
                                url: baseUrl + url,
                                type: 'POST',
                                data: form.serialize(),
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Action Successful',
                                        text: response.message,
                                    });

                                    employeeTableElement.DataTable().ajax.reload();
                                    resolve();
                                },
                                error: function(xhr) {
                                    let errors = xhr.responseJSON.errors;

                                    for (const [key, value] of Object.entries(errors)) {

                                        form.find(`[name="${key}"]`).addClass('is-invalid');
                                        form.find(`[name="${key}"]`).after(`<div class="invalid-tooltip">${value}</div>`);

                                    }

                                    Swal.enableButtons();
                                }
                            });
                        });
                    }
                });
            },
            error: function(xhr, status, error) {

            }
        });

    });

    $('.table-container').on('click', '.toggle', function() {
        let button = $(this);
        let employee_id = button.data('id');
        let title = button.hasClass('text-warning') ? 'Activate Employee' : 'Deactivate Employee';
        let message = button.hasClass('text-warning') ? 'Employee has been activated' : 'Employee has been deactivated';

        Swal.fire({
            title: title,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: baseUrl + '/toggle_employee_status',
                    type: 'POST',
                    data: {
                        id: employee_id
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: message,
                        });
                        employeeTableElement.DataTable().ajax.reload();
                    },
                    error: function(xhr, status, error) {
        
                    }
                });
            }
        });


    });

});