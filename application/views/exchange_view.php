<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Exchange management</title>

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
			$('input[name="exchange_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');

			/* textarea auto height */
			textarea_auto_height();
			$(document).on('keyup', 'textarea', function(){
				textarea_auto_height();
			});

			if($.validator){
			//fix: when several input elements shares the same name, but has different id-ies....
				$.validator.prototype.elements = function(){
					var validator = this,
					rulesCache = {};
					// select all valid inputs inside the form (no submit or reset buttons)
					// workaround $Query([]).add until http://dev.jquery.com/ticket/2114 is solved
					return $([]).add(this.currentForm.elements).filter(":input").not(":submit, :reset, :image, [disabled]").not(this.settings.ignore).filter(function() {
					// 这里加入ID判断
					var elementIdentification = this.id || this.name; ! elementIdentification && validator.settings.debug && window.console && console.error("%o has no id nor name assigned", this);
					// select only the first element for each name, and only those with rules specified
					if (elementIdentification in rulesCache || !validator.objectLength($(this).rules())) return false;
					rulesCache[elementIdentification] = true;
					return true;
					});
				};
			}
		});

		function check_delete(id){
			var answer = prompt("Confirm delete?");
			if(answer){
				$('input[name="exchange_id"]').val(id);
				$('input[name="exchange_delete_reason"]').val(encodeURI(answer));
				$('form[name="list"]').submit();
			}else{
				return false;
			}
		}

		function textarea_auto_height(){
			$.each($('textarea'), function(key, val){
				$(this).attr('rows', $(this).val().split('\n').length + 1);
			});
		}

		function login_as(id){
			$('input[name="exchange_id"]').val(id);
			$('input[name="act"]').val('login_as');
			$('form[name="list"]').submit();
		}
		</script>
	</head>

	<body>

		<?php $this->load->view('inc/header-area.php'); ?>

		








































		<?php if($this->router->fetch_method() == 'update' || $this->router->fetch_method() == 'insert'){ ?>
		<div class="content-area">

			<div class="container-fluid">
				<div class="row">

					<h2 class="col-sm-12"><a href="<?=base_url('exchange')?>">Exchange management</a> > <?=($this->router->fetch_method() == 'update') ? 'Upate' : 'Insert'?> Stock <?=ucfirst($this->uri->uri_to_assoc()['exchange_type'])?></h2>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="exchange_id" value="<?=$exchange->exchange_id?>" />
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
											<label for="exchange_type">Type</label>
											<input readonly="readonly" type="text" class="form-control input-sm" placeholder="Type" value="Stock <?=ucfirst($this->uri->uri_to_assoc()['exchange_type'])?>" />
											<input id="exchange_type" name="exchange_type" type="hidden" value="<?=$this->uri->uri_to_assoc()['exchange_type']?>" />
										</p>
										<p class="form-group">
											<label for="exchange_product_id">Product</label>
											<input readonly="readonly" type="text" class="form-control input-sm" placeholder="Product" value="<?=get_product($this->uri->uri_to_assoc()['product_id'])->product_name?>" />
											<input id="exchange_product_id" name="exchange_product_id" type="hidden" value="<?=$this->uri->uri_to_assoc()['product_id']?>" />
										</p>
										<?php
										switch($this->uri->uri_to_assoc()['exchange_type']){
											case 'in':
												echo '<p class="form-group">';
												echo '<label for="exchange_warehouse_id_from">Warehouse from</label>';
												echo '<input readonly="readonly" type="text" class="form-control input-sm" placeholder="Type" value="Other" />';
												echo '<input id="exchange_warehouse_id_from" name="exchange_warehouse_id_from" type="hidden" value="0" />';
												echo '</p>';
												break;
											case 'transfer':
											case 'out':
												echo '<p class="form-group">';
												echo '<label for="exchange_warehouse_id_from">Warehouse from</label>';
												echo '<select id="exchange_warehouse_id_from" name="exchange_warehouse_id_from" data-placeholder="Warehouse from" class="chosen-select required">';
												echo '<option value></option>';
												foreach($warehousefroms as $key => $value){
													$selected = ($value->warehouse_id == $this->uri->uri_to_assoc()['warehouse_id']) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value->warehouse_id.'"'.$selected.'>'.strtoupper($value->warehouse_name).'</option>';
												}
												echo '</select>';
												echo '</p>';
												break;
										}
										?>
										<?php
										switch($this->uri->uri_to_assoc()['exchange_type']){
											case 'in':
											case 'transfer':
												echo '<p class="form-group">';
												echo '<label for="exchange_warehouse_id_to">Warehouse to</label>';
												echo '<select id="exchange_warehouse_id_to" name="exchange_warehouse_id_to" data-placeholder="Warehouse to" class="chosen-select required">';
												echo '<option value></option>';
												foreach($warehousetos as $key => $value){
													$selected = ($value->warehouse_id == $this->uri->uri_to_assoc()['warehouse_id']) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value->warehouse_id.'"'.$selected.'>'.strtoupper($value->warehouse_name).'</option>';
												}
												echo '</select>';
												echo '</p>';
												break;
											case 'out':
												echo '<p class="form-group">';
												echo '<label for="exchange_warehouse_id_to">Warehouse to</label>';
												echo '<input readonly="readonly" type="text" class="form-control input-sm" placeholder="Type" value="Other" />';
												echo '<input id="exchange_warehouse_id_to" name="exchange_warehouse_id_to" type="hidden" value="0" />';
												echo '</p>';
												break;
										}
										?>
										<p class="form-group">
											<label for="exchange_quantity">Quantity <span class="highlight">*</span></label>
											<input id="exchange_quantity" name="exchange_quantity" type="text" class="form-control input-sm required" placeholder="Quantity" value="" />
										</p>
									</div>
									<div class="col-sm-4 col-xs-12">
										<h4 class="corpcolor-font">Related information</h4>
										<p class="form-group">
											<label for="exchange_remark">Remark <span class="highlight"></span></label>
											<textarea id="exchange_remark" name="exchange_remark" class="form-control input-sm" placeholder="Remark" rows="5"></textarea>
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

		
























		<?php if($this->router->fetch_method() == 'batchinsert'){ ?>
		<div class="content-area">

			<div class="container-fluid">
				<div class="row">

					<h2 class="col-sm-12"><a href="<?=base_url('exchange')?>">Exchange management</a> > <?=($this->router->fetch_method() == 'update') ? 'Upate' : 'Insert'?> Stock Batch In</h2>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="exchange_id" value="" />
							<input type="hidden" name="referrer" value="<?=$this->agent->referrer()?>" />
							<div class="fieldset">
								<div class="row">
									<div class="col-sm-4 col-xs-12 pull-right">
										<blockquote>
											<h4 class="corpcolor-font">Instructions</h4>
											<p><span class="highlight">*</span> is a required field</p>
										</blockquote>
									</div>
									<div class="col-sm-8 col-xs-12">
										<h4 class="corpcolor-font">Basic information</h4>
										<p class="form-group">
											<label for="exchange_type">Type</label>
											<input readonly="readonly" type="text" class="form-control input-sm" placeholder="Type" value="Stock In" />
											<input id="exchange_type" name="exchange_type" type="hidden" value="in" />
										</p>
										<p class="form-group">
											<label for="exchange_warehouse_id_from">Warehouse from</label>
											<input readonly="readonly" type="text" class="form-control input-sm" placeholder="Type" value="Other" />
											<input id="exchange_warehouse_id_from" name="exchange_warehouse_id_from" type="hidden" value="0" />
										</p>
										<?php
										foreach($product_ids as $key => $value){
										?>
										<div class="row">
											<div class="col-sm-3 col-xs-12">
												<p class="form-group">
													<label for="exchange_product_id">Product</label>
													<input readonly="readonly" type="text" class="form-control input-sm" placeholder="Product" value="<?=get_product($value)->product_name?>" />
													<input id="exchange_product_id" name="exchange_product_id[]" type="hidden" value="<?=$value?>" />
												</p>
											</div>
											<div class="col-sm-3 col-xs-12">
												<p class="form-group">
													<label for="exchange_warehouse_id_to">Warehouse to <span class="highlight">*</span></label>
													<select id="exchange_warehouse_id_to_<?=$value?>" name="exchange_warehouse_id_to[]" data-placeholder="Warehouse to" class="chosen-select required">
													<option value></option>
													<?php
														foreach($warehousetos as $k => $v){
															// $selected = ($v->warehouse_id == $this->uri->uri_to_assoc()['warehouse_id']) ? ' selected="selected"' : "" ;
															$selected = '';
															echo '<option value="'.$v->warehouse_id.'"'.$selected.'>'.strtoupper($v->warehouse_name).'</option>';
														}
													?>
													</select>
												</p>
											</div>
											<div class="col-sm-3 col-xs-12">
												<p class="form-group">
													<label for="exchange_quantity">Quantity <span class="highlight">*</span></label>
													<input id="exchange_quantity_<?=$value?>" name="exchange_quantity[]" type="number" min="0" step="0.01" class="form-control input-sm required" placeholder="Quantity" value="<?=$product_qtys[$key]?>" />
												</p>
											</div>
											<div class="col-sm-3 col-xs-12 pull-right">
												<p class="form-group">
													<label for="exchange_remark">Remark <span class="highlight"></span></label>
													<input id="exchange_remark" name="exchange_remark[]" class="form-control input-sm" placeholder="Remark" />
												</p>
											</div>
										</div>
										<?php
										}
										?>
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

					<h2 class="col-sm-12">Exchange management</h2>

					<div class="content-column-area col-md-12 col-sm-12">
						<div class="fieldset">
							<div class="search-area">

								<form exchange="form" method="get">
									<input type="hidden" name="exchange_id" />
									<table>
										<tbody>
											<tr>
												<td width="90%">
													<div class="row">
														<div class="col-sm-2"><h6>Exchange</h6></div>
														<div class="col-sm-2">
															<select id="exchange_type" name="exchange_type" data-placeholder="Type" class="chosen-select">
																<?php
																foreach($types as $key => $value){
																	$selected = ($value->type_name == $this->uri->uri_to_assoc()['exchange_type']) ? ' selected="selected"' : "" ;
																	echo '<option value="'.$value->type_name.'"'.$selected.'>Stock '.$value->type_name.'</option>';
																}
																?>
															</select>
														</div>
														<div class="col-sm-2">
															<select id="exchange_product_id" name="exchange_product_id" data-placeholder="Product" class="chosen-select">
																<option value></option>
																<?php foreach($exchange_product_ids as $key => $value){ ?>
																<option value="<?=$value->exchange_product_id?>">
																<?php
																$thisProduct = get_product($value->exchange_product_id);
																echo $thisProduct->product_code.' - '.$thisProduct->product_name;
																?>
																</option>
																<?php } ?>
															</select>
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
								<form name="list" action="<?=base_url('exchange/delete')?>" method="post">
									<input type="hidden" name="exchange_id" />
									<input type="hidden" name="exchange_delete_reason" />
									<div class="page-area">
										<span class="btn btn-sm btn-default"><?php print_r($num_rows); ?></span>
										<?=$this->pagination->create_links()?>
									</div>
									<table id="exchange" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>#</th>
												<th>Type</th>
												<th>Product</th>
												<th>Exchange</th>
												<th>Quantity</th>
												<th>Remark</th>
												<th>Handler</th>
												<th>Create</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($exchanges as $key => $value){ ?>
											<tr>
												<td title="<?=$value->exchange_id?>"><?=$key+1?></td>
												<td><?='Stock '.$value->exchange_type?></td>
												<td>
													<a href="<?=base_url('warehouse/select/product_id/'.$value->exchange_product_id)?>" data-toggle="tooltip" title="Warehouse">
														<i class="glyphicon glyphicon-unchecked"></i>
													</a>
													<?php
													$thisProduct = get_product($value->exchange_product_id);
													echo $thisProduct->product_code.' - '.$thisProduct->product_name;
													?>
												</td>
												<td>
													<?php
													if(!empty(get_warehouse($value->exchange_warehouse_id_from))){
														echo get_warehouse($value->exchange_warehouse_id_from)->warehouse_name;
													}else{
														echo 'Other';
													}
													?>
													<i class="glyphicon glyphicon-chevron-right"></i><i class="glyphicon glyphicon-chevron-right"></i><i class="glyphicon glyphicon-chevron-right"></i>
													<?php
													if(!empty(get_warehouse($value->exchange_warehouse_id_to))){
														echo get_warehouse($value->exchange_warehouse_id_to)->warehouse_name;
													}else{
														echo 'Other';
													}
													?>
												</td>
												<td><?=$value->exchange_quantity?></td>
												<td><?=$value->exchange_remark?></td>
												<td><?=get_user($value->exchange_user_id)->user_name?></td>
												<td><?=$value->exchange_create?></td>
											</tr>
											<?php } ?>

											<?php if(!$exchanges){ ?>
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