<?php
/*
 * ADD COLUMN
 */
$columnAdd['person']['archived'] = 'timestamp NULL AFTER deleted';
/*
 * ALTER COLUMN
 */
$columnAlter['symbol']['archiv'] = 'archived timestamp NULL AFTER deleted;';
/*
 * TIME TO CONVERT
 */
$convertTime[] = ['person', 'archiv', 'archived'];
/*
* DROP COLUMN
*/
$columnDrop['person'][] = 'archiv';
