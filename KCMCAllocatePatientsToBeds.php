<?php
include ('includes/session.php');
$Title = _('Allocate Patients to Beds');

include ('includes/header.php');
include ('includes/SQL_CommonFunctions.php');

if (isset($_POST['SelectedWard'])) {
	$SelectedWard = $_POST['SelectedWard'];
} elseif (isset($_GET['SelectedWard'])) {
	$SelectedWard = $_GET['SelectedWard'];
} else {
	$Title = _('This script can only be called with a ward ID as reference');
	include ('includes/header.php');
	prnMsg(_('This script can only be called with a ward ID as reference'), 'info');
	include ('includes/footer.php');
	exit;
}

echo '<p class="page_title_text">
		<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/magnifier.png" title="', _('Search'), '" alt="" />', $Title, '
	</p>';

$AllocatedToWard = array();
$SQL = "SELECT pid
			FROM care_encounter
			WHERE current_ward_nr='" . $SelectedWard . "'
				AND in_ward=1";
$Result = DB_query($SQL);
while ($MyRow = DB_fetch_array($Result)) {
	$AllocatedToWard[] = $MyRow['pid'];
}

$SQL = "SELECT care_ward.roomprefix,
				room_nr,
				nr_of_beds
			FROM care_room
			INNER JOIN care_ward
				ON care_room.ward_nr=care_ward.nr
			WHERE ward_nr='" . $SelectedWard . "'
			ORDER BY room_nr";
$Result = DB_query($SQL);

if (DB_num_rows($Result) > 0) {
	echo '<table>
			<tr>
				<th>', _('Room Number'), '</th>
				<th>', _('Bed Number'), '</th>
				<th>', _('Family Name'), '</th>
				<th>', _('First Name'), '</th>
				<th>', _('Birth Date'), '</th>
				<th>', _('Patient No.'), '</th>
				<th>', _('Insurance Co.'), '</th>
				<th>', _('Options'), '</th>
			</tr>';

	while ($MyRow = DB_fetch_array($Result)) {
		for ($BedNumber = 1;$BedNumber <= $MyRow['nr_of_beds'];$BedNumber++) {
			echo '<tr>';
			if ($BedNumber == 1) {
				echo '<td>', $MyRow['roomprefix'], $MyRow['room_nr'], '</td>';
			} else {
				echo '<td>&nbsp;</td>';
			}
			echo '<td>', _('Bed'), ' ', $BedNumber, '</td>';
			if (!isset($Occupant[$MyRow['room_nr']][$BedNumber]) or $Occupant[$MyRow['room_nr']][$BedNumber] == 0) {
				echo '<td colspan="4">', _('Bed is currently vacant'), '</td>';
				echo '<td colspan="2"><a href="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '?Allocate=Yes&SelectedWard=', $SelectedWard, '&SelectedRoom=', $MyRow['room_nr'], '$SelectedBed=', $BedNumber, '">', _('Allocate Patient To This Bed'), '</a><td>';
			}
			echo '</tr>';
		}
	}
	echo '</table>';
}

include ('includes/footer.php');

?>