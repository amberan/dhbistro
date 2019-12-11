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

## KONCEPT PRAV 2.0
ROOT - 0/1 - zalohovani 
ORGANIZATOR - 0/1 - není nové, antidatace  
SUPERUSER - 0/1 - správa uživatelů  
POWERUSER - 0/1 - aktuality, nástěnka, bludišťáci, casove moznosti

TASKS - cist (0) / psat (1)
SECRET n - utajení úrovně 1/2/3/...../x  
AUDIT - off (0) / cist (1)
API - off (0) / cist (1) / psat (2)
EDITOR skupin - cist (0) /psat (1)/mazat (2)
EDITOR osob - cist (0) /psat (1)/mazat (2)
EDITOR pripadu - cist (0) /psat (1)/mazat (2)