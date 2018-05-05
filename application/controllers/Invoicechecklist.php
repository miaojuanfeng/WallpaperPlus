<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoicechecklist extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// session_write_close();
		// session_id($this->input->post()['thisSession']);
		// session_start();
		check_session_timeout();
		check_is_login();
		convert_get_slashes_pretty_link();
		check_permission();

		$this->load->model('invoicechecklist_model');
		$this->load->model('salesorder_model');
		$this->load->model('invoice_model');
		$this->load->model('user_model');
	}

	public function index()
	{
		redirect('invoicechecklist/select');
	}

	public function update()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$this->invoicechecklist_model->update($thisPOST);

			/* set salesorder status */
			set_salesorder_status_complete(get_invoice($thisPOST['invoice_id'])->invoice_salesorder_id);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['invoice_id'];
			set_log($thisLog);

			$thisAlert = 'Data saved';
			$this->session->set_tempdata('alert', '<div class="btn btn-sm btn-primary btn-block bottom-buffer-10">'.$thisAlert.'</div>', 0);
			redirect('invoicechecklist/select/invoice_status/processing');
		}
	}

	public function delete()
	{
		// delete here
	}

	public function insert()
	{
		// insert here
	}

	public function select()
	{
		$per_page = get_setting('per_page')->setting_value;

		$thisGET = $this->uri->uri_to_assoc();
		// $thisGET['invoice_status'] = 'processing';
		$thisGET['invoice_deleted'] = 'N';

		/* check salesorder */
		if(isset($thisGET['salesorder_number_like'])){
			$thisSelect = array(
				'where' => $thisGET,
				'return' => 'result'
			);
			$data['salesorders'] = $this->salesorder_model->select($thisSelect);

			if($data['salesorders']){
				foreach($data['salesorders'] as $key => $value){
					$thisGET['invoice_salesorder_id_in'][] = $value->salesorder_id;
				}
			}else{
				$thisGET['invoice_salesorder_id_in'] = array(0);
			}
		}
		/* check salesorder */
		
		$thisSelect = array(
			'where' => $thisGET,
			'group' => 'invoice_number',
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['invoices'] = $this->invoicechecklist_model->select($thisSelect);

		$thisSelect = array(
			'where' => $thisGET,
			'group' => 'invoice_number',
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->invoicechecklist_model->select($thisSelect);

		/* status */
		$data['statuss'] = (object)array(
			(object)array('status_name' => 'processing'),
			(object)array('status_name' => 'complete')
		);

		/* user */
		$thisSelect = array(
			'return' => 'result'
		);
		$data['users'] = $this->user_model->select($thisSelect);

		/* pagination */
		$this->pagination->initialize(get_pagination_config($per_page, $data['num_rows']));

		$this->load->view('invoicechecklist_view', $data);
	}

}
