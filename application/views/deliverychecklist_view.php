<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Delivery checklist</title>

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

			// $('input[name^="checklistStatus-"]').click(function(){
			// 	thisInputName = $(this).attr('name');
			// 	if($('input[name="'+thisInputName+'"]:checked')){
			// 		setTimeout(function(){ 
			// 			var answer = prompt("Please insert remark");
			// 			if(answer){
			// 				thisId = thisInputName.replace('checklistStatus-', '');
			// 				$('input[name="deliverynote_id"]').val(thisId);
			// 				$('input[name="deliverynote_status"]').val('complete');
			// 				$('input[name="deliverynote_status_remark"]').val(encodeURI(answer));
			// 				$('form[name="list"]').submit();
			// 			}else{
			// 				$('input[name="'+thisInputName+'"]').prop('checked', false);
			// 			}
			// 		}, 529);
			// 	}
			// });
		});
		</script>
	</head>

	<body>

		<?php $this->load->view('inc/header-area.php'); ?>

		








































		<?php if($this->router->fetch_method() == 'update' || $this->router->fetch_method() == 'insert'){ ?>
		<div class="content-area">

			<div class="container-fluid">
				<div class="row">

					<h2 class="col-sm-12"><a href="<?=base_url('deliverychecklist/select/deliverynote_status/processing')?>">Delivery checklist management</a> > <?=($this->router->fetch_method() == 'update') ? 'Update' : 'Insert'?> delivery checklist</h2>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="deliverynote_id" value="<?=$deliverynote->deliverynote_id?>" />
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
										<h4 class="corpcolor-font">Delivery checklist</h4>
										<div class="row">
											<div class="col-sm-6 col-xs-6">
											</div>
											<div class="col-sm-1 col-xs-1">
											</div>
											<div class="col-sm-5 col-xs-5">
											</div>
										</div>
										<div class="list-area">
											
										</div>
										<hr />
										<p class="form-group">
											<label for="deliverynote_remark">Remark</label>
											<textarea id="deliverynote_remark" name="deliverynote_remark" class="form-control input-sm" placeholder="Remark" rows="3"><?=$deliverynote->deliverynote_remark?></textarea>
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

					<h2 class="col-sm-12">Delivery checklist</h2>

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
														<div class="col-sm-2"><h6>Delivery</h6></div>
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
															<select id="deliverynote_status" name="deliverynote_status" data-placeholder="Status" class="chosen-select">
																<?php
																foreach($statuss as $key => $value){
																	$selected = ($value->status_name == $this->uri->uri_to_assoc()['deliverynote_status']) ? ' selected="selected"' : "" ;
																	echo '<option value="'.$value->status_name.'"'.$selected.'>'.ucfirst($value->status_name).'</option>';
																}
																?>
															</select>
														</div>
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
								<form name="list" action="<?=base_url('deliverychecklist/update')?>" method="post">
									<input type="hidden" name="deliverynote_id" />
									<!-- <input type="hidden" name="deliverynote_delete_reason" /> -->
									<input type="hidden" name="deliverynote_status" />
									<input type="hidden" name="deliverynote_status_date" />
									<input type="hidden" name="deliverynote_status_remark" />
									<div class="page-area">
										<span class="btn btn-sm btn-default"><?php print_r($num_rows); ?></span>
										<?=$this->pagination->create_links()?>
									</div>
									<table class="table table-striped table-bordered">
										<thead>
											<tr>
												<th></th>
                                                <th>DN No</th>
                                                <th>SO No</th>
                                                <th>Create</th>
                                                <th>Customer</th>
                                                <th>Project</th>
                                                <th>Sales</th>
                                                <th>Status</th>
                                                <th>Remark</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($deliverynotes as $key => $value){ ?>
											<tr>
												<td>
													<input<?=($this->uri->uri_to_assoc()['deliverynote_status'] == 'complete') ? ' disabled="disabled" checked="checked"' : ''?> name="checklistStatus-<?=$value->deliverynote_id?>" type="checkbox" 
													name-id="deliverynote_id" 
													name-status="deliverynote_status" 
													name-date="deliverynote_status_date" 
													name-remark="deliverynote_status_remark" />
												</td>
												<td>
													<a href="<?=base_url('deliverynote/update/deliverynote_id/'.$value->deliverynote_id)?>">
														<?=$value->deliverynote_number?>
													</a>
												</td>
                                                <td><a href="<?=base_url('salesorder/update/salesorder_id/'.$value->deliverynote_salesorder_id)?>"><?=get_salesorder($value->deliverynote_salesorder_id)->salesorder_number?></a></td>
                                                <td><?=convert_datetime_to_date($value->deliverynote_create)?></td>
                                                <td><?=$value->deliverynote_client_company_name?></td>
                                                <td><?=$value->deliverynote_project_name?></td>
												<td><?=ucfirst(get_user($value->deliverynote_user_id)->user_name)?></td>
												<td><?=ucfirst($value->deliverynote_status)?></td>
												<td><?=($value->deliverynote_status_remark) ? $value->deliverynote_status_remark : 'N/A'?></td>
											</tr>
											<?php } ?>

											<?php if(!$deliverynotes){ ?>
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
						<!-- <p>Add processing and complete status filter</p>
						<p>"Complete" deliverynotechecklist will not show in this list</p> -->
					</div>
				</div>
			</div>

		</div>
		<?php } ?>












































		<?php $this->load->view('inc/footer-area.php'); ?>

	</body>
</html>

<div class="scriptLoader"></div>

<?php $this->load->view('modal/checklist_modal.php'); ?>