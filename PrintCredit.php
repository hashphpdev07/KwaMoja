<?php

include('includes/session.php');
include('includes/SQL_CommonFunctions.php');

$AutoPrintPage = 1;

if (isset($_GET['FromTransNo'])) {
	$FromTransNo = trim($_GET['FromTransNo']);
} elseif (isset($_POST['FromTransNo'])) {
	$FromTransNo = filter_number_format($_POST['FromTransNo']);
} else {
	$FromTransNo = '';
}

if (isset($_GET['ToTransNo'])) {
	$_POST['ToTransNo'] = $_GET['ToTransNo'];
}

if (!isset($_POST['ToTransNo']) or trim($_POST['ToTransNo']) == '' or filter_number_format($_POST['ToTransNo']) < $FromTransNo) {
	$_POST['ToTransNo'] = $FromTransNo;
}

$Title = _('Print Credit Notes') . ' - ' . _('From') . ' ' . $FromTransNo . ' ' . _('to') . ' ' . $_POST['ToTransNo'];

include('includes/header.php');
echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $_SESSION['Theme'] . '/images/printer.png" title="' . $Title . '" alt="" />' . ' ' . $Title . '</p>';

echo '<link href="', $RootPath, '/companies/' . $_SESSION['DatabaseName'] . '/FormDesigns/credit.css" rel="stylesheet" type="text/css" />';

