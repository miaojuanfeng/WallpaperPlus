<?php
defined('BASEPATH') OR exit('No direct script access allowed');

switch($deliverynote->deliverynote_currency){
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
		<title>Delivery note</title>

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
				<td width="15%" align="right"><h2>Delivery note</h2></td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="50%" valign="top">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td valign="top" width="24%"><b>To</b></td>
							<td width="76%"><?=$deliverynote->deliverynote_client_company_name?></td>
						</tr>
						<tr>
							<td valign="top"><b>Address</b></td>
							<td><?=$deliverynote->deliverynote_client_company_address?></td>
						</tr>
						<tr>
							<td valign="top"><b>Tel</b></td>
							<td><?=$deliverynote->deliverynote_client_phone?></td>
						</tr>
						<tr>
							<td valign="top"><b>Mobile</b></td>
							<td><?=$deliverynote->deliverynote_client_phone?></td>
						</tr>
						<tr>
							<td valign="top"><b>Attn</b></td>
							<td><?=$deliverynote->deliverynote_client_name?></td>
						</tr>
					</table>
				</td>
				<td width="10%"></td>
				<td width="40%" valign="top">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td width="40%"><b>Delivery note No.</b></td>
							<td width="60%"><?=$deliverynote->deliverynote_number?>-v<?=$deliverynote->deliverynote_version?>/INV0000000</td>
						</tr>
						<tr>
							<td><b>Date</b></td>
							<td><?=$deliverynote->deliverynote_issue?></td>
						</tr>
						<tr>
							<td><b>Sales</b></td>
							<td><?=$deliverynote->deliverynote_user_name?></td>
						</tr>
						<tr>
							<td><b>Payment Term</b></td>
							<td><?=$deliverynote->deliverynote_terms?></td>
						</tr>
						<tr>
							<td><b>Expire Date</b></td>
							<td><?=$deliverynote->deliverynote_expire?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0" class="document-br-20 document-separator-bottom">
			<tr>
				<td width="12%"><b>PART NO.</b></td>
				<td width="76%"><b>DESCRIPTION</b></td>
				<td width="12%" align="right"><b>QTY</b></td>
			</tr>
		</table>
		<?php } ?>

		








































		<?php if($this->router->fetch_method() == 'content'){ ?>
		<table cellspacing="0" cellpadding="0" class="content-table line-height-12 document-separator-bottom">
			<?php foreach($deliverynoteitems as $key => $value){ ?>
			<tr class="padding-top-5">
				<td width="12%"></td>
				<td width="76%"></td>
				<td width="12%"></td>
			</tr>
			<tr class="padding-bottom-5">
				<td valign="top"><div class="part_number"><?=$value->deliverynoteitem_product_code?></div></td>
				<td valign="top"><?=$value->deliverynoteitem_product_code.' - '.$value->deliverynoteitem_product_name?></td>
				<td valign="top" align="right"><?=$value->deliverynoteitem_quantity?></td>
			</tr>
			<?php } ?>
			<tr class="document-separator-bottom">
				<td height="100%"></td>
				<td></td>
				<td></td>
			</tr>
		</table>

		<table cellspacing="0" cellpadding="0">
			<tr>
				<td width="12%"></td>
				<td width="76%" align="right"><b>QUANTITY TOTAL</b></td>
				<td width="12%" align="right"><?=$deliverynote->deliverynote_total?></td>
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
						<br />This deliverynote is also an order confirmation. Once the order is confirmed, 100% balance of the total amount will be charged to the customer as a penalty for order cancellation.
						<br />This deliverynote will expired on above expired date or unless otherwise stated and subject to change without notice.
					</td>
				</tr>
			</table>

			<table cellspacing="0" cellpadding="0" class="document-br-10">
				<tr>
					<td><b>REMARK</b></td>
				</tr>
				<?php if($deliverynote->deliverynote_remark != ''){ ?>
				<tr>
					<td class="line-height-12">
						<?=$deliverynote->deliverynote_remark?>
					</td>
				</tr>
				<?php } ?>
			</table>

			<?php if($deliverynote->deliverynote_payment != ''){ ?>
			<table cellspacing="0" cellpadding="0" class="document-br-10">
				<tr>
					<td><b>PAYMENT</b></td>
				</tr>
				<tr>
					<td class="line-height-12">
						<?=$deliverynote->deliverynote_payment?>
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
							<div class="sign"><?=$deliverynote->deliverynote_user_name?></div>
						</div>
						<div>Authority Signature & Co. Chop</div>
					</td>
				</tr>
			</table>

			<table cellspacing="0" cellpadding="0" class="document-br-10">
				<tr>
					<td class="line-height-12">
						Pleas e return the copy of this deliverynote with your signature and company chop as confirmation of the above offer.
						<br />Address: Flat D, 3/F, Fu Hop Factory Building, 209-211 Wai Yip Street, Kwun Tong,Kowloon, Hong Kong.Tel: 2709 0666 Fax: 2709 0669
					</td>
				</tr>
			</table>
		</div>
		<?php } ?>

		








































	</body>
</html>