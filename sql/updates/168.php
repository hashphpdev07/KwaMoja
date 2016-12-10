<?php

AddColumn('remark', 'workorders', 'TEXT', 'NOT NULL', "", 'closecomments');
AddColumn('reference', 'workorders', 'VARCHAR(40)', 'NOT NULL', "", 'remark');

UpdateDBNo(basename(__FILE__, '.php'));

?>