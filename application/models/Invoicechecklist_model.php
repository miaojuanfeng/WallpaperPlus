<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoicechecklist_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	function update($data = array())
	{
		$data['invoice_user_id'] = $this->session->userdata('user_id');
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
		// delete here
	}

	function insert($data = array())
	{
		// insert here
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
					case 'invoice_client_id':
					case 'invoice_client_company_name':
					case 'invoice_project_name':
					case 'invoice_user_id':
					case 'invoice_status':
					case 'invoice_commission_status':
					case 'invoice_deleted':
						$thisField = $key;
						$this->db->where($thisField, urldecode($value));
						break;
					case 'invoice_id_like':
					case 'invoice_number_like':
					case 'invoice_client_id_like':
					case 'invoice_client_company_name_like':
					case 'invoice_project_name_like':
					case 'invoice_user_id_like':
					case 'invoice_status_like':
					case 'invoice_commission_status_like':
					case 'invoice_deleted_like':
						$thisField = str_replace('_like', '', $key);
						$this->db->like($thisField, urldecode($value));
						break;
					case 'invoice_id_noteq':
					case 'invoice_number_noteq':
					case 'invoice_client_id_noteq':
					case 'invoice_client_company_name_noteq':
					case 'invoice_project_name_noteq':
					case 'invoice_user_id_noteq':
					case 'invoice_status_noteq':
					case 'invoice_commission_status_eq':
					case 'invoice_deleted_noteq':
						$thisField = str_replace('_noteq', '', $key);
						$this->db->where($thisField.' !=', urldecode($value));
						break;
					case 'invoice_id_in':
					case 'invoice_salesorder_id_in':
					case 'invoice_user_id_in':
						$thisField = str_replace('_in', '', $key);
						$this->db->where($thisField.' in ('.implode(',', $value).')');
						break;
					case 'invoice_id_greateq':
					case 'invoice_number_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where($thisField.' >=', urldecode($value));
						break;
					case 'invoice_id_smalleq':
					case 'invoice_number_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$this->db->where($thisField.' <=', urldecode($value));
						break;
					case 'invoice_client_company_name_invoice_client_name_like':
						$this->db->group_start()
							->like('invoice_client_company_name', urldecode($value))
							->or_like('invoice_client_name', urldecode($value))
						->group_end();
						break;
					case 'invoice_default':
						$this->db->group_start()
							->where('invoice_status', 'processing')
							->or_where('invoice_status', 'complete')
						->group_end();
						break;
					case 'invoice_id_in':
					case 'invoice_quotation_id_in':
						$thisField = str_replace('_in', '', $key);
						$this->db->where_in($thisField, $value);
						break;
					case 'invoice_create_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where('DATE(invoice_create) >=', urldecode($value));
						break;
					case 'invoice_create_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$this->db->where('DATE(invoice_create) <=', urldecode($value));
						break;
					case 'YEAR(invoice_create)':
						$this->db->where('YEAR(invoice_create) = YEAR(CURDATE())');
						break;
					case 'MONTH(invoice_create)':
						$this->db->where('MONTH(invoice_create) = MONTH(CURDATE())');
						break;
					case 'DATE(invoice_create)':
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

		// $this->db->where('invoice_deleted', 'N');
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