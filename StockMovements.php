<?php

include('includes/session.inc');
$Title = _('Stock Movements');
/* Manual links before header.inc */
$ViewTopic = 'Inventory';
$BookMark = 'InventoryMovement';
include('includes/header.inc');

if (isset($_GET['StockID'])) {
	$StockID = trim(mb_strtoupper($_GET['StockID']));
} elseif (isset($_POST['StockID'])) {
	$StockID = trim(mb_strtoupper($_POST['StockID']));
} else {
	$StockID = '';
}

$result = DB_query("SELECT description, units FROM stockmaster WHERE stockid='" . $StockID . "'");
$MyRow = DB_fetch_row($result);
echo '<p class="page_title_text noPrint" ><img src="' . $RootPath . '/css/' . $Theme . '/images/inventory.png" title="' . _('Inventory') . '" alt="" /><b>' . ' ' . $StockID . ' - ' . $MyRow['0'] . ' : ' . _('in units of') . ' : ' . $MyRow[1] . '</b></p>';

echo '<form onSubmit="return VerifyForm(this);" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '" method="post" class="noPrint">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

if (!isset($_POST['BeforeDate']) or !Is_Date($_POST['BeforeDate'])) {
	$_POST['BeforeDate'] = Date($_SESSION['DefaultDateFormat']);
}
if (!isset($_POST['AfterDate']) or !Is_Date($_POST['AfterDate'])) {
	$_POST['AfterDate'] = Date($_SESSION['DefaultDateFormat'], Mktime(0, 0, 0, Date('m') - 3, Date('d'), Date('y')));
}
echo '<table class="selection">
			<tr>
				<th colspan="10">' . _('Stock Code') . ':<input type="text" name="StockID" size="21" value="' . $StockID . '" required="required" minlength="1" maxlength="20" />';

echo '  ' . _('From Stock Location') . ':<select required="required" minlength="1" name="StockLocation"> ';

if ($_SESSION['RestrictLocations'] == 0) {
	$sql = "SELECT locationname,
					loccode
				FROM locations";
} else {
	$sql = "SELECT locationname,
					loccode
				FROM locations
				INNER JOIN www_users
					ON locations.loccode=www_users.defaultlocation
				WHERE www_users.userid='" . $_SESSION['UserID'] . "'";
}
$resultStkLocs = DB_query($sql);

while ($MyRow = DB_fetch_array($resultStkLocs)) {
	if (isset($_POST['StockLocation']) and $_POST['StockLocation'] != 'All') {
		if ($MyRow['loccode'] == $_POST['StockLocation']) {
			echo '<option selected="selected" value="' . $MyRow['loccode'] . '">' . $MyRow['locationname'] . '</option>';
		} else {
			echo '<option value="' . $MyRow['loccode'] . '">' . $MyRow['locationname'] . '</option>';
		}
	} elseif ($MyRow['loccode'] == $_SESSION['UserStockLocation']) {
		echo '<option selected="selected" value="' . $MyRow['loccode'] . '">' . $MyRow['locationname'] . '</option>';
		$_POST['StockLocation'] = $MyRow['loccode'];
	} else {
		echo '<option value="' . $MyRow['loccode'] . '">' . $MyRow['locationname'] . '</option>';
	}
}

echo '</select></th>
	</tr>';
echo '<tr>
		<th colspan="10">' . _('Show Movements between') . ':
			<input type="text" name="AfterDate" class="date" alt="' . $_SESSION['DefaultDateFormat'] . '" size="12" required="required" minlength="1" maxlength="12" value="' . $_POST['AfterDate'] . '" /> ' . _('and') . ':
			<input type="text" name="BeforeDate" class="date" alt="' . $_SESSION['DefaultDateFormat'] . '" size="12" required="required" minlength="1" maxlength="12" value="' . $_POST['BeforeDate'] . '" />
			<input type="submit" name="ShowMoves" value="' . _('Show Stock Movements') . '" />
		</th>
	</tr>';

$SQLBeforeDate = FormatDateForSQL($_POST['BeforeDate']);
$SQLAfterDate = FormatDateForSQL($_POST['AfterDate']);

$sql = "SELECT stockmoves.stockid,
				systypes.typename,
				stockmoves.type,
				stockmoves.transno,
				stockmoves.trandate,
				stockmoves.debtorno,
				stockmoves.branchcode,
				stockmoves.qty,
				stockmoves.reference,
				stockmoves.price,
				stockmoves.discountpercent,
				stockmoves.newqoh,
				stockmaster.decimalplaces
		FROM stockmoves
		INNER JOIN systypes ON stockmoves.type=systypes.typeid
		INNER JOIN stockmaster ON stockmoves.stockid=stockmaster.stockid
		WHERE  stockmoves.loccode='" . $_POST['StockLocation'] . "'
		AND stockmoves.trandate >= '" . $SQLAfterDate . "'
		AND stockmoves.stockid = '" . $StockID . "'
		AND stockmoves.trandate <= '" . $SQLBeforeDate . "'
		AND hidemovt=0
		ORDER BY stkmoveno DESC";

