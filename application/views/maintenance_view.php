<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Maintenance</title>

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
		<script src="<?php echo base_url('assets/js/echarts.min.js'); ?>"></script>
		<script src="<?php echo base_url('assets/js/function.js'); ?>"></script>

		<script>
		$(function(){
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

					<h2 class="col-sm-12">Maintenance</h2>

					<div class="content-column-area col-md-12 col-sm-12">

						<div class="fieldset min-height-500">

							<h4>Client / Vendor</h4>
							<div class="row">
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-user"></i>
										<a href="<?=base_url('client')?>">Client</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-user"></i>
										<a href="<?=base_url('vendor')?>">Vendor</a>
									</blockquote>
								</div>
								<!-- <div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-home"></i>
										<a href="<?=base_url('company')?>">Company</a>
									</blockquote>
								</div> -->
								<div class="col-md-3 col-sm-12"></div>
								<div class="col-md-3 col-sm-12"></div>
							</div>

							<div class="hr"></div>

							<h4>Product</h4>
							<div class="row">
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-hdd"></i>
										<a href="<?=base_url('product')?>">Product</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12"></div>
								<div class="col-md-3 col-sm-12"></div>
								<div class="col-md-3 col-sm-12"></div>
							</div>
							<div class="row">
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-user"></i>
										<a href="<?=base_url('brand')?>">Brand</a>
									</blockquote>
								</div>
                                <div class="col-md-3 col-sm-12">
                                    <blockquote>
                                        <i class="glyphicon glyphicon-th-list"></i>
                                        <a href="<?=base_url('attribute/select/7')?>">Category</a>
                                    </blockquote>
                                </div>
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-user"></i>
										<a href="<?=base_url('attribute/select/1')?>">Color</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-home"></i>
										<a href="<?=base_url('attribute/select/2')?>">Style</a>
									</blockquote>
								</div>
							</div>
							<div class="row">
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-home"></i>
										<a href="<?=base_url('attribute/select/3')?>">Usage</a>
									</blockquote>
								</div>
                                <div class="col-md-3 col-sm-12">
                                    <blockquote>
                                        <i class="glyphicon glyphicon-home"></i>
                                        <a href="<?=base_url('attribute/select/4')?>">Material</a>
                                    </blockquote>
                                </div>
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-barcode"></i>
										<a href="<?=base_url('attribute/select/5')?>">Keyword</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-th-list"></i>
										<a href="<?=base_url('attribute/select/6')?>">Size</a>
									</blockquote>
								</div>
							</div>

							<div class="hr"></div>

							<h4>Warehouse</h4>
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

							<div class="hr"></div>
							
							<h4>Checklist</h4>
							<div class="row">
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-ok-circle"></i>
										<a href="<?=base_url('purchaseorderchecklist/select/purchaseorder_status/processing')?>">Purchase order checklist</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-ok-circle"></i>
										<a href="<?=base_url('invoicechecklist/select/invoice_status/processing')?>">Invoice checklist</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-ok-circle"></i>
										<a href="<?=base_url('commissionchecklist/select/invoice_commission_status/processing')?>">Commission checklist</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-ok-circle"></i>
										<a href="<?=base_url('xxx')?>">Delivery checklist</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12"></div>
							</div>

							<div class="hr"></div>
							
							<h4>Role / User</h4>
							<div class="row">
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-user"></i>
										<a href="<?=base_url('role')?>">Role</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-user"></i>
										<a href="<?=base_url('user')?>">User</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12"></div>
								<div class="col-md-3 col-sm-12"></div>
							</div>

							<div class="hr"></div>
							
							<h4>Setting</h4>
							<div class="row">
								<div class="col-md-3 col-sm-12">
									<blockquote>
										<i class="glyphicon glyphicon-cog"></i>
										<a href="<?=base_url('setting')?>">Setting</a>
									</blockquote>
								</div>
								<div class="col-md-3 col-sm-12">
                                    <blockquote>
                                        <i class="glyphicon glyphicon-cog"></i>
                                        <a href="<?=base_url('currency')?>">Currency</a>
                                    </blockquote>
                                </div>
								<div class="col-md-3 col-sm-12"></div>
								<div class="col-md-3 col-sm-12"></div>
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

<div class="scriptLoader"></div>