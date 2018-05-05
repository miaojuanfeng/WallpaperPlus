<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Waybillout extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		check_session_timeout();
		check_is_login();
		convert_get_slashes_pretty_link();
		check_permission();

		$this->load->model('waybillout_model');
		$this->load->model('invoice_model');
	}

	public function index()
	{
		redirect('waybillout/select');
	}

	public function update()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$this->waybillout_model->update($thisPOST);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['waybillout_id'];
			set_log($thisLog);

			redirect($thisPOST['referrer']);
		}else{
			/* waybillout */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'row'
			);
			$data['waybillout'] = $this->waybillout_model->select($thisSelect);

            /* invoice */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['invoices'] = $this->invoice_model->select($thisSelect);

			$this->load->view('waybillout_view', $data);
		}
	}

	public function delete()
	{
		$thisPOST = $this->input->post();
		$this->waybillout_model->delete($thisPOST);

		$thisLog['log_permission_class'] = $this->router->fetch_class();
		$thisLog['log_permission_action'] = $this->router->fetch_method();
		$thisLog['log_record_id'] = $thisPOST['waybillout_id'];
		set_log($thisLog);

		redirect($this->agent->referrer());
	}

	public function insert()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$thisInsertId = $this->waybillout_model->insert($thisPOST);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['waybillout_id'];
			set_log($thisLog);

			redirect($thisPOST['referrer']);
		}else{
			/* preset empty data */
			$thisArray = array();
			foreach($this->waybillout_model->structure() as $key => $value){
				$thisArray[$value->Field] = '';
			}
			$data['waybillout'] = (object)$thisArray;

            /* invoice */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['invoices'] = $this->invoice_model->select($thisSelect);

			$this->load->view('waybillout_view', $data);
		}
	}

	public function select()
	{
		$per_page = 3;

		$thisSelect = array(
			'where' => $this->uri->uri_to_assoc(),
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['waybillouts'] = $this->waybillout_model->select($thisSelect);

		$thisSelect = array(
			'where' => $this->uri->uri_to_assoc(),
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->waybillout_model->select($thisSelect);

		/* pagination */
		$this->pagination->initialize(get_pagination_config($per_page, $data['num_rows']));

		$this->load->view('waybillout_view', $data);
	}

}
