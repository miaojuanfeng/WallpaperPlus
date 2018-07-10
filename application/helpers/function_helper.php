<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('get_purchaseorder'))
{
	function get_purchaseorder($thisId){
		$CI =& get_instance();
		$CI->load->model('purchaseorder_model');

		/* purchaseorder */
		$thisSelect = array(
			'where' => array(
				'purchaseorder_id' => $thisId
			),
			'return' => 'row'
		);
		$data = $CI->purchaseorder_model->select($thisSelect);

		if($data){
			return $data;
		}else{
			return false;
		}
	}
}

if(!function_exists('get_invoice'))
{
	function get_invoice($thisId){
		$CI =& get_instance();
		$CI->load->model('invoice_model');

		/* invoice */
		$thisSelect = array(
			'where' => array(
				'invoice_id' => $thisId
			),
			'return' => 'row'
		);
		$data = $CI->invoice_model->select($thisSelect);

		if($data){
			return $data;
		}else{
			return false;
		}
	}
}

if(!function_exists('get_vendor'))
{
	function get_vendor($thisId){
		$CI =& get_instance();
		$CI->load->model('vendor_model');

		/* vendor */
		$thisSelect = array(
			'where' => array(
				'vendor_id' => $thisId
			),
			'return' => 'row'
		);
		$data = $CI->vendor_model->select($thisSelect);

		if($data){
			return $data;
		}else{
			return false;
		}
	}
}

if(!function_exists('get_client'))
{
	function get_client($thisId){
		$CI =& get_instance();
		$CI->load->model('client_model');

		/* vendor */
		$thisSelect = array(
			'where' => array(
				'client_id' => $thisId
			),
			'return' => 'row'
		);
		$data = $CI->client_model->select($thisSelect);

		if($data){
			return $data;
		}else{
			return false;
		}
	}
}

if(!function_exists('get_category'))
{
    function get_category($thisId){
        $CI =& get_instance();
        $CI->load->model('category_model');

        /* category */
        $thisSelect = array(
            'where' => array(
                'category_id' => $thisId
            ),
            'return' => 'row'
        );
        $data = $CI->category_model->select($thisSelect);

        if($data){
            return $data;
        }else{
            return false;
        }
    }
}

if(!function_exists('get_currency'))
{
    function get_currency($thisId){
        $CI =& get_instance();
        $CI->load->model('currency_model');

        /* vendor */
        $thisSelect = array(
            'where' => array(
                'currency_id' => $thisId
            ),
            'return' => 'row'
        );
        $data = $CI->currency_model->select($thisSelect);

        if($data){
            return $data;
        }else{
            return false;
        }
    }
}

if(!function_exists('get_product'))
{
	function get_product($thisId){
		$CI =& get_instance();
		$CI->load->model('product_model');

		/* product */
		$thisSelect = array(
			'where' => array(
				'product_id' => $thisId
			),
			'return' => 'row'
		);
		$data = $CI->product_model->select($thisSelect);

		if($data){
			return $data;
		}else{
			return false;
		}
	}
}

if(!function_exists('get_quotation'))
{
	function get_quotation($thisId){
		$CI =& get_instance();
		$CI->load->model('quotation_model');

		/* type */
		$thisSelect = array(
			'where' => array(
				'quotation_id' => $thisId
			),
			'return' => 'row'
		);
		$data = $CI->quotation_model->select($thisSelect);

		if($data){
			return $data;
		}else{
			return false;
		}
	}
}

if(!function_exists('get_salesorder'))
{
	function get_salesorder($thisId){
		$CI =& get_instance();
		$CI->load->model('salesorder_model');

		/* type */
		$thisSelect = array(
			'where' => array(
				'salesorder_id' => $thisId
			),
			'return' => 'row'
		);
		$data = $CI->salesorder_model->select($thisSelect);

		if($data){
			return $data;
		}else{
			return false;
		}
	}
}

if(!function_exists('get_terms'))
{
	function get_terms($thisId){
		$CI =& get_instance();
		$CI->load->model('terms_model');

		/* type */
		$thisSelect = array(
			'where' => array(
				'terms_id' => $thisId
			),
			'return' => 'row'
		);
		$data = $CI->terms_model->select($thisSelect);

		if($data){
			return $data;
		}else{
			return false;
		}
	}
}

if(!function_exists('get_type'))
{
	function get_type($thisId){
		$CI =& get_instance();
		$CI->load->model('type_model');

		/* type */
		$thisSelect = array(
			'where' => array(
				'type_id' => $thisId
			),
			'return' => 'row'
		);
		$data = $CI->type_model->select($thisSelect);

		if($data){
			return $data;
		}else{
			return false;
		}
	}
}

if(!function_exists('get_unit'))
{
    function get_unit($thisId){
        $CI =& get_instance();
        $CI->load->model('unit_model');

        /* type */
        $thisSelect = array(
            'where' => array(
                'unit_id' => $thisId
            ),
            'return' => 'row'
        );
        $data = $CI->unit_model->select($thisSelect);

        if($data){
            return $data;
        }else{
            return false;
        }
    }
}

if(!function_exists('get_location'))
{
	function get_location($thisId){
		$CI =& get_instance();
		$CI->load->model('location_model');

		/* location */
		$thisSelect = array(
			'where' => array(
				'location_id' => $thisId
			),
			'return' => 'row'
		);
		$data = $CI->location_model->select($thisSelect);

		if($data){
			return $data;
		}else{
			return false;
		}
	}
}

if(!function_exists('get_warehouse'))
{
	function get_warehouse($thisId){
		$CI =& get_instance();
		$CI->load->model('warehouse_model');

		/* warehouse */
		$thisSelect = array(
			'where' => array(
				'warehouse_id' => $thisId
			),
			'return' => 'row'
		);
		$data = $CI->warehouse_model->select($thisSelect);

		if($data){
			return $data;
		}else{
			return false;
		}
	}
}

