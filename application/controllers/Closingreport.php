<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Closingreport extends CI_Controller {

    private $th_header = array(
        'DN No',
        'SO No',
        'Item ID',
        'Item Name',
        'Quantity',
        'Customer',
        'Project',
        'Sales',
        'Status',
        'Create',
    );

    private $td_body = array();

    private $th_footer = array(
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        ''
    );

    private $thisGET;
    private $per_page;

	public function __construct()
	{
		parent::__construct();
		// session_write_close();
		// session_id($this->input->post()['thisSession']);
		// session_start();
		check_session_timeout();
		check_is_login();
		convert_get_slashes_pretty_link();
		check_permission();

        $this->load->library('PHPExcel');
		$this->load->model('salesorder_model');
		$this->load->model('deliverynote_model');
        $this->load->model('deliverynoteitem_model');
		$this->load->model('user_model');

        $this->per_page = get_setting('per_page')->setting_value;
        $this->thisGET = $this->uri->uri_to_assoc();
        $this->thisGET['deliverynote_status_like'] = 'processing';
        $this->thisGET['deliverynote_deleted'] = 'N';
	}

	private function make_form_data(&$data){
        $thisSelect = array(
            'where' => $this->thisGET,
            'group' => 'deliverynote_number',
            'limit' => $this->per_page,
            'return' => 'result'
        );
        $data['deliverynotes'] = $this->deliverynote_model->select($thisSelect);

        foreach($data['deliverynotes'] as $key => $value){
            /* deliverynotesitem */
            $thisSelect = array(
                'where' => array(
                    'deliverynoteitem_deliverynote_id' => $value->deliverynote_id
                ),
                'return' => 'result'
            );
            $data['deliverynoteitems'] = $this->deliverynoteitem_model->select($thisSelect);
            $data['deliverynotes'][$key]->deliverynoteitems = $data['deliverynoteitems'];
        }
    }

    private function get_form_data($rows){
        $data = array();

        $total = 0;
        $this->td_body = array();
        if( $rows && count($rows) ) {
            $total = 0;
            $subtotal = 0;
            $commission_subtotal = 0;
            foreach ($rows as $key => $value) {
                $row = array();
                $row[] = '<a href="' . base_url('deliverynote/update/deliverynote_id/' . $value->deliverynote_id) . '">' . $value->deliverynote_number . '</a>';
                $row[] = '<a href="' . base_url('salesorder/update/salesorder_id/' . $value->deliverynote_salesorder_id) . '">' . get_salesorder($value->deliverynote_salesorder_id)->salesorder_number . '</a>';
                $temp = '';
                foreach($value->deliverynoteitems as $key1 => $value1){
                    $temp .= '<div class="no-wrap">'.$value1->deliverynoteitem_product_code.'<br/></div>';
                }
                $row[] = $temp;
                $temp = '';
                foreach($value->deliverynoteitems as $key1 => $value1){
                    $temp .= '<div class="no-wrap">'.$value1->deliverynoteitem_product_name.'<br/></div>';
                }
                $row[] = $temp;
                $temp = '';
                foreach($value->deliverynoteitems as $key1 => $value1){
                    $temp .= '<div class="no-wrap">'.$value1->deliverynoteitem_quantity.'<br/></div>';
                }
                $row[] = $temp;
                $row[] = $value->deliverynote_client_company_name;
                $row[] = $value->deliverynote_project_name;
                $row[] = ucfirst(get_user($value->deliverynote_user_id)->user_name);
                $row[] = ucfirst($value->deliverynote_status);
                $row[] = convert_datetime_to_date($value->deliverynote_create);
                $this->td_body[] = $row;
            }
        }
        $data['th_header'] = $this->th_header;
        $data['td_body'] = $this->td_body;
        $data['th_footer'] = $this->th_footer;

        return $data;
    }

	public function index()
	{
		redirect('closingreport/select');
	}

	public function update()
	{
		if($this->input->post()){
            $thisPOST['deliverynote_confirmed_date'] = Date('Y-m-d');
			$thisPOST = $this->input->post();

			/* set delivery note status */
            set_delivery_note_status_complete($thisPOST['deliverynote_id']);

			$thisLog['log_permission_class'] = $this->router->fetch_class();
			$thisLog['log_permission_action'] = $this->router->fetch_method();
			$thisLog['log_record_id'] = $thisPOST['deliverynote_id'];
			set_log($thisLog);

			$thisAlert = 'Data saved';
			$this->session->set_tempdata('alert', '<div class="btn btn-sm btn-primary btn-block bottom-buffer-10">'.$thisAlert.'</div>', 0);
			redirect('closingreport/select/deliverynote_status/processing');
		}else{
			/* invoice */
			$thisSelect = array(
				'where' => $this->uri->uri_to_assoc(),
				'return' => 'row'
			);
			$data['invoice'] = $this->invoice_model->select($thisSelect);

			$this->load->view('closingreport_view', $data);
		}
	}

	public function delete()
	{
		// delete here
	}

	public function insert()
	{
		// insert here
	}

	public function select()
	{
//		/* check salesorder */
//		if(isset($this->thisGET['salesorder_number_like'])){
//			$thisSelect = array(
//				'where' => $this->thisGET,
//				'return' => 'result'
//			);
//			$data['salesorders'] = $this->salesorder_model->select($thisSelect);
//
//			if($data['salesorders']){
//				foreach($data['salesorders'] as $key => $value){
//					$this->thisGET['invoice_salesorder_id_in'][] = $value->salesorder_id;
//				}
//			}else{
//				$this->thisGET['invoice_salesorder_id_in'] = array(0);
//			}
//		}
//		/* check salesorder */
		
        $this->make_form_data($data);
        $data = array_merge($data, $this->get_form_data($data['deliverynotes']));

		$thisSelect = array(
			'where' => $this->thisGET,
			'group' => 'deliverynote_number',
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->deliverynote_model->select($thisSelect);

		/* status */
		$data['statuss'] = (object)array(
			(object)array('status_name' => 'processing'),
			(object)array('status_name' => 'complete')
		);

		/* user */
		$thisSelect = array(
			'return' => 'result'
		);
		$data['users'] = $this->user_model->select($thisSelect);

		/* pagination */
		$this->pagination->initialize(get_pagination_config($this->per_page, $data['num_rows']));

		$this->load->view('closingreport_view', $data);
	}

    public function export()
    {
        $this->make_form_data($data);
        $this->get_form_data($data['deliverynotes']);

        $fileName = 'Closing_stock_report_'.date('YmdHis');
        php_excel_export($this->th_header, $this->td_body, $fileName);
    }

}
