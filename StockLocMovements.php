<?php
include ('includes/session.php');

$Title = _('All Stock Movements By Location');

include ('includes/header.php');

echo '<form action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '" method="post">';
echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';

echo '<p class="page_title_text">
		<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/magnifier.png" title="', _('Search'), '" alt="" />', ' ', $Title, '
	</p>';

echo '<fieldset>
		<legend>', _('Inquiry Criteria'), '</legend>';

$SQL = "SELECT locationname,
				locations.loccode
			FROM locations
			INNER JOIN locationusers
				ON locationusers.loccode=locations.loccode
				AND locationusers.userid='" . $_SESSION['UserID'] . "'
				AND locationusers.canview=1";
$ResultStkLocs = DB_query($SQL);
if (!isset($_POST['StockLocation'])) {
	$_POST['StockLocation'] = 'All';
}
echo '<field>
		<label for="StockLocation">', _('From Stock Location'), ':</label>
		<select required="required" name="StockLocation">
			<option selected="selected" value="All">', _('All Locations'), '</option>';
while ($MyRow = DB_fetch_array($ResultStkLocs)) {
	if (isset($_POST['StockLocation']) and $_POST['StockLocation'] != 'All') {
		if ($MyRow['loccode'] == $_POST['StockLocation']) {
			echo '<option selected="selected" value="', $MyRow['loccode'], '">', $MyRow['locationname'], '</option>';
		} else {
			echo '<option value="', $MyRow['loccode'], '">', $MyRow['locationname'], '</option>';
		}
	} else {
		echo '<option value="', $MyRow['loccode'], '">', $MyRow['locationname'], '</option>';
	}
}
echo '</select>
	</field>';

if (!isset($_POST['BeforeDate']) or !is_date($_POST['BeforeDate'])) {
	$_POST['BeforeDate'] = Date($_SESSION['DefaultDateFormat']);
}
if (!isset($_POST['AfterDate']) or !is_date($_POST['AfterDate'])) {
	$_POST['AfterDate'] = Date($_SESSION['DefaultDateFormat'], Mktime(0, 0, 0, Date('m') - 1, Date('d'), Date('y')));
}
echo '<field>
		<label for="BeforeDate">', _('Show Movements before'), ':</label>
		<input type="text" class="date" name="BeforeDate" size="12" required="required" maxlength="12" value="', $_POST['BeforeDate'], '" />
	</field>';

echo '<field>
		<label for="AfterDate">', _('But after'), ':</label>
		<input type="text" class="date" name="AfterDate" size="12" required="required" maxlength="12" value="', $_POST['AfterDate'], '" />
	</field>';

echo '</fieldset>';

echo '<div class="centre">
		   <input type="submit" name="ShowMoves" value="', _('Show Stock Movements'), '" />
	 </div>';

if ($_POST['StockLocation'] == 'All') {
	$_POST['StockLocation'] = '%%';
}

$SQLBeforeDate = FormatDateForSQL($_POST['BeforeDate']);
$SQLAfterDate = FormatDateForSQL($_POST['AfterDate']);

$SQL = "SELECT stockmoves.stockid,
				stockmoves.stkmoveno,
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
        		stockmaster.controlled,
        		stockmaster.serialised,
        		stockmaster.decimalplaces
			FROM stockmoves
			INNER JOIN systypes ON stockmoves.type=systypes.typeid
			INNER JOIN stockmaster ON stockmoves.stockid=stockmaster.stockid
			WHERE  stockmoves.loccode " . LIKE . " '" . $_POST['StockLocation'] . "'
			AND stockmoves.trandate >= '" . $SQLAfterDate . "'
			AND stockmoves.trandate <= '" . $SQLBeforeDate . "'
			AND hidemovt=0
			ORDER BY stkmoveno DESC";
$ErrMsg = _('The stock movements for the selected criteria could not be retrieved because');
$MovtsResult = DB_query($SQL, $ErrMsg);

if (DB_num_rows($MovtsResult) > 0) {
	echo '<table cellpadding="5" cellspacing="4 "class="selection">
			<thead>
				<tr>
					<th class="SortedColumn">', _('Item Code'), '</th>
					<th class="SortedColumn">', _('Type'), '</th>
					<th class="SortedColumn">', _('Trans No'), '</th>
					<th class="SortedColumn">', _('Date'), '</th>
					<th class="SortedColumn">', _('Customer'), '</th>
					<th>', _('Quantity'), '</th>
					<th>', _('Reference'), '</th>
					<th>', _('Price'), '</th>
					<th>', _('Discount'), '</th>
					<th>', _('Quantity on Hand'), '</th>
					<th>', _('Serial No.'), '</th>
				</tr>
			</thead>
			<tbody>';

	while ($MyRow = DB_fetch_array($MovtsResult)) {

		$DisplayTranDate = ConvertSQLDate($MyRow['trandate']);

		$SerialSQL = "SELECT serialno, moveqty FROM stockserialmoves WHERE stockmoveno='" . $MyRow['stkmoveno'] . "'";
		$SerialResult = DB_query($SerialSQL);

		$SerialText = '';
		while ($SerialRow = DB_fetch_array($SerialResult)) {
			if ($MyRow['serialised'] == 1) {
				$SerialText.= $SerialRow['serialno'] . '<br />';
			} else {
				$SerialText.= $SerialRow['serialno'] . ' Qty- ' . $SerialRow['moveqty'] . '<br />';
			}
		}
		echo '<tr class="striped_row">
				<td><a target="_blank" href="', $RootPath, '/StockStatus.php?StockID=', mb_strtoupper(urlencode($MyRow['stockid'])), '">', mb_strtoupper($MyRow['stockid']), '</a></td>
				<td>', $MyRow['typename'], '</td>
				<td>', $MyRow['transno'], '</td>
				<td class="date">', $DisplayTranDate, '</td>
				<td>', $MyRow['debtorno'], '</td>
				<td class="number">', locale_number_format($MyRow['qty'], $MyRow['decimalplaces']), '</td>
				<td>', $MyRow['reference'], '</td>
				<td class="number">', locale_number_format($MyRow['price'], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
				<td class="number">', locale_number_format($MyRow['discountpercent'] * 100, 2), '%</td>
				<td class="number">', locale_number_format($MyRow['newqoh'], $MyRow['decimalplaces']), '</td>
				<td>', $SerialText, '</td>
			</tr>';
	}
	//end of while loop
	
}
echo '</tbody>
	</table>';
echo '</form>';

include ('includes/footer.php');

?>