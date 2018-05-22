<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	function update($data = array())
	{
		$data['product_modify'] = date('Y-m-d H:i:s');
		$data = get_array_prefix('product_', $data);
		$this->db->where('product_id', $data['product_id']);
		$thisResult = $this->db->update('product', $data); 

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function delete($data = array())
	{
		$data['product_modify'] = date('Y-m-d H:i:s');
		$data['product_deleted'] = 'Y';
		$data = get_array_prefix('product_', $data);
		$this->db->where('product_id', $data['product_id']);
		$thisResult = $this->db->update('product', $data);

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function insert($data = array())
	{
		$data['product_create'] = date('Y-m-d H:i:s');
		$data['product_modify'] = date('Y-m-d H:i:s');
		$data['product_deleted'] = 'N';
		$data = get_array_prefix('product_', $data);
		$thisResult = $this->db->insert('product', $data);
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
					case 'product_id':
					case 'product_code':
					case 'product_name':
						$thisField = $key;
						$this->db->where($thisField, urldecode($value));
						break;
					case 'product_id_like':
					case 'product_code_like':
                    case 'product_wpp_code_like':
					case 'product_name_like':
						$thisField = str_replace('_like', '', $key);
						$this->db->like($thisField, urldecode($value));
						break;
					case 'product_id_noteq':
					case 'product_code_noteq':
                    case 'product_wpp_code_noteq':
					case 'product_name_noteq':
						$thisField = str_replace('_noteq', '', $key);
						$this->db->where($thisField.' !=', urldecode($value));
						break;
                    case 'product_vendor_id_in':
                        $thisField = str_replace('_in', '', $key);
                        $this->db->where($thisField.' in ('.implode(',', $value).')');
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
		}else{
            $this->db->limit(100);
        }

		/* offset */
		if(isset($data['offset'])){
			if(isset($data['limit'])){
				$this->db->limit($data['limit'], $data['offset']);
			}
		}

		$this->db->where('product_deleted', 'N');
		$this->db->from('product');
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
		$query = $this->db->query("show full columns from product");
		return $query->result();
	}
 
}