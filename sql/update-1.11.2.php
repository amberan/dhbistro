<?php

$updateScript[116_1] = 'INSERT INTO '.DB_PREFIX.'record_type (id,name) VALUES (1,"'.$text['person'].'")  ON DUPLICATE KEY UPDATE id=1, name="'.$text['person'].'"';
$updateScript[116_2] = 'INSERT INTO '.DB_PREFIX.'record_type (id,name) VALUES (2,"'.$text['group'].'")  ON DUPLICATE KEY UPDATE id=2, name="'.$text['group'].'"';
$updateScript[116_3] = 'INSERT INTO '.DB_PREFIX.'record_type (id,name) VALUES (3,"'.$text['case'].'")  ON DUPLICATE KEY UPDATE id=3, name="'.$text['case'].'"';
$updateScript[116_4] = 'INSERT INTO '.DB_PREFIX.'record_type (id,name) VALUES (4,"'.$text['report'].'")  ON DUPLICATE KEY UPDATE id=4, name="'.$text['report'].'"';
$updateScript[116_5] = 'INSERT INTO '.DB_PREFIX.'record_type (id,name) VALUES (5,"'.$text['news'].'")  ON DUPLICATE KEY UPDATE id=5, name="'.$text['news'].'"';
$updateScript[116_6] = 'INSERT INTO '.DB_PREFIX.'record_type (id,name) VALUES (6,"'.$text['dashboard'].'")  ON DUPLICATE KEY UPDATE id=6, name="'.$text['dashboard'].'"';
$updateScript[116_7] = 'INSERT INTO '.DB_PREFIX.'record_type (id,name) VALUES (7,"'.$text['symbol'].'")  ON DUPLICATE KEY UPDATE id=7, name="'.$text['symbol'].'"';
$updateScript[116_8] = 'INSERT INTO '.DB_PREFIX.'record_type (id,name) VALUES (8,"'.$text['user'].'")  ON DUPLICATE KEY UPDATE id=8, name="'.$text['user'].'"';
$updateScript[116_9] = 'INSERT INTO '.DB_PREFIX.'record_type (id,name) VALUES (9,"'.$text['point'].'")  ON DUPLICATE KEY UPDATE id=9, name="'.$text['point'].'"';
$updateScript[116_10] = 'INSERT INTO '.DB_PREFIX.'record_type (id,name) VALUES (10,"'.$text['task'].'")  ON DUPLICATE KEY UPDATE id=10, name="'.$text['task'].'"';
$updateScript[116_11] = 'INSERT INTO '.DB_PREFIX.'record_type (id,name) VALUES (11,"'.$text['audit'].'")  ON DUPLICATE KEY UPDATE id=11, name="'.$text['audit'].'"';
$updateScript[116_12] = 'INSERT INTO '.DB_PREFIX.'record_type (id,name) VALUES (12,"'.$text['other'].'")  ON DUPLICATE KEY UPDATE id=12, name="'.$text['other'].'"';
$updateScript[116_13] = 'INSERT INTO '.DB_PREFIX.'record_type (id,name) VALUES (13,"'.$text['file'].'")  ON DUPLICATE KEY UPDATE id=13, name="'.$text['file'].'"';
$updateScript[116_14] = 'INSERT INTO '.DB_PREFIX.'record_type (id,name) VALUES (14,"'.$text['backup'].'")  ON DUPLICATE KEY UPDATE id=14, name="'.$text['backup'].'"';
$updateScript[116_15] = 'INSERT INTO '.DB_PREFIX.'record_type (id,name) VALUES (15,"'.$text['setting'].'")  ON DUPLICATE KEY UPDATE id=15, name="'.$text['setting'].'"';


$updateScript[116_16] = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name) VALUES (1,"'.$text['read'].'") ON DUPLICATE KEY UPDATE id=1, name="'.$text['read'].'"';
$updateScript[116_17] = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name) VALUES (2,"'.$text['edit'].'") ON DUPLICATE KEY UPDATE id=2, name="'.$text['edit'].'"';
$updateScript[116_18] = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name) VALUES (3,"'.$text['new'].'") ON DUPLICATE KEY UPDATE id=3, name="'.$text['new'].'"';
$updateScript[116_19] = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name) VALUES (4,"'.$text['addFile'].'") ON DUPLICATE KEY UPDATE id=4, name="'.$text['addFile'].'"';
$updateScript[116_20] = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name) VALUES (5,"'.$text['removeFile'].'") ON DUPLICATE KEY UPDATE id=5, name="'.$text['removeFile'].'"';
$updateScript[116_21] = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name) VALUES (6,"'.$text['link'].'") ON DUPLICATE KEY UPDATE id=6, name="'.$text['link'].'"';
$updateScript[116_22] = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name) VALUES (7,"'.$text['newNote'].'") ON DUPLICATE KEY UPDATE id=7, name="'.$text['newNote'].'"';
$updateScript[116_23] = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name) VALUES (8,"'.$text['deleteNote'].'") ON DUPLICATE KEY UPDATE id=8, name="'.$text['deleteNote'].'"';
$updateScript[116_24] = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name) VALUES (9,"'.$text['editNote'].'") ON DUPLICATE KEY UPDATE id=9, name="'.$text['editNote'].'"';
$updateScript[116_25] = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name) VALUES (10,"'.$text['gamemastersEdit'].'") ON DUPLICATE KEY UPDATE id=10, name="'.$text['gamemastersEdit'].'"';
$updateScript[116_26] = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name) VALUES (11,"'.$text['delete'].'") ON DUPLICATE KEY UPDATE id=11, name="'.$text['delete'].'"';
$updateScript[116_27] = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name) VALUES (12,"'.$text['unauthorizedAccess'].'") ON DUPLICATE KEY UPDATE id=12, name="'.$text['unauthorizedAccess'].'"';
$updateScript[116_28] = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name) VALUES (13,"'.$text['unauthorizedAccessToDeletedItem'].'") ON DUPLICATE KEY UPDATE id=13, name="'.$text['unauthorizedAccessToDeletedItem'].'"';
$updateScript[116_29] = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name) VALUES (14,"'.$text['search'].'") ON DUPLICATE KEY UPDATE id=14, name="'.$text['search'].'"';
$updateScript[116_30] = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name) VALUES (15,"'.$text['unauthorizedAccessToSecret'].'") ON DUPLICATE KEY UPDATE id=15, name="'.$text['unauthorizedAccessToSecret'].'"';
$updateScript[116_31] = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name) VALUES (16,"'.$text['passwordReset'].'") ON DUPLICATE KEY UPDATE id=16, name="'.$text['passwordReset'].'"';
$updateScript[116_32] = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name) VALUES (17,"'.$text['recovery'].'") ON DUPLICATE KEY UPDATE id=17, name="'.$text['recovery'].'"';
$updateScript[116_33] = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name) VALUES (18,"'.$text['lock'].'") ON DUPLICATE KEY UPDATE id=18, name="'.$text['lock'].'"';
$updateScript[116_34] = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name) VALUES (19,"'.$text['unlock'].'") ON DUPLICATE KEY UPDATE id=19, name="'.$text['unlock'].'"';
