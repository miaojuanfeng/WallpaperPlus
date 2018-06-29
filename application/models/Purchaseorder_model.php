<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchaseorder_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	function update($data = array())
	{
		$data['purchaseorder_user_id'] = $this->session->userdata('user_id');
		$data['purchaseorder_modify'] = date('Y-m-d H:i:s');
		$data = get_array_prefix('purchaseorder_', $data);
		$this->db->where('purchaseorder_id', $data['purchaseorder_id']);
		$thisResult = $this->db->update('purchaseorder', $data);

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function delete($data = array())
	{
		$data['purchaseorder_modify'] = date('Y-m-d H:i:s');
		// $data['purchaseorder_deleted'] = 'Y';
		$data['purchaseorder_status'] = 'cancel';
		$data = get_array_prefix('purchaseorder_', $data);
		$this->db->where('purchaseorder_id', $data['purchaseorder_id']);
		$thisResult = $this->db->update('purchaseorder', $data);

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function insert($data = array())
	{
		$data['purchaseorder_id'] = '';
		$data['purchaseorder_user_id'] = $this->session->userdata('user_id');
		$data['purchaseorder_create'] = date('Y-m-d H:i:s');
		$data['purchaseorder_modify'] = date('Y-m-d H:i:s');
		$data['purchaseorder_deleted'] = 'N';
		$data = get_array_prefix('purchaseorder_', $data);
		$thisResult = $this->db->insert('purchaseorder', $data);
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
					case 'purchaseorder_id':
					case 'purchaseorder_salesorder_id':
					case 'purchaseorder_number':
					case 'purchaseorder_client_id':
					case 'purchaseorder_client_company_name':
					case 'purchaseorder_project_name':
					case 'purchaseorder_user_id':
					case 'purchaseorder_status':
                    case 'purchaseorder_arrive_status':
					case 'purchaseorder_deleted':
						$thisField = $key;
						$this->db->where($thisField, urldecode($value));
						break;
					case 'purchaseorder_id_like':
					case 'purchaseorder_salesorder_id_like':
					case 'purchaseorder_number_like':
					case 'purchaseorder_client_id_like':
					case 'purchaseorder_client_company_name_like':
					case 'purchaseorder_project_name_like':
					case 'purchaseorder_user_id_like':
					case 'purchaseorder_status_like':
					case 'purchaseorder_deleted_like':
						$thisField = str_replace('_like', '', $key);
						$this->db->like($thisField, urldecode($value));
						break;
					case 'purchaseorder_id_noteq':
					case 'purchaseorder_salesorder_id_noteq':
					case 'purchaseorder_number_noteq':
					case 'purchaseorder_client_id_noteq':
					case 'purchaseorder_client_company_name_noteq':
					case 'purchaseorder_project_name_noteq':
					case 'purchaseorder_user_id_noteq':
					case 'purchaseorder_status_noteq':
					case 'purchaseorder_deleted_noteq':
						$thisField = str_replace('_noteq', '', $key);
						$this->db->where($thisField.' !=', urldecode($value));
						break;
					case 'purchaseorder_id_in':
					case 'purchaseorder_user_id_in':
						$thisField = str_replace('_in', '', $key);
						$this->db->where($thisField.' in ('.implode(',', $value).')');
						break;
                    case 'purchaseorder_id_not_in':
                        $thisField = str_replace('_not_in', '', $key);
                        $this->db->where($thisField.' not in ('.implode(',', $value).')');
                        break;
					case 'purchaseorder_status_in':
						$thisField = str_replace('_in', '', $key);
						$this->db->where($thisField.' in ("'.implode('","', $value).'")');
						break;
					case 'purchaseorder_id_greateq':
					case 'purchaseorder_number_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where($thisField.' >=', urldecode($value));
						break;
					case 'purchaseorder_id_smalleq':
					case 'purchaseorder_number_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$this->db->where($thisField.' <=', urldecode($value));
						break;
					case 'purchaseorder_client_company_name_purchaseorder_client_name_like':
						$this->db->group_start()
							->like('purchaseorder_client_company_name', urldecode($value))
							->or_like('purchaseorder_client_name', urldecode($value))
						->group_end();
						break;
					case 'purchaseorder_default':
						$this->db->group_start()
							->where('purchaseorder_status', 'processing')
							->or_where('purchaseorder_status', 'complete')
						->group_end();
						break;
					case 'purchaseorder_imcomplete':
						$this->db->group_start()
							->where('purchaseorder_status', 'processing')
							->or_where('purchaseorder_status', 'partial')
						->group_end();
						break;
					case 'purchaseorder_id_in':
					case 'purchaseorder_salesorder_id_in':
						$thisField = str_replace('_in', '', $key);
						$this->db->where_in($thisField, $value);
						break;
					case 'purchaseorder_create_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where('DATE(purchaseorder_create) >=', urldecode($value));
						break;
					case 'purchaseorder_create_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$this->db->where('DATE(purchaseorder_create) <=', urldecode($value));
						break;
					case 'date_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where('purchaseorder_issue >=', urldecode($value));
						break;
					case 'date_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$this->db->where('purchaseorder_issue <=', urldecode($value));
						break;
					case 'YEAR(purchaseorder_create)':
						$this->db->where('YEAR(purchaseorder_create) = YEAR(CURDATE())');
						break;
					case 'MONTH(purchaseorder_create)':
						$this->db->where('MONTH(purchaseorder_create) = MONTH(CURDATE())');
						break;
					case 'DATE(purchaseorder_create)':
						$this->db->where('DATE(purchaseorder_create) = CURDATE()');
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
			$this->db->order_by('purchaseorder_id', 'desc');
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

		// $this->db->where('purchaseorder_deleted', 'N');
		$this->db->from('purchaseorder');
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
		$query = $this->db->query("show full columns from purchaseorder");
		return $query->result();
	}
 
}