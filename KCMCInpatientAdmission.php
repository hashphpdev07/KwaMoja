<?php
include ('includes/session.php');
$Title = _('Admit Inpatient');
include ('includes/header.php');

if (isset($_POST['SelectedPatient'])) {
	$SelectedPatient = $_POST['SelectedPatient'];
} elseif (isset($_GET['SelectedPatient'])) {
	$SelectedPatient = $_GET['SelectedPatient'];
}

if (isset($_POST['Create'])) {

	$SQL = "SELECT pid FROM care_person WHERE hospital_file_nr='" . $SelectedPatient . "'";
	$Result = DB_query($SQL);
	$MyPIDRow = DB_fetch_array($Result);

	$SQL = "INSERT INTO care_encounter (pid,
										encounter_date,
										encounter_class_nr,
										referrer_diagnosis,
										referrer_dr,
										referrer_recom_therapy,
										referrer_notes,
										triage,
										admit_type,
										in_ward,
										current_ward_nr,
										status,
										insurance_firm_id,
										insurance_nr,
										modify_id,
										modify_time,
										create_id,
										create_time
									) VALUES (
										'" . $MyPIDRow['pid'] . "',
										NOW(),
										1,
										'" . $_POST['Diagnosis'] . "',
										'" . $_POST['ReferredBy'] . "',
										'" . $_POST['Therapy'] . "',
										'" . $_POST['ReferrerNotes'] . "',
										'" . $_POST['TriageCode'] . "',
										'" . $_POST['AdmissionType'] . "',
										1,
										'" . $_POST['Ward'] . "',
										'in_dept',
										'" . $_POST['Insurance'] . "',
										'" . $_POST['InsuranceNo'] . "',
										'" . $_SESSION['UserID'] . "',
										NOW(),
										'" . $_SESSION['UserID'] . "',
										NOW()
									)";
	$ErrMsg = _('There was a problem inserting the encounter record because');
	$DbgMsg = _('The SQL used to insert the encounter record was');
	$Result = DB_query($SQL, $ErrMsg, $DbgMsg);

	prnMsg(_('The patient was successfully admitted.'), 'success');
	unset($SelectedPatient);
	unset($_POST['Diagnosis']);
	unset($_POST['ReferredBy']);
	unset($_POST['Therapy']);
	unset($_POST['ReferrerNotes']);
	unset($_POST['TriageCode']);
	unset($_POST['AdmissionType']);
	unset($_POST['Department']);
	unset($_POST['Insurance']);
	unset($_POST['InsuranceNo']);

}

