$(function() {
    console.log('form_content_page.js');

    const createFormButton = $('.create-form');

    if (createFormButton.length) {
        createFormButton.on('click', function () {
            // console.log('createFormButton');
            // return;
            Swal.fire({
                title: 'Create Form',
                input: 'text',
                inputLabel: 'Form Name',
                inputPlaceholder: 'Enter the form name',
                showCancelButton: true,
                confirmButtonText: 'Create',
                preConfirm: (name) => {
                    if (!name) {
                        Swal.showValidationMessage('Form name is required');
                    }
                    return name;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formName = result.value;
    
                    $.ajax({
                        url: baseUrl + '/add_form',
                        method: 'POST',
                        data: { name: formName },
                        success: function (response) {
                            Swal.fire('Success', response.message, 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        },
                        error: function (xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                Swal.fire('Error', xhr.responseJSON.message, 'error');

                            } else {
                                Swal.fire('Error', 'An unexpected error occurred.', 'error');
                            }
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        }
                    });
                }
            });
        });
    }

    // const table = $('.data-table');
    // console.log(table);

    // return;

    // if (table.length) {
    //     table.on('click', '.redeem-button', function(event) {
    //         event.preventDefault();

    //         const id = $(this).attr('id');

    //         Swal.fire({
    //             title: 'Are you sure?',
    //             text: "You won't be able to revert this!",
    //             icon: 'warning',
    //             showCancelButton: true,
    //             confirmButtonColor: '#3085d6',
    //             cancelButtonColor: '#d33',
    //             confirmButtonText: 'Yes, redeem it!'
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 $.ajax({
    //                     url: baseUrl + '/redeem_voucher',
    //                     type: 'POST',
    //                     contentType: 'application/json',
    //                     data: JSON.stringify({ id: id }),
    //                     success: function(data) {
    //                         const parseData = JSON.parse(data);

    //                         if (parseData.result) {
    //                             Swal.fire({
    //                                 icon: 'success',
    //                                 title: 'Success',
    //                                 text: parseData.message,
    //                             });
    //                             cdiSurveyTable.length ? cdiSurveyTable.DataTable().ajax.reload() : '';
    //                             ctgSurveyTable.length ? ctgSurveyTable.DataTable().ajax.reload() : '';

    //                         } else {
    //                             Swal.fire({
    //                                 icon: 'error',
    //                                 title: 'Error',
    //                                 text: parseData.message,
    //                             });
    //                             setTimeout(function() { 
    //                                 if (parseData.refresh) {
    //                                     location.reload();
    //                                 }
    //                             }, 1500);
    //                         }
    //                     }
    //                 });
    //             }
    //         });
    //     });
    // }

});