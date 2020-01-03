# [BISTRO](https://github.com/amberan/dhbistro)

Tato webovka byla vyvynuta pro potřeby LARPu [Pražská Hlídka](http://www.prazskahlidka.cz/).  
Jedná se o systém používany organizacemi (Denní Hlídka, Noční Hlídka) ve hře. 
Který pomáhá udržovat informace o jednotlivých osobách, skupinách, akcích atp.  

## Disclaimer

This is a very old project, currently beeing reworked to more maintanable state.

## Folder structure
- /API/ 
- /css/ 
- /custom/ - customisation for all the main instances of Bistro + text files
- /doc/
- /files/ - attachments (to be moved to /files/attachement)
  - backups/ - local db backups 
  - portraits/ - photographs of "person"
  - symbols/ - symbols of "person"
- /images/ 
- /inc/ - internal libraries and shared functionality (mostly legacy code)
- /js/ 
- /processing/ - libraries
- /sql/ - database update scripts and empty database templates
- /templates/ - [Latte templates](https://latte.nette.org/)
- /testsuite/ - SoapUI test suite for REST API
- /vendor/ - external libraries
  - [latte/latte](https://latte.nette.org/)
  - [league/commonmark](https://github.com/thephpleague/commonmark)
  - [league/html-to-markdown](https://github.com/thephpleague/html-to-markdown)
  - [tracy/tracy](https://tracy.nette.org/)

## Running your own Bistro

To run your own Bistro is recommended a LAMP server. You need a MySQL/MariaDB 
database (to be configured in [inc/platform.php](https://gitlab.alembiq.net/larp/bistro/raw/master/inc/platform.php), password for database
needs to be put into /inc/important.php (whole of the second line of the file is
used as a password).
At this point Bistro isn't able to populate an empty database, but you can use 
a script in the sql folder named default-`version`.sql. This will create an empty
database structure with user admin.

### Troubleshooting

**admin password recovery**  
UPDATE nw_user SET pwd=md5('newpassword') WHERE id='1';


### [files description](doc/files.md)

### [API](doc/api.md)

### [uzivatelska prava](doc/rights.md)

### [ciselniky](doc/enums.md)

### TEST PLATFORMS
http://nhtestbistro.talmahera.eu/  
ftp://www.talmahera.eu/nhtestbistro.talmahera.eu/error_log  
http://bistro.alembiq.net  
http://dhtestbistro.talmahera.eu/  
ftp://www.talmahera.eu/dhtestbistro.talmahera.eu/error_log  
