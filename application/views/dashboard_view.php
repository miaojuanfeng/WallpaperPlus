<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Dashboard</title>

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

					<h2 class="col-sm-12">Dashboard</h2>

					<div class="content-column-area col-md-12 col-sm-12">
						<div class="fieldset">

							<div class="row">
								<div class="col-md-3 col-sm-6 bottom-buffer-10">
									<div class="dashboard-box-top">
										<i class="glyphicon glyphicon-stats pull-left"></i>
										<span class="pull-right">
											<div class="summary text-right"><?=money_format('%!n', $sumSalesordersTotalMonthly->sum_salesorder_total)?></div>
											<div class="text-right">Sales order amount total<br />/ per month</div>
										</span>
										<div class="clearfix"></div>
									</div>
									<div class="dashboard-box-bottom">
                                        <a href="<?=base_url('salesorder/select')?>">
                                            <span class="pull-left">More</span>
                                            <span class="pull-right"><i class="glyphicon glyphicon-chevron-right"></i></span>
                                        </a>
										<div class="clearfix"></div>
									</div>
								</div>
								<div class="col-md-3 col-sm-6 bottom-buffer-10">
									<div class="dashboard-box-top">
										<i class="glyphicon glyphicon-stats pull-left"></i>
										<span class="pull-right">
											<div class="summary text-right"><?=$countQuotationTotalMonthly->count_quotation_total?></div>
											<div class="text-right">Number of quotation<br />/ per month</div>
										</span>
										<div class="clearfix"></div>
									</div>
									<div class="dashboard-box-bottom">
                                        <a href="<?=base_url('quotation/select')?>">
                                            <span class="pull-left">More</span>
                                            <span class="pull-right"><i class="glyphicon glyphicon-chevron-right"></i></span>
                                        </a>
										<div class="clearfix"></div>
									</div>
								</div>
								<div class="col-md-3 col-sm-6 bottom-buffer-10">
									<div class="dashboard-box-top">
										<i class="glyphicon glyphicon-stats pull-left"></i>
										<span class="pull-right">
											<div class="summary text-right"><?=$countSalesorderTotal->count_salesorder_total?></div>
											<div class="text-right">Number of purchase order<br />/ per month</div>
										</span>
										<div class="clearfix"></div>
									</div>
									<div class="dashboard-box-bottom">
                                        <a href="<?=base_url('purchaseorder/select')?>">
                                            <span class="pull-left">More</span>
                                            <span class="pull-right"><i class="glyphicon glyphicon-chevron-right"></i></span>
                                        </a>
										<div class="clearfix"></div>
									</div>
								</div>
								<div class="col-md-3 col-sm-6 bottom-buffer-10">
									<div class="dashboard-box-top">
										<i class="glyphicon glyphicon-stats pull-left"></i>
										<span class="pull-right">
											<div class="summary text-right"><?=$countInvoiceTotal->count_invoice_total?></div>
											<div class="text-right">Number of invoice<br />/ per month</div>
										</span>
										<div class="clearfix"></div>
									</div>
									<div class="dashboard-box-bottom">
                                        <a href="<?=base_url('invoice/select')?>">
                                            <span class="pull-left">More</span>
                                            <span class="pull-right"><i class="glyphicon glyphicon-chevron-right"></i></span>
                                        </a>
										<div class="clearfix"></div>
									</div>
								</div>
							</div>

							<div class="list-area">
								<h4 class="corpcolor-font"><i class="glyphicon glyphicon-bullhorn"></i> Summary <small>Show processing sales order only</small></h4>
								<table class="table table-striped table-bordered">
									<thead>
										<tr>
											<th>Customer name</th>
											<th>Quotation #</th>
											<th>Sales order #</th>
											<th>Purchase order #</th>
											<th>Invoice #</th>
											<th>Delivery note #</th>
											<th>Sales</th>
											<th>Sales order amount</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($summarySalesorders as $key => $value){ ?>
										<tr>
											<td><?=$value->salesorder_client_company_name?></td>
											<td><a href="<?=base_url('quotation/update/quotation_id/'.$value->salesorder_quotation_id)?>"><?=get_quotation($value->salesorder_quotation_id)->quotation_number?></a></td>
											<td><a href="<?=base_url('salesorder/update')?>"><?=$value->salesorder_number?></a></td>
											<td>
												<?php foreach($value->purchaseorders as $key1 => $value1){ ?>
												<div><a href="<?=base_url('purchaseorder/update/purchaseorder_id/'.$value1->purchaseorder_id)?>"><?=$value1->purchaseorder_number?></a></div>
												<?php } ?>
											</td>
											<td>
												<?php foreach($value->invoices as $key1 => $value1){ ?>
												<div><?=$value1->invoice_number?></div>
												<?php } ?>
											</td>
											<td>
												<?php foreach($value->deliverynotes as $key1 => $value1){ ?>
												<div><?=$value1->deliverynote_number?></div>
												<?php } ?>
											</td>
											<td><?=ucfirst(get_user($value->salesorder_quotation_user_id)->user_name)?></td>
											<td><?=strtoupper($value->salesorder_currency).' '.$value->salesorder_total?></td>
										</tr>
										<?php } ?>

										<?php if(!$summarySalesorders){ ?>
										<tr>
											<td colspan="8">No record found</td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>

							<!-- if administrator or boss -->
							<?php if(in_array('1', $this->session->userdata('role')) || in_array('2', $this->session->userdata('role'))){ ?>
							<div class="row">
								<div class="col-md-6 col-sm-12">
									<div class="list-area">
										<h4 class="corpcolor-font"><i class="glyphicon glyphicon-bullhorn"></i> Processing sales order</h4>
										<table class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>Customer name</th>
													<th>Sales order #</th>
													<th>Sales</th>
													<th>Sales order amount</th>
													<th>Deadline</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($processingSalesorders as $key => $value){ ?>
												<tr>
													<td><?=$value->salesorder_client_company_name?></td>
													<td><a href="<?=base_url('salesorder/select/salesorder_id/'.$value->salesorder_id)?>"><?=$value->salesorder_number?></a></td>
													<td><?=ucfirst(get_user($value->salesorder_quotation_user_id)->user_name)?></td>
													<td><?=strtoupper($value->salesorder_currency).' '.$value->salesorder_total?></td>
													<td><?=$value->salesorder_expire?></td>
												</tr>
												<?php } ?>

												<?php if(!$processingSalesorders){ ?>
												<tr>
													<td colspan="5">No record found</td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
								<div class="col-md-6 col-sm-12">
									<div class="list-area">
										<h4 class="corpcolor-font"><i class="glyphicon glyphicon-bullhorn"></i> Processing purchase order <small>View <a href="<?=base_url('purchaseorderchecklist/select/purchaseorder_status/processing')?>"><u>checklist</u></a> here</small></h4>
										<table class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>Customer name</th>
													<th>Purchase order #</th>
													<th>Sales</th>
													<th>Purchase order amount</th>
													<th>Reminder date</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($processingPurchaseorders as $key => $value){ ?>
												<tr>
													<td><?=$value->purchaseorder_vendor_company_name?></td>
													<td><a href="<?=base_url('purchaseorder/select/purchaseorder_id/'.$value->purchaseorder_id)?>"><?=$value->purchaseorder_number?></a></td>
													<td><?=ucfirst(get_user($value->purchaseorder_quotation_user_id)->user_name)?></td>
													<td><?=strtoupper($value->purchaseorder_currency).' '.$value->purchaseorder_total?></td>
													<td><?=$value->purchaseorder_reminder_date?></td>
												</tr>
												<?php } ?>

												<?php if(!$processingPurchaseorders){ ?>
												<tr>
													<td colspan="5">No record found</td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6 col-sm-12">
									<div class="list-area">
										<h4 class="corpcolor-font"><i class="glyphicon glyphicon-bullhorn"></i> Processing invoice <small>View <a href="<?=base_url('invoicechecklist/select/invoice_status/processing')?>"><u>checklist</u></a> here</small></h4>
										<table class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>Customer name</th>
													<th>Invoice #</th>
													<th>Sales</th>
													<th>Invoice amount</th>
													<th>Deadline</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($processingInvoices as $key => $value){ ?>
												<tr>
													<td><?=$value->invoice_client_company_name?></td>
													<td><a href="<?=base_url('invoice/select/invoice_id/'.$value->invoice_id)?>"><?=$value->invoice_number?></a></td>
													<td><?=ucfirst(get_user($value->invoice_quotation_user_id)->user_name)?></td>
													<td><?=strtoupper($value->invoice_currency).' '.$value->invoice_total?></td>
													<td><?=$value->invoice_expire?></td>
												</tr>
												<?php } ?>

												<?php if(!$processingInvoices){ ?>
												<tr>
													<td colspan="5">No record found</td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
								<div class="col-md-6 col-sm-12">
									<div class="list-area">
										<h4 class="corpcolor-font"><i class="glyphicon glyphicon-bullhorn"></i> Processing delivery note</h4>
										<table class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>Delivery note #</th>
													<th>Company name</th>
													<th>Delivery address</th>
													<th>Contact</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($processingDeliverynotes as $key => $value){ ?>
												<tr>
													<td><?=$value->deliverynote_number?></td>
													<td><?=$value->deliverynote_client_company_name?></td>
													<td><?=$value->deliverynote_client_delivery_address?></td>
													<td><?=$value->deliverynote_client_phone?></td>
												</tr>
												<?php } ?>

												<?php if(!$processingDeliverynotes){ ?>
												<tr>
													<td colspan="4">No record found</td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<?php } ?>

							<!-- if sales management or sales -->
							<?php if(in_array('3', $this->session->userdata('role')) || in_array('4', $this->session->userdata('role'))){ ?>
							<div class="row">
								<div class="col-md-6 col-sm-12">
									<div class="list-area">
										<h4 class="corpcolor-font"><i class="glyphicon glyphicon-bullhorn"></i> Processing sales order</h4>
										<table class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>Customer name</th>
													<th>Sales order #</th>
													<th>Sales</th>
													<th>Sales order amount</th>
													<th>Deadline</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($processingSalesorders as $key => $value){ ?>
												<tr>
													<td><?=$value->salesorder_client_company_name?></td>
													<td><a href="<?=base_url('salesorder/select/salesorder_id/'.$value->salesorder_id)?>"><?=$value->salesorder_number?></a></td>
													<td><?=ucfirst(get_user($value->salesorder_quotation_user_id)->user_name)?></td>
													<td><?=strtoupper($value->salesorder_currency).' '.$value->salesorder_total?></td>
													<td><?=$value->salesorder_expire?></td>
												</tr>
												<?php } ?>

												<?php if(!$processingSalesorders){ ?>
												<tr>
													<td colspan="5">No record found</td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
								<div class="col-md-6 col-sm-12">
									<div class="list-area">
										<h4 class="corpcolor-font"><i class="glyphicon glyphicon-bullhorn"></i> Processing delivery note</h4>
										<table class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>Delivery note #</th>
													<th>Company name</th>
													<th>Delivery address</th>
													<th>Contact</th>
													<th>Deadline</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($processingDeliverynotes as $key => $value){ ?>
												<tr>
													<td><?=$value->deliverynote_number?></td>
													<td><?=$value->deliverynote_client_company_name?></td>
													<td><?=$value->deliverynote_client_delivery_address?></td>
													<td><?=$value->deliverynote_client_phone?></td>
													<td><?=$value->deliverynote_expire?></td>
												</tr>
												<?php } ?>

												<?php if(!$processingDeliverynotes){ ?>
												<tr>
													<td colspan="5">No record found</td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<?php } ?>

							<!-- if operation manager or operation -->
							<?php if(in_array('5', $this->session->userdata('role')) || in_array('6', $this->session->userdata('role'))){ ?>
							<div class="row">
								<div class="col-md-6 col-sm-12">
									<div class="list-area">
										<h4 class="corpcolor-font"><i class="glyphicon glyphicon-bullhorn"></i> Processing purchase order <small>View <a href="<?=base_url('purchaseorderchecklist/select/purchaseorder_status/processing')?>"><u>checklist</u></a> here</small></h4>
										<table class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>Customer name</th>
													<th>Purchase order #</th>
													<th>Sales</th>
													<th>Purchase order amount</th>
													<th>Reminder date</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($processingPurchaseorders as $key => $value){ ?>
												<tr>
													<td><?=$value->purchaseorder_vendor_company_name?></td>
													<td><a href="<?=base_url('purchaseorder/select/purchaseorder_id/'.$value->purchaseorder_id)?>"><?=$value->purchaseorder_number?></a></td>
													<td><?=ucfirst(get_user($value->purchaseorder_quotation_user_id)->user_name)?></td>
													<td><?=strtoupper($value->purchaseorder_currency).' '.$value->purchaseorder_total?></td>
													<td><?=$value->purchaseorder_reminder_date?></td>
												</tr>
												<?php } ?>

												<?php if(!$processingPurchaseorders){ ?>
												<tr>
													<td colspan="5">No record found</td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
								<div class="col-md-6 col-sm-12">
									<div class="list-area">
										<h4 class="corpcolor-font"><i class="glyphicon glyphicon-bullhorn"></i> Processing delivery note</h4>
										<table class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>Delivery note #</th>
													<th>Company name</th>
													<th>Delivery address</th>
													<th>Contact</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($processingDeliverynotes as $key => $value){ ?>
												<tr>
													<td><?=$value->deliverynote_number?></td>
													<td><?=$value->deliverynote_client_company_name?></td>
													<td><?=$value->deliverynote_client_delivery_address?></td>
													<td><?=$value->deliverynote_client_phone?></td>
												</tr>
												<?php } ?>

												<?php if(!$processingDeliverynotes){ ?>
												<tr>
													<td colspan="4">No record found</td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<?php } ?>

							<!-- if account -->
							<?php if(in_array('7', $this->session->userdata('role'))){ ?>
							<div class="row">
								<div class="col-md-6 col-sm-12">
									<div class="list-area">
										<h4 class="corpcolor-font"><i class="glyphicon glyphicon-bullhorn"></i> Processing sales order</h4>
										<table class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>Customer name</th>
													<th>Sales order #</th>
													<th>Sales</th>
													<th>Sales order amount</th>
													<th>Deadline</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($processingSalesorders as $key => $value){ ?>
												<tr>
													<td><?=$value->salesorder_client_company_name?></td>
													<td><a href="<?=base_url('salesorder/select/salesorder_id/'.$value->salesorder_id)?>"><?=$value->salesorder_number?></a></td>
													<td><?=ucfirst(get_user($value->salesorder_quotation_user_id)->user_name)?></td>
													<td><?=strtoupper($value->salesorder_currency).' '.$value->salesorder_total?></td>
													<td><?=$value->salesorder_expire?></td>
												</tr>
												<?php } ?>

												<?php if(!$processingSalesorders){ ?>
												<tr>
													<td colspan="5">No record found</td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
								<div class="col-md-6 col-sm-12">
									<div class="list-area">
										<h4 class="corpcolor-font"><i class="glyphicon glyphicon-bullhorn"></i> Processing invoice order <small>View <a href="<?=base_url('invoicechecklist/select/invoice_status/processing')?>"><u>checklist</u></a> here</small></h4>
										<table class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>Customer name</th>
													<th>Invoice #</th>
													<th>Sales</th>
													<th>Invoice amount</th>
													<th>Deadline</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($processingInvoices as $key => $value){ ?>
												<tr>
													<td><?=$value->invoice_client_company_name?></td>
													<td><a href="<?=base_url('invoice/select/invoice_id/'.$value->invoice_id)?>"><?=$value->invoice_number?></a></td>
													<td><?=ucfirst(get_user($value->invoice_quotation_user_id)->user_name)?></td>
													<td><?=strtoupper($value->salesorder_currency).' '.$value->invoice_total?></td>
													<td><?=$value->invoice_expire?></td>
												</tr>
												<?php } ?>

												<?php if(!$processingInvoices){ ?>
												<tr>
													<td colspan="5">No record found</td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<?php } ?>

							<!-- <h1 class="blue">if role is boss</h1>
							<p>Not complete SO</p>
							<p>Not complete PO</p>
							<p>Not complete invoice</p>
							<p>Not delivery stock</p>

							<h1 class="blue">if role is sales manager & sales</h1>
							<p>Not complete SO</p>
							<p>Not delivery stock</p>

							<h1 class="blue">if role is operation manager & operation clerk</h1>
							<p>Not complete PO</p>
							<p>Not delivery stock</p>

							<h1 class="blue">if role is account</h1>
							<p>Not complete SO</p>
							<p>Not complete invoice</p> -->

						</div>
					</div>
					<!-- <div class="blue">
						<p>Show record DESC by expiry date</p>
						<p>??? Show stock arrival</p>
						<p>Invoice credit reminder</p>
						<p>Sales order followup reminder</p>
						
						<p>1. all documents print PDF format not done</p>
						<p>2. 做quote時選product須要優化一下</p>
						<p>3. If PO, IN, PI, DN exist, SO cannot be delete</p>
						<p>4. If SO exist, Quote cannot be delete</p>
						<p>5. Export to Excel function</p>
					</div> -->
				</div>
			</div>

		</div>
		<?php } ?>

		











































		<?php $this->load->view('inc/footer-area.php'); ?>

	</body>
</html>