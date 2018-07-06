<?php
defined('BASEPATH') OR exit('No direct script access allowed');

switch($salesorder->salesorder_currency){
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
		<title>Sales order</title>

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
				<td width="15%" align="right"><h2>Sales order</h2></td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="50%" valign="top">
					<table cellpadding="0" cellspacing="0">
                        <tr>
                            <td valign="top" width="14%"><b>Date</b></td>
                            <td width="76%"><?=$salesorder->salesorder_issue?></td>
                        </tr>
						<tr>
							<td valign="top"><b>To</b></td>
							<td><?=$salesorder->salesorder_client_company_name?></td>
						</tr>
                        <tr>
                            <td valign="top"><b>Attn</b></td>
                            <td><?=$salesorder->salesorder_client_name?></td>
                        </tr>
						<tr>
							<td valign="top"><b>Address</b></td>
							<td><?=$salesorder->salesorder_client_company_address?></td>
						</tr>
						<tr>
							<td valign="top"><b>Tel</b></td>
							<td><?=$salesorder->salesorder_client_phone?></td>
						</tr>
					</table>
				</td>
				<td width="10%"></td>
				<td width="40%" valign="top">
					<table cellpadding="0" cellspacing="0">
                        <tr>
                            <td valign="top"><b>Email</b></td>
                            <td><?=$salesorder->salesorder_client_email?></td>
                        </tr>
						<tr>
							<td width="40%"><b>Sales order No.</b></td>
							<td width="60%"><?=$salesorder->salesorder_number?></td>
						</tr>

						<tr>
							<td><b>Sales</b></td>
							<td><?=$salesorder->salesorder_user_name?></td>
						</tr>
						<tr>
							<td><b>Expire Date</b></td>
							<td><?=$salesorder->salesorder_expire?></td>
						</tr>
                        <tr></tr>
					</table>
				</td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0" class="document-br-20 document-separator-bottom">
            <tr>
                <td width="12%"><b>PART NO.</b></td>
                <td width="12%"><b>CODE</b></td>
                <td width="12%"><b>NAME</b></td>
                <td width="11%" align="center"><b>SIZE</b></td>
                <td width="8%" align="center"><b>QTY</b></td>
                <td width="13%" align="right"><b>UNIT PRICE</b></td>
                <td width="10%" align="right"><b>AMOUNT</b></td>
            </tr>
		</table>
		<?php } ?>

		








































		<?php if($this->router->fetch_method() == 'content'){ ?>
		<table cellspacing="0" cellpadding="0" class="content-table line-height-12 document-separator-bottom">
			<?php
                foreach($salesorderitems as $key => $value){
                    $thisProduct = get_product($value->salesorderitem_product_id);
                ?>
			<tr class="padding-top-5">
                <td width="12%"></td>
                <td width="12%"></td>
                <td width="12%"></td>
                <td width="11%"></td>
                <td width="8%"></td>
                <td width="13%"></td>
                <td width="10%"></td>
			</tr>
			<tr class="padding-bottom-5">
				<td valign="top">
					<div class="part_number"><?=$value->salesorderitem_product_code?></div>
				</td>
                <td valign="top"><?=$value->salesorderitem_product_code?></td>
                <td valign="top"><?=$value->salesorderitem_product_name?></td>
                <td valign="top" align="center"><?=$value->salesorderitem_product_detail?></td>
                <td valign="top" align="center"><?=$value->salesorderitem_quantity.' '.get_unit($thisProduct->product_unit_id)->unit_name?></td>
                <td valign="top" align="right"><?=strtoupper($salesorder->salesorder_currency).' '.money_format('%!n', $value->salesorderitem_product_price)?></td>
                <td valign="top" align="right"><?=strtoupper($salesorder->salesorder_currency).' '.money_format('%!n', $value->salesorderitem_product_price * $value->salesorderitem_quantity)?></td>
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
				if( !empty($salesorder->salesorder_category_discount) ){ 
					$salesorder_category_discount = json_decode($salesorder->salesorder_category_discount);
					foreach ($salesorder_category_discount as $key => $value) {
			?>
			<tr>
				<td width="12%"></td>
				<td width="55%"></td>
				<td colspan="2" width="70%" align="right"><b><?=strtoupper($value->category_name)?> DISCOUNT</b></td>
				<td width="8%" align="center"><?=strtoupper($salesorder->salesorder_currency)?></td>
				<td width="10%" align="right"><?=money_format('%!n', $value->category_discount)?></td>
			</tr>
			<?php 	
					}
				}
			?>
			<?php if($salesorder->salesorder_discount != 0){ ?>
			<tr>
				<td width="12%"></td>
				<td width="55%"></td>
				<td width="15%" align="right"><b>DISCOUNT</b></td>
				<td width="8%" align="center"><?=strtoupper($salesorder->salesorder_currency)?></td>
                <td width="10%" align="right"><?=money_format('%!n', $salesorder->salesorder_discount)?></td>
			</tr>
			<?php } ?>
			<?php if($salesorder->salesorder_freight != 0){ ?>
            <tr>
                <td width="12%"></td>
                <td width="55%"></td>
                <td width="15%" align="right"><b>FREIGHT</b></td>
                <td width="8%" align="center"><?=strtoupper($salesorder->salesorder_currency)?></td>
                <td width="10%" align="right"><?=money_format('%!n', $salesorder->salesorder_freight)?></td>
            </tr>
            <?php } ?>
			<tr>
				<td width="12%"></td>
				<td width="55%"></td>
				<td width="15%" align="right"><b>GRAND TOTAL</b></td>
				<td width="8%" align="center"><?=strtoupper($salesorder->salesorder_currency)?></td>
				<td width="10%" align="right"><?=money_format('%!n', $salesorder->salesorder_total)?></td>
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
						<br />Cheque(s) should be crossed & made payable to Wallpaper+.
						<br />This salesorder is also an order confirmation. Once the order is confirmed, 100% balance of the total amount will be charged to the customer as a penalty for order cancellation.
						<br />This salesorder will expired on above expired date or unless otherwise stated and subject to change without notice.
					</td>
				</tr>
			</table>

			<table cellspacing="0" cellpadding="0" class="document-br-10">
				<tr>
					<td><b>REMARK</b></td>
				</tr>
				<?php if($salesorder->salesorder_remark != ''){ ?>
				<tr>
					<td class="line-height-12">
						<?=$salesorder->salesorder_remark?>
					</td>
				</tr>
				<?php } ?>
			</table>

			<?php if($salesorder->salesorder_payment != ''){ ?>
			<table cellspacing="0" cellpadding="0" class="document-br-10">
				<tr>
					<td><b>PAYMENT</b></td>
				</tr>
				<tr>
					<td class="line-height-12">
						<?=$salesorder->salesorder_payment?>
					</td>
				</tr>
			</table>
			<?php } ?>
		</div>
		
		<!-- <div class="page-break-inside-avoid">
			<table cellspacing="0" cellpadding="0" class="document-br-20">
				<tr>
					<td width="40%">
						<div><b>Received By</b></div>
						<div><?=$salesorder->salesorder_client_company_name?></div>
						<div class="sign-area"></div>
						<div>Authority Signature & Co. Chop</div>
					</td>
					<td width="20%"></td>
					<td width="40%">
						<div><b>For and on behalf of</b></div>
						<div>Wallpaper+</div>
						<div class="sign-area">
							<div class="sign"><?=$salesorder->salesorder_user_name?></div>
						</div>
						<div>Authority Signature & Co. Chop</div>
					</td>
				</tr>
			</table>

			<table cellspacing="0" cellpadding="0" class="document-br-10">
				<tr>
					<td class="line-height-12">
						Please return the copy of this salesorder with your signature and company chop as confirmation of the above offer.
						<br />Address: 9 Floor The Hennessy 256 Hennessy Road Wan Chal Hong Kong. 
						<br />Tel: +852 3525 1785 Fax: +852 3525 1784 Email: sales@wallpaperplus.com.hk
					</td>
				</tr>
			</table>
		</div> -->
		<?php } ?>

		








































	</body>
</html>