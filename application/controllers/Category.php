<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		check_session_timeout();
		check_is_login();
		convert_get_slashes_pretty_link();
		check_permission();

		$this->load->model('category_model');
        $this->load->model('team_model');
	}

	public function index()
	{
		redirect('category/select');
	}

	public function update()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$this->category_model->update($thisPOST);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['category_id'];
			set_log($thisLog);

			redirect($thisPOST['referrer']);
		}else{
			/* category */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'row'
			);
			$data['category'] = $this->category_model->select($thisSelect);

            /* team */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['teams'] = $this->team_model->select($thisSelect);

			$this->load->view('category_view', $data);
		}
	}

	public function delete()
	{
		$thisPOST = $this->input->post();
		$this->category_model->delete($thisPOST);

		$thisLog['log_permission_class'] = $this->router->fetch_class();
		$thisLog['log_permission_action'] = $this->router->fetch_method();
		$thisLog['log_record_id'] = $thisPOST['category_id'];
		set_log($thisLog);

		redirect($this->agent->referrer());
	}

	public function insert()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$thisInsertId = $this->category_model->insert($thisPOST);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['category_id'];
			set_log($thisLog);

			redirect($thisPOST['referrer']);
		}else{
			/* preset empty data */
			$thisArray = array();
			foreach($this->category_model->structure() as $key => $value){
				$thisArray[$value->Field] = '';
			}
			$data['category'] = (object)$thisArray;

            /* team */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['teams'] = $this->team_model->select($thisSelect);

			$this->load->view('category_view', $data);
		}
	}

	public function select()
	{
        $per_page = get_setting('per_page')->setting_value;

		$thisSelect = array(
			'where' => $this->uri->uri_to_assoc(),
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['categorys'] = $this->category_model->select($thisSelect);

		$thisSelect = array(
			'where' => $this->uri->uri_to_assoc(),
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->category_model->select($thisSelect);

		/* pagination */
		$this->pagination->initialize(get_pagination_config($per_page, $data['num_rows']));

		$this->load->view('category_view', $data);
	}

}
