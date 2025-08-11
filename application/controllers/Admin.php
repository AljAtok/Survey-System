<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    protected $user_permissions = [];

    public function __construct()
    {
        parent::__construct();

        $this->controller = strtolower(__CLASS__);
        $this->component_path = $this->controller . '/form/form_builder_components/';

    	$this->load->model('main_model', 'main');
        
        $session_name = system_default()['session_name'];
        if ($session_name) {
            $session_data = $this->session->userdata($session_name);
            $employee_id = clean_data(decode($this->session->userdata(system_default()['session_name'])['employee_id']));

            if (!isset($session_data['user_permissions'])) {
                $this->db->select('modules_tbl.name AS module, module_actions_tbl.action AS action');
                $this->db->from('employee_permissions_tbl');
                $this->db->join('permissions_tbl', 'employee_permissions_tbl.permission_id = permissions_tbl.id', 'inner');
                $this->db->join('module_actions_tbl', 'permissions_tbl.action_id = module_actions_tbl.id', 'inner');
                $this->db->join('modules_tbl', 'permissions_tbl.module_id = modules_tbl.id', 'inner');
                $this->db->where('employee_permissions_tbl.employee_id', $employee_id);
                $this->db->where('employee_permissions_tbl.status', 1); // Ensure active permissions
                $this->db->where('permissions_tbl.status', 1);          // Ensure active permissions
                $this->db->where('module_actions_tbl.status', 1);       // Ensure active actions
                $this->db->where('modules_tbl.status', 1);              // Ensure active modules
                $query = $this->db->get();
                
                if ($query->num_rows() > 0) {
                    $permissions = [];
                    foreach ($query->result_array() as $row) {
                        $permissions[$row['module']][] = $row['action'];
                    }
                } else {
                    $permissions = [];
                }

                $session_data['user_permissions'] = $permissions;

                $this->session->set_userdata([$session_name => $session_data]);

                $this->user_permissions = $permissions;
            } else {
                $this->user_permissions = $session_data['user_permissions'];
            }

            //Logs actions for non-POST & non-AJAX requests
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !$this->input->is_ajax_request()) {
                store_log(1, 'VIEW', $employee_id);
            }
        }
    }

    public function index()
    {
        require_login($this->controller);
        redirect($this->controller . '/employees'); 
        return;
    }

    //PROFILE INFO & PASSWORD RESET
    public function account_settings()
    {
        $login_info      				= require_login($this->controller);
        $emp_id          				= decode($login_info['employee_id']);

        $forms 							= $this->_get_form_access($emp_id);
		$form_access_links 				= array_column($forms, 'internal_link');
        $report_menus 					= $this->_get_form_menu($forms);

        $title = ucwords(str_replace('_', ' ', __FUNCTION__));

        $account_setting_page_resources = [
            'title' => $title,
            'controller' => $this->controller,
            'employee' => $this->main->get_data('employees_tbl', ['employee_id' => $emp_id], TRUE),
        ];

        $content = $this->load->view($this->controller . '/account_setting_content', $account_setting_page_resources, TRUE);
        
        $template_resources = [
            'content' 					=> $content,
            'js_scripts' 				=> [
                'account_settings/account_setting_content_page.js',
            ],
			'form_access_links'         => $form_access_links,
            'report_menus'         		=> $report_menus,
        ];

        $this->load->view($this->controller.'/template', $template_resources);
    }

    public function check_current_password($field_value)
    {
        $info   = require_login($this->controller);
        $emp_id = decode($info['employee_id']);
    
        if (empty($field_value)) {
            $this->form_validation->set_message('check_curr_password', 'The %s field is required.');
            return FALSE;
        }
    
        $get_emp = $this->main->get_data('employees_tbl', ['employee_id' => $emp_id, 'employee_status' => 1], TRUE);
    
        if ($get_emp && password_verify($field_value, $get_emp->employee_password)) {
            return TRUE;
        } else {
            $this->form_validation->set_message('check_curr_password', 'The %s field is incorrect.');
            return FALSE;
        }
    }
    public function check_new_pass_employee($field_value)
    {
        $info        = require_login($this->controller);
        $emp_id      = decode($info['employee_id']);
        $field_value = decode($field_value);
    
        if (empty($field_value)) {
            $this->form_validation->set_message('check_new_pass_employee', 'The %s field is invalid.');
            return FALSE;
        }
    
        if ($emp_id == $field_value) {
            $check_emp = $this->main->check_data('employees_tbl', ['employee_id' => $emp_id, 'employee_status' => 1]);
            if ($check_emp) {
                return TRUE;
            } else {
                $this->form_validation->set_message('check_new_pass_employee', 'Employee ID does not exist.');
                return FALSE;
            }
        } else {
            $this->form_validation->set_message('check_new_pass_employee', 'Employee ID mismatch.');
            return FALSE;
        }
    }
    public function store_new_password()
    {
        $info = require_login($this->controller);

        $rules = [
            ['field' => 'id', 'label' => 'Employee ID', 'rules' => 'required|callback_check_new_pass_employee'],
            ['field' => 'currentpassword', 'label' => 'Current Password', 'rules' => 'required|callback_check_current_password'],
            ['field' => 'newpassword', 'label' => 'New Password', 'rules' => 'required|min_length[8]'],
            ['field' => 'repeatnewpassword', 'label' => 'Repeat New Password', 'rules' => 'required|matches[newpassword]'],
        ];

        foreach ($rules as $key => $value) {
            $this->form_validation->set_rules($value['field'], $value['label'], $value['rules'], $value['errors'] ?? []);
        }

        if ($this->form_validation->run() == FALSE) { //Look into converting this into a helper function
            $errors = $this->form_validation->error_array();
            $response = [
                'errors'    => $errors,
            ];

            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode($response))
                ->_display();
            
            exit;
        }

        $employee_id = decode($info['employee_id']);
        $newpassword = clean_data($this->input->post('newpassword'));

        $set   = ['employee_password' => password_hash($newpassword, PASSWORD_DEFAULT) ];
        $where = ['employee_id' => $employee_id];
        
        $this->db->trans_start();
        $this->main->update_data('employees_tbl', $set, $where);

        if ($this->db->trans_status()) {
            $this->db->trans_commit();
            $response = [
                'status'   => 200,
                'message'  => 'Password has been updated',
            ];
        } else {
            $this->db->trans_rollback();
            $response = [
                'status'   => 400,
                'message'  => 'Registraion Failed',
            ];
        }

        $this->output
            ->set_status_header($response['status'])
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    //EMPLOYEE MANAGEMENT
    public function employees()
    { 
        $info = require_login($this->controller);
		$employee_id = decode($info['employee_id']);

		$forms 							= $this->_get_form_access($employee_id);
		$form_access_links 				= array_column($forms, 'internal_link');
        $report_menus 					= $this->_get_form_menu($forms);

        $employee_page_resources = [
            'controller' => $this->controller,
            'title' => ucwords(str_replace('_', ' ', __FUNCTION__)),
            'js_scripts' => [
                'employee/employee_content_page.js',
            ]
        ];

        $data['content']          			= $this->load->view($this->controller . '/employee/employee_content', $employee_page_resources, TRUE);
        $data['form_access_links']          = $form_access_links;
        $data['report_menus']          		= $report_menus;
        $this->load->view($this->controller . '/template', $data);
    }

    public function load_employee_list_table()
    {
        require_login($this->controller);

        $draw = intval($this->input->get('draw'));
        $start = intval($this->input->get('start'));
        $length = intval($this->input->get('length'));
        $search = $this->input->get('search')['value'];

        $subquery = $this->db->select('z.employee_id')
                     ->from('employee_locations_tbl z')
                     ->join('locations_tbl x', 'x.location_id = z.location_id')
                     ->where('z.employee_id = a.employee_id')
                     ->like('x.location_name', $search)
                     ->get_compiled_select();

        $this->db->select('*, 
            (SELECT GROUP_CONCAT(x.location_name SEPARATOR ", ") FROM employee_locations_tbl z JOIN locations_tbl x ON x.location_id = z.location_id WHERE z.employee_id = a.employee_id) as "location_name",
            ')
                ->from('employees_tbl a')
                ->join('employee_types_tbl b', 'b.employee_type_id = a.employee_type_id')
                ->join('units_tbl d', 'd.unit_id = a.unit_id')
                ->join('employment_types_tbl e', 'e.employment_type_id = a.employment_type_id')
                ->limit($length, $start);

        if (!empty($search)) {
            $this->db->group_start()
                    ->like('CONCAT(a.employee_fname, " ", a.employee_lname)', $search)
                    ->or_like('a.employee_no', $search)
                    ->or_like('a.employee_email', $search)
                    ->or_like('a.employee_contact', $search)
                    ->or_like('d.unit_name', $search)
                    ->or_like('b.employee_type_name', $search)
                    ->or_like('e.employment_type_name', $search)
                    ->or_where("a.employee_id IN ($subquery)", NULL, FALSE);
            $this->db->group_end();
        }
        $employees_data = $this->db->get()->result();

        $this->db->select('COUNT(*) as total_records')
            ->from('employees_tbl a')
            ->join('employee_types_tbl b', 'b.employee_type_id = a.employee_type_id')
            ->join('units_tbl d', 'd.unit_id = a.unit_id')
            ->join('employment_types_tbl e', 'e.employment_type_id = a.employment_type_id');
    
        if (!empty($search)) {
            $this->db->group_start()
                ->like('CONCAT(a.employee_fname, " ", a.employee_lname)', $search)
                ->or_like('a.employee_no', $search)
                ->or_like('a.employee_email', $search)
                ->or_like('a.employee_contact', $search)
                ->or_like('d.unit_name', $search)
                ->or_like('b.employee_type_name', $search)
                ->or_like('e.employment_type_name', $search)
                ->or_where("a.employee_id IN ($subquery)", NULL, FALSE);
            $this->db->group_end();
        }
        
        $total_records = $this->db->get()->row()->total_records;

        $table_data = [];
        foreach ($employees_data as $key => $values) {

            $encoded_id = encode($values->employee_id);
            $badge = $values->employee_status == 1 
                ? '<span class="badge text-bg-success">Active</span>' 
                : '<span class="badge text-bg-warning">Inactive</span>';
            
            $toggle_class = $values->employee_status == 1 
                ? 'text-success' 
                : 'text-warning';
            
            $toggle_icon = $values->employee_status == 1 
                ? 'fa-toggle-on' 
                : 'fa-toggle-off';
            
                $action = '
                <div class="d-flex gap-1 align-items-center">
                    <!-- Edit Employee Details -->
                    <button class="btn btn-primary btn-sm employee-btn" data-component="Edit_Employee_Details" data-id="' . $encoded_id . '" data-url="/update_employee" title="Edit Employee">
                        <i class="fas fa-edit"></i>
                    </button>
            
                    <!-- Reset Employee Password -->
                    <button class="btn btn-warning btn-sm employee-btn" data-component="Reset_Employee_Password" data-id="' . $encoded_id . '" data-url="/reset_employee" title="Reset Password">
                        <i class="fas fa-key"></i>
                    </button>
            
                    <!-- Manage Employee Form Access -->
                    <button class="btn btn-info btn-sm employee-btn" data-component="Manage_Employee_Form_Access" data-id="' . $encoded_id . '" data-url="/update_employee_form_access" title="Manage Form Access">
                        <i class="fas fa-tasks"></i>
                    </button>
            
                    <!-- Manage Employee Permission -->
                    <button class="btn btn-secondary btn-sm employee-btn" data-component="Manage_Employee_Permission" data-id="' . $encoded_id . '" data-url="/update_employee_permission" title="Manage Permissions">
                        <i class="fas fa-user-shield"></i>
                    </button>
            
                    <!-- Toggle Employee Status -->
                    <button class="border-0 bg-transparent ' . $toggle_class . ' toggle" data-id="' . $encoded_id . '">
                        <span class="fas ' . $toggle_icon . ' fa-lg"></span>
                    </button>
                </div>
            ';

            $table_data[] = [
                'action' => $action,
                'employee_no' => $values->employee_no,
                'name' => $values->employee_lname . ', ' . $values->employee_fname . ' ' . $values->employee_mname,
                'unit_name' => $values->unit_name,
                'location_name' => $values->location_name,
                'employee_email' => $values->employee_email,
                'employee_contact' => $values->employee_contact,
                'employment_type_name' => $values->employment_type_name,
                'employee_type_name' => $values->employee_type_name,
                'badge' => $badge,
            ];
        }

        $response = [
            'draw'            => $draw,
            'recordsTotal'    => $total_records,
            'recordsFiltered' => $total_records,
            'data'            => $table_data,
        ];

        echo json_encode($response);
    }

    public function load_employee_modal(){ //Used by Add Employee, Edit Employee and Reset Employee Password buttons from the employee page
        $component = strtolower($this->input->get('component'));
        $employee_id = $this->input->get('employee_id') ? decode(clean_data($this->input->get('employee_id'))) : NULL;

        $this->db->select('a.form_id, a.form_name, IFNULL(b.status, 0) as status');
        $this->db->from('form_tbl a');
        $this->db->join('employee_forms_access_tbl b', 'a.form_id = b.form_id AND b.employee_id = ' . $this->db->escape($employee_id), 'left');
        $query = $this->db->get();
        $forms = $query->result();

        $component_resources = [
            'employee' => $employee_id ? $this->main->get_join('employees_tbl a', ['employee_locations_tbl b' => 'a.employee_id = b.employee_id AND b.employee_location_status = 1'], TRUE, FALSE, FALSE, ['a.*', 'b.location_id'], ['a.employee_id' => $employee_id]) : NULL,
            'controller' => $this->controller,
            'bc' => $this->main->get_data("bc_tbl", [ 'bc_status' => 1 ]),
            'locations' => $this->main->get_data('locations_tbl', ['location_status' => 1]),
            'units' => $this->main->get_data('units_tbl', ['unit_status' => 1]),
            'employee_types' => $this->main->get_data('employee_types_tbl', ['employee_type_status' => 1]),
            'employment_types' => $this->main->get_data('employment_types_tbl', ['employment_type_status' => 1]),
            'forms' => $forms,
        ];

        $this->load->view($this->controller . '/employee/employee_component/' . $component, $component_resources);
    }

    public function store_employee(){
        require_login($this->controller);

        $rules = [
            [ 'field' => 'fname'           , 'label' => 'First Name'      , 'rules' => 'required'                                               ],
            [ 'field' => 'lname'           , 'label' => 'Last Name'       , 'rules' => 'required'                                               ],
            [ 'field' => 'password'        , 'label' => 'Password'        , 'rules' => 'required'                                               ],
            [ 'field' => 'employee_type'   , 'label' => 'User Type'       , 'rules' => 'required'                                               ],
            [ 'field' => 'unit'            , 'label' => 'Unit'            , 'rules' => 'required'                                               ],
            [ 'field' => 'location'        , 'label' => 'Location'        , 'rules' => 'required'                                               ],

            [ 'field' => 'email'           , 'label' => 'Email'           , 'rules' => 'required|valid_email|is_unique[employees_tbl.employee_email]' , 'errors' =>              
                [
                    'required' => 'The {field} field is required.',
                    'valid_email' => 'The {field} field must be a valid email.',
                    'is_unique' => 'This {field} is already registered.'
                ]
            ],
            [ 'field' => 'employee_no'     , 'label' => 'Employee Number' , 'rules' => 'required|is_unique[employees_tbl.employee_no]', 'errors' =>              
                [
                    'required' => 'The {field} field is required.',
                    'is_unique' => 'This {field} is already registered.'
                ]
            ],

        ];

        foreach ($rules as $key => $value) {
            $this->form_validation->set_rules($value['field'], $value['label'], $value['rules'], $value['errors'] ?? []);
        }

        if ($this->form_validation->run() == FALSE) {
            $errors = $this->form_validation->error_array();
            $response = [
                'errors'    => $errors,
            ];

            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode($response))
                ->_display();
            
            exit;
        }

        $fname              = clean_data($this->input->post('fname'));
        $lname              = clean_data($this->input->post('lname'));
        $mname              = clean_data($this->input->post('mname'));
        $emp_no             = clean_data($this->input->post('employee_no'));
        $email              = clean_data($this->input->post('email'));
        $contact            = clean_data($this->input->post('contact'));
        $password           = clean_data($this->input->post('password'));
        $employee_type      = clean_data(decode($this->input->post('employee_type')));
        $unit               = clean_data(decode($this->input->post('unit')));
        $location           = clean_data(decode($this->input->post('location')));

        $data = [
            'employee_fname'     => strtoupper(trim($fname)),
            'employee_lname'     => strtoupper(trim($lname)),
            'employee_mname'     => strtoupper(trim($mname)),
            'employee_no'        => $emp_no,
            'employee_email'     => $email,
            'employee_contact'   => $contact,
            'employee_password'  => password_hash($password, PASSWORD_DEFAULT),
            'employee_type_id'   => $employee_type,
            'employment_type_id' => 1,
            'unit_id'            => $unit,
            'employee_reset'     => 1,
            'employee_added'     => date_now(),
            'employee_status'    => 1
        ];
        
        $this->db->trans_start();
        $result = $this->main->insert_data('employees_tbl', $data, TRUE);

        if ($result['result']) {
            $location_data = [
                'employee_id'              => $result['id'],
                'location_id'              => $location,
                'employee_location_status' => 1,
                'employee_location_added'  => date_now(),
            ];
            $this->main->insert_data('employee_locations_tbl', $location_data);
        }

        if ($this->db->trans_status()) {
            $this->db->trans_commit();
            $response = [
                'status'   => 200,
                'message'  => 'Employee has been registered',
            ];
        } else {
            $this->db->trans_rollback();
            $response = [
                'status'   => 400,
                'message'  => 'Registraion Failed',
            ];
        }

        $this->output
            ->set_status_header($response['status'])
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }   

    public function update_employee(){
        require_login($this->controller);

        $rules = [
            [ 'field' => 'fname'           , 'label' => 'First Name'      , 'rules' => 'required'                                               ],
            [ 'field' => 'lname'           , 'label' => 'Last Name'       , 'rules' => 'required'                                               ],
            [ 'field' => 'employee_type'   , 'label' => 'User Type'       , 'rules' => 'required'                                               ],
            [ 'field' => 'unit'            , 'label' => 'Unit'            , 'rules' => 'required'                                               ],
            [ 'field' => 'location'        , 'label' => 'Location'        , 'rules' => 'required'                                               ],

            [ 'field' => 'email'           , 'label' => 'Email'           , 'rules' => 'required|valid_email' , 'errors' =>              
                [
                    'required' => 'The {field} field is required.',
                    'valid_email' => 'The {field} field must be a valid email.'
                ]
            ],
            [ 'field' => 'employee_no'     , 'label' => 'Employee Number' , 'rules' => 'required', 'errors' =>              
                [
                    'required' => 'The {field} field is required.'
                ]
            ],

        ];

        foreach ($rules as $key => $value) {
            $this->form_validation->set_rules($value['field'], $value['label'], $value['rules'], $value['errors'] ?? []);
        }

        if ($this->form_validation->run() == FALSE) {
            $errors = $this->form_validation->error_array();
            $response = [
                'errors'    => $errors,
            ];

            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode($response))
                ->_display();
            
            exit;
        }

        $employee_id        = clean_data(decode($this->input->post('employee_id')));
        $fname              = clean_data($this->input->post('fname'));
        $lname              = clean_data($this->input->post('lname'));
        $mname              = clean_data($this->input->post('mname'));
        $emp_no             = clean_data($this->input->post('employee_no'));
        $email              = clean_data($this->input->post('email'));
        $contact            = clean_data($this->input->post('contact'));
        $password           = clean_data($this->input->post('password'));
        $employee_type      = clean_data(decode($this->input->post('employee_type')));
        $unit               = clean_data(decode($this->input->post('unit')));
        $location           = clean_data(decode($this->input->post('location')));

        $data = [
            'employee_fname'     => strtoupper(trim($fname)),
            'employee_lname'     => strtoupper(trim($lname)),
            'employee_mname'     => strtoupper(trim($mname)),
            'employee_no'        => $emp_no,
            'employee_email'     => $email,
            'employee_contact'   => $contact,
            'employee_password'  => password_hash($password, PASSWORD_DEFAULT),
            'employee_type_id'   => $employee_type,
            'employment_type_id' => 1,
            'unit_id'            => $unit,
            'employee_reset'     => 1,
            'employee_added'     => date_now(),
            'employee_status'    => 1
        ];

        $where = ['employee_id' => $employee_id];
        
        $this->db->trans_start();
        $result = $this->main->update_data('employees_tbl', $data, $where);

        if ($result) {
            $emp_loc_set         = ['employee_location_status' => 0, 'employee_location_modified' => date_now()];
            $result              = $this->main->update_data('employee_locations_tbl', $emp_loc_set, $where);
            $check_emp_loc_where = ['location_id' => $location, 'employee_id' => $employee_id];
            $check_emp_loc       = $this->main->check_data('employee_locations_tbl', $check_emp_loc_where, TRUE);
            if ($check_emp_loc['result']) {
                $emp_loc_set   = ['employee_location_status' => 1, 'employee_location_modified' => date_now()];
                $emp_loc_where = ['employee_location_id' => $check_emp_loc['info']->employee_location_id];
                $result        = $this->main->update_data('employee_locations_tbl', $emp_loc_set, $emp_loc_where);
            } else {
                $location_data = [
                    'employee_id'              => $employee_id,
                    'location_id'              => $location,
                    'employee_location_status' => 1,
                    'employee_location_added'  => date_now(),
                ];
                $this->main->insert_data('employee_locations_tbl', $location_data, TRUE);
            }
        }

        if ($this->db->trans_status()) {
            $this->db->trans_commit();
            $response = [
                'status'   => 200,
                'message'  => 'Employee\'s Information has been Updated',
            ];
        } else {
            $this->db->trans_rollback();
            $response = [
                'status'   => 400,
                'message'  => 'Employee\'s Information Update Failed',
            ];
        }

        $this->output
            ->set_status_header($response['status'])
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    private function _reset_email_content($name, $password) //Might no longer be needed
    {
		$data['title']    = system_default()['system_name'];
		$data['name']     = $name;
		$data['password'] = $password;
		return $this->load->view($this->controller . '/employee_reset_content', $data, TRUE);
	}
    public function reset_employee()
    {
        require_login($this->controller);

        $rules = [
            [ 'field' => 'password'    , 'label' => 'Password' , 'rules' => 'required' ],
        ];
        
        foreach ($rules as $key => $value) {
            $this->form_validation->set_rules($value['field'], $value['label'], $value['rules'], $value['errors'] ?? []);
        }

        if ($this->form_validation->run() == FALSE) {
            $errors = $this->form_validation->error_array();
            $response = [
                'errors'    => $errors,
            ];

            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode($response))
                ->_display();
            
            exit;
        }

        $password    = clean_data($this->input->post('password'));
        $employee_id = clean_data(decode($this->input->post('employee_id')));

        $set = [
            'employee_password' => password_hash($password, PASSWORD_DEFAULT),
            'employee_reset'    => 1,
        ];

        $where = [ 'employee_id' => $employee_id ];

        $this->db->trans_start();
        $this->main->update_data('employees_tbl', $set, $where);
        
        if ($this->db->trans_status()) {
            $this->db->trans_commit();
            $response = [
                'status'   => 200,
                'message'  => 'Employee\'s Password has been Reset',
            ];
        } else {
            $this->db->trans_rollback();
            $response = [
                'status'   => 400,
                'message'  => 'Employee\'s Password Reset Failed',
            ];
        }

        $this->output
            ->set_status_header($response['status'])
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    function update_employee_form_access(){
        require_login($this->controller);

        $employee_id = clean_data(decode($this->input->post('employee_id')));
        $form_data = $this->input->post('forms');

        $this->db->trans_begin();

        // Set employee's forms access to inactive
        $this->db->set('status', 0);
        $this->db->where('employee_id', $employee_id);
        $this->db->update('employee_forms_access_tbl');

        if(!empty($form_data)){
            foreach ($form_data as $data) {
                $decoded_form_id = decode($data);
            
                // Check if the record exists
                $this->db->where('employee_id', $employee_id);
                $this->db->where('form_id', $decoded_form_id);
                $query = $this->db->get('employee_forms_access_tbl');
            
                if ($query->num_rows() > 0) {
                    // Record exists, update it
                    $this->db->set('status', 1);
                    $this->db->where('employee_id', $employee_id);
                    $this->db->where('form_id', $decoded_form_id);
                    $this->db->update('employee_forms_access_tbl');
                } else {
                    // Record does not exist, insert it
                    $this->db->insert('employee_forms_access_tbl', [
                        'employee_id' => $employee_id,
                        'form_id' => $decoded_form_id,
                        'status' => 1,
                    ]);
                }
            }
        }


        if ($this->db->trans_status()) {
            $this->db->trans_commit();
            $response = [
                'status'   => 200,
                'message'  => 'Employee\'s Access has been Updated',
            ];
        } else {
            $this->db->trans_rollback();
            $response = [
                'status'   => 400,
                'message'  => 'Employee\'s Access Update Failed',
            ];
        }

        $this->output
            ->set_status_header($response['status'])
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    function update_employee_permission(){

    }

    public function toggle_employee_status(){ //Responsible for activating and deactivating employees
        require_login($this->controller);

        $response = toggle_data('employees_tbl', 'employee_status', 'employee_id', clean_data((decode($this->input->post('id')))));

        $this->output
            ->set_status_header($response['status'])
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    //FORM MANAGEMENT
    public function forms() //Displays form page
    {   
        $info = require_login($this->controller);
        if(!in_array('read_form', $this->user_permissions['form'])){
            show_404();
        }

        $employee_id = decode($info['employee_id']);
        
        $employee_permission = [
            'create_form' => in_array('create_form', $this->user_permissions['form']),
            'update_form_content' => in_array('update_form_content', $this->user_permissions['form']),
        ];

        // Query to get forms the user has access to
        $forms 							= $this->_get_form_access($employee_id);
		$form_access_links 				= array_column($forms, 'internal_link');
        $report_menus 					= $this->_get_form_menu($forms);


        $forms_page_resources = [
            'title' => ucwords(str_replace('_', ' ', __FUNCTION__)),
            'forms' => $forms,
            'controller' => $this->controller,
            'employee_permission' => $employee_permission,
        ];

        $template_resources = [
            'content' 					=> $this->load->view($this->controller . '/form/form_content', $forms_page_resources, TRUE),
            'js_scripts' 				=> [
                'form/form_content_page.js'
            ],
			'form_access_links'         => $form_access_links,
            'report_menus'         		=> $report_menus,
        ];

        $this->load->view($this->controller . '/template', $template_resources);
    }

    private function prepare_options($field)
    {
        $options = '';
        if (in_array($field->field_type_id, [4, 5, 6])) {
            foreach ($field->options as &$option) {
                $option_content = [
                    'option_data' => $option,
                ];
                $options .= $this->load->view($this->component_path . 'option', $option_content, TRUE);
            }
        }
        return $options;
    }

    private function prepare_field_components($field)
    {
        $form_field_type_data = $this->main->get_data('form_field_type_tbl', ['status' => 1], FALSE, ['field_type_id', 'field_type']);

        $select = $this->load->view($this->component_path . 'field_components/field_type_select', [
            'field_type_data' => $form_field_type_data,
            'selected_field_type_id' => $field->field_type_id
        ], TRUE);

        $description = null;
        if($field->form_field_description){
            $description = $this->load->view($this->component_path . 'field_components/description', [
                'description' => $field->form_field_description
            ], TRUE);
        }

        $add_option = null;
        if (in_array($field->field_type_id, [4, 5, 6])) {
            $add_option = $this->load->view($this->component_path . 'field_components/add_option_btn', [
                'field_id' => $field->field_id
            ], TRUE);
        }

        return [
            'select' => $select,
            'description' => $description,
            'add_option' => $add_option,
        ];
    }

    private function prepare_fields($section)
    {
        $fields = '';
        foreach ($section->fields as &$field) {

            switch($field->field_type_id){
                case 4:
                    $field->special_options_data = $this->main->get_data('field_multiple_choice_options_tbl', ['field_id' => $field->field_id, 'status' => 1], TRUE);
                    break;
                case 6:
                    $field->special_options_data = $this->main->get_data('field_checkbox_options_tbl', ['field_id' => $field->field_id, 'status' => 1], TRUE);
                    break;
                case 7:
                    $field->special_options_data = $this->main->get_data('field_linear_scale_options_tbl', ['field_id' => $field->field_id, 'status' => 1], TRUE);
                    break;
            }

            $options = $this->prepare_options($field);
            $field_components = $this->prepare_field_components($field);

            $field_content = [
                'field_data' => $field,
                'field_component' => $field_components,
                'options' => $options,
            ];

            $fields .= $this->load->view($this->component_path . 'field', $field_content, TRUE);
        }
        return $fields;
    }

    private function prepare_sections($form)
    {
        $sections = '';
        foreach ($form->sections as &$section) {
            $fields = $this->prepare_fields($section);

            $section_content = [
                'section_data' => $section,
                'fields' => $fields,
            ];

            $sections .= $this->load->view($this->component_path . 'section', $section_content, TRUE);
        }
        return $sections;
    }

    private function prepare_form($form)
    {
        $content = '';
        $sections = $this->prepare_sections($form);

        $form_content = [
            'form_data' => $form,
        ];

        $content .= $this->load->view($this->component_path . 'form', $form_content, TRUE);
        $content .= $sections;

        return $content;
    }

    public function form_content($form_id = '') //Loads form's content and page
    {
        require_login($this->controller);

        // Get Form Data
        $decoded_form_id = clean_data(decode($form_id));
        $form = $this->main->get_data('form_tbl', ['form_id' => $decoded_form_id], TRUE, ['form_name', 'form_id']);
        $sections = $this->main->get_data('form_section_tbl', ['form_id' => $decoded_form_id, 'status' => 1], FALSE, ['section_sequence', 'section_name', 'section_id'], 'section_sequence asc');
        
        foreach ($sections as &$section) {
            $fields = $this->main->get_data('form_field_tbl', ['section_id' => $section->section_id, 'status' => 1], FALSE, ['*'], 'form_field_sequence asc');
            foreach ($fields as &$field) {
                if (!in_array($field->field_type_id, [4, 5, 6])) {
                    continue;
                }
                $field->options = $this->main->get_data('form_field_choice_tbl', ['field_id' => $field->field_id, 'status' => 1], FALSE, ['*'], 'option_sequence asc');
            }
            $section->fields = $fields;
        }
        $form->sections = $sections;
    

        $form_data = [
            'form_content' => $this->prepare_form($form),
            'title'        => $form->form_name,
            'controller'   => $this->controller,
            'form_id'      => $decoded_form_id,
            'js_scripts'   => [
                'form/form_maintenance_functionality.js'
            ],
        ];

        $template_data = [
            'content' => $this->load->view($this->controller . '/form/form_field_edit', $form_data, TRUE),
        ];

        $this->load->view($this->controller . '/template', $template_data);
    }

    //Functions used by the form builder
    private function retrieve_data(){
       return json_decode(file_get_contents('php://input'), true);
    }

    private function upsert_data($table, $data, $where) {
        if ($this->main->check_data($table, $where)) {
            // Update existing data to status 1
            $this->main->update_data($table, $data, $where);
            // Disable previous configurations
            // $this->main->update_data($table, ['status' => 0], array_merge($where, ['status' => 1]));
        } else {
            $data = array_merge($data, $where, ['status' => 1]);
            $this->main->insert_data($table, $data);
        }
    }
    
    private function disable_other_options($field_id, $exclude_table) {
        $tables = [
            'field_multiple_choice_options_tbl',
            'field_checkbox_options_tbl',
            'field_linear_scale_options_tbl'
        ];
    
        foreach ($tables as $table) {
            if ($table !== $exclude_table && $this->main->check_data($table, ['field_id' => $field_id])) {
                $this->main->update_data($table, ['status' => 0], ['field_id' => $field_id]);
            }
        }
    }
    
    private function update_special_option($field_id, $field_type_id, $data) {
        switch ($field_type_id) {
            case 4: // Multiple Choice
                $multiple_choice_data = [
                    'has_others_option' => $data['has_others_option'],
                    'status' => 1
                ];
                $this->upsert_data('field_multiple_choice_options_tbl', $multiple_choice_data, ['field_id' => $field_id]);
                $this->disable_other_options($field_id, 'field_multiple_choice_options_tbl');
                break;
    
            case 6: // Checkbox
                $checkbox_data = [
                    'has_others_option' => $data['has_others_option'],
                    'min_selection' => $data['min_selection'],
                    'max_selection' => $data['max_selection'],
                    'status' => 1
                ];
                $this->upsert_data('field_checkbox_options_tbl', $checkbox_data, ['field_id' => $field_id]);
                $this->disable_other_options($field_id, 'field_checkbox_options_tbl');
                break;
    
            case 7: // Linear Scale
                $linear_scale_data = [
                    'left_label' => $data['left_label'],
                    'right_label' => $data['right_label'],
                    'min_value' => $data['min_value'],
                    'max_value' => $data['max_value'],
                    'status' => 1
                ];
                $this->upsert_data('field_linear_scale_options_tbl', $linear_scale_data, ['field_id' => $field_id]);
                $this->disable_other_options($field_id, 'field_linear_scale_options_tbl');
                break;
    
            default:
                $this->disable_other_options($field_id, null);
                break;
        }
    }

    public function add_form(){
        $login_info = require_login($this->controller);

        $data = $this->input->post();
        // die(json_encode($data));
        $form_data = [
            'form_name' => clean_data($data['name']),
            'created_at' => date_now(),
            'created_by' => decode($login_info['employee_id']),
            'status' => 1,
        ];

        $this->db->trans_begin();
        $this->main->insert_data('form_tbl', $form_data);

        if ($this->db->trans_status()) {
            $this->db->trans_commit();
            $response = [
                'success' => true,
                'message' => 'Form created successfully',
            ];
            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response))
                ->_display();
        } else {
            $this->db->trans_rollback();
            $response = [
                'success' => false,
                'message' => 'Failed to create the form',
            ];
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode($response))
                ->_display();
        }
        exit;

    }

    public function add_section(){
        $login_info = require_login($this->controller);

        $data = $this->retrieve_data();
        $form_id = $data['form_id'];
        
        $section_data = [
            'section_name' => 'Untitled Section',
            'form_id'      => $form_id,
            'created_at'   => date_now(),
            'created_by'   => decode($login_info['employee_id']),
            'status'       => 1,
        ];

        $section_id = $this->main->insert_data('form_section_tbl', $section_data, TRUE)['id'];
        // $section_id = 999;
        $section_content = [
            'section_data' => (object) [
                'section_id'   => $section_id,
                'section_name' => 'Untitled Section'
            ],
            'fields'       => null,
        ];

        $section = $this->load->view($this->component_path . 'section', $section_content, TRUE);

        echo json_encode(['result' => true, 'section_content' => $section]);
    }   
    public function add_field(){
        $login_info = require_login($this->controller);

        $data = $this->retrieve_data();
        $section_id = $data['section_id'];
        
        $field_data = [
            'field_type_id'          => 1,
            'section_id'             => $section_id,
            'form_field_sequence'    => 1,
            'form_field_name'        => 'Untitled Field',
            'form_field_description' => '',
            'created_at'             => date_now(),
            'created_by'             => decode($login_info['employee_id']),
            'is_required'            => 0,
            'status'                 => 1,
        ];


        $field_components = $this->prepare_field_components($field_data = (object) $field_data);
        $field_id = $this->main->insert_data('form_field_tbl', $field_data, TRUE)['id'];
        
        $field_content = [
            'field_data' => (object) [
                'field_id'              => $field_id,
                'field_type_id'         => 1,
                'form_field_sequence'   => 1,
                'form_field_name'       => 'Untitled Field',
                'form_field_description' => '',
                'is_required'           => 0,
            ],
            'field_component' => $field_components,
            'options' => null,
        ];

        $field = $this->load->view($this->component_path . 'field', $field_content, TRUE);

        echo json_encode(['result' => true, 'field_content' => $field]);

    }
    public function add_option(){
        $login_info = require_login($this->controller);

        $data = $this->retrieve_data();
        $field_id = $data['field_id'];

        $option_data = [
            'field_id' => $field_id,
            'option_name' => 'Untitled Option',
            'created_at'  => date_now(),
            'created_by'  => decode($login_info['employee_id']),
            'status' => 1,
        ]; 

        $option_id = $this->main->insert_data('form_field_choice_tbl', $option_data, TRUE)['id'];

        $option_content = [
            'option_data' => (object) [
                'option_id'       => $option_id,
                'option_sequence' => 1,
                'option_name'     => 'Untitled Option'
            ],
        ];

        $option = $this->load->view($this->component_path. 'option', $option_content, TRUE);

        echo json_encode(['result' => true, 'option_content' => $option]);
    }

    public function update_form(){
        $login_info = require_login($this->controller);

        $data = $this->retrieve_data();
        $form_id = $data['form_id'];
        $form_name = $data['form_name'];

        $form_data = [
            'form_name' => $form_name,
            'modified_at' => date_now(),
            'modified_by' => decode($login_info['employee_id']),
        ];

        $this->db->trans_start();
        $this->main->update_data('form_tbl', $form_data, ['form_id' => $form_id]);

        if ($this->db->trans_status()) {
            $this->db->trans_commit();

            $data = [
                'result' => true,
                'message' => 'Data Update Success'
            ];
        } else {
            $this->db->trans_rollback();

            $data = [
                'result' => false,
                'message' => 'Data Update Failed'
            ];
        }

        echo json_encode($data);
    }

    public function update_section(){
        $login_info = require_login($this->controller);

        $data = $this->retrieve_data();

        $section_id = $data['section_id'];
        $section_sequence = $data['section_sequence'];
        $section_name = $data['section_name'];

        $section_data = [
            'section_sequence' => $section_sequence,
            'section_name' => $section_name,
            'modified_at'  => date_now(),
            'modified_by'  => decode($login_info['employee_id']),
        ];

        $this->db->trans_start();
        $this->main->update_data('form_section_tbl', $section_data, ['section_id' => $section_id]);

        if ($this->db->trans_status()) {
            $this->db->trans_commit();

            $data = [
                'result' => true,
                'message' => 'Data Update Success'
            ];
        } else {
            $this->db->trans_rollback();
            $data = [
                'result' => false,
                'message' => 'Data Update Failed'
            ];
        }

        echo json_encode($data);

    }

    public function update_field(){
        $login_info = require_login($this->controller);

        $data = $this->retrieve_data();
        $field_id = $data['field_id'];
        $field_name = $data['field_name'];
        $field_description = $data['field_description'] ?? '';
        $form_field_sequence = $data['field_sequence'];
        $field_type_id = $data['field_type_id'];
        $is_required = $data['is_required'];

        $field_data = [
            'form_field_name'        => $field_name,
            'form_field_description' => $field_description,
            'form_field_sequence'    => $form_field_sequence,
            'field_type_id'          => $field_type_id,
            'is_required'            => $is_required,
            'modified_at'            => date_now(),
            'modified_by'            => decode($login_info['employee_id']),
        ];
        
        $this->db->trans_start();
        $this->main->update_data('form_field_tbl', $field_data, ['field_id' => $field_id]);

        $this->update_special_option($field_id, $field_type_id, $data);
        
        if ($this->db->trans_status()) {
            $this->db->trans_commit();

            $data = [
                'result' => true,
                'message' => 'Data Update Success'
            ];
        } else {
            $this->db->trans_rollback();

            $data = [
                'result' => false,
                'message' => 'Data Update Failed'
            ];
        }

        echo json_encode($data);
    }
    
    public function update_option(){
        $login_info = require_login($this->controller);

        $data = $this->retrieve_data();
        $option_id = $data['option_id'];
        $option_name = $data['option_name'];
        $option_sequence = $data['option_sequence'];

        $option_data = [
            'option_name' => $option_name,
            'option_sequence' => $option_sequence,
            'modified_at' => date_now(),
            'modified_by' => decode($login_info['employee_id']),
        ];

        $this->db->trans_start();
        $this->main->update_data('form_field_choice_tbl', $option_data, ['option_id' => $option_id]);

        if ($this->db->trans_status()) {
            $this->db->trans_commit();

            $data = [
                'result' => true,
                'message' => 'Data Update Success'
            ];
        } else {
            $this->db->trans_rollback();
            $data = [
                'result' => false,
                'message' => 'Data Update Failed'
            ];
        }

        echo json_encode($data);
    }

    public function delete_section(){   
        $login_info = require_login($this->controller);

        $data = $this->retrieve_data();
        $section_id = $data['section_id'];

        $section_data = [
            'status' => 0,
            'modified_at' => date_now(),
            'modified_by' => decode($login_info['employee_id']),
        ];

        $this->db->trans_start();
        $this->main->update_data('form_section_tbl', $section_data, ['section_id' => $section_id]);
        if ($this->db->trans_status()) {
            $this->db->trans_commit();

            $data = [
                'result' => true,
                'message' => 'Section Deleted'
            ];
        } else {
            $this->db->trans_rollback();
            $data = [
                'result' => false,
                'message' => 'Section Deletion Failed'  
            ];
        }

        $this->sync_child_status_to_parent(__FUNCTION__, $section_id);

        echo json_encode($data);
    }

    public function delete_field(){
        $login_info = require_login($this->controller);

        $data = $this->retrieve_data();
        $field_id = $data['field_id'];

        $field_data = [
            'status' => 0,
            'modified_at' => date_now(),
            'modified_by' => decode($login_info['employee_id']),
        ];

        $this->db->trans_start();
        $this->main->update_data('form_field_tbl', $field_data, ['field_id' => $field_id]);
        if ($this->db->trans_status()) {
            $this->db->trans_commit();

            $data = [
                'result' => true,
                'message' => 'Field Deleted'
            ];
        } else {
            $this->db->trans_rollback();
            $data = [
                'result' => false,
                'message' => 'Field Deletion Failed'
            ];
        }

        $this->sync_child_status_to_parent(__FUNCTION__, $field_id);

        echo json_encode($data);
    }

    public function delete_option(){
        $login_info = require_login($this->controller);

        $data = $this->retrieve_data();
        $option_id = $data['option_id'];

        $option_data = [
            'status' => 0,
            'modified_at' => date_now(),
            'modified_by' => decode($login_info['employee_id']),
        ];

        $this->db->trans_start();
        $this->main->update_data('form_field_choice_tbl', $option_data, ['option_id' => $option_id]);

        if ($this->db->trans_status()) {
            $this->db->trans_commit();

            $data = [
                'result' => true,
                'message' => 'Option Deleted'
            ];
        } else {
            $this->db->trans_rollback();
            $data = [
                'result' => false,
                'message' => 'Option Deletion Failed'
            ];
        }

        echo json_encode($data);
    }

    private function sync_child_status_to_parent($function, $id) {
        $login_info = require_login($this->controller);
    
        if ($function == 'delete_field') {
            $option_ids = $this->main->get_data('form_field_choice_tbl', ['field_id' => $id], FALSE, ['option_id']);
    
            foreach ($option_ids as $option) {
                $option_data = [
                    'status' => 0,
                    'modified_at' => date_now(),
                    'modified_by' => decode($login_info['employee_id']),
                ];
                $this->main->update_data('form_field_choice_tbl', $option_data, ['option_id' => $option->option_id]);
            }
    
        } else {
            $field_ids = $this->main->get_data('form_field_tbl', ['section_id' => $id], FALSE, ['field_id']);
    
            foreach ($field_ids as $field) {
                $field_data = [
                    'status' => 0,
                    'modified_at' => date_now(),
                    'modified_by' => decode($login_info['employee_id']),
                ];
                $this->main->update_data('form_field_tbl', $field_data, ['field_id' => $field->field_id]);
    
                $option_ids = $this->main->get_data('form_field_choice_tbl', ['field_id' => $field->field_id], FALSE, ['option_id']);
    
                foreach ($option_ids as $option) {
                    $option_data = [
                        'status' => 0,
                        'modified_at' => date_now(),
                        'modified_by' => decode($login_info['employee_id']),
                    ];
                    $this->main->update_data('form_field_choice_tbl', $option_data, ['option_id' => $option->option_id]);
                }
            }
        }
    }

    //Status Toggle RMVD
    // private function _toggle_data_status($id, $table, $id_field, $status_field, $required_status)
    // {
    //     $status_label = ($required_status == 1) ? 'Activated' : 'Deactivated';

    //     if (empty($id)) {
    //         $alert_message = alert_template('ID is Required', false);
    //         $this->session->set_flashdata('message', $alert_message);
    //         redirect($_SERVER['HTTP_REFERER']);
    //     }

    //     $check_id = $this->main->check_data($table, [$id_field => $id], TRUE);
    //     if (!$check_id['result']) {
    //         $alert_message = alert_template('ID Doesn\'t Exist', false);
    //         $this->session->set_flashdata('message', $alert_message);
    //         redirect($_SERVER['HTTP_REFERER']);
    //     }

    //     $check_data_status = (array)$check_id['info'];
    //     if ( $check_data_status[$status_field] == $required_status) {
    //         $alert_message = alert_template('Data is already ' . $status_label, false);
    //         $this->session->set_flashdata('message', $alert_message);
    //         redirect($_SERVER['HTTP_REFERER']);
    //     }

    //     $set    = [$status_field => $required_status];
    //     $where  = [$id_field => $id];
    //     $result = $this->main->update_data($table, $set, $where);
    //     $msg    = ($result == TRUE) ? '<div class="alert alert-success">Data Successfully '.$status_label.'.</div>' : '<div class="alert alert-danger">Error please try again!</div>';
    //     $this->session->set_flashdata('message', $msg);
    // }

	// public function activate_form()
    // {
	// 	require_login($this->controller);

	// 	if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //         $id              = clean_data(decode($this->input->post('id')));
    //         $table           = 'form_tbl';
    //         $id_field        = 'form_id';
    //         $status_field    = 'status';
    //         $required_status = 1;
    //         $this->_toggle_data_status($id, $table, $id_field, $status_field, $required_status);

    //         redirect($_SERVER['HTTP_REFERER']);
	// 	}else{
	// 		redirect($this->controller);
	// 	}
    // }

	// public function deactivate_form()
    // {
	// 	require_login($this->controller);
	// 	if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //         $id              = clean_data(decode($this->input->post('id')));
    //         $table           = 'form_tbl';
    //         $id_field        = 'form_id';
    //         $status_field    = 'status';
    //         $required_status = 0;
    //         $this->_toggle_data_status($id, $table, $id_field, $status_field, $required_status);

    //         redirect($_SERVER['HTTP_REFERER']);
	// 	}else{
	// 		redirect($this->controller);
	// 	}
    // }

	private function _get_form_access($employee_id){
		$this->db->select('form_tbl.*');
        $this->db->from('form_tbl');
        $this->db->join('employee_forms_access_tbl', 'employee_forms_access_tbl.form_id = form_tbl.form_id', 'inner');
        $this->db->where('employee_forms_access_tbl.employee_id', $employee_id);
        $this->db->where('employee_forms_access_tbl.status', 1);
        $this->db->where('form_tbl.status', 1); 
        $query = $this->db->get();
        return $query->result();
	}

	private function _get_form_menu($forms){
		$list = '';
		$controller = $this->controller;
		foreach($forms as $form){
			$list .= '
			<li>
				<a href="'.base_url($controller . '/'.$form->internal_link).'">'.$form->report_name.'</a>
			</li>';
		}

		return $list;
	}

    public function transaction_list()
    {   
        $info 							= require_login($this->controller);
        $employee_id 					= decode($info['employee_id']);
        $forms 							= $this->_get_form_access($employee_id);
		$form_access_links 				= array_column($forms, 'internal_link');
        $report_menus 					= $this->_get_form_menu($forms);

        $transaction_page_resources = [
            'title'         			=> ucwords(str_replace('_', ' ', __FUNCTION__)),
            'controller'    			=> $this->controller,
            'forms'         			=> $forms,
        ];
        
        $template_resources = [
            'content' 					=> $this->load->view($this->controller . '/transaction/transaction_list_content', $transaction_page_resources, TRUE),
            'js_scripts' 				=> [
                'transaction/transaction_list_page.js?v=1.1'
            ],
			'form_access_links'         => $form_access_links,
            'report_menus'         		=> $report_menus,
        ];

        // $data['content']          = $this->load->view($this->controller . '/transaction/transaction_list_content', $transaction_page_resources, TRUE);
        $this->load->view($this->controller . '/template', $template_resources);
    }

    public function load_transaction_page_table($id){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');

        try {
            $employee_info = require_login($this->controller);
            $id = decode($id);

            $draw       = intval($this->input->post('draw'));
            $start      = intval($this->input->post('start'));
            $length     = intval($this->input->post('length'));
            $search     = $this->input->post('search')['value'];

            // Get total records
            $this->db->select('COUNT(*) as total_records')
                ->from('transaction_form_tbl a')
                ->join('store_tbl b', 'b.store_id = a.store_id', 'left')
                ->where('a.form_id', $id)
                ->where('a.status !=', 0);

            $total_records_data = $this->db->get()->row();
            $total_records = $total_records_data->total_records;

            // Get all transaction data for filtering
            $this->db->select('a.*, b.store_name')
                ->from('transaction_form_tbl a')
                ->join('store_tbl b', 'b.store_id = a.store_id', 'left')
                ->where('a.form_id', $id)
                ->where('a.status !=', 0)
                ->order_by('created_at', 'desc');

            $all_data = $this->db->get()->result();

            // Decode Data
            foreach ($all_data as $key => $row) {
                $row->reference_number = decode($row->reference_number);
                $row->name = decode($row->name);
                $row->contact_number = decode($row->contact_number);
                $row->email = decode($row->email);
                $all_data[$key] = $row;
            }

            if (!empty($search)) {
                // Filter the data based on the search term
                $filtered_data = array_filter($all_data, function($row) use ($search) {
                    return (
                        stripos($row->reference_number, $search) !== false ||
                        stripos($row->or_number, $search) !== false ||
                        stripos($row->store_name, $search) !== false ||
                        stripos($row->name, $search) !== false ||
                        stripos($row->contact_number, $search) !== false ||
                        stripos($row->email, $search) !== false
                    );
                });
            } else {
                $filtered_data = $all_data;
            }

            // Count the total number of filtered records
            $total_filtered_records = count($filtered_data);

            // Paginate the filtered data
            $paginated_data = array_slice($filtered_data, $start, $length);

            $table_data = [];
            foreach ($paginated_data as $key => $value) {
                $status = '<span class="badge text-bg-success w-100">Active</span>';

                if ($value->status == 2) {
                    $status = '<span class="badge text-bg-danger w-100">Redeemed</span>';
                } else if (strtotime($value->created_at) < strtotime("-14 days") && $value->status == 1) {
                    $status = '<span class="badge text-bg-danger w-100">Expired</span>';
                }

                $action = '<div class="d-flex flex-column justify-content-center" style="gap:0.5rem">
                            <a class="btn btn-sm btn-primary text-white w-100" href="' . base_url($this->controller . '/transaction-view/' . encode($value->transaction_id)) . '">View</a>';
                if (strtotime($value->created_at) >= strtotime("-14 days") && $value->status == 1) {
                    $action .= '<button class="btn btn-sm btn-success text-white redeem-button" id="' . encode($value->transaction_id) . '" >Redeem</button>';
                }
                if (decode($employee_info['employee_id']) == 115) { // This needs to be changed some time in the future - Will be changed to a role-based access
                    $action .= '<button class="btn btn-sm btn-danger text-white disable-button" id="' . encode($value->transaction_id) . '" >Disable</button>';
                }
                $action .= '</div>';

                $table_data[] = [
                    'reference_number' => $value->reference_number,
                    'or_number'        => $value->or_number,
                    'store_name'       => $value->store_name ?? '',
                    'name'             => $value->name,
                    'contact_number'   => $value->contact_number,
                    'email'            => $value->email,
                    'date'             => $value->created_at,
                    'status'           => $status,
                    'action'           => $action,
                ];
            }

            $response = [
                "draw" => $draw,
                "recordsTotal" => $total_records,
                "recordsFiltered" => $total_filtered_records,
                "data" => $table_data
            ];

            echo json_encode($response);
        } catch (Exception $e) {
            // Handle general exceptions
            log_message('error', 'Error in load_transaction_page_table: ' . $e->getMessage());
            $response = [
                "draw" => $draw ?? 0,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "error" => "An error occurred while processing your request. Please try again later."
            ];
            echo json_encode($response);
        } catch (Throwable $t) {
            // Handle PHP 7+ errors (e.g., TypeErrors)
            log_message('error', 'Throwable error in load_transaction_page_table: ' . $t->getMessage());
            $response = [
                "draw" => $draw ?? 0,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "error" => "A system error occurred. Please contact support."
            ];
            echo json_encode($response);
        }
    }
    
	public function load_transaction_page_table_for_qr_promo($id){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');

		
		$parent_db = parent_db();
		$sibling_db = sibling_one_db();

        try {
            $employee_info = require_login($this->controller);
            $id = decode($id);

            $draw       = intval($this->input->post('draw'));
            $start      = intval($this->input->post('start'));
            $length     = intval($this->input->post('length'));
            $search     = $this->input->post('search')['value'];

            // Get total records
            $this->db->select('COUNT(*) as total_records')
                ->from('transaction_form_tbl a')
                // ->join('store_tbl b', 'b.store_id = a.store_id', 'left')
				->join("{$parent_db}.provinces_tbl b", 'a.province_id = b.province_id')
                ->join("{$parent_db}.town_groups_tbl c", 'a.town_group_id = c.town_group_id')
                ->join("{$parent_db}.barangay_tbl d", 'a.barangay_id = d.barangay_id')
				->join("{$sibling_db}.survey_winners_tbl e", 'a.transaction_id = e.ref_id AND e.survey_winner_status = 1', 'left')
                ->where('a.form_id', $id)
                ->where('a.status !=', 0);

            $total_records_data = $this->db->get()->row();
            $total_records = $total_records_data->total_records;

            // Get all transaction data for filtering
            $this->db->select('a.*, b.province_name, c.town_group_name, d.barangay_name, IF(ISNULL(e.ref_id), "NO", "YES") as winner_flag, e.created_at as winning_date')
                ->from('transaction_form_tbl a')
                // ->join('store_tbl b', 'b.store_id = a.store_id', 'left')
				->join("{$parent_db}.provinces_tbl b", 'a.province_id = b.province_id')
                ->join("{$parent_db}.town_groups_tbl c", 'a.town_group_id = c.town_group_id')
                ->join("{$parent_db}.barangay_tbl d", 'a.barangay_id = d.barangay_id')
				->join("{$sibling_db}.survey_winners_tbl e", 'a.transaction_id = e.ref_id AND e.survey_winner_status = 1', 'left')
                ->where('a.form_id', $id)
                ->where('a.status !=', 0)
                ->order_by('created_at', 'desc');

            $all_data = $this->db->get()->result();

            // Decode Data
            foreach ($all_data as $key => $row) {
                $row->reference_number = decode($row->reference_number);
                $row->name = decode($row->name);
                $row->contact_number = decode($row->contact_number);
                $row->email = decode($row->email);
                $all_data[$key] = $row;
            }

            if (!empty($search)) {
                // Filter the data based on the search term
                $filtered_data = array_filter($all_data, function($row) use ($search) {
                    return (
                        stripos($row->reference_number, $search) !== false ||
                        stripos($row->province_name, $search) !== false ||
                        stripos($row->barangay_name, $search) !== false ||
                        stripos($row->town_name, $search) !== false ||
                        stripos($row->name, $search) !== false ||
                        stripos($row->contact_number, $search) !== false ||
                        stripos($row->email, $search) !== false
                    );
                });
            } else {
                $filtered_data = $all_data;
            }

            // Count the total number of filtered records
            $total_filtered_records = count($filtered_data);

            // Paginate the filtered data
            $paginated_data = array_slice($filtered_data, $start, $length);

            $table_data = [];
            foreach ($paginated_data as $key => $value) {
                $status = '<span class="badge text-bg-success w-100">Active</span>';

                if ($value->status == 1) {
                    $status = '<span class="badge text-bg-success w-100">Active</span>';
                } else if ($value->status == 0) {
					$status = '<span class="badge text-bg-warning w-100">Inactive</span>';
                } else {
                    $status = '<span class="badge text-bg-danger w-100">Redeemed</span>';
				}

                $action = '<div class="d-flex flex-column justify-content-center" style="gap:0.5rem">
                            <a class="btn btn-sm btn-primary text-white w-100" href="' . base_url($this->controller . '/transaction-view/' . encode($value->transaction_id)) . '">View</a>';
                // if (strtotime($value->created_at) >= strtotime("-14 days") && $value->status == 1) {
                //     $action .= '<button class="btn btn-sm btn-success text-white redeem-button" id="' . encode($value->transaction_id) . '" >Redeem</button>';
                // }
                // if (decode($employee_info['employee_id']) == 115) { // This needs to be changed some time in the future - Will be changed to a role-based access
                //     $action .= '<button class="btn btn-sm btn-danger text-white disable-button" id="' . encode($value->transaction_id) . '" >Disable</button>';
                // }
                $action .= '</div>';

                $table_data[] = [
                    'reference_number' 		=> $value->reference_number,
                    'province_name'        	=> $value->province_name,
                    'brgy_town_name'       	=> $value->barangay_name.', '.$value->town_group_name ?? '',
                    'name'             		=> $value->name,
                    'contact_number'   		=> $value->contact_number,
                    'email'            		=> $value->email,
                    'date'             		=> $value->created_at,
                    'status'           		=> $status,
                    'action'           		=> $action,
                ];
            }

            $response = [
                "draw" => $draw,
                "recordsTotal" => $total_records,
                "recordsFiltered" => $total_filtered_records,
                "data" => $table_data
            ];

            echo json_encode($response);
        } catch (Exception $e) {
            // Handle general exceptions
            log_message('error', 'Error in load_transaction_page_table: ' . $e->getMessage());
            $response = [
                "draw" => $draw ?? 0,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "error" => "An error occurred while processing your request. Please try again later."
            ];
            echo json_encode($response);
        } catch (Throwable $t) {
            // Handle PHP 7+ errors (e.g., TypeErrors)
            log_message('error', 'Throwable error in load_transaction_page_table: ' . $t->getMessage());
            $response = [
                "draw" => $draw ?? 0,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "error" => "A system error occurred. Please contact support."
            ];
            echo json_encode($response);
        }
    }

    public function transaction_view($transaction = '')
    {
        $login_info = require_login($this->controller);
        
        if (empty($transaction)) show_404();
        $decoded_transaction = decode($transaction);

      
        $form = $this->main->get_data('transaction_form_tbl', ['transaction_id' => $decoded_transaction], TRUE, 'form_id');

        $join = [
            'form_field_tbl b' => 'a.section_id = b.section_id',
            'transaction_response_tbl c' => 'b.field_id = c.field_id AND c.transaction_id ='. $decoded_transaction,   
        ];
        
        $details = [
            'a.section_id',
            'b.field_id',
            'b.field_type_id',
            'c.field_name',
            'c.response',
        ];

        $data['fields']         = $this->main->get_join('form_section_tbl a', $join, FALSE, 'b.form_field_sequence asc', FALSE, $details, ['a.status' => 1]);
        $data['sections']       = $this->main->get_data('form_section_tbl', ['form_id' => $form->form_id, 'status' => 1]);

        $data['transaction_id']  = $decoded_transaction;
        $data['controller']      = $this->controller;
        $data['title']           = ucwords(str_replace('_', ' ', __FUNCTION__));
        $data['content']         = $this->load->view($this->controller . '/transaction/transaction_view_content', $data, TRUE);
        $this->load->view($this->controller . '/template', $data);
    }

    public function redeem_voucher(){
        $login_info = require_login($this->controller);

        $data = json_decode(file_get_contents('php://input'), true);

        $decoded_transaction_id = clean_data(decode($data['id']));

        if($this->main->check_data('transaction_form_tbl', ['transaction_id' => $decoded_transaction_id, 'status' => 2])){
            $data = [
                'result'   => FALSE,
                'message'  => 'Data Already Redeemed',
                'refresh' =>  TRUE,
            ];
            echo json_encode($data);
            return;
        }

        if($this->main->check_data('transaction_form_tbl', ['transaction_id' => $decoded_transaction_id, 'status' => 0])){
            $data = [
                'result'   => FALSE,
                'message'  => 'Data Already Disabled',
                'refresh' =>  TRUE,
            ];
            echo json_encode($data);
            return;
        }

        $this->db->trans_start();
        $this->main->update_data('transaction_form_tbl', ['status' => 2], ['transaction_id' => $decoded_transaction_id]);

        if ($this->db->trans_status()) {
            $this->db->trans_commit();

            $log = [
                'transaction_id' => $decoded_transaction_id,
                'created_at' => date_now(),
                'created_by' => decode($login_info['employee_id'])
            ];

            $this->main->insert_data('redeem_logs_tbl', $log);

            $data = [
                'result'   => TRUE,
                'message'  => 'Data Redeem Success',
                'redirect' => ''
            ];
        } else {
            $this->db->trans_rollback();
            $data = [
                'result'   => FALSE,
                'message'  => 'Data Redeem Failed',
            ];
        }
        echo json_encode($data);
        return;

    }

    public function disable_transaction(){
        require_login($this->controller);

        $data = json_decode(file_get_contents('php://input'), true);

        $decoded_transaction_id = clean_data(decode($data['id']));

        if($this->main->check_data('transaction_form_tbl', ['transaction_id' => $decoded_transaction_id, 'status' => 0])){
            $data = [
                'result'   => FALSE,
                'message'  => 'Data Already Disabled',
                'refresh' =>  TRUE,
            ];
            echo json_encode($data);
            return;
        }
        
        $this->db->trans_start();
        $this->main->update_data('transaction_form_tbl', ['status' => 0], ['transaction_id' => $decoded_transaction_id]);

        if ($this->db->trans_status()) {
            $this->db->trans_commit();
            $data = [
                'result'   => TRUE,
                'message'  => 'Data Disabled',
                'redirect' => ''
            ];
        } else {
            $this->db->trans_rollback();
            $data = [
                'result'   => FALSE,
                'message'  => 'Data Disable Failed',
            ];
        }
        echo json_encode($data);
        return;
    }

    // REPORTS MODULE
    public function load_report_page_table($id){ //Refractor using left join. merge the query
        require_login($this->controller);
        $id = decode($id);

        $draw       = intval($this->input->get('draw'));
        $start      = intval($this->input->get('start'));
        $length     = intval($this->input->get('length'));
        $date       = $this->input->get('date_filter');
        $store_id   = $this->input->get('store_filter');

        // Split the date filter into date_from and date_to
        $date_range = explode(' to ', $date);
        $date_from = isset($date_range[0]) ? $date_range[0] . ' 00:00:00' : '';
        $date_to = isset($date_range[1]) ? $date_range[1] . ' 23:59:59' : '';

        $this->db->select('a.*, b.store_name')
                ->from('transaction_form_tbl a')
                ->join('store_tbl b', 'a.store_id = b.store_id', 'left')
                ->where('a.form_id', $id)
                ->where('a.status !=', 0)
                ->order_by('a.created_at', 'desc')
                ->limit($length, $start);

        // Apply date filter if provided
        if (!empty($date_from) && !empty($date_to)) {
            $this->db->where('a.created_at >=', $date_from)
                    ->where('a.created_at <=', $date_to);
        }

        // Apply store filter if provided
        if (!empty($store_id)) {
            $this->db->where('a.store_id', $store_id);
        }

        $data = $this->db->get()->result();

        $this->db->select('COUNT(*) as total_records')
                ->from('transaction_form_tbl a')
                ->where('a.form_id', $id)
                ->where('a.status !=', 0);

        // Apply date filter if provided
        if (!empty($date_from) && !empty($date_to)) {
            $this->db->where('a.created_at >=', $date_from)
                    ->where('a.created_at <=', $date_to);
        }

        // Apply store filter if provided
        if (!empty($store_id)) {
            $this->db->where('a.store_id', $store_id);
        }

        $total_records = $this->db->get()->row()->total_records;

        $table_data = [];
        
        foreach($data as $key => $value){
            $table_data[] = [
                'reference_number' => decode($value->reference_number),
                'or_number'        => $value->or_number,
                'store_name'       => $value->store_name ?? '',
                'name'             => decode($value->name),
                'contact_number'   => decode($value->contact_number),
                'email'            => decode($value->email),
                'date'             => $value->created_at,
                'store_id'         => $value->store_id ?? '',
            ];
        };

        $response = [
            "draw" => $draw,
            "recordsTotal" => $total_records,
            "recordsFiltered" => $total_records,
            "data" => $table_data
        ];

        echo json_encode($response);
    }

	public function load_report_page_table_for_qr_promo($id){ //Refractor using left join. merge the query
        require_login($this->controller);
        $id = decode($id);
		$parent_db = parent_db();
		$sibling_db = sibling_one_db();

        $draw       = intval($this->input->get('draw'));
        $start      = intval($this->input->get('start'));
        $length     = intval($this->input->get('length'));
        $date       = $this->input->get('date_filter');
        $loc_id     = $this->input->get('loc_filter');


        // Split the date filter into date_from and date_to
        $date_range = explode(' to ', $date);
        $date_from = isset($date_range[0]) ? $date_range[0] . ' 00:00:00' : '';
        $date_to = isset($date_range[1]) ? $date_range[1] . ' 23:59:59' : '';

        $this->db->select('a.*, b.province_name, c.town_group_name, d.barangay_name, IF(ISNULL(e.ref_id), "NO", "YES") as winner_flag, e.created_at as winning_date')
                ->from('transaction_form_tbl a')
                // ->join('store_tbl b', 'a.store_id = b.store_id', 'left')
                ->join("{$parent_db}.provinces_tbl b", 'a.province_id = b.province_id')
                ->join("{$parent_db}.town_groups_tbl c", 'a.town_group_id = c.town_group_id')
                ->join("{$parent_db}.barangay_tbl d", 'a.barangay_id = d.barangay_id')
				->join("{$sibling_db}.survey_winners_tbl e", 'a.transaction_id = e.ref_id AND e.survey_winner_status = 1', 'left')
                ->where('a.form_id', $id)
                ->where('a.status !=', 0)
                ->order_by('a.created_at', 'desc')
                ->limit($length, $start);

        // Apply date filter if provided
        if (!empty($date_from) && !empty($date_to)) {
            $this->db->where('a.created_at >=', $date_from)
                    ->where('a.created_at <=', $date_to);
        }

        // Apply location filter if provided
        if (!empty($loc_id)) {
            $this->db->where('a.province_id', $loc_id);
        }

        $data = $this->db->get()->result();

        $this->db->select('COUNT(*) as total_records')
                ->from('transaction_form_tbl a')
                ->where('a.form_id', $id)
                ->where('a.status !=', 0);

        // Apply date filter if provided
        if (!empty($date_from) && !empty($date_to)) {
            $this->db->where('a.created_at >=', $date_from)
                    ->where('a.created_at <=', $date_to);
        }

        // Apply location filter if provided
        if (!empty($loc_id)) {
            $this->db->where('a.province_id', $loc_id);
        }

        $total_records = $this->db->get()->row()->total_records;

        $table_data = [];
        
        foreach($data as $key => $value){
			$name = decode($value->name);
			$name = ucwords(strtolower($name));
            $table_data[] = [
                'reference_number' 				=> decode($value->reference_number),
                'province_name'       			=> $value->province_name ?? '',
                'brgy_town_name'       			=> $value->barangay_name.', '.$value->town_group_name ?? '',
                'name'             				=> $name,
                'contact_number'   				=> decode($value->contact_number),
                'email'            				=> decode($value->email),
                'is_winner'            			=> $value->winner_flag,
                'winning_date'            		=> $value->winning_date ? date("Y-m-d", strtotime($value->winning_date)) : "",
                'date'             				=> $value->created_at,
                'province_id'         			=> $value->province_id ?? '',
            ];
        };

        $response = [
            "draw" => $draw,
            "recordsTotal" => $total_records,
            "recordsFiltered" => $total_records,
            "data" => $table_data
        ];

        echo json_encode($response);
    }

    public function cdi_survey_report(){
        $info = require_login($this->controller);
		
		$employee_id 					= decode($info['employee_id']);
        $forms 							= $this->_get_form_access($employee_id);
		$form_access_links 				= array_column($forms, 'internal_link');
        $report_menus 					= $this->_get_form_menu($forms);

        $stores  = $this->main->get_data('store_tbl', ['status' => 1]);

        $title   = ucwords(str_replace('_', ' ', __FUNCTION__));

        $cdi_page_resources = [
            'stores' => $stores,
            'title' => $title,
            'controller' => $this->controller,
        ];

        $content = $this->load->view($this->controller . '/report/cdi_report_content', $cdi_page_resources, TRUE);

        $template_resources = [
            'content' 					=> $content,
            'js_scripts' 				=> [
                'report/report_content_page.js?v=1.1'
            ],
			'form_access_links'         => $form_access_links,
            'report_menus'         		=> $report_menus,
        ];

        $this->load->view($this->controller . '/template', $template_resources);
    }

    public function ctg_survey_report(){
        $info 							= require_login($this->controller);
        $employee_id 					= decode($info['employee_id']);
        $forms 							= $this->_get_form_access($employee_id);
		$form_access_links 				= array_column($forms, 'internal_link');
        $report_menus 					= $this->_get_form_menu($forms);

        $title            = ucwords(str_replace('_', ' ', __FUNCTION__));

        $ctg_page_resources = [
            'title' => $title,
            'controller' => $this->controller,
        ];

        $content = $this->load->view($this->controller . '/report/ctg_report_content', $ctg_page_resources, TRUE);

        $template_resources = [
            'content' 					=> $content,
            'js_scripts' 				=> [
                'report/report_content_page.js?v=1.1'
            ],
			'form_access_links'         => $form_access_links,
            'report_menus'         		=> $report_menus,
        ];

        $this->load->view($this->controller . '/template', $template_resources);
    }
    
	public function ctg_qr_promo_survey_report(){
		
		$parent_db 						= parent_db();
		$form_id						= 3;
        $info 							= require_login($this->controller);
        $employee_id 					= decode($info['employee_id']);
        $forms 							= $this->_get_form_access($employee_id);
		$form_access_links 				= array_column($forms, 'internal_link');
        $report_menus 					= $this->_get_form_menu($forms);

        $title            				= ucwords(str_replace('_', ' ', __FUNCTION__));
		$provinces  					= $this->main->get_data("{$parent_db}.provinces_tbl b", ['province_status' => 1]);

        $ctg_page_resources = [
            'provinces' => $provinces,
            'title' => $title,
            'controller' => $this->controller,
			'form_id'	=> $form_id
        ];

        $content = $this->load->view($this->controller . '/report/qr_promo_report_content', $ctg_page_resources, TRUE);

        $template_resources = [
            'content' 					=> $content,
            'js_scripts' 				=> [
                'report/report_content_page.js?v=1.1'
            ],
			'form_access_links'         => $form_access_links,
            'report_menus'         		=> $report_menus,
        ];

        $this->load->view($this->controller . '/template', $template_resources);
    }
	
	public function ur_qr_promo_survey_report(){
		
		$parent_db 						= parent_db();
		$form_id						= 4;
        $info 							= require_login($this->controller);
        $employee_id 					= decode($info['employee_id']);
        $forms 							= $this->_get_form_access($employee_id);
		$form_access_links 				= array_column($forms, 'internal_link');
        $report_menus 					= $this->_get_form_menu($forms);

        $title            				= ucwords(str_replace('_', ' ', __FUNCTION__));
		$provinces  					= $this->main->get_data("{$parent_db}.provinces_tbl b", ['province_status' => 1]);

        $ctg_page_resources = [
            'provinces' => $provinces,
            'title' => $title,
            'controller' => $this->controller,
			'form_id'	=> $form_id
        ];

        $content = $this->load->view($this->controller . '/report/qr_promo_report_content', $ctg_page_resources, TRUE);

        $template_resources = [
            'content' 					=> $content,
            'js_scripts' 				=> [
                'report/report_content_page.js?v=1.1'
            ],
			'form_access_links'         => $form_access_links,
            'report_menus'         		=> $report_menus,
        ];

        $this->load->view($this->controller . '/template', $template_resources);
    }

    private function initialize_form_report_data($form_id){
        $filter = $this->input->post();

        $store_id = $filter['store-filter'];
        $date_filter = $filter['date-filter'];

        $date_range = explode(' to ', $date_filter);

        $date_from = isset($date_range[0]) ? $date_range[0] . ' 00:00:00' : '';
        $date_to = isset($date_range[1]) ? $date_range[1] . ' 23:59:59' : '';

        $where = ['a.form_id' => $form_id, 'a.status != ' => 0]; 

        if(!empty($date_from) && !empty($date_to)){
            $where += [
                'created_at >=' => $date_from,
                'created_at <=' => $date_to
            ];
        }

        if(!empty($store_id)){
            $where += ['b.store_id' => $store_id];
        }

        //Gets active transactions related to the form
        $this->db->select('a.*, b.store_name')
                ->from('transaction_form_tbl a')
                ->join('store_tbl b', 'a.store_id = b.store_id', 'left')
                ->where($where);

        $transaction_data = $this->db->get()->result();

        foreach($transaction_data as $key => $value){

            $this->db->select('field_id, response')
                     ->from('transaction_response_tbl')
                     ->where(['transaction_id' => $value->transaction_id]);
            
            $transaction_data[$key]->responses = $this->db->get()->result();
        }

        //Gets transactions table's columns that will be used for filtering questions later on
        $transaction_column_names = $this->db->list_fields('transaction_form_tbl');
        foreach($transaction_column_names as $key => $value){
            $transaction_column_names[$key] = str_replace('_', ' ', $value);
        }

        //Gets active form's question fields.
        $this->db->select('b.form_field_name, b.field_id, b.form_field_sequence')
                    ->from('form_section_tbl a')
                    ->join('form_field_tbl b', 'a.section_id = b.section_id AND b.status = 1')
                    ->where(['a.status' => 1, 'a.form_id' => $form_id])
                    ->order_by('a.section_sequence', 'asc')
                    ->order_by('b.form_field_sequence', 'asc');
        
        $questions = $this->db->get()->result();

        foreach($questions as $key => $value){
            $name = strtolower(str_replace('_', ' ', $value->form_field_name));
            if(in_array($name, $transaction_column_names)){
                unset($questions[$key]);
            }
        }        

        $form_report_data = [
            'transaction_data' => $transaction_data,
            'questions' => $questions,
        ];

        return $form_report_data;
    }
    
	private function initialize_form_report_data_for_qr_promo($form_id){
        $filter = $this->input->post();
		$parent_db = parent_db();
		$sibling_db = sibling_one_db();

        // $store_id = $filter['store-filter'];
        $loc_id = $filter['province-filter'];
        $date_filter = $filter['date-filter'];

        $date_range = explode(' to ', $date_filter);

        $date_from = isset($date_range[0]) ? $date_range[0] . ' 00:00:00' : '';
        $date_to = isset($date_range[1]) ? $date_range[1] . ' 23:59:59' : '';

        $where = ['a.form_id' => $form_id, 'a.status != ' => 0]; 

        if(!empty($date_from) && !empty($date_to)){
            $where += [
                'a.created_at >=' => $date_from,
                'a.created_at <=' => $date_to
            ];
        }

        if (!empty($loc_id)) {
			$where += ['a.province_id' => $loc_id];
        }

		

        //Gets active transactions related to the form
        $this->db->select('a.*, b.province_name, c.town_group_name, d.barangay_name, IF(ISNULL(e.ref_id), "NO", "YES") as winner_flag, e.created_at as winning_date')
                ->from('transaction_form_tbl a')
                // ->join('store_tbl b', 'a.store_id = b.store_id', 'left')
				->join("{$parent_db}.provinces_tbl b", 'a.province_id = b.province_id')
                ->join("{$parent_db}.town_groups_tbl c", 'a.town_group_id = c.town_group_id')
                ->join("{$parent_db}.barangay_tbl d", 'a.barangay_id = d.barangay_id')
				->join("{$sibling_db}.survey_winners_tbl e", 'a.transaction_id = e.ref_id AND e.survey_winner_status = 1', 'left')
                ->where($where)
				->order_by('a.created_at', 'desc');

        $transaction_data = $this->db->get()->result();

        foreach($transaction_data as $key => $value){

            $this->db->select('field_id, response')
                     ->from('transaction_response_tbl')
                     ->where(['transaction_id' => $value->transaction_id]);
            
            $transaction_data[$key]->responses = $this->db->get()->result();
        }

        //Gets transactions table's columns that will be used for filtering questions later on
        $transaction_column_names = $this->db->list_fields('transaction_form_tbl');
        foreach($transaction_column_names as $key => $value){
            $transaction_column_names[$key] = str_replace('_', ' ', $value);
        }

        //Gets active form's question fields.
        $this->db->select('b.form_field_name, b.field_id, b.form_field_sequence')
                    ->from('form_section_tbl a')
                    ->join('form_field_tbl b', 'a.section_id = b.section_id AND b.status = 1')
                    ->where(['a.status' => 1, 'a.form_id' => $form_id])
                    ->order_by('a.section_sequence', 'asc')
                    ->order_by('b.form_field_sequence', 'asc');
        
        $questions = $this->db->get()->result();

        foreach($questions as $key => $value){
            $name = strtolower(str_replace('_', ' ', $value->form_field_name));
            if(in_array($name, $transaction_column_names)){
                unset($questions[$key]);
            }
        }        

        $form_report_data = [
            'transaction_data' => $transaction_data,
            'questions' => $questions,
        ];

        return $form_report_data;
    }

    public function survey_export($form_id){
        require_login($this->controller);

        // error_reporting(0);
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 500);

		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
        
        $form_id = decode($form_id);

		if($form_id <= 2){
			$data = $this->initialize_form_report_data($form_id);
		} else {
			$data = $this->initialize_form_report_data_for_qr_promo($form_id);
		}

        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();

        // Set the active sheet
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        $defaultColumnHeaders = ['Reference', 'OR Number', 'Store', 'Name', 'Email', 'Date', 'Contact Number', 'Status', ''];

        // Set the column headers
        if($form_id == 2){
            unset($defaultColumnHeaders[2]);
        }
		elseif($form_id == 3 || $form_id == 4){
			$defaultColumnHeaders = ['Reference', 'Province', 'Brgy & Town', 'Name', 'Email', 'Created At', 'Contact Number', 'Status', 'Is Winner', 'Winner Date'];
		}

        $questionColumnHeaders = array_column($data['questions'], 'form_field_name');
        $columnHeaders = array_merge($defaultColumnHeaders, $questionColumnHeaders);

        $row = 1;
        $column = 0;
        foreach ($columnHeaders as $header) {
            $sheet->setCellValueByColumnAndRow($column, $row, $header);
            $column++;
        }
       
        $row = 2; 

        if($form_id == 1){
            foreach ($data['transaction_data'] as $row_data) {

                if ($row_data->status == 1) {
                    $row_data->status = strtotime($row_data->created_at) <= strtotime("-14 days") ? 'expired' : 'active';
                } elseif ($row_data->status == 2) {
                    $row_data->status = 'redeemed';
                }

                $data_to_set = [
                    decode($row_data->reference_number),
                    $row_data->or_number,
                    $row_data->store_name,
                    decode($row_data->name),
                    decode($row_data->email),
                    date('Y-m-d h:i A', strtotime($row_data->created_at)),
                    decode($row_data->contact_number),
                    $row_data->status
                ];
    
                foreach ($data_to_set as $col => $value) {
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
                }
    
                $field_id_to_col = [];
                $col = 9;
                foreach ($data['questions'] as $question) {
                    $field_id_to_col[$question->field_id] = $col;
                    $col++;
                }
                
                foreach ($row_data->responses as $row_response_data) {
                    if (!isset($field_id_to_col[$row_response_data->field_id])) {
                        continue;
                    }
                    $col = $field_id_to_col[$row_response_data->field_id];
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, decode($row_response_data->response));
                }
                $row++;
            }

        }elseif($form_id == 2){
            foreach ($data['transaction_data'] as $row_data) {

                if ($row_data->status == 1) {
                    $row_data->status = strtotime($row_data->created_at) <= strtotime("-14 days") ? 'expired' : 'active';
                } elseif ($row_data->status == 2) {
                    $row_data->status = 'redeemed';
                }
    
                $data_to_set = [
                    decode($row_data->reference_number),
                    $row_data->or_number,
                    $row_data->store_name,
                    decode($row_data->name),
                    decode($row_data->email),
                    date('Y-m-d h:i A', strtotime($row_data->created_at)),
                    decode($row_data->contact_number),
                    $row_data->status
                ];
    
                foreach ($data_to_set as $col => $value) {
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
                }
    
                $field_id_to_col = [];
                $col = 8;
                foreach ($data['questions'] as $question) {
                    $field_id_to_col[$question->field_id] = $col;
                    $col++;
                }
                
                foreach ($row_data->responses as $row_response_data) {
                    if (!isset($field_id_to_col[$row_response_data->field_id])) {
                        continue;
                    }
                    $col = $field_id_to_col[$row_response_data->field_id];
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, decode($row_response_data->response));
                }
                $row++;
                 
            }

        } else {
			foreach ($data['transaction_data'] as $row_data) {

                if ($row_data->status == 1) {
                    // $row_data->status = strtotime($row_data->created_at) <= strtotime("-14 days") ? 'expired' : 'active';
                    $row_data->status = 'Active';
                } elseif ($row_data->status == 2) {
                    $row_data->status = 'Inactive';
                }

				$name = decode($row_data->name);
				$name = ucwords(strtolower($name));

                $data_to_set = [
                    decode($row_data->reference_number),
                    $row_data->province_name,
                    $row_data->barangay_name.', '.$row_data->town_group_name,
                    $name,
                    decode($row_data->email),
                    date('Y-m-d h:i A', strtotime($row_data->created_at)),
                    decode($row_data->contact_number),
                    $row_data->status,
					$row_data->winner_flag,
					$row_data->winning_date ? date("Y-m-d", strtotime($row_data->winning_date)) : ""

                ];
    
                foreach ($data_to_set as $col => $value) {
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
                }
    
                $field_id_to_col = [];
                $col = 10;
                foreach ($data['questions'] as $question) {
                    $field_id_to_col[$question->field_id] = $col;
                    $col++;
                }
                
                foreach ($row_data->responses as $row_response_data) {
                    if (!isset($field_id_to_col[$row_response_data->field_id])) {
                        continue;
                    }
                    $col = $field_id_to_col[$row_response_data->field_id];
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, decode($row_response_data->response));
                }
                $row++;
            }
		}

        //Resizing left side to data size
        function getColumnsRange($first = 'A', $last = 'Z') {
            $columns = [];
            $current = $first;
            while ($current !== $last) {
                $columns[] = $current;
                $current++;
            }
            $columns[] = $last;
            return $columns;
        }
     
        foreach (getColumnsRange('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set the filename for the exported Excel file
        function formatDateRange($date_from, $date_to) {
            if(!empty($date_from) && !empty($date_to)){
				$start_date = strtotime($date_from);
				$end_date = strtotime($date_to);
			
				$is_start_first_day = date('j', $start_date) == 1;
		
				$is_end_last_day = date('t', $end_date) == date('j', $end_date);
			
				$is_same_month = date('Y-m', $start_date) == date('Y-m', $end_date);
			
				if ($is_start_first_day && $is_end_last_day && $is_same_month) {
					$month = date('F', $start_date);
					return 'Month of ' . $month;
				} else {
					$from = 'FROM ' . date('m-d-Y', $start_date);
					$to = 'TO ' . date('m-d-Y', $end_date);
					return $from . ' ' . $to;
				}
			} else {
				return null;
			}
        }

        $filter = $this->input->post();

        $date_filter = $filter['date-filter'];

        $date_range = explode(' to ', $date_filter);

        $date_from = isset($date_range[0]) && $date_range[0] > '1970-01-01' ? $date_range[0] : '';
        $date_to = isset($date_range[1]) && $date_range[1] > '1970-01-01' ? $date_range[1] : '';


		if($form_id == 1) $survey_tag = 'CDI';
		elseif($form_id == 2) $survey_tag = 'CTG';
		elseif($form_id == 3) $survey_tag = 'CTG QR Promo';
		elseif($form_id == 4) $survey_tag = 'UR QR Promo';
        // $survey_tag = $form_id == 1 ? 'CDI' : 'CTG';

        $date = formatDateRange($date_from, $date_to) ?? 'All Time';

        $exported_at = date('m-d-Y');

        $filename = $survey_tag . '-Survey-'.$exported_at.' - '.$date.'.xlsx';
        //example filename: CDI-Survey-01-01-2021 - FROM 01-01-2021 TO 01-31-2021.xlsx OR CDI-Survey-01-01-2021 - Month of January.xlsx

        // Set the appropriate headers for Excel file download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Save the PHPExcel object to a file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_end_clean();
        $objWriter->save('php://output');
   }

}