while ($FromTransNo <= filter_number_format($_POST['ToTransNo'])) {

	/* Fetch the header details */
	$SQL = "SELECT debtortrans.trandate,
					debtortrans.ovamount,
					debtortrans.ovdiscount,
					debtortrans.ovfreight,
					debtortrans.ovgst,
					debtortrans.rate,
					debtortrans.invtext,
					debtortrans.packages,
					debtortrans.consignment,
					debtorsmaster.name,
					debtorsmaster.address1,
					debtorsmaster.address2,
					debtorsmaster.address3,
					debtorsmaster.address4,
					debtorsmaster.address5,
					debtorsmaster.address6,
					debtorsmaster.currcode,
					debtorsmaster.invaddrbranch,
					debtorsmaster.taxref,
					debtorsmaster.language_id,
					paymentterms.terms,
					salesorders.deliverto,
					salesorders.deladd1,
					salesorders.deladd2,
					salesorders.deladd3,
					salesorders.deladd4,
					salesorders.deladd5,
					salesorders.deladd6,
					salesorders.customerref,
					salesorders.orderno,
					salesorders.orddate,
					locations.locationname,
					shippers.shippername,
					custbranch.brname,
					custbranch.braddress1,
					custbranch.braddress2,
					custbranch.braddress3,
					custbranch.braddress4,
					custbranch.braddress5,
					custbranch.braddress6,
					custbranch.brpostaddr1,
					custbranch.brpostaddr2,
					custbranch.brpostaddr3,
					custbranch.brpostaddr4,
					custbranch.brpostaddr5,
					custbranch.brpostaddr6,
					salesman.salesmanname,
					debtortrans.debtorno,
					debtortrans.branchcode,
					currencies.decimalplaces
				FROM debtortrans
				INNER JOIN debtorsmaster
					ON debtortrans.debtorno=debtorsmaster.debtorno
				INNER JOIN custbranch
					ON debtortrans.debtorno=custbranch.debtorno
					AND debtortrans.branchcode=custbranch.branchcode
				INNER JOIN salesorders
					ON debtortrans.order_ = salesorders.orderno
				INNER JOIN shippers
					ON debtortrans.shipvia=shippers.shipper_id
				INNER JOIN salesman
					ON custbranch.salesman=salesman.salesmancode
				INNER JOIN locations
					ON salesorders.fromstkloc=locations.loccode
				INNER JOIN locationusers
					ON locationusers.loccode=locations.loccode
					AND locationusers.userid='" .  $_SESSION['UserID'] . "'
					AND locationusers.canview=1
				INNER JOIN paymentterms
					ON debtorsmaster.paymentterms=paymentterms.termsindicator
				INNER JOIN currencies
					ON debtorsmaster.currcode=currencies.currabrev
				WHERE debtortrans.type=11
					AND debtortrans.transno='" . $FromTransNo . "'";
	$Result = DB_query($SQL, '', '', false, false);

	if (DB_error_no() != 0) {
		$Title = _('Transaction Print Error Report');
		include('includes/header.php');
		prnMsg(_('There was a problem retrieving the credit note details for note number') . ' ' . $InvoiceToPrint . ' ' . _('from the database') . '. ' . _('To print a credit, the sales order record, the customer transaction record and the branch record for the customer must not have been purged') . '. ' . _('To print a credit note only requires the customer, transaction, salesman and branch records be available'), 'error');
		if ($Debug == 1) {
			prnMsg(_('The SQL used to get this information that failed was') . '<br />' . $SQL, 'error');
		}
		include('includes/footer.php');
		exit;
	}
	if (DB_num_rows($Result) == 1) {
		$MyRow = DB_fetch_array($Result);
		/* Get the correct address */
		if ($MyRow['invaddrbranch'] == 0) {
			$InvoiceAddress1 = html_entity_decode($MyRow['name']);
			$InvoiceAddress2 = html_entity_decode($MyRow['address1']);
			$InvoiceAddress3 = html_entity_decode($MyRow['address2']);
			$InvoiceAddress4 = html_entity_decode($MyRow['address3']) . ' ' . html_entity_decode($MyRow['address4']);
		} else {
			$InvoiceAddress1 = html_entity_decode($MyRow['name']);
			$InvoiceAddress2 = html_entity_decode($MyRow['brpostaddr1']);
			$InvoiceAddress3 = html_entity_decode($MyRow['brpostaddr2']);
			$InvoiceAddress4 = html_entity_decode($MyRow['brpostaddr3']) . ' ' . html_entity_decode($MyRow['brpostaddr4']) . ' ' . html_entity_decode($MyRow['brpostaddr5']) . ' ' . html_entity_decode($MyRow['brpostaddr6']);
		}

		echo '<table class="Main" title="', _('Main table for Credit note'), '">
				<thead>
					<tr>
						<td colspan="2" class="right_side CompanyAddress">
							<img class="logo" src="', $_SESSION['LogoFile'], '" /><br />',
							$_SESSION['CompanyRecord']['coyname'], '<br />',
							$_SESSION['CompanyRecord']['regoffice1'], '<br />',
							$_SESSION['CompanyRecord']['regoffice2'], '<br />',
							$_SESSION['CompanyRecord']['regoffice3'], '<br />',
							$_SESSION['CompanyRecord']['regoffice4'], '<br />',
							$_SESSION['CompanyRecord']['regoffice5'], ' ', $_SESSION['CompanyRecord']['regoffice6'], '<br />',
							_('Tel') . ': ' . $_SESSION['CompanyRecord']['telephone'] . '<br />' .
							_('Fax') . ': ' . $_SESSION['CompanyRecord']['fax'], '<br />',
							$_SESSION['CompanyRecord']['email'], '
						</td>
					</tr>
					<tr>
						<td colspan="2" class="TransType">
							<div class="centre">
							<img class="barcode" alt="Credit note - ', $FromTransNo, '" src="includes/barcode.php?text=', $FromTransNo, '" />
							', _('Credit Note'), '</div>
						</td>
					</tr>
					<tr>
						<td class="InvoiceAddress left_side">',
							'<b>', _('Sold To'), ':</b><br />',
							$InvoiceAddress1, '<br />',
							$InvoiceAddress2, '<br />',
							$InvoiceAddress3, '<br />',
							$InvoiceAddress4, '<br />
						</td>
						<td class="DeliveryAddress right_side">',
							'<b>', _('Deliver To'), ':</b><br />',
							html_entity_decode($MyRow['deliverto']), '<br />',
							html_entity_decode($MyRow['deladd1']), '<br />',
							html_entity_decode($MyRow['deladd2']), '<br />',
							html_entity_decode($MyRow['deladd3']) . ' ' . html_entity_decode($MyRow['deladd4']) . ' ' . html_entity_decode($MyRow['deladd5']), '<br />
						</td>
					</tr>
					<tr>
						<td colspan="2" class="InvoiceDetails">
							<table>
								<tr>
									<td>', _('Credit Note No'), ':  ', $FromTransNo, '</td>
									<td>', _('Order Date'), ':  ', ConvertSQLDate($MyRow['orddate']), '</td>
									<td>', _('Credit Note Date'), ':  ', ConvertSQLDate($MyRow['trandate']), '</td>
									<td>', _('Payment Terms'), ':  ', $MyRow['terms'], '</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<table class="HeaderTable" style="border:1px solid black">
								<tr>
									<td class="ItemCodeColumn">', _('Item Code'), '</td>
									<td class="ItemDescriptionColumn">', _('Description'), '</td>
									<td class="ItemPriceColumn">', _('Unit Price'), '</td>
									<td class="ItemQuantityColumn">', _('Quantity'), '</td>
									<td class="ItemUOMColumn">', _('UOM'), '</td>
									<td class="ItemDiscountColumn">', _('Discount'), '</td>
									<td class="ItemValueColumn">', _('Value'), '</td>
								</tr>
							</table>
						</td>
					</tr>
				</thead>';
		//Change the language to the customer's language
		$_SESSION['Language'] = $MyRow['language_id'];
		include('includes/LanguageSetup.php');

		$ExchRate = $MyRow['rate'];
		$SQL = "SELECT stockmoves.stockid,
						stockmaster.description,
						-stockmoves.qty as quantity,
						stockmoves.discountpercent,
						((1 - stockmoves.discountpercent) * stockmoves.price * " . $ExchRate . "* -stockmoves.qty) AS fxnet,
						(stockmoves.price * " . $ExchRate . ") AS fxprice,
						stockmoves.narrative,
						stockmaster.units,
						stockmaster.decimalplaces
					FROM stockmoves INNER JOIN stockmaster
					ON stockmoves.stockid = stockmaster.stockid
					WHERE stockmoves.type=11
					AND stockmoves.transno=" . $FromTransNo . "
					AND stockmoves.show_on_inv_crds=1";
		$LineResult = DB_query($SQL);
		echo '<tbody>';
		echo '<tr>
				<td colspan="2">
					<table class="HeaderTable" style="border-collapse:collapse;border:1px solid black">';
		while ($MyLineRow = DB_fetch_array($LineResult)) {
			echo '<tr>
					<td class="ItemCodeColumn">', $MyLineRow['stockid'], '</td>
					<td class="ItemDescriptionColumn">', $MyLineRow['description'], '</td>
					<td class="ItemPriceColumn number">', locale_number_format($MyLineRow['fxprice'], $MyRow['decimalplaces']), '</td>
					<td class="ItemQuantityColumn number">', locale_number_format($MyLineRow['quantity'], $MyLineRow['decimalplaces']), '</td>
					<td class="ItemUOMColumn">', $MyLineRow['units'], '</td>
					<td class="ItemDiscountColumn number">', $MyLineRow['discountpercent'], '</td>
					<td class="ItemValueColumn number">', locale_number_format($MyLineRow['fxnet'], $MyRow['decimalplaces']), '</td>
				</tr>';
		}
		echo '</table>
					</td>
				</tr>';
		echo '</tbody>';
		echo '<tfoot>
				<tr>
					<td colspan="2">
						<table class="SummaryTable">
							<tr>
								<td class="SummaryBlankColumn"></td>
								<td class="SummaryLabelColumn">', _('Sub-Total'). '</td>
								<td class="SummaryValueColumn number">', locale_number_format($MyRow['ovamount'], $MyRow['decimalplaces']), '</td>
							</tr>
							<tr>
								<td class="SummaryBlankColumn"></td>
								<td class="SummaryLabelColumn">', _('Freight'). '</td>
								<td class="SummaryValueColumn number">', locale_number_format($MyRow['ovfreight'], $MyRow['decimalplaces']), '</td>
							</tr>
							<tr>
								<td class="SummaryBlankColumn"></td>
								<td class="SummaryLabelColumn">', _('VAT'). '</td>
								<td class="SummaryValueColumn number">', locale_number_format($MyRow['ovgst'], $MyRow['decimalplaces']), '</td>
							</tr>
							<tr>
								<td class="SummaryBlankColumn"></td>
								<td class="SummaryLabelColumn">', _('Total'). '</td>
								<td class="SummaryValueColumn number">', locale_number_format($MyRow['ovfreight'] + $MyRow['ovgst'] + $MyRow['ovamount'], $MyRow['decimalplaces']), '</td>
							</tr>
						</table>
					</td>
				</tr>
			</tfoot>';
	}
	$FromTransNo++;
	echo '</table>';
}

echo '<div class="centre links">
		<a href="">', _('Re-print this credit note run'), '</a>
	</div>';

include('includes/footer.php');

?>