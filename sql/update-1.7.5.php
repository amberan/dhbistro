<?php
/*
 * ADD COLUMN
 */
$columnAdd['user']['aclReport'] = "int(3) NOT NULL DEFAULT '0' AFTER aclTask";
$columnAdd['user']['aclSymbol'] = "int(3) NOT NULL DEFAULT '0' AFTER aclReport";
/*
 * ALTER COLUMN
 */
$columnAlter['symbol']['archiv'] = 'archived timestamp NULL AFTER deleted;';
