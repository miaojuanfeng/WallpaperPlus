<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		check_session_timeout();
		check_is_login();
		convert_get_slashes_pretty_link();
		check_permission();

		$this->load->model('vendor_model');
        $this->load->model('currency_model');
	}

	public function index()
	{
		redirect('vendor/select');
	}

	public function update()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$this->vendor_model->update($thisPOST);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['vendor_id'];
			set_log($thisLog);

			redirect($thisPOST['referrer']);
		}else{
			/* vendor */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'row'
			);
			$data['vendor'] = $this->vendor_model->select($thisSelect);

			$thisSelect = array(
				'where' => array(
					'vendor_id_noteq' => $data['vendor']->vendor_id
				),
				'return' => 'result'
			);
			$data['vendors'] = $this->vendor_model->select($thisSelect);

            /* currency */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['currencys'] = $this->currency_model->select($thisSelect);

			$this->load->view('vendor_view', $data);
		}
	}

	public function delete()
	{
		$thisPOST = $this->input->post();
		$this->vendor_model->delete($thisPOST);

		$thisLog['log_permission_class'] = $this->router->fetch_class();
		$thisLog['log_permission_action'] = $this->router->fetch_method();
		$thisLog['log_record_id'] = $thisPOST['vendor_id'];
		set_log($thisLog);

		redirect($this->agent->referrer());
	}

	public function insert()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$thisInsertId = $this->vendor_model->insert($thisPOST);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['vendor_id'];
			set_log($thisLog);

			redirect($thisPOST['referrer']);
		}else{
			/* preset empty data */
			$thisArray = array();
			foreach($this->vendor_model->structure() as $key => $value){
				$thisArray[$value->Field] = '';
			}
			$data['vendor'] = (object)$thisArray;

			$thisSelect = array(
				'where' => array(
					'vendor_id_noteq' => $data['vendor']->vendor_id
				),
				'return' => 'result'
			);
			$data['vendors'] = $this->vendor_model->select($thisSelect);

            /* currency */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['currencys'] = $this->currency_model->select($thisSelect);

			$this->load->view('vendor_view', $data);
		}
	}

	public function select()
	{
		$per_page = get_setting('per_page')->setting_value;

		$thisSelect = array(
			'where' => $this->uri->uri_to_assoc(),
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['vendors'] = $this->vendor_model->select($thisSelect);

		$thisSelect = array(
			'where' => $this->uri->uri_to_assoc(),
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->vendor_model->select($thisSelect);

		/* pagination */
		$this->pagination->initialize(get_pagination_config($per_page, $data['num_rows']));

		$this->load->view('vendor_view', $data);
	}

}
