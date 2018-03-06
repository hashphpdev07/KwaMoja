<?php
include ('includes/session.php');

$Title = _('Stock Usage');

if (isset($_GET['StockID'])) {
	$StockId = trim(mb_strtoupper($_GET['StockID']));
} elseif (isset($_POST['StockID'])) {
	$StockId = trim(mb_strtoupper($_POST['StockID']));
} else {
	$StockId = '';
}

if (isset($_POST['ShowGraphUsage'])) {
	echo '<meta http-equiv="Refresh" content="0; url=', $RootPath, '/StockUsageGraph.php?StockLocation=', $_POST['StockLocation'], '&amp;StockID=', $StockId . '">';
	prnMsg(_('You should automatically be forwarded to the usage graph') . '. ' . _('If this does not happen') . ' (' . _('if the browser does not support META Refresh') . ') ' . '<a href="' . $RootPath . '/StockUsageGraph.php?StockLocation=' . urlencode($_POST['StockLocation']) . '&amp;StockID=' . urlencode($StockId) . '">' . _('click here') . '</a> ' . _('to continue'), 'info');
	exit;
}

include ('includes/header.php');

echo '<p class="page_title_text" >
		<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/magnifier.png" title="', _('Dispatch'), '" alt="" />', ' ', $Title, '
	</p>';

