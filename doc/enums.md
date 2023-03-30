# BISTRO ciselniky

### authorizedAccess

[record type](lib/audit.php#L32-46)      | [operation type](lib/audit.php#L3-19)        | idrecord
--- | --- | ---
1 - person          | 1 - read |
2 - group           | 2 - edit |
3 - case            | 3 - new |
4 - report          | 4 - add file |
5 - news            | 5 - remove file |
6 - dashboard       | 6 - link |
7 - symbol          | 7 - new note |
8 - user            | 8 - delete note |
9 - point           | 9 - edit note |
10 - task           | 10 - gamemasters edit |
11 - audit          | 11 - delete |
12 - other          | 12 - unauthorized access |
13 - file           | 13 - unauthorized access to deleted item |
14 - backup         | 14 - search |
15 - settings       | 15 - unauthorized access to secret |
16                  | 16 - password reset |
17                  | 17 - recovery |
18                  | 18 - lock |
19                  | 19 - unlock |


### report type
type | typeName
--- | ---
1 | vyjezd
2 | vyslech

### p2ar role
role| roleName
--- | ---
0 | pritomen
1 | vyslychany
2 | vyslychajici
3 | zatceny
4 | velistel zasahu

## report status
status| statusName
--- | ---
0 | rozpracovane
1 | dokoncene
2 | analyzovane
3 | archivovane

## deleteAllUnread
idtable | object
--- | ---
1 | ?report?

##  notes
idtable | source object
--- | ---
1  | editperson.php
2  | editgroup.php
3  | editcase.php
4  | reports/edit
5  | readperson.php
6  | readgroup.php
7  | readcase.php
8  | reports
9  | readsymbol.php
10 | symbols.php
