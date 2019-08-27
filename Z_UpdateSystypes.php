<?php
$PageSecurity = 15;
include ('includes/session.php');
$Title = _('Update systypes table');
include ('includes/header.php');

echo '<p class="page_title_text">
		<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/maintenance.png" title="', _('Search'), '" alt="" />', ' ', $Title, '
	</p>';

if (isset($_POST['Submit'])) {
	foreach ($_POST as $Key => $Value) {
		if (mb_substr($Key, 0, 6) == 'Actual') {
			$Index = mb_substr($Key, 6);
			if (isset($_POST[$Index])) {
				$SQL = "UPDATE systypes SET typeno='" . $Value . "'
							WHERE typeid='" . $Index . "'";
				$Result = DB_query($SQL);
			}
		}
	}
}

echo '<form method="post" action="' . htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8') . '">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

echo '<table>
		<tr>
			<th>', _('ID'), '</th>
			<th>', _('Name'), '</th>
			<th>', _('Current'), '</th>
			<th>', _('Actual'), '</th>
			<th>', _('Update?'), '</th>
		</tr>';

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=0";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=0";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=1";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=1";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=2";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=2";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=3";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=3";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=4";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(templateid) AS actual FROM jnltmplheader";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=10";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=10";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=11";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=11";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=12";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=12";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=15";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=15";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=16";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(transno) AS actual FROM stockmoves WHERE type=16";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=17";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(transno) AS actual FROM stockmoves WHERE type=17";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=18";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(orderno) AS actual FROM purchorders";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=19";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(pickinglistno) AS actual FROM pickinglists";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=20";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=20";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=21";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=21";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=22";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=22";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=23";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=23";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=25";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=25";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=26";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=26";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=28";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=28";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=29";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=29";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=30";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(orderno) AS actual FROM salesorders";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=31";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=31";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=32";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=32";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=35";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=35";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=36";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=36";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=37";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(tenderid) AS actual FROM tenders";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=38";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(dispatchid) AS actual FROM stockrequest";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=39";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=39";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=40";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(wo) AS actual FROM workorders";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=41";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=41";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=42";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=42";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=43";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=43";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=44";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=44";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=49";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=49";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=50";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=50";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=60";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=60";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=61";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(typeno) AS actual FROM gltrans WHERE type=61";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=500";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
if ($_SESSION['AutoDebtorNo'] == 1) {
	$SQL = "SELECT MAX(debtorno) AS actual FROM debtorsmaster";
	$ActualResult = DB_query($SQL);
	$ActualRow = DB_fetch_array($ActualResult);
	Display($MyRow, $ActualRow['actual']);
}

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=510";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
$SQL = "SELECT MAX(donorno) AS actual FROM donors";
$ActualResult = DB_query($SQL);
$ActualRow = DB_fetch_array($ActualResult);
Display($MyRow, $ActualRow['actual']);

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=600";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
if ($_SESSION['AutoSupplierNo'] == 1) {
	$SQL = "SELECT MAX(supplierid) AS actual FROM suppliers";
	$ActualResult = DB_query($SQL);
	$ActualRow = DB_fetch_array($ActualResult);
	Display($MyRow, $ActualRow['actual']);
}

$SQL = "SELECT typeid,
				typename,
				typeno
			FROM systypes
			WHERE typeid=700";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);
if ($_SESSION['AutoInvenoryNo'] == 1) {
	$SQL = "SELECT MAX(stockid) AS actual FROM stockmaster";
	$ActualResult = DB_query($SQL);
	$ActualRow = DB_fetch_array($ActualResult);
	Display($MyRow, $ActualRow['actual']);
}

echo '</table>';
echo '<div class="centre">
		<input type="submit" name="Submit" value="Update" />
	</div>';

echo '</form>';

function Display($CurrentRow, $Actual) {
	if ($Actual == '') {
		$Actual = 0;
	}

	if ($CurrentRow['typeno'] != $Actual) {
		$Checked = ' checked="checked" ';
	} else {
		$Checked = ' ';
	}

	echo '<tr>
			<td>', $CurrentRow['typeid'], '</td>
			<td>', $CurrentRow['typename'], '</td>
			<td class="number">', $CurrentRow['typeno'], '</td>
			<td class="number">', $Actual, '</td>
			<td><input type="checkbox" name="', $CurrentRow['typeid'], '" ', $Checked, ' /></td>
			<td><input type="hidden" name="Actual', $CurrentRow['typeid'], '" value="', $Actual, '" /></td>
		</tr>';
}

?>