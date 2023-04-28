<?php
namespace Vada\Model;

/**
 * A wrapper class for reading and writing an array to a browser cookie.
 */
// TODO: This is relatively insecure. A more robust version of this would rely on storing persistent user sessions on the server.
class CookieManager
{
  private $cookieName;
  /**
   * Initializes a cookie manager. Stores data in the given cookie name.
   */
  public function __construct($cookieName)
  {
    $this->cookieName = $cookieName;
    if (!isset($_COOKIE[$cookieName])) {
      $this->reset();
    }
  }
  /**
   * As a side effect: if the data cannot be parsed, it is reset to empty.
   * @return array The stored data
   */
  public function getData()
  {
    if (!isset($_COOKIE[$this->cookieName])) {
      return [];
    }
    $data = json_decode($_COOKIE[$this->cookieName], true);
    if ($data === null || !is_array($data)) {
      $this->reset();
    }
    return $data;
  }

  /**
   * @param array $data The data to store
   */
  private function setData(array $data)
  {
    // update locally (in case we read this again)
    $json = json_encode($data);
    $_COOKIE[$this->cookieName] = json_encode($data);
    // store as a cookie.
    setcookie($this->cookieName, $_COOKIE[$this->cookieName], [
      "expires" => time() + 86400 * 30,
      "path" => '/',
      "httponly" => true
    ]);
  }

  public function set($key, $value)
  {
    $access_codes = $this->getData();
    if (!isset($access_codes[$key])) {
      $access_codes[$key] = $value;
      $this->setData($access_codes);
    }
  }

  public function remove($key)
  {
    $access_codes = $this->getData();
    unset($access_codes[$key]);
    $this->setData($access_codes);
  }

  public function reset()
  {
    $this->setData([]);
  }
}