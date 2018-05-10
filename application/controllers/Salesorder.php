<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salesorder extends CI_Controller {

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
		$this->load->model('salesorderitem_model');
		$this->load->model('quotation_model');
		$this->load->model('client_model');
		$this->load->model('purchaseorder_model');
        $this->load->model('invoice_model');
		$this->load->model('product_model');
		$this->load->model('user_model');
		$this->load->model('quotationitem_model');
		$this->load->model('terms_model');
		$this->load->model('z_role_user_model');
	}

	public function index()
	{
		redirect('salesorder/select');
	}

	public function update()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$this->salesorder_model->update($thisPOST);

			$thisSalesorderitem = get_array_prefix('salesorderitem_', $thisPOST);
			$thisSalesorderitem = convert_formArray_to_DBArray($thisSalesorderitem, 'salesorderitem_product_name');
			$this->salesorderitem_model->delete($thisPOST);
			foreach($thisSalesorderitem as $key => $value){
				$value['salesorderitem_salesorder_id'] = $thisPOST['salesorder_id'];
				$this->salesorderitem_model->insert($value);
			}

			/* attachment */
			$attachment_path = $_SERVER['DOCUMENT_ROOT'].'/assets/images/attachment/salesorder/';
			if($_FILES['attachment']['error'] == UPLOAD_ERR_OK){
				move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment_path.$thisPOST['salesorder_id']);
			}

			/* print as PDF */
			$wkhtmltopdf  = $this->config->item("wkhtmltox_path");
			$wkhtmltopdf .= ' --no-outline --header-html "'.base_url('print/salesorder/header/salesorder_id/'.$thisPOST['salesorder_id']).'"';
			$wkhtmltopdf .= ' --margin-top 68 --header-spacing 0 "'.base_url('print/salesorder/content/salesorder_id/'.$thisPOST['salesorder_id']).'"';
			$wkhtmltopdf .= ' assets/images/pdf/salesorder/'.$thisPOST['salesorder_number'].'.pdf';
			$output = exec($wkhtmltopdf);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['salesorder_id'];
			set_log($thisLog);

			$thisAlert = 'Data saved';
			$this->session->set_tempdata('alert', '<div class="btn btn-sm btn-primary btn-block bottom-buffer-10">'.$thisAlert.'</div>', 0);
			redirect('salesorder/update/salesorder_id/'.$thisPOST['salesorder_id']);
		}else{
			/* salesorder */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'row'
			);
			$data['salesorder'] = $this->salesorder_model->select($thisSelect);

			/* quotation */
			$thisSelect = array(
				'where' => array(
					'quotation_id' => $data['salesorder']->salesorder_quotation_id
				),
				'return' => 'row'
			);
			$data['quotation'] = $this->quotation_model->select($thisSelect);

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

			/* salesorder */
			$thisSelect = array(
				'where' => array(
					'salesorderitem_salesorder_id' => $data['salesorder']->salesorder_id
				),
				'return' => 'result'
			);
			$data['salesorderitems'] = $this->salesorderitem_model->select($thisSelect);

			$this->load->view('salesorder_view', $data);
		}
	}

	public function delete()
	{
		$thisPOST = $this->input->post();
		$this->salesorder_model->delete($thisPOST);

		$thisLog['log_permission_class'] = $this->router->fetch_class();
		$thisLog['log_permission_action'] = $this->router->fetch_method();
		$thisLog['log_record_id'] = $thisPOST['salesorder_id'];
		set_log($thisLog);

		redirect($this->agent->referrer());
	}

	public function insert()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();

			/* quotation */
			$thisPOST['quotation_id'] = $thisPOST['salesorder_quotation_id'];
			$thisPOST['quotation_status'] = 'confirm';
			$thisPOST['quotation_confirmed'] = 'Y';
			$this->quotation_model->update($thisPOST);

			/* salesorder */
			$thisPOST['salesorder_serial'] = sprintf("%03s", (get_salesorder_serial() + 1));
			$thisPOST['salesorder_number'] = 'SO'.date('ym').$thisPOST['salesorder_serial'];
			$thisPOST['salesorder_version'] = 1;
			$thisPOST['salesorder_status'] = 'processing';
			$thisPOST['salesorder_id'] = $thisInsertId = $this->salesorder_model->insert($thisPOST);

			$thisQuotationitem = get_array_prefix('salesorderitem_', $thisPOST);
			$thisQuotationitem = convert_formArray_to_DBArray($thisQuotationitem, 'salesorderitem_product_name');
			foreach($thisQuotationitem as $key => $value){
				$value['salesorderitem_salesorder_id'] = $thisInsertId;
				$this->salesorderitem_model->insert($value);
			}

			/* attachment */
			$attachment_path = $_SERVER['DOCUMENT_ROOT'].'/assets/images/attachment/salesorder/';
			if($_FILES['attachment']['error'] == UPLOAD_ERR_OK){
				move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment_path.$thisPOST['salesorder_number']);
			}

			/* print as PDF */
			$wkhtmltopdf  = $this->config->item("wkhtmltox_path");
			$wkhtmltopdf .= ' --no-outline --header-html "'.base_url('print/salesorder/header/salesorder_id/'.$thisInsertId).'"';
			$wkhtmltopdf .= ' --margin-top 68 --header-spacing 0 "'.base_url('print/salesorder/content/salesorder_id/'.$thisInsertId).'"';
			$wkhtmltopdf .= ' assets/images/pdf/salesorder/'.$thisPOST['salesorder_number'].'.pdf';
			$output = exec($wkhtmltopdf);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['salesorder_id'];
			set_log($thisLog);
			
			$thisAlert = 'Data saved';
			$this->session->set_tempdata('alert', '<div class="btn btn-sm btn-primary btn-block bottom-buffer-10">'.$thisAlert.'</div>', 0);
			redirect('salesorder/update/salesorder_id/'.$thisPOST['salesorder_id']);
		}else{
			/* quotation */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'row'
			);
			$data['quotation'] = $this->quotation_model->select($thisSelect);
			$data['salesorder'] = convert_quotation_to_salesorder($data['quotation']);

			/* preset data */
			$data['salesorder']->salesorder_quotation_id = $data['quotation']->quotation_id;
			$data['salesorder']->salesorder_client_delivery_address = '';
			$data['salesorder']->salesorder_internal_remark = '';
			$data['salesorder']->salesorder_quotation_user_id = $data['quotation']->quotation_user_id;

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

			/* salesorderitem */
			$thisSelect = array(
				'where' => array(
					'quotationitem_quotation_id' => $data['quotation']->quotation_id
				),
				'return' => 'result'
			);
			$data['quotationitems'] = $this->quotationitem_model->select($thisSelect);
			$data['salesorderitems'] = convert_quotationitems_to_salesorderitems($data['quotationitems']);

			$this->load->view('salesorder_view', $data);
		}
	}

	public function select()
	{
		$per_page = get_setting('per_page')->setting_value;

		$thisGET = $this->uri->uri_to_assoc();
		if(!isset($thisGET['salesorder_status'])){
			$thisGET['salesorder_default'] = true;
		}
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
            /* invoice */
            $thisSelect = array(
                'where' => array(
                    'invoice_salesorder_id' => $value->salesorder_id,
                    'invoice_status_noteq' => 'cancel'
                ),
                'return' => 'result'
            );
            $data['invoiceorders'] = $this->invoice_model->select($thisSelect);
            $data['salesorders'][$key]->invoiceorders = $data['invoiceorders'];
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

        /* popup-list */
        $thisSelect = array(
            'where' => array('salesorder_status' => 'complete'),
            'group' => 'salesorder_number',
            'order' => 'salesorder_confirmed_date',
            'ascend' => 'desc',
            'return' => 'result'
        );
        $data['popup_list'] = $this->salesorder_model->select($thisSelect);

		/* user */
		$thisSelect = array(
			'return' => 'result'
		);
		$data['users'] = $this->user_model->select($thisSelect);

		/* pagination */
		$this->pagination->initialize(get_pagination_config($per_page, $data['num_rows']));

		$this->load->view('salesorder_view', $data);
	}

	public function setting()
	{
		$this->load->view('salesorder_view');
	}

}
