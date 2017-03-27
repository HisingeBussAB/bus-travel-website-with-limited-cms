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
   * @var string $target
   */
  private $target;

  /**
   * @var string $targetarg
   */
  private $targetarg;

  /**
   * @var string $method
   */
  private $method;

  /**
   * @param string $pattern RegExp URI match
   * @param string $target method called on match
   * @param string $method POST GET or ANY
   */
  public function mapRoute($pattern, $target, $targetarg, $method='ANY') {
    $this->pattern = $pattern;
    $this->target = $target;
    $this->targetarg = $targetarg;
    $this->method = $method;

  }

  /**
   * @param string $path URI part to match against regexp
   * @param string string $method POST GET or ANY
   * @return bool TRUE if match else FALSE
   */
  public function matchRoute($path, $method='ANY') {
    //If pattern match AND (allowed method is ANY OR method match allowed method)
    if (preg_match($this->pattern, $path) && ($this->method == 'ANY' || $this->method == $method))
      return TRUE;
    else
      return FALSE;
  }

  /**
   * @return string to be invoked by call_user_func
   */
  public function getTarget() {
    return $this->target;
  }

  /**
   * @return string to be invoked by call_user_func
   */
  public function getTargetarg() {
    return $this->targetarg;
  }

}
