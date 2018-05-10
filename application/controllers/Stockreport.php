<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stockreport extends CI_Controller {

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

		$this->load->model('salesorder_model');
		$this->load->model('client_model');
		$this->load->model('purchaseorder_model');
		$this->load->model('invoice_model');
		$this->load->model('user_model');
	}

	public function index()
	{
		redirect('stockreport/select');
	}

	public function update()
	{
		// update here
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
		$thisGET['salesorder_status_noteq'] = 'cancel';
		$thisGET['salesorder_deleted'] = 'N';

		/* check invoice */
		if(isset($thisGET['invoice_number_like']) || isset($thisGET['invoice_create_greateq']) || isset($thisGET['invoice_create_smalleq'])){
			$thisSelect = array(
				'where' => $thisGET,
				'return' => 'row'
			);
			$data['invoice'] = $this->invoice_model->select($thisSelect);

			if($data['invoice']){
				$thisGET['salesorder_id'] = $data['invoice']->invoice_salesorder_id;
			}else{
				$thisGET['salesorder_id'] = 0;
			}
		}
		/* check invoice */

		$thisSelect = array(
			'where' => $thisGET,
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['salesorders'] = $this->salesorder_model->select($thisSelect);

		foreach($data['salesorders'] as $key => $value){
			/* purchaseorder */
			$thisSelect = array(
				'where' => array(
					'purchaseorder_salesorder_id' => $value->salesorder_id,
					'purchaseorder_status_noteq' => 'cancel'
				),
				'return' => 'result'
			);
			$data['purchaseorders'] = $this->purchaseorder_model->select($thisSelect);
			$data['salesorders'][$key]->purchaseorders = $data['purchaseorders'];
		}

		foreach($data['salesorders'] as $key => $value){
			/* invoice */
			$thisSelect = array(
				'where' => array(
					'invoice_salesorder_id' => $value->salesorder_id,
					'invoice_status_noteq' => 'cancel'
				),
				'return' => 'result'
			);
			$data['invoices'] = $this->invoice_model->select($thisSelect);
			$data['salesorders'][$key]->invoices = $data['invoices'];
		}

		$thisSelect = array(
			'where' => $thisGET,
			'group' => 'salesorder_number',
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->salesorder_model->select($thisSelect);

		/* status */
		$data['statuss'] = (object)array(
			(object)array('status_name' => 'processing'),
			(object)array('status_name' => 'complete'),
			(object)array('status_name' => 'cancel')
		);

		/* user */
		$thisSelect = array(
			'return' => 'result'
		);
		$data['users'] = $this->user_model->select($thisSelect);

		/* pagination */
		$this->pagination->initialize(get_pagination_config($per_page, $data['num_rows']));

		$this->load->view('stockreport_view', $data);
	}

}
