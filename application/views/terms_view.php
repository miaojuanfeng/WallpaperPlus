<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Terms management</title>

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
			$('input[name="terms_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');
		});

		function check_delete(id){
			var answer = prompt("Confirm delete?");
			if(answer){
				$('input[name="terms_id"]').val(id);
				$('input[name="terms_delete_reason"]').val(encodeURI(answer));
				$('form[name="list"]').submit();
			}else{
				return false;
			}
		}

		function login_as(id){
			$('input[name="terms_id"]').val(id);
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

					<h2 class="col-sm-12"><a href="<?=base_url('terms')?>">Terms management</a> > <?=($this->router->fetch_method() == 'update') ? 'Upate' : 'Insert'?> terms</h2>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="terms_id" value="<?=$terms->terms_id?>" />
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
											<label for="terms_name">Name <span class="highlight">*</span></label>
											<input id="terms_name" name="terms_name" type="text" class="form-control input-sm required" placeholder="Name" value="<?=$terms->terms_name?>" />
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

					<h2 class="col-sm-12">Terms management</h2>

					<div class="content-column-area col-md-12 col-sm-12">

						<div class="fieldset">
							<div class="search-area">

								<form terms="form" method="get">
									<input type="hidden" name="terms_id" />
									<table>
										<tbody>
											<tr>
												<td width="90%">
													<div class="row">
														<div class="col-sm-2"><h6>Terms</h6></div>
														<div class="col-sm-2">
															<input type="text" name="terms_id" class="form-control input-sm" placeholder="#" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="terms_name_like" class="form-control input-sm" placeholder="Terms name" value="" />
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
								<form name="list" action="<?=base_url('terms/delete')?>" method="post">
									<input type="hidden" name="terms_id" />
									<input type="hidden" name="terms_delete_reason" />
									<div class="page-area">
										<span class="btn btn-sm btn-default"><?php print_r($num_rows); ?></span>
										<?=$this->pagination->create_links()?>
									</div>
									<table id="terms" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>#</th>
												<th>
													<a href="<?=get_order_link('terms_name')?>">
														Name <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<th>
													<a href="<?=get_order_link('terms_modify')?>">
														Modify <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<th width="40"></th>
												<th width="40" class="text-right">
													<a href="<?=base_url('terms/insert')?>" data-toggle="tooltip" title="Insert">
														<i class="glyphicon glyphicon-plus"></i>
													</a>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($termss as $key => $value){ ?>
											<tr>
												<td title="<?=$value->terms_id?>"><?=$key+1?></td>
												<td><?=ucfirst($value->terms_name)?></td>
												<td><?=convert_datetime_to_date($value->terms_modify)?></td>
												<td class="text-right">
													<a href="<?=base_url('terms/update/terms_id/'.$value->terms_id)?>" data-toggle="tooltip" title="Update">
														<i class="glyphicon glyphicon-edit"></i>
													</a>
												</td>
												<td class="text-right">
													<?php if(!check_permission('terms_delete', 'display')){ ?>
													<a onclick="check_delete(<?=$value->terms_id?>);" data-toggle="tooltip" title="Remove" class="<?=check_permission('terms_delete', 'disable')?>">
														<i class="glyphicon glyphicon-remove"></i>
													</a>
													<?php }else{ ?>
													<i class="glyphicon glyphicon-remove"></i>
													<?php } ?>
												</td>
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