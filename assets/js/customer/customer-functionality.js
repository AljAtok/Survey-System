$(function() {
    //Others functionality for CHECKBOX & MULTIPLE CHOICE
    $(function() {
        $('[data-belongs-to]').each(function() {
            const container = $(this).closest('.form-check');
            const othersTxtInput = $(this);
            const othersBtnInput = container.find('.form-check-input');
    
            // Handle checkboxes
            if (othersBtnInput.attr('type') === 'checkbox') {
                othersBtnInput.on('change', function() {
                    if ($(this).prop('checked')) {
                        othersTxtInput.prop('disabled', false);
                    } else {
                        othersTxtInput.prop('disabled', true);
                    }
                });
            }
    
            // Handle radio buttons
            if (othersBtnInput.attr('type') === 'radio') {
                const radioGroupName = othersBtnInput.attr('name');
                $(`input[name="${radioGroupName}"]`).on('change', function() {
                    if (othersBtnInput.prop('checked')) {
                        othersTxtInput.prop('disabled', false);
                    } else {
                        othersTxtInput.prop('disabled', true);
                    }
                });
            }
    
            othersTxtInput.on('keyup', function() {
                othersBtnInput.val($(this).val());
            });
        });
    });
    //Custom Rule Functions
    function disableInputs(container) {
        container.find('input, textarea, select').not('[data-belongs-to]').prop('disabled', true);
    }
    
    function enableInputs(container) {
        container.find('input, textarea, select').not('[data-belongs-to]').prop('disabled', false);
    }

    //Custom Rules
    const babalikKaPaBa = $('[for*="Babalik ka pa ba"]').closest('.form-group');

    const bakitKaBabalik = $('[for*="Bakit ka babalik"]').closest('.form-group');
    const bakitHindiKaBabalik = $('[for*="Bakit hindi"]').closest('.form-group');

    bakitKaBabalik.toggleClass('d-none');
    bakitHindiKaBabalik.toggleClass('d-none');
    disableInputs(bakitKaBabalik);
    disableInputs(bakitHindiKaBabalik);
    
    babalikKaPaBa.find('select').on('change', function() {
        if ($(this).val() == 'Oo') {
            bakitKaBabalik.removeClass('d-none');
            bakitHindiKaBabalik.addClass('d-none');
            enableInputs(bakitKaBabalik);
            disableInputs(bakitHindiKaBabalik);
        } else {
            bakitKaBabalik.addClass('d-none');
            bakitHindiKaBabalik.removeClass('d-none');
            disableInputs(bakitKaBabalik);
            enableInputs(bakitHindiKaBabalik);
        }
    });

    const gaanoKatagal = $('[for*="Gaano katagal ka nang customer ng Chooks"]').closest('.form-group');

    const paraSaFirstTime = $('[for*="Para sa first time customer"]').closest('.form-group');
    const paraSaReturning = $('[for*="Para sa returning customer"]').closest('.form-group');
    const gaanoKadalas = $('[for*="Gaano kadalas"]').closest('.form-group');

    paraSaFirstTime.toggleClass('d-none');
    paraSaReturning.toggleClass('d-none');
    gaanoKadalas.toggleClass('d-none');
    disableInputs(paraSaFirstTime);
    disableInputs(paraSaReturning);
    disableInputs(gaanoKadalas);

    gaanoKatagal.find('select').on('change', function() {
        if ($(this).val() == 'First Time') {
            paraSaFirstTime.removeClass('d-none');
            paraSaReturning.addClass('d-none');
            gaanoKadalas.addClass('d-none');
            enableInputs(paraSaFirstTime);
            disableInputs(paraSaReturning);
            disableInputs(gaanoKadalas);
        } else {
            paraSaFirstTime.addClass('d-none');
            paraSaReturning.removeClass('d-none');
            gaanoKadalas.removeClass('d-none');
            disableInputs(paraSaFirstTime);
            enableInputs(paraSaReturning);
            enableInputs(gaanoKadalas);
        }
    });




});