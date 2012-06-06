<?php
/**
 * api.php
 *
 * base class for WP Plugins that use an API
 * @package dko-wpplugin
 */

if (!function_exists('curl_init')) {
  throw new Exception('DKOWPPlugin_API requires the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('DKOWPPlugin_API requires the JSON PHP extension.');
}

if (!class_exists('DKOWPPlugin_API')):
abstract class DKOWPPlugin_API
{
  private $cookie_file = '';
  protected $curlopts = array();
  protected $ch = null; // curl handler

  /**
   * __construct
   *
   * @return void
   */
  public function __construct($cookie_file = '/tmp/cookie/DKOWPPlugin.txt') {
    $this->cookie_file = $cookie_file;

    if (!defined('SERVER_ENVIRONMENT') || in_array(SERVER_ENVIRONMENT, array('STAGE', 'PROD'))) {
      $this->curlopts = array(
        CURLOPT_COOKIEFILE      => $this->cookie_file,
        CURLOPT_COOKIEJAR       => $this->cookie_file,
        CURLOPT_SSL_VERIFYHOST  => true,
        CURLOPT_SSL_VERIFYPEER  => true,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_SSLVERSION      => 3 // fixes everything :D
      );
    }
    elseif (in_array(SERVER_ENVIRONMENT, array('LOCAL', 'DEV', 'QA'))) { // local or dev
      $this->curlopts = array(
        CURLOPT_COOKIEFILE      => $this->cookie_file,
        CURLOPT_COOKIEJAR       => $this->cookie_file,
        CURLOPT_SSL_VERIFYHOST  => false,
        CURLOPT_SSL_VERIFYPEER  => false,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_SSLVERSION      => 3
      );
    }

    add_action('dkowppplugin_api_handle_errors', array(&$this, 'handle_errors'), 10, 2);
  } // __construct

  /**
   * make_request
   *
   * @TODO handle expired access tokens
   * @param string $url
   * @param mixed $query string of GET params or array of POST
   * @param object $pch reference to curl persistent handler to use
   * @return string response
   */
  protected function make_request($url = '', $query = '', &$pch = null) {
    $ch = $pch ? $pch : curl_init();
    curl_setopt_array($ch, $this->curlopts);
    if (is_array($query) && count($query)) {
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
    }
    else {
      $url .= '?' . $query;
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    $result = curl_exec($ch);
    $result = apply_filters('dkowpplugin_api_after_request', $result, $ch, $url);
    do_action('dkowppplugin_api_handle_errors', $ch, $result);

    // close if not persistent
    if (!$pch) {
      curl_close($ch);
    }
    return $result;
  }

  /**
   * handle_errors
   *
   * @param mixed $result
   * @param object $ch last used CURL
   * @return void
   */
  public function handle_errors($ch, $result) {
    if (curl_errno($ch)) {
      // @TODO wp_die($msg, $title, $args=array())
      throw new Exception(curl_error($ch));
    }
  }

} // end class
endif;
