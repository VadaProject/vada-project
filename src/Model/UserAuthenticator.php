<?php

namespace Vada\Model;

class UserAuthenticator
{
    protected GroupRepository $groups;
    protected CookieManager $cookies;

    public function __construct(GroupRepository $groupRepository, CookieManager $cookieManager)
    {
        $this->groups = $groupRepository;
        $this->cookies = $cookieManager;
    }

    /**
     * @return object[] The set of group objects.
     */
    public function getAllActiveGroups()
    {
        $groups = [];
        $data = $this->cookies->getData();
        foreach ($this->cookies->getData() as $code) {
            $group = $this->groups->getGroupByAccessCode($code);
            if (!is_null($group)) {
                $groups[] = $group;
            }
        }
        return $groups;
    }

    /**
     * Is
     */
    public function isInGroup(int $group_id) {
        $code = $this->cookies->getData()[$group_id] ?? null;
        $group = $this->groups->getGroupByID($group_id);
        return $group && $code == $group->access_code;
    }

    public function canAccessTopic(int $topic_id) {
        $valid_groups = $this->groups->getGroupsOfTopic($topic_id);
        $my_groups = array_map(fn($group) => $group->id, $this->getAllActiveGroups());
        $intersect = array_intersect($valid_groups, $my_groups);
        return count($intersect) > 0;
    }

    /**
     * Join a group and store its access code.
     * 
     * @param string $access_code The code to use.
     * @throws \Exception if login fails.
     * @return int|null The group's ID, if login was successful. Otherwise null.
     */
    public function tryJoinGroup(string $access_code)
    {
        $group = $this->groups->getGroupByAccessCode($access_code);
        if (is_null($group)) {
            throw new \Exception("Access code invalid.");
        }
        $this->cookies->set($group->id, $access_code);
        return $group->id;
    }

    public function leaveGroup(int $group_id) {
        $this->cookies->remove($group_id);
    }
    public function logoutAllGroups() {
        $this->cookies->reset();
    }
}