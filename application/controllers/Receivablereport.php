<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receivablereport extends CI_Controller {

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

		$this->load->model('invoice_model');
		$this->load->model('user_model');
	}

	public function index()
	{
		redirect('receivablereport/select');
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
		$thisGET['invoice_status'] = 'processing';
		$thisGET['invoice_deleted'] = 'N';

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

				$thisGET['invoice_quotation_user_id_in'] = $data['user_ids'];
				break;
			case in_array('4', $this->session->userdata('role')): // sales
				/* get own client */
				$thisGET['invoice_quotation_user_id'] = $this->session->userdata('user_id');
				break;
			default:
				break;
		}

		$thisSelect = array(
			'where' => $thisGET,
			'order' => 'invoice_client_id',
			'ascend' => 'asc',
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['invoices'] = $this->invoice_model->select($thisSelect);

		$thisSelect = array(
			'where' => $thisGET,
			'group' => 'invoice_number',
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->invoice_model->select($thisSelect);

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

		$this->load->view('receivablereport_view', $data);
	}

}
