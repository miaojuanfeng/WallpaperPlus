<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		check_session_timeout();
		check_is_login();
		convert_get_slashes_pretty_link();
		check_permission();

		$this->load->model('invoice_model');
		$this->load->model('client_model');
		$this->load->model('product_model');
		$this->load->model('user_model');
		$this->load->model('invoiceitem_model');
		$this->load->model('salesorder_model');
		$this->load->model('salesorderitem_model');
		$this->load->model('terms_model');
		$this->load->model('z_role_user_model');
	}

	public function index()
	{
		redirect('invoice/select');
	}

	public function update()
	{
		// update here.
	}

	public function delete()
	{
		$thisPOST = $this->input->post();
		$this->invoice_model->delete($thisPOST);

		$thisLog['log_permission_class'] = $this->router->fetch_class();
		$thisLog['log_permission_action'] = $this->router->fetch_method();
		$thisLog['log_record_id'] = $thisPOST['invoice_id'];
		set_log($thisLog);

		redirect($this->agent->referrer());
	}

	public function insert()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();

			/* salesorder */
			$thisPOST['salesorder_id'] = $thisPOST['invoice_salesorder_id'];
			$thisPOST['salesorder_status'] = 'confirm';
			$thisPOST['salesorder_confirmed'] = 'Y';
			$this->salesorder_model->update($thisPOST);

			/* invoice */
			$thisPOST['invoice_serial'] = sprintf("%03s", (get_invoice_serial() + 1));
			$thisPOST['invoice_number'] = 'SO'.date('ym').$thisPOST['invoice_serial'];
			$thisPOST['invoice_version'] = 1;
			$thisPOST['invoice_status'] = 'processing';
			$thisPOST['invoice_id'] = $thisInsertId = $this->invoice_model->insert($thisPOST);

			$thisQuotationitem = get_array_prefix('invoiceitem_', $thisPOST);
			$thisQuotationitem = convert_formArray_to_DBArray($thisQuotationitem, 'invoiceitem_product_name');
			foreach($thisQuotationitem as $key => $value){
				$value['invoiceitem_invoice_id'] = $thisInsertId;
				$this->invoiceitem_model->insert($value);
			}

			/* attachment */
			$attachment_path = $_SERVER['DOCUMENT_ROOT'].'/assets/images/attachment/invoice/';
			if($_FILES['attachment']['error'] == UPLOAD_ERR_OK){
				move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment_path.$thisPOST['invoice_number']);
			}

			/* print as PDF */
			$wkhtmltopdf  = $this->config->item("wkhtmltox_path");
			$wkhtmltopdf .= ' --no-outline --header-html "'.base_url('print/invoice/header/invoice_id/'.$thisInsertId).'"';
			$wkhtmltopdf .= ' --margin-top 68 --header-spacing 0 "'.base_url('print/invoice/content/invoice_id/'.$thisInsertId).'"';
			$wkhtmltopdf .= ' assets/images/pdf/salesorder/'.$thisPOST['invoice_number'].'.pdf';
			$output = exec($wkhtmltopdf);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['invoice_id'];
			set_log($thisLog);
			
			$thisAlert = 'Data saved';
			$this->session->set_tempdata('alert', '<div class="btn btn-sm btn-primary btn-block bottom-buffer-10">'.$thisAlert.'</div>', 0);
			redirect('invoice/update/invoice_id/'.$thisPOST['invoice_id']);
		}else{
			/* salesorder */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'row'
			);
			$data['salesorder'] = $this->salesorder_model->select($thisSelect);
			$data['invoice'] = convert_salesorder_to_invoice($data['salesorder']);

			/* preset data */
			$data['invoice']->invoice_salesorder_id = $data['salesorder']->salesorder_id;

			/* currency */
			$data['currencys'] = (object)array(
				(object)array('currency_name' => 'rmb'),
				(object)array('currency_name' => 'hkd'),
				(object)array('currency_name' => 'usd')
			);

			/* commission */
			$data['commissions'] = (object)array(
				(object)array('commission_name' => '8'),
				(object)array('commission_name' => '15'),
				(object)array('commission_name' => '20'),
				(object)array('commission_name' => '40')
			);

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

			/* get sales manager & sales user */
			$thisSelect = array(
				'where' => array(
					'role_id_in' => array(
						3, // sales manager group
						4 // sales group
					)
				),
				'return' => 'result'
			);
			$data['z_role_user_user_ids'] = convert_object_to_array($this->z_role_user_model->select($thisSelect), 'z_role_user_user_id');

			$thisSelect = array(
				'where' => array(
					'user_id_in' => $data['z_role_user_user_ids']
				),
				'return' => 'result'
			);
			$data['users'] = $this->user_model->select($thisSelect);
			/* get sales manager & sales user */

			/* invoiceitem */
			$thisSelect = array(
				'where' => array(
					'salesorderitem_salesorder_id' => $data['salesorder']->salesorder_id
				),
				'return' => 'result'
			);
			$data['salesorderitems'] = $this->salesorderitem_model->select($thisSelect);
			$data['invoiceitems'] = convert_salesorderitems_to_invoiceitems($data['salesorderitems']);

			$this->load->view('invoice_view', $data);
		}
	}

	public function select()
	{
		$per_page = get_setting('per_page')->setting_value;

		$thisGET = $this->uri->uri_to_assoc();
		if(!isset($thisGET['invoice_status'])){
			$thisGET['invoice_default'] = true;
		}
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

				$thisGET['invoice_user_id_in'] = $data['user_ids'];
				break;
			case in_array('4', $this->session->userdata('role')): // sales
				/* get own client */
				$thisGET['invoice_user_id'] = $this->session->userdata('user_id');
				break;
			default:
				break;
		}

		/* check salesorder */
		if(isset($thisGET['salesorder_number_greateq']) || isset($thisGET['salesorder_number_smalleq'])){
			$thisSelect = array(
				'where' => $thisGET,
				'return' => 'result'
			);
			$data['salesorders'] = $this->salesorder_model->select($thisSelect);

			if($data['salesorders']){
				foreach($data['salesorders'] as $key => $value){
					$thisGET['invoice_salesorder_id_in'] = $value->salesorder_id;
				}
			}else{
				$thisGET['invoice_salesorder_id_in'] = array(0);
			}
		}
		/* check salesorderitem */

		/* check salesorderitem */
		if(isset($thisGET['salesorderitem_product_code_like']) || isset($thisGET['salesorderitem_product_name_like']) || isset($thisGET['salesorderitem_product_detail_like'])){
			$thisSelect = array(
				'where' => $thisGET,
				'return' => 'result'
			);
			$data['salesorderitems'] = $this->salesorderitem_model->select($thisSelect);

			if($data['salesorderitems']){
				foreach($data['salesorderitems'] as $key => $value){
					$thisGET['invoice_salesorder_id_in'] = $value->salesorderitem_salesorder_id;
				}
			}else{
				$thisGET['invoice_salesorder_id_in'] = array(0);
			}
		}
		/* check salesorderitem */

		$thisSelect = array(
			'where' => $thisGET,
			'group' => 'invoice_number',
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

		$this->load->view('invoice_view', $data);
	}

}
