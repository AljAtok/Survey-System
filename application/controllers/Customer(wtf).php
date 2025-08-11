<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->controller = strtolower(__CLASS__);
    	$this->load->model('main_model', 'main');
        
    }

    public function index()
    {
        $this->load->view('errors/custom-error-404.php');
    }

    public function cdi_survey()
    {      
        if($this->main->check_data('form_tbl', ['form_id' => 1, 'status' => 0])){
            $this->load->view('errors/form_maintenance.php');
            return;
        }
        
        // $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        // $this->output->set_header('Pragma: no-cache');
        // $this->output->set_header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

        // if($this->uri->uri_string() != 'cdi-survey'){ //???
        //     redirect('cdi-survey');
        // }

        $join = [
            'form_field_tbl b' => 'a.section_id = b.section_id',
        ];

        $select = [
            'b.form_field_name',
            'b.form_field_description',
            'b.section_id',
            'b.field_type_id',
            'b.field_id',
            'b.is_required',
        ];

        $data['sections']    = $this->main->get_data('form_section_tbl', ['form_id' => 1, 'status' => 1]);
        $data['fields']      = $this->main->get_join('form_section_tbl a', $join, FALSE, 'b.form_field_sequence asc', FALSE, $select, ['a.status' => 1, 'b.status' => 1, 'a.form_id' => 1]);
        $data['options']     = [];

        foreach($data['fields'] as $field){
            if(!in_array($field->field_type_id, [4, 5, 6, 7])) continue;

            $options = $this->main->get_data('form_field_choice_tbl', ['field_id' => $field->field_id, 'status' => 1]);
            $data['options'] = array_merge($data['options'] , $options);
        }

        
        $field_ids = [];
        $field_ids += array_column($data['fields'], 'field_id');
    
        $field_type_ids = [];
        $field_type_ids += array_column($data['fields'], 'field_type_id');

        $option_ids = [];
        $option_ids += array_column($data['options'], 'option_id');

        $_SESSION['cdi_field_ids'] = $field_ids;
        $_SESSION['cdi_field_type_ids'] = $field_type_ids;
        $_SESSION['cdi_option_ids'] = $option_ids;
        $_SESSION['cdi_form_id'] = '1';

        $this->load->helper('captcha');
        $captcha_config = array(
            'img_path'      => './captcha_images/',
            'img_url'       => base_url().'captcha_images/',
            'img_width'     => '200',
            'img_height'    => 50,
            'word_length'   => 5,
            'font_size'     => 20,
            'expiration'    => 7200,
            'pool'          => '23456789ABCDEFGHJKMNPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
        );
        $captcha = create_captcha($captcha_config);

        $this->session->set_userdata('captchaWord', encode($captcha['word']));

        $data['stores']           = $this->main->get_data('store_tbl', ['status' => 1]);
        $data['captcha']          = $captcha;
        $data['controller']       = $this->controller;
        $data['title']            = ucwords(str_replace('_', ' ', __FUNCTION__));
        $data['content']          = $this->load->view($this->controller . '/transaction_edit_content', $data, TRUE);
        $this->load->view($this->controller . '/template', $data);
    }

    public function ctg_survey() 
    {   
        if($this->main->check_data('form_tbl', ['form_id' => 2, 'status' => 0])){
            $this->load->view('errors/form_maintenance.php');
            return;
        }

        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

        if($this->uri->uri_string() != 'ctg-survey'){ //???
            redirect('ctg-survey');
        }

        $join = [
            'form_field_tbl b' => 'a.section_id = b.section_id',
        ];

        $select = [
            'b.form_field_name',
            'b.form_field_description',
            'b.section_id',
            'b.field_type_id',
            'b.field_id',
            'b.is_required',
        ];

        $data['sections']    = $this->main->get_data('form_section_tbl', ['form_id' => 2, 'status' => 1]);
        $data['fields']      = $this->main->get_join('form_section_tbl a', $join, FALSE, 'b.form_field_sequence asc', FALSE, $select, ['a.status' => 1, 'b.status' => 1, 'a.form_id' => 2]);
        $data['options']     = [];

        foreach($data['fields'] as $field){
            if(!in_array($field->field_type_id, [4, 5, 6, 7])) continue;

            $options = $this->main->get_data('form_field_choice_tbl', ['field_id' => $field->field_id, 'status' => 1]);
            $data['options'] = array_merge($data['options'] , $options);
        }

        
        $field_ids = [];
        $field_ids += array_column($data['fields'], 'field_id');
    
        $field_type_ids = [];
        $field_type_ids += array_column($data['fields'], 'field_type_id');

        $option_ids = [];
        $option_ids += array_column($data['options'], 'option_id');

        $_SESSION['ctg_field_ids'] = $field_ids;
        $_SESSION['ctg_field_type_ids'] = $field_type_ids;
        $_SESSION['ctg_option_ids'] = $option_ids;
        $_SESSION['ctg_form_id'] = '2';
        //dont forget
        $this->load->helper('captcha');
        $captcha_config = array(
            'img_path'      => './captcha_images/',
            'img_url'       => base_url().'captcha_images/',
            'img_width'     => '200',
            'img_height'    => 50,
            'word_length'   => 5,
            'font_size'     => 20,
            'expiration'    => 7200,
            'pool'          => '23456789ABCDEFGHJKMNPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
        );
        $captcha = create_captcha($captcha_config);
        $this->session->set_userdata('captchaWord', encode($captcha['word']));
        $data['captcha']          = $captcha;
        $data['controller']       = $this->controller;
        $data['title']            = ucwords(str_replace('_', ' ', __FUNCTION__));
        $data['content']          = $this->load->view($this->controller . '/transaction_edit_content', $data, TRUE);
        $this->load->view($this->controller . '/template', $data);
    }

    function validate_phone_number($contact) {

        $contact_pattern = '/^(09|\+639)\d{9}$/';
        
        $isValid = preg_match($contact_pattern, $contact);
        if (!$isValid) {
            $this->form_validation->set_message('validate_phone_number', '{field} is invalid.');
            return false;
        }
        return true;
    }

    private function special_cdi_validation($form_data){ //????

        if(decode($form_data['babalikkapaba']) == 86){
            $form_data['bakithindikanababalik'] = 'n/a';
        }else{
            $form_data['bakitkababalik'] = 'n/a';
        }

        if(!empty($form_data['parasafirsttimecustomeranoangdahilanbakitkakumainsachooks']) && $form_data['parasafirsttimecustomeranoangdahilanbakitkakumainsachooks'] != 'n/a'){
            $form_data['parasareturningcustomeranoangdahilanbakitkabumalik'] = 'n/a';
        }else{
            $form_data['parasafirsttimecustomeranoangdahilanbakitkakumainsachooks'] = 'n/a'; 
        }

        if(decode($form_data['gaanokatagalkanangcustomerngchooks']) == 1){
            $form_data['gaanokadalaskangkumainsachooks'] = 'n/a';
        }
        
        return $form_data;
    }

    public function validate_transaction() //hard to understand
    {      
        
        $form_data = $this->input->post();

        // var_dump($form_data);
        // die();

        $form_data['field_id']      = $_SESSION['ctg_field_ids']      ?? $_SESSION['cdi_field_ids'];
        $form_data['field_type_id'] = $_SESSION['ctg_field_type_ids'] ?? $_SESSION['cdi_field_type_ids'];
        $form_data['option_id']     = $_SESSION['ctg_option_ids']     ?? $_SESSION['cdi_option_ids'];
        $form_data['form_id']       = $_SESSION['ctg_form_id']        ?? $_SESSION['cdi_form_id'];

        if($form_data['form_id'] == 1){
           $form_data = $this->special_cdi_validation($form_data);
        }

        unset($_SESSION['ctg_field_ids'], $_SESSION['ctg_field_type_ids'], $_SESSION['ctg_option_ids'], $_SESSION['ctg_form_id'], $_SESSION['cdi_field_ids'], $_SESSION['cdi_field_type_ids'], $_SESSION['cdi_option_ids'], $_SESSION['cdi_form_id']);

        if ($this->input->post('captcha') != decode($this->session->userdata('captchaWord'))) {
            $this->session->set_flashdata('form_data', $form_data);
            $data = [
                'result'   => FALSE,
                'public'   => TRUE,
                'message'  => 'Captcha is incorrect',
                'redirect' => '',
            ];
            echo json_encode($data);
            return;
        }

        if(isset($form_data['store']) && !empty($form_data['store'])){
            if($this->main->check_data('transaction_form_tbl', ['store_id' => decode($form_data['store']), 'or_number' => $form_data['or_number']])){
                $this->session->set_flashdata('form_data', $form_data);
                $data = [
                    'result'   => FALSE,
                    'public'   => TRUE,
                    'message'  => 'This OR Number has already been used in this store. Please try another OR Number.',
                    'redirect' => '',
                ];
                echo json_encode($data);
                return;
            }
        } else {
            if($this->main->check_data('transaction_form_tbl', ['or_number' => $form_data['or_number']])){
                $this->session->set_flashdata('form_data', $form_data);
                $data = [
                    'result'   => FALSE,
                    'public'   => TRUE,
                    'message'  => 'This Order Number has already been used. Please try another OR Number.',
                    'redirect' => '',
                ];
                echo json_encode($data);
                return;
            }
        }

        $value_array = [];

        function outputError() {
            $data = [
                'result'   => FALSE,
                'public'   => TRUE,
                'message'  => 'System has encountered an error. Please try again.',
                'tampered' => TRUE,
            ];
            return json_encode($data);
        }
        
        foreach($form_data['field_name'] as $key => $value){
        
            if(in_array($form_data['field_type_id'][$key], [4, 5, 6])){


                set_error_handler(function($errno, $errstr, $errfile, $errline) {
                    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
                });

                try {
                    $decoded_value = decode(clean_data($this->input->post($value)));

                    if (!in_array($decoded_value, $form_data['option_id'])) {
                        $data = outputError();
                        echo $data;
                        return;
                    }

                    if($form_data['field_type_id'][$key] != 7){
                        $option = $this->main->get_data('form_field_choice_tbl', ['option_id' => $decoded_value], TRUE, 'field_id');

                        $option_field_id = $option->field_id;

                        //Check if option's field_id is not the same as its field's field_id
                        if ($option_field_id != $form_data['field_id'][$key]) {
                            $data = outputError();
                            echo $data;
                            return;
                        }
                    }

                    if($form_data['field_type_id'][$key] != 7){
                        array_push($value_array, $decoded_value);
                    }

                } catch (Exception $e) {

                    continue;
                } finally {

                    restore_error_handler();
                }
            }
        }
        
        //Checks if there are duplicate values in the value array.
        if(count($value_array) > count(array_unique($value_array))){
            $data = outputError();
            echo $data;
            return;
        }

        $rules = [ ['field' => 'or_number', 'label' => 'OR Number', 'rules' => 'required'], ];
        
        if($this->input->post('store') !== null && !empty($this->input->post('store'))){
            array_push($rules, ['field' => 'store', 'label' => 'Store', 'rules' => 'required']);
        }

        $has_entry = true;
       
        foreach($form_data['field_name'] as $key => $value){
            $requirement = $this->main->get_data('form_field_tbl', ['field_id' => $form_data['field_id'][$key]], TRUE, 'is_required');
            $validation = intval($requirement->is_required) == 1 ? 'required' : '';
            if($validation){
                if($form_data['field_id'][$key] == 9 || $form_data['field_id'][$key] == 37){
                    $validation = 'required|callback_validate_phone_number';
                } elseif($form_data['field_id'][$key] == 10 || $form_data['field_id'][$key] == 38){
                    $validation = 'required|valid_email';
                }
                $new_rule = [ 'field' => $value, 'label' => $value, 'rules' => $validation];
            }
            array_push($rules, $new_rule);

            if(($this->input->post($value)) && (!empty($this->input->post($value)))){
                $has_entry = false;
            };
        };

        if($has_entry){
            array_push($rules, ['field' => 'has_entry', 'label' => 'At Least One Entry', 'rules' => 'required']);
        }

        $validation = validate_post_data($rules);
        if (!$validation['result']) {

            // var_dump($validation);

            $this->session->set_flashdata('form_data', $form_data);

            $data = [
                'result'   => FALSE,
                'public'   => TRUE,
                'message'  => 'Invalid Input. Please check the fields and try again.',
                'redirect' => '',
            ];
            
            echo json_encode($data);
            return;
        }

        // die('end pero pasado sa rule validation');
        // die('transaction-end');
        
        $this->store_transaction($form_data);
    }

    private function store_transaction($form_data)
    {   

        // var_dump($form_data);
        // die();
        $generate_unique_id = function() use ($form_data) {
            $length = 3;
            $rand   = generate_random_coupon($length);

            $unique_id = ($form_data['form_id'] == 1 ? 'CDI' : 'CTG') . date('ymd') . $rand;

            $count = 0;

            while ($this->main->check_data('transaction_form_tbl', ['reference_number' => $unique_id])) {
                $count++;

                if($count == 15){
                    $length++;
                    $count = 0;
                    continue;
                }
                $unique_id = ($form_data['form_id'] == 1 ? 'CDI' : 'CTG') . date('-ymd-') . $rand;
            }

            return $unique_id;
        };

        $reference_code = $generate_unique_id();

        $transaction_data = [
            'form_id' => $form_data['form_id'],
            'reference_number' => encode($reference_code),
            // 'comment' => $form_data['form_id'] == 1 ? encode(clean_data($this->input->post('commentssaservice'))) : encode(clean_data($this->input->post('comments'))),
            'name' => encode(clean_data($this->input->post('name'))),
            'contact_number' => encode(clean_data($this->input->post('contactnumber'))),
            'email' => encode(clean_data($this->input->post('email'))),
            'created_at' => date_now(),
            'or_number' => clean_data($this->input->post('or_number')),
            'store_id' => $this->input->post('store') ? decode(clean_data($this->input->post('store'))) : NULL,
            'status' => 1,
        ];

        $this->db->trans_start();
        
        $result = $this->main->insert_data('transaction_form_tbl', $transaction_data, TRUE);
        
        foreach($form_data['field_name'] as $key => $value){

            if(in_array($form_data['field_type_id'][$key], [4, 5, 6])){

                //The try catch is for capturing the newly implemented others options which is hardcodedly included in some of the fields.
                set_error_handler(function($errno, $errstr, $errfile, $errline) {
                    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
                });

                try {

                    $decoded_value = decode(clean_data($this->input->post($value)));

                    $name = $this->main->get_data('form_field_choice_tbl', ['option_id' => $decoded_value], TRUE, 'option_name');

                    $response = $name->option_name;

                    
                } catch (Exception $e) {

                    $response = clean_data($form_data[$value]);
                } 
                finally {

                    restore_error_handler();
                }

            } else if($form_data['field_type_id'][$key] == 7){

                $response = clean_data(decode($form_data[$value]));

            } else {
                $response = clean_data($form_data[$value]);
            }

            $field_name = $this->main->get_data('form_field_tbl', ['field_id' => $form_data['field_id'][$key]], TRUE, 'form_field_name')->form_field_name;

            $response_data = [
                'transaction_id' => $result['id'],
                'field_id'       => $form_data['field_id'][$key],
                'field_name'     => $field_name,
                'response'       => encode($response),
                'created_at'     => date_now(),
            ];

            // var_dump($response_data);

            $this->main->insert_data('transaction_response_tbl', $response_data);
        }

        // die('end');

        unset($_SESSION['field_ids'], $_SESSION['field_type_ids'], $_SESSION['option_ids'], $_SESSION['form_id']);//temporary

        $redirect = base_url($this->controller . '/success');
        $this->session->set_tempdata('form_submitted', true, 900); // Set the time to 15minutes (900 seconds)
        $this->session->set_userdata('reference_code', $reference_code);

        if ($this->db->trans_status()) {
            $this->db->trans_commit();
            // $this->db->trans_rollback();
            // echo 'success';
            // die();
            $data = [
                'result'   => TRUE,
                'message'  => 'Survey Submitted Successfully',
                'redirect' => $redirect,
            ];
        } else {
            $this->db->trans_rollback();
            $data = [
                'result'   => FALSE,
                'message'  => 'Survey Submission Failed',
            ];
        }

        echo json_encode($data);
        return;
    }

    public function success() //obs
    {           
        if (!$this->session->tempdata('form_submitted')) {
            show_404();
        }

        if($this->uri->uri_string() != 'success'){
            redirect('success');
        }

        $data['reference_code'] = $this->session->userdata('reference_code');
        // $this->session->unset_userdata('voucher_code');

        $data['controller']       = $this->controller;
        $data['title']            = ucwords(str_replace('_', ' ', __FUNCTION__));
        $data['content']          = $this->load->view($this->controller . '/success', $data, TRUE);
        $this->load->view($this->controller . '/template', $data);
    }
}