if(!function_exists('get_product_warehouse'))
{
	function get_product_warehouse($thisProductId){
		$CI =& get_instance();
		$CI->load->model('z_product_warehouse_model');
		$CI->load->model('warehouse_model');

		/* setting */
		$thisSelect = array(
			'where' => array(
				'product_id' => $thisProductId,
				'z_product_warehouse_quantity_noteq' => 0
			),
			'return' => 'row'
		);
		$data = $CI->z_product_warehouse_model->select($thisSelect);

		if($data){
			/* warehouse */
			$thisSelect = array(
				'where' => array(
					'warehouse_id' => $data->warehouse_id
				),
				'return' => 'row'
			);
			$data = $CI->warehouse_model->select($thisSelect);

			if($data){
				return $data;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
}

if(!function_exists('get_user'))
{
	function get_user($thisId){
		$CI =& get_instance();
		$CI->load->model('user_model');

		/* user */
		$thisSelect = array(
			'where' => array(
				'user_id' => $thisId
			),
			'return' => 'row'
		);
		$data = $CI->user_model->select($thisSelect);

		if($data){
			return $data;
		}else{
			return false;
		}
	}
}

if(!function_exists('get_role'))
{
	function get_role($thisId){
		$CI =& get_instance();
		$CI->load->model('role_model');

		/* role */
		$thisSelect = array(
			'where' => array(
				'role_id' => $thisId
			),
			'return' => 'row'
		);
		$data = $CI->role_model->select($thisSelect);

		if($data){
			return $data;
		}else{
			return false;
		}
	}
}

if(!function_exists('get_setting'))
{
	function get_setting($thisSettingName){
		$CI =& get_instance();
		$CI->load->model('setting_model');

		/* setting */
		$thisSelect = array(
			'where' => array(
				'setting_name' => $thisSettingName
			),
			'return' => 'row'
		);
		$data = $CI->setting_model->select($thisSelect);

		if($data){
			return $data;
		}else{
			return false;
		}
	}
}


if(!function_exists('get_product_size'))
{
    function get_product_size($thisId){
        $CI =& get_instance();
        $CI->load->model('z_product_attribute_model');
        $CI->load->model('attribute_model');

        $data = null;

        /* product */
        $thisSelect = array(
            'where' => array(
                'product_id' => $thisId
            ),
            'return' => 'result'
        );
        $z_product_attribute_attribute_ids = convert_object_to_array($CI->z_product_attribute_model->select($thisSelect), 'z_product_attribute_attribute_id');

//        echo "ok";
//        var_dump($z_product_attribute_attribute_ids);

        if( $z_product_attribute_attribute_ids ){
            /* size */
            $thisSelect = array(
                'where' => array('attribute_type' => 'size'),
                'return' => 'result'
            );
            $sizes = $CI->attribute_model->select($thisSelect);

            foreach ($sizes as $key => $value){
                if( in_array($value->attribute_id, $z_product_attribute_attribute_ids) ){
                    $data = $value;
                    break;
                }
            }
        }

        if( !$data ){
            $thisArray = array();
            foreach($CI->attribute_model->structure() as $key => $value){
                $thisArray[$value->Field] = '';
            }
            $data = (object)$thisArray;
        }

        return $data;
    }
}

if(!function_exists('get_team'))
{
    function get_team($thisId){
        $CI =& get_instance();
        $CI->load->model('team_model');

        /* team */
        $thisSelect = array(
            'where' => array(
                'team_id' => $thisId
            ),
            'return' => 'row'
        );
        $data = $CI->team_model->select($thisSelect);

        if($data){
            return $data;
        }else{
            return false;
        }
    }
}

if(!function_exists('get_expire_period'))
{
	function get_expire_period($thisExpire){
		$CI =& get_instance();

		switch(true){
			case ($thisExpire <= date('Y-m-d', strtotime('+30 days'))):
				$data = '<=30';
				break;
			case ($thisExpire <= date('Y-m-d', strtotime('+60 days'))):
				$data = '31-60';
				break;
			case ($thisExpire <= date('Y-m-d', strtotime('+90 days'))):
				$data = '61-90';
				break;
			default:
				$data = '>=91';
				break;
		}

		if($data){
			return $data;
		}else{
			return false;
		}
	}
}

if(!function_exists('get_z_product_warehouse_quantity'))
{
	function get_z_product_warehouse_quantity($thisProductId, $thisWarehouseId){
		$CI =& get_instance();
		$CI->load->model('z_product_warehouse_model');

		/* setting */
		$thisSelect = array(
			'where' => array(
				'product_id' => $thisProductId,
				'warehouse_id' => $thisWarehouseId
			),
			'return' => 'row'
		);
		$data = $CI->z_product_warehouse_model->select($thisSelect);

		if($data){
			return $data->z_product_warehouse_quantity;
		}else{
			$thisData['z_product_warehouse_product_id'] = $thisProductId;
			$thisData['z_product_warehouse_warehouse_id'] = $thisWarehouseId;
			$thisData['z_product_warehouse_quantity'] = 0;
			$CI->z_product_warehouse_model->insert($thisData);

			return false;
		}
	}
}

if(!function_exists('get_quotation_version'))
{
	function get_quotation_version($thisQuotationNumber){
		$CI =& get_instance();
		$CI->load->model('quotation_model');

		/* quotation */
		$thisSelect = array(
			'select' => array(
				'*',
				'max(quotation_version) as max_quotation_version',
			),
			'where' => array(
				'quotation_number' => $thisQuotationNumber
			),
			'group' => 'YEAR(quotation_create), MONTH(quotation_create)',
			'return' => 'row'
		);
		$data = $CI->quotation_model->select($thisSelect);

		if($data){
			return $data->max_quotation_version;
		}else{
			return 0;
		}
	}
}

if(!function_exists('get_quotation_serial'))
{
	function get_quotation_serial(){
		$CI =& get_instance();
		$CI->load->model('quotation_model');

		/* quotation */
		// SELECT *, max(quotation_serial) as max_quotation_serial FROM quotation where month(quotation_create) = month(curdate()) and year(quotation_create) = year(curdate()) group by year(quotation_create), month(quotation_create)
		$thisSelect = array(
			'select' => array(
				'*',
				'max(quotation_serial) as max_quotation_serial',
			),
			'where' => array(
				'MONTH(quotation_create)' => 'MONTH(CURDATE())',
				'YEAR(quotation_create)' => 'YEAR(CURDATE())'
			),
			'group' => 'YEAR(quotation_create), MONTH(quotation_create)',
			'return' => 'row'
		);
		$data = $CI->quotation_model->select($thisSelect);

		if($data){
			return $data->max_quotation_serial;
		}else{
			return 0;
		}
	}
}

if(!function_exists('get_salesorder_serial'))
{
	function get_salesorder_serial(){
		$CI =& get_instance();
		$CI->load->model('salesorder_model');

		/* quotation */
		$thisSelect = array(
			'select' => array(
				'*',
				'max(salesorder_serial) as max_salesorder_serial',
			),
			'where' => array(
				'MONTH(salesorder_create)' => 'MONTH(CURDATE())',
				'YEAR(salesorder_create)' => 'YEAR(CURDATE())'
			),
			'group' => 'YEAR(salesorder_create), MONTH(salesorder_create)',
			'return' => 'row'
		);
		$data = $CI->salesorder_model->select($thisSelect);

		if($data){
			return $data->max_salesorder_serial;
		}else{
			return 0;
		}
	}
}

if(!function_exists('get_purchaseorder_serial'))
{
	function get_purchaseorder_serial(){
		$CI =& get_instance();
		$CI->load->model('purchaseorder_model');

		/* purchaseorder */
		$thisSelect = array(
			'select' => array(
				'*',
				'max(purchaseorder_serial) as max_purchaseorder_serial',
			),
			'where' => array(
				'MONTH(purchaseorder_create)' => 'MONTH(CURDATE())',
				'YEAR(purchaseorder_create)' => 'YEAR(CURDATE())'
			),
			'group' => 'YEAR(purchaseorder_create), MONTH(purchaseorder_create)',
			'return' => 'row'
		);
		$data = $CI->purchaseorder_model->select($thisSelect);

		if($data){
			return $data->max_purchaseorder_serial;
		}else{
			return 0;
		}
	}
}

if(!function_exists('get_invoice_serial'))
{
	function get_invoice_serial(){
		$CI =& get_instance();
		$CI->load->model('invoice_model');

		/* invoice */
		$thisSelect = array(
			'select' => array(
				'*',
				'max(invoice_serial) as max_invoice_serial',
			),
			'where' => array(
				'MONTH(invoice_create)' => 'MONTH(CURDATE())',
				'YEAR(invoice_create)' => 'YEAR(CURDATE())'
			),
			'group' => 'YEAR(invoice_create), MONTH(invoice_create)',
			'return' => 'row'
		);
		$data = $CI->invoice_model->select($thisSelect);

		if($data){
			return $data->max_invoice_serial;
		}else{
			return 0;
		}
	}
}

if(!function_exists('get_proformainvoice_serial'))
{
	function get_proformainvoice_serial(){
		$CI =& get_instance();
		$CI->load->model('proformainvoice_model');

		/* invoice */
		$thisSelect = array(
			'select' => array(
				'*',
				'max(proformainvoice_serial) as max_proformainvoice_serial',
			),
			'where' => array(
				'MONTH(proformainvoice_create)' => 'MONTH(CURDATE())',
				'YEAR(proformainvoice_create)' => 'YEAR(CURDATE())'
			),
			'group' => 'YEAR(proformainvoice_create), MONTH(proformainvoice_create)',
			'return' => 'row'
		);
		$data = $CI->proformainvoice_model->select($thisSelect);

		if($data){
			return $data->max_proformainvoice_serial;
		}else{
			return 0;
		}
	}
}

if(!function_exists('get_deliverynote_serial'))
{
	function get_deliverynote_serial(){
		$CI =& get_instance();
		$CI->load->model('deliverynote_model');

		/* deliverynote */
		$thisSelect = array(
			'select' => array(
				'*',
				'max(deliverynote_serial) as max_deliverynote_serial',
			),
			'where' => array(
				'MONTH(deliverynote_create)' => 'MONTH(CURDATE())',
				'YEAR(deliverynote_create)' => 'YEAR(CURDATE())'
			),
			'group' => 'YEAR(deliverynote_create), MONTH(deliverynote_create)',
			'return' => 'row'
		);
		$data = $CI->deliverynote_model->select($thisSelect);

		if($data){
			return $data->max_deliverynote_serial;
		}else{
			return 0;
		}
	}
}

if(!function_exists('get_invoice_paid'))
{
	function get_invoice_paid($thisInvoiceQuotationNumber){
		$CI =& get_instance();
		$CI->load->model('invoice_model');

		/* quotation */
		$thisSelect = array(
			'select' => array(
				'*',
				'sum(invoice_pay) as invoice_paid',
			),
			'where' => array(
				'invoice_quotation_number' => $thisInvoiceQuotationNumber
			),
			'group' => 'invoice_quotation_number',
			'return' => 'row'
		);
		$data = $CI->invoice_model->select($thisSelect);

		if($data){
			return $data->invoice_paid;
		}else{
			return 0;
		}
	}
}

if(!function_exists('get_proformainvoice_paid'))
{
	function get_proformainvoice_paid($thisProformainvoiceQuotationNumber){
		$CI =& get_instance();
		$CI->load->model('proformainvoice_model');

		/* quotation */
		$thisSelect = array(
			'select' => array(
				'*',
				'sum(proformainvoice_pay) as proformainvoice_paid',
			),
			'where' => array(
				'proformainvoice_quotation_number' => $thisProformainvoiceQuotationNumber
			),
			'group' => 'proformainvoice_quotation_number',
			'return' => 'row'
		);
		$data = $CI->proformainvoice_model->select($thisSelect);

		if($data){
			return $data->proformainvoice_paid;
		}else{
			return 0;
		}
	}
}

if(!function_exists('get_salesorder_cost'))
{
	function get_salesorder_cost($thisSalesorderId){
		$CI =& get_instance();
		$CI->load->model('purchaseorder_model');

		/* purchaseorder */
		$thisSelect = array(
			'select' => array(
				'sum(purchaseorder_total) as salesorder_cost',
			),
			'where' => array(
				'purchaseorder_salesorder_id' => $thisSalesorderId,
				'purchaseorder_status_noteq' => 'cancel'
			),
			'return' => 'row'
		);
		$data = $CI->purchaseorder_model->select($thisSelect);

		if($data){
			return $data->salesorder_cost;
		}else{
			return 0;
		}
	}
}

if(!function_exists('get_salesorderitem_quantity'))
{
	function get_salesorderitem_quantity($thisSalesorderId, $thisPurchaseorderItemId){
		$CI =& get_instance();
		$CI->load->model('salesorderitem_model');

		/* salesorder item */
		$thisSelect = array(
			'where' => array(
				'salesorderitem_salesorder_id' => $thisSalesorderId,
				'salesorderitem_product_id' => $thisPurchaseorderItemId,
			),
			'return' => 'row'
		);
		$data = $CI->salesorderitem_model->select($thisSelect);

		if($data){
			return $data->salesorderitem_quantity;
		}else{
			return 0;
		}
	}
}

if(!function_exists('get_purchaseorderitem_issued_quantity'))
{
	function get_purchaseorderitem_issued_quantity($thisSalesorderId, $thisPurchaseorderId, $thisPurchaseorderItemId){
		$CI =& get_instance();
		$CI->load->model('purchaseorder_model');
		$CI->load->model('purchaseorderitem_model');

		/* purchaseorder */
		$thisSelect = array(
			'where' => array(
				'purchaseorder_salesorder_id' => $thisSalesorderId,
				'purchaseorder_status_noteq' => 'cancel'
			),
			'return' => 'result'
		);
		$data['purchaseorders'] = $CI->purchaseorder_model->select($thisSelect);

		if($data['purchaseorders']){
			$purchaseorder_ids = array();
			foreach($data['purchaseorders'] as $key => $value){
				$purchaseorder_ids[] = $value->purchaseorder_id;
			}

			/* purchaseorder item */
			$thisSelect = array(
				'select' => array(
					'sum(purchaseorderitem_quantity) as purchaseorderitem_bought',
				),
				'where' => array(
					'purchaseorderitem_purchaseorder_id_in' => $purchaseorder_ids,
					'purchaseorderitem_product_id' => $thisPurchaseorderItemId,
					'purchaseorderitem_purchaseorder_id_noteq' => $thisPurchaseorderId,
				),
				'return' => 'row'
			);
			$data = $CI->purchaseorderitem_model->select($thisSelect);

			if($data){
				return $data->purchaseorderitem_bought;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}
}

if(!function_exists('get_invoiceitem_issued_quantity'))
{
	function get_invoiceitem_issued_quantity($thisSalesorderId, $thisInvoiceId, $thisInvoiceItemId){
		$CI =& get_instance();
		$CI->load->model('invoice_model');
		$CI->load->model('invoiceitem_model');

		/* invoice */
		$thisSelect = array(
			'where' => array(
				'invoice_salesorder_id' => $thisSalesorderId,
				'invoice_status_noteq' => 'cancel'
			),
			'return' => 'result'
		);
		$data['invoices'] = $CI->invoice_model->select($thisSelect);

		if($data['invoices']){
			$invoice_ids = array();
			foreach($data['invoices'] as $key => $value){
				$invoice_ids[] = $value->invoice_id;
			}

			/* invoice item */
			$thisSelect = array(
				'select' => array(
					'sum(invoiceitem_quantity) as invoiceitem_sold',
				),
				'where' => array(
					'invoiceitem_invoice_id_in' => $invoice_ids,
					'invoiceitem_product_id' => $thisInvoiceItemId,
					'invoiceitem_invoice_id_noteq' => $thisInvoiceId,
				),
				'return' => 'row'
			);
			$data = $CI->invoiceitem_model->select($thisSelect);

			if($data){
				return $data->invoiceitem_sold;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}
}

// if(!function_exists('get_quotation_version'))
// {
// 	function get_quotation_version($thisQuotationNumber){
// 		$CI =& get_instance();
// 		$CI->load->model('quotation_model');

// 		/* quotation */
// 		$thisSelect = array(
// 			'select' => array(
// 				'*',
// 				'max(quotation_version) as max_quotation_version',
// 			),
// 			'where' => array(
// 				'quotation_number' => $thisQuotationNumber
// 			),
// 			'group' => 'quotation_number',
// 			'return' => 'row'
// 		);
// 		$data = $CI->quotation_model->select($thisSelect);

// 		if($data){
// 			return $data->max_quotation_version;
// 		}else{
// 			return 0;
// 		}
// 	}
// }

if(!function_exists('convert_nl'))
{
	function convert_nl($thisText){
		return preg_replace("/(\r\n|\n|\r|\t)/i", '\n', $thisText);
	}
}

if(!function_exists('convert_br'))
{
	function convert_br($thisText){
		return preg_replace("/(\n)/i", '<br/>', $thisText);
	}
}

// if(!function_exists('resolve_token'))
// {
// 	function resolve_token($thisToken){
// 		$CI =& get_instance();
// 		$CI->load->library('encrypt');

// 		$thisSecret = 'DreamOver';
// 		$thisToken = base64_decode($thisToken);
// 		$thisToken_serialize = $CI->encrypt->decode($thisToken, $thisSecret);
// 		$thisToken_array = unserialize($thisToken_serialize);

// 		return $thisToken_array;
// 	}
// }

// if(!function_exists('check_token'))
// {
// 	function check_token($return_login_success = false){
// 		$CI =& get_instance();
// 		$CI->load->model('client_model');

// 		$thisValue = ($CI->input->method(true) == 'POST') ? $CI->input->post() : $CI->uri->uri_to_assoc(4) ;
// 		$thisSelect = array(
// 			'where' => array(
// 				'client_token' => $thisValue['client_token']
// 			),
// 			'return' => 'result'
// 		);
// 		$data['clients'] = $CI->client_model->select($thisSelect);

// 		if($data['clients']){
// 			$thisMessageArray = resolve_token($thisValue['client_token']);

// 			if($data['clients'][0]->client_device_id != $thisMessageArray['client_device_id']){
// 				return_JSON_format(WRONG_CLIENT_DEVICE_ID);
// 			}

// 			if($data['clients'][0]->client_last_use + $thisMessageArray['client_expire'] >= time()){
// 				if($data['clients'][0]->client_last_use + (60 * 60) >= time()){
// 					$thisPOST['client_id'] = $data['clients'][0]->client_id;
// 					$thisPOST['client_password'] = '';
// 					$thisPOST['client_last_use'] = time();
// 					$CI->client_model->update($thisPOST);
// 				}

// 				if($return_login_success){
// 					return_JSON_format(LOGIN_SUCCESS);	
// 				}
// 			}else{
// 				return_JSON_format(CLIENT_TOKEN_EXPIRED);
// 			}
// 		}else{
// 			return_JSON_format(WRONG_CLIENT_TOKEN);
// 		}
// 	}
// }

if(!function_exists('return_JSON_format'))
{
	function return_JSON_format($thisData){
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, dataType");
		header("Access-Control-Allow-Origin: *");
		header('Access-Control-Allow-Methods: GET, POST, PUT');
		header('Content-Type: application/json');
		echo json_encode($thisData, JSON_PRETTY_PRINT);
		exit;
	}
}

if(!function_exists('convert_quotation_to_salesorder'))
{
	function convert_quotation_to_salesorder($thisData){
		$thisResult = new stdClass();
		foreach($thisData as $key => $value){
			$key = str_replace('quotation', 'salesorder', $key);
			$thisResult->$key = $value;
		}
		return $thisResult;
	}
}

if(!function_exists('convert_salesorder_to_quotation'))
{
	function convert_salesorder_to_quotation($thisData){
		$thisResult = new stdClass();
		foreach($thisData as $key => $value){
			$key = str_replace('salesorder', 'quotation', $key);
			$thisResult->$key = $value;
		}
		return $thisResult;
	}
}

if(!function_exists('convert_quotationitems_to_salesorderitems'))
{
	function convert_quotationitems_to_salesorderitems($thisData){
		foreach($thisData as $key => $value){
			$thisResult[$key] = new stdClass();
			foreach($value as $key1 => $value1){
				$key1 = str_replace('quotation', 'salesorder', $key1);
				$thisResult[$key]->$key1 = $value1;
			}
		}
		return $thisResult;
	}
}

if(!function_exists('convert_salesorderitems_to_quotationitems'))
{
	function convert_salesorderitems_to_quotationitems($thisData){
		foreach($thisData as $key => $value){
			$thisResult[$key] = new stdClass();
			foreach($value as $key1 => $value1){
				$key1 = str_replace('salesorder', 'quotation', $key1);
				$thisResult[$key]->$key1 = $value1;
			}
		}
		return $thisResult;
	}
}

if(!function_exists('convert_salesorder_to_purchaseorder'))
{
	function convert_salesorder_to_purchaseorder($thisData){
		$thisResult = new stdClass();
		foreach($thisData as $key => $value){
			$key = str_replace('salesorder', 'purchaseorder', $key);
			$thisResult->$key = $value;
		}
		return $thisResult;
	}
}

if(!function_exists('convert_salesorderitems_to_purchaseorderitems'))
{
	function convert_salesorderitems_to_purchaseorderitems($thisData){
		foreach($thisData as $key => $value){
			$thisResult[$key] = new stdClass();
			foreach($value as $key1 => $value1){
				$key1 = str_replace('salesorder', 'purchaseorder', $key1);
				$thisResult[$key]->$key1 = $value1;
			}
		}
		return $thisResult;
	}
}

if(!function_exists('convert_salesorder_to_invoice'))
{
	function convert_salesorder_to_invoice($thisData){
		$thisResult = new stdClass();
		foreach($thisData as $key => $value){
			$key = str_replace('salesorder', 'invoice', $key);
			$thisResult->$key = $value;
		}
		return $thisResult;
	}
}

if(!function_exists('convert_salesorderitems_to_invoiceitems'))
{
	function convert_salesorderitems_to_invoiceitems($thisData){
		foreach($thisData as $key => $value){
			$thisResult[$key] = new stdClass();
			foreach($value as $key1 => $value1){
				$key1 = str_replace('salesorder', 'invoice', $key1);
				$thisResult[$key]->$key1 = $value1;
			}
		}
		return $thisResult;
	}
}

if(!function_exists('convert_salesorder_to_proformainvoice'))
{
	function convert_salesorder_to_proformainvoice($thisData){
		$thisResult = new stdClass();
		foreach($thisData as $key => $value){
			$key = str_replace('salesorder', 'proformainvoice', $key);
			$thisResult->$key = $value;
		}
		return $thisResult;
	}
}

if(!function_exists('convert_salesorderitems_to_proformainvoiceitems'))
{
	function convert_salesorderitems_to_proformainvoiceitems($thisData){
		foreach($thisData as $key => $value){
			$thisResult[$key] = new stdClass();
			foreach($value as $key1 => $value1){
				$key1 = str_replace('salesorder', 'proformainvoice', $key1);
				$thisResult[$key]->$key1 = $value1;
			}
		}
		return $thisResult;
	}
}

if(!function_exists('convert_salesorder_to_deliverynote'))
{
	function convert_salesorder_to_deliverynote($thisData){
		$thisResult = new stdClass();
		foreach($thisData as $key => $value){
			$key = str_replace('salesorder', 'deliverynote', $key);
			$thisResult->$key = $value;
		}
		return $thisResult;
	}
}

if(!function_exists('convert_salesorderitems_to_deliverynoteitems'))
{
	function convert_salesorderitems_to_deliverynoteitems($thisData){
		foreach($thisData as $key => $value){
			$thisResult[$key] = new stdClass();
			foreach($value as $key1 => $value1){
				$key1 = str_replace('salesorder', 'deliverynote', $key1);
				$thisResult[$key]->$key1 = $value1;
			}
		}
		return $thisResult;
	}
}

if(!function_exists('convert_formArray_to_DBArray'))
{
	function convert_formArray_to_DBArray($thisData, $thisKey){
		foreach($thisData[$thisKey] as $key => $value){
			foreach($thisData as $key1 => $value1){
				$thisResult[$key][$key1] = $thisData[$key1][$key];
			}
		}
		return $thisResult;
	}
}

if(!function_exists('get_pagination_config'))
{
	function get_pagination_config($per_page, $num_rows){
		$CI =& get_instance();
		$page_link = $CI->uri->uri_to_assoc();
		unset($page_link['page']);

		$config['base_url'] = base_url($CI->router->fetch_class().'/select/'.$CI->uri->assoc_to_uri($page_link).'/page');
		$config['total_rows'] = $num_rows;
		$config['per_page'] = $per_page;
		$config['num_links'] = 1;
		$config['first_link'] = '<<';
		$config['last_link'] = '>>';
		$config['full_tag_open'] = '<span class="pagination-area">';
		$config['full_tag_close'] = '</span>';

		return $config;
	}
}

if(!function_exists('get_pagination_js_config'))
{
    function get_pagination_js_config($url, $per_page, $num_rows){
        $CI =& get_instance();
        $page_link = $url;
        $current_page = $page_link['page'];
        $total_page = ceil($num_rows/$per_page);
        unset($page_link['page']);

        // $config['base_url'] = 'javascript:;';
        // $config['total_rows'] = $num_rows;
        // $config['per_page'] = $per_page;
        // $config['num_links'] = 1;
        // $config['first_link'] = '<<';
        // $config['last_link'] = '>>';
        // $config['cur_tag_open'] = '<strong class="btn btn-sm btn-primary disabled">';
        // $config['cur_tag_close'] = '</strong>';
        // $config['attributes'] = array('class' => 'btn btn-sm btn-primary', 'onclick' => 'changePage()');
        // $config['full_tag_open'] = '<span class="pagination-area">';
        // $config['full_tag_close'] = '</span>';

        $config = '';
        $config .= '<div class="page-area">';
        $config .= '<span class="btn btn-sm btn-default">'.$num_rows.'</span>';
        $config .= '<span class="pagination-area">';
        if( $current_page >= $per_page * 2 ){
        	$config .= '<a href="javascript:;" class="btn btn-sm btn-primary" onclick="changePage(0)">&lt;&lt;</a>';
        }
        if( $current_page >= $per_page ){
        	$config .= '<a href="javascript:;" class="btn btn-sm btn-primary" onclick="changePage('.($current_page-$per_page).')">&lt;</a>';
        }
        for ($i=1; $i <= $total_page; $i++) { 
        	$p_num = ($i-1)*$per_page;
        	if( $p_num == $current_page ){
        		$config .= '<strong class="btn btn-sm btn-primary disabled">'.$i.'</strong>';
        	}else{
        		if( $p_num == ($current_page-$per_page) || $p_num == ($current_page+$per_page) ){
        			$config .= '<a href="javascript:;" class="btn btn-sm btn-primary" onclick="changePage('.$p_num.')">'.$i.'</a>';
        		}
        	}
        }
        if( $current_page < $num_rows - $per_page  ){
        	$config .= '<a href="javascript:;" class="btn btn-sm btn-primary" onclick="changePage('.($current_page+$per_page).')">&gt;</a>';
        }
        if( $current_page <= $num_rows - $per_page * 2 ){
			$config .= '<a href="javascript:;" class="btn btn-sm btn-primary" onclick="changePage('.(($total_page -1 ) * $per_page).')">&gt;&gt;</a>';
		}
        $config .= '</span>';
        $config .= '</div>';

        return $config;
    }
}

if(!function_exists('get_order_link'))
{
	function get_order_link($order_field){
		$CI =& get_instance();

		/* convert order link */
		$thisOrderLink = $CI->uri->uri_to_assoc();
		unset($thisOrderLink['order']);
		unset($thisOrderLink['ascend']);

		/* get asc desc */
		$thisAscend = 'asc';
		if(isset($CI->uri->uri_to_assoc()['ascend'])){
			if($CI->uri->uri_to_assoc()['ascend'] == 'asc'){
				$thisAscend = 'desc';
			}else{
				$thisAscend = 'asc';	
			}
		}

		/* create link */
		return base_url(
			$CI->router->fetch_class().'/'
			.$CI->router->fetch_method()
			.'/order/'.$order_field
			.'/ascend/'.$thisAscend
			.'/'.$CI->uri->assoc_to_uri($thisOrderLink).'/'
		);
	}
}

// if(!function_exists('convert_order_link'))
// {
// 	function convert_order_link(){
// 		$CI =& get_instance();
// 		$order_link = $CI->uri->uri_to_assoc();
// 		unset($order_link['order']);
// 		unset($order_link['ascend']);
// 		return $order_link;
// 	}
// }

// if(!function_exists('get_asc_desc'))
// {
// 	function get_asc_desc(){
// 		$CI =& get_instance();
// 		$thisAscend = 'asc';
// 		if(isset($CI->uri->uri_to_assoc()['ascend'])){
// 			if($CI->uri->uri_to_assoc()['ascend'] == 'asc'){
// 				$thisAscend = 'desc';
// 			}else{
// 				$thisAscend = 'asc';	
// 			}
// 		}
// 		return $thisAscend;
// 	}
// }

// if(!function_exists('convert_get_to_slashes'))
// {
// 	function convert_get_to_slashes(){
// 		$CI =& get_instance();
// 		$thisGET = $CI->input->get();
// 		if($thisGET){
// 			foreach($thisGET as $key => $value){
// 				if($value == ''){
// 					unset($thisGET[$key]);
// 				}
// 			}
// 			redirect(base_url($CI->router->fetch_class().'/'.$CI->router->fetch_method().'/'.$CI->uri->assoc_to_uri($thisGET)));
// 		}
// 	}
// }

if(!function_exists('convert_get_slashes_pretty_link'))
{
	function convert_get_slashes_pretty_link(){
		$CI =& get_instance();
		
		/* get to pretty link */
		$thisGET = $CI->input->get();
		if($thisGET){
			foreach($thisGET as $key => $value){
				if($value == ''){
					unset($thisGET[$key]);
				}
			}
			redirect(base_url($CI->router->fetch_class().'/'.$CI->router->fetch_method().'/'.$CI->uri->assoc_to_uri($thisGET)));
			exit;
		}

		/* slashes to pretty link */
		$emptyValueSlashes = false;
		$thisSlashes = $CI->uri->uri_to_assoc();
		if($thisSlashes){
			foreach($thisSlashes as $key => $value){
				if($value == ''){
					$emptyValueSlashes = true;
					unset($thisSlashes[$key]);
				}
			}
			if($emptyValueSlashes){
				redirect(base_url($CI->router->fetch_class().'/'.$CI->router->fetch_method().'/'.$CI->uri->assoc_to_uri($thisSlashes)));
				exit;
			}
		}
	}
}

if(!function_exists('get_array_prefix'))
{
	function get_array_prefix($thisPrefix, $thisData = array()){
		$thisArray = array();
		if(!empty($thisData)){
			foreach($thisData as $key => $value){
				if(strpos($key, $thisPrefix, 0) === 0){
					$thisArray[$key] = $value;
				}
			}
		}
		return $thisArray;
	}
}

if(!function_exists('convert_object_to_array'))
{
	function convert_object_to_array($thisObject = array(), $thisKey = ''){
		$thisArray = array();
		foreach($thisObject as $key => $value){
			$thisArray[] = $value->{$thisKey};
		}
		return $thisArray;
	}
}

if(!function_exists('convert_datetime_to_date'))
{
	function convert_datetime_to_date($thisDate){
		return date("Y-m-d", strtotime($thisDate));
	}
}

if(!function_exists('check_session_timeout'))
{
	function check_session_timeout(){
		$CI =& get_instance();
		if($CI->session->userdata('last_activity') != '' && (time() - $CI->session->userdata('last_activity') > 3600)){
			redirect('login/select/referrer/'.urlencode(base64_encode(current_url())));
		}
		$CI->session->set_userdata('last_activity', time());
	}
}

if(!function_exists('check_is_login'))
{
	function check_is_login(){
		$CI =& get_instance();
		// if($CI->router->fetch_class() == 'salesorder'){
		// 	echo 'function_helper = '.$CI->session->session_id;
		// }
		if($CI->session->userdata('user_id') === null or $CI->session->userdata('user_id') < 1){
			redirect('login/select/referrer/'.urlencode(base64_encode(current_url())));
		}
	}
}

if(!function_exists('check_role'))
{
	function check_role($thisRole = ''){
		$CI =& get_instance();
		if(in_array($thisRole, $CI->session->userdata('role'))){
			return true;
		}else{
			return false;
		}
	}
}

if(!function_exists('check_permission'))
{
	function check_permission($thisPermission = '', $thisType = 'access'){
		$CI =& get_instance();
		if($CI->router->fetch_method() != 'index'){
			$thisPermission = ($thisPermission != '') ? $thisPermission : $CI->router->fetch_class().'_'.$CI->router->fetch_method();
			if(!in_array($thisPermission, $CI->session->userdata('permission'))){
				switch(true){
					case ($thisType == 'access'):
						die('Access denied');
						break;
					case ($thisType == 'disable'):
						return ' disabled';
						break;
					case ($thisType == 'display'):
						// return ' hidden';
						return true;
						break;
				}
			}
		}
	}
}

if(!function_exists('set_salesorder_status_complete'))
{
	function set_salesorder_status_complete($thisSalesorderId){
		$CI =& get_instance();
		$CI->load->model('salesorder_model');
		$CI->load->model('purchaseorder_model');
		$CI->load->model('invoice_model');

		/* salesorder */
		$thisSalesorderStatus = 'imcomplete';

		/* purchaseorder */
		$thisSelect = array(
			'where' => array(
				'purchaseorder_salesorder_id' => $thisSalesorderId,
				'purchaseorder_deleted' => 'N',
				'purchaseorder_imcomplete' => true
			),
			'return' => 'row'
		);
		$data = $CI->purchaseorder_model->select($thisSelect);
		if($data){
			$thisSalesorderPurchaseorderStatus = 'imcomplete';
		}else{
			$thisSalesorderPurchaseorderStatus = 'complete';
		}

		/* invoice */
		$thisSelect = array(
			'where' => array(
				'invoice_salesorder_id' => $thisSalesorderId,
				'invoice_deleted' => 'N',
				'invoice_imcomplete' => true
			),
			'return' => 'row'
		);
		$data = $CI->invoice_model->select($thisSelect);
		if($data){
			$thisSalesorderInvoiceStatus = 'imcomplete';
		}else{
			$thisSalesorderInvoiceStatus = 'complete';
		}

		// if all salesorder complete & all invoice complete
		if($thisSalesorderPurchaseorderStatus == 'complete' && $thisSalesorderInvoiceStatus == 'complete'){
			/* salesorder */
			$thisSelect = array(
				'where' => array(
					'salesorder_id' => $thisSalesorderId
				),
				'return' => 'row'
			);
			$data = $CI->salesorder_model->select($thisSelect);
			$thisSalesorderTotal = $data->salesorder_total;

			/* invoice */
			$thisSelect = array(
				'select' => array(
					'sum(invoice_pay) as sum_of_invoice_pay'
				),
				'where' => array(
					'invoice_salesorder_id' => $thisSalesorderId
				),
				'return' => 'row'
			);
			$data = $CI->invoice_model->select($thisSelect);
			$thisSalesorderSumOfInvoicePay = $data->sum_of_invoice_pay;

			if($thisSalesorderTotal == $thisSalesorderSumOfInvoicePay){
				$thisData['salesorder_status'] = 'complete';
                $thisData['salesorder_confirmed_date'] = Date('Y-m-d');
				$CI->db->where('salesorder_id', $thisSalesorderId);
				$thisResult = $CI->db->update('salesorder', $thisData);

				$log_SQL = $CI->session->userdata('log_SQL');
				$log_SQL[] = array(
					'result' => $thisResult,
					'sql' => $CI->db->last_query()
				);
				$CI->session->set_userdata('log_SQL', $log_SQL);
			}
		}
	}
}

if(!function_exists('set_delivery_note_status_complete'))
{
    function set_delivery_note_status_complete($thisSalesorderId){
        $CI =& get_instance();
        $CI->load->model('deliverynote_model');

        $thisData['deliverynote_status'] = 'complete';
        $CI->db->where('deliverynote_id', $thisSalesorderId);
        $thisResult = $CI->db->update('deliverynote', $thisData);

        $log_SQL = $CI->session->userdata('log_SQL');
        $log_SQL[] = array(
            'result' => $thisResult,
            'sql' => $CI->db->last_query()
        );
        $CI->session->set_userdata('log_SQL', $log_SQL);
    }
}

if(!function_exists('set_log'))
{
	function set_log($thisLog = array()){
		$CI =& get_instance();

		$log_SQL = '';
		foreach($CI->session->userdata('log_SQL') as $key => $value){
			$thisResult = ($value['result']) ? '<b class="sql-success">SUCCESS</b> ' : '<b class="sql-failed">FAILED</b> ';
			$log_SQL .= '<div>'.$thisResult.htmlentities($value['sql']).'</div>';
		}
		$CI->session->unset_userdata('log_SQL');

		$thisLog['log_IP'] = $CI->input->ip_address();
		$thisLog['log_user_id'] = $CI->session->userdata('user_id');
		$thisLog['log_path'] = current_url();
		$thisLog['log_SQL'] = $log_SQL;
		$thisLog['log_create'] = date('Y-m-d H:i:s');

		$CI->db->insert('log', $thisLog);
	}
}

if(!function_exists('chuyan'))
{
	function chuyan($thisArray){
		echo '<pre>';
		print_r($thisArray);
		echo '</pre>';
	}
}

// if(!function_exists('escape_form_value'))
// {
// 	function escape_form_value($form_value){
// 		foreach($form_value as $key => $value){
// 			if(!is_array($value)){
// 				$form_value[$key] = trim($form_value[$key]);
// 				//$form_value[$key] = $mysqli->real_escape_string($form_value[$key]);
// 			}else{
// 				$value = escape_form_value($value);
// 				$form_value[$key] = $value;
// 			}
// 		}
// 		return $form_value;
// 	}
// }

if (!function_exists('php_excel_export')) {
    function php_excel_export($title, $data, $name) {
//        require_once(dirname(__FILE__) . '../libraries/PHPExcel/PHPExcel.php');
        error_reporting(E_ALL);
        //date_default_timezone_set('Europe/London');
        $objPHPExcel = new PHPExcel();
        $letterArr=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
            'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        /*以下是一些设置 ，什么作者标题啊之类的*/
        $objPHPExcel->getProperties()->setCreator("nexus")
            ->setLastModifiedBy("nexus")
            ->setTitle("EXCEL导出")
            ->setSubject("EXCEL导出")
            ->setDescription("EXCEL导出")
            ->setKeywords("excel")
            ->setCategory("result file");

        //设置标题
        $num=1;
        $letterArrNum=0;
        foreach ($title as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($letterArr[$letterArrNum].$num, $value);
            $letterArrNum++;
        }

        $letterArrNum=0;
        foreach($data as $k => $v){
            $num = $k+2;
            $letterArrNum=0;
            foreach ($title as $key => $value) {
                $objPHPExcel->getActiveSheet()->getStyle($letterArr[$letterArrNum].$num)->getAlignment()->setWrapText(true);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($letterArr[$letterArrNum].$num, strip_tags(str_replace("<br/>", "\n", $v[$key])));
                $letterArrNum++;
            }
        }
        $objPHPExcel->getActiveSheet()->setTitle(ucfirst(explode('_', $name)[0]));
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }
}

if (!function_exists('get_uri_string_parameters')) {
    function get_uri_string_parameters($uri_string)
    {
        $retval = '';

        $uri_array = explode('/', $uri_string);
        foreach ($uri_array as $key => $value){
            if( $key < 2 ) continue;
            $retval .= '/'.$value;
        }
        return $retval;
    }
}