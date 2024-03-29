<?php global $column_left, $column_right, $max_columns;
/**
 * The template for displaying video format posts
 */

// Title (for everything except image left layout)
if (!$column_right) { 	
	theme_post_title();
}

$headerClass = ($column_left) ? 'span'.$column_left : ''; 

?>
<header class="post-header <?php echo $headerClass ?>">
	<?php theme_video_player($post->ID); ?>
</header>

<?php 
// Set a column width if using columns and we have media (an image)
if ($column_right && $headerClass) : ?>
<div class="span<?php echo  $column_right ?>">
<?php 

// Post title
theme_post_title();

endif; 

// Post Content
theme_post_content();

if ($column_right && $headerClass) : ?>
</div><!-- .span# -->
<?php endif; ?>