<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stockreport extends CI_Controller {

    private $th_header = array(
        'Type',
        'Item ID',
        'Item Name',
        'Exchange',
        'Quantity',
        'Unit',
        'Unit Cost',
        'Item Value',
        'Rate',
        'Item Value (HKD)',
        'Vendor',
        'Remark',
        'Handler',
        'Create'
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
        '',
        '',
        '',
        '',
        ''
    );

    private $thisGET;
    private $per_page;

    private function make_form_data(&$data, $isExport){
        if( $isExport ){
            $thisSelect = array(
                'where' => $this->thisGET,
                'return' => 'result'
            );
        }else{
            $thisSelect = array(
                'where' => $this->thisGET,
                'limit' => $this->per_page,
                'return' => 'result'
            );
        }
        $data['exchanges'] = $this->exchange_model->select($thisSelect);

        $thisSelect = array(
            'where' => $this->thisGET,
            'return' => 'num_rows'
        );
        $data['num_rows'] = $this->exchange_model->select($thisSelect);
    }

    private function get_form_data($rows){
        $data = array();

        $total = 0;
        $this->td_body = array();
        if( $rows && count($rows) ) {
            $total = 0;
            foreach ($rows as $key => $value) {
                $row = array();
                $row[] = 'Stock '.$value->exchange_type;
                $thisProduct = get_product($value->exchange_product_id);
                $thisUnit = get_unit($thisProduct->product_unit_id);
                $thisVendor = get_vendor($thisProduct->product_vendor_id);
                $row[] = $thisProduct->product_code;
                $row[] = $thisProduct->product_name;
                $thisWarehouse = '';
                if(!empty(get_warehouse($value->exchange_warehouse_id_from))){
                    $thisWarehouse .= get_warehouse($value->exchange_warehouse_id_from)->warehouse_name;
                }else{
                    $thisWarehouse .= 'Other';
                }
                $thisWarehouse .= ' >>> ';
                if(!empty(get_warehouse($value->exchange_warehouse_id_to))){
                    $thisWarehouse .= get_warehouse($value->exchange_warehouse_id_to)->warehouse_name;
                }else{
                    $thisWarehouse .= 'Other';
                }
                $row[] = $thisWarehouse;
                $row[] = $value->exchange_quantity;
                $row[] = $thisUnit->unit_name;
                $row[] = strtoupper(get_currency($thisVendor->vendor_currency_id)->currency_name).' '.money_format('%!n', $thisProduct->product_cost);
                $row[] = strtoupper(get_currency($thisVendor->vendor_currency_id)->currency_name).' '.money_format('%!n', $value->exchange_quantity * $thisProduct->product_cost);
                $row[] = get_currency($thisVendor->vendor_currency_id)->currency_exchange_rate;
//                $total += $value->exchange_quantity * $thisProduct->product_cost * get_currency($thisVendor->vendor_currency_id)->currency_exchange_rate;
                $row[] = 'HKD '.money_format('%!n', $value->exchange_quantity * $thisProduct->product_cost * get_currency($thisVendor->vendor_currency_id)->currency_exchange_rate);
                $row[] = $thisVendor->vendor_company_name;
                $row[] = $value->exchange_remark;
                $row[] = get_user($value->exchange_user_id)->user_name;
                $row[] = $value->exchange_create;
                $this->td_body[] = $row;
            }
//            $this->th_footer[9] = 'HKD '.money_format('%!n', $total);
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
		$this->load->model('exchange_model');
		$this->load->model('client_model');
		$this->load->model('purchaseorder_model');
		$this->load->model('invoice_model');
		$this->load->model('user_model');

        $this->per_page = get_setting('per_page')->setting_value;
        $this->thisGET = $this->uri->uri_to_assoc();
	}

	public function index()
	{
		redirect('stockreport/select');
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
        $data = array_merge($data, $this->get_form_data($data['exchanges']));

        /* type */
        $data['types'] = (object)array(
            (object)array('type_name' => 'in'),
            (object)array('type_name' => 'transfer'),
            (object)array('type_name' => 'out')
        );

        $thisSelect = array(
            'group' => 'exchange_product_id',
            'return' => 'result'
        );
        $data['exchange_product_ids'] = $this->exchange_model->select($thisSelect);

        /* pagination */
        $this->pagination->initialize(get_pagination_config($this->per_page, $data['num_rows']));

		$this->load->view('stockreport_view', $data);
	}

    public function export()
    {
        $this->make_form_data($data, true);
        $data = array_merge($data, $this->get_form_data($data['exchanges']));

        $fileName = 'Stock_report_'.date('YmdHis');
        php_excel_export($this->th_header, $this->td_body, $fileName);
    }

}
