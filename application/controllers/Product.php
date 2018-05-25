<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		check_session_timeout();
		check_is_login();
		convert_get_slashes_pretty_link();
		check_permission();

		$this->load->model('product_model');
		$this->load->model('brand_model');
		$this->load->model('category_model');
        $this->load->model('vendor_model');
//		$this->load->model('type_model');
		$this->load->model('unit_model');
        $this->load->model('team_model');
        $this->load->model('attribute_model');
        $this->load->model('z_product_attribute_model');
	}

	public function index()
	{
		redirect('product/select');
	}

	public function update()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$this->product_model->update($thisPOST);
            $this->z_product_attribute_model->delete($thisPOST);
            $this->z_product_attribute_model->insert($thisPOST);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['product_id'];
			set_log($thisLog);

			redirect($thisPOST['referrer']);
		}else{
			/* product */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'row'
			);
			$data['product'] = $this->product_model->select($thisSelect);
			
//			/* products */
//			$thisSelect = array(
//				'where' => array(
//					'product_code_noteq' => $data['product']->product_code
//				),
//				'return' => 'result'
//			);
//			$data['products'] = $this->product_model->select($thisSelect);

            /* z_product_attribute */
            $thisSelect = array(
                'where' => $this->uri->uri_to_assoc(),
                'return' => 'result'
            );
            $data['z_product_attribute_ids'] = convert_object_to_array($this->z_product_attribute_model->select($thisSelect), 'z_product_attribute_attribute_id');

            /* vendor */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['vendors'] = $this->vendor_model->select($thisSelect);

            /* brand */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['brands'] = $this->brand_model->select($thisSelect);

            /* category */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['categorys'] = $this->category_model->select($thisSelect);

			/* unit */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['units'] = $this->unit_model->select($thisSelect);

            /* team */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['teams'] = $this->team_model->select($thisSelect);

			/* vendor */
			// $thisSelect = array(
			// 	'return' => 'result'
			// );
			// $data['vendors'] = $this->vendor_model->select($thisSelect);

//			/* type */
//			$thisSelect = array(
//				'return' => 'result'
//			);
//			$data['types'] = $this->type_model->select($thisSelect);

            /* size */
            $thisSelect = array(
                'where' => array('attribute_type' => 'size'),
                'return' => 'result'
            );
            $data['sizes'] = $this->attribute_model->select($thisSelect);

            /* color */
            $thisSelect = array(
                'where' => array('attribute_type' => 'color'),
                'return' => 'result'
            );
            $data['colors'] = $this->attribute_model->select($thisSelect);

            /* style */
            $thisSelect = array(
                'where' => array('attribute_type' => 'style'),
                'return' => 'result'
            );
            $data['styles'] = $this->attribute_model->select($thisSelect);

            /* usage */
            $thisSelect = array(
                'where' => array('attribute_type' => 'usage'),
                'return' => 'result'
            );
            $data['usages'] = $this->attribute_model->select($thisSelect);

            /* material */
            $thisSelect = array(
                'where' => array('attribute_type' => 'material'),
                'return' => 'result'
            );
            $data['materials'] = $this->attribute_model->select($thisSelect);

            /* keyword */
            $thisSelect = array(
                'where' => array('attribute_type' => 'keyword'),
                'return' => 'result'
            );
            $data['keywords'] = $this->attribute_model->select($thisSelect);

			$this->load->view('product_view', $data);
		}
	}

	public function delete()
	{
		$thisPOST = $this->input->post();
		$this->product_model->delete($thisPOST);

		$thisLog['log_permission_class'] = $this->router->fetch_class();
		$thisLog['log_permission_action'] = $this->router->fetch_method();
		$thisLog['log_record_id'] = $thisPOST['product_id'];
		set_log($thisLog);

		redirect($this->agent->referrer());
	}

	public function insert()
	{
		if($this->input->post()){
			$thisPOST = $this->input->post();
			$thisInsertId = $this->product_model->insert($thisPOST);
            $thisPOST['product_id'] = $thisInsertId;
            $this->z_product_attribute_model->insert($thisPOST);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['product_id'];
			set_log($thisLog);

			redirect($thisPOST['referrer']);
		}else{
			/* preset empty data */
			$thisArray = array();
			foreach($this->product_model->structure() as $key => $value){
				$thisArray[$value->Field] = '';
			}
			$data['product'] = (object)$thisArray;
			
//			/* products */
//			$thisSelect = array(
//				'where' => array(
//					'product_code_noteq' => $data['product']->product_code
//				),
//				'return' => 'result'
//			);
//			$data['products'] = $this->product_model->select($thisSelect);

            /* vendor */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['vendors'] = $this->vendor_model->select($thisSelect);

            /* brand */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['brands'] = $this->brand_model->select($thisSelect);

            /* category */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['categorys'] = $this->category_model->select($thisSelect);

			/* unit */
			$thisSelect = array(
				'return' => 'result'
			);
			$data['units'] = $this->unit_model->select($thisSelect);

            /* team */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['teams'] = $this->team_model->select($thisSelect);

			/* vendor */
			// $thisSelect = array(
			// 	'return' => 'result'
			// );
			// $data['vendors'] = $this->vendor_model->select($thisSelect);

//			/* type */
//			$thisSelect = array(
//				'return' => 'result'
//			);
//			$data['types'] = $this->type_model->select($thisSelect);

            /* size */
            $thisSelect = array(
                'where' => array('attribute_type' => 'size'),
                'return' => 'result'
            );
            $data['sizes'] = $this->attribute_model->select($thisSelect);

            /* color */
            $thisSelect = array(
                'where' => array('attribute_type' => 'color'),
                'return' => 'result'
            );
            $data['colors'] = $this->attribute_model->select($thisSelect);

            /* style */
            $thisSelect = array(
                'where' => array('attribute_type' => 'style'),
                'return' => 'result'
            );
            $data['styles'] = $this->attribute_model->select($thisSelect);

            /* usage */
            $thisSelect = array(
                'where' => array('attribute_type' => 'usage'),
                'return' => 'result'
            );
            $data['usages'] = $this->attribute_model->select($thisSelect);

            /* material */
            $thisSelect = array(
                'where' => array('attribute_type' => 'material'),
                'return' => 'result'
            );
            $data['materials'] = $this->attribute_model->select($thisSelect);

            /* keyword */
            $thisSelect = array(
                'where' => array('attribute_type' => 'keyword'),
                'return' => 'result'
            );
            $data['keywords'] = $this->attribute_model->select($thisSelect);

			$this->load->view('product_view', $data);
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
		$data['products'] = $this->product_model->select($thisSelect);

		$thisSelect = array(
			'where' => $this->uri->uri_to_assoc(),
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->product_model->select($thisSelect);

		/* pagination */
		$this->pagination->initialize(get_pagination_config($per_page, $data['num_rows']));

		$this->load->view('product_view', $data);
	}

}
