<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Delivery note management</title>

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
			$('input[name="deliverynote_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');

			/*--------- date mask ---------*/
			$('.date-mask').mask('9999-99-99');

			/* deliverynoteitem-insert-btn */
			$(document).on('click', '.deliverynoteitem-insert-btn', function(){
				add_deliverynoteitem_row();
			});

			/* deliverynoteitem-delete-btn */
			$(document).on('click', '.deliverynoteitem-delete-btn', function(){
				if(confirm('Confirm delete?')){
					$(this).parent().parent().parent().remove();
				}else{
					return false;
				}
			});

			/* client loader */
			$(document).on('change', 'select[name="deliverynote_client_id"]', function(){
				$('.scriptLoader').load('/crm/load', {'thisTableId': 'clientLoader', 'thisRecordId': $(this).val(), 't': timestamp()}, function(){
					clientLoader();
				});
			});

			/* product loader */
			$(document).on('change', 'select[name="deliverynoteitem_product_id[]"]', function(){
				thisRow = $(this).parent().parent().parent().index();
				$('.scriptLoader').load('/crm/load', {'thisTableId': 'productLoader', 'thisRecordId': $(this).val(), 'thisRow': thisRow, 't': timestamp()}, function(){
					productLoader();
					calc();
				});
			});

			/* trigger calc */
			$(document).on('blur', 'input[name="deliverynote_hourlyrate"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="deliverynoteitem_price[]"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="deliverynoteitem_product_hour[]"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="deliverynoteitem_quantity[]"]', function(){
				calc();
			});
		});

		function calc(){
			var total = 0;
			$.each($('table.list tbody tr'), function(key, val){
				if($(this).find('input[name="deliverynoteitem_product_type_name[]"]').val() == 'service'){
					$(this).find('input[name="deliverynoteitem_product_price[]"]').val($(this).find('input[name="deliverynoteitem_product_hour[]"]').val() * $('input[name="deliverynote_hourlyrate"]').val());
				}
				$(this).find('input[name="deliverynoteitem_subtotal[]"]').val($(this).find('input[name="deliverynoteitem_product_price[]"]').val() * $(this).find('input[name="deliverynoteitem_quantity[]"]').val()).css('display', 'none').fadeIn();
				total += parseInt($(this).find('input[name="deliverynoteitem_subtotal[]"]').val());
				$('input[name="deliverynote_total"]').val(total).css('display', 'none').fadeIn();
			});
		}

		function check_delete(id){
			var answer = prompt("Confirm delete?");
			if(answer){
				$('input[name="deliverynote_id"]').val(id);
				$('input[name="deliverynote_delete_reason"]').val(encodeURI(answer));
				$('form[name="list"]').submit();
			}else{
				return false;
			}
		}

		<?php if($this->router->fetch_method() == 'update' || $this->router->fetch_method() == 'insert'){ ?>
		function add_deliverynoteitem_row(){
			deliverynoteitem_row = '';
			deliverynoteitem_row += '<tr>';
			deliverynoteitem_row += '<td>';
			deliverynoteitem_row += '<div>';
			deliverynoteitem_row += '<input name="deliverynoteitem_deliverynote_id[]" type="hidden" value="" />';
			deliverynoteitem_row += '<input name="deliverynoteitem_product_type_name[]" type="hidden" value="" />';
			deliverynoteitem_row += '<input id="deliverynoteitem_product_code" name="deliverynoteitem_product_code[]" type="text" class="form-control input-sm" placeholder="Code" value="" />';
			deliverynoteitem_row += '</div>';
			deliverynoteitem_row += '<div class="margin-top-10">';
			deliverynoteitem_row += '<a class="btn btn-sm btn-primary deliverynoteitem-delete-btn" data-toggle="tooltip" title="Delete">';
			deliverynoteitem_row += '<i class="glyphicon glyphicon-remove"></i>';
			deliverynoteitem_row += '</a>';
			deliverynoteitem_row += '</div>';
			deliverynoteitem_row += '</td>';
			deliverynoteitem_row += '<td>';
			deliverynoteitem_row += '<div>';
			deliverynoteitem_row += '<select id="deliverynoteitem_product_id" name="deliverynoteitem_product_id[]" data-placeholder="Product" class="chosen-select">';
			deliverynoteitem_row += '<option value></option>';
			<?php foreach($products as $key1 => $value1){ ?>
			deliverynoteitem_row += '<option value="<?=$value1->product_id?>"><?=$value1->product_code.' - '.$value1->product_name?></option>';
			<?php } ?>
			deliverynoteitem_row += '</select>';
			deliverynoteitem_row += '</div>';
			deliverynoteitem_row += '<div>';
			deliverynoteitem_row += '<input id="deliverynoteitem_product_name" name="deliverynoteitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="" />';
			deliverynoteitem_row += '</div>';
			deliverynoteitem_row += '<div>';
			deliverynoteitem_row += '<textarea id="deliverynoteitem_product_detail" name="deliverynoteitem_product_detail[]" class="form-control input-sm" placeholder="Detail" rows="3"></textarea>';
			deliverynoteitem_row += '</div>';
			deliverynoteitem_row += '</td>';
			deliverynoteitem_row += '<td>';
			deliverynoteitem_row += '<input id="deliverynoteitem_product_price" name="deliverynoteitem_product_price[]" type="text" class="form-control input-sm" placeholder="Price" value="" />';
			deliverynoteitem_row += '<div class="input-group">';
			deliverynoteitem_row += '<input id="deliverynoteitem_product_hour" name="deliverynoteitem_product_hour[]" type="text" class="form-control input-sm" placeholder="Hour" value="" />';
			deliverynoteitem_row += '<span class="input-group-addon">hrs</span>';
			deliverynoteitem_row += '</div>';
			deliverynoteitem_row += '</td>';
			deliverynoteitem_row += '<td>';
			deliverynoteitem_row += '<input id="deliverynoteitem_quantity" name="deliverynoteitem_quantity[]" type="text" class="form-control input-sm" placeholder="Quantity" value="1" />';
			deliverynoteitem_row += '</td>';
			deliverynoteitem_row += '<td>';
			deliverynoteitem_row += '<input readonly="readonly" id="deliverynoteitem_subtotal" name="deliverynoteitem_subtotal[]" type="text" class="form-control input-sm" placeholder="Subtotal" value="" />';
			deliverynoteitem_row += '</td>';
			deliverynoteitem_row += '</tr>';

			$('table.list tbody').append(deliverynoteitem_row);
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

					<h2 class="col-sm-12"><a href="<?=base_url('deliverynote')?>">Delivery note management</a> > <?=($this->router->fetch_method() == 'update') ? 'Update' : 'Insert'?> delivery note</h2>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="deliverynote_id" value="" />
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
											<label for="deliverynote_currency">Currency</label>
											<select id="deliverynote_currency" name="deliverynote_currency" data-placeholder="Currency" class="chosen-select required">
												<option value></option>
												<?php
												if($deliverynote->deliverynote_currency == ''){
													$deliverynote->deliverynote_currency = 'rmb';
												}
												foreach($currencys as $key => $value){
													$selected = ($value->currency_name == $deliverynote->deliverynote_currency) ? ' selected="selected"' : "" ;
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
										<h4 class="corpcolor-font">Quotation</h4>
										<div class="row">
											<div class="col-sm-6 col-xs-6">
												<table class="table table-condensed table-borderless">
													<tr>
														<td colspan="2">
															<select id="deliverynote_client_id" name="deliverynote_client_id" data-placeholder="Client" class="chosen-select">
																<option value></option>
																<?php
																foreach($clients as $key1 => $value1){
																	$selected = ($value1->client_id == $deliverynote->deliverynote_client_id) ? ' selected="selected"' : "" ;
																	echo '<option value="'.$value1->client_id.'"'.$selected.'>'.$value1->client_firstname.' '.$value1->client_lastname.'</option>';
																}
																?>
															</select>
														</td>
													</tr>
													<tr>
														<td><label for="deliverynote_client_company_name">Client</label></td>
														<td><input id="deliverynote_client_company_name" name="deliverynote_client_company_name" type="text" class="form-control input-sm required" placeholder="Company/Domain/Client" value="<?=$deliverynote->deliverynote_client_company_name?>" /></td>
													</tr>
													<tr>
														<td><label for="deliverynote_client_company_address">Address</label></td>
														<td><textarea id="deliverynote_client_company_address" name="deliverynote_client_company_address" class="form-control input-sm" placeholder="Address" rows="3"><?=$deliverynote->deliverynote_client_company_address?></textarea></td>
													</tr>
													<tr>
														<td><label for="deliverynote_client_name">Attn.</label></td>
														<td><input id="deliverynote_client_name" name="deliverynote_client_name" type="text" class="form-control input-sm required" placeholder="Attn." value="<?=$deliverynote->deliverynote_client_name?>" /></td>
													</tr>
													<tr>
														<td><label for="deliverynote_client_phone">Phone</label></td>
														<td><input id="deliverynote_client_phone" name="deliverynote_client_phone" type="text" class="form-control input-sm" placeholder="Phone" value="<?=$deliverynote->deliverynote_client_phone?>" /></td>
													</tr>
													<tr>
														<td><label for="deliverynote_client_fax">Fax</label></td>
														<td><input id="deliverynote_client_fax" name="deliverynote_client_fax" type="text" class="form-control input-sm" placeholder="Fax" value="<?=$deliverynote->deliverynote_client_fax?>" /></td>
													</tr>
													<tr>
														<td><label for="deliverynote_client_email">Email</label></td>
														<td><input id="deliverynote_client_email" name="deliverynote_client_email" type="text" class="form-control input-sm" placeholder="Email" value="<?=$deliverynote->deliverynote_client_email?>" /></td>
													</tr>
												</table>
											</div>
											<div class="col-sm-1 col-xs-1">
											</div>
											<div class="col-sm-5 col-xs-5">
												<table class="table table-condensed table-borderless">
													<tr>
														<td><label for="deliverynote_number">Quotation#</label></td>
														<td>
															<div class="input-group">
																<input readonly="readonly" id="deliverynote_number" name="deliverynote_number" type="text" class="form-control input-sm" placeholder="Quotation#" value="<?=$deliverynote->deliverynote_number?>" />
																<span class="input-group-addon"><?='v'.$deliverynote->deliverynote_version?></span>
															</div>
														</td>
													</tr>
													<tr>
														<td><label for="deliverynote_issue">Issue date</label></td>
														<td><input id="deliverynote_issue" name="deliverynote_issue" type="text" class="form-control input-sm required" placeholder="Issue date" value="<?=($deliverynote->deliverynote_issue != '') ? $deliverynote->deliverynote_issue : date('Y-m-d')?>" /></td>
													</tr>
													<tr>
														<td><label for="deliverynote_user_name">Saleman</label></td>
														<td><input id="deliverynote_user_name" name="deliverynote_user_name" type="text" class="form-control input-sm required" placeholder="Saleman" value="<?=$user->user_name?>" /></td>
													</tr>
													<tr>
														<td><label for="deliverynote_user_phone">Phone</label></td>
														<td><input id="deliverynote_user_phone" name="deliverynote_user_phone" type="text" class="form-control input-sm required" placeholder="Phone" value="<?=$user->user_phone?>" /></td>
													</tr>
													<tr>
														<td><label for="deliverynote_user_fax">Fax</label></td>
														<td><input id="deliverynote_user_fax" name="deliverynote_user_fax" type="text" class="form-control input-sm" placeholder="Fax" value="<?=$user->user_fax?>" /></td>
													</tr>
													<tr>
														<td><label for="deliverynote_user_email">Email</label></td>
														<td><input id="deliverynote_user_email" name="deliverynote_user_email" type="text" class="form-control input-sm required" placeholder="Email" value="<?=$user->user_email?>" /></td>
													</tr>
												</table>
											</div>
										</div>
										<div class="list-area">
											<table class="table list" id="deliverynote">
												<thead>
													<tr>
														<th width="10%">
															<a class="btn btn-sm btn-primary deliverynoteitem-insert-btn" data-toggle="tooltip" title="Insert">
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
													<?php foreach($deliverynoteitems as $key => $value){ ?>
													<tr>
														<td>
															<div>
																<input name="deliverynoteitem_deliverynote_id[]" type="hidden" value="" />
																<input name="deliverynoteitem_product_type_name[]" type="hidden" value="<?=$value->deliverynoteitem_product_type_name?>" />
																<input id="deliverynoteitem_product_code" name="deliverynoteitem_product_code[]" type="text" class="form-control input-sm" placeholder="Code" value="<?=$value->deliverynoteitem_product_code?>" />
															</div>
															<div class="margin-top-10">
																<a class="btn btn-sm btn-primary deliverynoteitem-delete-btn" data-toggle="tooltip" title="Delete">
																	<i class="glyphicon glyphicon-remove"></i>
																</a>
															</div>
														</td>
														<td>
															<div>
																<select id="deliverynoteitem_product_id" name="deliverynoteitem_product_id[]" data-placeholder="Product" class="chosen-select">
																	<option value></option>
																	<?php
																	foreach($products as $key1 => $value1){
																		$selected = ($value1->product_id == $value->deliverynoteitem_product_id) ? ' selected="selected"' : "" ;
																		echo '<option value="'.$value1->product_id.'"'.$selected.'>'.$value1->product_name.'</option>';
																	}
																	?>
																</select>
															</div>
															<div>
																<input id="deliverynoteitem_product_name" name="deliverynoteitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="<?=$value->deliverynoteitem_product_name?>" />
															</div>
															<div>
																<textarea id="deliverynoteitem_product_detail" name="deliverynoteitem_product_detail[]" class="form-control input-sm" placeholder="Detail" rows="3"><?=$value->deliverynoteitem_product_detail?></textarea>
															</div>
														</td>
														<td>
															<input id="deliverynoteitem_product_price" name="deliverynoteitem_product_price[]" type="text" class="form-control input-sm" placeholder="Price" value="<?=$value->deliverynoteitem_product_price?>" />
															<div class="input-group">
																<input id="deliverynoteitem_product_hour" name="deliverynoteitem_product_hour[]" type="text" class="form-control input-sm" placeholder="Hour" value="<?=$value->deliverynoteitem_product_hour?>" />
																<span class="input-group-addon">hrs</span>
															</div>
														</td>
														<td>
															<input id="deliverynoteitem_quantity" name="deliverynoteitem_quantity[]" type="text" class="form-control input-sm" placeholder="Quantity" value="<?=($value->deliverynoteitem_quantity) ? $value->deliverynoteitem_quantity : '1'?>" />
														</td>
														<td>
															<input readonly="readonly" id="deliverynoteitem_subtotal" name="deliverynoteitem_subtotal[]" type="text" class="form-control input-sm" placeholder="Subtotal" value="<?=$value->deliverynoteitem_subtotal?>" />
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
														<th><input readonly="readonly" id="deliverynote_total" name="deliverynote_total" type="text" class="form-control input-sm" placeholder="Grand total" value="<?=($deliverynote->deliverynote_total) ? $deliverynote->deliverynote_total : '0'?>" /></th>
													</tr>
												</tfoot>
											</table>
										</div>
										<hr />
										<p class="form-group">
											<label for="deliverynote_remark">Remark</label>
											<textarea id="deliverynote_remark" name="deliverynote_remark" class="form-control input-sm" placeholder="Remark" rows="3"><?=$deliverynote->deliverynote_remark?></textarea>
										</p>
										<p class="form-group">
											<label for="deliverynote_warranty">Warranty</label>
											<textarea id="deliverynote_warranty" name="deliverynote_warranty" class="form-control input-sm" placeholder="Warranty" rows="3"><?=$deliverynote->deliverynote_warranty?></textarea>
										</p>
										<p class="form-group">
											<label for="deliverynote_delivery">Delivery</label>
											<textarea id="deliverynote_delivery" name="deliverynote_delivery" class="form-control input-sm" placeholder="Delivery" rows="3"><?=$deliverynote->deliverynote_delivery?></textarea>
										</p>
										<p class="form-group">
											<label for="deliverynote_payment">Payment</label>
											<textarea id="deliverynote_payment" name="deliverynote_payment" class="form-control input-sm" placeholder="Payment" rows="3"><?=$deliverynote->deliverynote_payment?></textarea>
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

					<h2 class="col-sm-12">Delivery note management</h2>
					<blockquote class="blue">
						<p>Can 單獨 add delivery note</p>
					</blockquote>

					<div class="content-column-area col-md-12 col-sm-12">

						<div class="fieldset">
							<div class="search-area">

								<form deliverynote="form" method="get">
									<input type="hidden" name="deliverynote_id" />
									<table>
										<tbody>
											<tr>
												<td width="90%">
													<div class="row">
														<div class="col-sm-2"><h6>Delivery Note</h6></div>
														<div class="col-sm-2">
															<input type="text" name="deliverynote_client_company_name_deliverynote_client_name_like" class="form-control input-sm" placeholder="DNNo From" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="deliverynote_client_company_name_deliverynote_client_name_like" class="form-control input-sm" placeholder="DNNo To" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="deliverynote_client_company_name_deliverynote_client_name_like" class="form-control input-sm date-mask" placeholder="Date From (YYYY-MM-DD)" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="deliverynote_client_company_name_deliverynote_client_name_like" class="form-control input-sm date-mask" placeholder="Date To (YYYY-MM-DD)" value="" />
														</div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
													</div>
													<div class="row">
														<div class="col-sm-2"></div>
														<div class="col-sm-2">
															<input type="text" name="deliverynote_client_company_name_deliverynote_client_name_like" class="form-control input-sm" placeholder="SONo From" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="deliverynote_client_company_name_deliverynote_client_name_like" class="form-control input-sm" placeholder="SONo To" value="" />
														</div>
														<div class="col-sm-2">
															<select id="deliverynote_client_company_name_deliverynote_client_name_like" name="deliverynote_client_company_name_deliverynote_client_name_like" data-placeholder="Status" class="chosen-select">
																<option value></option>
															</select>
														</div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
													</div>
													<div class="row">
														<div class="col-sm-2"><h6>Customer</h6></div>
														<div class="col-sm-2">
															<input type="text" name="deliverynote_client_company_name_deliverynote_client_name_like" class="form-control input-sm" placeholder="Customer Name" value="" />
														</div>
														<div class="col-sm-2">
															<select id="deliverynote_client_company_name_deliverynote_client_name_like" name="deliverynote_client_company_name_deliverynote_client_name_like" data-placeholder="Sales" class="chosen-select">
																<option value></option>
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
															<input type="text" name="deliverynote_client_company_name_deliverynote_client_name_like" class="form-control input-sm" placeholder="Project Name" value="" />
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
															<input type="text" name="deliverynote_client_company_name_deliverynote_client_name_like" class="form-control input-sm" placeholder="Item Code" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="deliverynote_client_company_name_deliverynote_client_name_like" class="form-control input-sm" placeholder="Item Description" value="" />
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
								<form name="list" action="<?=base_url('deliverynote/delete')?>" method="post">
									<input type="hidden" name="deliverynote_id" />
									<input type="hidden" name="deliverynote_delete_reason" />
									<div class="page-area">
										<span class="btn btn-sm btn-default"><?php print_r($num_rows); ?></span>
										<?=$this->pagination->create_links()?>
									</div>
									<table class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>DN No</th>
												<th>SO No</th>
												<th>Create</th>
												<th>Customer</th>
												<th>Project</th>
												<th>Sales</th>
												<th>Delivery Date</th>
												<th>Currency</th>
												<th>Total</th>
												<th class="text-right">
													<a href="<?=base_url('deliverynote/insert')?>" data-toggle="tooltip" title="Insert">
														<i class="glyphicon glyphicon-plus"></i>
													</a>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php for($i=0; $i<12; $i++){ ?>
											<tr>
												<td><a href="<?=base_url('deliverynote/update')?>">DN1612074-1</a></td>
												<td><a href="<?=base_url('salesorder/update')?>">SO1612074-1</a></td>
												<td>2016-12-30</td>
												<td>Philips Electronics HK Ltd - Philips Healthcare</td>
												<td>Week 1651</td>
												<td>Danial Lo</td>
												<td>2016-12-30</td>
												<td>HKD</td>
												<td>33,000</td>
												<td class="text-right">
													<a href="<?=base_url('deliverynote/update')?>" data-toggle="tooltip" title="Remove">
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












































		<?php $this->load->view('inc/footer-area.php'); ?>

	</body>
</html>

<div class="scriptLoader"></div>