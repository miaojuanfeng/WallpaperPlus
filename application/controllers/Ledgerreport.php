<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ledgerreport extends CI_Controller {

    private $th_header = array(
        'Customer',
        'SO No',
        'Date',
        'Deadline',
        'Debit Amt (HKD)',
        'Credit Amt (HKD)',
        'Balance',
        'Sales'
    );

    private $td_body = array();

    private $th_footer = array(
        '',
        '',
        '',
        '',
        'Pay (HKD)',
        'Receive (HKD)',
        '',
        ''
    );

    private $thisGET;
    private $per_page;

    private function make_form_data(&$data){
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

                $this->thisGET['salesorder_quotation_user_id_in'] = $data['user_ids'];
                break;
            case in_array('4', $this->session->userdata('role')): // sales
                /* get own client */
                $this->thisGET['salesorder_quotation_user_id'] = $this->session->userdata('user_id');
                break;
            default:
                break;
        }

        $thisSelect = array(
            'where' => $this->thisGET,
            'order' => 'salesorder_client_id',
            'ascend' => 'asc',
            'limit' => $this->per_page,
            'return' => 'result'
        );
        $data['salesorders'] = $this->salesorder_model->select($thisSelect);

        foreach($data['salesorders'] as $key => $value){
            /* purchaseorder */
            $thisSelect = array(
                'where' => array(
                    'purchaseorder_salesorder_id' => $value->salesorder_id,
                    'purchaseorder_status_noteq' => 'cancel'
                ),
                'return' => 'result'
            );
            $data['purchaseorders'] = $this->purchaseorder_model->select($thisSelect);
            $data['salesorders'][$key]->purchaseorders = $data['purchaseorders'];
            /* invoice */
            $thisSelect = array(
                'where' => array(
                    'invoice_salesorder_id' => $value->salesorder_id,
                    'invoice_status_noteq' => 'cancel'
                ),
                'return' => 'result'
            );
            $data['invoiceorders'] = $this->invoice_model->select($thisSelect);
            $data['salesorders'][$key]->invoiceorders = $data['invoiceorders'];
        }
    }

    private function get_form_data($rows){
        $data = array();

        $total = 0;
        $this->td_body = array();
        if( $rows && count($rows) ) {
            $purchase_total = 0;
            $invoice_total = 0;
            foreach ($rows as $key => $value) {
                $row = array();
                $row[] = $value->salesorder_client_company_name;
                $row[] = '<a href="' . base_url('salesorder/update/salesorder_id/' . $value->salesorder_id) . '">' . $value->salesorder_number . '</a>';
                $row[] = $value->salesorder_issue;
                $row[] = $value->salesorder_expire;
                $purchaseorder_total = 0;
                foreach ($value->purchaseorders as $k => $v){
                    $purchaseorder_total += $v->purchaseorder_total*$v->purchaseorder_vendor_exchange_rate;
                }
                $purchase_total += $purchaseorder_total;
                $row[] = 'HKD ' . money_format('%!n', $purchaseorder_total);
                $invoiceorder_total = 0;
                foreach ($value->invoiceorders as $k => $v){
                    $invoiceorder_total += $v->invoice_total*$v->invoice_exchange_rate;
                }
                $invoice_total += $invoiceorder_total;
                $row[] = 'HKD ' . money_format('%!n', $invoiceorder_total);
                $row[] = 'HKD ' . money_format('%!n', $invoiceorder_total - $purchaseorder_total);
                $row[] = get_user($value->salesorder_quotation_user_id)->user_name;
                $this->td_body[] = $row;
            }
            $this->th_footer[4] = 'HKD '.money_format('%!n', $purchase_total);
            $this->th_footer[5] = 'HKD '.money_format('%!n', $invoice_total);
            $this->th_footer[6] = 'HKD '.money_format('%!n', $invoice_total - $purchase_total);
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
        $this->load->model('salesorder_model');
		$this->load->model('invoice_model');
        $this->load->model('purchaseorder_model');
		$this->load->model('user_model');

        $this->per_page = get_setting('per_page')->setting_value;
        $this->thisGET = $this->uri->uri_to_assoc();
        $this->thisGET['invoice_status'] = 'complete';
        $this->thisGET['invoice_deleted'] = 'N';
	}

	public function index()
	{
		redirect('ledgerreport/select');
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
        $this->make_form_data($data);
        $data = array_merge($data, $this->get_form_data($data['salesorders']));

		$thisSelect = array(
			'where' => $this->thisGET,
			'group' => 'invoice_number',
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->invoice_model->select($thisSelect);

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

		$this->load->view('ledgerreport_view', $data);
	}

    public function export()
    {
        $this->make_form_data($data);
        $this->get_form_data($data['salesorders']);

        $fileName = 'General_ledger_report_'.date('YmdHis');
        php_excel_export($this->th_header, $this->td_body, $fileName);
    }

}