$Result = DB_query("SELECT description,
						units,
						mbflag,
						decimalplaces
					FROM stockmaster
					WHERE stockid='" . $StockId . "'");
$MyRow = DB_fetch_array($Result);

$DecimalPlaces = $MyRow['decimalplaces'];

echo '<form action="', htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'), '" method="post">';
echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';
echo '<table>';

$ItsKitSetAssemblyOrDummy = False;
if ($MyRow['mbflag'] == 'K' or $MyRow['mbflag'] == 'A' or $MyRow['mbflag'] == 'D') {

	$ItsKitSetAssemblyOrDummy = True;
	echo '<h3>' . $StockId . ' - ' . $MyRow['description'] . '</h3>';

	prnMsg(_('The selected item is a dummy or assembly or kit-set item and cannot have a stock holding') . '. ' . _('Please select a different item'), 'warn');

	$StockId = '';
} else {
	echo '<tr>
			<th><h3>', _('Item'), ' : ', $StockId, ' - ', $MyRow['description'], '   (', _('in units of'), ' : ', $MyRow['units'], ')</h3></th>
		</tr>';
}

echo '<tr>
		<td>', _('Stock Code'), ':<input type="text" name="StockID" size="21" required="required" maxlength="20" value="', $StockId, '" />';

echo _('From Stock Location'), ':<select required="required" name="StockLocation">';

$SQL = "SELECT locationname,
				locations.loccode
			FROM locations
			INNER JOIN locationusers
				ON locationusers.loccode=locations.loccode
				AND locationusers.userid='" . $_SESSION['UserID'] . "'
				AND locationusers.canview=1";
echo '<option selected="selected" value="All">', _('All Locations'), '</option>';
$ResultStkLocs = DB_query($SQL);
while ($MyRow = DB_fetch_array($ResultStkLocs)) {
	if (isset($_POST['StockLocation'])) {
		if ($MyRow['loccode'] == $_POST['StockLocation']) {
			echo '<option selected="selected" value="', $MyRow['loccode'], '">', $MyRow['locationname'], '</option>';
		} else {
			echo '<option value="', $MyRow['loccode'], '">', $MyRow['locationname'], '</option>';
		}
	} else {
		echo '<option value="', $MyRow['loccode'], '">', $MyRow['locationname'], '</option>';
		$_POST['StockLocation'] = 'All';
	}
}
echo '</select>';

echo ' <input type="submit" name="ShowUsage" value="', _('Show Stock Usage'), '" />';
echo ' <input type="submit" name="ShowGraphUsage" value="', _('Show Graph Of Stock Usage'), '" /></td>
		</tr>
		</table>';

/*HideMovt ==1 if the movement was only created for the purpose of a transaction but is not a physical movement eg. A price credit will create a movement record for the purposes of display on a credit note
 but there is no physical stock movement - it makes sense honest ??? */

$CurrentPeriod = GetPeriod(Date($_SESSION['DefaultDateFormat']));

if (isset($_POST['ShowUsage'])) {
	if ($_POST['StockLocation'] == 'All') {
		$SQL = "SELECT periods.periodno,
				periods.lastdate_in_period,
				canview,
				SUM(CASE WHEN (stockmoves.type=10 OR stockmoves.type=11 OR stockmoves.type=17 OR stockmoves.type=28 OR stockmoves.type=38)
							AND stockmoves.hidemovt=0
							AND stockmoves.stockid = '" . $StockId . "'
						THEN -stockmoves.qty ELSE 0 END) AS qtyused
				FROM periods
				LEFT JOIN stockmoves
					ON periods.periodno=stockmoves.prd
				INNER JOIN locationusers
					ON locationusers.loccode=stockmoves.loccode
					AND locationusers.userid='" . $_SESSION['UserID'] . "'
					AND locationusers.canview=1
				WHERE periods.periodno <='" . $CurrentPeriod . "'
				GROUP BY periods.periodno,
					periods.lastdate_in_period
				ORDER BY periodno DESC LIMIT " . $_SESSION['NumberOfPeriodsOfStockUsage'];
	} else {
		$SQL = "SELECT periods.periodno,
				periods.lastdate_in_period,
				SUM(CASE WHEN (stockmoves.type=10 OR stockmoves.type=11 OR stockmoves.type=17 OR stockmoves.type=28 OR stockmoves.type=38)
								AND stockmoves.hidemovt=0
								AND stockmoves.stockid = '" . $StockId . "'
								AND stockmoves.loccode='" . $_POST['StockLocation'] . "'
							THEN -stockmoves.qty ELSE 0 END) AS qtyused
				FROM periods LEFT JOIN stockmoves
					ON periods.periodno=stockmoves.prd
				WHERE periods.periodno <='" . $CurrentPeriod . "'
				GROUP BY periods.periodno,
					periods.lastdate_in_period
				ORDER BY periodno DESC LIMIT " . $_SESSION['NumberOfPeriodsOfStockUsage'];

	}
	$MovtsResult = DB_query($SQL);
	if (DB_error_no() != 0) {
		echo _('The stock usage for the selected criteria could not be retrieved because'), ' - ', DB_error_msg();
		if ($Debug == 1) {
			echo '<br />', _('The SQL that failed was'), $SQL;
		}
		exit;
	}

	echo '<table>
			<thead>
				<tr>
					<th class="SortedColumn">', _('Month'), '</th>
					<th>', _('Usage'), '</th>
				</tr>
			</thead>';

	$k = 0; //row colour counter
	$TotalUsage = 0;
	$PeriodsCounter = 0;
	echo '<tbody>';
	while ($MyRow = DB_fetch_array($MovtsResult)) {

		$DisplayDate = MonthAndYearFromSQLDate($MyRow['lastdate_in_period']);

		$TotalUsage+= $MyRow['qtyused'];
		$PeriodsCounter++;
		echo '<tr class="striped_row">
				<td>', $DisplayDate, '</td>
				<td class="number">', locale_number_format($MyRow['qtyused'], $DecimalPlaces), '</td>
			</tr>';

		//end of page full new headings if
		
	}
	//end of while loop
	if ($TotalUsage > 0 and $PeriodsCounter > 0) {
		echo '</tbody>';
		echo '<tfoot>
				<tr>
					<th colspan="2">', _('Average Usage per month is'), ' ', locale_number_format($TotalUsage / $PeriodsCounter), '</th>
				</tr>
			</tfoot>';
	}
	echo '</table>';
}
/* end if Show Usage is clicked */

echo '<div class="centre">
		<a href="', $RootPath, '/StockStatus.php?StockID=', urlencode($StockId), '">', _('Show Stock Status'), '</a>
		<a href="', $RootPath, '/StockMovements.php?StockID=', urlencode($StockId), '&amp;StockLocation=', urlencode($_POST['StockLocation']), '">', _('Show Stock Movements'), '</a>
		<a href="', $RootPath, '/SelectSalesOrder.php?SelectedStockItem=', urlencode($StockId), '&amp;StockLocation=', urlencode($_POST['StockLocation']), '">', _('Search Outstanding Sales Orders'), '</a>
		<a href="', $RootPath, '/SelectCompletedOrder.php?SelectedStockItem=', urlencode($StockId), '">', _('Search Completed Sales Orders'), '</a>
		<a href="', $RootPath, '/PO_SelectOSPurchOrder.php?SelectedStockItem=', urlencode($StockId), '">', _('Search Outstanding Purchase Orders'), '</a>
	</div>';

echo '</form>';
include ('includes/footer.php');

?>