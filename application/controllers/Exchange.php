<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exchange extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		check_session_timeout();
		check_is_login();
		convert_get_slashes_pretty_link();
		check_permission();

		$this->load->model('exchange_model');
		$this->load->model('warehouse_model');
		$this->load->model('z_product_warehouse_model');
	}

	public function index()
	{
		redirect('exchange/select');
	}

	public function update()
	{
		// update here
	}

	public function delete()
	{
		// delete here
	}

	public function insert()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$thisInsertId = $this->exchange_model->insert($thisPOST);

			switch($thisPOST['exchange_type']){
				case 'in':
					$thisWarehouseToQuantity = get_z_product_warehouse_quantity($thisPOST['exchange_product_id'], $thisPOST['exchange_warehouse_id_to']);
					$thisPOST['z_product_warehouse_product_id'] = $thisPOST['exchange_product_id'];
					$thisPOST['z_product_warehouse_warehouse_id'] = $thisPOST['exchange_warehouse_id_to'];
					$thisPOST['z_product_warehouse_quantity'] = $thisWarehouseToQuantity + $thisPOST['exchange_quantity'];
					$this->z_product_warehouse_model->update($thisPOST);
					break;
				case 'transfer':
					/* warehouse from */
					$thisWarehouseFromQuantity = get_z_product_warehouse_quantity($thisPOST['exchange_product_id'], $thisPOST['exchange_warehouse_id_from']);
					$thisPOST['z_product_warehouse_product_id'] = $thisPOST['exchange_product_id'];
					$thisPOST['z_product_warehouse_warehouse_id'] = $thisPOST['exchange_warehouse_id_from'];
					$thisPOST['z_product_warehouse_quantity'] = $thisWarehouseFromQuantity - $thisPOST['exchange_quantity'];
					$this->z_product_warehouse_model->update($thisPOST);

					/* warehouse to */
					$thisWarehouseToQuantity = get_z_product_warehouse_quantity($thisPOST['exchange_product_id'], $thisPOST['exchange_warehouse_id_to']);
					$thisPOST['z_product_warehouse_product_id'] = $thisPOST['exchange_product_id'];
					$thisPOST['z_product_warehouse_warehouse_id'] = $thisPOST['exchange_warehouse_id_to'];
					$thisPOST['z_product_warehouse_quantity'] = $thisWarehouseToQuantity + $thisPOST['exchange_quantity'];
					$this->z_product_warehouse_model->update($thisPOST);
					break;
				case 'out':
					$thisWarehouseFromQuantity = get_z_product_warehouse_quantity($thisPOST['exchange_product_id'], $thisPOST['exchange_warehouse_id_from']);
					$thisPOST['z_product_warehouse_product_id'] = $thisPOST['exchange_product_id'];
					$thisPOST['z_product_warehouse_warehouse_id'] = $thisPOST['exchange_warehouse_id_from'];
					$thisPOST['z_product_warehouse_quantity'] = $thisWarehouseFromQuantity - $thisPOST['exchange_quantity'];
					$this->z_product_warehouse_model->update($thisPOST);
					break;
			}

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['exchange_id'];
			set_log($thisLog);

			// redirect($thisPOST['referrer']);
			redirect('exchange/select/exchange_type/'.$thisPOST['exchange_type']);
		}else{
			/* preset empty data */
			$thisArray = array();
			foreach($this->exchange_model->structure() as $key => $value){
				$thisArray[$value->Field] = '';
			}
			$data['exchange'] = (object)$thisArray;

			/* warehouse */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['warehousefroms'] = $this->warehouse_model->select($thisSelect);

			/* warehouse */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['warehousetos'] = $this->warehouse_model->select($thisSelect);

			$this->load->view('exchange_view', $data);
		}
	}

	public function batchinsert()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();

			foreach ($thisPOST['exchange_product_id'] as $key => $value) {
				$thisData['exchange_type'] = $thisPOST['exchange_type'];
				$thisData['exchange_warehouse_id_from'] = $thisPOST['exchange_warehouse_id_from'];
				$thisData['exchange_product_id'] = $thisPOST['exchange_product_id'][$key];
				$thisData['exchange_warehouse_id_to'] = $thisPOST['exchange_warehouse_id_to'][$key];
				$thisData['exchange_quantity'] = $thisPOST['exchange_quantity'][$key];
				$thisData['exchange_remark'] = $thisPOST['exchange_remark'][$key];
				$thisInsertId = $this->exchange_model->insert($thisData);

				$thisWarehouseToQuantity = get_z_product_warehouse_quantity($thisPOST['exchange_product_id'][$key], $thisPOST['exchange_warehouse_id_to'][$key]);
				$thisData['z_product_warehouse_product_id'] = $thisPOST['exchange_product_id'][$key];
				$thisData['z_product_warehouse_warehouse_id'] = $thisPOST['exchange_warehouse_id_to'][$key];
				$thisData['z_product_warehouse_quantity'] = $thisWarehouseToQuantity + $thisPOST['exchange_quantity'][$key];
				$this->z_product_warehouse_model->update($thisData);

				$thisLog['log_permission_class'] = $this->router->fetch_class();
				$thisLog['log_permission_action'] = $this->router->fetch_method();
				$thisLog['log_record_id'] = $thisInsertId;
				set_log($thisLog);
			}

			// redirect($thisPOST['referrer']);
			redirect('exchange/select/exchange_type/in');
		}else{
			/* preset empty data */
			$thisArray = array();
			foreach($this->exchange_model->structure() as $key => $value){
				$thisArray[$value->Field] = '';
			}
			$data['exchange'] = (object)$thisArray;

			/* warehouse */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['warehousefroms'] = $this->warehouse_model->select($thisSelect);

			/* warehouse */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['warehousetos'] = $this->warehouse_model->select($thisSelect);

			$data['product_ids'] = explode('_', $this->uri->uri_to_assoc()['product_id']);
			$data['product_qtys'] = explode('_', $this->uri->uri_to_assoc()['product_qty']);

			$this->load->view('exchange_view', $data);
		}
	}

	public function select()
	{
		$per_page = get_setting('per_page')->setting_value;

		$thisSelect = array(
			'where' => $this->uri->uri_to_assoc(),
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['exchanges'] = $this->exchange_model->select($thisSelect);

		$thisSelect = array(
			'where' => $this->uri->uri_to_assoc(),
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->exchange_model->select($thisSelect);

		/* type */
		$data['types'] = (object)array(
			(object)array('type_name' => 'in'),
			(object)array('type_name' => 'transfer'),
			(object)array('type_name' => 'out')
		);

		$thisSelect = array(
			'group' => 'exchange_product_id',
			'return' => 'result'
		);
		$data['exchange_product_ids'] = $this->exchange_model->select($thisSelect);

		/* pagination */
		$this->pagination->initialize(get_pagination_config($per_page, $data['num_rows']));

		$this->load->view('exchange_view', $data);
	}

}
