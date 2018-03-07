<?php

CreateTable('pcreceipts',
"CREATE TABLE `pcreceipts` (
  `pccashdetail` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `type` varchar(30) NOT NULL,
  `size` int(11) NOT NULL,
  `content` mediumblob NOT NULL,
  PRIMARY KEY (`pccashdetail`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

UpdateDBNo(basename(__FILE__, '.php'));

?>