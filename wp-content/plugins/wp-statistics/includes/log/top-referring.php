<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('.show-map').click(function(){
			alert('<?php _e('To be added soon', 'wp_statistics'); ?>');
		});

	postboxes.add_postbox_toggles(pagenow);
	});
</script>
<?php
	$search_engines = wp_statistics_searchengine_list();
	
	$search_result['All'] = wp_statistics_searchengine('all','total');

	foreach( $search_engines as $key => $se ) {
		$search_result[$key] = wp_statistics_searchengine($key,'total');
	}

	if( array_key_exists('referr',$_GET) ) {
		$referr = esc_sql( $_GET['referr'] );
	}
	else {
		$referr = '';
	}
	
	if( $referr ) {
		$total = $wpdb->query("SELECT `referred` FROM `{$table_prefix}statistics_visitor` WHERE `referred` LIKE '%" . esc_sql($referr) . "%'");
	} else {
		$total = $wpdb->query("SELECT `referred` FROM `{$table_prefix}statistics_visitor` WHERE referred <> ''");
	}
?>
<div class="wrap">
	<?php screen_icon('options-general'); ?>
	<h2><?php _e('Top referring sites', 'wp_statistics'); ?></h2>
	<ul class="subsubsub">
		<li class="all"><a <?php if(!$referr) { echo 'class="current"'; } ?>href="?page=wps_referers_menu"><?php _e('All', 'wp_statistics'); ?> <span class="count">(<?php echo $total; ?>)</span></a></li>
		<?php if($referr) { ?>
			| <li><a class="current" href="?page=wps_referers_menu&referr=<?php echo $referr; ?>"> <?php echo $referr; ?> <span class="count">(<?php echo $total; ?>)</span></a></li>
		<?php } ?>
	</ul>
	<div class="postbox-container" id="last-log">
		<div class="metabox-holder">
			<div class="meta-box-sortables">
				<div class="postbox">
					<div class="handlediv" title="<?php _e('Click to toggle', 'wp_statistics'); ?>"><br /></div>
					<?php if($referr) { ?>
						<h3 class="hndle"><span><?php _e('Referring sites from', 'wp_statistics'); ?>: <?php echo $referr; ?></span></h3>
					<?php } else { ?>
						<h3 class="hndle"><span><?php _e('Top referring sites', 'wp_statistics'); ?></span></h3>
					<?php } ?>
					<div class="inside">
							<?php
								// Instantiate pagination object with appropriate arguments
								$pagesPerSection = 10;
								$options = array(25, "All");
								$stylePageOff = "pageOff";
								$stylePageOn = "pageOn";
								$styleErrors = "paginationErrors";
								$styleSelect = "paginationSelect";

								$Pagination = new Pagination($total, $pagesPerSection, $options, false, $stylePageOff, $stylePageOn, $styleErrors, $styleSelect);
								
								$start = $Pagination->getEntryStart();
								$end = $Pagination->getEntryEnd();
								
								echo "<div class='log-latest'>";
								
								if( $referr ) {
								
									$result = $wpdb->get_results("SELECT * FROM `{$table_prefix}statistics_visitor` WHERE `referred` LIKE '%{$referr}%' AND referred <> '' ORDER BY `{$table_prefix}statistics_visitor`.`ID` DESC  LIMIT {$start}, {$end}");
									
									foreach($result as $items) {
								
										echo "<div class='log-item'>";
											echo "<div class='log-referred'><a href='?page=wp-statistics/wp-statistics.php&type=last-all-visitor&ip={$items->ip}'>".wp_statistics_icons('dashicons-visibility', 'visibility')."{$items->ip}</a></div>";
											echo "<div class='log-ip'>{$items->last_counter} - <a href='http://www.geoiptool.com/en/?IP={$items->ip}' target='_blank'>{$items->ip}</a></div>";
											echo "<div class='clear'></div>";
											echo "<a class='show-map' title='".__('Map', 'wp_statistics')."'><div class='dashicons dashicons-location-alt'></div></a>";
											
											if( array_search( strtolower( $items->agent ), array( "chrome", "firefox", "msie", "opera", "safari" ) ) !== FALSE ){
												$agent = "<img src='".plugins_url('wp-statistics/assets/images/').$items->agent.".png' class='log-tools' title='{$items->agent}'/>";
											} else {
												$agent = "<div class='dashicons dashicons-editor-help'></div>";
											}
											
											echo "<div class='log-agent'><a href='?page=wp-statistics/wp-statistics.php&type=last-all-visitor&agent={$items->agent}'>{$agent}</a></div>";
											
											echo "<div class='log-url'><a href='{$items->referred}'><div class='dashicons dashicons-admin-links'></div> ".substr($items->referred, 0, 100)."[...]</a></div>";
										echo "</div>";
									
									}
								} else {
								
									$result = $wpdb->get_results("SELECT `referred` FROM `{$table_prefix}statistics_visitor` WHERE referred <> ''");
									
									$urls = array();
									foreach( $result as $items ) {
									
										$url = parse_url($items->referred);
										
										if( empty($url['host']) )
											continue;
											
										$urls[] = $url['host'];
									}
									$get_urls = array_count_values($urls);
									arsort( $get_urls );
									$get_urls = array_slice($get_urls, $start, $end);
									
									$i = 0;
									foreach( $get_urls as $items => $value) {
										
										$i++;
										
										echo "<div class='log-item'>";
											echo "<div class='log-referred'>{$i} - <a href='?page=wps_referers_menu&referr={$items}'>{$items}</a></div>";
											echo "<div class='log-ip'>".__('Reference', 'wp_statistics').": {$value}</div>";
											echo "<div class='clear'></div>";
											echo "<div class='log-url'><a href='http://{$items}/' title='{$items}'><div class='dashicons dashicons-admin-links'></div> http://{$items}/</a></div>";
										echo "</div>";
										
									}
								}
								
								echo "</div>";
							?>
					</div>
				</div>
				
				<div class="pagination-log">
					<?php echo $Pagination->display(); ?>
					<p id="result-log"><?php echo ' ' . __('Page', 'wp_statistics') . ' ' . $Pagination->getCurrentPage() . ' ' . __('From', 'wp_statistics') . ' ' . $Pagination->getTotalPages(); ?></p>
				</div>
			</div>
		</div>
	</div>
</div>