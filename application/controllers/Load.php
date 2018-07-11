<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Load extends CI_Controller {

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
			if( $value->quotation_version ){
                echo '<td>R' . $value->quotation_version . '</td>';
            }else{
                echo '<td>N/A</td>';
            }
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

    public function productVendorLoader()
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
            echo 'function productVendorLoader(){';
            echo '$(".product-currency").html("'.$thisData2->currency_name.'")';
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

    public function approvalCodeLoader(){

    	$thisDiscount = $this->input->post('thisDiscount');
    	$thisTotal = $this->input->post('thisTotal');

        echo '<script>';
        echo 'function approvalCodeLoader(){';
        if( $thisTotal && floatval($thisDiscount/$thisTotal) > 0.3 ){
        	$html = '';
            $html .= '<tr class="tr-approval">';
            $html .= 	'<th></th>';
			$html .=	'<th></th>';
			$html .=	'<td></td>';
			$html .= 	'<td><label for="approval_code">Approval code</label></td>';
			$html .=	'<td><input id="approval_code" name="approval_code" type="text" class="form-control input-sm required approvalCodeCheckDuplicate" placeholder="Approval code" value="" /></td>';
			$html .= '</tr>';
			echo 'if( !($(".tr-approval").length > 0) ){';
			echo 	'$(".table-approval").append(\''.$html.'\');';
			echo 	'$(".tr-approval").css("display", "none").fadeIn();';
			echo '}';
        }else{
        	echo '$(".tr-approval").remove();';
        }
        echo '}';
        echo '</script>';
    }

    public function approvalCodeCheckDuplicate(){
    	$this->load->model('user_model');

    	$approval_code = $this->input->post('approvalCode');

    	$thisSelect = array(
            'where' => array('user_code' => $approval_code),
            'return' => 'row'
        );
        $user = $this->user_model->select($thisSelect);
        if( $approval_code && $user ) {
            echo $user->user_id;
        }
    }

    public function productCheckDuplicate(){
    	$this->load->model('product_model');

    	$product_id = trim($this->input->post('thisProductId'));
    	$product_name = trim($this->input->post('productName'));
    	$product_code = trim($this->input->post('productCode'));
    	$product_wpp_code = trim($this->input->post('productWppCode'));

    	if( !empty($product_name) ){
    		$thisSelect = array(
	            'where' => array('product_id_noteq' => $product_id, 'product_name' => $product_name),
	            'return' => 'row'
	        );
	        $product = $this->product_model->select($thisSelect);
	        if( $product ) {
	            echo $product->product_id;
	        }
    	}else if( !empty($product_code) ){
    		$thisSelect = array(
	            'where' => array('product_id_noteq' => $product_id, 'product_code' => $product_code),
	            'return' => 'row'
	        );
	        $product = $this->product_model->select($thisSelect);
	        if( $product ) {
	            echo $product->product_id;
	        }
    	}else if( !empty($product_wpp_code) ){
    		$thisSelect = array(
	            'where' => array('product_id_noteq' => $product_id, 'product_wpp_code' => $product_wpp_code),
	            'return' => 'row'
	        );
	        $product = $this->product_model->select($thisSelect);
	        if( $product ) {
	            echo $product->product_id;
	        }
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
        $this->load->model('currency_model');
			
		/* client */
		$thisSelect = array(
			'where' => array('product_id' => $this->input->post('thisRecordId')),
			'return' => 'row'
		);
		$thisData = $this->product_model->select($thisSelect);

        /* currency */
        $thisSelect = array(
            'where' => array('currency_name' => $this->input->post('thisCurrency')),
            'return' => 'row'
        );
        $thisCurrency = $this->currency_model->select($thisSelect);

		if($thisData){
			echo '<script>';
			echo 'function quotationProductLoader(){';
//			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'quotationitem_product_type_name[]\']").val("'.get_type($thisData->product_type_id)->type_name.'");';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'quotationitem_category_id[]\']").val("'.$thisData->product_category_id.'").css("display", "none").fadeIn();';
			echo 'if( $("select[name=\'quotation_display_code\']").val() == "code" ){';
                echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'quotationitem_product_code[]\']").val("'.$thisData->product_code.'").css("display", "none").fadeIn();';
            echo '}else {';
            	$product_code = $thisData->product_wpp_code?$thisData->product_wpp_code:$thisData->product_code;
                echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'quotationitem_product_code[]\']").val("'.$product_code.'").css("display", "none").fadeIn();';
            echo '}';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'quotationitem_product_name[]\']").val("'.$thisData->product_name.'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'quotationitem_product_link[]\']").val("'.$thisData->product_link.'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'quotationitem_unit[]\']").val("'.get_unit($thisData->product_unit_id)->unit_name.'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') .quotationitem_unit").html("'.get_unit($thisData->product_unit_id)->unit_name.'").css("display", "none").fadeIn();';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') textarea[name=\'quotationitem_product_detail[]\']").val("'.convert_nl('SIZE: '.get_product_size($thisData->product_id)->attribute_name.'\nREPEAT: '.$thisData->product_repeat).'").css("display", "none").fadeIn();';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'quotationitem_product_price[]\']").val("'.money_format('%!n', $thisData->product_price_hkd/$thisCurrency->currency_exchange_rate).'").css("display", "none").fadeIn();';
			echo 'category_discount();';
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
            $product_code = $thisData->product_wpp_code?$thisData->product_wpp_code:$thisData->product_code;
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'quotationitem_product_code[]\']").val("'.$product_code.'").css("display", "none").fadeIn();';
            echo '}';
            echo '}';
            echo '</script>';
        }
    }

    public function quotationCategoryDiscountLoader()
	{
		$this->load->model('category_model');

		$thisRecordId = $this->input->post('thisRecordId')?$this->input->post('thisRecordId'):array(0);
		/* salesorder */
		$thisSelect = array(
			'where' => array('category_id_in' => $thisRecordId),
			'return' => 'result'
		);
		$thisData = $this->category_model->select($thisSelect);

		$existsId = $this->input->post('existsRecordId')?$this->input->post('existsRecordId'):array();
		$categoryId = $thisData?convert_object_to_array($thisData, 'category_id'):array();

		echo '<script>';
		echo 'function quotationCategoryDiscountLoader(){';
		if($thisData){
			foreach ($thisData as $key => $value) {
				if( !in_array($value->category_id, $existsId) ){
					$code = '<tr id=\"category_discount_'.$value->category_id.'\"> \
								<th colspan=\"3\" style=\"text-align:right\"> \
									<input name=\"category_id[]\" type=\"hidden\" value=\"'.$value->category_id.'\" /> \
									<input name=\"category_name[]\" type=\"hidden\" value=\"'.$value->category_name.'\" /> \
									'.$value->category_name.' discount \
								</th> \
								<td> \
									<select name=\"category_discount_type[]\" data-placeholder=\"Discount type\" class=\"chosen-select required\"> \
										<option value=\"percent\">Percent</option> \
										<option value=\"fixed\">Fixed</option> \
									</select> \
								</td> \
								<th> \
									<input name=\"category_discount_value[]\" type=\"number\" min=\"0\" step=\"0.01\" class=\"form-control input-sm required\" placeholder=\"Discount value\" value=\"0.00\" /> \
								</th> \
								<th> \
									<input exists=\"'.$value->category_id.'\" name=\"category_discount[]\" type=\"number\" min=\"0\" step=\"0.01\" class=\"form-control input-sm required\" placeholder=\"Discount\" value=\"0.00\" /> \
                                </th> \
							</tr>';
					echo '$("#category_discount").prepend("'.$code.'");';
					echo '$(".chosen-select").chosen();';
				}
			}
		}
		foreach ($existsId as $key => $value) {
			if( !in_array($value, $categoryId) ){
				echo '$("#category_discount_'.$value.'").remove();';
			}
		}
		echo '}';
		echo '</script>';
	}

	public function salesorderProductLoader()
	{
		$this->load->model('product_model');
        $this->load->model('currency_model');
			
		/* product */
		$thisSelect = array(
			'where' => array('product_id' => $this->input->post('thisRecordId')),
			'return' => 'row'
		);
		$thisData = $this->product_model->select($thisSelect);

        /* currency */
        $thisSelect = array(
            'where' => array('currency_name' => $this->input->post('thisCurrency')),
            'return' => 'row'
        );
        $thisCurrency = $this->currency_model->select($thisSelect);

		if($thisData){
			echo '<script>';
			echo 'function salesorderProductLoader(){';
//			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'salesorderitem_product_type_name[]\']").val("'.get_type($thisData->product_type_id)->type_name.'");';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'salesorderitem_product_code[]\']").val("'.$thisData->product_code.'").css("display", "none").fadeIn();';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'salesorderitem_product_name[]\']").val("'.$thisData->product_name.'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'salesorderitem_unit[]\']").val("'.get_unit($thisData->product_unit_id)->unit_name.'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') .salesorderitem_unit").html("'.get_unit($thisData->product_unit_id)->unit_name.'").css("display", "none").fadeIn();';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') textarea[name=\'salesorderitem_product_detail[]\']").val("'.convert_nl('SIZE: '.get_product_size($thisData->product_id)->attribute_name.'\nREPEAT: '.$thisData->product_repeat).'").css("display", "none").fadeIn();';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'salesorderitem_product_price[]\']").val("'.money_format('%!n', $thisData->product_price_hkd/$thisCurrency->currency_exchange_rate).'").css("display", "none").fadeIn();';
			echo '}';
			echo '</script>';
		}
	}

	public function purchaseorderProductLoader()
	{
		$this->load->model('product_model');
		$this->load->model('currency_model');
			
		/* product */
		$thisSelect = array(
			'where' => array('product_id' => $this->input->post('thisRecordId')),
			'return' => 'row'
		);
		$thisData = $this->product_model->select($thisSelect);

        /* currency */
        $thisSelect = array(
            'where' => array('currency_name' => $this->input->post('thisCurrency')),
            'return' => 'row'
        );
        $thisCurrency = $this->currency_model->select($thisSelect);

		if($thisData){
			echo '<script>';
			echo 'function purchaseorderProductLoader(){';
//			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'purchaseorderitem_product_type_name[]\']").val("'.get_type($thisData->product_type_id)->type_name.'");';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'purchaseorderitem_product_code[]\']").val("'.$thisData->product_code.'").css("display", "none").fadeIn();';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'purchaseorderitem_product_name[]\']").val("'.$thisData->product_name.'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'purchaseorderitem_unit[]\']").val("'.get_unit($thisData->product_unit_id)->unit_name.'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') .purchaseorderitem_unit").html("'.get_unit($thisData->product_unit_id)->unit_name.'").css("display", "none").fadeIn();';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') textarea[name=\'purchaseorderitem_product_detail[]\']").val("'.convert_nl('SIZE: '.get_product_size($thisData->product_id)->attribute_name.'\nREPEAT: '.$thisData->product_repeat).'").css("display", "none").fadeIn();';
			echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'purchaseorderitem_product_price[]\']").val("'.money_format('%!n', $thisData->product_cost).'").css("display", "none").fadeIn();';
			echo '}';
			echo '</script>';
		}
	}

    public function invoiceProductLoader()
    {
        $this->load->model('product_model');
        $this->load->model('currency_model');

        /* product */
        $thisSelect = array(
            'where' => array('product_id' => $this->input->post('thisRecordId')),
            'return' => 'row'
        );
        $thisData = $this->product_model->select($thisSelect);

        /* currency */
        $thisSelect = array(
            'where' => array('currency_name' => $this->input->post('thisCurrency')),
            'return' => 'row'
        );
        $thisCurrency = $this->currency_model->select($thisSelect);

        if($thisData){
            echo '<script>';
            echo 'function invoiceProductLoader(){';
//            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'invoiceitem_product_type_name[]\']").val("'.get_type($thisData->product_type_id)->type_name.'");';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'invoiceitem_product_code[]\']").val("'.$thisData->product_code.'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'invoiceitem_product_name[]\']").val("'.$thisData->product_name.'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'invoiceitem_unit[]\']").val("'.get_unit($thisData->product_unit_id)->unit_name.'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') .invoiceitem_unit").html("'.get_unit($thisData->product_unit_id)->unit_name.'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') textarea[name=\'invoiceitem_product_detail[]\']").val("'.convert_nl('SIZE: '.get_product_size($thisData->product_id)->attribute_name.'\nREPEAT: '.$thisData->product_repeat).'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'invoiceitem_product_price[]\']").val("'.money_format('%!n', $thisData->product_price_hkd/$thisCurrency->currency_exchange_rate).'").css("display", "none").fadeIn();';
            echo '}';
            echo '</script>';
        }
    }

    public function deliverynoteProductLoader()
    {
        $this->load->model('product_model');
        $this->load->model('currency_model');

        /* product */
        $thisSelect = array(
            'where' => array('product_id' => $this->input->post('thisRecordId')),
            'return' => 'row'
        );
        $thisData = $this->product_model->select($thisSelect);

        /* currency */
        $thisSelect = array(
            'where' => array('currency_name' => $this->input->post('thisCurrency')),
            'return' => 'row'
        );
        $thisCurrency = $this->currency_model->select($thisSelect);

        if($thisData){
            echo '<script>';
            echo 'function deliverynoteProductLoader(){';
//            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'deliverynoteitem_product_type_name[]\']").val("'.get_type($thisData->product_type_id)->type_name.'");';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'deliverynoteitem_product_code[]\']").val("'.$thisData->product_code.'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'deliverynoteitem_product_name[]\']").val("'.$thisData->product_name.'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'deliverynoteitem_unit[]\']").val("'.get_unit($thisData->product_unit_id)->unit_name.'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') .deliverynoteitem_unit").html("'.get_unit($thisData->product_unit_id)->unit_name.'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') textarea[name=\'deliverynoteitem_product_detail[]\']").val("'.convert_nl('SIZE: '.get_product_size($thisData->product_id)->attribute_name.'\nREPEAT: '.$thisData->product_repeat).'").css("display", "none").fadeIn();';
            echo '$("table.list tbody tr:eq('.$this->input->post('thisRow').') input[name=\'deliverynoteitem_product_price[]\']").val("'.money_format('%!n', $thisData->product_price_hkd/$thisCurrency->currency_exchange_rate).'").css("display", "none").fadeIn();';
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

	public function paymentLoader()
	{
		$this->load->model('payment_model');
			
		/* terms */
		$thisSelect = array(
			'where' => array(
				'payment_type' => $this->input->post('thisType'),
				'payment_language' => $this->input->post('thisLanguage')
			),
			'return' => 'row'
		);
		$thisData = $this->payment_model->select($thisSelect);

		if($thisData){
			echo $thisData->payment_content;
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

	public function product()
	{
		$this->load->model('product_model');
			
		/* product */
		$thisSelect = array(
			'where' => array('product_id' => $this->input->post('thisRecordId')),
			'return' => 'row'
		);
		$thisData = $this->product_model->select($thisSelect);

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
