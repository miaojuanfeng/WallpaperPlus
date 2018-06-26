<?php
defined('BASEPATH') OR exit('No direct script access allowed');

switch($invoice->invoice_currency){
	case 'rmb':
		$thisDollarSign = '¥';
		break;
	default:
		$thisDollarSign = '$';
		break;
}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Invoice</title>

		<meta charset="utf-8">
		<style>
		body {
			font-family: Helvetica, Arial, sans-serif;
			font-size: 9px;
			line-height: 16px;
			margin: 0;
			padding: 0;
			-webkit-text-size-adjust: none;
		}
		table {
			width: 100%;
			margin: 0;
			padding: 0;
			table-layout: fixed;
		}
		table.content-table {
			height: 280px;
		}
		.line-height-12 {
			line-height: 12px;
		}
		.padding-top-5 td {
			padding-top: 5px;
		}
		.padding-bottom-5 td {
			padding-bottom: 5px;
		}
		h1 small {
			color: #777;
		}
		h1 {
			margin-left: -8px;
		}
		.corpcolor-font {
			color: #f98700;
		}
		.document-br-20 {
			margin-top: 20px;
		}
		.document-br-10 {
			margin-top: 10px;
		}
		.document-separator-bottom {
			border-bottom: 2px solid #333;
		}
		.sign-area {
			height: 80px;
			border-bottom: 1px solid #333;
			position: relative;
		}
		.sign {
			font-family: times;
			font-size: 38px;
			position: absolute;
			top: 50%;
			left: 50%;
			width: 200px;
			height: auto;
			margin-top: -20px;
			margin-left: -100px;
		}
		.page-break-inside-avoid {
			page-break-inside: avoid;
		}
		</style>
	</head>

	<body>

		








































		<?php if($this->router->fetch_method() == 'header'){ ?>
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="85%">
					<h1 class="corpcolor-font">【Wallpaper+】 <small><b>Your Business Partner</b></small></h1>
				</td>
				<td width="15%" align="right"><h2>Invoice</h2></td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="50%" valign="top">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td valign="top" width="24%"><b>To</b></td>
							<td width="76%"><?=$invoice->invoice_client_company_name?></td>
						</tr>
						<tr>
							<td valign="top"><b>Address</b></td>
							<td><?=$invoice->invoice_client_company_address?></td>
						</tr>
						<tr>
							<td valign="top"><b>Tel</b></td>
							<td><?=$invoice->invoice_client_phone?></td>
						</tr>
						<tr>
							<td valign="top"><b>Mobile</b></td>
							<td><?=$invoice->invoice_client_phone?></td>
						</tr>
						<tr>
							<td valign="top"><b>Attn</b></td>
							<td><?=$invoice->invoice_client_name?></td>
						</tr>
					</table>
				</td>
				<td width="10%"></td>
				<td width="40%" valign="top">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td width="40%"><b>Invoice No.</b></td>
							<td width="60%"><?=$invoice->invoice_number?></td>
						</tr>
						<tr>
							<td><b>Date</b></td>
							<td><?=$invoice->invoice_issue?></td>
						</tr>
						<tr>
							<td><b>Sales</b></td>
							<td><?=$invoice->invoice_user_name?></td>
						</tr>
						<tr>
							<td><b>Expire Date</b></td>
							<td><?=$invoice->invoice_expire?></td>
						</tr>
                        <tr></tr>
					</table>
				</td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0" class="document-br-20 document-separator-bottom">
			<tr>
				<td width="12%"><b>PART NO.</b></td>
				<td width="12%"><b>DESCRIPTION</b></td>
				<td width="11%" align="center"><b>SIZE</b></td>
                <td width="15%"><b>QTY</b></td>
				<td width="12%"><b>UNIT PRICE</b></td>
				<td width="10%" align="right"><b>AMOUNT</b></td>
			</tr>
		</table>
		<?php } ?>

		








































		<?php if($this->router->fetch_method() == 'content'){ ?>
		<table cellspacing="0" cellpadding="0" class="content-table line-height-12 document-separator-bottom">
			<?php 
			foreach($invoiceitems as $key => $value){ 
				$thisProduct = get_product($value->invoiceitem_product_id);
			?>
			<tr class="padding-top-5">
				<td width="12%"></td>
				<td width="12%"></td>
				<td width="11%"></td>
				<td width="15%"></td>
				<td width="12%"></td>
				<td width="10%"></td>
			</tr>
			<tr class="padding-bottom-5">
				<td valign="top"><div class="part_number"><?=$value->invoiceitem_product_code?></div></td>
				<td valign="top"><?=$value->invoiceitem_product_name?></td>
				<td valign="top" align="center"><?=$value->invoiceitem_product_detail?></td>
                <td valign="top"><?=$value->invoiceitem_quantity.' '.get_unit($thisProduct->product_unit_id)->unit_name?></td>
				<td valign="top"><?=strtoupper($invoice->invoice_currency).' '.money_format('%!n', $value->invoiceitem_product_price)?></td>
				<td valign="top" align="right"><?=strtoupper($invoice->invoice_currency).' '.money_format('%!n', $value->invoiceitem_product_price * $value->invoiceitem_quantity)?></td>
			</tr>
			<?php } ?>
			<tr class="document-separator-bottom">
				<td height="100%"></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</table>

		<table cellspacing="0" cellpadding="0">
			<?php 
				if( !empty($invoice->invoice_category_discount) ){ 
					$invoice_category_discount = json_decode($invoice->invoice_category_discount);
					foreach ($invoice_category_discount as $key => $value) {
			?>
			<tr>
				<td width="12%"></td>
				<td width="55%"></td>
				<td width="15%" align="right"><b><?=strtoupper($value->category_name)?> DISCOUNT</b></td>
				<td width="8%" align="center"><?=strtoupper($invoice->invoice_currency)?></td>
				<td width="10%" align="right"><?=money_format('%!n', $value->category_discount)?></td>
			</tr>
			<?php 	
					}
				}
			?>
			<?php if($invoice->invoice_discount != 0){ ?>
			<tr>
				<td width="12%"></td>
				<td width="55%"></td>
				<td width="15%" align="right"><b>DISCOUNT</b></td>
				<td width="8%" align="center"><?=strtoupper($invoice->invoice_currency)?></td>
				<td width="10%" align="right"><?=money_format('%!n', $invoice->invoice_discount)?></td>
			</tr>
			<?php } ?>
			<?php if($invoice->invoice_freight != 0){ ?>
            <tr>
                <td width="12%"></td>
                <td width="55%"></td>
                <td width="15%" align="right"><b>FREIGHT</b></td>
                <td width="8%" align="center"><?=strtoupper($invoice->invoice_currency)?></td>
                <td width="10%" align="right"><?=money_format('%!n', $invoice->invoice_freight)?></td>
            </tr>
            <?php } ?>
			<tr>
				<td></td>
				<td></td>
				<td align="right"><b>GRAND TOTAL</b></td>
				<td align="center"><?=strtoupper($invoice->invoice_currency)?></td>
				<td align="right"><?=money_format('%!n', $invoice->invoice_total)?></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td align="right"><b>PAID</b></td>
				<td align="center"><?=strtoupper($invoice->invoice_currency)?></td>
				<td align="right"><?=money_format('%!n', $invoice->invoice_paid)?></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td align="right"><b>PAY</b></td>
				<td align="center"><?=strtoupper($invoice->invoice_currency)?></td>
				<td align="right"><?=money_format('%!n', $invoice->invoice_pay)?></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td align="right"><b>BALANCE</b></td>
				<td align="center"><?=strtoupper($invoice->invoice_currency)?></td>
				<td align="right"><?=money_format('%!n', $invoice->invoice_balance)?></td>
			</tr>
		</table>

		<div class="page-break-inside-avoid">
			<table cellspacing="0" cellpadding="0" class="document-br-20">
				<tr>
					<td><b>TERMS AND CONDITIONS</b></td>
				</tr>
				<tr>
					<td class="line-height-12">
						All the received payments are non-refundable.
						<br />Cheque(s) should be crossed & made payable to TOP EXCELLENT CONSULTANTS LIMITED.
						<br />This invoice is also an order confirmation. Once the order is confirmed, 100% balance of the total amount will be charged to the customer as a penalty for order cancellation.
						<br />This invoice will expired on above expired date or unless otherwise stated and subject to change without notice.
					</td>
				</tr>
			</table>

			<table cellspacing="0" cellpadding="0" class="document-br-10">
				<tr>
					<td><b>REMARK</b></td>
				</tr>
				<?php if($invoice->invoice_remark != ''){ ?>
				<tr>
					<td class="line-height-12">
						<?=$invoice->invoice_remark?>
					</td>
				</tr>
				<?php } ?>
			</table>

			<?php if($invoice->invoice_payment != ''){ ?>
			<table cellspacing="0" cellpadding="0" class="document-br-10">
				<tr>
					<td><b>PAYMENT</b></td>
				</tr>
				<tr>
					<td class="line-height-12">
						<?=$invoice->invoice_payment?>
					</td>
				</tr>
			</table>
			<?php } ?>
		</div>
		
		<div class="page-break-inside-avoid">
			<table cellspacing="0" cellpadding="0" class="document-br-20">
				<tr>
					<td width="40%">

					</td>
					<td width="20%"></td>
					<td width="40%">
						<div><b>For and on behalf of</b></div>
						<div>Top Excellent Consultants Limited</div>
						<div class="sign-area">
							<div class="sign"><?=$invoice->invoice_user_name?></div>
						</div>
						<div>Authority Signature & Co. Chop</div>
					</td>
				</tr>
			</table>

			<table cellspacing="0" cellpadding="0" class="document-br-10">
				<tr>
					<td class="line-height-12">
						Pleas e return the copy of this invoice with your signature and company chop as confirmation of the above offer.
						<br />Address: Flat D, 3/F, Fu Hop Factory Building, 209-211 Wai Yip Street, Kwun Tong,Kowloon, Hong Kong.Tel: 2709 0666 Fax: 2709 0669
					</td>
				</tr>
			</table>
		</div>
		<?php } ?>

		








































	</body>
</html>