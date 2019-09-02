<?php
include ('includes/session.php');
$Title = _('Maintain Security Tokens');
$ViewTopic = 'SecuritySchema';
$BookMark = 'SecurityTokens'; // Pending ?
include ('includes/header.php');

if ($AllowDemoMode == true) {
	prnMsg(_('The the system is in demo mode and the security model administration is disabled'), 'warn');
	include ('includes/footer.php');
	exit;
}

// Merge gets into posts:
if (isset($_GET['Action'])) {
	$_POST['Action'] = $_GET['Action'];
}

if (isset($_GET['TokenID'])) {
	$_POST['TokenID'] = $_GET['TokenID'];
}
if (isset($_GET['TokenDescription'])) {
	$_POST['TokenDescription'] = $_GET['TokenDescription'];
}
// Validate the data sent:
$InputError = 0;

if (isset($_POST['Action']) and ($_POST['Action'] == 'insert' or $_POST['Action'] == 'update')) {
	if (!is_numeric($_POST['TokenID'])) {
		prnMsg(_('The token ID is expected to be a number. Please enter a number for the token ID'), 'error');
		$InputError = 1;
	}
	if (mb_strlen($_POST['TokenID']) == 0) {
		prnMsg(_('A token ID must be entered'), 'error');
		$InputError = 1;
	}
	if ($_POST['TokenID'] > 999) {
		prnMsg(_('The token ID must be less than 1000'), 'error');
		$InputError = 1;
	}
	if (mb_strlen($_POST['TokenDescription']) == 0) {
		prnMsg(_('A token description must be entered'), 'error');
		$InputError = 1;
	}
} else if (!isset($_POST['Action'])) {
	$_POST['Action'] = '';
}

// Execute the requested action:
switch ($_POST['Action']) {
	case 'cancel':
		unset($_POST['Action']);
		$_POST['TokenID'] = '';
		$_POST['TokenDescription'] = '';
	break;
	case 'delete':
		$Result = DB_query("SELECT script FROM scripts WHERE pagesecurity='" . $_POST['TokenID'] . "'");
		if (DB_num_rows($Result) > 0) {
			$List = '';
			while ($ScriptRow = DB_fetch_array($Result)) {
				$List.= ' ' . $ScriptRow['script'];
			}
			prnMsg(_('This security token is currently used by the following scripts and cannot be deleted') . ':' . $List, 'error');
		} else {
			$Result = DB_query("DELETE FROM securitytokens WHERE tokenid='" . $_POST['TokenID'] . "'");
			if ($Result) {
				prnMsg(_('The security token was deleted successfully'), 'success');
			}
		}
		unset($_POST['Action']);
		$_POST['TokenID'] = '';
		$_POST['TokenDescription'] = '';
	break;
	case 'edit':
		$Result = DB_query("SELECT tokenid, tokenname FROM securitytokens WHERE tokenid='" . $_POST['TokenID'] . "'");
		$MyRow = DB_fetch_array($Result);
		// Keeps $_POST['Action']=edit, and sets $_POST['TokenID'] and $_POST['TokenDescription'].
		$_POST['TokenID'] = $MyRow['tokenid'];
		$_POST['TokenDescription'] = $MyRow['tokenname'];
	break;
	case 'insert':
		$Result = DB_query("SELECT tokenid FROM securitytokens WHERE tokenid='" . $_POST['TokenID'] . "'");
		if (DB_num_rows($Result) != 0) {
			prnMsg(_('This token ID has already been used. Please use a new one'), 'warn');
			$InputError = 1;
		}
		if ($InputError == 0) {
			$Result = DB_query("INSERT INTO securitytokens values('" . $_POST['TokenID'] . "', '" . $_POST['TokenDescription'] . "')");
			if ($Result) {
				prnMsg(_('The security token was inserted successfully'), 'success');
			}
			unset($_POST['Action']);
			$_POST['TokenID'] = '';
			$_POST['TokenDescription'] = '';
		}
	break;
	case 'update':
		if ($InputError == 0) {
			$Result = DB_query("UPDATE securitytokens SET tokenname='" . $_POST['TokenDescription'] . "' WHERE tokenid='" . $_POST['TokenID'] . "'");
			if ($Result) {
				prnMsg(_('The security token was updated successfully'), 'success');
			}
			unset($_POST['Action']);
			$_POST['TokenID'] = '';
			$_POST['TokenDescription'] = '';
		}
	break;
	default: // Unknown requested action.
		unset($_POST['Action']);
		$_POST['TokenID'] = '';
		$_POST['TokenDescription'] = '';
} // END switch($_POST['Action']).
echo '<p class="page_title_text">
		<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/maintenance.png" title="', _('Print'), '" alt="" />', ' ', $Title, '
	</p>';

echo '<table>';
echo '<thead>
		<tr>
			<th>', _('Token ID'), '</th>
			<th>', _('Description'), '</th>
			<th class="noPrint" colspan="2">&nbsp;</th>
		</tr>
	</thead>';

$SQL = "SELECT tokenid, tokenname FROM securitytokens WHERE tokenid<1000 ORDER BY tokenid";
$Result = DB_query($SQL);
echo '<tbody>';
while ($MyRow = DB_fetch_array($Result)) {
	echo '<tr class="striped_row">
			<td>', $MyRow['tokenid'], '</td>
			<td>', htmlspecialchars($MyRow['tokenname'], ENT_QUOTES, 'UTF-8'), '</td>
			<td class="noPrint"><a href="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '?Action=edit&amp;TokenID=', $MyRow['tokenid'], '">', _('Edit'), '</a></td>
			<td class="noPrint"><a href="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '?Action=delete&amp;TokenID=', $MyRow['tokenid'], '" onclick="return MakeConfirm(\'', _('Are you sure you wish to delete this security token?'), '\', \'Confirm Delete\', this);">', _('Delete'), '</a></td>
		</tr>';
}

echo '</tbody>
	</table>';

echo '<form method="post" action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '" id="form">';
echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';
echo '<fieldset>';

if (isset($_GET['Action']) and $_GET['Action'] == 'edit') {
	echo '<legend>', _('Edit Security Token Details'), '</legend>';
	echo '<field>
			<label for="TokenID">', _('Token ID'), '</label>
			<div class="fieldtext">', $_POST['TokenID'], '</div>
			<input type="hidden" name="Action" value="update" />
			<input type="hidden" name="TokenID" value="', $_POST['TokenID'], '" />
		</field>';
} else {
	echo '<legend>', _('New Security Token Details'), '</legend>';
	echo '<field>
			<label for="TokenID">', _('Token ID'), '</label>
			<input autofocus="autofocus" class="number" size="6" required="required" maxlength="4" type="text" name="TokenID" value="', $_POST['TokenID'], '" />
			<fieldhelp>', _('The number of the security token being created'), '</fieldhelp>
			<input type="hidden" name="Action" value="insert" />
		</field>';
}
echo '<field>
		<label for="TokenDescription">', _('Description'), '</label>
		<input type="text" size="50" autofocus="autofocus" required="required" maxlength="50" name="TokenDescription" value="', _($_POST['TokenDescription']), '" /></td>
		<fieldhelp>', _('A description of this security token that it will be known as'), '</fieldhelp>
	</field>';

echo '</fieldset>';

echo '<div class="centre">
		<input type="submit" name="Submit" value="', _('Update'), '" />
	</div>';

echo '</form>';

include ('includes/footer.php');
?>