<?php
namespace Vada\Model;

class UserSessionManager
{
  private $cookieName;

  public function __construct($cookieName)
  {
    $this->cookieName = $cookieName;
    if (!isset($_COOKIE[$cookieName])) {
      $this->setAccessCodes([]);
    }
  }

  public function getGroups()
  {
    if (!isset($_COOKIE[$this->cookieName])) {
      return [];
    }
    $access_codes = json_decode($_COOKIE[$this->cookieName], true);
    return $access_codes;
  }

  public function setAccessCodes($access_codes)
  {
    // set locally (in case we read this again)
    $_COOKIE[$this->cookieName] = json_encode($access_codes);
    // store as a cookie.
    setcookie($this->cookieName, $_COOKIE[$this->cookieName], time() + 86400 * 30, '/');
  }

  public function addGroup(int $group_id, string $access_code)
  {
    $access_codes = $this->getGroups();
    if (!isset($access_codes[$group_id])) {
      $access_codes[$group_id] = $access_code;
      $this->setAccessCodes($access_codes);
    }
  }

  public function removeGroup($group_id)
  {
    $access_codes = $this->getGroups();
    unset($access_codes[$group_id]);
    $this->setAccessCodes($access_codes);
  }

  public function resetAccessCodes()
  {
    $this->setAccessCodes([]);
  }
}