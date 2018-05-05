<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Commission_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	function update($data = array())
	{
		// update here
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
					case 'salesorder_id':
					case 'salesorder_number':
					case 'salesorder_client_id':
					case 'salesorder_client_company_name':
					case 'salesorder_project_name':
					case 'salesorder_user_id':
					case 'salesorder_status':
					case 'salesorder_commission_status':
					case 'salesorder_deleted':
						$thisField = $key;
						$this->db->where($thisField, urldecode($value));
						break;
					case 'salesorder_id_like':
					case 'salesorder_number_like':
					case 'salesorder_client_id_like':
					case 'salesorder_client_company_name_like':
					case 'salesorder_project_name_like':
					case 'salesorder_user_id_like':
					case 'salesorder_status_like':
					case 'salesorder_commission_status_like':
					case 'salesorder_deleted_like':
						$thisField = str_replace('_like', '', $key);
						$this->db->like($thisField, urldecode($value));
						break;
					case 'salesorder_id_noteq':
					case 'salesorder_number_noteq':
					case 'salesorder_client_id_noteq':
					case 'salesorder_client_company_name_noteq':
					case 'salesorder_project_name_noteq':
					case 'salesorder_user_id_noteq':
					case 'salesorder_status_noteq':
					case 'salesorder_commission_status_eq':
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