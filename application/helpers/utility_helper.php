
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	function system_default(){
		return [
			'session_name' => 'chooksurvey-form-user',
			'system_name'  => 'Chooks Survey',
			'company_name' => 'Chooks To Go Inc.'
		];
	}

    function validate_post_data($rules) //This will get removed soon
    {
		$CI =& get_instance();
        $CI->form_validation->set_error_delimiters('', '')->set_rules($rules);
        $validation_result = $CI->form_validation->run();
        $validation_data   = [];
        if (!$validation_result) {
            // $validation_data = [
            //     'fname'    => $this->_set_validation_data('fname'),
            //     'lname'    => $this->_set_validation_data('lname'),
            //     'gender[]' => $this->_set_validation_data('gender[]', $this->input->post('gender')),
            // ];
            foreach ($rules as $row) {
                $value = (isset($row['data'])) ? $row['data'] : '';
                $field = $row['field'];
                $validation_data[$field] = set_validation_data($field, $value);
            }
        } 

        return [
            'result' => $validation_result,
            'data'   => $validation_data
        ];
    } 

    function set_validation_data($field, $value = '')
    {
        $error_message = form_error($field);
        $value         = ($value == "") ? set_value($field) : $value;
        $is_valid      = ($error_message === '') ? 'is-valid' : 'is-invalid';
        return ['value' => $value, 'err_message' => $error_message, 'is_valid' => $is_valid];
    }

	function require_login($user_type_controller)
    {
		$CI =& get_instance();
		$login = $CI->session->userdata(system_default()['session_name']);
		if(!isset($login)){
			$CI->session->unset_userdata(system_default()['session_name']);
            if ($user_type_controller != 'login') {
                redirect('login');
            }
		} else {
            return $login;
        }
	}

	// function send_email_helper($recipient, $email_content, $subject)
    // {
	// 	$CI =& get_instance();
    //     $config = [ 
    //         'protocol'  => 'smtp',
    //         'smtp_host' => 'ssl://server10.synermaxx.net',
    //         'smtp_port' => 465,
    //         'smtp_user' => 'alerts@bountyagro.com.ph',
    //         'smtp_pass' => '',
    //         'mailtype'  => 'html',
    //         'charset'   => 'utf-8',
    //         'wordwrap'  => TRUE
    //     ];
    //     $CI->load->library('email', $config);
    //     $CI->email->set_newline("\r\n")
    //                 ->from('alerts@bountyagro.com.ph', system_default()['system_name'] . ' Notification')
    //                 ->to($recipient)
    //                 ->subject($subject)
    //                 ->message($email_content);

    //     if($CI->email->send()){
    //         $result = TRUE;
    //         $msg    = 'Email Sent';
    //     }else{
    //         $result = FALSE;
    //         $msg    = $CI->email->print_debugger();
    //     }

    //     // store email log
    //     $data = [
    //         'email_log_recipient' => $recipient,
    //         'email_log_content'   => $email_content,
    //         'email_log_msg'       => $msg,
    //         'email_log_status'    => ($result) ? 1 : 0,
    //         'email_log_added'     => date_now(),
    //     ];
    //     $CI->main->insert_data('email_logs_tbl', $data);
    // }

	function store_log($type, $action, $user_id = NULL, $data_id = NULL)
	{
		$CI         = & get_instance();
		$url       = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$ip_address = $_SERVER['REMOTE_ADDR'];
		$data       = [
			'log_type'   => $type,         // 1 = INFO & 2 = DEBUG
			'log_action' => $action,
			'user_id'    => $user_id,
			'data_id'    => $data_id,
			'ip_address' => $ip_address,
			'log_url'    => $url,
			'log_added'  => date_now()

		];
		return $CI->main->insert_data('logs_tbl', $data, TRUE);
	}

	function store_log_details($log_id, $prev_data, $new_data, $tbl_name)
	{
		$keys      = array_keys($new_data);
		$prev_data = (array) $prev_data;

		if (empty($prev_data)) {
			foreach ($keys as $key) {
				$prev_value = NULL;
				$new_value  = $new_data[$key];
				store_log_detail($log_id, $prev_value, $new_value, $key, $tbl_name);
			}
		
		} else {
			foreach ($keys as $key) {
				$prev_value = $prev_data[$key];
				$new_value  = $new_data[$key];
				store_log_detail($log_id, $prev_value, $new_value, $key, $tbl_name);
			}
		}
	}

	function store_log_detail($log_id, $prev_value, $new_value, $key, $tbl_name)
	{
		$CI =& get_instance();
		$data = [
			'log_id'                => $log_id,
			'log_detail_prev_value' => $prev_value,
			'log_detail_new_value'  => $new_value,
			'log_detail_field'      => $key,
			'log_detail_tbl_name'   => $tbl_name,
			'log_detail_added'      => date_now()
		];
		$CI->main->insert_data('log_details_tbl', $data, TRUE);
	}

    function shorten_field_name($field_name, $counter) //Used in form builder
    {
        $cleaned_name = preg_replace('/[^a-zA-Z0-9 ]/', '', $field_name);
        $no_space_name = str_replace(' ', '', $cleaned_name);
        // $words = explode(' ', $cleaned_name);

        // $shortened_name = '';
        // foreach ($words as $word) {
        //     $shortened_name .= substr($word, 0, 1);
        // }

        // return strtolower($shortened_name) . $counter;
        return strtolower($no_space_name) . $counter;
    }

    function load_captcha()
    {
        $captcha_config = array(
            'img_path'      => './captcha_images/',
            'img_url'       => base_url().'captcha_images/',
            'img_width'     => 300,
            'img_height'    => 60,
            'word_length'   => 5,
			// 'font_path'     => './assets/fonts/AvenirLTStd-Medium.ttf', // <-- Use custom font
			'font_path' 	=> FCPATH . 'assets/fonts/AvenirLTStd-Medium.ttf', // FULL PATH, not URL
            'font_size'     => 20,
            'expiration'    => 7200,
            'pool'          => '23456789ABCDEFGHJKMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz',
			'colors'        => array(
				'background' => array(255, 255, 255),
				'border' => array(0, 0, 0),
				'text' => array(50, 50, 50),
				'grid' => array(200, 200, 200)
			)
        );
        return create_captcha($captcha_config);
    }

    function generate_reference($form_tag) {
        $length = 3;
        $rand   = generate_random_coupon($length);

        $unique_id = $form_tag . date('ymd') . $rand;

        $count = 0;

        while (get_instance()->main->check_data('transaction_form_tbl', ['reference_number' => $unique_id])) {
            $count++;

            if ($count == 15) {
                $length++;
                $count = 0;
                continue;
            }
            $rand = generate_random_coupon($length); // Regenerate the random part
            $unique_id = $form_tag . date('ymd') . $rand;
        }

        return $unique_id;
    }

    function toggle_data($tbl_name, $status_column_name, $where_column_name, $id) //Enables and Disables Data. Used by Employee Maintenance & Form Maintenance
    {
        $CI =& get_instance();
    
        $is_active = $CI->main->check_data($tbl_name, [
            $where_column_name => $id,
            $status_column_name => 1
        ]);

        $new_status = $is_active ? 0 : 1;

        $CI->db->trans_start();
    
        $CI->main->update_data($tbl_name, [
            $status_column_name => $new_status
        ], [
            $where_column_name => $id
        ]);

        if ($CI->db->trans_status()) {
            $CI->db->trans_commit();
            $response = [
                'status'   => 200,
            ];
        } else {
            $CI->db->trans_rollback();
            $response = [
                'status'   => 400,
            ];
        }

        return $response;
    }

	function parent_db(){
		$db_name = 'chooks_delivery_db';
		return $db_name;
	}
	function sibling_one_db(){
		$db_name = 'evoucher_db';
		return $db_name;
	}

	function pretty_dump($data){
		echo '<pre>';
        print_r($data);
        echo '</pre>';
		exit;
	}
