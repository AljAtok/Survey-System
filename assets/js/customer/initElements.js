$(function() {
    // Initialize Generic Select2
    let select2Elements = $('.select2');
    console.log(select2Elements);
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