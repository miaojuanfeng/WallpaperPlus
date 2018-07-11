<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salesorder extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// check_session_timeout();
		// check_is_login();
		// convert_get_slashes_pretty_link();
		// check_permission();

		$this->load->model('salesorder_model');
		$this->load->model('salesorderitem_model');

		setlocale(LC_MONETARY, 'en_HK');
	}

	public function index()
	{
		die('Access denied');
	}

	public function header()
	{
		/* salesorder */
		$thisSelect = array(
			'where' => $this->uri->uri_to_assoc(4),
			'return' => 'row'
		);
		$data['salesorder'] = $this->salesorder_model->select($thisSelect);

		/* language */
		$data['language'] = get_print_language($data['salesorder']->salesorder_language);

		$this->load->view('print/salesorder_view', $data);
	}

	public function content()
	{
		$thisGET = $this->uri->uri_to_assoc(4);
		$thisGET['salesorderitem_salesorder_id'] = $thisGET['salesorder_id'];

		/* salesorder */
		$thisSelect = array(
			'where' => $thisGET,
			'return' => 'row'
		);
		$data['salesorder'] = $this->salesorder_model->select($thisSelect);
		
		/* salesorderitem */
		$thisSelect = array(
			'where' => $thisGET,
			'return' => 'result'
		);
		$data['salesorderitems'] = $this->salesorderitem_model->select($thisSelect);

		/* language */
		$data['language'] = get_print_language($data['salesorder']->salesorder_language);

		$this->load->view('print/salesorder_view', $data);
	}

	public function document()
	{
		$this->content();
	}

}
