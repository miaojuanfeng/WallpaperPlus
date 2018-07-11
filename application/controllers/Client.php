<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		check_session_timeout();
		check_is_login();
		convert_get_slashes_pretty_link();
		check_permission();

		$this->load->model('client_model');
		$this->load->model('location_model');
		$this->load->model('terms_model');
		$this->load->model('user_model');
		$this->load->model('z_client_user_model');
	}

	public function index()
	{
		redirect('client/select');
	}

	public function update()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$this->client_model->update($thisPOST);
			$this->z_client_user_model->delete($thisPOST);
			$this->z_client_user_model->insert($thisPOST);
			$thisInsertId = $thisPOST['client_id'];

			/* attachment */
			$attachment_path = $_SERVER['DOCUMENT_ROOT'].'/assets/images/attachment/client/';
			if($_FILES['attachment']['error'] == UPLOAD_ERR_OK){
				move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment_path.$thisInsertId);
			}

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['client_id'];
			set_log($thisLog);

			redirect($thisPOST['referrer']);
		}else{
			/* client */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'row'
			);
			$data['client'] = $this->client_model->select($thisSelect);

			$thisSelect = array(
				'where' => array(
					'client_id_noteq' => $data['client']->client_id
				),
				'return' => 'result'
			);
			$data['clients'] = $this->client_model->select($thisSelect);

			/* gender */
			$data['genders'] = (object)array(
				(object)array('gender_name' => 'M'),
				(object)array('gender_name' => 'F'),
			);

			/* user */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['users'] = $this->user_model->select($thisSelect);
			
			/* z_client_user */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'result'
			);
			$data['z_client_user_user_ids'] = convert_object_to_array($this->z_client_user_model->select($thisSelect), 'z_client_user_user_id');

			/* location */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['locations'] = $this->location_model->select($thisSelect);

			/* terms */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['termss'] = $this->terms_model->select($thisSelect);

			$this->load->view('client_view', $data);
		}
	}

	public function delete()
	{
		$thisPOST = $this->input->post();
		$this->client_model->delete($thisPOST);

		$thisLog['log_permission_class'] = $this->router->fetch_class();
		$thisLog['log_permission_action'] = $this->router->fetch_method();
		$thisLog['log_record_id'] = $thisPOST['client_id'];
		set_log($thisLog);

		redirect($this->agent->referrer());
	}

	public function insert()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$thisInsertId = $this->client_model->insert($thisPOST);
            $thisPOST['client_id'] = $thisInsertId;
			$this->z_client_user_model->insert($thisPOST);

			/* attachment */
			$attachment_path = $_SERVER['DOCUMENT_ROOT'].'/assets/images/attachment/client/';
			if($_FILES['attachment']['error'] == UPLOAD_ERR_OK){
				move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment_path.$thisInsertId);
			}

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['client_id'];
			set_log($thisLog);

			redirect($thisPOST['referrer']);
		}else{
			/* preset empty data */
			$thisArray = array();
			foreach($this->client_model->structure() as $key => $value){
				$thisArray[$value->Field] = '';
			}
			$data['client'] = (object)$thisArray;

			$thisSelect = array(
				'where' => array(
					'client_id_noteq' => $data['client']->client_id
				),
				'return' => 'result'
			);
			$data['clients'] = $this->client_model->select($thisSelect);

			/* gender */
			$data['genders'] = (object)array(
				(object)array('gender_name' => 'M'),
				(object)array('gender_name' => 'F'),
			);

			/* user */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['users'] = $this->user_model->select($thisSelect);
			
			/* z_client_user */
			$data['z_client_user_user_ids'] = array();

			/* location */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['locations'] = $this->location_model->select($thisSelect);

			/* terms */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['termss'] = $this->terms_model->select($thisSelect);

			$this->load->view('client_view', $data);
		}
	}

	public function select()
	{
		$per_page = get_setting('per_page')->setting_value;

		$thisGET = $this->uri->uri_to_assoc();

		/* client */
		switch(true){
			case in_array('3', $this->session->userdata('role')): // sales manager
				/* get own & downline client */
				$thisSelect = array(
					'where' => array(
						'OWN_USER_ID_AND_DOWNLINE_USER_ID' => $this->session->userdata('user_id')
					),
					'return' => 'result'
				);
				$data['user_ids'] = convert_object_to_array($this->user_model->select($thisSelect), 'user_id');

				$thisGET['client_user_id_in'] = $data['user_ids'];
				break;
			case in_array('4', $this->session->userdata('role')): // sales
				/* get own client */
				/* z_client_user */
				$thisSelect = array(
					'where' => array(
						'user_id' => $this->session->userdata('user_id')
					),
					'return' => 'result'
				);
				$thisGET['client_id_in'] = convert_object_to_array($this->z_client_user_model->select($thisSelect), 'z_client_user_client_id');
				break;
			default:
				$thisGET['client_user_id'] = $this->session->userdata('user_id');
				break;
		}

		$thisSelect = array(
			'where' => $thisGET,
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['clients'] = $this->client_model->select($thisSelect);

		$thisSelect = array(
			'where' => $thisGET,
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->client_model->select($thisSelect);

		/* pagination */
		$this->pagination->initialize(get_pagination_config($per_page, $data['num_rows']));

		$this->load->view('client_view', $data);
	}

}
