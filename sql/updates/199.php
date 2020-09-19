<?php
NewModule('hospital', 'hosp', 'Hospital', 9);

NewScript('KCMCHospitalConfiguration.php', 15);
NewMenuItem('system', 'Transactions', _('Hospital Configuration Options'), '/KCMCHospitalConfiguration.php', 3);

NewConfigValue('DispenseOnBill', 0);
NewConfigValue('CanAmendBill', 0);
NewConfigValue('DefaultArea', '');
NewConfigValue('DefaultSalesPerson', '');
NewConfigValue('AutoPatientNo', 1);
NewConfigValue('InsuranceDebtorType', '');

NewSysType(520, 'Auto Patient Number');

NewScript('KCMCRegister.php', 15);
NewMenuItem('hospital', 'Transactions', _('Register a new patient'), '/KCMCRegister.php', 1);

CreateTable('care_person', "CREATE TABLE `care_person` (
  `pid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hospital_file_nr` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL COMMENT 'placeholder for existing individual hospital file number system',
  `date_reg` datetime DEFAULT '0000-00-00 00:00:00',
  `name_first` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `name_2` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `name_3` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `name_middle` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `name_last` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `name_maiden` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `name_others` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `date_birth` date DEFAULT '0000-00-00',
  `blood_group` char(2) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `addr_str` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `addr_str_nr` varchar(10) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `addr_zip` varchar(15) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `addr_citytown_nr` mediumint(8) unsigned DEFAULT '0',
  `addr_is_valid` tinyint(1) DEFAULT '0',
  `citizenship` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `phone_1_code` varchar(15) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT '0',
  `phone_1_nr` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `phone_2_code` varchar(15) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT '0',
  `phone_2_nr` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `cellphone_1_nr` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `cellphone_2_nr` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `fax` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `email` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `civil_status` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `sex` char(1) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `title` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `photo` blob,
  `photo_filename` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `ethnic_orig` mediumint(8) unsigned DEFAULT NULL,
  `org_id` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `sss_nr` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `nat_id_nr` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `religion` varchar(125) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `mother_pid` int(10) unsigned DEFAULT '0',
  `father_pid` int(10) unsigned DEFAULT '0',
  `contact_person` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `contact_pid` int(10) unsigned DEFAULT '0',
  `contact_relation` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT '0',
  `death_date` date DEFAULT '0000-00-00',
  `death_encounter_nr` int(10) unsigned DEFAULT '0',
  `death_cause` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `death_cause_code` varchar(15) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `date_update` datetime DEFAULT NULL,
  `status` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `history` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `modify_id` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `modify_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_id` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `relative_name_first` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `relative_name_last` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `relative_phone` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`pid`),
  KEY `name_last` (`name_last`),
  KEY `name_first` (`name_first`),
  KEY `date_reg` (`date_reg`),
  KEY `date_birth` (`date_birth`),
  KEY `pid` (`pid`)
)");

AddColumn('medical', 'departments', 'TINYINT', 'NOT NULL', 0, 'description');

NewScript('KCMCOutpatientAdmission.php', 15);
NewMenuItem('hospital', 'Transactions', _('Admit an outpatient'), '/KCMCOutpatientAdmission.php', 2);

NewScript('KCMCInpatientAdmission.php', 15);
NewMenuItem('hospital', 'Transactions', _('Admit an inpatient'), '/KCMCInpatientAdmission.php', 3);

