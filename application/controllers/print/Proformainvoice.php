<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proformainvoice extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// check_session_timeout();
		// check_is_login();
		// convert_get_slashes_pretty_link();
		// check_permission();

		$this->load->model('proformainvoice_model');
		$this->load->model('proformainvoiceitem_model');

		setlocale(LC_MONETARY, 'en_HK');
	}

	public function index()
	{
		die('Access denied');
	}

	public function header()
	{
		/* proformainvoice */
		$thisSelect = array(
			'where' => $this->uri->uri_to_assoc(4),
			'return' => 'row'
		);
		$data['proformainvoice'] = $this->proformainvoice_model->select($thisSelect);

		$this->load->view('print/proformainvoice_view', $data);
	}

	public function content()
	{
		$thisGET = $this->uri->uri_to_assoc(4);
		$thisGET['proformainvoiceitem_proformainvoice_id'] = $thisGET['proformainvoice_id'];

		/* proformainvoice */
		$thisSelect = array(
			'where' => $thisGET,
			'return' => 'row'
		);
		$data['proformainvoice'] = $this->proformainvoice_model->select($thisSelect);
		
		/* proformainvoiceitem */
		$thisSelect = array(
			'where' => $thisGET,
			'return' => 'result'
		);
		$data['proformainvoiceitems'] = $this->proformainvoiceitem_model->select($thisSelect);

		$this->load->view('print/proformainvoice_view', $data);
	}

	public function document()
	{
		$this->content();
	}

}
