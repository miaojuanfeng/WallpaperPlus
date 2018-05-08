		

		
		<div class="header-area">
			<div class="no-wrapper">

				<div class="header-header1-area">

					<div class="container-fluid">
						<div class="row hidden-xs">
							<div class="col-xs-12">

								<div class="header-desktop-area">
									<div class="pull-left">
										<h1>Wallpaper+ <small>Selling system</small></h1>
									</div>
									<div class="pull-right">
										<!-- <div class="btn-group">
											<a href="#" class="btn btn-primary btn-ms dropdown-toggle" data-toggle="dropdown">
												<i class="glyphicon glyphicon-user"> chuyan</i>
											</a>
											<ul class="dropdown-menu dropdown-menu-right" role="menu">
												<li><a href="#">Administrator</a></li>
											</ul>
										</div> -->
										<i class="glyphicon glyphicon-user corpcolor-font"></i> <?=ucfirst(get_user($this->session->userdata('user_id'))->user_name)?> (<a href="<?=base_url('login')?>">Logout</a>)
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="header-header2-area">
					<div class="container-fluid">
						<div class="row">

							<div class="col-xs-12">
								<nav class="navbar navbar-default">
									<div class="wrapper">
										<!-- <div class="container-fluid"> -->
											<div class="navbar-header">
												<button type="button" class="btn btn-primary navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
													&nbsp;<i class="glyphicon glyphicon-th"></i>&nbsp;
												</button>
												<div class="navbar-brand hidden-lg hidden-md hidden-sm">Top Excellent <small style="font-size:12px;">Selling system</small></div>
											</div>
											<div id="navbar" class="navbar-collapse collapse" aria-expanded="false">
												<ul class="nav navbar-nav">
													<li<?=($this->router->fetch_class() == 'dashboard') ? ' class="active"' : ''?>>
														<a href="<?=base_url('dashboard')?>">Dashboard</a>
													</li>
													<li<?=($this->router->fetch_class() == 'quotation') ? ' class="active"' : ''?>>
														<a href="<?=base_url('quotation')?>">Quotation</a>
													</li>
													<li<?=($this->router->fetch_class() == 'salesorder') ? ' class="active"' : ''?>>
														<a href="<?=base_url('salesorder')?>">Sales Order</a>
													</li>
													<li<?=($this->router->fetch_class() == 'purchaseorder' || $this->router->fetch_class() == 'waybill') ? ' class="active"' : ''?>>
														<a href="<?=base_url('purchaseorder')?>">Purchase Order</a>
													</li>
													<li<?=($this->router->fetch_class() == 'invoice') ? ' class="active"' : ''?>>
														<a href="<?=base_url('invoice')?>">Invoice</a>
													</li>
													<!-- <li<?=($this->router->fetch_class() == 'proformainvoice') ? ' class="active"' : ''?>>
														<a href="<?=base_url('proformainvoice')?>">Proforma Invoice</a>
													</li> -->
													<li<?=($this->router->fetch_class() == 'deliverynote') ? ' class="active"' : ''?>>
                                                        <a href="<?=base_url('deliverynote')?>">Delivery Note</a>
                                                    </li>
													<!-- <li<?=($this->router->fetch_class() == 'search') ? ' class="active"' : ''?>>
														<a href="<?=base_url('search')?>">Search</a>
													</li> -->
													<?php
													switch($this->router->fetch_class()){
														case 'maintenance':
														case 'client':
														case 'vendor':
														case 'product':
														case 'attribute':
														case 'brand':
														case 'category':
														case 'color':
														case 'style':
														case 'usage':
														case 'material':
														case 'keyword':
														case 'size':
														case 'warehouse':
														case 'exchange':
														case 'invoicechecklist':
														case 'purchaseorderchecklist':
														case 'commissionchecklist':
														case 'deliverychecklist':
														case 'role':
														case 'user':
														case 'setting':
														case 'currency':
															$thisCSS = ' class="active"';
															break;
														default:
															$thisCSS = '';
															break;
													}
													?>
													<li<?=$thisCSS?>>
														<a href="<?=base_url('maintenance')?>">Maintenance</a>
													</li>
													<?php
													switch($this->router->fetch_class()){
														case 'report':
														case 'incomereport':
														case 'expensesreport':
														case 'receivablereport':
														case 'payablereport':
                                                        case 'ledgerreport':
														case 'salesreport':
														case 'commissionreport':
                                                        case 'stockreport':
														case 'log':
															$thisCSS = ' class="active"';
															break;
														default:
															$thisCSS = '';
															break;
													}
													?>
													<li<?=$thisCSS?>>
														<a href="<?=base_url('report')?>">Report</a>
													</li>
													<!-- <li class="dropdown">
														<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Purchase Order <span class="caret"></span></a>
														<ul class="dropdown-menu">
															<li><a href="#">test</a></li>
															<li><a href="#">test</a></li>
															<li><a href="#">test</a></li>
															<li><a href="#">test</a></li>
														</ul>
													</li> -->
												</ul>
											</div>
										<!-- </div> -->
									</div>
								</nav>
							</div>

						</div>
					</div>

				</div>
			</div>
		</div>



