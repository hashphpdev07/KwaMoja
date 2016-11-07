<?php

NewScript('PrintInvoice.php', 3);

$DirHandle = dir($PathPrefix . 'companies/');
while (false !== ($CompanyEntry = $DirHandle->read())) {
	if (is_dir($PathPrefix . 'companies/' . $CompanyEntry) and $CompanyEntry != '..' and $CompanyEntry != '' and $CompanyEntry != '.') {
		copy($PathPrefix . 'companies/' . $DefaultDatabase . '/FormDesigns/invoice.css', $PathPrefix . 'companies/' . $CompanyEntry . '/FormDesigns/invoice.css');
	}
}
$DirHandle->close();

UpdateDBNo(basename(__FILE__, '.php'));

?>