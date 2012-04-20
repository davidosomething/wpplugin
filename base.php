<?php
if (!class_exists('DKOWPPlugin')):
abstract class DKOWPPlugin
{
  private $wpdb;
  private $plugin_file = __FILE__;

  /**
   * constructor
   */
  protected function __construct($extending_class_file) {
    global $wpdb;
    $this->wpdb = $wpdb;
    $this->plugin_file = $extending_class_file;
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
