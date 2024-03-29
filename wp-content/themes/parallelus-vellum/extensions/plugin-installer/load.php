<?php
/*
    Extension Name: Plugin Installer
    Extension URI:
    Version: 0.8.1
    Description: 
    Author: 
    Author URI: 
*/

#-----------------------------------------------------------------
# Plugin Install and Update Classes
#-----------------------------------------------------------------

require_once('install/class-tgm-plugin-activation.php'); 
require_once('update/init.php');

// Settings
$fields = array(
		'var' => array(),
		'array' => array()
);
$default = array();

$settings = array(
	'name' => 'Plugin Installer', 
	'option_key' => $shortname.'plugin_installer',
	'fields' => $fields,
	'default' => $default,
	'parent_menu' => 'settings',
	'wp_containers' => 'none',
	'file' => __FILE__,
);

// Required components
include('object.php');

global $plugin_installer, $plugin_installer_admin, $tgm;
$plugin_installer = new Plugin_Installer_Object($settings);

// Load admin components
if (is_admin()) {

	include('settings-object.php');
	$plugin_installer_admin = new Plugin_Installer_Admin_Object($settings);	

	add_action( 'admin_notices', 'site_admin_plugin_notice');
	add_action( 'admin_menu', 'remove_submenu');
}

$plugin_installer->include_custom_php();

// Setup a custom button in the title
function title_button_new_plugin( $title ) {
	if ( $_GET['page'] == 'plugin-installer' ) {
		$title .= ' <a href="admin.php?page=plugin-installer&navigation=add-plugin" class="add-new-h2">'. __( 'Add New', 'framework' ) .'</a> ';
	}
	return $title;
}

add_action('add_report', 'plugin_installer_report');

function remove_submenu() {
	global $plugin_installer, $theme_name;

	$plugins = $plugin_installer->get_all_plugins_list();
	$are_all_activate = true;
	foreach($plugins as $plugin) {
		if(! is_plugin_active( $plugin['slug'].'/'.$plugin['slug'].'.php') )
			$are_all_activate = false;
	}

	if($theme_name === 'runway-framework' && $are_all_activate ) {
		remove_submenu_page('options-general.php', 'plugin-installer');
	}
}

function site_admin_plugin_notice() {
	$tgm = new TGM_Plugin_Activation;
	$tgm->notices();
}

function plugin_installer_report($reports_object){
	$plugins_dir = FRAMEWORK_DIR.'extensions/plugin-installer/plugins/';
	$reports_object->assign_report(array(
		'source' => 'Layouts Manager',
		'report_key' => 'layouts_dir_exists',
		'path' => $plugins_dir,
		'success_message' => 'Layouts dir ('.$plugins_dir.') is exists.',
		'fail_message' => 'Layouts dir ('.$plugins_dir.') is not exists.',
	), 'DIR_EXISTS' );

	$reports_object->assign_report(array(
		'source' => 'Layouts Manager',
		'report_key' => 'layouts_dir_writable',
		'path' => $plugins_dir,
		'success_message' => 'Layouts dir ('.$plugins_dir.') is writable.',
		'fail_message' => 'Layouts dir ('.$plugins_dir.') is not writable.',
	), 'IS_WRITABLE' );	
	
}

add_filter( 'framework_admin_title', 'title_button_new_plugin' );
?>