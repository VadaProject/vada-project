# VadaProject / application

*Warning: This document is a work in progress, and is currently only ready for internal usage.*

This repository holds the PHP files which make up the application's front-end.

## Installation (local development)

1. Download and install [XAMPP](https://www.apachefriends.org/download.html).
    * From the control panel, start the Apache and MySQL servers.
1. From XAMPP's `htdocs/` folder, clone this repository, and name the folder `directory`.
    * ```sh
      cd [xampp_root]/htdocs
      git clone <repo_url>.git directory
1. Log into the phpMyAdmin dashboard (usually at <https://localhost/phpmyadmin/>)
1. Create a new database called `vadaProject`.
    * From the SQL tab, load and execute the commands in `vadaProject.sql`.
    * This should populate the database with two tables.
1. Create a new user account.
    * Set username, password to match the ones in `config/db_connect.php`.
    * Change hostname from '`%`' to `localhost`.
    * 🚨 For security reasons, this should be refactored to load database credentials from a .env file instead.
1. A local instance of the Vada Project should now be functional at <https://localhost/directory>.

## Deploying

T.B.D
