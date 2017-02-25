<?php

include('includes/session.php');
$Title = _('Petty Cash Expense Management Report');
$ViewTopic = 'PettyCash';
$BookMark = 'PcReportExpense';

if (isset($_GET['download'])) {
	$SQL = "SELECT type,
					size,
					content
				FROM pcreceipts
				WHERE pccashdetail='" . $_GET['receipt'] . "'
					AND name='" . $_GET['name'] . "'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);
	header('Content-type: ' . $MyRow['type'] . "\n");
	header('Content-Disposition: attachment; filename=' . $_GET['name'] . "\n");
	header('Content-Length: ' . $MyRow['size'] . "\n");
	echo $MyRow['content'];
	exit;
}

include('includes/SQL_CommonFunctions.php');
include('includes/header.php');

echo '<p class="page_title_text">
		<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/money_add.png" title="', _('PC Expense Report'), '" alt="" />', ' ', $Title, '
	</p>';

echo '<form method="post" action="', htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'), '">';
echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';

if (isset($_POST['SelectedExpense'])) {
	$SelectedExpense = mb_strtoupper($_POST['SelectedExpense']);
} elseif (isset($_GET['SelectedExpense'])) {
	$SelectedExpense = mb_strtoupper($_GET['SelectedExpense']);
}

if ((!isset($_POST['FromDate']) and !isset($_POST['ToDate'])) or isset($_POST['SelectDifferentDate'])) {

	if (!isset($_POST['FromDate'])) {
		$_POST['FromDate'] = Date($_SESSION['DefaultDateFormat'], mktime(0, 0, 0, Date('m'), 1, Date('Y')));
	}

	if (!isset($_POST['ToDate'])) {
		$_POST['ToDate'] = Date($_SESSION['DefaultDateFormat']);
	}

	/*Show a form to allow input of criteria for Expenses to show */
	echo '<table class="selection">
			<tr>
				<td>', _('Code Of Petty Cash Expense'), ':</td>
				<td><select name="SelectedExpense">';

	$SQL = "SELECT DISTINCT(pctabexpenses.codeexpense)
					FROM pctabs
					INNER JOIN pctabexpenses
						ON pctabexpenses.typetabcode = pctabs.typetabcode
					WHERE ( pctabs.authorizer='" . $_SESSION['UserID'] . "' OR pctabs.usercode ='" . $_SESSION['UserID'] . "' OR pctabs.assigner ='" . $_SESSION['UserID'] . "' )
					ORDER BY pctabexpenses.codeexpense";

	$Result = DB_query($SQL);

	while ($MyRow = DB_fetch_array($Result)) {
		if (isset($_POST['SelectedExpense']) and $MyRow['codeexpense'] == $_POST['SelectedExpense']) {
			echo '<option selected="selected" value="', $MyRow['codeexpense'], '">', $MyRow['codeexpense'], '</option>';
		} else {
			echo '<option value="', $MyRow['codeexpense'], '">', $MyRow['codeexpense'], '</option>';
		}

	} //end while loop get type of tab

	DB_free_result($Result);


	echo '</select>
			</td>
		</tr>
		<tr>
			<td>', _('From Date'), ':', '</td>
			<td><input tabindex="2" class="date" alt="', $_SESSION['DefaultDateFormat'], '" type="text" name="FromDate" maxlength="10" size="11" value="', $_POST['FromDate'], '" /></td>
		</tr>
		<tr>
			<td>', _('To Date'), ':', '</td>
			<td><input tabindex="3" class="date" alt="', $_SESSION['DefaultDateFormat'], '" type="text" name="ToDate" maxlength="10" size="11" value="', $_POST['ToDate'], '" /></td>
		</tr>
	</table>
	<div class="centre">
		<input type="submit" name="ShowTB" value="', _('Show HTML'), '" />
	</div>
</form>';

} else {

	$SQL_FromDate = FormatDateForSQL($_POST['FromDate']);
	$SQL_ToDate = FormatDateForSQL($_POST['ToDate']);

	echo '<input type="hidden" name="FromDate" value="', $_POST['FromDate'], '" />';
	echo '<input type="hidden" name="ToDate" value="', $_POST['ToDate'], '" />';

	echo '<table class="selection">
			<tr>
				<td>', _('Expense Code'), ':</td>
				<td style="width:200px">', $SelectedExpense, '</td>
				<td>', _('From'), ':</td>
				<td>', $_POST['FromDate'], '</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td>', _('To'), ':</td>
				<td>', $_POST['ToDate'], '</td>
			</tr>
		</table>';

	$SQL = "SELECT pcashdetails.counterindex,
					pcashdetails.date,
					pcashdetails.tabcode,
					pcashdetails.amount,
					pcashdetails.notes,
					pcashdetails.receipt,
					pcashdetails.authorized,
					pctabs.currency,
					currencies.decimalplaces
			FROM pcashdetails
			INNER JOIN pctabs
				ON pcashdetails.tabcode = pctabs.tabcode
			INNER JOIN currencies
				ON pctabs.currency = currencies.currabrev
			WHERE codeexpense='" . $SelectedExpense . "'
				AND date >='" . $SQL_FromDate . "'
				AND date <= '" . $SQL_ToDate . "'
				AND (pctabs.authorizer='" . $_SESSION['UserID'] . "' OR pctabs.usercode ='" . $_SESSION['UserID'] . "' OR pctabs.assigner ='" . $_SESSION['UserID'] . "')
			ORDER BY date,
					counterindex ASC";

	$TabDetail = DB_query($SQL, _('No Petty Cash movements for this expense code were returned by the SQL because'), _('The SQL that failed was:'));

	echo '<table class="selection">
			<tr>
				<th>', _('Date'), '</th>
				<th>', _('Tab'), '</th>
				<th>', _('Amount'), '</th>
				<th>', _('Currency'), '</th>
				<th>', _('Notes'), '</th>
				<th>', _('Receipt'), '</th>
				<th>', _('Authorised'), '</th>
			</tr>';

	$k = 0; //row colour counter

	while ($MyRow = DB_fetch_array($TabDetail)) {
		if ($k == 1) {
			echo '<tr class="EvenTableRows">';
			$k = 0;
		} else {
			echo '<tr class="OddTableRows">';
			$k = 1;
		}
		$ReceiptSQL = "SELECT name
							FROM pcreceipts
							WHERE pccashdetail='" . $MyRow['counterindex'] . "'";
		$ReceiptResult = DB_query($ReceiptSQL);
		if (DB_num_rows($ReceiptResult) > 0) {
			$ReceiptRow = DB_fetch_array($ReceiptResult);
			$ReceiptText = '<a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?download=yes&receipt=' . urlencode($MyRow['counterindex']) . '&name=' . urlencode($ReceiptRow['name']) . '">' . _('View receipt') . '</a>';
		} else {
			$ReceiptText = _('No receipt');
		}

		echo '<td>', ConvertSQLDate($MyRow['date']), '</td>
				<td>', $MyRow['tabcode'], '</td>
				<td class="number">', locale_number_format($MyRow['amount'], $MyRow['decimalplaces']), '</td>
				<td>', $MyRow['currency'], '</td>
				<td>', $MyRow['notes'], '</td>
				<td>', $ReceiptText, '</td>
				<td>', ConvertSQLDate($MyRow['authorized']), '</td>
			</tr>';
	}

	echo '</table>';
	echo '<div class="centre">
			<input type="submit" name="SelectDifferentDate" value="' . _('Select A Different Date') . '" />
		</div>';
	echo '</form>';
}
include('includes/footer.php');

?>