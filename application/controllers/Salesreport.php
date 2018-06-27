<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salesreport extends CI_Controller {

    private $th_header = array(
        'Customer',
        'Customer PO ? PO No',
        'SO No',
        'Total',
        'IN No',
        'IN date',
        'Sales',
        'IN total',
        'Rate',
        'IN total (HKD)'
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
        'IN total (HKD)'
    );

    private $thisGET;
    private $per_page;

    private function get_form_data($rows){
        $data = array();

        $total = 0;
        $this->td_body = array();
        if( $rows && count($rows) ) {
            $total = 0;
            $subtotal = 0;
            foreach ($rows as $key => $value) {
                $row = array();
                $row[] = $value->salesorder_client_company_name;
                $temp = '';
                foreach($value->purchaseorders as $key1 => $value1){
                    $temp .= '<div class="no-wrap">Customer PO ? <a href="'.base_url('purchaseorder/update/purchaseorder_id/'.$value1->purchaseorder_id).'">'.$value1->purchaseorder_number.'</a><br/></div>';
                }
                $row[] = $temp;
                $row[] = '<a href="' . base_url('salesorder/update/salesorder_id/' . $value->salesorder_id) . '">' . $value->salesorder_number . '</a>';
                $row[] = strtoupper($value->salesorder_currency).' '.money_format('%!n', $value->salesorder_total);
                $temp = '';
                foreach($value->invoices as $key1 => $value1){
                    $temp .= '<div class="no-wrap">'.$value1->invoice_number.'<br/></div>';
                }
                $row[] = $temp;
                $temp = '';
                foreach($value->invoices as $key1 => $value1){
                    $temp .= '<div class="no-wrap">'.convert_datetime_to_date($value1->invoice_create).'<br/></div>';
                }
                $row[] = $temp;
                $temp = '';
                foreach($value->invoices as $key1 => $value1){
                    $temp .= '<div class="no-wrap">'.ucfirst(get_user($value1->invoice_quotation_user_id)->user_name).'<br/></div>';
                }
                $row[] = $temp;
                $temp = '';
                foreach($value->invoices as $key1 => $value1){
                    $temp .= '<div class="no-wrap">'.strtoupper($value1->invoice_currency).' '.money_format('%!n', $value1->invoice_pay).'<br/></div>';
                }
                $row[] = $temp;
                $temp = '';
                foreach($value->invoices as $key1 => $value1){
                    $temp .= '<div class="no-wrap">'.$value1->invoice_exchange_rate.'<br/></div>';
                }
                $row[] = $temp;
                $temp = '';
                foreach($value->invoices as $key1 => $value1){
                    $temp .= '<div class="no-wrap">'.'HKD '.money_format('%!n', $value1->invoice_pay * $value1->invoice_exchange_rate).'<br/></div>';
                    $subtotal += $value1->invoice_pay * $value1->invoice_exchange_rate;
                }
                $row[] = $temp;
                $this->td_body[] = $row;
            }
            $this->th_footer[9] = 'HKD '.money_format('%!n', $subtotal);
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
		$this->load->model('client_model');
		$this->load->model('purchaseorder_model');
		$this->load->model('invoice_model');
		$this->load->model('user_model');

        $this->per_page = get_setting('per_page')->setting_value;
        $this->thisGET = $this->uri->uri_to_assoc();
        $this->thisGET['salesorder_status_noteq'] = 'cancel';
        $this->thisGET['salesorder_deleted'] = 'N';
	}

	public function index()
	{
		redirect('salesreport/select');
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
		/* check invoice */
		if(isset($this->thisGET['invoice_number_like']) || isset($this->thisGET['invoice_create_greateq']) || isset($this->thisGET['invoice_create_smalleq'])){
			$thisSelect = array(
				'where' => $this->thisGET,
				'return' => 'row'
			);
			$data['invoice'] = $this->invoice_model->select($thisSelect);

			if($data['invoice']){
				$this->thisGET['salesorder_id'] = $data['invoice']->invoice_salesorder_id;
			}else{
				$this->thisGET['salesorder_id'] = 0;
			}
		}
		/* check invoice */

		$thisSelect = array(
			'where' => $this->thisGET,
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
		}

		foreach($data['salesorders'] as $key => $value){
			/* invoice */
			$thisSelect = array(
				'where' => array(
					'invoice_salesorder_id' => $value->salesorder_id,
					'invoice_status_noteq' => 'cancel'
				),
				'return' => 'result'
			);
			$data['invoices'] = $this->invoice_model->select($thisSelect);
			$data['salesorders'][$key]->invoices = $data['invoices'];
		}

        $data = array_merge($data, $this->get_form_data($data['salesorders']));

		$thisSelect = array(
			'where' => $this->thisGET,
			'group' => 'salesorder_number',
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->salesorder_model->select($thisSelect);

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

		$this->load->view('salesreport_view', $data);
	}

    public function export()
    {
        $thisSelect = array(
            'where' => $this->thisGET,
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
        }

        foreach($data['salesorders'] as $key => $value){
            /* invoice */
            $thisSelect = array(
                'where' => array(
                    'invoice_salesorder_id' => $value->salesorder_id,
                    'invoice_status_noteq' => 'cancel'
                ),
                'return' => 'result'
            );
            $data['invoices'] = $this->invoice_model->select($thisSelect);
            $data['salesorders'][$key]->invoices = $data['invoices'];
        }

        $this->get_form_data($data['salesorders']);

        $fileName = 'Sales_report_'.date('YmdHis');
        php_excel_export($this->th_header, $this->td_body, $fileName);
    }

}
