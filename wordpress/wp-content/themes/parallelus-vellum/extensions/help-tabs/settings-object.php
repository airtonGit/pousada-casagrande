<?php

class Help_Tabs_Admin_Object extends Runway_Admin_Object {

	// Add hooks & crooks
	function add_actions() {

		//include JS for drag and drop layout manager (cutsom jquery UI)
		add_action( 'admin_print_scripts-' . $this->parent_menu . '_page_' . $this->slug, array( &$this, 'load_admin_js' ) );
		add_action( 'before-apply-tabs', array( &$this, 'apply_tabs' ) );

		if ( isset( $_REQUEST['tab_controll'] ) ) {
			$this->attach_tabs( $_REQUEST['tab_controll'] );
		}

	}

	function attach_tabs( $tab_settings = array() ) {

		$page_tabs = $this->get_page_tabs( $tab_settings['page_url'] );
		$page_tabs_keys_list = array();
		foreach ( $page_tabs as $tab ) {
			$page_tabs_keys_list[] = $tab['id'];
		}

		$detaching_list = array_diff( (array)$page_tabs_keys_list, (array)$tab_settings['attach_tabs'] );

		foreach ( $detaching_list as $detaching_id ) {
			$tab = $this->get_tab( $detaching_id );
			if ( ( $key = array_search( $tab_settings['page_url'], (array)$tab['attach_to'] ) ) !== false ) {
				unset( $tab['attach_to'][$key] );
				$this->update_or_add_tab( $tab );
			}
		}

		if ( isset( $tab_settings['attach_tabs'] ) ) {
			foreach ( $tab_settings['attach_tabs'] as $tab_id ) {
				$tab = $this->get_tab( $tab_id );
				if ( !in_array( $tab_settings['page_url'], $tab['attach_to'] ) ) {
					$tab['attach_to'][] = $tab_settings['page_url'];
					$this->update_or_add_tab( $tab );
				}
			}
		}

	}

	function apply_tabs() {

		$tabs = $this->get_all_tabs();

		if(isset($tabs) && !empty($tabs))
			foreach ( $tabs as $tab ) {
				WP_Screen_Tabs::add_tab( $tab['attach_to'], $tab['id'], $tab['title'], $tab['content'] );
		}

	}

	function after_settings_init() {

		/* nothing */

	}

	function validate_sumbission() {

		// If all is OK
		return true;

	}

	function load_objects() {

		global $help_tabs_settings;
		$this->data = $help_tabs_settings->load_objects();
		return $this->data;

	}

	function load_admin_js() {

		/* nothing */

	}

	function get_all_tabs() {

		return get_option( $this->option_key, array() );

	}

	function get_page_tabs( $page = '' ) {

		$all_tabs = $this->get_all_tabs();

		$result = array();

		foreach ( $all_tabs as $key => $tab ) {
			if ( in_array( $page, $tab['attach_to'] ) ) {
				$result[$key] = $tab;
			}
		}

		return $result;

	}

	function get_tab( $id = null ) {

		$tabs = $this->get_all_tabs();
		return $tabs[$id];

	}

	function delete_tab( $id = null ) {

		$tabs = $this->get_all_tabs();
		unset( $tabs[$id] );
		update_option( $this->option_key, $tabs );

		$file_name = $this->option_key.'.json';
		if(file_put_contents(THEME_DIR.'data/'.$file_name, json_encode($tabs))){
			return true;
		} else {
			return false;
		}
	}

	function update_or_add_tab( $tab = null ) {

		$tabs = $this->get_all_tabs();
		$tabs[$tab['id']] = $tab;
		update_option( $this->option_key, $tabs );

		$file_name = $this->option_key.'.json';
		if(file_put_contents(THEME_DIR.'data/'.$file_name, json_encode($tabs))){
			return true;
		} else {
			return false;
		}
	}

	function render_options_builder_page( $page_url = '' ) {

		if ( !$page_url ) {
			return 'Error: Unknow page URL';
		}

		$all_tabs = $this->get_all_tabs();
		$page_tabs = $this->get_page_tabs( $page_url );

		$html = '';
		ob_start();
		include 'views/options-builder-view.php';
		$html = ob_get_contents();
		ob_end_clean();

		return $html;

	}

}
?>
