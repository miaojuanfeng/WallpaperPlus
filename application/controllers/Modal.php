<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Modal extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// check_session_timeout();
		// check_is_login();
		// check_permission();
	}

	public function index()
	{
		$this->{$this->input->post('thisTableId')}();
	}

	private function getData(){
        $thisData = array();
        if( !empty($this->input->post('thisGet')) ){
            $t = explode('/', $this->input->post('thisGet'));
            $k = '';
            foreach ($t as $key => $value) {
                if(!($key%2)){
                    $k = $value;
                }else{
                    $thisData[$k] = $value;
                }
            }
        }
        if( !empty($this->input->post('thisPost')) ){
            foreach ($this->input->post('thisPost') as $key => $value) {
                $thisData[$key] = $value;
            }
        }
        return $thisData;
    }

	public function product_select()
	{
		$this->load->model('product_model');
        $this->load->model('vendor_model');
			
		$per_page = get_setting('per_page')->setting_value;

		$thisGet = $this->getData();
		
        /* check vendor */
        if( isset($thisGet['vendor_company_name_like']) ){
            $thisSelect = array(
                'where' => $thisGet,
                'return' => 'result'
            );
            $data['vendors'] = $this->vendor_model->select($thisSelect);

            if($data['vendors']){
                foreach($data['vendors'] as $key => $value){
                    $thisGet['product_vendor_id_in'][] = $value->vendor_id;
                }
            }else{
                $thisGet['product_vendor_id_in'] = array(0);
            }
        }
        /* check vendor */

//        var_dump($thisGet);

		$thisSelect = array(
			'where' => $thisGet,
			'limit' => $per_page,
			'return' => 'result'
		);
		$data['products'] = $this->product_model->select($thisSelect);

		$thisSelect = array(
			'where' => $thisGet,
			'return' => 'num_rows'
		);
		$data['num_rows'] = $this->product_model->select($thisSelect);

		/* pagination */
		$data['pagination'] = get_pagination_js_config($thisGet, $per_page, $data['num_rows']);

		echo $this->load->view('modal/product_view', $data, true);
	}

    public function product_update()
    {
        $this->load->model('product_model');
        $this->load->model('vendor_model');
        $this->load->model('brand_model');
        $this->load->model('unit_model');
        $this->load->model('team_model');
        $this->load->model('team_model');
        $this->load->model('type_model');

        $thisPOST = $this->getData();

        if( isset($thisPOST['action']) && $thisPOST['action'] == 'update' ){
            $this->product_model->update($thisPOST);

            $thisLog['log_permission_class'] = $this->router->fetch_class();
            $thisLog['log_permission_action'] = $this->router->fetch_method();
            $thisLog['log_record_id'] = $thisPOST['product_id'];
            set_log($thisLog);
        }else{
            /* product */
            $thisSelect = array(
                'where' => $thisPOST,
                'return' => 'row'
            );
            $data['product'] = $this->product_model->select($thisSelect);

            /* products */
            $thisSelect = array(
                'where' => array(
                    'product_code_noteq' => $data['product']->product_code
                ),
                'return' => 'result'
            );
            $data['products'] = $this->product_model->select($thisSelect);

            /* vendor */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['vendors'] = $this->vendor_model->select($thisSelect);

            /* brand */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['brands'] = $this->brand_model->select($thisSelect);

            /* unit */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['units'] = $this->unit_model->select($thisSelect);

            /* team */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['teams'] = $this->team_model->select($thisSelect);

            /* type */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['types'] = $this->type_model->select($thisSelect);

            echo $this->load->view('modal/product_insert_update_view', $data, true);
        }
    }

    public function product_insert()
    {
        $this->load->model('product_model');
        $this->load->model('vendor_model');
        $this->load->model('brand_model');
        $this->load->model('unit_model');
        $this->load->model('team_model');
        $this->load->model('team_model');
        $this->load->model('type_model');

        $thisPOST = $this->getData();

        if( isset($thisPOST['action']) && $thisPOST['action'] == 'insert' ){
            $thisInsertId = $this->product_model->insert($thisPOST);

            $thisLog['log_permission_class'] = $this->router->fetch_class();
            $thisLog['log_permission_action'] = $this->router->fetch_method();
            $thisLog['log_record_id'] = $thisPOST['product_id'];
            set_log($thisLog);
        }else{
            /* preset empty data */
            $thisArray = array();
            foreach($this->product_model->structure() as $key => $value){
                $thisArray[$value->Field] = '';
            }
            $data['product'] = (object)$thisArray;

            /* products */
            $thisSelect = array(
                'where' => array(
                    'product_code_noteq' => $data['product']->product_code
                ),
                'return' => 'result'
            );
            $data['products'] = $this->product_model->select($thisSelect);

            /* vendor */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['vendors'] = $this->vendor_model->select($thisSelect);

            /* brand */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['brands'] = $this->brand_model->select($thisSelect);

            /* unit */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['units'] = $this->unit_model->select($thisSelect);

            /* team */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['teams'] = $this->team_model->select($thisSelect);

            /* type */
            $thisSelect = array(
                'return' => 'result'
            );
            $data['types'] = $this->type_model->select($thisSelect);

            echo $this->load->view('modal/product_insert_update_view', $data, true);
        }
    }


	////////

	public function quotationLoader()
	{
		$this->load->model('quotation_model');
			
		/* quotation */
		$thisSelect = array(
			'where' => array(
				'quotation_number' => $this->input->post('thisRecordId')
			),
			'order' => 'quotation_version',
			'ascend' => 'desc',
			'return' => 'result'
		);
		$thisData = $this->quotation_model->select($thisSelect);
		
		foreach($thisData as $key => $value){
			echo '<tr class="more-'.$this->input->post('thisRecordId').' more-area">';
			echo '<td><a href="'.base_url('quotation/update/quotation_id/'.$value->quotation_id).'" data-toggle="tooltip" title="Update">'.$value->quotation_number.'</a></td>';
			echo '<td>V'.$value->quotation_version.'</td>';
			echo '<td>'.convert_datetime_to_date($value->quotation_create).'</td>';
			echo '<td>'.$value->quotation_client_company_name.'</td>';
			echo '<td>'.$value->quotation_client_name.'</td>';
			echo '<td>'.$value->quotation_project_name.'</td>';
			echo '<td>'.ucfirst(get_user($value->quotation_user_id)->user_name).'</td>';
			echo '<td>'.convert_datetime_to_date($value->quotation_expire).'</td>';
			echo '<td>'.ucfirst($value->quotation_status).'</td>';
			echo '<td>'.strtoupper($value->quotation_currency).' '.money_format('%!n', $value->quotation_total).'</td>';
			echo '<td align="right"><a target="_blank" href="'.base_url('/assets/images/pdf/quotation/'.$value->quotation_number.'-v'.$value->quotation_version).'" data-toggle="tooltip" title="Print"><i class="glyphicon glyphicon-print"></i></a></td>';
			echo '<td align="right"><a href="'.base_url('quotation/update/quotation_id/'.$value->quotation_id).'" data-toggle="tooltip" title="Update"><i class="glyphicon glyphicon-edit"></i></a></td>';
			echo '<td align="right"><a onclick="check_delete('.$value->quotation_id.');" data-toggle="tooltip" title="Remove"><i class="glyphicon glyphicon-remove"></i></a></td>';
			echo '</tr>';
		}
	}

	public function clientLoader()
	{
		$this->load->model('client_model');
			
		/* client */
		$thisSelect = array(
			'where' => array('client_id' => $this->input->post('thisRecordId')),
			'return' => 'row'
		);
		$thisData = $this->client_model->select($thisSelect);

		if($thisData){
			echo '<script>';
			echo 'function clientLoader(){';
            echo '$("input[name=\'quotation_client_company_code\']").val("'.$thisData->client_company_code.'").css("display", "none").fadeIn();';
			echo '$("input[name=\'quotation_client_company_name\']").val("'.$thisData->client_company_name.'").css("display", "none").fadeIn();';
			echo '$("textarea[name=\'quotation_client_company_address\']").val("'.convert_nl($thisData->client_company_address).'").css("display", "none").fadeIn();';
			echo '$("input[name=\'quotation_client_name\']").val("'.$thisData->client_firstname.' '.$thisData->client_lastname.'").css("display", "none").fadeIn();';
			echo '$("input[name=\'quotation_client_company_phone\']").val("'.$thisData->client_company_phone.'").css("display", "none").fadeIn();';
			echo '$("input[name=\'quotation_client_phone\']").val("'.$thisData->client_phone.'").css("display", "none").fadeIn();';
			echo '$("input[name=\'quotation_client_email\']").val("'.$thisData->client_email.'").css("display", "none").fadeIn();';
			echo '$("input[name=\'quotation_terms\']").val("'.get_terms($thisData->client_terms_id)->terms_name.'").css("display", "none").fadeIn();';
			echo '}';
			echo '</script>';
		}
	}

	public function vendorLoader()
	{
		$this->load->model('vendor_model');
        $this->load->model('currency_model');

		/* vendor */
		$thisSelect = array(
			'where' => array('vendor_id' => $this->input->post('thisRecordId')),
			'return' => 'row'
		);
		$thisData = $this->vendor_model->select($thisSelect);

        /* currency */
        $thisSelect = array(
            'where' => array('currency_id' => $thisData->vendor_currency_id),
            'return' => 'row'
        );
        $thisData2 = $this->currency_model->select($thisSelect);

		if($thisData){
			echo '<script>';
			echo 'function vendorLoader(){';
            echo '$("input[name=\'purchaseorder_vendor_company_code\']").val("'.$thisData->vendor_company_code.'").css("display", "none").fadeIn();';
			echo '$("input[name=\'purchaseorder_vendor_company_name\']").val("'.$thisData->vendor_company_name.'").css("display", "none").fadeIn();';
			echo '$("textarea[name=\'purchaseorder_vendor_company_address\']").val("'.convert_nl($thisData->vendor_company_address).'").css("display", "none").fadeIn();';
			echo '$("input[name=\'purchaseorder_vendor_name\']").val("'.$thisData->vendor_firstname.' '.$thisData->vendor_lastname.'").css("display", "none").fadeIn();';
			echo '$("input[name=\'purchaseorder_vendor_company_phone\']").val("'.$thisData->vendor_company_phone.'").css("display", "none").fadeIn();';
			echo '$("input[name=\'purchaseorder_vendor_phone\']").val("'.$thisData->vendor_phone.'").css("display", "none").fadeIn();';
			echo '$("input[name=\'purchaseorder_vendor_email\']").val("'.$thisData->vendor_email.'").css("display", "none").fadeIn();';
            echo '$("input[name=\'purchaseorder_vendor_exchange_rate\']").val("'.$thisData2->currency_exchange_rate.'").css("display", "none").fadeIn();';
            echo '$("input[name=\'purchaseorder_vendor_currency\']").val("'.strtoupper($thisData2->currency_name).'").css("display", "none").fadeIn();';
            echo '$("input[name=\'purchaseorder_currency\']").val("'.$thisData2->currency_name.'")';
			echo '}';
			echo '</script>';
		}
	}

	public function exchangeRateLoader(){
        $this->load->model('currency_model');

        /* currency */
        $thisSelect = array(
            'where' => array('currency_name' => $this->input->post('thisRecordId')),
            'return' => 'row'
        );
        $thisData = $this->currency_model->select($thisSelect);

        if($thisData){
            echo '<script>';
            echo 'function exchangeRateLoader(){';
            echo '$("input[name=\'quotation_exchange_rate\']").val("'.$thisData->currency_exchange_rate.'").css("display", "none").fadeIn();';
            echo '}';
            echo '</script>';
        }
    }

	public function salesorderLoader()
	{
		$this->load->model('salesorder_model');

		/* salesorder */
		$thisSelect = array(
			'where' => array('salesorder_id' => $this->input->post('thisRecordId')),
			'return' => 'row'
		);
		$thisData = $this->salesorder_model->select($thisSelect);

		if($thisData){
			echo '<script>';
			echo 'function salesorderLoader(){';
			echo '$("input[name=\'purchaseorder_project_name\']").val("'.$thisData->salesorder_project_name.'").css("display", "none").fadeIn();';
			echo '$("input[name=\'purchaseorder_quotation_user_id\']").val("'.$thisData->salesorder_quotation_user_id.'");';
			echo '}';
			echo '</script>';
		}
	}

	public function quotationProductLoader()
	{
		$this->load->model('product_model');
			
		/* client */
		$thisSelect = array(
			'where' => array('product_id' => $this->input->post('thisRecordId')),
			'return' => 'row'
		);
		$thisData = $this->product_model->select($thisSelect);

		if($thisData){
			echo '<script>';
			echo 'function quotationProductLoader(){';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'quotationitem_product_type_name[]\']").val("'.get_type($thisData->product_type_id)->type_name.'");';
			echo 'if( $("select[name=\'quotation_display_code\']").val() == "code" ){';
                echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'quotationitem_product_code[]\']").val("'.$thisData->product_code.'").css("display", "none").fadeIn();';
            echo '}else {';
                echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'quotationitem_product_code[]\']").val("'.$thisData->product_wpp_code.'").css("display", "none").fadeIn();';
            echo '}';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'quotationitem_product_name[]\']").val("'.$thisData->product_name.'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'quotationitem_product_link[]\']").val("'.$thisData->product_link.'").css("display", "none").fadeIn();';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') textarea[name=\'quotationitem_product_detail[]\']").val("'.convert_nl($thisData->product_detail).'").css("display", "none").fadeIn();';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'quotationitem_product_price[]\']").val("'.$thisData->{'product_price_'.$this->input->post('thisCurrency')}.'").css("display", "none").fadeIn();';
			echo '}';
			echo '</script>';
		}
	}

    public function quotationProductCodeLoader()
    {
        $this->load->model('product_model');

        /* client */
        $thisSelect = array(
            'where' => array('product_id' => $this->input->post('thisRecordId')),
            'return' => 'row'
        );
        $thisData = $this->product_model->select($thisSelect);

        if($thisData){
            echo '<script>';
            echo 'function quotationProductCodeLoader(){';
            echo 'if( $("select[name=\'quotation_display_code\']").val() == "code" ){';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'quotationitem_product_code[]\']").val("'.$thisData->product_code.'").css("display", "none").fadeIn();';
            echo '}else {';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'quotationitem_product_code[]\']").val("'.$thisData->product_wpp_code.'").css("display", "none").fadeIn();';
            echo '}';
            echo '}';
            echo '</script>';
        }
    }

	public function salesorderProductLoader()
	{
		$this->load->model('product_model');
			
		/* product */
		$thisSelect = array(
			'where' => array('product_id' => $this->input->post('thisRecordId')),
			'return' => 'row'
		);
		$thisData = $this->product_model->select($thisSelect);

		if($thisData){
			echo '<script>';
			echo 'function salesorderProductLoader(){';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'salesorderitem_product_type_name[]\']").val("'.get_type($thisData->product_type_id)->type_name.'");';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'salesorderitem_product_code[]\']").val("'.$thisData->product_code.'").css("display", "none").fadeIn();';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'salesorderitem_product_name[]\']").val("'.$thisData->product_name.'").css("display", "none").fadeIn();';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') textarea[name=\'salesorderitem_product_detail[]\']").val("'.convert_nl($thisData->product_detail).'").css("display", "none").fadeIn();';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'salesorderitem_product_price[]\']").val("'.$thisData->{'product_price_'.$this->input->post('thisCurrency')}.'").css("display", "none").fadeIn();';
			echo '}';
			echo '</script>';
		}
	}

	public function purchaseorderProductLoader()
	{
		$this->load->model('product_model');
			
		/* product */
		$thisSelect = array(
			'where' => array('product_id' => $this->input->post('thisRecordId')),
			'return' => 'row'
		);
		$thisData = $this->product_model->select($thisSelect);

		if($thisData){
			echo '<script>';
			echo 'function purchaseorderProductLoader(){';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'purchaseorderitem_product_type_name[]\']").val("'.get_type($thisData->product_type_id)->type_name.'");';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'purchaseorderitem_product_code[]\']").val("'.$thisData->product_code.'").css("display", "none").fadeIn();';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'purchaseorderitem_product_name[]\']").val("'.$thisData->product_name.'").css("display", "none").fadeIn();';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') textarea[name=\'purchaseorderitem_product_detail[]\']").val("'.convert_nl($thisData->product_detail).'").css("display", "none").fadeIn();';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'purchaseorderitem_product_price[]\']").val("'.$thisData->product_cost.'").css("display", "none").fadeIn();';
			echo '}';
			echo '</script>';
		}
	}

    public function invoiceProductLoader()
    {
        $this->load->model('product_model');

        /* product */
        $thisSelect = array(
            'where' => array('product_id' => $this->input->post('thisRecordId')),
            'return' => 'row'
        );
        $thisData = $this->product_model->select($thisSelect);

        if($thisData){
            echo '<script>';
            echo 'function invoiceProductLoader(){';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'invoiceitem_product_type_name[]\']").val("'.get_type($thisData->product_type_id)->type_name.'");';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'invoiceitem_product_code[]\']").val("'.$thisData->product_code.'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'invoiceitem_product_name[]\']").val("'.$thisData->product_name.'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') textarea[name=\'invoiceitem_product_detail[]\']").val("'.convert_nl($thisData->product_detail).'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'invoiceitem_product_price[]\']").val("'.$thisData->{'product_price_'.strtolower($this->input->post('thisCurrency'))}.'").css("display", "none").fadeIn();';
            echo '}';
            echo '</script>';
        }
    }

	public function termsLoader()
	{
		$this->load->model('terms_model');
			
		/* terms */
		$thisSelect = array(
			'where' => array('terms_id' => $this->input->post('thisRecordId')),
			'return' => 'row'
		);
		$thisData = $this->terms_model->select($thisSelect);

		if($thisData){
			echo '<script>';
			echo 'function termsLoader(){';
			echo '$("textarea[name=\''.$this->input->post('thisTableField').'\']").val("'.convert_nl($thisData->terms_content).'").css("display", "none").fadeIn();';
			echo '}';
			echo '</script>';
		}
	}

	public function quotation()
	{
		echo 'test';
	}

	public function invoice()
	{
		echo 'test';
	}

	public function client()
	{
		$this->load->model('client_model');
			
		/* client */
		$thisSelect = array(
			'where' => array('client_id' => $this->input->post('thisRecordId')),
			'return' => 'row'
		);
		$thisData = $this->client_model->select($thisSelect);

		echo $this->input->post('thisRecordId');
		exit;
	}

	public function vendor()
	{
		$this->load->model('vendor_model');
			
		/* client */
		$thisSelect = array(
			'where' => array('vendor_id' => $this->input->post('thisRecordId')),
			'return' => 'row'
		);
		$thisData = $this->vendor_model->select($thisSelect);

		echo $this->input->post('thisRecordId');
		exit;
	}

	public function company()
	{
		$this->load->model('company_model');
			
		/* client */
		$thisSelect = array(
			'where' => array('company_id' => $this->input->post('thisRecordId')),
			'return' => 'row'
		);
		$thisData = $this->company_model->select($thisSelect);

		echo $this->input->post('thisRecordId');
		exit;
	}

	

	public function category()
	{
		$this->load->model('category_model');
			
		/* category */
		$thisSelect = array(
			'where' => array('category_id' => $this->input->post('thisRecordId')),
			'return' => 'row'
		);
		$thisData = $this->category_model->select($thisSelect);

		echo $this->input->post('thisRecordId');
		exit;
	}

	public function brand()
	{
		$this->load->model('brand_model');
			
		/* brand */
		$thisSelect = array(
			'where' => array('brand_id' => $this->input->post('thisRecordId')),
			'return' => 'row'
		);
		$thisData = $this->brand_model->select($thisSelect);

		echo $this->input->post('thisRecordId');
		exit;
	}

	public function terms()
	{
		$this->load->model('terms_model');
			
		/* brand */
		$thisSelect = array(
			'where' => array('terms_id' => $this->input->post('thisRecordId')),
			'return' => 'row'
		);
		$thisData = $this->terms_model->select($thisSelect);

		echo $this->input->post('thisRecordId');
		exit;
	}

	public function role()
	{
		$this->load->model('r_permission_role_model');
			
		/* r_permission_role */
		$thisSelect = array(
			'where' => array('role_id' => $this->input->post('thisRecordId'))
		);
		$thisData = $this->r_permission_role_model->select($thisSelect);

		if($thisData){
			echo '<table class="table table-hover">';
			echo '<thead>';
			echo '<tr>';
			echo '<th>#</th>';
			echo '<th>Class</th>';
			echo '<th>Action</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			foreach($thisData as $key => $value){
				echo '<tr>';
				echo '<td>'.($key + 1).'</td>';
				echo '<td>'.ucfirst($value->permission_class).'</td>';
				echo '<td>'.ucfirst($value->permission_action).'</td>';
				echo '</tr>';
			}
			echo '</tbody>';
			echo '</table>';
		}else{
			echo '<table class="table table-hover">';
			echo '<thead>';
			echo '<tr>';
			echo '<th>No record found</th>';
			echo '</tr>';
			echo '</thead>';
			echo '</table>';
		}
	}

	public function user()
	{
		$this->load->model('r_role_user_model');
			
		/* r_role_user */
		$thisSelect = array(
			'where' => array('user_id' => $this->input->post('thisRecordId'))
		);
		$thisData = $this->r_role_user_model->select($thisSelect);

		if($thisData){
			echo '<table class="table table-hover">';
			echo '<thead>';
			echo '<tr>';
			echo '<th>Role name</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			foreach($thisData as $key => $value){
				echo '<tr>';
				echo '<td>'.ucfirst($value->role_name).'</td>';
				echo '</tr>';
			}
			echo '</tbody>';
			echo '</table>';
		}else{
			echo '<table class="table table-hover">';
			echo '<thead>';
			echo '<tr>';
			echo '<th>No record found</th>';
			echo '</tr>';
			echo '</thead>';
			echo '</table>';
		}
	}

	public function log()
	{
		$this->load->model('log_model');
			
		/* log */
		$thisSelect = array(
			'where' => array('log_id' => $this->input->post('thisRecordId')),
			'return' => 'result'
		);
		$thisData = $this->log_model->select($thisSelect);

		if($thisData){
			echo '<table class="table table-hover">';
			echo '<thead>';
			echo '<tr>';
			echo '<th>SQL statement</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			foreach($thisData as $key => $value){
				echo '<tr>';
				echo '<td>'.$value->log_SQL.'</td>';
				echo '</tr>';
			}
			echo '</tbody>';
			echo '</table>';
		}else{
			echo '<table class="table table-hover">';
			echo '<thead>';
			echo '<tr>';
			echo '<th>No record found</th>';
			echo '</tr>';
			echo '</thead>';
			echo '</table>';
		}
	}

}
