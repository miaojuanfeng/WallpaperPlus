<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		check_session_timeout();
		check_is_login();
		convert_get_slashes_pretty_link();
		check_permission();

		$this->load->model('role_model');
		$this->load->model('user_model');
        $this->load->model('team_model');
		$this->load->model('z_role_user_model');
	}

	public function index()
	{
		redirect('user/select');
	}

	public function update()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$this->user_model->update($thisPOST);
			$this->z_role_user_model->delete($thisPOST);
			$this->z_role_user_model->insert($thisPOST);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['user_id'];
			set_log($thisLog);

			redirect($thisPOST['referrer']);
		}else{
			/* user */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'result'
			);
			$data['user'] = $this->user_model->select($thisSelect)[0];

			/* role */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['roles'] = $this->role_model->select($thisSelect);
			
			/* z_role_user */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'result'
			);
			$data['z_role_user_role_ids'] = convert_object_to_array($this->z_role_user_model->select($thisSelect), 'z_role_user_role_id');
			
			/* get sales manager user */
			$thisSelect = array(
				'where' => array(
					'role_id' => 3 // sales manager group
				),
				'return' => 'result'
			);
			$data['z_role_user_user_ids'] = convert_object_to_array($this->z_role_user_model->select($thisSelect), 'z_role_user_user_id');

            /* team */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['teams'] = $this->team_model->select($thisSelect);

			$thisSelect = array(
				'where' => array(
					// 'user_id_noteq' => $this->session->userdata('user_id'),
					'user_id_in' => $data['z_role_user_user_ids']
				),
				'return' => 'result'
			);
			$data['users'] = $this->user_model->select($thisSelect);
			/* get sales manager user */

			$this->load->view('user_view', $data);
		}
	}

	public function delete()
	{
		$thisPOST = $this->input->post();
		$this->user_model->delete($thisPOST);

		$thisLog['log_permission_class'] = $this->router->fetch_class();
		$thisLog['log_permission_action'] = $this->router->fetch_method();
		$thisLog['log_record_id'] = $thisPOST['user_id'];
		set_log($thisLog);

		redirect($this->agent->referrer());
	}

	public function insert()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$thisInsertId = $this->user_model->insert($thisPOST);
			$thisPOST['user_id'] = $thisInsertId;
			$this->z_role_user_model->insert($thisPOST);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['user_id'];
			set_log($thisLog);
			
			redirect($thisPOST['referrer']);
		}else{
			/* preset empty data */
			$thisArray = array();
			foreach($this->user_model->structure() as $key => $value){
				$thisArray[$value->Field] = '';
			}
			$data['user'] = (object)$thisArray;

			/* role */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['roles'] = $this->role_model->select($thisSelect);
			
			/* z_role_user */
			$data['z_role_user_role_ids'] = array();
			
			/* get sales manager user */
			$thisSelect = array(
				'where' => array(
					'role_id' => 3 // sales manager group
				),
				'return' => 'result'
			);
			$data['z_role_user_user_ids'] = convert_object_to_array($this->z_role_user_model->select($thisSelect), 'z_role_user_user_id');

			/* team */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['teams'] = $this->team_model->select($thisSelect);

			$thisSelect = array(
				'where' => array(
					// 'user_id_noteq' => $this->session->userdata('user_id'),
					'user_id_in' => $data['z_role_user_user_ids']
				),
				'return' => 'result'
			);
			$data['users'] = $this->user_model->select($thisSelect);
			/* get sales manager user */

			$this->load->view('user_view', $data);
		}
	}

	public function select()
	{
		$per_page = 12;

		/* user */
		$thisSelect = array(
			'where' => $this->uri->uri_to_assoc(),
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['users'] = $this->user_model->select($thisSelect);

		/* num rows */
		$thisSelect = array(
			'where' => $this->uri->uri_to_assoc(),
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->user_model->select($thisSelect);

		/* pagination */
		$this->pagination->initialize(get_pagination_config($per_page, $data['num_rows']));

		$this->load->view('user_view', $data);
	}

}
