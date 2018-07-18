<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Deliverynote_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	function update($data = array())
	{
		$data['deliverynote_user_id'] = $this->session->userdata('user_id');
		$data['deliverynote_modify'] = date('Y-m-d H:i:s');
		$data = get_array_prefix('deliverynote_', $data);
		$this->db->where('deliverynote_id', $data['deliverynote_id']);
		$thisResult = $this->db->update('deliverynote', $data); 

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function delete($data = array())
	{
		$data['deliverynote_modify'] = date('Y-m-d H:i:s');
		// $data['deliverynote_deleted'] = 'Y';
		$data['deliverynote_status'] = 'cancel';
		$data = get_array_prefix('deliverynote_', $data);
		$this->db->where('deliverynote_id', $data['deliverynote_id']);
		$thisResult = $this->db->update('deliverynote', $data);

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function insert($data = array())
	{
		$data['deliverynote_id'] = '';
		$data['deliverynote_user_id'] = $this->session->userdata('user_id');
		$data['deliverynote_create'] = date('Y-m-d H:i:s');
		$data['deliverynote_modify'] = date('Y-m-d H:i:s');
		$data['deliverynote_deleted'] = 'N';
		$data = get_array_prefix('deliverynote_', $data);
		$thisResult = $this->db->insert('deliverynote', $data);
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
					case 'deliverynote_id':
					case 'deliverynote_salesorder_id':
					case 'deliverynote_number':
					case 'deliverynote_client_id':
					case 'deliverynote_client_company_name':
					case 'deliverynote_project_name':
					case 'deliverynote_user_id':
					case 'deliverynote_status':
					case 'deliverynote_deleted':
						$thisField = $key;
						$this->db->where($thisField, urldecode($value));
						break;
					case 'deliverynote_id_like':
					case 'deliverynote_salesorder_id_like':
					case 'deliverynote_number_like':
					case 'deliverynote_client_id_like':
					case 'deliverynote_client_company_name_like':
					case 'deliverynote_project_name_like':
					case 'deliverynote_user_id_like':
					case 'deliverynote_status_like':
					case 'deliverynote_deleted_like':
						$thisField = str_replace('_like', '', $key);
						$this->db->like($thisField, urldecode($value));
						break;
					case 'deliverynote_id_noteq':
					case 'deliverynote_salesorder_id_noteq':
					case 'deliverynote_number_noteq':
					case 'deliverynote_client_id_noteq':
					case 'deliverynote_client_company_name_noteq':
					case 'deliverynote_project_name_noteq':
					case 'deliverynote_user_id_noteq':
					case 'deliverynote_status_noteq':
					case 'deliverynote_deleted_noteq':
						$thisField = str_replace('_noteq', '', $key);
						$this->db->where($thisField.' !=', urldecode($value));
						break;
					case 'deliverynote_id_in':
					case 'deliverynote_user_id_in':
						$thisField = str_replace('_in', '', $key);
						$this->db->where($thisField.' in ('.implode(',', $value).')');
						break;
					case 'deliverynote_id_greateq':
					case 'deliverynote_number_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where($thisField.' >=', urldecode($value));
						break;
					case 'deliverynote_id_smalleq':
					case 'deliverynote_number_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$this->db->where($thisField.' <=', urldecode($value));
						break;
					case 'deliverynote_client_company_name_deliverynote_client_name_like':
						$this->db->group_start()
							->like('deliverynote_client_company_name', urldecode($value))
							->or_like('deliverynote_client_name', urldecode($value))
						->group_end();
						break;
					case 'deliverynote_default':
						$this->db->group_start()
							->where('deliverynote_status', 'processing')
							->or_where('deliverynote_status', 'complete')
						->group_end();
						break;
					case 'deliverynote_id_in':
					case 'deliverynote_salesorder_id_in':
						$thisField = str_replace('_in', '', $key);
						$this->db->where_in($thisField, $value);
						break;
					case 'deliverynote_create_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where('DATE(deliverynote_create) >=', urldecode($value));
						break;
					case 'deliverynote_issue_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where('DATE(deliverynote_issue) >=', urldecode($value));
						break;
					case 'deliverynote_create_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$this->db->where('DATE(deliverynote_create) <=', urldecode($value));
						break;
					case 'deliverynote_issue_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$this->db->where('DATE(deliverynote_issue) <=', urldecode($value));
						break;
					case 'YEAR(deliverynote_create)':
						$this->db->where('YEAR(deliverynote_create) = YEAR(CURDATE())');
						break;
					case 'MONTH(deliverynote_create)':
						$this->db->where('MONTH(deliverynote_create) = MONTH(CURDATE())');
						break;
					case 'DATE(deliverynote_create)':
						$this->db->where('DATE(deliverynote_create) = CURDATE()');
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
			$this->db->order_by('deliverynote_id', 'desc');
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

		// $this->db->where('deliverynote_deleted', 'N');
		$this->db->from('deliverynote');
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
		$query = $this->db->query("show full columns from deliverynote");
		return $query->result();
	}
 
}