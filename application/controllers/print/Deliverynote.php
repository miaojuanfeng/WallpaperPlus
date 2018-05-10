<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Deliverynote extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// check_session_timeout();
		// check_is_login();
		// convert_get_slashes_pretty_link();
		// check_permission();

		$this->load->model('deliverynote_model');
		$this->load->model('deliverynoteitem_model');

		setlocale(LC_MONETARY, 'en_HK');
	}

	public function index()
	{
		die('Access denied');
	}

	public function header()
	{
		/* deliverynote */
		$thisSelect = array(
			'where' => $this->uri->uri_to_assoc(4),
			'return' => 'row'
		);
		$data['deliverynote'] = $this->deliverynote_model->select($thisSelect);

		$this->load->view('print/deliverynote_view', $data);
	}

	public function content()
	{
		$thisGET = $this->uri->uri_to_assoc(4);
		$thisGET['deliverynoteitem_deliverynote_id'] = $thisGET['deliverynote_id'];

		/* deliverynote */
		$thisSelect = array(
			'where' => $thisGET,
			'return' => 'row'
		);
		$data['deliverynote'] = $this->deliverynote_model->select($thisSelect);
		
		/* deliverynoteitem */
		$thisSelect = array(
			'where' => $thisGET,
			'return' => 'result'
		);
		$data['deliverynoteitems'] = $this->deliverynoteitem_model->select($thisSelect);

		$this->load->view('print/deliverynote_view', $data);
	}

	public function document()
	{
		$this->content();
	}

}
