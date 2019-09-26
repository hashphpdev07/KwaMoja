<?php
CreateTable('prlemphdmffile', "CREATE TABLE prlemphdmffile (
  counterindex int(11) NOT NULL auto_increment,
  payrollid varchar(10) NOT NULL default '',
  employeeid varchar(10) NOT NULL default '',
  grosspay decimal(12,2) NOT NULL default '0.00',
  employerhdmf decimal(12,2) NOT NULL default '0.00',
  employeehdmf decimal(12,2) NOT NULL default '0.00',
  total decimal(12,2) NOT NULL default '0.00',
  fsmonth tinyint(4) NOT NULL default '0',
  fsyear double NOT NULL default '0',
  PRIMARY KEY  (counterindex)
)");

CreateTable('prlemptaxfile', "CREATE TABLE prlemptaxfile (
  counterindex int(11) NOT NULL auto_increment,
  payrollid varchar(10) NOT NULL default '',
  employeeid varchar(10) NOT NULL default '',
  taxableincome decimal(12,2) NOT NULL default '0.00',
  tax decimal(12,2) NOT NULL default '0.00',
  fsmonth tinyint(4) NOT NULL default '0',
  fsyear double NOT NULL default '0',
  PRIMARY KEY  (counterindex)
)");

CreateTable('prlempphfile', "CREATE TABLE prlempphfile (
  counterindex int(11) NOT NULL auto_increment,
  payrollid varchar(10) NOT NULL default '',
  employeeid varchar(10) NOT NULL default '',
  grosspay decimal(12,2) NOT NULL default '0.00',
  rangefrom decimal(12,2) NOT NULL default '0.00',
  rangeto decimal(12,2) NOT NULL default '0.00',
  salarycredit decimal(12,2) NOT NULL default '0.00',
  employerph decimal(12,2) NOT NULL default '0.00',
  employerec decimal(12,2) NOT NULL default '0.00',
  employeeph decimal(12,2) NOT NULL default '0.00',
  total decimal(12,2) NOT NULL default '0.00',
  fsmonth tinyint(4) NOT NULL default '0',
  fsyear double NOT NULL default '0',
  PRIMARY KEY  (counterindex)
)");

CreateTable('prlempsssfile', "CREATE TABLE prlempsssfile (
  counterindex int(11) NOT NULL auto_increment,
  payrollid varchar(10) NOT NULL default '',
  employeeid varchar(10) NOT NULL default '',
  grosspay decimal(12,2) NOT NULL default '0.00',
  rangefrom decimal(12,2) NOT NULL default '0.00',
  rangeto decimal(12,2) NOT NULL default '0.00',
  salarycredit decimal(12,2) NOT NULL default '0.00',
  employerss decimal(12,2) NOT NULL default '0.00',
  employerec decimal(12,2) NOT NULL default '0.00',
  employeess decimal(12,2) NOT NULL default '0.00',
  total decimal(12,2) NOT NULL default '0.00',
  fsmonth tinyint(4) NOT NULL default '0',
  fsyear double NOT NULL default '0',
  PRIMARY KEY  (counterindex)
)");

CreateTable('prlhdmftable', "CREATE TABLE IF NOT EXISTS `prlhdmftable` (
  `bracket` tinyint(4) NOT NULL default '0',
  `rangefrom` decimal(12,2) NOT NULL default '0.00',
  `rangeto` decimal(12,2) NOT NULL default '0.00',
  `dedtypeer` varchar(10) collate latin1_general_ci NOT NULL default '',
  `employershare` decimal(12,2) NOT NULL default '0.00',
  `dedtypeee` varchar(10) collate latin1_general_ci NOT NULL default '',
  `employeeshare` decimal(12,2) NOT NULL default '0.00',
  PRIMARY KEY  (`bracket`)
)");

InsertRecord('prlhdmftable', array('bracket'), array(1), array('bracket', 'rangefrom', 'rangeto', 'dedtypeer', 'employershare', 'dedtypeee', 'employeeshare'), array(1, 1.00, 1500.00, 'Percentage', 2.00, 'Percentage', 1.00));
InsertRecord('prlhdmftable', array('bracket'), array(2), array('bracket', 'rangefrom', 'rangeto', 'dedtypeer', 'employershare', 'dedtypeee', 'employeeshare'), array(2, 1500.01, 5000.00, 'Percentage', 2.00, 'Percentage', 2.00));
InsertRecord('prlhdmftable', array('bracket'), array(3), array('bracket', 'rangefrom', 'rangeto', 'dedtypeer', 'employershare', 'dedtypeee', 'employeeshare'), array(3, 5000.01, 99999999.00, 'Fixed', 100.00, 'Fixed', 100.00));

CreateTable('prlphilhealth', "CREATE TABLE IF NOT EXISTS `prlphilhealth` (
  `bracket` tinyint(4) NOT NULL default '0',
  `rangefrom` decimal(12,2) NOT NULL default '0.00',
  `rangeto` decimal(12,2) NOT NULL default '0.00',
  `salarycredit` decimal(12,2) NOT NULL default '0.00',
  `employerph` decimal(12,2) NOT NULL default '0.00',
  `employerec` decimal(12,2) NOT NULL default '0.00',
  `employeeph` decimal(12,2) NOT NULL default '0.00',
  `total` decimal(12,2) NOT NULL default '0.00',
  PRIMARY KEY  (`bracket`)
)");

