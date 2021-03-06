<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Invoice management</title>

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

			$('input[name="invoice_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');

			/*--------- date mask ---------*/
			$('.date-mask').mask('9999-99-99');

			/*--------- datetimepicker ---------*/
			$('.datetimepicker').datetimepicker({
				format: 'Y-MM-DD'
			});

			/* invoiceitem-insert-btn */
			$(document).on('click', '.invoiceitem-insert-btn', function(){
				add_invoiceitem_row();
			});

			/* invoiceitem-delete-btn */
			$(document).on('click', '.invoiceitem-delete-btn', function(){
				if(confirm('Confirm delete?')){
					$(this).closest('tr').remove();
					calc();
				}else{
					return false;
				}
			});

			/* product loader */
			$(document).on('change', 'select[name="invoiceitem_product_id[]"]', function(){
				product_loader($(this));
			});

			/* trigger calc */
			$(document).on('blur', 'input[name="invoiceitem_product_price[]"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="invoiceitem_quantity[]"]', function(){
				calc();
			});
			// $(document).on('blur', 'input[name="invoice_discount"]', function(){
			// 	calc();
			// });
			$(document).on('blur', 'input[name="invoice_pay"]', function(){
				calc_balance();
			});
			$(document).on('change', 'input[name="invoice_currency"]', function(){
				$.each($('select[name="invoiceitem_product_id[]"]'), function(key, val){
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
				$(this).find('input[name="invoiceitem_subtotal[]"]').val(parseFloat($(this).find('input[name="invoiceitem_product_price[]"]').val() * $(this).find('input[name="invoiceitem_quantity[]"]').val()).toFixed(2)).css('display', 'none').fadeIn();
				total += parseFloat($(this).find('input[name="invoiceitem_subtotal[]"]').val());
			});
			var category_discount_total = 0;
            $('#category_discount input[name="category_discount[]"]').each(function(){
            	category_discount_total += parseFloat($(this).val());
            });
            total = total - category_discount_total + parseFloat($('input[name="invoice_freight"]').val()) - parseFloat($('input[name="invoice_discount"]').val());
			$('input[name="invoice_pay"]').val(parseFloat(parseFloat(total)-parseFloat($('input[name="invoice_paid"]').val())).toFixed(2));
			$('input[name="invoice_total"]').val(parseFloat(total).toFixed(2)).css('display', 'none').fadeIn();
			$('input[name="invoice_balance"]').val(parseFloat(total.toFixed(2) - parseFloat($('input[name="invoice_paid"]').val()).toFixed(2) - parseFloat($('input[name="invoice_pay"]').val()).toFixed(2)).toFixed(2)).css('display', 'none').fadeIn();
		}

		function calc_balance(){
			$('input[name="invoice_balance"]').val(parseFloat($('input[name="invoice_total"]').val() - $('input[name="invoice_paid"]').val() - $('input[name="invoice_pay"]').val()).toFixed(2)).css('display', 'none').fadeIn();
		}

		function check_delete(id){
			var answer = prompt("Confirm delete?");
			if(answer){
				$('input[name="invoice_id"]').val(id);
				$('input[name="invoice_delete_reason"]').val(encodeURI(answer));
				$('form[name="list"]').submit();
			}else{
				return false;
			}
		}

		function product_loader(thisObject){
			thisRow = $(thisObject).closest('tr').index();
			thisCurrency = $('input[name="invoice_currency"]').val();
			$('.scriptLoader').load('/load', {'thisTableId': 'invoiceProductLoader', 'thisRecordId': $(thisObject).val(), 'thisCurrency': thisCurrency, 'thisRow': thisRow, 't': timestamp()}, function(){
				invoiceProductLoader();
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
		function add_invoiceitem_row(){
			invoiceitem_row = '';
			invoiceitem_row += '<tr>';
			invoiceitem_row += '<td>';
			invoiceitem_row += '<div>';
			invoiceitem_row += '<input name="invoiceitem_id[]" type="hidden" value="" />';
			invoiceitem_row += '<input name="invoiceitem_invoice_id[]" type="hidden" value="" />';
			invoiceitem_row += '<input name="invoiceitem_category_id[]" type="hidden" value="" />';
			invoiceitem_row += '<input name="invoiceitem_product_type_name[]" type="hidden" value="" />';
			invoiceitem_row += '<input id="invoiceitem_product_code" name="invoiceitem_product_code[]" type="text" class="form-control input-sm" placeholder="Code" value="" />';
			invoiceitem_row += '</div>';
			invoiceitem_row += '<div class="margin-top-10">';
			invoiceitem_row += '<input id="invoiceitem_product_color_code" name="invoiceitem_product_color_code[]" type="text" class="form-control input-sm" placeholder="Color code" value="" />';
            invoiceitem_row += '</div>';
			invoiceitem_row += '<div class="margin-top-10">';
			invoiceitem_row += '<div class="btn-group">';
			invoiceitem_row += '<button type="button" class="btn btn-sm btn-primary invoiceitem-delete-btn"><i class="glyphicon glyphicon-remove"></i></button>';
			invoiceitem_row += '<button type="button" class="btn btn-sm btn-primary up-btn"><i class="glyphicon glyphicon-chevron-up"></i></button>';
			invoiceitem_row += '<button type="button" class="btn btn-sm btn-primary down-btn"><i class="glyphicon glyphicon-chevron-down"></i></button>';
			invoiceitem_row += '</div>';
			invoiceitem_row += '</div>';
			invoiceitem_row += '</td>';
			invoiceitem_row += '<td>';
			invoiceitem_row += '<div>';
			invoiceitem_row += '<select id="invoiceitem_price_type" name="invoiceitem_price_type[]" data-placeholder="Price type" class="chosen-select required">';
			invoiceitem_row += '<option value="ex-fty">EX-FTY</option>';
			invoiceitem_row += '<option value="cif hk">CIF HK</option>';
			invoiceitem_row += '</select>';
			invoiceitem_row += '</div>';
			invoiceitem_row += '</td>';
			invoiceitem_row += '<td>';
            invoiceitem_row += '<div>';
            invoiceitem_row += '<input id="invoiceitem_product_id" name="invoiceitem_product_id[]" type="hidden" class="form-control input-sm" placeholder="Product" value="" />';
            invoiceitem_row += '<input type="button" class="form-control input-sm showModal" modal="product_select" value="Select a product" />';
            invoiceitem_row += '</div>';
			invoiceitem_row += '<div class="margin-top-10">';
			// invoiceitem_row += '<input id="invoiceitem_product_name" name="invoiceitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="" />';
			invoiceitem_row += '<div class="input-group">';
			invoiceitem_row += '<span class="input-group-addon corpcolor-font">Location</span>';
			invoiceitem_row += '<input id="invoiceitem_product_name" name="invoiceitem_product_name[]" type="hidden" class="form-control input-sm" placeholder="Name" value="" />';
			invoiceitem_row += '<input id="invoiceitem_product_location" name="invoiceitem_product_location[]" type="text" class="form-control input-sm" placeholder="Location" value="" />';
			invoiceitem_row += '</div>';
			invoiceitem_row += '</div>';
			invoiceitem_row += '<div>';
			invoiceitem_row += '<textarea id="invoiceitem_product_detail" name="invoiceitem_product_detail[]" class="form-control input-sm" placeholder="Detail"></textarea>';
			invoiceitem_row += '</div>';
			invoiceitem_row += '</td>';
			invoiceitem_row += '<td>';
			invoiceitem_row += '<input id="invoiceitem_product_price" name="invoiceitem_product_price[]" type="number" min="0" step="0.01" class="form-control input-sm" placeholder="Price" value="" />';
			invoiceitem_row += '</td>';
			invoiceitem_row += '<td>';
            invoiceitem_row += '<div class="input-group">';
			invoiceitem_row += '<input id="invoiceitem_quantity" name="invoiceitem_quantity[]" type="number" min="0" class="form-control input-sm" placeholder="Quantity" value="1" />';
            invoiceitem_row += '<input id="invoiceitem_unit" name="invoiceitem_unit[]" type="hidden" />';
            invoiceitem_row += '<span class="input-group-addon invoiceitem_unit">Unit</span>';
            invoiceitem_row += '</div>';
			invoiceitem_row += '</td>';
			invoiceitem_row += '<td>';
			invoiceitem_row += '<input readonly="readonly" id="invoiceitem_subtotal" name="invoiceitem_subtotal[]" type="text" class="form-control input-sm" placeholder="Subtotal" value="" />';
			invoiceitem_row += '</td>';
			invoiceitem_row += '</tr>';

			$('table.list tbody').append(invoiceitem_row);
			$('.chosen-select').chosen();
		}
		<?php } ?>
		</script>
	</head>

	<body>

		<?php $this->load->view('inc/header-area.php'); ?>

		








































		<?php if($this->router->fetch_method() == 'update' || $this->router->fetch_method() == 'insert'){ ?>
		<div class="content-area">

			<div class="container-fluid">
				<div class="row">

					<h2 class="col-sm-12"><a href="<?=base_url('invoice')?>">Invoice management</a> > <?=($this->router->fetch_method() == 'update') ? 'Update' : 'Insert'?> invoice</h2>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="invoice_id" value="<?=$invoice->invoice_id?>" />
							<input type="hidden" name="invoice_quotation_user_id" value="<?=$invoice->invoice_quotation_user_id?>" />
							<input type="hidden" name="invoice_salesorder_id" value="<?=$invoice->invoice_salesorder_id?>" />
							<input type="hidden" name="invoice_client_id" value="<?=$invoice->invoice_client_id?>" />
							<input type="hidden" name="invoice_project_name" value="<?=$invoice->invoice_project_name?>" />
                            <input type="hidden" name="invoice_client_company_code" value="<?=$invoice->invoice_client_company_code?>" />
							<input type="hidden" name="invoice_client_company_name" value="<?=$invoice->invoice_client_company_name?>" />
							<input type="hidden" name="invoice_client_company_address" value="<?=$invoice->invoice_client_company_address?>" />
							<input type="hidden" name="invoice_client_company_phone" value="<?=$invoice->invoice_client_company_phone?>" />
							<input type="hidden" name="invoice_client_phone" value="<?=$invoice->invoice_client_phone?>" />
							<input type="hidden" name="invoice_client_name" value="<?=$invoice->invoice_client_name?>" />
							<input type="hidden" name="invoice_issue" value="<?=$invoice->invoice_issue?>" />
							<input type="hidden" name="invoice_expire" value="<?=$invoice->invoice_expire?>" />
							<input type="hidden" name="salesorder_total" value="<?=$invoice->invoice_total?>" />
							<input type="hidden" name="invoice_user_id" value="<?=$invoice->invoice_user_id?>" />
							<input type="hidden" name="invoice_language" value="<?=$invoice->invoice_language?>" />
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
											<a class="btn btn-sm btn-primary btn-block" target="_blank" href="<?=base_url('assets/images/pdf/invoice/'.$invoice->invoice_number.'.pdf?'.time())?>" data-toggle="tooltip" title="Print"><i class="glyphicon glyphicon-print"></i> Print</a>
										</p>
										<?php } ?>
										<h4 class="corpcolor-font">Setting</h4>
										<p class="form-group">
											<label for="invoice_status">Status</label>
                                            <?php
                                            if( $invoice->invoice_status != 'complete' ) { ?>
                                                <select id="invoice_status" name="invoice_status"
                                                        data-placeholder="Status" class="chosen-select required">
                                                    <option value></option>
                                                    <?php
                                                    foreach ($statuss as $key => $value) {
                                                        $selected = ($value->status_name == $invoice->invoice_status) ? ' selected="selected"' : "";
                                                        echo '<option value="' . $value->status_name . '"' . $selected . '>' . strtoupper($value->status_name) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            <?php }else{ ?>
                                                <input readonly="readonly" id="invoice_status" name="invoice_status" type="text" class="form-control input-sm" placeholder="Status" value="<?=strtoupper($invoice->invoice_status)?>" />
                                            <?php } ?>
										</p>
									</div>
									<div class="col-sm-9 col-xs-12">
										<h4 class="corpcolor-font">Invoice</h4>
										<div class="row">
											<div class="col-sm-6 col-xs-6">
												<table class="table table-condensed table-borderless">
													<tr>
														<td><label for="invoice_client_company_name">To</label></td>
														<td><input id="invoice_client_company_name" name="invoice_client_company_name" type="text" class="form-control input-sm required" placeholder="Company/Domain/Client" value="<?=$invoice->invoice_client_company_name?>" /></td>
													</tr>
													<tr>
														<td><label for="invoice_client_company_address">Address</label></td>
														<td><textarea id="invoice_client_company_address" name="invoice_client_company_address" class="form-control input-sm" placeholder="Address"><?=$invoice->invoice_client_company_address?></textarea></td>
													</tr>
													<tr>
														<td><label for="invoice_client_company_phone">Phone</label></td>
														<td><input id="invoice_client_company_phone" name="invoice_client_company_phone" type="text" class="form-control input-sm" placeholder="Phone" value="<?=$invoice->invoice_client_company_phone?>" /></td>
													</tr>
													<tr>
														<td><label for="invoice_client_phone">Mobile</label></td>
														<td><input id="invoice_client_phone" name="invoice_client_phone" type="text" class="form-control input-sm" placeholder="Fax" value="<?=$invoice->invoice_client_phone?>" /></td>
													</tr>
													<tr>
														<td><label for="invoice_client_email">Email</label></td>
														<td><input id="invoice_client_email" name="invoice_client_email" type="text" class="form-control input-sm" placeholder="Email" value="<?=$invoice->invoice_client_email?>" /></td>
													</tr>
													<tr>
														<td><label for="invoice_client_name">Attn</label></td>
														<td><input id="invoice_client_name" name="invoice_client_name" type="text" class="form-control input-sm required" placeholder="Attn." value="<?=$invoice->invoice_client_name?>" /></td>
													</tr>
													<tr>
														<td><label for="invoice_design_by">Design by</label></td>
														<td><input id="invoice_design_by" name="invoice_design_by" type="text" class="form-control input-sm" placeholder="Design by" value="<?=$invoice->invoice_design_by?>" /></td>
													</tr>
                                                    <tr>
                                                        <td><label for="invoice_exchange_rate">Exchange rate</label></td>
                                                        <td><input id="invoice_exchange_rate" name="invoice_exchange_rate" type="text" class="form-control input-sm required" readonly="readonly" placeholder="Exchange rate" value="<?=$invoice->invoice_exchange_rate?>" /></td>
                                                    </tr>
                                                    <tr>
                                                        <td><label for="invoice_currency">Currency</label></td>
                                                        <td><input id="invoice_currency" name="invoice_currency" type="text" class="form-control input-sm required" readonly="readonly" placeholder="Currency" value="<?=strtoupper($invoice->invoice_currency)?>" /></td>
                                                    </tr>
												</table>
											</div>
											<div class="col-sm-1 col-xs-1">
											</div>
											<div class="col-sm-5 col-xs-5">
												<table class="table table-condensed table-borderless">
													<tr>
														<td><label for="invoice_number">Invoice#</label></td>
														<td>
															<div class="input-group">
																<span class="input-group-addon" style="padding:0;border:none;text-align:left;min-width:60px;">
																	<select id="number_prefix" name="number_prefix" data-placeholder="Prefix" class="chosen-select required">
																		<option value="<?=$number_prefix?>" <?php if( ( !empty($invoice->invoice_number) && strpos($invoice->invoice_number, 'E'.$number_prefix) === false ) || ( $this->router->fetch_method() == 'insert' && empty($this->session->userdata('user_order_prefix')) ) ) echo "selected"; ?>><?=$number_prefix?></option>
																		<option value="E<?=$number_prefix?>" <?php if( ( !empty($invoice->invoice_number) && strpos($invoice->invoice_number, 'E'.$number_prefix) !== false ) || ( $this->router->fetch_method() == 'insert' && !empty($this->session->userdata('user_order_prefix')) ) ) echo "selected"; ?>>E<?=$number_prefix?></option>
																	</select>
																</span>
																<input readonly="readonly" id="invoice_number" name="invoice_number" type="text" class="form-control input-sm" placeholder="Invoice#" value="<?=str_replace(array('DEP', 'INV', 'E', 'C'), '', $invoice->invoice_number)?>" />
																<!-- <span class="input-group-addon"><?=$invoice->invoice_version?'V'.$invoice->invoice_version:'N/A'?></span> -->
															</div>
														</td>
													</tr>
													<tr>
														<td><label for="invoice_issue">Date</label></td>
														<td>
															<span class="input-group date datetimepicker">
																<input id="invoice_issue" name="invoice_issue" type="text" class="form-control input-sm date-mask required" placeholder="Issue date" value="<?=($invoice->invoice_issue != '') ? $invoice->invoice_issue : date('Y-m-d')?>" />
																<span class="input-group-addon">
																	<span class="glyphicon glyphicon-calendar"></span>
																</span>
															</span>
														</td>
													</tr>
													<tr>
														<td><label for="invoice_user_name">Sales</label></td>
														<td><input readonly="readonly" id="invoice_user_name" name="invoice_user_name" type="text" class="form-control input-sm required" placeholder="Saleman" value="<?=$user->user_name?>" /></td>
													</tr>
													<tr>
														<td><label for="invoice_expire">Expire Date</label></td>
														<td>
															<span class="input-group date datetimepicker">
																<input id="invoice_expire" name="invoice_expire" type="text" class="form-control input-sm date-mask" placeholder="Expire Date" value="<?=($invoice->invoice_expire != '' && $this->router->fetch_method() != 'duplicate') ? $invoice->invoice_expire : date('Y-m-d', strtotime('+14 days', time()))?>" />
																<span class="input-group-addon">
																	<span class="glyphicon glyphicon-calendar"></span>
																</span>
															</span>
														</td>
													</tr>
												</table>
											</div>
										</div>
										<div class="list-area">
											<table class="table list" id="invoice">
												<thead>
													<tr>
														<th width="10%">
															<a class="btn btn-sm btn-primary invoiceitem-insert-btn" data-toggle="tooltip" title="Insert">
																<i class="glyphicon glyphicon-plus"></i>
															</a>
														</th>
														<th width="12%"></th>
														<th>Detail</th>
														<th width="12%">Price</th>
														<th width="12%">Quantity</th>
														<th width="12%">Subtotal</th>
													</tr>
												</thead>
												<tbody class="trModal">
													<?php foreach($invoiceitems as $key => $value){ ?>
													<tr>
														<td>
															<div>
																<input name="invoiceitem_id[]" type="hidden" value="<?=$value->invoiceitem_id?>" />
																<input name="invoiceitem_invoice_id[]" type="hidden" value="<?=$value->invoiceitem_invoice_id?>" />
																<input name="invoiceitem_category_id[]" type="hidden" value="<?=$value->invoiceitem_category_id?>" />
																<input name="invoiceitem_product_type_name[]" type="hidden" value="<?=$value->invoiceitem_product_type_name?>" />
																<input id="invoiceitem_product_code" name="invoiceitem_product_code[]" type="text" class="form-control input-sm" placeholder="Code" value="<?=$value->invoiceitem_product_code?>" />
															</div>
															<div class="margin-top-10">
																<input id="invoiceitem_product_color_code_<?=$key?>" name="invoiceitem_product_color_code[]" type="text" class="form-control input-sm" placeholder="Color code" value="<?=$value->invoiceitem_product_color_code?>" />
                                                            </div>
															<div class="margin-top-10">
																<div class="btn-group">
																	<button type="button" class="btn btn-sm btn-primary invoiceitem-delete-btn"><i class="glyphicon glyphicon-remove"></i></button>
																	<button type="button" class="btn btn-sm btn-primary up-btn"><i class="glyphicon glyphicon-chevron-up"></i></button>
																	<button type="button" class="btn btn-sm btn-primary down-btn"><i class="glyphicon glyphicon-chevron-down"></i></button>
																</div>
															</div>
														</td>
														<td>
															<div>
																<select id="invoiceitem_price_type" name="invoiceitem_price_type[]" data-placeholder="Price type" class="chosen-select required">
																	<option value="ex-fty" <?php if( $value->invoiceitem_price_type == 'ex-fty' ) echo "selected"; ?>>EX-FTY</option>
																	<option value="cif hk" <?php if( $value->invoiceitem_price_type == 'cif hk' ) echo "selected"; ?>>CIF HK</option>
																</select>
															</div>
														</td>
														<td>
                                                            <div>
                                                                <input id="invoiceitem_product_id" name="invoiceitem_product_id[]" type="hidden" class="form-control input-sm" placeholder="Product" value="<?=$value->invoiceitem_product_id?>" />
                                                                <input type="button" class="form-control input-sm showModal" modal="product_select" value="Select a product"/>
                                                            </div>
															<div class="margin-top-10">
																<div class="input-group">
																	<span class="input-group-addon corpcolor-font">Location</span>
																	<input id="invoiceitem_product_name" name="invoiceitem_product_name[]" type="hidden" class="form-control input-sm" placeholder="Name" value="<?=$value->invoiceitem_product_name?>" />
																	<input id="invoiceitem_product_location" name="invoiceitem_product_location[]" type="text" class="form-control input-sm" placeholder="Location" value="<?=$value->invoiceitem_product_location?>" />
																</div>
															</div>
															<div>
																<textarea id="invoiceitem_product_detail" name="invoiceitem_product_detail[]" class="form-control input-sm" placeholder="Detail"><?=$value->invoiceitem_product_detail?></textarea>
															</div>
														</td>
														<td>
															<input id="invoiceitem_product_price" name="invoiceitem_product_price[]" type="number" min="0" step="0.01" class="form-control input-sm" placeholder="Price" value="<?=$value->invoiceitem_product_price?>" />
<!--															<div class="margin-top-10">-->
<!--																<label>Sold</label>-->
<!--															</div>-->
<!--															<div class="margin-top-10">-->
<!--																<label>SO total</label>-->
<!--															</div>-->
														</td>
														<td>
															<?php
															/* get salesorder quantity */
															$salesorder_quantity = get_salesorderitem_quantity($invoice->invoice_salesorder_id, $value->invoiceitem_product_id);

															/* get invoice sold */
															$invoiceitem_sold = get_invoiceitem_issued_quantity($invoice->invoice_salesorder_id, $invoice->invoice_id, $value->invoiceitem_product_id);
															if(is_null($invoiceitem_sold)){
																$invoiceitem_sold = 0;
															}
															if($invoice->invoice_id > 0){
																$thisInvoiceitemQuantity = $value->invoiceitem_quantity;
															}else{
																$thisInvoiceitemQuantity = $salesorder_quantity - $invoiceitem_sold;
															}
															?>
                                                            <div class="input-group">
                                                                <input id="invoiceitem_quantity" name="invoiceitem_quantity[]" type="number" min="0" class="form-control input-sm" placeholder="Quantity" value="<?=$thisInvoiceitemQuantity?>" />
                                                                <input id="invoiceitem_unit" name="invoiceitem_unit[]" type="hidden" value="<?=$value->invoiceitem_unit?>" />
                                                                <span class="input-group-addon invoiceitem_unit"><?=($value->invoiceitem_unit) ? $value->invoiceitem_unit : 'Unit'?></span>
                                                            </div>
														</td>
														<td>
															<input readonly="readonly" id="invoiceitem_subtotal" name="invoiceitem_subtotal[]" type="text" class="form-control input-sm" placeholder="Subtotal" value="<?=$value->invoiceitem_subtotal?>" />
														</td>
													</tr>
													<?php } ?>
												</tbody>
												<tfoot id="category_discount">
													<?php foreach ($invoice_category_discount as $key => $value) { ?>
													<tr id="category_discount_<?=$value->category_id?>">
														<th colspan="3" style="text-align:right;">
															<input name="category_id[]" type="hidden" value="<?=$value->category_id?>" />
															<input name="category_name[]" type="hidden" value="<?=$value->category_name?>" />
															<?=$value->category_name?> discount
														</th>
														<th>
															<input readonly="readonly" name="category_discount_type[]" type="text" class="form-control input-sm required" placeholder="Discount type" value="<?=$value->category_discount_type?>" />
														</th>
														<th>
															<input readonly="readonly" name="category_discount_value[]" type="number" min="0" step="0.01" class="form-control input-sm required" placeholder="Discount value" value="<?=$value->category_discount_value?>" />
														</th>
														<th>
															<input readonly="readonly" exists="<?=$value->category_id?>" name="category_discount[]" type="number" min="0" step="0.01" class="form-control input-sm required" placeholder="Discount" value="<?=$value->category_discount?>" />
						                                </th>
													</tr>
													<?php } ?>
													<tr>
														<th></th>
														<th></th>
														<th>
															<p style="text-align:right;">Special discount</p>
														</th>
														<th>
															<input readonly="readonly" id="invoice_discount_type" name="invoice_discount_type" type="text" class="form-control input-sm required" placeholder="Discount type" value="<?=($invoice->invoice_discount_type) ? ucfirst($invoice->invoice_discount_type) : 'Percent'?>" />
														</th>
														<th>
															<input readonly="readonly" id="invoice_discount_value" name="invoice_discount_value" type="number" min="0" class="form-control input-sm required" placeholder="Discount value" value="<?=($invoice->invoice_discount_value) ? $invoice->invoice_discount_value : '0'?>" />
														</th>
														<th>
                                                            <input readonly="readonly" id="invoice_discount" name="invoice_discount" type="number" min="0" class="form-control input-sm required" placeholder="Discount" value="<?=($invoice->invoice_discount) ? $invoice->invoice_discount : '0'?>" />
                                                        </th>
													</tr>
													<tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th>Freight</th>
                                                        <th>
                                                            <input readonly="readonly" id="invoice_freight" name="invoice_freight" type="number" min="0" class="form-control input-sm required" placeholder="Freight" value="<?=($invoice->invoice_freight) ? $invoice->invoice_freight : '0'?>" />
                                                        </th>
                                                    </tr>
													<tr>
														<th></th>
														<th></th>
														<th></th>
														<th></th>
														<th>Grand total</th>
														<th><input readonly="readonly" id="invoice_total" name="invoice_total" type="text" class="form-control input-sm" placeholder="Grand total" value="<?=($invoice->invoice_total) ? $invoice->invoice_total : '0'?>" /></th>
													</tr>
													<tr>
														<th></th>
														<th></th>
														<th></th>
														<th></th>
														<th>Paid</th>
														<th><input readonly="readonly" id="invoice_paid" name="invoice_paid" type="text" class="form-control input-sm" placeholder="Paid" value="<?=($invoice->invoice_paid) ? $invoice->invoice_paid : '0'?>" /></th>
													</tr>
													<tr>
														<th></th>
														<th></th>
														<th></th>
														<th></th>
														<th>Pay</th>
														<th><input id="invoice_pay" name="invoice_pay" type="text" class="form-control input-sm" placeholder="Pay" value="<?=($invoice->invoice_pay) ? $invoice->invoice_pay : '0'?>" /></th>
													</tr>
													<tr>
														<th width="10%">
															<a class="btn btn-sm btn-primary invoiceitem-insert-btn" data-toggle="tooltip" title="Insert">
																<i class="glyphicon glyphicon-plus"></i>
															</a>
														</th>
														<th></th>
														<th></th>
														<th></th>
														<th>Balance</th>
														<th><input readonly="readonly" id="invoice_balance" name="invoice_balance" type="text" class="form-control input-sm" placeholder="Balance" value="<?=($invoice->invoice_balance) ? $invoice->invoice_balance : '0'?>" /></th>
													</tr>
												</tfoot>
											</table>
										</div>
										<hr />
										<p class="form-group">
											<label for="invoice_remark">Remark</label>
											<textarea id="invoice_remark" name="invoice_remark" class="form-control input-sm" placeholder="Remark" rows="3"><?=$invoice->invoice_remark?></textarea>
										</p>
										<p class="form-group">
											<label for="invoice_payment">Payment</label>
											<textarea id="invoice_payment" name="invoice_payment" class="form-control input-sm" placeholder="Payment" rows="3"><?=$invoice->invoice_payment?></textarea>
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

					<h2 class="col-sm-12">Invoice management</h2>

					<div class="content-column-area col-md-12 col-sm-12">

						<div class="fieldset">
							<?=$this->session->tempdata('alert');?>
							<div class="search-area">

								<form invoice="form" method="get">
									<input type="hidden" name="invoice_id" />
									<table>
										<tbody>
											<tr>
												<td width="90%">
													<div class="row">
														<div class="col-sm-2"><h6>Invoice</h6></div>
														<div class="col-sm-2">
															<input type="text" name="invoice_number_like" class="form-control input-sm" placeholder="INNo" value="" />
														</div>
														<div class="col-sm-2">
															<span class="input-group date datetimepicker">
																<input id="invoice_create_greateq" name="invoice_create_greateq" type="text" class="form-control input-sm date-mask" placeholder="Date From (YYYY-MM-DD)" />
																<span class="input-group-addon">
																	<span class="glyphicon glyphicon-calendar"></span>
																</span>
															</span>
														</div>
														<div class="col-sm-2">
															<span class="input-group date datetimepicker">
																<input id="invoice_create_smalleq" name="invoice_create_smalleq" type="text" class="form-control input-sm date-mask" placeholder="Date To (YYYY-MM-DD)" />
																<span class="input-group-addon">
																	<span class="glyphicon glyphicon-calendar"></span>
																</span>
															</span>
														</div>
														<div class="col-sm-2">
															<input type="text" name="salesorder_number_like" class="form-control input-sm" placeholder="SONo" value="" />
														</div>
														<div class="col-sm-2">
															<select id="invoice_status" name="invoice_status" data-placeholder="Status" class="chosen-select">
																<option value></option>
																<?php foreach($statuss as $key => $value){ ?>
																<option value="<?=$value->status_name?>"><?=ucfirst($value->status_name)?></option>
																<?php } ?>
															</select>
														</div>
													</div>
													<div class="row">
														<div class="col-sm-2"><h6>Customer</h6></div>
														<div class="col-sm-2">
															<input type="text" name="invoice_client_company_name_like" class="form-control input-sm" placeholder="Customer company name" value="" />
														</div>
														<div class="col-sm-2">
															<select id="invoice_user_id" name="invoice_user_id" data-placeholder="Sales" class="chosen-select">
																<option value></option>
																<?php foreach($users as $key => $value){ ?>
																<option value="<?=$value->user_id?>"><?=ucfirst($value->user_name)?></option>
																<?php } ?>
															</select>
														</div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
													</div>
													<div class="row">
														<div class="col-sm-2"><h6>Project</h6></div>
														<div class="col-sm-2">
															<input type="text" name="invoice_project_name_like" class="form-control input-sm" placeholder="Project Name" value="" />
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
														<div class="col-sm-2"></div>
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
								<form name="list" action="<?=base_url('invoice/delete')?>" method="post">
									<input type="hidden" name="invoice_id" />
									<input type="hidden" name="invoice_delete_reason" />
									<div class="page-area">
										<span class="btn btn-sm btn-default"><?php print_r($num_rows); ?></span>
										<?=$this->pagination->create_links()?>
									</div>
									<table class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>IN No</th>
												<th>SO No</th>
												<th>Create</th>
												<th>Customer</th>
												<th>Project</th>
												<th>Sales</th>
												<th>Deadline</th>
												<th>Status</th>
												<th>Total</th>
												<th></th>
												<th></th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($invoices as $key => $value){ ?>
											<tr>
												<td><?=$value->invoice_number?></td>
												<td><a href="<?=base_url('salesorder/update/salesorder_id/'.$value->invoice_salesorder_id)?>"><?=get_salesorder($value->invoice_salesorder_id)->salesorder_number?></a></td>
												<td><?=convert_datetime_to_date($value->invoice_create)?></td>
												<td><?=$value->invoice_client_company_name?></td>
												<td><?=$value->invoice_project_name?></td>
												<td><?=ucfirst(get_user($value->invoice_user_id)->user_name)?></td>
												<td><?=$value->invoice_expire?></td>
												<td><?=ucfirst($value->invoice_status)?></td>
												<td><?=strtoupper($value->invoice_currency).' '.money_format('%!n', $value->invoice_total)?></td>
												<td class="text-right">
													<a target="_blank" href="<?=base_url('/assets/images/pdf/invoice/'.$value->invoice_number.'.pdf')?>" data-toggle="tooltip" title="Print">
														<i class="glyphicon glyphicon-print"></i>
													</a>
												</td>
												<td class="text-right">
													<?php if(!check_permission('invoice_update', 'display')){ ?>
													<a href="<?=base_url('invoice/update/invoice_id/'.$value->invoice_id)?>" data-toggle="tooltip" title="Update">
														<i class="glyphicon glyphicon-edit"></i>
													</a>
													<?php }else{ ?>
													<i class="glyphicon glyphicon-edit"></i>
													<?php } ?>
												</td>
												<td class="text-right">
													<?php if(!check_permission('invoice_delete', 'display')){ ?>
													<a onclick="check_delete(<?=$value->invoice_id?>);" data-toggle="tooltip" title="Remove">
														<i class="glyphicon glyphicon-remove"></i>
													</a>
													<?php }else{ ?>
													<i class="glyphicon glyphicon-remove"></i>
													<?php } ?>
												</td>
											</tr>
											<?php } ?>

											<?php if(!$invoices){ ?>
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
										<a href="<?=base_url('invoicechecklist/select/invoice_status/processing')?>">Invoice checklist</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-usd"></i>
										<a href="<?=base_url('receivablereport')?>">Receivable report</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12">
                                    <!-- <blockquote>
                                        <i class="glyphicon glyphicon-ok-circle"></i>
                                        <a href="<?=base_url('waybillout')?>">Invoice waybill</a>
                                    </blockquote> -->
                                </div>
								<div class="col-md-3 col-sm-12"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
            <?php if(isset($popup_list) && !empty($popup_list)){ ?>
            <div class="popup-view">
                <div class="popup-header"><a href="javascript:" class="popup-close">Close</a></div>
                <div class="popup-list-area">
                    <form name="list">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>IN No</th>
                                <th>Confirmed</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($popup_list as $key => $value){ ?>
                                <tr>
                                    <td><a href="<?=base_url('invoice/update/invoice_id/'.$value->invoice_id)?>" data-toggle="tooltip" title="Update"><?=$value->invoice_number?></a></td>
                                    <td><?=$value->invoice_confirmed_date?></td>
                                    <td><?=ucfirst($value->invoice_status)?></td>
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