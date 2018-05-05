<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoiceitem_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	function update($data = array())
	{
		// $data['invoiceitem_modify'] = date('Y-m-d H:i:s');
		// $data = get_array_prefix('invoiceitem_', $data);
		// $this->db->where('invoiceitem_id', $data['invoiceitem_id']);
		// $thisResult = $this->db->update('invoiceitem', $data); 

		// $log_SQL = $this->session->userdata('log_SQL');
		// $log_SQL[] = array(
		// 	'result' => $thisResult,
		// 	'sql' => $this->db->last_query()
		// );
		// $this->session->set_userdata('log_SQL', $log_SQL);
	}

	function delete($data = array())
	{
		// $data['invoiceitem_modify'] = date('Y-m-d H:i:s');
		// $data['invoiceitem_deleted'] = 'Y';
		// $data = get_array_prefix('invoiceitem_', $data);
		// $this->db->where('invoiceitem_id', $data['invoiceitem_id']);
		// $thisResult = $this->db->update('invoiceitem', $data);

		// $log_SQL = $this->session->userdata('log_SQL');
		// $log_SQL[] = array(
		// 	'result' => $thisResult,
		// 	'sql' => $this->db->last_query()
		// );
		// $this->session->set_userdata('log_SQL', $log_SQL);
	}

	function insert($data = array())
	{
		// $data['invoiceitem_create'] = date('Y-m-d H:i:s');
		// $data['invoiceitem_modify'] = date('Y-m-d H:i:s');
		// $data['invoiceitem_deleted'] = 'N';
		$data = get_array_prefix('invoiceitem_', $data);
		$thisResult = $this->db->insert('invoiceitem', $data);
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
					case 'invoiceitem_id':
					case 'invoiceitem_invoice_id':
					case 'invoiceitem_name':
						$thisField = $key;
						$this->db->where($thisField, urldecode($value));
						break;
					case 'invoiceitem_id_like':
					case 'invoiceitem_invoice_like':
					case 'invoiceitem_name_like':
						$thisField = str_replace('_like', '', $key);
						$this->db->like($thisField, urldecode($value));
						break;
					case 'invoiceitem_id_noteq':
					case 'invoiceitem_invoice_id_noteq':
					case 'invoiceitem_name_noteq':
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

		// $this->db->where('invoiceitem_deleted', 'N');
		$this->db->from('invoiceitem');
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
		$query = $this->db->query("show full columns from invoiceitem");
		return $query->result();
	}
 
}