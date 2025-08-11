$(function() {
    const form = $('.form-content-container');

    //Minimize and maximize functionality for sections and fields
    $(form).on('click', '.toggle-section', function() {
        var section = $(this).closest('section');
        var fieldContainer = section.find('.section-inner-content');
        fieldContainer.slideToggle(300); // 300ms animation duration
        var icon = $(this).find('i');
        icon.toggleClass('fa-chevron-up fa-chevron-down');
    });

    $(form).on('click', '.toggle-field',function() {
        var field = $(this).closest('.form-group');
        var optionContainer = field.find('.field-lower-content');
        optionContainer.slideToggle(300); // 300ms animation duration
        var icon = $(this).find('i');
        icon.toggleClass('fa-chevron-up fa-chevron-down');
    });

    //form-content
    //form-section
    //form-group
    //form-option
    
    const formId =  $('.form-content').data('form-id');

    //Add Section
    $(form).on('click', '.add-section', function() {
        $.ajax({
            url: baseUrl + '/add_section',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                form_id: formId
            }),
            success: function(data) {
                data = JSON.parse(data);

                if (data.result) {
                    $('.form-content').append(data.section_content);
                } else {
                    console.error('Error adding section');
                }
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    });

    //Add Field
    $(form).on('click', '.add-field', function() {
        let section_id = $(this).closest('.form-section').data('section-id');
        $.ajax({
            url: baseUrl + '/add_field',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                section_id: section_id
            }),
            success: function(data) {
                data = JSON.parse(data);

                if (data.result) {
                    $(form).find('.form-section[data-section-id="' + section_id + '"] .section-inner-content').append(data.field_content);
                } else {
                    console.error('Error adding field');
                }
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });

    });

    //Add Option
    $(form).on('click', '.add-option', function() {
        let field_id = $(this).closest('.form-group').data('field-id');
        $.ajax({
            url: baseUrl + '/add_option',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                field_id: field_id
            }),
            success: function(data) {
                data = JSON.parse(data);

                if (data.result) {
                    let optionContainer = $(form).find('.form-group[data-field-id="' + field_id + '"] .option-container');
                    optionContainer.children().last().before(data.option_content);
                } else {
                    console.error('Error adding option');
                }
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    });

    //Update Form
    function updateForm(element) {
        let form = element.closest('.form-content');
        let form_name = form.find('[name="form_name"]').val();
        let form_description = form.find('[name="form_description"]').val(); //not implemented yet

        $.ajax({
            url: baseUrl + '/update_form',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                form_id: formId,
                form_name: form_name,
                form_description: form_description,
                form_sequence: form_sequence
            }),
            success: function(data) {
                data = JSON.parse(data);

                if (!data.result) {
                    console.error('Error updating form');
                }
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }
    $(form).on('keyup', '[name="form_name"], [name="form_description"]', function() {
        updateForm($(this));
    });

    //Update Section
    function updateSection(element) {
        let section = element.closest('section');
        let section_id = section.data('section-id');
        let section_name = section.find('[name="section_name"]').val();
        let section_description = section.find('[name="section_description"]').val(); //not implemented yet
        let section_sequence = section.find('[name="section_sequence"]').val(); //not implemented yet

        $.ajax({
            url: baseUrl + '/update_section',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                section_id: section_id,
                section_name: section_name,
                section_description: section_description,
                section_sequence: section_sequence
            }),
            success: function(data) {
                data = JSON.parse(data);

                if (!data.result) {
                    console.error('Error updating section');
                }
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }
    $(form).on('keyup', '[name="section_name"], [name="section_description"], [name="section_sequence"]', function() {
        updateSection($(this));
    });

    //Update Field
    function updateField(element) {
        let fieldGroup = element.closest('.form-group');
        let field_id = fieldGroup.data('field-id');
        let field_name = fieldGroup.find('[name="field_name"]').val();
        let field_description = fieldGroup.find('[name="field_description"]').val();
        let field_sequence = fieldGroup.find('[name="field_sequence"]').val();
        let field_type_id = fieldGroup.find('[name="field_type"]').val();
        let is_required = fieldGroup.find('[name="is_required"]').is(':checked');

        // Special options for multiple choice fields, checkbox, and linear scale

        // Multiple choice
        let has_others_option = fieldGroup.find('[name="has_others_option"]').is(':checked') || null;

        // Checkbox
        let min_selection = fieldGroup.find('[name="min_selection"]').val() || null;
        let max_selection = fieldGroup.find('[name="max_selection"]').val() || null;

        // Linear scale
        let min_value = fieldGroup.find('[name="min_value"]').val() || null;
        let max_value = fieldGroup.find('[name="max_value"]').val() || null;
        let left_label = fieldGroup.find('[name="left_label"]').val() || null;
        let right_label = fieldGroup.find('[name="right_label"]').val() || null;

        $.ajax({
            url: baseUrl + '/update_field',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                field_id: field_id,
                field_name: field_name,
                field_description: field_description,
                field_sequence: field_sequence,
                field_type_id: field_type_id,
                is_required: is_required,
                has_others_option: has_others_option,
                min_selection: min_selection,
                max_selection: max_selection,
                min_value: min_value,
                max_value: max_value,
                left_label: left_label,
                right_label: right_label
            }),
            success: function(data) {
                data = JSON.parse(data);

                if (!data.result) {
                    console.error('Error updating field');
                }
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }
    $(form).on('keyup', '[name="field_name"], [name="field_description"], [name="field_sequence"]', function() {
        updateField($(this));
    });
    $(form).on('change', '[name="field_type"]', function() {
        updateField($(this));
    });
    $(form).on('change', '[name="is_required"]', function() {
        updateField($(this));
    });
    //Special triggers for multiple choice fields, checkbox, and linear scale
    $(form).on('change', '[name="has_others_option"]', function() {
        updateField($(this));
    });
    $(form).on('keyup', '[name="min_selection"], [name="max_selection"]', function() {
        updateField($(this));
    });
    $(form).on('keyup', '[name="min_value"], [name="max_value"], [name="left_label"], [name="right_label"]', function() {
        updateField($(this));
    });
    $(form).on('change', '[name="min_value"], [name="max_value"]', function() {
        updateField($(this));
    });

    //Update Option
    function updateOption(element) {
        let optionGroup = element.closest('.form-option');
        let option_id = optionGroup.data('option-id');
        let option_name = optionGroup.find('[name="option_name"]').val();
        let option_sequence = optionGroup.find('[name="option_sequence"]').val();

        $.ajax({
            url: baseUrl + '/update_option',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                option_id: option_id,
                option_name: option_name,
                option_sequence: option_sequence
            }),
            success: function(data) {
                data = JSON.parse(data);

                if (!data.result) {
                    console.error('Error updating option');
                }
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    } 
    $(form).on('keyup', '[name="option_name"], [name="option_sequence"]', function() {
        updateOption($(this));
    });

    // Function to handle deletion of sections, fields, and options
    function handleDelete(element, data, url, successCallback) {
        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                url: baseUrl + url,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(response) {
                    response = JSON.parse(response);

                    if (response.result) {
                        successCallback(element);
                    } else {
                        console.error('Error deleting item');
                    }
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        }
    }

    // Delete Section
    $(form).on('click', '.delete-section', function() {
        let section = $(this).closest('section');
        let section_id = section.data('section-id');
        handleDelete(section, { section_id: section_id }, '/delete_section', function(element) {
            element.remove();
        });
    });

    // Delete Field
    $(form).on('click', '.delete-field', function() {
        let field = $(this).closest('.form-group');
        let field_id = field.data('field-id');
        handleDelete(field, { field_id: field_id }, '/delete_field', function(element) {
            element.remove();
        });
    });

    // Delete Option
    $(form).on('click', '.delete-option', function() {
        let option = $(this).closest('.form-option');
        let option_id = option.data('option-id');
        handleDelete(option, { option_id: option_id }, '/delete_option', function(element) {
            element.remove();
        });
    });

});