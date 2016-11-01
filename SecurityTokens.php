<?php

include('includes/session.php');
$Title = _('Maintain Security Tokens');
$ViewTopic = 'SecuritySchema';
$BookMark = 'SecurityTokens'; // Pending ?

include('includes/header.php');

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
				$List .= ' ' . $ScriptRow['script'];
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

echo '<form method="post" action="', htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'), '" id="form">';
echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';
echo '<table>
		<tr>';

if (isset($_GET['Action']) and $_GET['Action'] == 'edit') {
	echo '<td>', _('Description'), '</td>
		<td><input type="text" size="50" autofocus="autofocus" required="required" maxlength="50" name="TokenDescription" value="', _($_POST['TokenDescription']), '" /></td>
		<td><input type="hidden" name="TokenID" value="', $_GET['TokenID'], '" />
			<input type="hidden" name="Action" value="update" />
			<input type="submit" name="Submit" value="', _('Update'), '" />';
} else {
	echo '<td>', _('Token ID'), '</td>
			<td><input class="number" size="6" required="required" maxlength="4" type="text" name="TokenID" value="', $_POST['TokenID'], '" /></td>
		</tr>
		<tr>
			<td>', _('Description'), '</td>
			<td><input type="text" size="50" required="required" maxlength="60" name="TokenDescription" value="', _($_POST['TokenDescription']), '" /></td>
			<input type="hidden" name="Action" value="insert" />
			<td><input type="submit" name="Submit" value="', _('Insert'), '" />';
}

echo '</td>
	</tr>
</table>';

echo '</form>';

echo '<table class="selection">';
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
	echo '<tr>
			<td>', $MyRow['tokenid'], '</td>
			<td>', htmlspecialchars($MyRow['tokenname'], ENT_QUOTES, 'UTF-8'), '</td>
			<td class="noPrint"><a href="', htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'), '?Action=edit&amp;TokenID=', $MyRow['tokenid'], '">', _('Edit'), '</a></td>
			<td class="noPrint"><a href="', htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'), '?Action=delete&amp;TokenID=', $MyRow['tokenid'], '" onclick="return confirm(\'', _('Are you sure you wish to delete this security token?'), '\');">', _('Delete'), '</a></td>
		</tr>';
}

echo '</tbody>
	</table>';

include('includes/footer.php');
?>