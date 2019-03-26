<?php
NewScript('Timesheets.php', 1);
NewMenuItem('manuf', 'Transactions', _('Timesheet Entry'), '/Timesheets.php', 3);
NewConfigValue('LastDayOfWeek', '0');

AddColumn('userid', 'prlemployeemaster', 'VARCHAR(20)', 'NOT NULL', '', 'gender');
AddColumn('stockid', 'prlemployeemaster', 'VARCHAR(20)', 'NOT NULL', '', 'userid');
AddColumn('manager', 'prlemployeemaster', 'VARCHAR(20)', 'NOT NULL', '', 'stockid');
AddColumn('normalhours', 'prlemployeemaster', 'INT(11)', 'NOT NULL', 40, 'hourlyrate');

CreateTable('timesheets', "CREATE TABLE `timesheets` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `wo` int(11) NOT NULL COMMENT 'loose FK with workorders',
  `employeeid` INT NOT NULL,
  `weekending` DATE NOT NULL DEFAULT '1900-01-01',
  `workcentre` varchar(5) NOT NULL COMMENT 'loose FK with workcentres',
  `day1` double NOT NULL default 0,
  `day2` double NOT NULL default 0,
  `day3` double NOT NULL default 0,
  `day4` double NOT NULL default 0,
  `day5` double NOT NULL default 0,
  `day6` double NOT NULL default 0,
  `day7` double NOT NULL default 0,
  `status` tinyint(4) NOT NULL default 0,
  KEY `workcentre` (`workcentre`),
  KEY `employees` (`employeeid`),
  KEY `wo` (`wo`),
  KEY `weekending` (`weekending`)
)");

UpdateDBNo(basename(__FILE__, '.php'));

?>