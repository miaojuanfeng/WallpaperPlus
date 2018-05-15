<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stockstatus extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// session_write_close();
		// session_id($this->input->post()['thisSession']);
		// session_start();
		check_session_timeout();
		check_is_login();
		convert_get_slashes_pretty_link();
		check_permission();

		$this->load->model('purchaseorder_model');
		$this->load->model('purchaseorderitem_model');
		$this->load->model('salesorder_model');
		$this->load->model('vendor_model');
		$this->load->model('product_model');
		$this->load->model('user_model');
		$this->load->model('salesorderitem_model');
		$this->load->model('terms_model');
		$this->load->model('z_role_user_model');
	}

	public function index()
	{
		redirect('stockstatus/select');
	}

	public function update()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
            $this->purchaseorder_model->update($thisPOST);

			$thisSalesorderitem = get_array_prefix('purchaseorderitem_', $thisPOST);
			$thisSalesorderitem = convert_formArray_to_DBArray($thisSalesorderitem, 'purchaseorderitem_product_name');
			foreach($thisSalesorderitem as $key => $value){
				$this->purchaseorderitem_model->update($value);
			}

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['purchaseorder_id'];
			set_log($thisLog);

			$thisAlert = 'Data saved';
			$this->session->set_tempdata('alert', '<div class="btn btn-sm btn-primary btn-block bottom-buffer-10">'.$thisAlert.'</div>', 0);
			redirect('stockstatus/update/purchaseorder_id/'.$thisPOST['purchaseorder_id']);
		}else{
			$thisPOST = $this->uri->uri_to_assoc();

			/* purchaseorder */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'row'
			);
			$data['purchaseorder'] = $this->purchaseorder_model->select($thisSelect);

			/* currency */
			$data['currencys'] = (object)array(
				(object)array('currency_name' => 'rmb'),
				(object)array('currency_name' => 'hkd'),
				(object)array('currency_name' => 'usd')
			);

			/* status */
			$data['statuss'] = (object)array(
				(object)array('status_name' => 'processing'),
				(object)array('status_name' => 'complete'),
				(object)array('status_name' => 'cancel')
			);

			/* product */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['products'] = $this->product_model->select($thisSelect);

			/* vendor */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['vendors'] = $this->vendor_model->select($thisSelect);

			/* salesorder */
			$thisSelect = array(
				'where' => array(
					'salesorder_status' => 'processing'
				),
				'return' => 'result'
			);
			$data['salesorders'] = $this->salesorder_model->select($thisSelect);

			/* user */
			$thisSelect = array(
				'where' => array(
					'user_id' => $this->session->userdata('user_id')
				),
				'return' => 'row'
			);
			$data['user'] = $this->user_model->select($thisSelect);

			/* get sales manager & sales user */
			$thisSelect = array(
				'where' => array(
					'role_id_in' => array(
						3, // sales manager group
						4 // sales group
					)
				),
				'return' => 'result'
			);
			$data['z_role_user_user_ids'] = convert_object_to_array($this->z_role_user_model->select($thisSelect), 'z_role_user_user_id');

			$thisSelect = array(
				'where' => array(
					'user_id_in' => $data['z_role_user_user_ids']
				),
				'return' => 'result'
			);
			$data['users'] = $this->user_model->select($thisSelect);
			/* get sales manager & sales user */

			/* purchaseorderitem */
			$thisSelect = array(
				'where' => array(
					'purchaseorderitem_purchaseorder_id' => $data['purchaseorder']->purchaseorder_id,
				),
				'return' => 'result'
			);
			$data['purchaseorderitems'] = $this->purchaseorderitem_model->select($thisSelect);

			$this->load->view('stockstatus_view', $data);
		}
	}

	public function delete()
	{
	}

    /**
     *
     */
    public function insert()
	{
	}

	public function select()
	{
	}

}
