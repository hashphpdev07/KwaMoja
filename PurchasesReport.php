<?php
/* $Id: PurchasesReport.php 7672 2016-11-27 10:42:50Z rchacon $ */
/* Shows a report of purchases to suppliers for the range of selected dates. */
/* This program is under the GNU General Public License, last version. Rafael E. Chacón, 2016-12-18. */
/* This creative work is under the CC BY-NC-SA, later version. Rafael E. Chacón, 2016-12-18. */

// Notes:
// Coding Conventions/Style: http://www.weberp.org/CodingConventions.html

// BEGIN: Functions division ---------------------------------------------------
// END: Functions division -----------------------------------------------------

// BEGIN: Procedure division ---------------------------------------------------
include('includes/session.php');
$Title = _('Purchases from Suppliers');
$ViewTopic = 'PurchaseOrdering';
$BookMark = 'PurchasesReport';
include('includes/header.php');

echo '<p class="page_title_text"><img alt="" src="', $RootPath, '/css/', $_SESSION['Theme'],
	'/images/reports.png" title="', // Icon image.
	$Title, '" /> ', // Icon title.
	$Title, '</p>';// Page title.

// Merges gets into posts:
if(isset($_GET['PeriodFrom'])) {// Select period from.
	$_POST['PeriodFrom'] = $_GET['PeriodFrom'];
}
if(isset($_GET['PeriodTo'])) {// Select period to.
	$_POST['PeriodTo'] = $_GET['PeriodTo'];
}
if(isset($_GET['ShowDetails'])) {// Show the budget for the period.
	$_POST['ShowDetails'] = $_GET['ShowDetails'];
}

