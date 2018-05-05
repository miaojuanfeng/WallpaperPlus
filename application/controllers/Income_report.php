<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Income_report extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		check_session_timeout();
		check_is_login();
		convert_get_slashes_pretty_link();
		check_permission();

		// $this->load->model('quotation_model');
	}

	public function index()
	{
		redirect('income_report/select');
	}

	public function update()
	{
		// update here.
	}

	public function delete()
	{
		// delete here.
	}

	public function insert()
	{
		// insert here.
	}

	public function select()
	{
		// $per_page = 1;

		// $thisGET = $this->uri->uri_to_assoc();
		// $thisGET['quotation_deleted'] = 'N';

		// $thisSelect = array(
		// 	'select' => array(
		// 		'*',
		// 		'max(quotation_id) as max_quotation_id',
		// 		'max(quotation_version) as max_quotation_version'
		// 	),
		// 	'where' => $thisGET,
		// 	'group' => 'quotation_number',
		// 	'limit' => $per_page,
		// 	'return' => 'result'
		// );
		// $data['quotations'] = $this->quotation_model->select($thisSelect);

		// $thisSelect = array(
		// 	'select' => array(
		// 		'*',
		// 		'max(quotation_id) as max_quotation_id',
		// 		'max(quotation_version) as max_quotation_version'
		// 	),
		// 	'where' => $thisGET,
		// 	'group' => 'quotation_number',
		// 	'return' => 'num_rows'
		// );
		// $data['num_rows'] = $this->quotation_model->select($thisSelect);

		/* pagination */
		// $this->pagination->initialize(get_pagination_config($per_page, $data['num_rows']));

		// $this->load->view('report_view', $data);
		$this->load->view('income_report_view');
	}

}
