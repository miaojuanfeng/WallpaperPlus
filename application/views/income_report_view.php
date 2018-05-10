<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Income report</title>

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
			$('input[name="log_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');
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

					<h2 class="col-sm-12"><a href="<?=base_url('report')?>">Report</a> > Income report</h2>

					<div class="content-column-area col-md-12 col-sm-12">

						<div class="fieldset">
							<div class="search-area">

								<form role="form" method="get">
									<input type="hidden" name="role_id" />
									<table>
										<tbody>
											<tr>
												<td width="90%">
													<div class="row">
														<div class="col-sm-2"><h6>Income report</h6></div>
														<div class="col-sm-2">
															<input type="text" name="log_id" class="form-control input-sm" placeholder="#" value="" />
														</div>
														<div class="col-sm-2">
															<select name="log_permission_class" data-placeholder="Class" class="chosen-select">
																<option value></option>
															</select>
														</div>
														<div class="col-sm-2">
															<select name="log_permission_action" data-placeholder="Action" class="chosen-select">
																<option value></option>
															</select>
														</div>
														<div class="col-sm-2">
															<select name="log_user_id" data-placeholder="User" class="chosen-select">
																<option value></option>
															</select>
														</div>
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
								<form name="list" action="<?=base_url('role/delete')?>" method="post">
									<div class="page-area">
										<span class="btn btn-sm btn-default">10</span>
										<?=$this->pagination->create_links()?>
									</div>
									<table id="log" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>#</th>
												<th>Handler</th>
												<th>IP</th>
												<th>Link</th>
												<th>Permission</th>
												<th>Record#</th>
												<th></th>
												<th>Create</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<?php for($key=0; $key<10; $key++){ ?>
											<tr>
												<td title=""><?=$key+1?></td>
												<td>test</td>
												<td>test</td>
												<td>test</td>
												<td>test</td>
												<td>test</td>
												<td>test</td>
												<td>test</td>
												<td class="text-right">
													<span data-toggle="modal" data-target="#myModal" class="modal-btn" rel="1">
														<a data-toggle="tooltip" title="More">
															<i class="glyphicon glyphicon-chevron-right"></i>
														</a>
													</span>
												</td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
									<div class="page-area">
										<span class="btn btn-sm btn-default">10</span>
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