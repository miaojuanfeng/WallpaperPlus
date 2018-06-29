<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ledgerreport extends CI_Controller {

    private $th_header = array(
        'Account Description',
        'Date',
        'Reference',
        'Trans Description',
        'Debit Amt (HKD)',
        'Credit Amt (HKD)',
        'Balance'
    );

    private $td_body = array();

    private $th_footer = array(
        '',
        '',
        '',
        '',
        'Debit Amt (HKD)',
        'Credit Amt (HKD)',
        ''
    );

    private $thisGET;
    private $per_page;

    private function make_form_data($type, &$data){
        $data['type'] = $type;
        if( $type == 'account_receivable' ){
            /* client */
            // switch(true){
            //     case in_array('3', $this->session->userdata('role')): // sales manager
            //         /* get own & downline client */
            //         $thisSelect = array(
            //             'where' => array(
            //                 'OWN_USER_ID_AND_DOWNLINE_USER_ID' => $this->session->userdata('user_id')
            //             ),
            //             'return' => 'result'
            //         );
            //         $data['user_ids'] = convert_object_to_array($this->user_model->select($thisSelect), 'user_id');

            //         $this->thisGET['invoice_quotation_user_id_in'] = $data['user_ids'];
            //         break;
            //     case in_array('4', $this->session->userdata('role')): // sales
            //         /* get own client */
            //         $this->thisGET['invoice_quotation_user_id'] = $this->session->userdata('user_id');
            //         break;
            //     default:
            //         break;
            // }

            $thisSelect = array(
                'where' => $this->thisGET,
                'return' => 'num_rows'
            );
            $data['num_rows'] = $this->invoice_model->account('invoice', $thisSelect);

            $thisSelect = array(
                'where' => $this->thisGET,
                // 'limit' => $this->per_page,
                'return' => 'result'
            );
            $invoices = $this->invoice_model->account('invoice', $thisSelect);

            $data['data'] = array();
            foreach($invoices as $key => $value){
                $data['data'][$key][] = 'Accounts Receivable';
                $data['data'][$key][] = $value->invoice_sort;
                if( $value->invoice_type == 'debit' ){
                    $data['data'][$key][] = $value->invoice_number;
                    $data['data'][$key][] = get_client($value->invoice_client_id)->client_company_name;
                    $data['data'][$key][] = money_format('%!n', $value->invoice_total);
                    $data['data'][$key][] = '-';
                }else{
                    if( strpos($value->invoice_number, 'ECINV') !== false ){
                        $rv = 'ECRV';
                    }else if( strpos($value->invoice_number, 'EINV') !== false ){
                        $rv = 'ERV';
                    }else if( strpos($value->invoice_number, 'CINV') !== false ){
                        $rv = 'CRV';
                    }else{
                        $rv = 'RV';
                    }
                    $data['data'][$key][] = $rv.substr($value->invoice_number, strlen($value->invoice_number)-7);
                    $data['data'][$key][] = get_client($value->invoice_client_id)->client_company_name.' - Invoice: '.$value->invoice_number;
                    $data['data'][$key][] = '-';
                    $data['data'][$key][] = money_format('%!n', $value->invoice_total*$value->invoice_exchange_rate);
                }
                $data['data'][$key][] = '-';
            }

            // foreach($data['invoices'] as $key => $value){
            //     /* purchaseorder */
            //     $thisSelect = array(
            //         'where' => array(
            //             'purchaseorder_invoice_id' => $value->invoice_id,
            //             'purchaseorder_status_noteq' => 'cancel'
            //         ),
            //         'return' => 'result'
            //     );
            //     $data['purchaseorders'] = $this->purchaseorder_model->select($thisSelect);
            //     $data['invoices'][$key]->purchaseorders = $data['purchaseorders'];
            //     /* invoice */
            //     $thisSelect = array(
            //         'where' => array(
            //             'invoice_invoice_id' => $value->invoice_id,
            //             'invoice_status_noteq' => 'cancel'
            //         ),
            //         'return' => 'result'
            //     );
            //     $data['invoiceorders'] = $this->invoice_model->select($thisSelect);
            //     $data['invoices'][$key]->invoiceorders = $data['invoiceorders'];
            // }
        }else if( $type == 'account_payable' ){
            $thisSelect = array(
                'where' => $this->thisGET,
                'return' => 'num_rows'
            );
            $data['num_rows'] = $this->invoice_model->account('purchaseorder', $thisSelect);

            $thisSelect = array(
                'where' => $this->thisGET,
                // 'limit' => $this->per_page,
                'return' => 'result'
            );
            $purchaseorders = $this->invoice_model->account('purchaseorder', $thisSelect);

            $data['data'] = array();
            foreach($purchaseorders as $key => $value){
                $data['data'][$key][] = 'Accounts Payable';
                $data['data'][$key][] = $value->purchaseorder_sort;
                if( $value->purchaseorder_type == 'debit' ){
                    if( strpos($value->purchaseorder_number, 'EPO') !== false ){
                        $rv = 'EPV';
                    }else{
                        $rv = 'PV';
                    }
                    $data['data'][$key][] = $rv.substr($value->purchaseorder_number, strlen($value->purchaseorder_number)-7);
                    $data['data'][$key][] = get_vendor($value->purchaseorder_vendor_id)->vendor_company_name.' - Invoice: '.$value->purchaseorder_number;
                    $data['data'][$key][] = money_format('%!n', $value->purchaseorder_total);
                    $data['data'][$key][] = '-';
                }else{
                    $data['data'][$key][] = $value->purchaseorder_number;
                    $data['data'][$key][] = get_vendor($value->purchaseorder_vendor_id)->vendor_company_name;
                    $data['data'][$key][] = '-';
                    $data['data'][$key][] = money_format('%!n', $value->purchaseorder_total*$value->purchaseorder_vendor_exchange_rate);
                }
                $data['data'][$key][] = '-';
            }
        }else if( $type == 'sales' ){
            $thisSelect = array(
                'where' => $this->thisGET,
                'return' => 'result'
            );
            $invoices = $this->invoice_model->select($thisSelect);
            if( $invoices ){
                $invoice_ids = convert_object_to_array($invoices, 'invoice_id');
            }else{
                $invoice_ids = array(0);
            }
            $this->thisGET['invoiceitem_invoice_id_in'] = $invoice_ids;

            $thisSelect = array(
                'where' => $this->thisGET,
                'return' => 'num_rows'
            );
            $data['num_rows'] = $this->invoiceitem_model->select($thisSelect);

            $thisSelect = array(
                'where' => $this->thisGET,
                'order_sort' => 'invoiceitem_category_id ASC, invoiceitem_invoice_id ASC',
                'ascend' => 'asc',
                // 'limit' => $this->per_page,
                'return' => 'result'
            );
            $invoiceitems = $this->invoiceitem_model->select($thisSelect);

            $data['data'] = array();
            foreach($invoiceitems as $key => $value){
                $invoice = get_invoice($value->invoiceitem_invoice_id);
                $data['data'][$key][] = 'Sales - '.get_category($value->invoiceitem_category_id)->category_name;
                $data['data'][$key][] = date('Y-m-d', strtotime($invoice->invoice_create));
                $data['data'][$key][] = $invoice->invoice_number;
                $data['data'][$key][] = get_client($invoice->invoice_client_id)->client_company_name.' - Item: '.$value->invoiceitem_product_code;
                $data['data'][$key][] = '-';
                $data['data'][$key][] = money_format('%!n', $value->invoiceitem_subtotal*$invoice->invoice_exchange_rate);
                $data['data'][$key][] = '-';
            }
        }else if( $type == 'cost' ){
            $this->thisGET['purchaseorder_status_noteq'] = 'cancel';
            $thisSelect = array(
                'where' => $this->thisGET,
                'return' => 'result'
            );
            $purchaseorders = $this->purchaseorder_model->select($thisSelect);
            if( $purchaseorders ){
                $purchaseorder_ids = convert_object_to_array($purchaseorders, 'purchaseorder_id');
            }else{
                $purchaseorder_ids = array(0);
            }
            $this->thisGET['purchaseorderitem_purchaseorder_id_in'] = $purchaseorder_ids;

            $thisSelect = array(
                'where' => $this->thisGET,
                'return' => 'num_rows'
            );
            $data['num_rows'] = $this->purchaseorderitem_model->select($thisSelect);

            $thisSelect = array(
                'where' => $this->thisGET,
                'order_sort' => 'purchaseorderitem_category_id ASC, purchaseorderitem_purchaseorder_id ASC',
                'ascend' => 'asc',
                // 'limit' => $this->per_page,
                'return' => 'result'
            );
            $purchaseorderitems = $this->purchaseorderitem_model->select($thisSelect);

            $data['data'] = array();
            foreach($purchaseorderitems as $key => $value){
                $purchaseorder = get_purchaseorder($value->purchaseorderitem_purchaseorder_id);
                $data['data'][$key][] = 'Purchases - '.get_category($value->purchaseorderitem_category_id)->category_name;
                $data['data'][$key][] = date('Y-m-d', strtotime($purchaseorder->purchaseorder_create));
                $data['data'][$key][] = $purchaseorder->purchaseorder_number;
                $data['data'][$key][] = get_vendor($purchaseorder->purchaseorder_vendor_id)->vendor_company_name.' - Item: '.$value->purchaseorderitem_product_code;
                $data['data'][$key][] = money_format('%!n', get_product($value->purchaseorderitem_product_id)->product_cost*$purchaseorder->purchaseorder_vendor_exchange_rate);
                $data['data'][$key][] = '-';
                $data['data'][$key][] = '-';
            }
        }else if( $type == 'discount' ){
            $this->thisGET['invoice_discount_greateq'] = 0;

            $thisSelect = array(
                'where' => $this->thisGET,
                'return' => 'num_rows'
            );
            $data['num_rows'] = $this->invoice_model->select($thisSelect);

            $thisSelect = array(
                'where' => $this->thisGET,
                'order' => 'invoice_create',
                'ascend' => 'asc',
                // 'limit' => $this->per_page,
                'return' => 'result'
            );
            $invoices = $this->invoice_model->select($thisSelect);

            $data['data'] = array();
            $k = 0;
            foreach($invoices as $key => $value){
                $invoice_category_discount = $value->invoice_category_discount;
                if( $invoice_category_discount ){
                    $invoice_category_discount = json_decode($invoice_category_discount);
                    foreach ($invoice_category_discount as $key1 => $value1) {
                        $data['data'][$k][] = 'Sales discount';
                        $data['data'][$k][] = date('Y-m-d', strtotime($value->invoice_create));
                        $data['data'][$k][] = $value->invoice_number;
                        $data['data'][$k][] = get_client($value->invoice_client_id)->client_company_name.' - Item: DISCOUNT - '.($value->invoice_discount_type=='percent'&&$value->invoice_discount_value>0?$value->invoice_discount_value.'%':money_format('%!n', ($value->invoice_discount_value>0?$value->invoice_discount_value*$value->invoice_exchange_rate:$value->invoice_discount*$value->invoice_exchange_rate))).' on '.$value1->category_name;
                        $data['data'][$k][] = money_format('%!n', $value1->category_discount*$value->invoice_exchange_rate);
                        $data['data'][$k][] = '-';
                        $data['data'][$k][] = '-';
                        $k++;
                    }
                }
                $data['data'][$k][] = 'Sales discount';
                $data['data'][$k][] = date('Y-m-d', strtotime($value->invoice_create));
                $data['data'][$k][] = $value->invoice_number;
                $data['data'][$k][] = get_client($value->invoice_client_id)->client_company_name.' - Item: DISCOUNT - '.($value->invoice_discount_type=='percent'&&$value->invoice_discount_value>0?$value->invoice_discount_value.'%':money_format('%!n', ($value->invoice_discount_value>0?$value->invoice_discount_value*$value->invoice_exchange_rate:$value->invoice_discount*$value->invoice_exchange_rate)));
                $data['data'][$k][] = money_format('%!n', $value->invoice_discount*$value->invoice_exchange_rate);
                $data['data'][$k][] = '-';
                $data['data'][$k][] = '-';
                $k++;
            }
            $data['num_rows'] = $k;
        }else if( $type == 'freight' ){
            $this->thisGET['invoice_freight_greateq'] = 0;

            $thisSelect = array(
                'where' => $this->thisGET,
                'return' => 'num_rows'
            );
            $data['num_rows'] = $this->invoice_model->select($thisSelect);

            $thisSelect = array(
                'where' => $this->thisGET,
                'order' => 'invoice_create',
                'ascend' => 'asc',
                // 'limit' => $this->per_page,
                'return' => 'result'
            );
            $invoices = $this->invoice_model->select($thisSelect);

            $data['data'] = array();
            foreach($invoices as $key => $value){
                $data['data'][$key][] = 'Freight Income';
                $data['data'][$key][] = date('Y-m-d', strtotime($value->invoice_create));
                $data['data'][$key][] = $value->invoice_number;
                $data['data'][$key][] = get_client($value->invoice_client_id)->client_company_name.' - Item: FREIGHT INCOME';
                $data['data'][$key][] = '-';
                $data['data'][$key][] = money_format('%!n', $value->invoice_freight*$value->invoice_exchange_rate);
                $data['data'][$key][] = '-';
            }
        }else if( $type == 'fire' ){
            $this->thisGET['invoiceitem_category_id_in'] = array(9);
            $thisSelect = array(
                'where' => $this->thisGET,
                'return' => 'result'
            );
            $invoices = $this->invoice_model->select($thisSelect);
            if( $invoices ){
                $invoice_ids = convert_object_to_array($invoices, 'invoice_id');
            }else{
                $invoice_ids = array(0);
            }
            $this->thisGET['invoiceitem_invoice_id_in'] = $invoice_ids;

            $thisSelect = array(
                'where' => $this->thisGET,
                'return' => 'num_rows'
            );
            $data['num_rows'] = $this->invoiceitem_model->select($thisSelect);

            $thisSelect = array(
                'where' => $this->thisGET,
                'order_sort' => 'invoiceitem_category_id ASC, invoiceitem_invoice_id ASC',
                'ascend' => 'asc',
                // 'limit' => $this->per_page,
                'return' => 'result'
            );
            $invoiceitems = $this->invoiceitem_model->select($thisSelect);

            $data['data'] = array();
            foreach($invoiceitems as $key => $value){
                $invoice = get_invoice($value->invoiceitem_invoice_id);
                $data['data'][$key][] = 'Handling Income';
                $data['data'][$key][] = date('Y-m-d', strtotime($invoice->invoice_create));
                $data['data'][$key][] = $invoice->invoice_number;
                $data['data'][$key][] = get_client($invoice->invoice_client_id)->client_company_name.' - '.$value->invoiceitem_product_code;
                $data['data'][$key][] = '-';
                $data['data'][$key][] = money_format('%!n', $value->invoiceitem_subtotal*$invoice->invoice_exchange_rate);
                $data['data'][$key][] = '-';
            }
        }else if( $type == 'inventory' ){
            $thisSelect = array(
                'where' => $this->thisGET,
                'return' => 'result'
            );
            $invoices = $this->invoice_model->select($thisSelect);
            if( $invoices ){
                $invoice_ids = convert_object_to_array($invoices, 'invoice_id');
            }else{
                $invoice_ids = array(0);
            }
            $this->thisGET['invoiceitem_invoice_id_in'] = $invoice_ids;

            $purchaseorders = $this->purchaseorder_model->select($thisSelect);
            if( $purchaseorders ){
                $purchaseorder_ids = convert_object_to_array($purchaseorders, 'purchaseorder_id');
            }else{
                $purchaseorder_ids = array(0);
            }
            $this->thisGET['purchaseorderitem_purchaseorder_id_in'] = $purchaseorder_ids;

            $thisSelect = array(
                'where' => $this->thisGET,
                'return' => 'num_rows'
            );
            $data['num_rows'] = $this->invoice_model->inventory($thisSelect);

            $thisSelect = array(
                'where' => $this->thisGET,
                // 'order' => 'invoiceitem_category_id',
                // 'ascend' => 'asc',
                // 'limit' => $this->per_page,
                'return' => 'result'
            );
            $invoiceitems = $this->invoice_model->inventory($thisSelect);

            $data['data'] = array();
            foreach($invoiceitems as $key => $value){
                $data['data'][$key][] = 'Inventory - '.get_category($value->category_id)->category_name;
                $data['data'][$key][] = $value->inventory_sort;
                $data['data'][$key][] = $value->inventory_number;
                if( $value->inventory_type == 'inv' ){
                    $data['data'][$key][] = get_client($value->company_id)->client_company_name.' - Item: '.$value->product_code;
                    $data['data'][$key][] = '-';
                    $data['data'][$key][] = money_format('%!n', $value->product_subtotal*$value->exchange_rate);
                }else{
                    $data['data'][$key][] = get_vendor($value->company_id)->vendor_company_name.' - Item: '.$value->product_code;
                    $data['data'][$key][] = money_format('%!n', $value->product_subtotal*$value->exchange_rate);
                    $data['data'][$key][] = '-';
                }
                
                $data['data'][$key][] = '-';
            }
        }
    }

    private function get_form_data($rows){
        $data = array();

        $total = 0;
        $this->td_body = array();
        if( $rows && count($rows) ) {
            $debit_amt = 0;
            $credit_amt = 0;
            foreach ($rows as $key => $value) {
                $row = array();
                $row[] = $value[0];
                $row[] = $value[1];
                $row[] = $value[2];
                $row[] = $value[3];
                $debit_amt += $value[4];
                $row[] = $value[4];
                $credit_amt += $value[5];
                $row[] = $value[5];
                $row[] = $value[6];
                $this->td_body[] = $row;
            }
            $this->th_footer[3] = 'Current Period Change';
            $this->th_footer[4] = money_format('%!n', $debit_amt);
            $this->th_footer[5] = money_format('%!n', $credit_amt);
            $this->th_footer[6] = money_format('%!n', $debit_amt - $credit_amt);
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
		$this->load->model('invoiceitem_model');
        $this->load->model('purchaseorder_model');
        $this->load->model('purchaseorderitem_model');
		$this->load->model('user_model');

        $this->per_page = get_setting('per_page')->setting_value;
        $this->thisGET = $this->uri->uri_to_assoc();
        // $this->thisGET['invoice_status'] = 'complete';
        // $this->thisGET['invoice_deleted'] = 'N';

        if( !isset($this->thisGET['type']) ){
            $this->thisGET['type'] = 'account_receivable';
        }
        if( !isset($this->thisGET['date_greateq']) ){
            $this->thisGET['date_greateq'] = date("Y-m")."-01";
        }
        if( !isset($this->thisGET['date_smalleq']) ){
            $this->thisGET['date_smalleq'] = date("Y-m-d");
        }
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
        $this->make_form_data($this->thisGET['type'], $data);
        $data = array_merge($data, $this->get_form_data($data['data']));

		// $thisSelect = array(
		// 	'where' => $this->thisGET,
		// 	'return' => 'num_rows'
		// );
		// $data['num_rows'] = $this->invoice_model->report($thisSelect);

		/* status */
		// $data['statuss'] = (object)array(
		// 	(object)array('status_name' => 'processing'),
		// 	(object)array('status_name' => 'complete'),
		// 	(object)array('status_name' => 'cancel')
		// );

		// /* user */
		// $thisSelect = array(
		// 	'return' => 'result'
		// );
		// $data['users'] = $this->user_model->select($thisSelect);

		/* pagination */
		$this->pagination->initialize(get_pagination_config($this->per_page, $data['num_rows']));

		$this->load->view('ledgerreport_view', $data);
	}

    public function export()
    {
        $this->make_form_data($this->thisGET['type'], $data);
        $this->get_form_data($data['data']);

        $fileName = 'General_ledger_report_'.date('YmdHis');
        php_excel_export($this->th_header, $this->td_body, $fileName);
    }

}
