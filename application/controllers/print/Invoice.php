<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// check_session_timeout();
		// check_is_login();
		// convert_get_slashes_pretty_link();
		// check_permission();

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
		/* invoice */
		$thisSelect = array(
			'where' => $this->uri->uri_to_assoc(4),
			'return' => 'row'
		);
		$data['invoice'] = $this->invoice_model->select($thisSelect);

		/* language */
		$data['language'] = get_print_language($data['invoice']->invoice_language);

		$this->load->view('print/invoice_view', $data);
	}

	public function content()
	{
		$thisGET = $this->uri->uri_to_assoc(4);
		$thisGET['invoiceitem_invoice_id'] = $thisGET['invoice_id'];

		/* invoice */
		$thisSelect = array(
			'where' => $thisGET,
			'return' => 'row'
		);
		$data['invoice'] = $this->invoice_model->select($thisSelect);
		
		/* invoiceitem */
		$thisSelect = array(
			'where' => $thisGET,
			'return' => 'result'
		);
		$data['invoiceitems'] = $this->invoiceitem_model->select($thisSelect);

		/* language */
		$data['language'] = get_print_language($data['invoice']->invoice_language);

		$this->load->view('print/invoice_view', $data);
	}

	public function document()
	{
		$this->content();
	}

}
