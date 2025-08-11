<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct() {
    	parent::__construct();
    	$this->load->model('main_model', 'main');
	}

	public function _error_page()
    {
		show_404();
    }

	public function _require_login()
	{
		$login = $this->session->userdata(system_default()['session_name']);
		if(isset($login)){
			$employee_type = decode($login['employee_type_id']);
			if($login['employee_reset'] == 0){
				if($employee_type == '1'){
					redirect('admin');
				}elseif($employee_type == '2' || $employee_type == '3'){
					redirect('redeemer');
				}else{
					$this->session->unset_userdata(system_default()['session_name']);
				}
			}else{
				$this->session->unset_userdata(system_default()['session_name']);
				redirect('login/change-password/' . $login['employee_id']);
			}
		}else{
			$this->session->unset_userdata(system_default()['session_name']);
		}
	}

	public function index()
	{
		$info = $this->_require_login();
		$data['title'] = system_default()['system_name'];
		$this->load->view('login/login_content', $data);

	}

	public function logout_process()
	{
		$this->session->unset_userdata(system_default()['session_name']);
		redirect('login');
	}

	public function login_process()
	{
		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			redirect('login');
		}
		
		$rules = [
			[ 'field' => 'email'   , 'label' => 'Email'   , 'rules' => 'required' ],
			[ 'field' => 'password', 'label' => 'Password', 'rules' => 'required' ],
		];

        $validation = validate_post_data($rules);
        if (!$validation['result']) {
            echo json_encode($validation);
            return;
        }

		$email       = trim(clean_data($this->input->post('email')));
		$password    = trim(clean_data($this->input->post('password')));
		$join        = [
			'employee_types_tbl b' => 'b.employee_type_id = a.employee_type_id AND a.employee_status = 1 AND b.employee_type_login = 1 AND a.employee_email = "' . $email . '"'
		];
		$check_login = $this->main->check_join('employees_tbl a', $join, TRUE);

		if ($check_login['result']) {
            if (password_verify($password, $check_login['info']->employee_password)) {
				$employee_id = $check_login['info']->employee_id;
				$name        = $check_login['info']->employee_fname;
				$email       = $check_login['info']->employee_email;

				$user_location = $this->main->get_data('employee_locations_tbl', ['employee_id' => $employee_id, 'employee_location_status' => 1], TRUE);
				$unit          = $this->main->get_data('units_tbl', ['unit_id' => $check_login['info']->unit_id], TRUE);
				$location      = $this->main->get_data('locations_tbl', ['location_id' => $user_location->location_id], TRUE);

				$session = array(
					'employee_id'      => encode($check_login['info']->employee_id),
					'employee_type_id' => encode($check_login['info']->employee_type_id),
					'full_name'        => $check_login['info']->employee_fname . ' ' . $check_login['info']->employee_lname,
					'employee_reset'   => $check_login['info']->employee_reset,
					'unit_location'    => $unit->unit_name . ' | ' . $location->location_name
				);

				$this->_store_login_log($check_login['info']->employee_id);
				if($check_login['info']->employee_status == 1){
					if($check_login['info']->employee_type_id == '1'){
						$this->session->set_userdata(system_default()['session_name'], $session);
						$result = [
							'result'   => 1,
							'message'  => 'Login Success',
							'redirect' => 'admin',
						];
					} elseif($check_login['info']->employee_type_id == '2' || $check_login['info']->employee_type_id == '3'){
						$this->session->set_userdata(system_default()['session_name'], $session);
						$result = [
							'result'   => 1,
							'message'  => 'Login Success',
							'redirect' => 'redeemer',
						];
					}else{
						$result = [
							'result'  => 0,
							'message' => 'Invalid email and password',
						];
					}						
				}else{
					$result = [
						'result'  => 0,
						'message' => 'Error please contact your administrator.',
					];
				}
			}else{
				$result = [
					'result'  => 0,
					'message' => 'Invalid email and password',
				];
			}
		
		}else{
			$result = [
				'result'  => 0,
				'message' => 'Invalid email and password',
			];
		}

		echo json_encode($result);
		return;
	}

	public function change_password($id = null)
	{
		$employee_id   = decode($id);
		if(!empty($employee_id)){
			$check_id = $this->main->check_data('employees_tbl', "employee_id = {$employee_id} AND employee_reset = 1 AND employee_type_id IN (1,2,3)");
			if($check_id == TRUE){
				$data['employee_id'] = encode($employee_id);
				$data['title'] = system_default()['system_name'];
				$this->load->view('login/change_password_content', $data);
			}else{
				redirect(base_url());
			}
		}else{
			redirect(base_url());
		}
	}

	public function change_process()
	{
		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			redirect(base_url());
		}

		$rules = [
			[
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'required'
			],
			[
				'field' => 'repeat_password',
				'label' => 'Repeat Password',
				'rules' => 'required'
			],
		];

		$this->load->library('form_validation');
		$this->form_validation
			->set_rules($rules)
			->set_error_delimiters(
				'<div class="alert alert-danger alert-dismissible fade show" role="alert">', 
					'<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('message', validation_errors());
			redirect('login');
		}

		$employee_id = clean_data(decode($this->input->post('id')));
		$password    = clean_data($this->input->post('password'));
		$rpassword   = clean_data($this->input->post('repeat_password'));

		$check_id  = $this->main->check_data('employees_tbl', "employee_id = {$employee_id} AND employee_reset = 1 AND employee_type_id IN (1,2,3)");
		if($check_id == TRUE){
			if($password == $rpassword){

				$set = array(
					'employee_password' => password_hash($password, PASSWORD_DEFAULT),
					'employee_reset'    => 0
				);

				$where  = array('employee_id' => $employee_id);
				$result = $this->main->update_data('employees_tbl', $set, $where);

				if($result == TRUE){
					$msg = '<div class="alert alert-success">Login now with your new password.</div>';
					$this->session->set_flashdata('message', $msg);
					redirect('login');
				}else{
					$msg = '<div class="alert alert-danger">Error please try again.</div>';
					$this->session->set_flashdata('message', $msg);
					redirect('login');
				}
			}else{
				$msg = '<div class="alert alert-danger">Password not match. Please try again!</div>';
				$this->session->set_flashdata('message', $msg);
				redirect('login/change-password/' . encode($employee_id));
			}
		}else{
			redirect();	
		}
	}

    private function _store_login_log($employee_id)
    {
        $data = [
            'employee_id'         => $employee_id,
            'login_log_added' => date_now()
        ];
        $this->main->insert_data('login_log_tbl', $data);
    }

    public function view_email_otp(){
		$info = $this->_require_login();

		$data['title']    = system_default()['system_name'];
		$data['otp_code'] = 'Afw31';
		$data['name']     = 'Jose';
		$this->load->view('login/email_otp_content', $data, TRUE);
	}

	public function otp_email_content($name, $otp_code){
		$info = $this->_require_login();

		$data['title']    = system_default()['system_name'];
		$data['otp_code'] = $otp_code;
		$data['name']     = $name;
		
		$content = $this->load->view('login/email_otp_content', $data, TRUE);
		
		return $content;
	}

	private function _send_email($recipient, $email_content, $subject){

        $config = [ 
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://server10.synermaxx.net',
            'smtp_port' => 465,
            'smtp_user' => 'alerts@bountyagro.com.ph',
            'smtp_pass' => '',
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'wordwrap'  => TRUE
        ];
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n")
                    ->from('alerts@bountyagro.com.ph', 'merrier Notification')
                    ->to($recipient)
                    ->subject($subject)
                    ->message($email_content);

        if($this->email->send()){
            $email_result = [
                'result' => TRUE,
                'msg'    => 'Email Sent'
            ];
            //$this->_store_email_log($email_result, $recipient);
            return $email_result;
        }else{
            $email_result = [
                'result' => FALSE,
                'msg'    => $this->email->print_debugger()
            ];
            //$this->_store_email_log($email_result, $recipient);
            return $email_result;
        }
    }

}
