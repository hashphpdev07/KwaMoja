<?php

ChangeColumnType('lat', 'custbranch', 'FLOAT(12,8)', 'NOT NULL', '0.00000000');
ChangeColumnType('lng', 'custbranch', 'FLOAT(12,8)', 'NOT NULL', '0.00000000');

UpdateDBNo(basename(__FILE__, '.php'));

?>