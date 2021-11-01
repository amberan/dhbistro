# BISTRO user permissions >v1.7.0

||aclRoot|aclGamemaster|aclDirector|aclDeputy|aclTask|aclSecret|aclAudit|aclAPI|aclGroup|aclPerson|aclCase|aclHunt|aclReport|aclSymbol|
--- |:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:
audit                                           |||||||0/1
group                                           |||||||||0/1
person                                          ||||||||||0/1
case                                            |||||||||||0/1
audit trail of gamemasters                      ||0/1
antidation                                      ||0/1
mark as `not new`                               ||0/1
user management                                 |||0/1
secret                                          ||||||n-m
time capacity doodle                            ||||0/1
points assignement                              ||||0/1
task                                            |||||r/w
news                                            ||||0/1
board                                           ||||0/1
backup                                          |0/1
API                                             ||||||||0/r/w
hunt licenses                                   ||||||||||||0/r/w
report                                          |||||||||||||0/1
symbol                                          ||||||||||||||0/1

# BISTRO user permissions <v1.6.7

activity | POWER USER | EDITOR | AUDITOR | ORGANIZATOR
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


## migration matrix
- right_text > aclTask + aclGroup + aclPerson + aclCase
- right_audit > aclAudit
- right_org > aclGamemaster
- right_power > aclDirector + aclDeputy + aclSecret + aclHunt
- right_super > aclRoot
