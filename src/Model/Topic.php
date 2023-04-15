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
    public function getURL()
    {
        return "topic.php?tid={$this->id}";
    }

    /**
     * @return string The escaped HTML for the description.
     */
    public function getDescriptionHTML() {
        if (!isset($this->description) || $this->description === "") {
            return '<span class="empty">(no description)</span>';
        }
        return htmlspecialchars($this->description);
    }
}