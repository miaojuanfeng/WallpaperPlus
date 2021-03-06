<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Incomereport extends CI_Controller {

    private $th_header = array(
        'Customer ID',
        'Customer',
        'IN No',
        'Pay',
        'Rate',
        'Pay (HKD)',
        'IN date',
        'Confirm',
        'Sales'
    );

    private $td_body = array();

    private $th_footer = array(
        '',
        '',
        '',
        '',
        '',
        'Pay (HKD)',
        '',
        '',
        ''
    );

    private $thisGET;
    private $per_page;

    private function make_form_data(&$data, $isExport){
    	/* client */
		switch(true){
			case in_array('3', $this->session->userdata('role')): // sales manager
				/* get own & downline client */
				$thisSelect = array(
					'where' => array(
						'OWN_USER_ID_AND_DOWNLINE_USER_ID' => $this->session->userdata('user_id')
					),
					'return' => 'result'
				);
				$data['user_ids'] = convert_object_to_array($this->user_model->select($thisSelect), 'user_id');

				$this->thisGET['invoice_quotation_user_id_in'] = $data['user_ids'];
				break;
			case in_array('4', $this->session->userdata('role')): // sales
				/* get own client */
				$this->thisGET['invoice_quotation_user_id'] = $this->session->userdata('user_id');
				break;
			default:
				break;
		}

		if( $isExport ){
			$thisSelect = array(
				'where' => $this->thisGET,
				'order' => 'invoice_client_id',
				'ascend' => 'asc',
				'return' => 'result'
			);
		}else{
			$thisSelect = array(
				'where' => $this->thisGET,
				'order' => 'invoice_client_id',
				'ascend' => 'asc',
				'limit' => $this->per_page,
				'return' => 'result'
			);
		}
		$data['invoices'] = $this->invoice_model->select($thisSelect);

		$thisSelect = array(
			'where' => $this->thisGET,
			'group' => 'invoice_number',
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->invoice_model->select($thisSelect);
    }

    private function get_form_data($rows){
        $data = array();

        $total = 0;
        $this->td_body = array();
        if( $rows && count($rows) ) {
            foreach ($rows as $key => $value) {
            	$thisUser = get_user($value->invoice_quotation_user_id);
                $row = array();
                $row[] = $value->invoice_client_company_code;
                $row[] = $value->invoice_client_company_name;
                $row[] = '<a href="' . base_url('invoice/select/invoice_id/' . $value->invoice_id) . '">' . $value->invoice_number . '</a>';
                $row[] = strtoupper($value->invoice_currency) . ' ' . money_format('%!n', $value->invoice_pay);
                $row[] = $value->invoice_exchange_rate;
                $total += $value->invoice_pay * $value->invoice_exchange_rate;
                $row[] = 'HKD ' . money_format('%!n', $value->invoice_pay * $value->invoice_exchange_rate);
                $row[] = convert_datetime_to_date($value->invoice_create);
                $row[] = convert_datetime_to_date($value->invoice_confirmed_date);
                $row[] = ($thisUser?$thisUser->user_name:'');
                $this->td_body[] = $row;
            }
            $this->th_footer[5] = strtoupper($value->invoice_currency).' '.money_format('%!n', $total);
        }
        $data['th_header'] = $this->th_header;
        $data['td_body'] = $this->td_body;
        $data['th_footer'] = $this->th_footer;

        return $data;
    }

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
		$this->load->model('invoice_model');
		$this->load->model('user_model');

        $this->per_page = get_setting('per_page')->setting_value;
        $this->thisGET = $this->uri->uri_to_assoc();
        $this->thisGET['invoice_status'] = 'complete';
        $this->thisGET['invoice_deleted'] = 'N';
	}

	public function index()
	{
		redirect('incomereport/select');
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
		// insert here
	}

	public function select()
	{
		$this->make_form_data($data, false);
		$data = array_merge($data, $this->get_form_data($data['invoices']));

		/* status */
		$data['statuss'] = (object)array(
			(object)array('status_name' => 'processing'),
			(object)array('status_name' => 'complete'),
			(object)array('status_name' => 'cancel')
		);

		/* user */
		$thisSelect = array(
			'return' => 'result'
		);
		$data['users'] = $this->user_model->select($thisSelect);

		/* pagination */
		$this->pagination->initialize(get_pagination_config($this->per_page, $data['num_rows']));

		$this->load->view('incomereport_view', $data);
	}

    public function export()
    {
        $this->make_form_data($data, true);
		$data = array_merge($data, $this->get_form_data($data['invoices']));

        $fileName = 'Income_report_'.date('YmdHis');
        php_excel_export($this->th_header, $this->td_body, $fileName);
    }

}
