<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payablereport extends CI_Controller {

    private $th_header = array(
        'Vendor ID',
        'Vendor',
        'PO No',
        'Deadline',
        'Total',
        'Rate',
        'Total (HKD)',
        'PO date',
        'Sales'
    );

    private $td_body = array();

    private $th_footer = array(
        '',
        '',
        '',
        '',
        '',
        '',
        'Pay (HKD)',
        '',
        ''
    );

    private $thisGET;
    private $per_page;

    private function get_form_data($rows){
        $data = array();

        $total = 0;
        $this->td_body = array();
        if( $rows && count($rows) ) {
            foreach ($rows as $key => $value) {
                $thisUser = get_user($value->purchaseorder_quotation_user_id);
                $row = array();
                $row[] = $value->purchaseorder_vendor_company_code;
                $row[] = $value->purchaseorder_vendor_company_name;
                $row[] = '<a href="' . base_url('purchaseorder/select/purchaseorder_id/' . $value->purchaseorder_id) . '">' . $value->purchaseorder_number . '</a>';
                $row[] = $value->purchaseorder_reminder_date;
                $row[] = strtoupper($value->purchaseorder_currency) . ' ' . money_format('%!n', $value->purchaseorder_total);
                $row[] = $value->purchaseorder_vendor_exchange_rate;
                $total += $value->purchaseorder_total * $value->purchaseorder_vendor_exchange_rate;
                $row[] = 'HKD ' . money_format('%!n', $value->purchaseorder_total * $value->purchaseorder_vendor_exchange_rate);
                $row[] = convert_datetime_to_date($value->purchaseorder_create);
                $row[] = ($thisUser?$thisUser->user_name:'');
                $this->td_body[] = $row;
            }
            $this->th_footer[6] = strtoupper($value->purchaseorder_currency).' '.money_format('%!n', $total);
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
		$this->load->model('purchaseorder_model');
		$this->load->model('user_model');

        $this->per_page = get_setting('per_page')->setting_value;
        $this->thisGET = $this->uri->uri_to_assoc();
        $this->thisGET['purchaseorder_status'] = 'processing';
        $this->thisGET['purchaseorder_deleted'] = 'N';
	}

	public function index()
	{
		redirect('payablereport/select');
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
		$thisSelect = array(
			'where' => $this->thisGET,
			'order' => 'purchaseorder_vendor_id',
			'ascend' => 'asc',
			'limit' => $this->per_page,
			'return' => 'result'
		);
		$data['purchaseorders'] = $this->purchaseorder_model->select($thisSelect);

        $data = array_merge($data, $this->get_form_data($data['purchaseorders']));

		$thisSelect = array(
			'where' => $this->thisGET,
			'group' => 'purchaseorder_number',
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->purchaseorder_model->select($thisSelect);

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

		$this->load->view('payablereport_view', $data);
	}

    public function export()
    {
        $thisSelect = array(
            'where' => $this->thisGET,
            'order' => 'purchaseorder_vendor_id',
            'ascend' => 'asc',
            'limit' => $this->per_page,
            'return' => 'result'
        );
        $data['purchaseorders'] = $this->purchaseorder_model->select($thisSelect);
        $this->get_form_data($data['purchaseorders']);

        $fileName = 'Payable_report_'.date('YmdHis');
        php_excel_export($this->th_header, $this->td_body, $fileName);
    }

}
