<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salesorder_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	function update($data = array())
	{
		$data['salesorder_user_id'] = $this->session->userdata('user_id');
		$data['salesorder_modify'] = date('Y-m-d H:i:s');
		$data = get_array_prefix('salesorder_', $data);
		$this->db->where('salesorder_id', $data['salesorder_id']);
		$thisResult = $this->db->update('salesorder', $data); 

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function delete($data = array())
	{
		$data['salesorder_modify'] = date('Y-m-d H:i:s');
		// $data['salesorder_deleted'] = 'Y';
		$data['salesorder_status'] = 'cancel';
		$data = get_array_prefix('salesorder_', $data);
		$this->db->where('salesorder_id', $data['salesorder_id']);
		$thisResult = $this->db->update('salesorder', $data);

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function insert($data = array())
	{
		$data['salesorder_id'] = '';
		$data['salesorder_user_id'] = $this->session->userdata('user_id');
		$data['salesorder_create'] = date('Y-m-d H:i:s');
		$data['salesorder_modify'] = date('Y-m-d H:i:s');
		$data['salesorder_deleted'] = 'N';
		$data = get_array_prefix('salesorder_', $data);
		$thisResult = $this->db->insert('salesorder', $data);
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
					case 'salesorder_id':
					case 'salesorder_quotation_user_id':
					case 'salesorder_number':
					case 'salesorder_client_id':
					case 'salesorder_client_company_name':
					case 'salesorder_project_name':
					case 'salesorder_user_id':
					case 'salesorder_status':
					case 'salesorder_deleted':
						$thisField = $key;
						$this->db->where($thisField, urldecode($value));
						break;
					case 'salesorder_id_like':
					case 'salesorder_quotation_user_id_like':
					case 'salesorder_number_like':
					case 'salesorder_client_id_like':
					case 'salesorder_client_company_name_like':
					case 'salesorder_project_name_like':
					case 'salesorder_user_id_like':
					case 'salesorder_status_like':
					case 'salesorder_deleted_like':
						$thisField = str_replace('_like', '', $key);
						$this->db->like($thisField, urldecode($value));
						break;
					case 'salesorder_id_noteq':
					case 'salesorder_quotation_user_id_noteq':
					case 'salesorder_number_noteq':
					case 'salesorder_client_id_noteq':
					case 'salesorder_client_company_name_noteq':
					case 'salesorder_project_name_noteq':
					case 'salesorder_user_id_noteq':
					case 'salesorder_status_noteq':
					case 'salesorder_deleted_noteq':
						$thisField = str_replace('_noteq', '', $key);
						$this->db->where($thisField.' !=', urldecode($value));
						break;
					case 'salesorder_id_in':
					case 'salesorder_user_id_in':
						$thisField = str_replace('_in', '', $key);
						$this->db->where($thisField.' in ('.implode(',', $value).')');
						break;
					case 'salesorder_id_greateq':
					case 'salesorder_number_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where($thisField.' >=', urldecode($value));
						break;
					case 'salesorder_id_smalleq':
					case 'salesorder_number_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$this->db->where($thisField.' <=', urldecode($value));
						break;
					case 'salesorder_client_company_name_salesorder_client_name_like':
						$this->db->group_start()
							->like('salesorder_client_company_name', urldecode($value))
							->or_like('salesorder_client_name', urldecode($value))
						->group_end();
						break;
					case 'salesorder_default':
						$this->db->group_start()
							->where('salesorder_status', 'processing')
							->or_where('salesorder_status', 'complete')
						->group_end();
						break;
					case 'salesorder_id_in':
					case 'salesorder_quotation_id_in':
						$thisField = str_replace('_in', '', $key);
						$this->db->where_in($thisField, $value);
						break;
					case 'salesorder_create_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where('DATE(salesorder_create) >=', urldecode($value));
						break;
					case 'salesorder_create_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$this->db->where('DATE(salesorder_create) <=', urldecode($value));
						break;
					case 'YEAR(salesorder_create)':
						$this->db->where('YEAR(salesorder_create) = YEAR(CURDATE())');
						break;
					case 'MONTH(salesorder_create)':
						$this->db->where('MONTH(salesorder_create) = MONTH(CURDATE())');
						break;
					case 'DATE(salesorder_create)':
						$this->db->where('DATE(salesorder_create) = CURDATE()');
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
			$this->db->order_by('salesorder_id', 'desc');
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

		// $this->db->where('salesorder_deleted', 'N');
		$this->db->from('salesorder');
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
		$query = $this->db->query("show full columns from salesorder");
		return $query->result();
	}
 
}