<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warehouse extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        check_session_timeout();
        check_is_login();
        convert_get_slashes_pretty_link();
        check_permission();

        $this->load->model('warehouse_model');
        $this->load->model('product_model');
        $this->load->model('z_product_warehouse_model');
    }

    public function index()
    {
        redirect('warehouse/select');
    }

    public function update()
    {
        if($this->input->post()){
            $thisPOST = $this->input->post();
            $this->warehouse_model->update($thisPOST);

            $thisLog['log_permission_class'] = $this->router->fetch_class();
            $thisLog['log_permission_action'] = $this->router->fetch_method();
            $thisLog['log_record_id'] = $thisPOST['warehouse_id'];
            set_log($thisLog);

            redirect($thisPOST['referrer']);
        }else{
            /* warehouse */
            $thisSelect = array(
                'where' => $this->uri->uri_to_assoc(),
                'return' => 'row'
            );
            $data['warehouse'] = $this->warehouse_model->select($thisSelect);

            $this->load->view('warehouse_view', $data);
        }
    }

    public function delete()
    {
        $thisPOST = $this->input->post();
        $this->warehouse_model->delete($thisPOST);

        $thisLog['log_permission_class'] = $this->router->fetch_class();
        $thisLog['log_permission_action'] = $this->router->fetch_method();
        $thisLog['log_record_id'] = $thisPOST['warehouse_id'];
        set_log($thisLog);

        redirect($this->agent->referrer());
    }

    public function insert()
    {
        if($this->input->post()){
            $thisPOST = $this->input->post();
            $thisInsertId = $this->warehouse_model->insert($thisPOST);

            $thisLog['log_permission_class'] = $this->router->fetch_class();
            $thisLog['log_permission_action'] = $this->router->fetch_method();
            $thisLog['log_record_id'] = $thisPOST['warehouse_id'];
            set_log($thisLog);

            redirect($thisPOST['referrer']);
        }else{
            /* preset empty data */
            $thisArray = array();
            foreach($this->warehouse_model->structure() as $key => $value){
                $thisArray[$value->Field] = '';
            }
            $data['warehouse'] = (object)$thisArray;

            $this->load->view('warehouse_view', $data);
        }
    }

    public function select()
    {
        $per_page = get_setting('per_page')->setting_value;

        $thisCheck = $thisGET = $this->uri->uri_to_assoc();
        unset($thisCheck['page']);
        if(empty($thisCheck)){
            $thisGET['z_product_warehouse_quantity_noteq'] = 0;
        }

        $thisSelect = array(
            'where' => $thisGET,
            'group' => 'product_id',
            'limit' => $per_page,
            'return' => 'result'
        );
        $data['products'] = $this->z_product_warehouse_model->select($thisSelect);

        $thisSelect = array(
            'where' => $thisGET,
            'group' => 'product_id',
            'return' => 'num_rows'
        );
        $data['num_rows'] = $this->z_product_warehouse_model->select($thisSelect);

        $thisSelect = array(
            'return' => 'result'
        );
        $data['warehouses'] = $this->warehouse_model->select($thisSelect);

        /* status */
        $data['statuss'] = (object)array(
            (object)array('status_name' => 'all')
        );

        /* pagination */
        $this->pagination->initialize(get_pagination_config($per_page, $data['num_rows']));

        $this->load->view('warehouse_view', $data);
    }

}