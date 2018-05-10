<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approval extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		check_session_timeout();
		check_is_login();
		convert_get_slashes_pretty_link();
		check_permission();

		$this->load->model('approval_model');
		$this->load->model('quotation_model');
	}

	public function index()
	{
		redirect('approval/update');
	}

	public function update()
	{
		/* approval */
		$thisSelect = array(
			'where' => array('setting_name' => 'approval_key'),
			'return' => 'row'
		);
		$data['approval'] = $this->approval_model->select($thisSelect);

		/* quotation */
		$thisSelect = array(
			'return' => 'results'
		);
		$data['quotations'] = $this->quotation_model->select($thisSelect);

		$data['approval_code'] = '';
		if($this->input->post()){
			$thisPOST = $this->input->post();

			$thisApproval = get_array_prefix('approval_', $thisPOST);
			
			$approval_quotation = str_replace('QO', '', $thisApproval['approval_quotation']);
			$approval_key = $thisApproval['approval_key'];
			$approval_date = str_replace('-', '', $thisApproval['approval_date']);

			$approval_code = intval($approval_quotation) + intval($approval_key) + intval($approval_date);

			if( strlen($approval_code) > 8 ){
				$approval_code = substr($approval_code, strlen($approval_code) - 8);
			}
			$data['approval_code'] = $approval_code;
			$data['approval_quotation'] = 'QO'.$approval_quotation;
		}
		$this->load->view('approval_view', $data);
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
		// select here
	}

}
