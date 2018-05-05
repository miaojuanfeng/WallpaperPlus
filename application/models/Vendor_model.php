<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	function update($data = array())
	{
		$data['vendor_modify'] = date('Y-m-d H:i:s');
		$data = get_array_prefix('vendor_', $data);
		$this->db->where('vendor_id', $data['vendor_id']);
		$thisResult = $this->db->update('vendor', $data); 

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function delete($data = array())
	{
		$data['vendor_modify'] = date('Y-m-d H:i:s');
		$data['vendor_deleted'] = 'Y';
		$data = get_array_prefix('vendor_', $data);
		$this->db->where('vendor_id', $data['vendor_id']);
		$thisResult = $this->db->update('vendor', $data);

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function insert($data = array())
	{
		$data['vendor_user_id'] = $this->session->userdata('user_id');
		$data['vendor_create'] = date('Y-m-d H:i:s');
		$data['vendor_modify'] = date('Y-m-d H:i:s');
		$data['vendor_deleted'] = 'N';
		$data = get_array_prefix('vendor_', $data);
		$thisResult = $this->db->insert('vendor', $data);
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
					case 'vendor_id':
					case 'vendor_firstname':
					case 'vendor_company_name':
					case 'vendor_user_id':
						$thisField = $key;
						$this->db->where($thisField, urldecode($value));
						break;
					case 'vendor_id_like':
					case 'vendor_firstname_like':
					case 'vendor_company_name_like':
					case 'vendor_user_id_like':
						$thisField = str_replace('_like', '', $key);
						$this->db->like($thisField, urldecode($value));
						break;
					case 'vendor_id_noteq':
					case 'vendor_firstname_noteq':
					case 'vendor_company_name_noteq':
					case 'vendor_user_id_noteq':
						$thisField = str_replace('_noteq', '', $key);
						$this->db->where($thisField.' !=', urldecode($value));
						break;
					case 'vendor_id_in':
					case 'vendor_firstname_in':
					case 'vendor_company_name_in':
					case 'vendor_email_in':
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

		$this->db->where('vendor_deleted', 'N');
		$this->db->from('vendor');
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
		$query = $this->db->query("show full columns from vendor");
		return $query->result();
	}
 
}