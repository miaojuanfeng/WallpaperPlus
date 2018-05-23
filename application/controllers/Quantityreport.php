<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quantityreport extends CI_Controller {

    private $th_header = array(
        'IN No',
        'IN Qty',
        'PO No',
        'PO Qty',
        'Reason',
        'IN - PO'
    );

    private $td_body = array();

    private $th_footer = array(
        '',
        '',
        '',
        '',
        '',
        ''
    );

    private $thisGET;
    private $per_page;

    private function make_form_data(&$data){
        /* check invoice */
        if(isset($thisGET['invoice_number_like']) || isset($thisGET['invoice_create_greateq']) || isset($thisGET['invoice_create_smalleq'])){
            $thisSelect = array(
                'where' => $thisGET,
                'return' => 'row'
            );
            $data['invoice'] = $this->invoice_model->select($thisSelect);

            if($data['invoice']){
                $thisGET['salesorder_id'] = $data['invoice']->invoice_salesorder_id;
            }else{
                $thisGET['salesorder_id'] = 0;
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

            foreach($data['purchaseorders'] as $key1 => $value1){
                /* purchaseorderitems */
                $thisSelect = array(
                    'where' => array(
                        'purchaseorderitem_purchaseorder_id' => $value1->purchaseorder_id
                    ),
                    'return' => 'result'
                );
                $data['purchaseorderitems'] = $this->purchaseorderitem_model->select($thisSelect);
                $data['purchaseorders'][$key1]->purchaseorderitems = $data['purchaseorderitems'];
            }

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

            foreach($data['invoices'] as $key1 => $value1){
                /* invoiceitems */
                $thisSelect = array(
                    'where' => array(
                        'invoiceitem_invoice_id' => $value1->invoice_id
                    ),
                    'return' => 'result'
                );
                $data['invoiceitems'] = $this->invoiceitem_model->select($thisSelect);
                $data['invoices'][$key1]->invoiceitems = $data['invoiceitems'];
            }

            $data['salesorders'][$key]->invoices = $data['invoices'];
        }
    }

    private function get_form_data($rows){
        $data = array();

        $total = 0;
        $this->td_body = array();
        if( $rows && count($rows) ) {
            $total = 0;
            $subtotal = 0;
            foreach ($rows as $key => $value) {
                $row = array();
                $temp = '';
                foreach($value->invoices as $key1 => $value1){
                    $temp .= '<div class="no-wrap"><a href="' . base_url('invoice/update/invoice_id/' . $value1->invoice_id) . '">' . $value1->invoice_number . '</a><br/></div>';
                }
                $row[] = $temp;

                $temp = '';
                $invoiceitems_qty_total = 0;
                foreach($value->invoices as $key1 => $value1){
                    $invoiceitems_qty = 0;
                    foreach ($value1->invoiceitems as $key2 => $value2){
                        $invoiceitems_qty += $value2->invoiceitem_quantity;
                        $invoiceitems_qty_total += $value2->invoiceitem_quantity;
                    }
                    $temp .= '<div class="no-wrap">'.$invoiceitems_qty.'<br/></div>';
                }
                $row[] = $temp;

                $temp = '';
                foreach($value->purchaseorders as $key1 => $value1){
                    $temp .= '<div class="no-wrap"><a href="'.base_url('purchaseorder/update/purchaseorder_id/'.$value1->purchaseorder_id).'">'.$value1->purchaseorder_number.'</a><br/></div>';
                }
                $row[] = $temp;

                $temp = '';
                $purchaseorderitems_qty_total = 0;
                foreach($value->purchaseorders as $key1 => $value1){
                    $purchaseorderitems_qty = 0;
                    foreach ($value1->purchaseorderitems as $key2 => $value2){
                        $purchaseorderitems_qty += $value2->purchaseorderitem_quantity;
                        $purchaseorderitems_qty_total += $value2->purchaseorderitem_quantity;
                    }
                    $temp .= '<div class="no-wrap">'.$purchaseorderitems_qty.'<br/></div>';
                }
                $row[] = $temp;

                $row[] = '';
                $row[] = $invoiceitems_qty_total - $purchaseorderitems_qty_total;

//                $row[] = strtoupper($value->salesorder_currency).' '.money_format('%!n', $value->salesorder_total);
//
//                $temp = '';
//                foreach($value->invoices as $key1 => $value1){
//                    $temp .= '<div class="no-wrap">'.convert_datetime_to_date($value1->invoice_create).'<br/></div>';
//                }
//                $row[] = $temp;
//                $temp = '';
//                foreach($value->invoices as $key1 => $value1){
//                    $temp .= '<div class="no-wrap">'.ucfirst(get_user($value1->invoice_quotation_user_id)->user_name).'<br/></div>';
//                }
//                $row[] = $temp;
//                $temp = '';
//                foreach($value->invoices as $key1 => $value1){
//                    $temp .= '<div class="no-wrap">'.strtoupper($value1->invoice_currency).' '.money_format('%!n', $value1->invoice_pay).'<br/></div>';
//                }
//                $row[] = $temp;
//                $temp = '';
//                foreach($value->invoices as $key1 => $value1){
//                    $temp .= '<div class="no-wrap">'.$value1->invoice_exchange_rate.'<br/></div>';
//                }
//                $row[] = $temp;
//                $temp = '';
//                foreach($value->invoices as $key1 => $value1){
//                    $temp .= '<div class="no-wrap">'.'HKD '.money_format('%!n', $value1->invoice_pay * $value1->invoice_exchange_rate).'<br/></div>';
//                    $subtotal += $value1->invoice_pay * $value1->invoice_exchange_rate;
//                }
//                $row[] = $temp;
                $this->td_body[] = $row;
            }
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
        $this->load->model('purchaseorderitem_model');
		$this->load->model('invoice_model');
        $this->load->model('invoiceitem_model');
		$this->load->model('user_model');

        $this->per_page = get_setting('per_page')->setting_value;
        $this->thisGET = $this->uri->uri_to_assoc();
        $this->thisGET['salesorder_status_noteq'] = 'cancel';
        $this->thisGET['salesorder_deleted'] = 'N';
	}

	public function index()
	{
		redirect('quantityreport/select');
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

		$this->load->view('quantityreport_view', $data);
	}

    public function export()
    {
        $this->make_form_data($data);
        $this->get_form_data($data['salesorders']);

        $fileName = 'Quantity_report_'.date('YmdHis');
        php_excel_export($this->th_header, $this->td_body, $fileName);
    }

}