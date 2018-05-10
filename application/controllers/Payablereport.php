<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payablereport extends CI_Controller {

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

		$this->load->model('purchaseorder_model');
		$this->load->model('user_model');
	}

	public function index()
	{
		redirect('payablereport/select');
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
		$thisGET['purchaseorder_status'] = 'processing';
		$thisGET['purchaseorder_deleted'] = 'N';

		$thisSelect = array(
			'where' => $thisGET,
			'order' => 'purchaseorder_vendor_id',
			'ascend' => 'asc',
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['purchaseorders'] = $this->purchaseorder_model->select($thisSelect);

		$thisSelect = array(
			'where' => $thisGET,
			'group' => 'purchaseorder_number',
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->purchaseorder_model->select($thisSelect);

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

		$this->load->view('payablereport_view', $data);
	}

}
