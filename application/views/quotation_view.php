<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Quotation management</title>

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
		quotationitem_row_id = 99999999;

		$(function(){
			$('input[name="quotation_id"]').focus();

			/* pagination */
			$('.pagination-area>a, .pagination-area>strong').addClass('btn btn-sm btn-primary');
			$('.pagination-area>strong').addClass('disabled');

			/*--------- date mask ---------*/
			$('.date-mask').mask('9999-99-99');

			/*--------- datetimepicker ---------*/
			$('.datetimepicker').datetimepicker({
				format: 'Y-MM-DD'
			});

			/* quotationitem-insert-btn */
			$(document).on('click', '.quotationitem-insert-btn', function(){
				add_quotationitem_row();
			});

			/* quotationitem-delete-btn */
			$(document).on('click', '.quotationitem-delete-btn', function(){
				if(confirm('Confirm delete?')){
					$(this).closest('tr').remove();
					calc();
				}else{
					return false;
				}
			});

			/* client loader */
			<?php if($this->router->fetch_method() == 'insert' && isset($this->uri->uri_to_assoc()['quotation_client_id'])){ ?>
			client_loader();
			<?php } ?>
			$(document).on('change', 'select[name="quotation_client_id"]', function(){
				client_loader();
			});

			/* product loader */
			// $(document).on('change', 'input[name="quotationitem_product_id[]"]', function(){
			// 	product_loader($(this));
			// });

            $(document).on('change', 'select[name="quotation_display_code"]', function(){
                $('input[name="quotationitem_product_id[]"]').each(function(){
                    product_code_loader($(this));
                });
            });

			/* index_part_number */
			document_display_number();
			$(document).on('change', 'select[name="quotation_display_number"]', function(){
				document_display_number();
			});

			/* trigger calc */
			$(document).on('blur', 'input[name="quotationitem_product_price[]"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="quotationitem_quantity[]"]', function(){
				calc();
			});
			$(document).on('blur', 'input[name="quotation_discount"]', function(){
				calc();
			});
            $(document).on('blur', 'input[name="quotation_freight"]', function(){
                calc();
            });
			$(document).on('change', 'select[name="quotation_currency"]', function(){
				$.each($('input[name="quotationitem_product_id[]"]'), function(key, val){
				    if( $(this).val() != "" ) {
                        product_loader($(this));
                    }
				});
                exchange_rate_loader($(this));
			});

			/* up & down btn */
			$(document).on('click', '.up-btn', function(){
				if($(this).closest('tr').index() > 0){
					$('table.list tbody tr').eq($(this).closest('tr').index()).after($('table.list tbody tr').eq($(this).closest('tr').index() - 1));
				}
			});
			$(document).on('click', '.down-btn', function(){
				if($('table.list tbody tr').length > $(this).closest('tr').index()){
					$('table.list tbody tr').eq($(this).closest('tr').index()).before($('table.list tbody tr').eq($(this).closest('tr').index() + 1));
				}
			});

			/* textarea auto height */
			textarea_auto_height();
			$(document).on('keyup', 'textarea', function(){
				textarea_auto_height();
			});

			/* document auto zoom */
			document_auto_zoom();
			$(window).resize(function(){
				document_auto_zoom();
			});

			/* more detail */
			$(document).on('click', 'a[id^="more-"]', function(){
				thisObject = $(this);
				thisQuotationNumber = thisObject.attr('id').replace('more-', '');
				if($('.more-' + thisQuotationNumber).css('display') != 'table-row'){
					$('.more-area').css('display', 'none');
					$.post('/load/', {'thisTableId': 'quotationLoader', 'thisRecordId': thisQuotationNumber, 't': timestamp()}, function(data){
						thisObject.closest('tr').after(data);
					});
				}else{
					$('.more-' + thisQuotationNumber).css('display', 'none');
				}
			});

			$('input[name^="quotationitem_product_code[]"]').each(function(){
				$(this).rules("add", {
					required: true,
					messages: {
						required: "This is a required field"
					}
				});
			});

			if($.validator){
			//fix: when several input elements shares the same name, but has different id-ies....
				$.validator.prototype.elements = function(){
					var validator = this,
					rulesCache = {};
					// select all valid inputs inside the form (no submit or reset buttons)
					// workaround $Query([]).add until http://dev.jquery.com/ticket/2114 is solved
					return $([]).add(this.currentForm.elements).filter(":input").not(":submit, :reset, :image, [disabled]").not(this.settings.ignore).filter(function() {
					// 这里加入ID判断
					var elementIdentification = this.id || this.name; ! elementIdentification && validator.settings.debug && window.console && console.error("%o has no id nor name assigned", this);
					// select only the first element for each name, and only those with rules specified
					if (elementIdentification in rulesCache || !validator.objectLength($(this).rules())) return false;
					rulesCache[elementIdentification] = true;
					return true;
					});
				};
			}

			$('#quotation_currency').val('hkd');
            $('#quotation_currency').trigger('chosen:updated');
            $('#quotation_currency').change();
		});

		function document_display_number(){
			$('.index_number').css('display', 'none');
			$('.part_number').css('display', 'none');
			$('.' + $('select[name="quotation_display_number"]').val()).fadeIn();
		}

		function document_auto_zoom(){
			$('.document-a4').css('zoom', $('.document-area').width() / 785);
		}

		function client_loader(){
			$('.scriptLoader').load('/load', {'thisTableId': 'clientLoader', 'thisRecordId': $('select[name="quotation_client_id"]').val(), 't': timestamp()}, function(){
				clientLoader();
			});
		}

		function product_loader(thisObject){
			thisRow = $(thisObject).closest('tr').index();
			thisCurrency = $('select[name="quotation_currency"]').val();
			$('.scriptLoader').load('/load', {'thisTableId': 'quotationProductLoader', 'thisRecordId': $(thisObject).val(), 'thisCurrency': thisCurrency, 'thisRow': thisRow, 't': timestamp()}, function(){
				quotationProductLoader();
				textarea_auto_height();
				calc();
			});
		}

        function product_code_loader(thisObject){
            thisRow = $(thisObject).closest('tr').index();
            thisCurrency = $('select[name="quotation_currency"]').val();
            $('.scriptLoader').load('/load', {'thisTableId': 'quotationProductCodeLoader', 'thisRecordId': $(thisObject).val(), 'thisCurrency': thisCurrency, 'thisRow': thisRow, 't': timestamp()}, function(){
                quotationProductCodeLoader();
                textarea_auto_height();
                // calc();
            });
        }

        function exchange_rate_loader(thisObject){
            $('.scriptLoader').load('/load', {'thisTableId': 'exchangeRateLoader', 'thisRecordId': $('select[name="quotation_currency"]').val(), 't': timestamp()}, function(){
                exchangeRateLoader();
            });
        }

		function textarea_auto_height(){
			$.each($('textarea'), function(key, val){
				$(this).attr('rows', $(this).val().split('\n').length + 1);
			});
		}

		function calc(){
			var total = 0;
			$.each($('table.list tbody tr'), function(key, val){
				$(this).find('input[name="quotationitem_subtotal[]"]').val(parseFloat($(this).find('input[name="quotationitem_product_price[]"]').val() * $(this).find('input[name="quotationitem_quantity[]"]').val()).toFixed(2)).css('display', 'none').fadeIn();
				total += parseFloat($(this).find('input[name="quotationitem_subtotal[]"]').val());
			});
			$('input[name="quotation_total"]').val(parseFloat(total - parseFloat($('input[name="quotation_discount"]').val()) + parseFloat($('input[name="quotation_freight"]').val()) ).toFixed(2)).css('display', 'none').fadeIn();
		}

		function check_delete(id){
			var answer = prompt("Confirm delete?");
			if(answer){
				$('input[name="quotation_id"]').val(id);
				$('input[name="quotation_delete_reason"]').val(encodeURI(answer));
				$('form[name="list"]').submit();
			}else{
				return false;
			}
		}

		<?php if($this->router->fetch_method() == 'update' || $this->router->fetch_method() == 'insert' || $this->router->fetch_method() == 'duplicate'){ ?>
		function add_quotationitem_row(){
			quotationitem_row = '';
			quotationitem_row += '<tr>';
			quotationitem_row += '<td>';
			quotationitem_row += '<div>';
			quotationitem_row += '<input name="quotationitem_id[]" type="hidden" value="" />';
			quotationitem_row += '<input name="quotationitem_quotation_id[]" type="hidden" value="" />';
			quotationitem_row += '<input name="quotationitem_product_type_name[]" type="hidden" value="" />';
			quotationitem_row += '<input id="quotationitem_product_code_' + quotationitem_row_id + '" name="quotationitem_product_code[]" type="text" class="form-control input-sm required" placeholder="Code" value="" />';
			quotationitem_row_id -= 1;
			quotationitem_row += '</div>';
			// quotationitem_row += '<div class="margin-top-10">';
			// quotationitem_row += '<a class="btn btn-sm btn-primary quotationitem-delete-btn" data-toggle="tooltip" title="Delete">';
			// quotationitem_row += '<i class="glyphicon glyphicon-remove"></i>';
			// quotationitem_row += '</a>';
			// quotationitem_row += '</div>';
			quotationitem_row += '<div class="margin-top-10">';
			quotationitem_row += '<div class="btn-group">';
			quotationitem_row += '<button type="button" class="btn btn-sm btn-primary quotationitem-delete-btn"><i class="glyphicon glyphicon-remove"></i></button>';
			quotationitem_row += '<button type="button" class="btn btn-sm btn-primary up-btn"><i class="glyphicon glyphicon-chevron-up"></i></button>';
			quotationitem_row += '<button type="button" class="btn btn-sm btn-primary down-btn"><i class="glyphicon glyphicon-chevron-down"></i></button>';
			quotationitem_row += '</div>';
			quotationitem_row += '</div>';
			quotationitem_row += '</td>';
			quotationitem_row += '<td>';
			quotationitem_row += '<div>';
            quotationitem_row += '<input id="quotationitem_product_id" name="quotationitem_product_id[]" type="hidden" class="form-control input-sm" placeholder="Product" value="" />';
            quotationitem_row += '<input type="button" class="form-control input-sm showModal" value="Select a product" />';
			quotationitem_row += '</div>';
			quotationitem_row += '<div class="margin-top-10">';
            // quotationitem_row += '<input id="quotationitem_product_name" name="quotationitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="" />';
            quotationitem_row += '<div class="input-group">';
            quotationitem_row += '<span class="input-group-addon corpcolor-font">Title</span>';
            quotationitem_row += '<input id="quotationitem_product_name" name="quotationitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="" />';
            quotationitem_row += '</div>';
            quotationitem_row += '</div>';
			quotationitem_row += '<div>';
			quotationitem_row += '<textarea id="quotationitem_product_detail" name="quotationitem_product_detail[]" class="form-control input-sm" placeholder="Detail"></textarea>';
			quotationitem_row += '</div>';
            quotationitem_row += '<div>';
            // quotationitem_row += '<input id="quotationitem_product_name" name="quotationitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="" />';
            quotationitem_row += '<div class="input-group">';
            quotationitem_row += '<span class="input-group-addon corpcolor-font">Link</span>';
            quotationitem_row += '<input id="quotationitem_product_link" name="quotationitem_product_link[]" type="text" class="form-control input-sm" placeholder="Link" value="" />';
            quotationitem_row += '</div>';
            quotationitem_row += '</div>';
			quotationitem_row += '</td>';
			quotationitem_row += '<td>';
			quotationitem_row += '<input id="quotationitem_product_price" name="quotationitem_product_price[]" type="number" min="0" class="form-control input-sm" placeholder="Price" value="" />';
			quotationitem_row += '</td>';
			quotationitem_row += '<td>';
            quotationitem_row += '<div class="input-group">';
			quotationitem_row += '<input id="quotationitem_quantity" name="quotationitem_quantity[]" type="number" min="0" class="form-control input-sm" placeholder="Quantity" value="1" />';
            quotationitem_row += '<input id="quotationitem_unit" name="quotationitem_unit[]" type="hidden" />';
			quotationitem_row += '<span class="input-group-addon quotationitem_unit">Unit</span>';
            quotationitem_row += '</div>';
			quotationitem_row += '</td>';
			quotationitem_row += '<td>';
			quotationitem_row += '<input readonly="readonly" id="quotationitem_subtotal" name="quotationitem_subtotal[]" type="text" class="form-control input-sm" placeholder="Subtotal" value="" />';
			quotationitem_row += '</td>';
			quotationitem_row += '</tr>';

			$('table.list tbody').append(quotationitem_row);
			$('.chosen-select').chosen();
		}
		<?php } ?>
		</script>
	</head>

	<body>

		<?php $this->load->view('inc/header-area.php'); ?>

		








































		<?php if($this->router->fetch_method() == 'update' || $this->router->fetch_method() == 'insert' || $this->router->fetch_method() == 'duplicate'){ ?>
		<div class="content-area">

			<div class="container-fluid">
				<div class="row">

					<h2 class="col-sm-12"><a href="<?=base_url('quotation')?>">Quotation management</a> > <?=($this->router->fetch_method() == 'update') ? 'Update' : 'Insert'?> quotation</h2>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="quotation_id" value="<?=$quotation->quotation_id?>" />
							<input type="hidden" name="quotation_version" value="<?=$quotation->quotation_version?>" />
                            <input type="hidden" name="quotation_client_company_code" value="<?=$quotation->quotation_client_company_code?>" />
							<input type="hidden" name="quotation_serial" value="<?=$quotation->quotation_serial?>" />
							<input type="hidden" name="referrer" value="<?=$this->agent->referrer()?>" />
							<div class="fieldset">
								<?=$this->session->tempdata('alert');?>
								<div class="row">
									
									<div class="col-sm-3 col-xs-12 pull-right">
										<blockquote>
											<h4 class="corpcolor-font">Instructions</h4>
											<p><span class="highlight">*</span> is a required field</p>
											<p>
												<b>BOLD</b>: &lt;b&gt;content&lt;/b&gt;
												<br /><b>ITALIC</b>: &lt;i&gt;content&lt;/i&gt;
												<br /><b>UNDERLINE</b>: &lt;u&gt;content&lt;/u&gt;
											</p>
											<?php
											for($i=1; $i<=get_quotation_version($quotation->quotation_number); $i++){
												echo '<div><a target="_blank" href="'.base_url('/assets/images/pdf/quotation/'.$quotation->quotation_number.'-v'.$i).'">'.$quotation->quotation_number.'-v'.$i.'</a></div>';
											}
											?>
										</blockquote>
										<p class="form-group">
                                            <?php if( $quotation->quotation_confirmed == 'Y' && $this->router->fetch_method() != 'duplicate' ) { ?>
                                                <button type="submit" name="action" value="approval" class="btn btn-sm btn-primary btn-block" data-toggle="tooltip" title="Can only be updated with approved code"><i class="glyphicon glyphicon-floppy-disk"></i> Approval</button>
                                            <?php }else{ ?>
                                                <button<?=($quotation->quotation_confirmed == 'Y' && $this->router->fetch_method() != 'duplicate') ? ' disabled="disabled"' : ''?> type="submit" name="action" value="save" class="btn btn-sm btn-primary btn-block"><i class="glyphicon glyphicon-floppy-disk"></i> Save</button>
                                            <?php } ?>
										</p>
										<?php if($this->router->fetch_method() == 'update'){ ?>
										<p class="form-group">
											<button<?=($quotation->quotation_confirmed == 'Y' || get_quotation_version($quotation->quotation_number) > $quotation->quotation_version) ? ' disabled="disabled"' : ''?> type="submit" name="action" value="reversion" class="btn btn-sm btn-primary btn-block"><i class="glyphicon glyphicon-refresh"></i> Reversion</button>
										</p>
										<p class="form-group">
											<a class="btn btn-sm btn-primary btn-block" href="<?=base_url('quotation/duplicate/quotation_id/'.$quotation->quotation_id)?>" data-toggle="tooltip" title="Duplicate"><i class="glyphicon glyphicon-duplicate"></i> Duplicate</a>
										</p>
										<p class="form-group">
											<a class="btn btn-sm btn-primary btn-block" target="_blank" href="<?=base_url('assets/images/pdf/quotation/'.$quotation->quotation_number.'-v'.$quotation->quotation_version.'.pdf?'.time())?>" data-toggle="tooltip" title="Print"><i class="glyphicon glyphicon-print"></i> Print</a>
										</p>
										<p class="form-group">
											<a<?=($quotation->quotation_confirmed == 'Y') ? ' disabled="disabled"' : ''?> class="btn btn-sm btn-primary btn-block<?=check_permission('salesorder_insert', 'disable')?>" href="<?=base_url('salesorder/insert/quotation_id/'.$quotation->quotation_id)?>" data-toggle="tooltip" title="Convert to Sales Order"><i class="glyphicon glyphicon-ok"></i> Convert to Sales Order</a>
											<!-- <button<?=($quotation->quotation_confirmed == 'Y') ? ' disabled="disabled"' : ''?> type="submit" name="action" value="confirm" class="btn btn-sm btn-primary btn-block"><i class="glyphicon glyphicon-ok"></i> Convert to Sales Order</button> -->
										</p>
										<?php } ?>
										<h4 class="corpcolor-font">Setting</h4>
										<p class="form-group">
											<label for="quotation_project_name">Project name <span class="highlight">*</span></label>
											<input id="quotation_project_name" name="quotation_project_name" type="text" class="form-control input-sm required" placeholder="Project name" value="<?=$quotation->quotation_project_name?>" />
										</p>
										<!-- <p class="form-group">
											<label for="quotation_language">Language</label>
											<select id="quotation_language" name="quotation_language" data-placeholder="Language" class="chosen-select required">
												<option value></option>
												<?php
												if($quotation->quotation_language == ''){
													$quotation->quotation_language = 'en';
												}
												foreach($languages as $key => $value){
													$selected = ($value->language_name == $quotation->quotation_language) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value->language_name.'"'.$selected.'>'.strtoupper($value->language_name).'</option>';
												}
												?>
											</select>
										</p> -->
                                        <p class="form-group">
                                            <label for="quotation_exchange_rate">Exchange rate <span class="highlight">*</span></label>
                                            <input id="quotation_exchange_rate" name="quotation_exchange_rate" type="text" readonly="readonly" class="form-control input-sm required" placeholder="Exchange rate" value="<?=$quotation->quotation_exchange_rate?>" />
                                        </p>
										<p class="form-group">
											<label for="quotation_currency">Currency</label>
											<select id="quotation_currency" name="quotation_currency" data-placeholder="Currency" class="chosen-select required">
												<option value></option>
												<?php
//												if($quotation->quotation_currency == ''){
//													$quotation->quotation_currency = 'hkd';
//												}
												foreach($currencys as $key => $value){
													$selected = ($value->currency_name == $quotation->quotation_currency) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value->currency_name.'"'.$selected.'>'.strtoupper($value->currency_name).'</option>';
												}
												?>
											</select>
										</p>
										<p class="form-group">
											<label for="quotation_display_number">Index number / Part number</label>
											<select id="quotation_display_number" name="quotation_display_number" data-placeholder="Index number / Part number" class="chosen-select required">
												<option value></option>
												<?php
												if($quotation->quotation_display_number == ''){
													$quotation->quotation_display_number = 'index_number';
												}
												foreach($display_numbers as $key => $value){
													$selected = ($value->display_number_name == $quotation->quotation_display_number) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value->display_number_name.'"'.$selected.'>'.strtoupper($value->display_number_name).'</option>';
												}
												?>
											</select>
										</p>
										<!-- <p class="form-group">
											<label for="attachment">Business registration</label>
											<input id="attachment" name="attachment" type="file" class="form-control input-sm" placeholder="Business registration" accept="image/*" />
										</p> -->
										<!-- <p class="form-group">
											<label for="attachment">
												Business registration
												<?php if(file_exists($_SERVER['DOCUMENT_ROOT'].'/assets/images/attachment/quotation/'.$quotation->quotation_id)){ ?>
												<a target="_blank" href="<?=base_url('assets/images/attachment/quotation/'.$quotation->quotation_id)?>"><i class="glyphicon glyphicon-picture"></i></a></span>
												<?php } ?>
											</label>
											<input id="attachment" name="attachment" type="file" class="form-control input-sm" placeholder="Business registration" accept="image/*, application/pdf" />
										</p> -->
										<?php
										if($this->router->fetch_method() == 'update'){
											switch(true){
												case in_array('1', $this->session->userdata('role')): // administrator
												case in_array('2', $this->session->userdata('role')): // boss
												case in_array('5', $this->session->userdata('role')): // operation manager
												case in_array('6', $this->session->userdata('role')): // operation
												case in_array('7', $this->session->userdata('role')): // account
													echo '<p class="form-group"><label for="quotation_status">Status</label><select id="quotation_status" name="quotation_status" data-placeholder="Status" class="chosen-select required"><option value></option>';
													foreach($statuss as $key => $value){
														$selected = ($value->status_name == $quotation->quotation_status) ? ' selected="selected"' : "" ;
														echo '<option value="'.$value->status_name.'"'.$selected.'>'.ucfirst($value->status_name).'</option>';
													}
													echo '</select></p>';
													break;
												case in_array('3', $this->session->userdata('role')): // sales manager
												case in_array('4', $this->session->userdata('role')): // sales
													echo '<p class="form-group">';
													echo '<label>Status</label>';
													echo '<input readonly="readonly" type="text" class="form-control input-sm required" value="'.ucfirst($quotation->quotation_status).'" />';
													echo '</p>';
													break;
											}
										}
										?>
										<!-- <p class="form-group">
											<label for="quotation_status">Status</label>
											<select id="quotation_status" name="quotation_status" data-placeholder="Status" class="chosen-select required">
												<option value></option>
												<?php
												foreach($statuss as $key => $value){
													$selected = ($value->status_name == $quotation->quotation_status) ? ' selected="selected"' : "" ;
													echo '<option value="'.$value->status_name.'"'.$selected.'>'.ucfirst($value->status_name).'</option>';
												}
												?>
											</select>
										</p> -->
                                        <p class="form-group0">
                                            <label for="quotation_display_code">Product display code</label>
                                            <select id="quotation_display_code" name="quotation_display_code" data-placeholder="Product display code" class="chosen-select required">
                                                <option value="wpp_code" <?=($quotation->quotation_display_code == "wpp_code") ? ' selected="selected"' : "" ;?>>WPP code</option>
                                                <option value="code" <?=($quotation->quotation_display_code == "code") ? ' selected="selected"' : "" ;?>>Code</option>
                                            </select>
                                        </p>
									</div>
									<div class="col-sm-9 col-xs-12">
										<h4 class="corpcolor-font">Quotation</h4>
										<div class="row">
											<div class="col-sm-6 col-xs-6">
												<table class="table table-condensed table-borderless">
													<tr>
														<td colspan="2">
															<select id="quotation_client_id" name="quotation_client_id" data-placeholder="Client" class="chosen-select">
																<option value></option>
																<?php
																foreach($clients as $key1 => $value1){
																	$selected = ($value1->client_id == $quotation->quotation_client_id) ? ' selected="selected"' : "" ;
																	echo '<option value="'.$value1->client_id.'"'.$selected.'>'.$value1->client_company_name.' '.$value1->client_firstname.' '.$value1->client_lastname.'</option>';
																}
																?>
															</select>
														</td>
													</tr>
													<tr>
														<td><label for="quotation_client_company_name">To</label></td>
														<td><input id="quotation_client_company_name" name="quotation_client_company_name" type="text" class="form-control input-sm required" placeholder="Company/Domain/Client" value="<?=$quotation->quotation_client_company_name?>" /></td>
													</tr>
													<tr>
														<td><label for="quotation_client_company_address">Address</label></td>
														<td><textarea id="quotation_client_company_address" name="quotation_client_company_address" class="form-control input-sm" placeholder="Address"><?=$quotation->quotation_client_company_address?></textarea></td>
													</tr>
													<tr>
														<td><label for="quotation_client_company_phone">Phone</label></td>
														<td><input id="quotation_client_company_phone" name="quotation_client_company_phone" type="text" class="form-control input-sm" placeholder="Phone" value="<?=$quotation->quotation_client_company_phone?>" /></td>
													</tr>
													<tr>
														<td><label for="quotation_client_phone">Mobile</label></td>
														<td><input id="quotation_client_phone" name="quotation_client_phone" type="text" class="form-control input-sm" placeholder="Fax" value="<?=$quotation->quotation_client_phone?>" /></td>
													</tr>
													<tr>
														<td><label for="quotation_client_email">Email</label></td>
														<td><input id="quotation_client_email" name="quotation_client_email" type="text" class="form-control input-sm" placeholder="Email" value="<?=$quotation->quotation_client_email?>" /></td>
													</tr>
													<tr>
														<td><label for="quotation_client_name">Attn</label></td>
														<td><input id="quotation_client_name" name="quotation_client_name" type="text" class="form-control input-sm required" placeholder="Attn." value="<?=$quotation->quotation_client_name?>" /></td>
													</tr>
												</table>
											</div>
											<div class="col-sm-1 col-xs-1">
											</div>
											<div class="col-sm-5 col-xs-5">
												<table class="table table-condensed table-borderless">
													<tr>
														<td><label for="quotation_number">Quotation#</label></td>
														<td>
															<div class="input-group">
																<input readonly="readonly" id="quotation_number" name="quotation_number" type="text" class="form-control input-sm" placeholder="Quotation#" value="<?=$quotation->quotation_number?>" />
                                                                <span class="input-group-addon"><?=$quotation->quotation_version?'R'.$quotation->quotation_version:'N/A'?></span>
															</div>
														</td>
													</tr>
													<tr>
														<td><label for="quotation_issue">Date</label></td>
														<td><input id="quotation_issue" name="quotation_issue" type="text" class="form-control input-sm date-mask required" placeholder="Issue date" value="<?=($quotation->quotation_issue != '') ? $quotation->quotation_issue : date('Y-m-d')?>" /></td>
													</tr>
													<tr>
														<td><label for="quotation_user_name">Sales</label></td>
														<td><input readonly="readonly" id="quotation_user_name" name="quotation_user_name" type="text" class="form-control input-sm required" placeholder="Sales" value="<?=$user->user_name?>" /></td>
													</tr>
													<!-- <tr>
														<td><label for="quotation_user_phone">Phone</label></td>
														<td><input id="quotation_user_phone" name="quotation_user_phone" type="text" class="form-control input-sm required" placeholder="Phone" value="<?=$user->user_phone?>" /></td>
													</tr>
													<tr>
														<td><label for="quotation_user_email">Email</label></td>
														<td><input id="quotation_user_email" name="quotation_user_email" type="text" class="form-control input-sm required" placeholder="Email" value="<?=$user->user_email?>" /></td>
													</tr> -->
													<!-- <tr>
														<td><label for="quotation_terms_id">Payment Terms</label></td>
														<td>
															<select id="quotation_terms_id" name="quotation_terms_id" data-placeholder="Terms" class="chosen-select required">
																<option value></option>
																<?php
																foreach($terms as $key1 => $value1){
																	$selected = ($value1->terms_id == $quotation->quotation_terms_id) ? ' selected="selected"' : "" ;
																	echo '<option value="'.$value1->terms_id.'"'.$selected.'>'.$value1->terms_name.'</option>';
																}
																?>
															</select>
														</td>
													</tr> -->
													<tr>
														<td><label for="quotation_expire">Expire Date</label></td>
														<td><input id="quotation_expire" name="quotation_expire" type="text" class="form-control input-sm date-mask" placeholder="Expire Date" value="<?=($quotation->quotation_expire != '' && $this->router->fetch_method() != 'duplicate') ? $quotation->quotation_expire : date('Y-m-d', strtotime('+14 days', time()))?>" /></td>
													</tr>
													<tr>
														<td><label for="approval_code">Approval code</label></td>
														<td><input id="approval_code" name="approval_code" type="text" class="form-control input-sm <?php if( $quotation->quotation_confirmed == 'Y' && $this->router->fetch_method() != 'duplicate' ) { echo "required"; }?>" placeholder="Approval code" value="" /></td>
													</tr>
												</table>
											</div>
										</div>
										<div class="list-area">
											<table class="table list" id="quotation">
												<thead>
													<tr>
														<th width="10%">
															<a class="btn btn-sm btn-primary quotationitem-insert-btn" data-toggle="tooltip" title="Insert">
																<i class="glyphicon glyphicon-plus"></i>
															</a>
														</th>
														<th>Detail</th>
														<th width="12%">Price</th>
														<th width="12%">Quantity</th>
														<th width="14%">Subtotal</th>
													</tr>
												</thead>
												<tbody class="trModal">
													<?php foreach($quotationitems as $key => $value){ ?>
													<tr>
														<td>
															<div>
																<input name="quotationitem_id[]" type="hidden" value="<?=$value->quotationitem_id?>" />
																<input name="quotationitem_quotation_id[]" type="hidden" value="<?=$value->quotationitem_quotation_id?>" />
																<input name="quotationitem_product_type_name[]" type="hidden" value="<?=$value->quotationitem_product_type_name?>" />
																<input id="quotationitem_product_code_<?=$key?>" name="quotationitem_product_code[]" type="text" class="form-control input-sm required" placeholder="Code" value="<?=$value->quotationitem_product_code?>" />
                                                            </div>
															<div class="margin-top-10">
																<div class="btn-group">
																	<button type="button" class="btn btn-sm btn-primary quotationitem-delete-btn"><i class="glyphicon glyphicon-remove"></i></button>
																	<button type="button" class="btn btn-sm btn-primary up-btn"><i class="glyphicon glyphicon-chevron-up"></i></button>
																	<button type="button" class="btn btn-sm btn-primary down-btn"><i class="glyphicon glyphicon-chevron-down"></i></button>
																</div>
															</div>
														</td>
														<td>
															<div>
                                                                <input id="quotationitem_product_id" name="quotationitem_product_id[]" type="hidden" class="form-control input-sm" placeholder="Product" value="<?=$value->quotationitem_product_id?>" />
                                                                <input type="button" class="form-control input-sm showModal" modal="product_select" value="Select a product"/>
															</div>
															<div class="margin-top-10">
																<div class="input-group">
																	<span class="input-group-addon corpcolor-font">Title</span>
																	<input id="quotationitem_product_name" name="quotationitem_product_name[]" type="text" class="form-control input-sm" placeholder="Name" value="<?=$value->quotationitem_product_name?>" />
																</div>
															</div>
															<div>
																<textarea id="quotationitem_product_detail" name="quotationitem_product_detail[]" class="form-control input-sm" placeholder="Detail"><?=$value->quotationitem_product_detail?></textarea>
															</div>
                                                            <div>
                                                                <div class="input-group">
                                                                    <span class="input-group-addon corpcolor-font">Link</span>
                                                                    <input id="quotationitem_product_link" name="quotationitem_product_link[]" type="text" class="form-control input-sm" placeholder="Link" value="<?=$value->quotationitem_product_link?>" />
                                                                </div>
                                                            </div>
														</td>
														<td>
															<input id="quotationitem_product_price" name="quotationitem_product_price[]" type="number" min="0" class="form-control input-sm" placeholder="Price" value="<?=$value->quotationitem_product_price?>" />
														</td>
														<td>
                                                            <div class="input-group">
                                                                <input id="quotationitem_quantity" name="quotationitem_quantity[]" type="number" min="0" class="form-control input-sm" placeholder="Quantity" value="<?=($value->quotationitem_quantity) ? $value->quotationitem_quantity : '1'?>" />
                                                                <input id="quotationitem_unit" name="quotationitem_unit[]" type="hidden" value="<?=$value->quotationitem_unit?>" />
                                                                <span class="input-group-addon quotationitem_unit"><?=($value->quotationitem_unit) ? $value->quotationitem_unit : 'Unit'?></span>
                                                            </div>
                                                        </td>
														<td>
															<input readonly="readonly" id="quotationitem_subtotal" name="quotationitem_subtotal[]" type="text" class="form-control input-sm" placeholder="Subtotal" value="<?=$value->quotationitem_subtotal?>" />
														</td>
													</tr>
													<?php } ?>
												</tbody>
												<tfoot>
													<tr>
														<th></th>
														<th></th>
														<th></th>
														<th>Discount</th>
														<th>
                                                            <input id="quotation_discount" name="quotation_discount" type="number" min="0" class="form-control input-sm required" placeholder="Discount" value="<?=($quotation->quotation_discount) ? $quotation->quotation_discount : '0'?>" />
                                                        </th>
													</tr>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th>Freight</th>
                                                        <th><input id="quotation_freight" name="quotation_freight" type="number" min="0" class="form-control input-sm required" placeholder="Freight" value="<?=($quotation->quotation_freight) ? $quotation->quotation_freight : '0'?>" /></th>
                                                    </tr>
													<tr>
														<th width="10%">
															<a class="btn btn-sm btn-primary quotationitem-insert-btn" data-toggle="tooltip" title="Insert">
																<i class="glyphicon glyphicon-plus"></i>
															</a>
														</th>
														<th></th>
														<th></th>
														<th>Grand total</th>
														<th><input readonly="readonly" id="quotation_total" name="quotation_total" type="text" class="form-control input-sm" placeholder="Grand total" value="<?=($quotation->quotation_total) ? $quotation->quotation_total : '0'?>" /></th>
													</tr>
												</tfoot>
											</table>
										</div>
										<div class="hr"></div>
										<p class="form-group">
											<label for="quotation_remark">Remark</label>
											<textarea id="quotation_remark" name="quotation_remark" class="form-control input-sm" placeholder="Remark"><?=$quotation->quotation_remark?></textarea>
										</p>
										<p class="form-group">
											<label for="quotation_payment">Payment</label>
											<textarea id="quotation_payment" name="quotation_payment" class="form-control input-sm" placeholder="Payment"><?=$quotation->quotation_payment?></textarea>
										</p>
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

					<h2 class="col-sm-12">Quotation management</h2>

					<div class="content-column-area col-md-12 col-sm-12">

						<div class="fieldset">
							<div class="search-area">

								<form quotation="form" method="get">
									<input type="hidden" name="quotation_id" />
									<table>
										<tbody>
											<tr>
												<td width="90%">
													<div class="row">
														<div class="col-sm-2"><h6>Quotation</h6></div>
														<div class="col-sm-2">
															<input type="text" name="quotation_number_like" class="form-control input-sm" placeholder="QONo" value="" />
														</div>
														<!-- <div class="col-sm-2">
															<input type="text" name="quotation_number_greateq" class="form-control input-sm" placeholder="QONo From" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="quotation_number_smalleq" class="form-control input-sm" placeholder="QONo To" value="" />
														</div> -->
														<!-- <div class="col-sm-2">
															<span class="input-group">
																<span class="input-group-addon">From</span>
																<input type="text" name="quotation_number_greateq" class="form-control input-sm" placeholder="From" value="" />
																<span class="input-group-addon">To</span>
																<input type="text" name="quotation_number_smalleq" class="form-control input-sm" placeholder="To" value="" />
															</span>
														</div> -->
														<div class="col-sm-2">
															<span class="input-group date datetimepicker">
																<input id="quotation_create_greateq" name="quotation_create_greateq" type="text" class="form-control input-sm date-mask" placeholder="Date From (YYYY-MM-DD)" />
																<span class="input-group-addon">
																	<span class="glyphicon glyphicon-calendar"></span>
																</span>
															</span>
															<!-- <input type="text" name="quotation_create_greateq" class="form-control input-sm date-mask" placeholder="Date From (YYYY-MM-DD)" value="" /> -->
														</div>
														<div class="col-sm-2">
															<span class="input-group date datetimepicker">
																<input id="quotation_create_smalleq" name="quotation_create_smalleq" type="text" class="form-control input-sm date-mask" placeholder="Date To (YYYY-MM-DD)" />
																<span class="input-group-addon">
																	<span class="glyphicon glyphicon-calendar"></span>
																</span>
															</span>
															<!-- <input type="text" name="quotation_create_smalleq" class="form-control input-sm date-mask" placeholder="Date To (YYYY-MM-DD)" value="" /> -->
														</div>
														<div class="col-sm-2">
															<select id="quotation_status" name="quotation_status" data-placeholder="Status" class="chosen-select">
																<option value></option>
																<?php foreach($statuss as $key => $value){ ?>
																<option value="<?=$value->status_name?>"><?=ucfirst($value->status_name)?></option>
																<?php } ?>
															</select>
														</div>
														<div class="col-sm-2"></div>
													</div>
													<div class="row">
														<div class="col-sm-2"><h6>Customer</h6></div>
														<div class="col-sm-2">
															<input type="text" name="quotation_client_company_name_like" class="form-control input-sm" placeholder="Customer company name" value="" />
														</div>
														<div class="col-sm-2">
															<select id="quotation_user_id" name="quotation_user_id" data-placeholder="Sales" class="chosen-select">
																<option value></option>
																<?php foreach($users as $key => $value){ ?>
																<option value="<?=$value->user_id?>"><?=ucfirst($value->user_name)?></option>
																<?php } ?>
															</select>
														</div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
													</div>
													<div class="row">
														<div class="col-sm-2"><h6>Project</h6></div>
														<div class="col-sm-2">
															<input type="text" name="quotation_project_name_like" class="form-control input-sm" placeholder="Project Name" value="" />
														</div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
														<div class="col-sm-2"></div>
													</div>
													<div class="row">
														<div class="col-sm-2"><h6>Product</h6></div>
														<div class="col-sm-2">
															<input type="text" name="quotationitem_product_code_like" class="form-control input-sm" placeholder="Item Code" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="quotationitem_product_name_like" class="form-control input-sm" placeholder="Item Name" value="" />
														</div>
														<div class="col-sm-2">
															<input type="text" name="quotationitem_product_detail_like" class="form-control input-sm" placeholder="Item Description" value="" />
														</div>
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
								<form name="list" action="<?=base_url('quotation/delete')?>" method="post">
									<input type="hidden" name="quotation_id" />
									<input type="hidden" name="quotation_delete_reason" />
									<div class="page-area">
										<span class="btn btn-sm btn-default"><?php print_r($num_rows); ?></span>
										<?=$this->pagination->create_links()?>
									</div>
									<table id="quotation" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>Quotation#</th>
												<th>Version</th>
												<th>Create</th>
												<th>Customer</th>
												<th>Attn</th>
												<th>Project</th>
												<th>Sales</th>
												<th>Expiry date</th>
												<th>Status</th>
												<th>Total</th>
												<th width="40"></th>
												<!-- <th width="40"></th> -->
												<th width="40"></th>
												<th width="40" class="text-right">
													<?php if(!check_permission('quotation_insert', 'display')){ ?>
													<a href="<?=base_url('quotation/insert')?>" data-toggle="tooltip" title="Insert">
														<i class="glyphicon glyphicon-plus"></i>
													</a>
													<?php }else{ ?>
													<i class="glyphicon glyphicon-plus"></i>
													<?php } ?>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($quotations as $key => $value){ ?>
											<tr>
												<td><a href="<?=base_url('quotation/update/quotation_id/'.$value->quotation_id)?>" data-toggle="tooltip" title="Update"><?=$value->quotation_number?></a></td>
												<td>
                                                    <?php if( $value->quotation_version ) { ?>
                                                        <?= 'R' . $value->quotation_version ?>
                                                        <a href="javascript:void(0)"
                                                           id="more-<?= $value->quotation_number ?>">
                                                            <i class="glyphicon glyphicon-chevron-right"></i>
                                                        </a>
                                                    <?php }else{ ?>
                                                        N/A
                                                    <?php } ?>
												</td>
												<td><?=convert_datetime_to_date($value->quotation_create)?></td>
												<td><?=$value->quotation_client_company_name?></td>
												<td><?=$value->quotation_client_name?></td>
												<td><?=$value->quotation_project_name?></td>
												<td><?=ucfirst(get_user($value->quotation_user_id)->user_name)?></td>
												<td><?=convert_datetime_to_date($value->quotation_expire)?></td>
												<td><?=ucfirst($value->quotation_status)?></td>
												<td><?=strtoupper($value->quotation_currency).' '.money_format('%!n', $value->quotation_total)?></td>
												<td class="text-right">
													<a target="_blank" href="<?=base_url('/assets/images/pdf/quotation/'.$value->quotation_number.'-v'.$value->quotation_version.'.pdf')?>" data-toggle="tooltip" title="Print">
														<i class="glyphicon glyphicon-print"></i>
													</a>
												</td>
												<!-- <td class="text-right">
													<a href="<?=base_url('quotation/setting/quotation_id/'.$value->quotation_id)?>" data-toggle="tooltip" title="Setting">
														<i class="glyphicon glyphicon-cog"></i>
													</a>
												</td> -->
												<td class="text-right">
													<?php if(!check_permission('quotation_update', 'display')){ ?>
													<a href="<?=base_url('quotation/update/quotation_id/'.$value->quotation_id)?>" data-toggle="tooltip" title="Update">
														<i class="glyphicon glyphicon-edit"></i>
													</a>
													<?php }else{ ?>
													<i class="glyphicon glyphicon-edit"></i>
													<?php } ?>
												</td>
												<td class="text-right">
													<?php if(!check_permission('quotation_delete', 'display')){ ?>
													<a onclick="check_delete(<?=$value->quotation_id?>);" data-toggle="tooltip" title="Remove" class="<?=check_permission('quotation_delete', 'disable')?>">
														<i class="glyphicon glyphicon-remove"></i>
													</a>
													<?php }else{ ?>
													<i class="glyphicon glyphicon-remove"></i>
													<?php } ?>
												</td>
											</tr>
											<?php } ?>

											<?php if(!$quotations){ ?>
											<tr>
												<td colspan="15">No record found</td>
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

		








































		<?php if($this->router->fetch_method() == 'setting'){ ?>
		Setting
		<!-- <div class="content-area">

			<div class="container-fluid">
				<div class="row">

					<h2 class="col-sm-12"><a href="<?=base_url('quotation')?>">Quotation management</a> > Quotation setting</h2>

					<div class="col-sm-12">
						<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="quotation_id" value="<?=$quotation->quotation_id?>" />
							<input type="hidden" name="quotation_number" value="<?=$quotation->quotation_number?>" />
							<input type="hidden" name="quotation_version" value="<?=$quotation->quotation_version?>" />
							<input type="hidden" name="referrer" value="<?=$this->agent->referrer()?>" />
							<div class="fieldset">
								<div class="row">
									
									<div class="col-sm-3 col-xs-12 pull-right">
										<blockquote>
											<h4 class="corpcolor-font">Instructions</h4>
											<p><span class="highlight">*</span> is a required field</p>
										</blockquote>
										<p class="form-group">
											<a href="<?=base_url('quotation/select')?>" class="btn btn-sm btn-primary btn-block"><i class="glyphicon glyphicon-back"></i> Back</a>
										</p>
									</div>
									<div class="col-sm-9 col-xs-12">
										<h4 class="corpcolor-font">Quotation</h4>
										
										<div class="document-area">
											<div class="document-a4">
												<div class="document-header">
													<table>
														<tr>
															<td>
																<h1 class="corpcolor-font">【T】Top Excellent Consultants Limited <small><b>Your Business Partner</b></small></h1>
															</td>
														</tr>
														<tr>
															<td align="right"><h2>Quotation</h2></td>
														</tr>
													</table>
												</div>
												<div class="document-information">
													<table>
														<tr>
															<td width="50%" valign="top">
																<table>
																	<tr>
																		<td valign="top" width="24%"><b>To</b></td>
																		<td width="76%"><?=$quotation->quotation_client_company_name?></td>
																	</tr>
																	<tr>
																		<td valign="top"><b>Address</b></td>
																		<td><?=$quotation->quotation_client_company_address?></td>
																	</tr>
																	<tr>
																		<td valign="top"><b>Tel</b></td>
																		<td><?=$quotation->quotation_client_phone?></td>
																	</tr>
																	<tr>
																		<td valign="top"><b>Mobile</b></td>
																		<td><?=$quotation->quotation_client_phone?></td>
																	</tr>
																	<tr>
																		<td valign="top"><b>Attn</b></td>
																		<td><?=$quotation->quotation_client_name?></td>
																	</tr>
																</table>
															</td>
															<td width="10%"></td>
															<td width="40%" valign="top">
																<table>
																	<tr>
																		<td width="40%"><b>Quotation No.</b></td>
																		<td width="60%"><?=$quotation->quotation_number?>-v<?=$quotation->quotation_version?></td>
																	</tr>
																	<tr>
																		<td><b>Date</b></td>
																		<td><?=$quotation->quotation_issue?></td>
																	</tr>
																	<tr>
																		<td><b>Sales</b></td>
																		<td><?=$quotation->quotation_user_name?></td>
																	</tr>
																	<tr>
																		<td><b>Payment Term</b></td>
																		<td><?=$quotation->quotation_terms?></td>
																	</tr>
																	<tr>
																		<td><b>Expire Date</b></td>
																		<td><?=$quotation->quotation_expire?></td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</div>
												<div class="document-detail document-br">
													<table>
														<tr class="document-separator-bottom">
															<td width="12%"><b>PART NO.</b></td>
															<td width="55%"><b>DESCRIPTION</b></td>
															<td width="15%" align="right"><b>UNIT PRICE</b></td>
															<td width="8%" align="center"><b>QTY</b></td>
															<td width="10%" align="right"><b>AMOUNT</b></td>
														</tr>
														<?php foreach($quotationitems as $key => $value){ ?>
														<tr class="padding-top-5">
															<td>
																<div class="index_number"><?=$key+1?></div>
																<div class="part_number"><?=$value->quotationitem_product_code?></div>
															</td>
															<td><b><?=$value->quotationitem_product_name?></b></td>
															<td align="right"><?=money_format('%!n', $value->quotationitem_product_price)?></td>
															<td align="center"><?=$value->quotationitem_quantity?></td>
															<td align="right"><?=money_format('%!n', $value->quotationitem_product_price * $value->quotationitem_quantity)?></td>
														</tr>
														<tr class="padding-bottom-5">
															<td></td>
															<td valign="top"><?=nl2br($value->quotationitem_product_detail)?></td>
															<td></td>
															<td></td>
															<td></td>
														</tr>
														<?php } ?>
														<tr class="document-separator-bottom">
															<td height="100%"></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
														</tr>
														<?php if($quotation->quotation_discount != 0){ ?>
														<tr class="document-separator-top">
															<td></td>
															<td></td>
															<td align="right"><b>DISCOUNT</b></td>
															<td align="center"><?=strtoupper($quotation->quotation_currency)?></td>
															<td align="right"><?=money_format('%!n', $quotation->quotation_discount)?></td>
														</tr>
														<?php } ?>
														<tr>
															<td></td>
															<td></td>
															<td align="right"><b>GRAND TOTAL</b></td>
															<td align="center"><?=strtoupper($quotation->quotation_currency)?></td>
															<td align="right"><?=money_format('%!n', $quotation->quotation_total)?></td>
														</tr>
													</table>
												</div>
												<div class="document-terms document-br page-break-inside-avoid">
													<table>
														<tr>
															<td><b>TERMS AND CONDITIONS</b></td>
														</tr>
														<tr>
															<td>
																All the received payments are non-refundable.
																<br />Cheque(s) should be crossed & made payable to TOP EXCELLENT CONSULTANTS LIMITED.
																<br />This quotation is also an order confirmation. Once the order is confirmed, 100% balance of the total amount will be charged to the customer as a penalty for order cancellation.
																<br />This quotation will expired on above expired date or unless otherwise stated and subject to change without notice.
															</td>
														</tr>
													</table>
												</div>
												<div class="document-terms document-br page-break-inside-avoid">
													<table>
														<tr>
															<td><b>REMARK</b></td>
														</tr>
														<?php if($quotation->quotation_remark != ''){ ?>
														<tr>
															<td>
																<?=$quotation->quotation_remark?>
															</td>
														</tr>
														<?php } ?>
													</table>
												</div>
												<?php if($quotation->quotation_payment != ''){ ?>
												<div class="document-terms document-br page-break-inside-avoid">
													<table>
														<tr>
															<td><b>PAYMENT</b></td>
														</tr>
														<tr>
															<td>
																<?=$quotation->quotation_payment?>
															</td>
														</tr>
													</table>
												</div>
												<?php } ?>
												<div class="document-sign document-br page-break-inside-avoid">
													<table>
														<tr>
															<td width="40%">
																<div><b>Received By</b></div>
																<div><?=$quotation->quotation_client_company_name?></div>
																<div class="sign-area"></div>
																<div>Authority Signature & Co. Chop</div>
															</td>
															<td width="20%"></td>
															<td width="40%">
																<div><b>For and on behalf of</b></div>
																<div>Top Excellent Consultants Limited</div>
																<div class="sign-area">
																	<div class="sign"><?=$quotation->quotation_user_name?></div>
																</div>
																<div>Authority Signature & Co. Chop</div>
															</td>
														</tr>
													</table>
												</div>
												<div class="document-terms document-br">
													<table>
														<tr>
															<td>
																Pleas e return the copy of this quotation with your signature and company chop as confirmation of the above offer.
																<br />Address: Flat D, 3/F, Fu Hop Factory Building, 209-211 Wai Yip Street, Kwun Tong,Kowloon, Hong Kong.Tel: 2709 0666 Fax: 2709 0669
															</td>
														</tr>
													</table>
												</div>
											</div>
										</div>
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
		</div> -->
		<?php } ?>












































		<?php $this->load->view('inc/footer-area.php'); ?>

	</body>
</html>

<div class="scriptLoader"></div>

<!-- Modal -->
<script src="<?php echo base_url('assets/js/product-modal.js'); ?>"></script>
<div class="modal fade" id="popupModal" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="hideModal()">
                    <i class="glyphicon glyphicon-remove"></i>
                </button>
                <h4 class="modal-title corpcolor-font">Product</h4>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="hideModal()">Close</button>
            </div>
        </div>

    </div>
</div>
<!-- Modal -->