<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salesorderitem_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	function update($data = array())
	{
		// $data['salesorderitem_modify'] = date('Y-m-d H:i:s');
		$data = get_array_prefix('salesorderitem_', $data);
		$this->db->where('salesorderitem_id', $data['salesorderitem_id']);
		$thisResult = $this->db->update('salesorderitem', $data); 

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function delete($data = array())
	{
		// $data['salesorderitem_modify'] = date('Y-m-d H:i:s');
		// $data['salesorderitem_deleted'] = 'Y';
		// $data = get_array_prefix('salesorder_', $data);
		$this->db->where('salesorderitem_salesorder_id', $data['salesorder_id']);
		$thisResult = $this->db->delete('salesorderitem');

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function insert($data = array())
	{
		$data['salesorderitem_id'] = '';
		// $data['salesorderitem_create'] = date('Y-m-d H:i:s');
		// $data['salesorderitem_modify'] = date('Y-m-d H:i:s');
		// $data['salesorderitem_deleted'] = 'N';
		$data = get_array_prefix('salesorderitem_', $data);
		$thisResult = $this->db->insert('salesorderitem', $data);
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
					case 'salesorderitem_id':
					case 'salesorderitem_salesorder_id':
					case 'salesorderitem_product_id':
					case 'salesorderitem_product_code':
					case 'salesorderitem_product_name':
					case 'salesorderitem_product_detail':
					case 'salesorderitem_name':
						$thisField = $key;
						$this->db->where($thisField, urldecode($value));
						break;
					case 'salesorderitem_id_like':
					case 'salesorderitem_salesorder_id_like':
					case 'salesorderitem_product_id_like':
					case 'salesorderitem_product_code_like':
					case 'salesorderitem_product_name_like':
					case 'salesorderitem_product_detail_like':
					case 'salesorderitem_name_like':
						$thisField = str_replace('_like', '', $key);
						$this->db->like($thisField, urldecode($value));
						break;
					case 'salesorderitem_id_noteq':
					case 'salesorderitem_salesorder_id_noteq':
					case 'salesorderitem_product_id_noteq':
					case 'salesorderitem_product_code_noteq':
					case 'salesorderitem_product_name_noteq':
					case 'salesorderitem_product_detail_noteq':
					case 'salesorderitem_name_noteq':
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

		// $this->db->where('salesorderitem_deleted', 'N');
		$this->db->from('salesorderitem');
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
		$query = $this->db->query("show full columns from salesorderitem");
		return $query->result();
	}
 
}