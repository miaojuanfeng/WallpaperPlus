<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proformainvoice_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	function update($data = array())
	{
		$data['proformainvoice_user_id'] = $this->session->userdata('user_id');
		$data['proformainvoice_modify'] = date('Y-m-d H:i:s');
		$data = get_array_prefix('proformainvoice_', $data);
		$this->db->where('proformainvoice_id', $data['proformainvoice_id']);
		$thisResult = $this->db->update('proformainvoice', $data); 

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function delete($data = array())
	{
		$data['proformainvoice_modify'] = date('Y-m-d H:i:s');
		// $data['proformainvoice_deleted'] = 'Y';
		$data['proformainvoice_status'] = 'cancel';
		$data = get_array_prefix('proformainvoice_', $data);
		$this->db->where('proformainvoice_id', $data['proformainvoice_id']);
		$thisResult = $this->db->update('proformainvoice', $data);

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function insert($data = array())
	{
		$data['proformainvoice_id'] = '';
		$data['proformainvoice_user_id'] = $this->session->userdata('user_id');
		$data['proformainvoice_create'] = date('Y-m-d H:i:s');
		$data['proformainvoice_modify'] = date('Y-m-d H:i:s');
		$data['proformainvoice_deleted'] = 'N';
		$data = get_array_prefix('proformainvoice_', $data);
		$thisResult = $this->db->insert('proformainvoice', $data);
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
					case 'proformainvoice_id':
					case 'proformainvoice_salesorder_id':
					case 'proformainvoice_quotation_user_id':
					case 'proformainvoice_number':
					case 'proformainvoice_client_id':
					case 'proformainvoice_client_company_name':
					case 'proformainvoice_project_name':
					case 'proformainvoice_user_id':
					case 'proformainvoice_status':
					case 'proformainvoice_deleted':
						$thisField = $key;
						$this->db->where($thisField, urldecode($value));
						break;
					case 'proformainvoice_id_like':
					case 'proformainvoice_salesorder_id_like':
					case 'proformainvoice_quotation_user_id_like':
					case 'proformainvoice_number_like':
					case 'proformainvoice_client_id_like':
					case 'proformainvoice_client_company_name_like':
					case 'proformainvoice_project_name_like':
					case 'proformainvoice_user_id_like':
					case 'proformainvoice_status_like':
					case 'proformainvoice_deleted_like':
						$thisField = str_replace('_like', '', $key);
						$this->db->like($thisField, urldecode($value));
						break;
					case 'proformainvoice_id_noteq':
					case 'proformainvoice_salesorder_id_noteq':
					case 'proformainvoice_quotation_user_id_noteq':
					case 'proformainvoice_number_noteq':
					case 'proformainvoice_client_id_noteq':
					case 'proformainvoice_client_company_name_noteq':
					case 'proformainvoice_project_name_noteq':
					case 'proformainvoice_user_id_noteq':
					case 'proformainvoice_status_noteq':
					case 'proformainvoice_deleted_noteq':
						$thisField = str_replace('_noteq', '', $key);
						$this->db->where($thisField.' !=', urldecode($value));
						break;
					case 'proformainvoice_id_in':
					case 'proformainvoice_user_id_in':
					case 'proformainvoice_quotation_user_id_in':
						$thisField = str_replace('_in', '', $key);
						$this->db->where($thisField.' in ('.implode(',', $value).')');
						break;
					case 'proformainvoice_id_greateq':
					case 'proformainvoice_number_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where($thisField.' >=', urldecode($value));
						break;
					case 'proformainvoice_id_smalleq':
					case 'proformainvoice_number_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$this->db->where($thisField.' <=', urldecode($value));
						break;
					case 'proformainvoice_client_company_name_proformainvoice_client_name_like':
						$this->db->group_start()
							->like('proformainvoice_client_company_name', urldecode($value))
							->or_like('proformainvoice_client_name', urldecode($value))
						->group_end();
						break;
					case 'proformainvoice_default':
						$this->db->group_start()
							->where('proformainvoice_status', 'processing')
							->or_where('proformainvoice_status', 'complete')
						->group_end();
						break;
					case 'proformainvoice_id_in':
					case 'proformainvoice_salesorder_id_in':
						$thisField = str_replace('_in', '', $key);
						$this->db->where_in($thisField, $value);
						break;
					case 'proformainvoice_create_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where('DATE(proformainvoice_create) >=', urldecode($value));
						break;
					case 'proformainvoice_create_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$this->db->where('DATE(proformainvoice_create) <=', urldecode($value));
						break;
					case 'YEAR(proformainvoice_create)':
						$this->db->where('YEAR(proformainvoice_create) = YEAR(CURDATE())');
						break;
					case 'MONTH(proformainvoice_create)':
						$this->db->where('MONTH(proformainvoice_create) = MONTH(CURDATE())');
						break;
					case 'DATE(proformainvoice_create)':
						$this->db->where('DATE(proformainvoice_create) = CURDATE()');
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
			$this->db->order_by('proformainvoice_id', 'desc');
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

		// $this->db->where('proformainvoice_deleted', 'N');
		$this->db->from('proformainvoice');
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
		$query = $this->db->query("show full columns from proformainvoice");
		return $query->result();
	}
 
}