if (!isset($SelectedPatient)) {
	echo '<p class="page_title_text">
			<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/magnifier.png" title="', _('Search'), '" alt="" />', _('Search For Patient'), '
		</p>';

	echo '<form action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '" method="post">';
	echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';

	if (!isset($_POST['FileNumberSearch'])) {
		$_POST['FileNumberSearch'] = '';
	}
	if (!isset($_POST['NameSearch'])) {
		$_POST['NameSearch'] = '';
	}
	if (!isset($_POST['AddressSearch'])) {
		$_POST['AddressSearch'] = '';
	}
	if (!isset($_POST['PhoneSearch'])) {
		$_POST['PhoneSearch'] = '';
	}

	echo '<fieldset>
			<legend class="search">', _('Search for patient details'), '</legend>
			<field>
				<label for="FileNumberSearch">', _('File number'), '</label>
				<input type="search" autofocus="autofocus" name="FileNumberSearch" value="', $_POST['FileNumberSearch'], '" />
				<fieldhelp>', _('Enter all or part of the patients file number if it is known'), '</fieldhelp>
			</field>
			<field>
				<label for="NameSearch">', _('Patients name'), '</label>
				<input type="search" autofocus="autofocus" name="NameSearch" value="', $_POST['NameSearch'], '" />
				<fieldhelp>', _('Enter all or part of the patients name if it is known'), '</fieldhelp>
			</field>
			<field>
				<label for="AddressSearch">', _('Patients Address'), '</label>
				<input type="search" autofocus="autofocus" name="AddressSearch" size="50" value="', $_POST['AddressSearch'], '" />
				<fieldhelp>', _('Enter all or part of the patients address if it is known'), '</fieldhelp>
			</field>
			<field>
				<label for="PhoneSearch">', _('Patients Phone Number'), '</label>
				<input type="search" autofocus="autofocus" name="PhoneSearch" value="', $_POST['PhoneSearch'], '" />
				<fieldhelp>', _('Enter all or part of the patients phone number if it is known'), '</fieldhelp>
			</field>
		</fieldset>';

	echo '<div class="centre">
			<input type="submit" name="Search" value="', _('Search'), '" />
		</div>
	</form>';

	if (isset($_POST['Search'])) {
		$SQL = "SELECT debtorsmaster.debtorno,
						name,
						address1,
						address2,
						address3,
						address4,
						address5,
						address6,
						custbranch.phoneno
					FROM debtorsmaster
					INNER JOIN care_person
						ON debtorsmaster.debtorno=care_person.hospital_file_nr
					INNER JOIN custbranch
						ON debtorsmaster.debtorno=custbranch.debtorno
						AND custbranch.branchcode='CASH'
					WHERE name LIKE '%" . $_POST['NameSearch'] . "%'
						AND debtorsmaster.debtorno LIKE '%" . $_POST['FileNumberSearch'] . "%'
						AND custbranch.phoneno LIKE '%" . $_POST['PhoneSearch'] . "%'
						AND CONCAT(address1, address2, address3, address4, address5, address6) LIKE '%" . $_POST['AddressSearch'] . "%'";
		$Result = DB_query($SQL);

		echo '<table>
				<tr>
					<th>', _('Patient Number'), '</th>
					<th>', _('Patient Name'), '</th>
					<th>', _('Address'), '</th>
					<th>', _('Phone Number'), '</th>
					<th></th>
				</tr>';

		while ($MyRow = DB_fetch_array($Result)) {
			$Address = '';
			for ($i = 1;$i <= 6;$i++) {
				if ($MyRow['address' . $i] != '') {
					$Address.= $MyRow['address' . $i];
				}
			}
			echo '<tr class="striped_row">
					<td>', $MyRow['debtorno'], '</td>
					<td>', $MyRow['name'], '</td>
					<td>', $Address, '</td>
					<td>', $MyRow['phoneno'], '</td>
					<td><a href="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8') . '?SelectedPatient=', urlencode($MyRow['debtorno']), '">', _('Admit Patient'), '</a></td>
				</tr>';
		}

		echo '</table>';
	}
	include ('includes/footer.php');
	exit;
}

echo '<p class="page_title_text">
		<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/user.png" title="', $Title, '" alt="" />', $Title, '
	</p>';

$SQL = "SELECT debtorsmaster.debtorno,
				care_person.title,
				name,
				address1,
				address2,
				address3,
				address4,
				address5,
				address6,
				custbranch.phoneno,
				care_person.blood_group,
				care_person.sex,
				care_person.date_birth
			FROM debtorsmaster
			INNER JOIN care_person
				ON debtorsmaster.debtorno=care_person.hospital_file_nr
			INNER JOIN custbranch
				ON debtorsmaster.debtorno=custbranch.debtorno
				AND custbranch.branchcode='CASH'
			WHERE debtorsmaster.debtorno='" . $SelectedPatient . "'";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);

echo '<form action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '" method="post">';
echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';
echo '<input type="hidden" name="SelectedPatient" value="', $SelectedPatient, '" />';
$Titles = array(1 => _('Mr'), 2 => _('Ms'), 3 => _('Miss'), 4 => _('Mrs'), 5 => _('Dr'));

$Address = $MyRow['address1'];
for ($i = 2;$i <= 6;$i++) {
	if ($MyRow['address' . $i] != '') {
		$Address.= ', ' . $MyRow['address' . $i];
	}
}

$Gender['m'] = _('Male');
$Gender['f'] = _('Female');

echo '<fieldset>
		<legend>', _('Admit'), ' ', $MyRow['name'], ' (', $SelectedPatient, ') ', _('as an inpatient'), '</legend>
		<field>
			<label for="AdmissionDate">', _('Admission Date'), ':</label>
			<div class="fieldtext">', Date($_SESSION['DefaultDateFormat']), '</div>
		</field>
		<field>
			<label for="AdmissionTime">', _('Admission Time'), ':</label>
			<div class="fieldtext">', Date('H:i'), '</div>
		</field>
		<field>
			<label for="Name">', _('Name'), ':</label>
			<div class="fieldtext">', $Titles[$MyRow['title']], '. ', $MyRow['name'], '</div>
		</field>
		<field>
			<label for="Address">', _('Address'), ':</label>
			<div class="fieldtext">', $Address, '</div>
		</field>
		<field>
			<label for="BloodGroup">', _('Blood Group'), ':</label>
			<div class="fieldtext">', $MyRow['blood_group'], '</div>
		</field>
		<field>
			<label for="Gender">', _('Gender'), ':</label>
			<div class="fieldtext">', $Gender[$MyRow['sex']], '</div>
		</field>
		</field>
		<field>
			<label for="DateOfBirth">', _('Date Of Birth'), ':</label>
			<div class="fieldtext">', ConvertSQLDate($MyRow['date_birth']), '</div>
		</field>';

