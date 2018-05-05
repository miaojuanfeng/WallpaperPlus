<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Waybillin_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	function update($data = array())
	{
		$data['waybillin_modify'] = date('Y-m-d H:i:s');
		$data = get_array_prefix('waybillin_', $data);
		$this->db->where('waybillin_id', $data['waybillin_id']);
		$thisResult = $this->db->update('waybillin', $data);

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function delete($data = array())
	{
		$data['waybillin_modify'] = date('Y-m-d H:i:s');
		$data['waybillin_deleted'] = 'Y';
		$data = get_array_prefix('waybillin_', $data);
		$this->db->where('waybillin_id', $data['waybillin_id']);
		$thisResult = $this->db->update('waybillin', $data);

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function insert($data = array())
	{
		$data['waybillin_create'] = date('Y-m-d H:i:s');
		$data['waybillin_modify'] = date('Y-m-d H:i:s');
		$data['waybillin_deleted'] = 'N';
		$data = get_array_prefix('waybillin_', $data);
		$thisResult = $this->db->insert('waybillin', $data);
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
					case 'waybillin_id':
					case 'waybillin_number':
						$thisField = $key;
						$this->db->where($thisField, urldecode($value));
						break;
					case 'waybillin_id_like':
					case 'waybillin_number_like':
						$thisField = str_replace('_like', '', $key);
						$this->db->like($thisField, urldecode($value));
						break;
					case 'waybillin_id_noteq':
					case 'waybillin_number_noteq':
						$thisField = str_replace('_noteq', '', $key);
						$this->db->where($thisField.' !=', urldecode($value));
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

		$this->db->where('waybillin_deleted', 'N');
		$this->db->from('waybillin');
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
		$query = $this->db->query("show full columns from waybillin");
		return $query->result();
	}
 
}