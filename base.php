<?php
if (!class_exists('DKOWPPlugin')):
abstract class DKOWPPlugin
{
  private $plugin_file = __FILE__;
  private $wpdb;

  /**
   * constructor
   */
  protected function __construct($plugin_file) {
    global $wpdb;
    $this->wpdb = $wpdb;
    $this->plugin_file = $plugin_file;
  }

  /**
   * Outputs a template file from the plugin's views folder
   * @param string $view name of template file to load from views folder
   * @return void
   */
  protected function render($view, $data = array()) {
    $template = plugin_dir_path($this->plugin_file) . '/views/' . $view . '.php';
    include $template;
  }

} // class
endif; // class_exists()
