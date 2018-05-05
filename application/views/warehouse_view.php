<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Warehouse management</title>

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
		<script src="<?php echo base_url('assets/js/function.js'); ?>"></script>

		<script>
		$(function(){
			$('input[name="warehouse_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');
		});
		</script>
	</head>

	<body>

		<?php $this->load->view('inc/header-area.php'); ?>

		








































		<?php if($this->router->fetch_method() == 'update' || $this->router->fetch_method() == 'insert'){ ?>
		<div class="content-area">

			<div class="container-fluid">
				<div class="row">

					<h2 class="col-sm-12"><a href="<?=base_url('warehouse')?>">Warehouse management</a> > <?=($this->router->fetch_method() == 'update') ? 'Upate' : 'Insert'?> warehouse</h2>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="warehouse_id" value="<?=$warehouse->warehouse_id?>" />
							<input type="hidden" name="referrer" value="<?=$this->agent->referrer()?>" />
							<div class="fieldset">
								<div class="row">
									
									<div class="col-sm-4 col-xs-12 pull-right">
										<blockquote>
											<h4 class="corpcolor-font">Instructions</h4>
											<p><span class="highlight">*</span> is a required field</p>
										</blockquote>
									</div>
									<div class="col-sm-4 col-xs-12">
										<h4 class="corpcolor-font">Basic information</h4>
										<p class="form-group">
											<label for="warehouse_name">Name <span class="highlight">*</span></label>
											<input id="warehouse_name" name="warehouse_name" type="text" class="form-control input-sm required" placeholder="Name" value="<?=$warehouse->warehouse_name?>" />
										</p>
									</div>
									<div class="col-sm-4 col-xs-12">
										<h4 class="corpcolor-font">Related information</h4>
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

					<h2 class="col-sm-12">Warehouse management</h2>

					<div class="content-column-area col-md-12 col-sm-12">
						<div class="fieldset">

							<div class="search-area">

								<form warehouse="form" method="get">
									<input type="hidden" name="warehouse_id" />
									<table>
										<tbody>
											<tr>
												<td width="90%">
													<div class="row">
														<div class="col-sm-2"><h6>Product</h6></div>
														<div class="col-sm-2">
															<input type="text" name="product_code_like" class="form-control input-sm" placeholder="Product code" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="product_name_like" class="form-control input-sm" placeholder="Product name" value="" />
														</div>
														<div class="col-sm-2">
															<select id="status" name="status" data-placeholder="Status" class="chosen-select">
																<option value></option>
																<?php
																foreach($statuss as $key => $value){
																	$selected = ($value->status_name == $this->uri->uri_to_assoc()['status']) ? ' selected="selected"' : "" ;
																	echo '<option value="'.$value->status_name.'"'.$selected.'>'.ucfirst($value->status_name).'</option>';
																}
																?>
															</select>
														</div>
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
								<form name="list">
									<div class="page-area">
										<span class="btn btn-sm btn-default"><?php print_r($num_rows); ?></span>
										<?=$this->pagination->create_links()?>
									</div>
									<table id="warehouse" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>#</th>
												<th>
													<a href="<?=get_order_link('product_code')?>">
														Code <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<th>
													<a href="<?=get_order_link('product_name')?>">
														Name <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<?php foreach($warehouses as $key1 => $value1){ ?>
												<th>
													<a href="<?=base_url('warehouse/update/warehouse_id/'.$value1->warehouse_id)?>" data-toggle="tooltip" title="Update">
														<i class="glyphicon glyphicon-edit"></i>
													</a>
													<?=$value1->warehouse_name?>
												</th>
												<?php } ?>
												<th width="40" class="text-right">
													<?php if(!check_permission('warehouse_insert', 'display')){ ?>
													<a href="<?=base_url('warehouse/insert')?>" data-toggle="tooltip" title="Insert">
														<i class="glyphicon glyphicon-plus"></i>
													</a>
													<?php }else{ ?>
													<i class="glyphicon glyphicon-plus"></i>
													<?php } ?>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($products as $key => $value){ ?>
											<tr>
												<td title="<?=$value->product_id?>"><?=$key+1?></td>
												<td><?=$value->product_code?></td>
												<td>
													<a href="<?=base_url('exchange/select/exchange_product_id/'.$value->product_id)?>" data-toggle="tooltip" title="View exchange by product">
														<i class="glyphicon glyphicon-chevron-right"></i>
													</a>
													<?=ucfirst($value->product_name)?>
												</td>
												<?php
												$thisSubtotal = 0;
												foreach($warehouses as $key1 => $value1){
												?>
												<td>
													<a href="<?=base_url('exchange/insert/exchange_type/in/product_id/'.$value->product_id.'/warehouse_id/'.$value1->warehouse_id)?>" data-toggle="tooltip" title="Stock In">
														<i class="glyphicon glyphicon-log-in"></i>
													</a>
													<?php if(count($warehouses) > 1){ ?>
													|
													<a href="<?=base_url('exchange/insert/exchange_type/transfer/product_id/'.$value->product_id.'/warehouse_id/'.$value1->warehouse_id)?>" data-toggle="tooltip" title="Stock Transfer">
														<i class="glyphicon glyphicon-new-window"></i>
													</a>
													<?php } ?>
													|
													<a href="<?=base_url('exchange/insert/exchange_type/out/product_id/'.$value->product_id.'/warehouse_id/'.$value1->warehouse_id)?>" data-toggle="tooltip" title="Stock Out">
														<i class="glyphicon glyphicon-log-out"></i>
													</a>
													<?php
													$thisSubtotal += $thisQuantity = get_z_product_warehouse_quantity($value->product_id, $value1->warehouse_id);
													if($thisQuantity){
														echo $thisQuantity;
													}else{
														echo 0;
													}
													?>
												</td>
												<?php } ?>
												<td class="text-right"><?=$thisSubtotal?></td>
											</tr>
											<?php } ?>

											<?php if(!$products){ ?>
											<tr>
												<td colspan="8">No record found</td>
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
										<i class="glyphicon glyphicon-unchecked"></i>
										<a href="<?=base_url('warehouse')?>">Warehouse</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-log-in"></i>
										<a href="<?=base_url('exchange/select/exchange_type/in')?>">Stock In</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-new-window"></i>
										<a href="<?=base_url('exchange/select/exchange_type/transfer')?>">Stock Transfer</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-log-out"></i>
										<a href="<?=base_url('exchange/select/exchange_type/out')?>">Stock Out</a>
									</blockquote>
								</div>
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

<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
	<div class="modal-dialog">

		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<i class="glyphicon glyphicon-remove"></i>
				</button>
				<h4 class="modal-title corpcolor-font">Detail</h4>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
			</div>
		</div>

	</div>
</div>
<!-- myModal -->