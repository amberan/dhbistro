

## Folder structure

| folder            | purpose |
| ---               | --- |
| /source/API/             | prototype of rest API |
| /source/css/             | Cascade Style Sheets |
| /source/custom/          | customization for all the main instances of Bistro + text files |
| /doc/             | whatever little documentation that exists |
| /source/files/           | attachments from the user space |
| /source/images/          | graphics used in site |
| /source/inc/             | legacy libraries and shared fnc |
| /source/js/              | javascript scripts |
| /source/lib/             | libraries |
| /source/pages/           | pages |
| /source/sql/             | empty db and update configurations |
| /source/templates/       | [Latte templates](https://latte.nette.org/) |
| /testsuite/       | SoapUI test suite for REST API |
| /source/vendor/          | external libraries |
| .git | [repository](https://gitlab.com/alembiq/bistro/) |
| .gitlab | GitLab templates for issues |
| .codeclimate.yml | CodeClimate configuration |
| ecs.php | easy coding style configuration |
| .gitconfig | |
| .gitignore | list of files that git ignores|
| .gitlab-ci.yml | CI/CD pipeline definition|
| .php-cs-fixer.php | php coding style fixer configuration|

Legacy pages are using `source/inc/func_main.php` which is linking all the libraries and functionality. In the new code we're starting with `source/index.php` which pulls configuration from `source/config.php` and database setting from `source/.env.php`. Then loading libraries and providing general functionality to the rest of the site. In the last step (called THE LOOP) it's deciding what pages to generate for user, based on URL and POST data.
