# VadaProject / vada-project

An Apache+PHP+SQL application for collaborative philosophical debate.

## Getting started (local development)

These instructions are optimized for [XAMPP](https://www.apachefriends.org/download.html), a pre-configured distribution Apache distribution containing MariaDB, PHP, and Perl. You can try a simpler, more light-weight development server like [MAMP](https://www.mamp.info/en/downloads/). If you're living in the 21st century, you could also use something like Docker.

1. Install [XAMPP](https://www.apachefriends.org/download.html) (>=v8.x.x)
   - Note your document root folder; you'll need this later.
      - On macOS, this is this `/Applications/XAMPP/xamppfiles/htdocs/`
      - On Windows this is normally `C:/xampp/htdocs/`
1. Clone this repository into document root, and name the subfolder `vada`.
   - From CLI:
      ```sh
        cd path/to/xamppfiles/htdocs/
        git clone https://github.com/VadaProject/vada-project.git vada
      ```
   - Using [GitHub Desktop](https://desktop.github.com/):
      - "Clone a repository from the internet" > https://github.com/VadaProject/vada-project/
      - Set Local Path to "path/to/xampp/htdocs/vada"
      - Click "Clone"
1. Set up redirects
   - Copy .htaccess_root_example from this directory to its parent folder htdocs/ and name it .htaccess
   - If you're on Windows, you'll probably need to enable viewing hidden files.
3. Install [Composer](https://getcomposer.org/download/), either locally or globally.
   - Composer installer for Windows: <https://getcomposer.org/Composer-Setup.exe>
   - On macOS using Homebrew: `brew install composer`
4. Open a command prompt in this directory and run Composer:
   - ```sh
      composer install # gets dependencies and dumps autoloader files
      ```
1. Set up Database...
   1. Log into the phpMyAdmin dashboard at <https://localhost/phpmyadmin/>
      - XAMPP's default MySQL username is root, with no password.
   1. From the SQL tab, load and execute the script sql/vadaProject.sql.
      - This should create a `VadaProjectDB` database, and its tables.
   1. Optional step: load and execute the script in sql/create_dummy_claims.sql
      - This contains sample claims that might be useful for testing.
   1. Lastly, run databaseUpdate.sql
      - Currently necessary because I haven't regenerated the old scripts yet.
   3. Create a new database user with the username `VadaUser`, and a secure password.
      - **IMPORTANT:** Change the user's hostname from '`%`' to `localhost`.
1. Configure database connection variables...
   1. Copy config/.env.example.php to config/.env.php.
   2. Fill in the values...
      ```php
      $DB_SERVER = 'localhost';
      $DB_USERNAME = 'VadaUser';
      $DB_PASSWORD = '== MY PASSWORD ==';
      $DB_DATABASE = 'VadaProjectDB';
      ```
1. A local instance of The Vada Project should now be functional at <https://localhost/>.
   - If something went catastrophically wrong, create an Issue in this repository. 

## Deploying to production

We currently have a production server running at <https://vadaproject.com>. This runs a [webhook](https://github.com/VadaProject/vada-project/settings/hooks) and automatically pulls changes every time `main` is pushed to. The steps for making a new one of these are fairly similar to the above, although you may need to use a UI like hPanel.

## Development

If you don't already have an IDE of choice, I recommend [Visual Studio Code](https://code.visualstudio.com) and [DevSense PHP Tools](https://www.devsense.com/en). If you're a student, you can get an educational license of the Pro version.

You should also set up XAMPP's included instance of [XDebug](https://xdebug.org/docs/step_debug), so you can use it for step-debugging and breakpoints. The steps to do so are platform dependent and really confusing. Good luck!

### Other resources
- [Official PHP Docs](https://www.php.net/)
- [PHP The Right Way](https://phptherightway.com/) – MUST READ THIS
- [EasyDB README.md](https://github.com/paragonie/easydb) (database wrapper)
