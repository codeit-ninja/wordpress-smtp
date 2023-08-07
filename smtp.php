<?php
namespace CodeIT\SMTP;
/*
Plugin Name:  WP SMTP
Plugin URI:   https://github.com/codeit-ninja/wordpress-smtp
Description:  Plain SMTP plugin without bloatware, advertising to a paid version or commercial SMTP plugin.
This is just a plain  plugin that allows you to use custom SMTP settings, it will tell WordPress core to use `PHPMailer` 
and the provided SMTP settings you defined in the dashboard.

Version:      1.0.2
Author:       Code IT
Author URI:   https://codeit.ninja/
Text Domain:  codeit
License:      MIT License
*/
require_once __DIR__ . '/vendor/autoload.php';

define('CODEIT_SMTP_VERSION', '1.0.2');
define('CODEIT_SMTP_PLUGIN_FILE', __FILE__);
define('CODEIT_SMTP_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('CODEIT_SMTP_PLUGIN_SLUG', 'codeit-smtp');
define('CODEIT_SMTP_PLUGIN_ROOT_URI', plugins_url('/', __FILE__));
define('CODEIT_SMTP_ROOT_DIR_PATH', plugin_dir_path(__FILE__));

add_action( 'admin_enqueue_scripts', function( $hook ) {
    if( 'settings_page_codeit-smtp' !== $hook ) {
        return;
    }

    wp_enqueue_script('codeit-smtp-script', plugin_dir_url(__FILE__) . '/js/codeit-smtp.js');
    
    add_filter('admin_footer_text', function () {
        echo 'Plugin made with <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="red" style="position: relative; top: 2px;margin: 0 .25rem;"><path d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z"/></svg> by <a target="_blank" href="https://codeit.ninja">codeit.ninja</a>';
    });
});

new Init;
new Options;