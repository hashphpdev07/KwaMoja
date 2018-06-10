<?php
$PageSecurity = 0;

include ('includes/session.php');

if (isset($_SESSION['FirstLogIn']) and $_SESSION['FirstLogIn'] == '1' and isset($_SESSION['DatabaseName'])) {
	$_SESSION['FirstRun'] = true;
	echo '<meta http-equiv="refresh" content="0; url=' . $RootPath . '/InitialScripts.php">';
	exit;
} else {
	$_SESSION['FirstRun'] = false;
}

$Title = _('Main Menu');

if ($_SESSION['Theme'] == 'mobile') {

} else {

	$Title = _('KwaMoja Medical');
	include ('includes/header_main.inc');

	echo '<header>';

	echo '<div id="AppInfo">', //===HJ===
	'<div id="AppInfoCompany">
				<img class="header" alt="', _('Company'), '" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/company.png" title="', _('Company'), '" />
				<a href="#" class="header_link" onclick="Show(1, \'CompanyPreferences.php\', \'', stripslashes($_SESSION['CompanyRecord']['coyname']), '\'); return false;">', stripslashes($_SESSION['CompanyRecord']['coyname']), '</a>
			</div>', '<div id="AppInfoUser">
				<img class="header" alt="', _('User'), '" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/user.png" title="', _('User'), '" />
				<a href="#" class="header_link" onclick="Show(1, \'UserSettings.php\', \'', stripslashes($_SESSION['UsersRealName']), '\'); return false;">', stripslashes($_SESSION['UsersRealName']), '</a>
			</div>', '</div>'; // AppInfo
	echo '<div style="float:right;">
			<a title="Log out of KwaMoja" id="exit" href="' . $RootPath . '/Logout.php" onclick="return MakeConfirm(\'', _('Are you sure you wish to logout?'), '\', \'', _('Confirm Logout'), '\', this);">
				<img id="exit_image" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/quit.png" /></a>
		</div>';

	$SQL = "SELECT scripts FROM dashboard_users WHERE userid = '" . $_SESSION['UserID'] . "' ";

	$Result = DB_query($SQL);

	$MyRow = DB_fetch_array($Result);
	$ScriptArray = explode(',', $MyRow['scripts']);

	$SQL = "SELECT id,
				scripts,
				pagesecurity,
				description
			FROM dashboard_scripts";
	$Result = DB_query($SQL);

	echo '<div class="DashboardSelector">', _('Add reports to your dashboard'), ' :
			<select name="Reports" class="DashboardSelectBox" id="dashboard_options" onchange="AddApplet()">';
	echo '<option value=""></option>';
	while ($MyRow = DB_fetch_array($Result)) {
		if (!in_array($MyRow['id'], $ScriptArray) and in_array($MyRow['pagesecurity'], $_SESSION['AllowedPageSecurityTokens'])) {
			echo '<option value="', $MyRow['id'], '">', $MyRow['description'], '</option>';
		}
	}
	echo '</select>
		</div>';

	echo '</header>';

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

	echo '<nav>
			<ul id="module_menu">';

	while ($ModuleRow = DB_fetch_array($ModuleResult)) {
		echo '<li name="module_link" id="', $ModuleRow['modulelink'], '">
				<a href="#" onclick="SetClickedModuleLink(\'', $ModuleRow['modulelink'], '\');return false;">
					<img title="', _($ModuleRow['modulename']), '" class="ModuleIcon" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/modules/', $ModuleRow['modulelink'], '.png" />
					', _($ModuleRow['modulename']), '
				</a>
			</li>';
	}

	echo '</ul>
		</nav>';

	DB_data_seek($ModuleResult, 0);

	while ($ModuleRow = DB_fetch_array($ModuleResult)) {
		echo '<nav class="item_menu" name="item_menu" id="item_', $ModuleRow['modulelink'], '">
				<nav class="item_menu_header">', _($ModuleRow['modulename']), '<img title="', _('Close Menu'), '" class="menu_exit_icon" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/cross.png" onclick="CloseMenu(); return false;" />
				</nav>
				<nav class="menu_tab_bar">';
		$SQL = "SELECT DISTINCT menusection
					FROM menuitems
					WHERE secroleid='" . $_SESSION['AccessLevel'] . "'
						AND modulelink='" . $ModuleRow['modulelink'] . "'
					ORDER BY menusection DESC";
		$SectionResult = DB_query($SQL);
		$i = 0;
		while ($SectionRow = DB_fetch_array($SectionResult)) {
			if ($i == 0) {
				echo '<button class="menu_button_active" name="', $ModuleRow['modulelink'], 'tab_button" id="', $ModuleRow['modulelink'], $SectionRow['menusection'], '" onclick="ChangeTab(\'', $ModuleRow['modulelink'], '\', \'', $ModuleRow['modulelink'], $SectionRow['menusection'], '\'); return false;">', _($SectionRow['menusection']), '</button>';
			} else {
				echo '<button class="menu_button_inactive" name="', $ModuleRow['modulelink'], 'tab_button" id="', $ModuleRow['modulelink'], $SectionRow['menusection'], '" onclick="ChangeTab(\'', $ModuleRow['modulelink'], '\', \'', $ModuleRow['modulelink'], $SectionRow['menusection'], '\'); return false;">', _($SectionRow['menusection']), '</button>';
			}
			++$i;
		}
		echo '</nav>'; //Button bar
		DB_data_seek($SectionResult, 0);
		$i = 0;
		$j = 0;
		while ($SectionRow = DB_fetch_array($SectionResult)) {
			$SQL = "SELECT menusection,
							caption,
							url,
							sequence
						FROM menuitems
						WHERE secroleid='" . $_SESSION['AccessLevel'] . "'
							AND modulelink='" . $ModuleRow['modulelink'] . "'
							AND menusection='" . $SectionRow['menusection'] . "'
						ORDER BY sequence ASC";
			$MenuResult = DB_query($SQL);
			if ($i == 0) {
				echo '<ul class="menu_container" name="', $ModuleRow['modulelink'], 'menu_container" style="display:inline-block" id="', $ModuleRow['modulelink'], $SectionRow['menusection'], 'menu_container">';
			} else {
				echo '<ul class="menu_container" name="', $ModuleRow['modulelink'], 'menu_container" id="', $ModuleRow['modulelink'], $SectionRow['menusection'], 'menu_container">';
			}
			while ($MenuRow = DB_fetch_array($MenuResult)) {
				echo '<li  class="menu_link_box">
						<a href="#" class="menu_link" onclick="Show(', $j, ', \'', substr($MenuRow['url'], 1), '\', \'', _($MenuRow['caption']), '\'); return false;">', _($MenuRow['caption']), '</a>
					</li>';
				++$j;
			}
			echo '</ul>'; //menu_container
			++$i;
		}
		echo '</nav>'; //menu
		
	}

	$SQL = "SELECT id,
				scripts,
				pagesecurity,
				description
			FROM dashboard_scripts";

	$Result = DB_query($SQL);

	echo '<script>
		function InitialiseDashboard() {
			sessionStorage.clear();';

	while ($MyRow = DB_fetch_array($Result)) {
		if (in_array($MyRow['id'], $ScriptArray) and in_array($MyRow['pagesecurity'], $_SESSION['AllowedPageSecurityTokens'])) {
			echo 'sessionStorage.dashboard', $MyRow['id'], '=\'', $MyRow['scripts'], '\';';
		}
		echo 'sessionStorage.scripts', $MyRow['id'], '=\'', $MyRow['scripts'], '\';';
	}
	echo 'ShowDashboard();
			}
		</script>';

	echo '<div id="mask" name="mask"></div>';
	echo '<div id="dialog" name="dialog"></div>';
	echo '<input type="hidden" name="Theme" id="Theme" value="', $_SESSION['Theme'], '" />';

	echo '</body>
	</html>';

}
?>