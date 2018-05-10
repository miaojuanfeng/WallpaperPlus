<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Document extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		redirect('document/select');
	}

	public function update()
	{
	}

	public function delete()
	{
	}

	public function insert()
	{
	}

	public function select()
	{
		$this->load->view('document_view');
	}

}
