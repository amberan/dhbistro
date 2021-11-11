# BISTRO changelog


<!--
git log --no-merges 1.5.. | unexpand -a | sed -e 's/\s\s*$$//' | head -n 2070 | grep -v "Date\|commit\|Author\|typo\|.gitlab-ci.yml\|.codeclimate.yml" | sed -r '/^\s*$/d' | uniq

https://github.com/orhun/git-cliff
git-cliff 0e4e3abba94636c210fde5e7d02089298589f26d..HEAD --output CHANGELOG.md

  -->

**1.8.3**
- feature: link from note to parent [#201](https://gitlab.com/alembiq/bistro/issues/201)
- fix: uploading files [#197](https://gitlab.com/alembiq/bistro/issues/197)
- fix: edit report date [#196](https://gitlab.com/alembiq/bistro/issues/196)
- fix: assigning people to case [#195](https://gitlab.com/alembiq/bistro/issues/195)
- feature: help wanted [#175](https://gitlab.com/alembiq/bistro/issues/175)
- refactor: antidating person [#183](https://gitlab.com/alembiq/bistro/issues/183)
- refactor: Director permission to User [#178](https://gitlab.com/alembiq/bistro/issues/178)
- feature: groups status [#174](https://gitlab.com/alembiq/bistro/issues/175)
- style: marking of unread items [#173](https://gitlab.com/alembiq/bistro/issues/173)
- fix: archived flag on persons [#171](https://gitlab.com/alembiq/bistro/issues/171)
- fix: assigning persons to users [#172](https://gitlab.com/alembiq/bistro/issues/172)

**1.8.2 HLIDKY november 2021 release**
- style: cases listing [#118](https://gitlab.com/alembiq/bistro/issues/118)
- refactor: faster report listing and fixed sorting [#158](https://gitlab.com/alembiq/bistro/issues/158)
- fix: saving news [#156](https://gitlab.com/alembiq/bistro/issues/156)
- database: removal of old columns from dashboard table [#160](https://gitlab.com/alembiq/bistro/issues/160)
- refactor: `commonmark` replaced with `TUI editor` [#163](https://gitlab.com/alembiq/bistro/issues/163)
- fix: filtering symbols [#154](https://gitlab.com/alembiq/bistro/issues/154)
- fix: person listing on report [#161](https://gitlab.com/alembiq/bistro/issues/161)
- refactor: group detail filtering [#164](https://gitlab.com/alembiq/bistro/issues/164)
- refactor: validation of permission secret [#165](https://gitlab.com/alembiq/bistro/issues/165)
- ci: moved to gitlab.com [#166](https://gitlab.com/alembiq/bistro/issues/166)
- ci: reconfigured easy coding standard [#151](https://gitlab.com/alembiq/bistro/issues/151)

**1.8.1**
- refactor: groups filtering [#59](https://gitlab.com/alembiq/bistro/issues/59)
- database: change all tables to InnoDB [#150](https://gitlab.com/alembiq/bistro/issues/150)
- database: persons archived flag changed from int to timestamp [#140](https://gitlab.com/alembiq/bistro/issues/140)
- fix: adding removed persons to report [#149](https://gitlab.com/alembiq/bistro/issues/149)
- feature: persons `roof` [#40](https://gitlab.com/alembiq/bistro/issues/40)
- style: groups listing [#118](https://gitlab.com/alembiq/bistro/issues/117)

**1.7.6**
- refactoring: PHP warnings cleanup
- feature: displaying date of creation in search
- feature: showing flags in search
- feature: case date of creation [#51](https://gitlab.com/alembiq/bistro/issues/51)
- feature: group date of creation [#52](https://gitlab.com/alembiq/bistro/issues/52)

**1.7.5**
- feature: new permissions for reports and symbols [#132](https://gitlab.com/alembiq/bistro/issues/132)

**1.7.4**
- feature: symbols can be archived [#22](https://gitlab.com/alembiq/bistro/issues/22)

**1.7.3**
- feature: sorting on object listings [#87](https://gitlab.com/alembiq/bistro/issues/87)
- refactor: WIP cases filtering [#59](https://gitlab.com/alembiq/bistro/issues/59)
- style: cases listing [#118](https://gitlab.com/alembiq/bistro/issues/118)
- refactor: WIP removal of proccase.php [#106](https://gitlab.com/alembiq/bistro/issues/106)
- ci: new test deployment [#127](https://gitlab.com/alembiq/bistro/issues/127)

**1.7.2 Access 2.0**
- refactor: new permissions [#49](https://gitlab.com/alembiq/bistro/issues/49)
- refactor: updated user management [#49](https://gitlab.com/alembiq/bistro/issues/49) [#101](https://gitlab.com/alembiq/bistro/issues/101)
- refactor: sessions [#89](https://gitlab.com/alembiq/bistro/issues/89)
- feature: THE LOOP [#49](https://gitlab.com/alembiq/bistro/issues/49) [#89](https://gitlab.com/alembiq/bistro/issues/89)
- refactor: dashboard listing [#104](https://gitlab.com/alembiq/bistro/issues/104)

**1.7.1**
- feature: sorting [#87](https://gitlab.com/alembiq/bistro/issues/87)

**1.7.0 HLIDKY march 2020 release**

**1.6.7**
- ci: CODECLIMATE cleanup
- feature: search displays flags[#53](https://gitlab.com/alembiq/bistro/issues/53) [#85](https://gitlab.com/alembiq/bistro/issues/85)
- fix: user management displayes only unclosed cases [#84](https://gitlab.com/alembiq/bistro/issues/84)
- fix: email not mandatory in settings [#83](https://gitlab.com/alembiq/bistro/issues/83)
- fix: adding attachements [#86](https://gitlab.com/alembiq/bistro/issues/86)
- style: login screen[#75](https://gitlab.com/alembiq/bistro/issues/75)
- style: notifications [#17](https://gitlab.com/alembiq/bistro/issues/17)
- feature: themes [#72](https://gitlab.com/alembiq/bistro/issues/72)

**1.6.6**
- db: minor fix

**1.6.5**
- feature: self link in settings [#46](https://gitlab.com/alembiq/bistro/issues/46)
- feature: installer [#70](https://gitlab.com/alembiq/bistro/issues/70)
- fix: overflowing menu [#9](https://gitlab.com/alembiq/bistro/issues/9)
- style: news [#56](https://gitlab.com/alembiq/bistro/issues/56)
- style: board [#55](https://gitlab.com/alembiq/bistro/issues/55)
- feature: email in settings [#57](https://gitlab.com/alembiq/bistro/issues/57)
- style: backups [#58](https://gitlab.com/alembiq/bistro/issues/58)

**1.6.4**
- fix: displaying Tracy [#8](https://gitlab.com/alembiq/bistro/issues/8)
- feature: user email [#44](https://gitlab.com/alembiq/bistro/issues/44)

**1.6.3**
- refactor: news, board to MD
- ci: added [thephpleague/commonmark](https://github.com/thephpleague/commonmark) markdown2html convertor
- fix: saving board [#13](https://gitlab.com/alembiq/bistro/issues/13)

**1.6.2**
- refactor: search
- feature: .htaccess
- feature: THE LOOP: settings, user management, backup
- style: manual backup
- style: user management
- feature: linking users to people
- ci: easy coding standard
- ci: gitlab ci
- database: removal of unused tables

**1.6.1**
- feature: CDN [TUI.editor](https://github.com/nhn/tui.editor)
- style: settings
- refactor: user plans to MD [html-to-markdown](https://github.com/thephpleague/html-to-markdown)
- feature: search in menu
- feature: groups display attached pictures

**1.6.0**
- feature: functions for reading persons  (processing/person.php)
- feature: pull trigger
- refactoring: unread duplicity cleanup
- feature: unread numbers in menu
- refactoring: style images to /images/
- refactoring: backup
- feature: API login, logout, personRead
- doc: init
- test: SoapUI testsuite
- database: table rename - loggedin, map, backups, cases, data, groups, notes, persons, reports, tasks, symbols, users

**1.5.8 HLIDKY march 2019 release**
- style: login, header, footer
- feature: HTMLtoMD convertor
- feature: updater: alter table func
- fix: new user
- refactor: same func for users and myself
- feature: Tracy logging /log/info.log
- refactor: unified datum formating
- fix: search in deleted notes
- style: replacement of PageStart and PageEnd

**1.5.7**
- refactor: search - ignore national characters, lowercase/uppercase
- security: sql injection
- refactor: backup & update
- ci: latte, tracy, jquery, tinyMCE
- refactor: separation of db connetion and login
- refactor: custom texts
- database: markdown columns

**1.5.6 HLIDKY november 2018**
- style: login
- style: user management
- feature: user mangement permissions editable
- performance: removal od unread for deleted users
- feature: date of creation and modification of person, search, case displayed
- fix: showing attachements to report, person, case
- feature: phone numbers as active links
- feature: news delete
- feature: creation date of news
- security: sql injection $_REQUEST,$_POST,$_GET
- fix: genarating new passwords
- fix: sorting reports by time

**1.5.5 SUSPEND USER**
- refactor: news extracted from index.php
- fix: saving notes with empty name
- feature: password reset
- feature: user lock, unlock

**1.5.4 MD5 PASSWORD**
- security: md5 password
- refactor: `goto` removed
- refactor: valid html
- refactor: css files to /css/
- refactor: backup fallback for older db
- refactor: debug data on attachements
- refactor: backup compression
- refactor: settings

**1.5.3 SESSION MANAGEMENT REWORK**
- refactor: session management
- security: sql injection for $_REQUEST
- refactor: configurations to $conf[]
- feature: debug mode

**1.5.2 database UPDATE SCRIPT**
- fix: page processing timer
- refactor: <head /> cleanup
- feature: db updater

**1.5.1 BACKUP UPDATE**
- refactor: separation of /inc/func_main.php
- refactor: backup (INDEX>FULLTEXT)

**1.5 PHP7**
- refactor: PHP7 + MySQLi
