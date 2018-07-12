<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Closingreport extends CI_Controller {

    private $th_header = array(
        'PO No',
        'SO No',
        'DEP/INV NO',
        'Item Code',
        'Quantity',
        'Unit Cost',
        'Item Value',
        'Item Value (HKD)',
        'Customer',
        'Location',
        'Nature',
        'Factory',
        'ETA to Warehouse',
        'ETD to Client'
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
        'Item Value (HKD)',
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
		$this->load->model('purchaseorder_model');
        $this->load->model('purchaseorderitem_model');
        $this->load->model('invoice_model');
		$this->load->model('user_model');
        $this->load->model('deliverynote_model');

        $this->per_page = get_setting('per_page')->setting_value;
        $this->thisGET = $this->uri->uri_to_assoc();
        $this->thisGET['deliverynote_status_like'] = 'processing';
        $this->thisGET['deliverynote_deleted'] = 'N';
        $this->thisGET['purchaseorder_default'] = true;
        $this->thisGET['purchaseorder_deleted'] = 'N';
	}

	private function make_form_data(&$data){
        $thisSelect = array(
            'where' => $this->thisGET,
            'limit' => $this->per_page,
            'return' => 'result'
        );
        $data['purchaseorders'] = $this->purchaseorder_model->select($thisSelect);

        foreach($data['purchaseorders'] as $key => $value){
            /* purchaseorderitem */
            $thisSelect = array(
                'where' => array(
                    'purchaseorderitem_purchaseorder_id' => $value->purchaseorder_id
                ),
                'return' => 'result'
            );
            $data['purchaseorderitems'] = $this->purchaseorderitem_model->select($thisSelect);
            $data['purchaseorders'][$key]->purchaseorderitems = $data['purchaseorderitems'];

            /* invoice */
            $thisSelect = array(
                'where' => array(
                    'invoice_salesorder_id' => $value->purchaseorder_salesorder_id
                ),
                'return' => 'result'
            );
            $data['invoices'] = $this->invoice_model->select($thisSelect);
            $data['purchaseorders'][$key]->invoices = $data['invoices'];

            /* deliverynote */
            $thisSelect = array(
                'where' => array(
                    'deliverynote_salesorder_id' => $value->purchaseorder_salesorder_id
                ),
                'return' => 'result'
            );
            $data['deliverynotes'] = $this->deliverynote_model->select($thisSelect);
            $data['purchaseorders'][$key]->deliverynotes = $data['deliverynotes'];
        }
    }

    private function get_form_data($rows){
        $data = array();

        $total = 0;
        $this->td_body = array();
        if( $rows && count($rows) ) {
            $total = 0;
            foreach ($rows as $key => $value) {
                $thisSalesorder = get_salesorder($value->purchaseorder_salesorder_id);
                $row = array();
                $row[] = '<a href="' . base_url('purchaseorder/update/purchaseorder_id/' . $value->purchaseorder_id) . '">' . $value->purchaseorder_number . '</a>';
                $row[] = '<a href="' . base_url('salesorder/update/salesorder_id/' . $value->purchaseorder_salesorder_id) . '">' . ($thisSalesorder?$thisSalesorder->salesorder_number:'') . '</a>';
                $temp = '';
                foreach($value->invoices as $key1 => $value1){
                    $temp .= '<div class="no-wrap">'.'<a href="' . base_url('invoice/update/invoice_id/' . $value1->invoice_id) . '">'. $value1->invoice_number . '</a>' .'<br/></div>';
                }
                $row[] = $temp;
                $temp = '';
                foreach($value->purchaseorderitems as $key1 => $value1){
                    $temp .= '<div class="no-wrap">'.$value1->purchaseorderitem_product_code.'<br/></div>';
                }
                $row[] = $temp;
                $temp = '';
                foreach($value->purchaseorderitems as $key1 => $value1){
                    $temp .= '<div class="no-wrap">'.$value1->purchaseorderitem_quantity.'<br/></div>';
                }
                $row[] = $temp;
                $temp = '';
                foreach($value->purchaseorderitems as $key1 => $value1){
                    $thisProduct = get_product($value1->purchaseorderitem_product_id);
                    $temp .= '<div class="no-wrap">'.($thisProduct?get_currency(get_vendor($thisProduct->product_vendor_id)->vendor_currency_id)->currency_name.' '.money_format('%!n', $thisProduct->product_cost):'').'<br/></div>';
                }
                $row[] = $temp;
                $temp = '';
                foreach($value->purchaseorderitems as $key1 => $value1){
                    $thisProduct = get_product($value1->purchaseorderitem_product_id);
                    $temp .= '<div class="no-wrap">'.($thisProduct?get_currency(get_vendor($thisProduct->product_vendor_id)->vendor_currency_id)->currency_name.' '.money_format('%!n', $value1->purchaseorderitem_product_price):'').'<br/></div>';
                }
                $row[] = $temp;
                $temp = '';
                foreach($value->purchaseorderitems as $key1 => $value1){
                    $total += $value1->purchaseorderitem_product_price*$value->purchaseorder_vendor_exchange_rate;
                    $temp .= '<div class="no-wrap">'.'HKD '.money_format('%!n', $value1->purchaseorderitem_product_price*$value->purchaseorder_vendor_exchange_rate).'<br/></div>';
                }
                $row[] = $temp;
                $temp = '';
                foreach($value->invoices as $key1 => $value1){
                    $temp .= '<div class="no-wrap">'.$value1->invoice_client_company_name.'<br/></div>';
                }
                $row[] = $temp;
                $temp = '';
                foreach($value->purchaseorderitems as $key1 => $value1){
                    $warehouse = get_product_warehouse($value1->purchaseorderitem_product_id);
                    $temp .= '<div class="no-wrap">'.($warehouse?$warehouse->warehouse_name:'-').'<br/></div>';
                }
                $row[] = $temp;
                $temp = '';
                foreach($value->purchaseorderitems as $key1 => $value1){
                    $thisCategory = get_category($value1->purchaseorderitem_category_id);
                    $temp .= '<div class="no-wrap">'.($thisCategory?$thisCategory ->category_name:'').'<br/></div>';
                }
                $row[] = $temp;
                $temp = '';
                foreach($value->purchaseorderitems as $key1 => $value1){
                    $thisProduct = get_product($value1->purchaseorderitem_product_id);
                    $temp .= '<div class="no-wrap">'.($thisProduct?get_vendor($thisProduct->product_vendor_id)->vendor_company_code.' - '.get_vendor($thisProduct->product_vendor_id)->vendor_company_name:'').'<br/></div>';
                }
                $row[] = $temp;
                $row[] = $value->purchaseorder_arrive_date!='0000-00-00'?$value->purchaseorder_arrive_date:'-';
                $temp = '';
                foreach($value->deliverynotes as $key1 => $value1){
                    $temp .= '<div class="no-wrap">'.$value1->deliverynote_issue.'<br/></div>';
                }
                $row[] = $temp;
                $this->td_body[] = $row;
            }
            $this->th_footer[7] = 'HKD '.money_format('%!n', $total);
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
        $data = array_merge($data, $this->get_form_data($data['purchaseorders']));

		$thisSelect = array(
			'where' => $this->thisGET,
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->purchaseorder_model->select($thisSelect);

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
        $this->get_form_data($data['purchaseorders']);

        $fileName = 'Closing_stock_report_'.date('YmdHis');
        php_excel_export($this->th_header, $this->td_body, $fileName);
    }

}
