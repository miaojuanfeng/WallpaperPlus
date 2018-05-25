<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Product preset</title>

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
			$('input[name="product_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');

            $(document).on('change', 'select[name="product_vendor_id"]', function(){
                product_vendor_loader();
            });
		});

		function check_delete(id){
			var answer = prompt("Confirm delete?");
			if(answer){
				$('input[name="product_id"]').val(id);
				$('input[name="product_delete_reason"]').val(encodeURI(answer));
				$('form[name="list"]').submit();
			}else{
				return false;
			}
		}

		function login_as(id){
			$('input[name="product_id"]').val(id);
			$('input[name="act"]').val('login_as');
			$('form[name="list"]').submit();
		}

        function product_vendor_loader(){
            $('.scriptLoader').load('/load', {'thisTableId': 'productVendorLoader', 'thisRecordId': $('select[name="product_vendor_id"]').val(), 't': timestamp()}, function(){
                productVendorLoader();
            });
        }
		</script>
	</head>

	<body>

		<?php $this->load->view('inc/header-area.php'); ?>

		








































		<?php if($this->router->fetch_method() == 'update' || $this->router->fetch_method() == 'insert'){ ?>
		<div class="content-area">

			<div class="container-fluid">
				<div class="row">

					<h2 class="col-sm-12"><a href="<?=base_url('product')?>">Product preset</a> > <?=($this->router->fetch_method() == 'update') ? 'Update' : 'Insert'?> product</h2>

					<div class="col-sm-12">
						<form method="post">
							<input type="hidden" name="product_id" value="<?=$product->product_id?>" />
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
                                            <label for="product_vendor_id">Vendor <span class="highlight">*</span></label>
                                            <select id="product_vendor_id" name="product_vendor_id" data-placeholder="Vendor" class="chosen-select required">
                                                <option value></option>
                                                <?php
                                                foreach($vendors as $key1 => $value1){
                                                    $selected = ($value1->vendor_id == $product->product_vendor_id) ? ' selected="selected"' : "" ;
                                                    echo '<option value="'.$value1->vendor_id.'"'.$selected.'>'.$value1->vendor_company_name.' '.$value1->vendor_firstname.' '.$value1->vendor_lastname.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </p>
										<p class="form-group">
											<label for="product_code">Code <span class="highlight">*</span></label>
											<input id="product_code" name="product_code" type="text" class="form-control input-sm required" placeholder="Code" value="<?=$product->product_code?>" />
										</p>
                                        <p class="form-group">
                                            <label for="product_wpp_code">WPP code <span class="highlight">*</span></label>
                                            <input id="product_wpp_code" name="product_wpp_code" type="text" class="form-control input-sm required" placeholder="WPP code" value="<?=$product->product_wpp_code?>" />
                                        </p>
										<div class="form-group">
											<label for="product_name">Name <span class="highlight">*</span></label>
											<input id="product_name" name="product_name" type="text" class="form-control input-sm required" placeholder="Name" value="<?=$product->product_name?>" />
										</div>
										<div class="form-group">
											<label for="product_detail">Detail <span class="highlight">*</span></label>
											<textarea id="product_detail" name="product_detail" class="form-control input-sm required" placeholder="Detail" rows="5"><?=$product->product_detail?></textarea>
										</div>
										<div class="form-group">
                                            <label for="product_unit_id">Unit</label>
                                            <select id="product_unit_id" name="product_unit_id" data-placeholder="Unit" class="chosen-select required">
                                                <option value></option>
                                                <?php
                                                foreach($units as $key => $value){
                                                    $selected = ($value->unit_id == $product->product_unit_id) ? ' selected="selected"' : "" ;
                                                    echo '<option value="'.$value->unit_id.'"'.$selected.'>'.$value->unit_name.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="product_weight">Weight</label>
                                            <input id="product_weight" name="product_weight" type="text" class="form-control input-sm required" placeholder="Weight" value="<?=$product->product_weight?>" />
                                            <small>Eg: 1kg, 1g</small>
                                        </div>
										<div class="form-group">
											<label for="product_cost">Cost</label>
                                            <div class="input-group">
                                                <span class="input-group-addon product-currency"><?=$product->product_vendor_id?get_currency(get_vendor($product->product_vendor_id)->vendor_currency_id)->currency_name:'HKD'?></span>
											    <input id="product_cost" name="product_cost" type="number" min="0" class="form-control input-sm" placeholder="Cost" value="<?=$product->product_cost?>" />
                                            </div>
                                        </div>
										<div class="form-group">
											<label for="product_price">Price</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">HKD</span>
                                                <input id="product_price_hkd" name="product_price_hkd" type="number" min="0" class="form-control input-sm" placeholder="Price HKD" value="<?=$product->product_price_hkd?>" />
                                            </div>
										</div>
                                        <div class="form-group">
                                            <label for="product_link">Link</label>
                                            <input id="product_link" name="product_link" type="text" class="form-control input-sm" placeholder="Link" value="<?=$product->product_link?>" />
                                        </div>
									</div>
									<div class="col-sm-4 col-xs-12">
										<h4 class="corpcolor-font">Related information</h4>
										<p class="form-group">
											<label for="product_brand_id">Brand <span class="highlight">*</span></label>
											<select id="product_brand_id" name="product_brand_id" data-placeholder="Brand" class="chosen-select required">
												<option value></option>
												<?php
												foreach($brands as $key1 => $value1){
													$selected = ($value1->brand_id == $product->product_brand_id) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value1->brand_id.'"'.$selected.'>'.$value1->brand_name.'</option>';
												}
												?>
											</select>
										</p>
										<p class="form-group">
											<label for="product_category_id">Category <span class="highlight">*</span></label>
											<select id="product_category_id" name="product_category_id" data-placeholder="Category" class="chosen-select required">
												<option value></option>
												<?php
												foreach($categorys as $key1 => $value1){
													$selected = ($value1->category_id == $product->product_category_id) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value1->category_id.'"'.$selected.'>'.$value1->category_name.'</option>';
												}
												?>
											</select>
										</p>
                                        <p class="form-group">
                                            <label for="z_product_attribute_attribute_id">Size <span class="highlight">*</span></label>
                                            <select id="z_product_attribute_attribute_id" name="z_product_attribute_attribute_id[]" data-placeholder="Size" class="chosen-select required">
                                                <option value></option>
                                                <?php
                                                foreach($sizes as $key1 => $value1){
                                                    $selected = in_array($value1->attribute_id, $z_product_attribute_ids) ? ' selected="selected"' : "" ;
                                                    echo '<option value="'.$value1->attribute_id.'"'.$selected.'>'.$value1->attribute_name.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </p>
                                        <p class="form-group">
                                            <label for="z_product_attribute_attribute_id">Color <span class="highlight">*</span></label>
                                            <select id="z_product_attribute_attribute_id" name="z_product_attribute_attribute_id[]" data-placeholder="Color" class="chosen-select required" multiple="multiple">
                                                <option value></option>
                                                <?php
                                                foreach($colors as $key1 => $value1){
                                                    $selected = in_array($value1->attribute_id, $z_product_attribute_ids) ? ' selected="selected"' : "" ;
                                                    echo '<option value="'.$value1->attribute_id.'"'.$selected.'>'.$value1->attribute_name.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </p>
                                        <p class="form-group">
                                            <label for="z_product_attribute_attribute_id">Style <span class="highlight">*</span></label>
                                            <select id="z_product_attribute_attribute_id" name="z_product_attribute_attribute_id[]" data-placeholder="Style" class="chosen-select required" multiple="multiple">
                                                <option value></option>
                                                <?php
                                                foreach($styles as $key1 => $value1){
                                                    $selected = in_array($value1->attribute_id, $z_product_attribute_ids) ? ' selected="selected"' : "" ;
                                                    echo '<option value="'.$value1->attribute_id.'"'.$selected.'>'.$value1->attribute_name.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </p>
                                        <p class="form-group">
                                            <label for="z_product_attribute_attribute_id">Usage <span class="highlight">*</span></label>
                                            <select id="z_product_attribute_attribute_id" name="z_product_attribute_attribute_id[]" data-placeholder="Usage" class="chosen-select required">
                                                <option value></option>
                                                <?php
                                                foreach($usages as $key1 => $value1){
                                                    $selected = in_array($value1->attribute_id, $z_product_attribute_ids) ? ' selected="selected"' : "" ;
                                                    echo '<option value="'.$value1->attribute_id.'"'.$selected.'>'.$value1->attribute_name.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </p>
                                        <p class="form-group">
                                            <label for="z_product_attribute_attribute_id">Material <span class="highlight">*</span></label>
                                            <select id="z_product_attribute_attribute_id" name="z_product_attribute_attribute_id[]" data-placeholder="Material" class="chosen-select required">
                                                <option value></option>
                                                <?php
                                                foreach($materials as $key1 => $value1){
                                                    $selected = in_array($value1->attribute_id, $z_product_attribute_ids) ? ' selected="selected"' : "" ;
                                                    echo '<option value="'.$value1->attribute_id.'"'.$selected.'>'.$value1->attribute_name.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </p>
                                        <p class="form-group">
                                            <label for="z_product_attribute_attribute_id">Keyword <span class="highlight">*</span></label>
                                            <select id="z_product_attribute_attribute_id" name="z_product_attribute_attribute_id[]" data-placeholder="Keyword" class="chosen-select required" multiple="multiple">
                                                <option value></option>
                                                <?php
                                                foreach($keywords as $key1 => $value1){
                                                    $selected = in_array($value1->attribute_id, $z_product_attribute_ids) ? ' selected="selected"' : "" ;
                                                    echo '<option value="'.$value1->attribute_id.'"'.$selected.'>'.$value1->attribute_name.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </p>
<!--                                        <p class="form-group">-->
<!--                                            <label for="product_type_id">Type <span class="highlight">*</span></label>-->
<!--                                            <select id="product_type_id" name="product_type_id" data-placeholder="Type" class="chosen-select required">-->
<!--                                                <option value></option>-->
<!--                                                --><?php
//                                                foreach($types as $key1 => $value1){
//                                                    $selected = ($value1->type_id == $product->product_type_id) ? ' selected="selected"' : "" ;
//                                                    echo '<option value="'.$value1->type_id.'"'.$selected.'>'.$value1->type_name.'</option>';
//                                                }
//                                                ?>
<!--                                            </select>-->
<!--                                        </p>-->
                                        <p class="form-group">
                                            <label for="product_team_id">Team <span class="highlight">*</span></label>
                                            <select id="product_team_id" name="product_team_id" data-placeholder="Team" class="chosen-select required">
                                                <option value></option>
                                                <?php
                                                foreach($teams as $key1 => $value1){
                                                    $selected = ($value1->team_id == $product->product_team_id) ? ' selected="selected"' : "" ;
                                                    echo '<option value="'.$value1->team_id.'"'.$selected.'>'.$value1->team_name.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </p>
										<!-- <p class="form-group">
											<label for="product_vendor_id">Vendor <span class="highlight">*</span></label>
											<select id="product_vendor_id" name="product_vendor_id" data-placeholder="Vendor" class="chosen-select required">
												<option value></option>
												<?php
												foreach($vendors as $key1 => $value1){
													$selected = ($value1->vendor_id == $product->product_vendor_id) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value1->vendor_id.'"'.$selected.'>'.$value1->vendor_company_name.'</option>';
												}
												?>
											</select>
										</p> -->
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

					<h2 class="col-sm-12">Product preset</h2>

					<div class="content-column-area col-md-12 col-sm-12">

						<div class="fieldset">
							<div class="search-area">

								<form product="form" method="get">
									<input type="hidden" name="product_id" />
									<table>
										<tbody>
											<tr>
												<td width="90%">
													<div class="row">
														<div class="col-sm-2"><h6>Product</h6></div>
														<div class="col-sm-2">
															<input type="text" name="product_id" class="form-control input-sm" placeholder="#" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="product_code_like" class="form-control input-sm" placeholder="Product code" value="" />
														</div>
                                                        <div class="col-sm-2">
                                                            <input type="text" name="product_wpp_code_like" class="form-control input-sm" placeholder="Product wpp code" value="" />
                                                        </div>
														<div class="col-sm-2">
															<input type="text" name="product_name_like" class="form-control input-sm" placeholder="Product name" value="" />
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
								<form name="list" action="<?=base_url('product/delete')?>" method="post">
									<input type="hidden" name="product_id" />
									<input type="hidden" name="product_delete_reason" />
									<div class="page-area">
										<span class="btn btn-sm btn-default"><?php print_r($num_rows); ?></span>
										<?=$this->pagination->create_links()?>
									</div>
									<table id="product" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>#</th>
												<th>
													<a href="<?=get_order_link('product_code')?>">
														Code <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
                                                <th>
                                                    <a href="<?=get_order_link('product_wpp_code')?>">
                                                        WPP code <i class="glyphicon glyphicon-sort corpcolor-font"></i>
                                                    </a>
                                                </th>
												<th>
													<a href="<?=get_order_link('product_name')?>">
														Name <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<th>
													<a href="<?=get_order_link('product_cost')?>">
														Cost <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<th>
													<a href="<?=get_order_link('product_price_hkd')?>">
														Price <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<th>
													<a href="<?=get_order_link('product_modify')?>">
														Modify <i class="glyphicon glyphicon-sort corpcolor-font"></i>
													</a>
												</th>
												<!-- <th width="40"></th> -->
												<th width="40"></th>
												<th width="40" class="text-right">
													<a href="<?=base_url('product/insert')?>" data-toggle="tooltip" title="Insert">
														<i class="glyphicon glyphicon-plus"></i>
													</a>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($products as $key => $value){ ?>
											<tr>
												<td title="<?=$value->product_id?>"><?=$key+1?></td>
                                                <td><?=$value->product_code?></td>
												<td><?=$value->product_wpp_code?></td>
												<td><?=$value->product_name?></td>
												<td><?='HKD '.$value->product_cost?></td>
												<td><?='HKD '.$value->product_price_hkd?></td>
												<td><?=convert_datetime_to_date($value->product_modify)?></td>
												<!-- <td class="text-right">
													<span data-toggle="modal" data-target="#myModal" class="modal-btn" rel="<?=$value->product_id?>">
														<a data-toggle="tooltip" title="More">
															<i class="glyphicon glyphicon-chevron-right"></i>
														</a>
													</span>
												</td> -->
												<td class="text-right">
													<a href="<?=base_url('product/update/product_id/'.$value->product_id)?>" data-toggle="tooltip" title="Update">
														<i class="glyphicon glyphicon-edit"></i>
													</a>
												</td>
												<td class="text-right">
													<?php if(!check_permission('product_delete', 'display')){ ?>
													<a onclick="check_delete(<?=$value->product_id?>);" data-toggle="tooltip" title="Remove" class="<?=check_permission('product_delete', 'disable')?>">
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

<div class="scriptLoader"></div>

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