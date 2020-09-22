<?php
NewScript('KCMCBloodLaboratory.php', 13);
NewMenuItem('hospital', 'Reports', _('Blood Laboratory Details'), '/KCMCBloodLaboratory.php', 5);

NewScript('KCMCRequestBloodTest.php', 13);
NewScript('KCMCPendingBloodTests.php', 13);

CreateTable('care_test_request_blood', "CREATE TABLE IF NOT EXISTS `care_test_request_blood` (
  `batch_nr` int(11) NOT NULL auto_increment,
  `encounter_nr` int(11) unsigned NOT NULL default '0',
  `dept_nr` smallint(5) unsigned NOT NULL default '0',
  `blood_group` varchar(10) collate latin1_general_ci NOT NULL,
  `rh_factor` varchar(10) collate latin1_general_ci NOT NULL,
  `kell` varchar(10) collate latin1_general_ci NOT NULL,
  `date_protoc_nr` varchar(45) collate latin1_general_ci NOT NULL,
  `pure_blood` varchar(15) collate latin1_general_ci NOT NULL,
  `red_blood` varchar(15) collate latin1_general_ci NOT NULL,
  `leukoless_blood` varchar(15) collate latin1_general_ci NOT NULL,
  `washed_blood` varchar(15) collate latin1_general_ci NOT NULL,
  `prp_blood` varchar(15) collate latin1_general_ci NOT NULL,
  `thrombo_con` varchar(15) collate latin1_general_ci NOT NULL,
  `ffp_plasma` varchar(15) collate latin1_general_ci NOT NULL,
  `transfusion_dev` varchar(15) collate latin1_general_ci NOT NULL,
  `match_sample` tinyint(4) NOT NULL default '0',
  `transfusion_date` date NOT NULL default '0000-00-00',
  `diagnosis` tinytext collate latin1_general_ci NOT NULL,
  `notes` tinytext collate latin1_general_ci NOT NULL,
  `send_date` date NOT NULL default '0000-00-00',
  `doctor` varchar(35) collate latin1_general_ci NOT NULL,
  `phone_nr` varchar(40) collate latin1_general_ci NOT NULL,
  `status` varchar(10) collate latin1_general_ci NOT NULL,
  `blood_pb` tinytext collate latin1_general_ci NOT NULL,
  `blood_rb` tinytext collate latin1_general_ci NOT NULL,
  `blood_llrb` tinytext collate latin1_general_ci NOT NULL,
  `blood_wrb` tinytext collate latin1_general_ci NOT NULL,
  `blood_prp` tinyblob NOT NULL,
  `blood_tc` tinytext collate latin1_general_ci NOT NULL,
  `blood_ffp` tinytext collate latin1_general_ci NOT NULL,
  `b_group_count` mediumint(9) NOT NULL default '0',
  `b_group_price` float(10,2) NOT NULL default '0.00',
  `a_subgroup_count` mediumint(9) NOT NULL default '0',
  `a_subgroup_price` float(10,2) NOT NULL default '0.00',
  `extra_factors_count` mediumint(9) NOT NULL default '0',
  `extra_factors_price` float(10,2) NOT NULL default '0.00',
  `coombs_count` mediumint(9) NOT NULL default '0',
  `coombs_price` float(10,2) NOT NULL default '0.00',
  `ab_test_count` mediumint(9) NOT NULL default '0',
  `ab_test_price` float(10,2) NOT NULL default '0.00',
  `crosstest_count` mediumint(9) NOT NULL default '0',
  `crosstest_price` float(10,2) NOT NULL default '0.00',
  `ab_diff_count` mediumint(9) NOT NULL default '0',
  `ab_diff_price` float(10,2) NOT NULL default '0.00',
  `x_test_1_code` mediumint(9) NOT NULL default '0',
  `x_test_1_name` varchar(35) collate latin1_general_ci NOT NULL,
  `x_test_1_count` mediumint(9) NOT NULL default '0',
  `x_test_1_price` float(10,2) NOT NULL default '0.00',
  `x_test_2_code` mediumint(9) NOT NULL default '0',
  `x_test_2_name` varchar(35) collate latin1_general_ci NOT NULL,
  `x_test_2_count` mediumint(9) NOT NULL default '0',
  `x_test_2_price` float(10,2) NOT NULL default '0.00',
  `x_test_3_code` mediumint(9) NOT NULL default '0',
  `x_test_3_name` varchar(35) collate latin1_general_ci NOT NULL,
  `x_test_3_count` mediumint(9) NOT NULL default '0',
  `x_test_3_price` float(10,2) NOT NULL default '0.00',
  `lab_stamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `release_via` varchar(20) collate latin1_general_ci NOT NULL,
  `receipt_ack` varchar(20) collate latin1_general_ci NOT NULL,
  `mainlog_nr` varchar(7) collate latin1_general_ci NOT NULL,
  `lab_nr` varchar(7) collate latin1_general_ci NOT NULL,
  `mainlog_date` date NOT NULL default '0000-00-00',
  `lab_date` date NOT NULL default '0000-00-00',
  `mainlog_sign` varchar(20) collate latin1_general_ci NOT NULL,
  `lab_sign` varchar(20) collate latin1_general_ci NOT NULL,
  `history` text collate latin1_general_ci,
  `modify_id` varchar(35) collate latin1_general_ci NOT NULL,
  `modify_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `create_id` varchar(35) collate latin1_general_ci NOT NULL,
  `create_time` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`batch_nr`),
  KEY `send_date` (`send_date`)
)");

SetAutoIncStart('care_person', 'pid', 100000000);
SetAutoIncStart('care_encounter', 'encounter_nr', 200000000);

SetAutoIncStart('care_test_request_chemlabor', 'batch_nr', 400000000);
SetAutoIncStart('care_test_request_baclabor', 'batch_nr', 400000000);
SetAutoIncStart('care_test_request_patho', 'batch_nr', 400000000);
SetAutoIncStart('care_test_request_blood', 'batch_nr', 400000000);

UpdateDBNo(basename(__FILE__, '.php'));

?>