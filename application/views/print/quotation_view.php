<?php
defined('BASEPATH') OR exit('No direct script access allowed');

switch($quotation->quotation_currency){
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
		<title>Quotation</title>

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
				<td width="50%" valign="top">
					<h1 class="corpcolor-font">Wallpaper+</h1>
				</td>
				<td width="50%" align="right"><h2><?=$language['quotation']?></h2></td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="50%" valign="top">
					<table cellpadding="0" cellspacing="0">
                        <tr>
                            <td valign="top" width="24%"><b><?=$language['date']?></b></td>
                            <td width="76%"><?=$quotation->quotation_issue?></td>
                        </tr>
						<tr>
							<td valign="top"><b><?=$language['to']?></b></td>
							<td><?=$quotation->quotation_client_company_name?></td>
						</tr>
                        <tr>
                            <td valign="top"><b><?=$language['attn']?></b></td>
                            <td><?=$quotation->quotation_client_name?></td>
                        </tr>
						<tr>
							<td valign="top"><b><?=$language['address']?></b></td>
							<td><?=$quotation->quotation_client_company_address?></td>
						</tr>
						<tr>
							<td valign="top"><b><?=$language['tel']?></b></td>
							<td><?=$quotation->quotation_client_phone?></td>
						</tr>
					</table>
				</td>
				<td width="10%"></td>
				<td width="40%" valign="top">
					<table cellpadding="0" cellspacing="0">
                        <tr>
                            <td valign="top"><b><?=$language['email']?></b></td>
                            <td><?=$quotation->quotation_client_email?></td>
                        </tr>
						<tr>
							<td width="40%"><b><?=$language['quotation_no']?></b></td>
							<td width="60%"><?=$quotation->quotation_number?><?=$quotation->quotation_version?'-R'.$quotation->quotation_version:''?></td>
						</tr>
						<tr>
							<td><b><?=$language['sales']?></b></td>
							<td><?=$quotation->quotation_user_name?></td>
						</tr>
						<tr>
							<td></td>
							<td></td>
						</tr>
                        <tr></tr>
					</table>
				</td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0" class="document-br-20 document-separator-bottom">
			<tr>
				<td width="12%"><b><?=$language['part_no']?></b></td>
				<td width="12%"><b><?=$language['code']?></b></td>
				<td width="12%"><b><?=$language['price_type']?></b></td>
                <td width="12%"><b><?=$language['name']?></b></td>
                <td width="18%" align="left"><b><?=$language['size']?></b></td>
                <td width="8%" align="center"><b><?=$language['qty']?></b></td>
				<td width="13%" align="right"><b><?=$language['unit_price']?></b></td>
				<td width="10%" align="right"><b><?=$language['amount']?></b></td>
			</tr>
		</table>
		<?php } ?>

		








































		<?php if($this->router->fetch_method() == 'content'){ ?>
		<table cellspacing="0" cellpadding="0" class="content-table line-height-12 document-separator-bottom">
			<?php
                foreach($quotationitems as $key => $value){
                    $thisProduct = get_product($value->quotationitem_product_id);
                    $thisUnit = get_unit($thisProduct->product_unit_id);
                ?>
			<tr class="padding-top-5">
				<td width="12%"></td>
				<td width="12%"></td>
				<td width="12%"></td>
                <td width="12%"></td>
                <td width="18%"></td>
				<td width="8%"></td>
				<td width="13%"></td>
				<td width="10%"></td>
			</tr>
			<tr class="padding-bottom-5">
				<td valign="top">
					<?php
					if($quotation->quotation_display_number == 'index_number'){
						echo '<div class="index_number">'.($key + 1).'</div>';
					}else{
						echo '<div class="part_number">'.$value->quotationitem_product_code.'</div>';
					}
					?>
				</td>
				<td valign="top"><?=$value->quotationitem_product_code.' - '.$value->quotationitem_product_color_code?></td>
                <td valign="top"><?=strtoupper($value->quotationitem_price_type)?></td>
                <td valign="top"><?=$value->quotationitem_product_name?></td>
                <td valign="top" align="left"><?=convert_br($value->quotationitem_product_detail)?></td>
                <td valign="top" align="center"><?=$value->quotationitem_quantity.' '.(!empty($thisUnit)?$thisUnit->unit_name:'')?></td>
				<td valign="top" align="right"><?=strtoupper($quotation->quotation_currency).' '.money_format('%!n', $value->quotationitem_product_price)?></td>
				<td valign="top" align="right"><?=strtoupper($quotation->quotation_currency).' '.money_format('%!n', $value->quotationitem_product_price * $value->quotationitem_quantity)?></td>
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
				<td></td>
			</tr>
		</table>

		<table cellspacing="0" cellpadding="0">
			<?php 
				if( !empty($quotation->quotation_category_discount) ){ 
					$quotation_category_discount = json_decode($quotation->quotation_category_discount);
					foreach ($quotation_category_discount as $key => $value) {
			?>
			<tr>
				<td width="12%"></td>
				<td colspan="2" width="70%" align="right"><b><?=strtoupper($value->category_name)?> <?=$language['discount']?></b></td>
				<td width="8%" align="center"><?=strtoupper($quotation->quotation_currency)?></td>
				<td width="10%" align="right"><?=money_format('%!n', $value->category_discount)?></td>
			</tr>
			<?php
					}
				}
			?>
			<?php if($quotation->quotation_discount != 0){ ?>
			<tr>
				<td width="12%"></td>
				<td width="55%"></td>
				<td width="15%" align="right"><b><?=$language['special_discount']?></b></td>
				<td width="8%" align="center"><?=strtoupper($quotation->quotation_currency)?></td>
				<td width="10%" align="right"><?=money_format('%!n', $quotation->quotation_discount)?></td>
			</tr>
			<?php } ?>
            <?php if($quotation->quotation_freight != 0){ ?>
            <tr>
                <td width="12%"></td>
                <td width="55%"></td>
                <td width="15%" align="right"><b><?=$language['freight']?></b></td>
                <td width="8%" align="center"><?=strtoupper($quotation->quotation_currency)?></td>
                <td width="10%" align="right"><?=money_format('%!n', $quotation->quotation_freight)?></td>
            </tr>
            <?php } ?>
			<tr>
				<td width="12%"></td>
				<td width="55%"></td>
				<td width="15%" align="right"><b><?=$language['grand_total']?></b></td>
				<td width="8%" align="center"><?=strtoupper($quotation->quotation_currency)?></td>
				<td width="10%" align="right"><?=money_format('%!n', $quotation->quotation_total)?></td>
			</tr>
		</table>

		<div class="page-break-inside-avoid">
			<!--table cellspacing="0" cellpadding="0" class="document-br-20">
				<tr>
					<td><b>TERMS AND CONDITIONS</b></td>
				</tr>
				<tr>
					<td class="line-height-12">
						1、付款方式：50%訂金於確認及簽回報價單時支付；餘款在送貨前付清。
						<br />
						2a、交貨期：確認簽妥報價單及收妥款項日起計，項目1-15：如廠家有貨約10-14天貨交香港。
						<br />
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;項目16廠家約於9/4完成生產，另加約10-14天貨交香港。
						<br />
						<br />
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(逢星期二/三下單)
						<br />
						2b、由廠家至香港運輸途中如因天氣、清關而導致延誤則不計算於交貨天數內。
						<br />
						3、此報價單內之型號及數量經雙方簽定後不能取消及不接受退貨。
						<br />
						4、因貨品批期右異，買方訂購之型號與樣辨顏色可能有輕微及可接受之差距，乃屬正常。
					</td>
				</tr>
			</table-->

			<?php if($quotation->quotation_remark != ''){ ?>
			<table cellspacing="0" cellpadding="0" class="document-br-10">
				<tr>
					<td><b><?=$language['remark']?></b></td>
				</tr>
				<tr>
					<td class="line-height-12">
						<?=convert_br($quotation->quotation_remark)?>
					</td>
				</tr>
			</table>
			<?php } ?>

			<?php if($quotation->quotation_payment != ''){ ?>
			<table cellspacing="0" cellpadding="0" class="document-br-10">
				<tr>
					<td><b><?=$language['payment']?></b></td>
				</tr>
				<tr>
					<td class="line-height-12">
						<?=convert_br($quotation->quotation_payment)?>
					</td>
				</tr>
			</table>
			<?php } ?>
		</div>
		
		<div class="page-break-inside-avoid">
			<table cellspacing="0" cellpadding="0" class="document-br-20">
				<tr>
					<td width="40%">
						<div><b><?=$language['received_by']?></b></div>
						<div><?=$quotation->quotation_client_company_name?></div>
						<div class="sign-area"></div>
						<div><?=$language['signature']?></div>
					</td>
					<td width="20%"></td>
					<td width="40%">
						<div><b><?=$language['for_and_on_behalf_of']?></b></div>
						<div>Wallpaper+</div>
						<div class="sign-area">
							<div class="sign"><?=$quotation->quotation_user_name?></div>
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