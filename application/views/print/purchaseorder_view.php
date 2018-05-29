<?php
defined('BASEPATH') OR exit('No direct script access allowed');

switch($purchaseorder->purchaseorder_currency){
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
		<title>Purchase order</title>

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
				<td width="15%" align="right"><h2>Purchase order</h2></td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="50%" valign="top">
					<table cellpadding="0" cellspacing="0">
                        <tr>
                            <td valign="top"><b>Vendor</b></td>
                            <td><?=$purchaseorder->purchaseorder_vendor_company_name?></td>
                        </tr>
                        <tr>
                            <td valign="top"><b>Contact</b></td>
                            <td><?=$purchaseorder->purchaseorder_vendor_name?></td>
                        </tr>
                        <tr>
                            <td valign="top"><b>Tel</b></td>
                            <td><?=$purchaseorder->purchaseorder_vendor_company_phone?></td>
                        </tr>
                        <tr>
                            <td valign="top" width="24%"><b>Fax.</b></td>
                            <td width="76%"><?=$purchaseorder->purchaseorder_vendor_phone?></td>
                        </tr>
                        <tr>
                            <td valign="top"><b>Shipment</b></td>
                            <td><?=$purchaseorder->purchaseorder_shipment?></td>
                        </tr>
                        <tr>
                            <td valign="top"><b>Delivery Invoice No.</b></td>
                            <td><?=$purchaseorder->purchaseorder_delivery_invoice_no?></td>
                        </tr>
                        <tr>
                            <td valign="top"><b>Delivered To</b></td>
                            <td colspan="3"><?=$purchaseorder->purchaseorder_delivery_address?></td>
                        </tr>
					</table>
				</td>
				<td width="10%"></td>
				<td width="40%" valign="top">
					<table cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="40%"><b>Purchase order No.</b></td>
                            <td width="60%"><?=$purchaseorder->purchaseorder_number?></td>
                        </tr>
                        <tr>
                            <td><b>Sales</b></td>
                            <td><?=$purchaseorder->purchaseorder_user_name?></td>
                        </tr>
						<tr>
							<td><b>Date of order</b></td>
							<td><?=$purchaseorder->purchaseorder_issue?></td>
						</tr>
                        <tr>
                            <td><b>Arrival Date</b></td>
                            <td><?=$purchaseorder->purchaseorder_arrive_date?></td>
                        </tr>
						<tr>
							<td><b>Tel. No.</b></td>
							<td><?=$purchaseorder->purchaseorder_tel_no?></td>
						</tr>
						<tr>
							<td><b>Fax No.</b></td>
							<td><?=$purchaseorder->purchaseorder_fax_no?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0" class="document-br-20 document-separator-bottom">
			<tr>
				<td width="12%"><b>Our ref.</b></td>
				<td width="20%"><b>DESCRIPTION</b></td>
                <td width="15%"><b>QUANTITY</b></td>
				<td width="15%"><b>LIST PRICE</b></td>
				<td width="8%"><b>NET PRICE</b></td>
				<td width="10%" align="right"><b>AMOUNT</b></td>
			</tr>
		</table>
		<?php } ?>

		








































		<?php if($this->router->fetch_method() == 'content'){ ?>
		<table cellspacing="0" cellpadding="0" class="content-table line-height-12 document-separator-bottom">
			<?php
            $total = 0;
            $quantity_count = 0;
            $weight_count = 0;
            foreach($salesorder->invoices as $key => $value){
			    ?>
			<tr class="padding-top-5">
                <td width="12%"></td>
                <td width="20%"></td>
                <td width="15%"></td>
                <td width="15%"></td>
                <td width="8%"></td>
                <td width="10%"></td>
			</tr>
			<tr class="padding-bottom-5">
				<td valign="top"><?=$value->invoice_number?></td>
				<td valign="top">
                    <?php
                    foreach ($value->invoiceitems as $key1 => $value1) {
                        echo '<div>'.$value1->invoiceitem_product_code.' - '.$value1->invoiceitem_product_name.'</div>';
                    }
                    ?>
                </td>
                <td valign="top">
                    <?php
                    foreach ($value->invoiceitems as $key1 => $value1) {
                        $quantity_count += $value1->invoiceitem_quantity;
                        echo '<div>'.$value1->invoiceitem_quantity.' '.$value1->invoiceitem_unit.'</div>';
                    }
                    ?>
                </td>
                <td valign="top">
                    <?php
                    foreach ($value->invoiceitems as $key1 => $value1) {
                        echo '<div>'.strtoupper($value->invoice_currency).' '.money_format('%!n', $value1->invoiceitem_product_price).'</div>';
                    }
                    ?>
                </td>
                <td valign="top">
                    <?php
                    foreach ($value->invoiceitems as $key1 => $value1) {
                        echo '<div>'.'xxx'.'</div>';
                    }
                    ?>
                </td>
				<td valign="top" align="right">
                    <?php
                    foreach ($value->invoiceitems as $key1 => $value1) {
                        $total += $value1->invoiceitem_product_price * $value1->invoiceitem_quantity;
                        echo '<div>'.strtoupper($value->invoice_currency).' '.money_format('%!n', $value1->invoiceitem_product_price * $value1->invoiceitem_quantity).'</div>';
                    }
                    ?>
                </td>
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
			<tr>
                <td width="12%"></td>
                <td width="20%"></td>
                <td width="15%"><?=$quantity_count?></td>
                <td width="15%"><b>GRAND TOTAL</b></td>
                <td width="8%"><?=strtoupper($purchaseorder->purchaseorder_currency)?></td>
                <td width="10%" align="right"><?=money_format('%!n', $total)?></td>
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
						<br />This purchaseorder is also an order confirmation. Once the order is confirmed, 100% balance of the total amount will be charged to the customer as a penalty for order cancellation.
						<br />This purchaseorder will expired on above expired date or unless otherwise stated and subject to change without notice.
					</td>
				</tr>
			</table>

			<table cellspacing="0" cellpadding="0" class="document-br-10">
				<tr>
					<td><b>REMARK</b></td>
				</tr>
				<?php if($purchaseorder->purchaseorder_remark != ''){ ?>
				<tr>
					<td class="line-height-12">
						<?=$purchaseorder->purchaseorder_remark?>
					</td>
				</tr>
				<?php } ?>
			</table>

			<?php if($purchaseorder->purchaseorder_payment != ''){ ?>
			<table cellspacing="0" cellpadding="0" class="document-br-10">
				<tr>
					<td><b>PAYMENT</b></td>
				</tr>
				<tr>
					<td class="line-height-12">
						<?=$purchaseorder->purchaseorder_payment?>
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
							<div class="sign"><?=$purchaseorder->purchaseorder_user_name?></div>
						</div>
						<div>Authority Signature & Co. Chop</div>
					</td>
				</tr>
			</table>

			<table cellspacing="0" cellpadding="0" class="document-br-10">
				<tr>
					<td class="line-height-12">
						Pleas e return the copy of this purchaseorder with your signature and company chop as confirmation of the above offer.
						<br />Address: Flat D, 3/F, Fu Hop Factory Building, 209-211 Wai Yip Street, Kwun Tong,Kowloon, Hong Kong.Tel: 2709 0666 Fax: 2709 0669
					</td>
				</tr>
			</table>
		</div>
		<?php } ?>

		








































	</body>
</html>