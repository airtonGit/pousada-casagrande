<?php

class Plugin_Installer_Admin_Object extends Runway_Admin_Object {

	// Add hooks & crooks
	function add_actions() {
		// Init action
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'register_plugins_in_dir' ) );
		add_action( 'tgmpa_register', array($this, 'required_theme_plugins' ) );		
		
	}

	function init() { 

		// global $sidebar_settings;
		if ( isset( $_REQUEST['navigation'] ) && !empty( $_REQUEST['navigation'] ) ) { 
			global $plugin_installer_admin;
			$plugin_installer_admin->navigation = $_REQUEST['navigation'];
		}
	}

	function after_settings_init() {
		/* nothing */
  	}

  	function load_objects(){
  		
  	}

  	function register_plugins_in_dir(){
  		#-----------------------------------------------------------------
		# Register a plugin
		#-----------------------------------------------------------------
		global $themePlugins, $plugin_installer;
		$plugins_list = $plugin_installer->get_all_plugins_list();
		// wp_die(out($plugins_list));

		foreach ($plugins_list as $plugin_key => $plugin_info) {			
			$plugin_name         = $plugin_info['Name'];
			$plugin_slug         = $plugin_key;
			$plugin_file         = $plugin_key.'.php';
			$plugin_install_file = $plugin_info['file'];
			$plugin_version      = $plugin_info['Version'];
			$plugin_install_version = $plugin_info['install_version'];

			if(strstr($plugin_info['source'], 'plugin-installer/plugins/')){
				$source = get_template_directory() .'/extensions/plugin-installer/plugins/'. $plugin_install_file;				
			}
			else{
				$source = get_template_directory() .'/extensions/plugin-installer/extensions/'. $plugin_install_file;				
			}

			// Specify plugin details
			$themePlugins[$plugin_slug] = array(
				'Name'     				=> $plugin_name, // The plugin name
				'name'     				=> $plugin_name, // The plugin name
				'slug'     				=> $plugin_slug, // The plugin slug (typically the folder name)
				'source'   				=> $source, // The plugin source
				'required' 				=> true, // If false, the plugin is only 'recommended' instead of required
				'version' 				=> $plugin_version, // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'file_path' 			=> $plugin_slug.'/'.$plugin_slug.'.php', // The plugin name
				'force_activation' 		=> true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
			);


			// Plugin update 
			// ---------------------------------------------------------------

			$args = array(
			    'new_version' => $plugin_version,
			    'plugin_slug' => $plugin_slug,                  // Your plugin slug (typically the plugin folder name, e.g. "soliloquy")
			    'plugin_path' => $plugin_slug .'/'. $plugin_file,  // The plugin basename (e.g. plugin_basename( __FILE__ )) // The plugin folder and main file.
			    'plugin_url'  => WP_PLUGIN_URL .'/'. $plugin_slug, // The HTTP URL to the plugin (e.g. WP_PLUGIN_URL . '/soliloquy')
			    'remote_url'  => get_home_url(),    // The remote API URL that should be pinged when retrieving plugin update info
			    'download_url' => $source, // str_replace('\\', '/', get_template_directory()) .'/extensions/plugin-installer/plugins/'. $plugin_install_file,
			    'plugin_name' => $plugin_name,
			    'version'     =>  $plugin_install_version
			);
			
			if (  version_compare( $plugin_version, $plugin_install_version, '>' ) ) 
				if (file_exists( ABSPATH . 'wp-content/plugins/'. $plugin_slug . '/' . $plugin_slug . '.php' )) {  
					${"config_$plugin_slug"}            = new TGM_Updater_Config( $args );
					${"namespace_updater_$plugin_slug"} = new TGM_Updater( ${"config_$plugin_slug"} ); // Be sure to replace "namespace" with your own custom namespace
					${"namespace_updater_$plugin_slug"}->update_plugins();            // Be sure to replace "namespace" with your own custom namespace
				}
		}

  	}

  	/**
	 * Register the required plugins for this theme.
	 *
	 * In this example, we register two plugins - one included with the TGMPA library
	 * and one from the .org repo.
	 *
	 * The variable passed to tgmpa_register_plugins() should be an array of plugin
	 * arrays.
	 *
	 * This function is hooked into tgmpa_init, which is fired within the
	 * TGM_Plugin_Activation class constructor.
	 */
	function required_theme_plugins() {
		global $themePlugins;

		/**
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		$plugins = array();


		if (isset($themePlugins) && count($themePlugins)) {

			foreach ($themePlugins as $slug => $plugin_data) {
				$plugins[] = $plugin_data;
			}
		}

		// Change this to your theme text domain, used for internationalising strings
		$theme_text_domain = 'framework';

		/**
		 * Array of configuration settings. Amend each line as needed.
		 * If you want the default strings to be available under your own theme domain,
		 * leave the strings uncommented.
		 * Some of the strings are added into a sprintf, so see the comments at the
		 * end of each line for what each argument will be.
		 */
		$config = array(
			'domain'       		=> $theme_text_domain,         	// Text domain - likely want to be the same as your theme.
			'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
			'parent_menu_slug' 	=> 'themes.php', 				// Default parent menu slug
			'parent_url_slug' 	=> 'themes.php', 				// Default parent URL slug
			'menu'         		=> 'install-required-plugins',	// Menu slug
			'has_notices'      	=> true,                       	// Show admin notices or not
			'is_automatic'    	=> true,					   	// Automatically activate plugins after installation or not
			'message' 			=> '',							// Message to output right before the plugins table
			'strings'      		=> array(
				'page_title'                       			=> __( 'Install Required Plugins', $theme_text_domain ),
				'menu_title'                       			=> __( 'Theme Plugins', $theme_text_domain ),
				'installing'                       			=> __( 'Installing Plugin: %s', $theme_text_domain ), // %1$s = plugin name
				'oops'                             			=> __( 'Something went wrong with the plugin API.', $theme_text_domain ),
				'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
				'notice_can_install_recommended'			=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
				'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
				'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
				'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
				'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
				'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
				'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
				'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
				'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
				'return'                           			=> __( 'Return to Required Plugins Installer', $theme_text_domain ),
				'plugin_activated'                 			=> __( 'Plugin activated successfully.', $theme_text_domain ),
				'complete' 									=> __( 'All plugins installed and activated successfully. %s', $theme_text_domain ), // %1$s = dashboard link
				'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
			)
		);

		if(function_exists('tgmpa')){			
			tgmpa( $plugins, $config );
		}
	}

	public function make_plugin_from_extension($extension = null){
		global $extm;		

		if($extension != null){
			$sourcePath = $extm->extensions_dir.$extension;
			$zipPath = dirname(__FILE__).'/extensions/'.$extension.'.zip';

			if(file_exists($sourcePath)){
				$pathInfo = pathInfo($sourcePath); 
				$parentPath = $pathInfo['dirname']; 
				$dirName = $pathInfo['basename']; 

				$ext_info = $extm->get_extension_data($extm->extensions_dir.$extension.'/load.php');
				$ext = $this->make_plugin_header($ext_info, $extension.'/load.php');
				$load_extension = "<?php
	if(file_exists(get_theme_root().'/runway-framework/framework/load.php')){
		require_once(get_theme_root().'/runway-framework/framework/load.php');

		// include extension
		require_once(dirname(__FILE__).'" . $extension . "/load.php');
	}
?>";


				$z = new ZipArchive(); 
				if($z->open($zipPath, ZIPARCHIVE::CREATE)){
					$z->addEmptyDir($extension);
					$z->addFromString($extension.'/'.$extension.'.php', $ext);
					$z->addFromString($extension.'/load-extension.php', $load_extension);
					self::ext_to_plugin($sourcePath, $z, strlen("$parentPath/"), $extension.'/'); 
					$z->close();
				}
			}
		}
		else return false;
	}

	private function make_plugin_header($ext_info, $load_php){
		$content = "<?php \r\n/*\r\n";
		foreach ($ext_info as $key => $value) {
			if($value != ''){
				
				// Plugin Name: Hello Dolly
				// Plugin URI: http://wordpress.org/plugins/hello-dolly/
				// Description: This is not just a plugin, it symbolizes the hope and enthusiasm of an entire generation summed up in two words sung most famously by Louis Armstrong: Hello, Dolly. When activated you will randomly see a lyric from <cite>Hello, Dolly</cite> in the upper right of your admin screen on every page.
				// Author: Matt Mullenweg
				// Version: 1.6
				// Author URI: http://ma.tt/

				switch ($key) {
					case 'Name':{
						$content .= 'Plugin Name: '.$value."\r\n";
					} break;

					case 'ExtensionURI':{
						$content .= 'Plugin URI: '.$value."\r\n";
					} break;

					case 'Description':{
						$content .= 'Description: '.$value."\r\n";
					} break;
					
					default:{
						// nothing to do
					}break;
				}
			}
		}
		$content .= "*/\r\n\r\n"."require_once(dirname(__FILE__).'/load-extension.php');\r\n?>";
		return $content;
	}

	/** 
	* Add files and sub-directories in a folder to zip file. 
	* @param string $folder 
	* @param ZipArchive $zipFile 
	* @param int $exclusiveLength Number of text to be exclusived from the file path. 
	*/ 
	private static function ext_to_plugin($folder, $zipFile, $exclusiveLength, $zipPath = '') { 
		$handle = opendir($folder); 
		while (false !== $f = readdir($handle)) { 
		  if ($f != '.' && $f != '..') { 
		    $filePath = "$folder/$f"; 
		    // Remove prefix from file path before add to zip. 
		    $localPath = substr($filePath, $exclusiveLength); 
		    if (is_file($filePath)) { 
		      $zipFile->addFile($filePath, $zipPath.$localPath); 
		    } elseif (is_dir($filePath)) { 
		      // Add sub-directory. 
		      $zipFile->addEmptyDir($zipPath.'/'.$localPath);
		      self::ext_to_plugin($filePath, $zipFile, $exclusiveLength, $zipPath); 
		    } 
		  } 
		} 
		closedir($handle); 
	} 	
}
?>