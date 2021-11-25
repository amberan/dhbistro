

## Folder structure

| folder            | purpose |
| ---               | --- |
| /API/             | prototype of rest API |
| /css/             | Cascade Style Sheets |
| /custom/          | customization for all the main instances of Bistro + text files |
| /doc/             | whatever little documentation that exists |
| /files/           | attachments from the user space |
| /images/          | graphics used in site |
| /inc/             | legacy libraries and shared fnc |
| /js/              | javascript scripts |
| /lib/             | libraries |
| /pages/           | pages |
| /sql/             | empty db and update configurations |
| /templates/       | [Latte templates](https://latte.nette.org/) |
| /testsuite/       | SoapUI test suite for REST API |
| /vendor/          | external libraries |

Legacy pages are using `inc/func_main.php` which is linking all the libraries and functionality. In the new code we're starting with `index.php` which pulls configuration from `config.php` and database setting from `.env.php`. Then loading libraries and providing general functionality to the rest of the site. In the last step (called THE LOOP) it's deciding what pages to generate for user, based on URL and POST data.
