<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Expenses report</title>

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
		});
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

					<h2 class="col-sm-12">Expenses report</h2>

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
															<input type="text" name="purchaseorder_number_like" class="form-control input-sm" placeholder="INNo" value="" />
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
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
													</div>
													<div class="row">
														<div class="col-sm-2"><h6>Customer</h6></div>
														<div class="col-sm-2">
															<input type="text" name="purchaseorder_client_company_name_like" class="form-control input-sm" placeholder="Customer company name" value="" />
														</div>
														<div class="col-sm-2">
															<select id="purchaseorder_user_id" name="purchaseorder_user_id" data-placeholder="Sales" class="chosen-select">
																<option value></option>
																<?php foreach($users as $key => $value){ ?>
																<option value="<?=$value->user_id?>"><?=ucfirst($value->user_name)?></option>
																<?php } ?>
															</select>
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
												<th>Customer</th>
												<th>PO No</th>
												<th>Deadline</th>
												<th>Total</th>
												<th>0-30</th>
												<th>31-60</th>
												<th>61-90</th>
												<th>90+</th>
												<th>PO date</th>
												<th>Sales</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$purchaseorder_total = 0;
											$purchaseorder_total_smalleq30 = 0;
											$purchaseorder_total_from31to60 = 0;
											$purchaseorder_total_from61to90 = 0;
											$purchaseorder_total_largereq91 = 0;
											foreach($purchaseorders as $key => $value){
											?>
											<tr>
												<td><?=$value->purchaseorder_vendor_company_name?></td>
												<td><a href="<?=base_url('purchaseorder/select/purchaseorder_id/'.$value->purchaseorder_id)?>"><?=$value->purchaseorder_number?></a></td>
												<td><?=$value->purchaseorder_expire?></td>
												<td>
													<?php
													$purchaseorder_total += $value->purchaseorder_total;
													echo strtoupper($value->purchaseorder_currency).' '.money_format('%!n', $value->purchaseorder_total);
													?>
												</td>
												<td>
													<?php
													if(get_expire_period($value->purchaseorder_expire) == '<=30'){
														$purchaseorder_total_smalleq30 += $value->purchaseorder_total;
														echo strtoupper($value->purchaseorder_currency).' '.money_format('%!n', $value->purchaseorder_total);
													}else{
														echo '- - -';
													}
													?>
												</td>
												<td>
													<?php
													if(get_expire_period($value->purchaseorder_expire) == '31-60'){
														$purchaseorder_total_from31to60 += $value->purchaseorder_total;
														echo strtoupper($value->purchaseorder_currency).' '.money_format('%!n', $value->purchaseorder_total);
													}else{
														echo '- - -';
													}
													?>
												</td>
												<td>
													<?php
													if(get_expire_period($value->purchaseorder_expire) == '61-90'){
														$purchaseorder_total_from61to90 += $value->purchaseorder_total;
														echo strtoupper($value->purchaseorder_currency).' '.money_format('%!n', $value->purchaseorder_total);
													}else{
														echo '- - -';
													}
													?>
												</td>
												<td>
													<?php
													if(get_expire_period($value->purchaseorder_expire) == '>=91'){
														$purchaseorder_total_largereq91 += $value->purchaseorder_total;
														echo strtoupper($value->purchaseorder_currency).' '.money_format('%!n', $value->purchaseorder_total);
													}else{
														echo '- - -';
													}
													?>
												</td>
												<td><?=convert_datetime_to_date($value->purchaseorder_create)?></td>
												<td><?=get_user($value->purchaseorder_quotation_user_id)->user_name?></td>

											</tr>
											<?php
											// $purchaseorder_total += $value->purchaseorder_total;
											// $purchaseorder_total += $purchaseorder_subtotal;
											}
											?>

											<?php if(!$purchaseorders){ ?>
											<tr>
												<td colspan="10">No record found</td>
											</tr>
											<?php } ?>
										</tbody>
										<?php if($purchaseorders){ ?>
										<tfoot>
											<tr>
												<th></th>
												<th></th>
												<th></th>
												<th><?=strtoupper($value->purchaseorder_currency).' '.money_format('%!n', $purchaseorder_total)?></th>
												<th><?=strtoupper($value->purchaseorder_currency).' '.money_format('%!n', $purchaseorder_total_smalleq30)?></th>
												<th><?=strtoupper($value->purchaseorder_currency).' '.money_format('%!n', $purchaseorder_total_from31to60)?></th>
												<th><?=strtoupper($value->purchaseorder_currency).' '.money_format('%!n', $purchaseorder_total_from61to90)?></th>
												<th><?=strtoupper($value->purchaseorder_currency).' '.money_format('%!n', $purchaseorder_total_largereq91)?></th>
												<th></th>
												<th></th>
											</tr>
										</tfoot>
										<?php } ?>
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
						<!-- <p>Is it <b>completed</b> SO show in this report?</p>
						<p>Customer PO ?</p> -->
					</div>
				</div>
			</div>

		</div>
		<?php } ?>












































		<?php $this->load->view('inc/footer-area.php'); ?>

	</body>
</html>

<div class="scriptLoader"></div>