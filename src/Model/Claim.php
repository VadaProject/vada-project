<?php
namespace Vada\Model;

/**
 * A plain data object representing the values of a 
 */
abstract class Claim {
    public int|null $id;
    public string $topic_id;
    public string $subject;
    public string $targetP;
    public bool $active;
}