// Main code:
if(isset($_POST['PeriodFrom']) and isset($_POST['PeriodTo']) and $_POST['Action']!='New') {// If all parameters are set and valid, generates the report:

	// Validates the data submitted in the form:
	if(Date1GreaterThanDate2($_POST['PeriodFrom'], $_POST['PeriodTo'])) {// RChacon: Is it the correct way to do this? **********************************************************************************
		// The beginning is after the end.
		unset($_POST['PeriodFrom']);
		unset($_POST['PeriodTo']);
		prnMsg(_('The beginning of the period should be before or equal to the end of the period. Please reselect the reporting period.'), 'error');
	}
	$TotalGlAmount = 0;
	$TotalGlTax = 0;
	$k = 1;// Row colour counter.
	$PeriodFrom = ConvertSQLDate($_POST['PeriodFrom']);
	$PeriodTo = ConvertSQLDate($_POST['PeriodTo']);
	if(isset($_POST['ShowDetails'])) {// Parameters: PeriodFrom, PeriodTo, ShowDetails=on.
		echo '<table class="selection">
				<thead>
					<tr>
						<th>', _('Date'), '</th>
						<th>', _('Purchase Invoice'), '</th>
						<th>', _('Reference'), '</th>
						<th>', _('Original Overall Amount'), '</th>
						<th>', _('Original Overall Taxes'), '</th>
						<th>', _('Original Overall Total'), '</th>
						<th>', _('GL Overall Amount'), '</th>
						<th>', _('GL Overall Taxes'), '</th>
						<th>', _('GL Overall Total'), '</th>
						<th>
							<img src="' . $RootPath . '/css/' . $_SESSION['Theme'] . '/images/printer.png" class="PrintIcon" title="' . _('Print') . '" alt="' . _('Print') . '" onclick="window.print();" />
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="9"><br /><b>' .
							_('Notes') . '</b><br />' .
							_('Original amounts in the supplier\'s currency. GL amounts in the functional currency.') .
						'</td>
					</tr>
				</tfoot>';// Common table code.
		$SupplierId = '';
		$SupplierOvAmount = 0;
		$SupplierOvTax = 0;
		$SupplierGlAmount = 0;
		$SupplierGlTax = 0;
		$Sql = "SELECT
					supptrans.supplierno,
					suppliers.suppname,
					suppliers.currcode,
					supptrans.trandate,
					supptrans.suppreference,
					supptrans.transno,
					supptrans.ovamount,
					supptrans.ovgst,
					supptrans.rate
				FROM supptrans
					INNER JOIN suppliers ON supptrans.supplierno=suppliers.supplierid
				WHERE supptrans.trandate>='" . $PeriodFrom . "'
					AND supptrans.trandate<='" . $PeriodTo . "'
					AND supptrans.`type`=20
				ORDER BY supptrans.supplierno, supptrans.trandate";
		$Result = DB_query($Sql);
		include('includes/CurrenciesArray.php'); // To get the currency name from the currency code.
		echo '<tbody>';
		while ($MyRow = DB_fetch_array($Result)) {
			if($MyRow['supplierno'] != $SupplierId) {// If different, prints supplier totals:
				if($SupplierId != '') {// If NOT the first line.
					echo '<tr>',
							'<td colspan="3">&nbsp;</td>',
							'<td class="number">', locale_number_format($SupplierOvAmount, $_SESSION['CompanyRecord']['decimalplaces']), '</td>',
							'<td class="number">', locale_number_format($SupplierOvTax, $_SESSION['CompanyRecord']['decimalplaces']), '</td>',
							'<td class="number">', locale_number_format($SupplierOvAmount+$SupplierOvTax, $_SESSION['CompanyRecord']['decimalplaces']), '</td>',
							'<td class="number">', locale_number_format($SupplierGlAmount, $_SESSION['CompanyRecord']['decimalplaces']), '</td>',
							'<td class="number">', locale_number_format($SupplierGlTax, $_SESSION['CompanyRecord']['decimalplaces']), '</td>',
							'<td class="number">', locale_number_format($SupplierGlAmount+$SupplierGlTax, $_SESSION['CompanyRecord']['decimalplaces']), '</td>',
						'</tr>';
				}
				echo '<tr>
						<td colspan="9">&nbsp;</td>
					</tr>';
				echo '<tr>
						<td class="text" colspan="9">', $MyRow['supplierno'], ' - ', $MyRow['suppname'], ' - ', $MyRow['currcode'], ' ', $CurrencyName[$MyRow['currcode']], '</td>
					</tr>';
				$TotalGlAmount += $SupplierGlAmount;
				$TotalGlTax += $SupplierGlTax;
				$SupplierId = $MyRow['supplierno'];
				$SupplierOvAmount = 0;
				$SupplierOvTax = 0;
				$SupplierGlAmount = 0;
				$SupplierGlTax = 0;
			}
			if($k == 1) {
				echo '<tr class="OddTableRows">';
				$k = 0;
			} else {
				echo '<tr class="EvenTableRows">';
				$k = 1;
			}
			$GlAmount = $MyRow['ovamount']/$MyRow['rate'];
			$GlTax = $MyRow['ovgst']/$MyRow['rate'];
			echo	'<td class="centre">', $MyRow['trandate'], '</td>',
					'<td class="number">', $MyRow['transno'], '</td>',
					'<td class="text">', $MyRow['suppreference'], '</td>',
					'<td class="number">', locale_number_format($MyRow['ovamount'], $_SESSION['CompanyRecord']['decimalplaces']), '</td>',
					'<td class="number">', locale_number_format($MyRow['ovgst'], $_SESSION['CompanyRecord']['decimalplaces']), '</td>',
					'<td class="number"><a href="', $RootPath, '/SuppWhereAlloc.php?TransType=20&TransNo=', $MyRow['transno'], '&amp;ScriptFrom=PurchasesReport" target="_blank" title="', _('Click to view where allocated'), '">', locale_number_format($MyRow['ovamount']+$MyRow['ovgst'], $_SESSION['CompanyRecord']['decimalplaces']), '</a></td>',
					'<td class="number">', locale_number_format($GlAmount, $_SESSION['CompanyRecord']['decimalplaces']), '</td>',
					'<td class="number">',	locale_number_format($GlTax, $_SESSION['CompanyRecord']['decimalplaces']), '</td>',
					'<td class="number"><a href="', $RootPath, '/GLTransInquiry.php?TypeID=20&amp;TransNo=', $MyRow['transno'], '&amp;ScriptFrom=PurchasesReport" target="_blank" title="', _('Click to view the GL entries'), '">', locale_number_format($GlAmount+$GlTax, $_SESSION['CompanyRecord']['decimalplaces']), '</a></td>', // RChacon: Should be "Click to view the General Ledger transaction" instead?
				'</tr>';
			$SupplierOvAmount += $MyRow['ovamount'];
			$SupplierOvTax += $MyRow['ovgst'];
			$SupplierGlAmount += $GlAmount;
			$SupplierGlTax += $GlTax;
		}

		// Prints last supplier total:
		echo '<tr>
				<td colspan="3">&nbsp;</td>
				<td class="number">', locale_number_format($SupplierOvAmount, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
				<td class="number">', locale_number_format($SupplierOvTax, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
				<td class="number">', locale_number_format($SupplierOvAmount+$SupplierOvTax, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
				<td class="number">', locale_number_format($SupplierGlAmount, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
				<td class="number">', locale_number_format($SupplierGlTax, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
				<td class="number">', locale_number_format($SupplierGlAmount+$SupplierGlTax, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
			</tr>
			<tr>
				<td colspan="9">&nbsp;</td>
			</tr>';
		echo '</tbody>';

		$TotalGlAmount += $SupplierGlAmount;
		$TotalGlTax += $SupplierGlTax;

	} else {// Parameters: PeriodFrom, PeriodTo, ShowDetails=off.
		// RChacon: Needs to update the table_sort function to use in this table.
		echo '<table class="selection">
				<thead>
					<tr>
						<th>', _('Supplier Code'), '</th>
						<th>', _('Supplier Name'), '</th>
						<th>', _('Supplier\'s Currency'), '</th>
						<th>', _('Original Overall Amount'), '</th>
						<th>', _('Original Overall Taxes'), '</th>
						<th>', _('Original Overall Total'), '</th>
						<th>', _('GL Overall Amount'), '</th>
						<th>', _('GL Overall Taxes'), '</th>
						<th>', _('GL Overall Total'), '</th>
						<th>
							<img src="' . $RootPath . '/css/' . $_SESSION['Theme'] . '/images/printer.png" class="PrintIcon" title="' . _('Print') . '" alt="' . _('Print') . '" onclick="window.print();" />
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="9"><br /><b>' .
							_('Notes') . '</b><br />' .
							_('Original amounts in the supplier\'s currency. GL amounts in the functional currency.') .
						'</td>
					</tr>
				</tfoot>';
		$Sql = "SELECT
					supptrans.supplierno,
					suppliers.suppname,
					suppliers.currcode,
					SUM(supptrans.ovamount) AS SupplierOvAmount,
					SUM(supptrans.ovgst) AS SupplierOvTax,
					SUM(supptrans.ovamount/supptrans.rate) AS SupplierGlAmount,
					SUM(supptrans.ovgst/supptrans.rate) AS SupplierGlTax
				FROM supptrans
					INNER JOIN suppliers ON supptrans.supplierno=suppliers.supplierid
				WHERE supptrans.trandate>='" . $PeriodFrom . "'
					AND supptrans.trandate<='" . $PeriodTo . "'
					AND supptrans.`type`=20
				GROUP BY
					supptrans.supplierno
				ORDER BY supptrans.supplierno, supptrans.trandate";
		$Result = DB_query($Sql);
		echo '<tbody>';
		foreach($Result as $MyRow) {
			if($k == 1) {
				echo '<tr class="OddTableRows">';
				$k = 0;
			} else {
				echo '<tr class="EvenTableRows">';
				$k = 1;
			}
			echo	'<td class="text"><a href="', $RootPath, '/SupplierInquiry.php?SupplierID=', $MyRow['supplierno'], '">', $MyRow['supplierno'], '</a></td>',
					'<td class="text">', $MyRow['suppname'], '</td>',
					'<td class="text">', $MyRow['currcode'], '</td>',
					'<td class="number">', locale_number_format($MyRow['SupplierOvAmount'], $_SESSION['CompanyRecord']['decimalplaces']), '</td>',
					'<td class="number">', locale_number_format($MyRow['SupplierOvTax'], $_SESSION['CompanyRecord']['decimalplaces']), '</td>',
					'<td class="number">', locale_number_format($MyRow['SupplierOvAmount']+$MyRow['SupplierOvTax'], $_SESSION['CompanyRecord']['decimalplaces']), '</td>',
					'<td class="number">', locale_number_format($MyRow['SupplierGlAmount'], $_SESSION['CompanyRecord']['decimalplaces']), '</td>',
					'<td class="number">', locale_number_format($MyRow['SupplierGlTax'], $_SESSION['CompanyRecord']['decimalplaces']), '</td>',
					'<td class="number">', locale_number_format($MyRow['SupplierGlAmount']+$MyRow['SupplierGlTax'], $_SESSION['CompanyRecord']['decimalplaces']), '</td>',
				'</tr>';
			$TotalGlAmount += $MyRow['SupplierGlAmount'];
			$TotalGlTax += $MyRow['SupplierGlTax'];
		}
	}
	echo '<tr>
			<td class="text" colspan="6">&nbsp;</td>
			<td class="number">', locale_number_format($TotalGlAmount, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
			<td class="number">', locale_number_format($TotalGlTax, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
			<td class="number">', locale_number_format($TotalGlAmount+$TotalGlTax, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
		</tr>
	</tbody>
