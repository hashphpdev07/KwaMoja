<?php
/*
Info about financial statements: IAS 1 - Presentation of Financial Statements.
Parameters:
	PeriodFrom: Select the beginning of the reporting period.
	PeriodTo: Select the end of the reporting period.
	Period: Select a period instead of using the beginning and end of the reporting period.
{	ShowBudget: Check this box to show the budget for the period. Not used in this script.}
	ShowDetail: Check this box to show all accounts instead a summary.
	ShowZeroBalance: Check this box to show all accounts including those with zero balance.
	NewReport: Click this button to start a new report.
	IsIncluded: Parameter to indicate that a script is included within another.
*/

$Title = _('Profit and Loss'); // Screen identification.
$Title2 = _('Statement of Comprehensive Income'); // Name as IAS
$ViewTopic = 'GeneralLedger'; // Filename's id in ManualContents.php's TOC.
$BookMark = 'ProfitAndLoss'; // Anchor's id in the manual's html document.
// BEGIN: Procedure division ===================================================
include ('includes/session.php');

include_once ('includes/SQL_CommonFunctions.php');
include_once ('includes/AccountSectionsDef.php'); // This loads the $Sections variable
if (isset($_POST['PeriodFrom']) and ($_POST['PeriodFrom'] > $_POST['PeriodTo'])) {
	prnMsg(_('The selected period from is actually after the period to') . '! ' . _('Please reselect the reporting period'), 'error');
	$_POST['SelectADifferentPeriod'] = 'Select A Different Period';
}

if (isset($_GET['PeriodFrom'])) {
	$_POST['PeriodFrom'] = $_GET['PeriodFrom'];
}
if (isset($_GET['PeriodTo'])) {
	$_POST['PeriodTo'] = $_GET['PeriodTo'];
}

if (isset($_POST['Period']) and $_POST['Period'] != '') {
	$_POST['PeriodFrom'] = ReportPeriod($_POST['Period'], 'From');
	$_POST['PeriodTo'] = ReportPeriod($_POST['Period'], 'To');
}

