<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		convert_get_slashes_pretty_link();

		$this->load->model('login_model');
	}

	public function index()
	{
		redirect('login/select');
	}

	public function select()
	{
		// if($this->input->post()){
		// 	$thisPOST = $this->input->post();
		// 	if(isset($thisPOST['salesorder_id'])){
		// 		chuyan($thisPOST);
		// 		exit;
		// 	}
		// }
		if($this->input->post()){
			/* check login */
			$thisSelect = array(
				'where' => $this->input->post(),
				'return' => 'result'
			);
			$user = $this->login_model->check_login($thisSelect);

			if($user){
				$user = $user[0];

				/* get role */
				$thisSelect = array(
					'where' => array('user_id' => $user->user_id),
					'return' => 'result'
				);
				$roles = convert_object_to_array($this->login_model->get_role($thisSelect), 'role_id');

				/* get permission */
				$thisSelect = array(
					'where' => array('user_id' => $user->user_id),
					'return' => 'result'
				);
				$permissions = convert_object_to_array($this->login_model->get_permission($thisSelect), 'permission_name');

				if( $user->user_team_id == 2 ){
				    $user_order_prefix = 'E';
                }else{
                    $user_order_prefix = '';
                }

				/* save session */
				$this->session->set_userdata('user_id', $user->user_id);
				$this->session->set_userdata('role', $roles);
				$this->session->set_userdata('permission', $permissions);
                $this->session->set_userdata('user_order_prefix', $user_order_prefix);

				//$thisReferrer = $this->uri->uri_to_assoc();
				if($this->uri->uri_to_assoc()){
					redirect(base64_decode(urldecode($this->uri->uri_to_assoc()['referrer'])));
				}else{
					redirect('dashboard');
				}
			}else{
				$this->session->set_tempdata('alert', '<div class="btn btn-xs btn-block btn-default">Wrong username & password</div>', 0);
				redirect('login');
			}
		}else{
			$this->session->unset_userdata('user_id');
			$this->session->unset_userdata('permission');
			$this->session->unset_userdata('last_activity');
			$this->load->view('login_view');
		}
	}

}
