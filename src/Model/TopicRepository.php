<?php
namespace Vada\Model;

use \ParagonIE\EasyDB\EasyDB;

class TopicRepository
{
    private EasyDB $db;
    public function __construct(EasyDB $db)
    {
        $this->db = $db;
    }
    /**
     * @return Topic|null The topic with the given ID, or null if one does not exist.
     */
    public function getTopicByID(int $topic_id)
    {
        $row = $this->db->row(
            'SELECT id, name, description, ts FROM Topic WHERE id = :id LIMIT 1',
            $topic_id
        );
        if (!$row) {
            return null;
        }
        return new Topic(...$row);
    }
    /**
     * @return Topic[] All topics available in the database.
     */
    public function getAllTopics()
    {
        $rows = $this->db->run(
            'SELECT id, name, description, ts FROM Topic ORDER BY `ts` DESC'
        );
        return array_map(fn($row) => new Topic(...$row), $rows);
    }

    /**
     * Inserts the given topic into the database.
     */
    public function insert(Topic $topic)
    {
        $id = $this->db->insertReturnID("Topic", [
            "name" => $topic->name,
            "description" => $topic->description
        ]);
        $topic->id = intval($id);
        return $topic;
    }
}