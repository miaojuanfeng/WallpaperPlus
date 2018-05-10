<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchaseorderitem_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	function update($data = array())
	{
		// $data['purchaseorderitem_modify'] = date('Y-m-d H:i:s');
		$data = get_array_prefix('purchaseorderitem_', $data);
		$this->db->where('purchaseorderitem_id', $data['purchaseorderitem_id']);
		$thisResult = $this->db->update('purchaseorderitem', $data); 

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function delete($data = array())
	{
		// $data['purchaseorderitem_modify'] = date('Y-m-d H:i:s');
		// $data['purchaseorderitem_deleted'] = 'Y';
		// $data = get_array_prefix('purchaseorder_', $data);
		$this->db->where('purchaseorderitem_purchaseorder_id', $data['purchaseorder_id']);
		$thisResult = $this->db->delete('purchaseorderitem');

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function insert($data = array())
	{
		$data['purchaseorderitem_id'] = '';
		// $data['purchaseorderitem_create'] = date('Y-m-d H:i:s');
		// $data['purchaseorderitem_modify'] = date('Y-m-d H:i:s');
		// $data['purchaseorderitem_deleted'] = 'N';
		$data = get_array_prefix('purchaseorderitem_', $data);
		$thisResult = $this->db->insert('purchaseorderitem', $data);
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
		/* select */
		if(isset($data['select'])){
			foreach($data['select'] as $key => $value){
				$this->db->select($value);
			}
		}

		/* where */
		$where = "";
		if(isset($data['where'])){
			foreach($data['where'] as $key => $value){
				switch($key){
					case 'purchaseorderitem_id':
					case 'purchaseorderitem_purchaseorder_id':
					case 'purchaseorderitem_product_id':
					case 'purchaseorderitem_product_code':
					case 'purchaseorderitem_product_name':
					case 'purchaseorderitem_product_detail':
					case 'purchaseorderitem_name':
						$thisField = $key;
						$this->db->where($thisField, urldecode($value));
						break;
					case 'purchaseorderitem_id_like':
					case 'purchaseorderitem_purchaseorder_like':
					case 'purchaseorderitem_product_id_like':
					case 'purchaseorderitem_product_code_like':
					case 'purchaseorderitem_product_name_like':
					case 'purchaseorderitem_product_detail_like':
					case 'purchaseorderitem_name_like':
						$thisField = str_replace('_like', '', $key);
						$this->db->like($thisField, urldecode($value));
						break;
					case 'purchaseorderitem_id_noteq':
					case 'purchaseorderitem_purchaseorder_id_noteq':
					case 'purchaseorderitem_product_id_noteq':
					case 'purchaseorderitem_product_code_noteq':
					case 'purchaseorderitem_product_name_noteq':
					case 'purchaseorderitem_product_detail_noteq':
					case 'purchaseorderitem_name_noteq':
						$thisField = str_replace('_noteq', '', $key);
						$this->db->where($thisField.' !=', urldecode($value));
						break;
					case 'purchaseorderitem_id_in':
					case 'purchaseorderitem_purchaseorder_id_in':
					case 'purchaseorderitem_product_id_in':
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

		// $this->db->where('purchaseorderitem_deleted', 'N');
		$this->db->from('purchaseorderitem');
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
		$query = $this->db->query("show full columns from purchaseorderitem");
		return $query->result();
	}
 
}