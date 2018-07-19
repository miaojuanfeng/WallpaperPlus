<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Proforma invoice management</title>

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

			$('input[name="proformainvoice_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');

			/*--------- date mask ---------*/
			$('.date-mask').mask('9999-99-99');

			/*--------- datetimepicker ---------*/
			$('.datetimepicker').datetimepicker({
				format: 'Y-MM-DD'
			});

			/* proformainvoiceitem-insert-btn */
			$(document).on('click', '.proformainvoiceitem-insert-btn', function(){
				add_proformainvoiceitem_row();
			});

			/* proformainvoiceitem-delete-btn */
			$(document).on('click', '.proformainvoiceitem-delete-btn', function(){
				if(confirm('Confirm delete?')){
					$(this).closest('tr').remove();
					calc();
				}else{
					return false;
				}
			});

			/* product loader */
			$(document).on('change', 'select[name="proformainvoiceitem_product_id[]"]', function(){
				product_loader($(this));
			});

			/* trigger calc */
			$(document).on('blur', 'input[name="proformainvoiceitem_product_price[]"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="proformainvoiceitem_quantity[]"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="proformainvoice_discount"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="proformainvoice_pay"]', function(){
				calc();
			});
			$(document).on('change', 'input[name="proformainvoice_currency"]', function(){
				$.each($('select[name="proformainvoiceitem_product_id[]"]'), function(key, val){
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
				$(this).find('input[name="proformainvoiceitem_subtotal[]"]').val(parseFloat($(this).find('input[name="proformainvoiceitem_product_price[]"]').val() * $(this).find('input[name="proformainvoiceitem_quantity[]"]').val()).toFixed(2)).css('display', 'none').fadeIn();
				total += parseFloat($(this).find('input[name="proformainvoiceitem_subtotal[]"]').val());
			});
			$('input[name="proformainvoice_total"]').val(parseFloat(total - $('input[name="proformainvoice_discount"]').val()).toFixed(2)).css('display', 'none').fadeIn();
			$('input[name="proformainvoice_balance"]').val(parseFloat(total - $('input[name="proformainvoice_discount"]').val() - $('input[name="proformainvoice_paid"]').val() - $('input[name="proformainvoice_pay"]').val()).toFixed(2)).css('display', 'none').fadeIn();
		}

		function check_delete(id){
			var answer = prompt("Confirm delete?");
			if(answer){
				$('input[name="proformainvoice_id"]').val(id);
				$('input[name="proformainvoice_delete_reason"]').val(encodeURI(answer));
				$('form[name="list"]').submit();
			}else{
				return false;
			}
		}

		function product_loader(thisObject){
			thisRow = $(thisObject).closest('tr').index();
			thisCurrency = $('input[name="proformainvoice_currency"]').val();
			$('.scriptLoader').load('/load', {'thisTableId': 'proformainvoiceProductLoader', 'thisRecordId': $(thisObject).val(), 'thisCurrency': thisCurrency, 'thisRow': thisRow, 't': timestamp()}, function(){
				proformainvoiceProductLoader();
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
		function add_proformainvoiceitem_row(){
			proformainvoiceitem_row = '';
			proformainvoiceitem_row += '<tr>';
			proformainvoiceitem_row += '<td>';
			proformainvoiceitem_row += '<div>';
			proformainvoiceitem_row += '<input name="proformainvoiceitem_id[]" type="hidden" value="" />';
			proformainvoiceitem_row += '<input name="proformainvoiceitem_proformainvoice_id[]" type="hidden" value="" />';
			proformainvoiceitem_row += '<input name="proformainvoiceitem_product_type_name[]" type="hidden" value="" />';
			proformainvoiceitem_row += '<input id="proformainvoiceitem_product_code" name="proformainvoiceitem_product_code[]" type="text" class="form-control input-sm" placeholder="Code" value="" />';
			proformainvoiceitem_row += '</div>';
			proformainvoiceitem_row += '<div class="margin-top-10">';
			proformainvoiceitem_row += '<div class="btn-group">';
			proformainvoiceitem_row += '<button type="button" class="btn btn-sm btn-primary proformainvoiceitem-delete-btn"><i class="glyphicon glyphicon-remove"></i></button>';
			proformainvoiceitem_row += '<button type="button" class="btn btn-sm btn-primary up-btn"><i class="glyphicon glyphicon-chevron-up"></i></button>';
			proformainvoiceitem_row += '<button type="button" class="btn btn-sm btn-primary down-btn"><i class="glyphicon glyphicon-chevron-down"></i></button>';
			proformainvoiceitem_row += '</div>';
			proformainvoiceitem_row += '</div>';
			proformainvoiceitem_row += '</td>';
			proformainvoiceitem_row += '<td>';
			proformainvoiceitem_row += '<div>';
			proformainvoiceitem_row += '<select id="proformainvoiceitem_product_id" name="proformainvoiceitem_product_id[]" data-placeholder="Product" class="chosen-select">';
			proformainvoiceitem_row += '<option value></option>';
			<?php foreach($products as $key1 => $value1){ ?>
			proformainvoiceitem_row += '<option value="<?=$value1->product_id?>"><?=$value1->product_code.' - '.$value1->product_name?></option>';
			<?php } ?>
			proformainvoiceitem_row += '</select>';
			proformainvoiceitem_row += '</div>';
			proformainvoiceitem_row += '<div class="margin-top-10">';
			// proformainvoiceitem_row += '<input id="proformainvoiceitem_product_name" name="proformainvoiceitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="" />';
			proformainvoiceitem_row += '<div class="input-group">';
			proformainvoiceitem_row += '<span class="input-group-addon corpcolor-font">Title</span>';
			proformainvoiceitem_row += '<input id="proformainvoiceitem_product_name" name="proformainvoiceitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="" />';
			proformainvoiceitem_row += '</div>';
			proformainvoiceitem_row += '</div>';
			proformainvoiceitem_row += '<div>';
			proformainvoiceitem_row += '<textarea id="proformainvoiceitem_product_detail" name="proformainvoiceitem_product_detail[]" class="form-control input-sm" placeholder="Detail"></textarea>';
			proformainvoiceitem_row += '</div>';
			proformainvoiceitem_row += '</td>';
			proformainvoiceitem_row += '<td>';
			proformainvoiceitem_row += '<input id="proformainvoiceitem_product_price" name="proformainvoiceitem_product_price[]" type="text" class="form-control input-sm" placeholder="Price" value="" />';
			proformainvoiceitem_row += '</td>';
			proformainvoiceitem_row += '<td>';
			proformainvoiceitem_row += '<input id="proformainvoiceitem_quantity" name="proformainvoiceitem_quantity[]" type="text" class="form-control input-sm" placeholder="Quantity" value="1" />';
			proformainvoiceitem_row += '</td>';
			proformainvoiceitem_row += '<td>';
			proformainvoiceitem_row += '<input readonly="readonly" id="proformainvoiceitem_subtotal" name="proformainvoiceitem_subtotal[]" type="text" class="form-control input-sm" placeholder="Subtotal" value="" />';
			proformainvoiceitem_row += '</td>';
			proformainvoiceitem_row += '</tr>';

			$('table.list tbody').append(proformainvoiceitem_row);
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

					<h2 class="col-sm-12"><a href="<?=base_url('proformainvoice')?>">Proforma invoice management</a> > <?=($this->router->fetch_method() == 'update') ? 'Update' : 'Insert'?> proformainvoice</h2>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="proformainvoice_id" value="<?=$proformainvoice->proformainvoice_id?>" />
							<input type="hidden" name="proformainvoice_quotation_user_id" value="<?=$proformainvoice->proformainvoice_quotation_user_id?>" />
							<input type="hidden" name="proformainvoice_salesorder_id" value="<?=$proformainvoice->proformainvoice_salesorder_id?>" />
							<input type="hidden" name="proformainvoice_client_id" value="<?=$proformainvoice->proformainvoice_client_id?>" />
							<input type="hidden" name="proformainvoice_project_name" value="<?=$proformainvoice->proformainvoice_project_name?>" />
							<input type="hidden" name="proformainvoice_currency" value="<?=$proformainvoice->proformainvoice_currency?>" />
							<input type="hidden" name="proformainvoice_client_company_name" value="<?=$proformainvoice->proformainvoice_client_company_name?>" />
							<input type="hidden" name="proformainvoice_client_company_address" value="<?=$proformainvoice->proformainvoice_client_company_address?>" />
							<input type="hidden" name="proformainvoice_client_company_phone" value="<?=$proformainvoice->proformainvoice_client_company_phone?>" />
							<input type="hidden" name="proformainvoice_client_phone" value="<?=$proformainvoice->proformainvoice_client_phone?>" />
							<input type="hidden" name="proformainvoice_client_name" value="<?=$proformainvoice->proformainvoice_client_name?>" />
							<input type="hidden" name="proformainvoice_issue" value="<?=$proformainvoice->proformainvoice_issue?>" />
							<input type="hidden" name="proformainvoice_terms" value="<?=$proformainvoice->proformainvoice_terms?>" />
							<input type="hidden" name="proformainvoice_expire" value="<?=$proformainvoice->proformainvoice_expire?>" />
							<input type="hidden" name="salesorder_total" value="<?=$proformainvoice->proformainvoice_total?>" />
							<input type="hidden" name="proformainvoice_user_id" value="<?=$proformainvoice->proformainvoice_user_id?>" />
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
											<a class="btn btn-sm btn-primary btn-block" target="_blank" href="<?=base_url('assets/images/pdf/proformainvoice/'.$proformainvoice->proformainvoice_number.'.pdf?'.time())?>" data-toggle="tooltip" title="Print"><i class="glyphicon glyphicon-print"></i> Print</a>
										</p>
										<?php } ?>
										<h4 class="corpcolor-font">Setting</h4>
										<p class="form-group">
											<label for="proformainvoice_status">Status</label>
											<select id="proformainvoice_status" name="proformainvoice_status" data-placeholder="Status" class="chosen-select required">
												<option value></option>
												<?php
												if($proformainvoice->proformainvoice_status == ''){
													$proformainvoice->proformainvoice_status = 'hkd';
												}
												foreach($statuss as $key => $value){
													$selected = ($value->status_name == $proformainvoice->proformainvoice_status) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value->status_name.'"'.$selected.'>'.strtoupper($value->status_name).'</option>';
												}
												?>
											</select>
										</p>
									</div>
									<div class="col-sm-9 col-xs-12">
										<h4 class="corpcolor-font">Proforma invoice</h4>
										<div class="row">
											<div class="col-sm-6 col-xs-6">
												<table class="table table-condensed table-borderless">
													<tr>
														<td><label for="proformainvoice_client_company_name">To</label></td>
														<td><input id="proformainvoice_client_company_name" name="proformainvoice_client_company_name" type="text" class="form-control input-sm required" placeholder="Company/Domain/Client" value="<?=$proformainvoice->proformainvoice_client_company_name?>" /></td>
													</tr>
													<tr>
														<td><label for="proformainvoice_client_company_address">Address</label></td>
														<td><textarea id="proformainvoice_client_company_address" name="proformainvoice_client_company_address" class="form-control input-sm" placeholder="Address"><?=$proformainvoice->proformainvoice_client_company_address?></textarea></td>
													</tr>
													<tr>
														<td><label for="proformainvoice_client_company_phone">Phone</label></td>
														<td><input id="proformainvoice_client_company_phone" name="proformainvoice_client_company_phone" type="text" class="form-control input-sm" placeholder="Phone" value="<?=$proformainvoice->proformainvoice_client_company_phone?>" /></td>
													</tr>
													<tr>
														<td><label for="proformainvoice_client_phone">Mobile</label></td>
														<td><input id="proformainvoice_client_phone" name="proformainvoice_client_phone" type="text" class="form-control input-sm" placeholder="Fax" value="<?=$proformainvoice->proformainvoice_client_phone?>" /></td>
													</tr>
													<tr>
														<td><label for="proformainvoice_client_email">Email</label></td>
														<td><input id="proformainvoice_client_email" name="proformainvoice_client_email" type="text" class="form-control input-sm" placeholder="Email" value="<?=$proformainvoice->proformainvoice_client_email?>" /></td>
													</tr>
													<tr>
														<td><label for="proformainvoice_client_name">Attn</label></td>
														<td><input id="proformainvoice_client_name" name="proformainvoice_client_name" type="text" class="form-control input-sm required" placeholder="Attn." value="<?=$proformainvoice->proformainvoice_client_name?>" /></td>
													</tr>
												</table>
											</div>
											<div class="col-sm-1 col-xs-1">
											</div>
											<div class="col-sm-5 col-xs-5">
												<table class="table table-condensed table-borderless">
													<tr>
														<td><label for="proformainvoice_number">Proforma invoice#</label></td>
														<td>
															<div class="input-group">
																<input readonly="readonly" id="proformainvoice_number" name="proformainvoice_number" type="text" class="form-control input-sm" placeholder="Proforma invoice#" value="<?=$proformainvoice->proformainvoice_number?>" />
																<span class="input-group-addon"><?='v'.$proformainvoice->proformainvoice_version?></span>
															</div>
														</td>
													</tr>
													<tr>
														<td><label for="proformainvoice_issue">Date</label></td>
														<td><input id="proformainvoice_issue" name="proformainvoice_issue" type="text" class="form-control input-sm date-mask required" placeholder="Issue date" value="<?=($proformainvoice->proformainvoice_issue != '') ? $proformainvoice->proformainvoice_issue : date('Y-m-d')?>" /></td>
													</tr>
													<tr>
														<td><label for="proformainvoice_user_name">Sales</label></td>
														<td><input readonly="readonly" id="proformainvoice_user_name" name="proformainvoice_user_name" type="text" class="form-control input-sm required" placeholder="Saleman" value="<?=$user->user_name?>" /></td>
													</tr>
													<tr>
														<td><label for="proformainvoice_terms">Payment terms</label></td>
														<td><input id="proformainvoice_terms" name="proformainvoice_terms" type="text" class="form-control input-sm required" placeholder="Payment terms" value="<?=$proformainvoice->proformainvoice_terms?>" /></td>
													</tr>
													<tr>
														<td><label for="proformainvoice_expire">Expire Date</label></td>
														<td><input id="proformainvoice_expire" name="proformainvoice_expire" type="text" class="form-control input-sm date-mask" placeholder="Expire Date" value="<?=($proformainvoice->proformainvoice_expire != '' && $this->router->fetch_method() != 'duplicate') ? $proformainvoice->proformainvoice_expire : date('Y-m-d', strtotime('+14 days', time()))?>" /></td>
													</tr>
												</table>
											</div>
										</div>
										<div class="list-area">
											<table class="table list" id="proformainvoice">
												<thead>
													<tr>
														<th width="10%">
															<a class="btn btn-sm btn-primary proformainvoiceitem-insert-btn" data-toggle="tooltip" title="Insert">
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
													<?php foreach($proformainvoiceitems as $key => $value){ ?>
													<tr>
														<td>
															<div>
																<input name="proformainvoiceitem_id[]" type="hidden" value="<?=$value->proformainvoiceitem_id?>" />
																<input name="proformainvoiceitem_proformainvoice_id[]" type="hidden" value="<?=$value->proformainvoiceitem_proformainvoice_id?>" />
																<input name="proformainvoiceitem_product_type_name[]" type="hidden" value="<?=$value->proformainvoiceitem_product_type_name?>" />
																<input id="proformainvoiceitem_product_code" name="proformainvoiceitem_product_code[]" type="text" class="form-control input-sm" placeholder="Code" value="<?=$value->proformainvoiceitem_product_code?>" />
															</div>
															<div class="margin-top-10">
																<div class="btn-group">
																	<button type="button" class="btn btn-sm btn-primary proformainvoiceitem-delete-btn"><i class="glyphicon glyphicon-remove"></i></button>
																	<button type="button" class="btn btn-sm btn-primary up-btn"><i class="glyphicon glyphicon-chevron-up"></i></button>
																	<button type="button" class="btn btn-sm btn-primary down-btn"><i class="glyphicon glyphicon-chevron-down"></i></button>
																</div>
															</div>
														</td>
														<td>
															<div>
																<select id="proformainvoiceitem_product_id" name="proformainvoiceitem_product_id[]" data-placeholder="Product" class="chosen-select">
																	<option value></option>
																	<?php
																	foreach($products as $key1 => $value1){
																		$selected = ($value1->product_id == $value->proformainvoiceitem_product_id) ? ' selected="selected"' : "" ;
																		echo '<option value="'.$value1->product_id.'"'.$selected.'>'.$value1->product_code.' - '.$value1->product_name.'</option>';
																	}
																	?>
																</select>
															</div>
															<div class="margin-top-10">
																<div class="input-group">
																	<span class="input-group-addon corpcolor-font">Title</span>
																	<input id="proformainvoiceitem_product_name" name="proformainvoiceitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="<?=$value->proformainvoiceitem_product_name?>" />
																</div>
															</div>
															<div>
																<textarea id="proformainvoiceitem_product_detail" name="proformainvoiceitem_product_detail[]" class="form-control input-sm" placeholder="Detail"><?=$value->proformainvoiceitem_product_detail?></textarea>
															</div>
														</td>
														<td>
															<input id="proformainvoiceitem_product_price" name="proformainvoiceitem_product_price[]" type="text" class="form-control input-sm" placeholder="Price" value="<?=$value->proformainvoiceitem_product_price?>" />
														</td>
														<td>
															<input id="proformainvoiceitem_quantity" name="proformainvoiceitem_quantity[]" type="text" class="form-control input-sm" placeholder="Quantity" value="<?=($value->proformainvoiceitem_quantity) ? $value->proformainvoiceitem_quantity : '1'?>" />
														</td>
														<td>
															<input readonly="readonly" id="proformainvoiceitem_subtotal" name="proformainvoiceitem_subtotal[]" type="text" class="form-control input-sm" placeholder="Subtotal" value="<?=$value->proformainvoiceitem_subtotal?>" />
														</td>
													</tr>
													<?php } ?>
												</tbody>
												<tfoot>
													<tr>
														<th></th>
														<th></th>
														<th></th>
														<th>Discount</th>
														<th><input readonly="readonly" id="proformainvoice_discount" name="proformainvoice_discount" type="text" class="form-control input-sm required" placeholder="Discount" value="<?=($proformainvoice->proformainvoice_discount) ? $proformainvoice->proformainvoice_discount : '0'?>" /></th>
													</tr>
													<tr>
														<th></th>
														<th></th>
														<th></th>
														<th>Grand total</th>
														<th><input readonly="readonly" id="proformainvoice_total" name="proformainvoice_total" type="text" class="form-control input-sm" placeholder="Grand total" value="<?=($proformainvoice->proformainvoice_total) ? $proformainvoice->proformainvoice_total : '0'?>" /></th>
													</tr>
													<tr>
														<th></th>
														<th></th>
														<th></th>
														<th>Paid</th>
														<th><input readonly="readonly" id="proformainvoice_paid" name="proformainvoice_paid" type="text" class="form-control input-sm" placeholder="Paid" value="<?=($proformainvoice->proformainvoice_paid) ? $proformainvoice->proformainvoice_paid : '0'?>" /></th>
													</tr>
													<tr>
														<th></th>
														<th></th>
														<th></th>
														<th>Pay</th>
														<th><input id="proformainvoice_pay" name="proformainvoice_pay" type="text" class="form-control input-sm" placeholder="Pay" value="<?=($proformainvoice->proformainvoice_pay) ? $proformainvoice->proformainvoice_pay : '0'?>" /></th>
													</tr>
													<tr>
														<th width="10%">
															<a class="btn btn-sm btn-primary proformainvoiceitem-insert-btn" data-toggle="tooltip" title="Insert">
																<i class="glyphicon glyphicon-plus"></i>
															</a>
														</th>
														<th></th>
														<th></th>
														<th>Balance</th>
														<th><input readonly="readonly" id="proformainvoice_balance" name="proformainvoice_balance" type="text" class="form-control input-sm" placeholder="Balance" value="<?=($proformainvoice->proformainvoice_balance) ? $proformainvoice->proformainvoice_balance : '0'?>" /></th>
													</tr>
												</tfoot>
											</table>
										</div>
										<hr />
										<p class="form-group">
											<label for="proformainvoice_remark">Remark</label>
											<textarea id="proformainvoice_remark" name="proformainvoice_remark" class="form-control input-sm" placeholder="Remark" rows="3"><?=$proformainvoice->proformainvoice_remark?></textarea>
										</p>
										<p class="form-group">
											<label for="proformainvoice_payment">Payment</label>
											<textarea id="proformainvoice_payment" name="proformainvoice_payment" class="form-control input-sm" placeholder="Payment" rows="3"><?=$proformainvoice->proformainvoice_payment?></textarea>
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

					<h2 class="col-sm-12">Proforma invoice management</h2>

					<div class="content-column-area col-md-12 col-sm-12">

						<div class="fieldset">
							<?=$this->session->tempdata('alert');?>
							<div class="search-area">

								<form proformainvoice="form" method="get">
									<input type="hidden" name="proformainvoice_id" />
									<table>
										<tbody>
											<tr>
												<td width="90%">
													<div class="row">
														<div class="col-sm-2"><h6>Proforma invoice</h6></div>
														<div class="col-sm-2">
															<input type="text" name="proformainvoice_number_like" class="form-control input-sm" placeholder="PINo" value="" />
														</div>
														<div class="col-sm-2">
															<span class="input-group date datetimepicker">
																<input id="proformainvoice_create_greateq" name="proformainvoice_create_greateq" type="text" class="form-control input-sm date-mask" placeholder="Date From (YYYY-MM-DD)" />
																<span class="input-group-addon">
																	<span class="glyphicon glyphicon-calendar"></span>
																</span>
															</span>
														</div>
														<div class="col-sm-2">
															<span class="input-group date datetimepicker">
																<input id="proformainvoice_create_smalleq" name="proformainvoice_create_smalleq" type="text" class="form-control input-sm date-mask" placeholder="Date To (YYYY-MM-DD)" />
																<span class="input-group-addon">
																	<span class="glyphicon glyphicon-calendar"></span>
																</span>
															</span>
														</div>
														<div class="col-sm-2">
															<input type="text" name="salesorder_number_like" class="form-control input-sm" placeholder="SONo" value="" />
														</div>
														<div class="col-sm-2">
															<select id="proformainvoice_status" name="proformainvoice_status" data-placeholder="Status" class="chosen-select">
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
															<input type="text" name="proformainvoice_client_company_name_like" class="form-control input-sm" placeholder="Customer company name" value="" />
														</div>
														<div class="col-sm-2">
															<select id="proformainvoice_user_id" name="proformainvoice_user_id" data-placeholder="Sales" class="chosen-select">
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
															<input type="text" name="proformainvoice_project_name_like" class="form-control input-sm" placeholder="Project Name" value="" />
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
								<form name="list" action="<?=base_url('proformainvoice/delete')?>" method="post">
									<input type="hidden" name="proformainvoice_id" />
									<input type="hidden" name="proformainvoice_delete_reason" />
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
											<?php foreach($proformainvoices as $key => $value){ ?>
											<tr>
												<td><?=$value->proformainvoice_number?></td>
												<td><a href="<?=base_url('salesorder/update/salesorder_id/'.$value->proformainvoice_salesorder_id)?>"><?=get_salesorder($value->proformainvoice_salesorder_id)->salesorder_number?></a></td>
												<td><?=convert_datetime_to_date($value->proformainvoice_create)?></td>
												<td><?=$value->proformainvoice_client_company_name?></td>
												<td><?=$value->proformainvoice_project_name?></td>
												<td><?=ucfirst(get_user($value->proformainvoice_user_id)->user_name)?></td>
												<td><?=$value->proformainvoice_expire?></td>
												<td><?=ucfirst($value->proformainvoice_status)?></td>
												<td><?=strtoupper($value->proformainvoice_currency).' '.money_format('%!n', $value->proformainvoice_total)?></td>
												<td class="text-right">
													<a target="_blank" href="<?=base_url('/assets/images/pdf/proformainvoice/'.$value->proformainvoice_number.'.pdf')?>" data-toggle="tooltip" title="Print">
														<i class="glyphicon glyphicon-print"></i>
													</a>
												</td>
												<td class="text-right">
													<?php if(!check_permission('proformainvoice_update', 'display')){ ?>
													<a href="<?=base_url('proformainvoice/update/proformainvoice_id/'.$value->proformainvoice_id)?>" data-toggle="tooltip" title="Update">
														<i class="glyphicon glyphicon-edit"></i>
													</a>
													<?php }else{ ?>
													<i class="glyphicon glyphicon-edit"></i>
													<?php } ?>
												</td>
												<td class="text-right">
													<?php if(!check_permission('proformainvoice_delete', 'display')){ ?>
													<a onclick="check_delete(<?=$value->proformainvoice_id?>);" data-toggle="tooltip" title="Remove">
														<i class="glyphicon glyphicon-remove"></i>
													</a>
													<?php }else{ ?>
													<i class="glyphicon glyphicon-remove"></i>
													<?php } ?>
												</td>
											</tr>
											<?php } ?>

											<?php if(!$proformainvoices){ ?>
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
					<!-- <div class="blue">
						<p>test</p>
					</div> -->
				</div>
			</div>

		</div>
		<?php } ?>












































		<?php $this->load->view('inc/footer-area.php'); ?>

	</body>
</html>

<div class="scriptLoader"></div>