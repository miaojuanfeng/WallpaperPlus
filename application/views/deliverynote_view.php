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

			$('input[name="deliverynote_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');

			/*--------- date mask ---------*/
			$('.date-mask').mask('9999-99-99');

			/*--------- datetimepicker ---------*/
			$('.datetimepicker').datetimepicker({
				format: 'Y-MM-DD'
			});

			/* deliverynoteitem-insert-btn */
			$(document).on('click', '.deliverynoteitem-insert-btn', function(){
				add_deliverynoteitem_row();
			});

			/* deliverynoteitem-delete-btn */
			$(document).on('click', '.deliverynoteitem-delete-btn', function(){
				if(confirm('Confirm delete?')){
					$(this).closest('tr').remove();
					calc();
				}else{
					return false;
				}
			});

			/* product loader */
			$(document).on('change', 'select[name="deliverynoteitem_product_id[]"]', function(){
				product_loader($(this));
			});

			/* trigger calc */
			$(document).on('blur', 'input[name="deliverynoteitem_product_price[]"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="deliverynoteitem_quantity[]"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="deliverynote_discount"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="deliverynote_pay"]', function(){
				calc();
			});
			$(document).on('change', 'input[name="deliverynote_currency"]', function(){
				$.each($('select[name="deliverynoteitem_product_id[]"]'), function(key, val){
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
				total += parseFloat($(this).find('input[name="deliverynoteitem_quantity[]"]').val());
			});
			$('input[name="deliverynote_total"]').val(parseFloat(total)).css('display', 'none').fadeIn();
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

		function product_loader(thisObject){
			thisRow = $(thisObject).closest('tr').index();
			thisCurrency = $('input[name="deliverynote_currency"]').val();
			$('.scriptLoader').load('/load', {'thisTableId': 'deliverynoteProductLoader', 'thisRecordId': $(thisObject).val(), 'thisCurrency': thisCurrency, 'thisRow': thisRow, 't': timestamp()}, function(){
				deliverynoteProductLoader();
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
		function add_deliverynoteitem_row(){
			deliverynoteitem_row = '';
			deliverynoteitem_row += '<tr>';
			deliverynoteitem_row += '<td>';
			deliverynoteitem_row += '<div>';
			deliverynoteitem_row += '<input name="deliverynoteitem_id[]" type="hidden" value="" />';
			deliverynoteitem_row += '<input name="deliverynoteitem_deliverynote_id[]" type="hidden" value="" />';
			deliverynoteitem_row += '<input name="deliverynoteitem_product_type_name[]" type="hidden" value="" />';
			deliverynoteitem_row += '<input id="deliverynoteitem_product_code" name="deliverynoteitem_product_code[]" type="text" class="form-control input-sm" placeholder="Code" value="" />';
			deliverynoteitem_row += '</div>';
			deliverynoteitem_row += '<div class="margin-top-10">';
			deliverynoteitem_row += '<div class="btn-group">';
			deliverynoteitem_row += '<button type="button" class="btn btn-sm btn-primary deliverynoteitem-delete-btn"><i class="glyphicon glyphicon-remove"></i></button>';
			deliverynoteitem_row += '<button type="button" class="btn btn-sm btn-primary up-btn"><i class="glyphicon glyphicon-chevron-up"></i></button>';
			deliverynoteitem_row += '<button type="button" class="btn btn-sm btn-primary down-btn"><i class="glyphicon glyphicon-chevron-down"></i></button>';
			deliverynoteitem_row += '</div>';
			deliverynoteitem_row += '</div>';
			deliverynoteitem_row += '</td>';
			deliverynoteitem_row += '<td colspan="2">';
            deliverynoteitem_row += '<div>';
            deliverynoteitem_row += '<input id="deliverynoteitem_product_id" name="deliverynoteitem_product_id[]" type="hidden" class="form-control input-sm" placeholder="Product" value="" />';
            deliverynoteitem_row += '<input type="button" class="form-control input-sm showModal" modal="product_select" value="Select a product" />';
            deliverynoteitem_row += '</div>';
			deliverynoteitem_row += '<div class="margin-top-10">';
			// deliverynoteitem_row += '<input id="deliverynoteitem_product_name" name="deliverynoteitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="" />';
			deliverynoteitem_row += '<div class="input-group width100percent">';
			deliverynoteitem_row += '<span class="input-group-addon corpcolor-font">Title</span>';
			deliverynoteitem_row += '<input id="deliverynoteitem_product_name" name="deliverynoteitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="" />';
			deliverynoteitem_row += '</div>';
			deliverynoteitem_row += '</div>';
			deliverynoteitem_row += '<div>';
			deliverynoteitem_row += '<textarea id="deliverynoteitem_product_detail" name="deliverynoteitem_product_detail[]" class="form-control input-sm" placeholder="Detail"></textarea>';
			deliverynoteitem_row += '</div>';
			deliverynoteitem_row += '</td>';
			deliverynoteitem_row += '<td>';
            deliverynoteitem_row += '<div class="input-group">';
			deliverynoteitem_row += '<input id="deliverynoteitem_quantity" name="deliverynoteitem_quantity[]" type="number" min="0" class="form-control input-sm" placeholder="Quantity" value="1" />';
            deliverynoteitem_row += '<input id="deliverynoteitem_unit" name="deliverynoteitem_unit[]" type="hidden" />';
            deliverynoteitem_row += '<span class="input-group-addon deliverynoteitem_unit">Unit</span>';
            deliverynoteitem_row += '</div>';
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
							<input type="hidden" name="deliverynote_id" value="<?=$deliverynote->deliverynote_id?>" />
							<input type="hidden" name="deliverynote_quotation_user_id" value="<?=$deliverynote->deliverynote_quotation_user_id?>" />
							<input type="hidden" name="deliverynote_salesorder_id" value="<?=$deliverynote->deliverynote_salesorder_id?>" />
							<input type="hidden" name="deliverynote_client_id" value="<?=$deliverynote->deliverynote_client_id?>" />
							<input type="hidden" name="deliverynote_project_name" value="<?=$deliverynote->deliverynote_project_name?>" />
							<input type="hidden" name="deliverynote_currency" value="<?=$deliverynote->deliverynote_currency?>" />
							<input type="hidden" name="deliverynote_client_company_name" value="<?=$deliverynote->deliverynote_client_company_name?>" />
							<input type="hidden" name="deliverynote_client_company_address" value="<?=$deliverynote->deliverynote_client_company_address?>" />
							<input type="hidden" name="deliverynote_client_company_phone" value="<?=$deliverynote->deliverynote_client_company_phone?>" />
							<input type="hidden" name="deliverynote_client_phone" value="<?=$deliverynote->deliverynote_client_phone?>" />
							<input type="hidden" name="deliverynote_client_name" value="<?=$deliverynote->deliverynote_client_name?>" />
							<input type="hidden" name="deliverynote_issue" value="<?=$deliverynote->deliverynote_issue?>" />
							<input type="hidden" name="deliverynote_expire" value="<?=$deliverynote->deliverynote_expire?>" />
							<input type="hidden" name="salesorder_total" value="<?=$deliverynote->deliverynote_total?>" />
							<input type="hidden" name="deliverynote_user_id" value="<?=$deliverynote->deliverynote_user_id?>" />
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
											<a class="btn btn-sm btn-primary btn-block" target="_blank" href="<?=base_url('assets/images/pdf/deliverynote/'.$deliverynote->deliverynote_number.'.pdf?'.time())?>" data-toggle="tooltip" title="Print"><i class="glyphicon glyphicon-print"></i> Print</a>
										</p>
										<?php } ?>
										<h4 class="corpcolor-font">Setting</h4>
										<p class="form-group">
											<label for="deliverynote_lot_number">Lot number</label>
											<input id="deliverynote_lot_number" name="deliverynote_lot_number" type="text" class="form-control input-sm" placeholder="Lot number" value="<?=$deliverynote->deliverynote_lot_number?>" />
										</p>
                                        <p class="form-group">
                                            <label for="deliverynote_status">Status</label>
                                            <select id="deliverynote_status" name="deliverynote_status" data-placeholder="Status" class="chosen-select required">
                                                <option value></option>
                                                <?php
                                                if($deliverynote->deliverynote_status == ''){
                                                    $deliverynote->deliverynote_status = 'hkd';
                                                }
                                                foreach($statuss as $key => $value){
                                                    $selected = ($value->status_name == $deliverynote->deliverynote_status) ? ' selected="selected"' : "" ;
                                                    echo '<option value="'.$value->status_name.'"'.$selected.'>'.strtoupper($value->status_name).'</option>';
                                                }
                                                ?>
                                            </select>
                                        </p>
									</div>
									<div class="col-sm-9 col-xs-12">
										<h4 class="corpcolor-font">Delivery note</h4>
										<div class="row">
											<div class="col-sm-6 col-xs-6">
												<table class="table table-condensed table-borderless">
													<tr>
														<td><label for="deliverynote_client_company_name">To</label></td>
														<td><input id="deliverynote_client_company_name" name="deliverynote_client_company_name" type="text" class="form-control input-sm required" placeholder="Company/Domain/Client" value="<?=$deliverynote->deliverynote_client_company_name?>" /></td>
													</tr>
													<tr>
														<td><label for="deliverynote_client_delivery_address">Delivery</label></td>
														<td><textarea id="deliverynote_client_delivery_address" name="deliverynote_client_delivery_address" class="form-control input-sm" placeholder="Address"><?=$deliverynote->deliverynote_client_delivery_address?></textarea></td>
													</tr>
													<tr>
														<td><label for="deliverynote_client_company_phone">Phone</label></td>
														<td><input id="deliverynote_client_company_phone" name="deliverynote_client_company_phone" type="text" class="form-control input-sm" placeholder="Phone" value="<?=$deliverynote->deliverynote_client_company_phone?>" /></td>
													</tr>
													<tr>
														<td><label for="deliverynote_client_phone">Mobile</label></td>
														<td><input id="deliverynote_client_phone" name="deliverynote_client_phone" type="text" class="form-control input-sm" placeholder="Fax" value="<?=$deliverynote->deliverynote_client_phone?>" /></td>
													</tr>
													<tr>
														<td><label for="deliverynote_client_email">Email</label></td>
														<td><input id="deliverynote_client_email" name="deliverynote_client_email" type="text" class="form-control input-sm" placeholder="Email" value="<?=$deliverynote->deliverynote_client_email?>" /></td>
													</tr>
													<tr>
														<td><label for="deliverynote_client_name">Attn</label></td>
														<td><input id="deliverynote_client_name" name="deliverynote_client_name" type="text" class="form-control input-sm required" placeholder="Attn." value="<?=$deliverynote->deliverynote_client_name?>" /></td>
													</tr>
												</table>
											</div>
											<div class="col-sm-1 col-xs-1">
											</div>
											<div class="col-sm-5 col-xs-5">
												<table class="table table-condensed table-borderless">
													<tr>
														<td><label for="deliverynote_number">Delivery note#</label></td>
														<td>
															<div class="input-group">
																<input readonly="readonly" id="deliverynote_number" name="deliverynote_number" type="text" class="form-control input-sm" placeholder="Delivery note#" value="<?=$deliverynote->deliverynote_number?>" />
																<span class="input-group-addon"><?='v'.$deliverynote->deliverynote_version?></span>
															</div>
														</td>
													</tr>
													<tr>
														<td><label for="deliverynote_issue">Date</label></td>
														<td><input id="deliverynote_issue" name="deliverynote_issue" type="text" class="form-control input-sm date-mask required" placeholder="Issue date" value="<?=($deliverynote->deliverynote_issue != '') ? $deliverynote->deliverynote_issue : date('Y-m-d')?>" /></td>
													</tr>
													<tr>
														<td><label for="deliverynote_user_name">Sales</label></td>
														<td><input readonly="readonly" id="deliverynote_user_name" name="deliverynote_user_name" type="text" class="form-control input-sm required" placeholder="Saleman" value="<?=$user->user_name?>" /></td>
													</tr>
													<tr>
														<td><label for="deliverynote_expire">Expire Date</label></td>
														<td><input id="deliverynote_expire" name="deliverynote_expire" type="text" class="form-control input-sm date-mask" placeholder="Expire Date" value="<?=($deliverynote->deliverynote_expire != '' && $this->router->fetch_method() != 'duplicate') ? $deliverynote->deliverynote_expire : date('Y-m-d', strtotime('+14 days', time()))?>" /></td>
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
														<th width="10%"></th>
														<th width="12%">Quantity</th>
													</tr>
												</thead>
												<tbody class="trModal">
													<?php foreach($deliverynoteitems as $key => $value){ ?>
													<tr>
														<td>
															<div>
																<input name="deliverynoteitem_id[]" type="hidden" value="<?=$value->deliverynoteitem_id?>" />
																<input name="deliverynoteitem_deliverynote_id[]" type="hidden" value="<?=$value->deliverynoteitem_deliverynote_id?>" />
																<input name="deliverynoteitem_product_type_name[]" type="hidden" value="<?=$value->deliverynoteitem_product_type_name?>" />
																<input id="deliverynoteitem_product_code" name="deliverynoteitem_product_code[]" type="text" class="form-control input-sm" placeholder="Code" value="<?=$value->deliverynoteitem_product_code?>" />
															</div>
															<div class="margin-top-10">
																<div class="btn-group">
																	<button type="button" class="btn btn-sm btn-primary deliverynoteitem-delete-btn"><i class="glyphicon glyphicon-remove"></i></button>
																	<button type="button" class="btn btn-sm btn-primary up-btn"><i class="glyphicon glyphicon-chevron-up"></i></button>
																	<button type="button" class="btn btn-sm btn-primary down-btn"><i class="glyphicon glyphicon-chevron-down"></i></button>
																</div>
															</div>
														</td>
														<td colspan="2">
                                                            <div>
                                                                <input id="deliverynoteitem_product_id" name="deliverynoteitem_product_id[]" type="hidden" class="form-control input-sm" placeholder="Product" value="<?=$value->deliverynoteitem_product_id?>" />
                                                                <input type="button" class="form-control input-sm showModal" modal="product_select" value="Select a product"/>
                                                            </div>
															<div class="margin-top-10">
																<div class="input-group width100percent">
																	<span class="input-group-addon corpcolor-font">Title</span>
																	<input id="deliverynoteitem_product_name" name="deliverynoteitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="<?=$value->deliverynoteitem_product_name?>" />
																</div>
															</div>
															<div>
																<textarea id="deliverynoteitem_product_detail" name="deliverynoteitem_product_detail[]" class="form-control input-sm" placeholder="Detail"><?=$value->deliverynoteitem_product_detail?></textarea>
															</div>
														</td>
														<td>
                                                            <div class="input-group">
                                                                <input id="deliverynoteitem_quantity" name="deliverynoteitem_quantity[]" type="number" min="0" class="form-control input-sm" placeholder="Quantity" value="<?=($value->deliverynoteitem_quantity) ? $value->deliverynoteitem_quantity : '1'?>" />
                                                                <input id="deliverynoteitem_unit" name="deliverynoteitem_unit[]" type="hidden" value="<?=$value->deliverynoteitem_unit?>" />
                                                                <span class="input-group-addon deliverynoteitem_unit"><?=($value->deliverynoteitem_unit) ? $value->deliverynoteitem_unit : 'Unit'?></span>
                                                            </div>
                                                        </td>
													</tr>
													<?php } ?>
												</tbody>
												<tfoot>
													<tr>
														<th width="10%">
															<a class="btn btn-sm btn-primary deliverynoteitem-insert-btn" data-toggle="tooltip" title="Insert">
																<i class="glyphicon glyphicon-plus"></i>
															</a>
														</th>
														<th></th>
														<th>Quantity total</th>
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
											<label for="deliverynote_payment">Payment</label>
											<textarea id="deliverynote_payment" name="deliverynote_payment" class="form-control input-sm" placeholder="Payment" rows="3"><?=$deliverynote->deliverynote_payment?></textarea>
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

					<h2 class="col-sm-12">Delivery note management</h2>

					<div class="content-column-area col-md-12 col-sm-12">

						<div class="fieldset">
							<?=$this->session->tempdata('alert');?>
							<div class="search-area">

								<form deliverynote="form" method="get">
									<input type="hidden" name="deliverynote_id" />
									<table>
										<tbody>
											<tr>
												<td width="90%">
													<div class="row">
														<div class="col-sm-2"><h6>Delivery note</h6></div>
														<div class="col-sm-2">
															<input type="text" name="deliverynote_number_like" class="form-control input-sm" placeholder="DNNo" value="" />
														</div>
														<div class="col-sm-2">
															<span class="input-group date datetimepicker">
																<input id="deliverynote_create_greateq" name="deliverynote_create_greateq" type="text" class="form-control input-sm date-mask" placeholder="Date From (YYYY-MM-DD)" />
																<span class="input-group-addon">
																	<span class="glyphicon glyphicon-calendar"></span>
																</span>
															</span>
														</div>
														<div class="col-sm-2">
															<span class="input-group date datetimepicker">
																<input id="deliverynote_create_smalleq" name="deliverynote_create_smalleq" type="text" class="form-control input-sm date-mask" placeholder="Date To (YYYY-MM-DD)" />
																<span class="input-group-addon">
																	<span class="glyphicon glyphicon-calendar"></span>
																</span>
															</span>
														</div>
														<div class="col-sm-2">
															<input type="text" name="salesorder_number_like" class="form-control input-sm" placeholder="SONo" value="" />
														</div>
														<div class="col-sm-2">
															<select id="deliverynote_status" name="deliverynote_status" data-placeholder="Status" class="chosen-select">
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
															<input type="text" name="deliverynote_client_company_name_like" class="form-control input-sm" placeholder="Customer company name" value="" />
														</div>
														<div class="col-sm-2">
															<select id="deliverynote_user_id" name="deliverynote_user_id" data-placeholder="Sales" class="chosen-select">
																<option value></option>
																<?php foreach($users as $key => $value){ ?>
																<option value="<?=$value->user_id?>"><?=ucfirst($value->user_name)?></option>
																<?php } ?>
															</select>
														</div>
														<div class="col-sm-2">
															<!-- <input type="text" name="deliverynote_client_company_name_deliverynote_client_name_like" class="form-control input-sm" placeholder="Customer PO" value="" /> -->
														</div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
													</div>
													<div class="row">
														<div class="col-sm-2"><h6>Project</h6></div>
														<div class="col-sm-2">
															<input type="text" name="deliverynote_project_name_like" class="form-control input-sm" placeholder="Project Name" value="" />
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
												<th>Status</th>
												<th></th>
												<th></th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($deliverynotes as $key => $value){ ?>
											<tr>
												<td><?=$value->deliverynote_number?></td>
												<td><a href="<?=base_url('salesorder/update/salesorder_id/'.$value->deliverynote_salesorder_id)?>"><?=get_salesorder($value->deliverynote_salesorder_id)->salesorder_number?></a></td>
												<td><?=convert_datetime_to_date($value->deliverynote_create)?></td>
												<td><?=$value->deliverynote_client_company_name?></td>
												<td><?=$value->deliverynote_project_name?></td>
												<td><?=ucfirst(get_user($value->deliverynote_user_id)->user_name)?></td>
												<td><?=ucfirst($value->deliverynote_status)?></td>
												<td class="text-right">
													<a target="_blank" href="<?=base_url('/assets/images/pdf/deliverynote/'.$value->deliverynote_number.'.pdf')?>" data-toggle="tooltip" title="Print">
														<i class="glyphicon glyphicon-print"></i>
													</a>
												</td>
												<td class="text-right">
													<?php if(!check_permission('deliverynote_update', 'display')){ ?>
													<a href="<?=base_url('deliverynote/update/deliverynote_id/'.$value->deliverynote_id)?>" data-toggle="tooltip" title="Update">
														<i class="glyphicon glyphicon-edit"></i>
													</a>
													<?php }else{ ?>
													<i class="glyphicon glyphicon-edit"></i>
													<?php } ?>
												</td>
												<td class="text-right">
													<?php if(!check_permission('deliverynote_delete', 'display')){ ?>
													<a onclick="check_delete(<?=$value->deliverynote_id?>);" data-toggle="tooltip" title="Remove">
														<i class="glyphicon glyphicon-remove"></i>
													</a>
													<?php }else{ ?>
													<i class="glyphicon glyphicon-remove"></i>
													<?php } ?>
												</td>
											</tr>
											<?php } ?>

											<?php if(!$deliverynotes){ ?>
											<tr>
												<td colspan="10">No record found</td>
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
            <?php if(isset($popup_list) && !empty($popup_list)){ ?>
                <div class="popup-view">
                    <div class="popup-header"><a href="javascript:" class="popup-close">Close</a></div>
                    <div class="popup-list-area">
                        <form name="list">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>DN No</th>
                                    <th>Confirmed</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($popup_list as $key => $value){ ?>
                                    <tr>
                                        <td><a href="<?=base_url('deliverynote/update/deliverynote_id/'.$value->deliverynote_id)?>" data-toggle="tooltip" title="Update"><?=$value->deliverynote_number?></a></td>
                                        <td><?=$value->deliverynote_confirmed_date?></td>
                                        <td><?=ucfirst($value->deliverynote_status)?></td>
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