<?php
$PageSecurity = 0;

include('includes/session.inc');
if (!isset($_SESSION['FirstRun'])) {
	if (file_exists('install/InitialScripts.txt') and (filesize('install/InitialScripts.txt') > 0) and isset($_SESSION['DatabaseName'])) {
		$_SESSION['FirstRun'] = true;
		echo '<meta http-equiv="refresh" content="0; url=' . $RootPath . '/InitialScripts.php">';
		exit;
	} else {
		$_SESSION['FirstRun'] = false;
	}
}

$Title = _('Main Menu');

if ($_SESSION['Theme'] == 'mobile') {

} else {

	$Title = _('KwaMoja Medical');
	include('includes/header_main.inc');
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
				"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';

	echo '<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<title>The Official KwaMoja web site</title>';

	echo '<meta http-equiv="Content-Type" content="application/html; charset=utf-8" />';

	echo '<script type="text/javascript" src="javascripts/ModalWindow.js"></script>';

	echo '<link href="http://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet" type="text/css">';
	echo '<link href="http://fonts.googleapis.com/css?family=Dancing+Script" rel="stylesheet" type="text/css">';

	echo '</head>';

	echo '<body>';

	$SQL = "SELECT modulename,
					modulelink,
					secroleid
				FROM modules
				INNER JOIN www_users
					ON modules.secroleid=www_users.fullaccess
				WHERE userid='" . $_SESSION['UserID'] . "'
				ORDER BY sequence";

	$DbgMsg = _('The SQL that was used to retrieve the information was');
	$ErrMsg = _('Could not retrieve the modules associated with this account');

	$ModuleResult = DB_query($SQL, $ErrMsg, $DbgMsg);

	echo '<nav id="menu-wrap">
	<ul id="menu">';

	while ($ModuleRow = DB_fetch_array($ModuleResult)) {
		echo '<li>
				<a href="">
					<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/' . $ModuleRow['modulelink'] . '.png" />
				</a>
				<ul>';
		$SQL = "SELECT DISTINCT menusection FROM menuitems WHERE modulelink='" . $ModuleRow['modulelink'] . "'";
		$SectionResult = DB_query($SQL);
		echo '<li id="menu_title">' . $ModuleRow['modulename'] . '</li>';
		while ($SectionRow = DB_fetch_array($SectionResult)) {
			echo '<li>
						<a href="">
							<img title="' . $ModuleRow['modulename'] . '" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/' . strtolower($SectionRow['menusection']) . '.png" />
							' . $SectionRow['menusection'] . '
						</a>
						<ul class="sub_menu">';
			$SQL = "SELECT menuitems.url,
							caption
						FROM menuitems
						WHERE modulelink='" . $ModuleRow['modulelink'] . "'
							AND menusection='" . $SectionRow['menusection'] . "'
							AND secroleid='" . $ModuleRow['secroleid'] . "'
						ORDER BY sequence";
			$DbgMsg = _('The SQL that was used to retrieve the information was');
			$ErrMsg = _('Could not retrieve the scripts associated with this account');
			$ScriptResult = DB_query($SQL, $ErrMsg, $DbgMsg);

			while ($ScriptRow = DB_fetch_array($ScriptResult)) {
				echo '<li class="auto-width">
					<a href="#" onclick="Show(1, \'' . substr($ScriptRow['url'], 1) . '\', \'' . $ScriptRow['caption'] . '\'); return false;">' . $ScriptRow['caption'] . '</a>
				</li>';
			}
			echo '</ul>
					</li>';
		}
		echo '</ul>
			</li>';
	}

	echo '</ul>
		</nav>';
	echo '<div id="site_title">' . _('KwaMoja') . '<br />' . _('Medical') . '</div>';
/*

	echo '<div id="footer">';
	echo '<ul id="footer_menu">';

	while ($ModuleRow = DB_fetch_array($ModuleResult)) {
		echo '<li>
				<a href="#">
					<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/' . $ModuleRow['modulelink'] . '.png" />
				</a>';
		echo '<div class="one_column_layout">
			<div class="col_1">';
			echo '<a href="#" class="listLinks" >
				</a>';
		}
		echo '</div>
			</div>';
		echo '</li>';
	}
	echo '</ul>';

	echo'</div>';*/

	echo '</body>
	</html>';

}


