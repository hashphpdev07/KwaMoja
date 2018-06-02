<?php
$PageSecurity = 1;
require_once ('includes/session.php');

$SearchAnswers = array();
$i = 0;
$TotalRows = 0;

/* If no exact match then check for a matching telephone number */
if ($_POST['Telephone'] != '') {
	$SQL = "SELECT pid,
					name_first,
					name_middle,
					name_last,
					phone_1_nr,
					sex,
					date_birth
				FROM care_person
				WHERE REPLACE(phone_1_nr, ' ', '')='" . $_POST['Telephone'] . "'";
	$Result = DB_query($SQL);
	while ($MyRow = DB_fetch_array($Result)) {
		$SearchAnswers[$i]['PID'] = $MyRow['pid'];
		$SearchAnswers[$i]['FirstName'] = $MyRow['name_first'];
		$SearchAnswers[$i]['MiddleName'] = $MyRow['name_middle'];
		$SearchAnswers[$i]['LastName'] = $MyRow['name_last'];
		$SearchAnswers[$i]['Telephone'] = $MyRow['phone_1_nr'];
		if ($MyRow['sex'] == 'm') {
			$SearchAnswers[$i]['Gender'] = _('Male');
		} else {
			$SearchAnswers[$i]['Gender'] = _('Female');
		}
		$SearchAnswers[$i]['DOB'] = $MyRow['date_birth'];
		++$i;
	}
	$TotalRows = $i;
}

/* If no exact match then check for a nearest sounding match */
if ($TotalRows == 0 and strlen($_POST['FirstName']) > 2) {
	if (strlen($_POST['LastName']) > 2) {
		$SQL = "SELECT pid,
						name_first,
						name_middle,
						name_last,
						phone_1_nr,
						sex,
						date_birth
					FROM care_person
					WHERE SOUNDEX(CONCAT(name_first, ' ', name_last))=SOUNDEX('" . $_POST['FirstName'] . ' ' . $_POST['LastName'] . "')
					ORDER BY LEVENSHTEIN(CONCAT(name_first, ' ', name_last), '" . $_POST['FirstName'] . ' ' . $_POST['LastName'] . "')
					LIMIT 15";
	} else {
		$SQL = "SELECT pid,
						name_first,
						name_middle,
						name_last,
						phone_1_nr,
						sex,
						date_birth
					FROM care_person
					WHERE SOUNDEX(name_first)=SOUNDEX('" . $_POST['FirstName'] . "')
					ORDER BY LEVENSHTEIN(name_first, '" . $_POST['FirstName'] . "')
					LIMIT 15";
	}
	$Result = DB_query($SQL);
	while ($MyRow = DB_fetch_array($Result)) {
		$SearchAnswers[$i]['PID'] = $MyRow['pid'];
		$SearchAnswers[$i]['FirstName'] = $MyRow['name_first'];
		$SearchAnswers[$i]['MiddleName'] = $MyRow['name_middle'];
		$SearchAnswers[$i]['LastName'] = $MyRow['name_last'];
		$SearchAnswers[$i]['Telephone'] = $MyRow['phone_1_nr'];
		if ($MyRow['sex'] == 'm') {
			$SearchAnswers[$i]['Gender'] = _('Male');
		} else {
			$SearchAnswers[$i]['Gender'] = _('Female');
		}
		$SearchAnswers[$i]['DOB'] = $MyRow['date_birth'];
		++$i;
	}
}

foreach ($SearchAnswers as $Answer) {
	echo '<input type="hidden" id="fst' . $Answer['PID'] . '" value="' . $Answer['FirstName'] . '" />';
	echo '<input type="hidden" id="lst' . $Answer['PID'] . '" value="' . $Answer['LastName'] . '" />';
	echo '<input type="hidden" id="mid' . $Answer['PID'] . '" value="' . $Answer['MiddleName'] . '" />';
	echo '<tr>
			<td><input onclick="FillForm(this)" type="radio" name="Patient" value="' . $Answer['PID'] . '" /></td>
			<td>' . $Answer['PID'] . '</td>
			<td>' . $Answer['FirstName'] . ' ' . $Answer['LastName'] . '</td>
			<td id="Tel' . $Answer['PID'] . '">' . $Answer['Telephone'] . '</td>
			<td id="Sex' . $Answer['PID'] . '">' . $Answer['Gender'] . '</td>
			<td id="Dob' . $Answer['PID'] . '">' . ConvertSQLDate($Answer['DOB']) . '</td>
		</tr>';
}

?>