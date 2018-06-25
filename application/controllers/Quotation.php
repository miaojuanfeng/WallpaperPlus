<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotation extends CI_Controller {

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
		$this->load->model('terms_model');
		$this->load->model('z_client_user_model');
		$this->load->model('currency_model');
		// $this->load->model('salesorder_model');
		// $this->load->model('salesorderitem_model');

		setlocale(LC_MONETARY, 'en_HK');
	}

	public function index()
	{
		redirect('quotation/select');
	}

	public function update()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			//
			$quotation_category_discount = array();
			foreach ($thisPOST['category_name'] as $key => $value) {
				$category = (object) [
							    'category_id' => $thisPOST['category_id'][$key],
							    'category_name' => $thisPOST['category_name'][$key],
							    'category_discount' => $thisPOST['category_discount'][$key]
							  ];
				$quotation_category_discount[] = $category;
			}
			$thisPOST['quotation_category_discount'] = json_encode($quotation_category_discount);
			//
			switch($thisPOST['action']){
				// case 'confirm':
				case 'save':
					$thisPOST['quotation_number'] = $thisPOST['number_prefix'].$thisPOST['quotation_number'];
					$this->quotation_model->update($thisPOST);

					$thisQuotationitem = get_array_prefix('quotationitem_', $thisPOST);
					$thisQuotationitem = convert_formArray_to_DBArray($thisQuotationitem, 'quotationitem_product_name');
					$this->quotationitem_model->delete($thisPOST);
					foreach($thisQuotationitem as $key => $value){
						$value['quotationitem_quotation_id'] = $thisPOST['quotation_id'];
						$this->quotationitem_model->insert($value);
					}
					break;
				case 'reversion':
					$thisPOST['quotation_id'] = '';
					$thisPOST['quotation_version'] = $thisPOST['quotation_version'] + 1;
					
					$thisPOST['quotation_id'] = $thisInsertId = $this->quotation_model->insert($thisPOST);

					$thisQuotationitem = get_array_prefix('quotationitem_', $thisPOST);
					$thisQuotationitem = convert_formArray_to_DBArray($thisQuotationitem, 'quotationitem_product_name');
					foreach($thisQuotationitem as $key => $value){
						$value['quotationitem_id'] = '';
						$value['quotationitem_quotation_id'] = $thisInsertId;
						$this->quotationitem_model->insert($value);
					}
					break;
                case 'approval':
                    /* user */
                    $thisSelect = array(
                        'where' => array('user_code' => $thisPOST['approval_code']),
                        'return' => 'row'
                    );
                    $user = $this->user_model->select($thisSelect);
                    if( $thisPOST['approval_code'] && $user ) {
                        $quotation = array();
                        $thisPOST['quotation_approval_user_id'] = $user->user_id;

                        $this->quotation_model->update($thisPOST);

                        $thisQuotationitem = get_array_prefix('quotationitem_', $thisPOST);
                        $thisQuotationitem = convert_formArray_to_DBArray($thisQuotationitem, 'quotationitem_product_name');
                        $this->quotationitem_model->delete($thisPOST);
                        foreach($thisQuotationitem as $key => $value){
                            $value['quotationitem_quotation_id'] = $thisPOST['quotation_id'];
                            $this->quotationitem_model->insert($value);
                        }

                        $thisAlert = 'Data saved';
                    }else{
                        $thisAlert = 'Save failed, wrong approval code';
                    }
                    break;
			}

			/* attachment */
			$attachment_path = $_SERVER['DOCUMENT_ROOT'].'/assets/images/attachment/quotation/';
			if($_FILES['attachment']['error'] == UPLOAD_ERR_OK){
				move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment_path.$thisPOST['quotation_id']);
			}

			/* print as PDF */
			$wkhtmltopdf  = $this->config->item("wkhtmltox_path");
			$wkhtmltopdf .= ' --no-outline --header-html "'.base_url('print/quotation/header/quotation_id/'.$thisPOST['quotation_id']).'"';
			$wkhtmltopdf .= ' --margin-top 68 --header-spacing 0 "'.base_url('print/quotation/content/quotation_id/'.$thisPOST['quotation_id']).'"';
			$wkhtmltopdf .= ' assets/images/pdf/quotation/'.$thisPOST['quotation_number'].'-v'.$thisPOST['quotation_version'].'.pdf';
			$output = exec($wkhtmltopdf);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['quotation_id'];
			set_log($thisLog);

			switch($thisPOST['action']){
				// case 'confirm':
				// 	foreach($thisPOST as $key => $value){
				// 		$key = str_replace('quotation', 'salesorder', $key);
				// 		$thisPOSTData[$key] = $value;
				// 	}
				// 	$thisPOSTData['salesorder_id'] = '';
				// 	$thisPOSTData['salesorder_id'] = $thisInsertId = $this->salesorder_model->insert($thisPOSTData);

				// 	$thisSalesorderitem = get_array_prefix('salesorderitem_', $thisPOSTData);
				// 	$thisSalesorderitem = convert_formArray_to_DBArray($thisSalesorderitem, 'salesorderitem_product_name');
				// 	foreach($thisSalesorderitem as $key => $value){
				// 		$value['salesorderitem_id'] = '';
				// 		$value['salesorderitem_salesorder_id'] = $thisInsertId;
				// 		$this->salesorderitem_model->insert($value);
				// 	}

				// 	$thisLog['log_permission_class'] = 'salesorder';
				// 	$thisLog['log_permission_action'] = 'insert';
				// 	$thisLog['log_record_id'] = $thisPOSTData['salesorder_id'];
				// 	set_log($thisLog);

				// 	$thisAlert = 'Sales order created';
				// 	$this->session->set_tempdata('alert', '<div class="btn btn-sm btn-primary btn-block bottom-buffer-10">'.$thisAlert.'</div>', 0);
				// 	redirect('salesorder/select');
				// 	break;
				case 'save':
					$thisAlert = 'Data saved';
					break;
				case 'reversion':
					$thisAlert = 'Data saved, version + 1';
					break;
			}
			
			// redirect($thisPOST['referrer']);
			$this->session->set_tempdata('alert', '<div class="btn btn-sm btn-primary btn-block bottom-buffer-10">'.$thisAlert.'</div>', 0);
			redirect('quotation/update/quotation_id/'.$thisPOST['quotation_id']);
		}else{
			$thisPOST = $this->uri->uri_to_assoc();

			/* quotation */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'row'
			);
			$data['quotation'] = $this->quotation_model->select($thisSelect);

			$quotation_category_discount = $data['quotation']->quotation_category_discount;
			if( $quotation_category_discount ){
				$data['quotation_category_discount'] = json_decode($quotation_category_discount);
			}else{
				$data['quotation_category_discount'] = array();
			}

			/* language */
			$data['languages'] = (object)array(
				(object)array('language_name' => 'en'),
				(object)array('language_name' => 'tc'),
				(object)array('language_name' => 'sc')
			);

			/* currency */
			$thisSelect = array(
				'where' => array('currency_name_in' => array('hkd', 'rmb', 'usd')),
				'return' => 'result'
			);
			$data['currencys'] = $this->currency_model->select($thisSelect);
			// var_dump($data['currencys']);
			// $data['currencys'] = (object)array(
   //              (object)array('currency_name' => 'hkd'),
			// 	(object)array('currency_name' => 'rmb'),
			// 	(object)array('currency_name' => 'usd')
			// );

			/* display_number */
			$data['display_numbers'] = (object)array(
				(object)array('display_number_name' => 'index_number'),
				(object)array('display_number_name' => 'part_number')
			);

			/* status */
			$data['statuss'] = (object)array(
				(object)array('status_name' => 'draft'),
				(object)array('status_name' => 'confirm'),
				(object)array('status_name' => 'cancel')
			);

			/* client */
			switch(true){
				case in_array('1', $this->session->userdata('role')): // administrator
				case in_array('2', $this->session->userdata('role')): // boss
				case in_array('5', $this->session->userdata('role')): // operation manager
				case in_array('6', $this->session->userdata('role')): // operation
				case in_array('7', $this->session->userdata('role')): // account
					/* get all client */
					$thisSelect = array(
						'return' => 'result'
					);
					$data['clients'] = $this->client_model->select($thisSelect);
					break;
				case in_array('3', $this->session->userdata('role')): // sales manager
				case in_array('4', $this->session->userdata('role')): // sales
					/* get related client */
					$thisSelect = array(
						'where' => array(
							'user_id' => $this->session->userdata('user_id')
						),
						'return' => 'result'
					);
					$data['z_client_user_client_ids'] = convert_object_to_array($this->z_client_user_model->select($thisSelect), 'z_client_user_client_id');

					$thisSelect = array(
						'where' => array(
							'client_id_in' => $data['z_client_user_client_ids']
						),
						'return' => 'result'
					);
					$data['clients'] = $this->client_model->select($thisSelect);
					break;
			}

			/* user */
			$thisSelect = array(
				'where' => array(
					'user_id' => $this->session->userdata('user_id')
				),
				'return' => 'row'
			);
			$data['user'] = $this->user_model->select($thisSelect);

			if( !empty($data['quotation']->quotation_user_name) ){
				$data['user']->user_name = $data['quotation']->quotation_user_name;
			}

			/* quotation */
			$thisSelect = array(
				'where' => array(
					'quotationitem_quotation_id' => $data['quotation']->quotation_id
				),
				'return' => 'result'
			);
			$data['quotationitems'] = $this->quotationitem_model->select($thisSelect);

			$this->load->view('quotation_view', $data);
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
			$thisPOST['quotation_serial'] = sprintf("%03s", (get_quotation_serial() + 1));
			$thisPOST['quotation_number'] = $thisPOST['number_prefix'].date('ym').$thisPOST['quotation_serial'];
			$thisPOST['quotation_version'] = 0;
			$quotation_category_discount = array();
			foreach ($thisPOST['category_name'] as $key => $value) {
				$category = (object) [
							    'category_id' => $thisPOST['category_id'][$key],
							    'category_name' => $thisPOST['category_name'][$key],
							    'category_discount' => $thisPOST['category_discount'][$key]
							  ];
				$quotation_category_discount[] = $category;
			}
			$thisPOST['quotation_category_discount'] = json_encode($quotation_category_discount);
			$thisPOST['quotation_id'] = $thisInsertId = $this->quotation_model->insert($thisPOST);

			$thisQuotationitem = get_array_prefix('quotationitem_', $thisPOST);
			$thisQuotationitem = convert_formArray_to_DBArray($thisQuotationitem, 'quotationitem_product_name');
			foreach($thisQuotationitem as $key => $value){
				$value['quotationitem_quotation_id'] = $thisInsertId;
				$this->quotationitem_model->insert($value);
			}

			/* attachment */
			$attachment_path = $_SERVER['DOCUMENT_ROOT'].'/assets/images/attachment/quotation/';
			if($_FILES['attachment']['error'] == UPLOAD_ERR_OK){
				move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment_path.$thisPOST['quotation_id']);
			}

			/* print as PDF */
			$wkhtmltopdf  = $this->config->item("wkhtmltox_path");
			$wkhtmltopdf .= ' --no-outline --header-html "'.base_url('print/quotation/header/quotation_id/'.$thisInsertId).'"';
			$wkhtmltopdf .= ' --margin-top 68 --header-spacing 0 "'.base_url('print/quotation/content/quotation_id/'.$thisInsertId).'"';
			$wkhtmltopdf .= ' assets/images/pdf/quotation/'.$thisPOST['quotation_number'].'-v'.$thisPOST['quotation_version'].'.pdf';
			$output = exec($wkhtmltopdf);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['quotation_id'];
			set_log($thisLog);
			
			$thisAlert = 'Data saved';
			$this->session->set_tempdata('alert', '<div class="btn btn-sm btn-primary btn-block bottom-buffer-10">'.$thisAlert.'</div>', 0);
			redirect('quotation/update/quotation_id/'.$thisPOST['quotation_id']);
		}else{
			/* preset empty data */
			$thisArray = array();
			foreach($this->quotation_model->structure() as $key => $value){
				$thisArray[$value->Field] = '';
			}
			$data['quotation'] = (object)$thisArray;

			$data['quotation_category_discount'] = array();

			/* language */
			$data['languages'] = (object)array(
				(object)array('language_name' => 'en'),
				(object)array('language_name' => 'tc'),
				(object)array('language_name' => 'sc')
			);

			/* currency */
			$thisSelect = array(
				'where' => array('currency_name_in' => array('hkd', 'rmb', 'usd')),
				'return' => 'result'
			);
			$data['currencys'] = $this->currency_model->select($thisSelect);
			// $data['currencys'] = (object)array(
   //              (object)array('currency_name' => 'hkd'),
			// 	(object)array('currency_name' => 'rmb'),
			// 	(object)array('currency_name' => 'usd')
			// );

			/* display_number */
			$data['display_numbers'] = (object)array(
				(object)array('display_number_name' => 'index_number'),
				(object)array('display_number_name' => 'part_number')
			);

			/* status */
			$data['statuss'] = (object)array(
				(object)array('status_name' => 'draft'),
				(object)array('status_name' => 'confirm'),
				(object)array('status_name' => 'cancel')
			);

			/* client */
			switch(true){
				case in_array('1', $this->session->userdata('role')): // administrator
				case in_array('2', $this->session->userdata('role')): // boss
				case in_array('5', $this->session->userdata('role')): // operation manager
				case in_array('6', $this->session->userdata('role')): // operation
				case in_array('7', $this->session->userdata('role')): // account
					/* get all client */
					$thisSelect = array(
						'return' => 'result'
					);
					$data['clients'] = $this->client_model->select($thisSelect);
					break;
				case in_array('3', $this->session->userdata('role')): // sales manager
				case in_array('4', $this->session->userdata('role')): // sales
					/* get related client */
					$thisSelect = array(
						'where' => array(
							'user_id' => $this->session->userdata('user_id')
						),
						'return' => 'result'
					);
					$data['z_client_user_client_ids'] = convert_object_to_array($this->z_client_user_model->select($thisSelect), 'z_client_user_client_id');

					$thisSelect = array(
						'where' => array(
							'client_id_in' => $data['z_client_user_client_ids']
						),
						'return' => 'result'
					);
					$data['clients'] = $this->client_model->select($thisSelect);
					break;
			}

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

			$this->load->view('quotation_view', $data);
		}
	}

	public function select()
	{
		$per_page = get_setting('per_page')->setting_value;

		$thisGET = $this->uri->uri_to_assoc();
		if(!isset($thisGET['quotation_status']) && !isset($thisGET['quotation_create_greateq']) && !isset($thisGET['quotation_create_smalleq'])){
			$thisGET['quotation_default'] = true;
		}
		$thisGET['ONLY_FULL_GROUP_BY_DISABLE'] = true; // fix server issue
		$thisGET['quotation_deleted'] = 'N';

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

				$thisGET['quotation_user_id_in'] = $data['user_ids'];
				break;
			case in_array('4', $this->session->userdata('role')): // sales
				/* get own client */
				$thisGET['quotation_user_id'] = $this->session->userdata('user_id');
				break;
			default:
				break;
		}

		/* check quotationitem */
		if(isset($thisGET['quotationitem_product_code_like']) || isset($thisGET['quotationitem_product_name_like']) || isset($thisGET['quotationitem_product_detail_like'])){
			$thisSelect = array(
				'where' => $thisGET,
				'return' => 'result'
			);
			$data['quotationitems'] = $this->quotationitem_model->select($thisSelect);

			if($data['quotationitems']){
				foreach($data['quotationitems'] as $key => $value){
					$thisGET['quotation_id_in'][] = $value->quotationitem_quotation_id;
				}
			}else{
				$thisGET['quotation_id_in'] = array(0);
			}
		}
		/* check quotationitem */

		$thisSelect = array(
			// 'select' => array(
			// 	'*',
			// 	'max(quotation_id) as max_quotation_id',
			// 	'max(quotation_version) as max_quotation_version'
			// ),
			'where' => $thisGET,
			'group' => 'quotation_number',
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['quotations'] = $this->quotation_model->select($thisSelect);

		$thisSelect = array(
			'where' => $thisGET,
			'group' => 'quotation_number',
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->quotation_model->select($thisSelect);

		/* pagination */
		$this->pagination->initialize(get_pagination_config($per_page, $data['num_rows']));

		/* status */
		$data['statuss'] = (object)array(
			(object)array('status_name' => 'draft'),
			(object)array('status_name' => 'confirm'),
			(object)array('status_name' => 'cancel')
		);

		/* user */
		$thisSelect = array(
			'return' => 'result'
		);
		$data['users'] = $this->user_model->select($thisSelect);

		/* pagination */
		$this->pagination->initialize(get_pagination_config($per_page, $data['num_rows']));

		$this->load->view('quotation_view', $data);
	}

	public function setting()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$this->quotation_model->update($thisPOST);

			/* print as PDF */
			$wkhtmltopdf  = $this->config->item("wkhtmltox_path");
			$wkhtmltopdf .= ' --no-outline --header-html "'.base_url('print/quotation/header/quotation_id/'.$thisPOST['quotation_id']).'"';
			$wkhtmltopdf .= ' --margin-top 68 --header-spacing 0 "'.base_url('print/quotation/content/quotation_id/'.$thisPOST['quotation_id']).'"';
			$wkhtmltopdf .= ' assets/images/pdf/quotation/'.$thisPOST['quotation_number'].'-v'.$thisPOST['quotation_version'].'.pdf';
			$output = exec($wkhtmltopdf);

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

			/* display_number */
			$data['display_numbers'] = (object)array(
				(object)array('display_number_name' => 'index_number'),
				(object)array('display_number_name' => 'part_number')
			);

			/* quotation */
			$thisSelect = array(
				'where' => array(
					'quotationitem_quotation_id' => $data['quotation']->quotation_id
				),
				'return' => 'result'
			);
			$data['quotationitems'] = $this->quotationitem_model->select($thisSelect);

			$this->load->view('quotation_view', $data);
		}
	}

	public function duplicate()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$thisPOST['quotation_id'] = '';
			$thisPOST['quotation_serial'] = sprintf("%03s", (get_quotation_serial() + 1));
			$thisPOST['quotation_number'] = 'QO'.date('ym').$thisPOST['quotation_serial'];
			$thisPOST['quotation_version'] = 1;
			$thisPOST['quotation_id'] = $thisInsertId = $this->quotation_model->insert($thisPOST);

			$thisQuotationitem = get_array_prefix('quotationitem_', $thisPOST);
			$thisQuotationitem = convert_formArray_to_DBArray($thisQuotationitem, 'quotationitem_product_name');
			foreach($thisQuotationitem as $key => $value){
				$value['quotationitem_id'] = '';
				$value['quotationitem_quotation_id'] = $thisInsertId;
				$this->quotationitem_model->insert($value);
			}

			/* attachment */
			$attachment_path = $_SERVER['DOCUMENT_ROOT'].'/assets/images/attachment/quotation/';
			if($_FILES['attachment']['error'] == UPLOAD_ERR_OK){
				move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment_path.$thisPOST['quotation_number']);
			}

			/* print as PDF */
			$wkhtmltopdf  = $this->config->item("wkhtmltox_path");
			$wkhtmltopdf .= ' --no-outline --header-html "'.base_url('print/quotation/header/quotation_id/'.$thisInsertId).'"';
			$wkhtmltopdf .= ' --margin-top 68 --header-spacing 0 "'.base_url('print/quotation/content/quotation_id/'.$thisInsertId).'"';
			$wkhtmltopdf .= ' assets/images/pdf/quotation/'.$thisPOST['quotation_number'].'-v'.$thisPOST['quotation_version'].'.pdf';
			$output = exec($wkhtmltopdf);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['quotation_id'];
			set_log($thisLog);

			redirect('quotation');
		}else{
			/* quotation */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'row'
			);
			$data['quotation'] = $this->quotation_model->select($thisSelect);
			$data['quotation']->quotation_project_name = '';
			$data['quotation']->quotation_number = '';
			$data['quotation']->quotation_serial = '';
			$data['quotation']->quotation_version = '';
			$data['quotation']->quotation_expire = '';

			/* currency */
			$data['currencys'] = (object)array(
				(object)array('currency_name' => 'rmb'),
				(object)array('currency_name' => 'hkd'),
				(object)array('currency_name' => 'usd')
			);

			/* display_number */
			$data['display_numbers'] = (object)array(
				(object)array('display_number_name' => 'index_number'),
				(object)array('display_number_name' => 'part_number')
			);

			/* client */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['clients'] = $this->client_model->select($thisSelect);

			/* product */
			$thisSelect = array(
				'where' => array (
					'order' => 'product_type_id',
					'ascend' => 'asc'
				),
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

			$this->load->view('quotation_view', $data);
		}
	}

}
