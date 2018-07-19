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
		<script src="<?php echo base_url('assets/js/function.js'); ?>"></script>

		<script>
		$(function(){
			$('input[name="invoice_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');

			/*--------- date mask ---------*/
			$('.date-mask').mask('9999-99-99');

			/* invoiceitem-insert-btn */
			$(document).on('click', '.invoiceitem-insert-btn', function(){
				add_invoiceitem_row();
			});

			/* invoiceitem-delete-btn */
			$(document).on('click', '.invoiceitem-delete-btn', function(){
				if(confirm('Confirm delete?')){
					$(this).parent().parent().parent().remove();
				}else{
					return false;
				}
			});

			/* client loader */
			$(document).on('change', 'select[name="invoice_client_id"]', function(){
				$('.scriptLoader').load('/load', {'thisTableId': 'clientLoader', 'thisRecordId': $(this).val(), 't': timestamp()}, function(){
					clientLoader();
				});
			});

			/* product loader */
			$(document).on('change', 'select[name="invoiceitem_product_id[]"]', function(){
				thisRow = $(this).parent().parent().parent().index();
				$('.scriptLoader').load('/load', {'thisTableId': 'productLoader', 'thisRecordId': $(this).val(), 'thisRow': thisRow, 't': timestamp()}, function(){
					productLoader();
					calc();
				});
			});

			/* terms loader */
			$(document).on('change', 'select[name="payment"]', function(){
				$('.scriptLoader').load('/load', {'thisTableId': 'termsLoader', 'thisTableField': 'invoice_payment', 'thisRecordId': $(this).val(), 't': timestamp()}, function(){
					termsLoader();
				});
			});

			/* trigger calc */
			$(document).on('blur', 'input[name="invoice_hourlyrate"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="invoiceitem_price[]"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="invoiceitem_product_hour[]"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="invoiceitem_quantity[]"]', function(){
				calc();
			});
		});

		function calc(){
			var total = 0;
			$.each($('table.list tbody tr'), function(key, val){
				if($(this).find('input[name="invoiceitem_product_type_name[]"]').val() == 'service'){
					$(this).find('input[name="invoiceitem_product_price[]"]').val($(this).find('input[name="invoiceitem_product_hour[]"]').val() * $('input[name="invoice_hourlyrate"]').val());
				}
				$(this).find('input[name="invoiceitem_subtotal[]"]').val($(this).find('input[name="invoiceitem_product_price[]"]').val() * $(this).find('input[name="invoiceitem_quantity[]"]').val()).css('display', 'none').fadeIn();
				total += parseInt($(this).find('input[name="invoiceitem_subtotal[]"]').val());
				$('input[name="invoice_total"]').val(total).css('display', 'none').fadeIn();
			});
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

		<?php if($this->router->fetch_method() == 'update' || $this->router->fetch_method() == 'insert'){ ?>
		function add_invoiceitem_row(){
			invoiceitem_row = '';
			invoiceitem_row += '<tr>';
			invoiceitem_row += '<td>';
			invoiceitem_row += '<div>';
			invoiceitem_row += '<input name="invoiceitem_invoice_id[]" type="hidden" value="" />';
			invoiceitem_row += '<input name="invoiceitem_product_type_name[]" type="hidden" value="" />';
			invoiceitem_row += '<input id="invoiceitem_product_code" name="invoiceitem_product_code[]" type="text" class="form-control input-sm" placeholder="Code" value="" />';
			invoiceitem_row += '</div>';
			invoiceitem_row += '<div class="margin-top-10">';
			invoiceitem_row += '<a class="btn btn-sm btn-primary invoiceitem-delete-btn" data-toggle="tooltip" title="Delete">';
			invoiceitem_row += '<i class="glyphicon glyphicon-remove"></i>';
			invoiceitem_row += '</a>';
			invoiceitem_row += '</div>';
			invoiceitem_row += '</td>';
			invoiceitem_row += '<td>';
			invoiceitem_row += '<div>';
			invoiceitem_row += '<select id="invoiceitem_product_id" name="invoiceitem_product_id[]" data-placeholder="Product" class="chosen-select">';
			invoiceitem_row += '<option value></option>';
			<?php foreach($products as $key1 => $value1){ ?>
			invoiceitem_row += '<option value="<?=$value1->product_id?>"><?=$value1->product_code.' - '.$value1->product_name?></option>';
			<?php } ?>
			invoiceitem_row += '</select>';
			invoiceitem_row += '</div>';
			invoiceitem_row += '<div>';
			invoiceitem_row += '<input id="invoiceitem_product_name" name="invoiceitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="" />';
			invoiceitem_row += '</div>';
			invoiceitem_row += '<div>';
			invoiceitem_row += '<textarea id="invoiceitem_product_detail" name="invoiceitem_product_detail[]" class="form-control input-sm" placeholder="Detail" rows="3"></textarea>';
			invoiceitem_row += '</div>';
			invoiceitem_row += '</td>';
			invoiceitem_row += '<td>';
			invoiceitem_row += '<input id="invoiceitem_product_price" name="invoiceitem_product_price[]" type="text" class="form-control input-sm" placeholder="Price" value="" />';
			invoiceitem_row += '<div class="input-group">';
			invoiceitem_row += '<input id="invoiceitem_product_hour" name="invoiceitem_product_hour[]" type="text" class="form-control input-sm" placeholder="Hour" value="" />';
			invoiceitem_row += '<span class="input-group-addon">hrs</span>';
			invoiceitem_row += '</div>';
			invoiceitem_row += '</td>';
			invoiceitem_row += '<td>';
			invoiceitem_row += '<input id="invoiceitem_quantity" name="invoiceitem_quantity[]" type="text" class="form-control input-sm" placeholder="Quantity" value="1" />';
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

		








































		<?php if($this->router->fetch_method() == 'insert'){ ?>
		<div class="content-area">

			<div class="container-fluid">
				<div class="row">

					<h2 class="col-sm-12"><a href="<?=base_url('invoice')?>">Invoice management</a> > <?=($this->router->fetch_method() == 'update') ? 'Update' : 'Insert'?> invoice</h2>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="invoice_id" value="" />
							<input type="hidden" name="referrer" value="<?=$this->agent->referrer()?>" />
							<div class="fieldset">
								<div class="row">
									
									<div class="col-sm-3 col-xs-12 pull-right">
										<blockquote>
											<h4 class="corpcolor-font">Instructions</h4>
											<p><span class="highlight">*</span> is a required field</p>
										</blockquote>
										<h4 class="corpcolor-font">Setting</h4>
										<p class="form-group">
											<label for="invoice_language">Language</label>
											<select id="invoice_language" name="invoice_language" data-placeholder="Language" class="chosen-select required">
												<option value></option>
												<?php
												if($invoice->invoice_language == ''){
													$invoice->invoice_language = 'en';
												}
												foreach($languages as $key => $value){
													$selected = ($value->language_name == $invoice->invoice_language) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value->language_name.'"'.$selected.'>'.strtoupper($value->language_name).'</option>';
												}
												?>
											</select>
										</p>
										<p class="form-group">
											<label for="invoice_currency">Currency</label>
											<select id="invoice_currency" name="invoice_currency" data-placeholder="Currency" class="chosen-select required">
												<option value></option>
												<?php
												if($invoice->invoice_currency == ''){
													$invoice->invoice_currency = 'rmb';
												}
												foreach($currencys as $key => $value){
													$selected = ($value->currency_name == $invoice->invoice_currency) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value->currency_name.'"'.$selected.'>'.strtoupper($value->currency_name).'</option>';
												}
												?>
											</select>
										</p>
										<p class="form-group">
											<label for="attachment">Attachment</label>
											<input id="attachment" name="attachment" type="file" class="form-control input-sm" placeholder="Attachment" accept="image/*" />
										</p>
									</div>
									<div class="col-sm-9 col-xs-12">
										<h4 class="corpcolor-font">Invoice</h4>
										<div class="row">
											<div class="col-sm-6 col-xs-6">
												<table class="table table-condensed table-borderless">
													<tr>
														<td colspan="2">
															<select id="invoice_client_id" name="invoice_client_id" data-placeholder="Client" class="chosen-select">
																<option value></option>
																<?php
																foreach($clients as $key1 => $value1){
																	$selected = ($value1->client_id == $invoice->invoice_client_id) ? ' selected="selected"' : "" ;
																	echo '<option value="'.$value1->client_id.'"'.$selected.'>'.$value1->client_firstname.' '.$value1->client_lastname.'</option>';
																}
																?>
															</select>
														</td>
													</tr>
													<tr>
														<td><label for="invoice_client_company_name">To</label></td>
														<td><input id="invoice_client_company_name" name="invoice_client_company_name" type="text" class="form-control input-sm required" placeholder="Company/Domain/Client" value="<?=$invoice->invoice_client_company_name?>" /></td>
													</tr>
													<tr>
														<td><label for="invoice_client_company_address">Address</label></td>
														<td><textarea id="invoice_client_company_address" name="invoice_client_company_address" class="form-control input-sm" placeholder="Address" rows="3"><?=$invoice->invoice_client_company_address?></textarea></td>
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
														<td><label for="invoice_client_name">Attn.</label></td>
														<td><input id="invoice_client_name" name="invoice_client_name" type="text" class="form-control input-sm required" placeholder="Attn." value="<?=$invoice->invoice_client_name?>" /></td>
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
																<input readonly="readonly" id="invoice_number" name="invoice_number" type="text" class="form-control input-sm" placeholder="Invoice#" value="<?=$invoice->invoice_number?>" />
																<span class="input-group-addon"><?='v'.$invoice->invoice_version?></span>
															</div>
														</td>
													</tr>
													<tr>
														<td><label for="invoice_issue">Issue date</label></td>
														<td><input id="invoice_issue" name="invoice_issue" type="text" class="form-control input-sm required" placeholder="Issue date" value="<?=($invoice->invoice_issue != '') ? $invoice->invoice_issue : date('Y-m-d')?>" /></td>
													</tr>
													<tr>
														<td><label for="invoice_user_name">Saleman</label></td>
														<td><input id="invoice_user_name" name="invoice_user_name" type="text" class="form-control input-sm required" placeholder="Saleman" value="<?=$user->user_name?>" /></td>
													</tr>
													<tr>
														<td><label for="invoice_user_phone">Phone</label></td>
														<td><input id="invoice_user_phone" name="invoice_user_phone" type="text" class="form-control input-sm required" placeholder="Phone" value="<?=$user->user_phone?>" /></td>
													</tr>
													<tr>
														<td><label for="invoice_user_fax">Fax</label></td>
														<td><input id="invoice_user_fax" name="invoice_user_fax" type="text" class="form-control input-sm" placeholder="Fax" value="<?=$user->user_fax?>" /></td>
													</tr>
													<tr>
														<td><label for="invoice_user_email">Email</label></td>
														<td><input id="invoice_user_email" name="invoice_user_email" type="text" class="form-control input-sm required" placeholder="Email" value="<?=$user->user_email?>" /></td>
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
														<th>Detail</th>
														<th width="12%">Price</th>
														<th width="8%">Quantity</th>
														<th width="12%">Subtotal</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($invoiceitems as $key => $value){ ?>
													<tr>
														<td>
															<div>
																<input name="invoiceitem_invoice_id[]" type="hidden" value="" />
																<input name="invoiceitem_product_type_name[]" type="hidden" value="<?=$value->invoiceitem_product_type_name?>" />
																<input id="invoiceitem_product_code" name="invoiceitem_product_code[]" type="text" class="form-control input-sm" placeholder="Code" value="<?=$value->invoiceitem_product_code?>" />
															</div>
															<div class="margin-top-10">
																<a class="btn btn-sm btn-primary invoiceitem-delete-btn" data-toggle="tooltip" title="Delete">
																	<i class="glyphicon glyphicon-remove"></i>
																</a>
															</div>
														</td>
														<td>
															<div>
																<select id="invoiceitem_product_id" name="invoiceitem_product_id[]" data-placeholder="Product" class="chosen-select">
																	<option value></option>
																	<?php
																	foreach($products as $key1 => $value1){
																		$selected = ($value1->product_id == $value->invoiceitem_product_id) ? ' selected="selected"' : "" ;
																		echo '<option value="'.$value1->product_id.'"'.$selected.'>'.$value1->product_name.'</option>';
																	}
																	?>
																</select>
															</div>
															<div>
																<input id="invoiceitem_product_name" name="invoiceitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="<?=$value->invoiceitem_product_name?>" />
															</div>
															<div>
																<textarea id="invoiceitem_product_detail" name="invoiceitem_product_detail[]" class="form-control input-sm" placeholder="Detail" rows="3"><?=$value->invoiceitem_product_detail?></textarea>
															</div>
														</td>
														<td>
															<input id="invoiceitem_product_price" name="invoiceitem_product_price[]" type="text" class="form-control input-sm" placeholder="Price" value="<?=$value->invoiceitem_product_price?>" />
															<div class="input-group">
																<input id="invoiceitem_product_hour" name="invoiceitem_product_hour[]" type="text" class="form-control input-sm" placeholder="Hour" value="<?=$value->invoiceitem_product_hour?>" />
																<span class="input-group-addon">hrs</span>
															</div>
														</td>
														<td>
															<input id="invoiceitem_quantity" name="invoiceitem_quantity[]" type="text" class="form-control input-sm" placeholder="Quantity" value="<?=($value->invoiceitem_quantity) ? $value->invoiceitem_quantity : '1'?>" />
														</td>
														<td>
															<input readonly="readonly" id="invoiceitem_subtotal" name="invoiceitem_subtotal[]" type="text" class="form-control input-sm" placeholder="Subtotal" value="<?=$value->invoiceitem_subtotal?>" />
														</td>
													</tr>
													<?php } ?>
												</tbody>
												<tfoot>
													<tr>
														<th></th>
														<th></th>
														<th></th>
														<th>Grand total</th>
														<th><input readonly="readonly" id="invoice_total" name="invoice_total" type="text" class="form-control input-sm" placeholder="Grand total" value="<?=($invoice->invoice_total) ? $invoice->invoice_total : '0'?>" /></th>
													</tr>
												</tfoot>
											</table>
										</div>
										<hr />
										<p class="form-group">
											<label for="invoice_payment">Payment</label>
											<select id="remark" name="payment" data-placeholder="Invoice payment" class="chosen-select">
												<option value></option>
												<?php
												foreach($payments as $key1 => $value1){
													echo '<option value="'.$value1->terms_id.'">'.$value1->terms_name.'</option>';
												}
												?>
											</select>
											<textarea id="invoice_payment" name="invoice_payment" class="form-control input-sm" placeholder="Payment" rows="3"><?=$invoice->invoice_payment?></textarea>
										</p>
									</div>
								</div>

								<div class="row">
									<div class="col-xs-12">
										<button type="submit" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-floppy-disk"></i> Save</button>
									</div>
								</div>

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
														<div class="col-sm-2"><h6>Sales Order</h6></div>
														<div class="col-sm-2">
															<input type="text" name="invoice_number_like" class="form-control input-sm" placeholder="SONo" value="" />
														</div>
														<!-- <div class="col-sm-2">
															<input type="text" name="invoice_number_greateq" class="form-control input-sm" placeholder="SONo From" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="invoice_number_smalleq" class="form-control input-sm" placeholder="SONo To" value="" />
														</div> -->
														<div class="col-sm-2">
															<span class="input-group date datetimepicker">
																<input id="invoice_create_greateq" name="invoice_create_greateq" type="text" class="form-control input-sm date-mask" placeholder="Date From (YYYY-MM-DD)" />
																<span class="input-group-addon">
																	<span class="glyphicon glyphicon-calendar"></span>
																</span>
															</span>
															<!-- <input type="text" name="invoice_create_greateq" class="form-control input-sm date-mask" placeholder="Date From (YYYY-MM-DD)" value="" /> -->
														</div>
														<div class="col-sm-2">
															<span class="input-group date datetimepicker">
																<input id="invoice_create_smalleq" name="invoice_create_smalleq" type="text" class="form-control input-sm date-mask" placeholder="Date To (YYYY-MM-DD)" />
																<span class="input-group-addon">
																	<span class="glyphicon glyphicon-calendar"></span>
																</span>
															</span>
															<!-- <input type="text" name="invoice_create_smalleq" class="form-control input-sm date-mask" placeholder="Date To (YYYY-MM-DD)" value="" /> -->
														</div>
														<div class="col-sm-2">
															<input type="text" name="quotation_number_like" class="form-control input-sm" placeholder="QONo" value="" />
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
													<!-- <div class="row">
														<div class="col-sm-2"></div>
														<div class="col-sm-2">
															<input type="text" name="quotation_number_greateq" class="form-control input-sm" placeholder="QONo From" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="quotation_number_smalleq" class="form-control input-sm" placeholder="QONo To" value="" />
														</div>
														<div class="col-sm-2">
															<select id="invoice_status" name="invoice_status" data-placeholder="Status" class="chosen-select">
																<option value></option>
																<?php foreach($statuss as $key => $value){ ?>
																<option value="<?=$value->status_name?>"><?=ucfirst($value->status_name)?></option>
																<?php } ?>
															</select>
														</div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
													</div> -->
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
															<input type="text" name="quotationitem_product_code_like" class="form-control input-sm" placeholder="Item Code" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="quotationitem_product_name_like" class="form-control input-sm" placeholder="Item Name" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="quotationitem_product_detail_like" class="form-control input-sm" placeholder="Item Description" value="" />
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
												<th>SO No</th>
												<th>QO No</th>
												<th>PO No</th>
												<th>Create</th>
												<th>Customer</th>
												<th>Project</th>
												<th>Sales</th>
												<th>Status</th>
												<th>Currency</th>
												<th>Cost</th>
												<th>Total</th>
												<th>Estimate GP</th>
												<th></th>
												<th class="text-right">
													<!-- <a href="<?=base_url('invoice/insert')?>" data-toggle="tooltip" title="Insert">
														<i class="glyphicon glyphicon-plus"></i>
													</a> -->
												</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($invoices as $key => $value){ ?>
											<tr>
												<td><a href="<?=base_url('invoice/update')?>"><?=$value->invoice_number?></a></td>
												<td><a href="<?=base_url('quotation/update/quotation_id/'.$value->invoice_quotation_id)?>"><?=get_quotation($value->invoice_quotation_id)->quotation_number?></a></td>
												<td><a href="<?=base_url('purchaseorder/insert/invoice_id/'.$value->invoice_id)?>"><i class="glyphicon glyphicon-plus"></i> PO</a></td>
												<td><?=convert_datetime_to_date($value->invoice_create)?></td>
												<td><?=$value->invoice_client_company_name?></td>
												<td><?=$value->invoice_project_name?></td>
												<td><?=ucfirst(get_user($value->invoice_user_id)->user_name)?></td>
												<td><?=ucfirst($value->invoice_status)?></td>
												<td><?=strtoupper($value->invoice_currency)?></td>
												<td><span class="blue">PO subtotal</span></td>
												<td><?=money_format('%!n', $value->invoice_total)?></td>
												<td><span class="blue">Total - cost</span></td>
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
					</div>
					<div class="blue">
						<!-- <p>sales order handler totally same to quotation handler</p>
						<p>when sales order created, show followup status in dashboard, reminder user to follow PO & invoice</p>
						<p>Can view own SO, Invoice, PO, DN</p>
						<p>DONE: Can view own or DOWNLINE quotation</p>
						<p>DONE: When update SO, admin & operatin can update sales</p>
						<p>DONE: When update SO, can change project name</p>
						<p>DONE: Has permission can update the SO</p>
						<p>DONE: SO total amount是不變的, 只可以改入面數量， 但total amount不能變, 如SO amount最後有問題要轉，就要cancel左張SO. 由Quotation再做過</p>
						<p>DONE: Processing / cancel / complete</p>
						<p>DONE: operation can set commission rate for this sales order</p> -->
					</div>
				</div>
			</div>

		</div>
		<?php } ?>












































		<?php $this->load->view('inc/footer-area.php'); ?>

	</body>
</html>

<div class="scriptLoader"></div>