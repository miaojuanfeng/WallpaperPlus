<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		check_session_timeout();
		check_is_login();
		convert_get_slashes_pretty_link();
		check_permission();

		$this->load->model('setting_model');
	}

	public function index()
	{
		redirect('setting/update');
	}

	public function update()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();

			$thisSetting = get_array_prefix('setting_', $thisPOST);
			$thisSetting = convert_formArray_to_DBArray($thisSetting, 'setting_id'); //form array to DB array
			foreach($thisSetting as $key => $value){
				$this->setting_model->update($value);
			}

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			// $thisLog['log_record_id'] = $thisPOST['setting_id'];
			$thisLog['log_record_id'] = 0;
			set_log($thisLog);

			redirect('setting/update');
		}else{
			/* setting */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['settings'] = $this->setting_model->select($thisSelect);

			$this->load->view('setting_view', $data);
		}
	}

	public function delete()
	{
		// delete here
	}

	public function insert()
	{
		// insert here
	}

	public function select()
	{
		// select here
	}

}
