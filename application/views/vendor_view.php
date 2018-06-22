<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Vendor management</title>

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
		var vendors = <?=(!empty($vendors)) ? json_encode($vendors) : '[]'?>;

		$(function(){
			$('input[name="vendor_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');

			/*--------- jQuery validator ---------*/
			jQuery.validator.addMethod("vendorNameCheckDuplicate", function(value, element) {
				thisResult = true;
				$(vendors).each(function(key, val){
					if(value.toUpperCase() == val.vendor_company_name.toUpperCase()){
						thisResult = false;
					}
				});
				return this.optional(element) || thisResult;
			}, "This field is duplicated.");
			/*--------- jQuery validator ————*/

			/*--------- jQuery validator ---------*/
			jQuery.validator.addMethod("vendorCodeCheckDuplicate", function(value, element) {
				thisResult = true;
				$(vendors).each(function(key, val){
					if(value.toUpperCase() == val.vendor_company_code.toUpperCase()){
						thisResult = false;
					}
				});
				return this.optional(element) || thisResult;
			}, "This field is duplicated.");
			/*--------- jQuery validator ————*/
		});

		function check_delete(id){
			var answer = prompt("Confirm delete?");
			if(answer){
				$('input[name="vendor_id"]').val(id);
				$('input[name="vendor_delete_reason"]').val(encodeURI(answer));
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

					<h2 class="col-sm-12"><a href="<?=base_url('vendor')?>">Vendor management</a> > <?=($this->router->fetch_method() == 'update') ? 'Upate' : 'Insert'?> vendor</h2>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="vendor_id" value="<?=$vendor->vendor_id?>" />
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
                                            <label for="vendor_company_code">Company ID <span class="highlight">*</span></label>
                                            <input id="vendor_company_code" name="vendor_company_code" type="text" class="form-control input-sm required vendorCodeCheckDuplicate" placeholder="Company ID" value="<?=$vendor->vendor_company_code?>" />
                                        </p>
										<p class="form-group">
											<label for="vendor_company_name">Company name <span class="highlight">*</span></label>
											<input id="vendor_company_name" name="vendor_company_name" type="text" class="form-control input-sm required vendorNameCheckDuplicate" placeholder="Company name" value="<?=$vendor->vendor_company_name?>" />
										</p>
										<!-- <p class="form-group">
											<label for="vendor_company_scope">Service scope</label>
											<textarea id="vendor_company_scope" name="vendor_company_scope" class="form-control input-sm" placeholder="Service scope" rows="5"><?=$vendor->vendor_company_scope?></textarea>
										</p> -->
										<p class="form-group">
											<label for="vendor_company_address">Address</label>
											<textarea id="vendor_company_address" name="vendor_company_address" class="form-control input-sm" placeholder="Address" rows="5"><?=$vendor->vendor_company_address?></textarea>
										</p>
										<p class="form-group">
											<label for="vendor_company_phone">Company phone</label>
											<input id="vendor_company_phone" name="vendor_company_phone" type="text" class="form-control input-sm number" placeholder="Company phone" value="<?=$vendor->vendor_company_phone?>" />
										</p>
										<p class="form-group">
											<label for="vendor_company_fax">Company fax</label>
											<input id="vendor_company_fax" name="vendor_company_fax" type="text" class="form-control input-sm" placeholder="Company fax" value="<?=$vendor->vendor_company_fax?>" />
										</p>
										<p class="form-group">
											<label for="vendor_company_email">Company email</label>
											<input id="vendor_company_email" name="vendor_company_email" type="text" class="form-control input-sm email" placeholder="Company email" value="<?=$vendor->vendor_company_email?>" />
										</p>
										<p class="form-group">
											<label for="vendor_company_website">Website</label>
											<input id="vendor_company_website" name="vendor_company_website" type="text" class="form-control input-sm" placeholder="Website" value="<?=$vendor->vendor_company_website?>" />
										</p>
										<p class="form-group">
											<label for="vendor_company_remark">Remark</label>
											<textarea id="vendor_company_remark" name="vendor_company_remark" class="form-control input-sm" placeholder="Remark" rows="5"><?=$vendor->vendor_company_remark?></textarea>
										</p>
									</div>
									<div class="col-sm-4 col-xs-12">
										<h4 class="corpcolor-font">Related information</h4>
										<p class="form-group">
											<label for="vendor_firstname">First name</label>
											<input id="vendor_firstname" name="vendor_firstname" type="text" class="form-control input-sm" placeholder="First name" value="<?=$vendor->vendor_firstname?>" />
										</p>
										<p class="form-group">
											<label for="vendor_lastname">Last name <span class="highlight">*</span></label>
											<input id="vendor_lastname" name="vendor_lastname" type="text" class="form-control input-sm required" placeholder="Last name" value="<?=$vendor->vendor_lastname?>" />
										</p>
										<p class="form-group">
											<label for="vendor_email">Email <span class="highlight">*</span></label>
											<input id="vendor_email" name="vendor_email" type="text" class="form-control input-sm required" placeholder="Email" value="<?=$vendor->vendor_email?>" />
										</p>
										<p class="form-group">
											<label for="vendor_phone">Phone</label>
											<input id="vendor_phone" name="vendor_phone" type="text" class="form-control input-sm" placeholder="Phone" value="<?=$vendor->vendor_phone?>" />
										</p>
										<p class="form-group">
											<label for="vendor_fax">Fax</label>
											<input id="vendor_fax" name="vendor_fax" type="text" class="form-control input-sm" placeholder="Fax" value="<?=$vendor->vendor_fax?>" />
										</p>
                                        <p class="form-group">
                                            <label>Currency <span class="highlight">*</span></label>
                                            <select id="vendor_currency_id" name="vendor_currency_id" data-placeholder="Currency" class="chosen-select required">
                                                <option value></option>
                                                <?php
                                                foreach($currencys as $key => $value){
                                                    $selected = ($value->currency_id == $vendor->vendor_currency_id) ? ' selected="selected"' : "" ;
                                                    ?>
                                                    <option value="<?=$value->currency_id?>"<?=$selected?>><?=strtoupper($value->currency_name)?></option>
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

					<h2 class="col-sm-12">Vendor management</h2>

					<div class="content-column-area col-md-12 col-sm-12">

						<div class="fieldset">
							<div class="search-area">

								<form vendor="form" method="get">
									<input type="hidden" name="vendor_id" />
									<table>
										<tbody>
											<tr>
												<td width="90%">
													<div class="row">
														<div class="col-sm-2"><h6>Vendor</h6></div>
														<div class="col-sm-2">
															<input type="text" name="vendor_id" class="form-control input-sm" placeholder="#" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="vendor_company_name_like" class="form-control input-sm" placeholder="Vendor name" value="" />
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
								<form name="list" action="<?=base_url('vendor/delete')?>" method="post">
									<input type="hidden" name="vendor_id" />
									<input type="hidden" name="vendor_delete_reason" />
									<div class="page-area">
										<span class="btn btn-sm btn-default"><?php print_r($num_rows); ?></span>
										<?=$this->pagination->create_links()?>
									</div>
									<table id="vendor" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>#</th>
												<th>
													<a href="<?=get_order_link('vendor_firstname')?>">
														First name <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<th>
													<a href="<?=get_order_link('vendor_lastname')?>">
														Last name <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
                                                <th>
                                                    <a href="<?=get_order_link('vendor_company_code')?>">
                                                        Company ID <i class="glyphicon glyphicon-sort corpcolor-font"></i>
                                                    </a>
                                                </th>
												<th>
													<a href="<?=get_order_link('vendor_company_name')?>">
														Company name <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<th>
													<a href="<?=get_order_link('vendor_email')?>">
														Email <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<th>
													<a href="<?=get_order_link('vendor_modify')?>">
														Modify <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<th width="40"></th>
												<th width="40" class="text-right">
													<a href="<?=base_url('vendor/insert')?>" data-toggle="tooltip" title="Insert">
														<i class="glyphicon glyphicon-plus"></i>
													</a>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($vendors as $key => $value){ ?>
											<tr>
												<td title="<?=$value->vendor_id?>"><?=$key+1?></td>
												<td><?=$value->vendor_firstname?></td>
												<td><?=$value->vendor_lastname?></td>
                                                <td><?=$value->vendor_company_code?></td>
												<td><?=$value->vendor_company_name?></td>
												<td><a href="mailto:<?=$value->vendor_email?>"><?=$value->vendor_email?></a></td>
												<td><?=convert_datetime_to_date($value->vendor_modify)?></td>
												<td class="text-right">
													<a href="<?=base_url('vendor/update/vendor_id/'.$value->vendor_id)?>" data-toggle="tooltip" title="Update">
														<i class="glyphicon glyphicon-edit"></i>
													</a>
												</td>
												<td class="text-right">
													<?php if(!check_permission('vendor_delete', 'display')){ ?>
													<a onclick="check_delete(<?=$value->vendor_id?>);" data-toggle="tooltip" title="Remove" class="<?=check_permission('vendor_delete', 'disable')?>">
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