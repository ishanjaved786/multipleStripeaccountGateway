<?php 
/*
  Plugin Name: Istripe Switch.
  Plugin URI: https://wordpress.com
  Description: Plugin to Switch stripe account conditionally
  Version: 1.0
  Author: Ishan
  Author URI: http://bit.ly/ishanprofile
  License: GPLv2 or later
  Text Domain: iss
*/

  if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
  }

  define('ISS_PLUGIN_DIR', plugin_dir_path(__FILE__));
  define('ISS_PLUGIN_URL', plugin_dir_url(__FILE__));
  define('ISS_DIR_ASSETS', plugin_dir_url(__FILE__).'/assets/');


// error_reporting(E_ALL);
// ini_set('display_errors', 1);

  if (!class_exists('\Stripe\Stripe')) {

    require_once(ISS_PLUGIN_DIR.'/sdk/init.php');
  
  }

  require_once( ISS_PLUGIN_DIR.'/Iclass-register.php' );
  require_once( ISS_PLUGIN_DIR.'/Iclass-front.php' );
  require_once( ISS_PLUGIN_DIR.'/Iclass-stripe.php' );
