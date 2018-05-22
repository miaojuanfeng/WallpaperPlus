<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Deliverynote extends CI_Controller {

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

		$this->load->model('deliverynote_model');
		$this->load->model('deliverynoteitem_model');
		$this->load->model('salesorder_model');
		$this->load->model('client_model');
		$this->load->model('product_model');
		$this->load->model('user_model');
		$this->load->model('salesorderitem_model');
		$this->load->model('terms_model');
		$this->load->model('z_role_user_model');
	}

	public function index()
	{
		redirect('deliverynote/select');
	}

	public function update()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$this->deliverynote_model->update($thisPOST);

			$thisDeliverynoteitem = get_array_prefix('deliverynoteitem_', $thisPOST);
			$thisDeliverynoteitem = convert_formArray_to_DBArray($thisDeliverynoteitem, 'deliverynoteitem_product_name');
			$this->deliverynoteitem_model->delete($thisPOST);
			foreach($thisDeliverynoteitem as $key => $value){
				$value['deliverynoteitem_deliverynote_id'] = $thisPOST['deliverynote_id'];
				$this->deliverynoteitem_model->insert($value);
			}

			/* print as PDF */
			$wkhtmltopdf  = $this->config->item("wkhtmltox_path");
			$wkhtmltopdf .= ' --no-outline --header-html "'.base_url('print/deliverynote/header/deliverynote_id/'.$thisPOST['deliverynote_id']).'"';
			$wkhtmltopdf .= ' --margin-top 68 --header-spacing 0 "'.base_url('print/deliverynote/content/deliverynote_id/'.$thisPOST['deliverynote_id']).'"';
			$wkhtmltopdf .= ' assets/images/pdf/deliverynote/'.$thisPOST['deliverynote_number'].'.pdf';
			$output = exec($wkhtmltopdf);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['deliverynote_id'];
			set_log($thisLog);
			
			$thisAlert = 'Data saved';
			$this->session->set_tempdata('alert', '<div class="btn btn-sm btn-primary btn-block bottom-buffer-10">'.$thisAlert.'</div>', 0);
			redirect('deliverynote/update/deliverynote_id/'.$thisPOST['deliverynote_id']);
		}else{
			/* deliverynote */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'row'
			);
			$data['deliverynote'] = $this->deliverynote_model->select($thisSelect);

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

			/* commission */
			$data['commissions'] = (object)array(
				(object)array('commission_name' => '8'),
				(object)array('commission_name' => '15'),
				(object)array('commission_name' => '20'),
				(object)array('commission_name' => '40')
			);

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

			/* deliverynoteitem */
			$thisSelect = array(
				'where' => array(
					'deliverynoteitem_deliverynote_id' => $data['deliverynote']->deliverynote_id
				),
				'return' => 'result'
			);
			$data['deliverynoteitems'] = $this->deliverynoteitem_model->select($thisSelect);

			$this->load->view('deliverynote_view', $data);
		}
	}

	public function delete()
	{
		$thisPOST = $this->input->post();
		$this->deliverynote_model->delete($thisPOST);

		$thisLog['log_permission_class'] = $this->router->fetch_class();
		$thisLog['log_permission_action'] = $this->router->fetch_method();
		$thisLog['log_record_id'] = $thisPOST['deliverynote_id'];
		set_log($thisLog);

		redirect($this->agent->referrer());
	}

	public function insert()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();

			/* salesorder */
			$thisPOST['salesorder_id'] = $thisPOST['deliverynote_salesorder_id'];
			// $thisPOST['salesorder_status'] = 'processing';
			$thisPOST['salesorder_confirmed'] = 'Y';
			$this->salesorder_model->update($thisPOST);

			/* deliverynote */
			$thisPOST['deliverynote_serial'] = sprintf("%03s", (get_deliverynote_serial() + 1));
			$thisPOST['deliverynote_number'] = 'DN'.date('ym').$thisPOST['deliverynote_serial'];
			$thisPOST['deliverynote_version'] = 1;
			$thisPOST['deliverynote_status'] = 'processing';
			$thisPOST['deliverynote_id'] = $thisInsertId = $this->deliverynote_model->insert($thisPOST);

			$thisQuotationitem = get_array_prefix('deliverynoteitem_', $thisPOST);
			$thisQuotationitem = convert_formArray_to_DBArray($thisQuotationitem, 'deliverynoteitem_product_name');
			foreach($thisQuotationitem as $key => $value){
				$value['deliverynoteitem_deliverynote_id'] = $thisInsertId;
				$this->deliverynoteitem_model->insert($value);
			}

			/* print as PDF */
			$wkhtmltopdf  = $this->config->item("wkhtmltox_path");
			$wkhtmltopdf .= ' --no-outline --header-html "'.base_url('print/deliverynote/header/deliverynote_id/'.$thisInsertId).'"';
			$wkhtmltopdf .= ' --margin-top 68 --header-spacing 0 "'.base_url('print/deliverynote/content/deliverynote_id/'.$thisInsertId).'"';
			$wkhtmltopdf .= ' assets/images/pdf/salesorder/'.$thisPOST['deliverynote_number'].'.pdf';
			$output = exec($wkhtmltopdf);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['deliverynote_id'];
			set_log($thisLog);
			
			$thisAlert = 'Data saved';
			$this->session->set_tempdata('alert', '<div class="btn btn-sm btn-primary btn-block bottom-buffer-10">'.$thisAlert.'</div>', 0);
			redirect('deliverynote');
		}else{
			/* salesorder */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'row'
			);
			$data['salesorder'] = $this->salesorder_model->select($thisSelect);
			$data['deliverynote'] = convert_salesorder_to_deliverynote($data['salesorder']);

			/* deliverynote paid */
			$thisSelect = array(
				'select' => array(
					'sum(deliverynote_pay) as sum_of_deliverynote_pay'
				),
				'where' => array(
					'deliverynote_salesorder_id' => $data['salesorder']->salesorder_id
				),
				'return' => 'row'
			);
			$data['deliverynote']->deliverynote_paid = $this->deliverynote_model->select($thisSelect)->sum_of_deliverynote_pay;

			/* preset data */
			$data['deliverynote']->deliverynote_salesorder_id = $data['salesorder']->salesorder_id;
			// $data['deliverynote']->deliverynote_paid = '0';
			$data['deliverynote']->deliverynote_pay = '0';
			$data['deliverynote']->deliverynote_balance = '0';
            $data['deliverynote']->deliverynote_lot_number = '';
            $data['deliverynote']->deliverynote_waybill_number = '';
            $data['deliverynote']->deliverynote_customs_number = '';
            $data['deliverynote']->deliverynote_express_company = '';
            $data['deliverynote']->deliverynote_delivery_day = '';

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

			/* commission */
			$data['commissions'] = (object)array(
				(object)array('commission_name' => '8'),
				(object)array('commission_name' => '15'),
				(object)array('commission_name' => '20'),
				(object)array('commission_name' => '40')
			);

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

			/* deliverynoteitem */
			$thisSelect = array(
				'where' => array(
					'salesorderitem_salesorder_id' => $data['salesorder']->salesorder_id
				),
				'return' => 'result'
			);
			$data['salesorderitems'] = $this->salesorderitem_model->select($thisSelect);
			$data['deliverynoteitems'] = convert_salesorderitems_to_deliverynoteitems($data['salesorderitems']);

			$this->load->view('deliverynote_view', $data);
		}
	}

	public function select()
	{
		$per_page = get_setting('per_page')->setting_value;

		$thisGET = $this->uri->uri_to_assoc();
		if(!isset($thisGET['deliverynote_status'])){
			$thisGET['deliverynote_default'] = true;
		}
		$thisGET['deliverynote_deleted'] = 'N';

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

				$thisGET['deliverynote_user_id_in'] = $data['user_ids'];
				break;
			case in_array('4', $this->session->userdata('role')): // sales
				/* get own client */
				$thisGET['deliverynote_user_id'] = $this->session->userdata('user_id');
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
					$thisGET['deliverynote_salesorder_id_in'][] = $value->salesorder_id;
				}
			}else{
				$thisGET['deliverynote_salesorder_id_in'] = array(0);
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
					$thisGET['deliverynote_salesorder_id_in'][] = $value->salesorderitem_salesorder_id;
				}
			}else{
				$thisGET['deliverynote_salesorder_id_in'] = array(0);
			}
		}
		/* check salesorderitem */

		$thisSelect = array(
			// 'select' => array(
			// 	'*',
			// 	'max(deliverynote_id) as max_salesorder_id',
			// 	'max(deliverynote_version) as max_salesorder_version'
			// ),
			'where' => $thisGET,
			'group' => 'deliverynote_number',
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['deliverynotes'] = $this->deliverynote_model->select($thisSelect);

		$thisSelect = array(
			// 'select' => array(
			// 	'*',
			// 	'max(deliverynote_id) as max_deliverynote_id',
			// 	'max(deliverynote_version) as max_deliverynote_version'
			// ),
			'where' => $thisGET,
			'group' => 'deliverynote_number',
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->deliverynote_model->select($thisSelect);

		/* status */
		$data['statuss'] = (object)array(
			(object)array('status_name' => 'processing'),
			(object)array('status_name' => 'partial'),
			(object)array('status_name' => 'complete'),
			(object)array('status_name' => 'cancel')
		);

        /* popup-list */
//        $thisSelect = array(
//            'where' => array('deliverynote_status' => 'complete'),
//            'group' => 'deliverynote_number',
//            'order' => 'deliverynote_confirmed_date',
//            'ascend' => 'desc',
//            'return' => 'result'
//        );
//        $data['popup_list'] = $this->deliverynote_model->select($thisSelect);

		/* user */
		$thisSelect = array(
			'return' => 'result'
		);
		$data['users'] = $this->user_model->select($thisSelect);

		/* pagination */
		$this->pagination->initialize(get_pagination_config($per_page, $data['num_rows']));

		$this->load->view('deliverynote_view', $data);
	}

	public function setting()
	{
		$this->load->view('deliverynote_view');
	}

}
