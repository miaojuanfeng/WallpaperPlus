<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	function update($data = array())
	{
		$data['invoice_modify'] = date('Y-m-d H:i:s');
		$data = get_array_prefix('invoice_', $data);
		$this->db->where('invoice_id', $data['invoice_id']);
		$thisResult = $this->db->update('invoice', $data); 

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function delete($data = array())
	{
		$data['invoice_modify'] = date('Y-m-d H:i:s');
		$data['invoice_deleted'] = 'Y';
		$data = get_array_prefix('invoice_', $data);
		$this->db->where('invoice_id', $data['invoice_id']);
		$thisResult = $this->db->update('invoice', $data);

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function insert($data = array())
	{
		$data['invoice_user_id'] = $this->session->userdata('user_id');
		$data['invoice_create'] = date('Y-m-d H:i:s');
		$data['invoice_modify'] = date('Y-m-d H:i:s');
		$data['invoice_deleted'] = 'N';
		$data = get_array_prefix('invoice_', $data);
		$thisResult = $this->db->insert('invoice', $data);
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
					case 'invoice_id':
					case 'invoice_number':
					case 'invoice_quotation_number':
					case 'invoice_client_id':
						$thisField = $key;
						$this->db->where($thisField, urldecode($value));
						break;
					case 'invoice_id_like':
					case 'invoice_number_like':
					case 'invoice_quotation_number_like':
					case 'invoice_client_id_like':
						$thisField = str_replace('_like', '', $key);
						$this->db->like($thisField, urldecode($value));
						break;
					case 'invoice_id_noteq':
					case 'invoice_number_noteq':
					case 'invoice_quotation_number_noteq':
					case 'invoice_client_id_noteq':
						$thisField = str_replace('_noteq', '', $key);
						$this->db->where($thisField.' !=', urldecode($value));
						break;
					case 'invoice_client_company_name_invoice_client_name_like':
						$this->db->group_start()
							->like('invoice_client_company_name', urldecode($value))
							->or_like('invoice_client_name', urldecode($value))
						->group_end();
						break;
					case 'DATE(invoice_create)':
						$thisField = $key;
						$this->db->where('DATE(invoice_create) = CURDATE()');
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

		/* group */
		if(isset($data['group'])){
			$this->db->group_by($data['group']);
		}

		/* order */
		if(isset($data['order'])){
			$this->db->order_by($data['order'], $data['ascend']);
		}else{
			$this->db->order_by('invoice_id', 'desc');
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

		$this->db->where('invoice_deleted', 'N');
		$this->db->from('invoice');
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
		$query = $this->db->query("show full columns from invoice");
		return $query->result();
	}
 
}