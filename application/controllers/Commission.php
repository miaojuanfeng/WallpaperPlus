<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Commission extends CI_Controller {

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

		$this->load->model('commission_model');
		$this->load->model('quotation_model');
		$this->load->model('purchaseorder_model');
		$this->load->model('user_model');
		$this->load->model('quotationitem_model');
	}

	public function index()
	{
		redirect('commission/select');
	}

	public function update()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$this->commission_model->update($thisPOST);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['salesorder_id'];
			set_log($thisLog);

			$thisAlert = 'Data saved';
			$this->session->set_tempdata('alert', '<div class="btn btn-sm btn-primary btn-block bottom-buffer-10">'.$thisAlert.'</div>', 0);
			redirect('commission/select');
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
		$thisGET['salesorder_status'] = 'complete';
		$thisGET['salesorder_deleted'] = 'N';

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

				$thisGET['salesorder_user_id_in'] = $data['user_ids'];
				break;
			case in_array('4', $this->session->userdata('role')): // sales
				/* get own client */
				$thisGET['salesorder_user_id'] = $this->session->userdata('user_id');
				break;
			default:
				break;
		}

		/* check quotation */
		if(isset($thisGET['quotation_number_like'])){
			$thisSelect = array(
				'where' => $thisGET,
				'return' => 'result'
			);
			$data['quotations'] = $this->quotation_model->select($thisSelect);

			if($data['quotations']){
				foreach($data['quotations'] as $key => $value){
					$thisGET['salesorder_quotation_id_in'][] = $value->quotation_id;
				}
			}else{
				$thisGET['salesorder_quotation_id_in'] = array(0);
			}
		}
		/* check quotation */

		/* check quotationitem */
		if(isset($thisGET['quotationitem_product_code_like']) || isset($thisGET['quotationitem_product_name_like']) || isset($thisGET['quotationitem_product_detail_like'])){
			$thisSelect = array(
				'where' => $thisGET,
				'return' => 'result'
			);
			$data['quotationitems'] = $this->quotationitem_model->select($thisSelect);

			if($data['quotationitems']){
				foreach($data['quotationitems'] as $key => $value){
					$thisGET['salesorder_quotation_id_in'][] = $value->quotationitem_quotation_id;
				}
			}else{
				$thisGET['salesorder_quotation_id_in'] = array(0);
			}
		}
		/* check quotationitem */
		
		$thisSelect = array(
			'where' => $thisGET,
			'group' => 'salesorder_number',
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['salesorders'] = $this->commission_model->select($thisSelect);

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

		$thisSelect = array(
			'where' => $thisGET,
			'group' => 'salesorder_number',
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->commission_model->select($thisSelect);

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

		$this->load->view('commission_view', $data);
	}

}
