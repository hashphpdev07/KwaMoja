<?php
/*Through deviousness and cunning, this system allows trial balances for
 * any date range that recalcuates the p & l balances and shows the balance
 * sheets as at the end of the period selected - so first off need to show
 * the input of criteria screen while the user is selecting the criteria
 * the system is posting any unposted transactions
*/
$PageSecurity = 1;
include ('includes/session.php');
$Title = _('Trial Balance');
include ('includes/SQL_CommonFunctions.php');
include ('includes/AccountSectionsDef.php'); //this reads in the Accounts Sections array
// Merges gets into posts:
if (isset($_GET['PeriodFrom'])) {
	$_POST['PeriodFrom'] = $_GET['PeriodFrom'];
}
if (isset($_GET['PeriodTo'])) {
	$_POST['PeriodTo'] = $_GET['PeriodTo'];
}
if (isset($_GET['Period'])) {
	$_POST['Period'] = $_GET['Period'];
}

if (isset($_POST['PeriodFrom']) and isset($_POST['PeriodTo']) and $_POST['PeriodFrom'] > $_POST['PeriodTo']) {

	prnMsg(_('The selected period from is actually after the period to! Please re-select the reporting period'), 'error');
	$_POST['NewReport'] = _('Select A Different Period');
}

if ((!isset($_POST['PeriodFrom']) and !isset($_POST['PeriodTo'])) or isset($_POST['NewReport'])) {

	$ViewTopic = 'GeneralLedger';
	$BookMark = 'TrialBalance';
	include ('includes/header.php');
	echo '<p class="page_title_text" >
			<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/magnifier.png" title="', _('Trial Balance'), '" alt="', _('Trial Balance'), '" />', ' ', $Title, '
		</p>';
	echo '<form method="post" action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '">';
	echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';

	if (Date('m') > $_SESSION['YearEnd']) {
		/*Dates in SQL format */
		$DefaultFromDate = Date('Y-m-d', Mktime(0, 0, 0, $_SESSION['YearEnd'] + 2, 0, Date('Y')));
		$FromDate = Date($_SESSION['DefaultDateFormat'], Mktime(0, 0, 0, $_SESSION['YearEnd'] + 2, 0, Date('Y')));
	} else {
		$DefaultFromDate = Date('Y-m-d', Mktime(0, 0, 0, $_SESSION['YearEnd'] + 2, 0, Date('Y') - 1));
		$FromDate = Date($_SESSION['DefaultDateFormat'], Mktime(0, 0, 0, $_SESSION['YearEnd'] + 2, 0, Date('Y') - 1));
	}
	/*GetPeriod function creates periods if need be the return value is not used */
	$NotUsedPeriodNo = GetPeriod($FromDate);

	/*Show a form to allow input of criteria for TB to show */
	echo '<fieldset>
			<legend>', _('Input criteria for inquiry'), '</legend>
			<field>
				<label for="PeriodFrom">', _('Select Period From'), ':</label>
				<select name="PeriodFrom" autofocus="autofocus">';
	$NextYear = date('Y-m-d', strtotime('+1 Year'));
	$SQL = "SELECT periodno,
					lastdate_in_period
				FROM periods
				WHERE lastdate_in_period < '" . $NextYear . "'
				ORDER BY periodno DESC";
	$Periods = DB_query($SQL);

	while ($MyRow = DB_fetch_array($Periods)) {
		if (isset($_POST['PeriodFrom']) and $_POST['PeriodFrom'] != '') {
			if ($_POST['PeriodFrom'] == $MyRow['periodno']) {
				echo '<option selected="selected" value="', $MyRow['periodno'], '">', MonthAndYearFromSQLDate($MyRow['lastdate_in_period']), '</option>';
			} else {
				echo '<option value="', $MyRow['periodno'], '">', MonthAndYearFromSQLDate($MyRow['lastdate_in_period']), '</option>';
			}
		} else {
			if ($MyRow['lastdate_in_period'] == $DefaultFromDate) {
				echo '<option selected="selected" value="', $MyRow['periodno'], '">', MonthAndYearFromSQLDate($MyRow['lastdate_in_period']), '</option>';
			} else {
				echo '<option value="', $MyRow['periodno'], '">', MonthAndYearFromSQLDate($MyRow['lastdate_in_period']), '</option>';
			}
		}
	}
	echo '</select>
		<fieldhelp>', _('Select the starting period for this report'), '</fieldhelp>
	</field>';

	if (!isset($_POST['PeriodTo']) or $_POST['PeriodTo'] == '') {
		$DefaultPeriodTo = GetPeriod(date($_SESSION['DefaultDateFormat'], mktime(0, 0, 0, Date('m') + 1, 0, Date('Y'))));
	} else {
		$DefaultPeriodTo = $_POST['PeriodTo'];
	}

	echo '<field>
			<label for="PeriodTo">', _('Select Period To'), ':</label>
			<select name="PeriodTo">';

	$RetResult = DB_data_seek($Periods, 0);

	while ($MyRow = DB_fetch_array($Periods)) {

		if ($MyRow['periodno'] == $DefaultPeriodTo) {
			echo '<option selected="selected" value="' . $MyRow['periodno'] . '">' . MonthAndYearFromSQLDate($MyRow['lastdate_in_period']) . '</option>';
		} else {
			echo '<option value ="' . $MyRow['periodno'] . '">' . MonthAndYearFromSQLDate($MyRow['lastdate_in_period']) . '</option>';
		}
	}
	echo '</select>
		<fieldhelp>', _('Select the end period for this report'), '</fieldhelp>
	</field>';

	echo '<h3>', _('OR'), '</h3>';

	if (!isset($_POST['Period'])) {
		$_POST['Period'] = '';
	}

	echo '<field>
			<label for="Period">', _('Select Period'), ':</label>
			', ReportPeriodList($_POST['Period'], array('l', 't')), '
			<fieldhelp>', _('Select a predefined period from this list. If a selection is made here it will override anything selected in the From and To options above.'), '</fieldhelp>
		</field>';

	$SQL = "SELECT `id`,
					`name`,
					`current`
				FROM glbudgetheaders";
	$Result = DB_query($SQL);
	echo '<field>
			<label for="SelectedBudget">', _('Budget To Show Comparisons With'), '</label>
			<select name="SelectedBudget">';
	while ($MyRow = DB_fetch_array($Result)) {
		if (!isset($_POST['SelectedBudget']) and $MyRow['current'] == 1) {
			$_POST['SelectedBudget'] = $MyRow['id'];
		}
		if ($MyRow['id'] == $_POST['SelectedBudget']) {
			echo '<option selected="selected" value="', $MyRow['id'], '">', $MyRow['name'], '</option>';
		} else {
			echo '<option value="', $MyRow['id'], '">', $MyRow['name'], '</option>';
		}
	}
	echo '<fieldhelp>', _('Select the budget to make comparisons with.'), '</fieldhelp>
		</select>
	</field>';

	echo '</fieldset>';

	echo '<div class="centre">
			<input type="submit" name="ShowTB" value="' . _('Show Trial Balance') . '" />
			<input type="submit" name="PrintPDF" value="' . _('Print PDF') . '" />
			<input type="submit" name="ExportCSV" value="' . _('Export to Spreadsheet') . '" />
		</div>';

	echo '</form>';
	include ('includes/footer.php');

} else if (isset($_POST['ShowTB'])) {

	$ViewTopic = 'GeneralLedger';
	$BookMark = 'TrialBalance';
	include ('includes/header.php');

	echo '<form method="post" action="' . htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8') . '">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<input type="hidden" name="PeriodFrom" value="' . $_POST['PeriodFrom'] . '" />';
	echo '<input type="hidden" name="PeriodTo" value="' . $_POST['PeriodTo'] . '" />';

	if ($_POST['Period'] != '') {
		$_POST['PeriodFrom'] = ReportPeriod($_POST['Period'], 'From');
		$_POST['PeriodTo'] = ReportPeriod($_POST['Period'], 'To');
	}

	$NumberOfMonths = $_POST['PeriodTo'] - $_POST['PeriodFrom'] + 1;

	$SQL = "SELECT lastdate_in_period
			FROM periods
			WHERE periodno='" . $_POST['PeriodTo'] . "'";
	$PrdResult = DB_query($SQL);
	$MyRow = DB_fetch_row($PrdResult);
	$PeriodToDate = MonthAndYearFromSQLDate($MyRow[0]);

	$RetainedEarningsAct = $_SESSION['CompanyRecord']['retainedearnings'];

	echo '<p class="page_title_text">
			<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/gl.png" title="', _('Trial Balance'), '" alt="', _('Print'), '" />', ' ', _('Trial Balance Report'), '
		</p>';

	echo '<table cellpadding="2" summary="', _('Trial Balance Report'), '">';
	echo '<thead>
			<tr>
				<th colspan="6">
					<b>', _('Trial Balance for the month of '), $PeriodToDate, _(' and for the '), $NumberOfMonths, _(' months to '), $PeriodToDate, '</b>
					<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/printer.png" class="PrintIcon" title="', _('Print'), '" alt="', _('Print'), '" onclick="window.print();" />
				</th>
			</tr>
			<tr>
				<th>', _('Account'), '</th>
				<th>', _('Account Name'), '</th>
				<th>', _('Month Actual'), '</th>
				<th>', _('Month Budget'), '</th>
				<th>', _('Period Actual'), '</th>
				<th>', _('Period Budget'), '</th>
			</tr>
		</thead>';

	/* Firstly get the account totals for this period */
	$ThisMonthSQL = "SELECT account,
							SUM(amount) AS monthtotal
						FROM gltotals
						WHERE period='" . $_POST['PeriodTo'] . "'
						GROUP BY account";
	$ThisMonthResult = DB_query($ThisMonthSQL);
	$ThisMonthArray = array();

	while ($ThisMonthRow = DB_fetch_array($ThisMonthResult)) {
		$ThisMonthArray[$ThisMonthRow['account']] = $ThisMonthRow['monthtotal'];
	}

	/* Then get this periods cumulative P&L accounts */
	$ThisPeriodPLSQL = "SELECT account,
								SUM(amount) AS periodtotal
						FROM gltotals
						INNER JOIN chartmaster
							ON gltotals.account=chartmaster.accountcode
						INNER JOIN accountgroups
							ON chartmaster.groupcode=accountgroups.groupcode
							AND accountgroups.language=chartmaster.language
						WHERE period<='" . $_POST['PeriodTo'] . "'
							AND period>='" . $_POST['PeriodFrom'] . "'
							AND pandl=1
							AND chartmaster.language='" . $_SESSION['ChartLanguage'] . "'
						GROUP BY account";
	$ThisPeriodPLResult = DB_query($ThisPeriodPLSQL);
	$ThisPeriodArray = array();

	while ($ThisPeriodPLRow = DB_fetch_array($ThisPeriodPLResult)) {
		$ThisPeriodArray[$ThisPeriodPLRow['account']] = $ThisPeriodPLRow['periodtotal'];
	}

	/* Then get this periods cumulative BS accounts */
	$ThisPeriodBSSQL = "SELECT account,
								SUM(amount) AS periodtotal
						FROM gltotals
						INNER JOIN chartmaster
							ON gltotals.account=chartmaster.accountcode
						INNER JOIN accountgroups
							ON chartmaster.groupcode=accountgroups.groupcode
							AND accountgroups.language=chartmaster.language
						WHERE period<='" . $_POST['PeriodTo'] . "'
							AND pandl=0
							AND chartmaster.language='" . $_SESSION['ChartLanguage'] . "'
						GROUP BY account";
	$ThisPeriodBSResult = DB_query($ThisPeriodBSSQL);

	while ($ThisPeriodBSRow = DB_fetch_array($ThisPeriodBSResult)) {
		$ThisPeriodArray[$ThisPeriodBSRow['account']] = $ThisPeriodBSRow['periodtotal'];
	}

	/* Get the retained earnings amount */
	$RetainedEarningsSQL = "SELECT SUM(amount) AS retainedearnings
							FROM gltotals
							INNER JOIN chartmaster
								ON gltotals.account=chartmaster.accountcode
							INNER JOIN accountgroups
								ON chartmaster.groupcode=accountgroups.groupcode
								AND accountgroups.language=chartmaster.language
							WHERE period<'" . $_POST['PeriodFrom'] . "'
								AND chartmaster.language='" . $_SESSION['ChartLanguage'] . "'
								AND pandl=1";
	$RetainedEarningsResult = DB_query($RetainedEarningsSQL);
	$RetainedEarningsRow = DB_fetch_array($RetainedEarningsResult);

	// Get all account codes
	$SQL = "SELECT chartmaster.accountcode,
					chartmaster.groupcode,
					group_,
					chartmaster.language,
					accountname,
					pandl
			FROM chartmaster
			INNER JOIN glaccountusers
				ON glaccountusers.accountcode=chartmaster.accountcode
				AND glaccountusers.userid='" . $_SESSION['UserID'] . "'
				AND glaccountusers.canview=1
			INNER JOIN accountgroups
				ON accountgroups.groupcode=chartmaster.groupcode
				AND accountgroups.language=chartmaster.language
			WHERE chartmaster.language='" . $_SESSION['ChartLanguage'] . "'
			ORDER BY groupcode,
					accountcode";
	$AccountListResult = DB_query($SQL);
	$AccountListRow = DB_fetch_array($AccountListResult);

	echo '<tr><td></td></tr>';
	echo '<tr class="total_row">
			<td>', $AccountListRow['group_'], '</td>
			<td colspan="6"></td>
		</tr>';

	$LastGroup = $AccountListRow['groupcode'];
	$LastGroupName = $AccountListRow['group_'];

	$SQL = "SELECT amount AS monthbudget
			FROM glbudgetdetails
			WHERE account='" . $AccountListRow['accountcode'] . "'
				AND period='" . $_POST['PeriodTo'] . "'
				AND headerid='" . $_POST['SelectedBudget'] . "'";
	$MonthBudgetResult = DB_query($SQL);
	$MonthBudgetRow = DB_fetch_array($MonthBudgetResult);
	if (!isset($MonthBudgetRow['monthbudget'])) {
		$MonthBudgetRow['monthbudget'] = 0;
	}

	$SQL = "SELECT SUM(amount) AS periodbudget
			FROM glbudgetdetails
			WHERE account='" . $AccountListRow['accountcode'] . "'
				AND period>='" . $_POST['PeriodFrom'] . "'
				AND period<='" . $_POST['PeriodTo'] . "'
				AND headerid='" . $_POST['SelectedBudget'] . "'";
	$PeriodBudgetResult = DB_query($SQL);
	$PeriodBudgetRow = DB_fetch_array($PeriodBudgetResult);
	if (!isset($PeriodBudgetRow['periodbudget'])) {
		$PeriodBudgetRow['periodbudget'] = 0;
	}

	if (!isset($ThisMonthArray[$AccountListRow['accountcode']])) {
		$ThisMonthArray[$AccountListRow['accountcode']] = 0;
	}
	if (!isset($ThisPeriodArray[$AccountListRow['accountcode']])) {
		$ThisPeriodArray[$AccountListRow['accountcode']] = 0;
	}

	echo '<tr class="striped_row">
			<td><a href="', $RootPath, '/GLAccountInquiry.php?PeriodFrom=', $_POST['PeriodFrom'], '&amp;PeriodTo=', $_POST['PeriodTo'], '&amp;Account=', $AccountListRow['accountcode'], '&amp;Show=Yes">', $AccountListRow['accountcode'], '</a></td>
			<td>', $AccountListRow['accountname'], '</td>
			<td class="number">', locale_number_format($ThisMonthArray[$AccountListRow['accountcode']], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
			<td class="number">', locale_number_format($MonthBudgetRow['monthbudget'], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
			<td class="number">', locale_number_format($ThisPeriodArray[$AccountListRow['accountcode']], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
			<td class="number">', locale_number_format($PeriodBudgetRow['periodbudget'], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
		</tr>';

	$MonthActualGroupTotal = $ThisMonthArray[$AccountListRow['accountcode']];
	$MonthBudgetGroupTotal = $MonthBudgetRow['monthbudget'];
	$PeriodActualGroupTotal = $ThisPeriodArray[$AccountListRow['accountcode']];
	$PeriodBudgetGroupTotal = $PeriodBudgetRow['periodbudget'];

	$CumulativeMonthActualGroupTotal = 0;
	$CumulativePeriodActualGroupTotal = 0;

	while ($AccountListRow = DB_fetch_array($AccountListResult)) {
		if (!isset($ThisMonthArray[$AccountListRow['accountcode']])) {
			$ThisMonthArray[$AccountListRow['accountcode']] = 0;
		}
		if (!isset($ThisPeriodArray[$AccountListRow['accountcode']])) {
			$ThisPeriodArray[$AccountListRow['accountcode']] = 0;
		}
		if ($_SESSION['CompanyRecord']['retainedearnings'] == $AccountListRow['accountcode']) {
			$ThisMonthArray[$AccountListRow['accountcode']] = 0;
			$ThisPeriodArray[$AccountListRow['accountcode']] = $RetainedEarningsRow['retainedearnings'];
		}
		if ($AccountListRow['groupcode'] != $LastGroup) {
			echo '<tr><td></td></tr>';
			echo '<tr class="total_row">
					<td>', _('Total'), '</td>
					<td>', $LastGroupName, '</td>
					<td class="number">', locale_number_format($MonthActualGroupTotal, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
					<td class="number">', locale_number_format($MonthBudgetGroupTotal, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
					<td class="number">', locale_number_format($PeriodActualGroupTotal, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
					<td class="number">', locale_number_format($PeriodBudgetGroupTotal, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
				</tr>';
			echo '<tr><td></td></tr>';

			echo '<tr><td></td></tr>';
			echo '<tr class="total_row">
					<td>', $AccountListRow['group_'], '</td>
					<td colspan="6"></td>
				</tr>';

			$LastGroup = $AccountListRow['groupcode'];
			$LastGroupName = $AccountListRow['group_'];

			$CumulativeMonthActualGroupTotal+= $MonthActualGroupTotal;
			$CumulativePeriodActualGroupTotal+= $PeriodActualGroupTotal;

			$MonthActualGroupTotal = 0;
			$MonthBudgetGroupTotal = 0;
			$PeriodActualGroupTotal = 0;
			$PeriodBudgetGroupTotal = 0;

		}

		$SQL = "SELECT amount AS monthbudget
				FROM glbudgetdetails
				WHERE account='" . $AccountListRow['accountcode'] . "'
					AND period='" . $_POST['PeriodTo'] . "'
					AND headerid='" . $_POST['SelectedBudget'] . "'";
		$MonthBudgetResult = DB_query($SQL);
		$MonthBudgetRow = DB_fetch_array($MonthBudgetResult);
		if (!isset($MonthBudgetRow['monthbudget'])) {
			$MonthBudgetRow['monthbudget'] = 0;
		}

		$SQL = "SELECT SUM(amount) AS periodbudget
				FROM glbudgetdetails
				WHERE account='" . $AccountListRow['accountcode'] . "'
					AND period>='" . $_POST['PeriodFrom'] . "'
					AND period<='" . $_POST['PeriodTo'] . "'
					AND headerid='" . $_POST['SelectedBudget'] . "'";
		$PeriodBudgetResult = DB_query($SQL);
		$PeriodBudgetRow = DB_fetch_array($PeriodBudgetResult);
		if (!isset($PeriodBudgetRow['periodbudget'])) {
			$PeriodBudgetRow['periodbudget'] = 0;
		}

		echo '<tr class="striped_row">
				<td><a href="', $RootPath, '/GLAccountInquiry.php?PeriodFrom=', $_POST['PeriodFrom'], '&amp;PeriodTo=', $_POST['PeriodTo'], '&amp;Account=', $AccountListRow['accountcode'], '&amp;Show=Yes">', $AccountListRow['accountcode'], '</a></td>
				<td>', $AccountListRow['accountname'], '</td>
				<td class="number">', locale_number_format($ThisMonthArray[$AccountListRow['accountcode']], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
				<td class="number">', locale_number_format($MonthBudgetRow['monthbudget'], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
				<td class="number">', locale_number_format($ThisPeriodArray[$AccountListRow['accountcode']], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
				<td class="number">', locale_number_format($PeriodBudgetRow['periodbudget'], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
			</tr>';
		$MonthActualGroupTotal+= $ThisMonthArray[$AccountListRow['accountcode']];
		$MonthBudgetGroupTotal+= $MonthBudgetRow['monthbudget'];
		$PeriodActualGroupTotal+= $ThisPeriodArray[$AccountListRow['accountcode']];
		$PeriodBudgetGroupTotal+= $PeriodBudgetRow['periodbudget'];
	}
	echo '<tr><td></td></tr>';
	echo '<tr class="total_row">
			<td>', _('Total'), '</td>
			<td>', $LastGroupName, '</td>
			<td class="number">', locale_number_format($MonthActualGroupTotal, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
			<td class="number">', locale_number_format($MonthBudgetGroupTotal, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
			<td class="number">', locale_number_format($PeriodActualGroupTotal, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
			<td class="number">', locale_number_format($PeriodBudgetGroupTotal, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
		</tr>';
	echo '<tr><td></td></tr>';

	$CumulativeMonthActualGroupTotal+= $MonthActualGroupTotal;
	$CumulativePeriodActualGroupTotal+= $PeriodActualGroupTotal;

	echo '<tr><td></td></tr>';
	echo '<tr class="total_row">
			<td>', _('Check Totals'), '</td>
			<td></td>
			<td class="number">', locale_number_format($CumulativeMonthActualGroupTotal, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
			<td class="number"></td>
			<td class="number">', locale_number_format($CumulativePeriodActualGroupTotal, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
			<td class="number"></td>
		</tr>';
	echo '<tr><td></td></tr>';

	echo '</table>';

	echo '</form>';
	include ('includes/footer.php');
}

?>