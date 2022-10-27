<?php

require_once 'Database.php';
use Database\Database;

// TOOD: merge this and the doesThesisFlag functions

function haveRival($claimid)
{
    // TODO: if we do it a lot, this operation could probably be reduced to a SQL query
    $flaggers = Database::getFlaggedRivals($claimid);
    return count($flaggers) > 0;
}
