<?php

/**
 *
 */
function db_connect()
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try {
        $conn = mysqli_connect('localhost', 'amydonahue', 'amypassword', 'vadaProject');
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        error_log($e->getMessage());
        exit('Error connecting to database'); //Should be a message a typical user could understand
    }
}