if ((!isset($_POST['PeriodFrom']) and !isset($_POST['PeriodTo'])) or isset($_POST['SelectADifferentPeriod'])) {

	include ('includes/header.php');

	echo '<p class="page_title_text">
			<img alt="" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/printer.png" title="', $Title2, '" />', $Title, '
		</p>'; // Page title.
	echo '<div class="page_help_text">', _('Profit and loss statement, also called an Income Statement, or Statement of Operations, this is the statement that indicates how the revenue (money received from the sale of products and services before expenses are taken out, also known as the top line) is transformed into the net income (the result after all revenues and expenses have been accounted for, also known as the bottom line).'), '<br />', _('The purpose of the income statement is to show whether the company made or lost money during the period being reported.'), '<br />', _('The Profit and Loss statement represents a period of time. This contrasts with the Balance Sheet, which represents a single moment in time.'), '<br />', $ProjectName, _(' is an accrual based system (not a cash based system).  Accrual systems include items when they are invoiced to the customer, and when expenses are owed based on the supplier invoice date.'), '</div>';

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

	/*Show a form to allow input of criteria for profit and loss to show */
	echo '<fieldset>
			<legend>', _('Criteria for report'), '</legend>
			<field>
				<label for="PeriodFrom">', _('Select Period From'), ':</label>
				<select name="PeriodFrom" autofocus="autofocus">';

	$SQL = "SELECT periodno,
					lastdate_in_period
			FROM periods
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
				echo '<option selected="selected" value="' . $MyRow['periodno'] . '">' . MonthAndYearFromSQLDate($MyRow['lastdate_in_period']) . '</option>';
			} else {
				echo '<option value="', $MyRow['periodno'], '">', MonthAndYearFromSQLDate($MyRow['lastdate_in_period']), '</option>';
			}
		}
	}
	echo '</select>
		<fieldhelp>', _('Select the starting period for this report'), '</fieldhelp>
	</field>';

	if (!isset($_POST['PeriodTo']) or $_POST['PeriodTo'] == '') {
		$LastDate = date('Y-m-d', mktime(0, 0, 0, Date('m') + 1, 0, Date('Y')));
		$SQL = "SELECT periodno FROM periods where lastdate_in_period = '" . $LastDate . "'";
		$MaxPrd = DB_query($SQL);
		$MaxPrdrow = DB_fetch_row($MaxPrd);
		$DefaultPeriodTo = (int)($MaxPrdrow[0]);

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
			echo '<option value="' . $MyRow['periodno'] . '">' . MonthAndYearFromSQLDate($MyRow['lastdate_in_period']) . '</option>';
		}
	}
	echo '</select>
		<fieldhelp>', _('Select the end period for this report'), '</fieldhelp>
	</field>';

	if (!isset($_POST['Period'])) {
		$_POST['Period'] = '';
	}

	echo '<h3>', _('OR'), '</h3>';

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

	echo '<field>
			<label for="ShowDetail">', _('Detail Or Summary'), ':</label>
			<select name="ShowDetail">
				<option value="Summary">', _('Summary'), '</option>
				<option selected="selected" value="Detailed">', _('All Accounts'), '</option>
			</select>
			<fieldhelp>', _('Show report for all accounts, or just show summary report.'), '</fieldhelp>
		</field>';

	echo '<field>
			<label for="ShowZeroBalances">', _('Show all Accounts including zero balances'), '</label>
			<input type="checkbox" checked="checked" title="', _('Check this box to display all accounts including those accounts with no balance'), '" name="ShowZeroBalances">
			<fieldhelp>', _('Show all accounts, or just accounts with balances.'), '</fieldhelp>
		</field>';

	echo '</fieldset>';

	echo '<div class="centre">
			<input type="submit" name="ShowPL" value="', _('Show Profit and Loss Statement'), '" />
		</div>
	</form>';

} else {

	$ViewTopic = 'GeneralLedger';
	$BookMark = 'ProfitAndLoss';
	include ('includes/header.php');

	$NumberOfMonths = $_POST['PeriodTo'] - $_POST['PeriodFrom'] + 1;

	$SQL = "SELECT lastdate_in_period FROM periods WHERE periodno='" . $_POST['PeriodTo'] . "'";
	$PrdResult = DB_query($SQL);
	$MyRow = DB_fetch_row($PrdResult);
	$PeriodToDate = MonthAndYearFromSQLDate($MyRow[0]);

	if ($NumberOfMonths > 12) {
		include ('includes/header.php');
		echo '<p>';
		prnMsg(_('A period up to 12 months in duration can be specified') . ' - ' . _('the system automatically shows a comparative for the same period from the previous year') . ' - ' . _('it cannot do this if a period of more than 12 months is specified') . '. ' . _('Please select an alternative period range'), 'error');
		include ('includes/footer.php');
		exit;
	}
	echo '<p class="page_title_text">
			<img alt="" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/transactions.png" title="', $Title2, '" />
			', $Title, ' ', _('For'), ' ', $NumberOfMonths, ' ', _('months to'), ' and including ', $PeriodToDate, '
		</p>'; // Page title.
	echo '<table summary="', _('General Ledger Profit Loss Inquiry'), '">
			<thead>
				<tr>
					<th colspan="10">
						<b>', _('General Ledger Profit Loss Inquiry'), '</b>
						<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/printer.png" class="PrintIcon" title="', _('Print'), '" alt="', _('Print'), '" onclick="window.print();" />
					</th>
				</tr>';

	if ($_POST['ShowDetail'] == 'Detailed') {
		echo '<tr>
				<th>', _('Account'), '</th>
				<th>', _('Account Name'), '</th>
				<th colspan="2">', _('Period Actual'), '</th>
				<th colspan="2">', _('Period Budget'), '</th>
				<th colspan="2">', _('Last Year'), '</th>
			</tr>';
	} else {
		/*summary */
		echo '<tr>
				<th colspan="2"></th>
				<th colspan="2">', _('Period Actual'), '</th>
				<th colspan="2">', _('Period Budget'), '</th>
				<th colspan="2">', _('Last Year'), '</th>
			</tr>';
	}
	echo '</thead>';
	$j = 1;

	$Section = '';
	$SectionPrdActual = 0;
	$SectionPrdLY = 0;
	$SectionPrdBudget = 0;

	$PeriodProfitLoss = 0;
	$PeriodProfitLoss = 0;
	$PeriodLYProfitLoss = 0;
	$PeriodBudgetProfitLoss = 0;

	$ActGrp = '';
	$ParentGroups = array();
	$Level = 0;
	$ParentGroups[$Level] = '';
	$GrpPrdActual = array(0);
	$GrpPrdLY = array(0);
	$GrpPrdBudget = array(0);
	$TotalIncome = 0;
	$TotalBudgetIncome = 0;
	$TotalLYIncome = 0;

	$PeriodProfitLossActual = 0;
	$PeriodProfitLossBudget = 0;
	$PeriodProfitLossLY = 0;

	// Get all account codes
	$SQL = "SELECT sectionid,
					sectionname,
					parentgroupname,
					parentgroupcode,
					chartmaster.groupcode,
					chartmaster.accountcode,
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
				INNER JOIN accountsection
					ON accountsection.sectionid=accountgroups.sectioninaccounts
				WHERE chartmaster.language='" . $_SESSION['ChartLanguage'] . "'
					AND pandl=1
				ORDER BY sequenceintb,
						groupcode,
						accountcode";
	$AccountListResult = DB_query($SQL);

	$SQL = "SELECT account,
					SUM(amount) AS accounttotal
				FROM gltotals
				WHERE period>='" . $_POST['PeriodFrom'] . "'
					AND period<='" . $_POST['PeriodTo'] . "'
				GROUP BY account
				ORDER BY account";
	$Result = DB_query($SQL);

	$ThisYearActuals = array();
	while ($MyRow = DB_fetch_array($Result)) {
		$ThisYearActuals[$MyRow['account']] = $MyRow['accounttotal'];
	}

	$SQL = "SELECT account,
					SUM(amount) AS accounttotal
				FROM gltotals
				WHERE period>='" . ($_POST['PeriodFrom'] - 12) . "'
					AND period<='" . ($_POST['PeriodTo'] - 12) . "'
				GROUP BY account
				ORDER BY account";
	$Result = DB_query($SQL);

	$LastYearActuals = array();
	while ($MyRow = DB_fetch_array($Result)) {
		$LastYearActuals[$MyRow['account']] = $MyRow['accounttotal'];
	}

	while ($MyRow = DB_fetch_array($AccountListResult)) {

		$SQL = "SELECT SUM(amount) AS periodbudget
				FROM glbudgetdetails
				WHERE account='" . $MyRow['accountcode'] . "'
					AND period>='" . $_POST['PeriodFrom'] . "'
					AND period<='" . $_POST['PeriodTo'] . "'
					AND headerid='" . $_POST['SelectedBudget'] . "'";
		$PeriodBudgetResult = DB_query($SQL);
		$PeriodBudgetRow = DB_fetch_array($PeriodBudgetResult);
		if (!isset($PeriodBudgetRow['periodbudget'])) {
			$PeriodBudgetRow['periodbudget'] = 0;
		}
		if ($MyRow['group_'] != $ActGrp) {
			if ($MyRow['parentgroupname'] != $ActGrp and $ActGrp != '') {
				while ($MyRow['group_'] != $ParentGroups[$Level] and $Level > 0) {
					if ($_POST['ShowDetail'] == 'Detailed') {
						echo '<tr>
								<td colspan="2"></td>
								<td colspan="6"><hr /></td>
							</tr>';
						$ActGrpLabel = str_repeat('___', $Level) . $ParentGroups[$Level] . ' ' . _('total');
					} else {
						$ActGrpLabel = str_repeat('___', $Level) . $ParentGroups[$Level];
					}
					if ($Section == 1) { /*Income */
						echo '<tr>
								<td colspan="2"><h4><i>', $ActGrpLabel, '</i></h4></td>
								<td>&nbsp;</td>
								<td class="number">', locale_number_format(-$GrpPrdActual[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
								<td>&nbsp;</td>
								<td class="number">', locale_number_format(-$GrpPrdBudget[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
								<td>&nbsp;</td>
								<td class="number">', locale_number_format(-$GrpPrdLY[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							</tr>';
					} else { /*Costs */
						echo '<tr>
								<td colspan="2"><h4><i>', $ActGrpLabel, '</i></h4></td>
								<td class="number">', locale_number_format($GrpPrdActual[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
								<td>&nbsp;</td>
								<td class="number">', locale_number_format($GrpPrdBudget[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
								<td>&nbsp;</td>
								<td class="number">', locale_number_format($GrpPrdLY[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
								<td>&nbsp;</td>
							</tr>';
					}
					$GrpPrdActual[$Level] = 0;
					$GrpPrdBudget[$Level] = 0;
					$GrpPrdLY[$Level] = 0;
					$ParentGroups[$Level] = '';
					$Level--;
				} //end while
				//still need to print out the old group totals
				if ($_POST['ShowDetail'] == 'Detailed') {
					echo '<tr>
							<td colspan="2"></td>
							<td colspan="6"><hr /></td>
						</tr>';
					$ActGrpLabel = str_repeat('___', $Level) . $ParentGroups[$Level] . ' ' . _('total');
				} else {
					$ActGrpLabel = str_repeat('___', $Level) . $ParentGroups[$Level];
				}

				if ($Section == 1) { /*Income */
					echo '<tr class="total_row">
							<td colspan="2"><h4><i>', $ActGrpLabel, '</i></h4></td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format(-$GrpPrdActual[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format(-$GrpPrdBudget[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format(-$GrpPrdLY[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
						</tr>';
				} else { /*Costs */
					echo '<tr class="total_row">
							<td colspan="2"><h4><i>', $ActGrpLabel, '</i></h4></td>
							<td class="number">', locale_number_format($GrpPrdActual[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format($GrpPrdBudget[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format($GrpPrdLY[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td>&nbsp;</td>
						</tr>';
				}
				$GrpPrdLY[$Level] = 0;
				$GrpPrdActual[$Level] = 0;
				$GrpPrdBudget[$Level] = 0;
				$ParentGroups[$Level] = '';
			}
		}

		if ($MyRow['sectionid'] != $Section) {

			if ($SectionPrdLY + $SectionPrdActual + $SectionPrdBudget != 0) {
				if ($Section == 1) { /*Income*/
					echo '<tr>
							<td colspan="3"></td>
							<td><hr /></td>
							<td>&nbsp;</td>
							<td><hr /></td>
							<td>&nbsp;</td>
							<td><hr /></td>
						</tr>', '<tr class="total_row">
							<td colspan="2"><h2>', $Sections[$Section], '</td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format(-$SectionPrdActual, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format(-$SectionPrdBudget, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format(-$SectionPrdLY, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
						</tr>';
					$TotalIncomeActual = - $SectionPrdActual;
					$TotalIncomeBudget = - $SectionPrdBudget;
					$TotalIncomeLY = - $SectionPrdLY;
				} else {
					echo '<tr>
							<td colspan="2"></td>
							<td><hr /></td>
							<td>&nbsp;</td>
							<td><hr /></td>
							<td>&nbsp;</td>
							<td><hr /></td>
						</tr>', '<tr class="total_row">
							<td colspan="2"><h2>', $Sections[$Section], '</h2></td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format($SectionPrdActual, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format($SectionPrdBudget, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format($SectionPrdLY, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
						</tr>';
				}
				if ($Section == 2) { /*Cost of Sales - need sub total for Gross Profit*/
					echo '<tr>
							<td colspan="2"></td>
							<td colspan="6"><hr /></td>
						</tr>', '<tr class="total_row">
							<td colspan="2"><h2>', _('Gross Profit'), '</h2></td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format($TotalIncomeActual - $SectionPrdActual, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format($TotalIncomeBudget - $SectionPrdBudget, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format($TotalIncomeLY - $SectionPrdLY, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
						</tr>';

					if ($TotalIncomeActual != 0) {
						$GPPercentActual = ($TotalIncomeActual - $SectionPrdActual) / $TotalIncomeActual * 100;
					} else {
						$GPPercentActual = 0;
					}
					if ($TotalIncomeBudget != 0) {
						$GPPercentBudget = ($TotalIncomeBudget - $SectionPrdBudget) / $TotalIncomeBudget * 100;
					} else {
						$GPPercentBudget = 0;
					}
					if ($TotalIncomeLY != 0) {
						$GPPercentLY = ($TotalIncomeLY - $SectionPrdLY) / $TotalIncomeLY * 100;
					} else {
						$GPPercentLY = 0;
					}
					echo '<tr>
							<td colspan="2"></td>
							<td colspan="6"><hr /></td>
						</tr>', '<tr class="total_row">
							<td colspan="2"><h4><i>', _('Gross Profit Percent'), '</i></h4></td>
							<td>&nbsp;</td>
							<td class="number"><i>', locale_number_format($GPPercentActual, 1), '%</i></td>
							<td>&nbsp;</td>
							<td class="number"><i>', locale_number_format($GPPercentBudget, 1), '%</i></td>
							<td>&nbsp;</td>
							<td class="number"><i>', locale_number_format($GPPercentLY, 1), '%</i></td>
						</tr>
						<tr><td colspan="6">&nbsp;</td></tr>';
				}

				if (($Section != 1) and ($Section != 2)) {
					echo '<tr>
							<td colspan="2"></td>
							<td colspan="6"><hr /></td>
						</tr>', '<tr class="total_row">
							<td colspan="2"><h4><b>', _('Profit') . ' - ' . _('Loss') . ' ' . _('after') . ' ', $Sections[$Section], '</b></h2></td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format(-$PeriodProfitLossActual, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format(-$PeriodProfitLossBudget, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format(-$PeriodProfitLossLY, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
						</tr>';

					if ($TotalIncomeActual != 0) {
						$NPPercentActual = (-$PeriodProfitLossActual) / $TotalIncomeActual * 100;
					} else {
						$NPPercentActual = 0;
					}
					if ($TotalIncomeBudget != 0) {
						$NPPercentBudget = (-$PeriodProfitLossBudget) / $TotalIncomeBudget * 100;
					} else {
						$NPPercentBudget = 0;
					}
					if ($TotalIncomeLY != 0) {
						$NPPercentLY = (-$PeriodProfitLossLY) / $TotalIncomeLY * 100;
					} else {
						$NPPercentLY = 0;
					}
					echo '<tr class="total_row">
							<td colspan="2"><h4><i>', _('P/L Percent after') . ' ' . $Sections[$Section], '</i></h4></td>
							<td>&nbsp;</td>
							<td class="number"><i>', locale_number_format($NPPercentActual, 1), '%</i></td>
							<td>&nbsp;</td>
							<td class="number"><i>', locale_number_format($NPPercentBudget, 1), '%</i></td>
							<td>&nbsp;</td>
							<td class="number"><i>', locale_number_format($NPPercentLY, 1), '%</i></td>
						</tr>
						<tr><td colspan="6">&nbsp;</td></tr>', '<tr>
							<td colspan="2"></td>
							<td colspan="6"><hr /></td>
						</tr>';
				}
			}
			$SectionPrdActual = 0;
			$SectionPrdBudget = 0;
			$SectionPrdLY = 0;
			$Section = $MyRow['sectionid'];
			if ($_POST['ShowDetail'] == 'Detailed') {
				echo '<tr>
						<td colspan="6"><h2><b>', $Sections[$MyRow['sectionid']], '</b></h2></td>
					</tr>';
			}
		}

		if ($MyRow['group_'] != $ActGrp) {
			if ($MyRow['parentgroupname'] == $ActGrp and $ActGrp != '') { //adding another level of nesting
				$Level++;
			}

			$ParentGroups[$Level] = $MyRow['group_'];
			$ActGrp = $MyRow['group_'];
			if ($_POST['ShowDetail'] == 'Detailed') {
				echo '<tr>
						<td colspan="8"><b>', $MyRow['group_'], '</b></td>
					</tr>';
			}
		}
		$AccountPeriodActual = $ThisYearActuals[$MyRow['accountcode']];
		$AccountPeriodBudget = $PeriodBudgetRow['periodbudget'];
		$AccountPeriodLY = $LastYearActuals[$MyRow['accountcode']];
		$PeriodProfitLossActual+= $AccountPeriodActual;
		$PeriodProfitLossBudget+= $AccountPeriodBudget;
		$PeriodProfitLossLY+= $AccountPeriodLY;

		for ($i = 0;$i <= $Level;$i++) {
			if (!isset($GrpPrdActual[$i])) {
				$GrpPrdActual[$i] = 0;
			}
			$GrpPrdActual[$i]+= $AccountPeriodActual;
			if (!isset($GrpPrdBudget[$i])) {
				$GrpPrdBudget[$i] = 0;
			}
			$GrpPrdBudget[$i]+= $AccountPeriodBudget;
			if (!isset($GrpPrdLY[$i])) {
				$GrpPrdLY[$i] = 0;
			}
			$GrpPrdLY[$i]+= $AccountPeriodLY;
		}
		$SectionPrdActual+= $AccountPeriodActual;
		$SectionPrdBudget+= $AccountPeriodBudget;
		$SectionPrdLY+= $AccountPeriodLY;

		if ($_POST['ShowDetail'] == 'Detailed') {
			if (isset($_POST['ShowZeroBalance']) or (!isset($_POST['ShowZeroBalance']) and ($AccountPeriodActual <> 0 or $AccountPeriodBudget <> 0 or $AccountPeriodLY <> 0))) {
				$ActEnquiryURL = '<a href="' . $RootPath . '/GLAccountInquiry.php?PeriodFrom=' . urlencode($_POST['PeriodFrom']) . '&amp;PeriodTo=' . urlencode($_POST['PeriodTo']) . '&amp;Account=' . urlencode($MyRow['accountcode']) . '&amp;Show=Yes">' . $MyRow['accountcode'] . '</a>';
				if ($Section == 1) {
					echo '<tr class="striped_row">
							<td>', $ActEnquiryURL, '</td>
							<td>', htmlspecialchars($MyRow['accountname'], ENT_QUOTES, 'UTF-8', false), '</td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format(-$AccountPeriodActual, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format(-$AccountPeriodBudget, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format(-$AccountPeriodLY, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
						</tr>';
				} else {
					echo '<tr class="striped_row">
							<td>', $ActEnquiryURL, '</td>
							<td>', htmlspecialchars($MyRow['accountname'], ENT_QUOTES, 'UTF-8', false), '</td>
							<td class="number">', locale_number_format($AccountPeriodActual, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format($AccountPeriodBudget, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td>&nbsp;</td>
							<td class="number">', locale_number_format($AccountPeriodLY, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td>&nbsp;</td>
						</tr>';
				}
			}
		}
	}
	//end of loop
	echo '<tr>
			<td colspan="2"></td>
			<td colspan="6"><hr /></td>
		</tr>';

	echo '<tr class="total_row">
			<td colspan="2"><h2><b>' . _('Profit') . ' - ' . _('Loss') . '</b></h2></td>
			<td>&nbsp;</td>
			<td class="number">', locale_number_format(-$PeriodProfitLossActual, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
			<td>&nbsp;</td>
			<td class="number">', locale_number_format(-$PeriodProfitLossBudget, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
			<td>&nbsp;</td>
			<td class="number">', locale_number_format(-$PeriodProfitLossLY, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
		</tr>';

	if ($TotalIncomeActual != 0) {
		$NPPercentActual = (-$PeriodProfitLossActual) / $TotalIncomeActual * 100;
	} else {
		$NPPercentActual = 0;
	}
	if ($TotalIncomeBudget != 0) {
		$NPPercentBudget = (-$PeriodProfitLossBudget) / $TotalIncomeBudget * 100;
	} else {
		$NPPercentBudget = 0;
	}
	if ($TotalIncomeLY != 0) {
		$NPPercentLY = (-$PeriodProfitLossLY) / $TotalIncomeLY * 100;
	} else {
		$NPPercentLY = 0;
	}
	echo '<tr>
			<td colspan="2"></td>
			<td colspan="6"><hr /></td>
		</tr>', '<tr class="total_row">
				<td colspan="2"><h4><i>', _('Net Profit Percent'), '</i></h4></td>
				<td>&nbsp;</td>
				<td class="number"><i>', locale_number_format($NPPercentActual, 1), '%</i></td>
				<td>&nbsp;</td>
				<td class="number"><i>', locale_number_format($NPPercentBudget, 1), '%</i></td>
				<td>&nbsp;</td>
				<td class="number"><i>', locale_number_format($NPPercentLY, 1), '%</i></td>
		</tr>
		<tr><td colspan="6">&nbsp;</td>
		</tr>', '<tr>
			<td colspan="2"></td>
			<td colspan="6"><hr /></td>
		</tr>
		</tbody></table>', '</div>'; // div id="Report".
	

	echo '</table>';

	include ('includes/footer.php');

}
echo '</form>';

?>