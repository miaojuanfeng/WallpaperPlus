<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Stock status management</title>

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

			/* purchaseorderitem-insert-btn */
			$(document).on('click', '.purchaseorderitem-insert-btn', function(){
				add_purchaseorderitem_row();
			});

			/* purchaseordersubitem-insert-btn */
			$(document).on('click', '.purchaseordersubitem-insert-btn', function(){
				add_purchaseordersubitem_row($(this).parent().parent().parent().parent().index());
			});

			/* purchaseorderitem-delete-btn */
			$(document).on('click', '.purchaseorderitem-delete-btn', function(){
				if(confirm('Confirm delete?')){
					$(this).closest('tr').remove();
					calc();
				}else{
					return false;
				}
			});

			/* vendor loader */
			<?php if($this->router->fetch_method() == 'insert' && isset($this->uri->uri_to_assoc()['purchaseorder_vendor_id'])){ ?>
			vendor_loader();
			<?php } ?>
			$(document).on('change', 'select[name="purchaseorder_vendor_id"]', function(){
				vendor_loader();
			});

			/* salesorder loader */
			<?php if($this->router->fetch_method() == 'insert'){ ?>
			$(document).on('change', 'select[name="purchaseorder_salesorder_id"]', function(){
				if(confirm('Confirm to bundle this SO')){
					window.location = "<?php echo base_url('/purchaseorder/insert/salesorder_id'); ?>" + '/' + $(this).val();
				}else{
					$('select[name="purchaseorder_salesorder_id"]').val('').trigger("chosen:updated");
				}
			});
			<?php } ?>

			/* salesorder loader */
			$(document).on('change', 'select[name="purchaseorder_salesorder_id"]', function(){
				salesorder_loader();
			});

			/* product loader */
			$(document).on('change', 'select[name="purchaseorderitem_product_id[]"]', function(){
				product_loader($(this));
			});

			/* trigger calc */
			$(document).on('blur', 'input[name="purchaseorderitem_product_price[]"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="purchaseorderitem_quantity[]"]', function(){
				calc();
			});
            $(document).on('blur', 'input[name="purchaseorderitem_discount[]"]', function(){
                calc();
            });
			$(document).on('blur', 'input[name="purchaseorder_discount"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="purchaseorder_pay"]', function(){
				calc();
			});
			$(document).on('change', 'input[name="purchaseorder_currency"]', function(){
				$.each($('select[name="purchaseorderitem_product_id[]"]'), function(key, val){
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
			    var subtotal = parseFloat($(this).find('input[name="purchaseorderitem_product_price[]"]').val()) * parseInt($(this).find('input[name="purchaseorderitem_quantity[]"]').val());
                var discount = parseFloat($(this).find('input[name="purchaseorderitem_discount[]"]').val())/100;
                discount = discount?discount:1;
                subtotal = subtotal?subtotal:0;
				$(this).find('input[name="purchaseorderitem_subtotal[]"]').val((subtotal*discount).toFixed(2)).css('display', 'none').fadeIn();
				total += parseFloat($(this).find('input[name="purchaseorderitem_subtotal[]"]').val());
			});
			$('input[name="purchaseorder_total"]').val(parseFloat(total).toFixed(2));
		}

		function check_delete(id){
			var answer = prompt("Confirm delete?");
			if(answer){
				$('input[name="purchaseorder_id"]').val(id);
				$('input[name="purchaseorder_delete_reason"]').val(encodeURI(answer));
				$('form[name="list"]').submit();
			}else{
				return false;
			}
		}

		function vendor_loader(){
			$('.scriptLoader').load('/load', {'thisTableId': 'vendorLoader', 'thisRecordId': $('select[name="purchaseorder_vendor_id"]').val(), 't': timestamp()}, function(){
				vendorLoader();
			});
		}

		function salesorder_loader(){
			$('.scriptLoader').load('/load', {'thisTableId': 'salesorderLoader', 'thisRecordId': $('select[name="purchaseorder_salesorder_id"]').val(), 't': timestamp()}, function(){
				salesorderLoader();
			});
		}

		function product_loader(thisObject){
			thisRow = $(thisObject).closest('tr').index();
			thisCurrency = $('select[name="purchaseorder_currency"]').val();
			$('.scriptLoader').load('/load', {'thisTableId': 'purchaseorderProductLoader', 'thisRecordId': $(thisObject).val(), 'thisCurrency': thisCurrency, 'thisRow': thisRow, 't': timestamp()}, function(){
				purchaseorderProductLoader();
				textarea_auto_height();
				calc();
			});
		}

		function textarea_auto_height(){
			$.each($('textarea'), function(key, val){
				$(this).attr('rows', $(this).val().split('\n').length + 1);
			});
		}
		</script>
	</head>

	<body>

		<?php $this->load->view('inc/header-area.php'); ?>

		








































		<?php if($this->router->fetch_method() == 'update' || $this->router->fetch_method() == 'insert'){ ?>
		<div class="content-area">

			<div class="container-fluid">
				<div class="row">

					<h2 class="col-sm-12"><a href="<?=base_url('purchaseorder')?>">Purchase order management</a> > <?=($this->router->fetch_method() == 'update') ? 'Update' : 'Insert'?> stock status</h2>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="purchaseorder_id" value="<?=$purchaseorder->purchaseorder_id?>" />
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
										<h4 class="corpcolor-font">Setting</h4>
										<p class="form-group">
											<label for="purchaseorder_arrive_status">Stock status</label>
											<select id="purchaseorder_arrive_status" name="purchaseorder_arrive_status" data-placeholder="Status" class="chosen-select required">
												<option value></option>
												<?php
												foreach($statuss as $key => $value){
													$selected = ($value->status_name == $purchaseorder->purchaseorder_arrive_status) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value->status_name.'"'.$selected.'>'.strtoupper($value->status_name).'</option>';
												}
												?>
											</select>
										</p>
									</div>
									<div class="col-sm-9 col-xs-12">
										<h4 class="corpcolor-font">Stock status</h4>
										<div class="row">
											<div class="col-sm-6 col-xs-6">
											</div>
											<div class="col-sm-1 col-xs-1">
											</div>
											<div class="col-sm-5 col-xs-5">
											</div>
										</div>
										<div class="list-area">
											<table class="table list" id="purchaseorder">
												<thead>
													<tr>
														<th width="10%"></th>
														<th>Detail</th>
														<th width="12%">Quantity</th>
														<th width="12%">Arrived</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($purchaseorderitems as $key => $value){ ?>
													<tr<?=($value->purchaseorderitem_type == 'sub item') ? ' class="subitem-row"' : ''?>>
														<td>
															<div>
																<input name="purchaseorderitem_id[]" type="hidden" value="<?=$value->purchaseorderitem_id?>" />
																<input name="purchaseorderitem_purchaseorder_id[]" type="hidden" value="<?=$value->purchaseorderitem_purchaseorder_id?>" />
																<input name="purchaseorderitem_type[]" type="hidden" value="<?=($value->purchaseorderitem_type != '') ? $value->purchaseorderitem_type : 'main item'?>" />
																<input name="purchaseorderitem_product_type_name[]" type="hidden" value="<?=$value->purchaseorderitem_product_type_name?>" />
																<input readonly="readonly" id="purchaseorderitem_product_code" name="purchaseorderitem_product_code[]" type="text" class="form-control input-sm" placeholder="Code" value="<?=$value->purchaseorderitem_product_code?>" />
															</div>
														</td>
														<td>
															<div>
																<div class="input-group">
																	<span class="input-group-addon corpcolor-font">Title</span>
																	<input readonly="readonly" id="purchaseorderitem_product_name" name="purchaseorderitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="<?=$value->purchaseorderitem_product_name?>" />
																</div>
															</div>
															<div>
																<textarea readonly="readonly" id="purchaseorderitem_product_detail" name="purchaseorderitem_product_detail[]" class="form-control input-sm" placeholder="Detail"><?=$value->purchaseorderitem_product_detail?></textarea>
															</div>
														</td>
														<td>
															<?php
															/* get salesorder quantity */
															$salesorder_quantity = get_salesorderitem_quantity($purchaseorder->purchaseorder_salesorder_id, $value->purchaseorderitem_product_id);

															if($purchaseorder->purchaseorder_salesorder_id != ''){
																/* get purchaseorder bought */
																$purchaseorderitem_bought = get_purchaseorderitem_issued_quantity($purchaseorder->purchaseorder_salesorder_id, $purchaseorder->purchaseorder_id, $value->purchaseorderitem_product_id);
																if(is_null($purchaseorderitem_bought)){
																	$purchaseorderitem_bought = 0;
																}
																if($purchaseorder->purchaseorder_id > 0){
																	$thisPurchaseorderitemQuantity = $value->purchaseorderitem_quantity;	
																}else{
																	$thisPurchaseorderitemQuantity = $salesorder_quantity - $purchaseorderitem_bought;
																}
															}else{
																/* get purchaseorder bought */
																$purchaseorderitem_bought = 0;
																$thisPurchaseorderitemQuantity = $salesorder_quantity - $purchaseorderitem_bought;
															}
															?>
															<input readonly="readonly" id="purchaseorderitem_quantity" name="purchaseorderitem_quantity[]" type="number" min="0" class="form-control input-sm" placeholder="Quantity" value="<?=$thisPurchaseorderitemQuantity?>" />
														</td>
														<td>
															<div>
																<input id="purchaseorderitem_stock_arrive" name="purchaseorderitem_stock_arrive[]" type="number" min="0" class="form-control input-sm" placeholder="Arrived" value="<?=$value->purchaseorderitem_stock_arrive?>" />
															</div>
														</td>
													</tr>
													<?php } ?>
												</tbody>
												<tfoot>
													<tr>
														<th width="10%"></th>
														<th></th>
														<th></th>
														<th></th>
													</tr>
												</tfoot>
											</table>
										</div>
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
		<?php } ?>












































		<?php $this->load->view('inc/footer-area.php'); ?>

	</body>
</html>

<div class="scriptLoader"></div>