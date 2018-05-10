<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotation extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// check_session_timeout();
		// check_is_login();
		// convert_get_slashes_pretty_link();
		// check_permission();

		$this->load->model('quotation_model');
		$this->load->model('quotationitem_model');

		setlocale(LC_MONETARY, 'en_HK');
	}

	public function index()
	{
		die('Access denied');
	}

	public function header()
	{
		/* quotation */
		$thisSelect = array(
			'where' => $this->uri->uri_to_assoc(4),
			'return' => 'row'
		);
		$data['quotation'] = $this->quotation_model->select($thisSelect);

		$this->load->view('print/quotation_view', $data);
	}

	public function content()
	{
		$thisGET = $this->uri->uri_to_assoc(4);
		$thisGET['quotationitem_quotation_id'] = $thisGET['quotation_id'];

		/* quotation */
		$thisSelect = array(
			'where' => $thisGET,
			'return' => 'row'
		);
		$data['quotation'] = $this->quotation_model->select($thisSelect);
		
		/* quotationitem */
		$thisSelect = array(
			'where' => $thisGET,
			'return' => 'result'
		);
		$data['quotationitems'] = $this->quotationitem_model->select($thisSelect);

		$this->load->view('print/quotation_view', $data);
	}

	public function document()
	{
		$this->content();
	}

}
