<?php
namespace Vada\Model;
use PDO;

class TopicRepository
{
    private $conn;
    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }
    /**
     * @return Topic|null The topic with the given ID
     */
    public function getTopicByID(int $topic_id)
    {
        $stmt = $this->conn->prepare('SELECT * FROM Topic WHERE id = :ID LIMIT 1');
        $stmt->bindParam("ID", $topic_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return new Topic($row["id"], $row["name"], $row["description"]);
    }
    /**
     * @return Topic[] All topics available in the database.
     */
    public function getAllTopics()
    {
        $stmt = $this->conn->prepare('SELECT * FROM Topic');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => new Topic($row["id"], $row["name"], $row["description"]), $rows);
    }

    /**
     * Inserts the given topic into the database.
     */
    public function insert(Topic $topic) {
        $stmt = $this->conn->prepare("INSERT INTO Topic(name, description) VALUES (?, ?)");
        $stmt->bindValue(1, $topic->name);
        $stmt->bindValue(2, $topic->description);
        $stmt->execute();
        $topic->id = intval($this->conn->lastInsertId());
        return $topic;
    }
    public function createNewTopic(string $name, string $description)
    {

    }
}