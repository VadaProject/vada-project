<?php

namespace Vada\Model;

class Topic
{

    public int $id;
    public string $name;
    public string|null $description;
    public function __construct(int $id = -1, string $name, string $description = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
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