<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Attribute management</title>

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
		var attributes = <?=(!empty($attributes)) ? json_encode($attributes) : '[]'?>;

		$(function(){
			$('input[name="attribute_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');

			/*--------- jQuery validator ---------*/
			jQuery.validator.addMethod("attributeNameCheckDuplicate", function(value, element) {
				thisResult = true;
				$(attributes).each(function(key, val){
					if(value.toUpperCase() == val.attribute_name.toUpperCase()){
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
				$('input[name="attribute_id"]').val(id);
				$('input[name="attribute_delete_reason"]').val(encodeURI(answer));
				$('form[name="list"]').submit();
			}else{
				return false;
			}
		}

		function login_as(id){
			$('input[name="attribute_id"]').val(id);
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

					<h2 class="col-sm-12"><a href="<?=base_url('attribute')?>">Attribute management</a> > <?=($this->router->fetch_method() == 'update') ? 'Upate' : 'Insert'?> attribute</h2>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="attribute_id" value="<?=$attribute->attribute_id?>" />
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
											<label for="attribute_name">Name <span class="highlight">*</span></label>
											<input id="attribute_name" name="attribute_name" type="text" class="form-control input-sm required attributeNameCheckDuplicate" placeholder="Name" value="<?=$attribute->attribute_name?>" />
										</p>
									</div>
									<div class="col-sm-4 col-xs-12">
										<h4 class="corpcolor-font">Related information</h4>
                                        <p class="form-group">
                                            <label for="attribute_type">Type</label>
                                            <select id="attribute_type" name="attribute_type" data-placeholder="Status" class="chosen-select required">
                                                <option value></option>
                                                <?php
                                                if($attribute->attribute_type == ''){
                                                    $attribute->attribute_type = 'hkd';
                                                }
                                                foreach($types as $key => $value){
                                                    $selected = ($value->type_name == $attribute->attribute_type) ? ' selected="selected"' : "" ;
                                                    echo '<option value="'.$value->type_name.'"'.$selected.'>'.strtoupper($value->type_name).'</option>';
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

					<h2 class="col-sm-12">Attribute management</h2>

					<div class="content-column-area col-md-12 col-sm-12">

						<div class="fieldset">
							<div class="search-area">

								<form attribute="form" method="get">
									<input type="hidden" name="attribute_id" />
                                    <input type="hidden" name="attribute_type" value="<?=$this->uri->segment(4)?>" />
									<table>
										<tbody>
											<tr>
												<td width="90%">
													<div class="row">
														<div class="col-sm-2"><h6>Attribute</h6></div>
														<div class="col-sm-2">
															<input type="text" name="attribute_id" class="form-control input-sm" placeholder="#" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="attribute_name_like" class="form-control input-sm" placeholder="Attribute name" value="" />
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
								<form name="list" action="<?=base_url('attribute/delete')?>" method="post">
									<input type="hidden" name="attribute_id" />
									<input type="hidden" name="attribute_delete_reason" />
									<div class="page-area">
										<span class="btn btn-sm btn-default"><?php print_r($num_rows); ?></span>
										<?=$this->pagination->create_links()?>
									</div>
									<table id="attribute" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>#</th>
												<th>
													<a href="<?=get_order_link('attribute_name')?>">
														Name <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<th>
													<a href="<?=get_order_link('attribute_modify')?>">
														Modify <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<!-- <th width="40"></th> -->
												<th width="40"></th>
												<th width="40" class="text-right">
													<a href="<?=base_url('attribute/insert')?>" data-toggle="tooltip" title="Insert">
														<i class="glyphicon glyphicon-plus"></i>
													</a>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($attributes as $key => $value){ ?>
											<tr>
												<td title="<?=$value->attribute_id?>"><?=$key+1?></td>
												<td><?=ucfirst($value->attribute_name)?></td>
												<td><?=convert_datetime_to_date($value->attribute_modify)?></td>
												<!-- <td class="text-right">
													<span data-toggle="modal" data-target="#myModal" class="modal-btn" rel="<?=$value->attribute_id?>">
														<a data-toggle="tooltip" title="More">
															<i class="glyphicon glyphicon-chevron-right"></i>
														</a>
													</span>
												</td> -->
												<td class="text-right">
													<a href="<?=base_url('attribute/update/attribute_id/'.$value->attribute_id)?>" data-toggle="tooltip" title="Update">
														<i class="glyphicon glyphicon-edit"></i>
													</a>
												</td>
												<td class="text-right">
													<?php if(!check_permission('attribute_delete', 'display')){ ?>
													<a onclick="check_delete(<?=$value->attribute_id?>);" data-toggle="tooltip" title="Remove" class="<?=check_permission('attribute_delete', 'disable')?>">
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