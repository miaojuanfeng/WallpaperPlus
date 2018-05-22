<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Sales order management</title>

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
			$('input[name="salesorder_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');

			/*--------- date mask ---------*/
			$('.date-mask').mask('9999-99-99');

			/*--------- datetimepicker ---------*/
			$('.datetimepicker').datetimepicker({
				format: 'Y-MM-DD'
			});

			/* salesorderitem-insert-btn */
			$(document).on('click', '.salesorderitem-insert-btn', function(){
				add_salesorderitem_row();
			});

			/* salesorderitem-delete-btn */
			$(document).on('click', '.salesorderitem-delete-btn', function(){
				if(confirm('Confirm delete?')){
					$(this).closest('tr').remove();
					calc();
				}else{
					return false;
				}
			});

			/* product loader */
			$(document).on('change', 'select[name="salesorderitem_product_id[]"]', function(){
				product_loader($(this));
			});

			/* trigger calc */
			$(document).on('blur', 'input[name="salesorderitem_product_price[]"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="salesorderitem_quantity[]"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="salesorder_discount"]', function(){
				calc();
			});
			$(document).on('change', 'input[name="salesorder_currency"]', function(){
				$.each($('select[name="salesorderitem_product_id[]"]'), function(key, val){
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
				$(this).find('input[name="salesorderitem_subtotal[]"]').val(parseFloat($(this).find('input[name="salesorderitem_product_price[]"]').val() * $(this).find('input[name="salesorderitem_quantity[]"]').val()).toFixed(2)).css('display', 'none').fadeIn();
				total += parseFloat($(this).find('input[name="salesorderitem_subtotal[]"]').val());
			});
			$('input[name="salesorder_total"]').val(parseFloat(total - $('input[name="salesorder_discount"]').val()).toFixed(2)).css('display', 'none').fadeIn();
			
			/* grand total checker */
			if(parseFloat($('input[name="salesorder_total"]').val()) != parseFloat($('input[name="quotation_total"]').val())){
				alert('Grand total should be equal to quotation, please check carefully.');
			}
		}

		function check_delete(id){
			var answer = prompt("Confirm delete?");
			if(answer){
				$('input[name="salesorder_id"]').val(id);
				$('input[name="salesorder_delete_reason"]').val(encodeURI(answer));
				$('form[name="list"]').submit();
			}else{
				return false;
			}
		}

		function product_loader(thisObject){
			thisRow = $(thisObject).closest('tr').index();
			thisCurrency = $('input[name="salesorder_currency"]').val();
			$('.scriptLoader').load('/load', {'thisTableId': 'salesorderProductLoader', 'thisRecordId': $(thisObject).val(), 'thisCurrency': thisCurrency, 'thisRow': thisRow, 't': timestamp()}, function(){
				salesorderProductLoader();
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
		function add_salesorderitem_row(){
			salesorderitem_row = '';
			salesorderitem_row += '<tr>';
			salesorderitem_row += '<td>';
			salesorderitem_row += '<div>';
			salesorderitem_row += '<input name="salesorderitem_id[]" type="hidden" value="" />';
			salesorderitem_row += '<input name="salesorderitem_salesorder_id[]" type="hidden" value="" />';
			salesorderitem_row += '<input name="salesorderitem_product_type_name[]" type="hidden" value="" />';
			salesorderitem_row += '<input id="salesorderitem_product_code" name="salesorderitem_product_code[]" type="text" class="form-control input-sm required" placeholder="Code" value="" />';
			salesorderitem_row += '</div>';
			salesorderitem_row += '<div class="margin-top-10">';
			salesorderitem_row += '<div class="btn-group">';
			salesorderitem_row += '<button type="button" class="btn btn-sm btn-primary salesorderitem-delete-btn"><i class="glyphicon glyphicon-remove"></i></button>';
			salesorderitem_row += '<button type="button" class="btn btn-sm btn-primary up-btn"><i class="glyphicon glyphicon-chevron-up"></i></button>';
			salesorderitem_row += '<button type="button" class="btn btn-sm btn-primary down-btn"><i class="glyphicon glyphicon-chevron-down"></i></button>';
			salesorderitem_row += '</div>';
			salesorderitem_row += '</div>';
			salesorderitem_row += '</td>';
			salesorderitem_row += '<td>';
            salesorderitem_row += '<div>';
            salesorderitem_row += '<input id="quotationitem_product_id" name="quotationitem_product_id[]" type="hidden" class="form-control input-sm" placeholder="Product" value="" />';
            salesorderitem_row += '<input type="button" class="form-control input-sm showModal" value="Select a product" />';
            salesorderitem_row += '</div>';
			salesorderitem_row += '<div class="margin-top-10">';
			// salesorderitem_row += '<input id="salesorderitem_product_name" name="salesorderitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="" />';
			salesorderitem_row += '<div class="input-group">';
			salesorderitem_row += '<span class="input-group-addon corpcolor-font">Title</span>';
			salesorderitem_row += '<input id="salesorderitem_product_name" name="salesorderitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="" />';
			salesorderitem_row += '</div>';
			salesorderitem_row += '</div>';
			salesorderitem_row += '<div>';
			salesorderitem_row += '<textarea id="salesorderitem_product_detail" name="salesorderitem_product_detail[]" class="form-control input-sm" placeholder="Detail"></textarea>';
			salesorderitem_row += '</div>';
			salesorderitem_row += '</td>';
			salesorderitem_row += '<td>';
			salesorderitem_row += '<input id="salesorderitem_product_price" name="salesorderitem_product_price[]" type="number" min="0" class="form-control input-sm" placeholder="Price" value="" />';
			salesorderitem_row += '</td>';
			salesorderitem_row += '<td>';
            salesorderitem_row += '<div class="input-group">';
            salesorderitem_row += '<input id="salesorderitem_quantity" name="salesorderitem_quantity[]" type="number" min="0" class="form-control input-sm" placeholder="Quantity" value="1" />';
            salesorderitem_row += '<span class="input-group-addon">Unit</span>';
            salesorderitem_row += '</div>';
			salesorderitem_row += '</td>';
			salesorderitem_row += '<td>';
			salesorderitem_row += '<input readonly="readonly" id="salesorderitem_subtotal" name="salesorderitem_subtotal[]" type="text" class="form-control input-sm" placeholder="Subtotal" value="" />';
			salesorderitem_row += '</td>';
			salesorderitem_row += '</tr>';

			$('table.list tbody').append(salesorderitem_row);
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

					<h2 class="col-sm-12"><a href="<?=base_url('salesorder')?>">Sales order management</a> > <?=($this->router->fetch_method() == 'update') ? 'Update' : 'Insert'?> sales order</h2>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="salesorder_id" value="<?=$salesorder->salesorder_id?>" />
							<input type="hidden" name="salesorder_quotation_id" value="<?=$salesorder->salesorder_quotation_id?>" />
							<input type="hidden" name="salesorder_quotation_user_id" value="<?=$salesorder->salesorder_quotation_user_id?>" />
							<input type="hidden" name="salesorder_client_id" value="<?=$salesorder->salesorder_client_id?>" />
							<input type="hidden" name="salesorder_project_name" value="<?=$salesorder->salesorder_project_name?>" />
							<input type="hidden" name="salesorder_currency" value="<?=$salesorder->salesorder_currency?>" />
                            <input type="hidden" name="salesorder_client_company_code" value="<?=$salesorder->salesorder_client_company_code?>" />
							<input type="hidden" name="salesorder_client_company_name" value="<?=$salesorder->salesorder_client_company_name?>" />
							<input type="hidden" name="salesorder_client_company_address" value="<?=$salesorder->salesorder_client_company_address?>" />
							<input type="hidden" name="salesorder_client_company_phone" value="<?=$salesorder->salesorder_client_company_phone?>" />
							<input type="hidden" name="salesorder_client_phone" value="<?=$salesorder->salesorder_client_phone?>" />
							<input type="hidden" name="salesorder_client_name" value="<?=$salesorder->salesorder_client_name?>" />
							<input type="hidden" name="salesorder_issue" value="<?=$salesorder->salesorder_issue?>" />
							<input type="hidden" name="salesorder_expire" value="<?=$salesorder->salesorder_expire?>" />
							<input type="hidden" name="quotation_total" value="<?=$salesorder->salesorder_total?>" />
							<input type="hidden" name="salesorder_user_id" value="<?=$salesorder->salesorder_user_id?>" />
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
											<a class="btn btn-sm btn-primary btn-block" target="_blank" href="<?=base_url('assets/images/pdf/salesorder/'.$salesorder->salesorder_number.'.pdf?'.time())?>" data-toggle="tooltip" title="Print"><i class="glyphicon glyphicon-print"></i> Print</a>
										</p>
										<p class="form-group">
											<a class="btn btn-sm btn-primary btn-block<?=check_permission('purchaseorder_insert', 'disable')?>" href="<?=base_url('purchaseorder/insert/salesorder_id/'.$salesorder->salesorder_id)?>" data-toggle="tooltip" title="Purchase Order"><i class="glyphicon glyphicon-refresh"></i> Purchase order</a>
										</p>
										<p class="form-group">
											<a class="btn btn-sm btn-primary btn-block<?=check_permission('invoice_insert', 'disable')?>" href="<?=base_url('invoice/insert/salesorder_id/'.$salesorder->salesorder_id)?>" data-toggle="tooltip" title="Invoice"><i class="glyphicon glyphicon-refresh"></i> Invoice</a>
										</p>
										<!-- <p class="form-group">
											<a class="btn btn-sm btn-primary btn-block<?=check_permission('proformainvoice_insert', 'disable')?>" href="<?=base_url('proformainvoice/insert/salesorder_id/'.$salesorder->salesorder_id)?>" data-toggle="tooltip" title="Proforma Invoice"><i class="glyphicon glyphicon-refresh"></i> Proforma Invoice</a>
										</p> -->
										<p class="form-group">
											<a class="btn btn-sm btn-primary btn-block<?=check_permission('deliverynote_insert', 'disable')?>" href="<?=base_url('deliverynote/insert/salesorder_id/'.$salesorder->salesorder_id)?>" data-toggle="tooltip" title="Delivery Note"><i class="glyphicon glyphicon-refresh"></i> Delivery Note</a>
										</p>
										<?php } ?>
										<h4 class="corpcolor-font">Setting</h4>
										<p class="form-group">
											<label for="salesorder_project_name">Project name <span class="highlight">*</span></label>
											<input id="salesorder_project_name" name="salesorder_project_name" type="text" class="form-control input-sm required" placeholder="Project name" value="<?=$salesorder->salesorder_project_name?>" />
										</p>
										<p class="form-group">
											<label for="salesorder_commission_rate">Commission rate</label>
											<select id="salesorder_commission_rate" name="salesorder_commission_rate" data-placeholder="Commission rate" class="chosen-select required">
												<option value></option>
												<?php
												if($salesorder->salesorder_commission_rate == ''){
													$salesorder->salesorder_commission_rate = '8';
												}
												foreach($commissions as $key => $value){
													$selected = ($value->commission_name == $salesorder->salesorder_commission_rate) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value->commission_name.'"'.$selected.'>'.strtoupper($value->commission_name).'%</option>';
												}
												?>
											</select>
										</p>
										<p class="form-group">
											<label for="attachment">
												Customer PO
												<?php if(file_exists($_SERVER['DOCUMENT_ROOT'].'/assets/images/attachment/salesorder/'.$salesorder->salesorder_id)){ ?>
												<a target="_blank" href="<?=base_url('assets/images/attachment/salesorder/'.$salesorder->salesorder_id)?>"><i class="glyphicon glyphicon-picture"></i></a></span>
												<?php } ?>
											</label>
											<input id="attachment" name="attachment" type="file" class="form-control input-sm" placeholder="Customer PO" accept="image/*, application/pdf" />
										</p>
										<?php
										if($this->router->fetch_method() == 'update'){
											switch(true){
												case in_array('1', $this->session->userdata('role')): // administrator
												case in_array('2', $this->session->userdata('role')): // boss
												case in_array('5', $this->session->userdata('role')): // operation manager
												case in_array('6', $this->session->userdata('role')): // operation
												case in_array('7', $this->session->userdata('role')): // account
													echo '<p class="form-group"><label for="salesorder_commission_user_id">Commission to</label><select id="salesorder_commission_user_id" name="salesorder_commission_user_id" data-placeholder="Commission to" class="chosen-select required"><option value></option>';
													foreach($users as $key => $value){
														$selected = ($value->user_id == $salesorder->salesorder_commission_user_id) ? ' selected="selected"' : "" ;
														echo '<option value="'.$value->user_id.'"'.$selected.'>'.ucfirst($value->user_name).'</option>';
													}
													echo '</select></p>';
													break;
												case in_array('3', $this->session->userdata('role')): // sales manager
												case in_array('4', $this->session->userdata('role')): // sales
													echo '<p class="form-group">';
													echo '<label>Commission to</label>';
													echo '<input readonly="readonly" type="text" class="form-control input-sm required" value="'.ucfirst(get_user($salesorder->salesorder_commission_user_id)->user_name).'" />';
													echo '</p>';
													break;
											}
										}else{
											switch(true){
												case in_array('1', $this->session->userdata('role')): // administrator
												case in_array('2', $this->session->userdata('role')): // boss
												case in_array('5', $this->session->userdata('role')): // operation manager
												case in_array('6', $this->session->userdata('role')): // operation
												case in_array('7', $this->session->userdata('role')): // account
													echo '<p class="form-group"><label for="salesorder_commission_user_id">Commission to</label><select id="salesorder_commission_user_id" name="salesorder_commission_user_id" data-placeholder="Commission to" class="chosen-select required"><option value></option>';
													foreach($users as $key => $value){
														$selected = ($value->user_id == $salesorder->salesorder_commission_user_id) ? ' selected="selected"' : "" ;
														echo '<option value="'.$value->user_id.'"'.$selected.'>'.ucfirst($value->user_name).'</option>';
													}
													echo '</select></p>';
													break;
												case in_array('3', $this->session->userdata('role')): // sales manager
												case in_array('4', $this->session->userdata('role')): // sales
													echo '<p class="form-group">';
													echo '<label>Commission to</label>';
													echo '<input type="hidden" name="salesorder_commission_user_id" value="'.$this->session->userdata('user_id').'" />';
													echo '<input readonly="readonly" type="text" class="form-control input-sm required" value="'.ucfirst(get_user($this->session->userdata('user_id'))->user_name).'" />';
													echo '</p>';
													break;
											}
										}
										?>
										<p class="form-group">
											<label for="salesorder_internal_remark">Internal remark</label>
											<textarea id="salesorder_internal_remark" name="salesorder_internal_remark" class="form-control input-sm" placeholder="Internal remark"><?=$salesorder->salesorder_internal_remark?></textarea>
										</p>
										<!--
										<p class="form-group">
											<label for="salesorder_commission_user_id">Commission to</label>
											<select id="salesorder_commission_user_id" name="salesorder_commission_user_id" data-placeholder="Commission to" class="chosen-select required">
												<option value></option>
												<?php
												foreach($users as $key => $value){
													$selected = ($value->user_id == $salesorder->salesorder_commission_user_id) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value->user_id.'"'.$selected.'>'.ucfirst($value->user_name).'</option>';
												}
												?>
											</select>
										</p>
										-->
										<!-- <p class="form-group">
											<label for="attachment">Delivery address</label>
											<textarea id="salesorder_client_delivery_address" name="salesorder_client_delivery_address" class="form-control input-sm" placeholder="Delivery"><?=$salesorder->salesorder_client_delivery_address?></textarea>
										</p> -->
										<!-- <h4 class="corpcolor-font">Client information</h4>
										<table class="table table-striped">
											<tbody>
												<tr>
													<td><label>To</label></td>
													<td><?=$salesorder->salesorder_client_company_name?></td>
												</tr>
												<tr>
													<td><label>Address</label></td>
													<td><?=$salesorder->salesorder_client_company_address?></td>
												</tr>
												<tr>
													<td><label>Phone</label></td>
													<td><?=$salesorder->salesorder_client_company_phone?></td>
												</tr>
												<tr>
													<td><label>Mobile</label></td>
													<td><?=$salesorder->salesorder_client_phone?></td>
												</tr>
												<tr>
													<td><label>Attn.</label></td>
													<td><?=$salesorder->salesorder_client_name?></td>
												</tr>
											</tbody>
										</table>
										<h4 class="corpcolor-font">Quotation information</h4>
										<table class="table table-striped">
											<tbody>
												<tr>
													<td><label>Quotation#</label></td>
													<td><a href="<?=base_url('/quotation/select/quotation_id/8')?>"><?=get_quotation($salesorder->salesorder_quotation_id)->quotation_number?></a></td>
												</tr>
												<tr>
													<td><label>Issue date</label></td>
													<td><?=$salesorder->salesorder_issue?></td>
												</tr>
												<tr>
													<td><label>Saleman</label></td>
													<td><?=get_user($salesorder->salesorder_user_id)->user_name?></td>
												</tr>
												<tr>
													<td><label>Terms</label></td>
													<td><?=$salesorder->salesorder_terms?></td>
												</tr>
												<tr>
													<td><label>Expire Date</label></td>
													<td><?=$salesorder->salesorder_expire?></td>
												</tr>
												<tr>
													<td><label>Quotation discount</label></td>
													<td><?=$quotation->quotation_discount?></td>
												</tr>
												<tr>
													<td><label>Quotation grand total</label></td>
													<td><?=$quotation->quotation_total?></td>
												</tr>
											</tbody>
										</table> -->
									</div>
									<div class="col-sm-9 col-xs-12">
										<h4 class="corpcolor-font">Sales order</h4>
										<div class="row">
											<div class="col-sm-6 col-xs-6">
												<table class="table table-condensed table-borderless">
													<tr>
														<td><label for="salesorder_client_company_name">To</label></td>
														<td><input id="salesorder_client_company_name" name="salesorder_client_company_name" type="text" class="form-control input-sm required" placeholder="Company/Domain/Client" value="<?=$salesorder->salesorder_client_company_name?>" /></td>
													</tr>
													<tr>
														<td><label for="salesorder_client_company_address">Address</label></td>
														<td><textarea id="salesorder_client_company_address" name="salesorder_client_company_address" class="form-control input-sm" placeholder="Address"><?=$salesorder->salesorder_client_company_address?></textarea></td>
													</tr>
													<tr>
														<td><label for="salesorder_client_company_phone">Phone</label></td>
														<td><input id="salesorder_client_company_phone" name="salesorder_client_company_phone" type="text" class="form-control input-sm" placeholder="Phone" value="<?=$salesorder->salesorder_client_company_phone?>" /></td>
													</tr>
													<tr>
														<td><label for="salesorder_client_phone">Mobile</label></td>
														<td><input id="salesorder_client_phone" name="salesorder_client_phone" type="text" class="form-control input-sm" placeholder="Fax" value="<?=$salesorder->salesorder_client_phone?>" /></td>
													</tr>
													<tr>
														<td><label for="salesorder_client_email">Email</label></td>
														<td><input id="salesorder_client_email" name="salesorder_client_email" type="text" class="form-control input-sm" placeholder="Email" value="<?=$salesorder->salesorder_client_email?>" /></td>
													</tr>
													<tr>
														<td><label for="salesorder_client_name">Attn</label></td>
														<td><input id="salesorder_client_name" name="salesorder_client_name" type="text" class="form-control input-sm required" placeholder="Attn." value="<?=$salesorder->salesorder_client_name?>" /></td>
													</tr>
													<tr>
														<td><label for="salesorder_client_delivery_address">Delivery</label></td>
														<td><textarea id="salesorder_client_delivery_address" name="salesorder_client_delivery_address" class="form-control input-sm" placeholder="Delivery"><?=$salesorder->salesorder_client_delivery_address?></textarea></td>
													</tr>
												</table>
											</div>
											<div class="col-sm-1 col-xs-1">
											</div>
											<div class="col-sm-5 col-xs-5">
												<table class="table table-condensed table-borderless">
													<tr>
														<td><label for="salesorder_number">Sales order#</label></td>
														<td>
															<div class="input-group">
																<input readonly="readonly" id="salesorder_number" name="salesorder_number" type="text" class="form-control input-sm" placeholder="Sales order#" value="<?=$salesorder->salesorder_number?>" />
																<span class="input-group-addon"><?='v'.$salesorder->salesorder_version?></span>
															</div>
														</td>
													</tr>
													<tr>
														<td><label for="salesorder_issue">Date</label></td>
														<td><input id="salesorder_issue" name="salesorder_issue" type="text" class="form-control input-sm date-mask required" placeholder="Issue date" value="<?=($salesorder->salesorder_issue != '') ? $salesorder->salesorder_issue : date('Y-m-d')?>" /></td>
													</tr>
													<tr>
														<td><label for="salesorder_user_name">Sales</label></td>
														<td><input readonly="readonly" id="salesorder_user_name" name="salesorder_user_name" type="text" class="form-control input-sm required" placeholder="Saleman" value="<?=$user->user_name?>" /></td>
													</tr>
													<tr>
														<td><label for="salesorder_expire">Expire Date</label></td>
														<td><input id="salesorder_expire" name="salesorder_expire" type="text" class="form-control input-sm date-mask" placeholder="Expire Date" value="<?=($salesorder->salesorder_expire != '' && $this->router->fetch_method() != 'duplicate') ? $salesorder->salesorder_expire : date('Y-m-d', strtotime('+14 days', time()))?>" /></td>
													</tr>
												</table>
											</div>
										</div>
										<div class="list-area">
											<table class="table list" id="salesorder">
												<thead>
													<tr>
														<th width="10%">
															<a class="btn btn-sm btn-primary salesorderitem-insert-btn" data-toggle="tooltip" title="Insert">
																<i class="glyphicon glyphicon-plus"></i>
															</a>
														</th>
														<th>Detail</th>
														<th width="12%">Price</th>
														<th width="12%">Quantity</th>
														<th width="12%">Subtotal</th>
													</tr>
												</thead>
												<tbody class="trModal">
													<?php foreach($salesorderitems as $key => $value){ ?>
													<tr>
														<td>
															<div>
																<input name="salesorderitem_id[]" type="hidden" value="<?=$value->salesorderitem_id?>" />
																<input name="salesorderitem_salesorder_id[]" type="hidden" value="<?=$value->salesorderitem_salesorder_id?>" />
																<input name="salesorderitem_product_type_name[]" type="hidden" value="<?=$value->salesorderitem_product_type_name?>" />
																<input id="salesorderitem_product_code" name="salesorderitem_product_code[]" type="text" class="form-control input-sm required" placeholder="Code" value="<?=$value->salesorderitem_product_code?>" />
															</div>
															<div class="margin-top-10">
																<div class="btn-group">
																	<button type="button" class="btn btn-sm btn-primary salesorderitem-delete-btn"><i class="glyphicon glyphicon-remove"></i></button>
																	<button type="button" class="btn btn-sm btn-primary up-btn"><i class="glyphicon glyphicon-chevron-up"></i></button>
																	<button type="button" class="btn btn-sm btn-primary down-btn"><i class="glyphicon glyphicon-chevron-down"></i></button>
																</div>
															</div>
														</td>
														<td>
															<div>
                                                                <input id="salesorderitem_product_id" name="salesorderitem_product_id[]" type="hidden" class="form-control input-sm" placeholder="Product" value="<?=$value->salesorderitem_product_id?>" />
                                                                <input type="button" class="form-control input-sm showModal" value="Select a product"/>
															</div>
															<div class="margin-top-10">
																<div class="input-group">
																	<span class="input-group-addon corpcolor-font">Title</span>
																	<input id="salesorderitem_product_name" name="salesorderitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="<?=$value->salesorderitem_product_name?>" />
																</div>
															</div>
															<div>
																<textarea id="salesorderitem_product_detail" name="salesorderitem_product_detail[]" class="form-control input-sm" placeholder="Detail"><?=$value->salesorderitem_product_detail?></textarea>
															</div>
														</td>
														<td>
															<input id="salesorderitem_product_price" name="salesorderitem_product_price[]" type="number" min="0" class="form-control input-sm" placeholder="Price" value="<?=$value->salesorderitem_product_price?>" />
														</td>
														<td>
                                                            <div class="input-group">
                                                                <input id="salesorderitem_quantity" name="salesorderitem_quantity[]" type="number" min="0" class="form-control input-sm" placeholder="Quantity" value="<?=($value->salesorderitem_quantity) ? $value->salesorderitem_quantity : '1'?>" />
                                                                <span class="input-group-addon">Unit</span>
                                                            </div>
                                                        </td>
														<td>
															<input readonly="readonly" id="salesorderitem_subtotal" name="salesorderitem_subtotal[]" type="text" class="form-control input-sm" placeholder="Subtotal" value="<?=$value->salesorderitem_subtotal?>" />
														</td>
													</tr>
													<?php } ?>
												</tbody>
												<tfoot>
													<tr>
														<th></th>
														<th></th>
														<th></th>
														<th>Discount %</th>
														<th>
                                                            <div class="input-group">
                                                                <input readonly="readonly" id="salesorder_discount" name="salesorder_discount" type="number" min="0" max="100" class="form-control input-sm required" placeholder="Discount" value="<?=($salesorder->salesorder_discount) ? $salesorder->salesorder_discount : 100?>" />
                                                                <span class="input-group-addon">%</span>
                                                            </div>
                                                        </th>
													</tr>
													<tr>
														<th width="10%">
															<a class="btn btn-sm btn-primary salesorderitem-insert-btn" data-toggle="tooltip" title="Insert">
																<i class="glyphicon glyphicon-plus"></i>
															</a>
														</th>
														<th></th>
														<th></th>
														<th>Grand total</th>
														<th><input readonly="readonly" id="salesorder_total" name="salesorder_total" type="text" class="form-control input-sm" placeholder="Grand total" value="<?=($salesorder->salesorder_total) ? $salesorder->salesorder_total : '0'?>" /></th>
													</tr>
												</tfoot>
											</table>
										</div>
										<hr />
										<p class="form-group">
											<label for="salesorder_remark">Remark</label>
											<textarea id="salesorder_remark" name="salesorder_remark" class="form-control input-sm" placeholder="Remark" rows="3"><?=$salesorder->salesorder_remark?></textarea>
										</p>
										<p class="form-group">
											<label for="salesorder_payment">Payment</label>
											<textarea id="salesorder_payment" name="salesorder_payment" class="form-control input-sm" placeholder="Payment" rows="3"><?=$salesorder->salesorder_payment?></textarea>
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

					<h2 class="col-sm-12">Sales order management</h2>

					<div class="content-column-area col-md-12 col-sm-12">

						<div class="fieldset">
							<?=$this->session->tempdata('alert');?>
							<div class="search-area">

								<form salesorder="form" method="get">
									<input type="hidden" name="salesorder_id" />
									<table>
										<tbody>
											<tr>
												<td width="90%">
													<div class="row">
														<div class="col-sm-2"><h6>Sales Order</h6></div>
														<div class="col-sm-2">
															<input type="text" name="salesorder_number_like" class="form-control input-sm" placeholder="SONo" value="" />
														</div>
														<!-- <div class="col-sm-2">
															<input type="text" name="salesorder_number_greateq" class="form-control input-sm" placeholder="SONo From" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="salesorder_number_smalleq" class="form-control input-sm" placeholder="SONo To" value="" />
														</div> -->
														<div class="col-sm-2">
															<span class="input-group date datetimepicker">
																<input id="salesorder_create_greateq" name="salesorder_create_greateq" type="text" class="form-control input-sm date-mask" placeholder="Date From (YYYY-MM-DD)" />
																<span class="input-group-addon">
																	<span class="glyphicon glyphicon-calendar"></span>
																</span>
															</span>
															<!-- <input type="text" name="salesorder_create_greateq" class="form-control input-sm date-mask" placeholder="Date From (YYYY-MM-DD)" value="" /> -->
														</div>
														<div class="col-sm-2">
															<span class="input-group date datetimepicker">
																<input id="salesorder_create_smalleq" name="salesorder_create_smalleq" type="text" class="form-control input-sm date-mask" placeholder="Date To (YYYY-MM-DD)" />
																<span class="input-group-addon">
																	<span class="glyphicon glyphicon-calendar"></span>
																</span>
															</span>
															<!-- <input type="text" name="salesorder_create_smalleq" class="form-control input-sm date-mask" placeholder="Date To (YYYY-MM-DD)" value="" /> -->
														</div>
														<div class="col-sm-2">
															<input type="text" name="quotation_number_like" class="form-control input-sm" placeholder="QONo" value="" />
														</div>
														<div class="col-sm-2">
															<select id="salesorder_status" name="salesorder_status" data-placeholder="Status" class="chosen-select">
																<option value></option>
																<?php foreach($statuss as $key => $value){ ?>
																<option value="<?=$value->status_name?>"><?=ucfirst($value->status_name)?></option>
																<?php } ?>
															</select>
														</div>
													</div>
                                                    <div class="row">
                                                        <div class="col-sm-2"><h6>Purchase Order</h6></div>
                                                        <div class="col-sm-2">
                                                            <input type="text" name="purchase_number_like" class="form-control input-sm" placeholder="PONo" value="" />
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <select id="purchaseorder_status" name="purchaseorder_status" data-placeholder="PO Status" class="chosen-select">
                                                                <option value></option>
                                                                <?php foreach($statuss as $key => $value){ ?>
                                                                    <option value="<?=$value->status_name?>"><?=ucfirst($value->status_name)?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <select id="purchaseorder_arrive_status" name="purchaseorder_arrive_status" data-placeholder="Arrive Status" class="chosen-select">
                                                                <option value></option>
                                                                <?php foreach($statuss as $key => $value){ ?>
                                                                    <option value="<?=$value->status_name?>"><?=ucfirst($value->status_name)?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-2"><h6>Invoice Order</h6></div>
                                                        <div class="col-sm-2">
                                                            <input type="text" name="invoice_number_like" class="form-control input-sm" placeholder="INNo" value="" />
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <select id="invoiceorder_status" name="invoiceorder_status" data-placeholder="IN Status" class="chosen-select">
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
															<select id="salesorder_status" name="salesorder_status" data-placeholder="Status" class="chosen-select">
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
															<input type="text" name="salesorder_client_company_name_like" class="form-control input-sm" placeholder="Customer company name" value="" />
														</div>
														<div class="col-sm-2">
															<select id="salesorder_user_id" name="salesorder_user_id" data-placeholder="Sales" class="chosen-select">
																<option value></option>
																<?php foreach($users as $key => $value){ ?>
																<option value="<?=$value->user_id?>"><?=ucfirst($value->user_name)?></option>
																<?php } ?>
															</select>
														</div>
														<div class="col-sm-2">
															<!-- <input type="text" name="salesorder_client_company_name_salesorder_client_name_like" class="form-control input-sm" placeholder="Customer PO" value="" /> -->
														</div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
													</div>
													<div class="row">
														<div class="col-sm-2"><h6>Project</h6></div>
														<div class="col-sm-2">
															<input type="text" name="salesorder_project_name_like" class="form-control input-sm" placeholder="Project Name" value="" />
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
								<form name="list" action="<?=base_url('salesorder/delete')?>" method="post">
									<input type="hidden" name="salesorder_id" />
									<input type="hidden" name="salesorder_delete_reason" />
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
                                                <th>IN No</th>
												<th>Create</th>
												<th>Customer</th>
												<th>Project</th>
												<th>Sales</th>
												<th>Deadline</th>
												<th>Cost</th>
												<th>Total</th>
												<th>Status</th>
												<th>Est GP / GP</th>
												<th></th>
												<th></th>
												<th class="text-right">
													<!-- <a href="<?=base_url('salesorder/insert')?>" data-toggle="tooltip" title="Insert">
														<i class="glyphicon glyphicon-plus"></i>
													</a> -->
												</th>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach($salesorders as $key => $value){
											$salesorder_cost = get_salesorder_cost($value->salesorder_id);
											?>
											<tr>
												<td><a href="<?=base_url('salesorder/update/salesorder_id/'.$value->salesorder_id)?>"><?=$value->salesorder_number?></a></td>
												<td><a href="<?=base_url('quotation/update/quotation_id/'.$value->salesorder_quotation_id)?>"><?=get_quotation($value->salesorder_quotation_id)->quotation_number?></a></td>
												<td>
													<?php foreach($value->purchaseorders as $key1 => $value1){ ?>
													<div class="no-wrap">
                                                        <?php
                                                        switch($value1->purchaseorder_status){
                                                            case 'complete':
                                                                echo '<a data-toggle="tooltip" title="Payment Complete">';
                                                                echo '<span class="corpcolor-font"><i class="glyphicon glyphicon-ok-circle"></i></span>';
                                                                echo '</a>';
                                                                break;
                                                            case 'cancel':
                                                                echo '<a data-toggle="tooltip" title="Payment Cancel">';
                                                                echo '<span class="corpcolor-font"><i class="glyphicon glyphicon-remove-circle"></i></span>';
                                                                echo '</a>';
                                                                break;
                                                            case 'partial':
                                                                echo '<a data-toggle="tooltip" title="Payment Partial">';
                                                                echo '<span class="corpcolor-font"><i class="glyphicon glyphicon-adjust"></i></span>';
                                                                echo '</a>';
                                                                break;
                                                            default:
                                                                echo '<a data-toggle="tooltip" title="Payment Processing">';
                                                                echo '<span class="corpcolor-font"><i class="glyphicon glyphicon-play-circle"></i></span>';
                                                                echo '</a>';
                                                                break;
                                                        }
                                                        echo " ";
                                                        switch($value1->purchaseorder_arrive_status){
                                                            case 'complete':
                                                                echo '<a data-toggle="tooltip" title="Stock Complete">';
                                                                echo '<span class="corpcolor-font"><i class="glyphicon glyphicon-ok-circle"></i></span>';
                                                                echo '</a>';
                                                                break;
                                                            case 'cancel':
                                                                echo '<a data-toggle="tooltip" title="Stock Cancel">';
                                                                echo '<span class="corpcolor-font"><i class="glyphicon glyphicon-remove-circle"></i></span>';
                                                                echo '</a>';
                                                                break;
                                                            case 'partial':
                                                                echo '<a data-toggle="tooltip" title="Stock Partial">';
                                                                echo '<span class="corpcolor-font"><i class="glyphicon glyphicon-adjust"></i></span>';
                                                                echo '</a>';
                                                                break;
                                                            default:
                                                                echo '<a data-toggle="tooltip" title="Stock Processing">';
                                                                echo '<span class="corpcolor-font"><i class="glyphicon glyphicon-play-circle"></i></span>';
                                                                echo '</a>';
                                                                break;
                                                        }
                                                        ?>
                                                        <a href="<?=base_url('purchaseorder/update/purchaseorder_id/'.$value1->purchaseorder_id)?>"><?=$value1->purchaseorder_number?></a></div>
													<?php } ?>
												</td>
                                                <td>
                                                    <?php foreach($value->invoiceorders as $key1 => $value1){ ?>
                                                        <div class="no-wrap">
                                                            <?php
                                                            switch($value1->invoice_status){
                                                                case 'complete':
                                                                    echo '<a data-toggle="tooltip" title="IN Complete">';
                                                                    echo '<span class="corpcolor-font"><i class="glyphicon glyphicon-ok-circle"></i></span>';
                                                                    echo '</a>';
                                                                    break;
                                                                case 'cancel':
                                                                    echo '<a data-toggle="tooltip" title="IN Cancel">';
                                                                    echo '<span class="corpcolor-font"><i class="glyphicon glyphicon-remove-circle"></i></span>';
                                                                    echo '</a>';
                                                                    break;
                                                                case 'partial':
                                                                    echo '<a data-toggle="tooltip" title="IN Partial">';
                                                                    echo '<span class="corpcolor-font"><i class="glyphicon glyphicon-adjust"></i></span>';
                                                                    echo '</a>';
                                                                    break;
                                                                default:
                                                                    echo '<a data-toggle="tooltip" title="IN Processing">';
                                                                    echo '<span class="corpcolor-font"><i class="glyphicon glyphicon-play-circle"></i></span>';
                                                                    echo '</a>';
                                                                    break;
                                                            }
                                                            ?>
                                                            <a href="<?=base_url('invoice/update/invoice_id/'.$value1->invoice_id)?>"><?=$value1->invoice_number?></a></div>
                                                    <?php } ?>
                                                </td>
												<td><?=convert_datetime_to_date($value->salesorder_create)?></td>
												<td><?=$value->salesorder_client_company_name?></td>
												<td><?=$value->salesorder_project_name?></td>
												<td><?=ucfirst(get_user($value->salesorder_quotation_user_id)->user_name)?></td>
												<td><?=$value->salesorder_expire?></td>
												<td><?=strtoupper($value->salesorder_currency).' '.money_format('%!n', $salesorder_cost)?></td>
												<td><?=strtoupper($value->salesorder_currency).' '.money_format('%!n', $value->salesorder_total)?></td>
												<td><?=ucfirst($value->salesorder_status)?></td>
												<td><?=((ucfirst($value->salesorder_status) == 'Complete') ? '<span class="corpcolor-font">GP</span>' : '<span class="corpcolor-font">Est</span>').' '.strtoupper($value->salesorder_currency).' '.money_format('%!n', $value->salesorder_total - $salesorder_cost)?></td>
												<td class="text-right">
													<a target="_blank" href="<?=base_url('/assets/images/pdf/salesorder/'.$value->salesorder_number.'.pdf')?>" data-toggle="tooltip" title="Print">
														<i class="glyphicon glyphicon-print"></i>
													</a>
												</td>
												<td class="text-right">
													<?php if(!check_permission('salesorder_update', 'display')){ ?>
													<a href="<?=base_url('salesorder/update/salesorder_id/'.$value->salesorder_id)?>" data-toggle="tooltip" title="Update">
														<i class="glyphicon glyphicon-edit"></i>
													</a>
													<?php }else{ ?>
													<i class="glyphicon glyphicon-edit"></i>
													<?php } ?>
												</td>
												<td class="text-right">
													<?php if(!check_permission('salesorder_delete', 'display')){ ?>
													<a onclick="check_delete(<?=$value->salesorder_id?>);" data-toggle="tooltip" title="Remove">
														<i class="glyphicon glyphicon-remove"></i>
													</a>
													<?php }else{ ?>
													<i class="glyphicon glyphicon-remove"></i>
													<?php } ?>
												</td>
											</tr>
											<?php } ?>

											<?php if(!$salesorders){ ?>
											<tr>
												<td colspan="16">No record found</td>
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
										<a href="<?=base_url('commissionchecklist/select/invoice_commission_status/processing')?>">Commission checklist</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-user"></i>
										<a href="<?=base_url('salesreport')?>">Sales report</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-user"></i>
										<a href="<?=base_url('commissionreport')?>">Commission report</a>
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
                    <div class="popup-header"><a href="javascript:" class="popup-close">Close</a></div>
                    <div class="popup-list-area">
                        <form name="list">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>SO No</th>
                                    <th>Confirmed</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($popup_list as $key => $value){ ?>
                                    <tr>
                                        <td><a href="<?=base_url('salesorder/update/salesorder_id/'.$value->salesorder_id)?>" data-toggle="tooltip" title="Update"><?=$value->salesorder_number?></a></td>
                                        <td><?=$value->salesorder_confirmed_date?></td>
                                        <td><?=ucfirst($value->salesorder_status)?></td>
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