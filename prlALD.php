<?php
/* $Revision: 1.0 $ */
//include_once('includes/printerrmsg.php');
$PageSecurity = 10;
include ('includes/session.php');
include ('includes/prlFunctions.php');
$Title = _('Employee Loan Deduction Entry');
include ('includes/header.php');
//echo "<A HREF='" . $RootPath . '/prlALD.php?' . SID . "'>" . _('Add Another Loan Entry') . '</A><BR>';
if (isset($_GET['LoanFileId'])) {
	$SelectedID = $_GET['LoanFileId'];
} elseif (isset($_POST['LoanFileId'])) {
	$SelectedID = $_POST['LoanFileId'];
}
//printerr($_SESSION['TranDate']);
if (!isset($_SESSION['TranDate'])) {
	$_SESSION['TranDate'] = date($_SESSION['DefaultDateFormat']);
}

echo '<p class="page_title_text" >
		<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/money_add.png" title="', $Title, '" alt="', $Title, '" />', ' ', $Title, '
	</p>';

if (isset($_POST['insert']) or isset($_POST['update'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;
	$LoanBal = $_POST['LoanAmount'] - $_POST['YTDDeduction'];
	if ($LoanBal < 0) {
		$InputError = 1;
		prnMsg(_('Can not post. Total Deduction is greater that Loan Amount by') . ' ' . $LoanBal, 'error');
	}

	/* actions to take once the user has clicked the submit button
	 ie the page has called itself with some user input */

	//first off validate inputs sensible
	

	if ($InputError != 1) {
		//printerr($_POST['LoanTableID']);
		$SQL_LoanDate = FormatDateForSQL($_POST['LoanDate']);
		$SQL_StartDeduction = FormatDateForSQL($_POST['StartDeduction']);
		if (isset($_POST['update'])) {
			$SQL = "UPDATE prlloanfile SET loanfiledesc='" . DB_escape_string($_POST['LoanFileDesc']) . "',
											employeeid='" . DB_escape_string($_POST['EmployeeID']) . "',
											loandate='" . $SQL_LoanDate . "',
											loantableid='" . DB_escape_string($_POST['LoanTableID']) . "',
											loanamount='" . DB_escape_string($_POST['LoanAmount']) . "',
											amortization='" . DB_escape_string($_POST['Amortization']) . "',
											startdeduction='" . $SQL_StartDeduction . "',
											ytddeduction='" . $_POST['YTDDeduction'] . "'
										WHERE loanfileid='" . DB_escape_string($_POST['LoanFileId']) . "'";

			$ErrMsg = _('The employee loan') . ' ' . $_POST['LoanFileDesc'] . ' ' . _('could not be updated because');
			$DbgMsg = _('The SQL that was used to update the employee loan but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);

			prnMsg(_('The employee loan for') . ' ' . $_POST['LoanFileDesc'] . ' ' . _('has been updated'), 'success');

			unset($_POST['LoanFileId']);
			unset($_POST['LoanFileDesc']);
			unset($_POST['EmployeeID']);
			unset($_POST['LoanDate']);
			unset($_POST['LoanTableID']);
			unset($_POST['LoanAmount']);
			unset($_POST['Amortization']);
			unset($_POST['StartDeduction']);
		} elseif (isset($_POST['insert'])) { //its a new employee
			$SQL = "INSERT INTO prlloanfile (loanfileid,
												loanfiledesc,
												employeeid,
												loandate,
												loantableid,
												loanamount,
												amortization,
												startdeduction,
												loanbalance)
											VALUES (
												'" . DB_escape_string($_POST['LoanFileId']) . "',
												'" . DB_escape_string($_POST['LoanFileDesc']) . "',
												'" . DB_escape_string($_POST['EmployeeID']) . "',
												'" . $SQL_LoanDate . "',
												'" . DB_escape_string($_POST['LoanTableID']) . "',
												'" . DB_escape_string($_POST['LoanAmount']) . "',
												'" . DB_escape_string($_POST['Amortization']) . "',
												'" . $SQL_StartDeduction . "',
												'" . DB_escape_string($_POST['LoanAmount']) . "'
											)";

			$ErrMsg = _('The employee loan') . ' ' . $_POST['LoanFileDesc'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the employee loan but failed was');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg);

			prnMsg(_('A new employee loan for') . ' ' . $_POST['LoanFileDesc'] . ' ' . _('has been added to the database'), 'success');

			unset($_POST['LoanFileId']);
			unset($_POST['LoanFileDesc']);
			unset($_POST['EmployeeID']);
			unset($_POST['LoanDate']);
			unset($_POST['LoanTableID']);
			unset($_POST['LoanAmount']);
			unset($_POST['Amortization']);
			unset($_POST['StartDeduction']);
		}

	} else {

		prnMsg(_('Validation failed') . _('no updates or deletes took place'), 'warn');

	}

} elseif (isset($_POST['delete']) and $_POST['delete'] != '') {
	//delete
	
} //end of (isset($_POST['submit']))
$SQL = "SELECT loanfileid,
				loanfiledesc,
				prlloanfile.employeeid,
				prlemployeemaster.firstname,
				prlemployeemaster.lastname,
				loandate,
				prlloantable.loantabledesc,
				loanamount,
				amortization,
				startdeduction,
				loanbalance
			FROM prlloanfile
			INNER JOIN prlemployeemaster
				ON prlemployeemaster.employeeid=prlloanfile.employeeid
			INNER JOIN prlloantable
				ON prlloantable.loantableid=prlloanfile.loantableid";
$Result = DB_query($SQL);

if (DB_num_rows($Result) > 0) {
	echo '<table>
			<tr>
				<th>', _('Loan File ID'), '</th>
				<th>', _('Loan File Description'), '</th>
				<th>', _('Employee'), '</th>
				<th>', _('Loan Date'), '</th>
				<th>', _('Loan Type'), '</th>
				<th>', _('Loan Amount'), '</th>
				<th>', _('Amortisation'), '</th>
				<th>', _('Start Deduction On'), '</th>
				<th>', _('Balance'), '</th>
				<th></th>
			</tr>';

	while ($MyRow = DB_fetch_array($Result)) {
		echo '<tr class="striped_row">
				<td>', $MyRow['loanfileid'], '</td>
				<td>', $MyRow['loanfiledesc'], '</td>
				<td>', $MyRow['firstname'], ' ', $MyRow['lastname'], ' (', $MyRow['employeeid'], ')</td>
				<td>', ConvertSQLDate($MyRow['loandate']), '</td>
				<td>', $MyRow['loantabledesc'], '</td>
				<td class="number">', $MyRow['loanamount'], '</td>
				<td class="number">', $MyRow['amortization'], '</td>
				<td>', $MyRow['startdeduction'], '</td>
				<td class="number">', $MyRow['loanbalance'], '</td>
				<td><a href="', basename(__FILE__), '?LoanFileId=', $MyRow['loanfileid'], '&Edit=Yes">', _('Edit'), '</a></td>
			</tr>';
	}
	echo '</table>';
}

if (!isset($SelectedID)) {
	$_POST['LoanFileDesc'] = '';
	$_POST['EmployeeID'] = '';
	$_POST['LoanDate'] = Date($_SESSION['DefaultDateFormat']);
	$_POST['LoanTableID'] = '';
	$_POST['LoanAmount'] = 0;
	$_POST['Amortization'] = 0;
	$_POST['StartDeduction'] = Date($_SESSION['DefaultDateFormat']);
	$_POST['YTDDeduction'] = 0;

	echo '<form action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '" method="post">';
	echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';

	echo '<fieldset>
			<legend>', _('Create new employee loan file'), '</legend>';
	echo '<field>
			<label for="LoanFileId">', _('Loan Ref'), ':</label>
			<input type="text" name="LoanFileId" size="11" maxlength="10" />
		</field>';
} else {
	$SQL = "SELECT loanfileid,
					loanfiledesc,
					employeeid,
					loandate,
					loantableid,
					loanamount,
					amortization,
					startdeduction,
					ytddeduction
				FROM prlloanfile
				WHERE loanfileid='" . $SelectedID . "'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);
	$_POST['LoanFileDesc'] = $MyRow['loanfiledesc'];
	$_POST['EmployeeID'] = $MyRow['employeeid'];
	$_POST['LoanDate'] = $MyRow['loandate'];
	$_POST['LoanTableID'] = $MyRow['loantableid'];
	$_POST['LoanAmount'] = $MyRow['loanamount'];
	$_POST['Amortization'] = $MyRow['amortization'];
	$_POST['StartDeduction'] = $MyRow['startdeduction'];
	$_POST['YTDDeduction'] = $MyRow['ytddeduction'];

	echo '<form action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '" method="post">';
	echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';

	echo '<input type="hidden" name="LoanFileId" value="', $SelectedID, '" />';

	echo '<fieldset>
			<legend>', _('Edit employee loan file'), '</legend>';
	echo '<field>
			<label for="LoanFileId">', _('Loan Ref'), ':</label>
			<div class="fieldtext">', $SelectedID, '</div>
		</field>';
}

echo '<field>
		<label for="LoanFileDesc">', _('Description'), ':</label>
		<input type="text" name="LoanFileDesc" size="42" maxlength="40" value="', $_POST['LoanFileDesc'], '" />
	</field>';

$SQL = "SELECT employeeid, lastname, firstname FROM prlemployeemaster ORDER BY lastname, firstname";
$Result = DB_query($SQL);
echo '<field>
		<label for="EmployeeID">', _('Employee Name'), ':</label>
		<select name="EmployeeID">';
while ($MyRow = DB_fetch_array($Result)) {
	if ($_POST['EmployeeID'] == $MyRow['employeeid']) {
		echo '<option selected="selected" value=', $MyRow['employeeid'], '>', $MyRow['lastname'], ', ', $MyRow['firstname'], '</option>';
	} else {
		echo '<option value=', $MyRow['employeeid'], '>', $MyRow['lastname'], ', ', $MyRow['firstname'], '</option>';
	}
} //end while loop
echo '</select>
</field>';

echo '<field>
		<label for="LoanDate">', _('Loan Date'), ':</label>
		<input type="text" class="date" name="LoanDate" maxlength="10" size="11" value="', $_POST['LoanDate'], '" />
	</field>';

echo '<field>
		<label for="LoanTableID">', _('Loan Type'), ':</label>
		<select name="LoanTableID">';
$SQL = "SELECT loantableid, loantabledesc FROM prlloantable";
$Result = DB_query($SQL);
while ($MyRow = DB_fetch_array($Result)) {
	if ($_POST['LoanTableID'] == $MyRow['loantableid']) {
		echo '<option selected="selected" value=', $MyRow['loantableid'], '>', $MyRow['loantabledesc'], '</option>';
	} else {
		echo '<option value=', $MyRow['loantableid'], '>', $MyRow['loantabledesc'], '</option>';
	}
} //end while loop
echo '</select>
</field>';

echo '<field>
		<label for="LoanAmount">', _('LoanAmount'), '</label>
		<input type="text" class="number" name="LoanAmount" size="14" maxlength="12" value="', $_POST['LoanAmount'], '" />
	</field>';

echo '<field>
		<label for="Amortization">', _('Amortization'), ':</label>
		<input type="text" class="number" name="Amortization" size="14" maxlength="12" value="', $_POST['Amortization'], '" />
	</field>';

echo '<field>
		<label for="YTDDeduction">', _('Already Deducted'), ':</label>
		<input type="text" class="number" name="YTDDeduction" size="14" maxlength="12" value="', $_POST['YTDDeduction'], '" />
	</field>';

echo '<field>
		<label for="StartDeduction">', _('Start Deduction'), ':</label>
		<input type="text" class="date" name="StartDeduction" maxlength="10" size="11" value="', $_POST['StartDeduction'], '" />
	</field>';

echo '</fieldset>';

if (!isset($SelectedID)) {
	echo '<div class="centre">
			<input type="submit" name="insert" value="', _('Insert New Employee Loan'), '">
		</div>';
} else {
	echo '<div class="centre">
			<input type="submit" name="update" value="', _('Update Employee Loan'), '">
		</div>';
}

echo '</form>';

include ('includes/footer.php');
?>