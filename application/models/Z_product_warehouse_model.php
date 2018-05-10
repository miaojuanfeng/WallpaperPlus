<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Z_product_warehouse_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	function update($data = array())
	{
		$data = get_array_prefix('z_product_warehouse_', $data);
		$this->db->where('z_product_warehouse_product_id', $data['z_product_warehouse_product_id']);
		$this->db->where('z_product_warehouse_warehouse_id', $data['z_product_warehouse_warehouse_id']);
		$thisResult = $this->db->update('z_product_warehouse', $data); 

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function delete($data = array())
	{
		switch(true){
			case (isset($data['product_id'])):
				$thisData['z_product_warehouse_product_id'] = $data['product_id'];
				$data = get_array_prefix('z_product_warehouse_', $data);
				break;
			case (isset($data['warehouse_id'])):
				$thisData['z_product_warehouse_warehouse_id'] = $data['warehouse_id'];
				$data = get_array_prefix('z_product_warehouse_', $data);
				break;
		}
		$this->db->where($thisData);
		$this->db->delete('z_product_warehouse');
	}

	function insert($data = array())
	{
		/* auto create record when visit warehouse/select */
		$data = get_array_prefix('z_product_warehouse_', $data);
		$thisResult = $this->db->insert('z_product_warehouse', $data);
		$thisInsertId = $this->db->insert_id();
	}

	function select($data = array())
	{
		/* where */
		$where = "";
		if(isset($data['where'])){
			foreach($data['where'] as $key => $value){
				switch($key){
					case 'product_id':
					case 'warehouse_id':
						$where .= " and ".'z_product_warehouse_'.$key." = '".$value."'";
						break;
					case 'product_id_like':
					case 'product_code_like':
					case 'product_name_like':
					case 'warehouse_id_like':
						$thisField = str_replace('_like', '', $key);
						$where .= " and ".$thisField." like '%".$value."%'";
						break;
					case 'z_product_warehouse_quantity_noteq':
						$thisField = str_replace('_noteq', '', $key);
						$where .= " and ".$thisField." != '".$value."'";
						break;
				}
			}
		}

		/* group */
		$group = "";
		if(isset($data['group'])){
			$group .= " group by ".$data['group'];
		}

		/* query */
		$sql = "
		select
			*
		from
			product
			left join z_product_warehouse on product_id = z_product_warehouse_product_id
			left join warehouse on z_product_warehouse_warehouse_id = warehouse_id
		where
			product_deleted = 'N'
			and warehouse_deleted = 'N'
			".$where."
			".$group."
		";
		$query = $this->db->query($sql);
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
 
}