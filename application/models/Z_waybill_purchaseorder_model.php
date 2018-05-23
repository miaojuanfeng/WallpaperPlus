<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Z_waybill_purchaseorder_model extends CI_Model {
	
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
			case (isset($data['waybill_id'])):
				$thisData['z_waybill_purchaseorder_waybill_id'] = $data['waybill_id'];
				$data = get_array_prefix('z_waybill_purchaseorder_', $data);
				break;
			case (isset($data['purchaseorder_id'])):
				$thisData['z_waybill_purchaseorder_purchaseorder_id'] = $data['purchaseorder_id'];
				$data = get_array_prefix('z_waybill_purchaseorder_', $data);
				break;
		}
		$this->db->where($thisData);
		$thisResult = $this->db->delete('z_waybill_purchaseorder');

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
			case (isset($data['waybill_id'])):
				$thisData['z_waybill_purchaseorder_waybill_id'] = $data['waybill_id'];
				$data = get_array_prefix('z_waybill_purchaseorder_', $data);
				foreach($data as $key => $value){
					foreach($value as $key1 => $value1){
						$thisData['z_waybill_purchaseorder_purchaseorder_id'] = $value1;
						$thisResult = $this->db->insert('z_waybill_purchaseorder', $thisData);

						$log_SQL = $this->session->userdata('log_SQL');
						$log_SQL[] = array(
							'result' => $thisResult,
							'sql' => $this->db->last_query()
						);
						$this->session->set_userdata('log_SQL', $log_SQL);
					}
				}
				break;
			case (isset($data['purchaseorder_id'])):
				$thisData['z_waybill_purchaseorder_purchaseorder_id'] = $data['purchaseorder_id'];
				$data = get_array_prefix('z_waybill_purchaseorder_', $data);
				foreach($data as $key => $value){
					foreach($value as $key1 => $value1){
						$thisData['z_waybill_purchaseorder_waybill_id'] = $value1;
						$thisResult = $this->db->insert('z_waybill_purchaseorder', $thisData);

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
					case 'waybill_id':
					case 'purchaseorder_id':
						$where .= " and ".'z_waybill_purchaseorder_'.$key." = '".$value."'";
						break;
					case 'waybill_id_in':
					case 'purchaseorder_id_in':
						$key = str_replace('_in', '', $key);
						$where .= " and ".'z_waybill_purchaseorder_'.$key.' in ('.implode(',', $value).')';
						break;
                    case 'waybill_id_not_in':
                        $key = str_replace('_not_in', '', $key);
                        $where .= " and ".'z_waybill_purchaseorder_'.$key.' not in ('.implode(',', $value).')';
                        break;
				}
			}
		}

		/* query */
		$sql = "
		select
			*
		from
			waybill
			left join z_waybill_purchaseorder on waybill_id = z_waybill_purchaseorder_waybill_id
			left join purchaseorder on z_waybill_purchaseorder_purchaseorder_id = purchaseorder_id
		where
			waybill_deleted = 'N'
			and purchaseorder_deleted = 'N'
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