<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Stock report</title>

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
			$('input[name="salesorder_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');

			/*--------- date mask ---------*/
			$('.date-mask').mask('9999-99-99');

			/*--------- datetimepicker ---------*/
			$('.datetimepicker').datetimepicker({
				format: 'Y-MM-DD'
			});
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

					<h2 class="col-sm-12">Stock report</h2>

					<div class="content-column-area col-md-12 col-sm-12">

						<div class="fieldset">
							<?=$this->session->tempdata('alert');?>
							<div class="search-area">

								<form salesorder="form" method="get">
									<input type="hidden" name="salesorder_id" />
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td width="90%">
                                                <div class="row">
                                                    <div class="col-sm-2"><h6>Exchange</h6></div>
                                                    <div class="col-sm-2">
                                                        <select id="exchange_type" name="exchange_type" data-placeholder="Type" class="chosen-select">
                                                            <?php
                                                            foreach($types as $key => $value){
                                                                $selected = ($value->type_name == $this->uri->uri_to_assoc()['exchange_type']) ? ' selected="selected"' : "" ;
                                                                echo '<option value="'.$value->type_name.'"'.$selected.'>Stock '.$value->type_name.'</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <select id="exchange_product_id" name="exchange_product_id" data-placeholder="Product" class="chosen-select">
                                                            <option value></option>
                                                            <?php foreach($exchange_product_ids as $key => $value){ ?>
                                                                <option value="<?=$value->exchange_product_id?>">
                                                                    <?php
                                                                    $thisProduct = get_product($value->exchange_product_id);
                                                                    echo $thisProduct->product_code.' - '.$thisProduct->product_name;
                                                                    ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
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
								<form name="list" action="<?=base_url('salesorder/delete')?>" method="post">
									<input type="hidden" name="salesorder_id" />
									<input type="hidden" name="salesorder_delete_reason" />
									<div class="page-area">
										<span class="btn btn-sm btn-default"><?php print_r($num_rows); ?></span>
										<?=$this->pagination->create_links()?>
                                        <a href="<?=base_url('stockreport/export'.get_uri_string_parameters($this->uri->uri_string()))?>" class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" data-original-title="Export">
                                            <i class="glyphicon glyphicon-export"></i>
                                        </a>
									</div>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <?php foreach ($th_header as $key => $value) {
                                                echo '<th>'.$value.'</th>';
                                            } ?>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach($td_body as $key => $value){
                                            ?>
                                            <tr>
                                                <?php
                                                for($i=0;$i<count($th_header);$i++){
                                                    echo '<td>'.$value[$i].'</td>';
                                                }
                                                ?>
                                            </tr>
                                            <?php
                                        }
                                        ?>

                                        <?php if(!$td_body){ ?>
                                            <tr>
                                                <td colspan="11">No record found</td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                        <?php if($td_body){ ?>
                                            <tfoot>
                                            <tr>
                                                <?php foreach ($th_footer as $key => $value) {
                                                    echo '<th>'.$value.'</th>';
                                                } ?>
                                            </tr>
                                            </tfoot>
                                        <?php } ?>
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