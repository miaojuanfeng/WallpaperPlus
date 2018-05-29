<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Purchase order management</title>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

		<link rel="stylesheet" href="<?php echo base_url('assets/css/jquery-ui.css'); ?>">
		<link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.css'); ?>">
		<link rel="stylesheet" href="<?php echo base_url('assets/css/chosen.css'); ?>">
		<link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap-datetimepicker.css'); ?>">
		<link rel="stylesheet" href="<?php echo base_url('assets/css/style.css"'); ?>" media="all">

		<script src="<?php echo base_url('assets/js/jquery-1.11.3.min.js'); ?>"></script>
		<script src="<?php echo base_url('assets/js/jquery-ui.js'); ?>"></script>
		<script src="<?php echo base_url('assets/js/jquery-ui.multidatespicker.js'); ?>"></script>
		<!-- <script src="<?php echo base_url('assets/js/modernizr-custom.min.js'); ?>"></script> -->
		<script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>
		<script src="<?php echo base_url('assets/js/chosen.jquery.js'); ?>"></script>
		<script src="<?php echo base_url('assets/js/jquery.maskedinput.js'); ?>"></script>
		<script src="<?php echo base_url('assets/js/accounting.min.js'); ?>"></script>
		<script src="<?php echo base_url('assets/js/jquery.validate.js'); ?>"></script>
		<script src="<?php echo base_url('assets/js/additional-methods.min.js'); ?>"></script>
		<script src="<?php echo base_url('assets/js/echarts.min.js'); ?>"></script>
		<script src="<?php echo base_url('assets/js/moment.js'); ?>"></script>
		<script src="<?php echo base_url('assets/js/bootstrap-datetimepicker.min.js'); ?>"></script>
		<script src="<?php echo base_url('assets/js/function.js'); ?>"></script>

		<script>
		$(function(){
			calc();

			$('input[name="purchaseorder_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');

			/*--------- date mask ---------*/
			$('.date-mask').mask('9999-99-99');

			/*--------- datetimepicker ---------*/
			$('.datetimepicker').datetimepicker({
				format: 'Y-MM-DD'
			});

			/* purchaseorderitem-insert-btn */
			$(document).on('click', '.purchaseorderitem-insert-btn', function(){
				add_purchaseorderitem_row();
			});

			/* purchaseordersubitem-insert-btn */
			$(document).on('click', '.purchaseordersubitem-insert-btn', function(){
				add_purchaseordersubitem_row($(this).parent().parent().parent().parent().index());
			});

			/* purchaseorderitem-delete-btn */
			$(document).on('click', '.purchaseorderitem-delete-btn', function(){
				if(confirm('Confirm delete?')){
					$(this).closest('tr').remove();
					calc();
				}else{
					return false;
				}
			});

			/* vendor loader */
			<?php if($this->router->fetch_method() == 'insert' && isset($this->uri->uri_to_assoc()['purchaseorder_vendor_id'])){ ?>
			vendor_loader();
			<?php } ?>
			$(document).on('change', 'select[name="purchaseorder_vendor_id"]', function(){
				vendor_loader();
			});

			/* salesorder loader */
			<?php if($this->router->fetch_method() == 'insert'){ ?>
			$(document).on('change', 'select[name="purchaseorder_salesorder_id"]', function(){
				if(confirm('Confirm to bundle this SO')){
					window.location = "<?php echo base_url('/purchaseorder/insert/salesorder_id'); ?>" + '/' + $(this).val();
				}else{
					$('select[name="purchaseorder_salesorder_id"]').val('').trigger("chosen:updated");
				}
			});
			<?php } ?>

			/* salesorder loader */
			$(document).on('change', 'select[name="purchaseorder_salesorder_id"]', function(){
				salesorder_loader();
			});

			/* product loader */
			$(document).on('change', 'select[name="purchaseorderitem_product_id[]"]', function(){
				product_loader($(this));
			});

			/* trigger calc */
			$(document).on('blur', 'input[name="purchaseorderitem_product_price[]"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="purchaseorderitem_quantity[]"]', function(){
				calc();
			});
            $(document).on('blur', 'input[name="purchaseorderitem_discount[]"]', function(){
                calc();
            });
			$(document).on('blur', 'input[name="purchaseorder_discount"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="purchaseorder_pay"]', function(){
				calc();
			});
			$(document).on('change', 'input[name="purchaseorder_currency"]', function(){
				$.each($('select[name="purchaseorderitem_product_id[]"]'), function(key, val){
					product_loader($(this));
				});
			});

			/* up & down btn */
			$(document).on('click', '.up-btn', function(){
				if($(this).closest('tr').index() > 0){
					$('table.list tbody tr').eq($(this).closest('tr').index()).after($('table.list tbody tr').eq($(this).closest('tr').index() - 1));
				}
			});
			$(document).on('click', '.down-btn', function(){
				if($('table.list tbody tr').length > $(this).closest('tr').index()){
					$('table.list tbody tr').eq($(this).closest('tr').index()).before($('table.list tbody tr').eq($(this).closest('tr').index() + 1));
				}
			});

			/* textarea auto height */
			textarea_auto_height();
			$(document).on('keyup', 'textarea', function(){
				textarea_auto_height();
			});
		});

		function calc(){
			var total = 0;
			$.each($('table.list tbody tr'), function(key, val){
			    var subtotal = parseFloat($(this).find('input[name="purchaseorderitem_product_price[]"]').val()) * parseFloat($(this).find('input[name="purchaseorderitem_quantity[]"]').val());
                var discount = parseFloat($(this).find('input[name="purchaseorderitem_discount[]"]').val())/100;
                discount = discount?discount:1;
                subtotal = subtotal?subtotal:0;
				$(this).find('input[name="purchaseorderitem_subtotal[]"]').val((subtotal*discount).toFixed(2)).css('display', 'none').fadeIn();
				total += parseFloat($(this).find('input[name="purchaseorderitem_subtotal[]"]').val());
			});
			$('input[name="purchaseorder_total"]').val(parseFloat(total).toFixed(2));
		}

		function check_delete(id){
			var answer = prompt("Confirm delete?");
			if(answer){
				$('input[name="purchaseorder_id"]').val(id);
				$('input[name="purchaseorder_delete_reason"]').val(encodeURI(answer));
				$('form[name="list"]').submit();
			}else{
				return false;
			}
		}

		function vendor_loader(){
			$('.scriptLoader').load('/load', {'thisTableId': 'vendorLoader', 'thisRecordId': $('select[name="purchaseorder_vendor_id"]').val(), 't': timestamp()}, function(){
				vendorLoader();
			});
		}

		function salesorder_loader(){
			$('.scriptLoader').load('/load', {'thisTableId': 'salesorderLoader', 'thisRecordId': $('select[name="purchaseorder_salesorder_id"]').val(), 't': timestamp()}, function(){
				salesorderLoader();
			});
		}

		function product_loader(thisObject){
			thisRow = $(thisObject).closest('tr').index();
			thisCurrency = $('select[name="purchaseorder_currency"]').val();
			$('.scriptLoader').load('/load', {'thisTableId': 'purchaseorderProductLoader', 'thisRecordId': $(thisObject).val(), 'thisCurrency': thisCurrency, 'thisRow': thisRow, 't': timestamp()}, function(){
				purchaseorderProductLoader();
				textarea_auto_height();
				calc();
			});
		}

		function textarea_auto_height(){
			$.each($('textarea'), function(key, val){
				$(this).attr('rows', $(this).val().split('\n').length + 1);
			});
		}

		<?php if($this->router->fetch_method() == 'update' || $this->router->fetch_method() == 'insert'){ ?>
		function add_purchaseorderitem_row(){
			purchaseorderitem_row = '';
			purchaseorderitem_row += '<tr>';
			purchaseorderitem_row += '<td>';
			purchaseorderitem_row += '<div>';
			purchaseorderitem_row += '<input name="purchaseorderitem_id[]" type="hidden" value="" />';
			purchaseorderitem_row += '<input name="purchaseorderitem_purchaseorder_id[]" type="hidden" value="" />';
			purchaseorderitem_row += '<input name="purchaseorderitem_type[]" type="hidden" value="main item" />';
			purchaseorderitem_row += '<input name="purchaseorderitem_product_type_name[]" type="hidden" value="" />';
			purchaseorderitem_row += '<input id="purchaseorderitem_product_code" name="purchaseorderitem_product_code[]" type="text" class="form-control input-sm" placeholder="Code" value="" />';
			purchaseorderitem_row += '</div>';
			purchaseorderitem_row += '<div class="margin-top-10">';
			purchaseorderitem_row += '<div class="btn-group">';
			purchaseorderitem_row += '<button type="button" class="btn btn-sm btn-primary purchaseorderitem-delete-btn"><i class="glyphicon glyphicon-remove"></i></button>';
			purchaseorderitem_row += '<button type="button" class="btn btn-sm btn-primary up-btn"><i class="glyphicon glyphicon-chevron-up"></i></button>';
			purchaseorderitem_row += '<button type="button" class="btn btn-sm btn-primary down-btn"><i class="glyphicon glyphicon-chevron-down"></i></button>';
			purchaseorderitem_row += '</div>';
			purchaseorderitem_row += '</div>';
			purchaseorderitem_row += '</td>';
			purchaseorderitem_row += '<td>';
            purchaseorderitem_row += '<div>';
            purchaseorderitem_row += '<input id="purchaseorderitem_product_id" name="purchaseorderitem_product_id[]" type="hidden" class="form-control input-sm" placeholder="Product" value="" />';
            purchaseorderitem_row += '<input type="button" class="form-control input-sm showModal" value="Select a product" />';
            purchaseorderitem_row += '</div>';
			purchaseorderitem_row += '<div class="margin-top-10">';
			// purchaseorderitem_row += '<input id="purchaseorderitem_product_name" name="purchaseorderitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="" />';
			purchaseorderitem_row += '<div class="input-group">';
			purchaseorderitem_row += '<span class="input-group-addon corpcolor-font">Title</span>';
			purchaseorderitem_row += '<input id="purchaseorderitem_product_name" name="purchaseorderitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="" />';
			purchaseorderitem_row += '</div>';
			purchaseorderitem_row += '</div>';
			purchaseorderitem_row += '<div>';
			purchaseorderitem_row += '<textarea id="purchaseorderitem_product_detail" name="purchaseorderitem_product_detail[]" class="form-control input-sm" placeholder="Detail"></textarea>';
			purchaseorderitem_row += '</div>';
			purchaseorderitem_row += '</td>';
			purchaseorderitem_row += '<td>';
			purchaseorderitem_row += '<input id="purchaseorderitem_product_price" name="purchaseorderitem_product_price[]" type="text" class="form-control input-sm" placeholder="Price" value="" />';
			purchaseorderitem_row += '</td>';
			purchaseorderitem_row += '<td>';
            purchaseorderitem_row += '<div class="input-group">';
			purchaseorderitem_row += '<input id="purchaseorderitem_quantity" name="purchaseorderitem_quantity[]" type="number" min="0" class="form-control input-sm" placeholder="Quantity" value="1" />';
            purchaseorderitem_row += '<input id="purchaseorderitem_unit" name="purchaseorderitem_unit[]" type="hidden" />';
            purchaseorderitem_row += '<span class="input-group-addon purchaseorderitem_unit">Unit</span>';
            purchaseorderitem_row += '</div>';
			purchaseorderitem_row += '</td>';
            purchaseorderitem_row += '<td>';
            purchaseorderitem_row += '<div class="input-group">';
            purchaseorderitem_row += '<input id="purchaseorderitem_discount" name="purchaseorderitem_discount[]" type="number" min="0" max="100" class="form-control input-sm" placeholder="Discount" value="100" />';
            purchaseorderitem_row += '<span class="input-group-addon">%</span>';
            purchaseorderitem_row += '</div>';
            purchaseorderitem_row += '</td>';
			purchaseorderitem_row += '<td>';
			purchaseorderitem_row += '<div>';
			purchaseorderitem_row += '<input readonly="readonly" id="purchaseorderitem_subtotal" name="purchaseorderitem_subtotal[]" type="text" class="form-control input-sm" placeholder="Subtotal" value="" />';
			purchaseorderitem_row += '</div>';
			purchaseorderitem_row += '<div class="margin-top-10 text-right">';
			purchaseorderitem_row += '<div class="btn-group">';
			// purchaseorderitem_row += '<button type="button" class="btn btn-sm btn-primary purchaseordersubitem-insert-btn"><i class="glyphicon glyphicon-plus"></i></button>';
			purchaseorderitem_row += '</div>';
			purchaseorderitem_row += '</div>';
			purchaseorderitem_row += '</td>';
			purchaseorderitem_row += '</tr>';

			$('table.list tbody').append(purchaseorderitem_row);
			$('.chosen-select').chosen();
		}

		//function add_purchaseordersubitem_row(thisRow){
		//	purchaseordersubitem_row = '';
		//	purchaseordersubitem_row += '<tr class="subitem-row">';
		//	purchaseordersubitem_row += '<td>';
		//	purchaseordersubitem_row += '<div>';
		//	purchaseordersubitem_row += '<input name="purchaseorderitem_id[]" type="hidden" value="" />';
		//	purchaseordersubitem_row += '<input name="purchaseorderitem_purchaseorder_id[]" type="hidden" value="" />';
		//	purchaseordersubitem_row += '<input name="purchaseorderitem_type[]" type="hidden" value="sub item" />';
		//	purchaseordersubitem_row += '<input name="purchaseorderitem_product_type_name[]" type="hidden" value="" />';
		//	purchaseordersubitem_row += '<input id="purchaseorderitem_product_code" name="purchaseorderitem_product_code[]" type="text" class="form-control input-sm" placeholder="Code" value="" />';
		//	purchaseordersubitem_row += '</div>';
		//	purchaseordersubitem_row += '<div class="margin-top-10">';
		//	purchaseordersubitem_row += '<div class="btn-group">';
		//	purchaseordersubitem_row += '<button type="button" class="btn btn-sm btn-primary purchaseorderitem-delete-btn"><i class="glyphicon glyphicon-remove"></i></button>';
		//	purchaseordersubitem_row += '<button type="button" class="btn btn-sm btn-primary up-btn"><i class="glyphicon glyphicon-chevron-up"></i></button>';
		//	purchaseordersubitem_row += '<button type="button" class="btn btn-sm btn-primary down-btn"><i class="glyphicon glyphicon-chevron-down"></i></button>';
		//	purchaseordersubitem_row += '</div>';
		//	purchaseordersubitem_row += '</div>';
		//	purchaseordersubitem_row += '</td>';
		//	purchaseordersubitem_row += '<td>';
		//	purchaseordersubitem_row += '<div>';
		//	purchaseordersubitem_row += '<select id="purchaseorderitem_product_id" name="purchaseorderitem_product_id[]" data-placeholder="Product" class="chosen-select">';
		//	purchaseordersubitem_row += '<option value></option>';
		//	<?php //foreach($products as $key1 => $value1){ ?>
		//	purchaseordersubitem_row += '<option value="<?//=$value1->product_id?>//"><?//=$value1->product_code.' - '.$value1->product_name?>//</option>';
		//	<?php //} ?>
		//	purchaseordersubitem_row += '</select>';
		//	purchaseordersubitem_row += '</div>';
		//	purchaseordersubitem_row += '<div class="margin-top-10">';
		//	// purchaseordersubitem_row += '<input id="purchaseorderitem_product_name" name="purchaseorderitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="" />';
		//	purchaseordersubitem_row += '<div class="input-group">';
		//	purchaseordersubitem_row += '<span class="input-group-addon corpcolor-font">Title</span>';
		//	purchaseordersubitem_row += '<input id="purchaseorderitem_product_name" name="purchaseorderitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="" />';
		//	purchaseordersubitem_row += '</div>';
		//	purchaseordersubitem_row += '</div>';
		//	purchaseordersubitem_row += '<div>';
		//	purchaseordersubitem_row += '<textarea id="purchaseorderitem_product_detail" name="purchaseorderitem_product_detail[]" class="form-control input-sm" placeholder="Detail"></textarea>';
		//	purchaseordersubitem_row += '</div>';
		//	purchaseordersubitem_row += '</td>';
		//	purchaseordersubitem_row += '<td>';
		//	purchaseordersubitem_row += '<input id="purchaseorderitem_product_price" name="purchaseorderitem_product_price[]" type="number" min="0" class="form-control input-sm" placeholder="Price" value="" />';
		//	purchaseordersubitem_row += '</td>';
		//	purchaseordersubitem_row += '<td>';
         //   purchaseordersubitem_row += '<input id="purchaseorderitem_quantity" name="purchaseorderitem_quantity[]" type="number" min="0" class="form-control input-sm" placeholder="Quantity" value="1" />';
         //   purchaseordersubitem_row += '</td>';
         //   purchaseordersubitem_row += '<td>';
         //   purchaseordersubitem_row += '<input id="purchaseorderitem_discount" name="purchaseorderitem_discount[]" type="number" min="0" max="100" class="form-control input-sm" placeholder="Discount" value="0" />';
         //   purchaseordersubitem_row += '</td>';
		//	purchaseordersubitem_row += '<td>';
		//	purchaseordersubitem_row += '<div>';
		//	purchaseordersubitem_row += '<input readonly="readonly" id="purchaseorderitem_subtotal" name="purchaseorderitem_subtotal[]" type="text" class="form-control input-sm" placeholder="Subtotal" value="" />';
		//	purchaseordersubitem_row += '</div>';
		//	purchaseordersubitem_row += '<div class="margin-top-10 text-right">';
		//	purchaseordersubitem_row += '<div class="btn-group">';
		//	// purchaseordersubitem_row += '<button type="button" class="btn btn-sm btn-primary purchaseordersubitem-insert-btn"><i class="glyphicon glyphicon-plus"></i></button>';
		//	purchaseordersubitem_row += '</div>';
		//	purchaseordersubitem_row += '</div>';
		//	purchaseordersubitem_row += '</td>';
		//	purchaseordersubitem_row += '</tr>';
        //
		//	$(purchaseordersubitem_row).insertAfter('table.list tbody tr:eq(' + thisRow + ')');
		//	$('.chosen-select').chosen();
		//}
		<?php } ?>
		</script>
	</head>

	<body>

		<?php $this->load->view('inc/header-area.php'); ?>










































		<?php if($this->router->fetch_method() == 'update' || $this->router->fetch_method() == 'insert'){ ?>
		<div class="content-area">

			<div class="container-fluid">
				<div class="row">

					<h2 class="col-sm-12"><a href="<?=base_url('purchaseorder')?>">Purchase order management</a> > <?=($this->router->fetch_method() == 'update') ? 'Update' : 'Insert'?> purchase order</h2>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="purchaseorder_id" value="<?=$purchaseorder->purchaseorder_id?>" />
							<input type="hidden" name="purchaseorder_quotation_user_id" value="<?=$purchaseorder->purchaseorder_quotation_user_id?>" />
							<input type="hidden" name="purchaseorder_salesorder_id" value="<?=$purchaseorder->purchaseorder_salesorder_id?>" />
							<input type="hidden" name="purchaseorder_vendor_id" value="<?=$purchaseorder->purchaseorder_vendor_id?>" />
							<input type="hidden" name="purchaseorder_project_name" value="<?=$purchaseorder->purchaseorder_project_name?>" />
							<input type="hidden" name="purchaseorder_currency" value="<?=$purchaseorder->purchaseorder_currency?>" />
							<input type="hidden" name="purchaseorder_number" value="" />
                            <input type="hidden" name="purchaseorder_vendor_company_code" value="<?=$purchaseorder->purchaseorder_vendor_company_code?>" />
							<input type="hidden" name="purchaseorder_vendor_company_name" value="<?=$purchaseorder->purchaseorder_vendor_company_name?>" />
							<input type="hidden" name="purchaseorder_vendor_company_address" value="<?=$purchaseorder->purchaseorder_vendor_company_address?>" />
							<input type="hidden" name="purchaseorder_vendor_company_phone" value="<?=$purchaseorder->purchaseorder_vendor_company_phone?>" />
							<input type="hidden" name="purchaseorder_vendor_phone" value="<?=$purchaseorder->purchaseorder_vendor_phone?>" />
							<input type="hidden" name="purchaseorder_vendor_name" value="<?=$purchaseorder->purchaseorder_vendor_name?>" />
							<input type="hidden" name="purchaseorder_issue" value="<?=$purchaseorder->purchaseorder_issue?>" />
							<input type="hidden" name="purchaseorder_reminder_date" value="<?=$purchaseorder->purchaseorder_reminder_date?>" />
                            <input type="hidden" name="purchaseorder_currency" value="<?=$purchaseorder->purchaseorder_currency?>" />
							<input type="hidden" name="salesorder_total" value="<?=$purchaseorder->purchaseorder_total?>" />
							<input type="hidden" name="purchaseorder_user_id" value="<?=$purchaseorder->purchaseorder_user_id?>" />
							<input type="hidden" name="referrer" value="<?=$this->agent->referrer()?>" />
							<div class="fieldset">
								<?=$this->session->tempdata('alert');?>
								<div class="row">

									<div class="col-sm-3 col-xs-12 pull-right">
										<blockquote>
											<h4 class="corpcolor-font">Instructions</h4>
											<p><span class="highlight">*</span> is a required field</p>
											<p>
												<b>BOLD</b>: &lt;b&gt;content&lt;/b&gt;
												<br /><b>ITALIC</b>: &lt;i&gt;content&lt;/i&gt;
												<br /><b>UNDERLINE</b>: &lt;u&gt;content&lt;/u&gt;
											</p>
										</blockquote>
										<p class="form-group">
											<button type="submit" name="action" value="save" class="btn btn-sm btn-primary btn-block"><i class="glyphicon glyphicon-floppy-disk"></i> Save</button>
										</p>
										<?php if($this->router->fetch_method() == 'update'){ ?>
										<p class="form-group">
											<a class="btn btn-sm btn-primary btn-block" target="_blank" href="<?=base_url('assets/images/pdf/purchaseorder/'.$purchaseorder->purchaseorder_number.'.pdf?'.time())?>" data-toggle="tooltip" title="Print"><i class="glyphicon glyphicon-print"></i> Print</a>
										</p>
										<?php } ?>
										<h4 class="corpcolor-font">Setting</h4>
										<p class="form-group">
											<label for="purchaseorder_status">Status</label>
											<select id="purchaseorder_status" name="purchaseorder_status" data-placeholder="Status" class="chosen-select required">
												<option value></option>
												<?php
												if($purchaseorder->purchaseorder_status == ''){
													$purchaseorder->purchaseorder_status = 'processing';
												}
												foreach($statuss as $key => $value){
													$selected = ($value->status_name == $purchaseorder->purchaseorder_status) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value->status_name.'"'.$selected.'>'.strtoupper($value->status_name).'</option>';
												}
												?>
											</select>
										</p>
										<p class="form-group">
											<label for="purchaseorder_project_name">Project name <span class="highlight">*</span></label>
											<input id="purchaseorder_project_name" name="purchaseorder_project_name" type="text" class="form-control input-sm required" placeholder="Project name" value="<?=$purchaseorder->purchaseorder_project_name?>" />
										</p>
                                        <p class="form-group">
                                            <label for="purchaseorder_shipment">Shipment <span class="highlight">*</span></label>
                                            <input id="purchaseorder_shipment" name="purchaseorder_shipment" type="text" class="form-control input-sm required" placeholder="Shipment" value="<?=$purchaseorder->purchaseorder_shipment?>" />
                                        </p>
                                        <p class="form-group">
                                            <label for="purchaseorder_arrive_date">Arrive date <span class="highlight">*</span></label>
                                            <input id="purchaseorder_arrive_date" name="purchaseorder_arrive_date" type="text" class="form-control input-sm date-mask required" placeholder="Arrive date" value="<?=$purchaseorder->purchaseorder_arrive_date?>" />
                                        </p>
                                        <p class="form-group">
                                            <label for="purchaseorder_delivery_invoice_no">Delivery Invoice no. <span class="highlight"></span></label>
                                            <input id="purchaseorder_delivery_invoice_no" name="purchaseorder_delivery_invoice_no" type="text" class="form-control input-sm" placeholder="Delivery Invoice no." value="<?=$purchaseorder->purchaseorder_delivery_invoice_no?>" />
                                        </p>
                                        <p class="form-group">
                                            <label for="purchaseorder_delivery_address">Delivery address <span class="highlight"></span></label>
                                            <textarea id="purchaseorder_delivery_address" name="purchaseorder_delivery_address" class="form-control input-sm" placeholder="Delivery address"><?=$purchaseorder->purchaseorder_delivery_address?></textarea>
                                        </p>
<!--										<p class="form-group">-->
<!--											<label for="purchaseorder_currency">Currency</label>-->
<!--											<select id="purchaseorder_currency" name="purchaseorder_currency" data-placeholder="Currency" class="chosen-select required">-->
<!--												<option value></option>-->
<!--												--><?php
//												if($purchaseorder->purchaseorder_currency == ''){
//													$purchaseorder->purchaseorder_currency = 'hkd';
//												}
//												foreach($currencys as $key => $value){
//													$selected = ($value->currency_name == $purchaseorder->purchaseorder_currency) ? ' selected="selected"' : "" ;
//													echo '<option value="'.$value->currency_name.'"'.$selected.'>'.strtoupper($value->currency_name).'</option>';
//												}
//												?>
<!--											</select>-->
<!--										</p>-->
									</div>
									<div class="col-sm-9 col-xs-12">
										<h4 class="corpcolor-font">Purchase order</h4>
										<div class="row">
											<div class="col-sm-6 col-xs-6">
												<table class="table table-condensed table-borderless">
													<tr>
														<td colspan="2">
															<select id="purchaseorder_vendor_id" name="purchaseorder_vendor_id" data-placeholder="Vendor" class="chosen-select required">
																<option value></option>
																<?php
																foreach($vendors as $key1 => $value1){
																	$selected = ($value1->vendor_id == $purchaseorder->purchaseorder_vendor_id) ? ' selected="selected"' : "" ;
																	echo '<option value="'.$value1->vendor_id.'"'.$selected.'>'.$value1->vendor_company_name.' '.$value1->vendor_firstname.' '.$value1->vendor_lastname.'</option>';
																}
																?>
															</select>
														</td>
													</tr>
													<tr>
														<td><label for="purchaseorder_vendor_company_name">To</label></td>
														<td><input id="purchaseorder_vendor_company_name" name="purchaseorder_vendor_company_name" type="text" class="form-control input-sm required" placeholder="Company/Domain/Client" value="<?=$purchaseorder->purchaseorder_vendor_company_name?>" /></td>
													</tr>
													<tr>
														<td><label for="purchaseorder_vendor_company_address">Address</label></td>
														<td><textarea id="purchaseorder_vendor_company_address" name="purchaseorder_vendor_company_address" class="form-control input-sm" placeholder="Address"><?=$purchaseorder->purchaseorder_vendor_company_address?></textarea></td>
													</tr>
													<tr>
														<td><label for="purchaseorder_vendor_company_phone">Phone</label></td>
														<td><input id="purchaseorder_vendor_company_phone" name="purchaseorder_vendor_company_phone" type="text" class="form-control input-sm" placeholder="Phone" value="<?=$purchaseorder->purchaseorder_vendor_company_phone?>" /></td>
													</tr>
													<tr>
														<td><label for="purchaseorder_vendor_phone">Mobile</label></td>
														<td><input id="purchaseorder_vendor_phone" name="purchaseorder_vendor_phone" type="text" class="form-control input-sm" placeholder="Fax" value="<?=$purchaseorder->purchaseorder_vendor_phone?>" /></td>
													</tr>
													<tr>
														<td><label for="purchaseorder_vendor_email">Email</label></td>
														<td><input id="purchaseorder_vendor_email" name="purchaseorder_vendor_email" type="text" class="form-control input-sm" placeholder="Email" value="<?=$purchaseorder->purchaseorder_vendor_email?>" /></td>
													</tr>
													<tr>
														<td><label for="purchaseorder_vendor_name">Attn</label></td>
														<td><input id="purchaseorder_vendor_name" name="purchaseorder_vendor_name" type="text" class="form-control input-sm required" placeholder="Attn." value="<?=$purchaseorder->purchaseorder_vendor_name?>" /></td>
													</tr>
                                                    <tr>
                                                        <td><label for="purchaseorder_vendor_exchange_rate">Exchange rate</label></td>
                                                        <td><input id="purchaseorder_vendor_exchange_rate" name="purchaseorder_vendor_exchange_rate" type="text" class="form-control input-sm required" readonly="readonly" placeholder="Exchange rate" value="<?=$purchaseorder->purchaseorder_vendor_exchange_rate?>" /></td>
                                                    </tr>
                                                    <tr>
                                                        <td><label for="purchaseorder_vendor_currency">Currency</label></td>
                                                        <td><input id="purchaseorder_vendor_currency" name="purchaseorder_vendor_currency" type="text" class="form-control input-sm required" readonly="readonly" placeholder="Currency" value="<?=$purchaseorder->purchaseorder_vendor_currency?>" /></td>
                                                    </tr>
												</table>
											</div>
											<div class="col-sm-1 col-xs-1">
											</div>
											<div class="col-sm-5 col-xs-5">
												<table class="table table-condensed table-borderless">
													<tr>
														<td><label for="purchaseorder_number">Purchase order#</label></td>
														<td><input readonly="readonly" id="purchaseorder_number" name="purchaseorder_number" type="text" class="form-control input-sm" placeholder="Purchase order#" value="<?=$purchaseorder->purchaseorder_number?>" /></td>
													</tr>
													<tr>
														<td><label for="purchaseorder_salesorder_id">Sales order#</label></td>
														<td>
															<?php if(empty($this->uri->uri_to_assoc()) || $purchaseorder->purchaseorder_salesorder_id == 0){ ?>
															<select id="purchaseorder_salesorder_id" name="purchaseorder_salesorder_id" data-placeholder="SO No" class="chosen-select">
																<option value></option>
																<?php
																foreach($salesorders as $key1 => $value1){
																	$selected = ($value1->salesorder_id == $purchaseorder->purchaseorder_salesorder_id) ? ' selected="selected"' : "" ;
																	echo '<option value="'.$value1->salesorder_id.'" rel="'.$value1->salesorder_project_name.'"'.$selected.'>'.$value1->salesorder_number.' '.$value1->salesorder_project_name.'</option>';
																}
																?>
															</select>
															<?php }else{ ?>
															<div class="input-group">
																<input readonly="readonly" type="text" class="form-control input-sm" placeholder="Sales order#" value="<?=get_salesorder($purchaseorder->purchaseorder_salesorder_id)->salesorder_number?>" />
																<span class="input-group-addon">
																	<a target="_blank" href="<?=base_url('assets/images/pdf/salesorder/'.get_salesorder($purchaseorder->purchaseorder_salesorder_id)->salesorder_number.'.pdf?'.time())?>"><i class="glyphicon glyphicon-print"></i></a>
																</span>
															</div>
															<?php } ?>
														</td>
													</tr>
													<tr>
														<td><label for="purchaseorder_issue">Date</label></td>
														<td><input id="purchaseorder_issue" name="purchaseorder_issue" type="text" class="form-control input-sm date-mask required" placeholder="Issue date" value="<?=($purchaseorder->purchaseorder_issue != '') ? $purchaseorder->purchaseorder_issue : date('Y-m-d')?>" /></td>
													</tr>
													<tr>
														<td><label for="purchaseorder_user_name">Sales</label></td>
														<td><input readonly="readonly" id="purchaseorder_user_name" name="purchaseorder_user_name" type="text" class="form-control input-sm required" placeholder="Saleman" value="<?=$user->user_name?>" /></td>
													</tr>
													<tr>
														<td><label for="purchaseorder_reminder_date">Reminder date</label></td>
														<td><input id="purchaseorder_reminder_date" name="purchaseorder_reminder_date" type="text" class="form-control input-sm date-mask" placeholder="Reminder date" value="<?=($purchaseorder->purchaseorder_reminder_date != '' && $this->router->fetch_method() != 'duplicate') ? $purchaseorder->purchaseorder_reminder_date : date('Y-m-d', strtotime('+14 days', time()))?>" /></td>
													</tr>
                                                    <tr>
                                                        <td><label for="purchaseorder_tel_no">Tel. No.</label></td>
                                                        <td><input id="purchaseorder_tel_no" name="purchaseorder_tel_no" type="text" class="form-control input-sm" placeholder="Tel. No." value="<?=$purchaseorder->purchaseorder_tel_no?>" /></td>
                                                    </tr>
                                                    <tr>
                                                        <td><label for="purchaseorder_fax_no">Fax No.</label></td>
                                                        <td><input id="purchaseorder_fax_no" name="purchaseorder_fax_no" type="text" class="form-control input-sm" placeholder="Fax No." value="<?=$purchaseorder->purchaseorder_fax_no?>" /></td>
                                                    </tr>
												</table>
											</div>
										</div>
										<div class="list-area">
											<table class="table list" id="purchaseorder">
												<thead>
													<tr>
														<th width="10%">
															<a class="btn btn-sm btn-primary purchaseorderitem-insert-btn" data-toggle="tooltip" title="Insert">
																<i class="glyphicon glyphicon-plus"></i>
															</a>
														</th>
														<th>Detail</th>
														<th width="12%">Price</th>
														<th width="12%">Quantity</th>
														<th width="14%">Discount %</th>
														<th width="12%">Subtotal</th>
													</tr>
												</thead>
												<tbody class="trModal">
													<?php foreach($purchaseorderitems as $key => $value){ ?>
													<tr<?=($value->purchaseorderitem_type == 'sub item') ? ' class="subitem-row"' : ''?>>
														<td>
															<div>
																<input name="purchaseorderitem_id[]" type="hidden" value="<?=$value->purchaseorderitem_id?>" />
																<input name="purchaseorderitem_purchaseorder_id[]" type="hidden" value="<?=$value->purchaseorderitem_purchaseorder_id?>" />
																<input name="purchaseorderitem_type[]" type="hidden" value="<?=($value->purchaseorderitem_type != '') ? $value->purchaseorderitem_type : 'main item'?>" />
																<input name="purchaseorderitem_product_type_name[]" type="hidden" value="<?=$value->purchaseorderitem_product_type_name?>" />
																<input id="purchaseorderitem_product_code" name="purchaseorderitem_product_code[]" type="text" class="form-control input-sm" placeholder="Code" value="<?=$value->purchaseorderitem_product_code?>" />
															</div>
															<div class="margin-top-10">
																<div class="btn-group">
																	<button type="button" class="btn btn-sm btn-primary purchaseorderitem-delete-btn"><i class="glyphicon glyphicon-remove"></i></button>
																	<button type="button" class="btn btn-sm btn-primary up-btn"><i class="glyphicon glyphicon-chevron-up"></i></button>
																	<button type="button" class="btn btn-sm btn-primary down-btn"><i class="glyphicon glyphicon-chevron-down"></i></button>
																</div>
															</div>
														</td>
														<td>
                                                            <div>
                                                                <input id="purchaseorderitem_product_id" name="purchaseorderitem_product_id[]" type="hidden" class="form-control input-sm" placeholder="Product" value="<?=$value->purchaseorderitem_product_id?>" />
                                                                <input type="button" class="form-control input-sm showModal" modal="product_select" value="Select a product"/>
                                                            </div>
															<div class="margin-top-10">
																<div class="input-group">
																	<span class="input-group-addon corpcolor-font">Title</span>
																	<input id="purchaseorderitem_product_name" name="purchaseorderitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="<?=$value->purchaseorderitem_product_name?>" />
																</div>
															</div>
															<div>
																<textarea id="purchaseorderitem_product_detail" name="purchaseorderitem_product_detail[]" class="form-control input-sm" placeholder="Detail"><?=$value->purchaseorderitem_product_detail?></textarea>
															</div>
														</td>
														<td>
															<input id="purchaseorderitem_product_price" name="purchaseorderitem_product_price[]" type="number" min="0" class="form-control input-sm" placeholder="Price" value="<?=$value->purchaseorderitem_product_price?>" />
<!--															<div class="margin-top-10">-->
<!--																<label>Bought</label>-->
<!--															</div>-->
<!--															<div class="margin-top-10">-->
<!--																<label>SO total</label>-->
<!--															</div>-->
														</td>
														<td>
															<?php
															/* get salesorder quantity */
															$salesorder_quantity = get_salesorderitem_quantity($purchaseorder->purchaseorder_salesorder_id, $value->purchaseorderitem_product_id);

															if($purchaseorder->purchaseorder_salesorder_id != ''){
																/* get purchaseorder bought */
																$purchaseorderitem_bought = get_purchaseorderitem_issued_quantity($purchaseorder->purchaseorder_salesorder_id, $purchaseorder->purchaseorder_id, $value->purchaseorderitem_product_id);
																if(is_null($purchaseorderitem_bought)){
																	$purchaseorderitem_bought = 0;
																}
																if($purchaseorder->purchaseorder_id > 0){
																	$thisPurchaseorderitemQuantity = $value->purchaseorderitem_quantity;
																}else{
																	$thisPurchaseorderitemQuantity = $salesorder_quantity - $purchaseorderitem_bought;
																}
															}else{
																/* get purchaseorder bought */
																$purchaseorderitem_bought = 0;
																$thisPurchaseorderitemQuantity = $salesorder_quantity - $purchaseorderitem_bought;
															}
															?>
                                                            <div class="input-group">
															    <input id="purchaseorderitem_quantity" name="purchaseorderitem_quantity[]" type="number" min="0" class="form-control input-sm" placeholder="Quantity" value="<?=$thisPurchaseorderitemQuantity?$thisPurchaseorderitemQuantity:1?>" />
                                                                <input id="purchaseorderitem_unit" name="purchaseorderitem_unit[]" type="hidden" value="<?=$value->purchaseorderitem_unit?>" />
                                                                <span class="input-group-addon purchaseorderitem_unit"><?=($value->purchaseorderitem_unit) ? $value->purchaseorderitem_unit : 'Unit'?></span>
                                                            </div>
<!--															<div class="margin-top-10">-->
<!--																<input readonly="readonly" type="text" class="form-control input-sm" placeholder="Sum of purchase order item quantity" value="--><?//=$purchaseorderitem_bought?><!--" />-->
<!--																<input readonly="readonly" type="text" class="form-control input-sm" placeholder="Sales order item quantity" value="--><?//=$salesorder_quantity?><!--" />-->
<!--															</div>-->
														</td>
														<td>
                                                            <div class="input-group">
																<input id="purchaseorderitem_discount" name="purchaseorderitem_discount[]" type="number" min="0" max="100" class="form-control input-sm" placeholder="Discount" value="<?=$value->purchaseorderitem_discount?$value->purchaseorderitem_discount:100?>" />
                                                                <span class="input-group-addon">%</span>
                                                            </div>
														</td>
														<td>
															<div>
																<input readonly="readonly" id="purchaseorderitem_subtotal" name="purchaseorderitem_subtotal[]" type="text" class="form-control input-sm" placeholder="Subtotal" value="<?=$value->purchaseorderitem_subtotal?>" />
															</div>
															<div class="margin-top-10 text-right">
																<div class="btn-group">
																	<!-- <button type="button" class="btn btn-sm btn-primary purchaseordersubitem-insert-btn"><i class="glyphicon glyphicon-plus"></i></button> -->
																</div>
															</div>
														</td>
													</tr>
													<?php } ?>
												</tbody>
												<tfoot>
													<!-- <tr>
														<th></th>
														<th></th>
														<th></th>
														<th>Discount</th>
														<th><input readonly="readonly" id="purchaseorder_discount" name="purchaseorder_discount" type="text" class="form-control input-sm required" placeholder="Discount" value="<?=($purchaseorder->purchaseorder_discount) ? $purchaseorder->purchaseorder_discount : '0'?>" /></th>
													</tr> -->
													<tr>
														<th width="10%">
															<a class="btn btn-sm btn-primary purchaseorderitem-insert-btn" data-toggle="tooltip" title="Insert">
																<i class="glyphicon glyphicon-plus"></i>
															</a>
														</th>
														<th></th>
														<th></th>
                                                        <th></th>
														<th>Grand total</th>
														<th><input readonly="readonly" id="purchaseorder_total" name="purchaseorder_total" type="text" class="form-control input-sm" placeholder="Grand total" value="<?=($purchaseorder->purchaseorder_total) ? $purchaseorder->purchaseorder_total : '0'?>" /></th>
													</tr>
													<!-- <tr>
														<th></th>
														<th></th>
														<th></th>
														<th>Paid</th>
														<th><input readonly="readonly" id="purchaseorder_paid" name="purchaseorder_paid" type="text" class="form-control input-sm" placeholder="Paid" value="<?=($purchaseorder->purchaseorder_paid) ? $purchaseorder->purchaseorder_paid : '0'?>" /></th>
													</tr>
													<tr>
														<th></th>
														<th></th>
														<th></th>
														<th>Pay</th>
														<th><input id="purchaseorder_pay" name="purchaseorder_pay" type="text" class="form-control input-sm" placeholder="Pay" value="<?=($purchaseorder->purchaseorder_pay) ? $purchaseorder->purchaseorder_pay : '0'?>" /></th>
													</tr>
													<tr>
														<th width="10%">
															<a class="btn btn-sm btn-primary purchaseorderitem-insert-btn" data-toggle="tooltip" title="Insert">
																<i class="glyphicon glyphicon-plus"></i>
															</a>
														</th>
														<th></th>
														<th></th>
														<th>Balance</th>
														<th><input readonly="readonly" id="purchaseorder_balance" name="purchaseorder_balance" type="text" class="form-control input-sm" placeholder="Balance" value="<?=($purchaseorder->purchaseorder_balance) ? $purchaseorder->purchaseorder_balance : '0'?>" /></th>
													</tr> -->
												</tfoot>
											</table>
										</div>
										<hr />
										<p class="form-group">
											<label for="purchaseorder_remark">Remark</label>
											<textarea id="purchaseorder_remark" name="purchaseorder_remark" class="form-control input-sm" placeholder="Remark" rows="3"><?=$purchaseorder->purchaseorder_remark?></textarea>
										</p>
										<p class="form-group">
											<label for="purchaseorder_payment">Payment</label>
											<textarea id="purchaseorder_payment" name="purchaseorder_payment" class="form-control input-sm" placeholder="Payment" rows="3"><?=$purchaseorder->purchaseorder_payment?></textarea>
										</p>
									</div>
								</div>

								<!-- <div class="row">
									<div class="col-xs-12">
										<button type="submit" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-floppy-disk"></i> Save</button>
									</div>
								</div> -->

							</div>
						</form>
					</div>

				</div>
			</div>
		</div>
		<?php } ?>













































		<?php if($this->router->fetch_method() == 'select'){ ?>
		<div class="content-area">

			<div class="container-fluid">
				<div class="row">

					<h2 class="col-sm-12">Purchase order management</h2>

					<div class="content-column-area col-md-12 col-sm-12">

						<div class="fieldset">
							<?=$this->session->tempdata('alert');?>
							<div class="search-area">

								<form purchaseorder="form" method="get">
									<input type="hidden" name="purchaseorder_id" />
									<table>
										<tbody>
											<tr>
												<td width="90%">
													<div class="row">
														<div class="col-sm-2"><h6>Purchase order</h6></div>
														<div class="col-sm-2">
															<input type="text" name="purchaseorder_number_like" class="form-control input-sm" placeholder="PONo" value="" />
														</div>
														<div class="col-sm-2">
															<span class="input-group date datetimepicker">
																<input id="purchaseorder_create_greateq" name="purchaseorder_create_greateq" type="text" class="form-control input-sm date-mask" placeholder="Date From (YYYY-MM-DD)" />
																<span class="input-group-addon">
																	<span class="glyphicon glyphicon-calendar"></span>
																</span>
															</span>
														</div>
														<div class="col-sm-2">
															<span class="input-group date datetimepicker">
																<input id="purchaseorder_create_smalleq" name="purchaseorder_create_smalleq" type="text" class="form-control input-sm date-mask" placeholder="Date To (YYYY-MM-DD)" />
																<span class="input-group-addon">
																	<span class="glyphicon glyphicon-calendar"></span>
																</span>
															</span>
														</div>
														<div class="col-sm-2">
															<input type="text" name="salesorder_number_like" class="form-control input-sm" placeholder="SONo" value="" />
														</div>
														<div class="col-sm-2">
															<select id="purchaseorder_status" name="purchaseorder_status" data-placeholder="Status" class="chosen-select">
																<option value></option>
																<?php foreach($statuss as $key => $value){ ?>
																<option value="<?=$value->status_name?>"><?=ucfirst($value->status_name)?></option>
																<?php } ?>
															</select>
														</div>
													</div>
													<div class="row">
														<div class="col-sm-2"><h6>Vendor</h6></div>
														<div class="col-sm-2">
															<input type="text" name="purchaseorder_vendor_company_name_like" class="form-control input-sm" placeholder="Vendor company name" value="" />
														</div>
														<div class="col-sm-2">
															<select id="purchaseorder_user_id" name="purchaseorder_user_id" data-placeholder="Sales" class="chosen-select">
																<option value></option>
																<?php foreach($users as $key => $value){ ?>
																<option value="<?=$value->user_id?>"><?=ucfirst($value->user_name)?></option>
																<?php } ?>
															</select>
														</div>
														<div class="col-sm-2">
															<!-- <input type="text" name="purchaseorder_vendor_company_name_purchaseorder_vendor_name_like" class="form-control input-sm" placeholder="Customer PO" value="" /> -->
														</div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
													</div>
													<div class="row">
														<div class="col-sm-2"><h6>Project</h6></div>
														<div class="col-sm-2">
															<input type="text" name="purchaseorder_project_name_like" class="form-control input-sm" placeholder="Project Name" value="" />
														</div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
													</div>
													<div class="row">
														<div class="col-sm-2"><h6>Product</h6></div>
														<div class="col-sm-2">
															<input type="text" name="salesorderitem_product_code_like" class="form-control input-sm" placeholder="Item Code" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="salesorderitem_product_name_like" class="form-control input-sm" placeholder="Item Name" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="salesorderitem_product_detail_like" class="form-control input-sm" placeholder="Item Description" value="" />
														</div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
													</div>
												</td>
												<td valign="top" width="10%" class="text-right">
													<button type="submit" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Search">
														<i class="glyphicon glyphicon-search"></i>
													</button>
												</td>
											</tr>
										</tbody>
									</table>
								</form>

							</div> <!-- list-container -->
						</div>
						<div class="fieldset">

							<div class="list-area">
								<form name="list" action="<?=base_url('purchaseorder/delete')?>" method="post">
									<input type="hidden" name="purchaseorder_id" />
									<input type="hidden" name="purchaseorder_delete_reason" />
									<div class="page-area">
										<span class="btn btn-sm btn-default"><?php print_r($num_rows); ?></span>
										<?=$this->pagination->create_links()?>
									</div>
									<table class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>PO No</th>
												<th>SO No</th>
												<th>Create</th>
												<th>Vendor</th>
												<th>Project</th>
												<th>Sales</th>
												<th>Deadline</th>
												<th>Total</th>
												<th>Status</th>
												<th>Stock status</th>
												<th></th>
												<th></th>
												<th></th>
												<th class="text-right">
													<a href="<?=base_url('purchaseorder/insert')?>" data-toggle="tooltip" title="Insert">
														<i class="glyphicon glyphicon-plus"></i>
													</a>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($purchaseorders as $key => $value){ ?>
											<tr>
												<td>
													<a href="<?=base_url('purchaseorder/update/purchaseorder_id/'.$value->purchaseorder_id)?>">
														<?=$value->purchaseorder_number?>
													</a>
												</td>
												<td>
													<?php if($value->purchaseorder_salesorder_id != 0){ ?>
													<a href="<?=base_url('salesorder/update/salesorder_id/'.$value->purchaseorder_salesorder_id)?>"><?=get_salesorder($value->purchaseorder_salesorder_id)->salesorder_number?></a>
													<?php } ?>
												</td>
												<td><?=convert_datetime_to_date($value->purchaseorder_create)?></td>
												<td><?=$value->purchaseorder_vendor_company_name?></td>
												<td><?=$value->purchaseorder_project_name?></td>
												<td><?=ucfirst(get_user($value->purchaseorder_user_id)->user_name)?></td>
												<td><?=$value->purchaseorder_reminder_date?></td>
												<td><?=strtoupper($value->purchaseorder_currency).' '.money_format('%!n', $value->purchaseorder_total)?></td>
												<td><?=ucfirst($value->purchaseorder_status)?></td>
												<td><?=ucfirst($value->purchaseorder_arrive_status)?></td>
												<td class="text-right">
													<a target="_blank" href="<?=base_url('/assets/images/pdf/purchaseorder/'.$value->purchaseorder_number.'.pdf')?>" data-toggle="tooltip" title="Print">
														<i class="glyphicon glyphicon-print"></i>
													</a>
												</td>
												<td class="text-right">
													<a href="<?=base_url('stockstatus/update/purchaseorder_id/'.$value->purchaseorder_id)?>" data-toggle="tooltip" title="Stock status">
														<i class="glyphicon glyphicon-log-in"></i>
													</a>
												</td>
												<td class="text-right">
													<a href="<?=base_url('purchaseorder/update/purchaseorder_id/'.$value->purchaseorder_id)?>" data-toggle="tooltip" title="Update">
														<i class="glyphicon glyphicon-edit"></i>
													</a>
												</td>
												<td class="text-right">
													<?php if(!check_permission('purchaseorder_delete', 'display')){ ?>
													<a onclick="check_delete(<?=$value->purchaseorder_id?>);" data-toggle="tooltip" title="Remove">
														<i class="glyphicon glyphicon-remove"></i>
													</a>
													<?php }else{ ?>
													<i class="glyphicon glyphicon-remove"></i>
													<?php } ?>
												</td>
											</tr>
											<?php } ?>

											<?php if(!$purchaseorders){ ?>
											<tr>
												<td colspan="15">No record found</td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
									<div class="page-area">
										<span class="btn btn-sm btn-default"><?php print_r($num_rows); ?></span>
										<?=$this->pagination->create_links()?>
									</div>
								</form>
							</div>
						</div>
						<div class="fieldset">
							<div class="row">
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-ok-circle"></i>
										<a href="<?=base_url('purchaseorderchecklist/select/purchaseorder_status/processing')?>">Purchase order checklist</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-usd"></i>
										<a href="<?=base_url('payablereport')?>">Payable report</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12">
                                    <blockquote>
                                        <i class="glyphicon glyphicon-ok-circle"></i>
                                        <a href="<?=base_url('waybill')?>">Purchase order waybill</a>
                                    </blockquote>
                                </div>
								<div class="col-md-3 col-sm-12"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
            <?php if(isset($popup_list) && !empty($popup_list)){ ?>
                <div class="popup-view">
                    <div class="popup-header">出Stock已完成，SO未完成的单<a href="javascript:" class="popup-close">Close</a></div>
                    <div class="popup-list-area">
                        <form name="list">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>PO No</th>
                                    <th>SO No</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($popup_list as $key => $value){ ?>
                                    <tr>
                                        <td><a href="<?=base_url('purchaseorder/update/purchaseorder_id/'.$value->purchaseorder_id)?>" data-toggle="tooltip" title="Update"><?=$value->purchaseorder_number?></a></td>
                                        <td><a href="<?=base_url('salesorder/update/salesorder_id/'.$value->purchaseorder_salesorder_id)?>" data-toggle="tooltip" title="Update"><?=get_salesorder($value->purchaseorder_salesorder_id)->salesorder_number?></a></td>
                                        <td><?=ucfirst($value->purchaseorder_status)?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            <?php } ?>
		</div>
		<?php } ?>












































		<?php $this->load->view('inc/footer-area.php'); ?>

	</body>
</html>

<div class="scriptLoader"></div>


<!-- Modal -->
<script src="<?php echo base_url('assets/js/product-modal.js'); ?>"></script>
<div class="modal fade" id="popupModal" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="hideModal()">
                    <i class="glyphicon glyphicon-remove"></i>
                </button>
                <h4 class="modal-title corpcolor-font">Product list</h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="hideModal()">Close</button>
            </div>
        </div>

    </div>
</div>
<!-- Modal -->