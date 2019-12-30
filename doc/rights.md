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
zálohování                                      | | | | | X
| | POWER USER | EDITOR | AUDITOR | ORGANIZATOR |

## KONCEPT PRAV 2020
acl_root ROOT - 0/1 - zalohovani 
acl_gm ORGANIZATOR - 0/1 - není nové, antidatace  
acl_super SUPERUSER - 0/1 - správa uživatelů  
acl_power POWERUSER - 0/1 - aktuality, nástěnka, bludišťáci, casove moznosti

acl_task TASKS - cist (0) / psat (1)
acl_secret SECRET n - utajení úrovně 1/2/3/...../x  
acl_audit AUDIT - off (0) / cist (1)
acl_power API - off (0) / cist (1) / psat (2)
acl_group EDITOR skupin - cist (0) /psat (1)/mazat (2)
acl_person EDITOR osob - cist (0) /psat (1)/mazat (2)
acl_case EDITOR pripadu - cist (0) /psat (1)/mazat (2)