function GetRptLinks($GroupID) {
	/*
	This function retrieves the reports given a certain group id as defined in /reports/admin/defaults.php
	in the acssociative array $ReportGroups[]. It will fetch the reports belonging solely to the group
	specified to create a list of links for insertion into a table to choose a report. Two table sections will
	be generated, one for standard reports and the other for custom reports.
	*/
	global $RootPath;
	if (!isset($_SESSION['FormGroups'])) {
		$_SESSION['FormGroups'] = array(
			'gl:chk' => _('Bank Checks'), // Bank checks grouped with the gl report group
			'ar:col' => _('Collection Letters'),
			'ar:cust' => _('Customer Statements'),
			'gl:deps' => _('Bank Deposit Slips'),
			'ar:inv' => _('Invoices and Packing Slips'),
			'ar:lblc' => _('Labels - Customer'),
			'prch:lblv' => _('Labels - Vendor'),
			'prch:po' => _('Purchase Orders'),
			'ord:quot' => _('Customer Quotes'),
			'ar:rcpt' => _('Sales Receipts'),
			'ord:so' => _('Sales Orders'),
			'misc:misc' => _('Miscellaneous')
		); // do not delete misc category
	}
	if (isset($_SESSION['ReportList'][$GroupID])) {
		$GroupID = $_SESSION['ReportList'][$GroupID];
	}
	$Title = array(
		_('Custom Reports'),
		_('Standard Reports and Forms')
	);

	if (!isset($_SESSION['ReportList'])) {
		$SQL = "SELECT id,
						reporttype,
						defaultreport,
						groupname,
						reportname
					FROM reports
					ORDER BY groupname,
							reportname";
		$Result = DB_query($SQL, '', '', false, true);
		$_SESSION['ReportList'] = array();
		while ($Temp = DB_fetch_array($Result)) {
			$_SESSION['ReportList'][] = $Temp;
		}
	}
	$RptLinks = '';
	for ($Def = 1; $Def >= 0; $Def--) {
		$RptLinks .= '<li class="menu_group_headers">';
		$RptLinks .= '<b>' . $Title[$Def] . '</b>';
		$RptLinks .= '</li>';
		$NoEntries = true;
		if (isset($_SESSION['ReportList']['groupname']) and count($_SESSION['ReportList']['groupname']) > 0) { // then there are reports to show, show by grouping
			foreach ($_SESSION['ReportList'] as $Report) {
				if (isset($Report['groupname']) and $Report['groupname'] == $GroupID and $Report['defaultreport'] == $Def) {
					$RptLinks .= '<li class="menu_group_item">';
					$RptLinks .= '<p><a href="' . $RootPath . '/reportwriter/ReportMaker.php?action=go&amp;reportid=' . urlencode($Report['id']) . '">&bull; ' . _($Report['reportname']) . '</a></p>';
					$RptLinks .= '</li>';
					$NoEntries = false;
				}
			}
			// now fetch the form groups that are a part of this group (List after reports)
			$NoForms = true;
			foreach ($_SESSION['ReportList'] as $Report) {
				$Group = explode(':', $Report['groupname']); // break into main group and form group array
				if ($NoForms and $Group[0] == $GroupID and $Report['reporttype'] == 'frm' and $Report['defaultreport'] == $Def) {
					$RptLinks .= '<li class="menu_group_item">';
					$RptLinks .= '<img src="' . $RootPath . '/css/' . $_SESSION['Theme'] . '/images/folders.gif" width="16" height="13" alt="" />&nbsp;';
					$RptLinks .= '<a href="' . $RootPath . '/reportwriter/FormMaker.php?id=' . urlencode($Report['groupname']) . '">&bull; ';
					$RptLinks .= $_SESSION['FormGroups'][$Report['groupname']] . '</a>';
					$RptLinks .= '</li>';
					$NoForms = false;
					$NoEntries = false;
				}
			}
		}
		if ($NoEntries)
			$RptLinks .= '<li class="menu_group_item">' . _('There are no reports to show!') . '</li>';
	}
	return $RptLinks;
}
?>