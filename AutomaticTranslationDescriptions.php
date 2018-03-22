<?php
/* $Id: AutomaticTranslationDescriptions.php 7037 2014-12-22 14:45:20Z tehonu $ */

include ('includes/session.php');
$Title = _('Translate Item Descriptions');
$ViewTopic = 'SpecialUtilities'; // Filename in ManualContents.php's TOC.
$BookMark = 'Z_TranslateItemDescriptions'; // Anchor's id in the manual's html document.
include ('includes/header.php');

if (!function_exists('curl_init')) {
	prnMsg('This script requires that the PHP curl module be available to use the Google API. Unfortunately this installation does not have the curl module available', 'error');
	include ('includes/footer.php');
	exit;
}

include ('includes/GoogleTranslator.php');

$SourceLanguage = mb_substr($_SESSION['Language'], 0, 2);

// Select items and classify them
$SQL = "SELECT stockmaster.stockid,
				description,
				longdescription,
				stockdescriptiontranslations.language_id,
				descriptiontranslation
		FROM stockmaster
		LEFT JOIN stockdescriptiontranslations
			ON stockmaster.stockid = stockdescriptiontranslations.stockid
		WHERE stockmaster.discontinued = 0
			AND (descriptiontranslation = '')
		ORDER BY stockmaster.stockid,
				language_id";
$Result = DB_query($SQL);
echo $SQL;
if (DB_num_rows($Result) != 0) {
	echo '<p class="page_title_text" align="center">
			<strong>', _('Description Automatic Translation for empty translations'), '</strong>
		</p>';
	echo '<table>';
	echo '<tr>
			<th>', _('#'), '</th>
			<th>', _('Code'), '</th>
			<th>', _('Description'), '</th>
			<th>', _('To'), '</th>
			<th>', _('Translated'), '</th>
		</tr>';

	$i = 0;
	while ($MyRow = DB_fetch_array($Result)) {

		if ($MyRow['descriptiontranslation'] == '') {
			$TargetLanguage = mb_substr($MyRow['language_id'], 0, 2);
			$TranslatedText = translate_via_google_translator($MyRow['description'], $TargetLanguage, $SourceLanguage);
			$ErrMsg = _('Cannot update stock item descriptions');
			$DbgMsg = _('The sql that failed to update the item descriptions is');
			$SQL = "UPDATE stockdescriptiontranslations " . "SET descriptiontranslation='" . $TranslatedText . "', " . "needsrevision= '1' " . "WHERE stockid='" . $MyRow['stockid'] . "' AND (language_id='" . $MyRow['language_id'] . "')";
			$Update = DB_query($SQL, $ErrMsg, $DbgMsg, true);

			++$i;
			echo '<tr class="striped_row">
					<td class="number">', $i, '</td>
					<td>', $MyRow['stockid'], '</td>
					<td>', $MyRow['description'], '</td>
					<td>', $MyRow['language_id'], '</td>
					<td>', $TranslatedText, '</td>
				</tr>';
		}
		if ($MyRow['longdescriptiontranslation'] == '') {
			$TargetLanguage = mb_substr($MyRow['language_id'], 0, 2);
			$TranslatedText = translate_via_google_translator($MyRow['longdescription'], $TargetLanguage, $SourceLanguage);

			$SQL = "UPDATE stocklongdescriptiontranslations " . "SET longdescriptiontranslation='" . $TranslatedText . "', " . "needsrevision= '1' " . "WHERE stockid='" . $MyRow['stockid'] . "' AND (language_id='" . $MyRow['language_id'] . "')";
			$Update = DB_query($SQL, $ErrMsg, $DbgMsg, true);

			++$i;
			echo '<tr class="striped_row">
					<td class="number">', $i, '</td>
					<td>', $MyRow['stockid'], '</td>
					<td>', $MyRow['longdescription'], '</td>
					<td>', $MyRow['language_id'], '</td>
					<td>', $TranslatedText, '</td>
				</tr>';
		}
	}
	echo '</table>';
	prnMsg('Number of translated descriptions via Google API: ' . locale_number_format($i));
} else {

	echo '<p class="page_title_text">
			<img alt="" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/maintenance.png" title="', _('No item description was automatically translated'), '" />', ' ', _('No item description was automatically translated'), '
		</p>';

	// Add error message for "Google Translator API Key" empty.
	
}

include ('includes/footer.php');
?>