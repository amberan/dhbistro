# BISTRO user rights

aktivita | POWER USER | EDITOR | AUDITOR | ORGANIZATOR 
:---:|:---:|:---:|:---:|:---:
|| right_power | right_text | right_audit | right_org |right_super
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


## KONCEPT PRAV 2020

aktivita|ROOT|ORGANIZATOR|SUPERUSER|POWERUSER|UKOLY|UTAJENI|AUDIT|API|SKUPINY|OSOBY|PRIPADY
--- |:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:
||acl_root|acl_gm|acl_super|acl_power|acl_task|acl_secret|acl_audit|acl_api|acl_group|acl_person|acl_case
zobrazní auditu                                 |||||||0/1||||
editace skupin                                  |||||||||||0/1
editace osob                                    ||||||||||0/1|
editace pripadu                                 |||||||||0/1||
zobrazení auditní stopy jiných orgů             ||0/1|||||||||
antidatace                                      ||0/1|||||||||
"není nové"                                     ||0/1|||||||||
uživatelé - editace, zobrazení etc.             |||0/1||||||||
tajné věci                                      ||||||n-m|||||
editace časovných možností                      ||||0/1|||||||
přidělování bludišťáků                          ||||0/1|||||||
úkoly                                           |||||r/w||||||
přidávání aktualit                              ||||0/1|||||||
úprava nástěnky                                 ||||0/1|||||||
zálohování                                      |0/1|||||||||| 
pristup k API                                   ||||||||0/r/w||| 


#### konverze
- right_power > acl_super
- right_text > acl_task + acl_group + acl_person + acl_case
- right_audit > acl_audit
- right_org > acl_gm
- right_super > acl_root
