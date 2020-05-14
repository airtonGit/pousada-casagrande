<br><div>
	<a class="button" href="admin.php?page=help-tabs&navigation=new">New tab</a>
</div><br>

<table class="help-tabs wp-list-table widefat" cellspacing="0">
	<thead>
		<tr>
			<th scope="col" id="cb" class="manage-column column-cb check-column" style=""></th>
			<th scope="col" id="name" class="manage-column column-name" style="">Title</th>
			<th scope="col" id="description" class="manage-column column-description" style="">Content</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th scope="col" id="cb" class="manage-column column-cb check-column" style=""></th>
			<th scope="col" id="name" class="manage-column column-name" style="">Title</th>
			<th scope="col" id="description" class="manage-column column-description" style="">Content</th>
		</tr>
	</tfoot>

	<tbody id="the-list">

		<?php if ( $help_tabs_admin->get_all_tabs() ) : ?>
			<?php foreach ( $help_tabs_admin->get_all_tabs() as $tab ) : ?>
			<tr>
				<th scope="row" class="check-column"></th>
				<td class="plugin-title">
					<strong><?php echo stripslashes( $tab['title'] ); ?></strong>
					<div class="row-actions-visible">
						<span class="edit"><a href="admin.php?page=help-tabs&navigation=edit&id=<?php echo $tab['id'] ?>" title="" class="edit">Edit</a> | </span>
						<span class="delete"><a href="admin.php?page=help-tabs&navigation=delete&id=<?php echo $tab['id'] ?>" title="" class="delete">Delete</a></span>
					</div>
				</td>
				<td class="column-description desc">
					<div class="plugin-description">
						<p><?php echo stripslashes( $tab['content'] ) ?></p>
					</div>

					<div class="second plugin-version-author-uri">
						<strong>Attached to: </strong>
						<?php if ( ! empty( $tab['attach_to'] ) && count( $tab['attach_to'] ) ) : ?>
							<?php foreach ( $tab['attach_to'] as $attach_link ) : ?>
								<a href="<?php echo $attach_link ?>"><?php echo preg_replace( '/[0-9$]/', '', $translate_Link_To_Name[$attach_link] ); ?></a>
							<?php endforeach ?>
						<?php else: ?>
							None
						<?php endif ?>
					</div>
				</td>
			</tr>
			<?php endforeach; ?>

		<?php else : ?><tr><td colspan="3" style="width: 100%">Have no tabs</td></tr><?php endif ?>

	</tbody>
</table>
