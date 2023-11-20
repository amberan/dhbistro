# BISTRO

[![pipeline status](https://gitlab.com/alembiq/bistro/badges/master/pipeline.svg)](https://gitlab.com/alembiq/bistro/commits/master)
[![license](https://img.shields.io/github/license/amberan/dhbistro.svg)](https://gitlab.com/alembiq/bistro/blob/master/LICENSE)
[![lines of code](https://tokei.rs/b1/github/amberan/dhbistro)](https://tokei.rs/b1/github/amberan/dhbistro)
[![Latest Release](https://gitlab.com/alembiq/bistro/-/badges/release.svg)](https://gitlab.com/alembiq/bistro/-/releases)

**BISTRO** is a powerful tool designed to help you manage people, groups, investigation cases, and reports in the context of LARP games. Whether you belong to the Night Watch or Day Watch, BISTRO is here to keep you organized in the world of immersive storytelling.

## About

[BiStro](https://gitlab.com/alembiq/bistro/) is actively used by players of the Czech LARP [Pražská Hlídka](http://www.prazskahlidka.cz/), a cyclical LARP inspired by [Sergei Lukyanenko's Night Watch](https://en.wikipedia.org/wiki/Night_Watch_(Lukyanenko_novel)).

The history of BiStro dates back to 2006 when it was created for the first Watch game, part of Dies Irae. The core functionality was initially written in PHP 5 by [Ethan](https://github.com/ethanius). Over the years, [Amberan](https://github.com/amberan) took over and introduced source code management tools. Collaborating with [Atlan](https://github.com/czAtlan), they added numerous new features. Subsequently, [Ernedar](https://github.com/Ernedar) joined the development team.

At the end of 2018, [Charles](https://gitlab.com/alembiq) updated the code to work with PHP 7 and initiated a comprehensive refactoring of the entire system. This effort aimed to understand the system better and explore the possibility of migrating data to a different system. Alongside this, the idea of a new user interface emerged. Currently, the team is in the process of gradually refactoring the old code while updating the UI.

## Features

- Track people, groups, investigation cases, and reports
- Manage user access permissions, including visibility of secret items
- Record user actions and browse through activities in the Audit page

## Usage

### Running BISTRO

#### Docker
- Prerequisites: Docker, Docker-compose
1. Update [docker/docker-compose.yml](docker/docker-compose.yml#L9) to run under the user (UID:GID) who owns the folder with the source code.
2. Go to the `docker` directory and run `docker-compose up -d`. Docker will download a few images, and in a few minutes, your BISTRO should be running on [localhost](http://localhost), with [Adminer](http://localhost:8080) available to you.


#### LAMP
- Prerequisites: Apache2 (modules header, rewrite), MariaDB, PHP 8.x (modules mysql, xml, gd, zip, curl, mbstring), Composer
1. Clone the repository and run composer:
   ```bash
   git clone git@gitlab.com:alembiq/bistro.git && cd bistro && composer install
2. Point Apache to the BISTRO folder.
3. Create a database and user.
4. Open BISTRO in your browser - you'll get the installer.

## Development
To turn on debugging mode, add the following line to `source/.env` or `source/config.php`:
```
$config['logLevel'] = ['E', 'W', 'N', 'D'];
```
All logs, including exceptions logged by Tracy, are stored in the `log` folder.

## Resources
- [Changelog](CHANGELOG.md)
- [Contributing](CONTRIBUTING.md)
- [enumerators](doc/enums.md)
- [files & folder structure](doc/files.md)
- [reverse engineered user permissions](doc/rights.md)
- [API prototype](doc/api.md)


## FAQ
- Lost all passwords?
    ```sql
    UPDATE nw_user SET userPassword=md5('newPassword');
    ```

- Updating your production database for testing (removing secret information and randomly marking some as secret)
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
    UPDATE nw_report SET reportSecret=1 WHERE reportDeleted = 0 AND RAND() < 0.3;
    UPDATE nw_symbol SET secret=1 WHERE deleted = 0 AND RAND() < 0.3;

    UPDATE nw_user SET aclAPI=1, aclAudit=1, aclCase=2, aclUser=1, aclNews=1, aclBoard=1, aclGamemaster=1, aclGroup=2,
    aclHunt=1, aclPerson=2, aclRoot=1, aclSecret=2, aclReport=2, aclSymbol=2 where userSuspended=0 and userDeleted=0;
    ```

- Default user after installation: User in the default dataset is Shiva with password Shiva.

**Feel free to customize the content further or let us know if you have any specific requests!**
