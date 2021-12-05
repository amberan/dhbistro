<?php

$update221 = 'INSERT INTO '.DB_PREFIX.'operation_type (id,name)
 VALUES (16,"reset hesla") ON DUPLICATE KEY UPDATE id=16, name="reset hesla";
INSERT INTO '.DB_PREFIX.'operation_type (id,name)
 VALUES (17,"obnovení") ON DUPLICATE KEY UPDATE id=17, name="obnovení";
INSERT INTO '.DB_PREFIX.'operation_type (id,name)
 VALUES (18,"uzamknutí") ON DUPLICATE KEY UPDATE id=18, name="uzamknutí";
INSERT INTO '.DB_PREFIX.'operation_type (id,name)
 VALUES (19,"odemknutí") ON DUPLICATE KEY UPDATE id=19, name="odemknutí";
INSERT INTO '.DB_PREFIX.'record_type (id,name)
VALUES (13, "soubor")  ON DUPLICATE KEY UPDATE id=13, name="soubor";';
 mysqli_query($database, $update221);
