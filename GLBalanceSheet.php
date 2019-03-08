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

if (!isset($_POST['PeriodTo']) or isset($_POST['SelectADifferentPeriod'])) {

	/*Show a form to allow input of criteria for Balance Sheet to show */
	include ('includes/header.php');

	echo '<p class="page_title_text">
			<img class="page_title_icon" alt="" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/printer.png" title="', $Title2, '" />', $Title, '
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
			<input type="submit" name="ShowBalanceSheet" value="', _('Show on Screen (HTML)'), '" />
		</div>';
	echo '<div class="centre">
			<input type="submit" name="PrintPDF" value="', _('Produce PDF Report'), '" />
		</div>';
	echo '</form>';

	/*Now do the posting while the user is thinking about the period to select */
	include ('includes/GLPostings.php');

} elseif (isset($_POST['PrintPDF'])) {

	include ('includes/PDFStarter.php');
	$PDF->addInfo('Title', _('Balance Sheet'));
	$PDF->addInfo('Subject', _('Balance Sheet'));
	$line_height = 12;
	$PageNumber = 0;
	$FontSize = 10;

	$RetainedEarningsAct = $_SESSION['CompanyRecord']['retainedearnings'];

	$SQL = "SELECT lastdate_in_period FROM periods WHERE periodno='" . $_POST['PeriodTo'] . "'";
	$PrdResult = DB_query($SQL);
	$MyRow = DB_fetch_row($PrdResult);
	$BalanceDate = ConvertSQLDate($MyRow[0]);

	/*Calculate B/Fwd retained earnings */

	$SQL = "SELECT Sum(CASE WHEN chartdetails.period='" . $_POST['PeriodTo'] . "' THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END) AS accumprofitbfwd,
			Sum(CASE WHEN chartdetails.period='" . ($_POST['PeriodTo'] - 12) . "' THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END) AS lyaccumprofitbfwd
		FROM chartmaster
		INNER JOIN accountgroups
			ON chartmaster.groupcode = accountgroups.groupcode
			AND chartmaster.language = accountgroups.language
		INNER JOIN chartdetails
			ON chartmaster.accountcode= chartdetails.accountcode
		WHERE accountgroups.pandl=1
			AND chartmaster.language='" . $_SESSION['ChartLanguage'] . "'";

	$AccumProfitResult = DB_query($SQL);
	if (DB_error_no() != 0) {
		$Title = _('Balance Sheet') . ' - ' . _('Problem Report') . '....';
		include ('includes/header.php');
		prnMsg(_('The accumulated profits brought forward could not be calculated by the SQL because') . ' - ' . DB_error_msg());
		echo '<br />
				<a href="', $RootPath, '/index.php">', _('Back to the menu'), '</a>';
		if ($Debug == 1) {
			echo '<br />', $SQL;
		}
		include ('includes/footer.php');
		exit;
	}

	$AccumProfitRow = DB_fetch_array($AccumProfitResult);
	/*should only be one row returned */

	$SQL = "SELECT accountgroups.sectioninaccounts,
			accountgroups.groupname,
			accountgroups.parentgroupname,
			chartdetails.accountcode ,
			chartmaster.accountname,
			Sum(CASE WHEN chartdetails.period='" . $_POST['PeriodTo'] . "' THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END) AS balancecfwd,
			Sum(CASE WHEN chartdetails.period='" . ($_POST['PeriodTo'] - 12) . "' THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END) AS lybalancecfwd
		FROM chartmaster
			INNER JOIN accountgroups
				ON chartmaster.groupcode = accountgroups.groupcode
				AND chartmaster.language = accountgroups.language
			INNER JOIN chartdetails
				ON chartmaster.accountcode= chartdetails.accountcode
			INNER JOIN glaccountusers
				ON glaccountusers.accountcode=chartmaster.accountcode
				AND glaccountusers.userid='" . $_SESSION['UserID'] . "'
				AND glaccountusers.canview=1
		WHERE accountgroups.pandl=0
			AND chartmaster.language='" . $_SESSION['ChartLanguage'] . "'
		GROUP BY accountgroups.groupcode,
			chartdetails.accountcode,
			chartmaster.accountname,
			accountgroups.parentgroupname,
			accountgroups.sequenceintb,
			accountgroups.sectioninaccounts
		ORDER BY accountgroups.sectioninaccounts,
			accountgroups.sequenceintb,
			accountgroups.groupcode,
			chartdetails.accountcode";

	$AccountsResult = DB_query($SQL);

	if (DB_error_no() != 0) {
		$Title = _('Balance Sheet') . ' - ' . _('Problem Report') . '....';
		include ('includes/header.php');
		prnMsg(_('No general ledger accounts were returned by the SQL because') . ' - ' . DB_error_msg());
		echo '<br /><a href="', $RootPath, '/index.php">', _('Back to the menu'), '</a>';
		if ($Debug == 1) {
			echo '<br />', $SQL;
		}
		include ('includes/footer.php');
		exit;
	}

	$ListCount = DB_num_rows($AccountsResult); // UldisN
	include ('includes/PDFBalanceSheetPageHeader.php');

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

	while ($MyRow = DB_fetch_array($AccountsResult)) {
		$AccountBalance = $MyRow['balancecfwd'];
		$LYAccountBalance = $MyRow['lybalancecfwd'];

		if ($MyRow['accountcode'] == $RetainedEarningsAct) {
			$AccountBalance+= $AccumProfitRow['accumprofitbfwd'];
			$LYAccountBalance+= $AccumProfitRow['lyaccumprofitbfwd'];
		}
		if ($ActGrp != '') {
			if ($MyRow['groupname'] != $ActGrp) {
				$FontSize = 8;
				$PDF->setFont('', 'B');
				while ($MyRow['groupname'] != $ParentGroups[$Level] and $Level > 0) {
					$YPos-= $line_height;
					$LeftOvers = $PDF->addTextWrap($Left_Margin + (10 * ($Level + 1)), $YPos, 200, $FontSize, _('Total') . ' ' . $ParentGroups[$Level]);
					$LeftOvers = $PDF->addTextWrap($Left_Margin + 250, $YPos, 100, $FontSize, locale_number_format($GroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), 'right');
					$LeftOvers = $PDF->addTextWrap($Left_Margin + 350, $YPos, 100, $FontSize, locale_number_format($LYGroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), 'right');
					$ParentGroups[$Level] = '';
					$GroupTotal[$Level] = 0;
					$LYGroupTotal[$Level] = 0;
					$Level--;
					if ($YPos < $Bottom_Margin) {
						include ('includes/PDFBalanceSheetPageHeader.php');
					}
				}
				$YPos-= $line_height;
				$LeftOvers = $PDF->addTextWrap($Left_Margin + (10 * ($Level + 1)), $YPos, 200, $FontSize, _('Total') . ' ' . $ParentGroups[$Level]);
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 250, $YPos, 100, $FontSize, locale_number_format($GroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), 'right');
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 350, $YPos, 100, $FontSize, locale_number_format($LYGroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), 'right');
				$ParentGroups[$Level] = '';
				$GroupTotal[$Level] = 0;
				$LYGroupTotal[$Level] = 0;
				$YPos-= $line_height;
				if ($YPos < $Bottom_Margin) {
					include ('includes/PDFBalanceSheetPageHeader.php');
				}
			}
		}

		if ($MyRow['sectioninaccounts'] != $Section) {

			if ($Section != '') {
				$FontSize = 8;
				$PDF->setFont('', 'B');
				$LeftOvers = $PDF->addTextWrap($Left_Margin, $YPos, 200, $FontSize, $Sections[$Section]);
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 250, $YPos, 100, $FontSize, locale_number_format($SectionBalance, $_SESSION['CompanyRecord']['decimalplaces']), 'right');
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 350, $YPos, 100, $FontSize, locale_number_format($SectionBalanceLY, $_SESSION['CompanyRecord']['decimalplaces']), 'right');
				$YPos-= (2 * $line_height);
				if ($YPos < $Bottom_Margin) {
					include ('includes/PDFBalanceSheetPageHeader.php');
				}
			}
			$SectionBalanceLY = 0;
			$SectionBalance = 0;

			$Section = $MyRow['sectioninaccounts'];
			if ($_POST['ShowDetail'] == 'Detailed') {

				$LeftOvers = $PDF->addTextWrap($Left_Margin, $YPos, 200, $FontSize, $Sections[$MyRow['sectioninaccounts']]);
				$YPos-= (2 * $line_height);
				if ($YPos < $Bottom_Margin) {
					include ('includes/PDFBalanceSheetPageHeader.php');
				}
			}
		}

		if ($MyRow['groupname'] != $ActGrp) {
			if ($YPos < $Bottom_Margin + $line_height) {
				include ('includes/PDFBalanceSheetPageHeader.php');
			}
			$FontSize = 8;
			$PDF->setFont('', 'B');
			if ($MyRow['parentgroupname'] == $ActGrp and $ActGrp != '') {
				$Level++;
			}
			$ActGrp = $MyRow['groupname'];
			$ParentGroups[$Level] = $ActGrp;
			if ($_POST['ShowDetail'] == 'Detailed') {
				$LeftOvers = $PDF->addTextWrap($Left_Margin, $YPos, 200, $FontSize, $MyRow['groupname']);
				$YPos-= $line_height;
			}
			$GroupTotal[$Level] = 0;
			$LYGroupTotal[$Level] = 0;
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
			if (isset($_POST['ShowZeroBalances']) or (!isset($_POST['ShowZeroBalances']) and (round($AccountBalance, $_SESSION['CompanyRecord']['decimalplaces']) <> 0 or round($LYAccountBalance, $_SESSION['CompanyRecord']['decimalplaces']) <> 0))) {
				$FontSize = 8;
				$PDF->setFont('', '');
				$LeftOvers = $PDF->addTextWrap($Left_Margin, $YPos, 50, $FontSize, $MyRow['accountcode']);
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 55, $YPos, 200, $FontSize, $MyRow['accountname']);
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 250, $YPos, 100, $FontSize, locale_number_format($AccountBalance, $_SESSION['CompanyRecord']['decimalplaces']), 'right');
				$LeftOvers = $PDF->addTextWrap($Left_Margin + 350, $YPos, 100, $FontSize, locale_number_format($LYAccountBalance, $_SESSION['CompanyRecord']['decimalplaces']), 'right');
				$YPos-= $line_height;
			}
		}
	}
	$FontSize = 8;
	$PDF->setFont('', 'B');
	while ($Level > 0) {
		$YPos-= $line_height;
		$LeftOvers = $PDF->addTextWrap($Left_Margin + (10 * ($Level + 1)), $YPos, 200, $FontSize, _('Total') . ' ' . $ParentGroups[$Level]);
		$LeftOvers = $PDF->addTextWrap($Left_Margin + 250, $YPos, 100, $FontSize, locale_number_format($GroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), 'right');
		$LeftOvers = $PDF->addTextWrap($Left_Margin + 350, $YPos, 100, $FontSize, locale_number_format($LYGroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), 'right');
		$ParentGroups[$Level] = '';
		$GroupTotal[$Level] = 0;
		$LYGroupTotal[$Level] = 0;
		$Level--;
	}
	$YPos-= $line_height;
	$LeftOvers = $PDF->addTextWrap($Left_Margin + (10 * ($Level + 1)), $YPos, 200, $FontSize, _('Total') . ' ' . $ParentGroups[$Level]);
	$LeftOvers = $PDF->addTextWrap($Left_Margin + 250, $YPos, 100, $FontSize, locale_number_format($GroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), 'right');
	$LeftOvers = $PDF->addTextWrap($Left_Margin + 350, $YPos, 100, $FontSize, locale_number_format($LYGroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), 'right');
	$ParentGroups[$Level] = '';
	$GroupTotal[$Level] = 0;
	$LYGroupTotal[$Level] = 0;
	$YPos-= $line_height;

	if ($SectionBalanceLY + $SectionBalance != 0) {
		$FontSize = 8;
		$PDF->setFont('', 'B');
		$LeftOvers = $PDF->addTextWrap($Left_Margin, $YPos, 200, $FontSize, $Sections[$Section]);
		$LeftOvers = $PDF->addTextWrap($Left_Margin + 250, $YPos, 100, $FontSize, locale_number_format($SectionBalance, $_SESSION['CompanyRecord']['decimalplaces']), 'right');
		$LeftOvers = $PDF->addTextWrap($Left_Margin + 350, $YPos, 100, $FontSize, locale_number_format($SectionBalanceLY, $_SESSION['CompanyRecord']['decimalplaces']), 'right');
		$YPos-= $line_height;
	}

	$YPos-= $line_height;

	$LeftOvers = $PDF->addTextWrap($Left_Margin, $YPos, 200, $FontSize, _('Check Total'));
	$LeftOvers = $PDF->addTextWrap($Left_Margin + 250, $YPos, 100, $FontSize, locale_number_format($CheckTotal, $_SESSION['CompanyRecord']['decimalplaces']), 'right');
	$LeftOvers = $PDF->addTextWrap($Left_Margin + 350, $YPos, 100, $FontSize, locale_number_format($LYCheckTotal, $_SESSION['CompanyRecord']['decimalplaces']), 'right');

	if ($ListCount == 0) { //UldisN
		$Title = _('Print Balance Sheet Error');
		include ('includes/header.php');
		prnMsg(_('There were no entries to print out for the selections specified'));
		echo '<br /><a href="', $RootPath, '/index.php">', _('Back to the menu'), '</a>';
		include ('includes/footer.php');
		exit;
	} else {
		$PDF->OutputD($_SESSION['DatabaseName'] . '_GL_Balance_Sheet_' . date('Y-m-d') . '.pdf');
		$PDF->__destruct();
	}
	exit;
} else {

	if (!isset($IsIncluded)) { // Runs normally if this script is NOT included in another.
		$ViewTopic = 'GeneralLedger';
		$BookMark = 'BalanceSheet';
		include ('includes/header.php');
		echo '<p class="page_title_text">
				<img class="page_title_icon" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/preview.png" title="', _('HTML View'), '" alt="', _('HTML View'), '" /> ', _('HTML View'), '
			</p>';
	}

	echo '<form method="post" action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '">';
	echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';
	echo '<input type="hidden" name="PeriodTo" value="', $_POST['PeriodTo'], '" />';

	$RetainedEarningsAct = $_SESSION['CompanyRecord']['retainedearnings'];

	$SQL = "SELECT lastdate_in_period FROM periods WHERE periodno='" . $_POST['PeriodTo'] . "'";
	$PrdResult = DB_query($SQL);
	$MyRow = DB_fetch_row($PrdResult);
	$BalanceDate = ConvertSQLDate($MyRow[0]);

	/*Calculate B/Fwd retained earnings */

	$SQL = "SELECT Sum(CASE WHEN chartdetails.period='" . $_POST['PeriodTo'] . "' THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END) AS accumprofitbfwd,
			Sum(CASE WHEN chartdetails.period='" . ($_POST['PeriodTo'] - 12) . "' THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END) AS lyaccumprofitbfwd
		FROM chartmaster
		INNER JOIN accountgroups
			ON chartmaster.groupcode = accountgroups.groupcode
			AND chartmaster.language = accountgroups.language
		INNER JOIN chartdetails
			ON chartmaster.accountcode= chartdetails.accountcode
		WHERE accountgroups.pandl=1
			AND chartmaster.language='" . $_SESSION['ChartLanguage'] . "'";

	$AccumProfitResult = DB_query($SQL, _('The accumulated profits brought forward could not be calculated by the SQL because'));

	$AccumProfitRow = DB_fetch_array($AccumProfitResult);
	/*should only be one row returned */

	$SQL = "SELECT accountgroups.sectioninaccounts,
			accountgroups.groupname,
			accountgroups.parentgroupname,
			chartdetails.accountcode,
			chartmaster.accountname,
			Sum(CASE WHEN chartdetails.period='" . $_POST['PeriodTo'] . "' THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END) AS balancecfwd,
			Sum(CASE WHEN chartdetails.period='" . ($_POST['PeriodTo'] - 12) . "' THEN chartdetails.bfwd + chartdetails.actual ELSE 0 END) AS lybalancecfwd
		FROM chartmaster
			INNER JOIN accountgroups
				ON chartmaster.groupcode = accountgroups.groupcode
				AND chartmaster.language = accountgroups.language
			INNER JOIN chartdetails
				ON chartmaster.accountcode= chartdetails.accountcode
			INNER JOIN glaccountusers
				ON glaccountusers.accountcode=chartmaster.accountcode
				AND glaccountusers.userid='" . $_SESSION['UserID'] . "'
				AND glaccountusers.canview=1
		WHERE accountgroups.pandl=0
			AND chartmaster.language='" . $_SESSION['ChartLanguage'] . "'
		GROUP BY accountgroups.groupcode,
			chartdetails.accountcode,
			chartmaster.accountname,
			accountgroups.parentgroupname,
			accountgroups.sequenceintb,
			accountgroups.sectioninaccounts
		ORDER BY accountgroups.sectioninaccounts,
			accountgroups.sequenceintb,
			accountgroups.groupcode,
			chartdetails.accountcode";

	$AccountsResult = DB_query($SQL, _('No general ledger accounts were returned by the SQL because'));

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
		$TableHeader = '<tr>
							<th>' . _('Account') . '</th>
							<th>' . _('Account Name') . '</th>
							<th colspan="2">' . $BalanceDate . '</th>
							<th colspan="2">' . _('Last Year') . '</th>
						</tr>';
	} else {
		/*summary */
		$TableHeader = '<tr>
							<th colspan="2"></th>
							<th colspan="2">' . $BalanceDate . '</th>
							<th colspan="2">' . _('Last Year') . '</th>
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

	echo $TableHeader;
	$j = 0; //row counter
	while ($MyRow = DB_fetch_array($AccountsResult)) {
		$AccountBalance = $MyRow['balancecfwd'];
		$LYAccountBalance = $MyRow['lybalancecfwd'];

		if ($MyRow['accountcode'] == $RetainedEarningsAct) {
			$AccountBalance+= $AccumProfitRow['accumprofitbfwd'];
			$LYAccountBalance+= $AccumProfitRow['lyaccumprofitbfwd'];
		}

		if ($MyRow['groupname'] != $ActGrp and $ActGrp != '') {
			if ($MyRow['parentgroupname'] != $ActGrp) {
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
					echo '<tr>
							<td colspan="2"><I>', $ParentGroups[$Level], '</I></td>
							<td class="number">', locale_number_format($GroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
							<td></td>
							<td class="number">', locale_number_format($LYGroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
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

				echo '<tr>
						<td colspan="2">', $ParentGroups[$Level], '</td>
						<td class="number">', locale_number_format($GroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
						<td></td>
						<td class="number">', locale_number_format($LYGroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
					</tr>';

				$GroupTotal[$Level] = 0;
				$LYGroupTotal[$Level] = 0;
				$ParentGroups[$Level] = '';
				++$j;
			}
		}
		if ($MyRow['sectioninaccounts'] != $Section) {

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

				echo '<tr>
						<td colspan="3"><h2>', $Sections[$Section], '</h2></td>
						<td class="number">', locale_number_format($SectionBalance, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
						<td></td>
						<td class="number">', locale_number_format($SectionBalanceLY, $_SESSION['CompanyRecord']['decimalplaces']), '</td>
					</tr>';
				++$j;
			}
			$SectionBalanceLY = 0;
			$SectionBalance = 0;
			$Section = $MyRow['sectioninaccounts'];

			if ($_POST['ShowDetail'] == 'Detailed') {
				echo '<tr>
						<td colspan="6"><h1>', $Sections[$MyRow['sectioninaccounts']], '</h1></td>
					</tr>';
			}
		}

		if ($MyRow['groupname'] != $ActGrp) {

			if ($ActGrp != '' and $MyRow['parentgroupname'] == $ActGrp) {
				$Level++;
			}

			if ($_POST['ShowDetail'] == 'Detailed') {
				$ActGrp = $MyRow['groupname'];
				echo '<tr>
						<td colspan="6"><h3>', $MyRow['groupname'], '</h3></td>
					</tr>';
				echo $TableHeader;
			}
			$GroupTotal[$Level] = 0;
			$LYGroupTotal[$Level] = 0;
			$ActGrp = $MyRow['groupname'];
			$ParentGroups[$Level] = $MyRow['groupname'];
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
		echo '<tr>
				<td colspan="2"><I>', $ParentGroups[$Level], '</I></td>
				<td class="number">', locale_number_format($GroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
				<td></td>
				<td class="number">', locale_number_format($LYGroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
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

	echo '<tr>
			<td colspan="2">', $ParentGroups[$Level], '</td>
			<td class="number">', locale_number_format($GroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
			<td></td>
			<td class="number">', locale_number_format($LYGroupTotal[$Level], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
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

	echo '<tr>
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

	echo '<tr>
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
			<input type="submit" name="SelectADifferentPeriod" value="', _('Select A Different Balance Date'), '" />
		</div>';
	echo '</form>';
}
if (!isset($IsIncluded)) { // Runs normally if this script is NOT included in another.
	include ('includes/footer.php');
}
?>