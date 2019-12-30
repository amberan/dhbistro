# BISTRO user rights

aktivita | right_power | right_text | right_audit | right_org |right_super
--- | --- | --- | --- | --- | ---
zobrazní auditu                                 | | | x | |
editace hlavních textů = editace obecně objektů | | x | | |
zobrazení auditní stopy jiných orgů             | | | | x |
antidatace                                      | | | | x |
"není nové"                                     | | | | x |
uživatelé - editace, zobrazení etc.             | x | | | |
tajné věci                                      | x | | | |
editace časovných možností                      | x | | | |
přidělování bludišťáků                          | x | | | |
úkoly                                           | | X | | |
přidávání aktualit                              | x | | | |
úprava nástěnky                                 | x | | | |
zálohování                                      | | | | | | X
| | POWER USER | EDITOR | AUDITOR | ORGANIZATOR |

## KONCEPT PRAV 2020

|ROOT|ORGANIZATOR|SUPERUSER|POWERUSER|UKOLY|UTAJENI|AUDIT|API|SKUPINY|OSOBY|PRIPADY
--- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | ---
aktivita|acl_root|acl_gm|acl_super|acl_power|acl_task|acl_secret|acl_audit|acl_api|acl_group|acl_person|acl_case
|0/1|0/1|0/1|0/1|r/w|n|0/1|0/r/w|r/w|r/w|r/w
zobrazní auditu                                 |||||||X||||
editace skupin                                  |||||||||||X
editace osob                                    ||||||||||X|
editace pripadu                                 |||||||||X||
zobrazení auditní stopy jiných orgů             ||X|||||||||
antidatace                                      ||X|||||||||
"není nové"                                     ||X|||||||||
uživatelé - editace, zobrazení etc.             |||X||||||||
tajné věci                                      ||||||X|||||
editace časovných možností                      ||||X|||||||
přidělování bludišťáků                          ||||X|||||||
úkoly                                           |||||X||||||
přidávání aktualit                              ||||X|||||||
úprava nástěnky                                 ||||X|||||||
zálohování                                      |X|||||||||| 
pristup k API                                   ||||||||X||| 


#### konverze
right_power > acl_super
right_text > acl_task + acl_group + acl_person + acl_case
right_audit > acl_audit
right_org > acl_gm
right_super > acl_root