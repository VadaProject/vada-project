<?php

namespace Vada\Model;

class Topic
{

    public int $id;
    public string $name;
    public string|null $description;
    public \DateTime $ts;
    
    public function __construct(int $id = -1, string $name, string $description = null, string $ts = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->ts = new \DateTime($ts);
    }

    public function hasDescription()
    {
        return strlen($this->description ?? "") > 0;
    }
    public function getURL()
    {
        return "topic.php?tid={$this->id}";
    }
}