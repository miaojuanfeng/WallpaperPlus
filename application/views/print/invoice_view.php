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
			font-size: 14px;
			line-height: 20px;
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
			line-height: 18px;
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
			color: #cab132;
			margin-left: 0;
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
				<td width="85%" valign="top">
					<h1 class="corpcolor-font">Wallpaper+</h1>
				</td>
				<td width="15%" align="right"><h2><?=$language['invoice']?></h2></td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="50%" valign="top">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td valign="top" width="24%"><b><?=$language['to']?></b></td>
							<td width="76%"><?=$invoice->invoice_client_company_name?></td>
						</tr>
						<tr>
							<td valign="top"><b><?=$language['tel']?></b></td>
							<td><?=$invoice->invoice_client_phone?></td>
						</tr>
						<tr>
							<td valign="top"><b><?=$language['attn']?></b></td>
							<td><?=$invoice->invoice_client_name?></td>
						</tr>
						<tr>
							<td valign="top"><b><?=$language['address']?></b></td>
							<td><?=$invoice->invoice_client_company_address?></td>
						</tr>
					</table>
				</td>
				<td width="10%"></td>
				<td width="40%" valign="top">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td width="40%"><b><?=$language['invoice_no']?></b></td>
							<td width="60%"><?=$invoice->invoice_number?></td>
						</tr>
						<tr>
							<td><b><?=$language['date']?></b></td>
							<td><?=$invoice->invoice_issue?></td>
						</tr>
						<tr>
							<td><b><?=$language['sales']?></b></td>
							<td><?=$invoice->invoice_user_name?></td>
						</tr>
						<tr>
							<td><b><?=$language['expire_date']?></b></td>
							<td><?=$invoice->invoice_expire?></td>
						</tr>
                        <tr></tr>
					</table>
				</td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0" class="document-br-20 document-separator-bottom">
			<tr>
				<td width="12%"><b><?=$language['part_no']?></b></td>
				<td width="12%"><b><?=$language['price_type']?></b></td>
				<td width="12%"><b><?=$language['description']?></b></td>
				<td width="18%"><b><?=$language['size']?></b></td>
                <td width="15%"><b><?=$language['qty']?></b></td>
				<td width="12%"><b><?=$language['unit_price']?></b></td>
				<td width="10%" align="right"><b><?=$language['amount']?></b></td>
			</tr>
		</table>
		<?php } ?>

		








































		<?php if($this->router->fetch_method() == 'content'){ ?>
		<table cellspacing="0" cellpadding="0" class="content-table line-height-12 document-separator-bottom">
			<?php 
			foreach($invoiceitems as $key => $value){ 
				$thisProduct = get_product($value->invoiceitem_product_id);
				$thisUnit = get_unit($thisProduct->product_unit_id);
			?>
			<tr class="padding-top-5">
				<td width="12%"></td>
				<td width="12%"></td>
				<td width="12%"></td>
				<td width="18%"></td>
				<td width="15%"></td>
				<td width="12%"></td>
				<td width="10%"></td>
			</tr>
			<tr class="padding-bottom-5">
				<td valign="top"><div class="part_number"><?=$value->invoiceitem_product_code.' - '.$value->invoiceitem_product_color_code?></div></td>
				<td valign="top"><?=strtoupper($value->invoiceitem_price_type)?></td>
				<td valign="top"><?=$value->invoiceitem_product_name?></td>
				<td valign="top"><?=convert_br($value->invoiceitem_product_detail)?></td>
                <td valign="top"><?=$value->invoiceitem_quantity.' '.(!empty($thisUnit)?$thisUnit->unit_name:'')?></td>
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
				<td colspan="2" width="70%" align="right"><b><?=strtoupper($value->category_name)?> <?=$language['discount']?></b></td>
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
				<td width="15%" align="right"><b><?=$language['special_discount']?></b></td>
				<td width="8%" align="center"><?=strtoupper($invoice->invoice_currency)?></td>
				<td width="10%" align="right"><?=money_format('%!n', $invoice->invoice_discount)?></td>
			</tr>
			<?php } ?>
			<?php if($invoice->invoice_freight != 0){ ?>
            <tr>
                <td width="12%"></td>
                <td width="55%"></td>
                <td width="15%" align="right"><b><?=$language['freight']?></b></td>
                <td width="8%" align="center"><?=strtoupper($invoice->invoice_currency)?></td>
                <td width="10%" align="right"><?=money_format('%!n', $invoice->invoice_freight)?></td>
            </tr>
            <?php } ?>
			<tr>
				<td></td>
				<td></td>
				<td align="right"><b><?=$language['grand_total']?></b></td>
				<td align="center"><?=strtoupper($invoice->invoice_currency)?></td>
				<td align="right"><?=money_format('%!n', $invoice->invoice_total)?></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td align="right"><b><?=$language['paid']?></b></td>
				<td align="center"><?=strtoupper($invoice->invoice_currency)?></td>
				<td align="right"><?=money_format('%!n', $invoice->invoice_paid)?></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td align="right"><b><?=$language['pay']?></b></td>
				<td align="center"><?=strtoupper($invoice->invoice_currency)?></td>
				<td align="right"><?=money_format('%!n', $invoice->invoice_pay)?></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td align="right"><b><?=$language['balance']?></b></td>
				<td align="center"><?=strtoupper($invoice->invoice_currency)?></td>
				<td align="right"><?=money_format('%!n', $invoice->invoice_balance)?></td>
			</tr>
		</table>

		<div class="page-break-inside-avoid">
			<!--table cellspacing="0" cellpadding="0" class="document-br-20">
				<tr>
					<td><b>TERMS AND CONDITIONS</b></td>
				</tr>
				<tr>
					<td class="line-height-12">
						All the received payments are non-refundable.
						<br />Cheque(s) should be crossed & made payable to Wallpaper+.
						<br />This invoice is also an order confirmation. Once the order is confirmed, 100% balance of the total amount will be charged to the customer as a penalty for order cancellation.
						<br />This invoice will expired on above expired date or unless otherwise stated and subject to change without notice.
					</td>
				</tr>
			</table-->

			<?php if($invoice->invoice_remark != ''){ ?>
			<table cellspacing="0" cellpadding="0" class="document-br-10">
				<tr>
					<td><b><?=$language['remark']?></b></td>
				</tr>
				<tr>
					<td class="line-height-12">
						<?=convert_br($invoice->invoice_remark)?>
					</td>
				</tr>
			</table>
			<?php } ?>

			<?php if($invoice->invoice_payment != ''){ ?>
			<table cellspacing="0" cellpadding="0" class="document-br-10">
				<tr>
					<td><b><?=$language['payment']?></b></td>
				</tr>
				<tr>
					<td class="line-height-12">
						<?=convert_br($invoice->invoice_payment)?>
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
						<div><b><?=$language['for_and_on_behalf_of']?></b></div>
						<div>Wallpaper+</div>
						<div class="sign-area">
							<div class="sign"><?=$invoice->invoice_user_name?></div>
						</div>
						<div><?=$language['signature']?></div>
					</td>
				</tr>
			</table>

			<table cellspacing="0" cellpadding="0" class="document-br-10">
				<tr>
					<td class="line-height-12">
						<?=$language['confirmation']?>
						<br /><?=$language['address']?>: 9 Floor The Hennessy 256 Hennessy Road Wan Chal Hong Kong. 
						<br /><?=$language['tel']?>: +852 3525 1785 <?=$language['fax']?>: +852 3525 1784 <?=$language['email']?>: sales@wallpaperplus.com.hk
					</td>
				</tr>
			</table>
		</div>
		<?php } ?>

		








































	</body>
</html>