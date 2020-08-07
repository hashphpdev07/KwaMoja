<?php
NewScript('KCMCRequestLaboratoryTest.php', 13);
NewScript('KCMCPendingLaboratoryTests.php', 13);
NewScript('KCMCViewLaboratoryTest.php', 13);
NewScript('KCMCMedicalLaboratory.php', 13);
NewScript('KCMCEnterLaboratoryTestResults.php', 13);
NewMenuItem('hospital', 'Reports', _('Medical Laboratory Details'), '/KCMCMedicalLaboratory.php', 3);

CreateTable('care_test_request_chemlabor', "CREATE TABLE IF NOT EXISTS `care_test_request_chemlabor` (
  `batch_nr` int(11) NOT NULL auto_increment,
  `encounter_nr` int(11) unsigned NOT NULL default '0',
  `room_nr` varchar(10) collate latin1_general_ci NOT NULL,
  `dept_nr` smallint(5) unsigned NOT NULL default '0',
  `parameters` text collate latin1_general_ci NOT NULL,
  `doctor_sign` varchar(35) collate latin1_general_ci NOT NULL,
  `highrisk` smallint(1) NOT NULL default '0',
  `notes` tinytext collate latin1_general_ci NOT NULL,
  `send_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `sample_time` time NOT NULL default '00:00:00',
  `urgent` tinyint(4) NOT NULL default '0',
  `sample_weekday` smallint(1) NOT NULL default '0',
  `status` varchar(15) collate latin1_general_ci NOT NULL,
  `history` text collate latin1_general_ci,
  `modify_id` varchar(35) collate latin1_general_ci NOT NULL,
  `modify_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `create_id` varchar(35) collate latin1_general_ci NOT NULL,
  `create_time` timestamp NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`batch_nr`),
  KEY `encounter_nr` (`encounter_nr`)
)");

CreateTable('care_test_request_chemlabor_sub', "CREATE TABLE IF NOT EXISTS `care_test_request_chemlabor_sub` (
  `sub_id` int(40) NOT NULL auto_increment,
  `batch_nr` int(11) NOT NULL default '0',
  `encounter_nr` int(11) NOT NULL default '0',
  `paramater_name` varchar(255) default '0',
  `parameter_value` varchar(255) default '0',
  PRIMARY KEY  (`sub_id`)
)");

CreateTable('care_test_request_baclabor', "CREATE TABLE IF NOT EXISTS `care_test_request_baclabor` (
  `batch_nr` int(11) NOT NULL auto_increment COMMENT 'Test request batch number. primary key',
  `encounter_nr` int(11) unsigned NOT NULL default '0' COMMENT 'Related encounter number',
  `dept_nr` smallint(5) unsigned NOT NULL default '0' COMMENT 'Department number (foreign key)',
  `material` text collate latin1_general_ci NOT NULL COMMENT 'Material type',
  `test_type` text collate latin1_general_ci NOT NULL,
  `material_note` tinytext collate latin1_general_ci NOT NULL,
  `diagnosis_note` tinytext collate latin1_general_ci NOT NULL COMMENT 'Supplementary diagnosis notes',
  `immune_supp` tinyint(4) NOT NULL default '0' COMMENT 'Flag if immune suppressed. 1 = YES, 0 = NO',
  `send_date` date NOT NULL default '0000-00-00',
  `sample_date` date NOT NULL default '0000-00-00' COMMENT 'Date when sample was taken',
  `status` varchar(10) collate latin1_general_ci NOT NULL,
  `history` text collate latin1_general_ci NOT NULL,
  `modify_id` varchar(35) collate latin1_general_ci NOT NULL,
  `modify_time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `create_id` varchar(35) collate latin1_general_ci NOT NULL,
  `create_time` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`batch_nr`),
  KEY `send_date` (`send_date`)
)");

CreateTable('care_test_request_baclabor_sub', "CREATE TABLE IF NOT EXISTS `care_test_request_baclabor_sub` (
  `sub_id` int(40) NOT NULL auto_increment COMMENT 'primary key',
  `batch_nr` int(11) NOT NULL default '0' COMMENT 'connects to care_test_request_baclabor.batch_nr',
  `encounter_nr` int(11) NOT NULL default '0' COMMENT 'Related encounter number',
  `test_type` varchar(255) NOT NULL default '0' COMMENT 'Type of test requested',
  `test_type_value` varchar(255) NOT NULL default '0',
  `material` varchar(255) NOT NULL default '0' COMMENT 'Material notes and remarks',
  `material_value` varchar(255) NOT NULL default '0',
  PRIMARY KEY  (`sub_id`)
)");

CreateTable('care_test_findings_chemlab', "CREATE TABLE IF NOT EXISTS `care_test_findings_chemlab` (
  `batch_nr` int(11) NOT NULL auto_increment,
  `encounter_nr` int(11) NOT NULL default '0',
  `job_id` varchar(25) collate latin1_general_ci NOT NULL,
  `test_date` date NOT NULL default '0000-00-00',
  `test_time` time NOT NULL default '00:00:00',
  `group_id` varchar(30) collate latin1_general_ci NOT NULL,
  `serial_value` text collate latin1_general_ci NOT NULL,
  `validator` varchar(15) collate latin1_general_ci NOT NULL,
  `validate_dt` datetime NOT NULL default '0000-00-00 00:00:00',
  `status` varchar(20) collate latin1_general_ci NOT NULL,
  `history` text collate latin1_general_ci NOT NULL,
  `modify_id` varchar(35) collate latin1_general_ci NOT NULL,
  `modify_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `create_id` varchar(35) collate latin1_general_ci NOT NULL,
  `create_time` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`batch_nr`,`encounter_nr`,`job_id`)
)");

CreateTable('care_test_findings_chemlabor_sub', "CREATE TABLE IF NOT EXISTS `care_test_findings_chemlabor_sub` (
  `sub_id` int(40) NOT NULL auto_increment,
  `batch_nr` int(11) NOT NULL default '0',
  `job_id` varchar(25) character set latin1 collate latin1_general_ci NOT NULL default '0',
  `encounter_nr` int(11) NOT NULL default '0',
  `paramater_name` varchar(255) default '0',
  `parameter_value` varchar(255) default '0',
  `status` varchar(255) character set latin1 collate latin1_general_ci default NULL,
  `history` text character set latin1 collate latin1_general_ci,
  `test_date` date NOT NULL default '0000-00-00',
  `test_time` time default NULL,
  `create_id` varchar(35) character set latin1 collate latin1_general_ci default NULL,
  `create_time` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`sub_id`)
)");

UpdateDBNo(basename(__FILE__, '.php'));

?>