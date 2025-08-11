<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->controller = strtolower(__CLASS__);
        $this->component_path = $this->controller . '/form_components/';

        $this->load->helper('captcha');

    	$this->load->model('main_model', 'main');
    }

    public function index()
    {
        $this->load->view('errors/custom-error-404.php');
    }

    private function load_form($form_id){
        $form = $this->main->get_data('form_tbl', ['form_id' => $form_id], TRUE, ['form_name', 'form_id', 'status']);

        //Initialize form's data
        $sections = $this->main->get_data('form_section_tbl', ['form_id' => $form->form_id, 'status' => 1], FALSE, ['section_name', 'section_id'], 'section_sequence asc');
        foreach ($sections as &$section) {
            $fields = $this->main->get_data('form_field_tbl', ['section_id' => $section->section_id,'status' => 1], FALSE, ['*'], 'form_field_sequence asc');
            foreach ($fields as &$field) {

                switch ($field->field_type_id) {
                    case 4:
                        $field->mco_data = $this->main->get_data('field_multiple_choice_options_tbl', ['field_id' => $field->field_id, 'status' => 1], TRUE);
                        break;
            
                    case 6:
                        $field->co_data = $this->main->get_data('field_checkbox_options_tbl', ['field_id' => $field->field_id, 'status' => 1], TRUE);
                        break;
            
                    case 7:
                        $field->lso_data = $this->main->get_data('field_linear_scale_options_tbl', ['field_id' => $field->field_id, 'status' => 1], TRUE);
                        break;
                }

                if(in_array($field->field_type_id, [4, 5, 6])){
                    $field->options = $this->main->get_data('form_field_choice_tbl', ['field_id' => $field->field_id, 'status' => 1], FALSE, ['*'], 'option_sequence asc');
                }

            }

            $section->fields = $fields;
        }
        $form->sections = $sections;

        //Initialize form's view
        $content = '';
        foreach ($form->sections as &$section) {
            
            $fields = '';
            $counter = 0;

            foreach($section->fields as &$field){
                $field->enc_field_name = encode(shorten_field_name($field->form_field_name, $counter));
                $counter++;
                    
                switch ($field->field_type_id) {
                    case 1:
                        $path = 'long_text';
                        break;
                    case 2:
                        $path = 'short_text';
                        break;
                    case 3:
                        $path = 'number';
                        break;
                    case 4:
                        $path = 'multiple_choice';
                        break;
                    case 5:
                        $path = 'dropdown';
                        break;
                    case 6:
                        $path = 'checkbox';
                        break;
                    case 7:
                        $path = 'linear_scale';
                        break;
                    case 8:
                        $path = 'image_upload';
                        break;
                    case 9:
                        $path = 'date';
                        break;
                    default:
                        $path = 'time';
                        break;
                }
                $view = $this->load->view($this->component_path . 'field_components/' . $path, $field, TRUE);

                $field_data = [
                    'data'          => $field,
                    'field_content' => $view
                ];

                $fields .= $this->load->view($this->component_path . 'field', $field_data, TRUE);
            }

            $section_data = [
                'data'      => $section,
                'fields'    => $fields
            ];

            $content .= $this->load->view($this->component_path . 'section', $section_data, TRUE);
        }

        $data = [
            'content'    => $content,
            'form_data'  => $form
        ];

        return $data;

    }

    private function load_maintenance_page(){
        $maintenance_page_resources = [
            'controller'     => $this->controller,
            'title'          => 'Maintenance',
            'content'        => $this->load->view('errors/form_maintenance.php', null, TRUE)
        ];

        $this->load->view($this->controller . '/template', $maintenance_page_resources);
    }

    public function cdi_survey(){
        $form_id = 1;

		$check_form = $this->main->check_data('form_tbl', ['form_id' => $form_id], TRUE);
        if($check_form['result']){
			if($check_form['info']->status == 0){
				$this->load_maintenance_page();
				return;
			}
        }

		if($check_form['info']->start_date > date('Y-m-d H:i:s')){
			//* FORM NOT YET STARTED
			$this->_not_valid($form_id, 1);
		}
		elseif($check_form['info']->end_date < date('Y-m-d H:i:s')){
			//* FORM ALREADY ENDED
			$this->_not_valid($form_id, 2);
		}
		else {
			$survey_data    = $this->load_form($form_id);
			$captcha        = load_captcha();
			$this->session->set_userdata('captcha-word', $captcha['word']);
			$this->session->set_userdata('form_data', $survey_data['form_data']);
	
			$stores         = $this->main->get_data('store_tbl', ['status' => 1], FALSE, ['store_id', 'store_name'], 'store_name asc');
	
			$survey_resources = [
				'survey_content' => $survey_data['content'],
				'controller'     => $this->controller,
				'captcha'        => $captcha,
				'stores'         => $stores
			];
	
			$content = $this->load->view($this->controller . '/survey', $survey_resources, TRUE);
	
			$page_resources = [
				'title' => 'Chooks to Go Survey',
				'content' => $content
			];
	
			$this->load->view($this->controller . '/template', $page_resources);
		}

    }

    public function ctg_survey(){
        $form_id = 2;

        $check_form = $this->main->check_data('form_tbl', ['form_id' => $form_id], TRUE);
        if($check_form['result']){
			if($check_form['info']->status == 0){
				$this->load_maintenance_page();
				return;
			}
        }

		if($check_form['info']->start_date > date('Y-m-d H:i:s')){
			//* FORM NOT YET STARTED
			$this->_not_valid($form_id, 1);
		}
		elseif($check_form['info']->end_date < date('Y-m-d H:i:s')){
			//* FORM ALREADY ENDED
			$this->_not_valid($form_id, 2);
		}
		else {
			$survey_data = $this->load_form($form_id);
			$captcha        = load_captcha();
			$this->session->set_userdata('captcha-word', $captcha['word']);
			$this->session->set_userdata('form_data', $survey_data['form_data']);
	
			$survey_resources = [
				'survey_content' => $survey_data['content'],
				'controller'     => $this->controller,
				'captcha'        => $captcha
			];
	
			$content = $this->load->view($this->controller . '/survey', $survey_resources, TRUE);
	
			$page_resources = [
				'title' => 'Chooks to Go Survey',
				'content' => $content
			];
	
			$this->load->view($this->controller . '/template', $page_resources);
		}

    }	

	public function get_town()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$parent_db = parent_db();
            $province = decode($this->input->post('province_id'));
            if($province){
            	
            	$join_town = array(
            		"{$parent_db}.provinces_tbl b" => 'a.province_id = b.province_id AND b.province_status = 1 AND a.town_group_status = 1 AND a.province_id = ' . $province,
            	);
	            $town = $this->main->get_join("{$parent_db}.town_groups_tbl a", $join_town, FALSE, 'a.town_group_name ASC');
	            $options = '<option value=""></option>';
	            if (count($town) > 0) {
	                
	                foreach ($town as $row_town) {
	                    $options .= '<option value="'. encode($row_town->town_group_id) .'">' . $row_town->town_group_name. '</option>';
	                }
	            }
	            $data['result'] = 1;
	            $data['info'] = $options;
				$data['csrf_hash'] = $this->refresh_hash();
	        }else{
	        	$data['result'] = 0;
	        	$data['csrf_hash'] = $this->refresh_hash();
	        }
	        
            echo json_encode($data);

			// $this->output
			// 	->set_status_header(200)
			// 	->set_content_type('application/json')
			// 	->set_output(json_encode($data));
        }
    }

	public function get_barangay()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$parent_db = parent_db();
            $town = decode($this->input->post('barangay'));
            $check_town = $this->main->check_data("{$parent_db}.town_groups_tbl", array('town_group_id' => $town, 'town_group_status' => 1));
            if($check_town == TRUE){
            	
	            $barangay = $this->main->get_data("{$parent_db}.barangay_tbl a", array('town_group_id' => $town, 'barangay_status' => 1), FALSE, FALSE, 'a.barangay_name ASC');
	            $options = '<option value=""></option>';
	            
	            if (count($barangay) > 0) {
	                
	                foreach ($barangay as $row_barangay) {
	                    $options .= '<option value="'. encode($row_barangay->barangay_id) .'">' . $row_barangay->barangay_name. '</option>';
	                }
	            }
	            $data['result'] = 1;
	            $data['info'] = $options;
	        	$data['csrf_hash'] = $this->refresh_hash();
	        }else{
	        	$data['result'] = 0;
	        	$data['csrf_hash'] = $this->refresh_hash();
	        }
	        
            echo json_encode($data);

			// $this->output
			// 	->set_status_header(200)
			// 	->set_content_type('application/json')
			// 	->set_output(json_encode($data));
        }
    }

    //Private functions used by submit
    private function refresh_hash(){
        
        return $this->security->get_csrf_hash();
    }

    private function validate_captcha($captchaInput){
        if($captchaInput != $this->session->userdata('captcha-word')){

            $captcha = load_captcha();
            
            $this->session->set_userdata('captcha-word', $captcha['word']);

            $response = [
                'image'     => $captcha['image'],
                'csrf_hash' => $this->refresh_hash()
            ];

            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode($response))
                ->_display();
            
            exit;
        }
    }

    private function validate_data($encoded_data){

        foreach ($encoded_data as $key => $value) {
            $decoded_key = decode($key);
        
            if ($decoded_key == 'or_number') {
                $this->form_validation->set_rules($key, 'OR Number', 'required|is_unique[transaction_form_tbl.or_number]', [
                    'required' => '{field} is required.',
                    'is_unique' => 'This {field} is already taken.'
                ]);
            } elseif (strpos($decoded_key, 'email') !== false) {
                $this->form_validation->set_rules($key, 'Email', 'required|valid_email', [
                    'required' => '{field} is required.',
                    'valid_email' => 'The {field} field must contain a valid email address.'
                ]);
            } elseif (strpos($decoded_key, 'contactnumber') !== false) { 
                $this->form_validation->set_rules($key, 'Contact Number', 'required|regex_match[/^09[0-9]{9}$/]', [
                    'required' => 'Th {field} field is required.',
                    'regex_match' => 'Wrong {field} Format.'
                ]);
            }elseif ($decoded_key == 'captcha') {
                continue;
            } else {
                $this->form_validation->set_rules($key, 'Field Label', 'required', [
                    'required' => 'The {field} field is required.'
                ]);
            }
        }
        
        if ($this->form_validation->run() == FALSE) {
            $errors = $this->form_validation->error_array();
            $response = [
                'errors' => $errors,
                'csrf_hash' => $this->refresh_hash()
            ];
        
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode($response))
                ->_display();
            
            exit;
        }

    }

    private function validate_survey_submission(){ //Cleans the data, validates the captcha, and validates the data before returning it for submission
        $encoded_data = $this->security->xss_clean($this->input->post());

        $decoded_data = [];
        foreach ($encoded_data as $key => $value) {
            if (strpos($key, '[]') !== false) {
                $decoded_key = decode(str_replace('[]', '', $key)) . '[]';
            } else {
                $decoded_key = decode($key);
            }
            $decoded_data[$decoded_key] = $value;
        }
        $data = $decoded_data;

        $this->validate_captcha($data['captcha']);

        $this->validate_data($encoded_data);

        return $data;
    }

    private function throttle_form_submission(){
        $max_submissions = 5; // Maximum number of submissions allowed
        $time_frame = 30; //30 seconds
    
        $current_time = time();
    
        $submission_history = $this->session->userdata('submission_history');
    
        if (!$submission_history) {
            $submission_history = [];
        }
    
        $submission_history = array_filter($submission_history, function($timestamp) use ($current_time, $time_frame) {
            return ($current_time - $timestamp) <= $time_frame;
        });
    
        if (count($submission_history) >= $max_submissions) {

            $response = [
                'timer_interval' => $time_frame * 1000,
                'csrf_hash'      => $this->refresh_hash()
            ];

            $this->output
                ->set_status_header(429)
                ->set_content_type('application/json')
                ->set_output(json_encode($response))
                ->_display();
            
            exit;
        }
    
        $submission_history[] = $current_time;
    
        $this->session->set_userdata('submission_history', $submission_history);
    }

	private function _check_if_employee($normalized_name){
		$sibling_db = sibling_one_db();

		$check_name = $this->main->check_data("{$sibling_db}.employee_tbl", array('employee_normalized_name' => $normalized_name, 'employee_status' => 1));

		return $check_name;
	}

    public function submit(){
        // $this->throttle_form_submission();
		$sibling_db = sibling_one_db();
        $survey_data = $this->validate_survey_submission();
        $form_data = $this->session->userdata('form_data');

        //json encodes all array values
        foreach($survey_data as $key => $value){
            if(is_array($value)){
                $survey_data[$key] = json_encode($value);
            }
        }
		

		switch ($form_data->form_id) {
			case 1:
				$form_tag = 'CDI';
				break;
			case 2:
				$form_tag = 'CTG';
				break;
			case 3:
				$form_tag = 'CTGQR';
				break;
			case 4:
				$form_tag = 'URQR';
				break;
			case 5:
				$form_tag = 'CHOOKSIES-';
				break;
			default:
				$form_tag = 'NA';
				break;
		}
        $reference_code = generate_reference($form_tag);

        $transaction_survey_reference = [];

        foreach ($survey_data as $key => $value) {
            $processed_key = preg_replace('/[0-9]+/', '', $key);
            $transaction_survey_reference[$processed_key] = $value;
        }

		$normalized_name = $this->_normalized_names($transaction_survey_reference[shorten_field_name('name', null)]);
		$duplicate_entry = FALSE;
		$conflict_emp = FALSE;
		
		if($form_data->form_id == 3 || $form_data->form_id == 4 || $form_data->form_id == 5){ //* QR PROMO SURVEY

			$is_employee = $this->_check_if_employee($normalized_name);
			if($is_employee){
				$conflict_emp = TRUE;
				$this->_conflict_employee_entry($reference_code, $form_data->form_id);
			} else {
				//* RESTRICT TO 1 ENTRY PER NAME
				// $filter = "DATE_FORMAT(created_at, '%Y-%m-%d') = '".date('Y-m-d')."' AND ( name = '".$transaction_survey_reference[shorten_field_name('name', null)]."' OR email = '".$transaction_survey_reference[shorten_field_name('email', null)]."' OR contact_number = '".$transaction_survey_reference[shorten_field_name('contact number', null)]."')";
				$filter = "(normalized_name = '".$normalized_name."' OR email = '".$transaction_survey_reference[shorten_field_name('email', null)]."' OR contact_number = '".$transaction_survey_reference[shorten_field_name('contact number', null)]."') and status = 1 and form_id = ".$form_data->form_id;
				$survey_ref = $this->main->get_data("{$sibling_db}.survey_reference_tbl", $filter, TRUE, ['name', 'email', 'contact_number']);
				
	
				if(!empty($survey_ref)){
					//* SURVEY ENTRY ALREADY EXIST BASED ON NAME
					$duplicate_entry = TRUE;
					$this->_duplicate_entry($reference_code, $form_data->form_id);
					
				}
			}
			
		}

		if(!$duplicate_entry && !$conflict_emp){

			// Handle OR photo upload if available
			// pretty_dump($_FILES);
			// pretty_dump($survey_data);
			
			// if (isset($_FILES['or_photo']) && $_FILES['or_photo']['error'] == UPLOAD_ERR_OK) {
			// 	$upload_path = FCPATH . 'assets/uploads/or_number/';
			// 	if (!is_dir($upload_path)) {
			// 		mkdir($upload_path, 0755, true);
			// 	}

			// 	$file = $_FILES['or_photo'];
			// 	$allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
			// 	$max_size = 5 * 1024 * 1024; // 5MB

			// 	// Security checks
			// 	$finfo = finfo_open(FILEINFO_MIME_TYPE);
			// 	$mime_type = finfo_file($finfo, $file['tmp_name']);
			// 	finfo_close($finfo);

			// 	if (!in_array($mime_type, $allowed_types)) {
			// 		$response = [
			// 			'errors' => ['or_photo' => 'Invalid file type. Only JPG and PNG images are allowed.'],
			// 			'csrf_hash' => $this->refresh_hash()
			// 		];
			// 		$this->output
			// 			->set_status_header(400)
			// 			->set_content_type('application/json')
			// 			->set_output(json_encode($response))
			// 			->_display();
			// 		exit;
			// 	}

			// 	// Generate unique file name
			// 	$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
			// 	$filename = uniqid('or_', true) . '.' . $ext;
			// 	$target_file = $upload_path . $filename;

			// 	// Compress and move the image
			// 	if ($mime_type == 'image/jpeg' || $mime_type == 'image/jpg') {
			// 		$image = imagecreatefromjpeg($file['tmp_name']);
			// 	} else {
			// 		$image = imagecreatefrompng($file['tmp_name']);
			// 	}

			// 	// Compress image to target file (quality 75)
			// 	imagejpeg($image, $target_file, 75);
			// 	imagedestroy($image);

			// 	// Check file size after compression
			// 	if (filesize($target_file) > $max_size) {
			// 		unlink($target_file);
			// 		$response = [
			// 			'errors' => ['or_photo' => 'The uploaded image is too large. Please upload a photo smaller than 5MB.'],
			// 			'csrf_hash' => $this->refresh_hash()
			// 		];
			// 		$this->output
			// 			->set_status_header(400)
			// 			->set_content_type('application/json')
			// 			->set_output(json_encode($response))
			// 			->_display();
			// 		exit;
			// 	}

			// 	// Save the relative path to survey_data
			// 	$survey_data['or_photo'] = 'assets/uploads/or_number/' . $filename;
			// } else {
			// 	$survey_data['or_photo'] = '';
			// }

			$or_photo = $this->input->post('or_photo');
			if (
				isset($or_photo) &&
				is_string($or_photo) &&
				strpos($or_photo, 'data:image/') === 0
			) {
				$upload_path = rtrim(FCPATH, '/\\') . '/assets/uploads/or_number/';
				if (!is_dir($upload_path)) {
					mkdir($upload_path, 0755, true);
					chmod($upload_path, 0755);
				}

				// Parse base64 string
				if (preg_match('/^data:image\/([a-zA-Z0-9]+);base64,/', $or_photo, $matches)) {
					$image_data = substr($or_photo, strpos($or_photo, ',') + 1);
					$image_type = strtolower($matches[1]); // jpg, png, gif

					if (!in_array($image_type, ['jpg', 'jpeg', 'png'])) {
						$response = [
							'errors' => ['or_photo' => 'Invalid image type. Only JPG and PNG images are allowed.'],
							'csrf_hash' => $this->refresh_hash()
						];
						$this->output
							->set_status_header(400)
							->set_content_type('application/json')
							->set_output(json_encode($response))
							->_display();
						exit;
					}

					$image_data = base64_decode($image_data);
					if ($image_data === false) {
						$response = [
							'errors' => ['or_photo' => 'Base64 decode failed.'],
							'csrf_hash' => $this->refresh_hash()
						];
						$this->output
							->set_status_header(400)
							->set_content_type('application/json')
							->set_output(json_encode($response))
							->_display();
						exit;
					}
					$filename = uniqid('or_', true) . '.' . $image_type;
					$target_file = $upload_path . $filename;
					$target_file = str_replace('\\', '/', $target_file); // Ensure forward slashes

					// Save and compress image
					if ($image_type == 'jpg' || $image_type == 'jpeg') {
						$image = imagecreatefromstring($image_data);
						imagejpeg($image, $target_file, 75);
						imagedestroy($image);
					} elseif ($image_type == 'png') {
						$image = imagecreatefromstring($image_data);
						imagepng($image, $target_file, 6);
						imagedestroy($image);
					}

					$max_size = 5 * 1024 * 1024; // 5MB
					if (filesize($target_file) > $max_size) {
						unlink($target_file);
						$response = [
							'errors' => ['or_photo' => 'The uploaded image is too large. Please upload a photo smaller than 5MB.'],
							'csrf_hash' => $this->refresh_hash()
						];
						$this->output
							->set_status_header(400)
							->set_content_type('application/json')
							->set_output(json_encode($response))
							->_display();
						exit;
					}

					$survey_data['or_photo'] = 'assets/uploads/or_number/' . $filename;
				} else {
					$survey_data['or_photo'] = '';
				}
			}

			$transaction_data = [
				'form_id' 							=> $form_data->form_id,
				'reference_number' 					=> encode($reference_code),
				'email' 							=> encode($transaction_survey_reference[shorten_field_name('email', null)]),
				'name' 								=> encode($transaction_survey_reference[shorten_field_name('name', null)]),
				'contact_number' 					=> encode($transaction_survey_reference[shorten_field_name('contact number', null)]),
				'created_at' 						=> date('Y-m-d H:i:s'),
				'or_number' 						=> $survey_data['or_number'] ?? '',
				'or_photo' 							=> $survey_data['or_photo'] ?? '',
				'store_id' 							=> $survey_data['store'] ?? '',
				'province_id' 						=> decode($survey_data['province']) ?? NULL,
				'town_group_id' 					=> decode($survey_data['town']) ?? NULL,
				'barangay_id' 						=> decode($survey_data['brgy']) ?? NULL,
				'status' 							=> 1
			];
	
	
			$transaction_id = $this->main->insert_data('transaction_form_tbl', $transaction_data, TRUE)['id'];
			if(in_array($form_data->form_id, [3, 4, 5])){
				$set = [
					'ref_id' 						=> $transaction_id,
					'form_id' 						=> $form_data->form_id,
					'ref_no' 						=> $reference_code,
					'email' 						=> strtolower($transaction_survey_reference[shorten_field_name('email', null)]),
					'name' 							=> ucwords(strtolower($transaction_survey_reference[shorten_field_name('name', null)])),
					'normalized_name' 				=> $normalized_name,
					'contact_number' 				=> $transaction_survey_reference[shorten_field_name('contact number', null)],
					'age' 							=> $transaction_survey_reference[shorten_field_name('age', null)],
					'sex' 							=> $transaction_survey_reference[shorten_field_name('sex', null)],
					'civil_status' 					=> $transaction_survey_reference[shorten_field_name('civil status', null)],
					'occupation' 					=> $transaction_survey_reference[shorten_field_name('occupation', null)],
					'birthday' 						=> $transaction_survey_reference[shorten_field_name('birthday', null)],
					'created_at' 					=> date('Y-m-d H:i:s'),
					'province_id' 					=> decode($survey_data['province']) ?? NULL,
					'town_group_id' 				=> decode($survey_data['town']) ?? NULL,
					'barangay_id' 					=> decode($survey_data['brgy']) ?? NULL,
					'address' 						=> $transaction_survey_reference[shorten_field_name('address', null)],
					'or_photo' 						=> $survey_data['or_photo'] ?? NULL,
					'or_number' 					=> $survey_data['or_number'] ?? NULL,
					'status' 						=> 1
				];
	
				$this->main->insert_data("{$sibling_db}.survey_reference_tbl", $set, TRUE);
			}
	
			foreach($form_data->sections as $section){
	
				$counter = 0;
				foreach($section->fields as $field){
					$shorten_field_name = shorten_field_name($field->form_field_name, $counter);
					$counter++;
	
					$field_name = $field->form_field_name;
					$field_id = $field->field_id;
	
					if(isset($survey_data[$shorten_field_name])){
						$field_value = $survey_data[$shorten_field_name];
					}else{
						$field_value = 'N/A';
					}
	
					$field_data = [
						'field_id'       => $field_id,
						'field_name'     => $field_name,
						'transaction_id' => $transaction_id,
						'response'       => encode($field_value),
						'created_at'     => date('Y-m-d H:i:s'),
					];
					
					$this->main->insert_data('transaction_response_tbl', $field_data);
				}
			}
	
			$this->session->set_tempdata('form_submitted', true, 900); // Set the success page to be accessible for 15 minutes
			$this->session->set_userdata('reference_code', $reference_code);
			$this->session->unset_userdata('submission_history', $this->session->userdata('submission_history'));
			
			$name = ucwords(strtolower($transaction_survey_reference[shorten_field_name('name', null)]));
			$name = str_replace(' ', '-', $name);
			$data = [
				'redirect' => base_url('/success/'.encode($form_data->form_id).'/'.$name)
			];
	
			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($data));
		}
    }


	private function _duplicate_entry($reference_code, $form_id){
		$this->session->set_tempdata('form_submitted', true, 900); // Set the success page to be accessible for 15 minutes
        $this->session->set_userdata('reference_code', $reference_code);
        $this->session->unset_userdata('submission_history', $this->session->userdata('submission_history'));

        $data = [
            'redirect' => base_url('/duplicate/'.encode($form_id))
        ];

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
		
	}
	
	private function _conflict_employee_entry($reference_code, $form_id){
		$this->session->set_tempdata('form_submitted', true, 900); // Set the success page to be accessible for 15 minutes
        $this->session->set_userdata('reference_code', $reference_code);
        $this->session->unset_userdata('submission_history', $this->session->userdata('submission_history'));

        $data = [
            'redirect' => base_url('/conflict/'.encode($form_id))
        ];

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
		
	}

	public function sample_compare(){
		$full_name = "jenebeve v. cabangunay";
		echo $full_name = $this->_normalized_names($full_name);
		exit;
		echo $this->_is_name_unique($full_name);
	}

	private function _is_name_unique($full_name) {
		$sibling_db = sibling_one_db();
		//* Sanitize the input name
		$inputName = strtolower($full_name);
	
		//* Split the input name into words and sort them
		$inputWords = explode(' ', $inputName);
		sort($inputWords);
		$sortedInputName = implode(' ', $inputWords);

		// return $sortedInputName;
	
		//* Prepare the SQL query
		$filter = "status = 1";
		$survey_ref = $this->main->get_data("{$sibling_db}.survey_reference_tbl", $filter, TRUE, ['name', 'email', 'contact_number']);
	
		if (!empty($survey_ref)) {
			// $survey_ref = array_column($survey_ref, 'normalized_name');
			foreach($survey_ref as $r){
				$normalized_name = trim(strtolower($r->normalized_name));
	
				//* Split and sort the database name field
				$dbWords = explode(' ', $normalized_name);
				sort($dbWords);
				$sortedDbName = implode(' ', $dbWords);
	
				//* Compare the sorted names
				if ($sortedInputName === $sortedDbName) {
					return false; //* Not unique
				}
			}
		}
	
		return true; //* Unique
	}

	private function _normalized_names_back($full_name){
		// $full_name = 'Aljune K. Atok';

		$full_name = strtolower($full_name);

		//* Remove punctuation
		$full_name = preg_replace("/[^\w\s]/", "", $full_name);

		//* Remove extra spaces
		$full_name = preg_replace("/\s+/", " ", $full_name);
		$full_name = trim($full_name);

		//* Split into words and sort
		$parts = explode(" ", $full_name);
		sort($parts);
		$sortedInputName = implode(' ', $parts);

		$new_name = '';
		foreach($parts as $part){
			$new_name .= $part;
		}

		$new_name = preg_replace("/\s+/", " ", $full_name);
		$new_name = trim($new_name);
		$new_name = ucwords($new_name);

		return $new_name;
	}
	
	private function _normalized_names($full_name){
		// $full_name = 'Aljune K. Atok';

		$full_name = strtolower($full_name);

		//* Remove punctuation
		$full_name = preg_replace("/[^\w\s]/", "", $full_name);

		//* Remove extra spaces
		$full_name = preg_replace("/\s+/", " ", $full_name);
		$full_name = trim($full_name);

		//* Split into words and sort
		$parts = explode(" ", $full_name);
		sort($parts);
		$sortedInputName = implode(' ', $parts);


		$new_name = preg_replace("/\s+/", " ", $sortedInputName);
		$new_name = trim($new_name);
		$new_name = ucwords($new_name);

		return $new_name;
	}

	

    public function success($form_id, $name)
    {	
		$parent_db = parent_db();
		$sibling_db = sibling_one_db();
		$form_id = decode($form_id);
		$name = str_replace('-', ' ', $name);
		// $form_id = 4;
        if (!$this->session->tempdata('form_submitted')) {
            show_404();
        }
		
		$join_town = array(
			"{$sibling_db}.coupon_prod_sale_tbl b" => 'a.coupon_id = b.coupon_id AND a.survey_freebie_cal_status = 1 AND a.freebie_date = "' . date("Y-m-d") . '"',
			"{$parent_db}.product_sale_tbl c" => 'b.prod_sale_id = c.prod_sale_id',
		);
		$prod_saleable = $this->main->get_join("{$sibling_db}.survey_freebie_calendar_tbl a", $join_town, FALSE, 'c.prod_sale_name ASC', 'a.brand_id, b.prod_sale_id', 'COUNT(a.survey_freebie_cal_id) as prod_count, a.brand_id, c.prod_sale_name, c.prod_sale_promo_name');

		
		$prod_sale_count_name = 'SOME PRODUCTS';
		foreach($prod_saleable as $r){
			if($form_id == 3){ // CTG QR PROMO
				if($r->brand_id == 1){
					$prod_sale_count_name = '<strong>'.$r->prod_count.' FREE '.$r->prod_sale_promo_name.'</strong>';
				}
			}
			elseif($form_id == 4){ // UR QR PROMO
				if($r->brand_id == 2){
					$prod_sale_count_name = '<strong>'.$r->prod_count.' FREE '.$r->prod_sale_promo_name.'</strong>';
				}
			}
		}

		
		if($form_id <= 2){
			$view_file							= $this->controller . '/success';
			$mod_form_name						= NULL;
			$mod_footer_name					= NULL;
			$thanks_msg							= NULL;
			$hash_tag_msg						= NULL;
			$prod_sale_count_name				= NULL;
		}
		elseif($form_id >= 3 && $form_id <= 5){
			$form_var 							= $this->_form_variation($form_id, 'success');
			$view_file							= $form_var->view_file;
			$mod_form_name						= $form_var->mod_form_name;
			$mod_footer_name					= $form_var->mod_footer_name;
			$thanks_msg							= $form_var->thanks_msg;
			$hash_tag_msg						= $form_var->hash_tag_msg;
			$form_brand_logo					= $form_var->form_brand_logo;
			$form_socials						= $form_var->form_socials;
			$page_css_class						= $form_var->page_css_class;
			$brand_name							= $form_id == 3 || $form_id == 5 ? 'Chooks-To-Go' : 'Uling Roasters';
		}

		$sucess_page_resources = [
			'controller'						=> $this->controller,
			'title'								=> ucwords(str_replace('_', ' ', __FUNCTION__)),
			'reference_code'					=> $this->session->userdata('reference_code') ?? '',
			'thanks_msg'						=> $thanks_msg,
			'hash_tag_msg'						=> $hash_tag_msg,
			'prod_sale_count_name'				=> $prod_sale_count_name,
			'form_brand_logo'					=> $form_brand_logo,
			'form_socials'						=> $form_socials,
			'page_css_class'					=> $page_css_class,
			'brand_name'						=> $brand_name,
			'name'								=> $name,
			'form_id'							=> $form_id,
		];

		$view  = $this->load->view($view_file, $sucess_page_resources, TRUE);

		$template_data = [
			'title'								=> ucwords(str_replace('_', ' ', __FUNCTION__)),
			'mod_form_name'						=> $mod_form_name,
            'mod_footer_name'					=> $mod_footer_name,
			'content'							=> $view,
		];

        $this->load->view($this->controller . '/template', $template_data);
    }

	public function ctg_qr_promo_survey(){
        $form_id = 3;
		$this->_proceed_form($form_id);
    }
	
	public function ur_qr_promo_survey(){
        $form_id = 4;
		$this->_proceed_form($form_id);
    }
	
	public function chooksies_qr_promo_survey(){
        $form_id = 5;
		$this->_proceed_form($form_id);
    }

	private function _proceed_form($form_id){
		$parent_db = parent_db();
		$sibling_db = sibling_one_db();

		$check_form = $this->main->check_data('form_tbl', ['form_id' => $form_id], TRUE);
        if($check_form['result']){
			if($check_form['info']->status == 0){
				$this->load_maintenance_page();
				return;
			}
        }

		if($check_form['info']->start_date > date('Y-m-d H:i:s')){
			//* FORM NOT YET STARTED
			$this->_not_valid($form_id, 1);
		}
		elseif($check_form['info']->end_date < date('Y-m-d H:i:s')){
			//* FORM ALREADY ENDED
			$this->_not_valid($form_id, 2);
		}
		else{
			$form_var 							= $this->_form_variation($form_id);
			$survey_data 						= $this->load_form($form_id);
			$captcha        					= load_captcha();
			$this->session->set_userdata('captcha-word', $captcha['word']);
			$this->session->set_userdata('form_data', $survey_data['form_data']);
			
			$get_participating_bcs 				= $this->main->get_data("{$sibling_db}.survey_participating_bcs_tbl", ['form_id' => $form_id, 'survey_participating_bc_status' => 1]);
			$bc_id_array = !empty($get_participating_bcs) ? array_column($get_participating_bcs, 'bc_id') : NULL;
			$bc_ids = !empty($bc_id_array) ? join(",", $bc_id_array) : 0;
			
			// $bc_code = '1030';
			$join_tbl = [
				"{$parent_db}.bc_tbl b" 		=> 'a.bc_id = b.bc_id AND b.bc_id IN ('.$bc_ids.')'
			];
			$provinces 							= $this->main->get_join("{$parent_db}.provinces_tbl a", $join_tbl, FALSE, 'a.province_name', 'a.province_id', 'a.province_id, a.province_name', ['a.province_status' => 1]);
			
	
			$survey_resources = [
				'survey_content' 				=> $survey_data['content'],
				'controller'     				=> $this->controller,
				'captcha'        				=> $captcha,
				'provinces'      				=> $provinces,
				'form_id'      					=> $form_id,
				// 'company_name'					=> $form_var->mod_footer_name,
				'company_name'					=> $form_var->mod_footer_name == 'Chooks To Go, Inc.' ? 'Chooks To Go' : 'Uling Roasters',
			];
	
			$content = $this->load->view($this->controller . '/qr_promo_survey', $survey_resources, TRUE);
	
			$page_resources = [
				'title'							=> $form_var->mod_form_name,
				'content'						=> $content,
				'mod_form_name'					=> $form_var->mod_form_name,
				'mod_footer_name'				=> $form_var->mod_footer_name,
			];
	
			$this->load->view($this->controller . '/template', $page_resources);
		}
	}

	public function duplicate($form_id)
    {	
		$form_id = decode($form_id);
		// $form_id = 4;
        if (!$this->session->tempdata('form_submitted')) {
            show_404();
        }

		$form_var 								= $this->_form_variation($form_id, 'duplicate');
		$view_file								= $form_var->view_file;
		$mod_form_name							= $form_var->mod_form_name;
		$mod_footer_name						= $form_var->mod_footer_name;
		$thanks_msg								= $form_var->thanks_msg;
		$hash_tag_msg							= $form_var->hash_tag_msg;
		$form_brand_logo						= $form_var->form_brand_logo;
		$form_socials							= $form_var->form_socials;
		$page_css_class							= $form_var->page_css_class;


		$sucess_page_resources = [
			'controller'						=> $this->controller,
			'title'								=> ucwords(str_replace('_', ' ', __FUNCTION__)),
			'reference_code'					=> $this->session->userdata('reference_code') ?? '',
			'thanks_msg'						=> $thanks_msg,
			'hash_tag_msg'						=> $hash_tag_msg,
			'form_brand_logo'					=> $form_brand_logo,
			'form_socials'						=> $form_socials,
			'page_css_class'					=> $page_css_class,
		];

		$view  = $this->load->view($view_file, $sucess_page_resources, TRUE);

		$template_data = [
			'title'								=> ucwords(str_replace('_', ' ', __FUNCTION__)),
			'mod_form_name'						=> $mod_form_name,
            'mod_footer_name'					=> $mod_footer_name,
			'content'							=> $view,
		];

        $this->load->view($this->controller . '/template', $template_data);
    }
	
	public function conflict($form_id)
    {	
		$form_id = decode($form_id);
		// $form_id = 4;
        if (!$this->session->tempdata('form_submitted')) {
            show_404();
        }

		$form_var 								= $this->_form_variation($form_id, 'conflict');
		$view_file								= $form_var->view_file;
		$mod_form_name							= $form_var->mod_form_name;
		$mod_footer_name						= $form_var->mod_footer_name;
		$thanks_msg								= $form_var->thanks_msg;
		$hash_tag_msg							= $form_var->hash_tag_msg;
		$form_brand_logo						= $form_var->form_brand_logo;
		$form_socials							= $form_var->form_socials;
		$page_css_class							= $form_var->page_css_class;


		$sucess_page_resources = [
			'controller'						=> $this->controller,
			'title'								=> ucwords(str_replace('_', ' ', __FUNCTION__)),
			'reference_code'					=> $this->session->userdata('reference_code') ?? '',
			'thanks_msg'						=> $thanks_msg,
			'hash_tag_msg'						=> $hash_tag_msg,
			'form_brand_logo'					=> $form_brand_logo,
			'form_socials'						=> $form_socials,
			'page_css_class'					=> $page_css_class,
		];

		$view  = $this->load->view($view_file, $sucess_page_resources, TRUE);

		$template_data = [
			'title'								=> ucwords(str_replace('_', ' ', __FUNCTION__)),
			'mod_form_name'						=> $mod_form_name,
            'mod_footer_name'					=> $mod_footer_name,
			'content'							=> $view,
		];

        $this->load->view($this->controller . '/template', $template_data);
    }

	private function _not_valid($form_id, $type){
		
		$form_var 								= $this->_form_variation($form_id, 'not_valid');
		$view_file								= $form_var->view_file;
		$mod_form_name							= $form_var->mod_form_name;
		$mod_footer_name						= $form_var->mod_footer_name;
		$thanks_msg								= $form_var->thanks_msg;
		$hash_tag_msg							= $form_var->hash_tag_msg;
		$form_brand_logo						= $form_var->form_brand_logo;
		$form_socials							= $form_var->form_socials;
		$page_css_class							= $form_var->page_css_class;
		$expired_msg							= '
			<p class="text-center mt-2" style="font-size:1.3rem">This survey form has ended in taking responses!</p>
		';
		$not_started_msg						= '
			<p class="text-center mt-2" style="font-size:1.3rem">This survey form has not yet started taking responses!</p>
		';
		$expired_head							= '
			<h1 class="text-center font-chunkfive">Survey form ended!</h1>
		';
		$not_started_head						= '
			<h1 class="text-center font-chunkfive">Survey form not yet started!</h1>
		';
		$message					= $type == 2 ? $expired_msg : $not_started_msg;
		$headline					= $type == 2 ? $expired_head : $not_started_head;

		$sucess_page_resources = [
			'controller'						=> $this->controller,
			'title'					     		=> ucwords(str_replace('_', ' ', __FUNCTION__)),
			'reference_code'					=> $this->session->userdata('reference_code') ?? '',
			'thanks_msg'						=> $thanks_msg,
			'hash_tag_msg'						=> $hash_tag_msg,
			'form_brand_logo'					=> $form_brand_logo,
			'form_socials'						=> $form_socials,
			'message'							=> $message,
			'headline'							=> $headline,
			'page_css_class'					=> $page_css_class,
		];

		$view  = $this->load->view($view_file, $sucess_page_resources, TRUE);

		$template_data = [
			'title'								=> ucwords(str_replace('_', ' ', __FUNCTION__)),
			'mod_form_name'						=> $mod_form_name,
            'mod_footer_name'					=> $mod_footer_name,
			'content'							=> $view,
		];

        $this->load->view($this->controller . '/template', $template_data);
	}

	private function _form_variation($form_id, $suffix=""){
		$form = $this->main->get_data('form_tbl a', ['form_id' => $form_id], TRUE, 'a.*');

		$data['start_date'] 					= $form->start_date;
		$data['end_date']						= $form->end_date;
		$data['mod_form_name']					= $form->form_name;
		$data['mod_footer_name']				= $form->company_name;
		$data['view_file']						= $this->controller . '/qr_promo_'.$suffix;

		if($form_id == 3){
			$data['page_css_class'] 			= 'banner-bg';
			// $data['mod_footer_name'] 			= 'Chooks to Go Inc.';
			$data['thanks_msg'] 				= 'Thank you Ka-Chooks';
			$data['hash_tag_msg'] 				= '#MasarapKahitWalangSauce';
			$data['form_brand_logo'] 			= '
			<div class="d-flex justify-content-center">
				<img class="" src="'.base_url('assets\img\chooks-logo-transparent.png').'"> 
			</div>';
			$data['form_socials'] 				= '
			<div class="d-flex justify-content-around w-50 m-auto">
                <a href="https://www.facebook.com/chookstogo/" class="socials-icon" target="_blank"><h3 class="fab fa-facebook-f"></h3></a>
                <a href="https://www.instagram.com/chookstogoph/" class="socials-icon" target="_blank"><h3 class="fab fa-instagram"></h3></a>
                <a href="https://www.youtube.com/channel/UC1mn-pF58NABjDwMoaLeeyQ" class="socials-icon" target="_blank"><h3 class="fab fa-youtube"></h3></a>
            </div>';
		}
		elseif($form_id == 4){
			$data['page_css_class'] 			= 'ur-banner-bg';
			// $data['mod_footer_name']			= 'Uling Roasters';
			$data['thanks_msg']					= 'Thank you Ka-UR';
			$data['hash_tag_msg']				= '#DiRawMasyadongMasarap #PeroPwedeNa';
			$data['form_brand_logo'] 			= '
			<div class="d-flex justify-content-center">
				<img class="" src="'.base_url('assets\img\Uling Roasters Logo - transparent.png').'"> 
			</div>';
			$data['form_socials'] 				= '
			<div class="d-flex justify-content-around w-50 m-auto">
                <a href="https://www.facebook.com/ulingroasters" class="socials-icon" target="_blank"><h3 class="fab fa-facebook-f"></h3></a>
                <a href="https://www.instagram.com/ulingroasters" class="socials-icon" target="_blank"><h3 class="fab fa-instagram"></h3></a>
                <a href="https://www.youtube.com/@ulingroasters7544" class="socials-icon" target="_blank"><h3 class="fab fa-youtube"></h3></a>
            </div>';
		}
		else {
			$data['page_css_class'] 			= 'banner-bg';
			// $data['mod_footer_name'] 			= 'Chooks to Go Inc.';
			$data['thanks_msg'] 				= 'Thank you Ka-Chooks';
			$data['hash_tag_msg'] 				= '#MasarapKahitWalangSauce';
			$data['form_brand_logo'] 			= '
			<div class="d-flex justify-content-center">
				<img class="" src="'.base_url('assets\img\chooks-logo-transparent.png').'"> 
			</div>';
			$data['form_socials'] 				= '
			<div class="d-flex justify-content-around w-50 m-auto">
                <a href="https://www.facebook.com/chookstogo/" class="socials-icon" target="_blank"><h3 class="fab fa-facebook-f"></h3></a>
                <a href="https://www.instagram.com/chookstogoph/" class="socials-icon" target="_blank"><h3 class="fab fa-instagram"></h3></a>
                <a href="https://www.youtube.com/channel/UC1mn-pF58NABjDwMoaLeeyQ" class="socials-icon" target="_blank"><h3 class="fab fa-youtube"></h3></a>
            </div>';
		}

		$object = (object) $data;
		return $object;
	}

}
