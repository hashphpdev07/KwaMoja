<?php

AddColumn('percentdiscount', 'paymentmethods', 'DOUBLE', 'NOT NULL', '0.0', 'opencashdrawer');

UpdateDBNo(basename(__FILE__, '.php'));

?>