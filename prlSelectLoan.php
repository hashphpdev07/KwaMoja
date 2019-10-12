<?php
/* $Revision: 1.0 $ */

$PageSecurity = 10;
include ('includes/session.php');
$Title = _('View Employee Loan Master File');

include ('includes/header.php');

if (isset($_GET['SelectedID'])) {
	$SelectedID = $_GET['SelectedID'];
} elseif (isset($_POST['SelectedID'])) {
	$SelectedID = $_POST['SelectedID'];
}

if (!isset($SelectedID)) {
	$SQL = "SELECT prlloanfile.counterindex,
			prlloanfile.loanfileid,
			prlloanfile.loanfiledesc,
			prlloanfile.employeeid,
			prlloanfile.loantableid,
			prlloanfile.loanamount,
			prlloanfile.amortization,
			prlloanfile.startdeduction,
			prlloantable.loantableid,
			prlloantable.loantabledesc,
			prlemployeemaster.employeeid,
			prlemployeemaster.lastname,
			prlemployeemaster.firstname
		FROM prlloanfile,prlloantable,prlemployeemaster
		WHERE prlloanfile.loantableid = prlloantable.loantableid
		AND prlloanfile.employeeid = prlemployeemaster.employeeid
		ORDER BY counterindex";
	$ErrMsg = _('The employee record could not be retrieved because');
	$Result = DB_query($SQL, $ErrMsg);

	echo '<table border=1>';
	echo "<tr>
		<th>" . _('Index ') . "</th>
		<th>" . _('Ref ID') . "</th>
		<th>" . _('Loan Description ') . "</th>
		<th>" . _('Start of Deduction') . "</th>
		<th>" . _('Employee Name') . "</th>
		<th>" . _('Loan Type') . "</th>
		<th>" . _('Loan Amount') . "</th>
		<th>" . _('Amortization') . "</th>
	</tr>";
	$k = 0;
	while ($MyRow = DB_fetch_array($Result)) {

		//alternateTableRowColor($k);
		if ($k == 1) {
			echo "<TR BGCOLOR='#CCCCCC'>";
			$k = 0;
		} else {
			echo "<TR BGCOLOR='#EEEEEE'>";
			$k++;
		}
		echo "<td>" . $MyRow["counterindex"] . "</td>
				<td>" . $MyRow["loanfileid"] . "</td>
				<td>" . $MyRow["loanfiledesc"] . "</td>
				<td>" . $MyRow["startdeduction"] . "</td>
				<td>" . $MyRow["employeeid"] . " - " . $MyRow["lastname"] . ", " . $MyRow["firstname"] . "</td>
				<td>" . $MyRow["loantabledesc"] . "</td>
				<td>" . $MyRow["loanamount"] . "</td>
				<td>" . $MyRow["amortization"] . "</td>
				<td><a href=" . $RootPath . '/prlLoanFile.php?' . SID . '&SelectedID=' . $MyRow[0] . '>' . _('Edit') . "</td>
				<td><a href=" . $RootPath . '/prlLoanFile.php??' . SID . '&SelectedID=' . $MyRow[0] . '"&delete=1">' . _('Delete') . "</td></tr>";
	} //END WHILE LIST LOOP
	
}

echo '</table>';
//end of ifs and buts!
include ('includes/footer.php');
?>