CreateTable('care_type_encounter', "CREATE TABLE `care_type_encounter` (
  `type_nr` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `name` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `LD_var` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '0',
  `description` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `hide_from` tinyint(4) NOT NULL DEFAULT '0',
  `status` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `history` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `modify_id` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `modify_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_id` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`type_nr`)
)");
InsertRecord('care_type_encounter', array('type'), array('referral'), array('type_nr', 'type', 'name', 'LD_var', 'description', 'hide_from', 'status', 'history', 'modify_id', 'modify_time', 'create_id', 'create_time'), array(1, 'referral', 'Referral', 'LDEncounterReferral', 'Referral admission or visit', 0, '0', '', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00'));
InsertRecord('care_type_encounter', array('type'), array('emergency'), array('type_nr', 'type', 'name', 'LD_var', 'description', 'hide_from', 'status', 'history', 'modify_id', 'modify_time', 'create_id', 'create_time'), array(2, 'emergency', 'Emergency', 'LDEmergency', 'Emergency admission or visit', 0, '0', '', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00'));
InsertRecord('care_type_encounter', array('type'), array('birth_delivery'), array('type_nr', 'type', 'name', 'LD_var', 'description', 'hide_from', 'status', 'history', 'modify_id', 'modify_time', 'create_id', 'create_time'), array(3, 'birth_delivery', 'Birth delivery', 'LDBirthDelivery', 'Admission or visit for birth delivery', 0, '0', '', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00'));
InsertRecord('care_type_encounter', array('type'), array('walk_in'), array('type_nr', 'type', 'name', 'LD_var', 'description', 'hide_from', 'status', 'history', 'modify_id', 'modify_time', 'create_id', 'create_time'), array(4, 'walk_in', 'Walk-in', 'LDWalkIn', 'Walk -in admission or visit (non-referred)', 0, '0', '', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00'));
InsertRecord('care_type_encounter', array('type'), array('accident'), array('type_nr', 'type', 'name', 'LD_var', 'description', 'hide_from', 'status', 'history', 'modify_id', 'modify_time', 'create_id', 'create_time'), array(5, 'accident', 'Accident', 'LDAccident', 'Emergency admission due to accident', 0, '0', '', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00'));

CreateTable('care_encounter', "CREATE TABLE `care_encounter` (
  `encounter_nr` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `encounter_date` datetime DEFAULT '0000-00-00 00:00:00',
  `encounter_class_nr` smallint(5) unsigned DEFAULT '0',
  `encounter_type` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `encounter_status` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `referrer_diagnosis` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `referrer_recom_therapy` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `referrer_dr` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `referrer_dept` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `referrer_institution` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `referrer_notes` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `regional_code` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `triage` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT 'white',
  `admit_type` int(10) DEFAULT '0',
  `financial_class_nr` tinyint(3) unsigned DEFAULT '0',
  `insurance_nr` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT '0',
  `insurance_firm_id` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT '0',
  `insurance_class_nr` tinyint(3) unsigned DEFAULT '0',
  `insurance_2_nr` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT '0',
  `insurance_2_firm_id` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT '0',
  `guarantor_pid` int(11) DEFAULT '0',
  `contact_pid` int(11) DEFAULT '0',
  `contact_relation` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `current_ward_nr` smallint(3) unsigned DEFAULT '0',
  `current_room_nr` smallint(5) unsigned DEFAULT '0',
  `in_ward` tinyint(1) DEFAULT '0',
  `current_dept_nr` smallint(3) unsigned DEFAULT '0',
  `in_dept` tinyint(1) DEFAULT '0',
  `current_firm_nr` smallint(5) unsigned DEFAULT '0',
  `current_att_dr_nr` int(10) DEFAULT '0',
  `consulting_dr` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `extra_service` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `is_discharged` tinyint(1) unsigned DEFAULT '0',
  `discharge_date` date DEFAULT NULL,
  `discharge_time` time DEFAULT NULL,
  `followup_date` date DEFAULT '0000-00-00',
  `followup_responsibility` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `post_encounter_notes` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `status` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `history` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `modify_id` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `modify_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_id` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`encounter_nr`),
  KEY `pid` (`pid`),
  KEY `encounter_date` (`encounter_date`),
  CONSTRAINT `fk_care_person_encounter` FOREIGN KEY (`pid`) REFERENCES `care_person` (`pid`)
)");

NewScript('KCMCMaintainEncounterTypes.php', 15);
NewMenuItem('hospital', 'Maintenance', _('Maintain Encounter Types'), '/KCMCMaintainEncounterTypes.php', 1);

CreateTable('care_ward', "CREATE TABLE `care_ward` (
  `nr` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `ward_id` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `name` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `is_temp_closed` tinyint(1) NOT NULL DEFAULT '0',
  `date_create` date NOT NULL DEFAULT '0000-00-00',
  `date_close` date NOT NULL DEFAULT '0000-00-00',
  `description` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `info` tinytext CHARACTER SET latin1 COLLATE latin1_general_ci,
  `dept_nr` smallint(5) unsigned NOT NULL DEFAULT '0',
  `room_nr_start` smallint(6) NOT NULL DEFAULT '0',
  `room_nr_end` smallint(6) NOT NULL DEFAULT '0',
  `roomprefix` varchar(4) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `status` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `history` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `modify_id` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '0',
  `modify_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_id` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`nr`),
  KEY `ward_id` (`ward_id`)
)");

NewScript('KCMCMaintainWards.php', 15);
NewMenuItem('hospital', 'Maintenance', _('Maintain Wards'), '/KCMCMaintainWards.php', 2);

CreateTable('care_room', "CREATE TABLE `care_room` (
  `nr` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `type_nr` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_create` date NOT NULL DEFAULT '0000-00-00',
  `date_close` date NOT NULL DEFAULT '0000-00-00',
  `is_temp_closed` tinyint(1) DEFAULT '0',
  `room_nr` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ward_nr` smallint(5) unsigned NOT NULL DEFAULT '0',
  `dept_nr` smallint(5) unsigned NOT NULL DEFAULT '0',
  `nr_of_beds` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `closed_beds` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `info` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `status` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `history` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `modify_id` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `modify_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_id` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`nr`,`type_nr`,`ward_nr`,`dept_nr`),
  KEY `room_nr` (`room_nr`),
  KEY `ward_nr` (`ward_nr`),
  KEY `dept_nr` (`dept_nr`)
)");

NewScript('KCMCMaintainWardRooms.php', 15);

NewScript('KCMCWardOverview.php', 15);
NewMenuItem('hospital', 'Reports', _('Ward Overview'), '/KCMCWardOverview.php', 1);

NewScript('KCMCAllocatePatientsToBeds.php', 15);

CreateTable('care_type_location', "CREATE TABLE `care_type_location` (
  `nr` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `name` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `LD_var` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `description` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `status` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `modify_id` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `modify_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_id` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`nr`)
)");

InsertRecord('care_type_location', array('nr'), array(1), array('nr', 'type', 'name', 'LD_var', 'description', 'status', 'modify_id', 'modify_time', 'create_id', 'create_time'), array(1, 'dept', 'Department', 'LDDepartment', '', '', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00'));
InsertRecord('care_type_location', array('nr'), array(2), array('nr', 'type', 'name', 'LD_var', 'description', 'status', 'modify_id', 'modify_time', 'create_id', 'create_time'), array(2, 'ward', 'Ward', 'LDWard', '', '', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00'));
InsertRecord('care_type_location', array('nr'), array(3), array('nr', 'type', 'name', 'LD_var', 'description', 'status', 'modify_id', 'modify_time', 'create_id', 'create_time'), array(3, 'firm', 'Firm', 'LDFirm', '', '', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00'));
InsertRecord('care_type_location', array('nr'), array(4), array('nr', 'type', 'name', 'LD_var', 'description', 'status', 'modify_id', 'modify_time', 'create_id', 'create_time'), array(4, 'room', 'Room', 'LDRoom', '', '', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00'));
InsertRecord('care_type_location', array('nr'), array(5), array('nr', 'type', 'name', 'LD_var', 'description', 'status', 'modify_id', 'modify_time', 'create_id', 'create_time'), array(5, 'bed', 'Bed', 'LDBed', '', '', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00'));
InsertRecord('care_type_location', array('nr'), array(6), array('nr', 'type', 'name', 'LD_var', 'description', 'status', 'modify_id', 'modify_time', 'create_id', 'create_time'), array(6, 'clinic', 'Clinic', 'LDClinic', '', '', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00'));

CreateTable('care_encounter_location', "CREATE TABLE `care_encounter_location` (
  `nr` int(11) NOT NULL AUTO_INCREMENT,
  `encounter_nr` int(11) unsigned NOT NULL DEFAULT '0',
  `type_nr` smallint(5) unsigned NOT NULL DEFAULT '0',
  `location_nr` smallint(5) unsigned NOT NULL DEFAULT '0',
  `group_nr` smallint(5) unsigned NOT NULL DEFAULT '0',
  `date_from` date NOT NULL DEFAULT '0000-00-00',
  `date_to` date NOT NULL DEFAULT '0000-00-00',
  `time_from` time DEFAULT '00:00:00',
  `time_to` time DEFAULT NULL,
  `discharge_type_nr` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `history` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `modify_id` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `modify_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_id` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`nr`,`location_nr`),
  KEY `type` (`type_nr`),
  KEY `location_id` (`location_nr`),
  KEY `encounter_nr` (`encounter_nr`),
  KEY `location_nr` (`location_nr`)
)");

UpdateDBNo(basename(__FILE__, '.php'));

?>