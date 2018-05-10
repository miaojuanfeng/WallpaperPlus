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

		$this->load->view('print/purchaseorder_view', $data);
	}

	public function document()
	{
		$this->content();
	}

}