</table>';// Prints all suppliers total.

	echo '<form action="', htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8'), '" method="post">
		<input name="FormID" type="hidden" value="', $_SESSION['FormID'], '" />
		<input name="PeriodFrom" type="hidden" value="', $_POST['PeriodFrom'], '" />
		<input name="PeriodTo" type="hidden" value="', $_POST['PeriodTo'], '" />
		<input name="ShowDetails" type="hidden" value="', $_POST['ShowDetails'], '" />';

	echo '<div class="centre noPrint">
			<a href="', htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'), '">', _('New Report'), '</a>
		</div>';

} else {
	// Shows a form to allow input of criteria for the report to generate:
	echo '<form action="', htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'), '" method="post">',
		'<input name="FormID" type="hidden" value="', $_SESSION['FormID'], '" />';
		// Input table:
	if(!isset($_POST['PeriodFrom'])) {
		$_POST['PeriodFrom'] = date($_SESSION['DefaultDateFormat'], strtotime("-1 year", time()));// One year before current date.
	}
	if(!isset($_POST['PeriodTo'])) {
		$_POST['PeriodTo'] = date($_SESSION['DefaultDateFormat']);
	}
	echo '<table class="selection">
			<thead>
				<tr>
					<th colspan="2">', _('Report Parameters'), '</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="2">',
						'<div class="centre">',
							'<input name="Action" type="submit" value="', _('Submit'), '" />', // "Submit" button.
						'</div>
					</td>
				</tr>
			</tfoot>';
		// Content of the body of the input table:
			// Select period from:
	if (isset($_POST['ShowDetails'])) {
		$Checked = 'checked="checked"';
	} else {
		$Checked = '';
	}
	echo '<tbody>
			<tr>
				<td><label for="PeriodFrom">', _('Select period from'), '</label></td>
				<td>
					<input autofocus="autofocus" class="date" id="PeriodFrom" maxlength="10" minlength="0" name="PeriodFrom" required="required" size="12" type="text" value="', $_POST['PeriodFrom'], '" />
					<fieldhelp>', _('Select the beginning of the reporting period'), '</fieldhelp>
		 		</td>
			</tr>
			<tr>',
				'<td><label for="PeriodTo">', _('Select period to'), '</label></td>
				<td><input class="date" id="PeriodTo" maxlength="10" minlength="0" name="PeriodTo" required="required" size="12" type="text" value="', $_POST['PeriodTo'], '" />
					<fieldhelp>', _('Select the end of the reporting period'), '</fieldhelp>
		 		</td>
			</tr>
			<tr>
				<td><label for="ShowDetails">', _('Show details'), '</label></td>
			 	<td><input ', $Checked, ' id="ShowDetails" name="ShowDetails" type="checkbox">
					<fieldhelp>', _('Check this box to show purchase invoices'), '</fieldhelp>
			 	</td>
			</tr>',
		 '</tbody>
		</table>';
echo '</form>';
}

include('includes/footer.php');
// END: Procedure division -----------------------------------------------------
?>