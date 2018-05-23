<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>PO waybill management</title>

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
        <script src="<?php echo base_url('assets/js/moment.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/bootstrap-datetimepicker.min.js'); ?>"></script>
		<script src="<?php echo base_url('assets/js/function.js'); ?>"></script>

		<script>
		$(function(){
			$('input[name="waybill_id"]').focus();

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

		function check_delete(id){
			var answer = prompt("Confirm delete?");
			if(answer){
				$('input[name="waybill_id"]').val(id);
				$('input[name="waybill_delete_reason"]').val(encodeURI(answer));
				$('form[name="list"]').submit();
			}else{
				return false;
			}
		}

		function login_as(id){
			$('input[name="waybill_id"]').val(id);
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

					<h2 class="col-sm-12"><a href="<?=base_url('waybill')?>">PO waybill management</a> > <?=($this->router->fetch_method() == 'update') ? 'Upate' : 'Insert'?> waybill</h2>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="waybill_id" value="<?=$waybill->waybill_id?>" />
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
                                            <label for="waybill_number">Waybill number <span class="highlight">*</span></label>
                                            <input id="waybill_number" name="waybill_number" class="form-control input-sm required" placeholder="Waybill number" value="<?=$waybill->waybill_number?>" />
                                        </p>
                                        <p class="form-group">
                                            <label for="waybill_lot_number">Lot number</label>
                                            <input id="waybill_lot_number" name="waybill_lot_number" type="text" class="form-control input-sm" placeholder="Lot number" value="<?=$waybill->waybill_lot_number?>" />
                                        </p>
										<p class="form-group">
											<label for="waybill_customs_number">Customs number <span class="highlight"></span></label>
											<input id="waybill_customs_number" name="waybill_customs_number" type="text" class="form-control input-sm" placeholder="Customs number" value="<?=$waybill->waybill_customs_number?>" />
										</p>
                                        <p class="form-group">
                                            <label for="waybill_express_company">Express company</label>
                                            <input id="waybill_express_company" name="waybill_express_company" type="text" class="form-control input-sm" placeholder="Express company" value="<?=$waybill->waybill_express_company?>" />
                                        </p>
                                        <p class="form-group">
                                            <label for="waybill_delivery_day">Delivery day</label>
                                            <span class="input-group date datetimepicker">
                                                <input id="waybill_delivery_day" name="waybill_delivery_day" type="text" class="form-control input-sm date-mask" placeholder="Date From (YYYY-MM-DD)" value="<?=$waybill->waybill_delivery_day?>" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </span>
                                        </p>
                                        <p class="form-group">
                                            <label for="waybill_remark">Remark</label>
                                            <textarea id="waybill_remark" name="waybill_remark" class="form-control input-sm" placeholder="Remark" rows="3"><?=$waybill->waybill_remark?></textarea>
                                        </p>
									</div>
									<div class="col-sm-4 col-xs-12">
										<h4 class="corpcolor-font">Related information</h4>
                                        <p class="form-group">
                                            <label for="z_waybill_purchaseorder_purchaseorder_id">PO <span class="highlight">*</span></label>
                                            <select id="z_waybill_purchaseorder_purchaseorder_id" name="z_waybill_purchaseorder_purchaseorder_id[]" data-placeholder="PO" class="chosen-select required" multiple="multiple">
                                                <option value></option>
                                                <?php
                                                foreach($purchaseorders as $key => $value){
                                                    $selected = (in_array($value->purchaseorder_id, $z_waybill_purchaseorder_purchaseorder_ids)) ? ' selected="selected"' : "" ;
                                                    echo '<option value="'.$value->purchaseorder_id.'"'.$selected.'>'.strtoupper($value->purchaseorder_number).'</option>';
                                                }
                                                ?>
                                            </select>
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

					<h2 class="col-sm-12">PO waybill management</h2>

					<div class="content-column-area col-md-12 col-sm-12">

						<div class="fieldset">
							<div class="search-area">

								<form waybill="form" method="get">
									<input type="hidden" name="waybill_id" />
									<table>
										<tbody>
											<tr>
												<td width="90%">
													<div class="row">
														<div class="col-sm-2"><h6>PO waybill</h6></div>
														<div class="col-sm-2">
															<input type="text" name="waybill_id" class="form-control input-sm" placeholder="#" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="waybill_number_like" class="form-control input-sm" placeholder="Waybill number" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="purchaseorder_number_like" class="form-control input-sm" placeholder="PO number" value="" />
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
								<form name="list" action="<?=base_url('waybill/delete')?>" method="post">
									<input type="hidden" name="waybill_id" />
									<input type="hidden" name="waybill_delete_reason" />
									<div class="page-area">
										<span class="btn btn-sm btn-default"><?php print_r($num_rows); ?></span>
										<?=$this->pagination->create_links()?>
									</div>
									<table id="waybill" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>#</th>
												<th>Waybill number</th>
                                                <th>Lot number</th>
                                                <th>Customs number</th>
                                                <th>Express company</th>
                                                <th>Delivery day</th>
												<th>Modify</th>
												<!-- <th width="40"></th> -->
												<th width="40"></th>
												<th width="40" class="text-right">
													<a href="<?=base_url('waybill/insert')?>" data-toggle="tooltip" title="Insert">
														<i class="glyphicon glyphicon-plus"></i>
													</a>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($waybills as $key => $value){ ?>
											<tr>
												<td title="<?=$value->waybill_id?>"><?=$key+1?></td>
												<td><?=ucfirst($value->waybill_number)?></td>
                                                <td><?=ucfirst($value->waybill_lot_number)?></td>
                                                <td><?=ucfirst($value->waybill_customs_number)?></td>
                                                <td><?=ucfirst($value->waybill_express_company)?></td>
                                                <td><?=ucfirst($value->waybill_delivery_day)?></td>
												<td><?=convert_datetime_to_date($value->waybill_modify)?></td>
												<!-- <td class="text-right">
													<span data-toggle="modal" data-target="#myModal" class="modal-btn" rel="<?=$value->waybill_id?>">
														<a data-toggle="tooltip" title="More">
															<i class="glyphicon glyphicon-chevron-right"></i>
														</a>
													</span>
												</td> -->
												<td class="text-right">
													<a href="<?=base_url('waybill/update/waybill_id/'.$value->waybill_id)?>" data-toggle="tooltip" title="Update">
														<i class="glyphicon glyphicon-edit"></i>
													</a>
												</td>
												<td class="text-right">
													<?php if(!check_permission('waybill_delete', 'display')){ ?>
													<a onclick="check_delete(<?=$value->waybill_id?>);" data-toggle="tooltip" title="Remove" class="<?=check_permission('waybill_delete', 'disable')?>">
														<i class="glyphicon glyphicon-remove"></i>
													</a>
													<?php }else{ ?>
													<i class="glyphicon glyphicon-remove"></i>
													<?php } ?>
												</td>
											</tr>
											<?php } ?>
                                            <?php if(!$waybills){ ?>
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
				</div>
			</div>

		</div>
		<?php } ?>












































		<?php $this->load->view('inc/footer-area.php'); ?>

	</body>
</html>