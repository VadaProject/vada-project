<?php

namespace Vada\Model;

class UserAuthenticator
{
    protected GroupRepository $groups;
    protected UserSessionManager $session;

    public function __construct(GroupRepository $groupRepository, UserSessionManager $userSessionManager)
    {
        $this->groups = $groupRepository;
        $this->session = $userSessionManager;
    }

    /**
     * @return object[] The set of group objects.
     */
    public function getAllActiveGroups()
    {
        $groups = [];
        foreach ($this->session->getGroups() as $code) {
            $group = $this->groups->getGroupByAccessCode($code);
            if (!is_null($group)) {
                $groups[] = $group;
            }
        }
        return $groups;
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
        $this->session->addGroup($group->id, $access_code);
        return $group->id;
    }

    public function logoutGroup(int $group_id) {
        $this->session->removeGroup($group_id);
    }
    public function logoutAllGroups() {
        $this->session->resetAccessCodes();
    }
}