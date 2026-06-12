<?php
/**
 * Plugin Name: Torten Bern Core — Copilot
 * Plugin URI:  https://torten-bern.example
 * Description: Core plugin for Torten Bern: checkout fields, Twint QR flow, demo products and order meta handling. Coexists with other plugins. (Sandbox)
 * Version: 0.1.0
 * Author: Copilot
 * Text Domain: torten-bern-core
 */

if (!defined('ABSPATH')) exit;

define('TB_CP_VERSION','0.1.0');
define('TB_CP_DIR', plugin_dir_path(__FILE__));
define('TB_CP_URL', plugin_dir_url(__FILE__));

require_once TB_CP_DIR . 'autoload.php';

use TortenBern\Core\Plugin;

add_action('plugins_loaded', function(){
    Plugin::get_instance()->init();
});