$SQL = "SELECT type_nr, name FROM care_type_encounter WHERE status='0'";
$Result = DB_query($SQL);
echo '<field>
		<label for="AdmissionType">', _('Admission Type'), '</label>
		<select name="AdmissionType" autofocus="autofocus">';
while ($MyRow = DB_fetch_array($Result)) {
	if (isset($_POST['AdmissionType']) and $_POST['AdmissionType'] == $MyRow['type_nr']) {
		echo '<option selected="selected" value="', $MyRow['type_nr'], '">', $MyRow['name'], '</option>';
	} else {
		echo '<option value="', $MyRow['type_nr'], '">', $MyRow['name'], '</option>';
	}
}
echo '</select>
	<fieldhelp>', _('A type of admission.'), '</fieldhelp>
</field>';

$TriageCodes = array('white' => _('White'), 'green' => _('Green'), 'yellow' => _('Yellow'), 'red' => _('Red'));
echo '<field>
		<label for="TriageCode">', _('Triage Code'), ':</label>
		<select name="TriageCode">';
foreach ($TriageCodes as $Key => $Value) {
	if ($Key == $_POST['Title']) {
		echo '<option selected="selected" value="', $Key, '">', $Value, '</option>';
	} else {
		echo '<option value="', $Key, '">', $Value, '</option>';
	}
}
echo '</select>
	<fieldhelp>', _('The triage code for this patient, white being the least serious, and red being the most serious.'), '</fieldhelp>
</field>';

$SQL = "SELECT nr, name FROM care_ward";
$Result = DB_query($SQL);
echo '<field>
		<label for="Ward">', _('Ward'), '</label>
		<select name="Ward">';
while ($MyRow = DB_fetch_array($Result)) {
	if (isset($_POST['Ward']) and $_POST['Ward'] == $MyRow['nr']) {
		echo '<option selected="selected" value="', $MyRow['nr'], '">', $MyRow['name'], '</option>';
	} else {
		echo '<option value="', $MyRow['nr'], '">', $MyRow['name'], '</option>';
	}
}
echo '</select>
	<fieldhelp>', _('The ward that this patient is being admitted to.'), '</fieldhelp>
</field>';

echo '<field>
		<label for="Diagnosis">', _('Referrer Diagnosis'), ':</label>
		<input type="text" name="Diagnosis" size="100" value="" />
	</field>';

echo '<field>
		<label for="ReferredBy">', _('Referred By'), ':</label>
		<input type="text" name="ReferredBy" size="100" value="" />
	</field>';

echo '<field>
		<label for="Therapy">', _('Referrer Therapy'), ':</label>
		<input type="text" name="Therapy" size="100" value="" />
	</field>';

echo '<field>
		<label for="ReferrerNotes">', _('Referrer Notes'), ':</label>
		<input type="text" name="ReferrerNotes" size="100" value="" />
	</field>';

$SQL = "SELECT debtorno,
				name
			FROM debtorsmaster
			WHERE typeid='" . $_SESSION['InsuranceDebtorType'] . "'";
$Result = DB_query($SQL);

if (DB_num_rows($Result) > 0) {
	echo '<field>
			<label for="Insurance">', _('Insurance Company'), ':</label>
			<select name="Insurance">';
	echo '<option value=""></option>';
	while ($MyRow = DB_fetch_array($Result)) {
		if (isset($_POST['Insurance']) and $_POST['Insurance'] == $MyRow['debtorno']) {
			echo '<option selected="selected" value="', $MyRow['debtorno'], '">', $MyRow['name'], '</option>';
		} else {
			echo '<option value="', $MyRow['debtorno'], '">', $MyRow['name'], '</option>';
		}
	}
	echo '</select>
		<fieldhelp>', _('The insurance company, if any, that this patient belongs to'), '</fieldhelp>
	</field>';
}

echo '<field>
		<label for="InsuranceNo">', _('Insurance Number'), ':</label>
		<input type="text" name="InsuranceNo" size="20" value="" />
	</field>';

echo '</fieldset>';

echo '<div class="centre">
		<input type="submit" name="Create" value="' . ('Admit the patient') . '" />
	</div>';

include ('includes/footer.php');
?>