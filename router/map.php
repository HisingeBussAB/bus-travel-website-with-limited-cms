<?php
/**
 * Rekå Resor (www.rekoresor.se)
 * @author    Håkan Arnoldson
 */

namespace HisingeBussAB\RekoResor\website\router;

class Map
{

  /**
   * @var string $pattern
   */
  private $pattern;

  /**
   * @var string $target string or function
   */
  private $target;

  /**
   * @var string $method
   */
  private $method;

  /**
   * @param string $pattern RegExp URI match
   * @param string $target method called on match
   * @param string $method POST GET or ANY
   */
  public function mapRoute($pattern, $target, $method='ANY') {
    $this->pattern = $pattern;
    $this->target = $target;
    $this->method = $method;

  }

  /**
   * @param string $path URI part to match against regexp
   * @param string string $method POST GET or ANY
   * @return array arguments from uri if match, or return false
   */
  public function matchRoute($path, $method='ANY') {
    if (preg_match($this->pattern, $path, $args)) {
      if ($this->method != 'ANY' && $this->method != $method) {
        require __DIR__ . '/../includes/pages/error/403.php';
        http_response_code(403);
        exit;
      }
      unset($args[0]);
      if (!isset($args)) $args = [];
      return $args;
    } else {
      return false;
    }
  }

  /**
   * @return string to be invoked by call_user_func
   */
  public function getTarget() {
    return $this->target;
  }

}
