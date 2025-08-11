const baseUrl  = $("#base_url").val();

$(function() {
    // Initialize Generic Datapicker
    let dateElements = $('.datepicker');
    dateElements.each(function() {
        let $configuration = $(this).data('picker-config');

        if (typeof $configuration === 'string') {
            $configuration = JSON.parse($configuration);
        }

        flatpickr(this, $configuration);
    });

    // Initialize Generic DataTable
    let tableElements = $('.data-table');
    tableElements.each(function() {
        let $configuration = $(this).data('table-config');

        if (typeof $configuration === 'string') {
            $configuration = JSON.parse($configuration);
        }

        $(this).DataTable($configuration);
    });

    // Initialize Generic Select2
    let select2Elements = $('.select2');
    select2Elements.each(function() {
        let $configuration = $(this).data('select2-config');

        if (typeof $configuration === 'string') {
            $configuration = JSON.parse($configuration);
        }

        $(this).select2({
            theme: 'bootstrap-5',
            ...$configuration
        });
    });


});