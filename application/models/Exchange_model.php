<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exchange_model extends CI_Model {
	
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
		$data['exchange_user_id'] = $this->session->userdata('user_id');
		$data['exchange_create'] = date('Y-m-d H:i:s');
		$data = get_array_prefix('exchange_', $data);
		$thisResult = $this->db->insert('exchange', $data);
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
					case 'exchange_id':
					case 'exchange_type':
					case 'exchange_product_id':
						$thisField = $key;
						$this->db->where($thisField, urldecode($value));
						break;
					case 'exchange_id_like':
					case 'exchange_type_like':
					case 'exchange_product_id_like':
						$thisField = str_replace('_like', '', $key);
						$this->db->like($thisField, urldecode($value));
						break;
					case 'exchange_id_noteq':
					case 'exchange_type_noteq':
					case 'exchange_product_id_noteq':
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

		/* group */
		if(isset($data['group'])){
			$this->db->group_by($data['group']);
		}

		/* order */
		$this->db->order_by('exchange_id', 'desc');
		// if(isset($data['order'])){
		// 	$this->db->order_by($data['order'], $data['ascend']);
		// }

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

		$this->db->from('exchange');
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
		$query = $this->db->query("show full columns from exchange");
		return $query->result();
	}
 
}