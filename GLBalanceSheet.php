<?php
/* Through deviousness and cunning, this system allows shows the balance sheets
 * as at the end of any period selected - so first off need to show the input
 * of criteria screen while the user is selecting the period end of the balance
 * date meanwhile the system is posting any unposted transactions

Parameters:
{	PeriodFrom: Select the beginning of the reporting period. Not used in this script.}
	PeriodTo: Select the end of the reporting period.
{	Period: Select a period instead of using the beginning and end of the reporting period. Not used in this script.}
{	ShowBudget: Check this box to show the budget for the period. Not used in this script.}
	ShowDetail: Check this box to show all accounts instead a summary.
	ShowZeroBalance: Check this box to show all accounts including those with zero balance.
	NewReport: Click this button to start a new report.
	IsIncluded: Parameter to indicate that a script is included within another.
*/

if (!isset($IsIncluded)) { // Runs normally if this script is NOT included in another.
	include ('includes/session.php');
}

$Title = _('Balance Sheet'); // Screen identification.
$Title2 = _('Statement of Financial Position'); // Name as IAS.
$ViewTopic = 'GeneralLedger'; // Filename's id in ManualContents.php's TOC.
$BookMark = 'BalanceSheet'; // Anchor's id in the manual's html document.
include_once ('includes/SQL_CommonFunctions.php');
include_once ('includes/AccountSectionsDef.php'); // This loads the $Sections variable
// Merges GETs into POSTs:
if (isset($_GET['PeriodTo'])) {
	$_POST['PeriodTo'] = $_GET['PeriodTo'];
}
if (isset($_GET['ShowDetail'])) { // Select period from.
	$_POST['ShowDetail'] = $_GET['ShowDetail'];
}
if (isset($_GET['ShowZeroBalance'])) { // Select period from.
	$_POST['ShowZeroBalance'] = $_GET['ShowZeroBalance'];
}

