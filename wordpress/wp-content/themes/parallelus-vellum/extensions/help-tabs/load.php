<?php
/*

    Extension Name: Help Tabs
    Extension URI:
    Version: 0.8
    Description: Help Tabs module
    Author:
    Author URI:
    Text Domain:
    Domain Path:
    Network:
    Site Wide Only:

*/

// Reset
if ( 0 ) update_option( 'help_tabs', array() );

// Settings
$fields = array();
$default = array();

$settings = array(
    'name' => 'Help Tabs',
    'option_key' => $shortname.'help_tabs',
    'fields' => $fields,
    'default' => $default,
    'parent_menu' => 'framework-options',
    //'menu_permissions' => 5,
    'file' => __FILE__,
);

// Required components
include 'object.php';
global $help_tabs_admin, $help_tabs_settings;
$help_tabs_settings = new Help_Tabs_Object( $settings );

// Load admin components
if ( is_admin() ) {
    include 'settings-object.php';
    $help_tabs_admin = new Help_Tabs_Admin_Object( $settings );
}

?>