$ErrMsg = _('The stock movements for the selected criteria could not be retrieved because') . ' - ';
$DbgMsg = _('The SQL that failed was') . ' ';

$MovtsResult = DB_query($sql, $ErrMsg, $DbgMsg);

echo '<tr>
		<th>' . _('Type') . '</th>
		<th>' . _('Number') . '</th>
		<th>' . _('Date') . '</th>
		<th>' . _('Customer') . '</th>
		<th>' . _('Branch') . '</th>
		<th>' . _('Quantity') . '</th>
		<th>' . _('Reference') . '</th>
		<th>' . _('Cost') . '</th>
		<th>' . _('Discount') . '</th>
		<th>' . _('New Qty') . '</th>
	</tr>';

$k = 0; //row colour counter

while ($MyRow = DB_fetch_array($MovtsResult)) {

	if ($k == 1) {
		echo '<tr class="EvenTableRows">';
		$k = 0;
	} else {
		echo '<tr class="OddTableRows">';
		$k = 1;
	}

	$DisplayTranDate = ConvertSQLDate($MyRow['trandate']);

	if ($MyRow['type'] == 10) {
		/*its a sales invoice allow link to show invoice it was sold on*/

		printf('<td><a target="_blank" href="%s/PrintCustTrans.php?FromTransNo=%s&amp;InvOrCredit=Invoice">%s</a></td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td class="number">%s</td>
				<td>%s</td>
				<td class="number">%s</td>
				<td class="number">%s%%</td>
				<td class="number">%s</td>
				</tr>', $RootPath, $MyRow['transno'], $MyRow['typename'], $MyRow['transno'], $DisplayTranDate, $MyRow['debtorno'], $MyRow['branchcode'], locale_number_format($MyRow['qty'], $MyRow['decimalplaces']), $MyRow['reference'], locale_number_format($MyRow['price'], $_SESSION['CompanyRecord']['decimalplaces']), locale_number_format($MyRow['discountpercent'] * 100, 2), locale_number_format($MyRow['newqoh'], $MyRow['decimalplaces']));

	} elseif ($MyRow['type'] == 11) {

		printf('<td><a target="_blank" href="%s/PrintCustTrans.php?FromTransNo=%s&amp;InvOrCredit=Credit">%s</a></td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td class="number">%s</td>
				<td>%s</td>
				<td class="number">%s</td>
				<td class="number">%s%%</td>
				<td class="number">%s</td>
				</tr>', $RootPath, $MyRow['transno'], $MyRow['typename'], $MyRow['transno'], $DisplayTranDate, $MyRow['debtorno'], $MyRow['branchcode'], locale_number_format($MyRow['qty'], $MyRow['decimalplaces']), $MyRow['reference'], locale_number_format($MyRow['price'], $_SESSION['CompanyRecord']['decimalplaces']), locale_number_format($MyRow['discountpercent'] * 100, 2), locale_number_format($MyRow['newqoh'], $MyRow['decimalplaces']));
	} else {

		printf('<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td class="number">%s</td>
				<td>%s</td>
				<td class="number">%s</td>
				<td class="number">%s%%</td>
				<td class="number">%s</td>
				</tr>', $MyRow['typename'], $MyRow['transno'], $DisplayTranDate, $MyRow['debtorno'], $MyRow['branchcode'], locale_number_format($MyRow['qty'], $MyRow['decimalplaces']), $MyRow['reference'], locale_number_format($MyRow['price'], $_SESSION['CompanyRecord']['decimalplaces']), locale_number_format($MyRow['discountpercent'] * 100, 2), locale_number_format($MyRow['newqoh'], $MyRow['decimalplaces']));
	}
	//end of page full new headings if
}
//end of while loop

echo '</table>';
echo '<div class="centre"><a href="' . $RootPath . '/StockStatus.php?StockID=' . urlencode($StockID) . '">' . _('Show Stock Status') . '</a>';
echo '<a href="' . $RootPath . '/StockUsage.php?StockID=' . $StockID . '&amp;StockLocation=' . urlencode($_POST['StockLocation']) . '">' . _('Show Stock Usage') . '</a>';
echo '<a href="' . $RootPath . '/SelectSalesOrder.php?SelectedStockItem=' . urlencode($StockID) . '&amp;StockLocation=' . $_POST['StockLocation'] . '">' . _('Search Outstanding Sales Orders') . '</a>';
echo '<a href="' . $RootPath . '/SelectCompletedOrder.php?SelectedStockItem=' . urlencode($StockID) . '">' . _('Search Completed Sales Orders') . '</a>';

echo '</div>
	  </form>';

include('includes/footer.inc');

?>