if (!isset($_POST['PeriodTo']) or isset($_POST['NewReport'])) {

	/*Show a form to allow input of criteria for Balance Sheet to show */
	include ('includes/header.php');

	echo '<p class="page_title_text">
			<img alt="" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/printer.png" title="', $Title2, '" />', $Title, '
		</p>'; // Page title.
	echo '<div class="page_help_text">', _('Balance Sheet (or statement of financial position) is a summary  of balances. Assets, liabilities and ownership equity are listed as of a specific date, such as the end of its financial year. Of the four basic financial statements, the balance sheet is the only statement which applies to a single point in time.'), '<br />', _('The balance sheet has three parts: assets, liabilities and ownership equity. The main categories of assets are listed first and are followed by the liabilities. The difference between the assets and the liabilities is known as equity or the net assets or the net worth or capital of the company and according to the accounting equation, net worth must equal assets minus liabilities.'), '<br />', $ProjectName, _(' is an accrual based system (not a cash based system).  Accrual systems include items when they are invoiced to the customer, and when expenses are owed based on the supplier invoice date.'), '</div>';

	echo '<form method="post" action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '">';
	echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';
	echo '<fieldset>
			<legend>', _('Criteria for report'), '</legend>
			<field>
				<label for="PeriodTo">', _('Select the balance date'), ':</label>
				<select name="PeriodTo" autofocus="autofocus">';

	if (isset($_POST['PeriodTo'])) {
		$PeriodNo = $_POST['PeriodTo'];
	} else {
		$PeriodNo = GetPeriod(Date($_SESSION['DefaultDateFormat']));
	}
	$SQL = "SELECT lastdate_in_period FROM periods WHERE periodno='" . $PeriodNo . "'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);
	$lastdate_in_period = $MyRow[0];

	$SQL = "SELECT periodno, lastdate_in_period FROM periods ORDER BY periodno DESC";
	$Periods = DB_query($SQL);

	while ($MyRow = DB_fetch_array($Periods)) {
		if ($MyRow['periodno'] == $PeriodNo) {
			echo '<option selected="selected" value="', $MyRow['periodno'], '">', ConvertSQLDate($lastdate_in_period), '</option>';
		} else {
			echo '<option value="', $MyRow['periodno'], '">', ConvertSQLDate($MyRow['lastdate_in_period']), '</option>';
		}
	}

	echo '</select>
		<fieldhelp>', _('Select the period up to which you wish the balance sheet to be shown at.'), '</fieldhelp>
	</field>';

	echo '<field>
			<label for="ShowDetail">', _('Detail Or Summary'), ':</label>
			<select name="ShowDetail">
				<option value="Summary">', _('Summary'), '</option>
				<option selected="selected" value="Detailed">', _('All Accounts'), '</option>
			</select>
			<fieldhelp>', _('Show a summary report, or show all accounts.'), '</fieldhelp>
		</field>';

	echo '<field>
			<label for="ShowZeroBalances">', _('Show all Accounts including zero balances'), '</label>
			<input type="checkbox" checked="checked" name="ShowZeroBalances">
			<fieldhelp>', _('Check this box to display all accounts including those accounts with no balance'), '</fieldhelp>
		</field>
	</fieldset>';

	echo '<div class="centre">
			<input type="submit" name="ShowBalanceSheet" value="', _('Show Balance Sheet'), '" />
		</div>';

	echo '</form>';

} else {

	$ViewTopic = 'GeneralLedger';
	$BookMark = 'BalanceSheet';
	include ('includes/header.php');
	echo '<p class="page_title_text">
			<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/preview.png" title="', _('Balance Sheet'), '" alt="', _('Balance Sheet'), '" /> ', _('Balance Sheet'), '
		</p>';

	echo '<form method="post" action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '">';
	echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';
	echo '<input type="hidden" name="PeriodTo" value="', $_POST['PeriodTo'], '" />';

	$RetainedEarningsAct = $_SESSION['CompanyRecord']['retainedearnings'];

	$SQL = "SELECT lastdate_in_period FROM periods WHERE periodno='" . $_POST['PeriodTo'] . "'";
	$PrdResult = DB_query($SQL);
	$MyRow = DB_fetch_row($PrdResult);
	$BalanceDate = ConvertSQLDate($MyRow[0]);

	/*Calculate B/Fwd retained earnings */

	/* Get the retained earnings amount */
	$ThisYearRetainedEarningsSQL = "SELECT ROUND(SUM(amount),3) AS retainedearnings
									FROM gltotals
									INNER JOIN chartmaster
										ON gltotals.account=chartmaster.accountcode
									INNER JOIN accountgroups
										ON chartmaster.groupcode=accountgroups.groupcode
										AND accountgroups.language=chartmaster.language
									WHERE period<='" . $_POST['PeriodTo'] . "'
										AND chartmaster.language='" . $_SESSION['ChartLanguage'] . "'
										AND pandl=1";
	$ThisYearRetainedEarningsResult = DB_query($ThisYearRetainedEarningsSQL);
	$ThisYearRetainedEarningsRow = DB_fetch_array($ThisYearRetainedEarningsResult);

	$LastYearRetainedEarningsSQL = "SELECT ROUND(SUM(amount),3) AS retainedearnings
									FROM gltotals
									INNER JOIN chartmaster
										ON gltotals.account=chartmaster.accountcode
									INNER JOIN accountgroups
										ON chartmaster.groupcode=accountgroups.groupcode
										AND accountgroups.language=chartmaster.language
									WHERE period<='" . ($_POST['PeriodTo'] - 12) . "'
										AND chartmaster.language='" . $_SESSION['ChartLanguage'] . "'
										AND pandl=1";
	$LastYearRetainedEarningsResult = DB_query($LastYearRetainedEarningsSQL);
	$LastYearRetainedEarningsRow = DB_fetch_array($LastYearRetainedEarningsResult);

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
					AND accountgroups.language=chartmaster.language
				INNER JOIN accountsection
					ON accountsection.sectionid=accountgroups.sectioninaccounts
					AND accountgroups.language=accountsection.language
				WHERE chartmaster.language='" . $_SESSION['ChartLanguage'] . "'
					AND pandl=0
				ORDER BY sequenceintb,
						groupcode,
						accountcode";
	$AccountListResult = DB_query($SQL);

	$SQL = "SELECT account,
					ROUND(SUM(amount),3) AS accounttotal
				FROM gltotals
				WHERE period<='" . $_POST['PeriodTo'] . "'
				GROUP BY account
				ORDER BY account";
	$Result = DB_query($SQL);

	$ThisYearActuals = array();
	while ($MyRow = DB_fetch_array($Result)) {
		$ThisYearActuals[$MyRow['account']] = $MyRow['accounttotal'];
	}

	$SQL = "SELECT account,
					ROUND(SUM(amount),3) AS accounttotal
				FROM gltotals
				WHERE period<='" . ($_POST['PeriodTo'] - 12) . "'
				GROUP BY account
				ORDER BY account";
	$Result = DB_query($SQL);

	$LastYearActuals = array();
	while ($MyRow = DB_fetch_array($Result)) {
		$LastYearActuals[$MyRow['account']] = $MyRow['accounttotal'];
	}

	echo '<table summary="', _('HTML View'), '">
			<thead>
				<tr>
					<th colspan="6">
						<h2>', _('Balance Sheet as at'), ' ', $BalanceDate, '
						<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/printer.png" class="PrintIcon" title="', _('Print'), '" alt="', _('Print'), '" onclick="window.print();" />
						</h2>
					</th>
				</tr>';

	if ($_POST['ShowDetail'] == 'Detailed') {
		echo '<tr>
				<th>', _('Account'), '</th>
				<th>', _('Account Name'), '</th>
				<th colspan="2">', $BalanceDate, '</th>
				<th colspan="2">', _('Last Year'), '</th>
			</tr>';
	} else {
		/*summary */
		echo '<tr>
				<th colspan="2"></th>
				<th colspan="2">', $BalanceDate, '</th>
				<th colspan="2">', _('Last Year'), '</th>
			</tr>';
	}
	echo '</thead>';

	$Section = '';
	$SectionBalance = 0;
	$SectionBalanceLY = 0;

	$LYCheckTotal = 0;
	$CheckTotal = 0;

	$ActGrp = '';
	$Level = 0;
	$ParentGroups = array();
	$ParentGroups[$Level] = '';
	$GroupTotal = array(0);
	$LYGroupTotal = array(0);

	$j = 0; //row counter
	while ($MyRow = DB_fetch_array($AccountListResult)) {
		if (isset($ThisYearActuals[$MyRow['accountcode']])) {
			$AccountBalance = $ThisYearActuals[$MyRow['accountcode']];
		} else {
			$AccountBalance = 0;
		}
		if (isset($LastYearActuals[$MyRow['accountcode']])) {
			$LYAccountBalance = $LastYearActuals[$MyRow['accountcode']];
		} else {
			$LYAccountBalance = 0;
		}

		if ($MyRow['accountcode'] == $RetainedEarningsAct) {
			$AccountBalance = $ThisYearRetainedEarningsRow['retainedearnings'];
			$LYAccountBalance = $LastYearRetainedEarningsRow['retainedearnings'];
		}

		if ($MyRow['group_'] != $ActGrp and $ActGrp != '') {
			if ($MyRow['parentgroupname'] != $ActGrp) {
				while ($MyRow['group_'] != $ParentGroups[$Level] and $Level > 0) {
					if ($_POST['ShowDetail'] == 'Detailed') {
						echo '<tr>
								<td colspan="2"></td>
	  							<td><hr /></td>
								<td></td>
								<td><hr /></td>
								<td></td>
							</tr>';
					}
					echo '<tr class="total_row">
							<td colspan="2"><I>', $ParentGroups[$Level], '</I></td>
							<td class="number">', locale_number_format($GroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td></td>
							<td class="number">', locale_number_format($LYGroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td></td>
						</tr>';
					$GroupTotal[$Level] = 0;
					$LYGroupTotal[$Level] = 0;
					$ParentGroups[$Level] = '';
					$Level--;
					++$j;
				}
				if ($_POST['ShowDetail'] == 'Detailed') {
					echo '<tr>
							<td colspan="2"></td>
							<td><hr /></td>
							<td></td>
							<td><hr /></td>
							<td></td>
						</tr>';
				}

				echo '<tr class="total_row">
						<td colspan="2">', $ParentGroups[$Level], '</td>
						<td class="number">', locale_number_format($GroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
						<td></td>
						<td class="number">', locale_number_format($LYGroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
						<td></td>
					</tr>';

				$GroupTotal[$Level] = 0;
				$LYGroupTotal[$Level] = 0;
				$ParentGroups[$Level] = '';
				++$j;
			}
		}
		if ($MyRow['sectionid'] != $Section) {

			if ($Section != '') {
				if ($_POST['ShowDetail'] == 'Detailed') {
					echo '<tr>
							<td colspan="2"></td>
							<td><hr /></td>
							<td></td>
							<td><hr /></td>
							<td></td>
						</tr>';
				} else {
					echo '<tr>
							<td colspan="3"></td>
							<td><hr /></td>
							<td></td>
							<td><hr /></td>
						</tr>';
				}

				echo '<tr class="total_row">
						<td colspan="3"><h2>', $Sections[$Section], '</h2></td>
						<td class="number">', locale_number_format($SectionBalance, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
						<td></td>
						<td class="number">', locale_number_format($SectionBalanceLY, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
					</tr>';
				++$j;
			}
			$SectionBalanceLY = 0;
			$SectionBalance = 0;
			$Section = $MyRow['sectionid'];

			if ($_POST['ShowDetail'] == 'Detailed') {
				echo '<tr>
						<td colspan="6"><h1>', $Sections[$MyRow['sectionid']], '</h1></td>
					</tr>';
			}
		}

		if ($MyRow['group_'] != $ActGrp) {

			if ($ActGrp != '' and $MyRow['parentgroupname'] == $ActGrp) {
				$Level++;
			}

			if ($_POST['ShowDetail'] == 'Detailed') {
				$ActGrp = $MyRow['group_'];
				echo '<tr>
						<td colspan="6"><h3>', $MyRow['group_'], '</h3></td>
					</tr>';
			}
			$GroupTotal[$Level] = 0;
			$LYGroupTotal[$Level] = 0;
			$ActGrp = $MyRow['group_'];
			$ParentGroups[$Level] = $MyRow['group_'];
			++$j;
		}

		$SectionBalanceLY+= $LYAccountBalance;
		$SectionBalance+= $AccountBalance;
		for ($i = 0;$i <= $Level;$i++) {
			$LYGroupTotal[$i]+= $LYAccountBalance;
			$GroupTotal[$i]+= $AccountBalance;
		}
		$LYCheckTotal+= $LYAccountBalance;
		$CheckTotal+= $AccountBalance;

		if ($_POST['ShowDetail'] == 'Detailed') {

			if (isset($_POST['ShowZeroBalances']) or (!isset($_POST['ShowZeroBalances']) and ($AccountBalance <> 0 or $LYAccountBalance <> 0))) {
				$ActEnquiryURL = '<a href="' . $RootPath . '/GLAccountInquiry.php?FromPeriod=' . urlencode(FYStartPeriod($_POST['PeriodTo'])) . '&ToPeriod=' . urlencode($_POST['PeriodTo']) . '&amp;Account=' . urlencode($MyRow['accountcode']) . '">' . $MyRow['accountcode'] . '</a>';

				echo '<tr class="striped_row">
						<td>', $ActEnquiryURL, '</td>
						<td>', htmlspecialchars($MyRow['accountname'], ENT_QUOTES, 'UTF-8', false), '</td>
						<td class="number">', locale_number_format($AccountBalance, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
						<td></td>
						<td class="number">', locale_number_format($LYAccountBalance, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
						<td></td>
					</tr>';
				++$j;
			}
		}
	}
	//end of loop
	while ($MyRow['groupname'] != $ParentGroups[$Level] and $Level > 0) {
		if ($_POST['ShowDetail'] == 'Detailed') {
			echo '<tr>
					<td colspan="2"></td>
					<td><hr /></td>
					<td></td>
					<td><hr /></td>
					<td></td>
				</tr>';
		}
		echo '<tr class="total_row">
				<td colspan="2"><I>', $ParentGroups[$Level], '</I></td>
				<td class="number">', locale_number_format($GroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
				<td></td>
				<td class="number">', locale_number_format($LYGroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
				<td></td>
			</tr>';
		$Level--;
	}
	if ($_POST['ShowDetail'] == 'Detailed') {
		echo '<tr>
				<td colspan="2"></td>
				<td><hr /></td>
				<td></td>
				<td><hr /></td>
				<td></td>
			</tr>';
	}

	echo '<tr class="total_row">
			<td colspan="2">', $ParentGroups[$Level], '</td>
			<td class="number">', locale_number_format($GroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
			<td></td>
			<td class="number">', locale_number_format($LYGroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
			<td></td>
		</tr>';

	if ($_POST['ShowDetail'] == 'Detailed') {
		echo '<tr>
				<td colspan="2"></td>
				<td><hr /></td>
				<td></td>
				<td><hr /></td>
				<td></td>
			</tr>';
	} else {
		echo '<tr>
				<td colspan="3"></td>
				<td><hr /></td>
				<td></td>
				<td><hr /></td>
			</tr>';
	}

	echo '<tr class="total_row">
			<td colspan="3"><h2>', $Sections[$Section], '</h2></td>
			<td class="number">', locale_number_format($SectionBalance, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
			<td></td>
			<td class="number">', locale_number_format($SectionBalanceLY, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
		</tr>';

	$Section = $MyRow['sectioninaccounts'];

	if (isset($MyRow['sectioninaccounts']) and $_POST['ShowDetail'] == 'Detailed') {
		echo '<tr>
				<td colspan="6"><h1>', $Sections[$MyRow['sectioninaccounts']], '</h1></td>
			</tr>';
	}

	echo '<tr>
			<td colspan="3"></td>
	  		<td><hr /></td>
			<td></td>
			<td><hr /></td>
		</tr>';

	echo '<tr class="total_row">
			<td colspan="3"><h2>', _('Check Total'), '</h2></td>
			<td class="number">', locale_number_format($CheckTotal, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
			<td></td>
			<td class="number">', locale_number_format($LYCheckTotal, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
		</tr>';

	echo '<tr>
			<td colspan="3"></td>
			<td><hr /></td>
			<td></td>
			<td><hr /></td>
		</tr>';

	echo '</table>';
	echo '<div class="centre">
			<input type="submit" name="NewReport" value="', _('Select A Different Balance Date'), '" />
		</div>';
	echo '</form>';
}
if (!isset($IsIncluded)) { // Runs normally if this script is NOT included in another.
	include ('includes/footer.php');
}
?>