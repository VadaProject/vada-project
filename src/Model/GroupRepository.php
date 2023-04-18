<?php
namespace Vada\Model;

use Exception;
use ParagonIE\EasyDB\EasyDB;

class GroupRepository
{
    protected EasyDB $db;

    public function __construct(EasyDB $db)
    {
        $this->db = $db;
    }
    /**
     * @return object|null The group with the given ID, if it exists.
     */
    public function getGroupByID(int $group_id)
    {
        $row = $this->db->row('SELECT * FROM `Group` WHERE id = ?', $group_id);
        return $row ? (object) $row : null;
    }
    /**
     * The group with the given access code, if it exists.
     */
    public function getGroupByAccessCode(string $access_code)
    {
        $row = $this->db->row('SELECT * FROM `Group` WHERE access_code = ?', $access_code);
        return $row ? (object) $row : null;
    }

    /**
     * @return int[] The list of `topic_id`s that a given group has access to.
     */
    public function getTopicsOfGroup(int $group_id)
    {
        return $this->db->column(
            'SELECT topic_id FROM GroupTopic WHERE group_id = ?',
            [$group_id]
        );
    }
    /**
     * Associates a topic with a group.
     * @throws Exception If 
     */
    public function addTopicToGroup(int $topic_id, int $group_id)
    {
        try {
            $this->db->insert("GroupTopic", compact("topic_id", "group_id"));
        } catch (\PDOException $e) {
            if ($e->getCode() == "23000") {
                throw new Exception("Group $group_id already accesses $topic_id.");
            }
        }
    }
    /**
     * Create a new group with the given data.
     */
    public function insertGroup(string $name, string $description = null)
    {
        $access_code = $this->generateUniqueGroupCode();
        $this->db->insert("Group", compact("name", "description", "access_code"));
        return $access_code;
    }
    /**
     * Generates a random group code.
     */
    protected static function generateGroupCode()
    {
        $letter_count = 6;
        $digit_count = 2;
        $code = '';
        $vowels = array("A", "E", "I", "O", "U");
        $consonants = array('B', 'C', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'R', 'S', 'T', 'V', 'W', 'X', 'Y', 'Z');
        $max = $letter_count / 2;
        for ($i = 1; $i <= $max; $i++) {
            $code .= $consonants[rand(0, 19)];
            $code .= $vowels[rand(0, 4)];
        }
        // Second half: numbers
        for ($i = 0; $i < $digit_count; $i++) {
            $c = rand(0, 9);
            $code .= $c;
        }
        return $code;
    }

    protected function generateUniqueGroupCode()
    {
        do {
            $access_code = self::generateGroupCode();
            // keep trying access codes until we get a valid one.
            $exists = (bool) $this->db->cell(
                'SELECT EXISTS(SELECT id FROM `Group` WHERE access_code = ?)',
                $access_code
            );
        } while ($exists);
        return $access_code;
    }
}