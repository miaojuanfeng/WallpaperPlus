<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchaseorder extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// check_session_timeout();
		// check_is_login();
		// convert_get_slashes_pretty_link();
		// check_permission();

		$this->load->model('purchaseorder_model');
		$this->load->model('purchaseorderitem_model');
        $this->load->model('invoice_model');
        $this->load->model('invoiceitem_model');

		setlocale(LC_MONETARY, 'en_HK');
	}

	public function index()
	{
		die('Access denied');
	}

	public function header()
	{
		/* purchaseorder */
		$thisSelect = array(
			'where' => $this->uri->uri_to_assoc(4),
			'return' => 'row'
		);
		$data['purchaseorder'] = $this->purchaseorder_model->select($thisSelect);

		$this->load->view('print/purchaseorder_view', $data);
	}

	public function content()
	{
		$thisGET = $this->uri->uri_to_assoc(4);
		$thisGET['purchaseorderitem_purchaseorder_id'] = $thisGET['purchaseorder_id'];

		/* purchaseorder */
		$thisSelect = array(
			'where' => $thisGET,
			'return' => 'row'
		);
		$data['purchaseorder'] = $this->purchaseorder_model->select($thisSelect);
		
		/* purchaseorderitem */
		$thisSelect = array(
			'where' => $thisGET,
			'return' => 'result'
		);
		$data['purchaseorderitems'] = $this->purchaseorderitem_model->select($thisSelect);

   //      $data['salesorder'] = get_salesorder($data['purchaseorder']->purchaseorder_salesorder_id);

   //      if( $data['salesorder'] ){
			// /* invoice */
	  //       $thisSelect = array(
	  //           'where' => array(
	  //               'invoice_salesorder_id' => $data['salesorder']->salesorder_id,
	  //               'invoice_status_noteq' => 'cancel'
	  //           ),
	  //           'return' => 'result'
	  //       );
	  //       $data['invoices'] = $this->invoice_model->select($thisSelect);

	  //       /* invoiceitem */
	  //       foreach($data['invoices'] as $key => $value) {
	  //           $thisSelect = array(
	  //               'where' => array(
	  //                   'invoiceitem_invoice_id' => $value->invoice_id
	  //               ),
	  //               'return' => 'result'
	  //           );
	  //           $data['invoices'][$key]->invoiceitems = $this->invoiceitem_model->select($thisSelect);
	  //       }

	  //       $data['salesorder']->invoices = $data['invoices'];
	  //   }else{
	  //   	$data['invoices'] = array();
	  //   }

		$this->load->view('print/purchaseorder_view', $data);
	}

	public function document()
	{
		$this->content();
	}

}
