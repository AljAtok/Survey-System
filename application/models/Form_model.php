<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form_model extends CI_Model {

    //Insertion of new form components
    public function get_section($s_count){
        $section = '<section class="mb-5" section="'.$s_count.'"> 
                        <div class="row w-100 justify-content-between m-2">
                            <div class="w-75 d-flex">
                                <h5 class="w-25 m-0">Section Name:</h5>
                                <input class="w-75" type="text" name="section_name[]" class="form-control">
                            </div>
                            <div class="w-25 text-right">
                                <button type="button" class="btn btn-add btn-sm btn-primary" id="add-field"> 
                                    <i class="fas fa-plus-circle"></i> Add Field
                                </button>
                                <button type="button" style="padding:4px 9px" class="btn btn-add btn-sm btn-danger rounded-circle" id="delete">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div id="field-container">
                        
                        </div>
                    </section>';
        return $section;
    }

    public function get_field($s_count, $f_count){
        $field_types = $this->main->get_data('form_field_type_tbl', ['status' => 1]);

        $field = ' <div id="field" field="'.$f_count.'" class="container-fluid field-default"> 
                        <div class="row">
                            <input type="hidden" name="belongs_to[]" value="' .$s_count. '">
                            <div class="col-4">
                                <input style="width:3.5rem" type="number" name="sequence[]">
                                <p>Sequence</p>
                            </div>
                            <div class="col text-right">
                                <select name="field_type_id[]" id="select" class="zxc4">
                                    <option selected hidden disabled>Select Field Type</option>';
                                    foreach ($field_types as $field_type){
        $field .=                       '<option value="' . $field_type->field_type_id . '">' . $field_type->field_type . '</option>';	
                                    }
        $field .=               '</select>
                                <button type="button" style="padding: 2px 8px" class="btn btn-add btn-sm btn-danger rounded-circle" id="delete">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <hr>
                        <div id="field-content-container">
                            <div id="current-content">
                                    
                            </div>
                        </div>
                    </div>';
        return $field;
    }
    
    public function get_field_content($selected_type){
        if(in_array($selected_type, [1, 2, 3, 8, 9, 10])){
            $f_content = '<div class="container">
                            <div class="row w-100 justify-content-between">
                                <h5 class="w-25">Name</h5>
                                <input class="w-75" type="text" name="field_name[]">     
                            </div>
                            <div class="row my-1 pb-2 w-100 justify-content-between border-bottom">
                                <h5 class="w-25">Description</h5>
                                <textarea class="w-75" type="text" name="field_description[]" value="' . '"></textarea>
                            </div>
                            <div class=""> 
                                Required :
                                <input type="hidden" name="is_required[]" value="0">
                                <input type="checkbox" class="align-middle" id="is_required">
                            </div>
                        </div>';

        } elseif (in_array($selected_type, [4, 5, 6, 7])) {
            $f_content ='<div class="container">
                            <div class="row w-100 justify-content-between">
                                <h5 class="w-25">Name</h5>
                                <input class="w-75" type="text" name="field_name[]">     
                            </div>
                            <div class="row my-1 pb-2 w-100 justify-content-between border-bottom">
                                <h5 class="w-25">Description</h5>
                                <textarea class="w-75" type="text" name="field_description[]" value="' . '"></textarea>
                            </div>
                            <div id="option-container">
                                <div class="d-flex w-100 align-items-end">
                                    <input type="hidden" name="option_belongs_to[]" value="" class="defaultoption">
                                    <div class="mr-1">
                                        <h6>Option Name</h6>
                                        <input type="text" name="option_name[]">
                                    </div>
                                </div>
                                <div class="d-flex w-100 align-items-end">
                                    <input type="hidden" name="option_belongs_to[]" value="" class="defaultoption">
                                    <div class="mr-1">
                                        <h6>Option Name</h6>
                                        <input type="text" name="option_name[]">
                                    </div>
                                </div>             
                            </div>
                            <hr>
                            <div>
                                Required :
                                <input type="hidden" name="is_required[]" value="0">
                                <input type="checkbox" class="align-middle" id="is_required">
                                <button type="button" class="btn btn-add btn-sm btn-primary float-right" id="add-option"> 
                                    <i class="fas fa-plus-circle"></i> Add Option
                                </button>
                            </div>
                        </div>';
        } else {
            $f_content = '<div class="d-flex align-items-center flex-column"> <h1 class="text-danger">404</h1> <h3>Field Not Found</h3></div>';
        }
        return $f_content;
    }

    public function get_option($field_id){
        $option =   '<div id="option" class="d-flex align-items-end w-100 mt-1">
                        <input type="hidden" name="option[]" value="new">
                        <div class="mr-1">
                            <input type="hidden" name="option_belongs_to[]" value="'.$field_id.'">
                            <h6>Option Name</h6>
                            <input class="" type="text" name="option_name[]" value="">
                        </div>
                        <button type="button" style="padding:3px 9px" class="btn btn-add btn-sm btn-danger rounded-circle" id="delete">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>';
        return $option;
    }


    

    
    //Insertion of existing form components
    public function get_existing_section($s_count, $section_name = '', $section_id = '', $fields = []){

        $reference_section_name = str_replace(' ', '', strtolower($section_name));
        
        $section = '<section class="mb-5" section="'.$s_count.'"> 

                    <input type="hidden" name="section[]" value="' . $section_id . '"> 

                        <div class="row w-100 justify-content-between m-2">

                            <input type="hidden" name="section_name[]" value="' . $reference_section_name . '">

                            <div class="w-75 d-flex">
                                <h5 class="w-25">Section Name:</h5>
                                <input class="w-75" type="text" name="' . $reference_section_name . '" value="'. $section_name .'" class="form-control">
                            </div>
                            <div class="w-25 text-right">
                                <button type="button" class="btn btn-add btn-sm btn-primary" id="add-field"> 
                                    <i class="fas fa-plus-circle"></i> Add Field
                                </button>';
        // if($s_count > 1){
        $section .=             '<button type="button" style="padding:4px 9px" class="btn btn-add btn-sm btn-danger rounded-circle" id="delete">
                                    <i class="fas fa-times"></i>
                                </button>';
        // }
        $section .=          '</div>
                        </div>
                        <div id="field-container">
                            ' . implode('', $fields) . '
                        </div>
                    </section>';
        return $section;
    }

    public function get_existing_field($section_count, $field_count, $form_field_name, $form_field_description, $field_type_id, $field_id, $is_required, $sequence, $options = []){
        $field_types = $this->main->get_data('form_field_type_tbl');

        $checked = '';
       
        $field_content = '';

        $reference_field_name = '';
        $field_name_identifier = '';
        $field_id_identifier = '';

        if($is_required == 1){
            $checked = 'checked';
        }

        if($form_field_name){
            $reference_field_name = str_replace(' ', '', strtolower($form_field_name));
            $field_name_identifier = '<input type="hidden" name="field_name[]" value="' . $reference_field_name . '">';
            $field_id_identifier = '<input type="hidden" name="field[]" value="' . $field_id . '">';
        }

        $field = '<div id="field" field="'.$field_count.'" class="container-fluid field-default" tabindex="0">
                    ' . $field_name_identifier . ' 
                        
                        <div class="row border-bottom pb-1 mb-2">
                            
                            <input type="hidden" name="belongs_to[]" value="' .$section_count. '">

                            <div class="col">
                                <input style="width:3.5rem" type="number" name="sequence[]" value="'.$sequence.'" min="1">
                                <p class="m-0">Sequence</p>
                            </div>

                            <div class="col text-right">
                                <select name="field_type_id[]" id="select" class="zxc4">';
                                    if(empty($field_type_id)){
        $field .=                   '<option selected hidden disabled>Select Field Type</option>';
                                    }
                                    foreach ($field_types as $field_type){
                                        $selected = 'hidden';

                                        if($field_type->field_type_id == $field_type_id){
                                            $selected = 'selected';
                                            $field_content = $this->get_existing_field_content($field_type_id, $form_field_name, $form_field_description, $checked, $options , $field_id_identifier, $reference_field_name);
                                            
                                        }
        $field .=                       '<option '. $selected .' value="' . $field_type->field_type_id . '">' . $field_type->field_type . '</option>';	
                                    }
        $field .=                   '</select>';
        
        // if($field_count > 11){
        $field .=                  '<button type="button" style="padding: 2px 8px" class="btn btn-add btn-sm btn-danger rounded-circle" id="delete">
                                        <i class="fas fa-times"></i>
                                    </button>';
        // }
        $field .=               '</div>
                            </div>
    
                            <div id="field-content-container">
                                <div id="current-content">'
                                        . $field_content .
                                '</div>
                            </div>
                        </div>
                        ';
        return $field;
    }
    
    public function get_existing_field_content($selected_type, $form_field_name, $form_field_description, $checked, $options = [], $field_id_identifier = '', $reference_field_name = 'form_field_name[]'){
        $checked_value = '0';
        if(!empty($checked)){
            $checked_value = '1';
        }

        if(in_array($selected_type, [1, 2, 3, 8, 9, 10])){
            $f_content = '<div class="container">
                            ' . $field_id_identifier . '
                            <div class="row w-100 justify-content-between">
                                <h5 class="w-25">Name</h5>
                                <input class="w-75" type="text" name="'. $reference_field_name .'" value="' . $form_field_name . '">     
                            </div>
                            <div class="row my-1 pb-2 w-100 justify-content-between border-bottom">
                                <h5 class="w-25">Description</h5>
                                <input type="hidden" name="field_description[]">
                                <textarea class="w-75" type="text" name="'. $reference_field_name .'desc">' . $form_field_description . '</textarea>
                            </div>
                            <div class="">
                                Required :
                                <input type="hidden" name="is_required[]" value="' . $checked_value . '">
                                <input type="checkbox" class="align-middle" id="is_required" '. $checked .'>
                            </div>
                        </div>';

        } elseif (in_array($selected_type, [4, 5, 6, 7])) {
            $f_content ='<div class="container">
                            ' . $field_id_identifier . '
                            <div class="row w-100 justify-content-between">
                                <h5 class="w-25">Name</h5>
                                <input class="w-75" type="text" name="'. $reference_field_name .'" value="' . $form_field_name . '">     
                            </div>
                            <div class="row my-1 pb-2 w-100 justify-content-between border-bottom">
                                <h5 class="w-25">Description</h5>
                                <input type="hidden" name="field_description[]">
                                <textarea class="w-75" type="text" name="'. $reference_field_name .'desc">' . $form_field_description . '</textarea>
                            </div>
                            <div id="option-container">
                            ' . implode('', $options) . '';
            if(empty($form_field_name)){
            $f_content .=      '<div class="d-flex w-100 align-items-end">
                                    <input type="hidden" name="option_belongs_to[]" value="" class="defaultoption">
                                    <div class="mr-1">
                                        <h6>Option Name</h6>
                                        <input type="text" name="option_name[]">
                                    </div>
                                </div>
                                <div class="d-flex w-100 align-items-end">
                                    <input type="hidden" name="option_belongs_to[]" value="" class="defaultoption">
                                    <div class="mr-1">
                                        <h6>Option Name</h6>
                                        <input type="text" name="option_name[]">
                                    </div>
                                </div>';            
            }               
            $f_content .=   '</div>
                            <hr>
                            <div>
                                Required :
                                <input type="hidden" name="is_required[]" value="' . $checked_value . '">
                                <input type="checkbox" class="align-middle" id="is_required" '. $checked .'>';
                                if($selected_type != 7){
            $f_content .=           '<button type="button" class="btn btn-add btn-sm btn-primary float-right" id="add-option"> 
                                        <i class="fas fa-plus-circle"></i> Add Option
                                    </button>';
                                }
            $f_content .=   '</div>
                        </div>';
        } else {
            $f_content = '<div class="d-flex align-items-center flex-column"> <h1 class="text-danger">404</h1> <h3>Field Not Found</h3></div>';
        }

        return $f_content;
    }

    public function get_existing_option($field_count, $option_count, $option_name, $option_id){
        $reference_option_name = str_replace(' ', '', strtolower($option_name));

        $option =  '<div id="option" class="d-flex align-items-end w-100 mt-1">
                        <input type="hidden" name="option[]" value="' . $option_id . '">
                        <input type="hidden" name="option_belongs_to[]" value="'. $field_count .'">
                            <div class="mr-1">
                                <input type="hidden" name="option_name[]" value="'.$reference_option_name.'">
                                <h6>Option Name</h6>
                                <input class="" type="text" name="'.$reference_option_name.'" value="'. $option_name .'">
                            </div>';
        if($option_count > 2 ){
        $option .=      '<button type="button" style="padding:3px 9px" class="btn btn-add btn-sm btn-danger rounded-circle" id="delete">
                            <i class="fas fa-times"></i>
                        </button>';
        }
        $option .= '</div>';

        return $option;
    }
    
}
