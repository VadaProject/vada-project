<?php declare(strict_types=1);
namespace Vada\Model;

use \ParagonIE\EasyDB;

/**
 * Helper class for getting a database connection
 */
class Database
{   
    public static function connect()
    {
        require __DIR__ . '../../../config/.env.php';
        try {
            return EasyDB\Factory::fromArray([
                "mysql:host=$DB_SERVER;dbname=$DB_DATABASE",
                $DB_USERNAME,
                $DB_PASSWORD
            ]);
        } catch (EasyDB\Exception\EasyDBException $e) {
            error_log($e->getMessage());
            exit('Error connecting to database'); //Should be a message a typical user could understand
        }
    }
}
