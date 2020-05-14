<?php
if ( isset( $_REQUEST['id'] ) ) {
	$tab = $help_tabs_admin->get_tab( $_REQUEST['id'] );
}

$tab['attach_to'] = ( isset( $tab['attach_to'] ) && is_array( $tab['attach_to'] ) ) ? $tab['attach_to'] : array();
$tab['title'] = isset($tab['title'])? stripslashes( $tab['title'] ) : '';
?>

<form method="post" action="admin.php?page=help-tabs&navigation=edit">

	<br><div>
		<a class="button" href="admin.php?page=help-tabs">Back to tabs list</a>
		<input type="submit" class="button" value="Save" />
	</div><br>

	<?php if ( empty( $tab['id'] ) ) : ?>
		<input type="hidden" name="action" value="add" />
		<input type="hidden" name="tab[id]" value="<?php echo time(); ?>" />
	<?php else : ?>
		<input type="hidden" name="action" value="edit" />
		<input type="hidden" name="tab[id]" value="<?php echo $tab['id'] ?>" />
	<?php endif ?>

	<div id="titlediv">
		<label class="screen-reader-text" id="title-prompt-text" for="tab[title]">Enter title here</label>
		<input type="text" name="tab[title]" size="30" value="<?php echo isset( $tab['title'] ) ? $tab['title'] : '' ?>" id="title" autocomplete="off">
	</div>

	<?php 
		$tab['content'] = isset ( $tab['content'] ) ? $tab['content'] : '';
		wp_editor( stripslashes( $tab['content'] ), 'help-tab-content', array( 'textarea_name' => 'tab[content]', 'textarea_rows' => 25 ) ); 
	?>

	<ul class="attachment-list">
		<li>
			<label><input class="select-totally-all" type="checkbox" name="" value="" />Select all</label>
		</li>
		<?php global $menu, $submenu; ?>
		<?php foreach ( $menu as $menu_item ) : ?>
			<?php if ( $menu_item[4] != 'wp-menu-separator' ) : ?>
				<li><label>
						<input class="select-all" type="checkbox" name="" value="" />
						<?php echo 'Select / Deselect all ' . $menu_item[0] ?>
				</label></li>				
				<li>
					<ul class="sub-items">
						<?php if ( isset( $submenu[$menu_item[2]] ) && count( $submenu[$menu_item[2]] ) ) : ?>
							<?php foreach ( $submenu[$menu_item[2]] as $submenu_item ) : ?>
								<li><label><input 
									<?php echo in_array( $submenu_item[2], $tab['attach_to'] ) ? 'checked="true"' : '' ?> 
									type="checkbox" 
									name="tab[attach_to][]" 
									value="<?php echo $submenu_item[2] ?>" />
									<?php echo $submenu_item[2] . ': ' . $submenu_item[0] ?>
								</label></li>
							<?php endforeach ?> 	
						<?php else : ?>
							<li><label>
								<input 
									<?php echo in_array( $menu_item[2], $tab['attach_to'] ) ? 'checked="true"' : '' ?> 
									type="checkbox" 
									name="tab[attach_to][]" 
									value="<?php echo $menu_item[2] ?>" />
									<?php echo $menu_item[0] ?>
							</label></li>
						<?php endif ?>			
					</ul>
				</li>
			<?php endif ?>	
		<?php endforeach ?>
	</ul>

	<input type="submit" class="button" value="Save" />

	<script type="text/javascript">
		(function ($) {

			function auto_total_select() {

				/* check total select*/
				if( $('.sub-items li').length == $('.sub-items li input:checked').length ) {
					$('.select-totally-all').attr("checked", true);
				} else {
					$('.select-totally-all').attr("checked", false);
				}

				/* check subtotal select */
				$('.select-all').each(function () {
					var sub_Inputs_Conteiner = $(this).parent().parent().next();
					if( sub_Inputs_Conteiner.find("input").length == sub_Inputs_Conteiner.find("input:checked").length ) {
						$(this).attr("checked", true);
					} else {
						$(this).attr("checked", false);
					}
				});

			}

			$(function () {

				auto_total_select();

				$(".sub-items input").on("click", auto_total_select);

				/* bulk actions*/
				$('.select-all').on("click", function () {
					if($(this).is(':checked')) {
						$(this).parent().parent().next().find('input').attr('checked', true);
					} else {
						$(this).parent().parent().next().find('input').attr('checked', false);
					}
				});

				$('.select-totally-all').on("click", function () {
					if($(this).is(':checked')) {
						$('.attachment-list input').attr('checked', true);
					} else {
						$('.attachment-list input').attr('checked', false);
					}
				});

			});

		})(jQuery);
	</script>

</form>