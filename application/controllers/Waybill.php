<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Waybill extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		check_session_timeout();
		check_is_login();
		convert_get_slashes_pretty_link();
		check_permission();

		$this->load->model('waybill_model');
		$this->load->model('purchaseorder_model');
	}

	public function index()
	{
		redirect('waybill/select');
	}

	public function update()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$this->waybill_model->update($thisPOST);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['waybill_id'];
			set_log($thisLog);

			redirect($thisPOST['referrer']);
		}else{
			/* waybill */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'row'
			);
			$data['waybill'] = $this->waybill_model->select($thisSelect);

            /* purchaseorder */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['purchaseorders'] = $this->purchaseorder_model->select($thisSelect);

			$this->load->view('waybill_view', $data);
		}
	}

	public function delete()
	{
		$thisPOST = $this->input->post();
		$this->waybill_model->delete($thisPOST);

		$thisLog['log_permission_class'] = $this->router->fetch_class();
		$thisLog['log_permission_action'] = $this->router->fetch_method();
		$thisLog['log_record_id'] = $thisPOST['waybill_id'];
		set_log($thisLog);

		redirect($this->agent->referrer());
	}

	public function insert()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$thisInsertId = $this->waybill_model->insert($thisPOST);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['waybill_id'];
			set_log($thisLog);

			redirect($thisPOST['referrer']);
		}else{
			/* preset empty data */
			$thisArray = array();
			foreach($this->waybill_model->structure() as $key => $value){
				$thisArray[$value->Field] = '';
			}
			$data['waybill'] = (object)$thisArray;

            /* purchaseorder */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['purchaseorders'] = $this->purchaseorder_model->select($thisSelect);

			$this->load->view('waybill_view', $data);
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
		$data['waybills'] = $this->waybill_model->select($thisSelect);

		$thisSelect = array(
			'where' => $this->uri->uri_to_assoc(),
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->waybill_model->select($thisSelect);

		/* pagination */
		$this->pagination->initialize(get_pagination_config($per_page, $data['num_rows']));

		$this->load->view('waybill_view', $data);
	}

}
