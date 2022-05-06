[![pipeline status](https://gitlab.com/alembiq/bistro/badges/master/pipeline.svg)](https://gitlab.com/alembiq/bistro/commits/master)
[![license](https://img.shields.io/github/license/amberan/dhbistro.svg)](https://gitlab.com/alembiq/bistro/blob/master/LICENSE)
[![lines of code](https://tokei.rs/b1/github/amberan/dhbistro)](https://tokei.rs/b1/github/amberan/dhbistro)


# [BISTRO](https://gitlab.com/alembiq/bistro)

**Are you one of the Others? A member of the Night Watch or Day Watch? BiStro is here for you and will try to keep you from getting lost in  all the gloom related informations!**


[BiStro](https://github.com/amberan/dhbistro/) was originally created to help players of Czech LARP [Pražská Hlídka](http://www.prazskahlidka.cz/). This cyclical larp is inspired by [Sergei Lukyanenko Night Watch](https://en.wikipedia.org/wiki/Night_Watch_(Lukyanenko_novel)).
In-game it's used by two teams (Day and Night watch) to keep tracks of characters and events especially between iterations of the game.

## BiStro needs you!
No doubt you've noticed BiStro is not a professional product, and at this moment individual components look quite different. This is due to ongoing refactoring of GUI to be usable also on mobile devices. However, our efforts to rewrite the user interface is much more complicated and time consuming that we expected.  

Based on the current knowledge we know that rewriting BiStro in two people (one PHP, one HTML/CSS) is doable, but it would take too long and the user experience in the meantime would be greatly diminished. That's why we are looking for help, we could use more programmers but also some permanent testers.  

Just for the context - history of BiStro starts in 2006 - it was created for the first Watch game, part of Dies Irae. That's when Ethan wrote the core functionality in PHP (version 5 at the time). Years later Amberan took over and started using source code management tools. Together with Atlan they've added a lot of new functionality. A few years later, Ernedar joined. At the end of 2018 Charles updated the code to work with PHP 7 and started refactoring the whole system in an effort to understand how it works (and if we shouldn't just moved the data to some other system). While getting some understanding of the code an idea that came, idea of new UI. That's where we're now, slowly chewing up the old code and refactoring it while changing the UI.

At the moment we are targeting the following technologies PHP 7, MariaDB, Bootstrap, Latte and tui.editor. Of course, everything could be written on the green field with a mobile app and everything, but we really don't have the time and energy for that.   

P.S. If you don't know how to code but still want to help, you can always help us with testing or create user documentation.   

## running BiStro
It's a simple php website, you need just the basics - LAMP :)
### Prerequisities
- Apache2
    apache modules:
    - headers
    - rewrite
- MariaDB
- PHP7
    php modules:
    - php-mysql
    - php-xml
    - php-gd
    - php-zip
    - php-curl
    - php-cli
    - php-mbstring
### Installation
- clone repository & run composer
```bash
git clone git@gitlab.com:alembiq/bistro.git && cd bistro && composer install && composer update
```
- point apache to the BiStro folder
- create database & user
- open BiStro in your browser - you'll get installer :)

## [Changelog](CHANGELOG.md)

## [Contributing](CONTRIBUTING.md)

## Documentation
There is none:( docs below are just notes created during reverse engeneering phase.
- [enumerators](doc/enums.md)
- [files & folder structure](doc/files.md)
- [reverse engineered user permissions](doc/rights.md)
- [API prototype](doc/api.md)

## FAQ

- Lost all passwords?
```sql
UPDATE nw_user SET pwd=md5('newpassword') WHERE id='1';
```
- Prepare data for testing
```sql
DELETE FROM nw_case WHERE secret>0;
DELETE FROM nw_file WHERE secret>0;
DELETE FROM nw_group WHERE secret>0;
DELETE FROM nw_note WHERE secret>0;
DELETE FROM nw_person WHERE secret>0;
DELETE FROM nw_report WHERE secret>0;
DELETE FROM nw_symbol WHERE secret>0;

UPDATE nw_case SET secret=1 WHERE deleted = 0 AND RAND() < 0.3;
UPDATE nw_file SET secret=1 WHERE RAND() < 0.3;
UPDATE nw_group SET secret=1 WHERE deleted = 0 AND RAND() < 0.3;
UPDATE nw_note SET secret=1 WHERE deleted = 0 AND RAND() < 0.3;
UPDATE nw_person SET secret=1 WHERE deleted = 0 AND RAND() < 0.3;
UPDATE nw_report SET secret=1 WHERE deleted = 0 AND RAND() < 0.3;
UPDATE nw_symbol SET secret=1 WHERE deleted = 0 AND RAND() < 0.3;

UPDATE nw_user SET aclAPI=1, aclAudit=1, aclCase=2, aclUser=1, aclNews=1, aclBoard=1, aclGamemaster=1, aclGroup=2,
aclHunt=1, aclPerson=2, aclRoot=1, aclSecret=2, aclReport=2, aclSymbol=2 where userSuspended=0 and userDeleted=0;
```
