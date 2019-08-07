### OBSAH
soubor                  | obsah                                     | obsluhovany objekt
--- | --- | ---
API/include.php         | API nacitani generik
API/login.php           | API login
API/logout.php          | API logout
API/personread.php      | API personRead,personList                 | osoba
css						| **styly**
--- | --- | ---
customs					| **individualni texty a obrazky pro jednotlive instance**
custom/text-DH.php		| texty pro Denni Hlidku
custom/text-enigma.php	| texty pro Enigma
custom/text-NH.php		| texty pro Nocni Hlidku
--- | --- | ---
files					| **prilohy**
files/backups/*			| generovane zalohy SQL.GZ
files/portraits/*		| portrety IMG
files/symbols/*			| symboly IMG
--- | --- | ---
images					| **grafika pouzivana CSSky**
--- | --- | ---
inc/audit_trail.php     | generovani auditni stopy + zamezeni pristupu
inc/backup.php          | generator zaloh
inc/database.php        | pripojeni a nastaveni databaze
inc/footer.php          | fnc footer
inc/func_main.php       | knihovna balastu - pouzivane vsude
inc/header.php          | fnc header
inc/image.php			| manipulace s obrazky
inc/important.php       | heslo databaze
inc/menu.php            | generovani menu
inc/platform.php		| konfiguracni udaje jednotlivych platforem
inc/session.php         | obsluha session uzivatele
inc/unread.php          | obsluha neprectenych objektu
--- | --- | ---
js/tinymce5/*			| TinyMCE 5.0
js/jquery-3.3.1.min.js  | jQuery 3.3.1
js/mrFixit.js			| nevim, vyzaduje jQuery
js/timeout.js.php		| timeout pro ukladaci disketu
js/tinymce.init.js		| iniciace a konfigurace TinyMCE
--- | --- | ---
log 					| **adresar pro logovani**
log/error.log			| PHP Notice, Warning
log/exception.log		| PHP kolapsy
log/info.log			| vystup Tracy logovani
--- | --- | ---
processing/users.php    | zpracovani uzivatele                      | uzivatele
processing/settings.php	| zpracovani osobnich nastaveni
processing/person.php	| opeace s objekty osoby					| osoba
--- | --- | ---
sql						| **SQL updates, defaults**
--- | --- | ---
templates				| **LATTE templaty**
--- | --- | ---
vendor					| **pouzivane knihovny "composer"
--- | --- | ---
addar2c.php             | uprava hlaseni - report 2 case            | report / pripad
addc2ar.php             | uprava hlaseni - case 2 report            | pripad / report
addp2ar.php             | uprava hlaseni - person 2 report          | osoba / report
addp2c.php              | uprava pripadu - person 2 case            | osoba / pripad
addp2g.php              | uprava skupiny - person 2 group           | osoba / skupina
addpersons.php          | pridani osoby                             | osoby
addreports.php          | pridani reportu                           | reporty
adds2c.php              | prirazeni symbolu - case                  | symbol / pripad
addsy2ar.php            | prirazeni symbolu - report                | symbol / report
addsy2c.php             | prirazeni symbolu - case                  | symbol / pripad
addsy2p.php             | prirazeni symbolu - person                | symbol / osoba
addsymbols.php          | pridani symbolu                           | symboly
audit.php               | audit
cases.php               | LIST pripadu                              | pripady
dashboard.php           | osobni nastenka
doodle.php              | dostupnost
editactrep.php          | uprava reportu                            | reporty
editcase.php            | uprava pripadu                            | pripady
editdashboard.php       | uprava nastenky
editgroup.php           | uprava skupiny                            | skupiny
editnote.php            | uprava poznamek                           | poznamky
editperson.php          | uprava osoby                              | osoby
editsymbol.php          | uprava symbolu                            | symboly
evilpoints.php          | zlobody
getfile.php             | stazeni prilohy
getportrait.php         | zobrazeni fotografie
groups.php              | LIST skupiny                              | skupiny
index.php               | sdilena nastenka
login.php               | prihlasovaci stranka
logout.php              |
mapagents.php           | mapa agentu
newactrep.php           | novy report                               | reporty
newcase.php             | novy pripad                               | pripady
newgroup.php            | nova skupina                              | skupiny
newnews.php             | nova novinka                              | novinky
newnote.php             | nova poznamka                             | poznamky
newperson.php           | nova osoba                                | osoby
news.php				| LIST novinky								| novinky
newsymbol.php           | novy symbol                               | symboly
orgperson.php           | ORG uprava osoby                          | osoby
persons.php             | LIST osoby                                | osoby
procactrep.php          | zpracovani reportu                        | reporty
proccase.php            | zpracovani pripadu                        | pripady
procgroup.php           | zpracovani skupiny                        | skupiny
procnews.php            | zpracovani novinek                        | novinky
procnote.php            | zpracovani poznamek                       | poznamky
procother.php           | zpracovani 
procperson.php          | zpracovani osob                           | osoby
pull.php				| GIT webhook
readactrep.php          | DETAIL reportu                            | reporty
readcase.php            | DETAIL pripadu                            | pripady
readgroup.php           | DETAIL skupiny                            | skupiny
readnote.php            | DETAIL poznamky                           | poznamky
readperson.php          | DETAIL osoby                              | osoby
readsymbol.php          | DETAIL symbolu                            | symboly
reports.php             | LIST reporty                              | reporty
search.php              | vyhledavani                               | vyhledavani
settings.php            | nastaveni                                 | nastaveni
symbol_search.php       | vyhledavani podle symbolu                 | vyhledavani
symbol_search_result.php| vyhledavani podle symbolu - vysledky      | vyhledavani
symbols.php             | LIST symboly                              | symboly
tasks.php               | LIST ukoly                                | ukoly
users.php               | LIST uzivatele                            | uzivatele
usersedit.php           | uprava uzivatele                          | uzivatele
usersnew.php            | novy uzivatel                             | uzivatele