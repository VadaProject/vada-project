<?php declare(strict_types=1);
namespace Vada\Model;
use \PDO;
use \PDOException;

class Database
{   
    public static function connect()
    {
        require __DIR__ . '../../../config/.env.php';
        try {
            $conn = new PDO("mysql:host=$DB_SERVER;port=3306;dbname=$DB_DATABASE;charset=utf8mb4", $DB_USERNAME, $DB_PASSWORD);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            exit('Error connecting to database'); //Should be a message a typical user could understand
        }
    }
}
