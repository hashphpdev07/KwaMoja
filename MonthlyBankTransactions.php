<?php
include ('includes/session.php');
$Title = _('Monthly Bank Transactions Inquiry');
include ('includes/header.php');

echo '<p class="page_title_text">
		<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/money_add.png" title="', _('Search'), '" alt="" />', ' ', $Title, '
	</p>';

if (!isset($_POST['Show'])) {
	echo '<form action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '" method="post">';
	echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';

	echo '<fieldset>
			<legend>', _('Report Criteria'), '</legend>';

	$SQL = "SELECT 	bankaccountname,
					bankaccounts.accountcode,
					bankaccounts.currcode
				FROM bankaccounts
				INNER JOIN chartmaster
					ON bankaccounts.accountcode=chartmaster.accountcode
				INNER JOIN bankaccountusers
					ON bankaccounts.accountcode=bankaccountusers.accountcode
				WHERE bankaccountusers.userid = '" . $_SESSION['UserID'] . "'
					AND chartmaster.language='" . $_SESSION['ChartLanguage'] . "'";

	$ErrMsg = _('The bank accounts could not be retrieved because');
	$DbgMsg = _('The SQL used to retrieve the bank accounts was');
	$AccountsResults = DB_query($SQL, $ErrMsg, $DbgMsg);

	echo '<field>
			<label for="BankAccount">', _('Bank Account'), ':</label>
			<select name="BankAccount" autofocus="autofocus">';

	if (DB_num_rows($AccountsResults) == 0) {
		echo '</select>
			</field>
		</fieldset>';
		prnMsg(_('Bank Accounts have not yet been defined. You must first') . ' <a href="' . $RootPath . '/BankAccounts.php">' . _('define the bank accounts') . '</a> ' . _('and general ledger accounts to be affected'), 'warn');
		include ('includes/footer.php');
		exit;
	} else {
		while ($MyRow = DB_fetch_array($AccountsResults)) {
			/*list the bank account names */
			if (!isset($_POST['BankAccount']) and $MyRow['currcode'] == $_SESSION['CompanyRecord']['currencydefault']) {
				$_POST['BankAccount'] = $MyRow['accountcode'];
			}
			if (isset($_POST['BankAccount']) and $_POST['BankAccount'] == $MyRow['accountcode']) {
				echo '<option selected="selected" value="', $MyRow['accountcode'], '">', $MyRow['bankaccountname'], ' - ', $MyRow['currcode'], '</option>';
			} else {
				echo '<option value="', $MyRow['accountcode'], '">', $MyRow['bankaccountname'], ' - ', $MyRow['currcode'], '</option>';
			}
		}
		echo '</select>
			<fieldhelp>', _('Select the bank account to report on.'), '</fieldhelp>
		</field>';
	}
	if (!isset($_POST['FromPeriod'])) {
		$_POST['FromPeriod'] = GetPeriod(date($_SESSION['DefaultDateFormat']));
	}
	$NextYear = date('Y-m-d', strtotime('+1 Year'));
	$SQL = "SELECT periodno,
					lastdate_in_period
				FROM periods
				WHERE lastdate_in_period < '" . $NextYear . "'
				ORDER BY periodno DESC";
	$Periods = DB_query($SQL);

	echo '<field>
			<label for="FromPeriod">', _('Select Period'), ':</label>
			<select name="FromPeriod">';
	while ($MyRow = DB_fetch_array($Periods)) {
		if (isset($_POST['FromPeriod']) and $_POST['FromPeriod'] == $MyRow['periodno']) {
			echo '<option selected="selected" value="', $MyRow['periodno'], '">', MonthAndYearFromSQLDate($MyRow['lastdate_in_period']), '</option>';
		} else {
			echo '<option value="', $MyRow['periodno'], '">', MonthAndYearFromSQLDate($MyRow['lastdate_in_period']), '</option>';
		}
	}

	echo '</select>
		<fieldhelp>', _('Select the month to report on'), '</fieldhelp>
	</field>';

	echo '</fieldset>';

	echo '<div class="centre">
			<input type="submit" name="Show" value="', _('Show transactions'), '" />
		</div>
	</form>';
} else {

	$OpeningBalanceSQL = "SELECT SUM(amount) AS bfwd
						FROM gltrans
						WHERE account='" . $_POST['BankAccount'] . "'
							AND periodno<'" . $_POST['FromPeriod'] . "'";
	$BalancesResult = DB_query($OpeningBalanceSQL);
	$BalancesRow = DB_fetch_array($BalancesResult);
	$OpeningBalance = $BalancesRow['bfwd'];
	$PeriodBalanceSQL = "SELECT SUM(amount) AS actual
						FROM gltrans
						WHERE account='" . $_POST['BankAccount'] . "'
							AND periodno='" . $_POST['FromPeriod'] . "'";
	$BalancesResult = DB_query($PeriodBalanceSQL);
	$BalancesRow = DB_fetch_array($BalancesResult);
	$ClosingBalance = $OpeningBalance + $BalancesRow['actual'];

	$SQL = "SELECT 	bankaccountname,
					bankaccounts.currcode,
					currencies.decimalplaces
			FROM bankaccounts
			INNER JOIN currencies
				ON bankaccounts.currcode = currencies.currabrev
			WHERE bankaccounts.accountcode='" . $_POST['BankAccount'] . "'";
	$BankResult = DB_query($SQL, _('Could not retrieve the bank account details'));

	$SQL = "SELECT 	banktrans.currcode,
					banktrans.amount,
					banktrans.amountcleared,
					banktrans.functionalexrate,
					banktrans.exrate,
					banktrans.banktranstype,
					banktrans.transdate,
					banktrans.ref,
					banktrans.transno,
					banktrans.chequeno,
					bankaccounts.bankaccountname,
					systypes.typename,
					systypes.typeid
				FROM banktrans
				INNER JOIN gltrans
				ON banktrans.transno=gltrans.typeno
					AND banktrans.type=gltrans.type
					AND banktrans.bankact=gltrans.account
				INNER JOIN bankaccounts
				ON banktrans.bankact=bankaccounts.accountcode
				INNER JOIN systypes
				ON banktrans.type=systypes.typeid
				WHERE bankact='" . $_POST['BankAccount'] . "'
					AND periodno='" . $_POST['FromPeriod'] . "'
				ORDER BY banktrans.type,
						banktrans.transdate";
	$Result = DB_query($SQL);
	if (DB_num_rows($Result) == 0) {
		prnMsg(_('There are no transactions for this account in the date range selected'), 'info');
	} else {
		$SQL = "SELECT lastdate_in_period
					FROM periods
					WHERE periodno = '" . $_POST['FromPeriod'] . "'";
		$Periods = DB_query($SQL);
		$PeriodRow = DB_fetch_array($Periods);
		$EndDate = ConvertSQLDate($PeriodRow['lastdate_in_period']);

		$BankDetailRow = DB_fetch_array($BankResult);
		echo '<table>
				<tr>
					<th colspan="9">
						<h3>', _('Account Transactions For'), ' ', $BankDetailRow['bankaccountname'], ' ', _('and Month Ending'), ' ', $EndDate, '
							<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/printer.png" class="PrintIcon" title="', _('Print'), '" alt="" onclick="window.print();" />
						</h3>
					</th>
				</tr>
				<tr>
					<th>', ('Date'), '</th>
					<th>', _('Transaction type'), '</th>
					<th>', _('Cheque Number'), '</th>
					<th>', _('Reference'), '</th>
					<th>', _('Supplier/Customer'), '</th>
					<th>', _('Amount in'), ' ', $BankDetailRow['currcode'], '</th>
					<th>', _('Matched'), '</th>
				</tr>';

		echo '<tr>
				<td colspan="4"></td>
				<td>', _('Opening Balance'), '</td>
				<td class="number">', locale_number_format($OpeningBalance, $BankDetailRow['decimalplaces']), '</td>
			</tr>';

		$ReceiptsTotal = 0;
		$PaymentsTotal = 0;
		$LastType = 0;

		while ($MyRow = DB_fetch_array($Result)) {

			if ($MyRow['typeid'] == 22) {
				$CreditorPaymentSQL = "SELECT supplierno,
												suppname
											FROM supptrans
											INNER JOIN suppliers
												ON supptrans.supplierno=suppliers.supplierid
											WHERE type=22
												AND transno='" . $MyRow['transno'] . "'";
				$CreditorPaymentResult = DB_query($CreditorPaymentSQL);
				$CreditorPaymentRow = DB_fetch_array($CreditorPaymentResult);
				$CustomerSupplier = $CreditorPaymentRow['supplierno'] . ' - ' . $CreditorPaymentRow['suppname'];
			} else if ($MyRow['typeid'] == 12) {
				$DebtorPaymentSQL = "SELECT debtortrans.debtorno,
											name
										FROM debtortrans
										INNER JOIN debtorsmaster
											ON debtortrans.debtorno=debtorsmaster.debtorno
										WHERE type=12
											AND transno='" . $MyRow['transno'] . "'";
				$DebtorPaymentResult = DB_query($DebtorPaymentSQL);
				$DebtorPaymentRow = DB_fetch_array($DebtorPaymentResult);
				$CustomerSupplier = $DebtorPaymentRow['debtorno'] . ' - ' . $DebtorPaymentRow['name'];
			} else {
				$CustomerSupplier = '';
			}

			if ($MyRow['typeid'] == 12 or $MyRow['typeid'] == 2) {
				$ReceiptsTotal+= $MyRow['amount'];
			} else {
				$PaymentsTotal+= $MyRow['amount'];
			}

			if ($MyRow['amount'] == $MyRow['amountcleared']) {
				$Matched = _('Yes');
			} else {
				$Matched = _('No');
			}
			if (($LastType == 12 or $LastType == 2) and ($MyRow['typeid'] == 22 or $MyRow['typeid'] == 1)) {
				echo '<tr>
						<td colspan="4"></td>
						<td>', _('Total Receipts'), '</td>
						<td class="number">', locale_number_format($ReceiptsTotal, $BankDetailRow['decimalplaces']), '</td>
					</tr>';
			}

			echo '<tr class="striped_row">
					<td>', ConvertSQLDate($MyRow['transdate']), '</td>
					<td>', $MyRow['typename'], '</td>
					<td>', $MyRow['chequeno'], '</td>
					<td>', $MyRow['ref'], '</td>
					<td>', $CustomerSupplier, '</td>
					<td class="number">', locale_number_format($MyRow['amount'], $BankDetailRow['decimalplaces']), '</td>
					<td class="number">', $Matched, '</td>
				</tr>';
			$LastType = $MyRow['typeid'];
		}
		echo '<tr>
				<td colspan="4"></td>
				<td>', _('Total Payments'), '</td>
				<td class="number">', locale_number_format($PaymentsTotal, $BankDetailRow['decimalplaces']), '</td>
			</tr>';

		echo '<tr>
				<td colspan="4"></td>
				<td>', _('Closing Balance'), '</td>
				<td class="number">', locale_number_format($ClosingBalance, $BankDetailRow['decimalplaces']), '</td>
			</tr>';
		echo '</table>';
	} //end if no bank trans in the range to show
	echo '<form action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '" method="post">';
	echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';
	echo '<div class="centre">
			<input type="submit" name="Return" value="', _('Select Another Date'), '" />
		</div>';
	echo '</form>';
}
include ('includes/footer.php');

?>