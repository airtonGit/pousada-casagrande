<h2 class="nav-tab-wrapper tab-controlls" style="padding-top: 9px;">
	<a href="<?php echo $this->self_url(); ?>" class="nav-tab <?php if($this->navigation == '') {echo "nav-tab-active";} ?>"><?php _e('Plugins', 'framework') ?></a>
	<a href="<?php echo $this->self_url('extensions'); ?>" class="nav-tab <?php if($this->navigation == 'extensions') {echo "nav-tab-active";} ?>"><?php _e('Extensions', 'framework') ?></a>
</h2>
<?php
	global $plugin_installer, $themePlugins;
	$tgm = new TGM_Plugin_Activation;

    $link = home_url().'/wp-admin/admin.php?page=plugin-installer';
    $redirect = '<script type="text/javascript">window.location = "'.$link.'";</script>';	

    $action = isset( $_GET['action'] )? $_GET['action'] : '';				
	switch ($action) {				
		case 'install': {
			$tgm->do_plugin_install();
			echo $redirect;
		} break;

		case 'activate': {
			$tgm->do_plugin_install();
			echo $redirect;
		} break;

		case 'install-extension':{
			// out($_POST);
			$extensions = $_POST['ext_chk'];
			if($extensions[0] == 'on') unset($extensions[0]);			

			foreach ($extensions as $key => $ext) {
				$tmp = explode('/', $ext);
				$extension = $tmp[0];
				$this->make_plugin_from_extension($extension);
			}
		} break;

		default:{
			// nothing todo
		} break;
	}

	switch ( $this->navigation ) {
		case 'add-plugin':{
			if ( ! isset($_POST['plug-submit'] ) ) {
				include_once 'views/add-plugin.php';

			}
			else {
				if ( empty( $_POST ) || !wp_verify_nonce( $_POST['plugin-upload-field'], 'plugin-upload-action' ) ) { 
					print 'Sorry, your nonce did not verify.';
					exit;
				}
				else { 
					if ( isset( $_FILES['plugzip']['name'] ) && $_FILES['plugzip']['name'] != '' ) {
						$file_ext = array_pop( explode( '.', $_FILES['plugzip']['name'] ) );
						if ( $file_ext == 'zip' ) {
							$plugin_installer->load_new_plugin( $_FILES['plugzip'] );
							$tgm->install_plugins_page();
							echo $redirect;
						}
						else {
							$info_message = 'File must have <b>.zip</b> extension Please choose another file.';
							include_once 'views/add-plugin.php';
						}
					}
					else {
						$info_message = 'Select a file';
						include_once 'views/add-plugin.php';
					}
				}
			}
		} break;
		
		case 'extensions':{
			global $extm;
			$vals['extm'] = $extm;
			$vals['exts'] = $extm->get_extensions_list( $extm->extensions_dir );
			$this->view('extensions', false, $vals);
		} break;

		default : { 
			$tgm->install_plugins_page();
		} break;
	}

?>