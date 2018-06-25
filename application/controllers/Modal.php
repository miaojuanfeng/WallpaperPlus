<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Modal extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// check_session_timeout();
		// check_is_login();
		// check_permission();
	}

	public function index()
	{
		$this->{$this->input->post('thisTableId')}();
	}

	private function getData(){
        $thisData = array();
        if( !empty($this->input->post('thisGet')) ){
            $t = explode('/', $this->input->post('thisGet'));
            $k = '';
            foreach ($t as $key => $value) {
                if(!($key%2)){
                    $k = $value;
                }else{
                    $thisData[$k] = $value;
                }
            }
        }
        if( !empty($this->input->post('thisPost')) ){
            foreach ($this->input->post('thisPost') as $key => $value) {
                $thisData[$key] = $value;
            }
        }
        return $thisData;
    }

	public function product_select()
	{
		$this->load->model('product_model');
        $this->load->model('vendor_model');
			
		$per_page = get_setting('per_page')->setting_value;

		$thisGet = $this->getData();
		
        /* check vendor */
        if( isset($thisGet['vendor_company_code_like']) || isset($thisGet['vendor_company_name_like']) ){
            $thisSelect = array(
                'where' => $thisGet,
                'return' => 'result'
            );
            $data['vendors'] = $this->vendor_model->select($thisSelect);

            if($data['vendors']){
                foreach($data['vendors'] as $key => $value){
                    $thisGet['product_vendor_id_in'][] = $value->vendor_id;
                }
            }else{
                $thisGet['product_vendor_id_in'] = array(0);
            }
        }
        /* check vendor */

//        var_dump($thisGet);

		$thisSelect = array(
			'where' => $thisGet,
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['products'] = $this->product_model->select($thisSelect);

		$thisSelect = array(
			'where' => $thisGet,
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->product_model->select($thisSelect);

		/* pagination */
		$data['pagination'] = get_pagination_js_config($thisGet, $per_page, $data['num_rows']);

		echo $this->load->view('modal/product_view', $data, true);
	}

    public function product_update()
    {
        $this->load->model('product_model');
        $this->load->model('vendor_model');
        $this->load->model('brand_model');
        $this->load->model('unit_model');
        $this->load->model('team_model');
        $this->load->model('team_model');
        $this->load->model('type_model');

        $thisPOST = $this->getData();

        if( isset($thisPOST['action']) && $thisPOST['action'] == 'update' ){
            $this->product_model->update($thisPOST);

            $thisLog['log_permission_class'] = $this->router->fetch_class();
            $thisLog['log_permission_action'] = $this->router->fetch_method();
            $thisLog['log_record_id'] = $thisPOST['product_id'];
            set_log($thisLog);
        }else{
            /* product */
            $thisSelect = array(
                'where' => $thisPOST,
                'return' => 'row'
            );
            $data['product'] = $this->product_model->select($thisSelect);

            /* products */
            $thisSelect = array(
                'where' => array(
                    'product_code_noteq' => $data['product']->product_code
                ),
                'return' => 'result'
            );
            $data['products'] = $this->product_model->select($thisSelect);

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

            /* type */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['types'] = $this->type_model->select($thisSelect);

            echo $this->load->view('modal/product_insert_update_view', $data, true);
        }
    }

    public function product_insert()
    {
        $this->load->model('product_model');
        $this->load->model('vendor_model');
        $this->load->model('brand_model');
        $this->load->model('unit_model');
        $this->load->model('team_model');
        $this->load->model('team_model');
        $this->load->model('type_model');

        $thisPOST = $this->getData();

        if( isset($thisPOST['action']) && $thisPOST['action'] == 'insert' ){
            $thisInsertId = $this->product_model->insert($thisPOST);

            $thisLog['log_permission_class'] = $this->router->fetch_class();
            $thisLog['log_permission_action'] = $this->router->fetch_method();
            $thisLog['log_record_id'] = $thisPOST['product_id'];
            set_log($thisLog);
        }else{
            /* preset empty data */
            $thisArray = array();
            foreach($this->product_model->structure() as $key => $value){
                $thisArray[$value->Field] = '';
            }
            $data['product'] = (object)$thisArray;

            /* products */
            $thisSelect = array(
                'where' => array(
                    'product_code_noteq' => $data['product']->product_code
                ),
                'return' => 'result'
            );
            $data['products'] = $this->product_model->select($thisSelect);

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

            /* type */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['types'] = $this->type_model->select($thisSelect);

            echo $this->load->view('modal/product_insert_update_view', $data, true);
        }
    }

}
