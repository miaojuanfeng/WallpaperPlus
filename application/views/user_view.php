<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>User management</title>

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
			$('input[name="user_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');
		});

		function check_delete(id){
			var answer = prompt("Confirm delete?");
			if(answer){
				$('input[name="user_id"]').val(id);
				$('input[name="user_delete_reason"]').val(encodeURI(answer));
				$('form[name="list"]').submit();
			}else{
				return false;
			}
		}
		</script>
	</head>

	<body>

		<?php $this->load->view('inc/header-area.php'); ?>

		








































		<?php if($this->router->fetch_method() == 'update' || $this->router->fetch_method() == 'insert'){ ?>
		<div class="content-area">

			<div class="container-fluid">
				<div class="row">

					<h2 class="col-sm-12"><a href="<?=base_url('user')?>">User management</a> > <?=($this->router->fetch_method() == 'update') ? 'Upate' : 'Insert'?> user</h2>

					<div class="col-sm-12">
						<form method="post">
							<input type="hidden" name="user_id" value="<?=$user->user_id?>" />
							<input type="hidden" name="referrer" value="<?=$this->agent->referrer()?>" />
							<div class="fieldset">
								<div class="row">
									
									<div class="col-sm-4 col-xs-12 pull-right">
										<blockquote>
											<h4 class="corpcolor-font">Instructions</h4>
											<p>* is a required field</p>
										</blockquote>
									</div>
									<div class="col-sm-4 col-xs-12">
										<h4 class="corpcolor-font">Basic information</h4>
										<p class="form-group">
											<label for="user_name">Name <span class="highlight">*</span></label>
											<input id="user_name" name="user_name" type="text" class="form-control input-sm required" placeholder="Name" value="<?=$user->user_name?>" />
										</p>
										<p class="form-group">
											<label for="user_username">Username <span class="highlight">*</span></label>
											<input id="user_username" name="user_username" type="text" class="form-control input-sm required" placeholder="Username" value="<?=$user->user_username?>" />
										</p>
										<p class="form-group">
											<label for="user_password">Password</label>
											<input id="user_password" name="user_password" type="password" class="form-control input-sm" placeholder="Password" />
											<small class="corpcolor-font">If not update the password, please leave it blank</small>
										</p>
										<p class="form-group">
											<label for="user_email">Email <span class="highlight">*</span></label>
											<input id="user_email" name="user_email" type="text" class="form-control input-sm email required" placeholder="Email" value="<?=$user->user_email?>" />
										</p>
									</div>
									<div class="col-sm-4 col-xs-12">
										<h4 class="corpcolor-font">Related information</h4>
										<p class="form-group">
											<label for="z_role_user_role_id">Role <span class="highlight">*</span></label>
											<select id="z_role_user_role_id" name="z_role_user_role_id[]" data-placeholder="Role" class="chosen-select required">
												<option value></option>
												<?php
												foreach($roles as $key => $value){
												$selected = (in_array($value->role_id, $z_role_user_role_ids)) ? ' selected="selected"' : "" ;
												?>
												<option value="<?=$value->role_id?>"<?=$selected?>><?=$value->role_name?></option>
												<?php } ?>
											</select>
										</p>
										<p class="form-group">
											<label for="user_team">Team <span class="highlight">*</span></label>
                                            <select id="user_team" name="user_team[]" data-placeholder="Team" class="chosen-select required" multiple="multiple">
                                                <option value></option>
                                                <?php
                                                foreach($teams as $key => $value){
                                                    $selected = (in_array($value->team_id, $z_role_user_role_ids)) ? ' selected="selected"' : "" ;
                                                    ?>
                                                    <option value="<?=$value->team_id?>"<?=$selected?>><?=$value->team_name?></option>
                                                <?php } ?>
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

					<h2 class="col-sm-12">User management</h2>

					<div class="content-column-area col-md-12 col-sm-12">

						<div class="fieldset">
							<div class="search-area">

								<form user="form" method="get">
									<input type="hidden" name="user_id" />
									<table>
										<tbody>
											<tr>
												<td width="90%">
													<div class="row">
														<div class="col-sm-2"><h6>User</h6></div>
														<div class="col-sm-2">
															<input type="text" name="user_id" class="form-control input-sm" placeholder="#" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="user_name_like" class="form-control input-sm" placeholder="User name" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="user_email_like" class="form-control input-sm date-mask" placeholder="Email" value="" />
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
								<form name="list" action="<?=base_url('user/delete')?>" method="post">
									<input type="hidden" name="user_id" />
									<input type="hidden" name="user_delete_reason" />
									<div class="page-area">
										<span class="btn btn-sm btn-default"><?php print_r($num_rows); ?></span>
										<?=$this->pagination->create_links()?>
									</div>
									<table class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>#</th>
												<th>
													<a href="<?=get_order_link('user_name')?>">
														Name <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<th>
													<a href="<?=get_order_link('user_username')?>">
														Username <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<th>
													<a href="<?=get_order_link('user_email')?>">
														Email <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<th>
													<a href="<?=get_order_link('user_modify')?>">
														Modify <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<th width="40"></th>
												<th width="40" class="text-right">
													<a href="<?=base_url('user/insert')?>" data-toggle="tooltip" title="Insert">
														<i class="glyphicon glyphicon-plus"></i>
													</a>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($users as $key => $value){ ?>
											<tr>
												<td title="<?=$value->user_id?>"><?=$key+1?></td>
												<td><?=$value->user_name?></td>
												<td><?=$value->user_username?></td>
												<td><a href="mailto:<?=$value->user_email?>"><?=$value->user_email?></a></td>
												<td><?=convert_datetime_to_date($value->user_modify)?></td>
												<td class="text-right">
													<a href="<?=base_url('user/update/user_id/'.$value->user_id)?>" data-toggle="tooltip" title="Update">
														<i class="glyphicon glyphicon-edit"></i>
													</a>
												</td>
												<td class="text-right">
													<?php if(!check_permission('user_delete', 'display')){ ?>
													<a onclick="check_delete(<?=$value->user_id?>);" data-toggle="tooltip" title="Remove" class="<?=check_permission('user_delete', 'disable')?>">
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