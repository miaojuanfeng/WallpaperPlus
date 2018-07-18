<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	function update($data = array())
	{
		$data['client_modify'] = date('Y-m-d H:i:s');
		$data = get_array_prefix('client_', $data);
		$this->db->where('client_id', $data['client_id']);
		$thisResult = $this->db->update('client', $data); 

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function delete($data = array())
	{
		$data['client_modify'] = date('Y-m-d H:i:s');
		$data['client_deleted'] = 'Y';
		$data = get_array_prefix('client_', $data);
		$this->db->where('client_id', $data['client_id']);
		$thisResult = $this->db->update('client', $data);

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function insert($data = array())
	{
		$data['client_user_id'] = $this->session->userdata('user_id');
		$data['client_create'] = date('Y-m-d H:i:s');
		$data['client_modify'] = date('Y-m-d H:i:s');
		$data['client_deleted'] = 'N';
		$data = get_array_prefix('client_', $data);
		$thisResult = $this->db->insert('client', $data);
		$thisInsertId = $this->db->insert_id();

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);

		return($thisInsertId);
	}

	function select($data = array())
	{
		/* where */
		$where = "";
		if(isset($data['where'])){
			foreach($data['where'] as $key => $value){
				switch($key){
					case 'client_id':
					case 'client_firstname':
					case 'client_user_id':
						$thisField = $key;
						$this->db->where($thisField, urldecode($value));
						break;
					case 'client_id_like':
					case 'client_firstname_like':
					case 'client_user_id_like':
					case 'client_company_name_like':
						$thisField = str_replace('_like', '', $key);
						$this->db->like($thisField, urldecode($value));
						break;
					case 'client_id_noteq':
					case 'client_firstname_noteq':
					case 'client_user_id_noteq':
						$thisField = str_replace('_noteq', '', $key);
						$this->db->where($thisField.' !=', urldecode($value));
						break;
					case 'client_id_in':
					case 'client_name_in':
					case 'client_email_in':
						$thisField = str_replace('_in', '', $key);
						$this->db->where($thisField.' in ('.implode(',', $value).')');
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
		}else{
			$this->db->order_by('client_recall', 'asc');
			$this->db->order_by('client_id', 'desc');
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

		$this->db->where('client_deleted', 'N');
		$this->db->select('*, (SELECT followup_recall FROM followup WHERE followup_client_id = client_id ORDER BY followup_id DESC LIMIT 1) AS client_recall');
		$this->db->from('client');
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
		$query = $this->db->query("show full columns from client");
		return $query->result();
	}
 
}