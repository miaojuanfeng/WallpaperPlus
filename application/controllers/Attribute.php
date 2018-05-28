<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attribute extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		check_session_timeout();
		check_is_login();
		convert_get_slashes_pretty_link();
		check_permission();

		 $this->load->model('attribute_model');
	}

    public function index()
    {
        redirect('attribute/select');
    }

    public function update()
    {
        if($this->input->post()){
            $thisPOST = $this->input->post();
            $this->attribute_model->update($thisPOST);

            $thisLog['log_permission_class'] = $this->router->fetch_class();
            $thisLog['log_permission_action'] = $this->router->fetch_method();
            $thisLog['log_record_id'] = $thisPOST['attribute_id'];
            set_log($thisLog);

            redirect($thisPOST['referrer']);
        }else{
            /* attribute */
            $thisSelect = array(
                'where' => $this->uri->uri_to_assoc(),
                'return' => 'row'
            );
            $data['attribute'] = $this->attribute_model->select($thisSelect);

            /* type */
            $data['types'] = (object)array(
                (object)array('type_name' => 'color'),
                (object)array('type_name' => 'style'),
                (object)array('type_name' => 'usage'),
                (object)array('type_name' => 'material'),
                (object)array('type_name' => 'keyword'),
                (object)array('type_name' => 'size')
            );

            $this->load->view('attribute_view', $data);
        }
    }

    public function delete()
    {
        $thisPOST = $this->input->post();
        $this->attribute_model->delete($thisPOST);

        $thisLog['log_permission_class'] = $this->router->fetch_class();
        $thisLog['log_permission_action'] = $this->router->fetch_method();
        $thisLog['log_record_id'] = $thisPOST['attribute_id'];
        set_log($thisLog);

        redirect($this->agent->referrer());
    }

    public function insert()
    {
        if($this->input->post()){
            $thisPOST = $this->input->post();
            $thisInsertId = $this->attribute_model->insert($thisPOST);

            $thisLog['log_permission_class'] = $this->router->fetch_class();
            $thisLog['log_permission_action'] = $this->router->fetch_method();
            $thisLog['log_record_id'] = $thisPOST['attribute_id'];
            set_log($thisLog);

            redirect($thisPOST['referrer']);
        }else{
            /* preset empty data */
            $thisArray = array();
            foreach($this->attribute_model->structure() as $key => $value){
                $thisArray[$value->Field] = '';
            }
            $data['attribute'] = (object)$thisArray;

            /* type */
            $data['types'] = (object)array(
                (object)array('type_name' => 'color'),
                (object)array('type_name' => 'style'),
                (object)array('type_name' => 'usage'),
                (object)array('type_name' => 'material'),
                (object)array('type_name' => 'keyword'),
                (object)array('type_name' => 'size')
            );

            $this->load->view('attribute_view', $data);
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
        $data['attributes'] = $this->attribute_model->select($thisSelect);

        $thisSelect = array(
            'where' => $this->uri->uri_to_assoc(),
            'return' => 'num_rows'
        );
        $data['num_rows'] = $this->attribute_model->select($thisSelect);

        /* pagination */
        $this->pagination->initialize(get_pagination_config($per_page, $data['num_rows']));

        $this->load->view('attribute_view', $data);
    }

}
