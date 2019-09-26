<?php
NewSysType(4, _('GL Journal template'));

CreateTable('jnltmplheader', "CREATE TABLE `jnltmplheader` (
  `templateid` INT(11) NOT NULL DEFAULT 0,
  `templatedescription` VARCHAR(50) NOT NULL DEFAULT '',
  `journaltype` INT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`templateid`)
)");

CreateTable('jnltmpldetails', "CREATE TABLE `jnltmpldetails` (
  `linenumber` INT(11) NOT NULL DEFAULT 0,
  `templateid` INT(11) NOT NULL DEFAULT 0,
  `tags` VARCHAR(50) NOT NULL DEFAULT '0',
  `accountcode` VARCHAR(20) NOT NULL DEFAULT '1',
  `amount` DOUBLE NOT NULL DEFAULT 0,
  `narrative` VARCHAR(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`templateid`, `linenumber`)
)");

NewScript('GLJournalTemplates.php', 15);
NewMenuItem('GL', 'Maintenance', _('Maintain journal templates'), '/GLJournalTemplates.php', 11);

UpdateDBNo(basename(__FILE__, '.php'));

?>