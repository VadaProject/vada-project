<?php

function db_connect()
{
    // connect to database
    $conn = mysqli_connect('localhost', 'amydonahue', 'amypassword', 'vadaProject');

    // check connection
    if (!$conn) {
        echo 'Connection Error: ' . mysqli_connect_error();
        return false;
    }
    return $conn;
}
