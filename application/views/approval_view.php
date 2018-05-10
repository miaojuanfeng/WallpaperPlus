<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Approval code</title>

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
			$('input[name="approval_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');
		});

		function check_delete(id){
			var answer = prompt("Confirm delete?");
			if(answer){
				$('input[name="approval_id"]').val(id);
				$('input[name="approval_delete_reason"]').val(encodeURI(answer));
				$('form[name="list"]').submit();
			}else{
				return false;
			}
		}

		function login_as(id){
			$('input[name="approval_id"]').val(id);
			$('input[name="act"]').val('login_as');
			$('form[name="list"]').submit();
		}
		</script>
	</head>

	<body>

		<?php $this->load->view('inc/header-area.php'); ?>

		








































		<?php if($this->router->fetch_method() == 'update'){ ?>
		<div class="content-area">

			<div class="container-fluid">
				<div class="row">

					<h2 class="col-sm-12"><a href="<?=base_url('approval')?>">Approval code</a> > <?=($this->router->fetch_method() == 'update') ? 'Update' : 'Insert'?> approval</h2>

					<div class="col-sm-12">
						<form method="post">
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
                                            <label for="approval_quotation">Approval quotation <span class="highlight">*</span></label>
                                            <select id="approval_quotation" name="approval_quotation" data-placeholder="Approval quotation" class="chosen-select">
												<option value></option>
												<?php
												foreach($quotations as $key => $value){
													$selected = ($value->quotation_number == $approval_quotation) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value->quotation_number.'"'.$selected.'>'.$value->quotation_number.'</option>';
												}
												?>
											</select>
                                        </p>
										<p class="form-group">
											<label for="approval_key"><?=ucfirst(str_replace('_', ' ', $approval->setting_name))?> <span class="highlight">*</span></label>
											<input id="approval_key" name="approval_key" type="text" readonly="readonly" class="form-control input-sm required" placeholder="<?=ucfirst(str_replace('_', ' ', $approval->setting_name))?>" value="<?=$approval->setting_value?>" />
										</p>
										<p class="form-group">
											<label for="approval_date">Approval date <span class="highlight">*</span></label>
											<input id="approval_date" name="approval_date" type="text" readonly="readonly" class="form-control input-sm required" placeholder="Approval date" value="<?=Date("Y-m-d")?>" />
										</p>
									</div>
									<div class="col-sm-4 col-xs-12">
										<h4 class="corpcolor-font">Approval information</h4>
										<p class="form-group">
											<label for="approval_code"> <span class="highlight"></span></label>
											<span id="approval_code" style="display:block;font-size:22px;font-weight: bold;text-align:center;"><?=$approval_code?></span>
										</p>
									</div>
								</div>

								<div class="row">
									<div class="col-xs-12">
										<button type="submit" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-random"></i> Generate</button>
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
		<?php } ?>












































		<?php $this->load->view('inc/footer-area.php'); ?>

	</body>
</html>