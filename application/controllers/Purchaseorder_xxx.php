<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchaseorder extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		check_session_timeout();
		check_is_login();
		convert_get_slashes_pretty_link();
		check_permission();

		$this->load->model('quotation_model');
		$this->load->model('client_model');
		$this->load->model('product_model');
		$this->load->model('user_model');
		$this->load->model('quotationitem_model');
	}

	public function index()
	{
		redirect('purchaseorder/select');
	}

	public function update()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$thisPOST['quotation_version'] = get_quotation_version($thisPOST['quotation_number']) + 1;
			$thisInsertId = $this->quotation_model->insert($thisPOST);

			$thisPurchaseorderitem = get_array_prefix('quotationitem_', $thisPOST);
			$thisPurchaseorderitem = convert_formArray_to_DBArray($thisPurchaseorderitem, 'quotationitem_product_name'); //form array to DB array
			foreach($thisPurchaseorderitem as $key => $value){
				$value['quotationitem_quotation_id'] = $thisInsertId;
				$this->quotationitem_model->insert($value);
			}

			/* attachment */
			$attachment_path = $_SERVER['DOCUMENT_ROOT'].'/crm/assets/images/attachment/quotation/';
			if($_FILES['attachment']['error'] == UPLOAD_ERR_OK){
				move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment_path.$thisPOST['quotation_number']);
			}

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['quotation_id'];
			set_log($thisLog);

			redirect($thisPOST['referrer']);
		}else{
			/* quotation */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'row'
			);
			$data['quotation'] = $this->quotation_model->select($thisSelect);

			/* language */
			$data['languages'] = (object)array(
				(object)array('language_name' => 'en'),
				(object)array('language_name' => 'tc'),
				(object)array('language_name' => 'sc')
			);

			/* currency */
			$data['currencys'] = (object)array(
				(object)array('currency_name' => 'rmb'),
				(object)array('currency_name' => 'hkd'),
				(object)array('currency_name' => 'usd')
			);

			/* client */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['clients'] = $this->client_model->select($thisSelect);

			/* product */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['products'] = $this->product_model->select($thisSelect);

			/* user */
			$thisSelect = array(
				'where' => array(
					'user_id' => $this->session->userdata('user_id')
				),
				'return' => 'row'
			);
			$data['user'] = $this->user_model->select($thisSelect);

			/* quotation */
			$thisSelect = array(
				'where' => array(
					'quotationitem_quotation_id' => $data['quotation']->quotation_id
				),
				'return' => 'result'
			);
			$data['quotationitems'] = $this->quotationitem_model->select($thisSelect);

			$this->load->view('purchaseorder_view', $data);
		}
	}

	public function delete()
	{
		$thisPOST = $this->input->post();
		$this->quotation_model->delete($thisPOST);

		$thisLog['log_permission_class'] = $this->router->fetch_class();
		$thisLog['log_permission_action'] = $this->router->fetch_method();
		$thisLog['log_record_id'] = $thisPOST['quotation_id'];
		set_log($thisLog);

		redirect($this->agent->referrer());
	}

	public function insert()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$thisPOST['quotation_serial'] = sprintf("%02s", (get_quotation_serial() + 1));
			$thisPOST['quotation_number'] = date('Ymd').'-'.$thisPOST['quotation_serial'];
			$thisPOST['quotation_version'] = 1;
			$thisInsertId = $this->quotation_model->insert($thisPOST);

			$thisPurchaseorderitem = get_array_prefix('quotationitem_', $thisPOST);
			$thisPurchaseorderitem = convert_formArray_to_DBArray($thisPurchaseorderitem, 'quotationitem_product_name');
			foreach($thisPurchaseorderitem as $key => $value){
				$value['quotationitem_quotation_id'] = $thisInsertId;
				$this->quotationitem_model->insert($value);
			}

			/* attachment */
			$attachment_path = $_SERVER['DOCUMENT_ROOT'].'/crm/assets/images/attachment/quotation/';
			if($_FILES['attachment']['error'] == UPLOAD_ERR_OK){
				move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment_path.$thisPOST['quotation_number']);
			}

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['quotation_id'];
			set_log($thisLog);

			redirect($thisPOST['referrer']);
		}else{
			/* preset empty data */
			$thisArray = array();
			foreach($this->quotation_model->structure() as $key => $value){
				$thisArray[$value->Field] = '';
			}
			$data['quotation'] = (object)$thisArray;

			/* language */
			$data['languages'] = (object)array(
				(object)array('language_name' => 'en'),
				(object)array('language_name' => 'tc'),
				(object)array('language_name' => 'sc')
			);

			/* currency */
			$data['currencys'] = (object)array(
				(object)array('currency_name' => 'rmb'),
				(object)array('currency_name' => 'hkd'),
				(object)array('currency_name' => 'usd')
			);

			/* client */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['clients'] = $this->client_model->select($thisSelect);

			/* product */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['products'] = $this->product_model->select($thisSelect);

			/* user */
			$thisSelect = array(
				'where' => array(
					'user_id' => $this->session->userdata('user_id')
				),
				'return' => 'row'
			);
			$data['user'] = $this->user_model->select($thisSelect);

			/* preset quotationitem empty data */
			$thisArray = array();
			foreach($this->quotationitem_model->structure() as $key => $value){
				$thisArray[$value->Field] = '';
			}
			$data['quotationitems'][0] = (object)$thisArray;

			$this->load->view('purchaseorder_view', $data);
		}
	}

	public function select()
	{
		$per_page = 1;

		$thisGET = $this->uri->uri_to_assoc();
		$thisGET['quotation_deleted'] = 'N';

		$thisSelect = array(
			'select' => array(
				'*',
				'max(quotation_id) as max_quotation_id',
				'max(quotation_version) as max_quotation_version'
			),
			'where' => $thisGET,
			'group' => 'quotation_number',
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['quotations'] = $this->quotation_model->select($thisSelect);

		$thisSelect = array(
			'select' => array(
				'*',
				'max(quotation_id) as max_quotation_id',
				'max(quotation_version) as max_quotation_version'
			),
			'where' => $thisGET,
			'group' => 'quotation_number',
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->quotation_model->select($thisSelect);

		/* pagination */
		$this->pagination->initialize(get_pagination_config($per_page, $data['num_rows']));

		$this->load->view('purchaseorder_view', $data);
	}

	public function setting()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$thisPOST['quotation_version'] = get_quotation_version($thisPOST['quotation_number']) + 1;
			$thisInsertId = $this->quotation_model->insert($thisPOST);

			$thisPurchaseorderitem = get_array_prefix('quotationitem_', $thisPOST);
			$thisPurchaseorderitem = convert_formArray_to_DBArray($thisPurchaseorderitem, 'quotationitem_product_name'); //form array to DB array
			foreach($thisPurchaseorderitem as $key => $value){
				$value['quotationitem_quotation_id'] = $thisInsertId;
				$this->quotationitem_model->insert($value);
			}

			/* attachment */
			$attachment_path = $_SERVER['DOCUMENT_ROOT'].'/crm/assets/images/attachment/quotation/';
			if($_FILES['attachment']['error'] == UPLOAD_ERR_OK){
				move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment_path.$thisPOST['quotation_number']);
			}

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['quotation_id'];
			set_log($thisLog);

			redirect($thisPOST['referrer']);
		}else{
			/* quotation */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'row'
			);
			$data['quotation'] = $this->quotation_model->select($thisSelect);

			/* language */
			$data['languages'] = (object)array(
				(object)array('language_name' => 'en'),
				(object)array('language_name' => 'tc'),
				(object)array('language_name' => 'sc')
			);

			/* currency */
			$data['currencys'] = (object)array(
				(object)array('currency_name' => 'rmb'),
				(object)array('currency_name' => 'hkd'),
				(object)array('currency_name' => 'usd')
			);

			/* client */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['clients'] = $this->client_model->select($thisSelect);

			/* product */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['products'] = $this->product_model->select($thisSelect);

			/* user */
			$thisSelect = array(
				'where' => array(
					'user_id' => $this->session->userdata('user_id')
				),
				'return' => 'row'
			);
			$data['user'] = $this->user_model->select($thisSelect);

			/* quotation */
			$thisSelect = array(
				'where' => array(
					'quotationitem_quotation_id' => $data['quotation']->quotation_id
				),
				'return' => 'result'
			);
			$data['quotationitems'] = $this->quotationitem_model->select($thisSelect);

			$this->load->view('purchaseorder_view', $data);
		}
	}

}
