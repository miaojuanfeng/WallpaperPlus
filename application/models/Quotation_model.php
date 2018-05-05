<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotation_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	function update($data = array())
	{
		$data['quotation_modify'] = date('Y-m-d H:i:s');
		$data = get_array_prefix('quotation_', $data);
		$this->db->where('quotation_id', $data['quotation_id']);
		$thisResult = $this->db->update('quotation', $data); 

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function delete($data = array())
	{
		$data['quotation_modify'] = date('Y-m-d H:i:s');
		// $data['quotation_deleted'] = 'Y';
		$data['quotation_status'] = 'cancel';
		$data = get_array_prefix('quotation_', $data);
		$this->db->where('quotation_id', $data['quotation_id']);
		$thisResult = $this->db->update('quotation', $data);

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function insert($data = array())
	{
		$data['quotation_user_id'] = $this->session->userdata('user_id');
		$data['quotation_create'] = date('Y-m-d H:i:s');
		$data['quotation_modify'] = date('Y-m-d H:i:s');
		$data['quotation_deleted'] = 'N';
		$data = get_array_prefix('quotation_', $data);
		$thisResult = $this->db->insert('quotation', $data);
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
					case 'quotation_id':
					case 'quotation_number':
					case 'quotation_client_id':
					case 'quotation_client_company_name':
					case 'quotation_project_name':
					case 'quotation_user_id':
					case 'quotation_status':
					case 'quotation_deleted':
						$thisField = $key;
						$this->db->where($thisField, urldecode($value));
						break;
					case 'quotation_id_like':
					case 'quotation_number_like':
					case 'quotation_client_id_like':
					case 'quotation_client_company_name_like':
					case 'quotation_project_name_like':
					case 'quotation_user_id_like':
					case 'quotation_status_like':
					case 'quotation_deleted_like':
						$thisField = str_replace('_like', '', $key);
						$this->db->like($thisField, urldecode($value));
						break;
					case 'quotation_id_noteq':
					case 'quotation_number_noteq':
					case 'quotation_client_id_noteq':
					case 'quotation_client_company_name_noteq':
					case 'quotation_project_name_noteq':
					case 'quotation_user_id_noteq':
					case 'quotation_status_noteq':
					case 'quotation_deleted_noteq':
						$thisField = str_replace('_noteq', '', $key);
						$this->db->where($thisField.' !=', urldecode($value));
						break;
					case 'quotation_id_in':
					case 'quotation_user_id_in':
						$thisField = str_replace('_in', '', $key);
						$this->db->where($thisField.' in ('.implode(',', $value).')');
						break;
					case 'quotation_id_greateq':
					case 'quotation_number_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where($thisField.' >=', urldecode($value));
						break;
					case 'quotation_id_smalleq':
					case 'quotation_number_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$this->db->where($thisField.' <=', urldecode($value));
						break;
					case 'quotation_client_company_name_quotation_client_name_like':
						$this->db->group_start()
							->like('quotation_client_company_name', urldecode($value))
							->or_like('quotation_client_name', urldecode($value))
						->group_end();
						break;
					case 'ONLY_FULL_GROUP_BY_DISABLE':
						$this->db->join('(select quotation_number as t2_quotation_number, max(quotation_version) as t2_max_quotation_version from quotation group by quotation_number) t2', 'quotation_number = t2_quotation_number and quotation_version = t2_max_quotation_version');
						break;
					case 'quotation_default':
						$this->db->group_start()
							->group_start()
								->where('quotation_status', 'draft')
								->where('DATE(quotation_create) >=', date('Y-m-d', strtotime('-2 month', time())))
							->group_end()
							->or_where('quotation_status', 'confirm')
						->group_end();
						break;
					case 'quotation_id_in':
						$thisField = str_replace('_in', '', $key);
						$this->db->where_in($thisField, $value);
						break;
					case 'quotation_create_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where('DATE(quotation_create) >=', urldecode($value));
						break;
					case 'quotation_create_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$this->db->where('DATE(quotation_create) <=', urldecode($value));
						break;
					case 'YEAR(quotation_create)':
						$this->db->where('YEAR(quotation_create) = YEAR(CURDATE())');
						break;
					case 'MONTH(quotation_create)':
						$this->db->where('MONTH(quotation_create) = MONTH(CURDATE())');
						break;
					case 'DATE(quotation_create)':
						$this->db->where('DATE(quotation_create) = CURDATE()');
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
			$this->db->order_by('quotation_number', 'desc');
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

		// $this->db->where('quotation_deleted', 'N');
		$this->db->from('quotation');
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
		$query = $this->db->query("show full columns from quotation");
		return $query->result();
	}
 
}