# VadaProject / application

_Warning: This document is a work in progress, and is currently only ready for internal usage._

This repository holds the PHP files which make up the application's front-end.

## Installation (local development)

1. Download and install...
   - [XAMPP](https://www.apachefriends.org/download.html)
   - [Composer](https://getcomposer.org/download/) (globally)
     - macOS: `brew install php composer`
   - [PHP-CS-Fixer]()
     - ```sh
       composer require --global friendsofphp/php-cs-fixer
       ```
1. From the control panel, start the Apache and MySQL servers.
1. From XAMPP's `htdocs/` folder, clone this repository, and name the folder `directory`.
   - ```sh
     cd [xampp_root]/htdocs
     git clone <repo_url>.git directory
     ```
1. Log into the phpMyAdmin dashboard (usually at <https://localhost/phpmyadmin/>)
1. Create a new database called `vadaProject`.
   - From the SQL tab, load and execute the commands in `vadaProject.sql`.
   - This should populate the database with two tables.
1. Create a new user account.
   - Set username, password to match the ones in `config/db_connect.php`.
   - Change hostname from '`%`' to `localhost`.
   - 🚨 For security reasons, this should be refactored to load database credentials from a .env file instead.
1. A local instance of the Vada Project should now be functional at <https://localhost/directory>.

## Deploying

T.B.D

## Tooling and development

To ensure consistency, please use [Visual Studio Code](https://code.visualstudio.com), and run the following linting utilities.

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
