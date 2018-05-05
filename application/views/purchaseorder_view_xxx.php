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
			$('input[name="quotation_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');

			/*--------- date mask ---------*/
			$('.date-mask').mask('9999-99-99');

			/* quotationitem-insert-btn */
			$(document).on('click', '.quotationitem-insert-btn', function(){
				add_quotationitem_row();
			});

			/* quotationitem-delete-btn */
			$(document).on('click', '.quotationitem-delete-btn', function(){
				if(confirm('Confirm delete?')){
					$(this).parent().parent().parent().remove();
				}else{
					return false;
				}
			});

			/* client loader */
			$(document).on('change', 'select[name="quotation_client_id"]', function(){
				$('.scriptLoader').load('/crm/load', {'thisTableId': 'clientLoader', 'thisRecordId': $(this).val(), 't': timestamp()}, function(){
					clientLoader();
				});
			});

			/* product loader */
			$(document).on('change', 'select[name="quotationitem_product_id[]"]', function(){
				thisRow = $(this).parent().parent().parent().index();
				$('.scriptLoader').load('/crm/load', {'thisTableId': 'productLoader', 'thisRecordId': $(this).val(), 'thisRow': thisRow, 't': timestamp()}, function(){
					productLoader();
					calc();
				});
			});

			/* trigger calc */
			$(document).on('blur', 'input[name="quotation_hourlyrate"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="quotationitem_price[]"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="quotationitem_product_hour[]"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="quotationitem_quantity[]"]', function(){
				calc();
			});
		});

		function calc(){
			var total = 0;
			$.each($('table.list tbody tr'), function(key, val){
				if($(this).find('input[name="quotationitem_product_type_name[]"]').val() == 'service'){
					$(this).find('input[name="quotationitem_product_price[]"]').val($(this).find('input[name="quotationitem_product_hour[]"]').val() * $('input[name="quotation_hourlyrate"]').val());
				}
				$(this).find('input[name="quotationitem_subtotal[]"]').val($(this).find('input[name="quotationitem_product_price[]"]').val() * $(this).find('input[name="quotationitem_quantity[]"]').val()).css('display', 'none').fadeIn();
				total += parseInt($(this).find('input[name="quotationitem_subtotal[]"]').val());
				$('input[name="quotation_total"]').val(total).css('display', 'none').fadeIn();
			});
		}

		function check_delete(id){
			var answer = prompt("Confirm delete?");
			if(answer){
				$('input[name="quotation_id"]').val(id);
				$('input[name="quotation_delete_reason"]').val(encodeURI(answer));
				$('form[name="list"]').submit();
			}else{
				return false;
			}
		}

		<?php if($this->router->fetch_method() == 'update' || $this->router->fetch_method() == 'insert'){ ?>
		function add_quotationitem_row(){
			quotationitem_row = '';
			quotationitem_row += '<tr>';
			quotationitem_row += '<td>';
			quotationitem_row += '<div>';
			quotationitem_row += '<input name="quotationitem_quotation_id[]" type="hidden" value="" />';
			quotationitem_row += '<input name="quotationitem_product_type_name[]" type="hidden" value="" />';
			quotationitem_row += '<input id="quotationitem_product_code" name="quotationitem_product_code[]" type="text" class="form-control input-sm" placeholder="Code" value="" />';
			quotationitem_row += '</div>';
			quotationitem_row += '<div class="margin-top-10">';
			quotationitem_row += '<a class="btn btn-sm btn-primary quotationitem-delete-btn" data-toggle="tooltip" title="Delete">';
			quotationitem_row += '<i class="glyphicon glyphicon-remove"></i>';
			quotationitem_row += '</a>';
			quotationitem_row += '</div>';
			quotationitem_row += '</td>';
			quotationitem_row += '<td>';
			quotationitem_row += '<div>';
			quotationitem_row += '<select id="quotationitem_product_id" name="quotationitem_product_id[]" data-placeholder="Product" class="chosen-select">';
			quotationitem_row += '<option value></option>';
			<?php foreach($products as $key1 => $value1){ ?>
			quotationitem_row += '<option value="<?=$value1->product_id?>"><?=$value1->product_code.' - '.$value1->product_name?></option>';
			<?php } ?>
			quotationitem_row += '</select>';
			quotationitem_row += '</div>';
			quotationitem_row += '<div>';
			quotationitem_row += '<input id="quotationitem_product_name" name="quotationitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="" />';
			quotationitem_row += '</div>';
			quotationitem_row += '<div>';
			quotationitem_row += '<textarea id="quotationitem_product_detail" name="quotationitem_product_detail[]" class="form-control input-sm" placeholder="Detail" rows="3"></textarea>';
			quotationitem_row += '</div>';
			quotationitem_row += '</td>';
			quotationitem_row += '<td>';
			quotationitem_row += '<input id="quotationitem_product_price" name="quotationitem_product_price[]" type="text" class="form-control input-sm" placeholder="Price" value="" />';
			quotationitem_row += '<div class="input-group">';
			quotationitem_row += '<input id="quotationitem_product_hour" name="quotationitem_product_hour[]" type="text" class="form-control input-sm" placeholder="Hour" value="" />';
			quotationitem_row += '<span class="input-group-addon">hrs</span>';
			quotationitem_row += '</div>';
			quotationitem_row += '</td>';
			quotationitem_row += '<td>';
			quotationitem_row += '<input id="quotationitem_quantity" name="quotationitem_quantity[]" type="text" class="form-control input-sm" placeholder="Quantity" value="1" />';
			quotationitem_row += '</td>';
			quotationitem_row += '<td>';
			quotationitem_row += '<input readonly="readonly" id="quotationitem_subtotal" name="quotationitem_subtotal[]" type="text" class="form-control input-sm" placeholder="Subtotal" value="" />';
			quotationitem_row += '</td>';
			quotationitem_row += '</tr>';

			$('table.list tbody').append(quotationitem_row);
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

					<h2 class="col-sm-12"><a href="<?=base_url('purchaseorder')?>">Purchase order management</a> > <?=($this->router->fetch_method() == 'update') ? 'Update' : 'Insert'?> purchase order</h2>
					<blockquote class="blue">
						<p>Auto hide the purchased item, suppose when finish the order, no item inside</p>
						<p>Puchase order is a free format document</p>
						<p>Puchase order may has more item than SO, eg item is firewall, then also buy LAN cable separately</p>
						<p>Can 單獨 add purchase order</p>
					</blockquote>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="quotation_id" value="" />
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
											<label for="quotation_language">Language</label>
											<select id="quotation_language" name="quotation_language" data-placeholder="Language" class="chosen-select required">
												<option value></option>
												<?php
												if($quotation->quotation_language == ''){
													$quotation->quotation_language = 'en';
												}
												foreach($languages as $key => $value){
													$selected = ($value->language_name == $quotation->quotation_language) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value->language_name.'"'.$selected.'>'.strtoupper($value->language_name).'</option>';
												}
												?>
											</select>
										</p>
										<p class="form-group">
											<label for="quotation_currency">Currency</label>
											<select id="quotation_currency" name="quotation_currency" data-placeholder="Currency" class="chosen-select required">
												<option value></option>
												<?php
												if($quotation->quotation_currency == ''){
													$quotation->quotation_currency = 'rmb';
												}
												foreach($currencys as $key => $value){
													$selected = ($value->currency_name == $quotation->quotation_currency) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value->currency_name.'"'.$selected.'>'.strtoupper($value->currency_name).'</option>';
												}
												?>
											</select>
										</p>
										<p class="form-group">
											<label for="quotation_hourlyrate">Hourly rate</label>
											<input id="quotation_hourlyrate" name="quotation_hourlyrate" type="text" class="form-control input-sm" placeholder="Hourly rate" value="<?=$quotation->quotation_hourlyrate?>" />
										</p>
										<p class="form-group">
											<label for="attachment">Attachment</label>
											<input id="attachment" name="attachment" type="file" class="form-control input-sm" placeholder="Attachment" accept="image/*" />
										</p>
									</div>
									<div class="col-sm-9 col-xs-12">
										<h4 class="corpcolor-font">Quotation</h4>
										<div class="row">
											<div class="col-sm-6 col-xs-6">
												<table class="table table-condensed table-borderless">
													<tr>
														<td colspan="2">
															<select id="quotation_client_id" name="quotation_client_id" data-placeholder="Client" class="chosen-select">
																<option value></option>
																<?php
																foreach($clients as $key1 => $value1){
																	$selected = ($value1->client_id == $quotation->quotation_client_id) ? ' selected="selected"' : "" ;
																	echo '<option value="'.$value1->client_id.'"'.$selected.'>'.$value1->client_firstname.' '.$value1->client_lastname.'</option>';
																}
																?>
															</select>
														</td>
													</tr>
													<tr>
														<td><label for="quotation_client_company_name">Client</label></td>
														<td><input id="quotation_client_company_name" name="quotation_client_company_name" type="text" class="form-control input-sm required" placeholder="Company/Domain/Client" value="<?=$quotation->quotation_client_company_name?>" /></td>
													</tr>
													<tr>
														<td><label for="quotation_client_company_address">Address</label></td>
														<td><textarea id="quotation_client_company_address" name="quotation_client_company_address" class="form-control input-sm" placeholder="Address" rows="3"><?=$quotation->quotation_client_company_address?></textarea></td>
													</tr>
													<tr>
														<td><label for="quotation_client_name">Attn.</label></td>
														<td><input id="quotation_client_name" name="quotation_client_name" type="text" class="form-control input-sm required" placeholder="Attn." value="<?=$quotation->quotation_client_name?>" /></td>
													</tr>
													<tr>
														<td><label for="quotation_client_phone">Phone</label></td>
														<td><input id="quotation_client_phone" name="quotation_client_phone" type="text" class="form-control input-sm" placeholder="Phone" value="<?=$quotation->quotation_client_phone?>" /></td>
													</tr>
													<tr>
														<td><label for="quotation_client_fax">Fax</label></td>
														<td><input id="quotation_client_fax" name="quotation_client_fax" type="text" class="form-control input-sm" placeholder="Fax" value="<?=$quotation->quotation_client_fax?>" /></td>
													</tr>
													<tr>
														<td><label for="quotation_client_email">Email</label></td>
														<td><input id="quotation_client_email" name="quotation_client_email" type="text" class="form-control input-sm" placeholder="Email" value="<?=$quotation->quotation_client_email?>" /></td>
													</tr>
												</table>
											</div>
											<div class="col-sm-1 col-xs-1">
											</div>
											<div class="col-sm-5 col-xs-5">
												<table class="table table-condensed table-borderless">
													<tr>
														<td><label for="quotation_number">Quotation#</label></td>
														<td>
															<div class="input-group">
																<input readonly="readonly" id="quotation_number" name="quotation_number" type="text" class="form-control input-sm" placeholder="Quotation#" value="<?=$quotation->quotation_number?>" />
																<span class="input-group-addon"><?='v'.$quotation->quotation_version?></span>
															</div>
														</td>
													</tr>
													<tr>
														<td><label for="quotation_issue">Issue date</label></td>
														<td><input id="quotation_issue" name="quotation_issue" type="text" class="form-control input-sm required" placeholder="Issue date" value="<?=($quotation->quotation_issue != '') ? $quotation->quotation_issue : date('Y-m-d')?>" /></td>
													</tr>
													<tr>
														<td><label for="quotation_user_name">Saleman</label></td>
														<td><input id="quotation_user_name" name="quotation_user_name" type="text" class="form-control input-sm required" placeholder="Saleman" value="<?=$user->user_name?>" /></td>
													</tr>
													<tr>
														<td><label for="quotation_user_phone">Phone</label></td>
														<td><input id="quotation_user_phone" name="quotation_user_phone" type="text" class="form-control input-sm required" placeholder="Phone" value="<?=$user->user_phone?>" /></td>
													</tr>
													<tr>
														<td><label for="quotation_user_fax">Fax</label></td>
														<td><input id="quotation_user_fax" name="quotation_user_fax" type="text" class="form-control input-sm" placeholder="Fax" value="<?=$user->user_fax?>" /></td>
													</tr>
													<tr>
														<td><label for="quotation_user_email">Email</label></td>
														<td><input id="quotation_user_email" name="quotation_user_email" type="text" class="form-control input-sm required" placeholder="Email" value="<?=$user->user_email?>" /></td>
													</tr>
												</table>
											</div>
										</div>
										<div class="list-area">
											<table class="table list" id="quotation">
												<thead>
													<tr>
														<th width="10%">
															<a class="btn btn-sm btn-primary quotationitem-insert-btn" data-toggle="tooltip" title="Insert">
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
													<?php foreach($quotationitems as $key => $value){ ?>
													<tr>
														<td>
															<div>
																<input name="quotationitem_quotation_id[]" type="hidden" value="" />
																<input name="quotationitem_product_type_name[]" type="hidden" value="<?=$value->quotationitem_product_type_name?>" />
																<input id="quotationitem_product_code" name="quotationitem_product_code[]" type="text" class="form-control input-sm" placeholder="Code" value="<?=$value->quotationitem_product_code?>" />
															</div>
															<div class="margin-top-10">
																<a class="btn btn-sm btn-primary quotationitem-delete-btn" data-toggle="tooltip" title="Delete">
																	<i class="glyphicon glyphicon-remove"></i>
																</a>
															</div>
														</td>
														<td>
															<div>
																<select id="quotationitem_product_id" name="quotationitem_product_id[]" data-placeholder="Product" class="chosen-select">
																	<option value></option>
																	<?php
																	foreach($products as $key1 => $value1){
																		$selected = ($value1->product_id == $value->quotationitem_product_id) ? ' selected="selected"' : "" ;
																		echo '<option value="'.$value1->product_id.'"'.$selected.'>'.$value1->product_name.'</option>';
																	}
																	?>
																</select>
															</div>
															<div>
																<input id="quotationitem_product_name" name="quotationitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="<?=$value->quotationitem_product_name?>" />
															</div>
															<div>
																<textarea id="quotationitem_product_detail" name="quotationitem_product_detail[]" class="form-control input-sm" placeholder="Detail" rows="3"><?=$value->quotationitem_product_detail?></textarea>
															</div>
														</td>
														<td>
															<input id="quotationitem_product_price" name="quotationitem_product_price[]" type="text" class="form-control input-sm" placeholder="Price" value="<?=$value->quotationitem_product_price?>" />
															<div class="input-group">
																<input id="quotationitem_product_hour" name="quotationitem_product_hour[]" type="text" class="form-control input-sm" placeholder="Hour" value="<?=$value->quotationitem_product_hour?>" />
																<span class="input-group-addon">hrs</span>
															</div>
														</td>
														<td>
															<input id="quotationitem_quantity" name="quotationitem_quantity[]" type="text" class="form-control input-sm" placeholder="Quantity" value="<?=($value->quotationitem_quantity) ? $value->quotationitem_quantity : '1'?>" />
														</td>
														<td>
															<input readonly="readonly" id="quotationitem_subtotal" name="quotationitem_subtotal[]" type="text" class="form-control input-sm" placeholder="Subtotal" value="<?=$value->quotationitem_subtotal?>" />
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
														<th><input readonly="readonly" id="quotation_total" name="quotation_total" type="text" class="form-control input-sm" placeholder="Grand total" value="<?=($quotation->quotation_total) ? $quotation->quotation_total : '0'?>" /></th>
													</tr>
												</tfoot>
											</table>
										</div>
										<hr />
										<p class="form-group">
											<label for="quotation_remark">Remark</label>
											<textarea id="quotation_remark" name="quotation_remark" class="form-control input-sm" placeholder="Remark" rows="3"><?=$quotation->quotation_remark?></textarea>
										</p>
										<p class="form-group">
											<label for="quotation_warranty">Warranty</label>
											<textarea id="quotation_warranty" name="quotation_warranty" class="form-control input-sm" placeholder="Warranty" rows="3"><?=$quotation->quotation_warranty?></textarea>
										</p>
										<p class="form-group">
											<label for="quotation_delivery">Delivery</label>
											<textarea id="quotation_delivery" name="quotation_delivery" class="form-control input-sm" placeholder="Delivery" rows="3"><?=$quotation->quotation_delivery?></textarea>
										</p>
										<p class="form-group">
											<label for="quotation_payment">Payment</label>
											<textarea id="quotation_payment" name="quotation_payment" class="form-control input-sm" placeholder="Payment" rows="3"><?=$quotation->quotation_payment?></textarea>
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

					<h2 class="col-sm-12">Purchase order management</h2>
					<blockquote class="blue">
						<p>upload vendor delivery note</p>
						<p>Stock arrival update</p>
						<p>when Stock arrival, show followup status in dashboard</p>
						<p>Processing / cancel / parial arrival / complete</p>
						<p>Can 單獨 add purchase order</p>
					</blockquote>

					<div class="content-column-area col-md-12 col-sm-12">

						<div class="fieldset">
							<div class="search-area">

								<form quotation="form" method="get">
									<input type="hidden" name="quotation_id" />
									<table>
										<tbody>
											<tr>
												<td width="90%">
													<div class="row">
														<div class="col-sm-2"><h6>Purchase Order</h6></div>
														<div class="col-sm-2">
															<input type="text" name="quotation_client_company_name_quotation_client_name_like" class="form-control input-sm" placeholder="PONo From" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="quotation_client_company_name_quotation_client_name_like" class="form-control input-sm" placeholder="PONo To" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="quotation_client_company_name_quotation_client_name_like" class="form-control input-sm date-mask" placeholder="Date From (YYYY-MM-DD)" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="quotation_client_company_name_quotation_client_name_like" class="form-control input-sm date-mask" placeholder="Date To (YYYY-MM-DD)" value="" />
														</div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
													</div>
													<div class="row">
														<div class="col-sm-2"></div>
														<div class="col-sm-2">
															<input type="text" name="quotation_client_company_name_quotation_client_name_like" class="form-control input-sm" placeholder="SONo From" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="quotation_client_company_name_quotation_client_name_like" class="form-control input-sm" placeholder="SONo To" value="" />
														</div>
														<div class="col-sm-2">
															<select id="quotation_client_company_name_quotation_client_name_like" name="quotation_client_company_name_quotation_client_name_like" data-placeholder="Status" class="chosen-select">
																<option value></option>
															</select>
														</div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
													</div>
													<div class="row">
														<div class="col-sm-2"><h6>Vendor</h6></div>
														<div class="col-sm-2">
															<input type="text" name="quotation_client_company_name_quotation_client_name_like" class="form-control input-sm" placeholder="Vendor Name" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="quotation_client_company_name_quotation_client_name_like" class="form-control input-sm" placeholder="User" value="" />
														</div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
													</div>
													<div class="row">
														<div class="col-sm-2"><h6>Project</h6></div>
														<div class="col-sm-2">
															<input type="text" name="quotation_client_company_name_quotation_client_name_like" class="form-control input-sm" placeholder="Project Name" value="" />
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
															<input type="text" name="quotation_client_company_name_quotation_client_name_like" class="form-control input-sm" placeholder="Item Code" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="quotation_client_company_name_quotation_client_name_like" class="form-control input-sm" placeholder="Item Description" value="" />
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
								<form name="list" action="<?=base_url('quotation/delete')?>" method="post">
									<input type="hidden" name="quotation_id" />
									<input type="hidden" name="quotation_delete_reason" />
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
												<th>Delivery Date</th>
												<th>Status</th>
												<th>Currency</th>
												<th>Total</th>
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
											<?php for($i=0; $i<12; $i++){ ?>
											<tr>
												<td><a href="<?=base_url('purchaseorder/update')?>">PO1612074-1</a></td>
												<td><a href="<?=base_url('salesorder/update')?>">SO1612074-1</a></td>
												<td>2016-12-30</td>
												<td>EC Solutions Limited</td>
												<td>Week 1651</td>
												<td>Danial Lo</td>
												<td>2016-12-30</td>
												<td>Processing/Cancel/Partial Arrival/Complete</td>
												<td>HKD</td>
												<td>32,000</td>
												<td class="text-right">
													<a href="<?=base_url('purchaseorder/setting')?>" data-toggle="tooltip" title="Setting">
														<i class="glyphicon glyphicon-cog"></i>
													</a>
												</td>
												<td class="text-right">
													<a href="<?=base_url('purchaseorder/update')?>" data-toggle="tooltip" title="Update">
														<i class="glyphicon glyphicon-edit"></i>
													</a>
												</td>
												<td class="text-right">
													<a href="<?=base_url('purchaseorder/update')?>" data-toggle="tooltip" title="Remove">
														<i class="glyphicon glyphicon-remove"></i>
													</a>
												</td>
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
				</div>
			</div>

		</div>
		<?php } ?>

		








































		<?php if($this->router->fetch_method() == 'setting'){ ?>
		<div class="content-area">

			<div class="container-fluid">
				<div class="row">

					<h2 class="col-sm-12"><a href="<?=base_url('purchaseorder')?>">Purchase order management</a> > <?=($this->router->fetch_method() == 'update') ? 'Update' : 'Insert'?> purchase order</h2>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="quotation_id" value="" />
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
											<label for="quotation_hourlyrate">Hourly rate</label>
											<input id="quotation_hourlyrate" name="quotation_hourlyrate" type="text" class="form-control input-sm" placeholder="Hourly rate" value="" />
										</p>
									</div>
									<div class="col-sm-9 col-xs-12">
										<h4 class="corpcolor-font">Purchase order</h4>
										<div class="list-area">
											<table class="table list" id="quotation">
												<thead>
													<tr>
														<th width="10%">
															<a class="btn btn-sm btn-primary quotationitem-insert-btn" data-toggle="tooltip" title="Insert">
																<i class="glyphicon glyphicon-plus"></i>
															</a>
														</th>
														<th>Detail</th>
														<th width="32%">
															Stock Arrival
														</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($quotationitems as $key => $value){ ?>
													<tr>
														<td>
															<div>
																<input name="quotationitem_quotation_id[]" type="hidden" value="" />
																<input name="quotationitem_product_type_name[]" type="hidden" value="<?=$value->quotationitem_product_type_name?>" />
																<input id="quotationitem_product_code" name="quotationitem_product_code[]" type="text" class="form-control input-sm" placeholder="Code" value="<?=$value->quotationitem_product_code?>" />
															</div>
															<div class="margin-top-10">
																<a class="btn btn-sm btn-primary quotationitem-delete-btn" data-toggle="tooltip" title="Delete">
																	<i class="glyphicon glyphicon-remove"></i>
																</a>
															</div>
														</td>
														<td>
															<div>
																<select id="quotationitem_product_id" name="quotationitem_product_id[]" data-placeholder="Product" class="chosen-select">
																	<option value></option>
																	<?php
																	foreach($products as $key1 => $value1){
																		$selected = ($value1->product_id == $value->quotationitem_product_id) ? ' selected="selected"' : "" ;
																		echo '<option value="'.$value1->product_id.'"'.$selected.'>'.$value1->product_name.'</option>';
																	}
																	?>
																</select>
															</div>
															<div>
																<input id="quotationitem_product_name" name="quotationitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="<?=$value->quotationitem_product_name?>" />
															</div>
															<div>
																<textarea id="quotationitem_product_detail" name="quotationitem_product_detail[]" class="form-control input-sm" placeholder="Detail" rows="3"><?=$value->quotationitem_product_detail?></textarea>
															</div>
														</td>
														<td>
															<div class="input-group">
																<span class="input-group-addon">10</span>
																<input id="quotationitem_product_hour" name="quotationitem_product_hour[]" type="text" class="form-control input-sm" placeholder="Hour" value="0" />
															</div>
														</td>
													</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
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












































		<?php $this->load->view('inc/footer-area.php'); ?>

	</body>
</html>

<div class="scriptLoader"></div>