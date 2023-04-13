# VadaProject / application

_Warning: This README.md document is a work in progress, and is currently only ready for internal usage._

This repository holds the PHP files which make up the application's front-end.

## Deploying for local development

### Initialize Apache web server

1. Download and install [XAMPP](https://www.apachefriends.org/download.html) and [Composer](https://getcomposer.org/download/).
1. Clone this repository into XAMPP's `htdocs/` folder and name the folder `vada`.
   - ```sh
     cd [xampp_root]/htdocs
     git clone https://github.com/VadaProject/application.git vada
     ```
1. Run Composer:
   - ```sh
      composer install       # install dependencies
      composer dump-autoload # create 
      ```
1. From XAMPP's control panel, start the Apache and MySQL servers.

### Initialize database

1. Log into the phpMyAdmin dashboard at <https://localhost/phpmyadmin/>
1. From the SQL tab, load and execute the script [vadaProject.sql](vadaProject.sql).
   - This should create a `vadaProject` database and its tables.
1. Optional step: load and execute the script [create_dummy_claims.sql](create_dummy_claims.sql).
1. Create a new account with the username `vadaUser`, and a secure password.
   - Change the user's hostname from '`%`' to `localhost`.

### Configure database authentication
1. Create the file [config/.env.php](config/.env.php) with the following text:
      ```php
      $DB_SERVER = 'localhost';
      $DB_USERNAME = 'vadaUser';
      $DB_PASSWORD = '== MY PASSWORD ==';
      $DB_DATABASE = 'vadaProject';
      ```
1. A local instance of the Vada Project should now be functional at <https://localhost/directory>.

## Deploying

T.B.D

## Tooling and development

To ensure consistency, please use [Visual Studio Code](https://code.visualstudio.com) alongside the following linting utilities.

1. Install [Composer](https://getcomposer.org/doc/00-intro.md) and [PHP]()
   - macOS: available on [Homebrew](https://formulae.brew.sh/formula/composer) (`brew install php composer`)
   - Windows and Linux install guide
1. Install Composer dependencies:
   - ```sh
     composer install
     ```
   - Binaries for [GrumPHP](https://github.com/phpro/grumphp), [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer), and [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) will be installed in `./vendor/bin/`
1. Install [the suggested extensions](.vscode/extensions.json) in VS Code.
   - A popup should appear upon opening the project for the first time.
   - Warnings from PHP_Codesniffer should now appear in the "Problems" tab
1. Before comitting, activate GrumPHP to show errors:
   - ```sh
     ./vendor/bin/grumphp run
     ```