InsertRecord('prlphilhealth', array('bracket'), array(1), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(1, 1.00, 4999.99, 4000.00, 50.00, 0.00, 50.00, 100.00));
InsertRecord('prlphilhealth', array('bracket'), array(2), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(2, 5000.00, 5999.99, 5000.00, 62.50, 0.00, 62.50, 125.00));
InsertRecord('prlphilhealth', array('bracket'), array(3), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(3, 6000.00, 6999.99, 6000.00, 75.00, 0.00, 75.00, 150.00));
InsertRecord('prlphilhealth', array('bracket'), array(4), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(4, 7000.00, 7999.99, 7000.00, 87.50, 0.00, 87.50, 175.00));
InsertRecord('prlphilhealth', array('bracket'), array(5), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(5, 8000.00, 8999.99, 8000.00, 100.00, 0.00, 100.00, 200.00));
InsertRecord('prlphilhealth', array('bracket'), array(6), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(6, 9000.00, 9999.99, 9000.00, 112.50, 0.00, 112.50, 225.00));
InsertRecord('prlphilhealth', array('bracket'), array(7), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(7, 10000.00, 10999.99, 10000.00, 125.00, 0.00, 125.00, 250.00));
InsertRecord('prlphilhealth', array('bracket'), array(8), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(8, 11000.00, 11999.99, 11000.00, 137.50, 0.00, 137.00, 275.00));
InsertRecord('prlphilhealth', array('bracket'), array(9), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(9, 12000.00, 12999.99, 12000.00, 150.00, 0.00, 125.00, 300.00));
InsertRecord('prlphilhealth', array('bracket'), array(10), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(10, 13000.00, 13999.99, 13000.00, 162.50, 0.00, 162.50, 325.00));
InsertRecord('prlphilhealth', array('bracket'), array(11), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(11, 14000.00, 14999.99, 14000.00, 175.00, 0.00, 175.00, 350.00));
InsertRecord('prlphilhealth', array('bracket'), array(12), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(12, 15000.00, 15999.99, 15000.00, 187.50, 0.00, 187.50, 375.00));
InsertRecord('prlphilhealth', array('bracket'), array(13), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(13, 16000.00, 16999.99, 16000.00, 200.00, 0.00, 200.00, 400.00));
InsertRecord('prlphilhealth', array('bracket'), array(14), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(14, 17000.00, 17999.99, 17000.00, 212.50, 0.00, 212.50, 425.00));
InsertRecord('prlphilhealth', array('bracket'), array(15), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(15, 18000.00, 18999.99, 18000.00, 225.00, 0.00, 225.00, 450.00));
InsertRecord('prlphilhealth', array('bracket'), array(16), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(16, 19000.00, 19999.99, 19000.00, 237.50, 0.00, 237.50, 475.00));
InsertRecord('prlphilhealth', array('bracket'), array(17), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(17, 20000.00, 20999.99, 20000.00, 250.00, 0.00, 250.00, 500.00));
InsertRecord('prlphilhealth', array('bracket'), array(18), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(18, 21000.00, 21999.99, 21000.00, 262.50, 0.00, 262.50, 525.00));
InsertRecord('prlphilhealth', array('bracket'), array(19), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(19, 22000.00, 22999.99, 22000.00, 275.00, 0.00, 275.00, 550.00));
InsertRecord('prlphilhealth', array('bracket'), array(20), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(20, 23000.00, 23999.99, 23000.00, 287.50, 0.00, 287.50, 575.00));
InsertRecord('prlphilhealth', array('bracket'), array(21), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(21, 24000.00, 24999.99, 24000.00, 300.00, 0.00, 300.00, 600.00));
InsertRecord('prlphilhealth', array('bracket'), array(22), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(22, 25000.00, 25999.99, 25000.00, 312.50, 0.00, 312.50, 625.00));
InsertRecord('prlphilhealth', array('bracket'), array(23), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(23, 26000.00, 26999.99, 26000.00, 325.00, 0.00, 325.00, 650.00));
InsertRecord('prlphilhealth', array('bracket'), array(24), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(24, 27000.00, 27999.99, 27000.00, 337.50, 0.00, 337.50, 675.00));
InsertRecord('prlphilhealth', array('bracket'), array(25), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(25, 28000.00, 28999.99, 28000.00, 350.00, 0.00, 350.00, 700.00));
InsertRecord('prlphilhealth', array('bracket'), array(26), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(26, 29000.00, 29999.99, 29000.00, 362.50, 0.00, 362.50, 725.00));
InsertRecord('prlphilhealth', array('bracket'), array(27), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerph', 'employerec', 'employeeph', 'total'), array(27, 30000.00, 99999.99, 30000.00, 375.00, 0.00, 375.00, 750.00));

CreateTable('prlsstable', "CREATE TABLE IF NOT EXISTS `prlsstable` (
  `bracket` tinyint(4) NOT NULL default '0',
  `rangefrom` decimal(12,2) NOT NULL default '0.00',
  `rangeto` decimal(12,2) NOT NULL default '0.00',
  `salarycredit` decimal(12,2) NOT NULL default '0.00',
  `employerss` decimal(12,2) NOT NULL default '0.00',
  `employerec` decimal(12,2) NOT NULL default '0.00',
  `employeess` decimal(12,2) NOT NULL default '0.00',
  `total` decimal(12,2) NOT NULL default '0.00',
  PRIMARY KEY  (`bracket`)
)");

InsertRecord('prlsstable', array('bracket'), array(1), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(1, 1000.00, 1249.99, 1000.00, 70.70, 10.00, 33.30, 114.00));
InsertRecord('prlsstable', array('bracket'), array(2), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(2, 1250.00, 1749.99, 1500.00, 106.00, 10.00, 50.00, 166.00));
InsertRecord('prlsstable', array('bracket'), array(3), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(3, 1750.00, 2249.99, 2000.00, 141.30, 10.00, 66.70, 218.00));
InsertRecord('prlsstable', array('bracket'), array(4), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(4, 2250.00, 2749.99, 2500.00, 176.70, 10.00, 83.30, 270.00));
InsertRecord('prlsstable', array('bracket'), array(5), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(5, 2750.00, 3249.99, 3000.00, 212.00, 10.00, 100.00, 322.00));
InsertRecord('prlsstable', array('bracket'), array(6), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(6, 3250.00, 3749.99, 3500.00, 247.30, 10.00, 116.70, 374.00));
InsertRecord('prlsstable', array('bracket'), array(7), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(7, 3750.00, 4249.99, 4000.00, 282.70, 10.00, 133.30, 426.00));
InsertRecord('prlsstable', array('bracket'), array(8), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(8, 4250.00, 4749.99, 4500.00, 318.00, 10.00, 150.00, 478.00));
InsertRecord('prlsstable', array('bracket'), array(9), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(9, 4750.00, 5249.99, 5000.00, 353.30, 10.00, 166.70, 530.00));
InsertRecord('prlsstable', array('bracket'), array(10), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(10, 5250.00, 5749.99, 5500.00, 388.70, 10.00, 183.30, 582.00));
InsertRecord('prlsstable', array('bracket'), array(11), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(11, 5750.00, 6249.99, 6000.00, 424.00, 10.00, 200.00, 634.00));
InsertRecord('prlsstable', array('bracket'), array(12), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(12, 6250.00, 6749.99, 6500.00, 459.30, 10.00, 216.70, 686.00));
InsertRecord('prlsstable', array('bracket'), array(13), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(13, 6750.00, 7249.99, 7000.00, 494.70, 10.00, 233.30, 738.00));
InsertRecord('prlsstable', array('bracket'), array(14), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(14, 7250.00, 7749.99, 7500.00, 530.00, 10.00, 250.00, 790.00));
InsertRecord('prlsstable', array('bracket'), array(15), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(15, 7750.00, 8249.99, 8000.00, 565.30, 10.00, 266.70, 842.00));
InsertRecord('prlsstable', array('bracket'), array(16), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(16, 8250.00, 8749.99, 8500.00, 600.70, 10.00, 283.30, 894.00));
InsertRecord('prlsstable', array('bracket'), array(17), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(17, 8750.00, 9249.99, 9000.00, 636.00, 10.00, 300.00, 946.00));
InsertRecord('prlsstable', array('bracket'), array(18), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(18, 9250.00, 9749.99, 9500.00, 671.30, 10.00, 316.70, 998.00));
InsertRecord('prlsstable', array('bracket'), array(19), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(19, 9750.00, 10249.99, 10000.00, 706.70, 10.00, 333.30, 1050.00));
InsertRecord('prlsstable', array('bracket'), array(20), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(20, 10250.00, 10749.99, 10500.00, 742.00, 10.00, 350.00, 1102.00));
InsertRecord('prlsstable', array('bracket'), array(21), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(21, 10750.00, 11249.99, 11000.00, 777.30, 10.00, 366.70, 1154.00));
InsertRecord('prlsstable', array('bracket'), array(22), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(22, 11250.00, 11749.99, 11500.00, 812.70, 10.00, 383.30, 1206.00));
InsertRecord('prlsstable', array('bracket'), array(23), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(23, 11750.00, 12249.99, 12000.00, 848.00, 10.00, 400.00, 1258.00));
InsertRecord('prlsstable', array('bracket'), array(24), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(24, 12250.00, 12749.99, 12500.00, 883.30, 10.00, 416.70, 1310.00));
InsertRecord('prlsstable', array('bracket'), array(25), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(25, 12750.00, 13249.99, 13000.00, 918.70, 10.00, 433.30, 1362.00));
InsertRecord('prlsstable', array('bracket'), array(26), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(26, 13250.00, 13749.99, 13500.00, 954.00, 10.00, 450.00, 1414.00));
InsertRecord('prlsstable', array('bracket'), array(27), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(27, 13750.00, 14249.99, 14000.00, 989.30, 10.00, 466.70, 1466.00));
InsertRecord('prlsstable', array('bracket'), array(28), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(28, 14250.00, 14749.99, 14500.00, 1024.70, 10.00, 483.30, 1518.00));
InsertRecord('prlsstable', array('bracket'), array(29), array('bracket', 'rangefrom', 'rangeto', 'salarycredit', 'employerss', 'employerec', 'employeess', 'total'), array(29, 14750.00, 999999.00, 15000.00, 1060.00, 30.00, 500.00, 1590.00));

CreateTable('prltaxstatus', "CREATE TABLE IF NOT EXISTS `prltaxstatus` (
  `taxstatusid` varchar(10) collate latin1_general_ci NOT NULL default '',
  `taxstatusdescription` varchar(40) collate latin1_general_ci NOT NULL default '',
  `personalexemption` decimal(12,2) NOT NULL default '0.00',
  `additionalexemption` decimal(12,2) NOT NULL default '0.00',
  `totalexemption` decimal(12,2) NOT NULL default '0.00',
  PRIMARY KEY  (`taxstatusid`)
)");

InsertRecord('prltaxstatus', array('taxstatusid'), array('S'), array('taxstatusid', 'taxstatusdescription', 'personalexemption', 'additionalexemption', 'totalexemption'), array('S', 'Single', 20000.00, 0.00, 20000.00));
InsertRecord('prltaxstatus', array('taxstatusid'), array('HF'), array('taxstatusid', 'taxstatusdescription', 'personalexemption', 'additionalexemption', 'totalexemption'), array('HF', 'Head of the Family', 25000.00, 0.00, 25000.00));
InsertRecord('prltaxstatus', array('taxstatusid'), array('ME'), array('taxstatusid', 'taxstatusdescription', 'personalexemption', 'additionalexemption', 'totalexemption'), array('ME', 'Married', 32000.00, 0.00, 32000.00));
InsertRecord('prltaxstatus', array('taxstatusid'), array('HF1'), array('taxstatusid', 'taxstatusdescription', 'personalexemption', 'additionalexemption', 'totalexemption'), array('HF1', 'Head of the Family with 1 dependent', 25000.00, 8000.00, 33000.00));
InsertRecord('prltaxstatus', array('taxstatusid'), array('HF2'), array('taxstatusid', 'taxstatusdescription', 'personalexemption', 'additionalexemption', 'totalexemption'), array('HF2', 'Head of the Family with 2 dependent', 25000.00, 16000.00, 41000.00));
InsertRecord('prltaxstatus', array('taxstatusid'), array('HF3'), array('taxstatusid', 'taxstatusdescription', 'personalexemption', 'additionalexemption', 'totalexemption'), array('HF3', 'Head of the Family with 3 dependent', 25000.00, 24000.00, 49000.00));
InsertRecord('prltaxstatus', array('taxstatusid'), array('HF4'), array('taxstatusid', 'taxstatusdescription', 'personalexemption', 'additionalexemption', 'totalexemption'), array('HF4', 'Head of the Family with 4 dependent', 25000.00, 32000.00, 57000.00));
InsertRecord('prltaxstatus', array('taxstatusid'), array('ME1'), array('taxstatusid', 'taxstatusdescription', 'personalexemption', 'additionalexemption', 'totalexemption'), array('ME1', 'Married with 1 dependent', 32000.00, 8000.00, 40000.00));
InsertRecord('prltaxstatus', array('taxstatusid'), array('ME2'), array('taxstatusid', 'taxstatusdescription', 'personalexemption', 'additionalexemption', 'totalexemption'), array('ME2', 'Married with 2 dependent', 32000.00, 16000.00, 48000.00));
InsertRecord('prltaxstatus', array('taxstatusid'), array('ME3'), array('taxstatusid', 'taxstatusdescription', 'personalexemption', 'additionalexemption', 'totalexemption'), array('ME3', 'Married with 3 dependent', 32000.00, 24000.00, 56000.00));
InsertRecord('prltaxstatus', array('taxstatusid'), array('ME4'), array('taxstatusid', 'taxstatusdescription', 'personalexemption', 'additionalexemption', 'totalexemption'), array('ME4', 'Married with 4 dependent', 32000.00, 32000.00, 64000.00));
InsertRecord('prltaxstatus', array('taxstatusid'), array('Z'), array('taxstatusid', 'taxstatusdescription', 'personalexemption', 'additionalexemption', 'totalexemption'), array('Z', 'Zero Exemption', 0.00, 0.00, 0.00));

CreateTable('prltaxtablerate', "CREATE TABLE IF NOT EXISTS `prltaxtablerate` (
  `bracket` tinyint(4) NOT NULL default '0',
  `rangefrom` decimal(12,2) NOT NULL default '0.00',
  `rangeto` decimal(12,2) NOT NULL default '0.00',
  `fixtaxableamount` decimal(12,2) NOT NULL default '0.00',
  `fixtax` decimal(12,2) NOT NULL default '0.00',
  `percentofexcessamount` double NOT NULL default '1',
  PRIMARY KEY  (`bracket`)
)");

InsertRecord('prltaxtablerate', array('bracket'), array(1), array('bracket', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(1, 0.00, 9999.99, 0.00, 0.00, 5));
InsertRecord('prltaxtablerate', array('bracket'), array(2), array('bracket', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(2, 10000.00, 29999.99, 10000.00, 500.00, 10));
InsertRecord('prltaxtablerate', array('bracket'), array(3), array('bracket', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(3, 30000.00, 69999.99, 30000.00, 2500.00, 15));
InsertRecord('prltaxtablerate', array('bracket'), array(4), array('bracket', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(4, 70000.00, 139999.99, 70000.00, 8500.00, 20));
InsertRecord('prltaxtablerate', array('bracket'), array(5), array('bracket', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(5, 140000.00, 249999.99, 140000.00, 22500.00, 25));
InsertRecord('prltaxtablerate', array('bracket'), array(6), array('bracket', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(6, 250000.00, 499999.99, 250000.00, 50000.00, 30));
InsertRecord('prltaxtablerate', array('bracket'), array(7), array('bracket', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(7, 500000.00, 99999999.99, 500000.00, 125000.00, 32));

CreateTable('prltaxtablerate2', "CREATE TABLE IF NOT EXISTS `prltaxtablerate2` (
  `counterindex` int(11) NOT NULL auto_increment,
  `payperiodid` tinyint(4) NOT NULL default '0',
  `taxstatusid` varchar(10) collate latin1_general_ci NOT NULL default '',
  `rangefrom` decimal(12,2) NOT NULL default '0.00',
  `rangeto` decimal(12,2) NOT NULL default '0.00',
  `fixtaxableamount` decimal(12,2) NOT NULL default '0.00',
  `fixtax` decimal(12,2) NOT NULL default '0.00',
  `percentofexcessamount` double NOT NULL default '1',
  PRIMARY KEY  (`counterindex`,`payperiodid`)
)");

InsertRecord('prltaxtablerate2', array('counterindex'), array(1), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(1, 10, 'Z', 0.00, 416.99, 0.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(2), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(2, 10, 'Z', 417.00, 1249.99, 417.00, 20.83, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(3), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(3, 10, 'Z', 1250.00, 2916.99, 1250.00, 104.17, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(4), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(4, 10, 'Z', 2917.00, 5832.99, 2917.00, 354.17, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(5), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(5, 10, 'Z', 5833.00, 10416.99, 5833.00, 937.50, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(6), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(6, 10, 'Z', 10417.00, 20832.99, 10417.00, 2083.33, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(7), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(7, 10, 'Z', 20833.00, 99999999.99, 20833.00, 5208.33, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(8), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(8, 10, 'S', 833.00, 1249.99, 833.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(9), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(9, 10, 'S', 1250.00, 2082.99, 1250.00, 20.83, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(10), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(10, 10, 'S', 2083.00, 3749.99, 2083.00, 104.17, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(11), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(11, 10, 'S', 3750.00, 6666.99, 3750.00, 354.17, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(12), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(12, 10, 'S', 6667.00, 11249.99, 6667.00, 937.50, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(13), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(13, 10, 'S', 11250.00, 21666.99, 11250.00, 2083.33, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(14), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(14, 10, 'S', 21667.00, 99999999.99, 21667.00, 5208.33, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(15), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(15, 10, 'HF', 1042.00, 1457.99, 1042.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(16), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(16, 10, 'HF', 1458.00, 2291.99, 1458.00, 20.83, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(17), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(17, 10, 'HF', 2292.00, 3957.99, 2292.00, 104.17, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(18), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(18, 10, 'HF', 3958.00, 6874.99, 3958.00, 354.17, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(19), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(19, 10, 'HF', 6875.00, 11457.99, 6875.00, 937.50, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(20), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(20, 10, 'HF', 11458.00, 21874.99, 11458.00, 2083.33, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(21), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(21, 10, 'HF', 21875.00, 99999999.99, 21875.00, 5208.33, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(22), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(22, 10, 'ME', 1333.00, 1749.99, 1333.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(23), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(23, 10, 'ME', 1750.00, 2582.99, 1750.00, 20.83, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(24), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(24, 10, 'ME', 2583.00, 4249.99, 2583.00, 104.17, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(25), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(25, 10, 'ME', 4250.00, 7166.99, 4250.00, 354.17, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(26), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(26, 10, 'ME', 7167.00, 11749.99, 7167.00, 937.50, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(27), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(27, 10, 'ME', 11750.00, 22166.99, 11750.00, 2083.33, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(28), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(28, 10, 'ME', 22167.00, 99999999.99, 22167.00, 5208.33, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(29), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(29, 10, 'HF1', 1375.00, 1791.99, 1375.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(30), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(30, 10, 'HF1', 1792.00, 2624.99, 1792.00, 20.83, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(31), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(31, 10, 'HF1', 2625.00, 4291.99, 2625.00, 104.17, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(32), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(32, 10, 'HF1', 4292.00, 7207.99, 4292.00, 354.17, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(33), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(33, 10, 'HF1', 7208.00, 11791.99, 7208.00, 937.50, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(34), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(34, 10, 'HF1', 11792.00, 22207.99, 11792.00, 2083.33, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(35), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(35, 10, 'HF1', 22208.00, 99999999.99, 22208.00, 5208.33, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(36), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(36, 10, 'HF2', 1708.00, 2124.99, 1708.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(37), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(37, 10, 'HF2', 2125.00, 2957.99, 2125.00, 20.83, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(38), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(38, 10, 'HF2', 2958.00, 4624.99, 2958.00, 104.17, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(39), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(39, 10, 'HF2', 4625.00, 7541.99, 4625.00, 354.17, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(40), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(40, 10, 'HF2', 7542.00, 12124.99, 7542.00, 937.50, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(41), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(41, 10, 'HF2', 12125.00, 22541.99, 12125.00, 2083.33, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(42), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(42, 10, 'HF2', 22542.00, 99999999.99, 22542.00, 5208.33, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(43), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(43, 10, 'HF3', 2042.00, 2457.99, 2042.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(44), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(44, 10, 'HF3', 2458.00, 3291.99, 2458.00, 20.83, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(45), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(45, 10, 'HF3', 3292.00, 4957.99, 3292.00, 104.17, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(46), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(46, 10, 'HF3', 4958.00, 7874.99, 4958.00, 354.17, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(47), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(47, 10, 'HF3', 7875.00, 12457.99, 7875.00, 937.50, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(48), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(48, 10, 'HF3', 12458.00, 22874.99, 12458.00, 2083.33, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(49), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(49, 10, 'HF3', 22875.00, 99999999.99, 22875.00, 5208.33, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(50), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(50, 10, 'HF4', 2375.00, 2791.99, 2375.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(51), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(51, 10, 'HF4', 2792.00, 3624.99, 2792.00, 20.83, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(52), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(52, 10, 'HF4', 3625.00, 5291.99, 3625.00, 104.17, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(53), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(53, 10, 'HF4', 5292.00, 8207.99, 5292.00, 354.17, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(54), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(54, 10, 'HF4', 8208.00, 12791.99, 8208.00, 937.50, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(55), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(55, 10, 'HF4', 12792.00, 23207.99, 12792.00, 2083.33, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(56), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(56, 10, 'HF4', 23208.00, 99999999.99, 23208.00, 5208.33, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(57), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(57, 10, 'ME1', 1667.00, 2082.99, 1667.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(58), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(58, 10, 'ME1', 2083.00, 2916.99, 2083.00, 20.83, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(59), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(59, 10, 'ME1', 2917.00, 4582.99, 2917.00, 104.17, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(60), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(60, 10, 'ME1', 4583.00, 7499.99, 4583.00, 354.17, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(61), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(61, 10, 'ME1', 7500.00, 12082.99, 7500.00, 937.50, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(62), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(62, 10, 'ME1', 12083.00, 22499.99, 12083.00, 2083.33, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(63), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(63, 10, 'ME1', 22500.00, 99999999.99, 22500.00, 5208.33, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(64), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(64, 10, 'ME2', 2000.00, 2416.99, 2000.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(65), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(65, 10, 'ME2', 2417.00, 3249.99, 2417.00, 20.83, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(66), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(66, 10, 'ME2', 3250.00, 4916.99, 3250.00, 104.17, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(67), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(67, 10, 'ME2', 4917.00, 7832.99, 4917.00, 354.17, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(68), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(68, 10, 'ME2', 7833.00, 12416.99, 7833.00, 937.50, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(69), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(69, 10, 'ME2', 12417.00, 22832.99, 12417.00, 2083.33, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(70), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(70, 10, 'ME2', 22833.00, 99999999.99, 22833.00, 5208.33, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(71), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(71, 10, 'ME3', 2333.00, 2749.99, 2333.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(72), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(72, 10, 'ME3', 2750.00, 3582.99, 2750.00, 20.83, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(73), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(73, 10, 'ME3', 3583.00, 5249.99, 3583.00, 104.17, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(74), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(74, 10, 'ME3', 5250.00, 8166.99, 5250.00, 354.17, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(75), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(75, 10, 'ME3', 8167.00, 12749.99, 8167.00, 937.50, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(76), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(76, 10, 'ME3', 12750.00, 23166.99, 12750.00, 2083.33, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(77), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(77, 10, 'ME3', 23167.00, 99999999.99, 23167.00, 5208.33, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(78), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(78, 10, 'ME4', 2667.00, 3082.99, 2667.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(79), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(79, 10, 'ME4', 3083.00, 3916.99, 3083.00, 20.83, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(80), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(80, 10, 'ME4', 3917.00, 5582.99, 3917.00, 104.17, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(81), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(81, 10, 'ME4', 5583.00, 8499.99, 5583.00, 354.17, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(82), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(82, 10, 'ME4', 8500.00, 13082.99, 8500.00, 937.50, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(83), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(83, 10, 'ME4', 13083.00, 23499.99, 13083.00, 2083.33, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(84), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(84, 10, 'ME4', 23500.00, 99999999.99, 23500.00, 5208.33, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(85), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(85, 20, 'Z', 0.00, 829.99, 0.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(86), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(86, 20, 'Z', 833.00, 2499.99, 833.00, 41.67, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(87), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(87, 20, 'Z', 2500.00, 5832.99, 2500.00, 208.33, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(88), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(88, 20, 'Z', 5833.00, 11666.99, 5833.00, 708.33, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(89), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(89, 20, 'Z', 11667.00, 20832.99, 11667.00, 1875.00, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(90), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(90, 20, 'Z', 20833.00, 41666.99, 20833.00, 4166.67, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(91), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(91, 20, 'Z', 41667.00, 99999999.99, 41667.00, 10416.67, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(92), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(92, 20, 'S', 1667.00, 2499.99, 1667.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(93), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(93, 20, 'S', 2500.00, 4166.99, 2500.00, 41.67, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(94), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(94, 20, 'S', 4167.00, 7499.99, 4167.00, 208.33, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(95), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(95, 20, 'S', 7500.00, 13332.99, 7500.00, 708.33, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(96), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(96, 20, 'S', 13333.00, 22499.99, 13333.00, 1875.00, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(97), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(97, 20, 'S', 22500.00, 43332.99, 22500.00, 4166.67, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(98), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(98, 20, 'S', 43333.00, 99999999.99, 43333.00, 10416.67, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(99), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(99, 20, 'HF', 2083.00, 2916.99, 2083.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(100), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(100, 20, 'HF', 2917.00, 4582.99, 2917.00, 41.67, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(101), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(101, 20, 'HF', 4583.00, 7916.99, 4583.00, 208.33, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(102), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(102, 20, 'HF', 7917.00, 13749.99, 7917.00, 708.33, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(103), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(103, 20, 'HF', 13750.00, 22916.99, 13750.00, 1875.00, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(104), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(104, 20, 'HF', 22917.00, 43749.99, 22917.00, 4166.67, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(105), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(105, 20, 'HF', 43750.00, 99999999.99, 43750.00, 10416.67, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(106), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(106, 20, 'ME', 2667.00, 3499.99, 2667.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(107), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(107, 20, 'ME', 3500.00, 5166.99, 3500.00, 41.67, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(108), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(108, 20, 'ME', 5167.00, 8499.99, 5167.00, 208.33, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(109), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(109, 20, 'ME', 8500.00, 14332.99, 8500.00, 708.33, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(110), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(110, 20, 'ME', 14333.00, 23499.99, 14333.00, 1875.00, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(111), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(111, 20, 'ME', 23500.00, 44332.99, 23500.00, 4166.67, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(112), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(112, 20, 'ME', 44333.00, 99999999.99, 44333.00, 10416.67, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(113), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(113, 20, 'HF1', 2750.00, 3582.99, 2750.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(114), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(114, 20, 'HF1', 3583.00, 5249.99, 3583.00, 41.67, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(115), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(115, 20, 'HF1', 5250.00, 8582.99, 5250.00, 208.33, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(116), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(116, 20, 'HF1', 8583.00, 14416.99, 8583.00, 708.33, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(117), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(117, 20, 'HF1', 14417.00, 23582.99, 14417.00, 1875.00, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(118), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(118, 20, 'HF1', 23583.00, 44416.99, 23583.00, 4166.67, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(119), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(119, 20, 'HF1', 44417.00, 99999999.99, 44417.00, 10416.67, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(120), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(120, 20, 'HF2', 3417.00, 4249.99, 3417.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(121), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(121, 20, 'HF2', 4250.00, 5916.99, 4250.00, 41.67, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(122), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(122, 20, 'HF2', 5917.00, 9249.99, 5917.00, 208.33, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(123), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(123, 20, 'HF2', 9250.00, 15082.99, 9250.00, 708.33, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(124), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(124, 20, 'HF2', 15083.00, 24249.99, 15083.00, 1875.00, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(125), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(125, 20, 'HF2', 24250.00, 45082.99, 24250.00, 4166.67, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(126), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(126, 20, 'HF2', 45083.00, 99999999.99, 45083.00, 10416.67, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(127), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(127, 20, 'HF3', 4083.00, 4916.99, 4083.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(128), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(128, 20, 'HF3', 4917.00, 6582.99, 4917.00, 41.67, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(129), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(129, 20, 'HF3', 6583.00, 9916.99, 6583.00, 208.33, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(130), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(130, 20, 'HF3', 9917.00, 15749.99, 9917.00, 708.33, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(131), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(131, 20, 'HF3', 15750.00, 24916.99, 15750.00, 1875.00, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(132), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(132, 20, 'HF3', 24917.00, 45749.99, 24917.00, 4166.67, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(133), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(133, 20, 'HF3', 45750.00, 99999999.99, 45750.00, 10416.67, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(134), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(134, 20, 'HF4', 4750.00, 5582.99, 4750.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(135), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(135, 20, 'HF4', 5583.00, 7249.99, 5583.00, 41.67, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(136), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(136, 20, 'HF4', 7250.00, 10582.99, 7250.00, 208.33, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(137), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(137, 20, 'HF4', 10583.00, 16416.99, 10583.00, 708.33, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(138), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(138, 20, 'HF4', 16417.00, 25582.99, 16417.00, 1875.00, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(139), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(139, 20, 'HF4', 25583.00, 46416.99, 25583.00, 4166.67, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(140), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(140, 20, 'HF4', 46417.00, 99999999.99, 46417.00, 10416.67, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(141), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(141, 20, 'ME1', 3333.00, 4166.99, 3333.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(142), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(142, 20, 'ME1', 4167.00, 5832.99, 4167.00, 41.67, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(143), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(143, 20, 'ME1', 5833.00, 9166.99, 5833.00, 208.33, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(144), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(144, 20, 'ME1', 9167.00, 14999.99, 9167.00, 708.33, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(145), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(145, 20, 'ME1', 15000.00, 24166.99, 15000.00, 1875.00, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(146), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(146, 20, 'ME1', 24167.00, 44999.99, 24167.00, 4166.67, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(147), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(147, 20, 'ME1', 45000.00, 99999999.99, 45000.00, 10416.67, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(148), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(148, 20, 'ME2', 4000.00, 4832.99, 4000.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(149), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(149, 20, 'ME2', 4833.00, 6499.99, 4833.00, 41.67, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(150), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(150, 20, 'ME2', 6500.00, 9832.99, 6500.00, 208.33, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(151), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(151, 20, 'ME2', 9833.00, 15666.99, 9833.00, 708.33, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(152), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(152, 20, 'ME2', 15667.00, 24833.99, 15667.00, 1875.00, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(153), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(153, 20, 'ME2', 24833.00, 45666.99, 24833.00, 4166.67, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(154), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(154, 20, 'ME2', 45667.00, 99999999.99, 45667.00, 10416.67, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(155), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(155, 20, 'ME3', 4667.00, 5499.99, 4667.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(156), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(156, 20, 'ME3', 5500.00, 7166.99, 5500.00, 41.67, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(157), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(157, 20, 'ME3', 7167.00, 10499.99, 7167.00, 208.33, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(158), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(158, 20, 'ME3', 10500.00, 16332.99, 10500.00, 708.33, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(159), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(159, 20, 'ME3', 16333.00, 25499.99, 16333.00, 1875.00, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(160), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(160, 20, 'ME3', 25500.00, 25499.99, 25500.00, 4166.67, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(161), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(161, 20, 'ME3', 46333.00, 99999999.99, 46333.00, 10416.67, 32));
InsertRecord('prltaxtablerate2', array('counterindex'), array(162), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(162, 20, 'ME4', 5333.00, 6166.99, 5333.00, 0.00, 5));
InsertRecord('prltaxtablerate2', array('counterindex'), array(163), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(163, 20, 'ME4', 6167.00, 7832.99, 6167.00, 41.67, 10));
InsertRecord('prltaxtablerate2', array('counterindex'), array(164), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(164, 20, 'ME4', 7833.00, 11166.99, 7833.00, 208.33, 15));
InsertRecord('prltaxtablerate2', array('counterindex'), array(165), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(165, 20, 'ME4', 11167.00, 16999.99, 11167.00, 708.33, 20));
InsertRecord('prltaxtablerate2', array('counterindex'), array(166), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(166, 20, 'ME4', 17000.00, 26166.99, 17000.00, 1875.00, 25));
InsertRecord('prltaxtablerate2', array('counterindex'), array(167), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(167, 20, 'ME4', 26167.00, 46999.99, 26167.00, 4166.67, 30));
InsertRecord('prltaxtablerate2', array('counterindex'), array(168), array('counterindex', 'payperiodid', 'taxstatusid', 'rangefrom', 'rangeto', 'fixtaxableamount', 'fixtax', 'percentofexcessamount'), array(168, 20, 'ME4', 47000.00, 99999999.99, 47000.00, 10416.67, 32));

CreateTable('prlpayrolltrans', "CREATE TABLE prlpayrolltrans (
  counterindex int(11) NOT NULL auto_increment,
  payrollid varchar(10) NULL default '',
  employeeid varchar(10) NOT NULL default '',
  reghrs decimal(12,2) NOT NULL default '0.00',
  absenthrs decimal(12,2) NOT NULL default '0.00',
  latehrs decimal(12,2) NOT NULL default '0.00',
  periodrate decimal(12,2) NOT NULL default '0.00',
  hourlyrate decimal(12,2) NOT NULL default '0.00',
  basicpay decimal(12,2) NOT NULL default '0.00',
  othincome decimal(12,2) NOT NULL default '0.00',
  absent decimal(12,2) NOT NULL default '0.00',
  late   decimal(12,2) NOT NULL default '0.00',
  otpay decimal(12,2) NOT NULL default '0.00',
  grosspay decimal(12,2) NOT NULL default '0.00',
  loandeduction decimal(12,2) NOT NULL default '0.00',
  sss decimal(12,2) NOT NULL default '0.00',
  hdmf decimal(12,2) NOT NULL default '0.00',
  philhealth decimal(12,2) NOT NULL default '0.00',
  tax decimal(12,2) NOT NULL default '0.00',
  otherdeduction decimal(12,2) NOT NULL default '0.00',
  totaldeduction decimal(12,2) NOT NULL default '0.00',
  netpay decimal(12,2) NOT NULL default '0.00',
  fsmonth tinyint(4) NOT NULL default '0',
  fsyear double NOT NULL default '0',
  PRIMARY KEY  (counterindex)
)");

CreateTable('prldailytrans', "CREATE TABLE prldailytrans (
  counterindex int(11) NOT NULL auto_increment,
  rtref varchar(11) NOT NULL default '',
  rtdesc varchar(40) NOT NULL default '',
  rtdate date NOT NULL default '0000-00-00',
  payrollid varchar(10) NOT NULL default '',
  employeeid varchar(10) NOT NULL default '',
  reghrs decimal(12,2) NOT NULL default '0.00',
  absenthrs decimal(12,2) NOT NULL default '0.00',
  latehrs decimal(12,2) NOT NULL default '0.00',
  regamt decimal(12,2) NOT NULL default '0.00',
  absentamt decimal(12,2) NOT NULL default '0.00',
  lateamt decimal(12,2) NOT NULL default '0.00',
  PRIMARY KEY  (counterindex),
  KEY RTDate (rtdate)
)");

CreateTable('prlpayrollperiod', "CREATE TABLE prlpayrollperiod (
  payrollid varchar(10) NULL default '',
  payrolldesc varchar(40) NOT NULL default '',
  payperiodid tinyint(4) NOT NULL default '0',
  startdate date NOT NULL default '0000-00-00',
  enddate date NOT NULL default '0000-00-00',
  fsmonth tinyint(4) NOT NULL default '0',
  fsyear double NOT NULL default '0',
  deductsss tinyint(4) NOT NULL default '0',
  deducthdmf tinyint(4) NOT NULL default '0',
  deductphilhealth tinyint(4) NOT NULL default '0',
  payclosed tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (payrollid)
)");

CreateTable('prlemployeemaster', "CREATE TABLE prlemployeemaster (
  employeeid varchar(10) NOT NULL default '',
  lastname varchar(40) NOT NULL default '',
  firstname varchar(40) NOT NULL default '',
  middlename varchar(40) NOT NULL default '',
  address1 varchar(100) NOT NULL default '',
  address2 varchar(100) NOT NULL default '',
  city varchar(50) NOT NULL default '',
  state varchar(20) NOT NULL default '',
  zip varchar(15) NOT NULL default '',
  country varchar(40) NOT NULL default '',
  gender varchar(15) NOT NULL default '',
  phone1 varchar(20) NOT NULL default '',
  phone1comment varchar(20) NOT NULL default '',
  phone2 varchar(20) NOT NULL default '',
  phone2comment varchar(20) NOT NULL default '',
  email1 varchar(50) NOT NULL default '',
  email1comment varchar(20) NOT NULL default '',
  email2 varchar(50) NOT NULL default '',
  email2comment varchar(20) NOT NULL default '',
  atmnumber varchar(20) NOT NULL default '',
  ssnumber varchar(20) NOT NULL default '',
  hdmfnumber varchar(20) NOT NULL default '',
  phnumber varchar(15) NOT NULL default '',
  taxactnumber varchar(15) NOT NULL default '',
  birthdate date NOT NULL default '0000-00-00',
  hiredate date NOT NULL default '0000-00-00',
  terminatedate date NOT NULL default '0000-00-00',
  retireddate date NOT NULL default '0000-00-00',
  paytype tinyint(4) NOT NULL default '0',
  payperiodid tinyint(4) NOT NULL default '0',
  periodrate decimal(12,2) NOT NULL default '0.00',
  hourlyrate decimal(12,2) NOT NULL default '0.00',
  glactcode int(11) NOT NULL default '0',
  marital varchar(20) NOT NULL default '',
  taxstatusid varchar(10) NULL default '',
  employmentid tinyint(4) NOT NULL default '0',
  active int(11) NOT NULL default '0',
  costcenterid varchar(10) NOT NULL default '',
  position varchar(40) NOT NULL default '',
  PRIMARY KEY  (employeeid),
  KEY EmployeeName (lastname,firstname)
)");

CreateTable('prlloanfile', "CREATE TABLE prlloanfile (
  counterindex int(11) NOT NULL auto_increment,
  loanfileid varchar(10) NOT NULL default '',
  loanfiledesc varchar(40) NOT NULL default '',
  employeeid varchar(10) NOT NULL default '',
  loandate date NOT NULL default '0000-00-00',
  loantableid tinyint(4) NOT NULL default '0',
  loanamount decimal(12,2) NOT NULL default '0.00',
  amortization decimal(12,2) NOT NULL default '0.00',
  startdeduction date NOT NULL default '0000-00-00',
  ytddeduction decimal(12,2) NOT NULL default '0.00',
  loanbalance decimal(12,2) NOT NULL default '0.00',
  accountcode int(11) NOT NULL default '0',
  PRIMARY KEY  (counterindex),
  KEY LoanDate (loandate)
)");

CreateTable('prlloandeduction', "CREATE TABLE prlloandeduction (
  counterindex int(11) NOT NULL auto_increment,
  refid int(11) NOT NULL default '0',
  payrollid varchar(10) NULL default '',
  employeeid varchar(10) NOT NULL default '',
  loantableid tinyint(4) NOT NULL default '0',
  amount decimal(12,2) NOT NULL default '0.00',
  accountcode int(11) NOT NULL default '0',
  PRIMARY KEY  (counterindex)
)");

CreateTable('prlloantable', "CREATE TABLE prlloantable (
  loantableid tinyint(4) NOT NULL default '0',
  loantabledesc varchar(25) NOT NULL default '',
  accountcode int(11) NOT NULL default '0',
  PRIMARY KEY  (loantableid)
) ");

CreateTable('prlothincfile', "CREATE TABLE prlothincfile (
  counterindex int(11) NOT NULL auto_increment,
  othfileref varchar(10) NOT NULL default '',
  othfiledesc varchar(40) NOT NULL default '',
  employeeid varchar(10) NOT NULL default '',
  othdate date NOT NULL default '0000-00-00',
  othincid tinyint(4) NOT NULL default '0',
  othincamount decimal(12,2) NOT NULL default '0.00',
  accountcode int(11) NOT NULL default '0',
  PRIMARY KEY  (counterindex),
  KEY OthDate (othdate)
)");

CreateTable('prlothinctable', "CREATE TABLE prlothinctable (
  othincid tinyint(4) NOT NULL default '0',
  othincdesc varchar(25) NOT NULL default '',
  taxable varchar(10) NOT NULL default '',
  accountcode int(11) NOT NULL default '0',
  PRIMARY KEY  (othincid)
)");

CreateTable('prlottrans', "CREATE TABLE prlottrans (
  counterindex int(11) NOT NULL auto_increment,
  payrollid varchar(10) NULL default '',
  otref varchar(11) NOT NULL default '',
  otdesc varchar(40) NOT NULL default '',
  otdate date NOT NULL default '0000-00-00',
  overtimeid tinyint(4) NOT NULL default '0',
  employeeid varchar(10) NOT NULL default '',
  othours double NOT NULL default '0',
  joborder varchar(10) NOT NULL default '',
  accountcode int(11) NOT NULL default '0',
  otamount double NOT NULL default '0',
  PRIMARY KEY  (counterindex),
  KEY Account (accountcode),
  KEY OTDate (otdate)
)");

CreateTable('prlpayperiod', "CREATE TABLE prlpayperiod (
  payperiodid tinyint(4) NOT NULL default '0',
  payperioddesc varchar(15) NOT NULL default '',
  numberofpayday int(11) NOT NULL default '0',
  PRIMARY KEY  (payperiodid)
)");

CreateTable('prlovertimetable', "CREATE TABLE prlovertimetable (
  overtimeid tinyint(4) NOT NULL default '0',
  overtimedesc varchar(40) NOT NULL default '',
  overtimerate decimal(6,2) NOT NULL default '0.00',
  accountcode int(11) NOT NULL default '0',
  PRIMARY KEY  (overtimeid)
)");

CreateTable('prlemploymentstatus', "CREATE TABLE prlemploymentstatus (
  employmentid tinyint(4) NOT NULL default '0',
  employmentdesc varchar(15) NOT NULL default '',
  PRIMARY KEY  (employmentid)
)");

InsertRecord('prlpayrollperiod', array('payrollid'), array('10'), array('payrollid', 'payrolldesc', 'payperiodid', 'startdate', 'enddate', 'fsmonth', 'fsyear', 'deductsss', 'deducthdmf', 'deductphilhealth', 'payclosed'), array('10', 'Semi-Monthly Payroll (August 1-15, 2008)', 10, '2008-08-01', '2008-08-15', 8, 2008, 0, 0, 0, 0));

InsertRecord('prlothinctable', array('othincid'), array(10), array('othincid', 'othincdesc', 'taxable', 'accountcode'), array(10, 'Meal Allowance', 'Non-Tax', 1));
InsertRecord('prlothinctable', array('othincid'), array(20), array('othincid', 'othincdesc', 'taxable', 'accountcode'), array(20, 'Transportation Allowance', 'Non-Tax', 1));
InsertRecord('prlothinctable', array('othincid'), array(30), array('othincid', 'othincdesc', 'taxable', 'accountcode'), array(30, 'Housing Allowance', 'Taxable', 1));

InsertRecord('prlemploymentstatus', array('employmentid'), array(10), array('employmentid', 'employmentdesc'), array(10, 'Regular'));
InsertRecord('prlemploymentstatus', array('employmentid'), array(20), array('employmentid', 'employmentdesc'), array(20, 'Probationary'));
InsertRecord('prlemploymentstatus', array('employmentid'), array(30), array('employmentid', 'employmentdesc'), array(30, 'Contractual'));

InsertRecord('prlpayperiod', array('payperiodid'), array(10), array('payperiodid', 'payperioddesc', 'numberofpayday'), array(10, 'Semi-Monthly', 24));
InsertRecord('prlpayperiod', array('payperiodid'), array(20), array('payperiodid', 'payperioddesc', 'numberofpayday'), array(20, 'Monthly', 12));
InsertRecord('prlpayperiod', array('payperiodid'), array(30), array('payperiodid', 'payperioddesc', 'numberofpayday'), array(30, 'Weekly', 52));
InsertRecord('prlpayperiod', array('payperiodid'), array(40), array('payperiodid', 'payperioddesc', 'numberofpayday'), array(40, 'Bi-Weekly', 104));
InsertRecord('prlpayperiod', array('payperiodid'), array(50), array('payperiodid', 'payperioddesc', 'numberofpayday'), array(50, 'Daily', 312));
InsertRecord('prlpayperiod', array('payperiodid'), array(60), array('payperiodid', 'payperioddesc', 'numberofpayday'), array(60, 'Quarterly', 4));
InsertRecord('prlpayperiod', array('payperiodid'), array(70), array('payperiodid', 'payperioddesc', 'numberofpayday'), array(70, 'Bi-Annual', 2));
InsertRecord('prlpayperiod', array('payperiodid'), array(80), array('payperiodid', 'payperioddesc', 'numberofpayday'), array(80, 'Annual', 1));

InsertRecord('prlovertimetable', array('overtimeid'), array(10), array('overtimeid', 'overtimedesc', 'overtimerate', 'accountcode'), array(10, 'Regular Day OT Work', 1.25, 1));
InsertRecord('prlovertimetable', array('overtimeid'), array(15), array('overtimeid', 'overtimedesc', 'overtimerate', 'accountcode'), array(15, 'Night Shift Pay ', 0.1, 1));
InsertRecord('prlovertimetable', array('overtimeid'), array(20), array('overtimeid', 'overtimedesc', 'overtimerate', 'accountcode'), array(20, 'Restday or Special Day OT Work', 1.30, 1));
InsertRecord('prlovertimetable', array('overtimeid'), array(25), array('overtimeid', 'overtimedesc', 'overtimerate', 'accountcode'), array(25, 'Restday or Special Day OT Work >8 hrs', 1.69, 1));
InsertRecord('prlovertimetable', array('overtimeid'), array(30), array('overtimeid', 'overtimedesc', 'overtimerate', 'accountcode'), array(30, 'Regular Holiday OT Work', 2.00, 1));
InsertRecord('prlovertimetable', array('overtimeid'), array(35), array('overtimeid', 'overtimedesc', 'overtimerate', 'accountcode'), array(35, 'Regular Holiday OT Work >8 hrs', 2.6, 1));
InsertRecord('prlovertimetable', array('overtimeid'), array(40), array('overtimeid', 'overtimedesc', 'overtimerate', 'accountcode'), array(40, 'Restday and Regular Holiday OT Work', 2.60, 1));
InsertRecord('prlovertimetable', array('overtimeid'), array(45), array('overtimeid', 'overtimedesc', 'overtimerate', 'accountcode'), array(45, 'Restday and Regular Holiday OT Work >8hrs', 3.38, 1));

InsertRecord('prlloantable', array('loantableid'), array(10), array('loantableid', 'loantabledesc', 'accountcode'), array(10, 'SSS Salary Loan', 1));
InsertRecord('prlloantable', array('loantableid'), array(20), array('loantableid', 'loantabledesc', 'accountcode'), array(20, 'Pag-ibig Housing Loan', 1));
InsertRecord('prlloantable', array('loantableid'), array(30), array('loantableid', 'loantabledesc', 'accountcode'), array(30, 'Cash Advance', 1));
InsertRecord('prlloantable', array('loantableid'), array(40), array('loantableid', 'loantabledesc', 'accountcode'), array(40, 'Car Loan', 1));

UpdateDBNo(basename(__FILE__, '.php'));

?>