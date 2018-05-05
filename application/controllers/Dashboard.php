<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		check_session_timeout();
		check_is_login();

		$this->load->model('dashboard_model');
		$this->load->model('user_model');

		$this->load->model('salesorder_model');
		$this->load->model('salesorderitem_model');
		$this->load->model('quotation_model');
		$this->load->model('purchaseorder_model');
		$this->load->model('invoice_model');
		$this->load->model('deliverynote_model');	

		setlocale(LC_MONETARY, 'en_HK');	
	}

	public function index()
	{
		redirect('dashboard/select');
	}

	public function select()
	{
		$per_page = 5;

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

		/*************************** Dashboard ***************************/
		$thisGET['MONTH(salesorder_create)'] = true;
		$thisSelect = array(
			'select' => array(
				'sum(salesorder_total) as sum_salesorder_total',
			),
			'where' => $thisGET,
			'return' => 'row'
		);
		$data['sumSalesordersTotalMonthly'] = $this->salesorder_model->select($thisSelect);
		/*-----------------------------------------------*/
		$thisGET['MONTH(quotation_create)'] = true;
		$thisSelect = array(
			'select' => array(
				'count(quotation_number) as count_quotation_total',
			),
			'where' => $thisGET,
			'return' => 'row'
		);
		$data['countQuotationTotalMonthly'] = $this->quotation_model->select($thisSelect);
		/*-----------------------------------------------*/
		$thisGET['salesorder_status'] = 'processing';
		$thisSelect = array(
			'select' => array(
				'count(salesorder_id) as count_salesorder_total',
			),
			'where' => $thisGET,
			'return' => 'row'
		);
		$data['countSalesorderTotal'] = $this->salesorder_model->select($thisSelect);
		/*-----------------------------------------------*/
		$thisGET['invoice_status'] = 'processing';
		$thisSelect = array(
			'select' => array(
				'count(invoice_id) as count_invoice_total',
			),
			'where' => $thisGET,
			'return' => 'row'
		);
		$data['countInvoiceTotal'] = $this->invoice_model->select($thisSelect);
		/*************************** Dashboard ***************************/

		/*************************** Summary ***************************/
		$thisSelect = array(
			'where' => $thisGET,
			'group' => 'salesorder_number',
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['summarySalesorders'] = $this->salesorder_model->select($thisSelect);

		foreach($data['summarySalesorders'] as $key => $value){
			/* purchaseorder */
			$thisSelect = array(
				'where' => array(
					'purchaseorder_salesorder_id' => $value->salesorder_id,
					'purchaseorder_status_noteq' => 'cancel'
				),
				'return' => 'result'
			);
			$data['summaryPurchaseorders'] = $this->purchaseorder_model->select($thisSelect);
			$data['summarySalesorders'][$key]->purchaseorders = $data['summaryPurchaseorders'];
		}

		foreach($data['summarySalesorders'] as $key => $value){
			/* invoice */
			$thisSelect = array(
				'where' => array(
					'invoice_salesorder_id' => $value->salesorder_id,
					'invoice_status_noteq' => 'cancel'
				),
				'return' => 'result'
			);
			$data['summaryInvoices'] = $this->invoice_model->select($thisSelect);
			$data['summarySalesorders'][$key]->invoices = $data['summaryInvoices'];
		}

		foreach($data['summarySalesorders'] as $key => $value){
			/* invoice */
			$thisSelect = array(
				'where' => array(
					'deliverynote_salesorder_id' => $value->salesorder_id,
					'deliverynote_status_noteq' => 'cancel'
				),
				'return' => 'result'
			);
			$data['summaryDeliverynotes'] = $this->deliverynote_model->select($thisSelect);
			$data['summarySalesorders'][$key]->deliverynotes = $data['summaryDeliverynotes'];
		}
		/*************************** Summary ***************************/

		/*************************** Processing SO ***************************/
		$thisGET['salesorder_status'] = 'processing';

		$thisSelect = array(
			'where' => $thisGET,
			'order' => 'salesorder_expire',
			'ascend' => 'asc',
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['processingSalesorders'] = $this->salesorder_model->select($thisSelect);
		/*************************** Processing SO ***************************/

		/*************************** Processing PO ***************************/
		$thisGET['purchaseorder_status'] = 'processing';

		$thisSelect = array(
			'where' => $thisGET,
			'order' => 'purchaseorder_reminder_date',
			'ascend' => 'asc',
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['processingPurchaseorders'] = $this->purchaseorder_model->select($thisSelect);
		/*************************** Processing PO ***************************/

		/*************************** Processing IN ***************************/
		$thisGET['invoice_status'] = 'processing';

		$thisSelect = array(
			'where' => $thisGET,
			'order' => 'invoice_expire',
			'ascend' => 'asc',
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['processingInvoices'] = $this->invoice_model->select($thisSelect);
		/*************************** Processing IN ***************************/

		/*************************** Processing DN ***************************/
		$thisGET['deliverynote_status'] = 'processing';

		$thisSelect = array(
			'where' => $thisGET,
			'order' => 'deliverynote_expire',
			'ascend' => 'asc',
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['processingDeliverynotes'] = $this->deliverynote_model->select($thisSelect);
		/*************************** Processing DN ***************************/

		$this->load->view('dashboard_view', $data);
	}

}
