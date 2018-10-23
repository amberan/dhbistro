<?php
//ALTER TABLE       $1 ADD COLUMN $2        $3
$definition_column['backups']['version'] = "varchar(50) NOT NULL DEFAULT '1.5.2 =<'";


foreach(array_keys($definition_column) as $table) {
    foreach(array_keys($definition_column[$table]) as $column) {
        $check_sql=mysqli_query($database,"SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='$dbname' AND table_name='".DB_PREFIX."$table' and column_name='$column'");
        if(mysqli_num_rows($check_sql)== 0) {
            $alter_sql = "ALTER TABLE ".DB_PREFIX."$table ADD COLUMN `$column` ".$definition_column[$table][$column];
            mysqli_query($database,$alter_sql);
            $alter++;
        }
    }
}

if ($alter > 0 ) { //pokud zmeny probehly, prejmenovat tento soubor 
    rename(__FILE__,__FILE__.".old");
}
?>