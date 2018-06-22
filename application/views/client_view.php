<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Client management</title>

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
		var clients = <?=(!empty($clients)) ? json_encode($clients) : '[]'?>;

		$(function(){
			$('input[name="client_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');

			/*--------- jQuery validator ---------*/
			jQuery.validator.addMethod("clientNameCheckDuplicate", function(value, element) {
				thisResult = true;
				$(clients).each(function(key, val){
					if(value.toUpperCase() == val.client_company_name.toUpperCase()){
						thisResult = false;
					}
				});
				return this.optional(element) || thisResult;
			}, "This field is duplicated.");
			/*--------- jQuery validator ————*/

			/*--------- jQuery validator ---------*/
			jQuery.validator.addMethod("clientCodeCheckDuplicate", function(value, element) {
				thisResult = true;
				$(clients).each(function(key, val){
					if(value.toUpperCase() == val.client_company_code.toUpperCase()){
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
				$('input[name="client_id"]').val(id);
				$('input[name="client_delete_reason"]').val(encodeURI(answer));
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

					<h2 class="col-sm-12"><a href="<?=base_url('client')?>">Client management</a> > <?=($this->router->fetch_method() == 'update') ? 'Upate' : 'Insert'?> client</h2>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="client_id" value="<?=$client->client_id?>" />
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
											<label>Sales <span class="highlight">*</span></label>
											<select id="z_client_user_user_id" name="z_client_user_user_id[]" data-placeholder="Sales" class="chosen-select required" multiple="multiple">
												<option value></option>
												<?php
												foreach($users as $key => $value){
												$selected = (in_array($value->user_id, $z_client_user_user_ids)) ? ' selected="selected"' : "" ;
												?>
												<option value="<?=$value->user_id?>"<?=$selected?>><?=$value->user_name?></option>
												<?php } ?>
											</select>
										</p>
										<!-- <p class="form-group">
											<label for="client_company_location_id">Location</label>
											<select id="client_company_location_id" name="client_company_location_id" data-placeholder="Location" class="chosen-select">
												<option value></option>
												<?php
												foreach($locations as $key1 => $value1){
													$selected = ($value1->location_id == $client->client_company_location_id) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value1->location_id.'"'.$selected.'>'.$value1->location_name.'</option>';
												}
												?>
											</select>
										</p> -->
                                        <p class="form-group">
                                            <label for="client_company_code">Company ID <span class="highlight">*</span></label>
                                            <input id="client_company_code" name="client_company_code" type="text" class="form-control input-sm required clientCodeCheckDuplicate" placeholder="Company ID" value="<?=$client->client_company_code?>" />
                                        </p>
										<p class="form-group">
											<label for="client_company_name">Company name <span class="highlight">*</span></label>
											<input id="client_company_name" name="client_company_name" type="text" class="form-control input-sm required clientNameCheckDuplicate" placeholder="Company name" value="<?=$client->client_company_name?>" />
										</p>
										<!-- <p class="form-group">
											<label for="client_company_scope">Service scope</label>
											<textarea id="client_company_scope" name="client_company_scope" class="form-control input-sm" placeholder="Service scope" rows="5"><?=$client->client_company_scope?></textarea>
										</p> -->
										<p class="form-group">
											<label for="client_company_address">Address</label>
											<textarea id="client_company_address" name="client_company_address" class="form-control input-sm" placeholder="Address" rows="5"><?=$client->client_company_address?></textarea>
										</p>
										<p class="form-group">
											<label for="client_company_phone">Company phone</label>
											<input id="client_company_phone" name="client_company_phone" type="text" class="form-control input-sm number" placeholder="Company phone" value="<?=$client->client_company_phone?>" />
										</p>
										<p class="form-group">
											<label for="client_company_fax">Company fax</label>
											<input id="client_company_fax" name="client_company_fax" type="text" class="form-control input-sm" placeholder="Company fax" value="<?=$client->client_company_fax?>" />
										</p>
										<p class="form-group">
											<label for="client_company_email">Company email</label>
											<input id="client_company_email" name="client_company_email" type="text" class="form-control input-sm email" placeholder="Company email" value="<?=$client->client_company_email?>" />
										</p>
										<p class="form-group">
											<label for="client_company_website">Website</label>
											<input id="client_company_website" name="client_company_website" type="text" class="form-control input-sm" placeholder="Website" value="<?=$client->client_company_website?>" />
										</p>
										<p class="form-group">
											<label for="client_terms_id">Payment terms <span class="highlight">*</span></label>
											<select id="client_terms_id" name="client_terms_id" data-placeholder="Payment terms" class="chosen-select required">
												<option value></option>
												<?php
												foreach($termss as $key1 => $value1){
													$selected = ($value1->terms_id == $client->client_terms_id) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value1->terms_id.'"'.$selected.'>'.$value1->terms_name.'</option>';
												}
												?>
											</select>
										</p>
										<p class="form-group">
											<label for="attachment">Attachment</label>
											<input id="attachment" name="attachment" type="file" class="form-control input-sm" placeholder="Attachment" accept="image/*" />
										</p>
										<p class="form-group">
											<label for="client_company_remark">Remark</label>
											<textarea id="client_company_remark" name="client_company_remark" class="form-control input-sm" placeholder="Remark" rows="5"><?=$client->client_company_remark?></textarea>
										</p>
									</div>
									<div class="col-sm-4 col-xs-12">
										<h4 class="corpcolor-font">Related information</h4>
										<p class="form-group">
											<label for="client_firstname">First name</label>
											<input id="client_firstname" name="client_firstname" type="text" class="form-control input-sm" placeholder="First name" value="<?=$client->client_firstname?>" />
										</p>
										<p class="form-group">
											<label for="client_lastname">Last name <span class="highlight">*</span></label>
											<input id="client_lastname" name="client_lastname" type="text" class="form-control input-sm required" placeholder="Last name" value="<?=$client->client_lastname?>" />
										</p>
										<p class="form-group">
											<label for="client_email">Email <span class="highlight">*</span></label>
											<input id="client_email" name="client_email" type="text" class="form-control input-sm required" placeholder="Email" value="<?=$client->client_email?>" />
										</p>
										<p class="form-group">
											<label for="client_gender">Gender <span class="highlight">*</span></label>
											<select id="client_gender" name="client_gender" data-placeholder="Gender" class="chosen-select required">
												<option value></option>
												<?php
												foreach($genders as $key1 => $value1){
													$selected = ($value1->gender_name == $client->client_gender) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value1->gender_name.'"'.$selected.'>'.$value1->gender_name.'</option>';
												}
												?>
											</select>
										</p>
										<p class="form-group">
											<label for="client_jobtitle">Job title</label>
											<input id="client_jobtitle" name="client_jobtitle" type="text" class="form-control input-sm" placeholder="Job title" value="<?=$client->client_jobtitle?>" />
										</p>
										<p class="form-group">
											<label for="client_phone">Phone</label>
											<input id="client_phone" name="client_phone" type="text" class="form-control input-sm" placeholder="Phone" value="<?=$client->client_phone?>" />
										</p>
										<p class="form-group">
											<label for="client_fax">Fax</label>
											<input id="client_fax" name="client_fax" type="text" class="form-control input-sm" placeholder="Fax" value="<?=$client->client_fax?>" />
										</p>
										<p class="form-group">
											<label for="client_whatsapp">Whatsapp</label>
											<input id="client_whatsapp" name="client_whatsapp" type="text" class="form-control input-sm" placeholder="Whatsapp" value="<?=$client->client_whatsapp?>" />
										</p>
										<p class="form-group">
											<label for="client_wechat">WeChat</label>
											<input id="client_wechat" name="client_wechat" type="text" class="form-control input-sm" placeholder="WeChat" value="<?=$client->client_wechat?>" />
										</p>
										<p class="form-group">
											<label for="client_qq">QQ</label>
											<input id="client_qq" name="client_qq" type="text" class="form-control input-sm" placeholder="QQ" value="<?=$client->client_qq?>" />
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

					<h2 class="col-sm-12">Client management</h2>

					<div class="content-column-area col-md-12 col-sm-12">

						<div class="fieldset">
							<div class="search-area">

								<form client="form" method="get">
									<input type="hidden" name="client_id" />
									<table>
										<tbody>
											<tr>
												<td width="90%">
													<div class="row">
														<div class="col-sm-2"><h6>Client</h6></div>
														<div class="col-sm-2">
															<input type="text" name="client_id" class="form-control input-sm" placeholder="#" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="client_name_like" class="form-control input-sm" placeholder="Client name" value="" />
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
								<form name="list" action="<?=base_url('client/delete')?>" method="post">
									<input type="hidden" name="client_id" />
									<input type="hidden" name="client_delete_reason" />
									<div class="page-area">
										<span class="btn btn-sm btn-default"><?php print_r($num_rows); ?></span>
										<?=$this->pagination->create_links()?>
									</div>
									<table id="client" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>#</th>
												<!-- <th></th> -->
												<th>
													<a href="<?=get_order_link('client_firstname')?>">
														First name <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<th>
													<a href="<?=get_order_link('client_lastname')?>">
														Last name <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<th>
													<a href="<?=get_order_link('client_jobtitle')?>">
														Job title <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<th>
													<a href="<?=get_order_link('client_gender')?>">
														Gender <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
                                                <th>
                                                    <a href="<?=get_order_link('client_company_code')?>">
                                                        Company ID <i class="glyphicon glyphicon-sort corpcolor-font"></i>
                                                    </a>
                                                </th>
												<th>
													<a href="<?=get_order_link('client_company_name')?>">
														Company name <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<th>Attach</th>
												<!-- <th>Location</th>
												<th>
													<a href="<?=get_order_link('client_company_website')?>">
														Website <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th> -->
												<th>PIC</th>
												<th>
													<a href="<?=get_order_link('client_modify')?>">
														Modify <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<!-- <th width="40"></th> -->
												<th width="40"></th>
												<th width="40" class="text-right">
													<a href="<?=base_url('client/insert')?>" data-toggle="tooltip" title="Insert">
														<i class="glyphicon glyphicon-plus"></i>
													</a>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($clients as $key => $value){ ?>
											<tr>
												<td title="<?=$value->client_id?>"><?=$key+1?></td>
												<!-- <td><?=(date('Y-m-d H:i:s') > $value->client_recall) ? '<i class="corpcolor-font glyphicon glyphicon-phone-alt"></i>' : ''?></td> -->
												<td><?=$value->client_firstname?></td>
												<td><?=$value->client_lastname?></td>
												<td><?=$value->client_jobtitle?></td>
												<td><?=$value->client_gender?></td>
                                                <td><?=$value->client_company_code?></td>
												<td><?=$value->client_company_name?></td>
												<td>
													<?php
													if(file_exists($_SERVER['DOCUMENT_ROOT'].'/assets/images/attachment/client/'.$value->client_id)){
														echo '<a target="_blank" href="'.base_url('assets/images/attachment/client/'.$value->client_id).'?'.time().'"><i class="glyphicon glyphicon-picture"></i></a>';
													}else{
														echo '<i class="glyphicon glyphicon-picture"></i>';
													}
													?>
												</td>
												<!-- <td><?=get_location($value->client_company_location_id)->location_name?></td>
												<td><?=$value->client_company_website?></td> -->
												<td><?=get_user($value->client_user_id)->user_name?></td>
												<td><?=convert_datetime_to_date($value->client_modify)?></td>
												<!-- <td class="text-right">
													<span data-toggle="modal" data-target="#myModal" class="modal-btn" rel="<?=$value->client_id?>">
														<a data-toggle="tooltip" title="More">
															<i class="glyphicon glyphicon-chevron-right"></i>
														</a>
													</span>
												</td> -->
												<td class="text-right">
													<a href="<?=base_url('client/update/client_id/'.$value->client_id)?>" data-toggle="tooltip" title="Update">
														<i class="glyphicon glyphicon-edit"></i>
													</a>
												</td>
												<td class="text-right">
													<?php if(!check_permission('client_delete', 'display')){ ?>
													<a onclick="check_delete(<?=$value->client_id?>);" data-toggle="tooltip" title="Remove" class="<?=check_permission('client_delete', 'disable')?>">
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