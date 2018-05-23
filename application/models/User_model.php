<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	function update($data = array())
	{
		$thisPassword = $data['user_password'];
		unset($data['user_password']);

		$data['user_modify'] = date('Y-m-d H:i:s');
		$data = get_array_prefix('user_', $data);
		$this->db->where('user_id', $data['user_id']);
		$thisResult = $this->db->update('user', $data);

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);

		if($thisPassword != ''){
			$this->db->where('user_id', $data['user_id']);
			$this->db->update('user', array('user_password' => $thisPassword));
		}
	}

	function delete($data = array())
	{
		$data['user_modify'] = date('Y-m-d H:i:s');
		$data['user_deleted'] = 'Y';
		$data = get_array_prefix('user_', $data);
		$this->db->where('user_id', $data['user_id']);
		$thisResult = $this->db->update('user', $data);

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function insert($data = array())
	{
		$thisPassword = $data['user_password'];
		unset($data['user_password']);

		$data['user_create'] = date('Y-m-d H:i:s');
		$data['user_modify'] = date('Y-m-d H:i:s');
		$data['user_deleted'] = 'N';
		$data = get_array_prefix('user_', $data);
		$thisResult = $this->db->insert('user', $data);
		$thisInsertId = $this->db->insert_id();

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);

		if($thisPassword != ''){
			$this->db->where('user_id', $thisInsertId);
			$this->db->update('user', array('user_password' => $thisPassword));
		}

		return($thisInsertId);
	}

	function select($data = array())
	{
		/* where */
		$where = "";
		if(isset($data['where'])){
			foreach($data['where'] as $key => $value){
				switch($key){
					case 'user_id':
					case 'user_name':
					case 'user_email':
					case 'user_user_id':
                    case 'user_code':
						$thisField = $key;
						$this->db->where($thisField, urldecode($value));
						break;
					case 'user_id_like':
					case 'user_name_like':
					case 'user_email_like':
					case 'user_user_id_like':
						$thisField = str_replace('_like', '', $key);
						$this->db->like($thisField, urldecode($value));
						break;
					case 'user_id_noteq':
					case 'user_name_noteq':
					case 'user_email_noteq':
					case 'user_user_id_noteq':
						$thisField = str_replace('_noteq', '', $key);
						$this->db->where($thisField.' !=', urldecode($value));
						break;
					case 'user_id_in':
					case 'user_name_in':
					case 'user_email_in':
					case 'user_user_id_in':
						$thisField = str_replace('_in', '', $key);
						$this->db->where($thisField.' in ('.implode(',', $value).')');
						break;
					case 'OWN_USER_ID_AND_DOWNLINE_USER_ID':
						$this->db
						->group_start()
							->where('user_id', $value)
							->or_where('user_user_id', $value)
						->group_end();
						break;
					case 'order':
						$data['order'] = $value;
						break;
					case 'ascend':
						$data['ascend'] = $value;
						break;
					case 'page':
						$data['offset'] = $value;
						break;
				}
			}
		}

		/* order */
		if(isset($data['order'])){
			$this->db->order_by($data['order'], $data['ascend']);
		}

		/* limit */
		if(isset($data['limit'])){
			$this->db->limit($data['limit']);
		}

		/* offset */
		if(isset($data['offset'])){
			if(isset($data['limit'])){
				$this->db->limit($data['limit'], $data['offset']);
			}
		}

		$this->db->where('user_deleted', 'N');
		$this->db->from('user');
		$query = $this->db->get();
		// echo $this->db->last_query();
		// exit;

		/* return */
		if(isset($data['return'])){
			switch($data['return']){
				case 'num_rows':
					return $query->num_rows();
					break;
				case 'row':
					return $query->row();
					break;
				default:
					return $query->result();
					break;
			}
		}
	}

	function structure()
	{
		$query = $this->db->query("show full columns from user");
		return $query->result();
	}
 
}