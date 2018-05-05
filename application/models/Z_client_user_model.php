<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Z_client_user_model extends CI_Model {
	
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
			case (isset($data['client_id'])):
				$thisData['z_client_user_client_id'] = $data['client_id'];
				$data = get_array_prefix('z_client_user_', $data);
				break;
			case (isset($data['user_id'])):
				$thisData['z_client_user_user_id'] = $data['user_id'];
				$data = get_array_prefix('z_client_user_', $data);
				break;
		}
		$this->db->where($thisData);
		$this->db->delete('z_client_user');
	}

	function insert($data = array())
	{
		switch(true){
			case (isset($data['client_id'])):
				$thisData['z_client_user_client_id'] = $data['client_id'];
				$data = get_array_prefix('z_client_user_', $data);
				foreach($data as $key => $value){
					foreach($value as $key1 => $value1){
						$thisData['z_client_user_user_id'] = $value1;
						$this->db->insert('z_client_user', $thisData);
					}
				}
				break;
			case (isset($data['user_id'])):
				$thisData['z_client_user_user_id'] = $data['user_id'];
				$data = get_array_prefix('z_client_user_', $data);
				foreach($data as $key => $value){
					foreach($value as $key1 => $value1){
						$thisData['z_client_user_client_id'] = $value1;
						$this->db->insert('z_client_user', $thisData);
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
					case 'client_id':
					case 'user_id':
						$where .= " and ".'z_client_user_'.$key." = '".$value."'";
						break;
				}
			}
		}

		/* query */
		$sql = "
		select
			*
		from
			client
			left join z_client_user on client_id = z_client_user_client_id
			left join user on z_client_user_user_id = user_id
		where
			client_deleted = 'N'
			and user_deleted = 'N'
			".$where."
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