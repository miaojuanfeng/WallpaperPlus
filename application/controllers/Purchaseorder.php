<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchaseorder extends CI_Controller {

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
		$this->load->model('purchaseorderitem_model');
		$this->load->model('salesorder_model');
		$this->load->model('vendor_model');
		$this->load->model('product_model');
		$this->load->model('user_model');
		$this->load->model('salesorderitem_model');
		$this->load->model('terms_model');
		$this->load->model('z_role_user_model');
	}

	public function index()
	{
		redirect('purchaseorder/select');
	}

	public function update()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$this->purchaseorder_model->update($thisPOST);

			$thisSalesorderitem = get_array_prefix('purchaseorderitem_', $thisPOST);
			$thisSalesorderitem = convert_formArray_to_DBArray($thisSalesorderitem, 'purchaseorderitem_product_name');
			$this->purchaseorderitem_model->delete($thisPOST);
			foreach($thisSalesorderitem as $key => $value){
				$value['purchaseorderitem_purchaseorder_id'] = $thisPOST['purchaseorder_id'];
				$this->purchaseorderitem_model->insert($value);
			}

			/* print as PDF */
			$wkhtmltopdf  = $this->config->item("wkhtmltox_path");
			$wkhtmltopdf .= ' --no-outline --header-html "'.base_url('print/purchaseorder/header/purchaseorder_id/'.$thisPOST['purchaseorder_id']).'"';
			$wkhtmltopdf .= ' --margin-top 68 --header-spacing 0 "'.base_url('print/purchaseorder/content/purchaseorder_id/'.$thisPOST['purchaseorder_id']).'"';
			$wkhtmltopdf .= ' assets/images/pdf/purchaseorder/'.$thisPOST['purchaseorder_number'].'.pdf';
			$output = exec($wkhtmltopdf);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['purchaseorder_id'];
			set_log($thisLog);

			$thisAlert = 'Data saved';
			$this->session->set_tempdata('alert', '<div class="btn btn-sm btn-primary btn-block bottom-buffer-10">'.$thisAlert.'</div>', 0);
			redirect('purchaseorder/update/purchaseorder_id/'.$thisPOST['purchaseorder_id']);
		}else{
			$thisPOST = $this->uri->uri_to_assoc();

			/* purchaseorder */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'row'
			);
			$data['purchaseorder'] = $this->purchaseorder_model->select($thisSelect);

			/* currency */
			$data['currencys'] = (object)array(
				(object)array('currency_name' => 'rmb'),
				(object)array('currency_name' => 'hkd'),
				(object)array('currency_name' => 'usd')
			);

			/* status */
			$data['statuss'] = (object)array(
				(object)array('status_name' => 'processing'),
				(object)array('status_name' => 'partial'),
				(object)array('status_name' => 'complete'),
				(object)array('status_name' => 'cancel')
			);

			/* vendor */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['vendors'] = $this->vendor_model->select($thisSelect);

			/* salesorder */
			$thisSelect = array(
				'where' => array(
					'salesorder_status' => 'processing'
				),
				'return' => 'result'
			);
			$data['salesorders'] = $this->salesorder_model->select($thisSelect);

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

			/* purchaseorderitem */
			$thisSelect = array(
				'where' => array(
					'purchaseorderitem_purchaseorder_id' => $data['purchaseorder']->purchaseorder_id,
				),
				'return' => 'result'
			);
			$data['purchaseorderitems'] = $this->purchaseorderitem_model->select($thisSelect);

			$this->load->view('purchaseorder_view', $data);
		}
	}

	public function delete()
	{
		$thisPOST = $this->input->post();
		$this->purchaseorder_model->delete($thisPOST);

		$thisLog['log_permission_class'] = $this->router->fetch_class();
		$thisLog['log_permission_action'] = $this->router->fetch_method();
		$thisLog['log_record_id'] = $thisPOST['purchaseorder_id'];
		set_log($thisLog);

		redirect($this->agent->referrer());
	}

    /**
     *
     */
    public function insert()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();

			/* salesorder */
			$thisPOST['salesorder_id'] = $thisPOST['purchaseorder_salesorder_id'];
			// $thisPOST['salesorder_status'] = 'processing';
			$thisPOST['salesorder_confirmed'] = 'Y';
			$this->salesorder_model->update($thisPOST);

			/* purchaseorder */
			$thisPOST['purchaseorder_serial'] = sprintf("%03s", (get_purchaseorder_serial() + 1));
			$thisPOST['purchaseorder_number'] = $this->session->userdata('user_order_prefix').'PO'.date('ym').$thisPOST['purchaseorder_serial'];
			$thisPOST['purchaseorder_version'] = 1;
			$thisPOST['purchaseorder_status'] = 'processing';
			$thisPOST['purchaseorder_id'] = $thisInsertId = $this->purchaseorder_model->insert($thisPOST);

			$thisQuotationitem = get_array_prefix('purchaseorderitem_', $thisPOST);
			$thisQuotationitem = convert_formArray_to_DBArray($thisQuotationitem, 'purchaseorderitem_product_name');
			foreach($thisQuotationitem as $key => $value){
				$value['purchaseorderitem_purchaseorder_id'] = $thisInsertId;
				$this->purchaseorderitem_model->insert($value);
			}

			/* print as PDF */
			$wkhtmltopdf  = $this->config->item("wkhtmltox_path");
			$wkhtmltopdf .= ' --no-outline --header-html "'.base_url('print/purchaseorder/header/purchaseorder_id/'.$thisInsertId).'"';
			$wkhtmltopdf .= ' --margin-top 68 --header-spacing 0 "'.base_url('print/purchaseorder/content/purchaseorder_id/'.$thisInsertId).'"';
			$wkhtmltopdf .= ' assets/images/pdf/salesorder/'.$thisPOST['purchaseorder_number'].'.pdf';
			$output = exec($wkhtmltopdf);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['purchaseorder_id'];
			set_log($thisLog);
			
			$thisAlert = 'Data saved';
			$this->session->set_tempdata('alert', '<div class="btn btn-sm btn-primary btn-block bottom-buffer-10">'.$thisAlert.'</div>', 0);
			redirect('purchaseorder');
		}else{
			if(empty($this->uri->uri_to_assoc())){
				/* preset empty data */
				$thisArray = array();
				foreach($this->purchaseorder_model->structure() as $key => $value){
					$thisArray[$value->Field] = '';
				}
				$data['purchaseorder'] = (object)$thisArray;
			}else{
				/* salesorder */
				$thisSelect = array(
					'where' => $this->uri->uri_to_assoc(),
					'return' => 'row'
				);
				$data['salesorder'] = $this->salesorder_model->select($thisSelect);
				$data['purchaseorder'] = convert_salesorder_to_purchaseorder($data['salesorder']);

				/* preset data */
				$data['purchaseorder']->purchaseorder_id = '0';
				$data['purchaseorder']->purchaseorder_salesorder_id = $data['salesorder']->salesorder_id;
				// $data['purchaseorder']->purchaseorder_paid = '0';
				$data['purchaseorder']->purchaseorder_pay = '0';
				$data['purchaseorder']->purchaseorder_balance = '0';

				$data['purchaseorder']->purchaseorder_vendor_id = '';
				$data['purchaseorder']->purchaseorder_number = '';
                $data['purchaseorder']->purchaseorder_vendor_company_code = '';
				$data['purchaseorder']->purchaseorder_vendor_company_name = '';
				$data['purchaseorder']->purchaseorder_vendor_company_address = '';
				$data['purchaseorder']->purchaseorder_vendor_company_phone = '';
				$data['purchaseorder']->purchaseorder_vendor_phone = '';
				$data['purchaseorder']->purchaseorder_vendor_email = '';
				$data['purchaseorder']->purchaseorder_vendor_name = '';
				$data['purchaseorder']->purchaseorder_vendor_delivery_address = '';
                $data['purchaseorder']->purchaseorder_vendor_exchange_rate = '';
                $data['purchaseorder']->purchaseorder_vendor_currency = '';
                $data['purchaseorder']->purchaseorder_reminder_date = '';
			}

			/* currency */
			$data['currencys'] = (object)array(
				(object)array('currency_name' => 'rmb'),
				(object)array('currency_name' => 'hkd'),
				(object)array('currency_name' => 'usd')
			);

			/* status */
			$data['statuss'] = (object)array(
				(object)array('status_name' => 'processing'),
				(object)array('status_name' => 'partial'),
				(object)array('status_name' => 'complete'),
				(object)array('status_name' => 'cancel')
			);

			/* vendor */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['vendors'] = $this->vendor_model->select($thisSelect);

			/* salesorder */
			$thisSelect = array(
				'where' => array(
					'salesorder_status' => 'processing'
				),
				'return' => 'result'
			);
			$data['salesorders'] = $this->salesorder_model->select($thisSelect);

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

			if(empty($this->uri->uri_to_assoc())){
				/* preset purchaseorderitem empty data */
				$thisArray = array();
				foreach($this->purchaseorderitem_model->structure() as $key => $value){
					$thisArray[$value->Field] = '';
				}
				$data['purchaseorderitems'][0] = (object)$thisArray;
			}else{
				/* purchaseorderitem */
				$thisSelect = array(
					'where' => array(
						'salesorderitem_salesorder_id' => $data['salesorder']->salesorder_id
					),
					'return' => 'result'
				);
				$data['salesorderitems'] = $this->salesorderitem_model->select($thisSelect);
				$data['purchaseorderitems'] = convert_salesorderitems_to_purchaseorderitems($data['salesorderitems']);
				foreach($data['purchaseorderitems'] as $key => $value){
					$data['purchaseorderitems'][$key]->purchaseorderitem_type = 'main item';
					
					/* get item cost */
					$thisSelect = array(
						'where' => array(
							'product_id' => $value->purchaseorderitem_product_id
						),
						'return' => 'row'
					);
					$data['purchaseorderitems'][$key]->purchaseorderitem_product_price = $this->product_model->select($thisSelect)->product_cost;
                    $data['purchaseorderitems'][$key]->purchaseorderitem_discount = 0;
				}
			}

			$this->load->view('purchaseorder_view', $data);
		}
	}

	public function select()
	{
		$per_page = get_setting('per_page')->setting_value;

		$thisGET = $this->uri->uri_to_assoc();
		if(!isset($thisGET['purchaseorder_status'])){
			$thisGET['purchaseorder_default'] = true;
		}
		$thisGET['purchaseorder_deleted'] = 'N';

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

				$thisGET['purchaseorder_user_id_in'] = $data['user_ids'];
				break;
			case in_array('4', $this->session->userdata('role')): // sales
				/* get own client */
				$thisGET['purchaseorder_user_id'] = $this->session->userdata('user_id');
				break;
			default:
				break;
		}

		/* check salesorder */
		if(isset($thisGET['salesorder_number_like'])){
			$thisSelect = array(
				'where' => $thisGET,
				'return' => 'result'
			);
			$data['salesorders'] = $this->salesorder_model->select($thisSelect);

			if($data['salesorders']){
				foreach($data['salesorders'] as $key => $value){
					$thisGET['purchaseorder_salesorder_id_in'] = $value->salesorder_id;
				}
			}else{
				$thisGET['purchaseorder_salesorder_id_in'] = array(0);
			}
		}
		/* check salesorder */

		/* check salesorderitem */
		if(isset($thisGET['salesorderitem_product_code_like']) || isset($thisGET['salesorderitem_product_name_like']) || isset($thisGET['salesorderitem_product_detail_like'])){
			$thisSelect = array(
				'where' => $thisGET,
				'return' => 'result'
			);
			$data['salesorderitems'] = $this->salesorderitem_model->select($thisSelect);

			if($data['salesorderitems']){
				foreach($data['salesorderitems'] as $key => $value){
					$thisGET['purchaseorder_salesorder_id_in'] = $value->salesorderitem_salesorder_id;
				}
			}else{
				$thisGET['purchaseorder_salesorder_id_in'] = array(0);
			}
		}
		/* check salesorderitem */

		$thisSelect = array(
			// 'select' => array(
			// 	'*',
			// 	'max(purchaseorder_id) as max_salesorder_id',
			// 	'max(purchaseorder_version) as max_salesorder_version'
			// ),
			'where' => $thisGET,
			'group' => 'purchaseorder_number',
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['purchaseorders'] = $this->purchaseorder_model->select($thisSelect);

		$thisSelect = array(
			// 'select' => array(
			// 	'*',
			// 	'max(purchaseorder_id) as max_purchaseorder_id',
			// 	'max(purchaseorder_version) as max_purchaseorder_version'
			// ),
			'where' => $thisGET,
			'group' => 'purchaseorder_number',
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->purchaseorder_model->select($thisSelect);

		/* status */
		$data['statuss'] = (object)array(
			(object)array('status_name' => 'processing'),
			(object)array('status_name' => 'partial'),
			(object)array('status_name' => 'complete'),
			(object)array('status_name' => 'cancel')
		);

        /* popup-list */
//        $thisSelect = array(
//            'where' => array('purchaseorder_arrive_status' => 'complete'),
//            'group' => 'purchaseorder_number',
//            'order' => 'purchaseorder_confirmed_date',
//            'ascend' => 'desc',
//            'return' => 'result'
//        );
//        $popup_list = $this->purchaseorder_model->select($thisSelect);
//        $data['popup_list'] = array();
//        foreach ($popup_list as $key => $value){
//            if( get_salesorder($value->purchaseorder_salesorder_id)->salesorder_status == 'processing' ){
//                $data['popup_list'][] = $value;
//            }
//        }

		/* user */
		$thisSelect = array(
			'return' => 'result'
		);
		$data['users'] = $this->user_model->select($thisSelect);

		/* pagination */
		$this->pagination->initialize(get_pagination_config($per_page, $data['num_rows']));

		$this->load->view('purchaseorder_view', $data);
	}

	public function setting()
	{
		$this->load->view('purchaseorder_view');
	}

}
