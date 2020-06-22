<?php
/**
 * @package ld-connector
 */

/*
Plugin Name: LogicalDoc WP Connector 
Plugin URI: https://google.com
description: ¯\_(ツ)_/¯
Version: 1.0
Author: (Vishal P)-Kennovation
Author URI: https://google.com
*/

defined( 'ABSPATH' ) or die( 'hmm ¯\_(ツ)_/¯!' );
if(file_exists(plugin_dir_path(__FILE__).'vendor/autoload.php'))
{
  require_once plugin_dir_path(__FILE__).'vendor/autoload.php';
}
use Inc\ldinit;
if (class_exists('Inc\\ldinit')) {
    add_action('admin_init', 'init_connector');
}
function init_connector()
{
  $ld_connector=ldinit::create_self();
}

// Activation Hook 
register_activation_hook( __FILE__, 'activator_wrapper' );
function activator_wrapper()
{
  Inc\activate::set_ldwp_options();
}

// Dectivation Hook 
register_activation_hook( __FILE__, 'deactivator_wrapper' );
function deactivator_wrapper()
{
  // TODO
}