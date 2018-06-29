<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	function update($data = array())
	{
		$data['invoice_user_id'] = $this->session->userdata('user_id');
		$data['invoice_modify'] = date('Y-m-d H:i:s');
		$data = get_array_prefix('invoice_', $data);
		$this->db->where('invoice_id', $data['invoice_id']);
		$thisResult = $this->db->update('invoice', $data); 

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function delete($data = array())
	{
		$data['invoice_modify'] = date('Y-m-d H:i:s');
		// $data['invoice_deleted'] = 'Y';
		$data['invoice_status'] = 'cancel';
		$data = get_array_prefix('invoice_', $data);
		$this->db->where('invoice_id', $data['invoice_id']);
		$thisResult = $this->db->update('invoice', $data);

		$log_SQL = $this->session->userdata('log_SQL');
		$log_SQL[] = array(
			'result' => $thisResult,
			'sql' => $this->db->last_query()
		);
		$this->session->set_userdata('log_SQL', $log_SQL);
	}

	function insert($data = array())
	{
		$data['invoice_id'] = '';
		$data['invoice_user_id'] = $this->session->userdata('user_id');
		$data['invoice_create'] = date('Y-m-d H:i:s');
		$data['invoice_modify'] = date('Y-m-d H:i:s');
		$data['invoice_deleted'] = 'N';
		$data = get_array_prefix('invoice_', $data);
		$thisResult = $this->db->insert('invoice', $data);
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
					case 'invoice_id':
					case 'invoice_salesorder_id':
					case 'invoice_quotation_user_id':
					case 'invoice_number':
					case 'invoice_client_id':
					case 'invoice_client_company_name':
					case 'invoice_project_name':
					case 'invoice_user_id':
					case 'invoice_status':
					case 'invoice_deleted':
						$thisField = $key;
						$this->db->where($thisField, urldecode($value));
						break;
					case 'invoice_id_like':
					case 'invoice_salesorder_id_like':
					case 'invoice_quotation_user_id_like':
					case 'invoice_number_like':
					case 'invoice_client_id_like':
					case 'invoice_client_company_name_like':
					case 'invoice_project_name_like':
					case 'invoice_user_id_like':
					case 'invoice_status_like':
					case 'invoice_deleted_like':
						$thisField = str_replace('_like', '', $key);
						$this->db->like($thisField, urldecode($value));
						break;
					case 'invoice_id_noteq':
					case 'invoice_salesorder_id_noteq':
					case 'invoice_quotation_user_id_noteq':
					case 'invoice_number_noteq':
					case 'invoice_client_id_noteq':
					case 'invoice_client_company_name_noteq':
					case 'invoice_project_name_noteq':
					case 'invoice_user_id_noteq':
					case 'invoice_status_noteq':
					case 'invoice_deleted_noteq':
						$thisField = str_replace('_noteq', '', $key);
						$this->db->where($thisField.' !=', urldecode($value));
						break;
					case 'invoice_id_in':
					case 'invoice_user_id_in':
					case 'invoice_quotation_user_id_in':
						$thisField = str_replace('_in', '', $key);
						$this->db->where($thisField.' in ('.implode(',', $value).')');
						break;
					case 'invoice_id_greateq':
					case 'invoice_number_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where($thisField.' >=', urldecode($value));
						break;
					case 'date_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where('DATE(invoice_create) >=', urldecode($value));
						break;
					case 'invoice_id_smalleq':
					case 'invoice_number_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$this->db->where($thisField.' <=', urldecode($value));
						break;
					case 'date_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$this->db->where('DATE(invoice_create) <=', urldecode($value));
						break;
					case 'invoice_discount_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where($thisField.' >', urldecode($value));
						$this->db->or_group_start();
						$this->db->where('invoice_category_discount !=', '');
						$this->db->group_end();
						break;
					case 'invoice_freight_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where($thisField.' >', urldecode($value));
						break;
					case 'invoice_client_company_name_invoice_client_name_like':
						$this->db->group_start()
							->like('invoice_client_company_name', urldecode($value))
							->or_like('invoice_client_name', urldecode($value))
						->group_end();
						break;
					case 'invoice_default':
						$this->db->group_start()
							->where('invoice_status', 'processing')
							->or_where('invoice_status', 'complete')
						->group_end();
						break;
					case 'invoice_imcomplete':
						$this->db->group_start()
							->where('invoice_status', 'processing')
							->or_where('invoice_status', 'partial')
						->group_end();
						break;
					case 'invoice_id_in':
					case 'invoice_salesorder_id_in':
						$thisField = str_replace('_in', '', $key);
						$this->db->where_in($thisField, $value);
						break;
					case 'invoice_create_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where('DATE(invoice_create) >=', urldecode($value));
						break;
					case 'invoice_commission_status_date_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$this->db->where('DATE(invoice_commission_status_date) >=', urldecode($value));
						break;
					case 'invoice_create_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$this->db->where('DATE(invoice_create) <=', urldecode($value));
						break;
					case 'invoice_commission_status_date_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$this->db->where('DATE(invoice_commission_status_date) <=', urldecode($value));
						break;
					case 'YEAR(invoice_create)':
						$this->db->where('YEAR(invoice_create) = YEAR(CURDATE())');
						break;
					case 'MONTH(invoice_create)':
						$this->db->where('MONTH(invoice_create) = MONTH(CURDATE())');
						break;
					case 'DATE(invoice_create)':
						$this->db->where('DATE(invoice_create) = CURDATE()');
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
			$this->db->order_by('invoice_id', 'desc');
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

		// $this->db->where('invoice_deleted', 'N');
		$this->db->from('invoice');
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

	function account($prefix, $data = array()){

		$where = "";
		if(isset($data['where'])){
			foreach($data['where'] as $key => $value){
				switch($key){
					case 'date_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$where .= " AND ".$prefix."_sort >= '".urldecode($value)."'";
						break;
					case 'date_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$where .= " AND ".$prefix."_sort <= '".urldecode($value)."'";
						break;
					case 'page':
						$data['offset'] = $value;
						break;
				}
			}
		}

		/* limit */
		$limit = '';
		if(isset($data['limit'])){
			$limit = ' limit '.$data['limit'];
		}

		/* offset */

		if(isset($data['offset'])){
			if(isset($data['limit'])){
				$limit = ' limit '.$data['offset'].','.$data['limit'];
			}
		}

		$query = $this->db->query("SELECT * FROM (
										SELECT *, DATE(".$prefix."_create) AS ".$prefix."_sort, 'debit' as ".$prefix."_type FROM ".$prefix." WHERE ".$prefix."_status != 'cancel'
										UNION ALL
										SELECT *, DATE(".$prefix."_modify) AS ".$prefix."_sort, 'credit' as ".$prefix."_type FROM ".$prefix." WHERE ".$prefix."_status = 'complete'
									) account WHERE ".$prefix."_status != 'cancel' ".$where." ORDER BY ".$prefix."_sort ASC".$limit);
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

	function inventory($data = array()){
		$invoice_where = "";
		$purchaseorder_where = "";
		if(isset($data['where'])){
			foreach($data['where'] as $key => $value){
				switch($key){
					case 'date_greateq':
						$thisField = str_replace('_greateq', '', $key);
						$invoice_where .= " AND invoice_create >= '".urldecode($value)."'";
						$purchaseorder_where .= " AND purchaseorder_create >= '".urldecode($value)."'";
						break;
					case 'date_smalleq':
						$thisField = str_replace('_smalleq', '', $key);
						$invoice_where .= " AND invoice_create <= '".urldecode($value)."'";
						$purchaseorder_where .= " AND purchaseorder_create <= '".urldecode($value)."'";
						break;
					case 'page':
						$data['offset'] = $value;
						break;
				}
			}
		}

		$query = $this->db->query("SELECT * FROM (
										SELECT 
										invoiceitem_category_id as category_id,
										invoiceitem_product_code as product_code, 
										invoiceitem_subtotal as product_subtotal, 
										invoice_exchange_rate as exchange_rate,
										DATE(invoice_create) AS inventory_sort, 
										invoice_number as inventory_number,
										invoice_client_id as company_id,
										'inv' as inventory_type 
										FROM invoiceitem left join invoice on invoiceitem_invoice_id = invoice_id WHERE 1 ".$invoice_where."
										UNION ALL
										SELECT 
										purchaseorderitem_category_id as category_id,
										purchaseorderitem_product_code as product_code, 
										purchaseorderitem_subtotal as product_subtotal, 
										purchaseorder_vendor_exchange_rate as exchange_rate,
										DATE(purchaseorder_create) AS inventory_sort, 
										purchaseorder_number as inventory_number,
										purchaseorder_vendor_id as company_id,
										'po' as inventory_type  
										FROM purchaseorderitem left join purchaseorder on purchaseorderitem_purchaseorder_id = purchaseorder_id WHERE 1 ".$purchaseorder_where."
									) inventory ORDER BY category_id ASC, inventory_sort ASC");
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
		$query = $this->db->query("show full columns from invoice");
		return $query->result();
	}
 
}