<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Commission report</title>

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

			$('input[name^="commissionStatus-"]').click(function(){
				var answer = prompt("Please insert remark");
				if(answer){
					thisId = $(this).attr('name').replace('commissionStatus-', '');
					$('input[name="salesorder_id"]').val(thisId);
					$('input[name="salesorder_commission_status"]').val('complete');
					$('input[name="salesorder_commission_remark"]').val(encodeURI(answer));
					$('form[name="list"]').submit();
				}else{
					return false;
				}
			});
		});

		function insert_salesorder_commission_remark(id){
			// if(this.checked)
				var answer = prompt("Please insert remark");
				if(answer){
					// $('input[name="salesorder_id"]').val(id);
					// $('input[name="salesorder_delete_reason"]').val(encodeURI(answer));
					// $('form[name="list"]').submit();
				}else{
					return false;
				}
		}
		</script>
	</head>

	<body>

		<?php $this->load->view('inc/header-area.php'); ?>

		








































		<?php if($this->router->fetch_method() == 'update' || $this->router->fetch_method() == 'insert'){ ?>
		<?php } ?>

		











































		<?php if($this->router->fetch_method() == 'select'){ ?>
		<div class="content-area">

			<div class="container-fluid">
				<div class="row">

					<h2 class="col-sm-12">Commissoin</h2>

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
														<div class="col-sm-2">
															<span class="input-group date datetimepicker">
																<input id="salesorder_create_greateq" name="salesorder_create_greateq" type="text" class="form-control input-sm date-mask" placeholder="Date From (YYYY-MM-DD)" />
																<span class="input-group-addon">
																	<span class="glyphicon glyphicon-calendar"></span>
																</span>
															</span>
														</div>
														<div class="col-sm-2">
															<span class="input-group date datetimepicker">
																<input id="salesorder_create_smalleq" name="salesorder_create_smalleq" type="text" class="form-control input-sm date-mask" placeholder="Date To (YYYY-MM-DD)" />
																<span class="input-group-addon">
																	<span class="glyphicon glyphicon-calendar"></span>
																</span>
															</span>
														</div>
														<div class="col-sm-2">
															<input type="text" name="quotation_number_like" class="form-control input-sm" placeholder="QONo" value="" />
														</div>
														<div class="col-sm-2">
															<select id="salesorder_commission_status" name="salesorder_commission_status" data-placeholder="Status" class="chosen-select">
																<?php
																foreach($statuss as $key => $value){
																	$selected = ($value->status_name == $this->uri->uri_to_assoc()['salesorder_commission_status']) ? ' selected="selected"' : "" ;
																	echo '<option value="'.$value->status_name.'"'.$selected.'>'.ucfirst($value->status_name).'</option>';
																}
																?>
															</select>
														</div>
													</div>
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
														<div class="col-sm-2"></div>
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
													<!-- <div class="row">
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
													</div> -->
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
								<form name="list" action="<?=base_url('commission/update')?>" method="post">
									<input type="hidden" name="salesorder_id" />
									<!-- <input type="hidden" name="salesorder_delete_reason" /> -->
									<input type="hidden" name="salesorder_commission_status" />
									<input type="hidden" name="salesorder_commission_remark" />
									<div class="page-area">
										<span class="btn btn-sm btn-default"><?php print_r($num_rows); ?></span>
										<?=$this->pagination->create_links()?>
									</div>
									<table class="table table-striped table-bordered">
										<thead>
											<tr>
												<th></th>
												<th>SO No</th>
												<th>QO No</th>
												<th>PO No</th>
												<th>Create</th>
												<th>Customer</th>
												<th>Project</th>
												<th>Cost</th>
												<th>Total</th>
												<th>Status</th>
												<th>GP</th>
												<th>Commission rate</th>
												<th>Commission</th>
												<th>Commission to</th>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach($salesorders as $key => $value){
											$salesorder_cost = get_salesorder_cost($value->salesorder_id);
											?>
											<tr>
												<td><input name="commissionStatus-<?=$value->salesorder_id?>" type="checkbox" /></td>
												<td><a href="<?=base_url('salesorder/update')?>"><?=$value->salesorder_number?></a></td>
												<td><a href="<?=base_url('quotation/update/quotation_id/'.$value->salesorder_quotation_id)?>"><?=get_quotation($value->salesorder_quotation_id)->quotation_number?></a></td>
												<td>
													<?php foreach($value->purchaseorders as $key1 => $value1){ ?>
													<div><a href="<?=base_url('purchaseorder/update/purchaseorder_id/'.$value1->purchaseorder_id)?>"><?=$value1->purchaseorder_number?></a></div>
													<?php } ?>
												</td>
												<td><?=convert_datetime_to_date($value->salesorder_create)?></td>
												<td><?=$value->salesorder_client_company_name?></td>
												<td><?=$value->salesorder_project_name?></td>
												<td><?=strtoupper($value->salesorder_currency).' '.money_format('%!n', $salesorder_cost)?></td>
												<td><?=strtoupper($value->salesorder_currency).' '.money_format('%!n', $value->salesorder_total)?></td>
												<td><?=ucfirst($value->salesorder_status)?></td>
												<td><?=((ucfirst($value->salesorder_status) == 'Complete') ? '<span class="corpcolor-font">GP</span>' : '<span class="corpcolor-font">Est</span>').' '.strtoupper($value->salesorder_currency).' '.money_format('%!n', $value->salesorder_total - $salesorder_cost)?></td>
												<td><?=$value->salesorder_commission_rate?>%</td>
												<td><?=strtoupper($value->salesorder_currency).' '.money_format('%!n', $value->salesorder_total * 8 / 100)?></td>
												<td><?=ucfirst(get_user($value->salesorder_quotation_user_id)->user_name)?></td>
											</tr>
											<?php } ?>

											<?php if(!$salesorders){ ?>
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
						<p>Add processing and complete status filter</p>
						<p>"Complete" commission will not show in this list</p>
					</div>
				</div>
			</div>

		</div>
		<?php } ?>












































		<?php $this->load->view('inc/footer-area.php'); ?>

	</body>
</html>

<div class="scriptLoader"></div>