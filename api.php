<?php
/**
 * api.php
 *
 * base class for WP Plugins that use an API
 * @package dko-wpplugin
 */

if (!class_exists('DKOWPPlugin_API')):
abstract class DKOWPPlugin_API
{
  public $curlopts = array();
  protected $ch = null; // curl handler

  /**
   * __construct
   *
   * @return void
   */
  public function __construct() {
    if (!defined('SERVER_ENVIRONMENT') || in_array(SERVER_ENVIRONMENT, array('STAGE', 'PROD'))) {
      $this->curlopts = array(
        CURLOPT_SSL_VERIFYHOST => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSLVERSION => 3 // fixes everything :D
      );
    }
    elseif (in_array(SERVER_ENVIRONMENT, array('LOCAL', 'DEV'))) { // local or dev
      $this->curlopts = array(
        CURLOPT_SSL_VERIFYHOST  => false,
        CURLOPT_SSL_VERIFYPEER  => false,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_SSLVERSION      => 3,
        CURLOPT_VERBOSE         => 1
      );
    }

    add_action('dkowppplugin_api_handle_errors', array(&$this, 'handle_errors'), 10, 2);
  } // __construct

  /**
   * make_request
   *
   * @TODO handle expired access tokens
   * @param string $url
   * @return string response
   */
  public function make_request($url) {
    $this->ch = curl_init($url);
    curl_setopt_array($this->ch, $this->curlopts);

    $result = curl_exec($this->ch);
    $result = apply_filters('dkowpplugin_after_request', $result);
    do_action('dkowppplugin_api_handle_errors', $result);
    curl_close($this->ch);
    return $result;
  }

  /**
   * handle_errors
   *
   * @param mixed $result
   * @param object $ch last used CURL
   * @return void
   */
  public function handle_errors($result) {
    if (curl_errno($this->ch)) {
      // @TODO wp_die($msg, $title, $args=array())
      throw new Exception(curl_error($this->ch));
    }
  }

} // end class
endif;
