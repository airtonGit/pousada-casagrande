<style>

	.column-description {
		width: 80%;
	}

	.plugin-title {
		width: 20%;
	}

	ul li ul {
		padding-left: 25px;
	}

	.plugin-description {
		max-height: 300px;
		overflow: hidden;
	}

</style>

<?php
global $help_tabs_admin, $help_tabs_settings;

$navigation = isset( $_REQUEST['navigation'] )? $_REQUEST['navigation'] : '';
switch ( $navigation ) {
	case 'new':
	case 'edit': {
		if ( isset( $_REQUEST['action'] ) && ( $_REQUEST['action'] == 'add' || $_REQUEST['action'] == 'edit' ) ) {
			$keys = array( 'id', 'title', 'content', 'attach_to' );

			foreach ( $keys as $key ) {
				$tab[$key] = ( isset( $_REQUEST['tab'][$key] ) && $_REQUEST['tab'][$key] )? $_REQUEST['tab'][$key] : '';
			}

			$help_tabs_admin->update_or_add_tab( $tab );
		}

		include_once 'views/tab-settings.php';
	} break;

	case 'delete':
		$help_tabs_admin->delete_tab( $_REQUEST['id'] );
	default: {
		global $menu, $submenu;

		$tmp_Menu = $menu;

		foreach ( $submenu as $submenu_Items ) {
			foreach ( $submenu_Items as $submenu_Item ) {
				$tmp_Menu[] = $submenu_Item;
			}
		}

		$translate_Link_To_Name = array();

		foreach ( $tmp_Menu as $tmp_Menu_Item ) {
			$translate_Link_To_Name[$tmp_Menu_Item[2]] = $tmp_Menu_Item[0];
		}

		include_once 'views/tabs-list.php';
	} break;
} ?>
