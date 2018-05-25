<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Z_product_attribute_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	function update($data = array())
	{
		// do update here.
	}

	function delete($data = array())
	{
		switch(true){
			case (isset($data['product_id'])):
				$thisData['z_product_attribute_product_id'] = $data['product_id'];
				$data = get_array_prefix('z_product_attribute_', $data);
				break;
			case (isset($data['attribute_id'])):
				$thisData['z_product_attribute_attribute_id'] = $data['attribute_id'];
				$data = get_array_prefix('z_product_attribute_', $data);
				break;
		}
		$this->db->where($thisData);
		$thisResult = $this->db->delete('z_product_attribute');

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function insert($data = array())
	{
		switch(true){
			case (isset($data['product_id'])):
				$thisData['z_product_attribute_product_id'] = $data['product_id'];
				$data = get_array_prefix('z_product_attribute_', $data);
				foreach($data as $key => $value){
					foreach($value as $key1 => $value1){
						$thisData['z_product_attribute_attribute_id'] = $value1;
						$thisResult = $this->db->insert('z_product_attribute', $thisData);

						$log_SQL = $this->session->userdata('log_SQL');
						$log_SQL[] = array(
							'result' => $thisResult,
							'sql' => $this->db->last_query()
						);
						$this->session->set_userdata('log_SQL', $log_SQL);
					}
				}
				break;
			case (isset($data['attribute_id'])):
				$thisData['z_product_attribute_attribute_id'] = $data['attribute_id'];
				$data = get_array_prefix('z_product_attribute_', $data);
				foreach($data as $key => $value){
					foreach($value as $key1 => $value1){
						$thisData['z_product_attribute_product_id'] = $value1;
						$thisResult = $this->db->insert('z_product_attribute', $thisData);

						$log_SQL = $this->session->userdata('log_SQL');
						$log_SQL[] = array(
							'result' => $thisResult,
							'sql' => $this->db->last_query()
						);
						$this->session->set_userdata('log_SQL', $log_SQL);
					}
				}
				break;
		}
	}

	function select($data = array())
	{
		/* where */
		$where = "";
		if(isset($data['where'])){
			foreach($data['where'] as $key => $value){
				switch($key){
					case 'product_id':
					case 'attribute_id':
						$where .= " and ".'z_product_attribute_'.$key." = '".$value."'";
						break;
					case 'product_id_in':
					case 'attribute_id_in':
						$key = str_replace('_in', '', $key);
						$where .= " and ".'z_product_attribute_'.$key.' in ('.implode(',', $value).')';
						break;
                    case 'product_id_not_in':
                        $key = str_replace('_not_in', '', $key);
                        $where .= " and ".'z_product_attribute_'.$key.' not in ('.implode(',', $value).')';
                        break;
				}
			}
		}

		/* query */
		$sql = "
		select
			*
		from
			product 
			left join z_product_attribute on product_id = z_product_attribute_product_id 
		where
			product_deleted = 'N' 
			".$where."
		";
		$query = $this->db->query($sql);
//		 echo $this->db->last_query();
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